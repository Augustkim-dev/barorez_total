<?php
include $_SERVER['DOCUMENT_ROOT'] . "/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/cfg/config.mng.inc.php";

header('Content-Type: application/json; charset=utf-8');

global $DB;

// 현재 선택된 매장 (없으면 전체)
$sh_idx = (int)($_SESSION['current_sh_idx'] ?? 0);

/**
 * ct_snapshot JSON 파싱
 * - 메뉴명 / 카테고리 / 수량 / 금액만 추출
 */
function parseSnapshotItems($snapshotJson): array
{
    $items = [];
    if (empty($snapshotJson)) return $items;

    $snap = json_decode($snapshotJson, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($snap)) {
        return $items;
    }

    $snapItems = $snap['items'] ?? $snap;
    if (!is_array($snapItems)) return $items;

    foreach ($snapItems as $it) {
        $qty        = (int)($it['quantity']       ?? $it['ct_quantity']    ?? 0);
        $unitPrice  = (int)($it['unit_price']     ?? $it['ct_price']       ?? 0);
        $totalPrice = (int)($it['total_price']    ?? $it['ct_total_price'] ?? 0);

        if ($qty <= 0) continue;
        if ($totalPrice <= 0 && $unitPrice > 0) {
            $totalPrice = $unitPrice * $qty;
        }

        $items[] = [
            'menu_name'   => $it['menu_name'] ?? ($it['sm_title'] ?? ''),
            'category'    => $it['category']   ?? '-',
            'quantity'    => $qty,
            'unit_price'  => $unitPrice,
            'total_price' => $totalPrice,
        ];
    }

    return $items;
}

/**
 * 주문 타입 판별
 */
function detectOrderType(array $row): string
{
    if (isset($row['rv_idx']) && (int)$row['rv_idx'] > 0) {
        return 'reservation';
    }
    if (!empty($row['ot_table'])) {
        return 'table';
    }
    return 'takeout';
}

/**
 * 상태 라벨 매핑
 */
function mapStatusLabel(string $status): string
{
    switch ($status) {
        case 'PENDING':
            return '접수대기';
        case 'CONFIRMED':
            return '접수완료';
        case 'PREPARING':
            return '조리중';
        case 'COMPLETED':
            return '완료';
        case 'CANCELLED':
            return '취소';
        default:
            return $status;
    }
}

/**
 * 상대 시간 문자열 (2분 전, 3시간 전 등)
 */
function timeAgo(string $datetime): string
{
    $ts = strtotime($datetime);
    if (!$ts) return $datetime;

    $diff = time() - $ts;

    if ($diff < 60) {
        return '방금 전';
    } elseif ($diff < 3600) {
        $m = floor($diff / 60);
        return $m . '분 전';
    } elseif ($diff < 86400) {
        $h = floor($diff / 3600);
        return $h . '시간 전';
    } else {
        return date('Y.m.d H:i', $ts);
    }
}

