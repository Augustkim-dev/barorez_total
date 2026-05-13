<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu='7';
$chk_sub_menu='1';
include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.menu.inc.php";
//include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.sidebar.inc.php";

$search_day = $_POST['search_day'] ?? 1;
$search_status = $_POST['search_status'] ?? '';
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
                            <form method="POST" name="frm_search" id="frm_search" action="<?=$_SERVER['PHP_SELF']?>" onsubmit="return frm_search_chk(this, event);" class="row justify-content-between">
                                <div class="d-flex justify-content-between align-items-center col-12">
                                    <div class="d-flex justify-content-start align-items-center">
                                        <label for="date" class="col-form-label  mr-2" style="min-width: 60px">등록일</label>
                                        <?php foreach($arr_day as $key=>$value) {?>
                                            <?php
                                            $local_class_name = ($search_day == $key) ? 'btn-secondary' : 'btn-outline-secondary';
                                            ?>
                                            <button type="button" class="btn local2-search-btn mr-2 <?php echo $local_class_name?>" data-day="<?php echo $key?>" ><?php echo $value ?></button>
                                        <?php }?>
                                        <input type="hidden" name="search_day" id="search_day" value="<?=$search_day?>" />
                                        <input type="date" class="col-sm-3 form-control" name="sdate" id="sdate" value="<?=$_POST['sdate']?>" />
                                        <label for="edate" class="col-sm-1 text-center">~</label>
                                        <input type="date" class="col-sm-3 form-control" name="edate" id="edate" value="<?=$_POST['edate']?>" />
                                    </div>
                                    <div class="d-flex justify-content-start align-items-center">
                                        <label for="sel_search" class="col-form-label mr-2" style="min-width: 60px">검색어</label>
                                        <select class="form-control searchByStatus" name="sel_search" id="sel_search" tabindex="-1" aria-hidden="true">
                                            <option value="all">통합검색</option>
                                            <option value="name">문의 이름&아이디</option>
                                            <option value="rt_name">답변 이름</option>
                                            <option value="title">제목</option>
                                        </select>
                                        <input type="text" class="form-control searchByText" name="search_txt" id="search_txt" value="<?=$_POST['search_txt']?>"  placeholder="검색어를 입력바랍니다.">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center col-12">
                                    <div class="d-flex justify-content-start align-items-center mt-2">
                                        <label for="search_singo" class="col-form-label mr-2" style="min-width: 60px"">답변상태</label>

                                        <?php
                                        $local_total_class_name = 'btn-secondary';
                                        if ($search_status !== '') {
                                            $local_total_class_name = 'btn-outline-secondary';
                                        }
                                        ?>
                                        <button type="button" data-local="" class="margin-right-5 local-search-btn btn <?php echo $local_total_class_name?>">
                                            전체
                                        </button>


                                        <?php foreach($arr_qa as $key=>$value) {?>
                                            <?php
                                            $local_class_name = ($search_status == $key) ? 'btn-secondary' : 'btn-outline-secondary';
                                            ?>
                                            <button type="button" data-local="<?php echo $key?>" class="margin-right-5 local-search-btn btn <?php echo $local_class_name?>">
                                                <?php echo $value?>
                                            </button>
                                        <?php }?>
                                        <input type="hidden" name="search_status" id="search_status" value="<?=$search_status?>" />

                                    </div>

                                    <div class="d-flex justify-content-start align-items-center mt-2 ">
                                        <button type="submit" class="btn btn-secondary margin-right-5">검색</button>
                                        <button type="button" class="btn btn-gray" onclick="location.href='./list.php'">초기화</button>
                                    </div>
                                </div>
                                <script type="text/javascript">
                                    document.addEventListener("DOMContentLoaded", function () {
                                        $(".searchByStatus").select2({
                                            placeholder: "통합검색",
                                            minimumResultsForSearch: -1,
                                            width: '120px',
                                        });
                                        $(".search_singo").select2({
                                            placeholder: "선택하세요",
                                            minimumResultsForSearch: -1,
                                        });
                                    });

                                    // ✅ 검색 버튼 눌렀을 때만: 날짜 범위 + 검색어 적용
                                    function frm_search_chk(f, e) {
                                        if (e && e.preventDefault) {
                                            e.preventDefault();
                                        }

                                        // 검색 조건 → 리스트 hidden으로 복사
                                        $('#obj_sel_search').val($('#sel_search').val());
                                        $('#obj_search_txt').val($('#search_txt').val());

                                        // 날짜 범위도 복사 (등록일 빠른 버튼과는 별개로, 직접 선택한 값)
                                        var sdate = $('form#frm_search #sdate').val();
                                        var edate = $('form#frm_search #edate').val();
                                        $('#obj_sdate').val(sdate);
                                        $('#obj_edate').val(edate);

                                        // 페이지 1로 초기화
                                        $('#obj_pg').val(1);

                                        if (typeof f_get_box_mng_list === 'function') {
                                            f_get_box_mng_list();
                                        }

                                        return false;
                                    }

                                    $(document).ready(function() {
                                        // ✅ 답변상태 버튼: 클릭 시 바로 적용
                                        $('.local-search-btn').on('click', function(){
                                            let local = $(this).attr('data-local') || '';

                                            // 검색폼 hidden
                                            $('#search_status').val(local);
                                            // 리스트 hidden
                                            $('#obj_search_status').val(local);

                                            // 버튼 스타일
                                            $('.local-search-btn')
                                                .removeClass('btn-secondary')
                                                .addClass('btn-outline-secondary');
                                            $(this)
                                                .removeClass('btn-outline-secondary')
                                                .addClass('btn-secondary');

                                            // 페이지 1로
                                            $('#obj_pg').val(1);

                                            // 리스트 즉시 갱신
                                            if (typeof f_get_box_mng_list === 'function') {
                                                f_get_box_mng_list();
                                            }
                                        });

                                        // ✅ 등록일 빠른 버튼(오늘/7일/30일…): 클릭 시 바로 적용
                                        $('.local2-search-btn').on('click', function(){
                                            let local = $(this).attr('data-day') || '';

                                            // 검색폼 hidden
                                            $('#search_day').val(local);
                                            // 리스트 hidden
                                            $('#obj_search_day').val(local);

                                            // 날짜 직접 선택 필드는 초기화 (빠른 버튼이 우선)
                                            $('form#frm_search #sdate').val('');
                                            $('form#frm_search #edate').val('');
                                            $('#obj_sdate').val('');
                                            $('#obj_edate').val('');

                                            // 버튼 스타일
                                            $('.local2-search-btn')
                                                .removeClass('btn-secondary')
                                                .addClass('btn-outline-secondary');
                                            $(this)
                                                .removeClass('btn-outline-secondary')
                                                .addClass('btn-secondary');

                                            // 페이지 1로
                                            $('#obj_pg').val(1);

                                            // 리스트 즉시 갱신
                                            if (typeof f_get_box_mng_list === 'function') {
                                                f_get_box_mng_list();
                                            }
                                        });

                                        // ✅ 날짜 직접 선택 시: 등록일 빠른 버튼 해제만 하고, 실제 적용은 검색 버튼 눌렀을 때
                                        $('#sdate, #edate').on('focus click', function (){
                                            // 등록일 빠른 버튼 초기화
                                            $('#search_day').val('');
                                            $('#obj_search_day').val('');

                                            $('.local2-search-btn')
                                                .removeClass('btn-secondary')
                                                .addClass('btn-outline-secondary');
                                        });
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
                        <input type="hidden" name="obj_search_status" id="obj_search_status" value="<?=$search_status?>" />
                        <input type="hidden" name="obj_search_day" id="obj_search_day" value="<?=$search_day?>" />
                        <!-- ✅ id만 변경 -->
                        <input type="hidden" name="sdate" id="obj_sdate" value="<?=$_POST['sdate']?>" />
                        <input type="hidden" name="edate" id="obj_edate" value="<?=$_POST['edate']?>" />
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
                              '.switch input[name="rt_show"]',  // 셀렉터
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
