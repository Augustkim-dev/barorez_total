<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/cfg/bizppurio.lib.php";

header('Content-Type: application/json');

if($_POST['act']=='join') {
    try {
        if (empty($_POST['mt_id'])) {
            throw new Exception('아이디를 입력해주세요.');
        }
        if (empty($_POST['mt_pw'])) {
            throw new Exception('패스워드를 입력해주세요.');
        }
        if (empty($_POST['mt_name'])) {
            throw new Exception('이름을 입력해주세요.');
        }

        if($_POST['mt_id_chk']!="Y"){
            throw new Exception('아이디 중복확인이 되지 않았습니다.');
        }
        if($_POST['mt_hp_chk']!="Y"){
            throw new Exception('휴대폰 인증이 완료되지 않았습니다.');
        }

        $DB->startTransaction();

        $DB->where('mt_id', $_POST['mt_id']);
        $row = $DB->getOne('member_t', '*, idx as mt_idx');
        if ($row) {
            throw new Exception('현재 다른사람이 사용중인 아이디 입니다.');
        }


        //$DB->where('mt_nickname', $_POST['mt_nickname']);
        //$row = $DB->getOne('member_t', '*, idx as mt_idx');
        //if ($row) {
        //  throw new Exception($CFG_LANG['auth']['nickname_taken']);
        //}

//        $current_lang = $_SESSION['user_lang'] ?? 'ko';
//        $locale_row = $DB->rawQueryOne("SELECT idx FROM locale_t WHERE lang = ? AND w_show = 'Y' LIMIT 1", [$current_lang]);
//        $mt_language_idx = $locale_row['idx'] ?? 2;


        //$id = uniqid('user_');
        unset($arr_query);
        $arr_query = array(
            "mt_type" => 1,
            "mt_level" => 2,
            "mt_id" => $_POST['mt_id'],
            "mt_pwd" => password_hash($_POST['mt_pw'], PASSWORD_DEFAULT),
            "mt_name" => $_POST['mt_name'],
            "mt_nickname" => $_POST['mt_name'],
            "mt_hp" => $_POST['mt_hp'],
            "mt_birth" => $_POST['mt_birth'],
            "mt_gender" => $_POST['mt_gender'],
            "mt_email" => $_POST['mt_email'],
            "mt_zip" => $_POST['mt_zip'],
            "mt_add1" => $_POST['mt_add1'],
            "mt_add2" => $_POST['mt_add2'],
            "mt_smsing" => "Y",
            "mt_mailing" => "Y",
            "mt_pushing1" => "Y",
            "mt_status" => "Y",
            "mt_notice_push" => "Y",
            "mt_push" => "Y",
            "mt_language" => 1,
            'mt_wdate' => $DB->now(),
        );
        $_mt_last_idx = $DB->insert('member_t', $arr_query);

        $DB->commit();

        $query = "select * from member_t where idx = '".$_mt_last_idx."'";
        $row = $DB->rawQueryOne($query);

//        $profile = $ct_no_profile_url;
//        $_SESSION['user'] = [
//            'mt_idx'   => $row['idx'],
//            'mt_id'    => $row['mt_id'],
//            'mt_sns_id'    => $row['mt_sns_id'],
//            'mt_email'    => $row['mt_email'],
//            'mt_hp'    => $row['mt_hp'],
//            'mt_name'  => $row['mt_name'],
//            'mt_nickname' => $row['mt_nickname'],
//            'mt_type' => $row['mt_type'],
//            'mt_level' => $row['mt_level'],
//            'mt_grade' => $row['mt_grade'],
////            'profile_url' => $profile,
//            'mt_language' => $row['mt_language'],
//        ];
        $json = [ 'success' => true, 'message' => $CFG_LANG['auth']['finish_msg1'], 'redirect' => '/auth/complete.php'];
        die(json_encode($json, JSON_UNESCAPED_UNICODE));





    } catch (Exception $e) {
        $DB->rollback();
        die(json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE));
    }
}
else if($_POST['act']=='join_sns') {

    try {

        if (empty($_POST['mt_sns_id'])) {
            throw new Exception(getValidationMessage('required', $CFG_LANG['member']['mt_sns_id']));
        }
        if (empty($_POST['mt_name'])) {
            throw new Exception(getValidationMessage('required', $CFG_LANG['member']['mt_name']));
        }
        if (empty($_POST['mt_email'])) {
            throw new Exception(getValidationMessage('required', $CFG_LANG['member']['mt_email']));
        }
        if($_POST['mt_hp_chk']!="Y"){
            throw new Exception('휴대폰 인증이 완료되지 않았습니다.');
        }

        $DB->startTransaction();

        $DB->where('mt_id', $_POST['mt_id']);
        $row = $DB->getOne('member_t', '*, idx as mt_idx');
        if ($row) {
            throw new Exception('현재 다른사람이 사용중인 아이디 입니다.');
        }

        $DB->where('mt_sns_id', $_POST['mt_sns_id']);
        $row = $DB->getOne('member_t', '*, idx as mt_idx');
        if ($row) {
            throw new Exception($CFG_LANG['oauth']['sns_already_registered']);
        }



        $current_lang = $_SESSION['user_lang'] ?? 'ko';
        $locale_row = $DB->rawQueryOne("SELECT idx FROM locale_t WHERE lang = ? AND w_show = 'Y' LIMIT 1", [$current_lang]);
        $mt_language_idx = $locale_row['idx'] ?? 2;


        $mt_type_t = '';
        if (strpos($_POST['mt_sns_id'], 'kakao') !== false) {
            $mt_type_t = '2';
        } else if (strpos($_POST['mt_sns_id'], 'naver') !== false) {
            $mt_type_t = '3';
        } else if (strpos($_POST['mt_sns_id'], 'google') !== false) {
            $mt_type_t = '4';
        } else if (strpos($_POST['mt_sns_id'], 'apple') !== false) {
            $mt_type_t = '5';
        }

        $current_lang = $_SESSION['user_lang'] ?? 'ko';
        $locale_row = $DB->rawQueryOne("SELECT idx FROM locale_t WHERE lang = ? AND w_show = 'Y' LIMIT 1", [$current_lang]);
        $mt_language_idx = $locale_row['idx'] ?? 2;


        unset($arr_query);
        $arr_query = array(
            "mt_type" => $mt_type_t,
            "mt_level" => 2,
            "mt_id" => $_POST['mt_sns_id'],
            'mt_sns_id' => $_POST['mt_sns_id'],
            "mt_name" => $_POST['mt_name'],
            "mt_nickname" => $_POST['mt_name'],
            "mt_nickname_date" => $DB->now(),
            "mt_hp" => $_POST['mt_hp'],
            "mt_email" => $_POST['mt_email'],
            "mt_birth" => $_POST['mt_birth'],
            "mt_gender" => $_POST['mt_gender'],
            "mt_zip" => $_POST['mt_zip'],
            "mt_add1" => $_POST['mt_add1'],
            "mt_add2" => $_POST['mt_add2'],
            "mt_smsing" => "Y",
            "mt_mailing" => "Y",
            "mt_pushing1" => "Y",
            "mt_status" => "Y",
            "mt_notice_push" => "Y",
            "mt_push" => "Y",
            "mt_language" => $mt_language_idx,
            'mt_wdate' => $DB->now(),
        );
        $_mt_last_idx = $DB->insert('member_t', $arr_query);

        $DB->commit();

        $query = "select * from member_t where idx = '".$_mt_last_idx."'";
        $row = $DB->rawQueryOne($query);


        $token = hash('sha256', uniqid() . $row['mt_id'] . microtime(true));
        $arr_query = array(
            "mt_app_token" => $_SESSION['_mt_app_token'],
            'mt_ldate' => date('Y-m-d H:i:s', time()),
            'mt_auto_login' => 'Y',
            'mt_auto_login_token' => $token
        );
        $DB->where('idx', $row['idx']);
        $DB->update('member_t', $arr_query);

        $expire = time() + (60 * 60 * 24 * 30); // 30일
        $host = parse_url(APP_DOMAIN, PHP_URL_HOST);
        setcookie('auto_login_token', $token, $expire, '/', $host, true, true);

        $profile = $ct_no_profile_url;
        $_SESSION['user'] = [
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
            'profile_url' => $profile,
            'mt_language' => $row['mt_language'],
        ];
        $json = [ 'success' => true, 'message' => $CFG_LANG['auth']['finish_msg1'], 'redirect' => '/auth/complete.php'];
        die(json_encode($json, JSON_UNESCAPED_UNICODE));





    } catch (Exception $e) {
        $DB->rollback();
        die(json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE));
    }
}
else if($_POST['act']=='chk_mt_id') {
    try {
        if (empty($_POST['mt_id'])) {
            throw new Exception('아이디를 입력해주세요.');
        }

        $DB->startTransaction();
        $DB->where('mt_id', $_POST['mt_id']);
        $row = $DB->getOne('member_t', '*, idx as mt_idx');
        if ($row) {
            throw new Exception('이미 존재하는 아이디 입니다.');
        }

        $json = [ 'success' => true, 'message' => '사용가능한 아이디 입니다.'];
        echo json_encode($json, JSON_UNESCAPED_UNICODE);
        exit; // 🔥 반드시 종료

    } catch (Exception $e) {
        $DB->rollback();
        die(json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE));
    }
}
else if($_POST['act']=='chk_member_email') {
    try {


        if (empty($_POST['mt_email'])) {
            throw new Exception(getValidationMessage('required', $CFG_LANG['member']['mt_email']));
        }

        $DB->startTransaction();
        $DB->where('mt_email', $_POST['mt_email']);
        $row = $DB->getOne('member_t', '*, idx as mt_idx');
        if (!$row) {
            throw new Exception($CFG_LANG['auth']['no_account']);
        }


        $from = getConfigValue('GOOGLE', 'MAIL_FROM_ADDRESS');
        $fromname = getConfigValue('GOOGLE', 'MAIL_FROM_NAME');

        $to = $_POST['mt_email'];
        $toname = uniqid('mail');

        $title = sfLang($CFG_LANG['email']['title_pwd'], ['name' => $fromname]);
        $_SESSION['_confirm_email_sms'] = mt_sms_make();
        $auth_code  = $_SESSION['_confirm_email_sms'];
        $txt1 = sfLang($CFG_LANG['email']['txt1'], ['title' => APP_TITLE]);
        $txt2 = sfLang($CFG_LANG['email']['txt2']);
        $txt3 = sfLang($CFG_LANG['email']['txt3']);
        $txt4 = sfLang($CFG_LANG['email']['txt4']);


        ob_start();
        include $_SERVER['DOCUMENT_ROOT'] . '/views/mail/email_authcode.php';
        $msg = ob_get_clean();

        $send_res = sendMailer($from, $fromname,$to,$toname,$title,$msg);
        if($send_res['status']=='ok'){
            $DB->commit();
            $json = [ 'success' => true, 'message' => $send_res['message']];
            die(json_encode($json, JSON_UNESCAPED_UNICODE));

        } else {
            throw new Exception($send_res['message']);
        }


    } catch (Exception $e) {
        $DB->rollback();
        die(json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE));
    }
}
else if($_POST['act']=="confirm_mt_email") {

    $mt_email_confirm = (int) $_POST['mt_email_confirm'];


    if($mt_email_confirm==$_SESSION['_confirm_email_sms']) {
        die(json_encode([
            'success' => true,
            'message' => $CFG_LANG['auth']['verified']
        ], JSON_UNESCAPED_UNICODE));
    } else {
        die(json_encode([
            'success' => false,
            'post'=>$_POST,
            'confirm_email_sms' => $_SESSION['_confirm_email_sms'],
            'message' => $CFG_LANG['auth']['verification_failed']
        ], JSON_UNESCAPED_UNICODE));
    }
}
else if($_POST['act']=='chk_mt_nickname') {
    try {


        if (empty($_POST['mt_nickname'])) {
            throw new Exception(getValidationMessage('required', $CFG_LANG['member']['mt_nickname']));
        }

        $DB->startTransaction();
        $DB->where('mt_nickname', $_POST['mt_nickname']);
        $row = $DB->getOne('member_t', '*, idx as mt_idx');
        if ($row) {
            throw new Exception($CFG_LANG['auth']['nickname_exists']);
        }

        $DB->commit();
        $json = [ 'success' => true, 'message' => $CFG_LANG['auth']['nickname_available']];
        die(json_encode($json, JSON_UNESCAPED_UNICODE));


    } catch (Exception $e) {
        $DB->rollback();
        die(json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE));
    }
}
else if($_POST['act']=='chk_mt_pwd_change') {
    try {
        if (empty($_POST['mt_id'])) {
            throw new Exception(getValidationMessage('required', $CFG_LANG['member']['mt_id']));
        }
        //if (empty($_POST['mt_birth'])) {
        //  throw new Exception(getValidationMessage('required', $CFG_LANG['member']['mt_birth']));
        //}
        if (empty($_POST['mt_name'])) {
            throw new Exception(getValidationMessage('required', $CFG_LANG['member']['mt_name']));
        }
        if (empty($_POST['mt_pwd'])) {
            throw new Exception(getValidationMessage('required', $CFG_LANG['member']['mt_pwd']));
        }
        if (empty($_POST['mt_pwd_re'])) {
            throw new Exception(getValidationMessage('required', $CFG_LANG['member']['mt_pwd_re']));
        }

        if($_POST['mt_hp_chk']!="Y"){
            throw new Exception('휴대폰 인증이 완료되지 않았습니다.');
        }

        if($_POST['mt_pwd']!=$_POST['mt_pwd_re']) {
            throw new Exception($CFG_LANG['auth']['password_not_match']);
        }






        $DB->startTransaction();

        $DB->where('mt_id', $_POST['mt_id']);
        $row = $DB->getOne('member_t', '*, idx as mt_id');
        if (!$row) {
            throw new Exception($CFG_LANG['auth']['no_account']);
        }


        unset($arr_query);
        $arr_query = array(
            "mt_pwd" => password_hash($_POST['mt_pwd'], PASSWORD_DEFAULT),
        );

        $DB->where('idx', $row['idx']);
        if (!$DB->update('member_t', $arr_query)) {
            throw new Exception($CFG_LANG['auth']['modify_failed']);
        }
        $DB->commit();

        $json = [ 'success' => true, 'message' => $CFG_LANG['common']['changes_saved'], 'redirect' => '/auth/pwd_success.php'];
        die(json_encode($json, JSON_UNESCAPED_UNICODE));





    } catch (Exception $e) {
        $DB->rollback();
        die(json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE));
    }

}

