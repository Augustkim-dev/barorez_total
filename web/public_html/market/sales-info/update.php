<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";

header('Content-Type: application/json; charset=utf-8');

$sh_idx = (int)($_SESSION['current_sh_idx'] ?? 0);
$act    = $_POST['act'] ?? '';

if ($sh_idx <= 0) {
    echo json_encode(['success'=>false, 'message'=>'매장 정보가 없습니다.'], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($act === 'get_settle_detail') {
    try {
        $st_number = trim($_POST['st_number'] ?? '');

        if ($st_number === '') {
            throw new Exception('정산번호(st_number)가 없습니다.');
        }

        // 1. settle_t에서 정산 정보 가져오기
        $DB->where('st_number', $st_number);
        $DB->where('sh_idx', $sh_idx);
        $settle = $DB->getOne('settle_t');

        if (!$settle) {
            throw new Exception('해당 정산 내역을 찾을 수 없습니다.');
        }

        // 2. shop_t에서 정산 계좌 정보 가져오기
        $DB->where('idx', $settle['sh_idx']);
        $shop = $DB->getOne('shop_t', 'sh_bank, sh_bank_holder, sh_bank_account');

        // 3. 정산 주문 내역 (st_idx 기준 + ct_snapshot 포함)
        $orders = [];
        $DB->where('st_idx', $settle['idx']);          // 핵심 조건
        $DB->where('sh_idx', $sh_idx);
        $DB->where('ot_pay_status', 'PAID');
        $DB->orderBy('ot_pay_date', 'ASC');
        $orders = $DB->get('orders_t', null, '
            ot_pay_date,
            ot_number,
            ot_total_price,
            ct_snapshot
        ');

        // 응답
        echo json_encode([
            'success' => true,
            'data' => [
                'st_number'       => $settle['st_number'],
                'st_plan_date'    => $settle['st_plan_date'],
                'st_done_date'    => $settle['st_done_date'],
                'st_total_amount' => $settle['st_total_amount'],
                'st_service_fee'  => $settle['st_service_fee'],
                'st_final_amount' => $settle['st_final_amount'],
                'st_start_date'   => $settle['st_start_date'],
                'st_end_date'     => $settle['st_end_date'],
                'st_order_count'  => $settle['st_order_count'],
                'st_status'       => $settle['st_status'],
                'st_admin_memo'   => $settle['st_admin_memo'] ?? '',

                // 계좌 정보
                'sh_bank'         => $shop['sh_bank'] ?? '',
                'sh_bank_holder'  => $shop['sh_bank_holder'] ?? '',
                'sh_bank_account' => $shop['sh_bank_account'] ?? '',

                // 주문 내역 (ct_snapshot 포함)
                'orders'          => $orders
            ]
        ], JSON_UNESCAPED_UNICODE);

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

echo json_encode(['success'=>false, 'message'=>'잘못된 요청입니다.'], JSON_UNESCAPED_UNICODE);
exit;
