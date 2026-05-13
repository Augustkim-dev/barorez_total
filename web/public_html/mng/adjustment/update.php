<?php
// /mng/settle/update.php

include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";

header('Content-Type: text/html; charset=utf-8');

$tbl_shop_name   = $CFG_TBL['shop']['default'];   // shop_t
$tbl_orders_name = $CFG_TBL['orders']['default']; // orders_t
$tbl_settle_name = $CFG_TBL['settle']['default']; // settle_t

// 현재 settlement ACT
$act = $_POST['act'] ?? '';

/**
 * ✅ [정책변경]
 * 기존: 16일~다음달 15일
 * 변경: "월 정산" → 매월 1일 ~ 말일
 *
 * (shop_list에서 이번 정산기간 등 참고용)
 */
function getCurrentSettlePeriod()
{
    $startDate = date('Y-m-01');
    $endDate   = date('Y-m-t');

    return [
        'start' => $startDate . ' 00:00:00',
        'end'   => $endDate . ' 23:59:59',
    ];
}

/**
 * 공통 JSON 응답 헬퍼
 */
function json_response($success, $message = '', $extra = [])
{
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array_merge([
        'success' => $success,
        'message' => $message,
    ], $extra), JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * ✅ [정책변경]
 * 기준일(YYYY-mm-dd)을 기준으로, 해당 날짜가 속한 "월의 시작일(1일)"을 구함
 */
function getPeriodStartFromDate($ymd)
{
    $ts = strtotime($ymd);
    if ($ts === false) return null;
    return date('Y-m-01', $ts);
}

/**
 * ✅ [정책변경]
 * 기간 시작일(YYYY-mm-01)을 기준으로 해당 월의 말일을 구함
 */
function getPeriodEndFromStart($startYmd)
{
    $ts = strtotime($startYmd);
    if ($ts === false) return null;
    return date('Y-m-t', $ts);
}

/**
 * ✅ [정책변경]
 * 다음 월의 시작일(현재 시작일 기준 +1개월 1일)
 */
function getNextPeriodStart($startYmd)
{
    $ts = strtotime($startYmd);
    if ($ts === false) return null;
    return date('Y-m-01', strtotime('+1 month', $ts));
}

/**
 * ✅ [정책변경/보완]
 * 입력된 from/to를 "월 전체(1일~말일)"로 정규화
 * - from: 해당 월 1일
 * - to  : 해당 월 말일
 */
function normalizeMonthRange($fromYmd, $toYmd)
{
    $fromStart = getPeriodStartFromDate($fromYmd);
    $toStart   = getPeriodStartFromDate($toYmd);

    if (!$fromStart || !$toStart) return [null, null];

    $fromFixed = $fromStart;
    $toFixed   = getPeriodEndFromStart($toStart); // toYmd가 속한 월의 말일

    return [$fromFixed, $toFixed];
}

// =========================================================
// ① 매장 리스트 (정산 상태 포함)
//    act = shop_list
// =========================================================
if ($act === 'shop_list') {

    unset($list);

    $pageLimit        = (int)($_POST['obj_limit_num'] ?? 10);
    $DB->pageLimit    = $pageLimit;
    $pg               = (int)($_POST['obj_pg'] ?? 1);

    $obj_location     = $_POST['obj_search_location'] ?? 'all';
    $obj_sel_search   = $_POST['obj_sel_search'] ?? 'all';
    $obj_search_txt   = trim($_POST['obj_search_txt'] ?? '');
    $obj_settle_stat  = $_POST['obj_settle_status'] ?? 'all';

    // ✅ [정책변경] 현재 정산 기간(이번달 1일~말일)
    $period      = getCurrentSettlePeriod();
    $settleStart = $period['start'];
    $settleEnd   = $period['end'];

    /**
     * 🔹 전체 기준 정산 관련 서브쿼리
     *  - total_paid_count        : 해당 매장 전체 기간 동안 결제 완료 건수
     *  - unsettled_all_count     : 전체 기간 동안 결제 완료 + 미정산(N) 인 건수
     *  - earliest_unsettled_date : 전체 기간 동안 미정산 주문 중 가장 오래된 결제일
     */
    $totalPaidExpr = "(
        SELECT COUNT(*)
        FROM {$tbl_orders_name} o
        WHERE o.sh_idx = s.idx
          AND o.ot_pay_date IS NOT NULL
          AND o.ot_pay_status = 'PAID'
    )";

    $unsettledAllExpr = "(
        SELECT COUNT(*)
        FROM {$tbl_orders_name} o
        WHERE o.sh_idx = s.idx
          AND o.ot_pay_date IS NOT NULL
          AND o.ot_pay_status = 'PAID'
          AND o.ot_settle_yn = 'N'
    )";

    $earliestUnsettledExpr = "(
        SELECT MIN(o.ot_pay_date)
        FROM {$tbl_orders_name} o
        WHERE o.sh_idx = s.idx
          AND o.ot_pay_date IS NOT NULL
          AND o.ot_pay_status = 'PAID'
          AND o.ot_settle_yn = 'N'
    )";

    // (선택) 이번달 정산기간(1~말일) 안에서만 미정산 건이 얼마나 있는지
    $unsettledCurrentExpr = "(
        SELECT COUNT(*)
        FROM {$tbl_orders_name} o
        WHERE o.sh_idx = s.idx
          AND o.ot_pay_date IS NOT NULL
          AND o.ot_pay_status = 'PAID'
          AND o.ot_settle_yn = 'N'
          AND o.ot_pay_date BETWEEN '{$settleStart}' AND '{$settleEnd}'
    )";

    // =========================
    // 기본 조건
    // =========================
    $DB->where('s.del_date', null, 'IS');

    // 지역 필터
    if ($obj_location !== '' && $obj_location !== 'all') {
        $DB->where('s.sh_addr1', '%' . $obj_location . '%', 'LIKE');
    }

    // 검색어 필터
    if ($obj_search_txt !== '') {

        $search_txt = $obj_search_txt;

        if ($obj_sel_search === 'shop_title') {
            $DB->where("instr(s.sh_title, '{$search_txt}')");
        } elseif ($obj_sel_search === 'biz_no') {
            $DB->where("instr(s.sh_biz_no, '{$search_txt}')");
        } elseif ($obj_sel_search === 'ceo_name') {
            $DB->where("instr(s.sh_ceo_nm, '{$search_txt}')");
        } else {
            $DB->where("(
                instr(s.sh_title, '{$search_txt}')
                OR instr(s.sh_biz_no, '{$search_txt}')
                OR instr(s.sh_ceo_nm, '{$search_txt}')
            )");
        }
    }

    // 정산 상태 필터 (전체 기간 기준)
    if ($obj_settle_stat === 'unsettled') {
        $DB->where("{$unsettledAllExpr} > 0");
    } elseif ($obj_settle_stat === 'settled') {
        $DB->where("{$unsettledAllExpr} = 0");
    }

    // 정렬
    $orderby        = $_POST['obj_orderby']        ?? '';
    $order_desc_asc = $_POST['obj_order_desc_asc'] ?? '1';

    if ($orderby) {
        $DB->orderBy($orderby, $order_desc_asc === '1' ? 'asc' : 'desc');
    } else {
        $DB->orderBy('settle_priority', 'asc');
        $DB->orderBy('earliest_unsettled_pay_date', 'asc');
        $DB->orderBy('s.idx', 'desc');
    }

    // 조회 필드
    $fields = "
        s.*,
        {$totalPaidExpr}         AS total_paid_count,
        {$unsettledAllExpr}      AS unsettled_all_count,
        {$earliestUnsettledExpr} AS earliest_unsettled_pay_date,
        {$unsettledCurrentExpr}  AS unsettled_current_count,
        CASE
            WHEN {$unsettledAllExpr} > 0 THEN 1
            WHEN {$totalPaidExpr}    > 0 THEN 2
            ELSE 3
        END AS settle_priority
    ";

    $list   = $DB->arraybuilder()->paginate($tbl_shop_name . " s", $pg, $fields);
    $n_page = $DB->totalPages;
    $counts = $DB->totalCount;
    $counts = $counts - (($pg - 1) * $pageLimit);
    ?>
    <div class="table-responsive margin-top-20">
        <table class="table table-striped table-bordered margin-bottom-20"
               id="listTable"
               style="min-width: 900px">
            <thead class="thead-dark">
            <tr>
                <th class="text-center" style="width:70px;">번호</th>
                <th class="text-center" style="width:200px;">매장명</th>
                <th class="text-center" style="width:120px;">지역</th>
                <th class="text-center" style="width:260px;">사업자</th>
                <th class="text-center" style="width:150px;">정산상태</th>
                <th class="text-center" style="width:70px;">관리</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if ($list) {
                foreach ($list as $row) {

                    $region = '';
                    if (!empty($row['sh_addr1'])) {
                        $tmp    = explode(' ', $row['sh_addr1']);
                        $region = $tmp[0];
                    }

                    $corpName = $row['sh_corp_nm'] ?: $row['sh_title'];
                    $bizNo    = $row['sh_biz_no'] ?: '-';
                    $ceoName  = $row['sh_ceo_nm'] ?: '-';

                    $totalPaid         = (int)$row['total_paid_count'];
                    $unsettledAll      = (int)$row['unsettled_all_count'];
                    $earliestUnsettled = $row['earliest_unsettled_pay_date'];

                    if ($unsettledAll > 0 && $earliestUnsettled) {
                        $dateLabel    = substr($earliestUnsettled, 0, 10);
                        $settleStatus = "미정산 ({$dateLabel})";
                        $settleBadge  = 'danger';
                    } elseif ($totalPaid > 0) {
                        $settleStatus = '정산신청완료';
                        $settleBadge  = 'success';
                    } else {
                        $settleStatus = '정산대상없음';
                        $settleBadge  = 'secondary';
                    }
                    ?>
                    <tr data-id="<?=$row['idx']?>">
                        <td class="text-center"><?=$counts?></td>

                        <td class="text-center">
                            <div class="line1_text">
                                <?=htmlspecialchars($row['sh_title'], ENT_QUOTES)?>
                            </div>
                            <?php if (!empty($row['sh_branch_nm'])) { ?>
                                <div class="text-muted small">
                                    (<?=htmlspecialchars($row['sh_branch_nm'], ENT_QUOTES)?>)
                                </div>
                            <?php } ?>
                        </td>

                        <td class="text-center">
                            <span class="line1_text">
                                <?=htmlspecialchars($region, ENT_QUOTES)?>
                            </span>
                        </td>

                        <td class="text-left">
                            <div class="line1_text">
                                <strong><?=htmlspecialchars($corpName, ENT_QUOTES)?></strong>
                            </div>
                            <div class="text-muted small">
                                사업자번호: <?=htmlspecialchars($bizNo, ENT_QUOTES)?>
                            </div>
                            <div class="text-muted small">
                                대표자명: <?=htmlspecialchars($ceoName, ENT_QUOTES)?>
                            </div>
                        </td>

                        <td class="text-center">
                            <span class="badge badge-<?=$settleBadge?>">
                                <?=$settleStatus?>
                            </span>
                        </td>

                        <td class="text-center">
                            <input type="button"
                                   class="btn btn-outline-info"
                                   value="정산등록"
                                   onclick="location.href='./form.php?sh_idx=<?=$row['idx']?>'"/>
                        </td>
                    </tr>
                    <?php
                    $counts--;
                }
            } else { ?>
                <tr>
                    <td colspan="6" class="text-center"><b>자료가 없습니다.</b></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <?php
    if ($n_page > 1) {
        echo page_listing_xhr($pg, $n_page, 'f_get_box_mng_list_shop');
    }
    exit;
}

