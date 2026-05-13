<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";

header('Content-Type: application/json');

if($_POST['act']=='profile') {
    try {

        // ------------------------------
        // 1. 로그인 체크
        // ------------------------------
        if (empty($_SESSION['mng']) || empty($_SESSION['mng']['mt_idx'])) {
            throw new Exception('로그인이 필요합니다.');
        }

        $mt_idx = (int)$_SESSION['mng']['mt_idx'];

        // ------------------------------
        // 3. 기본 값 검증
        // ------------------------------
        $mt_id      = trim($_POST['mt_id']   ?? '');
        $mt_name    = trim($_POST['mt_name'] ?? '');
        $mt_hp_raw  = trim($_POST['mt_hp']   ?? '');
        $mt_hp_chk  = $_POST['mt_hp_chk']    ?? 'N';

        // 휴대폰 숫자만
        $mt_hp = preg_replace('/[^0-9]/', '', $mt_hp_raw);

        if ($mt_id === '') {
            throw new Exception('아이디가 정상적이지 않습니다.');
        }
        if ($mt_name === '') {
            throw new Exception('이름을 입력해주세요.');
        }
        if ($mt_hp === '') {
            throw new Exception('휴대폰 번호를 입력해주세요.');
        }
        if (!preg_match('/^[0-9]{10,11}$/', $mt_hp)) {
            throw new Exception('휴대폰 번호 형식이 올바르지 않습니다.');
        }

        // ------------------------------
        // 4. 현재 회원 정보 조회
        // ------------------------------
        $DB->where('idx', $mt_idx);
        $member = $DB->getOne('member_t');

        if (!$member) {
            throw new Exception('회원 정보를 찾을 수 없습니다.');
        }

        // 아이디 위변조 방지 (세션 아이디와 다르면 막기)
        if (!empty($member['mt_id']) && $member['mt_id'] !== $mt_id) {
            throw new Exception('아이디 정보가 일치하지 않습니다.');
        }

        // ------------------------------
        // 5. 휴대폰 변경 여부 / 인증 여부 체크
        // ------------------------------
        $orig_hp = preg_replace('/[^0-9]/', '', ($member['mt_hp'] ?? ''));

        $is_hp_changed = ($mt_hp !== $orig_hp);

        if ($is_hp_changed) {
            // 번호가 바뀌었는데 인증 완료 플래그가 아니라면 차단
            if ($mt_hp_chk !== 'Y') {
                throw new Exception('휴대폰 번호 변경 시 인증이 필요합니다. 인증을 완료해 주세요.');
            }

            // 다른 회원이 사용 중인지 체크
            $DB->where('mt_hp', $mt_hp);
            $DB->where('idx', $mt_idx, '!=');
            $hp_row = $DB->getOne('member_t', 'idx');
            if ($hp_row) {
                throw new Exception('이미 사용 중인 휴대폰 번호입니다.');
            }
        }

        // ------------------------------
        // 6. 비밀번호 변경 여부 체크
        // ------------------------------
        $mt_pwd      = trim($_POST['mt_pwd'] ?? '');
        $hasPwChange = ($mt_pwd !== '');

        if ($hasPwChange) {
            // 회원가입 때와 같은 규칙: 영문+숫자 포함 8~16자
            if (!preg_match('/^(?=.*[a-zA-Z])(?=.*\d)[A-Za-z\d!@#$%^&*()_+\-=]{8,16}$/', $mt_pwd)) {
                throw new Exception('비밀번호는 8~16자의 영문/숫자 조합이어야 합니다.');
            }
        }

        // ------------------------------
        // 7. DB 업데이트
        // ------------------------------
        $DB->startTransaction();

        $arr_update = [
            'mt_name'     => $mt_name,
            'mt_nickname' => $mt_name,   // 닉네임을 이름과 동일하게 유지
            'mt_hp'       => $mt_hp,
            'mt_udate'    => $DB->now(),
        ];

        if ($hasPwChange) {
            $arr_update['mt_pwd'] = password_hash($mt_pwd, PASSWORD_DEFAULT);
        }

        $DB->where('idx', $mt_idx);
        $ok = $DB->update('member_t', $arr_update);

        if (!$ok) {
            throw new Exception('회원정보 수정에 실패했습니다. 잠시 후 다시 시도해 주세요.');
        }

        $DB->commit();

        // ------------------------------
        // 8. 세션 정보 갱신
        // ------------------------------
        $_SESSION['mng']['mt_name'] = $mt_name;
        $_SESSION['mng']['mt_hp']   = $mt_hp;

        $json = [
            'success'  => true,
            'message'  => '회원정보가 수정되었습니다.',
            'redirect' => APP_PAGE . '/my/mypage.php',  // 필요 없으면 제거해도 됨
        ];

        echo json_encode($json, JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {

        // 트랜잭션 롤백 (실패 시)
        if ($DB && method_exists($DB, 'rollback')) {
            $DB->rollback();
        }

        echo json_encode([
            'success' => false,
            'message' => $e->getMessage(),
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}elseif ($_POST['act'] == 'pass_check') {
    try {
        // 세션에서 회원 ID 가져오기
        $mt_idx = (int)($_SESSION['mng']['mt_idx'] ?? $_SESSION['mt_idx'] ?? 0);
        if ($mt_idx <= 0) {
            throw new Exception('로그인이 필요합니다.');
        }

        $password = trim($_POST['mt_pwd'] ?? '');
        if ($password === '') {
            throw new Exception('비밀번호를 입력해주세요.');
        }

        // 프론트와 동일한 규칙으로 사전 검증 (불필요한 DB 조회 방지)
        if (!preg_match('/^(?=.*[a-z])(?=.*\d)[A-Za-z\d!@#$%^&*()_+\-={}[\]:;"\'<>,.?\/]{8,16}$/', $password)) {
            throw new Exception('비밀번호는 영소문자, 숫자 포함 8~16자로 입력해주세요.');
        }

        // 회원 정보 조회
        $DB->where('idx', $mt_idx);
        $DB->where('mt_status', 'Y'); // 탈퇴회원 제외 (보안 강화)
        $member = $DB->getOne('member_t', ['mt_pwd']);

        if (!$member || empty($member['mt_pwd'])) {
            throw new Exception('회원 정보 또는 비밀번호 정보를 찾을 수 없습니다.');
        }

        // 비밀번호 검증
        if (!password_verify($password, $member['mt_pwd'])) {
            // 보안을 위해 "비밀번호 불일치" 대신 일반 메시지 (타이밍 공격 방지)
            throw new Exception('비밀번호가 확인되지 않습니다.');
        }

        // 성공 시 세션 플래그 설정 (회원정보 수정 페이지에서 재검증 가능)
        $_SESSION['pw_verified'] = true;
        $_SESSION['pw_verified_at'] = time(); // 유효시간 제한 가능

        echo json_encode([
            'success' => true,
            'message' => '비밀번호가 확인되었습니다.'
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}
?>
