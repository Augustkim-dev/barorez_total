<?php
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
$_ENABLED_INC_TOP = true;
$_ENABLED_INC_QUICK = true;
//$_SUB_HEAD_TITLE = "로그인";
include_once $_SERVER['DOCUMENT_ROOT']."/app/inc/head.php";
$_SUB_HEAD_TITLE = "주문/결제"; //헤더에 타이틀명이 없을경우 공백
$hd_num = 2; //모바일 hd 1~n까지 있음
$_GET['bt_menu'] = ''; //모바일 하단메뉴 있음1, 없음 공백
include_once $_SERVER['DOCUMENT_ROOT']."/app/inc/header.php";

$mt_idx = (int)($_SESSION['mng']['mt_idx'] ?? 0);
$st_id  = (int)($_SESSION['cart_store_id'] ?? 0);

$cartRows = [];
$optionsMap = [];
$totalQty = 0;
$totalPrice = 0;

$shopRow = null;
$shopImg = DESIGN_HTTP.'/img/pr_sample01.jpg'; // fallback

// ------------------------------
// 1) 매장 정보
// ------------------------------
if ($st_id > 0) {
    $DB->where('idx', $st_id);
    $shopRow = $DB->getOne('shop_t'); // shop_t 구조가 프로젝트에 있다고 가정
    if (!empty($shopRow['sh_img1'])) $shopImg =  $shop_img = "/data/shop/{$st_id}/rs_{$shopRow['sh_img1']}";
    $shopType = $shopRow['sh_qr_pay_type'] === 'PREPAY';
    $shopResType = $shopRow['sh_reserve_pay_type'] === 'PREPAY';
}

// ------------------------------
// 2) 장바구니 목록 로드 (회원/비회원 분기)
// ------------------------------
$ct_ids = $_SESSION['cart_ct_ids'] ?? [];
$ct_ids = array_values(array_filter(array_map('intval', (array)$ct_ids)));

if ($st_id > 0) {
    if ($mt_idx > 0) {
        $DB->where('c.mt_idx', $mt_idx);
        $DB->where('c.st_id', $st_id);
    } else {
        if (!empty($ct_ids)) {
            $DB->where('c.idx', $ct_ids, 'IN');
            $DB->where('c.st_id', $st_id);
        } else {
            $cartRows = [];
        }
    }

    if ($mt_idx > 0 || !empty($ct_ids)) {
        $DB->join('shop_menu_t m', 'c.sm_id = m.idx', 'INNER');
        $DB->orderBy('c.idx', 'DESC');

        $cartRows = $DB->get('cart_t c', null, [
            'c.idx as ct_idx',
            'c.st_id',
            'c.sm_id',
            'c.ct_quantity',
            'c.ct_price',
            'c.ct_total_price',
            'm.sm_title',
            'm.sm_image',
            'm.sm_contents',
            'm.sm_price',
            'm.sm_su',
            'm.sm_type',
            'm.sm_show',
        ]);
    }
}

// ------------------------------
// 3) 옵션 로드
// ------------------------------
$cartCtIdxs = [];
foreach ($cartRows as $r) {
    $cartCtIdxs[] = (int)$r['ct_idx'];
    $totalQty += max(1, (int)$r['ct_quantity']);
    $totalPrice += (int)$r['ct_total_price'];
}

if (!empty($cartCtIdxs)) {
    $DB->join('menu_option_category_t oc', 'co.oc_idx = oc.idx', 'LEFT');
    $DB->where('co.ct_idx', $cartCtIdxs, 'IN');
    $DB->orderBy('oc.oc_order', 'ASC');
    $DB->orderBy('co.idx', 'ASC');
    $optRows = $DB->get('cart_options_t co', null, [
        'co.ct_idx',
        'co.oc_idx',
        'co.om_idx',
        'co.co_option_name',
        'co.co_option_price',
        'oc.oc_title',
    ]);

    foreach ($optRows as $o) {
        $ct = (int)$o['ct_idx'];
        if (!isset($optionsMap[$ct])) $optionsMap[$ct] = [];
        $optionsMap[$ct][] = $o;
    }
}

// ------------------------------
// 4) 쿠폰 목록 (coupon_t 기반, 매장 쿠폰 예시)
// - 프로젝트에 회원별 쿠폰 로그 테이블이 있다면 그걸로 교체 가능
// ------------------------------
$today = date('Y-m-d');
$coupons = [];

