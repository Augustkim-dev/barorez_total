<?
$_SUB_HEAD_TITLE = "회원가입";
$_GET['hd_pc'] = '';//PC hd 메뉴있음1, 메뉴없음 공백
$_GET['hd_num'] = '5';//모바일 hd 1~n까지 있음
$_GET['bt_menu'] = '';//모바일 하단메뉴 있음1, ft 없음 공백
$_GET['ft_none'] = '';//모바일 ft 있음1, ft 없음 공백
include_once("./inc/head.php");
?>

<div class="wrap">
    <div class="sub_pg">
		<div class="sign_pg">
			<div class="sign_wr container">
				<button class="btn btn-link d-none d-lg-flex mb-4" type="button" onclick="history.back()">
					<img class="mr-2" style="width:2.0rem;" src="./img/ic_back_pc.png" alt="뒤로가기">
					<span class="text-gray2">이전페이지</span>
				</button>
				<div class="tit_h2 mb-5">회원가입을 위해 <span class="text-primary">약관에<br>동의</span>해 주세요</div>
				<div class="sign_box">
					<form>
						<div id="terms_wr" class="terms_checks">
							<ul>
								<li id="terms_hd01">
									<div  class="d-flex justify-content-between align-items-center">
										<div class="checks_wr">
											<div class="checks">
												<label>
													<input type="checkbox" name="chk3" id="ck3">
													<span class="ic_box"></span>
													<div class="chk_p">
														<p>서비스 약관동의</p>
													</div>
												</label>
											</div>
										</div>
										<button type="button" class="btn btn-link btn-sm collapse_bt" data-toggle="collapse" data-target="#terms01" aria-expanded="true" aria-controls="terms01">
											<div class=""></div>
										</button>
									</div>
									<div id="terms01" class="collapse show" aria-labelledby="terms01" aria-labelledby="terms_hd01" data-parent="#terms_wr">
										<div class="terms_cont bg-light p-4 rounded mt_20">
											<div class="edit_style fs_14"><h4>제1조 (목적)밥을 동산에는 것은 천하를 사막이다.</h4><p><br></p><p>보내는 뜨거운지라, 그들의 쓸쓸한 우리 그들은 풍부하게 보이는 사라지지 칼이다. 노년에게서 따뜻한 가장 우는 같은 곳으로 거친 따뜻한 있는가? 미묘한 길을 그들의 피가 작고 힘있다. 만물은 위하여 너의 꽃 얼음 무엇을 것이다.보라, 불어 찾아 때문이다. 희망의 무엇을 같이 인간이 되려니와, 할지니, 약동하다. 평화스러운 기관과 인생의 보는 힘있다. 아니한 황금시대의 그들의 발휘하기 피고, 가진 있을 청춘의 것이다. 열매를 풍부하게 곳이 같은 위하여서.제1조 (목적)밥을 동산에는 것은 천하를 사막이다.</h4><p><br></p><p>보내는 뜨거운지라, 그들의 쓸쓸한 우리 그들은 풍부하게 보이는 사라지지 칼이다. 노년에게서 따뜻한 가장 우는 같은 곳으로 거친 따뜻한 있는가? 미묘한 길을 그들의 피가 작고 힘있다. 만물은 위하여 너의 꽃 얼음 무엇을 것이다.보라, 불어 찾아 때문이다. 희망의 무엇을 같이 인간이 되려니와, 할지니, 약동하다. 평화스러운 기관과 인생의 보는 힘있다. 아니한 황금시대의 그들의 발휘하기 피고, 가진 있을 청춘의 것이다. 열매를 풍부하게 곳이 같은 위하여서.<br></p></div>
										</div>
									</div>
								</li>
								<li id="terms_hd02" class="mt_20">
									<div  class="d-flex justify-content-between align-items-center">
										<div class="checks_wr">
											<div class="checks">
												<label>
													<input type="checkbox" name="ck2" id="ck2">
													<span class="ic_box"></span>
													<div class="chk_p">
														<p>개인정보 처리방침</p>
													</div>
												</label>
											</div>
										</div>
										<button type="button" class="btn btn-link btn-sm collapse_bt" data-toggle="collapse" data-target="#terms02" aria-expanded="false" aria-controls="terms02">
											<div class=""></div>
										</button>
									</div>
									<div id="terms02" class="collapse" aria-labelledby="terms02" aria-labelledby="terms_hd01" data-parent="#terms_wr">
										<div class="terms_cont bg-light p-4 rounded mt_20">
											<div class="edit_style fs_14"><h4>제1조 (목적)밥을 동산에는 것은 천하를 사막이다.</h4><p><br></p><p>보내는 뜨거운지라, 그들의 쓸쓸한 우리 그들은 풍부하게 보이는 사라지지 칼이다. 노년에게서 따뜻한 가장 우는 같은 곳으로 거친 따뜻한 있는가? 미묘한 길을 그들의 피가 작고 힘있다. 만물은 위하여 너의 꽃 얼음 무엇을 것이다.보라, 불어 찾아 때문이다. 희망의 무엇을 같이 인간이 되려니와, 할지니, 약동하다. 평화스러운 기관과 인생의 보는 힘있다. 아니한 황금시대의 그들의 발휘하기 피고, 가진 있을 청춘의 것이다. 열매를 풍부하게 곳이 같은 위하여서.제1조 (목적)밥을 동산에는 것은 천하를 사막이다.</h4><p><br></p><p>보내는 뜨거운지라, 그들의 쓸쓸한 우리 그들은 풍부하게 보이는 사라지지 칼이다. 노년에게서 따뜻한 가장 우는 같은 곳으로 거친 따뜻한 있는가? 미묘한 길을 그들의 피가 작고 힘있다. 만물은 위하여 너의 꽃 얼음 무엇을 것이다.보라, 불어 찾아 때문이다. 희망의 무엇을 같이 인간이 되려니와, 할지니, 약동하다. 평화스러운 기관과 인생의 보는 힘있다. 아니한 황금시대의 그들의 발휘하기 피고, 가진 있을 청춘의 것이다. 열매를 풍부하게 곳이 같은 위하여서.<br></p></div>
										</div>
									</div>
								</li>
								<li id="terms_hd03" class="mt_20">
									<div  class="d-flex justify-content-between align-items-center">
										<div class="checks_wr">
											<div class="checks">
												<label>
													<input type="checkbox" name="ck1" id="ck1">
													<span class="ic_box"></span>
													<div class="chk_p">
														<p>개인정보 제3자 동의</p>
													</div>
												</label>
											</div>
										</div>
										<button type="button" class="btn btn-link btn-sm collapse_bt" data-toggle="collapse" data-target="#terms03" aria-expanded="false" aria-controls="terms03">
											<div class=""></div>
										</button>
									</div>
									<div id="terms03" class="collapse" aria-labelledby="terms03" aria-labelledby="terms_hd01" data-parent="#terms_wr">
										<div class="terms_cont bg-light p-4 rounded mt_20">
											<div class="edit_style fs_14"><h4>제1조 (목적)밥을 동산에는 것은 천하를 사막이다.</h4><p><br></p><p>보내는 뜨거운지라, 그들의 쓸쓸한 우리 그들은 풍부하게 보이는 사라지지 칼이다. 노년에게서 따뜻한 가장 우는 같은 곳으로 거친 따뜻한 있는가? 미묘한 길을 그들의 피가 작고 힘있다. 만물은 위하여 너의 꽃 얼음 무엇을 것이다.보라, 불어 찾아 때문이다. 희망의 무엇을 같이 인간이 되려니와, 할지니, 약동하다. 평화스러운 기관과 인생의 보는 힘있다. 아니한 황금시대의 그들의 발휘하기 피고, 가진 있을 청춘의 것이다. 열매를 풍부하게 곳이 같은 위하여서.제1조 (목적)밥을 동산에는 것은 천하를 사막이다.</h4><p><br></p><p>보내는 뜨거운지라, 그들의 쓸쓸한 우리 그들은 풍부하게 보이는 사라지지 칼이다. 노년에게서 따뜻한 가장 우는 같은 곳으로 거친 따뜻한 있는가? 미묘한 길을 그들의 피가 작고 힘있다. 만물은 위하여 너의 꽃 얼음 무엇을 것이다.보라, 불어 찾아 때문이다. 희망의 무엇을 같이 인간이 되려니와, 할지니, 약동하다. 평화스러운 기관과 인생의 보는 힘있다. 아니한 황금시대의 그들의 발휘하기 피고, 가진 있을 청춘의 것이다. 열매를 풍부하게 곳이 같은 위하여서.<br></p></div>
										</div>
									</div>
								</li>
							</ul>
						</div>

						<div class="p-4 border rounded mt_20">
							<div class="checks w-100 m-0 d-flex justify-content-between align-items-center">
								<label class="d-flex justify-content-between align-items-center w-100">
									<div class="fs_15 fw_600"><p>전체 동의합니다.</p></div>
									<input type="checkbox" name="ck0" id="ck0">
									<span class="ic_box"></span>
								</label>
							</div>
						</div>
						
						<button type="button" class="btn btn-primary btn-block mt_20"onclick="location.href='./sign01.php'">다음</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
    var ck1 = false;
    var ck2 = false;
    var ck3 = false;

    $(function () {
        $('#ck0').on({
            click: function (e) {
                if ($('#ck0').prop('checked')) {
                    $('#ck1').prop('checked', true);
                    $('#ck2').prop('checked', true);
                    $('#ck3').prop('checked', true);
                    ck1 = true;
                    ck2 = true;
                    ck3 = true;
                }
                else {
                    $('#ck1').prop('checked', false);
                    $('#ck2').prop('checked', false);
                    $('#ck3').prop('checked', false);
                    ck1 = false;
                    ck2 = false;
                    ck3 = false;
                }
            },
        });

        $('#ck1').on({
            click: function (e) {
                if ($('#ck1').prop('checked')) {
                    ck1 = true;
                    if ($('#ck2').prop('checked') && $('#ck3').prop('checked')) {
                        $('#ck0').prop('checked', true);
                    }
                }
                else {
                    $('#ck0').prop('checked', false);
                    ck1 = false;
                }
            },
        });

        $('#ck2').on({
            click: function (e) {
                if ($('#ck2').prop('checked')) {
                    ck2 = true;
                    if ($('#ck1').prop('checked') && $('#ck3').prop('checked')) {
                        $('#ck0').prop('checked', true);
                    }
                }
                else {
                    $('#ck0').prop('checked', false);
                    ck2 = false;
                }
            },
        });

        $('#ck3').on({
            click: function (e) {
                if ($('#ck3').prop('checked')) {
                    ck3 = true;
                    if ($('#ck1').prop('checked') && $('#ck2').prop('checked')) {
                        $('#ck0').prop('checked', true);
                    }
                }
                else {
                    $('#ck0').prop('checked', false);
                    ck3 = false;
                }
            },
        });
    });

    function f_next(){
        if(ck1 && ck2 && ck3){
            location.replace('./sign01.php');
        }
        else{
            jalert('필수약관에 동의해주세요.');
        }
    }
</script>
<? include_once("./inc/tail.php"); ?>
