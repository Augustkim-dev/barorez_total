<div class="hd_m align-items-center justify-content-between">
    <a class="d-flex flex-fill align-items-center" href="../map/">
        <img class="flex-shrink-0 logo_img" src="<?=DESIGN_HTTP?>/img/logo.svg">
    </a>
    <div class=" justify-content-end d-flex align-items-center">



         <!--  <a href="" class="badg  green mr-3" onclick="location.href='./.php'">
            주문내역 <span class="ml-1 fw_500">7</span>
        </a> -->
        <?php if ($_SESSION['mng']) {?>
        <div class="dropdown item">
			<button class="btn2  dropdown-toggle down" type="button" data-toggle="dropdown" aria-expanded="false">
                <?=$_SESSION['mng']['mt_name'] ?? '비회원'?>
			</button>
            <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item " href="<?=APP_PAGE?>/my/mypage.php">마이페이지</a>
                <a class="dropdown-item" href="<?=APP_PAGE?>/order/history.php">주문내역</a>
                <a class="dropdown-item" href="#" id="btnLogout">로그아웃</a>
            </div>
		</div>
        <button class="hd_btn ml-3" type="button" onclick="location.href='../order/cart.php'">
            <img src="<?=DESIGN_HTTP?>/img/icon_cart.png" alt="장바구니">
            <span></span>
        </button>
        <?php }else{?>
            <button type="button" class="btn btn-secondary btn-sm rounded-pill mr-2"  id="storeOwnerBtn">가맹점주</button>
		<!-- 로그인버튼-->
            <button type="button" class="btn btn-secondary btn-sm rounded-pill"  onclick="location.href='<?=APP_PAGE?>/auth/login.php' ">회원로그인</button>
        <?php }?>
    </div>
</div>

<script>
    $(function () {
        console.log('헤더 스크립트 초기화');

        $('#storeOwnerBtn').on('click', function (e) {
            console.log('가맹점주 외부 페이지 이동');
            window.open('https://barorez.com/market/login.php', '_blank');
        });

        $('#btnLogout').on('click', function (e) {
            e.preventDefault();
            let url = '<?=AUTH_ACTIONS?>/logout.php';
            $.ajax({
                url: url,  // 🔥 실제 login.php 경로
                type: 'POST',
                dataType: 'json',
                data: {
                    act: 'logout',
                    token: '<?=$_SESSION['app_token']?>'
                },
                beforeSend: function () {
                    console.log('로그아웃 AJAX 전송 시작');
                },
                success: function (res) {
                    // console.log('로그아웃 응답:', res);

                    if (res && res.success) {
                        ModalUtil.alert({
                            title: '알림',
                            message: res.message || '로그아웃되었습니다.',
                            okText: '확인',
                            onOk: function () {
                                if (res.redirect) {
                                    location.href = res.redirect;
                                } else {
                                    location.href = '<?=APP_PAGE?>';
                                }
                            },
                        });
                    } else {
                        ModalUtil.alert({
                            title: '알림',
                            message: res && res.message ? res.message : '로그아웃에 실패했습니다. 잠시 후 다시 시도해 주세요.',
                            okText: '확인',
                            onOk: function () {
                            },
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error('로그아웃 AJAX 오류:', status, error);
                    console.log('서버 원본 응답:', xhr.responseText);
                    alert('로그아웃 처리 중 오류가 발생했습니다. 잠시 후 다시 시도해 주세요.');
                }
            });
        });
    });
</script>
