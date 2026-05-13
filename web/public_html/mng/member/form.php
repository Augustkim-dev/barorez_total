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
//    $DB->where('board', 'seller');
//    $DB->where('bo_id', $_GET['mt_idx']);
//    $DB->orderBy('bf_no', 'asc');
//    $files = $DB->get('board_file_t');
//
//    // 첨부파일 정보를 배열로 정리
//    $file_info = array();
//    if($files) {
//        foreach($files as $file) {
//            $file_info[$file['bf_no']] = $file;
//        }
//    }

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

//$DB->orderBy("idx", "asc");
//$grade_list = $DB->get("member_grade_t");
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
                </ul>
            </div>
            <div class="card-body">
                <form method="post" name="frm_form" id="frm_form" action="./update.php" target="hidden_ifrm" enctype="multipart/form-data">
                    <input type="hidden" name="act" id="act" value="<?=$_act?>" />
                    <input type="hidden" name="mt_idx" id="mt_idx" value="<?=$row['mt_idx']?>" />
                    <input type="hidden" name="type" id="type" value="<?=$_GET['type']?>" />
                    <input type="hidden" name="file_count" id="file_count" value="<?=$file_count?>" />
                    <input type="hidden" name="board" id="board" value="<?=$board?>" />
                    <div class="tab-content margin-top-15" id="myTabContent">
                        <!-- 회원 정보 -->
                        <div class="tab-pane fade show active" id="member-1" role="tabpanel" aria-labelledby="member-tab-1">
                            <?php if($_GET['type'] === 'approval'){?>
                            <div class="form-group row align-items-center">
                                <label for="mt_id" class="col-sm-2 col-form-label">승인상태 <b class="text-danger">*</b></label>
                                <div class="col-sm-4">
                                    <?php foreach($arr_appr_status as $key=>$value) {?>
                                        <?php
                                        $local_class_name = ($row['mt_appr'] == $key) ? 'btn-secondary' : 'btn-outline-secondary';
                                        ?>
                                        <button type="button" class="btn local-search-btn mr-2 <?php echo $local_class_name?>" data-appr="<?php echo $key?>" ><?php echo $value ?></button>
                                    <?php }?>
                                </div>
                                <input type="hidden" name="mt_appr" id="mt_appr" value="<?=$row['mt_appr']?>" />
                                <label for="mt_level" class="col-sm-2 col-form-label">회원유형</label>
                                <div class="col-sm-4">
                                    <span><?php echo $arr_member_status[$row['mt_level']]?></span>
                                </div>
                            </div>
                            <?php } ?>
                            <script>
                                $(document).ready(function() {
                                    $('.local-search-btn').on('click', function(){
                                        let local = $(this).attr('data-appr')
                                        $('#mt_appr').val(local);
                                        console.log(local);
                                        $('.local-search-btn').removeClass('btn-secondary').addClass('btn-outline-secondary');
                                        $(this).removeClass('btn-outline-secondary').addClass('btn-secondary');
                                    })
                                })
                            </script>
                            <div class="form-group row align-items-center">
                                <label for="mt_id" class="col-sm-2 col-form-label">아이디 <b class="text-danger">*</b></label>
                                <div class="col-sm-4">
                                    <span><?php echo $row['mt_id']?></span>
                                </div>
                                <label for="mt_type" class="col-sm-2 col-form-label">가입유형</label>
                                <div class="col-sm-4">
                                    <span><?php echo $arr_mt_type[$row['mt_type']]?></span>
                                    <small class="form-text">가입유형은 일반, 카카오, 네이버, 구글, 애플 유형으로 구분됩니다.</small>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="mt_pwd" class="col-sm-2 col-form-label">비밀번호 <b class="text-danger">*</b></label>
                                <div class="col-sm-4">
                                    <input type="password" name="mt_pwd" id="mt_pwd" value="" class="form-control" minlength="8" maxlength="50" <?=$row['mt_type'] !== 1 || $row['mt_level'] === 1 ? 'disabled' : '' ?>>
                                </div>
                                <label for="mt_hp" class="col-sm-2 col-form-label">휴대폰 번호 <b class="text-danger">*</b></label>
                                <div class="col-sm-4">
                                    <input type="text" name="mt_hp" id="mt_hp" value="<?=$row['mt_hp']?>" class="form-control" numberOnly placeholder="'-'없이 입력바랍니다." <?=$row['mt_level'] === 1 ? 'disabled' : '' ?>>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="mt_pwd" class="col-sm-2 col-form-label">비밀번호 확인</label>
                                <div class="col-sm-4">
                                    <input type="password" name="mt_pwd_re" id="mt_pwd_re" value="" class="form-control" minlength="8" maxlength="50" <?=$row['mt_type'] !== 1 || $row['mt_level'] === 1 ? 'disabled' : '' ?>>
                                    <small class="form-text">비밀번호 변경시에는 비밀번호 확인까지 입력바랍니다.</small>
                                </div>
                                <label for="wrap_zip1" class="col-sm-2 col-form-label">닉네임 <b class="text-danger">*</b></label>
                                <div class="col-sm-4">
                                    <p class="form-inline flex-nowrap">
                                        <input type="hidden" name="mt_nickname_chk" id="mt_nickname_chk" value="Y" />
                                        <input type="text" class="form-control" style="<?=$row['mt_level'] === 1 ? 'width: 100%' : 'width: 80%' ?>" name="mt_nickname" id="mt_nickname" value="<?php echo $row['mt_nickname']?>"  placeholder=""  <?=$row['mt_level'] === 1 ? 'disabled' : '' ?>>
                                        <?=$row['mt_level'] !== 1 ? '<button type="button" class="btn btn-gray ml-2" id="mt_nickname_chk_btn" onclick="f_mt_nickname_chk();">중복확인</button>' : '' ?>
                                    </p>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-2">
                                    <label for="mt_name" class="col-form-label d-block form-group">이름 <b class="text-danger">*</b></label>
                                    <?php if($_GET['type'] !== 'approval'){?>
                                    <label for="mt_type" class="col-form-label d-block">회원구분</label>
                                    <?php } ?>
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" name="mt_name" id="mt_name" value="<?=$row['mt_name']?>" class="form-control form-group" <?=$row['mt_level'] === 1 ? 'disabled' : '' ?>>
                                    <?php if($_GET['type'] !== 'approval'){?>
                                    <?php if($row['mt_level'] === 1){
                                        echo '탈퇴회원';
                                    }else{?>
                                    <select name="mt_level" id="mt_level" class="form-control">
                                        <?php
                                        foreach($arr_member_status as $key => $value) {?>
                                           <option value="<?=$key?>" <?=$row['mt_level'] === $key ? 'selected' : ''?>><?=$value?></option>
                                        <?php } ?>
                                    </select>
                                    <?php } ?>
                                    <?php }?>
                                </div>
