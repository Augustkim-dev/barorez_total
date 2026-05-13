<?php
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/visit.inc.php"; // 방문 세션 로직

$_ENABLED_INC_TOP    = true;
$_ENABLED_INC_QUICK  = true;
$_ENABLED_INC_FOOTER = true;
$_ENABLED_INC_MODAL  = true;

include_once $_SERVER['DOCUMENT_ROOT']."/app/inc/head.php";
$hd_num = 1;
include_once $_SERVER['DOCUMENT_ROOT']."/app/inc/header.php";

// 주문 모드 초기화 (다른 페이지에서 남아있을 수 있으므로)
unset($_SESSION['order_mode']);

if($_GET['app_os']){
    $_SESSION['app_token'] = $_GET['app_token'];
    $_SESSION['app_os'] = $_GET['app_os'];
    $_SESSION['app_lat'] = $_GET['lat'];
    $_SESSION['app_lng'] = $_GET['lng'];
}

if( $_SESSION['app_token']){
    $DB->where('mt_app_token', $_SESSION['app_token']);
    $new_member = $DB->getOne('member_t');

    if($new_member) {
        $_SESSION['mng'] = [
            'mt_idx' => $new_member['idx'],
            'mt_id' => $new_member['mt_id'],
            'mt_sns_id' => $new_member['mt_sns_id'],
            'mt_email' => $new_member['mt_email'],
            'mt_hp' => $new_member['mt_hp'],
            'mt_name' => $new_member['mt_name'],
            'mt_nickname' => $new_member['mt_nickname'],
            'mt_type' => $new_member['mt_type'],
            'mt_level' => $new_member['mt_level'],
            'mt_grade' => $new_member['mt_grade'],
            'mt_language' => $new_member['mt_language'],
        ];
    }
}

if (!empty($_GET['app_reset_qr'])) {
    unset($_SESSION['qr_token']);
}
/* =============================================
   1) QR 토큰(tk) 추출 및 세션 저장
   - URL: https://qrorder.epicque.com?tk=...
   ============================================= */
if (empty($_SESSION['qr_token'])) {
    $tk = trim((string)($_GET['tk'] ?? ''));

    // 하위호환: 예전 링크(table_id)가 들어오면 안내 후 종료(정책에 맞게 수정 가능)
    if ($tk === '' && !empty($_GET['table_id'])) {
        close_page_with_alert('이 QR은 구버전입니다. 새 QR로 다시 스캔해 주세요.');
    }

    if ($tk === '') {
        // tk가 없으면 지도/목록 페이지로
        echo '<script>location.replace("' . MAP_PAGE . '");</script>';
        exit;
    }

    $_SESSION['qr_token'] = $tk;
}

$qrToken = (string)$_SESSION['qr_token'];

/* =============================================
   2) tk로 매장/테이블 정보 조회
   - shop_table_qr_t.qr_token 으로 조회
   - shop_table_t 조인하여 tb_no/use_yn 검증
   ============================================= */
$resolved = resolve_qr_token($DB, $qrToken);

if (!$resolved) {
    unset($_SESSION['qr_token']);
    close_page_with_alert('유효하지 않은 QR 코드입니다.');
}

$shopId   = (int)$resolved['sh_idx'];
$tableNum = (string)$resolved['tb_no']; // 방문/주문에서 사용하는 테이블번호
$tb_idx   = (int)$resolved['tb_idx'];  // (선택) 추적/관리용

if ($shopId <= 0 || $tableNum === '') {
    unset($_SESSION['qr_token']);
    close_page_with_alert('잘못된 매장 또는 테이블 정보입니다.');
}

/* =============================================
   3) 세션에 값 저장 (기존 흐름 유지)
   ============================================= */
$_SESSION['current_sh_idx'] = $shopId;     // 매장 ID
$_SESSION['table_no']       = $tableNum;   // 테이블 번호(tb_no)
$_SESSION['tb_idx']         = $tb_idx;     // (선택)
$_SESSION['is_qr_order']    = true;        // QR 주문 모드 플래그

/* =============================================
   4) 방문 세션 보장 (재스캔 시에도 동일 방문 유지)
   ============================================= */
$mt_idx = (int)($_SESSION['mng']['mt_idx'] ?? 0);
$visit  = ensure_visit($DB, $shopId, $tableNum, $mt_idx);
// 이제 $_SESSION['tv_idx'] 로 주문이 연결됨

