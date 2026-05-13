<?php
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
$_ENABLED_INC_TOP = true;
$_ENABLED_INC_QUICK = true;

include_once $_SERVER['DOCUMENT_ROOT']."/app/inc/head.php";
$_SUB_HEAD_TITLE = "주문 상세";
$hd_num = 2;
$_GET['bt_menu'] = '';
include_once $_SERVER['DOCUMENT_ROOT']."/app/inc/header.php";

// ----------------------
// 입력값: ot_number 또는 rv_idx
// ----------------------
$ot_number = trim($_GET['ot_number'] ?? '');
$rv_idx    = (int)($_GET['rv_idx'] ?? 0);

$order = null;
$reservation = null;
$snapItems = [];
$showOrderMenus = false;
$isReservationOnly = false; // 단순 예약 여부 (현장결제)

// ----------------------
// 1. ot_number 있으면 → 선결제 주문 조회
// ----------------------
if ($ot_number !== '') {
    $DB->where('ot_number', $ot_number);
    $order = $DB->getOne('orders_t');

    if (!$order) {
        echo "<script>
            alert('주문 정보를 찾을 수 없습니다.');
            history.back();
        </script>";
        exit;
    }

    // 연동된 예약 있으면 가져오기
    if (!empty($order['rv_idx'])) {
        $DB->where('idx', (int)$order['rv_idx']);
        $reservation = $DB->getOne('reservation_t');
    }

    $DB->where('sh_idx', $reservation['sh_idx']);
    $rsvMsg = $DB->getOne('shop_reserve_setting_t');

    $DB->where('rs_idx',$rsvMsg['idx']);
    $rsvPrice = $DB->getOne('shop_reserve_penalty_t');
}
// ----------------------
// 2. ot_number 없고 rv_idx 있으면 → 단순 예약 조회 (현장결제)
// ----------------------
elseif ($rv_idx > 0) {
    $DB->where('idx', $rv_idx);
    $reservation = $DB->getOne('reservation_t');

    if (!$reservation) {
        echo "<script>
            alert('주문 정보를 찾을 수 없습니다.');
            history.back();
        </script>";
        exit;
    }

    // 단순 예약 플래그
    $isReservationOnly = true;

    // 선결제된 주문이 연동되어 있으면 가져오기 (있을 수도 있으니까)
    if (!empty($reservation['ot_idx'])) {
        $DB->where('idx', (int)$reservation['ot_idx']);
        $order = $DB->getOne('orders_t');
    }

} else {
    echo "<script>
            alert('주문 정보를 찾을 수 없습니다.');
            history.back();
        </script>";
    exit;
}

// ----------------------
// 공통: 매장 정보
// ----------------------
$shop = [];
$sh_idx = 0;
if ($order) {
    $sh_idx = (int)$order['sh_idx'];
} elseif ($reservation) {
    $sh_idx = (int)$reservation['sh_idx'];
}

if ($sh_idx > 0) {
    $DB->where('idx', $sh_idx);
    $shop = $DB->getOne('shop_t') ?: [];
}

// ----------------------
// ct_snapshot 파싱 (선결제 주문 있을 때만)
// ----------------------
$snap = [];
$snapItems = [];
$snapSummary = [];

if ($order && !empty($order['ct_snapshot'])) {
    $tmp = json_decode($order['ct_snapshot'], true);
    if (is_array($tmp)) {
        $snap = $tmp;
        $snapItems = (isset($tmp['items']) && is_array($tmp['items'])) ? $tmp['items'] : [];
        $snapSummary = (isset($tmp['summary']) && is_array($tmp['summary'])) ? $tmp['summary'] : [];
    }
}

$showOrderMenus = !empty($snapItems) && !$isReservationOnly;

// 총 수량
$totalQty = 0;
foreach ($snapItems as $it) {
    $q = (int)($it['quantity'] ?? $it['qty'] ?? 0);
    if ($q <= 0) $q = 1;
    $totalQty += $q;
}

// 결제 요약 (선결제 있을 때만)
$sumGoods = $sumDisc = $sumFinal = 0;
if (!$isReservationOnly && $order) {
    $sumGoods = (int)($snapSummary['sub_total'] ?? $snapSummary['subTotal'] ?? 0);
    $sumDisc  = (int)($snapSummary['discount'] ?? 0);
    $sumFinal = (int)($snapSummary['total'] ?? 0);

    if ($sumFinal <= 0) $sumFinal = (int)($order['ot_total_price'] ?? 0);
    if ($sumDisc  <= 0) $sumDisc  = (int)($order['ot_discount_amount'] ?? 0);
    if ($sumGoods <= 0) $sumGoods = $sumFinal + $sumDisc;
}

// ----------------------
// 배지 및 상태 텍스트
// ----------------------
$isReservation = !empty($reservation);

$badgeTitle = $isReservation ? '예약' : '포장';
$badgeColor = $isReservation ? 'blue' : 'green';
$badgeIcon  = $isReservation ? 'ic_calendar' : 'ic_pack';
$badgeClass = $isReservation ? 't_blue' : 't_green';

$rvStatusText = '예약요청';
if ($isReservation) {
    $rvStatus = (string)($reservation['rv_status'] ?? 'PENDING');
    if ($rvStatus === 'CONFIRMED') $rvStatusText = '예약확정';
    if ($rvStatus === 'CANCELLED') $rvStatusText = '예약취소';
    if ($rvStatus === 'REJECTED')  $rvStatusText = '예약거절';
    if ($rvStatus === 'ARRIVED')   $rvStatusText = '방문완료';

    $rvDate = $reservation['rv_date'] ?? '';
    $rvTime = $reservation['rv_time'] ?? '';
    $rvDateText = $rvDate ? date('Y.m.d', strtotime($rvDate)) : '';
    $rvTimeText = $rvTime ? substr($rvTime, 0, 5) : '';
} else {
    // 포장 주문 상태
    $otStatus = (string)($order['ot_status'] ?? '');
    $rvStatusText = '포장요청';
    if ($otStatus === 'CONFIRMED') $rvStatusText = '포장확정';
    if ($otStatus === 'CANCELLED') $rvStatusText = '포장취소';

    $DB->where('idx',$order['mt_idx']);
    $mt = $DB->getOne('member_t','mt_name, mt_hp');
    $prep_min = $order['ot_prep_min'];
}


$shop_img = DESIGN_HTTP.'/img/pr_sample01.jpg';
if (!empty($shop['sh_img1'])) {
    $shop_img = "/data/shop/{$sh_idx}/rs_{$shop['sh_img1']}";
}

$linkShop = './shop.php';
if ($sh_idx > 0) {
    $linkShop = '../shop/list.php?sh_idx=' . $sh_idx;
}

// 뷰 호출
include_once VIEWS_RSRV_PATH."/rsrv_history.php";

include_once $_SERVER['DOCUMENT_ROOT'] . "/app/inc/tail.php";
?>
