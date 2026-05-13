<?
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$_SUB_HEAD_TITLE = "회원가입";
$_GET['hd_pc'] = '';//PC hd 메뉴있음1, 메뉴없음 공백
$_GET['hd_num'] = '5';//모바일 hd 1~n까지 있음
$_GET['bt_menu'] = '';//모바일 하단메뉴 있음1, ft 없음 공백
$_GET['ft_none'] = '';//모바일 ft 있음1, ft 없음 공백
include_once("./inc/head.php");

$row = $DB->get('category_bank_t');

?>

<div class="wrap">
    <div class="sub_pg">
		<div class="sign_pg">
			<div class="sign_wr container">
				<button class="btn btn-link d-none d-lg-flex mb-4" type="button" onclick="history.back()">
					<img class="mr-2" style="width:2.0rem;" src="./img/ic_back_pc.png" alt="뒤로가기">
					<span class="text-gray2">이전페이지</span>
				</button>
				<div class="tit_h2 mb-5"><span class="text-primary">사업자 정보</span><br>내용을 입력해 주세요</div>
				<div class="sign_box">
                    <form role="form" method="post" name="frm" id="frm" action="./sign02_update.php" target="hidden_ifrm">
                        <input type="hidden" name="st_lat" id="st_lat">
                        <input type="hidden" name="st_lng" id="st_lng">
                        <input type="hidden" name="st_zip" id="st_zip">

						<div class="ip_wr">
							<div class="ip_tit required">
								<h5>매장명(상호명)</h5>
							</div>
							<input type="text" class="form-control" placeholder="매장명 입력" name="st_name" id="st_name">
						</div>

						<div class="ip_wr mt-5">
							<div class="ip_tit required">
								<h5>대표자 이름</h5>
							</div>
							<input type="text" class="form-control" placeholder="대표자 이름 입력" name="st_company_boss" id="st_company_boss">
						</div>

						<div class="ip_wr mt-5">
							<div class="ip_tit required">
								<h5>사업장주소(배송기준지)</h5>
							</div>
							<div class="form-row">
								<div class="col-9">
									<input type="text" class="form-control" placeholder="사업장주소 입력" id="st_add1" name="st_add1">
								</div>
								<div class="col-3">
									<button type="button" class="btn btn-outline-light btn-block">주소 검색</button>
								</div>
								<div class="col-12 mt_8">
									<input type="text" class="form-control" placeholder="상세주소 입력" id="st_add2" name="st_add2">
								</div>
							</div>
						</div>

						<div class="ip_wr mt-5">
							<div class="ip_tit required">
								<h5>매장 전화번호(유선)</h5>
							</div>
							<input type="text" class="form-control" placeholder="매장 전화번호 입력" name="st_tel" id="st_tel">
						</div>

						<div class="ip_wr mt-5">
							<div class="ip_tit required">
								<h5>사업자등록번호</h5>
							</div>
							<input type="text" class="form-control" placeholder="사용자등록번호 입력" name="st_company_num1" id="st_company_num1">
						</div>

						<div class="ip_wr mt-5">
							<div class="ip_tit required">
								<h5>정산받을 계좌</h5>
							</div>
							<div class="form-row">
								<div class="col-4">
									<select class="form-control custom-select">
										<option selected>은행명</option>
                                        <? foreach ($row as $key => $value) { ?>
                                            <option value="<?= $value['ct_num'] ?>"><?= $value['ct_name'] ?></option>
                                        <? } ?>
									</select>
								</div>
								<div class="col-8">
									<input type="text" class="form-control" placeholder="계좌번호 입력" name="st_bank_account" id="st_bank_account">
								</div>
								<div class="col-12 mt_8">
									<input type="text" class="form-control" placeholder="계좌주 입력" name="st_bank_name" id="st_bank_name">
								</div>
							</div>
						</div>

						<div class="ip_wr mt-5">
							<div class="ip_tit required">
								<h5>사업자등록증</h5>
							</div>
							<div class="touch_scroll scroll_bar_none">
								<div class="d-flex">
									<div class="image_upload">
										<input id="ip_file" type="file" class="d-none">
										<label for="ip_file" class="upload_box">
											<div class="rect">
											</div>
											<p class="max_img">사진 0/1</p>
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
							</div>
						</div>

						<div class="ip_wr mt-5">
							<div class="ip_tit required">
								<h5>통장사본</h5>
							</div>
							<div class="touch_scroll scroll_bar_none">
								<div class="d-flex">
									<div class="image_upload">
										<input id="ip_file" type="file" class="d-none">
										<label for="ip_file" class="upload_box">
											<div class="rect">
											</div>
											<p class="max_img">사진 0/1</p>
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
							</div>
						</div>

						
						<script src="https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.mjs" type="module"></script> 
						<!-- #modal_sign_complete 안에 있는 로티 스크립트 -->
						<button type="button" class="btn btn-primary btn-block mt_20" data-toggle="modal" data-target="#modal_sign_complete">완료</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>





<? include_once("./inc/tail.php"); ?>
