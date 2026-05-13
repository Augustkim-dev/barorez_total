<?php
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
$_ENABLED_INC_TOP = true;
$_ENABLED_INC_QUICK = true;
include_once $_SERVER['DOCUMENT_ROOT']."/app/inc/head.php";
$_SUB_HEAD_TITLE = "예약하기";
$hd_num = 2;
$_GET['bt_menu'] = '';
include_once $_SERVER['DOCUMENT_ROOT']."/app/inc/header.php";

$sh_idx = (int)($_SESSION['current_sh_idx'] ?? 0);
if ($sh_idx <= 0) {
    echo "<script>alert('매장 정보가 없습니다.'); history.back();</script>";
    exit;
}

// 1. 매장 예약 설정 가져오기
$DB->where('sh_idx', $sh_idx);
$reserveSetting = $DB->getOne('shop_reserve_setting_t', [
    'idx','rs_allow_same_day', 'rs_max_reserve_days', 'rs_min_person', 'rs_max_person'
]);

$allowSameDay     = ($reserveSetting['rs_allow_same_day'] ?? 'Y') === 'Y';
$maxReserveDays   = (int)($reserveSetting['rs_max_reserve_days'] ?? 0);
$minPerson        = (int)($reserveSetting['rs_min_person'] ?? 1);
$maxPerson        = (int)($reserveSetting['rs_max_person'] ?? 20);

// 2. 요일별 휴무일 체크 (shop_hours_t)
$closedDays = [];
$DB->where('sh_idx', $sh_idx);
$hours = $DB->get('shop_hours_t', null, ['dow', 'bt_type']);
foreach ($hours as $h) {
    if ($h['bt_type'] === 'CLOSE') {
        $closedDays[] = (int)$h['dow'];
    }
}

// 3. 요일별 예약 가능 슬롯 + 최대 인원 (shop_reserve_slot_t)
$slotsByDayType = [];
$DB->where('rs_idx', $reserveSetting['idx'] ?? 0);
$DB->where('slot_use', 'Y');
$DB->orderBy('slot_sort', 'ASC');
$slots = $DB->get('shop_reserve_slot_t', null, [
    'slot_day_type', 'slot_hour', 'slot_minute', 'slot_max_count'
]);

foreach ($slots as $s) {
    $dayType = $s['slot_day_type'];
    $hour    = (int)$s['slot_hour'];
    $min     = (int)$s['slot_minute'];
    $timeStr = sprintf("%02d:%02d", $hour, $min);

    if (!isset($slotsByDayType[$dayType])) {
        $slotsByDayType[$dayType] = [];
    }
    $slotsByDayType[$dayType][] = [
        'time' => $timeStr,
        'max_count' => (int)$s['slot_max_count']
    ];
}

$DB->where('sh_idx', $sh_idx);
$rsvMsg = $DB->getOne('shop_reserve_setting_t');

// 오늘 날짜 및 현재 시간
$todayStr    = date('Y-m-d');
$currentTime = date('H:i');

// JSON으로 프론트에 전달
$slotsJson      = json_encode($slotsByDayType, JSON_UNESCAPED_UNICODE);
$closedDaysJson = json_encode($closedDays);

$view_path = VIEWS_RSRV_PATH."/rsrv.php";
if (file_exists($view_path)) {
    include_once $view_path;
}

include_once $_SERVER['DOCUMENT_ROOT'] . "/app/inc/tail.php";
?>
