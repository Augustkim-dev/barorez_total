<?
$_SUB_HEAD_TITLE = "메인화면";
$_GET['hd_pc'] = 'logout'; //PC hd  로그인시공백 로그아웃시 logout
$hd_num = ' '; //왼쪽메뉴 active 땜시 만듬
include_once("./inc/head.php");
?>


<div class="sub_pg pl-0   bg-white">

    <div class="login_wr">
        <div class="login_l text-center">
            <div>
                <img src="./img/login_ico.png" alt=" 로고이미지">
                <p class="mt-5 text-white fs_20">바쁜 사장님들을 위한 간편한 주문 접수!</p>
            </div>
        </div>

        <div class="login_r">
            <div class="sign_box_wp">
                <h2 class="tit_st1 text-center">매장 관리자 로그인</h2>
                <div class="sign_box">
                    <form role="form" method="post" name="frm_login" id="frm_login" action="./login_update.php" target="hidden_ifrm">
                        <div class="ip_wr">
                            <input type="text" class="form-control" placeholder="아이디 입력" id="mt_id" name="mt_id">
                            <div class="form-text ip_invalid">아이디를 입력해 주세요.</div>
                        </div>
                        <div class="ip_wr mt-4">
                            <input type="password" class="form-control" placeholder="비밀번호 입력" id="mt_pwd" name="mt_pwd">
                            <div class="form-text ip_invalid">비밀번호를 입력해 주세요.</div>
                        </div>
                        <button type="button" class="btn btn-primary btn-lg btn-block mt-4" onclick="location.href='./index.php'">로그인</button>

                    </form>

                    <div class=" login_fbtn   d-flex justify-content-center mt-5">
                        <a href="./find_id.php">아이디 찾기</a>
                        <a href="./find_pw.php">비밀번호 찾기</a>
                        <a href="./join_form.php">회원가입</a>
                    </div>
                    <div class="fs_15 tg_500 sign_warii text-center">이 페이지는 접근과 동시에 IP주소가 자동저장됩니다.<br>
                        관계자 이외에 접근시도는 해킹시도로 의심, 추적되어 불이익을
                        당할 수 도 있습니다.</div>
                </div>
            </div>
        </div>


    </div>

</div>


<? include_once("./inc/tail.php"); ?>