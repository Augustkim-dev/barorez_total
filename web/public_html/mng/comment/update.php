<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";

if ($_POST['act'] == "delete") {

    header('Content-Type: application/json');
    try {
        // 트랜잭션 시작
        $DB->startTransaction();

        // 공지사항 정보 조회
        $DB->where('idx', $_POST['idx']);
        $row = $DB->getOne($CFG_TBL['board']['comment']);

        if(!$row) {
            throw new Exception("삭제할 데이터가 존재하지 않습니다.");
        }

        unset($arr_query);
        $arr_query = array(
            "del_date" => $DB->now(),
        );

        // 공지사항 삭제
        $DB->where('idx', $_POST['idx']);
        $DB->update($CFG_TBL['board']['comment'],$arr_query);

        $DB->commit();

        // 성공 응답
        echo json_encode([
            'success' => true,
            'message' => '삭제되었습니다.'
        ]);

    } catch (Exception $e) {
        $DB->rollback();
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

} elseif ($_POST['act'] == "list") {
    unset($list);
    $DB->pageLimit = $_POST['obj_limit_num'];
    $pg = $_POST['obj_pg'];

    $DB->join($CFG_TBL['member']['default']." a2", "a1.mt_idx = a2.idx", "LEFT");

    //검색
    if ($_POST['obj_search_txt']) {
        if ($_POST['obj_sel_search'] == "all") {
            $DB->where('( instr(a1.cmt_content, \''.$_POST['obj_search_txt'].'\') or instr(a1.mt_id, \''.$_POST['obj_search_txt'].'\') or instr(a1.mt_name, \''.$_POST['obj_search_txt'].'\') )');
        }else if ($_POST['obj_sel_search'] == "mt_id") {
            $DB->where('( instr(a1.mt_id, \''.$_POST['obj_search_txt'].'\'))');
        }else if ($_POST['obj_sel_search'] == "mt_name") {
            $DB->where('( instr(a1.mt_name, \''.$_POST['obj_search_txt'].'\'))');
        }else if ($_POST['obj_sel_search'] == "content") {
            $DB->where('( instr(a1.cmt_content, \''.$_POST['obj_search_txt'].'\'))');
        }else {
            $DB->where('( instr('.$_POST['obj_sel_search'].', \''.$_POST['obj_search_txt'].'\') )');
        }
    }

    if($_POST['obj_search_status'] != ''){
        if ($_POST['obj_search_status'] === '사용자') {
            $DB->where('mt_position', ['일반회원', '딜러회원'], 'IN');
        } else {
            $DB->where('mt_position', $_POST['obj_search_status']);
        }
    }

    if ($_POST['obj_search_day']) {
        $today = date('Y-m-d'); // 오늘 날짜 (시간 제외)

        if ($_POST['obj_search_day'] == '1') {
            // 오늘 00:00:00부터
            $DB->where('a1.cmt_wdate', $today . ' 00:00:00', '>=');

        } elseif ($_POST['obj_search_day'] == '2') {
            // 최근 7일
            $start_date = date('Y-m-d', strtotime('-7 days'));
            $DB->where('a1.cmt_wdate', $start_date . ' 00:00:00', '>=');

        } elseif ($_POST['obj_search_day'] == '3') {
            // 최근 30일
            $start_date = date('Y-m-d', strtotime('-30 days'));
            $DB->where('a1.cmt_wdate', $start_date . ' 00:00:00', '>=');
        }
    }

    if ($_POST['sdate'] && $_POST['edate']) {
        $start = $_POST['sdate'] . ' 00:00:00';
        $end = $_POST['edate'] . ' 23:59:59';

        $DB->where('a1.cmt_wdate', [$start, $end], 'BETWEEN');
    }

    if ($_POST['nt_show']) {
        $DB->where('a1.cmt_show', $_POST['nt_show']);
    }

    $DB->where('a1.del_date', null, 'IS');

    $DB->setTrace(true); // 쿼리 추적 활성화
    $list = $DB->arraybuilder()->paginate($CFG_TBL['board']['comment']." a1", $pg, '*, a1.idx as nt_idx');
    $debug = $DB->trace; // 모든 쿼리 정보 배열로 반환
    //print_r($debug);

    //페이징
    $n_page = $DB->totalPages;
    $counts = $DB->totalCount;
    $counts = $counts - (($pg - 1) * $_POST['obj_limit_num']);
    ?>
    <div class="table-responsive margin-top-20">
        <input type="hidden" value="<?php echo $counts; ?>" id="total" /> <!--  토탈 카운트 추가  -->
        <table class="table table-striped table-bordered margin-bottom-20" id="listTable" style="min-width: 800px">
            <thead class="thead-dark">
            <tr>
                <th class="text-center" style="width:80px;">
                    번호
                </th>
                <th class="text-center" style="width:120px;">
                    관리
                </th>
                <th class="text-center">
                    아이디
                </th>
                <th class="text-center">
                    이름
                </th>
                <th class="text-center">
                    회원 유형
                </th>
                <th class="text-center">
                    댓글 내용
                </th>
                <th class="text-center" style="width:130px;">
                    등록일시
                </th>
            </tr>
            </thead>
            <tbody>
            <?php
            if ($list) {
                foreach ($list as $row) {
                    ?>
                    <tr draggable="true" data-id="<?=$row['idx']?>">
                        <td data-title="번호" class="text-center">
                            <?=$counts?>
                        </td>
                        <td data-title="관리" class="text-center">
                            <input type="button" class="btn btn-outline-info btn-sm" value="상세" onclick="location.href='./form.php?act=update&nt_idx=<?=$row['nt_idx']?>'" />
                            <input type="button" class="btn btn-outline-danger btn-sm" value="삭제" onclick="f_post_del('./update.php', '<?=$row['nt_idx']?>');" />
                        </td>
                        <td data-title="아이디">
                            <span class="line1_text"><?=$row['mt_id']?></span>
                        </td>
                        <td data-title="이름">
                            <span class="line1_text"><?=$row['mt_name']?></span>
                        </td>
                        <td data-title="회원 유형">
                            <span class="line1_text"><?=$row['mt_position']?></span>
                        </td>
                        <td data-title="댓글 내용">
                            <span class="line1_text"><?=$row['cmt_content']?></span>
                        </td>
                        <td data-title="등록일시" class="text-center">
                            <?=DateType($row['cmt_wdate'], 6)?>
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


} else if($_POST['act'] == "updateSequence") {
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
                    'nt_order' => $sequence
                );

                $DB->where('idx', $idx);
                $DB->update($CFG_TBL['notice']['default'], $arr_query);

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

include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
