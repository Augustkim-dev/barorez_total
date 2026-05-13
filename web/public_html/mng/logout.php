<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";

//session_unset();
//session_destroy();
//unset($_SESSION);


unset($_SESSION['mng']);
//unset($_COOKIE);

gotourl("./login.php");

?>