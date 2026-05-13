<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";

header('Content-Type: application/json; charset=utf-8');

$mt_id   = trim($_POST['mt_id'] ?? '');
$mt_pass = trim($_POST['mt_pass'] ?? '');

// ✅ 필수값 체크
if ($mt_id === '' || $mt_pass === '') {
    echo json_encode([
        'success' => false,
        'message' => '아이디와 비밀번호를 입력해 주세요.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    // ✅ 회원 조회
    $DB->where('mt_id', $mt_id);
    $DB->where("mt_status", "Y");
//    $DB->where("mt_level", "5", ">=");
//    $DB->where("mt_appr", "Y");
    $DB->where("(mt_rdate = ? OR mt_rdate IS NULL)", array(''));

    $row = $DB->getOne('member_t', '*, idx as mt_idx');

    // ✅ 조회 실패(승인/상태/레벨 조건 포함)
    if (!$row || $DB->count === 0) {
        echo json_encode([
            'success' => false,
            'message' => "존재하지 않는 계정입니다.\n다시 확인 해주세요."
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ✅ 비밀번호 검증
    if (!password_verify($mt_pass, $row['mt_pwd'])) {
        echo json_encode([
            'success' => false,
            'message' => "아이디 및 비밀번호가 올바르지 않습니다.\n아이디, 비밀번호는 대문자, 소문자를 구분합니다.\n<Caps Lock>키가 켜져 있는지 확인하시고 다시 입력하십시오."
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (($row['mt_status'] ?? '') !== 'Y') {
        echo json_encode([
            'success' => false,
            'message' => "현재 계정이 비활성화 상태입니다.\n관리자에게 문의해 주세요."
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // 권한 레벨 부족
    if ((int)($row['mt_level'] ?? 0) < 5) {
        echo json_encode([
            'success' => false,
            'message' => '접근 권한이 없습니다.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // 승인 대기
    if (($row['mt_appr'] ?? '') === 'N') {
        echo json_encode([
            'success' => false,
            'message' => "관리자 승인 후\n로그인이 가능합니다."
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (($row['mt_appr'] ?? '') === 'D') {
        echo json_encode([
            'success' => false,
            'message' => "관리자 승인이\n거부 되었습니다."
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (!empty($row['mt_rdate'])) {
        echo json_encode([
            'success' => false,
            'message' => '탈퇴 처리된 계정입니다.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ✅ 대표 매장 1개 가져오기(없을 수도 있음)
    $DB->where('mb_idx', $row['mt_idx']);
    $DB->orderBy('idx', 'ASC');
    $shop = $DB->getOne('shop_t', 'idx');

    // ✅ 최근 로그인 시각 업데이트
    $DB->where('idx', $row['mt_idx']);
    $DB->update('member_t', [
        'mt_ldate' => $DB->now(),
    ]);

    // ✅ 프로필 이미지(기존 변수 유지)
    $profile = $ct_no_profile_url;
    if (!empty($row['mt_image1'])) {
        $filepath = $member_img_dir . $row['mt_image1'];
        if (file_exists($filepath)) {
            $profile = $member_img_url . $row['mt_image1'];
        }
    }

    // ✅ 세션 세팅
    $_SESSION['mng'] = [
        'mt_idx'       => $row['mt_idx'],
        'mt_id'        => $row['mt_id'],
        'mt_name'      => $row['mt_name'],
        'mt_hp'        => $row['mt_hp'],
        'mt_nickname'  => $row['mt_nickname'],
        'mt_level'     => $row['mt_level'],
        'profile_url'  => $profile,
    ];

    $_SESSION['current_sh_idx'] = (int)($shop['idx'] ?? 0);

    // ✅ 성공 응답
    echo json_encode([
        'success' => true,
        'message' => '로그인 성공',
        'data' => [
            'redirect' => './'
        ]
    ], JSON_UNESCAPED_UNICODE);
    exit;

} catch (Exception $e) {
    error_log("[login_update.php] " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => '서버 오류가 발생했습니다.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
