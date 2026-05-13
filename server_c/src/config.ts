import 'dotenv/config';
import { z } from 'zod';

const Schema = z.object({
  PORT: z.coerce.number().int().positive().default(3000),
  PRINT_SHARED_SECRET: z.string().min(1, 'PRINT_SHARED_SECRET 가 비어있습니다. PHP cfg/config.inc.php 와 동일 값으로 채울 것.'),
  PHP_API_BASE: z.string().url().default('https://barorez.com'),
  SQLITE_PATH: z.string().min(1).default('./data/server_c.db'),
  LOG_LEVEL: z.enum(['trace', 'debug', 'info', 'warn', 'error', 'fatal']).default('info'),
  NODE_ENV: z.enum(['development', 'production', 'test']).default('development'),
});

const parsed = Schema.safeParse(process.env);
if (!parsed.success) {
  // .env 미설정 시 명확한 에러로 종료 (production 에서 빈 시크릿으로 부팅되지 않도록)
  console.error('[config] 환경변수 검증 실패:');
  for (const issue of parsed.error.issues) {
    console.error(`  - ${issue.path.join('.')}: ${issue.message}`);
  }
  process.exit(1);
}

export const config = parsed.data;
export type Config = typeof config;
