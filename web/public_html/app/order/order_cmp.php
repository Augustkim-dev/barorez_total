<?php
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
$_ENABLED_INC_TOP = true;
$_ENABLED_INC_QUICK = true;
//$_SUB_HEAD_TITLE = "로그인";
include_once $_SERVER['DOCUMENT_ROOT']."/app/inc/head.php";
$_SUB_HEAD_TITLE = "주문 완료"; //헤더에 타이틀명이 없을경우 공백
$hd_num = ''; //모바일 hd 1~n까지 있음
$_GET['bt_menu'] = ''; //모바일 하단메뉴 있음1, 없음 공백
include_once $_SERVER['DOCUMENT_ROOT']."/app/inc/header.php";

// =====================================================
// 1) 주문 식별자 수집 (GET > SESSION)
// =====================================================
$ot_number = trim($_GET['ot_number'] ?? '');
$ot_idx    = (int)($_GET['ot_idx'] ?? 0);

if ($ot_number === '' && $ot_idx <= 0) {
    $ot_number = trim($_SESSION['last_order_ot_number'] ?? '');
    $ot_idx    = (int)($_SESSION['last_order_idx'] ?? 0);
}

// =====================================================
// 2) 주문 조회
// =====================================================
$orderRow = null;

if ($ot_number !== '') {
    $DB->where('ot_number', $ot_number);
    $orderRow = $DB->getOne('orders_t');
} else if ($ot_idx > 0) {
    $DB->where('idx', $ot_idx);
    $orderRow = $DB->getOne('orders_t');
}

if (!$orderRow) {
    // 주문이 없으면 홈이나 주문내역으로 보내는게 UX 좋음
    echo "<script>alert('주문 정보를 찾을 수 없습니다.');location.replace('/');</script>";
    exit;
}

// (선택) 로그인 사용자는 본인 주문만 보이게
$mt_idx = (int)($_SESSION['mng']['mt_idx'] ?? 0);
if ($mt_idx > 0) {
    if ((int)($orderRow['mt_idx'] ?? 0) !== $mt_idx) {
        echo "<script>alert('잘못된 접근입니다.');location.replace('/');</script>";
        exit;
    }
}

// =====================================================
// 3) 매장 조회
// =====================================================
$shopRow = [];
$shopImg = DESIGN_HTTP.'/img/pr_sample01.jpg';

$sh_idx = (int)($orderRow['sh_idx'] ?? 0);
if ($sh_idx > 0) {
    $DB->where('idx', $sh_idx);
    $shopRow = $DB->getOne('shop_t') ?: [];

    // 프로젝트 컬럼명에 맞춰 조정하세요 (예: sh_img, sh_logo, sh_thumb 등)
    if (!empty($shopRow['sh_img1'])) {
        $shopImg = "/data/shop/{$sh_idx}/rs_{$shopRow['sh_img1']}";
    }
}

// =====================================================
// 4) 스냅샷 파싱
// =====================================================
$snapshotRaw = (string)($orderRow['ct_snapshot'] ?? '');
$snapshot = [];
$items = [];
$summary = [
    'sub_total' => 0,
    'discount'  => 0,
    'total'     => 0,
];

if ($snapshotRaw !== '') {
    $snapshot = json_decode($snapshotRaw, true);
    if (is_array($snapshot)) {
        $items = (array)($snapshot['items'] ?? []);
        $summary = array_merge($summary, (array)($snapshot['summary'] ?? []));
    }
}

// 서버값 기준으로 한 번 더 정리 (안전)
$subTotal = (int)($summary['sub_total'] ?? 0);
$discount = (int)($summary['discount'] ?? 0);
$finalPrice = (int)($summary['total'] ?? 0);

// orders_t 값이 더 신뢰 가능하면 여기서 덮어써도 됨
// $discount   = (int)($orderRow['ot_discount_amount'] ?? $discount);
// $finalPrice = (int)($orderRow['ot_total_price'] ?? $finalPrice);

$totalQty = 0;
foreach ($items as $it) {
    $totalQty += max(1, (int)($it['quantity'] ?? 1));
}

// =====================================================
// 5) 화면 표시용 값
// =====================================================
$DB->where('tb_no',$orderRow['ot_table']);
$otTableRaw = $DB->getOne('shop_table_t', 'tb_no, tb_name');
$otTableRaw2 = trim((string)($otTableRaw['tb_name'] ?? '')); // "6" 또는 "" (포장)
$otTableTxt = '';
if ($otTableRaw2 !== '') {
    // 숫자만 들어오게 이미 수정했다고 했으니 그대로 사용
    $otTableTxt = $otTableRaw2 . "번 테이블 손님,";
}

$orderNumberTxt = (string)($orderRow['ot_number'] ?? '');
$orderDateTxt = '';
if (!empty($orderRow['ot_wdate'])) {
    $orderDateTxt = date('y.m.d H:i', strtotime($orderRow['ot_wdate']));
}

$view_path = VIEWS_ORDER_PATH."/order_cmp.php";
if (file_exists($view_path)) {
    include_once $view_path;
} else {

}

include_once $_SERVER['DOCUMENT_ROOT'] . "/app/inc/tail.php";
?>
