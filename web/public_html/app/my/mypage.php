<?php
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
$_ENABLED_INC_TOP = true;
$_ENABLED_INC_QUICK = true;
//$_SUB_HEAD_TITLE = "로그인";
include_once $_SERVER['DOCUMENT_ROOT']."/app/inc/head.php";
$_SUB_HEAD_TITLE = "마이페이지"; //헤더에 타이틀명이 없을경우 공백
$hd_num = 6; //모바일 hd 1~n까지 있음
$_GET['bt_menu'] = '1'; //모바일 하단메뉴 있음1, 없음 공백
include_once $_SERVER['DOCUMENT_ROOT']."/app/inc/header.php";

$mt_idx = (int)($_SESSION['mng']['mt_idx'] ?? 0);
if ($mt_idx <= 0) {
    // 비로그인 상태면 쿠폰 0개
    $availableCouponCount = 0;
} else {
    // ------------------------------
    // 사용 가능한 쿠폰 개수 계산
    // ------------------------------
    $today = date('Y-m-d');

    // 1. 모든 쿠폰 후보 조회 (전체 매장 + 모든 매장 공통)
    $DB->where('(sh_idx = 0 OR sh_idx > 0)'); // sh_idx = 0: 전체, 아니면 특정 매장
    $DB->where('ct_show', 'Y');
    $DB->where('ct_del_yn', 'N');

    // 기간 유효성
    $DB->where('(
        (ct_type1 = 1 AND (ct_sdate IS NULL OR ct_sdate <= ?) AND (ct_edate IS NULL OR ct_edate >= ?))
        OR
        (ct_type1 = 2 AND ct_days >= 0)
    )', [$today, $today]);

    $DB->orderBy('idx', 'DESC');
    $allCoupons = $DB->get('coupon_t', null, ['idx', 'ct_target_scope', 'ct_target_members']);

    // 2. 이미 사용한 쿠폰 ID 목록
    $usedCouponIds = [];
    $DB->where('mt_idx', $mt_idx);
    $DB->where('cl_view', 'Y');
    $usedLogs = $DB->get('coupon_log_t', null, ['ct_idx']);
    if ($usedLogs) {
        $usedCouponIds = array_column($usedLogs, 'ct_idx');
        $usedCouponIds = array_map('intval', $usedCouponIds);
    }

    // 3. 유효한 쿠폰만 카운트
    $availableCouponCount = 0;
    foreach ($allCoupons as $c) {
        $ct_idx = (int)$c['idx'];

        // 이미 사용한 쿠폰 제외
        if (in_array($ct_idx, $usedCouponIds, true)) {
            continue;
        }

        // 회원 전용 쿠폰 체크
        $scope = $c['ct_target_scope'] ?? 'ALL';
        if ($scope === 'MEMBER') {
            $membersCsv = trim($c['ct_target_members'] ?? '');
            if ($membersCsv !== '') {
                $allowed = array_map('intval', array_filter(array_map('trim', explode(',', $membersCsv))));
                if (!in_array($mt_idx, $allowed, true)) {
                    continue;
                }
            }
        }

        $availableCouponCount++;
    }
}

$DB->where('idx',$_SESSION['mng']['mt_idx']);
$mb = $DB->getOne('member_t','mt_type');

if($mb){
    if($mb['mt_type'] === 1) $link = './mypass.php';
    else $link = './myinfo.php';
}

$view_path = VIEWS_MY_PATH."/mypage.php";
if (file_exists($view_path)) {
    include_once $view_path;
} else {

}

include_once $_SERVER['DOCUMENT_ROOT'] . "/app/inc/tail.php";
?>
