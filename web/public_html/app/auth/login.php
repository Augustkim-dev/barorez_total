<?php
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
$_ENABLED_INC_TOP = true;
$_ENABLED_INC_QUICK = true;
include_once $_SERVER['DOCUMENT_ROOT']."/app/inc/head.php";
$_SUB_HEAD_TITLE = "로그인";
$hd_num = 6;
$_GET['bt_menu'] = '';
if($_SESSION['qr_token']){
    $url = APP_PAGE;
}else{
    $url = MAP_PAGE;
}
include_once $_SERVER['DOCUMENT_ROOT']."/app/inc/header.php";

if($_SESSION['mng']){
    header("Location: " . $url);  // 메인으로 이동
    exit;
}

$view_path = VIEWS_AUTH_PATH."/login.php";
if (file_exists($view_path)) {
    include_once $view_path;
} else {

}

include_once $_SERVER['DOCUMENT_ROOT'] . "/app/inc/tail.php";
?>
