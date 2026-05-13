<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";

if($_POST['act']=="update") {
    unset($arr_query);
    $arr_query = array(
        "st_agree1" => $_POST['st_agree1'],
        "st_agree2" => $_POST['st_agree2'],
        "st_agree3" => $_POST['st_agree3'],
        "st_agree4" => $_POST['st_agree4'],
        "st_agree5" => $_POST['st_agree5'],
        "st_agree6" => $_POST['st_agree6'],
        "st_agree7" => $_POST['st_agree7'],
        "st_agree8" => $_POST['st_agree8'],
        "st_agree9" => $_POST['st_agree9'],
        "st_agree10" => $_POST['st_agree10'],
        "st_agree11" => $_POST['st_agree11'],
    );

    $DB->where('idx', '1');
    $DB->update('setup_t', $arr_query);

    p_alert("수정되었습니다.");
}

include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
