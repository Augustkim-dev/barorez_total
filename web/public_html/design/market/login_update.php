<?
include $_SERVER['DOCUMENT_ROOT']."/lib.inc.php";

if(!isset($_POST['mt_pwd'], $_POST['mt_id'])){
    p_alert("필수값이 부족합니다.");
}


$DB->where('mt_id', $_POST['mt_id']);
$DB->where('mt_level', '4');
$DB->where('mt_status', 'Y');
$DB->where('mt_rdate', '');

$row = $DB->getone('member_t', '*, idx as mt_idx');

if (password_verify($_POST['mt_pwd'], $row['mt_pwd'])) {
    unset($arr_query);
    $arr_query = array(
        'mt_ldate' => $DB->now(),
    );
    if($chk_mobile){
        $arr_query['mt_app_token'] = $_POST['mt_app_token'];
    }
    $DB->where('idx', $row['mt_idx']);
    $DB->update('member_t', $arr_query);

    //확인필요
    
    //동시접속 DB 체크

    $_mt_idx   = $_SESSION['_mt_idx']   = $row['mt_idx'];
    $_mt_level = $_SESSION['_mt_level'] = $row['mt_level'];
    $_mt_id    = $_SESSION['_mt_id']    = $row['mt_id'];
    $_mt_name  = $_SESSION['_mt_name']  = $row['mt_name'];

    p_gotourl("./");
} else{
    p_alert("아이디 및 비밀번호가 올바르지 않습니다.<br/>아이디, 비밀번호는 대문자, 소문자를 구분합니다.<br/><Caps Lock>키가 켜져 있는지 확인하시고 다시 입력하십시오.");
}

include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";