<?
$_SUB_HEAD_TITLE = "쿠폰"; //헤더에 타이틀명이 없을경우 공백
$_GET['hd_num'] = '2'; //모바일 hd 1~n까지 있음
$_GET['bt_menu'] = ''; //모바일 하단메뉴 있음1, 없음 공백
include_once("./inc/head.php");
?>


<div class="wrap">
    <div class="sub_pg bg-light ">
        <nav class="tab_fixed">
            <ul class="nav nav_tab_line" id="nav-tab" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" id="tab03_tab1" data-toggle="tab" data-target="#tab03_1" type="button" role="tab" aria-selected="true">사용 가능</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="tab03_tab2" data-toggle="tab" data-target="#tab03_2" type="button" role="tab" aria-selected="false">사용 완료/불가
                    </button>
                </li>

            </ul>
        </nav>
        <div class="container mt_20">
            <div class="tab-content" id="nav_Content02">
                <div class="tab-pane fade show active" id="tab03_1">
                    <p class="mb-2">총 <span class="text-primary">2</span>장</p>
                    <div class="border rounded coupon_box mb-3 ">
                        <p class="fs_22 text-primary fw_700 ">10,000원 할인</p>
                        <p class="mt-2 fw_600 fs_15">회원가입 쿠폰</p>
                        <div class="d-flex justify-content-between align-items-end mt-4">
                            <p class="tg_400 fs_14 line_h1_3 ">최소주문금액 100,000원<br>25.03.30까지</p>
                            <p class="  coupon_use">사용가능</p>
                        </div>
                    </div>
                    <div class="border rounded coupon_box mb-3 ">
                        <p class="fs_22 text-primary fw_700 ">10,000원 할인</p>
                        <p class="mt-2 fw_600 fs_15">회원가입 쿠폰</p>
                        <div class="d-flex justify-content-between align-items-end mt-4">
                            <p class="tg_400 fs_14 line_h1_3 ">최소주문금액 100,000원<br>25.03.30까지</p>
                            <p class="  coupon_use">사용가능</p>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="tab03_2">
                    <p class="mb-2">총 <span class="text-primary">2</span>장</p>
                    <div class="border rounded coupon_box mb-3 notuse ">
                        <p class="fs_22 text-primary fw_700 ">10,000원 할인</p>
                        <p class="mt-2 fw_600 fs_15">회원가입 쿠폰</p>
                        <div class="d-flex justify-content-between align-items-end mt-4">
                            <p class="tg_400 fs_14 line_h1_3 ">최소주문금액 100,000원<br>25.03.30까지</p>
                            <p class="coupon_use">기간만료</p>
                        </div>
                    </div>
                    <div class="border rounded coupon_box mb-3 notuse ">
                        <p class="fs_22 text-primary fw_700 ">10,000원 할인</p>
                        <p class="mt-2 fw_600 fs_15">회원가입 쿠폰</p>
                        <div class="d-flex justify-content-between align-items-end mt-4">
                            <p class="tg_400 fs_14 line_h1_3 ">최소주문금액 100,000원<br>25.03.30까지</p>
                            <p class="coupon_use">사용완료</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


<? include_once("./inc/tail.php"); ?>