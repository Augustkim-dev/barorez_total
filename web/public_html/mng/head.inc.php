<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";

$_APP_TITLE = APP_TITLE;
$_OG_IMAGE = OG_IMAGE;

?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <title><?=$_APP_TITLE?></title>
    <!-- META SECTION -->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">

    <link rel="apple-touch-icon" sizes="57x57" href="<?php echo CDN_IMG_URL?>/favicon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="<?php echo CDN_IMG_URL?>/favicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="<?php echo CDN_IMG_URL?>/favicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="<?php echo CDN_IMG_URL?>/favicon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="<?php echo CDN_IMG_URL?>/favicon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="<?php echo CDN_IMG_URL?>/favicon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="<?php echo CDN_IMG_URL?>/favicon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="<?php echo CDN_IMG_URL?>/favicon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo CDN_IMG_URL?>/favicon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="<?php echo CDN_IMG_URL?>/favicon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo CDN_IMG_URL?>/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="<?php echo CDN_IMG_URL?>/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo CDN_IMG_URL?>/favicon/favicon-16x16.png">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="<?php echo CDN_IMG_URL?>/favicon/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <!-- END META SECTION -->
    <!-- CSS INCLUDE -->
    <link rel="stylesheet" href="<?=MNG_HTTP?>/css/styles.css?v=<?=$v_txt?>">
    <link rel="stylesheet" href="<?=MNG_HTTP?>/css/user.css?v=<?=$v_txt?>">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.0/themes/base/jquery-ui.css">
    <!-- EOF CSS INCLUDE -->

    <!--Jquery-->
    <script type="text/javascript" src="<?=MNG_HTTP?>/js/vendors/jquery/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.0/jquery-ui.js"></script>
    <link rel="stylesheet" href="<?=MNG_HTTP?>/js/vendors/jquery-uploader/jquery.uploader.css?v=<?=$v_txt?>">
    <script src="<?=MNG_HTTP?>/js/vendors/jquery-uploader/jquery.uploader.min.js"></script>
    <link rel="stylesheet" href="<?=MNG_HTTP?>/js/vendors/jquery-confirm/jquery-confirm.min.css" />
    <script src="<?=MNG_HTTP?>/js/vendors/jquery-confirm/jquery-confirm.min.js"></script>
    <link rel="stylesheet" href="<?=MNG_HTTP?>/js/vendors/jquery.toast/jquery.toast.min.css" />
    <script src="<?=MNG_HTTP?>/js/vendors/jquery.toast/jquery.toast.min.js"></script>

    <!-- FilePond -->
    <link href="<?=MNG_HTTP?>/js/vendors/filepond/filepond.css" rel="stylesheet">
    <link href="<?=MNG_HTTP?>/js/vendors/filepond/filepond-plugin-image-preview.css" rel="stylesheet">
    <script src="<?=MNG_HTTP?>/js/vendors/filepond/filepond-plugin-image-preview.js"></script>
    <script src="<?=MNG_HTTP?>/js/vendors/filepond/filepond.js"></script>
    <script src="<?=MNG_HTTP?>/js/vendors/sortable/Sortable.min.js"></script>
    <script type="text/javascript" src="<?=MNG_HTTP?>/js/fileupload.js?v=<?=$v_txt?>"></script>

    <link rel="stylesheet" type="text/css" href="<?=MNG_HTTP?>/js/vendors/datepicker/jquery.datetimepicker.min.css"/ >
    <script src="<?=MNG_HTTP?>/js/vendors/datepicker/jquery.datetimepicker.full.min.js"></script>

    <script src="<?=MNG_HTTP?>/js/jtoast.js?v=<?=$v_txt?>"></script>
    <script src="<?=MNG_HTTP?>/js/jalert.js?v=<?=$v_txt?>"></script>
    <script src="<?=MNG_HTTP?>/js/default.mng.js?v=<?=$v_txt?>"></script>

    <script>
        $.datetimepicker.setLocale('ko');
    </script>


</head>
<body>
<? if($_ADMIN_HEADER != false){ ?>
<!-- PAGE WRAPPER -->
<div class="page page--w-header page--w-fixed-header">
    <!-- PAGE HEADER -->
    <header class="page__header">
        <div class="logo-holder">
        <a href="<?php echo MNG_HTTP?>" class="logo-text d-none d-lg-block"><img src="<?=DESIGN_HTTP?>/market/img/logo2.svg" alt="홈으로 이동"></a>
        <div class="rw-btn rw-btn--nav" data-action="aside-hide">
        <span></span>
        </div>
        </div>
        <div class="box">
        </div>
        <div class="box-fluid">
        </div>
        <div class="box">
            <div class="dropdown float-left d-none">
                <button class="btn btn-light btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="li-clipboard-alert"></span></button>
                <div class="dropdown-menu dropdown-menu-right">
                    <div class="page-heading">
                        <div class="page-heading__container">
                            <h1 class="title">Notifications</h1>
                            <p class="caption">
                            List of latest events
                            </p>
                        </div>
                        <div class="page-heading__container float-right">
                            <button class="btn btn-light btn-icon"><span class="fa fa-refresh"></span></button>
                        </div>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item padding-left-5 border-top-0">
                            <div class="user user--bordered user--lg">
                                <img src="<?php echo MNG_HTTP?>/assets/img/users/user_2.jpg" alt="Tracey Newman">
                                    <div class="user__name">
                                    <strong>Tracey Newman</strong> commented on your <strong>Awesome article</strong>, <span class="text-muted">5 min ago</span>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item padding-left-5">
                            <div class="user user--bordered user--lg">
                                <img src="<?php echo MNG_HTTP?>/assets/img/users/user_1.jpg" alt="John Doe">
                                    <div class="user__name">
                                    <strong>John Doe</strong> added new article <strong>Progs for begginers</strong>, <span class="text-muted">13 min ago</span>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item padding-left-25">
                            <div class="icon-box icon-box--lg margin-right-10">
                                <span class="fa fa-cog"></span>
                            </div>
                            <p>
                                <strong>Important</strong> memory issue. Memory loading <strong>99%</strong> - 2021 of 2048
                            </p>
                        </li>
                        <li class="list-group-item padding-left-5">
                        <div class="user user--bordered user--lg">
                            <img src="<?php echo MNG_HTTP?>/assets/img/users/user_3.jpg" alt="Jonathan Foster">
                                <div class="user__name">
                                  <strong>Jonathan Foster</strong> edited product <strong>JST Smartphone</strong>, <span class="text-muted">30 min ago</span>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item padding-left-25">
                            <div class="icon-box icon-box--lg margin-right-10">
                            <i class="fa fa-folder-open-o"></i>
                            </div>
                            <strong>File uploading</strong> proccess 25%.
                            <div class="progress" style="height: 3px">
                                <div class="progress-bar bg-secondary" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item padding-left-10 padding-right-10"><button class="btn btn-light btn-block margin-top-5">All notifications</button></li>
                    </ul>
                </div>
            </div>
            <a class="btn btn-light btn-icon float-left" href="<?php echo MNG_HTTP?>/logout.php"><span class="li-exit-right"></span></a>
        </div>
    </header>
    <!-- //END PAGE HEADER -->



    <!-- PAGE CONTENT WRAPPER -->
    <div class="page__content page__content--w-aside-fixed" id="page-content">
<?php } ?>
