<?
$_SUB_HEAD_TITLE = "회원가입";
$_GET['hd_pc'] = ''; //PC hd 메뉴있음1, 메뉴없음 공백
$_GET['hd_num'] = '5'; //모바일 hd 1~n까지 있음
$_GET['bt_menu'] = ''; //모바일 하단메뉴 있음1, ft 없음 공백
$_GET['ft_none'] = ''; //모바일 ft 있음1, ft 없음 공백
include_once("./inc/head.php");
?>

<div class="wrap">
    <div class="sub_pg">
        <div class="sign_pg">
            <div class="sign_wr container">
                <button class="btn btn-link d-none d-lg-flex mb-4" type="button" onclick="history.back()">
                    <img class="mr-2" style="width:2.0rem;" src="./img/ic_back_pc.png" alt="뒤로가기">
                    <span class="text-gray2">이전페이지</span>
                </button>
                <div class="tit_h2 mb-5"><span class="text-primary">회원정보</span><br>정보를 입력해 주세요</div>
                <div class="sign_box">
                    <form role="form" method="post" name="frm_sign" id="frm_sign" action="./sign01_update.php" target="hidden_ifrm">

                        <!-- <ul class="list_style_1 fs_15 mt-5">
                            <li>
                                <span class="ip_tit required">
                                    <h5>이름</h5>
                                </span>
                                <div class="text_dynamic" id="name_div">홍길동</div>
                                <input type="hidden" name="mt_name" id="mt_name" value="홍길동">
                            </li>
                            <li>
                                <span class="ip_tit required">
                                    <h5>휴대폰 번호</h5>
                                </span>
                                <div class="text_dynamic" id="hp_div">010-4565-4565</div>
                                <input type="hidden" name="mt_hp" id="mt_hp" value="홍길동">

                            </li>
                            <li>
                                <span class="ip_tit required">
                                    <h5>생년월일</h5>
                                </span>
                                <div class="text_dynamic" id="birth_div">1990-12-24</div>
                                <input type="hidden" name="mt_birth" id="mt_birth" value="홍길동">

                            </li>
                        </ul> -->
						<div class="ip_wr">
                            <div class="ip_tit required">
                                <h5>실명인증</h5>
                            </div>
                            <div class="form-row">
                                <div class="col-6">
                                    <input type="text" class="form-control" placeholder="이름" id="id_input" name="mt_id" maxlength="16">
                                </div>
                                <div class="col-6">
                                    <input type="number" class="form-control" placeholder="생년월일" id="id_input" name="mt_id" maxlength="16">
                                </div>
                                <div class="col-12 mt-3">
                                    <input type="number" class="form-control" placeholder="휴대폰 번호" id="id_input" name="mt_id" maxlength="16">
                                </div>
                                <div class="col-12 mt-3">
                                    <button type="button" class="btn btn-outline-light btn-block"
                                        onclick="f_id_check()">실명인증</button>
                                </div>
                            </div>
                            <div class="form-text ip_invalid">이름을 입력해주세요</div>
                            <div class="form-text ip_invalid">휴대폰번호를 입력해주세요</div>
                            <div class="form-text ip_invalid">생년월일을 입력해주세요</div>
                        </div>
						

                        <div class="ip_wr mt-5" id="id_div">
                            <div class="ip_tit required">
                                <h5>아이디</h5>
                            </div>
                            <div class="form-row">
                                <div class="col-9">
                                    <input type="text" class="form-control" placeholder="아이디 입력" id="id_input" name="mt_id" maxlength="16">
                                </div>
                                <div class="col-3">
                                    <button type="button" class="btn btn-outline-light btn-block"
                                        onclick="f_id_check()">인증요청</button>
                                </div>
                            </div>
                            <div class="form-text ip_invalid">6~16자의 영문 소문자, 숫자로 입력해 주세요.</div>
                        </div>

                        <div class="ip_wr mt-5" id="pw_div">
                            <div class="ip_tit required">
                                <h5>비밀번호</h5>
                            </div>
                            <div class="form-row">
                                <div class="col-12">
                                    <input type="password" class="form-control"
                                        placeholder="비밀번호 입력 (영문, 숫자, 특수문자 조합 8~20자)" maxlength="20" id="pw_input" name="mt_pwd"
                                        onkeyup="f_pw_change()">
                                </div>
                                <div class="col-12 mt_8">
                                    <input type="password" class="form-control" placeholder="비밀번호 재입력" maxlength="20"
                                        id="re_pw_input" onkeyup="f_re_pw_change()">
                                </div>
                            </div>
                            <div class="form-text ip_invalid">영문, 숫자, 특수문자 조합 비밀번호를 입력해 주세요.</div>
                        </div>


                        <button type="button" class="btn btn-primary btn-block mt_20" onclick="location.href='./sign01.php'">다음</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- 토스트 Toast -->
