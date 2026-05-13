<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";

if ($_POST['act'] == "input") {
  header('Content-Type: application/json');

  try {
    // 필수 입력값 체크
    if(empty($_POST['nt_title'])) {
      throw new Exception("제목을 입력해주세요.");
    }
    if(empty($_POST['nt_content'])) {
      throw new Exception("내용을 입력해주세요.");
    }
    if(empty($_POST['nt_show'])) {
      throw new Exception("노출여부를 선택해주세요.");
    }

    // 트랜잭션 시작
    $DB->startTransaction();

    // tp_order 최대값 조회
    $max_order = $DB->getValue($CFG_TBL['notice']['default'], "COALESCE(MAX(nt_order), 0) + 1");

    unset($arr_query);
    $arr_query = array(
      "mt_idx" => $_SESSION['mng']['mt_idx'],
      "mt_id" => $_SESSION['mng']['mt_id'],
      "mt_name" => $_SESSION['mng']['mt_name'],
      "nt_title" => clean_xss_tags($_POST['nt_title']),
      "nt_content" => clean_xss_tags($_POST['nt_content']),
      "nt_show" => $_POST['nt_show'],
      'nt_order' => $max_order,
      "nt_wdate" => $DB->now(),
    );

    $_last_idx = $DB->insert($CFG_TBL['notice']['default'], $arr_query);

//    if($_last_idx) {
//      for($i = 1; $i <= $_POST['file_count']; $i++) {
//        if($_FILES["nt_file{$i}"]['name']) {
//          handle_file_upload($_POST['board'], $_FILES["nt_file{$i}"], $i, $_last_idx, "nt_file{$i}", $ct_notice_dir);
//        }
//      }
//    }

    $DB->commit();

//    $sql = "
//        SELECT *
//        FROM member_t
//        WHERE mt_pushing2 = 'Y'
//          AND mt_status = 'Y'
//          AND mt_level = 2
//      ";
//    $members = $DB->rawQuery($sql);
//    foreach ($members as $m) {
//      $tpl_sql = "SELECT * FROM fcm_template_t WHERE type = 'notice' AND activity = 'input' LIMIT 1";
//      $tpl = $DB->rawQueryOne($tpl_sql);
//
//      $target_link = isset($tpl['target_link']) ? $tpl['target_link'] : APP_DOMAIN;
//      $replacements = [
//        '{idx}' => $_last_idx,
//      ];
//      $target_link   = strtr($target_link, $replacements);
//      $param = [
//        'app_token'   => $m['mt_app_token'],
//        'template'    => $tpl['idx'],
//        'mt_idx'      => $m['idx'],
//        'title'       => clean_xss_tags($_POST['nt_title']),
//        'target_link' => $target_link
//      ];
//      $result = notifyByTemplate($param);
//
//      $web_target_link = isset($tpl['web_target_link']) ? $tpl['web_target_link'] : APP_DOMAIN;
//      $web_target_link = strtr($web_target_link, $replacements);
//
//      $sql = "SELECT * FROM member_fcm_token_t WHERE mt_idx = ? AND platform = 'web' ORDER BY idx ASC";
//      $list = $DB->rawQuery($sql, [$m['idx']]);
//      if (count($list) > 0) {
//        foreach ($list as $item) {
//          $secure_target_url = APP_DOMAIN."/webpush/redirect.php"
//            . "?redirect_mobile=" . urlencode($target_link)
//            . "&redirect_pc=" . urlencode($web_target_link)
//            . "&fcm_token=" . urlencode($item['fcm_token']);
//
//          $param = [
//            'web_token'   => $item['fcm_token'],
//            'template'    => $tpl['idx'],
//            'mt_idx'      => $m['idx'],
//            'title'       => $_POST['nt_title'],
//            'web_target_link' => $secure_target_url,
//          ];
//          $result = notifyByTemplate($param);
//        }
//      }
//    }

    // 성공 응답
    echo json_encode([
      'success' => true,
      'message' => '저장되었습니다.',
      'mt_idx' => $_last_idx,
      'uploaded_files' => '',
      'redirect' => './list.php',
    ]);

  } catch (Exception $e) {
    $DB->rollback();
    echo json_encode([
      'success' => false,
      'message' => $e->getMessage()
    ]);
  }
} elseif ($_POST['act'] == "update") {
  header('Content-Type: application/json');


  try {
    // 필수 입력값 체크
    if(empty($_POST['nt_title'])) {
      throw new Exception("제목을 입력해주세요.");
    }
    if(empty($_POST['nt_content'])) {
      throw new Exception("내용을 입력해주세요.");
    }
    if(empty($_POST['nt_show'])) {
      throw new Exception("노출여부를 선택해주세요.");
    }

    // 트랜잭션 시작
    $DB->startTransaction();

    // 공지사항 정보 업데이트
    unset($arr_query);
    $arr_query = array(
      "mt_idx" => $_SESSION['mng']['mt_idx'],
      "mt_id" => $_SESSION['mng']['mt_id'],
      "mt_name" => $_SESSION['mng']['mt_name'],
      "nt_title" => clean_xss_tags($_POST['nt_title']),
      "nt_content" => clean_xss_tags($_POST['nt_content']),
      "nt_show" => $_POST['nt_show'],
      "nt_order" => $_POST['nt_order'],
      "nt_udate" => $DB->now(),
    );

    $DB->where('idx', $_POST['nt_idx']);
    $DB->update($CFG_TBL['notice']['default'], $arr_query);
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
    $DB->where('idx', $_POST['idx']);
    $row = $DB->getOne($CFG_TBL['notice']['default']);

    if(!$row) {
      throw new Exception("삭제할 데이터가 존재하지 않습니다.");
    }

    // 첨부파일 삭제
//    $DB->where('board', 'notice');
//    $DB->where('bo_id', $_POST['idx']);
//    $files = $DB->get($CFG_TBL['board']['file']);
//
//    if($files) {
//      foreach($files as $file) {
//        @unlink($ct_notice_dir.$file['bf_file']);
//      }
//      $DB->where('board', $_POST['board']);
//      $DB->where('bo_id', $_POST['idx']);
//      $DB->delete($CFG_TBL['board']['file']);
//    }

    // 에디터 썸네일 삭제
//    delete_editor_thumbnail($row['nt_content']);

    // 에디터 이미지 삭제
//    delete_editor_image($row['nt_content']);

      unset($arr_query);
      $arr_query = array(
          "del_date" => $DB->now(),
      );

    // 공지사항 삭제
    $DB->where('idx', $_POST['idx']);
    $DB->update($CFG_TBL['notice']['default'], $arr_query);

    // 순서 재정렬
    $DB->where('nt_order', $row['nt_order'], '>');
    $arr_query = array(
      'nt_order' => $DB->dec(1)
    );
    $DB->update($CFG_TBL['notice']['default'], $arr_query);

    $DB->commit();

    // 성공 응답
    echo json_encode([
      'success' => true,
      'message' => '삭제되었습니다.'
    ]);

  } catch (Exception $e) {
    $DB->rollback();
    echo json_encode([
      'success' => false,
      'message' => $e->getMessage()
    ]);
  }

} elseif ($_POST['act'] == "list") {
  unset($list);
  $DB->pageLimit = $_POST['obj_limit_num'];
  $pg = $_POST['obj_pg'];

  //검색
  if ($_POST['obj_search_txt']) {
    if ($_POST['obj_sel_search'] == "all") {
      $DB->where('( instr(a1.nt_title, \''.$_POST['obj_search_txt'].'\') or instr(a1.nt_content, \''.$_POST['obj_search_txt'].'\') )');
    } else {
      $DB->where('( instr('.$_POST['obj_sel_search'].', \''.$_POST['obj_search_txt'].'\') )');
    }
  }

  if ($_POST['nt_show']) {
    $DB->where('a1.nt_show', $_POST['nt_show']);
  }

  $DB->where('a1.del_date', null, 'IS');

  //정렬
  if ($_POST['obj_order_desc_asc'] == '1') {
    $DB->orderBy("a1.nt_order", "desc");
  } else {
    $DB->orderBy("a1.nt_order", "asc");
  }

  $DB->setTrace(true); // 쿼리 추적 활성화
  $list = $DB->arraybuilder()->paginate($CFG_TBL['notice']['default']." a1", $pg, '*, idx as nt_idx');
  $debug = $DB->trace; // 모든 쿼리 정보 배열로 반환
  //print_r($debug);

  //페이징
  $n_page = $DB->totalPages;
  $counts = $DB->totalCount;
  $counts = $counts - (($pg - 1) * $_POST['obj_limit_num']);
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
        <th class="text-center" style="width: 50%">
          제목
        </th>
        <th class="text-center">
          노출
        </th>
        <th class="text-center">
          노출 순서
        </th>
          <th class="text-center">
              작성자
          </th>
        <th class="text-center" style="width:130px;">
          등록일시
        </th>
          <th class="text-center" style="width:130px;">
              관리
          </th>
      </tr>
      </thead>
      <tbody>
      <?php
      if ($list) {
        foreach ($list as $row) {
          ?>
          <tr draggable="true" data-id="<?=$row['idx']?>">
<!--            <td class="text-center checkbox-wrapper">-->
<!--              <input type="checkbox" class="rowCheckbox custom-checkbox-list" />-->
<!--            </td>-->
            <td data-title="번호" class="text-center">
              <?=$counts?>
            </td>
              <td data-title="제목">
                  <span class="line1_text"><?=$row['nt_title']?></span>
              </td>
              <td data-title="노출" class="text-center">
                  <label class="switch switch-sm"><input type="checkbox" name="nt_show" <?=$row['nt_show']=="Y" ? "checked" : ""?> value="<?=$row['nt_show']?>"><span></span></label>
<!--                  <span class="line1_text">--><?php //=$row['nt_show'] === 'Y' ? '노출' : '노출안함'?><!--</span>-->
              </td>
            <td data-title="노출 순서" class="text-center">
              <span class="line1_text"><?=$row['nt_order']?></span>
            </td>
              <td data-title="작성자" class="text-center">
                  <span class="line1_text"><?=$row['mt_name']?></span>
              </td>
            <td data-title="등록일시" class="text-center">
              <?=DateType($row['nt_wdate'], 4)?>
            </td>
              <td data-title="관리" class="text-center">
                  <input type="button" class="btn btn-outline-info" value="수정" onclick="location.href='./form.php?act=update&nt_idx=<?=$row['nt_idx']?>'" />
                  <input type="button" class="btn btn-outline-danger" value="삭제" onclick="f_post_del('./update.php', '<?=$row['nt_idx']?>');" />
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


} else if($_POST['act'] == "updateShow") {

    $DB->where('idx', $_POST['id']);
    $arr_query = array(
        "nt_show" => $_POST['rt_show'],
    );
    $result = $DB->update('notice_t', $arr_query);

    // 결과 반환
    echo json_encode([
        'success' => $result,
        'message' => $result ? '성공적으로 변경되었습니다.' : '처리 중 오류가 발생했습니다.'
    ]);
    exit;
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
