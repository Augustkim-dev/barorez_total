<?php
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";

$_ENABLED_INC_TOP    = true;
$_ENABLED_INC_QUICK  = true;
$_ENABLED_INC_FOOTER = true;
$_ENABLED_INC_MODAL  = true;

include_once $_SERVER['DOCUMENT_ROOT']."/app/inc/head.php";
$hd_num = 5;
include_once $_SERVER['DOCUMENT_ROOT']."/app/inc/header.php";

$mt_idx = (int)($_SESSION['mng']['mt_idx'] ?? 0);
$st_id  = (int)($_SESSION['cart_store_id'] ?? 0);
$ct_ids = $_SESSION['cart_ct_ids'] ?? [];
$ct_ids = array_values(array_filter(array_map('intval', $ct_ids)));
/* =========================================================
 * 1) 포장/예약 모드
 * ========================================================= */
$mode = strtolower((string)($_SESSION['order_mode'] ?? 'reservation'));
if (!in_array($mode, ['takeout', 'reservation'], true)) {
    $mode = 'reservation';
}

/* =========================================================
 * 2) 매장 키
 * ========================================================= */
$shopId = (int)($_GET['sh_idx'] ?? 0);
if ($shopId <= 0) {
    die('매장 정보(sh_idx)가 없습니다.');
}

/* =========================================================
 * 3) QR 관련 세션 제거
 * ========================================================= */
unset($_SESSION['qr_token'], $_SESSION['table_no'], $_SESSION['tv_idx'], $_SESSION['visit_key']);
$_SESSION['is_qr_order'] = false;

/* =========================================================
 * 4) 주문 흐름 세션 세팅
 * ========================================================= */
$_SESSION['current_sh_idx'] = $shopId;
$_SESSION['cart_store_id']  = $shopId;
$_SESSION['order_mode']     = $mode;

/* =========================================================
 * 5) 매장 정보 조회 (shop_t)
 *   - ✅ 포장/예약 플래그 포함
 *   - ✅ 이미지 5개 + re_ prefix 컬럼 고려
 * ========================================================= */
$DB->where('idx', $shopId);
$DB->where('del_date', null, 'IS');

// ⚠️ 컬럼명이 실제로 re_sh_img1~5 인지 / sh_img1~5 인지 프로젝트마다 다를 수 있음
// 지금 대화 기준: "sh_img가 5개이고 앞에 re_가 붙어야 한다" => 컬럼은 sh_img1~5이고 파일명에 re_가 붙는 케이스로 처리
$row = $DB->getOne('shop_t', [
    'idx','sh_title','sh_branch_nm','sh_addr1','sh_addr2',
    'sh_lat','sh_lng','sh_tel',
    'sh_show',
    'sh_reserve_yn','sh_takeout_yn',
    'sh_img1','sh_img2','sh_img3','sh_img4','sh_img5',
]);

if (!$row) {
    die('매장 정보를 찾을 수 없습니다.');
}

/* =========================================================
 * 6) 대표 이미지 구성
 *   - ✅ 이미지 5개
 *   - ✅ 파일명에 re_ prefix 적용 (rs_가 아니라 rs_)
 * ========================================================= */
$shopImg = '';
foreach (['sh_img1','sh_img2','sh_img3','sh_img4','sh_img5'] as $k) {
    $v = trim((string)($row[$k] ?? ''));
    if ($v !== '') {
        // 파일명 앞에 rs_가 붙는 정책
        $shopImg = '/data/shop/'.$shopId.'/rs_'.$v;
        break;
    }
}
if ($shopImg === '') {
    $shopImg = DESIGN_HTTP.'/img/pr_sample01.jpg';
}

/* =========================================================
 * ✅ 7) 포장/예약 가능 여부 판단
 * ========================================================= */
$allowTakeout = (($row['sh_takeout_yn'] ?? 'Y') === 'Y');
$allowReserve = (($row['sh_reserve_yn'] ?? 'Y') === 'Y');
$allowOrder   = ($allowTakeout || $allowReserve);

/* =========================================================
 * ✅ 8) 현재 mode가 비활성인 경우 자동 보정
 *   - 예약 불가인데 reservation이면 takeout으로 변경
 *   - 포장 불가인데 takeout이면 reservation으로 변경
 *   - 둘 다 불가면 mode는 유지하되 주문 불가 상태
 * ========================================================= */
if ($mode === 'takeout' && !$allowTakeout) {
    $mode = $allowReserve ? 'reservation' : $mode;
}
if ($mode === 'reservation' && !$allowReserve) {
    $mode = $allowTakeout ? 'takeout' : $mode;
}
$_SESSION['order_mode'] = $mode;

/* =========================================================
 * ✅ 9) 오늘 운영시간 조회 (shop_hours_t)
 *  - dow: 0(일)~6(토) => PHP date('w') 동일
 *  - bt_type OPEN 이면 시간 표기, 그 외는 휴무일
 * ========================================================= */
$todayDow = (int)date('w');
$todayHoursText = '휴무일';

// CLOSE row가 아예 없는 경우도 휴무일로 처리할거면 그대로 OK
$DB->where('sh_idx', $shopId);
$DB->where('dow', $todayDow);
$hourRow = $DB->getOne('shop_hours_t', ['bt_type','start_time','end_time']);

if ($hourRow) {
    $bt = strtoupper(trim((string)($hourRow['bt_type'] ?? 'CLOSE')));
    if ($bt === 'OPEN') {
        $st = trim((string)($hourRow['start_time'] ?? ''));
        $et = trim((string)($hourRow['end_time'] ?? ''));
        if ($st !== '' && $et !== '') {
            $todayHoursText = substr($st, 0, 5) . '~' . substr($et, 0, 5);
        }
    }
}

