<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";


// 엑셀로 출력할 헤더 설정
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=member_list_" . date('Y_M_D_h_i_s') . ".xls");
header("Content-Description: PHP Generated Data");
header("Pragma: no-cache");
header("Expires: 0");


$p1 = $_GET['p1'] ?? ''; // 검색조건
$p2 = $_GET['p2'] ?? ''; // 검색어

if ($p1 == "all") {
  $_instr_where = 'instr(mt_id, \''.$p2.'\') or ';
  $_instr_where .= 'instr(mt_name, \''.$p2.'\') or ';
  $_instr_where .= 'instr(mt_hp, \''.$p2.'\')';
  $DB->where('( '.$_instr_where.' )');
} else if($p1 == "mt_id"){
    $_instr_where = 'instr(mt_id, \''.$p2.'\')';
    $DB->where('( '.$_instr_where.' )');
}else if($p1 == "mt_name"){
    $_instr_where = 'instr(mt_name, \''.$p2.'\')';
    $DB->where('( '.$_instr_where.' )');
}else if($p1 == "mt_hp"){
    $_instr_where = 'instr(mt_hp, \''.$p2.'\')';
    $DB->where('( '.$_instr_where.' )');
}else {
  $DB->where('( instr('.$p1.', \''.$p2.'\') )');
}

$DB->where('del_status', 'N');
$DB->where('mt_level', [7, 8], 'IN');
$DB->where('mt_mng', 'Y');

$DB->orderBy('idx', 'asc');
$list = $DB->get($CFG_TBL['member']['default']);

// 인코딩 깨짐 방지 (엑셀용 UTF-8 BOM)
echo chr(255) . chr(254); // UTF-16LE BOM
$handle = fopen("php://output", "w");

// 헤더 출력
$header = ["아이디","이름","휴대폰번호","구분","등록일"];
fwrite($handle, mb_convert_encoding(implode("\t", $header) . "\r\n", 'UTF-16LE', 'UTF-8'));

// 각 행 출력
foreach ($list as $row) {

  $line = [
    $row['mt_id'],
    $row['mt_name'],
    format_phone($row['mt_hp']),
    $row['mt_position'] ?? '',
    DateType($row['mt_wdate'], 6),
  ];

  fwrite($handle, mb_convert_encoding(implode("\t", $line) . "\r\n", 'UTF-16LE', 'UTF-8'));
}

fclose($handle);
exit;
