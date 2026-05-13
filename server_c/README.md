# Server C — 영수증 출력 시스템 WebSocket 허브

맛집바로(barorez) 영수증 자동 출력 시스템의 중계 서버.
PHP 의 webhook 을 받아 매장 PC client 에 WebSocket 으로 Push 하고,
ACK 를 받아 PHP 에 콜백한다. 자세한 설계는 [PRD](../docs/barorez_print_system_PRD.md), [Phase 2 기획서](../docs/plans/002.영수증출력_Phase2_ServerC_WebSocket허브.md).

---

## 요구사항

- Node.js **20 LTS 이상**
- Apache 2.4 + `mod_proxy_wstunnel` (운영 시 외부 노출용)
- PM2 (운영 데몬화)

---

## 개발 환경

```bash
cd server_c
npm install
cp .env.example .env        # 값 채우기 (PRINT_SHARED_SECRET 등)
npm run dev                 # tsx watch, 127.0.0.1:3000
```

### mock PHP 서버 (선택)

운영 PHP-FPM 없이 통합 검증할 때:

```bash
# 별도 터미널
node scripts/dev_php_mock.mjs       # :18080 에서 PHP API 흉내

# .env 에서 PHP_API_BASE=http://127.0.0.1:18080 으로 설정 후 npm run dev
```

mock 등록 client: `17` (kitchen), `18` (counter), `19` (kitchen,counter,bar). 토큰은 `scripts/dev_php_mock.mjs` 안에 하드코딩.

---

## 운영 배포

### 1) 최초 1회 설정 (운영 서버)

```bash
# Node.js 20 설치 (없을 시)
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt-get install -y nodejs

# Apache 모듈 활성화
sudo a2enmod proxy proxy_http proxy_wstunnel
sudo systemctl reload apache2

# PM2 글로벌 설치
sudo npm install -g pm2
```

### 2) 배포

```bash
# git pull (또는 rsync)
cd /var/www/barorez_total/server_c

npm install --omit=dev      # 운영에서는 devDeps 미설치
npm run build               # tsc → dist/

# .env 설정 (운영 시크릿)
vi .env                     # PRINT_SHARED_SECRET, PHP_API_BASE 등

# PM2 기동
pm2 start ecosystem.config.cjs
pm2 save                    # 현재 프로세스 목록 저장
sudo pm2 startup            # 출력된 명령 그대로 실행 → 부팅 시 자동 기동
```

### 3) Apache 리버스 프록시

`server_c/deploy/apache_printsvc.conf` 를 barorez.com vhost 의 `<VirtualHost *:443>` 안에 Include:

```apache
<VirtualHost *:443>
    ServerName barorez.com
    # ... 기존 설정 ...

    Include /var/www/barorez_total/server_c/deploy/apache_printsvc.conf
</VirtualHost>
```

```bash
sudo apachectl configtest
sudo systemctl reload apache2
```

### 4) 검증

```bash
# 내부 (서버 SSH 안에서)
curl -sS http://127.0.0.1:3000/health
pm2 logs barorez-server-c --lines 50

# 외부
curl -sS https://barorez.com/printsvc/health
wscat -c wss://barorez.com/printsvc/ws   # 5초 후 close 4001 'auth timeout' 정상
```

---

## 환경 변수 (`.env`)

| 키 | 설명 |
|---|---|
| `PORT` | HTTP 서버 포트. 기본 3000. Apache 가 이 포트로 프록시. |
| `PRINT_SHARED_SECRET` | PHP `cfg/config.inc.php` 의 동명 상수와 **반드시 같은 값**. 비어있으면 부팅 실패. |
| `PHP_API_BASE` | PHP API 베이스 URL. 운영: `https://barorez.com`. 검증: `http://127.0.0.1:18080`. |
| `SQLITE_PATH` | SQLite 파일 경로. 기본 `./data/server_c.db`. |
| `LOG_LEVEL` | `trace` ~ `fatal`. 운영 권장 `info`. |
| `NODE_ENV` | `production` 일 때 pino-roll 파일 로그 활성화. |

---

## 로그

- 개발: pino-pretty, 콘솔 한 곳
- 운영: stdout (PM2 캡처) + `logs/server_c-YYYY-MM-DD.log` 일별 분할, 30일 보관

