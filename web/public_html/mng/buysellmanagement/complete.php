<?php
include $_SERVER['DOCUMENT_ROOT'] . "/mng/head.inc.php";
$chk_menu = '15';
$chk_sub_menu = '1';
include $_SERVER['DOCUMENT_ROOT'] . "/mng/inc/header.menu.inc.php";
//include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.sidebar.inc.php";

//$chk_post_code = "Y";
//$chk_ckeditor = "Y";

if (!$_GET['nt_idx']) {
  alert_b("잘못된 접근입니다.");
}


$DB->where('gmtt_idx', $_GET['nt_idx']);
$row = $DB->getone($CFG_TBL['golf_membership']['transaction'], '*, gmtt_idx as nt_idx');

$DB->where('gmt_idx', $row['gmt_idx']);
$saleInfo = $DB->getone($CFG_TBL['golf_membership']['main'], '*');
$prices = getGolfPrice($saleInfo['gmt_idx']);

$state_txt = '';
if ($_GET['state'] == '2') {
  $_act = "status_update";
  $state_txt = '판매';
} else if ($_GET['state'] == '1') {
  $_act = "status_update";
  $state_txt = '구매';
}


$DB->where('gmt_del', 'N');
$DB->orderBy("gmt_wdate", "desc");
$selectList = $DB->get($CFG_TBL['golf_membership']['main']);



