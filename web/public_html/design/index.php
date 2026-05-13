<?
$_SUB_HEAD_TITLE = "메인"; //헤더에 타이틀명이 없을경우 공백
$_GET['hd_num'] = '1'; //모바일 hd 1~n까지 있음
include_once("./inc/head.php");
?>


<div class="wrap">
	<div class=" idx_pg  ">
		<div class="container shop_hd">

			<div class="d-flex  align-items-center ">
				<div class="mr-2">
					<p class="fs_20 fw_700">바다마을 해물칼국수 [성수점] </p>
					<p class="text-primary fs_15 fw_500 mt-2">테이블 10번</p>
				</div>
				<div class="ml-auto ">
					<div class="item_img">
						<div class="rect rounded-pill">
							<img class="flex-shrink-0  " src="./img/pr_sample01.jpg">
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="bar">
		</div>



		<section class="collapse_cate  mb-3 mt-4">
			<div class="">
				<div id="cate_cont" class="  scroll_bar_none  scroll_mouse ">
					<div class="btn-group btn-group-toggle px_16" data-toggle="buttons">
						<label class="btn btn-outline-light btn-md rounded-pill active">
							<input type="radio" name="options" id="option1" checked=""> 전체
						</label>
						<label class="btn btn-outline-light btn-md rounded-pill">
							<input type="radio" name="options" id="option2"> 카테고리
						</label>
						<label class="btn btn-outline-light btn-md rounded-pill">
							<input type="radio" name="options" id="option3"> 카테고리
						</label>
						<label class="btn btn-outline-light btn-md rounded-pill">
							<input type="radio" name="options" id="option4"> 카테고리
						</label>
						<label class="btn btn-outline-light btn-md rounded-pill">
							<input type="radio" name="options" id="option5"> 카테고리
						</label>
						<label class="btn btn-outline-light btn-md rounded-pill">
							<input type="radio" name="options" id="option6"> 카테고리
						</label>
						<label class="btn btn-outline-light btn-md rounded-pill">
							<input type="radio" name="options" id="option7"> 카테고리
						</label>
						<label class="btn btn-outline-light btn-md rounded-pill">
							<input type="radio" name="options" id="option8"> 카테고리
						</label>
					</div>
				</div>
			</div>
		</section>

		<ul class="item_list">
			<li>
				<div class="item_box  ">
					<div class="item_img flex-shrink-0">
						<div class="rect rounded">
							<img class=" " src="./img/pr_sample03.jpg" alt="상품사진">
						</div>

					</div>
					<div class="w-100">
						<p class="fw_500">(대표메뉴)해물칼국수 </p>
						<p class="tg_400 mt-2  fs_15 line2_text">간단한 설명 두줄까지</p>
						<p class="mt-3 fs_15 fw_700">20,500원</p>
					</div>
					<a class="item_link" href="./item_detail.php"></a>
				</div>
			</li>
			<li>
				<div class="item_box  ">
					<div class="item_img flex-shrink-0">
						<div class="rect rounded">
							<img class=" " src="./img/pr_sample03.jpg" alt="상품사진">
						</div>

					</div>
					<div class="w-100">
						<p class="fw_500">(대표메뉴)해물칼국수 </p>
						<p class="tg_400 mt-2  fs_15 line2_text">간단한 설명 두줄까지</p>
						<p class="mt-3 fs_15 fw_700">20,500원</p>
					</div>
					<a class="item_link" href="./item_detail.php"></a>
				</div>
			</li>
			<li>
				<div class="item_box  ">
					<div class="item_img flex-shrink-0">
						<div class="rect rounded">
							<img class=" " src="./img/pr_sample04.jpg" alt="상품사진">
						</div>

					</div>
					<div class="w-100">
						<p class="fw_500">(대표메뉴)해물칼국수(대표메뉴)해물칼국수</p>
						<p class="tg_400 mt-2  fs_15 line2_text">간단한 설명 두줄까지</p>
						<p class="mt-3 fs_15 fw_700">20,500원</p>
					</div>
					<a class="item_link" href="./item_detail.php"></a>
				</div>
			</li>
			<li>
				<div class="item_box sold_out">
					<div class="item_img flex-shrink-0 rounded overflow-hidden">
						<p class="sold_out_txt ">품절</p>
						<div class="rect ">
							<img class=" " src="./img/pr_sample02.jpg" alt="상품사진">
						</div>
					</div>
					<div class="w-100">
						<p class="fw_500">(대표메뉴)해물칼국수(대표메뉴)해물칼국수</p>
						<p class="tg_400 mt-2 fs_15 line2_text">남해 앞바다에서 당일 조업한 국내산 바지락과 디포리, 멸치 다시마 등 6가지 육수재료</p>
						<p class="mt-3 fs_15 fw_700">20,500원</p>
					</div>
					<a class="item_link" href="./item_detail.php"></a>
				</div>
			</li>

		</ul>

		<!-- 메뉴가 담기면 장바구니 버튼이 보임
		<div class="bottom_btn  ">
			<div class="form-row">
				<div class="col-12"><button type="button" class="btn btn-primary btn-block btn-lg" onclick="location.href='./cart.php'">장바구니 <span class="badge bg-white  text-primary rounded-pill ml-2">3</span></button></div>
			</div>
		</div>
-->

		<!-- 로그인 유도 버튼-->
		<div class="bottom_sheet">
			<p class="text-right login_close"><a href=""><img class="flex-shrink-0  " src="./img/login_pop_x.png" style="width:3rem"></a></p>
			<p class="text-right mt-2"><a href="./login.php" class="login_bg"><img class=" " src="./img/login_pop.png"></a></p>
		</div>
		<script>
			$('.login_close').on('click', function(e) {
				e.preventDefault();
				$('.bottom_sheet').hide();
			});
		</script>


	</div>
</div>



<!-- 로그인 유도 팝업 // 옛날버전 안예뻐서 다시 디자인했지만 혹시나해서 남겨놓음 -->
<div class="modal modal_bottom fade  " id="pop_login" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
		<div class="modal-content">

			<div class="modal-body text-center">
				<p class="tit_st3">로그인 후 회원 혜택을 받아보세요!</p>
				<p class="text-primary fs_14 mt-2">비회원도 주문 가능합니다!</p>
			</div>
			<div class="modal-footer">
				<div class="form-row">
					<div class="col-6"><button type="button" class="btn btn-light btn-block" data-dismiss="modal">비회원 주문</button></div>
					<div class="col-6"><button type="button" class="btn btn-primary btn-block" onclick="location.href='./login.php'">로그인</button></div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- 로그인 유도 팝업 스크립트 -->
<script>
	//$(document).ready(function() {
	//$('#pop_login').modal('show');
	//});
</script>


<? include_once("./inc/tail.php"); ?>