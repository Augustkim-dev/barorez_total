<?
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";

// 성공 응답
$redirect_url = '';
switch($_POST['sel_lv']) {
    case '2':
        $redirect_url = './member_list.php';
        break;
    case '5':
        $redirect_url = './member_seller_list.php';
        break;
    case '7':
        $redirect_url = './member_agency_list.php';
        break;
    case '8':
        $redirect_url = './member_mng_list.php';
        break;
    default:
        $redirect_url = './member_list.php';
}

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
        if(empty($_POST['mt_email'])) {
            throw new Exception("이메일을 입력해주세요.");
        }

        // 트랜잭션 시작
        $DB->startTransaction();

        unset($arr_query);
        $arr_query = array(
            "mt_type" => 1,
            "mt_level" => $_POST['mt_level'],
            "mt_grade" => $_POST['mt_grade'],
            "mt_id" => $_POST['mt_id'],
            "mt_pwd" => password_hash($_POST['mt_pwd'], PASSWORD_DEFAULT),
            "mt_name" => $_POST['mt_name'],
            "mt_nickname" => $_POST['mt_nickname'] ? $_POST['mt_nickname'] : $_POST['mt_name'],
            "mt_nickname_date" => $DB->now(),
            "mt_email" => $_POST['mt_email'],
            "mt_hp" => format_phone($_POST['mt_hp']),
            "mt_birth" => $_POST['mt_birth'],
            "mt_gender" => $_POST['mt_gender'],
            "mt_position" => $_POST['mt_position'],
            "mt_status" => "Y",
            'mt_wdate' => $DB->now(),
        );

        if ($_POST['mt_level']=='5') {
            $arr_query['mt_seller'] = 'Y';
        }

        if ($_POST['mt_level']=='7') {
            $arr_query['mt_agency'] = 'Y';
        }

        if ($_POST['mt_level']=='8') {
          $arr_query['mt_mng'] = 'Y';
        }

        $_mt_last_idx = $DB->insert('member_t', $arr_query);


        if ($_mt_last_idx) {
            if ($_POST['mt_level']=='5') {
                unset($arr_query);
                $arr_query = array(
                    "mt_idx" => $_mt_last_idx,
                    "mt_agency_idx" => $_POST['mt_agency_idx'],
                    "slt_company_name" => $_POST['mt_name']." 상호명",
                    "slt_company_boss" => $_POST['mt_name'],
                    "slt_company_hp1" => $_POST['mt_hp'],
                    "slt_manager" => $_POST['mt_name'],
                    "slt_manager_email" => $_POST['mt_email'],
                    "slt_tax_email" => $_POST['mt_email'],
                    "slt_commission" => 10,
                );

                $_srt_last_idx = $DB->insert('seller_t', $arr_query);

                unset($arr_query);
                $arr_query = array(
                    "mt_idx" => $_mt_last_idx,
                    "mt_agency_idx" => $_POST['mt_agency_idx'],
                    "srt_name" => $_POST['mt_name']." 스토어",
                    "srt_show" => "N",
                );

                $_srt_last_idx = $DB->insert('store_t', $arr_query);

            } else if ($_POST['mt_level']=='7') {
                unset($arr_query);
                $arr_query = array(
                    "mt_idx" => $_mt_last_idx,
                    "agy_content" => "",
                    "agy_url" => "",
                    "agy_show" => "N",
                );

                $_agy_last_idx = $DB->insert('agency_t', $arr_query);
            }
        }

        $DB->commit();

        // 성공 응답
        echo json_encode([
            'success' => true,
            'message' => '저장되었습니다.',
            'mt_idx' => $_mt_last_idx,
            'uploaded_files' => '',
            'redirect' => $redirect_url.'?sel_lv='.$_POST['sel_lv'],
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
        if (empty($_POST['mt_email'])) {
            throw new Exception("이메일을 입력해주세요.");
        }

        // 트랜잭션 시작
        $DB->startTransaction();

        // 기본 데이터 준비
        $arr_query = array(
            "mt_id" => clean_xss_tags($_POST['mt_id']),
            "mt_grade" => $_POST['mt_grade'],
            "mt_name" => clean_xss_tags($_POST['mt_name']),
            "mt_hp" => clean_xss_tags($_POST['mt_hp']),
            "mt_email" => clean_xss_tags($_POST['mt_email']),
            "mt_nickname" => clean_xss_tags($_POST['mt_nickname']),
            "mt_add1" => clean_xss_tags($_POST['mt_add1']),
            "mt_add2" => clean_xss_tags($_POST['mt_add2']),
            "mt_zip" => clean_xss_tags($_POST['mt_zip']),
            "mt_type" => $_POST['mt_type'],
            "mt_position" => $_POST['mt_position'],
            "mt_status" => $_POST['mt_status'],
            "mt_smsing" => $_POST['mt_smsing'],
            "mt_mailing" => $_POST['mt_mailing'],
            "mt_pushing1" => $_POST['mt_pushing1'],
            "mt_pushing2" => $_POST['mt_pushing2'],
            "mt_pushing3" => $_POST['mt_pushing3']
        );

        // 비밀번호 처리
        if (!empty($_POST['mt_pwd']) && !empty($_POST['mt_pwd_re'])) {
            if ($_POST['mt_pwd'] !== $_POST['mt_pwd_re']) {
                throw new Exception("비밀번호가 일치하지 않습니다.");
            }
            $arr_query['mt_pwd'] = password_hash($_POST['mt_pwd'], PASSWORD_DEFAULT);
        }

        if (isset($_POST['mt_email'])) $arr_query['mt_email'] = $_POST['mt_email'];
        if (isset($_POST['mt_birth'])) $arr_query['mt_birth'] = $_POST['mt_birth'];
        if (isset($_POST['mt_gender'])) $arr_query['mt_gender'] = $_POST['mt_gender'];
        if (isset($_POST['mt_retire_memo'])) $arr_query['mt_retire_memo'] = $_POST['mt_retire_memo'];
        if (isset($_POST['mt_mng_memo'])) $arr_query['mt_mng_memo'] = $_POST['mt_mng_memo'];

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

        // 새로운 첨부 파일 업로드 처리
        if ($_last_idx) {
            for ($i = 1; $i <= $_POST['file_count']; $i++) {
                if ($_FILES["slt_file{$i}"]['name']) {
                    handle_file_upload($_POST['board'], $_FILES["slt_file{$i}"], $i, $_last_idx, "slt_file{$i}", $ct_seller_dir);
                }
            }
        }

        // 스토어 정보 업데이트
        if ($_POST['sel_lv'] == "5") {

            unset($seller_arr_query);
            $seller_arr_query = array(
                "slt_company_name" => $_POST['mt_name'],
                "slt_company_num"=> $_POST['slt_company_num'],
                "slt_company_boss" => $_POST['slt_company_boss'],
                "slt_company_hp1" => $_POST['slt_company_hp1'],
                "slt_company_hp2" => $_POST['slt_company_hp2'],
                "slt_company_zip" => $_POST['slt_company_zip'],
                "slt_company_add1" => $_POST['slt_company_add1'],
                "slt_company_add2" => $_POST['slt_company_add2'],
                "slt_company_addr_jibeon" => $_POST['slt_company_addr_jibeon'],
                "slt_company_sido" => $_POST['slt_company_sido'],
                "slt_company_gugun" => $_POST['slt_company_gugun'],
                "slt_company_dong" => $_POST['slt_company_dong'],
                "slt_company_hdong" => $_POST['slt_company_hdong'],
                "slt_company_lat" => $_POST['slt_company_lat'],
                "slt_company_lng" => $_POST['slt_company_lng'],
                "slt_company_uptae" => $_POST['slt_company_uptae'],
                "slt_company_upjong" => $_POST['slt_company_upjong'],
                "slt_company_tongsin" => $_POST['slt_company_tongsin'],
                "slt_manager" => $_POST['slt_manager'],
                "slt_manager_email" => $_POST['slt_manager_email'],
                "slt_tax_email" => $_POST['slt_tax_email'],
                "slt_commission" => $_POST['slt_commission'],
                "slt_bank" => $_POST['slt_bank'],
                "slt_bank_account" => $_POST['slt_bank_account'],
                "slt_bank_name" => $_POST['slt_bank_name'],
                "slt_chulgo_zip" => $_POST['slt_chulgo_zip'],
                "slt_chulgo_add1" => $_POST['slt_chulgo_add1'],
                "slt_chulgo_add2" => $_POST['slt_chulgo_add2'],
                "slt_chulgo_hp1" => $_POST['slt_chulgo_hp1'],
                "slt_chulgo_hp2" => $_POST['slt_chulgo_hp2'],
                "slt_return_zip" => $_POST['slt_return_zip'],
                "slt_return_add1" => $_POST['slt_return_add1'],
                "slt_return_add2" => $_POST['slt_return_add2'],
                "slt_return_hp1" => $_POST['slt_return_hp1'],
                "slt_return_hp2" => $_POST['slt_return_hp2'],
            );


            $DB->where('mt_idx', $_last_idx);
            $DB->update('seller_t', $seller_arr_query);

            // 이미지 업로드 처리
            if (!is_dir($member_store_dir)) {
                if (!mkdir($member_store_dir, 0707, true)) {
                    throw new Exception("업로드 디렉토리 생성 실패");
                }
                chmod($member_store_dir, 0707);
            }

            // 이미지 필드 처리 (최대 3개)
            $uploaded_files = [];
            unset($store_arr_query);
            for ($i = 1; $i <= 1; $i++) {
                $field_name = "srt_image{$i}";
                $delete_flag = "{$field_name}_delete";

                // 이미지 삭제 처리
                if (isset($_POST[$delete_flag]) && $_POST[$delete_flag] === 'Y') {
                    // 기존 파일 삭제
                    $old_file = $DB->getValue("store_t", $field_name, "mt_idx = {$_last_idx}");
                    if ($old_file && file_exists($member_store_dir . $old_file)) {
                        unlink($member_store_dir . $old_file);
                    }
                    $store_arr_query[$field_name] = '';
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
                    $old_file = $DB->getValue("store_t", $field_name, "mt_idx = {$_last_idx}");
                    if ($old_file && file_exists($member_img_dir . $old_file)) {
                        unlink($member_store_dir . $old_file);
                    }

                    // 새 파일 업로드
                    $timestamp = time();
                    $filename = "srt_image_{$_last_idx}_{$i}_{$timestamp}.{$file_ext}";
                    $filepath = $member_store_dir . $filename;

                    if (move_uploaded_file($file['tmp_name'], $filepath)) {
                        chmod($filepath, 0644);
                        $store_arr_query[$field_name] = $filename;
                        $uploaded_files[$i] = $filename;
                    } else {
                        throw new Exception("파일 업로드 실패 ({$field_name})");
                    }
                }
            }

            // 이미지 정보 업데이트
            if (!empty($store_arr_query)) {
                $DB->where('mt_idx', $_last_idx);
                if (!$DB->update('store_t', $store_arr_query)) {
                    throw new Exception("이미지 정보 업데이트 실패");
                }
            }

            $selected_categories = isset($_POST['categories']) ? $_POST['categories'] : [];
            $categories_json = json_encode($selected_categories, JSON_UNESCAPED_UNICODE);


            unset($store_arr_query);
            $store_arr_query = array(
                "srt_name" => clean_xss_tags($_POST['srt_name']),
                "srt_content" => clean_xss_tags($_POST['srt_content']),
                "srt_ca_content" => $categories_json,
                "srt_operating_hours" => clean_xss_tags($_POST['srt_operating_hours']),
                "srt_break_time" => clean_xss_tags($_POST['srt_break_time']),
            );

            $DB->where('mt_idx', $_last_idx);
            $DB->update('store_t', $store_arr_query);
        }

        // 에이전시 정보 업데이트
        if ($_POST['sel_lv'] == "7") {

            // 이미지 업로드 처리
            if (!is_dir($member_agency_dir)) {
                if (!mkdir($member_agency_dir, 0707, true)) {
                    throw new Exception("업로드 디렉토리 생성 실패");
                }
                chmod($member_agency_dir, 0707);
            }

            // 이미지 필드 처리 (최대 3개)
            $uploaded_files = [];
            unset($agency_arr_query);
            for ($i = 1; $i <= 1; $i++) {
                $field_name = "agy_logo{$i}";
                $delete_flag = "{$field_name}_delete";

                // 이미지 삭제 처리
                if (isset($_POST[$delete_flag]) && $_POST[$delete_flag] === 'Y') {
                    // 기존 파일 삭제
                    $old_file = $DB->getValue("agency_t", $field_name, "mt_idx = {$_last_idx}");
                    if ($old_file && file_exists($member_agency_dir . $old_file)) {
                        unlink($member_agency_dir . $old_file);
                    }
                    $agency_arr_query[$field_name] = '';
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
                    $old_file = $DB->getValue("agency_t", $field_name, "mt_idx = {$_last_idx}");
                    if ($old_file && file_exists($member_agency_dir . $old_file)) {
                        unlink($member_agency_dir . $old_file);
                    }

                    // 새 파일 업로드
                    $timestamp = time();
                    $filename = "agy_logo_{$_last_idx}_{$i}_{$timestamp}.{$file_ext}";
                    $filepath = $member_agency_dir . $filename;

                    if (move_uploaded_file($file['tmp_name'], $filepath)) {
                        chmod($filepath, 0644);
                        $agency_arr_query[$field_name] = $filename;
                        $uploaded_files[$i] = $filename;
                    } else {
                        throw new Exception("파일 업로드 실패 ({$field_name})");
                    }
                }
            }

            // 이미지 정보 업데이트
            if (!empty($arr_query)) {
                $DB->where('mt_idx', $_last_idx);
                if (!$DB->update('agency_t', $agency_arr_query)) {
                    throw new Exception("이미지 정보 업데이트 실패");
                }
            }

            unset($agency_arr_query);
            $agency_arr_query = array(
                "agy_name" => clean_xss_tags($_POST['agy_name']),
                "agy_content" => clean_xss_tags($_POST['agy_content']),
                "agy_url" => $_POST['agy_url'],
                "agy_show" => $_POST['agy_show'],
            );

            $DB->where('mt_idx', $_last_idx);
            $DB->update('agency_t', $agency_arr_query);
        }

        $DB->commit();

        echo json_encode([
            'success' => true,
            'message' => '수정되었습니다',
            'mt_idx' => $_last_idx,
            'uploaded_files' => $uploaded_files,
            'redirect' => $redirect_url.'?sel_lv='.$_POST['sel_lv'],
        ]);

    } catch (Exception $e) {
        $DB->rollback();
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

    /*
    if ($_mt_last_idx) {
        $DB->where('idx', '1');
        $DB->update('setup_t', array("st_optimize_date" => date('YmdHis')));

        if ($_POST['mt_level']=='4') {
            $_inc_act = 'input';
            include './store_update.inc.php';
            if ($_last_idx) {
                $DB->where('idx', $_last_idx);
                $row = $DB->getone('store_t');
                //----------------------------------------------------------------------------------------------------------
                $_inc_act = 'input_file';
                include './store_update.inc.php';
            }
        }
    }
    */

}
else if ($_POST['act']=="delete") {
    unset($arr_query);
    $arr_query = array(
        "del_status" => 'Y',
    );
    $DB->where('idx', $_POST['idx']);
    $DB->update('member_t', $arr_query);

    echo "Y";

}
else if ($_POST['act']=="status_chg") {
    unset($arr_query);
    $arr_query = array(
        $_POST['mt_obj'] => $_POST['mt_val'],
    );
    $DB->where('idx', $_POST['mt_idx']);
    $DB->update('member_t', $arr_query);

    echo "Y";

}
else if($_POST['act']=='approval') {
    unset($arr_query);
    $arr_query = array(
        "mt_seller" => $_POST['mt_seller'],
        "mt_seller_memo" => $_POST['mt_seller']==='N' ? $_POST['mt_seller_memo'] : "",
    );
    if ($_POST['mt_seller']=='Y') {
        $arr_query['mt_status'] = 'Y';
        $arr_query['mt_sldate'] = $DB->now();
    }

    $DB->where('idx', $_POST['mt_idx']);
    $DB->update('member_t', $arr_query);

    p_alert("수정되었습니다.");

}
else if($_POST['act']=='retire') {
    $DB->where('idx', $_POST['mt_idx_t']);
    $row_mt = $DB->getone('member_t', "*, idx as mt_idx");

    $retire = false;
    $reborn = false;
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
            "mt_status" => 'N',
            'mt_rdate' => $DB->now(),
        );
        if ($_POST['mt_retire_status']) { // 탈퇴요청
            $arr_query['mt_retire_status'] = $_POST['mt_retire_status'];
        } else { // 관리자 탈퇴처리
            $arr_query['mt_retire_level'] = $row_mt['mt_level'];
            $arr_query['mt_retire_memo'] = "관리자 권한 회원탈퇴 처리";
        }
    }
    if($reborn) {
        unset($arr_query);
        $arr_query = array(
            "mt_level" => $row_mt['mt_retire_level'],
            "mt_status" => 'Y',
            "mt_rdate" => '',
            "mt_retire_level" => null,
            "mt_retire_memo" => "",
            "mt_retire_rdate" => "",
            "mt_retire_status" => "N",
        );
    }
    if ($arr_query) {
        $DB->where('idx', $_POST['mt_idx_t']);
        $DB->update('member_t', $arr_query);
    }

    if($retire) {
        if($row_mt['st_idx'] && $row_mt['mt_retire_level']=='4') {
            $DB->where('mt_idx', $_POST['mt_idx_t']);
            $list_st = $DB->get('store_t');
            if($list_st) {
                foreach($list_st as $row_st) {
                    /*for($q=1;$q<=$st_company_file_num;$q++){
                        if ($row['st_company_file'.$q]) {
                            @unlink(DATA_PATH."/".$row['st_company_file'.$q]);
                            delete_file_thumbnail($row['st_company_file'.$q]);
                        }
                    }*/
                    unset($arr_query);
                    $arr_query = array(
                        "st_status" => 'N',
                    );
                    $DB->where('mt_idx', $_POST['mt_idx_t']);
                    $DB->update('store_t', $arr_query);

                    unset($arr_query);
                    $arr_query = array(
                        "pt_show" => 'N',
                    );
                    $DB->where('mt_idx', $_POST['mt_idx_t']);
                    $DB->update('product_t', $arr_query);

                    //직원계정 삭제상태로
                    unset($arr_query);
                    $arr_query = array(
                        "del_status" => 'Y',
                        "mt_level" => '1',
                        "mt_status" => 'N',
                        'mt_rdate' => $DB->now(),
                        "mt_retire_level" => '3',
                        "mt_retire_memo" => "판매자회원탈퇴 처리(직원계정)",
                    );
                    $DB->where('st_idx', $row_st['idx']);
                    $DB->where('mt_level', '3');
                    $DB->update('member_t', $arr_query);
                    //$DB->delete('member_t');
                }
            }
        }
    }

    echo "Y";
}
else if($_POST['act']=='reborn') {
    $DB->where('idx', $_POST['mt_idx_t']);
    $row_mt = $DB->getone('member_t', "*, idx as mt_idx");

    unset($arr_query);
    $arr_query = array(
        "mt_level" => $row_mt['mt_retire_level'],
        "mt_status" => 'Y',
        "mt_rdate" => '',
        "mt_retire_level" => null,
        "mt_retire_memo" => "",
        "mt_retire_rdate" => "",
        "mt_retire_status" => "N",
    );
    $DB->where('idx',$_POST['mt_idx_t']);
    $DB->update('member_t', $arr_query);

    /*if($row_mt['st_idx'] && $row_mt['mt_retire_level']=='4') {
        $DB->where('mt_idx', $_POST['mt_idx_t']);
        $list_st = $DB->get('store_t');
        if($list_st) {
            foreach($list_st as $row_st) {
                unset($arr_query);
                $arr_query = array(
                    "st_status" => 'Y',
                );
                $DB->where('mt_idx', $_POST['mt_idx_t']);
                $DB->update('store_t', $arr_query);
            }
        }
    }*/

    $resultArr = array();
    $resultArr['link'] = "./member_list.php?sel_lv=".$row_mt['mt_retire_level'];
    print(json_encode($resultArr));

}
else if($_POST['act'] == "list") {
    $rows = $_POST['rows'] ? $_POST['rows'] : $n_limit_num;
    $type = $_POST['type'];
    $_colspan_txt = $type === 'approval' ? 8 : 11;
//    if($_POST['form_type'] == 'R') { $_colspan_txt = 8; }
//    if($_POST['sel_lv'] == '5') { $_colspan_txt = 10; if($_POST['form_type'] == 'R') { $_colspan_txt = 8; } }
//    if($_POST['sel_lv'] == '7') { $_colspan_txt = 10; if($_POST['form_type'] == 'R') { $_colspan_txt = 9; } }
//
    unset($list);
    $DB->pageLimit = $rows;
    $pg = $_POST['obj_pg'];

//    //고정 검색값
    $DB->where('a1.del_status', 'N');
    if ($_POST['form_type'] == 'R') {
        $DB->where('a1.mt_level', '1');
        if ($_POST['sel_lv']) {
            $DB->where('a1.mt_retire_level', $_POST['sel_lv']);
        }
    } else {
        if ($_POST['sel_lv']) {
            $DB->where('a1.mt_level', $_POST['sel_lv']);
        }
    }

    //검색
    if ($_POST['search_txt']) {
        if ($_POST['sel_search'] == "all") {
            $_instr_where = 'instr(a1.mt_id, \''.$_POST['search_txt'].'\') or ';
            $_instr_where .= 'instr(a1.mt_name, \''.$_POST['search_txt'].'\') or ';
            $_instr_where .= 'instr(a1.mt_hp, \''.$_POST['search_txt'].'\') or ';
            $_instr_where .= 'instr(a1.mt_email, \''.$_POST['search_txt'].'\')';
            $DB->where('( '.$_instr_where.' )');
        } else {
            $DB->where('( instr('.$_POST['sel_search'].', \''.$_POST['search_txt'].'\') )');
        }
    }

    //로그인방법
    if ($_POST['sel_mt_login_type'] && $_POST['sel_mt_login_type'] != 'all') {
        $DB->where('a1.mt_login_type', $_POST['sel_mt_login_type']);
    }

    //회원상태
    if ($_POST['sel_mt_status'] && $_POST['sel_mt_status'] != 'all') {
        $DB->where('a1.mt_status', $_POST['sel_mt_status']);
    }

    //승인상태
    if ($_POST['sel_mt_seller'] && $_POST['sel_mt_seller'] != 'all') {
        $DB->where('a1.mt_seller', $_POST['sel_mt_seller']);
    }

    //탈퇴요청상태
    if ($_POST['sel_mt_retire_status'] && $_POST['sel_mt_retire_status'] != 'all') {
        $DB->where('a1.mt_retire_status', $_POST['sel_mt_retire_status']);
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
                <th class="text-center" style="width:200px;">관리</th>
                <th class="text-center" style="width: ;">아이디</th>
                <th class="text-center" style="width: 140px;">이름</th>
                <th class="text-center" style="width: 120px;">휴대폰번호</th>
                <th class="text-center" style="width: 120px;">닉네임</th>
                <?php if($type !== 'approval'){?>
                <th class="text-center" style="width: 120px;">로그인 구분</th>
                <th class="text-center" style="width: 120px;">회원 유형</th>
                <th class="text-center" style="width: 120px;">로그인</th>
                <?php } ?>
                <th class="text-center" style="width: 120px;"><?=$type !== 'approval' ? '회원 상태' : '승인 상태'?></th>
                <th class="text-center" style="width: 120px;"><?= $type === 'approval'
                        ? '승인 일시'
                        : ($type === 'secession'
                            ? '탈퇴 일시'
                            : '가입/승인 일시') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            if ($list) {
                foreach ($list as $row) {
                    $DB->where('idx', $row['mt_idx']);
//                    $row_st = $DB->getone("store_t a1", "*, a1.idx as st_idx");
//
//                    $DB->where('w_code', $row['mt_grade']);
//                    $grade = $DB->getone("member_grade_t");
//
//                    $DB->where('mt_idx', $row['mt_idx']);
//                    $DB->where('gmat_status', 2);
//                    $DB->where('gmat_del', 'N');
//                    $auths = $DB->get("golf_membership_auth_t");
//                    $auth_cnt = $DB->count;
                    ?>
                    <tr data-id="<?=$row['mt_idx']?>">
                        <td data-title="번호" class="text-center">
                            <?=$counts?>
                        </td>
                        <td data-title="관리" class="text-center">
<!--                            <input type="button" class="btn btn-outline-secondary btn-sm" value="상세" onclick="location.href='./member_view.php?mt_idx=<?php $row['mt_idx']?>&type=<?php $type?>'" />-->
                            <input type="button" class="btn btn-outline-info btn-sm" value="수정" onclick="location.href='./member_form.php?act=update&mt_idx=<?=$row['mt_idx']?>&type=<?=$type?>'" />
                            <?php if($type !== 'approval'){ ?>
                            <input type="button" class="btn btn-outline-danger btn-sm" value="탈퇴" onclick="f_retire_mem('<?=$row['mt_idx']?>');" />
                            <?php }?>
                        </td>

                        <td data-title="아이디" class="text-center">
                            <div class="user user--bordered">
                                <img src="assets/img/users/user_1.jpg">
                                <div class="user__name">
                                    <strong><?=$row['mt_id']?></strong>
                                </div>
                            </div>
                        </td>

                        <td data-title="이름" class="text-center">
                            <?=$row['mt_name']?>
                        </td>
                        <td data-title="휴대폰번호" class="text-left">
                            <?=$row['mt_hp']?>
                        </td>
                        <td data-title="닉네임" class="text-center">
                            <?=$row['mt_nickname']?>
                        </td>

                        <?php if($type !== 'approval'){?>
                        <td data-title="로그인 구분" class="text-center">
                            <?=$arr_mt_type[$row['mt_type']]?>
                        </td>
                        <td data-title="회원 유형" class="text-center">
                            <?=$row['mt_hp']?>
                        </td>
                        <td data-title="로그인" class="text-center">
                            <label class="switch switch-sm"><input type="checkbox" name="mt_status" <?=$row['mt_status']=="Y" ? "checked" : ""?> value="<?=$row['mt_status']?>"><span></span></label>
                        </td>
                        <?php }?>

                        <td data-title="회원상태" class="text-center">
                            <label class="switch switch-sm"><input type="checkbox" name="mt_status" <?=$row['mt_status']=="Y" ? "checked" : ""?> value="<?=$row['mt_status']?>"><span></span></label>
                        </td>
                        <td data-title="가입/승인 일시" class="text-center">
                            <?if($_POST['sel_lv'] == '4') {?>
                                <?=DateType($row['mt_wdate'], 1)?><br/>
                                <?=DateType($row['mt_sldate'], 1)?>
                            <?} else {?>
                                <?=DateType($row['mt_wdate'], 6)?>
                            <?}?>
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
    <?

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
