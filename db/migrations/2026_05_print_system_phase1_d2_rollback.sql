-- =============================================================
-- Phase 1 D2 롤백 — shop_t에서 print_enabled 컬럼 제거
-- 작성일: 2026-05-13
--
-- 주의:
--   - 컬럼만 제거. 기존 컬럼·데이터·인덱스 영향 없음
--   - 단 D2 hook이 활성 상태(주문 처리 중)에서 롤백하면
--     create_print_jobs_for_order()의 게이트 조회가 실패 → 빈 배열 반환 → 안전
--   - 그래도 권장 흐름: 코드 hook 비활성화 → 컬럼 제거
-- =============================================================

ALTER TABLE `shop_t` DROP COLUMN `print_enabled`;

-- 검증:
-- SHOW COLUMNS FROM shop_t LIKE 'print_enabled';  -- 결과 없음
