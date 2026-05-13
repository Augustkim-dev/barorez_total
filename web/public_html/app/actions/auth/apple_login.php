<?php
/**
 * 애플 소셜 로그인 API (구글 로그인과 동일 구조)
 * - 기존 회원: 바로 로그인
 * - 신규 회원: 자동 가입 후 로그인
 * - state 검증은 콜백 페이지에서 localStorage로만 처리하므로 여기서는 생략
 */

include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";

header('Content-Type: application/json; charset=UTF-8');

if (($_POST['act'] ?? '') === 'apple_login') {
    try {
        // ===============================
        // 1. 필수 값 검증
        // ===============================
        if (empty($_POST['id_token'])) {
            throw new Exception('애플 인증 토큰(id_token)이 누락되었습니다.');
        }

        $id_token    = trim((string)$_POST['id_token']);
        $user_json   = trim((string)($_POST['user'] ?? '')); // 최초 로그인 시만 전달
        $auto_login  = (($_POST['auto_login'] ?? 'N') === 'Y') ? 'Y' : 'N';
        $appOS = $_SESSION['app_os'];
        $appToken = $_SESSION['app_token'] ?? '';

        // state는 콜백 페이지에서 이미 검증했으므로 여기서는 생략
        // (구글/네이버/카카오와 동일하게)

        // ===============================
        // 2. id_token 검증 & 사용자 정보 추출
        // ===============================

        // id_token은 JWT 형식 → 헤더.페이로드.서명
        $parts = explode('.', $id_token);
        if (count($parts) !== 3) {
            throw new Exception('유효하지 않은 id_token 형식입니다.');
        }

        // 페이로드 디코딩 (Base64 URL-safe)
        $payloadJson = base64UrlDecode($parts[1]);
        $payload = json_decode($payloadJson, true);

        if (!$payload || empty($payload['sub'])) {
            throw new Exception('애플 사용자 식별값(sub)을 추출할 수 없습니다.');
        }

        $apple_sub = trim((string)$payload['sub']);
        $email     = trim((string)($payload['email'] ?? ''));

        // 최초 로그인 시 user JSON 파싱 (이름 받기)
        $firstName = $lastName = '';
        if ($user_json) {
            $userData = json_decode($user_json, true);
            $firstName = $userData['name']['firstName'] ?? '';
            $lastName  = $userData['name']['lastName'] ?? '';
        }

        $mt_name = trim($lastName . ' ' . $firstName);
        if (empty($mt_name)) {
            $mt_name = 'AppleUser_' . substr($apple_sub, 0, 8);
        }

        $mt_nickname = $mt_name;

        // ===============================
        // 3. DB 처리 (구글과 동일 로직)
        // ===============================
        $DB->startTransaction();

        // 애플 회원 식별자 (고유값)
        $sns_id = 'apple_' . $apple_sub;

        // mt_type 값은 프로젝트에 맞게 설정 (구글 4라면 애플은 5로 가정)
        $APPLE_MT_TYPE = 5; // ← 필요 시 수정

        // 기존 회원 조회
        $DB->where('mt_sns_id', $sns_id);
        $DB->where('mt_type', $APPLE_MT_TYPE);
        $DB->where('mt_level', 2, '>=');
        $DB->where('mt_rdate', NULL, 'IS');
        $row = $DB->getOne('member_t');

        // ===============================
        // 3-1. 기존 회원 → 로그인
        // ===============================
        if ($row) {
            if (!empty($appOS) && !empty($appToken)) {
                // 본인을 제외한 다른 계정에서만 동일 토큰 제거
                $DB->where('mt_app_token', $appToken);
                $DB->where('idx', $row['idx'], '!=');
                $DB->update('member_t', [
                    'mt_app_token' => ''
                ]);

                // 현재 계정에 토큰 저장
                $DB->where('idx', $row['idx']);
                $DB->update('member_t', [
                    'mt_app_token' => $appToken
                ]);
            }

            $arr_update = [
                'mt_ldate' => date('Y-m-d H:i:s'),
            ];

            // 이메일 업데이트 (최초 제공 후 비어있을 수 있음)
            if (!empty($email) && empty($row['mt_email'])) {
                $arr_update['mt_email'] = $email;
            }

            $DB->where('idx', $row['idx']);
            $DB->update('member_t', $arr_update);

            $DB->commit();

            // 세션 생성 (구글과 동일)
            $_SESSION['mng'] = [
                'mt_idx'      => $row['idx'],
                'mt_id'       => $row['mt_id'],
                'mt_sns_id'   => $row['mt_sns_id'],
                'mt_email'    => $row['mt_email'],
                'mt_hp'       => $row['mt_hp'],
                'mt_name'     => $row['mt_name'],
                'mt_nickname' => $row['mt_nickname'],
                'mt_type'     => $row['mt_type'],
                'mt_level'    => $row['mt_level'],
                'mt_grade'    => $row['mt_grade'],
                'mt_language' => $row['mt_language'],
            ];

            $redirect = (isset($_SESSION['qr_token']) && $_SESSION['qr_token'])
                ? APP_PAGE
                : MAP_PAGE;

            echo json_encode([
                'success'       => true,
                'message'       => $row['mt_name'] . '님, 환영합니다!',
                'redirect'      => $redirect,
                'is_new_member' => false
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // ===============================
        // 3-2. 신규 회원 → 자동 가입
        // ===============================
        $mt_id = $sns_id; // apple_{sub} 형태

        $arr_query = [
            "mt_type"       => $APPLE_MT_TYPE,
            "mt_level"      => 2,
            "mt_id"         => $mt_id,
            "mt_sns_id"     => $sns_id,
            "mt_name"       => $mt_name,
            "mt_nickname"   => $mt_nickname,
            "mt_nickname_date" => $DB->now(),
            "mt_email"      => $email,
            "mt_hp"         => "",
            "mt_smsing"     => "Y",
            "mt_mailing"    => "Y",
            "mt_pushing1"   => "Y",
            "mt_status"     => "Y",
            "mt_notice_push"=> "Y",
            "mt_push"       => "Y",
            "mt_language"   => 1,
            'mt_wdate'      => $DB->now(),
            'mt_ldate'      => $DB->now(),
        ];

        $new_idx = $DB->insert('member_t', $arr_query);

        if (!$new_idx) {
            throw new Exception('회원가입 처리 중 오류가 발생했습니다.');
        }

        if (!empty($appOS) && !empty($appToken)) {
            // 본인을 제외한 다른 계정에서만 동일 토큰 제거
            $DB->where('mt_app_token', $appToken);
            $DB->where('idx', $new_idx, '!=');
            $DB->update('member_t', [
                'mt_app_token' => ''
            ]);

            // 현재 계정에 토큰 저장
            $DB->where('idx', $new_idx);
            $DB->update('member_t', [
                'mt_app_token' => $appToken
            ]);
        }

        $DB->commit();

        $new_member = $DB->rawQueryOne("SELECT * FROM member_t WHERE idx = '" . $new_idx . "'");

        $_SESSION['mng'] = [
            'mt_idx'      => $new_member['idx'],
            'mt_id'       => $new_member['mt_id'],
            'mt_sns_id'   => $new_member['mt_sns_id'],
            'mt_email'    => $new_member['mt_email'],
            'mt_hp'       => $new_member['mt_hp'],
            'mt_name'     => $new_member['mt_name'],
            'mt_nickname' => $new_member['mt_nickname'],
            'mt_type'     => $new_member['mt_type'],
            'mt_level'    => $new_member['mt_level'],
            'mt_grade'    => $new_member['mt_grade'],
            'mt_language' => $new_member['mt_language'],
        ];

        $redirect = (isset($_SESSION['qr_token']) && $_SESSION['qr_token'])
            ? APP_PAGE
            : MAP_PAGE;

        echo json_encode([
            'success'       => true,
            'message'       => '애플 로그인에 성공했습니다. 환영합니다!',
            'redirect'      => $redirect,
            'is_new_member' => true
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        if (isset($DB)) $DB->rollback();
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

/**
 * Base64 URL-safe 디코딩 헬퍼 (id_token 페이로드 추출용)
 */
function base64UrlDecode($input) {
    $remainder = strlen($input) % 4;
    if ($remainder) {
        $padlen = 4 - $remainder;
        $input .= str_repeat('=', $padlen);
    }
    return base64_decode(strtr($input, '-_', '+/'));
}
?>
