<?
$_SUB_HEAD_TITLE = "메뉴추가";
$_GET['hd_pc'] = ' '; //PC hd  로그인시공백 / 로그아웃시 logout
$hd_num = 'menu'; //1차메뉴
$hd_num2 = 'menu'; //2차메뉴
$hd_left = ' '; //왼쪽메뉴 on 땜시 만듬
include_once("./inc/head.php");
?>


<!-- 왼쪽 메뉴-->
<? include_once("./inc/left_menu.php"); ?>

<div class="sub_pg ">
	<div class="sub_wr">
		<div class="hd_tit2 fs_16 flex-row">
			<h2 class="tit_st1 d-flex align-items-center mr-5 "><a href="#" onclick="history.back(); return false;" class="mr-4 line_h0 "><img src="./img/ico_back.svg" alt=" 뒤로가기"></a> <span>새 메뉴 추가</span></h2>
		</div>
		<div class="card mb-4">
			<div class="card-body">
				<div class="row">
					<div class="col-8">
						<div class="form_wr">
							<div class="ip_tit required">
								<h5>메뉴명 </h5>
							</div>
							<input type="text" class="form-control" placeholder="메뉴명 입력">
							<div class="form-text ip_invalid">반대문구</div>
						</div>
					</div>
					<div class="col-4  align-items-end  d-flex   ">
						<div class="custom-control custom-switch switch-outside">
							<span class="switch-state"></span>
							<input type="checkbox"
								class="custom-control-input"
								id="customSwitch_menu1"
								data-on="판매가능"
								data-off="미판매"
								checked>
							<label class="custom-control-label" for="customSwitch_menu1"></label>
						</div>
					</div>
				</div>
				<div class="row mt-5">
					<div class="col-md-6">
						<div class="form_wr  ip_valid">
							<div class="ip_tit required">
								<h5>카테고리</h5>
							</div>
							<div class="custom-sel">
								<button type="button" class="select-trigger">
									카테고리류
								</button>

								<ul class="select-options">
									<li data-value="1">옵션 1</li>
									<li data-value="2">옵션 2</li>
									<li data-value="3">옵션 3 </li>
									<li data-value="4">옵션 4</li>
									<li data-value="5">옵션 5</li>
									<li data-value="6">옵션 6</li>
									<li data-value="7">옵션 7</li>
									<li data-value="8">옵션 8</li>
								</ul>

								<input type="hidden" name="option">
							</div>
							<div class="form-text ip_invalid"> 입력해주세요</div>
						</div>
						<div class="form_wr mt-5 ip_valid">
							<div class="ip_tit required">
								<h5>가격(원)</h5>
							</div>
							<input type="text" class="form-control" placeholder="0">
							<div class="form-text ip_invalid">가격을 입력해주세요</div>
						</div>
						<div class="form_wr  mt-5 ip_valid">
							<div class="ip_tit required">
								<h5>메뉴 설명</h5>
							</div>
							<textarea class="form-control" placeholder="메뉴 소개하는 문구를 간략하게 입력하세요" rows="3" style="    min-height: 10rem;"></textarea>
							<p class="text-right mt-2 tg_500 fs_14">(0/100)</p>
							<div class="form-text ip_invalid">비밀번호를 입력해주세요</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form_wr  mt-5 ip_valid">
							<div class="ip_tit  ">
								<h5>메뉴 이미지(1장)</h5>
							</div>
							<div class="imgup_wp">
								<div class="image_upload">
									<input id="ip_file" type="file" class="d-none">
									<label for="ip_file" class="upload_box">
										<div class="rect">
										</div>
										<p class="max_img">0/1</p>
									</label>
									<button type="button" class="btn upload_del"><img src="./img/img_del.png"></button>
								</div>
								<p class="fs_16 text-left mt-4 line_h1_4">
									JPG/PNG 권장됩니다. <br>
									이미지 규격 1:1비율로 1:1비율의 사진이 아닐 경우 이미지가 잘릴 수 있습니다. <br>
									추천사이즈는 가로 800px 세로 800px 사이즈 입니다.
								</p>
							</div>
						</div>
						<div class="mt-5">
							<div class="checks">
								<label>
									<input type="checkbox" name="chk1" checked="">
									<span class="ic_box"></span>
									<div class="chk_p fs_20">
										<p>19세 이상 판매 품목일 경우 체크해주세요</p>
									</div>
								</label>
							</div>
							<p class="alim_txt fs_16 line_h1_4">
								주류 등 19세 이상 판매 품목은 청소년보호법에 따라 성인 여부 확인이 필수입니다. 매장 방문 시 신분증을 확인해 주시기 바라며, 미확인으로 인한 법적 책임은 가맹점에 귀속됩니다.
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="card">
			<div class="card-body">
				<div class="d-flex align-items-center justify-content-between mb-5">
					<div>
						<h3 class="tit_st2">옵션 설정</h3>
						<p class="tg_500 mt-1">옵션을 카테고리별로 그룹화하여 관리합니다</p>
					</div>
					<button type="button" class="btn btn-dark"> + 옵션 카테고리 추가</button>
				</div>
				<section class="memu_opt container-fluid">
					<div class="row">
						<div class="col-lg-4 bg-light col-12">
							<div class="d-flex align-items-center justify-content-between  ">
								<h4 class="tit_st3">옵션1</h4>
								<a href="" class="tg_500"><img src="./img/ico_delete2.svg" alt=" 삭제" style="width:2.8rem">삭제</a>
							</div>
							<div class="form_wr mt-5 ip_valid">
								<div class="ip_tit required">
									<h5>옵션 카테고리명</h5>
								</div>
								<input type="text" class="form-control" placeholder="입력하세요">
								<div class="form-text ip_invalid">카테고리명을 입력해주세요</div>
							</div>
							<div class="form_wr mt-5 ip_valid">
								<div class="ip_tit required">
									<h5>최대 선택 개수</h5>
								</div>
								<input type="number" class="form-control" placeholder="1">
								<div class="form-text ip_invalid">숫자만 입력해주세요</div>
							</div>
							<div class="checks mt-4">
								<label>
									<input type="checkbox" name="chk111" checked="">
									<span class="ic_box"></span>
									<div class="chk_p fs_20">
										<p>필수 선택 </p>
									</div>
								</label>
							</div>
						</div>
						<div class="col-lg-8  col-12 ">
							<div class="form_wr   ">
								<div class="ip_tit  ">
									<h5>옵션 항목</h5>
								</div>
							</div>
							<div class="d-flex memu_opt2">
								<input type="text" class="form-control" placeholder="예 : 레귤러, 라지">
								<div class="input_txt">
									<span>원</span>
									<input type="number" class="form-control " placeholder="0">
								</div>
								<a href="" class="ml-4 flex-shrink-0"><img src="./img/ico_close.svg" alt="삭제"></a>
							</div>
							<div class="d-flex memu_opt2">
								<input type="text" class="form-control" placeholder="예 : 레귤러, 라지">
								<div class="input_txt">
									<span>원</span>
									<input type="number" class="form-control " placeholder="0">
								</div>
								<a href="" class="ml-4 flex-shrink-0"><img src="./img/ico_close.svg" alt="삭제"></a>
							</div>
							<button type="button" class="btn btn-outline-secondary btn-block">+ 옵션 추가</button>
						</div>
					</div>
				</section>
				<section class="memu_opt container-fluid">
					<div class="row">
						<div class="col-lg-4 bg-light col-12">
							<div class="d-flex align-items-center justify-content-between  ">
								<h4 class="tit_st3">옵션2</h4>
								<a href="" class="tg_500"><img src="./img/ico_delete2.svg" alt=" 삭제" style="width:2.8rem">삭제</a>
							</div>
							<div class="form_wr mt-5 ip_valid">
								<div class="ip_tit required">
									<h5>옵션 카테고리명</h5>
								</div>
								<input type="text" class="form-control" placeholder="0">
								<div class="form-text ip_invalid">카테고리명을 입력해주세요</div>
							</div>
							<div class="form_wr mt-5 ip_valid">
								<div class="ip_tit required">
									<h5>최대 선택 개수</h5>
								</div>
								<input type="number" class="form-control" placeholder="1">
								<div class="form-text ip_invalid">숫자만 입력해주세요</div>
							</div>
							<div class="checks mt-4">
								<label>
									<input type="checkbox" name="chk111" checked="">
									<span class="ic_box"></span>
									<div class="chk_p fs_20">
										<p>필수 선택 </p>
									</div>
								</label>
							</div>
						</div>
						<div class="col-lg-8  col-12 ">
							<div class="form_wr   ">
								<div class="ip_tit  ">
									<h5>옵션 항목</h5>
								</div>
							</div>
							<div class="d-flex memu_opt2">
								<input type="text" class="form-control" placeholder="예 : 레귤러, 라지">
								<div class="input_txt">
									<span>원</span>
									<input type="number" class="form-control " placeholder="0">
								</div>
								<a href="" class="ml-4 flex-shrink-0"><img src="./img/ico_close.svg" alt="삭제"></a>
							</div>
							<div class="d-flex memu_opt2">
								<input type="text" class="form-control" placeholder="예 : 레귤러, 라지">
								<div class="input_txt">
									<span>원</span>
									<input type="number" class="form-control " placeholder="0">
								</div>
								<a href="" class="ml-4 flex-shrink-0"><img src="./img/ico_close.svg" alt="삭제"></a>
							</div>
							<button type="button" class="btn btn-outline-secondary btn-block">+ 옵션 추가</button>
						</div>
					</div>
				</section>
			</div>
		</div>
		<div class="d-flex  justify-content-center mt_40 btn_group">
			<button type="button" class="btn btn-primary btn-lg btn-w1" onclick="location.href='./menu.php'">등록하기</button>



		</div>
	</div>
</div>

<? include_once("./inc/tail.php"); ?>