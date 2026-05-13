<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/cfg/lib.inc.php";

header('Content-Type: application/json; charset=UTF-8');

function review_api_json_response($success, $message = '', array $extra = [])
{
    echo json_encode(array_merge([
        'success' => (bool)$success,
        'message' => (string)$message,
    ], $extra), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function review_api_get_login_member_id()
{
    if (!empty($_SESSION['mng']['mt_idx'])) {
        return (int)$_SESSION['mng']['mt_idx'];
    }

    if (!empty($_SESSION['mng']['idx'])) {
        return (int)$_SESSION['mng']['idx'];
    }

    if (!empty($_SESSION['member']['mt_idx'])) {
        return (int)$_SESSION['member']['mt_idx'];
    }

    if (!empty($_SESSION['member']['idx'])) {
        return (int)$_SESSION['member']['idx'];
    }

    return 0;
}

function review_api_normalize_upload_files($fileField)
{
    $files = [];

    if (empty($fileField) || empty($fileField['name'])) {
        return $files;
    }

    if (is_array($fileField['name'])) {
        $count = count($fileField['name']);

        for ($i = 0; $i < $count; $i++) {
            if (($fileField['error'][$i] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            $files[] = [
                'name'     => $fileField['name'][$i] ?? '',
                'type'     => $fileField['type'][$i] ?? '',
                'tmp_name' => $fileField['tmp_name'][$i] ?? '',
                'error'    => $fileField['error'][$i] ?? UPLOAD_ERR_NO_FILE,
                'size'     => (int)($fileField['size'][$i] ?? 0),
            ];
        }

        return $files;
    }

    if (($fileField['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
        $files[] = [
            'name'     => $fileField['name'] ?? '',
            'type'     => $fileField['type'] ?? '',
            'tmp_name' => $fileField['tmp_name'] ?? '',
            'error'    => $fileField['error'] ?? UPLOAD_ERR_NO_FILE,
            'size'     => (int)($fileField['size'] ?? 0),
        ];
    }

    return $files;
}

function review_api_detect_mime_type($tmpPath)
{
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo) {
            $mime = finfo_file($finfo, $tmpPath);
            finfo_close($finfo);

            if ($mime) {
                return $mime;
            }
        }
    }

    if (function_exists('mime_content_type')) {
        $mime = mime_content_type($tmpPath);
        if ($mime) {
            return $mime;
        }
    }

    return '';
}

function review_api_validate_review_images(array $files)
{
    $allowedMimes = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
        'image/heic' => 'heic',
        'image/heif' => 'heif',
    ];

    $validated = [];

    foreach ($files as $file) {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new Exception('사진 업로드 중 오류가 발생했습니다.');
        }

        if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            throw new Exception('올바르지 않은 업로드 파일입니다.');
        }

        if ((int)$file['size'] <= 0) {
            throw new Exception('비어있는 파일은 업로드할 수 없습니다.');
        }

        if ((int)$file['size'] > 10 * 1024 * 1024) {
            throw new Exception('사진은 10MB 이하만 업로드할 수 있습니다.');
        }

        $mime = review_api_detect_mime_type($file['tmp_name']);
        if ($mime === '' || !isset($allowedMimes[$mime])) {
            throw new Exception('지원하지 않는 이미지 형식입니다.');
        }

        $file['mime'] = $mime;
        $file['ext']  = $allowedMimes[$mime];
        $validated[]  = $file;
    }

    return $validated;
}

function review_api_snapshot_options_to_text($options)
{
    if (!is_array($options) || empty($options)) {
        return '';
    }

    $parts = [];

    foreach ($options as $opt) {
        if (is_string($opt)) {
            $text = trim($opt);
            if ($text !== '') {
                $parts[] = $text;
            }
            continue;
        }

        if (!is_array($opt)) {
            continue;
        }

        $name  = trim((string)($opt['name'] ?? $opt['option_name'] ?? $opt['group_name'] ?? ''));
        $value = trim((string)($opt['value'] ?? $opt['option_value'] ?? $opt['item_name'] ?? $opt['label'] ?? ''));

        if ($name !== '' && $value !== '') {
            $parts[] = $name . ': ' . $value;
        } elseif ($value !== '') {
            $parts[] = $value;
        } elseif ($name !== '') {
            $parts[] = $name;
        }
    }

    return implode(' / ', $parts);
}

function review_api_db_insert_or_fail($table, array $data, $message)
{
    global $DB;

    $id = $DB->insert($table, $data);
    if (!$id) {
        $dbError = method_exists($DB, 'getLastError') ? $DB->getLastError() : '';
        throw new Exception($message . ($dbError ? ' [' . $dbError . ']' : ''));
    }

    return (int)$id;
}

function review_api_db_update_or_fail($table, array $data, $message)
{
    global $DB;

    $ok = $DB->update($table, $data);
    if ($ok === false) {
        $dbError = method_exists($DB, 'getLastError') ? $DB->getLastError() : '';
        throw new Exception($message . ($dbError ? ' [' . $dbError . ']' : ''));
    }
}

function review_api_build_selected_snapshot_items(array $snapshotItems, array $menuKeys, array $menuIds)
{
    $selected = [];

    if (!empty($menuKeys)) {
        foreach ($menuKeys as $key) {
            if (isset($snapshotItems[$key]) && is_array($snapshotItems[$key])) {
                $selected[] = [
                    'snapshot_key' => $key,
                    'item'         => $snapshotItems[$key],
                ];
            }
        }
        return $selected;
    }

    if (empty($menuIds)) {
        return $selected;
    }

    $menuIdMap = array_fill_keys($menuIds, true);

    foreach ($snapshotItems as $idx => $item) {
        $smId = (int)($item['sm_id'] ?? 0);
        if ($smId > 0 && isset($menuIdMap[$smId])) {
            $selected[] = [
                'snapshot_key' => $idx,
                'item'         => $item,
            ];
        }
    }

    return $selected;
}

function review_api_build_review_image_src($reviewId, $file)
{
    $file = trim((string)$file);

    if ($file === '') {
        return '';
    }

    if (strpos($file, 'http://') === 0 || strpos($file, 'https://') === 0 || strpos($file, '/') === 0) {
        return $file;
    }

    return '/data/review/' . $reviewId . '/' . $file;
}

function review_api_human_date($datetime)
{
    $ts = strtotime((string)$datetime);
    if (!$ts) {
        return '';
    }

    $today = strtotime(date('Y-m-d'));
    $target = strtotime(date('Y-m-d', $ts));
    $diffDays = (int)(($today - $target) / 86400);

    if ($diffDays <= 0) {
        return '오늘';
    }

    if ($diffDays <= 6) {
        return $diffDays . '일 전';
    }

    return date('Y.m.d', $ts);
}

function review_api_fetch_page($DB, $sh_idx, $sm_idx, $sort, $page = 1, $limit = 10)
{
    $page   = max(1, (int)$page);
    $limit  = max(1, min(30, (int)$limit));
    $offset = ($page - 1) * $limit;

    $sort = strtolower(trim((string)$sort));
    $orderByMap = [
        'latest'      => 'r.rv_wdate DESC, r.idx DESC',
        'rating_high' => 'r.rv_food_score DESC, r.rv_wdate DESC, r.idx DESC',
        'rating_low'  => 'r.rv_food_score ASC, r.rv_wdate DESC, r.idx DESC',
    ];
    $orderBySql = $orderByMap[$sort] ?? $orderByMap['latest'];

    $idSql = "
        SELECT DISTINCT
            r.idx AS rv_idx
        FROM review_t r
    ";

    $params = [];

    if ($sm_idx > 0) {
        $idSql .= " INNER JOIN review_menu_t rmf ON rmf.rv_idx = r.idx AND rmf.sm_idx = ? ";
        $params[] = $sm_idx;
    }

    $idSql .= "
        WHERE r.sh_idx = ?
          AND r.rv_show = 'Y'
          AND r.del_date IS NULL
        ORDER BY {$orderBySql}
        LIMIT {$offset}, " . ($limit + 1);

    $params[] = $sh_idx;

    $idRows = $DB->rawQuery($idSql, $params);

    $reviewIds = [];
    foreach ((array)$idRows as $idRow) {
        $reviewIds[] = (int)$idRow['rv_idx'];
    }

    $hasMore = count($reviewIds) > $limit;
    if ($hasMore) {
        array_pop($reviewIds);
    }

    if (empty($reviewIds)) {
        return [
            'items'   => [],
            'hasMore' => false,
        ];
    }

    $placeholders = implode(',', array_fill(0, count($reviewIds), '?'));

    $reviewRows = $DB->rawQuery("
    SELECT
        r.idx AS rv_idx,
        r.rv_food_score,
        r.rv_contents,
        r.rv_wdate,
        r.mt_idx,
        m.mt_name
    FROM review_t r
    LEFT JOIN member_t m ON m.idx = r.mt_idx
    WHERE r.idx IN ({$placeholders})
", $reviewIds);

    $reviewMap = [];
    foreach ((array)$reviewRows as $reviewRow) {
        $reviewMap[(int)$reviewRow['rv_idx']] = $reviewRow;
    }

    $imageRows = $DB->rawQuery("
        SELECT
            rv_idx,
            ri_file,
            ri_order
        FROM review_image_t
        WHERE rv_idx IN ({$placeholders})
        ORDER BY rv_idx ASC, ri_order ASC, idx ASC
    ", $reviewIds);

    $imageMap = [];
    foreach ((array)$imageRows as $imageRow) {
        $rvIdx = (int)$imageRow['rv_idx'];
        $src   = review_api_build_review_image_src($rvIdx, $imageRow['ri_file'] ?? '');
        if ($src !== '') {
            $imageMap[$rvIdx][] = $src;
        }
    }

    $menuRows = $DB->rawQuery("
        SELECT
            rv_idx,
            rm_menu_name
        FROM review_menu_t
        WHERE rv_idx IN ({$placeholders})
        ORDER BY rv_idx ASC, rm_order ASC, idx ASC
    ", $reviewIds);

    $menuMap = [];
    foreach ((array)$menuRows as $menuRow) {
        $rvIdx    = (int)$menuRow['rv_idx'];
        $menuName = trim((string)($menuRow['rm_menu_name'] ?? ''));
        if ($menuName === '') {
            continue;
        }

        if (!isset($menuMap[$rvIdx])) {
            $menuMap[$rvIdx] = [];
        }

        if (!in_array($menuName, $menuMap[$rvIdx], true)) {
            $menuMap[$rvIdx][] = $menuName;
        }
    }

    $items = [];
    foreach ($reviewIds as $reviewId) {
        if (empty($reviewMap[$reviewId])) {
            continue;
        }

        $review = $reviewMap[$reviewId];
        $items[] = [
            'rv_idx'      => (int)$review['rv_idx'],
            'writer_name' => trim((string)($review['mt_name'] ?? '')) !== '' ? trim((string)$review['mt_name']) : '방문 고객',
            'score'       => (int)$review['rv_food_score'],
            'content'     => (string)($review['rv_contents'] ?? ''),
            'date_label'  => review_api_human_date($review['rv_wdate'] ?? ''),
            'images'      => $imageMap[$reviewId] ?? [],
            'menus'       => $menuMap[$reviewId] ?? [],
        ];
    }

    return [
        'items'   => $items,
        'hasMore' => $hasMore,
    ];
}

function review_api_fetch_my_page($DB, $memberId, $page = 1, $limit = 10)
{
    $page   = max(1, (int)$page);
    $limit  = max(1, min(30, (int)$limit));
    $offset = ($page - 1) * $limit;

    $DB->where('mt_idx', $memberId);
    $DB->where('rv_show', 'Y');
    $DB->where('del_date', null, 'IS');
    $DB->orderBy('rv_wdate', 'DESC');
    $DB->orderBy('idx', 'DESC');
    $idRows = $DB->get('review_t', [$offset, $limit + 1], ['idx AS rv_idx']);

    $reviewIds = [];
    foreach ((array)$idRows as $idRow) {
        $reviewIds[] = (int)$idRow['rv_idx'];
    }

    $hasMore = count($reviewIds) > $limit;
    if ($hasMore) {
        array_pop($reviewIds);
    }

    if (empty($reviewIds)) {
        return [
            'items'   => [],
            'hasMore' => false,
        ];
    }

    $placeholders = implode(',', array_fill(0, count($reviewIds), '?'));

    $reviewRows = $DB->rawQuery("
        SELECT
            r.idx AS rv_idx,
            r.rv_food_score,
            r.rv_contents,
            r.rv_wdate,
            r.sh_idx,
            s.sh_title,
            s.sh_branch_nm
        FROM review_t r
        LEFT JOIN shop_t s ON s.idx = r.sh_idx
        WHERE r.idx IN ({$placeholders})
    ", $reviewIds);

    $reviewMap = [];
    foreach ((array)$reviewRows as $reviewRow) {
        $reviewMap[(int)$reviewRow['rv_idx']] = $reviewRow;
    }

    $imageRows = $DB->rawQuery("
        SELECT
            rv_idx,
            ri_file,
            ri_order
        FROM review_image_t
        WHERE rv_idx IN ({$placeholders})
        ORDER BY rv_idx ASC, ri_order ASC, idx ASC
    ", $reviewIds);

    $imageMap = [];
    foreach ((array)$imageRows as $imageRow) {
        $rvIdx = (int)$imageRow['rv_idx'];
        $src = review_api_build_review_image_src($rvIdx, $imageRow['ri_file'] ?? '');
        if ($src !== '') {
            $imageMap[$rvIdx][] = $src;
        }
    }

    $menuRows = $DB->rawQuery("
        SELECT
            rv_idx,
            rm_menu_name
        FROM review_menu_t
        WHERE rv_idx IN ({$placeholders})
        ORDER BY rv_idx ASC, rm_order ASC, idx ASC
    ", $reviewIds);

    $menuMap = [];
    foreach ((array)$menuRows as $menuRow) {
        $rvIdx = (int)$menuRow['rv_idx'];
        $menuName = trim((string)($menuRow['rm_menu_name'] ?? ''));
        if ($menuName === '') {
            continue;
        }

        if (!isset($menuMap[$rvIdx])) {
            $menuMap[$rvIdx] = [];
        }

        if (!in_array($menuName, $menuMap[$rvIdx], true)) {
            $menuMap[$rvIdx][] = $menuName;
        }
    }

    $items = [];
    foreach ($reviewIds as $reviewId) {
        if (empty($reviewMap[$reviewId])) {
            continue;
        }

        $review = $reviewMap[$reviewId];
        $storeName = trim((string)($review['sh_title'] ?? '') . (string)($review['sh_branch_nm'] ?? ''));
        if ($storeName === '') {
            $storeName = '매장 정보 없음';
        }

        $items[] = [
            'rv_idx'      => (int)$review['rv_idx'],
            'store_name'  => $storeName,
            'score'       => (int)$review['rv_food_score'],
            'content'     => (string)($review['rv_contents'] ?? ''),
            'date_label'  => review_api_human_date($review['rv_wdate'] ?? ''),
            'images'      => $imageMap[$reviewId] ?? [],
            'menus'       => $menuMap[$reviewId] ?? [],
        ];
    }

    return [
        'items'   => $items,
        'hasMore' => $hasMore,
    ];
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    review_api_json_response(false, '잘못된 요청입니다.');
}

$act = trim((string)($_POST['act'] ?? ''));

if ($act === 'create_review') {
    $memberId = review_api_get_login_member_id();
    if ($memberId < 1) {
        review_api_json_response(false, '로그인이 필요합니다.', [
            'login_required' => true,
            'redirect_url'   => '/auth/login.php',
        ]);
    }

    $ot_idx         = (int)($_POST['ot_idx'] ?? $_POST['order_idx'] ?? 0);
    $sh_idx         = (int)($_POST['sh_idx'] ?? 0);
    $foodScore      = (int)($_POST['food_score'] ?? 0);
    $reviewContents = trim((string)($_POST['review_contents'] ?? ''));
    $menuIds        = isset($_POST['menu_ids']) && is_array($_POST['menu_ids']) ? array_values(array_unique(array_map('intval', $_POST['menu_ids']))) : [];
    $menuKeys       = isset($_POST['menu_keys']) && is_array($_POST['menu_keys']) ? array_values(array_unique(array_map('intval', $_POST['menu_keys']))) : [];
    $uploadFiles    = review_api_normalize_upload_files($_FILES['review_images'] ?? null);

    if ($ot_idx < 1 || $sh_idx < 1) {
        review_api_json_response(false, '주문 정보가 올바르지 않습니다.');
    }

    if ($foodScore < 1 || $foodScore > 5) {
        review_api_json_response(false, '음식 별점을 선택해주세요.');
    }

    if (mb_strlen($reviewContents, 'UTF-8') < 20) {
        review_api_json_response(false, '리뷰는 20자 이상 입력해주세요.');
    }

    if (count($uploadFiles) > 5) {
        review_api_json_response(false, '사진은 최대 5장까지 등록할 수 있습니다.');
    }

    $savedFilePaths = [];
    $uploadDir      = '';
    $reviewId       = 0;

    try {
        $validatedFiles = review_api_validate_review_images($uploadFiles);

        $DB->where('idx', $ot_idx);
        $DB->where('sh_idx', $sh_idx);
        $DB->where('mt_idx', $memberId);
        $DB->where('ot_status', 'COMPLETED');
        $orderRow = $DB->getOne('orders_t', [
            'idx',
            'mt_idx',
            'sh_idx',
            'ot_number',
            'ct_snapshot',
            'ot_status',
        ]);

        if (empty($orderRow)) {
            review_api_json_response(false, '리뷰를 작성할 수 있는 완료 주문이 아닙니다.');
        }

        $DB->where('ot_idx', $ot_idx);
        $existsReview = $DB->getValue('review_t', 'count(*)');
        if ((int)$existsReview > 0) {
            review_api_json_response(false, '이미 리뷰를 작성한 주문입니다.');
        }

        $snapshot = json_decode((string)($orderRow['ct_snapshot'] ?? ''), true);
        $snapshotItems = [];
        if (!empty($snapshot['items']) && is_array($snapshot['items'])) {
            $snapshotItems = array_values($snapshot['items']);
        }

        $selectedItems = review_api_build_selected_snapshot_items($snapshotItems, $menuKeys, $menuIds);

        $DB->startTransaction();

        $reviewId = review_api_db_insert_or_fail('review_t', [
            'mt_idx'        => $memberId,
            'sh_idx'        => $sh_idx,
            'ot_idx'        => $ot_idx,
            'rv_food_score' => $foodScore,
            'rv_contents'   => $reviewContents,
            'rv_photo_cnt'  => 0,
            'rv_show'       => 'Y',
        ], '리뷰 저장에 실패했습니다.');

        if (!empty($selectedItems)) {
            $menuOrder = 1;

            foreach ($selectedItems as $selected) {
                $item = $selected['item'];

                $smIdx      = (int)($item['sm_id'] ?? 0);
                $menuName   = trim((string)($item['menu_name'] ?? '메뉴'));
                $optionText = review_api_snapshot_options_to_text($item['options'] ?? []);
                $optionJson = !empty($item['options'])
                    ? json_encode($item['options'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                    : null;

                $quantity   = max(1, (int)($item['quantity'] ?? 1));
                $unitPrice  = (float)($item['unit_price'] ?? 0);
                $totalPrice = (float)($item['total_price'] ?? 0);

                review_api_db_insert_or_fail('review_menu_t', [
                    'rv_idx'         => $reviewId,
                    'sm_idx'         => $smIdx > 0 ? $smIdx : null,
                    'rm_menu_name'   => $menuName !== '' ? $menuName : '메뉴',
                    'rm_option_text' => $optionText !== '' ? $optionText : null,
                    'rm_option_json' => $optionJson ?: null,
                    'rm_quantity'    => $quantity,
                    'rm_unit_price'  => $unitPrice,
                    'rm_total_price' => $totalPrice,
                    'rm_order'       => $menuOrder,
                ], '리뷰 메뉴 저장에 실패했습니다.');

                $menuOrder++;
            }
        }

        if (!empty($validatedFiles)) {
            $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/data/review/' . $reviewId;

            if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
                throw new Exception('리뷰 이미지 저장 폴더를 만들지 못했습니다.');
            }

            foreach ($validatedFiles as $idx => $file) {
                $random     = bin2hex(random_bytes(4));
                $storedName = date('YmdHis') . '_' . ($idx + 1) . '_' . $random . '.' . $file['ext'];
                $targetPath = $uploadDir . '/' . $storedName;

                if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
                    throw new Exception('리뷰 이미지 저장에 실패했습니다.');
                }

                $savedFilePaths[] = $targetPath;

                review_api_db_insert_or_fail('review_image_t', [
                    'rv_idx'         => $reviewId,
                    'ri_file'        => $storedName,
                    'ri_origin_name' => $file['name'],
                    'ri_mime'        => $file['mime'],
                    'ri_size'        => (int)$file['size'],
                    'ri_order'       => $idx + 1,
                ], '리뷰 이미지 정보 저장에 실패했습니다.');
            }

            $DB->where('idx', $reviewId);
            review_api_db_update_or_fail('review_t', [
                'rv_photo_cnt' => count($validatedFiles),
            ], '리뷰 사진 수 갱신에 실패했습니다.');
        }

        $DB->commit();

        review_api_json_response(true, '리뷰가 등록되었습니다.', [
            'review_idx'   => $reviewId,
            'redirect_url' => '../order/history.php',
        ]);
    } catch (Throwable $e) {
        if (method_exists($DB, 'rollback')) {
            $DB->rollback();
        }

        if (!empty($savedFilePaths)) {
            foreach ($savedFilePaths as $savedPath) {
                if (is_file($savedPath)) {
                    @unlink($savedPath);
                }
            }
        }

        if (!empty($uploadDir) && is_dir($uploadDir)) {
            $files = @scandir($uploadDir);
            if (is_array($files) && count($files) <= 2) {
                @rmdir($uploadDir);
            }
        }

        review_api_json_response(false, $e->getMessage());
    }
} elseif ($act === 'review_list_page') {
    try {
        $sh_idx = (int)($_POST['sh_idx'] ?? 0);
        $sm_idx = (int)($_POST['sm_idx'] ?? 0);
        $page   = max(1, (int)($_POST['page'] ?? 1));
        $limit  = max(1, min(30, (int)($_POST['limit'] ?? 10)));

        $sort = strtolower(trim((string)($_POST['sort'] ?? 'latest')));
        $sort = in_array($sort, ['latest', 'rating_high', 'rating_low'], true) ? $sort : 'latest';

        if ($sh_idx < 1) {
            throw new Exception('잘못된 요청입니다.');
        }

        $DB->where('idx', $sh_idx);
        $shopExists = (int)$DB->getValue('shop_t', 'count(*)');
        if ($shopExists < 1) {
            throw new Exception('매장 정보를 찾을 수 없습니다.');
        }

        if ($sm_idx > 0) {
            $DB->join('shop_category_t c', 'c.idx = m.sc_idx', 'INNER');
            $DB->where('m.idx', $sm_idx);
            $DB->where('c.sh_idx', $sh_idx);
            $menuExists = (int)$DB->getValue('shop_menu_t m', 'count(*)');

            if ($menuExists < 1) {
                throw new Exception('메뉴 정보를 찾을 수 없습니다.');
            }
        }

        $pageData = review_api_fetch_page($DB, $sh_idx, $sm_idx, $sort, $page, $limit);

        review_api_json_response(true, '', [
            'items'   => $pageData['items'],
            'hasMore' => (bool)$pageData['hasMore'],
            'page'    => $page,
        ]);
    } catch (Throwable $e) {
        review_api_json_response(false, $e->getMessage());
    }
} elseif ($act === 'my_review_list_page') {
    try {
        $memberId = review_api_get_login_member_id();
        if ($memberId < 1) {
            review_api_json_response(false, '로그인이 필요합니다.', [
                'login_required' => true,
                'redirect_url'   => '/auth/login.php',
            ]);
        }

        $page  = max(1, (int)($_POST['page'] ?? 1));
        $limit = max(1, min(30, (int)($_POST['limit'] ?? 10)));

        $pageData = review_api_fetch_my_page($DB, $memberId, $page, $limit);

        review_api_json_response(true, '', [
            'items'   => $pageData['items'],
            'hasMore' => (bool)$pageData['hasMore'],
            'page'    => $page,
        ]);
    } catch (Throwable $e) {
        review_api_json_response(false, $e->getMessage());
    }
} else {
    review_api_json_response(false, '지원하지 않는 요청입니다.');
}
