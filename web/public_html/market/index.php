<?
$_SUB_HEAD_TITLE = "메인화면";
$hd_pc = ''; //PC hd  로그인시공백 로그아웃시 logout
$hd_num = ''; //왼쪽메뉴 active 땜시 만듬
$hd_left = 'index'; //왼쪽메뉴 on 땜시 만듬
include_once("./inc/head.php");
include_once("./inc/header.php");
include_once("./inc/modal.php");
?>

<!-- 왼쪽 메뉴-->
<? include_once("./inc/left_menu.php"); ?>

<div class="sub_pg ">
    <div class="sub_wr">
        <div class="hd_tit2">
            <div class="flex-shrink-0 ml-auto">
                <button type="button" class="btn btn-outline-light rounded-pill" onclick="location.href='./com-list' ">완료 내역</button>
                <button type="button" class="btn btn-secondary rounded-pill ml-2" data-toggle="modal" data-target="#modal_tbl_add">테이블 추가</button>
            </div>
            <div class="d-flex align-items-end flex-wrap">
                <h3 class="tit_st1 mr-5">테이블관리</h3>
                <div class="btn-group btn-group-toggle gr_st1" data-toggle="buttons">
                    <label class="btn mr-4 active   ">
                        <input type="radio" name="options" id="option1" checked=""> 최신순
                    </label>
                    <label class="btn mr-4  ">
                        <input type="radio" name="options" id="option2"> 테이블명순
                    </label>
                </div>
            </div>

        </div>

        <section class="tbl_box" id="tbl_box">

        </section>

    </div>

</div>

<!-- data-toggle="modal" data-target="#modal_tbl1" B-2 테이블관리 상세(모달)-->
<div class="modal modal_rr fade" id="modal_tbl1" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog ">
        <div class="modal-content">
            <button type="button" class="close1" data-dismiss="modal" aria-label="Close"><img src="<?=DESIGN_HTTP?>/market/img/ic_close.png" alt="닫기"></button>

            <div class="modal-body">
                <div class="d-flex align-items-center justify-content-between">
                    <span class="status status_01">주문접수</span>
                    <p class="d-flex align-items-center justify-content-center fs_16 tg_500">
                        <span class="mr-1"><img src="<?=DESIGN_HTTP?>/market/img/ico_time.svg" alt=" "></span>
                        <span>1분 전</span>
                    </p>
                </div>
                <div class=" detail_hd mt-4">
                    <div>
                        <h3 class="tit_st1">테이블번호 1</h3>
                        <p class="mt-2 fw_600">메뉴3개 ㆍ224,100원ㆍ4인석</p>
                    </div>
                    <button type="button" class="btn btn-primary">음식 준비하기</button>
                </div>
                <section class="bill_wr" id="detail_bill_wrap">
                    <div class="py-4 border-bottom-dot mb-4">
                        <span class="mr-4">주문 번호 : No.00000001</span>
                        <span>주문일시 : 2025년 08월 09일 15:00</span>
                    </div>

                    <ul class="bill_list">
                        <li class="d-flex align-items-center justify-content-between ">
                            <p class="tit_st3">주문메뉴</p>
                            <button type="button" class="btn btn-md btn-outline-light bg-light rounded-pill px-4 " data-toggle="modal" data-target="#modal_tbl2" data-dismiss="modal"><span class="mr-2"><img src="<?=DESIGN_HTTP?>/market/img/ico_edit.svg" alt=" "></span>주문 변경</button>
                        </li>
                        <li>
                            <div class="bill_box">
                                <div class="flex-fill">
                                    <div>
                                        <div class="d-flex   justify-content-between ">
                                            <p class="fw_600 fs_20">(대표메뉴)해물칼국수 </p>
                                            <p class="  flex-shrink-0  ml-4">1개</p>
                                        </div>
                                        <ul class="dot_list tg_500 mt-4">
                                            <li>맵기선택 : 1단계</li>
                                            <li>선택옵션 2 : 라면사리 (+1,000)</li>
                                            <li>선택옵션 3 : 라면사리 (+1,000)</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="bill_money">
                                    8,500원
                                </div>
                            </div>
                        </li>
                        <li class="border-bottom-dot"></li>
                        <li>
                            <div class="bill_box">
                                <div class="flex-fill">
                                    <div>
                                        <div class="d-flex  justify-content-between ">
                                            <p class="fw_600 fs_20">메뉴명이 길때는 이런식으로 나옵니다 메뉴명이 길때는 이런식으로 나옵니다 </p>
                                            <p class="  flex-shrink-0 ml-4">1개</p>
                                        </div>
                                        <ul class="dot_list tg_500 mt-4">
                                            <li>맵기선택 : 1단계</li>
                                            <li>선택옵션 2 : 라면사리 (+1,000)</li>
                                            <li>선택옵션 3 : 라면사리 (+1,000)선택옵션 3 : 라면사리 (+1,000)선택옵션 3 : 라면사리 (+1,000)선택옵션 3 : 라면사리 (+1,000)</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="bill_money">
                                    8,500원
                                </div>
                            </div>
                        </li>
                        <li class="border-bottom-dot"></li>
                        <li>
                            <div class="bill_box">
                                <div class="flex-fill">
                                    <div>
                                        <div class="d-flex  justify-content-between ">
                                            <p class="fw_600 fs_20">옵션이 없을때 </p>
                                            <p class="  flex-shrink-0 ml-4">1개</p>
                                        </div>
                                        <!-- <ul class="dot_list tg_500 mt-4">
                                                <li>맵기선택 : 1단계</li>
                                            </ul> -->
                                    </div>
                                </div>
                                <div class="bill_money">
                                    8,500원
                                </div>
                            </div>
                        </li>
                        <li class="border-bottom">
                        </li>
                        <li class=" ">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <p class=" ">쿠폰 할인</p>
                                <p class="fw_700 fs_20 ">-3,500원</p>
                            </div>
                            <div class="d-flex align-items-center justify-content-between  mb-2">
                                <p class=" ">결제 수단</p>
                                <p class="fw_700 fs_20 ">카드 결제</p>
                            </div>
                            <div class="d-flex align-items-center justify-content-between  mb-2">
                                <p class=" ">총 주문 금액</p>
                                <p class="fw_700 fs_20 ">23,500원</p>
                            </div>
                        </li>
                        <li class="border-bottom border-dark">
                        </li>
                        <li class=" ">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <p class="fw_600">결제 완료 금액</p>
                                <p class="fw_700 fs_24 text-primary ">32,000원</p>
                            </div>
                        </li>
                    </ul>
                </section>

                <!-- 추가 주문이 들어올시 회색바탕으로 감싸여 나옵니다.-->
                <div class="bill_wr_add" id="detail_add_wrap" style="display:none;">
                    <div class="bg-primary text-white p-2 text-center">
                        추가주문
                    </div>
                    <div class="px-4">
                        <section class="bill_wr">
                            <div class="py-4 border-bottom-dot mb-4">

                                <span class="mr-4">주문 번호 : No.00000001</span>
                                <span>주문일시 : 2025년 08월 09일 15:00</span>
                            </div>


                            <ul class="bill_list">
                                <li class="d-flex align-items-center justify-content-between ">
                                    <p class="tit_st3">주문메뉴</p>
                                    <button type="button" class="btn btn-md btn-outline-light bg-light rounded-pill px-4 " data-toggle="modal" data-target="#modal_tbl2"><span class="mr-2"><img src="<?=DESIGN_HTTP?>/market/img/ico_edit.svg" alt=" "></span>주문 변경</button>
                                </li>
                                <li>
                                    <div class="bill_box">
                                        <div class="flex-fill">
                                            <div>
                                                <div class="d-flex   justify-content-between ">
                                                    <p class="fw_600 fs_20">(대표메뉴)해물칼국수 </p>
                                                    <p class="  flex-shrink-0  ml-4">1개</p>
                                                </div>
                                                <ul class="dot_list tg_500 mt-4">
                                                    <li>맵기선택 : 1단계</li>
                                                    <li>선택옵션 2 : 라면사리 (+1,000)</li>
                                                    <li>선택옵션 3 : 라면사리 (+1,000)</li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="bill_money">
                                            8,500원
                                        </div>
                                    </div>
                                </li>
                                <li class="border-bottom-dot"></li>
                                <li>
                                    <div class="bill_box">
                                        <div class="flex-fill">
                                            <div>
                                                <div class="d-flex  justify-content-between ">
                                                    <p class="fw_600 fs_20">메뉴명이 길때는 이런식으로 나옵니다 메뉴명이 길때는 이런식으로 나옵니다 </p>
                                                    <p class="  flex-shrink-0 ml-4">1개</p>
                                                </div>
                                                <ul class="dot_list tg_500 mt-4">
                                                    <li>맵기선택 : 1단계</li>
                                                    <li>선택옵션 2 : 라면사리 (+1,000)</li>
                                                    <li>선택옵션 3 : 라면사리 (+1,000)선택옵션 3 : 라면사리 (+1,000)선택옵션 3 : 라면사리 (+1,000)선택옵션 3 : 라면사리 (+1,000)</li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="bill_money">
                                            8,500원
                                        </div>
                                    </div>
                                </li>
                                <li class="border-bottom-dot"></li>
                                <li>
                                    <div class="bill_box">
                                        <div class="flex-fill">
                                            <div>
                                                <div class="d-flex  justify-content-between ">
                                                    <p class="fw_600 fs_20">옵션이 없을때 </p>
                                                    <p class="  flex-shrink-0 ml-4">1개</p>
                                                </div>
                                                <!-- <ul class="dot_list tg_500 mt-4">
                                                <li>맵기선택 : 1단계</li>
                                            </ul> -->
                                            </div>
                                        </div>
                                        <div class="bill_money">
                                            8,500원
                                        </div>
                                    </div>
                                </li>
                                <li class="border-bottom">
                                </li>
                                <li class=" ">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <p class=" ">쿠폰 할인</p>
                                        <p class="fw_700 fs_20 ">-3,500원</p>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between  mb-2">
                                        <p class=" ">결제 수단</p>
                                        <p class="fw_700 fs_20 ">카드 결제</p>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between  mb-2">
                                        <p class=" ">총 주문 금액</p>
                                        <p class="fw_700 fs_20 ">23,500원</p>
                                    </div>
                                </li>
                                <li class="border-bottom border-dark">
                                </li>
                                <li class=" ">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <p class="fw_600">결제 완료 금액</p>
                                        <p class="fw_700 fs_24 text-primary ">32,000원</p>
                                    </div>
                                </li>
                            </ul>
                        </section>
                    </div>

                </div>
                <!-- 결제 취소 버튼을 누르면 .pay_cncl 나오게 해주세요-->
                <!--                    <button type="button" class="btn btn-secondary btn-block mt-4">결제 취소</button>-->
                <!--                    <div class="pay_cncl">-->
                <!--                        <div class="form_wr  ">-->
                <!--                            <div class="ip_tit  ">-->
                <!--                                <h5 class="   text-white">결제취소/환불 금액(원)</h5>-->
                <!--                            </div>-->
                <!--                            <div class="form-row ">-->
                <!--                                <div class="col-6">-->
                <!--                                    <input type="text" class="form-control" placeholder="환불 금액 입력">-->
                <!--                                </div>-->
                <!--                                <div class="col-3">-->
                <!--                                    <button type="button" class="btn btn-primary btn-block px-1">확인</button>-->
                <!--                                </div>-->
                <!--                                <div class="col-3">-->
                <!--                                    <button type="button" class="btn btn-outline-light btn-block px-1">취소</button>-->
                <!--                                </div>-->
                <!--                            </div>-->
                <!---->
                <!--                        </div>-->
                <!--                    </div>-->
                <div class="mt-4 d-flex align-items-center justify-content-between bg-light p-5 rounded">
                    <p class="fw_600">고객정보</p>
                    <p>홍길동 &#40;010-1234-5678&#41;</p>
                </div>
                <div class="form-row mt-5">
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-light btn-lg btn-block fs_20 btn_qr"><span class="mr-2"><img src="<?=DESIGN_HTTP?>/market/img/ico_qr.svg" alt=" "></span> QR코드 보기</button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-light  btn-lg btn-block  fs_20 btn_tbl_change"><span class="mr-2"><img src="<?=DESIGN_HTTP?>/market/img/ico_change.svg" alt=" "></span> 자리 이동</button>
                    </div>
                </div>
                <div class="btn_qr_wr" style="display:none;">
                    <div class="py-5 px-4 text-center">
                        <p class="mb-3">QR코드는 png 이미지파일로 다운로드됩니다.</p>
                        <img id="detail_qr_img" src="<?=DESIGN_HTTP?>/market/img/qrimg.jpg" alt="qr생성시 예시이미지">

                        <div class="d-flex justify-content-center mt-3">
                            <button type="button" class="btn btn-outline-light   mr-2">QR 새로 생성</button> <button type="button" class="btn btn-outline-light  ">QR 다운로드</button>
                        </div>
                        <button class="btn btn_close btn-light btn-block mt-5"><img src="<?=DESIGN_HTTP?>/market/img/selectarrow_up.svg" alt=" "></button>
                    </div>

                </div>

                <div class="btn_tbl_change_wr" style="display:none;">
                    <div class="mt-4 text-center bg-light rounded p-4">

                        <div class="  btn-group-toggle tbl_cbox" data-toggle="buttons">
                            <label class="btn btn-outline-primary    active">
                                <input type="radio" name="options" id="option1" checked=""> <b>ABED</b><span>4인석</span>
                            </label>
                            <label class="btn btn-outline-primary    ">
                                <input type="radio" name="options" id="option2"> <b>ABED</b><span>4인석</span>
                            </label>
                            <label class="btn btn-outline-primary   ">
                                <input type="radio" name="options" id="option3"> <b>ABED</b><span>4인석</span>
                            </label>
                            <label class="btn btn-outline-primary   ">
                                <input type="radio" name="options" id="option4"> <b>ABED</b><span>4인석</span>
                            </label>
                            <label class="btn btn-outline-primary   ">
                                <input type="radio" name="options" id="option5"> <b>ABED</b><span>4인석</span>
                            </label>
                        </div>

                        <button type="button" class="btn btn-secondary  btn-block mt-4" id="btnMoveSubmit">자리 이동</button>
                    </div>
                    <button class="btn btn_close btn-light btn-block mt-5"><img src="<?=DESIGN_HTTP?>/market/img/selectarrow_up.svg" alt=" "></button>

                </div>

                <script>
                    $('.btn_qr').on('click', function() {
                        // 내용 열기
                        $('.btn_qr_wr').stop().slideDown(200).addClass('active');
                        $('.btn_tbl_change_wr').stop().slideUp(200).removeClass('active');

                        // 버튼 active
                        $('.btn_qr').addClass('active');
                        $('.btn_tbl_change').removeClass('active');
                    });

                    $('.btn_tbl_change').on('click', function() {
                        // 내용 열기
                        $('.btn_tbl_change_wr').stop().slideDown(200).addClass('active');
                        $('.btn_qr_wr').stop().slideUp(200).removeClass('active');

                        // 버튼 active
                        $('.btn_tbl_change').addClass('active');
                        $('.btn_qr').removeClass('active');
                    });

                    $('.btn_close').on('click', function() {
                        // 내용 닫기
                        $(this)
                            .closest('.btn_qr_wr, .btn_tbl_change_wr')
                            .stop()
                            .slideUp(200)
                            .removeClass('active');

                        // 버튼 active 전부 제거
                        $('.btn_qr, .btn_tbl_change').removeClass('active');
                    });
                    $('.modal').on('hidden.bs.modal', function() {
                        $(this).find('.btn_qr_wr, .btn_tbl_change_wr')
                            .hide()
                            .removeClass('active');

                        $(this).find('.btn_qr, .btn_tbl_change')
                            .removeClass('active');
                    });
                </script>
            </div>

        </div>
    </div>
