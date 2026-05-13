<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";

if ($_POST['act'] == "input") {
    header('Content-Type: application/json');

    try {
        // 필수 입력값 체크
        if (empty($_POST['ct_title'])) {
            throw new Exception("쿠폰명을 입력해주세요.");
        }

        if (empty($_POST['ct_type1'])) {
            throw new Exception("유효기간 설정 방식을 선택해주세요.");
        }

        if ($_POST['ct_type1'] == '1' && (empty($_POST['ct_sdate']) || empty($_POST['ct_edate']))) {
            throw new Exception("유효기간을 설정해주세요.");
        }

        if ($_POST['ct_type1'] == '2' && (empty($_POST['ct_days']) || $_POST['ct_days'] <= 0)) {
            throw new Exception("유효일수를 입력해주세요.");
        }

        if (empty($_POST['ct_type2'])) {
            throw new Exception($_POST['ct_type2']."할인 유형을 선택해주세요.");
        }

        if (!isset($_POST['ct_discount1']) || $_POST['ct_discount1'] === '') {
            throw new Exception("할인 금액/비율을 입력해주세요.");
        }

        if ($_POST['ct_type2'] == '2' && $_POST['ct_discount1'] > 100) {
            throw new Exception("할인 비율은 최대 100%까지 설정 가능합니다.");
        }

        // 쿠폰 코드 처리
        if (empty($_POST['ct_code'])) {
            $_POST['ct_code'] = generate_coupon_code();

            // 중복 코드 확인 및 재생성
            while (check_coupon_code_exists($_POST['ct_code'])) {
                $_POST['ct_code'] = generate_coupon_code();
            }
        } else {
            // 사용자 지정 쿠폰 코드 중복 확인
            if (check_coupon_code_exists($_POST['ct_code'])) {
                throw new Exception("이미 사용 중인 쿠폰 코드입니다.");
            }
        }

        // 적용 대상 처리
        $ct_target_name = '';
        if ($_POST['ct_target'] == '1' && !empty($_POST['ct_target_category'])) {
            $ct_target_name = implode(',', $_POST['ct_target_category']);
        } else if ($_POST['ct_target'] == '2' && !empty($_POST['ct_target_product'])) {
            $ct_target_name = implode(',', $_POST['ct_target_product']);
        }

        // 회원 등급 처리
        $ct_member_type = '';
        if (!empty($_POST['ct_member_type'])) {
            $ct_member_type = implode(',', $_POST['ct_member_type']);
        }

        // 트랜잭션 시작
        $DB->startTransaction();

        // 쿠폰 데이터 준비
        $arr_query = array(
            "ct_seller_idx" => $_POST['ct_seller_idx'],
            "ct_title" => clean_xss_tags($_POST['ct_title']),
            "ct_subtitle" => clean_xss_tags($_POST['ct_subtitle'] ?? ''),
            "ct_code" => $_POST['ct_code'],
            "ct_method" => $_POST['ct_method'] ?? 2, // 기본값: 2 (수동 발급)
            "ct_target" => $_POST['ct_target'] ?? 0,
            "ct_target_name" => $ct_target_name,
            "ct_type1" => $_POST['ct_type1'],
            "ct_sdate" => $_POST['ct_type1'] == '1' ? $_POST['ct_sdate'] : null,
            "ct_edate" => $_POST['ct_type1'] == '1' ? $_POST['ct_edate'] : null,
            "ct_days" => $_POST['ct_type1'] == '2' ? $_POST['ct_days'] : 0,
            "ct_type2" => $_POST['ct_type2'],
            "ct_discount1" => $_POST['ct_discount1'],
            "ct_discount2" => $_POST['ct_discount2'] ?? 0,
            "ct_discount3" => $_POST['ct_discount3'] ?? 0,
            "ct_member_type" => $ct_member_type,
            "ct_member_limit" => $_POST['ct_member_limit'] ?? 0,
            "ct_auto_issue" => isset($_POST['ct_auto_issue']) ? 'Y' : 'N',
            "ct_auto_type" => isset($_POST['ct_auto_issue']) ? ($_POST['ct_auto_type'] ?? 0) : 0,
            "ct_issue_limit" => $_POST['ct_issue_limit'] ?? 0,
            "ct_memo" => clean_xss_tags($_POST['ct_memo'] ?? ''),
            "ct_show" => $_POST['ct_show'] ?? 'Y',
            "ct_download" => 0,
            "ct_use" => 0,
            "ct_wdate" => $DB->now()
        );

        // 쿠폰 데이터 삽입
        $_last_idx = $DB->insert('coupon_t', $arr_query);

        if (!$_last_idx) {
            throw new Exception("쿠폰 등록 중 오류가 발생했습니다.");
        }

        $DB->commit();

        // 성공 응답
        echo json_encode([
            'success' => true,
            'message' => '쿠폰이 성공적으로 등록되었습니다.',
            'coupon_id' => $_last_idx,
            'redirect' => './coupon_list.php'
        ]);

    } catch (Exception $e) {
        $DB->rollback();
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} elseif ($_POST['act'] == "update") {
    header('Content-Type: application/json');

    try {
        // 필수 입력값 체크
        if (empty($_POST['ct_title'])) {
            throw new Exception("쿠폰명을 입력해주세요.");
        }

        if (empty($_POST['ct_type1'])) {
            throw new Exception("유효기간 설정 방식을 선택해주세요.");
        }

        if ($_POST['ct_type1'] == '1' && (empty($_POST['ct_sdate']) || empty($_POST['ct_edate']))) {
            throw new Exception("유효기간을 설정해주세요.");
        }

        if ($_POST['ct_type1'] == '2' && (empty($_POST['ct_days']) || $_POST['ct_days'] <= 0)) {
            throw new Exception("유효일수를 입력해주세요.");
        }

        if (empty($_POST['ct_type2'])) {
            throw new Exception("할인 유형을 선택해주세요.");
        }

        if (!isset($_POST['ct_discount1']) || $_POST['ct_discount1'] === '') {
            throw new Exception("할인 금액/비율을 입력해주세요.");
        }

        if ($_POST['ct_type2'] == '2' && $_POST['ct_discount1'] > 100) {
            throw new Exception("할인 비율은 최대 100%까지 설정 가능합니다.");
        }

        // 쿠폰 존재 여부 확인
        $DB->where('idx', $_POST['ct_idx']);
        $coupon = $DB->getOne('coupon_t');

        if (!$coupon) {
            throw new Exception("존재하지 않는 쿠폰입니다.");
        }

        // 쿠폰 코드 중복 확인 (변경된 경우)
        if ($_POST['ct_code'] != $coupon['ct_code'] && check_coupon_code_exists($_POST['ct_code'], $_POST['ct_idx'])) {
            throw new Exception("이미 사용 중인 쿠폰 코드입니다.");
        }

        // 적용 대상 처리
        $ct_target_name = '';
        if ($_POST['ct_target'] == '1' && !empty($_POST['ct_target_category'])) {
            $ct_target_name = implode(',', $_POST['ct_target_category']);
        } else if ($_POST['ct_target'] == '2' && !empty($_POST['ct_target_product'])) {
            $ct_target_name = implode(',', $_POST['ct_target_product']);
        }

        // 회원 등급 처리
        $ct_member_type = '';
        if (!empty($_POST['ct_member_type'])) {
            $ct_member_type = implode(',', $_POST['ct_member_type']);
        }

        // 트랜잭션 시작
        $DB->startTransaction();

        // 쿠폰 데이터 준비
        $arr_query = array(
            "ct_title" => clean_xss_tags($_POST['ct_title']),
            "ct_subtitle" => clean_xss_tags($_POST['ct_subtitle'] ?? ''),
            "ct_code" => $_POST['ct_code'],
            "ct_method" => $_POST['ct_method'] ?? 2, // 기본값: 2 (수동 발급)
            "ct_target" => $_POST['ct_target'] ?? 0,
            "ct_target_name" => $ct_target_name,
            "ct_type1" => $_POST['ct_type1'],
            "ct_sdate" => $_POST['ct_type1'] == '1' ? $_POST['ct_sdate'] : null,
            "ct_edate" => $_POST['ct_type1'] == '1' ? $_POST['ct_edate'] : null,
            "ct_days" => $_POST['ct_type1'] == '2' ? $_POST['ct_days'] : 0,
            "ct_type2" => $_POST['ct_type2'],
            "ct_discount1" => $_POST['ct_discount1'],
            "ct_discount2" => $_POST['ct_discount2'] ?? 0,
            "ct_discount3" => $_POST['ct_discount3'] ?? 0,
            "ct_member_type" => $ct_member_type,
            "ct_member_limit" => $_POST['ct_member_limit'] ?? 0,
            "ct_auto_issue" => isset($_POST['ct_auto_issue']) ? 'Y' : 'N',
            "ct_auto_type" => isset($_POST['ct_auto_issue']) ? ($_POST['ct_auto_type'] ?? 0) : 0,
            "ct_issue_limit" => $_POST['ct_issue_limit'] ?? 0,
            "ct_memo" => clean_xss_tags($_POST['ct_memo'] ?? ''),
            "ct_show" => $_POST['ct_show'] ?? 'Y',
            "ct_wdate" => $DB->now()
        );

        // 쿠폰 데이터 업데이트
        $DB->where('idx', $_POST['ct_idx']);
        $result = $DB->update('coupon_t', $arr_query);

        if (!$result) {
            throw new Exception("쿠폰 수정 중 오류가 발생했습니다.");
        }

        $DB->commit();

        // 성공 응답
        echo json_encode([
            'success' => true,
            'message' => '쿠폰이 성공적으로 수정되었습니다.',
            'redirect' => './coupon_list.php'
        ]);

    } catch (Exception $e) {
        $DB->rollback();
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} elseif ($_POST['act'] == "delete") {
    header('Content-Type: application/json');

    try {
        if (empty($_POST['ct_idx'])) {
            throw new Exception("삭제할 쿠폰을 선택해주세요.");
        }

        // 트랜잭션 시작
        $DB->startTransaction();

        // 쿠폰 존재 여부 확인
        $DB->where('idx', $_POST['ct_idx']);
        $coupon = $DB->getOne('coupon_t');

        if (!$coupon) {
            throw new Exception("존재하지 않는 쿠폰입니다.");
        }

        // 이미 사용된 쿠폰인지 확인
        $DB->where('ct_idx', $_POST['ct_idx']);
        $DB->where('cm_status', 2); // 2 = 사용됨
        $used_count = $DB->getValue('coupon_member_t', 'COUNT(*)');

        if ($used_count > 0) {
            throw new Exception("이미 사용된 쿠폰이 있어 삭제할 수 없습니다.");
        }

        // 발급된 쿠폰 삭제
        $DB->where('ct_idx', $_POST['ct_idx']);
        $DB->delete('coupon_member_t');

        // 쿠폰 로그 삭제
        $DB->where('ct_idx', $_POST['ct_idx']);
        $DB->delete('coupon_log_t');

        // 쿠폰 삭제
        $DB->where('idx', $_POST['ct_idx']);
        $result = $DB->delete('coupon_t');

        if (!$result) {
            throw new Exception("쿠폰 삭제 중 오류가 발생했습니다.");
        }

        $DB->commit();

        // 성공 응답
        echo json_encode([
            'success' => true,
            'message' => '쿠폰이 성공적으로 삭제되었습니다.',
            'redirect' => './coupon_list.php'
        ]);

    } catch (Exception $e) {
        $DB->rollback();
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} elseif($_POST['act'] == 'get_sellers') {
    unset($list);

    // 판매자 레벨 필터링
    $mb_level = isset($_POST['mt_level']) ? (int)$_POST['mt_level'] : 5;
    $DB->where('mt_level', $mb_level, '=');

    // 탈퇴하지 않은 회원만 (빈 문자열 또는 NULL)
    $DB->where("(mt_rdate = '' OR mt_rdate IS NULL)");

    // 정렬 설정
    $DB->orderBy("mt_name", "asc");

    // 회원 데이터 가져오기 (필요한 필드만)
    $list = $DB->arraybuilder()->get("member_t", NULL, 'mt_id, mt_name, mt_nickname, idx as mt_idx');

    if ($list) {
        // 이름이 없는 경우 닉네임으로 대체
        foreach($list as &$seller) {
            if(empty($seller['mt_name'])) {
                $seller['mt_name'] = $seller['mb_nickname'];
            }
        }

        echo json_encode(array('success' => true, 'sellers' => $list));
    } else {
        echo json_encode(array('success' => false, 'message' => '등록된 판매자가 없습니다.'));
    }
    exit;
} elseif($_POST['act'] == 'get_products') {
    // 상품  정보 가져오기
    unset($list);

    // 사용 여부 필터링 (선택적)
    if (isset($_POST['pt_show'])) {
        $DB->where('a1.pt_show', $_POST['pt_show']);
    } else {
        $DB->where('a1.pt_show', 'Y'); // 기본적으로 사용 중인 상품  표시
    }

    // 정렬 설정
    if (isset($_POST['obj_order_desc_asc']) && $_POST['obj_order_desc_asc'] == '1') {
        $DB->orderBy("a1.idx", "asc");
    } else {
        $DB->orderBy("a1.idx", "asc");
    }

    // 카테고리 데이터 가져오기
    $list = $DB->arraybuilder()->get("product_t  a1", NULL, 'pt_title, idx as pt_idx');

    if ($list) {
        echo json_encode(array('success' => true, 'products' => $list));
    } else {
        echo json_encode(array('success' => false, 'message' => '등록된 상품이 없습니다.'));
    }
    exit;
} elseif($_POST['act'] == 'get_categories') {
    // 카테고리 정보 가져오기
    unset($list);

    // 사용 여부 필터링 (선택적)
    if (isset($_POST['ct_show'])) {
        $DB->where('a1.ct_show', $_POST['ct_show']);
    } else {
        $DB->where('a1.ct_show', 'Y'); // 기본적으로 사용 중인 카테고리만 표시
    }

    // 정렬 설정
    if (isset($_POST['obj_order_desc_asc']) && $_POST['obj_order_desc_asc'] == '1') {
        $DB->orderBy("a1.ct_order", "asc");
    } else {
        $DB->orderBy("a1.ct_order", "asc");
    }

    // 카테고리 데이터 가져오기
    $list = $DB->arraybuilder()->get("category_t a1", NULL, '*, ct_id as ct_idx');

    if ($list) {
        echo json_encode(array('success' => true, 'categories' => $list));
    } else {
        echo json_encode(array('success' => false, 'message' => '등록된 카테고리가 없습니다.'));
    }
    exit;
} elseif ($_POST['act'] == "duplicate") {
    header('Content-Type: application/json');

    try {
        if (empty($_POST['ct_idx'])) {
            throw new Exception("복제할 쿠폰을 선택해주세요.");
        }

        // 트랜잭션 시작
        $DB->startTransaction();

        // 쿠폰 존재 여부 확인
        $DB->where('idx', $_POST['ct_idx']);
        $coupon = $DB->getOne('coupon_t');

        if (!$coupon) {
            throw new Exception("존재하지 않는 쿠폰입니다.");
        }

        // 새 쿠폰 코드 생성
        $new_code = generate_coupon_code();

        // 중복 코드 확인 및 재생성
        while (check_coupon_code_exists($new_code)) {
            $new_code = generate_coupon_code();
        }

        // 복제할 쿠폰 데이터 준비
        $new_coupon = $coupon;
        unset($new_coupon['idx']); // 기본키 제거
        $new_coupon['ct_title'] = $coupon['ct_title'] . ' (복제)';
        $new_coupon['ct_code'] = $new_code;
        $new_coupon['ct_download'] = 0;
        $new_coupon['ct_used'] = 0;
        $new_coupon['reg_date'] = $DB->now();
        $new_coupon['mod_date'] = null;

        // 새 쿠폰 생성
        $new_id = $DB->insert('coupon_t', $new_coupon);

        if (!$new_id) {
            throw new Exception("쿠폰 복제 중 오류가 발생했습니다.");
        }

        $DB->commit();

        // 성공 응답
        echo json_encode([
            'success' => true,
            'message' => '쿠폰이 성공적으로 복제되었습니다.',
            'coupon_id' => $new_id,
            'redirect' => './coupon_list.php'
        ]);

    } catch (Exception $e) {
        $DB->rollback();
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} elseif ($_POST['act'] == "change_status") {
    header('Content-Type: application/json');

    try {
        if (empty($_POST['ct_idx'])) {
            throw new Exception("상태를 변경할 쿠폰을 선택해주세요.");
        }

        if (!isset($_POST['ct_show']) || ($_POST['ct_show'] != 'Y' && $_POST['ct_show'] != 'N')) {
            throw new Exception("올바른 상태 값을 입력해주세요.");
        }

        // 쿠폰 존재 여부 확인
        $DB->where('idx', $_POST['ct_idx']);
        $coupon = $DB->getOne('coupon_t');

        if (!$coupon) {
            throw new Exception("존재하지 않는 쿠폰입니다.");
        }

        // 상태 변경
        $DB->where('idx', $_POST['ct_idx']);
        $result = $DB->update('coupon_t', [
            'ct_show' => $_POST['ct_show'],
            'mod_date' => $DB->now()
        ]);

        if (!$result) {
            throw new Exception("쿠폰 상태 변경 중 오류가 발생했습니다.");
        }

        // 성공 응답
        echo json_encode([
            'success' => true,
            'message' => '쿠폰 상태가 성공적으로 변경되었습니다.',
            'status' => $_POST['ct_show']
        ]);

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} elseif ($_POST['act'] == "bulk_delete") {
    header('Content-Type: application/json');

    try {
        if (empty($_POST['ct_idx']) || !is_array($_POST['ct_idx'])) {
            throw new Exception("삭제할 쿠폰을 선택해주세요.");
        }

        $ids = array_map('intval', $_POST['ct_idx']);

        // 트랜잭션 시작
        $DB->startTransaction();

        // 이미 사용된 쿠폰이 있는지 확인
        $DB->where('ct_idx', $ids, 'IN');
        $DB->where('cm_status', 2); // 2 = 사용됨
        $used_count = $DB->getValue('coupon_member_t', 'COUNT(*)');

        if ($used_count > 0) {
            throw new Exception("이미 사용된 쿠폰이 있어 일괄 삭제할 수 없습니다.");
        }

        // 발급된 쿠폰 삭제
        $DB->where('ct_idx', $ids, 'IN');
        $DB->delete('coupon_member_t');

        // 쿠폰 로그 삭제
        $DB->where('ct_idx', $ids, 'IN');
        $DB->delete('coupon_log_t');

        // 쿠폰 삭제
        $DB->where('idx', $ids, 'IN');
        $result = $DB->delete('coupon_t');

        if (!$result) {
            throw new Exception("쿠폰 일괄 삭제 중 오류가 발생했습니다.");
        }

        $DB->commit();

        // 성공 응답
        echo json_encode([
            'success' => true,
            'message' => count($ids) . '개의 쿠폰이 성공적으로 삭제되었습니다.',
            'redirect' => './coupon_list.php'
        ]);

    } catch (Exception $e) {
        $DB->rollback();
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} elseif ($_POST['act'] == "issue_coupon") {
    header('Content-Type: application/json');

    try {
        if (empty($_POST['ct_idx'])) {
            throw new Exception("발급할 쿠폰을 선택해주세요.");
        }

        if (empty($_POST['mt_idx'])) {
            throw new Exception("쿠폰을 발급할 회원을 선택해주세요.");
        }

        // 발급 함수 호출
        $result = issue_coupon(
            $_POST['ct_idx'],
            $_POST['mt_idx'],
            !empty($_POST['cl_memo']) ? clean_xss_tags($_POST['cl_memo']) : '관리자 수동 발급'
        );

        if (!$result['success']) {
            throw new Exception($result['message']);
        }

        // 성공 응답
        echo json_encode([
            'success' => true,
            'message' => $result['message'],
            'coupon_code' => $result['coupon_code']
        ]);

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} elseif ($_POST['act'] == "cancel_issue") {
    header('Content-Type: application/json');

    try {
        if (empty($_POST['cm_idx'])) {
            throw new Exception("취소할 발급 쿠폰을 선택해주세요.");
        }

        // 발급 취소 함수 호출
        $result = cancel_coupon_issue(
            $_POST['cm_idx'],
            !empty($_POST['cl_memo']) ? clean_xss_tags($_POST['cl_memo']) : '관리자 발급 취소'
        );

        if (!$result['success']) {
            throw new Exception($result['message']);
        }

        // 성공 응답
        echo json_encode([
            'success' => true,
            'message' => $result['message']
        ]);

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} elseif ($_POST['act'] == "bulk_issue") {
    header('Content-Type: application/json');

    try {
        if (empty($_POST['ct_idx'])) {
            throw new Exception("발급할 쿠폰을 선택해주세요.");
        }

        if (empty($_POST['mt_idx']) || !is_array($_POST['mt_idx'])) {
            throw new Exception("쿠폰을 발급할 회원을 선택해주세요.");
        }

        $member_ids = array_map('intval', $_POST['mt_idx']);

        // 일괄 발급 함수 호출
        $result = bulk_issue_coupons(
            $_POST['ct_idx'],
            $member_ids,
            !empty($_POST['cl_memo']) ? clean_xss_tags($_POST['cl_memo']) : '관리자 일괄 발급'
        );

        if (!$result['success']) {
            throw new Exception($result['message']);
        }

        // 성공 응답
        echo json_encode([
            'success' => true,
            'message' => $result['message'],
            'success_count' => $result['success_count'],
            'fail_count' => $result['fail_count'],
            'results' => $result['results']
        ]);

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} elseif ($_POST['act'] == "generate_code") {
    header('Content-Type: application/json');

    try {
        // 쿠폰 코드 생성
        $coupon_code = generate_coupon_code();

        // 중복 코드 확인 및 재생성
        while (check_coupon_code_exists($coupon_code)) {
            $coupon_code = generate_coupon_code();
        }

        // 성공 응답
        echo json_encode([
            'success' => true,
            'coupon_code' => $coupon_code
        ]);

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

} elseif ($_POST['act'] == "list") {
    unset($list);
    $DB->pageLimit = $_POST['obj_limit_num'];
    $pg = $_POST['obj_pg'];

    // 검색 조건 처리
    if ($_POST['obj_search_txt']) {
        if ($_POST['obj_sel_search'] == "all") {
            $DB->where('( instr(a1.ct_title, \''.$_POST['obj_search_txt'].'\') or instr(a1.ct_code, \''.$_POST['obj_search_txt'].'\') )');
        } else {
            $DB->where('( instr('.$_POST['obj_sel_search'].', \''.$_POST['obj_search_txt'].'\') )');
        }
    }

    // 노출 상태 필터링
    if (isset($_POST['ct_show'])) {
        $DB->where('a1.ct_show', $_POST['ct_show']);
    }

    // 쿠폰 유형 필터링
    if (isset($_POST['ct_type2']) && $_POST['ct_type2'] !== '') {
        $DB->where('a1.ct_type2', $_POST['ct_type2']);
    }

    // 판매자 필터링
    if (isset($_POST['ct_seller_idx']) && $_POST['ct_seller_idx'] > 0) {
        $DB->where('a1.ct_seller_idx', $_POST['ct_seller_idx']);
    }

    // 정렬 설정
    if (isset($_POST['obj_order_desc_asc']) && $_POST['obj_order_desc_asc'] == '1') {
        $DB->orderBy("a1.idx", "asc");
    } else {
        $DB->orderBy("a1.idx", "desc");
    }

    // 쿠폰 목록 조회
    $list = $DB->arraybuilder()->paginate("coupon_t a1", $pg, '*, idx as ct_idx');

    // 페이징 정보
    $n_page = $DB->totalPages;
    $counts = $DB->totalCount;
    $counts = $counts - (($pg - 1) * $_POST['obj_limit_num']);
    ?>
    <div class="table-responsive margin-top-20">
        <table class="table table-striped table-bordered margin-bottom-20" id="listTable" style="min-width: 800px">
            <thead class="thead-dark">
            <tr>
                <th class="text-center" style="width:50px;">
                    <input type="checkbox" id="selectAll" class="custom-checkbox-list" />
                </th>
                <th class="text-center" style="width:80px;">번호</th>
                <th class="text-center" style="width:120px;">관리</th>
                <th class="text-center">쿠폰명</th>
                <th class="text-center">발급대상</th>
                <th class="text-center" style="width:200px;">쿠폰할인</th>
                <th class="text-center" style="width:100px;">최소 주문금액</th>
                <th class="text-center" style="width:160px;">유효기간</th>
                <th class="text-center" style="width:80px;">발급수</th>
                <th class="text-center" style="width:80px;">사용수</th>
                <th class="text-center" style="width:80px;">만료수</th>
<!--                <th class="text-center" style="width:80px;">상태</th>-->
                <th class="text-center" style="width:130px;">발급일시</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($list) {
                foreach ($list as $row) {
                    // 할인 유형 텍스트
                    $discount_type = '';
                    if ($row['ct_type2'] == '2') {
                        $discount_type = '정률(%)';
                    } elseif ($row['ct_type2'] == '1') {
                        $discount_type = '정액(원)';
                    }

                    // 유효기간 표시
                    $validity_period = '';
                    if ($row['ct_type1'] == '1') {
                        $validity_period = DateType($row['ct_sdate'], 2) . ' ~ ' . DateType($row['ct_edate'], 2);
                    } elseif ($row['ct_type1'] == '2') {
                        $validity_period = '발급 후 ' . $row['ct_days'] . '일';
                    }
                    ?>
                    <tr>
                        <td class="text-center checkbox-wrapper">
                            <input type="checkbox" name="ct_idx[]" value="<?=$row['ct_idx']?>" class="rowCheckbox custom-checkbox-list" />
                        </td>
                        <td data-title="번호" class="text-center"><?=$counts?></td>
                        <td data-title="관리" class="text-center">
                            <button type="button" class="btn btn-outline-info btn-sm" onclick="location.href='./coupon_form.php?act=update&ct_id=<?=$row['ct_idx']?>'">수정</button>
                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteCoupon(<?=$row['ct_idx']?>)">삭제</button>
                        </td>
                        <td data-title="쿠폰명">
                            <span class="line1_text"><?=$row['ct_title']?></span>
                        </td>
                        <td data-title="쿠폰코드" class="text-center"><?=$row['ct_code']?></td>
                        <td data-title="할인유형" class="text-center"><?=$discount_type?></td>
                        <td data-title="할인액/율" class="text-center">
                            <?php
                            if ($row['ct_type2'] == '2') {
                                echo $row['ct_discount1'] . '%';
                            } elseif ($row['ct_type2'] == '1') {
                                echo number_format($row['ct_discount1']) . '원';
                            }
                            ?>
                        </td>
                        <td data-title="유효기간" class="text-center"><?=$validity_period?></td>
                        <td data-title="발급수" class="text-center"><?=number_format($row['ct_download'])?></td>
                        <td data-title="사용수" class="text-center"><?=number_format($row['ct_use'])?></td>
                        <td data-title="상태" class="text-center">
                    <span class="badge <?=$row['ct_show'] == 'Y' ? 'badge-success' : 'badge-secondary'?>">
                        <?=$row['ct_show'] == 'Y' ? '사용' : '중지'?>
                    </span>
                        </td>
                        <td data-title="등록일시" class="text-center"><?=DateType($row['ct_wdate'], 4)?></td>
                    </tr>
                    <?php
                    $counts--;
                }
            } else {
                ?>
                <tr>
                    <td colspan="12" class="text-center"><b>등록된 쿠폰이 없습니다.</b></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- 페이징 -->
    <?php
    if($n_page > 1) {
        echo page_listing_xhr($pg, $n_page, 'f_get_coupon_list');
    }
}

include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
