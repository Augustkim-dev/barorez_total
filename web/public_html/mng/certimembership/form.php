<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu='14';
$chk_sub_menu='1';
include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.menu.inc.php";
//include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.sidebar.inc.php";

$chk_post_code = "Y";
//$chk_ckeditor = "Y";

if ($_GET['act'] == "update") {
    $DB->where('gmat_idx', $_GET['nt_idx']);
    $row = $DB->getone($CFG_TBL['golf_membership']['auth'], '*, gmat_idx as nt_idx');
    $_act = "update";
    $_act_txt = " 수정";

} else if ($_GET['act'] == "view") {
  $DB->where('gmat_idx', $_GET['nt_idx']);
  $row = $DB->getone($CFG_TBL['golf_membership']['auth'], '*, gmat_idx as nt_idx');
  $_act = "view";
  $_act_txt = " 상세";

} else {
    $_act = "input";
    $_act_txt = " 등록";

}
?>
    <!-- PAGE CONTENT CONTAINER -->
    <div class="content" id="content">
        <!-- PAGE HEADING -->
      <?php include_once "./pheading.php";?>
        <!-- //END PAGE HEADING -->
        <div class="container-fluid">
            <div class="card margin-bottom-0">
                <div class="card-body">

                    <div class="card-header">
                      <ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
                        <li class="nav-item"><a class="nav-link active" id="step-tab-1" data-toggle="tab" href="#step-1" role="tab" aria-controls="home" aria-selected="true">기본</a></li>

                      </ul>
                    </div>


                    <form method="post" name="frm_form" id="frm_form" action="./update.php" target="hidden_ifrm" enctype="multipart/form-data">
                        <input type="hidden" name="act" id="act" value="<?=$_act?>" />
                        <input type="hidden" name="nt_idx" id="nt_idx" value="<?=$row['nt_idx']?>" />
                        <div class="tab-content margin-top-15">
                          <div class="tab-pane fade show active" id="step-1" role="tabpanel" aria-labelledby="step-tab-1">


                            <div class="form-group row margin-top-30">
                              <label for="gmt_golf_name" class="col-sm-2 col-form-label">골프장명</label>
                              <div class="col-sm-4 form-validate">
                                <input type="text" name="gmt_golf_name" id="gmt_golf_name" value="<?=$row['gmt_golf_name']?>" placeholder="골프장명 입력" class="form-control">
                              </div>
                              <label for="" class="col-sm-2 col-form-label">회원권종류</label>
                              <div class="col-sm-4 form-validate">
                                <input type="text"  value="무기명"  class="form-control" />
                              </div>

                            </div>
                            <div class="form-group row margin-top-30">
                              <label for="gmat_type" class="col-sm-2 col-form-label">회원구분</label>
                              <div class="col-sm-4 form-validate">

                                <select name="gmat_type" id="gmat_type" class="form-control select-simple">
                                  <option value="">선택하세요.</option>
                                  <?php
                                  foreach($arr_gmat_type as $key=>$value) {
                                    $selected = ($row['gmat_type'] == $key) ? 'selected' : '';
                                    printf('<option value="%s" %s>%s</option>', $key, $selected, $value);
                                  }
                                  ?>
                                </select>
                              </div>
                              <label for="gmat_name" class="col-sm-2 col-form-label">이름/법인명</label>
                              <div class="col-sm-4 form-validate">
                                <input type="text" name="gmat_name" id="gmat_name" value="<?=$row['gmat_name']?>" placeholder="골프장명 입력" class="form-control">
                              </div>

                            </div>

                            <div class="form-group row margin-top-30">
                              <label for="gmat_num" class="col-sm-2 col-form-label">주민번호/사업자번호</label>
                              <div class="col-sm-4 form-validate">
                                <input type="text" name="gmat_num" id="gmat_num" value="<?=$row['gmat_num']?>" placeholder="골프장명 입력" class="form-control">
                              </div>
                              <label for="gmat_membership_num" class="col-sm-2 col-form-label">회원권번호</label>
                              <div class="col-sm-4 form-validate">
                                <input type="text" name="gmat_membership_num" id="gmat_membership_num" value="<?=$row['gmat_membership_num']?>" placeholder="골프장명 입력" class="form-control">
                              </div>

                            </div>

                            <div class="form-group row margin-top-30">
                              <label for="gmat_status" class="col-sm-2 col-form-label">상태</label>
                              <div class="col-sm-4 form-validate">

                                <select name="gmat_status" id="gmat_status" class="form-control select-simple">
                                  <option value="">선택하세요.</option>
                                  <?php
                                  foreach($arr_gmat_status as $key=>$value) {
                                    $selected = ($row['gmat_status'] == $key) ? 'selected' : '';
                                    printf('<option value="%s" %s>%s</option>', $key, $selected, $value);
                                  }
                                  ?>
                                </select>
                              </div>
                              <label for="gmat_wdate" class="col-sm-2 col-form-label">요청일</label>
                              <div class="col-sm-4 form-validate">
                                <input type="text" value="<?=DateType($row['gmat_wdate'], 6)?>" placeholder="요청일 입력" class="form-control">
                              </div>

                            </div>

                            <div class="form-group row margin-top-30">
                              <label for="gmat_hp" class="col-sm-2 col-form-label">휴대폰번호</label>
                              <div class="col-sm-10 form-validate">
                                <input type="text" name="gmat_hp" id="gmat_hp" value="<?=$row['gmat_hp']?>" placeholder="휴대폰번호 입력" class="form-control">
                              </div>

                            </div>

                            <div class="form-group row">
                              <label for="w_image" class="col-sm-2 col-form-label">회원권 사진</label>
                              <div class="col-sm-10">
                                <div class="upload-container" id="sortableContainer">
                                  <div class="upload-box" id="uploadTrigger">
                                    <div class="plus">+</div>
                                    <div class="text">Upload</div>
                                  </div>
                                </div>
                                <input type="file" class="filepond d-none" multiple>
                                <small class="form-text d-none">(이미지 사이즈 : 720x324)</small>
                              </div>

                            </div>



                            <div class="form-group row justify-content-center margin-top-30">

                                <button type="button"  onclick="history.go(-1);" class="btn btn-outline-secondary mx-1" >목록</button>
                                <? if($_act=='input' || $_act=='update'){?>
                                   <button type="submit" class="btn btn-secondary" ><?=$_act_txt?></button>
                                <? } ?>
                              <? if($_act=='view'){?>
                                <button type="button" class="btn btn-success mx-1" data-gmat-idx="<?=$row['gmat_idx']?>" onclick="onAuthStatus(2);" >승인</button>
                                <button type="button" class="btn btn-danger mx-1" data-gmat-idx="<?=$row['gmat_idx']?>" onclick="onAuthStatus(3);" >반려</button>
                              <? } ?>
                            </div>
                          </div>


                        </div>

                    </form>



                    <script>



                        function onAuthStatus(status){
                            const gmat_idx = event.target.getAttribute('data-gmat-idx');
                            if (!gmat_idx) {
                                app.toastr.showError('회원권 정보가 없습니다.');
                                return;
                            }

                            const formData = new FormData();
                            formData.append('act', 'auth_status_change');
                            formData.append('gmat_status', status);
                            formData.append('gmat_idx[]', gmat_idx);

                            const msg = status === 2 ? '승인 하시겠습니까?' : '반려 하시겠습니까?';


                            $.confirm({
                                title: '회원권 승인/반려',
                                content: msg,
                                buttons: {
                                    cancel: {
                                        text: "취소",
                                        btnClass: "btn-outline-light",
                                    },
                                    confirm: {
                                        text: "확인",
                                        btnClass: "btn-primary",
                                        action: function () {


                                            $.ajax({
                                                url: './update.php',
                                                type: 'POST',
                                                data: formData,
                                                dataType: 'json',
                                                processData: false,
                                                contentType: false,
                                                beforeSend: () => $('#splinner_modal').modal('show'),
                                                success: (response) => {
                                                    $('#splinner_modal').modal('hide');
                                                    console.log(response)
                                                    if(response.success) {
                                                        app.toastr.showSuccess(response.message, 'reload');
                                                    } else {
                                                        app.toastr.showError(response.message);
                                                    }
                                                },
                                                error: (xhr, status, error) => {
                                                    $('#splinner_modal').modal('hide');
                                                    console.error(error)
                                                    app.toastr.showError(error);
                                                }
                                            });

                                        },
                                    },
                                },
                            });






                        }


                        $(document).ready(function() {



                            // FileUploader 초기화
                            const uploader = createFileUploader({
                                container: '.upload-container',
                                trigger: '#uploadTrigger',
                                filepondElement: '.filepond',
                                maxFiles: 5,
                                maxFileSize: '5MB',
                                allowedFileTypes: ['image/jpeg', 'image/png', 'image/jpg'],
                                imageMinWidth: 100,
                                imageMinHeight: 100,
                                imageMaxWidth: 4000,
                                imageMaxHeight: 4000,
                                ajaxUrl: './update.php'
                            });


                            // 폼 검증 및 제출 핸들러
                            const formHandler = {
                                init() {
                                    this.initializeValidation();
                                    this.setInitialValues();
                                },

                                initializeValidation() {
                                    $("#frm_form").validate({
                                        submitHandler: this.handleSubmit,
                                        rules: {
                                            gmt_golf_name: { required: true },
                                            gmt_local: { required: true },
                                            gmt_owdate: { required: true },
                                            gmt_thum: { required: true },
                                            gmt_hole: { required: true },
                                            gmt_person: { required: true },
                                            gmt_sale_price: { required: true },
                                            gmt_hp: { required: true },
                                            gmt_zip: { required: true },
                                            gmt_add1: { required: true },
                                            gmt_add2: { required: true },
                                            gmt_membership: { required: true },
                                            gmt_benefit: { required: true },
                                            gmt_point: { required: true },
                                            gmt_temp: { required: true },
                                            gmt_yeyaglyul: { required: true },
                                            gmt_document: { required: true },
                                            'gmt_user_type[]': {
                                                required: function (element) {
                                                    return $('input[name="gmt_user_type[]"]:checked').length === 0;
                                                }
                                            },
                                            'gmt_reservation[]': {
                                                required: function (element) {
                                                    return $('input[name="gmt_reservation[]"]:checked').length === 0;
                                                }
                                            }
                                        },
                                        messages: {
                                            gmt_golf_name: { required: '골프장명을 입력해주세요.' },
                                            gmt_local: { required: '지역을 선택헤주세요.' },
                                            gmt_owdate: { required: '개장일 입력해주세요.' },
                                            gmt_thum: { required: '썸네일을 입력해주세요.' },
                                            gmt_hole: { required: '홀수를 입력해주세요.' },
                                            gmt_person: { required: '회원수를 입력해주세요.' },
                                            gmt_sale_price: { required: '분양가를 입력해주세요.' },
                                            gmt_hp: { required: '전화번호를 입력해주세요.' },
                                            gmt_zip: { required: '주소를 입력해주세요.' },
                                            gmt_add1: { required: '주소를 입력해주세요.' },
                                            gmt_add2: { required: '주소를 입력해주세요.' },
                                            gmt_membership: { required: '회원구성을 입력해주세요.' },
                                            gmt_benefit: { required: '회원혜택을 입력해주세요.' },
                                            gmt_point: { required: '회원권특징을 입력해주세요.' },
                                            gmt_temp: { required: '매매시 특이사항을 입력해주세요.' },
                                            gmt_yeyaglyul: { required: '예약률을 입력해주세요.' },
                                            gmt_document: { required: '준비서류를 입력해주세요.' },

                                        },
                                        ignore: function(index, element) {
                                            return $(element).is(":hidden") && !$(element).hasClass("always-validate");
                                        },
                                        errorElement: 'span',
                                        errorPlacement: (error, element) => {
                                            error.addClass('invalid-feedback');
                                            if(element.attr('name') === 'gmt_user_type[]') {
                                                app.toastr.showError('회원권 종류를 1개 이상 선택해주세요.');
                                            } else if(element.attr('name') === 'gmt_user_type[]') {
                                                app.toastr.showError('회원예약율을 1개 이상 선택해주세요.');
                                            } else {
                                                element.closest('.form-validate').append(error);
                                            }

                                        },
                                        highlight: (element) => $(element).addClass('is-invalid'),
                                        unhighlight: (element) => $(element).removeClass('is-invalid')
                                    });
                                },
                                handleSubmit(form) {



                                    const formData = new FormData(form);
                                    formData.append('maxFiles', uploader.options.maxFiles);

                                    const imageOrder = uploader.getImageOrder();
                                    formData.append('image_order', JSON.stringify(imageOrder));

                                    // FilePond 파일들을 FormData에 추가
                                    const files = uploader.getPond().getFiles();

                                    const findFileById = (id) => {
                                        const found = files.find(f => f.id === id);
                                        return found ? found.file : null;
                                    };

                                    // console.log(imageOrder)
                                    // console.log(files)
                                    imageOrder.forEach((img, index) => {
                                        if (img.type === 'new') {
                                            const fileObj = findFileById(img.id);
                                            if (fileObj) {
                                                console.log(fileObj)
                                                formData.append(`membership${index + 1}`, fileObj);
                                            }
                                        }
                                    });

                                    // 삭제된 파일 정보 전송
                                    const removedFiles = uploader.getRemovedFiles(); // 삭제된 파일 번호 배열
                                    formData.append('removed_files', JSON.stringify(removedFiles));

                                    // console.log(...formData)
                                    $.ajax({
                                        url: './update.php',
                                        type: 'POST',
                                        data: formData,
                                        processData: false,
                                        contentType: false,
                                        beforeSend: () => $('#splinner_modal').modal('show'),
                                        success: (response) => {
                                            $('#splinner_modal').modal('hide');
                                            console.log(response)
                                            if(response.success) {
                                                app.toastr.showSuccess(response.message, response.redirect);
                                            } else {
                                                app.toastr.showError(response.message);
                                            }
                                        },
                                        error: (xhr, status, error) => {
                                            $('#splinner_modal').modal('hide');
                                            console.error(error)
                                            app.toastr.showError(response.message);
                                        }
                                    });
                                    return false;
                                },
                                setInitialValues() {

                                }
                            };

                            // 초기화
                            formHandler.init();




                            const mt_idx = '<?php echo $row["gmat_idx"] ?? ""; ?>';

                            console.log(mt_idx)
                            if(mt_idx != '') {
                                uploader.loadImages(mt_idx);
                            }


                        });





                    </script>
                </div>
            </div>

        </div>
    </div>
    <!-- //END PAGE CONTENT CONTAINER -->
<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/foot.inc.php";
?>