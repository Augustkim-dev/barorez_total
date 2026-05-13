<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu='13';
$chk_sub_menu='1';
include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.menu.inc.php";
//include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.sidebar.inc.php";

?>
    <!-- PAGE CONTENT CONTAINER -->
    <div class="content" id="content">
        <!-- PAGE HEADING -->
        <?php include_once "./pheading.php";?>
        <!-- //END PAGE HEADING -->
        <div class="container-fluid">
            <div class="card margin-bottom-0">
                <div class="card-body">
                    <!-- 순서 변경 버튼과 검색 폼을 포함하는 컨테이너 -->
                    <div class="form-row">
                        <div class="col-6 col-lg-3">

                            <?
                            if (!$_POST['sel_search']) {
                              ?>
                              <div class="dt-buttons2 btn-group">
                                <button class="btn btn-light buttons-double-up buttons-html5"
                                        tabindex="0"
                                        aria-controls="dt-example-buttons"
                                        onclick="moveToTop()"
                                        id="moveTopBtn"
                                        data-toggle="tooltip"
                                        data-placement="top"
                                        title="맨 위로">
                                  <span class="fa fa-angle-double-up"></span>
                                </button>
                                <button class="btn btn-light buttons-up buttons-html5"
                                        tabindex="0"
                                        aria-controls="dt-example-buttons"
                                        onclick="moveUp()"
                                        id="moveUpBtn"
                                        data-toggle="tooltip"
                                        data-placement="top"
                                        title="위로">
                                  <span class="fa fa-angle-up"></span>
                                </button>
                                <button class="btn btn-light buttons-down buttons-html5"
                                        tabindex="0"
                                        aria-controls="dt-example-buttons"
                                        onclick="moveDown()"
                                        id="moveDownBtn"
                                        data-toggle="tooltip"
                                        data-placement="top"
                                        title="아래로">
                                  <span class="fa fa-angle-down"></span>
                                </button>
                                <button class="btn btn-light buttons-double-down buttons-html5"
                                        tabindex="0"
                                        aria-controls="dt-example-buttons"
                                        onclick="moveToBottom()"
                                        id="moveBottomBtn"
                                        data-toggle="tooltip"
                                        data-placement="top"
                                        title="맨 아래로">
                                  <span class="fa fa-angle-double-down"></span>
                                </button>
                                <button class="btn btn-light buttons-save buttons-html5"
                                        type="button"
                                        tabindex="0"
                                        aria-controls="dt-example-buttons"
                                        onclick="saveSequence(document.getElementById('obj_pg').value, document.getElementById('obj_limit_num').value)"
                                        id="saveOrderBtn"
                                        data-toggle="tooltip"
                                        data-placement="top"
                                        title="순서 저장">
                                  <span class="fa fa-save"></span>
                                </button>
                              </div>
                              <script>
                                  $(document).ready(function(){
                                      $('[data-toggle="tooltip"]').tooltip();
                                  });
                              </script>
                            <? } ?>

                        </div>

                        <div class="col-6 col-lg-9">
                            <form method="post" name="frm_search" id="frm_search" action="<?=$_SERVER['PHP_SELF']?>" onsubmit="return frm_search_chk(this, event);" class="d-flex justify-content-end align-items-center">
                                <script type="text/javascript">
                                    <!--
                                    document.addEventListener("DOMContentLoaded", function () {
                                        $(".searchByStatus").select2({
                                            placeholder: "통합검색",
                                            minimumResultsForSearch: -1,
                                            width: '120px',
                                        });
                                    });

                                    function frm_search_chk(f) {
                                        // if(f.search_txt.value=="") {
                                        //     alert("검색어를 입력바랍니다.");
                                        //     f.search_txt.focus();
                                        //     return false;
                                        // }

                                        return true;
                                    }
                                    $(document).ready(function() {
                                      $('.local-search-btn').on('click', function(e){
                                          let local = $(this).attr('data-local')

                                          console.log(local)
                                          $('#search_local').val(local);
                                          $('#frm_search').submit();
                                      })
                                    })

                                    <? if($_POST['sel_search']) { ?>$('#sel_search').val('<?=$_POST['sel_search']?>');<? } ?>
                                    //-->
                                </script>
                                 <input type="hidden" id="search_local" name="search_local" value="<?=$_POST['search_local']?>" />
                                <select class="form-control searchByStatus" name="sel_search" id="sel_search" tabindex="-1" aria-hidden="true">
                                    <option value="a1.gmt_golf_name">골프장명</option>
                                </select>
                                <input type="text" class="form-control searchByText" name="search_txt" id="search_txt" value="<?=$_POST['search_txt']?>"  placeholder="검색어를 입력바랍니다.">
                                <button type="submit" class="btn btn-secondary margin-right-5">검색</button>
                                <button type="button" class="btn btn-info margin-right-5" onclick="location.href='./form.php'">신규등록</button>
                                <button type="button" class="btn btn-gray" onclick="location.href='./list.php'">초기화</button>
                            </form>
                        </div>


                        <div class="col-8 mt-2">
                          <?php
                          $local_total_class_name = 'btn-secondary';
                          if ($_POST['search_local']) {
                            $local_total_class_name = 'btn-outline-secondary';
                          }
                          ?>
                          <button type="button" data-local="" class="local-search-btn btn <?php echo $local_total_class_name?>">
                            전체
                          </button>
                          <?php foreach($arr_gmt_local_type as $key=>$local) {?>
                              <?php
                                $local_class_name = 'btn-outline-secondary';
                                if ($_POST['search_local']==$key) {
                                  $local_class_name = 'btn-secondary';
                                }
                              ?>
                            <button type="button" data-local="<?php echo $key?>" class="local-search-btn btn <?php echo $local_class_name?>">
                              <?php echo $local?>
                            </button>
                          <?php }?>

                        </div>
                      
                        <div class="col-4 mt-2">
                          <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-outline-success margin-right-5" onclick="f_excel_up();">엑셀업로드</button>
                            <button type="button" class="btn btn-success" onclick="f_excel_down();">엑셀다운로드</button>

                          </div>

                        </div>
                    </div>


                    <!-- 게시물 리스트-->
                    <form name="frm_list" id="frm_list" onsubmit="return false;">
                        <input type="hidden" name="act" id="act" value="list" />
                        <input type="hidden" name="obj_list" id="obj_list" value="notice_list_box" />
                        <input type="hidden" name="obj_frm" id="obj_frm" value="frm_list" />
                        <input type="hidden" name="obj_uri" id="obj_uri" value="./update.php" />
                        <input type="hidden" name="obj_pg" id="obj_pg" value="1" />
                        <input type="hidden" name="obj_limit_num" id="obj_limit_num" value="10" />
                        <input type="hidden" name="obj_orderby" id="obj_orderby" value="" />
                        <input type="hidden" name="obj_order_desc_asc" id="obj_order_desc_asc" value="1" />
                        <input type="hidden" name="obj_sel_search" id="obj_sel_search" value="<?=$_POST['sel_search']?>" />
                        <input type="hidden" name="obj_search_txt" id="obj_search_txt" value="<?=$_POST['search_txt']?>" />
                        <input type="hidden" name="obj_search_local" id="obj_search_local" value="<?=$_POST['search_local']?>" />
                    </form>
                    <div id="notice_list_box"></div>

                    <!-- 상태 메시지 표시 -->
                    <div id="statusMessage" class="alert alert-success" style="display:none; position:fixed; bottom:20px; right:50px;"></div>



                  <!-- 엑셀 업로드 모달 -->
                  <div class="modal fade" id="excelUploadModal" tabindex="-1" role="dialog" aria-labelledby="excelUploadModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                      <div class="modal-content">

                        <div class="modal-header">
                          <h5 class="modal-title" id="excelUploadModalLabel">엑셀 업로드</h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="닫기">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>

                        <div class="modal-body">
                          <div class="mb-3">
                            <a href="./sample/membership.xlsx?ver=<?php echo $v_txt?>" class="btn btn-outline-secondary" download>샘플양식 다운로드</a>
                          </div>

                          <div class="form-group">
                            <label for="excel_file">엑셀 파일 선택</label>
                            <input type="file" class="form-control" id="excel_file" name="excel_file" accept=".xls,.xlsx">
                          </div>
                        </div>

                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-dismiss="modal">닫기</button>
                          <button type="button" class="btn btn-primary" onclick="upload_excel_file()">확인</button>
                        </div>

                      </div>
                    </div>
                  </div>



                  <script>
                        $(document).ready(function() {
                            //f_get_box_mng_list();
                        });
                        $(document).ready(function () {
                            var history_data = history.state;
                            if(history_data) {
                                f_get_box_mng_list(history_data.page, '');
                            } else {
                                f_get_box_mng_list('<?=$_GET['pg'] ? $_GET['pg'] : 1?>', '');
                            }


                        });

                        <?php if ($_POST['sel_search']) { ?>
                        $('#sel_search').val('<?=$_POST['sel_search']?>');
                        <?php } ?>



                    </script>


                  <script>
                      // 순서 저장
                      function saveSequence(currentPage, itemsPerPage) {
                          const table = document.getElementById('listTable');
                          if (!table) return;

                          const tbody = table.querySelector('tbody');
                          const rows = Array.from(tbody.querySelectorAll('tr[data-id]'));
                          const sequenceData = rows.map((row, index) => ({
                              idx: parseInt(row.dataset.id),
                              sequence: (currentPage - 1) * itemsPerPage + index + 1
                          }));


                          console.log('전송할 데이터:', sequenceData); // 전송 데이터 확인

                          $.ajax({
                              url: './update.php',
                              method: 'POST',
                              data: {
                                  act: 'updateSequence',
                                  data: sequenceData
                              },
                              dataType: 'json',
                              success: function(response) {
                                  if(response.success) {
                                      window.location.reload();
                                  } else {
                                      alert(response.message || '순서 저장에 실패했습니다.');
                                  }
                              },
                              error: function(xhr, status, error) {
                                  // 에러 상세 정보 출력
                                  console.error('Status:', status);
                                  console.error('Error:', error);
                                  console.error('Response:', xhr.responseText);

                                  try {
                                      const errorResponse = JSON.parse(xhr.responseText);
                                      alert(errorResponse.message || '오류가 발생했습니다. 관리자에게 문의해주세요.');
                                  } catch(e) {
                                      alert('서버 응답 처리 중 오류가 발생했습니다: ' + xhr.responseText);
                                  }
                              }
                          });
                      }

                      // Toastr 초기화 함수
                      function initToastr() {
                          toastr.options = {
                              "closeButton": true,
                              "progressBar": true,
                              "positionClass": "toast-bottom-right",
                              "timeOut": "3000",
                              "extendedTimeOut": "1000",
                              "preventDuplicates": true,
                              "showMethod": "fadeIn",
                              "hideMethod": "fadeOut",
                              "showDuration": "300",
                              "hideDuration": "300"
                          };
                      }

                      // 토글 상태 업데이트 함수
                      function updateToggleStatus(url, data, $element) {
                          $.ajax({
                              url: url,
                              type: 'POST',
                              data: data,
                              dataType: 'json',
                              success: function(response) {

                                  console.log(response)
                                  if(response.success) {
                                      toastr.success('노출 상태가 변경되었습니다.');
                                  } else {
                                      $element.prop('checked', !$element.is(':checked'));
                                      toastr.error(response.message || '처리 중 오류가 발생했습니다.');
                                  }
                              },
                              error: function() {
                                  $element.prop('checked', !$element.is(':checked'));
                                  toastr.error('서버 통신 오류가 발생했습니다.');
                              }
                          });
                      }

                      // 토글 이벤트 핸들러 설정 함수
                      function initToggleHandler(selector, dataIdAttribute, updateUrl) {
                          $(document).on('change', selector, function() {
                              var $this = $(this);
                              var itemId = $this.closest('tr').data(dataIdAttribute);

                              if(!itemId) {
                                  toastr.error('항목 정보를 찾을 수 없습니다.');
                                  $this.prop('checked', !$this.is(':checked'));
                                  return;
                              }

                              var isChecked = $this.is(':checked');
                              var data = {
                                  act: 'updateShow',
                                  [dataIdAttribute]: itemId,
                                  gmt_show: isChecked ? 'Y' : 'N'
                              };

                              updateToggleStatus(updateUrl, data, $this);
                          });
                      }



                      // 사용 예시
                      $(document).ready(function() {
                          // Toastr 초기화
                          initToastr();

                          // 카테고리 토글 이벤트 초기화
                          initToggleHandler(
                              '.switch input[name="gmt_show"]',  // 셀렉터
                              'id',                             // data 속성 이름
                              './update.php'    // 업데이트 URL
                          );


                      });

                      function f_excel_up() {
                          const modal = new bootstrap.Modal(document.getElementById('excelUploadModal'));
                          modal.show();
                      }

                      function upload_excel_file() {
                          const fileInput = document.getElementById('excel_file');
                          const file = fileInput.files[0];

                          if (!file) {
                              app.toastr.showError("엑셀 파일을 선택해주세요.");
                              return;
                          }

                          const formData = new FormData();
                          formData.append("act", "excel_upload");
                          formData.append("excel_file", file);

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
                                  console.log(response)
                                  if(response.success) {
                                      app.toastr.showSuccess(response.message, 'reload');
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


                      }

                      function f_excel_down() {
                          let p1 = $('#sel_search').val();
                          let p2 = $('#search_txt').val();
                          let p3 = $('#search_local').val();
                          let query = `p1=${encodeURIComponent(p1)}&p2=${encodeURIComponent(p2)}&p3=${encodeURIComponent(p3)}`;


                          // console.log(`./excel.php?${query}`)
                          hidden_ifrm.document.location.href = `./excel.php?${query}`;
                          // document.location.href = `./excel.php?${query}`;

                          return false;
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