<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";

header('Content-Type: application/json; charset=utf-8');

$sh_idx = (int)($_SESSION['current_sh_idx'] ?? 0);
$act    = (string)($_POST['act'] ?? '');

if ($sh_idx <= 0) {
    echo json_encode(['success'=>false,'message'=>'매장 정보가 없습니다.'], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($act === 'cmp_list') {
    try {
        $type       = strtoupper((string)($_POST['type'] ?? 'TABLE')); // TABLE|PACK|RV
        $dayPreset  = strtoupper((string)($_POST['day_preset'] ?? 'TODAY'));
        $status     = strtoupper((string)($_POST['status'] ?? 'ALL'));
        $kw         = trim((string)($_POST['kw'] ?? ''));

        $sdate = trim((string)($_POST['sdate'] ?? ''));
        $edate = trim((string)($_POST['edate'] ?? ''));

        // 날짜 preset (오늘 기본)
        if ($dayPreset === 'TODAY') {
            $sdate = date('Y-m-d');
            $edate = date('Y-m-d');
        } else if ($dayPreset === 'YESTERDAY') {
            $sdate = date('Y-m-d', strtotime('-1 day'));
            $edate = date('Y-m-d', strtotime('-1 day'));
        } else {
            if ($sdate === '' || $edate === '') {
                $sdate = date('Y-m-d');
                $edate = date('Y-m-d');
            }
        }

        $date_title = date('Y년 m월 d일', strtotime($sdate));
        if ($sdate !== $edate) {
            $date_title = date('Y년 m월 d일', strtotime($sdate)) . ' ~ ' . date('Y년 m월 d일', strtotime($edate));
        }
        $date_title .= ' 완료/취소 내역';

        // 환불 합계 헬퍼
        $getRefundSum = function($ot_idx) use ($DB, $sh_idx){
            $DB->where('ot_idx', (int)$ot_idx);
            $DB->where('sh_idx', (int)$sh_idx);
            $DB->where('status', 'APPROVED');
            $row = $DB->getOne('payment_refunds_t', 'SUM(approved_amount) AS s');
            return (int)($row['s'] ?? 0);
        };

        // 스냅샷 요약 헬퍼
        $makeSummary = function($snapshotRaw){
            $snapshotRaw = (string)($snapshotRaw ?? '');
            if ($snapshotRaw === '') return '주문 메뉴 없음';
            $decoded = json_decode($snapshotRaw, true);
            if (!is_array($decoded)) return '주문 메뉴 없음';

            $items = $decoded['items'] ?? $decoded['cart'] ?? $decoded ?? [];
            $parts = [];
            foreach ($items as $it) {
                if (!is_array($it)) continue;
                $name = trim((string)($it['menu_name'] ?? $it['name'] ?? $it['title'] ?? ''));
                if ($name === '') continue;
                $qty = (int)($it['quantity'] ?? $it['qty'] ?? 1);
                $parts[] = $name . ' ' . $qty . '개';
                if (count($parts) >= 3) break;
            }
            $txt = implode(', ', $parts);
            return $txt ?: '주문 메뉴 없음';
        };

        $rows = [];
        $no = 0;

        // TABLE / PACK : orders_t 기반
        if ($type === 'TABLE' || $type === 'PACK') {
            $DB->where('o.sh_idx', $sh_idx);
            $DB->where("DATE(o.ot_wdate) BETWEEN ? AND ?", [$sdate, $edate]);

            if ($type === 'TABLE') {
                $DB->where("(o.ot_table IS NOT NULL AND o.ot_table <> '')");
            } else {
                $DB->where("(o.ot_table IS NULL OR o.ot_table = '')");
            }

            if ($status === 'DONE') {
                $DB->where('o.ot_status', 'COMPLETED');
            } else if ($status === 'CANCEL') {
                $DB->where('o.ot_status', 'CANCELLED');
            } else {
                $DB->where("o.ot_status IN ('COMPLETED','CANCELLED')");
            }

            if ($kw !== '') {
                $DB->where('o.ot_number', "%{$kw}%", 'LIKE');
            }

            $DB->join('member_t m', 'm.idx = o.mt_idx', 'LEFT');
            $DB->where('o.rv_idx',null, 'IS');
            $DB->orderBy('o.ot_wdate', 'DESC');

            $list = $DB->get('orders_t o', null, [
                'o.idx', 'o.ot_number', 'o.ot_status', 'o.ot_wdate', 'o.ot_table', 'o.ct_snapshot',
                'o.ot_total_price', 'o.ot_discount_amount', 'o.mt_idx', 'm.mt_name', 'm.mt_hp'
            ]);

            foreach ($list as $r) {
                $no++;
                $ot_idx = (int)$r['idx'];
                $st = strtoupper($r['ot_status']);

                $status_label = $st === 'CANCELLED' ? '취소' : '완료';
                $status_class = $st === 'CANCELLED' ? 'text-danger' : '';

                $total = (int)$r['ot_total_price'];
                $dc    = (int)$r['ot_discount_amount'];
                $paid  = max(0, $total - $dc);
                $refunded = $getRefundSum($ot_idx);
                $netPaid  = max(0, $paid - $refunded);

                $tableLabel = $type === 'TABLE' ? ($r['ot_table'] ?: '-') : ($r['mt_name'] ?: '-');

                $rows[] = [
                    'no' => $no,
                    'kind' => $type,
                    'id' => $ot_idx,
                    'number_label' => $r['ot_number'] ?: ('No.' . str_pad($ot_idx, 8, '0', STR_PAD_LEFT)),
                    'status_label' => $status_label,
                    'status_class' => $status_class,
                    'datetime_label' => date('Y.m.d H:i', strtotime($r['ot_wdate'])),
                    'table_label' => $tableLabel,
                    'summary' => $makeSummary($r['ct_snapshot']),
                    'pay_label' => number_format($netPaid) . '원',
                ];
            }
        }

        // RV : reservation_t 기반 (완료 + 취소만)
        else if ($type === 'RV') {
            $DB->where('r.sh_idx', $sh_idx);
            $DB->where("r.rv_wdate BETWEEN ? AND ?", [$sdate . ' 00:00:00', $edate . ' 23:59:59']);

            if ($status === 'DONE') {
                $DB->where('r.rv_status', 'ARRIVED');
            } else if ($status === 'CANCEL') {
                $DB->where('r.rv_status', ['CANCELLED', 'REJECTED'], 'IN');
            } else {
                $DB->where('r.rv_status', ['ARRIVED', 'CANCELLED', 'REJECTED'], 'IN');
            }

            if ($kw !== '') {
                $DB->where("(r.rv_number LIKE ? OR r.rv_name LIKE ? OR r.rv_hp LIKE ?)",
                    ["%{$kw}%", "%{$kw}%", "%{$kw}%"]);
            }

            $DB->orderBy('r.rv_wdate', 'DESC');

            $list = $DB->get('reservation_t r', null, [
                'r.idx', 'r.rv_number', 'r.rv_status', 'r.rv_date', 'r.rv_time',
                'r.rv_people', 'r.rv_name', 'r.rv_hp', 'r.ot_idx', 'r.rv_type',
                'r.rv_wdate'
            ]);

            foreach ($list as $r) {
                $no++;
                $rv_idx = (int)$r['idx'];
                $st = strtoupper($r['rv_status']);

                $status_label = in_array($st, ['CANCELLED','REJECTED']) ? '취소' : '완료';
                $status_class = in_array($st, ['CANCELLED','REJECTED']) ? 'text-danger' : '';

                $netPaid = 0;
                $summary = '-';

                $rvType = strtoupper((string)($r['rv_type'] ?? ''));
                $isPrepaid = ($rvType === 'PREPAID');
                $isPostpaid = (!$isPrepaid && (int)($r['ot_idx'] ?? 0) > 0);

                if ($isPrepaid) {
                    $rvTypeLabel = '선결제예약';
                } else if ($isPostpaid) {
                    $rvTypeLabel = '후결제예약';
                } else {
                    $rvTypeLabel = '방문예약';
                }

                $ot_idx = (int)$r['ot_idx'];
                if ($ot_idx > 0) {
                    $DB->where('idx', $ot_idx);
                    $DB->where('sh_idx', $sh_idx);
                    $ot = $DB->getOne('orders_t', 'ot_total_price, ot_discount_amount, ct_snapshot');

                    if ($ot) {
                        $total = (int)$ot['ot_total_price'];
                        $dc    = (int)$ot['ot_discount_amount'];
                        $paid  = max(0, $total - $dc);
                        $refunded = $getRefundSum($ot_idx);
                        $netPaid  = max(0, $paid - $refunded);

                        $summary = $makeSummary($ot['ct_snapshot']);
                    }
                }

                $dt = $r['rv_date'].' '.$r['rv_time'];

                $rows[] = [
                    'no' => $no,
                    'kind' => 'RV',
                    'id' => $rv_idx,
                    'number_label' => $r['rv_number'] ?: ('No.' . str_pad($rv_idx, 8, '0', STR_PAD_LEFT)),
                    'status_label' => $status_label,
                    'status_class' => $status_class,
                    'datetime_label' => $dt,
                    'table_label' => $r['rv_name'] . ' (' . $rvTypeLabel . ')',
                    'summary' => $summary,
                    'pay_label' => number_format($netPaid) . '원',
                ];
            }
        }

        echo json_encode([
            'success' => true,
            'data' => [
                'date_title' => $date_title,
                'rows' => $rows
            ]
        ], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
        exit;

    } catch (Exception $e) {
        error_log('cmp_list error: ' . $e->getMessage());
        echo json_encode(['success'=>false, 'message'=>'서버 오류'], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

if ($act === 'cmp_detail') {
    try {
        global $DB;
        header('Content-Type: application/json; charset=utf-8');

        $kind = strtoupper((string)($_POST['kind'] ?? ''));
        $id   = (int)($_POST['id'] ?? 0);

        if (!in_array($kind, ['TABLE','PACK','RV'], true) || $id <= 0) {
            echo json_encode(['success'=>false,'message'=>'잘못된 요청'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $makeItemsHtml = function($snapshotRaw){
            $snapshotRaw = (string)($snapshotRaw ?? '');
            $decoded = json_decode($snapshotRaw, true);
            if (!is_array($decoded)) return '';

            $items = [];
            if (isset($decoded['items']) && is_array($decoded['items'])) $items = $decoded['items'];
            else if (isset($decoded['cart']) && is_array($decoded['cart'])) $items = $decoded['cart'];
            else if (isset($decoded[0]) && is_array($decoded[0])) $items = $decoded;

            $html = '';
            $html .= '<li class="d-flex align-items-center justify-content-between "><p class="tit_st3">주문메뉴</p></li>';

            foreach ($items as $it) {
                if (!is_array($it)) continue;
                $name = trim((string)($it['menu_name'] ?? $it['name'] ?? $it['title'] ?? ''));
                if ($name === '') continue;
                $qty  = (int)($it['quantity'] ?? $it['qty'] ?? 1);
                if ($qty <= 0) $qty = 1;

                // 가격 (없으면 공백)
                $linePrice = '';
                if (is_numeric($it['total_price'] ?? null)) $linePrice = number_format((float)$it['total_price']).'원';
                else if (is_numeric($it['unit_price'] ?? null)) $linePrice = number_format(((float)$it['unit_price'] * $qty)).'원';

                // 옵션
                $optHtml = '';
                $opts = $it['options'] ?? [];
                if (is_array($opts) && count($opts) > 0) {
                    $optHtml .= '<ul class="dot_list tg_500 mt-4">';
                    foreach ($opts as $op) {
                        if (!is_array($op)) continue;
                        $on = trim((string)($op['option_name'] ?? $op['name'] ?? ''));
                        if ($on === '') continue;
                        $oprice = is_numeric($op['option_price'] ?? null) ? (int)$op['option_price'] : 0;
                        $suffix = ($oprice > 0) ? ' (+' . number_format($oprice) . ')' : '';
                        $optHtml .= '<li>'.htmlspecialchars($on, ENT_QUOTES, 'UTF-8').$suffix.'</li>';
                    }
                    $optHtml .= '</ul>';
                }

                $html .= '
                <li>
                  <div class="bill_box">
                    <div class="flex-fill">
                      <div>
                        <div class="d-flex justify-content-between ">
                          <p class="fw_600 fs_20">'.htmlspecialchars($name, ENT_QUOTES, 'UTF-8').' </p>
                          <p class="flex-shrink-0 ml-4">'.$qty.'개</p>
                        </div>
                        '.$optHtml.'
                      </div>
                    </div>
                    <div class="bill_money">'.$linePrice.'</div>
                  </div>
                </li>
                <li class="border-bottom-dot"></li>';
            }

            return $html;
        };

        // 기본 응답 구조
        $data = [
            'status_title' => '',
            'title' => '',
            'sub' => '',
            'number_label' => '',
            'datetime_label' => '',
            'bill_html' => '',
            'customer_label' => '비회원',
        ];

        if ($kind === 'TABLE' || $kind === 'PACK') {
            $DB->where('idx', $id);
            $DB->where('sh_idx', $sh_idx);
            $ot = $DB->getOne('orders_t', 'idx, ot_number, ot_status, ot_wdate, ot_table, ct_snapshot');

            if (!$ot) {
                echo json_encode(['success'=>false,'message'=>'주문을 찾을 수 없습니다.'], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $st = strtoupper((string)$ot['ot_status']);
            $data['status_title'] = ($st === 'CANCELLED') ? '취소내역' : '완료내역';

            $table = ($kind === 'TABLE') ? (string)($ot['ot_table'] ?? '-') : '포장';
            $data['title'] = ($kind === 'TABLE') ? ('테이블번호 '.$table) : '포장 주문';
            $data['sub']   = ''; // 원하면 "메뉴N개 · 금액 · ..." 구성해서 넣어도 됨

            $data['number_label'] = (string)($ot['ot_number'] ?? ('No.'.str_pad((int)$ot['idx'],8,'0',STR_PAD_LEFT)));
            $data['datetime_label'] = !empty($ot['ot_wdate']) ? date('Y년 m월 d일 H:i', strtotime($ot['ot_wdate'])) : '-';
            $data['bill_html'] = $makeItemsHtml($ot['ct_snapshot'] ?? '');
        }

        else if ($kind === 'RV') {
            $DB->where('idx', $id);
            $DB->where('sh_idx', $sh_idx);
            $rv = $DB->getOne('reservation_t', 'idx, rv_number, rv_status, rv_date, rv_time, rv_people, rv_name, rv_hp, ot_idx, rv_type');

            if (!$rv) {
                echo json_encode(['success'=>false,'message'=>'예약을 찾을 수 없습니다.'], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $st = strtoupper((string)$rv['rv_status']);
            $data['status_title'] = ($st === 'ARRIVED') ? '완료내역' : '취소내역';

            $rvType = strtoupper((string)($rv['rv_type'] ?? ''));
            $ot_idx = (int)($rv['ot_idx'] ?? 0);

            $isPrepaid = ($rvType === 'PREPAID');
            $isPostpaid = (!$isPrepaid && $ot_idx > 0);

            if ($isPrepaid) {
                $rvTypeLabel = '선결제예약';
            } else if ($isPostpaid) {
                $rvTypeLabel = '후결제예약';
            } else {
                $rvTypeLabel = '방문예약';
            }

            $data['title'] = '예약';
            $data['sub']   = $rvTypeLabel . ' ' . $rv['rv_people'] . '명';

            $data['number_label'] = (string)($rv['rv_number'] ?? ('No.'.str_pad((int)$rv['idx'],8,'0',STR_PAD_LEFT)));
            $dt = (string)$rv['rv_date'].' '.substr((string)$rv['rv_time'],0,5);
            $data['datetime_label'] = date('Y년 m월 d일 H:i', strtotime($dt));

            if ($ot_idx > 0) {
                $DB->where('idx', $ot_idx);
                $DB->where('sh_idx', $sh_idx);
                $ot = $DB->getOne('orders_t', 'ct_snapshot');
                if ($ot) {
                    $data['bill_html'] = $makeItemsHtml($ot['ct_snapshot'] ?? '');
                } else {
                    $data['bill_html'] = '<li class="py-4 text-center">주문 내역이 없습니다.</li>';
                }
            } else {
                $data['bill_html'] = '<li class="py-4 text-center">주문 내역이 없습니다.</li>';
            }

            $data['customer_label'] = (string)$rv['rv_name'].' ('.(string)$rv['rv_hp'].')';
        }


        echo json_encode(['success'=>true,'data'=>$data], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
        exit;

    } catch (Exception $e) {
        error_log('cmp_detail error: '.$e->getMessage());
        http_response_code(500);
        echo json_encode(['success'=>false,'message'=>'서버 오류'], JSON_UNESCAPED_UNICODE);
        exit;
    }
}
