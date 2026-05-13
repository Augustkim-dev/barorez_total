<?
$_SUB_HEAD_TITLE = "매장관리>운영시간";
$_GET['hd_pc'] = ' '; //PC hd  로그인시공백 로그아웃시 logout
$hd_num = 'store'; //왼쪽메뉴 active 땜시 만듬
$hd_left = ' '; //왼쪽메뉴 on 땜시 만듬
include_once("./inc/head.php");
?>

<!-- 왼쪽 메뉴-->
<? include_once("./inc/left_menu.php"); ?>

<div class="sub_pg ">
    <div class="sub_wr">
        <div class="hd_tit2 flex-row-reverse">
            <div class="flex-shrink-0 ml-auto">
                <button type="button" class="btn  btn-outline-light rounded-pill " onclick="location.href='./store.php' ">매장정보</button>
                <button type="button" class="btn btn-secondary rounded-pill ml-2" onclick="location.href='./store_time.php' ">운영시간</button>
                <button type="button" class="btn btn-outline-light rounded-pill ml-2" onclick="location.href='./store_set.php' ">기능설졍</button>
            </div>
            <div class="d-flex align-items-end flex-wrap">
                <h3 class="tit_st1 mr-5">매장관리</h3>
            </div>
        </div>
        <form>
            <section class="card">
                <div class="card-body">
                    <div class="tit_st3 d-flex  align-items-center ">
                        <p class="mr-3"><img src="./img/join_ico4.svg" alt="이미지"></p>
                        <div>
                            <p>매장 운영 시간</p>
                            <p class="tg_500 fs_16 fw_400">토요일/일요일/브레이크 타임을 선택하지 않을시 평일운영시간으로 진행됩니다.</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mt-5">
                            <div class="d-flex  justify-content-between mb-4">
                                <h3 class="tit_st4 ">평일 운영</h5>
                                    <div class="custom-control custom-switch switch-outside">
                                        <input type="checkbox"
                                            class="custom-control-input"
                                            id="customSwitch2"
                                            data-on="사용중"
                                            data-off="사용안함" checked>
                                        <span class="switch-state"></span>
                                        <label class="custom-control-label" for="customSwitch2"></label>
                                    </div>
                            </div>
                            <div class="store_time">
                                <div class=" "><input type="number" class="form-control" placeholder="00시"></div>
                                <div class=" "><input type="number" class="form-control" placeholder="00분"></div>
                                <div class=" ">~</div>
                                <div class=" "><input type="number" class="form-control" placeholder="00시"></div>
                                <div class=" "><input type="number" class="form-control" placeholder="00분"></div>
                            </div>
                        </div>
                        <div class="col-md-6 mt-5">
                            <div class="d-flex  justify-content-between mb-4">
                                <h3 class="tit_st4 ">브레이크 타임</h5>
                                    <div class="custom-control custom-switch switch-outside">
                                        <input type="checkbox"
                                            class="custom-control-input"
                                            id="customSwitch2_1"
                                            data-on="사용중"
                                            data-off="사용안함" checked>
                                        <span class="switch-state"></span>
                                        <label class="custom-control-label" for="customSwitch2_1"></label>
                                    </div>
                            </div>
                            <div class="store_time">
                                <div class=" "><input type="number" class="form-control" placeholder="00시"></div>
                                <div class=" "><input type="number" class="form-control" placeholder="00분"></div>
                                <div class=" ">~</div>
                                <div class=" "><input type="number" class="form-control" placeholder="00시"></div>
                                <div class=" "><input type="number" class="form-control" placeholder="00분"></div>
                            </div>
                        </div>
                        <div class="col-md-6 mt-5">
                            <div class="d-flex  justify-content-between mb-4">
                                <h3 class="tit_st4 ">토요일 운영</h5>
                                    <div class="custom-control custom-switch switch-outside">
                                        <input type="checkbox"
                                            class="custom-control-input"
                                            id="customSwitch2_2"
                                            data-on="사용중"
                                            data-off="사용안함">
                                        <span class="switch-state"></span>
                                        <label class="custom-control-label" for="customSwitch2_2"></label>
                                    </div>
                            </div>
                            <div class="store_time">
                                <div class=" "><input type="number" class="form-control" placeholder="00시" disabled></div>
                                <div class=" "><input type="number" class="form-control" placeholder="00분" disabled></div>
                                <div class=" ">~</div>
                                <div class=" "><input type="number" class="form-control" placeholder="00시" disabled></div>
                                <div class=" "><input type="number" class="form-control" placeholder="00분" disabled></div>
                            </div>
                        </div>
                        <div class="col-md-6 mt-5">
                            <div class="d-flex  justify-content-between mb-4">
                                <h3 class="tit_st4 ">일요일 운영</h5>
                                    <div class="custom-control custom-switch switch-outside">
                                        <input type="checkbox"
                                            class="custom-control-input"
                                            id="customSwitch2_3"
                                            data-on="사용중"
                                            data-off="사용안함">
                                        <span class="switch-state"></span>
                                        <label class="custom-control-label" for="customSwitch2_3"></label>
                                    </div>
                            </div>
                            <div class="store_time">
                                <div class=" "><input type="number" class="form-control" placeholder="00시" disabled></div>
                                <div class=" "><input type="number" class="form-control" placeholder="00분" disabled></div>
                                <div class=" ">~</div>
                                <div class=" "><input type="number" class="form-control" placeholder="00시" disabled></div>
                                <div class=" "><input type="number" class="form-control" placeholder="00분" disabled></div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section class="card mt-4">
                <div class="card-body">
                    <h3 class="tit_st3 mb-4 ">정기휴무일</h5>
                        <div class="btn-group-toggle store_gbtn" data-toggle="buttons">
                            <label class="btn btn-outline-light ">
                                <input type="checkbox" name="options" id="option1" checked=""> 일요일
                            </label>
                            <label class="btn btn-outline-light  ">
                                <input type="checkbox" name="options" id="option2"> 월요일
                            </label>
                            <label class="btn btn-outline-light  ">
                                <input type="checkbox" name="options" id="option3"> 화요일
                            </label>
                            <label class="btn btn-outline-light  ">
                                <input type="checkbox" name="options" id="option4"> 수요일
                            </label>
                            <label class="btn btn-outline-light  ">
                                <input type="checkbox" name="options" id="option5"> 목요일
                            </label>
                            <label class="btn btn-outline-light  ">
                                <input type="checkbox" name="options" id="option6"> 금요일
                            </label>
                            <label class="btn btn-outline-light  ">
                                <input type="checkbox" name="options" id="option7"> 토요일
                            </label>
                        </div>
                </div>
            </section>
            <section class="card mt-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="tit_st3  ">임시휴무일</h3>
                            <p class="mb-4 mt-2">해당 날짜가 지나면 자동으로 삭제됩니다.</p>
                        </div>
                        <p><button type="button" class="btn btn-secondary rounded-pill" data-toggle="modal" data-target="#modal_store">추가</button></p>
                    </div>
                    <div class="store_holiday">
                        <button type="button" class="btn btn-light  ">📅 2026.01.08 ~ 2026.01.20 <img src="./img/ic_close.png" alt="닫기" style="width: 1.7rem;" class="ml-2"></button>
                        <button type="button" class="btn btn-light  ">📅 2026.05.01 <img src="./img/ic_close.png" alt="닫기" style="width: 1.7rem;" class="ml-2"></button>
                        <button type="button" class="btn btn-light  ">📅 2026.08.01 ~ 2026.08.04 <img src="./img/ic_close.png" alt="닫기" style="width: 1.7rem;" class="ml-2"></button>

                    </div>

                </div>
            </section>
            <div class="text-center mt_50 mb-5">
                <button type="button" class="btn btn-primary btn-lg btn-w1">저장</button>

            </div>
        </form>

    </div><!-- data-toggle="modal" data-target="#modal_store" 임시휴무일 추가(모달)-->
    <!-- 모달 md 1 -->
    <div class="modal fade" id="modal_store" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">임시휴무일 추가</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><img src="./img/ic_close.png"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex date_input">
                        <input type="date" class="form-control  ">
                        <p>~</p>
                        <input type="date" class="form-control   ">
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="form-row">
                        <div class="col-12"><button type="button" class="btn btn-secondary btn-block">추가</button></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<? include_once("./inc/tail.php"); ?>