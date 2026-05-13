<?
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";

header('Content-Type: application/json');



if ($_POST['act']=="pwd") {


  try {
    // 필수 입력값 체크
    if(empty($_POST['mt_pwd'])) {
      throw new Exception("비밀번호를 입력해주세요.");
    }

    if(empty($_POST['mt_pwd_re'])) {
      throw new Exception("비밀번호확인을 입력해주세요.");
    }

    if (!empty($_POST['mt_pwd']) && !empty($_POST['mt_pwd_re'])) {
      if ($_POST['mt_pwd'] !== $_POST['mt_pwd_re']) {
        throw new Exception("비밀번호가 일치하지 않습니다.");
      }
    }


    $DB->where('mt_id', $_POST['mt_id']);
    $row = $DB->getOne('member_t', '*, idx as mt_idx');
    if (!$row) {
      throw new Exception("관리자정보가 존재하지 않습니다.");
    }



    // 트랜잭션 시작
    $DB->startTransaction();

    unset($arr_query);
    $arr_query = array(
      "mt_pwd" => password_hash($_POST['mt_pwd'], PASSWORD_DEFAULT),
    );


    if (!empty($arr_query)) {
      $DB->where('idx', $row['idx']);
      if (!$DB->update('member_t', $arr_query)) {
        throw new Exception("비밀번호 정보 업데이트 실패");
      }
    }


    $DB->commit();
    // 성공 응답
    echo json_encode([
      'success' => true,
      'message' => '저장 되었습니다.',
    ]);

  } catch (Exception $e) {
    $DB->rollback();
    echo json_encode([
      'success' => false,
      'message' => $e->getMessage()
    ]);
  }


}
else if ($_POST['act'] == "update") {

  try {
    // 필수 입력값 체크
    if (empty($_POST['mt_name'])) {
      throw new Exception("이름을 입력해주세요.");
    }
    if (empty($_POST['mt_hp'])) {
      throw new Exception("휴대폰 번호를 입력해주세요.");
    }
    if (empty($_POST['mt_email'])) {
      throw new Exception("이메일을 입력해주세요.");
    }

    $maxFiles = isset($_POST['maxFiles']) ? (int)$_POST['maxFiles'] : 1; // 기본값 1

    // 트랜잭션 시작
    $DB->startTransaction();

    // 기존 데이터 조회
    $DB->where('idx', $_POST['mt_idx']);
    $old_data = $DB->getOne('member_t');

    // 기본 데이터 준비
    $arr_query = array(
      "mt_position" => clean_xss_tags($_POST['mt_position']),
      "mt_name" => clean_xss_tags($_POST['mt_name']),
      "mt_hp" => clean_xss_tags($_POST['mt_hp']),
      "mt_email" => clean_xss_tags($_POST['mt_email']),
      "mt_add1" => clean_xss_tags($_POST['mt_add1']),
      "mt_add2" => clean_xss_tags($_POST['mt_add2']),
      "mt_zip" => clean_xss_tags($_POST['mt_zip']),
    );

    $finalImages = [];
    // 현재 이미지 상태 저장
    for ($i = 1; $i <= $maxFiles; $i++) {
      $finalImages["mt_image{$i}"] = !empty($old_data["mt_image{$i}"]) ? $old_data["mt_image{$i}"] : '';
    }

    // 삭제된 파일 처리
    if (!empty($_POST['removed_files'])) {
      $removedFiles = json_decode($_POST['removed_files'], true);
      foreach ($removedFiles as $fileNum) {
        if (!empty($finalImages[$fileNum])) {
          $old_file = $member_img_dir . $finalImages[$fileNum];
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
        $arr_query["mt_image{$pos}"] = $filename;
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
          $imageNum = str_replace('mt_image', '', $item['id']);
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
        $arr_query["mt_image{$pos}"] = $filename;
      }
    }

    // 회원 정보 업데이트 또는 입력
    if (!empty($_POST['mt_idx'])) {
      $DB->where('idx', $_POST['mt_idx']);
      if (!$DB->update('member_t', $arr_query)) {
        throw new Exception("회원정보 수정에 실패했습니다.");
      }
      $_last_idx = $_POST['mt_idx'];
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
          $filename = "mt_image_".$_POST['mt_idx'] . "_{$position}_{$timestamp}." . $file_ext;
          $filepath = $member_img_dir . $filename;

          // 기존 파일 삭제
          if (!empty($finalImages[$position])) {
            $old_file = $member_img_dir . $finalImages[$position];
            if(file_exists($old_file)) {
              unlink($old_file);
            }
          }

          if (move_uploaded_file($file['tmp_name'], $filepath)) {
            chmod($filepath, 0644);

            $update_data = ["mt_image{$position}" => $filename];
            $DB->where('idx', $_POST['mt_idx']);
            if (!$DB->update('member_t', $update_data)) {
              throw new Exception("파일 정보 업데이트 실패");
            }

            $finalImages[$position] = $filename;
          }
        }
      }
    }


    $DB->commit();


    // 최종 이미지 상태 조회
    $DB->where('idx', $_POST['mt_idx']);
    $final_data = $DB->getOne('member_t');
    $finalImages = array(
      1 => !empty($final_data['mt_image1']) ? $final_data['mt_image1'] : '',
    );

    $profile = $ct_no_profile_url;
    if(!empty($final_data['mt_image1'])) {
      $filepath = $member_img_dir . $final_data['mt_image1'];
      if(file_exists($filepath)) {
        $profile = $member_img_url . $final_data['mt_image1'];
      }
    }

    $_SESSION['mng']['mt_name'] = $final_data['mt_name'];
    $_SESSION['mng']['profile_url'] = $profile;


    echo json_encode([
      'success' => true,
      'message' => '저장 되었습니다',
      'redirect' => './admin_profile.php',
      //'final_images' => $finalImages,
      //'POST' => $_POST,
      //'FILES' => $_FILES,
      //'removedFiles' => $removedFiles,
      //'imageOrder' => $imageOrder,
    ]);

  } catch (Exception $e) {
    $DB->rollback();
    echo json_encode([
      'success' => false,
      'message' => $e->getMessage()
    ]);
  }

}
else if ($_POST['act'] == "loadimage") {

  try {
    if(empty($_POST['ct_idx'])) {
      throw new Exception("필수 파라미터가 누락되었습니다.");
    }

    $DB->where('idx', $_POST['ct_idx']);
    $row = $DB->getOne('member_t');

    if(!$row) {
      throw new Exception("데이터를 찾을 수 없습니다.");
    }

    $result = array();

    // 이미지 정보 처리
    for($i = 1; $i <= 2; $i++) {
      $img_key = "mt_image".$i;
      $result[$img_key] = array(
        'exists' => false,
        'url' => '',
        'filename' => ''
      );

      if(!empty($row[$img_key])) {
        $filepath = $member_img_dir . $row[$img_key];
        if(file_exists($filepath)) {
          $result[$img_key] = array(
            'exists' => true,
            'url' => $member_img_url . $row[$img_key],
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