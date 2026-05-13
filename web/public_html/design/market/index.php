<?
$_SUB_HEAD_TITLE = "메인화면";
$_GET['hd_pc'] = ' '; //PC hd  로그인시공백 로그아웃시 logout
$hd_num = ' '; //왼쪽메뉴 active 땜시 만듬
$hd_left = 'index'; //왼쪽메뉴 on 땜시 만듬
include_once("./inc/head.php");
?>

<!-- 왼쪽 메뉴-->
<? include_once("./inc/left_menu.php"); ?>

<div class="sub_pg ">
	<div class="sub_wr">
		<div class="hd_tit2">
			<div class="flex-shrink-0 ml-auto">
				<button type="button" class="btn btn-outline-light rounded-pill" onclick="location.href='./cmp_list.php' ">완료 내역</button>
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

		<section class="tbl_box">
			<!-- 주문접수-->
			<div class="card">

				<div class="d-flex align-items-center justify-content-between">
					<span class="status status_01">주문접수</span>
					<p class="d-flex align-items-center justify-content-center fs_16 tg_500">
						<span class="mr-1"><img src="./img/ico_time.svg" alt=" "></span>
						<span>1분 전</span>
					</p>
				</div>
				<p class="fw_800 fs_44 mt_35 text-center">1</p>
				<p class="tg_500 mt_20 line1_text text-center">해물칼국수 1개 해물칼국수 1개</p>
				<p class=" fw_700 mt-1  text-center  ">8,000원</p>

				<div class="mt_20 position-relative">
					<button type="button" class="btn btn-primary btn-block px-1">음식 준비하기</button>
					<!-- 추가주문시 나오는 플로팅 이미지-->
					<div class="tooltip-bubble floating">
						추가 주문이에요! 😃
					</div>
				</div>
				<a href="" class="item_link" data-toggle="modal" data-target="#modal_tbl1"></a>
			</div>
			<!-- 음식준비중-->
			<div class="card">
				<div class="d-flex align-items-center justify-content-between">
					<span class="status status_02">음식준비중</span>
					<p class="d-flex align-items-center justify-content-center fs_16 tg_500">
						<span class="mr-1"><img src="./img/ico_time.svg" alt=" "></span>
						<span>1분 전</span>
					</p>
				</div>
				<p class="fw_800 fs_44 mt_35 text-center">2</p>
				<p class="tg_500 mt_20 line1_text text-center">해물칼국수 1개 해물칼국수 1개</p>
				<p class=" fw_700 mt-1  text-center  ">8,000원</p>
				<div class="mt_20 position-relative">
					<button type="button" class="btn btn-outline-primary btn-block px-1">전달 완료</button>
				</div>
				<a href="" class="item_link"></a>
			</div>
			<!-- 전달완료-->
			<div class="card">
				<div class="d-flex align-items-center justify-content-between">
					<span class="status status_03">전달완료</span>
					<p class="d-flex align-items-center justify-content-center fs_16 tg_500">
						<span class="mr-1"><img src="./img/ico_time.svg" alt=" "></span>
						<span>1분 전</span>
					</p>
				</div>
				<p class="fw_800 fs_44 mt_35 text-center">3</p>
				<p class="tg_500 mt_20 line1_text text-center">해물칼국수 1개 해물칼국수 1개</p>
				<p class=" fw_700 mt-1  text-center  ">8,000원</p>
				<div class="mt_20 position-relative">
					<button type="button" class="btn btn-outline-light btn-block px-1">좌석 비우기</button>
				</div>
				<a href="" class="item_link"></a>
			</div>
			<!-- 빈자리-->
			<div class="card empty_tbl">
				<div class="d-flex align-items-center justify-content-between">
					<span class="status status_04">빈자리</span>

				</div>
				<p class="fw_800 fs_44 mt_35 text-center">5</p>
				<div class="mt_20 position-relative">
					<button type="button" class="btn btn-outline-light btn-block px-1">QR 코드 확인</button>
					<button type="button" class="btn btn-outline-light btn-block px-1">테이블삭제</button>
				</div>
			</div>
			<!-- 빈자리-->
			<div class="card empty_tbl">
				<div class="d-flex align-items-center justify-content-between">
					<span class="status status_04">빈자리</span>

				</div>
				<p class="fw_800 fs_44 mt_35 text-center">6</p>
				<p class="mt_20 text-center"><span class="txt_under tg_500">테이블 삭제</span></p>
				<a href="" class="item_link"></a>
			</div>
			<!-- 빈자리-->
			<div class="card empty_tbl">
				<div class="d-flex align-items-center justify-content-between">
					<span class="status status_04">빈자리</span>

				</div>
				<p class="fw_800 fs_44 mt_35 text-center">7</p>
				<p class="mt_20 text-center"><span class="txt_under tg_500">테이블 삭제</span></p>
				<a href="" class="item_link"></a>
			</div>
		</section>

	</div>

</div>

