<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";



// 엑셀로 출력할 헤더 설정
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=membership_list_" . date('Y_m_d_H_i_s') . ".xls");
header("Content-Description: PHP Generated Data");
header("Pragma: no-cache");
header("Expires: 0");


$p1 = $_GET['p1'] ?? ''; // 검색조건
$p2 = $_GET['p2'] ?? ''; // 검색어
$p3 = $_GET['p3'] ?? ''; // 선택등급


$where = "";
$where .= " AND instr($p1, ?)";
$params = [$p2];

if ($p3) {
  $where .= " AND a1.gmt_local = ?";
  $params[] = $p3;
}


$sql = "
  SELECT 
    a1.*, 
    a1.gmt_idx AS nt_idx,

    (SELECT COUNT(*) 
     FROM {$CFG_TBL['golf_membership']['transaction']} 
     WHERE gmt_idx = a1.gmt_idx AND gmtt_status = 1 AND gmtt_del = 'N' AND gmtt_show = 'Y') AS gmtt_type1,

    (SELECT COUNT(*) 
     FROM {$CFG_TBL['golf_membership']['transaction']} 
     WHERE gmt_idx = a1.gmt_idx AND gmtt_status = 2 AND gmtt_del = 'N' AND gmtt_show = 'Y') AS gmtt_type2,

    (SELECT COUNT(*) 
     FROM {$CFG_TBL['golf_membership']['transaction']} 
     WHERE gmt_idx = a1.gmt_idx AND gmtt_status = 3 AND gmtt_del = 'N' AND gmtt_show = 'Y') AS gmtt_type3

  FROM {$CFG_TBL['golf_membership']['main']} a1
  WHERE a1.gmt_del = 'N' $where
  ORDER BY a1.gmt_idx DESC
";

$list = $DB->rawQuery($sql, $params);

// 인코딩 깨짐 방지 (엑셀용 UTF-8 BOM)
echo chr(255) . chr(254); // UTF-16LE BOM
$handle = fopen("php://output", "w");

// 헤더 출력
$header = ['지역', '골프장명', '분양가', '즉시구매가', '즉시판매가', '체결가능금액', '체결대기', '체결진행', '체결완료', '노출여부'];
fwrite($handle, mb_convert_encoding(implode("\t", $header) . "\r\n", 'UTF-16LE', 'UTF-8'));

// 각 행 출력
foreach ($list as $row) {
  $prices = getGolfPrice($row['gmt_idx']);


  $line = [
    $arr_gmt_local_type[$row['gmt_local']],
    $row['gmt_golf_name'],
    $row['gmt_sale_price'],
    $prices['gmt_now_buy_price'],
    $prices['gmt_now_sale_price'],
    $prices['gmt_conclusion_price'],
    $row['gmtt_type1'],
    $row['gmtt_type2'],
    $row['gmtt_type3'],
    $row['gmt_show'],
  ];

  fwrite($handle, mb_convert_encoding(implode("\t", $line) . "\r\n", 'UTF-16LE', 'UTF-8'));
}

fclose($handle);
exit;
