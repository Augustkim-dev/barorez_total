<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu='15';
$chk_sub_menu='1';
include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.menu.inc.php";
//include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.sidebar.inc.php";


$search_status = $_POST['search_status'] ?? 1;
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
                                        //     jalert("검색어를 입력바랍니다.");
                                        //     f.search_txt.focus();
                                        //     return false;
                                        // }

                                        return true;
                                    }
                                    $(document).ready(function() {
                                      $('.local-search-btn').on('click', function(e){
                                          let local = $(this).attr('data-local')

                                          console.log(local)
                                          $('.local-search-btn').removeClass('btn-secondary').addClass('btn-outline-secondary');
                                          $(this).removeClass('btn-outline-secondary').addClass('btn-secondary');
                                          $('#search_status').val(local);
                                          $('#frm_search').submit();
                                      })
                                    })

                                    <? if($_POST['sel_search']) { ?>$('#sel_search').val('<?=$_POST['sel_search']?>');<? } ?>
                                    //-->
                                </script>
                                 <input type="hidden" id="search_status" name="search_status" value="<?=$search_status?>" />
                                <select class="form-control searchByStatus" name="sel_search" id="sel_search" tabindex="-1" aria-hidden="true">
                                    <option value="a2.gmt_golf_name">골프장명</option>
                                </select>
                                <input type="text" class="form-control searchByText" name="search_txt" id="search_txt" value="<?=$_POST['search_txt']?>"  placeholder="검색어를 입력바랍니다.">
                                <button type="submit" class="btn btn-secondary margin-right-5">검색</button>
                              <button type="button" class="btn btn-gray margin-right-5" onclick="location.href='./list.php'">초기화</button>

                              <button type="button" class="btn btn-info margin-right-5" onclick="location.href='./form_buy.php'">구매등록</button>
                              <button type="button" class="btn btn-outline-info" onclick="location.href='./form_sell.php'">판매등록</button>

                            </form>
                        </div>


                        <div class="col-12 mt-2">
                          <div class="d-flex justify-content-start">
                            <?php foreach($arr_gmtt_status as $key=>$value) {?>
                              <?php
                              if($key == 4) continue;
                              $local_class_name = ($search_status == $key) ? 'btn-secondary' : 'btn-outline-secondary';
                              ?>
                              <button type="button" data-local="<?php echo $key?>" class="margin-right-5 local-search-btn btn <?php echo $local_class_name?>">
                                <?php echo $value?>
                              </button>
                            <?php }?>
                          </div>
                        </div>
                      

                    </div>


                    <!-- 판매 리스트-->
                    <form name="frm_list" id="frm_list" onsubmit="return false;">
                        <input type="hidden" name="act" value="sell_list" />
                        <input type="hidden" name="obj_list" id="obj_list" value="sell_list_box" />
                        <input type="hidden" name="obj_frm" id="obj_frm" value="frm_list" />
                        <input type="hidden" name="obj_uri" id="obj_uri" value="./update.php" />
                        <input type="hidden" name="obj_pg" id="obj_pg" value="1" />
                        <input type="hidden" name="obj_limit_num" id="obj_limit_num" value="10" />
                        <input type="hidden" name="obj_orderby" id="obj_orderby" value="" />
                        <input type="hidden" name="obj_order_desc_asc" id="obj_order_desc_asc" value="1" />
                        <input type="hidden" name="obj_sel_search" id="obj_sel_search" value="<?=$_POST['sel_search']?>" />
                        <input type="hidden" name="obj_search_txt" id="obj_search_txt" value="<?=$_POST['search_txt']?>" />
                        <input type="hidden" name="obj_search_status" id="obj_search_status" value="<?=$search_status?>" />
                    </form>

                    <!-- 구매 리스트-->
                    <form name="buy_list" id="buy_list" onsubmit="return false;">
                      <input type="hidden" name="act" value="buy_list" />
                      <input type="hidden" name="list_v_list" id="list_v_list" value="buy_list_box" />
                      <input type="hidden" name="list_v_frm" id="list_v_frm" value="buy_list" />
                      <input type="hidden" name="list_v_uri" id="list_v_uri" value="./update.php" />
                      <input type="hidden" name="list_v_pg" id="list_v_pg" value="1" />
                      <input type="hidden" name="list_v_limit_num" id="list_v_limit_num" value="10" />
                      <input type="hidden" name="list_v_orderby" id="list_v_orderby" value="" />
                      <input type="hidden" name="list_v_order_desc_asc" id="list_v_order_desc_asc" value="1" />
                      <input type="hidden" name="list_v_sel_search" id="list_v_sel_search" value="<?=$_POST['sel_search']?>" />
                      <input type="hidden" name="list_v_search_txt" id="list_v_search_txt" value="<?=$_POST['search_txt']?>" />
                      <input type="hidden" name="list_v_search_status" id="list_v_search_status" value="<?=$search_status?>" />
                    </form>



                  <div class="row">
                      <div class="col-md-6">
                        <div id="sell_list_box"></div>
                      </div>
                      <div class="col-md-6">
                        <div id="buy_list_box"></div>
                      </div>
                    </div>



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
                                f_get_box_mng_second_list(history_data.page, '');
                            } else {
                                f_get_box_mng_list('<?=$_GET['pg'] ? $_GET['pg'] : 1?>', '');
                                f_get_box_mng_second_list('<?=$_GET['pg'] ? $_GET['pg'] : 1?>', '');
                            }


                        });

                        <?php if ($_POST['sel_search']) { ?>
                        $('#sel_search').val('<?=$_POST['sel_search']?>');
                        <?php } ?>





                  </script>


                </div>
            </div>

        </div>
    </div>
    <!-- //END PAGE CONTENT CONTAINER -->
<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/foot.inc.php";
?>