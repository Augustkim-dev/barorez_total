<?php
// /mng/settle/settle_form.php

include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu     = 6;
$chk_sub_menu = 1;
include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.menu.inc.php";

$tbl_shop_name   = $CFG_TBL['shop']['default'];    // shop_t
$tbl_orders_name = $CFG_TBL['orders']['default'];  // orders_t

$sh_idx = (int)($_GET['sh_idx'] ?? 0);
if ($sh_idx <= 0) {
    echo "<script>alert('잘못된 접근입니다. (매장 정보)'); history.back();</script>";
    exit;
}

// 매장 정보
$DB->where('idx', $sh_idx);
$shop = $DB->getOne($tbl_shop_name);
if (!$shop) {
    echo "<script>alert('매장 정보를 찾을 수 없습니다.'); history.back();</script>";
    exit;
}

/**
 * ✅ [정책변경]
 * 기존: 16일~다음달 15일
 * 변경: "월 정산" → 매월 1일 ~ 말일
 *
 * 기본값은 "지난달 1일 ~ 지난달 말일"로 잡음
 * (월 마감 정산 특성상, 기본은 가장 최근 마감월이 자연스러움)
 *
 * return ['start' => 'Y-m-d', 'end' => 'Y-m-d']
 */
function getBaseSettlePeriod()
{
    $startDate = date('Y-m-01', strtotime('-1 month')); // 지난달 1일
    $endDate   = date('Y-m-t',  strtotime('-1 month')); // 지난달 말일

    return [
        'start' => $startDate,
        'end'   => $endDate,
    ];
}

/**
 * 해당 매장의 가장 오래된 미정산 결제일자(ot_pay_date) 조회
 * 없으면 null
 */
function getFirstUnsettledPayDate($sh_idx, $tbl_orders_name, $DB)
{
    $DB->where('sh_idx', $sh_idx);
    $DB->where('ot_pay_status', 'PAID');
    $DB->where('ot_settle_yn', 'N');
    $DB->where('ot_pay_date', null, 'IS NOT');
    $DB->orderBy('ot_pay_date', 'asc');
    $row = $DB->getOne($tbl_orders_name, 'ot_pay_date');

    if ($row && !empty($row['ot_pay_date'])) {
        return substr($row['ot_pay_date'], 0, 10); // Y-m-d만 사용
    }
    return null;
}

// ✅ [정책변경] 기본 정산기간: 지난달 1~말일
$basePeriod   = getBaseSettlePeriod();
$default_from = $basePeriod['start'];
$default_to   = $basePeriod['end'];

// ✅ [정책변경] 이전에 미정산 데이터가 있다면, "그 날짜가 속한 월의 1일"로 시작점 보정
$firstUnsettled = getFirstUnsettledPayDate($sh_idx, $tbl_orders_name, $DB);
if ($firstUnsettled) {
    $firstUnsettledMonthStart = date('Y-m-01', strtotime($firstUnsettled));
    if ($firstUnsettledMonthStart < $default_from) {
        $default_from = $firstUnsettledMonthStart;
        $default_to   = date('Y-m-t', strtotime($default_from)); // 해당 월 말일로 종료일도 맞춤
    }
}

