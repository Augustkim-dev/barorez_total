<?php
// 매장 PC(client) 등록 + 평문 토큰 1회 발급 API (Phase 2 D008)
//
// 권한: 매장 소유자(shop_t.mb_idx == mt_idx) 또는 mt_level >= 8 (관리자)
// 본 API 는 Phase 4 Admin UI 가 도착하기 전까지의 임시 운영용. Phase 4 에서 동일 함수 재사용.
// 응답에 plain_token 이 포함되므로 호출자(브라우저) 는 즉시 사용자에게 1회 노출 후 폐기해야 함.

include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include $_SERVER['DOCUMENT_ROOT']."/cfg/print_client.inc.php";

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'method_not_allowed'], JSON_UNESCAPED_UNICODE);
    exit;
}

$mt_idx   = (int)($_SESSION['mng']['mt_idx']   ?? 0);
$mt_level = (int)($_SESSION['mng']['mt_level'] ?? 0);
if ($mt_idx <= 0) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'unauthenticated'], JSON_UNESCAPED_UNICODE);
    exit;
}

$body = json_decode(file_get_contents('php://input'), true) ?: $_POST;
$shop_idx     = (int)($body['shop_idx'] ?? 0);
$client_name  = trim((string)($body['client_name'] ?? ''));
$capabilities = $body['capabilities'] ?? [];
if (is_string($capabilities)) {
    $capabilities = array_filter(array_map('trim', explode(',', $capabilities)));
}

if ($shop_idx <= 0 || $client_name === '' || !is_array($capabilities) || empty($capabilities)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'invalid_params'], JSON_UNESCAPED_UNICODE);
    exit;
}

// 권한 검증
if ($mt_level < 8) {
    $DB->where('idx', $shop_idx);
    $DB->where('mb_idx', $mt_idx);
    $shop = $DB->getOne('shop_t');
    if (!$shop) {
        http_response_code(403);
        echo json_encode(['ok' => false, 'error' => 'forbidden'], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

try {
    $result = register_print_client($shop_idx, $client_name, $capabilities);
} catch (\InvalidArgumentException $e) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
    exit;
} catch (\Throwable $e) {
    error_log('[print_clients_register] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'internal'], JSON_UNESCAPED_UNICODE);
    exit;
}

echo json_encode([
    'ok'          => true,
    'client_idx'  => $result['client_idx'],
    'plain_token' => $result['plain_token'],
    'warning'     => '이 토큰은 다시 표시되지 않습니다. 매장 PC 에 입력 후 즉시 폐기하세요.',
], JSON_UNESCAPED_UNICODE);
