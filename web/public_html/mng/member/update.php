<?
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";

// 성공 응답
$redirect_url = './list.php';

if ($_POST['act'] == "chk_mt_nickname"){
    header('Content-Type: application/json');
    try {
        // 트랜잭션 시작
        $DB->startTransaction();
        $DB->where('mt_nickname', $_POST['mt_nickname']);
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
        if (empty($_POST['mt_name'])) {
            throw new Exception("이름을 입력해주세요.");
        }
        if (empty($_POST['mt_hp'])) {
            throw new Exception("휴대폰 번호를 입력해주세요.");
        }

        // 트랜잭션 시작
        $DB->startTransaction();

        // 기본 데이터 준비
        $arr_query = array(
            "mt_name" => clean_xss_tags($_POST['mt_name']),
            "mt_hp" => clean_xss_tags($_POST['mt_hp']),
            "mt_nickname" => clean_xss_tags($_POST['mt_nickname']),
            "mt_status" => $_POST['del_status'],
            "del_status" => $_POST['del_status'],
        );

        // 비밀번호 처리
        if (!empty($_POST['mt_pwd']) && !empty($_POST['mt_pwd_re'])) {
            if ($_POST['mt_pwd'] !== $_POST['mt_pwd_re']) {
                throw new Exception("비밀번호가 일치하지 않습니다.");
            }
            $arr_query['mt_pwd'] = password_hash($_POST['mt_pwd'], PASSWORD_DEFAULT);
        }

        //회원관리에서 회원 구분 변경시
        if($_POST['mt_level'] === '5' || $_POST['mt_appr'] === 'Y'){
            $arr_query['mt_appr'] = 'Y';
            $arr_query['mt_level'] = 5;
            $arr_query['mt_position'] = '딜러회원';
            $arr_query['mt_udate'] = $DB->now();

        }

        if ($_POST['mt_level'] === '2' || $_POST['mt_appr'] === 'N' || $_POST['mt_appr'] === 'D') {
            if(!empty($_POST['mt_appr'])) {
                $arr_query['mt_appr'] = in_array($_POST['mt_appr'], ['N', 'D']) ? $_POST['mt_appr'] : '';
            }
            $arr_query['mt_level'] = 2;
            $arr_query['mt_position'] = '일반회원';
            $arr_query['mt_udate'] = null;
        }

//        if (isset($_POST['mt_retire_memo'])) $arr_query['mt_retire_memo'] = $_POST['mt_retire_memo'];
//        if (isset($_POST['mt_mng_memo'])) $arr_query['mt_mng_memo'] = $_POST['mt_mng_memo'];

        // 회원 정보 업데이트 또는 입력
        if (!empty($_POST['mt_idx'])) {
            $DB->where('idx', $_POST['mt_idx']);
            if (!$DB->update('member_t', $arr_query)) {
                throw new Exception("회원정보 수정에 실패했습니다.");
            }
            $_last_idx = $_POST['mt_idx'];
        }

        // 이미지 업로드 처리
        if (!is_dir($member_img_dir)) {
            if (!mkdir($member_img_dir, 0707, true)) {
                throw new Exception("업로드 디렉토리 생성 실패");
            }
            chmod($member_img_dir, 0707);
        }

        // 이미지 필드 처리 (최대 3개)
        $uploaded_files = [];
        for ($i = 1; $i <= 1; $i++) {
            $field_name = "mt_image{$i}";
            $delete_flag = "{$field_name}_delete";

            // 이미지 삭제 처리
            if (isset($_POST[$delete_flag]) && $_POST[$delete_flag] === 'Y') {
                // 기존 파일 삭제
                $old_file = $DB->getValue("member_t", $field_name, "mt_idx = {$_last_idx}");
                if ($old_file && file_exists($member_img_dir . $old_file)) {
                    unlink($member_img_dir . $old_file);
                }
                $arr_query[$field_name] = '';
            } // 새 이미지 업로드 처리
            else if (isset($_FILES[$field_name]) && $_FILES[$field_name]['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES[$field_name];

                // 파일 확장자 검사
                $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

                if (!in_array($file_ext, $allowed_extensions)) {
                    throw new Exception("허용되지 않는 파일 형식입니다. ({$field_name})");
                }

                // 파일 크기 검사 (5MB)
                if ($file['size'] > 5 * 1024 * 1024) {
                    throw new Exception("파일 크기는 5MB를 초과할 수 없습니다. ({$field_name})");
                }

                // 기존 파일 삭제
                $old_file = $DB->getValue("member_t", $field_name, "mt_idx = {$_last_idx}");
                if ($old_file && file_exists($member_img_dir . $old_file)) {
                    unlink($member_img_dir . $old_file);
                }

                // 새 파일 업로드
                $timestamp = time();
                $filename = "mt_img_{$_last_idx}_{$i}_{$timestamp}.{$file_ext}";
                $filepath = $member_img_dir . $filename;

                if (move_uploaded_file($file['tmp_name'], $filepath)) {
                    chmod($filepath, 0644);
                    $arr_query[$field_name] = $filename;
                    $uploaded_files[$i] = $filename;
                } else {
                    throw new Exception("파일 업로드 실패 ({$field_name})");
                }
            }
        }

        // 이미지 정보 업데이트
        if (!empty($arr_query)) {
            $DB->where('idx', $_last_idx);
            if (!$DB->update('member_t', $arr_query)) {
                throw new Exception("이미지 정보 업데이트 실패");
            }
        }


        // 첨부 파일 삭제 처리
        for ($i = 1; $i <= $_POST['file_count']; $i++) {
            if ($_POST["file{$i}_delete"] == 'Y') {
                $DB->where('board', $_POST['board']);
                $DB->where('bo_id', $_last_idx);
                $DB->where('bf_no', $i);
                $old_file = $DB->getOne('board_file_t');

                if ($old_file) {
                    @unlink($ct_seller_dir . $old_file['bf_file']);
                    $DB->where('idx', $old_file['idx']);
                    $DB->delete('board_file_t');
                }
            }
        }

        $DB->commit();

        echo json_encode([
            'success' => true,
            'message' => '수정되었습니다',
            'mt_idx' => $_last_idx,
            'uploaded_files' => $uploaded_files,
            'redirect' => $redirect_url.'?type='.$_POST['type'],
        ]);

    } catch (Exception $e) {
        $DB->rollback();
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}
else if($_POST['act']=='retire') {
    $DB->where('idx', $_POST['mt_idx_t']);
    $row_mt = $DB->getone('member_t', "*, idx as mt_idx");

    $retire = false;
    if ($_POST['mt_retire_status']) { // 탈퇴요청
        if ($_POST['mt_retire_status'] == 'Y') { $retire = true; }
        if ($_POST['mt_retire_status'] == 'N') { $reborn = true; }
    } else { // 관리자 탈퇴처리
        $retire = true;
    }


    if($retire) {
        unset($arr_query);
        $arr_query = array(
            "mt_level" => '1',
            "del_status" => 'N',
            "mt_status" => 'N',
            'mt_rdate' => $DB->now(),
        );
        if ($_POST['mt_retire_status']) { // 탈퇴요청
            $arr_query['mt_retire_status'] = $_POST['mt_retire_status'];
        } else { // 관리자 탈퇴처리
            $arr_query['mt_retire_level'] = $row_mt['mt_level'];
            $arr_query['mt_retire_memo'] =  isset($_POST['mt_retire_memo']) && $_POST['mt_retire_memo'] !== ''
                ? $_POST['mt_retire_memo']
                : "관리자 권한 회원탈퇴 처리";
        }
    }

    if ($arr_query) {
        $DB->where('idx', $_POST['mt_idx_t']);
        $DB->update('member_t', $arr_query);
    }

    echo "Y";
}
else if($_POST['act'] == "restoration"){
    $DB->where('idx', $_POST['mt_idx_t']);
    $row_mt = $DB->getone('member_t', "*, idx as mt_idx");

    if($row_mt['mt_position'] === '일반회원'){
        $level = 2;
    }else{
        $level = 5;
    }

    unset($arr_query);
    $arr_query = array(
        "mt_level" => $level,
        "del_status" => 'Y',
        "mt_status" => 'Y',
        'mt_rdate' => null,
        'mt_retire_memo' => '',
        'mt_retire_level' => 0,
    );

    if ($arr_query) {
        $DB->where('idx', $_POST['mt_idx_t']);
        $DB->update('member_t', $arr_query);
    }

    echo "Y";
}
else if($_POST['act'] == "list") {
    $rows = $_POST['rows'] ? $_POST['rows'] : $n_limit_num;
    $type = $_POST['type'];
    $_colspan_txt = 8;

    unset($list);
    $DB->pageLimit = $rows;
    $pg = $_POST['obj_pg'];

    //검색
    if ($_POST['obj_search_txt']) {
        if ($_POST['obj_sel_search'] == "all") {
            $_instr_where = 'instr(a1.mt_id, \''.$_POST['obj_search_txt'].'\') or ';
            $_instr_where .= 'instr(a1.mt_name, \''.$_POST['obj_search_txt'].'\') or ';
            $_instr_where .= 'instr(a1.mt_hp, \''.$_POST['obj_search_txt'].'\')';
            $DB->where('( '.$_instr_where.' )');
        }else if($_POST['obj_sel_search'] == "mt_id"){
            $_instr_where = 'instr(a1.mt_id, \''.$_POST['obj_search_txt'].'\')';
            $DB->where('( '.$_instr_where.' )');
        }else if($_POST['obj_sel_search'] == "mt_name"){
            $_instr_where = 'instr(a1.mt_name, \''.$_POST['obj_search_txt'].'\')';
            $DB->where('( '.$_instr_where.' )');
        }else if($_POST['obj_sel_search'] == "mt_hp"){
            $_instr_where = 'instr(a1.mt_hp, \''.$_POST['obj_search_txt'].'\')';
            $DB->where('( '.$_instr_where.' )');
        }else {
            $DB->where('( instr('.$_POST['obj_sel_search'].', \''.$_POST['obj_search_txt'].'\') )');
        }
    }

    //로그인방법
    if ($_POST['sel_mt_login_type'] && $_POST['sel_mt_login_type'] != 'all') {
        $DB->where('a1.mt_login_type', $_POST['sel_mt_login_type']);
    }

    //회원상태
    if ($_POST['obj_search_status'] && $_POST['obj_search_status'] != '') {
        $DB->where('a1.del_status', $_POST['obj_search_status']);
    }

    //승인상태
    if ($_POST['obj_search_level'] && $_POST['obj_search_level'] != 'all') {
        $DB->where('a1.mt_status', $_POST['obj_search_level']);
    }

    //탈퇴요청상태
    if ($_POST['sel_mt_retire_status'] && $_POST['sel_mt_retire_status'] != 'all') {
        $DB->where('a1.mt_retire_status', $_POST['sel_mt_retire_status']);
    }

    $DB->where('a1.mt_mng', 'N');
    if($type === 'secession') {
        $DB->where('a1.mt_level', [1], 'IN');
        $DB->where('a1.mt_appr', '', '=');
    }else{
        $DB->where('a1.mt_level', [2], 'IN');
    }
    //정렬
    if ($_POST['obj_order_desc_asc'] == '1') {
        $DB->orderBy("a1.idx", "desc");
    } else {
        $DB->orderBy("a1.idx", "asc");
    }
    $DB->orderBy("a1.idx", "desc");
//    echo $DB->getLastQuery();
    $list = $DB->arraybuilder()->paginate("member_t a1", $pg, '*, idx as mt_idx');
    $query = $DB->getLastQuery();

    //페이징
    $n_page = $DB->totalPages;
    $totalCount = $DB->totalCount;
    $counts = $totalCount - (($pg - 1) * $rows);
    ?>
    <div class="table-responsive margin-top-20">
        <table class="table table-striped table-bordered margin-bottom-20" id="listTable" style="min-width: 800px">
            <thead class="thead-dark">
            <tr>
                <th class="text-center" style="width:80px;">번호</th>
                <th class="text-center" style="width: 200px;">아이디</th>
                <th class="text-center" style="width: 140px;">이름</th>
                <th class="text-center" style="width: 120px;">휴대폰번호</th>
                <?php if($type !== 'approval'){?>
                    <th class="text-center" style="width: 120px;">로그인 구분</th>
                    <th class="text-center" style="width: 120px;">로그인</th>
                <?php } ?>
                <th class="text-center" style="width: 120px;"><?= $type === 'approval'
                        ? '탈퇴 일시'
                        : '가입 일시' ?></th>
                <th class="text-center" style="width:80px;">관리</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if ($list) {
                foreach ($list as $row) {
                    $DB->where('idx', $row['mt_idx']);
                    if($type === 'secession'){
                        $tab = '&tab=member-tab-3';
                    }else{
                        $tab = '&tab=member-tab-1';
                    }
                    ?>
                    <tr data-id="<?=$row['mt_idx']?>">
                        <td data-title="번호" class="text-center">
                            <?=$counts?>
                        </td>
                        <td data-title="아이디" class="text-center">
                            <div class="user user--bordered">
<!--                                <img src="../assets/img/users/user_1.jpg">-->
                                <div class="user__name">
                                    <strong><?=$row['mt_id']?></strong>
                                </div>
                            </div>
                        </td>

                        <td data-title="이름" class="text-center">
                            <?=$row['mt_name']?>
                        </td>
                        <td data-title="휴대폰번호" class="text-center">
                            <?=format_phone($row['mt_hp'])?>
                        </td>

                        <?php if($type !== 'approval'){?>
                            <td data-title="로그인 구분" class="text-center">
                                <?=$arr_mt_type[$row['mt_type']]?>
                            </td>
                            <td data-title="로그인" class="text-center">
                                <span id="statue"><?=$row['mt_status'] === 'Y' ? '가능' : '불가능'?></span>
                            </td>
                        <?php }?>

                        <td data-title="가입/승인일시" class="text-center">
                        <?php if($type === 'secession'){
                            echo DateType($row['mt_rdate'], 6);
                        }else if($type === 'approval'){
                            echo DateType($row['mt_udate'], 6);
                        }else{
                            echo DateType($row['mt_wdate'], 6);
                        }?>
                        </td>

                        <td data-title="관리" class="text-center">
                            <input type="button" class="btn btn-outline-info" value="수정" onclick="location.href='./form.php?act=update&mt_idx=<?=$row['mt_idx']?>&type=<?=$type.$tab?>'" />
                            <?php if($type !== 'approval' && $type !== 'secession'){ ?>
                                <input type="button" class="btn btn-outline-danger" value="탈퇴" onclick="f_retire_mem('<?=$row['mt_idx']?>');" />
                            <?php }?>
                            <?php if($type === 'secession'){?>
                                <input type="button" class="btn btn-outline-danger" value="복구" onclick="f_restoration_mem('<?=$row['mt_idx']?>');" />
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
    <?}
else if($_POST['act'] == "updateShow") {

    $DB->where('idx', $_POST['id']);
    $arr_query = array(
        "mt_status" => $_POST['mt_status'],
        "del_status" => $_POST['mt_status'],
    );
    $result = $DB->update('member_t', $arr_query);

    // 결과 반환
    echo json_encode([
        'success' => $result,
        'data' => $_POST['mt_status'],
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
