<?php
// update.php: 실시간 주문 데이터 처리 (orders_t 기반)

include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";

$sh_idx = (int)($_SESSION['current_sh_idx'] ?? 0);

$act = $_POST['act'] ?? '';

/**
 * 경과 시간 문자열 생성 (현재는 사용 안하지만 혹시 몰라 남겨둠)
 */
function formatElapsedTime($orderDateTime)
{
    if (empty($orderDateTime)) {
        return '-';
    }

    try {
        $orderTime = new DateTime($orderDateTime);
    } catch (Exception $e) {
        return '-';
    }

    $now  = new DateTime();
    $diff = $now->getTimestamp() - $orderTime->getTimestamp();

    if ($diff < 60) {
        return '방금 전';
    }
    $mins = floor($diff / 60);
    if ($mins < 60) {
        return $mins . '분 전';
    }
    $hours = floor($mins / 60);
    if ($hours < 24) {
        return $hours . '시간 전';
    }
    $days = floor($hours / 24);
    return $days . '일 전';
}

// =====================================================
// 리스트 조회
// =====================================================
if ($act === 'list') {

    header('Content-Type: text/html; charset=utf-8');

    $sh_idx     = (int)($_SESSION['current_sh_idx'] ?? 0);
    $tab        = $_POST['tab'] ?? 'current';              // current | completed | cancelled
    $orderType  = $_POST['obj_order_type'] ?? 'all';       // all | table | takeout | reservation
    $keyword    = trim($_POST['obj_search_txt'] ?? '');

    $pg    = max(1, (int)($_POST['obj_pg'] ?? 1));
    $limit = max(1, (int)($_POST['obj_limit_num'] ?? 12));

    // 🔹 오늘 날짜만 필터
    $today = date('Y-m-d');

    // 1) 오늘 주문 조회 (orders_t)
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
        ot_pay_type,       
        ot_pay_status,      
        ot_pay_date, 
        ot_wdate,
        ot_udate
    ');

    $ordersRaw = $rows ?: [];

    // 2) 고객 정보(이름, 전화번호) 매핑용 (member_t)
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
                'hp'   => $m['mt_hp'] ?? '',
            ];
        }
    }

    // 3) 예약 정보 매핑용 (reservation_t) - rv_idx 기준
    $reservationMap = [];
    $rvIds          = [];
    foreach ($ordersRaw as $r) {
        $rv = (int)($r['rv_idx'] ?? 0);
        if ($rv > 0) {
            $rvIds[$rv] = true;
        }
    }

    if (!empty($rvIds)) {
        $rvIds = array_keys($rvIds);
        $DB->where('idx', $rvIds, 'IN');
        if ($sh_idx > 0) {
            $DB->where('sh_idx', $sh_idx);
        }
        // 예약 타입/정보 조회
        $reservations = $DB->get('reservation_t', null, '
            idx,
            sh_idx,
            rv_name,
            rv_hp,
            rv_date,
            rv_time,
            rv_people,
            rv_type,
            rv_table
        ');

        foreach ($reservations as $rv) {
            $reservationMap[(int)$rv['idx']] = [
                'rv_idx'   => (int)$rv['idx'],
                'name'     => $rv['rv_name']  ?? '',
                'phone'    => $rv['rv_hp'] ?? '',
                'date'     => $rv['rv_date']  ?? '',
                'time'     => $rv['rv_time']  ?? '',
                'people'   => (int)($rv['rv_people'] ?? 0),
                'rv_type'  => $rv['rv_type']  ?? 'VISIT',   // VISIT | PREPAID
                'rv_table' => $rv['rv_table'] ?? '',
            ];
        }
    }

    // 4) 상태 라벨 & 뱃지 CSS 매핑
    $statusLabelMap = [
        'PENDING'    => '접수 대기',
        'CONFIRMED'  => '접수 완료',
        'PREPARING'  => '조리 중',
        'COMPLETED'  => '완료',
        'CANCELLED'  => '취소',
    ];

    $statusBadgeMap = [
        'PENDING'    => 'bg-secondary',
        'CONFIRMED'  => 'bg-info',
        'PREPARING'  => 'bg-warning text-dark',
        'COMPLETED'  => 'bg-success',
        'CANCELLED'  => 'bg-danger',
    ];

    $orders = [];

    foreach ($ordersRaw as $row) {
        $idx    = (int)$row['idx'];
        $status = $row['ot_status'] ?: 'PENDING';
        $rv_idx = (int)($row['rv_idx'] ?? 0);

        // 🔹 탭 필터
        if ($tab === 'current' && in_array($status, ['COMPLETED', 'CANCELLED'], true)) {
            continue;
        }
        if ($tab === 'completed' && $status !== 'COMPLETED') {
            continue;
        }
        if ($tab === 'cancelled' && $status !== 'CANCELLED') {
            continue;
        }

        // 🔹 예약 정보 가져오기 (rv_idx > 0 이면 예약 주문)
        $resv = null;
        if ($rv_idx > 0 && isset($reservationMap[$rv_idx])) {
            $resv = $reservationMap[$rv_idx];

            // 👉 실시간 주문에서는 "선결제 예약"만 보여야 하므로
            //    예약 타입이 PREPAID 가 아닌 경우는 스킵
            if (!empty($resv['rv_type']) && $resv['rv_type'] !== 'PREPAID') {
                continue;
            }
        }

        // 🔹 주문 타입 계산: reservation / table / takeout
        $type           = 'takeout';
        $orderTypeLabel = '포장 주문';
        $tableLabel     = '';

        if ($rv_idx > 0) {
            // 예약키가 있으면 예약 주문(선결제)으로 처리
            $type           = 'reservation';
            $orderTypeLabel = '예약 주문(선결제)';
            if (!empty($resv['rv_table'])) {
                $tableLabel = $resv['rv_table'].'번 테이블';
            }
        } elseif (!empty($row['ot_table'])) {
            $type           = 'table';
            $orderTypeLabel = '테이블 주문';
            $tableLabel     = $row['ot_table'].'번 테이블';
        } else {
            $type           = 'takeout';
            $orderTypeLabel = '포장 주문';
        }

        // ✅ 결제 방식/상태 (없던 예전 데이터 대비 기본값 세팅)
        $payType   = $row['ot_pay_type']   ?: 'PREPAID';
        $payStatus = $row['ot_pay_status'] ?: 'PAID';  // 기존 주문은 선결제 + 결제완료로 가정

        // 주문 타입 필터 (UI에서 선택한 경우)
        if ($orderType !== 'all' && $type !== $orderType) {
            continue;
        }

        // 🔹 고객 정보 (예약 정보가 우선, 없으면 member_t)
        $mt_idx       = (int)$row['mt_idx'];
        $memberName   = $memberMap[$mt_idx]['name'] ?? '';
        $memberPhone  = $memberMap[$mt_idx]['hp']   ?? '';

        $customerName = $resv['name']  ?? $memberName;
        $phone        = $resv['phone'] ?? $memberPhone;

        // 🔹 검색어 필터 (주문번호 / 테이블 / 메모 / 고객명 / 전화번호 / 예약일)
        if ($keyword !== '') {
            $haystack = ($row['ot_number'] ?? '') . ' ' .
                $tableLabel . ' ' .
                $customerName . ' ' .
                $phone . ' ' .
                ($row['ot_notes'] ?? '') . ' ' .
                ($resv['date'] ?? '');

            if (mb_stripos($haystack, $keyword) === false) {
                continue;
            }
        }

        // 🔹 주문 시간 / 경과 시간 표시용 (결제 생성 시각 기준)
        $orderTime    = '';
        $elapsedLabel = '';
        if (!empty($row['ot_wdate'])) {
            $ts = strtotime($row['ot_wdate']);
            if ($ts) {
                $orderTime = date('H:i', $ts);

                $diffSec = time() - $ts;
                if ($diffSec < 60) {
                    $elapsedLabel = $diffSec . '초 전';
                } elseif ($diffSec < 3600) {
                    $elapsedLabel = floor($diffSec / 60) . '분 전';
                } else {
                    $elapsedLabel = floor($diffSec / 3600) . '시간 전';
                }
            }
        }

        // 🔹 상태 텍스트 + 뱃지
        $statusLabel = $statusLabelMap[$status] ?? $status;
        $badgeClass  = $statusBadgeMap[$status] ?? 'bg-secondary';

        // 🔹 ct_snapshot 파싱 → 주문 메뉴 items 구성
        $items = [];
        if (!empty($row['ct_snapshot'])) {
            $snap = json_decode($row['ct_snapshot'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($snap)) {
                // snapshot이 {"items":[...]} 형태일 수도 있고, 바로 [...]일 수도 있음
                $snapItems = $snap['items'] ?? $snap;
                if (is_array($snapItems)) {
                    foreach ($snapItems as $it) {
                        $unitPrice  = (int)($it['unit_price']  ?? $it['ct_price']       ?? 0);
                        $totalPrice = (int)($it['total_price'] ?? $it['ct_total_price'] ?? 0);
                        $qty        = (int)($it['quantity']    ?? $it['ct_quantity']    ?? 0);

                        // total_price가 없으면 단가 * 수량으로 계산
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
                }
            }
        }

        // 🔹 예약 정보 정리 (카드에서 쓰기 편하게)
        $reservationInfo = null;
        if ($resv) {
            $reservationInfo = [
                'rv_idx'  => $resv['rv_idx'],
                'date'    => $resv['date'],
                'time'    => $resv['time'],
                'people'  => $resv['people'],
                'rv_type' => $resv['rv_type'],
                'table'   => $resv['rv_table'],
            ];
        }

        $orders[] = [
            'idx'           => $idx,
            'rv_idx'        => $rv_idx,
            'order_no'      => $row['ot_number'],
            'type'          => $type,              // table | takeout | reservation
            'order_type'    => $orderTypeLabel,
            'status'        => $status,
            'status_label'  => $statusLabel,
            'badge_class'   => $badgeClass,
            'table_label'   => $tableLabel,
            'customer_name' => $customerName,
            'phone'         => $phone,
            'order_time'    => $orderTime,
            'elapsed'       => $elapsedLabel,
            'total'         => (float)$row['ot_total_price'],
            'notes'         => $row['ot_notes'],
            'items'         => $items,
            'reservation'   => $reservationInfo,
            'pay_type'      => $payType,
            'pay_status'    => $payStatus,
        ];
    }

    // 5) 페이징
    $total      = count($orders);
    $totalPages = max(1, (int)ceil($total / $limit));
    if ($pg > $totalPages) $pg = $totalPages;

    $offset      = ($pg - 1) * $limit;
    $pagedOrders = array_slice(array_values($orders), $offset, $limit);

    ?>
    <!-- 리스트 영역 -->
    <div id="list_content" class="row g-3">
        <?php if (!empty($pagedOrders)): ?>
            <?php foreach ($pagedOrders as $order): ?>
                <?php
                $status        = $order['status'] ?? 'PENDING';
                $isReservation = ($order['type'] === 'reservation');

                // ✅ 결제 배지 표시용
                $payType   = $order['pay_type']   ?? 'PREPAID';
                $payStatus = $order['pay_status'] ?? 'PAID';
                $payBadgeHtml = '';

                if ($order['type'] === 'table') {
                    if ($payType === 'PREPAID' && $payStatus === 'PAID') {
                        $payBadgeHtml = '<span class="badge bg-success-subtle text-success ms-2">선결제</span>';
                    } elseif ($payType === 'POSTPAID' && $payStatus === 'UNPAID') {
                        $payBadgeHtml = '<span class="badge bg-warning-subtle text-warning ms-2">후결제 · 미결제</span>';
                    } elseif ($payType === 'POSTPAID' && $payStatus === 'PAID') {
                        $payBadgeHtml = '<span class="badge bg-primary-subtle text-primary ms-2">후결제 · 결제완료</span>';
                    }
                }

                // ✅ 후결제 미결제일 때만 "결제 처리" 버튼 노출
                $showPayButton = (
                    $order['type'] === 'table'
                    && $payType === 'POSTPAID'
                    && $payStatus === 'UNPAID'
                );

                // 🔹 상태별 메인 버튼
                $primaryButtons = [];
                if ($status === 'PENDING') {
                    $primaryButtons[] = ['label' => '주문 접수', 'action' => 'accept',  'class' => 'btn-dark'];
                    $primaryButtons[] = ['label' => '주문 취소', 'action' => 'cancel',  'class' => 'btn-outline-danger'];
                } elseif ($status === 'CONFIRMED') {
                    $primaryButtons[] = ['label' => '조리 시작', 'action' => 'accept',  'class' => 'btn-dark'];
                } elseif ($status === 'PREPARING') {
                    $primaryButtons[] = ['label' => '조리 완료', 'action' => 'complete','class' => 'btn-dark'];
                }
                ?>
                <div class="col-xl-3 col-lg-4 col-md-6 mb-5">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body d-flex flex-column">

                            <!-- 상단: 주문 타입 / 상태 -->
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <div class="small text-muted mb-1">
                                        <?= htmlspecialchars($order['order_type']) ?>
                                    </div>
                                    <h6 class="fw-bold mb-0">
                                        <?= htmlspecialchars($order['order_no']) ?>
                                        <?= $payBadgeHtml // ✅ 결제 타입 뱃지 ?>
                                    </h6>
                                </div>
                                <span class="badge rounded-pill <?= $order['badge_class'] ?>">
                                    <?= htmlspecialchars($order['status_label']) ?>
                                </span>
                            </div>

                            <!-- 주문 기본 정보 -->
                            <div class="mb-2 small">
                                <?php if ($isReservation && !empty($order['reservation'])): ?>
                                    <!-- 예약(선결제) 주문일 경우 예약 정보 우선 표시 -->
                                    <div class="mb-1">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        <?= htmlspecialchars($order['reservation']['date'] ?? '') ?>
                                        <?php if (!empty($order['reservation']['time'])): ?>
                                            <?= ' ' . htmlspecialchars(substr($order['reservation']['time'], 0, 5)) ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="mb-1">
                                        <i class="bi bi-people me-1"></i>
                                        <?= (int)($order['reservation']['people'] ?? 0) ?>명
                                    </div>
                                    <?php if (!empty($order['reservation']['table'])): ?>
                                        <div class="mb-1">
                                            <i class="bi bi-grid-3x3-gap me-1"></i>
                                            <?= htmlspecialchars($order['reservation']['table']) ?>번 테이블
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($order['customer_name'])): ?>
                                        <div class="fw-semibold mb-1">
                                            <?= htmlspecialchars($order['customer_name']) ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($order['phone'])): ?>
                                        <div class="text-muted">
                                            <?= htmlspecialchars($order['phone']) ?>
                                        </div>
                                    <?php endif; ?>

                                <?php else: ?>
                                    <!-- 일반 테이블/포장 주문 -->
                                    <?php if ($order['type'] === 'table'): ?>
                                        <div class="mb-1"><?= htmlspecialchars($order['table_label']) ?></div>
                                    <?php else: ?>
                                        <?php if (!empty($order['customer_name'])): ?>
                                            <div class="fw-semibold mb-1">
                                                <?= htmlspecialchars($order['customer_name']) ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($order['phone'])): ?>
                                            <div class="text-muted">
                                                <?= htmlspecialchars($order['phone']) ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if (!empty($order['notes'])): ?>
                                    <div class="mt-1 text-muted">
                                        <i class="bi bi-chat-dots me-1"></i>
                                        <?= nl2br(htmlspecialchars($order['notes'])) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- 주문 시간 / 경과 시간 -->
                            <div class="d-flex justify-content-between align-items-center text-muted small mb-3">
                                <span>주문시간: <?= htmlspecialchars($order['order_time']) ?></span>
                                <span><i class="bi bi-clock me-1"></i><?= htmlspecialchars($order['elapsed']) ?></span>
                            </div>

                            <hr class="my-2">

                            <!-- 주문 메뉴 (ct_snapshot 기반) -->
                            <div class="mb-3">
                                <div class="small text-muted mb-1">주문 메뉴</div>
                                <?php if (!empty($order['items'])): ?>
                                    <ul class="list-unstyled small mb-0">
                                        <?php foreach ($order['items'] as $item): ?>
                                            <?php
                                            $lineQty   = (int)($item['quantity'] ?? 0);
                                            $unitPrice = (int)($item['unit_price'] ?? 0);
                                            $lineTotal = (int)($item['total_price'] ?? 0);

                                            if ($lineTotal <= 0 && $unitPrice > 0 && $lineQty > 0) {
                                                $lineTotal = $unitPrice * $lineQty;
                                            }
                                            ?>
                                            <li>
                                                • <?= htmlspecialchars($item['menu_name']) ?>
                                                x <?= $lineQty ?>
                                                <?php if ($lineTotal > 0): ?>
                                                    - <?= number_format($lineTotal) ?>원
                                                <?php endif; ?>

                                                <?php if (!empty($item['options'])): ?>
                                                    <ul class="list-unstyled ms-3 mt-1">
                                                        <?php foreach ($item['options'] as $opt): ?>
                                                            <li>
                                                                - <?= htmlspecialchars($opt['option_name']) ?>
                                                                <?php if (!empty($opt['option_price']) && (int)$opt['option_price'] > 0): ?>
                                                                    (<?= number_format((int)$opt['option_price']) ?>원)
                                                                <?php endif; ?>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                <?php endif; ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <div class="small text-muted">주문 메뉴 정보가 없습니다.</div>
                                <?php endif; ?>
                            </div>

                            <!-- 총 금액 + 버튼 -->
                            <div class="mt-auto pt-2">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small text-muted">총 금액</span>
                                    <span class="fw-bold"><?= number_format($order['total']) ?>원</span>
                                </div>

                                <div class="d-flex gap-2">
                                    <?php foreach ($primaryButtons as $btn): ?>
                                        <button type="button"
                                                class="btn <?= $btn['class'] ?> flex-fill"
                                                onclick="f_action_order('<?= $btn['action'] ?>', <?= (int)$order['idx'] ?>)">
                                            <?= htmlspecialchars($btn['label']) ?>
                                        </button>
                                    <?php endforeach; ?>

                                    <?php if ($showPayButton): // ✅ 후결제 미결제일 때만 노출 ?>
                                        <button type="button"
                                                class="btn btn-outline-primary flex-fill"
                                                onclick="f_action_order('pay', <?= (int)$order['idx'] ?>)">
                                            결제 처리
                                        </button>
                                    <?php endif; ?>

                                    <!-- 상세보기 공통 -->
