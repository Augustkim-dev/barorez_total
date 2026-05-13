-- =============================================================
-- 영수증 자동 출력 시스템 — Phase 1 D2
-- shop_t에 print_enabled 컬럼 추가 (매장별 명시 토글)
-- 작성일: 2026-05-13
-- PRD: docs/barorez_print_system_PRD.md, plan: D2
--
-- 정책:
--   - 기본값 0 (비활성) — 어떤 매장도 자동으로 켜지지 않음
--   - D002의 "기존 테이블 변경 0건" 원칙에서 의도적 예외
--     (운영 편의성·명시적 토글이 더 중요한 판단)
--   - InnoDB Online DDL 사용 → 락 시간 짧음
--
-- 적용 전 점검:
--   1) DB 백업 완료 확인
--   2) SHOW COLUMNS FROM shop_t LIKE 'print_enabled';  -- 없어야 함
--   3) 트래픽 적은 시간 권장
--
-- 롤백: 2026_05_print_system_phase1_d2_rollback.sql
-- =============================================================

ALTER TABLE `shop_t`
  ADD COLUMN `print_enabled` TINYINT(1) NOT NULL DEFAULT 0
  COMMENT '영수증 자동 출력 활성 여부 (0=비활성/1=활성)';


-- =============================================================
-- 적용 후 검증
-- =============================================================
-- SHOW COLUMNS FROM shop_t LIKE 'print_enabled';
-- SELECT COUNT(*) FROM shop_t;                          -- 전체
-- SELECT COUNT(*) FROM shop_t WHERE print_enabled=1;    -- 0 (모두 비활성)
-- SELECT COUNT(*) FROM shop_t WHERE print_enabled=0;    -- 전체 = 모두 비활성
