<?php
// settle_form.php : 정산 관리 상세 (가맹점주 조회용)

include $_SERVER['DOCUMENT_ROOT']."/market/head.inc.php";
$chk_menu     = 6;  // 정산메뉴 번호에 맞게 조정
$chk_sub_menu = 1;
include $_SERVER['DOCUMENT_ROOT']."/market/inc/header.menu.inc.php";

$settle_no = trim($_GET['no'] ?? '');
$sh_idx    = (int)($_SESSION['current_sh_idx'] ?? 0);

// ─────────────────────────────
// 1) 기본 유효성 체크
// ─────────────────────────────
if ($settle_no === '' || $sh_idx <= 0) {
    ?>
    <div class="container-fluid py-4">
        <div class="alert alert-danger mb-3">
            잘못된 접근입니다. 정산 번호 또는 매장 정보가 올바르지 않습니다.
        </div>
        <button type="button" class="btn btn-outline-secondary" onclick="history.back();">뒤로가기</button>
    </div>
    <?php
    include $_SERVER['DOCUMENT_ROOT']."/market/foot.inc.php";
    exit;
}

// ─────────────────────────────
// 2) 정산 정보 조회 (settle_t)
// ─────────────────────────────
$DB->where('st_number', $settle_no);
$DB->where('sh_idx', $sh_idx); // 자신의 매장 정산만 조회
$settle = $DB->getOne('settle_t');

if (!$settle) {
    ?>
    <div class="container-fluid py-4">
        <div class="alert alert-danger mb-3">
            정산 정보를 찾을 수 없습니다. (정산번호: <?=htmlspecialchars($settle_no, ENT_QUOTES, 'UTF-8')?>)
        </div>
        <button type="button" class="btn btn-outline-secondary" onclick="history.back();">뒤로가기</button>
    </div>
    <?php
    include $_SERVER['DOCUMENT_ROOT']."/market/foot.inc.php";
    exit;
}

// ─────────────────────────────
// 3) 계좌 정보 (shop_t 기준, 컬럼명은 프로젝트에 맞게 조정)
// ─────────────────────────────
$bank_text = '등록된 정산 계좌 정보가 없습니다.';

$DB->where('idx', $settle['sh_idx']);
$shop = $DB->getOne('shop_t', 'sh_bank_holder, sh_bank_account, sh_bank'); // ⚠️ 컬럼명 프로젝트에 맞게

if ($shop) {
    $parts = [];
    if (!empty($shop['sh_bank']))    $parts[] = $shop['sh_bank'];
    if (!empty($shop['sh_bank_account'])) $parts[] = $shop['sh_bank_account'];
    if (!empty($shop['sh_bank_holder']))   $parts[] = $shop['sh_bank_holder'];
    if ($parts) {
        $bank_text = implode(' ', $parts);
    }
}

// ─────────────────────────────
// 4) 화면 표현용 가공
// ─────────────────────────────
$period_start = $settle['st_start_date'] ? substr($settle['st_start_date'], 0, 10) : '';
$period_end   = $settle['st_end_date']   ? substr($settle['st_end_date'], 0, 10)   : '';

$plan_date  = $settle['st_plan_date'] ? substr($settle['st_plan_date'], 0, 10) : '-';      // 정산 예정일
$done_date  = $settle['st_done_date'] ? substr($settle['st_done_date'], 0, 10) : '';       // 정산 완료일

$total_sales    = (float)$settle['st_total_amount'];   // 매출액
$service_fee    = (float)$settle['st_service_fee'];    // 수수료
$final_amount   = (float)$settle['st_final_amount'];   // 정산 금액

$total_sales_txt  = number_format((int)round($total_sales)) . '원';
$service_fee_txt  = number_format((int)round($service_fee)) . '원';
$final_amount_txt = number_format((int)round($final_amount)) . '원';

// 상태 뱃지용
$statusCode = $settle['st_status']; // READY / DONE / PLANNED

if ($statusCode === 'DONE') {
    $statusLabel = '정산완료';
    $badgeClass  = 'bg-success-subtle text-success';
} elseif ($statusCode === 'PLANNED') {
    $statusLabel = '정산예정';
    $badgeClass  = 'bg-info-subtle text-info';
} else { // READY 등
    $statusLabel = '미정산';
    $badgeClass  = 'bg-warning-subtle text-warning';
}

