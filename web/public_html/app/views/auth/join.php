

<div class="wrap">
    <div class="sub_pg pb_lg">
        <div class="container">
            <div class="mt-5">
                <form id="joinForm">
                    <input type="hidden" id="mt_id_chk" name="mt_id_chk" value="N">
                    <input type="hidden" id="act" name="act" value="join">
                    <input type="hidden" id="mt_hp_chk" name="mt_hp_chk" value="N">
                    <!-- 아이디 -->
                    <div class="form_wr ip_invalid" id="formId">
                        <div class="ip_tit required">
                            <h5>아이디</h5>
                        </div>
                        <div class="form-row">
                            <div class="col">
                                <input type="text"
                                       class="form-control"
                                       id="memId"
                                       name="mt_id"
                                       placeholder="영소문, 숫자 포함 6~16자">
                            </div>
                            <div class="col-3">
                                <button type="button"
                                        class="btn btn-outline-primary btn-block"
                                        id="btnIdCheck">
                                    중복체크
                                </button>
                            </div>
                        </div>
                        <div class="form-text ip_invalid" id="idMsg">
                            6~16자의 영문 소문자, 숫자로만 입력해 주세요
                        </div>
                    </div>

                    <!-- 비밀번호 -->
                    <div class="form_wr mt-5 ip_invalid" id="formPw">
                        <div class="ip_tit required">
                            <h5>비밀번호</h5>
                        </div>

                        <div>
                            <input type="password"
                                   class="form-control"
                                   id="memPw"
                                   name="mt_pw"
                                   placeholder="비밀번호  입력(영소문, 숫자 포함 8~16자)">
                        </div>
                        <div class="mt-2">
                            <input type="password"
                                   class="form-control"
                                   id="memPw2"
                                   name="mt_pw_confirm"
                                   placeholder="비밀번호 재입력">
                        </div>

                        <div class="form-text ip_invalid" id="pwMsg">
                            비밀번호가 일치하지 않습니다.
                        </div>
                    </div>

                    <!-- 이름 -->
                    <div class="form_wr mt-5 ip_invalid" id="formName">
                        <div class="ip_tit required">
                            <h5>이름</h5>
                        </div>
                        <div class="form-row">
                            <div class="col">
                                <input type="text"
                                       class="form-control"
                                       id="memName"
                                       name="mt_name"
                                       placeholder="이름 입력">
                            </div>
                        </div>
                        <div class="form-text ip_invalid" id="nameMsg">
                            이름을 입력하세요
                        </div>
                    </div>

                    <!-- 휴대폰번호 + 인증 -->
                    <div class="form_wr mt-5 ip_invalid" id="formHp">
                        <div class="ip_tit required">
                            <h5>휴대폰번호</h5>
                        </div>
                        <div class="form-row">
                            <div class="col">
                                <input type="text"
                                       class="form-control"
                                       id="memHp"
                                       name="mt_hp"
                                       placeholder="‘-’ 없이 숫자만 입력">
                            </div>
                            <div class="col-3">
                                <button type="button"
                                        class="btn btn-outline-primary btn-block"
                                        id="btnSendSms">
                                    인증요청
                                </button>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col mt-3 position-relative">
                                <p class="time_lim" id="hpTimer">05:00</p>
                                <input type="text"
                                       class="form-control"
                                       id="memHpCode"
                                       name="mt_hp_code"
                                       placeholder="인증번호 입력"
                                       disabled>
                            </div>
                            <div class="col-3 mt-3">
                                <button type="button"
                                        class="btn btn-primary btn-block"
                                        id="btnVerifySms"
                                        disabled>
                                    확인
                                </button>
                            </div>
                        </div>
                        <div class="form-text ip_invalid" id="hpMsg">
                            휴대폰 번호를 입력 후 인증요청을 해주세요.
                        </div>
                    </div>

                    <!-- 하단 회원가입 버튼 -->
                    <div class="bottom_btn bg-white">
                        <div class="form-row">
                            <div class="col-12">
                                <button type="button"
                                        class="btn btn-primary btn-block btn-lg"
                                        id="btnJoin">
                                    회원가입
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
<?php include_once("../inc/modal.php");?>
<script src="<?=CDN_UTIL_URL?>/timer.js"></script>
<script src="<?=CDN_UTIL_URL?>/msg_err.js"></script>
<script src="<?=CDN_UTIL_URL?>/validation.js"></script>
<script>
    $(function () {
        // 요소 변수들
        const $formId   = $('#formId');
        const $formPw   = $('#formPw');
        const $formName = $('#formName');
        const $formHp   = $('#formHp');

        const $memId    = $('#memId');
        const $memPw    = $('#memPw');
        const $memPw2   = $('#memPw2');
        const $memName  = $('#memName');
        const $memHp    = $('#memHp');
        const $memHpCode = $('#memHpCode');

        const $btnIdCheck   = $('#btnIdCheck');
        const $btnSendSms   = $('#btnSendSms');
        const $btnVerifySms = $('#btnVerifySms');
        const $btnJoin      = $('#btnJoin');

        const $hpTimer = $('#hpTimer');
        const $hpMsg   = $('#hpMsg');       // 휴대폰 에러 메시지 요소
        const $idMsg   = $('#idMsg');
        const $pwMsg   = $('#pwMsg');
        const $nameMsg = $('#nameMsg');

        const $mtIdChk = $('#mt_id_chk');
        const $mtHpChk = $('#mt_hp_chk');

        // 상태 플래그
        let idChecked     = false;
        let lastCheckedId = '';
        let hpVerified    = false;
        let smsRequested  = false;

        // 초기 중립 상태
        $('.form_wr').each(function() {
            const $wrap = $(this);
            const $errorEl = $wrap.find('.form-text');
            resetFieldState($wrap, $errorEl);
        });

        const timer = AuthTimerFactory($hpTimer, () => {
            smsRequested = false;
            if (!hpVerified) {
                showError($formHp, $hpMsg, '인증 시간이 만료되었습니다. 다시 인증요청을 해주세요.');
                $btnVerifySms.prop('disabled', true);
                $memHpCode.prop('disabled', true).val('');
            }
        });

        // 아이디 입력 시 중복체크 초기화
        $memId.on('input', () => {
            idChecked = false;
            lastCheckedId = '';
            resetFieldState($formId, $idMsg);
        });

        $memHp.on('input', function () {
            let hp = $(this).val().replace(/[^0-9]/g, '');
            $(this).val(hp);
        });

        // ===============================
        // 아이디 중복체크
        // ===============================
        $btnIdCheck.on('click', function () {
            const idVal = $.trim($memId.val());
            const result = ValidationUtils.validateId(idVal);

            if (!result.valid) {
                showError($formId, $idMsg, result.msg);
                ModalUtil.alert({
                    title: '회원가입',
                    message: result.msg,
                    okText: '확인',
                    onOk: function () {
                    },
                });
                $memId.focus();
                return;
            }

            $.ajax({
                url: '<?=AUTH_ACTIONS?>/join_update.php',
                type: 'POST',
                dataType: 'json',
                data: { mt_id: idVal, act: 'chk_mt_id' },
                success: function (res) {
                    if (res?.success) {
                        idChecked = true;
                        lastCheckedId = idVal;
                        $mtIdChk.val('Y');
                        clearError($formId, $idMsg);
                        $idMsg.text(res.message || '사용 가능한 아이디입니다.').show();
                        ModalUtil.alert({
                            title: '회원가입',
                            message: res.message || '사용 가능한 아이디입니다.',
                            okText: '확인',
                            onOk: function () {
                            },
                        });
                    } else {
                        idChecked = false;
                        $mtIdChk.val('N');
                        showError($formId, $idMsg, res?.message || '이미 사용 중인 아이디입니다.');
                        ModalUtil.alert({
                            title: '회원가입',
                            message: res.message || '이미 사용 중인 아이디입니다.',
                            okText: '확인',
                            onOk: function () {
                            },
                        });
                    }
                },
                error: function () {
                    showError($formId, $idMsg, '중복체크 중 오류가 발생했습니다.');
                }
            });
        });

        // ===============================
        // 휴대폰 인증요청
        // ===============================
        $btnSendSms.on('click', function () {
            const hpVal = $memHp.val();
            const result = ValidationUtils.validateHp(hpVal);

            if (!result.valid) {
                showError($formHp, $hpMsg, result.msg);
                ModalUtil.alert({
                    title: '회원가입',
                    message: result.message,
                    okText: '확인',
                    onOk: function () {
                    },
                });
                $memHp.focus();
                return;
            }

            $.ajax({
                url: '<?=AUTH_ACTIONS?>/join_update.php',
                type: 'POST',
                dataType: 'json',
                data: { mt_hp: hpVal, act: 'chk_mt_hp' },
                success: function (res) {
                    if (res?.success) {
                        smsRequested = true;
                        hpVerified = false;
                        $mtHpChk.val('N');

                        clearError($formHp, $hpMsg);
                        $hpMsg.text(res.message || '인증번호를 발송했습니다.').show();

                        ModalUtil.alert({
                            title: '회원가입',
                            message: res.message || '인증번호를 발송했습니다.',
                            okText: '확인',
                            onOk: function () {
                            },
                        });
                        // alert((res.message || '인증번호를 발송했습니다.') + (res.auth_code ? '\n(개발용: ' + res.auth_code + ')' : ''));

                        // if (res.auth_code) $memHpCode.val(res.auth_code);

                        timer.start(300);
                        $memHpCode.prop('disabled', false).focus();
                        $btnVerifySms.prop('disabled', false);
                    } else {
                        showError($formHp, $hpMsg, res?.message || '인증요청에 실패했습니다.');
                    }
                },
                error: function () {
                    showError($formHp, $hpMsg, '인증요청 중 오류가 발생했습니다.');
                }
            });
        });

        // ===============================
        // 휴대폰 인증 확인
        // ===============================
        $btnVerifySms.on('click', function () {
            if (!smsRequested) {
                ModalUtil.alert({
                    title: '회원가입',
                    message: '먼저 인증요청을 해주세요.',
                    okText: '확인',
                    onOk: function () {
                    },
                });
                // alert('먼저 인증요청을 해주세요.');
                return;
            }

            let code = $.trim($memHpCode.val());
            if (code === '' || !/^[0-9]{4,6}$/.test(code)) {
                showError($formHp, $hpMsg, '인증번호를 정확히 입력해 주세요.');
                $memHpCode.focus();
                return;
            }

            let hp = $memHp.val();
            $.ajax({
                url: '<?=AUTH_ACTIONS?>/join_update.php',
                type: 'POST',
                dataType: 'json',
                data: { mt_hp: hp, mt_hp_confirm: code, act: 'confirm_mt_hp' },
                success: function (res) {
                    if (res?.success) {
                        hpVerified = true;
                        $mtHpChk.val('Y');

                        timer.complete();

                        clearError($formHp, $hpMsg);
                        $hpMsg.text(res.message || '휴대폰 인증이 완료되었습니다.').show();

                        ModalUtil.alert({
                            title: '회원가입',
                            message: res.message || '휴대폰 인증이 완료되었습니다.',
                            okText: '확인',
                            onOk: function () {
                            },
                        });

                        $memHp.prop('readonly', true);
                        $memHpCode.prop('disabled', true);
                        $btnSendSms.prop('disabled', true);
                        $btnVerifySms.prop('disabled', true);
                    } else {
                        showError($formHp, $hpMsg, res?.message || '인증번호가 올바르지 않습니다.');
                    }
                },
                error: function () {
                    showError($formHp, $hpMsg, '인증 확인 중 오류가 발생했습니다.');
                }
            });
        });

        // ===============================
        // 회원가입 제출
        // ===============================
        $btnJoin.on('click', function () {
            // 모든 필드 중립 상태로 초기화
            resetFieldState($formId, $idMsg);
            resetFieldState($formPw, $pwMsg);
            resetFieldState($formName, $nameMsg);
            resetFieldState($formHp, $hpMsg);

            let firstErrorField = null;

            // 1) 아이디 검증 + 중복체크 여부
            const idVal = $.trim($memId.val());
            const idResult = ValidationUtils.validateId(idVal);

            if (!idResult.valid || !idChecked || lastCheckedId !== idVal) {
                const msg = !idResult.valid ? idResult.msg : '아이디 중복체크를 완료해 주세요.';
                showError($formId, $idMsg, msg);
                firstErrorField = firstErrorField || $memId;
            }

            // 2) 비밀번호 검증 (비밀번호 + 재입력 일치까지)
            const pwResult = ValidationUtils.validatePassword($memPw.val(), $memPw2.val());
            if (!pwResult.valid) {
                showError($formPw, $pwMsg, pwResult.msg);
                firstErrorField = firstErrorField || $memPw;
            }

            // 3) 이름 검증
            const nameResult = ValidationUtils.validateName($memName.val());
            if (!nameResult.valid) {
                showError($formName, $nameMsg, nameResult.msg);
                firstErrorField = firstErrorField || $memName;
            }

            // 4) 휴대폰 번호 검증 + 인증 여부
            const hpVal = $memHp.val();
            const hpResult = ValidationUtils.validateHp(hpVal);

            if (!hpResult.valid) {
                showError($formHp, $hpMsg, hpResult.msg);
                firstErrorField = firstErrorField || $memHp;
            } else if ($mtHpChk.val() !== 'Y') {
                showError($formHp, $hpMsg, '휴대폰 인증이 완료되지 않았습니다.');
                firstErrorField = firstErrorField || $memHpCode;
            }

            // 에러가 있으면 포커스 이동 후 종료
            if (firstErrorField) {
                firstErrorField.focus();
                ModalUtil.alert({
                    title: '회원가입',
                    message: '입력 정보를 확인해 주세요.',
                    okText: '확인',
                    onOk: function () {
                    },
                });
                return;
            }

            // 최종 제출
            $.ajax({
                url: '<?=AUTH_ACTIONS?>/join_update.php',
                type: 'POST',
                data: $('#joinForm').serialize(),
                dataType: 'json',
                beforeSend: () => $btnJoin.prop('disabled', true),
                success: function (res) {
                    if (res?.success) {
                        ModalUtil.alert({
                            title: '회원가입',
                            message: res.message || '회원가입이 완료되었습니다.',
                            okText: '확인',
                            onOk: function () {
                                location.href = '<?=AUTH_PAGE?>/join_cmp.php';
                            },
                        });
                        // alert(res.message || '회원가입이 완료되었습니다.');
                        //location.href = '<?php //=AUTH_PAGE?>///join_cmp.php';
                    } else {
                        ModalUtil.alert({
                            title: '회원가입',
                            message: res.message || '회원가입이 실패했습니다.',
                            okText: '확인',
                            onOk: function () {
                            },
                        });
                        $btnJoin.prop('disabled', false);
                    }
                },
                error: () => {
                    alert('서버 오류가 발생했습니다.');
                    $btnJoin.prop('disabled', false);
                }
            });
        });
    });
</script>
