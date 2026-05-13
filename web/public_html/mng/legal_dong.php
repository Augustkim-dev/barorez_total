<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu='99';
$chk_sub_menu='3';
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
                <h1 class="title">법정동 코드</h1>
                <p class="caption">
                    법정동 코드 정보를 확인 할 수 있습니다.
                </p>
            </div>
            <nav aria-label="breadcrumb" role="navigation">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#">환경설정</a></li>
                    <li class="breadcrumb-item active">법정동 코드</li>
                </ol>
            </nav>
        </div>
        <!-- //END PAGE HEADING -->
        <div class="container-fluid">
            <div class="card margin-bottom-0">
                <div class="card-body">
                    <form method="post" name="frm_list" id="frm_list" onsubmit="return false;">
                        <input type="hidden" name="act" id="act" value="list" />
                        <input type="hidden" name="obj_list" id="obj_list" value="legal_dong_list_box" />
                        <input type="hidden" name="obj_frm" id="obj_frm" value="frm_list" />
                        <input type="hidden" name="obj_uri" id="obj_uri" value="./legal_dong_update.php" />
                        <input type="hidden" name="obj_pg" id="obj_pg" value="1" />
                        <input type="hidden" name="obj_limit_num" id="obj_limit_num" value="50" />
                        <input type="hidden" name="obj_orderby" id="obj_orderby" value="" />
                        <input type="hidden" name="obj_order_desc_asc" id="obj_order_desc_asc" value="1" />
                        <div class="form-row">
                            <div class="col-6 col-lg-3">

                            </div>

                            <script>
                                $(document).ready(function(){
                                    $('[data-toggle="tooltip"]').tooltip();
                                });
                            </script>

                            <div class="col-6 col-lg-9 d-flex justify-content-end align-items-center">
                                <script type="text/javascript">
                                    document.addEventListener("DOMContentLoaded", function () {
                                        $(".searchByStatus").select2({
                                            placeholder: "통합검색",
                                            minimumResultsForSearch: -1,
                                            width: '120px',
                                        });
                                    });

                                </script>

                                <select class="form-control searchByStatus" name="sel_search" id="sel_search" tabindex="-1" aria-hidden="true">
                                    <option>통합검색</option>
                                    <option value="1">New</option>
                                    <option value="2">Active</option>
                                    <option value="3">Closed</option>
                                    <option value="3">Denied</option>
                                </select>
                            </div>
                        </div>
                    </form>

                    <!-- 게시물 리스트-->
                    <div id="legal_dong_list_box"></div>

                    <script>
                        $(document).ready(function() {
                            f_get_box_mng_list();
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