<?
$_SUB_HEAD_TITLE = "매장관리>매장정보";
$_GET['hd_pc'] = ' '; //PC hd  로그인시공백 로그아웃시 logout
$hd_num = 'store'; //왼쪽메뉴 active 땜시 만듬
$hd_left = ' '; //왼쪽메뉴 on 땜시 만듬
include_once("./inc/head.php");
?>

<!-- 왼쪽 메뉴-->
<? include_once("./inc/left_menu.php"); ?>

<div class="sub_pg ">
	<div class="sub_wr">
		<div class="hd_tit2 flex-row-reverse">
			<div class="flex-shrink-0 ml-auto">
				<button type="button" class="btn btn-secondary rounded-pill " onclick="location.href='./store.php' ">매장정보</button>
				<button type="button" class="btn btn-outline-light rounded-pill ml-2" onclick="location.href='./store_time.php' ">운영시간</button>
				<button type="button" class="btn btn-outline-light rounded-pill ml-2" onclick="location.href='./store_set.php' ">기능설졍</button>
			</div>
			<div class="d-flex align-items-end flex-wrap">
				<h3 class="tit_st1 mr-5">매장관리</h3>
			</div>
		</div>
		<form>
			<!-- 회원가입에 작성한 내용 그대로 나옴-->
			<section class="card">
				<div class="card-body">
					<p class="tit_st3   "><img src="./img/join_ico2.svg" alt="이미지" class="mr-3">사업자(매장) 정보</p>
					<div class="row">
						<div class="col-md-6 mt-5">
							<div class="form_wr">
								<div class="ip_tit required">
									<h5>상호(법인명) </h5>
								</div>
								<input type="text" class="form-control" placeholder="사업자등록증에 기재된 상호(법인명) 입력" value="풍년밥상">
								<div class="form-text ip_invalid">반대문구</div>
							</div>
							<div class="form_wr mt-5">
								<div class="ip_tit required">
									<h5>사업자등록번호 </h5>
								</div>
								<input type="text" class="form-control" placeholder="입력하세요" value="123">
								<div class="form-text ip_invalid">반대문구</div>
							</div>
							<div class="form_wr mt-5">
								<div class="ip_tit required">
									<h5>매장명</h5>
								</div>
								<input type="text" class="form-control" placeholder="매장명 입력" value="풍년밥상 성수점">
								<div class="form-text ip_invalid">반대문구</div>
							</div>
							<div class="form_wr mt-5">
								<div class="ip_tit required">
									<h5>매장 연락처</h5>
								</div>
								<input type="text" class="form-control" placeholder="연락처 입력" value="01012341234">
								<div class="form-text ip_invalid">반대문구</div>
							</div>
						</div>
						<div class="col-md-6 mt-5">
							<div class="form_wr  ">
								<div class="ip_tit required">
									<h5>대표자명</h5>
								</div>
								<input type="text" class="form-control" placeholder="대표자명 입력" value="홍길동">
								<div class="form-text ip_invalid">반대문구</div>
							</div>

							<div class="form_wr mt-5  ">
								<div class="ip_tit required">
									<h5>주소</h5>
								</div>
								<div class="form-row">
									<div class="col">
										<input type="text" class="form-control" placeholder="우편번호 검색시 자동등록" value="12345">
									</div>
									<div class="col-4">
										<button type="button" class="btn btn-secondary btn-block  px-1">우편번호 검색</button>
									</div>
								</div>
								<div class="mt-3">
									<input type="text" class="form-control" placeholder="우편번호 검색시 자동등록" value="서울특별시 성동구 성수일로 77">
								</div>
								<div class="mt-3">
									<input type="text" class="form-control" placeholder="상세주소" value="서울숲 IT 밸리) 123-142">
								</div>
								<div class="form-text ip_invalid">오류 텍스트</div>
							</div>
							<div class="form_wr  mt-5">
								<div class="ip_tit required">
									<h5>사업자등록증 </h5>
								</div>
								<div class="d-flex">
									<div class="image_upload">
										<input id="ip_file" type="file" class="d-none">
										<label for="ip_file" class="upload_box">
											<div class="rect">
											</div>
											<p class="max_img">사진 1/1</p>
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

								</div>
								<div class="form-text ip_invalid">반대문구</div>
							</div>
						</div>
					</div>
				</div>
			</section>
			<section class="card mt-4 ">
				<div class="card-body">
					<div class="form_wr   ">
						<div class="ip_tit">
							<h5>매장 소개</h5>
						</div>
						<textarea class="form-control" placeholder="매장을 소개하는 문구를 입력하세요" rows="5"></textarea>
						<p class="text-right mt-2 tg_500 fs_14">(0/500)</p>
					</div>
				</div>
			</section>
			<section class="card mt-4 ">
				<div class="card-body">
					<div class="form_wr   ">
						<div class="ip_tit">
							<h5>매장 이미지(최소 3장)</h5>
						</div>
						<div class="d-flex">
							<div class="image_upload">
								<input id="ip_file" type="file" class="d-none">
								<label for="ip_file" class="upload_box">
									<div class="rect">
									</div>
									<p class="max_img">사진 2/5</p>
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


						</div>
					</div>
				</div>
			</section>
		</form>
		<div class="text-center mt_50 mb-5">
			<button type="button" class="btn btn-primary btn-lg btn-w1"  >저장</button>

		</div>
	</div>
</div>


<? include_once("./inc/tail.php"); ?>