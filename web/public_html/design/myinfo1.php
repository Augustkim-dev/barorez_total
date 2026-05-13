<?
$_SUB_HEAD_TITLE = "비밀번호 입력"; //헤더에 타이틀명이 없을경우 공백
$_GET['hd_num'] = '2'; //모바일 hd 1~n까지 있음
$_GET['bt_menu'] = ''; //모바일 하단메뉴 있음1, 없음 공백
include_once("./inc/head.php");
?>


<div class="wrap">
    <div class="sub_pg pb_lg">
        <div class="container">
            <p class="tit_st3 mt-5">현재 비밀번호를 입력해주세요.</p>
			<form>
				<div class="form_wr mt-5 ip_invalid">
						<div class="ip_tit required">
							<h5>비밀번호</h5>
						</div>
						<div class="form-row">
							<div class="col">
								<input type="password" class="form-control" placeholder="비밀번호  입력(영소문, 숫자 포함 8~16자)">
							</div>
						</div>
						<div class="form-text ip_invalid">비밀번호가 확인되지 않습니다.</div>
					</div>

				

					<div class="bottom_btn  ">
						<div class="form-row">
							<div class="col-12"><button type="button" class="btn btn-primary btn-block  btn-lg" onclick="location.href='./myinfo2.php'">확인</button></div>
						</div>
					</div>
			</form>
        </div>

    </div>
</div>


<? include_once("./inc/tail.php"); ?>