else if($_POST['act']=='chk_mt_hp') {
    try {

        if (empty($_POST['mt_hp'])) {
            throw new Exception(getValidationMessage('required', $CFG_LANG['member']['mt_hp']));
        }

        // 숫자만 남기기(선택)
        $mt_hp = preg_replace('/[^0-9]/', '', $_POST['mt_hp']);

        $DB->startTransaction();
//
//        // 1) 개발용 특수 번호 처리
//        if ($mt_hp == '01020170700' || substr($mt_hp, 0, 3) == '090') {
//
//            // 개발용 고정 코드
//            $_SESSION['_confirm_sms'] = '111111';
//            $_SESSION['_confirm_hp']  = $mt_hp;                  // 🔥 휴대폰 번호도 세션에 저장
//
//            $json = [
//                'success'    => true,
//                'message'    => '개발번호 (인증번호: 111111)',
//                'auth_code'  => $_SESSION['_confirm_sms'],       // 개발용 코드
//            ];
//
//            echo json_encode($json, JSON_UNESCAPED_UNICODE);
//            exit;
//        }

        // 2) 휴대폰 중복 가입 여부 체크
        $DB->where('mt_hp', $mt_hp);
        $DB->where('mt_level', 2);
        $row = $DB->getOne('member_t', '*, idx as mt_idx');

        if ($row) {
            throw new Exception("동일한 휴대폰 번호로 가입이 되어 있습니다.");
        }

        // 3) 인증번호 생성 + 세션 저장
        $auth_code = mt_sms_make();   // 예: 6자리 랜덤 숫자
        $message = "[맛집바로]\n회원가입 인증 번호 : ".$auth_code;
        $result = bizppurio_send_sms($mt_hp, $message);

        $_SESSION['_confirm_sms'] = $auth_code;
        $_SESSION['_confirm_hp']  = $mt_hp;                      // 🔥 여기서 번호 저장

        // 4) 개발용: 인증번호를 그대로 응답
        $json = [
            'success'    => true,
            'message'    => '인증번호가 발송되었습니다.',
            'auth_code'  => $auth_code,                          // 🔥 개발용
        ];

        echo json_encode($json, JSON_UNESCAPED_UNICODE);
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
else if($_POST['act']=='chk_non_mt_hp') {
    try {


        if (empty($_POST['mt_hp'])) {
            throw new Exception(getValidationMessage('required', $CFG_LANG['member']['mt_hp']));
        }

        $DB->startTransaction();


        $mt_hp = $_POST['mt_hp'];
        $DB->where('mt_hp', $mt_hp);
        $row = $DB->getOne('member_t', '*, idx as mt_idx');
        if (!$row) {
            throw new Exception("휴대폰 번호로 가입된 계정이 없습니다.");
        }

        if ($_POST['mt_hp'] == '01020170700' || substr($_POST['mt_hp'], 0, 3) == '090') {

            $_SESSION['_confirm_sms'] = '111111';
            $json = [ 'success' => true, 'message' => "개발번호"];
            die(json_encode($json, JSON_UNESCAPED_UNICODE));

        } else {


            $_SESSION['_confirm_sms'] = mt_sms_make();
            $msg = "[" . APP_TITLE . "]본인확인 인증번호[" . $_SESSION['_confirm_sms'] . "]을 화면에 입력해주세요.";



            $rtn = f_aligo_sms_send($_POST['mt_hp'], $msg);
            $response = json_decode($rtn, true);
            if ($response['result_code'] < 0) {
                die(json_encode([
                    'success' => false,
                    'message' => $response['result_code'].':'.$response['message']. ' 관리자에게 문의해주세요.',
                ], JSON_UNESCAPED_UNICODE));
            } else {
                $json = ['success' => true];
                die(json_encode($json, JSON_UNESCAPED_UNICODE));
            }

        }

    } catch (Exception $e) {
        $DB->rollback();
        die(json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE));
    }
}
else if($_POST['act']=="confirm_mt_hp") {
    try {
        // 1) 입력값 체크
        if (empty($_POST['mt_hp'])) {
            throw new Exception('휴대폰 번호를 입력해 주세요.');
        }
        if (empty($_POST['mt_hp_confirm'])) {
            throw new Exception('인증번호를 입력해 주세요.');
        }

        // 숫자만 남기기(선택)
        $mt_hp = preg_replace('/[^0-9]/', '', $_POST['mt_hp']);
        $inputCode = trim($_POST['mt_hp_confirm']);

        // 2) 세션에 저장된 값 존재 여부 체크
        if (!isset($_SESSION['_confirm_sms']) || $_SESSION['_confirm_sms'] === '') {
            throw new Exception('인증요청 정보가 없습니다. 다시 인증요청을 진행해 주세요.');
        }

        if (!isset($_SESSION['_confirm_hp']) || $_SESSION['_confirm_hp'] === '') {
            throw new Exception('휴대폰 인증요청 정보가 없습니다. 다시 인증요청을 진행해 주세요.');
        }

        $sessionCode = trim($_SESSION['_confirm_sms']);
        $sessionHp   = preg_replace('/[^0-9]/', '', $_SESSION['_confirm_hp']);

        // 3) 휴대폰번호 + 인증번호 모두 일치해야 성공
        if ($mt_hp !== $sessionHp) {
            throw new Exception('인증요청한 휴대폰 번호와 일치하지 않습니다. 다시 인증요청을 해주세요.');
        }

        if ($inputCode !== $sessionCode) {
            throw new Exception('인증번호가 올바르지 않습니다.');
        }

        // 4) 성공 처리: 한 번 사용 후 세션 제거
        unset($_SESSION['_confirm_sms'], $_SESSION['_confirm_hp']);

        $json = [
            'success'    => true,
            'message'    => '인증이 확인되었습니다.',
        ];

        echo json_encode($json, JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {

        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}
else if($_POST['act']=="mt_id_chk") {

    try {

        if (empty($_POST['mt_id'])) {
            throw new Exception(getValidationMessage('required', $CFG_LANG['member']['mt_id']));
        }

        $arr_chk_id_restricted = explode(",", $setup_info['st_id_filter']);
        $lowercaseId = strtolower($_POST['mt_id']);
        $isCheckAccount = false;
        foreach ($arr_chk_id_restricted as $word) {
            if (strpos($lowercaseId, $word) !== false) {
                $isCheckAccount = true;
                break;
            }
        }

        if($isCheckAccount) {
            throw new Exception($CFG_LANG['auth']['id_restrict']);
        }


        $DB->where('mt_id', $_POST['mt_id']);
        $row = $DB->getOne('member_t', '*, idx as mt_idx');
        if ($row) {
            throw new Exception($CFG_LANG['auth']['id_exists']);
        } else {
            $json = [ 'success' => true, 'message' => $CFG_LANG['auth']['id_available']];
            die(json_encode($json, JSON_UNESCAPED_UNICODE));
        }


    } catch (Exception $e) {
        $DB->rollback();
        die(json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE));
    }







}
else if($_POST['act']=="find_mt_id") {

    try {

        //if (empty($_POST['mt_birth'])) {
        //  throw new Exception(getValidationMessage('required', $CFG_LANG['member']['mt_birth']));
        //}
        if (empty($_POST['mt_name'])) {
            throw new Exception(getValidationMessage('required', $CFG_LANG['member']['mt_name']));
        }

        if($_POST['mt_hp_chk']!="Y"){
            throw new Exception('휴대폰 인증이 완료되지 않았습니다.');
        }


        $DB->where('mt_hp', $_POST['mt_hp']);
        //$DB->where('mt_birth', $_POST['mt_birth']);
        $DB->where('mt_name', $_POST['mt_name']);
        $DB->where('mt_level >= 2');
        $row = $DB->getOne('member_t', '*, idx as mt_idx');
        if ($row) {
            $json = [ 'success' => true, 'message' => '아이디를 찾았습니다.', 'redirect' => '/auth/id_success.php?id='.$row['mt_id']];
            die(json_encode($json, JSON_UNESCAPED_UNICODE));

        } else {
            throw new Exception($CFG_LANG['auth']['no_account']);
        }


    } catch (Exception $e) {
        $DB->rollback();
        die(json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE));
    }
}

?>
