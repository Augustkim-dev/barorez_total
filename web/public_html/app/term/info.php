<?php
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
$_ENABLED_INC_TOP = true;
$_ENABLED_INC_QUICK = true;
//$_SUB_HEAD_TITLE = "로그인";
include_once $_SERVER['DOCUMENT_ROOT']."/app/inc/head.php";
$type = isset($_GET['type']) ? (int)$_GET['type'] : 0;
$row = $DB->getOne('setup_t');

if (!in_array($type, [0, 1, 2, 3], true)) {
    echo "<script>history.back();</script>";
    exit;
}
$agree = 'st_agree'.$type;

$titleMap = [
    1 => '이용약관',
    2 => '개인정보처리방침',
    3 => '위치기반서비스 이용약관',
];

$_SUB_HEAD_TITLE = $titleMap[$type]; //헤더에 타이틀명이 없을경우 공백
$hd_num = 2; //모바일 hd 1~n까지 있음
$_GET['bt_menu'] = ''; //모바일 하단메뉴 있음1, 없음 공백
include_once $_SERVER['DOCUMENT_ROOT']."/app/inc/header.php";
$view_path = VIEWS_TERM_PATH."/info.php";
if (file_exists($view_path)) {
    include_once $view_path;
} else {

}

include_once $_SERVER['DOCUMENT_ROOT'] . "/app/inc/tail.php";
?>
