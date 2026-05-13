<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu = 1;
$chk_sub_menu= 7;
include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.menu.inc.php";
//include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.sidebar.inc.php";




$DB->orderBy('idx', 'asc');
$list = $DB->get($CFG_TBL['user']['menu']);

$_act = "user_menu";



?>
<!-- PAGE CONTENT CONTAINER -->
<div class="content" id="content">
  <!-- PAGE HEADING -->
  <div class="page-heading">
    <div class="page-heading__container">
      <div class="icon">
        <span class="li-picture3"></span>
      </div>
      <h1 class="title">등급관리</h1>
      <p class="caption">
        등급관리를 할 수 있습니다.
      </p>
    </div>
    <nav aria-label="breadcrumb" role="navigation">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="#">회원관리</a></li>
        <li class="breadcrumb-item active">등급관리</li>
      </ol>
    </nav>
  </div>
  <!-- //END PAGE HEADING -->
  <div class="container-fluid">
    <div class="card margin-bottom-0">

      <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
          <li class="nav-item"><a class="nav-link active" id="member-tab-1" data-toggle="tab" href="#member-1" role="tab" aria-controls="home" aria-selected="true">기본정보</a></li>
        </ul>
      </div>
      <div class="card-body">
        <form method="post" name="frm_form" id="frm_form" action="./member_update.php" target="hidden_ifrm" enctype="multipart/form-data">
          <input type="hidden" name="act" id="act" value="<?=$_act?>" />

          <table class="table">
            <thead>
            <tr>
              <th>메뉴</th>
              <th>등급</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($list as $row):
              $levels = explode('|:|', $row['umt_level']);
              ?>
              <tr>
                <td><?=htmlspecialchars($row['umt_name'])?></td>
                <td>

                  <div class="form-check form-check-inline">
                    <div class="custom-control custom-checkbox mr-3">
                      <input type="checkbox" class="custom-control-input" id="idx1_<?=$row['idx']?>" name="umt_level[<?=$row['idx']?>][]" value="1" <?=in_array('1', $levels) ? 'checked' : ''?> />
                      <label class="custom-control-label" for="idx1_<?=$row['idx']?>">관리자</label>
                    </div>
                    <div class="custom-control custom-checkbox mr-3">
                      <input type="checkbox" class="custom-control-input" id="idx2_<?=$row['idx']?>" name="umt_level[<?=$row['idx']?>][]" value="2" <?=in_array('2', $levels) ? 'checked' : ''?> />
                      <label class="custom-control-label" for="idx2_<?=$row['idx']?>">인증회원</label>
                    </div>
                    <div class="custom-control custom-checkbox mr-3">
                      <input type="checkbox" class="custom-control-input" id="idx3_<?=$row['idx']?>" name="umt_level[<?=$row['idx']?>][]" value="3" <?=in_array('3', $levels) ? 'checked' : ''?> />
                      <label class="custom-control-label" for="idx3_<?=$row['idx']?>">미인증회원</label>
                    </div>
                  </div>

                </td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>


          <div class="form-group row justify-content-center margin-top-30">
            <button type="submit" class="btn btn-secondary" >저장</button>
          </div>
        </form>
      </div>

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
                      });
                  },

                  handleSubmit(form) {

                      const formData = new FormData(form);

                      $.ajax({
                          url: './member_update.php',
                          type: 'POST',
                          data: formData,
                          processData: false,
                          contentType: false,
                          beforeSend: () => $('#splinner_modal').modal('show'),
                          success: (response) => {

                              $('#splinner_modal').modal('hide');
                              if(response.success) {
                                  app.toastr.showSuccess(response.message, 'reload');
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


              formHandler.init();
          });

      </script>

    </div>
  </div>
</div>
<!-- //END PAGE CONTENT CONTAINER -->
<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/foot.inc.php";
?>
