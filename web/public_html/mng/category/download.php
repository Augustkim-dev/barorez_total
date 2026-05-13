<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";
require_once $_SERVER['DOCUMENT_ROOT']."/vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

try {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // 헤더 작성
    $sheet->fromArray([
        ['차량구분','제조사', '모델 1차', '모델 2차', '연식 시작일', '연식 종료일', '등급']
    ], null, 'A1');

    // 데이터 구성
    $sql = "
        SELECT 
            og.og_name AS origin,
            mf.mf_name AS manufacturer,
            mo.md_name AS model1,
            mv.mv_name AS model2,
            mv.mv_start AS startYear,
            mv.mv_end AS endYear,
            ct.cs_name AS class1
        FROM origin_t og 
        LEFT JOIN manufacturers_t mf ON mf.og_id = og.id
        LEFT JOIN model_t mo ON mo.mf_id = mf.id
        LEFT JOIN model_variants_t mv ON mv.md_id = mo.id
        LEFT JOIN class_t ct ON ct.mv_id = mv.id
        ORDER BY og.og_name, mf.mf_name, mo.md_name, mv.mv_name, ct.cs_name
    ";
    $data = $DB->rawQuery($sql);

    $rowIndex = 2;
    foreach ($data as $row) {
        $sheet->fromArray([
            $row['origin'] ?? '-',
            $row['manufacturer'] ?? '-',
            $row['model1'] ?? '-',
            $row['model2'] ?? '-',
            $row['startYear'] ?? '-',
            $row['endYear'] ?? '-',
            $row['class1'] ?? '-',
        ], null, 'A' . $rowIndex++);
    }

    $today = date('Ymd');
    $filename = "유토피아_차량_{$today}.xlsx";
    $encodedFilename = rawurlencode($filename);

    // 파일 다운로드 헤더
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"{$encodedFilename}\"; filename*=UTF-8''{$encodedFilename}");
    header('Cache-Control: max-age=0');

    // 출력 후 종료
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;

} catch (Exception $e) {
    // 에러는 JSON으로 반환할 수 있지만 실제 다운로드 요청에서는 에러 텍스트로만 응답할 것
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'sql' => $sql ?? null
    ]);
}
