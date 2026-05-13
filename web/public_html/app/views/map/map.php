<?php
$KAKAO_MAP_JS_KEY = defined('KAKAO_JAVASCRIPT_KEY') ? KAKAO_JAVASCRIPT_KEY : '여기에_카카오_JS_KEY';
?>
<div class="wrap">
    <div class="sub_pg pb-0">
        <div class="map_wp overflow-hidden">
            <div class="map_hd">
                <div class="d-flex align-items-center">
                    <button type="button"
                            class="btn mapbtn mr-2 rounded-pill"
                            id="btnRegionDefault"
                            data-toggle="modal"
                            data-target="#pop_cart">
                        지역선택
                        <img src="<?=DESIGN_HTTP?>/img/arrow_down.svg" class="ml-2">
                    </button>

                    <button type="button"
                            class="btn mapbtn act mr-2 rounded-pill d-none"
                            id="btnRegionActive"
                            data-toggle="modal"
                            data-target="#pop_cart">
                        <span id="selectedRegionText">서울전체</span>
                        <img src="<?=DESIGN_HTTP?>/img/arrow_down.svg" class="ml-2">
                    </button>

                    <form class="sch_ip sch_gray align-items-center flex-fill" id="mapSearchForm">
                        <input type="search" id="kw" class="form-control fs_15 flex-fill border-0" placeholder="매장명, 메뉴명으로 검색해보세요">
                        <button class="btn btn-icon flex-shrink-0" type="submit">
                            <img src="<?=DESIGN_HTTP?>/img/ic_sch_gray.png" style="width:1.8rem;">
                        </button>
                    </form>

                    <button type="button" class="btn2 map_gps ml-3" id="btnGps">
                        <img src="<?=DESIGN_HTTP?>/img/gps-on.png" alt="내위치" style="width:100%">
                    </button>
                </div>
            </div>

<!--            <div class="map_ft" --><?php //=$_SESSION['app_os'] ? 'style="bottom: 8rem"' : 'style="bottom: 5rem"'?><!-->-->
<!--                <button type="button" class="btn btn-outline-light btn-md rounded-pill fs_13" id="btnSearchHere">-->
<!--                    <img src="--><?php //=DESIGN_HTTP?><!--/img/sch_re.png" alt="재검색" style="width:1.4rem" class="mr-2">-->
<!--                    이 지역에서 검색-->
<!--                </button>-->
<!--            </div>-->
            <div class="map_ft" style="bottom: 13rem">
                <button type="button" class="btn btn-outline-light btn-md rounded-pill fs_13" id="btnSearchHere">
                    <img src="<?=DESIGN_HTTP?>/img/sch_re.png" alt="재검색" style="width:1.4rem" class="mr-2">
                    이 지역에서 검색
                </button>
            </div>

            <div class="map_ft">
                <button type="button" class="btn btn-outline-primary rounded-pill main-ft-btn" onclick="location.href='<?=APP_PAGE?>/search/?mode=reservation'">
                    예약
                </button>
                <button type="button" class="btn_qr rounded-pill" onclick="cameraBtn();">
                    <img src="<?=DESIGN_HTTP?>/img/qr.svg" alt="스캔" style="width:3.3rem">
                </button>

                <?php if(!$_SESSION['app_os']): ?>
                    <div id="webQrScanner" style="display:none;position:fixed;inset:0;z-index:3000;max-width: 576px; margin: 0 auto;">
                        <video id="qrVideo" class="web-qr-video" playsinline muted autoplay></video>
                        <div class="web-qr-dim"></div>
                        <div class="web-qr-frame"></div>
                        <button type="button" id="closeQrScanner" class="web-qr-close">닫기</button>
                        <div class="web-qr-help">QR 코드를 사각형 안에 맞춰주세요<p>인식되면 자동으로 스캔이 완료됩니다.</p></div>
                    </div>

                    <input type="file" id="webCameraInput" accept="image/*" capture="environment" style="display:none">
                    <canvas id="qrCanvas" style="display:none"></canvas>

                    <script src="https://cdn.jsdelivr.net/npm/jsqr/dist/jsQR.js"></script>
                <?php endif; ?>
                <button type="button" class="btn btn-primary rounded-pill main-ft-btn" onclick="location.href='<?=APP_PAGE?>/search/?mode=takeout'">
                    포장
                </button>
            </div>

            <div class="map_list">
                <button type="button" class="btn2 map_touchbar"><span></span></button>
                <button type="button" class="btn btn-outline-light btn-md rounded-pill fs_13 mapturn">
                    <img src="<?=DESIGN_HTTP?>/img/sch_re.png" alt="재검색" style="width:1.4rem" class="mr-2"> 지도보기
                </button>
                <ul class="shop_list scroll_y_bar2" id="shopList"></ul>
            </div>
            <div id="kakaoMap" style="width:100%;height:100%;"></div>
        </div>
    </div>
</div>

<div class="modal modal_bottom fade" id="pop_cart" tabindex="-1" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header ">
                <h5 class="modal-title">지역 선택</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><img class=" " src="<?=DESIGN_HTTP?>/img/ico_x.png" alt="삭제" style="width:18px"></button>
            </div>
            <div class="modal-body p-0">

                <div class="loc_wp">
                    <div class="deps1" id="regionDepth1"></div>
                    <div class="deps2" id="regionDepth2"></div>
                </div>
            </div>
            <div class="modal-footer pt-3 border-top">
                <div class="form-row">
                    <div class="col-4"><button type="button" class="btn btn-outline-light btn-block" id="btnRegionReset"><img  src="<?=DESIGN_HTTP?>/img/re.svg" alt="초기화" class="mr-2"  > 초기화</button></div>
                    <div class="col-8"><button type="button" class="btn btn-primary btn-block" id="btnRegionApply">적용</button></div>

                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once("../inc/modal.php");?>