// ─────────────────────────────
// 5) 정산 대상 주문 내역
//    ※ 프로젝트 스키마에 따라 여기서 orders_t 등에서 조회 필요
//    일단은 스켈레톤만 두고, 실제 구현은 TODO 로 남겨둔다.
// ─────────────────────────────
$orders = [];

$DB->where('st_idx', $settle['idx']);
$DB->where('sh_idx', $sh_idx);            // 내 매장 주문만
$DB->where('ot_pay_status', 'PAID');      // 결제 완료된 주문만 (원하면 빼도 됨)
$DB->orderBy('ot_pay_date', 'ASC');

$orders = $DB->get('orders_t', null, '
    idx,
    ot_number,
    ot_total_price,
    ot_discount_amount,
    ot_pay_date,
    ot_pay_type,
    ot_status,
    ot_pay_status
');
?>
    <div class="content" id="content">
        <div class="container-fluid py-4">

            <!-- 페이지 헤딩 -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="mb-1">정산관리 상세</h4>
                    <div class="small text-muted">
                        정산번호 <?= htmlspecialchars($settle['st_number'], ENT_QUOTES, 'UTF-8') ?>
                    </div>
                </div>
                <div>
                <span class="badge <?=$badgeClass?>">
                    <?=$statusLabel?>
                </span>
                </div>
            </div>

            <div class="card margin-bottom-0">
                <div class="card-body">

                    <!-- 1. 기본 정산 정보 (조회 전용) -->
                    <h5 class="mb-3 fw-bold">정산 정보</h5>

                    <div class="form-group row">
                        <label class="col-md-2 col-form-label">정산번호</label>
                        <div class="col-md-4 col-form-label">
                            <?= htmlspecialchars($settle['st_number'], ENT_QUOTES, 'UTF-8') ?>
                        </div>

                        <label class="col-md-2 col-form-label">정산 기간</label>
                        <div class="col-md-4 col-form-label">
                            <?= htmlspecialchars($period_start, ENT_QUOTES, 'UTF-8') ?>
                            ~
                            <?= htmlspecialchars($period_end, ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    </div>

                    <div class="form-group row margin-top-15">
                        <label class="col-md-2 col-form-label">정산 예정일</label>
                        <div class="col-md-4 col-form-label">
                            <?= htmlspecialchars($plan_date, ENT_QUOTES, 'UTF-8') ?>
                        </div>

                        <label class="col-md-2 col-form-label">정산 완료일</label>
                        <div class="col-md-4 col-form-label">
                            <?= $done_date ? htmlspecialchars($done_date, ENT_QUOTES, 'UTF-8') : '-' ?>
                        </div>
                    </div>

                    <div class="form-group row margin-top-15">
                        <label class="col-md-2 col-form-label">총 매출액</label>
                        <div class="col-md-4">
                            <input type="text"
                                   class="form-control text-right"
                                   value="<?= $total_sales_txt ?>"
                                   readonly>
                        </div>

                        <label class="col-md-2 col-form-label">서비스 수수료</label>
                        <div class="col-md-4">
                            <input type="text"
                                   class="form-control text-right"
                                   value="<?= $service_fee_txt ?>"
                                   readonly>
                        </div>
                    </div>

                    <div class="form-group row margin-top-15">
                        <label class="col-md-2 col-form-label">정산 금액</label>
                        <div class="col-md-4">
                            <input type="text"
                                   class="form-control text-right"
                                   value="<?= $final_amount_txt ?>"
                                   readonly>
                        </div>

                        <label class="col-md-2 col-form-label">정산 계좌</label>
                        <div class="col-md-4 col-form-label">
                            <?= htmlspecialchars($bank_text, ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    </div>

                    <?php if (!empty($settle['st_admin_memo'])): ?>
                        <div class="form-group row margin-top-15">
                            <label class="col-md-2 col-form-label">관리자 메모</label>
                            <div class="col-md-10">
                                <div class="form-control-plaintext border rounded p-2 bg-light" style="white-space: pre-wrap;">
                                    <?= nl2br(htmlspecialchars($settle['st_admin_memo'], ENT_QUOTES, 'UTF-8')) ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="form-group row justify-content-center margin-top-30">
                        <button type="button"
                                onclick="history.back();"
                                class="btn btn-outline-secondary mx-1">목록</button>
                    </div>

                    <hr class="my-4">

                    <!-- 2. 정산 상품(주문) 내역 -->
                    <h5 class="mb-3 fw-bold">정산 상품(주문) 내역</h5>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                            <tr>
                                <th style="width: 180px;">결제일시</th>
                                <th style="width: 140px;">주문번호</th>
                                <th>상품 정보</th>
                                <th class="text-end" style="width: 140px;">총 상품금액</th>
                                <th class="text-end" style="width: 140px;">서비스 수수료</th>
                                <th class="text-end" style="width: 160px;">정산 금액</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($orders)): ?>
                                <?php
                                // 전체 정산 수수료 비율 = st_service_fee / st_total_amount
                                // 주문별 수수료/정산금액은 "표시용"으로 비례 배분
                                $feeRate = ($total_sales > 0) ? ($service_fee / $total_sales) : 0;
                                ?>

                                <?php foreach ($orders as $od): ?>
                                    <?php
                                    $paidAt    = $od['ot_pay_date'] ? $od['ot_pay_date'] : $settle['st_wdate'];
                                    $paidAtTxt = $paidAt ? date('Y-m-d H:i', strtotime($paidAt)) : '-';

                                    $orderNo   = $od['ot_number'];
                                    $orderAmt  = (float)$od['ot_total_price'];        // 주문 총 금액
                                    $discount  = (float)$od['ot_discount_amount'];    // 쿠폰 할인 금액

                                    // 비례 배분 수수료/정산금액 (표시용)
                                    $orderFee       = $feeRate > 0 ? round($orderAmt * $feeRate) : 0;
                                    $orderSettleAmt = $orderAmt - $orderFee;

                                    $orderAmtTxt      = number_format((int)round($orderAmt)) . '원';
                                    $orderFeeTxt      = number_format((int)round($orderFee)) . '원';
                                    $orderSettleAmtTxt= number_format((int)round($orderSettleAmt)) . '원';

                                    // 상품정보 칼럼: 상세 메뉴명은 ct_snapshot 구조에 따라 추후 확장
                                    $productInfo = '총 주문금액: ' . $orderAmtTxt;
                                    if ($discount > 0) {
                                        $productInfo .= ' (쿠폰 할인 ' . number_format((int)round($discount)) . '원 포함)';
                                    }
                                    ?>
                                    <tr>
                                        <!-- 결제일시 -->
                                        <td class="text-muted">
                                            <?= htmlspecialchars($paidAtTxt, ENT_QUOTES, 'UTF-8') ?>
                                        </td>

                                        <!-- 주문번호 -->
                                        <td>
                                            <?= htmlspecialchars($orderNo, ENT_QUOTES, 'UTF-8') ?>
                                        </td>

                                        <!-- 상품 정보 -->
                                        <td>
                                            <?= htmlspecialchars($productInfo, ENT_QUOTES, 'UTF-8') ?>
                                            <div class="small text-muted mt-1">
                                                (상세 메뉴 구성은 추후 제공 예정)
                                            </div>
                                        </td>

                                        <!-- 총 상품금액 -->
                                        <td class="text-end">
                                            <?= $orderAmtTxt ?>
                                        </td>

                                        <!-- 서비스 수수료 -->
                                        <td class="text-end">
                                            <?= $orderFeeTxt ?>
                                        </td>

                                        <!-- 정산 금액 -->
                                        <td class="text-end font-weight-bold">
                                            <?= $orderSettleAmtTxt ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        해당 정산에 포함된 주문 내역이 없습니다.
                                    </td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                </div><!-- /.card-body -->
            </div><!-- /.card -->

        </div><!-- /.container-fluid -->
    </div><!-- /.content -->

<?php
include $_SERVER['DOCUMENT_ROOT']."/market/foot.inc.php";
?>
