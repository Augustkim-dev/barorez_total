<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu='5';
$chk_sub_menu='3';
include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.menu.inc.php";
//include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.sidebar.inc.php";

$tbl_name = "faq_category_t";

if ($_GET['act'] == "update") {
    $DB->where('idx', $_GET['nt_idx']);
    $row = $DB->getone($tbl_name, '*, idx as ct_idx');

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
                        <input type="hidden" name="ct_idx" id="ct_idx" value="<?=$row['ct_idx']?>" />
                        <div class="card-body">

                            <div class="form-group row margin-top-30">
                              <label for="ct_title" class="col-sm-2 col-form-label">FAQ 카테고리</label>
                              <div class="col-sm-10">
                                <input type="text" name="ct_title" id="ct_title" value="<?=$row['ct_title']?>" class="form-control">
                              </div>
                            </div>
                            <div class="form-group row">
                              <label for="w_show" class="col-sm-2 col-form-label">노출여부</label>
                              <div class="col-sm-10">
                                <select name="ct_show" id="ct_show" class="form-control select-simple">
                                  <option value="Y" <?=$row['ct_show'] === 'Y' ? 'selected' : ''?>>사용</option>
                                  <option value="N" <?=$row['ct_show'] === 'N' ? 'selected' : ''?>>미사용</option>
                                </select>
                              </div>
                            </div>
                            <div class="form-group row">
                                <label for="ct_title" class="col-sm-2 col-form-label">노출순서</label>
                                <div class="col-sm-10">
                                    <input type="text" name="ct_order" id="ct_order" value="<?=$row['ct_order']?>" class="form-control">
                                </div>
                            </div>


                            <? if ($_GET['act'] == "update") {?>
                                <div class="form-group row align-items-center">
                                    <label for="nt_wdate" class="col-md-2 col-form-label">등록일시</label>
                                    <div class="col-sm-4 col-form-label">
                                        <?=DateType($row['ct_wdate'], 6)?>
                                    </div>
                                    <label for="nt_wdate" class="col-md-2 col-form-label">수정일시</label>
                                    <div class="col-sm-4 col-form-label">
                                        <?=DateType($row['ct_udate'], 6)?>
                                    </div>
                                </div>
                            <? } ?>





                            <div class="form-group row justify-content-center margin-top-30">
                                <button type="button"  onclick="history.go(-1);" class="btn btn-outline-secondary mx-1" >목록</button>
                                <button type="submit" class="btn btn-secondary" >등록</button>
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
                                            ct_title: {
                                                required: true
                                            },
                                        },
                                        messages: {
                                            ct_title: {
                                                required: "제목을 입력해주세요."
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

                          <? if($row['ct_show']) { ?>
                            $('#ct_show').val('<?=$row['ct_show']?>');
                          <? } ?>


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
