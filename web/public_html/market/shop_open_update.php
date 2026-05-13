<?php
// shop_open_update.php : 매장 영업 상태 토글 업데이트 (JSON)

include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";

header('Content-Type: application/json; charset=utf-8');

$act = $_POST['act'] ?? '';

try {
    if (!isset($_SESSION['mng'])) {
        echo json_encode(['success' => false, 'message' => '로그인이 필요합니다.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($act !== 'set_open') {
        echo json_encode(['success' => false, 'message' => '잘못된 요청입니다.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $mb_idx  = (int)($_SESSION['mng']['mt_idx'] ?? 0);
    $sh_idx  = (int)($_POST['sh_idx'] ?? 0);
    $sh_open = ($_POST['sh_open'] ?? '') === 'Y' ? 'Y' : 'N';

    if (!$mb_idx || !$sh_idx) {
        echo json_encode(['success' => false, 'message' => '필수 값이 누락되었습니다.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ✅ 본인 매장인지 체크 (권한 체크)
    $DB->where('idx', $sh_idx);
    $DB->where('mb_idx', $mb_idx);
    $shop = $DB->getOne('shop_t', 'idx');

    if (!$shop) {
        echo json_encode(['success' => false, 'message' => '권한이 없거나 매장을 찾을 수 없습니다.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ✅ TODO: 실제 영업상태 컬럼명으로 변경 필요
    // 예: 'sh_open' / 'sh_is_open' / 'sh_open_yn' 등
    $updateData = [
        'sh_show'  => $sh_open,
        'sh_udate' => $DB->now(),
    ];

    $DB->where('idx', $sh_idx);
    $ok = $DB->update('shop_t', $updateData);

    if (!$ok) {
        throw new Exception('상태 저장에 실패했습니다.');
    }

    echo json_encode([
        'success' => true,
        'message' => ($sh_open === 'Y') ? '영업중으로 변경되었습니다.' : '문닫음으로 변경되었습니다.'
    ], JSON_UNESCAPED_UNICODE);
    exit;

} catch (Exception $e) {
    error_log("shop_open_update Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
    exit;
}
