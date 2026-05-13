<?
$_SUB_HEAD_TITLE = "폼";
$_GET['hd_pc'] = '1'; //PC hd 메뉴있음1, 메뉴없음 공백
$_GET['hd_num'] = ' '; //모바일 hd 1~n까지 있음
$_GET['bt_menu'] = ' '; //모바일 하단메뉴 있음1, ft 없음 공백
$_GET['ft_none'] = ' '; //모바일 ft 있음1, ft 없음 공백
include_once("./inc/head.php");
?>



<? include_once("./inc/modal.php"); ?>

<div class="wrap">
	<div class="sub_pg">
		<div class="container position-relative">

			<h1 class="tit_st1 mb-5">폼 스타일</h1>
			<h1 id="" class="guide_pg mb-3"><span class="bg-primary d-block py-3 px-3 text-white fs_17">▼ 팝업</span></h1>
			<button type="button" class="btn btn-sm btn-outline-primary m-1" data-toggle="modal" data-target="#modal_sm_1">modal-dialog-sm</button>
			<button type="button" class="btn btn-sm btn-outline-primary m-1" data-toggle="modal" data-target="#modal_sm_2">modal-dialog-sm 버튼 2열</button>
			<button type="button" class="btn btn-sm btn-outline-primary m-1" data-toggle="modal" data-target="#modal_md_1">modal-dialog-md</button>
			<button type="button" class="btn btn-sm btn-outline-primary m-1" data-toggle="modal" data-target="#modal_md_2">modal-dialog-md 버튼 2열</button>
			<button type="button" class="btn btn-sm btn-outline-primary m-1" data-toggle="modal" data-target="#modal-default">modal-default</button>
			<button type="button" class="btn btn-sm btn-outline-primary m-1" data-toggle="modal" data-target="#modal_bottom">.modal_bottom.modal 하단버튼</button>
			<button type="button" class="btn btn-sm btn-outline-primary m-1" data-toggle="modal" data-target="#modal_full">.modal_full.modal 하단버튼</button>
			<button type="button" class="btn btn-sm btn-outline-primary m-1" data-toggle="modal" data-target="#modal_rr">.modal_rr.modal 오른쪽 전체로 열리는 모달</button>


			<h1 id="" class="guide_pg mt-5 mb-3"><span class="bg-primary d-block py-3 px-3 text-white fs_17">▼ 백그라운드 컬러</span></h1>
			<ul class="sq_guide mb-5">
				<li class="border bg-primary text-white">primary</li>
				<li class="border bg-secondary text-white">secondary</li>
				<li class="border bg-success text-white">success</li>
				<li class="border bg-danger text-white">danger</li>
				<li class="border bg-warning text-dark">warning</li>
				<li class="border bg-info text-white">info</li>
				<li class="border bg-light text-dark">light</li>
				<li class="border bg-dark text-white">dark</li>
				<li class="border bg-white text-dark">white</li>
				<li class="border bg-transparent text-dark">transparent</li>
			</ul>

			<h1 id="" class="guide_pg mb-3"><span class="bg-primary d-block py-3 px-3 text-white fs_17">▼ 보더 컬러</span></h1>
			<ul class="sq_guide mb-5">
				<li class="border bg-white text-dark border-primary">primary</li>
				<li class="border bg-white text-dark border-secondary">secondary</li>
				<li class="border bg-white text-dark border-success">success</li>
				<li class="border bg-white text-dark border-danger">danger</li>
				<li class="border bg-white text-dark border-warning">warning</li>
				<li class="border bg-white text-dark border-info">info</li>
				<li class="border bg-dark text-white border-light">light</li>
				<li class="border bg-white text-dark border-dark">dark</li>
				<li class="border bg-dark text-white border-white">white</li>
			</ul>

			<h1 id="" class="guide_pg mb-3"><span class="bg-primary d-block py-3 px-3 text-white fs_17">▼ 폰트 컬러</span></h1>
			<div class="fs_20">
				<p class="mb-3 text-primary">text-primary <span class="float-right">#EA4248</span></p>
				<p class="mb-3 text-secondary bg-white">text-secondary <span class="float-right">#000000</span></p>
				<p class="mb-3 text-success">text-success <span class="float-right">#2273D1</span></p>
				<p class="mb-3 text-danger">text-danger <span class="float-right">#fa5028</span></p>
				<p class="mb-3 text-warning">text-warning <span class="float-right"></span></p>
				<p class="mb-3 text-info">text-info <span class="float-right"></span></p>
				<p class="mb-3 text-gray">text-gray <span class="float-right">#999</span></p>
				<p class="mb-3 text-body">text-body <span class="float-right">#000</span></p>
				<p class="mb-3 text-black">text-black <span class="float-right">#000</span></p>
				<p class="mb-3 text-muted bg-white">text-muted <span class="float-right">#6C709D</span></p>
				<p class="mb-3 text-white bg-dark">text-white <span class="float-right"></span></p>
				<p class="mb-3 text-black-50">text-black-50 <span class="float-right"></span></p>
				<p class="mb-3 text-white-50 bg-dark">text-white-50 <span class="float-right"></span></p>
			</div>


			<h1 id="" class="guide_pg mb-3"><span class="bg-primary d-block py-3 px-3 text-white fs_17">▼ 타이틀</span></h1>
			<h1 class="mb-3 mt-5 fs_16">▼ 타이틀 스타일1</h1>
			<div class="tit_st1">1. 타이틀용서체 Pretendard-Bold 40px</div>
			<h1 class="mb-3 mt-5 fs_16">▼ 타이틀 스타일2</h1>
			<div class="tit_st2">2. 타이틀용서체 Pretendard-Bold 30px -> 20px</div>
			<h1 class="mb-3 mt-5 fs_16">▼ 타이틀 스타일3</h1>
			<div class="tit_st3">2. 타이틀용서체 Pretendard-Bold 25px -> 20px</div>
			<h1 class="mb-3 mt-5 fs_16">▼ 타이틀 스타일4</h1>
			<div class="tit_st4">3. Pretendard-Bold 19px</div>
			<h1 class="mb-3 mt-5 fs_16">▼ 타이틀 스타일5</h1>
			<div class="tit_st5">4. Pretendard-Bold 16px</div>
			<h1 class="mb-3 mt-5 fs_16">▼ 타이틀 스타일6</h1>
			<div class="tit_st6">5. Pretendard-Bold 15px</div>


			<div class="py-4"></div>


			<h1 id="guide_pg2" class="guide_pg mb-3"><span class="bg-primary d-block py-3 px-3 text-white fs_17">▼ 폰트</span></h1>
			<h1 class="mb-3 mt-3 fs_16">▼ 폰트 사이즈</h1>
			<div class="px-2">
				<div class="fs_8">fs_8</div>
				<div class="fs_9">fs_9</div>
				<div class="fs_10">fs_10</div>
				<div class="fs_11">fs_11</div>
				<div class="fs_17">~</div>
				<div class="fs_32">fs_52</div>
			</div>
			<h1 class="mb-3 mt-3 fs_16">▼ 폰트 굵기</h1>
			<div class="px-2 py-2">
				<div class="fw_100">fw_100 Thin</div>
				<div class="fw_200">fw_200 ExtraLight</div>
				<div class="fw_300">fw_300 Light</div>
				<div class="fw_400">fw_400 Regular</div>
				<div class="fw_500">fw_500 Medium</div>
				<div class="fw_600">fw_600 SemiBold</div>
				<div class="fw_700">fw_700 Bold</div>
				<div class="fw_800">fw_800 ExtraBold</div>
				<div class="fw_900">fw_900 Black</div>
			</div>



			<h1 id="guide_pg3" class="guide_pg mb-3 mt-5"><span class="bg-primary d-block py-3 px-3 text-white fs_17">▼ 버튼</span></h1>

			<h1 class="mb-0 mt-3 fs_16">▼ 기타</h1>
			<button type="button" class="btn btn-link btn-sm"><span class="text-gray mr-1">전체보기</span><img src="./img/ic_more.png" style="width:1.8rem;"></button>
			<button type="button" class="btn btn-outline-light btn-sm pl-2 pr-1" style="height:2.4rem;">쿠폰받기<img src="./img/ic_more.png" style="width:1.8rem;"></button>

			<div class="item_opt_counter">
				<button type="button" class="btn item_opt_counter_btn pl-1" disabled><!-- 수량이 0일때 -->
					<img src="./img/ico_decrease.svg" alt="감소">
				</button>
				<input type="text" class="quantity" value="0">
				<button type="button" class="btn item_opt_counter_btn pr-1">
					<img src="./img/ico_increase.svg" alt="증가">
				</button>
			</div>


			<select class="custom-select custom-select_st2">
				<option selected>옵션 선택</option>
				<option value="1">One</option>
				<option value="2">Two</option>
				<option value="3">Three</option>
			</select>



			<h1 class="mb-0 mt-3 fs_16">▼ 버튼 btn-sm</h1>
			<div class="py-3">
				<button type="button" class="btn btn-primary btn-sm">버튼</button>
				<button type="button" class="btn btn-outline-primary btn-sm">버튼</button>
				<button type="button" class="btn btn-secondary btn-sm">버튼</button>
				<button type="button" class="btn btn-outline-secondary btn-sm">버튼</button>
				<button type="button" class="btn btn-light btn-sm">버튼</button>
				<button type="button" class="btn btn-outline-light btn-sm">버튼</button>
			</div>

			<h1 class="mb-0 mt-3 fs_16">▼ 버튼 disabled</h1>
			<div class="py-3">
				<button type="button" class="btn btn-primary btn-sm" disabled>버튼</button>
				<button type="button" class="btn btn-outline-primary btn-sm" disabled>버튼</button>
				<button type="button" class="btn btn-secondary btn-sm" disabled>버튼</button>
				<button type="button" class="btn btn-outline-secondary btn-sm" disabled>버튼</button>
				<button type="button" class="btn btn-light btn-sm" disabled>버튼</button>
				<button type="button" class="btn btn-outline-light btn-sm" disabled>버튼</button>
			</div>

			<h1 class="mb-0 mt-3 fs_16">▼ 버튼 md</h1>
			<div class="py-3">
				<button type="button" class="btn btn-primary btn-md">버튼</button>
				<button type="button" class="btn btn-outline-primary btn-md">버튼</button>
				<button type="button" class="btn btn-secondary btn-md">버튼</button>
				<button type="button" class="btn btn-outline-secondary btn-md">버튼</button>
				<button type="button" class="btn btn-light btn-md">버튼</button>
				<button type="button" class="btn btn-outline-light btn-md">버튼</button>
			</div>

			<h1 class="mb-0 mt-3 fs_16">▼ 버튼 default</h1>
			<div class="py-3">
				<button type="button" class="btn btn-primary">Primary</button>
				<button type="button" class="btn btn-outline-primary">버튼</button>
				<button type="button" class="btn btn-secondary">Secondary</button>
				<button type="button" class="btn btn-outline-secondary">버튼</button>
				<button type="button" class="btn btn-light">light</button>
				<button type="button" class="btn btn-outline-light">버튼</button>
				<button type="button" class="btn btn-dark">dark</button>
				<button type="button" class="btn btn-dark-light">버튼</button>
			</div>

			<h1 class="mb-0 mt-3 fs_16">▼ 버튼 2열</h1>
			<div class="py-3">
				<div class="form-row">
					<div class="col-3"><button type="button" class="btn btn-outline-light btn-block">버튼</button></div>
					<div class="col-9"><button type="button" class="btn btn-primary btn-block">버튼</button></div>
				</div>
			</div>

			<h1 class="mb-0 mt-3 fs_16">▼ 버튼 btn-lg block</h1>
			<div class="py-3">
				<button type="button" class="btn btn-primary btn-lg btn-block">버튼</button>
				<button type="button" class="btn btn-outline-primary btn-lg btn-block">버튼</button>
				<button type="button" class="btn btn-secondary btn-lg btn-block">버튼</button>
				<button type="button" class="btn btn-outline-secondary btn-lg btn-block">버튼</button>
				<button type="button" class="btn btn-light btn-lg btn-block">버튼</button>
				<button type="button" class="btn btn-outline-light btn-lg btn-block">버튼</button>
			</div>

			<h1 class="mb-4 mt-5 fs_16">▼ 버튼 group</h1>
			<div class="btn-group" role="group" aria-label="First group">
				<button type="button" class="btn btn-primary">버튼1</button>
				<button type="button" class="btn btn-primary">버튼2</button>
				<button type="button" class="btn btn-primary">버튼3</button>
				<button type="button" class="btn btn-primary">버튼4</button>
			</div>


			<div class="my-5">
				<div class="btn-group btn_toggle_primary">
					<button class="btn btn-outline-light active">버튼1</button>
					<button class="btn btn-outline-light">버튼2</button>
					<button class="btn btn-outline-light">버튼3</button>
					<button class="btn btn-outline-light">버튼4</button>
				</div>
			</div>


			<h1 class="mb-4 mt-5 fs_16">▼ 버튼 group radio</h1>
			<div class="btn-group btn-group-toggle" data-toggle="buttons">
				<label class="btn btn-outline-light btn-md rounded-pill active">
					<input type="radio" name="options" id="option1" checked> Radio
				</label>
				<label class="btn btn-outline-light btn-md rounded-pill">
					<input type="radio" name="options" id="option2"> Radio
				</label>
				<label class="btn btn-outline-light btn-md rounded-pill">
					<input type="radio" name="options" id="option3"> Radio
				</label>
			</div>


			<div class="my-5">
				<div class="btn-group btn-group-toggle btn_toggle_primary" data-toggle="buttons">
					<label class="btn btn-outline-light btn-md active">
						<input type="radio" name="options" id="option1" checked=""> Radio
					</label>
					<label class="btn btn-outline-light btn-md">
						<input type="radio" name="options" id="option2"> Radio
					</label>
					<label class="btn btn-outline-light btn-md">
						<input type="radio" name="options" id="option3"> Radio
					</label>
				</div>
			</div>


			<h1 class="mb-4 mt-5 fs_16">▼ 버튼 group checkbox</h1>
			<div class="btn-group btn-group-toggle" data-toggle="buttons">
				<label class="btn btn-outline-light btn-md rounded-pill active">
					<input type="checkbox" name="options" id="option1" checked> checkbox
				</label>
				<label class="btn btn-outline-light btn-md rounded-pill">
					<input type="checkbox" name="options" id="option2"> checkbox
				</label>
			</div>




			<h1 class="mb-4 mt-5 fs_16">▼ 버튼 슬라이드</h1>
			<!-- 카테고리 -->
			<div class="collapse_cate mb_20">
				<div class="mb_14">
					<div id="cate_cont" class="touch_scroll scroll_bar_none flex-fill">
						<div class="btn-group btn-group-toggle px_20" data-toggle="buttons">
							<label class="btn btn-outline-light btn-md rounded-pill active">
								<input type="radio" name="options" id="option1" checked> 전체
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
			</div>


			<h1 class="mb-4 mt-5 fs_16">▼ 스위치 토글버튼</h1>
			<div class="switch_flex">
				<div class="switch_tit mb-3">검색어 저장</div>
				<div class="custom-switch">
					<input type="checkbox" class="custom-control-input" id="search_switch">
					<label class="custom-control-label" for="search_switch"></label>
				</div>



				<div class="custom-control custom-switch switch-outside">
					<input type="checkbox"
						class="custom-control-input"
						id="customSwitch1"
						data-on="ON"
						data-off="OFF">
					<label class="custom-control-label" for="customSwitch1"></label>
					<span class="switch-state"></span>
				</div>

				<div class="custom-control custom-switch switch-outside">
					<input type="checkbox"
						class="custom-control-input"
						id="customSwitch2"
						data-on="사용"
						data-off="미사용"
						checked>
					<label class="custom-control-label" for="customSwitch2"></label>
					<span class="switch-state"></span>
				</div>




			</div>



			<h1 class="mb-4 mt-5 fs_16">▼ 탭메뉴01</h1>
			<nav class="mb_20">
				<div class="nav nav-tabs row mx-0" id="nav-tab" role="tablist">
					<button class="col nav-link active" id="tab01_tab1" data-toggle="tab" data-target="#tab01_1" type="button" role="tab" aria-selected="true">탭01_1</button>
					<button class="col nav-link" id="tab01_tab2" data-toggle="tab" data-target="#tab01_2" type="button" role="tab" aria-selected="false">탭01_2</button>
					<button class="col nav-link" id="tab01_tab3" data-toggle="tab" data-target="#tab01_3" type="button" role="tab" aria-selected="false">탭01_3</button>
					<button class="col nav-link" id="tab01_tab4" data-toggle="tab" data-target="#tab01_4" type="button" role="tab" aria-selected="false">탭01_4</button>
				</div>
			</nav>

			<div class="tab-content" id="nav_Content01">
				<div class="tab-pane fade show active" id="tab01_1">탭01_1</div>
				<div class="tab-pane fade" id="tab01_2">탭01_2</div>
				<div class="tab-pane fade" id="tab01_3">탭01_3</div>
				<div class="tab-pane fade" id="tab01_4">탭01_4</div>
			</div>



			<h1 class="mb-4 mt-5 fs_16">▼ 탭메뉴02</h1>
			<nav class="mb_20">
				<ul class="nav nav-pills row" id="nav-tab" role="tablist">
					<li class="col">
						<button class="nav-link btn-lg btn-block active" id="tab02_tab1" data-toggle="tab" data-target="#tab02_1" type="button" role="tab" aria-selected="true">탭02_1</button>
					</li>
					<li class="col">
						<button class="nav-link btn-lg btn-block" id="tab02_tab2" data-toggle="tab" data-target="#tab02_2" type="button" role="tab" aria-selected="false">탭02_2</button>
					</li>
					<li class="col">
						<button class="nav-link btn-lg btn-block" id="tab02_tab3" data-toggle="tab" data-target="#tab02_3" type="button" role="tab" aria-selected="false">탭02_3</button>
					</li>
					<li class="col">
						<button class="nav-link btn-lg btn-block" id="tab02_tab4" data-toggle="tab" data-target="#tab02_4" type="button" role="tab" aria-selected="false">탭02_4</button>
					</li>
				</ul>
			</nav>
			<div class="tab-content" id="nav_Content02">
				<div class="tab-pane fade show active" id="tab02_1">탭02_1</div>
				<div class="tab-pane fade" id="tab02_2">탭02_2</div>
				<div class="tab-pane fade" id="tab02_3">탭02_3</div>
				<div class="tab-pane fade" id="tab02_4">탭02_4</div>
			</div>



			<h1 class="mb-4 mt-5 fs_16">▼ 탭메뉴03</h1>
			<nav class="mb_20">
				<ul class="nav nav_tab_line" id="nav-tab" role="tablist">
					<li class="nav-item">
						<button class="nav-link active" id="tab03_tab1" data-toggle="tab" data-target="#tab03_1" type="button" role="tab" aria-selected="true">탭03_1</button>
					</li>
					<li class="nav-item">
						<button class="nav-link" id="tab03_tab2" data-toggle="tab" data-target="#tab03_2" type="button" role="tab" aria-selected="false">탭03_2</button>
					</li>
					<li class="nav-item">
						<button class="nav-link" id="tab03_tab3" data-toggle="tab" data-target="#tab03_3" type="button" role="tab" aria-selected="false">탭03_3</button>
					</li>
				</ul>
			</nav>


			<div class="tab-content" id="nav_Content02">
				<div class="tab-pane fade show active" id="tab03_1">탭03_1</div>
				<div class="tab-pane fade" id="tab03_2">탭03_2</div>
				<div class="tab-pane fade" id="tab03_3">탭03_3</div>
			</div>







			<h1 id="guide_pg3" class="guide_pg mb-3 mt-5"><span class="bg-primary d-block py-3 px-3 text-white fs_17">▼ 입력폼</span></h1>
			<h1 class="mb-3 mt-3 fs_16">▼ 기본</h1>
			<div class="">


				<div class="form_wr  mt-5 ip_valid">
					<div class="ip_tit required">
						<h5>아이디</h5>
					</div>
					<input type="text" class="form-control" placeholder="입력해주세요.">
					<div class="form-text ip_valid">확인되었습니다.</div>
					<div class="form-text ip_invalid">아이디를 다시 확인해주세요</div>
				</div>

				<div class="form_wr  mt-5 ip_invalid">
					<div class="ip_tit">
						<h5>아이디</h5>
					</div>
					<input type="text" class="form-control" placeholder="입력해주세요.">
					<div class="form-text ip_valid">확인되었습니다.</div>
					<div class="form-text ip_invalid">아이디를 다시 확인해주세요</div>
				</div>

				<div class="form-row">
					<div class="form_wr  mt-5 col-md-6">
						<div class="ip_tit">
							<h5>아이디</h5>
						</div>
						<input type="text" class="form-control" placeholder="입력하세요">
					</div>
					<div class="form_wr  mt-5 col-md-6">
						<div class="ip_tit">
							<h5>아이디</h5>
						</div>
						<input type="text" class="form-control" placeholder="0">
					</div>
				</div>

				<div class="form-row">
					<div class="form_wr  mt-5 col-6 col-md-3">
						<div class="ip_tit">
							<h5>아이디</h5>
						</div>
						<input type="text" class="form-control" placeholder="입력하세요">
					</div>
					<div class="form_wr  mt-5 col-6 col-md-3">
						<div class="ip_tit">
							<h5>아이디</h5>
						</div>
						<input type="text" class="form-control" placeholder="0">
					</div>
					<div class="form_wr  mt-5 col-md-6">
						<div class="ip_tit">
							<h5>아이디</h5>
						</div>
						<input type="text" class="form-control" placeholder="0">
					</div>
				</div>

				<div class="form_wr  mt-5">
					<div class="ip_tit d-flex align-items-center justify-content-between">
						후기를 작성해주세요
						<p class="fc_mgr fs_14">(0/1000)</p>
					</div>
					<textarea class="form-control" placeholder="입력해주세요" rows="5"></textarea>
					<div class="invalid-feedback">1000자까지만 써주세요</div>
				</div>

				<div class="form_wr  mt-5">
					<div class="ip_tit">
						<h5>아이디</h5>
					</div>
					<select class="form-control custom-select">
						<option selected>선택하기</option>
						<option value="1">One</option>
						<option value="2">Two</option>
						<option value="3">Three</option>
					</select>
				</div>





				<div class="form_wr  mt-5">
					<div class="ip_tit d-flex align-items-center justify-content-between">
						날짜 선택
					</div>
					<input type="date" class="form-control">
				</div>
				<div class="form_wr  mt-5">
					<div class="ip_tit d-flex align-items-center justify-content-between">
						시간 선택
					</div>
					<input type="time" class="form-control">
				</div>

				<div class="form_wr  mt-5">
					<div class="ip_tit">
						전화번호 인증
					</div>
					<div class="form-row">
						<div class="col-9 col-lg-10">
							<input type="text" class="form-control" placeholder="입력하세요">
						</div>
						<div class="col-3 col-lg-2">
							<button type="button" class="btn btn-outline-light btn-block">인증요청</button>
						</div>
					</div>
				</div>



				<div class="form_wr  mt-5">
					<div class="ip_tit">
						<h5>이미지업로드</h5>
					</div>
					<div class="touch_scroll scroll_bar_none">
						<div class="d-flex">
							<div class="image_upload">
								<input id="ip_file" type="file" class="d-none">
								<label for="ip_file" class="upload_box">
									<div class="rect">
									</div>
									<p class="max_img">사진 1/3</p>
								</label>
								<button type="button" class="btn upload_del"><img src="./img/img_del.png"></button>
							</div>
							<div class="image_upload on">
								<input id="ip_file" type="file" class="d-none">
								<label for="ip_file" class="upload_box">
									<div class="rect">
										<img src="./img/pr_sample01.jpg">
									</div>
								</label>
								<button type="button" class="btn upload_del"><img src="./img/img_del.png"></button>
							</div>
							<div class="image_upload on">
								<input id="ip_file" type="file" class="d-none">
								<label for="ip_file" class="upload_box">
									<div class="rect">
										<img src="./img/pr_sample02.jpg">
									</div>
								</label>
								<button type="button" class="btn upload_del"><img src="./img/img_del.png"></button>
							</div>
							<div class="image_upload on">
								<input id="ip_file" type="file" class="d-none">
								<label for="ip_file" class="upload_box">
									<div class="rect">
										<img src="./img/pr_sample03.jpg">
									</div>
								</label>
								<button type="button" class="btn upload_del"><img src="./img/img_del.png"></button>
							</div>
							<div class="image_upload on">
								<input id="ip_file" type="file" class="d-none">
								<label for="ip_file" class="upload_box">
									<div class="rect">
										<img src="./img/pr_sample04.jpg">
									</div>
								</label>
								<button type="button" class="btn upload_del"><img src="./img/img_del.png"></button>
							</div>
							<div class="image_upload on">
								<input id="ip_file" type="file" class="d-none">
								<label for="ip_file" class="upload_box">
									<div class="rect">
										<img src="./img/pr_sample05.jpg">
									</div>
								</label>
								<button type="button" class="btn upload_del"><img src="./img/img_del.png"></button>
							</div>
						</div>
					</div>
					<div class="fs_13 mt-3">*1000px x 1000px (파일확장자 PNG로 등록)</div>
				</div>



				<div class="form_wr  mt_20">
					<div class="ip_tit">
						<h5>이미지 첨부</h5>
					</div>

					<div class="touch_scroll scroll_bar_none mb_16">
						<div class="d-flex">
							<div class="image_upload on">
								<input id="ip_file" type="file" class="d-none">
								<label for="ip_file" class="upload_box">
									<div class="rect">
										<img src="./img/pr_sample01.jpg">
									</div>
								</label>
								<button type="button" class="btn upload_del"><img src="./img/img_del.png"></button>
							</div>
							<div class="image_upload on">
								<input id="ip_file" type="file" class="d-none">
								<label for="ip_file" class="upload_box">
									<div class="rect">
										<img src="./img/pr_sample02.jpg">
									</div>
								</label>
								<button type="button" class="btn upload_del"><img src="./img/img_del.png"></button>
							</div>
							<div class="image_upload on">
								<input id="ip_file" type="file" class="d-none">
								<label for="ip_file" class="upload_box">
									<div class="rect">
										<img src="./img/pr_sample03.jpg">
									</div>
								</label>
								<button type="button" class="btn upload_del"><img src="./img/img_del.png"></button>
							</div>
							<div class="image_upload on">
								<input id="ip_file" type="file" class="d-none">
								<label for="ip_file" class="upload_box">
									<div class="rect">
										<img src="./img/pr_sample04.jpg">
									</div>
								</label>
								<button type="button" class="btn upload_del"><img src="./img/img_del.png"></button>
							</div>
							<div class="image_upload on">
								<input id="ip_file" type="file" class="d-none">
								<label for="ip_file" class="upload_box">
									<div class="rect">
										<img src="./img/pr_sample05.jpg">
									</div>
								</label>
								<button type="button" class="btn upload_del"><img src="./img/img_del.png"></button>
							</div>
						</div>
					</div>
					<button type="button" class="btn btn-light btn-block">
						<img class="mr-2" src="./img/file_up.png" style="width:2.4rem;"><span class="text-black6 fs_14">이미지 업로드</span>
					</button>
				</div>


			</div>

			<h1 class="guide_pg mb-3 mt-5"><span class="bg-primary d-block py-3 px-3 text-white fs_17">▼ select 박스</span></h1>

			<div class="custom-sel">
				<button type="button" class="select-trigger">
					옵션 선택
				</button>

				<ul class="select-options">
					<li data-value="1">옵션 1</li>
					<li data-value="2">옵션 2</li>
					<li class="is-disabled" data-value="3">옵션 3 (선택불가)</li>
					<li data-value="4">옵션 4</li>
					<li data-value="5">옵션 5</li>
					<li data-value="6">옵션 6</li>
					<li data-value="7">옵션 7</li>
					<li data-value="8">옵션 8</li>
				</ul>

				<input type="hidden" name="option">
			</div>




			<h1 id="guide_pg3" class="guide_pg mb-3 mt-5"><span class="bg-primary d-block py-3 px-3 text-white fs_17">▼ 체크박스 / 라디오 버튼</span></h1>
			<h1 class="mb-3 mt-5 fs_16">▼ 체크박스 / 라디오 버튼</h1>

			<div class="form_wr  mt-5">
				<div class="ip_tit">
					<h5>체크박스1</h5>
				</div>
				<div class="checks_wr">
					<div class="checks">
						<label>
							<input type="checkbox" name="chk1" checked>
							<span class="ic_box"></span>
							<div class="chk_p">
								<p>체크박스1</p>
							</div>
						</label>
					</div>
				</div>
			</div>

			<div class="form_wr  mt-5">
				<div class="ip_tit">
					<h5>체크박스2</h5>
				</div>
				<div class="checks_wr">
					<div class="checks">
						<label class="chk_right">
							<input type="checkbox" name="chk2">
							<span class="ic_box"></span>
							<div class="chk_p">
								<p>체크박스1</p>
							</div>
						</label>
					</div>
				</div>
			</div>

			<div class="form_wr  mt-5">
				<div class="ip_tit">
					<h5>라디오1</h5>
				</div>
				<div class="radios_wr">
					<div class="radios">
						<label>
							<input type="radio" name="rd1">
							<span class="ic_box"></span>
							<div class="chk_p">
								<p>라디오1_1</p>
							</div>
						</label>
					</div>
					<div class="radios">
						<label>
							<input type="radio" name="rd1">
							<span class="ic_box"></span>
							<div class="chk_p">
								<p>라디오1_2</p>
							</div>
						</label>
					</div>
				</div>
			</div>

			<div class="form_wr  mt-5">
				<div class="ip_tit">
					<h5>라디오2</h5>
				</div>
				<div class="radios_wr">
					<div class="radios">
						<label class="chk_right">
							<input type="radio" name="rd2">
							<span class="ic_box"><i class="ri-check-line"></i></span>
							<div class="chk_p">
								<p>라디오1_1</p>
							</div>
						</label>
					</div>
					<div class="radios">
						<label class="chk_right">
							<input type="radio" name="rd2">
							<span class="ic_box"><i class="ri-check-line"></i></span>
							<div class="chk_p">
								<p>라디오1_2</p>
							</div>
						</label>
					</div>
				</div>
			</div>


			<h1 class="guide_pg mb-3 mt-5"><span class="bg-primary d-block py-3 px-3 text-white fs_17">▼ 테이블 style(넓이 작은 테이블은 class table_scroll 빼기)</span></h1>
			<div class="table_scroll">
				<table class="table_01" summary=" ">
					<caption>
						수시 일정
					</caption>
					<colgroup>
						<col width="15%">
						<col width="25%">
						<col width="30%">
						<col width="35%">
					</colgroup>
					<thead>
						<tr>
							<!-- <th class="backslash fs_13">
                                <div>제목1</div>제목2
                            </th> -->
							<th>제목</th>
							<th>제목</th>
							<th>제목</th>
							<th>제목</th>
							<!-- <th class="slash fs_13">
                                제목1<div>제목2</div>
                            </th> -->
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="text-left">왼쪽정렬</td>
							<td class="text-right">오른쪽정렬</td>
							<td>내용</td>
							<td>내용</td>
						</tr>
						<tr>
							<td>내용</td>
							<td>내용</td>
							<td>내용</td>
							<td>내용</td>
						</tr>

					</tbody>
				</table>
			</div>

			<h1 id="guide_pg3" class="guide_pg mb-3 mt-5"><span class="bg-primary d-block py-3 px-3 text-white fs_17">▼ 페이지 네이션</span></h1>
			<h1 class="mb-3 mt-5 fs_16">▼ 페이지 네이션</h1>
			<div class="my-5">
				<ul class="pagination fs_16">
					<li class=""><a href="#" class="disabled arrow"><img src="./img/pg_prev_prev.svg"></a></li>
					<li class=""><a href="#" class="disabled arrow"><img src="./img/pg_prev.svg"></a></li>
					<li class=""><a href="#" class="on">1</a></li>
					<li class=""><a href="#">2</a></li>
					<li class=""><a href="#">3</a></li>
					<li class=""><a href="#">4</a></li>
					<li class=""><a href="#">5</a></li>
					<li class=""><a href="#" class="arrow"><img src="./img/pg_next.svg"></a></li>
					<li class=""><a href="#" class="arrow"><img src="./img/pg_next_next.svg"></a></li>
				</ul>
			</div>

			<h1 class="mb-3 mt-5 fs_16">▼ 페이저</h1>
			<article class="pager">
				<button class="btn p-0 d-flex align-items-center"><i class="xi-long-arrow-left fs_20"></i></button>
				<p class="fs_18 mx-4"><span class="text-primary">1</span> / <span>12</span></p>
				<button class="btn p-0 d-flex align-items-center"><i class="xi-long-arrow-right fs_20"></i></button>
			</article>


			<h1 class="mb-3 mt-5 fs_16">▼ 노데이터</h1>
			<!-- 감싸는 div 높이 style로 지정 -->
			<div class="no_data  ">
				<img src="./img/img_mark.svg">
				<p class=" tg_500 line_h1_4 mt-3">준비중입니다.</p>
			</div>
			<!-- 데이터가 없을경우 d-flex를 d-none로 변경 -->



			<h1 class="mb-3 mt-5 fs_16">▼ Collapse 타이틀 밑에 컨텐츠 붙을때 <span class="text-primary">열리고 닫히는거 기본 구조와 예시일뿐 모양은 만들어서 쓰세요 </span></h1>
			<div id="collapse_wr" class="collapse_ex">
				<ul>
					<li id="collapse_hd01">
						<button type="button" class="btn btn-link btn-sm" data-toggle="collapse" data-target="#collapse01" aria-expanded="false" aria-controls="collapse01">
							<span class="">collapse_hd01</span>
							<img src="./img/ic_open.png" style="width:2.0rem;">
						</button>
						<div id="collapse01" class="collapse " aria-labelledby="collapse01" aria-labelledby="collapse_hd01" data-parent="#collapse_wr">
							내용1
						</div>
					</li>
					<li id="collapse_hd02">
						<button type="button" class="btn btn-link btn-sm" data-toggle="collapse" data-target="#collapse02" aria-expanded="false" aria-controls="collapse02">
							<span class="">collapse_hd02</span>
							<img src="./img/ic_open.png" style="width:2.0rem;">
						</button>
						<div id="collapse02" class="collapse" aria-labelledby="collapse02" aria-labelledby="collapse_hd02" data-parent="#collapse_wr">
							내용2
						</div>
					</li>
				</ul>
			</div>


			<h1 class="mb-3 mt-5 fs_16">▼ Collapse 타이틀 바깥에 컨텐츠 붙을때</h1>
			<div id="collapse_wr" class="collapse_ex">
				<ul>
					<li id="collapse_hd03">
						<button type="button" class="btn btn-link btn-sm" data-toggle="collapse" data-target="#collapse03" aria-expanded="false" aria-controls="collapse03">
							<span class="">collapse_hd03</span>
							<img src="./img/ic_open.png" style="width:2.0rem;">
						</button>
					</li>
					<li id="collapse_hd04">
						<button type="button" class="btn btn-link btn-sm" data-toggle="collapse" data-target="#collapse04" aria-expanded="false" aria-controls="collapse04">
							<span class="">collapse_hd04</span>
							<img src="./img/ic_open.png" style="width:2.0rem;">
						</button>
					</li>
				</ul>
				<div id="collapse03" class="collapse " aria-labelledby="collapse03" aria-labelledby="collapse_hd03" data-parent="#collapse_wr">
					내용1
				</div>
				<div id="collapse04" class="collapse" aria-labelledby="collapse04" aria-labelledby="collapse_hd04" data-parent="#collapse_wr">
					내용2
				</div>
			</div>

			<h1 class="mb-3 mt-5 fs_16">▼ toast 토스트는 이걸로 사용해주세요 </h1>
			<button type="button" class="btn btn-primary btn-sm" id="ToastBtn2">토스트 생성버튼</button>

			<div class="py-4"></div>

			<!-- 토스트 Toast -->
			<div id="Toast2" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true" data-delay="2000">
				<div class="toast-body">
					<p><i class="xi-error mr-2"></i>아이디 or 비밀번호를 다시 확인해주세요!</p>
				</div>
			</div>

			<script>
				// 토스트 toast
				const toastTrigger = document.getElementById('ToastBtn2')
				const toastToast = document.getElementById('Toast2');
				if (toastTrigger) {
					toastTrigger.addEventListener('click', () => {
						const toast_confirm = new bootstrap.Toast(toastToast);
						toast_confirm.show();
					});
				}
			</script>



			<h1 class="mb-3 mt-5 fs_16">▼ 검색</h1>
			<div class="mb-4">
				<form class="sch_ip border align-items-center">
					<input type="search" class="form-control fs_14 flex-fill border-0" placeholder="검색어를 입력해주세요">
					<button class="btn btn-icon flex-shrink-0"><img src="./img/ic_ip_sch.svg"  ></button>
				</form>
			</div>

			<h1 class="mb-3 mt-5 fs_16">▼ 회색검색</h1>
			<div class="mb-4">
				<form class="sch_ip sch_gray align-items-center">
					<input type="search" class="form-control fs_14 flex-fill border-0" placeholder="검색어를 입력해주세요">
					<button class="btn btn-icon flex-shrink-0"><img src="./img/ic_sch_gray.png" style="width:2.0rem;"></button>
				</form>
			</div>


			<h1 class="mb-3 mt-5 fs_16">▼ 상태값</h1>
			<div class="mb-4">
				<div class="od_status">
					<span class="status status_01">접수대기</span>
					<span class="status status_02">배달완료</span>
					<span class="status status_03">라이더배차</span>
					<button type="button" class="btn btn-link"><span class="status status_04">라이더호출</span></button>
					<span class="status status_05">진행중</span>
					<span class="status status_06">준비완료</span>
					<span class="status status_07">라이더 포장완료</span>
					<span class="status status_08">취소</span>
				</div>
			</div>
		</div>
	</div>
</div>


<? include_once("./inc/modal.php"); ?>
<? include_once("./inc/tail.php"); ?>