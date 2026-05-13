<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu='10';
$chk_sub_menu='2';
include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.menu.inc.php";
//include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.sidebar.inc.php";


//$chk_ckeditor = "Y";
$tbl_name = "fcm_template_t";

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
                            <h4 id="rw-fe-basic">템플릿 <?=$_act_txt?></h4>




                            <div class="form-group row margin-top-30">
                              <label for="type" class="col-sm-2 col-form-label">유형</label>
                              <div class="col-sm-10">
                                <input type="text" name="type" id="type" value="<?=$row['type']?>" class="form-control">
                              </div>
                            </div>
                          
                            <div class="form-group row margin-top-30">
                              <label for="activity" class="col-sm-2 col-form-label">동작</label>
                              <div class="col-sm-10">
                                <input type="text" name="activity" id="activity" value="<?=$row['activity']?>" class="form-control">
                              </div>
                            </div>

                            <div class="form-group row margin-top-30">
                              <label for="title" class="col-sm-2 col-form-label">제목</label>
                              <div class="col-sm-10">
                                <input type="text" name="title" id="title" value="<?=$row['title']?>" class="form-control">
                              </div>
                            </div>

                            <div class="form-group row margin-top-30">
                              <label for="body" class="col-sm-2 col-form-label">내용</label>
                              <div class="col-sm-10">
                                <input type="text" name="body" id="body" value="<?=$row['body']?>" class="form-control">
                              </div>
                            </div>

                            <div class="form-group row margin-top-30">
                              <label for="message" class="col-sm-2 col-form-label">메시지</label>
                              <div class="col-sm-10">
                                <input type="text" name="message" id="message" value="<?=$row['message']?>" class="form-control">
                              </div>
                            </div>

                            <div class="form-group row margin-top-30">
                              <label for="title_en" class="col-sm-2 col-form-label">제목(영문)</label>
                              <div class="col-sm-10">
                                <input type="text" name="title_en" id="title_en" value="<?=$row['title_en']?>" class="form-control">
                              </div>
                            </div>

                            <div class="form-group row margin-top-30">
                              <label for="body_en" class="col-sm-2 col-form-label">내용(영문)</label>
                              <div class="col-sm-10">
                                <input type="text" name="body_en" id="body_en" value="<?=$row['body_en']?>" class="form-control">
                              </div>
                            </div>

                            <div class="form-group row margin-top-30">
                              <label for="message_en" class="col-sm-2 col-form-label">메시지(영문)</label>
                              <div class="col-sm-10">
                                <input type="text" name="message_en" id="message_en" value="<?=$row['message_en']?>" class="form-control">
                              </div>
                            </div>

                            <div class="form-group row margin-top-30">
                              <label for="target_link" class="col-sm-2 col-form-label">타겟링크(M)</label>
                              <div class="col-sm-10">
                                <input type="text" name="target_link" id="target_link" value="<?=$row['target_link']?>" class="form-control">
                              </div>
                            </div>

                          <div class="form-group row margin-top-30">
                            <label for="web_target_link" class="col-sm-2 col-form-label">타겟링크(PC)</label>
                            <div class="col-sm-10">
                              <input type="text" name="web_target_link" id="web_target_link" value="<?=$row['web_target_link']?>" class="form-control">
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
                                            type: {
                                                required: true
                                            },
                                            activity: {
                                                required: true
                                            },
                                            title: {
                                                required: true
                                            },
                                            body: {
                                                required: true
                                            },
                                        },
                                        messages: {

                                            type: {
                                                required: "유형을 입력해주세요"
                                            },
                                            activity: {
                                                required: "동작을 입력해주세요"
                                            },
                                            title: {
                                                required: "제목을 입력해주세요"
                                            },
                                            body: {
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