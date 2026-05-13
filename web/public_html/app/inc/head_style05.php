<div class="hd_m align-items-center justify-content-between">
	<div class=" "><button class="hd_btn btn2" type="button" onclick="history.back()"><img src="<?=DESIGN_HTTP?>/img/ic_back.png" alt="뒤로가기"></button></div>
	<div class="page_tit  "><?= $_SUB_HEAD_TITLE ?></div>
	<div class="dropdown item">
        <?php if($_SESSION['mng']) { ?>
            <button class="btn2  dropdown-toggle down" type="button" data-toggle="dropdown" aria-expanded="false">
                <?=$_SESSION['mng']['mt_name'] ?? '비회원'?>
            </button>
            <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item " href="<?=APP_PAGE?>/my/mypage.php">마이페이지</a>
                <a class="dropdown-item" href="<?=APP_PAGE?>/order/history.php">주문내역</a>
                <a class="dropdown-item" href="#" id="btnLogout">로그아웃</a>
            </div>
        <?php }else{?>
            <!-- 로그인버튼-->
            <button type="button" class="btn btn-secondary btn-sm rounded-pill"  onclick="location.href='<?=APP_PAGE?>/auth/login.php' ">회원로그인</button>
        <?php } ?>
	</div>
</div>

<script>
    $(function () {
        console.log('헤더 스크립트 초기화');

        $('#btnLogout').on('click', function (e) {
            e.preventDefault();
            console.log('로그아웃 버튼 클릭');
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
