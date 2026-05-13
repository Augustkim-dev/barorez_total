<?
$_SUB_HEAD_TITLE = "장바구니"; //헤더에 타이틀명이 없을경우 공백
$_GET['hd_num'] = '2'; //모바일 hd 1~n까지 있음
$_GET['bt_menu'] = ''; //모바일 하단메뉴 있음1, 없음 공백
include_once("./inc/head.php");
?>


<div class="wrap">
    <div class="sub_pg pb_lg">
        <ul class="cart_list">
            <li>
                <div class="item_box  ">
                    <a href="./item_detail2.php">
                        <div class="item_img2 flex-shrink-0">
                            <div class="rect rounded-sm">
                                <img class=" " src="./img/pr_sample03.jpg" alt="상품사진">
                            </div>
                        </div>
                    </a>
                    <div class="w-100">
                        <div class="d-flex align-items-center justify-content-between">
                            <p class="fw_500">(대표메뉴)해물칼국수 </p>
                            <p><a href=""><img class=" " src="./img/ico_x.png" alt="삭제" style="width:18px"></a></p>
                        </div>

                        <ul class="tg_400 mt-2  fs_14 dot_list">
                            <li>맵기선택 : 1단계</li>
                            <li>선택옵션 2 : 라면사리 (+1,000)</li>
                            <li>선택옵션 3 : 라면사리 (+1,000)</li>
                        </ul>

                    </div>

                </div>
                <div class="d-flex align-items-center flex-wrap mt_20">
                    <div class="item_opt_counter mr-3">
                        <button type="button" class="btn item_opt_counter_btn  "><!-- 수량이 0일때 -->
                            <img src="./img/ico_decrease.svg" alt="감소">
                        </button>
                        <input type="txt" class="quantity" value="2">
                        <button type="button" class="btn item_opt_counter_btn  ">
                            <img src="./img/ico_increase.svg" alt="증가">
                        </button>
                    </div>
                    <button type="button" class="btn btn-outline-light btn-sm rounded-pill" data-toggle="modal" data-target="#pop_cart">옵션 변경</button>
                    <p class="mt-3 fs_15 fw_700 ml-auto">20,500원</p>
                </div>
            </li>
            <li>
                <div class="item_box sold_out ">
                    <a href="./item_detail2.php">
                        <div class="item_img2 flex-shrink-0 rounded-sm overflow-hidden">
                            <p class="sold_out_txt  ">품절</p>
                            <div class="rect ">
                                <img class=" " src="./img/pr_sample03.jpg" alt="상품사진">
                            </div>
                        </div>
                    </a>
                    <div class="w-100">
                        <div class="d-flex align-items-center justify-content-between">
                            <p class="fw_500">(대표메뉴)해물칼국수 </p>
                            <p><a href=""><img class=" " src="./img/ico_x.png" alt="삭제" style="width:18px"></a></p>
                        </div>

                        <ul class="tg_400 mt-2  fs_14 dot_list">
                            <li>맵기선택 : 1단계</li>
                            <li>선택옵션 2 : 라면사리 (+1,000)</li>
                            <li>선택옵션 3 : 라면사리 (+1,000)</li>
                        </ul>

                    </div>

                </div>
                <div class="d-flex align-items-center flex-wrap mt_20">
                    <div class="item_opt_counter mr-3">
                        <button type="button" class="btn item_opt_counter_btn  "><!-- 수량이 0일때 -->
                            <img src="./img/ico_decrease.svg" alt="감소">
                        </button>
                        <input type="txt" class="quantity" value="1">
                        <button type="button" class="btn item_opt_counter_btn  ">
                            <img src="./img/ico_increase.svg" alt="증가">
                        </button>
                    </div>
                    <button type="button" class="btn btn-outline-light btn-sm rounded-pill" data-toggle="modal" data-target="#pop_cart">옵션 변경</button>

                    <p class="mt-3 fs_15 fw_700 ml-auto">20,500원</p>
                </div>
            </li>
        </ul>
        <div class="container my-3">
            <button type="button" class="btn btn-outline-primary btn-block btn_st1" onclick="location.href='./item_detail2.php'">메뉴 추가 <img src="./img/btn_deco.svg" alt=" " class="ml-3"></button>
        </div>
        <div class="bar">
        </div>
        <div class="container mt-5">
            <dl class="">
                <dt class="tit_st3 mb-4">결제정보</dt>
                <dd class="d-flex align-items-center justify-content-between  ">
                    <p>결제 예정 금액</p>
                    <p class="fw_700 flex-shrink-0  ">41,000원</p>
                </dd>
            </dl>
        </div>
        <div class="bottom_btn  ">
            <div class="form-row">
                <div class="col-12"><button type="button" class="btn btn-primary btn-block btn-lg" onclick="location.href='./order3.php'">총 2개 41,000원 <span class="fw_100 mx-3">|</span> 포장 주문하기</button></div>
            </div>
        </div>
    </div>
