import { Router } from 'express';
import { z } from 'zod';
import { logger } from '../logger.js';
import { upsertJob } from '../store/sqlite.js';

/**
 * PHP 측 cfg/print.inc.php 의 send_print_webhook() 가 보내는 형식:
 *   POST /webhook/print
 *   Header: X-Signature: <HMAC-SHA256 hex of raw body>
 *   Body  : { "shop_id": 42, "jobs": [{ "job_id": 12345, "printer_type": "kitchen", "payload": {...} }, ...] }
 *
 * D007: 본 단계에서는 jobs 를 SQLite 에 적재만. 실제 Push (디스패치) 는 D008/D009.
 *
 * 참고: PHP 측이 D007 변경에서 jobs 배열을 동봉하도록 보강됨 (Phase 1 D006 시점 형식 {job_ids, shop_id} 에서 변경).
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

  for (const job of jobs) {
    const result = upsertJob({
      job_id: job.job_id,
      shop_id,
      printer_type: job.printer_type,
      payload_json: JSON.stringify(job.payload),
    });
    if (result.inserted) inserted++;
    else duplicate++;
  }

  logger.info(
    { event: 'webhook_received', shop_id, total: jobs.length, inserted, duplicate },
    '[webhook] jobs 적재',
  );

  res.status(200).json({ ok: true, inserted, duplicate });
});
