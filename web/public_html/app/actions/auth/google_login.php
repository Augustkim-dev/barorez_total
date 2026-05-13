<?php
/**
 * 구글 소셜 로그인 API
 * - 기존 회원: 바로 로그인
 * - 신규 회원: 자동 가입 후 로그인
 *
 * 입력값:
 * act=google_login
 *
 * [웹 방식]
 * - login_mode=web
 * - code (필수)
 * - state (필수)
 * - redirect_uri (필수)
 * - auto_login (선택) Y/N
 *
 * [앱 방식]
 * - login_mode=app
 * - id_token (권장)
 *   또는 access_token (예비)
 * - auto_login (선택) Y/N
 */

include $_SERVER['DOCUMENT_ROOT'] . "/cfg/lib.inc.php";

header('Content-Type: application/json; charset=UTF-8');

if (($_POST['act'] ?? '') === 'google_login') {
    try {
        // ===============================
        // 1. 공통 기본값
        // ===============================
        $login_mode = trim((string)($_POST['login_mode'] ?? 'web'));
        $auto_login = (($_POST['auto_login'] ?? 'N') === 'Y') ? 'Y' : 'N';
        $appToken = $_SESSION['app_token'] ?? '';
        $appOS = $_SESSION['app_os'] ?? '';

        if (!defined('GOOGLE_CLIENT_ID') || !defined('GOOGLE_CLIENT_SECRET')) {
            throw new Exception('구글 Client 설정이 누락되었습니다. (CLIENT_ID/SECRET)');
        }

        $google_sub = '';
        $email      = '';
        $name       = '';
        $nick       = '';
        $profile    = '';

        // ===============================
        // 2. 로그인 방식별 사용자 정보 확보
        // ===============================
        if ($login_mode === 'web') {
            // --------------------------------
            // 웹 OAuth 방식
            // code/state -> token -> userinfo
            // --------------------------------
            if (empty($_POST['code']) || empty($_POST['state'])) {
                throw new Exception('구글 인증 정보가 올바르지 않습니다. (code/state 누락)');
            }

            if (empty($_POST['redirect_uri'])) {
                throw new Exception('redirect_uri가 누락되었습니다.');
            }

            $code         = trim((string)$_POST['code']);
            $state        = trim((string)$_POST['state']);
            $redirect_uri = trim((string)$_POST['redirect_uri']);

            $tokenUrl = "https://oauth2.googleapis.com/token";

            $tokenRes = curl_json_post_form($tokenUrl, [
                'code'          => $code,
                'client_id'     => GOOGLE_CLIENT_ID,
                'client_secret' => GOOGLE_CLIENT_SECRET,
                'redirect_uri'  => $redirect_uri,
                'grant_type'    => 'authorization_code',
            ], 15);

            if (empty($tokenRes['access_token'])) {
                $msg = $tokenRes['error_description'] ?? ($tokenRes['error'] ?? '구글 토큰 발급 실패');
                throw new Exception('구글 토큰 발급에 실패했습니다. ' . $msg);
            }

            $accessToken = (string)$tokenRes['access_token'];

            $profileUrl = "https://openidconnect.googleapis.com/v1/userinfo";
            $profileRes = curl_json_get($profileUrl, 15, [
                "Authorization: Bearer " . $accessToken,
            ]);

            $google_sub = trim((string)($profileRes['sub'] ?? ''));
            if (!$google_sub) {
                throw new Exception('구글 사용자 식별값(sub)을 가져오지 못했습니다.');
            }

            $email   = trim((string)($profileRes['email'] ?? ''));
            $name    = trim((string)($profileRes['name'] ?? ''));
            $nick    = trim((string)($profileRes['given_name'] ?? ''));
            $profile = trim((string)($profileRes['picture'] ?? ''));

        } else if ($login_mode === 'app') {
            // --------------------------------
            // 앱 네이티브 로그인 방식
            // id_token 검증 우선
            // access_token은 예비 분기
            // --------------------------------
            $id_token     = trim((string)($_POST['id_token'] ?? ''));
            $access_token = trim((string)($_POST['access_token'] ?? ''));

            if ($id_token !== '') {
                // id_token 검증
                $tokenInfoUrl = "https://oauth2.googleapis.com/tokeninfo?id_token=" . urlencode($id_token);
                $tokenInfoRes = curl_json_get($tokenInfoUrl, 15);

                // aud 검증
                $aud = trim((string)($tokenInfoRes['aud'] ?? ''));
                if ($aud !== GOOGLE_CLIENT_ID) {
                    throw new Exception('구글 id_token 검증에 실패했습니다. (aud 불일치)');
                }

                $google_sub = trim((string)($tokenInfoRes['sub'] ?? ''));
                if (!$google_sub) {
                    throw new Exception('구글 사용자 식별값(sub)을 가져오지 못했습니다.');
                }

                $email   = trim((string)($tokenInfoRes['email'] ?? ''));
                $name    = trim((string)($tokenInfoRes['name'] ?? ''));
                $nick    = trim((string)($tokenInfoRes['given_name'] ?? ''));
                $profile = trim((string)($tokenInfoRes['picture'] ?? ''));

            } else if ($access_token !== '') {
                // 예비: access_token 으로 userinfo 조회
                $profileUrl = "https://openidconnect.googleapis.com/v1/userinfo";
                $profileRes = curl_json_get($profileUrl, 15, [
                    "Authorization: Bearer " . $access_token,
                ]);

                $google_sub = trim((string)($profileRes['sub'] ?? ''));
                if (!$google_sub) {
                    throw new Exception('구글 사용자 식별값(sub)을 가져오지 못했습니다.');
                }

                $email   = trim((string)($profileRes['email'] ?? ''));
                $name    = trim((string)($profileRes['name'] ?? ''));
                $nick    = trim((string)($profileRes['given_name'] ?? ''));
                $profile = trim((string)($profileRes['picture'] ?? ''));

            } else {
                throw new Exception('앱 로그인용 구글 토큰(id_token / access_token)이 누락되었습니다.');
            }

        } else {
            throw new Exception('지원하지 않는 로그인 방식입니다.');
        }

        // ===============================
        // 3. 기본 이름 처리
        // ===============================
        $mt_name = $name ?: ($nick ?: ('google' . substr($google_sub, 0, 8)));
        $mt_nickname = $nick ?: $mt_name;

        // ===============================
        // 4. DB 처리
        // ===============================
        $DB->startTransaction();

        $sns_id = 'google_' . $google_sub;
        $GOOGLE_MT_TYPE = 4;

        $DB->where('mt_sns_id', $sns_id);
        $DB->where('mt_type', $GOOGLE_MT_TYPE);
        $DB->where('mt_level', 2, '>=');
        $DB->where('mt_rdate', NULL, 'IS');
        $row = $DB->getOne('member_t');

        // ===============================
        // 4-1. 기존 회원 로그인
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

            if (!empty($profile)) {
                $arr_update['mt_image1'] = $profile;
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

        // ===============================
        // 4-2. 신규 회원 자동 가입
        // ===============================
        $mt_id = $sns_id;

        $arr_query = [
            "mt_type" => $GOOGLE_MT_TYPE,
            "mt_level" => 2,
            "mt_id" => $mt_id,
            "mt_sns_id" => $sns_id,
            "mt_name" => $mt_name,
            "mt_nickname" => $mt_nickname,
            "mt_nickname_date" => $DB->now(),
            "mt_email" => $email,
            "mt_image1" => $profile,
            "mt_hp" => "",
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
            'message'       => '구글 로그인에 성공했습니다. 환영합니다!',
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

/**
 * cURL JSON GET
 */
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

/**
 * Google code -> token 발급용 POST(form)
 */
function curl_json_post_form($url, $data, $timeout = 15, $headers = [])
{
    $ch = curl_init();

    $baseHeaders = ['Content-Type: application/x-www-form-urlencoded'];
    if (!empty($headers)) {
        $baseHeaders = array_merge($baseHeaders, $headers);
    }

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $baseHeaders);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

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

    if ($httpCode < 200 || $httpCode >= 300) {
        $msg = $json['error_description'] ?? ($json['error'] ?? $response);
        throw new Exception('토큰 발급 실패 (HTTP ' . $httpCode . '): ' . $msg);
    }

    return $json;
}
?>