<!--                                    <a href="./view.php?ot_idx=--><?php //= (int)$order['idx'] ?><!--"-->
<!--                                       class="btn btn-outline-secondary flex-fill">-->
<!--                                        상세보기-->
<!--                                    </a>-->
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5 text-muted">
                오늘 날짜에 해당하는 주문이 없습니다.
            </div>
        <?php endif; ?>
    </div>

    <!-- 페이징 영역 -->
    <div id="paging_content" class="mt-3">
        <?php if ($totalPages > 1): ?>
            <nav>
                <ul class="pagination justify-content-center mb-0">
                    <?php
                    $prevPg = max(1, $pg - 1);
                    $nextPg = min($totalPages, $pg + 1);
                    ?>
                    <li class="page-item <?= $pg <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="javascript:void(0);" data-pg="<?= $prevPg ?>"
                           onclick="f_get_box_mng_list(<?= $prevPg ?>)">이전</a>
                    </li>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i == $pg ? 'active' : '' ?>">
                            <a class="page-link" href="javascript:void(0);" data-pg="<?= $i ?>"
                               onclick="f_get_box_mng_list(<?= $i ?>)"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <li class="page-item <?= $pg >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link" href="javascript:void(0);" data-pg="<?= $nextPg ?>"
                           onclick="f_get_box_mng_list(<?= $nextPg ?>)">다음</a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </div>

    <?php
    exit;
}

