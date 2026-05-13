<?
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";

$tbl_name = "member_grade_t";


if ($_POST['act'] == "input") {
  header('Content-Type: application/json');

  try {
    // 필수 입력값 체크
    if(empty($_POST['w_name'])) {
      throw new Exception("이름을 입력해주세요.");
    }
    if(empty($_POST['w_code'])) {
      throw new Exception("코드를 입력해주세요.");
    }
    if(empty($_POST['w_scan'])) {
      throw new Exception("스캔횟수 입력해주세요.");
    }

    // 트랜잭션 시작
    $DB->startTransaction();


    unset($arr_query);
    $arr_query = array(
      "w_name" => clean_xss_tags($_POST['w_name']),
      "w_code" => clean_xss_tags($_POST['w_code']),
      "w_scan" => $_POST['w_scan'],
      "w_upgrade_condition" => $_POST['w_upgrade_condition'],
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
      'redirect' => './member_grade.php',
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
    if(empty($_POST['w_code'])) {
      throw new Exception("코드를 입력해주세요.");
    }
    if(empty($_POST['w_scan'])) {
      throw new Exception("스캔횟수 입력해주세요.");
    }


    // 트랜잭션 시작
    $DB->startTransaction();

    $DB->where('idx', $_POST['nt_idx']);
    $old_data = $DB->getOne($tbl_name);


    unset($arr_query);
    $arr_query = array(
      "w_name" => clean_xss_tags($_POST['w_name']),
      "w_code" => clean_xss_tags($_POST['w_code']),
      "w_scan" => $_POST['w_scan'],
      "w_upgrade_condition" => $_POST['w_upgrade_condition'],
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
      'redirect' => './member_grade.php'
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
      'redirect' => './member_grade.php'
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
  $rows = $_POST['rows'] ? $_POST['rows'] : $n_limit_num;

  $_colspan_txt = 5;

  unset($list);
  $DB->pageLimit = $rows;
  $pg = $_POST['obj_pg'];

  //검색
  if ($_POST['search_txt']) {
    if ($_POST['sel_search'] == "all") {
      $_instr_where = 'instr(a1.w_code, \''.$_POST['search_txt'].'\') or ';

      $_instr_where .= 'instr(a1.w_name, \''.$_POST['search_txt'].'\')';
      $DB->where('( '.$_instr_where.' )');
    } else {
      $DB->where('( instr('.$_POST['sel_search'].', \''.$_POST['search_txt'].'\') )');
    }
  }


  //정렬
  if ($_POST['obj_order_desc_asc'] == '1') {
    $DB->orderBy("a1.idx", "desc");
  } else {
    $DB->orderBy("a1.idx", "asc");
  }

  $list = $DB->arraybuilder()->paginate("member_grade_t a1", $pg, '*, idx as nt_idx');
  $query = $DB->getLastQuery();

  //페이징
  $n_page = $DB->totalPages;
  $totalCount = $DB->totalCount;
  $counts = $totalCount - (($pg - 1) * $rows);
  ?>
  <div class="table-responsive margin-top-20">
    <table class="table table-striped table-bordered margin-bottom-20" id="listTable" style="min-width: 800px">
      <thead class="thead-dark">
      <tr>
        <th class="text-center" style="width:80px;">번호</th>
        <th class="text-center" style="width:120px;">
          관리
        </th>
        <th class="text-center" style="width:150px;">코드</th>
        <th class="text-center" style="width:150px;">이름</th>
        <th class="text-center" style="width: 120px;">1일 스캔횟수</th>
        <th class="text-center" style="width: ;">승급 조건</th>
      </tr>
      </thead>
      <tbody>
      <?php
      if ($list) {
        foreach ($list as $row) {
          //$DB->where('idx', $row['mt_idx']);
          //$row_st = $DB->getone("store_t a1", "*, a1.idx as st_idx");
          ?>
          <tr data-id="<?=$row['mt_idx']?>">
            <td data-title="번호" class="text-center">
              <?=$counts?>
            </td>
            <td data-title="관리" class="text-center">
              <input type="button" class="btn btn-outline-info btn-sm" value="수정" onclick="location.href='./member_grade_form.php?act=update&nt_idx=<?=$row['nt_idx']?>'" />
              <input type="button" class="btn btn-outline-danger btn-sm" value="삭제" onclick="f_post_del('./member_grade_update.php', '<?=$row['nt_idx']?>');" />
            </td>
            <td data-title="구분" class="text-center">
              <?=$row['w_code']?>
            </td>
            <td data-title="구분" class="text-center">
              <?=$row['w_name']?>
            </td>

            <td data-title="구분" class="text-center">
              <?=($row['w_scan']<0)?'무제한':$row['w_scan'].'회'?>
            </td>
            <td data-title="구분" class="text-center">
              <?=$row['w_upgrade_condition']?>
            </td>
          </tr>
          <?php
          $counts--;
        }
      } else {
        ?>
        <tr>
          <td colspan="<?=$_colspan_txt?>" class="text-center"><b>자료가 없습니다.</b></td>
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
  ?>
  <script>
      $(document).find('[data-id="listCount"][data-fid="<?=$_POST['obj_frm']?>"]').text('<?=$totalCount?>');
      $(document).find('#fexcel input[name="excel_query"]').val("<?=$query?>");
  </script>
  <?

}





include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";