<?php
/**
 * 포트원 결제 콜백 전용 최소화 페이지 (서버에서 직접 처리)
 * - UI 전부 제거
 * - 포트원 결제 결과 재조회 + 검증 + 주문 생성 + 완료 페이지 이동
 * - ot_number 중복 시 재생성 또는 스킵 처리
 */

include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
require_once $_SERVER['DOCUMENT_ROOT']."/cfg/print.inc.php";

// =============================================
// 상수 및 전역 설정
// =============================================
$VISIT_TBL = 'table_visit_t';
$sessionMode = $_SESSION['order_mode'];
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

define('PORTONE_API_URL', 'https://api.portone.io'); // V2 호스트

// =============================================
// 헬퍼 함수들 (모두 포함)
// =============================================

function logPayment($level, $message, $context = []) {
    $safeContext = $context;
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

function savePaymentRecord($DB, $ot_idx, $sh_idx, $mt_idx, $merchant_uid, $paymentData) {
    $amount = (float)($paymentData['amount']['total'] ?? 0);
    $status = $paymentData['status'] ?? 'READY';

    $DB->where('idx', $sh_idx);
    $sh_name = $DB->getOne('shop_t', 'sh_title');

    $DB->where('idx', $mt_idx);
    $md = $DB->getOne('member_t', 'mt_name, mt_hp, mt_email');

    $dbStatus = 'READY';
    switch (strtoupper($status)) {
        case 'PAID': $dbStatus = 'PAID'; break;
        case 'FAILED': $dbStatus = 'FAILED'; break;
        case 'CANCELLED': $dbStatus = 'CANCELLED'; break;
        case 'PARTIAL_CANCELLED': $dbStatus = 'PARTIAL_CANCELLED'; break;
    }

    $paidAt = isset($paymentData['paid_at']) ? date('Y-m-d H:i:s', strtotime($paymentData['paid_at'])) : null;
    $cancelledAt = isset($paymentData['cancelled_at']) ? date('Y-m-d H:i:s', strtotime($paymentData['cancelled_at'])) : null;

    $DB->where('merchant_uid', $merchant_uid);
    $existing = $DB->getOne('payments_t', ['idx']);

    if ($existing) {
        $DB->where('idx', $existing['idx']);
        $updateResult = $DB->update('payments_t', [
            'imp_uid' => $paymentData['id'] ?? null,
            'status' => $dbStatus,
            'paid_at' => $paidAt,
            'cancelled_at' => $cancelledAt,
            'pg_payload' => json_encode($paymentData, JSON_UNESCAPED_UNICODE),
            'updated_at' => $DB->now(),
        ]);

        if (!$updateResult) throw new Exception('결제 내역 업데이트 실패');

        logPayment('INFO', '결제 내역 업데이트', ['merchant_uid' => $merchant_uid]);
        return $existing['idx'];
    }

    $paymentId = $DB->insert('payments_t', [
        'ot_idx' => $ot_idx,
        'sh_idx' => $sh_idx,
        'sh_name' => $sh_name['sh_title'] ?? '매장',
        'mt_idx' => ($mt_idx > 0 ? $mt_idx : null),
        'mt_name'  => $md['mt_name'] ?? '비회원',
        'mt_hp'    => $md['mt_hp'] ?? '010-0000-0000',
        'mt_email' => $md['mt_email'] ?? 'guest@qrorder.com',
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

    if (!$paymentId) throw new Exception('결제 내역 저장 실패: ' . $DB->getLastError());

    logPayment('INFO', '결제 내역 저장', ['merchant_uid' => $merchant_uid, 'payment_id' => $paymentId]);
    return $paymentId;
}

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

function calcCouponDiscount($coupon, $totalPrice) {
    $type2 = (int)($coupon['ct_type2'] ?? 1);
    $val   = (int)($coupon['ct_discount1'] ?? 0);

    $disc = ($type2 === 2) ? floor($totalPrice * ($val / 100)) : $val;
    return max(0, min($disc, $totalPrice));
}

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

function getVisitToken() {
    return $_SESSION['visit_token'] ?? $_COOKIE['visit_token'] ?? '';
}

function setVisitToken($token) {
    $_SESSION['visit_token'] = $token;
    setcookie('visit_token', $token, time() + 86400 * 30, '/', '', false, true);
}

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

/* =========================================================
 * 메인 로직 시작
 * ========================================================= */
$paymentId   = $_GET['paymentId']   ?? '';
$impUid      = $_GET['imp_uid']     ?? '';
$pending = $_SESSION['pending_payment'] ?? [];
$merchant_uid = $pending['merchant_uid'] ?? '';
$mt_idx = (int)($_SESSION['mng']['mt_idx'] ?? 0);
$st_id  = (int)($_SESSION['cart_store_id'] ?? 0);
$ct_ids = array_values(array_filter(array_map('intval', $_SESSION['cart_ct_ids'] ?? [])));

// 포트원 파라미터 검증
if (empty($paymentId) && empty($impUid)) {
    http_response_code(400);
    echo "<!DOCTYPE html><html lang='ko'><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width, initial-scale=1.0'><title>잘못된 접근</title></head><body>";
    echo "<div style='text-align:center; padding:100px;'><h1>잘못된 접근입니다</h1><p>결제 정보가 누락되었습니다.</p></div>";
    echo "</body></html>";
    exit;
}

// 세션 pending 확인
if (empty($pending) || empty($pending['merchant_uid'])) {
    http_response_code(400);
    die("결제 세션이 유효하지 않습니다.");
}

// =============================================
// 1. 포트원 결제 정보 재조회
// =============================================
try {
    $paymentData = getPortonePayment($paymentId ?: $impUid);

    if ($paymentData['status'] !== 'PAID') {
        throw new Exception('결제가 완료되지 않았습니다. 상태: ' . ($paymentData['status'] ?? 'UNKNOWN'));
    }

    $snap = buildCartSnapshot($DB, $mt_idx, $st_id, $ct_ids);
    $expectedAmount = $snap['total_price'] - ($pending['discount'] ?? 0);
    $paidAmount = (float)($paymentData['amount']['total'] ?? 0);

    if (abs($expectedAmount - $paidAmount) > 0.01) {
        logPayment('ERROR', '금액 불일치', [
            'expected' => $expectedAmount,
            'paid' => $paidAmount,
            'merchant_uid' => $merchant_uid
        ]);
        throw new Exception('결제 금액이 일치하지 않습니다.');
    }

} catch (Exception $e) {
    http_response_code(500);
    redirectBackOnly($e->getMessage());
}

// =============================================
// 2. 주문 생성 및 후처리 (중복 방지 강화)
// =============================================
try {
    $DB->startTransaction();

    $discount    = (int)($pending['discount'] ?? 0);
    $cl_idx      = !empty($_SESSION['order_coupon']['ct_idx']) ? (int)$_SESSION['order_coupon']['ct_idx'] : null;
    $ot_notes    = trim($_POST['ot_notes'] ?? '');
    $ot_notes    = $ot_notes ? mb_substr($ot_notes, 0, 2000) : null;

    // 스냅샷 생성
    $snap = buildCartSnapshot($DB, $mt_idx, $st_id, $ct_ids);
    if ($snap['total_qty'] <= 0) {
        throw new Exception('장바구니가 비어 있습니다.');
    }

    // QR/테이블 정보
    list($qrShopId, $qrTableNo) = parseTableFromSession($DB);
    echo 'ee'.$qrTableNo.'ww';
    $ot_table = ($qrTableNo !== '') ? $qrTableNo : null;
    $visitId  = null;
    if ($qrShopId > 0 && $qrTableNo !== '') {
        $visitId = ensureVisit($DB, $mt_idx, $qrShopId, $qrTableNo, $VISIT_TBL, $VISIT_COLS);
    }

    $rv_idx = null;

    // 예약 모드일 경우 → reservation_t 먼저 생성
    if ($sessionMode === 'reservation') {
        $reservation_form = $_SESSION['reservation_form'] ?? [];

        $rv_name   = trim($reservation_form['rv_name'] ?? '');
        $rv_hp     = trim($reservation_form['rv_hp'] ?? '');
        $rv_date   = trim($reservation_form['rv_date'] ?? '');
        $rv_time   = trim($reservation_form['rv_time'] ?? '');
        $rv_people = (int)($reservation_form['rv_people'] ?? 1);
        $rv_memo   = trim($reservation_form['rv_memo'] ?? '');

        if (empty($rv_name) || empty($rv_hp) || empty($rv_date) || empty($rv_time)) {
            throw new Exception('예약 정보가 누락되었습니다.');
        }

        $ymd = date('YmdHis');
        $rv_number = $ymd . $st_id . $mt_idx; // 고유 예약번호 생성

        $rvId = $DB->insert('reservation_t', [
            'sh_idx'    => $st_id,
            'mt_idx'    => ($mt_idx > 0 ? $mt_idx : null),
            'rv_number' => $rv_number,
            'rv_name'   => mb_substr($rv_name, 0, 100),
            'rv_hp'     => mb_substr($rv_hp, 0, 20),
            'rv_date'   => $rv_date,
            'rv_time'   => $rv_time,
            'rv_people' => $rv_people,
            'rv_type'   => 'PREPAID',
            'rv_status' => 'PENDING',
            'rv_memo'   => mb_substr($rv_memo, 0, 2000),
            'ot_idx'    => null, // 나중에 업데이트
            'rv_wdate'  => $DB->now(),
        ]);

        if (!$rvId) throw new Exception('예약 정보 저장 실패: ' . $DB->getLastError());

        $rv_idx = (int)$rvId;
    }

    // 주문 생성 (중복 시 ot_number 재생성)
    $ot_number = $merchant_uid;
    $maxRetries = 5;
    $retryCount = 0;

    while ($retryCount < $maxRetries) {
        try {
            $orderId = createOrderCommon(
                $DB, $mt_idx, $st_id, $ot_number, $snap, $discount, $cl_idx,
                $ot_notes, $rv_idx, $visitId, $ot_table
            );

             if ($sessionMode === 'reservation') {
                 $DB->where('idx', $rv_idx);
                 $DB->update('reservation_t', ['ot_idx' => $orderId]);
             }
            break; // 성공하면 루프 탈출
        } catch (Exception $e) {
            if ($DB->getLastErrno() === 1062 && strpos($DB->getLastError(), 'ot_number') !== false) {
                $retryCount++;
                $ot_number = generateOrderNumber($DB);
                logPayment('WARNING', 'ot_number 중복 → 재생성', ['retry' => $retryCount, 'new_ot_number' => $ot_number]);
                continue;
            }
            throw $e;
        }
    }

    if ($retryCount >= $maxRetries) {
        throw new Exception('주문번호 생성 실패: 중복 재시도 횟수 초과');
    }

    // 결제 상태 업데이트
    $DB->where('idx', $orderId);
    $DB->update('orders_t', [
        'ot_pay_status' => 'PAID',
        'ot_pay_date' => $DB->now(),
    ]);

    // 결제 내역 저장
    savePaymentRecord($DB, $orderId, $st_id, $mt_idx, $merchant_uid, $paymentData);

    // 쿠폰 사용 로그
    if ($cl_idx > 0) {
        consumeCouponLog($DB, $cl_idx, $mt_idx);
    }

    // 장바구니 비우기
    clearCart($DB, $mt_idx, $st_id, $ct_ids, $snap['ct_idx_list']);
    // 세션 정리 (재호출 방지)
    unset($_SESSION['pending_payment']);
    unset($_SESSION['order_coupon']);
    unset($_SESSION['cart_store_id']);
    unset($_SESSION['cart_ct_ids']);
    unset($_SESSION['reservation_form']);
//    unset($_SESSION['qr_token']);
    if($_SESSION['qr_token']){
        unset($_SESSION['order_mode']);
        $_SESSION['is_qr_order'] = true;
    } else{
        unset($_SESSION['qr_token']);
        $_SESSION['is_qr_order'] = false;
    }
    $DB->commit();

    logPayment('INFO', '콜백 결제 완료', [
        'order_id' => $orderId,
        'ot_number' => $ot_number,
        'mode' => $sessionMode
    ]);

    // 완료 페이지로 이동
    $redirectUrl = ($sessionMode === 'takeout' || $sessionMode === 'reservation')
        ? '../rsrv/rsrv_cmp.php?ot_number=' . urlencode($ot_number)
        : '../order/order_cmp.php?ot_number=' . urlencode($ot_number);

    header("Location: $redirectUrl");
    exit;

} catch (Exception $e) {
    if ($DB && method_exists($DB, 'rollback')) $DB->rollback();
    logPayment('ERROR', '콜백 결제 처리 실패', ['error' => $e->getMessage()]);
    http_response_code(500);
    redirectBackOnly($e->getMessage());
}

function redirectBackOnly($message = '') {
    echo "<!DOCTYPE html>";
    echo "<html lang='ko'>";
    echo "<head>";
    echo "<meta charset='UTF-8'>";
    echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
    echo "<title>이동 중</title>";
    echo "</head>";
    echo "<body>";
    echo "<script>";
    echo "console.log('[PORTONE CALLBACK] history.back() 실행');";

    if ($message !== '') {
        echo "alert(" . json_encode($message, JSON_UNESCAPED_UNICODE) . ");";
    }

    echo "location.replace('../order/order.php');";
//    echo "history.go(-3);";
    echo "</script>";
    echo "</body>";
    echo "</html>";
    exit;
}
?>
