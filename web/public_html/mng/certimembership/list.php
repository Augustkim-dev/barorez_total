<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu='14';
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


                        <div class="col-8">
                            <form method="post" name="frm_search" id="frm_search" action="<?=$_SERVER['PHP_SELF']?>" onsubmit="return frm_search_chk(this, event);" class="d-flex justify-content-start align-items-center">
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
                                    <option value="a2.gmt_golf_name">골프장</option>
                                </select>
                                <input type="text" class="form-control searchByText" name="search_txt" id="search_txt" value="<?=$_POST['search_txt']?>"  placeholder="검색어를 입력바랍니다.">
                                <button type="submit" class="btn btn-secondary margin-right-5">검색</button>
                                <button type="button" class="btn btn-info margin-right-5" onclick="location.href='./form.php'">신규등록</button>
                                <button type="button" class="btn btn-gray" onclick="location.href='./list.php'">초기화</button>
                            </form>
                        </div>


                        <div class="col-4">
                          <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-success margin-right-5" onclick="onTrueAuth();">승인</button>
                            <button type="button" class="btn btn-danger" onclick="onFalseAuth();">반려</button>
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


                      function getCheckedAuthItems() {
                          const checked = [];
                          document.querySelectorAll('.rowCheckbox:checked').forEach((el) => {
                              checked.push(el.value);
                          });
                          return checked;
                      }


                        function updateAuthStatus(status) {
                            const selected = getCheckedAuthItems();
                            if (selected.length === 0) {
                                const msg1 = status == 2 ? '승인할 항목을 선택해주세요.' : '반려할 항목을 선택해주세요.';
                                app.toastr.showError(msg1);
                                return;
                            }

                            const formData = new FormData();
                            formData.append('act', 'auth_status_change');
                            formData.append('gmat_status', status);
                            selected.forEach(id => formData.append('gmat_idx[]', id)); // name[] 형태로 추가

                            const msg = status === 2 ? '선택한 항목을 승인 하시겠습니까?' : '선택한 항목을 반려 하시겠습니까?';
                            $.confirm({
                                title: '회원권 승인/반려',
                                content: msg,
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
                                                dataType: 'json',
                                                data: formData,
                                                processData: false,
                                                contentType: false,
                                                beforeSend: () => $('#splinner_modal').modal('show'),
                                                success: (response) => {
                                                    $('#splinner_modal').modal('hide');
                                                    console.log(response)
                                                    if (response.success) {
                                                        app.toastr.showSuccess(response.message, 'reload');
                                                    } else {
                                                        app.toastr.showError(response.message);
                                                    }
                                                },
                                                error: (xhr) => {
                                                    $('#splinner_modal').modal('hide');
                                                    console.error(xhr.responseText);
                                                    app.toastr.showError(error);
                                                }
                                            });

                                        },
                                    },
                                },
                            });



                        }


                      function onTrueAuth() {
                          updateAuthStatus(2); // 승인
                      }

                      function onFalseAuth() {
                          updateAuthStatus(3); // 반려
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