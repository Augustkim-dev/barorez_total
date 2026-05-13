<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu='99';
$chk_sub_menu='4';
include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.menu.inc.php";
//include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.sidebar.inc.php";

$chk_ckeditor = "Y";
if ($_GET['act'] == "update") {
    $DB->where('ct_id', $_GET['ct_idx']);
    $row = $DB->getone('category_upjong_t', '*, ct_id as ct_idx');

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
                <h1 class="title">업종 카테고리</h1>
                <p class="caption">
                    업종 카테고리 등록, 수정, 삭제 등을 할 수 있습니다.
                </p>
            </div>
            <nav aria-label="breadcrumb" role="navigation">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#">환경설정</a></li>
                    <li class="breadcrumb-item active">업종 카테고리</li>
                </ol>
            </nav>
        </div>
        <!-- //END PAGE HEADING -->
        <div class="container-fluid">
            <div class="card margin-bottom-0">
                <div class="card-body">
                    <form method="post" name="frm_form" id="frm_form" action="./category_upjong_update.php" target="hidden_ifrm" enctype="multipart/form-data">
                        <input type="hidden" name="act" id="act" value="<?=$_act?>" />
                        <input type="hidden" name="ct_idx" id="ct_idx" value="<?=$row['ct_idx']?>" />
                        <input type="hidden" name="ct_img1" id="ct_img1" value="<?=$row['ct_img1']?>" />
                        <input type="hidden" name="ct_img2" id="ct_img2" value="<?=$row['ct_img2']?>" />
                        <div class="card-body">
                            <h4 id="rw-fe-basic">업종 카테고리 <?=$_act_txt?></h4>
                            <div class="form-group row margin-top-30">
                                <label for="ct_title" class="col-sm-2 col-form-label">카테고리명</label>
                                <div class="col-sm-10">
                                    <input type="text" name="ct_name" id="ct_name" value="<?=$row['ct_name']?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="ct_show" class="col-sm-2 col-form-label">노출여부</label>
                                <div class="col-sm-10">
                                    <select name="ct_show" id="ct_show" class="form-control select-simple">
                                        <option value="Y">사용</option>
                                        <option value="N">미사용</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="ct_show" class="col-sm-2 col-form-label">아이콘</label>
                                <div class="col-sm-10">
                                    <div class="upload-container" id="sortableContainer">
                                        <div class="upload-box" id="uploadTrigger">
                                            <div class="plus">+</div>
                                            <div class="text">Upload</div>
                                        </div>
                                    </div>
                                    <input type="file" class="filepond d-none" multiple>
                                </div>
                            </div>
                            <? if ($_GET['act'] == "update") {?>
                            <div class="form-group row">
                                <label for="ct_wdate" class="col-md-2 col-form-label">등록일시</label>
                                <div class="col-md-10">
                                    <p class="margin-top-10"><?=DateType($row['ct_wdate'], 6)?></p>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="ct_wdate" class="col-md-2 col-form-label">수정일시</label>
                                <div class="col-md-10">
                                    <p class="margin-top-10"><?=DateType($row['ct_udate'], 6)?></p>
                                </div>
                            </div>
                            <? } ?>
                            <div class="form-group row justify-content-center margin-top-30">
                                <button type="button"  onclick="history.go(-1);" class="btn btn-outline-secondary mx-1" >목록</button>
                                <button type="submit" class="btn btn-secondary" >확인</button>
                            </div>
                        </div>
                    </form>


                    <script type="application/javascript">

                        document.addEventListener('DOMContentLoaded', function() {
                            // FileUploader 초기화
                            const uploader = createFileUploader({
                                container: '.upload-container',
                                trigger: '#uploadTrigger',
                                filepondElement: '.filepond',
                                maxFiles: 1,
                                maxFileSize: '5MB',
                                allowedFileTypes: ['image/jpeg', 'image/png', 'image/gif'],
                                imageMinWidth: 100,
                                imageMinHeight: 100,
                                imageMaxWidth: 4000,
                                imageMaxHeight: 4000,
                                ajaxUrl: './category_upjong_update.php'
                            });

                            // 폼 검증 및 제출
                            $("#frm_form").validate({
                                submitHandler: function(form) {
                                    const formData = new FormData(form);

                                    // maxFiles 값을 FormData에 추가
                                    formData.append('maxFiles', uploader.options.maxFiles);

                                    // FilePond 파일들을 FormData에 추가
                                    const files = uploader.getPond().getFiles();
                                    files.forEach((fileItem, index) => {
                                        if (index < uploader.options.maxFiles) {  // maxFiles 값 만큼만 전송
                                            formData.append(`ct_img${index + 1}`, fileItem.file);
                                        }
                                    });

                                    // 삭제된 파일 정보 전송
                                    const removedFiles = uploader.getRemovedFiles(); // 삭제된 파일 번호 배열
                                    formData.append('removed_files', JSON.stringify(removedFiles));

                                    // 이미지 순서 정보 추가
                                    const imageOrder = uploader.getImageOrder();
                                    formData.append('image_order', JSON.stringify(imageOrder));

                                    $.ajax({
                                        url: './category_upjong_update.php',
                                        type: 'POST',
                                        data: formData,
                                        processData: false,
                                        contentType: false,
                                        beforeSend: function() {
                                            $('#splinner_modal').modal('show');
                                        },
                                        success: function(response) {
                                            $('#splinner_modal').modal('hide');
                                            console.log(response)
                                            if(response.success) {
                                                alert(response.message);
                                                location.href = './category_upjong_list.php';
                                            }
                                        },
                                        error: function() {
                                            $('#splinner_modal').modal('hide');
                                            alert('처리 중 오류가 발생했습니다.');
                                        }
                                    });
                                    return false;
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

                            <?php if(isset($_GET['act']) && $_GET['act'] == "update") { ?>
                            const ct_idx = '<?php echo $_GET["ct_idx"] ?? ""; ?>';
                            if(ct_idx) {
                                uploader.loadImages(ct_idx);
                            }
                            <?php } ?>
                        });

                        // 기존 데이터 설정
                        <? if($row['ct_show']) { ?>
                        $('#ct_show').val('<?=$row['ct_show']?>');
                        <? } ?>
                    </script>

                </div>
            </div>

        </div>
    </div>
    <!-- //END PAGE CONTENT CONTAINER -->
<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/foot.inc.php";
?>