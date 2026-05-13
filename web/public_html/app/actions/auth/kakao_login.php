<?php
/**
 * 카카오 소셜 로그인 API
 * - 기존 회원: 바로 로그인
 * - 신규 회원: 자동 가입 후 로그인
 */

include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";

header('Content-Type: application/json; charset=UTF-8');

if($_POST['act'] == 'kakao_login') {
    try {
        // ===============================
        // 1. 필수 값 검증
        // ===============================
        if (empty($_POST['kakao_id'])) {
            throw new Exception('카카오 계정 정보가 올바르지 않습니다.');
        }

        $kakao_id = trim($_POST['kakao_id']);
        $email = trim($_POST['email'] ?? '');
        $nickname = trim($_POST['nickname'] ?? '');
        $profile_image = trim($_POST['profile_image'] ?? '');
        $appToken = $_SESSION['app_token'] ?? '';
        $appOS = $_SESSION['app_os'] ?? '';

        $DB->startTransaction();

        // ===============================
        // 2. 카카오 ID로 기존 회원 확인
        // ===============================
        $DB->where('mt_sns_id', 'kakao_' . $kakao_id);
        $DB->where('mt_type', 2); // 카카오 타입
        $DB->where('mt_level', 2, '>='); // 정상 회원 (탈퇴 제외)
        $DB->where('mt_rdate', NULL, 'IS'); // 탈퇴하지 않은 회원
        $row = $DB->getOne('member_t');

        // ===============================
        // 3-1. 기존 회원 → 로그인 처리
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
            // 최근 로그인 시간 및 프로필 이미지 업데이트
            $arr_update = array(
                'mt_ldate' => date('Y-m-d H:i:s', time()),
            );

            if (!empty($profile_image)) {
                $arr_update['mt_file1'] = $profile_image;
            }

            $DB->where('idx', $row['idx']);
            $DB->update('member_t', $arr_update);

            $DB->commit();

            // 세션 저장 (기존 login.php와 동일한 구조)
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

            // 리다이렉트 경로 결정
            if(isset($_SESSION['qr_token']) && $_SESSION['qr_token']){
                $redirect = APP_PAGE;
            } else {
                $redirect = MAP_PAGE;
            }

            echo json_encode([
                'success'  => true,
                'message'  => $row['mt_name'] . '님, 환영합니다!',
                'redirect' => $redirect,
                'is_new_member' => false
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // ===============================
        // 3-2. 신규 회원 → 자동 가입 후 로그인
        // ===============================

        // 아이디 생성 (kakao_ + 카카오ID)
        $mt_id = 'kakao_' . $kakao_id;

        // 닉네임 설정 (카카오 닉네임 또는 기본값)
        $mt_name = !empty($nickname) ? $nickname : 'kakao' . substr($kakao_id, 0, 8);
        $mt_nickname = $mt_name;

        // 회원 정보 INSERT
        $arr_query = array(
            "mt_type" => 2,  // 카카오
            "mt_level" => 2, // 일반 회원
            "mt_id" => $mt_id,
            "mt_sns_id" => 'kakao_' . $kakao_id,
            "mt_name" => $mt_name,
            "mt_nickname" => $mt_nickname,
            "mt_nickname_date" => $DB->now(),
            "mt_email" => $email,
            "mt_image1" => $profile_image, // 프로필 이미지
            "mt_hp" => '', // 카카오는 휴대폰 번호 없음
            "mt_smsing" => "Y",
            "mt_mailing" => "Y",
            "mt_pushing1" => "Y",
            "mt_status" => "Y",
            "mt_notice_push" => "Y",
            "mt_push" => "Y",
            "mt_language" => 1,
            'mt_wdate' => $DB->now(),
            'mt_ldate' => $DB->now(),
        );

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

        // 가입한 회원 정보 조회
        $query = "SELECT * FROM member_t WHERE idx = '".$_mt_last_idx."'";
        $new_member = $DB->rawQueryOne($query);

        // 세션 저장 (기존 login.php와 동일한 구조)
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

        // 리다이렉트 경로 결정
        if(isset($_SESSION['qr_token']) && $_SESSION['qr_token']){
            $redirect = APP_PAGE;
        } else {
            $redirect = MAP_PAGE;
        }

        echo json_encode([
            'success'  => true,
            'message'  => '카카오 로그인에 성공했습니다. 환영합니다!',
            'redirect' => $redirect,
            'is_new_member' => true
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        $DB->rollback();
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

?>
