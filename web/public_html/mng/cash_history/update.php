<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";


$tbl_name = "cash_history_t";
$tbl_member_name = "member_t";

if ($_POST['act'] == "input" && $_POST['status'] == 'add') {
    header('Content-Type: application/json');

    try {

        $date = date('Y-m-d H:i:s', time());
        $timestamp = strtotime($date." +365 days");
        $expire_date = date("Y-m-d H:i:s", $timestamp);
        $point = 0;
        if($_POST['point']){
          $point = (int) $_POST['point'];
        }

        if(0>=$point){
          throw new Exception("사용할 캐시가 0보다 적습니다.");
        }

        $DB->where('idx', $_POST['mt_idx']);
        $my = $DB->getOne($tbl_member_name);


        // 트랜잭션 시작
        $DB->startTransaction();

        $message = isset($_POST['message']) ? $_POST['message'] : '관리자입력';
        unset($arr_query);
        $arr_query = array(
          "mt_idx" => $my['idx'],
          "point" => $point,
          'status' => "add",
          "regdate" => $date,
          "expired" => 0,
          "expire_date" => $expire_date,
          'message' => $message
        );

        $_last_idx = $DB->insert($tbl_name, $arr_query);
        if(!$_last_idx) {
          throw new Exception("데이터 저장에 실패했습니다.");
        }

        $mt_point = $point + $my['mt_point'];
        unset($arr_query);
        $arr_query = array(
          "mt_point" => $mt_point,
        );

        $DB->where('idx', $my['idx']);
        if(!$DB->update($tbl_member_name, $arr_query)) {
          throw new Exception("데이터 수정에 실패했습니다.");
        }


      $DB->commit();

      // 성공 응답
      echo json_encode([
          'success' => true,
          'message' => '사용처리(충전) 되었습니다.',
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
else if ($_POST['act'] == "input" && $_POST['status'] == 'remove') {
  header('Content-Type: application/json');

  try {

    $point = 0;
    if($_POST['point']){
      $point = (int) $_POST['point'];
    }

    if(0>=$point){
      throw new Exception("사용할 캐시가 0보다 적습니다.");
    }

    $DB->where('idx', $_POST['mt_idx']);
    $my = $DB->getOne($tbl_member_name);
    $sum = $my['mt_point'];
    if($sum<$point){
      throw new Exception("사용할 캐시가 총 캐시보다 적습니다.");
    }

    $message = isset($_POST['message']) ? $_POST['message'] : '관리자입력';
    $param = [
      'mt_idx'=> $my['idx'],
      'cash'=> $point,
      'message' => $message,
    ];
    use_cash_action($param);


    // 성공 응답
    echo json_encode([
      'success' => true,
      'message' => '사용처리(차감) 되었습니다.',
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

else if($_POST['act'] == "list") {
    unset($list);
    $DB->pageLimit = $_POST['obj_limit_num'];
    $pg = $_POST['obj_pg'];
    //검색
    if ($_POST['obj_search_txt']) {
        if ($_POST['obj_sel_search'] == "all") {
            $DB->where('( instr(a1.ot_code, \''.$_POST['obj_search_txt'].'\') )');
        } else {
            $DB->where('( instr('.$_POST['obj_sel_search'].', \''.$_POST['obj_search_txt'].'\') )');
        }
    }

    if ($_POST['obj_sel_mt_idx']) {
        $DB->where('a1.mt_idx', $_POST['obj_sel_mt_idx']);
    }

    if ($_POST['obj_sel_ct_status']) {
      $DB->where('a1.status', $_POST['obj_sel_ct_status']);
    }

    $DB->orderBy("a1.regdate", "desc");


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
                    <th class="text-center" style="width:50px;">
                        <input type="checkbox" id="selectAll" class="custom-checkbox-list" />
                    </th>
                    <th class="text-center" style="width:80px;">
                        번호
                    </th>
                    <th class="text-center" style="width:200px;">
                      회원
                    </th>
                    <th class="text-center">
                      코르크
                    </th>
                    <th class="text-center">
                      상태
                    </th>
                    <th class="text-center">
                      날짜
                    </th>
                    <th class="text-center">
                      상품정보
                    </th>
                    <th class="text-center">
                      메시지
                    </th>

                </tr>
            </thead>
            <tbody>
            <?php
            if ($list) {
                foreach ($list as $row) {
                  $member = get_mem_info('idx', $row['mt_idx']);
                  $chk_usable = check_cash_usable_action($row['ot_code']);

                  $class_name = 'badge-info';
                  $disp_point = $row['point'];

                  $order = null;
                  $cash_order = null;

                  if($row['status']=='add') {
                    $balance += $row['point'];
                    $disp_point = '+'.number_format($row['point']);
                    $class_name = 'badge-success';


                    //$cash_order = get_cash_order_info( $row['ot_code']);
                    //$order = get_order_info($row['ot_code']);

                    //$cash_info = get_cash_info($row['ot_code'], 'ot_code');
                    //$cash_info = $cash_info['cash_t'];

                  } else if($row['status']=='remove' || $row['status']=='remove_expired') {
                    $balance += $row['point'];
                    $disp_point = number_format($row['point']);
                    $class_name = 'badge-danger';

                    //$order = get_order_info($row['ot_code']);

                    //$pt_info = get_product_t_info($row['ot_code'], 'ot_code');


                  }


                    ?>
                <tr draggable="true" data-id="<?=$row['idx']?>">
                    <td class="text-center checkbox-wrapper">
                        <input type="checkbox" class="rowCheckbox custom-checkbox-list" />
                    </td>
                    <td data-title="번호" class="text-center">
                        <?=$counts?>
                    </td>


                    <td class="text-left">

                      <div class="user user--bordered">
                        <img src="<?php echo $member['profile']?>">
                        <div class="user__name">
                          <strong><?=$member['mt_name']?></strong><br/><span><?php echo $member['mt_email']?></span>
                        </div>
                      </div>



                    </td>
                    <td class="text-center">
                      <?php
                      echo $disp_point;
                      ?>
                    </td>
                    <td class="text-center">
                      <span class="badge <?php echo $class_name?>"><?=$arr_cash_status[$row['status']]?></span>
                    </td>
                    <td class="text-center">
                      <?=DateType($row['regdate'], 6)?>
                    </td>
                    <td class="text-center">

                    </td>
                    <td  class="text-center">
                      <span class="line1_text"><?=$row['message']?></span>
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



include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
