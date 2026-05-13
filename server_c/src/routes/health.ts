import { Router } from 'express';
import { getErrorCountLastHour } from '../metrics.js';
import { getStats as getQueueStats } from '../store/sqlite.js';
import { getStats as getConnectionStats } from '../ws/clients.js';

/**
 * GET /health
 *  PRD §9.1, §12.1 — 운영 모니터링 단순 가시성.
 *  외부 노출 시 IP whitelist 는 D011 Apache 설정에서.
 */

export const healthRouter = Router();

healthRouter.get('/', (_req, res) => {
  res.json({
    ok: true,
    uptime_sec: Math.floor(process.uptime()),
    queue: getQueueStats(),
    connections: getConnectionStats(),
    error_count_last_hour: getErrorCountLastHour(),
  });
});
