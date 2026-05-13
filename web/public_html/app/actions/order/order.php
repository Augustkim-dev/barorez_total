<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
require_once $_SERVER['DOCUMENT_ROOT']."/cfg/print.inc.php";
header('Content-Type: application/json; charset=utf-8');

/* =========================================================
 * 설정: table_visit_t 테이블/컬럼 매핑
 * ========================================================= */
$VISIT_TBL = 'table_visit_t';

$VISIT_COLS = [
    'pk'     => 'idx',
    'sh_idx' => 'sh_idx',
    'table'  => 'tv_table',
    'token'  => 'visit_key',
    'mt_idx' => 'mt_idx',
    'start'  => 'tv_started',
    'last'   => 'tv_last_active',
    'end'    => 'tv_ended',
];

$ORDER_VISIT_FK = 'tv_idx';

define('VISIT_TTL_MINUTES', 180);

/* =========================================================
 * 포트원 API 헬퍼 함수
 * ========================================================= */

function logPayment($level, $message, $context = []) {
    // 운영에서는 민감정보(토큰/카드 등) 남기지 않기
    $safeContext = $context;

    // 너무 커지는 것 방지
    $line = sprintf(
        "[%s] [%s] %s | %s\n",
        date('Y-m-d H:i:s'),
        strtoupper($level),
        $message,
        json_encode($safeContext, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
    );

    $logDir = $_SERVER['DOCUMENT_ROOT'] . '/_logs';
    if (!is_dir($logDir)) @mkdir($logDir, 0755, true);

    @file_put_contents($logDir . '/payment.log', $line, FILE_APPEND);
}

function getShopInfo($DB, $sh_idx) {
    if ($sh_idx <= 0) {
        return ['full' => '매장', 'img' => DESIGN_HTTP . '/img/pr_sample01.jpg'];
    }
    static $cache = [];
    if (isset($cache[$sh_idx])) return $cache[$sh_idx];

    $shop = $DB->where('idx', $sh_idx)->getOne('shop_t', ['sh_title', 'sh_branch_nm', 'sh_img1']);
    $full = trim($shop['sh_title'] ?? '매장') . (trim($shop['sh_branch_nm'] ?? '') ? " [{$shop['sh_branch_nm']}]" : '');
    $img  = $shop['sh_img1'] ?? DESIGN_HTTP . '/img/pr_sample01.jpg';

    $cache[$sh_idx] = ['full' => $full, 'img' => $img];
    return $cache[$sh_idx];
}

define('PORTONE_API_URL', 'https://api.portone.io');          // ✅ V2 호스트

/**
 * 포트원 결제 정보 조회
 */
function getPortonePayment($paymentId) {
    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL => PORTONE_API_URL . '/payments/' . rawurlencode($paymentId),
        CURLOPT_HTTPGET => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: PortOne ' . PORTONE_API_SECRET,
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_TIMEOUT => 30,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        throw new Exception('포트원 서버 통신 실패: ' . $curlError);
    }

    if ($httpCode < 200 || $httpCode >= 300) {
        throw new Exception("결제 정보 조회 실패 ({$httpCode}): " . $response);
    }

    $result = json_decode($response, true);
    if (!$result) {
        throw new Exception('결제 정보 파싱 실패: ' . $response);
    }

    return $result;
}

/**
 * 결제 내역 저장
 */
function savePaymentRecord($DB, $ot_idx, $sh_idx, $mt_idx, $merchant_uid, $paymentData) {
    $amount = (float)($paymentData['amount']['total'] ?? 0);
    $status = $paymentData['status'] ?? 'READY';

    $DB->where('idx',$sh_idx);
    $sh_name = $DB->getOne('shop_t','sh_title');

    $DB->where('idx',$mt_idx);
    $md = $DB->getOne('member_t','mt_name, mt_hp, mt_email');

    // 포트원 상태를 DB 상태로 매핑
    $dbStatus = 'READY';
    switch (strtoupper($status)) {
        case 'PAID':
            $dbStatus = 'PAID';
            break;
        case 'FAILED':
            $dbStatus = 'FAILED';
            break;
        case 'CANCELLED':
            $dbStatus = 'CANCELLED';
            break;
        case 'PARTIAL_CANCELLED':
            $dbStatus = 'PARTIAL_CANCELLED';
            break;
    }

    $paidAt = null;
    if (isset($paymentData['paid_at'])) {
        $paidAt = date('Y-m-d H:i:s', strtotime($paymentData['paid_at']));
    }

    $cancelledAt = null;
    if (isset($paymentData['cancelled_at'])) {
        $cancelledAt = date('Y-m-d H:i:s', strtotime($paymentData['cancelled_at']));
    }

    // 중복 체크
    $DB->where('merchant_uid', $merchant_uid);
    $existing = $DB->getOne('payments_t', ['idx']);

    if ($existing) {
        // 이미 존재하면 업데이트
        $DB->where('idx', $existing['idx']);
        $updateResult = $DB->update('payments_t', [
            'imp_uid' => $paymentData['id'] ?? null,
            'status' => $dbStatus,
            'paid_at' => $paidAt,
            'cancelled_at' => $cancelledAt,
            'pg_payload' => json_encode($paymentData, JSON_UNESCAPED_UNICODE),
            'updated_at' => $DB->now(),
        ]);

        if (!$updateResult) {
            throw new Exception('결제 내역 업데이트 실패');
        }

        logPayment('INFO', '결제 내역 업데이트', ['merchant_uid' => $merchant_uid]);
        return $existing['idx'];
    }

    // 신규 저장
    $paymentId = $DB->insert('payments_t', [
        'ot_idx' => $ot_idx,
        'sh_idx' => $sh_idx,
        'sh_name' => $sh_name['sh_title'],
        'mt_idx' => ($mt_idx > 0 ? $mt_idx : null),
        'mt_name'  => !empty($md['mt_name'])  ? $md['mt_name']  : '비회원',
        'mt_hp'    => !empty($md['mt_hp'])    ? $md['mt_hp']    : '010-0000-0000',
        'mt_email' => !empty($md['mt_email']) ? $md['mt_email'] : 'guest@qrorder.com',
        'merchant_uid' => $merchant_uid,
        'imp_uid' => $paymentData['id'] ?? null,
        'pg_provider' => $paymentData['pg_provider'] ?? 'inicis',
        'pay_method' => $paymentData['pay_method'] ?? 'card',
        'currency' => $paymentData['currency'] ?? 'KRW',
        'amount_total' => $amount,
        'amount_paid' => $amount,
        'amount_refunded' => 0,
        'amount_remain' => $amount,
        'status' => $dbStatus,
        'paid_at' => $paidAt,
        'cancelled_at' => $cancelledAt,
        'pg_payload' => json_encode($paymentData, JSON_UNESCAPED_UNICODE),
        'created_at' => $DB->now(),
        'updated_at' => $DB->now(),
    ]);

    if (!$paymentId) {
        throw new Exception('결제 내역 저장 실패: ' . $DB->getLastError());
    }

    logPayment('INFO', '결제 내역 저장', ['merchant_uid' => $merchant_uid, 'payment_id' => $paymentId]);

    return $paymentId;
}

/* =========================================================
 * 주문 관련 헬퍼 함수
 * ========================================================= */

/**
 * 후결제 주문 생성
 */
function createOrderPostpaid($DB, $mt_idx, $shopId, $ot_number, $snap, $discount, $cl_idx, $ot_notes, $tv_idx, $ot_table) {
    $finalPrice = $snap['total_price'] - $discount;
    if ($finalPrice < 0) $finalPrice = 0;

    $snapshot = [
        'items' => $snap['items'],
        'summary' => [
            'sub_total' => $snap['total_price'],
            'discount'  => $discount,
            'total'     => $finalPrice,
        ]
    ];
    $ct_snapshot_json = json_encode($snapshot, JSON_UNESCAPED_UNICODE);

    $orderId = $DB->insert('orders_t', [
        'tv_idx'             => $tv_idx,
        'mt_idx'             => ($mt_idx > 0 ? $mt_idx : null),
        'sh_idx'             => $shopId,
        'rv_idx'             => null,
        'ot_number'          => $ot_number,
        'ot_status'          => 'PENDING',
        'ot_table'           => $ot_table,
        'ot_total_price'     => $finalPrice,
        'cl_idx'             => $cl_idx,
        'ot_discount_amount' => $discount,
        'ct_snapshot'        => $ct_snapshot_json,
        'ot_notes'           => $ot_notes,
        'ot_wdate'           => $DB->now(),
        'ot_pay_type'        => 'POSTPAID',
        'ot_pay_status'      => 'UNPAID',
        'ot_pay_date'        => $DB->now(),
    ]);

    if (!$orderId) throw new Exception('후결제 주문 저장 실패: ' . $DB->getLastError());

    // === 영수증 자동 출력 hook (Phase 1 D2) ===
    try_emit_print_jobs((int)$orderId, (int)$shopId);

    return $orderId;
}

/**
 * 회원 장바구니 세션 복원
 */
function restoreCartSessionIfNeeded($DB, $mt_idx, &$st_id, &$ct_ids) {
    if ($mt_idx <= 0 || ($st_id > 0 && !empty($ct_ids))) return;

    $DB->where('mt_idx', $mt_idx);
    $DB->orderBy('idx', 'DESC');
    $latest = $DB->getOne('cart_t', ['st_id']);

    $db_st_id = (int)($latest['st_id'] ?? 0);
    if ($db_st_id <= 0) {
        unset($_SESSION['cart_store_id'], $_SESSION['cart_ct_ids']);
        $_SESSION['cart_qty'] = 0;
        $st_id = 0;
        $ct_ids = [];
        return;
    }

    $st_id = $db_st_id;
    $_SESSION['cart_store_id'] = $st_id;

    $DB->where('mt_idx', $mt_idx);
    $DB->where('st_id', $st_id);
    $rows = $DB->get('cart_t', null, ['idx', 'ct_quantity']);

    $newCtIds = [];
    $newQty = 0;
    foreach ($rows as $r) {
        $newCtIds[] = (int)$r['idx'];
        $newQty += (int)($r['ct_quantity'] ?? 0);
    }

    $_SESSION['cart_ct_ids'] = $newCtIds;
    $_SESSION['cart_qty'] = $newQty;
    $ct_ids = $newCtIds;
}

