<?
$_SUB_HEAD_TITLE = "메인화면";
$_GET['hd_pc'] = '1'; //PC hd  로그인시공백 / 로그아웃시 logout
$hd_num = 'revenue'; //1차메뉴
$hd_num2 = 'revenue1'; //2차메뉴
$hd_left = ' '; //왼쪽메뉴 on 땜시 만듬
include_once("./inc/head.php");
?>


<!-- 왼쪽 메뉴-->
<? include_once("./inc/left_menu.php"); ?>

<div class="sub_pg ">
	<div class="sub_wr">
		<div class="hd_tit2 fs_16 flex-row">
			<!-- <div class="flex-shrink-0 ml-auto   d-flex align-items-end">
				<p class="d-flex align-content-center mb-4 mb-lg-0"><img src="./img/img_mark2.svg" class="mr-2" alt=" "> 주문내역 클릭시 주문 상세보기가 나타납니다.</p>
			</div> -->
			<h2 class="tit_st1 d-flex align-items-center mr-5 "> <span>정산관리</span></h2>
			<p>기간별, 상태별 정산 내역을 조회합니다.</p>

		</div>

		<div class="card  cmp_box">
			<div class="card-header">
				<div>
					<div class="custom-sel">
						<button type="button" class="select-trigger">
							정산 상태
						</button>
						<ul class="select-options">
							<li data-value="1">전체</li>
							<li data-value="2">미정산</li>
							<li data-value="3">정산예정</li>
							<li data-value="4">정산완료</li>
						</ul>
						<input type="hidden" name="option">
					</div>
				</div>
				<div class=" ">
					<div class="btn-group btn-group-toggle btn_toggle_primary group_sm" data-toggle="buttons">
						<label class="btn btn-outline-light active">
							<input type="radio" name="options" id="option1" checked=""> 전체
						</label>
						<label class="btn btn-outline-light  ">
							<input type="radio" name="options" id="option2"> 오늘
						</label>
						<label class="btn btn-outline-light  ">
							<input type="radio" name="options" id="option3"> 3일
						</label>
						<label class="btn btn-outline-light  ">
							<input type="radio" name="options" id="option3"> 7일
						</label>
						<label class="btn btn-outline-light  ">
							<input type="radio" name="options" id="option3"> 30일
						</label>
					</div>
				</div>
				<div class="d-flex">
					<input type="date" class="form-control  ">
					<p>~</p>
					<input type="date" class="form-control   ">
				</div>
				<button type="button" class="btn btn-secondary  ">조회</button>
			</div>
			<div class="card-body">
				<h3 class="tit_st2 pr-3">정산내역</h3>

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
							<col width="*">
							<col width="*">
							<col width="*">
						</colgroup>
						<thead>
							<tr>
								<th>정산번호</th>
								<th>정산 예정일</th>
								<th>매출액</th>
								<th>수수료(원)</th>
								<th>정산금액(원)</th>
								<th>정산기간</th>
								<th>상태</th>
								<th>관리</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>ST12345-41234</td>
								<td>2026.01.05</td>
								<td>
									<p>123,123 </p>
								</td>
								<td>6,810 </td>

								<td>
									<p>123,123 </p>
								</td>
								<td>2026.01.05~2026.01.30</td>
								<td>
									<p>정산완료</p>
								</td>
								<td>
									<button type="button" class="btn btn-outline-primary btn-sm rounded-pill">상세</button>
								</td>
							</tr>
							<tr>
								<td>ST12345-41234</td>
								<td>2026.01.05</td>
								<td>
									<p>123,123 </p>
								</td>
								<td>6,810 </td>

								<td>
									<p>123,123 </p>
								</td>
								<td>2026.01.05~2026.01.30</td>
								<td>
									<p class="text-danger">미정산</p>
								</td>
								<td>
									<button type="button" class="btn btn-outline-primary btn-sm rounded-pill">상세</button>
								</td>
							</tr>
							<tr>
								<td>ST12345-41234</td>
								<td>2026.01.05</td>
								<td>
									<p>123,123 </p>
								</td>
								<td>6,810 </td>

								<td>
									<p>123,123 </p>
								</td>
								<td>2026.01.05~2026.01.30</td>
								<td>
									<p class="text-success">정산예정</p>
								</td>
								<td>
									<button type="button" class="btn btn-outline-primary btn-sm rounded-pill">상세</button>
								</td>
							</tr>


						</tbody>
					</table>
				</section>


			</div>
		</div>



	</div>


</div>



<? include_once("./inc/tail.php"); ?>