<?php
// join_update.php : 가맹점주 회원가입 및 보조 액션 처리 (JSON)

include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";

header('Content-Type: application/json; charset=utf-8');

$act = $_POST['act'] ?? '';

// ============================
// 공통 이미지 업로드 설정 (수정 API와 동일)
// ============================
$imgExtAllow = ['jpg','jpeg','png','gif','webp'];
$bizExtAllow = array_merge($imgExtAllow, ['pdf']); // 사업자등록증은 PDF 허용

/**
 * 매장 이미지 / 통장 사본 처리 (썸네일 rs_ 생성)
 */
function handleStoreImage($fileArr, $uploadDir, $imgExtAllow) {
    if (!isset($fileArr['error']) || $fileArr['error'] !== UPLOAD_ERR_OK || empty($fileArr['name'])) {
        return '';
    }

    $ext = strtolower(pathinfo($fileArr['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $imgExtAllow, true)) {
        return '';
    }

    $newName  = date('YmdHis') . '_' . mt_rand(1000, 9999) . '.' . $ext;
    $origPath = $uploadDir . $newName;
    $rsPath   = $uploadDir . 'rs_' . $newName;

    if (move_uploaded_file($fileArr['tmp_name'], $origPath)) {
        @chmod($origPath, 0644);

        // 프로젝트에 이미 존재하는 resize_crop_image 함수 사용 (없으면 GD로 대체 가능)
        if (function_exists('resize_crop_image')) {
            resize_crop_image(800, 800, $origPath, $rsPath, 85);
        }

        return $newName;
    }

    return '';
}

/**
 * 사업자등록증 처리 (이미지 or PDF, 썸네일 없음)
 */
function handleBizFile($fileArr, $uploadDir, $bizExtAllow) {
    if (!isset($fileArr['error']) || $fileArr['error'] !== UPLOAD_ERR_OK || empty($fileArr['name'])) {
        return '';
    }

    $ext = strtolower(pathinfo($fileArr['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $bizExtAllow, true)) {
        return '';
    }

    $newName  = 'biz_' . date('YmdHis') . '_' . mt_rand(1000, 9999) . '.' . $ext;
    $origPath = $uploadDir . $newName;

    if (move_uploaded_file($fileArr['tmp_name'], $origPath)) {
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
            echo json_encode([
                'success' => false,
                'message' => '아이디를 입력해 주세요.'
            ], JSON_UNESCAPED_UNICODE);
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
        echo json_encode([
            'success' => false,
            'message' => '서버 오류가 발생했습니다.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// ============================
// ② 휴대폰 인증번호 발송 (더미 또는 실제 SMS 연동)
// ============================
if ($act === 'send_hp_code') {
    try {
        $mb_hp = preg_replace('/\D+/', '', $_POST['mb_hp'] ?? '');

        if ($mb_hp === '') {
            echo json_encode([
                'success' => false,
                'message' => '휴대폰 번호를 입력해 주세요.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // TODO: 실제 SMS 연동 시 여기서 발송
        $code = (string)random_int(100000, 999999);

        $_SESSION['join_hp']         = $mb_hp;
        $_SESSION['join_hp_code']    = $code;
        $_SESSION['join_hp_expire']  = time() + 180; // 3분

        echo json_encode([
            'success' => true,
            'message' => '인증번호가 발송되었습니다. (개발용 코드: ' . $code . ')'
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        error_log("send_hp_code Error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => '서버 오류가 발생했습니다.'
        ], JSON_UNESCAPED_UNICODE);
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
            echo json_encode([
                'success' => false,
                'message' => '휴대폰 번호와 인증번호를 입력해 주세요.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        if (!isset($_SESSION['join_hp_code'], $_SESSION['join_hp_expire'], $_SESSION['join_hp'])) {
            echo json_encode([
                'success' => false,
                'message' => '인증 요청 이력이 없습니다.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        if (time() > (int)$_SESSION['join_hp_expire']) {
            echo json_encode([
                'success' => false,
                'message' => '인증 시간이 만료되었습니다.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        if ($_SESSION['join_hp'] !== $mb_hp) {
            echo json_encode([
                'success' => false,
                'message' => '인증 요청한 번호와 다릅니다.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        if ($_SESSION['join_hp_code'] !== $code) {
            echo json_encode([
                'success' => false,
                'message' => '인증번호가 일치하지 않습니다.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // 인증 성공 시 세션 정리 (필요시 유지 가능)
        unset($_SESSION['join_hp_code'], $_SESSION['join_hp_expire'], $_SESSION['join_hp']);

        echo json_encode([
            'success' => true,
            'message' => '휴대폰 인증이 완료되었습니다.'
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        error_log("verify_hp_code Error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => '서버 오류가 발생했습니다.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// ============================
// ④ 실제 회원가입 처리
// ============================
if ($act === 'register') {
    try {
        $DB->startTransaction();

        // 기본 정보
        $mb_id      = trim($_POST['mb_id'] ?? '');
        $mb_pw      = $_POST['mb_pw'] ?? '';
        $mb_pw_re   = $_POST['mb_pw_re'] ?? '';
        $mb_name    = trim($_POST['mb_name'] ?? '');
        $mb_hp      = preg_replace('/\D+/', '', $_POST['mb_hp'] ?? '');

        // 약관
        $agree_terms    = ($_POST['agree_terms'] ?? 'N') === 'Y';
        $agree_privacy  = ($_POST['agree_privacy'] ?? 'N') === 'Y';

        // 매장 정보
        $store_name = trim($_POST['store_name'] ?? '');
        $biz_no     = trim($_POST['biz_no'] ?? '');
        $owner_name = trim($_POST['owner_name'] ?? '');
        $owner_hp   = preg_replace('/\D+/', '', $_POST['owner_hp'] ?? '');
        $shop_name  = trim($_POST['shop_name'] ?? '');

        // 정산 정보
        $bank       = trim($_POST['settle_bank'] ?? '');
        $holder     = trim($_POST['settle_holder'] ?? '');
        $account    = trim($_POST['settle_account'] ?? '');

        // 필수 체크
        if ($mb_id === '' || $mb_pw === '' || $mb_name === '' || $mb_hp === '' ||
            $store_name === '' || $biz_no === '' || $owner_name === '' || $owner_hp === '' || $shop_name === '' ||
            $bank === '' || $holder === '' || $account === '') {
            throw new Exception('모든 필수 항목을 입력해 주세요.');
        }

        if ($mb_pw !== $mb_pw_re) {
            throw new Exception('비밀번호와 비밀번호 확인이 일치하지 않습니다.');
        }

        if (!$agree_terms || !$agree_privacy) {
            throw new Exception('필수 약관에 모두 동의해야 합니다.');
        }

        // 아이디 중복 체크
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
            'mt_level'    => 5,          // 가맹점주 레벨
            'mt_appr'     => 'N',        // 승인 대기
            'mt_auth'     => 'Y',
            'mt_status'   => 'Y',
            'mt_wdate'    => $DB->now(),
        ];

        $mb_idx = $DB->insert('member_t', $memberData);
        if (!$mb_idx) {
            throw new Exception('회원 등록에 실패했습니다.');
        }

        // 매장 등록
        $shopData = [
            'mb_idx'          => $mb_idx,
            'sh_corp_nm'      => $store_name,
            'sh_biz_no'       => $biz_no,
            'sh_ceo_nm'       => $owner_name,
            'sh_title'        => $shop_name,
            'sh_bank'         => $bank,
            'sh_bank_holder'  => $holder,
            'sh_bank_account' => $account,
        ];

        $sh_idx = $DB->insert('shop_t', $shopData);
        if (!$sh_idx) {
            throw new Exception('매장 등록에 실패했습니다.');
        }

        // 업로드 폴더 생성
        $shopUploadDir = $_SERVER['DOCUMENT_ROOT'] . "/data/shop/" . $sh_idx . "/";
        if (!is_dir($shopUploadDir)) {
            @mkdir($shopUploadDir, 0777, true);
        }

        // 파일 처리 및 shop_t 업데이트
        $updateFiles = [];

        if (!empty($_FILES['store_img1']['name'])) {
            $name = handleStoreImage($_FILES['store_img1'], $shopUploadDir, $imgExtAllow);
            if ($name) $updateFiles['sh_img1'] = $name;
        }
        if (!empty($_FILES['store_img2']['name'])) {
            $name = handleStoreImage($_FILES['store_img2'], $shopUploadDir, $imgExtAllow);
            if ($name) $updateFiles['sh_img2'] = $name;
        }
        if (!empty($_FILES['store_img3']['name'])) {
            $name = handleStoreImage($_FILES['store_img3'], $shopUploadDir, $imgExtAllow);
            if ($name) $updateFiles['sh_img3'] = $name;
        }
        if (!empty($_FILES['biz_file']['name'])) {
            $name = handleBizFile($_FILES['biz_file'], $shopUploadDir, $bizExtAllow);
            if ($name) $updateFiles['sh_biz_file'] = $name;
        }
        if (!empty($_FILES['store_bankbook']['name'])) {
            $name = handleStoreImage($_FILES['store_bankbook'], $shopUploadDir, $imgExtAllow);
            if ($name) $updateFiles['sh_bankbook'] = $name;
        }

        if (!empty($updateFiles)) {
            $DB->where('idx', $sh_idx);
            if (!$DB->update('shop_t', $updateFiles)) {
                throw new Exception('파일 정보 저장에 실패했습니다.');
            }
        }

        $DB->commit();

        echo json_encode([
            'success' => true,
            'message' => '회원가입이 완료되었습니다. 관리자 승인 후 이용 가능합니다.'
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        if ($DB->inTransaction()) {
            $DB->rollback();
        }
        error_log("register Error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage() ?: '회원가입 처리 중 오류가 발생했습니다.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// ============================
// 그 외 잘못된 act
// ============================
echo json_encode([
    'success' => false,
    'message' => '잘못된 요청입니다.'
], JSON_UNESCAPED_UNICODE);
exit;
