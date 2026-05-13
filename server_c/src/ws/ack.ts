import { clearInFlight, isInFlight } from '../dispatcher.js';
import { logger } from '../logger.js';
import { notifyPhpJobFailed, notifyPhpJobPrinted } from '../php/callback.js';
import { getByJobId, markFailed, markPrinted } from '../store/sqlite.js';
import type { ClientConnection } from './clients.js';
import type { AckMessage } from './protocol.js';

/**
 * client → server ACK 메시지 처리.
 *
 *  1. dispatcher in-flight set 에서 제거 (재전송 보호)
 *  2. SQLite 갱신 (printed / failed)
 *  3. PHP 콜백 (현재 stub, D010 에서 실제 POST)
 *
 * 검증:
 *  - job 이 DB 에 없으면 무시 (재시작 등으로 SQLite 가 재초기화된 경우)
 *  - 이미 status='printed' 인 job 에 대한 중복 ACK 는 idempotent — 다시 markPrinted 하지 않음
 */
export async function handleAck(ack: AckMessage, conn: ClientConnection): Promise<void> {
  const wasInFlight = isInFlight(ack.job_id);
  clearInFlight(ack.job_id);

  const job = getByJobId(ack.job_id);
  if (!job) {
    logger.warn(
      { event: 'ack_unknown_job', job_id: ack.job_id, client_id: conn.client_id },
      '[ack] 알 수 없는 job_id',
    );
    return;
  }

  if (job.status === 'printed' || job.status === 'failed') {
    logger.info(
      { event: 'ack_redundant', job_id: ack.job_id, current_status: job.status },
      '[ack] 이미 종료된 job 에 대한 중복 ACK — 무시',
    );
    return;
  }

  if (ack.status === 'printed') {
    const printedAtIso = ack.printed_at ?? new Date().toISOString();
    markPrinted(ack.job_id, printedAtIso);
    logger.info(
      {
        event: 'ack_received',
        job_id: ack.job_id,
        status: 'printed',
        client_id: conn.client_id,
        was_in_flight: wasInFlight,
      },
      '[ack] printed',
    );
    void notifyPhpJobPrinted({
      jobId: ack.job_id,
      printedAtIso,
      clientIdx: conn.client_id,
    });
    return;
  }

  // ack.status === 'failed'
  const errParts: string[] = [];
  if (ack.error_code) errParts.push(`[${ack.error_code}]`);
  if (ack.error_message) errParts.push(ack.error_message);
  const errText = errParts.join(' ').trim() || 'client_reported_failure';

  markFailed(ack.job_id, errText);
  logger.warn(
    {
      event: 'ack_received',
      job_id: ack.job_id,
      status: 'failed',
      client_id: conn.client_id,
      error: errText,
    },
    '[ack] failed (client report)',
  );
  void notifyPhpJobFailed(ack.job_id, errText, conn.client_id);
}
