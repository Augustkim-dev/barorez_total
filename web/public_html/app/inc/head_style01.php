<?php
$tv_idx = (int)($_SESSION['visit_id'] ?? 0);

if ($tv_idx <= 0) {
    $order_count = 0;
} else {
    $DB->where('tv_idx', $tv_idx);
    $DB->where('ot_status', 'CANCELLED', '!=');
    $order_count = (int)$DB->getValue('orders_t', 'COUNT(*)');
}
?>

<div class="hd_m align-items-center justify-content-between">
	<a class="d-flex flex-fill align-items-center" href="./">
		<img class="flex-shrink-0 logo_img" src="<?=DESIGN_HTTP?>/img/logo.svg">
	</a>
	<div class=" justify-content-end d-flex align-items-center">

		<!-- 장바구니 버튼 -->
		 <button class="hd_btn mr-4" type="button" onclick="location.href='<?=ORDER_PAGE?>/cart.php'">
			<img src="<?=DESIGN_HTTP?>/img/icon_cart.png" alt="장바구니">
			<span></span>
		</button>

		 <a href="<?=ORDER_PAGE?>/order_guest.php?tv_idx=<?=$tv_idx?>" class="badg  green mr-3">
			주문내역 <span class="ml-1 fw_500"><?=$order_count?></span>
		</a>

        <?php if($_SESSION['mng']) { ?>
		<div class="dropdown item">
			<button class="btn2  dropdown-toggle down" type="button" data-toggle="dropdown" aria-expanded="false">
				<?=$_SESSION['mng']['mt_name'] ?? '비회원'?>
			</button>
			<div class="dropdown-menu dropdown-menu-right">
				<a class="dropdown-item " href="<?=MY_PAGE?>/mypage.php">마이페이지</a>
				<a class="dropdown-item" href="<?=ORDER_PAGE?>/history.php">주문내역</a>
				<a class="dropdown-item" href="#" id="btnLogout">로그아웃</a>
			</div>
		</div>
        <?php }else{?>
		<!-- 로그인버튼-->
		 <button type="button" class="btn btn-secondary btn-sm rounded-pill"  onclick="location.href='<?=AUTH_PAGE?>/login.php' ">회원로그인</button>
        <?php } ?>
	</div>
</div>
<?php include_once("./inc/modal.php");?>
<script>
    $(function () {
        console.log('헤더 스크립트 초기화');

        $('#btnLogout').on('click', function (e) {
            e.preventDefault();

            let url = '<?=AUTH_ACTIONS?>/logout.php';
            $.ajax({
                url: url,  // 🔥 실제 login.php 경로
                type: 'POST',
                dataType: 'json',
                data: {
                    act: 'logout'  // 🔥 백엔드에서 분기할 값
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
