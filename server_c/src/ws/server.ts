import type { IncomingMessage, Server as HttpServer } from 'node:http';
import type { Socket } from 'node:net';
import { WebSocketServer, type WebSocket } from 'ws';
import { logger } from '../logger.js';
import { handleAck } from './ack.js';
import { handleNewConnection } from './auth.js';
import { unregister, type ClientConnection } from './clients.js';
import { ClientMessageSchema } from './protocol.js';

const PING_INTERVAL_MS = 30_000;
const PONG_TIMEOUT_PINGS = 2;

export function attachWebSocketServer(httpServer: HttpServer): WebSocketServer {
  const wss = new WebSocketServer({ noServer: true });

  httpServer.on('upgrade', (req: IncomingMessage, socket: Socket, head: Buffer) => {
    if (req.url !== '/ws') {
      socket.write('HTTP/1.1 404 Not Found\r\n\r\n');
      socket.destroy();
      return;
    }
    wss.handleUpgrade(req, socket, head, (ws) => {
      wss.emit('connection', ws, req);
    });
  });

  wss.on('connection', (socket: WebSocket, req: IncomingMessage) => {
    logger.debug({ remote: req.socket.remoteAddress }, '[ws] 새 연결, 인증 대기');
    void handleAndRun(socket);
  });

  return wss;
}

async function handleAndRun(socket: WebSocket): Promise<void> {
  const conn = await handleNewConnection(socket);
  if (!conn) return;

  let missedPongs = 0;
  const pingTimer = setInterval(() => {
    if (socket.readyState !== socket.OPEN) return;
    if (missedPongs >= PONG_TIMEOUT_PINGS) {
      logger.warn(
        { client_id: conn.client_id, shop_id: conn.shop_id, missed: missedPongs },
        '[ws] pong 응답 누락 — 연결 종료',
      );
      try {
        socket.close(4006, 'pong timeout');
      } catch {
        /* ignore */
      }
      return;
    }
    try {
      socket.send(JSON.stringify({ type: 'ping' }));
      missedPongs++;
    } catch (err) {
      logger.warn({ err: (err as Error).message }, '[ws] ping 전송 실패');
    }
  }, PING_INTERVAL_MS);

  socket.on('message', (data: Buffer | string) => {
    const text = Buffer.isBuffer(data) ? data.toString('utf-8') : data;
    let parsed: unknown;
    try {
      parsed = JSON.parse(text);
    } catch {
      logger.warn({ client_id: conn.client_id }, '[ws] 메시지 JSON 파싱 실패');
      return;
    }
    const validated = ClientMessageSchema.safeParse(parsed);
    if (!validated.success) {
      logger.warn({ client_id: conn.client_id, issues: validated.error.issues }, '[ws] 메시지 검증 실패');
      return;
    }

    switch (validated.data.type) {
      case 'pong':
        conn.last_pong_at = new Date();
        missedPongs = 0;
        break;
      case 'ack':
        void handleAck(validated.data, conn);
        break;
      case 'auth':
        // 이미 인증 완료된 연결에서 재차 auth 메시지는 무시
        logger.warn({ client_id: conn.client_id }, '[ws] 이미 인증된 연결의 중복 auth 메시지 무시');
        break;
    }
  });

  socket.on('close', () => {
    clearInterval(pingTimer);
    unregister(conn.shop_id, conn.client_id, conn as ClientConnection);
  });

  socket.on('error', (err) => {
    logger.warn({ err: err.message, client_id: conn.client_id }, '[ws] socket 오류');
  });
}
