<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";
if ($_POST['act'] == "update") {
  header('Content-Type: application/json');

  try {
    if(empty($_POST['w_show'])) {
      throw new Exception("노출여부를 선택해주세요.");
    }

    // 트랜잭션 시작
    $DB->startTransaction();

    // 공지사항 정보 업데이트
    unset($arr_query);
    $arr_query = array(
      "w_show" => $_POST['nt_show'],
      "upd_datetime" => $DB->now(),
    );

    $DB->where('id', $_POST['nt_idx']);
    $DB->update($CFG_TBL['vehicle']['default'], $arr_query);
    $_last_idx = $_POST['nt_idx'];

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

} elseif ($_POST['act'] == "delete") {

  header('Content-Type: application/json');
    try {
        // 트랜잭션 시작
        $DB->startTransaction();

        // 공지사항 정보 조회
        $DB->where('id', $_POST['idx']);
        $row = $DB->getOne($CFG_TBL['vehicle']['default']);

        if(!$row) {
            throw new Exception("삭제할 데이터가 존재하지 않습니다.");
        }

        unset($arr_query);
        $arr_query = array(
            "del_date" => $DB->now(),
        );

        // 공지사항 삭제
        $DB->where('id', $_POST['idx']);
        $DB->update($CFG_TBL['vehicle']['default'], $arr_query);

        $DB->commit();

        // 성공 응답
        echo json_encode([
            'success' => true,
            'message' => '삭제되었습니다.',
        ]);

    } catch (Exception $e) {
        $DB->rollback();
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

}
else if($_POST['act'] == "restoration"){
    $DB->setTrace(true);
    $DB->where('id', $_POST['idx_t']);
    $row_mt = $DB->getone('vehicle_t', "*, id as mt_idx");

    unset($arr_query);
    $arr_query = array(
        "del_date" => null,
    );

    if ($arr_query) {
        $DB->where('id', $_POST['idx_t']);
        $DB->update('vehicle_t', $arr_query);
    }

    echo "Y";
}
else if($_POST['act'] == "updateShow"){

    $DB->where('id', $_POST['id']);
    $arr_query = array(
        "w_show" => $_POST['w_show'],
    );
    $result = $DB->update('vehicle_t', $arr_query);

    // 결과 반환
    echo json_encode([
        'success' => $result,
        'data' => $_POST['w_show'],
        'message' => $result ? '성공적으로 변경되었습니다.' : '처리 중 오류가 발생했습니다.'
    ]);
    exit;
}
elseif ($_POST['act'] == "list") {
  unset($list);
  $DB->pageLimit = $_POST['obj_limit_num'];
  $pg = $_POST['obj_pg'];

    $DB->join($CFG_TBL['member']['default']." a2", "a1.mt_idx = a2.idx", "LEFT");
  //검색
  if ($_POST['obj_search_txt']) {
    if ($_POST['obj_sel_search'] == "all") {
      $DB->where('( instr(a1.title, \''.$_POST['obj_search_txt'].'\') or instr(a2.mt_id, \''.$_POST['obj_search_txt'].'\') or instr(a2.mt_name, \''.$_POST['obj_search_txt'].'\'))');
    }
    else if($_POST['obj_sel_search'] == "mt_id"){
        $DB->where('( instr(a2.mt_id, \''.$_POST['obj_search_txt'].'\'))');
    }
    else if($_POST['obj_sel_search'] == "mt_name"){
        $DB->where('( instr(a2.mt_name, \''.$_POST['obj_search_txt'].'\'))');
    }
    else if($_POST['obj_sel_search'] == "title"){
        $DB->where('( instr(a1.title, \''.$_POST['obj_search_txt'].'\'))');
    }
    else {
      $DB->where('( instr('.$_POST['obj_sel_search'].', \''.$_POST['obj_search_txt'].'\') )');
    }
  }

  if($_POST['obj_sel_category'] != ''){
      $DB->where('a1.type', $_POST['obj_sel_category']);
  }

  if ($_POST['obj_search_day']) {
      $today = date('Y-m-d'); // 오늘 날짜 (시간 제외)

      if ($_POST['obj_search_day'] == '1') {
          // 오늘 00:00:00부터
          $DB->where('reg_datetime', $today . ' 00:00:00', '>=');

      } elseif ($_POST['obj_search_day'] == '2') {
          // 최근 7일
          $start_date = date('Y-m-d', strtotime('-7 days'));
          $DB->where('reg_datetime', $start_date . ' 00:00:00', '>=');

      } elseif ($_POST['obj_search_day'] == '3') {
          // 최근 30일
          $start_date = date('Y-m-d', strtotime('-30 days'));
          $DB->where('reg_datetime', $start_date . ' 00:00:00', '>=');
      }
  }

  if ($_POST['sdate'] && $_POST['edate']) {
      $start = $_POST['sdate'] . ' 00:00:00';
      $end = $_POST['edate'] . ' 23:59:59';

      $DB->where('reg_datetime', [$start, $end], 'BETWEEN');
  }
  $DB->where('a1.del_date', null, 'IS');

  $DB->orderBy("a1.idx", "desc");
  $DB->setTrace(true); // 쿼리 추적 활성화
  $list = $DB->arraybuilder()->paginate($CFG_TBL['vehicle']['default']." a1", $pg, '*, a1.idx as nt_idx');
  $debug = $DB->trace; // 모든 쿼리 정보 배열로 반환
  //print_r($debug);

  //페이징
  $n_page = $DB->totalPages;
  $counts = $DB->totalCount;
  $counts = $counts - (($pg - 1) * $_POST['obj_limit_num']);

  if($_POST['type'] === 'history'){
      $type = '&type='.$_POST['type'];
  }
  ?>
  <div class="table-responsive margin-top-20">
    <input type="hidden" value="<?php echo $counts; ?>" id="total" /> <!--  토탈 카운트 추가  -->
    <table class="table table-striped table-bordered margin-bottom-20" id="listTable" style="min-width: 800px">
      <thead class="thead-dark">
      <tr>
<!--        <th class="text-center" style="width:50px;">-->
<!--          <input type="checkbox" id="selectAll" class="custom-checkbox-list" />-->
<!--        </th>-->
        <th class="text-center" style="width:80px;">
          번호
        </th>
        <th class="text-center" style="width:120px;">
          관리
        </th>
          <th class="text-center">
              아이디
          </th>
          <th class="text-center">
              이름
          </th>
          <th class="text-center">
              구분
          </th>
        <th class="text-center">
          제목
        </th>
          <th class="text-center">
              노출여부
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
          ?>
          <tr draggable="true" data-id="<?=$row['id']?>">
<!--            <td class="text-center checkbox-wrapper">-->
<!--              <input type="checkbox" class="rowCheckbox custom-checkbox-list" />-->
<!--            </td>-->
            <td data-title="번호" class="text-center">
              <?=$counts?>
            </td>
            <td data-title="관리" class="text-center">
              <input type="button" class="btn btn-outline-info btn-sm" value="상세" onclick="location.href='./form.php?act=update&nt_idx=<?=$row['nt_idx']?>'" />
                <?php if($_POST['type'] !== 'history'){?>
              <input type="button" class="btn btn-outline-danger btn-sm" value="삭제" onclick="f_post_del('./update.php', '<?=$row['nt_idx']?>');" />
                <?php }else{?>
              <input type="button" class="btn btn-outline-danger btn-sm" value="복구" onclick="f_restoration_board('<?=$row['nt_idx']?>');" />
                <?php }?>
            </td>
              <td data-title="아이디">
                  <span class="line1_text"><?=$row['mt_id']?></span>
              </td>
              <td data-title="이름">
                  <span class="line1_text"><?=$row['mt_name']?></span>
              </td>
              <td data-title="구분">
                  <span class="line1_text"><?=$row['dealer_type']?></span>
              </td>
            <td data-title="제목">
              <span class="line1_text"><?=$row['title']?></span>
            </td>
              <td data-title="제목">
                  <label class="switch switch-sm"><input type="checkbox" name="w_show" <?=$row['w_show']=="Y" ? "checked" : ""?> value="<?=$row['w_show']?>"><span></span></label>
              </td>
            <td data-title="등록일시" class="text-center">
              <?=DateType($row['reg_datetime'], 4)?>
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
        $DB->update($CFG_TBL['notice']['default'], $arr_query);

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

include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
