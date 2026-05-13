<?php
// view: order_cmp.php
// 컨트롤에서 아래 변수들이 준비되어 있다고 가정:
// $shopRow, $shopImg, $orderNumberTxt, $orderDateTxt, $otTableTxt
// $items, $totalQty, $subTotal, $discount, $finalPrice

$shopTitle  = trim((string)($shopRow['sh_title'] ?? ''));
$branchName = trim((string)($shopRow['sh_branch_nm'] ?? ''));
$shopTitleFull = $shopTitle;
if ($branchName !== '') $shopTitleFull .= ' ['.$branchName.']';

$isQrOrder = !empty($_SESSION['qr_token']); // 기존 UI와 동일하게 표시
?>
<!-- 상단 헤더 -->
<div class="hd_m align-items-center justify-content-between">
    <div class="hd_btn"></div>
    <div class="page_tit line1_text flex-fill text-center" style="word-break: break-word;">주문 완료</div>
    <div class="">
        <button class="hd_btn" type="button" onclick="location.replace('../')">
            <img src="<?=DESIGN_HTTP?>/img/ico_x.png" alt="닫기">
        </button>
    </div>
</div>

<div class="wrap">
    <div class="sub_pg">
        <div class="container">
            <p class="mt-5 text-center">
                <img src="<?=DESIGN_HTTP?>/img/gif_ani.gif" alt="움직이는 음식" style="width: 16rem;">
            </p>

            <p class="tit_st3 mt-4 text-center">
                <?php if ($otTableTxt !== '') { ?>
                    <span class="text-primary"><?=htmlspecialchars($otTableTxt)?></span><br>
                <?php } ?>
                주문이 완료되었습니다.
            </p>

            <!-- 매장/주문 카드 -->
            <div class="card mt-5 mb_20">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="mr-2">
                            <p class=" ">
                                <span class="badg">
                                    <?php if($isQrOrder){ ?>
                                        <span class="ic_img ic_qr mr-2"></span>QR주문
                                    <?php } else { ?>
                                        주문
                                    <?php } ?>
                                </span>
                            </p>

                            <a href="../">
                                <p class="fs_18 fw_700 mt-2">
                                    <?=htmlspecialchars($shopTitleFull)?>
                                </p>
                            </a>
                        </div>

                        <div class="ml-auto">
                            <div class="item_img">
                                <a href="../" class="d-block">
                                    <div class="rect rounded-pill">
                                        <img class="flex-shrink-0" src="<?=htmlspecialchars($shopImg)?>">
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <p class="tg_400 fs_14">
                        주문번호 : <?=htmlspecialchars($orderNumberTxt)?>
                        <?php if($orderDateTxt !== ''){ ?>
                            | <?=htmlspecialchars($orderDateTxt)?>
                        <?php } ?>
                    </p>
                </div>

                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <dt class="tg_400">결제 예정 금액</dt>
                        <dd class="fw_700"><?=number_format((int)$finalPrice)?>원</dd>
                    </div>
                </div>
            </div>

        </div>

        <div class="bar"></div>

        <!-- 주문 메뉴 -->
        <section class="container">
            <div class="pt_20">
                <h3 class="tit_st3">
                    주문 메뉴 <span class="text-primary"><?= (int)$totalQty ?></span>
                </h3>
            </div>

            <ul class="item_list2">
                <?php if (empty($items)) { ?>
                    <li class="text-center py-5 tg_400">주문 메뉴 정보가 없습니다.</li>
                <?php } else { ?>
                    <?php foreach ($items as $it) {
                        $menuName  = (string)($it['menu_name'] ?? '');
                        $qty       = max(1, (int)($it['quantity'] ?? 1));
                        $itemTotal = (int)($it['total_price'] ?? 0);
                        $opts      = (array)($it['options'] ?? []);
                        ?>
                        <li>
                            <div class="item_box">
                                <div class="w-100">
                                    <p class="fw_500"><?=htmlspecialchars($menuName)?></p>

                                    <ul class="tg_400 mt-2 fs_14 dot_list">
                                        <?php if (!empty($opts)) { ?>
                                            <?php foreach ($opts as $op) {
                                                // snapshot 예시 기준:
                                                // {"om_idx":5,"oc_idx":2,"option_name":"매운맛 조절","option_price":0,"quantity":1}
                                                $opName  = (string)($op['option_name'] ?? '옵션');
                                                $opPrice = (int)($op['option_price'] ?? 0);

                                                // 옵션 수량이 snapshot에 있으면 표시 (없으면 1로 간주)
                                                $opQty   = max(1, (int)($op['quantity'] ?? 1));

                                                $priceTxt = ($opPrice > 0) ? ' (+' . number_format($opPrice) . ')' : '';
                                                $qtyTxt   = ($opQty > 1) ? ' x' . $opQty : '';
                                                ?>
                                                <li><?=htmlspecialchars($opName)?><?=$priceTxt?><?=$qtyTxt?></li>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <li>선택 옵션 없음</li>
                                        <?php } ?>
                                    </ul>

                                    <p class="mt-3 fs_15 fw_700">
                                        <?=number_format($itemTotal)?>원
                                        <span class="tg_400 fs_13 ml-2 fw_500"><?=$qty?>개</span>
                                    </p>
                                </div>
                            </div>
                        </li>
                    <?php } ?>
                <?php } ?>
            </ul>
        </section>

        <div class="bar"></div>

        <!-- 결제정보 -->
        <section class="container">
            <div class="pt_20 mb_20">
                <h3 class="tit_st3">결제정보</h3>
            </div>

            <div class="d-flex align-items-center justify-content-between mb-3">
                <dt>총 상품 금액</dt>
                <dd class="fw_700"><?=number_format((int)$subTotal)?>원</dd>
            </div>

            <div class="d-flex align-items-center justify-content-between mb-3">
                <dt>쿠폰 할인</dt>
                <dd class="fw_700">-<?=number_format((int)$discount)?>원</dd>
            </div>

            <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                <dt>결제 예정 금액</dt>
                <dd class="fw_700"><?=number_format((int)$finalPrice)?>원</dd>
            </div>

            <div class="form-row mt-5">
                <div class="col-6">
                    <button type="button" class="btn btn-outline-primary btn-block"
                            onclick="location.href='./order_guest.php?tv_idx=<?=$orderRow['tv_idx']?>'">
                        주문 내역
                    </button>
                </div>
                <div class="col-6">
                    <button type="button" class="btn btn-primary btn-block" onclick="location.href='../'">
                        홈
                    </button>
                </div>
            </div>
        </section>

    </div>
</div>
