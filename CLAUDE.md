# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this project is

**맛집바로 (Barorez)** — Korean restaurant discovery and QR-table-ordering service. Production domain: `https://barorez.com/`. The repo contains the full PHP web stack (no build step) plus marketing/landing assets and a `db/` and `docs/` workspace that are currently empty.

The PHP application lives entirely under `web/public_html/` and is served as a classic shared-host PHP document root. There is no framework — just PHP 7+ scripts that share globals through a bootstrap include.

## ⚠️ 필수 작업 규칙 (Mandatory Workflow)

### 1. 계획 수립 (Planning)
사용자가 **의견을 물어보거나 작업 요청**을 하면:
1. **Plan 모드로 전환**하여 작업 시작
2. `docs/plans/` 폴더에 작업 계획서 작성
3. 파일명 형식: `NNN.파일이름.md` (예: `001.프로젝트_초기설정.md`)
4. 기존 파일 번호를 확인하여 순차적으로 넘버링

### 2. 작업 완료 기록 (Work Log)
작업이 완료되면:
1. `docs/worklogs/` 폴더에 작업 내역 기록
2. 파일명 형식: `NNN.파일이름.md` (예: `001.프로젝트_초기설정.md`)
3. 계획서와 동일한 번호 사용 권장

### 3. 작업 단위 완료 기준
**소스 코드 작업 시, git push까지 완료해야 하나의 작업 단위가 종료됨**

```
작업 흐름:
[의견 요청] → [Plan 모드 전환] → [계획서 작성] → [작업 수행] → [git commit & push] → [작업일지 작성] → [완료]
```

### 문서 폴더 구조
```
docs/
├── plans/           # 작업 계획서
│   ├── 001.xxx.md
│   └── 002.xxx.md
├── worklogs/        # 작업 완료 기록
│   ├── 001.xxx.md
│   └── 002.xxx.md
└── structure/       # 설계 문서
```

## Three logical apps in one document root

`web/public_html/` hosts three separate UIs that share `cfg/`, `vendor/`, `data/`, and `sessions/`:

| Path | Audience | Entry | Notes |
|------|----------|-------|-------|
| `/app/` | End-user mobile web | `app/index.php` (QR landing), `app/map/index.php` (map) | Customer flow: scan QR → land on shop menu → cart → order. Stores user session in `$_SESSION['mng']` (yes, the variable is named `mng`). |
| `/market/` | Merchant / store owner | `market/index.php` | Store dashboard (table mgmt, menu, orders, settlement, FCM, sales). Uses Bootstrap 4 + Pretendard. |
| `/mng/` | Super admin / back office | `mng/index.php` | Internal admin (members, shops, categories, badges, blog, popups, etc.). Restricted to `mt_level <= 2`; `app/inc/head.php` actively kicks higher levels back to login. |

**Don't conflate them.** Each area has its own `head.inc.php` / `header.php` / `tail.php` includes with different stylesheets and admin-vs-user header logic. `cfg/config.mng.inc.php` flips `$_ADMIN_HEADER` based on whether you're on `login.php`.

## Bootstrap chain (read this before editing any page)

Every page starts with:

```php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
```

`cfg/lib.inc.php` is a ~320KB monolith that:
1. Sets cache headers (admin vs user differ — see top of file).
2. Detects `mng` URL prefix to decide cache strategy.
3. Configures session cookies (file-based sessions in `/sessions/`, name `barorez`, samesite `Lax`).
4. `session_start()` — assume sessions are always live after this include.
5. Defines all global helpers (`alert`, `p_alert`, `gotourl`, `get_image_url`, `thumnail`, `encrypt`, `sendMail`, `handle_file_upload`, …).

