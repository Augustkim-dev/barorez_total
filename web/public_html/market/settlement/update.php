<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";

header('Content-Type: text/html; charset=utf-8');

if (($_POST['act'] ?? '') === 'list') {
    try {
        // -----------------------------
        // 0. 현재 선택된 매장
        // -----------------------------
        $sh_idx = (int)($_SESSION['current_sh_idx'] ?? 0);

        if ($sh_idx <= 0) {
            throw new Exception('선택된 매장이 없습니다. 매장을 먼저 선택해 주세요.');
        }

        // -----------------------------
        // 1. 필터 파라미터
        // -----------------------------
        $range      = $_POST['range']      ?? 'all';   // all, today, 3d, 7d, 1m
        $start_date = trim($_POST['start_date'] ?? '');
        $end_date   = trim($_POST['end_date']   ?? '');
        $status     = $_POST['status']     ?? 'all';   // all, READY, PLANNED, DONE

        $pg        = max(1, (int)($_POST['pg'] ?? 1));
        $pageSize  = 10;                              // 🔹 페이지당 개수(원하면 조절)

        $today = date('Y-m-d');

        // range !== all 인 경우, st_wdate(정산 신청일) 기준으로 날짜 보정
        if ($range !== 'all') {
            if ($start_date === '' || $end_date === '') {
                if ($range === 'today') {
                    $start_date = $today;
                    $end_date   = $today;
                } elseif ($range === '3d') {
                    $start_date = date('Y-m-d', strtotime('-2 days', strtotime($today)));
                    $end_date   = $today;
                } elseif ($range === '7d') {
                    $start_date = date('Y-m-d', strtotime('-6 days', strtotime($today)));
                    $end_date   = $today;
                } elseif ($range === '1m') {
                    $start_date = date('Y-m-d', strtotime('-1 month', strtotime($today)));
                    $end_date   = $today;
                }
            }
        }

        // -----------------------------
        // 2. DB 조건 구성
        // -----------------------------
        $DB->where('sh_idx', $sh_idx);

        // 정산 신청일(st_wdate) 기준 기간 필터
        if ($range !== 'all' && $start_date && $end_date) {
            $DB->where("DATE(st_wdate) >= ?", [$start_date]);
            $DB->where("DATE(st_wdate) <= ?", [$end_date]);
        }

        // 상태 필터: READY / PLANNED / DONE
        if (in_array($status, ['READY', 'PLANNED', 'DONE'], true)) {
            $DB->where('st_status', $status);
        }

        // 정렬: 정산 예정일 DESC -> 신청일 DESC -> PK DESC
        $DB->orderBy('st_plan_date', 'DESC');
        $DB->orderBy('st_wdate', 'DESC');
        $DB->orderBy('idx', 'DESC');

        // -----------------------------
        // 3. 페이징 조회 (settle_t)
        // -----------------------------
        $DB->pageLimit = $pageSize;
        $list = $DB->arraybuilder()->paginate('settle_t', $pg);

        if ($list === false) {
            throw new Exception('정산 내역을 조회하는 중 오류가 발생했습니다. ' . $DB->getLastError());
        }

        $n_page = (int)$DB->totalPages;

        // -----------------------------
        // 4. HTML <tr> 조각 만들기
        // -----------------------------
        ob_start();

        if (empty($list)) {
            ?>
            <tr>
                <td colspan="8" class="text-center py-4 text-muted">
                    조회된 정산 내역이 없습니다.
                </td>
            </tr>
            <?php
        } else {
            foreach ($list as $row) {
                // 정산번호
                $settleNo = $row['st_number'];

                // 정산 예정일
                $planDate = $row['st_plan_date'] ? substr($row['st_plan_date'], 0, 10) : '-';

                // 정산 기간 (st_start_date ~ st_end_date)
                $startDate = $row['st_start_date'] ? substr($row['st_start_date'], 0, 10) : '';
                $endDate   = $row['st_end_date']   ? substr($row['st_end_date'], 0, 10)   : '';

                $periodText = ($startDate && $endDate)
                    ? $startDate . ' ~ ' . $endDate
                    : ($startDate ?: $endDate ?: '-');

                // 금액들
                $totalAmount = (float)$row['st_total_amount'];  // 매출액
                $serviceFee  = (float)$row['st_service_fee'];   // 수수료
                $finalAmount = (float)$row['st_final_amount'];  // 정산 금액

                $totalAmountText = number_format((int)round($totalAmount)) . '원';
                $serviceFeeText  = number_format((int)round($serviceFee))  . '원';
                $finalAmountText = number_format((int)round($finalAmount)) . '원';

                // 상태 뱃지
                $statusCode = $row['st_status']; // READY / PLANNED / DONE

                if ($statusCode === 'DONE') {
                    $badgeClass = 'bg-success-subtle text-success';
                    $badgeLabel = '정산완료';
                } elseif ($statusCode === 'PLANNED') {
                    $badgeClass = 'bg-info-subtle text-info';
                    $badgeLabel = '정산예정';
                } else { // READY
                    $badgeClass = 'bg-warning-subtle text-warning';
                    $badgeLabel = '미정산';
                }
                ?>
                <tr>
                    <!-- 정산번호 -->
                    <td><?= htmlspecialchars($settleNo, ENT_QUOTES, 'UTF-8'); ?></td>

                    <!-- 정산 예정일 -->
                    <td><?= htmlspecialchars($planDate, ENT_QUOTES, 'UTF-8'); ?></td>

                    <!-- 매출액 -->
                    <td class="text-end"><?= $totalAmountText; ?></td>

                    <!-- 수수료 -->
                    <td class="text-end"><?= $serviceFeeText; ?></td>

                    <!-- 정산 금액 -->
                    <td class="text-end fw-bold"><?= $finalAmountText; ?></td>

                    <!-- 정산 기간 -->
                    <td class="text-center"><?= htmlspecialchars($periodText, ENT_QUOTES, 'UTF-8'); ?></td>

                    <!-- 상태 -->
                    <td class="text-center">
                        <span class="badge <?= $badgeClass; ?>">
                            <?= $badgeLabel; ?>
                        </span>
                    </td>

                    <!-- 관리 -->
                    <td class="text-center">
                        <button type="button"
                                class="btn btn-sm btn-outline-secondary"
                                onclick="goSettleDetail('<?= htmlspecialchars($settleNo, ENT_QUOTES, 'UTF-8'); ?>')">
                            상세
                        </button>
                    </td>
                </tr>
                <?php
            }
        }

        $html = ob_get_clean();

        // -----------------------------
        // 5. 페이징 HTML 생성
        // -----------------------------
        $pagingHtml = '';
        if ($n_page > 1) {
            // page_listing_xhr(현재페이지, 전체페이지수, JS콜백함수명)
            $pagingHtml = page_listing_xhr($pg, $n_page, 'f_get_settle_list');
        }

        echo json_encode([
            'success' => true,
            'html'    => $html,
            'paging'  => $pagingHtml,
        ], JSON_UNESCAPED_UNICODE);

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage(),
        ], JSON_UNESCAPED_UNICODE);
    }

    exit;
}