// ✅ [정책변경] 정산예정일 기본값: "정산월 종료일 기준 다음달 10일"로 세팅하고 싶지만
// PHP에서는 기본값만, 최종은 JS에서 자동 갱신 (to_date 변경 시에도 갱신)
$default_plan_date = date('Y-m-10', strtotime('+1 month', strtotime($default_to)));
?>
    <div class="content" id="content">
        <?php include_once "./pheading.php";?>

        <div class="container-fluid">
            <div class="card margin-bottom-0">
                <div class="card-body">

                    <!-- 상단 매장 정보 -->
                    <div class="mb-4 p-3 border rounded bg-light">
                        <h5 class="mb-2">매장 정보</h5>
                        <div class="mb-1">
                            <strong><?=htmlspecialchars($shop['sh_title'], ENT_QUOTES)?></strong>
                            <?php if (!empty($shop['sh_branch_nm'])) { ?>
                                <span class="text-muted"> (<?=htmlspecialchars($shop['sh_branch_nm'], ENT_QUOTES)?>)</span>
                            <?php } ?>
                        </div>
                        <div class="mb-1">
                            주소 :
                            <?=htmlspecialchars(trim(($shop['sh_addr1'] ?? '').' '.($shop['sh_addr2'] ?? '')), ENT_QUOTES)?>
                        </div>
                        <div class="mb-1">
                            사업자 :
                            <?=htmlspecialchars($shop['sh_corp_nm'] ?: $shop['sh_title'], ENT_QUOTES)?>
                            /
                            <?=htmlspecialchars($shop['sh_biz_no'] ?: '-', ENT_QUOTES)?>
                            /
                            대표자명 : <?=htmlspecialchars($shop['sh_ceo_nm'] ?: '-', ENT_QUOTES)?>
                        </div>
                    </div>

                    <form id="frm_settle" name="frm_settle" onsubmit="return false;">
                        <input type="hidden" name="sh_idx" id="sh_idx" value="<?=$sh_idx?>">

                        <!-- 정산기간 + 정산금액 불러오기 -->
                        <div class="form-row align-items-center mb-3">
                            <label class="col-form-label mr-2" style="min-width:80px;">정산기간</label>
                            <input type="date" class="form-control col-sm-3"
                                   name="settle_from" id="settle_from"
                                   value="<?=$default_from?>">
                            <label class="col-form-label text-center mx-2" style="width:30px;">~</label>
                            <input type="date" class="form-control col-sm-3"
                                   name="settle_to" id="settle_to"
                                   value="<?=$default_to?>">
                            <button type="button"
                                    class="btn btn-outline-primary ml-3"
                                    id="btn_load_settle">
                                정산금액 불러오기
                            </button>
                        </div>

                        <!-- 정산 예정일 / 완료일 -->
                        <div class="form-row mb-3">
                            <div class="col-md-3">
                                <label for="settle_plan_date">정산 예정일</label>
                                <input type="date"
                                       class="form-control"
                                       name="settle_plan_date"
                                       id="settle_plan_date"
                                       value="<?=$default_plan_date?>">
                            </div>
                            <div class="col-md-3">
                                <label for="settle_done_date">정산 완료일</label>
                                <input type="date"
                                       class="form-control"
                                       name="settle_done_date"
                                       id="settle_done_date"
                                       value="<?=date('Y-m-d')?>">
                            </div>
                        </div>

                        <!-- 월별 정산 리스트 -->
                        <div id="settle_period_list" class="mb-4">
                            <!-- 정산금액 불러오기 클릭 시 Ajax로 테이블 렌더링 -->
                        </div>

                        <!-- 금액 요약 -->
                        <div class="form-row mb-4">
                            <div class="col-md-3">
                                <label for="total_settle_amount">총 정산 금액</label>
                                <input type="text" class="form-control"
                                       id="total_settle_amount"
                                       name="total_settle_amount"
                                       value="0"
                                       readonly>
                            </div>
                            <div class="col-md-3">
                                <label for="service_fee">서비스 수수료 (기본 3%)</label>
                                <input type="text" class="form-control"
                                       id="service_fee"
                                       name="service_fee"
                                       value="0">
                            </div>
                            <div class="col-md-3">
                                <label for="final_settle_amount">정산 예정 금액</label>
                                <input type="text" class="form-control"
                                       id="final_settle_amount"
                                       name="final_settle_amount"
                                       value="0"
                                       readonly>
                            </div>
                        </div>

                        <!-- 정산 계좌 -->
                        <div class="form-row mb-3">
                            <div class="col-md-6">
                                <label>정산 계좌</label>
                                <div class="border rounded p-2 bg-light">
                                    <div>예금주 : <?=htmlspecialchars($shop['sh_bank_holder'] ?: '-', ENT_QUOTES)?></div>
                                    <div>은행명 : <?=htmlspecialchars($shop['sh_bank'] ?: '-', ENT_QUOTES)?></div>
                                    <div>계좌번호 : <?=htmlspecialchars($shop['sh_bank_account'] ?: '-', ENT_QUOTES)?></div>
                                </div>
                            </div>
                        </div>

                        <!-- 관리자 메모 -->
                        <div class="form-group mb-4">
                            <label for="admin_memo">관리자 메모</label>
                            <textarea class="form-control" name="admin_memo" id="admin_memo" rows="3"></textarea>
                        </div>

                        <!-- 버튼 -->
                        <div class="text-right">
                            <a href="./list.php" class="btn btn-gray">목록</a>
                            <button type="button" class="btn btn-primary" id="btn_settle_done">정산 완료</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script>
        // 숫자 포맷/파싱 유틸
        function parseNumber(val) {
            if (!val) return 0;
            return parseFloat(String(val).replace(/,/g, '')) || 0;
        }
        function formatNumber(num) {
            num = parseFloat(num) || 0;
            return num.toLocaleString('ko-KR', { maximumFractionDigits: 0 });
        }

        // ✅ [정책변경] 해당 날짜(YYYY-MM-DD)가 속한 월의 "1일" 반환
        function getMonthStart(ymd) {
            if (!ymd) return '';
            const p = ymd.split('-');
            if (p.length !== 3) return '';
            return `${p[0]}-${p[1]}-01`;
        }

        // ✅ [정책변경] 해당 날짜(YYYY-MM-DD)가 속한 월의 "말일" 반환
        function getMonthEnd(ymd) {
            if (!ymd) return '';
            const p = ymd.split('-');
            if (p.length !== 3) return '';
            const y = parseInt(p[0], 10);
            const m = parseInt(p[1], 10); // 1~12
            const lastDay = new Date(y, m, 0).getDate(); // 다음달 0일 = 이번달 말일
            return `${p[0]}-${p[1]}-${String(lastDay).padStart(2, '0')}`;
        }

        // ✅ [정책변경] 정산 예정일: 정산 종료일(to) 기준 "다음달 10일"
        function getPlanDateByToDate(toYmd) {
            if (!toYmd) return '';

            const parts = toYmd.split('-');
            if (parts.length !== 3) return '';

            const y = parseInt(parts[0], 10);
            const m = parseInt(parts[1], 10) - 1; // JS month 0-based
            const d = parseInt(parts[2], 10);

            const base = new Date(y, m, d);
            if (isNaN(base.getTime())) return '';

            // 다음달 10일
            const next = new Date(base.getFullYear(), base.getMonth() + 1, 10);

            const yy = next.getFullYear();
            const mm = String(next.getMonth() + 1).padStart(2, '0');
            const dd = String(next.getDate()).padStart(2, '0');

            return `${yy}-${mm}-${dd}`;
        }

        // ✅ [정책변경] 정산기간은 "월 전체(1일~말일)"로 자동 보정
        function normalizeSettleRangeByFrom(fromYmd) {
            const fixedFrom = getMonthStart(fromYmd);
            const fixedTo   = getMonthEnd(fromYmd);
            console.log('[settle_form] normalize by from:', { fromYmd, fixedFrom, fixedTo }); // 로그
            if (fixedFrom) $('#settle_from').val(fixedFrom);
            if (fixedTo)   $('#settle_to').val(fixedTo);

            // 예정일도 종료일 기준으로 자동 세팅
            const plan = getPlanDateByToDate(fixedTo);
            console.log('[settle_form] plan date updated by from:', { fixedTo, plan }); // 로그
            if (plan) $('#settle_plan_date').val(plan);
        }

        function normalizeSettleRangeByTo(toYmd) {
            const fixedFrom = getMonthStart(toYmd);
            const fixedTo   = getMonthEnd(toYmd);
            console.log('[settle_form] normalize by to:', { toYmd, fixedFrom, fixedTo }); // 로그
            if (fixedFrom) $('#settle_from').val(fixedFrom);
            if (fixedTo)   $('#settle_to').val(fixedTo);

            // 예정일도 종료일 기준으로 자동 세팅
            const plan = getPlanDateByToDate(fixedTo);
            console.log('[settle_form] plan date updated by to:', { fixedTo, plan }); // 로그
            if (plan) $('#settle_plan_date').val(plan);
        }

        // ✅ [정책변경] 최초 로드시에도 월 전체로 정규화(혹시 값이 중간날짜로 들어왔을 경우 대비)
        (function initNormalizeOnLoad() {
            const from = $('#settle_from').val();
            if (from) normalizeSettleRangeByFrom(from);
        })();

        // ✅ [정책변경] from 변경 시: 해당 월 전체(1~말일)로 보정
        $('#settle_from').on('change', function () {
            const from = $(this).val();
            normalizeSettleRangeByFrom(from);
        });

        // ✅ [정책변경] to 변경 시: 해당 월 전체(1~말일)로 보정
        $('#settle_to').on('change', function () {
            const to = $(this).val();
            normalizeSettleRangeByTo(to);
        });

        // 정산금액 불러오기
        $('#btn_load_settle').on('click', function () {
            const from = $('#settle_from').val();
            const to   = $('#settle_to').val();

            if (!from || !to) {
                alert('정산기간을 선택해주세요.');
                return;
            }

            console.log('[settle_form] calc_settle request:', { // ✅ 로그
                act: 'calc_settle',
                sh_idx: $('#sh_idx').val(),
                from_date: from,
                to_date: to
            });

            $.ajax({
                url: './update.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    act      : 'calc_settle',
                    sh_idx   : $('#sh_idx').val(),
                    from_date: from,
                    to_date  : to
                },
                success: function (res) {

                    console.log('[settle_form] calc_settle response:', res); // ✅ 로그

                    if (!res.success) {
                        alert(res.message || '정산 금액 조회에 실패했습니다.');
                        return;
                    }

                    // 월별 리스트 렌더링
                    let html = '<div class="table-responsive">';
                    html += '<table class="table table-striped table-bordered margin-bottom-20">';
                    html += '<thead class="thead-dark">';
                    html += '<tr>';
                    html += '<th class="text-center">정산기간</th>';
                    html += '<th class="text-center">정산 상태</th>';
                    html += '<th class="text-center">정산 금액</th>';
                    html += '</tr></thead><tbody>';

                    if (res.periods && res.periods.length > 0) {
                        res.periods.forEach(function (p) {
                            html += '<tr>';
                            html += '<td class="text-center">' + p.period_label + '</td>';
                            html += '<td class="text-center">' + p.status + '</td>';
                            html += '<td class="text-right">' + formatNumber(p.settle_amount) + ' 원</td>';
                            html += '</tr>';
                        });
                    } else {
                        html += '<tr><td colspan="3" class="text-center"><b>정산 대상 주문이 없습니다.</b></td></tr>';
                    }

                    html += '</tbody></table></div>';
                    $('#settle_period_list').html(html);

                    // 합계/수수료/정산예정금액 세팅
                    const total = parseNumber(res.total_amount || 0);
                    $('#total_settle_amount').val(formatNumber(total));

                    let fee = Math.floor(total * 0.03); // 기본 3%
                    $('#service_fee').val(formatNumber(fee));
                    $('#final_settle_amount').val(formatNumber(total - fee));
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                    alert('서버 통신 중 오류가 발생했습니다.');
                }
            });
        });

        // 서비스 수수료 수정 시 정산예정금액 재계산
        $('#service_fee').on('input', function () {
            const total = parseNumber($('#total_settle_amount').val());
            const fee   = parseNumber($(this).val());
            $('#final_settle_amount').val(formatNumber(total - fee));
        });

        // 정산 완료 처리
        $('#btn_settle_done').on('click', function () {
            const from      = $('#settle_from').val();
            const to        = $('#settle_to').val();
            const planDate  = $('#settle_plan_date').val();
            const doneDate  = $('#settle_done_date').val();
            const total     = parseNumber($('#total_settle_amount').val());
            const fee       = parseNumber($('#service_fee').val());
            const finalAmt  = parseNumber($('#final_settle_amount').val());
            const memo      = $('#admin_memo').val();

            if (!from || !to) {
                alert('정산기간을 선택해주세요.');
                return;
            }
            if (!total || total <= 0) {
                alert('정산 금액이 0원입니다. 먼저 [정산금액 불러오기]를 실행해주세요.');
                return;
            }
            if (!confirm('정말로 정산을 완료 처리하시겠습니까?\n해당 기간의 주문들은 정산완료로 표시됩니다.')) {
                return;
            }

            console.log('[settle_form] settle_complete request:', { // ✅ 로그
                act: 'settle_complete',
                sh_idx: $('#sh_idx').val(),
                from_date: from,
                to_date: to,
                plan_date: planDate,
                done_date: doneDate,
                total_amount: total,
                service_fee: fee,
                final_amount: finalAmt
            });

            $.ajax({
                url: './update.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    act          : 'settle_complete',
                    sh_idx       : $('#sh_idx').val(),
                    from_date    : from,
                    to_date      : to,
                    plan_date    : planDate,
                    done_date    : doneDate,
                    total_amount : total,
                    service_fee  : fee,
                    final_amount : finalAmt,
                    admin_memo   : memo
                },
                success: function (res) {

                    console.log('[settle_form] settle_complete response:', res); // ✅ 로그

                    if (res.success) {
                        alert(res.message || '정산이 완료되었습니다.');
                        location.href = './list.php';
                    } else {
                        alert(res.message || '정산 처리 중 오류가 발생했습니다.');
                    }
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                    alert('서버 통신 중 오류가 발생했습니다.');
                }
            });
        });
    </script>

<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/foot.inc.php";
?>
