<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu='99';
$chk_sub_menu='3';
include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.menu.inc.php";
//include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.sidebar.inc.php";

$chk_ckeditor = "Y";
$DB->where('idx', '1');
$row = $DB->getone('setup_t');
?>
    <!-- PAGE CONTENT CONTAINER -->
    <div class="content" id="content">
        <!-- PAGE HEADING -->
        <div class="page-heading">
            <div class="page-heading__container">
                <div class="icon">
                    <span class="li-register"></span>
                </div>
                <h1 class="title">이용약관 & 개인정보처리방침</h1>
                <p class="caption">
                    이용약관 & 개인정보처리방침 수정 할 수 있습니다.
                </p>
            </div>
            <nav aria-label="breadcrumb" role="navigation">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#">환경설정</a></li>
                    <li class="breadcrumb-item active">이용약관 & 개인정보처리방침</li>
                </ol>
            </nav>
        </div>
        <!-- //END PAGE HEADING -->
        <div class="container-fluid">
            <div class="card margin-bottom-0">
                <div class="card-body">
                    <form method="post" name="frm_form" id="frm_form" action="./agree_update.php" target="hidden_ifrm" enctype="multipart/form-data">
                        <input type="hidden" name="act" id="act" value="update" />
                        <div class="card-body">
                            <h4 id="rw-fe-basic">이용약관 & 개인정보처리방침 수정</h4>
                            <p class="subtitle margin-bottom-20">
                                &nbsp;
                            </p>
                            <div class="form-group row">
                                <label for="nt_content" class="col-sm-2 col-form-label">이용약관</label>
                                <div class="col-sm-10">
                                    <?php
                                    $editor_name = 'st_agree1';
                                    if($chk_ckeditor) {
                                        $editor_upload = 'Y';
                                        include "./inc/ckeditor.php";
                                    }else {
                                        echo "<textarea name='".$editor_name."' id='".$editor_name."' class='form-control' style='height:250px;'>".$row[$editor_name]."</textarea>";
                                    }
                                    ?>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="nt_content" class="col-sm-2 col-form-label">개인정보처리방침</label>
                                <div class="col-sm-10">
                                    <?php
                                    $editor_name = 'st_agree2';
                                    if($chk_ckeditor) {
                                        $editor_upload = 'N';
                                        include "./inc/ckeditor.php";
                                    }else {
                                        echo "<textarea name='".$editor_name."' id='".$editor_name."' class='form-control' style='height:250px;'>".$row[$editor_name]."</textarea>";
                                    }
                                    ?>
                                </div>
                            </div>

                            <div class="form-group row">
                              <label for="nt_content" class="col-sm-2 col-form-label">마케팅정보수집</label>
                              <div class="col-sm-10">
                                <?php
                                $editor_name = 'st_agree3';
                                if($chk_ckeditor) {
                                  $editor_upload = 'Y';
                                  include "./inc/ckeditor.php";
                                }else {
                                  echo "<textarea name='".$editor_name."' id='".$editor_name."' class='form-control' style='height:250px;'>".$row[$editor_name]."</textarea>";
                                }
                                ?>
                              </div>
                            </div>
                            <div class="form-group row justify-content-center margin-top-30">
                                <button type="submit" class="btn btn-secondary" >확인</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
    <!-- //END PAGE CONTENT CONTAINER -->
<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/foot.inc.php";
?>
