<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";

header('Content-Type: application/json; charset=utf-8');

$mt_idx = (int)($_SESSION['mng']['mt_idx'] ?? 0); // 세션에서 사용자 idx 가져오기

$act = $_POST['act'] ?? '';

if ($mt_idx <= 0) {
    echo json_encode([
        'success' => false,
        'message' => '로그인 정보가 없습니다. 다시 로그인해주세요.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($act === 'check_password') {
    try {
        $password = trim($_POST['password'] ?? '');

        if ($password === '') {
            echo json_encode([
                'success' => false,
                'message' => '비밀번호를 입력해주세요.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // 회원 정보 조회 (로그인 API와 동일한 조건 적용)
        $DB->where('idx', $mt_idx);
        $DB->where('mt_status', 'Y');
        $DB->where('mt_level', 5, '>=');
        $DB->where('mt_appr', ['Y','T'], 'IN');
        $DB->where("(mt_rdate = '' OR mt_rdate IS NULL)");

        $row = $DB->getOne('member_t', 'mt_pwd');

        if (!$row || $DB->count === 0) {
            echo json_encode([
                'success' => false,
                'message' => '사용자 정보를 찾을 수 없습니다. 다시 로그인해주세요.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // 비밀번호 검증 (로그인 API와 동일)
        if (!password_verify($password, $row['mt_pwd'])) {
            echo json_encode([
                'success' => false,
                'message' => '비밀번호가 일치하지 않습니다.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // 성공
        echo json_encode([
            'success' => true,
            'message' => '비밀번호가 확인되었습니다.'
        ], JSON_UNESCAPED_UNICODE);

    } catch (Exception $e) {
        error_log("[myinfo_check_pw.php] " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => '서버 오류가 발생했습니다.'
        ], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

// 지원하지 않는 act
echo json_encode(['success'=>false, 'message'=>'지원하지 않는 요청'], JSON_UNESCAPED_UNICODE);
exit;
