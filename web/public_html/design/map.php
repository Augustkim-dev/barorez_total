<?
$_SUB_HEAD_TITLE = "지도"; //헤더에 타이틀명이 없을경우 공백
$_GET['hd_num'] = '7'; //모바일 hd 1~n까지 있음
$_GET['bt_menu'] = ''; //모바일 하단메뉴 있음1, 없음 공백
include_once("./inc/head.php");
?>


<div class="wrap">
    <div class="sub_pg pb-0">
        <div class="map_wp overflow-hidden">
            <div class="map_hd">
                <div class="d-flex align-items-center">
                    <form class="sch_ip sch_gray align-items-center flex-fill">
                        <input type="search" class="form-control  fs_15  flex-fill border-0" placeholder="매장명, 지역으로 검색해보세요">
                        <button class="btn btn-icon flex-shrink-0"><img src="./img/ic_sch_gray.png" style="width:1.8rem;"></button>
                    </form>
                    <button type="button" class="btn2 map_gps ml-3"><img src="./img/gps-on.png" alt="내위치" style="width:100%"></button>
                </div>
            </div>
            <div class="map_ft">
                <button type="button" class="btn btn-outline-light btn-md rounded-pill fs_13"><img src="./img/sch_re.png" alt="재검색" style="width:1.4rem" class="mr-2"> 이 지역에서 검색</button>
            </div>

            <!-- 지도마커-->
            <a href="" style="position: absolute;top: 50%; left: 10%; "><img src="./img/marker.png" style="width:3.6rem;"></a>
            <a href="" style="position: absolute;top: 40%; left: 50%; "><img src="./img/marker2.png" style="width:3.6rem;"></a>

            <!-- 지도목록-->
            <div class="map_list">
                <button type="button" class="btn2 map_touchbar">
                    <span></span>
                </button>
                <button type="button" class="btn btn-outline-light btn-md rounded-pill fs_13 mapturn"><img src="./img/sch_re.png" alt="재검색" style="width:1.4rem" class="mr-2"> 지도보기</button>

                <ul class="shop_list scroll_y_bar2 ">
                    <li>

                        <div class="shop_box">

                            <div class="drag-slider">
                                <div class="slide flex-shrink-0">
                                    <div class="ratio-3-2 slide-img">
                                        <img class=" " src="./img/pr_sample01.jpg" alt="상품사진">
                                    </div>
                                </div>
                                <div class="slide flex-shrink-0">
                                    <div class="ratio-1-1  slide-img">
                                        <img class=" " src="./img/pr_sample02.jpg" alt="상품사진">
                                    </div>
                                </div>
                                <div class="slide flex-shrink-0">
                                    <div class="ratio-1-1  slide-img">
                                        <img class=" " src="./img/pr_sample03.jpg" alt="상품사진">
                                    </div>
                                </div>
                                <div class="slide flex-shrink-0">
                                    <div class="ratio-1-1  slide-img">
                                        <img class=" " src="./img/pr_sample04.jpg" alt="상품사진">
                                    </div>
                                </div>
                                <div class="slide flex-shrink-0">
                                    <div class="ratio-1-1  slide-img ">
                                        <a href="./shop.php" class="linkbox"><img src="./img/ico_shop.png" alt="이미지" style="width:38px"><br>메뉴 둘러보기</a>
                                    </div>
                                </div>
                            </div>
                            <a href="./shop.php" class="d-block">
                                <div class="txt_box">
                                    <p>바다마을 해물칼국수 [성수점] </p>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <p class="fs_14 fw_500 tg_400 ">11시 30분 영업시작</p>
                                        <p><span class="badg sm ml-1 "> 포장가능</span> <span class="badg sm m-1"> 예약가능</span></p>
                                    </div>
                                </div>
                            </a>
                        </div>

                    </li>
                    <li>
                        <div class="shop_box">
                            <div class="drag-slider">
                                <div class="slide flex-shrink-0">
                                    <div class="ratio-3-2 slide-img">
                                        <img class=" " src="./img/pr_sample03.jpg" alt="상품사진">
                                    </div>
                                </div>
                                <div class="slide flex-shrink-0">
                                    <div class="ratio-1-1  slide-img">
                                        <img class=" " src="./img/pr_sample03.jpg" alt="상품사진">
                                    </div>
                                </div>
                                <div class="slide flex-shrink-0">
                                    <div class="ratio-1-1  slide-img">
                                        <img class=" " src="./img/pr_sample03.jpg" alt="상품사진">
                                    </div>
                                </div>
                                <div class="slide flex-shrink-0">
                                    <div class="ratio-1-1  slide-img">
                                        <img class=" " src="./img/pr_sample03.jpg" alt="상품사진">
                                    </div>
                                </div>
                                <div class="slide flex-shrink-0">
                                    <div class="ratio-1-1  slide-img ">
                                        <a href="./shop.php" class="linkbox"><img src="./img/ico_shop.png" alt="이미지" style="width:38px"><br>메뉴 둘러보기</a>
                                    </div>
                                </div>
                            </div>
                            <a href="./shop.php" class="d-block">
                                <div class="txt_box">
                                    <p>바다마을 해물칼국수 [성수점] </p>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <p class="fs_14 fw_500 tg_400 ">11시 30분 영업시작</p>
                                        <p> <span class="badg sm m-1"> 예약가능</span></p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </li>


                    <li>
                        <div class="shop_box">
                            <div class="drag-slider">
                                <div class="slide flex-shrink-0">
                                    <div class="ratio-3-2 slide-img">
                                        <img class=" " src="./img/pr_sample03.jpg" alt="상품사진">
                                    </div>
                                </div>
                                <div class="slide flex-shrink-0">
                                    <div class="ratio-1-1  slide-img">
                                        <img class=" " src="./img/pr_sample03.jpg" alt="상품사진">
                                    </div>
                                </div>
                                <div class="slide flex-shrink-0">
                                    <div class="ratio-1-1  slide-img">
                                        <img class=" " src="./img/pr_sample03.jpg" alt="상품사진">
                                    </div>
                                </div>
                                <div class="slide flex-shrink-0">
                                    <div class="ratio-1-1  slide-img">
                                        <img class=" " src="./img/pr_sample03.jpg" alt="상품사진">
                                    </div>
                                </div>
                                <div class="slide flex-shrink-0">
                                    <div class="ratio-1-1  slide-img ">
                                        <a href="./shop.php" class="linkbox"><img src="./img/ico_shop.png" alt="이미지" style="width:38px"><br>메뉴 둘러보기</a>
                                    </div>
                                </div>
                            </div>
                            <a href="./shop.php" class="d-block">
                                <div class="txt_box">
                                    <p>바다마을 해물칼국수 [성수점] </p>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <p class="fs_14 fw_500 tg_400 ">11시 30분 영업시작</p>
                                        <p><span class="badg sm ml-1 "> 포장가능</span> <span class="badg sm m-1"> 예약가능</span></p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </li>
                </ul>
            </div>


            <!-- 지도예시-->
            <iframe style="width:100% ; height:100%" src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d26096.81317480725!2d129.06332159999997!3d35.15408565!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sko!2skr!4v1763966280024!5m2!1sko!2skr" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>


    </div>
