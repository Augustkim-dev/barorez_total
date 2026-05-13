<div class="hd_m hd_trans align-items-center">
    <div class="">
        <button class="hd_btn btn2" type="button" onclick="history.back();">
            <img src="<?=DESIGN_HTTP?>/img/ic_back.png" alt="뒤로가기">
        </button>
    </div>
    <div class="fw_700 line1_text"><?=$item['sm_title']?></div>
    <div class="hd_btn"></div>
</div>

<div class="wrap">
    <form method="post" id="menuDetailForm">
        <input type="hidden" name="act" value="add_cart">
        <input type="hidden" name="sm_id" id="smId" value="<?=$sm_idx?>">

        <div class="sub_pg pb_lg pt-0">
            <div class="rect">
                <img class=" " src="<?=$img?>" alt="상품사진">
            </div>

            <div class="container pt-5 pb-5">
                <a href="<?= htmlspecialchars($reviewListUrl) ?>" class=" d-flex align-items-center justify-content-between pb-4">
                    <div class="d-flex align-items-center">
                        <span class="fs_18 mr-2" style="color:#ffb100;">★</span>
                        <span class="fs_17 fw_700"><?= $reviewAvgScore ?></span>
                        <span class="fs_15 tg_400 ml-2">리뷰 <?= number_format($reviewCount) ?>개</span>
                    </div>
                    <img src="<?= DESIGN_HTTP ?>/img/ico_arrow1.png" class="flex-shrink-0" style="width:2rem;" alt="리뷰 보기">
                </a>
                <h2 class="tit_st2"><?=$item['sm_title']?></h2>
                <p class="tg_400 mt-4"><?=$item['sm_contents']?></p>
                <div class="d-flex align-items-end justify-content-between mt-3">
                    <p class="fw_600">가격</p>
                    <p class="tit_st2"><?=number_format($item['sm_price'])?>원</p>
                </div>
            </div>

            <div class="bar"></div>

            <div class="container pt_20 pb_20">
                <div class="d-flex align-items-end justify-content-between">
                    <p class="fw_600">수량</p>
                    <div class="item_opt_counter">
                        <button type="button" class="btn item_opt_counter_btn" id="qtyMinus" <?= $isSoldOut ? 'disabled' : '' ?>>
                            <img src="<?=DESIGN_HTTP?>/img/ico_decrease.svg" alt="감소">
                        </button>
                        <input type="text" class="quantity" id="qtyInput" name="qty" value="1" readonly>
                        <button type="button" class="btn item_opt_counter_btn" id="qtyPlus" <?= $isSoldOut ? 'disabled' : '' ?>>
                            <img src="<?=DESIGN_HTTP?>/img/ico_increase.svg" alt="증가">
                        </button>
                    </div>
                </div>
            </div>

            <div class="bar"></div>

            <div class="item_op_wp">
                <?php if (!empty($optCategories)): ?>
                    <?php foreach ($optCategories as $oc):
                        $oc_idx = (int)$oc['idx'];
                        $required = (($oc['oc_check'] ?? 'Y') === 'Y');
                        $items = $optItemsByCategory[$oc_idx] ?? [];
                        ?>
                        <dl data-oc-idx="<?=$oc_idx?>" data-required="<?= $required ? 'Y' : 'N' ?>">
                            <dt class="tit_st3 mb-4">
                                <?=htmlspecialchars($oc['oc_title'] ?? '')?>
                                <?php if ($required): ?>
                                    <span class="text-primary fs_15 ml-2">필수</span>
                                <?php else: ?>
                                    <span class="tg_400 fs_15 ml-2">선택</span>
                                <?php endif; ?>
                            </dt>
                            <dd class="opt_checks_wp">
                                <?php foreach ($items as $om): ?>
                                    <div class="checks opt_checks">
                                        <label>
                                            <input type="checkbox"
                                                   name="opt[<?=$oc_idx?>][]"
                                                   value="<?= (int)$om['idx'] ?>"
                                                   data-price="<?= (int)($om['om_price'] ?? 0) ?>">
                                            <span class="ic_box"></span>
                                            <div class="chk_p">
                                                <p><?=htmlspecialchars($om['om_title'] ?? '')?></p>
                                            </div>
                                            <p class="fw_700 flex-shrink-0 item_opmm">
                                                <?=number_format((int)($om['om_price'] ?? 0))?>원
                                            </p>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </dd>
                        </dl>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="bottom_btn">
            <div class="form-row">
                <div class="col-12">
                    <?php
                    // 1. 휴무일 우선 체크 (모든 모드 공통)
                    if ($_TODAY_HOURS_TEXT === '휴무일') {
                        echo '<button type="button" class="btn btn-primary btn-block btn-lg" disabled>
                        <span>휴무일</span>
                      </button>';
                    }
                    // 2. QR 테이블 주문 모드 (휴무일 제외)
                    elseif ($isQr) {
                        echo '<button type="button" class="btn btn-primary btn-block btn-lg" id="btnAddCart" '.($isSoldOut ? 'disabled' : '').'>
                        <span id="totalPriceText">'.number_format((int)$item['sm_price']).'원</span> 담기
                      </button>';
                    }
                    // 3. 포장/예약 모드 (휴무일 제외)
                    else {
                        if (!empty($_SESSION['mng'])) {
                            // 로그인 상태 → 장바구니 버튼
                            echo '<button type="button" class="btn btn-primary btn-block btn-lg" id="btnAddCart" '.($isSoldOut ? 'disabled' : '').'>
                            <span id="totalPriceText">'.number_format((int)$item['sm_price']).'원</span> 담기
                          </button>';
                        } else {
                            // 비로그인 → 로그인 버튼
                            echo '<button type="button" class="btn btn-primary btn-block btn-lg" onclick="location.href=\'' . AUTH_PAGE . '/login.php\'">
                            <span>로그인</span>
                          </button>';
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </form>
</div>
<?php include_once("../inc/modal.php");?>
<!-- 상단 헤더 스크롤 효과 -->
<script>
    $(window).on("scroll", function() {
        if ($(this).scrollTop() > 0) {
            $(".hd_m").removeClass("hd_trans");
        } else {
            $(".hd_m").addClass("hd_trans");
        }
    });
</script>

<script>
    (function() {
        let BASE_PRICE = <?= (int)($item['sm_price'] ?? 0) ?>;

        let $qtyInput = $('#qtyInput');
        let $qtyMinus = $('#qtyMinus');
        let $qtyPlus  = $('#qtyPlus');
        let $totalPriceText = $('#totalPriceText');
        let $btnAddCart = $('#btnAddCart');
        let $form = $('#menuDetailForm');

        let API_URL = '<?= SHOP_ACTIONS ?>/update.php';

        // 총액 계산
        function calcTotal() {
            let qty = parseInt($qtyInput.val(), 10) || 1;

            let optSum = 0;
            $('input[name^="opt["]:checked').each(function() {
                optSum += parseInt($(this).data('price'), 10) || 0;
            });

            let total = (BASE_PRICE + optSum) * qty;
            $totalPriceText.text(total.toLocaleString('ko-KR') + '원');

            return { qty: qty, optSum: optSum, total: total };
        }

        // 수량 감소
        function decreaseQty() {
            let qty = parseInt($qtyInput.val(), 10) || 1;
            qty = Math.max(1, qty - 1);
            $qtyInput.val(qty);
            calcTotal();
        }

        // 수량 증가
        function increaseQty() {
            let qty = parseInt($qtyInput.val(), 10) || 1;
            qty += 1;
            $qtyInput.val(qty);
            calcTotal();
        }

        // 필수 옵션 체크
        function checkRequiredOptions() {
            let ok = true;
            $('dl[data-required="Y"]').each(function() {
                let checkedCount = $(this).find('input[type="checkbox"]:checked').length;
                if (checkedCount === 0) {
                    ok = false;
                    ModalUtil.alert({
                        title: '알림',
                        message: '필수 옵션을 선택해 주세요.',
                        okText: '확인',
                        onOk: function () {
                        },
                    });
                    $('html, body').animate({ scrollTop: $(this).offset().top - 80 }, 300);
                    return false;
                }
            });
            return ok;
        }

        // 장바구니 담기 요청
        function requestAddCart(forceClear) {
            let formData = $form.serialize();
            if (forceClear) {
                formData += '&force_clear=Y';
            }

            $.ajax({
                url: API_URL,
                type: 'POST',
                dataType: 'json',
                data: formData,
                success: function(res) {
                    if (res && res.needs_confirm) {
                        ModalUtil.confirm({
                            title: '메뉴',
                            message: res.message || '기존 장바구니를 삭제하고 진행할까요?',
                            okText: '확인',
                            cancelText: '취소',
                            onOk: function () {
                                requestAddCart(true);
                            },
                            onCancel: function (){
                                return false;
                            }
                        });
                        // if (confirm(res.message || '기존 장바구니를 삭제하고 진행할까요?')) {
                        //     requestAddCart(true);
                        // }
                        return;
                    }

                    if (res && res.success) {
                        location.replace(res.redirect || '../order/cart.php');
                        return;
                    }

                    alert(res?.message || '처리 중 오류가 발생했습니다.');
                },
                error: function(xhr, status, err) {
                    console.error('[add_cart error]', status, err, xhr.responseText);
                    alert('서버 통신 오류가 발생했습니다.');
                }
            });
        }

        // 초기화
        $(function() {
            // 수량 버튼
            $qtyMinus.on('click', decreaseQty);
            $qtyPlus.on('click', increaseQty);

            let isQr = '<?=$isQr?>';
            let mb = '<?=$_SESSION['mng']?>';
            // 옵션 변경 시 총액 갱신
            $(document).on('change', 'input[name^="opt["]', calcTotal);

            // 담기 버튼 클릭
            $btnAddCart.on('click', function() {
                if (!checkRequiredOptions()) return;

                if(!isQr && !mb){
                    ModalUtil.alert({
                        title: '알림',
                        message: '로그인 후 이용 가능합니다',
                        okText: '확인',
                        onOk: function () {
                        },
                    });
                    return;
                }

                requestAddCart(false);
            });

            // 초기 총액 계산
            calcTotal();
        });
    })();
</script>
