<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";

// 카테고리 이미지 저장 디렉토리
$ct_category_dir = '../data/category/';

// 카테고리 이미지 업로드 처리 함수
function handle_category_file_upload($file, $file_num, $category_id, $dir) {
    global $DB;

    if(!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }

    $file_name = $file['name'];
    $file_tmp = $file['tmp_name'];
    $file_size = $file['size'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    // 허용된 파일 확장자
    $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif', 'webp', 'svg');

    if(!in_array($file_ext, $allowed_extensions)) {
        throw new Exception("허용되지 않는 파일 형식입니다. (jpg, jpeg, png, gif, webp, svg만 가능)");
    }

    // 파일 크기 제한 (5MB)
    if($file_size > 5242880) {
        throw new Exception("파일 크기는 5MB 이하여야 합니다.");
    }

    // 저장할 파일명 생성
    $new_file_name = 'category_' . $category_id . '_' . $file_num . '_' . time() . '.' . $file_ext;

    // 파일 업로드
    if(!move_uploaded_file($file_tmp, $dir . $new_file_name)) {
        throw new Exception("파일 업로드에 실패했습니다.");
    }

    // 기존 파일 정보 조회 및 삭제
    $field_name = '';
    switch($file_num) {
        case 1:
            $field_name = 'ct_file1';
            break;
        case 2:
            $field_name = 'ct_file2';
            break;
        case 3:
            $field_name = 'ct_banner';
            break;
    }

    if($field_name) {
        $DB->where('ct_id', $category_id);
        $old_file = $DB->getOne('category_t', $field_name);

        if($old_file && !empty($old_file[$field_name])) {
            @unlink($dir . $old_file[$field_name]);
        }

        // DB 업데이트
        $arr_query = array(
            $field_name => $new_file_name
        );

        $DB->where('ct_id', $category_id);
        $DB->update('category_t', $arr_query);
    }

    return $new_file_name;
}

// 카테고리 경로 생성 함수
function generate_category_path($parent_id) {
    global $DB;

    if(!$parent_id) {
        return '/';
    }

    $DB->where('ct_id', $parent_id);
    $parent = $DB->getOne('category_t', 'ct_path, ct_id');

    if(!$parent) {
        return '/';
    }

    return $parent['ct_path'] . $parent['ct_id'] . '/';
}

// 카테고리 전체 이름 생성 함수
function generate_full_category_name($parent_id, $name) {
    global $DB;

    if(!$parent_id) {
        return $name;
    }

    $DB->where('ct_id', $parent_id);
    $parent = $DB->getOne('category_t', 'ct_full_name');

    if(!$parent) {
        return $name;
    }

    return $parent['ct_full_name'] . ' > ' . $name;
}

// 카테고리 레벨 계산 함수
function calculate_category_level($parent_id) {
    global $DB;

    if(!$parent_id) {
        return 1;
    }

    $DB->where('ct_id', $parent_id);
    $parent = $DB->getOne('category_t', 'ct_level');

    if(!$parent) {
        return 1;
    }

    return $parent['ct_level'] + 1;
}

// 자식 카테고리가 있는지 확인하는 함수
function has_child_categories($category_id) {
    global $DB;

    $DB->where('ct_pid', $category_id);
    return $DB->getValue('category_t', 'COUNT(*)') > 0;
}

// 카테고리 등록 처리
if ($_POST['act'] == "input") {
    header('Content-Type: application/json');

    try {
        // 필수 입력값 체크
        if(empty($_POST['ct_code'])) {
            throw new Exception("카테고리 코드를 입력해주세요.");
        }
        if(empty($_POST['ct_name'])) {
            throw new Exception("카테고리명을 입력해주세요.");
        }
        if(empty($_POST['ct_show'])) {
            throw new Exception("노출여부를 선택해주세요.");
        }

        // 카테고리 코드 형식 검사 (영문, 숫자, 하이픈만 허용)
        if(!preg_match('/^[A-Za-z0-9\-]+$/', $_POST['ct_code'])) {
            throw new Exception("카테고리 코드는 영문, 숫자, 하이픈(-)만 사용 가능합니다.");
        }

        // 카테고리 코드 중복 검사
        $DB->where('ct_code', $_POST['ct_code']);
        $exists = $DB->getOne('category_t');
        if($exists) {
            throw new Exception("이미 사용 중인 카테고리 코드입니다.");
        }

        // 트랜잭션 시작
        $DB->startTransaction();

        // 상위 카테고리 정보 가져오기
        $parent_id = !empty($_POST['ct_pid']) ? $_POST['ct_pid'] : null;

        // 카테고리 경로, 레벨, 전체 이름 생성
        $ct_path = generate_category_path($parent_id);
        $ct_level = calculate_category_level($parent_id);
        $ct_full_name = generate_full_category_name($parent_id, $_POST['ct_name']);

        // 같은 부모 내에서의 최대 순서값 조회
        $DB->where('ct_pid', $parent_id);
        $max_rank = $DB->getValue("category_t", "COALESCE(MAX(ct_rank), 0) + 10");

        // 기본값이 없을 경우 10으로 설정
        $ct_rank = isset($_POST['ct_rank']) && !empty($_POST['ct_rank']) ? $_POST['ct_rank'] : ($max_rank ? $max_rank : 10);

        // 카테고리 데이터 준비
        unset($arr_query);
        $arr_query = array(
            "ct_code" => clean_xss_tags($_POST['ct_code']),
            "ct_name" => clean_xss_tags($_POST['ct_name']),
            "ct_sub_name" => clean_xss_tags($_POST['ct_sub_name']),
            "ct_level" => $ct_level,
            "ct_pid" => $parent_id,
            "ct_path" => $ct_path,
            "ct_full_name" => $ct_full_name,
            "ct_rank" => $ct_rank,
            "ct_description" => clean_xss_tags($_POST['ct_description']),
            "ct_meta_title" => clean_xss_tags($_POST['ct_meta_title']),
            "ct_meta_keywords" => clean_xss_tags($_POST['ct_meta_keywords']),
            "ct_meta_desc" => clean_xss_tags($_POST['ct_meta_desc']),
            "ct_url_alias" => clean_xss_tags($_POST['ct_url_alias']),
            "ct_show" => $_POST['ct_show'],
            "ct_show_menu" => $_POST['ct_show_menu'],
            "ct_show_main" => $_POST['ct_show_main'],
            "ct_is_leaf" => 'Y',  // 새로 추가되는 카테고리는 기본적으로 최하위 카테고리
            "ct_created_at" => $DB->now(),
        );

        //echo json_encode([
        //  'success' => false,
        //  'arr' => $arr_query,
        //]);
        //exit;

        // 상위 카테고리가 있으면 해당 카테고리의 최하위 여부를 N으로 변경
        if($parent_id) {
            $DB->where('ct_id', $parent_id);
            $DB->update('category_t', array('ct_is_leaf' => 'N'));
        }

        // 카테고리 등록
        $_last_idx = $DB->insert('category_t', $arr_query);

        // 파일 업로드 처리
        if($_last_idx) {
            // 카테고리 이미지
            if($_FILES["ct_file1"]['name']) {
                handle_category_file_upload($_FILES["ct_file1"], 1, $_last_idx, $ct_category_dir);
            }

            // 카테고리 아이콘
            if($_FILES["ct_file2"]['name']) {
                handle_category_file_upload($_FILES["ct_file2"], 2, $_last_idx, $ct_category_dir);
            }

            // 카테고리 배너
            if($_FILES["ct_banner"]['name']) {
                handle_category_file_upload($_FILES["ct_banner"], 3, $_last_idx, $ct_category_dir);
            }
        }

        $DB->commit();

        // 성공 응답
        echo json_encode([
            'success' => true,
            'message' => '카테고리가 등록되었습니다.',
            'ct_id' => $_last_idx,
            'redirect' => './category_list.php',
        ]);

    } catch (Exception $e) {
        $DB->rollback();
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}
// 카테고리 수정 처리
elseif ($_POST['act'] == "update") {
    header('Content-Type: application/json');

    try {
        // 필수 입력값 체크
        if(empty($_POST['ct_code'])) {
            throw new Exception("카테고리 코드를 입력해주세요.");
        }
        if(empty($_POST['ct_name'])) {
            throw new Exception("카테고리명을 입력해주세요.");
        }
        if(empty($_POST['ct_show'])) {
            throw new Exception("노출여부를 선택해주세요.");
        }

        // 카테고리 코드 형식 검사 (영문, 숫자, 하이픈만 허용)
        if(!preg_match('/^[A-Za-z0-9\-]+$/', $_POST['ct_code'])) {
            throw new Exception("카테고리 코드는 영문, 숫자, 하이픈(-)만 사용 가능합니다.");
        }

        // 카테고리 코드 중복 검사 (자기 자신 제외)
        $DB->where('ct_code', $_POST['ct_code']);
        $DB->where('ct_id', $_POST['ct_id'], '!=');
        $exists = $DB->getOne('category_t');
        if($exists) {
            throw new Exception("이미 사용 중인 카테고리 코드입니다.");
        }

        // 트랜잭션 시작
        $DB->startTransaction();

        // 현재 카테고리 정보 조회
        $DB->where('ct_id', $_POST['ct_id']);
        $current_category = $DB->getOne('category_t');

        if(!$current_category) {
            throw new Exception("수정할 카테고리 정보가 존재하지 않습니다.");
        }

        // 상위 카테고리 변경 여부 확인
        $parent_id = !empty($_POST['ct_pid']) ? $_POST['ct_pid'] : null;
        $parent_changed = $current_category['ct_pid'] != $parent_id;

        // 자기 자신을 상위 카테고리로 설정할 수 없음
        if($parent_id == $_POST['ct_id']) {
            throw new Exception("자기 자신을 상위 카테고리로 설정할 수 없습니다.");
        }

        // 하위 카테고리를 상위 카테고리로 설정할 수 없음 (순환 참조 방지)
        if($parent_id) {
            $temp_parent = $parent_id;
            while($temp_parent) {
                $DB->where('ct_id', $temp_parent);
                $parent_info = $DB->getOne('category_t', 'ct_pid');

                if(!$parent_info) break;

                if($parent_info['ct_pid'] == $_POST['ct_id']) {
                    throw new Exception("하위 카테고리를 상위 카테고리로 설정할 수 없습니다.");
                }

                $temp_parent = $parent_info['ct_pid'];
            }
        }

        // 상위 카테고리가 변경된 경우, 경로, 레벨, 전체 이름 재생성
        if($parent_changed) {
            $ct_path = generate_category_path($parent_id);
            $ct_level = calculate_category_level($parent_id);
            $ct_full_name = generate_full_category_name($parent_id, $_POST['ct_name']);

            // 이전 상위 카테고리의 최하위 여부 업데이트
            if($current_category['ct_pid']) {
                $DB->where('ct_pid', $current_category['ct_pid']);
                $siblings_count = $DB->getValue('category_t', 'COUNT(*)');

                if($siblings_count <= 1) {
                    $DB->where('ct_id', $current_category['ct_pid']);
                    $DB->update('category_t', array('ct_is_leaf' => 'Y'));
                }
            }

            // 새 상위 카테고리의 최하위 여부 업데이트
            if($parent_id) {
                $DB->where('ct_id', $parent_id);
                $DB->update('category_t', array('ct_is_leaf' => 'N'));
            }
        } else {
            $ct_path = $current_category['ct_path'];
            $ct_level = $current_category['ct_level'];
            $ct_full_name = generate_full_category_name($parent_id, $_POST['ct_name']);
        }

        // 카테고리 데이터 준비
        unset($arr_query);
        $arr_query = array(
            "ct_code" => clean_xss_tags($_POST['ct_code']),
            "ct_name" => clean_xss_tags($_POST['ct_name']),
            "ct_sub_name" => clean_xss_tags($_POST['ct_sub_name']),
            "ct_level" => $ct_level,
            "ct_pid" => $parent_id,
            "ct_path" => $ct_path,
            "ct_full_name" => $ct_full_name,
            "ct_rank" => $_POST['ct_rank'],
            "ct_description" => clean_xss_tags($_POST['ct_description']),
            "ct_meta_title" => clean_xss_tags($_POST['ct_meta_title']),
            "ct_meta_keywords" => clean_xss_tags($_POST['ct_meta_keywords']),
            "ct_meta_desc" => clean_xss_tags($_POST['ct_meta_desc']),
            "ct_url_alias" => clean_xss_tags($_POST['ct_url_alias']),
            "ct_show" => $_POST['ct_show'],
            "ct_show_menu" => $_POST['ct_show_menu'],
            "ct_show_main" => $_POST['ct_show_main'],
            "ct_updated_at" => $DB->now(),
        );

        // 카테고리 정보 업데이트
        $DB->where('ct_id', $_POST['ct_id']);
        $DB->update('category_t', $arr_query);
        $_last_idx = $_POST['ct_id'];

        // 파일 삭제 처리
        for($i = 1; $i <= 3; $i++) {
            if($_POST["file{$i}_delete"] == 'Y') {
                $field_name = '';
                switch($i) {
                    case 1: $field_name = 'ct_file1'; break;
                    case 2: $field_name = 'ct_file2'; break;
                    case 3: $field_name = 'ct_banner'; break;
                }

                if($field_name) {
                    $DB->where('ct_id', $_last_idx);
                    $old_file = $DB->getOne('category_t', $field_name);

                    if($old_file && !empty($old_file[$field_name])) {
                        @unlink($ct_category_dir . $old_file[$field_name]);

                        $DB->where('ct_id', $_last_idx);
                        $DB->update('category_t', array($field_name => ''));
                    }
                }
            }
        }

        // 새로운 파일 업로드 처리
        if($_last_idx) {
            // 카테고리 이미지
            if($_FILES["ct_file1"]['name']) {
                handle_category_file_upload($_FILES["ct_file1"], 1, $_last_idx, $ct_category_dir);
            }

            // 카테고리 아이콘
            if($_FILES["ct_file2"]['name']) {
                handle_category_file_upload($_FILES["ct_file2"], 2, $_last_idx, $ct_category_dir);
            }

            // 카테고리 배너
            if($_FILES["ct_banner"]['name']) {
                handle_category_file_upload($_FILES["ct_banner"], 3, $_last_idx, $ct_category_dir);
            }
        }

        // 하위 카테고리가 있는 경우, 전체 이름 업데이트
        if($current_category['ct_is_leaf'] == 'N') {
            update_child_categories($_last_idx);
        }

        $DB->commit();

        // 성공 응답
        echo json_encode([
            'success' => true,
            'message' => '카테고리가 수정되었습니다.',
            'redirect' => './category_list.php'
        ]);

    } catch (Exception $e) {
        $DB->rollback();
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}
// 카테고리 삭제 처리
elseif ($_POST['act'] == "delete") {
    header('Content-Type: application/json');
    try {
        // 트랜잭션 시작
        $DB->startTransaction();

        // 카테고리 정보 조회
        $DB->where('ct_id', $_POST['ct_id']);
        $row = $DB->getOne('category_t');

        if(!$row) {
            throw new Exception("삭제할 카테고리가 존재하지 않습니다.");
        }

        // 하위 카테고리 존재 여부 확인
        $DB->where('ct_pid', $_POST['ct_id']);
        $has_children = $DB->getValue('category_t', 'COUNT(*)') > 0;

        if($has_children) {
            throw new Exception("하위 카테고리가 있는 카테고리는 삭제할 수 없습니다.");
        }

        // 파일 삭제
        if(!empty($row['ct_file1'])) {
            @unlink($ct_category_dir . $row['ct_file1']);
        }

        if(!empty($row['ct_file2'])) {
            @unlink($ct_category_dir . $row['ct_file2']);
        }

        if(!empty($row['ct_banner'])) {
            @unlink($ct_category_dir . $row['ct_banner']);
        }

        // 카테고리 삭제
        $DB->where('ct_id', $_POST['ct_id']);
        $DB->delete('category_t');

        // 상위 카테고리의 최하위 여부 업데이트
        if($row['ct_pid']) {
            $DB->where('ct_pid', $row['ct_pid']);
            $siblings_count = $DB->getValue('category_t', 'COUNT(*)');

            if($siblings_count == 0) {
                $DB->where('ct_id', $row['ct_pid']);
                $DB->update('category_t', array('ct_is_leaf' => 'Y'));
            }
        }

        $DB->commit();

        // 성공 응답
        echo json_encode([
            'success' => true,
            'message' => '카테고리가 삭제되었습니다.'
        ]);

    } catch (Exception $e) {
        $DB->rollback();
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}
// 카테고리 목록 조회
elseif ($_POST['act'] == "list") {
    unset($list);
    $DB->pageLimit = $_POST['obj_limit_num'];
    $pg = $_POST['obj_pg'];

    // 검색 조건
    if ($_POST['obj_search_txt']) {
        if ($_POST['obj_sel_search'] == "all") {
            $DB->where('( instr(a1.ct_name, \''.$_POST['obj_search_txt'].'\') or instr(a1.ct_code, \''.$_POST['obj_search_txt'].'\') or instr(a1.ct_full_name, \''.$_POST['obj_search_txt'].'\') )');
        } else {
            $DB->where('( instr('.$_POST['obj_sel_search'].', \''.$_POST['obj_search_txt'].'\') )');
        }
    }

    // 노출 여부 필터
    if (isset($_POST['ct_show']) && $_POST['ct_show'] !== '') {
        $DB->where('a1.ct_show', $_POST['ct_show']);
    }

    // 카테고리 레벨 필터
    if (isset($_POST['ct_level']) && $_POST['ct_level'] > 0) {
        $DB->where('a1.ct_level', $_POST['ct_level']);
    }

    // 상위 카테고리 필터
    if (isset($_POST['ct_pid']) && $_POST['ct_pid'] > 0) {
        $DB->where('a1.ct_pid', $_POST['ct_pid']);
    }

    // 정렬
    $DB->orderBy("a1.ct_path", "asc");
    $DB->orderBy("a1.ct_rank", "asc");

    // 쿼리 추적 활성화
    $DB->setTrace(true);
    $list = $DB->arraybuilder()->paginate("category_t a1", $pg);
    $debug = $DB->trace; // 모든 쿼리 정보 배열로 반환

    // 페이징
    $n_page = $DB->totalPages;
    $counts = $DB->totalCount;
    $counts = $counts - (($pg - 1) * $_POST['obj_limit_num']);
    ?>
    <div class="table-responsive margin-top-20">
        <table class="table table-striped table-bordered margin-bottom-20" id="listTable" style="min-width: 1000px">
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
                <th class="text-center" style="width:150px;">
                    카테고리 코드
                </th>
                <th class="text-center">
                    카테고리명
                </th>
                <th class="text-center" style="width:80px;">
                    레벨
                </th>
                <th class="text-center" style="width:80px;">
                    순서
                </th>
                <th class="text-center" style="width:80px;">
                    노출
                </th>
                <th class="text-center" style="width:130px;">
                    등록일시
                </th>
            </tr>
            </thead>
            <tbody>
            <?php
            if ($list) {
                foreach ($list as $row) {
                    // 들여쓰기 계산 (레벨에 따라)
                    $indent = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $row['ct_level'] - 1);
                    $indent_icon = $row['ct_level'] > 1 ? '<i class="fa fa-level-up fa-rotate-90 mr-1"></i>&nbsp;' : '&nbsp;';
                    ?>
                    <tr data-id="<?=$row['ct_id']?>">
                        <td class="text-center checkbox-wrapper">
                            <input type="checkbox" class="rowCheckbox custom-checkbox-list" value="<?=$row['ct_id']?>" />
                        </td>
                        <td data-title="번호" class="text-center">
                            <?=$counts?>
                        </td>
                        <td data-title="관리" class="text-center">
                            <input type="button" class="btn btn-outline-info btn-sm" value="수정" onclick="location.href='./category_form.php?act=update&ct_id=<?=$row['ct_id']?>'" />
                            <input type="button" class="btn btn-outline-danger btn-sm" value="삭제" onclick="deleteCategory('<?=$row['ct_id']?>');" />
                        </td>
                        <td data-title="카테고리 코드">
                            <?=$row['ct_code']?>
                        </td>
                        <td data-title="카테고리명">
                            <?=$indent . $indent_icon?><span class="line1_text"><?=$row['ct_name']?></span>
                            <?php if(!empty($row['ct_sub_name'])) { ?>
                                <small class="text-muted ml-2">(<?=$row['ct_sub_name']?>)</small>
                            <?php } ?>
                        </td>
                        <td data-title="레벨" class="text-center">
                            <?=$row['ct_level']?>
                        </td>
                        <td data-title="순서" class="text-center">
                            <?=$row['ct_rank']?>
                        </td>
                        <td data-title="노출여부" class="text-center">
                            <?=$row['ct_show']?>
                        </td>
                        <td data-title="등록일시" class="text-center">
                            <?=DateType($row['ct_created_at'], 4)?>
                        </td>
                    </tr>
                    <?php
                    $counts--;
                }
            } else {
                ?>
                <tr>
                    <td colspan="9" class="text-center"><b>등록된 카테고리가 없습니다.</b></td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </div>

    <?php
    if($n_page > 1) {
        echo page_listing_xhr($pg, $n_page, 'f_get_category_list');
    }
}
// 카테고리 순서 변경 처리
else if($_POST['act'] == "updateRank") {
    header('Content-Type: application/json');

    try {
        // 디버깅을 위한 로그
        error_log("Received POST data: " . print_r($_POST, true));

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

        // 트랜잭션 시작
        $DB->startTransaction();

        foreach ($data as $item) {
            if (!isset($item['ct_id']) || !isset($item['ct_rank'])) {
                throw new Exception('필수 필드가 누락되었습니다.');
            }

            $ct_id = (int)$item['ct_id'];
            $ct_rank = (int)$item['ct_rank'];

            if ($ct_id <= 0 || $ct_rank <= 0) {
                throw new Exception('잘못된 데이터가 포함되어 있습니다.');
            }

            try {
                $arr_query = array(
                    'ct_rank' => $ct_rank
                );

                $DB->where('ct_id', $ct_id);
                $DB->update('category_t', $arr_query);

            } catch (Exception $e) {
                throw new Exception('데이터베이스 업데이트 중 오류 발생: ' . $e->getMessage());
            }
        }

        $DB->commit();

        echo json_encode([
            'success' => true,
            'message' => '카테고리 순서가 성공적으로 변경되었습니다.'
        ]);

    } catch (Exception $e) {
        $DB->rollback();
        error_log("Error in category_update.php: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

    exit;
}
// 카테고리 트리 구조 가져오기
elseif ($_POST['act'] == "getTree") {
    header('Content-Type: application/json');

    try {
        // 모든 카테고리 가져오기
        $DB->orderBy('ct_level', 'asc');
        $DB->orderBy('ct_rank', 'asc');
        $categories = $DB->get('category_t', null, 'ct_id, ct_pid, ct_name, ct_level, ct_is_leaf, ct_show');

        // 트리 구조로 변환
        $tree = buildCategoryTree($categories);

        echo json_encode([
            'success' => true,
            'data' => $tree
        ]);

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}
// 카테고리 일괄 노출/숨김 처리
elseif ($_POST['act'] == "bulkToggleVisibility") {
    header('Content-Type: application/json');

    try {
        if (!isset($_POST['ids']) || empty($_POST['ids'])) {
            throw new Exception('선택된 카테고리가 없습니다.');
        }

        if (!isset($_POST['show']) || !in_array($_POST['show'], ['Y', 'N'])) {
            throw new Exception('잘못된 노출 상태입니다.');
        }

        $ids = is_array($_POST['ids']) ? $_POST['ids'] : json_decode($_POST['ids'], true);
        $show = $_POST['show'];

        if (!is_array($ids) || empty($ids)) {
            throw new Exception('선택된 카테고리가 없습니다.');
        }

        // 트랜잭션 시작
        $DB->startTransaction();

        $arr_query = array(
            'ct_show' => $show,
            'ct_updated_at' => $DB->now()
        );

        $DB->where('ct_id', $ids, 'IN');
        $DB->update('category_t', $arr_query);

        $DB->commit();

        echo json_encode([
            'success' => true,
            'message' => '선택한 카테고리의 노출 상태가 변경되었습니다.'
        ]);

    } catch (Exception $e) {
        $DB->rollback();
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

// 하위 카테고리 정보 업데이트 (재귀 함수)
function update_child_categories($parent_id) {
    global $DB;

    // 상위 카테고리 정보 조회
    $DB->where('ct_id', $parent_id);
    $parent = $DB->getOne('category_t', 'ct_path, ct_level, ct_full_name, ct_name');

    if(!$parent) {
        return;
    }

    // 하위 카테고리 조회
    $DB->where('ct_pid', $parent_id);
    $children = $DB->get('category_t');

    if(!$children) {
        return;
    }

    // 각 하위 카테고리 정보 업데이트
    foreach($children as $child) {
        // 경로 업데이트
        $new_path = $parent['ct_path'] . $parent_id . '/';

        // 레벨 업데이트
        $new_level = $parent['ct_level'] + 1;

        // 전체 이름 업데이트
        $new_full_name = $parent['ct_full_name'] . ' > ' . $child['ct_name'];

        // 업데이트 쿼리
        $arr_query = array(
            'ct_path' => $new_path,
            'ct_level' => $new_level,
            'ct_full_name' => $new_full_name
        );

        $DB->where('ct_id', $child['ct_id']);
        $DB->update('category_t', $arr_query);

        // 해당 카테고리의 하위 카테고리도 재귀적으로 업데이트
        update_child_categories($child['ct_id']);
    }
}

// 카테고리 트리 구조 생성 함수
function buildCategoryTree($categories, $parent_id = null) {
    $tree = [];

    foreach ($categories as $category) {
        if ($category['ct_pid'] == $parent_id) {
            $children = buildCategoryTree($categories, $category['ct_id']);

            $node = [
                'id' => $category['ct_id'],
                'text' => $category['ct_name'],
                'level' => $category['ct_level'],
                'isLeaf' => $category['ct_is_leaf'] == 'Y',
                'state' => [
                    'opened' => true,
                    'selected' => false
                ]
            ];

            if (!empty($children)) {
                $node['children'] = $children;
            }

            $tree[] = $node;
        }
    }

    return $tree;
}



include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
