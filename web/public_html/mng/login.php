<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";

if (isset($_SESSION['mng']['mt_level']) && $_SESSION['mng']['mt_level'] >= 4) {
    alert("로그아웃을 진행하겠습니다", "/mng/logout.php");
}

?>
<!-- PAGE WRAPPER -->
<div class="page">
    <!-- PAGE CONTENT WRAPPER -->
    <div class="page__content" id="page-content">
        <!-- PAGE LOGIN CONTAINER -->
        <div class="important-container login-container">
            <div class="content">
                <a href="../index.php" class="logo-holder logo-holder--lg logo-holder--wide">
                    <div class="logo-text">
                        <strong class="text-primary">#</strong> <?php echo ADMIN_NAME?> <strong>Login</strong>
                    </div>
                </a>
                <p class="caption text-center margin-bottom-30">
                * 이 페이지는 접근과 동시에 IP주소가 자동저장됩니다.<br />관계자 이외에 접근시도는 해킹시도로 의심, 추적되어 불이익을 당할 수 도 있습니다.
                </p>
                <form class="pt-3" role="form" method="post" name="frm_login" id="frm_login" action="./login_update.php" target="hidden_ifrm">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="mt_id" id="mt_id" class="form-control" placeholder="아이디">
                    </div>
                    <div class="form-group margin-bottom-20">
                        <label>Password</label>
                        <input type="password" name="mt_pass" id="mt_pass" class="form-control" placeholder="Your password" autocomplete="비밀번호">
                    </div>
                    <div class="form-group margin-bottom-30">
                        <div class="form-row">
                            <div class="col-2">
                            </div>
                            <div class="col-8">
                                <button type="submit" class="btn btn-primary btn-block">로그인</button>
                            </div>
                        </div>
                    </div>
                    <script>
                        $(document).ready(function() {
                            $("#frm_login").validate({
                                submitHandler: function(form) {
                                    $('#splinner_modal').modal('toggle');
                                    return true;
                                },
                                rules: {
                                    mt_id: {
                                        required: true
                                    },
                                    mt_pass: {
                                        required: true
                                    }
                                },
                                messages: {
                                    mt_id: {
                                        required: "아이디를 입력해주세요"
                                    },
                                    mt_pass: {
                                        required: "비밀번호를 입력해주세요."
                                    }
                                },
                                errorElement: 'span',
                                errorPlacement: function(error, element) {
                                    error.addClass('invalid-feedback');
                                    element.closest('.form-group').append(error);
                                },
                                highlight: function(element, errorClass, validClass) {
                                    $(element).addClass('is-invalid');
                                },
                                unhighlight: function(element, errorClass, validClass) {
                                    $(element).removeClass('is-invalid');
                                }
                            });
                        });
                    </script>
                </form>
            </div>
        </div>
        <!-- PAGE LOGIN CONTAINER -->
        <!-- PAGE CONTENT CONTAINER -->
        <div class="content d-none d-lg-block" id="content" style="background: url(assets/img/backgrounds/bridge.jpg) left center no-repeat; background-size: 100% auto">
        </div>
        <!-- //END PAGE CONTENT CONTAINER -->
    </div>
    <!-- //END PAGE CONTENT -->
</div>
<!-- //END PAGE WRAPPER -->
<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/foot.inc.php";
?>
