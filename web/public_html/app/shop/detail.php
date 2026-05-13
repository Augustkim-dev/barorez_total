<?php
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
$_ENABLED_INC_TOP = true;
$_ENABLED_INC_QUICK = true;
//$_SUB_HEAD_TITLE = "로그인";
include_once $_SERVER['DOCUMENT_ROOT']."/app/inc/head.php";
$_SUB_HEAD_TITLE = ""; //헤더에 타이틀명이 없을경우 공백
$hd_num = ''; //모바일 hd 1~n까지 있음
$_GET['bt_menu'] = ''; //모바일 하단메뉴 있음1, 없음 공백
include_once $_SERVER['DOCUMENT_ROOT']."/app/inc/header.php";

$sm_idx = (int)($_GET['id'] ?? 0);
if ($sm_idx <= 0) {
    die('잘못된 접근입니다.');
}

$shopId = (int)($_SESSION['current_sh_idx'] ?? 0);
if ($shopId <= 0) {
    die('매장 정보(sh_idx)가 없습니다.');
}

$DB->where('idx',$sm_idx);
$DB->where('sm_show', 'Y');
$item = $DB->getOne('shop_menu_t');

if (!$item) {
    die('메뉴 정보를 찾을 수 없습니다.');
}

// 품절/판매중지 처리
$isSoldOut = false;
//if ((int)($item['sm_su'] ?? 0) <= 0) $isSoldOut = true;
if (($item['sm_type'] ?? 'Y') === 'N') $isSoldOut = true;

/**
 * ✅ 옵션 카테고리(그룹) 조회
 */
$DB->where('sm_idx', $sm_idx);
$DB->where('oc_show', 'Y');
$DB->orderBy('oc_order', 'ASC');
$optCategories = $DB->get('menu_option_category_t'); // 그룹들

/**
 * ✅ 옵션 항목 조회 (그룹별로 묶기)
 */
$optItemsByCategory = [];
if (!empty($optCategories)) {
    foreach ($optCategories as $oc) {
        $oc_idx = (int)$oc['idx'];
        $DB->where('oc_idx', $oc_idx);
        $DB->where('om_show', 'Y');
        $DB->orderBy('om_order', 'ASC');
        $optItemsByCategory[$oc_idx] = $DB->get('option_menu_t');
    }
}

$todayDow = (int)date('w');
$todayHoursText = '휴무일';

// CLOSE row가 아예 없는 경우도 휴무일로 처리할거면 그대로 OK
$DB->where('sh_idx', $shopId);
$DB->where('dow', $todayDow);
$hourRow = $DB->getOne('shop_hours_t', ['bt_type','start_time','end_time']);

if ($hourRow) {
    $bt = strtoupper(trim((string)($hourRow['bt_type'] ?? 'CLOSE')));
    if ($bt === 'OPEN') {
        $st = trim((string)($hourRow['start_time'] ?? ''));
        $et = trim((string)($hourRow['end_time'] ?? ''));
        if ($st !== '' && $et !== '') {
            $todayHoursText = substr($st, 0, 5) . '~' . substr($et, 0, 5);
        }
    }
}

$isQr     = !empty($_SESSION['is_qr_order']) && !empty($_SESSION['qr_token']) && !empty($_SESSION['table_no']);
$_TODAY_HOURS_TEXT = $todayHoursText;

$img = '../../data/menu/'.$item['sm_image'];

$reviewSummary = [
    'review_count' => 0,
    'avg_score'    => 0,
];

if ($shopId > 0 && !empty($sm_idx)) {
    $DB->join('review_menu_t rm', 'rm.rv_idx = r.idx', 'INNER');
    $DB->where('r.sh_idx', $shopId);
    $DB->where('rm.sm_idx', (int)$sm_idx);
    $DB->where('r.rv_show', 'Y');
    $DB->where('r.del_date', null, 'IS');
    $reviewSummary = $DB->getOne(
        'review_t r',
        'COUNT(DISTINCT r.idx) AS review_count, ROUND(IFNULL(AVG(r.rv_food_score), 0), 1) AS avg_score'
    );
}

$reviewCount    = (int)($reviewSummary['review_count'] ?? 0);
$reviewAvgScore = number_format((float)($reviewSummary['avg_score'] ?? 0), 1);
$reviewListUrl  = REVIEW_PAGE.'/list.php?sh_idx=' . $shopId . '&sm_idx=' . (int)$sm_idx;

$view_path = VIEWS_SHOP_PATH."/detail.php";
if (file_exists($view_path)) {
    include_once $view_path;
} else {

}

include_once $_SERVER['DOCUMENT_ROOT'] . "/app/inc/tail.php";
?>
