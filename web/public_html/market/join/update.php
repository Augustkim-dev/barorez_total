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
          $DB->where('jt_idx', $_POST['nt_idx']);
          $old_data = $DB->getOne($CFG_TBL['join']['default']);
          if(!$old_data) {
            throw new Exception("데이터가 없습니다.");
          }
        }

        $max_order = $DB->getValue($CFG_TBL['join']['default'], "COALESCE(MAX(rt_order), 0) + 1");

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
          $DB->where('jt_idx', $old_data['jt_idx']);
          if(!$DB->update($CFG_TBL['join']['default'], $arr_query)) {
            throw new Exception("데이터 변경에 실패했습니다.");
          }

          $_last_idx = $old_data['jt_idx'];
        } else {
          $arr_query['rt_order'] =  $max_order;
          $_last_idx = $DB->insert($CFG_TBL['join']['default'], $arr_query);
          if(!$_last_idx) {
            throw new Exception("데이터 저장에 실패했습니다.");
          }
        }


        $DB->where('jt_idx', $_last_idx);
        $DB->delete($tbl_flavor_name);
        if (isset($_POST['rt_flavors']) && is_array($_POST['rt_flavors'])) {
          foreach ($_POST['rt_flavors'] as $flavor_id) {
            $flavor_id = (int) $flavor_id;
            if ($flavor_id > 0) {
              $DB->insert($tbl_flavor_name, [
                "jt_idx"     => $_last_idx,
                "flavor_idx" => $flavor_id
              ]);
            }
          }
        }

        $DB->where('jt_idx', $_last_idx);
        $DB->delete($tbl_pairing_name);
        if (isset($_POST['pairing_detail']) && is_array($_POST['pairing_detail'])) {
          foreach ($_POST['pairing_detail'] as $i => $detail) {
            $pairing_idx = (int) ($_POST['pairing_detail_key'][$i] ?? 0);
            $pairing_detail = clean_xss_tags($detail);

            if ($pairing_idx > 0 && $pairing_detail !== '') {
              $DB->insert($tbl_pairing_name, [
                "jt_idx"         => $_last_idx,
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


      $DB->where('jt_idx', $_last_idx);
      if(!$DB->update($CFG_TBL['join']['default'], $arr_query)) {
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
              $DB->where('jt_idx', $_last_idx);
              if (!$DB->update($CFG_TBL['join']['default'], $update_data)) {
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

        $DB->where('jt_idx', $_POST['idx']);
        $row = $DB->getOne($CFG_TBL['join']['default']);

        if(!$row) {
            throw new Exception("삭제할 데이터가 존재하지 않습니다.");
        }

        $DB->where('jt_idx', $row['jt_idx']);
        $arr_query = ['jt_del' =>'Y'];
        $DB->update($CFG_TBL['join']['default'], $arr_query);

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

    if ($search_txt !== '') {
      if ($search_col == "all") {
        $DB->where("( INSTR(a1.gmt_golf_name, ?) )", [$search_txt]);
      } else {
        $DB->where("( INSTR({$search_col}, ?) )", [$search_txt]);
      }
    }

    $DB->where('a1.jt_del', 'N');
    $DB->orderBy("a1.jt_idx", "desc");

    $select = "
        *, 
        a1.jt_idx as nt_idx
      ";

    $table = "{$CFG_TBL['join']['default']} a1";

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
                      골프장명
                    </th>
                    <th class="text-center" >
                      제목
                    </th>
                    <th class="text-center" style="width:100px;">
                      초청일시
                    </th>
                    <th class="text-center" style="width:100px;">
                      등록일
                    </th>
                 </tr>
            </thead>
            <tbody>
            <?php
            if ($list) {
                foreach ($list as $row) {
              ?>
                <tr draggable="false" data-id="<?=$row['jt_idx']?>">
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
                      <span class="line1_text"><?=$row['gmt_golf_name']?></span>
                    </td>
                    <td class="text-center">
                        <span class="line1_text"><?=$row['jt_content']?></span>
                    </td>
                    <td class="text-center">
                      <span class="line1_text"><?=DateType($row['jt_jdate'], 4)?></span>
                    </td>
                    <td  class="text-center">
                      <span class="line1_text"><?=DateType($row['jt_wdate'], 4)?></span>
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
          'rt_order' => $sequence
        );

        $DB->where('jt_idx', $idx);
        $DB->update($CFG_TBL['join']['default'], $arr_query);

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
  $ct_show = $_POST['rt_show'];

  $DB->where('jt_idx', $_POST['id']);
  $arr_query = array(
    'rt_show' => $ct_show
  );
  $result = $DB->update($CFG_TBL['join']['default'], $arr_query);

  // 결과 반환
  echo json_encode([
    'success' => $result,
    'message' => $result ? '성공적으로 변경되었습니다.' : '처리 중 오류가 발생했습니다.'
  ]);
  exit;

}
else if ($_POST['act'] == "loadimage") {

  try {
    if(empty($_POST['ct_idx'])) {
      throw new Exception("필수 파라미터가 누락되었습니다.");
    }

    $DB->where('ft_pidx', $_POST['ct_idx']);
    $DB->where('ft_type', 3);
    $files = $DB->get($CFG_TBL['file']['default']);

    if(!$files) {
      throw new Exception("불러올 파일데이터가 없습니다.");
    }

    $result = array();

    // 이미지 정보 처리
    for($i = 1; $i <= 5; $i++) {
      $real_key = $i - 1;
      $img_key = $files[$real_key]['ft_idx'];
      $result[$img_key] = array(
        'exists' => false,
        'url' => '',
        'filename' => ''
      );

      if(!empty($files[$real_key])) {
        $filepath = $ct_review_dir . $files[$real_key]['ft_file'];
        if(file_exists($filepath)) {
          $result[$img_key] = array(
            'exists' => true,
            'url' => $ct_review_url . $files[$real_key]['ft_file'],
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



include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