</div>




<!-- data-toggle="modal" data-target="#modal_tbl2" B-3 주문수정(모달) -->
<div class="modal modal_rr fade" id="modal_tbl2" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog ">
        <div class="modal-content">
            <button type="button" class="close1" data-dismiss="modal" aria-label="Close"><img src="<?=DESIGN_HTTP?>/market/img/ic_close.png" alt="닫기"></button>
            <div class="modal-body">

                <div class=" detail_hd mt-4">
                    <h2 class="tit_st1 d-flex align-items-center"><a href="#" data-toggle="modal" data-target="#modal_tbl1" data-dismiss="modal" class="mr-4 line_h0 "><img src="<?=DESIGN_HTTP?>/market/img/ico_back.svg" alt=" 뒤로가기"></a><span>주문 변경</span></h2>
                    <button type="button" class="btn btn-primary" data-target="#modal_tbl1" >변경 완료</button>
                </div>
                <section class="py-5 border-top border-dark">

                    <ul class="bill_list wide_gap">

                        <li>
                            <div class="bill_box">
                                <div class="flex-fill">
                                    <div>
                                        <div class="d-flex   justify-content-between ">
                                            <p class="fw_600 fs_20">(대표메뉴)해물칼국수 </p>
                                            <p class="  flex-shrink-0  ml-4">1개</p>
                                        </div>
                                        <ul class="dot_list tg_500 mt-4">
                                            <li>맵기선택 : 1단계</li>
                                            <li>선택옵션 2 : 라면사리 (+1,000)</li>
                                            <li>선택옵션 3 : 라면사리 (+1,000)</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="bill_money">
                                    8,500원
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <div class="d-flex">
                                    <div class="item_opt_counter mr-2">
                                        <button type="button" class="btn item_opt_counter_btn pl-1" disabled=""><!-- 수량이 0일때 -->
                                            <img src="<?=DESIGN_HTTP?>/market/img/ico_decrease.svg" alt="감소">
                                        </button>
                                        <input type="text" class="quantity" value="255">
                                        <button type="button" class="btn item_opt_counter_btn pr-1">
                                            <img src="<?=DESIGN_HTTP?>/market/img/ico_increase.svg" alt="증가">
                                        </button>
                                    </div>
                                    <button type="button" class="btn btn-outline-light  " data-toggle="modal" data-target="#modal_tbl3" data-dismiss="modal">옵션 변경</button>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-outline-secondary  ">메뉴 삭제</button>
                                </div>
                            </div>
                        </li>
                        <li class="border-bottom"></li>
                        <li>
                            <div class="bill_box">
                                <div class="flex-fill">
                                    <div>
                                        <div class="d-flex   justify-content-between ">
                                            <p class="fw_600 fs_20">(대표메뉴)해물칼국수 </p>
                                            <p class="  flex-shrink-0  ml-4">1개</p>
                                        </div>
                                        <ul class="dot_list tg_500 mt-4">
                                            <li>맵기선택 : 1단계</li>
                                            <li>선택옵션 2 : 라면사리 (+1,000)</li>
                                            <li>선택옵션 3 : 라면사리 (+1,000)</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="bill_money">
                                    8,500원
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <div class="d-flex">
                                    <div class="item_opt_counter mr-2">
                                        <button type="button" class="btn item_opt_counter_btn pl-1" disabled=""><!-- 수량이 0일때 -->
                                            <img src="<?=DESIGN_HTTP?>/market/img/ico_decrease.svg" alt="감소">
                                        </button>
                                        <input type="text" class="quantity" value="255">
                                        <button type="button" class="btn item_opt_counter_btn pr-1">
                                            <img src="<?=DESIGN_HTTP?>/market/img/ico_increase.svg" alt="증가">
                                        </button>
                                    </div>
                                    <button type="button" class="btn btn-outline-light  " data-toggle="modal" data-target="#modal_tbl3" data-dismiss="modal">옵션 변경</button>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-outline-secondary  ">메뉴 삭제</button>
                                </div>
                            </div>
                        </li>
                        <li class="border-bottom"></li>

                    </ul>
                </section>
            </div>

        </div>
    </div>
</div>


<!-- data-toggle="modal" data-target="#modal_tbl3" B-4 주문수정 (옵션변경)(모달) -->
<div class="modal modal_rr fade" id="modal_tbl3" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog ">
        <div class="modal-content">
            <button type="button" class="close1" data-dismiss="modal" aria-label="Close"><img src="<?=DESIGN_HTTP?>/market/img/ic_close.png" alt="닫기"></button>
            <div class="modal-body">

                <div class=" detail_hd mt-4">
                    <h2 class="tit_st1 d-flex align-items-center"><a href="#" data-toggle="modal" data-target="#modal_tbl2" data-dismiss="modal" class="mr-4 line_h0 "><img src="<?=DESIGN_HTTP?>/market/img/ico_back.svg" alt=" 뒤로가기"></a><span>옵션 변경</span></h2>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal_tbl2" data-dismiss="modal">변경 완료</button>
                </div>
                <section class="py-5 border-top border-dark">


                    <ul class="bill_list wide_gap">
                        <li>
                            <div class="d-flex  justify-content-between align-items-center">
                                <div class="">
                                    <p class="fs_20">(대표메뉴)해물칼국수 </p>
                                    <p class="tit_st1 mt-2">8,500원</p>
                                </div>
                                <div>
                                    <div class="item_opt_counter">
                                        <button type="button" class="btn item_opt_counter_btn pl-1" disabled=""><!-- 수량이 0일때 -->
                                            <img src="<?=DESIGN_HTTP?>/market/img/ico_decrease.svg" alt="감소">
                                        </button>
                                        <input type="text" class="quantity" value="0">
                                        <button type="button" class="btn item_opt_counter_btn pr-1">
                                            <img src="<?=DESIGN_HTTP?>/market/img/ico_increase.svg" alt="증가">
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="border-bottom">
                        </li>

                        <li>
                            <p class="tit_st4 mb-4">맵기 선택 <span class="ml-3 text-primary">필수</span></p>
                            <div class="opt_checks_wp">
                                <div class="checks opt_checks">
                                    <label>
                                        <input type="checkbox" name="chk1">
                                        <span class="ic_box"></span>
                                        <div class="chk_p">
                                            <p>1단계 </p>
                                        </div>
                                        <p class="fw_700 flex-shrink-0 item_opmm">20,500원</p>
                                    </label>
                                </div>
                                <div class="checks opt_checks">
                                    <label>
                                        <input type="checkbox" name="chk1">
                                        <span class="ic_box"></span>
                                        <div class="chk_p">
                                            <p>1단계 </p>
                                        </div>
                                        <p class="fw_700 flex-shrink-0 item_opmm">20,500원</p>
                                    </label>
                                </div>
                            </div>
                        </li>
                        <li class="border-bottom">
                        </li>
                        <li>
                            <p class="tit_st4 mb-4">토핑 추가 <span class="ml-3 tg_500 fs_18">선택</span></p>
                            <div class="opt_checks_wp">
                                <div class="checks opt_checks">
                                    <label>
                                        <input type="checkbox" name="chk1">
                                        <span class="ic_box"></span>
                                        <div class="chk_p">
                                            <p>옵션값이 너무 길때 어떻게 나올지 텍스트 쳐봅니다. 옵션값이 너무 길때 어떻게 나올지 텍스트 쳐봅니다 옵션값이 너무 길때 어떻게 나올지 텍스트 쳐봅니다. 옵션값이 너무 길때 어떻게 나올지 텍스트 쳐봅니다</p>
                                        </div>
                                        <p class="fw_700 flex-shrink-0 item_opmm">20,500원</p>
                                    </label>
                                </div>
                                <div class="checks opt_checks">
                                    <label>
                                        <input type="checkbox" name="chk1">
                                        <span class="ic_box"></span>
                                        <div class="chk_p">
                                            <p>1단계 </p>
                                        </div>
                                        <p class="fw_700 flex-shrink-0 item_opmm">20,500원</p>
                                    </label>
                                </div>
                            </div>
                        </li>
                        <li class="border-bottom">
                        </li>


                    </ul>
                </section>
            </div>

        </div>
    </div>
</div>


<!-- data-toggle="modal" data-target="#modal_tbl_add" B-6 테이블 추가(모달) -->
<div class="modal modal_rr fade" id="modal_tbl_add" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog ">
        <div class="modal-content">
            <button type="button" class="close1" data-dismiss="modal" aria-label="Close"><img src="<?=DESIGN_HTTP?>/market/img/ic_close.png" alt="닫기"></button>
            <div class="modal-body">

                <div class=" detail_hd mt-4">
                    <h2 class="tit_st1 d-flex align-items-center"> <span>테이블 추가</span></h2>
                    <button type="button" class="btn btn-primary" data-dismiss="modal" id="btnAddTableFinal">추가하기</button>
                </div>
                <section class="py-5 border-top border-dark">

                    <div class="row">
                        <div class="col-4 fw_600 mb-4 d-flex align-items-center">
                            테이블명
                        </div>
                        <div class="col-8 mb-4">
                            <input type="text" class="form-control" id="add_tb_name" placeholder="5자 미만 숫자 or 영문만 가능합니다.">
                        </div>
                        <div class="col-4 fw_600 mb-4 d-flex align-items-center">
                            좌석 수
                        </div>
                        <div class="col-8 mb-4">
                            <input type="text" class="form-control" id="add_tb_seats" placeholder="1">
                        </div>
                        <div class="col-4 fw_600 mb-4 d-flex align-items-center">
                            QR코드 생성
                        </div>
                        <div class="col-8 mb-4">
                            <button type="button" class="btn btn-secondary" id="btnGenQrTemp">코드 생성하기</button>
                            <p class="mt-4" id="add_qr_preview">
                                <!--                                    <img src="--><?php //=DESIGN_HTTP?><!--/market/img/qrimg.jpg" alt="qr생성시 예시이미지">-->
                            </p>
                            <div class="d-flex justify-content-between mt-3">
                                <button type="button" class="btn btn-outline-light mr-2 w-50" id="btnQrRegen">QR 새로 생성</button> <button type="button" class="btn btn-outline-light w-50" id="btnQrDownload">QR 다운로드</button>
                            </div>
                        </div>

                    </div>

                </section>
            </div>

        </div>
    </div>
