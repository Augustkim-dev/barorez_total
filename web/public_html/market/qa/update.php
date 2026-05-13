<?php
// ./update.php  (기존 파일에 아래 코드 추가 또는 통합)

include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";

header('Content-Type: application/json; charset=utf-8');

global $DB;

// 로그인 체크 (필요한 방식으로 변경 가능)
if (!isset($_SESSION['mng']['mt_idx']) || (int)$_SESSION['mng']['mt_idx'] <= 0) {
    echo json_encode(['success'=>false, 'message'=>'로그인이 필요합니다.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$mt_idx = (int)$_SESSION['mng']['mt_idx'];
$act = $_POST['act'] ?? '';

if ($act === 'qa_list') {
    try {
        $status     = trim($_POST['status'] ?? 'all');
        $period     = trim($_POST['period'] ?? 'all');
        $start_date = trim($_POST['start_date'] ?? '');
        $end_date   = trim($_POST['end_date'] ?? '');
        $pg         = max(1, (int)($_POST['pg'] ?? 1));

        $pageSize   = 10;           // 페이지당 건수 (필요시 조정)

        $where = [];
        $params = [];

        // 1. 회원 본인 글만
        $where[] = "mt_idx = ?";
        $params[] = $mt_idx;

        // 2. 상태 필터
        if ($status !== 'all' && in_array($status, ['pending', 'answered'])) {
            $where[] = "rt_status = ?";
            $params[] = $status;
        }

        // 3. 기간 필터
        if ($period === 'today') {
            $where[] = "DATE(created_at) = CURDATE()";
        } elseif ($period === '3days') {
            $where[] = "created_at >= DATE_SUB(CURDATE(), INTERVAL 2 DAY)";
        } elseif ($period === '7days') {
            $where[] = "created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)";
        } elseif ($period === '30days') {
            $where[] = "created_at >= DATE_SUB(CURDATE(), INTERVAL 29 DAY)";
        } elseif ($period === 'custom' && $start_date && $end_date) {
            $where[] = "created_at BETWEEN ? AND ?";
            $params[] = $start_date . ' 00:00:00';
            $params[] = $end_date . ' 23:59:59';
        }

        $where_clause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        // 총 건수
        $total_count = $DB->rawQueryOne("
            SELECT COUNT(*) as cnt 
            FROM qa_t 
            $where_clause
        ", $params)['cnt'] ?? 0;

        $total_pages = ceil($total_count / $pageSize);

        // 리스트 조회
        $limit_offset = ($pg - 1) * $pageSize;

        $list = $DB->rawQuery("
            SELECT 
                idx,
                rt_title,
                rt_status,
                DATE_FORMAT(created_at, '%Y.%m.%d %H:%i') as created_at_fmt,
                created_at
            FROM qa_t 
            $where_clause 
            ORDER BY idx DESC 
            LIMIT ?, ?
        ", array_merge($params, [$limit_offset, $pageSize]));

        echo json_encode([
            'success'      => true,
            'data'         => $list,
            'total_pages'  => (int)$total_pages,
            'current_page' => $pg,
            'total_count'  => (int)$total_count
        ], JSON_UNESCAPED_UNICODE);

    } catch (Exception $e) {
        error_log("qa_list error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => '조회 중 오류가 발생했습니다.'
        ], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

// 기존 다른 act 처리 코드들...

// 기본 응답
echo json_encode(['success'=>false, 'message'=>'잘못된 요청입니다.'], JSON_UNESCAPED_UNICODE);
exit;
