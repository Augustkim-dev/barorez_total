<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu='7';
$chk_sub_menu='2';
include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.menu.inc.php";
//include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.sidebar.inc.php";


//$chk_ckeditor = "Y";

if ($_GET['act'] == "view") {
    $DB->where('jt_idx', $_GET['nt_idx']);
    $row = $DB->getone($CFG_TBL['join']['default'], '*, jt_idx as nt_idx');


    $my = get_mem_info('idx', $row['mt_idx']);
    $_act = "view";
    $_act_txt = " 보기";

    $isDsiabled = "disabled";

} else {
    $_act = "input";
    $_act_txt = " 등록";
}



?>
    <!-- PAGE CONTENT CONTAINER -->
    <div class="content" id="content">
        <!-- PAGE HEADING -->
      <?php include_once "./pheading.php";?>
        <!-- //END PAGE HEADING -->
        <div class="container-fluid">
            <div class="card margin-bottom-0">
                <div class="card-body">

                    <div class="card-header">
                      <ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
                        <li class="nav-item"><a class="nav-link active" id="step-tab-1" data-toggle="tab" href="#step-1" role="tab" aria-controls="home" aria-selected="true">기본</a></li>
                      </ul>
                    </div>


                    <form method="post" name="frm_form" id="frm_form" action="./update.php" target="hidden_ifrm" enctype="multipart/form-data">
                        <input type="hidden" name="act" id="act" value="<?=$_act?>" />
                        <input type="hidden" name="nt_idx" id="nt_idx" value="<?=$row['nt_idx']?>" />
                        <div class="tab-content margin-top-15">
                          <div class="tab-pane fade show active" id="step-1" role="tabpanel" aria-labelledby="step-tab-1">
                            <h4 id="rw-fe-basic">조인정보 <?=$_act_txt?></h4>


                            <div class="form-group row margin-top-30">
                              <label for="gmt_golf_name" class="col-sm-2 col-form-label">골프장명</label>
                              <div class="col-sm-10">
                                <input type="text" name="gmt_golf_name" id="gmt_golf_name" value="<?=$row['gmt_golf_name']?>" class="form-control" <?=$isDsiabled?> />
                              </div>
                            </div>

                            <div class="form-group row margin-top-30">
                              <label for="mt_id" class="col-sm-2 col-form-label">아이디</label>
                              <div class="col-sm-4">
                                <input type="text" name="mt_id" id="mt_id" value="<?=$row['mt_id']?>" class="form-control" <?=$isDsiabled?> />
                              </div>
                              <label for="mt_name" class="col-sm-2 col-form-label">이름</label>
                              <div class="col-sm-4">
                                <input type="text" name="mt_name" id="mt_name" value="<?=$row['mt_name']?>" class="form-control" <?=$isDsiabled?> />
                              </div>
                            </div>

                            <div class="form-group row margin-top-30">
                              <label for="jt_jdate" class="col-sm-2 col-form-label">초청일시</label>
                              <div class="col-sm-4">
                                <input type="text" name="jt_jdate" id="jt_jdate" value="<?=DateType($row['jt_jdate'], 4)?>" class="form-control" <?=$isDsiabled?> />
                              </div>
                              <label for="jt_person" class="col-sm-2 col-form-label">초청인원</label>
                              <div class="col-sm-4">
                                <input type="text" name="jt_person" id="jt_person" value="<?=$row['jt_person']?>" class="form-control" <?=$isDsiabled?> />
                              </div>
                            </div>

                            <div class="form-group row margin-top-30">
                              <label for="jt_gender" class="col-sm-2 col-form-label">초청성별</label>
                              <div class="col-sm-4">
                                <input type="text" name="jt_gender" id="jt_gender" value="<?=$row['jt_gender']=='1'?'남성':'여성'?>" class="form-control" <?=$isDsiabled?> />
                              </div>
                              <label for="jt_wdate" class="col-sm-2 col-form-label">등록일</label>
                              <div class="col-sm-4">
                                <input type="text" name="jt_wdate" id="jt_wdate" value="<?=DateType($row['jt_wdate'], 4)?>" class="form-control" <?=$isDsiabled?> />
                              </div>
                            </div>


                            <div class="form-group row">
                              <label for="jt_content" class="col-sm-2 col-form-label">제목</label>
                              <div class="col-sm-10">
                                <input type="text" name="jt_content" id="jt_content" value="<?=$row['jt_content']?>" class="form-control" <?=$isDsiabled?> />
                              </div>
                            </div>




                            <div class="form-group row justify-content-center margin-top-30">
                                <button type="button"  onclick="history.go(-1);" class="btn btn-outline-secondary mx-1" >목록</button>
                                <!--<button type="submit" class="btn btn-secondary" >확인</button>-->
                              <input type="button" class="btn btn-outline-danger" value="삭제" onclick="f_post_del('./update.php', '<?=$row['nt_idx']?>');" />
                            </div>
                          </div>


                        </div>

                    </form>



                    <script>




                        $(document).ready(function() {

                            $('#mt_idx').select2({
                                placeholder: "선택하세요",
                                allowClear: true
                            });




                            // FileUploader 초기화
                            const uploader = createFileUploader({
                                container: '.upload-container',
                                trigger: '#uploadTrigger',
                                filepondElement: '.filepond',
                                maxFiles: 5,
                                maxFileSize: '5MB',
                                allowedFileTypes: ['image/jpeg', 'image/png', 'image/gif'],
                                imageMinWidth: 100,
                                imageMinHeight: 100,
                                imageMaxWidth: 4000,
                                imageMaxHeight: 4000,
                                ajaxUrl: './update.php'
                            });


                            // 폼 검증 및 제출 핸들러
                            const formHandler = {
                                init() {
                                    this.initializeValidation();
                                    this.setInitialValues();
                                },

                                initializeValidation() {
                                    $("#frm_form").validate({
                                        submitHandler: this.handleSubmit,
                                        rules: {
                                            mt_idx: {
                                                required: true
                                            },
                                            pt_idx: {
                                                required: true
                                            },
                                            rt_content: {
                                                required: true,
                                                maxlength:200,
                                            },
                                            rt_score: {
                                                required: true
                                            },
                                            rt_place: {
                                                required: true,
                                                maxlength:20,
                                            },
                                            rt_price: {
                                                required: true,
                                                number: true
                                            },
                                            rt_temp: {
                                                required: true,
                                                digits: true,
                                                min: 0,
                                                max: 99
                                            },
                                            rt_repurchase: {
                                                required: true,
                                            },
                                            rt_color: {
                                                required: true
                                            },
                                            rt_flavor_check: {
                                                required: true,
                                                min: 1,
                                                max:3
                                            },
                                            rt_taste_intensity: {
                                                required: true,
                                            },
                                            rt_taste_acidity: {
                                                required: true,
                                            },
                                            rt_taste_sweetness: {
                                                required: true,
                                            },
                                            rt_taste_tannin: {
                                                required: true,
                                            },
                                            pairing_check: {
                                                required: true,
                                                min: 1,
                                                max:3
                                            },
                                        },

                                        ignore: function(index, element) {
                                            return $(element).is(":hidden") && !$(element).hasClass("always-validate");
                                        },
                                        errorElement: 'span',
                                        errorPlacement: (error, element) => {
                                            error.addClass('invalid-feedback');
                                            if(element.attr('name') === 'pairing_check'){
                                                app.toastr.showError('페어링은 최소1개이상 입력하세요.');
                                            } else {
                                                element.closest('.form-validate').append(error);
                                            }

                                        },
                                        highlight: (element) => $(element).addClass('is-invalid'),
                                        unhighlight: (element) => $(element).removeClass('is-invalid')
                                    });
                                },
                                handleSubmit(form) {



                                    const formData = new FormData(form);

                                    formData.append('maxFiles', uploader.options.maxFiles);

                                    // 이미지 순서 정보 추가
                                    const imageOrder = uploader.getImageOrder();
                                    formData.append('image_order', JSON.stringify(imageOrder));

                                    // FilePond 파일들을 FormData에 추가
                                    const files = uploader.getPond().getFiles();

                                    const findFileById = (id) => {
                                        const found = files.find(f => f.id === id);
                                        return found ? found.file : null;
                                    };

                                    // console.log(imageOrder)
                                    // console.log(files)
                                    imageOrder.forEach((img, index) => {
                                        if (img.type === 'new') {
                                            const fileObj = findFileById(img.id);
                                            if (fileObj) {
                                                console.log(fileObj)
                                                formData.append(`rt_img${index + 1}`, fileObj);
                                            }
                                        }
                                    });

                                    // 삭제된 파일 정보 전송
                                    const removedFiles = uploader.getRemovedFiles(); // 삭제된 파일 번호 배열
                                    formData.append('removed_files', JSON.stringify(removedFiles));

                                    console.log(...formData)
                                    $.ajax({
                                        url: './update.php',
                                        type: 'POST',
                                        data: formData,
                                        processData: false,
                                        contentType: false,
                                        beforeSend: () => $('#splinner_modal').modal('show'),
                                        success: (response) => {
                                            $('#splinner_modal').modal('hide');
                                            console.log(response)
                                            if(response.success) {
                                                app.toastr.showSuccess(response.message, response.redirect);
                                            } else {
                                                app.toastr.showError(response.message);
                                            }
                                        },
                                        error: (xhr, status, error) => {
                                            $('#splinner_modal').modal('hide');
                                            console.error(error)
                                            app.toastr.showError(response.message);
                                        }
                                    });
                                    return false;
                                },
                                setInitialValues() {

                                }
                            };

                            // 초기화
                            formHandler.init();

                            <? if($row['rt_show']) { ?>
                              $('#rt_show').val('<?=$row['rt_show']?>');
                            <? } ?>



                              const mt_idx = '<?php echo $row["jt_idx"] ?? ""; ?>';
                              if(mt_idx != '') {
                                  uploader.loadImages(mt_idx);
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