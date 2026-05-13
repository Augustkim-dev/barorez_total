<?
$_SUB_HEAD_TITLE = "비밀번호 변경완료";
$_GET['hd_pc'] = 'logout'; //PC hd  로그인시공백 로그아웃시 logout
$hd_num = ' '; //왼쪽메뉴 active 땜시 만듬
include_once("./inc/head.php");
?>


    <div class="sub_pg pl-0 ">
        <div class="join_form_wr">
            <div class="hd_tit">
                <h2 class="tit_st1 d-flex align-items-center"><a href="#" onclick="history.back(); return false;" class="mr-4 line_h0 "><img src="<?=DESIGN_HTTP?>/market/img/ico_back.svg" alt=" 뒤로가기"></a><span>비밀번호 변경 완료</span></h2>
            </div>
            <div class="join_form text-center">
                <img src="<?=DESIGN_HTTP?>/market/img/login_img2.svg" alt=" " class="mt-5">
                <h3 class="tit_st1 mt_35">비밀번호 변경 완료 되었습니다.</h3>
                <p class="tit_st4 mt-3 fw_300">로그인 후 이용가능합니다.</p>
                <div class="d-flex  justify-content-center mt_40 btn_group">
                    <button type="button" class="btn btn-primary btn-lg btn-block btn-w1" onclick="location.href='./login.php'">로그인</button>

                </div>

            </div>
        </div>

    </div>





<? include_once("./inc/tail.php"); ?>
