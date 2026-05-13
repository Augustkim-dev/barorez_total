<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu='1';
$chk_sub_menu='5';
include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.menu.inc.php";
//include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.sidebar.inc.php";

$DB->orderBy("idx", "asc");
$DB->where('mt_level', 2);
$member_list = $DB->get("member_t");

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


                        <div class="col-6 col-lg-12">
                            <form method="POST" name="frm_search" id="frm_search" action="<?=$_SERVER['PHP_SELF']?>" onsubmit="return frm_search_chk(this, event);" class="d-flex justify-content-end align-items-center">
                                <script type="text/javascript">
                                    <!--
                                    document.addEventListener("DOMContentLoaded", function () {
                                        $(".searchByStatus").select2({
                                            placeholder: "통합검색",
                                            minimumResultsForSearch: -1,
                                            width: '120px',
                                        });

                                        $('#sel_mt_idx, #sel_ct_status').select2({
                                            placeholder: "선택하세요",
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

                                    <? if($_POST['sel_search']) { ?>$('#sel_search').val('<?=$_POST['sel_search']?>');<? } ?>
                                    //-->
                                </script>
                                <div class="col-2 px-0 pr-1">
                                  <select class="form-control" name="sel_mt_idx" id="sel_mt_idx" tabindex="-1" aria-hidden="true">
                                    <option value="">선택하세요</option>
                                    <?php
                                      foreach ($member_list as $pr) {
                                        $selected = ($pr['idx'] == $_POST['sel_mt_idx']) ? 'selected' : '';
                                        echo '<option value="' . $pr['idx'] . '" ' . $selected . '>' . $pr['mt_name'] . '(' .$pr['mt_id']. ')</option>';
                                      }
                                    ?>
                                  </select>
                                </div>
                                <div class="col-1 px-0 pr-1">
                                  <select class="form-control" name="sel_ct_status" id="sel_ct_status" tabindex="-1" aria-hidden="true">
                                    <option value="">선택하세요</option>
                                    <option value="add" <?php echo ($_POST['sel_ct_status']=='add')?'selected':''?> ><?php echo $arr_cash_status['add'] ?></option>
                                    <option value="remove" <?php echo ($_POST['sel_ct_status']=='remove')?'selected':''?>><?php echo $arr_cash_status['remove'] ?></option>
                                    <option value="remove_expired" <?php echo ($_POST['sel_ct_status']=='remove_expired')?'selected':''?>><?php echo $arr_cash_status['remove_expired'] ?></option>
                                  </select>
                                </div>

                                <select class="form-control searchByStatus" name="sel_search" id="sel_search" tabindex="-1" aria-hidden="true">
                                    <option value="all">통합검색</option>
                                    <option value="a1.bm_name">이름</option>
                                </select>
                                <input type="text" class="form-control searchByText" name="search_txt" id="search_txt" value="<?=$_POST['search_txt']?>"  placeholder="검색어를 입력바랍니다.">
                                <button type="submit" class="btn btn-secondary margin-right-5">검색</button>
                                <button type="button" class="btn btn-info margin-right-5" onclick="location.href='./form.php'">신규등록</button>
                                <button type="button" class="btn btn-gray" onclick="location.href='./list.php'">초기화</button>
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
                        <input type="hidden" name="obj_sel_mt_idx" id="obj_sel_mt_idx" value="<?=$_POST['sel_mt_idx']?>" />
                        <input type="hidden" name="obj_sel_ct_status" id="obj_sel_ct_status" value="<?=$_POST['sel_ct_status']?>" />
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


                    </script>





                </div>
            </div>

        </div>
    </div>
    <!-- //END PAGE CONTENT CONTAINER -->
<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/foot.inc.php";
?>