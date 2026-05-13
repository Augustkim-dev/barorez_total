<?php
// store/update.php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";

header('Content-Type: application/json; charset=utf-8');

global $DB;

// =========================
// 공통: 로그인/매장키 체크
// =========================
if (!isset($_SESSION['mng'])) {
    echo json_encode(['success'=>false,'message'=>'로그인이 필요합니다.'], JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

$act   = (string)($_POST['act'] ?? '');
$sh_idx = (int)($_SESSION['current_sh_idx'] ?? 0);

if ($sh_idx <= 0) {
    echo json_encode(['success'=>false,'message'=>'매장 정보가 없습니다.'], JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

// =========================
// 업로드 설정 (요구사항 반영)
// - 실제 저장 경로: /data/shop/{sh_idx}/
// - DB에는 "파일명만" 저장
// - 매장이미지( sh_img1~5 ): 서버에는 rs_파일명 으로 저장, DB에는 rs_ 제거한 파일명 저장
// - 사업자등록증( sh_biz_file ): rs_ 없이 저장, DB에도 파일명 그대로 저장
// - 프론트로 보낼 땐 매장이미지엔 rs_ 붙여서 URL 생성, 사업자등록증은 rs_ 없이 URL 생성
// =========================
$uploadDir = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/data/shop/' . $sh_idx; // ✅ trailing slash 없이
if (!is_dir($uploadDir)) {
    @mkdir($uploadDir, 0775, true);
}

// DATA_URL 예: https://cdn.domain.com/data  또는 https://domain.com/data
// 최종 URL: DATA_URL + "/shop/{sh_idx}/(rs_)파일명"
$publicBase = rtrim(DATA_URL, '/') . '/shop/' . $sh_idx;

$allowedExt = ['jpg','jpeg','png','webp','pdf'];
$maxSize = 10 * 1024 * 1024; // 10MB

$normExt = function($ext){
    $ext = strtolower((string)$ext);
    if ($ext === 'jpeg') $ext = 'jpg';
    return $ext;
};

$stripRs = function($name){
    $name = (string)($name ?? '');
    if ($name === '') return '';
    return preg_replace('/^rs_/', '', $name);
};

$makeFileName = function($prefix, $ext) use ($sh_idx) {
    // DB에는 prefix_sh{idx}_yyyymmddhhii_rand.ext 같은 식으로 저장(파일명만)
    $rand = bin2hex(random_bytes(6));
    return $prefix . '_sh' . $sh_idx . '_' . date('YmdHis') . '_' . $rand . '.' . $ext;
};

$makePublicUrlShopImg = function($fileNameNoRs) use ($publicBase) {
    $fileNameNoRs = (string)($fileNameNoRs ?? '');
    if ($fileNameNoRs === '') return '';
    // ✅ 프론트로 보낼 때는 rs_ 붙여서 내려준다
    return $publicBase . '/rs_' . $fileNameNoRs;
};

$makePublicUrlBiz = function($fileName) use ($publicBase) {
    $fileName = (string)($fileName ?? '');
    if ($fileName === '') return '';
    // ✅ 사업자등록증은 rs_ 없이 그대로
    return $publicBase . '/' . $fileName;
};

// (선택) 실제 파일 삭제까지 하고 싶으면 true
$DO_DELETE_OLD_FILES = false;

$deleteFileIfExists = function($fileNameOnDisk) use ($uploadDir, $DO_DELETE_OLD_FILES) {
    if (!$DO_DELETE_OLD_FILES) return;
    $fileNameOnDisk = (string)($fileNameOnDisk ?? '');
    if ($fileNameOnDisk === '') return;

    $full = rtrim($uploadDir,'/') . '/' . $fileNameOnDisk;
    if (is_file($full)) @unlink($full);
};

// ✅ 사업자등록증 업로드: rs 없이 저장, DB에도 파일명 그대로 저장
$saveUploadBiz = function($fieldName, $prefix) use ($allowedExt, $maxSize, $uploadDir, $makeFileName, $normExt) {
    if (!isset($_FILES[$fieldName])) return null;

    $f = $_FILES[$fieldName];
    if (!is_array($f) || ($f['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
    if (($f['error'] ?? 0) !== UPLOAD_ERR_OK) {
        throw new Exception($fieldName.' 업로드 오류: '.$f['error']);
    }

    $size = (int)($f['size'] ?? 0);
    if ($size <= 0 || $size > $maxSize) {
        throw new Exception($fieldName.' 파일 용량이 올바르지 않습니다.');
    }

    $orig = (string)($f['name'] ?? '');
    $ext  = $normExt(pathinfo($orig, PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExt, true)) {
        throw new Exception($fieldName.' 허용되지 않는 확장자입니다.');
    }

    $plain = $makeFileName($prefix, $ext); // ✅ rs 없음
    $dest  = rtrim($uploadDir,'/') . '/' . $plain;

    if (!move_uploaded_file($f['tmp_name'], $dest)) {
        throw new Exception($fieldName.' 파일 저장에 실패했습니다.');
    }

    return $plain; // ✅ DB에는 파일명만
};

// ✅ 매장 이미지 업로드: rs_ 붙여서 저장, DB에는 rs_ 제거한 파일명 저장
$saveUploadShopImg = function($fieldName, $prefix) use ($allowedExt, $maxSize, $uploadDir, $makeFileName, $normExt, $stripRs) {
    if (!isset($_FILES[$fieldName])) return null;

    $f = $_FILES[$fieldName];
    if (!is_array($f) || ($f['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
    if (($f['error'] ?? 0) !== UPLOAD_ERR_OK) {
        throw new Exception($fieldName.' 업로드 오류: '.$f['error']);
    }

    $size = (int)($f['size'] ?? 0);
    if ($size <= 0 || $size > $maxSize) {
        throw new Exception($fieldName.' 파일 용량이 올바르지 않습니다.');
    }

    $orig = (string)($f['name'] ?? '');
    $ext  = $normExt(pathinfo($orig, PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExt, true)) {
        throw new Exception($fieldName.' 허용되지 않는 확장자입니다.');
    }

    $plain = $makeFileName($prefix, $ext); // base filename
    $saved = 'rs_' . $plain;               // ✅ 저장은 rs_
    $dest  = rtrim($uploadDir,'/') . '/' . $saved;

    if (!move_uploaded_file($f['tmp_name'], $dest)) {
        throw new Exception($fieldName.' 파일 저장에 실패했습니다.');
    }

    return $stripRs($saved); // ✅ DB에는 rs_ 제거한 파일명
};


// =========================================================
// 1) 매장정보 조회
// =========================================================
if ($act === 'store_get') {
    try {
        $DB->where('idx', $sh_idx);
        $shop = $DB->getOne('shop_t',
            'idx, mb_idx, sh_title, sh_contents, sh_corp_nm, sh_biz_no, sh_ceo_nm, sh_biz_file, sh_branch_nm, sh_zip, sh_addr1, sh_addr2, sh_lat, sh_lng, sh_img1, sh_img2, sh_img3, sh_img4, sh_img5, sh_tel'
        );

        if (!$shop) {
            echo json_encode(['success'=>false,'message'=>'매장 정보를 찾을 수 없습니다.'], JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_SUBSTITUTE);
            exit;
        }

        // ✅ 혹시 DB에 rs_가 들어가있어도 안전하게 제거
        $shop['sh_biz_file'] = (string)($shop['sh_biz_file'] ?? '');
        $shop['sh_img1'] = $stripRs($shop['sh_img1'] ?? '');
        $shop['sh_img2'] = $stripRs($shop['sh_img2'] ?? '');
        $shop['sh_img3'] = $stripRs($shop['sh_img3'] ?? '');
        $shop['sh_img4'] = $stripRs($shop['sh_img4'] ?? '');
        $shop['sh_img5'] = $stripRs($shop['sh_img5'] ?? '');

        // ✅ 프론트용 URL 필드 추가 (sh_idx 포함 + rs 규칙 반영)
        $shop['sh_idx'] = $sh_idx;
        $shop['sh_biz_file_url'] = $makePublicUrlBiz($shop['sh_biz_file']);

        $shop['sh_img1_url'] = $makePublicUrlShopImg($shop['sh_img1']);
        $shop['sh_img2_url'] = $makePublicUrlShopImg($shop['sh_img2']);
        $shop['sh_img3_url'] = $makePublicUrlShopImg($shop['sh_img3']);
        $shop['sh_img4_url'] = $makePublicUrlShopImg($shop['sh_img4']);
        $shop['sh_img5_url'] = $makePublicUrlShopImg($shop['sh_img5']);

        echo json_encode(['success'=>true,'data'=>$shop], JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_SUBSTITUTE);
        exit;

    } catch (Exception $e) {
        error_log('store_get error: '.$e->getMessage());
        http_response_code(500);
        echo json_encode(['success'=>false,'message'=>'서버 오류'], JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_SUBSTITUTE);
        exit;
    }
}


// =========================================================
// 2) 매장정보 저장(업데이트)
// =========================================================
if ($act === 'store_update') {
    try {
        // ---- 현재 데이터 조회 ----
        $DB->where('idx', $sh_idx);
        $cur = $DB->getOne('shop_t', 'idx, sh_biz_file, sh_img1, sh_img2, sh_img3, sh_img4, sh_img5');
        if (!$cur) {
            echo json_encode(['success'=>false,'message'=>'매장 정보를 찾을 수 없습니다.'], JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_SUBSTITUTE);
            exit;
        }

        // ---- 입력값 ----
        $lat = trim((string)($_POST['sh_lat'] ?? ''));
        $lng = trim((string)($_POST['sh_lng'] ?? ''));

        $upd = [
            'sh_corp_nm'   => trim((string)($_POST['sh_corp_nm'] ?? '')),
            'sh_biz_no'    => trim((string)($_POST['sh_biz_no'] ?? '')),
            'sh_title'     => trim((string)($_POST['sh_title'] ?? '')),
            'sh_tel'       => trim((string)($_POST['sh_tel'] ?? '')),
            'sh_ceo_nm'    => trim((string)($_POST['sh_ceo_nm'] ?? '')),
            'sh_lat'       => $lat,
            'sh_lng'       => $lng,
            'sh_zip'       => trim((string)($_POST['sh_zip'] ?? '')),
            'sh_addr1'     => trim((string)($_POST['sh_addr1'] ?? '')),
            'sh_addr2'     => trim((string)($_POST['sh_addr2'] ?? '')),
            'sh_branch_nm' => trim((string)($_POST['sh_branch_nm'] ?? '')),
            'sh_contents'  => trim((string)($_POST['sh_contents'] ?? '')),
            'sh_lat_num'   => $lat !== '' ? (float)$lat : null,
            'sh_lng_num'   => $lng !== '' ? (float)$lng : null,
        ];

        // 소개글 500자 제한(원하면 제거 가능)
        if (mb_strlen($upd['sh_contents'], 'UTF-8') > 500) {
            $upd['sh_contents'] = mb_substr($upd['sh_contents'], 0, 500, 'UTF-8');
        }

        // ---- 삭제 플래그 ----
        $delBiz = strtoupper((string)($_POST['del_biz'] ?? 'N')) === 'Y';

        $del = [];
        for ($i=1; $i<=5; $i++) {
            $del[$i] = strtoupper((string)($_POST['del_img'.$i] ?? 'N')) === 'Y';
        }

        // ---- 사업자등록증 업로드/삭제 ----
        // 프론트: <input name="biz_file" ...>
        $newBiz = $saveUploadBiz('biz_file', 'biz');
        if ($newBiz !== null) {
            // 기존 파일 삭제(선택)
            if (!empty($cur['sh_biz_file'])) {
                $deleteFileIfExists((string)$cur['sh_biz_file']); // biz는 rs_ 없음
            }
            $upd['sh_biz_file'] = $newBiz; // ✅ DB에는 파일명만
        } else if ($delBiz) {
            if (!empty($cur['sh_biz_file'])) {
                $deleteFileIfExists((string)$cur['sh_biz_file']);
            }
            $upd['sh_biz_file'] = '';
        }

        // ---- 매장 이미지 업로드/삭제 (최대 5장) ----
        // 프론트: <input name="shop_img1" ...> ~ shop_img5
        for ($i=1; $i<=5; $i++) {
            $field = 'shop_img'.$i;
            $col   = 'sh_img'.$i;

            $newImg = $saveUploadShopImg($field, 'img'.$i); // ✅ 저장 rs_, DB rs 제거
            if ($newImg !== null) {
                // 기존 파일 삭제(선택) - 기존은 rs_ 붙여서 디스크에 있음
                $oldPlain = $stripRs($cur[$col] ?? '');
                if ($oldPlain !== '') {
                    $deleteFileIfExists('rs_'.$oldPlain);
                }
                $upd[$col] = $newImg; // ✅ DB에는 rs 제거된 파일명
                continue;
            }

            // 삭제만 체크된 경우 (새 업로드 없을 때)
            if ($del[$i]) {
                $oldPlain = $stripRs($cur[$col] ?? '');
                if ($oldPlain !== '') {
                    $deleteFileIfExists('rs_'.$oldPlain);
                }
                $upd[$col] = '';
            }
        }

        // ✅ 최소 3장 검증(서버에서도 안전장치)
        // "최종 값" 기준으로 개수 계산: cur + upd 반영
        $finalImgs = [];
        for ($i=1; $i<=5; $i++) {
            $col = 'sh_img'.$i;
            if (array_key_exists($col, $upd)) $finalImgs[$i] = $stripRs($upd[$col]);
            else $finalImgs[$i] = $stripRs($cur[$col] ?? '');
        }
        $cnt = 0;
        for ($i=1; $i<=5; $i++) {
            if ($finalImgs[$i] !== '') $cnt++;
        }
        if ($cnt > 0 && $cnt < 3) { // "아예 안 올리는" 케이스는 허용하고, 올리는 경우엔 최소 3장 강제
            echo json_encode(['success'=>false,'message'=>'매장 이미지는 최소 3장을 등록해야 합니다. (현재 '.$cnt.'장)'], JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_SUBSTITUTE);
            exit;
        }

        // ---- 업데이트 ----
        $DB->where('idx', $sh_idx);
        $ok = $DB->update('shop_t', $upd);

        if (!$ok) {
            echo json_encode(['success'=>false,'message'=>'저장 실패: '.$DB->getLastError()], JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_SUBSTITUTE);
            exit;
        }

        echo json_encode(['success'=>true,'message'=>'저장되었습니다.'], JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_SUBSTITUTE);
        exit;

    } catch (Exception $e) {
        error_log('store_update error: '.$e->getMessage());
        http_response_code(500);
        echo json_encode(['success'=>false,'message'=>'서버 오류'], JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_SUBSTITUTE);
        exit;
    }
}


// =========================================================
// 그 외
// =========================================================
echo json_encode(['success'=>false,'message'=>'잘못된 요청입니다.'], JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_SUBSTITUTE);
exit;
