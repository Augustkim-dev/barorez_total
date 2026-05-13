import { logger } from '../logger.js';
import { getQueuedForShop, markSent, type JobRow } from '../store/sqlite.js';
import { sendJson } from './send.js';
import type { ClientConnection } from './clients.js';
import type { PrintJobMessage } from './protocol.js';

/**
 * 인증 직후 호출 — last_known_job_id 이후의 미전송 작업을 일괄 Push.
 *
 * 본 단계(D008) 의 디스패치 정책:
 *   같은 매장의 status='queued' 작업 중, capability 매칭되는 모든 것을 본 client 에 즉시 Push.
 *   같은 capability 의 다른 client 가 이미 있는 경우의 우선순위 처리는 D009 디스패처에서.
 *   따라서 본 함수는 사실상 "재접속한 client 가 자기 capability 의 queued 작업을 받아간다" 의미.
 */
export function syncOnReconnect(conn: ClientConnection, lastKnownJobId: number | undefined): number {
  const all: JobRow[] = getQueuedForShop(conn.shop_id);
  const capabilities = new Set(conn.capabilities);

  let pushed = 0;
  for (const job of all) {
    if (lastKnownJobId !== undefined && job.job_id <= lastKnownJobId) continue;
    if (!capabilities.has(job.printer_type)) continue;

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
    sendJson(conn.socket, msg);
    markSent(job.job_id, conn.client_id);
    pushed++;
  }

  if (pushed > 0) {
    logger.info(
      {
        event: 'sync_pushed',
        shop_id: conn.shop_id,
        client_id: conn.client_id,
        last_known_job_id: lastKnownJobId ?? null,
        pushed,
      },
      '[ws] 재접속 동기화 일괄 Push',
    );
  }
  return pushed;
}
