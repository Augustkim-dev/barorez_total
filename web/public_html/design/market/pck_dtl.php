<?
$_SUB_HEAD_TITLE = "포장 접수";
$_GET['hd_pc'] = '1'; //PC hd  로그인시공백 / 로그아웃시 logout
$hd_num = 'revenue'; //1차메뉴
$hd_left = 'pck_dtl'; //왼쪽메뉴 on 땜시 만듬
include_once("./inc/head.php");
?>
<!-- 왼쪽 메뉴-->
<? include_once("./inc/left_menu.php"); ?>



<div class="sub_pg  bg-white">
    <div class="pck_list_wr">
        <div class="pck_list">
            <div class="pck_list_box">
                <!--신규리스트  -->
                <div id="pck_list_new" class="collapse_ex">
                    <button type="button" class="btn  " data-toggle="collapse" data-target="#pck_list_new_dtl">
                        <p>신규 <span class="text-primary m-2">1건</span></p>
                        <img src="./img/selectarrow.svg">
                    </button>

                    <!--주문이 없을경우   
                    <div class="no_data  ">
                        <img src="./img/img_mark3.svg" style="width:5rem">
                        <p class=" tg_500 line_h1_4 mt-3">주문이 없습니다</p>
                    </div>
                    -->

                    <div id="pck_list_new_dtl" class="collapse show" data-parent="#pck_list_new">
                        <!-- 선택된 박스(.pck_card)는 주황색 테두리 .active -->
                        <a class="pck_card active" href="./pck_dtl.php">
                            <div class="cardtxt">
                                <div class="flex-fill">
                                    <p class="d-flex align-items-center text-primary fw_500">
                                        <span class="mr-2"><img src="./img/ico_time2.svg" alt=" "></span>
                                        <span>1분 48초전</span>
                                    </p>
                                    <p class="line1_text fw_500 mt-2">김치볶음밥 1개, 콜라 1개, 해물칼국수 대자</p>
                                </div>
                                <div class="">
                                    <p class="pck_alim">
                                        접수
                                    </p>
                                </div>
                            </div>
                            <p class="line1_text fs_16 tg_500 mt-1 ">20,500원 (김효주 010-1234-1234)</p>
                        </a>
                    </div>
                </div>

                <!-- 진행중 리스트  -->
                <div id="pck_list_ing" class="collapse_ex">
                    <button type="button" class="btn  " data-toggle="collapse" data-target="#pck_list_ing_dtl">
                        <p>진행중 <span class="text-primary m-2">1건</span></p>
                        <img src="./img/selectarrow.svg">
                    </button>

                    <div id="pck_list_ing_dtl" class="collapse show" data-parent="#pck_list_ing">
                        <a class="pck_card" href="./pck_dtl2.php">
                            <div class="cardtxt">
                                <div class="flex-fill">
                                    <p class="d-flex align-items-center text-primary fw_500">
                                        <span class="mr-2"><img src="./img/ico_time2.svg" alt=" "></span>
                                        <span>1분 48초전</span>
                                    </p>
                                    <p class="line1_text fw_500 mt-2">김치볶음밥 1개, 콜라 1개, 해물칼국수 대자</p>
                                </div>
                                <div class="">
                                    <p class="pck_alim" style="background-color:#1362E6 ;">
                                        음식<br>
                                        준비중
                                    </p>
                                </div>
                            </div>
                            <p class="line1_text fs_16 tg_500 mt-1 ">20,500원 (김효주 010-1234-1234)</p>
                        </a>
                        <a class="pck_card" href="./pck_dtl2.php">
                            <div class="cardtxt">
                                <div class="flex-fill">
                                    <p class="d-flex align-items-center text-primary fw_500">
                                        <span class="mr-2"><img src="./img/ico_time2.svg" alt=" "></span>
                                        <span>1분 48초전</span>
                                    </p>
                                    <p class="line1_text fw_500 mt-2">김치볶음밥 1개, 콜라 1개, 해물칼국수 대자</p>
                                </div>
                                <div class="">
                                    <p class="pck_alim" style="background-color:#1362E6 ;">
                                        음식<br>
                                        준비중
                                    </p>
                                </div>
                            </div>
                            <p class="line1_text fs_16 tg_500 mt-1 ">20,500원 (김효주 010-1234-1234)</p>
                        </a>
                    </div>
                </div>

                <!-- 완료/취소 리스트  -->
                <div id="pck_list_fi" class="collapse_ex">
                    <button type="button" class="btn  " data-toggle="collapse" data-target="#pck_list_fi_dtl">
                        <p>완료/취소 <span class="text-primary m-2">1건</span></p>
                        <img src="./img/selectarrow.svg">
                    </button>

                    <div id="pck_list_fi_dtl" class="collapse show" data-parent="#pck_list_fi">

                        <a class="pck_card" href="./pck_dtl3.php">
                            <div class="cardtxt">
                                <div class="flex-fill">
                                    <p class="d-flex align-items-center text-primary fw_500">
                                        <span class="mr-2"><img src="./img/ico_time2.svg" alt=" "></span>
                                        <span>1분 48초전</span>
                                    </p>
                                    <p class="line1_text fw_500 mt-2">김치볶음밥 1개, 콜라 1개, 해물칼국수 대자</p>
                                </div>
                                <div class="">
                                    <p class="pck_alim" style="background-color:#23B169 ;">
                                        전달<br>
                                        완료
                                    </p>
                                </div>
                            </div>
                            <p class="line1_text fs_16 tg_500 mt-1 ">20,500원 (김효주 010-1234-1234)</p>
                        </a>
                        <a class="pck_card" href="./pck_dtl4.php">
                            <div class="cardtxt">
                                <div class="flex-fill">
                                    <p class="d-flex align-items-center text-primary fw_500">
                                        <span class="mr-2"><img src="./img/ico_time2.svg" alt=" "></span>
                                        <span>1분 48초전</span>
                                    </p>
                                    <p class="line1_text fw_500 mt-2">김치볶음밥 1개, 콜라 1개, 해물칼국수 대자</p>
                                </div>
                                <div class="">
                                    <p class="pck_alim" style="background-color:#6C757D ;">
                                        취소
                                    </p>
                                </div>
                            </div>
                            <p class="line1_text fs_16 tg_500 mt-1 ">20,500원 (김효주 010-1234-1234)</p>
                        </a>
                        <a class="pck_card" href="./pck_dtl4.php">
                            <div class="cardtxt">
                                <div class="flex-fill">
                                    <p class="d-flex align-items-center text-primary fw_500">
                                        <span class="mr-2"><img src="./img/ico_time2.svg" alt=" "></span>
                                        <span>1분 48초전</span>
                                    </p>
                                    <p class="line1_text fw_500 mt-2">김치볶음밥 1개, 콜라 1개, 해물칼국수 대자</p>
                                </div>
                                <div class="">
                                    <p class="pck_alim" style="background-color:#6C757D ;">
                                        취소
                                    </p>
                                </div>
                            </div>
                            <p class="line1_text fs_16 tg_500 mt-1 ">20,500원 (김효주 010-1234-1234)</p>
                        </a>
                        <a class="pck_card" href="./pck_dtl4.php">
                            <div class="cardtxt">
                                <div class="flex-fill">
                                    <p class="d-flex align-items-center text-primary fw_500">
                                        <span class="mr-2"><img src="./img/ico_time2.svg" alt=" "></span>
                                        <span>1분 48초전</span>
                                    </p>
                                    <p class="line1_text fw_500 mt-2">김치볶음밥 1개, 콜라 1개, 해물칼국수 대자</p>
                                </div>
                                <div class="">
                                    <p class="pck_alim" style="background-color:#6C757D ;">
                                        취소
                                    </p>
                                </div>
                            </div>
                            <p class="line1_text fs_16 tg_500 mt-1 ">20,500원 (김효주 010-1234-1234)</p>
                        </a>
                        <a class="pck_card" href="./pck_dtl4.php">
                            <div class="cardtxt">
                                <div class="flex-fill">
                                    <p class="d-flex align-items-center text-primary fw_500">
                                        <span class="mr-2"><img src="./img/ico_time2.svg" alt=" "></span>
                                        <span>1분 48초전</span>
                                    </p>
                                    <p class="line1_text fw_500 mt-2">김치볶음밥 1개, 콜라 1개, 해물칼국수 대자</p>
                                </div>
                                <div class="">
                                    <p class="pck_alim" style="background-color:#6C757D ;">
                                        취소
                                    </p>
                                </div>
                            </div>
                            <p class="line1_text fs_16 tg_500 mt-1 ">20,500원 (김효주 010-1234-1234)</p>
                        </a>
                        <a class="pck_card" href="./pck_dtl4.php">
                            <div class="cardtxt">
                                <div class="flex-fill">
                                    <p class="d-flex align-items-center text-primary fw_500">
                                        <span class="mr-2"><img src="./img/ico_time2.svg" alt=" "></span>
                                        <span>1분 48초전</span>
                                    </p>
                                    <p class="line1_text fw_500 mt-2">김치볶음밥 1개, 콜라 1개, 해물칼국수 대자</p>
                                </div>
                                <div class="">
                                    <p class="pck_alim" style="background-color:#6C757D ;">
                                        취소
                                    </p>
                                </div>
                            </div>
                            <p class="line1_text fs_16 tg_500 mt-1 ">20,500원 (김효주 010-1234-1234)</p>
                        </a>



                    </div>
                </div>

            </div>


        </div>
        <div class="pck_list_dtl">
            <div>
                <div class="d-flex align-items-center justify-content-between">
                    <span class="status status_01">접수대기</span>
                    <p class="d-flex align-items-center justify-content-center fs_16 tg_500">
                        <span class="mr-1"><img src="./img/ico_time.svg" alt=" "></span>
                        <span>1분 전</span>
                    </p>
                </div>
                <div class=" detail_hd mt-4">
                    <div>
                        <h3 class="tit_st1">포장주문</h3>
                        <p class="mt-2 fw_600">메뉴3개 ㆍ224,100원</p>
                    </div>
                    <div class="d-flex pck_cont">
                        <button type="button" class="btn btn-light mr-3">거절</button>
                        <div class="item_opt_counter  mr-3 ">
                            <button type="button" class="btn item_opt_counter_btn pl-1">
                                <img src="./img/ico_decrease.svg" alt="감소">
                            </button>
                            <input type="text" class="quantity" value="10분">
                            <button type="button" class="btn item_opt_counter_btn pr-1">
                                <img src="./img/ico_increase.svg" alt="증가">
                            </button>
                        </div>
                        <!-- 숫자작성가능-->
                        <button type="button" class="btn btn-primary  ">접수</button>

                    </div>

                </div>
                <section class="bill_wr">
                    <div class="py-4 border-bottom-dot mb-4">
                        <span class="mr-4">주문 번호 : No.00000001</span>
                        <span>주문일시 : 2025년 08월 09일 15:00</span>
                    </div>

                    <ul class="bill_list">
                        <li class="d-flex align-items-center justify-content-between ">
                            <p class="tit_st3">주문메뉴</p>
                            <button type="button" class="btn btn-md btn-outline-light bg-light rounded-pill px-4 " data-toggle="modal" data-target="#modal_tbl2" data-dismiss="modal"><span class="mr-2"><img src="./img/ico_edit.svg" alt=" "></span>주문 변경</button>
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
                <div class="mt-4 d-flex align-items-center justify-content-between bg-light p-5 rounded">
                    <p class="fw_600">고객정보</p>
                    <p>홍길동 (010-1234-5678)</p>
                </div>
            </div>
        </div>
    </div>


