<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu='10';
$chk_sub_menu='1';
include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.menu.inc.php";
//include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.sidebar.inc.php";

?>
  <!-- PAGE CONTENT CONTAINER -->
  <div class="content" id="content">
    <!-- PAGE HEADING -->
    <?php include_once "./pheading2.php";?>
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
              <form method="POST" name="frm_search" id="frm_search" action="<?=$_SERVER['PHP_SELF']?>" onsubmit="return frm_search_chk(this, event);" class="d-flex justify-content-end align-items-center">
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
                        if(f.search_txt.value=="") {
                            alert("검색어를 입력바랍니다.");
                            f.search_txt.focus();
                            return false;
                        }

                        return true;
                    }

                    <? if($_POST['sel_search']) { ?>$('#sel_search').val('<?=$_POST['sel_search']?>');<? } ?>
                    //-->
                </script>

                <select class="form-control searchByStatus" name="sel_search" id="sel_search" tabindex="-1" aria-hidden="true">
                  <option value="all">통합검색</option>
                  <option value="a1.title">제목</option>
                  <option value="a1.body">내용</option>
                </select>
                <input type="text" class="form-control searchByText" name="search_txt" id="search_txt" value="<?=$_POST['search_txt']?>"  placeholder="검색어를 입력바랍니다.">
                <button type="submit" class="btn btn-secondary margin-right-5">검색</button>
                <button type="button" class="btn btn-gray" onclick="location.href='./notifications.php'">초기화</button>
              </form>
            </div>
          </div>


          <!-- 게시물 리스트-->
          <form name="frm_list" id="frm_list" onsubmit="return false;">
            <input type="hidden" name="act" id="act" value="notifications" />
            <input type="hidden" name="obj_list" id="obj_list" value="notice_list_box" />
            <input type="hidden" name="obj_frm" id="obj_frm" value="frm_list" />
            <input type="hidden" name="obj_uri" id="obj_uri" value="./update.php" />
            <input type="hidden" name="obj_pg" id="obj_pg" value="1" />
            <input type="hidden" name="obj_limit_num" id="obj_limit_num" value="10" />
            <input type="hidden" name="obj_orderby" id="obj_orderby" value="" />
            <input type="hidden" name="obj_order_desc_asc" id="obj_order_desc_asc" value="1" />
            <input type="hidden" name="obj_sel_search" id="obj_sel_search" value="<?=$_POST['sel_search']?>" />
            <input type="hidden" name="obj_search_txt" id="obj_search_txt" value="<?=$_POST['search_txt']?>" />
          </form>
          <div id="notice_list_box"></div>

          <!-- 상태 메시지 표시 -->
          <div id="statusMessage" class="alert alert-success" style="display:none; position:fixed; bottom:20px; right:50px;"></div>

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


              <?php if ($_POST['sel_mt_seller']) { ?>
              $('#sel_mt_seller').val('<?=$_POST['sel_mt_seller']?>');
              <?php } ?>


              function f_post_resend(idx) {
                  $.confirm({
                      title: 'FCM 재전송',
                      content: "재전송 하시겠습니까?",
                      buttons: {
                          cancel: {
                              text: "취소",
                              btnClass: "btn-outline-light",
                          },
                          confirm: {
                              text: "확인",
                              btnClass: "btn-primary",
                              action: function () {
                                  $.ajax({
                                      url: './update.php',
                                      type: 'POST',
                                      data: {
                                          act: 'resend',
                                          idx: idx
                                      },
                                      dataType: 'json',
                                      success: function(data) {
                                          console.log(data);
                                          if (data.success) {
                                              app.toastr.showSuccess(data.message, data.redirect);
                                          } else {
                                              app.toastr.showError(data.message);
                                          }
                                      },
                                      error: function(xhr, status, error) {
                                          console.error('AJAX Error:', status, error);
                                          app.toastr.showError('서버 통신 중 오류가 발생했습니다.');
                                      }
                                  });
                              },
                          },
                      },
                  });

                  return false;
              }

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


              // 사용 예시
              $(document).ready(function() {
                  // Toastr 초기화
                  initToastr();


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