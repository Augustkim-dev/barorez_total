<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";


$tbl_name = "stype_t";

if ($_POST['act'] == "input") {
    header('Content-Type: application/json');

    try {
        // 필수 입력값 체크
        if(empty($_POST['w_name'])) {
            throw new Exception("이름을 입력해주세요.");
        }
        if(empty($_POST['w_show'])) {
            throw new Exception("노출여부를 선택해주세요.");
        }

        // 트랜잭션 시작
        $DB->startTransaction();

        $max_order = $DB->getValue($tbl_name, "COALESCE(MAX(w_order), 0) + 1");

        unset($arr_query);
        $arr_query = array(
            "w_name" => clean_xss_tags($_POST['w_name']),
            "w_name_en" => clean_xss_tags($_POST['w_name_en']),
            "w_show" => $_POST['w_show'],
            "w_order" => $max_order,
        );

        $_last_idx = $DB->insert($tbl_name, $arr_query);
        if(!$_last_idx) {
            throw new Exception("데이터 저장에 실패했습니다.");
        }

        $DB->commit();

        // 성공 응답
        echo json_encode([
            'success' => true,
            'message' => '등록되었습니다.',
            'redirect' => './list.php',
        ]);

    } catch (Exception $e) {
        $DB->rollback();
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}
else if($_POST['act'] == "update") {
    header('Content-Type: application/json');


    try {
        // 필수 입력값 체크
        if(empty($_POST['w_name'])) {
            throw new Exception("이름을 입력해주세요.");
        }
        if(empty($_POST['w_show'])) {
            throw new Exception("노출여부를 선택해주세요.");
        }

        // 트랜잭션 시작
        $DB->startTransaction();

        $DB->where('idx', $_POST['nt_idx']);
        $old_data = $DB->getOne($tbl_name);


        unset($arr_query);
        $arr_query = array(
            "w_name" => clean_xss_tags($_POST['w_name']),
            "w_name_en" => clean_xss_tags($_POST['w_name_en']),
            "w_show" => $_POST['w_show'],
        );

        $DB->where('idx', $_POST['nt_idx']);
        if(!$DB->update($tbl_name, $arr_query)) {
            throw new Exception("데이터 수정에 실패했습니다.");
        }

        $DB->commit();

        // 성공 응답
        echo json_encode([
            'success' => true,
            'message' => '수정되었습니다.',
            'redirect' => './list.php'
        ]);

    } catch (Exception $e) {
        $DB->rollback();
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

}
else if($_POST['act'] == "delete") {

    header('Content-Type: application/json');
    try {
        // 트랜잭션 시작
        $DB->startTransaction();

        $DB->where('idx', $_POST['idx']);
        $row = $DB->getOne($tbl_name);

        if(!$row) {
            throw new Exception("삭제할 데이터가 존재하지 않습니다.");
        }

        $DB->where('idx', $_POST['idx']);
        $DB->delete($tbl_name);

        $DB->commit();

        // 성공 응답
        echo json_encode([
            'success' => true,
            'message' => '삭제되었습니다.',
            'redirect' => './list.php'
        ]);

    } catch (Exception $e) {
        $DB->rollback();
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

}
else if($_POST['act'] == "list") {
    unset($list);
    $DB->pageLimit = $_POST['obj_limit_num'];
    $pg = $_POST['obj_pg'];
    //검색
    if ($_POST['obj_search_txt']) {
        if ($_POST['obj_sel_search'] == "all") {
            $DB->where('( instr(a1.w_name, \''.$_POST['obj_search_txt'].'\') or instr(a1.w_name_en, \''.$_POST['obj_search_txt'].'\') )');
        } else if ($_POST['obj_sel_search'] == "title") {
            $DB->where('( instr(a1.w_name, \''.$_POST['obj_search_txt'].'\'))');
        } else {
            $DB->where('( instr('.$_POST['obj_sel_search'].', \''.$_POST['obj_search_txt'].'\') )');
        }
    }

    if ($_POST['w_show']) {
        $DB->where('a1.w_show', $_POST['w_show']);
    }

    //정렬
    if ($_POST['obj_order_desc_asc'] == '1') {
        $DB->orderBy("a1.w_order", "asc");
    } else {
        $DB->orderBy("a1.w_order", "asc");
    }


    $DB->setTrace(true); // 쿼리 추적 활성화
    $list = $DB->arraybuilder()->paginate($tbl_name." a1", $pg, '*, idx as nt_idx');
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
<!--                <th class="text-center" style="width:50px;">-->
<!--                    <input type="checkbox" id="selectAll" class="custom-checkbox-list" />-->
<!--                </th>-->
                <th class="text-center" style="width:80px;">
                    번호
                </th>
                <th class="text-center" style="width:120px;">
                    관리
                </th>

                <th class="text-center">
                    신고 사유
                </th>
                <th class="text-center">
                    노출여부
                </th>
                <th class="text-center">
                    생성일시
                </th>
                <th class="text-center">
                    수정일시
                </th>

            </tr>
            </thead>
            <tbody>
            <?php
            if ($list) {
                foreach ($list as $row) {
                    ?>
                    <tr draggable="true" data-id="<?=$row['idx']?>">
<!--                        <td class="text-center checkbox-wrapper">-->
<!--                            <input type="checkbox" class="rowCheckbox custom-checkbox-list" />-->
<!--                        </td>-->
                        <td data-title="번호" class="text-center">
                            <?=$counts?>
                        </td>
                        <td data-title="관리" class="text-center">
                            <input type="button" class="btn btn-outline-info btn-sm" value="수정" onclick="location.href='./form.php?act=update&nt_idx=<?=$row['nt_idx']?>'" />
                            <input type="button" class="btn btn-outline-danger btn-sm" value="삭제" onclick="f_post_del('./update.php', '<?=$row['nt_idx']?>');" />
                        </td>

                        <td class="text-center">
                            <span class="line1_text"><?=$row['w_name']?></span>
                        </td>
                        <td class="text-center">
                            <label class="switch switch-sm"><input type="checkbox" name="w_show" <?=$row['w_show']=="Y" ? "checked" : ""?> value="<?=$row['w_show']?>"><span></span></label>
                        </td>

                        <td  class="text-center">
                            <span class="line1_text"><?=DateType($row['created_at'], 4)?></span>
                        </td>
                        <td  class="text-center">
                            <span class="line1_text"><?=DateType($row['updated_at'], 4)?></span>
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


}
else if($_POST['act'] == "updateSequence") {
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
                    'w_order' => $sequence
                );

                $DB->where('idx', $idx);
                $DB->update($tbl_name, $arr_query);

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

}
else if($_POST['act'] == "updateShow") {

    $ct_idx = (int)$_POST['id'];
    $ct_show = $_POST['w_show'];

    $DB->where('idx', $_POST['id']);
    $arr_query = array(
        'w_show' => $ct_show
    );
    $result = $DB->update($tbl_name, $arr_query);

    // 결과 반환
    echo json_encode([
        'success' => $result,
        'message' => $result ? '성공적으로 변경되었습니다.' : '처리 중 오류가 발생했습니다.'
    ]);
    exit;

}




include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
