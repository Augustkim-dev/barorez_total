<?php
// /market/ajax/set_current_shop.php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";

header('Content-Type: application/json; charset=utf-8');

try {
    $mb_idx = $_SESSION['mng']['mt_idx'] ?? 0;
    if (!$mb_idx) {
        echo json_encode([
            'success' => false,
            'message' => '로그인 정보가 없습니다.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $sh_idx = (int)($_POST['sh_idx'] ?? 0);
    if ($sh_idx <= 0) {
        echo json_encode([
            'success' => false,
            'message' => '유효한 매장을 선택해 주세요.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // 이 회원의 매장인지 검증
    $DB->where('idx', $sh_idx);
    $DB->where('mb_idx', $mb_idx);
    $DB->where('del_date', null, 'IS');
    $shop = $DB->getOne('shop_t', 'idx');

    if (!$shop) {
        echo json_encode([
            'success' => false,
            'message' => '선택한 매장을 찾을 수 없습니다.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // 세션에 현재 매장 저장
    $_SESSION['current_sh_idx'] = $sh_idx;

    echo json_encode([
        'success' => true,
        'message' => '매장이 선택되었습니다.'
    ], JSON_UNESCAPED_UNICODE);
    exit;

} catch (Exception $e) {
    // 혹시 모를 예외용
    echo json_encode([
        'success' => false,
        'message' => '예외가 발생했습니다: '.$e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
