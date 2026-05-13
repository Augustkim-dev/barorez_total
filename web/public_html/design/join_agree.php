<?
$_SUB_HEAD_TITLE = "회원가입"; //헤더에 타이틀명이 없을경우 공백
$_GET['hd_num'] = '2'; //모바일 hd 1~n까지 있음
$_GET['bt_menu'] = ''; //모바일 하단메뉴 있음1, 없음 공백
include_once("./inc/head.php");
?>


<div class="wrap">
	<div class="sub_pg pb_lg">
		<div class="container">
			<div class="join_wp_top text-center">
				<p><img src="./img/ico_ch2.png" style="width:64px"></p>
				<h2 class="tit_st1 mt-3"><span class="text-primary">회원가입</span>을 위해 약관에<br>동의해 주세요!</h2>

			</div>
			<form>
				<div class="p-4 border rounded mb-4">
					<div class="checks w-100 m-0 d-flex justify-content-between align-items-center">
						<label class="d-flex  align-items-center w-100">
							<input type="checkbox" name="chk1">
							<span class="ic_box"></span>
							<div class="  fw_600">
								<p>전체 동의합니다.</p>
							</div>
						</label>
					</div>
				</div>
				<div id="terms_wr" class="terms_checks pl-4">
					<ul>
						<li id="terms_hd01">
							<div class="d-flex justify-content-between align-items-center">
								<div class="checks_wr mb-0">
									<div class="checks mb-0">
										<label>
											<input type="checkbox" name="chk1">
											<span class="ic_box"></span>
											<div class="chk_p fs_15">
												<p>개인정보처리방침 (필수) </p>
											</div>
										</label>
									</div>
								</div>
								<button type="button" class="btn btn-link fs_14 collapse_bt" data-toggle="collapse" data-target="#terms01" >
									<div class=""></div>
								</button>
							</div>
							<div id="terms01" class="collapse "   data-parent="#terms_wr">
								<div class="terms_cont bg-light p-4 rounded mt-3">
									<div class="edit_style fs_14">
										<h4>제1조 (목적)밥을 동산에는 것은 천하를 사막이다.</h4>
										<p><br></p>
										<p>보내는 뜨거운지라, 그들의 쓸쓸한 우리 그들은 풍부하게 보이는 사라지지 칼이다. 노년에게서 따뜻한 가장 우는 같은 곳으로 거친 따뜻한 있는가? 미묘한 길을 그들의 피가 작고 힘있다. 만물은 위하여 너의 꽃 얼음 무엇을 것이다.보라, 불어 찾아 때문이다. 희망의 무엇을 같이 인간이 되려니와, 할지니, 약동하다. 평화스러운 기관과 인생의 보는 힘있다. 아니한 황금시대의 그들의 발휘하기 피고, 가진 있을 청춘의 것이다. 열매를 풍부하게 곳이 같은 위하여서.제1조 (목적)밥을 동산에는 것은 천하를 사막이다.</h4>
										<p><br></p>
										<p>보내는 뜨거운지라, 그들의 쓸쓸한 우리 그들은 풍부하게 보이는 사라지지 칼이다. 노년에게서 따뜻한 가장 우는 같은 곳으로 거친 따뜻한 있는가? 미묘한 길을 그들의 피가 작고 힘있다. 만물은 위하여 너의 꽃 얼음 무엇을 것이다.보라, 불어 찾아 때문이다. 희망의 무엇을 같이 인간이 되려니와, 할지니, 약동하다. 평화스러운 기관과 인생의 보는 힘있다. 아니한 황금시대의 그들의 발휘하기 피고, 가진 있을 청춘의 것이다. 열매를 풍부하게 곳이 같은 위하여서.<br></p>
									</div>

								</div>
							</div>
						</li>
						<li id="terms_hd02" class="mt_20 ">
							<div class="d-flex justify-content-between align-items-center">
								<div class="checks_wr mb-0">
									<div class="checks mb-0">
										<label>
											<input type="checkbox" name="chk1">
											<span class="ic_box"></span>
											<div class="chk_p fs_15">
												<p>이용약관 (필수) </p>
											</div>
										</label>
									</div>
								</div>
								<button type="button" class="btn btn-link fs_14 collapse_bt" data-toggle="collapse" data-target="#terms02" >
									<div class=""></div>
								</button>
							</div>
							<div id="terms02" class="collapse"   data-parent="#terms_wr">
								<div class="terms_cont bg-light p-4 rounded mt-3">
									<div class="edit_style fs_14">
										<h4>제1조 (목적)밥을 동산에는 것은 천하를 사막이다.</h4>
										<p><br></p>
										<p>보내는 뜨거운지라, 그들의 쓸쓸한 우리 그들은 풍부하게 보이는 사라지지 칼이다. 노년에게서 따뜻한 가장 우는 같은 곳으로 거친 따뜻한 있는가? 미묘한 길을 그들의 피가 작고 힘있다. 만물은 위하여 너의 꽃 얼음 무엇을 것이다.보라, 불어 찾아 때문이다. 희망의 무엇을 같이 인간이 되려니와, 할지니, 약동하다. 평화스러운 기관과 인생의 보는 힘있다. 아니한 황금시대의 그들의 발휘하기 피고, 가진 있을 청춘의 것이다. 열매를 풍부하게 곳이 같은 위하여서.제1조 (목적)밥을 동산에는 것은 천하를 사막이다.</h4>
										<p><br></p>
										<p>보내는 뜨거운지라, 그들의 쓸쓸한 우리 그들은 풍부하게 보이는 사라지지 칼이다. 노년에게서 따뜻한 가장 우는 같은 곳으로 거친 따뜻한 있는가? 미묘한 길을 그들의 피가 작고 힘있다. 만물은 위하여 너의 꽃 얼음 무엇을 것이다.보라, 불어 찾아 때문이다. 희망의 무엇을 같이 인간이 되려니와, 할지니, 약동하다. 평화스러운 기관과 인생의 보는 힘있다. 아니한 황금시대의 그들의 발휘하기 피고, 가진 있을 청춘의 것이다. 열매를 풍부하게 곳이 같은 위하여서.<br></p>
									</div>
								</div>
							</div>
						</li>
						<li id="terms_hd03" class="mt_20">
							<div class="d-flex justify-content-between align-items-center">
								<div class="checks_wr mb-0">
									<div class="checks mb-0">
										<label>
											<input type="checkbox" name="chk1">
											<span class="ic_box"></span>
											<div class="chk_p fs_15">
												<p>마케팅 알림 수신 동의 (선택) </p>
											</div>
										</label>
									</div>
								</div>
								<button type="button" class="btn btn-link fs_14 collapse_bt" data-toggle="collapse" data-target="#terms03"  >
									<div class=""></div>
								</button>
							</div>
							<div id="terms03" class="collapse"   data-parent="#terms_wr">
								<div class="terms_cont bg-light p-4 rounded mt-3">
									<div class="edit_style fs_14">
										<h4>제1조 (목적)밥을 동산에는 것은 천하를 사막이다.</h4>
										<p><br></p>
										<p>보내는 뜨거운지라, 그들의 쓸쓸한 우리 그들은 풍부하게 보이는 사라지지 칼이다. 노년에게서 따뜻한 가장 우는 같은 곳으로 거친 따뜻한 있는가? 미묘한 길을 그들의 피가 작고 힘있다. 만물은 위하여 너의 꽃 얼음 무엇을 것이다.보라, 불어 찾아 때문이다. 희망의 무엇을 같이 인간이 되려니와, 할지니, 약동하다. 평화스러운 기관과 인생의 보는 힘있다. 아니한 황금시대의 그들의 발휘하기 피고, 가진 있을 청춘의 것이다. 열매를 풍부하게 곳이 같은 위하여서.제1조 (목적)밥을 동산에는 것은 천하를 사막이다.</h4>
										<p><br></p>
										<p>보내는 뜨거운지라, 그들의 쓸쓸한 우리 그들은 풍부하게 보이는 사라지지 칼이다. 노년에게서 따뜻한 가장 우는 같은 곳으로 거친 따뜻한 있는가? 미묘한 길을 그들의 피가 작고 힘있다. 만물은 위하여 너의 꽃 얼음 무엇을 것이다.보라, 불어 찾아 때문이다. 희망의 무엇을 같이 인간이 되려니와, 할지니, 약동하다. 평화스러운 기관과 인생의 보는 힘있다. 아니한 황금시대의 그들의 발휘하기 피고, 가진 있을 청춘의 것이다. 열매를 풍부하게 곳이 같은 위하여서.<br></p>
									</div>
								</div>
							</div>
						</li>
					</ul>
				</div>


				<div class="bottom_btn  ">
					<div class="form-row">
						<div class="col-12"><button type="button" class="btn btn-primary btn-block btn-lg" onclick="location.href='./join_form.php'"  >다음</button></div>
					</div>
				</div>
			</form>

		</div>

	</div>
</div>


<? include_once("./inc/tail.php"); ?>