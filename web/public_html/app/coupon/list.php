<?php
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
$_ENABLED_INC_TOP = true;
$_ENABLED_INC_QUICK = true;

include_once $_SERVER['DOCUMENT_ROOT']."/app/inc/head.php";
$_SUB_HEAD_TITLE = "쿠폰";
$hd_num = 2;
$_GET['bt_menu'] = '';
include_once $_SERVER['DOCUMENT_ROOT']."/app/inc/header.php";

/* 로그인 체크 */
$mt_idx = (int)($_SESSION['mng']['mt_idx'] ?? 0);
if ($mt_idx <= 0) {
    alert('로그인이 필요합니다.', '/member/login.php');
    exit;
}

/* 유틸 */
function coupon_discount_text($c) {
    $val = (int)($c['ct_discount1'] ?? 0);
    $type2 = (int)($c['ct_type2'] ?? 1); // 1:정액, 2:정율
    return ($type2 === 2) ? number_format($val)."% 할인" : number_format($val)."원 할인";
}
function coupon_end_date($c) {
    $type1 = (int)($c['ct_type1'] ?? 1); // 1:기간, 2:발급일+N일
    if ($type1 === 1) {
        return $c['ct_edate'] ?? null;
    }
    // type1=2 인 경우: (가능하면 내 쿠폰 로그 날짜) 없으면 쿠폰 생성일(ct_wdate) 기준으로 처리
    $days = (int)($c['ct_days'] ?? 0);
    $base = $c['my_base_date'] ?? $c['ct_wdate'] ?? null;
    if (!$base || $days <= 0) return null;
    return date('Y-m-d', strtotime(date('Y-m-d', strtotime($base)) . " +{$days} day"));
}
function coupon_is_valid_now($c) {
    $today = date('Y-m-d');
    $type1 = (int)($c['ct_type1'] ?? 1);

    if ($type1 === 1) {
        $s = $c['ct_sdate'] ?? null;
        $e = $c['ct_edate'] ?? null;
        if ($s && $today < $s) return [false, '사용불가'];
        if ($e && $today > $e) return [false, '기간만료'];
        return [true, '사용가능'];
    }

    // 발급일+N일 타입
    $e = coupon_end_date($c);
    if ($e && $today > $e) return [false, '기간만료'];
    return [true, '사용가능'];
}

/*
 * 쿠폰 조회 정책
 * - coupon_t : 발행된 쿠폰
 * - coupon_log_t : 해당 회원이 사용한 쿠폰 기록(또는 발급기록이 있을 수도 있음)
 * - 대상: ALL 이거나, MEMBER + FIND_IN_SET(mt_idx, ct_target_members)
 * - 삭제/미노출 제외: ct_del_yn='N', ct_show='Y'
 */
$sql = "
SELECT
    c.idx, c.ct_code, c.sh_idx, c.ct_title, c.ct_type1, c.ct_type2,
    c.ct_discount1, c.ct_discount3, c.ct_sdate, c.ct_edate, c.ct_days,
    c.ct_show, c.ct_target_scope, c.ct_target_members, c.ct_order,
    MAX(CASE WHEN l.cl_view='Y' THEN 1 ELSE 0 END) AS is_used,
    MAX(l.cl_wdate) AS log_wdate 
FROM coupon_t c 
         LEFT JOIN coupon_log_t l 
                   ON l.ct_idx = c.idx 
                       AND l.mt_idx = ? 
WHERE c.ct_del_yn = 'N'
  AND c.ct_show = 'Y'
GROUP BY c.idx 
ORDER BY c.ct_order DESC, c.idx DESC
";

$rows = $DB->rawQuery($sql, [$mt_idx]);

$availableCoupons = [];
$inactiveCoupons  = [];

foreach ($rows as $c) {
    $isUsed = ((int)($c['is_used'] ?? 0) === 1);

    $endDate = coupon_end_date($c);
    $endText = $endDate ? date('y.m.d', strtotime($endDate))."까지" : '';

    $minPrice = (int)($c['ct_discount3'] ?? 0);
    $minText  = $minPrice > 0 ? "최소주문금액 ".number_format($minPrice)."원" : "최소주문금액 없음";

    if ($isUsed) {
        $c['_state_text'] = '사용완료';
        $c['_end_text']   = $endText;
        $c['_min_text']   = $minText;
        $inactiveCoupons[] = $c;
        continue;
    }

    // 유효기간/불가 판정
    [$okNow, $stateText] = coupon_is_valid_now($c);
    $c['_state_text'] = $stateText;      // "사용가능" / "기간만료" / "사용불가"
    $c['_end_text']   = $endText;
    $c['_min_text']   = $minText;

    if ($okNow) $availableCoupons[] = $c;
    else $inactiveCoupons[] = $c;
}

/* 뷰 호출 */
$view_path = VIEWS_COUPON_PATH."/list.php";
if (file_exists($view_path)) {
    include_once $view_path;
}

include_once $_SERVER['DOCUMENT_ROOT'] . "/app/inc/tail.php";
?>
