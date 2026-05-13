<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";


if ($_POST['act'] == "input" || $_POST['act'] == "update") {
    header('Content-Type: application/json');

    try {
        // 필수 입력값 체크
      if (empty($_POST['gmt_golf_name'])) {
        throw new Exception("골프장명을 입력해주세요.");
      }
      if (empty($_POST['gmt_local'])) {
        throw new Exception("지역을 선택헤주세요.");
      }
      if (empty($_POST['gmt_owdate'])) {
        throw new Exception("개장일 입력해주세요.");
      }
      if (empty($_POST['gmt_thum'])) {
        throw new Exception("썸네일을 입력해주세요.");
      }
      if (empty($_POST['gmt_hole'])) {
        throw new Exception("홀수를 입력해주세요.");
      }
      if (empty($_POST['gmt_person'])) {
        throw new Exception("회원수를 입력해주세요.");
      }
      if (empty($_POST['gmt_sale_price'])) {
        throw new Exception("분양가를 입력해주세요.");
      }
      if (empty($_POST['gmt_hp'])) {
        throw new Exception("전화번호를 입력해주세요.");
      }
      if (empty($_POST['gmt_zip']) || empty($_POST['gmt_add1']) || empty($_POST['gmt_add2'])) {
        throw new Exception("주소를 입력해주세요.");
      }
      if (empty($_POST['gmt_membership'])) {
        throw new Exception("회원구성을 입력해주세요.");
      }
      if (empty($_POST['gmt_benefit'])) {
        throw new Exception("회원혜택을 입력해주세요.");
      }
      if (empty($_POST['gmt_point'])) {
        throw new Exception("회원권특징을 입력해주세요.");
      }
      if (empty($_POST['gmt_temp'])) {
        throw new Exception("매매시 특이사항을 입력해주세요.");
      }
      if (empty($_POST['gmt_yeyaglyul'])) {
        throw new Exception("예약률을 입력해주세요.");
      }
      if (empty($_POST['gmt_document'])) {
        throw new Exception("준비서류를 입력해주세요.");
      }

      // 체크박스 유효성 검사
      if (empty($_POST['gmt_user_type']) || count($_POST['gmt_user_type']) == 0) {
        throw new Exception("회원권 종류를 1개 이상 선택해주세요.");
      }
      if (empty($_POST['gmt_reservation']) || count($_POST['gmt_reservation']) == 0) {
        throw new Exception("하나 이상의 예약율을 선택해주세요.");
      }

        // 트랜잭션 시작
        $DB->startTransaction();

        if($_POST['act'] == "update"){
          $DB->where('gmt_idx', $_POST['nt_idx']);
          $old_data = $DB->getOne($CFG_TBL['golf_membership']['main']);
          if(!$old_data) {
            throw new Exception("데이터가 없습니다.");
          }
        }

        $max_order = $DB->getValue($CFG_TBL['golf_membership']['main'], "COALESCE(MAX(gmt_order), 0) + 1");

        $gmt_lat = '';
        $gmt_lng = '';

        $result = getlanlng($_POST['gmt_add1']);
        if ($result['success'] && isset($result['data']['addresses'][0])) {
          $addrInfo = $result['data']['addresses'][0];
          $gmt_lat = $addrInfo['y']; // 위도
          $gmt_lng = $addrInfo['x']; // 경도
        } else {
          throw new Exception("주소를 가져오던 중 오류가 발생하였습니다.");
        }


        unset($arr_query);
        $arr_query = array(
          "gmt_golf_name"     => clean_xss_tags($_POST['gmt_golf_name']),
          "gmt_captain"       => isset($_POST['gmt_captain']) ? 'Y' : 'N',
          "gmt_url"           => clean_xss_tags($_POST['gmt_url']),
          "gmt_local"         => clean_xss_tags($_POST['gmt_local']),
          "gmt_owdate"        => clean_xss_tags($_POST['gmt_owdate']),
          "gmt_thum"          => clean_xss_tags($_POST['gmt_thum']),
          "gmt_hole"          => clean_xss_tags($_POST['gmt_hole']),
          "gmt_person"        => clean_xss_tags($_POST['gmt_person']),
          "gmt_sale_price"    => clean_xss_tags($_POST['gmt_sale_price']),
          "gmt_hp"            => clean_xss_tags($_POST['gmt_hp']),
          "gmt_zip"           => clean_xss_tags($_POST['gmt_zip']),
          "gmt_add1"          => clean_xss_tags($_POST['gmt_add1']),
          "gmt_add2"          => clean_xss_tags($_POST['gmt_add2']),
          "gmt_lat"           => $gmt_lat,
          "gmt_lng"           => $gmt_lng,
          "gmt_membership"    => clean_xss_tags($_POST['gmt_membership']),
          "gmt_benefit"       => clean_xss_tags($_POST['gmt_benefit']),
          "gmt_point"         => clean_xss_tags($_POST['gmt_point']),
          "gmt_temp"          => clean_xss_tags($_POST['gmt_temp']),
          "gmt_yeyaglyul"     => clean_xss_tags($_POST['gmt_yeyaglyul']),
          "gmt_document"      => clean_xss_tags($_POST['gmt_document']),
          "gmt_user_type"   => isset($_POST['gmt_user_type']) && is_array($_POST['gmt_user_type'])
            ? implode('|:|', $_POST['gmt_user_type'])
            : '',

          "gmt_reservation" => isset($_POST['gmt_reservation']) && is_array($_POST['gmt_reservation'])
            ? implode('|:|', $_POST['gmt_reservation'])
            : '',
        );


        if($_POST['act'] == "update"){
          $DB->where('gmt_idx', $old_data['gmt_idx']);
          if(!$DB->update($CFG_TBL['golf_membership']['main'], $arr_query)) {
            throw new Exception("데이터 변경에 실패했습니다.");
          }

          $_last_idx = $old_data['gmt_idx'];
        } else {
          $arr_query['gmt_order'] =  $max_order;
          $_last_idx = $DB->insert($CFG_TBL['golf_membership']['main'], $arr_query);
          if(!$_last_idx) {
            throw new Exception("데이터 저장에 실패했습니다.");
          }
        }


        $DB->where('gmt_idx', $_last_idx);
        $DB->delete($CFG_TBL['golf_membership']['greenfee']);
        if (isset($_POST['gmgf_name']) && is_array($_POST['gmgf_name'])) {
          foreach ($_POST['gmgf_name']  as $i => $name) {
            $weekday = $_POST['gmgf_weekday'][$i];
            $weekend = $_POST['gmgf_weekend'][$i];

            if($name != '' && $weekday != '' && $weekend != ''){
              $DB->insert($CFG_TBL['golf_membership']['greenfee'], [
                "gmt_idx"     => $_last_idx,
                "gmgf_num" => $i+1,
                "gmgf_name" => $name,
                "gmgf_weekday" => $weekday,
                "gmgf_weekend" => $weekend,
                "gmgf_wdate" => date("Y-m-d H:i:s", time()),
              ]);

            }


          }
        }

        $DB->where('gmt_idx', $_last_idx);
        $DB->delete($CFG_TBL['golf_membership']['myeong']);
        if (isset($_POST['gmmf_name']) && is_array($_POST['gmmf_name'])) {
          foreach ($_POST['gmmf_name'] as $i => $name) {
            $price = $_POST['gmmf_price'][$i];
            $info = $_POST['gmmt_info'][$i];

            if($name != '' && $price != '' && $info != ''){
              $DB->insert($CFG_TBL['golf_membership']['myeong'], [
                "gmt_idx"     => $_last_idx,
                "gmmf_num" => $i+1,
                "gmgf_name" => $name,
                "gmmf_price" => $price,
                "gmmt_info" => $info,
                "gmmf_wdate" => date("Y-m-d H:i:s", time()),
              ]);
            }

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
            $filepath = $ct_certi_membership_dir . $file['ft_file'];
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
            $filename = "golf_".$_last_idx . "_{$position}_{$timestamp}." . $file_ext;
            $filepath = $ct_certi_membership_dir . $filename;


            if (move_uploaded_file($file['tmp_name'], $filepath)) {
              chmod($filepath, 0644);

              $insert_data = [
                "ft_pidx" => $_last_idx,
                "ft_type" => 1,
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



      if($_POST['act'] == "input") {
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

        $DB->where('gmat_idx', $_POST['idx']);
        $row = $DB->getOne($CFG_TBL['golf_membership']['auth']);

        if(!$row) {
            throw new Exception("삭제할 데이터가 존재하지 않습니다.");
        }

        $DB->where('gmat_idx', $_POST['idx']);

        $arr_query = ['gmat_del' =>'Y'];
        $DB->update($CFG_TBL['golf_membership']['auth'], $arr_query);

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

    $search_txt = trim($_POST['obj_search_txt'] ?? '');
    $search_col = $_POST['obj_sel_search'] ?? '';

    if ($search_txt !== '') {
        if ($search_col == "all") {
          $DB->where("( INSTR(a2.gmt_golf_name, ?) )", [$search_txt]);
        } else {
          $DB->where("( INSTR({$search_col}, ?) )", [$search_txt]);
        }
    }


    if ($_POST['gmt_show']) {
        $DB->where('a2.gmt_show', $_POST['gmt_show']);
    }

    $DB->where('a1.gmat_del', 'N');
    $DB->where('a2.gmt_del', 'N');

    //정렬
    $DB->orderBy("a1.gmat_idx", "desc");

    $select = "
      a1.*, 
      a2.gmt_golf_name,
      a1.gmat_idx as nt_idx
    ";

    $table = "{$CFG_TBL['golf_membership']['auth']} a1 
          LEFT JOIN {$CFG_TBL['golf_membership']['main']} a2 
          ON a1.gmt_idx = a2.gmt_idx";

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
                    <th class="text-center" style="width:50px;">
                        <input type="checkbox" id="selectAll" class="custom-checkbox-list" />
                    </th>
                    <th class="text-center" style="width:80px;">
                        번호
                    </th>
                    <th class="text-center">
                        관리
                    </th>
                    <th class="text-center">
                      골프장
                    </th>
                    <th class="text-center">
                      회원구분
                    </th>
                    <th class="text-center">
                      이름/법인명
                    </th>
                    <th class="text-center">
                      주민번호/사업자번호
                    </th>
                    <th class="text-center">
                      회원권번호
                    </th>
                    <th class="text-center">
                      상태
                    </th>
                    <th class="text-center">
                      요청일
                    </th>

                </tr>
            </thead>
            <tbody>
            <?php
            if ($list) {
                foreach ($list as $row) {
            ?>
                <tr draggable="false" data-id="<?=$row['nt_idx']?>">
                    <td class="text-center checkbox-wrapper">
                        <input type="checkbox" name="row_check[]" value="<?=$row['nt_idx']?>"  class="rowCheckbox custom-checkbox-list" />
                    </td>
                    <td data-title="번호" class="text-center">
                        <?=$counts?>
                    </td>
                    <td data-title="관리" class="text-center">
                        <input type="button" class="btn btn-outline-info btn-sm" value="상세" onclick="location.href='./form.php?act=view&nt_idx=<?=$row['nt_idx']?>'" />
                        <input type="button" class="btn btn-outline-danger btn-sm" value="삭제" onclick="f_post_del('./update.php', '<?=$row['nt_idx']?>');" />
                    </td>

                    <td class="text-center">
                      <span class="line1_text"><?=$row['gmt_golf_name']?></span>
                    </td>
                    <td class="text-center">
                        <span class="line1_text"><?=$arr_gmat_type[$row['gmat_type']]?></span>
                    </td>
                    <td class="text-center">
                      <span class="line1_text"><?=$row['gmat_name']?></span>
                    </td>
                    <td class="text-center">
                      <span class="line1_text"><?=$row['gmat_num']?></span>
                    </td>
                    <td class="text-center">
                      <span class="line1_text"><?=$row['gmat_membership_num']?></span>
                    </td>
                    <td class="text-center">
                      <span class="line1_text"><?=$arr_gmat_status[$row['gmat_status']]?></span>
                    </td>
                    <td class="text-center">
                      <span class="line1_text"><?=DateType($row['gmat_wdate'], 6)?></span>
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
else if ($_POST['act'] == "loadimage") {

  try {
    if(empty($_POST['ct_idx'])) {
      throw new Exception("필수 파라미터가 누락되었습니다.");
    }

    $DB->where('ft_pidx', $_POST['ct_idx']);
    $DB->where('ft_type', 2);
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
        $filepath = $ct_certi_membership_dir . $files[$real_key]['ft_file'];
        if(file_exists($filepath)) {
          $result[$img_key] = array(
            'exists' => true,
            'url' => $ct_certi_membership_url . $files[$real_key]['ft_file'],
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
else if ($_POST['act'] == 'auth_status_change') {
  try {
    if (empty($_POST['gmat_idx']) || !is_array($_POST['gmat_idx'])) {
      throw new Exception('선택된 항목이 없습니다.');
    }
    $status = (int)$_POST['gmat_status'];
    $idx_list = $_POST['gmat_idx'];

    $push_txt = ($status == 2) ? '등록된 회원권이 인증되었습니다.' : '등록된 회원권이 반려되었습니다.';


    foreach ($idx_list as $idx) {

      $DB->where('gmat_idx', $idx);
      $row = $DB->getOne($CFG_TBL['golf_membership']['auth']);
      if($row){

        $DB->where('gmat_idx', $idx);
        unset($arr_query);
        $arr_query = [
          'gmat_status' => $status,
          'gmat_sdate' => date('Y-m-d H:i:s', time()),
        ];
        if($DB->update($CFG_TBL['golf_membership']['auth'], $arr_query)) {

          $DB->where('idx', $row['mt_idx']);
          $DB->where('mt_level', '2');
          $DB->where('mt_push', 'Y');
          $member = $DB->getOne($CFG_TBL['member']['default']);

          unset($arr_query);
          $arr_query = [
            'mt_idx' => $member['idx'],
            'pt_type' => 2,
            'pt_pidx' => $row['gmt_idx'],
            'pt_sidx' => $row['gmat_idx'],
            'pt_ptype' => 4,
            'pt_title' => $push_txt,
          ];
          $_last_idx = $DB->insert($CFG_TBL['push']['default'], $arr_query);
          if(!$_last_idx) {
            throw new Exception("데이터 저장에 실패했습니다.");
          }

          $param = [
            'mt_idx' => $member['idx'],
            'body' => $row['gmt_golf_name'],
            'title' => $push_txt,
          ];


        }

      }

    }

    echo json_encode([
      'success' => true,
      'message' => '정상적으로 처리되었습니다.'
    ]);
  } catch (Exception $e) {
    echo json_encode([
      'success' => false,
      'message' => $e->getMessage(),
      'post' => $_POST
    ]);
  }
  exit;
}






include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
