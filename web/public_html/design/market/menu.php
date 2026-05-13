<?
$_SUB_HEAD_TITLE = "메뉴관리";
$_GET['hd_pc'] = ' '; //PC hd  로그인시공백 로그아웃시 logout
$hd_num = 'menu'; //왼쪽메뉴 active 땜시 만듬
$hd_left = ' '; //왼쪽메뉴 on 땜시 만듬
include_once("./inc/head.php");
?>

<!-- 왼쪽 메뉴-->
<? include_once("./inc/left_menu.php"); ?>

<div class="sub_pg ">
	<div class="sub_wr">
		<div class="hd_tit2">
			<div class="flex-shrink-0 ml-auto">
				<button type="button" class="btn btn-outline-primary  " data-toggle="modal" data-target="#modal_menu1">카테고리 추가</button>
				<button type="button" class="btn btn-outline-primary ml-2" onclick="location.href='./menu_add.php' ">메뉴 추가</button>
			</div>
			<div class="d-flex flex-wrap align-items-center ">
				<h3 class="tit_st1 mr-5">메뉴관리</h3>
				<div class="d-flex ">
					<input type="text" class="form-control " placeholder="메뉴명 검색"> <button type="button" class="btn btn-secondary ml-2 ">검색</button>
				</div>
			</div>

		</div>

		<div class="card rounded-lg">
			<div class="">
				<div class="collapse_cate menu_tab ">
					<div id="cate_cont" class="touch_scroll scroll_bar_none flex-fill">
						<div class="btn-group btn-group-toggle px_40" data-toggle="buttons">
							<label class="btn  active">
								<input type="radio" name="options" id="option1" checked> 전체(3)
							</label>
							<label class="btn ">
								<input type="radio" name="options" id="option2"> 사이드(1)
							</label>
							<label class="btn ">
								<input type="radio" name="options" id="option3"> 카테고리
							</label>
							<label class="btn ">
								<input type="radio" name="options" id="option4"> 카테고리
							</label>
							<label class="btn ">
								<input type="radio" name="options" id="option5"> 카테고리
							</label>
							<label class="btn ">
								<input type="radio" name="options" id="option6"> 카테고리
							</label>
							<label class="btn ">
								<input type="radio" name="options" id="option7"> 카테고리
							</label>
							<label class="btn ">
								<input type="radio" name="options" id="option8"> 카테고리
							</label>
						</div>
					</div>

				</div>
			</div>
			<section class="menu_wp">
				<div class="mu_list">
					<div class="mu_hd">
						<div class="d-flex align-items-center">
							<p class="tit_st3 text-white">식사류(2)</p>
							<button type="button" class="btn   text-white flex-shrink-0  " data-toggle="modal" data-target="#modal_menu2" data-dismiss="modal"><span class="mr-2"><img src="./img/ico_edit.svg" alt=" " class="icon_w"></span>편집</button>
						</div>
						<p class="d-flex align-content-center mb-4 mb-lg-0"><span><img src="./img/img_mark2.svg" class="mr-2" alt=" "></span> 판매중이 아닐 경우 품절로 표시됩니다.</p>
					</div>
					<div class="mu_box">
						<div class="  flex-column-reverse  d-flex  flex-md-row">
							<div class="flex-fill">
								<div class="item_box  ">
									<div class="item_img flex-shrink-0">
										<div class="rect rounded">
											<img class=" " src="./img/pr_sample03.jpg" alt="상품사진">
										</div>
									</div>
									<div class="">
										<p class="fw_500 tit_st4">(대표메뉴)해물칼국수</p>
										<p class="tg_400 mt-2 line1_text">간단한 설명 두줄까지</p>
										<p class="mt-3 fw_700 tit_st3">20,500원</p>
									</div>
								</div>
							</div>
							<div class="ml-auto flex-shrink-0 d-flex mb-3 ">
								<button type="button" class="btn btn-md bg-light rounded-pill px-4 ml-3" onclick="location.href='./menu_edit.php' "><span class="mr-2"><img src="./img/ico_edit.svg" alt=" "></span> 편집</button>
								<div class="mt-3    flex-shrink-0">
									<div class="custom-control custom-switch switch-outside swh_l">
										<input type="checkbox"
											class="custom-control-input"
											id="customSwitch_mm"
											data-on="판매중"
											data-off="판매 마감">
										<span class="switch-state"></span>
										<label class="custom-control-label" for="customSwitch_mm"></label>
									</div>
								</div>
							</div>
						</div>
						<div class="mu_box_sub">
							<dl>
								<dt>맛 선택</dt>
								<dd>
									<div class="d-flex sub_op">
										<p>순한맛 </p>
										<p>약간 순한맛</p>
										<p>약간 매운맛</p>
									</div>
								</dd>
							</dl>
							<dl>
								<dt>사이즈 선택(필수)</dt>
								<dd>
									<div class="d-flex sub_op">
										<p>순한맛 <span class=" text-primary">+1,000원</span></p>
										<p>순한맛 <span class=" text-primary">+1,000원</span></p>
										<p>순한맛 <span class=" text-primary">+1,000원</span></p>
									</div>
								</dd>
							</dl>
						</div>
					</div>

					<div class="mu_box">
						<div class="  flex-column-reverse  d-flex  flex-md-row">
							<div class="flex-fill">
								<div class="item_box  ">
									<div class="item_img flex-shrink-0">
										<div class="rect rounded">
											<img class=" " src="./img/pr_sample03.jpg" alt="상품사진">
										</div>
									</div>
									<div class="">
										<p class="fw_500 tit_st4">(대표메뉴)해물칼국수 (대표메뉴)해물칼국수 (대표메뉴)해물칼국수(대표메뉴)해물칼국수(대표메뉴)해물칼국수</p>
										<p class="tg_400 mt-2 line1_text">간단한 설명 두줄까지</p>
										<p class="mt-3 fw_700 tit_st3">20,500원</p>
									</div>
								</div>
							</div>
							<div class="ml-auto flex-shrink-0 d-flex mb-3 ">
								<button type="button" class="btn btn-md bg-light rounded-pill px-4 ml-3" onclick="location.href='./menu_edit.php' "><span class="mr-2"><img src="./img/ico_edit.svg" alt=" "></span> 편집</button>
								<div class="mt-3    flex-shrink-0">
									<div class="custom-control custom-switch switch-outside swh_l">
										<input type="checkbox"
											class="custom-control-input"
											id="customSwitch_mm"
											data-on="판매중"
											data-off="판매 마감">
										<span class="switch-state"></span>
										<label class="custom-control-label" for="customSwitch_mm"></label>
									</div>
								</div>
							</div>
						</div>
						<!-- <div class="mu_box_sub">
							<dl>
								<dt>맛 선택</dt>
								<dd>
									<div class="d-flex sub_op">
										<p>순한맛 </p>
										<p>약간 순한맛</p>
										<p>약간 매운맛</p>
									</div>
								</dd>
							</dl>
							<dl>
								<dt>사이즈 선택(필수)</dt>
								<dd>
									<div class="d-flex sub_op">
										<p>순한맛 <span class=" text-primary">+1,000원</span></p>
										<p>순한맛 <span class=" text-primary">+1,000원</span></p>
										<p>순한맛 <span class=" text-primary">+1,000원</span></p>
									</div>
								</dd>
							</dl>
						</div> -->
					</div>

					<div class="mu_box">
						<div class="  flex-column-reverse  d-flex  flex-md-row">
							<div class="flex-fill">
								<div class="item_box  ">
									<!-- <div class="item_img flex-shrink-0">
										<div class="rect rounded">
											<img class=" " src="./img/pr_sample03.jpg" alt="상품사진">
										</div>
									</div> -->
									<div class="">
										<p class="fw_500 tit_st4">(대표메뉴)해물칼국수 (대표메뉴)해물칼국수 (대표메뉴)해물칼국수(대표메뉴)해물칼국수(대표메뉴)해물칼국수</p>
										<p class="tg_400 mt-2 line1_text">간단한 설명 두줄까지</p>
										<p class="mt-3 fw_700 tit_st3">20,500원</p>
									</div>
								</div>
							</div>
							<div class="ml-auto flex-shrink-0 d-flex mb-3 ">
								<button type="button" class="btn btn-md bg-light rounded-pill px-4 ml-3" onclick="location.href='./menu_edit.php' "><span class="mr-2"><img src="./img/ico_edit.svg" alt=" "></span> 편집</button>
								<div class="mt-3    flex-shrink-0">
									<div class="custom-control custom-switch switch-outside swh_l">
										<input type="checkbox"
											class="custom-control-input"
											id="customSwitch_mm"
											data-on="판매중"
											data-off="판매 마감">
										<span class="switch-state"></span>
										<label class="custom-control-label" for="customSwitch_mm"></label>
									</div>
								</div>
							</div>
						</div>
						<div class="mu_box_sub">
							<dl>
								<dt>맛 선택</dt>
								<dd>
									<div class="d-flex sub_op">
										<p>순한맛 </p>
										<p>약간 순한맛</p>
										<p>약간 매운맛</p>
									</div>
								</dd>
							</dl>
							<dl>
								<dt>사이즈 선택(필수)</dt>
								<dd>
									<div class="d-flex sub_op">
										<p>순한맛 <span class=" text-primary">+1,000원</span></p>
										<p>순한맛 <span class=" text-primary">+1,000원</span></p>
										<p>순한맛 <span class=" text-primary">+1,000원</span></p>
									</div>
								</dd>
							</dl>
						</div>
					</div>

				</div>
				<div class="mu_list">
					<div class="mu_hd">
						<div class="d-flex align-items-center">
							<p class="tit_st3 text-white">식사류(2) </p>
							<button type="button" class="btn   text-white flex-shrink-0   " data-toggle="modal" data-target="#modal_menu2" data-dismiss="modal"><span class="mr-2"><img src="./img/ico_edit.svg" alt=" " class="icon_w"></span>편집</button>
						</div>
						<p class="d-flex align-content-center mb-4 mb-lg-0"><span><img src="./img/img_mark2.svg" class="mr-2" alt=" "></span> 판매중이 아닐 경우 품절로 표시됩니다.</p>
					</div>


					<div class="mu_box">
						<div class="  flex-column-reverse  d-flex  flex-md-row">
							<div class="flex-fill">
								<div class="item_box  ">
									<div class="item_img flex-shrink-0">
										<div class="rect rounded">
											<img class=" " src="./img/pr_sample03.jpg" alt="상품사진">
										</div>
									</div>
									<div class="">
										<p class="fw_500 tit_st4">(대표메뉴)해물칼국수 (대표메뉴)해물칼국수 (대표메뉴)해물칼국수(대표메뉴)해물칼국수(대표메뉴)해물칼국수</p>
										<p class="tg_400 mt-2 line1_text">간단한 설명 두줄까지</p>
										<p class="mt-3 fw_700 tit_st3">20,500원</p>
									</div>
								</div>
							</div>
							<div class="ml-auto flex-shrink-0 d-flex mb-3 ">
								<button type="button" class="btn btn-md bg-light rounded-pill px-4 ml-3" onclick="location.href='./menu_edit.php' "><span class="mr-2"><img src="./img/ico_edit.svg" alt=" "></span> 편집</button>

								<div class="mt-3    flex-shrink-0">
									<div class="custom-control custom-switch switch-outside swh_l">
										<input type="checkbox"
											class="custom-control-input"
											id="customSwitch_mm"
											data-on="판매중"
											data-off="판매 마감">
										<span class="switch-state"></span>
										<label class="custom-control-label" for="customSwitch_mm"></label>
									</div>
								</div>
							</div>
						</div>
						<!-- <div class="mu_box_sub">
							<dl>
								<dt>맛 선택</dt>
								<dd>
									<div class="d-flex sub_op">
										<p>순한맛 </p>
										<p>약간 순한맛</p>
										<p>약간 매운맛</p>
									</div>
								</dd>
							</dl>
							<dl>
								<dt>사이즈 선택(필수)</dt>
								<dd>
									<div class="d-flex sub_op">
										<p>순한맛 <span class=" text-primary">+1,000원</span></p>
										<p>순한맛 <span class=" text-primary">+1,000원</span></p>
										<p>순한맛 <span class=" text-primary">+1,000원</span></p>
									</div>
								</dd>
							</dl>
						</div> -->
					</div>


				</div>
			</section>
		</div>
	</div>

