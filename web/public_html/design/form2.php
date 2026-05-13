<?
$_SUB_HEAD_TITLE = "퍼블 공지"; //헤더에 타이틀명이 없을경우 공백
$_GET['hd_num'] = '2'; //모바일 hd 1~n까지 있음
$_GET['bt_menu'] = ''; //모바일 하단메뉴 있음1, 없음 공백
include_once("./inc/head.php");
?>


<div class="wrap">
    <div class="sub_pg pb_lg">
        <div class="container pt-5">

            <div class="mb-5">
                <p>스타일참고</p>
                <p>form.php</p>
            </div>

            <div class="mb-5">
                <p class="mb-3">form을 다 작성하지 않을경우 disable버튼으로 보이게</p>
                <button type="button" class="btn btn-primary btn-block" disabled>disabled </button>
                <button type="button" class="btn btn-primary btn-block">완료</button>
            </div>

            <div class="mb-5">
                <p class="mb-3">인풋</p>
                <div class="form_wr   ip_valid">
                    <div class="ip_tit">
                        <h5>ip_valid</h5>
                    </div>
                    <input type="text" class="form-control" placeholder="입력해주세요.">
                    <div class="form-text ip_valid">확인되었습니다.</div>
                    <div class="form-text ip_invalid">아이디를 다시 확인해주세요</div>
                </div>
                <div class="form_wr mt-5 ip_invalid">
                    <div class="ip_tit">
                        <h5>ip_invalid</h5>
                    </div>
                    <input type="text" class="form-control" placeholder="입력해주세요.">
                    <div class="form-text ip_valid">확인되었습니다.</div>
                    <div class="form-text ip_invalid">아이디를 다시 확인해주세요</div>
                </div>
            </div>


<p class="mt-5">카드 기본형태</p>
            <div>
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
                        <div class="d-flex align-items-center rsrv_list border-top mt-4 pt-4">
                            <dt class="tg_400">결제금액</dt>
                            <dd class="  fw_700 ml-auto">50,000원</dd>
                        </div>
                    </div>
                    <div class="card-footer ">
                        <button type="button" class="btn btn-outline-light btn-md btn-block" data-toggle="modal" data-target="#pop_rsrv">예약 취소</button>
                    </div>
                </div>
            </div>


            <!-- 하단 플로팅 버튼을 사용할경우 .sub_pg옆에 .pb_lg 붙해기 : 하단여백을 위해-->
            <div class="bottom_btn  ">
                <div class="form-row">
                    <div class="col-12"><button type="button" class="btn btn-primary btn-block bnt-lg" onclick="location.href='./sign01.php'">하단 플로팅버튼</button></div>
                </div>
            </div>
        </div>

    </div>
</div>


<? include_once("./inc/tail.php"); ?>