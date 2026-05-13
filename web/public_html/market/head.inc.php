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
    <link rel="stylesheet" href="<?=MARKET_HTTP?>/css/styles.css?v=<?=$v_txt?>">
    <link rel="stylesheet" href="<?=MARKET_HTTP?>/css/user.css?v=<?=$v_txt?>">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.0/themes/base/jquery-ui.css">
    <!-- EOF CSS INCLUDE -->

    <!--Jquery-->
    <script type="text/javascript" src="<?=MARKET_HTTP?>/js/vendors/jquery/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.0/jquery-ui.js"></script>
    <link rel="stylesheet" href="<?=MARKET_HTTP?>/js/vendors/jquery-uploader/jquery.uploader.css?v=<?=$v_txt?>">
    <script src="<?=MARKET_HTTP?>/js/vendors/jquery-uploader/jquery.uploader.min.js"></script>
    <link rel="stylesheet" href="<?=MARKET_HTTP?>/js/vendors/jquery-confirm/jquery-confirm.min.css" />
    <script src="<?=MARKET_HTTP?>/js/vendors/jquery-confirm/jquery-confirm.min.js"></script>
    <link rel="stylesheet" href="<?=MARKET_HTTP?>/js/vendors/jquery.toast/jquery.toast.min.css" />
    <script src="<?=MARKET_HTTP?>/js/vendors/jquery.toast/jquery.toast.min.js"></script>
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- FilePond -->
    <link href="<?=MARKET_HTTP?>/js/vendors/filepond/filepond.css" rel="stylesheet">
    <link href="<?=MARKET_HTTP?>/js/vendors/filepond/filepond-plugin-image-preview.css" rel="stylesheet">
    <script src="<?=MARKET_HTTP?>/js/vendors/filepond/filepond-plugin-image-preview.js"></script>
    <script src="<?=MARKET_HTTP?>/js/vendors/filepond/filepond.js"></script>
    <script src="<?=MARKET_HTTP?>/js/vendors/sortable/Sortable.min.js"></script>
    <script type="text/javascript" src="<?=MARKET_HTTP?>/js/fileupload.js?v=<?=$v_txt?>"></script>

    <link rel="stylesheet" type="text/css" href="<?=MARKET_HTTP?>/js/vendors/datepicker/jquery.datetimepicker.min.css"/ >
    <script src="<?=MARKET_HTTP?>/js/vendors/datepicker/jquery.datetimepicker.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="<?=MARKET_HTTP?>/js/jtoast.js?v=<?=$v_txt?>"></script>
    <script src="<?=MARKET_HTTP?>/js/jalert.js?v=<?=$v_txt?>"></script>
    <script src="<?=MARKET_HTTP?>/js/default.mng.js?v=<?=$v_txt?>"></script>

    <script>
        $.datetimepicker.setLocale('ko');
    </script>


</head>
<body>

<?php

$mb_idx = $_SESSION['mng']['mt_idx'] ?? 0;

$current_sh_idx = (int)($_SESSION['current_sh_idx'] ?? 0);

