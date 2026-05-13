import { createHmac } from 'node:crypto';
import { config } from '../config.js';
import { logger } from '../logger.js';
import type { PrinterCapability } from '../ws/clients.js';

export interface ClientAuthResult {
  valid: true;
  client_idx: number;
  shop_idx: number;
  client_name: string;
  capabilities: PrinterCapability[];
}

interface CacheEntry {
  result: ClientAuthResult | { valid: false };
  cached_at: number;
}

const CACHE_TTL_MS = 5 * 60 * 1000; // 5분
const cache = new Map<string, CacheEntry>();

function cacheKey(clientIdx: number, token: string): string {
  // 토큰 평문은 캐시 키에 저장하지 않고 hash 로 단축 — 메모리 덤프 시 토큰 노출 ↓
  // 평문 캐시는 어차피 5분 후 폐기되나, 보수적으로.
  const h = createHmac('sha256', config.PRINT_SHARED_SECRET).update(`${clientIdx}:${token}`).digest('hex');
  return h;
}

function isValidResult(r: ClientAuthResult | { valid: false }): r is ClientAuthResult {
  return r.valid === true;
}

function isCapability(s: unknown): s is PrinterCapability {
  return s === 'kitchen' || s === 'counter' || s === 'bar';
}

/**
 * PHP 의 api/print_clients_verify.php 를 HMAC 으로 호출하여 client 토큰을 검증한다.
 * 5분 메모리 캐시. 캐시 미스만 PHP 에 요청.
 */
export async function verifyClientToken(
  clientIdx: number,
  token: string,
): Promise<ClientAuthResult | { valid: false }> {
  const key = cacheKey(clientIdx, token);
  const cached = cache.get(key);
  if (cached && Date.now() - cached.cached_at < CACHE_TTL_MS) {
    return cached.result;
  }

  const body = JSON.stringify({ client_idx: clientIdx, token });
  const sig = createHmac('sha256', config.PRINT_SHARED_SECRET).update(body).digest('hex');
  const url = `${config.PHP_API_BASE.replace(/\/$/, '')}/api/print_clients_verify.php`;

  let response: Response;
  try {
    response = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-Signature': sig,
      },
      body,
      signal: AbortSignal.timeout(5000),
    });
  } catch (err) {
    logger.warn({ err: (err as Error).message, url, client_id: clientIdx }, '[clientAuth] PHP 호출 실패');
    return { valid: false };
  }

  if (!response.ok) {
    logger.warn({ status: response.status, client_id: clientIdx }, '[clientAuth] PHP 비정상 응답');
    return { valid: false };
  }

  let payload: unknown;
  try {
    payload = await response.json();
  } catch {
    logger.warn({ client_id: clientIdx }, '[clientAuth] PHP 응답 JSON 파싱 실패');
    return { valid: false };
  }

  if (typeof payload !== 'object' || payload === null) return { valid: false };
  const p = payload as Record<string, unknown>;
  if (p.ok !== true || p.valid !== true) {
    const negative: { valid: false } = { valid: false };
    cache.set(key, { result: negative, cached_at: Date.now() });
    return negative;
  }

  const capsRaw = Array.isArray(p.capabilities) ? p.capabilities : [];
  const capabilities = capsRaw.filter(isCapability);

  const result: ClientAuthResult = {
    valid: true,
    client_idx: Number(p.client_idx),
    shop_idx: Number(p.shop_idx),
    client_name: String(p.client_name ?? ''),
    capabilities,
  };
  cache.set(key, { result, cached_at: Date.now() });
  return result;
}

export function invalidateClientAuthCache(): void {
  cache.clear();
}

export { isValidResult };
