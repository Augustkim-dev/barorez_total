<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";


$tbl_name = $CFG_TBL['qa']['default'];
$tbl_member_name = $CFG_TBL['member']['default'];

if ($_POST['act'] == "input") {
    header('Content-Type: application/json');

    try {
        // 필수 입력값 체크
        if(empty($_POST['mt_idx'])) {
          throw new Exception("작성자를 입력해주세요.");
        }
        if(empty($_POST['rt_title'])) {
          throw new Exception("제목을 입력해주세요.");
        }
        if(empty($_POST['rt_description'])) {
          throw new Exception("내용을 입력해주세요.");
        }


        // 트랜잭션 시작
        $DB->startTransaction();

        $max_order = $DB->getValue($tbl_name, "COALESCE(MAX(rt_order), 0) + 1");

        $DB->where('idx', $_POST['mt_idx']);
        $member = $DB->getOne($tbl_member_name);



        unset($arr_query);
        $arr_query = array(
          "mt_idx" => $member['idx'],
          "rt_title" => $_POST['rt_title'],
          "rt_description" => clean_xss_tags($_POST['rt_description']),
          "rt_response_text" => $_POST['rt_response_text'],
          "rt_status" => $_POST['rt_status'],
          "rt_show" => $_POST['rt_show'],
          "rt_order" => $max_order,
        );

        $_last_idx = $DB->insert($tbl_name, $arr_query);
        if(!$_last_idx) {
          throw new Exception("데이터 저장에 실패했습니다.");
        }

        // 파일 업로드 처리
        $uploaded_files = []; // 업로드된 파일 정보 저장 배열 추가

        if (!is_dir($ct_qa_dir)) {
          if (!mkdir($ct_qa_dir, 0707, true)) {
            throw new Exception("category 디렉토리 생성 실패");
          }
          chmod($ct_qa_dir, 0707);
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
              $filepath = $ct_qa_dir . $filename;

              if (move_uploaded_file($file['tmp_name'], $filepath)) {
                chmod($filepath, 0644);

                // 파일명을 DB에 업데이트
                $update_data = ["rt_img{$filePosition}" => $filename];
                $DB->where('idx', $_last_idx);
                if (!$DB->update($tbl_name, $update_data)) {
                  throw new Exception("파일 정보 업데이트 실패");
                }

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
        if(empty($_POST['rt_description'])) {
          throw new Exception("내용을 입력해주세요.");
        }

        // 트랜잭션 시작
        $DB->startTransaction();

        $DB->where('idx', $_POST['nt_idx']);
        $old_data = $DB->getOne($tbl_name);

        unset($arr_query);
        $arr_query = array(
          "rt_name" => $_SESSION['mng']['mt_name'],
          "rt_response_text" => $_POST['rt_response_text'],
          "rt_status" => $_POST['rt_status'],
          "updated_at" => $DB->now(),
        );

        $DB->where('idx', $_POST['nt_idx']);
        if(!$DB->update($tbl_name, $arr_query)) {
          throw new Exception("데이터 수정에 실패했습니다.");
        }

        $DB->commit();

        // 성공 응답
        echo json_encode([
            'success' => true,
            'message' => '수정되었습니다.',
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

        $DB->where('idx', $_POST['idx']);
        $row = $DB->getOne($tbl_name);

        if(!$row) {
            throw new Exception("삭제할 데이터가 존재하지 않습니다.");
        }

        unset($arr_query);
        $arr_query = array(
            "rt_show" => 'N',
            "del_date" => $DB->now(),
        );

        $DB->where('idx', $_POST['idx']);
        $DB->delete($tbl_name);

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

    $DB->join("member_t a2", "a1.mt_idx = a2.idx", "LEFT");

    // =========================
    // ① 검색어 필터
    // =========================
    if ($_POST['obj_search_txt']) {
        if ($_POST['obj_sel_search'] == "all") {
            $DB->where('( instr(a1.rt_title, \''.$_POST['obj_search_txt'].'\') 
                       or instr(a2.mt_id, \''.$_POST['obj_search_txt'].'\') 
                       or instr(a2.mt_name, \''.$_POST['obj_search_txt'].'\') 
                       or instr(a1.rt_name, \''.$_POST['obj_search_txt'].'\'))');
        }
        elseif($_POST['obj_sel_search'] == "name") {
            $DB->where('( instr(a2.mt_id, \''.$_POST['obj_search_txt'].'\') 
                       or instr(a2.mt_name, \''.$_POST['obj_search_txt'].'\'))');
        }
        elseif($_POST['obj_sel_search'] == "rt_name") {
            $DB->where('( instr(a1.rt_name, \''.$_POST['obj_search_txt'].'\'))');
        }
        elseif($_POST['obj_sel_search'] == "title") {
            $DB->where('( instr(a1.rt_title, \''.$_POST['obj_search_txt'].'\'))');
        }
        else {
            $DB->where('( instr('.$_POST['obj_sel_search'].', \''.$_POST['obj_search_txt'].'\') )');
        }
    }

    // =========================
    // ② 답변상태 필터 (버튼)
    //   - ''  : 전체
    //   - 'pending'  : 답변대기
    //   - 'answered' : 답변완료
    // =========================
    if (isset($_POST['obj_search_status']) && $_POST['obj_search_status'] !== '') {
        $DB->where('a1.rt_status', $_POST['obj_search_status']);
    }

    // =========================
    // ③ 등록일 빠른 선택 (오늘 / 7일 / 30일)
    // =========================
    if ($_POST['obj_search_day']) {
        $today = date('Y-m-d'); // 오늘 날짜 (시간 제외)

        if ($_POST['obj_search_day'] == 1) {
            // 오늘 00:00:00부터
            $DB->where('a1.created_at', $today . ' 00:00:00', '>=');

        } elseif ($_POST['obj_search_day'] == 7) {
            // 최근 7일
            $start_date = date('Y-m-d', strtotime('-7 days'));
            $DB->where('a1.created_at', $start_date . ' 00:00:00', '>=');

        } elseif ($_POST['obj_search_day'] == 30) {
            // 최근 30일
            $start_date = date('Y-m-d', strtotime('-30 days'));
            $DB->where('a1.created_at', $start_date . ' 00:00:00', '>=');
        }
    }

    // =========================
    // ④ 날짜 직접 선택 (검색 버튼 눌렀을 때만 들어옴)
    // =========================
    if (!empty($_POST['sdate']) && !empty($_POST['edate'])) {
        $start = $_POST['sdate'] . ' 00:00:00';
        $end   = $_POST['edate'] . ' 23:59:59';
        $DB->where('a1.created_at', [$start, $end], 'BETWEEN');
    }

    // (기존 rt_show 필터 유지)
    if (!empty($_POST['rt_show'])) {
        $DB->where('a1.rt_show', $_POST['rt_show']);
    }

    // 정렬
    if ($_POST['obj_order_desc_asc'] == '1') {
        $DB->orderBy("a1.rt_order", "asc");
    } else {
        $DB->orderBy("a1.rt_order", "asc");
    }

    $DB->where('a1.del_date', null, 'IS');

    $list = $DB->arraybuilder()->paginate($tbl_name." a1", $pg, '*, a1.idx as nt_idx');
    $debug = $DB->trace; // 모든 쿼리 정보 배열로 반환
//    print_r($debug);
////
//    print_r($DB->trace);
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
                    <th class="text-center" style="width:100px;">
                      아이디
                    </th>
                    <th class="text-center">
                      이름
                    </th>
                    <th class="text-center" style="width: 50%">
                      제목
                    </th>
                    <th class="text-center">
                      답변상태
                    </th>
                    <th class="text-center">
                      문의일시/답변일시
                    </th>
                    <th class="text-center" style="width:130px;">
                        관리
                    </th>
                </tr>
            </thead>
            <tbody>
            <?php
            if ($list) {
                foreach ($list as $row) { ?>
                <tr draggable="true" data-id="<?=$row['idx']?>">
<!--                    <td class="text-center checkbox-wrapper">-->
<!--                        <input type="checkbox" class="rowCheckbox custom-checkbox-list" />-->
<!--                    </td>-->
                    <td data-title="번호" class="text-center">
                        <?=$counts?>
                    </td>
                    <td class="text-center">
                        <span class="line1_text"><?=$row['mt_id']?></span>
                    </td>
                    <td class="text-center">
                        <span class="line1_text"><?=$row['mt_name']?></span>
                    </td>
                    <td class="text-center">
                      <span class="line1_text"><?=$row['rt_title']?></span>
                    </td>
                  <td class="text-center">
                    <span class="line1_text">
                    <?php
                    if($row['rt_status'] == 'pending'){
                      echo "답변대기";
                    } else if($row['rt_status'] == 'answered'){
                      echo "답변완료";
                    }
                    ?>
                    </span>
                  </td>
                    <td  class="text-center">
                     <span class="line1_text">문의일시: <?=DateType($row['created_at'], 4)?></span>
                        <?php if($row['rt_status'] == 'answered'){?>
                     <span class="line1_text">답변일시: <?=DateType($row['updated_at'], 4)?></span>
                        <?php }?>
                    </td>
                    <td data-title="관리" class="text-center">
                        <input type="button" class="btn btn-outline-info" value="상세" onclick="location.href='./form.php?act=update&nt_idx=<?=$row['nt_idx']?>'" />
                        <input type="button" class="btn btn-outline-danger" value="삭제" onclick="f_post_del('./update.php', '<?=$row['nt_idx']?>');" />
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
else if ($_POST['act'] === 'answer') {
    $qa_idx = (int)($_POST['idx'] ?? 0);
    $answer = trim($_POST['rt_response_text'] ?? '');

    if ($qa_idx <= 0) {
        echo json_encode([
            'success' => false,
            'message' => '잘못된 요청입니다.',
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // 답변자 이름 (프로젝트에 맞게 세션 키 조정)
    $adminName = $_SESSION['user']['mt_name'] ?? '관리자';

    $data = [
        'rt_response_text' => $answer,
        'updated_at'       => $DB->now(),
    ];

    // 답변이 있으면 answered, 없으면 pending 유지
    if ($answer !== '') {
        $data['rt_status'] = 'answered';
        $data['rt_name']   = $adminName;
    } else {
        $data['rt_status'] = 'pending';
        $data['rt_name']   = null;
    }

    $DB->where('idx', $qa_idx);
    $ok = $DB->update('qa_t', $data);

    if (!$ok) {
        echo json_encode([
            'success' => false,
            'message' => '답변 저장 중 오류가 발생했습니다: ' . $DB->getLastError(),
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    echo json_encode([
        'success' => true,
        'message' => '답변이 저장되었습니다.',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
