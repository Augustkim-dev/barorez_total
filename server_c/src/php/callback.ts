import { createHmac } from 'node:crypto';
import { config } from '../config.js';
import { logger } from '../logger.js';
import { recordError } from '../metrics.js';

/**
 * PHP api/print_jobs_status.php 콜백.
 *  - POST {PHP_API_BASE}/api/print_jobs_status.php?id={job_id}
 *  - Header: X-Signature: HMAC-SHA256(raw_body, PRINT_SHARED_SECRET)
 *  - Body  : { status, printed_at?, client_idx, last_error? }
 *  - 5초 타임아웃
 *  - 실패 시 자체 재시도 1s / 5s / 30s (총 4회 시도)
 *
 * D009 의 retry.ts / ack.ts 가 이미 본 함수를 호출 (인터페이스만 stub).
 * D010 에서 본체를 채움.
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

const REQUEST_TIMEOUT_MS = 5_000;
const RETRY_BACKOFFS_MS = [1_000, 5_000, 30_000];

async function postOnce(
  jobId: number,
  body: string,
): Promise<{ ok: boolean; status?: number; err?: string }> {
  const sig = createHmac('sha256', config.PRINT_SHARED_SECRET).update(body).digest('hex');
  const url = `${config.PHP_API_BASE.replace(/\/$/, '')}/api/print_jobs_status.php?id=${jobId}`;
  try {
    const response = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-Signature': sig,
      },
      body,
      signal: AbortSignal.timeout(REQUEST_TIMEOUT_MS),
    });
    if (!response.ok) {
      return { ok: false, status: response.status };
    }
    return { ok: true, status: response.status };
  } catch (err) {
    return { ok: false, err: (err as Error).message };
  }
}

async function postWithRetry(jobId: number, body: string, event: 'php_callback_printed' | 'php_callback_failed_call'): Promise<void> {
  // 첫 시도
  let result = await postOnce(jobId, body);
  if (result.ok) {
    logger.info({ event, job_id: jobId, status: result.status, attempt: 1 }, '[php] callback 성공');
    return;
  }

  // 재시도
  for (let i = 0; i < RETRY_BACKOFFS_MS.length; i++) {
    const backoff = RETRY_BACKOFFS_MS[i] ?? 30_000;
    const attempt = i + 2;
    logger.warn(
      { event: 'php_callback_retry', job_id: jobId, attempt, backoff_ms: backoff, prev_status: result.status, prev_err: result.err },
      '[php] callback 실패 — 재시도 예약',
    );
    await new Promise((r) => setTimeout(r, backoff));
    result = await postOnce(jobId, body);
    if (result.ok) {
      logger.info({ event, job_id: jobId, status: result.status, attempt }, '[php] callback 성공 (재시도)');
      return;
    }
  }

  logger.error(
    {
      event: 'php_callback_failed',
      job_id: jobId,
      total_attempts: 1 + RETRY_BACKOFFS_MS.length,
      last_status: result.status,
      last_err: result.err,
    },
    '[php] callback 최종 실패 — DB 상태와 PHP 의 인지 상태가 불일치할 수 있음',
  );
  recordError('php_callback_failed');
}

export async function notifyPhpJobPrinted(input: JobPrintedInput): Promise<void> {
  const body = JSON.stringify({
    status: 'printed',
    printed_at: input.printedAtIso,
    client_idx: input.clientIdx,
  });
  await postWithRetry(input.jobId, body, 'php_callback_printed');
}

export async function notifyPhpJobFailed(jobId: number, lastError: string, clientIdx?: number): Promise<void> {
  const payload: Record<string, unknown> = {
    status: 'failed',
    last_error: lastError,
  };
  if (clientIdx !== undefined) payload.client_idx = clientIdx;
  const body = JSON.stringify(payload);
  await postWithRetry(jobId, body, 'php_callback_failed_call');
}
