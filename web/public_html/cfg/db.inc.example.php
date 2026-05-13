<?php
require $_SERVER['DOCUMENT_ROOT']."/vendor/autoload.php";
//require '../vendor/autoload.php';

// 운영 환경 셋업: 본 파일을 cfg/db.inc.php 로 복사 후 실제 값 입력.
// cfg/db.inc.php 는 .gitignore 에 의해 추적 제외 상태.
$DB = new MysqliDb(array(
    'host' => 'localhost',
    'username' => 'CHANGE_ME_DB_USERNAME',
    'password' => 'CHANGE_ME_DB_PASSWORD',
    'db' => 'CHANGE_ME_DB_NAME',
    'port' => 3306,
    'prefix' => '',
    'charset' => 'utf8mb4'));
