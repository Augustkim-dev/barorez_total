<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu='11';
$chk_sub_menu='1';
include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.menu.inc.php";
//include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.sidebar.inc.php";


if($_REQUEST['sel_search_sdate'] == ""){
  $_REQUEST['sel_search_sdate'] = date("Y-m-01");
}

if($_REQUEST['sel_search_edate'] == ""){
  $_REQUEST['sel_search_edate'] = date("Y-m-d");
}
//$_qs_txt = "sel_search_sdate=".$_REQUEST['sel_search_sdate']."&sel_search_edate=".$_REQUEST['sel_search_edate']."&pg=";
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
                            <form method="POST" name="frm_search" id="frm_search" action="<?=$_SERVER['PHP_SELF']?>" onsubmit="return frm_search_chk(this, event);" class="d-flex justify-content-end align-items-center">
                                <script type="text/javascript">
                                    <!--
                                    document.addEventListener("DOMContentLoaded", function () {
                                        $(".searchByStatus").select2({
                                            placeholder: "접속자",
                                            minimumResultsForSearch: -1,
                                            width: '120px',
                                        });
                                    });

                                    function frm_search_chk(f) {


                                        return true;
                                    }

                                    //-->
                                </script>

                                <div class="col-2 px-0 pr-2">
                                  <div class="input-group m-0">
                                    <input type="text" name="sel_search_sdate" id="sel_search_sdate" value="<?= $_REQUEST['sel_search_sdate'] ?>"
                                           class="form-control" readonly/>
                                    <span class="m-2">~</span>
                                    <input type="text" name="sel_search_edate"
                                           id="sel_search_edate"
                                           value="<?= $_REQUEST['sel_search_edate'] ?>"
                                           class="form-control"
                                           readonly/>
                                  </div>
                                </div>

                                <select class="form-control searchByStatus" name="sel_act" id="sel_act" tabindex="-1" aria-hidden="true">
                                    <option value="list">접속자</option>
                                    <option value="domain">도메인</option>
                                    <option value="browser">브라우저</option>
                                    <option value="os">운영체제</option>
                                    <option value="device">접속기기</option>
                                    <option value="hour">시간</option>
                                    <option value="week">요일</option>
                                    <option value="day">일</option>
                                    <option value="month">월</option>
                                    <option value="year">년</option>
                                </select>

                                <button type="submit" class="btn btn-secondary margin-right-5">선택</button>
                                <button type="button" class="btn btn-gray" onclick="location.href='./list.php'">초기화</button>
                            </form>
                        </div>
                    </div>


                    <!-- 게시물 리스트-->
                    <form name="frm_list" id="frm_list" onsubmit="return false;">
                        <input type="hidden" name="act" id="act" value="<?=$_REQUEST['sel_act'] ?? 'list'?>" />
                        <input type="hidden" name="obj_list" id="obj_list" value="notice_list_box" />
                        <input type="hidden" name="obj_frm" id="obj_frm" value="frm_list" />
                        <input type="hidden" name="obj_uri" id="obj_uri" value="./update.php" />
                        <input type="hidden" name="obj_pg" id="obj_pg" value="1" />
                        <input type="hidden" name="obj_limit_num" id="obj_limit_num" value="10" />
                        <input type="hidden" name="obj_orderby" id="obj_orderby" value="" />
                        <input type="hidden" name="obj_order_desc_asc" id="obj_order_desc_asc" value="1" />

                        <input type="hidden" name="obj_sdate" id="obj_sdate" value="<?=$_REQUEST['sel_search_sdate']?>" />
                        <input type="hidden" name="obj_edate" id="obj_edate" value="<?=$_REQUEST['sel_search_edate']?>" />
                    </form>
                    <div id="notice_list_box"></div>

                    <!-- 상태 메시지 표시 -->
                    <div id="statusMessage" class="alert alert-success" style="display:none; position:fixed; bottom:20px; right:50px;"></div>

                    <script>
                        $(document).ready(function() {
                            //f_get_box_mng_list();

                            $('#sel_search_sdate').datetimepicker({
                                format: 'Y-m-d',
                                onShow: function (ct) {
                                    this.setOptions({
                                        maxDate: $('#sel_search_edate').val() ? $('#sel_search_edate').val() : false
                                    })
                                },
                                timepicker: false
                            });
                            $('#sel_search_edate').datetimepicker({
                                format: 'Y-m-d',
                                onShow: function (ct) {
                                    this.setOptions({
                                        minDate: $('#sel_search_sdate').val() ? $('#sel_search_sdate').val() : false
                                    })
                                },
                                timepicker: false
                            });
                        });
                        $(document).ready(function () {
                            var history_data = history.state;
                            if(history_data) {
                                f_get_box_mng_list(history_data.page, '');
                            } else {
                                f_get_box_mng_list('<?=$_GET['pg'] ? $_GET['pg'] : 1?>', '');
                            }
                        });

                        <?php if ($_REQUEST['sel_act']) { ?>
                        $('#sel_act').val('<?=$_REQUEST['sel_act']?>');
                        <?php } ?>



                    </script>


                  <script>



                      // 사용 예시
                      $(document).ready(function() {

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