/* =========================================================
 * ② 정산금액 계산 (월별 리스트 + 합계)
 *    act = calc_settle
 * =======================================================*/
else if ($act === 'calc_settle') {

    $sh_idx    = (int)($_POST['sh_idx'] ?? 0);
    $from_date = trim($_POST['from_date'] ?? '');
    $to_date   = trim($_POST['to_date'] ?? '');

    if ($sh_idx <= 0 || !$from_date || !$to_date) {
        json_response(false, '정산 대상 정보가 올바르지 않습니다.');
        exit;
    }

    try {
        $fromTs = strtotime($from_date);
        $toTs   = strtotime($to_date);

        if ($fromTs === false || $toTs === false || $fromTs > $toTs) {
            throw new Exception('정산기간을 올바르게 선택해주세요.');
        }

        $fromYmd = date('Y-m-d', $fromTs);
        $toYmd   = date('Y-m-d', $toTs);

        list($fromFixed, $toFixed) = normalizeMonthRange($fromYmd, $toYmd);
        if (!$fromFixed || !$toFixed) {
            throw new Exception('정산기간 계산 중 오류가 발생했습니다.');
        }

        $fromYmd = $fromFixed;
        $toYmd   = $toFixed;

        $currentStart = getPeriodStartFromDate($fromYmd);
        if (!$currentStart) {
            throw new Exception('정산기간 계산 중 오류가 발생했습니다. (start)');
        }

        $limitYmd = $toYmd;

        $periods = [];
        $totalAmount = 0;

        while (true) {
            if ($currentStart > $limitYmd) break;

            $currentEnd = getPeriodEndFromStart($currentStart);
            if (!$currentEnd) {
                throw new Exception('정산기간 계산 중 오류가 발생했습니다. (end)');
            }

            if ($currentEnd > $limitYmd) {
                $currentEnd = $limitYmd;
            }

            $startDt = $currentStart . ' 00:00:00';
            $endDt   = $currentEnd . ' 23:59:59';

            // 메인 쿼리 조건
            $DB->join('orders_t o', 'o.idx = p.ot_idx', 'INNER');

            $DB->where('p.sh_idx', $sh_idx);
            $DB->where('p.status', ['PAID', 'PARTIAL_CANCELLED'], 'IN');
            $DB->where('o.ot_settle_yn', 'N');
            $DB->where('p.created_at', [$startDt, $endDt], 'BETWEEN');  // created_at 사용

            // 환불 서브쿼리 직접 문자열
            $refundSubSql = "(SELECT SUM(pr.approved_amount) 
                              FROM payment_refunds_t pr 
                              WHERE pr.pay_idx = p.idx 
                                AND pr.status = 'APPROVED')";

            // 전체 합산 집계 (getOne 사용 OK, SUM이 전체 계산)
            $row = $DB->getOne('payments_t p',
                "COUNT(DISTINCT p.idx) AS order_count,
                 COALESCE(SUM(p.amount_paid - COALESCE($refundSubSql, 0)), 0) AS settle_amount"
            );

            $amount = (float)($row['settle_amount'] ?? 0);
            $orderCount = (int)($row['order_count'] ?? 0);

            if ($amount > 0) {
                $periods[] = [
                    'start_date'    => $currentStart,
                    'end_date'      => $currentEnd,
                    'period_label'  => $currentStart . ' ~ ' . $currentEnd,
                    'order_count'   => $orderCount,
                    'settle_amount' => $amount,
                    'status'        => '미정산',
                ];
                $totalAmount += $amount;
            }

            $nextStart = getNextPeriodStart($currentStart);
            if (!$nextStart || $nextStart > $limitYmd) {
                break;
            }
            $currentStart = $nextStart;
        }

        json_response(true, '정산 대상이 조회되었습니다.', [
            'periods'      => $periods,
            'total_amount' => $totalAmount,
            'normalized'   => [
                'from' => $fromYmd,
                'to'   => $toYmd,
            ],
        ]);

    } catch (Exception $e) {
        json_response(false, $e->getMessage());
    }
}