try {
    $act = $_POST['act'] ?? '';

    // ========================================
    // 1) 대시보드 차트 + 카드 + 인기메뉴
    // ========================================
    if ($act === 'chart') {
        $start = $_POST['start_date'] ?? date('Y-m-d', strtotime('-6 days'));
        $end   = $_POST['end_date']   ?? date('Y-m-d');

        if ($start > $end) {
            $tmp   = $start;
            $start = $end;
            $end   = $tmp;
        }

        $today     = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));

        // -----------------------------
        // A. 기간 내 주문 조회 (매출/차트/인기메뉴용)
        //    → 결제 완료(PAID) + 취소 제외
        // -----------------------------
        if ($sh_idx > 0) {
            $DB->where('sh_idx', $sh_idx);
        }
        $DB->where('ot_status', 'CANCELLED', '!=');
        $DB->where('ot_pay_status', 'PAID');
        $DB->where('DATE(ot_wdate)', $start, '>=');
        $DB->where('DATE(ot_wdate)', $end,   '<=');
        $DB->orderBy('ot_wdate', 'ASC');

        $rows = $DB->get('orders_t', null, '
            idx,
            sh_idx,
            rv_idx,
            ot_number,
            ot_status,
            ot_table,
            ot_total_price,
            ct_snapshot,
            ot_wdate,
            ot_pay_status
        ');
        $ordersRange = $rows ?: [];

        // -----------------------------
        // B. 오늘 주문(카드용)
        //    → 운영상 주문 전체 (결제 완료/미완료 모두, 취소 제외)
        // -----------------------------
        if ($sh_idx > 0) {
            $DB->where('sh_idx', $sh_idx);
        }
        $DB->where('ot_status', 'CANCELLED', '!=');
        $DB->where('DATE(ot_wdate)', $today);
        $DB->orderBy('ot_wdate', 'ASC');

        $todayRows = $DB->get('orders_t', null, '
            idx,
            sh_idx,
            rv_idx,
            ot_number,
            ot_status,
            ot_table,
            ot_total_price,
            ct_snapshot,
            ot_wdate,
            ot_pay_type,
            ot_pay_status
        ');
        $todayRows = $todayRows ?: [];

        // -----------------------------
        // C. 어제 매출 (증감률 계산용)
        //    → 결제 완료(PAID)만 합산
        // -----------------------------
        if ($sh_idx > 0) {
            $DB->where('sh_idx', $sh_idx);
        }
        $DB->where('ot_status', 'CANCELLED', '!=');
        $DB->where('DATE(ot_wdate)', $yesterday);
        $DB->where('ot_pay_status', 'PAID');
        $yesterdayRows = $DB->get('orders_t', null, 'ot_total_price');
        $yesterdayRows = $yesterdayRows ?: [];

        // -----------------------------
        // D. 카드 집계
        // -----------------------------
        $todaySales        = 0;  // 오늘 매출(결제 완료 기준)
        $todayTotalOrders  = 0;  // 오늘 주문 수(취소 제외 전체)
        $todayPending      = 0;  // 오늘 처리 대기(상태 기준)
        $todayReservation  = 0;
        $todayTable        = 0;
        $todayTakeout      = 0;
        $todayTakeoutWait  = 0;  // 포장 중 조리/대기 수

        foreach ($todayRows as $r) {
            $amount    = (int)($r['ot_total_price'] ?? 0);
            $status    = $r['ot_status'] ?? '';
            $type      = detectOrderType($r);
            $payStatus = $r['ot_pay_status'] ?? 'UNPAID';

            // 오늘 전체 주문 수
            $todayTotalOrders++;

            // 결제 완료된 주문만 매출로 합산
            if ($payStatus === 'PAID') {
                $todaySales += $amount;
            }

            // 상태 기준 처리 대기
            if (in_array($status, ['PENDING', 'CONFIRMED', 'PREPARING'], true)) {
                $todayPending++;

                // 포장 주문 대기/조리 수
                if ($type === 'takeout') {
                    $todayTakeoutWait++;
                }
            }

            // 예약/테이블/포장 수량 (결제 상태 상관없이 운영 관점)
            if ($type === 'reservation') $todayReservation++;
            if ($type === 'table')       $todayTable++;
            if ($type === 'takeout')     $todayTakeout++;
        }

        // 어제 매출 (결제 완료 기준)
        $yesterdaySales = 0;
        foreach ($yesterdayRows as $r) {
            $yesterdaySales += (int)($r['ot_total_price'] ?? 0);
        }

        $todaySalesDiffRate = null;
        if ($yesterdaySales > 0) {
            $todaySalesDiffRate = round((($todaySales - $yesterdaySales) / $yesterdaySales) * 100, 1);
        }

        // -----------------------------
        // E. 차트 & 인기 메뉴 집계
        //    (기간 내 결제 완료 주문만 기준)
        // -----------------------------
        $weekdayLabels = ['월', '화', '수', '목', '금', '토', '일'];
        $chartAgg = [];
        foreach ($weekdayLabels as $w) {
            $chartAgg[$w] = [
                'reservation' => 0,
                'table'       => 0,
                'takeout'     => 0,
            ];
        }

        $itemAgg = []; // 인기 메뉴용

        foreach ($ordersRange as $o) {
            if (empty($o['ot_wdate'])) continue;

            $orderDate = date('Y-m-d', strtotime($o['ot_wdate']));
            if ($orderDate < $start || $orderDate > $end) continue;

            $amount = (int)($o['ot_total_price'] ?? 0);
            $type   = detectOrderType($o);

            // 요일 집계 (매출 기준)
            $wNum = (int)date('N', strtotime($orderDate)); // 1=월 ~ 7=일
            $wKor = $weekdayLabels[$wNum - 1];

            if (!isset($chartAgg[$wKor])) {
                $chartAgg[$wKor] = [
                    'reservation' => 0,
                    'table'       => 0,
                    'takeout'     => 0,
                ];
            }
            $chartAgg[$wKor][$type] += $amount;

            // 인기 메뉴 집계 (ct_snapshot 기준)
            $items = parseSnapshotItems($o['ct_snapshot']);
            foreach ($items as $it) {
                $key = $it['menu_name'] ?: '이름없는메뉴';
                if (!isset($itemAgg[$key])) {
                    $itemAgg[$key] = [
                        'name'     => $it['menu_name'],
                        'category' => $it['category'] ?? '-',
                        'qty'      => 0,
                        'sales'    => 0,
                    ];
                }
                $itemAgg[$key]['qty']   += $it['quantity'];
                $itemAgg[$key]['sales'] += $it['total_price'];
            }
        }

        // 차트 데이터 구성
        $labels      = $weekdayLabels;
        $dataReserve = [];
        $dataTable   = [];
        $dataTakeout = [];

        foreach ($weekdayLabels as $wKor) {
            $row = $chartAgg[$wKor] ?? ['reservation'=>0,'table'=>0,'takeout'=>0];
            $dataReserve[] = $row['reservation'];
            $dataTable[]   = $row['table'];
            $dataTakeout[] = $row['takeout'];
        }

        $chartData = [
            'labels'   => $labels,
            'datasets' => [
                [
                    'label' => '예약',
                    'data'  => $dataReserve,
                ],
                [
                    'label' => '테이블',
                    'data'  => $dataTable,
                ],
                [
                    'label' => '포장',
                    'data'  => $dataTakeout,
                ],
            ],
        ];

        // 인기 메뉴 TOP 5 (매출 기준)
        $items = array_values($itemAgg);
        usort($items, function ($a, $b) {
            return $b['sales'] <=> $a['sales'];
        });

        $bestItems = [];
        $rank = 1;
        foreach ($items as $it) {
            if ($rank > 5) break;
            $bestItems[] = [
                'rank'     => $rank,
                'name'     => $it['name'],
                'category' => $it['category'],
                'qty'      => $it['qty'],
                'sales'    => $it['sales'],
            ];
            $rank++;
        }

        $cards = [
            'today_sales'           => $todaySales,          // 오늘 매출(결제 완료)
            'today_sales_diff_rate' => $todaySalesDiffRate,  // 어제 대비 증감률
            'today_total_orders'    => $todayTotalOrders,    // 오늘 주문 수(취소 제외 전체)
            'today_pending_orders'  => $todayPending,        // 처리 대기 수
            'today_reservation'     => $todayReservation,
            'today_table'           => $todayTable,
            'today_takeout'         => $todayTakeout,
            'today_takeout_wait'    => $todayTakeoutWait,
        ];

        echo json_encode([
            'success'     => true,
            'message'     => '대시보드 차트/카드 데이터',
            'cards'       => $cards,
            'chart'       => $chartData,
            'best_items'  => $bestItems,
            'date_range'  => [
                'start_date' => $start,
                'end_date'   => $end,
            ],
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ========================================
    // 2) 최근 주문 리스트
    // ========================================
    if ($act === 'latest') {

        if ($sh_idx > 0) {
            $DB->where('sh_idx', $sh_idx);
        }
        $DB->where('ot_status', 'CANCELLED', '!=');
        $DB->orderBy('ot_wdate', 'DESC');

        // 최근 주문 10건 (결제 상태와 무관, 취소만 제외)
        $rows = $DB->get('orders_t', 10, '
            idx,
            sh_idx,
            rv_idx,
            ot_number,
            ot_status,
            ot_table,
            ot_total_price,
            ot_wdate,
            ot_pay_type,
            ot_pay_status
        ');
        $rows = $rows ?: [];

        $latestOrders = [];
        foreach ($rows as $r) {
            $type      = detectOrderType($r);
            $typeLabel = $type === 'reservation' ? '예약' : ($type === 'table' ? '테이블' : '포장');
            $status    = $r['ot_status'] ?? '';
            $statusLbl = mapStatusLabel($status);

            $latestOrders[] = [
                'order_no'     => $r['ot_number'],
                'type'         => $type,
                'type_label'   => $typeLabel,
                'table'        => $r['ot_table'] ?: '-',
                'amount'       => (int)($r['ot_total_price'] ?? 0),
                'status'       => $status,
                'status_label' => $statusLbl,
                'time_ago'     => timeAgo($r['ot_wdate']),
                // 필요하면 프론트에서 쓸 수 있게 결제 정보도 같이 넘겨둠
                'pay_type'     => $r['ot_pay_type']   ?? null, // PREPAID / POSTPAID
                'pay_status'   => $r['ot_pay_status'] ?? null, // UNPAID / PAID / REFUND
            ];
        }

        echo json_encode([
            'success'       => true,
            'message'       => '최근 주문 리스트',
            'latest_orders' => $latestOrders,
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // 그 외 act
    echo json_encode([
        'success' => false,
        'message' => '지원하지 않는 요청입니다.',
    ], JSON_UNESCAPED_UNICODE);
    exit;

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => '예외가 발생했습니다: ' . $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
