<?php
// find_update.php : 아이디/비밀번호 찾기 처리 (JSON)

include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/cfg/bizppurio.lib.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";

header('Content-Type: application/json; charset=utf-8');

$act = $_POST['act'] ?? '';

/**
 * 아이디 마스킹 (예: abcdef -> ab****)
 */
function maskId($id) {
    $id = (string)$id;
    $len = mb_strlen($id, 'UTF-8');
    if ($len <= 2) return str_repeat('*', $len);
    $keep = 2;
    return mb_substr($id, 0, $keep, 'UTF-8') . str_repeat('*', max(0, $len - $keep));
}

// ============================
// ① 휴대폰 인증번호 발송 (보조 액션)
// ============================
if ($act === 'send_hp_code') {
    try {
        $mb_hp = preg_replace('/\D+/', '', $_POST['mb_hp'] ?? '');
        if ($mb_hp === '') {
            echo json_encode(['success' => false, 'message' => '휴대폰 번호를 입력해 주세요.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $code = (string)random_int(100000, 999999);
        $message = "[맛집바로]\n휴대폰 인증 번호 : ".$code;
        $result = bizppurio_send_sms($mb_hp, $message);

        // 세션 저장 (아이디/비번 찾기 공용)
        $_SESSION['find_hp']        = $mb_hp;
        $_SESSION['find_hp_code']   = $code;
        $_SESSION['find_hp_expire'] = time() + 180; // 3분
        unset($_SESSION['find_hp_verified']);       // 인증 완료 플래그 초기화

        // TODO: 실제 SMS 발송 연동
        echo json_encode([
            'success' => true,
            'message' => '인증번호가 발송되었습니다. (개발용 코드: ' . $code . ')'
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        error_log("send_hp_code Error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => '서버 오류가 발생했습니다.'], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// ============================
// ② 휴대폰 인증번호 확인 (보조 액션)
// ============================
if ($act === 'verify_hp_code') {
    try {
        $mb_hp = preg_replace('/\D+/', '', $_POST['mb_hp'] ?? '');
        $code  = trim($_POST['hp_code'] ?? '');

        if ($mb_hp === '' || $code === '') {
            echo json_encode(['success' => false, 'message' => '정보를 입력해 주세요.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        if (!isset($_SESSION['find_hp_code'], $_SESSION['find_hp_expire'], $_SESSION['find_hp'])) {
            echo json_encode(['success' => false, 'message' => '인증 요청 이력이 없습니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        if (time() > (int)$_SESSION['find_hp_expire']) {
            echo json_encode(['success' => false, 'message' => '인증 시간이 만료되었습니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        if ($_SESSION['find_hp'] !== $mb_hp || $_SESSION['find_hp_code'] !== $code) {
            echo json_encode(['success' => false, 'message' => '인증번호가 올바르지 않습니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // 인증 완료 플래그(서버가 신뢰할 값)
        $_SESSION['find_hp_verified'] = $mb_hp;

        // 코드/만료는 제거 (원하면 유지해도 됨)
        unset($_SESSION['find_hp_code'], $_SESSION['find_hp_expire']);

        echo json_encode(['success' => true, 'message' => '휴대폰 인증이 완료되었습니다.'], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        error_log("verify_hp_code Error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => '서버 오류가 발생했습니다.'], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// ============================
// ③ 아이디 찾기 (최종 act = find_id)
// ============================
if ($act === 'find_id') {
    try {
        $mb_name = trim($_POST['mb_name'] ?? '');
        $mb_hp   = preg_replace('/\D+/', '', $_POST['mb_hp'] ?? '');

        if ($mb_name === '' || $mb_hp === '') {
            echo json_encode(['success' => false, 'message' => '필수 정보를 입력해 주세요.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // ✅ 휴대폰 인증 완료 여부(서버 기준)
        if (!isset($_SESSION['find_hp_verified']) || $_SESSION['find_hp_verified'] !== $mb_hp) {
            echo json_encode(['success' => false, 'message' => '휴대폰 인증을 완료해 주세요.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // 회원 조회
        $DB->where('mt_name', $mb_name);
        $DB->where('mt_hp', $mb_hp);
        $DB->where('mt_level', 5);
        $member = $DB->getOne('member_t', 'idx, mt_id, mt_appr');

        if (!$member || empty($member['mt_id'])) {
            echo json_encode(['success' => false, 'message' => '일치하는 회원 정보가 없습니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        if($member['mt_appr'] === 'D') {
            echo json_encode(['success' => false, 'message' => '승인이 거부된 계정입니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        if($member['mt_appr'] === 'N') {
            echo json_encode(['success' => false, 'message' => '아직 승인되지 않은 계정입니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        if($member['mt_appr'] === 'Y') {
//            $masked = maskId($member['mt_id']);
            $masked = $member['mt_id'];
            $_SESSION['mt_id'] = $masked;
        }

        echo json_encode([
            'success' => true,
            'message' => '회원님의 아이디는 ' . $masked . ' 입니다.'
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        error_log("find_id Error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => '서버 오류가 발생했습니다.'], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// ============================
// ④ 비밀번호 찾기 (최종 act = find_pw)
//    - 화면은 나중에 만들더라도, 분기 구조는 여기서 유지
// ============================
if ($act === 'find_pw') {
    try {
        // 예시: 아이디 + 이름 + 휴대폰 인증 후 임시 비밀번호 발급(또는 재설정 링크 발송)
        $mb_id   = trim($_POST['mb_id'] ?? '');
        $mb_hp   = preg_replace('/\D+/', '', $_POST['mb_hp'] ?? '');

        if ($mb_id === '' ||  $mb_hp === '') {
            echo json_encode(['success' => false, 'message' => '필수 정보를 입력해 주세요.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        if (!isset($_SESSION['find_hp_verified']) || $_SESSION['find_hp_verified'] !== $mb_hp) {
            echo json_encode(['success' => false, 'message' => '휴대폰 인증을 완료해 주세요.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $DB->where('mt_id', $mb_id);
        $DB->where('mt_level', 5);
        $DB->where('mt_hp', $mb_hp);
        $member = $DB->getOne('member_t', 'idx, mt_appr');

        if (!$member) {
            echo json_encode(['success' => false, 'message' => '일치하는 회원 정보가 없습니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        if($member['mt_appr'] === 'D') {
            echo json_encode(['success' => false, 'message' => '승인이 거부된 계정입니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        if($member['mt_appr'] === 'N') {
            echo json_encode(['success' => false, 'message' => '아직 승인되지 않은 계정입니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $_SESSION['mt_idx'] = $member['idx'];

        // TODO: 실제 비밀번호 재설정 방식(임시 비밀번호 발급/이메일 발송/재설정 페이지 등) 결정 필요
        echo json_encode([
            'success' => true,
            'message' => '비밀번호 찾기 처리가 준비되었습니다. (추후 정책에 맞게 재설정 로직을 연결하세요.)'
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        error_log("find_pw Error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => '서버 오류가 발생했습니다.'], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

if ($act === 'reset_pw') {

    if (empty($_SESSION['mt_idx'])) {
        echo json_encode(['success' => false, 'message' => '로그인 정보가 없습니다. 다시 인증을 진행해 주세요.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $pw  = trim($_POST['mb_pw'] ?? '');
    $pw2 = trim($_POST['mb_pw2'] ?? '');

    if ($pw === '' || $pw2 === '') {
        echo json_encode(['success' => false, 'message' => '비밀번호를 입력해 주세요.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($pw !== $pw2) {
        echo json_encode(['success' => false, 'message' => '비밀번호가 일치하지 않습니다.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $len = strlen($pw);
    $hasLetter = preg_match('/[a-zA-Z]/', $pw);
    $hasNumber = preg_match('/[0-9]/', $pw);

    if ($len < 8 || $len > 16 || !$hasLetter || !$hasNumber) {
        echo json_encode(['success' => false, 'message' => '비밀번호는 영문/숫자 포함 8~16자여야 합니다.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $tbl_member = $CFG_TBL['member']['default'] ?? 'member_t';

    // ✅ 컬럼명은 프로젝트에 맞춰 수정 필요
    $hashed = password_hash($pw, PASSWORD_DEFAULT);

    $arr = [];
    $arr['mt_pwd'] = $hashed;      // ← 여기 컬럼 확인
    $arr['mt_udate'] = $DB->now(); // ← 없으면 제거

    $DB->where('idx', $_SESSION['mt_idx']);
    $ok = $DB->update($tbl_member, $arr);

    if (!$ok) {
        echo json_encode(['success' => false, 'message' => '비밀번호 변경에 실패했습니다.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $_SESSION = [];
    session_destroy();

    echo json_encode(['success' => true, 'message' => '비밀번호가 변경되었습니다.'], JSON_UNESCAPED_UNICODE);
    exit;
}

// ============================
// 잘못된 act
// ============================
echo json_encode(['success' => false, 'message' => '잘못된 요청입니다.'], JSON_UNESCAPED_UNICODE);
exit;
