<?
$_SUB_HEAD_TITLE = "비밀번호 재설정";
$_GET['hd_pc'] = 'logout'; //PC hd  로그인시공백 로그아웃시 logout
$hd_num = ' '; //왼쪽메뉴 active 땜시 만듬
include_once("./inc/head.php");
?>


<div class="sub_pg pl-0 ">
    <div class="join_form_wr">
        <div class="hd_tit">
            <h2 class="tit_st1 d-flex align-items-center"><a href="#" onclick="history.back(); return false;" class="mr-4 line_h0 "><img src="./img/ico_back.svg" alt=" 뒤로가기"></a><span>비밀번호 재설정</span></h2>
        </div>
        <div class="join_form">
            <div class="join_box">


                <div class="form_wr  mt-5 ip_valid">
                    <div class="ip_tit required">
                        <h5>비밀번호</h5>
                    </div>
                    <input type="text" class="form-control" placeholder="비밀번호 입력">
                    <div class="form-text ip_invalid">비밀번호를 입력해주세요</div>
                </div>
                <div class="form_wr  mt-5 ip_valid">
                    <div class="ip_tit required">
                        <h5>비밀번호 재입력 </h5>
                    </div>
                    <input type="text" class="form-control" placeholder="비밀번호 재입력">
                    <div class="form-text ip_invalid">비밀번호가 일치하지않습니다.</div>
                </div>
                <div class="text-center mt-5">
                    <button type="button" class="btn btn-primary btn-lg btn-block" onclick="location.href='./find_pw_cmp.php'">완료</button>
                </div>
            </div>
        </div>
    </div>

</div>





<? include_once("./inc/tail.php"); ?>