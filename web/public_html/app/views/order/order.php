<div class="wrap">
    <div class="sub_pg pb_lg">
        <!-- 상단 매장 정보 -->
        <div class="container shop_hd">
            <div class="d-flex align-items-center">
                <div class="mr-2">
                    <p>
                        <?php if($isQr){?>
                        <span class="badg">
                            <span class="ic_img ic_qr mr-2"></span>QR주문
                        </span>
                        <?php }?>
                        <?php if($_SESSION['order_mode'] === 'reservation'){?>
                        <span class="badg blue">
                            <span class="ic_img ic_calendar mr-2"></span>예약
                        </span>
                        <?php }?>
                        <?php if($_SESSION['order_mode'] === 'takeout'){?>
                            <span class="badg green">
                            <span class="ic_img ic_pack mr-2"></span>포장
                        </span>
                        <?php }?>
                        </p>
                    <a href="./shop.php">
                        <p class="fs_18 fw_700 mt-2"><?=htmlspecialchars(($shopRow['sh_title'] ?? ''))?><?=!empty($shopRow['sh_branch_nm']) ? ' ['.htmlspecialchars($shopRow['sh_branch_nm']).']' : ''?></p>
                    </a>
                </div>
                <div class="ml-auto">
                    <div class="item_img">
                        <a href="../shop/list.php?sh_idx=<?=$st_id?>" class="d-block">
                            <div class="rect rounded-pill">
                                <img class="flex-shrink-0" src="<?=htmlspecialchars($shopImg)?>">
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="bar"></div>

        <?php if (empty($cartRows)) { ?>
            <div class="container py-5 text-center">
                <p class="tg_400">장바구니에 담긴 메뉴가 없습니다.</p>
                <div class="mt-4">
                    <button type="button" class="btn btn-outline-primary btn-block btn_st1" onclick="location.href='./shop.php'">
                        메뉴 보러가기 <img src="<?=DESIGN_HTTP?>/img/btn_deco.svg" alt=" " class="ml-3">
                    </button>
                </div>
            </div>
        <?php } else { ?>

            <!-- 주문 메뉴 -->
            <section class="container">
                <div class="pt_20">
                    <h3 class="tit_st3">주문 메뉴 <span class="text-primary" id="orderMenuCnt"><?= (int)$totalQty ?></span></h3>
                </div>

                <ul class="item_list2" id="orderMenuList">
                    <?php foreach ($cartRows as $row) {
                        $ct_idx = (int)$row['ct_idx'];
                        $qty = max(1, (int)$row['ct_quantity']);
                        $img = !empty($row['sm_img']) ? $row['sm_img'] : (DESIGN_HTTP.'/img/pr_sample03.jpg');

                        $optList = $optionsMap[$ct_idx] ?? [];
                        ?>
                        <li data-ct-idx="<?=$ct_idx?>">
                            <div class="item_box">
                                <div class="item_img2 flex-shrink-0">
                                    <div class="rect rounded">
                                        <img class=" " src="<?=htmlspecialchars($img)?>" alt="상품사진">
                                    </div>
                                </div>

                                <div class="w-100">
                                    <p class="fw_500"><?=htmlspecialchars($row['sm_title'] ?? '')?></p>

                                    <ul class="tg_400 mt-2 fs_14 dot_list">
                                        <?php if(!empty($optList)) { ?>
                                            <?php foreach($optList as $o){
                                                $ocTitle = $o['oc_title'] ?? '옵션';
                                                $opName  = $o['co_option_name'] ?? '';
                                                $opPrice = (int)($o['co_option_price'] ?? 0);
                                                $priceTxt = ($opPrice > 0) ? ' (+' . number_format($opPrice) . ')' : '';
                                                ?>
                                                <li><?=htmlspecialchars($ocTitle)?> : <?=htmlspecialchars($opName)?><?=$priceTxt?></li>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <li>선택 옵션 없음</li>
                                        <?php } ?>
                                    </ul>

                                    <p class="mt-3 fs_15 fw_700">
                                        <?=number_format((int)$row['ct_total_price'])?>원
                                        <span class="tg_400 fs_13 ml-2 fw_500"><?=$qty?>개</span>
                                    </p>
                                </div>

                                <a class="item_link" href="./shop/detail.php?id=<?=(int)$row['sm_id']?>"></a>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
            </section>

            <?php if($mt_idx):?>
            <div class="bar"></div>
            <!-- 할인 쿠폰 -->
            <section class="container pb-5">
                <div class="pt_20 mb_20">
                    <h3 class="tit_st3">할인 쿠폰</h3>
                </div>

                <?php
                $couponCnt = is_array($coupons) ? count($coupons) : 0;
                $hasApplied = !empty($appliedCoupon['ct_idx']);
                ?>

                <button type="button" class="coupon_btn border" data-toggle="modal" data-target="#pop_coupon" id="btnOpenCoupon">
                    <div>
                        쿠폰선택
                        <?php if($hasApplied){ ?>
                            <span class="badg sm ml-2"> 적용중</span>
                        <?php } ?>
                    </div>
                    <div class="text-primary fw_500" id="couponRightText">
                        <?php if($hasApplied){ ?>
                            <span class="mr-2">- <?=number_format($discount)?> 원</span>
                        <?php } else { ?>
                            <span class="mr-2">적용가능 <?= (int)$couponCnt ?>장</span>
                        <?php } ?>
                        <img class="flex-shrink-0" src="<?=DESIGN_HTTP?>/img/ico_arrow2.png" style="width:2rem">
                    </div>
                </button>

                <?php if($hasApplied){ ?>
                    <button type="button" class="btn btn-outline-light btn-block border-0 un_reboot_a btn-lg" id="btnClearCoupon">쿠폰 적용 해제</button>
                <?php } ?>
            </section>
            <?php endif;?>

            <div class="bar"></div>
            <!-- 결제정보 -->
            <section class="container">
                <div class="pt_20 mb_20">
                    <h3 class="tit_st3">결제정보</h3>
                </div>

                <div class="d-flex align-items-center justify-content-between mb-3">
                    <dt>총 상품 금액</dt>
                    <dd class="fw_700" id="sumGoods"><?=number_format($totalPrice)?>원</dd>
                </div>

                <div class="d-flex align-items-center justify-content-between mb-3">
                    <dt>쿠폰 할인</dt>
                    <dd class="fw_700" id="sumCoupon">-<?=number_format($discount)?>원</dd>
                </div>

                <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                    <dt>총 결제 금액</dt>
                    <dd class="fw_700" id="sumFinal"><?=number_format($finalPrice)?>원</dd>
                </div>
            </section>

            <div class="bottom_btn">
                <div class="form-row">
                    <div class="col-12">
                        <?php if($isQr):?>
                            <?php if($shopType):?>
                                <button type="button" class="btn btn-primary btn-block btn-lg" id="btnPay">
                                    <span id="btnPayPrice"><?=number_format($finalPrice)?>원</span> 결제하기
                                </button>
                            <?php else:?>
                                <button type="button" class="btn btn-primary btn-block btn-lg" id="btnPaid">
                                    주문하기
                                </button>
                            <?php endif;?>
                        <?php else:?>
                            <?php if(!$isRes ||$shopResType):?>
                            <button type="button" class="btn btn-primary btn-block btn-lg" id="btnPay">
                                <span id="btnPayPrice"><?=number_format($finalPrice)?>원</span> 결제하기
                            </button>
                            <?php else:?>
                                <button type="button" class="btn btn-primary btn-block btn-lg" id="btnPaidRes">
                                    주문하기
                                </button>
                            <?php endif;?>
                        <?php endif;?>
                    </div>
                </div>
            </div>

        <?php } // cartRows else ?>
    </div>
</div>

<!-- 쿠폰 선택 모달 -->
<div class="modal modal_full" id="pop_coupon" tabindex="-1" style="display:none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div class="hd_btn justify-content-start"></div>
                <div class="page_tit flex-fill text-center">쿠폰 선택</div>
                <div class="hd_btn justify-content-end">
                    <button type="button" data-dismiss="modal"><img src="<?=DESIGN_HTTP?>/img/ic_close.png" alt="닫기"></button>
                </div>
            </div>

            <div class="modal-body">
                <div class="coupon_list">
                    <ul>
                        <?php if(empty($coupons)) { ?>
                            <li class="text-center py-5 tg_400">적용 가능한 쿠폰이 없습니다.</li>
                        <?php } else { ?>
                            <?php foreach($coupons as $c){
                                $isChecked = (!empty($appliedCoupon['ct_idx']) && (int)$appliedCoupon['ct_idx'] === (int)$c['idx']);
                                ?>
                                <li>
                                    <label class="coupon_item">
                                        <div class="media w-100 align-items-center">
                                            <div class="flex-fill">
                                                <?php
                                                $type2 = (int)($c['ct_type2'] ?? 1);         // 1=정액,2=정율
                                                $val   = (int)($c['ct_discount1'] ?? 0);     // 금액/퍼센트
                                                $discTitle = ($type2 === 2) ? (number_format($val)."% 할인") : (number_format($val)."원 할인");

                                                $minPrice = (int)($c['ct_discount3'] ?? 0);

                                                $today = date('Y-m-d');
                                                if ((int)($c['ct_type1'] ?? 1) === 1) {
                                                    $expTxt = (!empty($c['ct_edate']) ? $c['ct_edate'] : '') . '까지';
                                                } else {
                                                    $days = (int)($c['ct_days'] ?? 0);
                                                    $expTxt = date('Y-m-d', strtotime($today." +{$days} day")) . '까지';
                                                }
                                                ?>

                                                <p class="tit_st4 text-primary"><?=$discTitle?></p>
                                                <p class="fw_600 mb-3 mt-3"><?=htmlspecialchars($c['ct_title'] ?? '')?></p>
                                                <p class="fs_13 tg_500">최소주문금액 <?=number_format($minPrice)?>원</p>
                                                <p class="fs_13 tg_500 mt-1"><?=htmlspecialchars($expTxt)?></p>
                                            </div>
                                            <div class="btn_wr mr-2">
                                                <div class="checks">
                                                    <input type="radio"
                                                           name="coupon_pick"
                                                           value="<?= (int)$c['idx'] ?>"
                                                           data-discount="<?= (int)$c['ct_discount1'] ?>"
                                                        <?= $isChecked ? 'checked' : '' ?>>
                                                    <span class="ic_box"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                </li>
                            <?php } ?>
                        <?php } ?>
                    </ul>
                </div>

                <button type="button" class="btn btn-primary btn-block btn-lg" id="btnApplyCoupon">적용</button>
                <button type="button" class="btn btn-outline-light btn-block border-0 un_reboot_a btn-lg" data-dismiss="modal">취소</button>

                <input type="hidden" id="csrfToken" value="<?=htmlspecialchars($_SESSION['csrf_token'] ?? '')?>">
                <input type="hidden" id="couponCount" value="<?=(int)$couponCnt?>">
            </div>
        </div>
    </div>
</div>
<?php include_once("../inc/modal.php");?>
<script src="https://cdn.portone.io/v2/browser-sdk.js"></script>
<script>
    $(function(){
        console.log('[order] init');

        let url = '<?=ORDER_ACTIONS?>/order.php'; // ✅ 주문/결제 전용 API
        let csrfToken = $('#csrfToken').val() || '';
        console.log('[order] api url:', url);
        console.log('[order] csrfToken:', csrfToken);

        // ----------------------------
        // 쿠폰 적용
        // ----------------------------
        $('#btnApplyCoupon').on('click', function(){
            let $pick = $('input[name="coupon_pick"]:checked');
            let cpIdx = parseInt($pick.val(), 10) || 0;

            console.log('[coupon apply] cpIdx:', cpIdx);

            if(!cpIdx){
                ModalUtil.alert({
                    title: '알림',
                    message: '쿠폰을 선택해 주세요.',
                    okText: '확인',
                    onOk: function () {
                    },
                });
                return;
            }

            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                data: {
                    act: 'apply_coupon',
                    ct_idx: cpIdx,
                    csrf_token: csrfToken
                },
                success: function(res){
                    if(!res || !res.success){
                        ModalUtil.alert({
                            title: '알림',
                            message: (res && res.message) ? res.message : '쿠폰 적용에 실패했습니다.',
                            okText: '확인',
                            onOk: function () {
                            },
                        });
                        return;
                    }

                    // 1. 금액 갱신
                    applySummary(res.data);

                    // 2. 쿠폰 버튼 텍스트/뱃지 갱신
                    let discountTxt = '- ' + Number(res.data.discount).toLocaleString('ko-KR') + ' 원';
                    $('#couponRightText').html(`
                <span class="mr-2">${discountTxt}</span>
                <img class="flex-shrink-0" src="<?=DESIGN_HTTP?>/img/ico_arrow2.png" style="width:2rem">
            `);

                    // 3. "적용중" 뱃지 추가 (이미 있으면 중복 방지)
                    if ($('#btnOpenCoupon .badg.sm').length === 0) {
                        $('#btnOpenCoupon > div:first').append('<span class="badg sm ml-2"> 적용중</span>');
                    }

                    // 4. "쿠폰 적용 해제" 버튼 동적 추가
                    if ($('#btnClearCoupon').length === 0) {
                        $('#btnOpenCoupon').after(`
                    <button type="button" class="btn btn-outline-light btn-block border-0 un_reboot_a btn-lg" id="btnClearCoupon">
                        쿠폰 적용 해제
                    </button>
                `);

                        // 새로 추가된 버튼에 이벤트 바인딩
                        $('#btnClearCoupon').on('click', function(){
                            // 기존 clear_coupon 로직 그대로
                            $.ajax({
                                url: url,
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    act: 'clear_coupon',
                                    csrf_token: csrfToken
                                },
                                success: function(clearRes){
                                    if(!clearRes || !clearRes.success){
                                        ModalUtil.alert({
                                            title: '알림',
                                            message: (clearRes && clearRes.message) ? clearRes.message : '쿠폰 해제에 실패했습니다.',
                                            okText: '확인',
                                            onOk: function () {
                                            },
                                        });
                                        return;
                                    }

                                    applySummary(clearRes.data);

                                    // 해제 후 UI 복구
                                    $('#couponRightText').html(`
                                <span class="mr-2">적용가능 <?= (int)$couponCnt ?>장</span>
                                <img class="flex-shrink-0" src="<?=DESIGN_HTTP?>/img/ico_arrow2.png" style="width:2rem">
                            `);
                                    $('#btnOpenCoupon .badg.sm').remove();
                                    $('#btnClearCoupon').remove();
                                }
                            });
                        });
                    }

                    // 5. 모달 닫기
                    $('#pop_coupon').modal('hide');
                },
                error: function(xhr, status, err){
                    console.log('[apply_coupon error]', status, err, xhr.responseText);
                    alert('서버 통신 오류가 발생했습니다.');
                }
            });
        });

        // ----------------------------
        // 쿠폰 해제
        // ----------------------------
        $('#btnClearCoupon').on('click', function(){
            console.log('[coupon clear] click');

            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                data: {
                    act: 'clear_coupon',
                    csrf_token: csrfToken
                },
                success: function(res){
                    console.log('[clear_coupon success]', res);

                    if(!res || !res.success){
                        ModalUtil.alert({
                            title: '알림',
                            message: (res && res.message) ? res.message : '쿠폰 해제에 실패했습니다.',
                            okText: '확인',
                            onOk: function () {
                            },
                        });
                        return;
                    }

                    applySummary(res.data);
                    // 페이지 내 “적용중” 뱃지/해제 버튼 노출까지 완벽히 하려면 새로고침이 가장 안전
                    // 퍼블리싱 변경 없이 처리하려면 reload 처리 권장
                    location.replace(location.href);
                },
                error: function(xhr, status, err){
                    console.log('[clear_coupon error]', status, err, xhr.responseText);
                    alert('서버 통신 오류가 발생했습니다.');
                }
            });
        });

        let sessionMode = '<?=($_SESSION["order_mode"] ?? "") ?>';
        // ----------------------------
        // 결제하기 버튼 (결제 연동 전까지는 임시)
        // ----------------------------
        function requestPortonePayment(paymentData, mode) {
            console.log('[PortOne] Request payment', paymentData);

            const {
                payment_id,
                merchant_uid,
                order_name,
                amount,
                buyer_name,
                buyer_tel,
                buyer_email
            } = paymentData;

            // customer 객체 기본 구성
            const customer = {
                fullName: (buyer_name || "고객").trim(),
                phoneNumber: (buyer_tel || "").trim()
            };

            // 이메일은 유효한 경우에만 추가 (키 자체를 넣지 않음)
            const email = (buyer_email || "guest@qrorder.com").trim();
            if (email && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                customer.email = email || "guest@qrorder.com";
            }

            if (typeof PortOne === "undefined") {
                ModalUtil.alert({
                    title: '알림',
                    message: '결제 모듈 로딩 실패',
                    okText: '확인',
                    onOk: function () {
                    },
                });
                return;
            }

            PortOne.requestPayment({
                storeId: '<?=PORTONE_STORE_ID?>',
                channelKey: '<?=PORTONE_CHANNEL_KEY?>',
                paymentId: payment_id,
                orderName: order_name,
                totalAmount: amount,
                currency: "CURRENCY_KRW",
                payMethod: "CARD",
                customer,  // ← 여기서 email 키가 없으면 문제없이 동작
                redirectUrl: '<?=CALLBACK_PAGE?>/portone_redirect.php',
                appScheme: "portone",
                locale: "KO_KR",
                windowType: {
                    pc: "IFRAME",
                    mobile: "REDIRECTION"
                }
            })
                .then(function(response){
                    if (response.code != null) {
                        ModalUtil.alert({
                            title: '알림',
                            message: '결제 실패: ' + (response.message || ''),
                            okText: '확인',
                            onOk: function () {
                            },
                        });
                        return;
                    }
                    verifyPayment(response.paymentId, merchant_uid, mode);
                })
                .catch(function(err){
                    console.log('[PortOne] Error', err);
                    alert(err.message || '결제 오류');
                });
        }

        $('#btnPaid').on('click', function (){
            let payload = {
                act: 'pay_postpaid',
                csrf_token: csrfToken,
            };

            $('#btnPay').prop('disabled', true).html('<span class="spinner-border spinner-border-sm mr-2"></span>주문 처리중...');

            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                data: payload,
                success: function(res) {
                    console.log('[pay_postpaid] success', res);

                    if (!res || !res.success) {
                        ModalUtil.alert({
                            title: '알림',
                            message: res.message || '주문 처리에 실패했습니다.',
                            okText: '확인',
                            onOk: function () {
                            },
                        });
                        $('#btnPay').prop('disabled', false).html('주문하기');
                        return;
                    }

                    // 후결제 완료 페이지로 이동 (필요 시 별도 페이지 생성)
                    // 예: order_postpaid_cmp.php 또는 order_cmp.php 재사용
                    const otNumber = res.data.ot_number;
                    location.href = './order_cmp.php?ot_number=' + encodeURIComponent(otNumber) + '&postpaid=1';
                },
                error: function(xhr, status, err) {
                    console.error('[pay_postpaid] error', status, err, xhr.responseText);
                    alert('서버 통신 오류가 발생했습니다.');
                    $('#btnPay').prop('disabled', false).html('주문하기');
                }
            });
        })

        $('#btnPaidRes').on('click', function (){
            let payload = {
                act: 'pay_postpaid_reservation',
                csrf_token: csrfToken,
            };

            $('#btnPay').prop('disabled', true).html('<span class="spinner-border spinner-border-sm mr-2"></span>주문 처리중...');

            payload.rv_name = '<?= $_SESSION["reservation_form"]["rv_name"] ?? "" ?>';
            payload.rv_hp = '<?= $_SESSION["reservation_form"]["rv_hp"] ?? "" ?>';
            payload.rv_date = '<?= $_SESSION["reservation_form"]["rv_date"] ?? "" ?>';
            payload.rv_time = '<?= $_SESSION["reservation_form"]["rv_time"] ?? "" ?>';
            payload.rv_people = '<?= $_SESSION["reservation_form"]["rv_people"] ?? 1 ?>';
            payload.rv_memo = '<?= $_SESSION["reservation_form"]["rv_memo"] ?? "" ?>';

            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                data: payload,
                success: function(res) {
                    console.log('[pay_postpaid] success', res);

                    if (!res || !res.success) {
                        ModalUtil.alert({
                            title: '알림',
                            message: res.message || '주문 처리에 실패했습니다.',
                            okText: '확인',
                            onOk: function () {
                            },
                        });
                        $('#btnPay').prop('disabled', false).html('주문하기');
                        return;
                    }

                    // 후결제 완료 페이지로 이동 (필요 시 별도 페이지 생성)
                    // 예: order_postpaid_cmp.php 또는 order_cmp.php 재사용
                    const otNumber = res.data.ot_number;
                    location.href = '../rsrv/rsrv_cmp.php?ot_number=' + encodeURIComponent(otNumber);
                },
                error: function(xhr, status, err) {
                    console.error('[pay_postpaid] error', status, err, xhr.responseText);
                    alert('서버 통신 오류가 발생했습니다.');
                    $('#btnPay').prop('disabled', false).html('주문하기');
                }
            });
        })

        $('#btnPay').on('click', function(){
            console.log('[Payment] Pay button clicked');

            // 결제 준비 API 호출
            let payload = {
                act: 'prepare_payment',
                csrf_token: csrfToken
            };

            // 주문 모드에 따라 act 변경
            if (sessionMode === 'takeout') {
                payload.act = 'prepare_payment_takeout';
            } else if (sessionMode === 'reservation') {
                payload.act = 'prepare_payment_reservation';

                // 예약 정보 추가
                payload.rv_name = '<?= $_SESSION["reservation_form"]["rv_name"] ?? "" ?>';
                payload.rv_hp = '<?= $_SESSION["reservation_form"]["rv_hp"] ?? "" ?>';
                payload.rv_date = '<?= $_SESSION["reservation_form"]["rv_date"] ?? "" ?>';
                payload.rv_time = '<?= $_SESSION["reservation_form"]["rv_time"] ?? "" ?>';
                payload.rv_people = '<?= $_SESSION["reservation_form"]["rv_people"] ?? 1 ?>';
                payload.rv_memo = '<?= $_SESSION["reservation_form"]["rv_memo"] ?? "" ?>';
            }

            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                data: payload,
                beforeSend: function() {
                    $('#btnPay').prop('disabled', true).html('<span class="spinner-border spinner-border-sm mr-2"></span>결제 준비중...');
                },
                success: function(res){
                    console.log('[Payment] Prepare success', res);

                    if(!res || !res.success){
                        ModalUtil.alert({
                            title: '알림',
                            message: res.message || '결제 준비 실패',
                            okText: '확인',
                            onOk: function () {
                            },
                        });
                        $('#btnPay').prop('disabled', false).html('<span id="btnPayPrice"><?=number_format($finalPrice)?>원</span> 결제하기');
                        return;
                    }

                    // 포트원 결제창 호출
                    requestPortonePayment(res.data, sessionMode);
                },
                error: function(xhr, status, e){
                    console.error('[Payment] Prepare error', status, e, xhr.responseText);
                    alert('서버 통신 오류가 발생했습니다.');
                    $('#btnPay').prop('disabled', false).html('<span id="btnPayPrice"><?=number_format($finalPrice)?>원</span> 결제하기');
                }
            });
        });

        // ===========================================
        // 포트원 결제창 호출
        // ===========================================
        function requestPortonePayment(paymentData, mode) {
            console.log('[PortOne] Request payment', paymentData);

            const {
                payment_id,
                merchant_uid,
                order_name,
                amount,
                buyer_name,
                buyer_tel,
                buyer_email
            } = paymentData;

            // 포트원 V2 SDK 호출
            PortOne.requestPayment({
                storeId: '<?=PORTONE_STORE_ID?>',
                channelKey: '<?=PORTONE_CHANNEL_KEY?>',
                paymentId: payment_id,
                orderName: order_name,
                totalAmount: amount,
                currency: "CURRENCY_KRW",
                payMethod: "CARD",
                customer: {
                    fullName: buyer_name || "고객",
                    phoneNumber: buyer_tel || "010-0000-0000",
                    email: buyer_email || "guest@qrorder.com"
                },
                redirectUrl: "<?=CALLBACK_PAGE?>/portone_redirect.php",
                appScheme: "portone",
                // 추가 옵션
                locale: "KO_KR",
                windowType: {
                    pc: "IFRAME",
                    mobile: "REDIRECTION"
                }
            }).then(function(response) {
                console.log('[PortOne] Response', response);

                // 에러 처리
                if (response.code != null) {
                    ModalUtil.alert({
                        title: '알림',
                        message: '결제에 실패했습니다.\n' + (response.message || '알 수 없는 오류'),
                        okText: '확인',
                        onOk: function () {
                        },
                    });
                    $('#btnPay').prop('disabled', false).html('<span id="btnPayPrice"><?=number_format($finalPrice)?>원</span> 결제하기');
                    return;
                }

                // 결제 성공 - 서버 검증
                verifyPayment(response.paymentId, merchant_uid, mode);

            }).catch(function(error) {
                console.error('[PortOne] Error', error);
                alert('결제 중 오류가 발생했습니다.\n' + (error.message || '알 수 없는 오류'));
                $('#btnPay').prop('disabled', false).html('<span id="btnPayPrice"><?=number_format($finalPrice)?>원</span> 결제하기');
            });
        }

        // ===========================================
        // 결제 검증
        // ===========================================
        function verifyPayment(paymentId, merchantUid, mode) {
            console.log('[Payment] Verify', { paymentId, merchantUid, mode });

            let verifyPayload = {
                act: 'verify_payment',
                payment_id: paymentId,
                merchant_uid: merchantUid,
                csrf_token: csrfToken
            };

            // 주문 모드에 따라 act 변경
            if (mode === 'takeout') {
                verifyPayload.act = 'verify_payment_takeout';
            } else if (mode === 'reservation') {
                verifyPayload.act = 'verify_payment_reservation';

                // 예약 정보 재전송
                verifyPayload.rv_name = '<?= $_SESSION["reservation_form"]["rv_name"] ?? "" ?>';
                verifyPayload.rv_hp = '<?= $_SESSION["reservation_form"]["rv_hp"] ?? "" ?>';
                verifyPayload.rv_date = '<?= $_SESSION["reservation_form"]["rv_date"] ?? "" ?>';
                verifyPayload.rv_time = '<?= $_SESSION["reservation_form"]["rv_time"] ?? "" ?>';
                verifyPayload.rv_people = '<?= $_SESSION["reservation_form"]["rv_people"] ?? 1 ?>';
                verifyPayload.rv_memo = '<?= $_SESSION["reservation_form"]["rv_memo"] ?? "" ?>';
            }

            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                data: verifyPayload,
                success: function(res){
                    console.log('[Payment] Verify success', res);

                    if(!res || !res.success){
                        ModalUtil.alert({
                            title: '알림',
                            message: res.message || '결제 검증 실패',
                            okText: '확인',
                            onOk: function () {
                            },
                        });
                        $('#btnPay').prop('disabled', false).html('<span id="btnPayPrice"><?=number_format($finalPrice)?>원</span> 결제하기');
                        return;
                    }

                    // 결제 완료 페이지로 이동
                    const otNumber = res.data.ot_number;

                    if(mode !== 'takeout' && mode !== 'reservation'){
                        // QR 테이블 주문
                        location.href = './order_cmp.php?ot_number=' + encodeURIComponent(otNumber);
                    } else {
                        // 포장/예약 주문
                        location.href = '../rsrv/rsrv_cmp.php?ot_number=' + encodeURIComponent(otNumber);
                    }
                },
                error: function(xhr, status, e){
                    console.error('[Payment] Verify error', status, e, xhr.responseText);
                    alert('결제 검증 중 오류가 발생했습니다.');
                    $('#btnPay').prop('disabled', false).html('<span id="btnPayPrice"><?=number_format($finalPrice)?>원</span> 결제하기');
                }
            });
        }

        //$('#btnPay').on('click', function(){
        //    console.log('[pay] click');
        //
        //    let sessionMode = '<?php //= ($_SESSION["order_mode"] ?? "") ?>//';
        //
        //    let payload = {
        //        act: '',
        //        csrf_token: csrfToken
        //    };
        //
        //    if (sessionMode === 'takeout') {
        //        payload.act = 'pay_takeout';        // 포장 주문
        //    } else if (sessionMode === 'reservation') {
        //        payload.act = 'pay_reservation';    // 예약 선결제
        //    } else {
        //        payload.act = 'pay';                // QR 테이블 주문 (기본)
        //    }
        //
        //    $.ajax({
        //        url: url,
        //        type: 'POST',
        //        dataType: 'json',
        //        data: payload,
        //        success: function(res){
        //            console.log('[pay success]', res);
        //            if(!res || !res.success){ alert(res.message||'결제 실패'); return; }
        //            if(sessionMode !== 'takeout' && sessionMode !== 'reservation'){
        //                // ✅ 주문번호 전달해서 완료페이지에서 조회
        //                location.href = './order_cmp.php?ot_number=' + encodeURIComponent(res.data.ot_number);
        //            }else{
        //                location.href = '../rsrv/rsrv_cmp.php?ot_number=' + encodeURIComponent(res.data.ot_number);
        //            }
        //        },
        //        error: function(xhr, status, e){
        //            console.log('[pay error]', status, e, xhr.responseText);
        //            alert('서버 통신 오류가 발생했습니다.');
        //        }
        //    });
        //});

        // ----------------------------
        // 결제정보 영역 갱신
        // ----------------------------
        function applySummary(d){
            console.log('[applySummary]', d);

            let goodsTxt = Number(d.total_price || 0).toLocaleString('ko-KR') + '원';
            let discTxt  = '-'+ Number(d.discount || 0).toLocaleString('ko-KR') + '원';
            let finalTxt = Number(d.final_price || 0).toLocaleString('ko-KR') + '원';

            $('#sumGoods').text(goodsTxt);
            $('#sumCoupon').text(discTxt);
            $('#sumFinal').text(finalTxt);
            $('#btnPayPrice').text(finalTxt);

            // 쿠폰 버튼 우측 텍스트도 갱신
            if((d.discount || 0) > 0){
                $('#couponRightText').html('<span class="mr-2">- ' + Number(d.discount).toLocaleString('ko-KR') + ' 원</span><img class="flex-shrink-0" src="<?=DESIGN_HTTP?>/img/ico_arrow2.png" style="width:2rem">');
            }
        }
    });
</script>
