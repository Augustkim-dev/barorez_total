<?php
// 영수증 출력 시스템 — print_job 생성 / 페이로드 빌드 / Webhook 발송 헬퍼
// 호출 측은 lib.inc.php 가 먼저 include되어 $DB가 사용 가능한 상태여야 함.

require_once $_SERVER['DOCUMENT_ROOT']."/cfg/hmac.inc.php";

/**
 * 주문 1건 → 라우팅 규칙 적용 → print_job_t 에 N건 INSERT.
 * 반환: 생성된 job_idx 배열. 호출자는 send_print_webhook($jobIds, $shopIdx)을 별도로 호출.
 */
function create_print_jobs_for_order(int $order_idx): array {
    global $DB;

    // 1) order 조회
    $DB->where('idx', $order_idx);
    $order = $DB->getOne('orders_t');
    if (!$order) return [];

    $shop_idx = (int)$order['sh_idx'];   // orders_t는 sh_idx (기존 컨벤션)

    // 2) 매장 활성화 확인 (Phase 1 D2 게이트)
    $DB->where('idx', $shop_idx);
    $shop = $DB->getOne('shop_t', null, 'print_enabled');
    if (!$shop || !(int)$shop['print_enabled']) return [];

    $snapshot = json_decode($order['ct_snapshot'] ?? '{}', true) ?: [];
    $items    = $snapshot['items'] ?? [];
    if (empty($items)) return [];

    // 2) 메뉴 → 카테고리 매핑 (한 번에 조회)
    $sm_ids = array_unique(array_map(fn($i) => (int)$i['sm_id'], $items));
    if (empty($sm_ids)) return [];
    $rows = $DB->where('idx', $sm_ids, 'IN')->get('shop_menu_t', null, 'idx, sc_idx');
    $menu_to_cat = [];
    foreach ($rows as $r) $menu_to_cat[(int)$r['idx']] = (int)$r['sc_idx'];

    // 3) 라우팅 규칙 조회 (해당 매장)
    $DB->where('shop_idx', $shop_idx);
    $rules = $DB->get('print_route_rule_t');
    $cat_to_rule = [];
    foreach ($rules as $rule) {
        $cat_to_rule[(int)$rule['category_idx']][] = $rule;
    }

    // 4) printer_type 별 그룹화
    $printer_to_items = [];
    $print_at_counter = false;
    foreach ($items as $item) {
        $sm_id = (int)$item['sm_id'];
        $sc_id = $menu_to_cat[$sm_id] ?? 0;
        $rs = $cat_to_rule[$sc_id] ?? [];
        if (empty($rs)) {
            $printer_to_items['counter'][] = $item;
            continue;
        }
        foreach ($rs as $rule) {
            $printer_to_items[$rule['printer_type']][] = $item;
            if ((int)$rule['also_print_at_counter'] === 1) $print_at_counter = true;
        }
    }
    if ($print_at_counter && !isset($printer_to_items['counter'])) {
        $printer_to_items['counter'] = $items;
    }

    // 5) printer_type 별로 print_job_t INSERT
    $job_idxs = [];
    foreach ($printer_to_items as $printer_type => $printer_items) {
        $payload = build_print_payload($order, $printer_type, $printer_items);
        $new_id = $DB->insert('print_job_t', [
            'shop_idx'      => $shop_idx,
            'order_idx'     => $order_idx,
            'printer_type'  => $printer_type,
            'payload'       => json_encode($payload, JSON_UNESCAPED_UNICODE),
            'status'        => 'queued',
            'attempt_count' => 0,
        ]);
        if ($new_id) $job_idxs[] = (int)$new_id;
    }
    return $job_idxs;
}

/**
 * PRD §7.2 형식 페이로드. 개인정보 제외 (PRD §11.4).
 */
function build_print_payload(array $order, string $printer_type, array $items): array {
    return [
        'printer_type' => $printer_type,
        'table_name'   => $order['ot_table'] ?? null,
        'order_time'   => $order['ot_wdate'] ?? date('Y-m-d H:i:s'),
        'items'        => array_map(function($i) {
            return [
                'name'    => $i['menu_name'] ?? '',
                'qty'     => (int)($i['quantity'] ?? 1),
                'options' => $i['options'] ?? [],
            ];
        }, $items),
        'memo'         => $order['ot_notes'] ?? '',
    ];
}

/**
 * Server C로 Webhook 발송. fire-and-forget — 실패해도 throw 안 함.
 *
 * 본문 형식 (Phase 2 D007 에서 보강 — Server C 가 별도 GET 안 해도 되도록 jobs 상세 동봉):
 *   {
 *     "shop_id": 42,
 *     "jobs": [
 *       { "job_id": 12345, "printer_type": "kitchen", "payload": { ... } },
 *       ...
 *     ]
 *   }
 */
function send_print_webhook(array $job_ids, int $shop_id): void {
    global $DB;
    if (empty($job_ids)) return;
    if (!defined('PRINT_SERVER_URL') || PRINT_SERVER_URL === '') return;

    // print_job_t 에서 printer_type / payload 페치
    $rows = $DB->where('job_idx', $job_ids, 'IN')
               ->get('print_job_t', null, 'job_idx, printer_type, payload');
    if (empty($rows)) {
        error_log('[print] webhook skip: jobs not found ids=' . implode(',', $job_ids));
        return;
    }

    $jobs = [];
    foreach ($rows as $r) {
        $jobs[] = [
            'job_id'       => (int)$r['job_idx'],
            'printer_type' => $r['printer_type'],
            'payload'      => json_decode($r['payload'] ?? '{}', true) ?: new \stdClass(),
        ];
    }

    $body = json_encode(['shop_id' => $shop_id, 'jobs' => $jobs], JSON_UNESCAPED_UNICODE);
    $sig  = compute_hmac_signature($body);
    $url  = rtrim(PRINT_SERVER_URL, '/') . '/webhook/print';
    $hdrname = defined('PRINT_HMAC_HEADER_NAME') ? PRINT_HMAC_HEADER_NAME : 'X-Signature';

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST              => true,
        CURLOPT_POSTFIELDS        => $body,
        CURLOPT_HTTPHEADER        => ['Content-Type: application/json', "$hdrname: $sig"],
        CURLOPT_RETURNTRANSFER    => true,
        CURLOPT_TIMEOUT_MS        => 500,
        CURLOPT_CONNECTTIMEOUT_MS => 300,
    ]);
    $resp = curl_exec($ch);
    if ($resp === false) {
        error_log('[print] webhook send failed: ' . curl_error($ch) . ' jobs=' . implode(',', $job_ids));
    }
    curl_close($ch);
}

/**
 * D2 hook용 안전 wrapper.
 * 어떤 예외도 호출자(트랜잭션)로 전파되지 않음.
 * 매장 비활성 / 규칙 없음 / DB 오류 / Webhook 실패 모두 무해.
 */
function try_emit_print_jobs(int $order_idx, int $shop_idx): void {
    try {
        $job_ids = create_print_jobs_for_order($order_idx);
        if (!empty($job_ids)) {
            send_print_webhook($job_ids, $shop_idx);
        }
    } catch (\Throwable $e) {
        error_log('[print] try_emit_print_jobs failed order=' . $order_idx
                . ' shop=' . $shop_idx
                . ' err=' . $e->getMessage());
    }
}
