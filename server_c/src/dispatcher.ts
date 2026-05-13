import { logger } from './logger.js';
import { notifyAckTimeout } from './queue/retry.js';
import {
  getByJobId,
  markSent,
  type JobRow,
} from './store/sqlite.js';
import { getByShopAndCapability, type ClientConnection } from './ws/clients.js';
import type { PrintJobMessage } from './ws/protocol.js';
import { sendJson } from './ws/send.js';

const ACK_TIMEOUT_MS = 10_000;

/**
 * 이미 client 로 전송되어 ACK 대기 중인 job_id 집합.
 * dispatch 시 add, ACK 수신 / ACK 타임아웃 시 remove.
 * 동일 job_id 의 짧은 시간 내 재전송 방지 (PRD §8.4).
 */
const inFlight = new Set<number>();

export function isInFlight(jobId: number): boolean {
  return inFlight.has(jobId);
}

export function clearInFlight(jobId: number): void {
  inFlight.delete(jobId);
}

/**
 * 후보 client 우선순위: capability 매칭 + last_connected 최근.
 */
function pickClient(shopId: number, capability: PrintJobMessage['printer_type']): ClientConnection | null {
  const candidates = getByShopAndCapability(shopId, capability);
  if (candidates.length === 0) return null;
  candidates.sort((a, b) => b.connected_at.getTime() - a.connected_at.getTime());
  // .at(0) 으로 좁히면 noUncheckedIndexedAccess 와 호환
  return candidates[0] ?? null;
}

/**
 * Job 1건 디스패치 시도.
 *  - in-flight 면 skip
 *  - 매칭 client 없으면 queued 유지 (재접속/재시도 워커가 다시 시도)
 *  - 매칭 성공 시 send + markSent + ACK 타임아웃 타이머
 *
 * 반환: 디스패치한 client_id 또는 null (오프라인/in-flight)
 */
export function tryDispatch(job: JobRow): number | null {
  if (job.status !== 'queued') return null;
  if (inFlight.has(job.job_id)) return null;

  const target = pickClient(job.shop_id, job.printer_type);
  if (!target) return null;

  let payload: unknown;
  try {
    payload = JSON.parse(job.payload_json);
  } catch {
    payload = {};
  }

  const msg: PrintJobMessage = {
    type: 'print_job',
    job_id: job.job_id,
    shop_id: job.shop_id,
    printer_type: job.printer_type,
    payload,
  };

  sendJson(target.socket, msg);
  markSent(job.job_id, target.client_id);
  inFlight.add(job.job_id);

  // markSent 이후의 attempt_count 를 캡처 — 타임아웃 콜백에서 비교
  const updated = getByJobId(job.job_id);
  const dispatchedAttempt = updated?.attempt_count ?? job.attempt_count + 1;

  setTimeout(() => {
    if (!inFlight.has(job.job_id)) return; // 이미 ACK 수신됨
    inFlight.delete(job.job_id);
    notifyAckTimeout(job.job_id, dispatchedAttempt);
  }, ACK_TIMEOUT_MS);

  logger.info(
    {
      event: 'job_dispatched',
      job_id: job.job_id,
      shop_id: job.shop_id,
      printer_type: job.printer_type,
      client_id: target.client_id,
      attempt: dispatchedAttempt,
    },
    '[dispatcher] dispatch 성공',
  );

  return target.client_id;
}

/**
 * 매장 단위 일괄 시도 — webhook 직후 / 재접속 동기화에서 호출.
 * last_known_job_id 가 주어지면 그 이후 + capability 매칭만 대상.
 */
export function tryDispatchShop(
  jobs: JobRow[],
  filter?: {
    lastKnownJobId?: number;
    capabilities?: Set<PrintJobMessage['printer_type']>;
  },
): number {
  let dispatched = 0;
  for (const job of jobs) {
    if (filter?.lastKnownJobId !== undefined && job.job_id <= filter.lastKnownJobId) continue;
    if (filter?.capabilities && !filter.capabilities.has(job.printer_type)) continue;
    if (tryDispatch(job) !== null) dispatched++;
  }
  return dispatched;
}
