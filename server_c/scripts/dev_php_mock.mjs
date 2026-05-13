// 개발 환경에서 PHP api/* 엔드포인트를 흉내내는 미니 서버.
// Server C 의 PHP_API_BASE 를 http://127.0.0.1:18080 으로 가리키면
// D008 (인증) ~ D010 (콜백) 통합 검증이 PHP-FPM 없이도 가능.
//
// 실행: node scripts/dev_php_mock.mjs

import { createServer } from 'node:http';
import { createHmac, timingSafeEqual } from 'node:crypto';
import { config as loadEnv } from 'dotenv';

loadEnv();
const SECRET = process.env.PRINT_SHARED_SECRET ?? '';
if (!SECRET) {
  console.error('[mock] PRINT_SHARED_SECRET 비어있음. .env 확인.');
  process.exit(1);
}

// mock 등록된 client (Phase 1 schema 의 row 흉내)
const CLIENTS = new Map([
  [17, { client_idx: 17, shop_idx: 48, client_name: 'mock-kitchen', capabilities: ['kitchen'], token: 'tk_kitchen_17_xxxxxxxxxx' }],
  [18, { client_idx: 18, shop_idx: 48, client_name: 'mock-counter', capabilities: ['counter'], token: 'tk_counter_18_xxxxxxxxxx' }],
  [19, { client_idx: 19, shop_idx: 48, client_name: 'mock-multi',   capabilities: ['kitchen','counter','bar'], token: 'tk_multi_19_xxxxxxxxxx' }],
]);

const callbackLog = [];

function verifyHmacReq(raw, headerSig) {
  if (!headerSig) return false;
  const expected = createHmac('sha256', SECRET).update(raw).digest('hex');
  if (expected.length !== headerSig.length) return false;
  return timingSafeEqual(Buffer.from(expected, 'utf8'), Buffer.from(headerSig, 'utf8'));
}

createServer(async (req, res) => {
  const chunks = [];
  for await (const c of req) chunks.push(c);
  const raw = Buffer.concat(chunks).toString('utf-8');
  const sig = req.headers['x-signature'];

  const reply = (code, obj) => {
    res.statusCode = code;
    res.setHeader('Content-Type', 'application/json; charset=utf-8');
    res.end(JSON.stringify(obj));
  };

  // 1) client 토큰 검증 (D008 통합)
  if (req.method === 'POST' && req.url === '/api/print_clients_verify.php') {
    if (!verifyHmacReq(raw, sig)) return reply(401, { ok: false, error: 'invalid_signature' });
    const body = JSON.parse(raw || '{}');
    const c = CLIENTS.get(body.client_idx);
    if (!c) return reply(200, { ok: true, valid: false });
    if (c.token !== body.token) return reply(200, { ok: true, valid: false });
    return reply(200, {
      ok: true, valid: true,
      client_idx: c.client_idx, shop_idx: c.shop_idx,
      client_name: c.client_name, capabilities: c.capabilities,
    });
  }

  // 2) job 상태 콜백 (D009 stub / D010 본격) — ?id=<job_id> 받음
  if (req.method === 'POST' && req.url?.startsWith('/api/print_jobs_status.php')) {
    if (!verifyHmacReq(raw, sig)) return reply(401, { ok: false, error: 'invalid_signature' });
    const m = req.url.match(/[?&]id=(\d+)/);
    const job_id = m ? Number(m[1]) : null;
    const body = JSON.parse(raw || '{}');
    callbackLog.push({ ts: new Date().toISOString(), job_id, ...body });
    return reply(200, { ok: true, job_idx: job_id });
  }

  // 3) 검증 도우미: 누적된 콜백 조회
  if (req.method === 'GET' && req.url === '/__mock/callbacks') {
    return reply(200, { count: callbackLog.length, entries: callbackLog });
  }

  // 4) mock 클라이언트 목록 (검증 스크립트가 token 얻을 때)
  if (req.method === 'GET' && req.url === '/__mock/clients') {
    return reply(200, {
      clients: [...CLIENTS.values()].map(({ token, ...rest }) => ({ ...rest, token })),
    });
  }

  res.statusCode = 404;
  res.end();
}).listen(18080, '127.0.0.1', () => {
  console.log('[mock] PHP API mock listening on http://127.0.0.1:18080');
  console.log('[mock] mock clients:', [...CLIENTS.keys()].join(', '));
});