</div>








<!-- data-toggle="modal" data-target="#modal_menu1" F-2 메뉴카테고리 추가(모달) -->
<div class="modal modal_rr fade" id="modal_menu1" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog ">
		<div class="modal-content">
			<button type="button" class="close1" data-dismiss="modal" aria-label="Close"><img src="./img/ic_close.png" alt="닫기"></button>
			<div class="modal-body">

				<div class=" detail_hd mt-4">
					<h2 class="tit_st1 d-flex align-items-center"> <span>메뉴 카테고리 추가</span></h2>
					<div class="custom-control custom-switch switch-outside swh_l">
						<input type="checkbox"
							class="custom-control-input"
							id="customSwitch_mm1"
							data-on="사용"
							data-off="사용안함">
						<span class="switch-state"></span>
						<label class="custom-control-label" for="customSwitch_mm1"></label>
					</div>
				</div>
				<section class="py-5 border-top border-dark">
					<div class="row">
						<div class="col-4 fw_600 mb-4 d-flex align-items-center">
							카테고리명
						</div>
						<div class="col-8 mb-4">
							<input type="text" class="form-control" placeholder="예)메인 메뉴,추천 메뉴,대표 메뉴">
						</div>
						<div class="col-4 fw_600 mb-4 d-flex align-items-center">
							정렬 순서
						</div>
						<div class="col-8 mb-4">
							<input type="text" class="form-control" placeholder="1">
						</div>


					</div>
					<div><button type="button" class="btn btn-primary btn-lg btn-block mt-5">카테고리 추가</button></div>

				</section>
			</div>

		</div>
	</div>
