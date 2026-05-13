<?
$_SUB_HEAD_TITLE = "비밀번호 재설정 완료";
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
				<div class="tit_h2 mb-5">비밀번호 <br>재설정<span class="text-primary">완료</span></div>
                <p class="wh_pre line_h1_4 text-gray fs_16">비밀번호 변경이 완료되었습니다.
				새로운 비밀번호로 로그인해 주세요.</p>

				<div class="my_40 d-flex justify-content-center">
					
					<script src="https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.mjs" type="module"></script> 
					<dotlottie-player src="./img/UXG6UYNKM0.json" background="transparent" speed="1" style="width: 160px; height: 160px;" loop autoplay></dotlottie-player>
				</div>
				<button type="button" class="btn btn-primary btn-block" onclick="location.href='./login.php'">로그인</button>
			</div>
		</div>
	</div>
</div>




<? include_once("./inc/tail.php"); ?>
