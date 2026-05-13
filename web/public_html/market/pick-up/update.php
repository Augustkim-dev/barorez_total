<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";

header('Content-Type: application/json; charset=utf-8');

$sh_idx = (int)($_SESSION['current_sh_idx'] ?? 0);
$act    = $_POST['act'] ?? '';

if ($sh_idx <= 0) {
    echo json_encode(['success'=>false,'message'=>'매장 정보가 없습니다.'], JSON_UNESCAPED_UNICODE);
    exit;
}

// -------------------------
// 유틸
// -------------------------
function parseCartSnapshot($snapshotJson) {
    $items = [];
    if (!$snapshotJson) return $items;

    $snap = json_decode($snapshotJson, true);
    if (!is_array($snap)) return $items;

    $snapItems = $snap['items'] ?? $snap;
    if (!is_array($snapItems)) return $items;

    foreach ($snapItems as $it) {
        $qty = (int)($it['quantity'] ?? $it['ct_quantity'] ?? 0);
        if ($qty <= 0) $qty = 1;

        $unitPrice  = (int)($it['unit_price'] ?? $it['ct_price'] ?? 0);
        $totalPrice = (int)($it['total_price'] ?? $it['ct_total_price'] ?? 0);
        if ($totalPrice <= 0) $totalPrice = $unitPrice * $qty;

        $row = [
            'menu_name'   => (string)($it['menu_name'] ?? $it['sm_title'] ?? ''),
            'quantity'    => $qty,
            'unit_price'  => $unitPrice,
            'total_price' => $totalPrice,
            'options'     => [],
        ];

        if (!empty($it['options']) && is_array($it['options'])) {
            foreach ($it['options'] as $opt) {
                $row['options'][] = [
                    'option_name'  => (string)($opt['option_name'] ?? $opt['co_option_name'] ?? ''),
                    'option_price' => (int)($opt['option_price'] ?? $opt['co_option_price'] ?? 0),
                    'quantity'     => (int)($opt['quantity'] ?? 1),
                ];
            }
        }

        if ($row['menu_name'] !== '') $items[] = $row;
    }
    return $items;
}

function itemsSummaryFromSnapshot($snapshotJson) {
    $items = parseCartSnapshot($snapshotJson);
    if (empty($items)) return '';
    $parts = [];
    foreach ($items as $it) {
        $nm = $it['menu_name'] ?? '';
        $q  = (int)($it['quantity'] ?? 0);
        if ($nm === '') continue;
        $parts[] = $nm.' '.$q.'개';
        if (count($parts) >= 3) break;
    }
    return implode(', ', $parts);
}

function elapsedLabel($dt) {
    if (!$dt) return '';
    $ts = strtotime($dt);
    if (!$ts) return '';
    $diff = time() - $ts;
    if ($diff < 60) return $diff.'초 전';
    if ($diff < 3600) return floor($diff/60).'분 전';
    return floor($diff/3600).'시간 전';
}

function statusLabel($st) {
    $st = strtoupper((string)$st);
    if ($st === 'PENDING') return '접수대기';
    if ($st === 'CONFIRMED') return '접수완료';
    if ($st === 'PREPARING') return '음식준비중';
    if ($st === 'COMPLETED') return '전달완료';
    if ($st === 'CANCELLED') return '취소';
    return $st;
}

function isTakeoutRow($row) {
    $rv = (int)($row['rv_idx'] ?? 0);
    $tb = trim((string)($row['ot_table'] ?? ''));
    return ($rv <= 0) && ($tb === '');
}

