<?
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";


if ($_POST['act'] == 'find_id') {
    if (!isset($_POST['mt_hp'])) {
        p_alert("필수값이 부족합니다.");
    }

    $DB->where('mt_hp', format_phone($_POST['mt_hp']));
    $row = $DB->getone('member_t');

    if ($row['idx']) {
        p_gotourl("./find_id_result.php?mt_id=".$row['mt_id']."&mt_wdate=".$row['mt_wdate']);
    } else {
        p_alert("해당 휴대폰번호로 가입된 아이디가 존재하지 않습니다.");
    }

} else if ($_POST['act'] == 'find_pw') {
    if (!isset($_POST['mt_hp'], $_POST['mt_id'])) {
        p_alert("필수값이 부족합니다.");
    }
    $DB->where('mt_hp', format_phone($_POST['mt_hp']));
    $DB->where('mt_id', $_POST['mt_id']);

    $row = $DB->getone('member_t');
    if ($row['idx']) {
        p_gotourl("./find_pw_reset.php?idx=".$row['idx']);
    } else {
        p_alert("해당 휴대폰번호로 가입된 아이디가 존재하지 않습니다.");
    }
} else if ($_POST['act'] == 'reset_pw') {
    if (!isset($_POST['idx'], $_POST['mt_pwd'])) {
        p_alert("필수값이 부족합니다.");
    }

    unset($arr_query);
    $arr_query = array(
        'mt_pwd' => password_hash($_POST['mt_pwd'], PASSWORD_DEFAULT),
    );

    $DB->where('idx', $_POST['idx']);
    $DB->update('member_t', $arr_query);

    p_gotourl("./find_pw_result.php");
}

include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";