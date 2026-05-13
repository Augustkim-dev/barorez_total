<?php
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
$_ENABLED_INC_TOP = true;
$_ENABLED_INC_QUICK = true;
//$_SUB_HEAD_TITLE = "로그인";
include_once $_SERVER['DOCUMENT_ROOT']."/app/inc/head.php";
$_SUB_HEAD_TITLE = "지도"; //헤더에 타이틀명이 없을경우 공백
$hd_num = 7; //모바일 hd 1~n까지 있음
$_GET['bt_menu'] = ''; //모바일 하단메뉴 있음1, 없음 공백
include_once $_SERVER['DOCUMENT_ROOT']."/app/inc/header.php";

unset($_SESSION['qr_token']);
unset($_SESSION['current_sh_idx']);
unset($_SESSION['tb_idx']);
unset($_SESSION['is_qr_order']);
unset($_SESSION['tv_idx']);
unset($_SESSION['visit_key']);
unset($_SESSION['visit_id']);
unset($_SESSION['visit_token']);

$view_path = VIEWS_MAP_PATH."/map.php";
if (file_exists($view_path)) {
    include_once $view_path;
} else {

}

include_once $_SERVER['DOCUMENT_ROOT'] . "/app/inc/tail.php";
?>
