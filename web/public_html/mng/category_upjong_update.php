<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";

try {
    if ($_POST['act'] == "input") {
        header('Content-Type: application/json');

        try {
            $maxFiles = isset($_POST['maxFiles']) ? (int)$_POST['maxFiles'] : 1; // 기본값 1

            if(empty($_POST['ct_name'])) {
                throw new Exception("카테고리명을 입력해주세요.");
            }
            if(empty($_POST['ct_show'])) {
                throw new Exception("노출여부를 선택해주세요.");
            }

            // 트랜잭션 시작
            $DB->startTransaction();

            // ct_order 최대값 조회
            $max_order = $DB->getValue("category_upjong_t", "COALESCE(MAX(ct_order), 0) + 1");

            // 기본 데이터 준비
            $arr_query = array(
                "ct_name" => clean_xss_tags($_POST['ct_name']),
                "ct_show" => $_POST['ct_show'],
                "ct_order" => $max_order,
                "ct_wdate" => $DB->now()
            );

            // 데이터 삽입
            $_last_idx = $DB->insert('category_upjong_t', $arr_query);
            if(!$_last_idx) {
                throw new Exception("데이터 저장에 실패했습니다.");
            }

            // 파일 업로드 처리
            $uploaded_files = []; // 업로드된 파일 정보 저장 배열 추가

            if (!is_dir($ct_upjong_dir)) {
                if (!mkdir($ct_upjong_dir, 0707, true)) {
                    throw new Exception("category 디렉토리 생성 실패");
                }
                chmod($ct_upjong_dir, 0707);
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
                        $filename = "ct_img_{$_last_idx}_{$filePosition}_{$timestamp}.{$file_ext}";
                        $filepath = $ct_upjong_dir . $filename;

                        if (move_uploaded_file($file['tmp_name'], $filepath)) {
                            chmod($filepath, 0644);

                            // 파일명을 DB에 업데이트
                            $update_data = ["ct_img{$filePosition}" => $filename];
                            $DB->where('ct_id', $_last_idx);
                            if (!$DB->update('category_upjong_t', $update_data)) {
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
                2 => isset($uploaded_files[2]) ? $uploaded_files[2] : ''
            );

            $DB->commit();

            // 성공 응답
            echo json_encode([
                'success' => true,
                'message' => '등록되었습니다.',
                'ct_id' => $_last_idx,
                'final_images' => $uploaded_files
            ]);

        } catch (Exception $e) {
            $DB->rollback();
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }

    }
    else if ($_POST['act'] == "update") {
        header('Content-Type: application/json');

        try {
            $maxFiles = isset($_POST['maxFiles']) ? (int)$_POST['maxFiles'] : 1; // 기본값 1

            if(empty($_POST['ct_name'])) {
                throw new Exception("카테고리명을 입력해주세요.");
            }
            if(empty($_POST['ct_show'])) {
                throw new Exception("노출여부를 선택해주세요.");
            }

            $DB->startTransaction();

            // 기존 데이터 조회
            $DB->where('ct_id', $_POST['ct_idx']);
            $old_data = $DB->getOne('category_upjong_t');

            // 기본 데이터 업데이트
            $arr_query = array(
                "ct_name" => clean_xss_tags($_POST['ct_name']),
                "ct_show" => $_POST['ct_show'],
                "ct_udate" => $DB->now()
            );

            $finalImages = [];
            // 현재 이미지 상태 저장
            for ($i = 1; $i <= $maxFiles; $i++) {
                $finalImages["ct_img{$i}"] = !empty($old_data["ct_img{$i}"]) ? $old_data["ct_img{$i}"] : '';
            }


            // 삭제된 파일 처리
            if (!empty($_POST['removed_files'])) {
                $removedFiles = json_decode($_POST['removed_files'], true);
                foreach ($removedFiles as $fileNum) {
                    if (!empty($finalImages[$fileNum])) {
                        $old_file = $ct_upjong_dir . $finalImages[$fileNum];
                        if(file_exists($old_file)) {
                            unlink($old_file);
                        }
                        $finalImages[$fileNum] = '';
                    }
                }

                // 빈 공간 없이 이미지 재정렬
                $tempImages = array_values(array_filter($finalImages));
                $finalImages = array_fill(1, $maxFiles, ''); // 배열 초기화
                foreach ($tempImages as $index => $filename) {
                    $finalImages[$index + 1] = $filename;
                }

                // DB 업데이트를 위한 쿼리 배열 설정
                foreach ($finalImages as $pos => $filename) {
                    $arr_query["ct_img{$pos}"] = $filename;
                }
            }

            // 이미지 순서 처리
            $newPositions = array();
            if (!empty($_POST['image_order'])) {
                $imageOrder = json_decode($_POST['image_order'], true);
                $tempImages = array_fill(1, $maxFiles, '');

                foreach ($imageOrder as $index => $item) {
                    $position = $index + 1;
                    if ($position > $maxFiles) break;

                    if ($item['type'] === 'existing') {
                        $imageNum = str_replace('ct_img', '', $item['id']);
                        if (!empty($finalImages[$imageNum])) {
                            $tempImages[$position] = $finalImages[$imageNum];
                        }
                    } elseif ($item['type'] === 'new') {
                        $newPositions[] = $position;
                    }
                }

                // 기존 이미지 순서 재배치
                if (!empty(array_filter($tempImages))) {
                    $finalImages = $tempImages;
                }

                // 이미지 순서 업데이트
                foreach ($finalImages as $pos => $filename) {
                    $arr_query["ct_img{$pos}"] = $filename;
                }
            }

            // 기본 데이터 업데이트
            $DB->where('ct_id', $_POST['ct_idx']);
            if(!$DB->update('category_upjong_t', $arr_query)) {
                throw new Exception("데이터 수정에 실패했습니다.");
            }

            // 새 파일 업로드 처리
            if (!empty($_FILES)) {
                foreach ($_FILES as $key => $file) {
                    if ($file['error'] === 0) {
                        // 새 이미지 위치 결정 - 수정된 로직
                        $position = !empty($newPositions) ? array_shift($newPositions) : (
                        array_search('', $finalImages) ?: count($finalImages) + 1
                        );

                        // position이 maxFiles를 초과하지 않도록 보장
                        $position = min($position, $maxFiles);

                        $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                        $timestamp = time(); // 타임스탬프 추가
                        $filename = "ct_img_".$_POST['ct_idx'] . "_{$position}_{$timestamp}." . $file_ext;
                        $filepath = $ct_upjong_dir . $filename;

                        // 기존 파일 삭제
                        if (!empty($finalImages[$position])) {
                            $old_file = $ct_upjong_dir . $finalImages[$position];
                            if(file_exists($old_file)) {
                                unlink($old_file);
                            }
                        }

                        if (move_uploaded_file($file['tmp_name'], $filepath)) {
                            chmod($filepath, 0644);

                            $update_data = ["ct_img{$position}" => $filename];
                            $DB->where('ct_id', $_POST['ct_idx']);
                            if (!$DB->update('category_upjong_t', $update_data)) {
                                throw new Exception("파일 정보 업데이트 실패");
                            }

                            $finalImages[$position] = $filename;
                        }
                    }
                }
            }

            $DB->commit();

            // 최종 이미지 상태 조회
            $DB->where('ct_id', $_POST['ct_idx']);
            $final_data = $DB->getOne('category_upjong_t');

            $finalImages = array(
                1 => !empty($final_data['ct_img1']) ? $final_data['ct_img1'] : '',
            );

            echo json_encode([
                'success' => true,
                'message' => '수정되었습니다.',
                'ct_id' => $_POST['ct_idx'],
                'final_images' => $finalImages
            ]);

        } catch (Exception $e) {
            $DB->rollback();
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }

    }
    else if ($_POST['act'] == "loadimage") {
        header('Content-Type: application/json');

        try {
            if(empty($_POST['ct_idx'])) {
                throw new Exception("필수 파라미터가 누락되었습니다.");
            }

            // 카테고리 정보 조회
            $DB->where('ct_id', $_POST['ct_idx']);
            $row = $DB->getOne('category_upjong_t');

            if(!$row) {
                throw new Exception("데이터를 찾을 수 없습니다.");
            }

            $result = array();

            // 이미지 1, 2 정보 처리
            for($i = 1; $i <= 2; $i++) {
                $img_key = "ct_img".$i;
                $result[$img_key] = array(
                    'exists' => false,
                    'url' => '',
                    'filename' => ''
                );

                if(!empty($row[$img_key])) {
                    $filepath = $ct_upjong_dir . $row[$img_key];
                    if(file_exists($filepath)) {
                        $result[$img_key] = array(
                            'exists' => true,
                            'url' => $ct_upjong_url . $row[$img_key],
                            'filename' => $row[$img_key]
                        );
                    }
                }
            }

            echo json_encode(array(
                'success' => true,
                'data' => $result
            ));

        } catch (Exception $e) {
            echo json_encode(array(
                'success' => false,
                'message' => $e->getMessage()
            ));
        }
        exit;

    }
    else if ($_POST['act'] == "delete") {
        $DB->startTransaction();

        // 기존 데이터 조회
        $DB->where('ct_id', $_POST['idx']);
        $row = $DB->getOne('category_upjong_t');

        if(!$row) {
            throw new Exception("삭제할 데이터가 존재하지 않습니다.");
        }

        // 파일 삭제
        $ct_category_dir = $_SERVER['DOCUMENT_ROOT']."/data/category/";
        for ($i = 1; $i <= 2; $i++) {
            if (!empty($row["ct_img{$i}"])) {
                $file_path = $ct_category_dir . $row["ct_img{$i}"];
                if(file_exists($file_path)) {
                    unlink($file_path);
                }
            }
        }

        // DB에서 삭제
        $DB->where('ct_id', $_POST['idx']);
        if(!$DB->delete('category_upjong_t')) {
            throw new Exception("데이터 삭제에 실패했습니다.");
        }

        // 순서 재정렬
        $DB->where('ct_order', $row['ct_order'], '>');
        $arr_query = array(
            'ct_order' => $DB->dec(1)
        );
        $DB->update('category_upjong_t', $arr_query);

        $DB->commit();
        echo "Y";
    }

} catch (Exception $e) {
    if(isset($DB) && $DB->count > 0) {
        $DB->rollback();
    }
    p_alert($e->getMessage(), "back");
}

if ($_POST['act'] == "list") {
    unset($list);
    $DB->pageLimit = $_POST['obj_limit_num'];
    $pg = $_POST['obj_pg'];

    //검색
    if ($_POST['obj_search_txt']) {
        if ($_POST['obj_sel_search'] == "all") {
            $DB->where('( instr(a1.ct_title, \''.$_POST['obj_search_txt'].'\') or instr(a1.ct_title, \''.$_POST['obj_search_txt'].'\') )');
        } else {
            $DB->where('( instr('.$_POST['obj_sel_search'].', \''.$_POST['obj_search_txt'].'\') )');
        }
    }

    if ($_POST['ct_show']) {
        $DB->where('a1.ct_show', $_POST['ct_show']);
    }

    //정렬
    if ($_POST['obj_order_desc_asc'] == '1') {
        $DB->orderBy("a1.ct_order", "asc");
    } else {
        $DB->orderBy("a1.ct_order", "asc");
    }

    $DB->setTrace(true); // 쿼리 추적 활성화
    $list = $DB->arraybuilder()->paginate("category_upjong_t a1", $pg, '*, ct_id as ct_idx');
    $debug = $DB->trace; // 모든 쿼리 정보 배열로 반환
    //print_r($debug);

    //페이징
    $n_page = $DB->totalPages;
    $counts = $DB->totalCount;
    $counts = $counts - (($pg - 1) * $_POST['obj_limit_num']);
    ?>
    <div class="table-responsive margin-top-20">
        <table class="table table-striped table-bordered margin-bottom-20" id="listTable" style="min-width: 800px">
            <thead class="thead-dark">
            <tr>
                <th class="text-center" style="width:50px;">
                    <input type="checkbox" id="selectAll" class="custom-checkbox-list" />
                </th>
                <th class="text-center" style="width:80px;">
                    번호
                </th>
                <th class="text-center" style="width:120px;">
                    관리
                </th>
                <th class="text-center" style="width:100px;">
                    아이콘
                </th>
                <th class="text-center">
                    분류명
                </th>
                <th class="text-center" style="width:100px;">
                    노출여부
                </th>
                 <th class="text-center" style="width:130px;">
                    등록일시
                </th>
                <th class="text-center" style="width:130px;">
                    수정일시
                </th>
            </tr>
            </thead>
            <tbody>
            <?php
            if ($list) {
                foreach ($list as $row) {
                    ?>
                    <tr draggable="true" data-id="<?=$row['ct_idx']?>">
                        <td class="text-center checkbox-wrapper">
                            <input type="checkbox" class="rowCheckbox custom-checkbox-list" />
                        </td>
                        <td data-title="번호" class="text-center">
                            <?=$counts?>
                        </td>
                        <td data-title="관리" class="text-center">
                            <input type="button" class="btn btn-outline-info btn-sm" value="수정" onclick="location.href='./category_upjong_form.php?act=update&ct_idx=<?=$row['ct_idx']?>'" />
                            <input type="button" class="btn btn-outline-danger btn-sm" value="삭제" onclick="f_post_del('./category_upjong_update.php', '<?=$row['ct_idx']?>');" />
                        </td>
                        <td data-title="노출여부" class="text-center">
                            <?php
                              if(!empty($row['ct_img1'])):
                               $img = resolveImageUrl($row['ct_img1'], $ct_upjong_dir, $ct_upjong_url);
                              ?>
                              <img src="<?php echo $img?>" class="ct_icon" />
                            <?php endif; ?>
                        </td>
                        <td data-title="제목">
                            <span class="line1_text"><?=$row['ct_name']?></span>
                        </td>
                        <td data-title="노출여부" class="text-center">
                            <label class="switch switch-sm"><input type="checkbox" name="ct_show" <?=$row['ct_show']=="Y" ? "checked" : ""?> value="<?=$row['ct_show']?>"><span></span></label>
                        </td>
                        <td data-title="등록일시" class="text-center">
                            <?=DateType($row['ct_wdate'], 4)?>
                        </td>
                        <td data-title="수정일시" class="text-center">
                            <?=DateType($row['ct_udate'], 4)?>
                        </td>
                    </tr>
                    <?php
                    $counts--;
                }
            } else {
                ?>
                <tr>
                    <td colspan="8" class="text-center"><b>자료가 없습니다.</b></td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </div>

    <?php
    if($n_page > 1) {
        echo page_listing_xhr($pg, $n_page, 'f_get_box_mng_list');
    }


} else if($_POST['act'] == "updateSequence") {
    header('Content-Type: application/json');

    try {
        // 디버깅을 위한 로그
        error_log("Received POST data: " . print_r($_POST, true));

        if (!isset($_POST['act']) || $_POST['act'] !== 'updateSequence') {
            throw new Exception('잘못된 요청입니다.');
        }

        if (!isset($_POST['data']) || empty($_POST['data'])) {
            throw new Exception('순서 데이터가 없습니다.');
        }

        $data = $_POST['data'];

        // 데이터가 문자열로 왔을 경우 디코딩 시도
        if (is_string($data)) {
            $data = json_decode($data, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('JSON 파싱 오류: ' . json_last_error_msg());
            }
        }

        if (!is_array($data)) {
            throw new Exception('데이터 형식이 올바르지 않습니다.');
        }

        // 디버깅을 위한 로그
        error_log("Parsed data: " . print_r($data, true));

        foreach ($data as $item) {
            if (!isset($item['idx']) || !isset($item['sequence'])) {
                throw new Exception('필수 필드가 누락되었습니다.');
            }

            $idx = (int)$item['idx'];
            $sequence = (int)$item['sequence'];

            if ($idx <= 0 || $sequence <= 0) {
                throw new Exception('잘못된 데이터가 포함되어 있습니다.');
            }

            try {
                $arr_query = array(
                    'nt_order' => $sequence
                );

                $DB->where('idx', $idx);
                $DB->update('notice_t', $arr_query);

            } catch (Exception $e) {
                throw new Exception('데이터베이스 업데이트 중 오류 발생: ' . $e->getMessage());
            }
        }

        echo json_encode([
            'success' => true,
            'message' => '순서가 성공적으로 변경되었습니다.'
        ]);

    } catch (Exception $e) {
        error_log("Error in notice_update.php: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

    exit;

} else if($_POST['act'] == "updateShow") {

    $ct_idx = (int)$_POST['id'];
    $ct_show = $_POST['ct_show'];

    $DB->where('ct_id', $_POST['id']);
    $arr_query = array(
        'ct_show' => $ct_show
    );
    $result = $DB->update('category_upjong_t', $arr_query);

    // 결과 반환
    echo json_encode([
        'success' => $result,
        'message' => $result ? '성공적으로 변경되었습니다.' : '처리 중 오류가 발생했습니다.'
    ]);
    exit;

}

include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
