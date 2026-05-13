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
                <h1 class="title">블로그 카테고리</h1>
                <p class="caption">
                    블로그 카테고리 등록, 수정, 삭제 등을 할 수 있습니다.
                </p>
            </div>
            <nav aria-label="breadcrumb" role="navigation">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#">블로그</a></li>
                    <li class="breadcrumb-item active">카테고리</li>
                </ol>
            </nav>
        </div>
        <!-- //END PAGE HEADING -->
        <div class="container-fluid">
            <div class="card margin-bottom-0">
                <div class="card-body">
                    <form method="post" name="frm_list" id="frm_list" onsubmit="return false;">
                        <input type="hidden" name="act" id="act" value="list" />
                        <input type="hidden" name="obj_list" id="obj_list" value="blog_category_list_box" />
                        <input type="hidden" name="obj_frm" id="obj_frm" value="frm_list" />
                        <input type="hidden" name="obj_uri" id="obj_uri" value="./blog_category_update.php" />
                        <input type="hidden" name="obj_pg" id="obj_pg" value="1" />
                        <input type="hidden" name="obj_orderby" id="obj_orderby" value="" />
                        <input type="hidden" name="obj_order_desc_asc" id="obj_order_desc_asc" value="1" />
                        <div class="form-row">
                            <div class="col-6 col-lg-2">
                            </div>
                            <div class="col-6 col-lg-2 d-none d-md-block">
                            </div>
                            <div class="col-6 col-lg-4 d-none d-md-block">
                            </div>
                            <div class="col-6 col-lg-2">
                            </div>
                            <div class="col-6 col-lg-2 d-none d-md-block">
                                <button class="btn btn-secondary btn-block" onclick="f_localStorage_reset_go('./blog_category_form.php');" >신규등록</button>
                            </div>
                        </div>
                    </form>
                    <div id="blog_category_list_box"></div>
                    <script>
                        $(document).ready(function() {
                            f_get_box_mng_list();
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