?>
  <!-- PAGE CONTENT CONTAINER -->
  <div class="content" id="content">
    <!-- PAGE HEADING -->
    <?php include_once "./pheading.php"; ?>
    <!-- //END PAGE HEADING -->
    <div class="container-fluid">
      <div class="card margin-bottom-0">
        <div class="card-body">

          <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
              <li class="nav-item"><a class="nav-link active" id="step-tab-1" data-toggle="tab" href="#step-1"
                                      role="tab" aria-controls="home" aria-selected="true">기본</a></li>

            </ul>
          </div>


          <form method="post" name="frm_form" id="frm_form" action="./update.php" target="hidden_ifrm"
                enctype="multipart/form-data">
            <input type="hidden" name="act" id="act" value="<?= $_act ?>"/>
            <input type="hidden" name="nt_idx" id="nt_idx" value="<?= $row['nt_idx'] ?>"/>


            <div class="tab-content margin-top-15">
              <div class="tab-pane fade show active" id="step-1" role="tabpanel" aria-labelledby="step-tab-1">

                <div class="form-group row margin-top-30">
                  <h5 class="h5 col-sm-12">등록자</h5>
                </div>
                <div class="form-group row margin-top-30">
                  <label class="col-sm-2 col-form-label">주문형태</label>
                  <div class="col-sm-4 form-validate">
                    <input type="text" value="<?=$state_txt?>" class="form-control" disabled/>
                  </div>
                  <label for="gmt_golf_name" class="col-sm-2 col-form-label">골프장명</label>
                  <div class="col-sm-4 form-validate">

                    <input type="text" class="form-control" name="gmt_golf_name" value="<?= $row['gmt_golf_name'] ?>" disabled />

                  </div>
                </div>

                <div class="form-group row margin-top-30">
                  <label for="mt_name" class="col-sm-2 col-form-label">고객명 </label>
                  <div class="col-sm-4 form-validate">
                    <input type="text" name="mt_name" id="mt_name" value="<?= $row['mt_name'] ?>" placeholder="등록자 입력"
                           class="form-control" disabled>
                  </div>
                  <label for="mt_hp" class="col-sm-2 col-form-label">전화번호 </label>
                  <div class="col-sm-4 form-validate">
                    <input type="text" name="mt_hp" id="mt_hp" value="<?= $row['mt_hp'] ?>" placeholder="숫자만 입력"
                           class="form-control" disabled>
                  </div>
                </div>

                <div class="form-group row margin-top-30">
                  <label for="gmtt_type" class="col-sm-2 col-form-label">구분 </label>
                  <div class="col-sm-4 form-validate">
                    <input type="text" class="form-control" name="gmtt_type" value="<?= $arr_gmtt_type[$row['gmtt_type']] ?>" disabled />
                  </div>


                  <? if($state_txt=='판매'){?>
                    <label for="gmtt_hope_price" class="col-sm-2 col-form-label">최소분양금액 <span
                        class="text-danger">*</span> <small>(단위 : 만원)</small></label>
                    <div class="col-sm-4 form-validate">
                      <input type="text" name="gmtt_hope_price" id="gmtt_hope_price"
                             value="<?= number_format($row['gmtt_hope_price']) ?>" placeholder="숫자만 입력" class="form-control" disabled>
                    </div>
                  <? }?>

                </div>


                <div class="form-group row margin-top-30">

                  <label for="gmtt_hope_price" class="col-sm-2 col-form-label">희망금액 <span
                      class="text-danger">*</span> <small>(단위 : 만원)</small></label>
                  <div class="col-sm-4 form-validate">
                    <input type="text" name="gmtt_hope_price" id="gmtt_hope_price"
                           value="<?= number_format($row['gmtt_hope_price']) ?>" placeholder="숫자만 입력" class="form-control" disabled>
                  </div>
                  <label for="gmt_sale_price" class="col-sm-2 col-form-label">분양가</label>
                  <div class="col-sm-4 form-validate">
                    <?php
                    $value = (isset($saleInfo['gmt_sale_price']) && $saleInfo['gmt_sale_price'] !== null)
                      ? $saleInfo['gmt_sale_price']
                      : 0;
                    ?>
                    <input type="text" name="gmt_sale_price" id="gmt_sale_price" value="<?= $value ?>만원" disabled
                           class="form-control">
                  </div>
                </div>

                <div class="form-group row margin-top-30">
                  <label for="gmtt_status" class="col-sm-2 col-form-label">진행상태</label>
                  <div class="col-sm-4 form-validate">
                    <input type="text" class="form-control" name="gmtt_status" value="<?= $arr_gmtt_status[$row['gmtt_status']] ?>" disabled />
                  </div>
                  <label for="gmtt_wdate" class="col-sm-2 col-form-label">등록일자</label>
                  <div class="col-sm-4 form-validate">
                    <input type="text" name="gmtt_wdate" id="gmtt_wdate"
                           value="<?= $row['gmtt_wdate'] ?>" class="form-control" disabled>
                  </div>
                </div>


                <div class="form-group row margin-top-30">
                  <label for="gmtt_conclusion_txt" class="col-sm-2 col-form-label">기타 특이사항</label>
                  <div class="col-sm-10 form-validate">
                    <input type="text" name="gmtt_conclusion_txt" id="gmtt_conclusion_txt"
                           value="<?= $row['gmtt_conclusion_txt'] ?>" class="form-control" disabled>
                  </div>
                </div>


                <div class="form-group row margin-top-30">
                  <h5 class="h5 col-sm-12">체결희망자</h5>
                </div>

                <div class="form-group row margin-top-30">
                  <label for="mt_conclusion_name" class="col-sm-2 col-form-label">고객명 </label>
                  <div class="col-sm-4 form-validate">
                    <input type="text" name="mt_conclusion_name" id="mt_conclusion_name" value="<?= $row['mt_conclusion_name'] ?>" placeholder="고객명 입력"
                           class="form-control" disabled>
                  </div>
                  <label for="mt_conclusion_hp" class="col-sm-2 col-form-label">전화번호 </label>
                  <div class="col-sm-4 form-validate">
                    <input type="text" name="mt_conclusion_hp" id="mt_conclusion_hp" value="<?= $row['mt_conclusion_hp'] ?>" placeholder="숫자만 입력"
                           class="form-control" disabled>
                  </div>
                </div>

                <div class="form-group row margin-top-30">
                  <label for="gmtt_conclusion_type" class="col-sm-2 col-form-label">구분 </label>
                  <div class="col-sm-10 form-validate">
                    <input type="text" name="gmtt_conclusion_type" id="gmtt_conclusion_type" value="<?= $arr_gmtt_type[$row['gmtt_conclusion_type']] ?>"
                           class="form-control" disabled>

                  </div>
                </div>


                <div class="form-group row margin-top-30">
                  <label for="gmtt_status" class="col-sm-2 col-form-label">진행상태 </label>
                  <div class="col-sm-4 form-validate">
                    <input type="text" name="gmtt_status" id="gmtt_status" value="<?= $arr_gmtt_status[$row['gmtt_status']] ?>"
                           class="form-control" disabled>
                  </div>
                  <label for="gmtt_hdate" class="col-sm-2 col-form-label">체결희망일자 </label>
                  <div class="col-sm-4 form-validate">
                    <input type="text" name="gmtt_hdate" id="gmtt_hdate" value="<?= DateType($row['gmtt_hdate'],1) ?>"
                           class="form-control" disabled>
                  </div>
                </div>



                <div class="form-group row justify-content-center margin-top-30">
                  <button type="button" onclick="history.go(-1);" class="btn btn-outline-secondary mx-1">목록</button>
                  <!--<button type="submit" class="btn btn-secondary">저장</button>-->
                </div>
              </div>


            </div>

          </form>


          <script>


              $(document).ready(function () {




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
                                  gmtt_status: {required: true},
                              },
                              messages: {
                                  gmtt_status: {required: '진행상태는 필수등록입니다.'},

                              },
                              ignore: function (index, element) {
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
                                  if (response.success) {
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
include $_SERVER['DOCUMENT_ROOT'] . "/mng/foot.inc.php";
?>