// =====================================================
// 상태 변경 (accept / complete / serve / cancel / del)
// =====================================================
elseif (
    $_POST['act'] === 'accept' ||
    $_POST['act'] === 'complete' ||
    $_POST['act'] === 'serve' ||
    $_POST['act'] === 'cancel' ||
    $_POST['act'] === 'del'   ||
    $_POST['act'] === 'pay'
) {
    header('Content-Type: application/json; charset=utf-8');

    $act    = $_POST['act'];
    // JS 쪽에서 아직 nt_idx 라는 이름으로 보내고 있으니 그대로 받음
    $ot_idx = (int)($_POST['nt_idx'] ?? 0);

    if ($ot_idx <= 0) {
        echo json_encode([
            'success' => false,
            'message' => '유효하지 않은 주문입니다. (인덱스 누락)'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // 현재 주문 정보 조회
    $DB->where('idx', $ot_idx);
    $order = $DB->getOne('orders_t', 'idx, ot_status, ot_pay_type, ot_pay_status');

    if (!$order) {
        echo json_encode([
            'success' => false,
            'message' => '유효하지 않은 주문입니다. (주문을 찾을 수 없습니다)'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $currentStatus = $order['ot_status'] ?: 'PENDING';
    $update        = [];
    $actionName    = '';

    /**
     * 상태 흐름
     * PENDING    -> (accept)   -> CONFIRMED
     * CONFIRMED  -> (accept)   -> PREPARING
     * PREPARING  -> (complete) -> COMPLETED
     * COMPLETED  -> (serve)    -> 완료 처리 (화면엔 변화 없지만, 필요 시 archived 등 추가 가능)
     */

    if ($act === 'accept') {
        if ($currentStatus === 'PENDING') {
            $update['ot_status'] = 'CONFIRMED';
            $actionName          = '주문 접수';
        } elseif ($currentStatus === 'CONFIRMED') {
            $update['ot_status'] = 'PREPARING';
            $actionName          = '조리 시작';
        } else {
            echo json_encode([
                'success' => false,
                'message' => '현재 상태에서는 [주문 접수/조리 시작]을 할 수 없습니다.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
    } elseif ($act === 'complete') {
        if ($currentStatus === 'PREPARING' || $currentStatus === 'CONFIRMED') {
            $update['ot_status'] = 'COMPLETED';
            $actionName          = '조리 완료';
        } else {
            echo json_encode([
                'success' => false,
                'message' => '현재 상태에서는 [조리 완료]로 변경할 수 없습니다.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
    } elseif ($act === 'serve') {
        // 🔸 "완료 처리" 단계 - 필요 시 별도 컬럼 추가해서 필터링
        $actionName = '완료 처리';
    } elseif ($act === 'cancel') {
        // ✅ 주문 취소
        $update['ot_status'] = 'CANCELLED';
        $actionName          = '주문 취소';
    } elseif ($act === 'pay') {
        // ✅ 후결제 주문 결제완료 처리
        if (($order['ot_pay_type'] ?? '') !== 'POSTPAID' || ($order['ot_pay_status'] ?? '') !== 'UNPAID') {
            echo json_encode([
                'success' => false,
                'message' => '후결제 미결제 상태의 주문만 결제 처리할 수 있습니다.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $update['ot_pay_status'] = 'PAID';
        $update['ot_pay_date']   = $DB->now();
        $actionName              = '결제 완료';

    } elseif ($act === 'del') {
        // 삭제 처리 (지금은 물리 삭제)
        $DB->where('idx', $ot_idx);
        if ($DB->delete('orders_t')) {
            echo json_encode([
                'success' => true,
                'message' => '주문이 삭제되었습니다.'
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                'success' => false,
                'message' => '주문 삭제 중 오류가 발생했습니다: ' . $DB->getLastError()
            ], JSON_UNESCAPED_UNICODE);
        }
        exit;
    }

    // 업데이트할 내용이 있으면 실행
    if (!empty($update)) {
        $update['ot_udate'] = $DB->now();

        $DB->where('idx', $ot_idx);
        if ($DB->update('orders_t', $update)) {
            echo json_encode([
                'success' => true,
                'message' => $actionName . '가 정상 처리되었습니다.'
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                'success' => false,
                'message' => $actionName . ' 처리 중 오류가 발생했습니다: ' . $DB->getLastError()
            ], JSON_UNESCAPED_UNICODE);
        }
        exit;
    }

    // serve 처럼 실질적인 update 없이도 성공으로 처리하는 경우
    echo json_encode([
        'success' => true,
        'message' => ($actionName ?: '요청') . '가 정상 처리되었습니다.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// =====================================================
// (선택) 완전 삭제 (별도 del 분기) - 필요 없으면 제거 가능
// =====================================================
if ($act === 'del') {
    header('Content-Type: application/json; charset=utf-8');

    if ($sh_idx <= 0) {
        echo json_encode([
            'success' => false,
            'message' => '매장 정보가 없습니다.',
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $ot_idx = (int)($_POST['ot_idx'] ?? 0);
    if ($ot_idx <= 0) {
        echo json_encode([
            'success' => false,
            'message' => '유효하지 않은 주문입니다.',
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $DB->where('idx', $ot_idx);
    $DB->where('sh_idx', $sh_idx);
    $ok = $DB->delete('orders_t');

    if (!$ok) {
        echo json_encode([
            'success' => false,
            'message' => '삭제 중 오류가 발생했습니다: ' . $DB->getLastError(),
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    echo json_encode([
        'success' => true,
        'message' => '주문이 삭제되었습니다.',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// 그 외 act
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'success' => false,
    'message' => '지원하지 않는 요청입니다.',
], JSON_UNESCAPED_UNICODE);
exit;