/* =============================================
   5) 매장 정보 조회 (존재 여부 + 삭제 여부 확인)
   ============================================= */
$DB->where('idx', $shopId);
$DB->where('del_date', null, 'IS');
$row = $DB->getOne('shop_t');

if (!$row) {
    unset($_SESSION['qr_token'], $_SESSION['current_sh_idx'], $_SESSION['table_no'], $_SESSION['tb_idx'], $_SESSION['is_qr_order']);
    close_page_with_alert('존재하지 않거나 삭제된 매장입니다.');
}

// 매장 이미지 (프로젝트 규칙에 맞게 유지)
$shopImg = '/data/shop/' . $shopId . '/rs_' . ($row['sh_img1'] ?? '');

/* =============================================
   6) 뷰 포함
   ============================================= */

$view_path = VIEWS_SHOP_PATH . "/list.php";
if (file_exists($view_path)) {
    include_once $view_path;
} else {
    die('페이지 로드 중 오류가 발생했습니다.');
}

include_once $_SERVER['DOCUMENT_ROOT'] . "/app/inc/tail.php";

/* =========================================================
   Functions
   ========================================================= */

/**
 * tk(랜덤 토큰) -> (sh_idx, tb_idx, tb_no) 해석
 * - shop_table_qr_t(q) + shop_table_t(st) 조인
 * - st.use_yn='Y' 인 테이블만 허용(soft delete 차단)
 */
function resolve_qr_token($DB, string $token): ?array
{
    $token = trim($token);

    // ✅ 토큰 형식(64 hex) 검증
    if (!preg_match('/^[a-f0-9]{64}$/i', $token)) {
        return null;
    }

    $DB->join('shop_table_t st', 'st.idx = q.tb_idx', 'INNER');

    $DB->where('q.qr_token', $token);
    $DB->where('st.use_yn', 'Y');

    // qr 테이블 sh_idx와 실제 테이블 sh_idx가 일치하는지까지 확인(데이터 이상 방지)
    $row = $DB->getOne('shop_table_qr_t q', 'q.sh_idx, q.tb_idx, st.idx as idx, st.tb_no, st.tb_name, st.sh_idx as st_sh_idx');

    if (!$row) return null;

    if ((int)$row['sh_idx'] !== (int)$row['st_sh_idx']) {
        return null;
    }

    // tb_no는 주문/방문에서 쓰는 테이블 번호이므로 필수
    $tb_no = (string)($row['idx'] ?? '');
    if ($tb_no === '') return null;

    return [
        'sh_idx'  => (int)$row['sh_idx'],
        'tb_idx'  => (int)$row['tb_idx'],
        'tb_no'   => $tb_no,
        'tb_name' => (string)($row['tb_name'] ?? ''),
    ];
}

/**
 * alert 메시지 표시 후 현재 탭/창을 강제 종료
 * 비정상 QR 접근, 위조된 tk 등 보안 상황에 사용
 */
function close_page_with_alert(string $message)
{
    $safeMessage = addslashes($message);

    echo "<script>
            alert('" . $safeMessage . "');

            // 1) 최대한 탭 닫기 시도
            window.close();

            // 2) 닫히지 않을 경우 대비 안내 화면 표시
            document.body.innerHTML = `
                <div style=\"
                    position: fixed;
                    top: 0; left: 0; right: 0; bottom: 0;
                    background: #f8f9fa;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    flex-direction: column;
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                    color: #333;
                    text-align: center;
                    padding: 20px;
                    box-sizing: border-box;
                    z-index: 9999;
                \">
                    <h2 style=\"margin-bottom: 16px; color: #dc3545;\">잘못된 접근입니다</h2>
                    <p style=\"font-size: 16px; line-height: 1.5; max-width: 300px;\">
                        정상적인 테이블 QR코드를 스캔해 주세요.<br><br>
                        이 탭을 닫아주세요.
                    </p>
                </div>
            `;

            // 뒤로가기 버튼 눌러도 벗어날 수 없게 방지
            history.pushState(null, null, location.href);
            window.onpopstate = function () {
                history.go(1);
            };

            // opener가 있으면 부모창 포커스
            if (window.opener) {
                window.opener.focus();
            }
          </script>";
    exit;
}
?>