After `lib.inc.php`, pages typically include:
- `cfg/config.inc.php` — constants for domain, CDN, OAuth keys, PortOne, Firebase, file upload paths, view/action route constants (`VIEWS_*_PATH`, `*_ACTIONS`, `*_PAGE`).
- `cfg/config.mng.inc.php` — only on admin/market pages, sets `$_ADMIN_HEADER`.
- `cfg/db.inc.php` — instantiates global `$DB` (`new MysqliDb(...)`, host `localhost`, db `baro`).
- `cfg/visit.inc.php` — visitor logging + `ensure_visit($DB, $sh_idx, $tb_no, $mt_idx)` (used by QR flow to bind a table session to a visit row).
- Domain-specific includes from `cfg/`: `coupon.inc.php`, `point.inc.php`, `push.inc.php`, `mail.inc.php`, `bizppurio.lib.php` (SMS), `golf.inc.php`, `comment.inc.php`, `follow.inc.php`, `badge.inc.php`, `htmlpurify.inc.php`, `MobileDetect.inc.php`.

`cfg/table.inc.php` is the canonical map of logical names → DB tables (`shop_t`, `member_t`, `shop_menu_t`, `cart_t`, `orders_t`, …). Use it when you need to know what table backs what.

## App routing pattern

There is no router. Each PHP file is its own URL. The convention inside `app/`:

- `app/<feature>/<page>.php` — view entry that pulls `lib.inc.php`, sets `$_SUB_HEAD_TITLE` / `$hd_num`, includes `app/inc/head.php` + `header.php`, then `include_once $view_path` (`VIEWS_<FEATURE>_PATH . "/<page>.php"`), then `app/inc/tail.php`.
- `app/views/<feature>/<page>.php` — the actual view markup. Reachable only via the entry pages, not directly.
- `app/actions/<feature>/<verb>.php` — POST handlers (login, join, cart mutations, order). Forms POST to these.
- `app/callback/` — OAuth/payment redirect targets (`google_callback.php`, `apple_callback.php`, `naver_callback.php`, `portone_redirect.php`).
- `app/inc/head.php`, `header.php`, `tail.php`, `modal.php`, `head_style0X.php` — shared UI fragments.

`market/` and `mng/` follow the same "entry includes head/header/menu/footer + inline content" pattern but flatter (no `views/` split — page logic lives directly in the entry file).

## QR-table ordering flow (the core business loop)

This is the load-bearing flow; understand it before changing anything in `app/`:

1. Customer scans QR → `https://barorez.com/?tk=<64-hex-token>` → hits `app/index.php`.
2. `app/index.php` checks `$_GET['app_os']` to capture native-app context, optionally hydrates `$_SESSION['mng']` from `member_t.mt_app_token`.
3. `resolve_qr_token($DB, $tk)` joins `shop_table_qr_t` ⨝ `shop_table_t` (must satisfy `st.use_yn='Y'` and `q.sh_idx === st.sh_idx`). Returns `sh_idx`, `tb_idx`, `tb_no`. Invalid tokens → `close_page_with_alert()` (alert + `window.close()`).
4. Stashes `current_sh_idx`, `table_no`, `tb_idx`, `is_qr_order=true` into the session.
5. `ensure_visit(...)` writes a visit row (idempotent for re-scans) and sets `$_SESSION['tv_idx']`.
6. Loads `app/views/shop/list.php` to render the shop's menu.
7. If a customer hits `app/map/index.php` instead, it explicitly **clears** all the QR/visit session keys — that page is the "browse without a table" entry.

Hard-coded entry checks: `cfg/visit.inc.php` skips the visit log when called via `?chk_app=Y`, and the `app/index.php` `?app_reset_qr=1` clears `qr_token`. Both come from the native app wrappers.

## Real-time updates (market dashboard)

`market/sse.php` is a long-poll SSE endpoint that watches `market/last_table_update.txt`'s mtime and pushes a `table-updated` event when it changes. Other market pages (table list, order screens) `touch()` that file after writes — that's how the dashboard refreshes without polling. Don't replace this with `setInterval` polling; the file-mtime contract is what other writers depend on.

Push notifications go through `cfg/push.inc.php` (Firebase HTTP v1 via service account JSON at `/api/savefrom-ce0f7-firebase-adminsdk-fbsvc-5617647285.json`) — JWT-signed OAuth2 token, then standard FCM v1 send.

## Database access

