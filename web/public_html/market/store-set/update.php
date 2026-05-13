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

$act = (string)($_POST['act'] ?? '');
$sh_idx = (int)($_SESSION['current_sh_idx'] ?? 0);

if ($sh_idx <= 0) {
    echo json_encode(['success' => false, 'message' => '매장 정보가 없습니다.'], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

// =========================
// 1) 설정 조회
// =========================
if ($act === 'store_set_get') {
    try {
        $DB->where('idx', $sh_idx);
        $row = $DB->getOne('shop_t', 'idx, sh_qr_yn, sh_qr_pay_type, sh_reserve_yn, sh_takeout_yn');

        if (!$row) {
            echo json_encode(['success' => false, 'message' => '매장 정보를 찾을 수 없습니다.'], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
            exit;
        }

        echo json_encode(['success' => true, 'data' => $row], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
        exit;

    } catch (Exception $e) {
        error_log('store_set_get error: '.$e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => '서버 오류'], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
        exit;
    }
}

// =========================
// 2) 설정 저장(부분 업데이트)
//  - 넘어온 값만 업데이트
// =========================
if ($act === 'store_set_update') {
    try {
        // 허용 키/값만 업데이트
        $upd = [];

        // Y/N 컬럼들
        $ynCols = ['sh_qr_yn', 'sh_reserve_yn', 'sh_takeout_yn'];
        foreach ($ynCols as $k) {
            if (isset($_POST[$k])) {
                $v = strtoupper(trim((string)$_POST[$k]));
                $upd[$k] = ($v === 'Y') ? 'Y' : 'N';
            }
        }

        // QR 결제 방식
        if (isset($_POST['sh_qr_pay_type'])) {
            $v = strtoupper(trim((string)$_POST['sh_qr_pay_type']));
            $upd['sh_qr_pay_type'] = ($v === 'PREPAY') ? 'PREPAY' : 'POSTPAY';
        }


        if (isset($_POST['sh_reserve_pay_type'])) {
            $v = strtoupper(trim((string)$_POST['sh_reserve_pay_type']));
            $upd['sh_reserve_pay_type'] = ($v === 'PREPAY') ? 'PREPAY' : 'POSTPAY';
        }

        if (empty($upd)) {
            echo json_encode(['success' => true, 'message' => '변경사항이 없습니다.'], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
            exit;
        }

        // QR 주문이 OFF인데 pay_type만 오는 경우도 있을 수 있음
        // 정책: 저장은 하되, 프론트에서 비활성화로 막고 있음
        $DB->where('idx', $sh_idx);
        $ok = $DB->update('shop_t', $upd);

        if (!$ok) {
            echo json_encode(['success' => false, 'message' => '저장 실패: '.$DB->getLastError()], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
            exit;
        }

        // 최신값 반환(프론트 동기화 용)
        $DB->where('idx', $sh_idx);
        $row = $DB->getOne('shop_t', 'idx, sh_qr_yn, sh_qr_pay_type, sh_reserve_yn, sh_reserve_pay_type, sh_takeout_yn');

        echo json_encode([
            'success' => true,
            'message' => '저장되었습니다.',
            'data' => $row
        ], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
        exit;

    } catch (Exception $e) {
        error_log('store_set_update error: '.$e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => '서버 오류'], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
        exit;
    }
}

// =========================
// 기타 act
// =========================
echo json_encode(['success' => false, 'message' => '잘못된 요청입니다.'], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
exit;
