<?php
// 영수증 출력 시스템 — HMAC 서명 유틸
// 사용: PHP ↔ Server C 간 webhook / 콜백 메시지 무결성/인증
// 알고리즘: HMAC-SHA256, 64자 hex 출력

function compute_hmac_signature(string $raw_body): string {
    if (!defined('PRINT_SHARED_SECRET') || PRINT_SHARED_SECRET === '') {
        throw new RuntimeException('PRINT_SHARED_SECRET is not configured');
    }
    return hash_hmac('sha256', $raw_body, PRINT_SHARED_SECRET);
}

function verify_hmac_signature(string $raw_body, ?string $header_signature): bool {
    if ($header_signature === null || $header_signature === '') {
        return false;
    }
    $expected = compute_hmac_signature($raw_body);
    return hash_equals($expected, $header_signature);
}
