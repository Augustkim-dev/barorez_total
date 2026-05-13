<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu='9';
$chk_sub_menu='3';
include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.menu.inc.php";
//include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.sidebar.inc.php";

$chk_ckeditor = "Y";
$file_count = 3;   // 원하는 첨부파일 갯수로 변경 가능
$board = "notice"; // 파일 업로드

if ($_GET['act'] == "update") {
    $DB->join($CFG_TBL['member']['default']." a2", "a1.mt_idx = a2.idx", "LEFT");
    $DB->join($CFG_TBL['board']['default']." a3", "a1.mt_idx = a3.idx", "LEFT");
    $DB->where('a1.idx', $_GET['nt_idx']);
    $row = $DB->getone($CFG_TBL['board']['comment'].' a1', '*, a1.idx as nt_idx, a3.idx as board_id');

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
            <h1 class="title">댓글 관리</h1>
            <p class="caption">
                댓글 상세 내용을 확인 할 수 있습니다.
            </p>
        </div>
        <nav aria-label="breadcrumb" role="navigation">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="#">게시판관리</a></li>
                <li class="breadcrumb-item active">댓글</li>
            </ol>
        </nav>
    </div>
    <!-- //END PAGE HEADING -->
    <div class="container-fluid">
        <div class="card margin-bottom-0">
            <div class="card-body">
                <div class="card-body">

                    <div class="form-group row align-items-center">
                        <label for="mt_pwd" class="col-sm-2 col-form-label">작성자</label>
                        <div class="col-sm-4">
                            <span><?=$row['mt_name']?></span>
                        </div>
                        <label for="mt_hp" class="col-sm-2 col-form-label">등록일시</label>
                        <div class="col-sm-4 col-form-label">
                            <span><?=DateType($row['cmt_wdate'], 6)?></span>
                        </div>
                    </div>
                    <div class="form-group row align-items-center">
                        <label for="mt_pwd" class="col-sm-2 col-form-label">게시글</label>
                        <div class="col-sm-4">
                            <a href="../../board_detail.php?id=<?=$row['board_id']?>" ><span><?=$row['nt_title']?></span></a>
                        </div>
                        <label for="mt_hp" class="col-sm-2 col-form-label">회원 유형</label>
                        <div class="col-sm-4 col-form-label">
                            <span><?=$row['mt_position']?></span>
                        </div>
                    </div>
                    <div class="form-group row align-items-center">
                        <label for="mt_pwd" class="col-sm-2 col-form-label">댓글 내용</label>
                        <div class="col-sm-10">
                            <span><?=$row['cmt_content']?></span>
                        </div>
                    </div>

                    <div class="form-group row justify-content-center margin-top-30">
                        <button type="button"  onclick="location.href='./list.php'" class="btn btn-outline-secondary mx-1" >목록</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<!-- //END PAGE CONTENT CONTAINER -->
<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/foot.inc.php";
?>