/**
 * 쿠폰 할인 계산
 */
function calcCouponDiscount($coupon, $totalPrice) {
    $type2 = (int)($coupon['ct_type2'] ?? 1);
    $val   = (int)($coupon['ct_discount1'] ?? 0);

    $disc = ($type2 === 2) ? floor($totalPrice * ($val / 100)) : $val;
    return max(0, min($disc, $totalPrice));
}

/**
 * 쿠폰 유효성 검증
 */
function isCouponValid($coupon, $today, $totalPrice, $mt_idx) {
    if (($coupon['ct_show'] ?? 'Y') !== 'Y' || ($coupon['ct_del_yn'] ?? 'N') !== 'N') return false;
    if ((int)($coupon['ct_discount3'] ?? 0) > $totalPrice) return false;

    if (($coupon['ct_target_scope'] ?? 'ALL') === 'MEMBER') {
        if ($mt_idx <= 0) return false;
        $csv = trim($coupon['ct_target_members'] ?? '');
        if ($csv !== '') {
            $arr = array_map('intval', array_filter(array_map('trim', explode(',', $csv))));
            if (!in_array($mt_idx, $arr, true)) return false;
        }
    }

    $type1 = (int)($coupon['ct_type1'] ?? 1);
    if ($type1 === 1) {
        $s = $coupon['ct_sdate'] ?? null;
        $e = $coupon['ct_edate'] ?? null;
        if (!empty($s) && $s > $today) return false;
        if (!empty($e) && $e < $today) return false;
    } else {
        $days = (int)($coupon['ct_days'] ?? -1);
        if ($days < 0) return false;
        $exp = date('Y-m-d', strtotime($today . " +{$days} day"));
        if ($today > $exp) return false;
    }
    return true;
}

/**
 * 주문번호 생성
 */
function generateOrderNumber($DB, $ymd = null) {
    $ymd = $ymd ?: date('Ymd');
    $prefix = "OR-{$ymd}-";
    $lockName = "order_no_{$ymd}";

    $lock = $DB->rawQueryOne("SELECT GET_LOCK(?, 3) AS l", [$lockName]);
    if (!$lock || $lock['l'] != 1) {
        throw new Exception('주문번호 잠금 획득 실패');
    }

    try {
        $last = $DB->rawQueryOne(
            "SELECT ot_number FROM orders_t WHERE ot_number LIKE ? ORDER BY ot_number DESC LIMIT 1",
            [$prefix . '%']
        );

        $nextSeq = 1;
        if ($last && $last['ot_number']) {
            $n = (int)substr($last['ot_number'], -4);
            if ($n > 0) $nextSeq = $n + 1;
        }

        return $prefix . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);
    } finally {
        $DB->rawQuery("SELECT RELEASE_LOCK(?)", [$lockName]);
    }
}

/**
 * QR 토큰 방식 테이블 정보 파싱
 */
function parseTableFromSession($DB) {
    $token = '';
    if (!empty($_SESSION['qr_token'])) {
        $token = trim((string)$_SESSION['qr_token']);
    } else if (!empty($_GET['tk'])) {
        $token = trim((string)$_GET['tk']);
        $_SESSION['qr_token'] = $token;
    }

    if ($token === '') return [0, ''];

    $DB->where('qr_token', $token);
    $qr = $DB->getOne('shop_table_qr_t', 'sh_idx, tb_idx');
    if (!$qr) return [0, ''];

    $shopId = (int)($qr['sh_idx'] ?? 0);
    $tbIdx  = (int)($qr['tb_idx'] ?? 0);
    if ($shopId <= 0 || $tbIdx <= 0) return [0, ''];

    $DB->where('idx', $tbIdx);
    $DB->where('sh_idx', $shopId);
    $DB->where('use_yn', 'Y');
    $tb = $DB->getOne('shop_table_t', 'idx');
    if (!$tb) return [0, ''];

    $tableNo = trim((string)($tb['idx'] ?? ''));
    if ($tableNo === '') return [0, ''];

    $_SESSION['current_sh_idx'] = $shopId;
    $_SESSION['table_no']       = $tableNo;
    $_SESSION['is_qr_order']    = true;

    return [$shopId, $tableNo];
}

/**
 * 방문 토큰 관리
 */
function getVisitToken() {
    return $_SESSION['visit_token'] ?? $_COOKIE['visit_token'] ?? '';
}

function setVisitToken($token) {
    $_SESSION['visit_token'] = $token;
    setcookie('visit_token', $token, time() + 86400 * 30, '/', '', false, true);
}

/**
 * 방문 확보
 */
function ensureVisit($DB, $mt_idx, $shopId, $tableNo, $VISIT_TBL, $VISIT_COLS) {
    if ($shopId <= 0 || $tableNo === '') {
        throw new Exception('테이블 정보가 없습니다.');
    }

    $token = getVisitToken();
    if ($token === '') {
        $token = bin2hex(random_bytes(32));
        setVisitToken($token);
    } elseif (empty($_SESSION['visit_token'])) {
        $_SESSION['visit_token'] = $token;
    }

    $ttlCut = date('Y-m-d H:i:s', time() - (VISIT_TTL_MINUTES * 60));

    $DB->startTransaction();
    try {
        $DB->where($VISIT_COLS['sh_idx'], $shopId);
        $DB->where($VISIT_COLS['table'], $tableNo);
        $DB->where($VISIT_COLS['token'], $token);
        $DB->where($VISIT_COLS['end'], null, 'IS');
        $DB->where($VISIT_COLS['last'], $ttlCut, '>=');
        $visit = $DB->getOne($VISIT_TBL, [$VISIT_COLS['pk']], null, 'FOR UPDATE');

        if (!empty($visit[$VISIT_COLS['pk']])) {
            $visitId = (int)$visit[$VISIT_COLS['pk']];
            $_SESSION['visit_id'] = $visitId;

            $DB->where($VISIT_COLS['pk'], $visitId);
            $DB->update($VISIT_TBL, [
                $VISIT_COLS['last']   => $DB->now(),
                $VISIT_COLS['mt_idx'] => ($mt_idx > 0 ? $mt_idx : null),
            ]);

            $DB->commit();
            return $visitId;
        }

        $visitId = $DB->insert($VISIT_TBL, [
            $VISIT_COLS['token']  => $token,
            $VISIT_COLS['sh_idx'] => $shopId,
            $VISIT_COLS['table']  => $tableNo,
            $VISIT_COLS['mt_idx'] => ($mt_idx > 0 ? $mt_idx : null),
            $VISIT_COLS['start']  => $DB->now(),
            $VISIT_COLS['last']   => $DB->now(),
            $VISIT_COLS['end']    => null,
            'tv_status'           => 'ACTIVE',
        ]);

        if (!$visitId && $DB->getLastErrno() === 1062) {
            $DB->rollback();
            $visit = $DB->getOne($VISIT_TBL, [$VISIT_COLS['pk']]);
            if (!empty($visit[$VISIT_COLS['pk']])) {
                $visitId = (int)$visit[$VISIT_COLS['pk']];
                $_SESSION['visit_id'] = $visitId;
                return $visitId;
            }
        }

        if (!$visitId) {
            $DB->rollback();
            throw new Exception('방문 세션 생성 실패');
        }

        $_SESSION['visit_id'] = (int)$visitId;
        $DB->commit();
        return (int)$visitId;

    } catch (Exception $e) {
        $DB->rollback();
        throw $e;
    }
}

/**
 * 장바구니 스냅샷 생성
 */
function buildCartSnapshot($DB, $mt_idx, $st_id, $ct_ids) {
    if ($mt_idx > 0) {
        $DB->where('mt_idx', $mt_idx);
        $DB->where('st_id', $st_id);
        $cartRows = $DB->get('cart_t', null, ['idx','sm_id','ct_quantity','ct_price','ct_total_price']);
    } else {
        $ct_ids = array_values(array_filter(array_map('intval', (array)$ct_ids)));
        if (empty($ct_ids)) $cartRows = [];
        else {
            $DB->where('idx', $ct_ids, 'IN');
            $DB->where('st_id', $st_id);
            $cartRows = $DB->get('cart_t', null, ['idx','sm_id','ct_quantity','ct_price','ct_total_price']);
        }
    }

    $ctIdxList = array_map(fn($r) => (int)$r['idx'], $cartRows);

    $optMap = [];
    if (!empty($ctIdxList)) {
        $DB->where('ct_idx', $ctIdxList, 'IN');
        $optRows = $DB->get('cart_options_t', null, ['ct_idx','om_idx','oc_idx','co_option_name','co_option_price']);
        foreach ($optRows as $o) {
            $k = (int)$o['ct_idx'];
            $optMap[$k][] = [
                'om_idx'       => (int)$o['om_idx'],
                'oc_idx'       => (int)$o['oc_idx'],
                'option_name'  => $o['co_option_name'],
                'option_price' => (int)$o['co_option_price'],
                'quantity'     => 1,
            ];
        }
    }

    $smIds = array_unique(array_filter(array_map(fn($r) => (int)$r['sm_id'], $cartRows)));
    $menuNameMap = [];
    if (!empty($smIds)) {
        $DB->where('idx', $smIds, 'IN');
        $menus = $DB->get('shop_menu_t', null, ['idx','sm_title','sm_show','sm_type','sm_su']);
        foreach ($menus as $m) $menuNameMap[(int)$m['idx']] = $m;
    }

    $items = [];
    $totalQty = 0;
    $totalPrice = 0;

    foreach ($cartRows as $r) {
        $ct_idx = (int)$r['idx'];
        $sm_id  = (int)$r['sm_id'];
        $qty    = max(1, (int)$r['ct_quantity']);
        $unit   = (int)$r['ct_price'];
        $line   = (int)$r['ct_total_price'];

        $m = $menuNameMap[$sm_id] ?? null;
        if (!$m) throw new Exception('메뉴 정보를 찾을 수 없습니다.');
        if ($m['sm_show'] !== 'Y') throw new Exception('노출 중인 메뉴가 아닙니다.');
        if ($m['sm_type'] === 'N') throw new Exception('판매중인 메뉴가 아닙니다.');

        $totalQty += $qty;
        $totalPrice += $line;

        $items[] = [
            'sm_id'       => $sm_id,
            'menu_name'   => $m['sm_title'],
            'quantity'    => $qty,
            'unit_price'  => $unit,
            'total_price' => $line,
            'options'     => $optMap[$ct_idx] ?? [],
        ];
    }

    return [
        'ct_idx_list' => $ctIdxList,
        'items'       => $items,
        'total_qty'   => $totalQty,
        'total_price' => $totalPrice,
    ];
}

