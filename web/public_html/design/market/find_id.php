<?
$_SUB_HEAD_TITLE = "아이디 찾기";
$_GET['hd_pc'] = 'logout'; //PC hd  로그인시공백 로그아웃시 logout
$hd_num = ' '; //왼쪽메뉴 active 땜시 만듬
include_once("./inc/head.php");
?>


<div class="sub_pg pl-0 ">
    <div class="join_form_wr">
        <div class="hd_tit">
            <h2 class="tit_st1 d-flex align-items-center"><a href="#" onclick="history.back(); return false;" class="mr-4 line_h0 "><img src="./img/ico_back.svg" alt=" 뒤로가기"></a><span>아이디 찾기</span></h2>
        </div>
        <div class="join_form">
            <div class="join_box">
                
                    <div class="form_wr">
                        <div class="ip_tit required">
                            <h5>이름 </h5>
                        </div>
                        <input type="text" class="form-control" placeholder="이름 입력">
                        <div class="form-text ip_invalid">반대문구</div>
                    </div>
                    <div class="form_wr mt-5 ip_invalid">
                        <div class="ip_tit required">
                            <h5>휴대폰번호</h5>
                        </div>
                        <div class="form-row">
                            <div class="col">
                                <input type="text" class="form-control" placeholder="‘-’ 없이 숫자만 입력">
                            </div>
                            <div class="col-4">
                                <button type="button" class="btn btn-secondary btn-block  px-1">인증 요청</button>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col mt-3 position-relative">
                                <p class="time_lim">04:25</p>
                                <input type="text" class="form-control" placeholder="인증번호 입력">
                            </div>
                            <div class="col-4 mt-3">
                                <button type="button" class="btn btn-primary btn-block  " disabled="">확인</button>
                            </div>
                        </div>
                        <div class="form-text ip_invalid">오류 텍스트</div>
                    </div>
                 <div class="text-center mt-5">
                <button type="button" class="btn btn-primary btn-lg btn-block"  onclick="location.href='./find_id_cmp.php'">아이디 찾기</button>
            </div>
            </div>
        </div>
    </div>

</div>





<? include_once("./inc/tail.php"); ?>