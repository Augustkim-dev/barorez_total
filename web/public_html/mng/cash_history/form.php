<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu='1';
$chk_sub_menu='5';
include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.menu.inc.php";
//include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.sidebar.inc.php";

$tbl_name = "cash_history_t";

$DB->orderBy("idx", "asc");
$DB->where('mt_level', 2);
$member_list = $DB->get("member_t");

$_act = "input";
$_act_txt = "등록";
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
                            <h4 id="rw-fe-basic">코르크내역 </h4>

                            <div class="form-group row margin-top-30">
                              <label for="point" class="col-sm-2 col-form-label">사용</label>
                              <div class="col-sm-10">
                                <input type="text" name="point" id="point" maxlength="10" class="form-control">
                              </div>
                            </div>

                            <div class="form-group row margin-top-30">
                              <label for="mt_idx" class="col-sm-2 col-form-label">회원</label>
                              <div class="col-sm-10">
                                <select name="mt_idx" id="mt_idx" class="form-control select2"
                                        data-initial-value="">
                                  <option value="">선택하세요.</option>
                                  <?php
                                  foreach ($member_list as $pr) {
                                    $selected = ($pr['idx'] == $row['mt_idx']) ? 'selected' : '';
                                    echo '<option value="' . $pr['idx'] . '" ' . $selected . '>' . $pr['mt_name'] . '(' .$pr['mt_id']. ')</option>';
                                  }
                                  ?>
                                </select>
                              </div>
                            </div>

                            <div class="form-group row margin-top-30">
                              <label for="status" class="col-sm-2 col-form-label">사용처리</label>
                              <div class="col-sm-10">
                                <select name="status" id="status" class="form-control select-simple">
                                  <option value="">선택하세요</option>
                                  <option value="add"><?php echo $arr_cash_status['add'] ?>(+)</option>
                                  <option value="remove"><?php echo $arr_cash_status['remove'] ?>(-)</option>
                                </select>
                              </div>
                            </div>

                            <div class="form-group row margin-top-30">
                              <label for="message" class="col-sm-2 col-form-label">메모</label>
                              <div class="col-sm-10">
                                <input type="text" name="message" id="message"  class="form-control">
                              </div>
                            </div>




                            <div class="form-group row justify-content-center margin-top-30">
                                <button type="button"  onclick="history.go(-1);" class="btn btn-outline-secondary mx-1" >목록</button>
                                <button type="submit" class="btn btn-secondary" >확인</button>
                            </div>
                        </div>
                    </form>


                    <script>
                        $(document).ready(function() {

                            $('#mt_idx').select2({
                                placeholder: "선택하세요",
                                allowClear: true
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
                                            point: {
                                                required: true
                                            },
                                            mt_idx: {
                                                required: true
                                            },
                                            status: {
                                                required: true
                                            },
                                        },
                                        messages: {
                                            point: {
                                                required: "사용할 코르크를 입력해주세요"
                                            },
                                            bm_name: {
                                                required: "회원을 선택해주세요"
                                            },
                                            bm_level: {
                                                required: "상태를 선택해주세요."
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