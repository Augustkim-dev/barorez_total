<div class="wrap">
    <div class="sub_pg pb_lg">
        <div class="container">
            <p class="tit_st3 mt-5">현재 비밀번호를 입력해주세요.</p>
            <form id="pwForm" novalidate>
                <div class="form_wr mt-5">
                    <div class="ip_tit required">
                        <h5>비밀번호</h5>
                    </div>
                    <div class="form-row">
                        <div class="col">
                            <input
                                    type="password"
                                    class="form-control"
                                    id="mt_pwd"
                                    name="mt_pwd"
                                    placeholder="비밀번호 입력(영소문자, 숫자 포함 8~16자)"
                                    autocomplete="current-password"
                                    required
                                    minlength="8"
                                    maxlength="16"
                            >
                        </div>
                    </div>
                    <div class="form-text text-danger mt-2" id="pw_err" style="display:none;"></div>
                </div>

                <div class="bottom_btn mt-5">
                    <div class="form-row">
                        <button type="submit" id="btnCheck" class="btn btn-primary btn-block btn-lg">
                            확인
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include_once("../inc/modal.php");?>
<script>
    $(function () {
        const $form     = $('#pwForm');
        const $input    = $('#mt_pwd');
        const $err      = $('#pw_err');
        const $btn      = $('#btnCheck');

        $form.on('submit', function(e) {
            e.preventDefault();

            // 초기화
            $err.hide().text('');
            $input.removeClass('is-invalid');

            let password = $.trim($input.val());

            if (!password) {
                showError('비밀번호를 입력해주세요.');
                $input.focus();
                return;
            }

            // 간단한 프론트 검증 (백엔드와 동일 규칙)
            if (!/^(?=.*[a-z])(?=.*\d)[A-Za-z\d!@#$%^&*()_+\-={}[\]:;"'<>,.?\/~`|\\]{8,16}$/.test(password)) {
                showError('영소문자와 숫자를 포함한 8~16자 비밀번호를 입력해주세요.');
                $input.focus();
                return;
            }

            // 버튼 비활성화 + 로딩 상태
            $btn.prop('disabled', true).text('확인 중...');

            $.ajax({
                url: '<?= MY_ACTIONS ?>/update.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    act: 'pass_check',
                    mt_pwd: password
                },
                success: function(res) {
                    if (res && res.success) {
                        // 성공 → 회원정보 수정 페이지로 이동
                        location.href = './myinfo.php';
                    } else {
                        showError(res?.message || '비밀번호가 일치하지 않습니다.');
                        $input.focus().select();
                        ModalUtil.alert({
                            title: '회원정보',
                            message: res?.message || '비밀번호가 일치하지 않습니다.',
                            okText: '확인',
                            onOk: function () {
                            },
                        });
                    }
                },
                error: function(xhr) {
                    console.error('[pass_check] AJAX error', xhr.responseText);
                    showError('서버와 통신 중 오류가 발생했습니다. 잠시 후 다시 시도해주세요.');
                },
                complete: function() {
                    $btn.prop('disabled', false).text('확인');
                }
            });
        });

        function showError(msg) {
            $err.text(msg).show();
            $input.addClass('is-invalid');
        }

        // 엔터키로도 제출
        $input.on('keypress', function(e) {
            if (e.which === 13) {
                $form.trigger('submit');
            }
        });
    });
</script>
