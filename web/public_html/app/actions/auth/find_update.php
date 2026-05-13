<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/cfg/bizppurio.lib.php";

header('Content-Type: application/json');

if($_POST['act']=="find_id") {

    try {
        $mt_name = trim($_POST['mt_name'] ?? '');
        $mt_hp   = trim($_POST['mt_hp'] ?? '');

        if ($mt_name === '') {
            throw new Exception('이름을 입력해 주세요.');
        }
        if ($mt_hp === '') {
            throw new Exception('휴대폰번호를 입력해 주세요.');
        }

//        // 휴대폰 인증 여부 확인
//        if (empty($_SESSION['_find_id_ok']) || $_SESSION['_find_id_ok'] !== true) {
//            throw new Exception('휴대폰 인증이 완료되지 않았습니다.');
//        }
//        if (empty($_SESSION['_find_id_hp']) || $_SESSION['_find_id_hp'] !== $mt_hp) {
//            throw new Exception('인증된 휴대폰번호와 일치하지 않습니다.');
//        }

        // 회원 검색
        $DB->where('mt_name', $mt_name);
        $DB->where('mt_hp', $mt_hp);
        $DB->where('mt_status', 'Y'); // 정상회원만
        $DB->where('mt_level', 2);
        $row = $DB->getOne('member_t', 'idx, mt_id');

        if (!$row) {
            throw new Exception('입력하신 정보와 일치하는 회원을 찾을 수 없습니다.');
        }

        $mt_id = $row['mt_id'];
        $encId = encrypt_member_id($mt_id);
        if (!$encId) {
            throw new Exception('아이디 암호화 중 오류가 발생했습니다.');
        }

        echo json_encode([
            'success'      => true,
            'message'      => '아이디를 찾았습니다.',
            'encrypted_id' => $encId,
            'redirect'     => '/auth/find_id_cmp.php', // JS에서 이 URL + ?id= 로 이동
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        $DB->rollback();
        die(json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE));
    }
}else if($_POST['act']=='chk_mt_hp') {
    try {

        if (empty($_POST['mt_hp'])) {
            throw new Exception('휴대폰번호를 입력해 주세요.');
        }

        // 숫자만 추출
        $mt_hp = preg_replace('/[^0-9]/', '', $_POST['mt_hp']);
        if (strlen($mt_hp) < 10 || strlen($mt_hp) > 11) {
            throw new Exception('올바른 휴대폰번호를 입력해 주세요.');
        }

        // ★ 핵심: 이미 가입된 회원인지 확인 (아이디/비번 찾기용이므로 가입된 번호만 허용)
        $DB->where('mt_hp', $mt_hp);
        $DB->where('mt_level', 2);
        $DB->where('mt_status', 'Y');  // 정상 회원만
        $member = $DB->getOne('member_t', 'idx, mt_id, mt_name');

        if (!$member) {
            throw new Exception('가입되지 않은 휴대폰번호입니다. 회원가입 후 이용해 주세요.');
        }

        $auth_code = mt_sms_make(); // 모든 인증에 고정 코드 사용 (테스트 편의)
        $message = "[맛집바로]\n휴대폰 인증 번호 : ".$auth_code;
        $result = bizppurio_send_sms($mt_hp, $message);

        // 세션 저장
        $_SESSION['_confirm_sms'] = $auth_code;
        $_SESSION['_confirm_hp']  = $mt_hp;
        $_SESSION['_confirm_member_idx'] = $member['idx'];  // 추가로 회원 idx 저장 (선택)
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        // 응답 (개발용으로 인증코드 직접 노출)
        echo json_encode([
            'success'    => true,
            'message'    => '인증번호가 발송되었습니다.',
            'auth_code'  => $auth_code  // 프론트에서 입력란에 자동 채우기 가능
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}else if($_POST['act']=="find_pw") {

    try {
        $mt_id = trim($_POST['mt_id'] ?? '');
        $mt_hp   = trim($_POST['mt_hp'] ?? '');

        if ($mt_id === '') {
            throw new Exception('아이디를 입력해 주세요.');
        }
        if ($mt_hp === '') {
            throw new Exception('휴대폰번호를 입력해 주세요.');
        }

//        // 휴대폰 인증 여부 확인
//        if (empty($_SESSION['_find_id_ok']) || $_SESSION['_find_id_ok'] !== true) {
//            throw new Exception('휴대폰 인증이 완료되지 않았습니다.');
//        }
//        if (empty($_SESSION['_find_id_hp']) || $_SESSION['_find_id_hp'] !== $mt_hp) {
//            throw new Exception('인증된 휴대폰번호와 일치하지 않습니다.');
//        }

        // 회원 검색
        $DB->where('mt_id', $mt_id);
        $DB->where('mt_hp', $mt_hp);
        $DB->where('mt_status', 'Y'); // 정상회원만
        $DB->where('mt_level', 2);
        $row = $DB->getOne('member_t', 'idx, mt_id');

        if (!$row) {
            throw new Exception('입력하신 정보와 일치하는 회원을 찾을 수 없습니다.');
        }

        $mt_id = $row['mt_id'];
        $encId = encrypt_member_id($mt_id);
        if (!$encId) {
            throw new Exception('아이디 암호화 중 오류가 발생했습니다.');
        }

        echo json_encode([
            'success'      => true,
            'message'      => '패스워드를 변경해주세요.',
            'encrypted_id' => $encId,
            'redirect'     => '/auth/find_pw_reset.php', // JS에서 이 URL + ?id= 로 이동
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        $DB->rollback();
        die(json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE));
    }
}else if($_POST['act']=="reset_pw") {

    try {
        $idToken = trim($_POST['id_token'] ?? '');
        $mt_pwd  = trim($_POST['mt_pwd'] ?? '');

        if ($idToken === '') {
            throw new Exception('유효하지 않은 접근입니다. 아이디 찾기부터 다시 진행해 주세요.');
        }
        if ($mt_pwd === '') {
            throw new Exception('새 비밀번호를 입력해 주세요.');
        }

        // 비밀번호 규칙 검증 (프론트와 동일한 조건)
        if (!preg_match('/^(?=.*[a-z])(?=.*\d)[A-Za-z\d!@#$%^&*()_+\-={}[\]:;"\'<>,.?\/]{8,16}$/', $mt_pwd)) {
            throw new Exception('비밀번호는 영소문자, 숫자 포함 8~16자로 입력해 주세요.');
        }

        // 🔐 토큰 복호화 → mt_id 얻기
        $mt_id = decrypt_member_id($idToken);
        if (!$mt_id) {
            throw new Exception('유효하지 않은 비밀번호 재설정 링크입니다. 다시 시도해 주세요.');
        }

        // 회원 조회 (정상회원만)
        $DB->where('mt_id', $mt_id);
        $DB->where('mt_status', 'Y');   // 탈퇴회원 제외
        // 필요하다면 관리자 제외:
        // $DB->where('mt_level', 1, '>');

        $member = $DB->getOne('member_t', 'idx, mt_id, mt_status');
        if (!$member) {
            throw new Exception('해당 회원 정보를 찾을 수 없습니다.');
        }

        $mt_idx = (int)$member['idx'];

        // 비밀번호 변경
        $DB->startTransaction();

        $arr_update = [
            'mt_pwd'   => password_hash($mt_pwd, PASSWORD_DEFAULT),
            'mt_udate' => $DB->now(),
        ];

        $DB->where('idx', $mt_idx);
        $ok = $DB->update('member_t', $arr_update);

        if (!$ok) {
            throw new Exception('비밀번호 변경에 실패했습니다. 잠시 후 다시 시도해 주세요.');
        }

        $DB->commit();

        // 비밀번호 변경 후, 보안상 세션/토큰 정리할 수 있음 (선택)
        // unset($_SESSION['some_reset_token']);

        echo json_encode([
            'success'  => true,
            'message'  => '비밀번호가 변경되었습니다.',
            'redirect' => '/auth/find_pw_cmp.php', // 결과 페이지
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        if ($DB && method_exists($DB, 'rollback')) {
            $DB->rollback();
        }

        echo json_encode([
            'success' => false,
            'message' => $e->getMessage(),
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

?>
