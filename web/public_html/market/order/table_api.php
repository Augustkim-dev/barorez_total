<?php
// table_api.php
session_start();
header('Content-Type: application/json; charset=utf-8');

/**
 * ✅ 필요 테이블
 * - table_visit_t (ACTIVE/CLOSED)
 * - orders_t (tv_idx로 연결)
 *
 * ✅ 주의
 * - 아래 include 경로는 프로젝트에 맞게 교체하세요.
 * - $DB는 thingengineer/mysqli-database-class (MysqliDb) 인스턴스라고 가정
 */

// TODO: 프로젝트에 맞게 include 수정
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";

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

// ✅ list: 테이블별 카드 데이터 반환
if ($act === 'list') {
    try {
        global $DB, $ALL_TABLES;

        // 1) ACTIVE 방문 세션 가져오기 (매장 기준)
        $DB->where('sh_idx', $sh_idx);
        $DB->where('tv_status', 'ACTIVE');
        $visits = $DB->get('table_visit_t', null, 'idx, tv_table, tv_started, tv_last_active');

        $visitByTable = [];
        $tvIds = [];
        foreach ($visits as $v) {
            $tno = (string)($v['tv_table'] ?? '');
            if ($tno === '') continue;
            $visitByTable[$tno] = $v;
            $tvIds[] = (int)$v['idx'];
        }

        // 2) ACTIVE 세션들의 주문 가져오기 (오늘 주문만)
        $ordersByTv = [];
        if (!empty($tvIds)) {
            $today = date('Y-m-d');

            $DB->where('sh_idx', $sh_idx);
            $DB->where('tv_idx', $tvIds, 'IN');
//            $DB->where('DATE(ot_wdate)', $today);
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

            // 주문이 아직 없는 ACTIVE 세션 (정책상 빈자리처럼 보여도 되고, 별도 표시 가능)
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

            // 메뉴 요약(상위 1~2개만 표시)
            $menuCount = []; // name => qty

            foreach ($orders as $o) {
                $latestOrderIdx = max($latestOrderIdx, (int)$o['idx']);
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

            // ✅ 추가주문 여부: 전달완료 이력이 있는데, 다시 RECEIVED가 생긴 경우
            $hasNew = ($hasServed && $hasReceived);

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
            $DB->where('idx', $tv_idx);
            $DB->update('table_visit_t', ['tv_last_active' => $DB->now()]);

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

        // ✅ 오늘 + 해당 매장 + 해당 tv_idx 주문들
        $DB->where('sh_idx', $sh_idx);
        $DB->where('tv_idx', $tv_idx);
        $DB->where('ot_wdate', [$todayStart, $todayEnd], 'BETWEEN');

        // ✅ 취소 제외(원하면)
        $DB->where('ot_status', ['CANCELLED'], 'NOT IN');

        // 최신순
        $orders = $DB->get('orders_t', null, '
            idx,
            ot_number,
            ot_status,
            ot_table,
            rv_idx,
            tv_idx,
            ot_total_price,
            ot_discount_amount,
            ct_snapshot,
            ot_notes,
            ot_pay_type,
            ot_pay_status,
            ot_pay_date,
            ot_wdate
        ');

        // 테이블번호(ot_table) 추정: 가장 최신 주문 기준
        $tableNo = '';
        $statusForUi = 'RECEIVED'; // UI용(테스트)
        if (!empty($orders)) {
            // 최신순이 아니라 get이 기본 ASC로 나올 수 있으니, 안전하게 idx 큰거로 잡기
            usort($orders, function($a,$b){ return (int)$b['idx'] - (int)$a['idx']; });

            $tableNo = $orders[0]['ot_table'] ?? '';
            // ✅ 상태 매핑(너희 로직에 맞게 바꾸면 됨)
            // orders_t: PENDING/CONFIRMED/PREPARING/COMPLETED...
            $lastStatus = $orders[0]['ot_status'] ?? 'PENDING';
            if ($lastStatus === 'PENDING' || $lastStatus === 'CONFIRMED') $statusForUi = 'RECEIVED';
            else if ($lastStatus === 'PREPARING') $statusForUi = 'PREPARING';
            else if ($lastStatus === 'COMPLETED') $statusForUi = 'SERVED';
        }

        // summary 계산
        $sumTotal = 0;
        $sumDiscount = 0;
        foreach ($orders as $o) {
            $sumTotal += (float)($o['ot_total_price'] ?? 0);
            $sumDiscount += (float)($o['ot_discount_amount'] ?? 0);
        }

        echo json_encode([
            'success' => true,
            'data' => [
                'sh_idx' => $sh_idx,
                'tv_idx' => $tv_idx,
                'table_no' => $tableNo,
                'status' => $statusForUi, // ✅ UI용
                'orders' => $orders,
                'summary' => [
                    'total_price' => $sumTotal,
                    'discount' => $sumDiscount,
                    'pay_method' => '카드 결제', // ✅ 임시(실제는 마지막 주문의 pay_type/pay_status로 만들어도 됨)
                    'badge' => '주문접수',
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

echo json_encode(['success'=>false, 'message'=>'invalid act'], JSON_UNESCAPED_UNICODE);
exit;
