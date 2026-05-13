<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";

header('Content-Type: application/json; charset=utf-8');

$sh_idx = (int)($_SESSION['current_sh_idx'] ?? 0);

if ($sh_idx <= 0) {
    echo json_encode(['success'=>false, 'message'=>'선택된 매장이 없습니다.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$act = $_POST['act'] ?? '';

if ($act === 'list') {
    try {
        $status     = $_POST['status'] ?? 'all';
        $period     = $_POST['period'] ?? 'all';
        $start_date = trim($_POST['start_date'] ?? '');
        $end_date   = trim($_POST['end_date'] ?? '');
        $pg         = max(1, (int)($_POST['pg'] ?? 1));
        $pageSize   = 10;

        $date_where = '';
        $params = [$sh_idx];

        if ($period === 'today') {
            $date_where = "AND DATE(st_plan_date) = CURDATE()";
        } elseif ($period === '3days') {
            $date_where = "AND st_plan_date >= DATE_SUB(CURDATE(), INTERVAL 2 DAY)";
        } elseif ($period === '7days') {
            $date_where = "AND st_plan_date >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)";
        } elseif ($period === '30days') {
            $date_where = "AND st_plan_date >= DATE_SUB(CURDATE(), INTERVAL 29 DAY)";
        } elseif ($period === 'custom' && $start_date && $end_date) {
            $date_where = "AND st_plan_date BETWEEN ? AND ?";
            $params[] = $start_date;
            $params[] = $end_date;
        }

        $status_where = '';
        if ($status !== 'all' && in_array($status, ['READY','PLANNED','DONE'])) {
            $status_where = "AND st_status = ?";
            $params[] = $status;
        }

        $base_where = "WHERE sh_idx = ? $date_where $status_where";

        // 총 개수
        $total_count = $DB->rawQueryOne("SELECT COUNT(*) as cnt FROM settle_t $base_where", $params)['cnt'] ?? 0;
        $total_pages = ceil($total_count / $pageSize);

        // 리스트 조회
        $limit_offset = ($pg - 1) * $pageSize;
        $list = $DB->rawQuery("
            SELECT 
                st_number, st_plan_date, st_total_amount, st_service_fee, st_final_amount,
                st_start_date, st_end_date, st_status
            FROM settle_t 
            $base_where 
            ORDER BY st_plan_date DESC, st_wdate DESC, idx DESC 
            LIMIT $limit_offset, $pageSize
        ", $params);

        // 데이터만 내려보냄 (HTML은 프론트에서 생성)
        echo json_encode([
            'success'     => true,
            'data'        => $list,
            'total_pages' => $total_pages,
            'current_page'=> $pg
        ], JSON_UNESCAPED_UNICODE);

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

// 잘못된 요청
echo json_encode(['success'=>false, 'message'=>'잘못된 요청입니다.'], JSON_UNESCAPED_UNICODE);
exit;
