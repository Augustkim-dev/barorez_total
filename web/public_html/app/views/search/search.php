<?php
$orderMode = $_GET['mode'] ?? '';
$orderMode = in_array($orderMode, ['reservation', 'takeout'], true) ? $orderMode : '';
$orderModeLabel = $orderMode === 'reservation' ? '예약' : ($orderMode === 'takeout' ? '포장' : '');
?>
<div class="wrap">
    <div class="sub_pg search_pg pb-0">
        <div class="search_hd">
            <div class="d-flex align-items-center">
                <button type="button"
                        class="btn mapbtn mr-2 rounded-pill"
                        id="btnRegionDefault"
                        data-toggle="modal"
                        data-target="#pop_region">
                    지역선택
                    <img src="<?=DESIGN_HTTP?>/img/arrow_down.svg" class="ml-2">
                </button>

                <button type="button"
                        class="btn mapbtn act mr-2 rounded-pill d-none"
                        id="btnRegionActive"
                        data-toggle="modal"
                        data-target="#pop_region">
                    <span id="selectedRegionText"></span>
                    <img src="<?=DESIGN_HTTP?>/img/arrow_down.svg" class="ml-2">
                </button>

                <form class="sch_ip sch_gray align-items-center flex-fill" id="searchForm">
                    <input type="search"
                           id="kw"
                           class="form-control fs_15 flex-fill border-0"
                           placeholder="매장명, 메뉴명으로 검색해보세요">
                    <button class="btn btn-icon flex-shrink-0" type="submit">
                        <img src="<?=DESIGN_HTTP?>/img/ic_sch_gray.png" style="width:1.8rem;">
                    </button>
                </form>
            </div>

            <div class="search_state">
                <?php if ($orderModeLabel): ?>
                    <span class="search_mode_badge"><?=$orderModeLabel?></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="search_body">
            <ul class="shop_list scroll_y_bar2 search_result_list" id="shopList"></ul>
        </div>
    </div>
</div>

<link rel="stylesheet" href="<?=DESIGN_HTTP?>/css/search.css?v=3">

<div class="modal modal_bottom fade search-region-modal"
     id="pop_region"
     tabindex="-1"
     aria-hidden="true"
     data-backdrop="static">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">지역 선택</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <img src="<?=DESIGN_HTTP?>/img/ico_x.png" alt="닫기" style="width:18px">
                </button>
            </div>

            <div class="modal-body p-0">
                <div class="loc_wp">
                    <div class="deps1" id="regionDepth1"></div>
                    <div class="deps2" id="regionDepth2"></div>
                </div>
            </div>

            <div class="modal-footer pt-3 border-top">
                <div class="form-row">
                    <div class="col-4">
                        <button type="button" class="btn btn-outline-light btn-block" id="btnRegionReset">
                            <img src="<?=DESIGN_HTTP?>/img/re.svg" alt="초기화" class="mr-2"> 초기화
                        </button>
                    </div>
                    <div class="col-8">
                        <button type="button" class="btn btn-primary btn-block" id="btnRegionApply">적용</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once("../inc/modal.php");?>

<style>
    .search_pg {
        min-height: 100vh;
        background: #f7f7f7;
    }
    .search_hd {
        position: sticky;
        top: 0;
        z-index: 20;
        background: #fff;
        padding: 1.6rem;
        border-bottom: 1px solid #f1f1f1;
    }
    .search_state {
        margin-top: 1rem;
        display: flex;
        align-items: center;
        gap: .8rem;
        flex-wrap: wrap;
    }
    .search_mode_badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 4.8rem;
        height: 2.8rem;
        padding: 0 1rem;
        background: #FF4516;
        color: #fff;
        border-radius: 999px;
        font-size: 1.2rem;
        font-weight: 600;
    }
    .search_summary {
        color: #666;
        font-size: 1.3rem;
    }
    .search_body {
        padding: 1.6rem;
    }
    .search_result_list {
        height: auto;
        max-height: none;
        overflow: visible;
    }
    .search_result_list > li + li {
        margin-top: 1.2rem;
    }
    .search-empty {
        padding: 6rem 1.6rem;
        text-align: center;
        color: #777;
        font-size: 1.4rem;
    }
    .search_cta {
        margin-top: 1.2rem;
        display: flex;
        justify-content: flex-end;
    }
</style>

