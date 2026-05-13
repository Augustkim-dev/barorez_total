<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";

header('Content-Type: application/json; charset=utf-8');

$sh_idx = (int)($_SESSION['current_sh_idx'] ?? 0);
$act    = $_POST['act'] ?? '';

if ($sh_idx <= 0) {
    echo json_encode(['success'=>false, 'message'=>'매장 정보 없음'], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($act === 'get_statistics') {
    try {
        $period     = $_POST['period'] ?? 'all';
        $start_date = trim($_POST['start_date'] ?? '');
        $end_date   = trim($_POST['end_date'] ?? '');
        $graph_type = $_POST['graph_type'] ?? 'order';

        // ────────────────────────────────────────────────
        // 기본 통계용 날짜 조건 (문자열 직접 치환)
        // ────────────────────────────────────────────────
        $date_where = '';
        if ($period === 'today') {
            $date_where = "AND DATE(ot_wdate) = CURDATE()";
        } elseif ($period === '3days') {
            $date_where = "AND ot_wdate >= DATE_SUB(CURDATE(), INTERVAL 3 DAY)";
        } elseif ($period === '7days') {
            $date_where = "AND ot_wdate >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        } elseif ($period === '30days') {
            $date_where = "AND ot_wdate >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        } elseif ($period === 'custom' && $start_date && $end_date) {
            $date_where = "AND ot_wdate BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59'";
        }

        $base_where = "WHERE sh_idx = $sh_idx AND ot_pay_status = 'PAID' $date_where";

        // 기본 통계
        $total_sales = (int)($DB->rawQueryOne("
            SELECT COALESCE(SUM(ot_total_price - ot_discount_amount), 0) as total 
            FROM orders_t $base_where
        ")['total'] ?? 0);

        $total_orders = (int)($DB->rawQueryOne("
            SELECT COUNT(*) as cnt 
            FROM orders_t $base_where
        ")['cnt'] ?? 0);

        $avg_order_amount = $total_orders > 0 ? round($total_sales / $total_orders) : 0;

        // ────────────────────────────────────────────────
        // 베스트 10위 (컬럼 이름 변수화)
        // ────────────────────────────────────────────────
        $best_menus = [];
        $snapshot_column = 'ct_snapshot'; // ← 확인된 컬럼 이름으로 유지 (필요 시 변경)

        $orders = $DB->rawQuery("SELECT $snapshot_column FROM orders_t $base_where");

        $menu_stats = [];
        foreach ($orders as $order) {
            $snapshot_str = $order[$snapshot_column] ?? '{}';
            $snapshot = json_decode($snapshot_str, true);

            if (json_last_error() !== JSON_ERROR_NONE || !isset($snapshot['items']) || !is_array($snapshot['items'])) {
                continue;
            }

            foreach ($snapshot['items'] as $item) {
                $sm_id = (int)($item['sm_id'] ?? 0);
                $name  = trim($item['menu_name'] ?? '알수없음');
                $qty   = (int)($item['quantity'] ?? 0);

                if ($sm_id > 0 && $qty > 0) {
                    $key = $sm_id . '|' . $name;
                    $menu_stats[$key] = ($menu_stats[$key] ?? 0) + $qty;
                }
            }
        }

        arsort($menu_stats);
        $top10 = array_slice($menu_stats, 0, 10, true);

        foreach ($top10 as $key => $qty) {
            [$sm_id, $name] = explode('|', $key, 2);
            $best_menus[] = [
                'sm_idx'     => (int)$sm_id,
                'menu_name'  => $name,
                'quantity'   => $qty
            ];
        }

        // ────────────────────────────────────────────────
        // 그래프 데이터 → 이번 주 고정 (문자열 치환)
        // ────────────────────────────────────────────────
        $days = ['월', '화', '수', '목', '금', '토', '일'];
        $day_map = ['Monday'=>'월', 'Tuesday'=>'화', 'Wednesday'=>'수', 'Thursday'=>'목',
            'Friday'=>'금', 'Saturday'=>'토', 'Sunday'=>'일'];

        $table_data = array_fill(0, 7, 0);
        $order_data = array_fill(0, 7, 0);
        $reserve_data = array_fill(0, 7, 0);

        $this_week_start = date('Y-m-d 00:00:00', strtotime('monday this week'));
        $this_week_end   = date('Y-m-d 23:59:59', strtotime('sunday this week'));

        $graph_where = "WHERE sh_idx = $sh_idx AND ot_pay_status = 'PAID' 
                        AND ot_wdate BETWEEN '$this_week_start' AND '$this_week_end'";

        $sql = "
            SELECT 
                DAYNAME(ot_wdate) as dayname,
                CASE 
                    WHEN ot_table IS NOT NULL AND ot_table != '' THEN 'table'
                    WHEN rv_idx IS NOT NULL THEN 'reserve'
                    ELSE 'order'
                END as order_type,
                COUNT(*) as cnt,
                COALESCE(SUM(ot_total_price - ot_discount_amount), 0) as sales
            FROM orders_t 
            $graph_where 
            GROUP BY dayname, order_type
            ORDER BY FIELD(dayname, 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')
        ";

        $graph_rows = $DB->rawQuery($sql);

        foreach ($graph_rows as $row) {
            $day_kr = $day_map[$row['dayname']] ?? '기타';
            $idx = array_search($day_kr, $days);
            if ($idx === false) $idx = 6;

            $value = ($graph_type === 'sales') ? (float)$row['sales'] : (int)$row['cnt'];

            if ($row['order_type'] === 'table') {
                $table_data[$idx] += $value;
            } elseif ($row['order_type'] === 'reserve') {
                $reserve_data[$idx] += $value;
            } else {
                $order_data[$idx] += $value;
            }
        }

        $graph_data = [
            'labels' => $days,
            'datasets' => [
                [
                    'label' => '테이블',
                    'data'  => $table_data,
                    'backgroundColor' => 'rgba(255, 99, 132, 0.7)',
                    'stack' => 'stack'
                ],
                [
                    'label' => '주문',
                    'data'  => $order_data,
                    'backgroundColor' => 'rgba(255, 159, 64, 0.7)',
                    'stack' => 'stack'
                ],
                [
                    'label' => '예약',
                    'data'  => $reserve_data,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.7)',
                    'stack' => 'stack'
                ]
            ]
        ];

        echo json_encode([
            'success'           => true,
            'total_sales'       => $total_sales,
            'total_orders'      => $total_orders,
            'avg_order_amount'  => $avg_order_amount,
            'best_menus'        => $best_menus,
            'graph_data'        => $graph_data,
            'graph_type'        => $graph_type
        ], JSON_UNESCAPED_UNICODE);

    } catch (Exception $e) {
        error_log("get_statistics error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => '서버 오류: ' . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
    exit;
}
