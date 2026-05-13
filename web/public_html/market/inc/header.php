<?php
$mb_idx = $_SESSION['mng']['mt_idx'] ?? 0;
$mb_level = $_SESSION['mng']['mt_level'];
if ($mb_level === 5) {
    $DB->where('mb_idx', $mb_idx);
    $DB->where('del_date', null, 'IS');
    $shop_show = $DB->getOne('shop_t', 'sh_show');
}else{
    echo "<script>location.replace('" . MARKET_HTTP . "/logout.php');</script>";
}

?>
<?php if ($hd_pc === 'logout') { ?>
	<!-- 로그아웃상태일때-->
	<div class="hd_pc ">
		<div class="container-fluid">

			<div class="d-flex">
				<a class=" " href="<?=MARKET_HTTP?>/login.php">
					<img src="<?=DESIGN_HTTP?>/market/img/logo2.svg" alt="홈으로 이동">
				</a>
			</div>
			<div class="d-flex align-items-center">
				<a href="" class="login_btn">로그인 가기</a>
			</div>

		</div>
	</div>

<? } else { ?>

	<!-- PC 헤더 -->
	<div class="hd_pc ">
		<div class="container-fluid">
			<div class="d-flex">
				<div class="hd_menu_btn mr-3"><a href="#menu"></a></div>
				<a class="logo" href="<?=MARKET_HTTP?>">
					<img src="<?=DESIGN_HTTP?>/market/img/logo.svg" alt="홈으로 이동">
				</a>

			</div>
			<div class="d-flex align-items-center">
				<p class="mr-5" id="currentTimeText"><?=date('n월 j일(D) H:i')?></p>
				<div class="custom-control custom-switch switch-outside">
					<input type="checkbox"
						class="custom-control-input"
						id="customSwitch1"
						data-on="영업중"
						data-off="영업마감"
                        <?=$shop_show['sh_show']=="Y" ? "checked" : ""?>
                    >
					<span class="switch-state"></span>
					<label class="custom-control-label" for="customSwitch1"></label>
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
					<a class="close_btn" href="#menu"><img src="<?=DESIGN_HTTP?>/market/img/ic_close.png"></a>
				</div>

				<div class="m_nav_menu  ">

					<ul class="nav-menu">
						<!-- 1차 : 바로 링크 -->
						<li class="nav-item">
							<button type="button" class="nav-link <?= ($hd_num === 'store') ? 'active' : '' ?>" onclick="location.href='<?=MARKET_HTTP?>/store'">
								<p><img src="<?=DESIGN_HTTP?>/market/img/lm_1.png" alt="" class="nav-item-ico"> 매장관리</p>
							</button>
						</li>
						<li class="nav-item">
							<button type="button" class="nav-link <?= ($hd_num === 'menu') ? 'active' : '' ?>" onclick="location.href='<?=MARKET_HTTP?>/menu'">
								<p><img src="<?=DESIGN_HTTP?>/market/img/lm_2.png" alt="" class="nav-item-ico"> 메뉴관리</p>
							</button>
						</li>

						<!-- 1차 : 2차 있음 -->
						<li class="nav-item has-sub <?= ($hd_num === 'revenue') ? 'is-open' : '' ?>">
							<button type="button" class="nav-link nav-toggle <?= ($hd_num === 'revenue') ? 'active' : '' ?>">
								<p><img src="<?=DESIGN_HTTP?>/market/img/lm_3.png" alt="" class="nav-item-ico"> 매출관리</p>
								<img src="<?=DESIGN_HTTP?>/market/img/ico_polygon.png" alt="" class="arrow">
							</button>
							<ul class="sub-menu">
								<li><a href="<?=MARKET_HTTP?>/sales" class="nav-link <?= ($hd_num2 === 'revenue1') ? 'active' : '' ?>">정산관리</a></li>
								<li><a href="<?=MARKET_HTTP?>/statistics" class="nav-link <?= ($hd_num2 === 'revenue2') ? 'active' : '' ?>">통계관리</a></li>
							</ul>
						</li>

						<li class="nav-item has-sub <?= ($hd_num === 'setting') ? 'is-open' : '' ?>">
							<button type="button" class="nav-link nav-toggle <?= ($hd_num === 'setting') ? 'active' : '' ?>">
								<p><img src="<?=DESIGN_HTTP?>/market/img/lm_4.png" alt="" class="nav-item-ico"> 설정</p>
								<img src="<?=DESIGN_HTTP?>/market/img/ico_polygon.png" alt="" class="arrow">
							</button>
							<ul class="sub-menu">
								<li><a href="<?=MARKET_HTTP?>/myinfo" class="nav-link <?= ($hd_num2 === 'setting1') ? 'active' : '' ?>">내정보수정</a></li>
								<li><a href="<?=MARKET_HTTP?>/logout.php" class="nav-link ">로그아웃</a></li>
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
    console.log('[time] header script loaded');

    (function () {
        var el = document.getElementById('currentTimeText');
        if (!el) {
            console.log('[time] currentTimeText not found -> skip');
            return;
        }

        // ✅ 서버시간 기준(권장): 최초 1회만 서버 timestamp를 박아둠
        var serverTs = <?= time() ?>;
        var clientStart = Date.now();
        var dayK = ['일','월','화','수','목','금','토'];

        function pad2(n){ return String(n).padStart(2,'0'); }

        function getNowByServerBase(){
            var elapsedSec = Math.floor((Date.now() - clientStart) / 1000);
            return new Date((serverTs + elapsedSec) * 1000);
        }

        function formatKorean(dt){
            var m = dt.getMonth() + 1;
            var d = dt.getDate();
            var w = dayK[dt.getDay()];
            var hh = pad2(dt.getHours());
            var mm = pad2(dt.getMinutes());
            return m + '월 ' + d + '일(' + w + ') ' + hh + ':' + mm;
        }

        function renderTime(){
            var dt = getNowByServerBase();
            var text = formatKorean(dt);
            console.log('[time] render:', text);
            el.textContent = text;
        }

        // ✅ 중복 실행 방지(헤더가 여러번 include될 가능성 대비)
        if (window.__qr_time_tick_started) {
            console.log('[time] already started -> skip');
            return;
        }
        window.__qr_time_tick_started = true;

        // 1) 즉시 출력
        renderTime();

        // 2) 다음 분(00초)에 맞춰 시작
        var dt = getNowByServerBase();
        var msToNextMinute = (60 - dt.getSeconds()) * 1000 - dt.getMilliseconds();

        console.log('[time] msToNextMinute:', msToNextMinute);

        setTimeout(function(){
            renderTime();
            setInterval(renderTime, 60000);
        }, msToNextMinute);
    })();
</script>
<script>
    $(function () {
        console.log('[shop-open] toggle init');

        $(document).on('change', '#customSwitch1', function () {
            var $toggle = $(this);

            var sh_idx = <?=$_SESSION['current_sh_idx']?>;
            var nextVal = $toggle.is(':checked') ? 'Y' : 'N';
            var prevChecked = ! $toggle.is(':checked'); // 실패 시 되돌리기용

            console.log('[shop-open] change', { sh_idx: sh_idx, nextVal: nextVal });

            if (!sh_idx) {
                console.log('[shop-open] no shop selected');
                alert('매장을 먼저 선택해 주세요.');
                // 원복
                $toggle.prop('checked', prevChecked);
                return;
            }

            // 중복 클릭 방지
            $toggle.prop('disabled', true);

            $.ajax({
                url: '<?=MARKET_HTTP?>/shop_open_update.php', // ✅ 새로 만들 파일
                type: 'POST',
                dataType: 'json',
                data: {
                    act: 'set_open',        // ✅ 서버 분기용
                    sh_idx: sh_idx,
                    sh_open: nextVal        // ✅ 'Y' or 'N'
                },
                success: function (res) {
                    console.log('[shop-open] success:', res);

                    if (res && res.success) {
                        // value도 동기화(기존 코드가 value를 쓰는 경우 대비)
                        $toggle.val(nextVal);

                        ModalUtil.alert({
                            title: '알림',
                            message: res.message,
                            okText: '확인',
                            size: 'sm',
                        });
                    } else {
                        ModalUtil.alert({
                            title: '알림',
                            message: res.message,
                            okText: '확인',
                            size: 'sm',
                        });
                        // 원복
                        $toggle.prop('checked', prevChecked);
                    }
                },
                error: function (xhr, status, error) {
                    console.log('[shop-open] error status:', status);
                    console.log('[shop-open] error:', error);
                    console.log('[shop-open] responseText:', xhr.responseText);
                    // 원복
                    $toggle.prop('checked', prevChecked);
                },
                complete: function () {
                    $toggle.prop('disabled', false);
                }
            });
        });

    });
</script>
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
