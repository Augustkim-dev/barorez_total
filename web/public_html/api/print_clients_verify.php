<?php
// Server C 가 호출하는 client 토큰 검증 API (Phase 2 D008)
// HMAC 서명으로 인증, 본문에 client_idx + 평문 token.

include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include $_SERVER['DOCUMENT_ROOT']."/cfg/hmac.inc.php";
include $_SERVER['DOCUMENT_ROOT']."/cfg/print_client.inc.php";

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'method_not_allowed'], JSON_UNESCAPED_UNICODE);
    exit;
}

$raw = file_get_contents('php://input');
if ($raw === false) $raw = '';
$sig = $_SERVER['HTTP_X_SIGNATURE'] ?? null;
if (!verify_hmac_signature($raw, $sig)) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'invalid_signature'], JSON_UNESCAPED_UNICODE);
    exit;
}

$body = json_decode($raw, true);
if (!is_array($body)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'invalid_json'], JSON_UNESCAPED_UNICODE);
    exit;
}

$client_idx = isset($body['client_idx']) ? (int)$body['client_idx'] : 0;
$token      = (string)($body['token'] ?? '');

if ($client_idx <= 0 || $token === '') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'invalid_params'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $result = verify_client_token($client_idx, $token);
} catch (\Throwable $e) {
    error_log('[print_clients_verify] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'internal'], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($result === null) {
    // 200 + valid:false — 401 안 줌 (Server C 측 캐시 무효 처리에 명시적 응답이 더 단순)
    echo json_encode(['ok' => true, 'valid' => false], JSON_UNESCAPED_UNICODE);
    exit;
}

echo json_encode([
    'ok'           => true,
    'valid'        => true,
    'client_idx'   => $result['client_idx'],
    'shop_idx'     => $result['shop_idx'],
    'client_name'  => $result['client_name'],
    'capabilities' => $result['capabilities'],
], JSON_UNESCAPED_UNICODE);