/**
 * 장바구니 비우기
 */
function clearCart($DB, $mt_idx, $st_id, $ct_ids, $ctIdxList) {
    $DB->startTransaction();

    if (!empty($ctIdxList)) {
        $DB->where('ct_idx', $ctIdxList, 'IN');
        $DB->delete('cart_options_t');
    }

    if ($mt_idx > 0) {
        $DB->where('mt_idx', $mt_idx);
        $DB->where('st_id', $st_id);
        $DB->delete('cart_t');
    } else {
        $ct_ids = array_values(array_filter(array_map('intval', (array)$ct_ids)));
        if (!empty($ct_ids)) {
            $DB->where('idx', $ct_ids, 'IN');
            $DB->where('st_id', $st_id);
            $DB->delete('cart_t');
        }
    }

    $DB->commit();

    unset($_SESSION['cart_store_id'], $_SESSION['cart_ct_ids']);
    $_SESSION['cart_qty'] = 0;
    unset($_SESSION['order_coupon']);
}

/**
 * 쿠폰 사용 이력 저장
 */
function consumeCouponLog($DB, $ct_idx, $mt_idx) {
    if ($mt_idx <= 0 || $ct_idx <= 0) return;

    $DB->where('ct_idx', $ct_idx);
    $DB->where('mt_idx', $mt_idx);
    $log = $DB->getOne('coupon_log_t', ['idx','cl_view']);

    if ($log) {
        if (($log['cl_view'] ?? 'N') === 'Y') {
            throw new Exception('이미 사용한 쿠폰입니다.');
        }

        $DB->where('idx', (int)$log['idx']);
        $ok = $DB->update('coupon_log_t', [
            'cl_view'  => 'Y',
            'cl_udate' => $DB->now(),
        ]);
        if (!$ok) throw new Exception('쿠폰 사용 처리 실패');
        return;
    }

    $id = $DB->insert('coupon_log_t', [
        'ct_idx'   => $ct_idx,
        'mt_idx'   => $mt_idx,
        'cl_view'  => 'Y',
        'cl_wdate' => $DB->now(),
        'cl_udate' => $DB->now(),
    ]);
    if (!$id) throw new Exception('쿠폰 사용 이력 저장 실패');
}

/**
 * 공통 주문 생성
 */
function createOrderCommon($DB, $mt_idx, $shopId, $ot_number, $snap, $discount, $cl_idx, $ot_notes, $rv_idx = null, $tv_idx = null, $ot_table = null) {
    $finalPrice = $snap['total_price'] - $discount;

    $snapshot = [
        'items' => $snap['items'],
        'summary' => [
            'sub_total' => $snap['total_price'],
            'discount'  => $discount,
            'total'     => $finalPrice,
        ]
    ];
    $ct_snapshot_json = json_encode($snapshot, JSON_UNESCAPED_UNICODE);

    $orderId = $DB->insert('orders_t', [
        'tv_idx'             => $tv_idx,
        'mt_idx'             => ($mt_idx > 0 ? $mt_idx : null),
        'sh_idx'             => $shopId,
        'rv_idx'             => $rv_idx,
        'ot_number'          => $ot_number,
        'ot_status'          => 'PENDING',
        'ot_table'           => $ot_table,
        'ot_total_price'     => $finalPrice,
        'cl_idx'             => $cl_idx,
        'ot_discount_amount' => $discount,
        'ct_snapshot'        => $ct_snapshot_json,
        'ot_notes'           => $ot_notes,
        'ot_wdate'           => $DB->now(),
        'ot_pay_type'        => 'PREPAID',
        'ot_pay_status'      => 'PAID',
    ]);

    if (!$orderId) throw new Exception('주문 저장 실패: ' . $DB->getLastError());

    // === 영수증 자동 출력 hook (Phase 1 D2) ===
    try_emit_print_jobs((int)$orderId, (int)$shopId);

    return $orderId;
}

function createOrderReservationPostpaid($DB, $mt_idx, $shopId, $ot_number, $snap, $discount, $cl_idx, $ot_notes, $rv_idx) {
    $finalPrice = $snap['total_price'] - $discount;
    if ($finalPrice < 0) $finalPrice = 0;

    $snapshot = [
        'items' => $snap['items'],
        'summary' => [
            'sub_total' => $snap['total_price'],
            'discount'  => $discount,
            'total'     => $finalPrice,
        ]
    ];
    $ct_snapshot_json = json_encode($snapshot, JSON_UNESCAPED_UNICODE);

    $orderId = $DB->insert('orders_t', [
        'tv_idx'             => null,
        'mt_idx'             => ($mt_idx > 0 ? $mt_idx : null),
        'sh_idx'             => $shopId,
        'rv_idx'             => (int)$rv_idx,
        'ot_number'          => $ot_number,
        'ot_status'          => 'PENDING',
        'ot_table'           => null,
        'ot_total_price'     => $finalPrice,
        'cl_idx'             => $cl_idx,
        'ot_discount_amount' => $discount,
        'ct_snapshot'        => $ct_snapshot_json,
        'ot_notes'           => $ot_notes,
        'ot_wdate'           => $DB->now(),
        'ot_pay_type'        => 'POSTPAID',
        'ot_pay_status'      => 'UNPAID',
        'ot_pay_date'        => null,
    ]);

    if (!$orderId) {
        throw new Exception('예약 후결제 주문 저장 실패: ' . $DB->getLastError());
    }

    // === 영수증 자동 출력 hook (Phase 1 D2) ===
    try_emit_print_jobs((int)$orderId, (int)$shopId);

    return $orderId;
}


/* =========================================================
 * 메인 로직 시작
 * ========================================================= */
$act  = $_POST['act'] ?? '';
$csrf = $_POST['csrf_token'] ?? '';

$mt_idx = (int)($_SESSION['mng']['mt_idx'] ?? 0);
$st_id  = (int)($_SESSION['cart_store_id'] ?? 0);
$ct_ids = array_values(array_filter(array_map('intval', $_SESSION['cart_ct_ids'] ?? [])));

restoreCartSessionIfNeeded($DB, $mt_idx, $st_id, $ct_ids);