- `$DB` is an instance of `thingengineer/mysqli-database-class` (MysqliDb) — fluent builder: `$DB->where(...)->getOne('shop_t')`, `$DB->insert('table', $arr)`, `$DB->rawQueryOne(...)`.
- Connection lives in `cfg/db.inc.php` (credentials are in source — this is a shared-host setup, not 12-factor).
- There is no migration tool. Schema reference: [`db/baro_sample_backup.sql`](db/baro_sample_backup.sql) — a MariaDB 11.4 mariadb-dump from production. **The data is `LIMIT 10` per table** (the dump comments say `-- WHERE: true limit 10`), so use it for **schema and column comments**, never as fixtures expected to be referentially complete.
- Soft delete: many tables use `del_date IS NULL` to filter live rows (see `app/index.php` shop lookup, and `shop_t.del_date`, `notice_t.del_date`, `qa_t.del_date`, `review_t.del_date`).
- **Tables in the dump (35)**: `member_t`, `shop_t`, `shop_table_t`, `shop_table_qr_t`, `shop_menu_t`, `shop_category_t`, `menu_option_category_t`, `option_menu_t`, `shop_hours_t`, `shop_break_t`, `shop_temp_holiday_t`, `shop_reserve_setting_t`, `shop_reserve_slot_t`, `shop_reserve_penalty_t`, `cart_t`, `cart_options_t`, `orders_t`, `payments_t`, `payment_refunds_t`, `reservation_t`, `review_t`, `review_image_t`, `review_menu_t`, `coupon_t`, `coupon_log_t`, `settle_t`, `setup_t`, `notice_t`, `qa_t`, `table_visit_t`, `visit_t`, `visit_sum_t`, `badge_seen_t`. `cfg/table.inc.php` only registers a small subset — do **not** rely on it as the canonical list; check the SQL dump.

### Schema facts that affect every change in `app/`

- **`member_t.mt_level`** (per the column comment): `1: 탈퇴 (withdrawn), 2: 회원, 5: 딜러회원, 7: 폴리스관리자, 8: 서브관리자, 9: 관리자`. `app/inc/head.php` rejects `mt_level > 2`. Withdrawn members (`mt_level=1`) currently pass that gate — be aware.
- **`member_t` is the master identity table** for *all three apps*. Admins (`mng/`) and merchants (`market/`) are just `member_t` rows with elevated `mt_level` or `mt_seller='Y'`. There is no separate admin-users table.
- **`shop_t.sh_qr_pay_type`** (`PREPAY`/`POSTPAY`) and **`sh_reserve_pay_type`** are per-shop knobs that change the order/reservation flow. Do not assume one mode globally.
- **Operating-hours model is split** across three tables, all keyed by `sh_idx`:
  - `shop_hours_t` — one row per (shop, day-of-week 0=Sun..6=Sat), `bt_type` `OPEN`/`CLOSE`.
  - `shop_break_t` — single brake-time row per shop (PRIMARY KEY is `sh_idx`).
  - `shop_temp_holiday_t` — date-range exceptions.
  Any "is shop open now" check needs all three.