$mt_idx = (int)($_SESSION['mng']['mt_idx'] ?? 0);
$ct_ids = $_SESSION['cart_ct_ids'] ?? [];
$ct_ids = array_values(array_filter(array_map('intval', $ct_ids)));

$shopId = (int)($_GET['sh_idx'] ?? 0);
if ($shopId <= 0) {
    die('매장 정보(sh_idx)가 없습니다.');
}

// 기존 장바구니 매장 확인
$cartStoreId = 0;
$hasOtherStoreCart = false;

if ($mt_idx > 0) {
    $DB->where('mt_idx', $mt_idx);
    $cartRow = $DB->getOne('cart_t', ['st_id']);
    if (!empty($cartRow)) {
        $cartStoreId = (int)($cartRow['st_id'] ?? 0);
    }
} else if (!empty($ct_ids)) {
    $DB->where('idx', $ct_ids, 'IN');
    $cartRow = $DB->getOne('cart_t', ['st_id']);
    if (!empty($cartRow)) {
        $cartStoreId = (int)($cartRow['st_id'] ?? 0);
    }
}

if ($cartStoreId > 0 && $cartStoreId !== $shopId) {
    $hasOtherStoreCart = true;
}

$cartRows = [];
$optionsMap = [];
$totalQty = 0;
$totalPrice = 0;

// 1) st_id가 없으면 (세션 꼬임 대비) DB에서 추정
if ($st_id <= 0) {
    if ($mt_idx > 0) {
        $DB->where('mt_idx', $mt_idx);
        $row = $DB->getOne('cart_t', ['st_id']);
        $st_id = (int)($row['st_id'] ?? 0);
        if ($st_id > 0) $_SESSION['cart_store_id'] = $st_id;
    } else if (!empty($ct_ids)) {
        $DB->where('idx', $ct_ids, 'IN');
        $row = $DB->getOne('cart_t', ['st_id']);
        $st_id = (int)($row['st_id'] ?? 0);
        if ($st_id > 0) $_SESSION['cart_store_id'] = $st_id;
    }
}

// 2) cart_t + shop_menu_t 조인 조회
if ($mt_idx > 0 && $st_id > 0) {
    $DB->join('shop_menu_t m', 'c.sm_id = m.idx', 'INNER');
    $DB->where('c.mt_idx', $mt_idx);
    $DB->where('c.st_id', $st_id);
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
        'm.sm_su',
        'm.sm_type',
        'm.sm_show'
    ]);

} else if ($st_id > 0 && !empty($ct_ids)) {
    $DB->join('shop_menu_t m', 'c.sm_id = m.idx', 'INNER');
    $DB->where('c.idx', $ct_ids, 'IN');
    $DB->where('c.st_id', $st_id);
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
        'm.sm_su',
        'm.sm_type',
        'm.sm_show'
    ]);
}

// 3) 옵션 조회 (cart_options_t + 옵션카테고리명)
if (!empty($cartRows)) {
    $ctIdxList = array_map(function($r){ return (int)$r['ct_idx']; }, $cartRows);

    $DB->join('menu_option_category_t oc', 'co.oc_idx = oc.idx', 'LEFT');
    $DB->join('option_menu_t om', 'co.om_idx = om.idx', 'LEFT');
    $DB->where('co.ct_idx', $ctIdxList, 'IN');
    $DB->orderBy('oc.oc_order', 'ASC');
    $DB->orderBy('om.om_order', 'ASC');

    $optRows = $DB->get('cart_options_t co', null, [
        'co.ct_idx',
        'co.oc_idx',
        'oc.oc_title',
        'co.co_option_name',
        'co.co_option_price'
    ]);

    foreach ($optRows as $o) {
        $k = (int)$o['ct_idx'];
        if (!isset($optionsMap[$k])) $optionsMap[$k] = [];
        $optionsMap[$k][] = $o;
    }

    // 4) 합계 계산 + 세션 동기화(뱃지 정확도 위해)
    $totalQty = 0;
    $totalPrice = 0;

    foreach ($cartRows as $r) {
        $q = (int)($r['ct_quantity'] ?? 0);
        $p = (int)($r['ct_total_price'] ?? 0);
        $totalQty += $q;
        $totalPrice += $p;
    }

    $_SESSION['cart_qty'] = $totalQty;

    $_SESSION['cart_ct_ids'] = array_map(function($r){ return (int)$r['ct_idx']; }, $cartRows);
}

/* =========================================================
 * 10) 뷰 변수
 * ========================================================= */
$_PAGE_MODE = $mode;
$_SHOP_ID   = $shopId;
$_SHOP_ROW  = $row;
$_SHOP_IMG  = $shopImg;

$_TODAY_DOW_TEXT   = $todayDow;
$_TODAY_HOURS_TEXT = $todayHoursText;

// ✅ 핵심: 여기 이제 true 고정이 아니라 DB 기반
$_ALLOW_TAKEOUT     = $allowTakeout;
$_ALLOW_RESERVATION = $allowReserve;
$_ALLOW_ORDER       = $allowOrder;

/* =========================================================
 * 11) 뷰 호출
 * ========================================================= */
$view_path = VIEWS_SHOP_PATH."/list.php";
if (file_exists($view_path)) {
    include_once $view_path;
}

include_once $_SERVER['DOCUMENT_ROOT'] . "/app/inc/tail.php";
?>
