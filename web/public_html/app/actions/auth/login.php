<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";

header('Content-Type: application/json');

if($_POST['act']=="login") {
    try {
        if (empty($_POST['mt_id']) || empty($_POST['mt_pwd'])) {
            throw new Exception('아이디와 비밀번호를 모두 입력해 주세요.');
        }

        $mt_id  = $_POST['mt_id'];
        $mt_pwd = $_POST['mt_pwd'];

        $DB->where('mt_id', $mt_id);
        $DB->where('mt_level', 2, '=');
        $DB->where('mt_rdate', NULL, 'IS');
        $row = $DB->getOne('member_t');

        if (!$row || !password_verify($mt_pwd, $row['mt_pwd'])) {
            throw new Exception('아이디 또는 비밀번호를 다시 확인해 주세요.');
        }

        if ($row['mt_level'] === 1){
            throw new Exception('탈퇴된 회원입니다.');
        }

        $appToken = $_SESSION['app_token'] ?? '';
        $appOS = $_SESSION['app_os'] ?? '';

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


        // 세션 저장 (예: $_SESSION['mng'])
        $_SESSION['mng'] = [
            'mt_idx'   => $row['idx'],
            'mt_id'    => $row['mt_id'],
            'mt_sns_id'    => $row['mt_sns_id'],
            'mt_email'    => $row['mt_email'],
            'mt_hp'    => $row['mt_hp'],
            'mt_name'  => $row['mt_name'],
            'mt_nickname' => $row['mt_nickname'],
            'mt_type' => $row['mt_type'],
            'mt_level' => $row['mt_level'],
            'mt_grade' => $row['mt_grade'],
//            'profile_url' => $profile,
            'mt_language' => $row['mt_language'],
        ];

        if(isset($_SESSION['qr_token']) && $_SESSION['qr_token']){
            $redirect =  APP_PAGE;
        }else{
            $redirect = MAP_PAGE;
        }

        echo json_encode([
            'success'  => true,
            'message'  => '로그인에 성공했습니다.',
            'redirect' => $redirect // 메인으로
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
