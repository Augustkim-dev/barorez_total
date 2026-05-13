<?
$_SUB_HEAD_TITLE = "비밀번호 찾기";
$_GET['hd_pc'] = 'logout'; //PC hd  로그인시공백 로그아웃시 logout
$hd_num = ' '; //왼쪽메뉴 active 땜시 만듬
include_once("./inc/head.php");
?>


    <div class="sub_pg pl-0 ">
        <div class="join_form_wr">
            <div class="hd_tit">
                <h2 class="tit_st1 d-flex align-items-center"><a href="#" onclick="history.back(); return false;" class="mr-4 line_h0 "><img src="<?=DESIGN_HTTP?>/market/img/ico_back.svg" alt=" 뒤로가기"></a><span>비밀번호 찾기</span></h2>
            </div>
            <form class="join_form" id="frm_find_pw" method="post" action="./find_update.php">
                <input type="hidden" name="act" value="find_pw">
                <input type="hidden" name="hp_cert_ok" id="hp_cert_ok" value="N">

                <div class="join_box">
                    <div class="form_wr">
                        <div class="ip_tit required">
                            <h5>아이디 </h5>
                        </div>
                        <!-- ✅ id/name 추가 -->
                        <input type="text" class="form-control" placeholder="아이디 입력" id="mb_id" name="mb_id">
                        <div class="form-text ip_invalid">아이디를 입력해 주세요.</div>
                    </div>

                    <div class="form_wr mt-5 ip_invalid">
                        <div class="ip_tit required">
                            <h5>휴대폰번호</h5>
                        </div>

                        <div class="form-row">
                            <div class="col">
                                <input type="text" class="form-control" placeholder="‘-’ 없이 숫자만 입력" id="mb_hp" name="mb_hp">
                            </div>
                            <div class="col-4">
                                <button type="button" class="btn btn-secondary btn-block  px-1" id="btnSendHpCode">인증 요청</button>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col mt-3 position-relative">
                                <p class="time_lim" id="hp_timer">00:00</p>
                                <input type="text" class="form-control" placeholder="인증번호 입력" id="hp_code" name="hp_code">
                            </div>
                            <div class="col-4 mt-3">
                                <button type="button" class="btn btn-primary btn-block" id="btnVerifyHpCode" disabled>확인</button>
                            </div>
                        </div>
                        <div class="form-text ip_invalid">인증 요청 후 인증번호를 입력해 주세요.</div>
                    </div>

                    <div class="text-center mt-5">
                        <!-- ✅ 기존 onclick 제거 권장: ajax/검증 후 이동해야 하니까 -->
                        <button type="button" class="btn btn-primary btn-lg btn-block" id="btnFindPw">확인</button>
                    </div>
                </div>
            </form>
        </div>

    </div>

<script src="<?=MARKET_HTTP?>/utils/formState.js"></script>
<script src="<?=MARKET_HTTP?>/utils/hpAuth.js"></script>
<script>
    console.log('[find_pw] page loaded');

    $(function () {
        console.log('[find_pw] document ready');

        // ✅ 숫자만 입력 강제 (FormState 공통)
        FormState.bindOnlyNumber('#mb_hp');
        FormState.bindOnlyNumber('#hp_code');

        // ✅ 휴대폰 인증 공통 모듈 init
        HPAuth.init({
            ajaxUrl: './find_update.php',
            hpInput: '#mb_hp',
            codeInput: '#hp_code',
            sendBtn: '#btnSendHpCode',
            verifyBtn: '#btnVerifyHpCode',
            timerEl: '#hp_timer',
            certOkInput: '#hp_cert_ok',
            timerSec: 300,
            lockOnSuccess: true, // 인증 성공 후 입력/재요청 잠금(아이디찾기와 동일)
        });

        // ✅ 아이디 입력 시 에러 제거
        $('#mb_id').on('input', () => {
            const id = ($('#mb_id').val() || '').trim();
            if (id) FormState.clearState($('#mb_id'));
        });

        // ✅ "확인" 버튼 클릭 -> submit 트리거
        $('#btnFindPw').on('click', () => {
            console.log('[find_pw] btnFindPw click -> trigger submit');
            $('#frm_find_pw').trigger('submit');
        });

        // ✅ 비밀번호 찾기 submit (AJAX)
        $('#frm_find_pw').on('submit', (e) => {
            e.preventDefault();
            console.log('[find_pw] submit try');

            const mbId = ($('#mb_id').val() || '').trim();
            const hp = ($('#mb_hp').val() || '').replace(/[^0-9]/g, '');
            const hpOk = $('#hp_cert_ok').val() === 'Y';

            // 1) 아이디 검증
            if (!mbId) {
                FormState.setInvalid($('#mb_id'), '아이디를 입력해 주세요.');
                $('#mb_id').focus();
                return;
            } else {
                FormState.clearState($('#mb_id'));
            }

            // 2) 휴대폰번호 검증
            if (!hp) {
                FormState.setInvalid($('#mb_hp'), '휴대폰번호를 입력해 주세요.');
                $('#mb_hp').focus();
                return;
            }

            // 3) 휴대폰 인증 완료 여부
            if (!hpOk) {
                FormState.setInvalid($('#mb_hp'), '휴대폰 인증을 완료해 주세요.');
                return;
            }

            // 진행 메시지
            FormState.clearState($('#mb_hp'), '정보 확인중...');

            $.ajax({
                url: './find_update.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    act: 'find_pw',
                    mb_id: mbId,
                    mb_hp: hp,
                },
                success: (res) => {
                    console.log('[find_pw] submit success:', res);

                    if (res && res.success) {
                        // ✅ 성공이면 리셋 페이지로 이동 (기존 플로우 유지)
                        location.href = './find_pw_reset.php';
                    } else {
                        // 실패 문구는 휴대폰 블록에 노출 (퍼블 규칙 그대로)
                        FormState.setInvalid($('#mb_hp'), (res && res.message) || '일치하는 정보가 없습니다.');
                    }
                },
                error: (xhr) => {
                    console.log('[find_pw] submit error:', xhr);
                    FormState.setInvalid($('#mb_hp'), '서버 통신 오류가 발생했습니다.');
                },
            });
        });
    });
</script>

<? include_once("./inc/tail.php"); ?>
