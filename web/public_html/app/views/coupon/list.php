<div class="wrap">
    <div class="sub_pg bg-light ">
        <nav class="tab_fixed">
            <ul class="nav nav_tab_line" id="nav-tab" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" id="tab03_tab1" data-toggle="tab" data-target="#tab03_1" type="button" role="tab" aria-selected="true">사용 가능</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="tab03_tab2" data-toggle="tab" data-target="#tab03_2" type="button" role="tab" aria-selected="false">사용 완료/불가</button>
                </li>
            </ul>
        </nav>

        <div class="container mt_20">
            <div class="tab-content" id="nav_Content02">

                <!-- 사용 가능 -->
                <div class="tab-pane fade show active" id="tab03_1">
                    <p class="mb-2">총 <span class="text-primary"><?=count($availableCoupons)?></span>장</p>

                    <?php if (empty($availableCoupons)) { ?>
                        <div class="no_data text-center py-5">
                            <p>사용 가능한 쿠폰이 없습니다.</p>
                        </div>
                    <?php } else { ?>
                        <?php foreach ($availableCoupons as $c) { ?>
                            <div class="border rounded coupon_box mb-3 ">
                                <p class="fs_22 text-primary fw_700 ">
                                    <?=coupon_discount_text($c)?>
                                </p>
                                <p class="mt-2 fw_600 fs_15">
                                    <?=$c['ct_title']?>
                                </p>
                                <div class="d-flex justify-content-between align-items-end mt-4">
                                    <p class="tg_400 fs_14 line_h1_3 ">
                                        <?=$c['_min_text']?><br>
                                        <?=$c['_end_text']?>
                                    </p>
                                    <p class="coupon_use"><?=$c['_state_text']?></p>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>

                <!-- 사용 완료/불가 -->
                <div class="tab-pane fade" id="tab03_2">
                    <p class="mb-2">총 <span class="text-primary"><?=count($inactiveCoupons)?></span>장</p>

                    <?php if (empty($inactiveCoupons)) { ?>
                        <div class="no_data text-center py-5">
                            <p>사용 완료/불가 쿠폰이 없습니다.</p>
                        </div>
                    <?php } else { ?>
                        <?php foreach ($inactiveCoupons as $c) { ?>
                            <div class="border rounded coupon_box mb-3 notuse ">
                                <p class="fs_22 text-primary fw_700 ">
                                    <?=coupon_discount_text($c)?>
                                </p>
                                <p class="mt-2 fw_600 fs_15">
                                    <?=$c['ct_title']?>
                                </p>
                                <div class="d-flex justify-content-between align-items-end mt-4">
                                    <p class="tg_400 fs_14 line_h1_3 ">
                                        <?=$c['_min_text']?><br>
                                        <?=$c['_end_text']?>
                                    </p>
                                    <p class="coupon_use"><?=$c['_state_text']?></p>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>

            </div>
        </div>
    </div>
</div>
