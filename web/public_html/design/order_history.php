<?
$_SUB_HEAD_TITLE = "주문 내역"; //헤더에 타이틀명이 없을경우 공백
$_GET['hd_num'] = '2'; //모바일 hd 1~n까지 있음
$_GET['bt_menu'] = ''; //모바일 하단메뉴 있음1, 없음 공백
include_once("./inc/head.php");
?>


<div class="wrap">
    <div class="sub_pg bg-light">
        <ul class="nav nav_tab_line">
            <li class="nav-item">
                <button class="nav-link active">주문 내역</button>
            </li>
            <li class="nav-item">
                <button class="nav-link"  onclick="location.href='./order_history_cal.php ' ">취소 내역</button>
            </li>
        </ul>
        <div class="container">
            <div class="order_filter py-3">
                <div class="sch_ip align-items-center">
                    <input type="search" class="form-control fs_14 flex-fill border-0" placeholder="검색어를 입력해주세요">
                    <button class="btn btn-icon flex-shrink-0"><img src="./img/ic_sch_gray.png" style="width:2.0rem;"></button>
                </div>
                <div class="scroll_mouse scroll_bar_none mt-3 order_filter_tg mx_n16 ">
                    <div class="d-flex px_16  ">
                        <button type="button" class="btn btn-outline-light btn-md rounded-pill reset_btn"><img src="./img/sch_re1.svg" alt="초기화"></button>
                        <button type="button" class="btn btn-outline-light btn-md rounded-pill" data-toggle="modal" data-target="#pop_filter1">주문상태 <span class="line_arrow down ml-2"></span></button>
                        <button type="button" class="btn btn-outline-light btn-md rounded-pill" data-toggle="modal" data-target="#pop_filter2">조회 기간 <span class="line_arrow down ml-2"></span></button>
                        <!-- 주문상태 선택됬을때 예시
                        <button type="button" class="btn btn-outline-light btn-md rounded-pill active">QR 주문<span class="line_arrow down ml-2"></span></button> -->
                        <!-- 조회기간 선택됬을때 예시
                        <button type="button" class="btn btn-outline-light btn-md rounded-pill active">2025년 08월 11일~2025년 11월 10일 <span class="line_arrow down ml-2"></span></button> -->
                        &nbsp;&nbsp;
                    </div>
                </div>
            </div>
            <!-- 주문내역 리스트 / QR주문-->
            <div class="card">
                <div class="card-header">
                    <div class="d-flex  align-items-center ">
                        <div class="mr-2">
                            <p class="d-flex align-items-center"><span class="badg"><span class="ic_img ic_qr mr-2"></span>QR주문</span></p>
                            <a href="./ord_his_qr.php" class="d-flex align-items-center mt-2">
                                <p class="fs_18 fw_700  line1_text">바다마을 해물칼국수 [성수점]</p>
                                <img src="./img/ico_arrow1.png" alt="내정보 수정" class="ml-3 flex-shrink-0" style="width: 2rem;">
                            </a>
                        </div>
                        <div class="ml-auto ">
                            <div class="item_img ">
                                <a href="./ord_his_qr.php" class="d-block">
                                    <div class="rect rounded-pill">
                                        <img class="flex-shrink-0  " src="./img/pr_sample01.jpg">
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                    <p class="tg_400 fs_14">주문번호 : No.00000001 | 25.07.02</p>
                </div>
                <div class="card-body ">
                    <div>
                        [대표메뉴]해물칼국수 외 3건
                    </div>
                    <div class="d-flex align-items-center rsrv_list border-top mt-4 pt-4">
                        <dt class="tg_400">결제금액</dt>
                        <dd class="  fw_700 ml-auto">50,000원</dd>
                    </div>
                </div>
                 
            </div>
            <!-- 주문내역 리스트 / 예약요청 / 결제X-->
            <div class="card">
                <div class="card-header">
                    <div class="d-flex  align-items-center ">
                        <div class="mr-2">
                            <p class="d-flex align-items-center"><span class="badg blue"><span class="ic_img ic_calendar mr-2"></span>예약</span> <span class="t_blue ml-3">예약요청</span></p>
                            <a href="./ord_his1.php" class="d-flex align-items-center mt-2">
                                <p class="fs_18 fw_700  line1_text">바다마을 해물칼국수 [성수점]</p>
                                <img src="./img/ico_arrow1.png" alt="내정보 수정" class="ml-3 flex-shrink-0" style="width: 2rem;">
                            </a>
                        </div>
                        <div class="ml-auto ">
                            <div class="item_img ">
                                <a href="./ord_his1.php" class="d-block">
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
                    <div class="d-flex align-items-center rsrv_list border-top mt-4 pt-4">
                        <dt class="tg_400">결제금액</dt>
                        <dd class="  fw_700 ml-auto">50,000원</dd>
                    </div>
                </div>
                <div class="card-footer ">
                    <button type="button" class="btn btn-outline-light btn-md btn-block" data-toggle="modal" data-target="#pop_rsrv">예약 취소</button>
                </div>
            </div>

            <!-- 주문내역 리스트 / 예약완료 / 선결제-->
            <div class="card">
                <div class="card-header">
                    <div class="d-flex  align-items-center ">
                        <div class="mr-2">
                            <p class="d-flex align-items-center"><span class="badg blue"><span class="ic_img ic_calendar mr-2"></span>예약</span> <span class="t_blue ml-3">예약완료</span></p>
                            <a href="./ord_his1.php" class="d-flex align-items-center mt-2">
                                <p class="fs_18 fw_700  line1_text">바다마을 해물칼국수 [성수점]</p>
                                <img src="./img/ico_arrow1.png" alt="내정보 수정" class="ml-3 flex-shrink-0" style="width: 2rem;">
                            </a>
                        </div>
                        <div class="ml-auto ">
                            <div class="item_img ">
                                <a href="./ord_his1.php" class="d-block">
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
                    <div class="d-flex align-items-center rsrv_list">
                        <dt class="tg_400">주문메뉴</dt>
                        <dd class="line1_text">(대표메뉴)해물칼국수, 물국수 (대표메뉴)해물칼국수, 물국수</dd>
                    </div>
                    <div class="d-flex align-items-center rsrv_list border-top mt-4 pt-4">
                        <dt class="tg_400">결제금액</dt>
                        <dd class="  fw_700 ml-auto">50,000원</dd>
                    </div>
                </div>
                
            </div>

            <!-- 주문내역 리스트 / 포장요청:포장완료:포장준비중??-->
            <div class="card">
                <div class="card-header">
                    <div class="d-flex  align-items-center ">
                        <div class="mr-2">
                            <p class="d-flex align-items-center"><span class="badg green"><span class="ic_img ic_pack mr-2"></span>포장</span> <span class="t_green ml-3">포장 요청</span></p>
                            <a href="./ord_pack.php" class="d-flex align-items-center mt-2">
                                <p class="fs_18 fw_700  line1_text">바다마을 해물칼국수 [성수점]</p>
                                <img src="./img/ico_arrow1.png" alt="내정보 수정" class="ml-3 flex-shrink-0" style="width: 2rem;">
                            </a>
                        </div>
                        <div class="ml-auto ">
                            <div class="item_img ">
                                <a href="./ord_pack.php" class="d-block">
                                    <div class="rect rounded-pill">
                                        <img class="flex-shrink-0  " src="./img/pr_sample01.jpg">
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                    <p class="tg_400 fs_14">주문번호 : No.00000001 | 25.07.02</p>
                </div>
                <div class="card-body ">
                    <div class="d-flex align-items-center rsrv_list  ">
                        <dt class="tg_400">주문일시</dt>
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
                    <div class="d-flex align-items-center rsrv_list">
                        <dt class="tg_400">주문메뉴</dt>
                        <dd class="line1_text">(대표메뉴)해물칼국수, 물국수 (대표메뉴)해물칼국수, 물국수</dd>
                    </div>
                    <div class="d-flex align-items-center rsrv_list border-top mt-4 pt-4">
                        <dt class="tg_400">결제금액</dt>
                        <dd class="  fw_700 ml-auto">50,000원</dd>
                    </div>
                </div>
                <div class="card-footer ">
                    <button type="button" class="btn btn-outline-light btn-md btn-block" data-toggle="modal" data-target="#pop_rsrv">포장 취소</button>
                </div>
            </div>
        </div>

    </div>
