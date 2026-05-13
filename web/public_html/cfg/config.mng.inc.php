<?php
//최고관리자 전용 설정
error_reporting(E_ERROR);
ini_set('display_errors', '1');

// 현재 페이지의 URL 가져오기
$current_url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

// URL에서 파일명 추출
$file_name = basename($current_url);

// 파일명이 "login.php"인지 확인
if (!isset($_SESSION['mng']['mt_id']) || $file_name === "login.php") {
    $_ADMIN_HEADER = false;
} else {
    $_ADMIN_HEADER = true;
}
