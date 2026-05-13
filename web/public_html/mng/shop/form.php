<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu='4';
$chk_sub_menu='1';
include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.menu.inc.php";
//include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.sidebar.inc.php";


$chk_ckeditor = "Y";
$tbl_name = "qa_t";

if ($_GET['act'] == "update") {
    // 조인 추가
    $DB->join('member_t'." a2", "a1.mt_idx = a2.idx", "LEFT");

    $DB->where('a1.idx', $_GET['nt_idx']);
    $row = $DB->getOne($tbl_name . ' a1', '*, a1.idx as nt_idx');

    $_act = "update";
    $_act_txt = " 수정";
} else {
    $_act = "input";
    $_act_txt = " 등록";
}
//
//$DB->orderBy("a1.idx", "asc");
//$DB->where('mt_level', 2);
//$member_list = $DB->get("member_t");


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
                            <div class="form-group row">
                                <label for="mt_id" class="col-sm-2 col-form-label">아이디</label>
                                <div class="col-sm-4 col-form-label">
                                    <input type="text" name="mt_id" id="mt_id" value="<?=$row['mt_id']?>" class="form-control" minlength="8" maxlength="50" disabled>
                                </div>
                                <label for="mt_hp" class="col-sm-2 col-form-label">이름</label>
                                <div class="col-sm-4 col-form-label">
                                    <input type="text" name="mt_pwd" id="mt_pwd" value="<?=$row['mt_name']?>" class="form-control" minlength="8" maxlength="50" disabled>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="mt_pwd" class="col-sm-2 col-form-label">제목</label>
                                <div class="col-sm-4 col-form-label">
                                    <input type="text" name="mt_pwd" id="mt_pwd" value="<?=$row['rt_title']?>" class="form-control" minlength="8" maxlength="50" disabled>
                                </div>
                                <label for="mt_hp" class="col-sm-2 col-form-label">문의일시</label>
                                <div class="col-sm-4 col-form-label">
                                    <input type="text" name="mt_pwd" id="mt_pwd" value=" <?=DateType($row['created_at'], 6)?>" class="form-control" minlength="8" maxlength="50" disabled>
                                </div>
                            </div>

                            <div class="form-group row">
                              <label for="rt_description" class="col-sm-2 col-form-label">내용</label>
                              <div class="col-sm-10">
                                <?php
                                $editor_name = 'rt_description';
                                if($chk_ckeditor) {
                                  $editor_upload = 'Y';
                                  include $_SERVER['DOCUMENT_ROOT']."/mng/inc/ckeditor.php";
                                }else {
                                  echo "<textarea name='".$editor_name."' id='".$editor_name."' class='form-control' style='height:250px;'>".$row[$editor_name]."</textarea>";
                                }
                                ?>
                              </div>
                            </div>


                            <div class="form-group row">
                              <label for="rt_status" class="col-sm-2 col-form-label">상태</label>
                              <div class="col-sm-10">
                                <select name="rt_status" id="rt_status" class="form-control select-simple">
                                    <?php foreach($arr_qa as $key=>$value) {?>
                                        <?php
                                        $local_class_name = ($search_status == $key) ? 'btn-secondary' : 'btn-outline-secondary';
                                        ?>
                                        <option value="<?php echo $key ?>" <?=$row['rt_status'] === $key ? 'selected' : ''?>>
                                            <?php echo $value?>
                                        </option>
                                    <?php }?>
                                </select>
                              </div>
                            </div>


                            <div class="form-group row">
                              <label for="rt_response_text" class="col-sm-2 col-form-label">답변</label>
                              <div class="col-sm-10">
                                <?php
                                $editor_name = 'rt_response_text';
                                if($chk_ckeditor) {
                                  $editor_upload = 'Y';
                                  include $_SERVER['DOCUMENT_ROOT']."/mng/inc/ckeditor.php";
                                }else {
                                  echo "<textarea name='".$editor_name."' id='".$editor_name."' class='form-control' style='height:250px;'>".$row[$editor_name]."</textarea>";
                                }
                                ?>
                              </div>
                            </div>

                            <div class="form-group row justify-content-center margin-top-30">
                                <button type="button"  onclick="history.go(-1);" class="btn btn-outline-secondary mx-1" >목록</button>
                                <button type="submit" class="btn btn-secondary" >완료</button>
                            </div>
                        </div>
                    </form>



                    <script>
                        $(document).ready(function() {

                            $('#mt_idx').select2({
                                placeholder: "선택하세요",
                                allowClear: true
                            });

                            // FileUploader 초기화
                            const uploader = createFileUploader({
                                container: '.upload-container',
                                trigger: '#uploadTrigger',
                                filepondElement: '.filepond',
                                maxFiles: 5,
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
                                            mt_idx: {
                                                required: true
                                            },
                                            rt_title: {
                                                required: true
                                            },
                                            rt_description: {
                                                required: true
                                            },
                                        },
                                        messages: {

                                            mt_idx: {
                                                required: "작성자를 입력해주세요"
                                            },
                                            rt_title: {
                                                required: "제목을 입력해주세요"
                                            },
                                            rt_description: {
                                                required: "내용을 입력해주세요"
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
                            <? if($row['rt_status']) { ?>
                              $('#rt_status').val('<?=$row['rt_status']?>');
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
