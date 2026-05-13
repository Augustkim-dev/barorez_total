<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";

header('Content-Type: application/json; charset=utf-8');

$act    = $_POST['act'] ?? '';
$sh_idx = (int)($_SESSION['current_sh_idx'] ?? 0);

if ($sh_idx <= 0) {
    echo json_encode(['success'=>false, 'message'=>'매장키(sh_idx) 세션이 없습니다.'], JSON_UNESCAPED_UNICODE);
    exit;
}

// ✅ 테스트: 테이블 1~8 (실제 테이블 마스터가 있으면 그걸로 교체)
// 0) 테이블 마스터 가져오기 (shop_table_t)
$DB->where('sh_idx', $sh_idx);
$DB->where('use_yn', 'Y');
$DB->orderBy('tb_no', 'ASC');     // 정렬용 번호 우선
$DB->orderBy('idx', 'ASC');       // tb_no가 NULL인 경우 대비

$tablesMaster = $DB->get('shop_table_t', null, 'idx, tb_name, tb_no, tb_seats');

$ALL_TABLES = []; // "표시 순서대로" 카드 생성 기준
foreach ($tablesMaster as $t) {
    // tv_table과 맞춰야 하므로 "테이블 번호"를 어떤 값으로 쓸지 결정 필요
    // ✅ 지금 시스템에서 tv_table이 "1", "2" 처럼 번호 문자열이라면 tb_no를 쓰는게 자연스럽습니다.
    // tb_no가 NULL이면 idx로 대체(테스트용)하거나 tb_name에서 숫자 추출 등 정책 필요.
    $no = (string)($t['tb_no'] ?? '');
    if ($no === '') {
        // tb_no가 비어있으면 idx로 임시 매핑(권장X, 운영에선 tb_no 필수 권장)
        $no = (string)$t['idx'];
    }
    $ALL_TABLES[] = [
        'table_no'   => $no,
        'table_name' => $t['tb_name'] ?? ('테이블 '.$no),
        'seats'      => (int)($t['tb_seats'] ?? 2),
    ];
}

/**
 * ✅ 주문 상태 호환
 * - 기존: PENDING/CONFIRMED/PREPARING/COMPLETED/CANCELLED
 * - 신규 컨셉: RECEIVED/PREPARING/SERVED/CANCELLED
 * 여기서는 기존 DB를 깨지 않기 위해 "PENDING을 RECEIVED로", "COMPLETED를 SERVED로"처럼 해석합니다.
 */
function normalize_status($st) {
    if ($st === 'RECEIVED') return 'RECEIVED';
    if ($st === 'SERVED') return 'SERVED';
    if ($st === 'PENDING') return 'RECEIVED';
    if ($st === 'COMPLETED') return 'SERVED';
    return $st ?: 'RECEIVED';
}

function status_label($st) {
    if ($st === 'RECEIVED') return '주문접수';
    if ($st === 'PREPARING') return '음식준비중';
    if ($st === 'SERVED') return '전달완료';
    return '빈자리';
}

$QR_LANDING_BASE = APP_PAGE; // 주문 랜딩 도메인
$QR_LANDING_QS   = 'tk';                          // 쿼리키: tk

function make_token($bytes = 32) {
    // 32 bytes => 64 hex chars
    return bin2hex(random_bytes($bytes));
}

function build_qr_text($base, $qsKey, $token) {
    // https://qrorder.epicque.com?tk=xxxx
    $sep = (strpos($base, '?') !== false) ? '&' : '?';
    return $base . $sep . $qsKey . '=' . $token;
}

function fetch_qr_image($qrText, $size = '260x260') {
    $encoded = urlencode($qrText);
    $qrApi   = "https://api.qrserver.com/v1/create-qr-code/?size={$size}&data={$encoded}";
    $imgData = @file_get_contents($qrApi);
    return $imgData;
}

