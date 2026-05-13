<?php
include $_SERVER['DOCUMENT_ROOT'] . "/cfg/lib.inc.php";
header('Content-Type: application/json; charset=utf-8');

// 세션 값 초기화
$mt_idx = (int)($_SESSION['mng']['mt_idx'] ?? 0);
$st_id  = (int)($_SESSION['cart_store_id'] ?? 0);
$ct_ids = $_SESSION['cart_ct_ids'] ?? [];
$ct_ids = array_values(array_filter(array_map('intval', $ct_ids)));

// =======================================
// 로그인 사용자: 세션 비었으면 DB에서 복원
// =======================================
if ($mt_idx > 0 && ($st_id <= 0 || empty($ct_ids))) {
    $DB->where('mt_idx', $mt_idx);
    $DB->orderBy('idx', 'DESC');
    $latest = $DB->getOne('cart_t', ['st_id']);

    $db_st_id = (int)($latest['st_id'] ?? 0);

    if ($db_st_id > 0) {
        $st_id = $db_st_id;

        $DB->where('mt_idx', $mt_idx);
        $DB->where('st_id', $st_id);
        $rows = $DB->get('cart_t', null, ['idx', 'ct_quantity']);

        $newCtIds = [];
        $newQty = 0;
        foreach ($rows as $r) {
            $newCtIds[] = (int)$r['idx'];
            $newQty += (int)$r['ct_quantity'];
        }

//        $_SESSION['cart_store_id'] = $st_id;
        $_SESSION['cart_ct_ids']   = $newCtIds;
        $_SESSION['cart_qty']      = $newQty;

        $ct_ids = $newCtIds;
    } else {
        unset($_SESSION['cart_ct_ids']);
        $_SESSION['cart_qty'] = 0;
        $st_id = 0;
        $ct_ids = [];
    }
}

// 공통 파라미터
$act    = $_POST['act'] ?? '';
$ct_idx = (int)($_POST['ct_idx'] ?? 0);

// 기본 유효성 체크
if ($ct_idx <= 0) {
    echo json_encode(['success' => false, 'message' => '항목 정보가 올바르지 않습니다.'], JSON_UNESCAPED_UNICODE);
    exit;
}
if ($st_id <= 0) {
    echo json_encode(['success' => false, 'message' => '장바구니 정보가 없습니다.'], JSON_UNESCAPED_UNICODE);
    exit;
}

