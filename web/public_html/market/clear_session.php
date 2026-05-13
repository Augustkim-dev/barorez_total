<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";

header('Content-Type: application/json; charset=utf-8');

try {

    $_SESSION = [];
    session_destroy();
    
    // TODO: 실제 SMS 발송 연동
    echo json_encode([
        'success' => true,
        'message' => '세션 초기화',
    ], JSON_UNESCAPED_UNICODE);
    exit;

} catch (Exception $e) {
    error_log("send_hp_code Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '서버 오류가 발생했습니다.'], JSON_UNESCAPED_UNICODE);
    exit;
}
