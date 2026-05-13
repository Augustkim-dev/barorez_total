import Database from 'better-sqlite3';
import { mkdirSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { config } from '../config.js';
import { logger } from '../logger.js';

export type JobStatus = 'queued' | 'sent' | 'printed' | 'failed';
export type PrinterType = 'kitchen' | 'counter' | 'bar';

export interface JobRow {
  job_id: number;
  shop_id: number;
  printer_type: PrinterType;
  payload_json: string;
  status: JobStatus;
  attempt_count: number;
  assigned_client_id: number | null;
  last_error: string | null;
  sent_at: string | null;
  printed_at: string | null;
  created_at: string;
  next_retry_at: string | null;
}

const dbPath = resolve(config.SQLITE_PATH);
mkdirSync(dirname(dbPath), { recursive: true });

const db = new Database(dbPath);
db.pragma('journal_mode = WAL');
db.pragma('synchronous = NORMAL');
db.pragma('foreign_keys = ON');

db.exec(`
  CREATE TABLE IF NOT EXISTS jobs (
    job_id INTEGER PRIMARY KEY,
    shop_id INTEGER NOT NULL,
    printer_type TEXT NOT NULL CHECK(printer_type IN ('kitchen', 'counter', 'bar')),
    payload_json TEXT NOT NULL,
    status TEXT NOT NULL DEFAULT 'queued' CHECK(status IN ('queued', 'sent', 'printed', 'failed')),
    attempt_count INTEGER NOT NULL DEFAULT 0,
    assigned_client_id INTEGER,
    last_error TEXT,
    sent_at TEXT,
    printed_at TEXT,
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    next_retry_at TEXT
  );
  CREATE INDEX IF NOT EXISTS idx_jobs_shop_status ON jobs(shop_id, status);
  CREATE INDEX IF NOT EXISTS idx_jobs_retry ON jobs(status, next_retry_at);
`);

logger.info({ dbPath }, '[sqlite] 초기화 완료 (WAL 모드)');

const stmtUpsert = db.prepare(`
  INSERT INTO jobs (job_id, shop_id, printer_type, payload_json, status)
  VALUES (@job_id, @shop_id, @printer_type, @payload_json, 'queued')
  ON CONFLICT(job_id) DO NOTHING
`);

const stmtMarkSent = db.prepare(`
  UPDATE jobs
  SET status = 'sent',
      assigned_client_id = ?,
      sent_at = CURRENT_TIMESTAMP,
      attempt_count = attempt_count + 1
  WHERE job_id = ?
`);

const stmtMarkPrinted = db.prepare(`
  UPDATE jobs
  SET status = 'printed',
      printed_at = ?
  WHERE job_id = ?
`);

const stmtMarkFailed = db.prepare(`
  UPDATE jobs
  SET status = 'failed',
      last_error = ?
  WHERE job_id = ?
`);

const stmtScheduleRetry = db.prepare(`
  UPDATE jobs
  SET status = 'queued',
      next_retry_at = ?,
      last_error = ?
  WHERE job_id = ?
`);

const stmtGetQueuedForShop = db.prepare(`
  SELECT * FROM jobs
  WHERE shop_id = ? AND status = 'queued'
  ORDER BY job_id ASC
`);

const stmtGetByJobId = db.prepare(`SELECT * FROM jobs WHERE job_id = ?`);

const stmtGetRetryDue = db.prepare(`
  SELECT * FROM jobs
  WHERE status = 'queued' AND next_retry_at IS NOT NULL
    AND datetime(next_retry_at) <= datetime('now')
  ORDER BY next_retry_at ASC
`);

export interface UpsertJobInput {
  job_id: number;
  shop_id: number;
  printer_type: PrinterType;
  payload_json: string;
}

export function upsertJob(input: UpsertJobInput): { inserted: boolean } {
  const result = stmtUpsert.run(input);
  return { inserted: result.changes > 0 };
}

export function markSent(jobId: number, clientId: number): void {
  stmtMarkSent.run(clientId, jobId);
}

export function markPrinted(jobId: number, printedAtIso: string): void {
  stmtMarkPrinted.run(printedAtIso, jobId);
}

export function markFailed(jobId: number, lastError: string): void {
  stmtMarkFailed.run(lastError, jobId);
}

export function scheduleRetry(jobId: number, nextRetryAtIso: string, lastError: string): void {
  stmtScheduleRetry.run(nextRetryAtIso, lastError, jobId);
}

export function getQueuedForShop(shopId: number): JobRow[] {
  return stmtGetQueuedForShop.all(shopId) as JobRow[];
}

export function getByJobId(jobId: number): JobRow | undefined {
  return stmtGetByJobId.get(jobId) as JobRow | undefined;
}

export function getRetryDue(): JobRow[] {
  return stmtGetRetryDue.all() as JobRow[];
}

export function getStats(): { queued: number; sent: number; printed: number; failed: number } {
  const row = db
    .prepare(
      `SELECT
         SUM(CASE WHEN status = 'queued'  THEN 1 ELSE 0 END) AS queued,
         SUM(CASE WHEN status = 'sent'    THEN 1 ELSE 0 END) AS sent,
         SUM(CASE WHEN status = 'printed' THEN 1 ELSE 0 END) AS printed,
         SUM(CASE WHEN status = 'failed'  THEN 1 ELSE 0 END) AS failed
       FROM jobs`,
    )
    .get() as { queued: number | null; sent: number | null; printed: number | null; failed: number | null };
  return {
    queued: row.queued ?? 0,
    sent: row.sent ?? 0,
    printed: row.printed ?? 0,
    failed: row.failed ?? 0,
  };
}

export { db };
