<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.menu.inc.php";
//include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.sidebar.inc.php";

$chk_ckeditor = "Y";
if ($_GET['act'] == "update") {
    $DB->where('idx', $_GET['btIdx']);
    $row = $DB->getone('blog_t', '*, idx as btIdx');

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
                <h1 class="title">공지사항</h1>
                <p class="caption">
                    공지사항 등록, 수정, 삭제 등을 할 수 있습니다.
                </p>
            </div>
            <nav aria-label="breadcrumb" role="navigation">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#">게시판관리</a></li>
                    <li class="breadcrumb-item active">블로그</li>
                </ol>
            </nav>
        </div>
        <!-- //END PAGE HEADING -->
        <div class="container-fluid">
            <div class="card margin-bottom-0">
                <div class="card-body">
                    <form method="post" name="frm_form" id="frm_form" action="./blog_update.php" target="hidden_ifrm" enctype="multipart/form-data">
                        <input type="hidden" name="act" id="act" value="<?=$_act?>" />
                        <input type="hidden" name="btIdx" id="btIdx" value="<?=$row['btIdx']?>" />
                        <div class="card-body">
                            <h4 id="rw-fe-basic">블로그 등록</h4>
                            <div class="form-group row">
                                <label for="bt_catetory" class="col-sm-2 col-form-label">카테고리</label>
                                <div class="col-sm-10">
                                    <select name="bt_catetory" id="bt_catetory" class="custom-select" required>
                                        <option value="">선택하세요.</option>
                                        <?
                                        $DB->where('ct_level', '0');
                                        $DB->orderBy('ct_rank', 'asc')->orderBy('ct_id', 'asc');
                                        $list_ct = $DB->get("board_category_t");
                                        if($list_ct) {
                                            foreach ($list_ct as $row_ct) {
                                                echo '<option value="'.$row_ct['ct_id'].'" '.($row_ct['ct_id']===$row['ct_id']?'selected':'').'>'.$row_ct['ct_name'].'</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="bt_title" class="col-sm-2 col-form-label">제목</label>
                                <div class="col-sm-10">
                                    <input type="text" name="bt_title" id="bt_title" value="<?php echo htmlspecialchars($row['bt_title'], ENT_QUOTES); ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="bt_keyword" class="col-sm-2 col-form-label">키워드</label>
                                <div class="col-sm-10">
                                    <input type="text" name="bt_keyword" id="bt_keyword" value="<?=$row['bt_keyword']?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="bt_content" class="col-sm-2 col-form-label">내용</label>
                                <div class="col-sm-10">
                                    <?php
                                    $editor_name = 'bt_content';
                                    if($chk_ckeditor) {
                                        $editor_upload = 'Y';
                                        include "./inc/ckeditor.php";
                                    }else {
                                        echo "<textarea name='".$editor_name."' id='".$editor_name."' class='form-control' style='height:450px;'>".$row[$editor_name]."</textarea>";
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="bt_show" class="col-sm-2 col-form-label">노출여부</label>
                                <div class="col-sm-10">
                                    <select name="bt_show" id="bt_show" class="form-control select-simple">
                                        <option value="Y">Y</option>
                                        <option value="N">N</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="bt_file1" class="col-sm-2 col-form-label">첨부파일1</label>
                                <div class="col-sm-10">
                                    <label class="custom-file">
                                        <input type="file" id="bt_file1" class="custom-file-input">
                                        <span class="custom-file-label" id="file-label1">Choose file</span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="bt_file2" class="col-sm-2 col-form-label">첨부파일2</label>
                                <div class="col-sm-10">
                                    <label class="custom-file">
                                        <input type="file" id="bt_file2" class="custom-file-input">
                                        <span class="custom-file-label" id="file-label2">Choose file</span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="bt_wdate" class="col-md-2 col-form-label">등록일시</label>
                                <div class="col-md-10">
                                    <?=DateType($row['bt_wdate'], 6)?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="bt_udate" class="col-md-2 col-form-label">수정일시</label>
                                <div class="col-md-10">
                                    <?=DateType($row['bt_udate'], 6)?>
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
                        <? if($row['bt_catetory']) { ?>$('#bt_catetory').val('<?=$row['bt_catetory']?>');<? } ?>
                        <? if($row['bt_show']) { ?>$('#bt_show').val('<?=$row['bt_show']?>');<? } ?>

                        function updateFileName(inputId, labelId) {
                            var fileInput = document.getElementById(inputId);
                            var label = document.getElementById(labelId);

                            fileInput.addEventListener('change', function(event) {
                                var fileName = fileInput.files.length > 0 ? fileInput.files[0].name : "Choose file";
                                if (label) {
                                    label.textContent = fileName;
                                } else {
                                    console.error("Label element not found for", labelId);
                                }
                            });
                        }

                        // 각 파일 입력 요소에 대해 이벤트 리스너 추가
                        updateFileName('bt_file1', 'file-label1');
                        updateFileName('bt_file2', 'file-label2');


                        $(document).ready(function() {
                            $("#frm_form").validate({
                                submitHandler: function(form) {
                                    $('#splinner_modal').modal('toggle');
                                    return true;
                                },
                                rules: {
                                    nt_title: {
                                        required: true
                                    },
                                    nt_content: {
                                        required: true
                                    }
                                },
                                messages: {
                                    nt_title: {
                                        required: "제목을 입력해주세요"
                                    },
                                    nt_content: {
                                        required: "내용을 입력해주세요."
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