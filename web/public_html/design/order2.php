<?
$_SUB_HEAD_TITLE = "주문/결제"; //헤더에 타이틀명이 없을경우 공백
$_GET['hd_num'] = '2'; //모바일 hd 1~n까지 있음
$_GET['bt_menu'] = ''; //모바일 하단메뉴 있음1, 없음 공백
include_once("./inc/head.php");
?>


<div class="wrap">
    <div class="sub_pg pb_lg">
        <div class="container shop_hd">
            <div class="d-flex  align-items-center ">
                <div class="mr-2">
                    <p><span class="badg blue"><span class="ic_img ic_calendar mr-2"></span>예약</span></p>
                    <a href="./shop.php">
                        <p class="fs_18 fw_700 mt-2">바다마을 해물칼국수 [성수점] </p>
                    </a>
                </div>
                <div class="ml-auto ">
                    <div class="item_img ">
                        <a href="./shop.php" class="d-block">
                            <div class="rect rounded-pill">
                                <img class="flex-shrink-0  " src="./img/pr_sample01.jpg">
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="bar">
        </div>
        <section class="container">
            <div class="  pt_20">
                <h3 class="tit_st3">주문 메뉴 <span class="text-primary">3</span></h3>
            </div>
            <ul class="item_list2">
                <li>
                    <div class="item_box  ">
                        <div class="item_img2  flex-shrink-0">
                            <div class="rect rounded">
                                <img class=" " src="./img/pr_sample03.jpg" alt="상품사진">
                            </div>

                        </div>
                        <div class="w-100">
                            <p class="fw_500">(대표메뉴)해물칼국수 </p>
                            <ul class="tg_400 mt-2  fs_14 dot_list">
                                <li>맵기선택 : 1단계</li>
                                <li>선택옵션 2 : 라면사리 (+1,000)</li>
                                <li>선택옵션 3 : 라면사리 (+1,000)</li>
                            </ul>
                            <p class="mt-3 fs_15 fw_700">20,500원 <span class="tg_400 fs_13 ml-2  fw_500">1개</span></p>
                        </div>
                        <a class="item_link" href="./item_detail.php"></a>
                    </div>
                </li>
                <li>
                    <div class="item_box  ">
                        <div class="item_img2  flex-shrink-0">
                            <div class="rect rounded">
                                <img class=" " src="./img/pr_sample04.jpg" alt="상품사진">
                            </div>

                        </div>
                        <div class="w-100">
                            <p class="fw_500">(대표메뉴)해물칼국수(대표메뉴)해물칼국수</p>
                            <ul class="tg_400 mt-2  fs_14 dot_list">
                                <li>맵기선택 : 1단계</li>
                                <li>선택옵션 2 : 라면사리 (+1,000)</li>
                                <li>선택옵션 3 : 라면사리 (+1,000)</li>
                            </ul>
                            <p class="mt-3 fs_15 fw_700">20,500원 <span class="tg_400 fs_13 ml-2 fw_500">1개</span></p>
                        </div>
                        <a class="item_link" href="./item_detail.php"></a>
                    </div>
                </li>


            </ul>
        </section>
        <div class="bar">
        </div>
        <section class="container pb-5">
            <div class="  pt_20 mb_20">
                <h3 class="tit_st3">할인 쿠폰 </h3>
            </div>
            <button type="button" class="coupon_btn border" data-toggle="modal" data-target="#pop_coupon">
                <div>
                    쿠폰선택
                </div>
                <div class="text-primary fw_500">
                    <span class="mr-2">적용가능 1장</span><img class="flex-shrink-0  " src="./img/ico_arrow2.png" style="width:2rem">
                </div>
            </button>
            <!-- 쿠폰적용됬을시
            <button type="button" class="coupon_btn border" onclick="location.href='./order.php'">
                <div>
                    쿠폰선택<span class="badg sm ml-2"> 적용중</span>
                </div>
                <div class="text-primary fw_500">
                    <span class="mr-2">- 3,000 원</span><img class="flex-shrink-0  " src="./img/ico_arrow2.png" style="width:2rem">
                </div>
            </button> -->
        </section>
        <div class="bar">
        </div>
        <section class="container">
            <div class="  pt_20 mb_20">
                <h3 class="tit_st3">결제정보 </h3>
            </div>

            <div class="d-flex align-items-center justify-content-between mb-3 ">
                <dt>총 상품 금액</dt>
                <dd class="fw_700">41,000원</dd>
            </div>

            <div class="d-flex align-items-center justify-content-between mb-3 ">
                <dt>쿠폰 할인</dt>
                <dd class="fw_700">-3,000원</dd>
            </div>

            <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                <dt>총 결제 금액</dt>
                <dd class="fw_700">41,000원</dd>
            </div>

        </section>
        <div class="bottom_btn  ">
            <div class="form-row">
                <div class="col-12"><button type="button" class="btn btn-primary btn-block btn-lg" onclick="location.href='./rsrv_cmp2.php'">24,000원 결제하기 </button></div>
            </div>
        </div>
    </div>
</div>

<div class="modal modal_full" id="pop_coupon" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div class="hd_btn justify-content-start"></div>
                <div class="page_tit   flex-fill text-center">쿠폰 선택</div>
                <div class="hd_btn justify-content-end"><button type="button" data-dismiss="modal"><img src="./img/ic_close.png" alt="닫기"></button></div>
            </div>
            <div class="modal-body">
                <div class="coupon_list">
                    <ul class=" ">
                        <li class=" ">
                            <label class="coupon_item  ">
                                <div class="media w-100 align-items-center">
                                    <div class="flex-fill">
                                        <p class="tit_st4 text-primary ">10,000원 할인</p>
                                        <p class="fw_600 mb-3 mt-3 ">쿠폰명 들어갑니다.</p>
                                        <p class="fs_13 tg_500">최소주문금액 100,000원</p>
                                        <p class="fs_13 tg_500 mt-1">25.03.30까지</p>
                                    </div>
                                    <div class="btn_wr mr-2">
                                        <div class="checks">
                                            <input type="radio" name="rd1">
                                            <span class="ic_box"></span>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </li>
                        <li class=" ">
                            <label class="coupon_item  ">
                                <div class="media w-100 align-items-center">
                                    <div class="flex-fill">
                                        <p class="tit_st4 text-primary ">10,000원 할인</p>
                                        <p class="fw_600 mb-3 mt-3 ">쿠폰명 들어갑니다.</p>
                                        <p class="fs_13 tg_500">최소주문금액 100,000원</p>
                                        <p class="fs_13 tg_500 mt-1">25.03.30까지</p>
                                    </div>
                                    <div class="btn_wr mr-2">
                                        <div class="checks">
                                            <input type="radio" name="rd1">
                                            <span class="ic_box"></span>
                                        </div>
                                    </div>
                                </div>

                            </label>

                        </li>



                    </ul>

                </div>
                <button type="button" class="btn btn-primary btn-block btn-lg" data-dismiss="modal">적용</button>
                <button type="button" class="btn btn-outline-light   btn-block border-0 un_reboot_a  btn-lg" data-dismiss="modal">취소</button>

            </div>

        </div>
    </div>
</div>
<? include_once("./inc/tail.php"); ?>