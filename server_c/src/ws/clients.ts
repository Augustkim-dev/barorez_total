import type { WebSocket } from 'ws';
import { logger } from '../logger.js';

export type PrinterCapability = 'kitchen' | 'counter' | 'bar';

export interface ClientConnection {
  client_id: number;
  shop_id: number;
  client_name: string;
  capabilities: PrinterCapability[];
  app_version: string | null;
  socket: WebSocket;
  connected_at: Date;
  last_pong_at: Date;
}

/**
 * 매장(shop_id) → client_id → 연결 매핑.
 * 같은 client_id 가 다시 접속하면 이전 소켓을 close 하고 새 소켓으로 교체.
 */
const byShop = new Map<number, Map<number, ClientConnection>>();

export function register(conn: ClientConnection): ClientConnection | null {
  let shopMap = byShop.get(conn.shop_id);
  if (!shopMap) {
    shopMap = new Map();
    byShop.set(conn.shop_id, shopMap);
  }
  const prev = shopMap.get(conn.client_id) ?? null;
  shopMap.set(conn.client_id, conn);
  logger.info(
    {
      event: 'client_connected',
      shop_id: conn.shop_id,
      client_id: conn.client_id,
      client_name: conn.client_name,
      replaced: prev !== null,
    },
    '[ws] client 연결 등록',
  );
  return prev;
}

export function unregister(shopId: number, clientId: number, conn: ClientConnection): boolean {
  const shopMap = byShop.get(shopId);
  if (!shopMap) return false;
  const cur = shopMap.get(clientId);
  // 이미 다른 연결로 교체된 경우 unregister 무시 (race condition)
  if (cur !== conn) return false;
  shopMap.delete(clientId);
  if (shopMap.size === 0) byShop.delete(shopId);
  logger.info({ event: 'client_disconnected', shop_id: shopId, client_id: clientId }, '[ws] client 연결 해제');
  return true;
}

export function getByShopAndCapability(shopId: number, capability: PrinterCapability): ClientConnection[] {
  const shopMap = byShop.get(shopId);
  if (!shopMap) return [];
  const list: ClientConnection[] = [];
  for (const conn of shopMap.values()) {
    if (conn.capabilities.includes(capability)) list.push(conn);
  }
  return list;
}

export function getConnection(shopId: number, clientId: number): ClientConnection | null {
  return byShop.get(shopId)?.get(clientId) ?? null;
}

export function getAllConnections(): ClientConnection[] {
  const all: ClientConnection[] = [];
  for (const shopMap of byShop.values()) {
    for (const conn of shopMap.values()) all.push(conn);
  }
  return all;
}

export function getStats(): { total: number; by_shop: Record<string, number> } {
  const by_shop: Record<string, number> = {};
  let total = 0;
  for (const [shopId, shopMap] of byShop.entries()) {
    by_shop[String(shopId)] = shopMap.size;
    total += shopMap.size;
  }
  return { total, by_shop };
}
