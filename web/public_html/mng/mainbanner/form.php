<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu='2';
$chk_sub_menu='1';
include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.menu.inc.php";
//include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.sidebar.inc.php";


//$chk_ckeditor = "Y";

if ($_GET['act'] == "update") {
    $DB->where('idx', $_GET['nt_idx']);
    $row = $DB->getone($CFG_TBL['mainbanner']['default'], '*, idx as nt_idx');

    $_act = "update";
    $_act_txt = " 수정";
} else {
    $_act = "input";
    $_act_txt = " 등록";
}


$DB->orderBy("rt_order", "asc");
$DB->where('rt_show', 'Y');
$events = $DB->get($CFG_TBL['event']['default']);


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
                            <h4 id="rw-fe-basic">메인배너 <?=$_act_txt?></h4>


                            <div class="form-group row margin-top-30" id="internal_link_wrap">
                                <label for="rt_status" class="col-sm-2 col-form-label">구분</label>
                                <div class="col-sm-10">
                                    <select name="rt_status" id="rt_status" class="form-control select-simple">
                                        <?php foreach($arr_banner_status as $key=>$value) {?>
                                            <option value="<?php echo $key?>" <?=$row['rt_status'] === $key ? 'selected' : ''?>> <?php echo $value?></option>
                                        <?php }?>
                                    </select>
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
                                <label for="link_url2" class="col-sm-2 col-form-label">링크 URL</label>
                                <div class="col-sm-10">
                                    <input type="text" name="link_url2" value="<?=$row['rt_link_url']?>" class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                              <label for="rt_link_target" class="col-sm-2 col-form-label">링크 새창</label>
                              <div class="col-sm-10">
                                <select name="rt_link_target" id="rt_link_target" class="form-control select-simple">
                                  <option value="Y">새창</option>
                                  <option value="N">현재창</option>
                                </select>
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
                                <label for="rt_order" class="col-sm-2 col-form-label">노출 순서</label>
                                <div class="col-sm-10">
                                    <input type="text" name="rt_order" value="<?=$row['rt_order']?>" class="form-control">
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
                                <input type="file" class="filepond d-none" multiple>
                              </div>
                              <div class="offset-sm-2">
                                <small class="form-text">최대 권장사이즈 - 가로: 1440px, 세로: 1000px </small>
                              </div>
                            </div>


                            <? if ($_GET['act'] == "update") {?>
                              <div class="form-group row align-items-center">
                                <label for="created_at" class="col-md-2 col-form-label">등록일시</label>
                                <div class="col-md-10">
                                  <?=DateType($row['created_at'], 6)?>
                                </div>
                              </div>
                              <div class="form-group row align-items-center">
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
                                            rt_description: {
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
                                            rt_description: {
                                                required: "내용을 입력해주세요"
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

                            <? if($row['rt_link_target']) { ?>
                              $('#rt_link_target').val('<?=$row['rt_link_target']?>');
                            <? } ?>


                            <?php if(isset($row['rt_img1'])) { ?>
                              const mt_idx = '<?php echo $row["idx"] ?? ""; ?>';
                              if(mt_idx) {
                                  uploader.loadImages(mt_idx);
                              }
                            <?php } ?>

                        });

                        function toggleLinkInput() {
                            const type = $('input[name="rt_link_type"]:checked').val();
                            if (type === 'external') {
                                $('#external_link_wrap').show();
                                $('#internal_link_wrap').hide();
                            } else {
                                $('#external_link_wrap').hide();
                                $('#internal_link_wrap').show();
                            }
                        }

                        $('input[name="rt_link_type"]').on('change', toggleLinkInput);
                        toggleLinkInput(); // 초기 렌더링 시 실행



                    </script>
                </div>
            </div>

        </div>
    </div>
    <!-- //END PAGE CONTENT CONTAINER -->
<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/foot.inc.php";
?>
