<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.menu.inc.php";
//include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.sidebar.inc.php";

$chk_ckeditor = "Y";
if ($_GET['act'] == "update") {
    $DB->where('ct_id', $_GET['ct_idx']);
    $row = $DB->getone('board_category_t', '*, ct_id as ct_idx');

    $_act = "update";
    $_act_txt = " 수정";
} else {
    $_act = "input";
    $_act_txt = " 등록";
}
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
                    <form method="post" name="frm_form" id="frm_form" action="./blog_category_update.php" target="hidden_ifrm" enctype="multipart/form-data">
                        <input type="hidden" name="act" id="act" value="<?=$_act?>" />
                        <input type="hidden" name="ct_idx" id="ct_idx" value="<?=$row['ct_idx']?>" />
                        <div class="card-body">
                            <h4 id="rw-fe-basic">블로그 카테고리 <?=$_act_txt?></h4>
                            <div class="form-group row">
                                <label for="nt_title" class="col-sm-2 col-form-label">카테고리명</label>
                                <div class="col-sm-10">
                                    <input type="text" name="ct_name" id="ct_name" value="<?=$row['ct_name']?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="nt_title" class="col-sm-2 col-form-label">카테고리설명</label>
                                <div class="col-sm-10">
                                    <input type="text" name="ct_sub_name" id="ct_sub_name" value="<?=$row['ct_sub_name']?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="nt_show" class="col-sm-2 col-form-label">노출여부</label>
                                <div class="col-sm-10">
                                    <select name="ct_show" id="ct_show" class="form-control select-simple">
                                        <option value="Y">Y</option>
                                        <option value="N">N</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="nt_wdate" class="col-md-2 col-form-label">등록일시</label>
                                <div class="col-md-10">
                                    <?=DateType($row['ct_datetime'], 6)?>
                                </div>
                            </div>
                            <div class="form-group row justify-content-center margin-top-30">
                                <button type="button"  onclick="history.go(-1);" class="btn btn-outline-secondary mx-1" >목록</button>
                                <button type="submit" class="btn btn-secondary" >확인</button>
                            </div>
                        </div>
                    </form>
                    <div id="notice_list_box"></div>
                    <script>
                        $(document).ready(function() {
                            f_get_box_mng_list();
                        });

                        <? if($row['ct_show']) { ?>$('#nt_show').val('<?=$row['ct_show']?>');<? } ?>


                        $(document).ready(function() {
                            $("#frm_form").validate({
                                submitHandler: function(form) {
                                    $('#splinner_modal').modal('toggle');
                                    return true;
                                },
                                rules: {
                                    ct_name: {
                                        required: true
                                    }
                                },
                                messages: {
                                    ct_name: {
                                        required: "카테고리명을 입력해주세요"
                                    }
                                },
                                errorElement: 'span',
                                errorPlacement: function(error, element) {
                                    error.addClass('invalid-feedback');
                                    element.closest('.col-sm-10').append(error);
                                },
                                highlight: function(element, errorClass, validClass) {
                                    $(element).addClass('is-invalid');
                                },
                                unhighlight: function(element, errorClass, validClass) {
                                    $(element).removeClass('is-invalid');
                                }
                            });
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