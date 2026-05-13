<div class="wrap">
    <div class="sub_pg pb_lg">
        <div class="container">
            <form id="findIdForm">
                <div class="form_wr mt-5 ip_valid">
                    <div class="ip_tit required">
                        <h5>아이디</h5>
                    </div>
                    <div class="form-row">
                        <div class="col">
                            <input type="text" class="form-control" id="findId" placeholder="아이디 입력">
                        </div>
                    </div>
                    <div class="form-text ip_invalid" id="findIdError">아이디를 입력하세요</div>
                </div>
                <div class="form_wr mt-5 ip_valid">
                    <div class="ip_tit required">
                        <h5>휴대폰번호</h5>
                    </div>
                    <div class="form-row">
                        <div class="col">
                            <input type="text" class="form-control" id="findIdHp" placeholder="‘-’ 없이 숫자만 입력">
                        </div>
                        <div class="col-3">
                            <button type="button" class="btn btn-outline-primary btn-block" id="btnFindIdHpReq" >인증요청</button>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col mt-3 position-relative">
                            <p class="time_lim" id="findIdTimer">05:00</p>
                            <input type="text" class="form-control" id="findIdHpCode" placeholder="인증번호 입력">
                        </div>
                        <div class="col-3 mt-3">
                            <button type="button" class="btn btn-primary btn-block" id="btnFindIdHpConfirm" disabled>확인</button>
                        </div>
                    </div>
                    <div class="form-text ip_invalid" id="findIdHpError">오류 텍스트</div>
                </div>

                <div class="bottom_btn">
                    <div class="form-row">
                        <div class="col-12">
                            <button type="button" class="btn btn-primary btn-block btn-lg" id="btnFindIdSubmit">
                                비밀번호 찾기
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include_once("../inc/modal.php");?>
<script src="<?=CDN_UTIL_URL?>/timer.js"></script>
<script src="<?=CDN_UTIL_URL?>/msg_err.js"></script>
<script src="<?=CDN_UTIL_URL?>/validation.js"></script>
<script>
    $(function () {
        // 요소 캐싱
        const $form         = $('#findIdForm');
        const $idInput      = $('#findId');
        const $idWrap       = $idInput.closest('.form_wr');
        const $idError      = $('#findIdError');

        const $hpInput      = $('#findIdHp');
        const $hpWrap       = $hpInput.closest('.form_wr');
        const $hpError      = $('#findIdHpError');

        const $codeInput    = $('#findIdHpCode');
        const $timerEl      = $('#findIdTimer');

        const $btnHpReq     = $('#btnFindIdHpReq');
        const $btnHpConfirm = $('#btnFindIdHpConfirm');
        const $btnSubmit    = $('#btnFindIdSubmit');

        let hpConfirmed = false;

        // 타이머 인스턴스
        const timer = AuthTimerFactory($timerEl, () => {
            ModalUtil.alert({
                title: '패스워드 찾기',
                message: '인증 유효시간이 만료되었습니다. 다시 인증요청 해주세요.',
                okText: '확인',
                onOk: function () {
                },
            });

            hpConfirmed = false;
            $btnHpConfirm.prop('disabled', true);
            $codeInput.val('').prop('disabled', true);
            $btnHpReq.prop('disabled', false).text('인증요청');
        });

        // 초기 상태: 중립
        resetFieldState($idWrap, $idError);
        resetFieldState($hpWrap, $hpError);

        // 휴대폰 입력: 숫자만 허용
        $hpInput.on('input', function () {
            let cleaned = $(this).val().replace(/[^0-9]/g, '');
            $(this).val(cleaned);
        });

        // ===============================
        // 1) 휴대폰 인증 요청
        // ===============================
        $btnHpReq.on('click', function () {
            const hpVal = $hpInput.val();
            const hpResult = ValidationUtils.validateHp(hpVal);

            if (!hpResult.valid) {
                showError($hpWrap, $hpError, hpResult.msg);
                $hpInput.focus();
                return;
            }

            $.ajax({
                url: '<?=AUTH_ACTIONS?>/find_update.php',
                type: 'POST',
                dataType: 'json',
                data: { act: 'chk_mt_hp', mt_hp: hpVal },
                beforeSend: () => $btnHpReq.prop('disabled', true),
                success: function (res) {
                    if (res?.success) {
                        ModalUtil.alert({
                            title: '패스워드 찾기',
                            message: (res.message || '인증번호가 발송되었습니다.'),
                            okText: '확인',
                            onOk: function () {
                            },
                        });

                        hpConfirmed = false;
                        $btnHpReq.text('재요청');
                        $codeInput.val('').prop('disabled', false).focus();
                        $btnHpConfirm.prop('disabled', false);

                        timer.start(300);

                        // if (res.auth_code) $codeInput.val(res.auth_code);
                    } else {
                        showError($hpWrap, $hpError, res?.message || '인증 요청에 실패했습니다.');
                        ModalUtil.alert({
                            title: '패스워드 찾기',
                            message: res?.message || '인증요청 중 오류가 발생했습니다.',
                            okText: '확인',
                            onOk: function () {
                            },
                        });
                    }
                },
                error: function () {
                    showError($hpWrap, $hpError, '인증요청 중 오류가 발생했습니다.');
                    ModalUtil.alert({
                        title: '패스워드 찾기',
                        message: '인증요청 중 오류가 발생했습니다.',
                        okText: '확인',
                        onOk: function () {
                        },
                    });
                },
                complete: () => $btnHpReq.prop('disabled', false)
            });
        });

        // ===============================
        // 2) 인증번호 확인
        // ===============================
        $btnHpConfirm.on('click', function () {
            const hp = $hpInput.val();
            const code = $.trim($codeInput.val());

            if (!code) {
                showError($hpWrap, $hpError, '인증번호를 입력해 주세요.');
                $codeInput.focus();
                return;
            }

            if (!/^[0-9]{4,6}$/.test(code)) {
                showError($hpWrap, $hpError, '인증번호는 4~6자리 숫자입니다.');
                $codeInput.focus();
                return;
            }

            $.ajax({
                url: '<?=AUTH_ACTIONS?>/join_update.php',
                type: 'POST',
                dataType: 'json',
                data: { act: 'confirm_mt_hp', mt_hp: hp, mt_hp_confirm: code },
                beforeSend: () => {
                    $btnHpReq.prop('disabled', true);
                    $btnHpConfirm.prop('disabled', true);
                },
                success: function (res) {
                    if (res?.success) {
                        ModalUtil.alert({
                            title: '패스워드 찾기',
                            message: res.message || '휴대폰 인증이 완료되었습니다.',
                            okText: '확인',
                            onOk: function () {
                            },
                        });

                        hpConfirmed = true;

                        timer.complete();
                        clearError($hpWrap, $hpError);

                        // 인증 완료 → 모든 인증 요소 잠금
                        $hpInput.prop('readonly', true);
                        $codeInput.prop('readonly', true);
                        $btnHpReq.prop('disabled', true);
                        $btnHpConfirm.prop('disabled', true);
                    } else {
                        hpConfirmed = false;
                        showError($hpWrap, $hpError, res?.message || '인증번호가 올바르지 않습니다.');
                    }
                },
                error: function () {
                    showError($hpWrap, $hpError, '인증 확인 중 오류가 발생했습니다.');
                    ModalUtil.alert({
                        title: '패스워드 찾기',
                        message: '인증 확인 중 오류가 발생했습니다.',
                        okText: '확인',
                        onOk: function () {
                        },
                    });
                },
                complete: function () {
                    if (!hpConfirmed) {
                        $btnHpConfirm.prop('disabled', false);
                        $btnHpReq.prop('disabled', false);
                    }
                }
            });
        });

        // ===============================
        // 3) 비밀번호 찾기 제출 (아이디 + 인증된 휴대폰)
        // ===============================
        $btnSubmit.on('click', function () {
            // 상태 초기화
            resetFieldState($idWrap, $idError);
            resetFieldState($hpWrap, $hpError);

            let hasError = false;

            // 아이디 입력 체크
            const idVal = $.trim($idInput.val());
            if (!idVal) {
                showError($idWrap, $idError, '아이디를 입력해 주세요.');
                if (!hasError) $idInput.focus();
                hasError = true;
            }

            // 휴대폰 번호 형식 체크
            const hpVal = $hpInput.val();
            const hpResult = ValidationUtils.validateHp(hpVal);
            if (!hpResult.valid) {
                showError($hpWrap, $hpError, hpResult.msg);
                if (!hasError) $hpInput.focus();
                hasError = true;
            } else if (!hpConfirmed) {
                showError($hpWrap, $hpError, '휴대폰 인증을 완료해 주세요.');
                hasError = true;
            }

            if (hasError) {
                ModalUtil.alert({
                    title: '패스워드 찾기',
                    message: '입력 정보를 확인해 주세요.',
                    okText: '확인',
                    onOk: function () {
                    },
                });
                return;
            }

            // 모든 검증 통과 → 비밀번호 재설정 페이지 이동
            $.ajax({
                url: '<?=AUTH_ACTIONS?>/find_update.php',
                type: 'POST',
                dataType: 'json',
                data: { act: 'find_pw', mt_id: idVal, mt_hp: hpVal },
                beforeSend: () => $btnSubmit.prop('disabled', true),
                success: function (res) {
                    if (res?.success && res.encrypted_id) {
                        const encId = encodeURIComponent(res.encrypted_id);
                        location.href = '<?=AUTH_PAGE?>/find_pw_reset.php?id=' + encId;
                    } else {
                        ModalUtil.alert({
                            title: '패스워드 찾기',
                            message: res?.message || '해당 정보로 등록된 계정을 찾을 수 없습니다.',
                            okText: '확인',
                            onOk: function () {
                            },
                        });
                    }
                },
                error: function () {
                    ModalUtil.alert({
                        title: '패스워드 찾기',
                        message: '비밀번호 찾기 중 오류가 발생했습니다. 잠시 후 다시 시도해 주세요.',
                        okText: '확인',
                        onOk: function () {
                        },
                    });
                },
                complete: () => $btnSubmit.prop('disabled', false)
            });
        });

        // 폼 submit 방지
        $form.on('submit', e => e.preventDefault());
    });
</script>
