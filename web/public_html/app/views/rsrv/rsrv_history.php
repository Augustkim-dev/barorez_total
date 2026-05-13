<div class="wrap">
    <div class="sub_pg">
        <div class="bg-light px_16 py_20">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="mr-2">
                            <p class="d-flex align-items-center">
                                <span class="badg <?=$badgeColor?>">
                                    <span class="ic_img <?=$badgeIcon?> mr-2"></span><?=$badgeTitle?>
                                </span>
                                <span class="<?=$badgeClass?> ml-3"><?=$rvStatusText?></span>
                            </p>

                            <a href="<?=$linkShop?>">
                                <p class="fs_18 fw_700 mt-2">
                                    <?=($shop['sh_title'] ?? '')?>
                                    <?=!empty($shop['sh_branch_nm']) ? ' ['.($shop['sh_branch_nm']).']' : ''?>
                                </p>
                            </a>
                        </div>

                        <div class="ml-auto">
                            <div class="item_img">
                                <a href="<?=$linkShop?>" class="d-block">
                                    <div class="rect rounded-pill">
                                        <img class="flex-shrink-0" src="<?=$shop_img?>" alt="">
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <p class="tg_400 fs_14">
                        <?php if ($isReservation): ?>
                            <!-- 예약인 경우: 예약번호 (reservation_t.idx 패딩) -->
                            예약번호 : No.<?= str_pad($reservation['rv_number'], 8, '0', STR_PAD_LEFT) ?>
                            <?php if (!empty($reservation['rv_date']) && !empty($reservation['rv_time'])): ?>
                                | <?= date('y.m.d H:i', strtotime($reservation['rv_date'] . ' ' . $reservation['rv_time'])) ?>
                            <?php endif; ?>
                        <?php else: ?>
                            <!-- 포장 주문인 경우: 주문번호 -->
                            주문번호 : <?= $order['ot_number'] ?? '' ?>
                            <?php if (!empty($order['ot_wdate'])): ?>
                                | <?= date('y.m.d H:i', strtotime($order['ot_wdate'])) ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    </p>
                </div>

                <div class="card-body">
                    <?php if($isReservation){ ?>
                        <div class="d-flex align-items-center rsrv_list">
                            <dt class="tg_400">예약일시</dt>
                            <dd><?=$rvDateText?> <?=$rvTimeText?></dd>
                        </div>
                        <div class="d-flex align-items-center rsrv_list">
                            <dt class="tg_400">예약자</dt>
                            <dd><?=($reservation['rv_name'] ?? '')?> (<?=($reservation['rv_hp'] ?? '')?>)</dd>
                        </div>
                        <div class="d-flex align-items-center rsrv_list">
                            <dt class="tg_400">예약인원</dt>
                            <dd><?= (int)($reservation['rv_people'] ?? 1) ?>명</dd>
                        </div>
                    <?php } else{ ?>
                        <div class="d-flex align-items-center rsrv_list">
                            <dt class="tg_400">포장 예약자</dt>
                            <dd><?=($mt['mt_name'] ?? '비회원')?> <?=$mt ? '('.$mt['mt_hp'].')' : ''?></dd>
                        </div>
                        <div class="d-flex align-items-center rsrv_list">
                            <dt class="tg_400">조리 시간</dt>
                            <dd><?= !empty($prep_min) ? $prep_min.'분' : '-' ?></dd>
                        </div>
                    <?php } ?>
                </div>
                <?php if(!$prep_min):?>
                    <?php if(!$order['ot_cancel'] && $reservation['rv_status'] !== 'CANCELLED'){ ?>
                        <div class="card-footer">
                            <button type="button" class="btn btn-outline-light btn-md btn-block" data-toggle="modal" data-target="#pop_rsrv">
                                <?=$isReservation ? '예약 취소' : '포장 취소'?>
                            </button>
                            <?php
                            $resType = $shop['sh_reserve_pay_type'] === 'POSTPAY';

                            if(!$resType && $isReservation){ ?>
                                <div class="d-flex align-items-center rsrv_list mt-5">
                                    <dt class="tg_400">환불규정</dt>
                                    <div>
                                        <dd><?=$rsvMsg['rs_notice']?></dd>
                                    </div>
                                </div>
                                <?php if($rsvPrice['rp_use'] === 'Y'){?>
                                <div class="d-flex align-items-center rsrv_list mt-5">
                                    <dt class="tg_400">위약금</dt>
                                    <div>
                                        <dd><?=$rsvPrice['rp_type'] === 'FIXED' ? $rsvPrice['rp_value'].'원' : $rsvPrice['rp_value'].'%'?></dd>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center rsrv_list mt-5">
                                    <dt class="tg_400">무료 환불</dt>
                                    <div>
                                        <dd>방문 당일 기준 <?=$rsvPrice['rp_free_cancel_before_min']/60 === 24 ? '24시간전' : ($rsvPrice['rp_free_cancel_before_min']/60/24).'일전'?></dd>
                                    </div>
                                </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    <?php } ?>
                <?php else:?>
                    <div class="card-footer">
                        <button type="button" class="btn btn-outline-light btn-md btn-block" disabled>
                            주문 접수로 취소가 불가능합니다.
                        </button>
                    </div>
                <?php endif;?>
            </div>
        </div>

        <div class="bar"></div>

        <?php if($showOrderMenus){ ?>
            <!-- 주문 메뉴 -->
            <section class="container">
                <div class="pt_20">
                    <h3 class="tit_st3">주문 메뉴 <span class="text-primary"><?= (int)$totalQty ?></span></h3>
                </div>

                <ul class="item_list2">
                    <?php foreach($snapItems as $it){
                        $menuName = $it['menu_name'] ?? $it['sm_title'] ?? $it['title'] ?? '';
                        $qty      = (int)($it['quantity'] ?? $it['qty'] ?? 1); if($qty<=0) $qty = 1;
                        $unit     = (int)($it['unit_price'] ?? $it['unitPrice'] ?? 0);
                        $total    = (int)($it['total_price'] ?? $it['totalPrice'] ?? ($unit*$qty));

                        $opts = (isset($it['options']) && is_array($it['options'])) ? $it['options'] : [];
                        ?>
                        <li>
                            <div class="item_box">
                                <div class="w-100">
                                    <p class="fw_500"><?= $menuName ?></p>

                                    <ul class="tg_400 mt-2 fs_14 dot_list">
                                        <?php if(!empty($opts)){ ?>
                                            <?php foreach($opts as $op){
                                                // 옵션 구조가 프로젝트마다 다를 수 있어서 최대한 유연하게
                                                $oc = $op['oc_title'] ?? $op['category'] ?? $op['option_category'] ?? '';
                                                $on = $op['name'] ?? $op['om_title'] ?? $op['option_name'] ?? '';
                                                $pp = (int)($op['price'] ?? $op['om_price'] ?? $op['option_price'] ?? 0);
                                                $line = trim(($oc ? $oc.' : ' : '').$on);
                                                if($pp > 0) $line .= ' (+' . number_format($pp) . ')';
                                                if($line === '') $line = '옵션';
                                                ?>
                                                <li><?=$line?></li>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <li>선택 옵션 없음</li>
                                        <?php } ?>
                                    </ul>

                                    <p class="mt-3 fs_15 fw_700">
                                        <?=number_format($total)?>원
                                        <span class="tg_400 fs_13 ml-2 fw_500"><?=$qty?>개</span>
                                    </p>
                                </div>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
            </section>

            <div class="bar"></div>
        <?php } ?>

        <!-- 매장정보 -->
        <section class="container pb_20">
            <div class="pt_20 mb-3">
                <h3 class="tit_st3">매장정보</h3>
            </div>

            <div class="mt-4">
                <div class="d-flex shop_story">
                    <div class="tg_400 tit">연락처</div>
                    <div class="flex-fill">
                        <?= $shop['sh_tel'] ?? $shop['sh_hp'] ?? $shop['sh_phone'] ?? '-' ?>
                    </div>
                </div>

                <div class="d-flex shop_story">
                    <div class="tg_400 tit">위치안내</div>
                    <div class="flex-fill">
                        <?php
                        $addr = $shop['sh_addr'] ?? $shop['sh_addr1'] ?? '';
                        $addr2= $shop['sh_addr2'] ?? '';
                        $fullAddr = trim($addr . ' ' . $addr2);
                        ?>
                        <p>
                            <?=$fullAddr ?: '-'?>
                            <?php if($fullAddr){ ?>
                                <br>
                                <a href="#"
                                   class="un_reboot_a tg_400 mt-2"
                                   onclick="btnCopyAddr('<?=addslashes($fullAddr)?>')">
                                    주소 복사
                                </a>
                            <?php } ?>
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <?php if($showOrderMenus){ ?>
            <div class="bar"></div>

            <!-- 결제정보 -->
            <section class="container">
                <div class="pt_20 mb_20">
                    <h3 class="tit_st3">결제정보</h3>
                </div>

                <div class="d-flex align-items-center justify-content-between mb-3">
                    <dt>총 상품 금액</dt>
                    <dd class="fw_700"><?=number_format($sumGoods)?>원</dd>
                </div>

                <div class="d-flex align-items-center justify-content-between mb-3">
                    <dt>쿠폰 할인</dt>
                    <dd class="fw_700">-<?=number_format($sumDisc)?>원</dd>
                </div>

                <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                    <dt>총 결제 금액</dt>
                    <dd class="fw_700"><?=number_format($sumFinal)?>원</dd>
                </div>
            </section>
        <?php } ?>
    </div>
</div>

<!-- 주문취소 팝업 -->
<div class="modal fade" id="pop_rsrv" tabindex="-1" style="display:none;" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-body mt-5">
                <div class="no_data">
                    <img src="<?=DESIGN_HTTP?>/img/img_mark.png" alt="">
                    <p class="line_h1_4 mt-3 fs_18 fw_600"><?=$isReservation ? '예약' : '포장'?>을 취소하시겠습니까?</p>
                    <p class="tg_400 mt-3 fs_14">취소 후에는 복구가 불가능합니다.</p>
                </div>
            </div>

            <div class="modal-footer">
                <div class="form-row justify-content-end">
                    <div class="col-4">
                        <button type="button" class="btn btn-outline-light btn-block" data-dismiss="modal">
                            아니오
                        </button>
                    </div>
                    <div class="col-8">
                        <button type="button" class="btn btn-primary btn-block" id="confirmCancelBtn">
                            <?=$isReservation ? '예약' : '포장'?> 취소
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<?php include_once("../inc/modal.php");?>
<!-- 예약 취소 AJAX 처리 (jQuery 사용) -->
<script>
    $(document).ready(function() {
        $('#confirmCancelBtn').on('click', function() {
            // Bootstrap 모달 닫기
            $('#pop_rsrv').modal('hide');
            let rv_idx = '<?=$isReservation?>';
            let title = rv_idx ? '예약' : '포장';
            // 최종 확인 (이미 모달에서 한 번 물어봤지만 안전하게 한 번 더)
            ModalUtil.confirm({
                title: '메뉴',
                message: '정말로 '+title+'을 취소하시겠습니까?\n\n취소 후 복구가 불가능합니다.',
                okText: '확인',
                cancelText: '취소',
                onOk: function () {
                    // 로딩 표시 (선택사항)
                    let $btn = $(this);
                    $btn.prop('disabled', true).text('취소 처리중...');
                    let url = '<?=RSRV_ACTIONS?>/update.php';
                    $.ajax({
                        url: url,  // ← 본인의 API 파일 경로로 정확히 수정!!
                        type: 'POST',
                        data: {
                            act: 'cancel_reservation',
                            ot_number: '<?= addslashes($ot_number) ?>',
                            type: rv_idx ? '예약' : '포장',
                            rv_idx: '<?=$_GET['rv_idx']?>',
                        },
                        dataType: 'json',
                        success: function(res) {
                            console.log('res',res);
                            if (res.success) {
                                console.log('res2','dur');
                                if(res?.data?.ot_number){
                                    location.replace('../rsrv/rsrv_history.php?ot_number='+res?.data?.ot_number);
                                }else{
                                    location.replace('../rsrv/rsrv_history.php?rv_idx='+res?.data?.rv_idx);
                                }
                            } else {
                                ModalUtil.alert({
                                    title: '알림',
                                    message: '취소 실패: ' + res.message,
                                    okText: '확인',
                                    onOk: function () {
                                    },
                                });
                                $btn.prop('disabled', false).text('예약 취소');
                            }
                        },
                        error: function(xhr, status, err) {
                            console.error(xhr.responseText);
                            alert('서버와 통신 중 오류가 발생했습니다.\n잠시 후 다시 시도해주세요.');
                            $btn.prop('disabled', false).text('예약 취소');
                        }
                    });
                },
                onCancel: function (){
                    return false;
                }
            });
        });
    });
    function btnCopyAddr(SHOP_ADDR) {
        if (!SHOP_ADDR) {
            ModalUtil.alert({
                title: '알림',
                message: '복사할 주소가 없습니다.',
                okText: '확인',
                onOk: function () {
                },
            });
            return;
        }

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(SHOP_ADDR).then(function () {
                ModalUtil.alert({
                    title: '알림',
                    message: '주소가 복사되었습니다.',
                    okText: '확인',
                    onOk: function () {
                    },
                });
            }).catch(function () {
                ModalUtil.alert({
                    title: '알림',
                    message: '복사에 실패했습니다.',
                    okText: '확인',
                    onOk: function () {
                    },
                });
            });
        } else {
            let ta = document.createElement('textarea');
            ta.value = SHOP_ADDR;
            document.body.appendChild(ta);
            ta.select();
            document.execCommand('copy');
            document.body.removeChild(ta);
            ModalUtil.alert({
                title: '알림',
                message: '주소가 복사되었습니다.',
                okText: '확인',
                onOk: function () {
                },
            });
        }
    }
</script>
