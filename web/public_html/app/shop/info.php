<?php
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
$_ENABLED_INC_TOP = true;
$_ENABLED_INC_QUICK = true;

include_once $_SERVER['DOCUMENT_ROOT']."/app/inc/head.php";
$_SUB_HEAD_TITLE = "매장정보";
$hd_num = 2;
$_GET['bt_menu'] = '';
include_once $_SERVER['DOCUMENT_ROOT']."/app/inc/header.php";

// -----------------------------
// 1) 파라미터 검증
// -----------------------------
$sh_idx = (int)($_GET['sh_idx'] ?? 0);
if ($sh_idx <= 0) {
    alert('잘못된 접근입니다.', '/');
    exit;
}

// -----------------------------
// 2) 매장 기본 정보 조회 (shop_t 기준)
// -----------------------------
$DB->where('idx', $sh_idx);
$DB->where('del_date', null, 'IS');          // ✅ 안전한 NULL 체크
// 필요하면 노출중인 매장만
// $DB->where('sh_show', 'Y');

$shop = $DB->getOne('shop_t', [
    'idx', 'sh_title', 'sh_branch_nm', 'sh_addr1', 'sh_addr2',
    'sh_lat', 'sh_lng',
    'sh_img1', 'sh_img2', 'sh_img3', 'sh_img4', 'sh_img5', // ✅ 여기
    'sh_tel',
]);

if (!$shop) {
    alert('매장 정보를 찾을 수 없습니다.', '/');
    exit;
}

// -----------------------------
// 3) 기본 가공
// -----------------------------
$shopId   = (int)$shop['idx'];
$fullName = trim(($shop['sh_title'] ?? '') . (!empty($shop['sh_branch_nm']) ? " [{$shop['sh_branch_nm']}]" : ''));
$fullAddr = trim(trim((string)($shop['sh_addr1'] ?? '')) . ' ' . trim((string)($shop['sh_addr2'] ?? '')));
$lat      = (float)($shop['sh_lat'] ?? 0);
$lng      = (float)($shop['sh_lng'] ?? 0);
$tel      = trim((string)($shop['sh_tel'] ?? ''));
$tel      = $tel !== '' ? $tel : '-';

// 이미지 URL 조합 (PHP버전 호환)
// 이미지 URL 조합
function shopImgUrl($shopId, $val) {
    $val = trim((string)$val);
    if ($val === '') return '';
    if (preg_match('#^https?://#i', $val)) return $val;
    if (substr($val, 0, 1) === '/') return $val;
    return '/data/shop/'.$shopId.'/rs_'.$val;
}

$shopImages = [];
// ✅ re_ prefix + 5개로 변경
foreach (['sh_img1','sh_img2','sh_img3','sh_img4','sh_img5'] as $k) {
    $u = shopImgUrl($shopId, $shop[$k] ?? '');
    if ($u) $shopImages[] = $u;
}


// -----------------------------
// 4) 운영시간/휴무일 계산 (현재 구조 기준)
//   - shop_hours_t : OPEN/CLOSE + start_time/end_time
//   - shop_break_t : 1행 (있으면 표시)
//   - shop_temp_holiday_t : 기간 휴무
// -----------------------------
$dowNames = ['일', '월', '화', '수', '목', '금', '토'];
$daysOrder = [1,2,3,4,5,6,0]; // 월~일 순서로 노출

// 4-1) 운영시간 로딩 (0~6)
$DB->where('sh_idx', $shopId);
$DB->orderBy('dow', 'ASC');
$rows = $DB->get('shop_hours_t', null, ['dow','bt_type','start_time','end_time']);

$week = [];
for ($i=0; $i<=6; $i++) {
    $week[$i] = [
        'dow' => $i,
        'bt_type' => 'CLOSE',
        'start_time' => null,
        'end_time' => null,
    ];
}

if ($rows) {
    foreach ($rows as $r) {
        $d = (int)($r['dow'] ?? -1);
        if ($d < 0 || $d > 6) continue;

        $bt = strtoupper(trim((string)($r['bt_type'] ?? 'CLOSE')));
        $bt = ($bt === 'OPEN') ? 'OPEN' : 'CLOSE';

        $week[$d] = [
            'dow' => $d,
            'bt_type' => $bt,
            'start_time' => $r['start_time'] ?? null,
            'end_time' => $r['end_time'] ?? null,
        ];
    }
}

// 4-2) 브레이크 타임(있으면 표시)
$DB->where('sh_idx', $shopId);
$bk = $DB->getOne('shop_break_t', ['start_time','end_time']);
$breakText = '';
if ($bk && !empty($bk['start_time']) && !empty($bk['end_time'])) {
    $breakText = substr($bk['start_time'], 0, 5) . '~' . substr($bk['end_time'], 0, 5);
}

// 4-3) 운영시간 텍스트 만들기
$openLines = [];
$closedDays = [];

foreach ($daysOrder as $d) {
    $name = $dowNames[$d];
    $it = $week[$d] ?? null;

    if (!$it || $it['bt_type'] !== 'OPEN') {
        $closedDays[] = $name;
        continue;
    }

    $st = (string)($it['start_time'] ?? '');
    $et = (string)($it['end_time'] ?? '');

    if ($st === '' || $et === '') {
        $closedDays[] = $name;
        continue;
    }

    $line = $name . ' ' . substr($st,0,5) . '~' . substr($et,0,5);
    if ($breakText !== '') {
        $line .= ' (브레이크 ' . $breakText . ')';
    }
    $openLines[] = $line;
}

$openTime = $openLines ? implode("\n", $openLines) : '-';

// 4-4) 휴무일(정기휴무 + 임시휴무)
$holidayParts = [];

if ($closedDays) {
    // "매주 월,화,..." 형태
    $holidayParts[] = '매주 ' . implode(', ', $closedDays);
}

// 임시휴무
$today = date('Y-m-d');
$DB->where('sh_idx', $shopId);
$DB->where('use_yn', 'Y');
$DB->where('end_date', $today, '>=');
$DB->orderBy('start_date', 'ASC');
$tempRows = $DB->get('shop_temp_holiday_t', null, ['start_date','end_date','memo']);

if ($tempRows) {
    $tmp = [];
    foreach ($tempRows as $th) {
        $sd = (string)$th['start_date'];
        $ed = (string)$th['end_date'];
        $memo = trim((string)($th['memo'] ?? ''));
        $period = ($sd === $ed) ? $sd : ($sd . '~' . $ed);
        if ($memo !== '') $period .= ' (' . $memo . ')';
        $tmp[] = $period;
    }
    if ($tmp) $holidayParts[] = '임시휴무: ' . implode(' / ', $tmp);
}

$holiday = $holidayParts ? implode("\n", $holidayParts) : '없음';

// -----------------------------
// 5) 뷰로 전달
// -----------------------------
$_SHOP             = $shop;
$_SHOP_ID          = $shopId;
$_SHOP_NAME        = $fullName;
$_SHOP_ADDR        = $fullAddr;
$_SHOP_LAT         = $lat;
$_SHOP_LNG         = $lng;
$_SHOP_IMAGES      = $shopImages;
$_SHOP_OPEN_TIME   = $openTime;
$_SHOP_HOLIDAY     = $holiday;
$_SHOP_TEL         = $tel;

// -----------------------------
// 6) 뷰 포함
// -----------------------------
$view_path = VIEWS_SHOP_PATH."/info.php";
if (file_exists($view_path)) {
    include_once $view_path;
}

include_once $_SERVER['DOCUMENT_ROOT'] . "/app/inc/tail.php";
?>
