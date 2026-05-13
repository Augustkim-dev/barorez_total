<?
$_SUB_HEAD_TITLE = "아이디 찾기"; //헤더에 타이틀명이 없을경우 공백
$_GET['hd_num'] = '2'; //모바일 hd 1~n까지 있음
$_GET['bt_menu'] = ''; //모바일 하단메뉴 있음1, 없음 공백
include_once("./inc/head.php");
?>


<div class="wrap">
	<div class="sub_pg ">
		<div class="container">

			<div class="find_wp_top text-center">
				<p><img src="./img/find_idimg.png" style="width:150px"></p>
				<h2 class="tit_st3 mt-3">회원님의 아이디는<br>
					<span class="text-primary">ID_1234 </span>입니다
				</h2>
			</div>
			<p class="mt-5"><button type="button" class="btn btn-primary btn-block" onclick="location.href='./login.php'">로그인</button></p>
			<p class="mt-3"><button type="button" class="btn btn-outline-light btn-block" onclick="location.href='./find_pw.php'">비밀번호 찾기</button></p>


		</div>

	</div>
</div>


<? include_once("./inc/tail.php"); ?>