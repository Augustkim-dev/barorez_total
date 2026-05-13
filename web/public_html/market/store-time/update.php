<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";

header('Content-Type: application/json; charset=utf-8');

$act = $_POST['act'] ?? '';

if (!$act) {
    echo json_encode(['success' => false, 'message' => 'act 누락']);
    exit;
}

$tbl_shop      = $CFG_TBL['shop_t'] ?? 'shop_t';
$tbl_hours     = $CFG_TBL['shop_hours_t'] ?? 'shop_hours_t';
$tbl_break     = $CFG_TBL['shop_break_t'] ?? 'shop_break_t';
$tbl_temp      = $CFG_TBL['shop_temp_holiday_t'] ?? 'shop_temp_holiday_t';

// ----------------------------------------------------
// ✅ 내 매장 sh_idx 구하기(세션 기준)
// ----------------------------------------------------
$mt_idx = $_SESSION['mng']['mt_idx'] ?? 0;
$mt_idx = (int)$mt_idx;

if ($mt_idx <= 0) {
    echo json_encode(['success' => false, 'message' => '로그인이 필요합니다.']);
    exit;
}

$DB->where('mb_idx', $mt_idx);
$shopRow = $DB->getOne($tbl_shop, 'idx');

$sh_idx = (int)($shopRow['idx'] ?? 0);
if ($sh_idx <= 0) {
    echo json_encode(['success' => false, 'message' => '매장 정보를 찾을 수 없습니다.']);
    exit;
}

$nowDate = date('Y-m-d');

// ----------------------------------------------------
// ✅ 임시휴무 유효성(날짜 형식) 체크 함수 (json_fail 같은 건 안씀)
// ----------------------------------------------------
function is_ymd($s) {
    if (!is_string($s) || $s === '') return false;
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $s)) return false;
    $p = explode('-', $s);
    return checkdate((int)$p[1], (int)$p[2], (int)$p[0]);
}

// ----------------------------------------------------
// act: time_get (운영시간+브레이크+임시휴무 목록)
// ----------------------------------------------------
if ($act === 'time_get') {

    // ✅ 임시휴무 자동 만료 처리(끝난 건 use_yn=N)
    $DB->where('sh_idx', $sh_idx);
    $DB->where('end_date', $nowDate, '<');
    $DB->where('use_yn', 'Y');
    $DB->update($tbl_temp, ['use_yn' => 'N']);

    // ✅ 운영시간 0~6 로딩
    $DB->where('sh_idx', $sh_idx);
    $rows = $DB->get($tbl_hours, null, ['dow','bt_type','start_time','end_time']);

    $week = [];
    for ($i=0; $i<=6; $i++) {
        $week[(string)$i] = [
            'dow' => $i,
            'bt_type' => 'CLOSE',
            'start_time' => null,
            'end_time' => null,
        ];
    }

    if ($rows) {
        foreach ($rows as $r) {
            $d = (int)$r['dow'];
            if ($d < 0 || $d > 6) continue;
            $week[(string)$d] = [
                'dow' => $d,
                'bt_type' => $r['bt_type'] ?: 'CLOSE',
                'start_time' => $r['start_time'],
                'end_time' => $r['end_time'],
            ];
        }
    }

    // ✅ 브레이크(1행)
    $DB->where('sh_idx', $sh_idx);
    $bk = $DB->getOne($tbl_break, ['start_time','end_time']);

    $break = [
        'start_time' => $bk['start_time'] ?? null,
        'end_time' => $bk['end_time'] ?? null,
    ];

    // ✅ 임시휴무 목록(use_yn=Y & end_date>=today)
    $DB->where('sh_idx', $sh_idx);
    $DB->where('use_yn', 'Y');
    $DB->where('end_date', $nowDate, '>=');
    $DB->orderBy('start_date', 'ASC');
    $temps = $DB->get($tbl_temp, null, ['idx','start_date','end_date','memo']);

    echo json_encode([
        'success' => true,
        'data' => [
            'sh_idx' => $sh_idx,
            'week' => $week,
            'break' => $break,
            'temp' => $temps ?: [],
        ]
    ]);
    exit;
}

