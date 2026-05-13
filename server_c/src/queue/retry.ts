import { logger } from '../logger.js';
import { notifyPhpJobFailed } from '../php/callback.js';
import { tryDispatch } from '../dispatcher.js';
import {
  getByJobId,
  getRetryDue,
  markFailed,
  scheduleRetry,
} from '../store/sqlite.js';

/**
 * 재시도 백오프 (ms). 1-based: index = attempt_count (직전 시도 회차).
 *  - attempt 1 후 timeout → BACKOFF_MS[1] = 1초 후 재시도
 *  - attempt 2 후 timeout → BACKOFF_MS[2] = 3초 후 재시도
 *  - attempt 3 후 timeout → 더 이상 재시도 없음 → failed
 */
const BACKOFF_MS: Record<number, number> = {
  1: 1_000,
  2: 3_000,
  3: 10_000, // attempt 3 후로는 더 재시도 안 함. 보존만.
};

const MAX_ATTEMPTS = 3;

const RETRY_WORKER_INTERVAL_MS = 1_000;

let workerHandle: NodeJS.Timeout | null = null;

/**
 * dispatcher.ts 의 ACK 타임아웃 콜백이 호출.
 *  - 이미 ACK 받았으면 그 사이 status 가 'printed' 또는 'failed' 로 바뀌었을 것 → 무동작
 *  - attempt 가 dispatch 시점 값과 다르면 다른 흐름에서 처리됨 → 무동작 (race condition 방어)
 *  - 그 외: attempt 가 3 이상이면 failed, 그 외엔 백오프 후 재시도 예약
 */
export function notifyAckTimeout(jobId: number, dispatchedAttempt: number): void {
  const job = getByJobId(jobId);
  if (!job) return;
  if (job.status !== 'sent') return; // ACK 받았거나 이미 failed
  if (job.attempt_count !== dispatchedAttempt) return; // race

  if (job.attempt_count >= MAX_ATTEMPTS) {
    const err = `${MAX_ATTEMPTS} attempts exhausted (ACK timeout)`;
    markFailed(jobId, err);
    logger.warn(
      { event: 'job_failed', job_id: jobId, attempts: job.attempt_count, reason: err },
      '[retry] 최대 시도 초과 — failed',
    );
    void notifyPhpJobFailed(jobId, err);
    return;
  }

  const backoff = BACKOFF_MS[job.attempt_count] ?? 10_000;
  const nextAt = new Date(Date.now() + backoff).toISOString();
  scheduleRetry(jobId, nextAt, 'ack_timeout');
  logger.info(
    {
      event: 'retry_scheduled',
      job_id: jobId,
      next_attempt: job.attempt_count + 1,
      next_retry_at: nextAt,
      backoff_ms: backoff,
    },
    '[retry] 재시도 예약',
  );
}

/**
 * 1초 주기로 next_retry_at 도래한 작업들을 재디스패치.
 * 매칭 client 없으면 queued 유지 — 다음 워커 tick 에서 재시도.
 */
export function startRetryWorker(): void {
  if (workerHandle) return;
  workerHandle = setInterval(() => {
    const due = getRetryDue();
    if (due.length === 0) return;
    for (const job of due) {
      // tryDispatch 가 markSent 로 status='sent' 변경 → 다음 tick 에서 다시 안 잡힘
      // dispatch 실패(오프라인) 시 next_retry_at 은 유지되어 또 다시 잡힘
      tryDispatch(job);
    }
  }, RETRY_WORKER_INTERVAL_MS);
  logger.info({ interval_ms: RETRY_WORKER_INTERVAL_MS }, '[retry] worker 시작');
}

export function stopRetryWorker(): void {
  if (workerHandle) {
    clearInterval(workerHandle);
    workerHandle = null;
  }
}
