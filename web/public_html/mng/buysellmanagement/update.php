<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";


if ($_POST['act'] == "buy_input" || $_POST['act'] == "buy_update") {
    header('Content-Type: application/json');

    try {
        // 필수 입력값 체크
      if (empty($_POST['gmt_golf_name'])) {
        throw new Exception("골프장명을 입력해주세요.");
      }
      if (empty($_POST['gmtt_type'])) {
        throw new Exception("구분을 선택헤주세요.");
      }
      if (empty($_POST['mt_name'])) {
        throw new Exception("등록자를 입력해주세요.");
      }
      if (empty($_POST['mt_hp'])) {
        throw new Exception("등록자 전화번호를 입력해주세요.");
      }
      if (empty($_POST['gmtt_hope_price'])) {
        throw new Exception("구매 희망금액을 입력해주세요.");
      }


        // 트랜잭션 시작
        $DB->startTransaction();

        if($_POST['act'] == "buy_update"){
          $DB->where('gmtt_idx', $_POST['nt_idx']);
          $old_data = $DB->getOne($CFG_TBL['golf_membership']['transaction']);
          if(!$old_data) {
            throw new Exception("데이터가 없습니다.");
          }
        }

        $DB->where('gmt_golf_name', $_POST['gmt_golf_name']);
        $saleInfo = $DB->getone($CFG_TBL['golf_membership']['main'], '*');
        if(!$saleInfo) {
          throw new Exception("해당 회원권을 찾을 수 없습니다.");
        }



        unset($arr_query);
        $arr_query = array(
          "gmt_idx"           => clean_xss_tags($saleInfo['gmt_idx']),
          "gmt_golf_name"     => clean_xss_tags($_POST['gmt_golf_name']),
          "mt_name"           => clean_xss_tags($_POST['mt_name']),
          "mt_hp"             => clean_xss_tags($_POST['mt_hp']),
          "gmtt_level"        => 2,
          "gmtt_type"         => clean_xss_tags($_POST['gmtt_type']),
          "gmtt_status"       => 1,
          "gmtt_hope_price"   => clean_xss_tags($_POST['gmtt_hope_price']),
          "gmtt_conclusion_txt"  => clean_xss_tags($_POST['gmtt_conclusion_txt']),
        );


        if($_POST['act'] == "buy_update"){
          $DB->where('gmtt_idx', $old_data['gmtt_idx']);
          if(!$DB->update($CFG_TBL['golf_membership']['transaction'], $arr_query)) {
            throw new Exception("데이터 변경에 실패했습니다.");
          }

          $_last_idx = $old_data['gmt_idx'];
        } else {
          $_last_idx = $DB->insert($CFG_TBL['golf_membership']['transaction'], $arr_query);
          if(!$_last_idx) {
            throw new Exception("데이터 저장에 실패했습니다.");
          }
        }


      $DB->commit();

      // 성공 응답
      echo json_encode([
          'success' => true,
          'message' => '저장 되었습니다.',
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
else if($_POST['act'] == "buy_delete") {

    header('Content-Type: application/json');
    try {
        // 트랜잭션 시작
        $DB->startTransaction();

        $DB->where('gmtt_idx', $_POST['idx']);
        $row = $DB->getOne($CFG_TBL['golf_membership']['transaction']);

        if(!$row) {
            throw new Exception("삭제할 데이터가 존재하지 않습니다.");
        }

        $DB->where('gmtt_idx', $_POST['idx']);

        $arr_query = ['gmtt_del' =>'Y'];
        $DB->update($CFG_TBL['golf_membership']['transaction'], $arr_query);

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
else if($_POST['act'] == "buy_list") {
  unset($list);
  $DB->pageLimit = $_POST['list_v_limit_num'];
  $pg = $_POST['list_v_pg'];
  //검색

  $search_txt = trim($_POST['list_v_search_txt'] ?? '');
  $search_col = $_POST['list_v_sel_search'] ?? '';

  if ($search_txt !== '') {
    if ($search_col == "all") {
      $DB->where("( INSTR(a2.gmt_golf_name, ?) )", [$search_txt]);
    } else {
      $DB->where("( INSTR({$search_col}, ?) )", [$search_txt]);
    }
  }

  $obj_search_status = $_POST['list_v_search_status'] ?? 1;
  $DB->where('a1.gmtt_status', $obj_search_status);

  $DB->where('a1.gmtt_del', 'N');
  $DB->where('a1.gmtt_show', 'Y');
  $DB->where('a1.gmtt_level', 2);
  $DB->where('a2.gmt_del', 'N');

  //정렬
  $DB->orderBy("a1.gmtt_idx", "desc");

  $select = "
      a1.*, 
      a2.gmt_golf_name,
      a1.gmtt_idx as nt_idx
    ";

  $table = "{$CFG_TBL['golf_membership']['transaction']} a1 
          LEFT JOIN {$CFG_TBL['golf_membership']['main']} a2 
          ON a1.gmt_idx = a2.gmt_idx";

  $DB->setTrace(true); // 쿼리 추적 활성화
  $list = $DB->arraybuilder()->paginate($table, $pg,  $select);
  $debug = $DB->trace; // 모든 쿼리 정보 배열로 반환
  //print_r($debug);

  //페이징
  $n_page = $DB->totalPages;
  $total_count = $DB->totalCount;
  $counts = $total_count - (($pg - 1) * $_POST['list_v_limit_num']);


  // 상태별 컬럼 $obj_search_status
  $column_config = [
    1 => [
      '구분' => 'gmtt_type',
      '골프장명' => 'gmt_golf_name',
      '등록자' => 'mt_name',
      '등록자 전화번호' => 'mt_hp',
      '희망가' => 'gmtt_hope_price',
      '진행여부' => 'gmtt_status',
      '등록일자' => 'gmtt_wdate',
      '체결 희망자' => 'contractUser',
      '관리' => '__action__'
    ],
    2 => [
      '구분' => 'gmtt_type',
      '골프장명' => 'gmt_golf_name',
      '등록자' => 'mt_name',
      '등록자 전화번호' => 'mt_hp',
      '희망가' => 'gmtt_hope_price',
      '체결희망자' => 'contractUser',
      '체결희망자 전화번호' => 'mt_conclusion_hp',
      '진행여부' => 'gmtt_status',
      '체결 희망일자' => 'gmtt_hdate',
      '관리' => '__action__'
    ],
    3 => [
      '구분' => 'gmtt_type',
      '골프장명' => 'gmt_golf_name',
      '등록자' => 'mt_name',
      '등록자 전화번호' => 'mt_hp',
      '희망가' => 'gmtt_hope_price',
      '체결희망자' => 'contractUser',
      '체결희망자 전화번호' => 'mt_conclusion_hp',
      '진행여부' => 'gmtt_status',
      '체결 완료일자' => 'gmtt_edate',
      '관리' => '__action__'
    ]
  ];
  $list_columns = $column_config[$obj_search_status];
  $list_columns_cnt = count($list_columns);


  ?>
  <div class="d-flex justify-content-between align-items-center mt-3 mb-2">
    <h5 class="h5 mb-0">구매(<span><?= number_format($total_count) ?></span>)</h5>
  </div>
  <div class="table-responsive margin-top-5">
    <table class="table table-striped table-bordered margin-bottom-20" id="buyListTable" style="min-width: 770px; table-layout: fixed;">
      <thead class="thead-dark">
      <tr>
        <?php foreach ($list_columns as $label => $field): ?>
          <th class="text-center" scope="col"><?= $label ?></th>
        <?php endforeach; ?>
      </tr>
      </thead>
      <tbody>
      <?php
      if ($list) {
        foreach ($list as $row) {

          if($obj_search_status==1){
            $registration_url = "./purchaser.php?nt_idx={$row['nt_idx']}&state=1";
            $registration_txt = "등록";
          } else if($obj_search_status==2){
            $registration_url = "./procress.php?nt_idx={$row['nt_idx']}&state=1";
            $registration_txt = "[{$row['mt_conclusion_name']}]";
          } else if($obj_search_status==3){
            $registration_url = "./complete.php?nt_idx={$row['nt_idx']}&state=1";
            $registration_txt = "[{$row['mt_conclusion_name']}]";
          }


          $modify_url = "./form_buy.php?act=buy_update&nt_idx={$row['nt_idx']}&gmt_idx={$row['gmt_idx']}";

          ?>
          <tr draggable="false" data-id="<?=$row['nt_idx']?>" data-gmt-idx="<?=$row['gmt_idx']?>" data-gmtt-idx="<?=$row['gmtt_idx']?>">

            <?php foreach ($list_columns as $label => $field): ?>
              <td class="text-center">
                <?php if ($field === '__action__'): ?>
                  <input type="button" class="btn btn-outline-info btn-sm" value="수정" onclick="location.href='<?=$modify_url?>'" />
                  <input type="button" class="btn btn-outline-danger btn-sm" value="삭제" onclick="f_post_act_del('./update.php', '<?=$row['nt_idx']?>', 'buy_delete');" />
                <?php elseif ($field === 'contractUser' && $registration_txt == '등록'): ?>
                  <input type="button" class="btn btn-outline-info btn-sm" value="<?=$registration_txt?>" onclick="location.href='<?=$registration_url?>'" />
                <?php elseif ($field === 'contractUser' && $registration_txt != '등록'): ?>
                  <input type="button" class="btn btn-sm" value="<?=$registration_txt?>" onclick="location.href='<?=$registration_url?>'" />
                <?php elseif (str_ends_with($field, 'date')): ?>
                  <?= DateType($row[$field], 1) ?>
                <?php elseif ($field === 'gmtt_type'): ?>
                  <?= $arr_gmtt_type[$row[$field]] ?>
                <?php elseif ($field === 'gmtt_status'): ?>

                      <?= $arr_gmtt_status[$row[$field]] ?>

                <?php elseif ($field === 'gmtt_hope_price'): ?>
                  <?= is_numeric($row[$field]) ? number_format($row[$field]) : $row[$field] ?>
                <?php elseif ($field === 'gmt_golf_name'): ?>

                          <?= $row[$field] ?>

                <?php else: ?>
                  <?= $row[$field] ?>
                <?php endif; ?>
              </td>
            <?php endforeach; ?>
          </tr>
          <?php
          $counts--;
        }
      } else {
        ?>
        <tr>
          <td colspan="<?php echo $list_columns_cnt?>" class="text-center"><b>자료가 없습니다.</b></td>
        </tr>
        <?php
      }
      ?>
      </tbody>
    </table>
  </div>

  <?php
  if($n_page > 1) {
    echo page_listing_xhr($pg, $n_page, 'f_get_box_mng_second_list');
  }


}
else if ($_POST['act'] == "sell_input" || $_POST['act'] == "sell_update") {
  header('Content-Type: application/json');

  try {
    // 필수 입력값 체크
    if (empty($_POST['gmt_golf_name'])) {
      throw new Exception("골프장명을 입력해주세요.");
    }
    if (empty($_POST['gmtt_type'])) {
      throw new Exception("구분을 선택헤주세요.");
    }
    if (empty($_POST['mt_name'])) {
      throw new Exception("등록자를 입력해주세요.");
    }
    if (empty($_POST['mt_hp'])) {
      throw new Exception("등록자 전화번호를 입력해주세요.");
    }
    if (empty($_POST['gmtt_first_price'])) {
      throw new Exception("판매 최초분양금액을 입력해주세요.");
    }
    if (empty($_POST['gmtt_hope_price'])) {
      throw new Exception("판매 희망금액을 입력해주세요.");
    }
    if (empty($_POST['gmtt_num'])) {
      throw new Exception("회원권번호을 입력해주세요.");
    }


    // 트랜잭션 시작
    $DB->startTransaction();

    if($_POST['act'] == "sell_update"){
      $DB->where('gmtt_idx', $_POST['nt_idx']);
      $old_data = $DB->getOne($CFG_TBL['golf_membership']['transaction']);
      if(!$old_data) {
        throw new Exception("데이터가 없습니다.");
      }
    }

    $DB->where('gmt_golf_name', $_POST['gmt_golf_name']);
    $saleInfo = $DB->getone($CFG_TBL['golf_membership']['main'], '*');
    if(!$saleInfo) {
      throw new Exception("해당 회원권을 찾을 수 없습니다.");
    }


    unset($arr_query);
    $arr_query = array(
      "gmt_idx"           => clean_xss_tags($saleInfo['gmt_idx']),
      "gmt_golf_name"     => clean_xss_tags($_POST['gmt_golf_name']),
      "mt_name"           => clean_xss_tags($_POST['mt_name']),
      "mt_hp"             => clean_xss_tags($_POST['mt_hp']),
      "gmtt_level"        => 1,
      "gmtt_type"         => clean_xss_tags($_POST['gmtt_type']),
      "gmtt_status"       => 1,
      "gmtt_hope_price"   => clean_xss_tags($_POST['gmtt_hope_price']),
      "gmtt_first_price"  => clean_xss_tags($_POST['gmtt_first_price']),
      "gmtt_num"          => clean_xss_tags($_POST['gmtt_num']),
      "gmtt_conclusion_txt"  => clean_xss_tags($_POST['gmtt_conclusion_txt']),
    );


    if($_POST['act'] == "sell_update"){
      $DB->where('gmtt_idx', $old_data['gmtt_idx']);
      if(!$DB->update($CFG_TBL['golf_membership']['transaction'], $arr_query)) {
        throw new Exception("데이터 변경에 실패했습니다.");
      }

      $_last_idx = $old_data['gmtt_idx'];
    } else {
      $_last_idx = $DB->insert($CFG_TBL['golf_membership']['transaction'], $arr_query);
      if(!$_last_idx) {
        throw new Exception("데이터 저장에 실패했습니다.");
      }
    }

    // 삭제된 파일 처리
    if (!empty($_POST['removed_files'])) {
      $removed_file_ids = json_decode($_POST['removed_files'], true); // 배열로 복원

      foreach ($removed_file_ids as $ft_idx) {
        $ft_idx = (int)$ft_idx;

        // 파일 정보 가져오기
        $file = $DB->getOne($CFG_TBL['file']['default'], '*', ['ft_idx' => $ft_idx]);

        if ($file) {
          $filepath = $ct_sell_membership_dir . $file['ft_file'];
          if (file_exists($filepath)) {
            unlink($filepath);
          }
          // DB 삭제
          $DB->where('ft_idx', $ft_idx)->delete($CFG_TBL['file']['default']);
        }
      }
    }

    $maxFiles = isset($_POST['maxFiles']) ? (int)$_POST['maxFiles'] : 1; // 기본값 1
    // 기존 등록된 파일 개수 조회
    $DB->where('ft_pidx', $_last_idx);
    $existing_files = $DB->get($CFG_TBL['file']['default']);
    $used_positions = count($existing_files);

    // 새 파일 업로드 처리
    if (!empty($_FILES)) {
      foreach ($_FILES as $key => $file) {
        if ($file['error'] === 0) {
          if ($used_positions >= $maxFiles) break;

          $position = $used_positions + 1;

          $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
          $timestamp = time(); // 타임스탬프 추가
          $filename = "sell_".$_last_idx . "_{$position}_{$timestamp}." . $file_ext;
          $filepath = $ct_sell_membership_dir . $filename;


          if (move_uploaded_file($file['tmp_name'], $filepath)) {
            chmod($filepath, 0644);

            $insert_data = [
              "ft_pidx" => $_last_idx,
              "ft_type" => 4,
              "ft_file" => $filename,
              "ft_file_ori" => $file['name'],
              "ft_file_size" => $file['size'],
            ];

            if (!$DB->insert($CFG_TBL['file']['default'], $insert_data)) {
              throw new Exception("파일업데이트 실패했습니다.");
            }

            $used_positions++;
          }
        }
      }
    }


    $DB->commit();

    // 성공 응답
    echo json_encode([
      'success' => true,
      'message' => '저장 되었습니다.',
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
else if($_POST['act'] == "sell_delete") {

  header('Content-Type: application/json');
  try {
    // 트랜잭션 시작
    $DB->startTransaction();

    $DB->where('gmtt_idx', $_POST['idx']);
    $row = $DB->getOne($CFG_TBL['golf_membership']['transaction']);

    if(!$row) {
      throw new Exception("삭제할 데이터가 존재하지 않습니다.");
    }

    $DB->where('gmtt_idx', $_POST['idx']);

    $arr_query = ['gmtt_del' =>'Y'];
    $DB->update($CFG_TBL['golf_membership']['transaction'], $arr_query);

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
else if($_POST['act'] == "sell_list") {
    unset($list);
    $DB->pageLimit = $_POST['obj_limit_num'];
    $pg = $_POST['obj_pg'];
    //검색

    $search_txt = trim($_POST['obj_search_txt'] ?? '');
    $search_col = $_POST['obj_sel_search'] ?? '';

    if ($search_txt !== '') {
        if ($search_col == "all") {
          $DB->where("( INSTR(a2.gmt_golf_name, ?) )", [$search_txt]);
        } else {
          $DB->where("( INSTR({$search_col}, ?) )", [$search_txt]);
        }
    }

    $obj_search_status = $_POST['obj_search_status'] ?? 1;
    $DB->where('a1.gmtt_status', $obj_search_status);

    $DB->where('a1.gmtt_del', 'N');
    $DB->where('a1.gmtt_show', 'Y');
    $DB->where('a1.gmtt_level', 1);
    $DB->where('a2.gmt_del', 'N');

    //정렬
    $DB->orderBy("a1.gmtt_idx", "desc");

    $select = "
      a1.*, 
      a2.gmt_golf_name,
      a1.gmtt_idx as nt_idx
    ";

    $table = "{$CFG_TBL['golf_membership']['transaction']} a1 
          LEFT JOIN {$CFG_TBL['golf_membership']['main']} a2 
          ON a1.gmt_idx = a2.gmt_idx";

    $DB->setTrace(true); // 쿼리 추적 활성화
    $list = $DB->arraybuilder()->paginate($table, $pg,  $select);
    $debug = $DB->trace; // 모든 쿼리 정보 배열로 반환
    //print_r($debug);

    //페이징
    $n_page = $DB->totalPages;
    $total_count = $DB->totalCount;
    $counts = $total_count - (($pg - 1) * $_POST['obj_limit_num']);


    // 상태별 컬럼 $obj_search_status
    $column_config = [
      1 => [
        '구분' => 'gmtt_type',
        '골프장명' => 'gmt_golf_name',
        '등록자' => 'mt_name',
        '등록자 전화번호' => 'mt_hp',
        '희망가' => 'gmtt_hope_price',
        '진행여부' => 'gmtt_status',
        '등록일자' => 'gmtt_wdate',
        '체결 희망자' => 'contractUser',
        '관리' => '__action__'
      ],
      2 => [
        '구분' => 'gmtt_type',
        '골프장명' => 'gmt_golf_name',
        '등록자' => 'mt_name',
        '등록자 전화번호' => 'mt_hp',
        '희망가' => 'gmtt_hope_price',
        '체결 희망자' => 'contractUser',
        '체결희망자 전화번호' => 'mt_conclusion_hp',
        '진행여부' => 'gmtt_status',
        '체결 희망일자' => 'gmtt_hdate',
        '관리' => '__action__'
      ],
      3 => [
        '구분' => 'gmtt_type',
        '골프장명' => 'gmt_golf_name',
        '등록자' => 'mt_name',
        '등록자 전화번호' => 'mt_hp',
        '희망가' => 'gmtt_hope_price',
        '체결 희망자' => 'contractUser',
        '체결희망자 전화번호' => 'mt_conclusion_hp',
        '진행여부' => 'gmtt_status',
        '체결 완료일자' => 'gmtt_edate',
        '관리' => '__action__'
      ]
    ];
    $list_columns = $column_config[$obj_search_status];
    $list_columns_cnt = count($list_columns);


    ?>
    <div class="d-flex justify-content-between align-items-center mt-3 mb-2">
      <h5 class="h5 mb-0">판매(<span><?= number_format($total_count) ?></span>)</h5>
    </div>
    <div class="table-responsive margin-top-5">
        <table class="table table-striped table-bordered margin-bottom-20" id="listTable" style="min-width: 770px; table-layout: fixed;">
            <thead class="thead-dark">
                <tr>
                  <?php foreach ($list_columns as $label => $field): ?>
                    <th class="text-center" scope="col"><?= $label ?></th>
                  <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
            <?php
            if ($list) {
                foreach ($list as $row) {

                  if($obj_search_status==1){
                    $registration_url = "./purchaser.php?nt_idx={$row['nt_idx']}&state=2";
                    $registration_txt = "등록";
                  } else if($obj_search_status==2){
                    $registration_url = "./procress.php?nt_idx={$row['nt_idx']}&state=2";
                    $registration_txt = "[{$row['mt_conclusion_name']}]";
                  } else if($obj_search_status==3){
                    $registration_url = "./complete.php?nt_idx={$row['nt_idx']}&state=2";
                    $registration_txt = "[{$row['mt_conclusion_name']}]";
                  }

                  $modify_url = "./form_sell.php?act=sell_update&nt_idx={$row['nt_idx']}&gmt_idx={$row['gmt_idx']}";
            ?>
                <tr draggable="false" data-id="<?=$row['nt_idx']?>" data-gmt-idx="<?=$row['gmt_idx']?>" data-gmtt-idx="<?=$row['gmtt_idx']?>">

                  <?php foreach ($list_columns as $label => $field): ?>
                    <td class="text-center">
                      <?php if ($field === '__action__'): ?>

                        <input type="button" class="btn btn-outline-info btn-sm" value="수정" onclick="location.href='<?=$modify_url?>'" />
                        <input type="button" class="btn btn-outline-danger btn-sm" value="삭제" onclick="f_post_act_del('./update.php', '<?=$row['nt_idx']?>', 'sell_delete');" />
                      <?php elseif ($field === 'contractUser' && $registration_txt == '등록'): ?>
                        <input type="button" class="btn btn-outline-info btn-sm" value="<?=$registration_txt?>" onclick="location.href='<?=$registration_url?>'" />
                      <?php elseif ($field === 'contractUser' && $registration_txt != '등록'): ?>
                        <input type="button" class="btn btn-sm" value="<?=$registration_txt?>" onclick="location.href='<?=$registration_url?>'" />

                      <?php elseif (str_ends_with($field, 'date')): ?>
                        <?= DateType($row[$field], 1) ?>
                      <?php elseif ($field === 'gmtt_type'): ?>
                        <?= $arr_gmtt_type[$row[$field]] ?>
                      <?php elseif ($field === 'gmtt_status'): ?>

                          <?= $arr_gmtt_status[$row[$field]] ?>

                      <?php elseif ($field === 'gmtt_hope_price'): ?>
                        <?= is_numeric($row[$field]) ? number_format($row[$field]) : $row[$field] ?>
                      <?php elseif ($field === 'gmt_golf_name'): ?>

                          <?= $row[$field] ?>

                      <?php else: ?>
                        <?= $row[$field] ?>
                      <?php endif; ?>
                    </td>
                  <?php endforeach; ?>
                </tr>
                <?php
                    $counts--;
                }
            } else {
                ?>
                <tr>
                    <td colspan="<?php echo $list_columns_cnt?>" class="text-center"><b>자료가 없습니다.</b></td>
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
else if ($_POST['act'] == "loadimage") {

  try {
    if(empty($_POST['ct_idx'])) {
      throw new Exception("필수 파라미터가 누락되었습니다.");
    }

    $DB->where('ft_pidx', $_POST['ct_idx']);
    $DB->where('ft_type', 4);
    $files = $DB->get($CFG_TBL['file']['default']);

    if(!$files) {
      throw new Exception("불러올 파일 데이터가 없습니다.");
    }

    $result = array();

    // 이미지 정보 처리
    for($i = 1; $i <= 3; $i++) {
      $real_key = $i - 1;
      $img_key = $files[$real_key]['ft_idx'];
      $result[$img_key] = array(
        'exists' => false,
        'url' => '',
        'filename' => ''
      );

      if(!empty($files[$real_key])) {
        $filepath = $ct_sell_membership_dir . $files[$real_key]['ft_file'];
        if(file_exists($filepath)) {
          $result[$img_key] = array(
            'exists' => true,
            'url' => $ct_sell_membership_url . $files[$real_key]['ft_file'],
            'filename' => $files[$real_key]['ft_file']
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
else if ($_POST['act'] == "golf_price_info") {

  try {
    if(empty($_POST['golf_name'])) {
      throw new Exception("골프장명이 없습니다.");
    }

    $DB->where('gmt_golf_name', $_POST['golf_name']);
    $saleInfo = $DB->getone($CFG_TBL['golf_membership']['main'], '*');
    if(!$saleInfo) {
      throw new Exception("골프장 데이터가 없습니다.");
    }

    $prices = getGolfPrice($saleInfo['gmt_idx']);

    echo json_encode(array(
      'success' => true,
      'data' => [
        'gmt_sale_price' => $saleInfo['gmt_sale_price'] ?? 0,
        'gmt_conclusion_price' => $prices['gmt_conclusion_price'] ?? 0,
        'gmt_now_buy_price' => $prices['gmt_now_buy_price'] ?? 0,
        'gmt_now_sale_price' => $prices['gmt_now_sale_price'] ?? 0,
      ]
    ));

  } catch (Exception $e) {
    echo json_encode(array(
      'success' => false,
      'message' => $e->getMessage()
    ));
  }
  exit;

}
else if ($_POST['act'] == "deal_update") {
  header('Content-Type: application/json');

  try {
    // 필수 입력값 체크
    if (empty($_POST['mt_conclusion_name'])) {
      throw new Exception("체결자명을 입력해주세요.");
    }
    if (empty($_POST['mt_conclusion_hp'])) {
      throw new Exception("체결희망자 전화번호를 선택해주세요.");
    }
    if (empty($_POST['gmtt_conclusion_type'])) {
      throw new Exception("체결구분을 선택해주세요.");
    }


    // 트랜잭션 시작
    $DB->startTransaction();

    $DB->where('gmtt_idx', $_POST['nt_idx']);
    $old_data = $DB->getOne($CFG_TBL['golf_membership']['transaction']);
    if(!$old_data) {
      throw new Exception("데이터가 없습니다.");
    }

    if($old_data['gmtt_status']>1) {
      throw new Exception("이미 체결 진행중이거나 완료된 건입니다.");
    }

    unset($arr_query);
    $arr_query = array(
      "mt_conclusion_name"    => clean_xss_tags($_POST['mt_conclusion_name']),
      "mt_conclusion_hp"      => clean_xss_tags($_POST['mt_conclusion_hp']),
      "gmtt_conclusion_type"  => clean_xss_tags($_POST['gmtt_conclusion_type']),
      "gmtt_status"           => 2,
      "gmtt_hdate"            => date('Y-m-d H:i:s', time()),
    );

    $DB->where('gmtt_idx', $old_data['gmtt_idx']);
    if(!$DB->update($CFG_TBL['golf_membership']['transaction'], $arr_query)) {
      throw new Exception("데이터 변경에 실패했습니다.");
    }

    $_last_idx = $old_data['gmtt_idx'];


    $DB->commit();

    // 성공 응답
    echo json_encode([
      'success' => true,
      'message' => '변경완료 되었습니다.',
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
else if ($_POST['act'] == "status_update") {
  header('Content-Type: application/json');

  try {
    // 필수 입력값 체크
    if (empty($_POST['gmtt_status'])) {
      throw new Exception("진행상태를 입력해주세요.");
    }

    if($_POST['gmtt_status']!='1' && $_POST['gmtt_status']!='3'){
      throw new Exception("변경가능한 진행상태가 아닙니다.");
    }

    // 트랜잭션 시작
    $DB->startTransaction();
    $DB->where('gmtt_idx', $_POST['nt_idx']);
    $DB->where('gmtt_del', 'N');
    $old_data = $DB->getOne($CFG_TBL['golf_membership']['transaction']);
    if(!$old_data) {
      throw new Exception("데이터가 없습니다.");
    }

    if($old_data['gmtt_status'] == '3') {
      throw new Exception("체결 완료시에는 상태 변경이 불가능합니다.");
    }



    unset($arr_query);

    if($_POST['gmtt_status']=='1'){
      $arr_query = array(
        "mt_conclusion_idx" => null,
        "mt_conclusion_name" => null,
        "mt_conclusion_hp" => null,
        "gmtt_conclusion_type" => null,
        "gmtt_hdate"            => null,
        "gmtt_status"           => $_POST['gmtt_status'],
      );
    } else if($_POST['gmtt_status']=='3'){
      $arr_query = array(
        "gmtt_status"           => $_POST['gmtt_status'],
        "gmtt_edate"            => date('Y-m-d H:i:s', time()),
      );
    }

    $DB->where('gmtt_idx', $old_data['gmtt_idx']);
    if(!$DB->update($CFG_TBL['golf_membership']['transaction'], $arr_query)) {
      throw new Exception("데이터 변경에 실패했습니다.");
    }
    $_last_idx = $old_data['gmtt_idx'];

    //체결진행 -> 체결 완료
    if($_POST['gmtt_status']=='3'){
      //골프장 최근 거래가 수정
      unset($arr_query);
      $arr_query = array(
        "gmt_deal_price" => $old_data['gmtt_hope_price'],
      );
      $DB->where('gmt_idx', $old_data['gmt_idx']);
      if(!$DB->update($CFG_TBL['golf_membership']['main'], $arr_query)) {
        throw new Exception("데이터 변경에 실패했습니다.");
      }
      //회원알림

      $DB->where('gmt_idx', $old_data['gmt_idx']);
      $DB->where('gmt_show', 'Y');
      $DB->where('gmt_del', 'N');
      $saleInfo = $DB->getOne($CFG_TBL['golf_membership']['main']);
      if(!$saleInfo) {
        throw new Exception("해당 회원권 정보를 찾을 수 없습니다.");
      }

      $where = ['gmt_idx'=>$saleInfo['gmt_idx']];
      $order = ['order'=>'gmlt_idx', 'by'=>'ASC'];
      $push_list = getListByTable($CFG_TBL['golf_membership']['like'], $where, $order);

      if(!$push_list){
        throw new Exception("알림을 보낼 사용자를 찾을 수 없습니다.");
      }

      foreach ($push_list as $push) {

        $DB->where('idx', $push['mt_idx']);
        $DB->where('mt_level', '2');
        $DB->where('mt_push', 'Y');
        $member = $DB->getOne($CFG_TBL['member']['default']);
        if($member){

          unset($arr_query);
          $arr_query = [
            'mt_idx' => $member['idx'],
            'pt_type' => 2,
            'pt_pidx' => $saleInfo['gmt_idx'],
            'pt_sidx' => $old_data['gmtt_idx'],
            'pt_ptype' => 3,
            'pt_title' => '회원권 시세가 변경되었습니다.',
          ];
          $_last_idx = $DB->insert($CFG_TBL['push']['default'], $arr_query);
          if(!$_last_idx) {
            throw new Exception("데이터 저장에 실패했습니다.");
          }

          //push 전송
          $param = [
            'mt_idx' => $member['idx'],
            'body' => $saleInfo['gmt_golf_name'],
            'title' => '회원권 시세가 변경되었습니다.',
          ];



        }
      }



    }


    $DB->commit();

    // 성공 응답
    echo json_encode([
      'success' => true,
      'message' => '변경완료 되었습니다.',
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




include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
