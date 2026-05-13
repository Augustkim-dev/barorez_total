<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.menu.inc.php";

$chk_post_code = "Y";
$file_count = 1;
$board = "admin"; // 파일 업로드

$_GET['act'] = 'update';
if ($_GET['act'] == "update") {
  $DB->where('mt_id', 'admin');
  $row = $DB->getone('member_t', '*, idx as mt_idx');

  $_act = "update";
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
      <h1 class="title">관리자 정보</h1>
      <p class="caption">
        관리자 정보를 수정 할 수 있습니다.
      </p>
    </div>
    <nav aria-label="breadcrumb" role="navigation">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="./index.php">Dashboard</a></li>
        <li class="breadcrumb-item active">관리자 정보</li>
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
          <input type="hidden" name="mt_idx" id="mt_idx" value="<?=$row['mt_idx']?>" />
          <div class="tab-content margin-top-15" id="myTabContent">
            <div class="tab-pane fade show active" id="member-1" role="tabpanel" aria-labelledby="member-tab-1">


              <div class="form-group row">
                <label for="mt_name" class="col-sm-2 col-form-label">이름 <b class="text-danger">*</b></label>
                <div class="col-sm-10">
                  <input type="text" name="mt_name" id="mt_name" value="<?=$row['mt_name']?>" class="form-control">
                </div>
              </div>
              <div class="form-group row">
                <label for="mt_hp" class="col-sm-2 col-form-label">휴대폰 번호 <b class="text-danger">*</b></label>
                <div class="col-sm-10">
                  <input type="text" name="mt_hp" id="mt_hp" value="<?=$row['mt_hp']?>" class="form-control" numberOnly placeholder="'-'없이 입력바랍니다.">
                </div>
              </div>
              <div class="form-group row">
                <label for="mt_email" class="col-sm-2 col-form-label">E-mail <b class="text-danger">*</b></label>
                <div class="col-sm-10">
                  <input type="text" name="mt_email" id="mt_email" value="<?=$row['mt_email']?>" class="form-control">
                </div>
              </div>
              <div class="form-group row">
                <label for="mt_position" class="col-sm-2 col-form-label">직책</label>
                <div class="col-sm-10">
                  <input type="text" name="mt_position" id="mt_position" value="<?=$row['mt_position']?>" class="form-control">
                </div>
              </div>
              <div class="form-group row">
                <label for="wrap_zip1" class="col-sm-2 col-form-label">주소</label>
                <div class="col-sm-10">
                  <p class="form-inline">
                    <input type="text" class="form-control" name="mt_zip" id="mt_zip" value="<?=$row['mt_zip']?>" style="width:100px;" placeholder="" readonly="">
                    <button type="button" class="btn btn-secondary ml-2" onclick="DaumPostcode('mt_zip', 'mt_add1', 'mt_add2', 'wrap_zip1');">우편번호</button>
                  </p>
                  <div id="wrap_zip1" style="display:none;border:1px solid;width:100%;height:300px;margin:5px 0;position:relative">
                    <img src="//i1.daumcdn.net/localimg/localimages/07/postcode/320/close.png" id="btnFoldWrap" style="cursor:pointer;position:absolute;right:0px;top:-1px;z-index:1" onclick="foldDaumPostcode('wrap_zip1')" alt="접기 버튼">
                  </div>
                  <p>
                    <input type="text" class="form-control" name="mt_add1" id="mt_add1" value="<?=$row['mt_add1']?>" placeholder="" readonly="">
                  </p>
                  <p>
                    <input type="text" class="form-control" name="mt_add2" id="mt_add2" value="<?=$row['mt_add2']?>" placeholder="">
                  </p>
                </div>
              </div>

              <div class="form-group row">
                <label for="ct_show" class="col-sm-2 col-form-label">프로필</label>
                <div class="col-sm-10">
                  <div class="upload-container" id="sortableContainer">
                    <div class="upload-box" id="uploadTrigger">
                      <div class="plus">+</div>
                      <div class="text">Upload</div>
                    </div>
                  </div>
                  <input type="file" class="filepond d-none" multiple>
                </div>
              </div>


            </div>
          </div>
          <div class="form-group row justify-content-center margin-top-30">
            <button type="submit" class="btn btn-secondary" >확인</button>
          </div>
        </form>
      </div>



      <script type="application/javascript">

          $(document).ready(function() {

              // FileUploader 초기화
              const uploader = createFileUploader({
                  container: '.upload-container',
                  trigger: '#uploadTrigger',
                  filepondElement: '.filepond',
                  maxFiles: 1,
                  maxFileSize: '5MB',
                  allowedFileTypes: ['image/jpeg', 'image/png', 'image/gif'],
                  imageMinWidth: 100,
                  imageMinHeight: 100,
                  imageMaxWidth: 4000,
                  imageMaxHeight: 4000,
                  ajaxUrl: './admin_update.php'
              });

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

                              mt_name: {
                                  required: true,
                                  minlength: 2,
                                  maxlength: 20
                              },
                              mt_hp: {
                                  required: true,
                                  regex: /^01[0|1|6|7|8|9][0-9]{3,4}[0-9]{4}$/
                              },
                              mt_email: {
                                  required: true,
                                  email: true,
                                  regex: /^[0-9a-zA-Z]([-_.]?[0-9a-zA-Z])*@[0-9a-zA-Z]([-_.]?[0-9a-zA-Z])*\.[a-zA-Z]{2,3}$/
                              },
                          },
                          messages: {
                              mt_name: {
                                  required: "이름을 입력해주세요.",
                                  minlength: "이름은 최소 {0}자 이상이어야 합니다.",
                                  maxlength: "이름은 최대 {0}자까지만 가능합니다.",
                              },
                              mt_hp: {
                                  required: "휴대폰 번호를 입력해주세요",
                                  regex: "올바른 휴대폰 번호 형식이 아닙니다"
                              },
                              mt_email: {
                                  required: "이메일을 입력해주세요",
                                  email: "올바른 이메일 형식을 입력해주세요",
                                  regex: "올바른 이메일 형식이 아닙니다"
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
                      formData.append('maxFiles', uploader.options.maxFiles);

                      // FilePond 파일들을 FormData에 추가
                      const files = uploader.getPond().getFiles();
                      files.forEach((fileItem, index) => {
                          if (index < uploader.options.maxFiles) {  // maxFiles 값 만큼만 전송
                              formData.append(`mt_image${index + 1}`, fileItem.file);
                          }
                      });

                      // 삭제된 파일 정보 전송
                      const removedFiles = uploader.getRemovedFiles(); // 삭제된 파일 번호 배열
                      formData.append('removed_files', JSON.stringify(removedFiles));


                      // 이미지 순서 정보 추가
                      const imageOrder = uploader.getImageOrder();
                      formData.append('image_order', JSON.stringify(imageOrder));

                      $.ajax({
                          url: './admin_update.php',
                          type: 'POST',
                          data: formData,
                          processData: false,
                          contentType: false,
                          beforeSend: () => $('#splinner_modal').modal('show'),
                          success: (response) => {
                              $('#splinner_modal').modal('hide');

                              // console.log(response)
                              if(response.success) {
                                  app.toastr.showSuccess(response.message, response.redirect);
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

            <?php if(isset($row['mt_image1'])) { ?>
              const mt_idx = '<?php echo $row["idx"] ?? ""; ?>';
              if(mt_idx) {
                  uploader.loadImages(mt_idx);
              }
            <?php } ?>
          });




      </script>

    </div>
  </div>
</div>
<!-- //END PAGE CONTENT CONTAINER -->
<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/foot.inc.php";
?>
