<?php
// update.php : 포장 주문 관리 (orders_t 기반)

include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";

$sh_idx = (int)($_SESSION['current_sh_idx'] ?? 0);
$act    = $_POST['act'] ?? '';

// --------------------------------------------------
// 공통: ct_snapshot 파싱 함수
// --------------------------------------------------
function parseCartSnapshot($snapshotJson) {
    $items = [];

    if (empty($snapshotJson)) {
        return $items;
    }

    $snap = json_decode($snapshotJson, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($snap)) {
        return $items;
    }

    // snapshot이 {"items":[...]} 형태일 수도 있고, 바로 [...] 일 수도 있음
    $snapItems = $snap['items'] ?? $snap;
    if (!is_array($snapItems)) {
        return $items;
    }

    foreach ($snapItems as $it) {
        $unitPrice  = (int)($it['unit_price']     ?? $it['ct_price']       ?? 0);
        $totalPrice = (int)($it['total_price']    ?? $it['ct_total_price'] ?? 0);
        $qty        = (int)($it['quantity']       ?? $it['ct_quantity']    ?? 0);

        if ($totalPrice <= 0 && $unitPrice > 0 && $qty > 0) {
            $totalPrice = $unitPrice * $qty;
        }

        $item = [
            'menu_name'   => $it['menu_name'] ?? ($it['sm_title'] ?? ''),
            'quantity'    => $qty,
            'unit_price'  => $unitPrice,
            'total_price' => $totalPrice,
            'options'     => [],
        ];

        if (!empty($it['options']) && is_array($it['options'])) {
            foreach ($it['options'] as $opt) {
                $item['options'][] = [
                    'option_name'  => $opt['option_name']  ?? ($opt['co_option_name']  ?? ''),
                    'option_price' => (int)($opt['option_price'] ?? $opt['co_option_price'] ?? 0),
                ];
            }
        }

        if ($item['menu_name'] !== '') {
            $items[] = $item;
        }
    }

    return $items;
}

