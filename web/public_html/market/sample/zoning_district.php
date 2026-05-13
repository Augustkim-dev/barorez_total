<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_WARNING);
ini_set("display_errors", 0);

include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
header('Content-Type: application/json; charset=utf-8');

try {
    // 용도지구 중복 제거 후 가져오기
    $sql = "SELECT DISTINCT zoning_district 
            FROM guesthouse_t 
            WHERE zoning_district IS NOT NULL 
              AND zoning_district != '' 
            ORDER BY zoning_district ASC";

    $result = $DB->rawQuery($sql);

    $items = array_map(function($row) {
        return $row['zoning_district'];
    }, $result);

    echo json_encode([
        'success' => true,
        'items'   => $items,
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
}
