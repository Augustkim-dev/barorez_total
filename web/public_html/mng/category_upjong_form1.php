<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
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
    <style>
        .upload-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            padding: 0px;
        }

        .upload-box {
            width: 120px;
            height: 120px;
            border: 2px dashed #ccc;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            position: relative;
        }

        .upload-box.hidden {
            display: none;
        }

        .upload-box .plus {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .upload-box .text {
            color: #333;
        }

        .preview-box {
            width: 120px;
            height: 120px;
            border: 1px solid #ddd;
            position: relative;
            overflow: hidden;
            cursor: move;
        }

        .preview-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .preview-box a {
            display: block;
            width: 100%;
            height: 100%;
        }

        .remove-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(255, 255, 255, 0.8);
            border: none;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
        }

        .sortable-ghost {
            opacity: 0.5;
        }

        .filepond--root {
            display: none;
        }

        .error-message {
            color: red;
            font-size: 12px;
            position: absolute;
            bottom: -20px;
            left: 0;
        }
    </style>


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

                                    <input type="file" class="filepond" multiple>



                                </div>

                            </div>
                            <? if ($_GET['act'] == "update") {?>
                                <div class="form-group row">
                                    <label for="ct_wdate" class="col-md-2 col-form-label">등록일시</label>
                                    <div class="col-md-10">
                                        <?=DateType($row['ct_wdate'], 6)?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="ct_wdate" class="col-md-2 col-form-label">수정일시</label>
                                    <div class="col-md-10">
                                        <?=DateType($row['ct_udate'], 6)?>
                                    </div>
                                </div>
                            <? } ?>
                            <div class="form-group row justify-content-center margin-top-30">
                                <button type="button"  onclick="history.go(-1);" class="btn btn-outline-secondary mx-1" >목록</button>
                                <button type="submit" class="btn btn-secondary" >확인</button>
                            </div>
                        </div>
                    </form>

                    <!-- 스크립트 -->
                    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
                    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
                    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
                    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>

                    <script>
                        // 메시지 표시 플래그
                        let hasShownMaxFileMessage = false;
                        let hasShownFileSizeMessage = false;

                        // 파일 제한 설정
                        const FILE_CONSTRAINTS = {
                            maxFiles: 2,                    // 최대 파일 개수
                            maxFileSize: '5MB',            // 최대 파일 크기
                            allowedFileTypes: ['image/jpeg', 'image/png', 'image/gif'], // 허용된 파일 타입
                            imageMinWidth: 100,            // 최소 이미지 너비
                            imageMinHeight: 100,           // 최소 이미지 높이
                            imageMaxWidth: 4000,           // 최대 이미지 너비
                            imageMaxHeight: 3000,          // 최대 이미지 높이
                        };

                        // 에러 메시지
                        const ERROR_MESSAGES = {
                            fileSize: `파일 크기는 ${FILE_CONSTRAINTS.maxFileSize}를 초과할 수 없습니다.`,
                            fileType: '허용된 파일 형식: JPG, PNG, GIF',
                            imageSize: `이미지 크기는 최소 ${FILE_CONSTRAINTS.imageMinWidth}x${FILE_CONSTRAINTS.imageMinHeight}px, 최대 ${FILE_CONSTRAINTS.imageMaxWidth}x${FILE_CONSTRAINTS.imageMaxHeight}px 이내여야 합니다.`,
                            maxFiles: `최대 ${FILE_CONSTRAINTS.maxFiles}개의 파일만 업로드할 수 있습니다.`
                        };

                        // FilePond 초기화
                        FilePond.registerPlugin(FilePondPluginImagePreview);

                        const container = document.querySelector('.upload-container');
                        const uploadTrigger = document.getElementById('uploadTrigger');

                        // 파일 유효성 검사 함수
                        function validateFile(file) {
                            return new Promise((resolve, reject) => {
                                // 파일 타입 검사
                                if (!FILE_CONSTRAINTS.allowedFileTypes.includes(file.type)) {
                                    reject(ERROR_MESSAGES.fileType);
                                    return;
                                }

                                // 파일 크기 검사
                                const maxSize = parseInt(FILE_CONSTRAINTS.maxFileSize) * 1024 * 1024;
                                if (file.size > maxSize) {
                                    reject(ERROR_MESSAGES.fileSize);
                                    return;
                                }

                                // 이미지 크기 검사
                                const img = new Image();
                                img.onload = function() {
                                    if (img.width < FILE_CONSTRAINTS.imageMinWidth ||
                                        img.height < FILE_CONSTRAINTS.imageMinHeight ||
                                        img.width > FILE_CONSTRAINTS.imageMaxWidth ||
                                        img.height > FILE_CONSTRAINTS.imageMaxHeight) {
                                        reject(ERROR_MESSAGES.imageSize);
                                    } else {
                                        resolve();
                                    }
                                };
                                img.onerror = function() {
                                    reject(ERROR_MESSAGES.fileType);
                                };
                                img.src = URL.createObjectURL(file);
                            });
                        }

                        // FilePond 설정
                        const pond = FilePond.create(document.querySelector('.filepond'), {
                            allowMultiple: true,
                            maxFiles: FILE_CONSTRAINTS.maxFiles,
                            acceptedFileTypes: FILE_CONSTRAINTS.allowedFileTypes,
                            maxFileSize: FILE_CONSTRAINTS.maxFileSize,

                            beforeAddFile: (item) => {
                                // 현재 파일 개수를 FilePond의 파일 개수로 확인
                                const currentFiles = document.querySelectorAll('.preview-box').length;

                                // 최대 파일 개수 초과 체크
                                if (currentFiles >= FILE_CONSTRAINTS.maxFiles) {
                                    alert(ERROR_MESSAGES.maxFiles); // 항상 메시지 표시하도록 수정
                                    return false;
                                }

                                return validateFile(item.file)
                                    .then(() => {
                                        return true;
                                    })
                                    .catch((error) => {
                                        alert(error); // 모든 에러 메시지 표시
                                        return false;
                                    });
                            },



                            onaddfile: (error, file) => {
                                if (error) {
                                    console.error('Error adding file:', error);
                                    return;
                                }

                                // 미리보기 박스 생성
                                const previewBox = document.createElement('div');
                                previewBox.className = 'preview-box';
                                previewBox.setAttribute('data-filepond-file-id', file.id);

                                // 이미지 미리보기
                                const img = document.createElement('img');
                                img.src = URL.createObjectURL(file.file);

                                // Fancybox 링크 생성
                                const link = document.createElement('a');
                                link.href = URL.createObjectURL(file.file);
                                link.setAttribute('data-fancybox', 'gallery');
                                link.appendChild(img);

                                // 삭제 버튼
                                const removeBtn = document.createElement('button');
                                removeBtn.className = 'remove-btn';
                                removeBtn.innerHTML = '×';
                                removeBtn.onclick = (e) => {
                                    e.preventDefault();
                                    e.stopPropagation();
                                    pond.removeFile(file.id);
                                };

                                previewBox.appendChild(link);
                                previewBox.appendChild(removeBtn);
                                container.insertBefore(previewBox, uploadTrigger);

                                // 파일 개수 체크를 실제 미리보기 박스 개수로 확인
                                const currentFiles = document.querySelectorAll('.preview-box').length;
                                if (currentFiles >= FILE_CONSTRAINTS.maxFiles) {
                                    uploadTrigger.classList.add('hidden');
                                }

                                // Fancybox 초기화
                                Fancybox.bind('[data-fancybox="gallery"]', {
                                    groupAll: true
                                });
                            },

                            onremovefile: (error, file) => {
                                if (error) return;
                                const previewBox = document.querySelector(`[data-filepond-file-id="${file.id}"]`);
                                if (previewBox) {
                                    previewBox.remove();
                                }

                                // 파일이 제거되어 최대 개수 미만이 되면 업로드 버튼 다시 표시하고 메시지 플래그 초기화
                                if (pond.getFiles().length < FILE_CONSTRAINTS.maxFiles) {
                                    uploadTrigger.classList.remove('hidden');
                                    hasShownMaxFileMessage = false;
                                    hasShownFileSizeMessage = false;
                                }
                            }
                        });

                        // 파일 선택 트리거
                        uploadTrigger.addEventListener('click', () => {
                            const currentFiles = document.querySelectorAll('.preview-box').length;

                            if (currentFiles >= FILE_CONSTRAINTS.maxFiles) {
                                alert(ERROR_MESSAGES.maxFiles);
                                return;
                            }

                            const input = document.createElement('input');
                            input.type = 'file';
                            input.multiple = true;
                            input.accept = FILE_CONSTRAINTS.allowedFileTypes.join(',');
                            input.onchange = e => {
                                const selectedFiles = Array.from(e.target.files);
                                const remainingSlots = FILE_CONSTRAINTS.maxFiles - currentFiles;

                                if (selectedFiles.length > remainingSlots) {
                                    alert(ERROR_MESSAGES.maxFiles); // 선택된 파일이 허용 개수를 초과할 경우 메시지 표시
                                }

                                const filesToAdd = selectedFiles.slice(0, remainingSlots);
                                filesToAdd.forEach(file => {
                                    pond.addFile(file);
                                });
                            };
                            input.click();
                        });



                        // Sortable 초기화
                        new Sortable(container, {
                            animation: 150,
                            handle: '.preview-box',
                            filter: '.upload-box',
                            onEnd: function(evt) {
                                console.log('Order changed');
                            }
                        });


                        // loadImages 함수 수정
                        function loadImages(ct_idx) {
                            $.ajax({
                                url: './category_upjong_update.php',
                                type: 'POST',
                                data: {
                                    act: 'loadimage',
                                    ct_idx: ct_idx
                                },
                                beforeSend: function() {
                                    $('#splinner_modal').modal('show');
                                },
                                success: function(response) {
                                    $('#splinner_modal').modal('hide');
                                    console.log(response)
                                    if(response.success) {
                                        // 기존 미리보기 초기화
                                        document.querySelectorAll('.preview-box').forEach(box => box.remove());

                                        // 이미지 1 처리
                                        if(response.data.ct_img1.exists) {
                                            createPreviewBox(response.data.ct_img1.url, 'ct_img1');
                                        }

                                        // 이미지 2 처리
                                        if(response.data.ct_img2.exists) {
                                            createPreviewBox(response.data.ct_img2.url, 'ct_img2');
                                        }

                                        // 파일 개수에 따라 업로드 버튼 표시/숨김 처리
                                        const currentFiles = document.querySelectorAll('.preview-box').length;
                                        if (currentFiles >= FILE_CONSTRAINTS.maxFiles) {
                                            uploadTrigger.classList.add('hidden');
                                        } else {
                                            uploadTrigger.classList.remove('hidden');
                                        }
                                    } else {
                                        alert(response.message || '이미지 로드 중 오류가 발생했습니다.');
                                    }
                                },
                                error: function() {
                                    $('#splinner_modal').modal('hide');
                                    alert('이미지 로드 중 오류가 발생했습니다.');
                                }
                            });
                        }

                        // 미리보기 박스 생성 함수
                        function createPreviewBox(imageUrl, imageId) {
                            const previewBox = document.createElement('div');
                            previewBox.className = 'preview-box';
                            previewBox.setAttribute('data-image-id', imageId);

                            const img = document.createElement('img');
                            img.src = imageUrl;

                            const link = document.createElement('a');
                            link.href = imageUrl;
                            link.setAttribute('data-fancybox', 'gallery');
                            link.appendChild(img);

                            const removeBtn = document.createElement('button');
                            removeBtn.className = 'remove-btn';
                            removeBtn.innerHTML = '×';
                            removeBtn.onclick = (e) => {
                                e.preventDefault();
                                e.stopPropagation();
                                // 이미지 삭제 처리
                                if(confirm('이미지를 삭제하시겠습니까?')) {
                                    previewBox.remove();
                                    document.getElementById(imageId).value = ''; // hidden input 값 초기화

                                    // 업로드 버튼 다시 표시
                                    const currentFiles = document.querySelectorAll('.preview-box').length;
                                    if (currentFiles < FILE_CONSTRAINTS.maxFiles) {
                                        uploadTrigger.classList.remove('hidden');
                                    }
                                }
                            };

                            previewBox.appendChild(link);
                            previewBox.appendChild(removeBtn);
                            container.insertBefore(previewBox, uploadTrigger);

                            // Fancybox 초기화
                            Fancybox.bind('[data-fancybox="gallery"]', {
                                groupAll: true
                            });
                        }

                        // 페이지 로드 시 기존 이미지 로드
                        $(document).ready(function() {
                            <?php if($_GET['act'] == "update") { ?>
                            var ct_idx = '<?php echo $_GET["ct_idx"] ?? ""; ?>';
                            if(ct_idx) {
                                loadImages(ct_idx);
                            }
                            <?php } ?>
                        });

                    </script>


                    <script type="application/javascript">

                        // 폼 검증 및 제출 처리
                        $(document).ready(function() {
                            // 폼 검증 및 제출
                            $("#frm_form").validate({
                                submitHandler: function(form) {
                                    const formData = new FormData(form);

                                    // FilePond 파일들을 FormData에 추가
                                    const files = pond.getFiles();
                                    files.forEach((fileItem, index) => {
                                        if (index < 2) {
                                            formData.append(`ct_img${index + 1}`, fileItem.file);
                                        }
                                    });

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
                                            if(response.success) {
                                                alert('처리되었습니다.');
                                                location.href = './category_upjong_list.php';
                                            } else {
                                                alert(response.message || '처리 중 오류가 발생했습니다.');
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