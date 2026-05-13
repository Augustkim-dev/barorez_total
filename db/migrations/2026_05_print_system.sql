-- =============================================================
-- 영수증 자동 출력 시스템 (barorez print system) — Phase 1 D1
-- 마이그레이션: 신규 테이블 3종 + 인덱스 2종 추가
-- 작성일: 2026-05-12
-- PRD: docs/barorez_print_system_PRD.md §5.1
--
-- 정책:
--   - 기존 테이블 변경 0건 (신규 테이블만 추가)
--   - FK(FOREIGN KEY) 제약 없음, INDEX만 추가
--   - 정합성은 PHP 코드에서 보장
--   - 모든 테이블 InnoDB / utf8mb4
--
-- 적용 전 점검:
--   1) DB 백업 완료 확인
--   2) SHOW TABLES LIKE 'print\_%';  -- 동일 이름 테이블 없음 확인
--   3) 트래픽 적은 시간 권장
--
-- 롤백: 2026_05_print_system_rollback.sql 참조
-- =============================================================


-- -------------------------------------------------------------
-- 1) print_client_t — 매장 PC 등록
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `print_client_t` (
  `client_idx`           INT             NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `shop_idx`             INT             NOT NULL                COMMENT '논리적 FK: shop_t.idx',
  `client_name`          VARCHAR(100)    NOT NULL                COMMENT '카운터 PC, 바 PC 등',
  `auth_token_hash`      VARCHAR(128)    NOT NULL                COMMENT 'SHA-256 + salt 해시',
  `capabilities`         VARCHAR(255)    NOT NULL                COMMENT '콤마 구분: kitchen,counter,bar',
  `last_connected_at`    DATETIME        NULL                    COMMENT '마지막 WebSocket 접속',
  `last_disconnected_at` DATETIME        NULL                    COMMENT '마지막 끊김',
  `is_active`            TINYINT(1)      NOT NULL DEFAULT 1      COMMENT '0=비활성',
  `wdate`                DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `udate`                DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`client_idx`),
  KEY `idx_shop_active` (`shop_idx`, `is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='영수증 출력 - 매장 PC(클라이언트) 등록';


-- -------------------------------------------------------------
-- 2) print_route_rule_t — 라우팅 규칙 (메뉴 카테고리 → 프린터 타입)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `print_route_rule_t` (
  `rule_idx`               INT             NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `shop_idx`               INT             NOT NULL                COMMENT '논리적 FK: shop_t.idx',
  `category_idx`           INT             NOT NULL                COMMENT '논리적 FK: shop_category_t.idx',
  `printer_type`           ENUM('kitchen','counter','bar') NOT NULL COMMENT '출력 대상 capability',
  `also_print_at_counter`  TINYINT(1)      NOT NULL DEFAULT 1      COMMENT '카운터 동시 출력 여부',
  `wdate`                  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`rule_idx`),
  KEY `idx_shop_category` (`shop_idx`, `category_idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='영수증 출력 - 카테고리별 라우팅 규칙';


-- -------------------------------------------------------------
-- 3) print_job_t — 출력 작업
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `print_job_t` (
  `job_idx`              BIGINT          NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `shop_idx`             INT             NOT NULL                COMMENT '논리적 FK: shop_t.idx',
  `order_idx`            INT             NOT NULL                COMMENT '논리적 FK: orders_t.idx',
  `printer_type`         ENUM('kitchen','counter','bar') NOT NULL,
  `payload`              JSON            NOT NULL                COMMENT '출력 내용 (테이블/메뉴/시각 등)',
  `status`               ENUM('queued','sent','printed','failed') NOT NULL DEFAULT 'queued',
  `attempt_count`        TINYINT         NOT NULL DEFAULT 0,
  `assigned_client_idx`  INT             NULL                    COMMENT '실제 전송 대상 client_idx',
  `last_error`           TEXT            NULL,
  `created_at`           DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sent_at`              DATETIME        NULL                    COMMENT 'Push 전송 시각',
  `printed_at`           DATETIME        NULL                    COMMENT 'ACK 수신 시각',
  PRIMARY KEY (`job_idx`),
  KEY `idx_shop_status_created` (`shop_idx`, `status`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='영수증 출력 - 출력 작업 큐';


-- =============================================================
-- 적용 후 검증 쿼리 (수동 실행)
-- =============================================================
-- SHOW CREATE TABLE print_client_t\G
-- SHOW CREATE TABLE print_route_rule_t\G
-- SHOW CREATE TABLE print_job_t\G
-- SHOW INDEX FROM print_client_t;
-- SHOW INDEX FROM print_route_rule_t;
-- SHOW INDEX FROM print_job_t;
-- SELECT COUNT(*) FROM print_client_t;      -- 0
-- SELECT COUNT(*) FROM print_route_rule_t;  -- 0
-- SELECT COUNT(*) FROM print_job_t;         -- 0