</div>


<!--주문상태 pop_filter1 -->
<div class="modal modal_bottom fade" id="pop_filter1" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">주문상태</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><img src="./img/ic_close.png"></button>
            </div>
            <div class="modal-body  pt-2">
                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                    <label class="btn btn-outline-light dark rounded-pill btn-md px-5">
                        <input type="radio" name="options" id="option1" checked=""> QR주문
                    </label>
                    <label class="btn btn-outline-light dark rounded-pill btn-md  px-5">
                        <input type="radio" name="options" id="option2"> 예약
                    </label>
                    <label class="btn btn-outline-light dark rounded-pill btn-md  px-5">
                        <input type="radio" name="options" id="option3"> 포장
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <div class="form-row">
                    <div class="col-3"><button type="button" class="btn btn-outline-light btn-block" data-dismiss="modal">초기화</button></div>
                    <div class="col-9"><button type="button" class="btn btn-primary btn-block" data-dismiss="modal">검색</button></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--조회 기간 pop_filter2 -->
<div class="modal modal_bottom fade" id="pop_filter2" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">조회 기간</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><img src="./img/ic_close.png"></button>
            </div>
            <div class="modal-body pt-2">
                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                    <label class="btn btn-outline-light dark rounded-pill btn-md  px-4">
                        <input type="radio" name="options" id="option1" checked=""> 최근 1개월
                    </label>
                    <label class="btn btn-outline-light dark  rounded-pill  btn-md">
                        <input type="radio" name="options" id="option2"> 최근 3개월
                    </label>
                    <label class="btn btn-outline-light dark  rounded-pill  btn-md">
                        <input type="radio" name="options" id="option3"> 최근 6개월
                    </label>
                </div>

                <div class="form-row mt-5">
                    <div class="form_wr   col-6  ">
                        <div class="ip_tit">
                            <h5>시작일</h5>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class=" flex-fill">
                                <input type="date" class="form-control  fs_14" placeholder="날짜 선택">
                            </div>
                            <p class="ml-2">~</p>
                        </div>
                    </div>
                    <div class="form_wr   col-6  ">
                        <div class="ip_tit">
                            <h5>종료일</h5>
                        </div>
                        <input type="date" class="form-control fs_14" placeholder="날짜 선택">
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <div class="form-row">
                    <div class="col-3"><button type="button" class="btn btn-outline-light btn-block" data-dismiss="modal">초기화</button></div>
                    <div class="col-9"><button type="button" class="btn btn-primary btn-block" data-dismiss="modal">검색</button></div>
                </div>
            </div>
        </div>
    </div>
</div>
<? include_once("./inc/tail.php"); ?>

<!-- 예약취소 팝업-->
<div class="modal fade" id="pop_rsrv" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
           
            <div class="modal-body mt-5">
                <div class="no_data  ">
                    <img src="./img/img_mark.png">
                    <p class="   line_h1_4 mt-3 fs_18 fw_600">예약 하시겠습니까?</p>
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