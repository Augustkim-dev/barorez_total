<?php
include $_SERVER['DOCUMENT_ROOT'] . "/mng/head.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/mng/inc/header.menu.inc.php";
//include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.sidebar.inc.php";



if ($_GET['act'] == "update") {
  $DB->where('ct_id', $_GET['ct_id']);
  $row = $DB->getone('category_t', '*');


  $_act = "update";
  $_act_txt = " 수정";
} else {
  $_act = "input";
  $_act_txt = " 등록";
}

$DB->where('a1.ct_level', 1);
$DB->orWhere('a1.ct_level', 2);
$DB->orderBy("a1.ct_path", "asc");
$DB->orderBy("a1.ct_rank", "asc");
$category_list = $DB->get("category_t a1");
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
          공지사항 등록, 수정, 삭제 등을 할 수 있습니다.
        </p>
      </div>
      <nav aria-label="breadcrumb" role="navigation">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="#">게시판관리</a></li>
          <li class="breadcrumb-item active">공지사항</li>
        </ol>
      </nav>
    </div>
    <!-- //END PAGE HEADING -->
    <div class="container-fluid">
      <div class="card margin-bottom-0">
        <div class="card-body">
          <form method="post" name="frm_form" id="frm_form" action="./category_update.php" target="hidden_ifrm"
                enctype="multipart/form-data">
            <input type="hidden" name="act" id="act" value="<?= $_act ?>"/>
            <input type="hidden" name="ct_id" id="ct_id" value="<?= $row['ct_id'] ?>"/>
            <input type="hidden" name="file_count" id="file_count" value="3"/>
            <div class="card-body">
              <h4 id="rw-fe-basic">카테고리 <?= $_act_txt ?></h4>

              <!-- 상위 카테고리 선택 -->
              <div class="form-group row margin-top-30">
                <label for="ct_pid" class="col-sm-2 col-form-label">상위 카테고리</label>
                <div class="col-sm-10">
                  <select name="ct_pid" id="ct_pid" class="form-control select2"
                          data-initial-value="<?= $row['ct_pid'] ?>">
                    <option value="">최상위 카테고리</option>
                    <?php
                    // 상위 카테고리 목록 출력
                    foreach ($category_list as $cat) {
                      // 자기 자신은 상위 카테고리로 선택할 수 없도록 제외
                      if ($cat['ct_id'] != $row['ct_id']) {
                        $selected = ($cat['ct_id'] == $row['ct_pid']) ? 'selected' : '';
                        echo '<option value="' . $cat['ct_id'] . '" ' . $selected . '>' . $cat['ct_full_name'] . '</option>';
                      }
                    }
                    ?>
                  </select>
                </div>
              </div>

              <!-- 카테고리 코드 -->
              <div class="form-group row">
                <label for="ct_code" class="col-sm-2 col-form-label">카테고리 코드</label>
                <div class="col-sm-10">
                  <input type="text" name="ct_code" id="ct_code" value="<?= $row['ct_code'] ?>" class="form-control">
                  <small class="form-text text-muted">영문, 숫자, 하이픈(-)만 사용 가능합니다. (예: FASHION-MEN)</small>
                </div>
              </div>

              <!-- 카테고리명 -->
              <div class="form-group row">
                <label for="ct_name" class="col-sm-2 col-form-label">카테고리명</label>
                <div class="col-sm-10">
                  <input type="text" name="ct_name" id="ct_name" value="<?= $row['ct_name'] ?>" class="form-control">
                </div>
              </div>

              <!-- 부카테고리명 -->
              <div class="form-group row">
                <label for="ct_sub_name" class="col-sm-2 col-form-label">부카테고리명</label>
                <div class="col-sm-10">
                  <input type="text" name="ct_sub_name" id="ct_sub_name" value="<?= $row['ct_sub_name'] ?>"
                         class="form-control">
                  <small class="form-text text-muted">카테고리 부제목으로 사용됩니다. (선택사항)</small>
                </div>
              </div>

              <!-- 노출 순서 -->
              <div class="form-group row">
                <label for="ct_rank" class="col-sm-2 col-form-label">노출 순서</label>
                <div class="col-sm-10">
                  <input type="number" name="ct_rank" id="ct_rank" value="<?= $row['ct_rank'] ? $row['ct_rank'] : 10 ?>"
                         class="form-control" min="1">
                  <small class="form-text text-muted">숫자가 작을수록 먼저 노출됩니다. (기본값: 10)</small>
                </div>
              </div>

              <!-- 카테고리 설명 -->
              <div class="form-group row">
                <label for="ct_description" class="col-sm-2 col-form-label">카테고리 설명</label>
                <div class="col-sm-10">
                  <textarea name="ct_description" id="ct_description" class="form-control"
                            style="height:100px;"><?= $row['ct_description'] ?></textarea>
                </div>
              </div>

              <!-- URL 별칭 -->
              <div class="form-group row">
                <label for="ct_url_alias" class="col-sm-2 col-form-label">URL 별칭</label>
                <div class="col-sm-10">
                  <input type="text" name="ct_url_alias" id="ct_url_alias" value="<?= $row['ct_url_alias'] ?>"
                         class="form-control">
                  <small class="form-text text-muted">SEO에 최적화된 URL을 입력하세요. (예: fashion/men)</small>
                </div>
              </div>

              <!-- SEO 메타 정보 -->
              <div class="form-group row">
                <label for="ct_meta_title" class="col-sm-2 col-form-label">메타 타이틀</label>
                <div class="col-sm-10">
                  <input type="text" name="ct_meta_title" id="ct_meta_title" value="<?= $row['ct_meta_title'] ?>"
                         class="form-control">
                </div>
              </div>

              <div class="form-group row">
                <label for="ct_meta_keywords" class="col-sm-2 col-form-label">메타 키워드</label>
                <div class="col-sm-10">
                  <input type="text" name="ct_meta_keywords" id="ct_meta_keywords"
                         value="<?= $row['ct_meta_keywords'] ?>" class="form-control">
                  <small class="form-text text-muted">키워드는 쉼표(,)로 구분하여 입력하세요.</small>
                </div>
              </div>

              <div class="form-group row">
                <label for="ct_meta_desc" class="col-sm-2 col-form-label">메타 설명</label>
                <div class="col-sm-10">
                  <textarea name="ct_meta_desc" id="ct_meta_desc" class="form-control"
                            style="height:80px;"><?= $row['ct_meta_desc'] ?></textarea>
                </div>
              </div>

              <!-- 카테고리 이미지 -->
              <div class="form-group row">
                <label for="ct_file1" class="col-sm-2 col-form-label">카테고리 이미지</label>
                <div class="col-sm-10">
                  <div
                    class="<?php echo (isset($row['ct_file1']) && !empty($row['ct_file1'])) ? 'input-group' : ''; ?> mb-0"
                    id="input_group1">
                    <?php if (isset($row['ct_file1']) && !empty($row['ct_file1'])) { ?>
                      <div class="input-group-prepend">
                            <span class="input-group-text" onclick="deleteFile(1)" style="cursor: pointer;">
                                <i class="fa fa-remove"></i>
                            </span>
                      </div>
                      <div class="input-group-prepend">
                            <span class="input-group-text">
                                <a href="<?= $file_path . $row['ct_file1'] ?>" target="_blank"><i class="fa fa-eye"></i></a>
                            </span>
                      </div>
                    <?php } ?>
                    <label class="custom-file">
                      <input type="file" id="ct_file1" name="ct_file1" class="custom-file-input">
                      <span class="custom-file-label" id="file-label1">
                            <?php if (isset($row['ct_file1']) && !empty($row['ct_file1'])) { ?>
                              <?= $row['ct_file1'] ?>
                            <?php } else { ?>
                              파일 선택
                            <?php } ?>
                        </span>
                      <input type="hidden" name="file1_delete" id="file1_delete" value="N">
                    </label>
                  </div>
                  <small class="form-text text-muted">권장 크기: 800x400px, 최대 2MB</small>
                </div>
              </div>

              <!-- 카테고리 아이콘 -->
              <div class="form-group row">
                <label for="ct_file2" class="col-sm-2 col-form-label">카테고리 아이콘</label>
                <div class="col-sm-10">
                  <div
                    class="<?php echo (isset($row['ct_file2']) && !empty($row['ct_file2'])) ? 'input-group' : ''; ?> mb-0"
                    id="input_group2">
                    <?php if (isset($row['ct_file2']) && !empty($row['ct_file2'])) { ?>
                      <div class="input-group-prepend">
                            <span class="input-group-text" onclick="deleteFile(2)" style="cursor: pointer;">
                                <i class="fa fa-remove"></i>
                            </span>
                      </div>
                      <div class="input-group-prepend">
                            <span class="input-group-text">
                                <a href="<?= $file_path . $row['ct_file2'] ?>" target="_blank"><i class="fa fa-eye"></i></a>
                            </span>
                      </div>
                    <?php } ?>
                    <label class="custom-file">
                      <input type="file" id="ct_file2" name="ct_file2" class="custom-file-input">
                      <span class="custom-file-label" id="file-label2">
                            <?php if (isset($row['ct_file2']) && !empty($row['ct_file2'])) { ?>
                              <?= $row['ct_file2'] ?>
                            <?php } else { ?>
                              파일 선택
                            <?php } ?>
                        </span>
                      <input type="hidden" name="file2_delete" id="file2_delete" value="N">
                    </label>
                  </div>
                  <small class="form-text text-muted">권장 크기: 64x64px, 최대 1MB</small>
                </div>
              </div>

              <!-- 카테고리 배너 -->
              <div class="form-group row">
                <label for="ct_banner" class="col-sm-2 col-form-label">카테고리 배너</label>
                <div class="col-sm-10">
                  <div
                    class="<?php echo (isset($row['ct_banner']) && !empty($row['ct_banner'])) ? 'input-group' : ''; ?> mb-0"
                    id="input_group3">
                    <?php if (isset($row['ct_banner']) && !empty($row['ct_banner'])) { ?>
                      <div class="input-group-prepend">
                            <span class="input-group-text" onclick="deleteFile(3)" style="cursor: pointer;">
                                <i class="fa fa-remove"></i>
                            </span>
                      </div>
                      <div class="input-group-prepend">
                            <span class="input-group-text">
                                <a href="<?= $file_path . $row['ct_banner'] ?>" target="_blank"><i
                                    class="fa fa-eye"></i></a>
                            </span>
                      </div>
                    <?php } ?>
                    <label class="custom-file">
                      <input type="file" id="ct_banner" name="ct_banner" class="custom-file-input">
                      <span class="custom-file-label" id="file-label3">
                            <?php if (isset($row['ct_banner']) && !empty($row['ct_banner'])) { ?>
                              <?= $row['ct_banner'] ?>
                            <?php } else { ?>
                              파일 선택
                            <?php } ?>
                        </span>
                      <input type="hidden" name="file3_delete" id="file3_delete" value="N">
                    </label>
                  </div>
                  <small class="form-text text-muted">권장 크기: 1200x300px, 최대 3MB</small>
                </div>
              </div>

              <!-- 노출 설정 -->
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">노출 설정</label>
                <div class="col-sm-10">
                  <div class="row">
                    <div class="col-md-4">
                      <label for="ct_show" class="d-block">사용 여부</label>
                      <select name="ct_show" id="ct_show" class="form-control "
                              data-initial-value="<?= $row['ct_show'] ? $row['ct_show'] : 'Y' ?>">
                        <option value="Y">Y</option>
                        <option value="N">N</option>
                      </select>
                    </div>
                    <div class="col-md-4">
                      <label for="ct_show_menu" class="d-block">메뉴 노출</label>
                      <select name="ct_show_menu" id="ct_show_menu" class="form-control "
                              data-initial-value="<?= $row['ct_show_menu'] ? $row['ct_show_menu'] : 'Y' ?>">
                        <option value="Y">Y</option>
                        <option value="N">N</option>
                      </select>
                    </div>
                    <div class="col-md-4">
                      <label for="ct_show_main" class="d-block">메인 노출</label>
                      <select name="ct_show_main" id="ct_show_main" class="form-control "
                              data-initial-value="<?= $row['ct_show_main'] ? $row['ct_show_main'] : 'N' ?>">
                        <option value="Y">Y</option>
                        <option value="N">N</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>

              <!-- 수정 시에만 표시되는 정보 -->
              <?php if ($_GET['act'] == "update") { ?>
                <div class="form-group row">
                  <label class="col-md-2 col-form-label">카테고리 정보</label>
                  <div class="col-md-10">
                    <div class="row">
                      <div class="col-md-4">
                        <label class="d-block">카테고리 레벨</label>
                        <p class="form-control-static"><?= $row['ct_level'] ?></p>
                      </div>
                      <div class="col-md-4">
                        <label class="d-block">카테고리 경로</label>
                        <p class="form-control-static"><?= $row['ct_path'] ?></p>
                      </div>
                      <div class="col-md-4">
                        <label class="d-block">최하위 카테고리 여부</label>
                        <p class="form-control-static"><?= $row['ct_is_leaf'] ?></p>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="ct_wdate" class="col-md-2 col-form-label">등록일시</label>
                  <div class="col-md-10">
                    <?= DateType($row['ct_created_at'], 6) ?>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="ct_udate" class="col-md-2 col-form-label">수정일시</label>
                  <div class="col-md-10">
                    <?= DateType($row['ct_updated_at'], 6) ?>
                  </div>
                </div>
              <?php } ?>

              <!-- 버튼 영역 -->
              <div class="form-group row justify-content-center margin-top-30">
                <button type="button" onclick="history.go(-1);" class="btn btn-outline-secondary mx-1">목록</button>
                <button type="submit" class="btn btn-secondary">확인</button>
              </div>
            </div>
          </form>

          <script>
              $(document).ready(function () {
                  // Select2 초기화 (상위 카테고리 선택용)
                  $('.select2').select2({
                      placeholder: "상위 카테고리를 선택하세요",
                      allowClear: true
                  });

                  // 폼 검증 및 제출 핸들러
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
                                  ct_code: {
                                      required: true,
                                      regex: /^[A-Za-z0-9\-]+$/
                                  },
                                  ct_name: {
                                      required: true
                                  },
                                  ct_rank: {
                                      required: true,
                                      number: true,
                                      min: 1
                                  }
                              },
                              messages: {
                                  ct_code: {
                                      required: "카테고리 코드를 입력해주세요",
                                      regex: "영문, 숫자, 하이픈(-)만 사용 가능합니다"
                                  },
                                  ct_name: {
                                      required: "카테고리명을 입력해주세요"
                                  },
                                  ct_rank: {
                                      required: "노출 순서를 입력해주세요",
                                      number: "숫자만 입력 가능합니다",
                                      min: "1 이상의 값을 입력해주세요"
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
                              url: './category_update.php',
                              type: 'POST',
                              data: formData,
                              processData: false,
                              contentType: false,
                              beforeSend: () => $('#splinner_modal').modal('show'),
                              success: (response) => {
                                  $('#splinner_modal').modal('hide');

                                  console.log(response)


                                  if (response.success) {
                                      alert(response.message);
                                      if (response.redirect) {
                                          window.location.href = response.redirect;
                                      }
                                  } else {
                                      alert(response.message);
                                  }
                              },
                              error: (xhr, status, error) => {
                                  $('#splinner_modal').modal('hide');
                                  console.log(error)
                                  alert('처리 중 오류가 발생했습니다.');
                              }
                          });
                          return false;
                      },

                      setInitialValues() {
                          const fields = ['ct_show', 'ct_show_menu', 'ct_show_main'];  // 초기화가 필요한 필드들
                          fields.forEach(field => {
                              const value = $(`#${field}`).data('initial-value');
                              if (value) {
                                  $(`#${field}`).val(value).trigger('change');
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
                          const fileId = this.id;
                          let fileNum;

                          if (fileId === 'ct_file1') fileNum = 1;
                          else if (fileId === 'ct_file2') fileNum = 2;
                          else if (fileId === 'ct_banner') fileNum = 3;

                          $(`#file-label${fileNum}`).text(fileName);
                          $(`#file${fileNum}_delete`).val('N');
                      }
                  };

                  // URL 별칭 자동 생성 (카테고리명 기반)
                  $('#ct_name').on('blur', function () {
                      const urlAliasField = $('#ct_url_alias');
                      // URL 별칭이 비어있을 때만 자동 생성
                      if (!urlAliasField.val()) {
                          let urlAlias = $(this).val()
                              .toLowerCase()
                              .replace(/\s+/g, '-')     // 공백을 하이픈으로 변경
                              .replace(/[^a-z0-9\-]/g, ''); // 영문 소문자, 숫자, 하이픈만 허용

                          // 상위 카테고리가 있으면 경로에 추가
                          const parentId = $('#ct_pid').val();
                          if (parentId) {
                              // 상위 카테고리의 URL 별칭 가져오기 (실제 구현 시 AJAX로 처리 필요)
                              const parentUrlAlias = $('#ct_pid option:selected').data('url-alias') || '';
                              if (parentUrlAlias) {
                                  urlAlias = parentUrlAlias + '/' + urlAlias;
                              }
                          }

                          urlAliasField.val(urlAlias);
                      }
                  });

                  // 초기화
                  formHandler.init();
                  fileHandler.init();
              });

              // 파일 삭제 함수
              function deleteFile(fileNum) {
                  if (confirm('파일을 삭제하시겠습니까?')) {
                      $(`#file${fileNum}_delete`).val('Y');
                      $(`#file-label${fileNum}`).text('파일 선택');
                      $(`#input_group${fileNum}`).removeClass('input-group');
                      $(`.input-group-prepend span[onclick="deleteFile(${fileNum})"]`).hide();
                      $(`.input-group-prepend span a[href]`).parent().parent().hide();
                  }
              }
          </script>

        </div>
      </div>

    </div>
  </div>
  <!-- //END PAGE CONTENT CONTAINER -->
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/mng/foot.inc.php";
?>