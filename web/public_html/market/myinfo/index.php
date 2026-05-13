<?
$_SUB_HEAD_TITLE = "내정보 수정 확인";
$_GET['hd_pc'] = ' ';
$hd_num = 'setting';
$hd_num2 = 'setting1';
$hd_left = ' ';
include_once("../inc/head.php");
include_once("../inc/header.php");
include_once("../inc/modal.php");
?>

    <!-- 왼쪽 메뉴-->
<? include_once("../inc/left_menu.php"); ?>

    <div class="sub_pg ">
        <div class="sub_wr">
            <div class="hd_tit2 fs_16 flex-row">
                <h2 class="tit_st1 d-flex align-items-center mr-5 ">
                    <span>내정보 수정</span>
                </h2>
            </div>

            <div class="card join_form">
                <h3 class="text-center tit_st3">개인정보 보호를 위해<br>
                    내정보 수정 전 <span class="text-primary">비밀번호를 한 번 더 확인</span>합니다.</h3>
                <div class="join_box">
                    <div class="form_wr mt-5">
                        <div class="ip_tit required">
                            <h5>비밀번호</h5>
                        </div>

                        <div class="form-row">
                            <div class="col">
                                <input type="password" class="form-control" id="password_input" placeholder="비밀번호 입력">
                            </div>
                        </div>

                        <div class="text-danger mt-2" id="error_message" style="display:none;">
                            비밀번호가 일치하지 않습니다.
                        </div>

                        <button type="button" class="btn btn-secondary btn-lg btn-block mt-4" id="check_pw_btn">
                            확인
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#check_pw_btn').on('click', function() {
                const password = $('#password_input').val().trim();

                if (!password) {
                    $('#error_message').text('비밀번호를 입력해주세요.').show();
                    return;
                }

                $.ajax({
                    url: './update.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        act: 'check_password',
                        password: password
                    },
                    success: function(res) {
                        if (res.success) {
                            // 비밀번호 맞음 → 수정 페이지로 이동
                            location.href = '../myinfo-edit';
                        } else {
                            // 비밀번호 틀림
                            $('#error_message').text(res.message || '비밀번호가 일치하지 않습니다.').show();
                            $('#password_input').val('').focus();
                        }
                    },
                    error: function() {
                        alert('서버와의 연결에 문제가 발생했습니다.');
                    }
                });
            });
        });
    </script>

<? include_once("../inc/tail.php"); ?>
