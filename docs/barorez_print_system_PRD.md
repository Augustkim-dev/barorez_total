# barorez 영수증 자동 출력 시스템 PRD

| 항목 | 내용 |
|---|---|
| 문서명 | barorez 매장 영수증 자동 출력 시스템 PRD |
| 버전 | v0.1 (초안) |
| 작성일 | 2026-05-12 |
| 작성자 | August |
| 대상 제품 | barorez.com (QR/앱 주문 시스템) |
| 상태 | 설계 합의 완료, 구현 착수 직전 |

---

## 1. 개요

### 1.1 배경

barorez.com은 식당·카페에서 손님이 QR 코드 또는 앱을 통해 주문할 수 있는 시스템이다. 현재 주문은 매장 PC의 화면에서만 확인이 가능하며, 매장 직원이 직접 화면을 확인하고 주방·바·카운터에 전달하는 방식으로 운영되고 있다.

이는 다음과 같은 문제를 야기한다.

- 주문 누락 위험 (화면 확인 지연)
- 주방·바·카운터 간 정보 전달 비효율
- 손님이 주문 내용을 받지 못함 (영수증 부재)
- 직원의 동선이 화면에 묶이는 비효율

### 1.2 문제 정의

> **"손님이 주문을 한 직후, 매장의 USB 영수증 프린터에서 주문 내용이 자동으로 출력되어야 한다."**

이때 매장에는 다음과 같은 다양한 구성이 존재할 수 있다.

- 1개 매장에 프린터가 1개 (단순 카페)
- 1개 매장에 프린터가 여러 개 (주방용/카운터용/바용 등)
- 1개 매장에 PC가 여러 대, 각 PC가 다른 프린터를 담당

### 1.3 솔루션 요약

PHP/MariaDB로 운영 중인 기존 주문 처리 시스템에 다음 컴포넌트를 신규로 추가한다.

