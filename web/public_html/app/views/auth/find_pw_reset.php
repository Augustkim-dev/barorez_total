
<div class="wrap">
    <div class="sub_pg pb_lg">
        <div class="container">
            <form id="resetPwForm">
                <div class="form_wr mt-5 ip_valid">
                    <div class="ip_tit required">
                        <h5>새 비밀번호</h5>
                    </div>
                    <div class="form-row">
                        <div class="col-12">
                            <!-- 🔥 새 비밀번호 -->
                            <input type="password"
                                   class="form-control"
                                   id="newPw"
                                   placeholder="새 비밀번호  입력(영소문, 숫자 포함 8~16자)">
                        </div>
                        <div class="col-12 mt-2">
                            <!-- 🔥 새 비밀번호 재입력 -->
                            <input type="password"
                                   class="form-control"
                                   id="newPwConfirm"
                                   placeholder="비밀번호 재입력">
                        </div>
                    </div>
                    <div class="form-text ip_invalid" id="newPwError">비밀번호가 일치하지 않습니다.</div>
                </div>

                <div class="bottom_btn">
                    <div class="form-row">
                        <!-- onclick 제거, id만 부여 -->
                        <div class="col-12">
                            <button type="button"
                                    class="btn btn-primary btn-block btn-lg"
                                    id="btnResetPwSubmit">
                                완료
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include_once("../inc/modal.php");?>
<script src="<?=CDN_UTIL_URL?>/msg_err.js"></script>
<script src="<?=CDN_UTIL_URL?>/validation.js"></script>
<script>
    $(function () {
        const $form         = $('#resetPwForm');
        const $newPw        = $('#newPw');
        const $newPwConfirm = $('#newPwConfirm');
        const $pwWrap       = $newPw.closest('.form_wr');
        const $pwError      = $('#newPwError');
        const $btnSubmit    = $('#btnResetPwSubmit');

        const token = '<?=$_GET['id']?>';

        resetFieldState($pwWrap, $pwError);

        // ===============================
        // 완료 버튼 클릭
        // ===============================
        $btnSubmit.on('click', function () {
            console.log('비밀번호 재설정 완료 버튼 클릭');

            // 상태 초기화
            resetFieldState($pwWrap, $pwError);

            const pw    = $newPw.val();
            const pw2   = $newPwConfirm.val();

            // 1) 토큰 유효성 체크
            if (!token) {
                showError($pwWrap, $pwError, '유효하지 않은 접근입니다.');
                alert('유효하지 않은 접근입니다. 비밀번호 찾기부터 다시 진행해 주세요.');
                return;
            }

            // 2) 비밀번호 검증 (공통 유틸 사용)
            const pwResult = ValidationUtils.validatePassword(pw, pw2);

            if (!pwResult.valid) {
                showError($pwWrap, $pwError, pwResult.msg);
                // 포커스 이동
                if (!pw) {
                    $newPw.focus();
                } else {
                    $newPwConfirm.focus();
                }
                return;
            }

            // 검증 통과 → 성공 상태 표시
            clearError($pwWrap, $pwError);

            // ===============================
            // AJAX로 비밀번호 재설정 요청
            // ===============================
            $.ajax({
                url: '<?=AUTH_ACTIONS?>/find_update.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    act: 'reset_pw',
                    id_token: token,
                    mt_pwd: pw
                },
                beforeSend: () => {
                    console.log('비밀번호 재설정 AJAX 시작');
                    $btnSubmit.prop('disabled', true);
                },
                success: function (res) {
                    console.log('비밀번호 재설정 응답:', res);

                    if (res?.success) {
                        ModalUtil.alert({
                            title: '패스워드 찾기',
                            message: res.message || '비밀번호가 성공적으로 변경되었습니다.',
                            okText: '확인',
                            onOk: function () {
                                location.href = '<?=AUTH_PAGE?>/find_pw_cmp.php';
                            },
                        });
                    } else {
                        const msg = res?.message || '비밀번호 변경에 실패했습니다.';
                        showError($pwWrap, $pwError, msg);
                        ModalUtil.alert({
                            title: '패스워드 찾기',
                            message: res?.message || '비밀번호 변경에 실패했습니다.',
                            okText: '확인',
                            onOk: function () {
                            },
                        });
                    }
                },
                error: function () {
                    const msg = '비밀번호 변경 중 오류가 발생했습니다. 잠시 후 다시 시도해 주세요.';
                    showError($pwWrap, $pwError, msg);
                    ModalUtil.alert({
                        title: '패스워드 찾기',
                        message: msg,
                        okText: '확인',
                        onOk: function () {
                        },
                    });
                },
                complete: () => {
                    $btnSubmit.prop('disabled', false);
                }
            });
        });

        // 엔터키로 submit 방지
        $form.on('submit', e => e.preventDefault());
    });
</script>
