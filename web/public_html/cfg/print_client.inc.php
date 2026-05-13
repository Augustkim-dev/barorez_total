<?php
// 영수증 출력 시스템 — Client 인증 헬퍼 (Phase 2 D008)
// 토큰 발급/해싱/검증. 호출 측은 cfg/config.inc.php 가 먼저 include되어
// PRINT_CLIENT_TOKEN_SALT 가 정의된 상태여야 함.

/**
 * 64자 hex 평문 토큰을 발급한다. 발급 후 1회만 반환되며 DB에는 해시만 저장한다.
 */
function generate_client_token(): string {
    return bin2hex(random_bytes(32));
}

/**
 * 평문 토큰을 SHA-256 + global salt 로 해시한다.
 * 결과: 64자 hex (DB 컬럼 VARCHAR(128) 허용 범위).
 */
function hash_client_token(string $plain_token): string {
    if (!defined('PRINT_CLIENT_TOKEN_SALT') || PRINT_CLIENT_TOKEN_SALT === '') {
        throw new RuntimeException('PRINT_CLIENT_TOKEN_SALT is not configured');
    }
    return hash('sha256', $plain_token . PRINT_CLIENT_TOKEN_SALT);
}

/**
 * client_idx + plain_token 으로 print_client_t 의 활성 row 와 매치되는지 확인.
 * 반환: 매치된 row 또는 null. row 에는 shop_idx, capabilities, is_active 포함.
 */
function verify_client_token(int $client_idx, string $plain_token): ?array {
    global $DB;
    if ($client_idx <= 0 || $plain_token === '') return null;

    $expected_hash = hash_client_token($plain_token);

    $DB->where('client_idx', $client_idx);
    $DB->where('is_active', 1);
    $row = $DB->getOne('print_client_t', null,
        'client_idx, shop_idx, client_name, capabilities, auth_token_hash, is_active');
    if (!$row) return null;

    // timing-safe 비교
    if (!hash_equals($row['auth_token_hash'], $expected_hash)) return null;

    return [
        'client_idx'   => (int)$row['client_idx'],
        'shop_idx'     => (int)$row['shop_idx'],
        'client_name'  => $row['client_name'],
        'capabilities' => array_values(array_filter(
            array_map('trim', explode(',', $row['capabilities'] ?? '')),
        )),
    ];
}

/**
 * print_client_t 에 새 client 를 INSERT 한다. 평문 토큰을 1회 반환.
 * 호출 측은 평문 토큰을 매장에 전달한 뒤 메모리에서 즉시 폐기해야 한다.
 *
 * @param int    $shop_idx     shop_t.idx
 * @param string $client_name  '카운터 PC' 등
 * @param array  $capabilities ['kitchen', 'counter'] 등
 * @return array { client_idx: int, plain_token: string }
 */
function register_print_client(int $shop_idx, string $client_name, array $capabilities): array {
    global $DB;

    $allowed = ['kitchen', 'counter', 'bar'];
    $caps = array_values(array_intersect($allowed, array_map('strtolower', $capabilities)));
    if (empty($caps)) {
        throw new InvalidArgumentException('capabilities must contain at least one of: kitchen, counter, bar');
    }

    $plain_token = generate_client_token();
    $token_hash  = hash_client_token($plain_token);

    $new_id = $DB->insert('print_client_t', [
        'shop_idx'        => $shop_idx,
        'client_name'     => $client_name,
        'auth_token_hash' => $token_hash,
        'capabilities'    => implode(',', $caps),
        'is_active'       => 1,
    ]);
    if (!$new_id) {
        throw new RuntimeException('print_client_t INSERT failed: ' . $DB->getLastError());
    }

    return [
        'client_idx'  => (int)$new_id,
        'plain_token' => $plain_token,
    ];
}
