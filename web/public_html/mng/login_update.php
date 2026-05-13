<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";

if ($_POST['mt_id']=="") {
    p_alert("잘못된 접근입니다.");
}
if ($_POST['mt_pass']=="") {
    p_alert("잘못된 접근입니다.");
}

//$_POST['mt_id'] = "admin";
//$_POST['mt_pass'] = "1016";


$DB->where('mt_id', $_POST['mt_id']);
$DB->where("mt_status", "Y");
$DB->where("mt_level", "5", ">=");
$DB->where("(mt_rdate = ? OR mt_rdate IS NULL)", array(''));

$row = $DB->getOne('member_t', '*, idx as mt_idx');

//// 마지막으로 실행된 쿼리 가져오기
//$lastQuery = $DB->getLastQuery();
//// 쿼리 출력
//echo "실행된 SELECT 쿼리:\n";
//print_r($lastQuery);
//
//exit;

if ($DB->count === 0) {
    p_alert("잘못된 접근입니다.");
} else {
    if (password_verify($_POST['mt_pass'], $row['mt_pwd'])) {

        unset($arr_query);
        $arr_query = array(
            'mt_ldate' => $DB->now(),
        );
        $DB->where('idx', $row['mt_idx']);
        $DB->update('member_t', $arr_query);


        $profile = $ct_no_profile_url;
        if(!empty($row['mt_image1'])) {
          $filepath = $member_img_dir . $row['mt_image1'];
          if(file_exists($filepath)) {
            $profile = $member_img_url . $row['mt_image1'];
          }
        }

        $_SESSION['mng'] = [
          'mt_idx'   => $row['mt_idx'],
          'mt_id'    => $row['mt_id'],
          'mt_name'  => $row['mt_name'],
          'mt_hp'    => $row['mt_hp'],
          'mt_nickname'  => $row['mt_nickname'],
          'mt_level' => $row['mt_level'],
          'profile_url' => $profile,
        ];
      //echo "<!-- pre>";
      //print_r($_SESSION);
      //echo "</pre --!>";
      //exit;



      p_gotourl("./");
    } else {
        p_alert("아이디 및 비밀번호가 올바르지 않습니다.<br/>아이디, 비밀번호는 대문자, 소문자를 구분합니다.<br/><Caps Lock>키가 켜져 있는지 확인하시고 다시 입력하십시오.");
    }
}

include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
