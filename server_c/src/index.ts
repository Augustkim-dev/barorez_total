import { createServer } from 'node:http';
import express from 'express';
import { config } from './config.js';
import { logger } from './logger.js';
import { captureRawBody, verifyHmac } from './middleware/hmac.js';
import { startRetryWorker, stopRetryWorker } from './queue/retry.js';
import { healthRouter } from './routes/health.js';
import { webhookRouter } from './routes/webhook.js';
import { attachWebSocketServer } from './ws/server.js';

const app = express();

app.get('/', (_req, res) => {
  res.type('text/plain').send('Server C alive');
});

// webhook 전용 — raw body 보존 + HMAC 검증
app.use(
  '/webhook',
  express.json({ limit: '1mb', verify: captureRawBody }),
  verifyHmac,
  webhookRouter,
);

app.use('/health', healthRouter);

const httpServer = createServer(app);
const wss = attachWebSocketServer(httpServer);

httpServer.listen(config.PORT, '127.0.0.1', () => {
  logger.info({ port: config.PORT, env: config.NODE_ENV }, '[server] Server C 기동 (HTTP + WS)');
  startRetryWorker();
});

const shutdown = (signal: string): void => {
  logger.info({ signal }, '[server] 종료 신호 수신');
  stopRetryWorker();
  for (const client of wss.clients) {
    try {
      client.close(1001, 'server shutdown');
    } catch {
      /* ignore */
    }
  }
  wss.close();
  httpServer.close(() => {
    logger.info('[server] HTTP 서버 종료');
    process.exit(0);
  });
};

process.on('SIGTERM', () => shutdown('SIGTERM'));
process.on('SIGINT', () => shutdown('SIGINT'));
