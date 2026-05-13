<?php
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
$_ENABLED_INC_TOP = true;
$_ENABLED_INC_QUICK = true;
//$_SUB_HEAD_TITLE = "로그인";
include_once $_SERVER['DOCUMENT_ROOT']."/app/inc/head.php";
$_SUB_HEAD_TITLE = "장바구니"; //헤더에 타이틀명이 없을경우 공백
$hd_num = 2; //모바일 hd 1~n까지 있음
$_GET['bt_menu'] = ''; //모바일 하단메뉴 있음1, 없음 공백
include_once $_SERVER['DOCUMENT_ROOT']."/app/inc/header.php";

$mt_idx = (int)($_SESSION['mng']['mt_idx'] ?? 0);
$st_id  = (int)($_SESSION['cart_store_id'] ?? 0);
$ct_ids = $_SESSION['cart_ct_ids'] ?? [];
$ct_ids = array_values(array_filter(array_map('intval', $ct_ids)));

if ($mt_idx > 0) {

    // 세션이 완전히 비었거나(브라우저 종료/만료), 매장정보가 없으면 복원 시도
    $needRestore = ($st_id <= 0 || empty($ct_ids));

    if ($needRestore) {

        // 1) 해당 유저의 장바구니가 DB에 있는지 확인 (최신 항목 기준으로 매장 추정)
        $DB->where('mt_idx', $mt_idx);
        $DB->orderBy('idx', 'DESC');
        $latest = $DB->getOne('cart_t', ['st_id']);

        $db_st_id = (int)($latest['st_id'] ?? 0);

        if ($db_st_id > 0) {

            $st_id = $db_st_id;

            // 2) 해당 매장 장바구니 전체를 가져와서 세션을 재구성
            $DB->where('mt_idx', $mt_idx);
            $DB->where('st_id', $st_id);
            $DB->orderBy('idx', 'DESC');
            $dbCart = $DB->get('cart_t', null, ['idx', 'ct_quantity', 'ct_total_price']);

            $newCtIds = [];
            $newQty = 0;

            foreach ($dbCart as $r) {
                $newCtIds[] = (int)$r['idx'];
                $newQty += (int)($r['ct_quantity'] ?? 0);
            }

//            $_SESSION['cart_store_id'] = $st_id;
            $_SESSION['cart_ct_ids']   = $newCtIds;
            $_SESSION['cart_qty']      = $newQty;

            // 컨트롤에서도 이후 로직이 세션 변수를 쓰니 갱신
            $ct_ids = $newCtIds;

        } else {
            // DB에도 장바구니가 없으면 세션도 정리
            unset($_SESSION['cart_ct_ids']);
            $_SESSION['cart_qty'] = 0;

            $st_id = 0;
            $ct_ids = [];
        }
    }
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

    // 비회원이면 ct_ids도 실제 조회 기준으로 다시 맞춰주기(세션 꼬임 방지)
    if ($mt_idx <= 0) {
        $_SESSION['cart_ct_ids'] = array_map(function($r){ return (int)$r['ct_idx']; }, $cartRows);
    }
}

$isQr     = !empty($_SESSION['is_qr_order']) && !empty($_SESSION['qr_token']) && !empty($_SESSION['table_no']);

$menu_add = '';
if($isQr){
    $menu_add = APP_PAGE;
}else{
    $menu_add = '../shop/list.php?sh_idx='.$_SESSION['cart_store_id'];
}

$view_path = VIEWS_ORDER_PATH."/cart.php";
if (file_exists($view_path)) {
    include_once $view_path;
} else {

}

include_once $_SERVER['DOCUMENT_ROOT'] . "/app/inc/tail.php";
?>
