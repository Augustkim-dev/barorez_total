<?php
// badge_poll.php : 테이블/포장/예약 신규 뱃지 카운트 + 페이지 접속 시 뱃지 OFF(mark_seen)

include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";

header('Content-Type: application/json; charset=utf-8');

$act = $_POST['act'] ?? '';

try {
    // 로그인 체크 (프로젝트 세션 구조에 맞게 유지)
    if (!isset($_SESSION['mng'])) {
        echo json_encode(['success' => false, 'message' => '로그인이 필요합니다.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $sh_idx = (int)($_POST['sh_idx'] ?? 0);
    if (!$sh_idx) {
        echo json_encode(['success' => true, 'table' => 0, 'pack' => 0, 'reserve' => 0, 'total' => 0], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ✅ 가맹점주 관리자 키(mt_idx)
    $mt_idx = (int)($_SESSION['_mt_idx'] ?? ($_SESSION['mng']['mt_idx'] ?? 0));
    if (!$mt_idx) {
        echo json_encode(['success' => false, 'message' => '관리자 키(mt_idx)를 찾을 수 없습니다.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ✅ 오늘 범위 (원하면 제거 가능)
    $start = date('Y-m-d 00:00:00');
    $end   = date('Y-m-d 00:00:00', strtotime('+1 day'));

    // -----------------------------
    // 유틸: 마지막 확인 시각 가져오기/저장하기
    // -----------------------------
    $getLastSeen = function($badgeType) use ($DB, $sh_idx, $mt_idx) {
        $DB->where('sh_idx', $sh_idx);
        $DB->where('mt_idx', $mt_idx);
        $DB->where('badge_type', $badgeType);
        $row = $DB->getOne('badge_seen_t', 'last_seen_at');
        return $row ? (string)($row['last_seen_at'] ?? '') : '';
    };

    $upsertSeenNow = function($badgeType) use ($DB, $sh_idx, $mt_idx) {
        $now = $DB->now();

        $DB->where('sh_idx', $sh_idx);
        $DB->where('mt_idx', $mt_idx);
        $DB->where('badge_type', $badgeType);
        $row = $DB->getOne('badge_seen_t', 'idx');

        if ($row && !empty($row['idx'])) {
            $DB->where('idx', (int)$row['idx']);
            return $DB->update('badge_seen_t', [
                'last_seen_at' => $now,
                'b_udate' => $now,
            ]);
        } else {
            return $DB->insert('badge_seen_t', [
                'sh_idx' => $sh_idx,
                'mt_idx' => $mt_idx,
                'badge_type' => $badgeType,
                'last_seen_at' => $now,
                'b_wdate' => $now,
                'b_udate' => $now,
            ]);
        }
    };

    // -----------------------------
    // 1) mark_seen : 페이지 접속 시 해당 메뉴 뱃지 OFF
    // -----------------------------
    if ($act === 'mark_seen') {
        $badgeType = strtoupper(trim((string)($_POST['badge_type'] ?? '')));
        if (!in_array($badgeType, ['TABLE','PACK','RESERVE'], true)) {
            echo json_encode(['success'=>false,'message'=>'badge_type 오류'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $ok = $upsertSeenNow($badgeType);
        echo json_encode(['success'=> (bool)$ok], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // -----------------------------
    // 2) check_badges : 뱃지 카운트
    // -----------------------------
    if ($act !== 'check_badges') {
        echo json_encode(['success' => false, 'message' => '잘못된 요청입니다.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ✅ 진행중 주문 상태 (테이블/포장)
    $activeStatuses = ['PENDING', 'CONFIRMED', 'PREPARING'];
    $inPlaceholders = implode(',', array_fill(0, count($activeStatuses), '?'));

    // last_seen
    $seenTable   = $getLastSeen('TABLE');
    $seenPack    = $getLastSeen('PACK');
    $seenReserve = $getLastSeen('RESERVE');

    // 없으면 오늘 시작으로 (오늘 신규만 뱃지로 보겠다는 정책 유지)
    if (!$seenTable)   $seenTable   = $start;
    if (!$seenPack)    $seenPack    = $start;
    if (!$seenReserve) $seenReserve = $start;

    // -----------------------------
    // (A) 테이블/포장 : orders_t 기준
    // - "오늘 + last_seen 이후 + 진행중 상태"만 카운트
    // -----------------------------
    $sqlOrders = "
        SELECT
            SUM(
                CASE
                    WHEN (rv_idx IS NULL OR rv_idx = 0)
                         AND ot_table IS NOT NULL
                         AND ot_table <> ''
                         AND ot_wdate > ?
                    THEN 1 ELSE 0
                END
            ) AS table_cnt,
            SUM(
                CASE
                    WHEN (rv_idx IS NULL OR rv_idx = 0)
                         AND (ot_table IS NULL OR ot_table = '')
                         AND ot_wdate > ?
                    THEN 1 ELSE 0
                END
            ) AS pack_cnt
        FROM orders_t
        WHERE sh_idx = ?
          AND ot_wdate >= ?
          AND ot_wdate < ?
          AND ot_status IN ($inPlaceholders)
    ";
    $paramsOrders = array_merge([$seenTable, $seenPack, $sh_idx, $start, $end], $activeStatuses);
    $rowO = $DB->rawQueryOne($sqlOrders, $paramsOrders);

    $table = (int)($rowO['table_cnt'] ?? 0);
    $pack  = (int)($rowO['pack_cnt'] ?? 0);

    // -----------------------------
    // (B) 예약 : reservation_t 기준 (⭐ 방문예약(VISIT) 포함)
    // - "오늘 + last_seen 이후 + 예약대기(PENDING)"만 카운트
    // -----------------------------
    $sqlRv = "
        SELECT COUNT(*) AS reserve_cnt
        FROM reservation_t
        WHERE sh_idx = ?
          AND rv_wdate >= ?
          AND rv_wdate < ?
          AND rv_wdate > ?
          AND rv_status = 'PENDING'
    ";
    $rowR = $DB->rawQueryOne($sqlRv, [$sh_idx, $start, $end, $seenReserve]);
    $reserve = (int)($rowR['reserve_cnt'] ?? 0);

    $total = $table + $pack + $reserve;

    echo json_encode([
        'success' => true,
        'table' => $table,
        'pack' => $pack,
        'reserve' => $reserve,
        'total' => $total,
        // 디버그 필요하면 아래 남겨도 됨
        // 'seen' => ['TABLE'=>$seenTable,'PACK'=>$seenPack,'RESERVE'=>$seenReserve],
        // 'today_start' => $start,
        // 'today_end' => $end
    ], JSON_UNESCAPED_UNICODE);
    exit;

} catch (Exception $e) {
    error_log("badge_poll Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '서버 오류가 발생했습니다.'], JSON_UNESCAPED_UNICODE);
    exit;
}
