<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/cfg/lib.inc.php";

function my_review_redirect($message, $url = '/')
{
    echo '<script>';
    echo 'alert(' . json_encode($message, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ');';
    echo 'location.replace(' . json_encode($url, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ');';
    echo '</script>';
    exit;
}

function my_review_get_login_member_id()
{
    if (!empty($_SESSION['mng']['mt_idx'])) {
        return (int)$_SESSION['mng']['mt_idx'];
    }

    if (!empty($_SESSION['mng']['idx'])) {
        return (int)$_SESSION['mng']['idx'];
    }

    if (!empty($_SESSION['member']['mt_idx'])) {
        return (int)$_SESSION['member']['mt_idx'];
    }

    if (!empty($_SESSION['member']['idx'])) {
        return (int)$_SESSION['member']['idx'];
    }

    return 0;
}

function my_review_build_review_image_src($reviewId, $file)
{
    $file = trim((string)$file);

    if ($file === '') {
        return '';
    }

    if (strpos($file, 'http://') === 0 || strpos($file, 'https://') === 0 || strpos($file, '/') === 0) {
        return $file;
    }

    return '/data/review/' . $reviewId . '/' . $file;
}

function my_review_human_date($datetime)
{
    $ts = strtotime((string)$datetime);
    if (!$ts) {
        return '';
    }

    $today = strtotime(date('Y-m-d'));
    $target = strtotime(date('Y-m-d', $ts));
    $diffDays = (int)(($today - $target) / 86400);

    if ($diffDays <= 0) {
        return '오늘';
    }

    if ($diffDays <= 6) {
        return $diffDays . '일 전';
    }

    return date('Y.m.d', $ts);
}

function my_review_fetch_page($DB, $memberId, $page = 1, $limit = 10)
{
    $page   = max(1, (int)$page);
    $limit  = max(1, min(30, (int)$limit));
    $offset = ($page - 1) * $limit;

    $DB->where('mt_idx', $memberId);
    $DB->where('rv_show', 'Y');
    $DB->where('del_date', null, 'IS');
    $DB->orderBy('rv_wdate', 'DESC');
    $DB->orderBy('idx', 'DESC');
    $idRows = $DB->get('review_t', [$offset, $limit + 1], ['idx AS rv_idx']);

    $reviewIds = [];
    foreach ((array)$idRows as $idRow) {
        $reviewIds[] = (int)$idRow['rv_idx'];
    }

    $hasMore = count($reviewIds) > $limit;
    if ($hasMore) {
        array_pop($reviewIds);
    }

    if (empty($reviewIds)) {
        return [
            'items'   => [],
            'hasMore' => false,
        ];
    }

    $placeholders = implode(',', array_fill(0, count($reviewIds), '?'));

    $reviewRows = $DB->rawQuery("
        SELECT
            r.idx AS rv_idx,
            r.rv_food_score,
            r.rv_contents,
            r.rv_wdate,
            r.sh_idx,
            s.sh_title,
            s.sh_branch_nm
        FROM review_t r
        LEFT JOIN shop_t s ON s.idx = r.sh_idx
        WHERE r.idx IN ({$placeholders})
    ", $reviewIds);

    $reviewMap = [];
    foreach ((array)$reviewRows as $reviewRow) {
        $reviewMap[(int)$reviewRow['rv_idx']] = $reviewRow;
    }

    $imageRows = $DB->rawQuery("
        SELECT
            rv_idx,
            ri_file,
            ri_order
        FROM review_image_t
        WHERE rv_idx IN ({$placeholders})
        ORDER BY rv_idx ASC, ri_order ASC, idx ASC
    ", $reviewIds);

    $imageMap = [];
    foreach ((array)$imageRows as $imageRow) {
        $rvIdx = (int)$imageRow['rv_idx'];
        $src = my_review_build_review_image_src($rvIdx, $imageRow['ri_file'] ?? '');
        if ($src !== '') {
            $imageMap[$rvIdx][] = $src;
        }
    }

    $menuRows = $DB->rawQuery("
        SELECT
            rv_idx,
            rm_menu_name
        FROM review_menu_t
        WHERE rv_idx IN ({$placeholders})
        ORDER BY rv_idx ASC, rm_order ASC, idx ASC
    ", $reviewIds);

    $menuMap = [];
    foreach ((array)$menuRows as $menuRow) {
        $rvIdx = (int)$menuRow['rv_idx'];
        $menuName = trim((string)($menuRow['rm_menu_name'] ?? ''));
        if ($menuName === '') {
            continue;
        }

        if (!isset($menuMap[$rvIdx])) {
            $menuMap[$rvIdx] = [];
        }

        if (!in_array($menuName, $menuMap[$rvIdx], true)) {
            $menuMap[$rvIdx][] = $menuName;
        }
    }

    $items = [];
    foreach ($reviewIds as $reviewId) {
        if (empty($reviewMap[$reviewId])) {
            continue;
        }

        $review = $reviewMap[$reviewId];
        $storeName = trim((string)($review['sh_title'] ?? '') . (string)($review['sh_branch_nm'] ?? ''));
        if ($storeName === '') {
            $storeName = '매장 정보 없음';
        }

        $items[] = [
            'rv_idx'      => (int)$review['rv_idx'],
            'store_name'  => $storeName,
            'score'       => (int)$review['rv_food_score'],
            'content'     => (string)($review['rv_contents'] ?? ''),
            'date_label'  => my_review_human_date($review['rv_wdate'] ?? ''),
            'images'      => $imageMap[$reviewId] ?? [],
            'menus'       => $menuMap[$reviewId] ?? [],
        ];
    }

    return [
        'items'   => $items,
        'hasMore' => $hasMore,
    ];
}

$memberId = my_review_get_login_member_id();
if ($memberId < 1) {
    my_review_redirect('로그인이 필요합니다.', '/auth/login.php');
}

$DB->where('mt_idx', $memberId);
$DB->where('rv_show', 'Y');
$DB->where('del_date', null, 'IS');
$totalReviewCount = (int)$DB->getValue('review_t', 'count(*)');

$pageSize = 10;
$initialPage = my_review_fetch_page($DB, $memberId, 1, $pageSize);

$_ENABLED_INC_TOP = true;
$_ENABLED_INC_QUICK = true;
$_SUB_HEAD_TITLE = "내가 작성한 리뷰";
$hd_num = 2;
$_GET['bt_menu'] = '';

$_MY_REVIEW_TOTAL = $totalReviewCount;
$_MY_REVIEW_ITEMS = $initialPage['items'];
$_MY_REVIEW_HAS_MORE = $initialPage['hasMore'];
$_MY_REVIEW_PAGE_SIZE = $pageSize;
$_MY_REVIEW_API_URL = REVIEW_ACTIONS . '/update.php';

include_once $_SERVER['DOCUMENT_ROOT'] . "/app/inc/head.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/app/inc/header.php";

$view_path = VIEWS_REVIEW_PATH . "/my.php";
if (file_exists($view_path)) {
    include_once $view_path;
}

include_once $_SERVER['DOCUMENT_ROOT'] . "/app/inc/tail.php";
?>