<!--                                <label for="mt_image1" class="col-sm-2 col-form-label">프로필 사진</label>-->
<!--                                <div class="col-sm-4">-->
<!--                                     --><?php //if($row['mt_level'] !== 1){?>
<!--                                    <div class="upload-container">-->
<!--                                        <div class="upload-box" id="uploadMtImageTrigger1" data-existing-image="--><?php //echo !empty($row['mt_image1']) ? $member_img_url.$row['mt_image1'] : ''; ?><!--">-->
<!--                                            <div class="upload-content">-->
<!--                                                <div class="plus">+</div>-->
<!--                                                <div class="text">Upload</div>-->
<!--                                            </div>-->
<!--                                            <button type="button" class="remove-btn">×</button>-->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                    <input type="file" class="filepond d-none" name="mt_image1" id="mt_image1" accept="image/*">-->
<!--                                     삭제 플래그를 위한 hidden 필드 추가 -->
<!--                                    <input type="hidden" name="mt_image1_delete" id="mt_image1_delete" value="N">-->
<!--                                    --><?php //}else{?>
<!--                                         <div class="upload-container">-->
<!--                                             <div class="upload-box" id="uploadMtImageTrigger1" data-existing-image="--><?php //echo !empty($row['mt_image1']) ? $member_img_url.$row['mt_image1'] : ''; ?><!--">-->
<!--                                                 <div class="upload-content">-->
<!--                                                     <div class="plus">+</div>-->
<!--                                                     <div class="text">Upload</div>-->
<!--                                                 </div>-->
<!--                                             </div>-->
<!--                                         </div>-->
<!--                                         <input type="file" class="filepond d-none" name="mt_image1" id="mt_image1" accept="image/*">-->
<!--                                    --><?php //}?>
<!--                                </div>-->
                            </div>
                        </div>

                        <!-- 접속 정보 -->
                        <div class="tab-pane fade" id="member-3" role="tabpanel" aria-labelledby="member-tab-3">
                            <div class="form-group row align-items-center">
                                <label for="mt_status" class="col-sm-2 col-form-label">로그인가능</label>
                                <div class="col-sm-4">
                                    <span><?=$arr_mt_status[$row['mt_status']]?></span>
