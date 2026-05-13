<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.menu.inc.php";



$_GET['act'] = 'pwd';
if ($_GET['act'] == "pwd") {
  $DB->where('mt_id', 'admin');
  $row = $DB->getone('member_t', '*, idx as mt_idx');

  $_act = "pwd";
}

?>
<!-- PAGE CONTENT CONTAINER -->
<div class="content" id="content">
  <!-- PAGE HEADING -->
  <div class="page-heading">
    <div class="page-heading__container">
      <div class="icon">
        <span class="li-picture3"></span>
      </div>
      <h1 class="title">관리자 비밀번호</h1>
      <p class="caption">
        관리자 비밀번호를 수정 할 수 있습니다.
      </p>
    </div>
    <nav aria-label="breadcrumb" role="navigation">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="./index.php">Dashboard</a></li>
        <li class="breadcrumb-item active">관리자 비밀번호</li>
      </ol>
    </nav>
  </div>
  <!-- //END PAGE HEADING -->
  <div class="container-fluid">
    <div class="card margin-bottom-0">

      <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
          <li class="nav-item"><a class="nav-link active" id="member-tab-1" data-toggle="tab" href="#member-1" role="tab" aria-controls="home" aria-selected="true">기본</a></li>
         
        </ul>
      </div>
      <div class="card-body">
        <form method="post" name="frm_form" id="frm_form" enctype="multipart/form-data">
          <input type="hidden" name="act" id="act" value="<?=$_act?>" />
          <input type="hidden" name="mt_id" id="mt_id" value="<?=$row['mt_id']?>" />
          <div class="tab-content margin-top-15" id="myTabContent">
            <div class="tab-pane fade show active" id="member-1" role="tabpanel" aria-labelledby="member-tab-1">


              <div class="form-group row">
                <label for="mt_pwd" class="col-sm-2 col-form-label">비밀번호</label>
                <div class="col-sm-10">
                  <input type="password" name="mt_pwd" id="mt_pwd" value="" class="form-control" />
                </div>
              </div>
              <div class="form-group row">
                <label for="mt_pwd" class="col-sm-2 col-form-label">비밀번호 확인</label>
                <div class="col-sm-10">
                  <input type="password" name="mt_pwd_re" id="mt_pwd_re" value="" class="form-control" />
                  <small class="form-text">비밀번호 변경시에는 비밀번호 확인까지 입력바랍니다.</small>
                </div>
              </div>


            </div>


          </div>
          <div class="form-group row justify-content-center margin-top-30">
            <button type="submit" class="btn btn-secondary" >확인</button>
          </div>
        </form>
      </div>
      <script type="text/javascript" src="<?=MNG_HTTP?>/js/fileupload.js?v=<?=$v_txt?>"></script>
      <script>


          $(document).ready(function() {


              // 폼 검증 및 제출
              const formHandler = {
                  init() {
                      this.initializeValidation();
                      this.setInitialValues();
                  },
                  initializeValidation() {
                      $.validator.addMethod("regex", function(value, element, regexp) {
                          var re = new RegExp(regexp);
                          return this.optional(element) || re.test(value);
                      }, "형식이 올바르지 않습니다.");

                      $("#frm_form").validate({
                          submitHandler: this.handleSubmit,
                          rules: {

                              mt_pwd: {
                                  required: true,
                                  minlength: 4,
                                  maxlength: 20
                              },
                              mt_pwd_re: {
                                  equalTo: "#mt_pwd"
                              }
                          },
                          messages: {
                              mt_pwd: {
                                  required: "비밀번호를 입력해주세요.",
                                  minlength: "비밀번호는 최소 {0}자 이상이어야 합니다.",
                                  maxlength: "비밀번호는 최대 {0}자까지만 가능합니다.",
                              },
                              mt_pwd_re: { equalTo: "비밀번호가 일치하지 않습니다" }
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
                          url: './admin_update.php',
                          type: 'POST',
                          data: formData,
                          processData: false,
                          contentType: false,
                          beforeSend: () => $('#splinner_modal').modal('show'),
                          success: (response) => {
                              $('#splinner_modal').modal('hide');
                              if(response.success) {
                                  app.toastr.showSuccess(response.message);
                              } else {
                                  app.toastr.showError(response.message);
                              }
                          },
                          error: (xhr, status, error) => {
                              $('#splinner_modal').modal('hide');
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



          // 페이지 로드 후 지도 초기화
          document.addEventListener('DOMContentLoaded', function() {
              console.log('DOM 로드 완료');
          });
      </script>

    </div>
  </div>
</div>
<!-- //END PAGE CONTENT CONTAINER -->
<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/foot.inc.php";
?>
