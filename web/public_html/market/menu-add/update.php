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

// ────────────────────────────────────────────────
// 1. 카테고리 목록 가져오기 (셀렉트 박스용)
// ────────────────────────────────────────────────
if ($act === 'get_categories') {
    try {
        $DB->where('sh_idx', $sh_idx);
        $DB->where('sc_del IS NULL');
        $DB->orderBy('sc_order', 'DESC');
        $DB->orderBy('idx', 'ASC');
        $rows = $DB->get('shop_category_t', null, 'idx, sc_title');

        $categories = [];
        foreach ($rows as $r) {
            $categories[] = [
                'idx'   => (int)$r['idx'],
                'title' => (string)$r['sc_title']
            ];
        }

        echo json_encode([
            'success'    => true,
            'categories' => $categories
        ], JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        echo json_encode(['success'=>false, 'message'=>'서버 오류'], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

// ────────────────────────────────────────────────
// 2. 메뉴 상세 정보 가져오기 (수정용)
// ────────────────────────────────────────────────
if ($act === 'get_menu_detail') {
    try {
        $menu_idx = (int)($_POST['menu_idx'] ?? 0);
        if ($menu_idx <= 0) {
            echo json_encode(['success' => false, 'message' => '메뉴 ID가 없습니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $DB->where('idx', $menu_idx);
        $menu = $DB->getOne('shop_menu_t', '
            idx, sc_idx, sm_title, sm_image, sm_contents, 
            sm_price, sm_type, sm_age_show, sm_main
        ');

        if (!$menu) {
            echo json_encode(['success' => false, 'message' => '메뉴를 찾을 수 없습니다.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // 옵션 카테고리
        $DB->where('sm_idx', $menu_idx);
        $DB->orderBy('oc_order', 'ASC');
        $DB->orderBy('idx', 'ASC');
        $ocRows = $DB->get('menu_option_category_t', null, '
            idx, oc_title, oc_check, oc_su, oc_show, oc_order
        ');

        $options = [];
        $ocIds = [];
        foreach ($ocRows as $oc) {
            $oc_id = (int)$oc['idx'];
            $ocIds[] = $oc_id;

            $options[$oc_id] = [
                'oc_idx'     => $oc_id,
                'title'      => (string)$oc['oc_title'],
                'required'   => $oc['oc_check'] === 'Y',
                'max_select' => (int)$oc['oc_su'],
                'options'    => []
            ];
        }

        // 옵션 항목
        if (!empty($ocIds)) {
            $DB->where('oc_idx', $ocIds, 'IN');
            $DB->orderBy('om_order', 'ASC');
            $DB->orderBy('idx', 'ASC');
            $omRows = $DB->get('option_menu_t', null, '
                idx, oc_idx, om_title, om_price, om_show, om_order
            ');

            foreach ($omRows as $om) {
                $oc_id = (int)$om['oc_idx'];
                if (isset($options[$oc_id])) {
                    $options[$oc_id]['options'][] = [
                        'om_idx' => (int)$om['idx'],
                        'title'  => (string)$om['om_title'],
                        'price'  => (int)$om['om_price']
                    ];
                }
            }
        }

        echo json_encode([
            'success' => true,
            'menu' => [
                'idx'         => (int)$menu['idx'],
                'sc_idx'      => (int)$menu['sc_idx'],
                'title'       => (string)$menu['sm_title'],
                'image'       => (string)$menu['sm_image'],
                'contents'    => (string)$menu['sm_contents'],
                'price'       => (int)$menu['sm_price'],
                'is_sale'     => $menu['sm_type'] === 'Y',
                'is_adult'    => $menu['sm_age_show'] === 'Y',
                'is_main'     => $menu['sm_main'] === 'Y',
            ],
            'option_categories' => array_values($options)
        ], JSON_UNESCAPED_UNICODE);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => '서버 오류'], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

// ────────────────────────────────────────────────
// 3. 신규 메뉴 등록 (메뉴 + 이미지 + 옵션 전체)
// ────────────────────────────────────────────────
if ($act === 'add_menu_with_options') {
    try {
        $DB->startTransaction();

        // 메뉴 기본 정보
        $sc_idx      = (int)($_POST['sc_idx'] ?? 0);
        $sm_title    = trim($_POST['sm_title'] ?? '');
        $sm_price    = (int)($_POST['sm_price'] ?? 0);
        $sm_contents = trim($_POST['sm_contents'] ?? '');
        $sm_type     = ($_POST['sm_type'] ?? 'Y') === 'Y' ? 'Y' : 'N';
        $sm_main     = ($_POST['sm_main'] ?? 'Y') === 'Y' ? 'Y' : 'N';
        $sm_age_show = ($_POST['is_adult'] ?? 'N') === 'Y' ? 'Y' : 'N';

        if ($sc_idx <= 0 || $sm_title === '' || $sm_price < 0) {
            throw new Exception('필수 값이 누락되었습니다.');
        }

        // 이미지 처리
        $sm_image = '';
        if (!empty($_FILES['menu_image']) && $_FILES['menu_image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['menu_image'];
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $maxSize = 5 * 1024 * 1024;

            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) {
                throw new Exception('JPG, PNG, GIF 파일만 허용됩니다.');
            }
            if ($file['size'] > $maxSize) {
                throw new Exception('파일 크기는 5MB 이하여야 합니다.');
            }

            $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/data/menu/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $newFileName = 'menu_' . time() . '_' . mt_rand(10000, 99999) . '.' . $ext;
            $destPath = $uploadDir . $newFileName;

            if (!move_uploaded_file($file['tmp_name'], $destPath)) {
                throw new Exception('이미지 저장 실패');
            }

            $sm_image = $newFileName;
        }

        // 메뉴 등록
        $max_order = (int)$DB->getValue('shop_menu_t', 'MAX(sm_order)', ['sc_idx' => $sc_idx]) ?: 0;

        $menu_data = [
            'sc_idx'       => $sc_idx,
            'sm_title'     => $sm_title,
            'sm_image'     => $sm_image,
            'sm_contents'  => $sm_contents,
            'sm_price'     => $sm_price,
            'sm_type'      => $sm_type,
            'sm_age_show'  => $sm_age_show,
            'sm_show'      => 'Y',
            'sm_main'      => $sm_main,
            'sm_order'     => $max_order + 1,
            'sm_wdate'     => $DB->now(),
            'sm_udate'     => $DB->now(),
        ];

        $menu_id = $DB->insert('shop_menu_t', $menu_data);
        if (!$menu_id) {
            throw new Exception('메뉴 등록 실패');
        }

        // 옵션 카테고리 & 옵션 등록
        $option_categories = json_decode($_POST['option_categories'] ?? '[]', true);

        if (is_array($option_categories) && !empty($option_categories)) {
            foreach ($option_categories as $oc_index => $oc) {
                $oc_title      = trim($oc['title'] ?? '');
                $oc_required   = ($oc['required'] ?? 'N') === 'Y' ? 'Y' : 'N';
                $oc_max_select = (int)($oc['max_select'] ?? 1);

                if ($oc_title === '') continue;

                $oc_data = [
                    'sm_idx'    => $menu_id,
                    'oc_title'  => $oc_title,
                    'oc_check'  => $oc_required,
                    'oc_su'     => $oc_max_select,
                    'oc_show'   => 'Y',
                    'oc_order'  => $oc_index + 1,
                    'oc_wdate'  => $DB->now(),
                    'oc_udate'  => $DB->now(),
                ];

                $oc_id = $DB->insert('menu_option_category_t', $oc_data);
                if (!$oc_id) {
                    throw new Exception('옵션 카테고리 등록 실패');
                }

                $options = $oc['options'] ?? [];
                if (is_array($options) && !empty($options)) {
                    foreach ($options as $om_index => $om) {
                        $om_title = trim($om['title'] ?? '');
                        $om_price = (int)($om['price'] ?? 0);

                        if ($om_title === '') continue;

                        $om_data = [
                            'oc_idx'   => $oc_id,
                            'om_title' => $om_title,
                            'om_price' => $om_price,
                            'om_show'  => 'Y',
                            'om_order' => $om_index + 1,
                            'om_wdate' => $DB->now(),
                            'om_udate' => $DB->now(),
                        ];

                        if (!$DB->insert('option_menu_t', $om_data)) {
                            throw new Exception('옵션 항목 등록 실패');
                        }
                    }
                }
            }
        }

        $DB->commit();

        echo json_encode([
            'success' => true,
            'message' => '메뉴와 옵션이 모두 등록되었습니다.',
            'menu_id' => $menu_id
        ], JSON_UNESCAPED_UNICODE);

    } catch (Exception $e) {
        $DB->rollback();
        error_log("add_menu_with_options error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage() ?: '등록 중 오류가 발생했습니다.'
        ], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

// ────────────────────────────────────────────────
// 4. 메뉴 수정 (기존 옵션 삭제 후 재등록 방식)
// ────────────────────────────────────────────────
// -------------------------
// 메뉴 수정 (기존 옵션 삭제 후 재등록 방식)
// -------------------------
if ($act === 'update_menu_with_options') {
    try {
        $menu_idx = (int)($_POST['menu_idx'] ?? 0);
        if ($menu_idx <= 0) {
            throw new Exception('메뉴 ID가 없습니다.');
        }

        $DB->startTransaction();

        // 1. 메뉴 기본 정보
        $sc_idx      = (int)($_POST['sc_idx'] ?? 0);
        $sm_title    = trim($_POST['sm_title'] ?? '');
        $sm_price    = (int)($_POST['sm_price'] ?? 0);
        $sm_contents = trim($_POST['sm_contents'] ?? '');
        $sm_type     = ($_POST['sm_type'] ?? 'Y') === 'Y' ? 'Y' : 'N';
        $sm_main     = ($_POST['sm_main'] ?? 'Y') === 'Y' ? 'Y' : 'N';
        $sm_age_show = ($_POST['is_adult'] ?? 'N') === 'Y' ? 'Y' : 'N';

        if ($sc_idx <= 0 || $sm_title === '' || $sm_price < 0) {
            throw new Exception('필수 값 누락');
        }

        // 2. 기존 메뉴 정보 확인 (이미지 유지/삭제 판단용)
        $DB->where('idx', $menu_idx);
        $existing = $DB->getOne('shop_menu_t', 'sm_image');
        if (!$existing) {
            throw new Exception('메뉴를 찾을 수 없습니다.');
        }

        // 3. 이미지 처리
        $sm_image = $existing['sm_image'];

        // 새 이미지 업로드 → 기존 삭제 후 새로 저장
        if (!empty($_FILES['menu_image']) && $_FILES['menu_image']['error'] === UPLOAD_ERR_OK) {
            // 기존 이미지 삭제
            if ($sm_image && file_exists($_SERVER['DOCUMENT_ROOT'] . '/data/menu/' . $sm_image)) {
                @unlink($_SERVER['DOCUMENT_ROOT'] . '/data/menu/' . $sm_image);
            }

            $file = $_FILES['menu_image'];
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) {
                throw new Exception('허용되지 않는 파일 형식');
            }

            $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/data/menu/';
            $newFileName = 'menu_' . time() . '_' . mt_rand(10000, 99999) . '.' . $ext;
            $dest = $uploadDir . $newFileName;

            if (!move_uploaded_file($file['tmp_name'], $dest)) {
                throw new Exception('이미지 저장 실패');
            }

            $sm_image = $newFileName;
        }
        // 프론트에서 existing_image='' 보냈을 때 (이미지 삭제 의사)
        else if (isset($_POST['existing_image']) && $_POST['existing_image'] === '') {
            if ($sm_image && file_exists($_SERVER['DOCUMENT_ROOT'] . '/data/menu/' . $sm_image)) {
                @unlink($_SERVER['DOCUMENT_ROOT'] . '/data/menu/' . $sm_image);
            }
            $sm_image = '';
        }
        // 그 외 → 기존 이미지 유지 (기본값 그대로)

        // 4. 메뉴 업데이트
        $menu_data = [
            'sc_idx'       => $sc_idx,
            'sm_title'     => $sm_title,
            'sm_image'     => $sm_image,
            'sm_contents'  => $sm_contents,
            'sm_price'     => $sm_price,
            'sm_type'      => $sm_type,
            'sm_main'      => $sm_main,
            'sm_age_show'  => $sm_age_show,
            'sm_udate'     => $DB->now(),
        ];

        $DB->where('idx', $menu_idx);
        if (!$DB->update('shop_menu_t', $menu_data)) {
            throw new Exception('메뉴 수정 실패');
        }

        // 5. 기존 옵션 전체 삭제 후 재등록
        $DB->where('sm_idx', $menu_idx);
        $DB->delete('menu_option_category_t'); // 자식 옵션도 자동 삭제 (FK CASCADE 없으면 수동 삭제 필요)

        // 옵션 재등록
        $option_categories = json_decode($_POST['option_categories'] ?? '[]', true);
        if (is_array($option_categories) && !empty($option_categories)) {
            foreach ($option_categories as $oc_index => $oc) {
                $oc_title = trim($oc['title'] ?? '');
                if ($oc_title === '') continue;

                $oc_data = [
                    'sm_idx'    => $menu_idx,
                    'oc_title'  => $oc_title,
                    'oc_check'  => ($oc['required'] ?? 'N') === 'Y' ? 'Y' : 'N',
                    'oc_su'     => (int)($oc['max_select'] ?? 1),
                    'oc_show'   => 'Y',
                    'oc_order'  => $oc_index + 1,
                    'oc_wdate'  => $DB->now(),
                    'oc_udate'  => $DB->now(),
                ];

                $oc_id = $DB->insert('menu_option_category_t', $oc_data);
                if (!$oc_id) throw new Exception('옵션 카테고리 등록 실패');

                $options = $oc['options'] ?? [];
                foreach ($options as $om_index => $om) {
                    $om_title = trim($om['title'] ?? '');
                    if ($om_title === '') continue;

                    $om_data = [
                        'oc_idx'   => $oc_id,
                        'om_title' => $om_title,
                        'om_price' => (int)($om['price'] ?? 0),
                        'om_show'  => 'Y',
                        'om_order' => $om_index + 1,
                        'om_wdate' => $DB->now(),
                        'om_udate' => $DB->now(),
                    ];

                    $DB->insert('option_menu_t', $om_data);
                }
            }
        }

        $DB->commit();

        echo json_encode([
            'success' => true,
            'message' => '메뉴가 수정되었습니다.',
            'menu_id' => $menu_idx
        ], JSON_UNESCAPED_UNICODE);

    } catch (Exception $e) {
        $DB->rollback();
        error_log("update_menu_with_options error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage() ?: '수정 중 오류가 발생했습니다.'
        ], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

if ($act === 'delete_option_category') {
    $oc_idx = (int)($_POST['oc_idx'] ?? 0);
    if ($oc_idx <= 0) {
        echo json_encode(['success'=>false, 'message'=>'ID 누락']);
        exit;
    }

    try {
        $DB->startTransaction();

        // 1. 먼저 자식 옵션들 모두 삭제
        $DB->where('oc_idx', $oc_idx);
        $DB->delete('option_menu_t');

        // 2. 그 다음 부모 옵션 카테고리 삭제
        $DB->where('idx', $oc_idx);
        // 보안: 본인 매장의 메뉴에 속한 것만
        $DB->where('sm_idx IN (SELECT idx FROM shop_menu_t WHERE sh_idx = ?)', [$sh_idx]);
        $deleted = $DB->delete('menu_option_category_t');

        if (!$deleted) {
            throw new Exception('삭제할 카테고리가 없거나 권한 없음');
        }

        $DB->commit();

        echo json_encode(['success'=>true, 'message'=>'옵션 카테고리가 삭제되었습니다.']);
    } catch (Exception $e) {
        $DB->rollback();
        echo json_encode(['success'=>false, 'message'=>$e->getMessage()]);
    }
    exit;
}
if ($act === 'delete_menu') {
    try {
        $menu_idx = (int)($_POST['menu_idx'] ?? 0);
        if ($menu_idx <= 0) {
            throw new Exception('메뉴 ID가 없습니다.');
        }

        $DB->startTransaction();

        // 1. 메뉴에 연결된 옵션 카테고리 삭제 (자식 옵션도 자동 삭제 - FK CASCADE 필요 시)
        $DB->where('sm_idx', $menu_idx);
        $DB->delete('menu_option_category_t');

        // 2. 메뉴 삭제 (하드 삭제)
        $DB->where('idx', $menu_idx);
        $deleted = $DB->delete('shop_menu_t');

        if (!$deleted) {
            throw new Exception('삭제할 메뉴가 없거나 권한이 없습니다.');
        }

        $DB->commit();

        echo json_encode([
            'success' => true,
            'message' => '메뉴가 삭제되었습니다.'
        ], JSON_UNESCAPED_UNICODE);

    } catch (Exception $e) {
        $DB->rollback();
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
    exit;
}
// 지원하지 않는 act
echo json_encode(['success'=>false, 'message'=>'지원하지 않는 요청'], JSON_UNESCAPED_UNICODE);
exit;