// ----------------------------------------------------
// act: time_save (운영시간/브레이크 저장)
// ----------------------------------------------------
if ($act === 'time_save') {

    $week_json = $_POST['week_json'] ?? '';
    $weekData = json_decode($week_json, true);

    if (!$weekData || !is_array($weekData)) {
        echo json_encode(['success' => false, 'message' => 'week_json이 올바르지 않습니다.']);
        exit;
    }

    $break_on = $_POST['break_on'] ?? 'N';
    $break_on = ($break_on === 'Y') ? 'Y' : 'N';

    $break_start = $_POST['break_start'] ?? null;
    $break_end   = $_POST['break_end'] ?? null;

    $DB->startTransaction();

    try {

        // 1) 운영시간 upsert (dow 0~6)
        for ($dow=0; $dow<=6; $dow++) {

            $k = (string)$dow;
            $it = $weekData[$k] ?? [];
            $bt_type = $it['bt_type'] ?? 'CLOSE';
            $bt_type = ($bt_type === 'OPEN') ? 'OPEN' : 'CLOSE';

            $st = $it['start_time'] ?? null;
            $et = $it['end_time'] ?? null;

            if ($bt_type === 'CLOSE') {
                $st = null;
                $et = null;
            } else {
                if (!$st || !$et) {
                    throw new Exception("요일({$dow}) 영업시간이 비었습니다.");
                }

                // ✅ 오픈/마감 비교 (마감이 오픈보다 빠르면 불가)
                // "HH:MM:SS" 문자열 비교는 같은 포맷이면 안전
                if (strlen($st) === 8 && strlen($et) === 8 && $et <= $st) {
                    throw new Exception("요일({$dow}) 마감시간은 오픈시간보다 늦어야 합니다.");
                }
            }

            $DB->where('sh_idx', $sh_idx);
            $DB->where('dow', $dow);
            $exists = $DB->getOne($tbl_hours, 'idx');

            $arr = [
                'sh_idx' => $sh_idx,
                'dow' => $dow,
                'bt_type' => $bt_type,
                'start_time' => $st,
                'end_time' => $et,
            ];

            if ($exists && (int)$exists['idx'] > 0) {
                $DB->where('idx', (int)$exists['idx']);
                $ok = $DB->update($tbl_hours, $arr);
                if ($ok === false) throw new Exception("운영시간 업데이트 실패(dow={$dow})");
            } else {
                $ok = $DB->insert($tbl_hours, $arr);
                if (!$ok) throw new Exception("운영시간 인서트 실패(dow={$dow})");
            }
        }

        // 2) 브레이크 upsert
        $bkArr = [
            'sh_idx' => $sh_idx,
            'start_time' => null,
            'end_time' => null,
        ];

        if ($break_on === 'Y') {
            if (!$break_start || !$break_end) {
                throw new Exception("브레이크 시간이 비었습니다.");
            }
            if (strlen($break_start) === 8 && strlen($break_end) === 8 && $break_end <= $break_start) {
                throw new Exception("브레이크 종료시간은 시작시간보다 늦어야 합니다.");
            }
            $bkArr['start_time'] = $break_start;
            $bkArr['end_time'] = $break_end;
        }

        $DB->where('sh_idx', $sh_idx);
        $bkExist = $DB->getOne($tbl_break, 'sh_idx');

        if ($bkExist && (int)$bkExist['sh_idx'] > 0) {
            $DB->where('sh_idx', $sh_idx);
            $ok = $DB->update($tbl_break, $bkArr);
            if ($ok === false) throw new Exception("브레이크 업데이트 실패");
        } else {
            $ok = $DB->insert($tbl_break, $bkArr);
            if (!$ok) throw new Exception("브레이크 인서트 실패");
        }

        $DB->commit();
        echo json_encode(['success' => true, 'message' => '저장되었습니다.']);
        exit;

    } catch (Exception $e) {
        $DB->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
}

// ----------------------------------------------------
// act: temp_add (임시휴무 추가)  ✅겹침검사/중복방지/형식검사 보강
// ----------------------------------------------------
if ($act === 'temp_add') {

    $start_date = $_POST['start_date'] ?? '';
    $end_date   = $_POST['end_date'] ?? '';
    $memo       = $_POST['memo'] ?? null;

    if (!$start_date) {
        echo json_encode(['success' => false, 'message' => '시작일을 선택해 주세요.']);
        exit;
    }
    if (!$end_date) $end_date = $start_date;

    if (!is_ymd($start_date) || !is_ymd($end_date)) {
        echo json_encode(['success' => false, 'message' => '날짜 형식이 올바르지 않습니다.']);
        exit;
    }

    if ($start_date > $end_date) {
        echo json_encode(['success' => false, 'message' => '종료일이 시작일보다 빠릅니다.']);
        exit;
    }

    // ✅ 과거만 등록하려는 경우 막기(원하면 제거)
    // (오늘 이전은 의미 없으니)
    if ($end_date < $nowDate) {
        echo json_encode(['success' => false, 'message' => '이미 지난 날짜는 등록할 수 없습니다.']);
        exit;
    }

    // ✅ 겹치는 기간 있는지 체크 (기존.start <= 새.end AND 기존.end >= 새.start)
    $DB->where('sh_idx', $sh_idx);
    $DB->where('use_yn', 'Y');
    $DB->where('start_date', $end_date, '<=');
    $DB->where('end_date', $start_date, '>=');
    $exist = $DB->getOne($tbl_temp, 'idx');

    if ($exist && (int)$exist['idx'] > 0) {
        echo json_encode(['success' => false, 'message' => '이미 등록된 임시휴무 기간과 겹칩니다.']);
        exit;
    }

    $arr = [
        'sh_idx' => $sh_idx,
        'start_date' => $start_date,
        'end_date' => $end_date,
        'memo' => $memo,
        'use_yn' => 'Y',
    ];

    $ok = $DB->insert($tbl_temp, $arr);
    if (!$ok) {
        echo json_encode(['success' => false, 'message' => '임시휴무 등록 실패']);
        exit;
    }

    // ✅ 추가 후 최신 리스트도 같이 내려주면 프론트가 fetch 한 번 덜함(원하면 제거)
    $DB->where('sh_idx', $sh_idx);
    $DB->where('use_yn', 'Y');
    $DB->where('end_date', $nowDate, '>=');
    $DB->orderBy('start_date', 'ASC');
    $temps = $DB->get($tbl_temp, null, ['idx','start_date','end_date','memo']);

    echo json_encode(['success' => true, 'message' => '추가되었습니다.', 'data' => ['temp' => $temps ?: []]]);
    exit;
}

// ----------------------------------------------------
// act: temp_del (임시휴무 삭제: use_yn=N)
// ----------------------------------------------------
if ($act === 'temp_del') {

    $idx = (int)($_POST['idx'] ?? 0);
    if ($idx <= 0) {
        echo json_encode(['success' => false, 'message' => 'idx 누락']);
        exit;
    }

    // ✅ 내 매장 데이터인지 확인(보안)
    $DB->where('idx', $idx);
    $DB->where('sh_idx', $sh_idx);
    $row = $DB->getOne($tbl_temp, 'idx');

    if (!$row) {
        echo json_encode(['success' => false, 'message' => '삭제할 데이터가 없습니다.']);
        exit;
    }

    $DB->where('idx', $idx);
    $DB->where('sh_idx', $sh_idx);
    $ok = $DB->update($tbl_temp, ['use_yn' => 'N']);

    if ($ok === false) {
        echo json_encode(['success' => false, 'message' => '삭제 실패']);
        exit;
    }

    // ✅ 삭제 후 최신 리스트도 같이 내려줌(원하면 제거)
    $DB->where('sh_idx', $sh_idx);
    $DB->where('use_yn', 'Y');
    $DB->where('end_date', $nowDate, '>=');
    $DB->orderBy('start_date', 'ASC');
    $temps = $DB->get($tbl_temp, null, ['idx','start_date','end_date','memo']);

    echo json_encode(['success' => true, 'message' => '삭제되었습니다.', 'data' => ['temp' => $temps ?: []]]);
    exit;
}

// (선택) 임시휴무만 별도로 불러오는 act가 필요하면 아래 추가 가능
// if ($act === 'temp_get') { ... }

echo json_encode(['success' => false, 'message' => '지원하지 않는 act']);
exit;