<script>
    const API_URL = '<?=MAP_ACTIONS?>/update.php';
    const APP_OS  = '<?=$_SESSION['app_os'] ?? ''?>';
    const APP_LAT = '<?=isset($_SESSION['app_lat']) ? $_SESSION['app_lat'] : ''?>';
    const APP_LNG = '<?=isset($_SESSION['app_lng']) ? $_SESSION['app_lng'] : ''?>';
    const PAGE_MODE = '<?=$orderMode?>';

    const $list = $('#shopList');
    const $kw = $('#kw');
    const $searchForm = $('#searchForm');
    const $btnRegionDefault = $('#btnRegionDefault');
    const $btnRegionActive = $('#btnRegionActive');
    const $selectedRegionText = $('#selectedRegionText');
    const $regionDepth1 = $('#regionDepth1');
    const $regionDepth2 = $('#regionDepth2');
    const $btnRegionApply = $('#btnRegionApply');
    const $btnRegionReset = $('#btnRegionReset');
    const $regionModal = $('#pop_region');
    const $searchSummary = $('#searchSummary');

    let currentPos = null;
    let lastKeyword = '';

    let draftRegionIndex = 0;
    let draftDistrictValue = '';
    let draftDistrictLabel = '';

    let appliedRegionValue = '';
    let appliedRegionLabel = '';
    let appliedDistrictValue = '';
    let appliedDistrictLabel = '';

    const escapeHtml = str => String(str || '')
        .replace(/&/g,'&amp;')
        .replace(/</g,'&lt;')
        .replace(/>/g,'&gt;')
        .replace(/"/g,'&quot;')
        .replace(/'/g,'&#039;');

    const fmtDistance = m => (m = Number(m || 0)) >= 1000
        ? (m / 1000).toFixed(1) + 'km'
        : Math.round(m) + 'm';

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

    const getRefPos = () => currentPos ? {
        refLat: currentPos.lat,
        refLng: currentPos.lng
    } : {};

    const syncOrderMode = (mode) => {
        if (!mode) return $.Deferred().resolve().promise();

        return $.ajax({
            url: API_URL,
            type: 'POST',
            data: { act: 'changList', key: mode },
            dataType: 'json'
        });
    };

    const fetchShops = (params) => {
        return $.post(API_URL, { act: 'map', ...params })
            .then(json => {
                if (!json.success) {
                    throw new Error(json.message || 'API 오류');
                }
                return json.data?.shops || [];
            })
            .catch(() => {
                ModalUtil.alert({
                    title: '오류',
                    message: '매장 정보를 불러오지 못했습니다.',
                    okText: '확인'
                });
                return [];
            });
    };

    const filterByMode = (shops) => {
        if (PAGE_MODE === 'reservation') return shops.filter(shop => shop.reservation);
        if (PAGE_MODE === 'takeout') return shops.filter(shop => shop.takeout);
        return shops;
    };

    const updateSummary = (count, keyword = '') => {
        if (!appliedRegionValue) {
            $searchSummary.text('지역을 먼저 선택해주세요.');
            return;
        }

        const regionText = appliedDistrictLabel || `${appliedRegionLabel}전체`;

        if (keyword) {
            $searchSummary.text(`${regionText}에서 "${keyword}" 검색 결과 ${count}개`);
        } else {
            $searchSummary.text(`${regionText} 매장 ${count}개`);
        }
    };

    const renderEmpty = (message) => {
        $list.html(`<li class="search-empty">${message}</li>`);
    };

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

            const end = () => { isDown = false; };

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

    const renderList = (shops) => {
        $list.empty();

        if (!shops.length) {
            renderEmpty(PAGE_MODE === 'reservation'
                ? '예약 가능한 매장이 없습니다.'
                : PAGE_MODE === 'takeout'
                    ? '포장 가능한 매장이 없습니다.'
                    : '검색 결과가 없습니다.');
            return;
        }

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
                    <div class="shop_box">
                        <div class="drag-slider">
                            ${imagesHtml}
                            <div class="slide flex-shrink-0">
                                <div class="ratio-1-1 slide-img">
                                    <a href="../shop/list.php?sh_idx=${shop.idx}" class="linkbox shop-link" data-shop-id="${shop.idx}">
                                        <img src="<?=DESIGN_HTTP?>/img/ico_shop.png" style="width:38px"><br>메뉴 둘러보기
                                    </a>
                                </div>
                            </div>
                        </div>

                        <a href="../shop/list.php?sh_idx=${shop.idx}" class="d-block shop-link" data-shop-id="${shop.idx}">
                            <div class="txt_box">
                                <p>${escapeHtml(shop.name)}${shop.tel ? `<span class="pl-2"><a href="tel:${shop.tel}" class="fs_13 tg_400">${escapeHtml(shop.tel)}</a></span>` : ''}</p>
                                <div class="d-flex align-items-center justify-content-between">
                                    <p class="tg_400 fs_13 mt-1">거리 ${fmtDistance(shop.distance_m)}</p>
                                    <p class="fs_14 fw_500 tg_400">${escapeHtml(shop.open_time || '')}</p>
                                </div>
                                <p class="tg_400 fs_13 mt-1">${escapeHtml([shop.addr1, shop.addr2].filter(Boolean).join(' '))}</p>
                            </div>
                        </a>
                    </div>
                </li>
            `;

            $list.append($(html));
        });

        initDragSliders();
    };

    const searchShops = async (keyword = '') => {
        if (!appliedRegionValue) {
            $regionModal.modal('show');
            return;
        }

        const shops = await fetchShops({
            searchMode: 'region',
            region: appliedRegionValue,
            district: appliedDistrictValue,
            keyword,
            ...getRefPos()
        });

        const filtered = filterByMode(shops);
        lastKeyword = keyword;
        updateSummary(filtered.length, keyword);
        renderList(filtered);
    };

    const detectLocation = () => {
        const dfd = $.Deferred();

        if (APP_LAT && APP_LNG) {
            currentPos = {
                lat: parseFloat(APP_LAT),
                lng: parseFloat(APP_LNG)
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
                timeout: 10000,
                maximumAge: 0
            }
        );

        return dfd.promise();
    };

    const syncRegionButton = () => {
        if (!appliedRegionValue) {
            $btnRegionDefault.removeClass('d-none');
            $btnRegionActive.addClass('d-none');
            $selectedRegionText.text('');
            return;
        }

        $btnRegionDefault.addClass('d-none');
        $btnRegionActive.removeClass('d-none');
        $selectedRegionText.text(appliedDistrictLabel || `${appliedRegionLabel}전체`);
    };

    const renderRegionDepth1 = () => {
        $regionDepth1.empty();

        REGION_FILTERS.forEach((region, index) => {
            const activeClass = index === draftRegionIndex ? 'active' : '';
            $regionDepth1.append(
                `<a href="#" class="${activeClass}" data-index="${index}">${escapeHtml(region.label)}</a>`
            );
        });

        renderRegionDepth2();
    };

    const renderRegionDepth2 = () => {
        $regionDepth2.empty();

        const region = REGION_FILTERS[draftRegionIndex];
        if (!region) return;

        const allLabel = `${region.label}전체`;

        $regionDepth2.append(
            `<a href="#" class="${draftDistrictValue === '' ? 'active' : ''}" data-value="" data-label="${allLabel}">${allLabel}</a>`
        );

        region.districts.forEach((district) => {
            const activeClass = district === draftDistrictValue ? 'active' : '';
            $regionDepth2.append(
                `<a href="#" class="${activeClass}" data-value="${district}" data-label="${district}">${district}</a>`
            );
        });
    };

    const setDraftFromApplied = () => {
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
    };

    const pageList = (key, idx) => {
        $.ajax({
            url: API_URL,
            type: 'POST',
            data: { act: 'changList', key },
            dataType: 'json',
            success: (res) => {
                if (res?.success) {
                    location.href = `../shop/list.php?sh_idx=${idx}`;
                }
            }
        });
    };

    $(document).on('click', '.shop-link', function(e) {
        if (!PAGE_MODE) return;

        e.preventDefault();
        const idx = $(this).data('shopId');
        if (idx) pageList(PAGE_MODE, idx);
    });

    $searchForm.on('submit', function(e) {
        e.preventDefault();
        searchShops($kw.val().trim());
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

        syncRegionButton();
        $regionModal.modal('hide');
        searchShops($kw.val().trim());
    });

    $btnRegionReset.on('click', function() {
        draftRegionIndex = 0;
        draftDistrictValue = '';
        draftDistrictLabel = REGION_FILTERS[0] ? `${REGION_FILTERS[0].label}전체` : '';

        appliedRegionValue = '';
        appliedRegionLabel = '';
        appliedDistrictValue = '';
        appliedDistrictLabel = '';

        syncRegionButton();
        updateSummary(0, '');
        renderRegionDepth1();
        renderEmpty('지역을 선택해주세요.');
    });

    async function init() {
        syncRegionButton();
        setDraftFromApplied();
        renderRegionDepth1();
        renderEmpty('지역을 선택해주세요.');

        await syncOrderMode(PAGE_MODE);
        await detectLocation();

        $regionModal.modal({
            backdrop: 'static',
            keyboard: false,
            show: true
        });
    }

    $(init);
</script>