// cart row 가져오기 + 소유권 검증
if ($mt_idx > 0) {
    $DB->where('idx', $ct_idx);
    $DB->where('mt_idx', $mt_idx);
    $DB->where('st_id', $st_id);
    $cart = $DB->getOne('cart_t');
} else {
    if (!in_array($ct_idx, $ct_ids, true)) {
        echo json_encode(['success' => false, 'message' => '잘못된 접근입니다.'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    $DB->where('idx', $ct_idx);
    $DB->where('st_id', $st_id);
    $cart = $DB->getOne('cart_t');
}

if (!$cart) {
    echo json_encode(['success' => false, 'message' => '장바구니 항목을 찾을 수 없습니다.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$sm_id = (int)$cart['sm_id'];

// 메뉴 유효성 체크 (판매중, 노출, 재고)
$DB->where('idx', $sm_id);
$DB->where('sm_show', 'Y');
$menu = $DB->getOne('shop_menu_t');

if (!$menu) {
    echo json_encode(['success' => false, 'message' => '메뉴 정보를 찾을 수 없습니다.'], JSON_UNESCAPED_UNICODE);
    exit;
}
if (($menu['sm_type'] ?? 'Y') === 'N') {
    echo json_encode(['success' => false, 'message' => '현재 판매중인 메뉴가 아닙니다.'], JSON_UNESCAPED_UNICODE);
    exit;
}

// =======================================
// 전체 장바구니 합계 재계산 함수
// =======================================
function recalculateCartTotals($mt_idx, $st_id, $DB) {
    $totalQty = 0;
    $totalPrice = 0;
    $rows = [];

    if ($mt_idx > 0) {
        $DB->where('mt_idx', $mt_idx);
        $DB->where('st_id', $st_id);
        $rows = $DB->get('cart_t', null, ['ct_quantity', 'ct_total_price']);
    } else {
        $remaining_ct_ids = $_SESSION['cart_ct_ids'] ?? [];
        if (!empty($remaining_ct_ids)) {
            $DB->where('idx', $remaining_ct_ids, 'IN');
            $DB->where('st_id', $st_id);
            $rows = $DB->get('cart_t', null, ['ct_quantity', 'ct_total_price']);
        }
    }

    foreach ($rows as $r) {
        $totalQty   += (int)$r['ct_quantity'];
        $totalPrice += (int)$r['ct_total_price'];
    }

    // 세션 수량 동기화
    if ($totalQty > 0) {
        $_SESSION['cart_qty'] = $totalQty;
    } else {
        unset($_SESSION['cart_qty']);
    }

    return ['total_qty' => $totalQty, 'total_price' => $totalPrice];
}

// =======================================
// 1. 옵션 모달 데이터 가져오기
// =======================================
if ($act === 'get_modal') {
    try {
        // 옵션 카테고리 (oc_su 추가!)
        $DB->where('sm_idx', $sm_id);
        $DB->where('oc_show', 'Y');
        $DB->orderBy('oc_order', 'ASC');
        $cats = $DB->get('menu_option_category_t', null, [
            'idx', 'oc_title', 'oc_check', 'oc_su'  // ← oc_su 추가
        ]);

        $catIds = array_column($cats ?: [], 'idx');
        $optsByCat = [];

        if (!empty($catIds)) {
            $DB->where('oc_idx', $catIds, 'IN');
            $DB->where('om_show', 'Y');
            $DB->orderBy('om_order', 'ASC');
            $opts = $DB->get('option_menu_t', null, ['idx', 'oc_idx', 'om_title', 'om_price']);

            foreach ($opts as $o) {
                $optsByCat[(int)$o['oc_idx']][] = $o;
            }
        }

        // 현재 선택된 옵션
        $DB->where('ct_idx', $ct_idx);
        $chosen = $DB->get('cart_options_t', null, ['oc_idx', 'om_idx']);

        $chosenMap = [];
        foreach ($chosen as $ch) {
            $chosenMap[(int)$ch['oc_idx']][] = (int)$ch['om_idx'];
        }

        echo json_encode([
            'success' => true,
            'data' => [
                'ct_idx'         => $ct_idx,
                'sm_id'          => $sm_id,
                'sm_title'       => $menu['sm_title'] ?? '',
                'qty'            => (int)$cart['ct_quantity'],
                'base_price'     => (int)$menu['sm_price'],
                'unit_price'     => (int)$cart['ct_price'],
                'total_price'    => (int)$cart['ct_total_price'],
                'categories'     => $cats ?: [],          // oc_su 포함됨
                'options_by_cat' => $optsByCat,
                'chosen'         => $chosenMap,
            ]
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// =======================================
// 2. 옵션 + 수량 적용
// =======================================
else if ($act === 'apply') {
    try {
        $qty = max(1, (int)($_POST['qty'] ?? 1));
        $opt = $_POST['opt'] ?? [];

        // 선택된 옵션 추출 + oc_idx별 선택 개수 집계
        $picked = [];
        $pickedByOc = [];  // ← 핵심: oc_idx별 선택 개수

        if (is_array($opt)) {
            foreach ($opt as $ocIdxStr => $arr) {
                $ocIdx = (int)$ocIdxStr;  // 키가 문자열로 올 수 있으니 강제 int
                if ($ocIdx <= 0) continue;

                if (!is_array($arr)) $arr = [$arr];
                $arr = array_filter(array_map('intval', $arr)); // 안전하게 정수화

                $pickedByOc[$ocIdx] = $arr;  // 배열 자체 저장 (중복 제거 용이)
                $picked = array_merge($picked, $arr);
            }
        }
        $picked = array_unique(array_filter($picked));  // 최종 선택 om_idx 목록

        // 필수 옵션 카테고리 + oc_su 조회
        $DB->where('sm_idx', $sm_id);
        $DB->where('oc_show', 'Y');
        $DB->where('oc_check', 'Y');
        $requiredCats = $DB->get('menu_option_category_t', null, ['idx as oc_idx', 'oc_su']);

        // 필수 옵션 개수 검증 (정확히 oc_su 개수 선택)
        if (!empty($requiredCats)) {
            foreach ($requiredCats as $rc) {
                $reqOcIdx = (int)$rc['oc_idx'];
                $reqSu    = (int)$rc['oc_su'];

                if ($reqSu <= 0) continue;  // oc_su가 0이면 무시 (비필수)

                $selectedArr = $pickedByOc[$reqOcIdx] ?? [];
                $selectedCount = count(array_unique($selectedArr));  // 중복 제거 후 개수

                if ($selectedCount !== $reqSu) {
                    throw new Exception("필수 옵션을 정확히 {$reqSu}개 선택해주세요. (현재: {$selectedCount}개)");
                }
            }
        }

        // 옵션 유효성 검증 + 가격 합산
        $optRows = [];
        $optPriceSum = 0;
        if (!empty($picked)) {
            $DB->join('menu_option_category_t oc', 'om.oc_idx = oc.idx', 'INNER');
            $DB->where('oc.sm_idx', $sm_id);
            $DB->where('oc.oc_show', 'Y');
            $DB->where('om.om_show', 'Y');
            $DB->where('om.idx', $picked, 'IN');

            $optRows = $DB->get('option_menu_t om', null, [
                'om.idx as om_idx',
                'oc.idx as oc_idx',
                'om.om_title',
                'om.om_price'
            ]);

            foreach ($optRows as $r) {
                $optPriceSum += (int)$r['om_price'];
            }
        }

        $basePrice  = (int)$menu['sm_price'];
        $unitPrice  = $basePrice + $optPriceSum;
        $totalPrice = $unitPrice * $qty;

        $DB->startTransaction();

        // 기존 옵션 삭제 후 재삽입
        $DB->where('ct_idx', $ct_idx);
        $DB->delete('cart_options_t');

        foreach ($optRows as $r) {
            $DB->insert('cart_options_t', [
                'ct_idx'           => $ct_idx,
                'om_idx'           => (int)$r['om_idx'],
                'oc_idx'           => (int)$r['oc_idx'],
                'co_option_name'   => $r['om_title'],
                'co_option_price'  => (int)$r['om_price'],
                'co_wdate'         => $DB->now(),
            ]);
        }

        // cart 항목 갱신
        $DB->where('idx', $ct_idx);
        $DB->update('cart_t', [
            'ct_quantity'    => $qty,
            'ct_price'       => $unitPrice,
            'ct_total_price' => $totalPrice,
            'ct_udate'       => $DB->now(),
        ]);

        $DB->commit();

        // 전체 합계 재계산
        $totals = recalculateCartTotals($mt_idx, $st_id, $DB);

        // 프론트에 보여줄 옵션 리스트
        $viewOpts = array_map(function($r) {
            return [
                'oc_idx' => (int)$r['oc_idx'],
                'om_idx' => (int)$r['om_idx'],
                'title'  => $r['om_title'],
                'price'  => (int)$r['om_price']
            ];
        }, $optRows);

        echo json_encode([
            'success' => true,
            'message' => '옵션이 변경되었습니다.',
            'data' => [
                'ct_idx'      => $ct_idx,
                'qty'         => $qty,
                'unit_price'  => $unitPrice,
                'item_total'  => $totalPrice,
                'total_qty'   => $totals['total_qty'],
                'total_price' => $totals['total_price'],
                'options'     => $viewOpts,
            ]
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        if ($DB && method_exists($DB, 'rollback')) $DB->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// =======================================
// 3. 수량만 변경
// =======================================
else if ($act === 'update_qty') {
    try {
        $qty = max(1, (int)($_POST['qty'] ?? 1));

        $unitPrice  = (int)$cart['ct_price'];
        $totalPrice = $unitPrice * $qty;

        $DB->startTransaction();

        $DB->where('idx', $ct_idx);
        $ok = $DB->update('cart_t', [
            'ct_quantity'    => $qty,
            'ct_total_price' => $totalPrice,
            'ct_udate'       => $DB->now(),
        ]);

        if (!$ok) throw new Exception('수량 업데이트에 실패했습니다.');

        $DB->commit();

        $totals = recalculateCartTotals($mt_idx, $st_id, $DB);

        echo json_encode([
            'success' => true,
            'message' => '수량이 변경되었습니다.',
            'data' => [
                'ct_idx'      => $ct_idx,
                'qty'         => $qty,
                'item_total'  => $totalPrice,
                'total_qty'   => $totals['total_qty'],
                'total_price' => $totals['total_price'],
            ]
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        if ($DB && method_exists($DB, 'rollback')) $DB->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// =======================================
// 4. 항목 삭제
// =======================================
else if ($act === 'delete') {
    try {
        $DB->startTransaction();

        // 옵션 삭제
        $DB->where('ct_idx', $ct_idx);
        $DB->delete('cart_options_t');

        // 장바구니 항목 삭제
        $DB->where('idx', $ct_idx);
        $ok = $DB->delete('cart_t');
        if (!$ok) throw new Exception('삭제에 실패했습니다.');

        $DB->commit();

        // 세션에서 ct_idx 제거
        if (($key = array_search($ct_idx, $ct_ids)) !== false) {
            unset($ct_ids[$key]);
            $ct_ids = array_values($ct_ids);

            if (empty($ct_ids)) {
                unset($_SESSION['cart_ct_ids'], $_SESSION['cart_qty']);
            } else {
                $_SESSION['cart_ct_ids'] = $ct_ids;
            }
        }

        // 전체 합계 재계산
        $totals = recalculateCartTotals($mt_idx, $st_id, $DB);
        $isEmpty = ($totals['total_qty'] == 0);

        echo json_encode([
            'success' => true,
            'message' => '장바구니에서 삭제되었습니다.',
            'data' => [
                'ct_idx'      => $ct_idx,
                'total_qty'   => $totals['total_qty'],
                'total_price' => $totals['total_price'],
                'cart_empty'  => $isEmpty
            ]
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        if ($DB && method_exists($DB, 'rollback')) $DB->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// 잘못된 act
echo json_encode(['success' => false, 'message' => '잘못된 요청입니다.'], JSON_UNESCAPED_UNICODE);
exit;
