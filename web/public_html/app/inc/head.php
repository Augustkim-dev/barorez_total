<?php
if ($_SESSION['mng']['mt_level'] > 2) {
    // 세션 파괴
    $_SESSION = [];

    // 로그아웃 후 로그인 페이지로
    alert('일반 계정만 접근 가능합니다.', '../auth/login.php');
    exit;
}
?>
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
    <link rel="apple-touch-icon" sizes="180x180" href="<?=DESIGN_HTTP?>/img/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?=DESIGN_HTTP?>/img/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?=DESIGN_HTTP?>/img/favicon-16x16.png">


<!--    <link rel="manifest" href="manifest.json">-->
    <!-- iOS 홈 화면 아이콘 이름 -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="맛집바로">


    <link rel="manifest" href="">
    <link rel="mask-icon" href="" color="#ffffff">
    <meta name="msapplication-TileColor" content="">
    <meta name="theme-color" content="">
    <title>맛집바로</title>

    <!-- 제이쿼리 -->
    <script src="<?=DESIGN_HTTP?>/js/jquery.min.js"></script>

    <!-- 폰트 -->
    <link rel="stylesheet" as="style" crossorigin href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard@v1.3.8/dist/web/variable/pretendardvariable.css" />

    <!-- j얼럿
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
    <script src="./js/jalert.js"></script>-->

    <!-- Swiper -->
    <link rel="stylesheet" href="<?=DESIGN_HTTP?>/css/swiper-bundle.min.css" />
    <script src="<?=DESIGN_HTTP?>/js/swiper-bundle.min.js"></script>

    <!-- 별점 -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/raty/2.8.0/jquery.raty.min.js"></script>

    <!-- xe아이콘
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/xeicon@2.3.3/xeicon.min.css"> -->

    <!--부트스트랩-->
    <link rel="stylesheet" href="<?=DESIGN_HTTP?>/css/boot_custom.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- 로티
    <script src="./js/lottie-player.js"></script>-->


    <!-- ie css 변수적용 -->
    <script src="<?=DESIGN_HTTP?>/js/ie11CustomProperties.min.js"></script>

    <!-- JS -->
    <script src="<?=DESIGN_HTTP?>/js/custom.js" defer></script>

    <!-- CSS -->
    <link rel="stylesheet" href="<?=DESIGN_HTTP?>/css/custom.css"><!-- UI 커스텀 -->
    <link rel="stylesheet" href="<?=DESIGN_HTTP?>/css/design.css?v=12"><!-- 디자인 -->


</head>
<body class="lang-<?= htmlspecialchars($resolved_lang) ?>">
<div class="mobile_wr">


