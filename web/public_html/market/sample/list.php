<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu='00';
$chk_sub_menu='1';
include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.menu.inc.php";
//include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.sidebar.inc.php";
if($_SESSION['mng']['mt_level'] < 10) {
    header("Location: ./"); // 이동할 페이지 경로
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
                    <!-- 게시물 리스트-->
                    <form name="frm_list" id="frm_list" onsubmit="return false;">
                        <input type="hidden" name="act" id="act" value="list" />
                        <input type="hidden" name="obj_list" id="obj_list" value="notice_list_box" />
                        <input type="hidden" name="obj_frm" id="obj_frm" value="frm_list" />
                        <input type="hidden" name="obj_uri" id="obj_uri" value="./update.php" />
                        <input type="hidden" name="obj_pg" id="obj_pg" value="1" />
                        <input type="hidden" name="obj_limit_num" id="obj_limit_num" value="50" />
                        <input type="hidden" name="obj_orderby" id="obj_orderby" value="" />
                        <input type="hidden" name="obj_order_desc_asc" id="obj_order_desc_asc" value="1" />
                        <input type="hidden" name="obj_sel_search" id="obj_sel_search" value="<?=$_POST['sel_search']?>" />
                        <input type="hidden" name="obj_search_txt" id="obj_search_txt" value="<?=$_POST['search_txt']?>" />
                        <input type="hidden" name="obj_search_status" id="obj_search_status" value="<?=$_POST['search_status']?>" />
                        <input type="hidden" name="obj_search_day" id="obj_search_day" value="<?=$_POST['search_day']?>" />
                        <input type="hidden" name="sdate" id="sdate" value="<?=$_POST['sdate']?>" />
                        <input type="hidden" name="edate" id="edate" value="<?=$_POST['edate']?>" />
                        <div class="my-4">
                            <div class="d-flex">
                                <!-- 주소 -->
                                <div class="col-md-4">
                                    <label class="form-label">시/도</label>
                                    <select class="form-control" id="sido" name="province">
                                        <option value="">전체</option>
                                        <option>서울특별시</option>
                                        <option>부산광역시</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">시/군/구</label>
                                    <select class="form-control" id="sigungu" name="city" disabled>
                                        <option value="">전체</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">읍/면/동</label>
                                    <select class="form-control" id="eupmyeondong" name="district" disabled>
                                        <option value="">전체</option>
                                    </select>
                                </div>
                            </div>
                            <style>
                                .segmented-control {
                                    border: 1px solid #ced4da;
                                    border-radius: .25rem;
                                    overflow: hidden;
                                    width: 100%;
                                    display: flex;
                                    justify-content: space-between;
                                }
                                .segmented-control input {
                                    display: none;
                                }
                                .segmented-control label {
                                    width: 50%;
                                    padding: 9px 0;
                                    cursor: pointer;
                                    margin-bottom: 0;
                                    text-align: center;
                                    background: #fff;
                                    border-right: 1px solid #ced4da;
                                }
                                .segmented-control input:checked + label {
                                    background: #333;
                                    color: #fff;
                                }
                                .segmented-control label:last-child {
                                    border-right: none;
                                }
                            </style>

                            <div class="d-flex mt-4">
                                <div class="col-md-4">
                                    <label class="form-label d-block">주소 타입</label>
                                    <div class="segmented-control">
                                        <input type="radio" name="addr_type" value="road" id="road" checked>
                                        <label for="road">도로명주소</label>

                                        <input type="radio" name="addr_type" value="jibeon" id="jibeon">
                                        <label for="jibeon">지번주소</label>
                                    </div>
<!--                                    <label class="form-label d-block">주소 타입</label>-->
<!--                                    <div class="form-check form-check-inline">-->
<!--                                        <input class="form-check-input" type="radio" name="addr_type" id="road" value="road" checked>-->
<!--                                        <label class="form-check-label" for="road">도로명주소</label>-->
<!--                                    </div>-->
<!--                                    <div class="form-check form-check-inline">-->
<!--                                        <input class="form-check-input" type="radio" name="addr_type" id="jibeon" value="jibeon">-->
<!--                                        <label class="form-check-label" for="jibeon">지번주소</label>-->
<!--                                    </div>-->
                                </div>

                                <!-- 도로명주소 입력 -->
                                <div class="col-md-8 addr-input addr-road">
                                    <label class="form-label">도로명주소</label>
                                    <input type="text" class="form-control" name="addr_road" placeholder="도로명주소를 입력하세요">
                                </div>

                                <!-- 지번주소 입력 -->
                                <div class="col-md-8 addr-input addr-jibeon d-none">
                                    <label class="form-label">지번주소</label>
                                    <input type="text" class="form-control" name="addr_jibeon" placeholder="지번주소를 입력하세요">
                                </div>
                            </div>

                            <div class="d-flex mt-4">
                                <!-- 사업장명 -->
                                <div class="col-md-4">
                                    <label class="form-label">사업장명</label>
                                    <input type="text" class="form-control" name="business_name" placeholder="사업장명을 입력하세요">
                                </div>

                                <!-- 인허가일자 -->
                                <div class="col-md-8">
                                    <label class="form-label">인허가일자</label>
                                    <div class="d-flex align-items-center">
                                        <input type="date" class="form-control" name="date_start">
                                        <span class="mx-2">~</span>
                                        <input type="date" class="form-control" name="date_end">
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex mt-4">
                                <!-- 건물용도 -->
                                <div class="col-md-4">
                                    <label class="form-label">건물용도</label>
                                    <select class="form-control" name="building_use" id="building_use">
                                        <option value="">전체</option>
                                    </select>
                                </div>

                                <!-- 객실수 -->
                                <div class="col-md-8">
                                    <label class="form-label">객실수</label>
                                    <div class="d-flex align-items-center">
                                        <input type="number" class="form-control" name="room_min" placeholder="최소">
                                        <span class="mx-2">~</span>
                                        <input type="number" class="form-control" name="room_max" placeholder="최대">
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex mt-4">
                                <!-- 시설면적 -->
                                <div class="col-md-4">
                                    <div class="d-flex justify-content-between">
                                        <label class="form-label">시설면적(㎡)</label>
                                        <div class="mt-1 text-muted" id="area_display">
                                            평수: <span id="pyeong_min">0</span> ~ <span id="pyeong_max">0</span> 평
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center align-items-center">
                                        <input type="number" class="form-control" id="area_min" name="area_min" placeholder="최소㎡">
                                        <span class="mx-2">~</span>
                                        <input type="number" class="form-control" id="area_max" name="area_max" placeholder="최대㎡">
                                    </div>
                                </div>

                                <!-- 층수 -->
                                <div class="col-md-4">
                                    <label class="form-label">지상층수</label>
                                    <div class="d-flex align-items-center">
                                        <input type="number" class="form-control" name="floor_above_min" placeholder="최소">
                                        <span class="mx-2">~</span>
                                        <input type="number" class="form-control" name="floor_above_max" placeholder="최대">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">지하층수</label>
                                    <div class="d-flex align-items-center">
                                        <input type="number" class="form-control" name="floor_below_min" placeholder="최소">
                                        <span class="mx-2">~</span>
                                        <input type="number" class="form-control" name="floor_below_max" placeholder="최대">
                                    </div>
                                </div>
                            </div>

                            <!-- 용도지구 -->
                            <div class="col-md-12 mt-4">
                                <label class="form-label">용도지구</label>
                                <select class="form-control" name="zoning_district[]" multiple id="zoning_district">

                                </select>
                            </div>

                            <!-- 검색 버튼 -->
                            <div class="col-12 text-center mt-4">
                                <button type="submit" class="btn btn-primary">검색</button>
                                <button type="reset" class="btn btn-secondary">초기화</button>
                            </div>
                            <script>
                                $(document).ready(function() {
                                    // 검색 버튼 클릭 이벤트
                                    $('form#frm_list').on('reset', function(e) {
                                        setTimeout(function() { // reset 동작 후 실행
                                            $('#obj_pg').val(1);
                                            f_get_box_mng_list(1, '');
                                        }, 0);
                                    });

                                    // 검색 버튼 클릭 이벤트
                                    $('form#frm_list').on('submit', function(e) {
                                        e.preventDefault();
                                        $('#obj_pg').val(1); // 검색 시 항상 첫 페이지로
                                        f_get_box_mng_list(1, '');
                                    });

                                    // 리스트 불러오기 함수
                                    function f_get_box_mng_list(pg, extraParams) {
                                        $('#obj_pg').val(pg);

                                        const formData = $('#frm_list').serialize();

                                        $.ajax({
                                            url: './update.php',
                                            type: 'POST',
                                            data: formData,
                                            dataType: 'html',
                                            success: function(response) {
                                                $('#notice_list_box').html(response);
                                            },
                                            error: function(xhr, status, error) {
                                                console.error('Error:', error);
                                                alert('리스트를 불러오는 중 오류가 발생했습니다.');
                                            }
                                        });
                                    }

                                    // 페이지 로드시 리스트 호출
                                    f_get_box_mng_list($('#obj_pg').val(), '');

                                    // 시설면적 입력 시 평수 자동 계산
                                    function updatePyeong() {
                                        const min = parseFloat($('#area_min').val()) || 0;
                                        const max = parseFloat($('#area_max').val()) || 0;
                                        $('#pyeong_min').text((min / 3.3058).toFixed(1));
                                        $('#pyeong_max').text((max / 3.3058).toFixed(1));
                                    }
                                    $('#area_min, #area_max').on('input', updatePyeong);

                                    // 주소 타입 토글
                                    $('input[name="addr_type"]').on('change', function() {
                                        const type = $(this).val();
                                        $('.addr-input').addClass('d-none');
                                        if (type === 'road') {
                                            $('.addr-road').removeClass('d-none');
                                        } else {
                                            $('.addr-jibeon').removeClass('d-none');
                                        }
                                    });

                                    // 주소 선택 기능
                                    function loadAddress(depth, parent, target, nextTarget) {
                                        $.ajax({
                                            url: './address.php',
                                            method: 'GET',
                                            data: { depth: depth, parent: parent },
                                            success: function(response) {
                                                let $target = $(target);
                                                $target.empty().append('<option value="">선택</option>');

                                                if (response.success && response.items.length > 0) {
                                                    response.items.forEach(function(item) {
                                                        $target.append('<option value="'+item+'">'+item+'</option>');
                                                    });
                                                    $target.prop('disabled', false);
                                                } else {
                                                    $target.prop('disabled', true);
                                                }

                                                if (nextTarget) {
                                                    $(nextTarget).empty().append('<option value="">선택</option>')
                                                        .prop('disabled', true);
                                                }
                                            }
                                        });
                                    }


                                        $.ajax({
                                            url: './building_use.php',
                                            method: 'GET',
                                            dataType: 'json',
                                            success: function(response) {
                                                if (response.success) {
                                                    const $select = $('#building_use');
                                                    $select.empty().append('<option value="">건물용도 선택</option>');
                                                    response.items.forEach(function(use) {
                                                        $select.append('<option value="'+use+'">'+use+'</option>');
                                                    });
                                                } else {
                                                    alert('데이터를 불러올 수 없습니다: ' + response.message);
                                                }
                                            },
                                            error: function() {
                                                alert('서버와의 통신 중 오류가 발생했습니다.');
                                            }
                                        });

                                        $.ajax({
                                            url: './zoning_district.php',
                                            method: 'GET',
                                            dataType: 'json',
                                            success: function(response) {
                                                if (response.success) {
                                                    const $select = $('#zoning_district');
                                                    $select.empty().append('<option value="">용도지구 선택</option>');
                                                    response.items.forEach(function(zone) {
                                                        $select.append('<option value="'+zone+'">'+zone+'</option>');
                                                    });
                                                } else {
                                                    alert('데이터를 불러올 수 없습니다: ' + response.message);
                                                }
                                            },
                                            error: function() {
                                                alert('서버와의 통신 중 오류가 발생했습니다.');
                                            }
                                        });
                                    // 시/도 로드
                                    loadAddress(0, null, '#sido', '#sigungu');

                                    // 시/도 선택 시 시/군/구 로드
                                    $('#sido').on('change', function() {
                                        const parent = $(this).val();
                                        if (parent) {
                                            loadAddress(1, parent, '#sigungu', '#eupmyeondong');
                                        } else {
                                            $('#sigungu').prop('disabled', true).empty().append('<option value="">시/군/구 선택</option>');
                                            $('#eupmyeondong').prop('disabled', true).empty().append('<option value="">읍/면/동 선택</option>');
                                        }
                                    });

                                    // 시/군/구 선택 시 읍/면/동 로드
                                    $('#sigungu').on('change', function() {
                                        const parent = $('#sido').val() + ' ' + $(this).val();
                                        if (parent) {
                                            loadAddress(2, parent, '#eupmyeondong');
                                        } else {
                                            $('#eupmyeondong').prop('disabled', true).empty().append('<option value="">읍/면/동 선택</option>');
                                        }
                                    });

                                });
                            </script>
                        </div>
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
