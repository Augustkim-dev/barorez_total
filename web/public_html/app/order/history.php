<?php
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
$_ENABLED_INC_TOP = true;
$_ENABLED_INC_QUICK = true;

include_once $_SERVER['DOCUMENT_ROOT']."/app/inc/head.php";
$_SUB_HEAD_TITLE = "주문 내역";
$hd_num = 2;
$_GET['bt_menu'] = '';
include_once $_SERVER['DOCUMENT_ROOT']."/app/inc/header.php";

/* 로그인 체크 */
$mt_idx = (int)($_SESSION['mng']['mt_idx'] ?? 0);
if ($mt_idx <= 0) {
    alert('로그인이 필요합니다.', '/member/login.php');
    exit;
}

// 초기 데이터는 뷰에서 AJAX로 가져오므로 여기서는 아무것도 안 함
$orders = [];
$cancelOrders = [];

/* 뷰 호출 */
$view_path = VIEWS_ORDER_PATH . "/history.php";
if (file_exists($view_path)) {
    include_once $view_path;
}

include_once $_SERVER['DOCUMENT_ROOT'] . "/app/inc/tail.php";
?>
