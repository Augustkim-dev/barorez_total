-- =============================================================
-- 영수증 자동 출력 시스템 — Phase 1 D1 롤백 SQL
-- 작성일: 2026-05-12
--
-- 적용: 2026_05_print_system.sql 의 역순으로 DROP
-- 데이터 손실 경고:
--   - 본 롤백을 실행하면 print_client_t / print_route_rule_t / print_job_t
--     의 모든 데이터가 영구 삭제됩니다.
--   - 운영 데이터가 쌓인 후 롤백할 경우 반드시 사전 백업 필수.
-- =============================================================

DROP TABLE IF EXISTS `print_job_t`;
DROP TABLE IF EXISTS `print_route_rule_t`;
DROP TABLE IF EXISTS `print_client_t`;

-- 검증:
-- SHOW TABLES LIKE 'print\_%';  -- 결과 없음