// -------------------------
// pack_list
// -------------------------
if ($act === 'pack_list') {
    try {
        global $DB;

        $selected = (int)($_POST['selected_ot_idx'] ?? 0);

        $todayStart = date('Y-m-d 00:00:00');
        $todayEnd   = date('Y-m-d 00:00:00', strtotime('+1 day'));

        $DB->where('sh_idx', $sh_idx);
        $DB->where('ot_wdate', [$todayStart, $todayEnd], 'BETWEEN');
        $DB->where('(rv_idx IS NULL OR rv_idx = 0)');
        $DB->where('(ot_table IS NULL OR TRIM(ot_table) = \'\')');
        $DB->orderBy('ot_wdate', 'DESC');

        $rows = $DB->get('orders_t', null, '
          idx,
          mt_idx,
          rv_idx,
          ot_table,
          ot_number,
          ot_status,
          ot_total_price,
          ot_discount_amount,
          ct_snapshot,
          ot_wdate
        ');
        $rows = $rows ?: [];

        // 회원정보 매핑
        $mtIds = [];
        foreach ($rows as $r) {
            $mid = (int)($r['mt_idx'] ?? 0);
            if ($mid > 0) $mtIds[$mid] = true;
        }

        $memberMap = [];
        if (!empty($mtIds)) {
            $ids = array_keys($mtIds);
            $DB->where('idx', $ids, 'IN');
            $members = $DB->get('member_t', null, 'idx, mt_name, mt_hp');
            foreach ($members as $m) {
                $memberMap[(int)$m['idx']] = [
                    'name' => (string)($m['mt_name'] ?? ''),
                    'hp'   => (string)($m['mt_hp'] ?? ''),
                ];
            }
        }

        $new_list = [];
        $ing_list = [];
        $fi_list  = [];

        foreach ($rows as $r) {
            if (!isTakeoutRow($r)) continue;

            $st = strtoupper((string)($r['ot_status'] ?? 'PENDING'));
            $idx = (int)$r['idx'];

            $mid = (int)($r['mt_idx'] ?? 0);
            $cust = $memberMap[$mid] ?? null;

            $item = [
                'idx'          => $idx,
                'ot_number'    => (string)($r['ot_number'] ?? ''),
                'status'       => $st,
                'status_label' => statusLabel($st),
                'elapsed'      => elapsedLabel($r['ot_wdate'] ?? ''),
                'items_summary'=> itemsSummaryFromSnapshot($r['ct_snapshot'] ?? ''),
                'total_price'  => (int)($r['ot_total_price'] ?? 0),
                'discount_amount' => (int)($r['ot_discount_amount'] ?? 0),
                'customer_name'=> $cust ? ($cust['name'] ?: '회원') : '비회원',
                'phone'        => $cust['hp'] ?? '',
            ];

            if ($st === 'PENDING') {
                $new_list[] = $item;
            } else if ($st === 'CONFIRMED' || $st === 'PREPARING') {
                $ing_list[] = $item;
            } else {
                $fi_list[] = $item;
            }
        }

        $exists = false;
        foreach (array_merge($new_list,$ing_list,$fi_list) as $t) {
            if ((int)$t['idx'] === $selected) { $exists = true; break; }
        }
        if (!$exists) {
            $selected = (int)($new_list[0]['idx'] ?? $ing_list[0]['idx'] ?? $fi_list[0]['idx'] ?? 0);
        }

        echo json_encode([
            'success' => true,
            'data' => [
                'selected_ot_idx' => $selected,
                'new_list' => $new_list,
                'ing_list' => $ing_list,
                'fi_list'  => $fi_list,
            ],
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        error_log('pack_list error: '.$e->getMessage());
        http_response_code(500);
        echo json_encode(['success'=>false,'message'=>'서버 오류'], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// -------------------------
// pack_detail
// - PREPARING: prep_minutes(조리시간)만 내려줌
// - COMPLETED : prep_minutes + cook_elapsed_minutes + completed_hm 내려줌
// -------------------------
if ($act === 'pack_detail') {
    try {
        global $DB;

        $ot_idx = (int)($_POST['ot_idx'] ?? 0);
        if (!$ot_idx) {
            echo json_encode(['success'=>false,'message'=>'ot_idx 누락'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $DB->where('idx', $ot_idx);
        $DB->where('sh_idx', $sh_idx);

        // ✅ 컬럼명 통일: ot_prep_min, ot_prep_set_at, ot_completed_at
        $order = $DB->getOne('orders_t', '
          idx, mt_idx, sh_idx, rv_idx, ot_table,
          ot_number, ot_status, ot_total_price,
          ot_discount_amount,
          ct_snapshot, ot_wdate, ot_pay_status,
          ot_prep_min, ot_prep_set_at, ot_completed_at,
          ot_cancel, ot_cancel_reason
        ');

        if (!$order) {
            echo json_encode(['success'=>false,'message'=>'주문을 찾을 수 없습니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }
        if (!isTakeoutRow($order)) {
            echo json_encode(['success'=>false,'message'=>'포장 주문이 아닙니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $items = parseCartSnapshot($order['ct_snapshot'] ?? '');
        $menuCountTotal = 0;
        foreach ($items as $it) $menuCountTotal += (int)($it['quantity'] ?? 0);

        $totalPrice = (int)($order['ot_total_price'] ?? 0);
        $payStatus = strtoupper((string)($order['ot_pay_status'] ?? 'UNPAID'));
        $paid = ($payStatus === 'PAID') ? $totalPrice : 0;

        // ✅ 환불(승인) 합계
        $refundApprovedSum = 0;
        $DB->where('ot_idx', $ot_idx);
        $DB->where('sh_idx', $sh_idx);
        $DB->where('status', 'APPROVED');
        $row = $DB->getOne('payment_refunds_t', 'SUM(approved_amount) AS s');
        if ($row && isset($row['s'])) $refundApprovedSum = (int)$row['s'];

        $paidAfterRefund = max(0, (int)$paid - (int)$refundApprovedSum);

        // 고객정보
        $customer = null;
        $mt_idx = (int)($order['mt_idx'] ?? 0);
        if ($mt_idx > 0) {
            $DB->where('idx', $mt_idx);
            $m = $DB->getOne('member_t', 'mt_name, mt_hp');
            if ($m) $customer = ['name'=>$m['mt_name'] ?? '회원', 'hp'=>$m['mt_hp'] ?? ''];
        }

        $st = strtoupper((string)($order['ot_status'] ?? 'PENDING'));
        $discount = (int)($order['ot_discount_amount'] ?? 0);

        // ✅ 준비시간(접수 시 선택)
        $prepMin   = (int)($order['ot_prep_min'] ?? 0);
        // ✅ 준비 시작 시각(접수→PREPARING으로 바뀐 시각)
        $prepSetAt = $order['ot_prep_set_at'] ?? null;
        // ✅ 전달 완료 시각
        $completedAt = $order['ot_completed_at'] ?? null;

        // 전달 완료 HH:ii
        $completedHm = '';
        if ($completedAt) $completedHm = date('H:i', strtotime($completedAt));

        // 실제 조리 소요(분): prep_set_at ~ completed_at
        $cookElapsedMin = 0;
        if ($prepSetAt && $completedAt) {
            $t1 = strtotime($prepSetAt);
            $t2 = strtotime($completedAt);
            if ($t1 && $t2 && $t2 >= $t1) $cookElapsedMin = (int)floor(($t2 - $t1) / 60);
        }

        // ✅ 상태에 따라 노출값 제어(프론트가 더 편하게)
        $showCookElapsed = ($st === 'COMPLETED');

        echo json_encode([
            'success' => true,
            'data' => [
                'status' => $st,
                'status_label' => statusLabel($st),
                'elapsed' => elapsedLabel($order['ot_wdate'] ?? ''),
                'order_datetime' => $order['ot_wdate'] ? date('Y년 m월 d일 H:i', strtotime($order['ot_wdate'])) : '',
                'menu_count_total' => $menuCountTotal,
                'total_price' => $totalPrice,

                // 결제 관련
                'pay_status' => $payStatus,
                'paid_price' => (int)$paid,
                'refunded_price' => (int)$refundApprovedSum,
                'paid_after_refund' => (int)$paidAfterRefund,

                'discount_amount' => $discount,
                'pay_method' => 'CARD',

                // ✅ 시간 관련(요구사항)
                'cancel_reason' => (string)($order['ot_cancel_reason'] ?? ''),
                'cancel_at'     => $order['ot_cancel'] ? date('Y.m.d H:i', strtotime($order['ot_cancel'])) : '',
                'prep_minutes' => $prepMin,                 // 조리시간(선택값)
                'prep_set_at'  => $prepSetAt,
                'completed_at' => $completedAt,
                'completed_hm' => $completedHm,             // 예: "18:24"
                'cook_elapsed_minutes' => $showCookElapsed ? $cookElapsedMin : 0,
                'show_cook_elapsed' => $showCookElapsed,    // 프론트 조건 처리용

                'order' => [
                    'idx' => (int)$order['idx'],
                    'ot_number' => (string)($order['ot_number'] ?? ''),
                    'ot_status' => $st,
                ],
                'items' => $items,
                'customer' => $customer,
            ]
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        error_log('pack_detail error: '.$e->getMessage());
        http_response_code(500);
        echo json_encode(['success'=>false,'message'=>'서버 오류'], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// -------------------------
// pack_refund (결제취소/환불 금액 처리)
// -------------------------
if ($act === 'pack_refund') {
    try {
        global $DB;

        $ot_idx = (int)($_POST['ot_idx'] ?? 0);

        if (!$ot_idx) {
            echo json_encode(['success'=>false,'message'=>'요청값 누락'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $DB->where('idx', $ot_idx);
        $DB->where('sh_idx', $sh_idx);
        $order = $DB->getOne('orders_t', 'idx, ot_number, sh_idx, rv_idx, ot_table, ot_status, ot_total_price, ot_pay_status');
        if (!$order) {
            echo json_encode(['success'=>false,'message'=>'주문을 찾을 수 없습니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }
        if (!isTakeoutRow($order)) {
            echo json_encode(['success'=>false,'message'=>'포장 주문이 아닙니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $st = strtoupper((string)($order['ot_status'] ?? 'PENDING'));
//        if ($st !== 'PENDING') {
//            echo json_encode(['success'=>false,'message'=>'현재 상태에서는 환불할 수 없습니다.'], JSON_UNESCAPED_UNICODE);
//            exit;
//        }

        $payStatus = strtoupper((string)($order['ot_pay_status'] ?? 'UNPAID'));
        if ($payStatus !== 'PAID') {
            echo json_encode(['success'=>false,'message'=>'결제 완료된 주문만 환불 가능합니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $totalPrice = (int)($order['ot_total_price'] ?? 0);
        $discount   = (int)($order['ot_discount_amount'] ?? 0);
        $paidAmount = max(0, $totalPrice - $discount);

        $DB->where('ot_idx', $ot_idx);
        $DB->where('sh_idx', $sh_idx);
        $DB->where('status', 'APPROVED');
        $row = $DB->getOne('payment_refunds_t', 'SUM(approved_amount) AS s');
        $refunded = (int)($row['s'] ?? 0);

        $refundable = max(0, $paidAmount - $refunded);

        $DB->where('merchant_uid', $order['ot_number']);
        $payRow = $DB->getOne('payments_t', ['imp_uid', 'merchant_uid']);

        if (!$payRow || empty($payRow['imp_uid'])) {
            throw new Exception('결제 paymentId(imp_uid)가 없습니다.');
        }

        $pay_idx = 0;
        $imp_uid = '';
        if ($refundable > 0) {
            $insert = [
                'pay_idx' => $pay_idx,
                'ot_idx' => $ot_idx,
                'sh_idx' => $sh_idx,
                'refund_type' => 'FULL',
                'request_amount' => $refundable,
                'approved_amount' => $refundable,
                'reason' => '가맹점주 환불',
                'requested_by' => (int)($_SESSION['_mt_idx'] ?? 0),
                'requested_ip' => $_SERVER['REMOTE_ADDR'] ?? '',
                'imp_uid' => $imp_uid,
                'status' => 'APPROVED',
                'requested_at' => $DB->now(),
                'processed_at' => $DB->now(),
                'pg_payload' => json_encode(['note' => 'TODO: portone cancel call'], JSON_UNESCAPED_UNICODE),
            ];

            $ok = $DB->insert('payment_refunds_t', $insert);
            if (!$ok) {
                echo json_encode(['success' => false, 'message' => '환불 이력 저장 실패: ' . $DB->getLastError()], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $update = [
                'ot_status' => 'CANCELLED',
                'ot_cancel' => $DB->now(),
                'ot_cancel_reason' => '전액 환불 처리',
                'ot_udate' => $DB->now(),
            ];

            $DB->where('idx', $ot_idx);
            $DB->where('sh_idx', $sh_idx);
            $uok = $DB->update('orders_t', $update);

            if (!$uok) {
                if (method_exists($DB, 'rollback')) $DB->rollback();
                echo json_encode(['success' => false, 'message' => '주문 취소 업데이트 실패: ' . $DB->getLastError()], JSON_UNESCAPED_UNICODE);
                exit;
            }

            if ($order['ot_number']) {
                $paymentId = (string)$payRow['imp_uid'];
                $res = cancelPortonePayment($paymentId, '고객 요청', $refundable);
            }
        }
        if (method_exists($DB, 'commit')) $DB->commit();

        echo json_encode(['success'=>true,'message'=>'환불 처리되었습니다.'], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        error_log('pack_refund error: '.$e->getMessage());
        http_response_code(500);
        echo json_encode(['success'=>false,'message'=>'서버 오류'], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// -------------------------
// pack_action
// - accept: PREPARING 전환 + ot_prep_min 저장 + ot_prep_set_at 저장
// - complete: COMPLETED 전환 + ot_completed_at 저장
// -------------------------
if ($act === 'pack_action') {
    try {
        global $DB;

        $ot_idx = (int)($_POST['ot_idx'] ?? 0);
        $action = (string)($_POST['action'] ?? '');

        if (!$ot_idx || $action === '') {
            echo json_encode(['success'=>false,'message'=>'요청값 누락'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $DB->where('idx', $ot_idx);
        $DB->where('sh_idx', $sh_idx);
        $order = $DB->getOne('orders_t', 'idx, rv_idx, ot_table, ot_status');

        if (!$order) {
            echo json_encode(['success'=>false,'message'=>'주문을 찾을 수 없습니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }
        if (!isTakeoutRow($order)) {
            echo json_encode(['success'=>false,'message'=>'포장 주문이 아닙니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $cur = strtoupper((string)($order['ot_status'] ?? 'PENDING'));
        $next = '';
        $msg = '';

        // accept일 때 준비시간 받기
        $prepMin = null;
        if ($action === 'accept') {
            $prepMin = (int)($_POST['prep_min'] ?? 0);
            if ($prepMin < 0) $prepMin = 0;
            $prepMin = (int)(round($prepMin / 5) * 5); // 5분 단위 보정
        }

        if ($action === 'accept') {
            if ($cur !== 'PENDING') {
                echo json_encode(['success'=>false,'message'=>'현재 상태에서는 접수할 수 없습니다.'], JSON_UNESCAPED_UNICODE);
                exit;
            }
            $next = 'PREPARING';
            $msg  = '접수 후 음식 준비중으로 변경되었습니다.';
        }
        else if ($action === 'complete') {
            if ($cur !== 'PREPARING') {
                echo json_encode(['success'=>false,'message'=>'현재 상태에서는 전달완료 처리할 수 없습니다.'], JSON_UNESCAPED_UNICODE);
                exit;
            }
            $next = 'COMPLETED';
            $msg = '전달완료 처리되었습니다.';
        }
        else if ($action === 'reject') {
            if (in_array($cur, ['COMPLETED','CANCELLED'], true)) {
                echo json_encode(['success'=>false,'message'=>'이미 완료/취소된 주문입니다.'], JSON_UNESCAPED_UNICODE);
                exit;
            }
            $next = 'CANCELLED';
            $msg = '취소 처리되었습니다.';
        }
        else {
            echo json_encode(['success'=>false,'message'=>'알 수 없는 action'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $update = [
            'ot_status' => $next,
            'ot_udate'  => $DB->now(),
        ];

        // ✅ 접수(=PREPARING 전환) 시: 준비시간 + 준비시각 저장
        if ($action === 'accept') {
            $update['ot_prep_min']    = (int)$prepMin;   // ✅ 통일
            $update['ot_prep_set_at'] = $DB->now();      // ✅ 통일
        }

        // ✅ 전달완료 시각 저장
        if ($action === 'complete') {
            $update['ot_completed_at'] = $DB->now();
        }

        if ($next === 'CANCELLED') {
            $update['ot_cancel'] = $DB->now();
            $update['ot_cancel_reason'] = '매장에서 주문을 거절하였습니다.';

            // 주문 상세 다시 로드(결제/금액 확인)
            $DB->where('idx', $ot_idx);
            $DB->where('sh_idx', $sh_idx);
            $o2 = $DB->getOne('orders_t', 'idx, ot_total_price, ot_pay_status');

            if ($o2) {
                $payStatus = strtoupper((string)($o2['ot_pay_status'] ?? 'UNPAID'));
                $totalPrice = (int)($o2['ot_total_price'] ?? 0);

                if ($payStatus === 'PAID' && $totalPrice > 0) {

                    // 기 환불 승인 합계
                    $DB->where('ot_idx', $ot_idx);
                    $DB->where('sh_idx', $sh_idx);
                    $DB->where('status', 'APPROVED');
                    $row = $DB->getOne('payment_refunds_t', 'SUM(approved_amount) AS s');
                    $refunded = (int)($row['s'] ?? 0);

                    $refundable = max(0, $totalPrice - $refunded);

                    if ($refundable > 0) {

                        // payments_t에서 pay_idx 찾기
                        $DB->where('ot_idx', $ot_idx);
                        $DB->where('sh_idx', $sh_idx);
                        $pay = $DB->getOne('payments_t', 'idx, imp_uid');

                        if ($pay && !empty($pay['idx'])) {
                            $pay_idx = (int)$pay['idx'];
                            $imp_uid = (string)($pay['imp_uid'] ?? '');

                            $insert = [
                                'pay_idx'         => $pay_idx,
                                'ot_idx'          => $ot_idx,
                                'sh_idx'          => $sh_idx,
                                'refund_type'     => 'FULL',
                                'request_amount'  => $refundable,
                                'approved_amount' => $refundable,
                                'reason'          => '주문 취소 자동환불',
                                'requested_by'    => (int)($_SESSION['_mt_idx'] ?? 0),
                                'requested_ip'    => $_SERVER['REMOTE_ADDR'] ?? '',
                                'imp_uid'         => $imp_uid,
                                'status'          => 'APPROVED',
                                'requested_at'    => $DB->now(),
                                'processed_at'    => $DB->now(),
                                'pg_payload'      => json_encode(['note'=>'TODO: portone cancel call (auto refund)'], JSON_UNESCAPED_UNICODE),
                            ];

                            $DB->insert('payment_refunds_t', $insert);
                        }
                    }
                }
            }
        }

        $DB->where('idx', $ot_idx);
        $ok = $DB->update('orders_t', $update);

        if (!$ok) {
            echo json_encode(['success'=>false,'message'=>'DB 업데이트 실패: '.$DB->getLastError()], JSON_UNESCAPED_UNICODE);
            exit;
        }

        echo json_encode(['success'=>true,'message'=>$msg], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        error_log('pack_action error: '.$e->getMessage());
        http_response_code(500);
        echo json_encode(['success'=>false,'message'=>'서버 오류'], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// -------------------------
echo json_encode(['success'=>false,'message'=>'지원하지 않는 act'], JSON_UNESCAPED_UNICODE);
exit;
