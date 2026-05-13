<?php
// join_update.php : 가맹점주 회원가입 및 보조 액션 처리 (JSON)

include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/cfg/bizppurio.lib.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";

header('Content-Type: application/json; charset=utf-8');

$act = $_POST['act'] ?? '';

// ============================
// 공통 설정
// ============================
$imgExtAllow = ['jpg','jpeg','png','gif','webp'];
$bizExtAllow = array_merge($imgExtAllow, ['pdf']);

/**
 * 이미지 처리 (썸네일 rs_ 생성)
 */
function handleStoreImage($fileData, $uploadDir, $imgExtAllow) {
    if (empty($fileData['name']) || $fileData['error'] !== UPLOAD_ERR_OK) {
        return '';
    }

    $ext = strtolower(pathinfo($fileData['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $imgExtAllow, true)) {
        return '';
    }

    $newName  = date('YmdHis') . '_' . mt_rand(1000, 9999) . '.' . $ext;
    $origPath = $uploadDir . $newName;
    $rsPath   = $uploadDir . 'rs_' . $newName;

    if (move_uploaded_file($fileData['tmp_name'], $origPath)) {
        @chmod($origPath, 0644);
        if (function_exists('resize_crop_image')) {
            resize_crop_image(800, 800, $origPath, $rsPath, 85);
        }
        return $newName;
    }
    return '';
}

/**
 * 사업자등록증 처리 (PDF 포함, 썸네일 없음)
 */
function handleBizFile($fileData, $uploadDir, $bizExtAllow) {
    if (empty($fileData['name']) || $fileData['error'] !== UPLOAD_ERR_OK) {
        return '';
    }

    $ext = strtolower(pathinfo($fileData['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $bizExtAllow, true)) {
        return '';
    }

    $newName  = 'biz_' . date('YmdHis') . '_' . mt_rand(1000, 9999) . '.' . $ext;
    $origPath = $uploadDir . $newName;

    if (move_uploaded_file($fileData['tmp_name'], $origPath)) {
        @chmod($origPath, 0644);
        return $newName;
    }
    return '';
}

// ============================
// ① 아이디 중복 확인
// ============================
if ($act === 'check_id') {
    try {
        $mb_id = trim($_POST['mb_id'] ?? '');
        if ($mb_id === '') {
            echo json_encode(['success' => false, 'message' => '아이디를 입력해 주세요.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $DB->where('mt_id', $mb_id);
        $exists = $DB->getOne('member_t', 'idx');

        echo json_encode([
            'success' => !$exists,
            'message' => $exists ? '이미 사용중인 아이디입니다.' : '사용 가능한 아이디입니다.'
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        error_log("check_id Error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => '서버 오류가 발생했습니다.'], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// ============================
// ② 휴대폰 인증번호 발송
// ============================
if ($act === 'send_hp_code') {
    try {
        $mb_hp = preg_replace('/\D+/', '', $_POST['mb_hp'] ?? '');
        if ($mb_hp === '') {
            echo json_encode(['success' => false, 'message' => '휴대폰 번호를 입력해 주세요.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // ✅ 회원가입용: 이미 가입된 휴대폰 번호인지 체크
        $DB->where('mt_hp', $mb_hp);
        $DB->where('mt_level', 5);
        $existsHp = $DB->getOne('member_t', 'idx');
        if ($existsHp) {
            echo json_encode([
                'success' => false,
                'message' => '이미 가입된 휴대폰 번호입니다.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $code = (string)random_int(100000, 999999);
        $message = "[맛집바로]\n회원가입 인증 번호 : ".$code;
        $result = bizppurio_send_sms($mb_hp, $message);

        $_SESSION['join_hp']        = $mb_hp;
        $_SESSION['join_hp_code']   = $code;
        $_SESSION['join_hp_expire'] = time() + 180;

        // ✅ 인증 완료 플래그는 초기화
        unset($_SESSION['join_hp_verified'], $_SESSION['join_hp_verified_hp'], $_SESSION['join_hp_verified_expire']);

        echo json_encode([
            'success' => true,
            'message' => '인증번호가 발송되었습니다. (개발용 코드: ' . $code . ')',
            'code'    => $code,
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        error_log("send_hp_code Error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => '서버 오류가 발생했습니다.'], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// ============================
// ③ 휴대폰 인증번호 확인
// ============================
if ($act === 'verify_hp_code') {
    try {
        $mb_hp = preg_replace('/\D+/', '', $_POST['mb_hp'] ?? '');
        $code  = trim($_POST['hp_code'] ?? '');

        if ($mb_hp === '' || $code === '') {
            echo json_encode(['success' => false, 'message' => '정보를 입력해 주세요.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        if (!isset($_SESSION['join_hp_code'], $_SESSION['join_hp_expire'], $_SESSION['join_hp'])) {
            echo json_encode(['success' => false, 'message' => '인증 요청 이력이 없습니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        if (time() > (int)$_SESSION['join_hp_expire']) {
            echo json_encode(['success' => false, 'message' => '인증 시간이 만료되었습니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        if ($_SESSION['join_hp'] !== $mb_hp || $_SESSION['join_hp_code'] !== $code) {
            echo json_encode(['success' => false, 'message' => '인증번호가 올바르지 않습니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // ✅ (선택) 검증 시점에도 중복 체크 한번 더 (경쟁상황 방지)
        $DB->where('mt_hp', $mb_hp);
        $DB->where('mt_level',5);
        if ($DB->getOne('member_t', 'idx')) {
            echo json_encode(['success' => false, 'message' => '이미 가입된 휴대폰 번호입니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // ✅ 인증 완료 플래그 저장 (register에서 검증용)
        $_SESSION['join_hp_verified']        = 'Y';
        $_SESSION['join_hp_verified_hp']     = $mb_hp;
        $_SESSION['join_hp_verified_expire'] = time() + 600; // 10분 유효

        // ✅ 코드 관련 세션은 제거
        unset($_SESSION['join_hp_code'], $_SESSION['join_hp_expire'], $_SESSION['join_hp']);

        echo json_encode(['success' => true, 'message' => '휴대폰 인증이 완료되었습니다.'], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        error_log("verify_hp_code Error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => '서버 오류가 발생했습니다.'], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// ============================
// ④ 회원가입 실제 처리
// ============================
if ($act === 'register') {
    try {
        $DB->startTransaction();

        // 기본 정보
        $mb_id     = trim($_POST['mb_id'] ?? '');
        $mb_pw     = $_POST['mb_pw'] ?? '';
        $mb_pw_re  = $_POST['mb_pw_re'] ?? '';
        $mb_name   = trim($_POST['mb_name'] ?? '');
        $mb_hp     = preg_replace('/\D+/', '', $_POST['mb_hp'] ?? '');

        // 약관
        $agree_terms    = ($_POST['agree_terms'] ?? 'N') === 'Y';
        $agree_privacy  = ($_POST['agree_privacy'] ?? 'N') === 'Y';

        // 매장 정보 (인덱스 0만 사용)
        $store_name = trim($_POST['store_name'][0] ?? '');
        $biz_no     = trim($_POST['biz_no'][0] ?? '');
        $owner_name = trim($_POST['owner_name'][0] ?? '');
        $shop_name  = trim($_POST['shop_name'][0] ?? '');
//        $branch_name= trim($_POST['branch_name'][0] ?? '');
        $shop_tel   = trim($_POST['shop_tel'][0] ?? '');
        $zip        = trim($_POST['zip'][0] ?? '');
        $addr1      = trim($_POST['addr1'][0] ?? '');
        $addr2      = trim($_POST['addr2'][0] ?? '');
        $lat        = trim($_POST['lat'][0] ?? '');
        $lng        = trim($_POST['lng'][0] ?? '');

        // 정산 정보
        $bank       = trim($_POST['settle_bank'][0] ?? '');
        $holder     = trim($_POST['settle_holder'][0] ?? '');
        $account    = trim($_POST['settle_account'][0] ?? '');

        // 필수 검증
        if ($mb_id === '' || $mb_pw === '' || $mb_name === '' || $mb_hp === '' ||
            $store_name === '' || $biz_no === '' || $owner_name === '' || $shop_name === '' ||
            $bank === '' || $holder === '' || $account === '') {
            throw new Exception('모든 필수 항목을 입력해 주세요.');
        }

        if (!isset($_SESSION['join_hp_verified'], $_SESSION['join_hp_verified_hp'], $_SESSION['join_hp_verified_expire'])
            || $_SESSION['join_hp_verified'] !== 'Y'
            || $_SESSION['join_hp_verified_hp'] !== $mb_hp
            || time() > (int)$_SESSION['join_hp_verified_expire']) {
            throw new Exception('휴대폰 인증을 완료해 주세요.');
        }

        if ($mb_pw !== $mb_pw_re) {
            throw new Exception('비밀번호가 일치하지 않습니다.');
        }

        if (!$agree_terms || !$agree_privacy) {
            throw new Exception('필수 약관에 동의해야 합니다.');
        }

        // 아이디 중복
        $DB->where('mt_id', $mb_id);
        if ($DB->getOne('member_t', 'idx')) {
            throw new Exception('이미 사용 중인 아이디입니다.');
        }

        // 회원 등록
        $memberData = [
            'mt_type'     => 1,
            'mt_id'       => $mb_id,
            'mt_pwd'      => password_hash($mb_pw, PASSWORD_DEFAULT),
            'mt_name'     => $mb_name,
            'mt_nickname' => $mb_name,
            'mt_hp'       => $mb_hp,
            'mt_level'    => 5,
            'mt_appr'     => 'N',
            'mt_auth'     => 'Y',
            'mt_status'   => 'Y',
            'mt_wdate'    => $DB->now(),
        ];

        $mb_idx = $DB->insert('member_t', $memberData);
        if (!$mb_idx) throw new Exception('회원 등록 실패');

        // 매장 등록
        $shopData = [
            'mb_idx'          => $mb_idx,
            'sh_corp_nm'      => $store_name,
            'sh_biz_no'       => $biz_no,
            'sh_ceo_nm'       => $owner_name,
            'sh_title'        => $shop_name,
//            'sh_branch_nm'    => $branch_name,
            'sh_tel'          => $shop_tel,
            'sh_zip'          => $zip,
            'sh_addr1'        => $addr1,
            'sh_addr2'        => $addr2,
            'sh_lat'          => $lat,
            'sh_lng'          => $lng,
            'sh_bank'         => $bank,
            'sh_bank_holder'  => $holder,
            'sh_bank_account' => $account,
            'sh_lat_num' => $lat !== '' ? (float)$lat : null,
            'sh_lng_num' => $lng !== '' ? (float)$lng : null,
        ];

        $sh_idx = $DB->insert('shop_t', $shopData);
        if (!$sh_idx) throw new Exception('매장 등록 실패');

        // 업로드 폴더
        $shopUploadDir = $_SERVER['DOCUMENT_ROOT'] . "/data/shop/" . $sh_idx . "/";
        if (!is_dir($shopUploadDir)) {
            @mkdir($shopUploadDir, 0777, true);
        }

        $updateFiles = [];

        // 매장 이미지 3장 (배열 [] 대응)
//        foreach (['store_img1' => 'sh_img1', 'store_img2' => 'sh_img2', 'store_img3' => 'sh_img3'] as $field => $dbCol) {
//            if (isset($_FILES[$field]) && is_array($_FILES[$field]['error'])) {
//                $file = [
//                    'name'     => $_FILES[$field]['name'][0] ?? '',
//                    'tmp_name' => $_FILES[$field]['tmp_name'][0] ?? '',
//                    'error'    => $_FILES[$field]['error'][0] ?? UPLOAD_ERR_NO_FILE,
//                ];
//                $name = handleStoreImage($file, $shopUploadDir, $imgExtAllow);
//                if ($name) $updateFiles[$dbCol] = $name;
//            }
//        }

        // 통장 사본 (배열 [] 대응)
        if (isset($_FILES['store_bankbook']) && is_array($_FILES['store_bankbook']['error'])) {
            $file = [
                'name'     => $_FILES['store_bankbook']['name'][0] ?? '',
                'tmp_name' => $_FILES['store_bankbook']['tmp_name'][0] ?? '',
                'error'    => $_FILES['store_bankbook']['error'][0] ?? UPLOAD_ERR_NO_FILE,
            ];
            $name = handleStoreImage($file, $shopUploadDir, $imgExtAllow);
            if ($name) $updateFiles['sh_bankbook'] = $name;
        }

        // 사업자등록증 (단일)
        if (isset($_FILES['biz_file']) && $_FILES['biz_file']['error'] === UPLOAD_ERR_OK) {
            $name = handleBizFile($_FILES['biz_file'], $shopUploadDir, $bizExtAllow);
            if ($name) $updateFiles['sh_biz_file'] = $name;
        }

        if (!empty($updateFiles)) {
            $DB->where('idx', $sh_idx);
            if (!$DB->update('shop_t', $updateFiles)) {
                throw new Exception('파일 정보 저장 실패');
            }
        }

        unset($_SESSION['join_hp_verified'], $_SESSION['join_hp_verified_hp'], $_SESSION['join_hp_verified_expire']);

        $DB->commit();

        echo json_encode([
            'success' => true,
            'message' => '회원가입이 완료되었습니다. 관리자 승인 후 이용 가능합니다.'
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        if ($DB->inTransaction()) $DB->rollback();
        error_log("register Error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage() ?: '처리 중 오류가 발생했습니다.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// ============================
// 잘못된 act
// ============================
echo json_encode(['success' => false, 'message' => '잘못된 요청입니다.'], JSON_UNESCAPED_UNICODE);
exit;
