<?php
// /mng/settle/settle_detail.php

include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu     = 6;
$chk_sub_menu = 1;
include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.menu.inc.php";

$tbl_shop_name   = $CFG_TBL['shop']['default'];   // shop_t
$tbl_settle_name = $CFG_TBL['settle']['default']; // settle_t

$st_idx = (int)($_GET['st_idx'] ?? 0);

if ($st_idx <= 0) {
    echo "<script>alert('잘못된 접근입니다.'); history.back();</script>";
    exit;
}

// 정산 + 매장 정보 가져오기
$DB->join($tbl_shop_name . " s", "s.idx = st.sh_idx", "LEFT");
$DB->where('st.idx', $st_idx);
$row = $DB->getOne($tbl_settle_name . " st", "
    st.*,
    s.sh_title,
    s.sh_branch_nm,
    s.sh_addr1,
    s.sh_addr2,
    s.sh_biz_no,
    s.sh_ceo_nm,
    s.sh_bank,
    s.sh_bank_holder,
    s.sh_bank_account
");

if (!$row) {
    echo "<script>alert('정산 내역을 찾을 수 없습니다.'); history.back();</script>";
    exit;
}

// 상태 라벨/뱃지
$settle_status = $row['st_status'] ?? 'PLANNED';

if ($settle_status === 'DONE') {
    $status_label = '정산완료';
    $status_badge = 'success';
} else {
    $status_label = '정산예정';
    $status_badge = 'warning';
}

$shop_name = $row['sh_title'];
if (!empty($row['sh_branch_nm'])) {
    $shop_name .= ' (' . $row['sh_branch_nm'] . ')';
}

// 정산 기간
$period_label = $row['st_start_date'] . ' ~ ' . $row['st_end_date'];

// 금액 포맷
function nf($v) {
    return number_format((float)$v);
}

?>
    <!-- PAGE CONTENT CONTAINER -->
    <div class="content" id="content">
        <!-- PAGE HEADING -->
        <?php include_once "./pheading.php";?>
        <!-- //END PAGE HEADING -->

        <div class="container-fluid">
            <div class="card margin-bottom-0">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">정산 상세</h5>
                        <div>
                            <a href="./list.php?tab=settle" class="btn btn-sm btn-gray">
                                목록으로
                            </a>
                            <?php if ($settle_status !== 'DONE') { ?>
                                <button type="button"
                                        class="btn btn-sm btn-primary"
                                        id="btnSettleComplete"
                                        data-st-idx="<?=$st_idx?>">
                                    정산 완료 처리
                                </button>
                            <?php } ?>
                        </div>
                    </div>

                    <!-- 정산 기본 정보 -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <strong>정산 기본 정보</strong>
                        </div>
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-md-4">
                                    <label class="font-weight-bold">정산번호</label>
                                    <div><?=htmlspecialchars($row['st_number'], ENT_QUOTES)?></div>
                                </div>
                                <div class="col-md-4">
                                    <label class="font-weight-bold">정산 상태</label>
                                    <div>
                                    <span class="badge badge-<?=$status_badge?>">
                                        <?=$status_label?>
                                    </span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="font-weight-bold">정산 기간</label>
                                    <div><?=$period_label?></div>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-md-4">
                                    <label class="font-weight-bold">정산 예정일</label>
                                    <div>
                                        <?= $row['st_plan_date'] ? htmlspecialchars($row['st_plan_date'], ENT_QUOTES) : '-' ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="font-weight-bold">정산 완료일</label>
                                    <div>
                                        <?= $row['st_done_date'] ? htmlspecialchars($row['st_done_date'], ENT_QUOTES) : '-' ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="font-weight-bold">등록일 / 수정일</label>
                                    <div>
                                        <?=htmlspecialchars($row['st_wdate'], ENT_QUOTES)?>
                                        <br/>
                                        <span class="text-muted small">
                                        (수정: <?=htmlspecialchars($row['st_udate'], ENT_QUOTES)?>)
                                    </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 매장 정보 -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <strong>매장 정보</strong>
                        </div>
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <label class="font-weight-bold">매장명</label>
                                    <div><?=htmlspecialchars($shop_name, ENT_QUOTES)?></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="font-weight-bold">대표자명</label>
                                    <div>
                                        <?= $row['sh_ceo_nm']
                                            ? htmlspecialchars($row['sh_ceo_nm'], ENT_QUOTES)
                                            : '-' ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <label class="font-weight-bold">사업자번호</label>
                                    <div>
                                        <?= $row['sh_biz_no']
                                            ? htmlspecialchars($row['sh_biz_no'], ENT_QUOTES)
                                            : '-' ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="font-weight-bold">주소</label>
                                    <div>
                                        <?= htmlspecialchars(trim(($row['sh_addr1'] ?? '') . ' ' . ($row['sh_addr2'] ?? '')), ENT_QUOTES) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 정산 금액 정보 -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <strong>정산 금액</strong>
                        </div>
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-md-4">
                                    <label class="font-weight-bold">주문 건수</label>
                                    <div><?= (int)$row['st_order_count'] ?> 건</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="font-weight-bold">총 정산 금액</label>
                                    <div><?= nf($row['st_total_amount']) ?> 원</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="font-weight-bold">서비스 수수료</label>
                                    <div><?= nf($row['st_service_fee']) ?> 원</div>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-md-4">
                                    <label class="font-weight-bold">최종 정산 금액</label>
                                    <div class="text-primary font-weight-bold">
                                        <?= nf($row['st_final_amount']) ?> 원
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 정산 계좌 정보 -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <strong>정산 계좌</strong>
                        </div>
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-md-4">
                                    <label class="font-weight-bold">예금주</label>
                                    <div>
                                        <?= $row['sh_bank_holder']
                                            ? htmlspecialchars($row['sh_bank_holder'], ENT_QUOTES)
                                            : '-' ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="font-weight-bold">은행명</label>
                                    <div>
                                        <?= $row['sh_bank']
                                            ? htmlspecialchars($row['sh_bank'], ENT_QUOTES)
                                            : '-' ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="font-weight-bold">계좌번호</label>
                                    <div>
                                        <?= $row['sh_bank_account']
                                            ? htmlspecialchars($row['sh_bank_account'], ENT_QUOTES)
                                            : '-' ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 관리자 메모 -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <strong>관리자 메모</strong>
                        </div>
                        <div class="card-body">
                            <textarea class="form-control" rows="4" readonly><?=htmlspecialchars($row['st_admin_memo'], ENT_QUOTES)?></textarea>
                        </div>
                    </div>

                    <div class="text-right">
                        <a href="./list.php?tab=settle" class="btn btn-gray">목록으로</a>
                        <?php if ($settle_status !== 'DONE') { ?>
                            <button type="button"
                                    class="btn btn-primary"
                                    id="btnSettleCompleteBottom"
                                    data-st-idx="<?=$st_idx?>">
                                정산 완료 처리
                            </button>
                        <?php } ?>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        function callSettleComplete(stIdx) {
            if (!confirm('해당 정산을 완료 처리하시겠습니까?\n처리 후에는 되돌릴 수 없습니다.')) {
                return;
            }

            $.ajax({
                url: './update.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    act: 'settle_finalize',
                    st_idx: stIdx
                },
                success: function (res) {
                    if (res.success) {
                        alert(res.message || '정산이 완료되었습니다.');
                        location.reload();
                    } else {
                        alert(res.message || '정산 완료 처리 중 오류가 발생했습니다.');
                    }
                },
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                    alert('서버 통신 오류가 발생했습니다.');
                }
            });
        }

        $(document).ready(function () {
            $('#btnSettleComplete, #btnSettleCompleteBottom').on('click', function () {
                const stIdx = $(this).data('st-idx');
                callSettleComplete(stIdx);
            });
        });
    </script>

<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/foot.inc.php";
