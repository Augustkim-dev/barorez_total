<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";

function columnExists($table, $column) {
    global $DB;
    $DB->where('TABLE_NAME', $table);
    $DB->where('COLUMN_NAME', $column);
    $DB->where('TABLE_SCHEMA', 'utopiakorea');
    $result = $DB->get('INFORMATION_SCHEMA.COLUMNS');

    // 디버그 로그
    if (count($result) === 0) {
        error_log("컬럼 없음: {$table}.{$column}");
    }
    return count($result) > 0;
}

$tbl_name = "singo_t";

if($_POST['act'] == "update") {
  header('Content-Type: application/json');

    try {

        // 트랜잭션 시작
        $DB->startTransaction();

        $DB->where('idx', $_POST['nt_idx']);
        $old_data = $DB->getOne($tbl_name);


        unset($arr_query);
        $arr_query = array(
          "sg_status" => $_POST['sg_status'],
          "sg_udate" => $DB->now(),
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

        unset($arr_query);
        $arr_query = array(
            "del_date" => $DB->now(),
        );

        $DB->where('idx', $_POST['idx']);
        $DB->update($tbl_name, $arr_query);

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
    $DB->setTrace(true); // 쿼리 추적 활성화
// 기본 조인
    $DB->join("member_t a2", "a1.mt_idx = a2.idx", "LEFT");
    $DB->join("stype_t s1", "a1.sg_id = s1.idx", "LEFT");


    //검색
    if ($_POST['obj_search_txt']) {
        if ($_POST['obj_sel_search'] == "all") {
            $DB->where('( instr(a2.mt_name, \''.$_POST['obj_search_txt'].'\') or instr(a2.mt_id, \''.$_POST['obj_search_txt'].'\') )');
        } else {
            $DB->where('( instr('.$_POST['obj_sel_search'].', \''.$_POST['obj_search_txt'].'\') )');
        }
    }

    if($_POST['obj_search_status'] != ''){
        $DB->where('sg_status', $_POST['obj_search_status']);
    }

    if ($_POST['obj_search_day']) {
        $today = date('Y-m-d'); // 오늘 날짜 (시간 제외)

        if ($_POST['obj_search_day'] == '1') {
            // 오늘 00:00:00부터
            $DB->where('a1.sg_date', $today . ' 00:00:00', '>=');

        } elseif ($_POST['obj_search_day'] == '2') {
            // 최근 7일
            $start_date = date('Y-m-d', strtotime('-7 days'));
            $DB->where('a1.sg_date', $start_date . ' 00:00:00', '>=');

        } elseif ($_POST['obj_search_day'] == '3') {
            // 최근 30일
            $start_date = date('Y-m-d', strtotime('-30 days'));
            $DB->where('a1.sg_date', $start_date . ' 00:00:00', '>=');
        }
    }

    if ($_POST['sdate'] && $_POST['edate']) {
        $start = $_POST['sdate'] . ' 00:00:00';
        $end = $_POST['edate'] . ' 23:59:59';

        $DB->where('a1.sg_date', [$start, $end], 'BETWEEN');
    }

    $DB->where('a1.del_date', null, 'IS');

    $list = $DB->arraybuilder()->paginate($tbl_name." a1", $pg, '*, a1.idx as nt_idx');

    $debug = $DB->trace; // 모든 쿼리 정보 배열로 반환

    //페이징
    $n_page = $DB->totalPages;
    $counts = $DB->totalCount;
    $counts = $counts - (($pg - 1) * $_POST['obj_limit_num']);
    ?>
    <div class="table-responsive margin-top-20">
        <table class="table table-striped table-bordered margin-bottom-20" id="listTable" style="min-width: 800px">
            <thead class="thead-dark">
                <tr>
<!--                    <th class="text-center" style="width:50px;">-->
<!--                        <input type="checkbox" id="selectAll" class="custom-checkbox-list" />-->
<!--                    </th>-->
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
                        구분
                    </th>
                    <th class="text-center">
                        제목/댓글
                    </th>
                    <th class="text-center">
                        신고요청 회원
                    </th>
                    <th class="text-center">
                        처리 상태
                    </th>
                    <th class="text-center">
                        신고일/처리일
                    </th>

                </tr>
            </thead>
            <tbody>
            <?php
            if ($list) {
                foreach ($list as $row) {
                    if($row['bo_id']){
                        $DB->where('idx', $row['bo_id']);
                        $row_title = $DB->getone("board_t b1", "*, idx as bo_idx");
                        $row_title = $row_title['nt_title'];
                    }else{
                        $DB->where('idx', $row['co_id']);
                        $row_title = $DB->getone("comment_t c1", "*, idx as co_idx");
                        $row_title = $row_title['cmt_content'];
                    }
                    ?>
                <tr draggable="true" data-id="<?=$row['idx']?>">
<!--                    <td class="text-center checkbox-wrapper">-->
<!--                        <input type="checkbox" class="rowCheckbox custom-checkbox-list" />-->
<!--                    </td>-->
                    <td data-title="번호" class="text-center">
                        <?=$counts?>
                    </td>
                    <td data-title="관리" class="text-center">
                        <input type="button" class="btn btn-outline-info btn-sm" value="상세" onclick="location.href='./form.php?act=update&nt_idx=<?=$row['nt_idx']?>'" />
                        <input type="button" class="btn btn-outline-danger btn-sm" value="삭제" onclick="f_post_del('./update.php', '<?=$row['nt_idx']?>');" />
                    </td>
                    <td class="text-center">
                        <span class="line1_text"><?=$row['w_name']?></span>
                    </td>
                    <td class="text-center">
                        <span class="line1_text"><?= ($row['bo_id']) ? '게시글' : (($row['co_id']) ? '댓글' : '') ?></span>
                    </td>
                    <td class="text-center">
                        <span class="line1_text"><?=$row_title ?></span>
                    </td>
                    <td class="text-center">
                        <span class="line1_text"><?=$row['mt_id']?></span><br>
                        <span class="line1_text"><?=$row['mt_name']?></span>
                    </td>
                    <td class="text-center">
                        <span class="line1_text"><?=$arr_singo_rt_status[$row['sg_status']]?></span>
                    </td>
                    <td  class="text-center">
                      <span class="line1_text">신고일시: <?=DateType($row['sg_date'], 4)?></span>
                        <?php if($row['sg_udate']) {?>
                      <span class="line1_text">처리일시: <?=DateType($row['sg_udate'], 4)?></span>
                        <?php } ?>
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