if (($_POST['act'] ?? '') === 'settle_update') {
    header('Content-Type: application/json; charset=utf-8');

    $settle_no      = $_POST['settle_no']      ?? '';
    $settle_status  = $_POST['settle_status']  ?? 'N';
    $settle_date    = $_POST['settle_date']    ?? '';
    $service_fee    = preg_replace('/[^0-9]/', '', $_POST['service_fee']     ?? '0');
    $expected_amount= preg_replace('/[^0-9]/', '', $_POST['expected_amount'] ?? '0');

    if ($settle_no === '') {
        echo json_encode([
            'success' => false,
            'message' => '정산번호가 존재하지 않습니다.'
        ]);
        exit;
    }

    // TODO: 실제 구현 시 DB 업데이트
    // 예)
    // $DB->where('settle_no', $settle_no);
    // $DB->update('settle_t', [
    //     'settle_status'   => $settle_status,
    //     'settle_date'     => $settle_date ?: null,
    //     'service_fee'     => (int)$service_fee,
    //     'expected_amount' => (int)$expected_amount,
    //     'settle_udate'    => $DB->now(),
    // ]);

    echo json_encode([
        'success'  => true,
        'message'  => '정산 정보가 저장되었습니다.',
        'redirect' => './settle_list.php'  // 실제 리스트 파일명에 맞게 수정
    ]);
    exit;
}

// 그 외 act에 대해서는 아직 처리 안 함
http_response_code(400);
echo 'Invalid request';
exit;
