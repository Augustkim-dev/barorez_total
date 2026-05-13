import { createHmac, timingSafeEqual } from 'node:crypto';
import type { NextFunction, Request, Response } from 'express';
import { config } from '../config.js';
import { logger } from '../logger.js';

const HEADER_NAME = 'x-signature';

/**
 * Express 의 raw body 보존용 verify 함수.
 * webhook 라우트의 express.json({ verify }) 옵션으로 등록.
 */
export function captureRawBody(req: Request, _res: Response, buf: Buffer): void {
  (req as Request & { rawBody?: Buffer }).rawBody = Buffer.from(buf);
}

/**
 * PHP 측 cfg/hmac.inc.php 와 동일 알고리즘:
 *   hash_hmac('sha256', $raw_body, PRINT_SHARED_SECRET) → 64자 hex
 *   hash_equals 로 timing-safe 비교
 */
export function verifyHmac(req: Request, res: Response, next: NextFunction): void {
  const reqWithRaw = req as Request & { rawBody?: Buffer };
  const rawBody = reqWithRaw.rawBody;
  if (!rawBody) {
    logger.warn({ path: req.path }, '[hmac] rawBody 미설정 — captureRawBody verify 누락');
    res.status(500).json({ error: 'raw body unavailable' });
    return;
  }

  const headerSig = req.headers[HEADER_NAME];
  if (typeof headerSig !== 'string' || headerSig.length === 0) {
    logger.warn({ path: req.path }, '[hmac] X-Signature 헤더 누락');
    res.status(401).json({ error: 'missing signature' });
    return;
  }

  const expected = createHmac('sha256', config.PRINT_SHARED_SECRET).update(rawBody).digest('hex');

  // timingSafeEqual 은 버퍼 길이가 같아야만 동작 — 길이 다르면 즉시 거부
  if (expected.length !== headerSig.length) {
    logger.warn({ path: req.path }, '[hmac] signature 길이 불일치');
    res.status(401).json({ error: 'invalid signature' });
    return;
  }

  const ok = timingSafeEqual(Buffer.from(expected, 'utf8'), Buffer.from(headerSig, 'utf8'));
  if (!ok) {
    logger.warn({ path: req.path }, '[hmac] signature 검증 실패');
    res.status(401).json({ error: 'invalid signature' });
    return;
  }

  next();
}
