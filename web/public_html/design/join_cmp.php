<?
$_SUB_HEAD_TITLE = "회원가입 완료"; //헤더에 타이틀명이 없을경우 공백
$_GET['hd_num'] = '3'; //모바일 hd 1~n까지 있음
$_GET['bt_menu'] = ''; //모바일 하단메뉴 있음1, 없음 공백
include_once("./inc/head.php");
?>
<style>
    .hd_btn {
        display: none;
    }
</style>

<div class="wrap">
    <div class="sub_pg pb_lg">
        <div class="container">


            <div class="join_wr">

                <!-- svg애니메이션 -->
                <svg width="138" height="139" viewBox="0 0 138 139" fill="none" xmlns="http://www.w3.org/2000/svg" class="svg_ani">
                    <!-- ▼ 레이어 1~7 -->
                    <ellipse class="layer l1" cx="72.5" cy="124" rx="65.5" ry="15" fill="#FFFCF8" />
                    <ellipse class="layer l2" cx="72.5" cy="108" rx="65.5" ry="15" fill="#FFF6E6" />
                    <ellipse class="layer l3" cx="72.5" cy="91" rx="65.5" ry="15" fill="#FFECDB" />
                    <ellipse class="layer l4" cx="72.5" cy="75" rx="65.5" ry="15" fill="#FFC29C" />

                    <path class="pp l5" d="M23.8733 30.8733C27.1579 27.5887 27.4902 21 27.4902 21C27.4902 21 27.8226 27.5887 31.1072 30.8733C34.3917 34.1579 40.9804 34.4902 40.9804 34.4902C40.9804 34.4902 34.3917 34.8226 31.1072 38.1072C27.8226 41.3917 27.4902 47.9804 27.4902 47.9804C27.4902 47.9804 27.1579 41.3917 23.8733 38.1072C20.5887 34.8226 14 34.4902 14 34.4902C14 34.4902 20.5887 34.1579 23.8733 30.8733Z" fill="#FDCC44" />
                    <path class="pp l6" d="M113.873 9.87328C117.158 6.5887 117.49 0 117.49 0C117.49 0 117.823 6.5887 121.107 9.87328C124.392 13.1579 130.98 13.4902 130.98 13.4902C130.98 13.4902 124.392 13.8226 121.107 17.1072C117.823 20.3917 117.49 26.9804 117.49 26.9804C117.49 26.9804 117.158 20.3917 113.873 17.1072C110.589 13.8226 104 13.4902 104 13.4902C104 13.4902 110.589 13.1579 113.873 9.87328Z" fill="#FDCC44" />
                    <path class="pp l7" d="M5.11948 16.1195C6.82259 14.4164 6.99493 11 6.99493 11C6.99493 11 7.16727 14.4164 8.87038 16.1195C10.5735 17.8226 13.9899 17.9949 13.9899 17.9949C13.9899 17.9949 10.5735 18.1673 8.87038 19.8704C7.16727 21.5735 6.99493 24.9899 6.99493 24.9899C6.99493 24.9899 6.82259 21.5735 5.11948 19.8704C3.41636 18.1673 0 17.9949 0 17.9949C0 17.9949 3.41636 17.8226 5.11948 16.1195Z" fill="#FDCC44" />

                    <!-- 맨 위 상자 -->
                    <g class="topbox">
                        <rect x="43.5" y="18.5" width="59" height="59" rx="29.5" fill="#FF4516" stroke="#FF4516" />
                        <path d="M82.9322 42.0405L69.2745 53.96L63.0664 48.5421" stroke="white" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" />
                    </g>
                </svg>

                <!-- svg애니메이션 끝 -->
                 <p class="tit_st1 mt-3">회원가입이 완료되었습니다.</p>
                 <p class="mt-2 fs_14">로그인 후 다양한 서비스를 즐겨봐요!</p>
            </div>
        </div>
        <div class="bottom_btn  ">
            <div class="form-row">
                <div class="col-12"><button type="button" class="btn btn-primary btn-block btn-lg" onclick="location.href='./index.php'">확인</button></div>
            </div>
        </div>
    </div>
</div>


<? include_once("./inc/tail.php"); ?>