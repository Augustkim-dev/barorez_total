<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";



if ($_POST['act'] == "input" || $_POST['act'] == "update") {
    header('Content-Type: application/json');

    try {
        // 필수 입력값 체크
        if(empty($_POST['mt_idx'])) {
          throw new Exception("작성자를 입력해주세요.");
        }
        if(empty($_POST['pt_idx'])) {
          throw new Exception("와인정보를 입력해주세요.");
        }
        if(empty($_POST['rt_content'])) {
          throw new Exception("내용을 입력해주세요.");
        }
        if(empty($_POST['rt_score'])) {
          throw new Exception("평점을 입력해주세요.");
        }

        // 트랜잭션 시작
        $DB->startTransaction();

        if($_POST['act'] == "update"){
          $DB->where('rt_idx', $_POST['nt_idx']);
          $old_data = $DB->getOne($CFG_TBL['report']['default']);
          if(!$old_data) {
            throw new Exception("데이터가 없습니다.");
          }
        }

        $max_order = $DB->getValue($CFG_TBL['report']['default'], "COALESCE(MAX(rt_order), 0) + 1");

        $DB->where('idx', $_POST['mt_idx']);
        $member = $DB->getOne($CFG_TBL['member']['default']);




        $rt_price = (int) str_replace(',', '', $_POST['rt_price']);


        unset($arr_query);
        $arr_query = array(
          "mt_idx" => $member['idx'],
          "mt_id" => $member['mt_id'],
          "mt_name" => $member['mt_nickname'],
          "pt_idx" => $wine['idx'],
          "type_id" => $wine['type_id'],
          "pt_title" => $wine['name'],
          "wine_country" => $wine['country'],
          "rt_wine_name"      => clean_xss_tags($_POST['rt_wine_name']),
          "rt_variety_name"   => clean_xss_tags($_POST['rt_variety_name']),
          "rt_country_name"   => clean_xss_tags($_POST['rt_country_name']),
          "rt_place"          => clean_xss_tags($_POST['rt_place']),
          "rt_price"          => $rt_price,
          "rt_temp"           => (int) $_POST['rt_temp'],
          "rt_repurchase"     => clean_xss_tags($_POST['rt_repurchase']),
          "rt_content" => clean_xss_tags($_POST['rt_content']),
          "rt_score" => $_POST['rt_score'],
          "rt_color"             => !empty($_POST['rt_color']) ? $_POST['rt_color'] : 0,
          "rt_taste_intensity"   => !empty($_POST['rt_taste_intensity']) ? $_POST['rt_taste_intensity'] : 0,
          "rt_taste_acidity"     => !empty($_POST['rt_taste_acidity']) ? $_POST['rt_taste_acidity'] : 0,
          "rt_taste_sweetness"   => !empty($_POST['rt_taste_sweetness']) ? $_POST['rt_taste_sweetness'] : 0,
          "rt_taste_tannin"      => !empty($_POST['rt_taste_tannin']) ? $_POST['rt_taste_tannin'] : 0,
        );


        if($_POST['act'] == "update"){
          $DB->where('rt_idx', $old_data['rt_idx']);
          if(!$DB->update($CFG_TBL['report']['default'], $arr_query)) {
            throw new Exception("데이터 변경에 실패했습니다.");
          }

          $_last_idx = $old_data['rt_idx'];
        } else {
          $arr_query['rt_order'] =  $max_order;
          $_last_idx = $DB->insert($CFG_TBL['report']['default'], $arr_query);
          if(!$_last_idx) {
            throw new Exception("데이터 저장에 실패했습니다.");
          }
        }


        $DB->where('rt_idx', $_last_idx);
        $DB->delete($tbl_flavor_name);
        if (isset($_POST['rt_flavors']) && is_array($_POST['rt_flavors'])) {
          foreach ($_POST['rt_flavors'] as $flavor_id) {
            $flavor_id = (int) $flavor_id;
            if ($flavor_id > 0) {
              $DB->insert($tbl_flavor_name, [
                "rt_idx"     => $_last_idx,
                "flavor_idx" => $flavor_id
              ]);
            }
          }
        }

        $DB->where('rt_idx', $_last_idx);
        $DB->delete($tbl_pairing_name);
        if (isset($_POST['pairing_detail']) && is_array($_POST['pairing_detail'])) {
          foreach ($_POST['pairing_detail'] as $i => $detail) {
            $pairing_idx = (int) ($_POST['pairing_detail_key'][$i] ?? 0);
            $pairing_detail = clean_xss_tags($detail);

            if ($pairing_idx > 0 && $pairing_detail !== '') {
              $DB->insert($tbl_pairing_name, [
                "rt_idx"         => $_last_idx,
                "pairing_idx"    => $pairing_idx,
                "pairing_detail" => $pairing_detail
              ]);
            }
          }
        }



      $maxFiles = isset($_POST['maxFiles']) ? (int)$_POST['maxFiles'] : 1; // 기본값 1
      $finalImages = [];
      // 현재 이미지 상태 저장
      for ($i = 1; $i <= $maxFiles; $i++) {
        $finalImages["rt_img{$i}"] = !empty($old_data["rt_img{$i}"]) ? $old_data["rt_img{$i}"] : '';
      }

      // 삭제된 파일 처리
      if (!empty($_POST['removed_files'])) {
        $removedFiles = json_decode($_POST['removed_files'], true);
        foreach ($removedFiles as $fileNum) {
          if (!empty($finalImages[$fileNum])) {
            $old_file = $ct_review_dir . $finalImages[$fileNum];
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
          $arr_query["rt_img{$pos}"] = $filename;
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
            $imageNum = str_replace('rt_img', '', $item['id']);
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
          $arr_query["rt_img{$pos}"] = $filename;
        }
      }


      $DB->where('rt_idx', $_last_idx);
      if(!$DB->update($CFG_TBL['report']['default'], $arr_query)) {
        throw new Exception("데이터 저장에 실패했습니다.");
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
            $filename = "rt_img_".$_last_idx . "_{$position}_{$timestamp}." . $file_ext;
            $filepath = $ct_review_dir . $filename;

            // 기존 파일 삭제
            if (!empty($finalImages[$position])) {
              $old_file = $ct_review_dir . $finalImages[$position];
              if(file_exists($old_file)) {
                unlink($old_file);
              }
            }

            if (move_uploaded_file($file['tmp_name'], $filepath)) {
              chmod($filepath, 0644);

              $update_data = ["rt_img{$position}" => $filename];
              $DB->where('rt_idx', $_last_idx);
              if (!$DB->update($CFG_TBL['report']['default'], $update_data)) {
                throw new Exception("파일업데이트 실패했습니다.");
              }

              $finalImages[$position] = $filename;
            }
          }
        }
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
else if($_POST['act'] == "delete") {

    header('Content-Type: application/json');
    try {
        // 트랜잭션 시작
        $DB->startTransaction();

        $DB->where('rt_idx', $_POST['idx']);
        $row = $DB->getOne($CFG_TBL['report']['default']);

        if(!$row) {
            throw new Exception("삭제할 데이터가 존재하지 않습니다.");
        }

        $DB->where('rt_idx', $row['rt_idx']);
        $arr_query = ['rt_del' =>'Y'];
        $DB->update($CFG_TBL['report']['default'], $arr_query);

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

    $search_txt = trim($_POST['obj_search_txt'] ?? '');
    $search_col = $_POST['obj_sel_search'] ?? '';

    $sdate = $_POST['obj_search_sdate'] ?? '';
    $edate = $_POST['obj_search_edate'] ?? '';
    $search_status = $_POST['obj_search_status'] ?? '';
    $search_singo = $_POST['obj_search_singo'] ?? '';

    if ($search_txt !== '') {
      if ($search_col == "all") {
        $DB->where("( INSTR(a1.mt_id, ?) or INSTR(a2.rt_content, ?) )", [$search_txt, $search_txt]);
      } else {
        $DB->where("( INSTR({$search_col}, ?) )", [$search_txt]);
      }
    }

    if($sdate!='' && $edate!=''){
      $DB->where('a1.rt_wdate', Array ($sdate." 00:00:00", $edate." 23:59:59"), 'BETWEEN');
    }

    if($search_status!='all'){
      $DB->where('a1.rt_status', $search_status);
    }

    if($search_singo!='all'){
      $DB->where('a1.rt_reason', $search_singo);
    }


    $DB->where('a1.rt_type', '3');
    $DB->where('a1.rt_del', 'N');
    $DB->orderBy("a1.rt_idx", "desc");

    $select = "
        a1.*, a1.rt_idx as nt_idx, a2.rt_content, a2.rt_del as review_del
      ";

    $table = "{$CFG_TBL['report']['default']} a1 
            LEFT JOIN {$CFG_TBL['review']['default']} a2 
            ON a1.rt_pidx = a2.rt_idx";

    $DB->setTrace(true); // 쿼리 추적 활성화
    $list = $DB->arraybuilder()->paginate($table, $pg,  $select);
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
                    <th class="text-center d-none" style="width:50px;">
                        <input type="checkbox" id="selectAll" class="custom-checkbox-list" />
                    </th>
                    <th class="text-center" style="width:80px;">
                        번호
                    </th>
                    <th class="text-center" style="width:120px;">
                        관리
                    </th>
                    <th class="text-center" style="width:200px;">
                      아이디
                    </th>
                    <th class="text-center" >
                      내용
                    </th>
                    <th class="text-center" >
                      신고사유
                    </th>
                    <th class="text-center" style="width:100px;">
                      처리상태
                    </th>
                    <th class="text-center" style="width:100px;">
                      신고일
                    </th>
                 </tr>
            </thead>
            <tbody>
            <?php
            if ($list) {
                foreach ($list as $row) {
              ?>
                <tr draggable="false" data-id="<?=$row['rt_idx']?>">
                    <td class="text-center checkbox-wrapper d-none">
                        <input type="checkbox" class="rowCheckbox custom-checkbox-list" />
                    </td>
                    <td data-title="번호" class="text-center">
                        <?=$counts?>
                    </td>
                    <td data-title="관리" class="text-center">
                        <input type="button" class="btn btn-outline-info btn-sm" value="보기" onclick="location.href='./form.php?act=view&nt_idx=<?=$row['nt_idx']?>'" />
                        <input type="button" class="btn btn-outline-danger btn-sm" value="삭제" onclick="f_post_del('./update.php', '<?=$row['nt_idx']?>');" />
                    </td>
                    <td class="text-center">
                      <span class="line1_text"><?=$row['mt_id']?></span>
                    </td>
                    <td class="text-center">
                        <? if($row['review_del']=='Y'){?>
                          <span class="line1_text">삭제된 리뷰입니다.</span>
                        <? } else {?>
                          <span class="line1_text"><?=$row['rt_content']?></span>
                        <?}?>
                    </td>
                    <td class="text-center">
                      <span class="line1_text"><?=$arr_stype[$row['rt_reason']]?></span>
                    </td>

                    <td class="text-center">
                      <span class="line1_text"><?=$arr_singo_rt_status[$row['rt_status']]?></span>
                    </td>
                    <td  class="text-center">
                      <span class="line1_text"><?=DateType($row['rt_wdate'], 4)?></span>
                    </td>



                </tr>
                <?php
                    $counts--;
                }
            } else {
                ?>
                <tr>
                    <td colspan="10" class="text-center"><b>자료가 없습니다.</b></td>
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
else if($_POST['act'] == "review_update") {

  header('Content-Type: application/json');
  try {
    // 트랜잭션 시작
    $DB->startTransaction();

    $DB->where('rt_idx', $_POST['nt_idx']);
    $DB->where('rt_type', '3');
    $DB->where('rt_del', 'N');
    $row = $DB->getOne($CFG_TBL['report']['default']);

    if(!$row) {
      throw new Exception("해당 신고 내용을 찾을 수 없습니다.");
    }

    $DB->where('rt_idx', $row['rt_idx']);
    $arr_query = ['rt_status' => $_POST['rt_status']];
    if(!$DB->update($CFG_TBL['report']['default'], $arr_query)) {
      throw new Exception("신고 처리 사유 변경에 실패했습니다.");
    }


    $DB->where('rt_idx', $row['rt_pidx']);
    $reply = $DB->getone($CFG_TBL['review']['default']);
    if($_POST['rt_status']=='2'){
      if($reply){
        $DB->where('rt_idx', $reply['rt_idx']);
        $arr_query = ['rt_del' => 'Y'];
        if(!$DB->update($CFG_TBL['review']['default'], $arr_query)) {
          throw new Exception("댓글을 삭제 실패했습니다.");
        }
      }
    } else {

      if($reply){
        $DB->where('rt_idx', $reply['rt_idx']);
        $arr_query = ['rt_del' => 'N'];
        if(!$DB->update($CFG_TBL['review']['default'], $arr_query)) {
          throw new Exception("댓글복구 실패했습니다.");
        }
      }

    }


    $DB->commit();

    // 성공 응답
    echo json_encode([
      'success' => true,
      'message' => '처리완료 되었습니다.',
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



include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