</div>


<!-- data-toggle="modal" data-target="#modal_tbl2" B-3 주문수정(모달) // 똑같음 -->
<div class="modal modal_rr fade" id="modal_tbl2" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog ">
		<div class="modal-content">
			<button type="button" class="close1" data-dismiss="modal" aria-label="Close"><img src="./img/ic_close.png" alt="닫기"></button>
			<div class="modal-body">

				<div class=" detail_hd mt-4">
					<h2 class="tit_st1 d-flex align-items-center"><a href="#" data-toggle="modal" data-target="#modal_tbl1" data-dismiss="modal" class="mr-4 line_h0 "><img src="./img/ico_back.svg" alt=" 뒤로가기"></a><span>주문 변경</span></h2>
					<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal_tbl1" data-dismiss="modal">변경 완료</button>
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
											<img src="./img/ico_decrease.svg" alt="감소">
										</button>
										<input type="text" class="quantity" value="255">
										<button type="button" class="btn item_opt_counter_btn pr-1">
											<img src="./img/ico_increase.svg" alt="증가">
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
											<img src="./img/ico_decrease.svg" alt="감소">
										</button>
										<input type="text" class="quantity" value="255">
										<button type="button" class="btn item_opt_counter_btn pr-1">
											<img src="./img/ico_increase.svg" alt="증가">
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
			<button type="button" class="close1" data-dismiss="modal" aria-label="Close"><img src="./img/ic_close.png" alt="닫기"></button>
			<div class="modal-body">

				<div class=" detail_hd mt-4">
					<h2 class="tit_st1 d-flex align-items-center"><a href="#" data-toggle="modal" data-target="#modal_tbl2" data-dismiss="modal" class="mr-4 line_h0 "><img src="./img/ico_back.svg" alt=" 뒤로가기"></a><span>옵션 변경</span></h2>
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
											<img src="./img/ico_decrease.svg" alt="감소">
										</button>
										<input type="text" class="quantity" value="0">
										<button type="button" class="btn item_opt_counter_btn pr-1">
											<img src="./img/ico_increase.svg" alt="증가">
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

<? include_once("./inc/tail.php"); ?>