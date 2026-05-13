<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";

if($_POST['act'] == "buy_member_list") {
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
                  <?=$registration_txt?>
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
else if($_POST['act'] == "sell_member_list") {
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
                  <?=$registration_txt?>

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

include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
