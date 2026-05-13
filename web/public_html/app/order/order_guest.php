<?php
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
$_ENABLED_INC_TOP = true;
$_ENABLED_INC_QUICK = true;

include_once $_SERVER['DOCUMENT_ROOT']."/app/inc/head.php";
$_SUB_HEAD_TITLE = "주문 내역";
$hd_num = 2;
$_GET['bt_menu'] = '';
include_once $_SERVER['DOCUMENT_ROOT']."/app/inc/header.php";

/* =========================================================
 * 로그인 체크
 * ========================================================= */
$mt_idx = (int)($_SESSION['mng']['mt_idx'] ?? 0);
//if ($mt_idx <= 0) {
//    alert('로그인이 필요합니다.', '/member/login.php');
//}

/* =========================================================
 * 파라미터 및 방문 ID 결정
 * ========================================================= */
$tv_idx_param = (int)($_GET['tv_idx'] ?? 0); // 내역 리스트에서 접근 시

$visitId = 0;
$shopTitleFull = '매장';
$shopImg = DESIGN_HTTP . '/img/pr_sample01.jpg';
$tableNoDisplay = '';

if ($tv_idx_param > 0) {
    // 과거 내역 조회 (내역 리스트 클릭)
    $visitId = $tv_idx_param;
} else {
    // 현재 QR 세션 조회
    $token = $_SESSION['visit_token'] ?? $_COOKIE['visit_token'] ?? '';
    if ($token && !empty($_SESSION['qr_token'])) {
        $decoded = decrypt_member_id($_SESSION['qr_token']);
        if ($decoded) {
            $parts = explode('/', $decoded);
            $shopId = (int)($parts[0] ?? 0);
            $tableNo = trim($parts[1] ?? '');

            if ($shopId > 0 && $tableNo !== '') {
                $ttlCut = date('Y-m-d H:i:s', time() - (180 * 60));

                $DB->where('sh_idx', $shopId);
                $DB->where('tv_table', $tableNo);
                $DB->where('visit_key', $token);
                $DB->where('tv_ended', null, 'IS');
                $DB->where('tv_last_active', $ttlCut, '>=');

                $visit = $DB->getOne('table_visit_t', ['idx']);
                $visitId = !empty($visit['idx']) ? (int)$visit['idx'] : 0;
            }
        }
    }
}

/* =========================================================
 * 매장 정보 헬퍼 함수
 * ========================================================= */
function getShopInfo($DB, $sh_idx) {
    if ($sh_idx <= 0) {
        return ['full' => '매장', 'img' => DESIGN_HTTP . '/img/pr_sample01.jpg'];
    }

    static $cache = [];
    if (isset($cache[$sh_idx])) return $cache[$sh_idx];

    $shop = $DB->where('idx', $sh_idx)->getOne('shop_t', ['sh_title', 'sh_branch_nm', 'sh_img1']);
    $title = trim($shop['sh_title'] ?? '매장');
    $branch = trim($shop['sh_branch_nm'] ?? '');
    $full = $title . ($branch ? " [{$branch}]" : '');
    $img = !empty($shop['sh_img1']) ? $shop['sh_img1'] : DESIGN_HTTP . '/img/pr_sample01.jpg';

    $cache[$sh_idx] = ['full' => $full, 'img' => $img];
    return $cache[$sh_idx];
}

/* =========================================================
 * 주문 내역 조회
 * ========================================================= */
$orders = [];
$totalMenuQty = 0;
$sumSubTotal = 0;
$sumDiscount = 0;
$sumFinal = 0;

if ($visitId > 0) {
    $DB->where('tv_idx', $visitId);
    $DB->where('ot_status', 'CANCELLED', '!=');
    $DB->orderBy('ot_wdate', 'DESC');

    $rows = $DB->get('orders_t', null, [
        'idx', 'ot_number', 'ot_table', 'ot_total_price', 'ot_discount_amount',
        'ct_snapshot', 'ot_wdate', 'ot_status', 'ot_pay_status', 'sh_idx'
    ]);

    foreach ($rows as $r) {
        $snapshot = json_decode($r['ct_snapshot'] ?? '', true) ?: [];
        $items = $snapshot['items'] ?? [];

        $menuQty = 0;
        $normItems = [];

        foreach ($items as $it) {
            $q = max(1, (int)($it['quantity'] ?? 1));
            $menuQty += $q;

            $normItems[] = [
                'menu_name'   => $it['menu_name'] ?? '메뉴',
                'quantity'    => $q,
                'unit_price'  => (int)($it['unit_price'] ?? 0),
                'total_price' => (int)($it['total_price'] ?? 0),
                'options'     => $it['options'] ?? [],
            ];
        }

        $sub = (int)$r['ot_total_price'];
        $dis = (int)$r['ot_discount_amount'];
        $dis = max(0, min($dis, $sub));
        $fin = $sub - $dis;

        $totalMenuQty += $menuQty;
        $sumSubTotal += $sub;
        $sumDiscount += $dis;
        $sumFinal += $fin;

        $orders[] = [
            'idx'           => (int)$r['idx'],
            'ot_number'     => $r['ot_number'] ?? '',
            'ot_table'      => $r['ot_table'] ?? '',
            'ot_wdate'      => $r['ot_wdate'] ?? '',
            'ot_status'     => $r['ot_status'] ?? 'PENDING',
            'ot_pay_status' => $r['ot_pay_status'] ?? 'UNPAID',
            'sub_total'     => $sub,
            'discount'      => $dis,
            'final_total'   => $fin,
            'menu_qty'      => $menuQty,
            'items'         => $normItems,
        ];

        // 테이블 번호
        if (empty($tableNoDisplay) && !empty($r['ot_table'])) {
            $DB->where('tb_no',$r['ot_table']);
            $otTableRaw = $DB->getOne('shop_table_t', 'tb_no, tb_name');
            $tableNoDisplay = $otTableRaw['tb_name'];
        }

        // 매장 정보 (sh_idx 있으면 업데이트)
        if (!empty($r['sh_idx'])) {
            $shopInfo = getShopInfo($DB, (int)$r['sh_idx']);
            $shopTitleFull = $shopInfo['full'];
            $shopImg = "/data/shop/{$r['sh_idx']}/rs_{$shopInfo['img']}";
        }
    }
}

$isQrOrder = ($visitId > 0);
$isNoOrder = empty($orders);

/* =========================================================
 * 뷰 호출
 * ========================================================= */
$view_path = VIEWS_ORDER_PATH . "/order_guest.php";
if (file_exists($view_path)) {
    include_once $view_path;
}

include_once $_SERVER['DOCUMENT_ROOT'] . "/app/inc/tail.php";
?>
