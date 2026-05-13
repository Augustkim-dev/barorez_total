<?php
include $_SERVER['DOCUMENT_ROOT'] . "/mng/head.inc.php";
$chk_menu = '15';
$chk_sub_menu = '1';
include $_SERVER['DOCUMENT_ROOT'] . "/mng/inc/header.menu.inc.php";
//include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.sidebar.inc.php";

//$chk_post_code = "Y";
//$chk_ckeditor = "Y";

if ($_GET['act'] == "buy_update") {
  $DB->where('gmtt_idx', $_GET['nt_idx']);
  $row = $DB->getone($CFG_TBL['golf_membership']['transaction'], '*, gmtt_idx as nt_idx');
  $_act = "buy_update";
  $_act_txt = " 수정";
} else {
  $_act = "buy_input";
  $_act_txt = " 등록";
}

if ($_GET['gmt_idx']) {
  $DB->where('gmt_idx', $_GET['gmt_idx']);
  $saleInfo = $DB->getone($CFG_TBL['golf_membership']['main'], '*');

  $prices = getGolfPrice($saleInfo['gmt_idx']);
}


$DB->where('gmt_del', 'N');
$DB->orderBy("gmt_wdate", "desc");
$selectList = $DB->get($CFG_TBL['golf_membership']['main']);