// ✅ list: 테이블별 카드 데이터 반환
if ($act === 'list') {
    try {
        global $DB, $ALL_TABLES;

        // 1) ACTIVE 방문 세션 가져오기 (매장 기준)
        $DB->where('sh_idx', $sh_idx);
        $DB->where('tv_status', 'ACTIVE');
        $visits = $DB->get('table_visit_t', null, 'idx, tv_table, tv_started, tv_last_active, tv_last_seen_order_idx');

        $visitByTable = [];
        $tvIds = [];
        foreach ($visits as $v) {
            $tno = (string)($v['tv_table'] ?? '');
            if ($tno === '') continue;
            $visitByTable[$tno] = $v;
            $tvIds[] = (int)$v['idx'];
        }

        // 2) ACTIVE 세션들의 주문 가져오기
        $ordersByTv = [];
        if (!empty($tvIds)) {
            $DB->where('sh_idx', $sh_idx);
            $DB->where('tv_idx', $tvIds, 'IN');
            $DB->where('ot_cancel', null, 'IS');
            $DB->orderBy('ot_wdate', 'ASC');
            $rows = $DB->get('orders_t', null, 'idx, tv_idx, ot_status, ot_total_price, ct_snapshot, ot_wdate');

            foreach ($rows as $r) {
                $tid = (int)$r['tv_idx'];
                if (!isset($ordersByTv[$tid])) $ordersByTv[$tid] = [];
                $ordersByTv[$tid][] = $r;
            }
        }

        // 3) 테이블 카드 만들기
        $tables = [];
        foreach ($ALL_TABLES as $tm) {
            $tableNo   = (string)$tm['table_no'];
            $tableName = (string)$tm['table_name'];

            $visit = $visitByTable[$tableNo] ?? null;

            if (!$visit) {
                $tables[] = [
                    'table_no' => (int)$tableNo,
                    'table_name' => $tableName,
                    'tv_idx'   => 0,
                    'status'   => 'EMPTY',
                    'status_label' => '빈자리',
                    'items_summary' => '',
                    'total_price' => 0,
                    'has_new' => false,
                    'latest_order_idx' => 0,
                    'elapsed' => '',
                ];
                continue;
            }

            $tv_idx = (int)$visit['idx'];
            $orders = $ordersByTv[$tv_idx] ?? [];

            // 주문이 아직 없는 ACTIVE 세션
            if (empty($orders)) {
                $tables[] = [
                    'table_no' => (int)$tableNo,
                    'table_name' => $tableName,
                    'tv_idx'   => $tv_idx,
                    'status'   => 'EMPTY',
                    'status_label' => '빈자리',
                    'items_summary' => '',
                    'total_price' => 0,
                    'has_new' => false,
                    'latest_order_idx' => 0,
                    'elapsed' => '',
                ];
                continue;
            }

            // 누적 계산
            $hasReceived  = false;
            $hasPreparing = false;
            $hasServed    = false;

            $latestOrderIdx = 0;
            $totalPrice = 0;
            $orderCount = 0;

            // 메뉴 요약(상위 1~2개만 표시)
            $menuCount = []; // name => qty

            foreach ($orders as $o) {
                $orderCount++;
                $oid = (int)$o['idx'];
                if ($oid > $latestOrderIdx) $latestOrderIdx = $oid;

                $totalPrice += (float)($o['ot_total_price'] ?? 0);

                $st = normalize_status($o['ot_status'] ?? '');
                if ($st === 'RECEIVED') $hasReceived = true;
                if ($st === 'PREPARING') $hasPreparing = true;
                if ($st === 'SERVED') $hasServed = true;

                // snapshot에서 메뉴 누적
                if (!empty($o['ct_snapshot'])) {
                    $snap = json_decode($o['ct_snapshot'], true);
                    $snapItems = $snap['items'] ?? $snap;
                    if (is_array($snapItems)) {
                        foreach ($snapItems as $it) {
                            $name = $it['menu_name'] ?? ($it['sm_title'] ?? '');
                            if ($name === '') continue;
                            $qty = (int)($it['quantity'] ?? $it['ct_quantity'] ?? 0);
                            if (!isset($menuCount[$name])) $menuCount[$name] = 0;
                            $menuCount[$name] += max(1, $qty);
                        }
                    }
                }
            }

            // 대표 상태 결정
            $status = 'SERVED';
            if ($hasReceived) $status = 'RECEIVED';
            else if ($hasPreparing) $status = 'PREPARING';
            else $status = 'SERVED';

            // ✅ has_new = (추가 주문이 있고) (현재 주문접수 상태이며) (사장님이 아직 확인처리하지 않은 주문이 존재)
            $seenIdx = (int)($visit['tv_last_seen_order_idx'] ?? 0);
            $hasNew = false;

            // ✅ baseline 처리: 기존 데이터/초기 적용 시 툴팁 도배 방지
            // seenIdx가 0이면 "현재 최신 주문까지는 이미 본 것으로" 처리하고 has_new는 false로 둔다.
            if ($seenIdx <= 0) {
                $DB->where('idx', $tv_idx);
                $DB->update('table_visit_t', [
                    'tv_last_seen_order_idx' => $latestOrderIdx,
                ]);
                $seenIdx = $latestOrderIdx;
                $hasNew = false;
            } else {
                if ($orderCount >= 2 && $status === 'RECEIVED' && $latestOrderIdx > $seenIdx) {
                    $hasNew = true;
                }
            }

            // 메뉴 요약 문자열
            arsort($menuCount);
            $summaryParts = [];
            $i = 0;
            foreach ($menuCount as $name => $qty) {
                $summaryParts[] = $name.' '.$qty.'개';
                $i++;
                if ($i >= 2) break;
            }
            $itemsSummary = implode(', ', $summaryParts);

            // 경과시간 (마지막 active 기준)
            $elapsed = '';
            if (!empty($visit['tv_last_active'])) {
                $ts = strtotime($visit['tv_last_active']);
                if ($ts) {
                    $diff = time() - $ts;
                    if ($diff < 60) $elapsed = $diff.'초 전';
                    else if ($diff < 3600) $elapsed = floor($diff/60).'분 전';
                    else $elapsed = floor($diff/3600).'시간 전';
                }
            }

            $tables[] = [
                'table_no' => (int)$tableNo,
                'table_name' => $tableName,
                'tv_idx'   => $tv_idx,
                'status'   => $status,
                'status_label' => status_label($status),
                'items_summary' => $itemsSummary,
                'total_price' => (int)$totalPrice,
                'has_new' => $hasNew,
                'latest_order_idx' => $latestOrderIdx,
                'elapsed' => $elapsed,
            ];
        }

        echo json_encode([
            'success' => true,
            'data' => ['tables' => $tables],
            'sql' => $DB->getLastQuery()
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        echo json_encode(['success'=>false, 'message'=>$e->getMessage()], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// ✅ action: prepare/serve/clear
if ($act === 'action') {
    try {
        $action = $_POST['action'] ?? '';
        $tv_idx = (int)($_POST['tv_idx'] ?? 0);

        if ($tv_idx <= 0) throw new Exception('tv_idx가 없습니다.');

        // 세션 소유 검증 (다른 매장 tv_idx 조작 방지)
        $DB->where('idx', $tv_idx);
        $DB->where('sh_idx', $sh_idx);
        $visit = $DB->getOne('table_visit_t', 'idx, tv_status');
        if (!$visit) throw new Exception('유효하지 않은 방문세션입니다.');

        if ($action === 'prepare') {
            // ✅ RECEIVED(PENDING 포함) -> PREPARING
            $DB->where('sh_idx', $sh_idx);
            $DB->where('tv_idx', $tv_idx);
            $DB->where('ot_status', ['PENDING','RECEIVED'], 'IN');
            $DB->update('orders_t', ['ot_status' => 'PREPARING', 'ot_udate' => $DB->now()]);

            // 방문 last_active 갱신
            $DB->where('tv_idx', $tv_idx);
            $DB->orderBy('idx', 'DESC');
            $last = $DB->getOne('orders_t', 'idx');

            $lastOrderIdx = (int)($last['idx'] ?? 0);
            if ($lastOrderIdx > 0) {
                $DB->where('idx', $tv_idx);
                $DB->update('table_visit_t', [
                    'tv_last_seen_order_idx' => $lastOrderIdx,
                ]);
            }

            echo json_encode(['success'=>true, 'message'=>'준비중으로 변경'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        if ($action === 'serve') {
            // ✅ PREPARING -> SERVED(또는 COMPLETED로 쓰고 있으면 COMPLETED로 변경해도 됨)
            $DB->where('sh_idx', $sh_idx);
            $DB->where('tv_idx', $tv_idx);
            $DB->where('ot_status', 'PREPARING');
            $DB->update('orders_t', ['ot_status' => 'COMPLETED', 'ot_udate' => $DB->now()]);

            $DB->where('idx', $tv_idx);
            $DB->update('table_visit_t', ['tv_last_active' => $DB->now()]);

            echo json_encode(['success'=>true, 'message'=>'전달완료로 변경'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        if ($action === 'clear') {
            // ✅ 좌석 비우기: 진행중 주문이 있으면 막는 정책
            $DB->where('sh_idx', $sh_idx);
            $DB->where('tv_idx', $tv_idx);
            $DB->where('ot_status', ['PENDING','RECEIVED','PREPARING'], 'IN');
            $cnt = (int)$DB->getValue('orders_t', 'COUNT(*)');
            if ($cnt > 0) throw new Exception('진행중 주문이 있어 좌석을 비울 수 없습니다.');

            $DB->where('sh_idx', $sh_idx);
            $DB->update('orders_t', ['ot_pay_status' => 'PAID', 'ot_pay_date' => $DB->now()]);


            $DB->where('idx', $tv_idx);
            $DB->update('table_visit_t', [
                'tv_status' => 'CLOSED',
                'tv_ended' => $DB->now(),
                'tv_last_active' => $DB->now()
            ]);

            echo json_encode(['success'=>true, 'message'=>'좌석 비우기 완료'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        throw new Exception('알 수 없는 action');

    } catch (Exception $e) {
        echo json_encode(['success'=>false, 'message'=>$e->getMessage()], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// ✅ mock_order: 테스트용 추가주문 생성
if ($act === 'mock_order') {
    try {
        $tableNo = trim($_POST['table_no'] ?? '');
        if ($tableNo === '') throw new Exception('table_no가 없습니다.');

        // 1) ACTIVE 세션이 없으면 생성
        $DB->where('sh_idx', $sh_idx);
        $DB->where('tv_table', $tableNo);
        $DB->where('tv_status', 'ACTIVE');
        $visit = $DB->getOne('table_visit_t', 'idx');

        if (!$visit) {
            $DB->insert('table_visit_t', [
                'visit_key' => bin2hex(random_bytes(16)),
                'sh_idx' => $sh_idx,
                'tv_table' => $tableNo,
                'mt_idx' => null,
                'tv_status' => 'ACTIVE',
                'tv_started' => $DB->now(),
                'tv_last_active' => $DB->now(),
                'tv_ended' => null,
                'tv_ip' => $_SERVER['REMOTE_ADDR'] ?? null,
                'tv_ua' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            ]);
            $tv_idx = (int)$DB->getInsertId();
        } else {
            $tv_idx = (int)$visit['idx'];
        }

        // 2) orders_t에 임시 주문 insert
        $ot_number = 'T'.$tableNo.'-'.date('YmdHis').'-'.rand(100,999);

        // 임시 snapshot
        $snap = [
            'items' => [
                ['menu_name' => '해물칼국수', 'quantity' => 1, 'unit_price' => 8000],
            ]
        ];

        $DB->insert('orders_t', [
            'mt_idx' => null,
            'sh_idx' => $sh_idx,
            'rv_idx' => null,
            'tv_idx' => $tv_idx,
            'ot_number' => $ot_number,
            'ot_status' => 'PENDING', // ✅ 새 주문은 접수(PENDING/RECEIVED)
            'ot_table' => $tableNo,
            'ot_total_price' => 8000,
            'cl_idx' => null,
            'ot_discount_amount' => 0,
            'ct_snapshot' => json_encode($snap, JSON_UNESCAPED_UNICODE),
            'ot_notes' => null,
            'ot_cancel' => null,
            'ot_cancel_reason' => null,
            'ot_wdate' => $DB->now(),
            'ot_udate' => $DB->now(),
            'ot_pay_type' => 'PREPAID',
            'ot_pay_status' => 'PAID',
            'ot_pay_date' => $DB->now(),
            'ot_settle_yn' => 'N',
            'ot_settle_date' => null,
            'st_idx' => null,
        ]);

        // 방문 last_active 갱신
        $DB->where('idx', $tv_idx);
        $DB->update('table_visit_t', ['tv_last_active' => $DB->now()]);

        echo json_encode(['success'=>true, 'message'=>'모의 추가주문 생성', 'tv_idx'=>$tv_idx], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        echo json_encode(['success'=>false, 'message'=>$e->getMessage()], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

$todayStart = date('Y-m-d 00:00:00');
$todayEnd   = date('Y-m-d 00:00:00', strtotime('+1 day'));

if ($act === 'detail') {
    try {
        $tv_idx = (int)($_POST['tv_idx'] ?? 0);
        if (!$tv_idx) {
            echo json_encode(['success' => false, 'message' => 'tv_idx 누락'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // ✅ tv_idx 소유 검증
        $DB->where('idx', $tv_idx);
        $DB->where('sh_idx', $sh_idx);
        $visit = $DB->getOne('table_visit_t', 'idx, tv_table, tv_status, tv_last_active');
        if (!$visit) {
            echo json_encode(['success' => false, 'message' => '유효하지 않은 방문세션'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $tableNo = (string)($visit['tv_table'] ?? '');

        // ✅ 테이블 좌석수 (있으면)
        $tbSeats = 0;
        if ($tableNo !== '') {
            $DB->where('sh_idx', $sh_idx);
            $DB->where('use_yn', 'Y');
            $DB->where('tb_no', $tableNo);
            $tb = $DB->getOne('shop_table_t', 'tb_seats');
            if ($tb && isset($tb['tb_seats'])) $tbSeats = (int)$tb['tb_seats'];
        }

        $todayStart = date('Y-m-d 00:00:00');
        $todayEnd   = date('Y-m-d 00:00:00', strtotime('+1 day'));

        $DB->where('sh_idx', $sh_idx);
        $DB->where('tv_idx', $tv_idx);
        $DB->where('ot_wdate', [$todayStart, $todayEnd], 'BETWEEN');
        $DB->where('ot_status', ['CANCELLED'], 'NOT IN');
        $DB->orderBy('ot_wdate', 'ASC'); // ✅ 시간순(첫 주문 = 대표주문)

        $orders = $DB->get('orders_t', null, '
            idx,
            mt_idx,
            ot_number,
            ot_status,
            ot_table,
            ot_total_price,
            ot_discount_amount,
            ct_snapshot,
            ot_notes,
            ot_pay_type,
            ot_pay_status,
            ot_pay_date,
            ot_wdate
        ');

        // ✅ snapshot 파싱 헬퍼
        $parseSnap = function($json){
            if (!$json) return ['items'=>[], 'summary'=>['sub_total'=>0,'discount'=>0,'total'=>0]];
            $d = json_decode($json, true);
            if (!is_array($d)) return ['items'=>[], 'summary'=>['sub_total'=>0,'discount'=>0,'total'=>0]];

            $items = $d['items'] ?? [];
            $summary = $d['summary'] ?? [];
            if (!is_array($items)) $items = [];
            if (!is_array($summary)) $summary = [];

            return [
                'items' => $items,
                'summary' => [
                    'sub_total' => (float)($summary['sub_total'] ?? 0),
                    'discount'  => (float)($summary['discount'] ?? 0),
                    'total'     => (float)($summary['total'] ?? 0),
                ]
            ];
        };

        // =========================================================
        // ✅ (추가) 주문별 환불 승인 합계 매핑 (payment_refunds_t)
        // =========================================================
        $refundMap = []; // ot_idx => approved_sum
        $orderIds = [];

        foreach ($orders as $o) {
            $orderIds[] = (int)($o['idx'] ?? 0);
        }
        $orderIds = array_values(array_filter($orderIds));

        if (!empty($orderIds)) {
            $DB->where('ot_idx', $orderIds, 'IN');
            $DB->where('status', 'APPROVED');
            $DB->groupBy('ot_idx');
            $refundRows = $DB->get('payment_refunds_t', null, 'ot_idx, SUM(approved_amount) AS sum_amount');

            if (is_array($refundRows)) {
                foreach ($refundRows as $rr) {
                    $oid = (int)($rr['ot_idx'] ?? 0);
                    $sum = (int)($rr['sum_amount'] ?? 0);
                    if ($oid > 0) $refundMap[$oid] = $sum;
                }
            }
        }

        // ✅ 대표/추가 주문 분리
        $mainOrder = null;
        $addOrders = [];

        // ✅ 모달 상단 요약(메뉴 총 개수/금액)
        $menuCountTotal = 0;
        $sumTotal = 0;
        $sumDiscount = 0;

        // ✅ 추가: 환불 합계 / 결제완료(환불 반영) 합계
        $sumRefunded = 0;
        $sumPaidRemaining = 0;

        foreach ($orders as &$o) {
            $snap = $parseSnap($o['ct_snapshot'] ?? '');

            // items 수량 합
            foreach (($snap['items'] ?? []) as $it) {
                $qty = (int)($it['quantity'] ?? 0);
                if ($qty <= 0) $qty = 1;
                $menuCountTotal += $qty;
            }

            // 금액은 DB컬럼 우선, 없으면 snapshot
            $orderTotal = (float)($o['ot_total_price'] ?? 0);
            if ($orderTotal <= 0) $orderTotal = (float)($snap['summary']['total'] ?? 0);

            $orderDiscount = (float)($o['ot_discount_amount'] ?? 0);
            if ($orderDiscount <= 0) $orderDiscount = (float)($snap['summary']['discount'] ?? 0);

            $sumTotal += $orderTotal;
            $sumDiscount += $orderDiscount;

            // ✅ 주문별 승인 환불액
            $oid = (int)($o['idx'] ?? 0);
            $refundedAmount = (int)($refundMap[$oid] ?? 0);
            if ($refundedAmount < 0) $refundedAmount = 0;

            // ✅ 환불합 누적
            $sumRefunded += $refundedAmount;

            // ✅ 결제완료금액(환불 반영): PAID면 (total - refunded), 아니면 0
            $isPaid = (strtoupper((string)($o['ot_pay_status'] ?? '')) === 'PAID');
            $paidRemaining = $isPaid ? max(0, (int)$orderTotal - $refundedAmount) : 0;
            $sumPaidRemaining += $paidRemaining;

            // 프론트 편하게 snapshot을 객체로 붙여서 내려줌
            $o['snapshot_obj'] = $snap;
            $o['order_total'] = $orderTotal;
            $o['order_discount'] = $orderDiscount;

            // ✅ 추가 필드들
            $o['refunded_amount'] = $refundedAmount;   // "결제취소/환불 금액" 표시용
            $o['paid_amount'] = $paidRemaining;        // "결제 완료 금액" 표시용(환불 반영)
        }
        unset($o);

        if (!empty($orders)) {
            $mainOrder = $orders[0];
            if (count($orders) > 1) {
                $addOrders = array_slice($orders, 1);
            }
        }

        // ✅ 상태(UI용): 가장 최신 주문 기준으로 판단(너 기존 로직 유지)
        $statusForUi = 'RECEIVED';
        if (!empty($orders)) {
            $last = $orders[count($orders)-1];
            $lastStatus = $last['ot_status'] ?? 'PENDING';
            if ($lastStatus === 'PENDING' || $lastStatus === 'CONFIRMED') $statusForUi = 'RECEIVED';
            else if ($lastStatus === 'PREPARING') $statusForUi = 'PREPARING';
            else if ($lastStatus === 'COMPLETED') $statusForUi = 'SERVED';
        }

        echo json_encode([
            'success' => true,
            'data' => [
                'sh_idx' => $sh_idx,
                'tv_idx' => $tv_idx,
                'table_no' => $tableNo,
                'tb_seats' => $tbSeats,
                'status' => $statusForUi, // UI용
                'main_order' => $mainOrder,
                'add_orders' => $addOrders,
                'summary' => [
                    'menu_count_total' => $menuCountTotal,
                    'total_price' => (int)$sumTotal,
                    'discount' => (int)$sumDiscount,

                    // ✅ 기존 paid_price 대신(혹은 함께) 환불 반영된 결제완료합계를 내려줌
                    'paid_remaining_total' => (int)$sumPaidRemaining,

                    // ✅ 전체 환불 승인 합계
                    'refunded_total' => (int)$sumRefunded,
                ],
            ]
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        error_log('table_api detail error: '.$e->getMessage());
        http_response_code(500);
        echo json_encode(['success'=>false,'message'=>'서버 오류'], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

if ($act === 'delete_table') {
    try {
        $tableNo = trim($_POST['table_no'] ?? '');
        if ($tableNo === '') throw new Exception('table_no가 없습니다.');

        // 0) shop_table_t에서 해당 테이블 존재 확인(매장 소유 검증)
        $DB->where('sh_idx', $sh_idx);
        $DB->where('use_yn', 'Y');
        // tb_no가 숫자/문자 혼용될 수 있어 string으로 비교
        $DB->where('tb_no', $tableNo);
        $tb = $DB->getOne('shop_table_t', 'idx, tb_no, tb_name');

        // tb_no가 비어있어서 idx로 매핑하는 구조를 너가 list에서 쓰고 있어서
        // 만약 tb_no가 비어있는 테이블을 삭제할 가능성이 있다면 idx 매칭도 추가로 허용
        if (!$tb) {
            $maybeIdx = (int)$tableNo;
            if ($maybeIdx > 0) {
                $DB->where('sh_idx', $sh_idx);
                $DB->where('use_yn', 'Y');
                $DB->where('idx', $maybeIdx);
                $tb = $DB->getOne('shop_table_t', 'idx, tb_no, tb_name');
            }
        }

        if (!$tb) throw new Exception('삭제할 테이블을 찾을 수 없습니다.');

        $targetTableNo = (string)($tb['tb_no'] ?? '');
        if ($targetTableNo === '') {
            // tb_no가 비어있으면 list에서 idx로 노출했을 수 있음
            // 이 경우 tv_table과 정확히 매칭이 어려워서 "안전하게" tv_table 체크를 idx 기준으로도 한번 더 확인
            $targetTableNo = (string)$tb['idx'];
        }

        // 1) 해당 테이블에 ACTIVE 방문세션이 있는지 확인
        $DB->where('sh_idx', $sh_idx);
        $DB->where('tv_table', $targetTableNo);
        $DB->where('tv_status', 'ACTIVE');
        $visit = $DB->getOne('table_visit_t', 'idx');

        if ($visit) {
            $tv_idx = (int)$visit['idx'];

            // 2) ACTIVE 세션에 주문이 있으면 삭제 불가
            $DB->where('sh_idx', $sh_idx);
            $DB->where('tv_idx', $tv_idx);
            $orderCnt = (int)$DB->getValue('orders_t', 'COUNT(*)');

            if ($orderCnt > 0) {
                throw new Exception('주문이 있는 테이블은 삭제할 수 없습니다.');
            }

            // 3) 주문은 없지만 ACTIVE 세션이 남아있다면 -> 세션 닫고 진행(권장 정책)
            $DB->where('idx', $tv_idx);
            $DB->where('sh_idx', $sh_idx);
            $DB->update('table_visit_t', [
                'tv_status' => 'CLOSED',
                'tv_ended' => $DB->now(),
                'tv_last_active' => $DB->now(),
            ]);
        }

        // 4) 실제 삭제는 soft delete 권장: use_yn = 'N'
        $DB->where('idx', (int)$tb['idx']);
        $DB->where('sh_idx', $sh_idx);
        $ok = $DB->update('shop_table_t', [
            'use_yn' => 'N'
        ]);

        if (!$ok) throw new Exception('테이블 삭제에 실패했습니다.');

        echo json_encode([
            'success' => true,
            'message' => '테이블이 삭제되었습니다.',
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage(),
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

/**
 * =========================================================
 * 1) 임시 QR 생성 (DB/최종저장 X)
 * act: generate_qr_temp
 * =========================================================
 */
if ($act === 'generate_qr_temp') {
    try {
        $tb_name  = trim($_POST['tb_name'] ?? '');
        $tb_seats = (int)($_POST['tb_seats'] ?? 0);

        if (!preg_match('/^[A-Za-z0-9]{1,5}$/', $tb_name)) {
            echo json_encode(['success'=>false,'message'=>'테이블명은 5자 미만의 영문/숫자만 가능합니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }
        if ($tb_seats <= 0) {
            echo json_encode(['success'=>false,'message'=>'좌석 수를 올바르게 입력해 주세요.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // ✅ 임시 토큰 발급 (최종 토큰으로 그대로 사용)
        $token = make_token(32); // 64 chars

        // ✅ QR 내용: tk 기반
        $qrText = build_qr_text($QR_LANDING_BASE, $QR_LANDING_QS, $token);

        $imgData = fetch_qr_image($qrText, '260x260');
        if ($imgData === false || $imgData === null) {
            echo json_encode(['success'=>false,'message'=>'QR 서버에서 이미지를 가져오지 못했습니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $tmpDirFs = $_SERVER['DOCUMENT_ROOT'] . '/data/qr_tmp/'.$sh_idx.'/';
        if (!is_dir($tmpDirFs)) {
            @mkdir($tmpDirFs, 0775, true);
        }

        $tmpFileName = "tmp_{$token}.png";
        $tmpFileFs   = $tmpDirFs . $tmpFileName;
        $tmpFileUrl  = '/data/qr_tmp/'.$sh_idx.'/'.$tmpFileName;

        $saved = @file_put_contents($tmpFileFs, $imgData);
        if ($saved === false) {
            echo json_encode(['success'=>false,'message'=>'임시 QR 이미지를 저장하지 못했습니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        if (!isset($_SESSION['tbl_add_temp'])) $_SESSION['tbl_add_temp'] = [];
        $_SESSION['tbl_add_temp'][$token] = [
            'sh_idx'    => $sh_idx,
            'tb_name'   => $tb_name,
            'tb_seats'  => $tb_seats,
            // ✅ qr_text는 선택: 유지해도 되고 없어도 됨
            'qr_text'   => $qrText,
            'tmp_file'  => $tmpFileUrl,
            'created'   => time(),
        ];

        echo json_encode([
            'success' => true,
            'message' => '임시 QR 코드가 생성되었습니다.',
            'data' => [
                'token'  => $token,
                'qr_url' => $tmpFileUrl,
            ],
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        echo json_encode(['success'=>false,'message'=>$e->getMessage()], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

/**
 * =========================================================
 * 2) 최종 등록: 임시 QR -> 테이블/QR DB 저장
 * act: add_table_with_qr
 * =========================================================
 */
if ($act === 'add_table_with_qr') {
    try {
        $token = trim($_POST['qr_token'] ?? '');
        if ($token === '') {
            echo json_encode(['success'=>false,'message'=>'qr_token이 없습니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $temp = $_SESSION['tbl_add_temp'][$token] ?? null;
        if (!$temp) {
            echo json_encode(['success'=>false,'message'=>'임시 QR 정보가 없습니다. 다시 코드 생성하기를 진행해 주세요.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        if ((int)$temp['sh_idx'] !== $sh_idx) {
            echo json_encode(['success'=>false,'message'=>'매장 정보가 일치하지 않습니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $tb_name  = $temp['tb_name'];
        $tb_seats = (int)$temp['tb_seats'];
        $tmpFile  = $temp['tmp_file'];

        // qr_text는 파생값이라 없어도 됨
        $qrText   = $temp['qr_text'] ?? build_qr_text($QR_LANDING_BASE, $QR_LANDING_QS, $token);

        $tmpFs = $_SERVER['DOCUMENT_ROOT'] . $tmpFile;
        if (!file_exists($tmpFs)) {
            echo json_encode(['success'=>false,'message'=>'임시 QR 이미지가 없습니다. 다시 코드 생성하기를 진행해 주세요.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // tb_no 자동 증가
        $DB->where('sh_idx', $sh_idx);
        $maxNo  = (int)$DB->getValue('shop_table_t', 'MAX(tb_no)') ?: 0;
        $nextNo = $maxNo + 1;

        $DB->startTransaction();

        // 1) 테이블 insert
        $tableId = $DB->insert('shop_table_t', [
            'sh_idx'   => $sh_idx,
            'tb_name'  => $tb_name,
            'tb_no'    => $nextNo,
            'tb_seats' => $tb_seats,
            'use_yn'   => 'Y',
        ]);
        if (!$tableId) {
            $DB->rollback();
            echo json_encode(['success'=>false,'message'=>'테이블 등록에 실패했습니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // 2) 최종 QR 경로
        $finalDirFs = $_SERVER['DOCUMENT_ROOT'] . '/data/qr/'.$sh_idx.'/';
        if (!is_dir($finalDirFs)) {
            @mkdir($finalDirFs, 0775, true);
        }

        // 파일명은 기존 유지
        $finalFileName = "table_{$tableId}.png";
        $finalFs  = $finalDirFs . $finalFileName;
        $finalUrl = '/data/qr/'.$sh_idx.'/'.$finalFileName;

        // 3) 파일 이동
        $moved = @rename($tmpFs, $finalFs);
        if (!$moved) {
            $copied = @copy($tmpFs, $finalFs);
            if ($copied) @unlink($tmpFs);
            $moved = $copied;
        }
        if (!$moved) {
            $DB->rollback();
            echo json_encode(['success'=>false,'message'=>'QR 이미지 최종 저장(이동)에 실패했습니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // 4) (중요) 토큰 중복 방지: UNIQUE 걸려있으면 여기서 실패 가능
        // 충돌 확률은 사실상 0이지만, 안전하게 한번 체크해도 됨(선택)
        $DB->where('qr_token', $token);
        $exists = $DB->getValue('shop_table_qr_t', 'COUNT(*)');
        if ((int)$exists > 0) {
            if (file_exists($finalFs)) @unlink($finalFs);
            $DB->rollback();
            echo json_encode(['success'=>false,'message'=>'QR 토큰이 중복되었습니다. 다시 시도해 주세요.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // 5) QR 테이블 insert (✅ qr_token 저장)
        $qrId = $DB->insert('shop_table_qr_t', [
            'sh_idx'    => $sh_idx,
            'tb_idx'    => (int)$tableId,
            'qr_token'  => $token,
            // qr_text는 선택: 유지하거나 제거 가능
            'qr_text'   => $qrText,
            'qr_file'   => $finalUrl,
            'qr_udate'  => $DB->now(),
        ]);
        if (!$qrId) {
            if (file_exists($finalFs)) @unlink($finalFs);
            $DB->rollback();
            echo json_encode(['success'=>false,'message'=>'QR 정보(DB) 저장에 실패했습니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $DB->commit();
        unset($_SESSION['tbl_add_temp'][$token]);

        echo json_encode([
            'success' => true,
            'message' => '테이블이 추가되었습니다.',
            'data' => [
                'table_id' => (int)$tableId,
                'qr_url'   => $finalUrl,
                'qr_token' => $token,
            ]
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        try { $DB->rollback(); } catch (Exception $ignore) {}
        echo json_encode(['success'=>false,'message'=>$e->getMessage()], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

/**
 * =========================================================
 * 3) 임시 QR 취소
 * act: cancel_qr_temp
 * =========================================================
 */
if ($act === 'cancel_qr_temp') {
    try {
        $token = trim($_POST['token'] ?? '');
        if ($token === '') {
            echo json_encode(['success'=>true,'message'=>'취소할 token 없음'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $temp = $_SESSION['tbl_add_temp'][$token] ?? null;
        if (!$temp) {
            echo json_encode(['success'=>true,'message'=>'이미 만료되었거나 없음'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        if (!empty($temp['tmp_file'])) {
            $fs = $_SERVER['DOCUMENT_ROOT'] . $temp['tmp_file'];
            if (file_exists($fs)) @unlink($fs);
        }

        unset($_SESSION['tbl_add_temp'][$token]);

        echo json_encode(['success'=>true,'message'=>'임시 QR 취소 완료'], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        echo json_encode(['success'=>false,'message'=>$e->getMessage()], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

/**
 * =========================================================
 * 4) 테이블 정보 + QR 조회
 * act: table_info
 * =========================================================
 */
if ($act === 'table_info') {
    try {
        $tableNo = trim($_POST['table_no'] ?? '');
        if ($tableNo === '') {
            echo json_encode(['success'=>false,'message'=>'table_no가 없습니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // 1) 테이블 찾기(매장 검증)
        $DB->where('sh_idx', $sh_idx);
        $DB->where('use_yn', 'Y');
        $DB->where('tb_no', $tableNo);
        $tb = $DB->getOne('shop_table_t', 'idx, tb_no, tb_name, tb_seats');

        if (!$tb) {
            $maybeIdx = (int)$tableNo;
            if ($maybeIdx > 0) {
                $DB->where('sh_idx', $sh_idx);
                $DB->where('use_yn', 'Y');
                $DB->where('idx', $maybeIdx);
                $tb = $DB->getOne('shop_table_t', 'idx, tb_no, tb_name, tb_seats');
            }
        }

        if (!$tb) {
            echo json_encode(['success'=>false,'message'=>'테이블 정보를 찾을 수 없습니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // 2) QR 조회 (✅ qr_token 포함)
        $DB->where('sh_idx', $sh_idx);
        $DB->where('tb_idx', (int)$tb['idx']);
        $qr = $DB->getOne('shop_table_qr_t', 'qr_file, qr_token');

        echo json_encode([
            'success' => true,
            'data' => [
                'table_id' => (int)$tb['idx'],
                'tb_no'    => $tb['tb_no'],
                'tb_name'  => $tb['tb_name'],
                'tb_seats' => (int)$tb['tb_seats'],
                'qr_url'   => $qr['qr_file'] ?? '',
                'qr_token' => $qr['qr_token'] ?? '',
            ],
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        echo json_encode(['success'=>false,'message'=>$e->getMessage()], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

/**
 * =========================================================
 * 5) QR 새로 생성
 * - 기존 파일 삭제
 * - 새 토큰 발급
 * - 새 파일 저장
 * - DB 업데이트: qr_token, qr_file, qr_udate (+선택: qr_text)
 * act: regenerate_qr
 * =========================================================
 */
if ($act === 'regenerate_qr') {
    try {
        $tableNo = trim($_POST['table_no'] ?? '');
        if ($tableNo === '') {
            echo json_encode(['success'=>false, 'message'=>'table_no가 없습니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // 1) shop_table_t: 테이블 소유 검증 + tb_idx 찾기
        $DB->where('sh_idx', $sh_idx);
        $DB->where('use_yn', 'Y');
        $DB->where('tb_no', $tableNo);
        $tb = $DB->getOne('shop_table_t', 'idx, tb_no, tb_name');

        if (!$tb) {
            $maybeIdx = (int)$tableNo;
            if ($maybeIdx > 0) {
                $DB->where('sh_idx', $sh_idx);
                $DB->where('use_yn', 'Y');
                $DB->where('idx', $maybeIdx);
                $tb = $DB->getOne('shop_table_t', 'idx, tb_no, tb_name');
            }
        }

        if (!$tb) {
            echo json_encode(['success'=>false, 'message'=>'테이블 정보를 찾을 수 없습니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $tb_idx = (int)$tb['idx'];

        // 2) 기존 QR 정보 조회
        $DB->where('sh_idx', $sh_idx);
        $DB->where('tb_idx', $tb_idx);
        $qrRow = $DB->getOne('shop_table_qr_t', 'idx, qr_file');

        if (!$qrRow || empty($qrRow['idx'])) {
            echo json_encode(['success'=>false, 'message'=>'기존 QR 정보가 없습니다. (shop_table_qr_t)'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $qr_idx  = (int)$qrRow['idx'];
        $oldFile = trim($qrRow['qr_file'] ?? '');

        // 3) 기존 파일 삭제
        if ($oldFile !== '') {
            $oldFs = $_SERVER['DOCUMENT_ROOT'] . $oldFile;
            if (file_exists($oldFs)) @unlink($oldFs);
        }

        // 4) 새 토큰 발급 + QR 텍스트 생성
        $newToken = make_token(32);
        $newQrText = build_qr_text($QR_LANDING_BASE, $QR_LANDING_QS, $newToken);

        // 5) 외부 QR 생성 API 호출
        $imgData = fetch_qr_image($newQrText, '260x260');
        if ($imgData === false || $imgData === null) {
            echo json_encode(['success'=>false, 'message'=>'QR 서버에서 이미지를 가져오지 못했습니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // 6) 저장 경로(기존과 동일)
        $finalDirFs = $_SERVER['DOCUMENT_ROOT'] . '/data/qr/'.$sh_idx.'/';
        if (!is_dir($finalDirFs)) {
            @mkdir($finalDirFs, 0775, true);
        }

        $finalFileName = "table_{$tb_idx}.png";
        $finalFs  = $finalDirFs . $finalFileName;
        $finalUrl = '/data/qr/'.$sh_idx.'/'.$finalFileName;

        $saved = @file_put_contents($finalFs, $imgData);
        if ($saved === false) {
            echo json_encode(['success'=>false, 'message'=>'QR 이미지를 서버에 저장하지 못했습니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // 7) DB 업데이트 (✅ qr_token 포함)
        $DB->where('idx', $qr_idx);
        $DB->where('sh_idx', $sh_idx);
        $DB->where('tb_idx', $tb_idx);

        $ok = $DB->update('shop_table_qr_t', [
            'qr_token' => $newToken,
            // qr_text는 선택: 유지하고 싶으면 저장, 아니면 빼도 됨
            'qr_text'  => $newQrText,
            'qr_file'  => $finalUrl,
            'qr_udate' => $DB->now(),
        ]);

        if (!$ok) {
            if (file_exists($finalFs)) @unlink($finalFs);
            echo json_encode(['success'=>false, 'message'=>'QR 정보 업데이트에 실패했습니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        echo json_encode([
            'success' => true,
            'message' => 'QR 코드가 새로 생성되었습니다.',
            'data' => [
                'table_no' => (string)$tableNo,
                'tb_idx'   => $tb_idx,
                'qr_url'   => $finalUrl,
                'qr_token' => $newToken,
            ],
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        echo json_encode(['success'=>false,'message'=>$e->getMessage()], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

/* =========================================================
 * 자리 이동: 이동 가능한 테이블 목록
 * act: move_table_list
 * ========================================================= */
if ($act === 'move_table_list') {
    try {
        $tv_idx = (int)($_POST['tv_idx'] ?? 0);
        if ($tv_idx <= 0) throw new Exception('tv_idx 누락');

        // 1) 현재 방문세션 매장 소유/ACTIVE 검증
        $DB->where('idx', $tv_idx);
        $DB->where('sh_idx', $sh_idx);
        $DB->where('tv_status', 'ACTIVE');
        $cur = $DB->getOne('table_visit_t', 'idx, tv_table');
        if (!$cur) throw new Exception('유효하지 않은 방문 세션');

        $currentTableNo = (string)($cur['tv_table'] ?? '');
        if ($currentTableNo === '') throw new Exception('현재 테이블 정보가 없습니다.');

        // 2) 테이블 마스터(=shop_table_t) 기준으로 목록 구성 (use_yn='Y')
        $DB->where('sh_idx', $sh_idx);
        $DB->where('use_yn', 'Y');
        $DB->orderBy('tb_no', 'ASC');
        $tablesMaster = $DB->get('shop_table_t', null, 'idx, tb_no, tb_name, tb_seats');

        $all = [];
        foreach ($tablesMaster as $t) {
            $no = (string)($t['tb_no'] ?? '');
            if ($no === '') continue;
            $all[$no] = [
                'table_no'    => (int)$no,
                'table_name'  => (string)($t['tb_name'] ?? $no),
                'table_seats' => (int)($t['tb_seats'] ?? 0),
            ];
        }

        // 3) 점유(=다른 ACTIVE 방문세션이 있는 테이블) 확인
        $DB->where('sh_idx', $sh_idx);
        $DB->where('tv_status', 'ACTIVE');
        $vis = $DB->get('table_visit_t', null, 'idx, tv_table');

        $occupied = []; // tableNo => true
        foreach ($vis as $v) {
            $tno = (string)($v['tv_table'] ?? '');
            if ($tno === '') continue;
            $occupied[$tno] = true;
        }

        // 4) 응답 리스트
        $out = [];
        foreach ($all as $no => $tm) {
            $out[] = [
                'table_no'    => $tm['table_no'],
                'table_name'  => $tm['table_name'],
                'table_seats' => $tm['table_seats'],
                // "사용중" 표시는 하되, 현재 테이블은 선택 가능해야 함
                'is_occupied' => isset($occupied[$no]) ? true : false,
                'is_current'  => ($no === $currentTableNo),
            ];
        }

        echo json_encode(['success' => true, 'data' => ['tables' => $out]], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

/* =========================================================
 * 자리 이동: 실제 이동 처리
 * act: move_table
 * ========================================================= */
if ($act === 'move_table') {
    try {
        $tv_idx = (int)($_POST['tv_idx'] ?? 0);
        $target = trim((string)($_POST['target_table_no'] ?? ''));

        if ($tv_idx <= 0) throw new Exception('tv_idx 누락');
        if ($target === '') throw new Exception('target_table_no 누락');

        $DB->startTransaction();

        // 1) 현재 방문세션 잠금/검증(매장 + ACTIVE)
        $DB->where('idx', $tv_idx);
        $DB->where('sh_idx', $sh_idx);
        $DB->where('tv_status', 'ACTIVE');
        $cur = $DB->getOne('table_visit_t', 'idx, tv_table, tv_status');
        if (!$cur) throw new Exception('유효하지 않은 방문 세션');

        $curTable = trim((string)($cur['tv_table'] ?? ''));
        if ($curTable === '') throw new Exception('현재 테이블 정보가 없습니다.');

        if ($curTable === $target) {
            $DB->commit();
            echo json_encode(['success' => true, 'message' => '이미 해당 테이블입니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // 2) 타겟 테이블이 shop_table_t에 존재하는지 검증
        $DB->where('sh_idx', $sh_idx);
        $DB->where('use_yn', 'Y');
        $DB->where('tb_no', $target);
        $tb = $DB->getOne('shop_table_t', 'idx, tb_no');
        if (!$tb) throw new Exception('존재하지 않는 테이블입니다.');

        // 3) 타겟 테이블에 다른 ACTIVE 방문세션이 있으면 이동 불가
        $DB->where('sh_idx', $sh_idx);
        $DB->where('tv_status', 'ACTIVE');
        $DB->where('tv_table', $target);
        $DB->where('idx', $tv_idx, '!=');
        $dup = $DB->getOne('table_visit_t', 'idx');
        if ($dup) throw new Exception('해당 테이블은 현재 사용중입니다.');

        // 4) (선택) 이동 가능 조건 강화 - 진행중 주문이 하나도 없으면 막기(원하면 주석 해제)
        /*
        $DB->where('sh_idx', $sh_idx);
        $DB->where('tv_idx', $tv_idx);
        $DB->where('ot_status', ['CANCELLED'], 'NOT IN');
        $cntOrder = (int)$DB->getValue('orders_t', 'COUNT(*)');
        if ($cntOrder <= 0) throw new Exception('주문 내역이 없어 자리 이동이 불가합니다.');
        */

        // 5) ✅ 가장 중요: UPDATE 전에 WHERE를 "반드시" 다시 설정
        $DB->where('idx', $tv_idx);
        $DB->where('sh_idx', $sh_idx);
        $DB->where('tv_status', 'ACTIVE');
        $ok1 = $DB->update('table_visit_t', [
            'tv_table'       => $target,
            'tv_last_active' => $DB->now(),
        ]);

        if ($ok1 === false) throw new Exception('방문세션 업데이트 실패');

        // ✅ 안전장치: 영향 row가 1개가 아니면 롤백
        if (method_exists($DB, 'getRowCount')) {
            $rc = (int)$DB->getRowCount();
            if ($rc !== 1) {
                throw new Exception('비정상 업데이트 감지(방문세션 rowcount=' . $rc . ')');
            }
        }

        // 6) 주문 테이블 표기도 함께 변경(동일 tv_idx 주문들)
        $DB->where('sh_idx', $sh_idx);
        $DB->where('tv_idx', $tv_idx);
        $ok2 = $DB->update('orders_t', [
            'ot_table' => $target,
            'ot_udate' => $DB->now(),
        ]);
        if ($ok2 === false) throw new Exception('주문 테이블 업데이트 실패');

        $DB->commit();

        echo json_encode([
            'success' => true,
            'message' => '자리 이동 완료',
            'data' => [
                'from' => $curTable,
                'to'   => $target,
                'tv_idx' => $tv_idx,
            ],
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        try { $DB->rollback(); } catch (Exception $ignore) {}
        echo json_encode(['success' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// ✅ 환불 처리 (부분/전체) + payment_refunds_t 저장
if ($act === 'refund') {
    try {
        $orderIdx = (int)($_POST['order_idx'] ?? ($_POST['ot_idx'] ?? 0));
        $amount   = (int)preg_replace('/[^0-9]/', '', (string)($_POST['refund_amount'] ?? 0));

        if (!$orderIdx) {
            echo json_encode(['success'=>false,'message'=>'order_idx 누락'], JSON_UNESCAPED_UNICODE);
            exit;
        }
        if ($amount <= 0) {
            echo json_encode(['success'=>false,'message'=>'환불 금액이 올바르지 않습니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // ✅ 주문 소유(매장) 검증 + 결제 상태 확인
        $DB->where('idx', $orderIdx);
        $DB->where('sh_idx', $sh_idx);
        $o = $DB->getOne('orders_t', 'idx, sh_idx, ot_total_price, ot_pay_status, ot_pay_type, ot_number, ot_wdate');
        if (!$o) {
            echo json_encode(['success'=>false,'message'=>'유효하지 않은 주문입니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $payIdx = $_POST['order_idx'];

        $payStatus = strtoupper((string)($o['ot_pay_status'] ?? ''));
        if ($payStatus !== 'PAID') {
            echo json_encode(['success'=>false,'message'=>'결제 완료(PAID) 주문만 환불할 수 있습니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $DB->where('idx',$orderIdx);
        $DB->update('orders_t',[
           'ot_status' =>  'CANCELLED',
           'ot_cancel' => $DB->now(),
           'ot_cancel_reason' => '재고가 부족합니다.',
        ]);

        $orderTotal = (int)($o['ot_total_price'] ?? 0);

        // ✅ 이미 승인된 환불 합계
        $DB->where('ot_idx', $orderIdx);
        $DB->where('status', 'APPROVED');
        $approvedSumRow = $DB->getOne('payment_refunds_t', 'IFNULL(SUM(approved_amount),0) AS s');
        $approvedSum = (int)($approvedSumRow['s'] ?? 0);

        $remain = max(0, $orderTotal - $approvedSum);
        if ($amount > $remain) {
            echo json_encode(['success'=>false,'message'=>"환불 가능 금액을 초과했습니다. (가능: {$remain}원)"], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // ✅ payments_t 연결 필요하면 여기서 pay_idx 조회해서 넣어야 함 (없으면 0으로 테스트)
        // 프로젝트마다 결제 구조가 달라서 우선 0 처리 (반드시 실제 pay_idx 연결 필요)

        $refundType = ($amount === $remain) ? 'FULL' : 'PARTIAL';

        // ✅ 환불 요청 row 생성 (REQUESTED)
        $reqIp = $_SERVER['REMOTE_ADDR'] ?? '';
        $now = date('Y-m-d H:i:s');

        $insert = [
            'pay_idx'         => $payIdx,
            'ot_idx'          => $orderIdx,
            'sh_idx'          => $sh_idx,
            'refund_type'     => $refundType,
            'request_amount'  => $amount,
            'approved_amount' => $amount,
            'reason'          => null,
            'requested_by'    => $_SESSION['_mt_idx'] ?? null,
            'requested_ip'    => $reqIp,
            'imp_uid'         => null,
            'cancel_receipt_id'=> null,
            'result_code'     => null,
            'result_msg'      => null,
            'status'          => 'REQUESTED',
            'requested_at'    => $now,
            'processed_at'    => null,
            'pg_payload'      => null,
        ];

        $rid = $DB->insert('payment_refunds_t', $insert);
        if (!$rid) {
            echo json_encode(['success'=>false,'message'=>'환불 이력 저장 실패'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $DB->where('ot_idx', $payIdx);
        $payRow = $DB->getOne('payments_t', ['imp_uid', 'merchant_uid']);

        if (!$payRow || empty($payRow['imp_uid'])) {
            throw new Exception('결제 paymentId(imp_uid)가 없습니다.');
        }

        $paymentId = (string)$payRow['imp_uid'];
        $res = cancelPortonePayment($paymentId, '고객 요청', $amount);
        // =========================================================
        // ✅ 여기서 PortOne "부분취소" 실제 호출을 해야 함
        // - 성공 시: APPROVED 로 업데이트 + approved_amount 채우기
        // - 실패 시: FAILED 로 업데이트 + result_msg 채우기
        // =========================================================

        // ✅ 일단 성공 처리(테스트용)
        $DB->where('idx', $rid);
        $DB->update('payment_refunds_t', [
            'status'         => 'APPROVED',
            'approved_amount'=> $amount,
            'processed_at'   => date('Y-m-d H:i:s'),
            'result_code'    => 'OK',
            'result_msg'     => 'TEST APPROVED',
        ]);

        echo json_encode(['success'=>true, 'message'=>'환불 처리 완료(테스트)'], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        error_log('table_api refund error: '.$e->getMessage());
        http_response_code(500);
        echo json_encode(['success'=>false,'message'=>'서버 오류','test'=>$paymentId], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// 전체 메뉴 리스트
if ($act === 'shop_menus') {
    try {
        // ✅ 카테고리까지 같이 내려주고 싶으면 join해서 내려도 되지만
        // 1단계는 메뉴만 전부 노출이 목적이므로 메뉴만 내려줌.

        $DB->where('c.sh_idx', $sh_idx);
        $DB->where('m.sm_show', 'Y');   // 노출여부
        $DB->where('m.sm_type', 'Y');   // 판매중(Y) / 판매중지(N)  ← 너 테이블 정의 기준
        $DB->orderBy('c.sc_order', 'ASC');
        $DB->orderBy('m.sm_order', 'ASC');

        $rows = $DB->get('shop_menu_t m', null, '
            m.idx as sm_id,
            m.sc_idx,
            m.sm_title,
            m.sm_price,
            m.sm_image,
            m.sm_contents,
            m.sm_su,
            m.sm_type,
            m.sm_show
        ', [
            'shop_category_t c' => 'm.sc_idx = c.idx'
        ]);

        if (!is_array($rows)) $rows = [];

        echo json_encode([
            'success' => true,
            'data' => [
                'menus' => $rows,
            ],
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        error_log('table_api shop_menus error: '.$e->getMessage());
        http_response_code(500);
        echo json_encode(['success'=>false,'message'=>'서버 오류'], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// ================================
// act = catalog
// - 카테고리/메뉴/옵션그룹/옵션항목 전체 내려줌
// ================================
if ($act === 'catalog') {
    try {
        // ✅ 카테고리
        $DB->where('sh_idx', $sh_idx);
        $DB->where('sc_show', 'Y');
        $DB->orderBy('sc_order', 'ASC');
        $DB->orderBy('idx', 'ASC');
        $categories = $DB->get('shop_category_t', null, '
            idx AS sc_idx,
            sc_title,
            sc_order
        ');

        // ✅ 메뉴
        $DB->join('shop_category_t c', 'm.sc_idx = c.idx', 'INNER');
        $DB->where('c.sh_idx', $sh_idx);
        $DB->where('m.sm_show', 'Y');
        $DB->where('m.sm_type', 'Y'); // 판매중
        $DB->orderBy('c.sc_order', 'ASC');
        $DB->orderBy('m.sm_order', 'ASC');
        $DB->orderBy('m.idx', 'ASC');
        $menus = $DB->get('shop_menu_t m', null, '
            m.idx AS sm_id,
            m.sc_idx,
            m.sm_title,
            m.sm_price,
            m.sm_image,
            m.sm_contents
        ');

        // ✅ 옵션 그룹(메뉴 옵션 카테고리)
        $smIds = array_column($menus, 'sm_id');
        $optionGroups = [];
        if (!empty($smIds)) {
            $DB->where('sm_idx', $smIds, 'IN');
            $DB->where('oc_show', 'Y');
            $DB->orderBy('oc_order', 'ASC');
            $DB->orderBy('idx', 'ASC');
            $optionGroups = $DB->get('menu_option_category_t', null, '
                idx AS oc_idx,
                sm_idx AS sm_id,
                oc_title,
                oc_check,
                oc_order
            ');
        }

        // ✅ 옵션 항목(option_menu_t) + (중요) sm_id 포함되도록 JOIN
        $ocIds = array_column($optionGroups, 'oc_idx');
        $options = [];
        if (!empty($ocIds)) {
            $DB->join('menu_option_category_t g', 'o.oc_idx = g.idx', 'INNER');
            $DB->where('o.oc_idx', $ocIds, 'IN');
            $DB->where('o.om_show', 'Y');
            $DB->where('g.oc_show', 'Y');
            $DB->orderBy('g.oc_order', 'ASC');
            $DB->orderBy('o.om_order', 'ASC');
            $DB->orderBy('o.idx', 'ASC');
            $options = $DB->get('option_menu_t o', null, '
                o.idx AS om_idx,
                o.oc_idx,
                g.sm_idx AS sm_id,
                o.om_title,
                o.om_price,
                o.om_order
            ');
        }

        echo json_encode([
            'success' => true,
            'data' => [
                'categories'    => $categories,
                'menus'         => $menus,
                'option_groups' => $optionGroups,
                'options'       => $options
            ]
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        error_log('catalog error: '.$e->getMessage());
        http_response_code(500);
        echo json_encode(['success'=>false,'message'=>'서버 오류'], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// ================================
// act = order_update
// - 주문 변경 저장(스냅샷 갱신 + 총금액 갱신)
// - 제한: PREPARING 이상이면 변경 불가(필요시 조정)
// ================================
if ($act === 'order_update') {
    try {
        $tv_idx   = (int)($_POST['tv_idx'] ?? 0);
        $orderIdx = (int)($_POST['order_idx'] ?? 0);
        $snapJson = (string)($_POST['snapshot_json'] ?? '');

        if (!$tv_idx || !$orderIdx || !$snapJson) {
            echo json_encode(['success'=>false,'message'=>'필수값 누락(tv_idx/order_idx/snapshot_json)'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $snap = json_decode($snapJson, true);
        if (!is_array($snap)) {
            echo json_encode(['success'=>false,'message'=>'snapshot_json 형식 오류'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // ✅ tv_idx 소유 검증
        $DB->where('idx', $tv_idx);
        $DB->where('sh_idx', $sh_idx);
        $visit = $DB->getOne('table_visit_t', 'idx');
        if (!$visit) {
            echo json_encode(['success'=>false,'message'=>'유효하지 않은 방문세션'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // ✅ 주문 소유 검증 + 상태 검증
        $DB->where('idx', $orderIdx);
        $DB->where('sh_idx', $sh_idx);
        $DB->where('tv_idx', $tv_idx);
        $order = $DB->getOne('orders_t', 'idx, ot_status, ot_discount_amount');
        if (!$order) {
            echo json_encode(['success'=>false,'message'=>'유효하지 않은 주문'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $st = strtoupper((string)$order['ot_status']);
        if (in_array($st, ['PREPARING','COMPLETED'], true)) {
            echo json_encode(['success'=>false,'message'=>'조리중/완료 주문은 변경할 수 없습니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // ✅ 총금액 재계산(클라 스냅샷 기반)
        $items = $snap['items'] ?? [];
        if (!is_array($items) || empty($items)) {
            echo json_encode(['success'=>false,'message'=>'변경할 메뉴가 없습니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $subTotal = 0;
        foreach ($items as $it) {
            $qty = (int)($it['quantity'] ?? 1);
            if ($qty <= 0) $qty = 1;

            $unit = (float)($it['unit_price'] ?? 0);
            $line = $unit * $qty;

            $opts = $it['options'] ?? [];
            if (is_array($opts)) {
                foreach ($opts as $op) {
                    $p = (float)($op['option_price'] ?? 0);
                    $oq = (int)($op['quantity'] ?? 1);
                    if ($oq <= 0) $oq = 1;
                    $line += ($p * $oq);
                }
            }
            $subTotal += $line;
        }

        $discount = (float)($order['ot_discount_amount'] ?? 0); // 기존 쿠폰 유지
        $total = max(0, $subTotal - $discount);

        // ✅ 스냅샷 summary 갱신해서 저장(프론트/백 모두 동일하게)
        $snap['summary'] = [
            'sub_total' => $subTotal,
            'discount'  => $discount,
            'total'     => $total
        ];
        $snapJsonFixed = json_encode($snap, JSON_UNESCAPED_UNICODE);

        $DB->where('idx', $orderIdx);
        $DB->update('orders_t', [
            'ct_snapshot'     => $snapJsonFixed,
            'ot_total_price'  => $total,
            'ot_udate'        => $DB->now(),
        ]);

        echo json_encode([
            'success' => true,
            'message' => '주문 변경 완료',
            'data' => [
                'order_idx' => $orderIdx,
                'total' => (int)$total
            ]
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        error_log('order_update error: '.$e->getMessage());
        http_response_code(500);
        echo json_encode(['success'=>false,'message'=>'서버 오류'], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

echo json_encode(['success'=>false, 'message'=>'invalid act'], JSON_UNESCAPED_UNICODE);
exit;
