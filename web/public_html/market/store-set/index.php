<?
$_SUB_HEAD_TITLE = "매장관리";
$_GET['hd_pc'] = ' '; //PC hd  로그인시공백 로그아웃시 logout
$hd_num = 'store'; //왼쪽메뉴 active 땜시 만듬
$hd_left = ' '; //왼쪽메뉴 on 땜시 만듬
include_once("../inc/head.php");
include_once("../inc/header.php");
include_once("../inc/modal.php");
?>

    <!-- 왼쪽 메뉴-->
<? include_once("../inc/left_menu.php"); ?>

    <div class="sub_pg ">
        <div class="sub_wr">
            <div class="hd_tit2">
                <div class="flex-shrink-0 ml-auto">
                    <button type="button" class="btn btn-outline-light rounded-pill " onclick="location.href='../store' ">매장정보</button>
                    <button type="button" class="btn btn-outline-light rounded-pill ml-2" onclick="location.href='../store-time' ">운영시간</button>
                    <button type="button" class="btn btn-secondary rounded-pill ml-2" onclick="location.href='../store-set' ">기능설정</button>
                </div>
                <div class="d-flex align-items-end flex-wrap">
                    <h3 class="tit_st1 mr-5">매장관리</h3>
                </div>
            </div>

            <div class="store_box">

                <!-- =========================
                     1) 테이블 QR 주문
                ========================= -->
                <section class="card">
                    <div class="card-body">
                        <div>
                            <div class="d-flex justify-content-end">
                                <div class="custom-control custom-switch switch-outside" data-key="sh_qr_yn">
                                    <input type="checkbox"
                                           class="custom-control-input"
                                           id="customSwitch3_1"
                                           data-on="운영중"
                                           data-off="미운영">
                                    <span class="switch-state"></span>
                                    <label class="custom-control-label" for="customSwitch3_1"></label>
                                </div>
                            </div>

                            <div class="mt-5">
                                <p class=""><img src="<?=DESIGN_HTTP?>/market/img/qr_img.jpg" alt=" "></p>
                                <p class="tit_st2 mt-5">테이블 QR 주문</p>
                                <p class="tg_500 mt-1 mb-4">매장 내 테이블에서 QR로 주문합니다</p>
                            </div>
                        </div>

                        <!-- 결제방식 라디오 (QR주문 ON일 때만 의미) -->
                        <div class="btn-group btn-group-toggle btn_toggle_primary w-100 mt-5" data-toggle="buttons" id="qrPayTypeGroup">
                            <label class="btn btn-outline-light" data-value="PREPAY">
                                <input type="radio" name="qr_pay_type" id="option1" autocomplete="off"> 선결제
                            </label>
                            <label class="btn btn-outline-light" data-value="POSTPAY">
                                <input type="radio" name="qr_pay_type" id="option2" autocomplete="off"> 후불결제
                            </label>
                        </div>

                    </div>
                </section>

                <!-- =========================
                     2) 예약 기능
                ========================= -->
                <section class="card">
                    <div class="card-body">
                        <div>
                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-primary" onclick="location.href='../reserve-stngs' ">예약 설정</button>
                                <div class="custom-control custom-switch switch-outside" data-key="sh_reserve_yn">
                                    <input type="checkbox" class="custom-control-input" id="customSwitch3_2" data-on="운영중" data-off="미운영">
                                    <span class="switch-state"></span>
                                    <label class="custom-control-label" for="customSwitch3_2"></label>
                                </div>
                            </div>

                            <div class="mt-5">
                                <p class=""><img src="<?=DESIGN_HTTP?>/market/img/rev_img.jpg" alt=" "></p>
                                <p class="tit_st2 mt-5">예약 기능</p>
                                <p class="tg_500 mt-1 mb-4">고객이 앱으로 방문 예약할 수 있습니다.</p>
                                <p class="tg_500 mt-1 mb-4">24시간 이내 예약 확인이 되지않을 경우 접수가 자동 취소됩니다.</p>
                            </div>

                            <div class="btn-group btn-group-toggle btn_toggle_primary w-100 mt-5" data-toggle="buttons" id="rsPayTypeGroup">
                                <label class="btn btn-outline-light" data-value="PREPAY">
                                    <input type="radio" name="rs_pay_type" id="option3" autocomplete="off"> 선결제
                                </label>
                                <label class="btn btn-outline-light" data-value="POSTPAY">
                                    <input type="radio" name="rs_pay_type" id="option4" autocomplete="off"> 후불결제
                                </label>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- =========================
                     3) 포장 주문
                ========================= -->
                <section class="card">
                    <div class="card-body">
                        <div>
                            <div class="d-flex justify-content-end">
                                <div class="custom-control custom-switch switch-outside" data-key="sh_takeout_yn">
                                    <input type="checkbox" class="custom-control-input" id="customSwitch3_3" data-on="운영중" data-off="미운영">
                                    <span class="switch-state"></span>
                                    <label class="custom-control-label" for="customSwitch3_3"></label>
                                </div>
                            </div>

                            <div class="mt-5">
                                <p class=""><img src="<?=DESIGN_HTTP?>/market/img/pack_img.jpg" alt=" "></p>
                                <p class="tit_st2 mt-5">포장 주문</p>
                                <p class="tg_500 mt-1 mb-4">고객이 앱으로 포장 주문을 할 수 있는 기능입니다.</p>
                                <p class="tg_500 mt-1 mb-4">고객편의를 위해 주문 주문 확인이 10분 이상 되지 않을 경우 자동 취소됩니다.</p>
                            </div>
                        </div>
                    </div>
                </section>

            </div>
        </div>
    </div>

