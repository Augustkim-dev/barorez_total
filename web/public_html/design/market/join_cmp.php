<?
$_SUB_HEAD_TITLE = "회원가입 완료";
$_GET['hd_pc'] = 'logout'; //PC hd  로그인시공백 로그아웃시 logout
$hd_num = ' '; //왼쪽메뉴 active 땜시 만듬
include_once("./inc/head.php");
?>


<div class="sub_pg pl-0 ">
    <div class="join_form_wr">
        <div class="hd_tit">
            <h2 class="tit_st1 d-flex align-items-center"><a href="#" onclick="history.back(); return false;" class="mr-4 line_h0 "><img src="./img/ico_back.svg" alt=" 뒤로가기"></a><span>회원가입 완료</span></h2>
        </div>
        <div class="join_form text-center">
            <img src="./img/login_img.png" alt=" " class="mt-5">
            <h3 class="tit_st1 mt_35">가맹점주 회원 요청이 완료되었습니다.</h3>
            <p class="tit_st4 mt-3 fw_300">최고 관리자 승인 후 회원가입 완료 처리됩니다. </p>
            <div class="text-center mt_50">
                <button type="button" class="btn btn-primary btn-lg btn-w1">확인</button>
            </div>

        </div>
    </div>

</div>





<? include_once("./inc/tail.php"); ?>