<!--                                    <small id="mt_status_help" class="form-text text-muted">* 'N'으로 선택시 로그인이 차단됩니다.</small>-->
                                </div>
                                <label for="del_status" class="col-sm-2 col-form-label">회원 상태</label>
                                <div class="col-sm-4">
                                    <?php if($row['mt_level'] !== 1){?>
                                        <select name="del_status" id="del_status" class="custom-select" data-initial-value="<?=$row['del_status']?>">
                                            <?php foreach($arr_del_status as $key=>$value) {?>
                                                <option value="<?php echo $key ?>" <?=$row['del_status'] === $key ? 'selected' : ''?>>
                                                    <?php echo $value?>
                                                </option>
                                            <?php }?>
                                        </select>
                                    <?php }else{
                                        echo $arr_del_status[$row['del_status']];
                                    } ?>
                                </div>
                            </div>
                            <div class="form-group row align-items-center">
                                <label for="mt_ldate" class="col-sm-2 col-form-label">로그인일시</label>
                                <div class="col-sm-4">
                                    <span><?=DateType($row['mt_ldate'], 4)?></span>
                                </div>
                                <label for="mt_ldate" class="col-sm-2 col-form-label">탈퇴일시</label>
                                <div class="col-sm-4">
                                    <span><?=DateType($row['mt_rdate'], 4)?></span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="mt_retire_memo" class="col-sm-2 col-form-label">회원탈퇴메모</label>
                                <div class="col-sm-10">
                                    <textarea name="mt_retire_memo" id="mt_retire_memo" class="form-control" rows="3"><?=$row['mt_retire_memo']?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row justify-content-center margin-top-30">
                        <button type="button"  onclick="location.href='./list.php?type=<?=$_GET['type']?>'" class="btn btn-outline-secondary mx-1" >목록</button>
                        <?php if($_GET['type'] !== 'approval'){?>
                        <?php if($row['mt_level'] === 1){?>
                        <button type="button" onclick="f_retire_mem('<?=$row['mt_idx']?>');" class="btn btn-secondary mx-1" >저장</button>
                        <?php }else{?>
                        <button type="button" onclick="f_retire_mem('<?=$row['mt_idx']?>');" class="btn btn-outline-danger mx-1" >탈퇴</button>
                        <?php }?>
                        <?php } ?>
                        <?php if($row['mt_level'] !== 1){?>
                        <button type="submit" class="btn btn-secondary mx-1" >저장</button>
                        <?php }else{?>
                        <button type="button" onclick="f_restoration_mem('<?=$row['mt_idx']?>');" class="btn btn-outline-danger mx-1" >복구</button>
                        <?php }?>
                    </div>
                </form>
            </div>
            <script type="text/javascript" src="<?=MNG_HTTP?>/js/fileupload.js?v=<?=$v_txt?>"></script>
            <script>
                $(document).ready(function() {
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

                            $.post('./update.php', {
                                act: 'chk_mt_nickname',
                                mt_nickname: nickname,
                            })
                                .done(function(data) {
                                    if(data?.message === 'N') {
                                        alert('이미 사용중인 닉네임입니다.');
                                        $('#mt_nickname').val('').focus();
                                    } else if(data?.message === 'Y') {
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
                                    mt_name: {
                                        required: true,
                                        minlength: 2,
                                        maxlength: 20
                                    },
                                    mt_hp: {
                                        required: true,
                                        regex: /^01[0|1|6|7|8|9][0-9]{3,4}[0-9]{4}$/
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
                                    mt_name: {
                                        required: "이름을 입력해주세요.",
                                        minlength: "이름은 최소 2자 이상이어야 합니다.",
                                        maxlength: "이름은 최대 20자까지만 가능합니다.",
                                    },
                                    mt_hp: {
                                        required: "휴대폰 번호를 입력해주세요",
                                        regex: "올바른 휴대폰 번호 형식이 아닙니다"
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
                                url: './update.php',
                                type: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
                                beforeSend: () => $('#splinner_modal').modal('show'),
                                success: (response) => {
                                    console.log(response)
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
                    let nickname = $('#mt_nickname').val();
                    if(nickname=="") {
                        alert("닉네임을 등록해주세요.");
                        nickname.focus();
                        return false;
                    }
                    $('#mt_nickname_chk').val('Y');

                    $.post('./update.php', {
                        act: 'chk_mt_nickname',
                        mt_nickname: nickname,
                    })
                        .done(function(data) {
                            if(data?.message === 'N') {
                                alert('이미 사용중인 닉네임입니다.');
                                $('#mt_nickname').val('').focus();
                            } else if(data?.message === 'Y') {
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

                <? if($_act=='update'){?>

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
            </script>

        </div>
    </div>
</div>
<!-- //END PAGE CONTENT CONTAINER -->
<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/foot.inc.php";
?>
