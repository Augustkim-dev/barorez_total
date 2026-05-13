<?php
/* 쿠폰 기본 관리 함수 */
/* 쿠폰 코드 생성 함수 (xxxx-xxxx-xxxx-xxx 형식) */
function generate_coupon_code($length = 12) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $code = '';

    for ($i = 0; $i < $length; $i++) {
        $code .= $characters[rand(0, strlen($characters) - 1)];
    }

    // 4자리마다 하이픈 추가
    $code = implode('-', str_split($code, 4));

    return $code;
}

/* 쿠폰 코드 중복 확인 함수 */
function check_coupon_code_exists($code, $exclude_id = null) {
    global $DB;

    // 쿠폰 테이블 확인
    $DB->where('ct_code', $code);
    if ($exclude_id !== null) {
        $DB->where('idx', $exclude_id, '!=');
    }
    $exists = $DB->getValue('coupon_t', 'COUNT(*)');

    if ($exists > 0) {
        return true;
    }

    // 발급된 쿠폰 테이블 확인
    $DB->where('cm_code', $code);
    $exists = $DB->getValue('coupon_member_t', 'COUNT(*)');

    return $exists > 0;
}


/* 쿠폰 로그 상태 텍스트 반환 함수*/
function get_coupon_status_text($status) {
    $status_texts = [
        1 => '사용 가능',
        2 => '사용 완료',
        3 => '발급 취소',
        4 => '기간 만료'
    ];

    return isset($status_texts[$status]) ? $status_texts[$status] : '알 수 없음';
}

/* 쿠폰 로그 상태 텍스트 반환 함수 */
function get_coupon_log_status_text($status) {
    $status_text = [
        1 => '발급',
        2 => '사용',
        3 => '발급 취소',
        4 => '사용 취소',
        5 => '기간 만료'
    ];

    return $status_text[$status] ?? '알 수 없음';
}

/* 쿠폰 발급 방식 텍스트 반환 함수 */
function get_coupon_method_text($method) {
    $method_text = [
        1 => '자동 발급',
        2 => '수동 발급',
        3 => '회원가입 발급',
        4 => '이벤트 발급',
        5 => '생일 발급'
    ];

    return $method_text[$method] ?? '알 수 없음';
}

/* 쿠폰 기간 설정 방식 텍스트 반환 함수 */
function get_coupon_period_type_text($type) {
    $type_text = [
        1 => '기간 설정',
        2 => '발급일 기준'
    ];

    return $type_text[$type] ?? '알 수 없음';
}

/* 쿠폰 할인 유형 텍스트 반환 함수 */
function get_coupon_discount_type_text($type) {
    $type_text = [
        0 => '정액',
        1 => '정률'
    ];

    return $type_text[$type] ?? '알 수 없음';
}

/* 쿠폰 대상 텍스트 반환 함수 */
function get_coupon_target_text($target) {
    $target_text = [
        0 => '전체 상품',
        1 => '특정 카테고리',
        2 => '특정 상품'
    ];

    return $target_text[$target] ?? '알 수 없음';
}

