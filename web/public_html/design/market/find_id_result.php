<?
include $_SERVER['DOCUMENT_ROOT']."/lib.inc.php";
$_SUB_HEAD_TITLE = "아이디 찾기";
$_GET['hd_pc'] = '';//PC hd 메뉴있음1, 메뉴없음 공백
$_GET['hd_num'] = '5';//모바일 hd 1~n까지 있음
$_GET['bt_menu'] = '';//모바일 하단메뉴 있음1, ft 없음 공백
$_GET['ft_none'] = '';//모바일 ft 있음1, ft 없음 공백
include_once("./inc/head.php");
?>

<div class="wrap">
    <div class="sub_pg">
		<div class="sign_pg">
			<div class="sign_wr container">
				<div class="tit_h2 mb-5">회원님의 아이디는 <br><span class="text-primary"><?= $_GET['mt_id'] ?></span>입니다.</div>
				<div class="rounded border px_20 py_20">
					<ul class="list_style_1">
						<li>
							<span class="">아이디</span>
							<div class="text_dynamic"><?= $_GET['mt_id'] ?></div>
						</li>
						<li class="pb-0">
							<span class="">가입일</span>
							<div class="text_dynamic"><?= DateType($_GET['mt_wdate'], 4) ?></div>
						</li>
					</ul>
				</div>
				
				<button type="button" class="btn btn-primary btn-block mt-5" onclick="location.href='./login.php'">로그인</button>
				<button type="button" class="btn btn-outline-light btn-block mt-3" onclick="location.href='./find_pw.php'">비밀번호 찾기</button>
			</div>
		</div>
	</div>
</div>




<? include_once("./inc/tail.php"); ?>
