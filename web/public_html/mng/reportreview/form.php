<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu='12';
$chk_sub_menu='3';
include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.menu.inc.php";
//include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.sidebar.inc.php";


//$chk_ckeditor = "Y";

if ($_GET['act'] == "view") {
    $DB->where('rt_idx', $_GET['nt_idx']);
    $row = $DB->getone($CFG_TBL['report']['default'], '*, rt_idx as nt_idx');

    $DB->where('rt_idx', $row['rt_pidx']);
    $review = $DB->getone($CFG_TBL['review']['default']);


    $my = get_mem_info('idx', $row['mt_idx']);
    $_act = "review_update";
    $_act_txt = " 보기";
    $isDsiabled = "disabled";

    $review_mt_id = $review['mt_id'];
    $review_rt_wdate = DateType($review['rt_wdate'], 4);
    $review_rt_content = $review['rt_content'];
    $review_gmt_golf_name = $review['gmt_golf_name'];
    $review_rt_average_start = $review['rt_average_start'];

    $raw = $review['rt_hash'];
    $tags = explode('|:|', $raw);
    $hash_tags = array_map(fn($tag) => '#' . trim($tag), $tags);
    $final_output = implode(' ', $hash_tags);
    $review_rt_hash = $final_output;

    if($review['rt_del']=='Y'){
      $review_mt_id = '-';
      $review_rt_wdate = '-';
      $review_rt_content = '삭제된 리뷰입니다.';
      $review_gmt_golf_name = '-';
      $review_rt_average_start = '-';
      $review_rt_hash = '-';

    }



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
                            <h4 id="rw-fe-basic">신고 정보 <?=$_act_txt?></h4>



                            <div class="form-group row margin-top-30">
                              <label for="mt_id" class="col-sm-2 col-form-label">신고자 아이디</label>
                              <div class="col-sm-4">
                                <input type="text" name="mt_id" id="mt_id" value="<?=$row['mt_id']?>" class="form-control" <?=$isDsiabled?> />
                              </div>
                              <label for="rt_wdate" class="col-sm-2 col-form-label">신고일시</label>
                              <div class="col-sm-4">
                                <input type="text" name="rt_wdate" id="rt_wdate" value="<?=DateType($row['rt_wdate'], 4)?>" class="form-control" <?=$isDsiabled?> />
                              </div>
                            </div>
                            <div class="form-group row margin-top-30">
                              <label for="rt_reason" class="col-sm-2 col-form-label">신고사유</label>
                              <div class="col-sm-10">
                                <select name="rt_reason" id="rt_reason" class="form-control" <?=$isDsiabled?>>
                                  <option value="">선택하세요.</option>
                                  <?php
                                  foreach($arr_stype as $key=>$value) {
                                    $selected = ($row['rt_reason'] == $key) ? 'selected' : '';
                                    printf('<option value="%s" %s>%s</option>', $key, $selected, $value);
                                  }
                                  ?>
                                </select>
                              </div>
                            </div>

                            <h4 id="rw-fe-basic">댓글/답글 정보 <?=$_act_txt?></h4>


                            <div class="form-group row margin-top-30">
                              <label for="mt_id" class="col-sm-2 col-form-label">작성자 아이디</label>
                              <div class="col-sm-4">
                                <input type="text" name="mt_id" id="mt_id" value="<?=$review_mt_id?>" class="form-control" <?=$isDsiabled?> />
                              </div>
                              <label for="rt_wdate" class="col-sm-2 col-form-label">작성일</label>
                              <div class="col-sm-4">
                                <input type="text" name="rt_wdate" id="rt_wdate" value="<?=$review_rt_wdate?>" class="form-control" <?=$isDsiabled?> />
                              </div>
                            </div>
                            <div class="form-group row">
                              <label for="gmt_golf_name" class="col-sm-2 col-form-label">골프장명</label>
                              <div class="col-sm-10">
                                <input type="text" name="gmt_golf_name" id="gmt_golf_name" value="<?=$review_gmt_golf_name?>" class="form-control" <?=$isDsiabled?> />
                              </div>
                            </div>
                            <div class="form-group row margin-top-30">
                              <label for="rt_hash" class="col-sm-2 col-form-label">해시태그</label>
                              <div class="col-sm-4">
                                <input type="text" name="rt_hash" id="rt_hash" value="<?=$review_rt_hash?>" class="form-control" <?=$isDsiabled?> />
                              </div>
                              <label for="rt_average_start" class="col-sm-2 col-form-label">평점</label>
                              <div class="col-sm-4">
                                <input type="text" name="rt_average_start" id="rt_average_start" value="<?=$review_rt_average_start?>" class="form-control" <?=$isDsiabled?> />
                              </div>
                            </div>


                            <div class="form-group row">
                              <label for="rt_content" class="col-sm-2 col-form-label">내용</label>
                              <div class="col-sm-10">
                                <input type="text" name="rt_content" id="rt_content" value="<?=$review_rt_content?>" class="form-control" <?=$isDsiabled?> />
                              </div>
                            </div>


                            <div class="form-group row margin-top-30">
                              <label for="rt_status" class="col-sm-2 col-form-label">처리상태</label>
                              <div class="col-sm-10">
                                <select name="rt_status" id="rt_status" class="form-control">
                                  <option value="">선택하세요.</option>
                                  <?php
                                  foreach($arr_singo_rt_status as $key=>$value) {
                                    $selected = ($row['rt_status'] == $key) ? 'selected' : '';
                                    printf('<option value="%s" %s>%s</option>', $key, $selected, $value);
                                  }
                                  ?>
                                </select>
                              </div>
                            </div>




                            <div class="form-group row justify-content-center margin-top-30">
                                <button type="button"  onclick="history.go(-1);" class="btn btn-outline-secondary mx-1" >목록</button>
                                <button type="submit" class="btn btn-secondary" >저장</button>
                            </div>
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
                                            nt_idx: {
                                                required: true
                                            },
                                            rt_status: {
                                                required: true
                                            },

                                        },

                                        ignore: function(index, element) {
                                            return $(element).is(":hidden") && !$(element).hasClass("always-validate");
                                        },
                                        errorElement: 'span',
                                        errorPlacement: (error, element) => {
                                            error.addClass('invalid-feedback');
                                            element.closest('.form-validate').append(error);

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