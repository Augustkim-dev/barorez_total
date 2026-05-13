<?php
// qa_write_ok.php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";

header('Content-Type: application/json; charset=utf-8');

global $DB;

// =========================
// 공통: 로그인 체크
// =========================
if (!isset($_SESSION['mng']['mt_idx']) || (int)$_SESSION['mng']['mt_idx'] <= 0) {
    echo json_encode(['success'=>false, 'message'=>'로그인이 필요합니다.'], JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

$mt_idx = (int)$_SESSION['mng']['mt_idx'];
$act    = (string)($_POST['act'] ?? '');

if ($act !== 'qa_write') {
    echo json_encode(['success'=>false, 'message'=>'잘못된 요청입니다.'], JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

// =========================
// 업로드 설정
// =========================
$uploadDir = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/data/qa/' . $mt_idx;
if (!is_dir($uploadDir)) {
    @mkdir($uploadDir, 0775, true);
}

$publicBase = rtrim(DATA_URL, '/') . '/qa/' . $mt_idx;

$allowedExt = ['jpg','jpeg','png','webp','gif'];
$maxSize    = 10 * 1024 * 1024; // 10MB

$normExt = function($ext) {
    $ext = strtolower((string)$ext);
    if ($ext === 'jpeg') $ext = 'jpg';
    return $ext;
};

$makeFileName = function($prefix, $ext) use ($mt_idx) {
    $rand = bin2hex(random_bytes(6));
    return $prefix . '_qa' . $mt_idx . '_' . date('YmdHis') . '_' . $rand . '.' . $ext;
};

$saveUploadImg = function($fieldName, $prefix) use ($allowedExt, $maxSize, $uploadDir, $makeFileName, $normExt) {
    if (!isset($_FILES[$fieldName])) return null;

    $f = $_FILES[$fieldName];
    if (!is_array($f) || ($f['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
    if (($f['error'] ?? 0) !== UPLOAD_ERR_OK) {
        throw new Exception($fieldName . ' 업로드 오류: ' . $f['error']);
    }

    $size = (int)($f['size'] ?? 0);
    if ($size <= 0 || $size > $maxSize) {
        throw new Exception($fieldName . ' 파일 용량이 너무 큽니다. (최대 10MB)');
    }

    $orig = (string)($f['name'] ?? '');
    $ext  = $normExt(pathinfo($orig, PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExt, true)) {
        throw new Exception($fieldName . ' 허용되지 않는 파일 형식입니다.');
    }

    $plain = $makeFileName($prefix, $ext);
    $dest  = rtrim($uploadDir, '/') . '/' . $plain;

    if (!move_uploaded_file($f['tmp_name'], $dest)) {
        throw new Exception($fieldName . ' 파일 저장 실패');
    }

    return $plain;  // DB에 저장할 파일명 (rs_ 없음)
};

// =========================================================
// 문의 등록 처리
// =========================================================
try {
    $rt_title       = trim((string)($_POST['rt_title'] ?? ''));
    $rt_description = trim((string)($_POST['rt_description'] ?? ''));

    if (mb_strlen($rt_title, 'UTF-8') < 1) {
        echo json_encode(['success'=>false, 'message'=>'제목을 입력해 주세요.'], JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_SUBSTITUTE);
        exit;
    }

    if (mb_strlen($rt_description, 'UTF-8') < 5) {
        echo json_encode(['success'=>false, 'message'=>'문의 내용을 5자 이상 입력해 주세요.'], JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_SUBSTITUTE);
        exit;
    }

    // 이미지 최대 5장
    $img_columns = ['rt_img1','rt_img2','rt_img3','rt_img4','rt_img5'];
    $img_values  = [];

    for ($i = 1; $i <= 5; $i++) {
        $field = 'qa_img' . $i;
        $new_filename = $saveUploadImg($field, 'img' . $i);

        $img_values[$img_columns[$i-1]] = $new_filename ?? '';
    }

    // DB 입력
    $insert_data = [
        'mt_idx'         => $mt_idx,
        'rt_title'       => $rt_title,
        'rt_description' => $rt_description,
        'rt_status'      => 'pending',
        'rt_show'        => 'Y',
        'created_at'     => date('Y-m-d H:i:s'),
    ];

    $insert_data = array_merge($insert_data, $img_values);

    $new_idx = $DB->insert('qa_t', $insert_data);

    if (!$new_idx) {
        echo json_encode(['success'=>false, 'message'=>'등록 실패 : ' . $DB->getLastError()], JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_SUBSTITUTE);
        exit;
    }

    echo json_encode([
        'success' => true,
        'message' => '문의가 정상적으로 등록되었습니다.',
        'qa_idx'  => (int)$new_idx
    ], JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_SUBSTITUTE);

} catch (Exception $e) {
    error_log('qa_write error: ' . $e->getMessage());
    echo json_encode(['success'=>false, 'message'=>'서버 오류가 발생했습니다.'], JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

exit;
