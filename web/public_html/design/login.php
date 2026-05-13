<?
$_SUB_HEAD_TITLE = "로그인"; //헤더에 타이틀명이 없을경우 공백
$_GET['hd_num'] = '6'; //모바일 hd 1~n까지 있음
$_GET['bt_menu'] = ''; //모바일 하단메뉴 있음1, 없음 공백
include_once("./inc/head.php");
?>


<div class="wrap">
	<div class="sub_pg">
		<div class="container">
			<div class="login_logo text-center">
				<img src="./img/logo.svg">
			</div>
			<form>
				<div class="  ">
					 
					<input type="text" class="form-control" placeholder="아이디 입력">
					<div class="form-text ip_invalid">아이디를 다시 확인해주세요</div>
				</div>
				<div class="form_wr mt-2">
					<input type="text" class="form-control" placeholder="비밀번호 입력">
					<div class="form-text ip_invalid">아이디를 다시 확인해주세요</div>
				</div>
				<div class="mt-4 d-flex justify-content-between fs_15 align-items-center">
					<div class="checks">
						<label>
							<input type="checkbox" name="auto_login" id="auto_login" value="Y">
							<span class="ic_box"></span>
							<div class="chk_p tg_500">
								<p>자동 로그인</p>
							</div>
						</label>
					</div>
					<div class="fs_14">
						<a href="./find_id.php" class="mr-3 tg_500 fw_500">아이디 찾기</a>
						<a href="./find_pw.php" class="tg_500 fw_500">비밀번호 찾기</a>
					</div>
				</div>
			</form>
			<button type="button" onclick="location.href='./index.php' " class="btn btn-lg btn-primary btn-block mt-5">로그인</button>
			<button type="button" onclick="location.href='./join_agree.php' " class="btn btn-lg btn-outline-light btn-block mt-3">회원가입</button>
			<ul class="sns_login">
				<li><a href=" "><img src="./img/sns_google.svg" alt="구글 로그인"></a></li>
				<li><a href=" "><img src="./img/sns_apple.svg" alt="애플 로그인"></a></li>
				<li><a href=" "><img src="./img/sns_naver.svg" alt="네이버 로그인"></a></li>
				<li><a href=" "><img src="./img/sns_kakao.svg" alt="카카오 로그인"></a></li>
			</ul>
		</div>
	</div>
</div>


<? include_once("./inc/tail.php"); ?>