<?
$_SUB_HEAD_TITLE = "회원가입 완료";
$_GET['hd_pc'] = 'logout'; //PC hd  로그인시공백 로그아웃시 logout
$hd_num = ' '; //왼쪽메뉴 active 땜시 만듬
include_once("./inc/head.php");
?>


    <div class="sub_pg pl-0 ">
        <div class="join_form_wr">
            <div class="hd_tit">
                <h2 class="tit_st1 d-flex align-items-center"><a href="#" onclick="history.back(); return false;" class="mr-4 line_h0 "><img src="<?=DESIGN_HTTP?>/market/img/ico_back.svg" alt=" 뒤로가기"></a><span>회원가입 완료</span></h2>
            </div>
            <div class="join_form text-center">
                <img src="<?=DESIGN_HTTP?>/market/img/login_img2.svg" alt=" " class="mt-5">
                <h3 class="tit_st1 mt_35">아이디 찾기가 완료되었습니다.</h3>
                <h3 class="tit_st1 mt-3 text-primary"><?=$_SESSION['mt_id']?></h3>
                <div class="d-flex  justify-content-center mt_40 btn_group">
                    <button type="button" class="btn btn-outline-light btn-lg btn-w2"  onclick="sessionResetLink('./find_pw.php')">비밀번호 찾기</button>
                    <button type="button" class="btn btn-primary btn-lg btn-w2"  onclick="sessionResetLink('./login.php')">로그인</button>
                </div>
            </div>
        </div>
    </div>


<script>
    function sessionResetLink(targetUrl){
        $.ajax({
            url: './clear_session.php',         // ✅ 세션 초기화 처리할 엔드포인트 (아래 PHP 예시 참고)
            type: 'POST',
            dataType: 'json',
            success: (res) => {
                console.log('[find_id_complete] clear session success:', res);
                // 성공/실패 상관없이 이동 (요구사항에 맞게 안정적으로)
                location.href = targetUrl;
            },
            error: (xhr) => {
                console.log('[find_id_complete] clear session error:', xhr);
                // 에러여도 이동
                location.href = targetUrl;
            },
        });
    }
</script>


<? include_once("./inc/tail.php"); ?>
