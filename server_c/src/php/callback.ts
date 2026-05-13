import { logger } from '../logger.js';

/**
 * PHP 측 api/print_jobs_status.php 에 ACK 결과를 통보한다.
 *
 * D009 시점에는 stub (로깅만). 본격 구현은 D010:
 *   POST {PHP_API_BASE}/api/print_jobs_status.php?id={job_id}
 *   Header: X-Signature: <HMAC-SHA256 hex>
 *   Body: { status: 'printed'|'failed', printed_at?, client_idx?, last_error? }
 *   5초 타임아웃, 실패 시 자체 재시도 1s/5s/30s.
 *
 * stub 단계에서도 retry.ts / ack.ts 와의 인터페이스는 확정 — D010 에서 본 함수 본체만 채움.
 */

export interface JobPrintedInput {
  jobId: number;
  printedAtIso: string;
  clientIdx: number;
}

export interface JobFailedInput {
  jobId: number;
  lastError: string;
  clientIdx?: number;
}

export async function notifyPhpJobPrinted(input: JobPrintedInput): Promise<void> {
  logger.info(
    {
      event: 'php_callback_stub',
      job_id: input.jobId,
      status: 'printed',
      printed_at: input.printedAtIso,
      client_idx: input.clientIdx,
    },
    '[php] callback stub (printed) — D010 에서 실제 POST',
  );
}

export async function notifyPhpJobFailed(jobId: number, lastError: string, clientIdx?: number): Promise<void> {
  logger.info(
    {
      event: 'php_callback_stub',
      job_id: jobId,
      status: 'failed',
      last_error: lastError,
      client_idx: clientIdx ?? null,
    },
    '[php] callback stub (failed) — D010 에서 실제 POST',
  );
}
