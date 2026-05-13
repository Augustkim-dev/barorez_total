<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu='1';
$chk_sub_menu='3';
include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.menu.inc.php";
//include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.sidebar.inc.php";

$tbl_name = "member_grade_t";

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
              <h4 id="rw-fe-basic">회원등급 <?=$_act_txt?></h4>

              <div class="form-group row margin-top-30">
                <label for="w_code" class="col-sm-2 col-form-label">코드</label>
                <div class="col-sm-10">
                  <input type="text" name="w_code" id="w_code" value="<?=$row['w_code']?>" class="form-control">
                </div>
              </div>
              <div class="form-group row margin-top-30">
                <label for="w_name" class="col-sm-2 col-form-label">이름</label>
                <div class="col-sm-10">
                  <input type="text" name="w_name" id="w_name" value="<?=$row['w_name']?>" class="form-control">
                </div>
              </div>
              <div class="form-group row margin-top-30">
                <label for="w_scan" class="col-sm-2 col-form-label">무료스캔횟수</label>
                <div class="col-sm-10">
                  <input type="number" name="w_scan" id="w_scan" value="<?=$row['w_scan']?>" class="form-control">
                </div>
              </div>
              <div class="form-group row margin-top-30">
                <label for="w_upgrade_condition" class="col-sm-2 col-form-label">승급 조건 설명</label>
                <div class="col-sm-10">
                  <input type="text" name="w_upgrade_condition" id="w_upgrade_condition" value="<?=$row['w_upgrade_condition']?>" class="form-control">
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
                                  w_code: {
                                      required: true
                                  },
                                  w_name: {
                                      required: true
                                  },
                                  w_scan: {
                                      required: true,
                                      number: true
                                  },
                              },
                              messages: {
                                  w_code: {
                                      required: "코드를 입력해주세요"
                                  },
                                  w_name: {
                                      required: "이름을 입력해주세요"
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
                              url: './member_grade_update.php',
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