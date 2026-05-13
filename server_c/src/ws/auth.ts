import type { WebSocket } from 'ws';
import { logger } from '../logger.js';
import { verifyClientToken } from '../php/clientAuth.js';
import { register, type ClientConnection, type PrinterCapability } from './clients.js';
import { ClientMessageSchema, type AuthMessage } from './protocol.js';
import { sendJson } from './send.js';
import { syncOnReconnect } from './sync.js';

const AUTH_TIMEOUT_MS = 5000;

/**
 * 새 WebSocket 연결의 첫 메시지(=auth) 처리.
 * 인증 성공 시 ClientConnection 반환. 실패 시 socket 자동 close 후 null.
 */
export async function handleNewConnection(socket: WebSocket): Promise<ClientConnection | null> {
  const firstMessage = await waitForFirstMessage(socket, AUTH_TIMEOUT_MS);
  if (firstMessage === null) {
    logger.warn('[ws] 첫 메시지 5초 내 미수신 — 강제 종료');
    safeClose(socket, 4001, 'auth timeout');
    return null;
  }

  const parsed = ClientMessageSchema.safeParse(firstMessage);
  if (!parsed.success || parsed.data.type !== 'auth') {
    logger.warn({ issues: !parsed.success ? parsed.error.issues : 'wrong first type' }, '[ws] auth 메시지 검증 실패');
    sendJson(socket, { type: 'auth_fail', reason: 'invalid auth message' });
    safeClose(socket, 4002, 'invalid auth');
    return null;
  }

  const auth = parsed.data;
  const verifyResult = await verifyClientToken(auth.client_id, auth.token);
  if (!verifyResult.valid) {
    logger.warn({ client_id: auth.client_id }, '[ws] PHP 검증 실패');
    sendJson(socket, { type: 'auth_fail', reason: 'invalid token' });
    safeClose(socket, 4003, 'invalid token');
    return null;
  }

  // client 가 주장한 capabilities 와 DB 의 capabilities 의 교집합만 인정
  const clientCaps = new Set<PrinterCapability>(auth.capabilities);
  const dbCaps = new Set<PrinterCapability>(verifyResult.capabilities);
  const effectiveCaps: PrinterCapability[] = [];
  for (const cap of clientCaps) {
    if (dbCaps.has(cap)) effectiveCaps.push(cap);
  }
  if (effectiveCaps.length === 0) {
    logger.warn(
      { client_id: auth.client_id, requested: auth.capabilities, db: verifyResult.capabilities },
      '[ws] capability 교집합 비어있음',
    );
    sendJson(socket, { type: 'auth_fail', reason: 'no matching capabilities' });
    safeClose(socket, 4004, 'no matching capabilities');
    return null;
  }

  const conn: ClientConnection = {
    client_id: verifyResult.client_idx,
    shop_id: verifyResult.shop_idx,
    client_name: verifyResult.client_name,
    capabilities: effectiveCaps,
    app_version: auth.app_version ?? null,
    socket,
    connected_at: new Date(),
    last_pong_at: new Date(),
  };

  const prev = register(conn);
  if (prev) safeClose(prev.socket, 4005, 'replaced by new connection');

  sendJson(socket, { type: 'auth_ok', client_id: conn.client_id, shop_id: conn.shop_id });
  logger.info(
    { event: 'client_authenticated', client_id: conn.client_id, shop_id: conn.shop_id, caps: effectiveCaps },
    '[ws] 인증 성공',
  );

  syncOnReconnect(conn, auth.last_known_job_id);

  return conn;
}

function waitForFirstMessage(socket: WebSocket, timeoutMs: number): Promise<unknown> {
  return new Promise((resolve) => {
    const onMessage = (data: Buffer | string): void => {
      cleanup();
      const text = Buffer.isBuffer(data) ? data.toString('utf-8') : data;
      try {
        resolve(JSON.parse(text));
      } catch {
        resolve(null);
      }
    };
    const onClose = (): void => {
      cleanup();
      resolve(null);
    };
    const onError = (): void => {
      cleanup();
      resolve(null);
    };
    const timer = setTimeout(() => {
      cleanup();
      resolve(null);
    }, timeoutMs);

    const cleanup = (): void => {
      clearTimeout(timer);
      socket.off('message', onMessage);
      socket.off('close', onClose);
      socket.off('error', onError);
    };
    socket.once('message', onMessage);
    socket.once('close', onClose);
    socket.once('error', onError);

    // 의도하지 않은 타입의 message (auth 가 아닌 ack 등) 도 첫 메시지로 잡힘 — auth 검증에서 거부.
    // resolve 후 cleanup 으로 추가 메시지는 본 함수에서 무시.
    void (onMessage as unknown);
  });
}

function safeClose(socket: WebSocket, code: number, reason: string): void {
  try {
    socket.close(code, reason);
  } catch {
    // ignore
  }
}