/* =========================================================
 * ③ 정산 완료 처리
 *    act = settle_complete
 * =======================================================*/
else if ($act === 'settle_complete') {

    $sh_idx      = (int)($_POST['sh_idx'] ?? 0);
    $from_date   = trim($_POST['from_date'] ?? '');
    $to_date     = trim($_POST['to_date'] ?? '');
    $plan_date   = trim($_POST['plan_date'] ?? '');
    $done_date   = trim($_POST['done_date'] ?? '');
    $service_fee = (float)($_POST['service_fee'] ?? 0);
    $final       = (float)($_POST['final_amount'] ?? 0);
    $admin_memo  = trim($_POST['admin_memo'] ?? '');

    if ($sh_idx <= 0 || !$from_date || !$to_date) {
        json_response(false, '정산 정보가 올바르지 않습니다.');
    }

    try {
        $fromTs = strtotime($from_date);
        $toTs   = strtotime($to_date);
        if ($fromTs === false || $toTs === false || $fromTs > $toTs) {
            throw new Exception('정산기간을 올바르게 선택해주세요.');
        }

        $startYmd = date('Y-m-d', $fromTs);
        $endYmd   = date('Y-m-d', $toTs);

        // ✅ [정책변경] 월 단위 정규화 (from=월1일 / to=월말일)
        list($startYmd, $endYmd) = normalizeMonthRange($startYmd, $endYmd);
        if (!$startYmd || !$endYmd) {
            throw new Exception('정산기간 계산 중 오류가 발생했습니다.');
        }

        // ✅ [정책변경/안전장치] 미래 월(말일이 오늘 이후) 정산 완료는 막음 (월 마감 전 정산 방지)
        $todayYmd = date('Y-m-d');
        if ($endYmd > $todayYmd) {
            throw new Exception('정산 종료일(월말)은 오늘 이전이어야 정산 완료 처리할 수 있습니다.');
        }

        // ✅ [정책변경] plan_date 없으면 "정산월 종료일 기준 다음달 10일"
        if (!$plan_date) {
            $plan_date = date('Y-m-10', strtotime('+1 month', strtotime($endYmd)));
        }

        $startDt = $startYmd . ' 00:00:00';
        $endDt   = $endYmd   . ' 23:59:59';

        // 정산 완료일 없으면 오늘
        if (!$done_date) {
            $done_date = date('Y-m-d');
        }
        $settleDoneDt = $done_date . ' 00:00:00';

        $DB->startTransaction();

        // 1) 정산 대상 주문 목록 다시 조회
        $DB->where('sh_idx', $sh_idx);
        $DB->where('ot_pay_status', 'PAID');
        $DB->where('ot_settle_yn', 'N');
        $DB->where('ot_pay_date', [$startDt, $endDt], 'BETWEEN');

        $orders = $DB->get($tbl_orders_name, null, 'idx, ot_total_price, ot_discount_amount');

        if (!$orders) {
            throw new Exception('정산 대상 주문이 존재하지 않습니다.');
        }

        $orderCount  = count($orders);
        $totalAmount = 0;

        foreach ($orders as $o) {
            $price    = (float)($o['ot_total_price'] ?? 0);
            $discount = (float)($o['ot_discount_amount'] ?? 0);
            $totalAmount += max(0, $price - $discount);
        }

        $total = $totalAmount;

        // 2) 정산번호 생성 (예: ST20251202-0001)
        $prefix = 'ST' . date('Ymd');
        $DB->where('st_number', $prefix . '%', 'LIKE');
        $maxRow = $DB->getOne($tbl_settle_name, 'MAX(st_number) AS max_no');

        $lastSeq = 0;
        if ($maxRow && !empty($maxRow['max_no'])) {
            $lastSeq = (int)substr($maxRow['max_no'], strlen($prefix) + 1);
        }
        $nextSeq   = $lastSeq + 1;
        $st_number = $prefix . '-' . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);

        // 3) settle_t INSERT
        $settleData = [
            'st_number'       => $st_number,
            'sh_idx'          => $sh_idx,
            'st_start_date'   => $startYmd,
            'st_end_date'     => $endYmd,
            'st_order_count'  => $orderCount,
            'st_total_amount' => $total,
            'st_service_fee'  => $service_fee,
            'st_final_amount' => $final,
            'st_plan_date'    => $plan_date ?: null,
            'st_done_date'    => $settleDoneDt,
            'st_status'       => 'PLANNED',
            'st_admin_memo'   => $admin_memo,
        ];

        $st_idx = $DB->insert($tbl_settle_name, $settleData);
        if (!$st_idx) {
            throw new Exception('정산 내역 저장에 실패했습니다.');
        }

        // 4) orders_t 업데이트 (해당 기간 + 미정산 → 정산 완료 + st_idx 세팅)
        $orderIds = array_column($orders, 'idx');

        $DB->where('idx', $orderIds, 'IN');
        $updateData = [
            'ot_settle_yn'   => 'Y',
            'ot_settle_date' => $settleDoneDt,
            'st_idx'         => $st_idx,
        ];

        $res = $DB->update($tbl_orders_name, $updateData);
        if ($res === false) {
            throw new Exception('정산 대상 주문 업데이트에 실패했습니다.');
        }

        $DB->commit();

        json_response(true, '정산 처리가 완료되었습니다.', [
            'settle_number' => $st_number,
            'settle_idx'    => $st_idx,
            'order_count'   => $orderCount,
            'total_amount'  => $total,
            'service_fee'   => $service_fee,
            'final_amount'  => $final,
            // ✅ [정책변경] 확정된 기간 반환
            'period'        => [
                'start' => $startYmd,
                'end'   => $endYmd,
                'plan'  => $plan_date,
            ],
        ]);

    } catch (Exception $e) {
        $DB->rollback();
        json_response(false, $e->getMessage());
    }
}