</div>

<script>
    (function (global) {
        'use strict';
        if (!global.jQuery) return;

        const $ = global.jQuery;

        // =========================================================
        // CONFIG
        // =========================================================
        const API_URL = './table_api.php';
        const POLL_MS = 50000;

        // =========================================================
        // UTILS
        // =========================================================
        function uiAlert(title, message, cb) {
            if (global.ModalUtil && typeof global.ModalUtil.alert === 'function') {
                global.ModalUtil.alert({ title: title || '알림', message: message || '', onOk: cb || null });
                return;
            }
            alert((title ? '[' + title + '] ' : '') + (message || ''));
            if (typeof cb === 'function') cb();
        }

        function uiConfirm(title, message, onOk, onCancel) {
            if (global.ModalUtil && typeof global.ModalUtil.confirm === 'function') {
                global.ModalUtil.confirm({
                    title: title || '확인',
                    message: message || '',
                    onOk: onOk || null,
                    onCancel: onCancel || null,
                });
                return;
            }
            if (confirm((title ? '[' + title + '] ' : '') + (message || ''))) {
                if (typeof onOk === 'function') onOk();
            } else {
                if (typeof onCancel === 'function') onCancel();
            }
        }

        function toNum(v, def) {
            const n = Number(v);
            return isNaN(n) ? (def || 0) : n;
        }

        function pickFirst() {
            for (let i = 0; i < arguments.length; i++) {
                const v = arguments[i];
                if (v !== undefined && v !== null && v !== '') return v;
            }
            return '';
        }

        function deepClone(obj) {
            try { return JSON.parse(JSON.stringify(obj || {})); }
            catch (e) { return obj; }
        }

        function escHtml(s) {
            return String(s == null ? '' : s).replace(/[&<>"']/g, function (m) {
                return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[m];
            });
        }

        function won(v) {
            const n = Number(v);
            if (!v || isNaN(n)) return '0원';
            return n.toLocaleString() + '원';
        }

        function onlyNumberStr(s) { return String(s || '').replace(/[^0-9]/g, ''); }
        function formatComma(n) { return Number(n || 0).toLocaleString(); }
        function parseAmount(s) {
            const raw = onlyNumberStr(s);
            return raw ? Number(raw) : 0;
        }

        function fmtDate(dtStr) {
            if (!dtStr) return '';
            const s = String(dtStr);
            const parts = s.split(' ');
            const d = parts[0] || '';
            const t = parts[1] || '';
            const ds = d.split('-');
            const y = ds[0] || '';
            const m = ds[1] || '';
            const dd = ds[2] || '';
            let hhmm = '';
            if (t) {
                const ts = t.split(':');
                const hh = ts[0] || '00';
                const mm = ts[1] || '00';
                hhmm = hh + ':' + mm;
            }
            if (!y) return s;
            return y + '년 ' + m + '월 ' + dd + '일 ' + hhmm;
        }

        // =========================================================
        // STATUS / SORT
        // =========================================================
        function statusToUi(st) {
            st = (st || '').toUpperCase();
            if (st === 'RECEIVED')  return { label: '주문접수',   cls: 'status_01', btnType: 'primary',         btnText: '음식 준비하기', action: 'prepare' };
            if (st === 'PREPARING') return { label: '음식준비중', cls: 'status_02', btnType: 'outline-primary', btnText: '전달 완료',     action: 'serve' };
            if (st === 'SERVED')    return { label: '전달완료',   cls: 'status_03', btnType: 'outline-light',   btnText: '좌석 비우기',   action: 'clear' };
            return { label: '빈자리', cls: 'status_04', btnType: '', btnText: '', action: '' };
        }

        function hasOrder(t) {
            return !!(t && t.tv_idx && String(t.status || '').toUpperCase() !== 'EMPTY');
        }

        let SORT_MODE = 'latest';

        function bindSortControls() {
            $(document).on('change', 'input[name="options"]', function () {
                const id = $(this).attr('id');
                SORT_MODE = id === 'option2' ? 'table' : 'latest';
                fetchList();
            });
        }

        function isOrderedTable(t) {
            const st = String(t.status || '').toUpperCase();
            return !!(t && t.tv_idx && st !== 'EMPTY');
        }

        function sortLatestFirst(a, b) {
            const ao = isOrderedTable(a);
            const bo = isOrderedTable(b);
            if (ao !== bo) return ao ? -1 : 1;

            if (ao && bo) {
                const al = parseInt(a.latest_order_idx || 0, 10);
                const bl = parseInt(b.latest_order_idx || 0, 10);
                if (al !== bl) return bl - al;
                return parseInt(a.table_no || 0, 10) - parseInt(b.table_no || 0, 10);
            }
            return parseInt(a.table_no || 0, 10) - parseInt(b.table_no || 0, 10);
        }

        function sortTableNo(a, b) {
            return parseInt(a.table_no || 0, 10) - parseInt(b.table_no || 0, 10);
        }

        function applySort(tables) {
            const arr = (tables || []).slice();
            if (SORT_MODE === 'table') arr.sort(sortTableNo);
            else arr.sort(sortLatestFirst);
            return arr;
        }

        // =========================================================
        // GLOBAL STATE
        // =========================================================
        const currentDetail = { tv_idx: 0, table_no: 0, status: '' };
        const DetailCache = { ordersByIdx: {} }; // order_idx -> orderObject

        // 주문 편집 컨텍스트
        const EditCtx = {
            tv_idx: 0,
            order_idx: 0,
            draft: null,           // snapshot draft { items:[], summary:{} }
            pendingItem: null,          // draft에 아직 안 넣은 임시 메뉴(옵션 선택 전/중)
            pendingCommitted: false,    // modal3 변경완료로 확정했는지 여부
        };

        // =========================================================
        // API COMMON
        // =========================================================
        function apiPost(data, onOk, onErr) {
            $.ajax({
                url: API_URL,
                method: 'POST',
                dataType: 'json',
                data: data || {},
                success: function (res) { onOk && onOk(res); },
                error: function (xhr) { onErr && onErr(xhr); },
            });
        }

        // =========================================================
        // LIST PAGE
        // =========================================================
        function fetchList() {
            apiPost(
                { act: 'list' },
                function (res) {
                    if (!res || !res.success) return;
                    const tables = res.data && res.data.tables ? res.data.tables : [];
                    renderTables(applySort(tables));
                },
                function (xhr) {
                    console.log('[fetchList] error', xhr.status, xhr.responseText);
                }
            );
        }

        function doAction(action, tvIdx, onDone) {
            apiPost(
                { act: 'action', action: action, tv_idx: tvIdx },
                function (res) {
                    if (!res || !res.success) {
                        uiAlert('처리 실패', (res && res.message) ? res.message : '처리에 실패했습니다.');
                        return;
                    }
                    if (typeof onDone === 'function') onDone(res);
                    fetchList();
                },
                function (xhr) {
                    console.log('[action] error', xhr.status, xhr.responseText);
                    uiAlert('서버 오류', '처리에 실패했습니다.');
                }
            );
        }

        function deleteTable(tableNo, onDone) {
            apiPost(
                { act: 'delete_table', table_no: tableNo },
                function (res) {
                    if (!res || !res.success) {
                        uiAlert('삭제 실패', (res && res.message) ? res.message : '삭제 실패');
                        return;
                    }
                    if (typeof onDone === 'function') onDone(res);
                    fetchList();
                },
                function () {
                    uiAlert('서버 오류', '테이블 삭제 중 오류가 발생했습니다.');
                }
            );
        }

        function renderTables(tables) {
            const $box = $('#tbl_box');
            $box.empty();

            (tables || []).forEach(function (t) {
                const st = statusToUi(t.status);
                const ordered = hasOrder(t);
                const isEmpty = !ordered;

                let btnHtml = '';
                if (ordered && st.action) {
                    btnHtml =
                        '<div class="mt_20 position-relative">' +
                        '  <button type="button" class="btn btn-' + st.btnType + ' btn-block px-1 btn-table-action" data-action="' + st.action + '" data-tv-idx="' + t.tv_idx + '">' + st.btnText + '</button>' +
                        (t.has_new ? '<div class="tooltip-bubble floating">추가 주문이에요! 😃</div>' : '') +
                        '</div>';
                }

                let emptyHtml = '';
                if (isEmpty) {
                    emptyHtml =
                        '<div class="mt_20 position-relative">' +
                        '  <button type="button" class="btn btn-outline-light btn-block px-1 btn-table-qr" data-table-no="' + (t.table_no || '') + '">QR 코드 확인</button>' +
                        '  <button type="button" class="btn btn-outline-light btn-block px-1 btn-table-delete" data-table-no="' + (t.table_no || '') + '">테이블삭제</button>' +
                        '</div>';
                }

                let linkHtml = '';
                if (ordered) {
                    linkHtml =
                        '<a href="#" class="item_link btn-open-detail" data-target="#modal_tbl1" ' +
                        'data-tv-idx="' + t.tv_idx + '" data-table-no="' + t.table_no + '" data-status="' + (t.status || '') + '"></a>';
                }

                const itemsSummary = t.items_summary || '';
                const totalPrice = t.total_price ? won(t.total_price) : '';
                const elapsed = t.elapsed || '';

                const cardHtml =
                    '<div class="card ' + (isEmpty ? 'empty_tbl' : '') + '" data-tv-idx="' + (t.tv_idx || 0) + '" data-table-no="' + (t.table_no || '') + '" data-status="' + (t.status || '') + '">' +
                    '  <div class="d-flex align-items-center justify-content-between">' +
                    '    <span class="status ' + st.cls + '">' + st.label + '</span>' +
                    (elapsed
                        ? '    <p class="d-flex align-items-center justify-content-center fs_16 tg_500">' +
                        '      <span class="mr-1"><img src="<?=DESIGN_HTTP?>/market/img/ico_time.svg" alt=" "></span>' +
                        '      <span>' + elapsed + '</span>' +
                        '    </p>'
                        : '') +
                    '  </div>' +
                    '  <p class="fw_800 fs_44 mt_35 text-center">' + (t.table_name || '') + '</p>' +
                    (itemsSummary ? '<p class="tg_500 mt_20 line1_text text-center">' + itemsSummary + '</p>' : '') +
                    (totalPrice ? '<p class="fw_700 mt-1 text-center">' + totalPrice + '</p>' : '') +
                    (isEmpty ? emptyHtml : btnHtml) +
                    linkHtml +
                    '</div>';

                $box.append(cardHtml);
            });
        }

        // =========================================================
        // DETAIL MODAL (modal_tbl1)
        // =========================================================
        function fetchDetail(tvIdx, cb) {
            apiPost(
                { act: 'detail', tv_idx: tvIdx },
                function (res) {
                    if (!res || !res.success) {
                        uiAlert('오류', (res && res.message) ? res.message : '상세 정보를 불러오지 못했습니다.');
                        return;
                    }
                    if (typeof cb === 'function') cb(res.data);
                },
                function (xhr) {
                    console.log('[detail] error', xhr.status, xhr.responseText);
                    uiAlert('서버 오류', '상세 정보를 불러오지 못했습니다.');
                }
            );
        }

        function renderDetailModal(data) {
            const st = statusToUi(data.status);

            $('#modal_tbl1 .status').attr('class', 'status ' + st.cls).text(st.label);
            $('#modal_tbl1 .detail_hd h3.tit_st1').text('테이블번호 ' + (data.table_no || ''));

            const $topBtn = $('#modal_tbl1 .detail_hd button.btn');
            $topBtn.show();

            const upper = String(data.status || '').toUpperCase();
            if (upper === 'RECEIVED') {
                $topBtn.attr('class', 'btn btn-primary btn-detail-action').text('음식 준비하기').data('action', 'prepare');
            } else if (upper === 'PREPARING') {
                $topBtn.attr('class', 'btn btn-outline-primary btn-detail-action').text('전달 완료').data('action', 'serve');
            } else if (upper === 'SERVED') {
                $topBtn.attr('class', 'btn btn-outline-light btn-detail-action').text('좌석 비우기').data('action', 'clear');
            } else {
                $topBtn.hide();
            }
        }

        function payLabel(payType, payStatus) {
            const pt = String(payType || '').toUpperCase();
            const ps = String(payStatus || '').toUpperCase();
            if (pt === 'PREPAID') return ps === 'PAID' ? '선결제(결제완료)' : '선결제(미결제)';
            if (pt === 'POSTPAID') return ps === 'PAID' ? '후결제(결제완료)' : '후결제';
            return '결제';
        }

        function renderOrderBlock(order) {
            if (!order) return '';

            const orderIdx = Number(order.idx || 0); // orders_t.idx
            const otNumber = escHtml(order.ot_number || '');
            const otWdate = escHtml(order.ot_wdate || '');

            const totalFromFields = Number(order.order_total || order.ot_total_price || 0);
            const discount = Number(order.order_discount || order.ot_discount_amount || 0);
            const paid = (String(order.ot_pay_status || '').toUpperCase() === 'PAID');

            const couponText = discount > 0 ? `-${won(discount)}` : '미사용';
            const payText = payLabel(order.ot_pay_type, order.ot_pay_status);

            const snap = order.snapshot_obj || {};
            const items = snap.items || [];

            const refundedAmount = Number(order.refunded_amount || 0);
            const paidAmount = paid ? Math.max(0, totalFromFields - refundedAmount) : 0;

            let itemsHtml = '';
            items.forEach((it, idx) => {
                const name = escHtml(it.menu_name || it.name || '');
                const qty = Number(it.quantity || 1);

                // 스냅샷 total_price가 있으면 표시엔 그걸 쓰고, 없으면 단가*수량
                const linePrice = Number(
                    (it.total_price !== undefined && it.total_price !== null)
                        ? it.total_price
                        : (Number(it.unit_price || 0) * qty)
                );

                const options = Array.isArray(it.options) ? it.options : [];
                let optHtml = '';
                if (options.length) {
                    const li = options.map(op => {
                        const nm = escHtml(op.option_name ?? op.om_title ?? op.title ?? op.name ?? '');
                        const oq = Number(op.quantity ?? 1);
                        const pr = Number(op.option_price ?? op.price ?? op.add_price ?? 0);
                        if (!nm) return '';
                        const tail = `${oq > 1 ? ` x${oq}` : ''}${pr ? ` (+${won(pr)})` : ''}`;
                        return `<li>${nm}${tail}</li>`;
                    }).filter(Boolean).join('');
                    if (li) optHtml = `<ul class="dot_list tg_500 mt-4">${li}</ul>`;
                }

                const needDot = idx !== items.length - 1;
                itemsHtml += ''
                    + '<li>'
                    + '  <div class="bill_box">'
                    + '    <div class="flex-fill">'
                    + '      <div>'
                    + '        <div class="d-flex justify-content-between">'
                    + '          <p class="fw_600 fs_20">' + name + '</p>'
                    + '          <p class="flex-shrink-0 ml-4">' + qty + '개</p>'
                    + '        </div>'
                    +          optHtml
                    + '      </div>'
                    + '    </div>'
                    + '    <div class="bill_money">' + won(linePrice) + '</div>'
                    + '  </div>'
                    + '</li>'
                    + (needDot ? '<li class="border-bottom-dot"></li>' : '');
            });

            const refundFormHtml = ''
                + '<div class="pay_cncl mt-4 mb-4" data-refund-box="1" style="display:none;">'
                + '  <div class="form_wr">'
                + '    <div class="ip_tit">'
                + '      <h5 class="text-white">결제취소/환불 금액(원)</h5>'
                + '    </div>'
                + '    <div class="form-row">'
                + '      <div class="col-6">'
                + '        <input type="text" class="form-control input-refund-amount" placeholder="환불 금액 입력" inputmode="numeric" />'
                + '        <p class="mt-2 mb-0 tg_500 text-white-50 refund-hint">최대 ' + won(paidAmount) + ' 까지 입력</p>'
                + '      </div>'
                + '      <div class="col-3"><button type="button" class="btn btn-primary btn-block px-1 btn-refund-confirm">확인</button></div>'
                + '      <div class="col-3"><button type="button" class="btn btn-outline-light btn-block px-1 btn-refund-cancel">취소</button></div>'
                + '    </div>'
                + '    <p class="mt-3 mb-0 tg_500 refund-msg" style="display:none;"></p>'
                + '  </div>'
                + '</div>';

            const editBtnHtml = (!paid)
                ? ''
                + '<button type="button" class="btn btn-md btn-outline-light bg-light rounded-pill px-4 btn-order-edit mr-2" data-order-idx="' + orderIdx + '">'
                +   '<span class="mr-2"><img src="<?=DESIGN_HTTP?>/market/img/ico_edit.svg" alt=" "></span>주문 변경'
                + '</button>'
                : ''
                + '<button type="button" class="btn btn-md btn-secondary rounded-pill px-4 btn-order-refund" data-order-idx="' + orderIdx + '">결제 취소</button>';

            return ''
                + '<div class="order-block" data-order-idx="' + orderIdx + '" data-order-total="' + paidAmount + '">'
                +   '<div class="py-4 border-bottom-dot mb-4">'
                +     '<span class="mr-4">주문 번호 : ' + otNumber + '</span>'
                +     '<span>주문일시 : ' + fmtDate(otWdate) + '</span>'
                +   '</div>'
                +   '<ul class="bill_list">'
                +     '<li class="d-flex align-items-center justify-content-between">'
                +       '<p class="tit_st3">주문메뉴</p>'
                +       '<div>'
                //+         '<button type="button" class="btn btn-md btn-outline-light bg-light rounded-pill px-4 btn-order-edit mr-2" data-order-idx="' + orderIdx + '">'
                //+           '<span class="mr-2"><img src="<?php //=DESIGN_HTTP?>///market/img/ico_edit.svg" alt=" "></span>주문 변경'
                //+         '</button>'
                +         editBtnHtml
                +       '</div>'
                +     '</li>'
                +      itemsHtml
                +     '<li class="border-bottom"></li>'
                +     '<li>'
                +       '<div class="d-flex align-items-center justify-content-between mb-2"><p>쿠폰 할인</p><p class="fw_700 fs_20">' + escHtml(couponText) + '</p></div>'
                +       '<div class="d-flex align-items-center justify-content-between mb-2"><p>결제 수단</p><p class="fw_700 fs_20">' + escHtml(payText) + '</p></div>'
                +       '<div class="d-flex align-items-center justify-content-between mb-2"><p>총 주문 금액</p><p class="fw_700 fs_20">' + won(totalFromFields) + '</p></div>'
                +       '<div class="d-flex align-items-center justify-content-between mb-2"><p>결제취소/환불 금액</p><p class="fw_700 fs_20">' + (refundedAmount > 0 ? ('-' + won(refundedAmount)) : '0원') + '</p></div>'
                +     '</li>'
                +     '<li class="border-bottom border-dark"></li>'
                +     '<li><div class="d-flex align-items-center justify-content-between mb-3"><p class="fw_600">결제 완료 금액</p><p class="fw_700 fs_24 text-primary">' + won(paidAmount) + '</p></div></li>'
                +   '</ul>'
                +   refundFormHtml
                + '</div>';
        }

        function renderDetailOrders(data) {
            data = data || {};
            const main = data.main_order || null;
            const adds = Array.isArray(data.add_orders) ? data.add_orders : [];
            const sum = data.summary || {};
            const seats = Number(data.tb_seats || 0);

            const menuCnt = Number(sum.menu_count_total || 0);
            const totalPrice = Number(sum.total_price || 0);
            $('#modal_tbl1 .detail_hd p.mt-2').text(
                '메뉴' + menuCnt + '개 ㆍ' + totalPrice.toLocaleString() + '원' + (seats ? 'ㆍ' + seats + '인석' : '')
            );

            const mainHtml = renderOrderBlock(main);
            const $billWrap = $('#detail_bill_wrap');
            if ($billWrap.length) $billWrap.html(mainHtml || '<div class="py-5 text-center tg_500">주문 내역이 없습니다.</div>');

            const $addWrap = $('#detail_add_wrap');
            if ($addWrap.length) {
                if (adds.length > 0) {
                    let addInner = '';
                    adds.forEach(function (o) { addInner += '<div class="px-4">' + renderOrderBlock(o) + '</div>'; });
                    $addWrap.show().html('<div class="bg-primary text-white p-2 text-center">추가주문</div>' + addInner);
                } else {
                    $addWrap.hide().empty();
                }
            }

            // 캐시 구성
            DetailCache.ordersByIdx = {};
            if (main && main.idx) DetailCache.ordersByIdx[Number(main.idx)] = main;
            adds.forEach(o => { if (o && o.idx) DetailCache.ordersByIdx[Number(o.idx)] = o; });

            // 고객정보
            const mtIdx = Number((main && main.mt_idx) || data.mt_idx || 0);
            let customerText = '비회원';
            const customer = (data.customer || (main && main.customer) || null);

            if (mtIdx > 0 && customer) {
                const nm = escHtml(customer.name || customer.mt_name || '');
                const hp = escHtml(customer.hp || customer.mt_hp || '');
                if (nm || hp) customerText = (nm ? nm : '회원') + (hp ? ' (' + hp + ')' : '');
                else customerText = '회원';
            } else if (mtIdx > 0) {
                customerText = '회원';
            }

            const $custBox = $('#modal_tbl1 .mt-4.d-flex.align-items-center.justify-content-between.bg-light.p-5.rounded');
            if ($custBox.length) {
                $custBox.find('p').eq(1).html(customerText);
            } else {
                $('#modal_tbl1 p.fw_600').filter(function () { return $(this).text().trim() === '고객정보'; })
                    .each(function () { $(this).parent().find('p').eq(1).html(customerText); });
            }
        }

        // =========================================================
        // TABLE ADD / QR VIEW MODAL (modal_tbl_add)
        // =========================================================
        const AddState = { token: '', isTempReady: false, tempQrUrl: '' };
        const AddMode = { ADD: 'add', VIEW: 'view' };
        const AddViewState = { mode: AddMode.ADD };
        const AddViewCtx = { table_no: 0, qr_url: '' };

        function $mAdd() { return $('#modal_tbl_add'); }
        function $name() { return $('#add_tb_name'); }
        function $seats() { return $('#add_tb_seats'); }
        function $btnGen() { return $('#btnGenQrTemp'); }
        function $btnFinal() { return $('#btnAddTableFinal'); }
        function $btnRegen() { return $('#btnQrRegen'); }
        function $btnDownload() { return $('#btnQrDownload'); }

        function ensureQrPreviewWrap() {
            let $wrap = $('#add_qr_preview');
            if ($wrap.length) return $wrap;

            const $modal = $mAdd();
            const $p = $modal.find('.col-8.mb-4').has('#btnGenQrTemp').find('p.mt-4').first();
            $wrap = $('<p class="mt-4" id="add_qr_preview"></p>');
            if ($p.length) $p.replaceWith($wrap);
            else $('#btnGenQrTemp').after($wrap);
            return $wrap;
        }

        function setQrPreview(url) {
            const $wrap = ensureQrPreviewWrap();
            const bust = url.indexOf('?') >= 0 ? '&v=' + Date.now() : '?v=' + Date.now();
            $wrap.html('<img src="' + url + bust + '" alt="qr 미리보기" style="max-width:260px;width:100%;height:auto;" />');
        }

        function resetQrPreview() { ensureQrPreviewWrap().html(''); }

        function $msg() {
            let $el = $('#add_tbl_msg');
            if ($el.length) return $el;
            $el = $('<p id="add_tbl_msg" class="mt-3 mb-0 tg_500"></p>');
            $btnGen().after($el);
            return $el;
        }

        function setMsg(type, text) {
            const $el = $msg();
            $el.removeClass('text-danger text-success text-muted');
            if (type === 'error') $el.addClass('text-danger');
            else if (type === 'success') $el.addClass('text-success');
            else $el.addClass('text-muted');
            $el.text(text || '');
        }

        function resetAddState() {
            AddState.token = '';
            AddState.isTempReady = false;
            AddState.tempQrUrl = '';
            $btnFinal().prop('disabled', true);
        }

        function validateInputs() {
            const tbName = String($name().val() || '').trim();
            const seatsRaw = String($seats().val() || '').trim();
            const tbSeats = parseInt(seatsRaw, 10);

            if (!/^[A-Za-z0-9]{1,5}$/.test(tbName)) {
                setMsg('error', '테이블명은 5자 미만의 영문/숫자만 가능합니다.');
                $name().focus();
                return null;
            }
            if (!tbSeats || tbSeats <= 0) {
                setMsg('error', '좌석 수를 올바르게 입력해 주세요.');
                $seats().focus();
                return null;
            }
            return { tbName, tbSeats };
        }

        function setAddModalMode(mode) {
            AddViewState.mode = mode;

            const $modal = $mAdd();
            const $title = $modal.find('.detail_hd .tit_st1 span');

            if (mode === AddMode.VIEW) {
                $title.text('QR 코드 확인');
                $name().prop('readonly', true);
                $seats().prop('readonly', true);
                $btnFinal().hide();
                $btnGen().hide();
                if ($btnRegen().length) $btnRegen().show();
                if ($btnDownload().length) $btnDownload().show();
            } else {
                $title.text('테이블 추가');
                $name().prop('readonly', false);
                $seats().prop('readonly', false);
                $btnFinal().show();
                $btnGen().show();
                if ($btnRegen().length) $btnRegen().hide();
                if ($btnDownload().length) $btnDownload().hide();
                AddViewCtx.table_no = 0;
                AddViewCtx.qr_url = '';
            }
        }

        function resetAddModalUi() {
            $name().val('');
            $seats().val('');
            setMsg('muted', '');
            resetQrPreview();
            resetAddState();
            $btnGen().prop('disabled', false);

            if ($btnRegen().length) $btnRegen().hide();
            if ($btnDownload().length) $btnDownload().hide();
        }

        function cancelTempIfNeeded() {
            if (!AddState.token) return;
            apiPost({ act: 'cancel_qr_temp', token: AddState.token });
            resetAddState();
        }

        function generateTempQr() {
            const v = validateInputs();
            if (!v) return;

            resetAddState();
            setMsg('muted', 'QR 생성 중...');
            $btnGen().prop('disabled', true);
            $btnFinal().prop('disabled', true);

            apiPost(
                { act: 'generate_qr_temp', tb_name: v.tbName, tb_seats: v.tbSeats },
                function (res) {
                    if (!res || !res.success) {
                        setMsg('error', (res && res.message) ? res.message : 'QR 생성에 실패했습니다.');
                        $btnGen().prop('disabled', false);
                        return;
                    }

                    const token = res.data && res.data.token ? String(res.data.token) : '';
                    const qrUrl = res.data && res.data.qr_url ? String(res.data.qr_url) : '';

                    if (!token || !qrUrl) {
                        setMsg('error', 'QR 생성 응답이 올바르지 않습니다.');
                        $btnGen().prop('disabled', false);
                        return;
                    }

                    AddState.token = token;
                    AddState.tempQrUrl = qrUrl;
                    AddState.isTempReady = true;

                    setQrPreview(qrUrl);

                    $btnFinal().prop('disabled', false);
                    setMsg('success', 'QR 생성 완료! "추가하기"를 눌러 최종 등록해주세요.');
                    $btnGen().prop('disabled', false);
                },
                function () {
                    setMsg('error', '서버 오류로 QR 생성에 실패했습니다.');
                    $btnGen().prop('disabled', false);
                }
            );
        }

        function finalAdd() {
            if (!AddState.isTempReady || !AddState.token) {
                setMsg('error', '먼저 "코드 생성하기"로 QR 생성을 완료해 주세요.');
                return;
            }

            setMsg('muted', '등록 중...');
            $btnFinal().prop('disabled', true);

            apiPost(
                { act: 'add_table_with_qr', qr_token: AddState.token },
                function (res) {
                    if (!res || !res.success) {
                        setMsg('error', (res && res.message) ? res.message : '등록에 실패했습니다.');
                        $btnFinal().prop('disabled', false);
                        return;
                    }

                    $mAdd().one('hidden.bs.modal', function () {
                        uiAlert('완료', '테이블이 추가되었습니다.');
                        fetchList();
                    }).modal('hide');
                },
                function () {
                    setMsg('error', '서버 오류로 등록에 실패했습니다.');
                    $btnFinal().prop('disabled', false);
                }
            );
        }

        function openAddModalWithExistingTable(tableNo) {
            apiPost(
                { act: 'table_info', table_no: tableNo },
                function (res) {
                    if (!res || !res.success) {
                        uiAlert('안내', (res && res.message) ? res.message : '테이블 정보를 불러오지 못했습니다.');
                        return;
                    }

                    const d = res.data || {};
                    const tbName = d.tb_name || '';
                    const tbSeats = d.tb_seats || '';
                    const qrUrl = d.qr_url || '';

                    AddViewCtx.table_no = tableNo;
                    AddViewCtx.qr_url = qrUrl;

                    setAddModalMode(AddMode.VIEW);
                    resetAddState();
                    setMsg('muted', '');

                    $name().val(tbName);
                    $seats().val(tbSeats);

                    resetQrPreview();
                    if (qrUrl) setQrPreview(qrUrl);
                    else setQrPreview('<?=DESIGN_HTTP?>/market/img/qrimg.jpg');

                    $mAdd().modal('show');
                },
                function (xhr) {
                    console.log('[table_info] error', xhr.status, xhr.responseText);
                    uiAlert('서버 오류', '테이블 정보를 불러오지 못했습니다.');
                }
            );
        }

        function downloadQr(url) {
            if (!url) { uiAlert('안내', '다운로드할 QR 코드가 없습니다.'); return; }
            const bust = url.indexOf('?') >= 0 ? '&v=' + Date.now() : '?v=' + Date.now();
            const a = document.createElement('a');
            a.href = url + bust;
            a.download = 'table_qr.png';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }

        function regenerateQr(tableNo) {
            if (!tableNo) return;

            uiConfirm('QR 새로 생성', 'QR 코드를 새로 생성하시겠습니까?', function () {
                apiPost(
                    { act: 'regenerate_qr', table_no: tableNo },
                    function (res) {
                        if (!res || !res.success) {
                            uiAlert('실패', (res && res.message) ? res.message : 'QR 새로 생성에 실패했습니다.');
                            return;
                        }
                        const qrUrl = res.data && res.data.qr_url ? String(res.data.qr_url) : '';
                        if (!qrUrl) { uiAlert('실패', '새 QR 주소를 받지 못했습니다.'); return; }

                        AddViewCtx.qr_url = qrUrl;
                        setQrPreview(qrUrl);
                        uiAlert('완료', 'QR 코드가 새로 생성되었습니다.');
                        fetchList();
                    },
                    function () { uiAlert('서버 오류', 'QR 새로 생성 중 오류가 발생했습니다.'); }
                );
            });
        }

        // =========================================================
        // QR / MOVE PANEL in DETAIL MODAL
        // =========================================================
        function openQrPanel() {
            $('#modal_tbl1 .btn_qr_wr').stop().slideDown(200).addClass('active');
            $('#modal_tbl1 .btn_tbl_change_wr').stop().slideUp(200).removeClass('active');
            $('#modal_tbl1 .btn_qr').addClass('active');
            $('#modal_tbl1 .btn_tbl_change').removeClass('active');
            loadQrForDetail(currentDetail.table_no);
        }

        function openMovePanel() {
            $('#modal_tbl1 .btn_tbl_change_wr').stop().slideDown(200).addClass('active');
            $('#modal_tbl1 .btn_qr_wr').stop().slideUp(200).removeClass('active');
            $('#modal_tbl1 .btn_tbl_change').addClass('active');
            $('#modal_tbl1 .btn_qr').removeClass('active');
            loadMoveTableList(currentDetail.tv_idx, currentDetail.table_no);
        }

        function loadQrForDetail(tableNo) {
            if (!tableNo) return;

            apiPost(
                { act: 'table_info', table_no: tableNo },
                function (res) {
                    if (!res || !res.success) {
                        uiAlert('안내', (res && res.message) ? res.message : 'QR 정보를 불러오지 못했습니다.');
                        return;
                    }
                    const d = res.data || {};
                    const qrUrl = d.qr_url || '';
                    if (!qrUrl) { uiAlert('안내', 'QR 코드가 없습니다.'); return; }

                    const bust = (qrUrl.indexOf('?') >= 0 ? '&v=' : '?v=') + Date.now();
                    $('#detail_qr_img').attr('src', qrUrl + bust);

                    $('#modal_tbl1 .btn_qr_wr').data('table-no', tableNo);
                    $('#modal_tbl1 .btn_qr_wr').data('qr-url', qrUrl);
                },
                function () { uiAlert('서버 오류', 'QR 정보를 불러오지 못했습니다.'); }
            );
        }

        function downloadQrFromDetail() {
            const $wrap = $('#modal_tbl1 .btn_qr_wr');
            const url = $wrap.data('qr-url') || '';
            if (!url) { uiAlert('안내', '다운로드할 QR 코드가 없습니다.'); return; }
            downloadQr(url);
        }

        function regenerateQrFromDetail() {
            const $wrap = $('#modal_tbl1 .btn_qr_wr');
            const tableNo = parseInt($wrap.data('table-no') || '0', 10);
            if (!tableNo) return;

            uiConfirm('QR 새로 생성', 'QR 코드를 새로 생성하시겠습니까?', function () {
                apiPost(
                    { act: 'regenerate_qr', table_no: tableNo },
                    function (res) {
                        if (!res || !res.success) {
                            uiAlert('실패', (res && res.message) ? res.message : 'QR 새로 생성 실패');
                            return;
                        }
                        const qrUrl = (res.data && res.data.qr_url) ? String(res.data.qr_url) : '';
                        if (!qrUrl) { uiAlert('실패', '새 QR URL을 받지 못했습니다.'); return; }

                        $wrap.data('qr-url', qrUrl);
                        const bust = (qrUrl.indexOf('?') >= 0 ? '&v=' : '?v=') + Date.now();
                        $('#detail_qr_img').attr('src', qrUrl + bust);

                        uiAlert('완료', 'QR 코드가 새로 생성되었습니다.');
                        fetchList();
                    },
                    function () { uiAlert('서버 오류', 'QR 새로 생성 중 오류가 발생했습니다.'); }
                );
            });
        }

        function loadMoveTableList(tvIdx, currentTableNo) {
            if (!tvIdx) return;

            const $box = $('#modal_tbl1 .tbl_cbox');
            $box.html('<div class="tg_500 text-center w-100 py-4">불러오는 중...</div>');

            apiPost(
                { act: 'move_table_list', tv_idx: tvIdx },
                function (res) {
                    if (!res || !res.success) {
                        $box.html('<div class="tg_500 text-center w-100 py-4">테이블 정보를 불러오지 못했습니다.</div>');
                        return;
                    }

                    const list = (res.data && res.data.tables) ? res.data.tables : [];
                    if (!list.length) {
                        $box.html('<div class="tg_500 text-center w-100 py-4">이동 가능한 테이블이 없습니다.</div>');
                        return;
                    }

                    let html = '';
                    for (let i = 0; i < list.length; i++) {
                        const t = list[i];
                        const tno = parseInt(t.table_no || 0, 10);
                        const seats = t.table_seats ? String(t.table_seats) : '';
                        const isCurrent = (tno === parseInt(currentTableNo || 0, 10));
                        const disabled = (t.is_occupied === true && !isCurrent) ? 'disabled' : '';

                        const title = String(t.table_name || tno);
                        const sub = seats ? (seats + '인석') : '';
                        const badge = (t.is_occupied === true && !isCurrent) ? ' (사용중)' : '';

                        html += ''
                            + '<label class="btn btn-outline-primary ' + (isCurrent ? 'active' : '') + ' ' + (disabled ? 'disabled' : '') + '">'
                            + '  <input type="radio" name="move_table" value="' + tno + '" ' + (isCurrent ? 'checked' : '') + ' ' + disabled + '>'
                            + '  <b>' + title + '</b>'
                            + '  <span>' + sub + badge + '</span>'
                            + '</label>';
                    }

                    $box.html(html);
                    $('#btnMoveSubmit').data('tv-idx', tvIdx);
                },
                function () { $box.html('<div class="tg_500 text-center w-100 py-4">서버 오류</div>'); }
            );
        }

        function submitMoveTable() {
            const tvIdx = parseInt($('#btnMoveSubmit').data('tv-idx') || '0', 10);
            if (!tvIdx) return;

            const targetNo = parseInt($('#modal_tbl1 input[name="move_table"]:checked').val() || '0', 10);
            if (!targetNo) { uiAlert('안내', '이동할 테이블을 선택해 주세요.'); return; }
            if (targetNo === parseInt(currentDetail.table_no || 0, 10)) { uiAlert('안내', '현재 테이블입니다.'); return; }

            uiConfirm('자리 이동', targetNo + '번 테이블로 이동하시겠습니까?', function () {
                apiPost(
                    { act: 'move_table', tv_idx: tvIdx, target_table_no: targetNo },
                    function (res) {
                        if (!res || !res.success) {
                            uiAlert('실패', (res && res.message) ? res.message : '자리 이동 실패');
                            return;
                        }

                        currentDetail.table_no = targetNo;

                        uiAlert('완료', '자리 이동이 완료되었습니다.', function () {
                            fetchList();
                            fetchDetail(tvIdx, function (data) {
                                renderDetailModal(data);
                                renderDetailOrders(data);
                            });
                        });
                    },
                    function () { uiAlert('서버 오류', '자리 이동 중 오류가 발생했습니다.'); }
                );
            });
        }

        // =========================================================
        // REFUND
        // =========================================================
        function refundOrder(orderIdx, amount, cb) {
            apiPost(
                { act: 'refund', order_idx: orderIdx, refund_amount: amount },
                function (res) {
                    if (!res || !res.success) {
                        uiAlert('환불 실패', (res && res.message) ? res.message : '환불 처리에 실패했습니다.');
                        return;
                    }
                    cb && cb(res);
                },
                function (xhr) {
                    console.log('[refund] error', xhr.status, xhr.responseText);
                    uiAlert('서버 오류', '환불 처리 중 오류가 발생했습니다.');
                }
            );
        }

        // =========================================================
        // CATALOG (menus + option groups + options)
        // =========================================================
        const Catalog = {
            loaded: false,
            categories: [],
            menus: [],
            groups: [],
            options: [],
            catById: {},
            menusById: {},
            menusByCat: {},
            groupsByMenu: {},
            optionsByGroup: {},
        };

        function buildCatalogIndexes() {
            Catalog.catById = {};
            Catalog.menusById = {};
            Catalog.menusByCat = {};
            Catalog.groupsByMenu = {};
            Catalog.optionsByGroup = {};

            (Catalog.categories || []).forEach(c => {
                const sc = toNum(c.sc_idx, 0);
                if (sc) Catalog.catById[sc] = c;
            });

            (Catalog.menus || []).forEach(m => {
                const sm = toNum(m.sm_id, 0);
                const sc = toNum(m.sc_idx, 0);
                if (sm) Catalog.menusById[sm] = m;
                if (sc) {
                    if (!Catalog.menusByCat[sc]) Catalog.menusByCat[sc] = [];
                    Catalog.menusByCat[sc].push(m);
                }
            });

            (Catalog.groups || []).forEach(g => {
                const oc = toNum(g.oc_idx, 0);
                const sm = toNum(g.sm_id, 0);
                if (!oc || !sm) return;
                if (!Catalog.groupsByMenu[sm]) Catalog.groupsByMenu[sm] = [];
                Catalog.groupsByMenu[sm].push(g);
            });

            (Catalog.options || []).forEach(o => {
                const oc = toNum(o.oc_idx, 0);
                if (!oc) return;
                if (!Catalog.optionsByGroup[oc]) Catalog.optionsByGroup[oc] = [];
                Catalog.optionsByGroup[oc].push(o);
            });

            Object.keys(Catalog.menusByCat).forEach(sc => {
                Catalog.menusByCat[sc].sort((a, b) => toNum(a.sm_order, 0) - toNum(b.sm_order, 0));
            });
            Object.keys(Catalog.groupsByMenu).forEach(sm => {
                Catalog.groupsByMenu[sm].sort((a, b) => toNum(a.oc_order, 0) - toNum(b.oc_order, 0));
            });
            Object.keys(Catalog.optionsByGroup).forEach(oc => {
                Catalog.optionsByGroup[oc].sort((a, b) => toNum(a.om_order, 0) - toNum(b.om_order, 0));
            });
        }

        function fetchCatalog(cb) {
            if (Catalog.loaded) { cb && cb(); return; }
            apiPost(
                { act: 'catalog' },
                function (res) {
                    if (!res || !res.success) {
                        uiAlert('오류', (res && res.message) ? res.message : '메뉴/옵션 정보를 불러오지 못했습니다.');
                        return;
                    }
                    const d = res.data || {};
                    Catalog.categories = Array.isArray(d.categories) ? d.categories : [];
                    Catalog.menus = Array.isArray(d.menus) ? d.menus : [];
                    Catalog.groups = Array.isArray(d.option_groups) ? d.option_groups : [];
                    Catalog.options = Array.isArray(d.options) ? d.options : [];

                    buildCatalogIndexes();
                    Catalog.loaded = true;
                    cb && cb();
                },
                function () { uiAlert('서버 오류', '메뉴/옵션 정보를 불러오지 못했습니다.'); }
            );
        }

        // =========================================================
        // ORDER EDIT (modal_tbl2) + OPTION EDIT (modal_tbl3)
        // =========================================================
        function calcOptionsTotal(options) {
            if (!Array.isArray(options)) return 0;
            let sum = 0;
            options.forEach(op => {
                const p = toNum(pickFirst(op.option_price, op.price, op.add_price, 0), 0);
                const q = Math.max(1, toNum(op.quantity, 1));
                sum += (p * q);
            });
            return sum;
        }

        function calcLinePrice(it) {
            const qty = Math.max(1, toNum(it.quantity, 1));
            const unit = toNum(it.unit_price, 0);
            return (unit * qty) + calcOptionsTotal(it.options || []);
        }

        function recomputeSummaryDraft() {
            const items = (EditCtx.draft && Array.isArray(EditCtx.draft.items)) ? EditCtx.draft.items : [];
            let sub = 0;
            items.forEach(it => { sub += calcLinePrice(it); });

            const discount = toNum(EditCtx.draft?.summary?.discount, 0);
            const total = Math.max(0, sub - discount);

            EditCtx.draft.summary = { sub_total: sub, discount: discount, total: total };
        }

        function renderOptionListHtml(options) {
            if (!Array.isArray(options) || !options.length) return '';
            const li = options.map(op => {
                const nm = escHtml(pickFirst(op.option_name, op.om_title, op.title, op.name, ''));
                if (!nm) return '';
                const pr = toNum(pickFirst(op.option_price, op.price, op.add_price, 0), 0);
                return `<li>${nm}${pr ? ` (+${won(pr)})` : ''}</li>`;
            }).filter(Boolean).join('');
            if (!li) return '';
            return `<ul class="dot_list tg_500 mt-4">${li}</ul>`;
        }

        function ensureEditCatalogWrap() {
            const $m2 = $('#modal_tbl2');
            let $wrap = $m2.find('#edit_catalog_wrap');
            if ($wrap.length) return $wrap;

            const html = ''
                + '<div id="edit_catalog_wrap" class="mt-4">'
                + '  <div class="d-flex align-items-center justify-content-between">'
                + '    <p class="fw_600">메뉴 추가</p>'
                + '    <button type="button" class="btn btn-outline-light btn-sm" id="btnToggleCatalog">열기</button>'
                + '  </div>'
                + '  <div id="catalog_panel" style="display:none;">'
                + '    <div class="form-row mt-3">'
                + '      <div class="col-6"><select class="form-control" id="catalog_cat"></select></div>'
                + '      <div class="col-6"><input type="text" class="form-control" id="catalog_search" placeholder="메뉴 검색" /></div>'
                + '    </div>'
                + '    <div class="mt-3" id="catalog_menu_list"></div>'
                + '  </div>'
                + '</div>';

            const $ul = $m2.find('ul.bill_list.wide_gap');
            if ($ul.length) $ul.after(html);
            else $m2.find('.modal-body').append(html);

            return $m2.find('#edit_catalog_wrap');
        }

        function renderCatalogPanel() {
            ensureEditCatalogWrap();

            const $cat = $('#catalog_cat');
            const $list = $('#catalog_menu_list');

            let opt = '<option value="0">전체</option>';
            (Catalog.categories || []).forEach(c => {
                opt += `<option value="${escHtml(c.sc_idx)}">${escHtml(c.sc_title || '')}</option>`;
            });
            $cat.html(opt);

            function draw() {
                const scIdx = toNum($cat.val(), 0);
                const q = String($('#catalog_search').val() || '').trim().toLowerCase();

                let menus = [];
                if (scIdx && Catalog.menusByCat[scIdx]) menus = Catalog.menusByCat[scIdx].slice();
                else menus = (Catalog.menus || []).slice();

                if (q) {
                    menus = menus.filter(m => String(m.sm_title || '').toLowerCase().indexOf(q) >= 0);
                }

                if (!menus.length) {
                    $list.html('<div class="tg_500 text-center py-4">메뉴가 없습니다.</div>');
                    return;
                }

                let html = '<div class="list-group">';
                menus.forEach(m => {
                    const smId = toNum(m.sm_id, 0);
                    const title = escHtml(m.sm_title || '');
                    const price = won(toNum(m.sm_price, 0));
                    html += ''
                        + `<a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center btn-add-menu" data-sm-id="${smId}">`
                        + `  <span>${title}</span><span class="fw_700">${price}</span>`
                        + `</a>`;
                });
                html += '</div>';
                $list.html(html);
            }

            draw();
            $(document).off('change.catalog').on('change.catalog', '#catalog_cat', draw);
            $(document).off('input.catalog').on('input.catalog', '#catalog_search', draw);
        }

        function renderEditModal() {
            const $m2 = $('#modal_tbl2');
            const $ul = $m2.find('ul.bill_list.wide_gap');
            if (!$ul.length) return;

            const snap = EditCtx.draft || { items: [], summary: { sub_total: 0, discount: 0, total: 0 } };
            const items = Array.isArray(snap.items) ? snap.items : [];

            if (!items.length) {
                $ul.html('<li class="tg_500 text-center w-100 py-5">변경할 메뉴가 없습니다.</li>');
                recomputeSummaryDraft();
                return;
            }

            let html = '';
            items.forEach((it, idx) => {
                const smId = toNum(it.sm_id || it.sm_idx, 0);
                const master = Catalog.menusById[smId];

                if (!it.menu_name && master) it.menu_name = master.sm_title;
                if (!it.unit_price && master) it.unit_price = toNum(master.sm_price, 0);

                const name = escHtml(it.menu_name || it.name || '');
                const qty = Math.max(1, toNum(it.quantity, 1));
                const line = calcLinePrice(it);
                const optHtml = renderOptionListHtml(it.options || []);

                html += ''
                    + `<li class="edit-item" data-item-idx="${idx}">`
                    + `  <div class="bill_box">`
                    + `    <div class="flex-fill"><div>`
                    + `      <div class="d-flex justify-content-between">`
                    + `        <p class="fw_600 fs_20">${name}</p>`
                    + `        <p class="flex-shrink-0 ml-4"><span class="edit-qty-text">${qty}</span>개</p>`
                    + `      </div>`
                    +        optHtml
                    + `    </div></div>`
                    + `    <div class="bill_money"><span class="edit-line-price">${won(line)}</span></div>`
                    + `  </div>`
                    + `  <div class="d-flex justify-content-between align-items-center mt-4">`
                    + `    <div class="d-flex">`
                    + `      <div class="item_opt_counter mr-2">`
                    + `        <button type="button" class="btn item_opt_counter_btn pl-1 btn-edit-minus" ${qty <= 1 ? 'disabled' : ''}><img src="<?=DESIGN_HTTP?>/market/img/ico_decrease.svg" alt="감소"></button>`
                    + `        <input type="text" class="quantity edit-qty" value="${qty}" readonly>`
                    + `        <button type="button" class="btn item_opt_counter_btn pr-1 btn-edit-plus"><img src="<?=DESIGN_HTTP?>/market/img/ico_increase.svg" alt="증가"></button>`
                    + `      </div>`
                    + `      <button type="button" class="btn btn-outline-light btn-edit-options" data-item-idx="${idx}">옵션 변경</button>`
                    + `    </div>`
                    + `    <div><button type="button" class="btn btn-outline-secondary btn-edit-remove">메뉴 삭제</button></div>`
                    + `  </div>`
                    + `</li>`
                    + `<li class="border-bottom"></li>`;
            });

            html = html.replace(/<li class="border-bottom"><\/li>\s*$/, '');
            $ul.html(html);

            recomputeSummaryDraft();

            const $total = $m2.find('#edit_total_text');
            if ($total.length) $total.text(won(toNum(EditCtx.draft?.summary?.total, 0)));

            fetchCatalog(function () {
                renderCatalogPanel();
            });
        }

        function buildSelectedSet(item) {
            const set = new Set();
            (item.options || []).forEach(op => {
                const om = toNum(op.om_idx, 0);
                if (om) set.add(String(om));
            });
            return set;
        }

        function applyOptionsFromModal3() {
            const $m3 = $('#modal_tbl3');

            const mode = String($m3.data('editing-mode') || 'DRAFT');
            let item = null;

            if (mode === 'PENDING_ADD') {
                item = EditCtx.pendingItem;           // ✅ 임시 메뉴 대상
            } else {
                const itemIdx = toNum($m3.data('editing-item-idx'), -1);
                item = EditCtx.draft?.items?.[itemIdx]; // ✅ 기존 draft 수정 대상
            }

            if (!item) return false;

            const qty = Math.max(1, toNum($m3.find('.m3-qty').val(), toNum(item.quantity, 1)));
            item.quantity = qty;

            // ✅ 필수 옵션 체크(너 기존 로직 그대로 유지)
            let ok = true;
            $m3.find('li[data-required="Y"]').each(function () {
                const $li = $(this);
                if ($li.find('input.chk-opt:checked').length <= 0) {
                    ok = false;
                    return false;
                }
            });
            if (!ok) {
                uiAlert('안내', '필수 옵션을 선택해 주세요.');
                return false;
            }

            // ✅ 체크된 옵션 수집(너 기존 로직 그대로)
            const newOpts = [];
            $m3.find('input.chk-opt:checked').each(function () {
                const $in = $(this);
                const omIdx = toNum($in.data('om-idx'), 0);
                const ocIdx = toNum($in.data('oc-idx'), 0);
                const price = toNum($in.data('price'), 0);

                const optObj = (Catalog.options || []).find(x => toNum(x.om_idx, 0) === omIdx) || {};
                const name = pickFirst(optObj.om_title, optObj.option_name, '');

                newOpts.push({
                    om_idx: omIdx,
                    oc_idx: ocIdx,
                    option_name: name,
                    option_price: price,
                    quantity: 1
                });
            });

            item.options = newOpts;

            // ✅ 여기부터가 핵심: pending이면 "변경 완료" 눌렀을 때만 draft에 넣기
            if (mode === 'PENDING_ADD') {
                if (!EditCtx.draft.items) EditCtx.draft.items = [];
                EditCtx.draft.items.push(item);          // ✅ 이 시점에만 추가
                EditCtx.pendingCommitted = true;
                EditCtx.pendingItem = null;
                $m3.data('editing-mode', 'DRAFT');
            }

            renderEditModal();
            return true;
        }

        $('#modal_tbl3').on('hidden.bs.modal', function () {
            const mode = String($('#modal_tbl3').data('editing-mode') || 'DRAFT');

            // ✅ 임시추가 중인데 확정 안 됐다면 폐기
            if (mode === 'PENDING_ADD' && !EditCtx.pendingCommitted) {
                EditCtx.pendingItem = null;
            }

            // 다음 사용을 위해 초기화
            EditCtx.pendingCommitted = false;
            $('#modal_tbl3').data('editing-mode', 'DRAFT');
        });

        function submitOrderUpdate(cb) {
            if (!EditCtx.tv_idx || !EditCtx.order_idx || !EditCtx.draft) {
                uiAlert('오류', '편집 정보가 없습니다.');
                return;
            }

            recomputeSummaryDraft();

            apiPost(
                {
                    act: 'order_update',
                    tv_idx: EditCtx.tv_idx,
                    order_idx: EditCtx.order_idx,
                    snapshot_json: JSON.stringify(EditCtx.draft),
                },
                function (res) {
                    if (!res || !res.success) {
                        uiAlert('실패', (res && res.message) ? res.message : '주문 변경에 실패했습니다.');
                        return;
                    }
                    cb && cb(res);
                },
                function (xhr) {
                    console.log('[order_update] error', xhr.status, xhr.responseText);
                    uiAlert('서버 오류', '주문 변경에 실패했습니다.');
                }
            );
        }

        // =========================================================
        // EVENTS (ONE PLACE)
        // =========================================================
        // 테이블 삭제
        $(document).on('click', '.btn-table-delete', function (e) {
            e.preventDefault(); e.stopPropagation();
            const tableNo = parseInt($(this).data('table-no') || '0', 10);
            if (!tableNo) return;

            uiConfirm('테이블 삭제', tableNo + '번 테이블을 삭제하시겠습니까?\n삭제 후 복구할 수 없습니다.', function () {
                deleteTable(tableNo, function () {
                    uiAlert('삭제 완료', '테이블이 삭제되었습니다.');
                });
            });
        });

        // 테이블 QR 보기(빈자리 카드)
        $(document).on('click', '.btn-table-qr', function (e) {
            e.preventDefault(); e.stopPropagation();
            const tableNo = parseInt($(this).data('table-no') || '0', 10);
            if (!tableNo) return;
            openAddModalWithExistingTable(tableNo);
        });

        // 카드 액션 버튼
        $(document).on('click', '.btn-table-action', function (e) {
            e.preventDefault(); e.stopPropagation();
            const action = $(this).data('action');
            const tvIdx = parseInt($(this).data('tv-idx') || '0', 10);
            if (!tvIdx || !action) return;

            if (action === 'clear') {
                uiConfirm('좌석 비우기', '정말로 좌석을 비우시겠습니까?', function () { doAction(action, tvIdx); });
                return;
            }
            doAction(action, tvIdx);
        });

        // 상세 모달 오픈
        $(document).on('click', '.btn-open-detail', function (e) {
            e.preventDefault();

            const tvIdx = parseInt($(this).data('tv-idx') || '0', 10);
            const tableNo = parseInt($(this).data('table-no') || '0', 10);
            const status = $(this).data('status') || '';

            currentDetail.tv_idx = tvIdx;
            currentDetail.table_no = tableNo;
            currentDetail.status = status;

            const st = statusToUi(status);
            $('#modal_tbl1 .status').attr('class', 'status ' + st.cls).text(st.label);
            $('#modal_tbl1 .detail_hd h3.tit_st1').text('테이블번호 ' + tableNo);
            $('#modal_tbl1 .detail_hd p.mt-2').text('불러오는 중...');

            $('#modal_tbl1').modal('show');

            fetchDetail(tvIdx, function (data) {
                renderDetailModal(data);
                renderDetailOrders(data);
            });
        });

        // 상세 상단 액션 버튼
        $(document).on('click', '#modal_tbl1 .btn-detail-action', function (e) {
            e.preventDefault();
            const action = $(this).data('action');
            const tvIdx = currentDetail.tv_idx;
            if (!tvIdx || !action) return;

            if (action === 'clear') {
                uiConfirm('좌석 비우기', '정말로 좌석을 비우시겠습니까?', function () {
                    doAction(action, tvIdx, function () { $('#modal_tbl1').modal('hide'); });
                });
                return;
            }

            doAction(action, tvIdx, function () {
                fetchDetail(tvIdx, function (data) {
                    renderDetailModal(data);
                    renderDetailOrders(data);
                });
            });
        });

        // 상세 모달 QR/자리이동 탭
        $(document).on('click', '#modal_tbl1 .btn_qr', function (e) { e.preventDefault(); openQrPanel(); });
        $(document).on('click', '#modal_tbl1 .btn_tbl_change', function (e) { e.preventDefault(); openMovePanel(); });

        // 상세 QR 영역 버튼(텍스트로 분기)
        $(document).on('click', '#modal_tbl1 .btn_qr_wr .btn-outline-light', function (e) {
            e.preventDefault();
            const txt = $.trim($(this).text());
            if (txt.indexOf('새로') >= 0) regenerateQrFromDetail();
            else if (txt.indexOf('다운') >= 0) downloadQrFromDetail();
        });

        // 자리이동 확정 버튼
        $(document).on('click', '#btnMoveSubmit', function (e) {
            e.preventDefault();
            submitMoveTable();
        });

        // modal_tbl_add (추가/QR보기) 버튼들
        $(document).on('click', '#btnGenQrTemp', function (e) { e.preventDefault(); generateTempQr(); });
        $(document).on('click', '#btnAddTableFinal', function (e) { e.preventDefault(); e.stopPropagation(); finalAdd(); return false; });

        $(document).on('click', '#btnQrDownload', function (e) {
            e.preventDefault(); e.stopPropagation();
            if (AddViewState.mode !== AddMode.VIEW) return;
            downloadQr(AddViewCtx.qr_url);
        });

        $(document).on('click', '#btnQrRegen', function (e) {
            e.preventDefault(); e.stopPropagation();
            if (AddViewState.mode !== AddMode.VIEW) return;
            regenerateQr(AddViewCtx.table_no);
        });

        // modal_tbl_add 입력 변경 시 임시토큰 무효화
        $(document).on('input', '#add_tb_name, #add_tb_seats', function () {
            if (AddViewState.mode !== AddMode.ADD) return;
            if (AddState.isTempReady) {
                cancelTempIfNeeded();
                setMsg('muted', '입력이 변경되었습니다. 다시 "코드 생성하기"를 눌러주세요.');
                resetQrPreview();
                $btnFinal().prop('disabled', true);
            }
        });

        // 좌석 입력 숫자만
        $(document).on('input', '#add_tb_seats', function () {
            const after = String(this.value || '').replace(/[^0-9]/g, '');
            if (this.value !== after) this.value = after;
        });

        // modal_tbl_add 모달 shown/hidden
        $(function () {
            ensureQrPreviewWrap();
            $mAdd().attr('data-backdrop', 'static').attr('data-keyboard', 'false');

            $mAdd().on('shown.bs.modal', function () {
                if (AddViewState.mode === AddMode.ADD) resetAddModalUi();
            });

            $mAdd().on('hidden.bs.modal', function () {
                if (AddViewState.mode === AddMode.VIEW) {
                    setAddModalMode(AddMode.ADD);
                    resetAddModalUi();
                    return;
                }
                cancelTempIfNeeded();
                resetAddModalUi();
                setAddModalMode(AddMode.ADD);
            });
        });

        // 주문 변경(상세 -> 편집)
        $(document).on('click', '.btn-order-edit', function (e) {
            e.preventDefault(); e.stopPropagation();

            const orderIdx = toNum($(this).data('order-idx'), 0);
            if (!orderIdx) return;

            const order = DetailCache.ordersByIdx[orderIdx];
            if (!order) { uiAlert('오류', '주문 데이터를 찾지 못했습니다.'); return; }

            EditCtx.tv_idx = currentDetail.tv_idx || 0;
            EditCtx.order_idx = orderIdx;
            EditCtx.draft = deepClone(order.snapshot_obj || { items: [], summary: { sub_total: 0, discount: 0, total: 0 } });

            // 아이템 정규화
            (EditCtx.draft.items || []).forEach(it => {
                const sm = toNum(it.sm_id || it.sm_idx, 0);
                if (!it.sm_id && sm) it.sm_id = sm;
                if (!it.menu_name) it.menu_name = it.name || '';
                if (it.unit_price == null) it.unit_price = toNum(it.unit_price, 0);
                if (!Array.isArray(it.options)) it.options = [];
                if (!it.quantity) it.quantity = 1;
            });

            fetchCatalog(function () {
                // 마스터 기반 보강
                (EditCtx.draft.items || []).forEach(it => {
                    const m = Catalog.menusById[toNum(it.sm_id, 0)];
                    if (m) {
                        if (!it.menu_name) it.menu_name = m.sm_title;
                        if (!it.unit_price) it.unit_price = toNum(m.sm_price, 0);
                    }
                });

                renderEditModal();
                $('#modal_tbl1').modal('hide');
                $('#modal_tbl2').modal('show');
            });
        });

        // modal_tbl2 수량 +/-
        $(document).on('click', '#modal_tbl2 .btn-edit-plus', function (e) {
            e.preventDefault();
            const idx = toNum($(this).closest('.edit-item').data('item-idx'), -1);
            const it = EditCtx.draft?.items?.[idx];
            if (!it) return;
            it.quantity = Math.max(1, toNum(it.quantity, 1) + 1);
            renderEditModal();
        });

        $(document).on('click', '#modal_tbl2 .btn-edit-minus', function (e) {
            e.preventDefault();
            const idx = toNum($(this).closest('.edit-item').data('item-idx'), -1);
            const it = EditCtx.draft?.items?.[idx];
            if (!it) return;
            it.quantity = Math.max(1, toNum(it.quantity, 1) - 1);
            renderEditModal();
        });

        // modal_tbl2 메뉴 삭제
        $(document).on('click', '#modal_tbl2 .btn-edit-remove', function (e) {
            e.preventDefault();
            const idx = toNum($(this).closest('.edit-item').data('item-idx'), -1);
            uiConfirm('메뉴 삭제', '해당 메뉴를 삭제하시겠습니까?', function () {
                EditCtx.draft.items.splice(idx, 1);
                renderEditModal();
            });
        });

        // modal_tbl2 옵션 변경 -> tbl3
        $(document).on('click', '#modal_tbl2 .btn-edit-options', function (e) {
            e.preventDefault(); e.stopPropagation();
            const idx = toNum($(this).data('item-idx'), -1);
            if (idx < 0) return;

            renderOptionsModal(idx);
            $('#modal_tbl2').modal('hide');
            $('#modal_tbl3').modal('show');
        });

        // modal_tbl3 수량 +/-
        $(document).on('click', '#modal_tbl3 .btn-m3-plus', function (e) {
            e.preventDefault();
            const $m3 = $('#modal_tbl3');
            let q = Math.max(1, toNum($m3.find('.m3-qty').val(), 1) + 1);
            $m3.find('.m3-qty').val(q);
            $m3.find('.btn-m3-minus').prop('disabled', q <= 1);
        });

        $(document).on('click', '#modal_tbl3 .btn-m3-minus', function (e) {
            e.preventDefault();
            const $m3 = $('#modal_tbl3');
            let q = Math.max(1, toNum($m3.find('.m3-qty').val(), 1) - 1);
            $m3.find('.m3-qty').val(q);
            $(this).prop('disabled', q <= 1);
        });

        // 단일 선택 그룹이면 같은 그룹 내 1개만 체크 (oc_multi가 N인 경우를 대비)
        $(document).on('change', '#modal_tbl3 li[data-multiple="N"] input.chk-opt', function () {
            const $li = $(this).closest('li[data-oc-idx]');
            if (!$(this).is(':checked')) return;
            $li.find('input.chk-opt').not(this).prop('checked', false);
        });

        // modal_tbl3 변경 완료 -> tbl2 복귀
        $(document).on('click', '#modal_tbl3 .detail_hd button.btn.btn-primary', function (e) {
            e.preventDefault(); e.stopPropagation();
            const ok = applyOptionsFromModal3();
            if (!ok) return;
            $('#modal_tbl3').modal('hide');
            $('#modal_tbl2').modal('show');
        });

        // 메뉴 추가 패널 토글
        $(document).on('click', '#btnToggleCatalog', function (e) {
            e.preventDefault();
            const $p = $('#catalog_panel');
            if (!$p.length) return;
            $p.toggle();
            $(this).text($p.is(':visible') ? '닫기' : '열기');
        });

        // 메뉴 추가 클릭 -> draft에 아이템 추가 -> 옵션 그룹 있으면 즉시 tbl3
        $(document).on('click', '.btn-add-menu', function (e) {
            e.preventDefault();

            const smId = toNum($(this).data('sm-id'), 0);
            if (!smId) return;

            const m = Catalog.menusById[smId];
            if (!m) return;

            const it = {
                sm_id: smId,
                menu_name: m.sm_title,
                quantity: 1,
                unit_price: toNum(m.sm_price, 0),
                options: []
            };

            const groups = Catalog.groupsByMenu[smId] || [];

            // ✅ 옵션이 없는 메뉴면: 바로 draft에 추가(선택할 게 없으니까)
            if (!groups.length) {
                if (!EditCtx.draft.items) EditCtx.draft.items = [];
                EditCtx.draft.items.push(it);
                renderEditModal();
                return;
            }

            // ✅ 옵션이 있는 메뉴면: draft에 넣지 말고 pending에만 보관
            EditCtx.pendingItem = it;
            EditCtx.pendingCommitted = false;

            // modal3가 지금 "draft itemIdx"만 받는 구조라서
            // 임시로 itemIdx 대신 mode를 저장해둔다.
            $('#modal_tbl3').data('editing-mode', 'PENDING_ADD');
            $('#modal_tbl3').data('editing-item-idx', -1); // 의미 없음(구분용)

            // ✅ pending 메뉴를 modal3에 렌더하는 함수를 하나 호출(3번에서 추가)
            renderOptionsModal(it);

            $('#modal_tbl2').modal('hide');
            $('#modal_tbl3').modal('show');
        });

        function renderOptionsModal(target) {
            // target이 숫자면 draft itemIdx, 객체면 pendingItem
            const isIdx = (typeof target === 'number');
            const item = isIdx ? (EditCtx.draft?.items?.[target]) : target;
            if (!item) return;

            const $m3 = $('#modal_tbl3');
            const $ul = $m3.find('ul.bill_list.wide_gap');
            if (!$ul.length) return;

            const smId = toNum(item.sm_id || item.sm_idx, 0);
            const menu = Catalog.menusById[smId] || {};
            const title = escHtml(menu.sm_title || item.menu_name || '(메뉴)');
            const basePrice = toNum(menu.sm_price, toNum(item.unit_price, 0));
            const qty = Math.max(1, toNum(item.quantity, 1));

            const groups = Array.isArray(Catalog.groupsByMenu[smId]) ? Catalog.groupsByMenu[smId] : [];
            const selected = buildSelectedSet(item);

            let html = '';

            // 상단
            html += ''
                + '<li>'
                + '  <div class="d-flex justify-content-between align-items-center">'
                + '    <div>'
                + '      <p class="fs_20">(대표메뉴)' + title + '</p>'
                + '      <p class="tit_st1 mt-2">' + basePrice.toLocaleString() + '원</p>'
                + '    </div>'
                + '    <div>'
                + '      <div class="item_opt_counter">'
                + '        <button type="button" class="btn item_opt_counter_btn pl-1 btn-m3-minus" ' + (qty <= 1 ? 'disabled' : '') + '>'
                + '          <img src="<?=DESIGN_HTTP?>/market/img/ico_decrease.svg" alt="감소">'
                + '        </button>'
                + '        <input type="text" class="quantity m3-qty" value="' + qty + '" readonly>'
                + '        <button type="button" class="btn item_opt_counter_btn pr-1 btn-m3-plus">'
                + '          <img src="<?=DESIGN_HTTP?>/market/img/ico_increase.svg" alt="증가">'
                + '        </button>'
                + '      </div>'
                + '    </div>'
                + '  </div>'
                + '</li>'
                + '<li class="border-bottom"></li>';

            // 옵션 그룹
            if (!groups.length) {
                html += '<li class="tg_500 text-center w-100 py-5">등록된 옵션이 없습니다.</li>';
                $ul.html(html);
            } else {
                groups.forEach(g => {
                    const ocIdx = toNum(g.oc_idx, 0);
                    const gTitle = escHtml(g.oc_title || '');
                    const required = String(g.oc_check || '').toUpperCase() === 'Y';
                    const opts = Array.isArray(Catalog.optionsByGroup[ocIdx]) ? Catalog.optionsByGroup[ocIdx] : [];

                    html += ''
                        + `<li data-oc-idx="${ocIdx}" data-required="${required ? 'Y' : 'N'}" data-multiple="${String(g.oc_multi || 'Y').toUpperCase()==='Y' ? 'Y':'N'}">`
                        + `  <p class="tit_st4 mb-4">${gTitle}`
                        +      (required ? ' <span class="ml-3 text-primary">필수</span>' : ' <span class="ml-3 tg_500 fs_18">선택</span>')
                        + `  </p>`
                        + `  <div class="opt_checks_wp">`;

                    if (!opts.length) {
                        html += '    <div class="tg_500 py-3">등록된 옵션이 없습니다.</div>';
                    } else {
                        opts.forEach(o => {
                            const omIdx = toNum(o.om_idx, 0);
                            const omTitle = escHtml(o.om_title || '');
                            const price = toNum(o.om_price, 0);
                            const checked = selected.has(String(omIdx));

                            html += ''
                                + '    <div class="checks opt_checks">'
                                + '      <label>'
                                + `        <input type="checkbox" class="chk-opt" data-oc-idx="${ocIdx}" data-om-idx="${omIdx}" data-price="${price}" ${checked ? 'checked' : ''}>`
                                + '        <span class="ic_box"></span>'
                                + `        <div class="chk_p"><p>${omTitle}</p></div>`
                                + `        <p class="fw_700 flex-shrink-0 item_opmm">${price.toLocaleString()}원</p>`
                                + '      </label>'
                                + '    </div>';
                        });
                    }

                    html += ''
                        + '  </div>'
                        + '</li>'
                        + '<li class="border-bottom"></li>';
                });

                html = html.replace(/<li class="border-bottom"><\/li>\s*$/, '');
                $ul.html(html);
            }

            // 모드/대상 저장
            if (isIdx) {
                $m3.data('editing-mode', 'DRAFT');
                $m3.data('editing-item-idx', target);
            } else {
                $m3.data('editing-mode', 'PENDING_ADD');
                $m3.data('editing-item-idx', -1);
            }
        }

        // modal_tbl2 저장(주문 변경 저장)
        $(document).on('click', '#modal_tbl2 .detail_hd button.btn.btn-primary', function (e) {
            e.preventDefault(); e.stopPropagation();

            if (!EditCtx.draft?.items?.length) {
                uiAlert('안내', '변경할 메뉴가 없습니다.');
                return;
            }

            uiConfirm('주문 변경', '변경 내용을 저장하시겠습니까?', function () {
                submitOrderUpdate(function () {
                    $('#modal_tbl2').modal('hide');
                    const tvIdx = currentDetail.tv_idx || EditCtx.tv_idx;

                    if (tvIdx) {
                        fetchDetail(tvIdx, function (data) {
                            renderDetailModal(data);
                            renderDetailOrders(data);
                            $('#modal_tbl1').modal('show');
                        });
                    } else {
                        $('#modal_tbl1').modal('show');
                    }

                    fetchList();
                });
            });
        });

        // 결제 취소 폼 토글
        $(document).on('click', '.btn-order-refund', function (e) {
            e.preventDefault(); e.stopPropagation();
            const $block = $(this).closest('.order-block');
            if (!$block.length) return;

            $('.order-block .pay_cncl').not($block.find('.pay_cncl')).hide();

            const $box = $block.find('.pay_cncl[data-refund-box="1"]');
            if (!$box.length) return;

            $box.toggle();
            if ($box.is(':visible')) {
                const $input = $box.find('.input-refund-amount');
                $input.val('');
                $input.focus();
                $box.find('.refund-msg').hide().text('');
            }
        });

        $(document).on('input', '.pay_cncl .input-refund-amount', function () {
            const raw = onlyNumberStr(this.value);
            this.value = raw ? formatComma(raw) : '';
        });

        $(document).on('click', '.pay_cncl .btn-refund-cancel', function (e) {
            e.preventDefault(); e.stopPropagation();
            $(this).closest('.pay_cncl').hide();
        });

        $(document).on('click', '.pay_cncl .btn-refund-confirm', function (e) {
            e.preventDefault(); e.stopPropagation();

            const $box = $(this).closest('.pay_cncl');
            const $block = $box.closest('.order-block');

            const orderIdx = Number($block.data('order-idx') || 0);
            const maxTotal = Number($block.data('order-total') || 0);

            const $input = $box.find('.input-refund-amount');
            const amount = parseAmount($input.val());
            const $msg = $box.find('.refund-msg');

            if (!orderIdx) { uiAlert('오류', '주문 식별값이 없습니다.'); return; }
            if (!amount || amount <= 0) {
                $msg.show().removeClass('text-success').addClass('text-warning').text('환불 금액을 입력해 주세요.');
                $input.focus(); return;
            }
            if (amount > maxTotal) {
                $msg.show().removeClass('text-success').addClass('text-warning').text('환불 금액이 주문 금액을 초과할 수 없습니다.');
                $input.focus(); return;
            }

            uiConfirm('결제 취소', `${formatComma(amount)}원을 환불 처리하시겠습니까?`, function () {
                const $btn = $box.find('.btn-refund-confirm');
                $btn.prop('disabled', true);

                refundOrder(orderIdx, amount, function () {
                    $btn.prop('disabled', false);
                    $msg.show().removeClass('text-warning').addClass('text-success').text('환불 요청이 완료되었습니다.');

                    setTimeout(function () {
                        $box.hide();
                        if (currentDetail && currentDetail.tv_idx) {
                            fetchDetail(currentDetail.tv_idx, function (data) {
                                renderDetailModal(data);
                                renderDetailOrders(data);
                            });
                        }
                        fetchList();
                    }, 400);
                });
            });
        });

        // =========================================================
        // INIT
        // =========================================================
        $(function () {
            bindSortControls();
            fetchList();
            setInterval(fetchList, POLL_MS);
        });

        // 필요시 외부에서 접근 가능하도록 노출
        global.renderDetailOrders = renderDetailOrders;
        global.fetchDetail = fetchDetail;
        global.currentDetail = currentDetail;

    })(window);
</script>

<? include_once("./inc/tail.php"); ?>
