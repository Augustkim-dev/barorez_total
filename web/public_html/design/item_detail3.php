<?
$_SUB_HEAD_TITLE = " "; //헤더에 타이틀명이 없을경우 공백
$_GET['hd_num'] = ' '; //모바일 hd 1~n까지 있음
$_GET['bt_menu'] = ''; //모바일 하단메뉴 있음1, 없음 공백
include_once("./inc/head.php");
?>

<div class="hd_m hd_trans align-items-center  ">
    <div class=" "><button class="hd_btn btn2" type="button" onclick="history.back()"><img src="./img/ic_back.png" alt="뒤로가기"></button></div><!-- 이전 결과값이 아닌 이전페이지로 이동되어야 합니다. -->
    <div class="fw_700  line1_text  ">해물칼국수해물칼국수해물칼국수해물해물칼국수해물칼국수해물칼국수해물칼국수해물칼국수해물칼국수해물칼국수해물칼국수칼국수</div>
    <div class="hd_btn"></div>
</div>
<div class="wrap">
    <div class="sub_pg pb_lg pt-0">
        <div class="rect">
            <img class=" " src="./img/pr_sample03.jpg" alt="상품사진">
        </div>
        <div class="container pt-5 pb-5">
            <h2 class="tit_st2">(대표메뉴)해물칼국수</h2>
            <p class="tg_400 mt-4">남해 앞바다에서 당일 조업한 국내산 바지락과 디포리, 멸치 다시마 등 6가지 육수재료로 정성껏 만들었습니다!</p>
            <div class="d-flex align-items-end justify-content-between mt-3 ">
                <p class="fw_600">가격</p>
                <p class="tit_st2">20,500원</p>
            </div>
        </div>
        <div class="bar">
        </div>
        <div class="container pt_20 pb_20">
            <div class="d-flex align-items-end justify-content-between  ">
                <p class="fw_600">수량</p>
                <div class="item_opt_counter">
                    <button type="button" class="btn item_opt_counter_btn  " disabled=""><!-- 수량이 0일때 -->
                        <img src="./img/ico_decrease.svg" alt="감소">
                    </button>
                    <input type="txt" class="quantity" value="0">
                    <button type="button" class="btn item_opt_counter_btn  ">
                        <img src="./img/ico_increase.svg" alt="증가">
                    </button>
                </div>
            </div>
        </div>
        <div class="bar">
        </div>
        <div class="item_op_wp">
            <dl class="">
                <dt class="tit_st3 mb-4">맵기 선택 <span class="text-primary fs_15 ml-2">필수</span></dt>
                <dd class="opt_checks_wp">
                    <div class="checks opt_checks">
                        <label>
                            <input type="checkbox" name="chk1">
                            <span class="ic_box"></span>
                            <div class="chk_p">
                                <p>1단계 </p>
                            </div>
                            <p class="fw_700 flex-shrink-0 item_opmm">20,500원</p>
                        </label>
                    </div>
                    <div class="checks opt_checks">
                        <label>
                            <input type="checkbox" name="chk1">
                            <span class="ic_box"></span>
                            <div class="chk_p">
                                <p>2단계</p>
                            </div>
                            <p class="fw_700 flex-shrink-0 item_opmm">20,500원</p>
                        </label>
                    </div>
                </dd>

            </dl>
            <dl class="">
                <dt class="tit_st3 mb-4">토핑 추가 <span class="tg_400 fs_15 ml-2">선택</span></dt>
                <dd class="opt_checks_wp">
                    <div class="checks opt_checks">
                        <label>
                            <input type="checkbox" name="chk1">
                            <span class="ic_box"></span>
                            <div class="chk_p">
                                <p>진짜맛있는 김치찌게를 1인분 통째로 드립니다. 한번 잡솨봐</p>
                            </div>
                            <p class="fw_700 flex-shrink-0 item_opmm">20,500원</p>
                        </label>
                    </div>
                    <div class="checks opt_checks">
                        <label>
                            <input type="checkbox" name="chk1">
                            <span class="ic_box"></span>
                            <div class="chk_p">
                                <p>핵불닭 매운맛</p>
                            </div>
                            <p class="fw_700 flex-shrink-0 item_opmm">20,500원</p>
                        </label>
                    </div>
                </dd>
            </dl>
        </div>
    </div>



    <!--메뉴담기를 누르면 장바구니가 아닌 매장 메인 홈으로 이동 / 하단에 장바구니 버튼이나옴-->

    <div class="bottom_btn  ">
        <div class="form-row">
            <div class="col-12"><button type="button" class="btn btn-primary btn-block btn-lg" onclick="location.href='./cart3.php'">24,000원 담기 </button></div>
        </div>
    </div>
</div>


<!-- 상단메뉴 스크롤시 변함-->
<script>
    $(window).on("scroll", function() {
        if ($(this).scrollTop() > 0) {
            $(".hd_m").removeClass("hd_trans");
        } else {
            $(".hd_m").addClass("hd_trans");
        }
    });
</script>

<? include_once("./inc/tail.php"); ?>