if ($st_id > 0 && $_SESSION['mng']) {
    $DB->where('(sh_idx = 0 OR sh_idx = ?)', [$st_id]);
    $DB->where('ct_show', 'Y');
    $DB->where('ct_del_yn', 'N');
    $DB->where('ct_discount3', $totalPrice, '<=');

    // 기간 유효성
    $DB->where('(
        (ct_type1 = 1 AND (ct_sdate IS NULL OR ct_sdate <= ?) AND (ct_edate IS NULL OR ct_edate >= ?))
        OR
        (ct_type1 = 2 AND (ct_days IS NOT NULL AND ct_days >= 0))
    )', [$today, $today]);

    $DB->orderBy('ct_order', 'ASC');
    $DB->orderBy('idx', 'DESC');

    $allCoupons = $DB->get('coupon_t', null, [
        'idx',
        'ct_title',
        'ct_type1',
        'ct_type2',
        'ct_discount1',
        'ct_discount3',
        'ct_sdate',
        'ct_edate',
        'ct_days',
        'ct_target_scope',
        'ct_target_members'
    ]);

    // 사용된 쿠폰 ID 목록 (회원일 때만)
    $usedCouponIds = [];
    if ($mt_idx > 0) {
        $DB->where('mt_idx', $mt_idx);
        $DB->where('cl_view', 'Y');
        $usedLogs = $DB->get('coupon_log_t', null, ['ct_idx']);
        $usedCouponIds = array_column($usedLogs, 'ct_idx');
        $usedCouponIds = array_map('intval', $usedCouponIds);
    }

    foreach ($allCoupons as $c) {
        $ct_idx = (int)$c['idx'];

        // ★★★ 이미 사용한 쿠폰은 제외 ★★★
        if (in_array($ct_idx, $usedCouponIds, true)) {
            continue;
        }

        // 회원 전용 쿠폰 체크
        $scope = $c['ct_target_scope'] ?? 'ALL';
        if ($scope === 'MEMBER') {
            if ($mt_idx <= 0) continue; // 비회원

            $membersCsv = trim($c['ct_target_members'] ?? '');
            if ($membersCsv !== '') {
                $allowed = array_map('intval', array_filter(array_map('trim', explode(',', $membersCsv))));
                if (!in_array($mt_idx, $allowed, true)) {
                    continue;
                }
            }
        }

        $coupons[] = $c;
    }
}

// ------------------------------
// 5) 적용 쿠폰(세션) 검증 + 할인 계산
// ------------------------------
$appliedCoupon = $_SESSION['order_coupon'] ?? null;
$discount = 0;

// 쿠폰 할인 계산 함수
$calcCouponDiscount = function($coupon, $totalPrice) {
    $type2 = (int)($coupon['ct_type2'] ?? 1);      // 1=정액, 2=정율
    $val   = (int)($coupon['ct_discount1'] ?? 0);  // 금액 또는 %
    $disc  = 0;

    if ($type2 === 2) {
        // 정율: % 할인 (내림)
        $disc = (int)floor($totalPrice * ($val / 100));
    } else {
        // 정액
        $disc = $val;
    }

    if ($disc < 0) $disc = 0;
    if ($disc > $totalPrice) $disc = $totalPrice;

    return $disc;
};

if (!empty($appliedCoupon['ct_idx'])) {
    $ct_idx = (int)$appliedCoupon['ct_idx'];

    // 현재 가능한 쿠폰 목록에서 찾기
    $valid = null;
    foreach ($coupons as $c) {
        if ((int)$c['idx'] === $ct_idx) { $valid = $c; break; }
    }

    // MEMBER 쿠폰 체크
    if ($valid && ($valid['ct_target_scope'] ?? 'ALL') === 'MEMBER' && $mt_idx <= 0) {
        $valid = null;
    }
    if ($valid && ($valid['ct_target_scope'] ?? 'ALL') === 'MEMBER') {
        $targetCsv = trim((string)($valid['ct_target_members'] ?? ''));
        if ($targetCsv !== '') {
            $arr = array_map('intval', array_filter(array_map('trim', explode(',', $targetCsv))));
            if (!in_array($mt_idx, $arr, true)) $valid = null;
        }
    }

    if ($valid) {
        $discount = $calcCouponDiscount($valid, $totalPrice);
        // 세션 갱신
        $_SESSION['order_coupon'] = [
            'ct_idx' => (int)$valid['idx'],
        ];
        $appliedCoupon = $_SESSION['order_coupon'];
    } else {
        unset($_SESSION['order_coupon']);
        $appliedCoupon = null;
        $discount = 0;
    }
}

$finalPrice = max(0, $totalPrice - $discount);

$isQr     = !empty($_SESSION['is_qr_order']) && !empty($_SESSION['qr_token']) && !empty($_SESSION['table_no']);
$isRes    = $_SESSION['order_mode'] === 'reservation';


$view_path = VIEWS_ORDER_PATH."/order.php";
if (file_exists($view_path)) {
    include_once $view_path;
} else {

}

include_once $_SERVER['DOCUMENT_ROOT'] . "/app/inc/tail.php";
?>
