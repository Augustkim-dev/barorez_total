<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";

header('Content-Type: application/json; charset=utf-8');

global $DB;

// =========================
// 공통: 로그인/매장키 체크
// =========================
if (!isset($_SESSION['mng'])) {
    echo json_encode(['success' => false, 'message' => '로그인이 필요합니다.'], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

$act   = (string)($_POST['act'] ?? '');
$sh_idx = (int)($_SESSION['current_sh_idx'] ?? 0);

if ($sh_idx <= 0) {
    echo json_encode(['success' => false, 'message' => '매장 정보가 없습니다.'], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

// =========================
// 공통 함수: 최신 데이터 조회
// =========================
function getReserveSettingData($DB, $sh_idx) {
    $DB->where('sh_idx', $sh_idx);
    $setting = $DB->getOne('shop_reserve_setting_t', '*');

    if (!$setting) {
        return [
            'setting' => [
                'rs_notice' => '',
                'rs_allow_same_day' => 'Y',
                'rs_max_reserve_days' => 0,
                'rs_min_person' => 1,
                'rs_max_person' => 1,
                'rs_slot_unit_min' => 30,
            ],
            'slots' => [
                [
                    'slot_use' => 'Y',
                    'slot_day_type' => 'WEEKDAY',
                    'slot_hour' => 7,
                    'slot_minute' => 0,
                    'slot_max_count' => 1,
                    'slot_sort' => 1,
                ],
                [
                    'slot_use' => 'Y',
                    'slot_day_type' => 'WEEKDAY',
                    'slot_hour' => 7,
                    'slot_minute' => 30,
                    'slot_max_count' => 1,
                    'slot_sort' => 2,
                ],
            ],
            'penalty' => [
                'rp_use' => 'Y',
                'rp_type' => 'FIXED',
                'rp_value' => 0,
                'rp_free_cancel_before_min' => 1440,
            ],
        ];
    }

    $rs_idx = (int)$setting['idx'];

    // slots
    $DB->where('rs_idx', $rs_idx);
    $DB->orderBy('slot_sort', 'asc');
    $slots = $DB->get('shop_reserve_slot_t');
    if (!$slots) $slots = [];

    // penalty
    $DB->where('rs_idx', $rs_idx);
    $penalty = $DB->getOne('shop_reserve_penalty_t', '*');

    // penalty 없으면 기본값
    if (!$penalty) {
        $penalty = [
            'rp_use' => 'Y',
            'rp_type' => 'FIXED',
            'rp_value' => 0,
            'rp_free_cancel_before_min' => 1440,
        ];
    }

    return [
        'setting' => $setting,
        'slots' => $slots,
        'penalty' => $penalty,
    ];
}

// =========================
// 1) 설정 조회
// =========================
if ($act === 'reserve_set_get') {
    try {
        $data = getReserveSettingData($DB, $sh_idx);

        echo json_encode([
            'success' => true,
            'data' => $data
        ], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
        exit;

    } catch (Exception $e) {
        error_log('reserve_set_get error: '.$e->getMessage());
        echo json_encode(['success' => false, 'message' => '서버 오류'], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
        exit;
    }
}

// =========================
// 2) 설정 저장
// =========================
if ($act === 'reserve_set_update') {
    try {
        $rs_notice = trim((string)($_POST['rs_notice'] ?? ''));
        if (mb_strlen($rs_notice) > 300) {
            $rs_notice = mb_substr($rs_notice, 0, 300);
        }

        $rs_allow_same_day = ((string)($_POST['rs_allow_same_day'] ?? 'Y') === 'N') ? 'N' : 'Y';

        $rs_max_reserve_days = (int)($_POST['rs_max_reserve_days'] ?? 0);
        if ($rs_max_reserve_days < 0) $rs_max_reserve_days = 0;

        $rs_min_person = (int)($_POST['rs_min_person'] ?? 1);
        if ($rs_min_person < 1) $rs_min_person = 1;

        $rs_max_person = (int)($_POST['rs_max_person'] ?? 1);
        if ($rs_max_person < $rs_min_person) $rs_max_person = $rs_min_person;

        $rs_slot_unit_min = (int)($_POST['rs_slot_unit_min'] ?? 30);
        if ($rs_slot_unit_min <= 0) $rs_slot_unit_min = 30;

        $slots_json = (string)($_POST['slots_json'] ?? '[]');
        $slots = json_decode($slots_json, true);
        if (!is_array($slots)) $slots = [];

        // penalty
        $rp_use = ((string)($_POST['rp_use'] ?? 'N') === 'Y') ? 'Y' : 'N';

        $rp_type = strtoupper(trim((string)($_POST['rp_type'] ?? 'FIXED')));
        $rp_type = ($rp_type === 'PERCENT') ? 'PERCENT' : 'FIXED';

        $rp_value = (int)($_POST['rp_value'] ?? 0);
        if ($rp_value < 0) $rp_value = 0;

        $rp_free_cancel_before_min = (int)($_POST['rp_free_cancel_before_min'] ?? 0);
        if ($rp_free_cancel_before_min < 0) $rp_free_cancel_before_min = 0;

        $DB->startTransaction();

        // 1) setting upsert (uq_srs_shop)
        $DB->where('sh_idx', $sh_idx);
        $setting = $DB->getOne('shop_reserve_setting_t', 'idx');

        if ($setting && isset($setting['idx'])) {
            $rs_idx = (int)$setting['idx'];
            $upd = [
                'rs_notice' => $rs_notice,
                'rs_allow_same_day' => $rs_allow_same_day,
                'rs_max_reserve_days' => $rs_max_reserve_days,
                'rs_min_person' => $rs_min_person,
                'rs_max_person' => $rs_max_person,
                'rs_slot_unit_min' => $rs_slot_unit_min,
            ];
            $DB->where('idx', $rs_idx);
            $ok = $DB->update('shop_reserve_setting_t', $upd);
            if (!$ok) throw new Exception('저장 실패: '.$DB->getLastError());
        } else {
            $ins = [
                'sh_idx' => $sh_idx,
                'rs_notice' => $rs_notice,
                'rs_allow_same_day' => $rs_allow_same_day,
                'rs_max_reserve_days' => $rs_max_reserve_days,
                'rs_min_person' => $rs_min_person,
                'rs_max_person' => $rs_max_person,
                'rs_slot_unit_min' => $rs_slot_unit_min,
            ];
            $rs_idx = (int)$DB->insert('shop_reserve_setting_t', $ins);
            if ($rs_idx <= 0) throw new Exception('저장 실패: '.$DB->getLastError());
        }

        // 2) 슬롯 중복 체크(활성 Y만) - 서버에서 친절 에러
        $dup = [];
        foreach ($slots as $s) {
            $use = strtoupper((string)($s['slot_use'] ?? 'Y'));
            if ($use !== 'Y') continue;

            $day = strtoupper(trim((string)($s['slot_day_type'] ?? 'WEEKDAY')));
            if (!in_array($day, ['WEEKDAY','SAT','SUN'], true)) $day = 'WEEKDAY';

            $h = (int)($s['slot_hour'] ?? 0);
            $m = (int)($s['slot_minute'] ?? 0);

            if ($h < 1 || $h > 24 || $m < 0 || $m > 60) {
                $DB->rollback();
                echo json_encode(['success'=>false,'message'=>'시간 설정 값이 올바르지 않습니다.'], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
                exit;
            }

            $key = $day.'|'.$h.'|'.$m;
            if (isset($dup[$key])) {
                $DB->rollback();
                echo json_encode(['success'=>false,'message'=>'같은 시간대를 중복으로 설정할 수 없습니다.'], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
                exit;
            }
            $dup[$key] = true;
        }

        // 3) 슬롯 저장(삭제 후 재삽입)
        $DB->where('rs_idx', $rs_idx);
        $DB->delete('shop_reserve_slot_t');

        foreach ($slots as $i => $s) {
            $use = strtoupper((string)($s['slot_use'] ?? 'Y'));
            $use = ($use === 'N') ? 'N' : 'Y';

            $day = strtoupper(trim((string)($s['slot_day_type'] ?? 'WEEKDAY')));
            if (!in_array($day, ['WEEKDAY','SAT','SUN'], true)) $day = 'WEEKDAY';

            $h = (int)($s['slot_hour'] ?? 0);
            $m = (int)($s['slot_minute'] ?? 0);

            $cnt = (int)($s['slot_max_count'] ?? 1);
            if ($cnt < 1) $cnt = 1;

            $sort = (int)($s['slot_sort'] ?? ($i+1));
            if ($sort < 0) $sort = 0;

            $insSlot = [
                'rs_idx' => $rs_idx,
                'slot_use' => $use,
                'slot_day_type' => $day,
                'slot_hour' => $h,
                'slot_minute' => $m,
                'slot_max_count' => $cnt,
                'slot_sort' => $sort,
            ];

            $slotId = $DB->insert('shop_reserve_slot_t', $insSlot);
            if (!$slotId) {
                // uq_slot(rs_idx, day, hour, minute) 충돌도 여기로 들어올 수 있음
                $err = (string)$DB->getLastError();
                $DB->rollback();

                if (stripos($err, 'uq_slot') !== false || stripos($err, 'Duplicate') !== false) {
                    echo json_encode(['success'=>false,'message'=>'같은 시간대를 중복으로 설정할 수 없습니다.'], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
                    exit;
                }

                echo json_encode(['success'=>false,'message'=>'시간대 저장 실패'], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
                exit;
            }
        }

        // 4) 위약금 upsert (uq_srp rs_idx unique)
        $DB->where('rs_idx', $rs_idx);
        $pen = $DB->getOne('shop_reserve_penalty_t', 'idx');

        $penData = [
            'rs_idx' => $rs_idx,
            'rp_use' => $rp_use,
            'rp_type' => $rp_type,
            'rp_value' => $rp_value,
            'rp_free_cancel_before_min' => $rp_free_cancel_before_min,
        ];

        if ($pen && isset($pen['idx'])) {
            $DB->where('rs_idx', $rs_idx);
            $ok = $DB->update('shop_reserve_penalty_t', $penData);
            if (!$ok) throw new Exception('위약금 저장 실패: '.$DB->getLastError());
        } else {
            $ok = $DB->insert('shop_reserve_penalty_t', $penData);
            if (!$ok) throw new Exception('위약금 저장 실패: '.$DB->getLastError());
        }

        $DB->commit();

        // ✅ 저장된 최신 데이터 함께 응답
        $data = getReserveSettingData($DB, $sh_idx);

        echo json_encode([
            'success'=>true,
            'message'=>'저장되었습니다.',
            'data' => $data
        ], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
        exit;

    } catch (Exception $e) {
        error_log('reserve_set_update error: '.$e->getMessage());
        if (method_exists($DB, 'rollback')) $DB->rollback();

        // ✅ 500 대신 success:false로 사용자 메시지
        echo json_encode(['success'=>false,'message'=>'서버 통신 중 오류가 발생했습니다.'], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
        exit;
    }
}

// =========================
// 기타 act
// =========================
echo json_encode(['success' => false, 'message' => '잘못된 요청입니다.'], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
exit;
