import { mkdirSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import pino, { multistream, type StreamEntry } from 'pino';
import { config } from './config.js';

const isDev = config.NODE_ENV === 'development';

/**
 * 로그 정책 (PRD §9.1, §12.1):
 *  - dev: pino-pretty 콘솔 한 곳
 *  - prod: stdout JSON (PM2 가 캡처) + 파일 일별 분할 (pino-roll, 30일 보관)
 *
 * 이벤트 종류 (event 필드 사용 일관성):
 *   webhook_received | client_connected | client_authenticated | client_disconnected
 *   job_dispatched | ack_received | ack_redundant | ack_unknown_job
 *   retry_scheduled | job_failed | sync_dispatched
 *   php_callback_printed | php_callback_failed_call | php_callback_retry | php_callback_failed
 *   ack_received_stub (D008 잔재 — D009 에서 제거됨, 아래 이름 충돌 없음)
 */

const logFilePath = resolve('./logs/server_c.log');
mkdirSync(dirname(logFilePath), { recursive: true });

let internalLogger: pino.Logger;

if (isDev) {
  internalLogger = pino({
    level: config.LOG_LEVEL,
    transport: {
      target: 'pino-pretty',
      options: {
        colorize: true,
        translateTime: 'SYS:HH:MM:ss.l',
        ignore: 'pid,hostname',
      },
    },
  });
} else {
  // prod: stdout 과 일별 분할 파일 동시 기록
  const fileStream = pino.transport({
    target: 'pino-roll',
    options: {
      file: logFilePath,
      frequency: 'daily',
      mkdir: true,
      limit: { count: 30 }, // 30일 보관
      dateFormat: 'yyyy-MM-dd',
      extension: '.log',
    },
  });
  const streams: StreamEntry[] = [
    { level: config.LOG_LEVEL, stream: process.stdout },
    { level: config.LOG_LEVEL, stream: fileStream },
  ];
  internalLogger = pino(
    {
      level: config.LOG_LEVEL,
      formatters: { level: (label) => ({ level: label }) },
      timestamp: pino.stdTimeFunctions.isoTime,
    },
    multistream(streams),
  );
}

export const logger = internalLogger;