<style>
    /* ── 말풍선 오버레이 ── */
    .map-overlay-wrap {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        filter: drop-shadow(0 4px 12px rgba(0,0,0,0.18));
        pointer-events: auto;
    }
    .map-overlay-inner {
        background: #fff;
        border: 1px solid #FF4516;
        border-radius: 12px;
        padding: 14px 16px 12px;
        min-width: 190px;
        max-width: 260px;
        position: relative;
        pointer-events: auto;
        cursor: default;
        top: -40px;
    }
    .map-overlay-close {
        position: absolute;
        top: 8px;
        right: 10px;
        background: none;
        border: none;
        font-size: 18px;
        color: #999;
        cursor: pointer;
        line-height: 1;
        padding: 0;
    }
    .map-overlay-title {
        font-size: 12px;
        color: #999;
        margin: 0 0 8px 0;
        padding-right: 20px;
        font-weight: 500;
    }
    .map-overlay-item {
        border-top: 1px solid #f2f2f2;
    }
    .map-overlay-item:first-of-type {
        border-top: none;
    }
    .map-overlay-link {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 0;
        text-decoration: none;
        color: #222;
        pointer-events: auto;
        cursor: pointer;
        position: relative;
        z-index: 1;
    }
    .map-overlay-link:hover { color: #FF4516; }
    .map-overlay-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #ff5a5f;
        flex-shrink: 0;
    }
    .map-overlay-img{
        width:18px;
        height: 20px;
        object-fit: cover;
    }
    .map-overlay-name {
        flex: 1;
        font-size: 13px;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .map-overlay-dist {
        font-size: 12px;
        color: #999;
        flex-shrink: 0;
    }
    .map-overlay-tail {
        width: 0;
        height: 0;
        border-left: 9px solid transparent;
        border-right: 9px solid transparent;
        border-top: 11px solid #fff;
        margin-top: -1px;
    }
</style>

<script src="//dapi.kakao.com/v2/maps/sdk.js?appkey=<?=KAKAO_JAVASCRIPT_KEY?>&libraries=services"></script>

<script>
    let qrStream = null;
    let qrRafId = null;

    function cameraBtn() {
        try {
            if (APP_OS) {
                const payload = { type: 'CAMERA' };
                if (window.ReactNativeWebView && window.ReactNativeWebView.postMessage) {
                    window.ReactNativeWebView.postMessage(JSON.stringify(payload));
                    return true;
                }
                return false;
            }

            openWebQrScanner();
            return true;
        } catch (e) {
            console.error('[SCAN] 실행 실패:', e);
            return false;
        }
    }

    async function openWebQrScanner() {
        const video = document.getElementById('qrVideo');
        const wrap = document.getElementById('webQrScanner');

        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia || !video || !wrap) {
            openWebCameraFallback();
            return;
        }

        try {
            qrStream = await navigator.mediaDevices.getUserMedia({
                audio: false,
                video: { facingMode: { ideal: 'environment' } }
            });

            video.srcObject = qrStream;
            wrap.style.display = 'block';
            await video.play();
            scanQrFrame();
        } catch (e) {
            console.error('[SCAN] 카메라 실행 실패:', e);
            openWebCameraFallback();
        }
    }

    function openWebCameraFallback() {
        const input = document.getElementById('webCameraInput');
        if (!input) return;
        input.value = '';
        input.click();
    }

    function stopWebQrScanner() {
        if (qrRafId) {
            cancelAnimationFrame(qrRafId);
            qrRafId = null;
        }

        const video = document.getElementById('qrVideo');
        const wrap = document.getElementById('webQrScanner');

        if (video) {
            video.pause();
            video.srcObject = null;
        }

        if (qrStream) {
            qrStream.getTracks().forEach(track => track.stop());
            qrStream = null;
        }

        if (wrap) {
            wrap.style.display = 'none';
        }
    }

    function scanQrFrame() {
        const video = document.getElementById('qrVideo');
        const canvas = document.getElementById('qrCanvas');
        if (!video || !canvas) return;

        if (video.readyState !== video.HAVE_ENOUGH_DATA) {
            qrRafId = requestAnimationFrame(scanQrFrame);
            return;
        }

        const ctx = canvas.getContext('2d', { willReadFrequently: true });
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        const code = jsQR(imageData.data, imageData.width, imageData.height);

        if (code && code.data) {
            stopWebQrScanner();
            handleQrResult(code.data);
            return;
        }

        qrRafId = requestAnimationFrame(scanQrFrame);
    }

    function handleQrResult(value) {
        if (/^https?:\/\//i.test(value)) {
            location.href = value;
            return;
        }

        alert('QR 결과: ' + value);
    }

    function decodeQrFromFile(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();

            reader.onload = () => {
                const img = new Image();

                img.onload = () => {
                    const canvas = document.getElementById('qrCanvas');
                    const ctx = canvas.getContext('2d', { willReadFrequently: true });

                    canvas.width = img.naturalWidth;
                    canvas.height = img.naturalHeight;
                    ctx.drawImage(img, 0, 0);

                    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                    const code = jsQR(imageData.data, imageData.width, imageData.height);

                    resolve(code ? code.data : '');
                };

                img.onerror = reject;
                img.src = reader.result;
            };

            reader.onerror = reject;
            reader.readAsDataURL(file);
        });
    }

    const webCameraInput = document.getElementById('webCameraInput');
    if (webCameraInput) {
        webCameraInput.addEventListener('change', async function(e) {
            const file = e.target.files && e.target.files[0];
            if (!file) return;

            try {
                const result = await decodeQrFromFile(file);
                if (result) {
                    handleQrResult(result);
                } else {
                    alert('QR 코드를 찾지 못했습니다.');
                }
            } catch (err) {
                console.error('[SCAN] 파일 해석 실패:', err);
                alert('이미지 분석에 실패했습니다.');
            }
        });
    }

    const closeQrScanner = document.getElementById('closeQrScanner');
    if (closeQrScanner) {
        closeQrScanner.addEventListener('click', stopWebQrScanner);
    }

    window.addEventListener('beforeunload', stopWebQrScanner);

    $(function() {
        const scanner = document.getElementById('webQrScanner');
        if (scanner && scanner.parentNode !== document.body) {
            document.body.appendChild(scanner);
        }
    });

    const API_URL = '<?=MAP_ACTIONS?>/update.php';

    const APP_OS  = '<?= $_SESSION['app_os'] ?? '' ?>';
    const APP_LAT = '<?= isset($_SESSION['app_lat']) ? $_SESSION['app_lat'] : '' ?>';
    const APP_LNG = '<?= isset($_SESSION['app_lng']) ? $_SESSION['app_lng'] : '' ?>';

    const REGION_FILTERS = [
        { label: '서울', value: '서울특별시', districts: ['종로구','중구','용산구','성동구','광진구','동대문구','중랑구','성북구','강북구','도봉구','노원구','은평구','서대문구','마포구','양천구','강서구','구로구','금천구','영등포구','동작구','관악구','서초구','강남구','송파구','강동구'] },
        { label: '부산', value: '부산광역시', districts: ['중구','서구','동구','영도구','부산진구','동래구','남구','북구','해운대구','사하구','금정구','강서구','연제구','수영구','사상구','기장군'] },
        { label: '대구', value: '대구광역시', districts: ['중구','동구','서구','남구','북구','수성구','달서구','달성군','군위군'] },
        { label: '인천', value: '인천광역시', districts: ['중구','동구','미추홀구','연수구','남동구','부평구','계양구','서구','강화군','옹진군'] },
        { label: '광주', value: '광주광역시', districts: ['동구','서구','남구','북구','광산구'] },
        { label: '대전', value: '대전광역시', districts: ['동구','중구','서구','유성구','대덕구'] },
        { label: '울산', value: '울산광역시', districts: ['중구','남구','동구','북구','울주군'] },
        { label: '세종', value: '세종특별자치시', districts: [] },
        { label: '경기', value: '경기도', districts: ['수원시','성남시','의정부시','안양시','부천시','광명시','평택시','동두천시','안산시','고양시','과천시','구리시','남양주시','오산시','시흥시','군포시','의왕시','하남시','용인시','파주시','이천시','안성시','김포시','화성시','광주시','양주시','포천시','여주시','연천군','가평군','양평군'] },
        { label: '강원', value: '강원특별자치도', districts: ['춘천시','원주시','강릉시','동해시','태백시','속초시','삼척시','홍천군','횡성군','영월군','평창군','정선군','철원군','화천군','양구군','인제군','고성군','양양군'] },
        { label: '충북', value: '충청북도', districts: ['청주시','충주시','제천시','보은군','옥천군','영동군','증평군','진천군','괴산군','음성군','단양군'] },
        { label: '충남', value: '충청남도', districts: ['천안시','공주시','보령시','아산시','서산시','논산시','계룡시','당진시','금산군','부여군','서천군','청양군','홍성군','예산군','태안군'] },
        { label: '전북', value: '전북특별자치도', districts: ['전주시','군산시','익산시','정읍시','남원시','김제시','완주군','진안군','무주군','장수군','임실군','순창군','고창군','부안군'] },
        { label: '전남', value: '전라남도', districts: ['목포시','여수시','순천시','나주시','광양시','담양군','곡성군','구례군','고흥군','보성군','화순군','장흥군','강진군','해남군','영암군','무안군','함평군','영광군','장성군','완도군','진도군','신안군'] },
        { label: '경북', value: '경상북도', districts: ['포항시','경주시','김천시','안동시','구미시','영주시','영천시','상주시','문경시','경산시','의성군','청송군','영양군','영덕군','청도군','고령군','성주군','칠곡군','예천군','봉화군','울진군','울릉군'] },
        { label: '경남', value: '경상남도', districts: ['창원시','진주시','통영시','사천시','김해시','밀양시','거제시','양산시','의령군','함안군','창녕군','고성군','남해군','하동군','산청군','함양군','거창군','합천군'] },
        { label: '제주', value: '제주특별자치도', districts: ['제주시','서귀포시'] }
    ];

    let map = null;
    let myMarker = null;
    let currentPos = null;

    let lastKeyword = '';
    let lastShops = [];
    let lastClusters = [];
    let lastResponseMode = 'shop';
    let lastLoadedBounds = null;
    let lastQuerySignature = '';
    let lastTotalCount = 0;

    let markerStore = new Map();
    let markerSignatureStore = new Map();
    let activeOverlay = null;
    let selectedMarkerKey = null;

    let activeMapRequest = null;
    let mapRequestSeq = 0;
    let mapIdleTimer = null;
    let isBootstrapping = true;

    let appGpsMarker = null;
    let appGpsPath = [];
    let appGpsLatest = null;

    const $list = $('#shopList');
    const $kw = $('#kw');
    const $mapSearchForm = $('#mapSearchForm');

    const $btnRegionDefault = $('#btnRegionDefault');
    const $btnRegionActive = $('#btnRegionActive');
    const $selectedRegionText = $('#selectedRegionText');
    const $regionDepth1 = $('#regionDepth1');
    const $regionDepth2 = $('#regionDepth2');
    const $btnRegionApply = $('#btnRegionApply');
    const $btnRegionReset = $('#btnRegionReset');
    const $regionModal = $('#pop_cart');

    let draftRegionIndex = 0;
    let draftDistrictValue = '';
    let draftDistrictLabel = '';

    let appliedRegionValue = '';
    let appliedRegionLabel = '';
    let appliedDistrictValue = '';
    let appliedDistrictLabel = '';

    const Z_INDEX_MY_LOCATION = 0;
    const Z_INDEX_SHOP_DEFAULT = 10;
    const Z_INDEX_SHOP_SELECTED = 20;

    const MARKER_URL_DEFAULT  = '<?=DESIGN_HTTP?>/img/marker.png';
    const MARKER_URL_SELECTED = '<?=DESIGN_HTTP?>/img/marker2.png';
    const MARKER_URL_LIST     = '<?=DESIGN_HTTP?>/img/marker3.png';
    const MARKER_URL_TEMP     = '<?=DESIGN_HTTP?>/img/marker4.png';

    const W = 36;
    const H = 40;

    const markerImageCache = new Map();
    const badgeMarkerDataCache = new Map();
    const clusterBubbleImageCache = new Map();

    const sheet = document.querySelector('.map_list');
    let currentSnapIndex = 3;
    const SNAP_POINTS = [0, 15, 50, 97];
    let currentTranslateY = SNAP_POINTS[currentSnapIndex];

    function escapeHtml(str) {
        return String(str || '')
            .replace(/&/g,'&amp;')
            .replace(/</g,'&lt;')
            .replace(/>/g,'&gt;')
            .replace(/"/g,'&quot;')
            .replace(/'/g,'&#039;');
    }

    function fmtDistance(m) {
        const n = Number(m || 0);
        return n >= 1000 ? (n / 1000).toFixed(1) + 'km' : Math.round(n) + 'm';
    }

    function makeQuerySignature(keyword = '', region = '', district = '', zoomLevel = 7) {
        return [
            `keyword:${keyword || ''}`,
            `region:${region || ''}`,
            `district:${district || ''}`,
            `zoom:${zoomLevel}`
        ].join('|');
    }

    function getBoundsParams() {
        const bounds = map.getBounds();
        const sw = bounds.getSouthWest();
        const ne = bounds.getNorthEast();

        return {
            swLat: sw.getLat(),
            swLng: sw.getLng(),
            neLat: ne.getLat(),
            neLng: ne.getLng()
        };
    }

    function isInsideLoadedBounds(currentBounds, loadedBounds) {
        if (!currentBounds || !loadedBounds) return false;

        return (
            currentBounds.swLat >= loadedBounds.swLat &&
            currentBounds.neLat <= loadedBounds.neLat &&
            currentBounds.swLng >= loadedBounds.swLng &&
            currentBounds.neLng <= loadedBounds.neLng
        );
    }

    function getRefPos() {
        if (currentPos) {
            return {
                refLat: currentPos.lat,
                refLng: currentPos.lng
            };
        }

        const c = map.getCenter();
        return {
            refLat: c.getLat(),
            refLng: c.getLng()
        };
    }

    function getAppliedRegionParams() {
        return {
            region: appliedRegionValue,
            district: appliedDistrictValue
        };
    }

    function getBaseMarkerImage(src) {
        const cacheKey = `base:${src}`;

        if (!markerImageCache.has(cacheKey)) {
            markerImageCache.set(
                cacheKey,
                new kakao.maps.MarkerImage(
                    src,
                    new kakao.maps.Size(W, H),
                    { offset: new kakao.maps.Point(18, H) }
                )
            );
        }

        return markerImageCache.get(cacheKey);
    }

    function makeBadgeMarkerDataUrl(baseSrc, count) {
        const cacheKey = `${baseSrc}__${count}`;
        if (badgeMarkerDataCache.has(cacheKey)) {
            return Promise.resolve(badgeMarkerDataCache.get(cacheKey));
        }

        const badgeRadius = 8;
        const badgeStroke = 2;
        const topPadding = 14;
        const rightPadding = 14;

        const canvasWidth = W + rightPadding;
        const canvasHeight = H + topPadding;

        const canvas = document.createElement('canvas');
        canvas.width = canvasWidth;
        canvas.height = canvasHeight;

        const ctx = canvas.getContext('2d');
        const img = new Image();
        img.crossOrigin = 'anonymous';

        return new Promise(resolve => {
            img.onload = () => {
                const markerX = 0;
                const markerY = topPadding;

                ctx.drawImage(img, markerX, markerY, W, H);

                const bx = markerX + W - 8;
                const by = topPadding + 5;

                ctx.beginPath();
                ctx.arc(bx, by, badgeRadius, 0, Math.PI * 2);
                ctx.fillStyle = '#333';
                ctx.fill();

                ctx.strokeStyle = '#fff';
                ctx.lineWidth = badgeStroke;
                ctx.stroke();

                ctx.fillStyle = '#fff';
                ctx.font = 'bold 10px sans-serif';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText(formatClusterCountLabel(count), bx, by);

                const dataUrl = canvas.toDataURL();
                badgeMarkerDataCache.set(cacheKey, dataUrl);
                resolve(dataUrl);
            };

            img.onerror = () => resolve(baseSrc);
            img.src = baseSrc;
        });
    }

    async function getBadgeMarkerImage(baseSrc, count) {
        const dataUrl = await makeBadgeMarkerDataUrl(baseSrc, count);

        return new kakao.maps.MarkerImage(
            dataUrl,
            new kakao.maps.Size(W + 14, H + 14),
            { offset: new kakao.maps.Point(18, H + 14) }
        );
    }

    function formatClusterCountLabel(count) {
        const n = Math.max(0, Number(count) || 0);

        if (n >= 500000) return '499999+';
        if (n >= 100000) return '99999+';
        if (n >= 50000) return '49999+';
        if (n >= 10000) return '9999+';
        if (n >= 5000) return '4999+';
        if (n >= 1000) return '1000+';
        return String(n);
    }

    function getClusterBubbleStyle(count) {
        if (count < 10) return { size: 44, fontSize: 14 };
        if (count < 100) return { size: 56, fontSize: 14 };
        if (count < 1000) return { size: 68, fontSize: 16 };
        if (count < 10000) return { size: 80, fontSize: 16 };
        return { size: 92, fontSize: 16 };
    }

    function getClusterBubbleImage(count) {
        const label = formatClusterCountLabel(count);
        const cacheKey = `cluster:${label}`;

        if (clusterBubbleImageCache.has(cacheKey)) {
            return clusterBubbleImageCache.get(cacheKey);
        }

        const style = getClusterBubbleStyle(count);
        const size = style.size;
        const radius = size / 2;
        const canvas = document.createElement('canvas');
        canvas.width = size;
        canvas.height = size;

        const ctx = canvas.getContext('2d');

        ctx.beginPath();
        ctx.arc(radius, radius, radius - 2, 0, Math.PI * 2);
        ctx.fillStyle = '#FF5A5F';
        ctx.fill();

        ctx.beginPath();
        ctx.arc(radius, radius, radius - 2, 0, Math.PI * 2);
        ctx.lineWidth = 3;
        ctx.strokeStyle = '#FFFFFF';
        ctx.stroke();

        ctx.fillStyle = '#FFFFFF';
        ctx.font = `700 ${style.fontSize}px sans-serif`;
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(label, radius, radius + 1);

        const image = new kakao.maps.MarkerImage(
            canvas.toDataURL(),
            new kakao.maps.Size(size, size),
            { offset: new kakao.maps.Point(radius, radius) }
        );

        clusterBubbleImageCache.set(cacheKey, image);
        return image;
    }

    function closeOverlay() {
        if (activeOverlay) {
            activeOverlay.setMap(null);
            activeOverlay = null;
        }
    }

    function createOverlayContent(shops) {
        const wrap = document.createElement('div');
        wrap.className = 'map-overlay-wrap';

        const inner = document.createElement('div');
        inner.className = 'map-overlay-inner';

        ['click', 'mousedown', 'touchstart'].forEach(evtName => {
            inner.addEventListener(evtName, e => e.stopPropagation(), { passive: false });
        });

        shops.forEach(shop => {
            const item = document.createElement('div');
            item.className = 'map-overlay-item';

            const markerImg = shop.mt_appr === 'Y' ? MARKER_URL_LIST : MARKER_URL_TEMP;

            const link = document.createElement('a');
            link.href = `../shop/list.php?sh_idx=${shop.idx}`;
            link.className = 'map-overlay-link';
            link.innerHTML = `
                <img src="${markerImg}" class="map-overlay-img"/>
                <span class="map-overlay-name">${escapeHtml(shop.name)}</span>
                <span class="map-overlay-dist">${fmtDistance(shop.distance_m)}</span>
            `;

            item.appendChild(link);
            inner.appendChild(item);
        });

        wrap.appendChild(inner);
        return wrap;
    }

    function showOverlay(shops, lat, lng) {
        closeOverlay();

        activeOverlay = new kakao.maps.CustomOverlay({
            position: new kakao.maps.LatLng(lat, lng),
            content: createOverlayContent(shops),
            xAnchor: 0.5,
            yAnchor: 1.12,
            zIndex: 10
        });

        activeOverlay.setMap(map);
        map.panTo(new kakao.maps.LatLng(lat, lng));
    }

    function resetMarkerStyles() {
        selectedMarkerKey = null;

        markerStore.forEach(item => {
            if (!item.defaultImg) return;
            item.marker.setImage(item.defaultImg);
            item.marker.setZIndex(Z_INDEX_SHOP_DEFAULT);
        });
    }

    function selectMarker(key) {
        selectedMarkerKey = key;

        markerStore.forEach((item, itemKey) => {
            if (itemKey === key) {
                item.marker.setImage(item.selectedImg);
                item.marker.setZIndex(Z_INDEX_SHOP_SELECTED);
            } else {
                item.marker.setImage(item.defaultImg);
                item.marker.setZIndex(Z_INDEX_SHOP_DEFAULT);
            }
        });
    }

    async function createShopGroupMarker(markerKey, groupKey, shops) {
        const firstShop = shops[0];
        const count = shops.length;
        const latLng = new kakao.maps.LatLng(firstShop.lat, firstShop.lng);

        let defaultImg;
        let selectedImg;

        if (count > 1) {
            defaultImg = await getBadgeMarkerImage(MARKER_URL_DEFAULT, count);
            selectedImg = await getBadgeMarkerImage(
                firstShop.mt_appr === 'Y' ? MARKER_URL_SELECTED : MARKER_URL_TEMP,
                count
            );
        } else {
            defaultImg = getBaseMarkerImage(MARKER_URL_DEFAULT);
            selectedImg = getBaseMarkerImage(firstShop.mt_appr === 'Y' ? MARKER_URL_SELECTED : MARKER_URL_TEMP);
        }

        const marker = new kakao.maps.Marker({
            position: latLng,
            image: defaultImg,
            zIndex: Z_INDEX_SHOP_DEFAULT
        });

        kakao.maps.event.addListener(marker, 'click', () => {
            selectMarker(markerKey);

            if (shops.length === 1) {
                closeOverlay();
                showSingleShop(shops[0]);
                return;
            }

            showOverlay(shops, firstShop.lat, firstShop.lng);
            renderList(shops);
        });

        return {
            type: 'shop',
            groupKey,
            shops,
            marker,
            defaultImg,
            selectedImg
        };
    }

    function createServerClusterMarker(markerKey, cluster) {
        const count = Number(cluster.cluster_count || 1);
        const latLng = new kakao.maps.LatLng(cluster.lat, cluster.lng);

        const defaultImg = getClusterBubbleImage(count);
        const selectedImg = getClusterBubbleImage(count);

        const marker = new kakao.maps.Marker({
            position: latLng,
            image: defaultImg,
            zIndex: Z_INDEX_SHOP_DEFAULT
        });

        kakao.maps.event.addListener(marker, 'click', () => {
            selectMarker(markerKey);
            closeOverlay();
            setBottomSheetSnap(3);

            const currentLevel = map.getLevel();
            const zoomStep = currentLevel >= 8 ? 3 : 2;

            map.setCenter(latLng);
            map.setLevel(Math.max(1, currentLevel - zoomStep));
        });

        return {
            type: 'cluster',
            shops: [cluster],
            marker,
            defaultImg,
            selectedImg
        };
    }

    function redrawMarkers() {
        markerStore.forEach(item => item.marker.setMap(null));
        markerStore.forEach(item => item.marker.setMap(map));
    }

    function fetchShops(params) {
        if (activeMapRequest && activeMapRequest.readyState !== 4) {
            activeMapRequest.abort();
        }

        const requestId = ++mapRequestSeq;

        return new Promise(resolve => {
            activeMapRequest = $.ajax({
                url: API_URL,
                type: 'POST',
                dataType: 'json',
                data: {
                    act: 'map',
                    zoomLevel: map.getLevel(),
                    ...params
                }
            });

            activeMapRequest.done(function(json) {
                if (requestId !== mapRequestSeq) {
                    resolve(null);
                    return;
                }

                if (!json || !json.success) {
                    ModalUtil.alert({
                        title: '오류',
                        message: json?.message || '매장 정보를 불러오지 못했습니다.',
                        okText: '확인'
                    });
                    resolve(null);
                    return;
                }

                resolve({
                    mode: json.data?.mode || 'shop',
                    shops: json.data?.shops || [],
                    clusters: json.data?.clusters || [],
                    bounds: json.data?.bounds || null,
                    total: Number(json.data?.total || 0)
                });
            });

            activeMapRequest.fail(function(xhr, textStatus) {
                if (textStatus === 'abort') {
                    resolve(null);
                    return;
                }

                ModalUtil.alert({
                    title: '오류',
                    message: '매장 정보를 불러오지 못했습니다.',
                    okText: '확인'
                });
                resolve(null);
            });
        });
    }

    async function renderMarkersForResponse(result) {
        const nextStore = new Map();
        const nextSignatures = new Map();

        closeOverlay();
        resetMarkerStyles();

        const clusterItems = (result.mode === 'cluster' || result.mode === 'mixed')
            ? (result.clusters || [])
            : [];

        const shopItems = (result.mode === 'shop' || result.mode === 'mixed')
            ? (result.shops || [])
            : [];

        for (const cluster of clusterItems) {
            const markerKey = `cluster:${cluster.idx}`;
            const signature = [
                cluster.idx,
                Number(cluster.cluster_count || 0),
                Number(cluster.lat).toFixed(6),
                Number(cluster.lng).toFixed(6)
            ].join('|');

            nextSignatures.set(markerKey, signature);

            const prevSignature = markerSignatureStore.get(markerKey);
            const prevItem = markerStore.get(markerKey);

            if (prevItem && prevSignature === signature) {
                prevItem.shops = [cluster];
                nextStore.set(markerKey, prevItem);
                continue;
            }

            if (prevItem && prevItem.marker) {
                prevItem.marker.setMap(null);
            }

            nextStore.set(markerKey, createServerClusterMarker(markerKey, cluster));
        }

        const grouped = {};

        shopItems.forEach(shop => {
            const groupKey = `${Number(shop.lat).toFixed(6)}_${Number(shop.lng).toFixed(6)}`;
            if (!grouped[groupKey]) grouped[groupKey] = [];
            grouped[groupKey].push(shop);
        });

        for (const [groupKey, shops] of Object.entries(grouped)) {
            const markerKey = `shop:${groupKey}`;
            const firstShop = shops[0];

            const signature = [
                shops.length,
                firstShop.mt_appr || '',
                Number(firstShop.lat).toFixed(6),
                Number(firstShop.lng).toFixed(6)
            ].join('|');

            nextSignatures.set(markerKey, signature);

            const prevSignature = markerSignatureStore.get(markerKey);
            const prevItem = markerStore.get(markerKey);

            if (prevItem && prevSignature === signature) {
                prevItem.shops = shops;
                nextStore.set(markerKey, prevItem);
                continue;
            }

            if (prevItem && prevItem.marker) {
                prevItem.marker.setMap(null);
            }

            nextStore.set(markerKey, await createShopGroupMarker(markerKey, groupKey, shops));
        }

        markerStore.forEach((oldItem, oldKey) => {
            if (!nextStore.has(oldKey) && oldItem.marker) {
                oldItem.marker.setMap(null);
            }
        });

        markerStore = nextStore;
        markerSignatureStore = nextSignatures;
        redrawMarkers();
    }

    function renderClusterHintList(total, isMixed = false) {
        const totalText = total > 0
            ? `현재 범위 매장 약 ${Number(total).toLocaleString()}개`
            : '현재 범위 매장이 많습니다.';

        const subText = isMixed
            ? '밀집 지역은 클러스터로, 그 외 지역은 마커로 표시됩니다.'
            : '현재 레벨에서는 클러스터만 표시됩니다.';

        $list.html(`
            <li style="padding:20px;text-align:center;color:#666;">
                ${totalText}<br>
                ${subText}<br>
                지도를 더 확대하면 실제 매장을 더 자세히 볼 수 있습니다.
            </li>
        `);

        setBottomSheetSnap(1);
    }

    async function loadVisibleShops(options = {}) {
        const { force = false, showList = false } = options;
        if (!map) return;

        const bounds = getBoundsParams();
        const selected = getAppliedRegionParams();
        const zoomLevel = map.getLevel();

        const params = {
            ...bounds,
            ...getRefPos()
        };

        if (lastKeyword) params.keyword = lastKeyword;
        if (selected.region) params.region = selected.region;
        if (selected.district) params.district = selected.district;

        const querySignature = makeQuerySignature(
            lastKeyword,
            selected.region,
            selected.district,
            zoomLevel
        );

        if (
            !force &&
            lastLoadedBounds &&
            lastQuerySignature === querySignature &&
            isInsideLoadedBounds(bounds, lastLoadedBounds)
        ) {
            if (showList) {
                if (lastResponseMode === 'shop') {
                    renderList(lastShops);
                } else {
                    renderClusterHintList(lastTotalCount, lastResponseMode === 'mixed');
                }
            }
            return;
        }

        const result = await fetchShops(params);
        if (!result) return;

        lastLoadedBounds = result.bounds;
        lastQuerySignature = querySignature;
        lastResponseMode = result.mode;
        lastShops = result.shops || [];
        lastClusters = result.clusters || [];
        lastTotalCount = result.total;

        await renderMarkersForResponse(result);

        if (showList) {
            if (result.mode === 'shop') {
                renderList(lastShops);
            } else {
                renderClusterHintList(result.total, result.mode === 'mixed');
            }
        }
    }

    function scheduleVisibleLoad() {
        if (mapIdleTimer) {
            clearTimeout(mapIdleTimer);
        }

        mapIdleTimer = setTimeout(() => {
            loadVisibleShops();
        }, 180);
    }

    function findNearestShop(shops) {
        if (!Array.isArray(shops) || shops.length === 0) return null;

        const ref = currentPos
            ? { lat: Number(currentPos.lat), lng: Number(currentPos.lng) }
            : (() => {
                const center = map.getCenter();
                return { lat: center.getLat(), lng: center.getLng() };
            })();

        const nearest = shops.reduce((best, shop) => {
            const lat = Number(shop.lat);
            const lng = Number(shop.lng);
            const dist = Math.pow(lat - ref.lat, 2) + Math.pow(lng - ref.lng, 2);

            if (!best || dist < best.dist) {
                return { shop, dist };
            }

            return best;
        }, null);

        return nearest ? nearest.shop : null;
    }

    function moveToNearestShop(shops) {
        const nearest = findNearestShop(shops);
        if (!nearest) return;

        closeOverlay();
        resetMarkerStyles();

        lastLoadedBounds = null;
        lastQuerySignature = '';

        if (map.getLevel() > 5) {
            map.setLevel(5);
        }

        map.setCenter(new kakao.maps.LatLng(nearest.lat, nearest.lng));
    }

    async function runSearchAndFocusNearest() {
        if (!map) return;

        const selected = getAppliedRegionParams();
        let result = null;

        if (selected.region || selected.district) {
            result = await fetchShops({
                searchMode: 'region',
                region: selected.region,
                district: selected.district,
                keyword: lastKeyword,
                ...getRefPos()
            });
        } else if (lastKeyword) {
            const center = map.getCenter();

            result = await fetchShops({
                lat: center.getLat(),
                lng: center.getLng(),
                radius: 50000,
                keyword: lastKeyword,
                ...getRefPos()
            });
        } else {
            await loadVisibleShops({ force: true, showList: true });
            return;
        }

        if (!result) return;

        lastResponseMode = 'shop';
        lastShops = result.shops || [];
        lastClusters = [];
        lastTotalCount = lastShops.length;

        renderList(lastShops);

        if (lastShops.length > 0) {
            moveToNearestShop(lastShops);
        }
    }

    function makeMyLocationMarkerImage() {
        const size = 16;
        const canvas = document.createElement('canvas');
        canvas.width = size;
        canvas.height = size;
        const ctx = canvas.getContext('2d');

        ctx.beginPath();
        ctx.arc(size / 2, size / 2, size / 2 - 1, 0, Math.PI * 2);
        ctx.fillStyle = '#fff';
        ctx.fill();

        ctx.beginPath();
        ctx.arc(size / 2, size / 2, size / 2 - 3, 0, Math.PI * 2);
        ctx.fillStyle = '#4A90E2';
        ctx.fill();

        return canvas.toDataURL();
    }

    const MY_LOCATION_IMG = makeMyLocationMarkerImage();
    const MY_LOCATION_MARKER_SIZE = new kakao.maps.Size(16, 16);
    const MY_LOCATION_MARKER_OFFSET = new kakao.maps.Point(8, 8);
    const myLocationMarkerImage = new kakao.maps.MarkerImage(
        MY_LOCATION_IMG,
        MY_LOCATION_MARKER_SIZE,
        { offset: MY_LOCATION_MARKER_OFFSET }
    );

    function setMyLocationMarker(lat, lng) {
        const pos = new kakao.maps.LatLng(lat, lng);

        if (!myMarker) {
            myMarker = new kakao.maps.Marker({
                position: pos,
                image: myLocationMarkerImage,
                zIndex: Z_INDEX_MY_LOCATION
            });
            myMarker.setMap(map);
        } else {
            myMarker.setPosition(pos);
        }
    }

    function updateAppGpsMarker(lat, lng, moveCenter = false) {
        if (!map) return;

        const parsedLat = parseFloat(lat);
        const parsedLng = parseFloat(lng);
        if (!parsedLat || !parsedLng) return;

        const pos = new kakao.maps.LatLng(parsedLat, parsedLng);

        currentPos = { lat: parsedLat, lng: parsedLng };
        appGpsLatest = { lat: parsedLat, lng: parsedLng };
        window.__RN_GPS = { lat: parsedLat, lng: parsedLng };

        if (!appGpsMarker) {
            appGpsMarker = new kakao.maps.Marker({
                position: pos,
                image: myLocationMarkerImage,
                zIndex: Z_INDEX_MY_LOCATION
            });
            appGpsMarker.setMap(map);
        } else {
            appGpsMarker.setPosition(pos);
            appGpsMarker.setZIndex(Z_INDEX_MY_LOCATION);
        }

        setMyLocationMarker(parsedLat, parsedLng);

        const lastPoint = appGpsPath.length ? appGpsPath[appGpsPath.length - 1] : null;
        const isSamePoint =
            lastPoint &&
            lastPoint.getLat().toFixed(7) === parsedLat.toFixed(7) &&
            lastPoint.getLng().toFixed(7) === parsedLng.toFixed(7);

        if (!isSamePoint) {
            appGpsPath.push(pos);
        }

        if (moveCenter) {
            map.panTo(pos);
        }
    }

    function requestAppLocation() {
        if (window.ReactNativeWebView && window.ReactNativeWebView.postMessage) {
            const payload = { type: 'REQUEST_LOCATION' };
            window.ReactNativeWebView.postMessage(JSON.stringify(payload));
            return true;
        }
        return false;
    }

    window.updateUserLocation = function(lat, lng) {
        updateAppGpsMarker(lat, lng, false);
    };

    window.addEventListener('FROM_APP', function(event) {
        const detail = event.detail || {};

        if (detail.type !== 'appLocation' || !detail.ok) return;

        const shouldMoveCenter = detail.moveCenter === true;
        const shouldReloadShops = detail.reloadShops === true;

        updateAppGpsMarker(detail.lat, detail.lng, shouldMoveCenter);

        if (shouldReloadShops) {
            kakao.maps.event.addListener(map, 'idle', function onAppGpsIdle() {
                kakao.maps.event.removeListener(map, 'idle', onAppGpsIdle);
                lastLoadedBounds = null;
                lastQuerySignature = '';
                loadVisibleShops({ force: true });
            });
        }
    });

    function showSingleShop(shop) {
        map.panTo(new kakao.maps.LatLng(shop.lat, shop.lng));
        renderList([shop]);
    }

    function renderList(shops) {
        $list.empty();

        if (!shops.length) {
            $list.append('<li style="padding:20px;text-align:center;color:#666;">매장이 없습니다.</li>');
            setBottomSheetSnap(1);
            return;
        }

        const snapIdx = shops.length === 1 ? 1 : 2;

        shops.forEach(shop => {
            const imagesHtml = (shop.images || []).map((src, i) => `
                <div class="slide flex-shrink-0">
                    <div class="${i === 0 ? 'ratio-3-2' : 'ratio-1-1'} slide-img">
                        <img src="${src}" alt="매장사진">
                    </div>
                </div>
            `).join('');

            const html = `
                <li>
                    <div class="shop_box" data-shop-id="${shop.idx}">
                        <div class="drag-slider">
                            ${imagesHtml}
                            <div class="slide flex-shrink-0">
                                <div class="ratio-1-1 slide-img">
                                    <a href="../shop/list.php?sh_idx=${shop.idx}" class="linkbox">
                                        <img src="<?=DESIGN_HTTP?>/img/ico_shop.png" style="width:38px"><br>메뉴 둘러보기
                                    </a>
                                </div>
                            </div>
                        </div>
                        <a href="../shop/list.php?sh_idx=${shop.idx}" class="d-block">
                            <div class="txt_box">
                                <p>${escapeHtml(shop.name)}${shop.tel ? `<span class="pl-2"><a href="tel:${shop.tel}" class="fs_13 tg_400">${escapeHtml(shop.tel)}</a></span>` : ''}</p>
                                <div class="d-flex align-items-center justify-content-between">
                                    <p class="tg_400 fs_13 mt-1">거리 ${fmtDistance(shop.distance_m)}</p>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <p class="fs_14 fw_500 tg_400">${escapeHtml(shop.open_time || '')}</p>
                                        <p>
                                            ${shop.takeout ? `<a href="#" onclick="pageList('takeout', ${shop.idx}); return false;"><span class="badg sm ml-1">포장 접수</span></a>` : ''}
                                            ${shop.reservation ? `<a href="#" onclick="pageList('reservation', ${shop.idx}); return false;"><span class="badg sm ml-1">예약 접수</span></a>` : ''}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </li>
            `;

            const $li = $(html);

            $li.find('.shop_box').on('click', e => {
                if (!$(e.target).closest('a').length) {
                    e.preventDefault();
                }
            });

            $list.append($li);
        });

        initDragSliders();
        setBottomSheetSnap(snapIdx);
        applyNaverScrollPolicy();
    }

    function pageList(key, idx) {
        $.ajax({
            url: API_URL,
            type: 'POST',
            data: { act: 'changList', key },
            dataType: 'json',
            success: res => {
                if (res?.success) {
                    location.href = `../shop/list.php?sh_idx=${idx}`;
                }
            }
        });
    }

    function syncRegionButton() {
        if (!appliedRegionValue) {
            $btnRegionDefault.removeClass('d-none');
            $btnRegionActive.addClass('d-none');
            $selectedRegionText.text('');
            return;
        }

        $btnRegionDefault.addClass('d-none');
        $btnRegionActive.removeClass('d-none');
        $selectedRegionText.text(appliedDistrictLabel || `${appliedRegionLabel}전체`);
    }

    function renderRegionDepth1() {
        $regionDepth1.empty();

        REGION_FILTERS.forEach((region, index) => {
            const activeClass = index === draftRegionIndex ? 'active' : '';
            $regionDepth1.append(
                `<a href="#" class="${activeClass}" data-index="${index}">${escapeHtml(region.label)}</a>`
            );
        });

        renderRegionDepth2();
    }

    function renderRegionDepth2() {
        $regionDepth2.empty();

        const region = REGION_FILTERS[draftRegionIndex];
        if (!region) return;

        const allLabel = `${region.label}전체`;

        $regionDepth2.append(
            `<a href="#" class="${draftDistrictValue === '' ? 'active' : ''}" data-value="" data-label="${allLabel}">${allLabel}</a>`
        );

        region.districts.forEach(district => {
            const activeClass = district === draftDistrictValue ? 'active' : '';
            $regionDepth2.append(
                `<a href="#" class="${activeClass}" data-value="${district}" data-label="${district}">${district}</a>`
            );
        });
    }

    function setDraftFromApplied() {
        if (!appliedRegionValue) {
            draftRegionIndex = 0;
            draftDistrictValue = '';
            draftDistrictLabel = REGION_FILTERS[0] ? `${REGION_FILTERS[0].label}전체` : '';
            return;
        }

        const foundIndex = REGION_FILTERS.findIndex(item => item.value === appliedRegionValue);
        draftRegionIndex = foundIndex >= 0 ? foundIndex : 0;
        draftDistrictValue = appliedDistrictValue || '';
        draftDistrictLabel = appliedDistrictLabel || `${REGION_FILTERS[draftRegionIndex].label}전체`;
    }

    function detectLocation() {
        const dfd = $.Deferred();

        if (APP_LAT && APP_LNG) {
            currentPos = {
                lat: parseFloat(APP_LAT),
                lng: parseFloat(APP_LNG)
            };

            window.__RN_GPS = {
                lat: parseFloat(APP_LAT),
                lng: parseFloat(APP_LNG)
            };

            return dfd.resolve(currentPos).promise();
        }

        if (window.__RN_GPS?.lat && window.__RN_GPS?.lng) {
            currentPos = {
                lat: parseFloat(window.__RN_GPS.lat),
                lng: parseFloat(window.__RN_GPS.lng)
            };

            return dfd.resolve(currentPos).promise();
        }

        if (!navigator.geolocation) {
            return dfd.resolve(null).promise();
        }

        navigator.geolocation.getCurrentPosition(
            pos => {
                currentPos = {
                    lat: pos.coords.latitude,
                    lng: pos.coords.longitude
                };
                dfd.resolve(currentPos);
            },
            () => dfd.resolve(null),
            {
                enableHighAccuracy: true,
                timeout: 12000,
                maximumAge: 0
            }
        );

        return dfd.promise();
    }

    function initDragSliders() {
        document.querySelectorAll('.drag-slider').forEach(slider => {
            if (slider.__binded) return;
            slider.__binded = true;

            let isDown = false;
            let startX = 0;
            let startY = 0;
            let scrollLeft = 0;
            let lock = null;
            const threshold = 6;

            slider.style.touchAction = 'pan-y';

            const start = (x, y) => {
                isDown = true;
                lock = null;
                startX = x;
                startY = y;
                scrollLeft = slider.scrollLeft;
            };

            const move = (x, y, ev) => {
                if (!isDown) return;

                const dx = x - startX;
                const dy = y - startY;

                if (!lock) {
                    if (Math.abs(dx) < threshold && Math.abs(dy) < threshold) return;
                    lock = Math.abs(dx) > Math.abs(dy) ? 'x' : 'y';
                }

                if (lock === 'x') {
                    ev.preventDefault();
                    slider.scrollLeft = scrollLeft - dx * 1.2;
                }
            };

            const end = () => {
                isDown = false;
            };

            slider.addEventListener('mousedown', e => start(e.pageX, e.pageY));
            slider.addEventListener('mousemove', e => move(e.pageX, e.pageY, e));
            slider.addEventListener('mouseup', end);
            slider.addEventListener('mouseleave', end);

            slider.addEventListener('touchstart', e => {
                const t = e.touches[0];
                start(t.pageX, t.pageY);
            }, { passive: true });

            slider.addEventListener('touchmove', e => {
                const t = e.touches[0];
                move(t.pageX, t.pageY, e);
            }, { passive: false });

            slider.addEventListener('touchend', end, { passive: true });
            slider.addEventListener('touchcancel', end, { passive: true });
        });
    }

    function applyNaverScrollPolicy() {
        const listEl = document.getElementById('shopList');
        if (!listEl) return;

        if (currentSnapIndex === 0) {
            listEl.style.overflowY = 'auto';
            listEl.style.webkitOverflowScrolling = 'touch';
            listEl.style.touchAction = 'pan-y';
        } else {
            listEl.scrollTop = 0;
            listEl.style.overflowY = 'hidden';
        }

        if (sheet) {
            sheet.style.overflow = 'hidden';
        }
    }

    function setTranslateY(value, transition = true) {
        if (!sheet) return;

        currentTranslateY = Math.min(97, Math.max(0, value));
        sheet.classList.toggle('no-transition', !transition);
        sheet.style.setProperty('--sheet-translateY', currentTranslateY + '%');

        requestAnimationFrame(applyNaverScrollPolicy);
    }

    function setBottomSheetSnap(index) {
        if (index === currentSnapIndex) return;
        currentSnapIndex = index;
        setTranslateY(SNAP_POINTS[index], true);
    }

    function initBottomSheet() {
        if (!sheet) return;

        const handle = sheet.querySelector('.map_touchbar');
        const mapBtn = sheet.querySelector('.mapturn');
        const listEl = document.getElementById('shopList');

        let isDragging = false;
        let startY = 0;
        let startTranslateY = currentTranslateY;

        const pointerDown = clientY => {
            isDragging = true;
            startY = clientY;
            startTranslateY = currentTranslateY;
            sheet.classList.add('no-transition');
        };

        const pointerMove = clientY => {
            if (!isDragging) return;
            setTranslateY(startTranslateY + (clientY - startY) / window.innerHeight * 100, false);
        };

        const pointerUp = () => {
            if (!isDragging) return;

            isDragging = false;
            sheet.classList.remove('no-transition');

            let nearest = 0;
            let minDiff = Infinity;

            SNAP_POINTS.forEach((point, index) => {
                const diff = Math.abs(currentTranslateY - point);
                if (diff < minDiff) {
                    minDiff = diff;
                    nearest = index;
                }
            });

            setBottomSheetSnap(nearest);
        };

        if (handle) {
            handle.addEventListener('mousedown', e => {
                pointerDown(e.clientY);

                const move = ev => pointerMove(ev.clientY);
                const up = () => {
                    pointerUp();
                    window.removeEventListener('mousemove', move);
                    window.removeEventListener('mouseup', up);
                };

                window.addEventListener('mousemove', move);
                window.addEventListener('mouseup', up);
            });

            handle.addEventListener('touchstart', e => {
                pointerDown(e.touches[0].clientY);

                const move = ev => {
                    pointerMove(ev.touches[0].clientY);
                    ev.preventDefault();
                };

                const up = () => {
                    pointerUp();
                    window.removeEventListener('touchmove', move);
                    window.removeEventListener('touchend', up);
                };

                window.addEventListener('touchmove', move, { passive: false });
                window.addEventListener('touchend', up);
            });
        }

        if (mapBtn) {
            mapBtn.addEventListener('click', () => {
                setBottomSheetSnap(currentSnapIndex === 0 ? 3 : 0);
            });
        }

        if (listEl) {
            let touchStartY = 0;

            listEl.addEventListener('touchstart', e => {
                touchStartY = e.touches[0].clientY;
                applyNaverScrollPolicy();
            }, { passive: true });

            listEl.addEventListener('touchmove', e => {
                if (currentSnapIndex === 0) return;

                if (e.touches[0].clientY - touchStartY < -10) {
                    setBottomSheetSnap(0);
                }

                e.preventDefault();
            }, { passive: false });
        }

        applyNaverScrollPolicy();
    }

    function initMapEvents() {
        kakao.maps.event.addListener(map, 'dragstart', () => {
            closeOverlay();
            resetMarkerStyles();
            setBottomSheetSnap(3);
        });

        kakao.maps.event.addListener(map, 'zoom_start', () => {
            closeOverlay();
            resetMarkerStyles();
            setBottomSheetSnap(3);
        });

        kakao.maps.event.addListener(map, 'idle', () => {
            if (isBootstrapping) return;
            scheduleVisibleLoad();
        });

        kakao.maps.event.addListener(map, 'click', () => {
            closeOverlay();
            resetMarkerStyles();
        });
    }

    function init() {
        let initLat = 37.5665;
        let initLng = 126.9780;

        syncRegionButton();
        setDraftFromApplied();
        renderRegionDepth1();

        detectLocation().then(loc => {
            if (loc) {
                initLat = loc.lat;
                initLng = loc.lng;
                currentPos = loc;
            }

            map = new kakao.maps.Map(document.getElementById('kakaoMap'), {
                center: new kakao.maps.LatLng(initLat, initLng),
                level: loc ? 5 : 7
            });

            if (loc) {
                setMyLocationMarker(loc.lat, loc.lng);
                updateAppGpsMarker(loc.lat, loc.lng, false);
            }

            initMapEvents();

            let initialized = false;

            const doInit = async () => {
                if (initialized) return;
                initialized = true;

                await loadVisibleShops({ force: true });
                initBottomSheet();
                isBootstrapping = false;
            };

            kakao.maps.event.addListener(map, 'idle', function onFirstIdle() {
                kakao.maps.event.removeListener(map, 'idle', onFirstIdle);
                map.relayout();
                map.setCenter(new kakao.maps.LatLng(initLat, initLng));
                doInit();
            });

            setTimeout(doInit, 500);
        }).catch(() => {
            ModalUtil.alert({
                title: '오류',
                message: '지도를 불러오지 못했습니다.',
                okText: '확인'
            });
        });
    }

    $('#btnGps').on('click', () => {
        if (APP_OS) {
            const requested = requestAppLocation();

            if (!requested && appGpsLatest) {
                map.panTo(new kakao.maps.LatLng(appGpsLatest.lat, appGpsLatest.lng));
                kakao.maps.event.addListener(map, 'idle', function onGpsIdle() {
                    kakao.maps.event.removeListener(map, 'idle', onGpsIdle);
                    lastLoadedBounds = null;
                    lastQuerySignature = '';
                    loadVisibleShops({ force: true });
                });
            }
            return;
        }

        detectLocation().then(pos => {
            if (!pos) return;

            currentPos = pos;
            setMyLocationMarker(pos.lat, pos.lng);
            map.panTo(new kakao.maps.LatLng(pos.lat, pos.lng));

            kakao.maps.event.addListener(map, 'idle', function onGpsIdle() {
                kakao.maps.event.removeListener(map, 'idle', onGpsIdle);
                lastLoadedBounds = null;
                lastQuerySignature = '';
                loadVisibleShops({ force: true });
            });
        });
    });

    $('#btnSearchHere').on('click', () => {
        loadVisibleShops({ force: true, showList: true });
    });

    $mapSearchForm.on('submit', e => {
        e.preventDefault();

        lastKeyword = $kw.val().trim();

        $kw.blur();
        if (document.activeElement) {
            document.activeElement.blur();
        }

        lastLoadedBounds = null;
        lastQuerySignature = '';

        runSearchAndFocusNearest();
    });

    $regionModal.on('show.bs.modal', function() {
        setDraftFromApplied();
        renderRegionDepth1();
    });

    $(document).on('click', '#regionDepth1 a', function(e) {
        e.preventDefault();

        draftRegionIndex = Number($(this).data('index'));
        draftDistrictValue = '';
        draftDistrictLabel = `${REGION_FILTERS[draftRegionIndex].label}전체`;

        $('#regionDepth1 a').removeClass('active');
        $(this).addClass('active');

        renderRegionDepth2();
    });

    $(document).on('click', '#regionDepth2 a', function(e) {
        e.preventDefault();

        $('#regionDepth2 a').removeClass('active');
        $(this).addClass('active');

        draftDistrictValue = String($(this).data('value') || '');
        draftDistrictLabel = String($(this).data('label') || '');
    });

    $btnRegionApply.on('click', function() {
        const region = REGION_FILTERS[draftRegionIndex];
        if (!region) return;

        appliedRegionValue = region.value;
        appliedRegionLabel = region.label;
        appliedDistrictValue = draftDistrictValue;
        appliedDistrictLabel = draftDistrictLabel || `${region.label}전체`;

        lastLoadedBounds = null;
        lastQuerySignature = '';

        syncRegionButton();
        $regionModal.modal('hide');

        loadVisibleShops({ force: true });
    });

    $btnRegionReset.on('click', function() {
        draftRegionIndex = 0;
        draftDistrictValue = '';
        draftDistrictLabel = REGION_FILTERS[0] ? `${REGION_FILTERS[0].label}전체` : '';

        appliedRegionValue = '';
        appliedRegionLabel = '';
        appliedDistrictValue = '';
        appliedDistrictLabel = '';

        lastLoadedBounds = null;
        lastQuerySignature = '';

        syncRegionButton();
        renderRegionDepth1();
        $regionModal.modal('hide');

        loadVisibleShops({ force: true });
    });

    $(init);
</script>


