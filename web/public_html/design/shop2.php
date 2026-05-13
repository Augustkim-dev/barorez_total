<?
$_SUB_HEAD_TITLE = " "; //헤더에 타이틀명이 없을경우 공백
$_GET['hd_num'] = '5'; //모바일 hd 1~n까지 있음
include_once("./inc/head.php");
?>


<div class="wrap">
    <div class=" idx_pg  ">
        <div class="container shop_hd">

            <div class="d-flex  align-items-center ">
                <div class="mr-2">
                    <p class="fs_20 fw_700">바다마을 해물칼국수 [성수점] </p>
                    <p class="tg_500 fs_15 fw_500 mt-2">서울특별시 성동구 성수동 </p>
                </div>
                <div class="ml-auto ">
                    <div class="item_img">
                        <div class="rect rounded-pill">
                            <img class="flex-shrink-0  " src="./img/pr_sample01.jpg">
                        </div>
                    </div>
                </div>
            </div>
            <div class="ck_btn_group">
                <button type="button" class="btn  btn-md  btn-block " onclick="location.href='./shop.php' ">예약</button>
                <button type="button" class="btn  btn-outline-primary btn-md btn-block mt-0   ">포장</button>
            </div>
            <div class="mt-3">
                <!-- 지도 예시-->
                <p class="rounded mb-4">
                    <img src="./img/map_etc.jpg" alt="이미지  " class="w-100">
                </p>
                <div class="d-flex shop_story  ">
                    <div class="tg_400 tit">
                        조리시간
                    </div>
                    <div class="flex-fill">
                        10~20분 예상
                    </div>
                    <div class="  ml-auto">
                        <a href="./shop_info.php" class="rounded-pill bg-light py-2 px-4 fs_13 tg_500">가게정보</a>
                    </div>
                </div>
                <div class="d-flex shop_story">
                    <div class="tg_400 tit">
                        위치안내
                    </div>
                    <div class="flex-fill">
                        <p>서울특별시 성동구 성수동 1254-5번지 <br> <a href="" class="un_reboot_a tg_400 mt-2">주소 복사</a></p>
                    </div>
                </div>
            </div>

        </div>

        <div class="bar">
        </div>



        <section class="collapse_cate  mb-3 mt-4">
            <div class="">
                <div id="cate_cont" class="  scroll_bar_none  scroll_mouse ">
                    <div class="btn-group btn-group-toggle px_16" data-toggle="buttons">
                        <label class="btn btn-outline-light btn-md rounded-pill active">
                            <input type="radio" name="options" id="option1" checked=""> 전체
                        </label>
                        <label class="btn btn-outline-light btn-md rounded-pill">
                            <input type="radio" name="options" id="option2"> 카테고리
                        </label>
                        <label class="btn btn-outline-light btn-md rounded-pill">
                            <input type="radio" name="options" id="option3"> 카테고리
                        </label>
                        <label class="btn btn-outline-light btn-md rounded-pill">
                            <input type="radio" name="options" id="option4"> 카테고리
                        </label>
                        <label class="btn btn-outline-light btn-md rounded-pill">
                            <input type="radio" name="options" id="option5"> 카테고리
                        </label>
                        <label class="btn btn-outline-light btn-md rounded-pill">
                            <input type="radio" name="options" id="option6"> 카테고리
                        </label>
                        <label class="btn btn-outline-light btn-md rounded-pill">
                            <input type="radio" name="options" id="option7"> 카테고리
                        </label>
                        <label class="btn btn-outline-light btn-md rounded-pill">
                            <input type="radio" name="options" id="option8"> 카테고리
                        </label>
                    </div>
                </div>
            </div>
        </section>

        <ul class="item_list">
            <li>
                <div class="item_box  ">
                    <div class="item_img flex-shrink-0">
                        <div class="rect rounded">
                            <img class=" " src="./img/pr_sample03.jpg" alt="상품사진">
                        </div>

                    </div>
                    <div class="w-100">
                        <p class="fw_500">(대표메뉴)해물칼국수 </p>
                        <p class="tg_400 mt-2  fs_15 line2_text">간단한 설명 두줄까지</p>
                        <p class="mt-3 fs_15 fw_700">20,500원</p>
                    </div>
                    <a class="item_link" href="./item_detail3.php"></a>
                </div>
            </li>
            <li>
                <div class="item_box  ">
                    <div class="item_img flex-shrink-0">
                        <div class="rect rounded">
                            <img class=" " src="./img/pr_sample04.jpg" alt="상품사진">
                        </div>

                    </div>
                    <div class="w-100">
                        <p class="fw_500">(대표메뉴)해물칼국수(대표메뉴)해물칼국수</p>
                        <p class="tg_400 mt-2  fs_15 line2_text">간단한 설명 두줄까지</p>
                        <p class="mt-3 fs_15 fw_700">20,500원</p>
                    </div>
                    <a class="item_link" href="./item_detail3.php"></a>
                </div>
            </li>
            <li>
                <div class="item_box sold_out">
                    <div class="item_img flex-shrink-0 rounded overflow-hidden">
                        <p class="sold_out_txt ">품절</p>
                        <div class="rect ">
                            <img class=" " src="./img/pr_sample02.jpg" alt="상품사진">
                        </div>
                    </div>
                    <div class="w-100">
                        <p class="fw_500">(대표메뉴)해물칼국수(대표메뉴)해물칼국수</p>
                        <p class="tg_400 mt-2 fs_15 line2_text">남해 앞바다에서 당일 조업한 국내산 바지락과 디포리, 멸치 다시마 등 6가지 육수재료</p>
                        <p class="mt-3 fs_15 fw_700">20,500원</p>
                    </div>
                    <a class="item_link" href="./item_detail3.php"></a>
                </div>
            </li>

        </ul>

        <!-- 메뉴가 담겨있을때만 이 버튼이 노출됨-->
        <div class="bottom_btn  ">
            <div class="form-row">
                <div class="col-12"><button type="button" class="btn btn-primary btn-block  btn-lg" onclick="location.href='./cart3.php'">장바구니 <span class="badge bg-white  text-primary rounded-pill ml-2">3</span></button></div>
            </div>
        </div>

       

    </div>
</div>





<? include_once("./inc/tail.php"); ?>