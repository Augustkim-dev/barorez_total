<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include $_SERVER['DOCUMENT_ROOT']."/cfg/hmac.inc.php";

header('Content-Type: application/json; charset=utf-8');

// 1) 메서드 검증
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'method_not_allowed'], JSON_UNESCAPED_UNICODE);
    exit;
}

// 2) raw body + HMAC 검증
$raw = file_get_contents('php://input');
if ($raw === false) $raw = '';
$sig = $_SERVER['HTTP_X_SIGNATURE'] ?? null;
if (!verify_hmac_signature($raw, $sig)) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'invalid_signature'], JSON_UNESCAPED_UNICODE);
    exit;
}

// 3) 본문 파싱
$body = json_decode($raw, true);
if (!is_array($body)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'invalid_json'], JSON_UNESCAPED_UNICODE);
    exit;
}

// 4) 파라미터 검증
$job_idx = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$status  = $body['status']     ?? null;
$printed = $body['printed_at'] ?? null;  // ISO8601 또는 NULL
$client  = isset($body['client_idx']) ? (int)$body['client_idx'] : null;
$err     = $body['last_error'] ?? null;

$allowed_status = ['queued', 'sent', 'printed', 'failed'];
if ($job_idx <= 0 || !in_array($status, $allowed_status, true)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'invalid_params'], JSON_UNESCAPED_UNICODE);
    exit;
}

// 5) DB UPDATE
$update = [
    'status'     => $status,
    'last_error' => $err,
];
if ($status === 'sent')    $update['sent_at']    = $DB->now();
if ($status === 'printed') $update['printed_at'] = $printed ?: $DB->now();
if ($client !== null)      $update['assigned_client_idx'] = $client;

$DB->where('job_idx', $job_idx);
$DB->update('print_job_t', $update);

// 6) 응답
echo json_encode(['ok' => true, 'job_idx' => $job_idx], JSON_UNESCAPED_UNICODE);
