<?php
// view: order_guest.php

function fmtOrderDatetime($dt) {
    if (!$dt) return '';
    $ts = strtotime($dt);
    return $ts ? date('Y년 m월 d일 H:i', $ts) : htmlspecialchars($dt);
}

$isNoOrder = empty($orders);
?>

<div class="wrap">
    <div class="sub_pg">
        <!-- 상단 매장 정보 -->
        <div class="bg-light px_16 py_20">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="mr-2">
                            <p class="mb-1">
                                <span class="badg">
                                    <?php if($isQrOrder): ?>
                                        <span class="ic_img ic_qr mr-2"></span>QR 주문
                                    <?php else: ?>
                                        주문 내역
                                    <?php endif; ?>
                                </span>
                            </p>
                            <a href="../">
                                <p class="fs_18 fw_700 mt-2"><?=htmlspecialchars($shopTitleFull)?></p>
                            </a>
                            <?php if($tableNoDisplay !== ''): ?>
                                <p class="tg_500 fs_14 mt-1"><?=htmlspecialchars($tableNoDisplay)?>번 테이블</p>
                            <?php endif; ?>
                        </div>
                        <div class="ml-auto">
                            <div class="item_img">
                                <a href="../" class="d-block">
                                    <div class="rect rounded-pill">
                                        <img class="flex-shrink-0" src="<?=htmlspecialchars($shopImg)?>" alt="매장 로고">
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <dt class="tg_400">결제 예정 금액</dt>
                        <dd class="fw_700 text-primary"><?=number_format((int)$sumFinal)?>원</dd>
                    </div>
                </div>
            </div>
        </div>

        <div class="bar"></div>

        <section class="container pb_20">
            <div class="pt_20 pb-4">
                <h3 class="tit_st3">
                    주문 메뉴 <span class="text-primary"><?= (int)$totalMenuQty ?></span>
                </h3>
            </div>

            <?php if ($isNoOrder): ?>
                <div class="text-center py-5 tg_400 fs_16">
                    <?php if($isQrOrder): ?>
                        아직 주문 내역이 없습니다.<br>
                        메뉴를 선택해 주문해주세요!
                    <?php else: ?>
                        주문 내역을 확인할 수 있는 세션이 없습니다.<br>
                        QR 코드를 스캔해 주세요.
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <?php foreach($orders as $od): ?>
                    <div class="card mb-3">
                        <div class="card-header bg-light line_h1_3">
                            <p class="tg_500 fs_14">주문 번호: <?=htmlspecialchars($od['ot_number'])?></p>
                            <p class="tg_500 fs_14">주문 시간: <?=fmtOrderDatetime($od['ot_wdate'])?></p>
                        </div>

                        <div class="card-body py-0">
                            <ul class="item_list2">
                                <?php if(empty($od['items'])): ?>
                                    <li class="py-4 text-center tg_400">주문 메뉴 정보가 없습니다.</li>
                                <?php else: ?>
                                    <?php foreach($od['items'] as $it): ?>
                                        <li>
                                            <div class="item_box">
                                                <div class="w-100">
                                                    <p class="fw_500"><?=htmlspecialchars($it['menu_name'])?></p>

                                                    <ul class="tg_400 mt-2 fs_14 dot_list">
                                                        <?php if(!empty($it['options'])): ?>
                                                            <?php foreach($it['options'] as $op):
                                                                $opName = htmlspecialchars($op['option_name'] ?? '옵션');
                                                                $opPrice = (int)($op['option_price'] ?? 0);
                                                                $priceTxt = $opPrice > 0 ? ' (+'.number_format($opPrice).')' : '';
                                                                ?>
                                                                <li><?=$opName?><?=$priceTxt?></li>
                                                            <?php endforeach; ?>
                                                        <?php else: ?>
                                                            <li>선택 옵션 없음</li>
                                                        <?php endif; ?>
                                                    </ul>

                                                    <p class="mt-3 fs_15 fw_700">
                                                        <?=number_format($it['total_price'])?>원
                                                        <span class="tg_400 fs_13 ml-2 fw_500"><?=$it['quantity']?>개</span>
                                                    </p>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </div>

                        <div class="card-footer bg-transparent border-0 pt-0">
                            <div class="d-flex justify-content-end align-items-center">
                                <small class="tg_500">소계</small>
                                <strong class="ml-2"><?=number_format($od['final_total'])?>원</strong>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>

        <?php if (!$isNoOrder): ?>
            <div class="bar"></div>

            <section class="container mb_40">
                <div class="pt_20 mb_20">
                    <h3 class="tit_st3">결제 정보</h3>
                </div>

                <div class="d-flex align-items-center justify-content-between mb-3">
                    <dt>총 상품 금액</dt>
                    <dd class="fw_700"><?=number_format((int)$sumSubTotal)?>원</dd>
                </div>

                <div class="d-flex align-items-center justify-content-between mb-3">
                    <dt>쿠폰 할인</dt>
                    <dd class="fw_700 text-danger">-<?=number_format((int)$sumDiscount)?>원</dd>
                </div>

                <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                    <dt class="fs_18">결제 예정 금액</dt>
                    <dd class="fw_700 text-primary fs_18"><?=number_format((int)$sumFinal)?>원</dd>
                </div>
            </section>
        <?php endif; ?>
    </div>
</div>
