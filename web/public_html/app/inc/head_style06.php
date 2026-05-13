
<div class="hd_m align-items-center justify-content-between">
	<div class=" "><button class="hd_btn btn2" type="button" onclick="history.back()"><img src="<?=DESIGN_HTTP?>/img/ic_back.png" alt="뒤로가기"></button></div><!-- 이전 결과값이 아닌 이전페이지로 이동되어야 합니다. -->
	<div class="page_tit  "><?= $_SUB_HEAD_TITLE ?></div>
	<div class=" "><button class="hd_btn btn2" type="button" onclick="location.href='<?=$_SESSION['qr_token'] ? APP_PAGE : MAP_PAGE?>' "><img src="<?=DESIGN_HTTP?>/img/ico_home.png" alt="홈으로"></button></div>
</div>
