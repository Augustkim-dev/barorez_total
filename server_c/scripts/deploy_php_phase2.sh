#!/usr/bin/env bash
# =============================================================
# Phase 2 D007~D010 PHP 측 변경사항 운영 동기화 스크립트
# =============================================================
# 사용 (운영 서버, root):
#   cd /home/barorez_total && git pull && bash server_c/scripts/deploy_php_phase2.sh
#
# 멱등(idempotent): 여러 번 실행해도 안전. 이미 적용된 부분은 skip.
# =============================================================

set -euo pipefail

# 색상
RED=$'\e[31m'; GREEN=$'\e[32m'; YELLOW=$'\e[33m'; BLUE=$'\e[34m'; RESET=$'\e[0m'
ok()    { echo "${GREEN}✓${RESET} $*"; }
info()  { echo "${BLUE}→${RESET} $*"; }
warn()  { echo "${YELLOW}!${RESET} $*"; }
fail()  { echo "${RED}✗${RESET} $*" >&2; exit 1; }

# 경로
SRC_ROOT="/home/barorez_total/web/public_html"
DST_ROOT="/home/baro/public_html"
TS=$(date +%Y%m%d_%H%M%S)

# 사전 점검
[[ -d "$SRC_ROOT" ]] || fail "소스 디렉토리 없음: $SRC_ROOT (git clone 안 됨?)"
[[ -d "$DST_ROOT" ]] || fail "대상 디렉토리 없음: $DST_ROOT"
[[ -f "$DST_ROOT/cfg/config.inc.php" ]] || fail "운영 config.inc.php 없음"
[[ -f "$SRC_ROOT/cfg/print_client.inc.php" ]] || fail "git 측 print_client.inc.php 없음 (git pull 필요?)"

# 운영 PHP 파일의 소유자 / 권한 자동 감지
PHP_OWNER=$(stat -c '%U:%G' "$DST_ROOT/cfg/config.inc.php")
PHP_PERM=$(stat -c '%a' "$DST_ROOT/cfg/config.inc.php")
info "운영 PHP 파일 owner/perm 감지: $PHP_OWNER / $PHP_PERM"

# 1. 백업
echo
info "[1/5] 백업 (suffix .bak.$TS)"
cp -p "$DST_ROOT/cfg/print.inc.php"   "$DST_ROOT/cfg/print.inc.php.bak.$TS"
cp -p "$DST_ROOT/cfg/config.inc.php"  "$DST_ROOT/cfg/config.inc.php.bak.$TS"
ok "백업 완료"

# 2. D007 변경: cfg/print.inc.php (webhook body 형식)
echo
info "[2/5] D007 cfg/print.inc.php 교체"
cp "$SRC_ROOT/cfg/print.inc.php" "$DST_ROOT/cfg/print.inc.php"
ok "교체 완료"

# 3. D008 신규 파일 3개
echo
info "[3/5] D008 신규 파일 복사 (3개)"
for f in cfg/print_client.inc.php api/print_clients_register.php api/print_clients_verify.php; do
  if [[ -f "$DST_ROOT/$f" ]]; then
    cp -p "$DST_ROOT/$f" "$DST_ROOT/$f.bak.$TS"
    warn "  $f 이미 존재 → 백업 후 덮어쓰기"
  fi
  cp "$SRC_ROOT/$f" "$DST_ROOT/$f"
  ok "  $f"
done

# 4. PRINT_CLIENT_TOKEN_SALT 추가 (이미 있으면 skip)
echo
info "[4/5] PRINT_CLIENT_TOKEN_SALT 점검"
if grep -q "PRINT_CLIENT_TOKEN_SALT" "$DST_ROOT/cfg/config.inc.php"; then
  warn "PRINT_CLIENT_TOKEN_SALT 이미 정의되어 있음 — skip (값 덮어쓰지 않음)"
else
  SALT=$(openssl rand -hex 32)
  {
    echo ""
    echo "// Phase 2 D008 — client 인증 토큰 해싱 salt (회전 시 모든 매장 PC 토큰 재발급 필요)"
    echo "define('PRINT_CLIENT_TOKEN_SALT', '$SALT');"
  } >> "$DST_ROOT/cfg/config.inc.php"
  ok "PRINT_CLIENT_TOKEN_SALT 신규 추가"
  echo "${YELLOW}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${RESET}"
  echo "${YELLOW}메모해 두세요 (회전 시 필요):${RESET}"
  echo "${YELLOW}SALT=$SALT${RESET}"
  echo "${YELLOW}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${RESET}"
fi

# 5. 권한 정렬 + syntax 검증
echo
info "[5/5] 권한 정렬 + syntax 검증"
NEW_FILES=(
  "$DST_ROOT/cfg/print.inc.php"
  "$DST_ROOT/cfg/print_client.inc.php"
  "$DST_ROOT/api/print_clients_register.php"
  "$DST_ROOT/api/print_clients_verify.php"
)
for f in "${NEW_FILES[@]}"; do
  chown "$PHP_OWNER" "$f"
  chmod "$PHP_PERM" "$f"
done
ok "권한 정렬 완료 ($PHP_OWNER, $PHP_PERM)"

ALL_OK=1
for f in "${NEW_FILES[@]}" "$DST_ROOT/cfg/config.inc.php"; do
  if ! out=$(php -l "$f" 2>&1); then
    echo "${RED}✗ syntax 오류: $f${RESET}"
    echo "$out"
    ALL_OK=0
  fi
done
if [[ $ALL_OK -eq 1 ]]; then
  ok "5개 파일 모두 syntax OK"
else
  fail "syntax 오류 발생 — 백업본으로 복구 권장"
fi

# 결과 요약
echo
echo "${GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${RESET}"
echo "${GREEN}Phase 2 PHP 동기화 완료${RESET}"
echo "${GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${RESET}"
echo
echo "다음 단계 — end-to-end 검증:"
echo "  1) PM2 로그 tailing:  pm2 logs barorez-server-c --lines 0"
echo "  2) 활성 매장(sh_idx=48) 에서 실제 주문 1건 발생"
echo "  3) Server C 로그에 'webhook_received' 출력 확인"
echo "  4) DB 확인:  SELECT * FROM print_job_t ORDER BY job_idx DESC LIMIT 5;"
echo
echo "백업본 위치 (롤백 시):"
ls -la "$DST_ROOT"/cfg/*.bak."$TS" "$DST_ROOT"/api/*.bak."$TS" 2>/dev/null || true