/* 쿠폰 자동화 처리 함수 */
/*쿠폰 자동 발급 처리 함수 (크론 작업용)*/
function process_auto_coupons() {
    global $DB;

    $today = date('Y-m-d');
    $log = [];

    try {
        // 트랜잭션 시작
        $DB->startTransaction();

        // 1. 회원가입 쿠폰 발급
        $log[] = process_signup_coupons();

        // 2. 생일 쿠폰 발급
        $log[] = process_birthday_coupons($today);

        // 3. 만료된 쿠폰 처리
        $log[] = process_expired_coupons($today);

        $DB->commit();

        return [
            'success' => true,
            'log' => $log
        ];
    } catch (Exception $e) {
        $DB->rollback();

        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}

/* 회원가입 쿠폰 자동 발급 처리 함수 */
function process_signup_coupons() {
    global $DB;

    // 회원가입 쿠폰 조회
    $DB->where('ct_method', 3); // 3 = 회원가입 발급
    $DB->where('ct_show', 'Y');
    $signup_coupons = $DB->get('coupon_t');

    if (empty($signup_coupons)) {
        return '회원가입 쿠폰이 설정되어 있지 않습니다.';
    }

    // 어제 가입한 회원 조회 (일 1회 실행 기준)
    $yesterday = date('Y-m-d', strtotime('-1 day'));
    $DB->where('DATE(reg_date)', $yesterday);
    $new_members = $DB->get('member_t');

    if (empty($new_members)) {
        return '어제 가입한 회원이 없습니다.';
    }

    $issued_count = 0;

    foreach ($new_members as $member) {
        foreach ($signup_coupons as $coupon) {
            // 이미 발급된 쿠폰인지 확인
            $DB->where('ct_idx', $coupon['idx']);
            $DB->where('mt_idx', $member['idx']);
            $exists = $DB->getValue('coupon_member_t', 'COUNT(*)');

            if ($exists > 0) {
                continue;
            }

            // 쿠폰 코드 생성
            $coupon_code = generate_coupon_code();

            // 중복 코드 확인 및 재생성
            while (true) {
                $DB->where('cm_code', $coupon_code);
                $exists = $DB->getValue('coupon_member_t', 'COUNT(*)');

                if ($exists == 0) {
                    break;
                }

                $coupon_code = generate_coupon_code();
            }

            // 쿠폰 유효기간 설정
            $start_date = null;
            $end_date = null;

            if ($coupon['ct_type1'] == 1) {
                // 기간 설정 방식
                $start_date = $coupon['ct_sdate'];
                $end_date = $coupon['ct_edate'];
            } else {
                // 발급일 기준 방식
                $start_date = date('Y-m-d');
                $end_date = date('Y-m-d', strtotime("+{$coupon['ct_days']} days"));
            }

            // 쿠폰 발급 데이터
            $issue_data = [
                'ct_idx' => $coupon['idx'],
                'mt_idx' => $member['idx'],
                'cm_code' => $coupon_code,
                'cm_sdate' => $start_date,
                'cm_edate' => $end_date,
                'cm_status' => 1, // 1 = 사용 가능
                'reg_date' => date('Y-m-d H:i:s')
            ];

            // 쿠폰 발급
            $issue_id = $DB->insert('coupon_member_t', $issue_data);

            if ($issue_id) {
                // 쿠폰 발급 로그
                $log_data = [
                    'ct_idx' => $coupon['idx'],
                    'cm_idx' => $issue_id,
                    'mt_idx' => $member['idx'],
                    'cl_status' => 1, // 1 = 발급
                    'cl_memo' => '회원가입 자동 발급',
                    'reg_date' => date('Y-m-d H:i:s')
                ];

                $DB->insert('coupon_log_t', $log_data);

                // 쿠폰 발급 카운트 증가
                $DB->where('idx', $coupon['idx']);
                $DB->update('coupon_t', ['ct_download' => $DB->inc(1)]);

                $issued_count++;
            }
        }
    }

    return "회원가입 쿠폰 {$issued_count}개가 발급되었습니다.";
}

/* 생일 쿠폰 자동 발급 처리 함수 */
function process_birthday_coupons($today) {
    global $DB;

    // 생일 쿠폰 조회
    $DB->where('ct_method', 5); // 5 = 생일 발급
    $DB->where('ct_show', 'Y');
    $birthday_coupons = $DB->get('coupon_t');

    if (empty($birthday_coupons)) {
        return '생일 쿠폰이 설정되어 있지 않습니다.';
    }

    // 오늘이 생일인 회원 조회
    $today_month_day = date('m-d');
    $sql = "SELECT * FROM member_t WHERE DATE_FORMAT(mt_birth, '%m-%d') = ?";
    $members = $DB->rawQuery($sql, [$today_month_day]);

    if (empty($members)) {
        return '오늘 생일인 회원이 없습니다.';
    }

    $issued_count = 0;

    foreach ($members as $member) {
        foreach ($birthday_coupons as $coupon) {
            // 이미 발급된 쿠폰인지 확인 (올해 기준)
            $this_year = date('Y');
            $start_of_year = "{$this_year}-01-01";
            $end_of_year = "{$this_year}-12-31";

            $DB->where('ct_idx', $coupon['idx']);
            $DB->where('mt_idx', $member['idx']);
            $DB->where('reg_date', $start_of_year, '>=');
            $DB->where('reg_date', $end_of_year, '<=');
            $exists = $DB->getValue('coupon_member_t', 'COUNT(*)');

            if ($exists > 0) {
                continue;
            }

            // 쿠폰 코드 생성
            $coupon_code = generate_coupon_code();

            // 중복 코드 확인 및 재생성
            while (true) {
                $DB->where('cm_code', $coupon_code);
                $exists = $DB->getValue('coupon_member_t', 'COUNT(*)');

                if ($exists == 0) {
                    break;
                }

                $coupon_code = generate_coupon_code();
            }

            // 쿠폰 유효기간 설정
            $start_date = null;
            $end_date = null;

            if ($coupon['ct_type1'] == 1) {
                // 기간 설정 방식
                $start_date = $coupon['ct_sdate'];
                $end_date = $coupon['ct_edate'];
            } else {
                // 발급일 기준 방식
                $start_date = date('Y-m-d');
                $end_date = date('Y-m-d', strtotime("+{$coupon['ct_days']} days"));
            }

            // 쿠폰 발급 데이터
            $issue_data = [
                'ct_idx' => $coupon['idx'],
                'mt_idx' => $member['idx'],
                'cm_code' => $coupon_code,
                'cm_sdate' => $start_date,
                'cm_edate' => $end_date,
                'cm_status' => 1, // 1 = 사용 가능
                'reg_date' => date('Y-m-d H:i:s')
            ];

            // 쿠폰 발급
            $issue_id = $DB->insert('coupon_member_t', $issue_data);

            if ($issue_id) {
                // 쿠폰 발급 로그
                $log_data = [
                    'ct_idx' => $coupon['idx'],
                    'cm_idx' => $issue_id,
                    'mt_idx' => $member['idx'],
                    'cl_status' => 1, // 1 = 발급
                    'cl_memo' => '생일 자동 발급',
                    'reg_date' => date('Y-m-d H:i:s')
                ];

                $DB->insert('coupon_log_t', $log_data);

                // 쿠폰 발급 카운트 증가
                $DB->where('idx', $coupon['idx']);
                $DB->update('coupon_t', ['ct_download' => $DB->inc(1)]);

                $issued_count++;
            }
        }
    }

    return "생일 쿠폰 {$issued_count}개가 발급되었습니다.";
}

/* 만료된 쿠폰 처리 함수 */
function process_expired_coupons($today) {
    global $DB;

    // 어제 만료된 쿠폰 조회
    $yesterday = date('Y-m-d', strtotime('-1 day'));
    $DB->where('cm_status', 1); // 1 = 사용 가능
    $DB->where('cm_edate', $yesterday);
    $expired_coupons = $DB->get('coupon_member_t');

    if (empty($expired_coupons)) {
        return '만료된 쿠폰이 없습니다.';
    }

    $processed_count = 0;

    foreach ($expired_coupons as $coupon) {
        // 쿠폰 상태 업데이트
        $DB->where('idx', $coupon['idx']);
        $result = $DB->update('coupon_member_t', [
            'cm_status' => 4, // 4 = 기간 만료
            'mod_date' => date('Y-m-d H:i:s')
        ]);

        if ($result) {
            // 쿠폰 만료 로그
            $log_data = [
                'ct_idx' => $coupon['ct_idx'],
                'cm_idx' => $coupon['idx'],
                'mt_idx' => $coupon['mt_idx'],
                'cl_status' => 5, // 5 = 기간 만료
                'cl_memo' => '유효기간 만료',
                'reg_date' => date('Y-m-d H:i:s')
            ];

            $DB->insert('coupon_log_t', $log_data);
            $processed_count++;
        }
    }

    return "만료된 쿠폰 {$processed_count}개가 처리되었습니다.";
}



/* 쿠폰 CRUD 함수 */
/* 쿠폰 데이터 유효성 검사 */
function validate_coupon_data($data) {
    $errors = [];

    // 필수 필드 확인
    if (empty($data['ct_title'])) {
        $errors[] = '쿠폰명은 필수 입력 항목입니다.';
    }

    if (empty($data['ct_method'])) {
        $errors[] = '쿠폰 종류는 필수 선택 항목입니다.';
    } else {
        // 쿠폰 종류에 따른 대상 확인
        if (($data['ct_method'] == 1 || $data['ct_method'] == 2) && empty($data['ct_target'])) {
            $errors[] = '대상 선택은 필수 항목입니다.';
        }
    }

    if (empty($data['ct_type2'])) {
        $errors[] = '할인 유형은 필수 선택 항목입니다.';
    }

    if (!isset($data['ct_discount1']) || $data['ct_discount1'] === '') {
        $errors[] = '할인 금액/비율은 필수 입력 항목입니다.';
    } else if ($data['ct_discount1'] <= 0) {
        $errors[] = '할인 금액/비율은 0보다 커야 합니다.';
    }

    // 할인 유형이 정률일 경우 최대 100% 제한
    if (isset($data['ct_type2']) && $data['ct_type2'] == 1 && $data['ct_discount1'] > 100) {
        $errors[] = '할인 비율은 최대 100%까지 설정 가능합니다.';
    }

    // 기간 설정 검증
    if (empty($data['ct_type1'])) {
        $errors[] = '기간 설정 방식은 필수 선택 항목입니다.';
    } else {
        if ($data['ct_type1'] == 1) {
            // 기간 설정 방식
            if (empty($data['ct_sdate'])) {
                $errors[] = '시작일은 필수 입력 항목입니다.';
            }

            if (empty($data['ct_edate'])) {
                $errors[] = '종료일은 필수 입력 항목입니다.';
            }

            if (!empty($data['ct_sdate']) && !empty($data['ct_edate']) && $data['ct_sdate'] > $data['ct_edate']) {
                $errors[] = '종료일은 시작일 이후로 설정해야 합니다.';
            }
        } else {
            // 발급일 기준 방식
            if (empty($data['ct_days']) || $data['ct_days'] <= 0) {
                $errors[] = '유효 기간은 1일 이상이어야 합니다.';
            }
        }
    }

    return $errors;
}

/* 쿠폰 조회 함수 */
function get_coupon_by_id($id) {
    global $DB;

    $DB->where('idx', $id);
    return $DB->getOne('coupon_t');
}

/* 쿠폰 목록 조회 함수 */
function get_coupons($page = 1, $per_page = 10, $search = []) {
    global $DB;

    // 페이지네이션 설정
    $DB->pageLimit = $per_page;

    // 검색 조건 적용
    if (!empty($search['ct_title'])) {
        $DB->where('ct_title', '%' . $search['ct_title'] . '%', 'LIKE');
    }

    if (!empty($search['ct_code'])) {
        $DB->where('ct_code', '%' . $search['ct_code'] . '%', 'LIKE');
    }

    if (isset($search['ct_method']) && $search['ct_method'] !== '') {
        $DB->where('ct_method', $search['ct_method']);
    }

    if (isset($search['ct_show']) && $search['ct_show'] !== '') {
        $DB->where('ct_show', $search['ct_show']);
    }

    if (!empty($search['start_date'])) {
        $DB->where('ct_sdate', $search['start_date'], '>=');
    }

    if (!empty($search['end_date'])) {
        $DB->where('ct_edate', $search['end_date'], '<=');
    }

    // 정렬 설정
    $DB->orderBy('idx', 'DESC');

    // 쿠폰 목록 조회
    $coupons = $DB->paginate('coupon_t', $page);

    return [
        'coupons' => $coupons,
        'total_pages' => $DB->totalPages,
        'current_page' => $page,
        'total_rows' => $DB->totalCount
    ];
}


/* 쿠폰 생성 함수 */
function create_coupon($data) {
    global $DB;

    // 필수 필드 검증
    $errors = validate_coupon_data($data);
    if (!empty($errors)) {
        return [
            'success' => false,
            'errors' => $errors
        ];
    }

    // 쿠폰 코드 생성 또는 검증
    if (empty($data['ct_code'])) {
        $data['ct_code'] = generate_coupon_code();

        // 중복 코드 확인 및 재생성
        while (check_coupon_code_exists($data['ct_code'])) {
            $data['ct_code'] = generate_coupon_code();
        }
    } else {
        // 사용자 지정 쿠폰 코드 중복 확인
        if (check_coupon_code_exists($data['ct_code'])) {
            return [
                'success' => false,
                'errors' => ['이미 사용 중인 쿠폰 코드입니다.']
            ];
        }
    }

    try {
        // 트랜잭션 시작
        $DB->startTransaction();

        // 쿠폰 데이터 준비
        $coupon_data = [
            'ct_title' => $data['ct_title'],
            'ct_subtitle' => $data['ct_subtitle'] ?? '',
            'ct_code' => $data['ct_code'],
            'ct_method' => $data['ct_method'],
            'ct_target' => $data['ct_target'] ?? 0,
            'ct_target_name' => $data['ct_target_name'] ?? '',
            'ct_type1' => $data['ct_type1'],
            'ct_sdate' => $data['ct_sdate'] ?? null,
            'ct_edate' => $data['ct_edate'] ?? null,
            'ct_days' => $data['ct_days'] ?? 0,
            'ct_type2' => $data['ct_type2'],
            'ct_discount1' => $data['ct_discount1'],
            'ct_discount2' => $data['ct_discount2'] ?? 0,
            'ct_discount3' => $data['ct_discount3'] ?? 0,
            'ct_member_type' => $data['ct_member_type'] ?? '',
            'ct_member_idx' => $data['ct_member_idx'] ?? '',
            'ct_show' => $data['ct_show'] ?? 'Y',
            'ct_download' => 0,
            'ct_used' => 0,
            'reg_date' => $DB->now()
        ];

        // 쿠폰 데이터 삽입
        $coupon_id = $DB->insert('coupon_t', $coupon_data);

        if (!$coupon_id) {
            throw new Exception('쿠폰 생성 중 오류가 발생했습니다.');
        }

        $DB->commit();

        return [
            'success' => true,
            'message' => '쿠폰이 성공적으로 생성되었습니다.',
            'coupon_id' => $coupon_id
        ];
    } catch (Exception $e) {
        $DB->rollback();

        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}

/* 쿠폰 수정 함수 */
function update_coupon($id, $data) {
    global $DB;

    // 쿠폰 존재 여부 확인
    $coupon = get_coupon_by_id($id);
    if (!$coupon) {
        return [
            'success' => false,
            'message' => '존재하지 않는 쿠폰입니다.'
        ];
    }

    // 필수 필드 검증
    $errors = validate_coupon_data($data);
    if (!empty($errors)) {
        return [
            'success' => false,
            'errors' => $errors
        ];
    }

    // 쿠폰 코드 중복 확인 (변경된 경우)
    if ($data['ct_code'] != $coupon['ct_code'] && check_coupon_code_exists($data['ct_code'], $id)) {
        return [
            'success' => false,
            'errors' => ['이미 사용 중인 쿠폰 코드입니다.']
        ];
    }

    try {
        // 트랜잭션 시작
        $DB->startTransaction();

        // 쿠폰 데이터 준비
        $coupon_data = [
            'ct_title' => $data['ct_title'],
            'ct_subtitle' => $data['ct_subtitle'] ?? '',
            'ct_code' => $data['ct_code'],
            'ct_method' => $data['ct_method'],
            'ct_target' => $data['ct_target'] ?? 0,
            'ct_target_name' => $data['ct_target_name'] ?? '',
            'ct_type1' => $data['ct_type1'],
            'ct_sdate' => $data['ct_sdate'] ?? null,
            'ct_edate' => $data['ct_edate'] ?? null,
            'ct_days' => $data['ct_days'] ?? 0,
            'ct_type2' => $data['ct_type2'],
            'ct_discount1' => $data['ct_discount1'],
            'ct_discount2' => $data['ct_discount2'] ?? 0,
            'ct_discount3' => $data['ct_discount3'] ?? 0,
            'ct_member_type' => $data['ct_member_type'] ?? '',
            'ct_member_idx' => $data['ct_member_idx'] ?? '',
            'ct_show' => $data['ct_show'] ?? 'Y',
            'mod_date' => $DB->now()
        ];

        // 쿠폰 데이터 업데이트
        $DB->where('idx', $id);
        $result = $DB->update('coupon_t', $coupon_data);

        if (!$result) {
            throw new Exception('쿠폰 수정 중 오류가 발생했습니다.');
        }

        $DB->commit();

        return [
            'success' => true,
            'message' => '쿠폰이 성공적으로 수정되었습니다.'
        ];
    } catch (Exception $e) {
        $DB->rollback();

        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}

/* 쿠폰 삭제 함수 */
function delete_coupon($id) {
    global $DB;

    // 쿠폰 존재 여부 확인
    $coupon = get_coupon_by_id($id);
    if (!$coupon) {
        return [
            'success' => false,
            'message' => '존재하지 않는 쿠폰입니다.'
        ];
    }

    try {
        // 트랜잭션 시작
        $DB->startTransaction();

        // 이미 사용된 쿠폰인지 확인
        $DB->where('ct_idx', $id);
        $DB->where('cm_status', 2); // 2 = 사용됨
        $used_count = $DB->getValue('coupon_member_t', 'COUNT(*)');

        if ($used_count > 0) {
            return [
                'success' => false,
                'message' => '이미 사용된 쿠폰이 있어 삭제할 수 없습니다.'
            ];
        }

        // 발급된 쿠폰 삭제
        $DB->where('ct_idx', $id);
        $DB->delete('coupon_member_t');

        // 쿠폰 로그 삭제
        $DB->where('ct_idx', $id);
        $DB->delete('coupon_log_t');

        // 쿠폰 삭제
        $DB->where('idx', $id);
        $result = $DB->delete('coupon_t');

        if (!$result) {
            throw new Exception('쿠폰 삭제 중 오류가 발생했습니다.');
        }

        $DB->commit();

        return [
            'success' => true,
            'message' => '쿠폰이 성공적으로 삭제되었습니다.'
        ];
    } catch (Exception $e) {
        $DB->rollback();

        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}

/* 쿠폰 복제 함수 */
function duplicate_coupon($id) {
    global $DB;

    // 쿠폰 존재 여부 확인
    $coupon = get_coupon_by_id($id);
    if (!$coupon) {
        return [
            'success' => false,
            'message' => '존재하지 않는 쿠폰입니다.'
        ];
    }

    try {
        // 트랜잭션 시작
        $DB->startTransaction();

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
            throw new Exception('쿠폰 복제 중 오류가 발생했습니다.');
        }

        $DB->commit();

        return [
            'success' => true,
            'message' => '쿠폰이 성공적으로 복제되었습니다.',
            'coupon_id' => $new_id
        ];
    } catch (Exception $e) {
        $DB->rollback();

        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}

/* 쿠폰 상태 변경 함수 */
function change_coupon_status($id, $status) {
    global $DB;

    // 쿠폰 존재 여부 확인
    $coupon = get_coupon_by_id($id);
    if (!$coupon) {
        return [
            'success' => false,
            'message' => '존재하지 않는 쿠폰입니다.'
        ];
    }

    try {
        // 상태 변경
        $DB->where('idx', $id);
        $result = $DB->update('coupon_t', [
            'ct_show' => $status,
            'mod_date' => $DB->now()
        ]);

        if (!$result) {
            return [
                'success' => false,
                'message' => '쿠폰 상태 변경 중 오류가 발생했습니다.'
            ];
        }

        return [
            'success' => true,
            'message' => '쿠폰 상태가 성공적으로 변경되었습니다.'
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}

/* 쿠폰 일괄 삭제 함수 */
function bulk_delete_coupons($ids) {
    global $DB;

    if (empty($ids)) {
        return [
            'success' => false,
            'message' => '삭제할 쿠폰을 선택해주세요.'
        ];
    }

    try {
        // 트랜잭션 시작
        $DB->startTransaction();

        // 이미 사용된 쿠폰이 있는지 확인
        $DB->where('ct_idx', $ids, 'IN');
        $DB->where('cm_status', 2); // 2 = 사용됨
        $used_count = $DB->getValue('coupon_member_t', 'COUNT(*)');

        if ($used_count > 0) {
            return [
                'success' => false,
                'message' => '이미 사용된 쿠폰이 있어 일괄 삭제할 수 없습니다.'
            ];
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
            throw new Exception('쿠폰 일괄 삭제 중 오류가 발생했습니다.');
        }

        $DB->commit();

        return [
            'success' => true,
            'message' => count($ids) . '개의 쿠폰이 성공적으로 삭제되었습니다.'
        ];
    } catch (Exception $e) {
        $DB->rollback();

        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}


/* 쿠폰 발급 및 사용 관련 함수 */
/* 쿠폰 발급 함수 */
function issue_coupon($coupon_id, $member_id, $memo = '') {
    global $DB;

    // 쿠폰 존재 여부 확인
    $coupon = get_coupon_by_id($coupon_id);
    if (!$coupon) {
        return [
            'success' => false,
            'message' => '존재하지 않는 쿠폰입니다.'
        ];
    }

    // 회원 존재 여부 확인
    $DB->where('idx', $member_id);
    $member = $DB->getOne('member_t');
    if (!$member) {
        return [
            'success' => false,
            'message' => '존재하지 않는 회원입니다.'
        ];
    }

    // 이미 발급된 쿠폰인지 확인
    $DB->where('ct_idx', $coupon_id);
    $DB->where('mt_idx', $member_id);
    $DB->where('cm_status', [1, 2], 'IN'); // 1 = 사용 가능, 2 = 사용됨
    $exists = $DB->getValue('coupon_member_t', 'COUNT(*)');

    if ($exists > 0) {
        return [
            'success' => false,
            'message' => '이미 발급된 쿠폰입니다.'
        ];
    }

    try {
        // 트랜잭션 시작
        $DB->startTransaction();

        // 쿠폰 코드 생성
        $coupon_code = generate_coupon_code();

        // 중복 코드 확인 및 재생성
        while (true) {
            $DB->where('cm_code', $coupon_code);
            $exists = $DB->getValue('coupon_member_t', 'COUNT(*)');

            if ($exists == 0) {
                break;
            }

            $coupon_code = generate_coupon_code();
        }

        // 쿠폰 유효기간 설정
        $start_date = null;
        $end_date = null;

        if ($coupon['ct_type1'] == 1) {
            // 기간 설정 방식
            $start_date = $coupon['ct_sdate'];
            $end_date = $coupon['ct_edate'];
        } else {
            // 발급일 기준 방식
            $start_date = date('Y-m-d');
            $end_date = date('Y-m-d', strtotime("+{$coupon['ct_days']} days"));
        }

        // 쿠폰 발급 데이터
        $issue_data = [
            'ct_idx' => $coupon_id,
            'mt_idx' => $member_id,
            'cm_code' => $coupon_code,
            'cm_sdate' => $start_date,
            'cm_edate' => $end_date,
            'cm_status' => 1, // 1 = 사용 가능
            'reg_date' => date('Y-m-d H:i:s')
        ];

        // 쿠폰 발급
        $issue_id = $DB->insert('coupon_member_t', $issue_data);

        if (!$issue_id) {
            throw new Exception('쿠폰 발급 중 오류가 발생했습니다.');
        }

        // 쿠폰 발급 로그
        $log_data = [
            'ct_idx' => $coupon_id,
            'cm_idx' => $issue_id,
            'mt_idx' => $member_id,
            'cl_status' => 1, // 1 = 발급
            'cl_memo' => $memo ?: '관리자 수동 발급',
            'reg_date' => date('Y-m-d H:i:s')
        ];

        $DB->insert('coupon_log_t', $log_data);

        // 쿠폰 발급 카운트 증가
        $DB->where('idx', $coupon_id);
        $DB->update('coupon_t', ['ct_download' => $DB->inc(1)]);

        $DB->commit();

        return [
            'success' => true,
            'message' => '쿠폰이 성공적으로 발급되었습니다.',
            'coupon_code' => $coupon_code
        ];
    } catch (Exception $e) {
        $DB->rollback();

        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}

/* 쿠폰 발급 취소 함수 */
function cancel_coupon_issue($issue_id, $memo = '') {
    global $DB;

    // 발급된 쿠폰 존재 여부 확인
    $DB->where('idx', $issue_id);
    $issued_coupon = $DB->getOne('coupon_member_t');

    if (!$issued_coupon) {
        return [
            'success' => false,
            'message' => '존재하지 않는 발급 쿠폰입니다.'
        ];
    }

    // 이미 사용된 쿠폰인지 확인
    if ($issued_coupon['cm_status'] == 2) {
        return [
            'success' => false,
            'message' => '이미 사용된 쿠폰은 발급 취소할 수 없습니다.'
        ];
    }

    // 이미 취소된 쿠폰인지 확인
    if ($issued_coupon['cm_status'] == 3) {
        return [
            'success' => false,
            'message' => '이미 발급 취소된 쿠폰입니다.'
        ];
    }

    try {
        // 트랜잭션 시작
        $DB->startTransaction();

        // 쿠폰 발급 취소
        $DB->where('idx', $issue_id);
        $result = $DB->update('coupon_member_t', [
            'cm_status' => 3, // 3 = 발급 취소
            'mod_date' => date('Y-m-d H:i:s')
        ]);

        if (!$result) {
            throw new Exception('쿠폰 발급 취소 중 오류가 발생했습니다.');
        }

        // 쿠폰 발급 취소 로그
        $log_data = [
            'ct_idx' => $issued_coupon['ct_idx'],
            'cm_idx' => $issue_id,
            'mt_idx' => $issued_coupon['mt_idx'],
            'cl_status' => 3, // 3 = 발급 취소
            'cl_memo' => $memo ?: '관리자 발급 취소',
            'reg_date' => date('Y-m-d H:i:s')
        ];

        $DB->insert('coupon_log_t', $log_data);

        // 쿠폰 발급 카운트 감소
        $DB->where('idx', $issued_coupon['ct_idx']);
        $DB->update('coupon_t', ['ct_download' => $DB->dec(1)]);

        $DB->commit();

        return [
            'success' => true,
            'message' => '쿠폰 발급이 성공적으로 취소되었습니다.'
        ];
    } catch (Exception $e) {
        $DB->rollback();

        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}

/* 발급된 쿠폰 목록 조회 함수 */
function get_issued_coupons($page = 1, $per_page = 10, $search = []) {
    global $DB;

    // 페이지네이션 설정
    $DB->pageLimit = $per_page;

    // 조인 설정
    $DB->join('coupon_t c', 'cm.ct_idx = c.idx', 'LEFT');
    $DB->join('member_t m', 'cm.mt_idx = m.idx', 'LEFT');

    // 검색 조건 적용
    if (!empty($search['ct_title'])) {
        $DB->where('c.ct_title', '%' . $search['ct_title'] . '%', 'LIKE');
    }

    if (!empty($search['cm_code'])) {
        $DB->where('cm.cm_code', '%' . $search['cm_code'] . '%', 'LIKE');
    }

    if (!empty($search['mt_name'])) {
        $DB->where('m.mt_name', '%' . $search['mt_name'] . '%', 'LIKE');
    }

    if (!empty($search['mt_id'])) {
        $DB->where('m.mt_id', '%' . $search['mt_id'] . '%', 'LIKE');
    }

    if (isset($search['cm_status']) && $search['cm_status'] !== '') {
        $DB->where('cm.cm_status', $search['cm_status']);
    }

    if (!empty($search['start_date'])) {
        $DB->where('cm.reg_date', $search['start_date'] . ' 00:00:00', '>=');
    }

    if (!empty($search['end_date'])) {
        $DB->where('cm.reg_date', $search['end_date'] . ' 23:59:59', '<=');
    }

    // 정렬 설정
    $DB->orderBy('cm.idx', 'DESC');

    // 쿠폰 목록 조회
    $columns = [
        'cm.idx', 'cm.ct_idx', 'cm.mt_idx', 'cm.cm_code', 'cm.cm_sdate', 'cm.cm_edate',
        'cm.cm_status', 'cm.reg_date', 'cm.mod_date',
        'c.ct_title', 'c.ct_code', 'c.ct_method', 'c.ct_type2', 'c.ct_discount1',
        'm.mt_id', 'm.mt_name', 'm.mt_email', 'm.mt_phone'
    ];

    $issued_coupons = $DB->paginate('coupon_member_t cm', $page, $columns);

    // 추가 정보 설정
    foreach ($issued_coupons as &$coupon) {
        $coupon['cm_status_text'] = get_coupon_status_text($coupon['cm_status']);
        $coupon['ct_method_text'] = get_coupon_method_text($coupon['ct_method']);
        $coupon['ct_discount_type_text'] = get_coupon_discount_type_text($coupon['ct_type2']);
    }

    return [
        'coupons' => $issued_coupons,
        'total_pages' => $DB->totalPages,
        'current_page' => $page,
        'total_rows' => $DB->totalCount
    ];
}

/* 사용자 쿠폰 조회 함수 */
function get_user_coupons($member_id, $status = null) {
    global $DB;

    // 회원 존재 여부 확인
    $DB->where('idx', $member_id);
    $member = $DB->getOne('member_t');
    if (!$member) {
        return [
            'success' => false,
            'message' => '존재하지 않는 회원입니다.'
        ];
    }

    // 조인 설정
    $DB->join('coupon_t c', 'cm.ct_idx = c.idx', 'LEFT');

    // 회원 조건
    $DB->where('cm.mt_idx', $member_id);

    // 상태 조건
    if ($status !== null) {
        $DB->where('cm.cm_status', $status);
    }

    // 정렬 설정
    $DB->orderBy('cm.cm_status', 'ASC');
    $DB->orderBy('cm.cm_edate', 'ASC');

    // 쿠폰 목록 조회
    $columns = [
        'cm.idx', 'cm.ct_idx', 'cm.mt_idx', 'cm.cm_code', 'cm.cm_sdate', 'cm.cm_edate',
        'cm.cm_status', 'cm.reg_date', 'cm.mod_date',
        'c.ct_title', 'c.ct_subtitle', 'c.ct_code', 'c.ct_method', 'c.ct_type2',
        'c.ct_discount1', 'c.ct_discount2', 'c.ct_discount3', 'c.ct_target',
        'c.ct_target_name'
    ];

    $coupons = $DB->get('coupon_member_t cm', null, $columns);

    // 추가 정보 설정
    foreach ($coupons as &$coupon) {
        $coupon['cm_status_text'] = get_coupon_status_text($coupon['cm_status']);
        $coupon['ct_method_text'] = get_coupon_method_text($coupon['ct_method']);
        $coupon['ct_discount_type_text'] = get_coupon_discount_type_text($coupon['ct_type2']);
        $coupon['ct_target_text'] = get_coupon_target_text($coupon['ct_target']);

        // 현재 날짜 기준 상태 업데이트
        $today = date('Y-m-d');
        if ($coupon['cm_status'] == 1 && $coupon['cm_edate'] < $today) {
            $coupon['cm_status'] = 4; // 4 = 기간 만료
            $coupon['cm_status_text'] = get_coupon_status_text(4);
        }
    }

    return [
        'success' => true,
        'coupons' => $coupons,
        'total' => count($coupons)
    ];
}

/* 쿠폰 사용 함수 */
function use_coupon($issue_id, $order_id, $memo = '') {
    global $DB;

    // 발급된 쿠폰 존재 여부 확인
    $DB->where('idx', $issue_id);
    $issued_coupon = $DB->getOne('coupon_member_t');

    if (!$issued_coupon) {
        return [
            'success' => false,
            'message' => '존재하지 않는 발급 쿠폰입니다.'
        ];
    }

    // 쿠폰 상태 확인
    if ($issued_coupon['cm_status'] != 1) {
        $status_text = get_coupon_status_text($issued_coupon['cm_status']);
        return [
            'success' => false,
            'message' => "사용할 수 없는 쿠폰입니다. (상태: {$status_text})"
        ];
    }

    // 쿠폰 유효기간 확인
    $today = date('Y-m-d');
    if ($issued_coupon['cm_sdate'] > $today) {
        return [
            'success' => false,
            'message' => '아직 사용 기간이 시작되지 않은 쿠폰입니다.'
        ];
    }

    if ($issued_coupon['cm_edate'] < $today) {
        return [
            'success' => false,
            'message' => '유효기간이 만료된 쿠폰입니다.'
        ];
    }

    try {
        // 트랜잭션 시작
        $DB->startTransaction();

        // 쿠폰 사용 처리
        $DB->where('idx', $issue_id);
        $result = $DB->update('coupon_member_t', [
            'cm_status' => 2, // 2 = 사용됨
            'cm_order_idx' => $order_id,
            'mod_date' => date('Y-m-d H:i:s')
        ]);

        if (!$result) {
            throw new Exception('쿠폰 사용 처리 중 오류가 발생했습니다.');
        }

        // 쿠폰 사용 로그
        $log_data = [
            'ct_idx' => $issued_coupon['ct_idx'],
            'cm_idx' => $issue_id,
            'mt_idx' => $issued_coupon['mt_idx'],
            'cl_status' => 2, // 2 = 사용
            'cl_memo' => $memo ?: "주문번호: {$order_id}",
            'cl_order_idx' => $order_id,
            'reg_date' => date('Y-m-d H:i:s')
        ];

        $DB->insert('coupon_log_t', $log_data);

        // 쿠폰 사용 카운트 증가
        $DB->where('idx', $issued_coupon['ct_idx']);
        $DB->update('coupon_t', ['ct_used' => $DB->inc(1)]);

        $DB->commit();

        return [
            'success' => true,
            'message' => '쿠폰이 성공적으로 사용되었습니다.'
        ];
    } catch (Exception $e) {
        $DB->rollback();

        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}

/* 쿠폰 사용 취소 함수 */
function cancel_coupon_usage($issue_id, $memo = '') {
    global $DB;

    // 발급된 쿠폰 존재 여부 확인
    $DB->where('idx', $issue_id);
    $issued_coupon = $DB->getOne('coupon_member_t');

    if (!$issued_coupon) {
        return [
            'success' => false,
            'message' => '존재하지 않는 발급 쿠폰입니다.'
        ];
    }

    // 쿠폰 상태 확인
    if ($issued_coupon['cm_status'] != 2) {
        return [
            'success' => false,
            'message' => '사용된 쿠폰만 사용 취소할 수 있습니다.'
        ];
    }

    try {
        // 트랜잭션 시작
        $DB->startTransaction();

        // 쿠폰 사용 취소 처리
        $DB->where('idx', $issue_id);
        $result = $DB->update('coupon_member_t', [
            'cm_status' => 1, // 1 = 사용 가능
            'cm_order_idx' => 0,
            'mod_date' => date('Y-m-d H:i:s')
        ]);

        if (!$result) {
            throw new Exception('쿠폰 사용 취소 처리 중 오류가 발생했습니다.');
        }

        // 쿠폰 사용 취소 로그
        $log_data = [
            'ct_idx' => $issued_coupon['ct_idx'],
            'cm_idx' => $issue_id,
            'mt_idx' => $issued_coupon['mt_idx'],
            'cl_status' => 4, // 4 = 사용 취소
            'cl_memo' => $memo ?: '주문 취소로 인한 쿠폰 사용 취소',
            'cl_order_idx' => $issued_coupon['cm_order_idx'],
            'reg_date' => date('Y-m-d H:i:s')
        ];

        $DB->insert('coupon_log_t', $log_data);

        // 쿠폰 사용 카운트 감소
        $DB->where('idx', $issued_coupon['ct_idx']);
        $DB->update('coupon_t', ['ct_used' => $DB->dec(1)]);

        $DB->commit();

        return [
            'success' => true,
            'message' => '쿠폰 사용이 성공적으로 취소되었습니다.'
        ];
    } catch (Exception $e) {
        $DB->rollback();

        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}

/* 쿠폰 코드로 쿠폰 조회 함수 */
function get_coupon_by_code($code) {
    global $DB;

    // 쿠폰 코드 검색
    $DB->where('cm_code', $code);
    $DB->join('coupon_t c', 'cm.ct_idx = c.idx', 'LEFT');
    $DB->join('member_t m', 'cm.mt_idx = m.idx', 'LEFT');

    $columns = [
        'cm.idx', 'cm.ct_idx', 'cm.mt_idx', 'cm.cm_code', 'cm.cm_sdate', 'cm.cm_edate',
        'cm.cm_status', 'cm.cm_order_idx', 'cm.reg_date', 'cm.mod_date',
        'c.ct_title', 'c.ct_subtitle', 'c.ct_code', 'c.ct_method', 'c.ct_type2',
        'c.ct_discount1', 'c.ct_discount2', 'c.ct_discount3', 'c.ct_target',
        'c.ct_target_name',
        'm.mt_id', 'm.mt_name', 'm.mt_email', 'm.mt_phone'
    ];

    $coupon = $DB->getOne('coupon_member_t cm', $columns);

    if (!$coupon) {
        return [
            'success' => false,
            'message' => '존재하지 않는 쿠폰 코드입니다.'
        ];
    }

    // 추가 정보 설정
    $coupon['cm_status_text'] = get_coupon_status_text($coupon['cm_status']);
    $coupon['ct_method_text'] = get_coupon_method_text($coupon['ct_method']);
    $coupon['ct_discount_type_text'] = get_coupon_discount_type_text($coupon['ct_type2']);
    $coupon['ct_target_text'] = get_coupon_target_text($coupon['ct_target']);

    // 현재 날짜 기준 상태 업데이트
    $today = date('Y-m-d');
    if ($coupon['cm_status'] == 1 && $coupon['cm_edate'] < $today) {
        $coupon['cm_status'] = 4; // 4 = 기간 만료
        $coupon['cm_status_text'] = get_coupon_status_text(4);
    }

    return [
        'success' => true,
        'coupon' => $coupon
    ];
}

/* 쿠폰 로그 조회 함수 */
function get_coupon_logs($page = 1, $per_page = 10, $search = []) {
    global $DB;

    // 페이지네이션 설정
    $DB->pageLimit = $per_page;

    // 조인 설정
    $DB->join('coupon_t c', 'cl.ct_idx = c.idx', 'LEFT');
    $DB->join('coupon_member_t cm', 'cl.cm_idx = cm.idx', 'LEFT');
    $DB->join('member_t m', 'cl.mt_idx = m.idx', 'LEFT');

    // 검색 조건 적용
    if (!empty($search['ct_title'])) {
        $DB->where('c.ct_title', '%' . $search['ct_title'] . '%', 'LIKE');
    }

    if (!empty($search['cm_code'])) {
        $DB->where('cm.cm_code', '%' . $search['cm_code'] . '%', 'LIKE');
    }

    if (!empty($search['mt_name'])) {
        $DB->where('m.mt_name', '%' . $search['mt_name'] . '%', 'LIKE');
    }

    if (!empty($search['mt_id'])) {
        $DB->where('m.mt_id', '%' . $search['mt_id'] . '%', 'LIKE');
    }

    if (isset($search['cl_status']) && $search['cl_status'] !== '') {
        $DB->where('cl.cl_status', $search['cl_status']);
    }

    if (!empty($search['start_date'])) {
        $DB->where('cl.reg_date', $search['start_date'] . ' 00:00:00', '>=');
    }

    if (!empty($search['end_date'])) {
        $DB->where('cl.reg_date', $search['end_date'] . ' 23:59:59', '<=');
    }

    // 정렬 설정
    $DB->orderBy('cl.idx', 'DESC');

    // 쿠폰 로그 목록 조회
    $columns = [
        'cl.idx', 'cl.ct_idx', 'cl.cm_idx', 'cl.mt_idx', 'cl.cl_status',
        'cl.cl_memo', 'cl.cl_order_idx', 'cl.reg_date',
        'c.ct_title', 'c.ct_code',
        'cm.cm_code', 'cm.cm_sdate', 'cm.cm_edate',
        'm.mt_id', 'm.mt_name', 'm.mt_email', 'm.mt_phone'
    ];

    $logs = $DB->paginate('coupon_log_t cl', $page, $columns);

    // 추가 정보 설정
    foreach ($logs as &$log) {
        $log['cl_status_text'] = get_coupon_log_status_text($log['cl_status']);
    }

    return [
        'logs' => $logs,
        'total_pages' => $DB->totalPages,
        'current_page' => $page,
        'total_rows' => $DB->totalCount
    ];
}

/* 쿠폰 할인 계산 함수 */
function calculate_coupon_discount($coupon_id, $product_price, $product_id = null, $category_id = null) {
    global $DB;

    // 쿠폰 정보 조회
    $DB->where('idx', $coupon_id);
    $coupon = $DB->getOne('coupon_t');

    if (!$coupon) {
        return [
            'success' => false,
            'message' => '존재하지 않는 쿠폰입니다.'
        ];
    }

    // 쿠폰 대상 확인
    if ($coupon['ct_target'] > 0) {
        // 특정 카테고리 쿠폰
        if ($coupon['ct_target'] == 1 && $category_id !== null) {
            $target_categories = explode(',', $coupon['ct_target_name']);
            if (!in_array($category_id, $target_categories)) {
                return [
                    'success' => false,
                    'message' => '해당 카테고리에 적용할 수 없는 쿠폰입니다.'
                ];
            }
        }

        // 특정 상품 쿠폰
        if ($coupon['ct_target'] == 2 && $product_id !== null) {
            $target_products = explode(',', $coupon['ct_target_name']);
            if (!in_array($product_id, $target_products)) {
                return [
                    'success' => false,
                    'message' => '해당 상품에 적용할 수 없는 쿠폰입니다.'
                ];
            }
        }
    }

    // 할인 금액 계산
    $discount_amount = 0;

    if ($coupon['ct_type2'] == 0) {
        // 정액 할인
        $discount_amount = $coupon['ct_discount1'];
    } else {
        // 정률 할인
        $discount_amount = floor($product_price * ($coupon['ct_discount1'] / 100));

        // 최대 할인 금액 제한
        if ($coupon['ct_discount2'] > 0 && $discount_amount > $coupon['ct_discount2']) {
            $discount_amount = $coupon['ct_discount2'];
        }
    }

    // 최소 주문 금액 확인
    if ($coupon['ct_discount3'] > 0 && $product_price < $coupon['ct_discount3']) {
        return [
            'success' => false,
            'message' => '최소 주문 금액(' . number_format($coupon['ct_discount3']) . '원)을 충족하지 않습니다.'
        ];
    }

    // 할인 금액이 상품 가격보다 클 경우 조정
    if ($discount_amount > $product_price) {
        $discount_amount = $product_price;
    }

    return [
        'success' => true,
        'discount_amount' => $discount_amount,
        'final_price' => $product_price - $discount_amount,
        'coupon' => $coupon
    ];
}

/* 쿠폰 일괄 발급 함수 */
function bulk_issue_coupons($coupon_id, $member_ids, $memo = '') {
    global $DB;

    // 쿠폰 존재 여부 확인
    $coupon = get_coupon_by_id($coupon_id);
    if (!$coupon) {
        return [
            'success' => false,
            'message' => '존재하지 않는 쿠폰입니다.'
        ];
    }

    if (empty($member_ids)) {
        return [
            'success' => false,
            'message' => '발급할 회원을 선택해주세요.'
        ];
    }

    try {
        // 트랜잭션 시작
        $DB->startTransaction();

        $success_count = 0;
        $fail_count = 0;
        $results = [];

        foreach ($member_ids as $member_id) {
            // 회원 존재 여부 확인
            $DB->where('idx', $member_id);
            $member = $DB->getOne('member_t');

            if (!$member) {
                $results[] = [
                    'member_id' => $member_id,
                    'success' => false,
                    'message' => '존재하지 않는 회원입니다.'
                ];
                $fail_count++;
                continue;
            }

            // 이미 발급된 쿠폰인지 확인
            $DB->where('ct_idx', $coupon_id);
            $DB->where('mt_idx', $member_id);
            $DB->where('cm_status', [1, 2], 'IN'); // 1 = 사용 가능, 2 = 사용됨
            $exists = $DB->getValue('coupon_member_t', 'COUNT(*)');

            if ($exists > 0) {
                $results[] = [
                    'member_id' => $member_id,
                    'member_name' => $member['mt_name'],
                    'success' => false,
                    'message' => '이미 발급된 쿠폰입니다.'
                ];
                $fail_count++;
                continue;
            }

            // 쿠폰 코드 생성
            $coupon_code = generate_coupon_code();

            // 중복 코드 확인 및 재생성
            while (true) {
                $DB->where('cm_code', $coupon_code);
                $exists = $DB->getValue('coupon_member_t', 'COUNT(*)');

                if ($exists == 0) {
                    break;
                }

                $coupon_code = generate_coupon_code();
            }

            // 쿠폰 유효기간 설정
            $start_date = null;
            $end_date = null;

            if ($coupon['ct_type1'] == 1) {
                // 기간 설정 방식
                $start_date = $coupon['ct_sdate'];
                $end_date = $coupon['ct_edate'];
            } else {
                // 발급일 기준 방식
                $start_date = date('Y-m-d');
                $end_date = date('Y-m-d', strtotime("+{$coupon['ct_days']} days"));
            }

            // 쿠폰 발급 데이터
            $issue_data = [
                'ct_idx' => $coupon_id,
                'mt_idx' => $member_id,
                'cm_code' => $coupon_code,
                'cm_sdate' => $start_date,
                'cm_edate' => $end_date,
                'cm_status' => 1, // 1 = 사용 가능
                'reg_date' => date('Y-m-d H:i:s')
            ];

            // 쿠폰 발급
            $issue_id = $DB->insert('coupon_member_t', $issue_data);

            if (!$issue_id) {
                $results[] = [
                    'member_id' => $member_id,
                    'member_name' => $member['mt_name'],
                    'success' => false,
                    'message' => '쿠폰 발급 중 오류가 발생했습니다.'
                ];
                $fail_count++;
                continue;
            }

            // 쿠폰 발급 로그
            $log_data = [
                'ct_idx' => $coupon_id,
                'cm_idx' => $issue_id,
                'mt_idx' => $member_id,
                'cl_status' => 1, // 1 = 발급
                'cl_memo' => $memo ?: '관리자 일괄 발급',
                'reg_date' => date('Y-m-d H:i:s')
            ];

            $DB->insert('coupon_log_t', $log_data);

            $results[] = [
                'member_id' => $member_id,
                'member_name' => $member['mt_name'],
                'success' => true,
                'message' => '쿠폰이 성공적으로 발급되었습니다.',
                'coupon_code' => $coupon_code
            ];
            $success_count++;
        }

        // 쿠폰 발급 카운트 증가
        if ($success_count > 0) {
            $DB->where('idx', $coupon_id);
            $DB->update('coupon_t', ['ct_download' => $DB->inc($success_count)]);
        }

        $DB->commit();

        return [
            'success' => true,
            'message' => "총 {$success_count}개의 쿠폰이 발급되었습니다. (실패: {$fail_count}개)",
            'results' => $results,
            'success_count' => $success_count,
            'fail_count' => $fail_count
        ];
    } catch (Exception $e) {
        $DB->rollback();

        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}

/* 쿠폰 통계 함수 */
/* 쿠폰 통계 조회 함수 */
function get_coupon_statistics() {
    global $DB;

    $today = date('Y-m-d');

    // 총 쿠폰 수
    $total_coupons = $DB->getValue('coupon_t', 'COUNT(*)');

    // 활성화된 쿠폰 수
    $DB->where('ct_show', 'Y');
    $active_coupons = $DB->getValue('coupon_t', 'COUNT(*)');

    // 발급된 쿠폰 수
    $total_issued = $DB->getValue('coupon_member_t', 'COUNT(*)');

    // 사용된 쿠폰 수
    $DB->where('cm_status', 2);
    $used_coupons = $DB->getValue('coupon_member_t', 'COUNT(*)');

    // 만료된 쿠폰 수
    $DB->where('cm_status', 1);
    $DB->where('cm_edate', $today, '<');
    $expired_coupons = $DB->getValue('coupon_member_t', 'COUNT(*)');

    // 사용 가능한 쿠폰 수
    $DB->where('cm_status', 1);
    $DB->where('cm_sdate', $today, '<=');
    $DB->where('cm_edate', $today, '>=');
    $available_coupons = $DB->getValue('coupon_member_t', 'COUNT(*)');

    // 쿠폰 사용 금액 합계
    $DB->join('coupon_t c', 'cm.ct_idx = c.idx', 'LEFT');
    $DB->where('cm.cm_status', 2);
    $total_discount = $DB->getValue('coupon_member_t cm', 'SUM(c.ct_discount1)');

    return [
        'total_coupons' => $total_coupons,
        'active_coupons' => $active_coupons,
        'total_issued' => $total_issued,
        'used_coupons' => $used_coupons,
        'expired_coupons' => $expired_coupons,
        'available_coupons' => $available_coupons,
        'total_discount' => $total_discount ?: 0
    ];
}

/* 회원별 쿠폰 통계 조회 함수 */
function get_member_coupon_statistics($member_id) {
    global $DB;

    $today = date('Y-m-d');

    // 총 발급된 쿠폰 수
    $DB->where('mt_idx', $member_id);
    $total_issued = $DB->getValue('coupon_member_t', 'COUNT(*)');

    // 사용한 쿠폰 수
    $DB->where('mt_idx', $member_id);
    $DB->where('cm_status', 2);
    $used_coupons = $DB->getValue('coupon_member_t', 'COUNT(*)');

    // 만료된 쿠폰 수
    $DB->where('mt_idx', $member_id);
    $DB->where('cm_status', 1);
    $DB->where('cm_edate', $today, '<');
    $expired_coupons = $DB->getValue('coupon_member_t', 'COUNT(*)');

    // 사용 가능한 쿠폰 수
    $DB->where('mt_idx', $member_id);
    $DB->where('cm_status', 1);
    $DB->where('cm_sdate', $today, '<=');
    $DB->where('cm_edate', $today, '>=');
    $available_coupons = $DB->getValue('coupon_member_t', 'COUNT(*)');

    // 쿠폰 사용 금액 합계
    $DB->join('coupon_t c', 'cm.ct_idx = c.idx', 'LEFT');
    $DB->where('cm.mt_idx', $member_id);
    $DB->where('cm.cm_status', 2);
    $total_discount = $DB->getValue('coupon_member_t cm', 'SUM(c.ct_discount1)');

    return [
        'total_issued' => $total_issued,
        'used_coupons' => $used_coupons,
        'expired_coupons' => $expired_coupons,
        'available_coupons' => $available_coupons,
        'total_discount' => $total_discount ?: 0
    ];
}

/* 추가 쿠폰 유틸리티 함수 */
/* 쿠폰 자동 발급 함수 (회원 가입 시) */
function auto_issue_coupon_on_signup($member_id) {
    global $DB;

    // 자동 발급 설정된 쿠폰 조회
    $DB->where('ct_auto_issue', 'Y');
    $DB->where('ct_auto_type', 1); // 1 = 회원 가입 시
    $DB->where('ct_show', 'Y');
    $auto_coupons = $DB->get('coupon_t');

    if (empty($auto_coupons)) {
        return [
            'success' => true,
            'message' => '자동 발급 설정된 쿠폰이 없습니다.',
            'issued_count' => 0
        ];
    }

    $issued_count = 0;
    $results = [];

    foreach ($auto_coupons as $coupon) {
        $result = issue_coupon($coupon['idx'], $member_id, '회원 가입 자동 발급');
        $results[] = $result;

        if ($result['success']) {
            $issued_count++;
        }
    }

    return [
        'success' => true,
        'message' => "{$issued_count}개의 쿠폰이 자동 발급되었습니다.",
        'issued_count' => $issued_count,
        'results' => $results
    ];
}

/* 쿠폰 자동 발급 함수 (생일 축하) */
function auto_issue_birthday_coupons() {
    global $DB;

    // 오늘이 생일인 회원 조회
    $today = date('m-d');
    $DB->where("DATE_FORMAT(mt_birth, '%m-%d')", $today);
    $DB->where('mt_status', 'Y');
    $birthday_members = $DB->get('member_t');

    if (empty($birthday_members)) {
        return [
            'success' => true,
            'message' => '오늘 생일인 회원이 없습니다.',
            'issued_count' => 0
        ];
    }

    // 생일 축하 자동 발급 설정된 쿠폰 조회
    $DB->where('ct_auto_issue', 'Y');
    $DB->where('ct_auto_type', 2); // 2 = 생일 축하
    $DB->where('ct_show', 'Y');
    $birthday_coupons = $DB->get('coupon_t');

    if (empty($birthday_coupons)) {
        return [
            'success' => true,
            'message' => '생일 축하 자동 발급 설정된 쿠폰이 없습니다.',
            'issued_count' => 0
        ];
    }

    $issued_count = 0;
    $results = [];

    foreach ($birthday_members as $member) {
        foreach ($birthday_coupons as $coupon) {
            $result = issue_coupon($coupon['idx'], $member['idx'], '생일 축하 자동 발급');

            if ($result['success']) {
                $issued_count++;
                $results[] = [
                    'member_id' => $member['idx'],
                    'member_name' => $member['mt_name'],
                    'coupon_id' => $coupon['idx'],
                    'coupon_title' => $coupon['ct_title'],
                    'coupon_code' => $result['coupon_code']
                ];
            }
        }
    }

    return [
        'success' => true,
        'message' => "총 {$issued_count}개의 생일 축하 쿠폰이 발급되었습니다.",
        'issued_count' => $issued_count,
        'results' => $results
    ];
}

/* 만료 예정 쿠폰 알림 함수 */
function notify_expiring_coupons($days_before = 7) {
    global $DB;

    $target_date = date('Y-m-d', strtotime("+{$days_before} days"));

    // 만료 예정 쿠폰 조회
    $DB->where('cm_status', 1); // 사용 가능한 쿠폰만
    $DB->where('cm_edate', $target_date);
    $DB->join('member_t m', 'cm.mt_idx = m.idx', 'LEFT');
    $DB->join('coupon_t c', 'cm.ct_idx = c.idx', 'LEFT');

    $columns = [
        'cm.idx', 'cm.ct_idx', 'cm.mt_idx', 'cm.cm_code', 'cm.cm_edate',
        'c.ct_title', 'c.ct_type2', 'c.ct_discount1', 'c.ct_discount2',
        'm.mt_id', 'm.mt_name', 'm.mt_email', 'm.mt_phone'
    ];

    $expiring_coupons = $DB->get('coupon_member_t cm', null, $columns);

    if (empty($expiring_coupons)) {
        return [
            'success' => true,
            'message' => '만료 예정인 쿠폰이 없습니다.',
            'count' => 0
        ];
    }

    $notifications = [];

    foreach ($expiring_coupons as $coupon) {
        // 여기서 실제 알림 로직 구현 (이메일, SMS 등)
        // 예시: send_email($coupon['mt_email'], '쿠폰 만료 예정 알림', '쿠폰이 곧 만료됩니다.');

        $notifications[] = [
            'member_id' => $coupon['mt_idx'],
            'member_name' => $coupon['mt_name'],
            'member_email' => $coupon['mt_email'],
            'member_phone' => $coupon['mt_phone'],
            'coupon_id' => $coupon['ct_idx'],
            'coupon_title' => $coupon['ct_title'],
            'coupon_code' => $coupon['cm_code'],
            'expiry_date' => $coupon['cm_edate']
        ];
    }

    return [
        'success' => true,
        'message' => count($expiring_coupons) . '개의 만료 예정 쿠폰 알림이 발송되었습니다.',
        'count' => count($expiring_coupons),
        'notifications' => $notifications
    ];
}

/* 만료된 쿠폰 상태 업데이트 함수 */
function update_expired_coupons() {
    global $DB;

    $today = date('Y-m-d');

    // 만료된 쿠폰 조회
    $DB->where('cm_status', 1); // 사용 가능한 쿠폰만
    $DB->where('cm_edate', $today, '<');
    $expired_count = $DB->getValue('coupon_member_t', 'COUNT(*)');

    if ($expired_count == 0) {
        return [
            'success' => true,
            'message' => '만료된 쿠폰이 없습니다.',
            'updated_count' => 0
        ];
    }

    try {
        // 트랜잭션 시작
        $DB->startTransaction();

        // 만료된 쿠폰 상태 업데이트
        $DB->where('cm_status', 1);
        $DB->where('cm_edate', $today, '<');
        $result = $DB->update('coupon_member_t', [
            'cm_status' => 4, // 4 = 기간 만료
            'mod_date' => date('Y-m-d H:i:s')
        ]);

        if (!$result) {
            throw new Exception('만료된 쿠폰 상태 업데이트 중 오류가 발생했습니다.');
        }

        // 만료된 쿠폰 로그 추가
        $DB->where('cm_status', 4);
        $DB->where('cm_edate', $today, '<');
        $expired_coupons = $DB->get('coupon_member_t', null, ['idx', 'ct_idx', 'mt_idx']);

        foreach ($expired_coupons as $coupon) {
            $log_data = [
                'ct_idx' => $coupon['ct_idx'],
                'cm_idx' => $coupon['idx'],
                'mt_idx' => $coupon['mt_idx'],
                'cl_status' => 5, // 5 = 기간 만료
                'cl_memo' => '유효기간 만료',
                'reg_date' => date('Y-m-d H:i:s')
            ];

            $DB->insert('coupon_log_t', $log_data);
        }

        $DB->commit();

        return [
            'success' => true,
            'message' => "{$expired_count}개의 만료된 쿠폰 상태가 업데이트되었습니다.",
            'updated_count' => $expired_count
        ];
    } catch (Exception $e) {
        $DB->rollback();

        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}

/* 쿠폰 유효성 검사 함수 */
function validate_coupon($coupon_code, $member_id, $product_price = 0, $product_id = null, $category_id = null) {
    global $DB;

    // 쿠폰 코드로 쿠폰 조회
    $result = get_coupon_by_code($coupon_code);

    if (!$result['success']) {
        return $result;
    }

    $coupon = $result['coupon'];

    // 회원 일치 여부 확인
    if ($coupon['mt_idx'] != $member_id) {
        return [
            'success' => false,
            'message' => '해당 회원에게 발급된 쿠폰이 아닙니다.'
        ];
    }

    // 쿠폰 상태 확인
    if ($coupon['cm_status'] != 1) {
        return [
            'success' => false,
            'message' => '사용할 수 없는 쿠폰입니다. (상태: ' . $coupon['cm_status_text'] . ')'
        ];
    }

    // 쿠폰 유효기간 확인
    $today = date('Y-m-d');
    if ($coupon['cm_sdate'] > $today) {
        return [
            'success' => false,
            'message' => '아직 사용 기간이 시작되지 않은 쿠폰입니다.'
        ];
    }

    if ($coupon['cm_edate'] < $today) {
        return [
            'success' => false,
            'message' => '유효기간이 만료된 쿠폰입니다.'
        ];
    }

    // 쿠폰 대상 확인
    if ($coupon['ct_target'] > 0) {
        // 특정 카테고리 쿠폰
        if ($coupon['ct_target'] == 1 && $category_id !== null) {
            $target_categories = explode(',', $coupon['ct_target_name']);
            if (!in_array($category_id, $target_categories)) {
                return [
                    'success' => false,
                    'message' => '해당 카테고리에 적용할 수 없는 쿠폰입니다.'
                ];
            }
        }

        // 특정 상품 쿠폰
        if ($coupon['ct_target'] == 2 && $product_id !== null) {
            $target_products = explode(',', $coupon['ct_target_name']);
            if (!in_array($product_id, $target_products)) {
                return [
                    'success' => false,
                    'message' => '해당 상품에 적용할 수 없는 쿠폰입니다.'
                ];
            }
        }
    }

    // 최소 주문 금액 확인
    if ($coupon['ct_discount3'] > 0 && $product_price < $coupon['ct_discount3']) {
        return [
            'success' => false,
            'message' => '최소 주문 금액(' . number_format($coupon['ct_discount3']) . '원)을 충족하지 않습니다.'
        ];
    }

    // 할인 금액 계산
    $discount_amount = 0;

    if ($coupon['ct_type2'] == 0) {
        // 정액 할인
        $discount_amount = $coupon['ct_discount1'];
    } else {
        // 정률 할인
        $discount_amount = floor($product_price * ($coupon['ct_discount1'] / 100));

        // 최대 할인 금액 제한
        if ($coupon['ct_discount2'] > 0 && $discount_amount > $coupon['ct_discount2']) {
            $discount_amount = $coupon['ct_discount2'];
        }
    }

    // 할인 금액이 상품 가격보다 클 경우 조정
    if ($discount_amount > $product_price) {
        $discount_amount = $product_price;
    }

    return [
        'success' => true,
        'message' => '유효한 쿠폰입니다.',
        'coupon' => $coupon,
        'discount_amount' => $discount_amount,
        'final_price' => $product_price - $discount_amount
    ];
}
