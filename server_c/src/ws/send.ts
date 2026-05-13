import type { WebSocket } from 'ws';
import { logger } from '../logger.js';
import type { ServerMessage } from './protocol.js';

export function sendJson(socket: WebSocket, msg: ServerMessage): void {
  try {
    socket.send(JSON.stringify(msg));
  } catch (err) {
    logger.warn({ err: (err as Error).message, type: msg.type }, '[ws] send 실패');
  }
}
