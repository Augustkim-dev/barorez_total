<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/cfg/lib.inc.php";

function reviewRedirect($message, $url = '/order/history.php') {
    echo '<script>';
    echo 'alert(' . json_encode($message, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ');';
    echo 'location.replace(' . json_encode($url, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ');';
    echo '</script>';
    exit;
}

function pickShopImage($shopId, array $shopRow) {
    foreach (['sh_img1', 'sh_img2', 'sh_img3', 'sh_img4', 'sh_img5'] as $field) {
        $file = trim((string)($shopRow[$field] ?? ''));
        if ($file !== '') {
            return '/data/shop/' . $shopId . '/rs_' . $file;
        }
    }

    return DESIGN_HTTP . '/img/pr_sample01.jpg';
}

function snapshotOptionsToText($options) {
    if (!is_array($options) || empty($options)) {
        return '';
    }

    $parts = [];

    foreach ($options as $opt) {
        if (is_string($opt)) {
            $text = trim($opt);
            if ($text !== '') {
                $parts[] = $text;
            }
            continue;
        }

        if (!is_array($opt)) {
            continue;
        }

        $name  = trim((string)($opt['name'] ?? $opt['option_name'] ?? $opt['group_name'] ?? ''));
        $value = trim((string)($opt['value'] ?? $opt['option_value'] ?? $opt['item_name'] ?? $opt['label'] ?? ''));

        if ($name !== '' && $value !== '') {
            $parts[] = $name . ': ' . $value;
        } elseif ($value !== '') {
            $parts[] = $value;
        } elseif ($name !== '') {
            $parts[] = $name;
        }
    }

    return implode(' / ', $parts);
}

$ot_idx = (int)($_GET['ot_idx'] ?? 0);
$sh_idx = (int)($_GET['sh_idx'] ?? 0);

if ($ot_idx < 1 || $sh_idx < 1) {
    reviewRedirect('잘못된 접근입니다.');
}

/*
 * 실제 세션 구조에 맞게 주문 소유자 검증도 꼭 추가해주세요.
 * 예)
 * if (!empty($_SESSION['mng']['idx'])) {
 *     $DB->where('o.mt_idx', (int)$_SESSION['mng']['idx']);
 * }
 */

$DB->join('shop_t s', 'o.sh_idx = s.idx', 'INNER');
$DB->where('o.idx', $ot_idx);
$DB->where('o.sh_idx', $sh_idx);
$DB->where('o.ot_status', 'COMPLETED');

$orderData = $DB->getOne('orders_t o', [
    'o.idx AS ot_idx',
    'o.mt_idx',
    'o.sh_idx',
    'o.ot_number',
    'o.ot_status',
    'o.ot_total_price',
    'o.ot_pay_type',
    'o.ot_pay_status',
    'o.ot_pay_date',
    'o.ot_completed_at',
    'o.ot_wdate',
    'o.ct_snapshot',

    's.idx AS shop_idx',
    's.sh_title',
    's.sh_branch_nm',
    's.sh_addr1',
    's.sh_addr2',
    's.sh_img1',
    's.sh_img2',
    's.sh_img3',
    's.sh_img4',
    's.sh_img5',
]);

if (empty($orderData)) {
    reviewRedirect('완료된 주문 정보를 찾을 수 없습니다.');
}

$shopRow = [
    'idx'          => (int)($orderData['shop_idx'] ?? 0),
    'sh_title'     => $orderData['sh_title'] ?? '',
    'sh_branch_nm' => $orderData['sh_branch_nm'] ?? '',
    'sh_addr1'     => $orderData['sh_addr1'] ?? '',
    'sh_addr2'     => $orderData['sh_addr2'] ?? '',
    'sh_img1'      => $orderData['sh_img1'] ?? '',
    'sh_img2'      => $orderData['sh_img2'] ?? '',
    'sh_img3'      => $orderData['sh_img3'] ?? '',
    'sh_img4'      => $orderData['sh_img4'] ?? '',
    'sh_img5'      => $orderData['sh_img5'] ?? '',
];

$shopImg = pickShopImage($sh_idx, $orderData);

$ctSnapshot = json_decode((string)($orderData['ct_snapshot'] ?? ''), true);
if (!is_array($ctSnapshot)) {
    $ctSnapshot = [];
}

$snapshotItems = [];
if (!empty($ctSnapshot['items']) && is_array($ctSnapshot['items'])) {
    $snapshotItems = $ctSnapshot['items'];
}

$menuIds = [];
foreach ($snapshotItems as $item) {
    $smId = (int)($item['sm_id'] ?? 0);
    if ($smId > 0) {
        $menuIds[$smId] = $smId;
    }
}

$menuMetaMap = [];
if (!empty($menuIds)) {
    $DB->where('idx', array_values($menuIds), 'IN');
    $menuRows = $DB->get('shop_menu_t', null, [
        'idx',
        'sm_title',
        'sm_image',
        'sm_contents'
    ]);

    foreach ((array)$menuRows as $menu) {
        $menuMetaMap[(int)$menu['idx']] = $menu;
    }
}

$orderedMenus = [];
foreach ($snapshotItems as $item) {
    $smId       = (int)($item['sm_id'] ?? 0);
    $menuMeta   = $menuMetaMap[$smId] ?? [];
    $menuTitle  = trim((string)($item['menu_name'] ?? $menuMeta['sm_title'] ?? '메뉴'));
    $menuImage  = trim((string)($menuMeta['sm_image'] ?? ''));
    $quantity   = (int)($item['quantity'] ?? 0);
    $optionText = snapshotOptionsToText($item['options'] ?? []);

    $descParts = [];
    if ($optionText !== '') {
        $descParts[] = $optionText;
    }
    if ($quantity > 0) {
        $descParts[] = '수량 ' . number_format($quantity) . '개';
    }

    $orderedMenus[] = [
        'idx'         => $smId,
        'title'       => $menuTitle,
        'option_text' => implode(' / ', $descParts),
        'image'       => $menuImage !== '' ? '/data/menu/' . ltrim($menuImage, '/') : '',
        'quantity'    => $quantity,
        'unit_price'  => (int)($item['unit_price'] ?? 0),
        'total_price' => (int)($item['total_price'] ?? 0),
    ];
}

/*
 * review_write.php 뷰에서 바로 쓰기 쉽게 변수 세팅
 */
$_SHOP_ID        = $sh_idx;
$_SHOP_ROW       = $shopRow;
$_SHOP_IMG       = $shopImg;
$_ORDER_ID       = $ot_idx;
$_ORDER_ROW      = $orderData;
$_ORDER_MENUS    = $orderedMenus;
$_ORDER_SNAPSHOT = $ctSnapshot;

$shopId       = $_SHOP_ID;
$row          = $_SHOP_ROW;
$shopImg      = $_SHOP_IMG;
$orderId      = $_ORDER_ID;
$orderRow     = $_ORDER_ROW;
$orderedMenus = $_ORDER_MENUS;

/*
 * 액션 URL이 있으면 같이 내려주세요.
 * 실제 프로젝트 상수명에 맞게 바꾸시면 됩니다.
 */
// $_REVIEW_ACTION = REVIEW_ACTIONS . '/update.php';

$_ENABLED_INC_TOP   = true;
$_ENABLED_INC_QUICK = true;
$_SUB_HEAD_TITLE    = "리뷰작성";
$hd_num             = 2;
$_GET['bt_menu']    = '';

include_once $_SERVER['DOCUMENT_ROOT'] . "/app/inc/head.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/app/inc/header.php";

$view_path = VIEWS_REVIEW_PATH . "/write.php";
if (file_exists($view_path)) {
    include_once $view_path;
}

include_once $_SERVER['DOCUMENT_ROOT'] . "/app/inc/tail.php";
?>
