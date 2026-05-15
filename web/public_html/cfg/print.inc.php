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
 *
 * items[].unit_price 와 summary.total 은 영수증 금액 표시 용도.
 * orders_t.ct_snapshot 의 unit_price 와 summary 를 그대로 전달.
 * 클라이언트 (Python formatter) 는 가격 필드가 있을 때만 가격 컬럼·
 * 합계 라인을 출력 — 없으면 수량만 표시 (하위 호환).
 *
 * 테이블 번호: orders_t.ot_table 은 사실 shop_table_t.idx 값이라
 * (app/index.php 의 의도적 설계, CLAUDE.md 참조), 영수증 표시용으로는
 * shop_table_t.tb_name 으로 변환하여 전달.
 *
 * 옵션: ct_snapshot 의 options 는 dict 배열({om_idx,option_name,option_price,...})
 * 이라 그대로 보내면 클라이언트가 dict repr 로 출력. 사람 읽기 가능
 * 문자열 리스트로 변환 — 가격>0 인 옵션은 '이름 +N원' suffix.
 */
function build_print_payload(array $order, string $printer_type, array $items): array {
    global $DB;
    $snapshot = json_decode($order['ct_snapshot'] ?? '{}', true) ?: [];
    $summary  = $snapshot['summary'] ?? null;

    // ot_table (= shop_table_t.idx) → tb_name 변환
    $table_display = (string)($order['ot_table'] ?? '-');
    $st_idx = (int)($order['ot_table'] ?? 0);
    if ($st_idx > 0) {
        $DB->where('idx', $st_idx);
        $row = $DB->getOne('shop_table_t', null, 'tb_name');
        if ($row && !empty($row['tb_name'])) {
            $table_display = (string)$row['tb_name'];
        }
    }

    return [
        'printer_type' => $printer_type,
        'table_name'   => $table_display,
        'order_time'   => $order['ot_wdate'] ?? date('Y-m-d H:i:s'),
        'items'        => array_map(function($i) {
            $line = [
                'name'    => $i['menu_name'] ?? '',
                'qty'     => (int)($i['quantity'] ?? 1),
                'options' => _print_format_options($i['options'] ?? []),
            ];
            if (isset($i['unit_price'])) {
                $line['unit_price'] = (int)$i['unit_price'];
            }
            return $line;
        }, $items),
        'memo'         => $order['ot_notes'] ?? '',
        // summary.total / sub_total / discount 만 클라이언트로 전달.
        // null 이면 클라이언트가 items 합산으로 자동 계산.
        'summary'      => is_array($summary) ? [
            'sub_total' => isset($summary['sub_total']) ? (int)$summary['sub_total'] : null,
            'discount'  => isset($summary['discount'])  ? (int)$summary['discount']  : null,
            'total'     => isset($summary['total'])     ? (int)$summary['total']     : null,
        ] : null,
    ];
}

/**
 * 옵션 배열 → 영수증 표시용 문자열 리스트.
 *
 * 입력: ct_snapshot.items[].options — dict 배열 (option_name/option_price 포함)
 *       또는 이미 문자열 배열 (구버전 또는 외부 입력)
 * 출력: 사람 읽기 가능 문자열 리스트. 가격>0 옵션은 '이름 +N원'.
 */
function _print_format_options($opts): array {
    if (!is_array($opts)) return [];
    $out = [];
    foreach ($opts as $opt) {
        if (is_string($opt)) {
            $opt = trim($opt);
            if ($opt !== '') $out[] = $opt;
            continue;
        }
        if (!is_array($opt)) continue;
        $name  = trim((string)($opt['option_name'] ?? ''));
        $price = (int)($opt['option_price'] ?? 0);
        if ($name === '') continue;
        $out[] = $price > 0 ? sprintf('%s +%s원', $name, number_format($price)) : $name;
    }
    return $out;
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
