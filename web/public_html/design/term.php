<?
$_SUB_HEAD_TITLE = "이용약관 및 정책"; //헤더에 타이틀명이 없을경우 공백
$_GET['hd_num'] = '2'; //모바일 hd 1~n까지 있음
$_GET['bt_menu'] = ''; //모바일 하단메뉴 있음1, 없음 공백
include_once("./inc/head.php");
?>


<div class="wrap">
	<div class="sub_pg term_pg ">
		<div class=" ">
			<ul class="mypage_list">
				<li class="border-bottom">
					<a class="d-flex align-items-center" href="./term1.php">
						<div>위치기반 서비스 이용약관</div>
						<img class="ml-auto flex-shrink-0" src="./img/ic_more02.png" style="width:1.6rem;">
					</a>
				</li>
				<li class="border-bottom">
					<a class="d-flex align-items-center" href="./term2.php">
						<div>개인정보 처리방침</div>
						<img class="ml-auto flex-shrink-0" src="./img/ic_more02.png" style="width:1.6rem;">
					</a>
				</li>
				<li class="border-bottom">
					<a class="d-flex align-items-center" href="./term3.php">
						<div>서비스 이용약관</div>
						<img class="ml-auto flex-shrink-0" src="./img/ic_more02.png" style="width:1.6rem;">
					</a>
				</li>
				

			</ul>
		</div>
	</div>
</div>


<? include_once("./inc/tail.php"); ?>