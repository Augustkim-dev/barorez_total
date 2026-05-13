<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";

function isValidYear($year) {
    return is_numeric($year) && $year >= 1901 && $year <= 2155;
}

if ($_POST['act'] == "write") {
    header('Content-Type: application/json');

    try {
        // 트랜잭션 시작
        $DB->startTransaction();

        if($_POST['db'] === 'origin_t') {
            $DB->where('og_name', $_POST['data']['nameOnly']);
            $id = $DB->getValue($_POST['db'], 'id');

            unset($arr_query);
            $arr_query = array(
                "og_name" => $_POST['data']['nameOnly'],
                "og_datetime" => $DB->now(),
                "og_updatetime" => $DB->now(),
            );
        }

        if($_POST['db'] === 'manufacturers_t') {
            $DB->where('og_name', $_POST['data']['parentName']);
            $origin_id = $DB->getValue('origin_t', 'id');
            if($origin_id){
                $DB->where('mf_name', $_POST['data']['nameOnly']);
                $DB->where('og_id', $origin_id);
                $id = $DB->getValue($_POST['db'], 'id');

                unset($arr_query);
                $arr_query = array(
                    "og_id" => $origin_id,
                    "mf_name" => $_POST['data']['nameOnly'],
                    "mf_datetime" => $DB->now(),
                    "mf_updatetime" => $DB->now(),
                    'mf_division' => '',
                );
            }
        }

        if($_POST['db'] === 'model_t') {
            $DB->where('mf_name', $_POST['data']['parentName']);
            $manufacturer_id = $DB->getValue('manufacturers_t', 'id');
            if($manufacturer_id) {
                $DB->where('md_name', $_POST['data']['nameOnly']);
                $DB->where('mf_id', $manufacturer_id);
                $id = $DB->getValue($_POST['db'], 'id');

                unset($arr_query);
                $arr_query = array(
                    "mf_id" => $manufacturer_id,
                    'md_name' => $_POST['data']['nameOnly'],
                    "md_datetime" => $DB->now(),
                    "md_updatetime" => $DB->now(),
                );
            }
        }

        if($_POST['db'] === 'model_variants_t') {
            $DB->where('md_name', $_POST['data']['parentName']);
            $model_id = $DB->getValue('model_t', 'id');
            if($model_id) {
                $DB->where('mv_name', $_POST['data']['nameOnly']);
                $DB->where('md_id', $model_id);
                $model2_id = $DB->getValue($_POST['db'], 'id');
                unset($arr_query);
                $arr_query = array(
                    "md_id" => $model_id,
                    'mv_name' => $_POST['data']['nameOnly'],
                    "mv_start" => isValidYear($_POST['data']['startYear']) ? $_POST['data']['startYear'] : null,
                    'mv_end' => isValidYear($_POST['data']['endYear']) ? $_POST['data']['endYear'] : null,
                    "mv_datetime" => $DB->now(),
                    "mv_updatetime" => $DB->now(),
                );
            }
        }

        if($_POST['db'] === 'class_t') {
            $DB->where('mv_name', $_POST['data']['parentName']);
            $model_id2 = $DB->getValue('model_variants_t', 'id');
            if($model_id2) {
                $DB->where('cs_name', $_POST['data']['nameOnly']);
                $DB->where('mv_id', $model_id2);
                $trim_id = $DB->getValue($_POST['db'], 'id');
                unset($arr_query);
                $arr_query = array(
                    "mv_id" => $model_id2,
                    'cs_name' => $_POST['data']['nameOnly'],
                    "cs_datetime" => $DB->now(),
                    "cs_updatetime" => $DB->now(),
                );
            }
        }

//        if($_POST['db'] === 'class_variants_t') {
//            $DB->where('cs_name', $_POST['data']['parentName']);
//            $class_id = $DB->getValue('class_t', 'id');
//            if($class_id) {
//                $DB->where('cv_name', $_POST['data']['nameOnly']);
//                $DB->where('cs_id', $class_id);
//                $id = $DB->has($_POST['db']);
//
//                unset($arr_query);
//                $arr_query = array(
//                    "cs_id" => $class_id,
//                    'cv_name' => $_POST['data']['nameOnly'],
//                    "cv_datetime" => $DB->now(),
//                    "cv_updatetime" => $DB->now(),
//                );
//            }
//        }
        if (!$id) {
            $DB->insert($_POST['db'], $arr_query);
        }

        // 성공 응답
        echo json_encode([
            'success' => true,
            'message' => '저장되었습니다.',
            'post' => $arr_query,
            'uploaded_files' => '',
        ]);
        $DB->commit();
    } catch (Exception $e) {
        $DB->rollback();
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage(),
            'data' => $arr_query
        ]);
    }
}
elseif ($_POST['act'] == "update") {
    header('Content-Type: application/json');

    try {
        // 트랜잭션 시작
        $DB->startTransaction();

        if($_POST['db'] === 'origin_t') {
            $DB->where('id', $_POST['data']['id']);
            $id = $DB->getValue($_POST['db'], 'id');

            unset($arr_query);
            $arr_query = array(
                "og_name" => $_POST['data']['nameOnly'],
                "og_datetime" => $DB->now(),
                "og_updatetime" => $DB->now(),
            );
        }

        if($_POST['db'] === 'manufacturers_t') {
            $DB->where('id', $_POST['data']['parentId']);
            $origin_id = $DB->getValue('origin_t', 'id');
            if($origin_id) {
                $DB->where('mf_name', $_POST['data']['currentName']);
                $DB->where('og_id', $origin_id);
                $id = $DB->getValue($_POST['db'], 'id');

                unset($arr_query);
                $arr_query = array(
                    "mf_name" => $_POST['data']['nameOnly'],
                    "mf_updatetime" => $DB->now(),
                    'mf_division' => '',
                );
            }
        }

        if($_POST['db'] === 'model_t') {
            $DB->where('id', $_POST['data']['parentId']);
            $manufacturer_id = $DB->getValue('manufacturers_t', 'id');
            if($manufacturer_id) {
                $DB->where('md_name', $_POST['data']['currentName']);
                $DB->where('mf_id', $manufacturer_id);
                $id = $DB->getValue($_POST['db'], 'id');

                unset($arr_query);
                $arr_query = array(
                    'md_name' => $_POST['data']['nameOnly'],
                    "md_updatetime" => $DB->now(),
                );
            }
        }

        if($_POST['db'] === 'model_variants_t') {
            $DB->where('id', $_POST['data']['parentId']);
            $model_id = $DB->getValue('model_t', 'id');
            if($model_id) {
                $DB->where('mv_name', $_POST['data']['currentName']);
                $DB->where('md_id', $model_id);
                $id = $DB->getValue($_POST['db'], 'id');

                unset($arr_query);
                $arr_query = array(
                    'mv_name' => $_POST['data']['nameOnly'],
                    "mv_start" => isValidYear($_POST['data']['startYear']) ? $_POST['data']['startYear'] : null,
                    'mv_end' => isValidYear($_POST['data']['endYear']) ? $_POST['data']['endYear'] : null,
                    "mv_updatetime" => $DB->now(),
                );
            }
        }

        if($_POST['db'] === 'class_t') {
            $DB->where('id', $_POST['data']['parentId']);
            $model_id2 = $DB->getValue('model_variants_t', 'id');
            if($model_id2) {
                $DB->where('cs_name', $_POST['data']['currentName']);
                $DB->where('mv_id', $model_id2);
                $id = $DB->getValue($_POST['db'], 'id');

                unset($arr_query);
                $arr_query = array(
                    'cs_name' => $_POST['data']['nameOnly'],
                    "cs_updatetime" => $DB->now(),
                );
            }
        }
//
//        if($_POST['db'] === 'class_variants_t') {
//            $DB->where('id', $_POST['data']['parentId']);
//            $class_id = $DB->getValue('class_t', 'id');
//            if($class_id) {
//                $DB->where('cv_name', $_POST['data']['currentName']);
//                $DB->where('cs_id', $class_id);
//                $id = $DB->getValue($_POST['db'], 'id');
//
//                unset($arr_query);
//                $arr_query = array(
//                    'cv_name' => $_POST['data']['nameOnly'],
//                    "cv_updatetime" => $DB->now(),
//                );
//            }
//        }

        if ($id) {
            $DB->where('id', $id);
            $DB->update($_POST['db'], $arr_query);
        }

        // 성공 응답
        echo json_encode([
            'success' => true,
            'message' => '수정되었습니다.',
            'post' => $DB->getLastQuery(),
            'id' => $_POST,
            'uploaded_files' => '',
        ]);
        $DB->commit();
    } catch (Exception $e) {
        $DB->rollback();
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage(),
            'data' => $arr_query
        ]);
    }
}
elseif ($_POST['act'] == "delete") {
    header('Content-Type: application/json');

    try {
        // 트랜잭션 시작
        $DB->startTransaction();

        $DB->where('id', $_POST['data']['id']);
        $DB->delete($_POST['db']);

        // 성공 응답
        echo json_encode([
            'success' => true,
            'message' => '삭제되었습니다.',
            'post' => $DB->getLastQuery(),
            'id' => $_POST,
            'uploaded_files' => '',
        ]);
        $DB->commit();
    } catch (Exception $e) {
        $DB->rollback();
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage(),
            'data' => $arr_query
        ]);
    }
}
elseif ($_POST['act'] == "imgUpload") {
    header('Content-Type: application/json');

    try {
        // 트랜잭션 시작
        $maxFiles = isset($_POST['maxFiles']) ? (int)$_POST['maxFiles'] : 1; // 기본값 1

        $DB->startTransaction();

        // 파일 업로드 처리
        $uploaded_files = []; // 업로드된 파일 정보 저장 배열 추가

        if (!is_dir($category_dir)) {
            if (!mkdir($category_dir, 0707, true)) {
                throw new Exception("category 디렉토리 생성 실패");
            }
            chmod($category_dir, 0707);
        }

        // FilePond 파일 처리
        if (!empty($_FILES)) {
            $filePosition = 1; // 시작 위치

            foreach ($_FILES as $key => $file) {
                if ($file['error'] === 0 && $filePosition <= $maxFiles) {
                    $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);

                    // 허용된 파일 확장자 검사
                    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
                    if (!in_array(strtolower($file_ext), $allowed_extensions)) {
                        throw new Exception("허용되지 않는 파일 형식입니다.");
                    }

                    // 타임스탬프 추가하여 파일명 중복 방지
                    $timestamp = time();
                    $filename = "manufacturers_img_{$_POST['mf_id']}.{$file_ext}";
                    $filepath = $category_dir . $filename;

                    if (move_uploaded_file($file['tmp_name'], $filepath)) {
                        chmod($filepath, 0644);

                        // 파일명을 DB에 업데이트
                        $update_data = ["mf_img" => $filename];
                        $DB->where('id', $_POST['mf_id']);
                        if (!$DB->update('manufacturers_t', $update_data)) {
                            throw new Exception("파일 정보 업데이트 실패");
                        }

                        // 업로드된 파일 정보 저장
                        $uploaded_files[$filePosition] = $filename;

                        $filePosition++;
                    }
                }
            }
        }

        // 최종 이미지 상태를 배열로 준비
        $finalImages = array(
            1 => isset($uploaded_files[1]) ? $uploaded_files[1] : '',
        );

        $DB->commit();

        // 성공 응답
        echo json_encode([
            'success' => true,
            'message' => '등록되었습니다.',
            'ct_id' => $_POST['mf_id'],
            'final_images' => $uploaded_files
        ]);

    } catch (Exception $e) {
        $DB->rollback();
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage(),
            'data' => $arr_query
        ]);
    }
}
elseif ($_POST['act'] == "imgView") {
    header('Content-Type: application/json');

    try {
        $DB->where('id', $_POST['id']);
        $row = $DB->getOne('manufacturers_t');

        echo json_encode([
            'success' => true,
            'message' => '이미지를 불러왔습니다.',
            'post' => $DB->getLastQuery(),
            'data' => $row,
            'uploaded_files' => '',
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage(),
            'query' => $DB->getLastQuery()
        ]);
    }
    exit;
}
elseif ($_POST['act'] === 'list'){
    header('Content-Type: application/json');

    try {
        $depth = intval($_POST['depth']);
        $parentId = $_POST['parentId'] ?? null;

        $map = [
            0 => ['table' => 'origin_t', 'select' => 'og_name AS name, id', 'where' => '1', 'bind' => false],
            1 => ['table' => 'manufacturers_t', 'select' => 'mf_name AS name, id', 'where' => "og_id = (SELECT id FROM origin_t WHERE id = ?)", 'bind' => true],
            2 => ['table' => 'model_t', 'select' => 'md_name AS name, id', 'where' => "mf_id = (SELECT id FROM manufacturers_t WHERE id = ?)", 'bind' => true],
            3 => ['table' => 'model_variants_t', 'select' => 'mv_name AS name, mv_start, mv_end, id', 'where' => "md_id = (SELECT id FROM model_t WHERE id = ?)", 'bind' => true],
            4 => ['table' => 'class_t', 'select' => 'cs_name AS name, id', 'where' => "mv_id = (SELECT id FROM model_variants_t WHERE id = ?)", 'bind' => true],
        ];

        if (!isset($map[$depth])) throw new Exception("Invalid depth");

        $table = $map[$depth]['table'];
        $select = $map[$depth]['select'];
        $where = $map[$depth]['where'];
        $bind = $map[$depth]['bind'];

        $sql = "SELECT {$select} FROM {$table} WHERE {$where}";
        $result = $bind ? $DB->rawQuery($sql, [$parentId]) : $DB->rawQuery($sql);

        $data = array_map(function($row) use ($parentId) {
            $row['parentId'] = $parentId;
            return $row;
        }, $result);

        echo json_encode([
            'success' => true,
            'message' => '저장되었습니다.',
            'post' => $sql,
            'items' => $data,
            'uploaded_files' => '',
        ]);

    } catch (Exception $e) {
        $DB->rollback();
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage(),
            'data' => $sql
        ]);
    }

    exit;
}

include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
