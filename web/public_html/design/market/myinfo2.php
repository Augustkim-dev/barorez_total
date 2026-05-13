<?
$_SUB_HEAD_TITLE = "내정보 수정 확인";
$_GET['hd_pc'] = ' '; //PC hd  로그인시공백 / 로그아웃시 logout
$hd_num = 'setting'; //1차메뉴
$hd_num2 = 'setting1'; //2차메뉴
$hd_left = ' '; //왼쪽메뉴 on 땜시 만듬
include_once("./inc/head.php");
?>

<!-- 왼쪽 메뉴-->
<? include_once("./inc/left_menu.php"); ?>

<div class="sub_pg ">
	<div class="sub_wr">
		<div class="hd_tit2 flex-row">
			<div class="d-flex align-items-end flex-wrap">
				<h3 class="tit_st1 mr-5">내정보 수정</h3>
			</div>
		</div>
		<section class="card">
			<div class="card-body">
				<form>
					<div class=" ">
						<div class="pb-5">
							<p class="tit_st3 "><img src="./img/join_ico1.svg" alt=" 이미지" class="mr-3">기본정보</p>
							<div class="row">
								<div class="col-md-6 mt-5">
									<div class="form_wr ip_invalid" id="id_div">
										<div class="ip_tit required  ">
											<h5>아이디</h5>
										</div>
										<div class="form-row ">
											<div class="col-12">
												<input type="text" class="form-control" placeholder="아이디 입력" value="test1234" disabled >
											</div>
											
										</div>
										
									</div>

									<div class="form_wr  mt-5 ip_valid">
										<div class="ip_tit required">
											<h5>비밀번호</h5>
										</div>
										<input type="text" class="form-control" placeholder="비밀번호 입력">
										<div class="form-text ip_invalid">비밀번호를 입력해주세요</div>
									</div>
									<div class="form_wr  mt-5 ip_valid">
										<div class="ip_tit required">
											<h5>비밀번호 재입력 </h5>
										</div>
										<input type="text" class="form-control" placeholder="비밀번호 재입력">
										<div class="form-text ip_invalid">비밀번호가 일치하지않습니다.</div>
									</div>

								</div>
								<div class="col-md-6 mt-5">
									<div class="form_wr">
										<div class="ip_tit required">
											<h5>이름 </h5>
										</div>
										<input type="text" class="form-control" placeholder="이름 입력">
										<div class="form-text ip_invalid">반대문구</div>
									</div>
									<div class="form_wr mt-5 ip_invalid">
										<div class="ip_tit required">
											<h5>휴대폰번호</h5>
										</div>
										<div class="form-row">
											<div class="col">
												<input type="text" class="form-control" placeholder="‘-’ 없이 숫자만 입력">
											</div>
											<div class="col-4">
												<button type="button" class="btn btn-secondary btn-block  px-1">인증 요청</button>
											</div>
										</div>

										<div class="form-row">
											<div class="col mt-3 position-relative">
												<p class="time_lim">04:25</p>
												<input type="text" class="form-control" placeholder="인증번호 입력">
											</div>
											<div class="col-4 mt-3">
												<button type="button" class="btn btn-primary btn-block  " disabled="">확인</button>
											</div>
										</div>
										<div class="form-text ip_invalid">오류 텍스트</div>
									</div>
								</div>
							</div>
						</div>
						
						<div class="mt-5 border-top pt-5 pb-5">
							<p class="tit_st3   "><img src="./img/join_ico3.svg" alt="이미지" class="mr-3">정산 정보</p>

							<div class="row">
								<div class="col-md-6 mt-5">
									<div class="form_wr   ">
										<div class="ip_tit required">
											<h5>통장사본 파일첨부 </h5>
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
								<div class="col-md-6 mt-5">
									<div class="form_wr  ">
										<div class="ip_tit required  ">
											<h5>정산 받을계좌 </h5>
										</div>
										<div class="form-row ">
											<div class="col-12 mb-3">
												<div class="custom-sel">
													<button type="button" class="select-trigger">
														은행선택
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
											</div>
											<div class="col-12  mb-3">
												<input type="text" class="form-control" placeholder="예금주 입력">
											</div>
											<div class="col-12  mb-3">
												<input type="text" class="form-control" placeholder="‘-’ 없이 계좌번호 입력해주세요.">
											</div>
										</div>
										<div class="form-text ip_invalid">계좌정보를 입력하세요</div>
									</div>


								</div>

								
							</div>
						</div>
					</div>
				</form>
			</div>
		</section>


<div class="d-flex  justify-content-center mt_40 btn_group">
            <!--메뉴를 삭제하시겠습니까? 알림창으로 한번더 물어보기 -->
            <button type="button" class="btn btn-outline-light btn-lg btn-w2">취소</button>
            <button type="button" class="btn btn-primary btn-lg btn-w2" >수정 완료</button>



        </div>


	</div>
</div>


<? include_once("./inc/tail.php"); ?>