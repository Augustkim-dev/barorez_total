<?php

include $_SERVER['DOCUMENT_ROOT'] . "/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/cfg/config.mng.inc.php";

header('Content-Type: application/json');

// 결제 완료 상태(orders_t.ot_pay_status)
$PAID_STATUS = 'PAID';

if ($_POST['act'] == 'chart') {

    $start = $_POST['start_date'] ?? date('Y-m-d', strtotime('-6 days'));
    $end   = $_POST['end_date']   ?? date('Y-m-d');

    /* ===========================
     *  1) 카드 상단 통계
     * =========================== */

    // 1-1. 총 회원 수 (정상 회원)
    $signupTotalRow = $DB->rawQueryOne("
        SELECT COUNT(*) AS cnt
        FROM member_t
        WHERE mt_level   = 2
          AND mt_status  = 'Y'
          AND del_status = 'N'
    ");
    $signupTotal = (int)($signupTotalRow['cnt'] ?? 0);

    // 1-2. 총 주문 수 (결제 완료 기준)
    $orderTotalRow = $DB->rawQueryOne("
        SELECT COUNT(*) AS cnt
        FROM orders_t
        WHERE ot_pay_status = ?
    ", [$PAID_STATUS]);
    $orderTotal = (int)($orderTotalRow['cnt'] ?? 0);

    // 1-3. 총 매출 (결제 완료 기준, 전체 기간)
    $salesTotalRow = $DB->rawQueryOne("
        SELECT COALESCE(SUM(ot_total_price), 0) AS amount
        FROM orders_t
        WHERE ot_pay_status = ?
    ", [$PAID_STATUS]);
    $salesTotal = (int)($salesTotalRow['amount'] ?? 0);

    /* ===========================
     *  2) 기간별 일자 집계
     * =========================== */

    // (1) 일자별 회원가입 수
    $signupMap = [];
    $signupResult = $DB->rawQuery("
        SELECT DATE(mt_wdate) AS date, COUNT(*) AS cnt
        FROM member_t
        WHERE mt_level   = 2
          AND mt_status  = 'Y'
          AND del_status = 'N'
          AND DATE(mt_wdate) BETWEEN ? AND ?
        GROUP BY DATE(mt_wdate)
    ", [$start, $end]);

    foreach ($signupResult as $row) {
        $signupMap[$row['date']] = (int)$row['cnt'];
    }

    // (2) 일자별 주문 수 (결제 완료 기준, 기준일: ot_pay_date)
    $orderMap = [];
    $orderResult = $DB->rawQuery("
        SELECT DATE(ot_pay_date) AS date, COUNT(*) AS cnt
        FROM orders_t
        WHERE ot_pay_status = ?
          AND ot_pay_date IS NOT NULL
          AND DATE(ot_pay_date) BETWEEN ? AND ?
        GROUP BY DATE(ot_pay_date)
    ", [$PAID_STATUS, $start, $end]);

    foreach ($orderResult as $row) {
        $orderMap[$row['date']] = (int)$row['cnt'];
    }

    // (3) 일자별 매출액 (결제 완료 기준, 기준일: ot_pay_date)
    $salesMap = [];
    $salesResult = $DB->rawQuery("
        SELECT DATE(ot_pay_date) AS date, COALESCE(SUM(ot_total_price), 0) AS amount
        FROM orders_t
        WHERE ot_pay_status = ?
          AND ot_pay_date IS NOT NULL
          AND DATE(ot_pay_date) BETWEEN ? AND ?
        GROUP BY DATE(ot_pay_date)
    ", [$PAID_STATUS, $start, $end]);

    foreach ($salesResult as $row) {
        $salesMap[$row['date']] = (int)$row['amount'];
    }

    // (4) 선택 기간 전체 매출액 (하단 "기간 내 수치"용으로 재활용)
    $salesRangeRow = $DB->rawQueryOne("
        SELECT COALESCE(SUM(ot_total_price), 0) AS amount
        FROM orders_t
        WHERE ot_pay_status = ?
          AND ot_pay_date IS NOT NULL
          AND DATE(ot_pay_date) BETWEEN ? AND ?
    ", [$PAID_STATUS, $start, $end]);
    $salesInRange = (int)($salesRangeRow['amount'] ?? 0);

    /* ===========================
     *  3) 기간 배열 생성
     *  - chart-signup  : 가입 수
     *  - chart-withdrawal : 주문 수
     *  - chart-review  : 매출액
     * =========================== */

    $period  = [];
    $begin   = new DateTime($start);
    $endDate = new DateTime($end);

    while ($begin <= $endDate) {
        $d = $begin->format("Y-m-d");

        $period[] = [
            'date'       => date('m-d', strtotime($d)),
            'signup'     => $signupMap[$d] ?? 0,
            'withdrawal' => $orderMap[$d] ?? 0,
            'review'     => $salesMap[$d] ?? 0,
        ];

        $begin->modify('+1 day');
    }

    /* ===========================
     *  4) 프론트로 내려줄 응답
     *  - JSON 구조는 기존 JS 그대로 사용 가능
     * =========================== */

    $response = [
        // 상단 카드 영역
        'signup_total'             => $signupTotal,   // 총 회원 수
        'withdrawal_total'         => $orderTotal,    // 총 주문 수(결제 완료)
        'review_total'             => 0,              // 현재는 사용 안함(필요 시 매출 관련 값 넣어도 됨)
        'inquiry_total'            => $salesTotal,    // 총 매출액(결제 완료)
        'inquiry_pending_in_range' => $salesInRange,  // 선택 기간 매출액

        // 차트 영역 (가입 / 주문 / 매출)
        'chart_data' => [
            'signup' => array_map(
                fn($p) => ['date' => $p['date'], 'count' => $p['signup']],
                $period
            ),
            'withdrawal' => array_map(
                fn($p) => ['date' => $p['date'], 'count' => $p['withdrawal']],
                $period
            ),
            'review' => array_map(
                fn($p) => ['date' => $p['date'], 'count' => $p['review']],
                $period
            ),
        ],
    ];

    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

/* ===========================================================
 *  최신 리스트 영역 (방문 / 회원 / 리뷰 / 1:1문의)
 *  - 질문에서 "리스트 유지"라고 하신 부분 (기존 그대로 둠)
 * =========================================================== */
else if ($_POST['act'] == 'latest') {

    $start = $_POST['start_date'] ?? date('Y-m-d', strtotime('-6 days'));
    $end   = $_POST['end_date']   ?? date('Y-m-d');

    // 1) 방문자
    $latestVisitsRaw = $DB->rawQuery("
        SELECT *
        FROM visit_t
        WHERE vi_date BETWEEN ? AND ?
        ORDER BY CONCAT(vi_date, ' ', vi_time) DESC
        LIMIT 5
    ", [$start, $end]);

    $latestVisits = [];

    foreach ($latestVisitsRaw as $row) {
        $memInfo = get_mem_info('idx', $row['vi_mt_idx']);

        $latestVisits[] = [
            'vi_date'  => date('Y.m.d', strtotime($row['vi_date'])),
            'vi_time'  => $row['vi_time'],
            'vi_ip'    => $row['vi_ip'],
            'mt_name'  => $memInfo['mt_name']  ?? '비회원',
            'mt_email' => $memInfo['mt_email'] ?? '-',
            'profile'  => $memInfo['profile']  ?? $ct_no_profile_url
        ];
    }

    // 2) 최근 가입 회원
    $latestMembersRaw = $DB->rawQuery("
        SELECT *
        FROM member_t
        WHERE del_status = 'N'
          AND mt_level   = 2
          AND mt_status  = 'Y'
          AND DATE(mt_wdate) BETWEEN ? AND ?
        ORDER BY mt_wdate DESC
        LIMIT 5
    ", [$start, $end]);

    $latestMembers = [];

    foreach ($latestMembersRaw as $row) {
        $profile = $ct_no_profile_url;
        if (!empty($row['mt_image1'])) {
            $filepath = $member_img_dir . $row['mt_image1'];
            if (file_exists($filepath)) {
                $profile = $member_img_url . $row['mt_image1'];
            }
        }

        $latestMembers[] = [
            'mt_name'    => $row['mt_name'],
            'mt_email'   => $row['mt_email'],
            'created_at' => date('Y.m.d', strtotime($row['mt_wdate'])),
            'profile'    => $profile
        ];
    }

    // 3) 최근 리뷰
//    $latestReviewsRaw = $DB->rawQuery("
//        SELECT *
//        FROM review_t
//        WHERE rt_show = 'Y'
//          AND is_hidden_by_admin = 'N'
//          AND DATE(rt_wdate) BETWEEN ? AND ?
//        ORDER BY rt_wdate DESC
//        LIMIT 5
//    ", [$start, $end]);
//
//    $latestReviews = [];
//
//    foreach ($latestReviewsRaw as $row) {
//        $memInfo = get_mem_info('idx', $row['mt_idx']);
//
//        $latestReviews[] = [
//            'created_at' => date('Y.m.d', strtotime($row['rt_wdate'])),
//            'mt_name'    => $memInfo['mt_name'],
//            'mt_email'   => $memInfo['mt_email'],
//            'profile'    => $memInfo['profile']
//        ];
//    }

    // 4) 최근 1:1 문의 (미답변)
    $latestQnARaw = $DB->rawQuery("
        SELECT *
        FROM qa_t
        WHERE rt_show   = 'Y'
          AND rt_status = 'pending'
          AND DATE(created_at) BETWEEN ? AND ?
        ORDER BY created_at DESC
        LIMIT 5
    ", [$start, $end]);

    $latestQnA = [];

    foreach ($latestQnARaw as $row) {
        $memInfo = get_mem_info('idx', $row['mt_idx']);

        $latestQnA[] = [
            'rt_title'   => $row['rt_title'],
            'created_at' => date('Y.m.d', strtotime($row['created_at'])),
            'mt_name'    => $memInfo['mt_name'],
            'mt_email'   => $memInfo['mt_email'],
            'profile'    => $memInfo['profile']
        ];
    }

    $response = [
        'latest' => [
            'visit'  => $latestVisits,
            'member' => $latestMembers,
            'review' => $latestReviews,
            'qna'    => $latestQnA,
        ],
    ];

    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

?>
