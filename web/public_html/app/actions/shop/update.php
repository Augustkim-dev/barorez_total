<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
header('Content-Type: application/json; charset=utf-8');
$act = $_POST['act'];
if($act === 'add_cart') {
    try {
        // ------------------------------
        // 2) 로그인 여부 (비회원 허용)
        // ------------------------------
        $mt_idx = 0;
        if (!empty($_SESSION['mng']) && !empty($_SESSION['mng']['mt_idx'])) {
            $mt_idx = (int)$_SESSION['mng']['mt_idx'];
        }

        // ------------------------------
        // 3) 입력값
        // ------------------------------
        $sm_id = (int)($_POST['sm_id'] ?? 0);
        $qty   = max(1, (int)($_POST['qty'] ?? 1));
        $opt   = $_POST['opt'] ?? []; // opt[oc_idx][]=om_idx 형태 배열
        $force_clear = ($_POST['force_clear'] ?? 'N') === 'Y';

        if ($sm_id <= 0) {
            throw new Exception('메뉴 정보가 올바르지 않습니다.');
        }

        // ------------------------------
        // 4) 메뉴 + 매장 검증
        // ------------------------------
        $DB->join('shop_category_t c', 'm.sc_idx = c.idx', 'INNER');
        $DB->where('m.idx', $sm_id);
        $DB->where('m.sm_show', 'Y');
        $menu = $DB->getOne('shop_menu_t m', [
            'm.idx as sm_id',
            'm.sm_title',
            'm.sm_price',
            'm.sm_su',
            'm.sm_type',
            'm.sm_show',
            'c.sh_idx as st_id'
        ]);

        if (!$menu) {
            throw new Exception('메뉴 정보를 찾을 수 없습니다.');
        }

        $st_id = (int)($menu['st_id'] ?? 0);
        if ($st_id <= 0) {
            throw new Exception('매장 정보를 찾을 수 없습니다.');
        }

        // 판매중/재고 체크
        if (($menu['sm_show'] ?? 'Y') !== 'Y') throw new Exception('현재 노출되지 않는 메뉴입니다.');
        if (($menu['sm_type'] ?? 'Y') === 'N') throw new Exception('현재 판매중인 메뉴가 아닙니다.');
        // if ((int)($menu['sm_su'] ?? 0) <= 0) throw new Exception('품절된 메뉴입니다.');

        // ------------------------------
        // 5) 매장 1개 정책 - 기존 장바구니 삭제 확인
        // ------------------------------
        $hasCart = false;
        $cartStoreId = 0; // 기존 장바구니에 담긴 매장 키

        if ($mt_idx > 0) {
            // 회원 장바구니 조회
            $DB->where('mt_idx', $mt_idx);
            $cartRow = $DB->getOne('cart_t', ['idx', 'st_id']);

            if (!empty($cartRow)) {
                $hasCart = true;
                $cartStoreId = (int)($cartRow['st_id'] ?? 0);
            }
        } else {
            // 비회원 장바구니 조회
            $ctIds = $_SESSION['cart_ct_ids'] ?? [];
            $ctIds = array_values(array_filter(array_map('intval', $ctIds)));

            if (!empty($ctIds)) {
                $DB->where('idx', $ctIds, 'IN');
                $cartRow = $DB->getOne('cart_t', ['idx', 'st_id']);

                if (!empty($cartRow)) {
                    $hasCart = true;
                    $cartStoreId = (int)($cartRow['st_id'] ?? 0);
                }
            }
        }

        // 기존 장바구니가 있고, 그 매장과 현재 메뉴 매장이 다를 경우 확인
        if ($hasCart && $cartStoreId > 0 && $cartStoreId !== $st_id && !$force_clear) {
            echo json_encode([
                'success'       => false,
                'needs_confirm' => true,
                'code'          => 'DIFF_STORE',
                'message'       => "다른 매장의 메뉴를 담으면\n기존 장바구니가 삭제됩니다. 진행할까요?",
                'data'          => [
                    'current_store_id' => $cartStoreId,
                    'new_store_id'     => $st_id
                ],
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // force_clear 시 기존 장바구니 삭제
        if ($hasCart && $cartStoreId > 0 && $cartStoreId !== $st_id && $force_clear) {
            $DB->startTransaction();

            if ($mt_idx > 0) {
                clearCartByMember($DB, $mt_idx);
            } else {
                $ctIds = $_SESSION['cart_ct_ids'] ?? [];
                clearCartByCtIds($DB, $ctIds);
            }

            $DB->commit();

            $_SESSION['cart_ct_ids'] = [];
            $_SESSION['cart_store_id'] = 0;
            $cartStoreId = 0;
        }

        // ------------------------------
        // 6) 선택 옵션 정리 + 필수 개수 검증
        // ------------------------------
        $pickedOmIds = []; // 선택된 옵션 메뉴 ID 목록
        $pickedByOc = [];  // oc_idx별 선택된 개수 집계

        if (is_array($opt)) {
            foreach ($opt as $oc_idx => $arr) {
                if (!is_array($arr)) $arr = [$arr];
                $oc_idx = (int)$oc_idx;

                $pickedByOc[$oc_idx] = count($arr); // 해당 옵션 카테고리별 선택 개수

                foreach ($arr as $om_id) {
                    $om_id = (int)$om_id;
                    if ($om_id > 0) $pickedOmIds[] = $om_id;
                }
            }
        }
        $pickedOmIds = array_values(array_unique($pickedOmIds));

        // 필수 옵션 카테고리 조회 (oc_check = 'Y')
        $DB->where('sm_idx', $sm_id);
        $DB->where('oc_show', 'Y');
        $DB->where('oc_check', 'Y');
        $requiredCats = $DB->get('menu_option_category_t', null, ['idx as oc_idx', 'oc_su']);

        // 필수 옵션 선택 개수 검증
        if (!empty($requiredCats)) {
            foreach ($requiredCats as $rc) {
                $reqOcIdx = (int)$rc['oc_idx'];
                $reqSu    = (int)$rc['oc_su']; // 필수 선택 개수

                $selectedCount = $pickedByOc[$reqOcIdx] ?? 0;

                if ($selectedCount !== $reqSu) {
                    throw new Exception("필수 옵션을 {$reqSu}개 선택해주세요.");
                }
            }
        }

        // 옵션 유효성 검증 + 가격 합산 (기존 로직 유지)
        $optRows = [];
        $optPriceSum = 0;
        if (!empty($pickedOmIds)) {
            $DB->join('menu_option_category_t oc', 'om.oc_idx = oc.idx', 'INNER');
            $DB->where('oc.sm_idx', $sm_id);
            $DB->where('oc.oc_show', 'Y');
            $DB->where('om.om_show', 'Y');
            $DB->where('om.idx', $pickedOmIds, 'IN');

            $optRows = $DB->get('option_menu_t om', null, [
                'om.idx as om_idx',
                'oc.idx as oc_idx',
                'om.om_title',
                'om.om_price'
            ]);

            foreach ($optRows as $r) {
                $optPriceSum += (int)($r['om_price'] ?? 0);
            }
        }

        $basePrice  = (int)($menu['sm_price'] ?? 0);
        $unitPrice  = $basePrice + $optPriceSum;
        $totalPrice = $unitPrice * $qty;

        // ------------------------------
        // 7) 동일 메뉴+동일 옵션 merge (기존 로직 유지)
        // ------------------------------
        $newSig = makeOptionSignatureFromRows($optRows);

        $candidates = [];
        if ($mt_idx > 0) {
            $DB->where('mt_idx', $mt_idx);
            $DB->where('st_id', $st_id);
            $DB->where('sm_id', $sm_id);
            $candidates = $DB->get('cart_t', null, ['idx','ct_quantity']);
        } else {
            $ctIds = $_SESSION['cart_ct_ids'] ?? [];
            $ctIds = array_values(array_filter(array_map('intval', $ctIds)));
            if (!empty($ctIds)) {
                $DB->where('idx', $ctIds, 'IN');
                $DB->where('st_id', $st_id);
                $DB->where('sm_id', $sm_id);
                $candidates = $DB->get('cart_t', null, ['idx','ct_quantity']);
            }
        }

        $matchedCtIdx = 0;
        if (!empty($candidates)) {
            foreach ($candidates as $c) {
                $ct_idx = (int)$c['idx'];
                $oldSig = getCartOptionSignature($DB, $ct_idx);
                if ($oldSig === $newSig) {
                    $matchedCtIdx = $ct_idx;
                    break;
                }
            }
        }

        // ------------------------------
        // 8) DB 저장 (UPDATE or INSERT) + 옵션 저장
        // ------------------------------
        $DB->startTransaction();

        if ($matchedCtIdx > 0) {
            // UPDATE
            $DB->where('idx', $matchedCtIdx);
            $row = $DB->getOne('cart_t', ['ct_quantity']);
            $oldQty = (int)($row['ct_quantity'] ?? 0);

            $newQty = $oldQty + $qty;
            $newTotal = $unitPrice * $newQty;

            $DB->where('idx', $matchedCtIdx);
            $ok = $DB->update('cart_t', [
                'ct_quantity'    => $newQty,
                'ct_price'       => $unitPrice,
                'ct_total_price' => $newTotal,
                'ct_udate'       => $DB->now(),
            ]);

            if (!$ok) throw new Exception('장바구니 수량 업데이트에 실패했습니다.');

            $ct_idx = $matchedCtIdx;
        } else {
            // INSERT
            $ctData = [
                'mt_idx'         => ($mt_idx > 0 ? $mt_idx : null),
                'st_id'          => $st_id,
                'sm_id'          => $sm_id,
                'ct_quantity'    => $qty,
                'ct_price'       => $unitPrice,
                'ct_total_price' => $totalPrice,
            ];

            $ct_idx = $DB->insert('cart_t', $ctData);
            if (!$ct_idx) throw new Exception('장바구니 저장에 실패했습니다.');

            // 옵션 저장
            if (!empty($optRows)) {
                foreach ($optRows as $r) {
                    $coData = [
                        'ct_idx'          => (int)$ct_idx,
                        'om_idx'          => (int)$r['om_idx'],
                        'oc_idx'          => (int)$r['oc_idx'],
                        'co_option_name'  => $r['om_title'],
                        'co_option_price' => (int)($r['om_price'] ?? 0),
                    ];
                    $ok = $DB->insert('cart_options_t', $coData);
                    if (!$ok) throw new Exception('옵션 저장에 실패했습니다.');
                }
            }
        }

        $DB->commit();

        // ------------------------------
        // 9) 세션 갱신
        // ------------------------------
        $_SESSION['cart_store_id'] = (int)$st_id;
        if (!isset($_SESSION['cart_qty'])) $_SESSION['cart_qty'] = 0;
        $_SESSION['cart_qty'] += $qty;
        if (!isset($_SESSION['cart_ct_ids'])) $_SESSION['cart_ct_ids'] = [];
        if (!in_array((int)$ct_idx, $_SESSION['cart_ct_ids'], true)) {
            $_SESSION['cart_ct_ids'][] = (int)$ct_idx;
        }

        $cartCount = count($_SESSION['cart_ct_ids']);
        $redirect = APP_PAGE;
        if($_SESSION['order_mode']){
            $redirect = '../shop/list.php?sh_idx='.$st_id;
        }

        echo json_encode([
            'success'  => true,
            'message'  => ($matchedCtIdx > 0) ? '동일 메뉴가 있어 수량이 추가되었습니다.' : '장바구니에 담았습니다.',
            'data'     => [
                'ct_idx'     => (int)$ct_idx,
                'cart_count' => (int)$cartCount,
                'st_id'      => (int)$st_id,
            ],
            'test3' => $cnt,
            'tes2' =>$currentStore,
            'test1' =>$force_clear,
            'test4' => $st_id,
            'test5' => $hasCart,
            'redirect' => $redirect,
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
else if ($act === 'change_order_mode') {
    try {
        $new_mode = strtolower(trim($_POST['mode'] ?? ''));
        if (!in_array($new_mode, ['takeout', 'reservation'], true)) {
            throw new Exception('잘못된 주문 모드입니다.');
        }

        // 세션에 저장
        $_SESSION['order_mode'] = $new_mode;

        echo json_encode([
            'success' => true,
            'message' => '주문 모드가 변경되었습니다.',
            'data' => [
                'mode' => $new_mode
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
