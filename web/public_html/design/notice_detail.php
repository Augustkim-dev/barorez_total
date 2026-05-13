<?
$_SUB_HEAD_TITLE = "공지사항"; //헤더에 타이틀명이 없을경우 공백
$_GET['hd_num'] = '2'; //모바일 hd 1~n까지 있음
$_GET['bt_menu'] = ''; //모바일 하단메뉴 있음1, 없음 공백
include_once("./inc/head.php");
?>


<div class="wrap">
	<div class="sub_pg   ">
		<div class="container">
			<div class=" ">
				<div class="bo_tit py_20 border-bottom">
					<div class="tit_st3">공지 제목입니다. 공지목입니다.공지 제목입니다. 공지목입니다.공지 제목입니다. 공지목입니다. </div>
					<div class="tg_400 fs_13 mt-2">2023.12.01</div>
				</div>
				<div class="bo_body py_20">
					내용이 나옴
				</div>
			</div>

		</div>
	</div>
</div>




<? include_once("./inc/tail.php"); ?>