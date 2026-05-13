<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu='1';
$chk_sub_menu='4';
include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.menu.inc.php";
//include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.sidebar.inc.php";

$tbl_name = "badge_master_t";

if ($_GET['act'] == "update") {
    $DB->where('idx', $_GET['nt_idx']);
    $row = $DB->getone($tbl_name, '*, idx as nt_idx');

    $_act = "update";
    $_act_txt = " 수정";
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
                    <form method="post" name="frm_form" id="frm_form" action="./update.php" target="hidden_ifrm" enctype="multipart/form-data">
                        <input type="hidden" name="act" id="act" value="<?=$_act?>" />
                        <input type="hidden" name="nt_idx" id="nt_idx" value="<?=$row['nt_idx']?>" />
                        <div class="card-body">
                            <h4 id="rw-fe-basic">뱃지 <?=$_act_txt?></h4>

                            <div class="form-group row margin-top-30">
                              <label for="bm_type" class="col-sm-2 col-form-label">유형</label>
                              <div class="col-sm-10">
                                <input type="text" name="bm_type" id="bm_type" value="<?=$row['bm_type']?>" class="form-control">
                              </div>
                            </div>
                            <div class="form-group row margin-top-30">
                              <label for="bm_category" class="col-sm-2 col-form-label">카테고리</label>
                              <div class="col-sm-10">
                                <input type="text" name="bm_category" id="bm_category" value="<?=$row['bm_category']?>" class="form-control">
                              </div>
                            </div>
                            <div class="form-group row margin-top-30">
                              <label for="bm_name" class="col-sm-2 col-form-label">이름</label>
                              <div class="col-sm-10">
                                <input type="text" name="bm_name" id="bm_name" value="<?=$row['bm_name']?>" class="form-control">
                              </div>
                            </div>
                            <div class="form-group row margin-top-30">
                              <label for="bm_level" class="col-sm-2 col-form-label">단계</label>
                              <div class="col-sm-10">
                                <input type="text" name="bm_level" id="bm_level" value="<?=$row['bm_level']?>" class="form-control">
                              </div>
                            </div>
                            <div class="form-group row margin-top-30">
                              <label for="bm_threshold" class="col-sm-2 col-form-label">획득조건(리뷰수)</label>
                              <div class="col-sm-10">
                                <input type="text" name="bm_threshold" id="bm_threshold" value="<?=$row['bm_threshold']?>" class="form-control">
                              </div>
                            </div>

                            <div class="form-group row">
                              <label for="w_show" class="col-sm-2 col-form-label">노출여부</label>
                              <div class="col-sm-10">
                                <select name="w_show" id="w_show" class="form-control select-simple">
                                  <option value="Y">사용</option>
                                  <option value="N">미사용</option>
                                </select>
                              </div>
                            </div>

                            <div class="form-group row margin-top-30">
                              <label for="w_image" class="col-sm-2 col-form-label">이미지</label>
                              <div class="col-sm-10">
                                <div class="upload-container" id="sortableContainer">
                                  <div class="upload-box" id="uploadTrigger">
                                    <div class="plus">+</div>
                                    <div class="text">Upload</div>
                                  </div>
                                </div>
                                <input type="file" class="filepond d-none" multiple>
                              </div>
                            </div>



                          <? if ($_GET['act'] == "update") {?>
                              <div class="form-group row">
                                <label for="created_at" class="col-md-2 col-form-label">등록일시</label>
                                <div class="col-md-10">
                                  <?=DateType($row['created_at'], 6)?>
                                </div>
                              </div>
                              <div class="form-group row">
                                <label for="updated_at" class="col-md-2 col-form-label">수정일시</label>
                                <div class="col-md-10">
                                  <?=DateType($row['updated_at'], 6)?>
                                </div>
                              </div>
                            <? } ?>

                            <div class="form-group row justify-content-center margin-top-30">
                                <button type="button"  onclick="history.go(-1);" class="btn btn-outline-secondary mx-1" >목록</button>
                                <button type="submit" class="btn btn-secondary" >확인</button>
                            </div>
                        </div>
                    </form>


                    <script>
                        $(document).ready(function() {

                            Fancybox.bind('[data-fancybox="gallery"]', {
                                groupAll: true
                            });

                            // FileUploader 초기화
                            const uploader = createFileUploader({
                                container: '.upload-container',
                                trigger: '#uploadTrigger',
                                filepondElement: '.filepond',
                                maxFiles: 1,
                                maxFileSize: '5MB',
                                allowedFileTypes: ['image/jpeg', 'image/png', 'image/gif'],
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
                                            bm_type: {
                                                required: true
                                            },
                                            bm_name: {
                                                required: true
                                            },
                                            bm_level: {
                                                required: true
                                            },
                                            bm_threshold: {
                                                required: true
                                            },
                                        },
                                        messages: {
                                            bm_type: {
                                                required: "유형를 입력해주세요"
                                            },
                                            bm_name: {
                                                required: "이름을 입력해주세요"
                                            },
                                            bm_level: {
                                                required: "단계를 입력해주세요"
                                            },

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
                                    const formData = new FormData(form);

                                    formData.append('maxFiles', uploader.options.maxFiles);

                                    // 이미지 순서 정보 추가
                                    const imageOrder = uploader.getImageOrder();
                                    formData.append('image_order', JSON.stringify(imageOrder));

                                    // FilePond 파일들을 FormData에 추가
                                    const files = uploader.getPond().getFiles();

                                    const findFileById = (id) => {
                                        const found = files.find(f => f.id === id);
                                        return found ? found.file : null;
                                    };

                                    console.log(imageOrder)
                                    console.log(files)
                                    imageOrder.forEach((img, index) => {
                                        if (img.type === 'new') {
                                            const fileObj = findFileById(img.id);
                                            if (fileObj) {
                                                console.log(fileObj)
                                                formData.append(`w_img${index + 1}`, fileObj);
                                            }
                                        }
                                    });

                                    // 삭제된 파일 정보 전송
                                    const removedFiles = uploader.getRemovedFiles(); // 삭제된 파일 번호 배열
                                    formData.append('removed_files', JSON.stringify(removedFiles));


                                    $.ajax({
                                        url: './update.php',
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

                            <? if($row['w_show']) { ?>
                              $('#w_show').val('<?=$row['w_show']?>');
                            <? } ?>

                          <?php if(isset($row['w_img1'])) { ?>
                            const mt_idx = '<?php echo $row["idx"] ?? ""; ?>';
                            if(mt_idx) {
                                uploader.loadImages(mt_idx);
                            }
                          <?php } ?>



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