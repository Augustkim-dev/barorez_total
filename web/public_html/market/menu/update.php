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

// -------------------------
// menu_category_list
// -------------------------
if ($act === 'menu_category_list') {
    try {
        global $DB;

        $DB->where('sh_idx', $sh_idx);
        $DB->where('sc_del IS NULL');
        $DB->orderBy('sc_order', 'DESC');
        $DB->orderBy('idx', 'ASC');
        $categories = $DB->get('shop_category_t', null, '
            idx, sc_title, sc_show, sc_order, sc_memo
        ');

        $result = [];
        $catIds = [];
        foreach ($categories as $row) {
            $idx = (int)$row['idx'];
            $catIds[] = $idx;
            $result[] = [
                'idx'       => $idx,
                'title'     => (string)$row['sc_title'],
                'show'      => $row['sc_show'] === 'Y',
                'order'     => (int)$row['sc_order'],
                'memo'      => (string)$row['sc_memo'],
                'menu_count'=> 0,
            ];
        }

        // 모든 메뉴 조회 → PHP에서 카운트
        $menusAll = [];
        if (!empty($catIds)) {
            $DB->where('sc_idx', $catIds, 'IN');
            // $DB->where('sm_show', 'Y');   // 관리자 화면 → 주석 처리
            $menusAll = $DB->get('shop_menu_t');
        }

        $countMap = array_fill_keys($catIds, 0);
        foreach ($menusAll as $menu) {
            $sc_idx = (int)$menu['sc_idx'];
            if (isset($countMap[$sc_idx])) {
                $countMap[$sc_idx]++;
            }
        }

        foreach ($result as &$cat) {
            $cat['menu_count'] = $countMap[$cat['idx']] ?? 0;
        }
        unset($cat);

        echo json_encode([
            'success'     => true,
            'total_count' => count($categories),
            'categories'  => $result,
            'all_count'   => count($menusAll),
        ], JSON_UNESCAPED_UNICODE);

    } catch (Exception $e) {
        error_log("menu_category_list error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success'=>false, 'message'=>'서버 오류'], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

// -------------------------
// menu_list_by_category
// -------------------------
if ($act === 'menu_list_by_category') {
    try {
        global $DB;

        $sc_idx  = (int)($_POST['sc_idx'] ?? 0);
        $keyword = trim((string)($_POST['keyword'] ?? ''));

        // 1. 모든 카테고리 조회 (메뉴 유무와 상관없이)
        $DB->where('sh_idx', $sh_idx);
        $DB->where('sc_del IS NULL');
        $DB->orderBy('sc_order', 'DESC');
        $DB->orderBy('idx', 'ASC');
        $all_categories = $DB->get('shop_category_t', null, '
            idx, sc_title, sc_show, sc_order
        ');

        $category_map = [];
        $category_titles = [];
        foreach ($all_categories as $cat) {
            $idx = (int)$cat['idx'];
            $category_map[$idx] = [
                'idx'        => $idx,
                'title'      => (string)$cat['sc_title'],
                'menu_count' => 0,
                'menus'      => []
            ];
            $category_titles[$idx] = (string)$cat['sc_title'];
        }

        // 2. 메뉴 조회
        if ($sc_idx > 0) {
            $DB->where('sc_idx', $sc_idx);
        }
        if ($keyword !== '') {
            $DB->where('sm_title', "%{$keyword}%", 'LIKE');
        }
        $DB->orderBy('sm_order', 'ASC');
        $DB->orderBy('idx', 'ASC');

        $menus = $DB->get('shop_menu_t', null, '
            idx, sc_idx, sm_title, sm_image, sm_contents, 
            sm_price, sm_su, sm_type, sm_show, sm_order, sm_age_show
        ');

        // 3. 옵션 데이터 조회 준비
        $menuIds = array_column($menus, 'idx');
        $optionCategories = [];   // [sm_idx => [ ... ]]
        $optionsByCategory   = []; // [oc_idx => [ ... ]]

        if (!empty($menuIds)) {
            // 옵션 카테고리
            $DB->where('sm_idx', $menuIds, 'IN');
            $DB->orderBy('oc_order', 'ASC');
            $DB->orderBy('idx', 'ASC');
            $ocRows = $DB->get('menu_option_category_t', null, '
                idx, sm_idx, oc_title, oc_check, oc_show, oc_order
            ');

            $ocIds = [];
            foreach ($ocRows as $oc) {
                $sm_idx = (int)$oc['sm_idx'];
                $oc_idx = (int)$oc['idx'];

                if (!isset($optionCategories[$sm_idx])) {
                    $optionCategories[$sm_idx] = [];
                }

                $optionCategories[$sm_idx][] = [
                    'oc_idx'   => $oc_idx,
                    'title'    => (string)$oc['oc_title'],
                    'required' => $oc['oc_check'] === 'Y',
                    'show'     => $oc['oc_show'] === 'Y',
                    'order'    => (int)$oc['oc_order'],
                ];

                $ocIds[] = $oc_idx;
            }

            // 옵션 아이템
            if (!empty($ocIds)) {
                $DB->where('oc_idx', $ocIds, 'IN');
                $DB->orderBy('om_order', 'ASC');
                $DB->orderBy('idx', 'ASC');
                $omRows = $DB->get('option_menu_t', null, '
                    idx, oc_idx, om_title, om_price, om_show, om_order
                ');

                foreach ($omRows as $om) {
                    $oc_idx = (int)$om['oc_idx'];
                    if (!isset($optionsByCategory[$oc_idx])) {
                        $optionsByCategory[$oc_idx] = [];
                    }
                    $optionsByCategory[$oc_idx][] = [
                        'om_idx' => (int)$om['idx'],
                        'title'  => (string)$om['om_title'],
                        'price'  => (int)$om['om_price'],
                        'show'   => $om['om_show'] === 'Y',
                        'order'  => (int)$om['om_order'],
                    ];
                }
            }
        }

        // 4. 메뉴에 옵션 붙이기 + 카테고리 배정
        foreach ($menus as $m) {
            $menu_sc_idx = (int)$m['sc_idx'];
            $menu_idx    = (int)$m['idx'];
            $imgSrc = $m['sm_image'] ? '/data/menu/'.$m['sm_image'] : '';

            if (!isset($category_map[$menu_sc_idx])) continue;

            $menuData = [
                'idx'        => $menu_idx,
                'sc_idx'     => $menu_sc_idx,
                'title'      => (string)$m['sm_title'],
                'age'        => $m['sm_age_show'] === 'N' ? '' : '19세이상',
                'image'      => $imgSrc,
                'contents'   => (string)$m['sm_contents'],
                'price'      => (int)$m['sm_price'],
                'stock'      => (int)$m['sm_su'],
                'sold_out'   => $m['sm_type'] !== 'Y',
                'visible'    => $m['sm_show'] === 'Y',
                'order'      => (int)$m['sm_order'],
                'options'    => [],
            ];

            // 옵션 카테고리 + 하위 옵션 붙이기
            if (isset($optionCategories[$menu_idx])) {
                foreach ($optionCategories[$menu_idx] as $oc) {
                    $oc_idx = $oc['oc_idx'];
                    $oc['options'] = $optionsByCategory[$oc_idx] ?? [];
                    $menuData['options'][] = $oc;
                }
            }

            $category_map[$menu_sc_idx]['menus'][] = $menuData;
            $category_map[$menu_sc_idx]['menu_count']++;
        }

        // 5. 출력할 카테고리 선택
        $display_categories = [];
        if ($sc_idx > 0) {
            if (isset($category_map[$sc_idx])) {
                $display_categories[$sc_idx] = $category_map[$sc_idx];
            }
        } else {
            $display_categories = $category_map;
        }

        // 6. 평탄화된 menus (기존 프론트 호환용)
        $flat_menus = [];
        foreach ($display_categories as $cat) {
            foreach ($cat['menus'] as $menu) {
                $flat_menus[] = $menu;
            }
        }

        echo json_encode([
            'success'         => true,
            'menus'           => $flat_menus,
            'count'           => count($flat_menus),
            'categories'      => array_values($display_categories),
            'category_titles' => $category_titles,
        ], JSON_UNESCAPED_UNICODE);

    } catch (Exception $e) {
        error_log("menu_list_by_category error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success'=>false, 'message'=>'서버 오류'], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

if ($act === 'menu_toggle_sale') {
    try {
        global $DB;

        $menu_idx = (int)($_POST['menu_idx'] ?? 0);
        $is_sale  = ($_POST['is_sale'] ?? 'N') === 'Y' ? 'Y' : 'N';

        if ($menu_idx <= 0) {
            echo json_encode(['success' => false, 'message' => '메뉴 ID가 없습니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $DB->where('idx', $menu_idx);

        $ok = $DB->update('shop_menu_t', [
            'sm_type' => $is_sale,
            'sm_udate' => $DB->now()
        ]);

        if (!$ok) {
            echo json_encode(['success' => false, 'message' => '상태 변경 실패: ' . $DB->getLastError()], JSON_UNESCAPED_UNICODE);
            exit;
        }

        echo json_encode([
            'success' => true,
            'message' => $is_sale === 'Y' ? '판매중으로 변경되었습니다.' : '판매중지(품절)로 변경되었습니다.',
            'new_state' => $is_sale
        ], JSON_UNESCAPED_UNICODE);

    } catch (Exception $e) {
        error_log("menu_toggle_sale error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => '서버 오류'], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

// -------------------------
// menu_category_add
// -------------------------
if ($act === 'menu_category_add') {
    try {
        global $DB;

        $title = trim($_POST['title'] ?? '');
        $order = (int)($_POST['order'] ?? 1);
        $show  = ($_POST['show'] ?? 'Y') === 'Y' ? 'Y' : 'N';

        if ($title === '') {
            echo json_encode(['success'=>false, 'message'=>'카테고리명을 입력해주세요.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        if ($order <= 0) {
            $max = $DB->getValue('shop_category_t', 'MAX(sc_order)', ['sh_idx'=>$sh_idx, 'sc_del IS NULL']);
            $order = ($max ? (int)$max : 0) + 1;
        }

        $data = [
            'sh_idx'   => $sh_idx,
            'sc_title' => $title,
            'sc_order' => $order,
            'sc_show'  => $show,
            'sc_wdate' => $DB->now(),
            'sc_udate' => $DB->now(),
        ];

        $newId = $DB->insert('shop_category_t', $data);
        if (!$newId) {
            echo json_encode(['success'=>false, 'message'=>'추가 실패'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        echo json_encode([
            'success' => true,
            'message' => '카테고리가 추가되었습니다.',
            'category' => [
                'idx'   => (int)$newId,
                'title' => $title,
                'show'  => $show === 'Y',
                'order' => $order,
                'menu_count' => 0
            ]
        ], JSON_UNESCAPED_UNICODE);

    } catch (Exception $e) {
        error_log("menu_category_add error: ".$e->getMessage());
        echo json_encode(['success'=>false, 'message'=>'서버 오류'], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

// -------------------------
// menu_category_update
// -------------------------
if ($act === 'menu_category_update') {
    try {
        global $DB;

        $cat_idx = (int)($_POST['cat_idx'] ?? 0);
        $title   = trim($_POST['title'] ?? '');
        $order   = (int)($_POST['order'] ?? 1);
        $show    = ($_POST['show'] ?? 'Y') === 'Y' ? 'Y' : 'N';

        if ($cat_idx <= 0 || $title === '') {
            echo json_encode(['success'=>false, 'message'=>'필수 값 누락'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $DB->where('idx', $cat_idx);
        $DB->where('sh_idx', $sh_idx);
        $DB->where('sc_del IS NULL');

        $ok = $DB->update('shop_category_t', [
            'sc_title' => $title,
            'sc_order' => $order,
            'sc_show'  => $show,
            'sc_udate' => $DB->now()
        ]);

        if (!$ok) {
            echo json_encode(['success'=>false, 'message'=>'수정 실패'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        echo json_encode([
            'success' => true,
            'message' => '카테고리가 수정되었습니다.',
            'category' => [
                'idx'   => $cat_idx,
                'title' => $title,
                'show'  => $show === 'Y',
                'order' => $order
            ]
        ], JSON_UNESCAPED_UNICODE);

    } catch (Exception $e) {
        error_log("menu_category_update error: ".$e->getMessage());
        echo json_encode(['success'=>false, 'message'=>'서버 오류'], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

if ($act === 'menu_category_delete') {
    try {
        global $DB;

        $cat_idx = (int)($_POST['cat_idx'] ?? 0);

        if ($cat_idx <= 0) {
            echo json_encode(['success'=>false, 'message'=>'카테고리 ID가 없습니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // 해당 카테고리에 메뉴가 있는지 확인 (메뉴 있으면 삭제 불가)
//        $DB->where('sc_idx', $cat_idx);
//        $DB->where('sc_del IS NULL');
//        $menuCount = $DB->getValue('shop_menu_t', 'COUNT(*)');
//
//        if ($menuCount > 0) {
//            echo json_encode(['success'=>false, 'message'=>'해당 카테고리에 메뉴가 있어 삭제할 수 없습니다.'], JSON_UNESCAPED_UNICODE);
//            exit;
//        }

        $DB->where('idx', $cat_idx);
        $DB->where('sh_idx', $sh_idx);
        $DB->where('sc_del IS NULL');

        $ok = $DB->update('shop_category_t', [
            'sc_del'   => $DB->now(),
            'sc_udate' => $DB->now()
        ]);

        if (!$ok) {
            echo json_encode(['success'=>false, 'message'=>'삭제 실패'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        echo json_encode([
            'success' => true,
            'message' => '카테고리가 삭제되었습니다.'
        ], JSON_UNESCAPED_UNICODE);

    } catch (Exception $e) {
        error_log("menu_category_delete error: ".$e->getMessage());
        echo json_encode(['success'=>false, 'message'=>'서버 오류'], JSON_UNESCAPED_UNICODE);
    }
    exit;
}
// 그 외 act는 지원 안 함
echo json_encode(['success'=>false, 'message'=>'지원하지 않는 act'], JSON_UNESCAPED_UNICODE);
exit;
