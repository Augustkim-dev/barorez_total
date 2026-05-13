<?
$_SUB_HEAD_TITLE = "완료 내역";
$_GET['hd_pc'] = ' '; //PC hd  로그인시공백 로그아웃시 logout
$hd_num = ' '; //왼쪽메뉴 active 땜시 만듬
$hd_left = 'cmp_list'; //왼쪽메뉴 on 땜시 만듬
include_once("./inc/head.php");
?>

<!-- 왼쪽 메뉴-->
<? include_once("./inc/left_menu.php"); ?>

<div class="sub_pg ">
	<div class="sub_wr">
		<div class="hd_tit2 fs_16">
			<div class="flex-shrink-0 ml-auto   d-flex align-items-end">
				<p class="d-flex align-content-center mb-4 mb-lg-0"><img src="./img/img_mark2.svg" class="mr-2" alt=" "> 주문내역 클릭시 주문 상세보기가 나타납니다.</p>
			</div>
			<div class="d-flex align-items-end flex-wrap">
				<h2 class="tit_st1 d-flex align-items-center mr-5"><a href="#" onclick="history.back(); return false;" class="mr-4 line_h0 "><img src="./img/ico_back.svg" alt=" 뒤로가기"></a><span>완료/취소</span></h2>

				<div class="btn-group btn-group-toggle gr_st1" data-toggle="buttons">
					<label class="btn  mr-4 active  ">
						<input type="radio" name="options" id="option1" checked=""> 테이블
					</label>
					<label class="btn  mr-4 ">
						<input type="radio" name="options" id="option2"> 포장
					</label>
					<label class="btn mr-4  ">
						<input type="radio" name="options" id="option3"> 예약
					</label>
				</div>
			</div>

		</div>
		<div class="card cmp_box">
			<div class="card-header">
				<div class=" btn-group-toggle" data-toggle="buttons">
					<label class="btn btn-outline-light active">
						<input type="radio" name="options" id="option1" checked=""> 어제
					</label>
					<label class="btn btn-outline-light    ">
						<input type="radio" name="options" id="option2"> 오늘
					</label>
				</div>
				<div class="d-flex">
					<input type="date" class="form-control  ">
					<p>~</p>
					<input type="date" class="form-control   ">
				</div>
				<div class="d-flex">
					<input type="text" class="form-control " placeholder="입력하세요"> <button type="button" class="btn btn-secondary  ">검색</button>
				</div>
			</div>
			<div class="card-body">
				<div class="d-flex align-items-center justify-content-between  ">
					<h3 class="tit_st2 pr-3">2025년 12월 09일 주문내역</h3>
					<div class=" ">
						<div class="btn-group btn-group-toggle btn_toggle_primary" data-toggle="buttons">
							<label class="btn btn-outline-light   active">
								<input type="radio" name="options" id="option1" checked=""> 전체
							</label>
							<label class="btn btn-outline-light  ">
								<input type="radio" name="options" id="option2"> 완료
							</label>
							<label class="btn btn-outline-light  ">
								<input type="radio" name="options" id="option3"> 취소
							</label>
						</div>
					</div>
				</div>

				<section class="table_scroll mt-4">
					<table class="table_01" summary=" ">
						<caption>
							주문내역 리스트
						</caption>
						<colgroup>
							<col width="*">
							<col width="*">
							<col width="*">
							<col width="*">
							<col width="*">
							<col width="25%">
							<col width="*">
						</colgroup>
						<thead>
							<tr>
								<th>번호</th>
								<th>주문번호</th>
								<th>주문상태</th>
								<th>주문시간</th>
								<th>테이블명</th>
								<th>주문내역</th>
								<th>결제금액</th>

								</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>3</td>
								<td><a href="" class="txt_under_link" data-toggle="modal" data-target="#modal_tbl_list">No.00000001</a></td>
								<td>
									<p>완료</p>
								</td>
								<td>2025.12.12 11:03</td>
								<td>1</td>
								<td>
									<p class="line1_text">칼국수 1개, 콜라 1개, 김치볶음밥칼국수 1개, 콜라 1개, 김치볶음밥</p>
								</td>
								<td class="text-right"><b>22,000원</b></td>
							</tr>
							<tr>
								<td>2</td>
								<td><a href="" class="txt_under_link" data-toggle="modal" data-target="#modal_tbl_list">No.00000001</a></td>
								<td>
									<p class="text-danger">취소</p>
								</td>
								<td>2025.12.12 11:03</td>
								<td>1</td>
								<td>
									<p class="line1_text">칼국수 1개</p>
								</td>
								<td class="text-right"><b>8,000원</b></td>
							</tr>
							<tr>
								<td>1</td>
								<td><a href="" class="txt_under_link" data-toggle="modal" data-target="#modal_tbl_list">No.00000001</a></td>
								<td>
									<p>완료</p>
								</td>
								<td>2025.12.12 11:03</td>
								<td>8</td>
								<td>
									<p class="line1_text">칼국수 1개, 콜라 1개, 김치볶음밥칼국수 1개, 콜라 1개, 김치볶음밥</p>
								</td>
								<td class="text-right"><b>1,122,000원</b></td>
							</tr>


						</tbody>
					</table>
				</section>


			</div>
		</div>



	</div>
	<div class="modal modal_rr fade" id="modal_tbl_list" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog ">
			<div class="modal-content">
				<button type="button" class="close1" data-dismiss="modal" aria-label="Close"><img src="./img/ic_close.png" alt="닫기"></button>
				<div class="modal-body">
					<div class="d-flex  ">
						<!-- 완료일때와 취소일때-->
						<span class="status status_04">완료내역</span> <span class="status status_04">취소내역</span>
					</div>
					<div class=" detail_hd mt-4">
						<div>
							<h3 class="tit_st1">테이블번호 1</h3>
							<p class="mt-2">메뉴3개 ㆍ224,100원ㆍ4인석</p>
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

</div>






<? include_once("./inc/tail.php"); ?>