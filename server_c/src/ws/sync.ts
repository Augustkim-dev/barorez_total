import { tryDispatchShop } from '../dispatcher.js';
import { logger } from '../logger.js';
import { getQueuedForShop } from '../store/sqlite.js';
import type { ClientConnection } from './clients.js';

/**
 * 인증 직후 호출 — last_known_job_id 이후의 미전송 작업을 일괄 디스패치 시도.
 *
 * 본 함수는 dispatcher 의 단순 wrapper:
 *  - getQueuedForShop 으로 매장 단위 queued 목록 조회
 *  - 디스패처가 capability 매칭 + last_connected 우선 + in-flight 회피 일관 적용
 *
 * 본 client 의 capability 와 매칭되는 job 만 분배되도록 capability 필터를 함께 전달.
 * 같은 매장에 더 최근 접속한 client 가 별도로 있으면 dispatcher 가 그쪽을 우선할 수 있음 — 의도된 동작.
 */
export function syncOnReconnect(conn: ClientConnection, lastKnownJobId: number | undefined): number {
  const jobs = getQueuedForShop(conn.shop_id);
  const dispatched = tryDispatchShop(jobs, {
    lastKnownJobId,
    capabilities: new Set(conn.capabilities),
  });
  if (dispatched > 0) {
    logger.info(
      {
        event: 'sync_dispatched',
        shop_id: conn.shop_id,
        client_id: conn.client_id,
        last_known_job_id: lastKnownJobId ?? null,
        dispatched,
        candidates: jobs.length,
      },
      '[ws] 재접속 동기화 일괄 디스패치',
    );
  }
  return dispatched;
}
