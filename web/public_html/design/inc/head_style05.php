<div class="hd_m align-items-center justify-content-between">
	<div class=" "><button class="hd_btn btn2" type="button" onclick="history.back()"><img src="./img/ic_back.png" alt="뒤로가기"></button></div><!-- 이전 결과값이 아닌 이전페이지로 이동되어야 합니다. -->
	<div class="page_tit  "><?= $_SUB_HEAD_TITLE ?></div>
	<div class="dropdown item">
		<button class="btn2  dropdown-toggle down" type="button" data-toggle="dropdown" aria-expanded="false">
			김이름
		</button>
		<div class="dropdown-menu dropdown-menu-right">
			<a class="dropdown-item " href="./mypage.php">마이페이지</a>
			<a class="dropdown-item" href="./order_history.php">주문내역</a>
			<a class="dropdown-item" href="#">로그아웃</a>
		</div>
	</div>
</div>