<!-- data-toggle="modal" data-target="#modal_tbl1" B-2 테이블관리 상세(모달)-->
<div class="modal modal_rr fade" id="modal_tbl1" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog ">
		<div class="modal-content">
			<button type="button" class="close1" data-dismiss="modal" aria-label="Close"><img src="./img/ic_close.png" alt="닫기"></button>

			<div class="modal-body">
				<div class="d-flex align-items-center justify-content-between">
					<span class="status status_01">주문접수</span>
					<p class="d-flex align-items-center justify-content-center fs_16 tg_500">
						<span class="mr-1"><img src="./img/ico_time.svg" alt=" "></span>
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
				<section class="bill_wr">
					<div class="py-4 border-bottom-dot mb-4">
						<span class="mr-4">주문 번호 : No.00000001</span>
						<span>주문일시 : 2025년 08월 09일 15:00</span>
					</div>

					<ul class="bill_list">
						<li class="d-flex align-items-center justify-content-between ">
							<p class="tit_st3">주문메뉴</p>
							<div> <button type="button" class="btn btn-md btn-outline-light bg-light rounded-pill px-4 mr-2 " data-toggle="modal" data-target="#modal_tbl2" data-dismiss="modal"><span class="mr-2"><img src="./img/ico_edit.svg" alt=" "></span>주문 변경</button>
							<button type="button" class="btn btn-md btn-secondary   rounded-pill px-4 " data-toggle="modal" data-target="#modal_tbl2" data-dismiss="modal"><span class="mr-2"> </span>결제취소</button>
							</div>

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
				<div class="bill_wr_add">
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
									<button type="button" class="btn btn-md btn-outline-light bg-light rounded-pill px-4 " data-toggle="modal" data-target="#modal_tbl2"><span class="mr-2"><img src="./img/ico_edit.svg" alt=" "></span>주문 변경</button>
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
				<button type="button" class="btn btn-secondary btn-block mt-4">결제 취소</button>
				<div class="pay_cncl">
					<div class="form_wr  ">
						<div class="ip_tit  ">
							<h5 class="   text-white">결제취소/환불 금액(원)</h5>
						</div>
						<div class="form-row ">
							<div class="col-6">
								<input type="text" class="form-control" placeholder="0">
							</div>
							<div class="col-3">
								<button type="button" class="btn btn-primary btn-block px-1">확인</button>
							</div>
							<div class="col-3">
								<button type="button" class="btn btn-outline-light btn-block px-1">취소</button>
							</div>
						</div>

					</div>
				</div>
				<div class="mt-4 d-flex align-items-center justify-content-between bg-light p-5 rounded">
					<p class="fw_600">고객정보</p>
					<p>홍길동 &#40;010-1234-5678&#41;</p>
				</div>
				<div class="form-row mt-5">
					<div class="col-6">
						<button type="button" class="btn btn-outline-light btn-lg btn-block fs_20 btn_qr"><span class="mr-2"><img src="./img/ico_qr.svg" alt=" "></span> QR코드 보기</button>
					</div>
					<div class="col-6">
						<button type="button" class="btn btn-outline-light  btn-lg btn-block  fs_20 btn_tbl_change"><span class="mr-2"><img src="./img/ico_change.svg" alt=" "></span> 자리 이동</button>
					</div>
				</div>
				<div class="btn_qr_wr" style="display:none;">
					<div class="py-5 px-4 text-center">
						<p class="mb-3">QR코드는 png 이미지파일로 다운로드됩니다.</p>
						<img src="./img/qrimg.jpg" alt="qr생성시 예시이미지">

						<div class="d-flex justify-content-center mt-3">
							<button type="button" class="btn btn-outline-light   mr-2">QR 새로 생성</button> <button type="button" class="btn btn-outline-light  ">QR 다운로드</button>
						</div>
						<button class="btn btn_close btn-light btn-block mt-5"><img src="./img/selectarrow_up.svg" alt=" "></button>
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

						<button type="button" class="btn btn-secondary  btn-block mt-4">자리 이동</button>
					</div>
					<button class="btn btn_close btn-light btn-block mt-5"><img src="./img/selectarrow_up.svg" alt=" "></button>

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


<!-- data-toggle="modal" data-target="#modal_tbl_add" B-6 테이블 추가(모달) -->
<div class="modal modal_rr fade" id="modal_tbl_add" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog ">
		<div class="modal-content">
			<button type="button" class="close1" data-dismiss="modal" aria-label="Close"><img src="./img/ic_close.png" alt="닫기"></button>
			<div class="modal-body">

				<div class=" detail_hd mt-4">
					<h2 class="tit_st1 d-flex align-items-center"> <span>테이블 추가</span></h2>
					<button type="button" class="btn btn-primary" data-dismiss="modal">추가하기</button>
				</div>
				<section class="py-5 border-top border-dark">

					<div class="row">
						<div class="col-4 fw_600 mb-4 d-flex align-items-center">
							테이블명
						</div>
						<div class="col-8 mb-4">
							<input type="text" class="form-control" placeholder="5자 미만 숫자 or 영문만 가능합니다.">
						</div>
						<div class="col-4 fw_600 mb-4 d-flex align-items-center">
							좌석 수
						</div>
						<div class="col-8 mb-4">
							<input type="text" class="form-control" placeholder="1">
						</div>
						<div class="col-4 fw_600 mb-4 d-flex align-items-center">
							QR코드 생성
						</div>
						<div class="col-8 mb-4">
							<button type="button" class="btn btn-secondary">코드 생성하기</button>
							<p class="mt-4">
								<img src="./img/qrimg.jpg" alt="qr생성시 예시이미지">
							</p>
						</div>


					</div>

				</section>
			</div>

		</div>
	</div>
</div>


<? include_once("./inc/tail.php"); ?>
