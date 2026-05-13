<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/cfg/lib.inc.php";

function review_list_redirect($message, $url = '/')
{
    echo '<script>';
    echo 'alert(' . json_encode($message, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ');';
    echo 'location.replace(' . json_encode($url, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ');';
    echo '</script>';
    exit;
}

function review_list_build_shop_image_src($shopId, $file)
{
    $file = trim((string)$file);

    if ($file === '') {
        return DESIGN_HTTP . '/img/pr_sample01.jpg';
    }

    if (strpos($file, 'http://') === 0 || strpos($file, 'https://') === 0 || strpos($file, '/') === 0) {
        return $file;
    }

    return '/data/shop/' . $shopId . '/rs_' . $file;
}

function review_list_build_review_image_src($reviewId, $file)
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

function review_list_human_date($datetime)
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

function review_list_fetch_summary($DB, $sh_idx, $sm_idx = 0)
{
    $sql = "
        SELECT
            COUNT(*) AS review_count,
            ROUND(IFNULL(AVG(base.rv_food_score), 0), 1) AS avg_score,
            SUM(CASE WHEN base.rv_food_score = 5 THEN 1 ELSE 0 END) AS score_5,
            SUM(CASE WHEN base.rv_food_score = 4 THEN 1 ELSE 0 END) AS score_4,
            SUM(CASE WHEN base.rv_food_score = 3 THEN 1 ELSE 0 END) AS score_3,
            SUM(CASE WHEN base.rv_food_score = 2 THEN 1 ELSE 0 END) AS score_2,
            SUM(CASE WHEN base.rv_food_score = 1 THEN 1 ELSE 0 END) AS score_1
        FROM (
            SELECT DISTINCT
                r.idx,
                r.rv_food_score
            FROM review_t r
    ";

    $params = [];

    if ($sm_idx > 0) {
        $sql .= " INNER JOIN review_menu_t rm ON rm.rv_idx = r.idx AND rm.sm_idx = ? ";
        $params[] = $sm_idx;
    }

    $sql .= "
            WHERE r.sh_idx = ?
              AND r.rv_show = 'Y'
              AND r.del_date IS NULL
        ) base
    ";
    $params[] = $sh_idx;

    $rows = $DB->rawQuery($sql, $params);
    return (is_array($rows) && !empty($rows[0])) ? $rows[0] : [];
}

function review_list_fetch_page($DB, $sh_idx, $sm_idx, $sort, $page = 1, $limit = 10)
{
    $page  = max(1, (int)$page);
    $limit = max(1, min(30, (int)$limit));
    $offset = ($page - 1) * $limit;

    $sort = strtolower(trim((string)$sort));
    $orderByMap = [
        'latest'      => 'r.rv_wdate DESC, r.idx DESC',
        'rating_high' => 'r.rv_food_score DESC, r.rv_wdate DESC, r.idx DESC',
        'rating_low'  => 'r.rv_food_score ASC, r.rv_wdate DESC, r.idx DESC',
    ];
    $orderBySql = $orderByMap[$sort] ?? $orderByMap['latest'];

    $idSql = "
        SELECT DISTINCT
            r.idx AS rv_idx
        FROM review_t r
    ";

    $params = [];

    if ($sm_idx > 0) {
        $idSql .= " INNER JOIN review_menu_t rmf ON rmf.rv_idx = r.idx AND rmf.sm_idx = ? ";
        $params[] = $sm_idx;
    }

    $idSql .= "
        WHERE r.sh_idx = ?
          AND r.rv_show = 'Y'
          AND r.del_date IS NULL
        ORDER BY {$orderBySql}
        LIMIT {$offset}, " . ($limit + 1);

    $params[] = $sh_idx;

    $idRows = $DB->rawQuery($idSql, $params);

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
            'items' => [],
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
        r.mt_idx,
        m.mt_name
    FROM review_t r
    LEFT JOIN member_t m ON m.idx = r.mt_idx
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
        $src = review_list_build_review_image_src($rvIdx, $imageRow['ri_file'] ?? '');
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
        $items[] = [
            'rv_idx'      => (int)$review['rv_idx'],
            'writer_name' => trim((string)($review['mt_name'] ?? '')) !== '' ? trim((string)$review['mt_name']) : '방문 고객',
            'score'       => (int)$review['rv_food_score'],
            'content'     => (string)($review['rv_contents'] ?? ''),
            'date_label'  => review_list_human_date($review['rv_wdate'] ?? ''),
            'images'      => $imageMap[$reviewId] ?? [],
            'menus'       => $menuMap[$reviewId] ?? [],
        ];
    }

    return [
        'items' => $items,
        'hasMore' => $hasMore,
    ];
}

$sh_idx = (int)($_GET['sh_idx'] ?? 0);
$sm_idx = (int)($_GET['sm_idx'] ?? 0);

$sort = strtolower(trim((string)($_GET['sort'] ?? 'latest')));
$sort = in_array($sort, ['latest', 'rating_high', 'rating_low'], true) ? $sort : 'latest';

if ($sh_idx < 1) {
    review_list_redirect('잘못된 접근입니다.');
}

$DB->where('idx', $sh_idx);
$shopRow = $DB->getOne('shop_t', [
    'idx',
    'sh_title',
    'sh_branch_nm',
    'sh_addr1',
    'sh_img1'
]);

if (empty($shopRow)) {
    review_list_redirect('매장 정보를 찾을 수 없습니다.');
}

$menuRow = null;
if ($sm_idx > 0) {
    $DB->join('shop_category_t c', 'c.idx = m.sc_idx', 'INNER');
    $DB->where('m.idx', $sm_idx);
    $DB->where('c.sh_idx', $sh_idx);
    $menuRow = $DB->getOne('shop_menu_t m', [
        'm.idx',
        'm.sm_title'
    ]);

    if (empty($menuRow)) {
        review_list_redirect('메뉴 정보를 찾을 수 없습니다.', '/review/review_list.php?sh_idx=' . $sh_idx);
    }
}

$summary = review_list_fetch_summary($DB, $sh_idx, $sm_idx);
$pageSize = 10;
$initialPage = review_list_fetch_page($DB, $sh_idx, $sm_idx, $sort, 1, $pageSize);

$shopFullName = trim((string)($shopRow['sh_title'] ?? '') . (string)($shopRow['sh_branch_nm'] ?? ''));
$shopImg = review_list_build_shop_image_src($sh_idx, $shopRow['sh_img1'] ?? '');
$_REVIEW_SCOPE_TITLE = $shopFullName;
$shopFullName  = trim((string)($_REVIEW_SCOPE_TITLE ?? (($row['sh_title'] ?? '') . ($row['sh_branch_nm'] ?? ''))));

$_ENABLED_INC_TOP = true;
$_ENABLED_INC_QUICK = true;
$_SUB_HEAD_TITLE = $shopFullName;
$hd_num = 2;
$_GET['bt_menu'] = '';

$_SHOP_ID = $sh_idx;
$_SHOP_ROW = $shopRow;
$_SHOP_IMG = $shopImg;
$_MENU_ROW = $menuRow;
$_REVIEW_SUMMARY = $summary;
$_REVIEW_SORT = $sort;
$_INITIAL_REVIEWS = $initialPage['items'];
$_INITIAL_HAS_MORE = $initialPage['hasMore'];
$_REVIEW_PAGE_SIZE = $pageSize;
$_REVIEW_API_URL = REVIEW_ACTIONS . '/update.php';

include_once $_SERVER['DOCUMENT_ROOT'] . "/app/inc/head.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/app/inc/header.php";

$view_path = VIEWS_REVIEW_PATH . "/list.php";
if (file_exists($view_path)) {
    include_once $view_path;
}

include_once $_SERVER['DOCUMENT_ROOT'] . "/app/inc/tail.php";
?>