$shops = [];
if ($mb_idx) {
    $DB->where('mb_idx', $mb_idx);
    $DB->where('del_date', null, 'IS');
    $shops = $DB->get('shop_t', null, '
        idx,
        sh_title,
        sh_corp_nm,
        sh_branch_nm
    ');

    $DB->where('mb_idx', $mb_idx);
    $DB->where('del_date', null, 'IS');
    $DB->where('idx', 1);
    $shop_show = $DB->getOne('shop_t', 'sh_show');

}
?>

<? if($_ADMIN_HEADER != false){ ?>
<!-- PAGE WRAPPER -->
<div class="page page--w-header page--w-fixed-header">
    <!-- PAGE HEADER -->
    <header class="page__header">
        <div class="logo-holder">
            <a href="<?php echo MARKET_HTTP?>" class="logo-text d-none d-lg-block"><strong class="text-primary">#</strong>  <strong><?php echo ADMIN_NAME?></strong></a><a href="<?php echo MARKET_HTTP?>" class="logo-text d-lg-none"><strong class="text-primary">#</strong><strong><?php echo ADMIN_NAME?></strong></a>
            <div class="rw-btn rw-btn--nav" data-action="aside-hide">
                <span></span>
            </div>
        </div>
        <div class="box">
        </div>
        <div class="box-fluid">
        </div>
        <div class="box" style="width:25%">
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
                                <img src="<?php echo MARKET_HTTP?>/assets/img/users/user_2.jpg" alt="Tracey Newman">
                                <div class="user__name">
                                    <strong>Tracey Newman</strong> commented on your <strong>Awesome article</strong>, <span class="text-muted">5 min ago</span>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item padding-left-5">
                            <div class="user user--bordered user--lg">
                                <img src="<?php echo MARKET_HTTP?>/assets/img/users/user_1.jpg" alt="John Doe">
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
                                <img src="<?php echo MARKET_HTTP?>/assets/img/users/user_3.jpg" alt="Jonathan Foster">
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
            <div class="d-flex">
                <span id="currentTimeText"><?=date('n월 j일(D) H:i')?></span>
                <select class="form-control form-select-sm me-2" id="admin_shop_select">
                    <option value="" disabled <?=$current_sh_idx === 0 ? 'selected' : ''?>>매장 선택</option>
                    <?php foreach ($shops as $s): ?>
                        <?php
                        $labelParts = [];
                        if (!empty($s['sh_branch_nm'])) $labelParts[] = '['.$s['sh_branch_nm'].']';
                        if (!empty($s['sh_title']))     $labelParts[] = $s['sh_title'];
                        if (!empty($s['sh_corp_nm']))   $labelParts[] = '('.$s['sh_corp_nm'].')';
                        $label = trim(implode(' ', $labelParts)) ?: ('매장 #'.$s['idx']);
                        ?>
                        <option value="<?=$s['idx']?>"
                            <?=$current_sh_idx === (int)$s['idx'] ? 'selected' : ''?>>
                            <?=$label?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label class="switch switch-sm">영업중<input type="checkbox" name="sh_show" <?=$shop_show['sh_show']=="Y" ? "checked" : ""?> id="shop_open_toggle" value="<?=$shop_show['sh_show']?>"><span></span></label>
                <a class="btn btn-light btn-icon float-left" href="<?php echo MARKET_HTTP?>/logout.php"><span class="li-exit-right"></span></a>
            </div>
        </div>
    </header>
    <!-- //END PAGE HEADER -->
    <script>
        console.log('[time] header script loaded');

        (function () {
            var el = document.getElementById('currentTimeText');
            if (!el) {
                console.log('[time] currentTimeText not found -> skip');
                return;
            }

            // ✅ 서버시간 기준(권장): 최초 1회만 서버 timestamp를 박아둠
            var serverTs = <?= time() ?>;
            var clientStart = Date.now();
            var dayK = ['일','월','화','수','목','금','토'];

            function pad2(n){ return String(n).padStart(2,'0'); }

            function getNowByServerBase(){
                var elapsedSec = Math.floor((Date.now() - clientStart) / 1000);
                return new Date((serverTs + elapsedSec) * 1000);
            }

            function formatKorean(dt){
                var m = dt.getMonth() + 1;
                var d = dt.getDate();
                var w = dayK[dt.getDay()];
                var hh = pad2(dt.getHours());
                var mm = pad2(dt.getMinutes());
                return m + '월 ' + d + '일(' + w + ') ' + hh + ':' + mm;
            }

            function renderTime(){
                var dt = getNowByServerBase();
                var text = formatKorean(dt);
                console.log('[time] render:', text);
                el.textContent = text;
            }

            // ✅ 중복 실행 방지(헤더가 여러번 include될 가능성 대비)
            if (window.__qr_time_tick_started) {
                console.log('[time] already started -> skip');
                return;
            }
            window.__qr_time_tick_started = true;

            // 1) 즉시 출력
            renderTime();

            // 2) 다음 분(00초)에 맞춰 시작
            var dt = getNowByServerBase();
            var msToNextMinute = (60 - dt.getSeconds()) * 1000 - dt.getMilliseconds();

            console.log('[time] msToNextMinute:', msToNextMinute);

            setTimeout(function(){
                renderTime();
                setInterval(renderTime, 60000);
            }, msToNextMinute);
        })();
    </script>
    <script>
        $(function () {
            console.log('[shop-open] toggle init');

            $(document).on('change', '#shop_open_toggle', function () {
                var $toggle = $(this);

                var sh_idx = 1;
                var nextVal = $toggle.is(':checked') ? 'Y' : 'N';
                var prevChecked = ! $toggle.is(':checked'); // 실패 시 되돌리기용

                console.log('[shop-open] change', { sh_idx: sh_idx, nextVal: nextVal });

                if (!sh_idx) {
                    console.log('[shop-open] no shop selected');
                    alert('매장을 먼저 선택해 주세요.');
                    // 원복
                    $toggle.prop('checked', prevChecked);
                    return;
                }

                // 중복 클릭 방지
                $toggle.prop('disabled', true);

                $.ajax({
                    url: '<?=MARKET_HTTP?>/shop_open_update.php', // ✅ 새로 만들 파일
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        act: 'set_open',        // ✅ 서버 분기용
                        sh_idx: sh_idx,
                        sh_open: nextVal        // ✅ 'Y' or 'N'
                    },
                    success: function (res) {
                        console.log('[shop-open] success:', res);

                        if (res && res.success) {
                            // value도 동기화(기존 코드가 value를 쓰는 경우 대비)
                            $toggle.val(nextVal);

                            // 메시지
                            alert(res.message || (nextVal === 'Y' ? '영업중으로 변경되었습니다.' : '문닫음으로 변경되었습니다.'));
                        } else {
                            alert((res && res.message) || '상태 변경에 실패했습니다.');
                            // 원복
                            $toggle.prop('checked', prevChecked);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.log('[shop-open] error status:', status);
                        console.log('[shop-open] error:', error);
                        console.log('[shop-open] responseText:', xhr.responseText);

                        alert('통신 오류가 발생했습니다.');
                        // 원복
                        $toggle.prop('checked', prevChecked);
                    },
                    complete: function () {
                        $toggle.prop('disabled', false);
                    }
                });
            });

        });
    </script>
    <script>
        $(function () {
            $('#admin_shop_select').on('change', function () {
                var sh_idx = $(this).val();

                if (!sh_idx) {
                    alert('매장을 선택해 주세요.');
                    return;
                }

                $.ajax({
                    url: '<?php echo MARKET_HTTP?>/current_shop.php',
                    type: 'POST',
                    dataType: 'json',
                    data: { sh_idx: sh_idx },
                    success: function (res) {
                        if (res.success) {
                            location.reload();
                        } else {
                            alert(res.message || '매장 선택 중 오류가 발생했습니다.');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.log('AJAX ERROR status:', status);
                        console.log('AJAX ERROR error:', error);
                        console.log('AJAX ERROR responseText:', xhr.responseText);
                        alert('매장 선택 중 통신 오류가 발생했습니다.');
                    }
                });
            });
        });
    </script>

    <!-- PAGE CONTENT WRAPPER -->
    <div class="page__content page__content--w-aside-fixed" id="page-content">
        <?php } ?>
