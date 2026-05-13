<?php
$shopName   = (string)($_SHOP_NAME ?? '');
$shopAddr   = (string)($_SHOP_ADDR ?? '');
$shopLat    = (float)($_SHOP_LAT ?? 0);
$shopLng    = (float)($_SHOP_LNG ?? 0);
$shopImages = ($_SHOP_IMAGES ?? []);

$openTime = (string)($_SHOP_OPEN_TIME ?? '-');
$holiday  = (string)($_SHOP_HOLIDAY ?? '없음');
$tel      = (string)($_SHOP_TEL ?? '-');
?>

<div class="wrap">
    <div class="sub_pg ">

        <?php if($shopImages):?>
        <section class="shop_banner">
            <div class="swiper review_swiper">
                <div class="swiper-wrapper">
                    <?php foreach ($shopImages as $img): ?>
                        <div class="swiper-slide">
                            <div class="rect">
                                <img src="<?= htmlspecialchars($img) ?>" alt="이미지">
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="swiper-pagination pag_st2"></div>
            </div>

            <script>
                var swiper = new Swiper(".review_swiper", {
                    pagination: { el: ".swiper-pagination", type: "fraction" }
                });
            </script>
        </section>
        <?php endif;?>
        <div class="container py-5">
            <p class="fs_20 fw_600 mb-4"><?= htmlspecialchars($shopName) ?></p>

            <!-- ✅ 카카오 지도 -->
            <div class="rounded overflow-hidden" style="height:190px;">
                <div id="shopInfoMap" style="width:100%;height:100%;"></div>
            </div>

            <div class="mt-4">
                <div class="d-flex shop_story">
                    <div class="tg_400 tit">상호명</div>
                    <div class="flex-fill"><?= htmlspecialchars($shopName) ?></div>
                </div>

                <div class="d-flex shop_story">
                    <div class="tg_400 tit">주소</div>
                    <div class="flex-fill">
                        <p>
                            <?= htmlspecialchars($shopAddr ?: '주소 정보 없음') ?><br>
                            <a href="javascript:void(0)" class="un_reboot_a tg_400 mt-2" id="btnCopyAddr">주소 복사</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bar"></div>

        <div class="container py-5">
            <div>
                <!-- 운영시간 -->
                <div class="d-flex shop_story align-items-start mb-3">
                    <div class="tg_400 tit flex-shrink-0">운영시간</div>
                    <div class="flex-fill ms-3">
                        <?php if ($openTime !== '-' && trim($openTime) !== ''): ?>
                            <p class="mb-1"><?= nl2br(htmlspecialchars($openTime)) ?></p>
                        <?php else: ?>
                            <p class="text-muted">운영시간 정보 없음</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- 휴무일 -->
                <div class="d-flex shop_story align-items-start mb-3">
                    <div class="tg_400 tit flex-shrink-0">휴무일</div>
                    <div class="flex-fill ms-3">
                        <?php if ($holiday !== '' && $holiday !== '없음'): ?>
                            <p class="mb-1"><?= nl2br(htmlspecialchars($holiday)) ?></p>
                        <?php else: ?>
                            <p class="text-muted">휴무일 없음</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- 전화번호 -->
                <div class="d-flex shop_story align-items-start">
                    <div class="tg_400 tit flex-shrink-0">전화번호</div>
                    <div class="flex-fill ms-3">
                        <a href="tel:<?=format_phone($tel)?>"><p><?= htmlspecialchars(format_phone($tel)) ?></p></a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<?php include_once("../inc/modal.php");?>
<script>
    (function(){
        var KAKAO_KEY = <?= json_encode(KAKAO_JAVASCRIPT_KEY) ?>;
        var lat  = <?= json_encode($shopLat) ?>;
        var lng  = <?= json_encode($shopLng) ?>;
        var name = <?= json_encode($shopName) ?>;
        var addr = <?= json_encode($shopAddr) ?>;

        function loadKakao(cb){
            if (window.kakao && window.kakao.maps) return cb();

            var exist = document.getElementById('kakaoSdkShopInfo');
            if (exist) {
                var t = setInterval(function(){
                    if (window.kakao && window.kakao.maps) { clearInterval(t); cb(); }
                }, 100);
                return;
            }

            var s = document.createElement('script');
            s.id = 'kakaoSdkShopInfo';
            s.src = "//dapi.kakao.com/v2/maps/sdk.js?appkey=" + encodeURIComponent(KAKAO_KEY) + "&autoload=false";
            s.onload = cb;
            s.onerror = function(){ console.error('[SHOP_INFO] Kakao SDK load fail'); };
            document.head.appendChild(s);
        }

        function escapeHtml(str){
            return String(str||'')
                .replaceAll('&','&amp;')
                .replaceAll('<','&lt;')
                .replaceAll('>','&gt;')
                .replaceAll('"','&quot;')
                .replaceAll("'","&#039;");
        }

        function initMap(){
            if (!lat || !lng) return;

            loadKakao(function(){
                kakao.maps.load(function(){
                    var container = document.getElementById('shopInfoMap');
                    if (!container) return;

                    var center = new kakao.maps.LatLng(lat, lng);
                    var map = new kakao.maps.Map(container, { center: center, level: 3 });

                    var marker = new kakao.maps.Marker({ position: center });
                    marker.setMap(map);

                    var iw = new kakao.maps.InfoWindow({
                        content: '<div style="padding:8px 10px; font-size:12px;"><b>' + escapeHtml(name) + '</b><br>' + escapeHtml(addr || '') + '</div>'
                    });
                    iw.open(map, marker);

                    setTimeout(function(){
                        map.relayout();
                        map.setCenter(center);
                    }, 60);
                });
            });
        }

        // 주소 복사
        $('#btnCopyAddr').on('click', function(){
            if (!addr) {
                ModalUtil.alert({
                    title: '알림',
                    message: '복사할 주소가 없습니다.',
                    okText: '확인',
                    onOk: function () {
                    },
                });
                return;
            }

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(addr).then(function(){
                    ModalUtil.alert({
                        title: '알림',
                        message: '주소가 복사되었습니다.',
                        okText: '확인',
                        onOk: function () {
                        },
                    });
                }).catch(function(){
                    ModalUtil.alert({
                        title: '알림',
                        message: '복사에 실패했습니다.',
                        okText: '확인',
                        onOk: function () {
                        },
                    });
                });
            } else {
                var ta = document.createElement('textarea');
                ta.value = addr;
                document.body.appendChild(ta);
                ta.select();
                try { document.execCommand('copy');
                    ModalUtil.alert({
                        title: '알림',
                        message: '주소가 복사되었습니다.',
                        okText: '확인',
                        onOk: function () {
                        },
                    });
                 }
                catch(e){
                    ModalUtil.alert({
                        title: '알림',
                        message: '복사에 실패했습니다.',
                        okText: '확인',
                        onOk: function () {
                        },
                    });
                }
                document.body.removeChild(ta);
            }
        });

        $(function(){ initMap(); });
    })();
</script>
