<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_WARNING);
ini_set("display_errors", 0);

include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";

header('Content-Type: application/json; charset=utf-8');

try {
    $depth = isset($_GET['depth']) ? intval($_GET['depth']) : 0;  // 0: 시/도, 1: 시/군/구, 2: 읍/면/동
    $parent = isset($_GET['parent']) ? trim($_GET['parent']) : null;

    if ($depth < 0 || $depth > 2) {
        throw new Exception("잘못된 depth 값입니다.");
    }

    // 쿼리 분기
    if ($depth === 0) {
        // 시/도
        $sql = "SELECT DISTINCT SUBSTRING_INDEX(addr_road, ' ', 1) AS name
                FROM guesthouse_t
                WHERE addr_road IS NOT NULL AND addr_road != ''
                ORDER BY name";
        $rows = $DB->rawQuery($sql);
    } elseif ($depth === 1 && $parent) {
        // 시/군/구
        $sql = "SELECT DISTINCT SUBSTRING_INDEX(SUBSTRING_INDEX(addr_road, ' ', 2), ' ', -1) AS name
                FROM guesthouse_t
                WHERE addr_road LIKE ? 
                ORDER BY name";
        $rows = $DB->rawQuery($sql, [$parent.'%']);
    } elseif ($depth === 2 && $parent) {
        // 읍/면/동
        $sql = "SELECT DISTINCT SUBSTRING_INDEX(SUBSTRING_INDEX(addr_road, ' ', 3), ' ', -1) AS name
                FROM guesthouse_t
                WHERE addr_road LIKE ? 
                ORDER BY name";
        $rows = $DB->rawQuery($sql, [$parent.'%']);
    } else {
        $rows = [];
    }

    $data = array_map(fn($row) => $row['name'], $rows);

    echo json_encode([
        'success' => true,
        'depth'   => $depth,
        'items'   => $data,
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
