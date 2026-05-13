<?
$_SUB_HEAD_TITLE = "비밀번호 재설정";
$_GET['hd_pc'] = 'logout'; //PC hd  로그인시공백 로그아웃시 logout
$hd_num = ' '; //왼쪽메뉴 active 땜시 만듬
include_once("./inc/head.php");

?>


    <div class="sub_pg pl-0 ">
        <div class="join_form_wr">
            <div class="hd_tit">
                <h2 class="tit_st1 d-flex align-items-center"><a href="#" onclick="history.back(); return false;" class="mr-4 line_h0 "><img src="<?=DESIGN_HTTP?>/market/img/ico_back.svg" alt=" 뒤로가기"></a><span>비밀번호 재설정</span></h2>
            </div>
            <form class="join_form" id="frm_reset_pw" method="post" action="./find_update.php">
                <input type="hidden" name="act" value="reset_pw">

                <div class="join_box">

                    <div class="form_wr mt-5">
                        <div class="ip_tit required">
                            <h5>비밀번호</h5>
                        </div>
                        <input type="password" class="form-control" placeholder="비밀번호 입력" id="mb_pw" name="mb_pw">
                        <div class="form-text ip_invalid">비밀번호를 입력해주세요</div>
                    </div>

                    <div class="form_wr mt-5">
                        <div class="ip_tit required">
                            <h5>비밀번호 재입력</h5>
                        </div>
                        <input type="password" class="form-control" placeholder="비밀번호 재입력" id="mb_pw2" name="mb_pw2">
                        <div class="form-text ip_invalid">비밀번호가 일치하지않습니다.</div>
                    </div>

                    <div class="text-center mt-5">
                        <button type="button" class="btn btn-primary btn-lg btn-block" id="btnResetPw">완료</button>
                    </div>

                </div>
            </form>
        </div>

    </div>

<script src="<?=MARKET_HTTP?>/utils/formState.js"></script>
<script src="<?=MARKET_HTTP?>/utils/common.js"></script>

<script>
    console.log('[find_pw_reset] page loaded');

    $(function () {
        console.log('[find_pw_reset] document ready');

        const $pw = $('#mb_pw');
        const $pw2 = $('#mb_pw2');

        // 입력 시 상태 정리
        $pw.on('input', () => {
            console.log('[find_pw_reset] pw input');
            const pw = ($pw.val() || '').trim();
            if (pw) FormState.clearState($pw);

            const pw2 = ($pw2.val() || '').trim();
            if (pw2) {
                if (pw === pw2) FormState.setValid($pw2, '비밀번호가 일치합니다.');
                else FormState.setInvalid($pw2, '비밀번호가 일치하지않습니다.');
            }
        });

        $pw2.on('input', () => {
            console.log('[find_pw_reset] pw2 input');

            const pw = ($pw.val() || '').trim();
            const pw2 = ($pw2.val() || '').trim();

            if (!pw2) {
                FormState.setInvalid($pw2, '비밀번호 재입력을 입력해주세요.');
                return;
            }

            if (pw && pw === pw2) FormState.setValid($pw2, '비밀번호가 일치합니다.');
            else FormState.setInvalid($pw2, '비밀번호가 일치하지않습니다.');
        });

        // 완료 버튼 -> submit
        $('#btnResetPw').on('click', () => {
            console.log('[find_pw_reset] btnResetPw click -> submit');
            $('#frm_reset_pw').trigger('submit');
        });

        // submit (AJAX)
        $('#frm_reset_pw').on('submit', (e) => {
            e.preventDefault();
            console.log('[find_pw_reset] submit try');

            const pw = ($pw.val() || '').trim();
            const pw2 = ($pw2.val() || '').trim();

            if (!pw) {
                FormState.setInvalid($pw, '비밀번호를 입력해주세요');
                $pw.focus();
                return;
            }

            if (!validatePwRule(pw)) {
                FormState.setInvalid($pw, '비밀번호는 영소문/숫자 포함 8~16자여야 합니다.');
                $pw.focus();
                return;
            } else {
                FormState.clearState($pw);
            }

            if (!pw2) {
                FormState.setInvalid($pw2, '비밀번호 재입력을 입력해주세요.');
                $pw2.focus();
                return;
            }

            if (pw !== pw2) {
                FormState.setInvalid($pw2, '비밀번호가 일치하지않습니다.');
                $pw2.focus();
                return;
            } else {
                FormState.setValid($pw2, '비밀번호가 일치합니다.');
            }

            FormState.clearState($pw2, '비밀번호 변경중...');

            $.ajax({
                url: './find_update.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    act: 'reset_pw',
                    mb_pw: pw,
                    mb_pw2: pw2
                },
                success: (res) => {
                    console.log('[find_pw_reset] reset_pw success:', res);

                    if (res && res.success) {
                        location.href = './find_pw_cmp.php';
                    } else {
                        FormState.setInvalid($pw, (res && res.message) || '비밀번호 변경에 실패했습니다.');
                    }
                },
                error: (xhr) => {
                    console.log('[find_pw_reset] reset_pw error:', xhr);
                    FormState.setInvalid($pw, '서버 통신 오류가 발생했습니다.');
                }
            });
        });
    });
</script>

<? include_once("./inc/tail.php"); ?>
