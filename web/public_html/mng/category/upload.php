<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";
require_once $_SERVER['DOCUMENT_ROOT']."/vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\IOFactory;

function sanitizeYear($value) {
    $value = trim($value);
    // 비어있거나 무효한 값은 null 처리
    $invalids = ['-', '-', '–', '—', '-', '-', '-'];
    if (in_array(strtolower($value), array_map('strtolower', $invalids))) {
        return null;
    }
    // 숫자 4자리일 경우만 허용
    return preg_match('/^\d{4}$/', $value) ? $value : null;
}

try {
    if (!isset($_FILES['excel']) || $_FILES['excel']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("엑셀 파일을 업로드해주세요.");
    }

    $inputFileName = $_FILES['excel']['tmp_name'];

    $spreadsheet = IOFactory::load($inputFileName);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray(null, true, true, true);

    $DB->startTransaction();

    foreach ($rows as $index => $row) {
        if ($index === 1) continue; // 첫 줄은 헤더로 간주

        $origin       = trim($row['A']);
        $manufacturer = trim($row['B']);
        $model1       = trim($row['C']);
        $model2       = trim($row['D']);
        $start        = trim($row['E']) !== '' ? sanitizeYear($row['E']) : null;
        $end          = trim($row['F']) !== '' ? sanitizeYear($row['F']) : null;
        $trim         = trim($row['G']);

//        if (!$manufacturer || !$model1 || !$model2 || !$trim || !$subtrim) continue;

        if ($origin) {
            // 제조사 등록 또는 조회
            $DB->where('og_name', $origin);
            $origin_id = $DB->getValue('origin_t', 'id');

            unset($arr_query);
            $arr_query = array(
                "og_name" => $manufacturer,
                "og_datetime" => $DB->now(),
                "og_updatetime" => $DB->now(),
            );

            if (!$origin_id) {
                $origin_id = $DB->insert('origin_t', $arr_query);
            }
        }

        if ($origin_id && $manufacturer) {
            // 제조사 등록 또는 조회
            $DB->where('mf_name', $manufacturer);
            $DB->where('og_id', $origin_id);
            $manufacturer_id = $DB->getValue('manufacturers_t', 'id');

            unset($arr_query);
            $arr_query = array(
                "og_id" => $origin_id,
                "mf_name" => $manufacturer,
                "mf_datetime" => $DB->now(),
                "mf_updatetime" => $DB->now(),
                'mf_division' => '',
            );

            if (!$manufacturer_id) {
                $manufacturer_id = $DB->insert('manufacturers_t', $arr_query);
            }
        }

        if ($manufacturer_id && $model1) {
            // 1차 모델
            $DB->where('md_name', $model1);
            $DB->where('mf_id', $manufacturer_id);
            $model1_id = $DB->getValue('model_t', 'id');
            unset($arr_query);
            $arr_query = array(
                "mf_id" => $manufacturer_id,
                'md_name' => $model1,
                "md_datetime" => $DB->now(),
                "md_updatetime" => $DB->now(),
            );
            if (!$model1_id) {
                $model1_id = $DB->insert('model_t', $arr_query);
            }
        }

        if ($model1_id && $model2) {
            // 2차 모델
            $DB->where('mv_name', $model2);
            $DB->where('md_id', $model1_id);
            $model2_id = $DB->getValue('model_variants_t', 'id');
            unset($arr_query);
            $arr_query = array(
                "md_id" => $model1_id,
                'mv_name' => $model2,
                "mv_start" => $start,
                'mv_end' => $end,
                "mv_datetime" => $DB->now(),
                "mv_updatetime" => $DB->now(),
            );
            if (!$model2_id) {
                $model2_id = $DB->insert('model_variants_t', $arr_query);
            }
        }

        if ($model2_id && $trim) {
            // 등급
            $DB->where('cs_name', $trim);
            $DB->where('mv_id', $model2_id);
            $trim_id = $DB->getValue('class_t', 'id');
            unset($arr_query);
            $arr_query = array(
                "mv_id" => $model2_id,
                'cs_name' => $trim,
                "cs_datetime" => $DB->now(),
                "cs_updatetime" => $DB->now(),
            );
            if (!$trim_id) {
                $trim_id = $DB->insert('class_t', $arr_query);
            }
        }

//        if ($trim_id && $subtrim) {
//            // 세부 등급
//            $DB->where('cv_name', $subtrim);
//            $DB->where('cs_id', $trim_id);
//            $exists = $DB->has('class_variants_t');
//            unset($arr_query);
//            $arr_query = array(
//                "cs_id" => $trim_id,
//                'cv_name' => $subtrim,
//                "cv_datetime" => $DB->now(),
//                "cv_updatetime" => $DB->now(),
//            );
//            if (!$exists) {
//                $DB->insert('class_variants_t', $arr_query);
//            }
//        }
    }

    $DB->commit();

    echo "<script>alert('엑셀 데이터가 성공적으로 등록되었습니다.'); location.href='./list.php';</script>";

} catch (Exception $e) {
    $DB->rollback();
    echo "<script>alert('오류 발생: " . addslashes($e->getMessage()) . "'); history.back();</script>";
}
?>
