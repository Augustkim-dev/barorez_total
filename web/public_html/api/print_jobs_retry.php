<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include $_SERVER['DOCUMENT_ROOT']."/cfg/print.inc.php";

header('Content-Type: application/json; charset=utf-8');

// 1) 메서드 검증
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'method_not_allowed'], JSON_UNESCAPED_UNICODE);
    exit;
}

// 2) 세션 검증
$mt_idx   = (int)($_SESSION['mng']['mt_idx']   ?? 0);
$mt_level = (int)($_SESSION['mng']['mt_level'] ?? 0);
if ($mt_idx <= 0) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'unauthenticated'], JSON_UNESCAPED_UNICODE);
    exit;
}

// 3) 파라미터 검증
$job_idx = (int)($_GET['id'] ?? 0);
if ($job_idx <= 0) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'invalid_params'], JSON_UNESCAPED_UNICODE);
    exit;
}

// 4) 원본 job 조회
$DB->where('job_idx', $job_idx);
$job = $DB->getOne('print_job_t');
if (!$job) {
    http_response_code(404);
    echo json_encode(['ok' => false, 'error' => 'not_found'], JSON_UNESCAPED_UNICODE);
    exit;
}

// 5) 권한 검증 — 매장 소유자 OR mt_level >= 8
if ($mt_level < 8) {
    $DB->where('idx', (int)$job['shop_idx']);
    $DB->where('mb_idx', $mt_idx);
    $shop = $DB->getOne('shop_t');
    if (!$shop) {
        http_response_code(403);
        echo json_encode(['ok' => false, 'error' => 'forbidden'], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// 6) 신규 row INSERT (원본 payload 그대로 복사)
$new_job_idx = $DB->insert('print_job_t', [
    'shop_idx'      => (int)$job['shop_idx'],
    'order_idx'     => (int)$job['order_idx'],
    'printer_type'  => $job['printer_type'],
    'payload'       => $job['payload'],
    'status'        => 'queued',
    'attempt_count' => 0,
]);

if (!$new_job_idx) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'insert_failed'], JSON_UNESCAPED_UNICODE);
    exit;
}

// 7) Webhook 발송 (fire-and-forget)
send_print_webhook([(int)$new_job_idx], (int)$job['shop_idx']);

// 8) 응답
echo json_encode(['ok' => true, 'new_job_idx' => (int)$new_job_idx], JSON_UNESCAPED_UNICODE);
