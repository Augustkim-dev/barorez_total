<?
$_SUB_HEAD_TITLE = "주문 완료"; //헤더에 타이틀명이 없을경우 공백
$_GET['hd_num'] = ' '; //모바일 hd 1~n까지 있음
$_GET['bt_menu'] = ''; //모바일 하단메뉴 있음1, 없음 공백
include_once("./inc/head.php");
?>

<div class="hd_m align-items-center justify-content-between">
    <div class="hd_btn"></div>
    <div class="page_tit line1_text   flex-fill text-center" style="word-break: break-word;">주문 완료</div>
    <div class=""><button class="hd_btn" type="button" onclick="history.back()"><img src="./img/ico_x.png" alt="닫기"></button></div>
</div>
<div class="wrap">
    <div class="sub_pg ">
        <div class="container ">
            <p class="mt-5 text-center"><img src="./img/gif_ani.gif" alt="움직이는 음식" style="width: 16rem;"></p>
            <p class="tit_st3 mt-4 text-center"><span class="text-primary">10번 테이블 손님, </span><br>
                주문이 완료되었습니다.</p>
            <div class="card  mt-5 mb_20">
                <div class="card-header">
                    <div class="d-flex  align-items-center ">
                        <div class="mr-2">
                            <p class=" "><span class="badg"><span class="ic_img ic_qr mr-2"></span>QR주문</span></p>
                            <a href="./index.php">
                                <p class="fs_18 fw_700 mt-2">바다마을 해물칼국수 [성수점] </p>
                            </a>
                        </div>
                        <div class="ml-auto ">
                            <div class="item_img ">
                                <a href="./index.php" class="d-block">
                                    <div class="rect rounded-pill">
                                        <img class="flex-shrink-0  " src="./img/pr_sample01.jpg">
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                    <p class="tg_400 fs_14">주문번호 : No.00000001 | 25.07.02 15:00</p>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between   ">
                        <dt class="tg_400">결제 예정 금액</dt>
                        <dd class="fw_700">41,000원</dd>
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
                        <div class="w-100">
                            <p class="fw_500">(대표메뉴)해물칼국수 </p>
                            <ul class="tg_400 mt-2  fs_14 dot_list">
                                <li>맵기선택 : 1단계</li>
                                <li>선택옵션 2 : 라면사리 (+1,000)</li>
                                <li>선택옵션 3 : 라면사리 (+1,000)</li>
                            </ul>
                            <p class="mt-3 fs_15 fw_700">20,500원 <span class="tg_400 fs_13 ml-2  fw_500">1개</span></p>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="item_box  ">
                        <div class="w-100">
                            <p class="fw_500">(대표메뉴)해물칼국수(대표메뉴)해물칼국수</p>
                            <ul class="tg_400 mt-2  fs_14 dot_list">
                                <li>맵기선택 : 1단계</li>
                                <li>선택옵션 2 : 라면사리 (+1,000)</li>
                                <li>선택옵션 3 : 라면사리 (+1,000)</li>
                            </ul>
                            <p class="mt-3 fs_15 fw_700">20,500원 <span class="tg_400 fs_13 ml-2 fw_500">1개</span></p>
                        </div>
                    </div>
                </li>
            </ul>
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



            <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                <dt>결제 예정 금액</dt>
                <dd class="fw_700">41,000원</dd>
            </div>
            <div class="form-row mt-5">
                <div class="col-6"><button type="button" class="btn btn-outline-primary btn-block" onclick="location.href='./order_guest.php'">주문 내역 </button></div>
                <div class="col-6"><button type="button" class="btn btn-primary btn-block" onclick="location.href='./index.php'">홈 </button></div>

            </div>
        </section>



    </div>
</div>


<? include_once("./inc/tail.php"); ?>