- **Reservation config is also split** across three: `shop_reserve_setting_t` (per-shop), `shop_reserve_slot_t` (per-setting time slots, day-type `WEEKDAY`/`SAT`/`SUN`), `shop_reserve_penalty_t` (per-setting cancellation penalty).
- **`orders_t.ct_snapshot`** is a JSON-serialized cart at submission time (`{items: [{sm_id, menu_name, quantity, unit_price, options}], summary: {sub_total, discount, total}}`). Historical orders do **not** join through `cart_t` — read the snapshot. `cart_t` rows are session-scoped and can be deleted after order placement.
- **`orders_t.ot_pay_type`** (`PREPAID`/`POSTPAID`) is per-order; **`ot_pay_status`** (`UNPAID`/`PAID`/`REFUND`) tracks the money. Statuses (`ot_status`): `PENDING → CONFIRMED → PREPARING → COMPLETED` or `CANCELLED`. `ot_prep_min`/`ot_prep_set_at`/`ot_completed_at` are the prep-timer the merchant sets at acceptance.
- **`orders_t.tv_idx` → `table_visit_t.idx`** binds a QR order to its visit session. `orders_t.rv_idx` → `reservation_t.idx` binds a pre-paid reservation order. Both are nullable — three legitimate origin types: walk-in QR (only `tv_idx`), reservation/prepaid (only `rv_idx`), takeout (`ot_table` NULL). Don't assume mutual exclusion is enforced — check both.
- **`payments_t` vs `payment_refunds_t`** — `payments_t` is one row per PortOne attempt with running totals (`amount_paid`, `amount_refunded`, `amount_remain`); refunds are appended to `payment_refunds_t` and roll up to `payments_t`. `merchant_uid` is the PortOne reference (typically `orders_t.ot_number`).
- **`reservation_t.rv_type`** `VISIT`/`PREPAID`. `PREPAID` rows link to `orders_t` via `reservation_t.ot_idx` (and inversely `orders_t.rv_idx`). Cancellation cascades to the linked order.
- **`shop_table_qr_t`** has UNIQUE `qr_token` (64-char hex, matches the regex `^[a-f0-9]{64}$/i` in `app/index.php`) and UNIQUE `tb_idx`. One QR per table; rotating a token requires deleting+reinserting (no UPDATE convention seen).
- **`shop_table_t.tb_no` is INT** but `app/index.php:168` substitutes `st.idx` for `tb_no` when populating `$_SESSION['table_no']` (it reads `$row['idx']`, not `$row['tb_no']`). That looks like a bug or intentional override — don't "fix" it without checking what `market/` reads from sessions/orders for table identification.
- **`table_visit_t`** is the load-bearing entity for the QR flow. `visit_key` (64-char) is the cookie identity, `tv_status` `ACTIVE`/`CLOSED`, `tv_last_seen_order_idx` is the merchant-side "last acknowledged order" pointer. `ensure_visit()` (in `cfg/visit.inc.php`) is idempotent for re-scans on the same `(sh_idx, tv_table, mt_idx)`.
- **`badge_seen_t`** drives the merchant dashboard's unread badges per `(sh_idx, mt_idx, badge_type)` where badge_type is `TABLE`/`PACK`/`RESERVE`. Updating `last_seen_at` here is what clears the dashboard counter — pair it with the `last_table_update.txt` SSE touch.
- **`coupon_t`** is global (`sh_idx=0`) or per-shop. `ct_target_scope` `ALL`/`MEMBER`; `ct_target_members` is a CSV of `mt_idx`. `ct_type1`/`ct_type2` encode period vs N-days and fixed vs percent. `coupon_log_t` records which member used which.
- **AUTO_INCREMENT gaps**: `member_t` AUTO_INCREMENT=493849, `shop_t` AUTO_INCREMENT=493809 against ~10 rows — the schema was copied/migrated from another system. Don't assume `idx` ranges are dense or that low IDs are old.
- **`notice_t` is MyISAM**, every other table is InnoDB. Likely legacy — be aware if you do anything transactional involving notices.
- **`del_status`** vs **`del_date`**: `member_t.del_status` is `Y` (active) / `N` (banned) — NOT a tombstone (the comment is misleading: `Y:정상, N:정지`). For tombstones look at `mt_level=1` + `mt_rdate`.
- **`mt_app_token`** on `member_t` is what `app/index.php` matches on when the URL carries `?app_os=&app_token=...` — that's how the native-app webview authenticates the embedded session without a login screen.

## External integrations (constants in `cfg/config.inc.php`)

- **PortOne V2** (payment) — `PORTONE_*`. Old IMP keys are commented out.
- **Kakao** native/REST/JS keys, OAuth callback at `api/kakaoOauth.php`.
- **Naver / Apple / Google** SNS login callbacks under `/callback/` and `/api/`.
- **Firebase Cloud Messaging** — service account JSON path baked into `cfg/push.inc.php`.
- **Aligo** SMS / **Bizppurio** alerts (loaded via `cfg/bizppurio.lib.php`).
- **Coupang** API hook in `mng/coupang_api.php`.
- **PHP libs via Composer**: `vendor/` is checked in (no top-level `composer.json`). PHPSpreadsheet, Guzzle, PHPMailer, Firebase JWT, Google API client, OpenAI client, Symfony HttpClient, MysqliDb, Mobile_Detect, etc. are already vendored.