주요 이벤트 (`event` 필드):
`webhook_received`, `client_connected`, `client_authenticated`, `client_disconnected`,
`job_dispatched`, `ack_received`, `retry_scheduled`, `job_failed`,
`php_callback_printed`, `php_callback_failed_call`, `php_callback_retry`, `php_callback_failed`.

---

## 데이터 영속화

- 단일 SQLite 파일 (`data/server_c.db`), WAL 모드
- 부팅 시 미완료 작업(`status IN ('queued','sent')`) 자동 복구
- 백업이 필요한 경우 정기적으로 `data/server_c.db*` 복사 (WAL 파일 포함)

---

## HMAC 시크릿 회전

새 시크릿 발급:
```bash
openssl rand -hex 32        # 또는: php -r "echo bin2hex(random_bytes(32)).PHP_EOL;"
```

동기화 (PHP ↔ Server C 가 같은 값이어야 함):

1. PHP `cfg/config.inc.php` 의 `PRINT_SHARED_SECRET` 갱신
2. Server C `.env` 의 `PRINT_SHARED_SECRET` 갱신
3. 운영 적용
   ```bash
   sudo systemctl reload apache2   # PHP-FPM 재로드
   pm2 restart barorez-server-c
   ```
4. 검증: PHP 에서 활성 매장 주문 1건 → Server C 로그에 `webhook_received` 확인

`PRINT_CLIENT_TOKEN_SALT` 회전 시에는 발급된 모든 매장 PC 토큰을 재발급해야 함 (시간 소요 큼, 운영 협의 권장).

---

## 트러블슈팅

| 증상 | 점검 |
|---|---|
| `[config] 환경변수 검증 실패: PRINT_SHARED_SECRET 가 비어있습니다` | `.env` 미설정 또는 빈 값. `cp .env.example .env` 후 값 입력. |
| webhook 401 `invalid signature` | PHP 와 Server C 의 `PRINT_SHARED_SECRET` 불일치. 동일 값 확인. |
| WS 접속 후 close 4001 | 5초 내 auth 메시지 미발송. client 측 코드 점검. |
| WS 접속 후 close 4003 `invalid token` | `print_client_t.auth_token_hash` 와 client 가 보낸 평문 토큰 불일치. PHP `cfg/config.inc.php` 의 `PRINT_CLIENT_TOKEN_SALT` 확인. |
| WS 접속 후 close 4006 `pong timeout` | client 가 ping 메시지에 30초 내 pong 미응답. 네트워크 또는 client 점검. |
| PHP `[print] webhook send failed` error_log | Server C 미기동. `pm2 status` 확인. |
| PHP 콜백 도달 안 함 (DB 와 SQLite 불일치) | Server C 로그에서 `php_callback_failed` 검색. PHP API 가용성 확인. |

---

## 디렉토리

```
server_c/
├── src/
│   ├── config.ts          dotenv + zod 검증
│   ├── logger.ts          pino (+ pino-pretty / pino-roll)
│   ├── metrics.ts         에러 카운터
│   ├── dispatcher.ts      capability 매칭 + ACK 타임아웃
│   ├── store/sqlite.ts    jobs 테이블 CRUD
│   ├── middleware/hmac.ts X-Signature 검증
│   ├── routes/
│   │   ├── webhook.ts     POST /webhook/print
│   │   └── health.ts      GET /health
│   ├── ws/
│   │   ├── server.ts      ws upgrade + ping/pong
│   │   ├── auth.ts        첫 메시지 검증 + PHP verify
│   │   ├── ack.ts         ACK → SQLite + PHP 콜백
│   │   ├── sync.ts        재접속 일괄 디스패치
│   │   ├── clients.ts     Map<shop, client>
│   │   ├── protocol.ts    zod 메시지 스키마
│   │   └── send.ts
│   ├── queue/retry.ts     백오프 + 재시도 워커
│   ├── php/
│   │   ├── clientAuth.ts  토큰 검증 + LRU 5분 캐시
│   │   └── callback.ts    PHP api/print_jobs_status.php POST
│   └── index.ts           Express + WS + retry worker 부트
├── scripts/
│   └── dev_php_mock.mjs   개발용 PHP API 모킹
├── deploy/
│   └── apache_printsvc.conf  Apache 리버스 프록시 템플릿
├── ecosystem.config.cjs   PM2 설정
├── .env.example
├── package.json
└── tsconfig.json
```