if ($act === '') {
    echo json_encode([
        'success' => false,
        'data'    => [],
        'sql'     => '',
        'search'  => null,
        'message' => '잘못된 접근입니다.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

list($qrShopId, $qrTableNo) = parseTableFromSession($DB);

if ($qrShopId > 0 && $st_id > 0 && $qrShopId !== $st_id) {
    echo json_encode([
        'success' => false,
        'data'    => [],
        'sql'     => $DB->getLastQuery(),
        'search'  => null,
        'message' => '장바구니 매장과 QR 매장이 다릅니다. 다시 QR을 스캔해주세요.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($st_id <= 0 && $qrShopId > 0) {
    $st_id = $qrShopId;
    $_SESSION['cart_store_id'] = $st_id;
}

$today = date('Y-m-d');

/* =========================================================
 * 1. 쿠폰 적용
 * ========================================================= */
if ($act === 'apply_coupon') {
    try {
        if ($qrShopId > 0 && $qrTableNo !== '') {
            ensureVisit($DB, $mt_idx, $qrShopId, $qrTableNo, $VISIT_TBL, $VISIT_COLS);
        }

        $snap = buildCartSnapshot($DB, $mt_idx, $st_id, $ct_ids);
        if ($snap['total_qty'] <= 0) {
            unset($_SESSION['order_coupon']);
            echo json_encode([
                'success' => true,
                'data'    => ['total_qty'=>0,'total_price'=>0,'discount'=>0,'final_price'=>0],
                'sql'     => $DB->getLastQuery(),
                'search'  => null
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $coupon_idx = (int)($_POST['ct_idx'] ?? 0);
        if ($coupon_idx <= 0) throw new Exception('쿠폰을 선택해주세요.');

        $DB->where('idx', $coupon_idx);
        $DB->where('(sh_idx = 0 OR sh_idx = ?)', [$st_id]);
        $coupon = $DB->getOne('coupon_t');
        if (!$coupon) throw new Exception('쿠폰을 찾을 수 없습니다.');

        if (!isCouponValid($coupon, $today, $snap['total_price'], $mt_idx)) {
            throw new Exception('사용할 수 없는 쿠폰입니다.');
        }

        $_SESSION['order_coupon'] = ['ct_idx' => $coupon_idx];
        $discount = calcCouponDiscount($coupon, $snap['total_price']);
        $finalPrice = $snap['total_price'] - $discount;

        logPayment('INFO', '쿠폰 적용', ['ct_idx' => $coupon_idx, 'discount' => $discount]);

        echo json_encode([
            'success' => true,
            'data'    => [
                'total_qty'    => $snap['total_qty'],
                'total_price'  => $snap['total_price'],
                'discount'     => $discount,
                'final_price'  => $finalPrice,
            ],
            'sql'     => $DB->getLastQuery(),
            'search'  => null
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        logPayment('ERROR', '쿠폰 적용 실패', ['error' => $e->getMessage()]);
        echo json_encode([
            'success' => false,
            'data'    => [],
            'sql'     => $DB->getLastQuery(),
            'search'  => null,
            'message' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

/* =========================================================
 * 2. 쿠폰 해제
 * ========================================================= */
elseif ($act === 'clear_coupon') {
    try {
        if ($qrShopId > 0 && $qrTableNo !== '') {
            ensureVisit($DB, $mt_idx, $qrShopId, $qrTableNo, $VISIT_TBL, $VISIT_COLS);
        }

        unset($_SESSION['order_coupon']);

        $snap = buildCartSnapshot($DB, $mt_idx, $st_id, $ct_ids);

        logPayment('INFO', '쿠폰 해제', []);

        echo json_encode([
            'success' => true,
            'data'    => [
                'total_qty'    => $snap['total_qty'],
                'total_price'  => $snap['total_price'],
                'discount'     => 0,
                'final_price'  => $snap['total_price'],
            ],
            'sql'     => $DB->getLastQuery(),
            'search'  => null
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        logPayment('ERROR', '쿠폰 해제 실패', ['error' => $e->getMessage()]);
        echo json_encode([
            'success' => false,
            'data'    => [],
            'sql'     => $DB->getLastQuery(),
            'search'  => null,
            'message' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

/* =========================================================
 * 3. 결제 준비 (QR 테이블 주문)
 * ========================================================= */
elseif ($act === 'prepare_payment') {
    try {
        $visitId = ensureVisit($DB, $mt_idx, $qrShopId, $qrTableNo, $VISIT_TBL, $VISIT_COLS);

        $snap = buildCartSnapshot($DB, $mt_idx, $st_id, $ct_ids);
        if ($snap['total_qty'] <= 0) {
            throw new Exception('장바구니가 비어 있습니다.');
        }

        $discount = 0;
        if (!empty($_SESSION['order_coupon']['ct_idx'])) {
            $coupon_idx = (int)$_SESSION['order_coupon']['ct_idx'];
            $DB->where('idx', $coupon_idx);
            $DB->where('(sh_idx = 0 OR sh_idx = ?)', [$st_id]);
            $coupon = $DB->getOne('coupon_t');

            if ($coupon && isCouponValid($coupon, $today, $snap['total_price'], $mt_idx)) {
                $discount = calcCouponDiscount($coupon, $snap['total_price']);
            } else {
                unset($_SESSION['order_coupon']);
            }
        }

        $merchant_uid = generateOrderNumber($DB);

        $payment_id = 'pay_' . $merchant_uid . '_' . time();

        $_SESSION['pending_payment'] = [
            'merchant_uid' => $merchant_uid,
            'visit_id' => $visitId,
            'discount' => $discount,
            'type' => 'qr'
        ];

        // 회원 정보 조회
        $buyer_name = '고객';
        $buyer_tel = '';
        $buyer_email = '';

        if ($mt_idx > 0) {
            $DB->where('idx', $mt_idx);
            $member = $DB->getOne('member_t', ['mt_name', 'mt_hp', 'mt_email']);
            if ($member) {
                $buyer_name = $member['mt_name'] ?? '고객';
                $buyer_tel = $member['mt_hp'] ?? '';
                $buyer_email = $member['mt_email'] ?? '';
            }
        }

        // 주문명 생성
        $order_name = $snap['items'][0]['menu_name'];
        if (count($snap['items']) > 1) {
            $order_name .= ' 외 ' . (count($snap['items']) - 1) . '개';
        }

        $finalAmount = $snap['total_price'] - $discount;

        logPayment('INFO', '결제 준비 (QR)', [
            'merchant_uid' => $merchant_uid,
            'amount' => $finalAmount,
            'items_count' => count($snap['items'])
        ]);

        echo json_encode([
            'success' => true,
            'data'    => [
                'merchant_uid' => $merchant_uid,
                'payment_id'   => $payment_id,
                'order_name'   => $order_name,
                'amount'       => $finalAmount,
                'buyer_name'   => $buyer_name,
                'buyer_tel'    => $buyer_tel,
                'buyer_email'  => $buyer_email,
            ],
            'sql'     => $DB->getLastQuery(),
            'search'  => null
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        logPayment('ERROR', '결제 준비 실패 (QR)', ['error' => $e->getMessage()]);
        echo json_encode([
            'success' => false,
            'data'    => [],
            'sql'     => $DB->getLastQuery(),
            'search'  => null,
            'message' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

/* =========================================================
 * 4. 결제 검증 및 완료 (QR 테이블 주문)
 * ========================================================= */
elseif ($act === 'verify_payment') {
    try {
        $payment_id = trim($_POST['payment_id'] ?? '');
        $merchant_uid = trim($_POST['merchant_uid'] ?? '');

        if (!$payment_id || !$merchant_uid) {
            throw new Exception('결제 정보가 없습니다.');
        }

        $pending = $_SESSION['pending_payment'] ?? [];
        if (empty($pending) || $pending['merchant_uid'] !== $merchant_uid) {
            throw new Exception('결제 세션이 유효하지 않습니다.');
        }

        // 포트원에서 결제 정보 조회
        $paymentData = getPortonePayment($payment_id);

        if ($paymentData['status'] !== 'PAID') {
            throw new Exception('결제가 완료되지 않았습니다. 상태: ' . ($paymentData['status'] ?? 'UNKNOWN'));
        }

        // 금액 검증
        $snap = buildCartSnapshot($DB, $mt_idx, $st_id, $ct_ids);
        $expectedAmount = $snap['total_price'] - $pending['discount'];
        $paidAmount = (float)($paymentData['amount']['total'] ?? 0);

        if (abs($expectedAmount - $paidAmount) > 0.01) {
            logPayment('ERROR', '금액 불일치', [
                'expected' => $expectedAmount,
                'paid' => $paidAmount,
                'merchant_uid' => $merchant_uid
            ]);
            throw new Exception('결제 금액이 일치하지 않습니다.');
        }

        $DB->startTransaction();

        $discount = $pending['discount'];
        $cl_idx = null;
        if (!empty($_SESSION['order_coupon']['ct_idx'])) {
            $cl_idx = (int)$_SESSION['order_coupon']['ct_idx'];
        }

        $ot_notes = trim($_POST['ot_notes'] ?? '');
        $ot_notes = ($ot_notes !== '') ? mb_substr($ot_notes, 0, 2000) : null;
        $ot_table = ($qrTableNo !== '') ? $qrTableNo : null;

        $orderId = createOrderCommon(
            $DB, $mt_idx, $st_id, $merchant_uid, $snap, $discount, $cl_idx,
            $ot_notes, null, $pending['visit_id'], $ot_table
        );

        // 결제 완료 상태로 업데이트
        $DB->where('idx', $orderId);
        $DB->update('orders_t', [
            'ot_pay_status' => 'PAID',
            'ot_pay_date' => $DB->now(),
        ]);

        // 결제 내역 저장
        savePaymentRecord($DB, $orderId, $st_id, $mt_idx, $merchant_uid, $paymentData);

        if ($cl_idx > 0) {
            consumeCouponLog($DB, $cl_idx, $mt_idx);
        }

        clearCart($DB, $mt_idx, $st_id, $ct_ids, $snap['ct_idx_list']);
        unset($_SESSION['cart_store_id']);
        unset($_SESSION['order_coupon']);
        unset($_SESSION['pending_payment']);

        $DB->commit();

        logPayment('INFO', '결제 완료 (QR)', [
            'order_id' => $orderId,
            'merchant_uid' => $merchant_uid,
            'amount' => $expectedAmount
        ]);

        echo json_encode([
            'success' => true,
            'data'    => [
                'order_id'     => (int)$orderId,
                'ot_number'    => $merchant_uid,
                'visit_id'     => $pending['visit_id'],
                'total_qty'    => $snap['total_qty'],
                'total_price'  => $snap['total_price'],
                'discount'     => $discount,
                'final_price'  => $snap['total_price'] - $discount,
                'pay_status'   => 'PAID',
            ],
            'sql'     => $DB->getLastQuery(),
            'search'  => null
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        if ($DB && method_exists($DB, 'rollback')) $DB->rollback();
        logPayment('ERROR', '결제 검증 실패 (QR)', ['error' => $e->getMessage()]);
        echo json_encode([
            'success' => false,
            'data'    => [],
            'sql'     => $DB->getLastQuery(),
            'search'  => null,
            'message' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

/* =========================================================
 * 5. 결제 준비 (포장 주문)
 * ========================================================= */
elseif ($act === 'prepare_payment_takeout') {
    try {
        $shopId = (int)($_SESSION['cart_store_id'] ?? $_SESSION['current_sh_idx'] ?? 0);
        if ($shopId <= 0) {
            throw new Exception('매장 정보가 없습니다.');
        }

        $snap = buildCartSnapshot($DB, $mt_idx, $shopId, $ct_ids);
        if ($snap['total_qty'] <= 0) {
            throw new Exception('장바구니가 비어 있습니다.');
        }

        $discount = 0;
        if (!empty($_SESSION['order_coupon']['ct_idx'])) {
            $coupon_idx = (int)$_SESSION['order_coupon']['ct_idx'];
            $DB->where('idx', $coupon_idx);
            $DB->where('(sh_idx = 0 OR sh_idx = ?)', [$shopId]);
            $coupon = $DB->getOne('coupon_t');

            if ($coupon && isCouponValid($coupon, $today, $snap['total_price'], $mt_idx)) {
                $discount = calcCouponDiscount($coupon, $snap['total_price']);
            } else {
                unset($_SESSION['order_coupon']);
            }
        }

        $merchant_uid = generateOrderNumber($DB);

        $payment_id = 'pay_' . $merchant_uid . '_' . time();

        $_SESSION['pending_payment'] = [
            'merchant_uid' => $merchant_uid,
            'discount' => $discount,
            'type' => 'takeout',
        ];

        $buyer_name = '고객';
        $buyer_tel = '';
        $buyer_email = '';

        if ($mt_idx > 0) {
            $DB->where('idx', $mt_idx);
            $member = $DB->getOne('member_t', ['mt_name', 'mt_hp', 'mt_email']);
            if ($member) {
                $buyer_name = $member['mt_name'] ?? '고객';
                $buyer_tel = $member['mt_hp'] ?? '';
                $buyer_email = $member['mt_email'] ?? '';
            }
        }

        $order_name = $snap['items'][0]['menu_name'];
        if (count($snap['items']) > 1) {
            $order_name .= ' 외 ' . (count($snap['items']) - 1) . '개';
        }

        $finalAmount = $snap['total_price'] - $discount;

        logPayment('INFO', '결제 준비 (포장)', [
            'merchant_uid' => $merchant_uid,
            'amount' => $finalAmount
        ]);

        echo json_encode([
            'success' => true,
            'data'    => [
                'merchant_uid' => $merchant_uid,
                'payment_id'   => $payment_id,
                'order_name'   => $order_name,
                'amount'       => $finalAmount,
                'buyer_name'   => $buyer_name,
                'buyer_tel'    => $buyer_tel,
                'buyer_email'  => $buyer_email,
            ],
            'sql'     => $DB->getLastQuery(),
            'search'  => null
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        logPayment('ERROR', '결제 준비 실패 (포장)', ['error' => $e->getMessage()]);
        echo json_encode([
            'success' => false,
            'data'    => [],
            'sql'     => $DB->getLastQuery(),
            'search'  => null,
            'message' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

/* =========================================================
 * 6. 결제 검증 및 완료 (포장 주문)
 * ========================================================= */
elseif ($act === 'verify_payment_takeout') {
    try {
        $payment_id = trim($_POST['payment_id'] ?? '');
        $merchant_uid = trim($_POST['merchant_uid'] ?? '');

        if (!$payment_id || !$merchant_uid) {
            throw new Exception('결제 정보가 없습니다.');
        }

        $pending = $_SESSION['pending_payment'] ?? [];
        if (empty($pending) || $pending['merchant_uid'] !== $merchant_uid) {
            throw new Exception('결제 세션이 유효하지 않습니다.');
        }

        $shopId = (int)($_SESSION['cart_store_id'] ?? $_SESSION['current_sh_idx'] ?? 0);

        $paymentData = getPortonePayment($payment_id);

        if ($paymentData['status'] !== 'PAID') {
            throw new Exception('결제가 완료되지 않았습니다.');
        }

        $snap = buildCartSnapshot($DB, $mt_idx, $shopId, $ct_ids);
        $expectedAmount = $snap['total_price'] - $pending['discount'];
        $paidAmount = (float)($paymentData['amount']['total'] ?? 0);

        if (abs($expectedAmount - $paidAmount) > 0.01) {
            throw new Exception('결제 금액이 일치하지 않습니다.');
        }

        $DB->startTransaction();

        $discount = $pending['discount'];
        $cl_idx = null;
        if (!empty($_SESSION['order_coupon']['ct_idx'])) {
            $cl_idx = (int)$_SESSION['order_coupon']['ct_idx'];
        }

        $ot_notes = trim($_POST['ot_notes'] ?? '');
        $ot_notes = ($ot_notes !== '') ? mb_substr($ot_notes, 0, 2000) : null;

        $orderId = createOrderCommon(
            $DB, $mt_idx, $shopId, $merchant_uid, $snap, $discount, $cl_idx,
            $ot_notes, null, null, null
        );

        $DB->where('idx', $orderId);
        $DB->update('orders_t', [
            'ot_pay_status' => 'PAID',
            'ot_pay_date' => $DB->now(),
        ]);

        savePaymentRecord($DB, $orderId, $shopId, $mt_idx, $merchant_uid, $paymentData);

        if ($cl_idx > 0) consumeCouponLog($DB, $cl_idx, $mt_idx);

        clearCart($DB, $mt_idx, $shopId, $ct_ids, $snap['ct_idx_list']);
        unset($_SESSION['cart_store_id']);
        unset($_SESSION['order_coupon']);
        unset($_SESSION['pending_payment']);

        $DB->commit();

        logPayment('INFO', '결제 완료 (포장)', [
            'order_id' => $orderId,
            'merchant_uid' => $merchant_uid
        ]);

        echo json_encode([
            'success' => true,
            'data'    => [
                'order_id'     => (int)$orderId,
                'ot_number'    => $merchant_uid,
                'total_qty'    => $snap['total_qty'],
                'total_price'  => $snap['total_price'],
                'discount'     => $discount,
                'final_price'  => $snap['total_price'] - $discount,
                'pay_status'   => 'PAID',
            ],
            'sql'     => $DB->getLastQuery(),
            'search'  => null
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        if ($DB && method_exists($DB, 'rollback')) $DB->rollback();
        logPayment('ERROR', '결제 검증 실패 (포장)', ['error' => $e->getMessage()]);
        echo json_encode([
            'success' => false,
            'data'    => [],
            'sql'     => $DB->getLastQuery(),
            'search'  => null,
            'message' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

/* =========================================================
 * 7. 결제 준비 (예약 선결제)
 * ========================================================= */
elseif ($act === 'prepare_payment_reservation') {
    try {
        $shopId = (int)($_SESSION['current_sh_idx'] ?? 0);
        if ($shopId <= 0) throw new Exception('매장 정보가 없습니다.');

        $snap = buildCartSnapshot($DB, $mt_idx, $shopId, $ct_ids);
        if ($snap['total_qty'] <= 0) {
            throw new Exception('장바구니가 비어 있습니다.');
        }

        $discount = 0;
        if (!empty($_SESSION['order_coupon']['ct_idx'])) {
            $coupon_idx = (int)$_SESSION['order_coupon']['ct_idx'];
            $DB->where('idx', $coupon_idx);
            $DB->where('(sh_idx = 0 OR sh_idx = ?)', [$shopId]);
            $coupon = $DB->getOne('coupon_t');

            if ($coupon && isCouponValid($coupon, $today, $snap['total_price'], $mt_idx)) {
                $discount = calcCouponDiscount($coupon, $snap['total_price']);
            } else {
                unset($_SESSION['order_coupon']);
            }
        }

        $merchant_uid = generateOrderNumber($DB);

        $payment_id = 'pay_' . $merchant_uid . '_' . time();

        $_SESSION['pending_payment'] = [
            'merchant_uid' => $merchant_uid,
            'discount' => $discount,
            'type' => 'reservation',
        ];

        $buyer_name = '고객';
        $buyer_tel = '';
        $buyer_email = '';

        if ($mt_idx > 0) {
            $DB->where('idx', $mt_idx);
            $member = $DB->getOne('member_t', ['mt_name', 'mt_hp', 'mt_email']);
            if ($member) {
                $buyer_name = $member['mt_name'] ?? '고객';
                $buyer_tel = $member['mt_hp'] ?? '';
                $buyer_email = $member['mt_email'] ?? '';
            }
        }

        $order_name = $snap['items'][0]['menu_name'];
        if (count($snap['items']) > 1) {
            $order_name .= ' 외 ' . (count($snap['items']) - 1) . '개';
        }

        $finalAmount = $snap['total_price'] - $discount;

        logPayment('INFO', '결제 준비 (예약)', [
            'merchant_uid' => $merchant_uid,
            'amount' => $finalAmount
        ]);

        echo json_encode([
            'success' => true,
            'data'    => [
                'merchant_uid' => $merchant_uid,
                'payment_id'   => $payment_id,
                'order_name'   => $order_name,
                'amount'       => $finalAmount,
                'buyer_name'   => $buyer_name,
                'buyer_tel'    => $buyer_tel,
                'buyer_email'  => $buyer_email,
            ],
            'sql'     => $DB->getLastQuery(),
            'search'  => null
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        logPayment('ERROR', '결제 준비 실패 (예약)', ['error' => $e->getMessage()]);
        echo json_encode([
            'success' => false,
            'data'    => [],
            'sql'     => $DB->getLastQuery(),
            'search'  => null,
            'message' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

/* =========================================================
 * 8. 결제 검증 및 완료 (예약 선결제)
 * ========================================================= */
elseif ($act === 'verify_payment_reservation') {
    try {
        $payment_id = trim($_POST['payment_id'] ?? '');
        $merchant_uid = trim($_POST['merchant_uid'] ?? '');

        if (!$payment_id || !$merchant_uid) {
            throw new Exception('결제 정보가 없습니다.');
        }

        $pending = $_SESSION['pending_payment'] ?? [];
        if (empty($pending) || $pending['merchant_uid'] !== $merchant_uid) {
            throw new Exception('결제 세션이 유효하지 않습니다.');
        }

        $shopId = (int)($_SESSION['current_sh_idx'] ?? 0);

        $paymentData = getPortonePayment($payment_id);

        if ($paymentData['status'] !== 'PAID') {
            throw new Exception('결제가 완료되지 않았습니다.');
        }

        $snap = buildCartSnapshot($DB, $mt_idx, $shopId, $ct_ids);
        $expectedAmount = $snap['total_price'] - $pending['discount'];
        $paidAmount = (float)($paymentData['amount']['total'] ?? 0);

        if (abs($expectedAmount - $paidAmount) > 0.01) {
            throw new Exception('결제 금액이 일치하지 않습니다.');
        }

        // 예약 정보 가져오기
        $rv_name   = trim($_POST['rv_name'] ?? ($_SESSION['reservation_form']['rv_name'] ?? ''));
        $rv_hp     = trim($_POST['rv_hp'] ?? ($_SESSION['reservation_form']['rv_hp'] ?? ''));
        $rv_date   = trim($_POST['rv_date'] ?? ($_SESSION['reservation_form']['rv_date'] ?? ''));
        $rv_time   = trim($_POST['rv_time'] ?? ($_SESSION['reservation_form']['rv_time'] ?? ''));
        $rv_people = (int)($_POST['rv_people'] ?? ($_SESSION['reservation_form']['rv_people'] ?? 1));
        $rv_memo   = trim($_POST['rv_memo'] ?? ($_SESSION['reservation_form']['rv_memo'] ?? ''));

        if ($rv_name === '') throw new Exception('예약자 이름을 입력해 주세요.');
        if ($rv_hp === '') throw new Exception('예약자 휴대폰번호를 입력해 주세요.');
        if ($rv_date === '') throw new Exception('예약 날짜를 선택해 주세요.');
        if ($rv_time === '') throw new Exception('예약 시간을 선택해 주세요.');

        $ot_notes = trim($_POST['ot_notes'] ?? '');
        $ot_notes = ($ot_notes !== '') ? mb_substr($ot_notes, 0, 2000) : null;

        $discount = $pending['discount'];
        $finalPrice = $snap['total_price'] - $discount;

        $snapshot = [
            'items' => $snap['items'],
            'summary' => [
                'sub_total' => $snap['total_price'],
                'discount'  => $discount,
                'total'     => $finalPrice,
            ],
            'reservation' => [
                'rv_name' => $rv_name,
                'rv_hp' => $rv_hp,
                'rv_date' => $rv_date,
                'rv_time' => $rv_time,
                'rv_people' => $rv_people,
                'rv_memo' => $rv_memo,
            ],
        ];

        $ymd = $mt_idx < 0 ? date('YmdHis') : date('Ymd');
        $DB->where('sh_idx', $shopId);
        $DB->where('mt_idx', $mt_idx);
        $row = $DB->getOne('reservation_t', 'COUNT(*) as cnt');
        $rv_number = $ymd . $shopId . $mt_idx . ($row['cnt'] ?? 0);

        $DB->startTransaction();

        $rvId = $DB->insert('reservation_t', [
            'sh_idx'    => $shopId,
            'mt_idx'    => ($mt_idx > 0 ? $mt_idx : null),
            'rv_number' => $rv_number,
            'rv_name'   => mb_substr($rv_name, 0, 100),
            'rv_hp'     => mb_substr($rv_hp, 0, 20),
            'rv_date'   => $rv_date,
            'rv_time'   => $rv_time,
            'rv_people' => $rv_people,
            'rv_type'   => 'PREPAID',
            'rv_status' => 'PENDING',
            'rv_memo'   => ($rv_memo !== '' ? mb_substr($rv_memo, 0, 2000) : null),
            'ot_idx'    => null,
            'rv_wdate'  => $DB->now(),
        ]);

        if (!$rvId) throw new Exception('예약 저장 실패');

        $cl_idx = null;
        if (!empty($_SESSION['order_coupon']['ct_idx'])) {
            $cl_idx = (int)$_SESSION['order_coupon']['ct_idx'];
        }

        $orderId = createOrderCommon(
            $DB, $mt_idx, $shopId, $merchant_uid, $snap, $discount, $cl_idx,
            $ot_notes, (int)$rvId, null, null
        );

        $DB->where('idx', $orderId);
        $DB->update('orders_t', [
            'ot_pay_status' => 'PAID',
            'ot_pay_date' => $DB->now(),
        ]);

        $DB->where('idx', (int)$rvId);
        $ok = $DB->update('reservation_t', [
            'ot_idx'   => (int)$orderId,
            'rv_udate' => $DB->now(),
        ]);
        if (!$ok) throw new Exception('예약-주문 연결 실패');

        savePaymentRecord($DB, $orderId, $shopId, $mt_idx, $merchant_uid, $paymentData);

        if ($cl_idx > 0) consumeCouponLog($DB, $cl_idx, $mt_idx);

        clearCart($DB, $mt_idx, $shopId, $ct_ids, $snap['ct_idx_list']);
        unset($_SESSION['cart_store_id']);
        unset($_SESSION['reservation_form']);
        unset($_SESSION['order_coupon']);
        unset($_SESSION['pending_payment']);

        $DB->commit();

        logPayment('INFO', '결제 완료 (예약)', [
            'order_id' => $orderId,
            'rv_idx' => $rvId,
            'merchant_uid' => $merchant_uid
        ]);

        echo json_encode([
            'success' => true,
            'data'    => [
                'order_id'     => (int)$orderId,
                'rv_idx'       => (int)$rvId,
                'ot_number'    => $merchant_uid,
                'total_qty'    => $snap['total_qty'],
                'total_price'  => $snap['total_price'],
                'discount'     => $discount,
                'final_price'  => $finalPrice,
                'pay_status'   => 'PAID',
            ],
            'sql'     => $DB->getLastQuery(),
            'search'  => null
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        if ($DB && method_exists($DB, 'rollback')) $DB->rollback();
        logPayment('ERROR', '결제 검증 실패 (예약)', ['error' => $e->getMessage()]);
        echo json_encode([
            'success' => false,
            'data'    => [],
            'sql'     => $DB->getLastQuery(),
            'search'  => null,
            'message' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}
/* =========================================================
 * 추가 예약 후결제
 * ========================================================= */
elseif ($act === 'pay_postpaid_reservation') {
    try {
        $shopId = (int)($_SESSION['current_sh_idx'] ?? $_SESSION['cart_store_id'] ?? 0);
        if ($shopId <= 0) {
            throw new Exception('매장 정보가 없습니다.');
        }

        $snap = buildCartSnapshot($DB, $mt_idx, $shopId, $ct_ids);
        if ($snap['total_qty'] <= 0) {
            throw new Exception('장바구니가 비어 있습니다.');
        }

        $discount = 0;
        $cl_idx = null;

        if (!empty($_SESSION['order_coupon']['ct_idx'])) {
            $coupon_idx = (int)$_SESSION['order_coupon']['ct_idx'];
            $DB->where('idx', $coupon_idx);
            $DB->where('(sh_idx = 0 OR sh_idx = ?)', [$shopId]);
            $coupon = $DB->getOne('coupon_t');

            if ($coupon && isCouponValid($coupon, $today, $snap['total_price'], $mt_idx)) {
                $discount = calcCouponDiscount($coupon, $snap['total_price']);
                $cl_idx = $coupon_idx;
            } else {
                unset($_SESSION['order_coupon']);
            }
        }

        $rv_name   = trim($_POST['rv_name'] ?? ($_SESSION['reservation_form']['rv_name'] ?? ''));
        $rv_hp     = trim($_POST['rv_hp'] ?? ($_SESSION['reservation_form']['rv_hp'] ?? ''));
        $rv_date   = trim($_POST['rv_date'] ?? ($_SESSION['reservation_form']['rv_date'] ?? ''));
        $rv_time   = trim($_POST['rv_time'] ?? ($_SESSION['reservation_form']['rv_time'] ?? ''));
        $rv_people = (int)($_POST['rv_people'] ?? ($_SESSION['reservation_form']['rv_people'] ?? 1));
        $rv_memo   = trim($_POST['rv_memo'] ?? ($_SESSION['reservation_form']['rv_memo'] ?? ''));

        if ($rv_name === '') throw new Exception('예약자 이름을 입력해 주세요.');
        if ($rv_hp === '') throw new Exception('예약자 휴대폰번호를 입력해 주세요.');
        if ($rv_date === '') throw new Exception('예약 날짜를 선택해 주세요.');
        if ($rv_time === '') throw new Exception('예약 시간을 선택해 주세요.');

        $ot_notes = trim($_POST['ot_notes'] ?? '');
        $ot_notes = ($ot_notes !== '') ? mb_substr($ot_notes, 0, 2000) : null;

        $ot_number = generateOrderNumber($DB);
        $finalPrice = $snap['total_price'] - $discount;
        if ($finalPrice < 0) $finalPrice = 0;

        $ymd = $mt_idx < 0 ? date('YmdHis') : date('Ymd');
        $DB->where('sh_idx', $shopId);
        $DB->where('mt_idx', $mt_idx);
        $row = $DB->getOne('reservation_t', 'COUNT(*) as cnt');
        $rv_number = $ymd . $shopId . $mt_idx . ($row['cnt'] ?? 0);

        $DB->startTransaction();

        $rvId = $DB->insert('reservation_t', [
            'sh_idx'    => $shopId,
            'mt_idx'    => ($mt_idx > 0 ? $mt_idx : null),
            'rv_number' => $rv_number,
            'rv_name'   => mb_substr($rv_name, 0, 100),
            'rv_hp'     => mb_substr($rv_hp, 0, 20),
            'rv_date'   => $rv_date,
            'rv_time'   => $rv_time,
            'rv_people' => $rv_people,
            'rv_type'   => 'POSTPAID',
            'rv_status' => 'PENDING',
            'rv_memo'   => ($rv_memo !== '' ? mb_substr($rv_memo, 0, 2000) : null),
            'ot_idx'    => null,
            'rv_wdate'  => $DB->now(),
        ]);

        if (!$rvId) {
            throw new Exception('예약 저장 실패');
        }

        $orderId = createOrderReservationPostpaid(
            $DB,
            $mt_idx,
            $shopId,
            $ot_number,
            $snap,
            $discount,
            $cl_idx,
            $ot_notes,
            (int)$rvId
        );

        $DB->where('idx', (int)$rvId);
        $ok = $DB->update('reservation_t', [
            'ot_idx'   => (int)$orderId,
            'rv_udate' => $DB->now(),
        ]);

        if (!$ok) {
            throw new Exception('예약-주문 연결 실패');
        }

        if ($cl_idx > 0) {
            consumeCouponLog($DB, $cl_idx, $mt_idx);
        }

        clearCart($DB, $mt_idx, $shopId, $ct_ids, $snap['ct_idx_list']);

        unset($_SESSION['cart_store_id']);
        unset($_SESSION['reservation_form']);
        unset($_SESSION['order_coupon']);
        unset($_SESSION['pending_payment']);
        unset($_SESSION['order_mode']);

        $DB->commit();

        logPayment('INFO', '후결제 예약 완료', [
            'order_id' => $orderId,
            'rv_idx' => $rvId,
            'ot_number' => $ot_number
        ]);

        echo json_encode([
            'success' => true,
            'data'    => [
                'order_id'     => (int)$orderId,
                'rv_idx'       => (int)$rvId,
                'ot_number'    => $ot_number,
                'total_qty'    => $snap['total_qty'],
                'total_price'  => $snap['total_price'],
                'discount'     => $discount,
                'final_price'  => $finalPrice,
                'pay_type'     => 'POSTPAID',
                'pay_status'   => 'UNPAID',
            ],
            'sql'     => $DB->getLastQuery(),
            'search'  => null
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        if ($DB && method_exists($DB, 'rollback')) $DB->rollback();

        logPayment('ERROR', '후결제 예약 실패', [
            'error' => $e->getMessage()
        ]);

        echo json_encode([
            'success' => false,
            'data'    => [],
            'sql'     => $DB->getLastQuery(),
            'search'  => null,
            'message' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}
/* =========================================================
 * 9. 후결제 주문 (테스트용)
 * ========================================================= */
elseif ($act === 'pay_postpaid') {
    try {
        if ($qrShopId <= 0 || $qrTableNo === '') {
            throw new Exception('테이블(QR) 정보가 없습니다.');
        }

        $visitId = ensureVisit($DB, $mt_idx, $qrShopId, $qrTableNo, $VISIT_TBL, $VISIT_COLS);

        $snap = buildCartSnapshot($DB, $mt_idx, $st_id, $ct_ids);
        if ($snap['total_qty'] <= 0) {
            throw new Exception('장바구니가 비어 있습니다.');
        }

        $discount = 0;
        $cl_idx = null;

        if (!empty($_SESSION['order_coupon']['ct_idx'])) {
            $coupon_idx = (int)$_SESSION['order_coupon']['ct_idx'];
            $DB->where('idx', $coupon_idx);
            $DB->where('(sh_idx = 0 OR sh_idx = ?)', [$st_id]);
            $coupon = $DB->getOne('coupon_t');

            if ($coupon && isCouponValid($coupon, $today, $snap['total_price'], $mt_idx)) {
                $discount = calcCouponDiscount($coupon, $snap['total_price']);
                $cl_idx = $coupon_idx;
            } else {
                unset($_SESSION['order_coupon']);
            }
        }

        $ot_number = generateOrderNumber($DB);
        $ot_notes = trim($_POST['ot_notes'] ?? '');
        $ot_notes = ($ot_notes !== '') ? mb_substr($ot_notes, 0, 2000) : null;
        $ot_table = ($qrTableNo !== '') ? $qrTableNo : null;

        $DB->startTransaction();

        $orderId = createOrderPostpaid(
            $DB, $mt_idx, $st_id, $ot_number, $snap, $discount, $cl_idx,
            $ot_notes, $visitId, $ot_table
        );

        clearCart($DB, $mt_idx, $st_id, $ct_ids, $snap['ct_idx_list']);
        unset($_SESSION['cart_store_id']);
        unset($_SESSION['order_mode']);
        $_SESSION['is_qr_order'] = true;
        $DB->commit();

        logPayment('INFO', '후결제 주문 완료', [
            'order_id' => $orderId,
            'ot_number' => $ot_number
        ]);

        echo json_encode([
            'success' => true,
            'data'    => [
                'order_id'     => (int)$orderId,
                'ot_number'    => $ot_number,
                'visit_id'     => (int)$visitId,
                'total_qty'    => $snap['total_qty'],
                'total_price'  => $snap['total_price'],
                'discount'     => $discount,
                'final_price'  => $snap['total_price'] - $discount,
                'pay_type'     => 'POSTPAID',
                'pay_status'   => 'UNPAID',
            ],
            'sql'     => $DB->getLastQuery(),
            'search'  => null
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        if ($DB && method_exists($DB, 'rollback')) $DB->rollback();
        logPayment('ERROR', '후결제 주문 실패', ['error' => $e->getMessage()]);
        echo json_encode([
            'success' => false,
            'data'    => [],
            'sql'     => $DB->getLastQuery(),
            'search'  => null,
            'message' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}
/* =========================================================
 * 10. 주문내역/취소내역 리스트 (무한스크롤)
 * act: order_history_list
 * ========================================================= */
elseif ($act === 'order_history_list') {
    try {
        $tab      = ($_POST['tab'] ?? 'order') === 'cancel' ? 'cancel' : 'order';
        $page     = max(1, (int)($_POST['page'] ?? 1));
        $limit    = 10;
        $offset   = ($page - 1) * $limit;

        $kind     = trim((string)($_POST['kind'] ?? ''));
        $search   = trim((string)($_POST['search'] ?? ''));
        $dateFrom = trim((string)($_POST['date_from'] ?? ''));
        $dateTo   = trim((string)($_POST['date_to'] ?? ''));

        if ($mt_idx <= 0) {
            throw new Exception('로그인이 필요합니다.');
        }

        $data = [];
        $hasMore = false;

        if($kind === 'reservation' || $kind === '') {
            // ──────────────────────────────────────────────
            // 1. 예약 내역 (VISIT + PREPAID)
            // ──────────────────────────────────────────────
            $DB->where('r.mt_idx', $mt_idx);

            if ($tab === 'cancel') {
                $DB->where('r.rv_status', 'CANCELLED');
            } else {
                $DB->where('r.rv_status', ['PENDING', 'CONFIRMED', 'ARRIVED'], 'IN');
            }

            $DB->join('shop_t s', 's.idx = r.sh_idx', 'LEFT');
            $DB->join('orders_t o', 'o.idx = r.ot_idx', 'LEFT');

            // kind 필터
            if ($kind === 'qr' || $kind === 'takeout') {
                $DB->where('r.sh_idx', -1); // 예약 제외
            }

            // 검색어
            if ($search !== '') {
                $DB->where("(s.sh_title LIKE ? OR r.rv_number LIKE ? OR r.rv_name LIKE ?)",
                    ["%{$search}%", "%{$search}%", "%{$search}%"]);
            }

            // 기간 필터
            if ($dateFrom !== '' && $dateTo !== '') {
                $DB->where("r.rv_date BETWEEN ? AND ?", [$dateFrom, $dateTo]);
            } elseif ($dateFrom !== '') {
                $DB->where("r.rv_date >= ?", [$dateFrom]);
            } elseif ($dateTo !== '') {
                $DB->where("r.rv_date <= ?", [$dateTo]);
            }

            $DB->orderBy('r.rv_wdate', 'DESC');

            $reservations = $DB->get('reservation_t r', [$offset, $limit + 1], [
                'r.idx AS rv_idx',
                'r.rv_type',
                'r.rv_status',
                'r.rv_date',
                'r.rv_time',
                'r.rv_people',
                'r.rv_name',
                'r.rv_hp',
                'r.rv_number',
                'r.sh_idx',
                'r.ot_idx',
                'r.rv_wdate',
                's.sh_title',
                's.sh_branch_nm',
                's.sh_img1',
                'o.ot_total_price',
                'o.ct_snapshot',
                'o.ot_number',
                'o.ot_status',
                'o.idx AS ot_idx',
            ]);

            $totalReservations = count($reservations);
            $hasMore = $totalReservations > $limit;
            if ($hasMore) array_pop($reservations);

            foreach ($reservations as $r) {
                $shop = getShopInfo($DB, (int)$r['sh_idx']);

                $rvType = strtoupper((string)($r['rv_type'] ?? ''));
                $prepaid = $rvType === 'PREPAID';
                $postpaid = $rvType === 'POSTPAID';
                $hasOrder = (int)($r['ot_idx'] ?? 0) > 0;

                $rows = [
                    ['label' => '예약일시', 'value' => date('Y.m.d H:i', strtotime($r['rv_date'] . ' ' . $r['rv_time']))],
                    ['label' => '예약자', 'value' => $r['rv_name'] . '(' . format_phone($r['rv_hp']) . ')'],
                    ['label' => '예약인원', 'value' => $r['rv_people'] . '명'],
                ];

                $payment = '현장결제';
                $menuText = '현장 주문 예정';

                if ($hasOrder) {
                    $snap = json_decode($r['ct_snapshot'] ?? '{}', true);

                    if (!empty($snap['items'])) {
                        $first = $snap['items'][0]['menu_name'] ?? '메뉴';
                        $cnt = count($snap['items']);
                        $menuText = $cnt > 1 ? $first . ' 외 ' . ($cnt - 1) . '개' : $first;
                    } else {
                        $menuText = '주문 메뉴';
                    }

                    if ($prepaid) {
                        $payment = number_format((int)$r['ot_total_price']) . '원';
                    } elseif ($postpaid) {
                        $payment = '후결제';
                    }
                }

                $rows[] = ['label' => '주문메뉴', 'value' => $menuText, 'dd_class' => 'line1_text'];

                $badge = ['color_class' => 'blue', 'icon' => 'ic_calendar', 'text' => '예약'];

                if ($tab === 'cancel') {
                    $statusText = '예약취소';
                    $statusColor = 'text-danger';
                } else {
                    if ($prepaid && $hasOrder) {
                        $statusText = '예약 주문 완료';
                        $statusColor = 'text-success';
                    } elseif ($postpaid && $hasOrder) {
                        $statusText = '예약 주문 접수';
                        $statusColor = 't_blue';
                    } elseif ($prepaid) {
                        $statusText = '예약요청 (결제대기)';
                        $statusColor = 'text-warning';
                    } else {
                        $statusText = $r['rv_status'] === 'PENDING' ? '예약요청' : '예약완료';
                        $statusColor = 't_blue';
                    }
                }

                $detailUrl = $hasOrder && !empty($r['ot_number'])
                    ? '../rsrv/rsrv_history.php?ot_number=' . $r['ot_number']
                    : '../rsrv/rsrv_history.php?rv_idx=' . $r['rv_idx'];

                $data[] = [
                    'kind' => 'reservation',
                    'sh_idx' => (int)$r['sh_idx'],
                    'badge' => $badge,
                    'status_text' => $statusText,
                    'status_color_class' => $statusColor,
                    'store_name' => $shop['full'],
                    'thumb' => '/data/shop/' . $r['sh_idx'] . '/rs_' . $shop['img'],
                    'code_label' => '예약번호',
                    'code_text' => $r['rv_number'] ?? 'No.' . str_pad($r['rv_idx'], 8, '0', STR_PAD_LEFT),
                    'code_date' => date('y.m.d H:i', strtotime($r['rv_date'] . ' ' . $r['rv_time'])),
                    'rows' => $rows,
                    'payment' => $payment,
                    'detail_url' => $detailUrl,
                    'sort_date' => $r['rv_wdate'],
                    'status' => $r['ot_status'],
                    'ot_idx' => $r['ot_idx'],
                ];
            }
        }
        // ──────────────────────────────────────────────
        // 2. QR 테이블 주문 (tv_idx 그룹화)
        // ──────────────────────────────────────────────
        if ($kind === 'qr' || $kind === '') {  // 포장만 볼 때는 QR 조회 생략
            $DB->where('o.mt_idx', $mt_idx);
            $DB->where('o.rv_idx', null, 'IS');
            $DB->where('o.tv_idx', null, 'IS NOT'); // QR만

            if ($tab === 'cancel') {
                $DB->where('o.ot_status', 'CANCELLED');
            } else {
                $DB->where('o.ot_status', 'CANCELLED', '!=');
            }
            $DB->join('shop_t s', 's.idx = o.sh_idx', 'LEFT');
            if ($search !== '') {
                $DB->where("(s.sh_title LIKE ? OR o.ot_number LIKE ?)", ["%{$search}%", "%{$search}%"]);
            }

            if ($dateFrom !== '' && $dateTo !== '') {
                $DB->where("DATE(o.ot_wdate) BETWEEN ? AND ?", [$dateFrom, $dateTo]);
            } elseif ($dateFrom !== '') {
                $DB->where("DATE(o.ot_wdate) >= ?", [$dateFrom]);
            } elseif ($dateTo !== '') {
                $DB->where("DATE(o.ot_wdate) <= ?", [$dateTo]);
            }

            // 그룹화 핵심
            $DB->groupBy('o.tv_idx');
            $DB->orderBy('MAX(o.ot_wdate)', 'DESC');

            $qrGroups = $DB->get('orders_t o', [$offset, $limit + 1], [
                'o.tv_idx',
                'MAX(o.idx) AS latest_idx',
                'MAX(o.ot_wdate) AS latest_wdate',
                'MAX(o.ot_number) AS latest_number',
                'MAX(o.ot_total_price) AS latest_total',
                'MAX(o.ct_snapshot) AS latest_snapshot',
                'MAX(o.sh_idx) AS sh_idx',
                'MAX(o.ot_table) AS ot_table',
                'COUNT(o.idx) AS order_count',
                'SUBSTRING_INDEX(GROUP_CONCAT(o.ot_status ORDER BY o.ot_wdate DESC, o.idx DESC), ",", 1) AS ot_status',
                'MAX(o.idx) AS ot_idx',
            ]);

            $hasMore = $hasMore || count($qrGroups) > $limit;
            if ($hasMore) array_pop($qrGroups);

            foreach ($qrGroups as $g) {
                $shop = getShopInfo($DB, (int)$g['sh_idx']);
                $isGroup = (int)$g['order_count'] > 1;

                $snap = json_decode($g['latest_snapshot'] ?? '{}', true);
                $menuText = '주문 메뉴';
                if (!empty($snap['items'])) {
                    $first = $snap['items'][0]['menu_name'] ?? '메뉴';
                    $cnt = count($snap['items']);
                    $menuText = $cnt > 1 ? $first . ' 외 ' . ($cnt - 1) . '개' : $first;
                }
                if ($isGroup) {
                    $menuText .= ' (총 ' . $g['order_count'] . '건)';
                }

                $rows = [
                    ['label' => '테이블', 'value' => $g['ot_table'] ?: '테이블 번호 없음'],
                ];
                if ($isGroup) {
                    $rows[] = ['label' => '최근 주문', 'value' => date('Y.m.d H:i', strtotime($g['latest_wdate']))];
                }
                $rows[] = ['label' => null, 'value' => $menuText];

                $statusText = $tab === 'cancel' ? 'QR주문 취소' : 'QR주문';
                $statusColor = $tab === 'cancel' ? 'text-danger' : 't_green';

                $data[] = [
                    'kind'               => 'qr',
                    'sh_idx'             => (int)$g['sh_idx'],
                    'badge'              => ['color_class' => '', 'icon' => 'ic_qr', 'text' => 'QR주문'],
                    'status_text'        => $statusText,
                    'status_color_class' => $statusColor,
                    'store_name'         => $shop['full'],
                    'thumb'              => '/data/shop/'.$g['sh_idx'].'/rs_'.$shop['img'],
                    'code_label'         => '테이블',
                    'code_text'          => $g['ot_table'] ?: '테이블 번호 없음',
                    'code_date'          => date('y.m.d H:i', strtotime($g['latest_wdate'])),
                    'rows'               => $rows,
                    'payment'            => number_format((int)$g['latest_total']) . '원' . ($isGroup ? ' (후결제)' : ''),
                    'detail_url'         => '../order/order_guest.php?tv_idx=' . $g['tv_idx'],
                    'sort_date'          => $g['latest_wdate'],
                    'status'             => $g['ot_status'],
                    'ot_idx'             => $g['ot_idx'],
                ];
            }
        }

        // ──────────────────────────────────────────────
        // 3. 포장 주문 (개별 출력)
        // ──────────────────────────────────────────────
        if ($kind === 'takeout' || $kind === '') {  // QR만 볼 때는 포장 생략
            $DB->where('o.mt_idx', $mt_idx);
            $DB->where('o.rv_idx', null, 'IS');
            $DB->where('o.tv_idx', null, 'IS');  // 포장만

            if ($tab === 'cancel') {
                $DB->where('o.ot_status', 'CANCELLED');
            } else {
                $DB->where('o.ot_status', 'CANCELLED', '!=');
            }
            $DB->join('shop_t s', 's.idx = o.sh_idx', 'LEFT');
            if ($search !== '') {
                $DB->where("(s.sh_title LIKE ? OR o.ot_number LIKE ?)", ["%{$search}%", "%{$search}%"]);
            }

            if ($dateFrom !== '' && $dateTo !== '') {
                $DB->where("DATE(o.ot_wdate) BETWEEN ? AND ?", [$dateFrom, $dateTo]);
            } elseif ($dateFrom !== '') {
                $DB->where("DATE(o.ot_wdate) >= ?", [$dateFrom]);
            } elseif ($dateTo !== '') {
                $DB->where("DATE(o.ot_wdate) <= ?", [$dateTo]);
            }

            $DB->orderBy('o.ot_wdate', 'DESC');

            $takeoutOrders = $DB->get('orders_t o', [$offset, $limit + 1], [
                'o.idx AS ot_idx',
                'o.ot_number',
                'o.ot_wdate',
                'o.ot_total_price',
                'o.sh_idx',
                'o.ct_snapshot',
                's.sh_title',
                's.sh_branch_nm',
                's.sh_img1',
                'o.ot_status'
            ]);

            $hasMore = $hasMore || count($takeoutOrders) > $limit;
            if (count($takeoutOrders) > $limit) array_pop($takeoutOrders);

            foreach ($takeoutOrders as $o) {
                $shop = getShopInfo($DB, (int)$o['sh_idx']);

                $snap = json_decode($o['ct_snapshot'] ?? '{}', true);
                $menuText = '주문 메뉴';
                if (!empty($snap['items'])) {
                    $first = $snap['items'][0]['menu_name'] ?? '메뉴';
                    $cnt = count($snap['items']);
                    $menuText = $cnt > 1 ? $first . ' 외 ' . ($cnt - 1) . '개' : $first;
                }

                $rows = [
                    ['label' => null, 'value' => $menuText]
                ];

                $statusText = $tab === 'cancel' ? '포장 취소' : '포장 요청';
                $statusColor = $tab === 'cancel' ? 'text-danger' : 't_green';

                $data[] = [
                    'kind'               => 'takeout',
                    'sh_idx'             => (int)$o['sh_idx'],
                    'badge'              => ['color_class' => 'green', 'icon' => 'ic_pack', 'text' => '포장'],
                    'status_text'        => $statusText,
                    'status_color_class' => $statusColor,
                    'store_name'         => $shop['full'],
                    'thumb'              => '/data/shop/'.$o['sh_idx'].'/rs_'.$shop['img'],
                    'code_label'         => '주문번호',
                    'code_text'          => $o['ot_number'],
                    'code_date'          => date('y.m.d', strtotime($o['ot_wdate'])),
                    'rows'               => $rows,
                    'payment'            => number_format((int)$o['ot_total_price']) . '원',
                    'detail_url'         => '../rsrv/rsrv_history.php?ot_number=' . $o['ot_number'],
                    'sort_date'          => $o['ot_wdate'],
                    'status'             => $o['ot_status'],
                    'ot_idx'             => $o['ot_idx'],
                ];
            }
        }

        // 최신순 정렬
        usort($data, function($a, $b) {
            return strtotime($b['sort_date'] ?? '1970-01-01') - strtotime($a['sort_date'] ?? '1970-01-01');
        });

        $reviewMap = [];
        $otIdxList = [];

        foreach ($data as $item) {
            $otIdx = (int)($item['ot_idx'] ?? 0);
            if ($otIdx > 0) {
                $otIdxList[$otIdx] = $otIdx;
            }
        }

        if (!empty($otIdxList)) {
            $DB->where('ot_idx', array_values($otIdxList), 'IN');
            $reviewRows = $DB->get('review_t', null, ['ot_idx']);

            foreach ((array)$reviewRows as $reviewRow) {
                $reviewMap[(int)$reviewRow['ot_idx']] = true;
            }
        }

        foreach ($data as &$item) {
            $otIdx = (int)($item['ot_idx'] ?? 0);
            $status = strtoupper((string)($item['status'] ?? ''));
            $hasReview = $otIdx > 0 && !empty($reviewMap[$otIdx]);

            $item['has_review'] = $hasReview ? 'Y' : 'N';
            $item['can_write_review'] = ($status === 'COMPLETED' && !$hasReview);
        }
        unset($item);

        echo json_encode([
            'success' => true,
            'data' => $data,
            'hasMore' => $hasMore
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}
/* =========================================================
 * 기타: 알 수 없는 요청
 * ========================================================= */
else {
    logPayment('WARNING', '알 수 없는 요청', ['act' => $act]);
    echo json_encode([
        'success' => false,
        'data'    => [],
        'sql'     => '',
        'search'  => null,
        'message' => '알 수 없는 요청입니다.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
