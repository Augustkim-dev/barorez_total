<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";

header('Content-Type: application/json');

if($_POST['act']=="secede") {
    try {
        // 1. 로그인 여부 체크
        if (empty($_POST['mt_idx'])) {
            throw new Exception('로그인이 필요합니다.');
        }
        $mt_idx = (int)$_POST['mt_idx'];
        // 4. 회원 존재 여부 확인
        $DB->where('idx', $mt_idx);
        $member = $DB->getOne('member_t', 'idx, mt_status');
        if (!$member) {
            throw new Exception('회원 정보를 찾을 수 없습니다.');
        }

        // 5. 탈퇴 처리 (상태 변경)
        $DB->startTransaction();

        $arr_update = [
            'mt_status' => 'N',       // 탈퇴 처리 상태
            'mt_level'  => 1,
            'mt_app_token' => '',
            'mt_rdate'  => $DB->now() // 수정일시
        ];

        $DB->where('idx', $mt_idx);
        $ok = $DB->update('member_t', $arr_update);

        if (!$ok) {
            throw new Exception('탈퇴 처리에 실패했습니다. 잠시 후 다시 시도해 주세요.');
        }

        $DB->commit();

        // 6. 세션 정리
        unset($_SESSION['mng']);
        // 다른 사용자 세션 키가 있다면 필요에 따라 정리
        // session_destroy(); // 전체 세션 종료가 필요하면 사용

        // 7. 응답
        echo json_encode([
            'success'  => true,
            'message'  => '회원 탈퇴가 완료되었습니다.',
            'redirect' => APP_PAGE . '/',   // 메인 페이지로 이동
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        if ($DB && method_exists($DB, 'rollback')) {
            $DB->rollback();
        }

        echo json_encode([
            'success' => false,
            'message' => $e->getMessage(),
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

?>
