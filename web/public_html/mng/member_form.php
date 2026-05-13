<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$menu_map = [
    '' => ['key' => 1, 'label' => '회원관리'],
    'approval' => ['key' => 2, 'label' => '승인관리'],
    'secession' => ['key' => 3, 'label' => '탈퇴관리']
];
$type = $menu_map[$_GET['type']];
$chk_menu = 1;
$chk_sub_menu = $type['key'];
include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.menu.inc.php";
//include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.sidebar.inc.php";

$chk_post_code = "Y";
$chk_ckeditor = "Y";
$file_count = 3;   // 원하는 첨부파일 갯수로 변경 가능
$board = "seller"; // 파일 업로드

if ($_GET['act'] == "update") {
    $DB->where('idx', $_GET['mt_idx']);
    $row = $DB->getone('member_t', '*, idx as mt_idx');

    // 첨부파일 정보 조회
    $DB->where('board', 'seller');
    $DB->where('bo_id', $_GET['mt_idx']);
    $DB->orderBy('bf_no', 'asc');
    $files = $DB->get('board_file_t');

    // 첨부파일 정보를 배열로 정리
    $file_info = array();
    if($files) {
        foreach($files as $file) {
            $file_info[$file['bf_no']] = $file;
        }
    }

    $_act = "update";
    $_act_txt = " 수정";
} else {
    $_act = "input";
    $_act_txt = " 등록";
}

if($row['mt_level']=='5') {
    $seller_info = true;
} else {
    $seller_info = false;
}

if($row['mt_seller']=='D') {
    $seller_info = true;
}

if($row['mt_level']=='7') {
    $agency_info = true;
} else {
    $agency_info = false;
}

if($row['mt_agency']=='D') {
    $agency_info = true;
}

$DB->orderBy("idx", "asc");
$grade_list = $DB->get("member_grade_t");
?>
<!-- PAGE CONTENT CONTAINER -->
<div class="content" id="content">
    <!-- PAGE HEADING -->
    <div class="page-heading">
        <div class="page-heading__container">
            <div class="icon">
                <span class="li-picture3"></span>
            </div>
            <h1 class="title">회원 수정</h1>
            <p class="caption">
                회원 등록, 수정, 삭제 등을 할 수 있습니다.
            </p>
        </div>
        <nav aria-label="breadcrumb" role="navigation">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="#"><?=$type['label']?></a></li>
                <li class="breadcrumb-item active">회원</li>
            </ol>
        </nav>
    </div>
    <!-- //END PAGE HEADING -->
    <div class="container-fluid">
        <div class="card margin-bottom-0">

            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
                    <?php if($type['key'] === 1 || $type['key'] === 2){?>
                    <li class="nav-item"><a class="nav-link active" id="member-tab-1" data-toggle="tab" href="#member-1" role="tab" aria-controls="home" aria-selected="true">회원정보</a></li>
                    <?php }?>
                    <?php if($type['key'] === 1 || $type['key'] === 3){?>
                    <li class="nav-item"><a class="nav-link <?=$type['key'] === 3 ? 'active' : ''?>" id="member-tab-3" data-toggle="tab" href="#member-3" role="tab" aria-controls="contact" aria-selected="false">접속정보</a></li>
                    <?php }?>
