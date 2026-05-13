<?php $isTest = ((int)($_GET['test'] ?? 0) === 1);?>

<div class="wrap">
    <div class="sub_pg pb_lg">
        <?php if (empty($cartRows)) { ?>
            <div class="container py-5 text-center">
                <p class="tg_400">장바구니에 담긴 메뉴가 없습니다.</p>
                <?php if($_SESSION['current_sh_idx']):?>
                    <div class="mt-4">
                        <button type="button" class="btn btn-outline-primary btn-block btn_st1" onclick="location.href='<?=$menu_add?>'">
                            메뉴 보러가기 <img src="<?=DESIGN_HTTP?>/img/btn_deco.svg" alt=" " class="ml-3">
                        </button>
                    </div>
                <?php endif;?>
            </div>
        <?php } else { ?>

            <ul class="cart_list" id="cartList">
                <?php foreach ($cartRows as $row) {
                    $ct_idx = (int)$row['ct_idx'];
                    $qty    = max(1, (int)$row['ct_quantity']);
                    $img    = !empty($row['sm_image']) ? '/data/menu/'.$row['sm_image'] : (DESIGN_HTTP.'/img/pr_sample03.jpg');

                    $soldOut = false;
                    if (($row['sm_show'] ?? 'Y') !== 'Y') $soldOut = true;
                    if (($row['sm_type'] ?? 'Y') === 'N') $soldOut = true;

                    $optList = $optionsMap[$ct_idx] ?? [];
                    ?>
                    <li data-ct-idx="<?=$ct_idx?>">
                        <div class="item_box <?= $soldOut ? 'sold_out' : '' ?> ">

                            <a href="./shop/detail.php?id=<?=(int)$row['sm_id']?>">
                                <div class="item_img2 flex-shrink-0 <?= $soldOut ? 'rounded-sm overflow-hidden' : '' ?>">
                                    <?php if($soldOut){ ?>
                                        <p class="sold_out_txt">품절</p>
                                    <?php } ?>
                                    <div class="rect <?= $soldOut ? '' : 'rounded-sm' ?>">
                                        <img class=" " src="<?=htmlspecialchars($img)?>" alt="상품사진">
                                    </div>
                                </div>
                            </a>

                            <div class="w-100">
                                <div class="d-flex align-items-center justify-content-between">
                                    <p class="fw_500"><?=htmlspecialchars($row['sm_title'] ?? '')?></p>

                                    <p>
                                        <a href="javascript:void(0)"
                                           data-role="cart-delete"
                                           data-ct-idx="<?=$ct_idx?>">
                                            <img class=" " src="<?=DESIGN_HTTP?>/img/ico_x.png" alt="삭제" style="width:18px">
                                        </a>
                                    </p>
                                </div>

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

                            </div>
                        </div>

                        <div class="d-flex align-items-center flex-wrap mt_20">
                            <div class="item_opt_counter mr-3">
                                <button type="button"
                                        class="btn item_opt_counter_btn"
                                        data-role="qty-dec"
                                        data-ct-idx="<?=$ct_idx?>"
                                    <?=($qty <= 1 ? 'disabled' : '')?>>
                                    <img src="<?=DESIGN_HTTP?>/img/ico_decrease.svg" alt="감소">
                                </button>

                                <input type="txt"
                                       class="quantity"
                                       id="qty_<?=$ct_idx?>"
                                       value="<?=$qty?>"
                                       readonly>

                                <button type="button"
                                        class="btn item_opt_counter_btn"
                                        data-role="qty-inc"
                                        data-ct-idx="<?=$ct_idx?>">
                                    <img src="<?=DESIGN_HTTP?>/img/ico_increase.svg" alt="증가">
                                </button>
                            </div>

                            <button type="button"
                                    class="btn btn-outline-light btn-sm rounded-pill"
                                    data-toggle="modal"
                                    data-target="#pop_cart"
                                    data-role="opt-change"
                                    data-ct-idx="<?=$ct_idx?>">
                                옵션 변경
                            </button>

                            <p class="mt-3 fs_15 fw_700 ml-auto"
                               data-role="item-total"
                               data-ct-idx="<?=$ct_idx?>">
                                <?=number_format((int)$row['ct_total_price'])?>원
                            </p>
                        </div>
                    </li>
                <?php } ?>
            </ul>

            <?php
            if($isQr){
                $link = '../';
            }else{
                $link = '../shop/list.php?sh_idx='.$st_id;
            }

            ?>
            <div class="container my-3">
                <button type="button" class="btn btn-outline-primary btn-block btn_st1" onclick="location.href='<?=$link?>'">
                    메뉴 추가 <img src="<?=DESIGN_HTTP?>/img/btn_deco.svg" alt=" " class="ml-3">
                </button>
            </div>

            <div class="bar"></div>

            <div class="container mt-5">
                <dl class="">
                    <dt class="tit_st3 mb-4">결제정보</dt>
                    <dd class="d-flex align-items-center justify-content-between">
                        <p>결제 예정 금액</p>
                        <p class="fw_700 flex-shrink-0" id="cartTotalPrice"><?=number_format($totalPrice)?>원</p>
                    </dd>
                </dl>
            </div>

            <div class="bottom_btn">
                <div class="form-row">
                    <div class="col-12">
                        <?php if($_SESSION['order_mode'] === 'reservation'){?>
                            <button type="button"
                                    class="btn btn-primary btn-block btn-lg"
                                    onclick="location.href='../rsrv/rsrv.php'">
                                총 <span id="cartTotalQty"><?=$totalQty?></span>개
                                <span id="cartTotalPriceBtn"><?=number_format($totalPrice)?>원</span>
                                <span class="fw_100 mx-3">|</span> 예약하기
                            </button>
                        <?php }else{?>
                            <button type="button"
                                    class="btn btn-primary btn-block btn-lg"
                                    id="btnOrderSubmit"
                                <?php if(!$isTest){ ?>
                                    onclick="location.href='./order.php'"
                                <?php } ?>>
                                총 <span id="cartTotalQty"><?=$totalQty?></span>개
                                <span id="cartTotalPriceBtn"><?=number_format($totalPrice)?>원</span>
                                <span class="fw_100 mx-3">|</span> 주문하기
                            </button>
                        <?php } ?>
                    </div>
                </div>
            </div>

        <?php } // empty else ?>
    </div>
