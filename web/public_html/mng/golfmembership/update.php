<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\IOFactory;

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
            $filepath = $ct_golf_membership_dir . $file['ft_file'];
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
            $filepath = $ct_golf_membership_dir . $filename;


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

      if($_POST['act'] == "update") {
        if($_POST['gmt_golf_name'] != $old_data['gmt_golf_name']) {

          unset($arr_query);
          $arr_query = array(
            "gmt_golf_name"     => $_POST['gmt_golf_name'],
          );

          $DB->where('gmt_idx', $old_data['gmt_idx']);
          if(!$DB->update($CFG_TBL['golf_membership']['auth'], $arr_query)) {
            throw new Exception("골프장명 변경에 실패했습니다.");
          }

          $DB->where('gmt_idx', $old_data['gmt_idx']);
          if(!$DB->update($CFG_TBL['golf_membership']['transaction'], $arr_query)) {
            throw new Exception("골프장명 변경에 실패했습니다.");
          }

          $DB->where('gmt_idx', $old_data['gmt_idx']);
          if(!$DB->update($CFG_TBL['join']['default'], $arr_query)) {
            throw new Exception("골프장명 변경에 실패했습니다.");
          }

          $DB->where('gmt_idx', $old_data['gmt_idx']);
          if(!$DB->update($CFG_TBL['review']['default'], $arr_query)) {
            throw new Exception("골프장명 변경에 실패했습니다.");
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

else if ($_POST['act'] == "copy") {
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


    $DB->where('gmt_idx', $_POST['nt_idx']);
    $old_data = $DB->getOne($CFG_TBL['golf_membership']['main']);
    if(!$old_data) {
      throw new Exception("원본 데이터가 없습니다.");
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


    $arr_query['gmt_order'] =  $max_order;
    $_last_idx = $DB->insert($CFG_TBL['golf_membership']['main'], $arr_query);
    if(!$_last_idx) {
      throw new Exception("데이터 저장에 실패했습니다.");
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


    $old_idx = $old_data['gmt_idx'];   // 원본 글 ID
    $new_idx = $_last_idx;              // 새로 복사한 글 ID

    $maxFiles = isset($_POST['maxFiles']) ? (int)$_POST['maxFiles'] : 1; // 기본값 1
    // 원본 파일 개수 조회
    $DB->where('ft_pidx', $old_data['gmt_idx']);
    $existing_files = $DB->get($CFG_TBL['file']['default']);

    // 파일복사
    $position = 1;
    foreach ($existing_files as $file) {
      $old_filename = $file['ft_file'];
      $old_path = $ct_golf_membership_dir . $old_filename;

      // 새 파일명 생성
      $file_ext = pathinfo($old_filename, PATHINFO_EXTENSION);
      $timestamp = time();
      $new_filename = "golf_" . $new_idx . "_{$position}_{$timestamp}." . $file_ext;
      $new_path = $ct_golf_membership_dir . $new_filename;

      // 파일 복사
      if (file_exists($old_path)) {
        if (!copy($old_path, $new_path)) {
          throw new Exception("이미지 복사 실패: $old_filename");
        }
        chmod($new_path, 0644);
      } else {
        continue; // 파일이 없으면 무시
      }

      // DB INSERT (ft_idx 제외, 새 파일로)
      $insert_data = [
        'ft_pidx'       => $new_idx,
        'ft_type'       => $file['ft_type'],
        'ft_file'       => $new_filename,
        'ft_file_ori'   => $file['ft_file_ori'],
        'ft_file_size'  => $file['ft_file_size'],
      ];

      if (!$DB->insert($CFG_TBL['file']['default'], $insert_data)) {
        throw new Exception("파일 정보 복사 실패: {$file['ft_file']}");
      }
      $position++;
    }


    $DB->commit();

    // 성공 응답
    echo json_encode([
      'success' => true,
      'message' => '복사 되었습니다.',
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

        $DB->where('gmt_idx', $_POST['idx']);
        $row = $DB->getOne($CFG_TBL['golf_membership']['main']);

        if(!$row) {
            throw new Exception("삭제할 데이터가 존재하지 않습니다.");
        }

        $DB->where('gmt_idx', $_POST['idx']);

        $arr_query = ['gmt_del' =>'Y'];
        $DB->update($CFG_TBL['golf_membership']['main'], $arr_query);

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
        if ($_POST['obj_sel_search'] == "all") {
            $DB->where('( instr(a1.gmt_golf_name, \''.$_POST['obj_search_txt'].'\'))');
        } else {
            $DB->where('( instr('.$_POST['obj_sel_search'].', \''.$_POST['obj_search_txt'].'\') )');
        }
    }

    if ($_POST['obj_search_local'] != '') {
      $DB->where('a1.gmt_local', $_POST['obj_search_local']);
    }

    if ($_POST['gmt_show']) {
        $DB->where('a1.gmt_show', $_POST['gmt_show']);
    }


    $DB->where('a1.gmt_del', 'N');

    //정렬

    $DB->orderBy("a1.gmt_golf_name", "asc");
    //$DB->orderBy("a1.gmt_idx", "desc");

    $select = "
      a1.*, 
      a1.gmt_idx AS nt_idx,
    
      (SELECT COUNT(*) 
       FROM {$CFG_TBL['golf_membership']['transaction']} 
       WHERE gmt_idx = a1.gmt_idx AND gmtt_status = 1 AND gmtt_del = 'N' AND gmtt_show = 'Y') AS gmtt_type1,
    
      (SELECT COUNT(*) 
       FROM {$CFG_TBL['golf_membership']['transaction']} 
       WHERE gmt_idx = a1.gmt_idx AND gmtt_status = 2 AND gmtt_del = 'N' AND gmtt_show = 'Y') AS gmtt_type2,
    
      (SELECT COUNT(*) 
       FROM {$CFG_TBL['golf_membership']['transaction']} 
       WHERE gmt_idx = a1.gmt_idx AND gmtt_status = 3 AND gmtt_del = 'N' AND gmtt_show = 'Y') AS gmtt_type3
    ";

    $DB->setTrace(true); // 쿼리 추적 활성화
    $list = $DB->arraybuilder()->paginate($CFG_TBL['golf_membership']['main']." a1", $pg,  $select);
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
                      지역
                    </th>
                    <th class="text-center">
                      골프장명
                    </th>
                    <th class="text-center">
                      분양가
                    </th>
                    <th class="text-center">
                      즉시매도가
                    </th>
                    <th class="text-center">
                      즉시매입가
                    </th>
                    <th class="text-center">
                      체결가능금액
                    </th>
                    <th class="text-center">
                      체결대기
                    </th>
                    <th class="text-center">
                      체결진행
                    </th>
                    <th class="text-center">
                      체결완료
                    </th>
                    <th class="text-center">
                      노출여부
                    </th>
                    <th class="text-center">
                      회원권등록
                    </th>

                </tr>
            </thead>
            <tbody>
            <?php
            if ($list) {
                foreach ($list as $row) {
                  $prices = getGolfPrice($row['gmt_idx']);
            ?>
                <tr draggable="true" data-id="<?=$row['nt_idx']?>">
                    <td class="text-center checkbox-wrapper">
                        <input type="checkbox" class="rowCheckbox custom-checkbox-list" />
                    </td>
                    <td data-title="번호" class="text-center">
                        <?=$counts?>
                    </td>
                    <td data-title="관리" class="text-center">
                        <input type="button" class="btn btn-outline-info btn-sm" value="수정" onclick="location.href='./form.php?act=update&nt_idx=<?=$row['nt_idx']?>'" />
                        <input type="button" class="btn btn-outline-secondary btn-sm" value="복사" onclick="location.href='./form.php?act=copy&nt_idx=<?=$row['nt_idx']?>'" />
                        <input type="button" class="btn btn-outline-danger btn-sm" value="삭제" onclick="f_post_del('./update.php', '<?=$row['nt_idx']?>');" />
                    </td>

                    <td class="text-center">
                      <span class="line1_text"><?=$arr_gmt_local_type[$row['gmt_local']]?></span>
                    </td>
                    <td class="text-center">
                        <span class="line1_text"><?=$row['gmt_golf_name']?></span>
                    </td>
                    <td class="text-center">
                      <?= is_numeric($row['gmt_sale_price']) ? number_format($row['gmt_sale_price']) : $row['gmt_sale_price'] ?>
                    </td>
                    <td class="text-center">
                      <?= is_numeric($prices['gmt_now_buy_price']) ? number_format($prices['gmt_now_buy_price']) : $prices['gmt_now_buy_price'] ?>
                    </td>
                    <td class="text-center">
                      <?= is_numeric($prices['gmt_now_sale_price']) ? number_format($prices['gmt_now_sale_price']) : $prices['gmt_now_sale_price'] ?>
                    </td>
                    <td class="text-center">
                      <?= is_numeric($prices['gmt_conclusion_price']) ? number_format($prices['gmt_conclusion_price']) : $prices['gmt_conclusion_price'] ?>
                    </td>
                    <td class="text-center">
                      <span class="line1_text"><?=$row['gmtt_type1']?></span>
                    </td>
                    <td class="text-center">
                      <span class="line1_text"><?=$row['gmtt_type2']?></span>
                    </td>
                    <td class="text-center">
                      <span class="line1_text"><?=$row['gmtt_type3']?></span>
                    </td>
                    <td class="text-center">
                      <label class="switch switch-sm"><input type="checkbox" name="gmt_show" <?=$row['gmt_show']=="Y" ? "checked" : ""?> value="<?=$row['gmt_show']?>"><span></span></label>
                    </td>
                    <td class="text-center">

                      <input type="button" class="btn btn-outline-info btn-sm" value="구매등록" onclick="location.href='../buysellmanagement/form_buy.php?gmt_idx=<?=$row['nt_idx']?>'" />
                      <input type="button" class="btn btn-outline-secondary btn-sm" value="판매등록" onclick="location.href='../buysellmanagement/form_sell.php?gmt_idx=<?=$row['nt_idx']?>'" />

                    </td>




                </tr>
                <?php
                    $counts--;
                }
            } else {
                ?>
                <tr>
                    <td colspan="15" class="text-center"><b>자료가 없습니다.</b></td>
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
          'gmt_order' => $sequence
        );

        $DB->where('idx', $idx);
        $DB->update($CFG_TBL['golf_membership']['main'], $arr_query);

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
  $ct_show = $_POST['gmt_show'];

  $DB->where('gmt_idx', $_POST['id']);
  $arr_query = array(
    'gmt_show' => $ct_show
  );
  $result = $DB->update($CFG_TBL['golf_membership']['main'], $arr_query);

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
    $files = $DB->get($CFG_TBL['file']['default']);

    if(!$files) {
      throw new Exception("불러올 파일데이터가 없습니다.");
    }

    $result = array();

    // 이미지 정보 처리
    for($i = 1; $i <= 10; $i++) {
      $real_key = $i - 1;
      $img_key = $files[$real_key]['ft_idx'];
      $result[$img_key] = array(
        'exists' => false,
        'url' => '',
        'filename' => ''
      );

      if(!empty($files[$real_key])) {
        $filepath = $ct_golf_membership_dir . $files[$real_key]['ft_file'];
        if(file_exists($filepath)) {
          $result[$img_key] = array(
            'exists' => true,
            'url' => $ct_golf_membership_url . $files[$real_key]['ft_file'],
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
else if ($_POST['act'] == "onPush") {

  try {
    if(empty($_POST['gmt_idx'])) {
      throw new Exception("필수 파라미터가 누락되었습니다.");
    }

    $DB->where('gmt_idx', $_POST['gmt_idx']);
    $DB->where('gmt_show', 'Y');
    $DB->where('gmt_del', 'N');
    $row = $DB->getOne($CFG_TBL['golf_membership']['main']);

    if(!$row) {
      throw new Exception("해당 회원권 정보를 찾을 수 없습니다.");
    }

    $where = ['gmt_idx'=>$row['gmt_idx']];
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
          'pt_pidx' => $row['gmt_idx'],
          'pt_sidx' => null,
          'pt_ptype' => 8,
          'pt_title' => '회원권 상세가 변경되었습니다.',
        ];
        $_last_idx = $DB->insert($CFG_TBL['push']['default'], $arr_query);
        if(!$_last_idx) {
          throw new Exception("데이터 저장에 실패했습니다.");
        }

        $param = [
          'mt_idx' => $member['idx'],
          'body' => $row['gmt_golf_name'],
          'title' => '회원권 정보가 변경되었습니다.',
        ];



      }
    }


    echo json_encode(array(
      'success' => true,
      'data' => $row
    ));

  } catch (Exception $e) {
    echo json_encode(array(
      'success' => false,
      'message' => $e->getMessage()
    ));
  }
  exit;

}
else if ($_POST['act'] == "excel_upload") {

  try {
    if (
      !isset($_FILES['excel_file']) ||
      $_FILES['excel_file']['error'] !== UPLOAD_ERR_OK
    ) {
      throw new Exception("엑셀 파일 업로드에 실패했습니다.");
    }

    $file = $_FILES['excel_file'];
    $tmpFile = $file['tmp_name'];
    $fileName = $file['name'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // ✅ 확장자 검사
    if (!in_array($fileExt, ['xls', 'xlsx'])) {
      throw new Exception("지원하지 않는 파일 형식입니다. (허용: xls, xlsx)");
    }

    // PhpSpreadsheet reader 자동 감지
    $spreadsheet = IOFactory::load($tmpFile);
    $sheet = $spreadsheet->getActiveSheet();
    $data_rows = $sheet->toArray(null, true, true, true);

    $header = $data_rows[1]; // A1, B1, C1...
    unset($data_rows[1]);

    $return_arr = [];
    $success_count = 0;
    $all_count = 0;
    $today = date('Y-m-d H:i:s');

    $header_map = [
      '썸네일'       => 'gmt_thum',
      '지역'         => 'gmt_local',
      '개장일'       => 'gmt_owdate',
      '골프장명'     => 'gmt_golf_name',
      '대표회원권'   => 'gmt_captain',
      '홈페이지 주소' => 'gmt_url',
      '우편번호'     => 'gmt_zip',
      '주소'         => 'gmt_add1',
      '상세주소'     => 'gmt_add2',
      '홀수'         => 'gmt_hole',
      '회원수'       => 'gmt_person',
      '분양가'       => 'gmt_sale_price',
      '전화번호'     => 'gmt_hp',
      '회원구성'     => 'gmt_membership',
      '회원혜택'     => 'gmt_benefit',
      '회원특징'     => 'gmt_point',
      '매매시특이사항'=> 'gmt_temp',
      '예약률'       => 'gmt_yeyaglyul',
      '회원예약률'   => 'gmt_reservation',
      '준비서류'     => 'gmt_document',
      '회원권종류'   => 'gmt_user_type',
    ];

    $columns = [];
    foreach ($header as $col => $korean) {
      if (isset($header_map[$korean])) {
        $columns[$col] = $header_map[$korean];
      }
    }

    foreach ($data_rows as $i => $row) {
      $insert_data = [];
      foreach ($columns as $col => $field_name) {
        $insert_data[$field_name] = trim($row[$col]);
      }

      // 필수값 체크
      foreach (['gmt_thum', 'gmt_local', 'gmt_owdate', 'gmt_golf_name', 'gmt_captain', 'gmt_add1', 'gmt_add2', 'gmt_hole', 'gmt_person', 'gmt_sale_price', 'gmt_hp', 'gmt_membership', 'gmt_benefit', 'gmt_point', 'gmt_temp', 'gmt_yeyaglyul', 'gmt_reservation', 'gmt_document', 'gmt_user_type'] as $required_field) {
        if (empty($insert_data[$required_field])) {
          $return_arr[] = "{$i}번째: {$required_field} 값 누락";
          continue 2;
        }
      }

      // 날짜 형식 검증
      if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/', $insert_data['gmt_owdate'])) {
        $return_arr[] = "{$i}번째: 개장일 형식 오류";
        continue;
      }

      // 위경도 구하기
      $gmt_lat = '';
      $gmt_lng = '';
      $result = getlanlng($insert_data['gmt_add1']);
      if ($result['success'] && isset($result['data']['addresses'][0])) {
        $addrInfo = $result['data']['addresses'][0];
        $gmt_lat = $addrInfo['y']; // 위도
        $gmt_lng = $addrInfo['x']; // 경도
      }

      // 기본값 추가
      $insert_data['gmt_lat'] = $gmt_lat;
      $insert_data['gmt_lng'] = $gmt_lng;
      $insert_data['gmt_show'] = 'Y';
      $insert_data['gmt_del'] = 'N';
      $insert_data['gmt_wdate'] = $today;


      $multi_fields = ['gmt_reservation', 'gmt_user_type'];
      foreach ($multi_fields as $field) {
        if (!empty($insert_data[$field])) {
          $insert_data[$field] = implode('|:|', array_map('trim', explode(',', $insert_data[$field])));
        }
      }



      $result = $DB->insert($CFG_TBL['golf_membership']['main'], $insert_data);
      if ($result) $success_count++;
      else $return_arr[] = "{$i}번째: DB insert 실패";

      $all_count++;
    }


    echo json_encode(array(
      'success' => true,
      'message' => "총 {$all_count}개 중 {$success_count}개 등록 완료",
      'errors' => $return_arr

    ));

  }  catch (Exception $e) {
    echo json_encode(array(
      'success' => false,
      'message' => $e->getMessage()
    ));
  }
  exit;

}




include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
