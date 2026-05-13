<?
$_SUB_HEAD_TITLE = "통계관리";
$_GET['hd_pc'] = ' '; //PC hd  로그인시공백 / 로그아웃시 logout
$hd_num = 'revenue'; //1차메뉴
$hd_num2 = 'revenue2'; //2차메뉴
$hd_left = ' '; //왼쪽메뉴 on 땜시 만듬
include_once("./inc/head.php");
?>

<!-- 왼쪽 메뉴-->
<? include_once("./inc/left_menu.php"); ?>

<div class="sub_pg ">
	<div class="sub_wr">
		<div class="hd_tit2 fs_16 flex-row">
			<h2 class="tit_st1 d-flex align-items-center mr-5 "> <span>통계관리</span></h2>
		</div>
		<div class="row stati_row">
			<div class="col-12">
				<div class="card rounded-lg">
					<div class="card-body">
						<div class="stati_hd">
							<div class="hd_wp">
								<p class="fw_600 mb-3">기간선택</p>
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
							<div class="hd_wp">
								<p class="fw_600 mb-3">날짜 선택</p>
								<div class="d-flex  align-items-center">
									<input type="date" class="form-control  ">
									<p class="px-2">~</p>
									<input type="date" class="form-control   ">
								</div>
							</div>
							<div class="hd_wp">
								<p class="fw_600 mb-lg-3 d-none d-lg-block">&nbsp;</p>
								<div class="d-flex">
									<button type="button" class="btn btn-secondary mx-2">검색</button>
									<button type="button" class="btn btn-outline-secondary px-4  flex-shrink-0 "><img src="./img/ico_reset.svg" alt="초기화"></button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-4 col-12">
				<div class="card rounded-lg  h-100">
					<div class="card-body">
						<div class="tit_st4 d-flex align-items-center mb-5">
							<span class="mr-2"><img src="./img/stat_img1.svg" alt=" "></span>
							<p>총 매출</p>
						</div>
						<p class="tit_st1">22,222,222원</p>
					</div>
				</div>
			</div>
			<div class="col-md-4 col-12 ">
				<div class="card rounded-lg h-100">
					<div class="card-body">
						<div class="tit_st4 d-flex align-items-center mb-5">
							<span class="mr-2"><img src="./img/stat_img2.svg" alt=" "></span>
							<p>총 주문 수</p>
						</div>
						<p class="tit_st1">48건</p>
					</div>
				</div>
			</div>
			<div class="col-md-4 col-12 h-100">
				<div class="card rounded-lg">
					<div class="card-body">
						<div class="tit_st4 d-flex align-items-center mb-5">
							<span class="mr-2"><img src="./img/stat_img1.svg" alt=" "></span>
							<p>평균 주문 금액(항목 바꿔도됨)</p>
						</div>
						<p class="tit_st1">22,222,222원</p>
					</div>
				</div>
			</div>
			<div class="col-md-8 col-12 ">
				<div class="card rounded-lg h-100">
					<div class="card-body">
						<div class="d-flex align-items-center justify-content-between mb-5">
							<div>
								<p class="tit_st4  ">주간 매출 현황</p>
								<p class="tg_500 fs_16 mt-1">주문 유형별 비교 그래프입니다.</p>
							</div>
							<div class="btn-group btn-group-toggle btn_toggle_primary group_sm" data-toggle="buttons">
								<label class="btn btn-outline-light active">
									<input type="radio" name="options" id="option1" checked=""> 주문 현황
								</label>
								<label class="btn btn-outline-light  ">
									<input type="radio" name="options" id="option2"> 매출 현황
								</label>
							</div>
						</div>
						<img src="./img/graphimg.png" alt=" ">
					</div>
				</div>
			</div>
			<div class="col-md-4 col-12">
				<div class="card rounded-lg h-100">
					<div class="card-body">
						<div>
							<p class="tit_st4  ">판매 베스트 10위</p>
							<p class="tg_500 fs_16 mt-1">우리 매장에 가장 인기 있는 메뉴</p>
						</div>
						<ul class="stati_ranking">
							<li>
								<div class="text-primary fw_700 flex-shrink-0">1위</div>
								<div class="  flex-fill"><a href="./menu_edit.php"> (대표메뉴)해물칼국수</a></div>
								<div class="ml-auto  flex-shrink-0">1505건</div>
							</li>
							<li>
								<div class="text-primary fw_700 flex-shrink-0">2위</div>
								<div class="  flex-fill"><a href="./menu_edit.php"> (대표메뉴)해물칼국수  (대표메뉴)해물칼국수  (대표메뉴)해물칼국수</a></div>
								<div class="ml-auto  flex-shrink-0">1505건</div>
							</li>
							<li>
								<div class="text-primary fw_700 flex-shrink-0">3위</div>
								<div class="  flex-fill"><a href="./menu_edit.php"> (대표메뉴)해물칼국수</a></div>
								<div class="ml-auto  flex-shrink-0">1505건</div>
							</li>
							<li>
								<div class="text-primary fw_700 flex-shrink-0">4위</div>
								<div class="  flex-fill"><a href="./menu_edit.php"> (대표메뉴)해물칼국수</a></div>
								<div class="ml-auto  flex-shrink-0">1505건</div>
							</li>
							<li>
								<div class="text-primary fw_700 flex-shrink-0">5위</div>
								<div class="  flex-fill"><a href="./menu_edit.php"> (대표메뉴)해물칼국수</a></div>
								<div class="ml-auto  flex-shrink-0">1505건</div>
							</li>
							<li>
								<div class="text-primary fw_700 flex-shrink-0">6위</div>
								<div class="  flex-fill"><a href="./menu_edit.php"> (대표메뉴)해물칼국수</a></div>
								<div class="ml-auto  flex-shrink-0">1505건</div>
							</li>
							<li>
								<div class="text-primary fw_700 flex-shrink-0">7위</div>
								<div class="  flex-fill"><a href="./menu_edit.php"> (대표메뉴)해물칼국수</a></div>
								<div class="ml-auto  flex-shrink-0">1505건</div>
							</li>
							<li>
								<div class="text-primary fw_700 flex-shrink-0">8위</div>
								<div class="  flex-fill"><a href="./menu_edit.php"> (대표메뉴)해물칼국수</a></div>
								<div class="ml-auto  flex-shrink-0">5건</div>
							</li>
							<li>
								<div class="text-primary fw_700 flex-shrink-0">9위</div>
								<div class="  flex-fill"><a href="./menu_edit.php"> (대표메뉴)해물칼국수</a></div>
								<div class="ml-auto  flex-shrink-0">5건</div>
							</li>
							<li>
								<div class="text-primary fw_700 flex-shrink-0">10위</div>
								<div class="  flex-fill"><a href="./menu_edit.php"> (대표메뉴)해물칼국수</a></div>
								<div class="ml-auto  flex-shrink-0">5건</div>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>

<? include_once("./inc/tail.php"); ?>