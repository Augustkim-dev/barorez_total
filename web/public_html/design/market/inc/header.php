<?php if ($_GET['hd_pc'] == 'logout') { ?>
	<!-- 로그아웃상태일때-->
	<div class="hd_pc ">
		<div class="container-fluid">
			
			<div class="d-flex">
				<a class=" " href="./index.php">
					<img src="./img/logo2.svg" alt="홈으로 이동">
				</a>
			</div>
			<div class="d-flex align-items-center">
				<a href="./login.php" class="login_btn">로그인 가기</a>
			</div>

		</div>
	</div>

<? } else { ?>

	<!-- PC 헤더 -->
	<div class="hd_pc ">
		<div class="container-fluid">
			<div class="d-flex">
				<div class="hd_menu_btn mr-3"><a href="#menu"></a></div>
				<a class="logo" href="./index.php">
					<img src="./img/logo.svg" alt="홈으로 이동">
				</a>

			</div>
			<div class="d-flex align-items-center">
				<p class="mr-5">12월 22일(수) 13:05</p>
				<div class="custom-control custom-switch switch-outside">
					<input type="checkbox"
						class="custom-control-input"
						id="customSwitch_hd"
						data-on="영업중"
						data-off="영업마감">
					<span class="switch-state"></span>
					<label class="custom-control-label" for="customSwitch_hd"></label>
				</div>
			</div>
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

				<div class="m_nav_menu  ">

					<ul class="nav-menu">
						<!-- 1차 : 바로 링크 -->
						<li class="nav-item">
							<button type="button" class="nav-link <?= ($hd_num === 'store') ? 'active' : '' ?>" onclick="location.href='./store.php'">
								<p><img src="./img/lm_1.png" alt="" class="nav-item-ico"> 매장관리</p>
							</button>
						</li>
						<li class="nav-item">
							<button type="button" class="nav-link <?= ($hd_num === 'menu') ? 'active' : '' ?>" onclick="location.href='./menu.php'">
								<p><img src="./img/lm_2.png" alt="" class="nav-item-ico"> 메뉴관리</p>
							</button>
						</li>

						<!-- 1차 : 2차 있음 -->
						<li class="nav-item has-sub <?= ($hd_num === 'revenue') ? 'is-open' : '' ?>">
							<button type="button" class="nav-link nav-toggle <?= ($hd_num === 'revenue') ? 'active' : '' ?>">
								<p><img src="./img/lm_3.png" alt="" class="nav-item-ico"> 매출관리</p>
								<img src="./img/ico_polygon.png" alt="" class="arrow">
							</button>
							<ul class="sub-menu">
								<li><a href="./sales.php" class="nav-link <?= ($hd_num2 === 'revenue1') ? 'active' : '' ?>">정산관리</a></li>
								<li><a href="./statistics.php" class="nav-link <?= ($hd_num2 === 'revenue2') ? 'active' : '' ?>">통계관리</a></li>
							</ul>
						</li>

						<li class="nav-item has-sub <?= ($hd_num === 'setting') ? 'is-open' : '' ?>">
							<button type="button" class="nav-link nav-toggle <?= ($hd_num === 'setting') ? 'active' : '' ?>">
								<p><img src="./img/lm_4.png" alt="" class="nav-item-ico"> 설정</p>
								<img src="./img/ico_polygon.png" alt="" class="arrow">

							</button>
							<ul class="sub-menu">
								<li><a href="./myinfo1.php" class="nav-link <?= ($hd_num2 === 'setting1') ? 'active' : '' ?>">내정보수정</a></li>
								<li><a href=" " class="nav-link ">로그아웃</a></li>
							</ul>
						</li>


					</ul>

				</div>
			</nav>
		</div>
		<div class="menu_bg"></div>
	</div>
<? } ?>



<script>
	// 모바일 메뉴 열기/닫기
	$('.hd_menu_btn, .menu_hd .close_btn, .menu_bg').on('click', function() {
		$('body').toggleClass('menu_on');
	});

	//왼쪽열리는 메뉴
	function initNavToggle(root) {
		root = root || document;

		root.querySelectorAll('.nav-item.has-sub .nav-toggle').forEach(function(btn) {
			if (btn.dataset.init === '1') return;
			btn.dataset.init = '1';

			btn.addEventListener('click', function() {
				const item = btn.closest('.nav-item');
				item.classList.toggle('is-open');
			});
		});
	}



	// 페이지 로드 후 실행
	initNavToggle();
</script>