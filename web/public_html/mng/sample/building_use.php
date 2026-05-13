<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_WARNING);
ini_set("display_errors", 0);

include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
header('Content-Type: application/json; charset=utf-8');

try {
    // 건물용도 중복 제거 후 가져오기
    $sql = "SELECT DISTINCT building_use 
            FROM guesthouse_t 
            WHERE building_use IS NOT NULL 
              AND building_use != '' 
            ORDER BY building_use ASC";

    $result = $DB->rawQuery($sql);

    $items = array_map(function($row) {
        return $row['building_use'];
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
