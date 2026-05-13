<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";




if ($_POST['act'] == "input") {
    header('Content-Type: application/json');

    try {
        // 필수 입력값 체크
        if(empty($_POST['rt_title'])) {
          throw new Exception("제목을 입력해주세요.");
        }

        // 트랜잭션 시작
        $DB->startTransaction();

        $max_order = $DB->getValue($CFG_TBL['mainbanner']['default'], "COALESCE(MAX(rt_order), 0) + 1");

        unset($arr_query);
        $arr_query = array(
          "rt_status" => $_POST['rt_status'],
          "rt_title" => $_POST['rt_title'],
          "rt_start" => $_POST['rt_start'],
          "rt_end" => $_POST['rt_end'],
          "rt_show" => $_POST['rt_show'],
          "rt_link_url" => $_POST['link_url2'],
          "rt_link_target" => $_POST['rt_link_target'],
          "rt_order" => $max_order,
        );

        $_last_idx = $DB->insert($CFG_TBL['mainbanner']['default'], $arr_query);
        if(!$_last_idx) {
          throw new Exception("데이터 저장에 실패했습니다.");
        }

        // 파일 업로드 처리
        $uploaded_files = []; // 업로드된 파일 정보 저장 배열 추가

        if (!is_dir($ct_mainbanner_dir)) {
          if (!mkdir($ct_mainbanner_dir, 0707, true)) {
            throw new Exception("category 디렉토리 생성 실패");
          }
          chmod($ct_mainbanner_dir, 0707);
        }

        $maxFiles = isset($_POST['maxFiles']) ? (int)$_POST['maxFiles'] : 1; // 기본값 1

        // FilePond 파일 처리
        if (!empty($_FILES)) {
          $filePosition = 1; // 시작 위치

          foreach ($_FILES as $key => $file) {
            if ($file['error'] === 0 && $filePosition <= $maxFiles) {
              $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);

              // 허용된 파일 확장자 검사
              $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
              if (!in_array(strtolower($file_ext), $allowed_extensions)) {
                throw new Exception("허용되지 않는 파일 형식입니다.");
              }

              // 타임스탬프 추가하여 파일명 중복 방지
              $timestamp = time();
              $filename = "rt_img_{$_last_idx}_{$filePosition}_{$timestamp}.{$file_ext}";
              $filepath = $ct_mainbanner_dir . $filename;

              if (move_uploaded_file($file['tmp_name'], $filepath)) {
                chmod($filepath, 0644);

                // 파일명을 DB에 업데이트
                $update_data = ["rt_img{$filePosition}" => $filename];
                $DB->where('idx', $_last_idx);
                if (!$DB->update($CFG_TBL['mainbanner']['default'], $update_data)) {
                  throw new Exception("파일 정보 업데이트 실패");
                }
//                $img_width_t = 1440;
//                $img_height_t = 1000;
//                cropImageWithRatio($filepath, $filepath, $img_width_t, $img_height_t);

                // 업로드된 파일 정보 저장
                $uploaded_files[$filePosition] = $filename;

                $filePosition++;
              }
            }
          }
        }

        // 최종 이미지 상태를 배열로 준비
        $finalImages = array(
          1 => isset($uploaded_files[1]) ? $uploaded_files[1] : '',
        );


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
        if(empty($_POST['rt_title'])) {
          throw new Exception("제목을 입력해주세요.");
        }

        // 트랜잭션 시작
        $DB->startTransaction();

        $DB->where('idx', $_POST['nt_idx']);
        $old_data = $DB->getOne($CFG_TBL['mainbanner']['default']);

        unset($arr_query);
        $arr_query = array(
          "rt_status" => $_POST['rt_status'],
          "rt_title" => $_POST['rt_title'],
          "rt_start" => $_POST['rt_start'],
          "rt_end" => $_POST['rt_end'],
          "rt_show" => $_POST['rt_show'],
          "rt_link_url" => $_POST['link_url2'],
          "rt_link_target" => $_POST['rt_link_target'],
          "rt_order" => $_POST['rt_order'],
        );

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
              $old_file = $ct_mainbanner_dir . $finalImages[$fileNum];
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


        $DB->where('idx', $_POST['nt_idx']);
        if(!$DB->update($CFG_TBL['mainbanner']['default'], $arr_query)) {
          throw new Exception("데이터 수정에 실패했습니다.");
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
              $filename = "rt_img_".$_POST['nt_idx'] . "_{$position}_{$timestamp}." . $file_ext;
              $filepath = $ct_mainbanner_dir . $filename;

              // 기존 파일 삭제
              if (!empty($finalImages[$position])) {
                $old_file = $ct_mainbanner_dir . $finalImages[$position];
                if(file_exists($old_file)) {
                  unlink($old_file);
                }
              }

              if (move_uploaded_file($file['tmp_name'], $filepath)) {
                chmod($filepath, 0644);

                $update_data = ["rt_img{$position}" => $filename];
                $DB->where('idx', $_POST['nt_idx']);
                if (!$DB->update($CFG_TBL['mainbanner']['default'], $update_data)) {
                  throw new Exception("파일 정보 업데이트 실패");
                }

//                $img_width_t = 1440;
//                $img_height_t = 1000;
//                cropImageWithRatio($filepath, $filepath, $img_width_t, $img_height_t);

                $finalImages[$position] = $filename;
              }
            }
          }
        }

        $DB->commit();


        // 최종 이미지 상태 조회
        $DB->where('idx', $_POST['nt_idx']);
        $final_data = $DB->getOne($CFG_TBL['mainbanner']['default']);
        $finalImages = array(
          1 => !empty($final_data['rt_img1']) ? $final_data['rt_img1'] : '',
          2 => !empty($final_data['rt_img2']) ? $final_data['rt_img2'] : '',
          3 => !empty($final_data['rt_img3']) ? $final_data['rt_img3'] : '',
          4 => !empty($final_data['rt_img4']) ? $final_data['rt_img4'] : '',
          5 => !empty($final_data['rt_img5']) ? $final_data['rt_img5'] : '',
        );

        // 성공 응답
        echo json_encode([
            'success' => true,
            'message' => '수정되었습니다.',
            'redirect' => './list.php',
            'finalImages' => $finalImages,
            '_POST' => $_POST,
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
        $row = $DB->getOne($CFG_TBL['mainbanner']['default']);

        if(!$row) {
            throw new Exception("삭제할 데이터가 존재하지 않습니다.");
        }

        unset($arr_query);
        $arr_query = array(
            "rt_show"=>'N',
            "del_date"=>$DB->now(),
        );

        $DB->where('idx', $_POST['idx']);
        $DB->update($CFG_TBL['mainbanner']['default'],$arr_query);

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
        if ($_POST['obj_sel_search'] == "rt_title") {
            $DB->where('( instr(a1.rt_title, \''.$_POST['obj_search_txt'].'\'))');
        } else {
            $DB->where('( instr('.$_POST['obj_sel_search'].', \''.$_POST['obj_search_txt'].'\') )');
        }
    }

    if ($_POST['rt_show']) {
        $DB->where('a1.rt_show', $_POST['rt_show']);
    }


    //정렬
    //if ($_POST['obj_order_desc_asc'] == '1') {
    //  $DB->orderBy("a1.rt_order", "desc");
    //} else {
    //  $DB->orderBy("a1.rt_order", "asc");
    //}
    $DB->orderBy("a1.rt_order", "desc");

    $DB->where('a1.del_date', null, 'IS');

    $DB->setTrace(true); // 쿼리 추적 활성화
    $list = $DB->arraybuilder()->paginate($CFG_TBL['mainbanner']['default']." a1", $pg, '*, idx as nt_idx');
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
<!--                    <th class="text-center" style="width:50px;">-->
<!--                        <input type="checkbox" id="selectAll" class="custom-checkbox-list" />-->
<!--                    </th>-->
                    <th class="text-center" style="width:80px;">
                        번호
                    </th>
                    <th class="text-center" style="width:120px;">
                        관리
                    </th>
                    <th class="text-center" style="width:100px;">
                      구분
                    </th>
                    <th class="text-center">
                      배너제목
                    </th>
                    <th class="text-center">
                      노출기간
                    </th>
                    <th class="text-center">
                      노출여부
                    </th>
                    <th class="text-center">
                      노출순서
                    </th>
                    <th class="text-center">
                      등록일시
                    </th>
<!--                    <th class="text-center">-->
<!--                      수정일시-->
<!--                    </th>-->
                </tr>
            </thead>
            <tbody>
            <?php
            if ($list) {
                foreach ($list as $row) {

                    ?>
                <tr draggable="true" data-id="<?=$row['idx']?>">
<!--                    <td class="text-center checkbox-wrapper">-->
<!--                        <input type="checkbox" class="rowCheckbox custom-checkbox-list" />-->
<!--                    </td>-->
                    <td data-title="번호" class="text-center">
                        <?=$counts?>
                    </td>
                    <td data-title="관리" class="text-center">
                        <input type="button" class="btn btn-outline-info btn-sm" value="수정" onclick="location.href='./form.php?act=update&nt_idx=<?=$row['nt_idx']?>'" />
                        <input type="button" class="btn btn-outline-danger btn-sm" value="삭제" onclick="f_post_del('./update.php', '<?=$row['nt_idx']?>');" />
                    </td>
                    <td class="text-center">
                        메인배너
                    </td>
<!--                    <td class="text-center">-->
<!--                      --><?php
//                      if(!empty($row['rt_img1'])):
//                        $img = resolveImageUrl($row['rt_img1'], $ct_mainbanner_dir, $ct_mainbanner_url);
//                        ?>
<!--                        <img src="--><?php //echo $img?><!--" class="ct_icon" />-->
<!--                      --><?php //endif; ?>
<!--                    </td>-->
                    <td class="text-center">
                      <span class="line1_text"><?=$row['rt_title']?></span>
                    </td>

                    <td class="text-center">
                      <span class="line1_text">
                       <?= $row['rt_start'] ?> ~ <?= $row['rt_end'] ?>
                      </span>
                    </td>

                    <td class="text-center">
                      <label class="switch switch-sm"><input type="checkbox" name="rt_show" <?=$row['rt_show']=="Y" ? "checked" : ""?> value="<?=$row['rt_show']?>"><span></span></label>
                    </td>
                    <td  class="text-center">
                        <span class="line1_text"><?= $row['rt_order'] ?></span>
                    </td>
                    <td  class="text-center">
                      <span class="line1_text"><?=DateType($row['created_at'], 4)?></span>
                    </td>



                </tr>
                <?php
                    $counts--;
                }
            } else {
                ?>
                <tr>
                    <td colspan="9" class="text-center"><b>자료가 없습니다.</b></td>
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

        $DB->where('idx', $idx);
        $DB->update($CFG_TBL['mainbanner']['default'], $arr_query);

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

  $DB->where('idx', $_POST['id']);
  $arr_query = array(
    'rt_show' => $ct_show
  );
  $result = $DB->update($CFG_TBL['mainbanner']['default'], $arr_query);

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

    $DB->where('idx', $_POST['ct_idx']);
    $row = $DB->getOne($CFG_TBL['mainbanner']['default']);

    if(!$row) {
      throw new Exception("데이터를 찾을 수 없습니다.");
    }

    $result = array();

    // 이미지 정보 처리
    for($i = 1; $i <= 5; $i++) {
      $img_key = "rt_img".$i;
      $result[$img_key] = array(
        'exists' => false,
        'url' => '',
        'filename' => ''
      );

      if(!empty($row[$img_key])) {
        $filepath = $ct_mainbanner_dir . $row[$img_key];
        if(file_exists($filepath)) {
          $result[$img_key] = array(
            'exists' => true,
            'url' => $ct_mainbanner_url . $row[$img_key],
            'filename' => $row[$img_key]
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
