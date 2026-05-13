<?
$_SUB_HEAD_TITLE = "주문 상세"; //헤더에 타이틀명이 없을경우 공백
$_GET['hd_num'] = '2'; //모바일 hd 1~n까지 있음
$_GET['bt_menu'] = ''; //모바일 하단메뉴 있음1, 없음 공백
include_once("./inc/head.php");
?>
<div class="wrap">
    <div class="sub_pg ">
        <div class="bg-light px_16 py_20">
            <div class="card   ">
                <div class="card-header">
                    <div class="d-flex  align-items-center ">
                        <div class="mr-2">
                            <p class="d-flex align-items-center"><span class="badg blue"><span class="ic_img ic_calendar mr-2"></span>예약</span> <span class="t_blue ml-3">예약요청</span></p>
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
                    <p class="tg_400 fs_14">예약번호 : No.00000001 | 25.07.02 15:00</p>
                </div>
                <div class="card-body ">
                    <div class="d-flex align-items-center rsrv_list  ">
                        <dt class="tg_400">예약일시</dt>
                        <dd class=" ">2025.07.16 13:00~16:00 </dd>
                    </div>
                    <div class="d-flex align-items-center rsrv_list">
                        <dt class="tg_400">예약자</dt>
                        <dd class=" ">김이름(010-1234-1234)</dd>
                    </div>
                    <div class="d-flex align-items-center rsrv_list">
                        <dt class="tg_400">예약인원</dt>
                        <dd class=" ">3명</dd>
                    </div>
                </div>
                <div class="card-footer ">
                    <button type="button" class="btn btn-outline-light btn-md btn-block" data-toggle="modal" data-target="#pop_rsrv">예약 취소</button>
                </div>
            </div>
        </div>
        <div class="bar">
        </div>
        <section class="container ">
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
        <section class="container pb_20">
            <div class="  pt_20 mb-3">
                <h3 class="tit_st3">매장정보 </h3>
            </div>
            <div class="mt-4">
                <div class="d-flex shop_story  ">
                    <div class="tg_400 tit">
                        연락처
                    </div>
                    <div class="flex-fill">
                        010-1234-5678
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
    </div>
</div>
<!-- 예약취소 팝업-->
<div class="modal fade" id="pop_rsrv" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-body mt-5">
                <div class="no_data  ">
                    <img src="./img/img_mark.png">
                    <p class="   line_h1_4 mt-3 fs_18 fw_600">예약취소 하시겠습니까?</p>
                </div>
            </div>
            <div class="modal-footer">
                <div class="form-row justify-content-end">
                    <div class="col-4"><button type="button" class="btn btn-outline-light btn-block  " data-dismiss="modal">아니요</button></div>
                    <div class="col-8"><button type="button" class="btn btn-primary btn-block" data-dismiss="modal">예약 취소</button></div>
                </div>
            </div>
        </div>
    </div>
</div>
<? include_once("./inc/tail.php"); ?>