1. **Server C** — WebSocket 허브 (Node.js)
2. **Windows 클라이언트** — 매장 PC의 트레이 앱 (C# .NET 8)
3. **Admin UI 확장** — PC 등록·라우팅 규칙 설정 화면 (기존 PHP admin 확장)

전체 흐름: **주문 발생 → PHP가 Server C로 Webhook → Server C가 해당 매장 PC로 WebSocket Push → 매장 PC가 USB 프린터로 ESC/POS 출력 → ACK 회수**.

---

## 2. 목표 및 비목표

### 2.1 목표

| # | 목표 | 측정 기준 |
|---|---|---|
| G1 | 주문 발생 후 영수증 출력까지의 지연을 1초 이내로 한다 | E2E 지연 측정 |
| G2 | 매장별 프린터 1개 이상을 정확한 라우팅으로 처리 | 라우팅 정확도 100% |
| G3 | 매장 PC가 일시적으로 오프라인이어도 주문 손실이 없어야 한다 | 재접속 시 미출력 분 자동 출력 |
| G4 | 출력 성공/실패가 모두 추적 가능해야 한다 | 모든 job의 상태 로깅 |
| G5 | 매장에서 별도 IT 지식 없이 설치·운영 가능 | 단일 인스톨러, 자동 시작, 자동 업데이트 |
| G6 | Server C는 향후 PHP/DB 서버와 물리적으로 분리 가능하도록 설계 | HTTP 기반 통신, 환경 변수 분리 |

### 2.2 비목표

- 본 PRD는 **주문 시스템(QR 주문/앱 주문) 자체의 변경**을 다루지 않는다.
- POS 결제 단말기와의 통합은 다루지 않는다.
- 외부 배달 플랫폼(배민/요기요) 연동은 다루지 않는다.
- iOS/macOS/Linux 매장 PC 지원은 다루지 않는다 (Windows 전용).

### 2.3 성공 지표

- **MVP 단계**: 1개 매장에서 1주일 무중단 운영, 출력 실패율 < 1%
- **확장 단계**: 10개 매장 동시 운영, 출력 평균 지연 < 1초
- **운영 단계**: 100개 매장 동시 연결 시 Server C 부하 안정

---

## 3. 시스템 아키텍처

### 3.1 전체 흐름

주문 발생부터 출력까지의 5단계 흐름은 다음과 같다.

```
1. 주문 발생 (PHP + MariaDB)
       ↓
2. Webhook 전송 (PHP → Server C)
       ↓
3. Server C: 매장 소켓 매칭 + 라우팅
       ↓
4. 윈도우 클라이언트: WebSocket Push 수신
       ↓
5. 프린터 출력 (ESC/POS over USB)
       ↓
6. ACK 회수 → PHP의 print_job 상태 업데이트
```

### 3.2 배포 토폴로지

```
┌──────── 클라우드 서버 (단일 머신, 분리 가능 설계) ────────┐
│                                                          │
│   PHP + MariaDB          Server C        Redis/SQLite    │
│   (기존 + 수정)          (Node.js TS)    (큐/상태)        │
│   - Admin UI             - WebSocket                     │
│   - Webhook 발송         - 매칭/라우팅                    │
│   - ACK 수신             - 재시도/큐                      │
│                                                          │
└──────────────────────────────────────────────────────────┘
                    ↑                        ↕ WSS (WebSocket Secure)
                    │ HTTPS                  ↓
              [손님 주문]              ┌────── 매장 PC (Windows) ──────┐
                                       │                              │
                                       │   Windows Client              │
                                       │   (C# .NET 8, 트레이 앱)      │
                                       │     ↓ USB ESC/POS             │
                                       │   영수증 프린터 (1개 이상)     │
                                       │                              │
                                       └──────────────────────────────┘
```

### 3.3 분리 가능 설계 원칙

Server C는 초기에는 PHP/MariaDB와 같은 물리 서버에 배치하지만, 향후 분리할 수 있도록 다음 원칙을 따른다.

- PHP와 Server C 간 통신은 **HTTP만** 사용 (DB 직접 공유 금지)
- Server C는 PHP의 MariaDB에 직접 접근하지 않음 — 별도 저장소(Redis/SQLite) 사용
- 모든 엔드포인트는 환경 변수로 주입 (`PRINT_SERVER_URL`, `PHP_API_BASE`)
- 인증은 공유 시크릿 기반 (localhost 신뢰에 의존 금지)
- 로그·설정·데이터 파일 경로를 서로 침범하지 않음

---

## 4. 컴포넌트 명세

### 4.1 PHP 백엔드 (기존 + 수정)

**역할**: 기존 주문 처리 로직에 print_job 생성과 Webhook 발송을 추가한다.

**신규/수정 사항**

- 주문 INSERT 직후, `print_route_rule_t` 조회 → 메뉴별로 `print_job_t`에 N건 INSERT
- `POST {PRINT_SERVER_URL}/webhook/print` 로 Webhook 발송 (HMAC 서명 헤더 포함)
- `PATCH /api/print-jobs/{id}/status` 엔드포인트 신설 (Server C가 ACK 통보용으로 호출)
- `POST /api/print-jobs/{id}/retry` 엔드포인트 신설 (관리자 수동 재출력용)

**스택**: PHP (기존 그대로), MariaDB

**비기능 요구사항**: Webhook 발송은 fire-and-forget으로 처리 (응답 대기 < 500ms), 실패 시 PHP는 별도 재시도하지 않고 Server C 측에서 처리.

### 4.2 Server C (신규)

**역할**: PHP와 매장 PC를 중계하는 WebSocket 허브.

**주요 기능**

- PHP로부터 Webhook 수신 → 작업 큐에 적재
- 매장 PC와의 WebSocket 연결 유지 (인증, ping/pong, 재접속)
- `shop_id + capability` 매칭으로 적절한 클라이언트에 Push
- ACK 대기 (10초) → 미수신 시 재시도 (1s → 3s → 10s, 최대 3회)
- 결과를 PHP API로 통보 (`PATCH /api/print-jobs/{id}/status`)
- 매장 재접속 시 미전송 작업 동기화

**스택**: Node.js 20 LTS + TypeScript, `ws` 라이브러리, Express (HTTP 엔드포인트용), `better-sqlite3` (초기) 또는 `ioredis` (확장 시)

**프로세스 관리**: PM2 또는 systemd 서비스

**포트**: 내부 `127.0.0.1:3000`, Apache 리버스 프록시로 `https://barorez.com/printsvc/*` 노출

**비기능 요구사항**:
- 동시 WebSocket 연결 1,000개까지 단일 인스턴스에서 처리
- 메모리 사용량 < 200MB 평상시
- 재시작 시 미완료 작업이 SQLite/Redis에서 복구되어야 함

### 4.3 Windows 클라이언트 (신규)

**역할**: 매장 PC에서 백그라운드로 동작하며 Server C로부터 Push를 받아 USB 프린터로 출력한다.

**주요 기능**

- 부팅 시 자동 시작 (레지스트리 Run 키 또는 시작 폴더)
- 시스템 트레이 아이콘 (상태 표시: 정상/연결끊김/오류)
- Server C로 WebSocket 연결 (`client_id` + `auth_token` 인증)
- ping/pong 유지 (30초 주기), 연결 끊김 시 지수 백오프 재접속
- Push 메시지 파싱 → ESC/POS 명령으로 변환 → 지정 USB 프린터로 출력
- 출력 결과를 ACK로 회신 (성공/실패 + 사유)
- 로컬 로그 파일 (최근 30일, 일별 분할)
- 트레이 메뉴: "최근 출력 50건", "상태 확인", "재연결", "로그 폴더 열기", "버전 정보"

**스택 (2단계 전략)**

1. **Phase 1 — 프로토타입**: Python 3.11+, `websockets`, `python-escpos`, `pystray`, PyInstaller
   - 목적: 1주일 내 흐름 검증 (한 매장 베타)
2. **Phase 2 — 본 빌드**: C# .NET 8, WinForms (트레이용), `ClientWebSocket` (내장), `ESCPOS_NET`, `Velopack` (자동 업데이트 + 인스톨러)
   - 단일 .exe (~15MB), .NET 런타임 불필요 (self-contained)

**ESC/POS 출력 방식**: Windows Print Spooler raw 모드 (`winspool.drv` P/Invoke)를 1순위로 사용. 매장에서 이미 정품 드라이버를 설치한 상태를 그대로 활용하고 ESC/POS 바이트만 raw로 흘려보낸다. LibUsbDotNet을 통한 직접 USB 접근은 fallback으로만 보관.

### 4.4 Admin UI 확장 (기존 PHP 확장)

기존 barorez.com 관리자 페이지에 다음 메뉴를 추가한다.

- **매장 PC 관리**: PC(client) 등록, 인증 토큰 발급/재발급, 마지막 접속 시각 표시, 비활성화
- **프린터 라우팅 규칙**: 메뉴 카테고리별 출력 대상 프린터 타입(`kitchen` / `counter` / `bar`) 매핑
- **출력 작업 로그**: 최근 print_job 목록, 상태별 필터, 실패 사유 표시, 수동 재출력 버튼
- **매장별 출력 현황 대시보드**: 오늘 출력 건수, 실패 건수, 평균 지연

**스택**: 기존 PHP admin과 동일

### 4.5 DB 마이그레이션 (1회성)

신규 테이블 3개를 추가하는 SQL 마이그레이션 스크립트.

---

## 5. 데이터 모델

### 5.1 신규 테이블

#### 5.1.1 `print_client_t` — 매장 PC 등록

| 컬럼 | 타입 | 설명 |
|---|---|---|
| `client_idx` | INT PK AUTO_INCREMENT | PK |
| `shop_idx` | INT FK | shop_t 참조 |
| `client_name` | VARCHAR(100) | '카운터 PC', '바 PC' 등 |
| `auth_token` | VARCHAR(64) | 랜덤 토큰 (관리자가 발급) |
| `capabilities` | VARCHAR(255) | 콤마 구분: 'kitchen,counter' |
| `last_connected_at` | DATETIME NULL | 마지막 WebSocket 접속 시각 |
| `last_disconnected_at` | DATETIME NULL | 마지막 끊김 시각 |
| `is_active` | TINYINT(1) DEFAULT 1 | 비활성화 시 0 |
| `wdate` | DATETIME DEFAULT CURRENT_TIMESTAMP | |
| `udate` | DATETIME ON UPDATE CURRENT_TIMESTAMP | |

#### 5.1.2 `print_route_rule_t` — 라우팅 규칙

| 컬럼 | 타입 | 설명 |
|---|---|---|
| `rule_idx` | INT PK AUTO_INCREMENT | PK |
| `shop_idx` | INT FK | shop_t 참조 |
| `category_idx` | INT FK | 메뉴 카테고리 참조 |
| `printer_type` | ENUM('kitchen','counter','bar') | 출력 대상 capability |
| `also_print_at_counter` | TINYINT(1) DEFAULT 1 | 카운터에도 동시 출력할지 여부 |
| `wdate` | DATETIME DEFAULT CURRENT_TIMESTAMP | |

기본 규칙: 카운터 영수증은 모든 주문에 대해 발행, 주방/바 출력은 카테고리별로 결정.

#### 5.1.3 `print_job_t` — 출력 작업

| 컬럼 | 타입 | 설명 |
|---|---|---|
| `job_idx` | BIGINT PK AUTO_INCREMENT | PK |
| `shop_idx` | INT FK | |
| `order_idx` | INT FK | orders 참조 |
| `printer_type` | ENUM('kitchen','counter','bar') | |
| `payload` | JSON | 출력 내용 (테이블, 메뉴, 시각 등) |
| `status` | ENUM('queued','sent','printed','failed') DEFAULT 'queued' | |
| `attempt_count` | TINYINT DEFAULT 0 | 시도 횟수 |
| `assigned_client_idx` | INT FK NULL | 실제로 보낸 client |
| `last_error` | TEXT NULL | 실패 사유 |
| `created_at` | DATETIME DEFAULT CURRENT_TIMESTAMP | |
| `sent_at` | DATETIME NULL | Push 전송 시각 |
| `printed_at` | DATETIME NULL | ACK 수신 시각 |

### 5.2 인덱스

- `print_job_t (shop_idx, status, created_at)` — 미전송 작업 조회용
- `print_client_t (shop_idx, is_active)` — 매장별 활성 클라이언트 조회용

---

## 6. 인증 및 라우팅 모델

### 6.1 인증 단위는 PC

매장이 아닌 **PC (client) 단위**로 인증한다. 이유: 한 매장에 PC가 여러 대 있을 수 있고, 각 PC가 서로 다른 프린터를 담당할 수 있기 때문.

- 각 PC는 고유 `client_id` (= `client_idx`) 와 `auth_token` (64자 랜덤) 보유
- WebSocket 연결 시 첫 메시지로 인증 정보 전달
- Server C는 토큰을 `print_client_t.auth_token`과 검증 (해시 저장 권장)

### 6.2 Capability 기반 라우팅

각 PC는 접속 시 자신이 보유한 프린터의 capability 목록을 등록한다 (예: `["kitchen", "counter"]`).

주문 발생 시 PHP는 `print_route_rule_t`을 보고 메뉴별로 출력 대상 `printer_type`을 결정 → 해당 capability를 가진 client에게 Server C가 Push.

같은 capability를 여러 PC가 가진 경우의 우선순위는 `client_idx`가 작은 쪽 또는 마지막 접속이 최근인 쪽 (구현 시 결정).

### 6.3 토큰 관리

- 발급: Admin UI의 "PC 추가" 버튼 → 토큰 자동 생성 → 매장 운영자에게 1회 노출
- 재발급: 보안 사고 또는 PC 교체 시 Admin UI에서 재발급, 기존 토큰 즉시 무효
- 저장: DB에는 해시(SHA-256 + salt)로 저장, 평문은 발급 직후 1회만 표시
- 전송: WSS(TLS) 위에서만 전송

---

## 7. 메시지 프로토콜

### 7.1 PHP → Server C: Webhook

```http
POST /webhook/print HTTP/1.1
Content-Type: application/json
X-Signature: <HMAC-SHA256 of body with SHARED_SECRET>

{
  "job_ids": [12345, 12346],
  "shop_id": 42
}
```

응답: `200 OK` (즉시 응답, 처리는 비동기)

### 7.2 Server C → Client: Push

```json
{
  "type": "print_job",
  "job_id": 12345,
  "shop_id": 42,
  "printer_type": "kitchen",
  "payload": {
    "table_name": "3번",
    "order_time": "2026-05-12 14:23:01",
    "items": [
      { "name": "김치찌개", "qty": 1, "options": ["맵게"] },
      { "name": "공기밥", "qty": 2 }
    ],
    "memo": "포크 추가요"
  }
}
```

### 7.3 Client → Server C: ACK

성공:
```json
{ "type": "ack", "job_id": 12345, "status": "printed", "printed_at": "2026-05-12T14:23:02.341Z" }
```

실패:
```json
{ "type": "ack", "job_id": 12345, "status": "failed", "error_code": "PRINTER_OFFLINE", "error_message": "프린터 응답 없음" }
```

### 7.4 클라이언트 ↔ Server C: 인증

연결 직후 클라이언트가 보내는 첫 메시지:
```json
{
  "type": "auth",
  "client_id": 17,
  "token": "...",
  "capabilities": ["kitchen", "counter"],
  "app_version": "1.0.3",
  "last_known_job_id": 12340
}
```

Server C는 토큰 검증 후 `{ "type": "auth_ok" }` 또는 `{ "type": "auth_fail", "reason": "..." }` 회신.
`last_known_job_id`가 있으면 그 이후의 미전송 작업을 일괄 Push.

### 7.5 Server C → PHP: ACK 통보

```http
PATCH /api/print-jobs/12345/status HTTP/1.1
Content-Type: application/json
X-Signature: <HMAC-SHA256>

{
  "status": "printed",
  "printed_at": "2026-05-12T14:23:02.341Z",
  "client_idx": 17
}
```

### 7.6 Ping/Pong

WebSocket 표준 프레임 사용, 30초 주기. 연속 2회 무응답 시 연결 끊김으로 간주.

---

## 8. ACK / 재시도 / 오류 처리

### 8.1 정상 흐름 타임라인

```
T+0      PHP: orders INSERT
T+0.05   PHP: print_job_t INSERT (status=queued)
T+0.05   PHP → Server C: Webhook (job_ids)
T+0.10   Server C: 매장 client 매칭 → Push 전송 (status=sent, sent_at)
T+0.20   Client: USB 프린터 출력 성공
T+0.22   Client → Server C: ACK (status=printed)
T+0.22   Server C: 자체 큐 상태 업데이트 (printed_at)
T+0.22   Server C → PHP: PATCH /api/print-jobs/{id}/status
```

### 8.2 재시도 정책

- ACK 미수신 (10초 초과) → 재시도
- 백오프: 1초 → 3초 → 10초 (최대 3회)
- 3회 모두 실패 → `status=failed`, Admin UI에 알림, 매장 PC 트레이 아이콘 경고

### 8.3 매장 오프라인 처리

- Webhook은 도착했으나 해당 매장 client 소켓이 없는 경우: `status=queued` 유지
- 매장 client가 재접속하면 `last_known_job_id` 이후 미전송 작업을 일괄 Push
- 24시간 이상 미전송 작업은 별도 알림 (관리자 SMS/이메일)

### 8.4 중복 방지

- 같은 `job_id`가 재전송될 수 있음 (네트워크 불안 + 재시도)
- Client는 최근 처리한 `job_id`를 24시간 캐시 → 중복 수신 시 다시 ACK만 보내고 출력은 생략

### 8.5 수동 재출력

- Admin UI 또는 매장 사장님 화면에서 "다시 출력" 버튼
- PHP가 같은 order의 새로운 `print_job_t` row를 생성하고 Webhook 발송
- 흐름은 일반 출력과 동일

### 8.6 오류 코드 카탈로그

| 코드 | 의미 | 처리 |
|---|---|---|
| `PRINTER_OFFLINE` | USB 연결 안됨 / 전원 꺼짐 | 재시도, 매장 트레이 알림 |
| `PRINTER_OUT_OF_PAPER` | 용지 없음 | 재시도, 매장 트레이 알림 |
| `PRINTER_OVERHEATED` | 과열 | 30초 대기 후 재시도 |
| `UNSUPPORTED_ENCODING` | 한글 인코딩 실패 | 즉시 failed (재시도 무의미) |
| `UNKNOWN` | 분류되지 않은 오류 | 재시도, 상세 메시지 로깅 |

---

## 9. 로깅

### 9.1 Server C 로그

구조화된 JSON 로그 (pino 또는 winston 사용 권장).

이벤트 종류:
- `webhook_received` (job_ids, shop_id)
- `client_connected` / `client_disconnected` (client_id, ip)
- `push_sent` (job_id, client_id)
- `ack_received` (job_id, status, duration_ms)
- `push_retry` (job_id, attempt, reason)
- `push_failed` (job_id, final_error)
- `php_callback_sent` (job_id, http_status)

보관 정책: 30일, 일별 파일 분할, gzip 압축.

### 9.2 Windows Client 로그

로컬 파일 (`%LOCALAPPDATA%\barorez-printer\logs\YYYY-MM-DD.log`).

이벤트 종류:
- WebSocket 연결/끊김 (시각, 사유)
- 작업 수신 (job_id, printer_type)
- 출력 시도 (printer_name, byte_count)
- 출력 성공/실패 (소요 시간, 오류 상세)
- 프린터 상태 체크 결과

트레이 메뉴 "로그 폴더 열기"로 직접 접근 가능.

### 9.3 데이터베이스 로그

`print_job_t`에 모든 상태 전이가 자동 기록됨 (status, attempt_count, last_error, 각 타임스탬프). 별도 로그 테이블 불필요.

---

## 10. 기술 스택 결정

### 10.1 Server C

| 항목 | 선택 | 이유 |
|---|---|---|
| 런타임 | Node.js 20 LTS | WebSocket 생태계 성숙, 비동기 I/O 최적 |
| 언어 | TypeScript | PHP/Client와 페이로드 타입 정의 공유 |
| WebSocket | `ws` (직접 사용) | socket.io의 오버헤드 불필요, 프로토콜 단순 |
| HTTP | Express | Webhook 수신 + 헬스체크 엔드포인트 |
| 저장소 | better-sqlite3 (초기) | 단일 머신 + 단일 인스턴스 단계엔 충분 |
| 향후 확장 | Redis (ioredis) | 다중 인스턴스 시 이전 |
| 프로세스 | PM2 | 무중단 재시작, 로그 수집 |

**PHP 대안 미채택 사유 (2026-05-12 보강)**: Node.js 외에 PHP (Workerman/Swoole)로도 WebSocket 허브 구현이 가능하며 100매장/1,000연결 규모에서는 성능상 차이가 크지 않다. 그럼에도 PHP를 채택하지 않은 이유는 (1) 현재 코드베이스(`vendor/`)에 해당 라이브러리가 없어 학습·도입 비용이 새로 발생하고, (2) 어차피 Apache의 요청-응답 PHP와는 별도 데몬으로 띄워야 하므로 "기존 PHP 스택에 자연스럽게 얹힌다"는 통합 이점이 없으며, (3) `ws` 생태계가 WebSocket 허브로서 더 성숙하고, (4) TypeScript로 메시지 프로토콜(§7)의 타입을 강하게 공유할 수 있기 때문이다. 향후 팀 인력 구성이 PHP 전담으로 바뀌면 재검토 대상.

### 10.2 Windows Client

| 단계 | 선택 | 이유 |
|---|---|---|
| Phase 1 (검증) | Python 3.11 + python-escpos + pystray | 1주 안에 흐름 검증, 빠른 반복 |
| Phase 2 (양산) | C# .NET 8 + WinForms + ESCPOS_NET + Velopack | ESC/POS 안정성, 메모리·배포 효율, 트레이/USB 네이티브 지원 |

**Electron 미채택 사유**: USB 직접 제어 시 libusb 드라이버 교체(Zadig)가 필요하고, 이는 매장의 기존 프린터 드라이버를 무력화하는 부작용을 일으킨다. Node 네이티브 모듈 빌드 의존성도 운영상 위험.

**Flutter Windows 미채택 사유**: system_tray, USB ESC/POS 패키지의 성숙도 부족, Windows 빌드 안정성 이슈.

### 10.3 인스톨러 / 자동 업데이트

**Velopack** 단독 사용.

- 단일 .exe 인스톨러 생성
- 자동 업데이트 채널 (앱 실행 시 백그라운드로 새 버전 다운, 다음 실행 시 적용)
- 정적 호스팅으로 업데이트 채널 운영 가능 (S3 / Cloudflare R2 / 자체 서버)
- 코드 서명: 초기에는 미서명 (점주 첫 설치 시 SmartScreen 경고 1회 발생) → 운영 안정화 후 EV 코드서명 인증서 도입 검토

### 10.4 빌드 명령 (참고)

```bash
# .NET 8 자체 포함 단일 파일 빌드
dotnet publish -c Release -r win-x64 --self-contained true /p:PublishSingleFile=true

# Velopack 패키징
vpk pack --packId barorez-printer --packVersion 1.0.0 --packDir publish/ --mainExe barorez-printer.exe
```

---

## 11. 보안 요구사항

### 11.1 통신 보안

- 손님 ↔ PHP: HTTPS (기존)
- PHP ↔ Server C: localhost 통신이지만 HMAC-SHA256 서명 헤더 필수
- Server C ↔ Client: WSS (TLS) — barorez.com 인증서 재활용
- Client ↔ Printer: USB 로컬 통신 (별도 보안 불필요)

### 11.2 인증/인가

- Client → Server C: `client_id + auth_token`, 토큰은 DB에 해시 저장
- PHP ↔ Server C: 공유 시크릿 기반 HMAC, 환경 변수에서 읽음
- Admin UI 접근: 기존 barorez.com 관리자 세션 재활용

### 11.3 시크릿 관리

- 환경 변수 사용 (`.env` 파일은 git 제외)
- 운영 환경: 서버의 환경 변수 또는 secrets 디렉토리(권한 600)
- 시크릿 회전 정책: 6개월 또는 인력 이동 시 즉시

### 11.4 개인정보

- 출력 페이로드에 손님 개인정보(전화번호, 주소) 포함 금지
- 출력에 필요한 최소 정보(테이블 번호, 메뉴, 옵션, 메모, 시각)만 전송
- Server C 로그에 페이로드 전문 저장 금지 (job_id, shop_id, status만)

---

## 12. 운영 요구사항

### 12.1 모니터링

- Server C: `/health` 엔드포인트 (uptime, active connections, queue size, error count)
- 외부 모니터링: UptimeRobot 또는 Cronitor로 1분 단위 헬스체크
- Admin UI 대시보드: 매장별 출력 실패율, 평균 지연 시간

### 12.2 알림

- 출력 3회 연속 실패 → 매장 트레이 아이콘 빨간 경고 + 관리자 알림
- 매장 PC 1시간 이상 미접속 → 매장 사장님에게 알림 (선택)
- Server C 다운 → 운영자 SMS

### 12.3 백업

- MariaDB: 기존 백업 정책 재활용
- Server C SQLite: 1일 1회 dump → 클라우드 스토리지
- 매장 PC 로그: 매장 PC 로컬 30일 보관, 별도 중앙 수집 안 함

### 12.4 배포

- Server C: PM2 zero-downtime reload (`pm2 reload`)
- Windows Client: Velopack 자동 업데이트
- PHP: 기존 배포 프로세스 재활용

---

## 13. 단계별 일정 (Phased Rollout)

### Phase 1 — 데이터 모델 + 백엔드 골격 (1주차)

- [ ] DB 마이그레이션 SQL 작성 및 검증
- [ ] PHP 측 print_job 생성 로직 추가
- [ ] PHP 측 ACK 수신 API 작성 (`PATCH /api/print-jobs/{id}/status`)
- [ ] HMAC 서명 미들웨어 구현

### Phase 2 — Server C 구현 (2주차)

- [ ] Node.js + TypeScript 프로젝트 초기화
- [ ] Webhook 수신 엔드포인트
- [ ] WebSocket 서버 + 인증
- [ ] 매장 client 매칭 + Push
- [ ] ACK 처리 + 재시도 큐 (SQLite)
- [ ] PM2 + Apache 리버스 프록시 세팅

### Phase 3 — Python 프로토타입으로 한 매장 베타 (3주차)

- [ ] Python 클라이언트 작성 (websockets + python-escpos + pystray)
- [ ] PyInstaller 단일 .exe 빌드
- [ ] 1개 매장에 설치, 1주일 운영 모니터링
- [ ] 발견된 이슈를 PRD와 코드에 반영

### Phase 4 — Admin UI + 라우팅 규칙 (4주차)

- [ ] PC 등록/토큰 발급 화면
- [ ] 라우팅 규칙 설정 화면
- [ ] 출력 작업 로그 조회 화면
- [ ] 수동 재출력 기능

### Phase 5 — C# 본 빌드 + Velopack (5~7주차)

- [ ] C# 프로젝트 초기화 (.NET 8 + WinForms)
- [ ] WebSocket 클라이언트 + 자동 재접속
- [ ] ESCPOS_NET 통합 + Print Spooler raw 출력
- [ ] 트레이 UI + 로컬 로그 + 트레이 메뉴
- [ ] Velopack 통합 + 자동 업데이트 채널 구축
- [ ] EXE 코드 서명 검토

### Phase 6 — 확장 (8주차 이후)

- [ ] 다중 매장 동시 운영 모니터링
- [ ] Redis 이전 검토 (트래픽이 SQLite 한계에 가까워질 때)
- [ ] Server C 분리 (별도 인스턴스)

---

## 14. 위험 요소 및 미해결 사항

### 14.1 기술적 위험

| 위험 | 영향 | 대응 |
|---|---|---|
| 매장 인터넷 불안정 | 출력 지연/실패 | 재접속 시 동기화 메커니즘, 로컬 큐 |
| 프린터 드라이버 이슈 | 출력 실패 | 매장별 프린터 모델 사전 확인, ESCPOS_NET의 호환 모드 |
| 한글 인코딩 문제 | 한글 깨짐 | EUC-KR/CP949 우선, 프린터 펌웨어 한글 코드페이지 설정 사전 점검 |
| Server C 단일 장애점 | 전 매장 출력 중단 | 모니터링 + 자동 재시작, 향후 다중 인스턴스 |
| Windows Defender SmartScreen | 첫 설치 시 경고 | 운영 안정화 후 EV 코드 서명 |

### 14.2 운영 위험

| 위험 | 영향 | 대응 |
|---|---|---|
| 매장 PC가 꺼져있는 시간 | 미출력 누적 | 영업 시작 시 자동 동기화, 24h 이상 알림 |
| 프린터 토큰 유출 | 가짜 출력 가능 | 즉시 재발급, 토큰 회전 정책 |
| 매장 PC 교체 | 새 PC에 재설치 필요 | Admin UI에서 손쉽게 PC 추가/비활성화 |

### 14.3 미해결 사항 (Open Questions)

- [ ] 같은 capability를 가진 client가 여러 개일 때 우선순위 정책 (라운드로빈 / 최근 접속 우선 / 명시적 우선순위 필드)
- [ ] 프린터 종류별 ESC/POS 명령 차이 — 어느 모델까지 공식 지원할지
- [ ] 매장이 영업종료 시 client 종료를 명시할지, 항상 켜둘지
- [ ] 매장 사장님이 자신의 모바일에서 출력 상태를 볼 수 있는 화면 — 별도 모바일 앱? barorez 사장님 앱 안의 메뉴?
- [ ] 코드 서명 인증서 도입 시점과 비용

---

## 15. 부록

### 15.1 용어 정의

| 용어 | 정의 |
|---|---|
| Server C | 본 시스템의 WebSocket 허브, Node.js로 구현됨 |
| Client | 매장 PC에서 동작하는 Windows 트레이 앱 |
| Job | 1건의 출력 작업, `print_job_t` 1 row에 해당 |
| Capability | 한 client가 처리할 수 있는 프린터 타입 (kitchen / counter / bar) |
| ACK | 클라이언트가 출력 결과를 Server C에 회신하는 메시지 |
| ESC/POS | EPSON이 정의한 영수증 프린터 명령 표준, 대부분의 영수증 프린터가 따름 |

### 15.2 참고 자료

- ESC/POS 명령 레퍼런스: https://download4.epson.biz/sec_pubs/pos/reference_en/escpos/
- ESCPOS_NET: https://github.com/lukevp/ESC-POS-.NET
- Velopack: https://github.com/velopack/velopack
- python-escpos: https://python-escpos.readthedocs.io/

### 15.3 변경 이력

| 버전 | 일자 | 변경 내용 | 작성자 |
|---|---|---|---|
| v0.1 | 2026-05-12 | 초안 작성 | August |

---

**이 문서는 살아있는 문서입니다. 구현 진행 중 발견되는 이슈와 결정사항은 본 PRD에 즉시 반영합니다.**