// ==================================================
// 리스트 조회
// ==================================================
if ($act === 'list') {

    header('Content-Type: text/html; charset=utf-8');

    $pg        = max(1, (int)($_POST['obj_pg'] ?? 1));
    $limit     = max(1, (int)($_POST['obj_limit_num'] ?? 12));
    $searchTxt = trim($_POST['obj_search_txt'] ?? '');
    $tab       = $_POST['obj_tab'] ?? 'progress'; // progress | done

    $today = date('Y-m-d');

    // 1) 오늘 주문 전체 중, 현재 매장 것만 조회
    if ($sh_idx > 0) {
        $DB->where('sh_idx', $sh_idx);
    }
    $DB->where('DATE(ot_wdate)', $today);
    $DB->orderBy('ot_wdate', 'DESC');

    $rows = $DB->get('orders_t', null, '
        idx,
        mt_idx,
        sh_idx,
        rv_idx,
        ot_number,
        ot_status,
        ot_table,
        ot_total_price,
        ct_snapshot,
        ot_notes,
        ot_cancel,
        ot_cancel_reason,
        ot_wdate,
        ot_udate
    ');

    $ordersRaw = $rows ?: [];

    // 2) 고객 정보(이름, 전화번호) 매핑
    $memberMap = [];
    $mtIds     = [];
    foreach ($ordersRaw as $r) {
        $mid = (int)$r['mt_idx'];
        if ($mid > 0) {
            $mtIds[$mid] = true;
        }
    }
    if (!empty($mtIds)) {
        $mtIds = array_keys($mtIds);
        $DB->where('idx', $mtIds, 'IN');
        $members = $DB->get('member_t', null, 'idx, mt_name, mt_hp');
        foreach ($members as $m) {
            $memberMap[(int)$m['idx']] = [
                'name' => $m['mt_name'] ?? '',
                'hp'   => $m['mt_hp']   ?? '',
            ];
        }
    }

    // 3) 상태 라벨/배지
    $statusLabelMap = [
        'PENDING'    => '접수 대기',
        'CONFIRMED'  => '접수 완료',
        'PREPARING'  => '조리 중',
        'COMPLETED'  => '픽업 완료',
        'CANCELLED'  => '취소',
    ];

    $statusBadgeMap = [
        'PENDING'    => 'bg-secondary',
        'CONFIRMED'  => 'bg-info',
        'PREPARING'  => 'bg-warning text-dark',
        'COMPLETED'  => 'bg-success',
        'CANCELLED'  => 'bg-danger',
    ];

    // 4) 포장 주문 필터링 + 요약 집계 + 검색/탭 필터
    $takeouts          = []; // 요약용 전체 포장 주문
    $displayTakeouts   = []; // 실제 리스트에 보여줄 포장 주문

    $countProgressTotal = 0; // 진행중(완료/취소 제외)
    $countPendingLike   = 0; // PENDING + CONFIRMED
    $countPreparing     = 0; // PREPARING
    $countCompleted     = 0; // COMPLETED

    foreach ($ordersRaw as $row) {
        $status = $row['ot_status'] ?: 'PENDING';
        $rvIdx  = (int)($row['rv_idx'] ?? 0);

        // 🔸 1) 예약/테이블 제외하고 "포장"만 남기기
        // rv_idx > 0 이면 예약 선결제
        if ($rvIdx > 0) {
            continue;
        }
        // ot_table 이 비어있지 않으면 테이블 주문
        if (!empty($row['ot_table'])) {
            continue;
        }

        // 🔸 2) 요약용 공통 포장 주문 집계
        $isDone = in_array($status, ['COMPLETED', 'CANCELLED'], true);

        if (!$isDone) {
            $countProgressTotal++;
        }
        if (in_array($status, ['PENDING', 'CONFIRMED'], true)) {
            $countPendingLike++;
        }
        if ($status === 'PREPARING') {
            $countPreparing++;
        }
        if ($status === 'COMPLETED') {
            $countCompleted++;
        }

        // takeouts 전체 목록에 넣어둠 (필요시 디버깅/확장용)
        $takeouts[] = $row;

        // 🔸 3) 탭(progress/done)에 따른 필터
        if ($tab === 'progress' && $isDone) {
            continue;
        }
        if ($tab === 'done' && !$isDone) {
            continue;
        }

        // 🔸 4) 검색어 (주문번호 / 고객명 / 전화번호)
        $mt_idx       = (int)$row['mt_idx'];
        $customerName = $memberMap[$mt_idx]['name'] ?? '';
        $phone        = $memberMap[$mt_idx]['hp']   ?? '';

        if ($searchTxt !== '') {
            $haystack = ($row['ot_number'] ?? '') . ' ' .
                $customerName . ' ' .
                $phone;
            if (mb_stripos($haystack, $searchTxt) === false) {
                continue;
            }
        }

        // 🔸 5) 시간/경과시간
        $orderTime    = '';
        $elapsedLabel = '';
        if (!empty($row['ot_wdate'])) {
            $ts = strtotime($row['ot_wdate']);
            if ($ts) {
                $orderTime = date('H:i', $ts);
                $diffSec   = time() - $ts;

                if ($diffSec < 60) {
                    $elapsedLabel = $diffSec . '초 전';
                } elseif ($diffSec < 3600) {
                    $elapsedLabel = floor($diffSec / 60) . '분 전';
                } else {
                    $elapsedLabel = floor($diffSec / 3600) . '시간 전';
                }
            }
        }

        // 🔸 6) 메뉴/옵션 (ct_snapshot)
        $items = parseCartSnapshot($row['ct_snapshot']);

        $displayTakeouts[] = [
            'idx'          => (int)$row['idx'],
            'order_no'     => $row['ot_number'],
            'status'       => $status,
            'status_label' => $statusLabelMap[$status] ?? $status,
            'badge_class'  => $statusBadgeMap[$status] ?? 'bg-secondary',
            'customer_name'=> $customerName,
            'phone'        => $phone,
            'order_time'   => $orderTime,
            'elapsed'      => $elapsedLabel,
            'total'        => (float)$row['ot_total_price'],
            'notes'        => $row['ot_notes'],
            'items'        => $items,
        ];
    }

    // 5) 페이징
    $total      = count($displayTakeouts);
    $total_page = max(1, (int)ceil($total / $limit));
    if ($pg > $total_page) $pg = $total_page;

    $offset      = ($pg - 1) * $limit;
    $paged       = array_slice(array_values($displayTakeouts), $offset, $limit);

    ?>
    <!-- 상단 요약 카드 -->
    <div id="summary_content">
        <div class="row g-3">
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100" style="border-radius:16px;">
                    <div class="card-body">
                        <div class="small text-muted mb-1">진행중</div>
                        <div class="h4 fw-bold mb-0"><?= $countProgressTotal ?>건</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100" style="border-radius:16px;">
                    <div class="card-body">
                        <div class="small text-muted mb-1">접수(대기/확정)</div>
                        <div class="h4 fw-bold mb-0"><?= $countPendingLike ?>건</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100" style="border-radius:16px;">
                    <div class="card-body">
                        <div class="small text-muted mb-1">조리중</div>
                        <div class="h4 fw-bold mb-0 text-warning"><?= $countPreparing ?>건</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100" style="border-radius:16px;">
                    <div class="card-body">
                        <div class="small text-muted mb-1">오늘 픽업완료</div>
                        <div class="h4 fw-bold mb-0 text-success"><?= $countCompleted ?>건</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 카드 리스트 -->
    <div id="list_content" class="row g-3 mt-2">
        <?php if (!empty($paged)): ?>
            <?php foreach ($paged as $o): ?>
                <?php $status = $o['status']; ?>
                <div class="col-xl-4 col-lg-6 col-md-6">
                    <div class="card border-0 shadow-sm h-100" style="border-radius:16px;">
                        <div class="card-body d-flex flex-column">

                            <!-- 상단: 주문번호 + 상태 -->
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="fw-semibold">
                                    <?= htmlspecialchars($o['order_no']); ?>
                                </div>
                                <span class="badge rounded-pill <?= $o['badge_class']; ?> px-3 py-2">
                                    <?= htmlspecialchars($o['status_label']); ?>
                                </span>
                            </div>

                            <!-- 고객명 -->
                            <div class="mb-2 small">
                                <div class="fw-semibold">
                                    <?= htmlspecialchars($o['customer_name']); ?>
                                </div>
                                <?php if (!empty($o['phone'])): ?>
                                    <div class="text-muted">
                                        <i class="bi bi-telephone me-1"></i>
                                        <?= htmlspecialchars($o['phone']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- 시간 -->
                            <div class="row small text-muted mb-3">
                                <div class="col-6 mb-2">
                                    <div class="text-muted">주문 시간</div>
                                    <div class="fw-semibold mt-1"><?= htmlspecialchars($o['order_time']); ?></div>
                                </div>
                                <div class="col-6 mb-2 text-end">
                                    <div class="text-muted">경과 시간</div>
                                    <div class="fw-semibold mt-1"><?= htmlspecialchars($o['elapsed']); ?></div>
                                </div>
                            </div>

                            <hr class="my-2">

                            <!-- 주문 메뉴 -->
                            <div class="mb-2 small">
                                <div class="text-muted mb-1">주문 메뉴</div>
                                <?php if (!empty($o['items'])): ?>
                                    <ul class="mb-0 ps-3">
                                        <?php foreach ($o['items'] as $it): ?>
                                            <?php
                                            $lineQty   = (int)($it['quantity'] ?? 0);
                                            $lineTotal = (int)($it['total_price'] ?? 0);
                                            ?>
                                            <li>
                                                <?= htmlspecialchars($it['menu_name']); ?>
                                                x<?= $lineQty; ?>
                                                <?php if ($lineTotal > 0): ?>
                                                    - <?= number_format($lineTotal); ?>원
                                                <?php endif; ?>

                                                <?php if (!empty($it['options'])): ?>
                                                    <ul class="list-unstyled ms-3 mt-1">
                                                        <?php foreach ($it['options'] as $opt): ?>
                                                            <li>
                                                                - <?= htmlspecialchars($opt['option_name']); ?>
                                                                <?php if (!empty($opt['option_price']) && (int)$opt['option_price'] > 0): ?>
                                                                    (<?= number_format((int)$opt['option_price']); ?>원)
                                                                <?php endif; ?>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                <?php endif; ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <div class="text-muted">메뉴 정보 없음</div>
                                <?php endif; ?>
                            </div>

                            <!-- 요청사항 -->
                            <?php if (!empty($o['notes'])): ?>
                                <div class="mb-3 small">
                                    <div class="text-muted mb-1">요청사항</div>
                                    <div>
                                        <?= nl2br(htmlspecialchars($o['notes'])); ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <hr class="my-2">

                            <!-- 총 금액 -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="small text-muted">총 금액</div>
                                <div class="fw-bold">
                                    <?= number_format($o['total']); ?>원
                                </div>
                            </div>

                            <!-- 하단 버튼 -->
                            <div class="mt-auto pt-2">
                                <?php if ($status === 'PENDING'): ?>
                                    <div class="d-flex gap-2">
                                        <button type="button"
                                                class="btn btn-dark flex-grow-1"
                                                onclick="f_takeout_action('accept', '<?= $o['order_no']; ?>')">
                                            접수 확인
                                        </button>
                                        <button type="button"
                                                class="btn btn-outline-secondary flex-grow-1"
                                                onclick="f_takeout_action('cancel', '<?= $o['order_no']; ?>')">
                                            취소
                                        </button>
                                    </div>
                                <?php elseif ($status === 'CONFIRMED'): ?>
                                    <button type="button"
                                            class="btn btn-dark w-100"
                                            onclick="f_takeout_action('prepare', '<?= $o['order_no']; ?>')">
                                        준비 시작
                                    </button>
                                <?php elseif ($status === 'PREPARING'): ?>
                                    <button type="button"
                                            class="btn btn-dark w-100"
                                            onclick="f_takeout_action('pickup', '<?= $o['order_no']; ?>')">
                                        픽업 완료
                                    </button>
                                <?php else: ?>
                                    <a href="/market/orders/view.php?ot_idx=<?= (int)$o['idx']; ?>"
                                       class="btn btn-outline-secondary w-100">
                                        상세보기
                                    </a>
                                <?php endif; ?>
                            </div>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5 text-muted">
                현재 조건에 해당하는 포장 주문이 없습니다.
            </div>
        <?php endif; ?>
    </div>

    <!-- 간단 페이징 -->
    <div id="paging_content" class="d-flex justify-content-center mt-3">
        <?php if ($total_page > 1): ?>
            <ul class="pagination pagination-sm mb-0">
                <?php for ($i=1; $i<=$total_page; $i++): ?>
                    <li class="page-item <?= $i === $pg ? 'active' : '' ?>">
                        <a class="page-link"
                           href="javascript:void(0);"
                           onclick="f_get_takeout_list(<?= $i ?>)"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        <?php endif; ?>
    </div>

    <?php
    exit;
}

// ==================================================
// 포장 주문 상태 변경 (takeout_action)
// ==================================================
if ($act === 'takeout_action') {
    header('Content-Type: application/json; charset=utf-8');

    $orderNo = trim($_POST['order_no'] ?? '');
    $action  = $_POST['action'] ?? '';

    if ($orderNo === '' || $action === '') {
        echo json_encode([
            'success' => false,
            'message' => '유효하지 않은 요청입니다.',
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // 주문 조회
    if ($sh_idx > 0) {
        $DB->where('sh_idx', $sh_idx);
    }
    $DB->where('ot_number', $orderNo);
    $order = $DB->getOne('orders_t', 'idx, ot_status, rv_idx, ot_table');

    if (!$order) {
        echo json_encode([
            'success' => false,
            'message' => '해당 주문을 찾을 수 없습니다.',
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // 포장 주문인지 한번 더 체크 (안전장치)
    if ((int)($order['rv_idx'] ?? 0) > 0 || !empty($order['ot_table'])) {
        echo json_encode([
            'success' => false,
            'message' => '포장 주문이 아니므로 처리할 수 없습니다.',
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $currentStatus = $order['ot_status'] ?: 'PENDING';
    $update        = [];
    $actionName    = '';

    switch ($action) {
        case 'accept': // 접수 확인: PENDING -> CONFIRMED
            if ($currentStatus === 'PENDING') {
                $update['ot_status'] = 'CONFIRMED';
                $actionName          = '접수 확인';
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => '현재 상태에서는 [접수 확인]을 할 수 없습니다.',
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }
            break;

        case 'prepare': // 준비 시작: CONFIRMED -> PREPARING
            if ($currentStatus === 'CONFIRMED') {
                $update['ot_status'] = 'PREPARING';
                $actionName          = '준비 시작';
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => '현재 상태에서는 [준비 시작]을 할 수 없습니다.',
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }
            break;

        case 'pickup': // 픽업 완료: PREPARING -> COMPLETED
            if ($currentStatus === 'PREPARING') {
                $update['ot_status'] = 'COMPLETED';
                $actionName          = '픽업 완료';
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => '현재 상태에서는 [픽업 완료]를 할 수 없습니다.',
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }
            break;

        case 'cancel': // 취소
            if (!in_array($currentStatus, ['COMPLETED', 'CANCELLED'], true)) {
                $update['ot_status'] = 'CANCELLED';
                $actionName          = '주문 취소';
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => '이미 완료되었거나 취소된 주문입니다.',
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }
            break;

        default:
            echo json_encode([
                'success' => false,
                'message' => '알 수 없는 액션입니다.',
            ], JSON_UNESCAPED_UNICODE);
            exit;
    }

    if (!empty($update)) {
        $update['ot_udate'] = $DB->now();

        $DB->where('idx', (int)$order['idx']);
        if ($DB->update('orders_t', $update)) {
            echo json_encode([
                'success' => true,
                'message' => $actionName . '가 정상 처리되었습니다.',
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                'success' => false,
                'message' => $actionName . ' 처리 중 오류가 발생했습니다: ' . $DB->getLastError(),
            ], JSON_UNESCAPED_UNICODE);
        }
        exit;
    }

    echo json_encode([
        'success' => false,
        'message' => '업데이트할 내용이 없습니다.',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// 기타 act
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'success' => false,
    'message' => '지원하지 않는 요청입니다.',
], JSON_UNESCAPED_UNICODE);
exit;