$gmt_golf_name = '';
if ($_act == 'buy_input' && $saleInfo) {
  $gmt_golf_name = $saleInfo['gmt_golf_name'];
} else if ($_act == 'buy_update') {
  $gmt_golf_name = $row['gmt_golf_name'];
}




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
                  <label class="col-sm-2 col-form-label">주문형태</label>
                  <div class="col-sm-4 form-validate">
                    <input type="text" value="구매" class="form-control" disabled/>
                  </div>
                  <label for="gmt_golf_name" class="col-sm-2 col-form-label">골프장명 <span
                      class="text-danger">*</span></label>
                  <div class="col-sm-4 form-validate">

                      <?php
                        $isDisabled = isset($saleInfo['gmt_idx']) ? 'disabled' : '';
                      ?>

                      <select name="gmt_golf_name" id="gmt_golf_name" class="form-control" <?= $isDisabled ?> >
                        <option value="">선택하세요.</option>
                        <?php
                        foreach ($selectList as $key => $golf) {
                          $selected = ($gmt_golf_name == $golf['gmt_golf_name']) ? 'selected' : '';
                          printf('<option value="%s" %s>%s</option>', $golf['gmt_golf_name'], $selected, $golf['gmt_golf_name']);
                        }
                        ?>
                      </select>
                      <?php if ($isDisabled=='disabled'): ?>
                        <input type="hidden" name="gmt_golf_name" value="<?= htmlspecialchars($gmt_golf_name) ?>">
                      <?php endif; ?>

                  </div>
                </div>

                <div class="form-group row margin-top-30">
                  <label for="mt_name" class="col-sm-2 col-form-label">등록자 <span class="text-danger">*</span></label>
                  <div class="col-sm-4 form-validate">
                    <input type="text" name="mt_name" id="mt_name" value="<?= $row['mt_name'] ?>" placeholder="등록자 입력"
                           class="form-control">
                  </div>
                  <label for="mt_hp" class="col-sm-2 col-form-label">등록자 전화번호 <span class="text-danger">*</span></label>
                  <div class="col-sm-4 form-validate">
                    <input type="text" name="mt_hp" id="mt_hp" value="<?= $row['mt_hp'] ?>" placeholder="숫자만 입력"
                           class="form-control">
                  </div>
                </div>

                <div class="form-group row margin-top-30">
                  <label for="gmtt_type" class="col-sm-2 col-form-label">구분 <span class="text-danger">*</span></label>
                  <div class="col-sm-10 form-validate">
                    <select name="gmtt_type" id="gmtt_type" class="form-control select-simple">
                      <option value="">선택하세요.</option>
                      <?php
                      foreach ($arr_gmtt_type as $key => $value) {
                        $selected = ($row['gmtt_type'] == $key) ? 'selected' : '';
                        printf('<option value="%s" %s>%s</option>', $key, $selected, $value);
                      }
                      ?>
                    </select>
                  </div>
                </div>


                <div class="form-group row margin-top-30">
                  <label for="gmtt_hope_price" class="col-sm-2 col-form-label">구매 희망금액 <span
                      class="text-danger">*</span> <small>(단위 : 만원)</small></label>
                  <div class="col-sm-10 form-validate">
                    <input type="text" name="gmtt_hope_price" id="gmtt_hope_price"
                           value="<?= $row['gmtt_hope_price'] ?>" placeholder="숫자만 입력" class="form-control">
                  </div>
                </div>

                <div class="form-group row margin-top-30">
                  <label for="gmt_sale_price" class="col-sm-2 col-form-label">분양가</label>
                  <div class="col-sm-4 form-validate">
                    <?php
                    $value = (isset($saleInfo['gmt_sale_price']) && $saleInfo['gmt_sale_price'] !== null)
                      ? $saleInfo['gmt_sale_price']
                      : 0;
                    ?>
                    <input type="text" name="gmt_sale_price" id="gmt_sale_price" value="<?= $value ?>만원" readonly
                           class="form-control">
                  </div>
                  <label for="gmt_conclusion_price" class="col-sm-2 col-form-label">체결가능금액</label>
                  <div class="col-sm-4 form-validate">
                    <?php
                    $value = (isset($saleInfo['gmt_conclusion_price']) && $saleInfo['gmt_conclusion_price'] !== null)
                      ? $saleInfo['gmt_conclusion_price']
                      : 0;
                    ?>
                    <input type="text" name="gmt_conclusion_price" id="gmt_conclusion_price" value="<?= $value ?>만원"
                           readonly class="form-control">
                  </div>
                </div>

                <div class="form-group row margin-top-30">
                  <label for="gmt_now_buy_price" class="col-sm-2 col-form-label">즉시구매가</label>
                  <div class="col-sm-4 form-validate">
                    <?php
                    $value = (isset($saleInfo['gmt_now_buy_price']) && $saleInfo['gmt_now_buy_price'] !== null)
                      ? $saleInfo['gmt_now_buy_price']
                      : 0;
                    ?>
                    <input type="text" name="gmt_now_buy_price" id="gmt_now_buy_price" value="<?= $value ?>만원" readonly
                           class="form-control">
                  </div>
                  <label for="gmt_now_sale_price" class="col-sm-2 col-form-label">즉시판매가</label>
                  <div class="col-sm-4 form-validate">
                    <?php
                    $value = (isset($saleInfo['gmt_now_sale_price']) && $saleInfo['gmt_now_sale_price'] !== null)
                      ? $saleInfo['gmt_now_sale_price']
                      : 0;
                    ?>
                    <input type="text" name="gmt_now_sale_price" id="gmt_now_sale_price" value="<?= $value ?>만원"
                           readonly class="form-control">
                  </div>
                </div>


                <div class="form-group row margin-top-30">
                  <label for="gmtt_conclusion_txt" class="col-sm-2 col-form-label">기타 특이사항</label>
                  <div class="col-sm-10 form-validate">
                    <input type="text" name="gmtt_conclusion_txt" id="gmtt_conclusion_txt"
                           value="<?= $row['gmtt_conclusion_txt'] ?>" class="form-control">
                  </div>
                </div>


                <div class="form-group row justify-content-center margin-top-30">

                  <button type="button" onclick="history.go(-1);" class="btn btn-outline-secondary mx-1">목록</button>
                  <? if ($_act == 'buy_input' || $_act == 'buy_update') { ?>
                    <button type="submit" class="btn btn-secondary"><?= $_act_txt ?></button>
                  <? } ?>
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
                                  gmt_golf_name: {required: true},
                                  mt_name: {required: true},
                                  mt_hp: {required: true, digits: true},
                                  gmtt_type: {required: true},
                                  gmtt_hope_price: {required: true, digits: true},
                              },
                              messages: {
                                  gmt_golf_name: {required: '골프장명은 필수등록입니다.'},
                                  mt_name: {required: '등록자는 필수등록입니다.'},
                                  mt_hp: {required: '등록자 전화번호는 필수등록입니다.', digits: "숫자만 입력 가능합니다."},
                                  gmtt_type: {required: '구분은 필수등록입니다.'},
                                  gmtt_hope_price: {required: '구매 희망금액은 필수등록입니다.', digits: "숫자만 입력 가능합니다."},

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
                                  app.toastr.showError(error);
                              }
                          });
                          return false;
                      },
                      setInitialValues() {

                      }
                  };

                  // 초기화
                  formHandler.init();


                  $('#gmt_golf_name').on('change', function () {
                      const golfName = $(this).val();

                      if (!golfName) return;

                      $.ajax({
                          url: './update.php',
                          type: 'POST',
                          data: { act:'golf_price_info', golf_name: golfName },
                          dataType: 'json',
                          success: function (response) {
                              console.log(response)
                              if (response.success) {
                                  $('#gmt_sale_price').val(response.data.gmt_sale_price + '만원');
                                  $('#gmt_conclusion_price').val(response.data.gmt_conclusion_price + '만원');
                                  $('#gmt_now_buy_price').val(response.data.gmt_now_buy_price + '만원');
                                  $('#gmt_now_sale_price').val(response.data.gmt_now_sale_price + '만원');
                              } else {
                                  app.toastr.showError('가격 정보를 불러올 수 없습니다.');
                              }
                          },
                          error: (xhr, status, error) => {
                              console.error(error)
                              app.toastr.showError(error);
                          }
                      });
                  });


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