</div>
<script>
    document.querySelectorAll('.drag-slider').forEach(slider => {

        let isDown = false;
        let startX;
        let scrollLeft;
        let velX = 0;
        let momentumID;

        slider.addEventListener('mousedown', e => {
            isDown = true;
            slider.classList.add('active');
            startX = e.pageX - slider.offsetLeft;
            scrollLeft = slider.scrollLeft;
            cancelMomentum();
        });

        slider.addEventListener('mouseleave', () => {
            isDown = false;
            slider.classList.remove('active');
        });

        slider.addEventListener('mouseup', () => {
            isDown = false;
            slider.classList.remove('active');
            beginMomentum();
        });

        slider.addEventListener('mousemove', e => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - slider.offsetLeft;
            const walk = (x - startX) * 2;
            const prev = slider.scrollLeft;
            slider.scrollLeft = scrollLeft - walk;
            velX = slider.scrollLeft - prev;
        });

        /* 모멘텀 */
        function beginMomentum() {
            cancelMomentum();
            momentumID = requestAnimationFrame(momentum);
        }

        function cancelMomentum() {
            cancelAnimationFrame(momentumID);
        }

        function momentum() {
            slider.scrollLeft += velX;
            velX *= 0.95;
            if (Math.abs(velX) > 0.5) {
                momentumID = requestAnimationFrame(momentum);
            }
        }
    });
</script>


<? include_once("./inc/tail.php"); ?>