/* =========================================================
 * ④ 정산 리스트 조회
 *    act = settle_list
 * =======================================================*/
else if ($act === 'settle_list') {

    $pageLimit     = (int)($_POST['obj_limit_num'] ?? 10);
    $DB->pageLimit = $pageLimit;
    $pg            = (int)($_POST['obj_pg'] ?? 1);

    $search_day    = $_POST['obj_search_day']   ?? '';
    $sdate         = trim($_POST['sdate']       ?? '');
    $edate         = trim($_POST['edate']       ?? '');
    $statusKey     = $_POST['obj_settle_status'] ?? 'all';
    $sel_search    = $_POST['obj_sel_search']  ?? 'all';
    $search_txt    = trim($_POST['obj_search_txt'] ?? '');

    $DB->join($tbl_shop_name . " s", "s.idx = st.sh_idx", "LEFT");

    // ✅ 정산 기준 날짜: 완료일이 있으면 완료일, 없으면 예정일 기준
    $dateExpr = "COALESCE(st.st_done_date, st.st_plan_date)";

    if ($search_day) {
        $today = date('Y-m-d');

        if ($search_day === '1') {
            $start = $today . ' 00:00:00';
            $end   = $today . ' 23:59:59';
        } elseif ($search_day === '2') {
            $start = date('Y-m-d', strtotime('-2 days')) . ' 00:00:00';
            $end   = $today . ' 23:59:59';
        } elseif ($search_day === '3') {
            $start = date('Y-m-d', strtotime('-6 days')) . ' 00:00:00';
            $end   = $today . ' 23:59:59';
        } elseif ($search_day === '4') {
            $start = date('Y-m-d', strtotime('-29 days')) . ' 00:00:00';
            $end   = $today . ' 23:59:59';
        }

        if (isset($start, $end)) {
            $DB->where($dateExpr, [$start, $end], 'BETWEEN');
        }
    }

    // 직접 기간 선택 (우선순위: 직접 선택 > 버튼)
    if ($sdate && $edate) {
        $start = $sdate . ' 00:00:00';
        $end   = $edate . ' 23:59:59';
        $DB->where($dateExpr, [$start, $end], 'BETWEEN');
    }

    if ($statusKey !== '' && $statusKey !== 'all') {
        if ($statusKey === 'planned') {
            $DB->where('st.st_status', 'PLANNED');
        } elseif ($statusKey === 'done') {
            $DB->where('st.st_status', 'DONE');
        }
    }

    if ($search_txt !== '') {
        if ($sel_search === 'shop_title') {
            $DB->where("instr(s.sh_title, '{$search_txt}')");
        } elseif ($sel_search === 'settle_number') {
            $DB->where("instr(st.st_number, '{$search_txt}')");
        } else {
            $DB->where("(
                instr(s.sh_title, '{$search_txt}')
                OR instr(st.st_number, '{$search_txt}')
            )");
        }
    }

    $DB->orderBy('st.idx', 'desc');

    $fields = "
        st.*,
        s.sh_title
    ";

    $list   = $DB->arraybuilder()->paginate($tbl_settle_name . " st", $pg, $fields);
    $n_page = $DB->totalPages;
    $counts = $DB->totalCount;
    $counts = $counts - (($pg - 1) * $pageLimit);
    ?>
    <div class="table-responsive margin-top-20">
        <table class="table table-striped table-bordered margin-bottom-20"
               id="settleListTable"
               style="min-width: 900px">
            <thead class="thead-dark">
            <tr>
                <th class="text-center" style="width:70px;">번호</th>
                <th class="text-center" style="width:150px;">정산예정금액</th>
                <th class="text-center" style="width:120px;">서비스 수수료</th>
                <th class="text-center" style="width:220px;">정산 기간</th>
                <th class="text-center" style="width:130px;">정산 예정일</th>
                <th class="text-center" style="width:110px;">정산 상태</th>
                <th class="text-center" style="width:70px;">관리</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if ($list) {
                foreach ($list as $row) {

                    $finalAmount   = number_format((float)$row['st_final_amount']);
                    $serviceFee    = number_format((float)$row['st_service_fee']);

                    $periodLabel = '';
                    if (!empty($row['st_start_date']) && !empty($row['st_end_date'])) {
                        $periodLabel =
                            DateType($row['st_start_date'], 4)
                            . ' ~ ' .
                            DateType($row['st_end_date'], 4);
                    }

                    $planDateLabel = '-';
                    if (!empty($row['st_plan_date'])) {
                        $planDateLabel = DateType($row['st_plan_date'], 4);
                    }

                    $statusText  = '정산예정';
                    $statusBadge = 'warning';

                    if ($row['st_status'] === 'DONE') {
                        $statusText  = '정산완료';
                        $statusBadge = 'success';
                    } elseif ($row['st_status'] === 'CANCELLED') {
                        $statusText  = '정산취소';
                        $statusBadge = 'secondary';
                    }
                    ?>
                    <tr data-id="<?=$row['idx']?>">
                        <td class="text-center"><?=$counts?></td>

                        <td class="text-center">
                            <span class="line1_text"><?=$finalAmount?></span>
                        </td>

                        <td class="text-center">
                            <span class="line1_text"><?=$serviceFee?></span>
                        </td>

                        <td class="text-center">
                            <span class="line1_text"><?=$periodLabel?></span>
                        </td>

                        <td class="text-center">
                            <span class="line1_text"><?=$planDateLabel?></span>
                        </td>

                        <td class="text-center">
                            <span class="badge badge-<?=$statusBadge?>">
                                <?=$statusText?>
                            </span>
                        </td>

                        <td class="text-center">
                            <input type="button"
                                   class="btn btn-outline-info"
                                   value="상세"
                                   onclick="location.href='./detail.php?st_idx=<?=$row['idx']?>'"/>
                        </td>
                    </tr>
                    <?php
                    $counts--;
                }
            } else { ?>
                <tr>
                    <td colspan="7" class="text-center"><b>자료가 없습니다.</b></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <?php
    if ($n_page > 1) {
        echo page_listing_xhr($pg, $n_page, 'f_get_box_mng_list_settle');
    }

    exit;
}

/* =========================================================
 * ⑤ 정산 완료 처리 (상세 페이지에서 호출)
 *    act = settle_done
 * =======================================================*/
else if ($act === 'settle_done') {

    $st_idx    = (int)($_POST['st_idx'] ?? 0);
    $done_date = trim($_POST['done_date'] ?? '');

    if ($st_idx <= 0) {
        json_response(false, '정산 정보를 찾을 수 없습니다.');
    }

    try {
        if (!$done_date) {
            $done_date = date('Y-m-d');
        }
        $settleDoneDt = $done_date . ' 00:00:00';

        $DB->startTransaction();

        $DB->where('idx', $st_idx);
        $settle = $DB->getOne($tbl_settle_name);
        if (!$settle) {
            throw new Exception('정산 내역을 찾을 수 없습니다.');
        }

        if ($settle['st_status'] === 'DONE') {
            $DB->commit();
            json_response(true, '이미 정산 완료된 내역입니다.');
        }

        $DB->where('idx', $st_idx);
        $res = $DB->update($tbl_settle_name, [
            'st_status'    => 'DONE',
            'st_done_date' => $settleDoneDt,
        ]);

        if ($res === false) {
            throw new Exception('정산 상태 변경에 실패했습니다.');
        }

        $DB->commit();

        json_response(true, '정산이 완료 처리되었습니다.');

    } catch (Exception $e) {
        $DB->rollback();
        json_response(false, $e->getMessage());
    }
}

/* =========================================================
 * ⑥ 정산 최종 완료 처리 (상세 페이지에서 호출)
 *    act = settle_finalize
 * =======================================================*/
else if ($act === 'settle_finalize') {

    $st_idx = (int)($_POST['st_idx'] ?? 0);
    if ($st_idx <= 0) {
        json_response(false, '정산 정보가 올바르지 않습니다.');
    }

    try {
        $DB->where('idx', $st_idx);
        $settle = $DB->getOne($tbl_settle_name);
        if (!$settle) {
            throw new Exception('정산 내역을 찾을 수 없습니다.');
        }

        if ($settle['st_status'] === 'DONE') {
            throw new Exception('이미 정산 완료된 내역입니다.');
        }

        $sh_idx    = (int)$settle['sh_idx'];
        $startYmd  = $settle['st_start_date'];
        $endYmd    = $settle['st_end_date'];

        if (!$sh_idx || !$startYmd || !$endYmd) {
            throw new Exception('정산 기간 정보가 올바르지 않습니다.');
        }

        $startDt = $startYmd . ' 00:00:00';
        $endDt   = $endYmd   . ' 23:59:59';

        $DB->startTransaction();

        $DB->where('sh_idx', $sh_idx);
        $DB->where('ot_pay_status', 'PAID');
        $DB->where('ot_settle_yn', 'N');
        $DB->where('ot_pay_date', [$startDt, $endDt], 'BETWEEN');

        $updateOrderData = [
            'ot_settle_yn'   => 'Y',
            'ot_settle_date' => $DB->now(),
            'st_idx'         => $st_idx,
        ];

        $res = $DB->update($tbl_orders_name, $updateOrderData);
        if ($res === false) {
            throw new Exception('정산 대상 주문 업데이트에 실패했습니다.');
        }

        $DB->where('idx', $st_idx);
        $updateSettleData = [
            'st_status'    => 'DONE',
            'st_done_date' => $DB->now(),
        ];

        if (!$DB->update($tbl_settle_name, $updateSettleData)) {
            throw new Exception('정산 상태 변경에 실패했습니다.');
        }

        $DB->commit();

        json_response(true, '정산이 완료되었습니다.');

    } catch (Exception $e) {
        $DB->rollback();
        json_response(false, $e->getMessage());
    }
}

include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
?>
