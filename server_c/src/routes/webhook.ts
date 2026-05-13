import { Router } from 'express';
import { z } from 'zod';
import { tryDispatch } from '../dispatcher.js';
import { logger } from '../logger.js';
import { getByJobId, upsertJob } from '../store/sqlite.js';

/**
 * PHP 측 cfg/print.inc.php 의 send_print_webhook() 가 보내는 형식:
 *   POST /webhook/print
 *   Header: X-Signature: <HMAC-SHA256 hex of raw body>
 *   Body  : { "shop_id": 42, "jobs": [{ "job_id": 12345, "printer_type": "kitchen", "payload": {...} }, ...] }
 *
 * D007: SQLite 에 적재만.
 * D009: 적재 후 즉시 dispatcher 시도 — 매장 client 가 접속 중이면 곧바로 Push.
 *       오프라인이면 status='queued' 유지 → 재접속 동기화에서 처리.
 */

const PrinterTypeSchema = z.enum(['kitchen', 'counter', 'bar']);

const JobItemSchema = z.object({
  job_id: z.number().int().positive(),
  printer_type: PrinterTypeSchema,
  payload: z.unknown(),
});

const WebhookBodySchema = z.object({
  shop_id: z.number().int().positive(),
  jobs: z.array(JobItemSchema).min(1),
});

export const webhookRouter = Router();

webhookRouter.post('/print', (req, res) => {
  const parsed = WebhookBodySchema.safeParse(req.body);
  if (!parsed.success) {
    logger.warn({ issues: parsed.error.issues }, '[webhook] 본문 검증 실패');
    res.status(400).json({ error: 'invalid body', issues: parsed.error.issues });
    return;
  }

  const { shop_id, jobs } = parsed.data;
  let inserted = 0;
  let duplicate = 0;
  let dispatched = 0;

  for (const job of jobs) {
    const result = upsertJob({
      job_id: job.job_id,
      shop_id,
      printer_type: job.printer_type,
      payload_json: JSON.stringify(job.payload),
    });
    if (result.inserted) inserted++;
    else duplicate++;

    if (result.inserted) {
      const row = getByJobId(job.job_id);
      if (row) {
        const clientId = tryDispatch(row);
        if (clientId !== null) dispatched++;
      }
    }
  }

  logger.info(
    { event: 'webhook_received', shop_id, total: jobs.length, inserted, duplicate, dispatched },
    '[webhook] jobs 적재 + 즉시 디스패치 시도',
  );

  res.status(200).json({ ok: true, inserted, duplicate, dispatched });
});
