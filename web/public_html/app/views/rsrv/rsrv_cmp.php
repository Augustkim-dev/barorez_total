<div class="hd_m align-items-center justify-content-between">
    <div class="hd_btn"></div>
    <div class="page_tit line1_text flex-fill text-center" style="word-break: break-word;"><?=$main_title?></div>
    <div>
        <button class="hd_btn" type="button" onclick="location.href='<?=$_SESSION['qr_token'] ? APP_PAGE : MAP_PAGE?>'">
            <img src="<?=DESIGN_HTTP?>/img/ico_x.png" alt="닫기">
        </button>
    </div>
</div>

<div class="wrap">
    <div class="sub_pg">
        <div class="container">

            <p class="mt-5 text-center">
                <img src="<?=DESIGN_HTTP?>/img/ico_ch2.png" alt="체크" style="width: 6.4rem;">
            </p>

            <p class="tit_st3 mt-4 text-center">
                <?= $success ? $main_message : ($error_message ?? '처리되었습니다.') ?>
            </p>

            <?php if (!$success): ?>
                <div class="py-5 text-center tg_400">요청 정보를 찾을 수 없습니다.</div>
            <?php else: ?>

                <div class="card mt-5 mb_20">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <div class="mr-2">
                                <p class="d-flex align-items-center">
                                    <span class="badg <?=$status_badg?>">
                                        <span class="ic_img <?=$status_icon?> mr-2"></span><?= $type_label ?>
                                    </span>
                                    <span class="<?= $status_class ?> ml-3">
                                        <?= $status_text ?>
                                    </span>
                                </p>

                                <a href="<?= $shop_url ?>">
                                    <p class="fs_18 fw_700 mt-2"><?= $full_shop_name ?></p>
                                </a>
                            </div>

                            <div class="ml-auto">
                                <div class="item_img">
                                    <a href="<?= $shop_url ?>" class="d-block">
                                        <div class="rect rounded-pill">
                                            <img class="flex-shrink-0" src="<?= $shop_img ?>" alt="<?= $full_shop_name ?>">
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <p class="tg_400 fs_14"><?= $meta_line ?></p>
                    </div>

                    <div class="card-body">
                        <?php if ($rsv): ?>
                            <!-- 예약 정보 있음 (단순 예약 or 선결제 예약) -->
                            <div class="d-flex align-items-center rsrv_list">
                                <dt class="tg_400">예약일시</dt>
                                <dd><?= fmt_ymd_dot($rsv['rv_date']) ?> <?= fmt_hm($rsv['rv_time']) ?></dd>
                            </div>
                            <div class="d-flex align-items-center rsrv_list">
                                <dt class="tg_400">예약자</dt>
                                <dd><?= trim($rsv['rv_name'] ?? '') ?> (<?= fmt_hp($rsv['rv_hp'] ?? '') ?>)</dd>
                            </div>
                            <div class="d-flex align-items-center rsrv_list">
                                <dt class="tg_400">예약인원</dt>
                                <dd><?= (int)($rsv['rv_people'] ?? 1) ?>명</dd>
                            </div>
                        <?php elseif ($order): ?>
                            <!-- 예약 없고 주문만 있음 (포장 주문 등) -->
                            <div class="d-flex align-items-center rsrv_list  ">
                                <dt class="tg_400">주문일시</dt>
                                <dd class=" "><?= fmt_req_at($order['ot_wdate'] ?? '') ?></dd>
                            </div>
                            <div class="d-flex align-items-center rsrv_list">
                                <dt class="tg_400">예약자</dt>
                                <dd class=" "><?=$mt['mt_name'] ?? '비회원'?> <?=$mt ? '('.$mt['mt_hp'].')' : ''?></dd>
                            </div>
<!--                            <div class="d-flex align-items-center rsrv_list">-->
<!--                                <dt class="tg_400">조리시간</dt>-->
<!--                                <dd class=" ">10~20분 예상</dd>-->
<!--                            </div>-->
                        <?php endif; ?>

                        <?php if ($menu_summary): ?>
                            <div class="d-flex align-items-center rsrv_list">
                                <dt class="tg_400">주문메뉴</dt>
                                <dd class="line1_text"><?= $menu_summary ?></dd>
                            </div>
                        <?php endif; ?>

                        <?php if ($order): ?>
                        <div class="d-flex align-items-center rsrv_list border-top mt-4 pt-4">
                            <dt class="tg_400">결제금액</dt>
                            <dd class="  fw_700 ml-auto"><?=number_format($order['ot_total_price'])?>원</dd>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- 하단 버튼 -->
                <div class="form-row mt-5">
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-primary btn-block"
                                onclick="location.href='./rsrv_history.php<?= $ot_number ? '?ot_number=' . urlencode($ot_number) : ($rsv ? '?rv_idx=' . (int)$rsv['idx'] : '') ?>'">
                            <?=$order ? '주문 내역' : '내역 확인' ?>
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-primary btn-block"
                                onclick="location.href='<?=$_SESSION['qr_token'] ? APP_PAGE : MAP_PAGE?>'">
                            홈
                        </button>
                    </div>
                </div>

            <?php endif; ?>

        </div>
    </div>
</div>