<script>
    (function () {
        'use strict';
        if (!window.jQuery) return;
        const $ = window.jQuery;

        const API_URL = './update.php';

        const yn = (v) => (String(v || '').toUpperCase() === 'Y') ? 'Y' : 'N';

        function setSwitchUI($wrap, on) {
            const $state = $wrap.find('.switch-state').first();
            const onTxt = $wrap.find('input[type="checkbox"]').data('on') || 'ON';
            const offTxt = $wrap.find('input[type="checkbox"]').data('off') || 'OFF';

            if (!$state.length) return;

            if (on) {
                $state.addClass('is-on').text(onTxt);
            } else {
                $state.removeClass('is-on').text(offTxt);
            }
        }

        function setPayTypeUI(groupSelector, payType) {
            const v = (payType === 'PREPAY') ? 'PREPAY' : 'POSTPAY';
            const $group = $(groupSelector);

            $group.find('label').removeClass('active');
            $group.find('input[type="radio"]').prop('checked', false);

            $group.find('label[data-value="' + v + '"]')
                .addClass('active')
                .find('input[type="radio"]')
                .prop('checked', true);
        }

        function togglePayTypeDisabled(groupSelector, enabled) {
            const $group = $(groupSelector);

            $group.toggleClass('disabled', !enabled);
            $group.find('label').toggleClass('disabled', !enabled);
            $group.find('input[type="radio"]').prop('disabled', !enabled);
        }

        function applySettings(data) {
            const d = data || {};

            // 1) QR 주문
            const qrOn = yn(d.sh_qr_yn) === 'Y';
            $('#customSwitch3_1').prop('checked', qrOn);
            setSwitchUI($('#customSwitch3_1').closest('.switch-outside'), qrOn);
            setPayTypeUI('#qrPayTypeGroup', d.sh_qr_pay_type === 'PREPAY' ? 'PREPAY' : 'POSTPAY');
            togglePayTypeDisabled('#qrPayTypeGroup', qrOn);

            // 2) 예약
            const reserveOn = yn(d.sh_reserve_yn) === 'Y';
            $('#customSwitch3_2').prop('checked', reserveOn);
            setSwitchUI($('#customSwitch3_2').closest('.switch-outside'), reserveOn);
            setPayTypeUI('#rsPayTypeGroup', d.sh_reserve_pay_type === 'PREPAY' ? 'PREPAY' : 'POSTPAY');
            togglePayTypeDisabled('#rsPayTypeGroup', reserveOn);

            // 3) 포장
            const takeoutOn = yn(d.sh_takeout_yn) === 'Y';
            $('#customSwitch3_3').prop('checked', takeoutOn);
            setSwitchUI($('#customSwitch3_3').closest('.switch-outside'), takeoutOn);
        }

        function loadSetting() {
            $.ajax({
                url: API_URL,
                type: 'POST',
                dataType: 'json',
                data: { act: 'store_set_get' },
                success: function(res){
                    if (!res || !res.success) {
                        alert((res && res.message) ? res.message : '설정 값을 불러오지 못했습니다.');
                        return;
                    }

                    applySettings(res.data || {});
                },
                error: function(xhr){
                    console.log(xhr.responseText);
                    alert('서버 통신 오류');
                }
            });
        }

        let saving = false;
        let pendingPayload = null;

        function saveSetting(payload, options) {
            options = options || {};

            if (options.confirmMessage) {
                ModalUtil.confirm({
                    title: '알림',
                    message: options.confirmMessage,
                    okText: '확인',
                    cancelText: '취소',
                    onOk: function () {
                        return true;
                    },
                    onCancel: function (){
                        return false;
                    }
                });
            }

            if (typeof options.onBeforeSave === 'function') {
                options.onBeforeSave();
            }

            pendingPayload = Object.assign({}, pendingPayload || {}, payload);

            if (saving) return;
            saving = true;

            const send = function(){
                const requestPayload = Object.assign({ act: 'store_set_update' }, pendingPayload || {});
                pendingPayload = null;


                $.ajax({
                    url: API_URL,
                    type: 'POST',
                    dataType: 'json',
                    data: requestPayload,
                    success: function(res){
                        if (!res || !res.success) {
                            ModalUtil.alert({
                                title: '알림',
                                message: (res && res.message) ? res.message : '설정 저장에 실패했습니다.',
                                okText: '확인',
                                onOk: function () {}
                            });
                            loadSetting();
                            return;
                        }

                        applySettings(res.data || {});
                    },
                    error: function(xhr){
                        console.log(xhr.responseText);
                        alert('서버 통신 오류');
                        loadSetting();
                    },
                    complete: function(){
                        saving = false;
                        if (pendingPayload) {
                            saving = true;
                            send();
                        }
                    }
                });
            };

            send();
        }

        function bindEvents() {
            // 스위치 공통
            $(document).on('change', '.switch-outside input[type="checkbox"]', function(){
                const $chk = $(this);
                const $wrap = $chk.closest('.switch-outside');
                const key = $wrap.data('key');
                if (!key) return;

                const on = $chk.is(':checked');
                setSwitchUI($wrap, on);

                const payload = {};
                payload[key] = on ? 'Y' : 'N';

                if (key === 'sh_qr_yn') {
                    togglePayTypeDisabled('#qrPayTypeGroup', on);
                }

                if (key === 'sh_reserve_yn') {
                    togglePayTypeDisabled('#rsPayTypeGroup', on);
                }

                saveSetting(payload);
            });

            // QR 결제방식
            $(document).on('click', '#qrPayTypeGroup label', function(e){
                if (!$('#customSwitch3_1').is(':checked')) {
                    e.preventDefault();
                    return;
                }

                const payType = $(this).data('value') === 'PREPAY' ? 'PREPAY' : 'POSTPAY';
                setPayTypeUI('#qrPayTypeGroup', payType);
                saveSetting({ sh_qr_pay_type: payType });
            });

            // 예약 결제방식
            $(document).on('click', '#rsPayTypeGroup label', function(e){
                e.preventDefault();

                if (!$('#customSwitch3_2').is(':checked')) {
                    return;
                }

                const payType = $(this).data('value') === 'PREPAY' ? 'PREPAY' : 'POSTPAY';
                const currentPayType = $('#rsPayTypeGroup label.active').data('value') === 'PREPAY' ? 'PREPAY' : 'POSTPAY';

                if (payType === currentPayType) {
                    return;
                }

                const options = {
                    onBeforeSave: function () {
                        setPayTypeUI('#rsPayTypeGroup', payType);
                    },
                    onCancel: function () {
                        setPayTypeUI('#rsPayTypeGroup', currentPayType);
                    }
                };

                if (payType === 'POSTPAY') {
                    options.confirmMessage = '예약 기능을 후불결제로 변경하면\n노쇼 위험이 발생할 수 있습니다.\n그래도 후불결제로 변경하시겠습니까?';
                }

                saveSetting({ sh_reserve_pay_type: payType }, options);
            });
        }

        $(function(){
            $('.switch-outside').each(function(){
                setSwitchUI($(this), $(this).find('input[type="checkbox"]').is(':checked'));
            });

            bindEvents();
            loadSetting();
        });

    })();
</script>

<? include_once("./inc/tail.php"); ?>
