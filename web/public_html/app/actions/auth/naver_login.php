<?php
include $_SERVER['DOCUMENT_ROOT'] . "/cfg/lib.inc.php";

header('Content-Type: application/json; charset=UTF-8');

if (($_POST['act'] ?? '') === 'naver_login') {
    try {
        $login_mode = trim($_POST['login_mode'] ?? 'web'); // web | app
        $auto_login = (($_POST['auto_login'] ?? 'N') === 'Y') ? 'Y' : 'N';
        $appToken = $_SESSION['app_token'] ?? '';
        $appOS = $_SESSION['app_os'] ?? '';

        if (!defined('NAVER_CLIENT_ID') || !defined('NAVER_CLIENT_SECRET')) {
            throw new Exception('네이버 Client 설정이 누락되었습니다. (CLIENT_ID/SECRET)');
        }

        $accessToken = '';

        /**
         * ===============================
         * 1. 로그인 방식별 access_token 확보
         * ===============================
         */
        if ($login_mode === 'web') {
            // 웹 OAuth 방식
            if (empty($_POST['code']) || empty($_POST['state'])) {
                throw new Exception('네이버 인증 정보가 올바르지 않습니다. (code/state 누락)');
            }

            if (empty($_POST['redirect_uri'])) {
                throw new Exception('redirect_uri가 누락되었습니다.');
            }

            $code         = trim($_POST['code']);
            $state        = trim($_POST['state']);
            $redirect_uri = trim($_POST['redirect_uri']);

            $tokenUrl = "https://nid.naver.com/oauth2.0/token"
                . "?grant_type=authorization_code"
                . "&client_id=" . urlencode(NAVER_CLIENT_ID)
                . "&client_secret=" . urlencode(NAVER_CLIENT_SECRET)
                . "&code=" . urlencode($code)
                . "&state=" . urlencode($state)
                . "&redirect_uri=" . urlencode($redirect_uri);

            $tokenRes = curl_json_get($tokenUrl, 15);

            if (empty($tokenRes['access_token'])) {
                $msg = $tokenRes['error_description'] ?? ($tokenRes['error'] ?? '네이버 토큰 발급 실패');
                throw new Exception('네이버 토큰 발급에 실패했습니다. ' . $msg);
            }

            $accessToken = $tokenRes['access_token'];

        } else if ($login_mode === 'app') {
            // 앱 네이티브 로그인 방식
            if (empty($_POST['access_token'])) {
                throw new Exception('앱 로그인용 access_token 이 누락되었습니다.');
            }

            $accessToken = trim($_POST['access_token']);

        } else {
            throw new Exception('지원하지 않는 로그인 방식입니다.');
        }

        /**
         * ===============================
         * 2. 사용자 정보 조회
         * ===============================
         * - 웹/앱 모두 동일하게 access_token 으로 조회
         */
        $profileUrl = "https://openapi.naver.com/v1/nid/me";
        $profileRes = curl_json_get($profileUrl, 15, [
            "Authorization: Bearer " . $accessToken
        ]);

        if (!isset($profileRes['resultcode']) || $profileRes['resultcode'] !== '00') {
            $msg = $profileRes['message'] ?? '네이버 사용자 정보 조회 실패';
            throw new Exception('네이버 사용자 정보 조회에 실패했습니다. ' . $msg);
        }

        $response = $profileRes['response'] ?? [];
        $naver_id = trim($response['id'] ?? '');

        if (!$naver_id) {
            throw new Exception('네이버 사용자 식별값(id)을 가져오지 못했습니다.');
        }

        $email   = trim($response['email'] ?? '');
        $name    = trim($response['name'] ?? '');
        $nick    = trim($response['nickname'] ?? '');
        $mobile  = trim($response['mobile'] ?? '');
        $profile = trim($response['profile_image'] ?? '');

        $mt_name = $name ?: ($nick ?: ('naver' . substr($naver_id, 0, 8)));
        $mt_nickname = $nick ?: $mt_name;

        /**
         * ===============================
         * 3. 기존 회원 조회 / 신규 가입
         * ===============================
         */
        $DB->startTransaction();

        $sns_id = 'naver_' . $naver_id;

        $DB->where('mt_sns_id', $sns_id);
        $DB->where('mt_type', 3);
        $DB->where('mt_level', 2, '>=');
        $DB->where('mt_rdate', NULL, 'IS');
        $row = $DB->getOne('member_t');

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

            if (!empty($profile)) {
                $arr_update['mt_file1'] = $profile;
            }

            if (!empty($email) && empty($row['mt_email'])) {
                $arr_update['mt_email'] = $email;
            }

            $DB->where('idx', $row['idx']);
            $DB->update('member_t', $arr_update);

            $DB->commit();

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

            if (isset($_SESSION['qr_token']) && $_SESSION['qr_token']) {
                $redirect = APP_PAGE;
            } else {
                $redirect = MAP_PAGE;
            }

            echo json_encode([
                'success'       => true,
                'message'       => $row['mt_name'] . '님, 환영합니다!',
                'redirect'      => $redirect,
                'is_new_member' => false
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $mt_id = $sns_id;

        $arr_query = [
            "mt_type" => 3,
            "mt_level" => 2,
            "mt_id" => $mt_id,
            "mt_sns_id" => $sns_id,
            "mt_name" => $mt_name,
            "mt_nickname" => $mt_nickname,
            "mt_nickname_date" => $DB->now(),
            "mt_email" => $email,
            "mt_image1" => $profile,
            "mt_hp" => $mobile,
            "mt_smsing" => "Y",
            "mt_mailing" => "Y",
            "mt_pushing1" => "Y",
            "mt_status" => "Y",
            "mt_notice_push" => "Y",
            "mt_push" => "Y",
            "mt_language" => 1,
            'mt_wdate' => $DB->now(),
            'mt_ldate' => $DB->now(),
        ];

        $_mt_last_idx = $DB->insert('member_t', $arr_query);

        if (!$_mt_last_idx) {
            throw new Exception('회원가입 처리 중 오류가 발생했습니다.');
        }

        if (!empty($appOS) && !empty($appToken)) {
            // 본인을 제외한 다른 계정에서만 동일 토큰 제거
            $DB->where('mt_app_token', $appToken);
            $DB->where('idx', $_mt_last_idx, '!=');
            $DB->update('member_t', [
                'mt_app_token' => ''
            ]);

            // 현재 계정에 토큰 저장
            $DB->where('idx', $_mt_last_idx);
            $DB->update('member_t', [
                'mt_app_token' => $appToken
            ]);
        }

        $DB->commit();

        $new_member = $DB->rawQueryOne("SELECT * FROM member_t WHERE idx = '" . $_mt_last_idx . "'");

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

        if (isset($_SESSION['qr_token']) && $_SESSION['qr_token']) {
            $redirect = APP_PAGE;
        } else {
            $redirect = MAP_PAGE;
        }

        echo json_encode([
            'success'       => true,
            'message'       => '네이버 로그인에 성공했습니다. 환영합니다!',
            'redirect'      => $redirect,
            'is_new_member' => true
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        if (isset($DB)) {
            $DB->rollback();
        }

        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

function curl_json_get($url, $timeout = 15, $headers = [])
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

    if (!empty($headers)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

    $response = curl_exec($ch);

    if ($response === false) {
        $err = curl_error($ch);
        curl_close($ch);
        throw new Exception('cURL 요청 실패: ' . $err);
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $json = json_decode($response, true);

    if (!is_array($json)) {
        throw new Exception('서버 응답이 JSON 형식이 아닙니다. (HTTP ' . $httpCode . ')');
    }

    return $json;
}
?>