</div>

<!-- data-toggle="modal" data-target="#modal_menu1" F-2 메뉴카테고리 편집(모달) -->
<div class="modal modal_rr fade" id="modal_menu2" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog ">
		<div class="modal-content">
			<button type="button" class="close1" data-dismiss="modal" aria-label="Close"><img src="./img/ic_close.png" alt="닫기"></button>
			<div class="modal-body">

				<div class=" detail_hd mt-4">
					<h2 class="tit_st1 d-flex align-items-center"> <span>메뉴 카테고리 편집</span></h2>
					<div class="custom-control custom-switch switch-outside swh_l">
						<input type="checkbox"
							class="custom-control-input"
							id="customSwitch_mm1"
							data-on="사용"
							data-off="사용안함">
						<span class="switch-state"></span>
						<label class="custom-control-label" for="customSwitch_mm1"></label>
					</div>
				</div>
				<section class="py-5 border-top border-dark">
					<div class="row">
						<div class="col-4 fw_600 mb-4 d-flex align-items-center">
							카테고리명
						</div>
						<div class="col-8 mb-4">
							<input type="text" class="form-control" placeholder="예)메인 메뉴,추천 메뉴,대표 메뉴" value="메인메뉴">
						</div>
						<div class="col-4 fw_600 mb-4 d-flex align-items-center">
							정렬 순서
						</div>
						<div class="col-8 mb-4">
							<input type="text" class="form-control" placeholder="1" value="2">
						</div>


					</div>
					<div class="form-row mt-5">

						<div class="col-6"><button type="button" class="btn btn-light btn-lg btn-block ">삭제</button></div>
						<div class="col-6"><button type="button" class="btn  btn-primary btn-lg btn-block ">수정 완료</button></div>
					</div>


				</section>
			</div>

		</div>
	</div>
</div>


<? include_once("./inc/tail.php"); ?>