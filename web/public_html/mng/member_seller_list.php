<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu='2';
$chk_sub_menu='1';
include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.menu.inc.php";
//include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.sidebar.inc.php";
?>
    <!-- PAGE CONTENT CONTAINER -->
    <div class="content" id="content">
        <!-- PAGE HEADING -->
        <div class="page-heading">
            <div class="page-heading__container">
                <div class="icon">
                    <span class="li-picture3"></span>
                </div>
                <h1 class="title">스토어회원</h1>
                <p class="caption">
                    스토어회원 수정, 삭제, 정지 등을 할 수 있습니다.
                </p>
            </div>
            <nav aria-label="breadcrumb" role="navigation">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#">회원관리</a></li>
                    <li class="breadcrumb-item active">스토어회원</li>
                </ol>
            </nav>
        </div>
        <!-- //END PAGE HEADING -->
        <div class="container-fluid">
            <div class="card margin-bottom-0">
                <div class="card-body">
                    <form method="POST" name="frm_list" id="frm_list" action="<?=$_SERVER['PHP_SELF']?>" onsubmit="return frm_search_chk(this, event);">
                        <input type="hidden" name="act" id="act" value="list" />
                        <input type="hidden" name="obj_list" id="obj_list" value="member_list_box" />
                        <input type="hidden" name="obj_frm" id="obj_frm" value="frm_list" />
                        <input type="hidden" name="obj_uri" id="obj_uri" value="./member_update.php" />
                        <input type="hidden" name="obj_pg" id="obj_pg" value="1" />
                        <input type="hidden" name="obj_orderby" id="obj_orderby" value="" />
                        <input type="hidden" name="obj_order_desc_asc" id="obj_order_desc_asc" value="1" />
                        <input type="hidden" name="sel_lv" value="<?=$_GET['sel_lv']?>" />
                        <div class="form-row">
                            <div class="col-6 col-lg-3">
                            </div>
                            <div class="col-6 col-lg-9 d-flex justify-content-end align-items-center">
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
                                    <option value="a1.nt_title">제목</option>
                                    <option value="a1.nt_content">내용</option>
                                </select>
                                <input type="text" class="form-control searchByText" name="search_txt" id="search_txt" value="<?=$_POST['search_txt']?>"  placeholder="검색어를 입력바랍니다.">
                                <button type="submit" class="btn btn-secondary margin-right-5">검색</button>
                                <button type="button" class="btn btn-info margin-right-5" onclick="location.href='./member_form.php?sel_lv=<?=$_GET['sel_lv']?>'">회원등록</button>
                                <button type="button" class="btn btn-gray" onclick="location.href='./notice_list.php'">초기화</button>
                            </div>
                        </div>
                    </form>
                    <div id="member_list_box"></div>
                    <script>
                        $(document).ready(function() {
                            f_get_box_mng_list();
                        });

                        <?php if ($_POST['sel_search']) { ?>
                        $('#sel_search').val('<?=$_POST['sel_search']?>');
                        <?php } ?>
                        <?php if ($_POST['sel_mt_login_type']) { ?>
                        $('#sel_mt_login_type').val('<?=$_POST['sel_mt_login_type']?>');
                        <?php } ?>
                        <?php if ($_POST['sel_mt_status']) { ?>
                        $('#sel_mt_status').val('<?=$_POST['sel_mt_status']?>');
                        <?php } ?>
                        <?php if ($_POST['sel_mt_seller']) { ?>
                        $('#sel_mt_seller').val('<?=$_POST['sel_mt_seller']?>');
                        <?php } ?>

                        function f_status_chg(v, o, idx) {
                            console.log({act: 'status_chg', mt_idx: idx, mt_obj: o, mt_val: v});
                            $.post('./member_update.php', {act: 'status_chg', mt_idx: idx, mt_obj: o, mt_val: v}, function (data) {
                                if ($.trim(data)=='Y') {
                                    //jalert('처리되었습니다.');
                                }
                            });
                        }
                        function f_excel_down() {
                            var f = document.fexcel;
                            hidden_ifrm.document.location.href = './excel_down.php?excel_down='+f.excel_down.value+'&excel_query='+f.excel_query.value;

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