## Working with files and uploads

`cfg/config.inc.php` defines paired `$X_dir` / `$X_url` for every uploadable asset class (members, shop, badge, banner, mainbanner, popup, brand, category, product, review, qa, faq, event, notice, golf_membership, sellmembership, certimembership, wineproduct/vintage/region/winery/type/country/taste/pairing/variety/color/flavor, device, manufacturers, chat, pdf, excel, landing, upjong). Use these constants — don't hardcode `/data/...` paths.

Uploaded images live in `web/public_html/data/<category>/`. Generated thumbnails use the `thumnail()` / `thumbnail_crop_center()` / `resize_crop_image()` helpers from `lib.inc.php`.

## Cache busting

`cfg/config.inc.php` sets `$v_txt = time();` (overwrites the static version string immediately above). Use `?v=<?=$v_txt?>` on every CSS/JS link — the rest of the codebase does. This means cache is busted on every request; do not "optimize" it without checking whether merchant terminals expect fresh assets.

## Develop / serve

There is no build pipeline, package manager script, or test runner in this project.

- **Local serve**: point any PHP-capable server at `web/public_html/` as the document root. PHP built-in works for smoke tests:
  ```powershell
  php -S localhost:8000 -t web/public_html
  ```
  But session cookies are configured for production domain `barorez.com`; expect login flows and OAuth callbacks to misbehave locally without host overrides.
- **DB**: requires a local MySQL named `baro` matching `cfg/db.inc.php`. Schema is not in this repo — pull from the production server or ask before assuming a table layout (`cfg/table.inc.php` is the most reliable map).
- **No tests, no linter, no CI** configured. Manual testing on a staging copy is the norm.

## Conventions and gotchas

- **Mixing `<? ... ?>` and `<?php ... ?>`** is common — short tags are used in `market/`/`mng/` views. Don't normalize them mid-edit; depending on the host's `short_open_tag` setting, "fixing" them is risky.
- **Many `*.php.bak` / `*_old.php` files** exist throughout `market/` and `mng/`. They are reference snapshots, not active. Don't include them via `require`.
- **`error_reporting(E_ERROR)` + `display_errors=1`** in `cfg/config.mng.inc.php` — admin pages intentionally swallow notices/warnings. Don't rely on PHP errors surfacing in the browser.
- **`$_SESSION['mng']`** holds the *user* identity (`mt_idx`, `mt_id`, `mt_level`, ...) on the `app/` side too — the name is misleading. `mt_level <= 2` is the gate for normal users; `app/inc/head.php` rejects higher levels.
- **Sessions are file-based** in `web/public_html/sessions/`. Cleaning that directory will log everyone out. The directory is committed to the repo with live session files — do not edit/commit anything there.
- **Output buffering with gzip**: `ob_start('ob_gzhandler')` is the very first call. Anything that emits before this (BOM, stray whitespace) breaks the page.
- **No CSRF tokens** are wired in by default; auth is session-cookie only. Be aware when adding new POST endpoints under `app/actions/`.
- **Credentials in source**: payment/SNS/FCM keys are committed to `cfg/config.inc.php` and DB password is in `cfg/db.inc.php`. Treat any change to those files as production-impacting and confirm with the user before editing.

## When extending each area

- **Adding an app page**: create `app/<feature>/<page>.php` following the `app/map/index.php` pattern (lib.inc → head → header → include view → tail). Add the matching `app/views/<feature>/<page>.php`. If it needs a new route constant, add to the `Views 경로` / `페이지 경로` blocks in `cfg/config.inc.php`.
- **Adding a market dashboard page**: create `market/<feature>/index.php` following `market/index.php` (head → header → modal → left_menu → content). Touch `market/last_table_update.txt` from any write that the dashboard should react to.
- **Adding an admin page**: create `mng/<feature>.php` following `mng/index.php` (mng/head.inc.php → header.menu.inc.php → content). Add a sidebar entry in `mng/inc/header.sidebar.inc.php` if it should be reachable from navigation.
- **New table operations**: add the table name to `cfg/table.inc.php`. Use `$DB` (MysqliDb) consistently — don't reach for raw mysqli or PDO.
