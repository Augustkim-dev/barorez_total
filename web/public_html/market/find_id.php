<?
$_SUB_HEAD_TITLE = "아이디 찾기";
$_GET['hd_pc'] = 'logout'; //PC hd  로그인시공백 로그아웃시 logout
$hd_num = ' '; //왼쪽메뉴 active 땜시 만듬
include_once("./inc/head.php");
?>


    <div class="sub_pg pl-0 ">
        <div class="join_form_wr">
            <div class="hd_tit">
                <h2 class="tit_st1 d-flex align-items-center"><a href="#" onclick="history.back(); return false;" class="mr-4 line_h0 "><img src="<?=DESIGN_HTTP?>/market/img/ico_back.svg" alt=" 뒤로가기"></a><span>아이디 찾기</span></h2>
            </div>
            <div class="join_form">
                <form class="join_box" id="frm_find_id" method="post" action="./find_update.php">
                    <input type="hidden" name="act" value="find_id">
                    <input type="hidden" name="hp_cert_ok" id="hp_cert_ok" value="N">
                    <div class="form_wr">
                        <div class="ip_tit required">
                            <h5>이름 </h5>
                        </div>
                        <input type="text" class="form-control" placeholder="이름 입력" name="mb_name" id="mb_name">
                        <div class="form-text ip_invalid">이름을 입력해주세요.</div>
                    </div>
                    <div class="form_wr mt-5">
                        <div class="ip_tit required">
                            <h5>휴대폰번호</h5>
                        </div>
                        <div class="form-row">
                            <div class="col">
                                <input type="text" class="form-control" placeholder="‘-’ 없이 숫자만 입력" name="mb_hp" id="mb_hp">
                            </div>
                            <div class="col-4">
                                <button type="button" class="btn btn-secondary btn-block px-1" id="btnSendHpCode">인증 요청</button>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col mt-3 position-relative">
                                <p class="time_lim" id="hp_timer">04:25</p>
                                <input type="text" class="form-control" placeholder="인증번호 입력" name="hp_code" id="hp_code">
                            </div>
                            <div class="col-4 mt-3">
                                <button type="button" class="btn btn-primary btn-block" id="btnVerifyHpCode" disabled>확인</button>
                            </div>
                        </div>
                        <div class="form-text ip_invalid">인증확인 후 인증번호를 입력해 주세요.</div>
                    </div>
                    <div class="text-center mt-5">
                        <button type="button" class="btn btn-primary btn-lg btn-block" id="btnFindId">아이디 찾기</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

<script src="<?=MARKET_HTTP?>/utils/formState.js"></script>
<script src="<?=MARKET_HTTP?>/utils/hpAuth.js"></script>
<script>
    console.log('[find_id] page loaded');

    $(function () {
        console.log('[find_id] document ready');

        // ✅ 휴대폰 인증 공통 init
        HPAuth.init({
            ajaxUrl: './find_update.php',
            hpInput: '#mb_hp',
            codeInput: '#hp_code',
            sendBtn: '#btnSendHpCode',
            verifyBtn: '#btnVerifyHpCode',
            timerEl: '#hp_timer',
            certOkInput: '#hp_cert_ok',
            timerSec: 300,
            lockOnSuccess: true, // 필요하면 false로
        });

        // ✅ 아이디 찾기 버튼 -> submit
        $('#btnFindId').on('click', () => {
            console.log('[find_id] btnFindId click -> trigger submit');
            $('#frm_find_id').trigger('submit');
        });

        // ✅ 이름 입력 시 에러 제거
        $('#mb_name').on('input', () => {
            const name = ($('#mb_name').val() || '').trim();
            if (name) FormState.clearState($('#mb_name'));
        });

        // ✅ 아이디 찾기 submit (휴대폰 인증은 HPAuth가 관리)
        $('#frm_find_id').on('submit', (e) => {
            e.preventDefault();
            console.log('[find_id] submit try');

            const name = ($('#mb_name').val() || '').trim();
            const hp = ($('#mb_hp').val() || '').replace(/[^0-9]/g, '');
            const hpOk = $('#hp_cert_ok').val() === 'Y';

            if (!name) {
                FormState.setInvalid($('#mb_name'), '이름을 입력해주세요.');
                $('#mb_name').focus();
                return;
            } else {
                FormState.clearState($('#mb_name'));
            }

            if (!hp) {
                FormState.setInvalid($('#mb_hp'), '휴대폰번호를 입력해 주세요.');
                $('#mb_hp').focus();
                return;
            }

            if (!hpOk) {
                FormState.setInvalid($('#mb_hp'), '휴대폰 인증을 완료해 주세요.');
                return;
            }

            FormState.clearState($('#mb_hp'), '정보 확인중...');

            $.ajax({
                url: './find_update.php',
                type: 'POST',
                dataType: 'json',
                data: { act: 'find_id', mb_name: name, mb_hp: hp },
                success: (res) => {
                    console.log('[find_id] submit success:', res);
                    if (res && res.success) {
                        location.href = 'find_id_result.php';
                    } else {
                        FormState.setInvalid($('#mb_hp'), (res && res.message) || '일치하는 정보가 없습니다.');
                    }
                },
                error: (xhr) => {
                    console.log('[find_id] submit error:', xhr);
                    FormState.setInvalid($('#mb_hp'), '서버 통신 오류가 발생했습니다.');
                },
            });
        });
    });
</script>

<? include_once("./inc/tail.php"); ?>
