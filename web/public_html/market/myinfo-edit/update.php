<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/cfg/bizppurio.lib.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";

header('Content-Type: application/json; charset=utf-8');

$mt_idx = (int)($_SESSION['mng']['mt_idx'] ?? 0);

if ($mt_idx <= 0) {
    echo json_encode(['success'=>false, 'message'=>'로그인 정보가 없습니다.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$act = $_POST['act'] ?? '';

// 1. 회원정보 불러오기
if ($act === 'get_user_info') {
    try {
        $DB->where('idx', $mt_idx);
        $user = $DB->getOne('member_t', 'mt_id, mt_name, mt_hp');

        if (!$user) throw new Exception('회원 정보를 찾을 수 없습니다.');

        $DB->where('mb_idx', $mt_idx);
        $DB->orderBy('idx', 'ASC');
        $shop = $DB->getOne('shop_t', 'sh_bank, sh_bank_holder, sh_bank_account, sh_bankbook, idx as sh_idx');

        echo json_encode([
            'success' => true,
            'data' => [
                'mt_id'           => $user['mt_id'] ?? '',
                'mt_name'         => $user['mt_name'] ?? '',
                'mt_hp'           => $user['mt_hp'] ?? '',
                'sh_bank'         => $shop['sh_bank'] ?? '',
                'sh_bank_holder'  => $shop['sh_bank_holder'] ?? '',
                'sh_bank_account' => $shop['sh_bank_account'] ?? '',
                'sh_bankbook'     => $shop['sh_bankbook'] ?? '',
                'sh_idx'          => $shop['sh_idx'] ?? 0
            ]
        ], JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        echo json_encode(['success'=>false, 'message'=>$e->getMessage()], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

// 2. 인증번호 발송 (임시)
if ($act === 'send_hp_code') {
    try {
        $hp = preg_replace('/\D+/', '', $_POST['hp'] ?? '');
        if (strlen($hp) < 10 || strlen($hp) > 11) {
            throw new Exception('올바른 휴대폰번호(10~11자리 숫자)를 입력해주세요.');
        }

        // 임시 인증번호
        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $message = "[맛집보고]\n휴대폰 인증 번호 : ".$code;
        $result = bizppurio_send_sms($hp, $message);

        $_SESSION['hp_verify_code']   = $code;
        $_SESSION['hp_verify_hp']     = $hp;
        $_SESSION['hp_verify_expire'] = time() + 180;

        echo json_encode([
            'success' => true,
            'message' => '인증번호가 발송되었습니다',
        ], JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        echo json_encode(['success'=>false, 'message'=>$e->getMessage()], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

// 3. 인증번호 확인
if ($act === 'verify_hp_code') {
    try {
        $code = trim($_POST['hp_code'] ?? '');
        if ($code === '') throw new Exception('인증번호를 입력해주세요.');

        if (!isset($_SESSION['hp_verify_code'], $_SESSION['hp_verify_expire'], $_SESSION['hp_verify_hp'])) {
            throw new Exception('인증 요청 이력이 없습니다.');
        }

        if (time() > $_SESSION['hp_verify_expire']) {
            unset($_SESSION['hp_verify_code'], $_SESSION['hp_verify_expire'], $_SESSION['hp_verify_hp']);
            throw new Exception('인증 시간이 만료되었습니다. 다시 요청해주세요.');
        }

        if ($_SESSION['hp_verify_code'] !== $code) {
            throw new Exception('인증번호가 일치하지 않습니다.');
        }

        $_SESSION['hp_verified'] = 'Y';
        $_SESSION['hp_verified_hp'] = $_SESSION['hp_verify_hp'];
        unset($_SESSION['hp_verify_code'], $_SESSION['hp_verify_expire']);

        echo json_encode(['success'=>true, 'message'=>'인증이 완료되었습니다.'], JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        echo json_encode(['success'=>false, 'message'=>$e->getMessage()], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

// 4. 회원정보 수정 처리
if ($act === 'update_user_info') {
    try {
        $DB->startTransaction();

        $mt_pwd          = trim($_POST['mt_pwd'] ?? '');
        $mt_pwd_re       = trim($_POST['mt_pwd_re'] ?? '');
        $mt_name         = trim($_POST['mt_name'] ?? '');
        $mt_hp           = preg_replace('/\D+/', '', $_POST['mt_hp'] ?? '');
        $sh_bank         = trim($_POST['sh_bank'] ?? '');
        $sh_bank_holder  = trim($_POST['sh_bank_holder'] ?? '');
        $sh_bank_account = trim($_POST['sh_bank_account'] ?? '');

        // 휴대폰 변경 시 인증 필수 확인
        $DB->where('idx', $mt_idx);
        $original = $DB->getOne('member_t', 'mt_hp');

//        if ($mt_hp !== $original['mt_hp']) {
//            if (!isset($_SESSION['hp_verified']) || $_SESSION['hp_verified'] !== 'Y' || $_SESSION['hp_verified_hp'] !== $mt_hp) {
//                throw new Exception('휴대폰번호 변경 시 인증을 완료해주세요.');
//            }
//        }

        // 유효성 검사
        if ($mt_name === '') throw new Exception('이름을 입력해주세요.');
        if ($mt_hp === '') throw new Exception('휴대폰번호를 입력해주세요.');
        if ($sh_bank === '' || $sh_bank_holder === '' || $sh_bank_account === '') {
            throw new Exception('정산 정보를 모두 입력해주세요.');
        }

        // 비밀번호 변경
        $update_member = [
            'mt_name'  => $mt_name,
            'mt_hp'    => $mt_hp,
            'mt_udate' => $DB->now()
        ];

        if ($mt_pwd !== '') {
            if ($mt_pwd !== $mt_pwd_re) throw new Exception('비밀번호가 일치하지 않습니다.');
            $update_member['mt_pwd'] = password_hash($mt_pwd, PASSWORD_DEFAULT);
        }

        $DB->where('idx', $mt_idx);
        if (!$DB->update('member_t', $update_member)) {
            throw new Exception('회원 정보 업데이트 실패');
        }

        // 정산 정보 업데이트 (가장 최근 매장 기준)
        $DB->where('mb_idx', $mt_idx);
        $DB->orderBy('idx', 'ASC');
        $shop = $DB->getOne('shop_t', 'idx');

        if ($shop) {
            $update_shop = [
                'sh_bank'         => $sh_bank,
                'sh_bank_holder'  => $sh_bank_holder,
                'sh_bank_account' => $sh_bank_account,
                'sh_udate'        => $DB->now()
            ];

            // 통장사본 파일 처리
            if (!empty($_FILES['bankbook_file']) && $_FILES['bankbook_file']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['bankbook_file'];
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg','jpeg','png','gif'])) {
                    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . "/data/shop/{$shop['idx']}/";
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

                    $newName = 'bankbook_' . date('YmdHis') . '_' . mt_rand(1000,9999) . '.' . $ext;
                    $dest = $uploadDir . $newName;

                    if (move_uploaded_file($file['tmp_name'], $dest)) {
                        $update_shop['sh_bankbook'] = $newName;
                    } else {
                        throw new Exception('통장사본 파일 저장 실패');
                    }
                } else {
                    throw new Exception('통장사본은 jpg, png, gif 파일만 가능합니다.');
                }
            }

            $DB->where('idx', $shop['idx']);
            if (!$DB->update('shop_t', $update_shop)) {
                throw new Exception('정산 정보 업데이트 실패');
            }
        }

        // 인증 세션 정리
        unset($_SESSION['hp_verified'], $_SESSION['hp_verified_hp']);

        $DB->commit();

        echo json_encode(['success'=>true, 'message'=>'정보가 성공적으로 수정되었습니다.'], JSON_UNESCAPED_UNICODE);

    } catch (Exception $e) {
        $DB->rollback();
        echo json_encode(['success'=>false, 'message'=>$e->getMessage()], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

echo json_encode(['success'=>false, 'message'=>'지원하지 않는 요청'], JSON_UNESCAPED_UNICODE);
exit;
