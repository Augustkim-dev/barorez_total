<?php
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
$_ENABLED_INC_TOP = true;
$_ENABLED_INC_QUICK = true;

include_once $_SERVER['DOCUMENT_ROOT']."/app/inc/head.php";
$_SUB_HEAD_TITLE = "예약 요청 완료";
$hd_num = 2;
$_GET['bt_menu'] = '';
include_once $_SERVER['DOCUMENT_ROOT']."/app/inc/header.php";

/* 간단 유틸 함수 */
function fmt_ymd_dot($date) {
    return $date ? date('Y.m.d', strtotime($date)) : '';
}

function fmt_hm($time) {
    if (!$time) return '';
    preg_match('/^\d{2}:\d{2}/', $time, $m);
    return $m[0] ?? $time;
}

function fmt_req_at($dt) {
    return $dt ? date('y.m.d H:i', strtotime($dt)) : '';
}

function fmt_hp($hp) {
    $hp = preg_replace('/\D/', '', $hp);
    if (preg_match('/^010(\d{4})(\d{4})$/', $hp, $m)) return "010-{$m[1]}-{$m[2]}";
    if (preg_match('/^01[1-9](\d{3,4})(\d{4})$/', $hp, $m)) return substr($hp,0,3)."-{$m[1]}-{$m[2]}";
    return $hp;
}

function menu_summary($snapshot) {
    if (!$snapshot) return '';
    $data = json_decode($snapshot, true);
    if (!is_array($data['items'] ?? [])) return '';
    $names = [];
    foreach ($data['items'] as $item) {
        if (!is_array($item)) continue;
        $name = trim($item['menu_name'] ?? $item['sm_title'] ?? $item['title'] ?? $item['name'] ?? '');
        if ($name) $names[] = $name;
    }
    $names = array_unique($names);
    if (!$names) return '';
    $show = array_slice($names, 0, 3);
    $more = count($names) - 3;
    $text = implode(', ', $show);
    if ($more > 0) $text .= " 외 {$more}개";
    return $text;
}

/* 1. 파라미터 확보 */
// ot_number 우선 (선결제 예약/포장 주문)
$ot_number = trim($_GET['ot_number'] ?? $_SESSION['last_order_ot_number'] ?? '');

// rv_idx fallback (단순 장소 예약)
$rv_idx = (int)($_GET['rv_idx'] ?? $_SESSION['last_rv_idx'] ?? 0);

$order = null;
$rsv = null;
$shop = [];
$shopId = 0;
$is_simple_reservation = false;  // 단순 예약 여부

if ($ot_number) {
    // 기존 로직: ot_number로 주문 조회
    $DB->where('ot_number', $ot_number);
    $order = $DB->getOne('orders_t');

    if (!$order) {
        echo "<script>
            alert('주문 정보를 찾을 수 없습니다.');
            history.back();
        </script>";
        exit;
    }

    $shopId = (int)$order['sh_idx'];
    $rv_idx_from_order = (int)$order['rv_idx'];

    if ($rv_idx_from_order > 0) {
        $DB->where('idx', $rv_idx_from_order);
        $rsv = $DB->getOne('reservation_t');
    }

    if($rsv){
        $is_simple_reservation = true;
    }

} elseif ($rv_idx > 0) {
    // 단순 예약: rv_idx로 직접 예약 조회
    $DB->where('idx', $rv_idx);
    $rsv = $DB->getOne('reservation_t');

    if (!$rsv) {
        echo "<script>
            alert('주문 정보를 찾을 수 없습니다.');
            history.back();
        </script>";
        exit;
    }

    $shopId = (int)$rsv['sh_idx'];
    $is_simple_reservation = true;

} else {
    echo "<script>
            alert('주문 정보를 찾을 수 없습니다.');
            history.back();
        </script>";
    exit;
}

/* 매장 정보 조회 */
if ($shopId > 0) {
    $DB->where('idx', $shopId);
    $shop = $DB->getOne('shop_t') ?: [];
}

/* 상태 및 메시지 결정 */
if ($rsv) {
    // 예약 존재 (단순 예약 or 선결제 예약)
    $status_map = [
        'PENDING'   => ['예약요청', 't_blue'],
        'CONFIRMED' => ['예약확정', 't_blue'],
        'ARRIVED'   => ['방문완료', 't_blue'],
        'CANCELLED' => ['취소', 't_blue'],
        'REJECTED'  => ['거절', 't_blue'],
    ];
    $status_key = $rsv['rv_status'] ?? 'PENDING';
    list($status_text, $status_class) = $status_map[$status_key] ?? ['예약요청', 't_blue'];
    $status_badg = 'blue';
    $status_icon = 'ic_calendar';

    $type_label = '예약';
    $main_title = '예약 요청 완료';
    $main_message = '예약 요청이 완료되었습니다.';

    // 예약번호: rv_idx 사용 (단순 예약 시 ot_number 없음)
    $display_number = $is_simple_reservation ? '예약번호 : ' . $rsv['rv_number'] : $ot_number;
    $req_at = fmt_req_at($rsv['rv_wdate'] ?? '');
    $meta_line = $display_number . ($req_at ? " | {$req_at}" : "");

} else {
    // 예약 없음 → 포장/주문만
    $status_map = [
        'PENDING'   => ['포장요청', 't_green'],
        'CONFIRMED' => ['확인완료', 't_green'],
        'PREPARING' => ['준비중', 't_green'],
        'COMPLETED' => ['완료', 't_green'],
        'CANCELLED' => ['취소', 't_green'],
    ];
    $status_key = $order['ot_status'] ?? 'PENDING';
    list($status_text, $status_class) = $status_map[$status_key] ?? ['포장요청', 't_green'];
    $status_badg = 'green';
    $status_icon = 'ic_pack';

    $type_label = '포장';
    $main_title = '포장 주문 완료';
    $main_message = '포장 주문이 완료되었습니다.';

    $req_at = fmt_req_at($order['ot_wdate'] ?? '');
    $meta_line = "주문번호 : {$ot_number}" . ($req_at ? " | {$req_at}" : "");

    $DB->where('idx',$order['mt_idx']);
    $mt = $DB->getOne('member_t','mt_name, mt_hp');
}

/* 메뉴 요약 (주문 있을 때만) */
$menu_summary = $order ? menu_summary($order['ct_snapshot'] ?? '') : '';

/* 성공 플래그 */
$success = true;

/* 뷰에 전달할 변수들 (뷰에서 직접 사용) */
$full_shop_name = trim($shop['sh_title'] ?? '') . ($shop['sh_branch_nm'] ? ' [' . trim($shop['sh_branch_nm']) . ']' : '');
$shop_img = DESIGN_HTTP.'/img/pr_sample01.jpg';
if (!empty($shop['sh_img1'])) {
    $shop_img = "/data/shop/{$shopId}/rs_{$shop['sh_img1']}";
}
$shop_url = $shopId ? "../shop/list.php?sh_idx={$shopId}" : "./shop.php";

/* 뷰 포함 */
include VIEWS_RSRV_PATH."/rsrv_cmp.php";
include_once $_SERVER['DOCUMENT_ROOT']."/app/inc/tail.php";
