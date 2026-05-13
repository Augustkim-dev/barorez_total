<?
$_SUB_HEAD_TITLE = "매장관리";
$_GET['hd_pc'] = ' '; //PC hd  로그인시공백 로그아웃시 logout
$hd_num = 'store'; //왼쪽메뉴 active 땜시 만듬
$hd_left = ' '; //왼쪽메뉴 on 땜시 만듬
include_once("./inc/head.php");
?>

<!-- 왼쪽 메뉴-->
<? include_once("./inc/left_menu.php"); ?>

<div class="sub_pg ">
    <div class="sub_wr">
        <div class="hd_tit2">
            <div class="flex-shrink-0 ml-auto">
                <button type="button" class="btn btn-outline-light rounded-pill " onclick="location.href='./store.php' ">매장정보</button>
                <button type="button" class="btn btn-outline-light rounded-pill ml-2" onclick="location.href='./store_time.php' ">운영시간</button>
                <button type="button" class="btn btn-secondary rounded-pill ml-2" onclick="location.href='./store_set.php' ">기능설졍</button>
            </div>
            <div class="d-flex align-items-end flex-wrap">
                <h3 class="tit_st1 mr-5">매장관리</h3>
            </div>

        </div>
        <div class="store_box">
            <section class="card">
                <div class="card-body">
                    <div>
                        <div class="d-flex justify-content-end">
                            <div class="custom-control custom-switch switch-outside">
                                <input type="checkbox"
                                    class="custom-control-input"
                                    id="customSwitch3_1"
                                    data-on="운영중"
                                    data-off="미운영" checked>
                                <span class="switch-state"></span>
                                <label class="custom-control-label" for="customSwitch3_1"></label>
                            </div>
                        </div>
                        <div class="mt-5">
                            <p class=""><img src="./img/qr_img.jpg" alt=" "></p>
                            <p class="tit_st2 mt-5">테이블 QR 주문</p>
                            <p class="tg_500 mt-1 mb-4">매장 내 테이블에서 QR로 주문합니다</p>
                        </div>
                    </div>
                    <div class="btn-group btn-group-toggle btn_toggle_primary w-100 mt-5" data-toggle="buttons">
                        <label class="btn btn-outline-light   active">
                            <input type="radio" name="options" id="option1" checked=""> 선결제
                        </label>
                        <label class="btn btn-outline-light  ">
                            <input type="radio" name="options" id="option2"> 후불결제
                        </label>
                    </div>

                </div>
            </section>
            <section class="card">
                <div class="card-body">
                    <div>
                        <div class="d-flex justify-content-end">
                            <div class="custom-control custom-switch switch-outside" data-init="true">
                                <input type="checkbox" class="custom-control-input" id="customSwitch3_2" data-on="운영중" data-off="미운영" checked="">
                                <span class="switch-state is-on">운영중</span>
                                <label class="custom-control-label" for="customSwitch3_2"></label>
                            </div>
                        </div>
                        <div class="mt-5">
                            <p class=""><img src="./img/rev_img.jpg" alt=" "></p>
                            <p class="tit_st2 mt-5">예약 기능</p>
                            <p class="tg_500 mt-1 mb-4">고객이 앱으로 방문 예약할 수 있습니다.</p>
                            <p class="tg_500 mt-1 mb-4">24시간 이내 예약 확인이 되지않을 경우 접수가 자동 취소됩니다. </p>
                        </div>

                        <div class="mt-5">
                            <button type="button" class="btn btn-primary btn-block" onclick="location.href='./reserve_stngs.php' ">예약 설정</button>
                        </div>

                    </div>
            </section>
            <section class="card">
                <div class="card-body">
                    <div>
                        <div class="d-flex justify-content-end">
                            <div class="custom-control custom-switch switch-outside" data-init="true">
                                <input type="checkbox" class="custom-control-input" id="customSwitch3_3" data-on="운영중" data-off="미운영" checked="">
                                <span class="switch-state is-on">운영중</span>
                                <label class="custom-control-label" for="customSwitch3_3"></label>
                            </div>
                        </div>
                        <div class="mt-5">
                            <p class=""><img src="./img/pack_img.jpg" alt=" "></p>
                            <p class="tit_st2 mt-5">포장 주문</p>
                            <p class="tg_500 mt-1 mb-4">고객이 앱으로 포장 주문을 할 수 있는 기능입니다.</p>
                            <p class="tg_500 mt-1 mb-4">고객편의를 위해 주문 주문 확인이 10분 이상 되지 않을 경우 자동 취소됩니다. </p>
                        </div>
                    </div>
                    <div class="mt-5">

                    </div>


                </div>
            </section>
        </div>
    </div>
</div>


<? include_once("./inc/tail.php"); ?>
