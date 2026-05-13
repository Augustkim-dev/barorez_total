/**
 * HPAuth (공통)
 * - 휴대폰 인증 요청/확인 + 타이머 + 버튼 상태 + FormState 연동
 * ✅ 퍼블 수정 없이 사용
 * ✅ let/const만 사용
 * ✅ console.log 포함
 * ✅ jQuery 의존
 * ✅ FormState 필요
 */
(function (global) {
    'use strict';

    const HPAuth = (() => {

        // ============================
        // 내부 타이머 (HPTimer 의존성 제거)
        // ============================
        const createTimer = (opt) => {
            const { timerSelector, onExpire } = opt;

            const $timer = $(timerSelector);
            let interval = null;
            let remainSec = 0;

            const render = () => {
                const m = String(Math.floor(remainSec / 60)).padStart(2, '0');
                const s = String(remainSec % 60).padStart(2, '0');
                $timer.text(`${m}:${s}`);
            };

            const stop = () => {
                // console.log('[HPAuth][timer] stop');
                if (interval) clearInterval(interval);
                interval = null;
                remainSec = 0;
                $timer.text('00:00');
            };

            const start = (sec) => {
                // console.log('[HPAuth][timer] start:', sec);
                remainSec = sec;

                if (interval) clearInterval(interval);
                render();

                interval = setInterval(() => {
                    remainSec = Math.max(0, remainSec - 1);
                    render();

                    if (remainSec <= 0) {
                        // console.log('[HPAuth][timer] expired');
                        stop();
                        if (typeof onExpire === 'function') onExpire();
                    }
                }, 1000);
            };

            const isExpired = () => !interval && ($timer.text() === '00:00');

            return { start, stop, isExpired };
        };

        const init = (opt) => {
            const {
                ajaxUrl,

                // selectors
                hpInput,
                codeInput,
                sendBtn,
                verifyBtn,
                timerEl,
                certOkInput,
                hpCode,

                // server act
                actSend = 'send_hp_code',
                actVerify = 'verify_hp_code',

                // ui text
                sendTextDefault = '인증 요청',
                sendTextRetry = '재 요청',
                msgDefault = '인증 요청 후 인증번호를 입력해 주세요.',
                msgSending = '인증번호 전송중...',
                msgVerifying = '인증번호 확인중...',
                msgExpired = '인증시간이 만료되었습니다. 다시 인증 요청을 해주세요.',
                msgVerified = '인증번호 확인',

                // config
                timerSec = 300,
                lockOnSuccess = true,
            } = opt;

            // console.log('[HPAuth] init');

            // ✅ 필수 의존성 체크
            if (!global.FormState) {
                // console.log('[HPAuth] FormState not found (window.FormState undefined)');
                return null;
            }

            const $hp = $(hpInput);
            const $code = $(codeInput);
            const $send = $(sendBtn);
            const $verify = $(verifyBtn);
            const $certOk = $(certOkInput);
            const $hpCode = $(hpCode);

            // ✅ 숫자만 입력 강제
            if (typeof global.FormState.bindOnlyNumber === 'function') {
                global.FormState.bindOnlyNumber(hpInput);
                global.FormState.bindOnlyNumber(codeInput);
            }

            // ✅ 타이머 생성 (내장 타이머)
            const timer = createTimer({
                timerSelector: timerEl,
                onExpire: () => {
                    // console.log('[HPAuth] timer expired callback');
                    $certOk.val('N');
                    $verify.prop('disabled', true);
                    global.FormState.setInvalid($hp, msgExpired);
                },
            });

            const reset = () => {
                // console.log('[HPAuth] reset');

                $certOk.val('N');
                $verify.prop('disabled', true);
                $send.text(sendTextDefault);

                timer.stop();
                global.FormState.clearState($hp, msgDefault);

                if (lockOnSuccess) {
                    $hp.prop('readonly', false);
                    $code.prop('readonly', false);
                    $send.prop('disabled', false);
                }
            };

            // ✅ 입력 변경 시 리셋
            $hp.on('input', () => reset());
            $code.on('input', () => {
                if ($certOk.val() === 'Y') reset();
            });

            // ============================
            // ① 인증 요청
            // ============================
            $send.on('click', () => {
                // console.log('[HPAuth] send click');

                const hp = ($hp.val() || '').replace(/[^0-9]/g, '');
                if (!hp) {
                    global.FormState.setInvalid($hp, '휴대폰번호를 입력해 주세요.');
                    $hp.focus();
                    return;
                }

                $certOk.val('N');
                $verify.prop('disabled', true);
                global.FormState.clearState($hp, msgSending);

                timer.stop(); // 재요청 대비

                $.ajax({
                    url: ajaxUrl,
                    type: 'POST',
                    dataType: 'json',
                    data: { act: actSend, mb_hp: hp },
                    success: (res) => {
                        // console.log('[HPAuth] send success:', res);

                        if (res && res.success) {
                            $send.text(sendTextRetry);
                            $verify.prop('disabled', false);

                            timer.start(timerSec);
                            $code.prop('readonly', false);
                            global.FormState.clearState(
                                $hp,
                                res.message || '인증번호가 발송되었습니다. 인증번호를 입력해 주세요.'
                            );
                            // $hpCode.val(res.code);
                        } else {
                            global.FormState.setInvalid($hp, (res && res.message) || '인증번호 발송에 실패했습니다.');
                        }
                    },
                    error: (xhr) => {
                        console.log('[HPAuth] send error:', xhr);
                        global.FormState.setInvalid($hp, '서버 통신 오류로 인증 요청에 실패했습니다.');
                    },
                });
            });

            // ============================
            // ② 인증번호 확인
            // ============================
            $verify.on('click', () => {
                console.log('[HPAuth] verify click');

                const hp = ($hp.val() || '').replace(/[^0-9]/g, '');
                const code = ($code.val() || '').trim();

                if (!hp) {
                    global.FormState.setInvalid($hp, '휴대폰번호를 입력해 주세요.');
                    $hp.focus();
                    return;
                }
                if (!code) {
                    global.FormState.setInvalid($hp, '인증번호를 입력해 주세요.');
                    $code.focus();
                    return;
                }
                if (timer.isExpired()) {
                    global.FormState.setInvalid($hp, msgExpired);
                    return;
                }

                global.FormState.clearState($hp, msgVerifying);

                $.ajax({
                    url: ajaxUrl,
                    type: 'POST',
                    dataType: 'json',
                    data: { act: actVerify, mb_hp: hp, hp_code: code },
                    success: (res) => {
                        // console.log('[HPAuth] verify success:', res);

                        if (res && res.success) {
                            $certOk.val('Y');

                            // ✅ 성공: ip_valid
                            global.FormState.setValid($hp, res.message || msgVerified);

                            // ✅ 확인 버튼 잠금
                            $verify.prop('disabled', true);

                            // ✅ 타이머 종료
                            timer.stop();

                            // ✅ 성공 후 잠금(옵션)
                            if (lockOnSuccess) {
                                // $hp.prop('readonly', true);
                                $code.prop('readonly', true);
                                // $send.prop('disabled', true);
                            }
                        } else {
                            $certOk.val('N');
                            global.FormState.setInvalid($hp, (res && res.message) || '인증번호가 올바르지 않습니다.');
                        }
                    },
                    error: (xhr) => {
                        console.log('[HPAuth] verify error:', xhr);
                        $certOk.val('N');
                        global.FormState.setInvalid($hp, '서버 통신 오류로 인증 확인에 실패했습니다.');
                    },
                });
            });

            // init 시 기본 상태
            reset();

            return { reset, timer };
        };

        return { init };
    })();

    global.HPAuth = HPAuth;
})(window);
