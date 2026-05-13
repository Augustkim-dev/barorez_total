<!-- PC 헤더 -->
<div class="hd_pc d-none d-lg-block">
	<div class="container">
		<a class="logo" href="./index.php">
			<img src="./img/logo.svg" alt="홈으로 이동">
			<div class="log_txt">
				<span class="fw_600">사장님</span>
				<span>페이지</span>
			</div>
		</a>

		<?php if ($_GET['hd_pc'] == '1') {?>
		<div class="d-flex align-items-center">
			<div class="nav_menu d-none d-lg-block ml-5">
				<ul class="nav_ul">
					<li class="nav_li"><a class="nav_a" href="./index.php">홈</a></li>
					<li class="nav_li"><a class="nav_a" href="./product_list.php">상품관리</a></li>
					<li class="nav_li"><a class="nav_a" href="./order_list01.php">주문관리</a></li>
					<li class="nav_li"><a class="nav_a" href="./settle_list.php">정산내역</a></li>
					<li class="nav_li"><a class="nav_a" href="./shop_info01.php">내 상점</a></li>
				</ul>
			</div>
			<div class="hd_menu_btn"><a href="#menu"></a></div>
		</div>
		<?}else{?>
		<?}?>

	</div>
</div>

<!-- 전체메뉴 -->
<div class="m_menu_wr">
	<div class="m_nav">
		<nav class="nav_wr">
			<div class="menu_hd">
				<div class="fs_18 fw_700">전체메뉴</div>
				<a class="close_btn" href="#menu"><img src="./img/ic_close.png"></a>
			</div>
			<div class="bg-light container-md pt-5 pb_20">
				<div class="d-flex justify-content-between align-items-center">
					<div>
						<div class="fs_22 fw_700 mb-2">팜스코님 환영합니다.</div>
						<button type="button" class="btn btn-outline-light btn-sm border-0 pr-2" data-toggle="modal" data-target="#modal_logout">
							<span class="mr-2">로그아웃</span>
							<img src="./img/ic_more.png" style="width:1.2rem;">
						</button>
					</div>
					<div class="rect rounded-circle" style="width:4.6rem;"><img src="./img/no_profile.png"></div>
				</div>
			</div>
			<div class="m_nav_menu container-md fs_16 fw_600 mt-4 mt-md-5">
				<ul class="px-sm-3 py_8">
					<li>
						<div class="menu_tit fs_15 mb_8">주문</div>
						<ul class="menu_link">
							<li><a href="#"><div class="line1_text">상점정보수정</div><img src="./img/ic_more.png" style="width:1.9rem;"></a></li>
							<li><a href="#"><div class="line1_text">주문관리</div><img src="./img/ic_more.png" style="width:1.9rem;"></a></li>
							<li><a href="#"><div class="line1_text">택배주문관리</div><img src="./img/ic_more.png" style="width:1.9rem;"></a></li>
							<li><a href="#"><div class="line1_text">리뷰내역</div><img src="./img/ic_more.png" style="width:1.9rem;"></a></li>
							<li><a href="#"><div class="line1_text">상품카테고리관리</div><img src="./img/ic_more.png" style="width:1.9rem;"></a></li>
							<li><a href="#"><div class="line1_text">상품관리</div><img src="./img/ic_more.png" style="width:1.9rem;"></a></li>
							<li><a href="#"><div class="line1_text">택배상품관리</div><img src="./img/ic_more.png" style="width:1.9rem;"></a></li>
							<li><a href="#"><div class="line1_text">정산내역</div><img src="./img/ic_more.png" style="width:1.9rem;"></a></li>
						</ul>
					</li>
					<li>
						<div class="menu_tit fs_15 mb_8">설정</div>
						<ul class="menu_link">
							<li><a href="#"><div class="line1_text">설정</div><img src="./img/ic_more.png" style="width:1.9rem;"></a></li>
							<li><a href="#"><div class="line1_text">광고메세지승인</div><img src="./img/ic_more.png" style="width:1.9rem;"></a></li>
							<li><a href="#"><div class="line1_text">직원계정생성</div><img src="./img/ic_more.png" style="width:1.9rem;"></a></li>
							<li><a href="#"><div class="line1_text">회원정보수정</div><img src="./img/ic_more.png" style="width:1.9rem;"></a></li>
						</ul>
					</li>
					<li>
						<div class="menu_tit fs_15 mb_8">고객지원</div>
						<ul class="menu_link">
							<li><a href="#"><div class="line1_text">고객센터</div><img src="./img/ic_more.png" style="width:1.9rem;"></a></li>
							<li><a href="#"><div class="line1_text">공지사항</div><img src="./img/ic_more.png" style="width:1.9rem;"></a></li>
						</ul>
					</li>
				</ul>
			</div>
		</nav>
	</div>
	<div class="menu_bg"></div>
</div>

<script>
// 모바일 메뉴 열기/닫기
$('.hd_menu_btn, .menu_hd .close_btn, .menu_bg').on('click', function(){
    $('body').toggleClass('menu_on');
});
</script>