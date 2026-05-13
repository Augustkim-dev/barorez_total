<?
$_SUB_HEAD_TITLE = "회원 탈퇴"; //헤더에 타이틀명이 없을경우 공백
$_GET['hd_num'] = '2'; //모바일 hd 1~n까지 있음
$_GET['bt_menu'] = ''; //모바일 하단메뉴 있음1, 없음 공백
include_once("./inc/head.php");
?>


<div class="wrap">
    <div class="sub_pg ">
        <div class="container">
            <div class="bg-light   rounded px_20 py_20">
                <p class="tit_st3">탈퇴 전 확인하세요!</p>
                <div class="mt-3 line_h1_5">
                    계정삭제, 리뷰삭제, 탈퇴처리 방침 내용 노출됩니다. 계정삭제, 리뷰삭제, 탈퇴처리 방침 내용 노출됩니다.
                    계정삭제, 리뷰삭제, 탈퇴처리 방침 내용 노출됩니다. 계정삭제, 리뷰삭제, 탈퇴처리 방침 내용 노출됩니다.
                    계정삭제, 리뷰삭제, 탈퇴처리 방침 내용 노출됩니다. 계정삭제, 리뷰삭제, 탈퇴처리 방침 내용 노출됩니다.
                </div>
            </div>
            <div class="checks mt-4">
                <label>
                    <input type="checkbox" name="chk1">
                    <span class="ic_box"></span>
                    <div class="chk_p">
                        <p>탈퇴처리방침 동의합니다.</p>
                    </div>
                </label>
            </div>
        </div>

    </div>
    <div class="bottom_btn bg-white">
        <div class="form-row">
            <div class="col-12"><button type="button" class="btn btn-primary btn-block btn-lg" data-toggle="modal" data-target="#pop_secede">탈퇴하기</button></div>
        </div>
    </div>
</div>
<!-- 탈퇴 팝업-->
<div class="modal fade" id="pop_secede" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-body mt-5">
                <div class="no_data  ">
                    <img src="./img/img_mark.png">
                    <p class="   line_h1_4 mt-3 fs_18 fw_600">탈퇴 시 서비스 이용이 불가합니다.<br>
                        탈퇴 하시겠습니까?</p>
                </div>
            </div>
            <div class="modal-footer">
                <div class="form-row justify-content-end">
                    <div class="col-4"><button type="button" class="btn btn-outline-light btn-block  " data-dismiss="modal">취소</button></div>
                    <div class="col-8"><button type="button" class="btn btn-primary btn-block" data-dismiss="modal">확인</button></div>
                </div>
            </div>
        </div>
    </div>
</div>

<? include_once("./inc/tail.php"); ?>