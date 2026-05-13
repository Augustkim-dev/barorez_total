<?
$_SUB_HEAD_TITLE = "매장정보"; //헤더에 타이틀명이 없을경우 공백
$_GET['hd_num'] = '2'; //모바일 hd 1~n까지 있음
$_GET['bt_menu'] = ''; //모바일 하단메뉴 있음1, 없음 공백
include_once("./inc/head.php");
?>


<div class="wrap">
    <div class="sub_pg ">
        <section class="shop_banner">
            <div class="swiper review_swiper  ">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <div class="rect">
                            <img src="./img/pr_sample01.jpg" alt="이미지  ">
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="rect">
                            <img src="./img/pr_sample02.jpg" alt="이미지  ">
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="rect">
                            <img src="./img/pr_sample03.jpg" alt="이미지  ">
                        </div>
                    </div>

                </div>
                <!-- <div class="swiper-button-next"><img src="./img/swiper_r.png"></div>
                <div class="swiper-button-prev"><img src="./img/swiper_l.png"></div> -->
                <div class="swiper-pagination pag_st2"></div>
            </div>
            <script class="">
                var swiper = new Swiper(".review_swiper", {
                    pagination: {
                        el: ".swiper-pagination",
                        type: "fraction",
                    },
                    navigation: {
                        nextEl: ".review_swiper .swiper-button-next",
                        prevEl: ".review_swiper .swiper-button-prev",
                    },
                });
            </script>
        </section>
        <div class="container py-5">
            <p class="fs_20 fw_600 mb-4">바다마을 해물칼국수 [성수점] </p>
            <!-- 지도 예시-->
            <p class="rounded">
                <img src="./img/map_etc.jpg" alt="이미지  " class="w-100">
            </p>
            <div class="mt-4">
                <div class="d-flex shop_story  ">
                    <div class="tg_400 tit">
                        상호명
                    </div>
                    <div class="flex-fill">
                        바다마을 해물칼국수 [성수점]
                    </div>

                </div>
                <div class="d-flex shop_story">
                    <div class="tg_400 tit">
                        주소
                    </div>
                    <div class="flex-fill">
                        <p>서울특별시 성동구 성수동 1254-5번지 <br> <a href="" class="un_reboot_a tg_400 mt-2">주소 복사</a></p>
                    </div>
                </div>
            </div>

        </div>
        <div class="bar">
        </div>
        <div class="container py-5">
            <div class=" ">
                <div class="d-flex shop_story  ">
                    <div class="tg_400 tit">
                        운영시간
                    </div>
                    <div class="flex-fill">
                        09:00~18:00
                    </div>
                </div>
                <div class="d-flex shop_story">
                    <div class="tg_400 tit">
                        휴무일
                    </div>
                    <div class="flex-fill">
                        <p>매주 일요일, 법정 공휴일 매주 일요일, 법정 공휴일</p>
                    </div>
                </div>
                <div class="d-flex shop_story">
                    <div class="tg_400 tit">
                        전화번호
                    </div>
                    <div class="flex-fill">
                        <p>070-1234-1234</p>
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>


<? include_once("./inc/tail.php"); ?>