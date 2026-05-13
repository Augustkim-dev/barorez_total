<!doctype html>
<html lang="ko">

<head>
	<meta charset="UTF-8">
	<meta name="Generator" content="맛집바로">
	<meta name="Author" content="맛집바로">
	<meta name="Keywords" content="맛집바로">
	<meta name="Description" content="맛집바로">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
	<meta name="apple-mobile-web-app-title" content="맛집바로">
	<meta content="telephone=no" name="format-detection">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta property="og:title" content="맛집바로">
	<meta property="og:description" content="맛집바로">
	<meta property="og:image" content="./img/og-image.png">
	<link rel="apple-touch-icon" sizes="180x180" href="./img/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="./img/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="./img/favicon-16x16.png">


	<link rel="manifest" href="manifest.json">
	<!-- iOS 홈 화면 아이콘 이름 -->
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-title" content="맛집바로">


	<link rel="manifest" href="">
	<link rel="mask-icon" href="" color="#ffffff">
	<meta name="msapplication-TileColor" content="">
	<meta name="theme-color" content="">
	<title>맛집바로</title>

	<!-- 제이쿼리 -->
	<script src="./js/jquery.min.js"></script>

	<!-- 폰트 -->
	<link rel="stylesheet" as="style" crossorigin href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard@v1.3.8/dist/web/variable/pretendardvariable.css" />

	<!-- j얼럿 
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
    <script src="./js/jalert.js"></script>-->

	<!-- Swiper -->
	<link rel="stylesheet" href="./css/swiper-bundle.min.css" />
	<script src="./js/swiper-bundle.min.js"></script>

	<!-- 별점 -->
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/raty/2.8.0/jquery.raty.min.js"></script>

	<!-- xe아이콘
	<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/xeicon@2.3.3/xeicon.min.css"> -->

	<!--부트스트랩-->
	<link rel="stylesheet" href="./css/boot_custom.css">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

	<!-- 로티 
	<script src="./js/lottie-player.js"></script>-->


	<!-- ie css 변수적용 -->
	<script src="./js/ie11CustomProperties.min.js"></script>

	<!-- JS -->
	<script src="./js/custom.js" defer></script>

	<!-- CSS -->
	<link rel="stylesheet" href="./css/custom.css"><!-- UI 커스텀 -->
	<link rel="stylesheet" href="./css/design.css"><!-- 디자인 -->


</head>


<div class="mobile_wr">
	<!-- 상단 -->
	<?php
	if ($_GET['hd_num'] == '1') {
		include_once('./inc/head_style01.php');
	} else if ($_GET['hd_num'] == '2') {
		include_once('./inc/head_style02.php');
	} else if ($_GET['hd_num'] == '3') {
		include_once('./inc/head_style03.php');
	} else if ($_GET['hd_num'] == '4') {
		include_once('./inc/head_style04.php');
	} else if ($_GET['hd_num'] == '5') {
		include_once('./inc/head_style05.php');
	} else if ($_GET['hd_num'] == '6') {
		include_once('./inc/head_style06.php');
	} else if ($_GET['hd_num'] == '7') {
		include_once('./inc/head_style07.php');
	} else if ($_GET['hd_num'] == '8') {
		include_once('./inc/head_style08.php');
	} else if ($_GET['hd_num'] == '9') {
		include_once('./inc/head_style09.php');
	} else if ($_GET['hd_num'] == '10') {
		include_once('./inc/head_style10.php');
	} else if ($_GET['hd_num'] == '11') {
		include_once('./inc/head_style11.php');
	} else {
	}
	?>

	<!-- 바텀 메뉴없음 -->
	<?php
	//if ($_GET['bt_menu'] == '1') {
	//include_once('./inc/bt_menu.php');
	//} else {
	//}
	?>

