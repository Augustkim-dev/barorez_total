<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu='9';
$chk_sub_menu='3';
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

                        <div class="col-12">
                            <form method="POST" name="frm_search" id="frm_search" action="<?=$_SERVER['PHP_SELF']?>" onsubmit="return frm_search_chk(this, event);" class="d-flex justify-content-between align-items-center">
                                <div class="d-flex">
                                <button type="button" class="btn btn-outline-secondary mr-1">오늘</button>
                                <button type="button" class="btn btn-outline-secondary mr-1">7일</button>
                                <button type="button" class="btn btn-outline-secondary mr-1">30일</button>
                                <input type="text" name="rt_start" id="rt_start" value="<?= $row['rt_start'] ?>"
                                       class="form-control" readonly/>
                                <span class="m-1">~</span>
                                <input type="text" name="rt_end"
                                       id="rt_end"
                                       value="<?= $row['rt_end'] ?>"
                                       class="form-control mr-1"
                                       readonly/>
                                <select class="form-control searchByStatus" name="sel_search" id="sel_search" tabindex="-1" aria-hidden="true">
                                    <option value="all">통합검색</option>
                                    <option value="a1.nt_title">제목</option>
                                    <option value="a1.nt_content">내용</option>

                                </select>
                                <input type="text" class="form-control searchByText" name="search_txt" id="search_txt" value="<?=$_POST['search_txt']?>"  placeholder="검색어를 입력바랍니다.">
                                <select class="form-control searchByStatus" name="sel_search" id="sel_search" tabindex="-1" aria-hidden="true">
                                    <option value="all">구분</option>
                                    <option value="a1.nt_title">자동차</option>
                                    <option value="a1.nt_content">명품</option>

                                </select>
                                </div>
                                <div class="d-flex">
                                <button type="submit" class="btn btn-secondary margin-right-5">검색</button>
                                <!--                                <button type="button" class="btn btn-info margin-right-5" onclick="location.href='./form.php'">신규등록</button>-->
                                <button type="button" class="btn btn-gray" onclick="location.href='./list.php'">초기화</button>
                                </div>
                                <script type="text/javascript">
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

                                    $('#rt_start').datetimepicker({
                                        format: 'Y-m-d',
                                        onShow: function (ct) {
                                            this.setOptions({
                                                maxDate: $('#rt_end').val() ? $('#rt_end').val() : false
                                            })
                                        },
                                        timepicker: false
                                    });
                                    $('#rt_end').datetimepicker({
                                        format: 'Y-m-d',
                                        onShow: function (ct) {
                                            this.setOptions({
                                                minDate: $('#rt_start').val() ? $('#rt_start').val() : false
                                            })
                                        },
                                        timepicker: false
                                    });

                                    <? if($_POST['sel_search']) { ?>$('#sel_search').val('<?=$_POST['sel_search']?>');<? } ?>
                                </script>
                            </form>
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

                    </script>


                  <script>
                      // 순서 저장
                      function saveSequence(currentPage, itemsPerPage) {
                          const table = document.getElementById('listTable');
                          if (!table) return;

                          const tbody = table.querySelector('tbody');
                          const rows = Array.from(tbody.querySelectorAll('tr[data-id]'));
                          // 내림차순으로 계산
                          const totalItems = parseInt(document.getElementById('total').value);
                          const sequenceData = rows.map((row, index) => ({
                              idx: parseInt(row.dataset.id),
                              // sequence: (currentPage - 1) * itemsPerPage + index + 1
                              sequence: totalItems - ((currentPage - 1) * itemsPerPage + index)
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
                                  rt_show: isChecked ? 'Y' : 'N'
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
                              '.switch input[name="nt_show"]',  // 셀렉터
                              'id',                             // data 속성 이름
                              './update.php'    // 업데이트 URL
                          );
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
