
    <div class="wrap">
        <div class="sub_pg my_pg container px-0 pb-0">
            <div class="container pb_20">
                <div class="d-flex align-items-center fs_18 fw_300 mt-5">
                    <p><span class="fw_600"><?=$_SESSION['mng']['mt_name']?>님</span> 안녕하세요!</p>
                    <p><a href="<?=$link?>"><img src="<?=DESIGN_HTTP?>/img/ico_arrow1.png" alt="내정보 수정" class="ml-3" style="width: 2.5rem;"></a></p>
                </div>
                <div class="mt-4 bg-primary rounded-md px-4 py-3 text-white d-flex justify-content-between ">
                    <div class="d-flex align-items-center">
                        <p><img src="<?=DESIGN_HTTP?>/img/my_coupon.png" alt="쿠폰" class="mr-2" style="width: 2.4rem;"></p>
                        <p>나의 쿠폰</p>
                        <p class="fw_700 t_yellw ml-3"><?= $availableCouponCount ?></p>
                    </div>
                    <div>
                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill fw_600"  onclick="location.href='../coupon/list.php' ">쿠폰내역</button>
                    </div>
                </div>
            </div>
            <div class="bar">

            </div>

            <div class=" ">
                <ul class="mypage_list">
                    <li>
                        <a class="d-flex align-items-center" href="../order/history.php">
                            <p><img src="<?=DESIGN_HTTP?>/img/my_wallet.png" alt="꾸밈이미지" class="mr-4" style="width: 2rem;"></p>
                            <div>주문 내역</div>
                            <img class="ml-auto flex-shrink-0" src="<?=DESIGN_HTTP?>/img/ic_more02.png" style="width:1.6rem;">
                        </a>
                    </li>
                    <li>
                        <a class="d-flex align-items-center" href="../review/my.php">
                            <p><img src="<?=DESIGN_HTTP?>/img/my_review.png?v=1" alt="꾸밈이미지" class="mr-4" style="width: 2rem;"></p>
                            <div>내가 작성한 리뷰</div>
                            <img class="ml-auto flex-shrink-0" src="<?=DESIGN_HTTP?>/img/ic_more02.png" style="width:1.6rem;">
                        </a>
                    </li>
                    <li>
                        <a class="d-flex align-items-center" href="../customer/info.php">
                            <p><img src="<?=DESIGN_HTTP?>/img/my_profile.png" alt="꾸밈이미지" class="mr-4" style="width: 2rem;"></p>
                            <div>사업자 정보</div>
                            <img class="ml-auto flex-shrink-0" src="<?=DESIGN_HTTP?>/img/ic_more02.png" style="width:1.6rem;">
                        </a>
                    </li>
                    <li>
                        <a class="  d-flex align-items-center" href="../notice/list.php">
                            <p><img src="<?=DESIGN_HTTP?>/img/my_note.png" alt="꾸밈이미지" class="mr-4" style="width: 2rem;"></p>
                            <div>공지사항</div>
                            <img class="ml-auto flex-shrink-0" src="<?=DESIGN_HTTP?>/img/ic_more02.png" style="width:1.6rem;">
                        </a>
                    </li>
                    <li>
                        <a class=" d-flex align-items-center" href="../term/list.php">
                            <p><img src="<?=DESIGN_HTTP?>/img/my_term.png" alt="꾸밈이미지" class="mr-4" style="width: 2rem;"></p>
                            <div>이용약관 및 정책</div>
                            <img class="ml-auto flex-shrink-0" src="<?=DESIGN_HTTP?>/img/ic_more02.png" style="width:1.6rem;">
                        </a>
                    </li>

                    <li>
                        <a class="  d-flex align-items-center" href="#" onclick="logout(); return false;">
                            <p><img src="<?=DESIGN_HTTP?>/img/my_logout.png" alt="꾸밈이미지" class="mr-4" style="width: 2rem;"></p>
                            <div>로그아웃</div>
                            <img class="ml-auto flex-shrink-0" src="<?=DESIGN_HTTP?>/img/ic_more02.png" style="width:1.6rem;">
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <?php include_once("../inc/modal.php");?>
    <script>
        function logout(){
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
                    console.log('로그아웃 응답:', res);

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
        }
    </script>
