<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu='5';
$chk_sub_menu='1';
include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.menu.inc.php";
//include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.sidebar.inc.php";

$chk_ckeditor = "Y";
$file_count = 3;   // 원하는 첨부파일 갯수로 변경 가능
$board = "notice"; // 파일 업로드

if ($_GET['act'] == "update") {
  $DB->where('idx', $_GET['nt_idx']);
  $row = $DB->getone($CFG_TBL['notice']['default'], '*, idx as nt_idx');

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
    <div class="page-heading">
      <div class="page-heading__container">
        <div class="icon">
          <span class="li-picture3"></span>
        </div>
        <h1 class="title">공지사항</h1>
        <p class="caption">
          공지사항 등록, 수정을 할 수 있습니다.
        </p>
      </div>
      <nav aria-label="breadcrumb" role="navigation">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="#">고객센터</a></li>
          <li class="breadcrumb-item active">공지사항</li>
        </ol>
      </nav>
    </div>
    <!-- //END PAGE HEADING -->
    <div class="container-fluid">
      <div class="card margin-bottom-0">
        <div class="card-body">
          <form method="post" name="frm_form" id="frm_form" action="./update.php" target="hidden_ifrm" enctype="multipart/form-data">
            <input type="hidden" name="act" id="act" value="<?=$_act?>" />
            <input type="hidden" name="nt_idx" id="nt_idx" value="<?=$row['nt_idx']?>" />
            <input type="hidden" name="file_count" id="file_count" value="<?=$file_count?>" />
            <input type="hidden" name="board" id="board" value="<?=$board?>" />
            <div class="card-body">
              <div class="form-group row margin-top-30">
                <label for="nt_title" class="col-sm-2 col-form-label">제목</label>
                <div class="col-sm-10">
                  <input type="text" name="nt_title" id="nt_title" value="<?=$row['nt_title']?>" class="form-control">
                </div>
              </div>

              <div class="form-group row">
                <label for="nt_content" class="col-sm-2 col-form-label">내용</label>
                <div class="col-sm-10">
                  <?php
                  $editor_name = 'nt_content';
                  if($chk_ckeditor) {
                    $editor_upload = 'Y';
                    include $_SERVER['DOCUMENT_ROOT']."/mng/inc/ckeditor.php";
                  }else {
                    echo "<textarea name='".$editor_name."' id='".$editor_name."' class='form-control' style='height:250px;'>".$row[$editor_name]."</textarea>";
                  }
                  ?>
                </div>
              </div>
              <div class="form-group row">
                <label for="nt_show" class="col-sm-2 col-form-label">노출여부</label>
                <div class="col-sm-10">
                  <select name="nt_show" id="nt_show" class="form-control select-simple" data-initial-value="<?=$row['nt_show']?>">
                    <option value="Y">Y</option>
                    <option value="N">N</option>
                  </select>
                </div>
              </div>
                <div class="form-group row">
                    <label for="nt_show" class="col-sm-2 col-form-label">노출순서</label>
                    <div class="col-sm-10">
                        <input type="text" name="nt_order" id="nt_order" value="<?=$row['nt_order']?>" class="form-control">
                    </div>
                </div>
                <? if ($_GET['act'] == "update") {?>
                    <div class="form-group row align-items-center">
                        <label for="nt_wdate" class="col-md-2 col-form-label">등록일시</label>
                        <div class="col-sm-4 col-form-label">
                            <?=DateType($row['nt_wdate'], 6)?>
                        </div>
                        <label for="nt_wdate" class="col-md-2 col-form-label">수정일시</label>
                        <div class="col-sm-4 col-form-label">
                            <?=DateType($row['nt_udate'], 6)?>
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
                                  nt_title: {
                                      required: true
                                  },
                                  nt_content: {
                                      required: true
                                  }
                              },
                              messages: {
                                  nt_title: {
                                      required: "제목을 입력해주세요"
                                  },
                                  nt_content: {
                                      required: "내용을 입력해주세요."
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
                              dataType: 'json',
                              processData: false,
                              contentType: false,
                              beforeSend: () => $('#splinner_modal').modal('show'),
                              success: (response) => {
                                  $('#splinner_modal').modal('hide');
                                  if(response.success) {
                                      alert(response.message);
                                      if(response.redirect) {
                                          window.location.href = response.redirect;
                                      }
                                  } else {
                                      alert(response.message);
                                  }
                              },
                              error: (xhr, status, error) => {
                                  console.error('에러 상태:', status);         // e.g., "error"
                                  console.error('에러 메시지:', error);         // e.g., "Internal Server Error"
                                  console.error('HTTP 상태 코드:', xhr.status); // e.g., 500
                                  console.error('응답 본문:', xhr.responseText); // 서버에서 전송한 내용 (html, json 등)
                                  $('#splinner_modal').modal('hide');
                                  alert('처리 중 오류가 발생했습니다.');
                              }
                          });
                          return false;
                      },
                      setInitialValues() {
                          const fields = ['nt_show'];  // 초기화가 필요한 필드들
                          fields.forEach(field => {
                              const value = $(`#${field}`).data('initial-value');
                              if(value) {
                                  $(`#${field}`).val(value).trigger('change');  // change 이벤트 trigger 추가
                              }
                          });
                      }
                  };

                  // 파일 관련 핸들러
                  const fileHandler = {
                      init() {
                          this.bindEvents();
                      },

                      bindEvents() {
                          // 파일 선택 이벤트
                          $('.custom-file-input').on('change', this.handleFileSelect);
                      },

                      handleFileSelect(e) {
                          const fileName = e.target.files[0]?.name || "파일 선택";
                          const fileNum = this.id.replace('nt_file', '');
                          $(`#file-label${fileNum}`).text(fileName);
                          $(`#file${fileNum}_delete`).val('N');
                      }
                  };

                  // 초기화
                  formHandler.init();
                  fileHandler.init();
              });

              // 파일 삭제 함수
              function deleteFile(fileNum) {
                  if(confirm('파일을 삭제하시겠습니까?')) {
                      $(`#file${fileNum}_delete`).val('Y');
                      $(`#file-label${fileNum}`).text('파일 선택');
                      $(`#input_group${fileNum}`).removeClass('input-group');
                      $(`.input-group-prepend span[onclick="deleteFile(${fileNum})"]`).hide();
                  }
              }
          </script>
        </div>
      </div>

    </div>
  </div>
  <!-- //END PAGE CONTENT CONTAINER -->
<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/foot.inc.php";
?>