<div id="Toast2" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true" data-delay="2000">
    <div class="toast-body">
        <p>인증되었습니다.</p>
    </div>
</div>

<div id="Toast3" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true" data-delay="2000">
    <div class="toast-body">
        <p>이미 존재하는 아이디입니다.</p>
    </div>
</div>

<script>
    // 토스트 toast
    // const toastTrigger = document.getElementById('ToastBtn2')
    const toastToast2 = document.getElementById('Toast2');
    const toastToast3 = document.getElementById('Toast3');

    // if (toastTrigger) {
    // 		toastTrigger.addEventListener('click', () => {
    // 		const toast_confirm = new bootstrap.Toast(toastToast);
    // 		toast_confirm.show();
    // 	});
    // }


    var idRegExp = /^[a-z0-9_]{6,16}$/;
    var pwRegExp = /^(?=.*[a-zA-Z])(?=.*\d|.*[\W_])[A-Za-z\d\W_]{8,20}$/;

    var idCheck = false;
    var pwCheck = false;
    var rePwCheck = false;
    //임시
    var authCheck = true;

    var checkIdStr = null;


    function f_id_check() {

        var id = $('#id_input').val();

        if (!idRegExp.test(id)) {
            $('#id_div').addClass('ip_invalid');
            $('#id_div').removeClass('ip_valid');
            return;
        }

        var data = {
            'method': 'proc_check_id',
            'mt_id': $('#id_input').val()
        };

        $.ajax({
            type: 'POST',
            url: './json/proc_json.php',
            dataType: 'json',
            contentType: 'application/x-www-form-urlencoded',
            data: data,
            success: function (result) {
                console.log(result);

                if (result['result'] == 'Y') {
                    $('#id_div').addClass('ip_valid');
                    $('#id_div').removeClass('ip_invalid');
                    idCheck = true;

                    const toast_confirm = new bootstrap.Toast(toastToast2);
                    toast_confirm.show();
                    checkIdStr = $('#id_input').val();

                }
                else {
                    // $('#id_div').removeClass('ip_valid');
                    // $('#id_div').addClass('ip_invalid');

                    const toast_confirm = new bootstrap.Toast(toastToast3);
                    toast_confirm.show();

                    idCheck = false;
                    checkIdStr = null;

                }
            },
            error: function (request, status, error) {

            }
        });
    };

    function f_pw_change() {
        var pw = $('#pw_input').val();

        if (!pwRegExp.test(pw)) {
            $('#pw_div').addClass('ip_invalid');
            $('#pw_div').removeClass('ip_valid');

            pwCheck = false;
        }
        else {
            $('#pw_div').removeClass('ip_invalid');
            $('#pw_div').addClass('ip_valid');

            pwCheck = true;
        }

        f_re_pw_change();

    }

    function f_re_pw_change() {
        var pw = $('#pw_input').val();
        var re_pw = $('#re_pw_input').val();

        if (!isEmpty(re_pw) && pw === re_pw) {

            rePwCheck = true;
        }
        else {

            rePwCheck = false;
        }
    }

    function f_join() {

        if (!authCheck) {
            jalert('실명인증을 진행해 주세요.');
            return;
        }

        if (!idCheck) {
            jalert('아이디를 중복체크 해주세요.');
            return;
        }

        if (checkIdStr != $('#id_input').val()) {
            jalert('아이디를 중복체크 해주세요.');
            return;
        }

        if (!pwCheck) {
            jalert('비밀번호를 확인해 주세요.');
            return;
        }

        if (!rePwCheck) {
            jalert('비밀번호를 확인해 주세요.');
            return;
        }



        $('#frm_sign').submit();

        // location.replace('./sign02.php');
    }
</script>


<? include_once("./inc/tail.php"); ?>