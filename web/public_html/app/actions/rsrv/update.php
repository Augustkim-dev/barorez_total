<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
header('Content-Type: application/json; charset=utf-8');

$act = $_POST['act'] ?? '';

if ($act === 'save_session') {
    try {
        // 입력값
        $date   = trim($_POST['date'] ?? '');
        $time   = trim($_POST['time'] ?? '');
        $people = (int)($_POST['people'] ?? 1);

        $name  = trim($_POST['reserver_name'] ?? '');
        $phone = trim($_POST['reserver_phone'] ?? '');

        // 기본 검증(최소)
        if ($date === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            throw new Exception('예약 날짜가 올바르지 않습니다.');
        }
        if ($time === '' || !preg_match('/^\d{2}:\d{2}$/', $time)) {
            throw new Exception('예약 시간이 올바르지 않습니다.');
        }
        if ($people < 1) $people = 1;

        if ($name === '') {
            throw new Exception('예약자명을 입력해주세요.');
        }

        // 휴대폰: 숫자만 저장
        $phoneDigits = preg_replace('/\D+/', '', $phone);
        if ($phoneDigits === '' || strlen($phoneDigits) < 9) {
            throw new Exception('휴대폰번호를 올바르게 입력해주세요.');
        }

        // ✅ 예약자 정보만 세션 저장
        $_SESSION['reservation_form'] = [
            'rv_name'   => $name,
            'rv_hp'     => $phoneDigits,
            'rv_date'   => $date,
            'rv_time'   => $time,      // 필요하면 HH:MM:SS로 바꿔도 됨
            'rv_people' => $people,
            'saved_at'  => date('Y-m-d H:i:s'),
        ];

        echo json_encode([
            'success' => true,
            'message' => '예약자 정보가 저장되었습니다.',
            'data' => [
                'rv_name'   => $name,
                'rv_hp'     => $phoneDigits,
                'rv_date'   => $date,
                'rv_time'   => $time,
                'rv_people' => $people,
                'saved_at'  => date('Y-m-d H:i:s'),
            ]
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

else if ($act === 'clear_session') {
    unset($_SESSION['reservation_form']);
    echo json_encode(['success' => true, 'message' => '예약자 세션이 초기화되었습니다.'], JSON_UNESCAPED_UNICODE);
    exit;
}

else if ($act === 'cancel_reservation') {
    try {
        // 보안: 로그인 상태 또는 세션 검증 필요시 추가 가능
        // 지금은 ot_number로 본인 확인만 간단히
        $rv_idx = (int)$_POST['rv_idx'];
        // 현재 시간
        $now = date('Y-m-d H:i:s');
        $payPrice = 0;
        if(!$rv_idx) {
            $ot_number = trim($_POST['ot_number'] ?? '');
            if ($ot_number === '') {
                throw new Exception('주문번호가 필요합니다.');
            }

            // 1) orders_t에서 해당 주문 조회
            $DB->where('ot_number', $ot_number);
            $order = $DB->getOne('orders_t');

            if (!$order) {
                throw new Exception('주문을 찾을 수 없습니다.');
            }

            // 이미 취소된 주문인지 확인
            if ($order['ot_status'] === 'CANCELLED') {
                throw new Exception('이미 취소된 주문입니다.');
            }

            $DB->where('merchant_uid', $ot_number);
            $payRow = $DB->getOne('payments_t', ['imp_uid', 'merchant_uid']);

            if (!$payRow || empty($payRow['imp_uid'])) {
                throw new Exception('결제 paymentId(imp_uid)가 없습니다.');
            }
            // 결제 완료된 주문만 취소 가능 (UNPAID는 아직 결제 안 된 거라 취소 의미 없음)
//        if ($order['ot_pay_status'] !== 'PAID') {
//            throw new Exception('결제 완료된 주문만 취소할 수 있습니다.');
//        }

            $DB->startTransaction();

            // 주문 상태 취소로 변경
            $DB->where('idx', $order['idx']);
            $updateOrder = $DB->update('orders_t', [
                'ot_status' => 'CANCELLED',
                'ot_cancel' => $now,
                'ot_udate' => $now
            ]);

            if (!$updateOrder) {
                throw new Exception('주문 취소 처리 중 오류가 발생했습니다.');
            }

            // 예약이 연결되어 있으면 예약도 취소
            $rv_idx = (int)$order['rv_idx'];
        }

        if ($rv_idx > 0) {
            $DB->where('idx', $rv_idx);
            $rsv = $DB->getOne('reservation_t');

            if ($rsv) {
                if ($rsv['rv_status'] === 'CANCELLED') {
                    // 이미 취소된 예약이면 무시
                } elseif ($rsv['rv_status'] === 'ARRIVED') {
                    throw new Exception('이미 방문 완료된 예약은 취소할 수 없습니다.');
                } else {
                    $DB->where('idx', $rv_idx);
                    $updateRsv = $DB->update('reservation_t', [
                        'rv_status' => 'CANCELLED',
                        'rv_udate'  => $now
                    ]);

                    if (!$updateRsv) {
                        throw new Exception('예약 취소 처리 중 오류가 발생했습니다.');
                    }

                    $DB->where('sh_idx', $rsv['sh_idx']);
                    $rsvMsg = $DB->getOne('shop_reserve_setting_t');

                    $DB->where('rs_idx',$rsvMsg['idx']);
                    $rsvPrice = $DB->getOne('shop_reserve_penalty_t');

                    $today = new DateTime(date('Y-m-d'));
                    $reserveDate = new DateTime($rsv['rv_date']);

                    $diffDays = (int)$today->diff($reserveDate)->format('%r%a');

                    $refundLimitDays = (int)$rsvPrice['rp_free_cancel_before_min'] / 1440;

                    if ($diffDays >= $refundLimitDays) {
                        $payPrice = 0;
                    } else {
                        // 부분 환불
                        if ($rsvPrice['rp_type'] === 'FIXED') {
                            $payPrice = $rsvPrice['rp_value'];
                        } else {
                            $payPrice = $order['ot_total_price'] / $rsvPrice['rp_value'];
                        }
                    }
                }
            }
        }

        if($ot_number) {
            $paymentId = (string)$payRow['imp_uid'];
            if($order['ot_total_price'] > $payPrice) {
                $res = cancelPortonePayment($paymentId, '고객 요청', $payPrice);
            }
        }

        $DB->commit();

        echo json_encode([
            'success' => true,
            'message' => '예약이 취소되었습니다.',
            'data' => [
                'ot_number' => $ot_number,
                'rv_idx' => $rv_idx,
                'cancelled_at'  => $now,
                'is_reservation'=> ($rv_idx > 0),
                'paymentId' => $paymentId,
                'res'   => $res,
                'test' => $payPrice,
            ]
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
else if ($act === 'create_reservation') {
    try {
        // 세션에서 예약 정보 가져오기 (프론트에서 save_session 먼저 호출했음)
        $form = $_SESSION['reservation_form'] ?? [];

        $date   = $_POST['rv_date']   ?? $form['rv_date']   ?? '';
        $time   = $_POST['rv_time']   ?? $form['rv_time']   ?? '';
        $people = (int)($_POST['rv_people'] ?? $form['rv_people'] ?? 1);
        $name   = trim($_POST['rv_name'] ?? $form['rv_name'] ?? '');
        $hp     = $_POST['rv_hp']     ?? $form['rv_hp']     ?? '';

        if (!$date || !$time || !$name || !$hp || $people < 1) {
            throw new Exception('예약 정보가 없습니다. 다시 시도해주세요.');
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            throw new Exception('예약 날짜가 올바르지 않습니다.');
        }
        if (!preg_match('/^\d{2}:\d{2}$/', $time)) {
            throw new Exception('예약 시간이 올바르지 않습니다.');
        }
        if ($people < 1 || $people > 50) { // 인원 제한 (필요시 조정)
            throw new Exception('예약 인원이 올바르지 않습니다.');
        }
        if ($name === '') {
            throw new Exception('예약자명을 입력해주세요.');
        }
        if (strlen($hp) < 9) {
            throw new Exception('휴대폰번호를 확인해주세요.');
        }

        // 매장 정보 (현재 선택된 매장 - 세션 또는 GET으로 전달)
        // 예: 매장 상세 페이지에서 예약 들어오면 sh_idx를 세션이나 GET으로 가지고 있음
        $sh_idx = (int)($_SESSION['current_sh_idx'] ?? $_POST['sh_idx'] ?? 0);
        if ($sh_idx <= 0) {
            throw new Exception('매장 정보를 찾을 수 없습니다.');
        }

        // 회원 여부
        $mt_idx = (int)($_SESSION['mng']['mt_idx'] ?? 0); // 로그인 세션 예시

        // 중복 예약 방지 (같은 날짜/시간/매장에 이미 PENDING 이상인 예약 있는지)
        $DB->where('sh_idx', $sh_idx);
        $DB->where('rv_date', $date);
        $DB->where('rv_time', $time . ':00'); // time 필드가 TIME 타입이므로 초 추가
        $DB->where('mt_idx', $mt_idx);
        $DB->where('rv_status', ['PENDING', 'CONFIRMED', 'ARRIVED'], 'IN');
        $existing = $DB->getOne('reservation_t', 'COUNT(*) as cnt');
        if (($existing['cnt'] ?? 0) > 0) {
            throw new Exception('해당 시간대는 이미 예약이 접수되었습니다.');
        }

        if($mt_idx < 0){
            $ymd = date('YmdHis');
        }else{
            $ymd = date('Ymd');
        }
        $DB->where('sh_idx', $sh_idx);
        $DB->where('mt_idx', $mt_idx);
        $row = $DB->getOne('reservation_t', 'COUNT(*) as cnt');

        $rv_number = $ymd.$sh_idx.$mt_idx.$row['cnt'];

        // 예약 INSERT
        $insertData = [
            'sh_idx'    => $sh_idx,
            'mt_idx'    => $mt_idx > 0 ? $mt_idx : null,
            'rv_number' => $rv_number,
            'rv_name'   => $name,
            'rv_hp'     => $hp,
            'rv_date'   => $date,
            'rv_time'   => $time . ':00',
            'rv_people' => $people,
            'rv_type'   => 'VISIT',           // 현장 방문 예약
            'rv_status' => 'PENDING',         // 대기
            'rv_memo'   => null,
            'ot_idx'    => null,              // 선결제 없음
            'rv_wdate'  => $DB->now(),
        ];

        $rv_idx = $DB->insert('reservation_t', $insertData);
        if (!$rv_idx) {
            throw new Exception('예약 등록에 실패했습니다. 다시 시도해주세요.');
        }

        // 예약 성공 후 세션 정리 (필요시)
        unset($_SESSION['reservation_form']);

        // 응답
        echo json_encode([
            'success' => true,
            'message' => '예약이 접수되었습니다.',
            'data' => [
                'rv_idx'    => $rv_idx,
                'rv_date'   => $date,
                'rv_time'   => $time,
                'rv_people' => $people,
                'sh_idx'    => $sh_idx,
            ]
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
else if ($act === 'check_reserved_count') {
    try {
        $date   = trim($_POST['date'] ?? '');
        $time   = trim($_POST['time'] ?? '');
        $sh_idx = (int)($_POST['sh_idx'] ?? 0);

        if ($date === '' || $time === '' || $sh_idx <= 0) {
            throw new Exception('필수 파라미터 누락');
        }

        // 해당 날짜+시간에 PENDING/CONFIRMED/ARRIVED 상태 예약 수
        $DB->where('sh_idx', $sh_idx);
        $DB->where('rv_date', $date);
        $DB->where('rv_time', $time . ':00'); // TIME 타입이라 초 추가
        $DB->where('rv_status', ['PENDING', 'CONFIRMED', 'ARRIVED'], 'IN');
        $count = $DB->getValue('reservation_t', 'COUNT(*)');

        echo json_encode([
            'success' => true,
            'data' => ['count' => (int)$count]
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
else {
    echo json_encode(['success' => false, 'message' => '알 수 없는 요청입니다.'], JSON_UNESCAPED_UNICODE);
    exit;
}
