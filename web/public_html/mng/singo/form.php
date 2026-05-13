<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu='3';
$chk_sub_menu='2';
include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.menu.inc.php";
//include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.sidebar.inc.php";

$tbl_name = "singo_t";

if ($_GET['act'] == "update") {
    $DB->join("member_t a2", "a1.mt_idx = a2.idx", "LEFT");
    $DB->join('stype_t s1', 'a1.sg_id = s1.idx', 'LEFT');
    $DB->where('a1.idx', $_GET['nt_idx']);
    $row = $DB->getone($tbl_name.' a1', '*, a1.idx as nt_idx');

    if($row['bo_id']){
        $DB->where('idx', $row['bo_id']);
        $row_title = $DB->getone("board_t b1", "*, idx as bo_idx");
        $category = $row_title['nt_category'];
    }else{
        $DB->where('idx', $row['bo_id']);
        $row_title = $DB->getone("board_t b1", "*, idx as bo_idx");
        $category = $row_title['nt_category'];
    }

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
                            <h4 id="rw-fe-basic">신고항목 <?=$_act_txt?></h4>


                            <div class="form-group row align-items-center">
                                <label for="sg_status" class="col-sm-2 col-form-label">처리상태</label>
                                <div class="col-sm-4">
                                    <select name="sg_status" id="sg_status" class="form-control select-simple">
                                        <?php foreach($arr_singo_rt_status as $key=>$value) {?>
                                            <option value="<?php echo $key?>" <?=$row['sg_status'] === $key ? 'selected' : ''?>> <?php echo $value?></option>
                                        <?php }?>
                                    </select>
                                </div>
                                <label for="sg_date" class="col-sm-2 col-form-label">등록일시</label>
                                <div class="col-sm-4 col-form-label">
                                    <?=DateType($row['sg_date'], 6)?>
                                </div>
                            </div>

                            <div class="form-group row align-items-center">
                                <label for="mt_name" class="col-sm-2 col-form-label">신고자</label>
                                <div class="col-sm-4 col-form-label">
                                    <?=$row['mt_name']?>
                                </div>
                                <label for="sg_udate" class="col-sm-2 col-form-label">처리완료일시</label>
                                <div class="col-sm-4 col-form-label">
                                    <?=DateType($row['sg_udate'], 6)?>
                                </div>
                            </div>

                            <div class="form-group row align-items-center">
                                <label for="w_name" class="col-sm-2 col-form-label">신고사유</label>
                                <div class="col-sm-4 col-form-label">
                                    <?=$row['w_name']?>
                                </div>
                                <label for="w_name" class="col-sm-2 col-form-label">신고 카테고리</label>
                                <div class="col-sm-4 col-form-label">
                                    <?=$category?>
                                </div>
                            </div>

                            <div class="form-group row justify-content-center margin-top-30">
                                <button type="button"  onclick="history.go(-1);" class="btn btn-outline-secondary mx-1" >목록</button>
                                <button type="submit" class="btn btn-secondary" >저장</button>
                            </div>
                        </div>

                        <!-- 신고한 글 -->

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
                                            w_name: {
                                                required: true
                                            },
                                            w_name_en: {
                                                required: true
                                            }
                                        },
                                        messages: {
                                            w_name: {
                                                required: "이름을 입력해주세요"
                                            },
                                            w_name_en: {
                                                required: "영문이름을 입력해주세요"
                                            }
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

                          <? if($row['w_show']) { ?>
                            $('#w_show').val('<?=$row['w_show']?>');
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
