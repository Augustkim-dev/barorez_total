<?
$_SUB_HEAD_TITLE = "포장 주문 완료"; //헤더에 타이틀명이 없을경우 공백
$_GET['hd_num'] = ' '; //모바일 hd 1~n까지 있음
$_GET['bt_menu'] = ''; //모바일 하단메뉴 있음1, 없음 공백
include_once("./inc/head.php");
?>
<div class="hd_m align-items-center justify-content-between">
    <div class="hd_btn"></div>
    <div class="page_tit line1_text   flex-fill text-center" style="word-break: break-word;">포장 주문 완료</div>
    <div class=""><button class="hd_btn" type="button"><img src="./img/ico_x.png" alt="닫기"></button></div>
</div>
<div class="wrap">
    <div class="sub_pg ">
        <div class="container ">
            <p class="mt-5 text-center"><img src="./img/ico_ch2.png" alt="체크" style="width: 6.4rem;"></p>
            <p class="tit_st3 mt-4 text-center"> 포장 주문이  완료되었습니다.</p>
            <div class="card  mt-5 mb_20">
                <div class="card-header">
                    <div class="d-flex  align-items-center ">
                        <div class="mr-2">
                            <p class="d-flex align-items-center"><span class="badg green"><span class="ic_img ic_pack mr-2"></span>포장</span> <span class="t_green ml-3">포장 요청</span></p>
                            <a href="./shop2.php">
                                <p class="fs_18 fw_700 mt-2">바다마을 해물칼국수 [성수점] </p>
                            </a>
                        </div>
                        <div class="ml-auto ">
                            <div class="item_img ">
                                <a href="./shop2.php" class="d-block">
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
                        <dd class=" ">2025.07.16 13:48 </dd>
                    </div>
                    <div class="d-flex align-items-center rsrv_list">
                        <dt class="tg_400">예약자</dt>
                        <dd class=" ">김이름(010-1234-1234)</dd>
                    </div>
                    <div class="d-flex align-items-center rsrv_list">
                        <dt class="tg_400">조리시간</dt>
                        <dd class=" ">10~20분 예상</dd>
                    </div>
                    <div class="d-flex align-items-center rsrv_list">
                        <dt class="tg_400">주문메뉴</dt>
                        <dd class="line1_text">(대표메뉴)해물칼국수, 물국수, 주문 내용이 한줄만 나옴</dd>
                    </div>
                    <div class="d-flex align-items-center rsrv_list border-top mt-4 pt-4">
                        <dt class="tg_400">결제금액</dt>
                        <dd class="  fw_700 ml-auto">50,000원</dd>
                    </div>
                </div>
            </div>
            <div class="form-row mt-5">
                <div class="col-6"><button type="button" class="btn btn-outline-primary btn-block" onclick="location.href='./rsrv_history3.php'">주문 내역 </button></div>
                <div class="col-6"><button type="button" class="btn btn-primary btn-block" onclick="location.href='./shop2.php'">홈 </button></div>
            </div>
        </div>
    </div>
</div>
<? include_once("./inc/tail.php"); ?>