</div>

<div class="modal modal_bottom fade" id="pop_cart" tabindex="-1" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header ">
                <h5 class="modal-title" id="popCartTitle">(메뉴명)</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <img class=" " src="<?=DESIGN_HTTP?>/img/ico_x.png" alt="삭제" style="width:18px">
                </button>
            </div>

            <div class="modal-body pt-0">
                <div class="d-flex align-items-end justify-content-between border-bottom py_20">
                    <p class="fw_600">가격</p>
                    <p class="tit_st3" id="popCartUnitPrice">0원</p>
                </div>

                <div class="d-flex align-items-end justify-content-between border-bottom py_20">
                    <p class="fw_600">수량</p>
                    <div class="item_opt_counter">
                        <button type="button" class="btn item_opt_counter_btn" id="popQtyDec" disabled>
                            <img src="<?=DESIGN_HTTP?>/img/ico_decrease.svg" alt="감소">
                        </button>

                        <input type="txt" class="quantity" id="popQty" value="1" readonly>

                        <button type="button" class="btn item_opt_counter_btn" id="popQtyInc">
                            <img src="<?=DESIGN_HTTP?>/img/ico_increase.svg" alt="증가">
                        </button>
                    </div>
                </div>

                <div id="popCartOptionsArea"></div>

                <input type="hidden" id="popCtIdx" value="">
                <input type="hidden" id="popSmId" value="">
                <input type="hidden" id="csrfToken" value="<?=htmlspecialchars($_SESSION['csrf_token'] ?? '')?>">
            </div>

            <div class="modal-footer pt-3">
                <div class="form-row">
                    <div class="col-12">
                        <button type="button" class="btn btn-primary btn-block" id="btnPopApply">
                            <span id="popCartTotalPriceTxt">0원</span> <span class="fw_100 mx-3">|</span> 변경하기
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<?php include_once("../inc/modal.php");?>
<script>
    $(function(){
        console.log('[cart option modal] init');

        let url = '<?=ORDER_ACTIONS?>/cart.php';
        let csrfToken = $('#csrfToken').val() || '';

        $('#cartList').on('click', '[data-role="opt-change"]', function(){
            let ctIdx = parseInt($(this).data('ctIdx'), 10) || 0;

            $('#popCtIdx').val(ctIdx);

            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                data: {
                    act: 'get_modal',
                    ct_idx: ctIdx,
                    csrf_token: csrfToken
                },
                success: function(res){
                    if(!res || !res.success){
                        ModalUtil.alert({
                            title: '알림',
                            message: res?.message || '옵션 정보를 불러오지 못했습니다.',
                            okText: '확인',
                            onOk: function () {
                            },
                        });
                        $('#pop_cart').modal('hide');
                        return;
                    }

                    fillOptionModal(res.data);
                },
                error: function(xhr, status, err){
                    alert('서버 통신 오류가 발생했습니다.');
                    $('#pop_cart').modal('hide');
                }
            });
        });

        function fillOptionModal(data){
            window.POP_BASE_PRICE = Number(data.base_price || 0);

            $('#popSmId').val(data.sm_id || '');
            $('#popCartTitle').text(data.sm_title || '');
            $('#popQty').val(data.qty || 1);

            let unitTxt = Number(data.unit_price || 0).toLocaleString('ko-KR') + '원';
            let totalTxt = Number(data.total_price || 0).toLocaleString('ko-KR') + '원';
            $('#popCartUnitPrice').text(unitTxt);
            $('#popCartTotalPriceTxt').text(totalTxt);

            $('#popQtyDec').prop('disabled', (parseInt(data.qty,10) || 1) <= 1);

            let $area = $('#popCartOptionsArea');
            $area.empty();

            let cats = data.categories || [];
            let optionsByCat = data.options_by_cat || {};
            let chosen = data.chosen || {};

            cats.forEach(function(c){
                let ocIdx = parseInt(c.idx, 10) || 0;
                let ocSu  = parseInt(c.oc_su || 1);
                let title = c.oc_title || '옵션';
                let required = (c.oc_check === 'Y');

                let wrapId = 'pop_oc_wrap_' + ocIdx;
                let collapseId = 'pop_oc_collapse_' + ocIdx;

                let badge = required
                    ? '<span class="text-primary fs_15 ml-2">필수 (' + ocSu + '개)</span>'
                    : '<span class="tg_400 fs_15 ml-2">선택</span>';

                let html = '';
                html += '<div class="collapse_ex border-bottom py_20" ';
                html += 'data-oc-idx="' + ocIdx + '" ';
                html += 'data-required="' + (required?'Y':'N') + '" ';
                html += 'data-oc-su="' + ocSu + '" ';
                html += 'id="' + wrapId + '">';
                html += '  <ul><li>';
                html += '    <button type="button" class="btn d-flex p-0 justify-content-between w-100 h-auto collapsed" data-toggle="collapse" data-target="#' + collapseId + '" aria-expanded="false">';
                html += '      <div class="tit_st3 ">' + escapeHtml(title) + ' ' + badge + '</div>';
                html += '      <p><img src="<?=DESIGN_HTTP?>/img/ico_arrow.png" style="width:2.4rem;"></p>';
                html += '    </button>';
                html += '  </li></ul>';
                html += '  <div id="' + collapseId + '" class="collapse" data-parent="#' + wrapId + '">';
                html += '    <div class="opt_checks_wp mt-4">';

                let opts = optionsByCat[ocIdx] || [];
                let pickedList = chosen[ocIdx] || [];

                opts.forEach(function(o){
                    let omIdx = parseInt(o.idx, 10) || 0;
                    let omTitle = o.om_title || '';
                    let omPrice = parseInt(o.om_price, 10) || 0;

                    let inputType = 'checkbox';
                    let inputName = 'opt[' + ocIdx + '][]';

                    let checked = (pickedList.indexOf(omIdx) >= 0) ? 'checked' : '';
                    let priceTxt = (omPrice > 0) ? ('(+' + Number(omPrice).toLocaleString('ko-KR') + ')') : '';

                    html += '      <div class="checks opt_checks">';
                    html += '        <label>';
                    html += '          <input type="' + inputType + '" name="' + inputName + '" value="' + omIdx + '" data-price="' + omPrice + '" ' + checked + '>';
                    html += '          <span class="ic_box"></span>';
                    html += '          <div class="chk_p"><p>' + escapeHtml(omTitle) + '</p></div>';
                    html += '          <p class="fw_700 flex-shrink-0 item_opmm">' + priceTxt + '</p>';
                    html += '        </label>';
                    html += '      </div>';
                });

                html += '    </div>';
                html += '  </div>';
                html += '</div>';

                $area.append(html);
            });

            $area.off('change', 'input').on('change', 'input', function(){
                recalcModalPrice(data.base_price || 0);
            });

            recalcModalPrice(data.base_price || 0);
        }

        $('#popQtyInc').on('click', function(){
            let qty = parseInt($('#popQty').val(), 10) || 1;
            qty += 1;
            $('#popQty').val(qty);
            $('#popQtyDec').prop('disabled', qty <= 1);
            recalcModalPrice(window.POP_BASE_PRICE || 0);
        });

        $('#popQtyDec').on('click', function(){
            let qty = parseInt($('#popQty').val(), 10) || 1;
            if (qty <= 1) return;
            qty -= 1;
            $('#popQty').val(qty);
            $('#popQtyDec').prop('disabled', qty <= 1);
            recalcModalPrice(window.POP_BASE_PRICE || 0);
        });

        $('#btnPopApply').on('click', function(){
            let ctIdx = parseInt($('#popCtIdx').val(), 10) || 0;
            let qty   = parseInt($('#popQty').val(), 10) || 1;

            let ok = true;

            $('#popCartOptionsArea .collapse_ex[data-required="Y"]').each(function(){
                let $this = $(this);
                let ocIdx = $this.data('oc-idx');
                let reqSu = parseInt($this.data('oc-su') || 1);
                let checked = $this.find('input:checked').length;

                if (checked !== reqSu) {
                    ok = false;
                    ModalUtil.alert({
                        title: '알림',
                        message: `필수 옵션을 정확히 ${reqSu}개 선택해주세요. (현재: ${checked}개)`,
                        okText: '확인',
                        onOk: function () {
                        },
                    });
                    return false;
                }
            });

            if (!ok) return;

            let optData = $('#popCartOptionsArea').find('input').serialize();

            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                data: 'act=apply'
                    + '&ct_idx=' + encodeURIComponent(ctIdx)
                    + '&qty=' + encodeURIComponent(qty)
                    + '&csrf_token=' + encodeURIComponent(csrfToken)
                    + (optData ? ('&' + optData) : ''),
                success: function(res){
                    if(!res || !res.success){
                        ModalUtil.alert({
                            title: '알림',
                            message: res?.message || '옵션 변경에 실패했습니다.',
                            okText: '확인',
                            onOk: function () {
                            },
                        });
                        return false;
                    }

                    applyCartRowUI(res.data);
                    updateTotals(res.data.total_qty, res.data.total_price);

                    $('#pop_cart').modal('hide');
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();
                },
                error: function(xhr, status, err){
                    alert('서버 통신 오류가 발생했습니다.');
                }
            });
        });

        function applyCartRowUI(data){
            let ctIdx = parseInt(data.ct_idx, 10) || 0;

            $('#qty_' + ctIdx).val(data.qty || 1);

            let $li = $('li[data-ct-idx="'+ctIdx+'"]');
            let $decBtn = $li.find('[data-role="qty-dec"]');
            $decBtn.prop('disabled', (parseInt(data.qty,10) || 1) <= 1);

            let itemTotalTxt = Number(data.item_total || 0).toLocaleString('ko-KR') + '원';
            $('[data-role="item-total"][data-ct-idx="'+ctIdx+'"]').text(itemTotalTxt);

            let $ul = $li.find('ul.dot_list');
            $ul.empty();

            if(data.options && data.options.length){
                data.options.forEach(function(o){
                    let priceTxt = (o.price > 0) ? (' (+'+ Number(o.price).toLocaleString('ko-KR') +')') : '';
                    $ul.append('<li>옵션 : '+ escapeHtml(o.title || '') + priceTxt +'</li>');
                });
            } else {
                $ul.append('<li>선택 옵션 없음</li>');
            }
        }

        function recalcModalPrice(basePrice){
            basePrice = Number(basePrice || window.POP_BASE_PRICE || 0);

            let qty = parseInt($('#popQty').val(), 10) || 1;

            let optSum = 0;
            $('#popCartOptionsArea input:checked').each(function(){
                let p = parseInt($(this).data('price'), 10) || 0;
                optSum += p;
            });

            let unit = basePrice + optSum;
            let total = unit * qty;

            $('#popCartUnitPrice').text(Number(unit).toLocaleString('ko-KR') + '원');
            $('#popCartTotalPriceTxt').text(Number(total).toLocaleString('ko-KR') + '원');
        }

        function escapeHtml(str){
            return String(str || '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        function updateTotals(totalQty, totalPrice) {
            let priceTxt = Number(totalPrice || 0).toLocaleString('ko-KR') + '원';

            $('#cartTotalQty').text(totalQty || 0);
            $('#cartTotalPrice').text(priceTxt);
            $('#cartTotalPriceBtn').text(priceTxt);
        }

        $('#cartList').on('click', '[data-role="qty-inc"]', function(){
            let ctIdx = parseInt($(this).data('ctIdx'), 10) || 0;
            let $input = $('#qty_' + ctIdx);
            let qty = parseInt($input.val(), 10) || 1;
            qty += 1;
            $input.val(qty);

            $(this).closest('.item_opt_counter').find('[data-role="qty-dec"]').prop('disabled', false);

            updateCartItem(ctIdx, qty);
        });

        $('#cartList').on('click', '[data-role="qty-dec"]', function(){
            let ctIdx = parseInt($(this).data('ctIdx'), 10) || 0;
            let $input = $('#qty_' + ctIdx);
            let qty = parseInt($input.val(), 10) || 1;
            if(qty <= 1) return;

            qty -= 1;
            $input.val(qty);

            if(qty <= 1){
                $(this).prop('disabled', true);
            }

            updateCartItem(ctIdx, qty);
        });

        $('#cartList').on('click', '[data-role="cart-delete"]', function(){
            let ctIdx = parseInt($(this).data('ctIdx'), 10) || 0;

            ModalUtil.confirm({
                title: '메뉴',
                message: '장바구니에서 삭제하시겠습니까?',
                okText: '확인',
                cancelText: '취소',
                onOk: function () {
                    $.ajax({
                        url: url,
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            act: 'delete',
                            ct_idx: ctIdx,
                            csrf_token: csrfToken
                        },
                        success: function(res){
                            if(!res || !res.success){
                                ModalUtil.alert({
                                    title: '알림',
                                    message: res?.message || '삭제 실패했습니다.',
                                    okText: '확인',
                                    onOk: function () {
                                    },
                                });
                                return false;
                            }

                            $('li[data-ct-idx="'+ctIdx+'"]').remove();

                            updateTotals(res.data.total_qty, res.data.total_price);

                            if(res.data.total_qty <= 0){
                                location.reload();
                            }
                        },
                        error: function(){
                            alert('서버 통신 오류');
                        }
                    });
                },
                onCancel: function (){
                    return false;
                }
            });
        });

        function updateCartItem(ctIdx, qty){
            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                data: {
                    act: 'update_qty',
                    ct_idx: ctIdx,
                    qty: qty,
                    csrf_token: csrfToken
                },
                success: function(res){
                    if(!res || !res.success){
                        ModalUtil.alert({
                            title: '알림',
                            message: res?.message || '수량 변경 실패',
                            okText: '확인',
                            onOk: function () {
                            },
                        });
                        return false;
                    }

                    let itemTotalTxt = Number(res.data.item_total || 0).toLocaleString('ko-KR') + '원';
                    $('[data-role="item-total"][data-ct-idx="'+ctIdx+'"]').text(itemTotalTxt);

                    updateTotals(res.data.total_qty, res.data.total_price);
                },
                error: function(){
                    alert('서버 통신 오류');
                }
            });
        }

        let isTest = <?= $isTest ? 'true' : 'false' ?>;
        let isQrOrder = <?= !empty($_SESSION['qr_token']) ? 'true' : 'false' ?>;

        if(isTest){
            console.log('[test mode] enabled - postpaid flow');

            $('#btnOrderSubmit').on('click', function(){
                console.log('[postpaid click] start');

                if(!isQrOrder){
                    ModalUtil.alert({
                        title: '알림',
                        message: 'QR 주문(테이블) 환경이 아닙니다.',
                        okText: '확인',
                        onOk: function () {
                        },
                    });
                    return false;
                }

                let $btn = $(this);
                $btn.prop('disabled', true);

                $.ajax({
                    url: '<?=ORDER_ACTIONS?>/order.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        act: 'pay_postpaid',
                        csrf_token: csrfToken
                    },
                    success: function(res){
                        console.log('[pay_postpaid success]', res);

                        if(!res || !res.success){
                            ModalUtil.alert({
                                title: '알림',
                                message: res?.message || '후결제 주문 생성에 실패했습니다.',
                                okText: '확인',
                                onOk: function () {
                                },
                            });
                            $btn.prop('disabled', false);
                            return;
                        }

                        if(res.data && res.data.visit_id){
                            location.replace('../order/order_guest.php?tv_idx=' + res.data.visit_id);
                        }else{
                            location.reload();
                        }
                    },
                    error: function(xhr, status, err){
                        console.log('[pay_postpaid error]', status, err, xhr.responseText);
                        alert('서버 통신 오류가 발생했습니다.');
                        $btn.prop('disabled', false);
                    }
                });
            });
        }
    });
</script>
