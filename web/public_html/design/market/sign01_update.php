<?php
include $_SERVER['DOCUMENT_ROOT']."/src/seller/lib_seller.php";

if(!isset($_POST['mt_name'], $_POST['mt_hp'], $_POST['mt_birth'], $_POST['mt_id'], $_POST['mt_pwd'])){
    p_alert("필수값이 부족합니다.");
}

unset($arr_query);
$arr_query = array(
    "mt_login_type" => 1,
    "mt_id" => $_POST['mt_id'],
    "mt_pwd" => password_hash($_POST['mt_pwd'], PASSWORD_DEFAULT),
    "mt_level" => 4,
    "mt_name" => $_POST['mt_name'],
    "mt_birth" => $_POST['mt_birth'],
    "mt_point" => 0,
    "mt_hp" => $_POST['mt_hp'],
    "mt_hp_certify" => $DB->now(),
    "mt_status" => "Y",
    "mt_seller" => "D",
    "mt_wdate" => $DB->now()
);

if($chk_mobile){
    $arr_query['mt_app_token'] = $_POST['mt_app_token'];
}

$idx = $DB->insert('member_t', $arr_query);

p_gotourl("./sign02.php?idx=".$idx);


include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";