<!--                    --><?// if($_act=='update'){?>
<!--                        <li class="nav-item"><a class="nav-link" id="member-tab-2" data-toggle="tab" href="#member-2" role="tab" aria-controls="profile" aria-selected="false">프로필정보</a></li>-->
<!--                        --><?// if($seller_info) { ?>
<!--                            <li class="nav-item"><a class="nav-link" id="member-tab-4" data-toggle="tab" href="#member-4" role="tab" aria-controls="profile" aria-selected="false">사업자정보</a></li>-->
<!--                            <li class="nav-item"><a class="nav-link" id="member-tab-5" data-toggle="tab" href="#member-5" role="tab" aria-controls="profile" aria-selected="false">스토어정보</a></li>-->
<!--                        --><?// } ?>
<!---->
<!--                        --><?// if($agency_info){ ?>
<!--                            <li class="nav-item"><a class="nav-link" id="member-tab-4" data-toggle="tab" href="#member-4" role="tab" aria-controls="profile" aria-selected="false">사업자정보</a></li>-->
<!--                            <li class="nav-item"><a class="nav-link" id="member-tab-6" data-toggle="tab" href="#member-6" role="tab" aria-controls="profile" aria-selected="false">에이전시정보</a></li>-->
<!--                        --><?// } ?>
<!--                        <li class="nav-item"><a class="nav-link" id="member-tab-3" data-toggle="tab" href="#member-3" role="tab" aria-controls="contact" aria-selected="false">접속정보</a></li>-->
<!--                    --><?// } ?>
                </ul>
            </div>
            <div class="card-body">
                <form method="post" name="frm_form" id="frm_form" action="./member_update.php" target="hidden_ifrm" enctype="multipart/form-data">
                    <input type="hidden" name="act" id="act" value="<?=$_act?>" />
                    <input type="hidden" name="mt_idx" id="mt_idx" value="<?=$row['mt_idx']?>" />
                    <input type="hidden" name="sel_lv" id="sel_lv" value="<?=$_GET['sel_lv']?>" />
                    <input type="hidden" name="file_count" id="file_count" value="<?=$file_count?>" />
                    <input type="hidden" name="board" id="board" value="<?=$board?>" />
                    <div class="tab-content margin-top-15" id="myTabContent">
                        <!-- 회원 정보 -->
                        <div class="tab-pane fade show active" id="member-1" role="tabpanel" aria-labelledby="member-tab-1">
                            <div class="form-group row">
                                <label for="mt_id" class="col-sm-2 col-form-label">아이디 <b class="text-danger">*</b></label>
                                <div class="col-sm-4">
                                    <input type="text" name="mt_id" id="mt_id" value="<?=$row['mt_id']?>" class="form-control">
                                </div>
                                <label for="mt_type" class="col-sm-2 col-form-label">가입유형</label>
                                <div class="col-sm-4">
                                    <select name="mt_type" id="mt_type" class="form-control">
                                        <?php
                                        if ($_act == 'input') {
                                            echo '<option value="1">'.$arr_mt_type[1].'</option>';
                                        } else {
                                            foreach ($arr_mt_type as $key => $value) {
                                                $selected = ($row['mt_type']==$key)?'selected':'';
                                                echo '<option value="'.$key.'" '.$selected.' >'.$value.'</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                    <small class="form-text">가입유형은 일반, 카카오, 네이버, 구글, 애플 유형으로 구분됩니다.</small>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="mt_pwd" class="col-sm-2 col-form-label">비밀번호 <b class="text-danger">*</b></label>
                                <div class="col-sm-4">
                                    <input type="text" name="mt_pwd" id="mt_pwd" value="" class="form-control" minlength="8" maxlength="50">
                                </div>
                                <label for="mt_hp" class="col-sm-2 col-form-label">휴대폰 번호 <b class="text-danger">*</b></label>
                                <div class="col-sm-4">
                                    <input type="text" name="mt_hp" id="mt_hp" value="<?=$row['mt_hp']?>" class="form-control" numberOnly placeholder="'-'없이 입력바랍니다.">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="mt_pwd" class="col-sm-2 col-form-label">비밀번호 확인</label>
                                <div class="col-sm-4">
                                    <input type="text" name="mt_pwd_re" id="mt_pwd_re" value="" class="form-control" minlength="8" maxlength="50">
                                    <small class="form-text">비밀번호 변경시에는 비밀번호 확인까지 입력바랍니다.</small>
                                </div>
                                <label for="wrap_zip1" class="col-sm-2 col-form-label">닉네임 <b class="text-danger">*</b></label>
                                <div class="col-sm-4">
                                    <p class="form-inline flex-nowrap">
                                        <input type="hidden" name="mt_nickname_chk" id="mt_nickname_chk" value="Y" />
                                        <input type="text" class="form-control" style="width: 80%" name="mt_nickname" id="mt_nickname" value="<?php echo $row['mt_nickname']?>"  placeholder="">
                                        <button type="button" class="btn btn-gray ml-2" id="mt_nickname_chk_btn" onclick="f_mt_nickname_chk();">중복확인</button>
                                    </p>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-2">
                                    <label for="mt_name" class="col-form-label d-block form-group">이름 <b class="text-danger">*</b></label>
                                    <label for="mt_type" class="col-form-label d-block">회원구분</label>
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" name="mt_name" id="mt_name" value="<?=$row['mt_name']?>" class="form-control form-group">
                                    <select name="mt_level" id="mt_level" class="form-control">
                                        <?php
                                        foreach($arr_mt_level as $key => $value) {
                                            if(in_array($key, array($_GET['sel_lv']))) {
                                                echo '<option value="'.$key.'">'.$value.'</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <label for="mt_image1" class="col-sm-2 col-form-label">프로필 사진</label>
                                <div class="col-sm-4">
                                    <div class="upload-container">
                                        <div class="upload-box" id="uploadMtImageTrigger1" data-existing-image="<?php echo !empty($row['mt_image1']) ? $member_img_url.$row['mt_image1'] : ''; ?>">
                                            <div class="upload-content">
                                                <div class="plus">+</div>
                                                <div class="text">Upload</div>
                                            </div>
                                            <button type="button" class="remove-btn">×</button>
                                        </div>
                                    </div>
                                    <input type="file" class="filepond d-none" name="mt_image1" id="mt_image1" accept="image/*">
                                    <!-- 삭제 플래그를 위한 hidden 필드 추가 -->
                                    <input type="hidden" name="mt_image1_delete" id="mt_image1_delete" value="N">
                                </div>
                            </div>
                        </div>

                        <!-- 접속 정보 -->
                        <div class="tab-pane fade" id="member-3" role="tabpanel" aria-labelledby="member-tab-3">
                            <div class="form-group row">
                                <label for="mt_status" class="col-sm-2 col-form-label">로그인가능</label>
                                <div class="col-sm-4">
                                    <select name="mt_status" id="mt_status" class="custom-select" data-initial-value="<?=$row['mt_status']?>">
                                        <?=$arr_mt_status_option?>
                                    </select>
                                    <small id="mt_status_help" class="form-text text-muted">* 'N'으로 선택시 로그인이 차단됩니다.</small>
                                </div>
                                <label for="mt_smsing" class="col-sm-2 col-form-label">회원 상태</label>
                                <div class="col-sm-4">
                                    <select name="mt_smsing" id="mt_smsing" class="custom-select" data-initial-value="<?=$row['mt_smsing']?>">
                                        <option value="Y">정상</option>
                                        <option value="N">정지</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="mt_ldate" class="col-sm-2 col-form-label">로그인일시</label>
                                <div class="col-sm-4">
                                    <div class="input-text-box"><?=DateType($row['mt_ldate'], 4)?></div>
                                </div>
                                <label for="mt_ldate" class="col-sm-2 col-form-label">탈퇴일시</label>
                                <div class="col-sm-4">
                                    <div class="input-text-box"><?=DateType($row['mt_ldate'], 4)?></div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="mt_retire_memo" class="col-sm-2 col-form-label">회원탈퇴메모</label>
                                <div class="col-sm-10">
                                    <textarea name="mt_retire_memo" id="mt_retire_memo" class="form-control" rows="3"><?=$row['mt_retire_memo']?></textarea>
                                </div>
                            </div>
                        </div>

                        <? if($_act=='update'){?>
                            <div class="tab-pane fade" id="member-2" role="tabpanel" aria-labelledby="member-tab-2">
                                <div class="form-group row">
                                    <label for="wrap_zip1" class="col-sm-2 col-form-label">닉네임</label>
                                    <div class="col-sm-10">
                                        <p class="form-inline">
                                            <input type="hidden" name="mt_nickname_chk" id="mt_nickname_chk" value="Y" />
                                            <input type="text" class="form-control" name="mt_nickname" id="mt_nickname" value="<?php echo $row['mt_nickname']?>"  placeholder="">
                                            <button type="button" class="btn btn-gray ml-2" id="mt_nickname_chk_btn" onclick="f_mt_nickname_chk();">중복확인</button>
                                        </p>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="mt_image1" class="col-sm-2 col-form-label">프로필 사진</label>
                                    <div class="col-sm-10">
                                        <div class="upload-container">
                                            <div class="upload-box" id="uploadMtImageTrigger1" data-existing-image="<?php echo !empty($row['mt_image1']) ? $member_img_url.$row['mt_image1'] : ''; ?>">
                                                <div class="upload-content">
                                                    <div class="plus">+</div>
                                                    <div class="text">Upload</div>
                                                </div>
                                                <button type="button" class="remove-btn">×</button>
                                            </div>
                                        </div>
                                        <input type="file" class="filepond d-none" name="mt_image1" id="mt_image1" accept="image/*">
                                        <!-- 삭제 플래그를 위한 hidden 필드 추가 -->
                                        <input type="hidden" name="mt_image1_delete" id="mt_image1_delete" value="N">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="mt_profile_memo" class="col-sm-2 col-form-label">소개내용</label>
                                    <div class="col-sm-10">
                                        <textarea name="mt_profile_memo" id="mt_profile_memo" class="form-control" rows="3"><?=$row['mt_profile_memo']?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="member-3" role="tabpanel" aria-labelledby="member-tab-3">
                                <div class="form-group row">
                                    <label for="mt_status" class="col-sm-2 col-form-label">로그인가능</label>
                                    <div class="col-sm-4">
                                        <select name="mt_status" id="mt_status" class="custom-select" data-initial-value="<?=$row['mt_status']?>">
                                            <?=$arr_mt_status_option?>
                                        </select>
                                        <small id="mt_status_help" class="form-text text-muted">* 'N'으로 선택시 로그인이 차단됩니다.</small>
                                    </div>
                                    <label for="mt_smsing" class="col-sm-2 col-form-label">문자수신</label>
                                    <div class="col-sm-4">
                                        <select name="mt_smsing" id="mt_smsing" class="custom-select" data-initial-value="<?=$row['mt_smsing']?>">
                                            <option value="Y">Y</option>
                                            <option value="N">N</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="mt_mailing" class="col-sm-2 col-form-label">메일수신</label>
                                    <div class="col-sm-4">
                                        <select name="mt_mailing" id="mt_mailing" class="custom-select" data-initial-value="<?=$row['mt_mailing']?>">
                                            <option value="Y">Y</option>
                                            <option value="N">N</option>
                                        </select>
                                    </div>
                                    <label for="mt_pushing1" class="col-sm-2 col-form-label">푸시알림(마케팅 정보 수신)</label>
                                    <div class="col-sm-4">
                                        <select name="mt_pushing1" id="mt_pushing1" class="custom-select" data-initial-value="<?=$row['mt_pushing1']?>">
                                            <option value="Y">Y</option>
                                            <option value="N">N</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                  <label for="mt_push" class="col-sm-2 col-form-label">푸시알림(즐겨찾기)</label>
                                  <div class="col-sm-4">
                                    <select name="mt_push" id="mt_push" class="custom-select" data-initial-value="<?=$row['mt_push']?>">
                                      <option value="Y">Y</option>
                                      <option value="N">N</option>
                                    </select>
                                  </div>
                                  <label for="mt_notice_push" class="col-sm-2 col-form-label">푸시알림(공지사항)</label>
                                  <div class="col-sm-4">
                                    <select name="mt_notice_push" id="mt_notice_push" class="custom-select" data-initial-value="<?=$row['mt_notice_push']?>">
                                      <option value="Y">Y</option>
                                      <option value="N">N</option>
                                    </select>
                                  </div>
                                </div>
                                <div class="form-group row">
                                    <label for="mt_wdate" class="col-sm-2 col-form-label">가입일시</label>
                                    <div class="col-sm-4">
                                        <div class="input-text-box"><?=DateType($row['mt_wdate'], 4)?></div>
                                    </div>
                                    <label for="mt_ldate" class="col-sm-2 col-form-label">로그인일시</label>
                                    <div class="col-sm-4">
                                        <div class="input-text-box"><?=DateType($row['mt_ldate'], 4)?></div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="mt_retire_memo" class="col-sm-2 col-form-label">회원탈퇴메모</label>
                                    <div class="col-sm-10">
                                        <textarea name="mt_retire_memo" id="mt_retire_memo" class="form-control" rows="3"><?=$row['mt_retire_memo']?></textarea>
                                    </div>
                                </div>
                            </div>
                            <?
                            if($seller_info || $agency_info) {
                                $DB->where('mt_idx', $row['mt_idx']);
                                $row_slt = $DB->getone('seller_t', '*, idx as slt_idx');
                                ?>
                                <input type="hidden" name="slt_idx" id="slt_idx" value="<?=$row_slt['idx']?>" />
                                <div class="tab-pane fade" id="member-4" role="tabpanel" aria-labelledby="member-tab-3">
                                    <div class="form-group row">
                                        <label for="mt_seller" class="col-sm-2 col-form-label">판매자전환</label>
                                        <div class="col-sm-4">
                                            <select name="mt_seller" id="mt_seller" class="custom-select" data-initial-value="<?=$row['mt_seller']?>">
                                                <?=$arr_mt_seller_option?>
                                            </select>
                                        </div>
                                        <label for="mt_smsing" class="col-sm-2 col-form-label">판매자 전환일시</label>
                                        <div class="col-sm-4">
                                            <div class="input-text-box"><?=DateType($row['mt_sldate'], 4)?></div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="mt_seller" class="col-sm-2 col-form-label">상호 <b class="text-danger">*</b></label>
                                        <div class="col-sm-4">
                                            <input type="text" name="slt_company_name" id="slt_company_name" value="<?=$row_slt['slt_company_name']?>" class="form-control" />
                                        </div>
                                        <label for="mt_smsing" class="col-sm-2 col-form-label">사업자 구분 <b class="text-danger">*</b></label>
                                        <div class="col-sm-4">
                                            <select name="slt_company_type" id="slt_company_type" class="custom-select" data-initial-value="<?=$row['slt_company_type']?>">
                                                <option value="1">개인사업자</option>
                                                <option value="2">법인사업자</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="slt_company_boss" class="col-sm-2 col-form-label">대표자명 <b class="text-danger">*</b></label>
                                        <div class="col-sm-4">
                                            <input type="text" name="slt_company_boss" id="slt_company_boss" value="<?=$row_slt['slt_company_boss']?>" class="form-control" placeholder="" />
                                        </div>
                                        <label for="slt_company_uptae" class="col-sm-2 col-form-label">업태/업종 <b class="text-danger">*</b></label>
                                        <div class="col-sm-4">
                                            <div class="form-inline w-100">
                                                <input type="text" name="slt_company_uptae" id="slt_company_uptae" value="<?=$row_slt['slt_company_uptae']?>" class="form-control w-45" placeholder="업태">
                                                <span class="mx-2">/</span>
                                                <input type="text" name="slt_company_upjong" id="slt_company_upjong" value="<?=$row_slt['slt_company_upjong']?>"  class="form-control w-45" placeholder="업종">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="slt_company_num" class="col-sm-2 col-form-label">사업자등록번호 <b class="text-danger">*</b></label>
                                        <div class="col-sm-4">
                                            <input type="text" name="slt_company_num" id="slt_company_num" value="<?=$row_slt['slt_company_num']?>" class="form-control" numberOnly placeholder="'-'없이 입력바랍니다." />
                                        </div>
                                        <label for="slt_company_tongsin" class="col-sm-2 col-form-label">통신판매업신고번호</label>
                                        <div class="col-sm-4">
                                            <input type="text" name="slt_company_tongsin" id="slt_company_tongsin" value="<?=$row_slt['slt_company_tongsin']?>" class="form-control" placeholder="" />
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="slt_company_hp1" class="col-sm-2 col-form-label">사업장 연락처1</label>
                                        <div class="col-sm-4">
                                            <input type="text" name="slt_company_hp1" id="slt_company_hp1" value="<?=$row_slt['slt_company_hp1']?>" class="form-control" numberOnly placeholder="'-'없이 입력바랍니다." />
                                        </div>
                                        <label for="slt_company_hp2" class="col-sm-2 col-form-label">사업장 연락처2</label>
                                        <div class="col-sm-4">
                                            <input type="text" name="slt_company_hp2" id="slt_company_hp2" value="<?=$row_slt['slt_company_hp2']?>" class="form-control" numberOnly placeholder="'-'없이 입력바랍니다." />
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="slt_company_hp1" class="col-sm-2 col-form-label">사업장 주소</label>
                                        <div class="col-sm-4">
                                            <p class="form-inline">
                                                <input type="text" class="form-control" name="slt_company_zip" id="slt_company_zip" value="" style="width:100px;" placeholder="" readonly="">
                                                <button type="button" class="btn btn-secondary ml-2" onclick="DaumPostcode('slt_company_zip', 'slt_company_add1', 'slt_company_add2', 'wrap_zip2', {
                                                    hiddenFields: {
                                                        jibeon: 'slt_company_addr_jibeon',
                                                        sido: 'slt_company_sido',
                                                        gugun: 'slt_company_gugun',
                                                        dong: 'slt_company_dong',
                                                        hdong: 'slt_company_hdong',
                                                        lat: 'slt_company_lat',
                                                        lng: 'slt_company_lng'
                                                    }
                                                });">우편번호</button>
                                            </p>
                                            <div id="wrap_zip2" style="display:none;border:1px solid;width:100%;height:300px;margin:5px 0;position:relative">
                                                <img src="//i1.daumcdn.net/localimg/localimages/07/postcode/320/close.png" id="btnFoldWrap" style="cursor:pointer;position:absolute;right:0px;top:-1px;z-index:1" onclick="foldDaumPostcode('wrap_zip2')" alt="접기 버튼">
                                            </div>
                                            <p>
                                                <input type="text" class="form-control" name="slt_company_add1" id="slt_company_add1" value="" placeholder="" readonly="">
                                            </p>
                                            <p>
                                                <input type="text" class="form-control" name="slt_company_add2" id="slt_company_add2" value="" placeholder="">
                                            </p>
                                            <input type="hidden" name="slt_company_addr_jibeon" id="slt_company_addr_jibeon" value="<?php echo $row_slt['slt_company_addr_jibeon']; ?>">
                                            <input type="hidden" name="slt_company_sido" id="slt_company_sido" value="<?php echo get_text($row_slt['slt_company_sido']); ?>">
                                            <input type="hidden" name="slt_company_gugun" id="slt_company_gugun" value="<?php echo get_text($row_slt['slt_company_gugun']); ?>">
                                            <input type="hidden" name="slt_company_dong" id="slt_company_dong" value="<?php echo get_text($row_slt['slt_company_dong']); ?>">
                                            <input type="hidden" name="slt_company_hdong" id="slt_company_hdong" value="<?php echo get_text($row_slt['slt_company_hdong']); ?>">
                                            <input type="hidden" name="slt_company_lat" id="slt_company_lat" value="<?php echo $row_slt['slt_company_lat'] ?>" <?php echo $required ?> class="frm_input" size="30" maxlength="30" placeholder="위도">
                                            <input type="hidden" name="slt_company_lng" id="slt_company_lng" value="<?php echo $row_slt['slt_company_lng'] ?>" <?php echo $required ?> class="frm_input" size="30" maxlength="30" placeholder="경도">
                                        </div>
                                        <div class="col-sm-6">
                                            <div style="background-color:#f9f9f9; border: 1px solid #d7d7d7; width:100%; margin-top:1px; height:350px; border-radius:4px;" id="st_map"></div>
                                            <div id="clickLatlng"></div>
                                            <p id="result"></p>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="slt_bank" class="col-sm-2 col-form-label">정산정보</label>
                                        <div class="col-sm-4">
                                            <select class="form-control" name="slt_bank" id="slt_bank">
                                                <?=$arr_ct_refund_bank_option?>
                                            </select>
                                        </div>
                                        <label for="slt_tax_email" class="col-sm-2 col-form-label">세금계산서 이메일</label>
                                        <div class="col-sm-4">
                                            <input type="text" name="slt_tax_email" id="slt_tax_email" value="<?=$row_slt['slt_tax_email']?>" class="form-control" placeholder="" />
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="slt_bank" class="col-sm-2 col-form-label"></label>
                                        <div class="col-sm-4">
                                            <input type="text" name="slt_bank_account" id="slt_bank_account" value="<?=$row_slt['slt_bank_account']?>"  class="form-control" placeholder="'-'없이 계좌번호 입력해주세요">
                                        </div>
                                        <label for="slt_commission" class="col-sm-2 col-form-label">수수료%</label>
                                        <div class="col-sm-4">
                                            <div class="input-group mb-0">
                                                <input type="text" name="slt_commission" id="slt_commission" value="<?=$row_slt['slt_commission']?>" class="form-control" maxlength="2" numberOnly>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="slt_bank" class="col-sm-2 col-form-label"></label>
                                        <div class="col-sm-4">
                                            <input type="text" name="slt_bank_name" id="slt_bank_name" value="<?=$row_slt['slt_bank_name']?>"  class="form-control" placeholder="예금주 입력 해주세요">
                                        </div>
                                        <label for="slt_commission" class="col-sm-2 col-form-label"></label>
                                        <div class="col-sm-4">
                                            <P class="mb-0">* 정산시 기준 수수료율이며 등록한 수수료가 없을 경우 쇼핑몰 기본설정값이 적용됩니다.</P>
                                            <P class="mb-0">* 결제수수료를 포함하여 입력바랍니다.</P>
                                        </div>
                                    </div>
                                    <?php
                                    $file_labels = [
                                        1 => '사업자등록증',
                                        2 => '통신판매신고증',
                                        3 => '통장사본'
                                    ];
                                    foreach($file_labels as $i => $label) { ?>
                                        <div class="form-group row">
                                            <label for="slt_file<?=$i?>" class="col-sm-2 col-form-label"><?=$label?></label>
                                            <div class="col-sm-10">
                                                <div class="<?php echo (isset($file_info[$i])) ? 'input-group' : ''; ?> mb-0" id="input_group<?=$i?>">
                                                    <?php if(isset($file_info[$i])) { ?>
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" onclick="deleteFile(<?=$i?>)" style="cursor: pointer;">
                                                                <i class="fa fa-remove"></i>
                                                            </span>
                                                        </div>
                                                    <?php } ?>
                                                    <label class="custom-file">
                                                        <input type="file" id="slt_file<?=$i?>" name="slt_file<?=$i?>" class="custom-file-input">
                                                        <span class="custom-file-label" id="file-label<?=$i?>">
                                                            <?php if(isset($file_info[$i])) { ?>
                                                                <?=$file_info[$i]['bf_source']?>
                                                            <?php } else { ?>
                                                                파일 선택
                                                            <?php } ?>
                                                        </span>
                                                        <input type="hidden" name="file<?=$i?>_delete" id="file<?=$i?>_delete" value="N">
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                                <?
                                $DB->where('mt_idx', $row['mt_idx']);
                                $row_srt = $DB->getone('store_t', '*, idx as srt_idx');

                                // JSON 문자열을 PHP 배열로 변환
                                $selected_categories = [];
                                if ($row_srt && $row_srt['srt_ca_content']) {
                                    $selected_categories = json_decode($row_srt['srt_ca_content'], true);
                                }
                                ?>
                                <div class="tab-pane fade" id="member-5" role="tabpanel" aria-labelledby="member-tab-3">
                                    <!--
                                    <div class="form-group row">
                                        <label for="srt_name" class="col-sm-2 col-form-label">행정동</label>
                                        <div class="col-sm-10">
                                            <input type="text" name="srt_name" id="srt_name" value="<?=$row_srt['srt_name']?>" class="form-control" placeholder="" />
                                        </div>
                                    </div>
                                    -->
                                    <div class="form-group row">
                                        <label for="categories" class="col-sm-2 col-form-label">카테고리 분류 <b class="text-danger">*</b></label>
                                        <div class="col-sm-10">
                                            <style>
                                                .category-container {
                                                    padding: 10px;
                                                    width: 100%;
                                                    color: #495057;
                                                    border: 1px solid #ced4da;
                                                    border-radius: .25rem;
                                                }
                                                .category-row {
                                                    display: flex;
                                                    flex-wrap: wrap;
                                                    align-items: center;
                                                    gap: 10px 20px;
                                                    margin-bottom: 0px;
                                                }
                                                .category-item {
                                                    display: flex;
                                                    align-items: center;
                                                    gap: 10px;
                                                    min-width: 100px;
                                                }

                                            </style>
                                            <?
                                            // 카테고리 데이터 조회
                                            $DB->where('ct_show', 'Y');
                                            $categories = $DB->get('category_upjong_t', null, ['ct_name', 'ct_img1']);

                                            ?>
                                            <!--
                                            <div class="category-container">
                                                <div class="category-row">
                                                    <?php foreach ($categories as $category): ?>
                                                        <div class="category-item">
                                                            <input type="checkbox"
                                                                   name="categories[]"
                                                                   id="cat_<?php echo htmlspecialchars($category['ct_id']); ?>"
                                                                   value="<?php echo htmlspecialchars($category['ct_name']); ?>"
                                                                   class="category-checkbox"
                                                                <?php echo in_array($category['ct_name'], $selected_categories) ? 'checked' : ''; ?>>
                                                            <span class="category-name"><?php echo htmlspecialchars($category['ct_name']); ?></span>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                            -->
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="srt_name" class="col-sm-2 col-form-label">스토어명 <b class="text-danger">*</b></label>
                                        <div class="col-sm-10">
                                            <input type="text" name="srt_name" id="srt_name" value="<?=$row_srt['srt_name']?>" class="form-control" placeholder="" />
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="srt_content" class="col-sm-2 col-form-label">스토어 소개(50자)</label>
                                        <div class="col-sm-10">
                                            <input type="text" name="srt_content" id="srt_content" value="<?=$row_srt['srt_content']?>" class="form-control" placeholder="" />
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="srt_operating_hours" class="col-sm-2 col-form-label">영업시간</label>
                                        <div class="col-sm-4">
                                            <textarea name="srt_operating_hours" id="srt_operating_hours" class="form-control" rows="3"><?=$row_srt['srt_operating_hours']?></textarea>
                                        </div>
                                        <label for="srt_break_time" class="col-sm-2 col-form-label">브레이크 타임</label>
                                        <div class="col-sm-4">
                                            <textarea name="srt_break_time" id="srt_break_time" class="form-control" rows="3"><?=$row_srt['srt_break_time']?></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="ct_show" class="col-sm-2 col-form-label">스토어 사진</label>
                                        <div class="col-sm-10">
                                            <div class="upload-container">
                                                <div class="upload-box" id="uploadSrtImageTrigger1"
                                                     data-existing-image="<?php echo !empty($row_srt['srt_image1']) ? $member_store_url.$row_srt['srt_image1'] : ''; ?>">
                                                    <div class="upload-content">
                                                        <div class="plus">+</div>
                                                        <div class="text">Upload</div>
                                                    </div>
                                                    <button type="button" class="remove-btn">×</button>
                                                </div>
                                            </div>
                                            <input type="file" class="filepond d-none" name="srt_image1" id="srt_image1" accept="image/*">
                                            <!-- 삭제 플래그를 위한 hidden 필드 추가 -->
                                            <input type="hidden" name="srt_image1_delete" id="srt_image1_delete" value="N">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="srt_content" class="col-sm-2 col-form-label">스토어 노출여부</label>
                                        <div class="col-sm-4">
                                            <select name="srt_show" id="srt_show" class="custom-select" data-initial-value="<?=$row['srt_show']?>">
                                                <option value="Y">노출</option>
                                                <option value="N">미노출</option>
                                            </select>
                                        </div>
                                    </div>

                                </div>
                                <?
                                $DB->where('mt_idx', $row['mt_idx']);
                                $row_agy = $DB->getone('agency_t', '*, idx as agy_idx');
                                ?>
                                <div class="tab-pane fade" id="member-6" role="tabpanel" aria-labelledby="member-tab-3">
                                    <div class="form-group row">
                                        <label for="agy_name" class="col-sm-2 col-form-label">사이트명</label>
                                        <div class="col-sm-4">
                                            <input type="text" name="agy_name" id="agy_name" value="<?=$row_agy['agy_name']?>" class="form-control" placeholder="사이트명" />
                                        </div>
                                        <label for="agy_url" class="col-sm-2 col-form-label">URL</label>
                                        <div class="col-sm-4">
                                            <input type="text" name="agy_url" id="agy_url" value="<?=$row_agy['agy_url']?>" class="form-control" placeholder="https://" />
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="agy_content" class="col-sm-2 col-form-label">사이트 소개(50자)</label>
                                        <div class="col-sm-10">
                                            <input type="text" name="agy_content" id="agy_content" value="<?=$row_agy['agy_content']?>" class="form-control" placeholder="" />
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="agy_logo1" class="col-sm-2 col-form-label">사이트 로고</label>
                                        <div class="col-sm-10">
                                            <div class="upload-container">
                                                <div class="upload-box" id="uploadAgyLogoTrigger1"
                                                     data-existing-image="<?php echo !empty($row_agy['agy_logo1']) ? $member_agency_url.$row_agy['agy_logo1'] : ''; ?>">
                                                    <div class="upload-content">
                                                        <div class="plus">+</div>
                                                        <div class="text">Upload</div>
                                                    </div>
                                                    <button type="button" class="remove-btn">×</button>
                                                </div>
                                            </div>
                                            <input type="file" class="filepond d-none" name="agy_logo1" id="agy_logo1" accept="image/*">
                                            <!-- 삭제 플래그를 위한 hidden 필드 추가 -->
                                            <input type="hidden" name="agy_logo1_delete" id="agy_logo1_delete" value="N">
                                        </div>
                                    </div>
                                </div>
                            <? } ?>
                        <?}?>
                    </div>
                    <div class="form-group row justify-content-center margin-top-30">
                        <button type="button"  onclick="history.go(-1);" class="btn btn-outline-secondary mx-1" >목록</button>
                        <button type="button"  onclick="history.go(-1);" class="btn btn-outline-danger mx-1" >탈퇴</button>
                        <button type="submit" class="btn btn-secondary" >저장</button>
                    </div>
                </form>
            </div>
            <script type="text/javascript" src="<?=MNG_HTTP?>/js/fileupload.js?v=<?=$v_txt?>"></script>
            <script>
                // 카테고리 선택은 3개까지 가능
                document.addEventListener('DOMContentLoaded', function() {
                    const checkboxes = document.querySelectorAll('.category-checkbox');
                    const maxChecked = 3;

                    checkboxes.forEach(checkbox => {
                        checkbox.addEventListener('change', function() {
                            const checkedBoxes = document.querySelectorAll('.category-checkbox:checked');

                            if (checkedBoxes.length > maxChecked) {
                                this.checked = false;
                                app.toastr.showError('최대 3개까지만 선택 가능합니다.');
                            }
                        });
                    });
                });

                $(document).ready(function() {
                    // 닉네임 관련 기능
                    const nicknameHandler = {
                        init() {
                            this.bindEvents();
                        },
                        bindEvents() {
                            $('#mt_nickname').on('input', this.handleNicknameInput);
                            $('#mt_nickname_chk_btn').on('click', this.checkNicknameDuplicate);
                        },
                        handleNicknameInput() {
                            $('#mt_nickname_chk').val('N');
                            $('#mt_nickname_chk_btn').text('중복확인').removeClass('d-none');
                            $('#mt_nickname').attr("readonly", false).css("background-color", '');
                        },
                        checkNicknameDuplicate() {
                            const nickname = $('#mt_nickname').val();
                            if(!nickname) {
                                alert("닉네임을 등록해주세요.");
                                $('#mt_nickname').focus();
                                return false;
                            }

                            $.post('./member_update.php', {
                                act: 'chk_mt_nickname',
                                mt_nickname: nickname,
                                mt_idx: $('#mt_idx').val()
                            })
                                .done(function(data) {
                                    if(data === 'N') {
                                        alert('이미 사용중인 닉네임입니다.');
                                        $('#mt_nickname').val('').focus();
                                    } else if(data === 'Y') {
                                        alert('중복확인이 완료되었습니다.');
                                        $('#mt_nickname_chk').val('Y');
                                        $('#mt_nickname').attr("readonly", true).css("background-color", '#e9ecef');
                                        $('#mt_nickname_chk_btn').addClass('d-none');
                                    }
                                })
                                .fail(function() {
                                    alert('중복확인 중 오류가 발생했습니다.');
                                });
                        }
                    };

                    // 탭 관련 기능
                    const tabHandler = {
                        init() {
                            this.initializeTab();
                            this.bindTabEvents();
                        },
                        initializeTab() {
                            const urlParams = new URLSearchParams(window.location.search);
                            const tabParam = urlParams.get('tab');

                            if (tabParam) {
                                this.activateTab(tabParam);
                            }
                        },
                        bindTabEvents() {
                            $('a[data-toggle="tab"]').on('shown.bs.tab', (e) => {
                                const tabId = $(e.target).attr('id');
                                this.updateUrl(tabId);
                            });
                        },
                        activateTab(tabParam) {
                            $('#' + tabParam).tab('show');
                            $('.nav-link').removeClass('active');
                            $('#' + tabParam).addClass('active');
                            $('.tab-pane').removeClass('show active');
                            $(('#' + tabParam.replace('-tab', '')).replace('-1', '-1')).addClass('show active');
                        },
                        updateUrl(tabId) {
                            const newUrl = new URL(window.location.href);
                            newUrl.searchParams.set('tab', tabId);
                            window.history.pushState({}, '', newUrl);
                        }
                    };


                    // 폼 검증 및 제출
                    const formHandler = {
                        init() {
                            this.initializeValidation();
                            this.setInitialValues();
                        },
                        initializeValidation() {
                            $.validator.addMethod("regex", function(value, element, regexp) {
                                var re = new RegExp(regexp);
                                return this.optional(element) || re.test(value);
                            }, "형식이 올바르지 않습니다.");

                            $("#frm_form").validate({
                                submitHandler: this.handleSubmit,
                                rules: {
                                    mt_id: {
                                        required: true,
                                        minlength: 4,
                                        maxlength: 20,
                                        //regex: "^[a-zA-Z0-9_]{6,20}$" // 영문자, 숫자, 언더바 포함
                                        regex: "^[a-z\\d!@#$%^&*()_]{6,20}$" // 영문자, 숫자, 특수기호 포함
                                    },
                                    mt_name: {
                                        required: true,
                                        minlength: 2,
                                        maxlength: 20
                                    },
                                    mt_hp: {
                                        required: true,
                                        regex: /^01[0|1|6|7|8|9][0-9]{3,4}[0-9]{4}$/
                                    },
                                    mt_email: {
                                        required: true,
                                        email: true,
                                        regex: /^[0-9a-zA-Z]([-_.]?[0-9a-zA-Z])*@[0-9a-zA-Z]([-_.]?[0-9a-zA-Z])*\.[a-zA-Z]{2,3}$/
                                    },
                                    mt_nickname: {
                                        minlength: 2,
                                        maxlength: 20
                                    },
                                    mt_pwd: {
                                        minlength: 4,
                                        maxlength: 20
                                    },
                                    mt_pwd_re: {
                                        minlength: 4,
                                        maxlength: 20,
                                        equalTo: "#mt_pwd"
                                    }
                                },
                                messages: {
                                    mt_id: {
                                        required: "아이디를 입력해주세요.",
                                        minlength: "아이디는 최소 4자 이상이어야 합니다.",
                                        maxlength: "아이디는 최대 20자까지만 가능합니다.",
                                        regex: "아이디는 영문자와 숫자, 허용된 특수문자만 사용 가능합니다."
                                    },
                                    mt_name: {
                                        required: "이름을 입력해주세요.",
                                        minlength: "이름은 최소 2자 이상이어야 합니다.",
                                        maxlength: "이름은 최대 20자까지만 가능합니다.",
                                    },
                                    mt_hp: {
                                        required: "휴대폰 번호를 입력해주세요",
                                        regex: "올바른 휴대폰 번호 형식이 아닙니다"
                                    },
                                    mt_email: {
                                        required: "이메일을 입력해주세요",
                                        email: "올바른 이메일 형식을 입력해주세요",
                                        regex: "올바른 이메일 형식이 아닙니다"
                                    },
                                    mt_nickname: { required: "닉네임을 입력해주세요" },
                                    mt_pwd_re: { equalTo: "비밀번호가 일치하지 않습니다" }
                                },
                                errorElement: 'span',
                                errorPlacement: (error, element) => {
                                    error.addClass('invalid-feedback');
                                    element.closest('.col-sm-10').append(error);
                                },
                                highlight: (element) => $(element).addClass('is-invalid'),
                                unhighlight: (element) => $(element).removeClass('is-invalid')
                            });
                        },

                        handleSubmit(form) {
                            <? if($_act=='update'){?>
                            // 닉네임 중복확인 체크
                            if($('#mt_nickname_chk').val() !== 'Y') {
                                app.toastr.showError('닉네임 중복확인을 해주세요.');
                                return false;
                            }
                            <? } ?>

                            const formData = new FormData(form);

                            $.ajax({
                                url: './member_update.php',
                                type: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
                                beforeSend: () => $('#splinner_modal').modal('show'),
                                success: (response) => {
                                    $('#splinner_modal').modal('hide');
                                    if(response.success) {
                                        app.toastr.showSuccess(response.message, response.redirect);
                                    } else {
                                        app.toastr.showError(response.message);
                                    }
                                },
                                error: (xhr, status, error) => {
                                    $('#splinner_modal').modal('hide');
                                    app.toastr.showError(response.message);
                                }
                            });
                            return false;
                        },
                        setInitialValues() {
                            // PHP에서 전달된 초기값 설정
                            const fields = [
                                'mt_status', 'mt_smsing', 'mt_mailing',
                                'mt_pushing1', 'mt_seller', 'mt_rank',
                                'mt_promo_rank', 'mt_type' , 'mt_push' , 'mt_notice_push'
                            ];

                            fields.forEach(field => {
                                const value = $(`#${field}`).data('initial-value');
                                if(value) {
                                    $(`#${field}`).val(value);
                                }
                            });
                        }
                    };

                    // 파일 관련 핸들러
                    const fileHandler = {
                        init() {
                            this.bindEvents();
                        },

                        bindEvents() {
                            // 파일 선택 이벤트
                            $('.custom-file-input').on('change', this.handleFileSelect);
                        },

                        handleFileSelect(e) {
                            const fileName = e.target.files[0]?.name || "파일 선택";
                            const fileNum = this.id.replace('slt_file', '');
                            $(`#file-label${fileNum}`).text(fileName);
                            $(`#file${fileNum}_delete`).val('N');
                        }
                    };

                    // 초기화
                    nicknameHandler.init();
                    tabHandler.init();
                    formHandler.init();
                    fileHandler.init();
                });

                // 파일 삭제 함수
                function deleteFile(fileNum) {
                    if(confirm('파일을 삭제하시겠습니까?')) {
                        $(`#file${fileNum}_delete`).val('Y');
                        $(`#file-label${fileNum}`).text('파일 선택');
                        $(`#input_group${fileNum}`).removeClass('input-group');
                        $(`.input-group-prepend span[onclick="deleteFile(${fileNum})"]`).hide();
                    }
                }

                function f_mt_nickname_chk() {
                    if($('#mt_nickname').val()=="") {
                        alert("닉네임을 등록해주세요.");
                        $('#mt_nickname').focus();
                        return false;
                    }
                    $('#mt_nickname_chk').val('Y');
                    /*
                    $.post('./member_update.php', {act: 'chk_mt_nickname', mt_nickname: $('#mt_nickname').val(), mt_idx: $('#mt_idx').val()}, function (data) {
                        if(data=='N') {
                            alert('이미 사용중인 닉네임입니다.');
                            $('#mt_nickname').val('');
                            $('#mt_nickname').focus();
                            return false;
                        } else if(data=='Y') {
                            alert('중복확인이 완료되었습니다.');
                            $('#mt_nickname_chk').val('Y');
                            $('#mt_nickname').attr("readonly", true);
                            $('#mt_nickname').css("background-color", '#e9ecef');
                            $('#mt_nickname_chk_btn').addClass('d-none');
                        }
                    });

                     */
                }

                <? if($_act=='update'){?>

                // 각 파일 입력 요소에 대해 이벤트 리스너 추가
                <? if($seller_info) { ?>
                updateFileName('slt_file1', 'file-label1');
                updateFileName('slt_file2', 'file-label2');
                updateFileName('slt_file3', 'file-label3');
                <? } ?>

                // 이미지 업로더 설정 및 초기화
                const imageUploader = createImageUploader({
                    // filePatterns를 constraints 안이 아닌 최상위 레벨에 위치시킴
                    filePatterns: {
                        'mt_image1': {
                            triggerPrefix: 'uploadMtImageTrigger1'
                        },
                        <? if($seller_info) { ?>
                        'srt_image1': {
                            triggerPrefix: 'uploadSrtImageTrigger1'
                        },
                        <? } ?>
                        <? if($agency_info) { ?>
                        'agy_logo1': {
                            triggerPrefix: 'uploadAgyLogoTrigger1'
                        }
                        <? } ?>
                    },
                    constraints: {
                        'mt_image1': {
                            maxFileSize: 5 * 1024 * 1024,
                            allowedFileTypes: ['image/jpeg', 'image/png', 'image/gif'],
                            imageMinWidth: 100,
                            imageMinHeight: 100,
                            imageMaxWidth: 4000,
                            imageMaxHeight: 3000,
                            errorMessages: {
                                fileSize: '이미지는 5MB를 초과할 수 없습니다.',
                                fileType: '허용된 파일 형식: JPG, PNG, GIF',
                                imageSize: '이미지 크기는 100x100px에서 4000x3000px 사이여야 합니다.'
                            }
                        },
                        <? if($seller_info) { ?>
                        'srt_image1': {
                            maxFileSize: 5 * 1024 * 1024,
                            allowedFileTypes: ['image/jpeg', 'image/png', 'image/gif'],
                            imageMinWidth: 100,
                            imageMinHeight: 100,
                            imageMaxWidth: 4000,
                            imageMaxHeight: 3000,
                            errorMessages: {
                                fileSize: '이미지는 5MB를 초과할 수 없습니다.',
                                fileType: '허용된 파일 형식: JPG, PNG, GIF',
                                imageSize: '이미지 크기는 100x100px에서 4000x3000px 사이여야 합니다.'
                            }
                        },
                        <? } ?>
                        <? if($agency_info) { ?>
                        'agy_logo1': {
                            maxFileSize: 5 * 1024 * 1024,
                            allowedFileTypes: ['image/jpeg', 'image/png', 'image/gif'],
                            imageMinWidth: 100,
                            imageMinHeight: 100,
                            imageMaxWidth: 4000,
                            imageMaxHeight: 3000,
                            errorMessages: {
                                fileSize: '이미지는 5MB를 초과할 수 없습니다.',
                                fileType: '허용된 파일 형식: JPG, PNG, GIF',
                                imageSize: '이미지 크기는 100x100px에서 600x600px 사이여야 합니다.'
                            }
                        }
                        <? } ?>
                    }
                });

                // 이미지 업로더 초기화
                document.addEventListener('DOMContentLoaded', function() {
                    imageUploader.init();
                });


                <? } ?>


                // 지도 초기화 및 표시 함수
                // 지도 초기화 및 표시 함수
                function initMap() {
                    console.log('지도 초기화 시작');

                    // 좌표값이 있는지 확인
                    const lat = document.getElementById('slt_company_lat').value;
                    const lng = document.getElementById('slt_company_lng').value;

                    console.log('좌표값:', lat, lng);

                    // 좌표값이 없으면 지도 초기화하지 않음
                    if (!lat || !lng) {
                        console.log('좌표값이 없습니다.');
                        return;
                    }

                    // 카카오맵 API가 로드되었는지 확인
                    if (typeof kakao === 'undefined' || !kakao.maps) {
                        console.warn('카카오맵 API가 로드되지 않았습니다.');
                        return;
                    }

                    // 지도 컨테이너 요소 확인
                    const mapContainer = document.getElementById('st_map');
                    if (!mapContainer) {
                        console.warn('지도를 표시할 요소가 없습니다.');
                        return;
                    }

                    // 좌표 변환
                    const position = new kakao.maps.LatLng(parseFloat(lat), parseFloat(lng));

                    // 지도 옵션 설정
                    const mapOption = {
                        center: position, // 지도의 중심좌표
                        level: 3 // 지도의 확대 레벨
                    };

                    // 지도 생성
                    const map = new kakao.maps.Map(mapContainer, mapOption);

                    // 마커 생성 (드래그 가능하도록 설정)
                    const marker = new kakao.maps.Marker({
                        position: position,
                        draggable: true // 마커를 드래그 가능하도록 설정
                    });

                    // 마커를 지도에 표시
                    marker.setMap(map);

                    // 마커 드래그 이벤트 처리
                    kakao.maps.event.addListener(marker, 'dragend', function() {
                        // 마커 위치를 기준으로 좌표 업데이트
                        const markerPosition = marker.getPosition();

                        // hidden 필드 업데이트
                        updateCoordinates(markerPosition);

                        // 주소 정보 업데이트
                        updateAddressInfo(markerPosition);
                    });

                    // 지도 클릭 이벤트 처리 - 마커 위치 변경
                    kakao.maps.event.addListener(map, 'click', function(mouseEvent) {
                        // 클릭한 위치로 마커 이동
                        const clickPosition = mouseEvent.latLng;
                        marker.setPosition(clickPosition);

                        // hidden 필드 업데이트
                        updateCoordinates(clickPosition);

                        // 주소 정보 업데이트
                        updateAddressInfo(clickPosition);
                    });

                    // 지도 드래그 종료 이벤트 처리
                    kakao.maps.event.addListener(map, 'dragend', function() {
                        // 지도 중심 좌표 가져오기
                        const center = map.getCenter();

                        // 지도 중심 좌표 표시
                        if (document.getElementById('clickLatlng')) {
                            document.getElementById('clickLatlng').innerHTML = '현재 지도 중심 좌표는 ' +
                                center.getLat() + ', ' + center.getLng() + ' 입니다';
                        }

                        // 마커를 지도 중심으로 이동
                        marker.setPosition(center);

                        // hidden 필드 업데이트 (지도 중심 좌표로)
                        updateCoordinates(center);

                        // 주소 정보 업데이트
                        updateAddressInfo(center);
                    });

                    // 초기 좌표 정보 표시
                    if (document.getElementById('clickLatlng')) {
                        document.getElementById('clickLatlng').innerHTML = '현재 좌표는 ' +
                            position.getLat() + ', ' + position.getLng() + ' 입니다';
                    }

                    // 초기 주소 정보 업데이트
                    updateAddressInfo(position);

                    console.log('지도 초기화 완료');

                    // 안내 메시지 업데이트
                    const infoElement = document.querySelector('.frm_info.strMaps');
                    if (infoElement) {
                        infoElement.innerHTML = '지도를 드래그하거나 클릭하여 정확한 위치를 설정해주세요.';
                    }

                    // 좌표 업데이트 함수
                    function updateCoordinates(position) {
                        // hidden 필드에 좌표 저장
                        const latField = document.getElementById('slt_company_lat');
                        const lngField = document.getElementById('slt_company_lng');

                        if (latField && lngField) {
                            latField.value = position.getLat();
                            lngField.value = position.getLng();
                            console.log('좌표 업데이트:', position.getLat(), position.getLng());
                        }

                        // 좌표 정보 표시
                        if (document.getElementById('clickLatlng')) {
                            document.getElementById('clickLatlng').innerHTML = '현재 좌표는 ' +
                                position.getLat() + ', ' + position.getLng() + ' 입니다';
                        }
                    }
                }

                // 주소 정보 업데이트 함수
                function updateAddressInfo(position) {
                    // 주소 정보 가져오기
                    const geocoder = new kakao.maps.services.Geocoder();

                    // 좌표로 주소 정보 가져오기
                    geocoder.coord2Address(position.getLng(), position.getLat(), function(result, status) {
                        if (status === kakao.maps.services.Status.OK && result[0]) {
                            const addr = result[0];

                            // 주소 표시
                            if (document.getElementById('result')) {
                                document.getElementById('result').innerHTML = '현재 위치의 주소는 ' +
                                    (addr.address.address_name || '알 수 없음') + ' 입니다';
                            }

                            // 도로명 주소 정보
                            const roadAddr = addr.road_address ? addr.road_address.address_name : '';

                            // 지번 주소 정보
                            const jibunAddr = addr.address ? addr.address.address_name : '';

                            // 상세 주소 정보
                            const sido = addr.address ? addr.address.region_1depth_name : '';
                            const sigungu = addr.address ? addr.address.region_2depth_name : '';
                            const bname = addr.address ? addr.address.region_3depth_name : '';
                            const hname = addr.address ? addr.address.region_3depth_h_name || bname : '';

                            console.log('주소 정보:', {
                                roadAddr,
                                jibunAddr,
                                sido,
                                sigungu,
                                bname,
                                hname
                            });

                            // 주소 필드 업데이트 (있을 경우에만)
                            if (document.getElementById('slt_company_add1')) {
                                document.getElementById('slt_company_add1').value = roadAddr || jibunAddr;
                            }

                            // 지번 주소 필드 업데이트
                            if (document.getElementById('slt_company_addr_jibeon')) {
                                document.getElementById('slt_company_addr_jibeon').value = jibunAddr;
                            }

                            // 시도 필드 업데이트
                            if (document.getElementById('slt_company_sido')) {
                                document.getElementById('slt_company_sido').value = sido;
                            }

                            // 구군 필드 업데이트
                            if (document.getElementById('slt_company_gugun')) {
                                document.getElementById('slt_company_gugun').value = sigungu;
                            }

                            // 동 필드 업데이트
                            if (document.getElementById('slt_company_dong')) {
                                document.getElementById('slt_company_dong').value = bname;
                            }

                            // 행정동 필드 업데이트
                            if (document.getElementById('slt_company_hdong')) {
                                document.getElementById('slt_company_hdong').value = hname;
                            }

                            // 우편번호 조회 (필요한 경우)
                            if (document.getElementById('slt_company_zip')) {
                                // 주소로 우편번호 검색
                                geocoder.addressSearch(roadAddr || jibunAddr, function(zipResult, zipStatus) {
                                    if (zipStatus === kakao.maps.services.Status.OK && zipResult[0] && zipResult[0].road_address) {
                                        const zipCode = zipResult[0].road_address.zone_no || '';
                                        document.getElementById('slt_company_zip').value = zipCode;
                                        console.log('우편번호 업데이트:', zipCode);
                                    }
                                });
                            }
                        } else {
                            console.error('주소 정보 가져오기 실패:', status);
                        }
                    });
                }

                // 페이지 로드 후 지도 초기화
                document.addEventListener('DOMContentLoaded', function() {
                    console.log('DOM 로드 완료');

                    // 탭 변경 이벤트 감지
                    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                        const targetId = $(e.target).attr('id');
                        console.log('탭 변경:', targetId);

                        // 사업자 정보 탭이 활성화되면 지도 초기화
                        if (targetId === 'member-tab-4') {
                            // 약간의 지연을 두고 지도 초기화 (탭 전환 후 DOM이 완전히 렌더링되도록)
                            setTimeout(initMap, 100);
                        }
                    });

                    // 페이지 로드 시 사업자 정보 탭이 활성화되어 있으면 지도 초기화
                    if ($('#member-tab-4').hasClass('active')) {
                        console.log('사업자 정보 탭이 활성화되어 있습니다.');
                        setTimeout(initMap, 100);
                    }
                });
            </script>

        </div>
    </div>
</div>
<!-- //END PAGE CONTENT CONTAINER -->
<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/foot.inc.php";
?>
