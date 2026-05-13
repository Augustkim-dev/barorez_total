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
        if (count($parts) >= 3) break; // 3개까지만
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

function parseCancelReasonFromMemo($memo) {
    $memo = (string)$memo;
    $key = 'CANCEL_REASON:';
    $pos = strpos($memo, $key);
    if ($pos === false) return '';
    return trim(substr($memo, $pos + strlen($key)));
}

function ko_datetime_label($date, $time){
    $ts = strtotime($date.' '.$time);
    $week = ['일','월','화','수','목','금','토'];
    $w = $week[(int)date('w', $ts)];
    return date('n월 j일', $ts) . "({$w}) " . date('H:i', $ts);
}

// -------------------------
// rv_calendar : 월별 날짜별 예약 카운트
// -------------------------
if ($act === 'rv_calendar') {
    try {
        global $DB;

        $ym = preg_replace('/[^0-9\-]/', '', (string)($_POST['ym'] ?? ''));
        if (!preg_match('/^\d{4}\-\d{2}$/', $ym)) {
            $ym = date('Y-m');
        }

        $start = $ym . '-01';
        $end   = date('Y-m-d', strtotime($start.' +1 month')); // 다음달 1일 (BETWEEN의 상한으로 사용)

        // ✅ 캘린더 카운트: 취소/거절/도착완료 제외 → PENDING, CONFIRMED만
        $DB->where('sh_idx', $sh_idx);
        $DB->where('rv_date', [$start, $end], 'BETWEEN');
        $DB->where('rv_status', ['PENDING','CONFIRMED'], 'IN');
        $DB->groupBy('rv_date');

        $rows = $DB->get('reservation_t', null, 'rv_date, COUNT(*) AS cnt');

        $countsByDate = [];
        if (is_array($rows)) {
            foreach ($rows as $r) {
                $d = (string)($r['rv_date'] ?? '');
                $c = (int)($r['cnt'] ?? 0);
                if ($d !== '') $countsByDate[$d] = $c;
            }
        }

        echo json_encode([
            'success' => true,
            'data' => [
                'ym' => $ym,
                'today' => date('Y-m-d'),

                // ✅ 프론트에서 쓰기 좋은 키
                'counts_by_date' => $countsByDate,

                // ✅ (선택) 기존 코드 호환용
                'days' => $countsByDate,
            ]
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        error_log('rv_calendar error: '.$e->getMessage());
        http_response_code(500);
        echo json_encode(['success'=>false,'message'=>'서버 오류'], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// -------------------------
// rv_list : 선택 날짜 리스트 + 탭 카운트
// -------------------------
if ($act === 'rv_list') {
    try {
        global $DB;

        $week = ['일','월','화','수','목','금','토'];

        $ko_datetime_label = function($date, $time) use ($week) {
            $time = substr((string)$time, 0, 5);
            $ts = strtotime($date.' '.$time);
            $w = $week[(int)date('w', $ts)];
            return date('Y년 m월 d일', $ts) . "({$w}) " . date('H:i', $ts);
        };

        $parse_snapshot_items = function($json) {
            $out = [];
            if (!$json) return $out;

            $raw = (string)$json;
            $arr = json_decode($raw, true);

            if (is_string($arr)) {
                $arr2 = json_decode($arr, true);
                if (is_array($arr2)) $arr = $arr2;
            }
            if (!is_array($arr)) return $out;

            $candidates = [];
            $paths = [
                ['items'],
                ['cart'],
                ['data','items'],
                ['list'],
                ['menus'],
                ['menu'],
                ['order','items'],
                ['ct_snapshot','items'],
            ];

            foreach ($paths as $p) {
                $tmp = $arr;
                $ok = true;
                foreach ($p as $k) {
                    if (is_array($tmp) && array_key_exists($k, $tmp)) $tmp = $tmp[$k];
                    else { $ok=false; break; }
                }
                if ($ok && is_array($tmp)) { $candidates = $tmp; break; }
            }

            if (!$candidates && array_keys($arr) === range(0, count($arr)-1)) {
                $candidates = $arr;
            }
            if (!is_array($candidates)) return $out;

            foreach ($candidates as $it) {
                if (!is_array($it)) continue;

                $name =
                    $it['menu_title'] ??
                    $it['menu_name'] ??
                    $it['product_name'] ??
                    $it['pd_name'] ??
                    $it['it_name'] ??
                    $it['title'] ??
                    $it['name'] ??
                    '';

                $qty =
                    $it['qty'] ??
                    $it['quantity'] ??
                    $it['count'] ??
                    $it['ea'] ??
                    1;

                $name = trim((string)$name);
                $qty = (int)$qty;
                if ($qty <= 0) $qty = 1;

                if ($name === '') continue;

                $out[] = ['name'=>$name, 'qty'=>$qty];
            }

            return $out;
        };

        $make_summary = function($items) {
            if (!is_array($items) || count($items) === 0) return '';

            $parts = [];
            foreach ($items as $it) {
                $name = trim((string)($it['name'] ?? ''));
                if ($name === '') continue;

                $qty = (int)($it['qty'] ?? 1);
                if ($qty <= 0) $qty = 1;

                $parts[] = $name . ' ' . $qty . '개';
            }

            return implode(', ', $parts);
        };

        $date   = preg_replace('/[^0-9\-]/', '', (string)($_POST['date'] ?? ''));
        if (!preg_match('/^\d{4}\-\d{2}\-\d{2}$/', $date)) $date = date('Y-m-d');

        $filter = strtoupper((string)($_POST['filter'] ?? 'TODAY'));
        $keyword = trim((string)($_POST['keyword'] ?? ''));

        $VISIBLE_RV_STATUS = ['PENDING','CONFIRMED'];

        $DB->where('sh_idx', $sh_idx);
        $DB->where('rv_date', $date);
        $DB->where('rv_status', $VISIBLE_RV_STATUS, 'IN');
        $cnt_today = (int)$DB->getValue('reservation_t', 'COUNT(*)');

        $DB->where('sh_idx', $sh_idx);
        $DB->where('rv_status', 'CONFIRMED');
        $cnt_confirmed = (int)$DB->getValue('reservation_t', 'COUNT(*)');

        $DB->where('sh_idx', $sh_idx);
        $DB->where('rv_status', 'PENDING');
        $cnt_pending = (int)$DB->getValue('reservation_t', 'COUNT(*)');

        $statusFilter = $VISIBLE_RV_STATUS;
        if ($filter === 'CONFIRMED') $statusFilter = ['CONFIRMED'];
        if ($filter === 'PENDING')   $statusFilter = ['PENDING'];

        $DB->where('sh_idx', $sh_idx);
        if ($filter === 'TODAY') {
            $DB->where('rv_date', $date);
        }
        $DB->where('rv_status', $statusFilter, 'IN');

        if ($keyword !== '') {
            $DB->where("(rv_name LIKE ? OR rv_hp LIKE ?)", ["%{$keyword}%", "%{$keyword}%"]);
        }

        $DB->orderBy('rv_date', 'ASC');
        $DB->orderBy('rv_time', 'ASC');
        $DB->orderBy('idx', 'DESC');

        $rows = $DB->get('reservation_t', null, "
            idx, rv_number, rv_type, rv_date, rv_time, rv_status,
            rv_name, rv_hp, rv_people
        ");
        if (!is_array($rows)) $rows = [];

        $rvIds = [];
        foreach ($rows as $r) $rvIds[] = (int)$r['idx'];
        $rvIds = array_values(array_unique(array_filter($rvIds)));

        $orderMap = [];
        if (count($rvIds) > 0) {
            $DB->where('sh_idx', $sh_idx);
            $DB->where('rv_idx', $rvIds, 'IN');
            $DB->orderBy('idx', 'DESC');

            $orders = $DB->get('orders_t', null, "
                idx, rv_idx, ot_number, ot_pay_type, ot_pay_status,
                ot_total_price, ot_discount_amount, ct_snapshot
            ");
            if (!is_array($orders)) $orders = [];

            foreach ($orders as $ot) {
                $k = (int)($ot['rv_idx'] ?? 0);
                if ($k <= 0) continue;
                if (!isset($orderMap[$k])) {
                    $orderMap[$k] = $ot;
                }
            }
        }

        $statusOrder = ['PENDING'=>1,'CONFIRMED'=>2];

        $groups = [];
        foreach ($rows as $r) {
            $rv_date = (string)($r['rv_date'] ?? '');
            $rv_time = substr((string)($r['rv_time'] ?? ''), 0, 5);
            $key = $rv_date . ' ' . $rv_time;

            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'datetime' => $key,
                    'datetime_label' => $ko_datetime_label($rv_date, $rv_time),
                    'items' => [],
                ];
            }

            $rv_idx = (int)($r['idx'] ?? 0);
            $st = strtoupper((string)($r['rv_status'] ?? ''));

            $ot = $orderMap[$rv_idx] ?? null;
            $payType = strtoupper((string)($ot['ot_pay_type'] ?? ($r['rv_type'] ?? 'POSTPAID')));
            $isPrepaid = ($payType === 'PREPAID');

            $items_summary = '';
            if ($ot) {
                $items = $parse_snapshot_items($ot['ct_snapshot'] ?? '');
                $items_summary = $make_summary($items);
            }

            if ($isPrepaid) {
                if ($items_summary === '') {
                    $items_summary = '선결제 주문';
                }
            } else {
                // 후불: 메뉴가 있으면 후불결제, 없으면 방문예약
                $items_summary = ($items_summary !== '') ? '후불결제' : '방문예약';
            }

            $people = (int)($r['rv_people'] ?? 1);

            $groups[$key]['items'][] = [
                'idx' => $rv_idx,
                'rv_status' => $st,
                'rv_type' => $isPrepaid ? 'PREPAID' : 'POSTPAID',
                'rv_number' => (string)($r['rv_number'] ?? ''),
                'rv_name' => (string)($r['rv_name'] ?? ''),
                'rv_hp' => (string)($r['rv_hp'] ?? ''),
                'rv_people' => $people,
                'items_summary' => $items_summary,
                'sort_status' => $statusOrder[$st] ?? 999,
            ];
        }

        $groupList = array_values($groups);
        usort($groupList, function($a, $b){
            return strcmp($a['datetime'], $b['datetime']);
        });

        foreach ($groupList as &$g) {
            usort($g['items'], function($a, $b){
                $ai = (int)($a['sort_status'] ?? 999);
                $bi = (int)($b['sort_status'] ?? 999);
                if ($ai === $bi) return $b['idx'] <=> $a['idx'];
                return $ai <=> $bi;
            });
            foreach ($g['items'] as &$it) unset($it['sort_status']);
            unset($it);
        }
        unset($g);

        echo json_encode([
            'success' => true,
            'data' => [
                'date' => $date,
                'counts' => [
                    'today' => $cnt_today,
                    'confirmed' => $cnt_confirmed,
                    'pending' => $cnt_pending,
                ],
                'groups' => $groupList,
            ]
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        error_log('rv_list error: '.$e->getMessage());
        http_response_code(200);
        echo json_encode([
            'success' => false,
            'message' => '리스트 조회 실패'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// -------------------------
// rv_detail : 예약 상세(선결제면 주문/환불 포함)
// -------------------------
if ($act === 'rv_detail') {
    try {
        global $DB;

        header('Content-Type: application/json; charset=utf-8');

        $rv_idx = (int)($_POST['rv_idx'] ?? 0);
        if ($rv_idx <= 0) {
            echo json_encode(['success' => false, 'message' => '잘못된 요청'], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
            exit;
        }

        $fmt_won = function($num){
            $n = (float)($num ?? 0);
            return number_format($n) . '원';
        };

        $makeRvNumber = function($idx){
            return 'No.' . str_pad((string)$idx, 8, '0', STR_PAD_LEFT);
        };

        $formatDatetimeLabel = function($rv_date, $rv_time){
            $rv_date = (string)$rv_date;
            $rv_time = (string)$rv_time;
            if ($rv_time && strlen($rv_time) >= 5) $rv_time = substr($rv_time, 0, 5);

            $ts = strtotime(trim($rv_date . ' ' . $rv_time));
            if (!$ts) return trim($rv_date . ' ' . $rv_time);

            return date('Y년 m월 d일 H:i', $ts);
        };

        $parse_snapshot_items = function($snapshotRaw){
            $snapshotRaw = (string)($snapshotRaw ?? '');
            if ($snapshotRaw === '') return [];

            $decoded = json_decode($snapshotRaw, true);
            if (!is_array($decoded)) return [];

            $list = [];
            if (isset($decoded['items']) && is_array($decoded['items'])) {
                $list = $decoded['items'];
            } else if (isset($decoded['cart']) && is_array($decoded['cart'])) {
                $list = $decoded['cart'];
            } else if (isset($decoded[0]) && is_array($decoded[0])) {
                $list = $decoded;
            }

            if (!is_array($list) || count($list) === 0) return [];

            $bucket = [];

            foreach ($list as $it) {
                if (!is_array($it)) continue;

                $name = trim((string)($it['menu_name'] ?? $it['name'] ?? $it['title'] ?? ''));
                if ($name === '') continue;

                $qty = (int)($it['quantity'] ?? $it['qty'] ?? 0);
                if ($qty <= 0) $qty = 1;

                $unitPrice  = is_numeric($it['unit_price'] ?? null) ? (float)$it['unit_price'] : null;
                $totalPrice = is_numeric($it['total_price'] ?? null) ? (float)$it['total_price'] : null;

                $optionsRaw = $it['options'] ?? [];
                $opts = [];

                if (is_array($optionsRaw)) {
                    foreach ($optionsRaw as $op) {
                        if (!is_array($op)) continue;

                        $optName  = trim((string)($op['option_name'] ?? $op['name'] ?? ''));
                        if ($optName === '') continue;

                        $optQty   = (int)($op['quantity'] ?? 1);
                        if ($optQty <= 0) $optQty = 1;

                        $optPrice = is_numeric($op['option_price'] ?? null) ? (float)$op['option_price'] : 0;

                        $opts[] = [
                            'name' => $optName,
                            'price' => $optPrice,
                            'qty' => $optQty,
                            'oc_idx' => (int)($op['oc_idx'] ?? 0),
                            'om_idx' => (int)($op['om_idx'] ?? 0),
                        ];
                    }
                }

                usort($opts, function($a, $b){
                    $ka = ($a['oc_idx'] * 100000) + $a['om_idx'];
                    $kb = ($b['oc_idx'] * 100000) + $b['om_idx'];
                    if ($ka !== $kb) return $ka <=> $kb;

                    $na = $a['name'] ?? '';
                    $nb = $b['name'] ?? '';
                    if ($na !== $nb) return strcmp($na, $nb);

                    if (($a['price'] ?? 0) !== ($b['price'] ?? 0)) return ($a['price'] ?? 0) <=> ($b['price'] ?? 0);
                    return ($a['qty'] ?? 1) <=> ($b['qty'] ?? 1);
                });

                $optSigParts = [];
                foreach ($opts as $o) {
                    $optSigParts[] = implode(':', [
                        (int)($o['oc_idx'] ?? 0),
                        (int)($o['om_idx'] ?? 0),
                        (string)($o['name'] ?? ''),
                        (int)($o['qty'] ?? 1),
                        (int)($o['price'] ?? 0),
                    ]);
                }
                $optSig = implode('|', $optSigParts);

                $key = $name . '||' . $optSig;

                if (!isset($bucket[$key])) {
                    $bucket[$key] = [
                        'name' => $name,
                        'qty' => 0,
                        'unit_price' => $unitPrice,
                        'total_price' => 0,
                        'options' => $opts,
                    ];
                }

                $bucket[$key]['qty'] += $qty;

                if (is_numeric($totalPrice)) {
                    $bucket[$key]['total_price'] += $totalPrice;
                } else if (is_numeric($unitPrice)) {
                    $bucket[$key]['total_price'] += ($unitPrice * $qty);
                }
            }

            return array_values($bucket);
        };

        $build_option_labels = function($opts){
            if (!is_array($opts) || count($opts) === 0) return [];

            $labels = [];
            foreach ($opts as $o) {
                $n = trim((string)($o['name'] ?? ''));
                if ($n === '') continue;

                $q = (int)($o['qty'] ?? 1);
                if ($q <= 0) $q = 1;

                $p = is_numeric($o['price'] ?? null) ? (float)$o['price'] : 0;

                $suffix = '';
                if ($p > 0) $suffix = ' (+' . number_format($p) . '원)';
                $labels[] = $n . ' x' . $q . $suffix;
            }
            return $labels;
        };

        $make_summary = function($items){
            if (!is_array($items) || count($items) === 0) return '';
            $parts = [];
            foreach ($items as $it) {
                $name = trim((string)($it['name'] ?? ''));
                $qty  = (int)($it['qty'] ?? 1);
                if ($name === '') continue;
                if ($qty <= 0) $qty = 1;
                $parts[] = $name . ' ' . $qty . '개';
            }
            return implode(', ', $parts);
        };

        $make_elapsed_label = function($dtStr){
            if (!$dtStr) return '-';

            $ts = strtotime($dtStr);
            if (!$ts) return '-';

            $diff = time() - $ts;
            if ($diff < 0) $diff = 0;

            $min = (int)floor($diff / 60);
            $hr  = (int)floor($diff / 3600);
            $day = (int)floor($diff / 86400);

            if ($min < 1) return '방금 전';
            if ($min < 60) return $min.'분 전';
            if ($hr  < 24) return $hr.'시간 전';
            return $day.'일 전';
        };

        $DB->where('sh_idx', $sh_idx);
        $DB->where('idx', $rv_idx);
        $rv = $DB->getOne('reservation_t', '
            idx, rv_number, rv_date, rv_time, rv_status, rv_type,
            rv_name, rv_hp, rv_people,
            rv_cancel_reason, rv_cancel_at,
            rv_wdate, rv_udate
        ');

        if (!$rv) {
            echo json_encode(['success' => false, 'message' => '예약 내역을 찾을 수 없습니다.'], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
            exit;
        }

        $rv_status = strtoupper((string)($rv['rv_status'] ?? ''));
        $rv_date   = (string)($rv['rv_date'] ?? '');
        $rv_time   = (string)($rv['rv_time'] ?? '00:00');
        if ($rv_time && strlen($rv_time) >= 5) $rv_time = substr($rv_time, 0, 5);

        $datetime_label = $formatDatetimeLabel($rv_date, $rv_time);

        $DB->where('sh_idx', $sh_idx);
        $DB->where('rv_idx', $rv_idx);
        $DB->orderBy('idx', 'DESC');
        $ot = $DB->getOne('orders_t', 'idx, ot_number, ot_total_price, ot_discount_amount, ct_snapshot, ot_pay_type, ot_pay_status, ot_pay_date');

        $rv_type = strtoupper((string)($rv['rv_type'] ?? 'POSTPAID'));
        $menus = [];
        $top_summary = '';
        $pay = [
            'ot_idx' => 0,
            'ot_number' => '-',
            'pay_type' => $rv_type,
            'pay_status' => ($rv_type === 'PREPAID' ? 'PAID' : 'UNPAID'),
            'pay_date' => null,
            'total_price' => 0,
            'discount_amount' => 0,
            'paid_amount' => 0,
            'total_price_label' => '0원',
            'discount_label' => '0원',
            'paid_label' => '0원',
            'pay_method_label' => '-',
            'refunded_amount' => 0,
            'refunded_label' => '0원',
        ];

        if ($ot) {
            $payType   = strtoupper((string)($ot['ot_pay_type'] ?? $rv_type));
            $payStatus = strtoupper((string)($ot['ot_pay_status'] ?? 'UNPAID'));

            if (in_array($payType, ['PREPAID', 'POSTPAID'], true)) {
                $rv_type = $payType;
            }

            $pay['ot_idx']     = (int)($ot['idx'] ?? 0);
            $pay['ot_number']  = (string)($ot['ot_number'] ?? '-');
            $pay['pay_type']   = $payType;
            $pay['pay_status'] = $payStatus;
            $pay['pay_date']   = $ot['ot_pay_date'] ?? null;

            $pay['total_price']     = (float)($ot['ot_total_price'] ?? 0);
            $pay['discount_amount'] = (float)($ot['ot_discount_amount'] ?? 0);
            $pay['paid_amount']     = max(0, $pay['total_price'] - $pay['discount_amount']);

            $pay['total_price_label'] = $fmt_won($pay['total_price']);
            $pay['discount_label']    = $fmt_won($pay['discount_amount']);
            $pay['paid_label']        = $fmt_won($pay['paid_amount']);

            $items = $parse_snapshot_items($ot['ct_snapshot'] ?? '');

            foreach ($items as $it) {
                $name = (string)($it['name'] ?? '');
                $qty  = (int)($it['qty'] ?? 1);
                if ($qty <= 0) $qty = 1;

                $optLabels = $build_option_labels($it['options'] ?? []);

                $linePrice = null;
                if (is_numeric($it['total_price'] ?? null)) {
                    $linePrice = (float)$it['total_price'];
                } else if (is_numeric($it['unit_price'] ?? null)) {
                    $linePrice = (float)$it['unit_price'] * $qty;
                }

                $menus[] = [
                    'name' => $name,
                    'qty' => $qty,
                    'options' => $optLabels,
                    'price_label' => is_numeric($linePrice) ? number_format($linePrice) . '원' : '',
                ];
            }

            if ($rv_type === 'PREPAID') {
                $top_summary = $make_summary($items);

                if ($pay['ot_idx'] > 0) {
                    $DB->where('ot_idx', (int)$pay['ot_idx']);
                    $DB->where('sh_idx', $sh_idx);
                    $DB->where('status', 'APPROVED');
                    $rrow = $DB->getOne('payment_refunds_t', 'SUM(approved_amount) AS s');

                    $refundedSum = (float)($rrow['s'] ?? 0);
                    if ($refundedSum < 0) $refundedSum = 0;

                    $pay['refunded_amount'] = $refundedSum;
                    $pay['refunded_label']  = $fmt_won($refundedSum);
                    $pay['paid_amount'] = max(0, ($pay['total_price'] - $pay['discount_amount']) - $refundedSum);
                    $pay['paid_label']  = $fmt_won($pay['paid_amount']);
                }
            } else {
                $top_summary = !empty($menus) ? '후불결제' : '방문예약';
            }
        } else {
            if ($rv_type !== 'PREPAID') {
                $top_summary = '방문예약';
            }
        }

        $can_pay_cancel = 'N';
        if ($rv_type === 'PREPAID') {
            if ($pay['pay_status'] === 'PAID' && ($rv_status === 'PENDING' || $rv_status === 'CONFIRMED')) {
                $can_pay_cancel = 'Y';
            }
        }

        $base_dt = '';
        if (!empty($rv['rv_wdate'])) {
            $base_dt = $rv['rv_wdate'];
        } elseif (!empty($rv['rv_udate'])) {
            $base_dt = $rv['rv_udate'];
        } else {
            $d = preg_replace('/[^0-9\-]/', '', (string)($rv['rv_date'] ?? ''));
            $t = substr((string)($rv['rv_time'] ?? ''), 0, 5);
            if ($d && $t) $base_dt = $d.' '.$t.':00';
        }

        $elapsed_label = $make_elapsed_label($base_dt);

        $data = [
            'idx' => (int)$rv['idx'],
            'rv_status' => $rv_status,
            'rv_type' => $rv_type,
            'rv_number' => (string)($rv['rv_number'] ?? $makeRvNumber((int)$rv['idx'])),
            'datetime_label' => $datetime_label,
            'rv_name' => (string)($rv['rv_name'] ?? ''),
            'rv_hp' => (string)($rv['rv_hp'] ?? ''),
            'rv_people' => (int)($rv['rv_people'] ?? 1),
            'rv_cancel_reason' => (string)($rv['rv_cancel_reason'] ?? ''),
            'rv_cancel_at' => !empty($rv['rv_cancel_at']) ? date('Y.m.d H:i', strtotime($rv['rv_cancel_at'])) : '',
            'top_summary' => $top_summary,
            'elapsed_label' => $elapsed_label,
            'menus' => $menus,
            'pay' => $pay,
            'can_pay_cancel' => $can_pay_cancel,
        ];

        echo json_encode(['success' => true, 'data' => $data], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
        exit;

    } catch (Exception $e) {
        error_log('rv_detail error: '.$e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => '서버 오류'], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// -------------------------
// rv_action : 상태 변경 + (PREPAID면 orders/refund 연동)
// action: ACCEPT | REJECT | CANCEL | ARRIVE
// -------------------------
if ($act === 'rv_action') {
    try {
        global $DB;

        $rv_idx = (int)($_POST['rv_idx'] ?? 0);
        $action = strtoupper((string)($_POST['action'] ?? ''));
        $reason = trim((string)($_POST['reason'] ?? ''));

        if ($rv_idx <= 0 || $action === '') {
            echo json_encode(['success'=>false,'message'=>'요청값 누락'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $DB->where('idx', $rv_idx);
        $DB->where('sh_idx', $sh_idx);
        $rv = $DB->getOne('reservation_t', 'idx, sh_idx, rv_type, rv_status, ot_idx');

        if (!$rv) {
            echo json_encode(['success'=>false,'message'=>'예약을 찾을 수 없습니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $cur         = strtoupper((string)($rv['rv_status'] ?? 'PENDING'));
        $rv_type_raw = strtoupper((string)($rv['rv_type'] ?? 'POSTPAID'));
        $ot_idx      = (int)($rv['ot_idx'] ?? 0);

        $isPrepaid  = ($rv_type_raw === 'PREPAID');
        $isPostpaid = in_array($rv_type_raw, ['POSTPAID', 'VISIT'], true);

        $next = '';
        $msg  = '';

        if ($action === 'ACCEPT') {
            if ($cur !== 'PENDING') {
                echo json_encode(['success'=>false,'message'=>'현재 상태에서는 접수할 수 없습니다.'], JSON_UNESCAPED_UNICODE);
                exit;
            }
            $next = 'CONFIRMED';
            $msg  = '예약이 접수(확정)되었습니다.';
        }
        else if ($action === 'ARRIVE') {
            if ($cur !== 'CONFIRMED') {
                echo json_encode(['success'=>false,'message'=>'현재 상태에서는 도착 확인을 할 수 없습니다.'], JSON_UNESCAPED_UNICODE);
                exit;
            }
            $next = 'ARRIVED';
            $msg  = '도착 확인 처리되었습니다.';
        }
        else if ($action === 'REJECT') {
            if ($cur !== 'PENDING') {
                echo json_encode(['success'=>false,'message'=>'현재 상태에서는 거절할 수 없습니다.'], JSON_UNESCAPED_UNICODE);
                exit;
            }
            $next = 'REJECTED';
            $msg  = '예약을 거절하였습니다.';
            if ($reason === '') $reason = '매장에서 예약을 거절하였습니다.';
        }
        else if ($action === 'CANCEL') {
            if ($cur !== 'CONFIRMED') {
                echo json_encode(['success'=>false,'message'=>'현재 상태에서는 예약취소를 할 수 없습니다.'], JSON_UNESCAPED_UNICODE);
                exit;
            }
            $next = 'CANCELLED';
            $msg  = '예약을 취소하였습니다.';
            if ($reason === '') $reason = '매장에서 예약을 취소하였습니다.';
        }
        else {
            echo json_encode(['success'=>false,'message'=>'알 수 없는 action'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $needAutoFullRefund    = $isPrepaid && in_array($next, ['CANCELLED','REJECTED'], true);
        $needPostpaidOrderCancel = $isPostpaid && in_array($next, ['CANCELLED','REJECTED'], true) && $ot_idx > 0;
        $needCompleteOrder     = ($action === 'ARRIVE' && $ot_idx > 0);

        if (method_exists($DB, 'startTransaction')) $DB->startTransaction();

        // 1) reservation_t 업데이트
        $updRv = [
            'rv_status' => $next,
            'rv_udate'  => $DB->now(),
        ];

        if (in_array($next, ['CANCELLED','REJECTED'], true)) {
            $updRv['rv_cancel_reason'] = $reason;
            $updRv['rv_cancel_at']     = $DB->now();
        }

        $DB->where('idx', $rv_idx);
        $DB->where('sh_idx', $sh_idx);
        $ok1 = $DB->update('reservation_t', $updRv);

        if (!$ok1) {
            if (method_exists($DB, 'rollback')) $DB->rollback();
            echo json_encode(['success'=>false,'message'=>'예약 상태 변경 실패: '.$DB->getLastError()], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // 2) 도착확인 시 연결 주문 COMPLETED 처리
        if ($needCompleteOrder) {
            $DB->where('idx', $ot_idx);
            $DB->where('sh_idx', $sh_idx);
            $okOrderComplete = $DB->update('orders_t', [
                'ot_status' => 'COMPLETED',
                'ot_udate'  => $DB->now(),
            ]);

            if (!$okOrderComplete) {
                if (method_exists($DB, 'rollback')) $DB->rollback();
                echo json_encode([
                    'success' => false,
                    'message' => '주문 완료 처리 실패: ' . $DB->getLastError()
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }
        }

        // 3) 선결제 + 취소/거절 => 자동 전액환불
        if ($needAutoFullRefund) {
            if ($ot_idx <= 0) {
                if (method_exists($DB, 'rollback')) $DB->rollback();
                echo json_encode(['success'=>false,'message'=>'선결제 주문키(ot_idx)가 없습니다.'], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $DB->where('idx', $ot_idx);
            $DB->where('sh_idx', $sh_idx);
            $order = $DB->getOne('orders_t', 'idx, ot_number, ot_total_price, ot_discount_amount, ot_pay_status, ot_status');

            if (!$order) {
                if (method_exists($DB, 'rollback')) $DB->rollback();
                echo json_encode(['success'=>false,'message'=>'주문 정보를 찾을 수 없습니다.'], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $payStatus = strtoupper((string)($order['ot_pay_status'] ?? 'UNPAID'));
            if ($payStatus !== 'PAID') {
                if (method_exists($DB, 'rollback')) $DB->rollback();
                echo json_encode(['success'=>false,'message'=>'결제 완료된 선결제 예약만 환불 가능합니다.'], JSON_UNESCAPED_UNICODE);
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

            $updOrder = [
                'ot_status'        => 'CANCELLED',
                'ot_cancel'        => $DB->now(),
                'ot_cancel_reason' => $reason,
                'ot_udate'         => $DB->now(),
            ];

            if ($refundable > 0) {
                $insert = [
                    'pay_idx'         => 0,
                    'ot_idx'          => $ot_idx,
                    'sh_idx'          => $sh_idx,
                    'refund_type'     => 'FULL',
                    'request_amount'  => $refundable,
                    'approved_amount' => $refundable,
                    'reason'          => $reason,
                    'requested_by'    => (int)($_SESSION['_mt_idx'] ?? 0),
                    'requested_ip'    => $_SERVER['REMOTE_ADDR'] ?? '',
                    'imp_uid'         => '',
                    'status'          => 'APPROVED',
                    'requested_at'    => $DB->now(),
                    'processed_at'    => $DB->now(),
                    'pg_payload'      => json_encode(['note'=>'NO_PG_LINKED'], JSON_UNESCAPED_UNICODE),
                ];

                $okR = $DB->insert('payment_refunds_t', $insert);
                if (!$okR) {
                    if (method_exists($DB, 'rollback')) $DB->rollback();
                    echo json_encode(['success'=>false,'message'=>'환불 이력 저장 실패: '.$DB->getLastError()], JSON_UNESCAPED_UNICODE);
                    exit;
                }

                $updOrder['ot_pay_status'] = 'REFUND';
            }

            $DB->where('idx', $ot_idx);
            $DB->where('sh_idx', $sh_idx);
            $okO = $DB->update('orders_t', $updOrder);

            if (!$okO) {
                if (method_exists($DB, 'rollback')) $DB->rollback();
                echo json_encode(['success'=>false,'message'=>'주문 취소 처리 실패: '.$DB->getLastError()], JSON_UNESCAPED_UNICODE);
                exit;
            }

            if (!empty($order['ot_number']) && $refundable > 0) {
                $paymentId = (string)$payRow['imp_uid'];
                $res = cancelPortonePayment($paymentId, '예약 취소', $refundable);
            }
        }

        // 4) 후결제 + 취소/거절 => 연결 주문 취소
        else if ($needPostpaidOrderCancel) {
            $DB->where('idx', $ot_idx);
            $DB->where('sh_idx', $sh_idx);
            $okO = $DB->update('orders_t', [
                'ot_status'        => 'CANCELLED',
                'ot_cancel'        => $DB->now(),
                'ot_cancel_reason' => $reason,
                'ot_udate'         => $DB->now(),
            ]);

            if (!$okO) {
                if (method_exists($DB, 'rollback')) $DB->rollback();
                echo json_encode([
                    'success' => false,
                    'message' => '후결제 주문 취소 처리 실패: ' . $DB->getLastError()
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }
        }

        if (method_exists($DB, 'commit')) $DB->commit();

        echo json_encode(['success'=>true,'message'=>$msg], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        error_log('rv_action error: '.$e->getMessage());
        if (isset($DB) && method_exists($DB, 'rollback')) $DB->rollback();
        http_response_code(500);
        echo json_encode(['success'=>false,'message'=>'서버 오류'], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

if ($act === 'rv_pay_cancel') {
    try {
        global $DB;

        $rv_idx = (int)($_POST['rv_idx'] ?? 0);
        $reason = trim((string)($_POST['reason'] ?? '가맹점주 결제취소'));

        if ($rv_idx <= 0) {
            echo json_encode(['success'=>false,'message'=>'rv_idx 누락'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $DB->where('idx', $rv_idx);
        $DB->where('sh_idx', $sh_idx);
        $rv = $DB->getOne('reservation_t', 'idx, sh_idx, rv_type, rv_status, ot_idx');
        if (!$rv) {
            echo json_encode(['success'=>false,'message'=>'예약을 찾을 수 없습니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        if (($rv['rv_type'] ?? '') !== 'PREPAID' || empty($rv['ot_idx'])) {
            echo json_encode(['success'=>false,'message'=>'선결제 예약이 아닙니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $st = strtoupper((string)($rv['rv_status'] ?? ''));
        if (!in_array($st, ['PENDING','CONFIRMED'], true)) {
            echo json_encode(['success'=>false,'message'=>'현재 상태에서는 결제 취소가 불가능합니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $ot_idx = (int)$rv['ot_idx'];

        $DB->where('idx', $ot_idx);
        $DB->where('sh_idx', $sh_idx);
        $order = $DB->getOne('orders_t', 'idx, ot_total_price, ot_discount_amount, ot_pay_status');
        if (!$order) {
            echo json_encode(['success'=>false,'message'=>'주문을 찾을 수 없습니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $payStatus = strtoupper((string)($order['ot_pay_status'] ?? ''));
        if ($payStatus !== 'PAID') {
            echo json_encode(['success'=>false,'message'=>'결제 완료된 주문만 취소 가능합니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $total = (int)($order['ot_total_price'] ?? 0);
        $discount = (int)($order['ot_discount_amount'] ?? 0);
        $paid = max(0, $total - $discount);

        // 이미 환불된 금액
        $DB->where('ot_idx', $ot_idx);
        $DB->where('sh_idx', $sh_idx);
        $DB->where('status', 'APPROVED');
        $row = $DB->getOne('payment_refunds_t', 'SUM(approved_amount) AS s');
        $refunded = (int)($row['s'] ?? 0);

        $refundable = max(0, $paid - $refunded);
        if ($refundable <= 0) {
            echo json_encode(['success'=>false,'message'=>'이미 전액 환불 처리된 주문입니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // ✅ 환불 이력 추가 (PG 미연동이므로 APPROVED로 기록만)
        $insert = [
            'pay_idx'         => 0,
            'ot_idx'          => $ot_idx,
            'sh_idx'          => $sh_idx,
            'refund_type'     => 'FULL',
            'request_amount'  => $refundable,
            'approved_amount' => $refundable,
            'reason'          => $reason,
            'requested_by'    => (int)($_SESSION['_mt_idx'] ?? 0),
            'requested_ip'    => $_SERVER['REMOTE_ADDR'] ?? '',
            'imp_uid'         => '',
            'status'          => 'APPROVED',
            'requested_at'    => $DB->now(),
            'processed_at'    => $DB->now(),
            'pg_payload'      => json_encode(['note'=>'TODO: portone cancel call'], JSON_UNESCAPED_UNICODE),
        ];

        $ok = $DB->insert('payment_refunds_t', $insert);
        if (!$ok) {
            echo json_encode(['success'=>false,'message'=>'환불 이력 저장 실패: '.$DB->getLastError()], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // ✅ 주문 취소 처리
        $DB->where('idx', $ot_idx);
        $DB->where('sh_idx', $sh_idx);
        $ok2 = $DB->update('orders_t', [
            'ot_status' => 'CANCELLED',
            'ot_udate'  => $DB->now(),
        ]);
        if (!$ok2) {
            echo json_encode(['success'=>false,'message'=>'주문 상태 변경 실패: '.$DB->getLastError()], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // ✅ 예약 취소 처리
        $DB->where('idx', $rv_idx);
        $DB->where('sh_idx', $sh_idx);
        $ok3 = $DB->update('reservation_t', [
            'rv_status' => 'CANCELLED',
            'rv_memo'   => $reason,
            'rv_udate'  => $DB->now(),
        ]);
        if (!$ok3) {
            echo json_encode(['success'=>false,'message'=>'예약 상태 변경 실패: '.$DB->getLastError()], JSON_UNESCAPED_UNICODE);
            exit;
        }

        echo json_encode(['success'=>true], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        error_log('rv_pay_cancel error: '.$e->getMessage());
        http_response_code(500);
        echo json_encode(['success'=>false,'message'=>'서버 오류'], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

if ($act === 'pay_refund') {
    try {
        global $DB;

        $ot_idx = (int)($_POST['ot_idx'] ?? 0);
        $amount = (int)preg_replace('/[^0-9]/', '', (string)($_POST['amount'] ?? '0'));

        if ($ot_idx <= 0 || $amount <= 0) {
            echo json_encode(['success'=>false,'message'=>'요청값 누락'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        if (method_exists($DB, 'startTransaction')) $DB->startTransaction();

        // 1) 예약 찾기 (선결제 예약이어야 함)
        $DB->where('sh_idx', $sh_idx);
        $DB->where('ot_idx', $ot_idx);
        $DB->where('rv_type', 'PREPAID');
        $rv = $DB->getOne('reservation_t', 'idx, sh_idx, rv_type, rv_status, rv_name, rv_hp, ot_idx');

        if (!$rv) {
            if (method_exists($DB, 'rollback')) $DB->rollback();
            echo json_encode(['success'=>false,'message'=>'선결제 예약 정보를 찾을 수 없습니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $rv_idx    = (int)($rv['idx'] ?? 0);
        $rv_status = strtoupper((string)($rv['rv_status'] ?? 'PENDING'));

        // ✅ 요구사항: 예약확정일 경우만 결제취소 가능
        if ($rv_status !== 'CONFIRMED') {
            if (method_exists($DB, 'rollback')) $DB->rollback();
            echo json_encode(['success'=>false,'message'=>'예약확정 상태에서만 결제취소(환불)가 가능합니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // 2) 주문 조회
        $DB->where('idx', $ot_idx);
        $DB->where('sh_idx', $sh_idx);
        $order = $DB->getOne('orders_t', 'idx, sh_idx, ot_status, ot_total_price, ot_pay_status');

        if (!$order) {
            if (method_exists($DB, 'rollback')) $DB->rollback();
            echo json_encode(['success'=>false,'message'=>'주문을 찾을 수 없습니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $otStatus  = strtoupper((string)($order['ot_status'] ?? 'PENDING'));
        $payStatus = strtoupper((string)($order['ot_pay_status'] ?? 'UNPAID'));

        // ✅ 결제 완료된 주문만 환불 가능
        if ($payStatus !== 'PAID') {
            if (method_exists($DB, 'rollback')) $DB->rollback();
            echo json_encode(['success'=>false,'message'=>'결제 완료된 주문만 환불 가능합니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // ✅ "현재 상태에서는 환불할 수 없습니다." 이슈 방지
        // 예약 선결제는 PREPARING 강제하지 말고, 아래 상태는 허용(필요시 추가 가능)
        $allowStatuses = ['CONFIRMED', 'PREPARING', 'PENDING', 'COMPLETED'];
        if (!in_array($otStatus, $allowStatuses, true)) {
            if (method_exists($DB, 'rollback')) $DB->rollback();
            echo json_encode(['success'=>false,'message'=>'현재 상태에서는 환불할 수 없습니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $totalPrice = (int)($order['ot_total_price'] ?? 0);

        // 3) 기 환불 합계
        $DB->where('ot_idx', $ot_idx);
        $DB->where('sh_idx', $sh_idx);
        $DB->where('status', 'APPROVED');
        $row = $DB->getOne('payment_refunds_t', 'SUM(approved_amount) AS s');
        $refunded = (int)($row['s'] ?? 0);

        $refundable = max(0, $totalPrice - $refunded);

        if ($refundable <= 0) {
            if (method_exists($DB, 'rollback')) $DB->rollback();
            echo json_encode(['success'=>false,'message'=>'이미 전액 환불된 주문입니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        if ($amount > $refundable) {
            if (method_exists($DB, 'rollback')) $DB->rollback();
            echo json_encode(['success'=>false,'message'=>'환불 가능 금액을 초과했습니다. (가능: '.$refundable.'원)'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $type = ($amount >= $refundable) ? 'FULL' : 'PARTIAL';

        // 4) 환불 이력 저장 (PG 미연동, 포장 환불과 동일)
        $insert = [
            'pay_idx'         => 0,
            'ot_idx'          => $ot_idx,
            'sh_idx'          => $sh_idx,
            'refund_type'     => $type,
            'request_amount'  => $amount,
            'approved_amount' => $amount,
            'reason'          => '가맹점주 환불(예약)',
            'requested_by'    => (int)($_SESSION['_mt_idx'] ?? 0),
            'requested_ip'    => $_SERVER['REMOTE_ADDR'] ?? '',
            'imp_uid'         => '',
            'status'          => 'APPROVED',
            'requested_at'    => $DB->now(),
            'processed_at'    => $DB->now(),
            'pg_payload'      => json_encode(['note'=>'NO_PG_LINKED'], JSON_UNESCAPED_UNICODE),
        ];

        $ok = $DB->insert('payment_refunds_t', $insert);
        if (!$ok) {
            if (method_exists($DB, 'rollback')) $DB->rollback();
            echo json_encode(['success'=>false,'message'=>'환불 이력 저장 실패: '.$DB->getLastError()], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // 5) 전액 환불이면 주문 취소 + 예약 취소 동시 처리
        if ($type === 'FULL') {

            // orders_t 취소 처리
            $updOrder = [
                'ot_status'        => 'CANCELLED',
                'ot_cancel'        => $DB->now(),
                'ot_cancel_reason' => '전액 환불 처리(예약)',
                'ot_udate'         => $DB->now(),
            ];

            $DB->where('idx', $ot_idx);
            $DB->where('sh_idx', $sh_idx);
            $uok = $DB->update('orders_t', $updOrder);
            if (!$uok) {
                if (method_exists($DB, 'rollback')) $DB->rollback();
                echo json_encode(['success'=>false,'message'=>'주문 취소 업데이트 실패: '.$DB->getLastError()], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // reservation_t 취소 처리
            $updRv = [
                'rv_status'        => 'CANCELLED',
                'rv_cancel_reason' => '전액 환불로 예약 취소',
                'rv_cancel_at'     => $DB->now(),
                'rv_udate'         => $DB->now(),
            ];

            $DB->where('idx', $rv_idx);
            $DB->where('sh_idx', $sh_idx);
            $rok = $DB->update('reservation_t', $updRv);
            if (!$rok) {
                if (method_exists($DB, 'rollback')) $DB->rollback();
                echo json_encode(['success'=>false,'message'=>'예약 취소 업데이트 실패: '.$DB->getLastError()], JSON_UNESCAPED_UNICODE);
                exit;
            }
        }

        if (method_exists($DB, 'commit')) $DB->commit();

        // 6) 응답: 프론트에서 즉시 금액 갱신할 수 있게 계산값 내려줌
        $newRefunded = $refunded + $amount;
        $newRemain   = max(0, $totalPrice - $newRefunded);

        $msg = ($type === 'FULL')
            ? '전액 환불 처리되어 예약이 취소되었습니다.'
            : '부분 환불 처리되었습니다.';

        echo json_encode([
            'success' => true,
            'message' => $msg,
            'data' => [
                'refund_type'      => $type,
                'ot_idx'           => $ot_idx,
                'rv_idx'           => $rv_idx,
                'total_price'      => $totalPrice,
                'refunded_total'   => $newRefunded,
                'paid_remaining'   => $newRemain, // ✅ "결제완료금액 차감"에 사용
                'refundable_before'=> $refundable,
                'refunded_now'     => $amount
            ]
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        error_log('pay_refund error: '.$e->getMessage());
        if (isset($DB) && method_exists($DB, 'rollback')) $DB->rollback();
        http_response_code(500);
        echo json_encode(['success'=>false,'message'=>'서버 오류'], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// -------------------------
echo json_encode(['success'=>false,'message'=>'지원하지 않는 act'], JSON_UNESCAPED_UNICODE);
exit;
