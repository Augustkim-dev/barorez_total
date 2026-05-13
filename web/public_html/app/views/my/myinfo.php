
<div class="wrap">
    <div class="sub_pg   pb_lg  ">
        <div class="container">
            <div class="mt-5">
                <form id="profileForm">
                    <!-- 아이디 (수정불가, 세션에서 출력) -->
                    <div class="form_wr">
                        <div class="ip_tit required">
                            <h5>아이디</h5>
                        </div>
                        <p class="fw_700 mt-3" id="mt_id_text">
                            <?=htmlspecialchars($_SESSION['mng']['mt_id'] ?? '', ENT_QUOTES, 'UTF-8')?>
                        </p>
                        <!-- 필요시 백엔드로 전송할 hidden -->
                        <input type="hidden" id="mt_id" name="mt_id"
                               value="<?=htmlspecialchars($_SESSION['mng']['mt_id'] ?? '', ENT_QUOTES, 'UTF-8')?>">
                    </div>

                    <!-- 비밀번호 (변경 시에만 입력) -->
                    <div class="form_wr mt-5" id="formPw">
                        <div class="ip_tit required">
                            <h5>비밀번호</h5>
                        </div>

                        <div>
                            <input type="password"
                                   class="form-control"
                                   id="mt_pwd"
                                   name="mt_pwd"
                                   placeholder="비밀번호  입력(영소문, 숫자 포함 8~16자)">
                        </div>
                        <div class="mt-2">
                            <input type="password"
                                   class="form-control"
                                   id="mt_pwd2"
                                   placeholder="비밀번호 재입력">
                        </div>

                        <div class="form-text ip_invalid">비밀번호가 일치하지 않습니다.</div>
                    </div>

                    <!-- 이름 -->
                    <div class="form_wr mt-5" id="formName">
                        <div class="ip_tit required">
                            <h5>이름</h5>
                        </div>
                        <div class="form-row">
                            <div class="col">
                                <input type="text"
                                       class="form-control"
                                       id="mt_name"
                                       name="mt_name"
                                       placeholder="이름 입력"
                                       value="<?=htmlspecialchars($_SESSION['mng']['mt_name'] ?? '', ENT_QUOTES, 'UTF-8')?>">
                            </div>
                        </div>
                        <div class="form-text ip_invalid">이름을 입력하세요</div>
                    </div>

                    <!-- 휴대폰번호 -->
                    <div class="form_wr mt-5" id="formHp">
                        <div class="ip_tit required">
                            <h5>휴대폰번호</h5>
                        </div>
                        <div class="form-row">
                            <div class="col">
                                <input type="text"
                                       class="form-control"
                                       id="mt_hp"
                                       name="mt_hp"
                                       placeholder="‘-’ 없이 숫자만 입력"
                                       value="<?=htmlspecialchars($_SESSION['mng']['mt_hp'] ?? '', ENT_QUOTES, 'UTF-8')?>">
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
                                <p class="time_lim" id="hpTimer">00:00</p>
                                <input type="text"
                                       class="form-control"
                                       id="mt_hp_code"
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
                                <!-- 인증요청 아직 안했을때는 비활성화 / 인증요청 후 disabled 삭제 -->
                            </div>
                        </div>
                        <div class="form-text ip_invalid">오류 텍스트</div>
                    </div>

                    <!-- 기존 휴대폰번호 / 인증여부 / CSRF -->
                    <input type="hidden" id="org_hp"
                           value="<?=htmlspecialchars($_SESSION['mng']['mt_hp'] ?? '', ENT_QUOTES, 'UTF-8')?>">
                    <input type="hidden" id="mt_hp_chk" name="mt_hp_chk" value="Y"><!-- 처음엔 기존 번호라 인증된 상태로 간주 -->

                    <div class="mt-4">
                        <button type="button"
                                class="btn btn-outline-light btn-block un_reboot_a border-0"
                                onclick="location.href='./secede.php' ">
                            회원탈퇴
                        </button>
                    </div>

                    <div class="bottom_btn bg-white">
                        <div class="form-row">
                            <div class="col-12">
                                <button type="button"
                                        class="btn btn-primary btn-block btn-lg"
                                        id="btnSave">
                                    저장
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
<script>
    $(function () {
        console.log('마이페이지 회원정보 수정 스크립트 초기화');

        let $form        = $('#profileForm');

        let $formPw      = $('#formPw');
        let $formName    = $('#formName');
        let $formHp      = $('#formHp');

        let $mtId        = $('#mt_id');
        let $mtPwd       = $('#mt_pwd');
        let $mtPwd2      = $('#mt_pwd2');
        let $mtName      = $('#mt_name');
        let $mtHp        = $('#mt_hp');
        let $mtHpCode    = $('#mt_hp_code');

        let $btnSendSms  = $('#btnSendSms');
        let $btnVerifySms= $('#btnVerifySms');
        let $btnSave     = $('#btnSave');

        let $hpTimer     = $('#hpTimer');

        let $orgHp       = $('#org_hp');
        let $mtHpChk     = $('#mt_hp_chk');

        // 상태 플래그
        let hpVerified   = true;   // 처음엔 기존 번호라 인증된 상태로 본다
        let smsRequested = false;
        let timerSec     = 0;
        let timerInterval= null;

        // 공통: 폼 submit 막기
        $form.on('submit', function (e) {
            e.preventDefault();
        });

        // 공통: 필드 상태 함수
        function setFieldError($wrap, msg) {
            $wrap.removeClass('ip_valid').addClass('ip_invalid');
            if (msg !== undefined) {
                $wrap.find('.form-text').text(msg);
            }
        }
        function setFieldSuccess($wrap, msg) {
            $wrap.removeClass('ip_invalid').addClass('ip_valid');
            if (msg !== undefined) {
                $wrap.find('.form-text').text(msg);
            }
        }
        function clearFieldState($wrap, msg) {
            $wrap.removeClass('ip_invalid ip_valid');
            if (msg !== undefined) {
                $wrap.find('.form-text').text(msg);
            }
        }

        // 검증 함수들 (메시지 없이 결과만)
        function checkPasswordValue() {
            let pw1 = $.trim($mtPwd.val());
            let pw2 = $.trim($mtPwd2.val());
            let regex = /^(?=.*[a-zA-Z])(?=.*\d)[A-Za-z\d!@#$%^&*()_+\-=]{8,16}$/;

            // 둘 다 비어있으면 "비번 변경 안함" → 유효
            if (pw1 === '' && pw2 === '') {
                return { valid: true, msg: '' , changed: false};
            }

            if (pw1 === '' || pw2 === '') {
                return { valid: false, msg: '비밀번호를 모두 입력해 주세요.', changed: true };
            }
            if (!regex.test(pw1)) {
                return { valid: false, msg: '비밀번호는 8~16자의 영문/숫자 조합이어야 합니다.', changed: true };
            }
            if (pw1 !== pw2) {
                return { valid: false, msg: '비밀번호가 일치하지 않습니다.', changed: true };
            }
            return { valid: true, msg: '사용 가능한 비밀번호입니다.', changed: true };
        }

        function checkNameValue() {
            let val = $.trim($mtName.val());
            if (val === '') {
                return { valid: false, msg: '이름을 입력해 주세요.' };
            }
            return { valid: true, msg: '' };
        }

        function checkHpValue() {
            let hp = $mtHp.val().replace(/[^0-9]/g, '');
            $mtHp.val(hp);

            if (hp === '') {
                return { valid: false, msg: '휴대폰 번호를 입력해 주세요.' };
            }
            if (!/^[0-9]{10,11}$/.test(hp)) {
                return { valid: false, msg: '휴대폰 번호 형식이 올바르지 않습니다.' };
            }
            return { valid: true, msg: '휴대폰 번호 형식이 올바릅니다.' };
        }

        // 휴대폰 입력 시 숫자만 / 기존번호와 비교해서 인증 상태 리셋
        $mtHp.on('input', function () {
            let hp = $(this).val().replace(/[^0-9]/g, '');
            $(this).val(hp);

            let orgHp = $orgHp.val();

            if (hp !== orgHp) {
                hpVerified = false;
                smsRequested = false;
                $mtHpChk.val('N');
                clearFieldState($formHp);
                $mtHpCode.val('');
                $mtHpCode.prop('disabled', true);
                $btnVerifySms.prop('disabled', true);
                if (timerInterval) {
                    clearInterval(timerInterval);
                    timerInterval = null;
                }
                $hpTimer.text('00:00');
            } else {
                hpVerified = true;
                $mtHpChk.val('Y');
                clearFieldState($formHp);
                $mtHpCode.val('');
                $mtHpCode.prop('disabled', true);
                $btnVerifySms.prop('disabled', true);
                if (timerInterval) {
                    clearInterval(timerInterval);
                    timerInterval = null;
                }
                $hpTimer.text('00:00');
            }
        });

        // 타이머 함수
        function startTimer(seconds) {
            timerSec = seconds;

            if (timerInterval) {
                clearInterval(timerInterval);
            }

            updateTimerDisplay();

            timerInterval = setInterval(function () {
                timerSec--;
                updateTimerDisplay();

                if (timerSec <= 0) {
                    clearInterval(timerInterval);
                    timerInterval = null;

                    smsRequested = false;
                    if (!hpVerified) {
                        setFieldError($formHp, '인증 시간이 만료되었습니다. 다시 인증요청을 해주세요.');
                        $btnVerifySms.prop('disabled', true);
                        $mtHpCode.prop('disabled', true);
                        $mtHpChk.val('N');
                    }
                }
            }, 1000);
        }
        function updateTimerDisplay() {
            let min = Math.floor(timerSec / 60);
            let sec = timerSec % 60;
            let text =
                (min < 10 ? '0' + min : min) +
                ':' +
                (sec < 10 ? '0' + sec : sec);
            $hpTimer.text(text);
        }

        // 휴대폰 인증요청 (번호가 기존과 동일하면 굳이 요청 안 해도 됨)
        $btnSendSms.on('click', function () {
            console.log('휴대폰 인증요청 버튼 클릭');

            let hpResult = checkHpValue();
            if (!hpResult.valid) {
                setFieldError($formHp, hpResult.msg);
                ModalUtil.alert({
                    title: '회원정보',
                    message: hpResult.msg,
                    okText: '확인',
                    onOk: function () {
                    },
                });
                $mtHp.focus();
                return;
            }

            let hp = $mtHp.val();
            let orgHp = $orgHp.val();

            if (hp === orgHp) {
                ModalUtil.alert({
                    title: '회원정보',
                    message: '기존에 등록된 번호와 동일합니다. 인증이 이미 완료된 상태입니다.',
                    okText: '확인',
                    onOk: function () {
                    },
                });
                return;
            }

            // console.log('휴대폰 인증요청 AJAX 전송:', hp);
            let url = '<?=AUTH_ACTIONS?>/join_update.php';
            $.ajax({
                url: url, // 🔥 휴대폰 인증요청 API (기존 chk_mt_hp 쓰던 곳)
                type: 'POST',
                dataType: 'json',
                data: {
                    act: 'chk_mt_hp',
                    mt_hp: hp
                },
                beforeSend: function () {
                    console.log('휴대폰 인증요청 AJAX 시작');
                },
                success: function (res) {

                    if (res && res.success) {
                        smsRequested = true;
                        hpVerified   = false;
                        $mtHpChk.val('N');

                        setFieldSuccess(
                            $formHp,
                            res.message || '인증번호를 발송했습니다. 문자메시지를 확인해 주세요.'
                        );
                        ModalUtil.alert({
                            title: '회원정보',
                            message: (res.message || '인증번호를 발송했습니다.'),
                            okText: '확인',
                            onOk: function () {
                            },
                        });

                        startTimer(5 * 60);

                        $mtHpCode.prop('disabled', false);
                        $btnVerifySms.prop('disabled', false);
                        $mtHpCode.focus();
                        // if(res.auth_code) $mtHpCode.val(res.auth_code);
                    } else {
                        smsRequested = false;
                        hpVerified   = false;
                        $mtHpChk.val('N');

                        setFieldError(
                            $formHp,
                            res && res.message ? res.message : '인증요청에 실패했습니다. 다시 시도해 주세요.'
                        );
                        ModalUtil.alert({
                            title: '회원정보',
                            message: res && res.message ? res.message : '인증요청에 실패했습니다.',
                            okText: '확인',
                            onOk: function () {
                            },
                        });
                    }
                },
                error: function (xhr, status, error) {
                    smsRequested = false;
                    hpVerified   = false;
                    $mtHpChk.val('N');

                    setFieldError($formHp, '인증요청 중 오류가 발생했습니다. 잠시 후 다시 시도해 주세요.');
                    ModalUtil.alert({
                        title: '회원정보',
                        message: '인증요청 중 오류가 발생했습니다.',
                        okText: '확인',
                        onOk: function () {
                        },
                    });
                }
            });
        });

        // 휴대폰 인증번호 확인
        $btnVerifySms.on('click', function () {
            if (!smsRequested) {
                ModalUtil.alert({
                    title: '회원정보',
                    message: '먼저 인증요청을 해주세요.',
                    okText: '확인',
                    onOk: function () {
                    },
                });
                return;
            }

            let hp   = $mtHp.val();
            let code = $.trim($mtHpCode.val());

            if (code === '') {
                ModalUtil.alert({
                    title: '회원정보',
                    message: '인증번호를 입력해 주세요.',
                    okText: '확인',
                    onOk: function () {
                    },
                });
                $mtHpCode.focus();
                return;
            }

            if (!/^[0-9]{4,6}$/.test(code)) {
                ModalUtil.alert({
                    title: '회원정보',
                    message: '인증번호 형식이 올바르지 않습니다.',
                    okText: '확인',
                    onOk: function () {
                    },
                });
                $mtHpCode.focus();
                return;
            }

            console.log('휴대폰 인증번호 확인 AJAX 전송:', hp, code);
            let url = '<?=AUTH_ACTIONS?>/join_update.php';
            $.ajax({
                url: url, // 🔥 confirm_mt_hp 쓰던 곳
                type: 'POST',
                dataType: 'json',
                data: {
                    act: 'confirm_mt_hp',
                    mt_hp: hp,
                    mt_hp_confirm: code
                },
                beforeSend: function () {
                    console.log('휴대폰 인증번호 확인 AJAX 시작');
                },
                success: function (res) {
                    if (res && res.success) {
                        hpVerified   = true;
                        smsRequested = false;
                        $mtHpChk.val('Y');

                        if (timerInterval) {
                            clearInterval(timerInterval);
                            timerInterval = null;
                        }

                        setFieldSuccess($formHp, res.message || '휴대폰 인증이 완료되었습니다.');
                        ModalUtil.alert({
                            title: '회원정보',
                            message: res.message || '휴대폰 인증이 완료되었습니다.',
                            okText: '확인',
                            onOk: function () {
                            },
                        });

                        $mtHp.prop('readonly', false); // 수정은 계속 가능하지만, 변경 시 다시 인증 필요
                        $mtHpCode.prop('disabled', true);
                        $btnVerifySms.prop('disabled', true);
                    } else {
                        hpVerified   = false;
                        $mtHpChk.val('N');

                        setFieldError(
                            $formHp,
                            res && res.message ? res.message : '인증번호가 올바르지 않습니다.'
                        );
                        ModalUtil.alert({
                            title: '회원정보',
                            message: res && res.message ? res.message : '인증번호가 올바르지 않습니다.',
                            okText: '확인',
                            onOk: function () {
                            },
                        });
                    }
                },
                error: function (xhr, status, error) {
                    hpVerified   = false;
                    $mtHpChk.val('N');

                    setFieldError($formHp, '인증 확인 중 오류가 발생했습니다. 잠시 후 다시 시도해 주세요.');
                    ModalUtil.alert({
                        title: '회원정보',
                        message: '인증 확인 중 오류가 발생했습니다.',
                        okText: '확인',
                        onOk: function () {
                        },
                    });
                }
            });
        });

        // 저장 버튼 클릭 → 검증 + 수정 API 호출
        $btnSave.on('click', function () {

            clearFieldState($formPw);
            clearFieldState($formName);
            clearFieldState($formHp);

            let firstErrorField = null;

            // 1) 비밀번호 검증
            let pwResult = checkPasswordValue();
            if (!pwResult.valid) {
                setFieldError($formPw, pwResult.msg);
                if (!firstErrorField) firstErrorField = $mtPwd;
            } else {
                setFieldSuccess($formPw, '');
            }

            // 2) 이름 검증
            let nameResult = checkNameValue();
            if (!nameResult.valid) {
                setFieldError($formName, nameResult.msg);
                if (!firstErrorField) firstErrorField = $mtName;
            } else {
                setFieldSuccess($formName, '');
            }

            // 3) 휴대폰 형식 검증
            let hpResult = checkHpValue();
            if (!hpResult.valid) {
                setFieldError($formHp, hpResult.msg);
                if (!firstErrorField) firstErrorField = $mtHp;
            } else {
                setFieldSuccess($formHp, '');
            }

            // 4) 휴대폰 번호 변경 시 인증 여부 확인
            let hp    = $mtHp.val();
            let orgHp = $orgHp.val();
            if (hp !== orgHp && $mtHpChk.val() !== 'Y') {
                setFieldError($formHp, '휴대폰 번호 변경 시 인증이 필요합니다. 인증을 완료해 주세요.');
                if (!firstErrorField) firstErrorField = $mtHpCode;
            }

            if (firstErrorField) {
                console.log('회원정보 검증 실패, 첫 에러 필드로 포커스');
                firstErrorField.focus();
                ModalUtil.alert({
                    title: '회원정보',
                    message: '입력하신 정보를 다시 확인해 주세요.',
                    okText: '확인',
                    onOk: function () {
                    },
                });
                return;
            }

            // --------------------------
            //  검증 통과 → AJAX 저장
            // --------------------------
            console.log('회원정보 검증 통과, AJAX 요청 준비');

            let data = {
                act: 'profile',  // 🔥 백엔드에서 분기용
                mt_id:  $mtId.val(),
                mt_name: $.trim($mtName.val()),
                mt_hp:   $.trim($mtHp.val()),
                mt_hp_chk: $mtHpChk.val(),
            };

            // 비밀번호 변경이 있는 경우에만 전송
            if (pwResult.changed && pwResult.valid) {
                data.mt_pwd = $.trim($mtPwd.val());
            }

            console.log('회원정보 수정 전송 데이터:', data);
            let url = '<?=MY_ACTIONS?>/update.php';
            $.ajax({
                url: url, // 🔥 실제 회원정보 수정 API 경로로 변경
                type: 'POST',
                dataType: 'json',
                data: data,
                beforeSend: function () {
                    console.log('회원정보 수정 AJAX 전송 시작');
                    $btnSave.prop('disabled', true);
                },
                success: function (res) {
                    console.log('회원정보 수정 응답:', res);

                    if (res && res.success) {
                        ModalUtil.alert({
                            title: '회원정보',
                            message: res.message || '회원정보가 수정되었습니다.',
                            okText: '확인',
                            onOk: function () {
                                if (res.redirect) {
                                    location.href = res.redirect;
                                } else {
                                    location.reload();
                                }
                            },
                        });
                    } else {
                        ModalUtil.alert({
                            title: '회원정보',
                            message: res && res.message ? res.message : '회원정보 수정에 실패했습니다. 다시 시도해 주세요.',
                            okText: '확인',
                            onOk: function () {
                            },
                        });
                        $btnSave.prop('disabled', false);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('회원정보 수정 AJAX 오류:', status, error);
                    console.log('서버 원본 응답:', xhr.responseText);
                    alert('회원정보 수정 중 오류가 발생했습니다. 잠시 후 다시 시도해 주세요.');
                    $btnSave.prop('disabled', false);
                }
            });
        });
    });
</script>
