<?
$_SUB_HEAD_TITLE = "새 메뉴 추가";
$_GET['hd_pc'] = ' '; //PC hd  로그인시공백 로그아웃시 logout
$hd_num = 'menu'; //왼쪽메뉴 active 땜시 만듬
$hd_left = ' '; //왼쪽메뉴 on 땜시 만듬
include_once("./inc/head.php");
?>

<!-- 왼쪽 메뉴-->
<? include_once("./inc/left_menu.php"); ?>

<div class="sub_pg ">
    <div class="sub_wr">
        <div class="hd_tit ">
            <div class="d-flex  ">
                <h2 class="tit_st1 d-flex align-items-center mr-5"><a href="#" onclick="history.back(); return false;" class="mr-4 line_h0 "><img src="./img/ico_back.svg" alt=" 뒤로가기"></a><span>예약설정</span></h2>
            </div>
        </div>
        <form>
            <section class="card mt-4 rounded-lg ">
                <div class="card-body">
                    <div class="form_wr   ">
                        <div class="ip_tit">
                            <h5>예약 안내글</h5>
                        </div>
                        <textarea class="form-control" placeholder="예약취소에 대한 수수료 및 안내사항이 있으실 여기 적어주세요" rows="5"></textarea>
                        <p class="text-right mt-2 tg_500 fs_14">(0/300)</p>
                    </div>
                </div>
            </section>
            <section class="card mt-4 rounded-lg ">
                <div class="card-body">
                    <h3 class="tit_st2">예약 시간 설정</h3>
                    <p class="tg_500 fs_16">예약 가능한 시간대와 테이블 개수를 설정하세요</p>

                    <div class="mt-5 border-top pt-5">
                        <div class="d-flex justify-content-between align-items-center mb-3 ">
                            <h3 class="tit_st3">예약 가능 시간대</h3>
                            <button type="button" class="btn btn-secondary btn-md">시간대 추가</button>
                        </div>
                        <div class="form-row  rounded p-4  align-items-end time_set">
                            <div class="col-2">

                                <div class="custom-switch mb-4">
                                    <input type="checkbox" class="custom-control-input" id="search_switch">
                                    <label class="custom-control-label" for="search_switch"></label>
                                </div>


                            </div>
                            <div class="col-3">
                                <div class="form_wr  ">
                                    <div class="ip_tit  ">
                                        <h5>시간</h5>
                                    </div>
                                    <div class="custom-sel">
                                        <button type="button" class="select-trigger">
                                            평일
                                        </button>
                                        <ul class="select-options">
                                            <li data-value="1">평일</li>
                                            <li data-value="2">토요일</li>
                                            <li data-value="4">일요일</li>
                                        </ul>
                                        <input type="hidden" name="option">
                                    </div>

                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form_wr  ">
                                    <div class="ip_tit  ">
                                        <h5> &nbsp;</h5>
                                    </div>
                                    <div class="time_form">
                                        <div class="input_txt"><span>시</span><input type="number" class="form-control" placeholder="00" value="07"></div>:
                                        <div class="input_txt"><span>분</span><input type="number" class="form-control" placeholder="00" value="00"></div>
                                    </div>

                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form_wr  ">
                                    <div class="ip_tit  ">
                                        <h5>예약건수</h5>
                                    </div>
                                    <input type="number" class="form-control" placeholder="1">
                                    <div class="form-text ip_invalid">반대문구</div>
                                </div>
                            </div>
                            <div class="col-1 text-center">
                              
                                    <a href="" class="mb-3"><img src="./img/ico_delete.svg" alt=" 삭제" style="width:5rem"></a>
                               
                            </div>
                        </div>
                        <div class="form-row  rounded p-4 align-items-end time_set">
                            <div class="col-2">
                                <div class="custom-switch mb-4">
                                    <input type="checkbox" class="custom-control-input" id="search_switch">
                                    <label class="custom-control-label" for="search_switch"></label>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form_wr  ">
                                    <div class="ip_tit  ">
                                        <h5>시간</h5>
                                    </div>
                                    <div class="custom-sel">
                                        <button type="button" class="select-trigger">
                                            평일
                                        </button>
                                        <ul class="select-options">
                                            <li data-value="1">평일</li>
                                            <li data-value="2">토요일</li>
                                            <li data-value="4">일요일</li>
                                        </ul>
                                        <input type="hidden" name="option">
                                    </div>

                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form_wr  ">
                                    <div class="ip_tit  ">
                                        <h5> </h5>
                                    </div>
                                    <div class="time_form">
                                        <div class="input_txt"><span>시</span><input type="number" class="form-control" placeholder="00" value="07"></div> : 
                                        <div class="input_txt"><span>분</span><input type="number" class="form-control" placeholder="00" value="00"></div>
                                    </div>

                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form_wr  ">
                                    <div class="ip_tit  ">
                                        <h5>예약건수</h5>
                                    </div>
                                    <input type="number" class="form-control" placeholder="1">
                                    <div class="form-text ip_invalid">반대문구</div>
                                </div>
                            </div>
                            <div class="col-1 text-center">
                              
                                    <a href="" class="mb-3"><img src="./img/ico_delete.svg" alt=" 삭제" style="width:5rem"></a>
                           

                            </div>
                        </div>
                    </div>
                    <div class="mt-5 border-top pt-5">
                        <h3 class="tit_st3">예약 제한 설정</h3>
                        <div class="border p-5 d-flex align-items-center justify-content-between rounded mt-3">
                            <div class="">
                                <h3 class="tit_st4">당일 예약 허용</h3>
                                <p class="tg_500 mt-2 fs_16">예약 당일에도 예약을 받을 수 있습니다.</p>
                            </div>
                            <div>
                                <div class="custom-control custom-switch switch-outside">
                                    <span class="switch-state"></span>
                                    <input type="checkbox"
                                        class="custom-control-input"
                                        id="customSwitch2"
                                        data-on="사용"
                                        data-off="미사용"
                                        checked>
                                    <label class="custom-control-label" for="customSwitch2"></label>

                                </div>
                            </div>


                        </div>
                        <div class="form-row mt-5">
                            <div class="col-4">
                                <div class="form_wr  ">
                                    <div class="ip_tit  ">
                                        <h5>최대 예약 가능 일수(일)</h5>
                                    </div>
                                    <input type="text" class="form-control" placeholder="0">
                                    <div class="form-text ip_invalid">반대문구</div>
                                    <p class="fs_16 mt-2 tg_500">몇 일 전부터 예약을 받을지 설정합니다</p>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form_wr  ">
                                    <div class="ip_tit  ">
                                        <h5>최소 예약 인원(명)</h5>
                                    </div>
                                    <input type="text" class="form-control" placeholder="1">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form_wr  ">
                                    <div class="ip_tit  ">
                                        <h5>최대 예약 인원(명)</h5>
                                    </div>
                                    <input type="text" class="form-control" placeholder="0">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section class="card mt-4 rounded-lg ">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between ">
                        <div>
                            <h3 class="tit_st2">당일취소 및 미방문 위약금 설정</h3>
                            <p class="tg_500 fs_16">예약 후 미방문(노쇼) 시 위약금을 설정할 수 있습니다.  선결제 주문만 위약금결제가 가능합니다.</p>
                        </div>
                        <div>
                                <div class="custom-control custom-switch switch-outside">
                                    <span class="switch-state"></span>
                                    <input type="checkbox"
                                        class="custom-control-input"
                                        id="customSwitch2"
                                        data-on="사용"
                                        data-off="미사용"
                                        checked>
                                    <label class="custom-control-label" for="customSwitch2"></label>

                                </div>
                            </div>
                    </div>

                    <div class="row mt_50">
                        <div class="col-6">
                            <div class="form_wr  ">
                                <div class="ip_tit  ">
                                    <h5>최대 예약 가능 일수(일)</h5>
                                </div>
                                <div class="d-flex">
                                    <div class="custom-sel mr-2">
                                        <button type="button" class="select-trigger">
                                            고정금액
                                        </button>
                                        <ul class="select-options">
                                            <li data-value="1">고정금액</li>
                                            <li data-value="2">일정비율</li>

                                        </ul>
                                        <input type="hidden" name="option">
                                    </div>
                                    <input type="text" class="form-control" placeholder="0">
                                </div>


                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form_wr  ">
                                <div class="ip_tit  ">
                                    <h5>무료취소 기한</h5>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="custom-sel mr-2">
                                        <button type="button" class="select-trigger">
                                            24시간
                                        </button>
                                        <ul class="select-options">
                                            <li data-value="1">1일</li>
                                            <li data-value="2">3일</li>
                                        </ul>
                                        <input type="hidden" name="option">
                                    </div>
                                   <p class=" flex-shrink-0">전까지</p>
                                </div>
                                <p class="mt-2 fs_15 tg_500">예약 시간 기준으로 지정된 시간 전까지는 위약금 없이 취소 가능합니다</p>


                            </div>
                        </div>
                    </div>
                </div>

            </section>



        </form>
        <div class="text-center mt_50 mb-5">
            <button type="button" class="btn btn-primary btn-lg btn-w1">설정 저장</button>

        </div>
    </div>
</div>


<? include_once("./inc/tail.php"); ?>