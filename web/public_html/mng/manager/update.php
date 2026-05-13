<?
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";

// 성공 응답
$redirect_url = 'list.php';

if ($_POST['act']=='input') {
    header('Content-Type: application/json');

    try {
        // 필수 입력값 체크
        if(empty($_POST['mt_id'])) {
            throw new Exception("아이디를 입력해주세요.");
        }
        if(empty($_POST['mt_name'])) {
            throw new Exception("이름을 입력해주세요.");
        }
        if(empty($_POST['mt_hp'])) {
            throw new Exception("휴대폰 번호를 입력해주세요.");
        }

        // 트랜잭션 시작
        $DB->startTransaction();

        unset($arr_query);
        $arr_query = array(
            "mt_type" => 1,
            "mt_level" => $_POST['mt_level'],
            "mt_id" => $_POST['mt_id'],
            "mt_pwd" => password_hash($_POST['mt_pwd'], PASSWORD_DEFAULT),
            "mt_name" => $_POST['mt_name'],
            "mt_nickname" => $_POST['mt_nickname'] ? $_POST['mt_nickname'] : $_POST['mt_name'],
            "mt_nickname_date" => $DB->now(),
            "mt_hp" => format_phone($_POST['mt_hp']),
            "mt_status" => "Y",
            "mt_mng" => "Y",
            "mt_position" => $_POST['mt_level'] === '8' ? '중간관리자' : '폴리스관리자',
            'mt_wdate' => $DB->now(),
        );

        $_mt_last_idx = $DB->insert('member_t', $arr_query);


        $DB->commit();

        // 성공 응답
        echo json_encode([
            'success' => true,
            'message' => '저장되었습니다.',
            'mt_idx' => $_mt_last_idx,
            'uploaded_files' => '',
            'redirect' => $redirect_url,
        ]);

    } catch (Exception $e) {
        $DB->rollback();
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

}
else if ($_POST['act'] == "chk_mt_id"){
    header('Content-Type: application/json');
    try {

    // 트랜잭션 시작
    $DB->startTransaction();
    $DB->where('mt_id', $_POST['mt_id']);
    $id = $DB->getValue('member_t', 'idx');

    if($id){
        $msg = 'N';
    }else{
        $msg = 'Y';
    }
    // 성공 응답
    echo json_encode([
        'success' => true,
        'message' => $msg,
        'uploaded_files' => '',
    ]);
    $DB->commit();
    } catch (Exception $e) {
        $DB->rollback();
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage(),
            'data' => $_POST
        ]);
    }

}
else if ($_POST['act'] == "update") {

    header('Content-Type: application/json');

    try {
        // 필수 입력값 체크
        if (empty($_POST['mt_id'])) {
            throw new Exception("아이디를 입력해주세요.");
        }
        if (empty($_POST['mt_name'])) {
            throw new Exception("이름을 입력해주세요.");
        }
        if (empty($_POST['mt_hp'])) {
            throw new Exception("휴대폰 번호를 입력해주세요.");
        }

        // 트랜잭션 시작obj_search_status
        $DB->startTransaction();

        // 기본 데이터 준비
        $arr_query = array(
            "mt_name" => clean_xss_tags($_POST['mt_name']),
            "mt_hp" => clean_xss_tags($_POST['mt_hp']),
            "mt_status" => 'Y',
            "mt_appr" => $_POST['mt_appr'],
            'mt_ldate' => $DB->now(),
        );

        // 비밀번호 처리
        if (!empty($_POST['mt_pwd']) && !empty($_POST['mt_pwd_re'])) {
            if ($_POST['mt_pwd'] !== $_POST['mt_pwd_re']) {
                throw new Exception("비밀번호가 일치하지 않습니다.");
            }
            $arr_query['mt_pwd'] = password_hash($_POST['mt_pwd'], PASSWORD_DEFAULT);
        }

        // 회원 정보 업데이트 또는 입력
        if (!empty($_POST['mt_idx'])) {
            $DB->where('idx', $_POST['mt_idx']);
            if (!$DB->update('member_t', $arr_query)) {
                throw new Exception("회원정보 수정에 실패했습니다.");
            }
            $_last_idx = $_POST['mt_idx'];
        }

        $DB->commit();

        echo json_encode([
            'success' => true,
            'message' => '수정되었습니다',
            'mt_idx' => $_last_idx,
            'redirect' => $redirect_url,
        ]);

    } catch (Exception $e) {
        $DB->rollback();
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}
else if ($_POST['act']=="retire") {

    unset($arr_query);
    $arr_query = array(
        "del_status" => 'Y',
        "mt_level" => '1',
        "mt_status" => 'N',
        "mt_retire_memo" => "관리자 권한 회원삭제 처리",
        'mt_rdate' => $DB->now(),
    );
    $DB->where('idx', $_POST['mt_idx_t']);
    $DB->update('member_t', $arr_query);

    echo "Y";

}
else if($_POST['act'] == "list") {

    $rows = $_POST['rows'] ? $_POST['rows'] : $n_limit_num;

    $_colspan_txt = 8;

    unset($list);
    $DB->pageLimit = $rows;
    $pg = $_POST['obj_pg'];

    $type = $_POST['type'] ?? '';

    /* ===========================
     *  공통 기본 조건
     * =========================== */

    if ($type === 'secession') {
        // 🔹 가맹점주 탈퇴 회원
        $DB->where('a1.mt_level', 1);
        // mt_appr 값이 존재하는 경우만 (가맹점주였던 사람)
        $DB->where('a1.mt_appr', 'D');
    } elseif ($type === 'approval') {
        // 🔹 가맹점주 승인 관리
        $DB->where('a1.mt_appr', 'Y','!=');
        $DB->where('a1.mt_level', 5);

        // 회원 상태(승인요청/승인/거부 등) => mt_appr
        if (!empty($_POST['obj_search_status'])) {
            $DB->where('a1.mt_appr', $_POST['obj_search_status']);
        }
    } else {
        // 🔹 기본: 가맹점주 회원 (승인 완료된 가맹점주만)
        $DB->where('a1.mt_level', 5);
        $DB->where('a1.mt_appr', ['Y','T'],'IN');

        // 회원 유형 버튼으로 mt_level 필터링 (필요 시)
        if (!empty($_POST['obj_search_level'])) {
            $DB->where('a1.mt_status', $_POST['obj_search_level']);
        }
    }

    /* ===========================
     *  검색어
     * =========================== */
    if (!empty($_POST['obj_search_txt'])) {
        $searchTxt   = $_POST['obj_search_txt'];
        $sel_search  = $_POST['obj_sel_search'] ?? 'all';

        if ($sel_search == "all") {
            $_instr_where = 'instr(a1.mt_id, \''.$searchTxt.'\') or ';
            $_instr_where .= 'instr(a1.mt_name, \''.$searchTxt.'\') or ';
            $_instr_where .= 'instr(a1.mt_hp, \''.$searchTxt.'\')';
            $DB->where('( '.$_instr_where.' )');
        } elseif ($sel_search == "mt_id") {
            $DB->where('instr(a1.mt_id, ?)', [$searchTxt]);
        } elseif ($sel_search == "mt_name") {
            $DB->where('instr(a1.mt_name, ?)', [$searchTxt]);
        } elseif ($sel_search == "mt_hp") {
            $DB->where('instr(a1.mt_hp, ?)', [$searchTxt]);
        } else {
            $DB->where('( instr('.$sel_search.', \''.$searchTxt.'\') )');
        }
    }

    /* ===========================
     *  정렬 / 페이징
     * =========================== */
    $DB->orderBy("a1.idx", "desc");

    $list = $DB->arraybuilder()->paginate("member_t a1", $pg, '*, idx as mt_idx');
    $query = $DB->getLastQuery();

    $n_page     = $DB->totalPages;
    $totalCount = $DB->totalCount;
    $counts     = $totalCount - (($pg - 1) * $rows);


    $appr = [
            'N' => '미승인',
            'Y' => '승인',
            'T' => '임시',
            'D' => '거절',
    ]
    ?>
    <div class="table-responsive margin-top-20">
        <table class="table table-striped table-bordered margin-bottom-20" id="listTable" style="min-width: 800px">
            <thead class="thead-dark">
            <tr>
                <th class="text-center" style="width:80px;">번호</th>
                <th class="text-center" style="width:200px;">아이디</th>
                <th class="text-center" style="width:140px;">이름</th>
                <th class="text-center" style="width:120px;">휴대폰번호</th>
                <?php if($type === 'approval'){?>
                    <th class="text-center" style="width:120px;">승인여부</th>
                <?php } ?>
                <th class="text-center" style="width:90px;">로그인</th>
                <th class="text-center" style="width:90px;">상태</th>
                <th class="text-center" style="width:120px;">가입일시</th>
                <th class="text-center" style="width:120px;">관리</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if ($list) {
                foreach ($list as $row) {
                    ?>
                    <tr data-id="<?=$row['mt_idx']?>">
                        <td data-title="번호" class="text-center">
                            <?=$counts?>
                        </td>
                        <td data-title="아이디" class="text-center">
                            <?=$row['mt_id']?>
                        </td>
                        <td data-title="이름" class="text-center">
                            <?=$row['mt_name']?>
                        </td>
                        <td data-title="휴대폰번호" class="text-center">
                            <?=format_phone($row['mt_hp'])?>
                        </td>
                        <?php if($type === 'approval'){?>
                            <td data-title="승인여부" class="text-center">
                                <?=$row['mt_appr'] === 'N' ? '승인대기' : '승인거절'?>
                            </td>
                        <?php } ?>
                        <td data-title="구분" class="text-center">
                            <?=$row['mt_status'] === 'Y' ? '가능' : '불가능' ?>
                        </td>
                        <td data-title="구분" class="text-center">
                            <?=$appr[$row['mt_appr']]?>
                        </td>
                        <td data-title="가입/승인 일시" class="text-center">
                            <?=DateType($row['mt_wdate'], 6)?>
                        </td>
                        <td data-title="관리" class="text-center">
                            <input type="button"
                                   class="btn btn-outline-info"
                                   value="수정"
                                   onclick="location.href='./form.php?act=update&mt_idx=<?=$row['mt_idx']?>&type=<?=htmlspecialchars($type, ENT_QUOTES)?>'" />
                            <?php if ($type !== 'secession') { ?>
                                <input type="button"
                                       class="btn btn-outline-danger"
                                       value="탈퇴"
                                       onclick="f_retire_mem(<?=$row['mt_idx']?>);" />
                            <?php } ?>
                        </td>
                    </tr>
                    <?php
                    $counts--;
                }
            } else {
                ?>
                <tr>
                    <td colspan="<?=$_colspan_txt?>" class="text-center"><b>자료가 없습니다.</b></td>
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
    ?>
    <script>
        $(document).find('[data-id="listCount"][data-fid="<?=$_POST['obj_frm']?>"]').text('<?=$totalCount?>');
        $(document).find('#fexcel input[name="excel_query"]').val("<?=$query?>");
    </script>
    <?php
}
else if($_POST['act'] == "updateShow") {

    $DB->where('idx', $_POST['id']);
    $arr_query = array(
        'mt_status' => $_POST['mt_status']
    );
    $result = $DB->update('member_t', $arr_query);

    // 결과 반환
    echo json_encode([
        'success' => $result,
        'message' => $result ? '성공적으로 변경되었습니다.' : '처리 중 오류가 발생했습니다.'
    ]);
    exit;

}


if ($_POST['act']=='follow_accept') {
    header('Content-Type: application/json');

    $follower_idx = $_POST['follower_idx'];
    $following_idx = $_POST['following_idx'];
    followAccept($follower_idx, $following_idx);

}
elseif ($_POST['act']=='follow_reject') {
    header('Content-Type: application/json');

    $follower_idx = $_POST['follower_idx'];
    $following_idx = $_POST['following_idx'];
    followReject($follower_idx, $following_idx);

}
elseif ($_POST['act']=='following') {
    header('Content-Type: application/json');

    $follower_idx = $_POST['follower_idx'];
    $following_idx = $_POST['following_idx'];
    followApprove($follower_idx, $following_idx);

}
elseif ($_POST['act']=='unfollow') {
    header('Content-Type: application/json');

    $follower_idx = $_POST['follower_idx'];
    $following_idx = $_POST['following_idx'];
    unfollow($follower_idx, $following_idx);

}
elseif ($_POST['act']=='follow_remove') {
    header('Content-Type: application/json');

    $follower_idx = $_POST['follower_idx'];
    $my_idx = $_POST['my_idx'];
    followRemove($my_idx, $follower_idx);

} elseif ($_POST['act']=='badge_remove') {
    header('Content-Type: application/json');

    $badge_idx = $_POST['badge_idx'];
    $my_idx = $_POST['my_idx'];

    $exists = $DB->where('idx', $badge_idx)
        ->where('mt_idx', $my_idx)
        ->has('member_badge_t');

    if (!$exists) {
        die(json_encode(['success' => false, 'message' => '데이터가 없습니다.'], JSON_UNESCAPED_UNICODE));
    }


    $DB->where('idx', $badge_idx);
    $DB->where('mt_idx', $my_idx);
    $DB->delete('member_badge_t');

    die(json_encode(['success' => true, 'message' => '삭제되었습니다.'], JSON_UNESCAPED_UNICODE));


}
elseif ($_POST['act']=='user_menu') {
    header('Content-Type: application/json');



    $menuList = $DB->get($CFG_TBL['user']['menu'], null, 'idx');

    foreach ($menuList as $menu) {
        $idx = $menu['idx'];

        // 등급 값이 전달된 경우만 사용, 없으면 빈 문자열
        $level_arr = $_POST['umt_level'][$idx] ?? [];
        $level_string = is_array($level_arr) && count($level_arr) > 0
            ? implode('|:|', array_map('intval', $level_arr))
            : '';

        $DB->where('idx', intval($idx));
        $DB->update($CFG_TBL['user']['menu'], ['umt_level' => $level_string]);
    }

    die(json_encode(['success' => true, 'message' => '저장되었습니다.' , $_POST['umt_level']], JSON_UNESCAPED_UNICODE));


}









include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
