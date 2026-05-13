<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
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
                <h1 class="title">공지사항</h1>
                <p class="caption">
                    공지사항 등록, 수정, 삭제 등을 할 수 있습니다.
                </p>
            </div>
            <nav aria-label="breadcrumb" role="navigation">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#">게시판관리</a></li>
                    <li class="breadcrumb-item active">공지사항</li>
                </ol>
            </nav>
        </div>
        <!-- //END PAGE HEADING -->
        <div class="container-fluid">
            <div class="card margin-bottom-0">
                <div class="card-body">
                    <form method="post" name="frm_list" id="frm_list" onsubmit="return false;">
                        <input type="hidden" name="act" id="act" value="list" />
                        <input type="hidden" name="obj_list" id="obj_list" value="blog_list_box" />
                        <input type="hidden" name="obj_frm" id="obj_frm" value="frm_list" />
                        <input type="hidden" name="obj_uri" id="obj_uri" value="./blog_update.php" />
                        <input type="hidden" name="obj_pg" id="obj_pg" value="1" />
                        <input type="hidden" name="obj_orderby" id="obj_orderby" value="" />
                        <input type="hidden" name="obj_order_desc_asc" id="obj_order_desc_asc" value="1" />
                        <div class="form-row">
                            <div class="col-6 col-lg-2">
                                <select class="form-control actionWithSelected" tabindex="-1" aria-hidden="true">
                                    <option></option>
                                    <option value="1">Set as readed</option>
                                    <option value="2">Remove</option>
                                </select>
                                <script type="text/javascript">document.addEventListener("DOMContentLoaded", function () {
                                        $(".actionWithSelected").select2({placeholder: "Action with selected",minimumResultsForSearch: -1});
                                    });</script>
                            </div>
                            <div class="col-6 col-lg-2 d-none d-md-block">
                                <select class="form-control customPeriod" tabindex="-1" aria-hidden="true">
                                    <option></option>
                                    <option value="1">This month</option>
                                    <option value="2">Prev month</option>
                                    <option value="3">Other</option>
                                </select>
                                <script type="text/javascript">document.addEventListener("DOMContentLoaded", function () {
                                        $(".customPeriod").select2({placeholder: "Choose custom period",minimumResultsForSearch: -1});
                                    });</script>
                            </div>
                            <div class="col-6 col-lg-4 d-none d-md-block">
                            </div>
                            <div class="col-6 col-lg-2">
                                <select class="form-control orderByStatus" tabindex="-1" aria-hidden="true">
                                    <option></option>
                                    <option value="1">New</option>
                                    <option value="2">Active</option>
                                    <option value="3">Closed</option>
                                    <option value="3">Denied</option>
                                </select>
                                <script type="text/javascript">document.addEventListener("DOMContentLoaded", function () {
                                        $(".orderByStatus").select2({placeholder: "Status to order",minimumResultsForSearch: -1});
                                    });</script>
                            </div>
                            <div class="col-6 col-lg-2 d-none d-md-block">
                                <button class="btn btn-secondary btn-block" onclick="f_localStorage_reset_go('./blog_form.php');" >신규등록</button>
                            </div>
                        </div>
                    </form>
                    <div id="blog_list_box"></div>
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

                    </script>
                </div>
            </div>

        </div>
    </div>
    <!-- //END PAGE CONTENT CONTAINER -->
<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/foot.inc.php";
?>