</div>

<!-- 장바구니 옵션 변경 팝업-->
<div class="modal modal_bottom fade" id="pop_cart" tabindex="-1" aria-hidden="true"  data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header ">
                <h5 class="modal-title">(대표메뉴)해물칼국수</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><img class=" " src="./img/ico_x.png" alt="삭제" style="width:18px"></button>
            </div>
            <div class="modal-body pt-0">
                <div class="d-flex align-items-end justify-content-between border-bottom py_20">
                    <p class="fw_600">가격</p>
                    <p class="tit_st3">20,500원</p>
                </div>
                <div class="d-flex align-items-end justify-content-between  border-bottom py_20">
                    <p class="fw_600">수량</p>
                    <div class="item_opt_counter">
                        <button type="button" class="btn item_opt_counter_btn  " disabled=""><!-- 수량이 0일때 -->
                            <img src="./img/ico_decrease.svg" alt="감소">
                        </button>
                        <input type="txt" class="quantity" value="105">
                        <button type="button" class="btn item_opt_counter_btn ">
                            <img src="./img/ico_increase.svg" alt="증가">
                        </button>
                    </div>
                </div>
                <!-- 옵션1-->
                <div class="collapse_ex border-bottom py_20">
                    <ul>
                        <li id="item_borrow_wp1">
                            <button type="button" class="btn d-flex p-0 justify-content-between w-100 h-auto collapsed" data-toggle="collapse" data-target="#item_borrow1" aria-expanded="false">
                                <div class="tit_st3 ">
                                    맵기 선택 <span class="text-primary fs_15 ml-2">필수</span>
                                </div>
                                <p><img src="./img/ico_arrow.png" style="width:2.4rem;"></p>
                            </button>
                        </li>
                    </ul>
                    <div id="item_borrow1" class="collapse" data-parent="#item_borrow_wp1" style="">
                        <div class="opt_checks_wp mt-4">
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
                        </div>


                    </div>
                </div>
                <!-- 옵션2-->
                <div class="collapse_ex border-bottom py_20">
                    <ul>
                        <li id="item_borrow_wp2">
                            <button type="button" class="btn d-flex p-0 justify-content-between w-100 h-auto collapsed" data-toggle="collapse" data-target="#item_borrow2" aria-expanded="false">
                                <div class="tit_st3 ">
                                    토핑추가 <span class="tg_400 fs_15 ml-2">선택</span>
                                </div>
                                <p><img src="./img/ico_arrow.png" style="width:2.4rem;"></p>
                            </button>
                        </li>
                    </ul>
                    <div id="item_borrow2" class="collapse" data-parent="#item_borrow_wp2" style="">
                      <div class="opt_checks_wp mt-4">
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
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer pt-3">
                <div class="form-row">
                    <div class="col-12"><button type="button" class="btn btn-primary btn-block" data-dismiss="modal"> 41,000원 <span class="fw_100 mx-3">|</span> 변경하기</button></div>

                </div>
            </div>
        </div>
    </div>
</div>
<? include_once("./inc/tail.php"); ?>