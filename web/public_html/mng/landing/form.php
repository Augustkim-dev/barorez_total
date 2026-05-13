<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu='12';
$chk_sub_menu='1';
include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.menu.inc.php";
//include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.sidebar.inc.php";


//$chk_ckeditor = "Y";
$tbl_name = "landing_t";

if ($_GET['act'] == "update") {
    $DB->where('idx', $_GET['nt_idx']);
    $row = $DB->getone($tbl_name, '*, idx as nt_idx');

    $_act = "update";
    $_act_txt = " 수정";
} else {
    $_act = "input";
    $_act_txt = " 등록";
}

if($_REQUEST['gubun'] == 'A'){
  $gubun = 'portacellar.com';
} else if($_REQUEST['gubun'] == 'B'){
  $gubun = 'vinporta.com';
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
                            <h4 id="rw-fe-basic">랜딩이미지 <?=$_act_txt?></h4>


                            <div class="form-group row margin-top-30">
                              <label for="rt_title" class="col-sm-2 col-form-label">제목</label>
                              <div class="col-sm-10">
                                <input type="text" name="rt_gubun" id="rt_gubun" value="<?=$gubun?>" class="form-control" readonly>
                              </div>
                            </div>

                            <div class="form-group row margin-top-30">
                              <label for="rt_title" class="col-sm-2 col-form-label">제목</label>
                              <div class="col-sm-10">
                                <input type="text" name="rt_title" id="rt_title" value="<?=$row['rt_title']?>" class="form-control">
                              </div>
                            </div>

                            <div class="form-group row margin-top-30">
                              <label for="rt_title" class="col-sm-2 col-form-label">노출기간</label>
                              <div class="col-sm-4">
                                <div class="input-group">
                                  <input type="text" name="rt_start" id="rt_start" value="<?= $row['rt_start'] ?>"
                                         class="form-control" readonly/>
                                  <span class="m-2">~</span>
                                  <input type="text" name="rt_end"
                                                     id="rt_end"
                                                     value="<?= $row['rt_end'] ?>"
                                                     class="form-control"
                                                     readonly/>
                                </div>
                              </div>
                            </div>






                          <div class="form-group row">
                              <label for="rt_show" class="col-sm-2 col-form-label">노출여부</label>
                              <div class="col-sm-10">
                                <select name="rt_show" id="rt_show" class="form-control select-simple">
                                  <option value="Y">사용</option>
                                  <option value="N">미사용</option>
                                </select>
                              </div>
                            </div>

                            <div class="form-group row">
                              <label for="w_image" class="col-sm-2 col-form-label">사진</label>
                              <div class="col-sm-10">
                                <div class="upload-container" id="sortableContainer">
                                  <div class="upload-box" id="uploadTrigger">
                                    <div class="plus">+</div>
                                    <div class="text">Upload</div>
                                  </div>
                                </div>
                                <input type="file" class="filepond d-none">
                              </div>
                              <div class="offset-sm-2">
                                <small class="form-text">최대 권장사이즈 - 가로:1636px, 세로:2739px </small>
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


                            $('#rt_start').datetimepicker({
                                format: 'Y-m-d',
                                onShow: function (ct) {
                                    this.setOptions({
                                        maxDate: $('#rt_end').val() ? $('#rt_end').val() : false
                                    })
                                },
                                timepicker: false
                            });
                            $('#rt_end').datetimepicker({
                                format: 'Y-m-d',
                                onShow: function (ct) {
                                    this.setOptions({
                                        minDate: $('#rt_start').val() ? $('#rt_start').val() : false
                                    })
                                },
                                timepicker: false
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

                                            rt_title: {
                                                required: true
                                            },

                                            rt_start: {
                                                required: true
                                            },
                                            rt_end: {
                                                required: true
                                            },
                                        },
                                        messages: {


                                            rt_title: {
                                                required: "제목을 입력해주세요"
                                            },

                                            rt_start: {
                                                required: "노출 시작일을 입력해주세요."
                                            },
                                            rt_end: {
                                                required: "노출 종료일을 입력해주세요"
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
                                                formData.append(`rt_img${index + 1}`, fileObj);
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


                            <? if($row['rt_show']) { ?>
                              $('#rt_show').val('<?=$row['rt_show']?>');
                            <? } ?>


                            <?php if(isset($row['rt_img1'])) { ?>
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