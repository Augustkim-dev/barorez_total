<?php
// 공통 변수 초기화 (기존 그대로)
$shopId   = (int)($_SHOP_ID ?? $shopId ?? ($_SESSION['current_sh_idx'] ?? 0));
$row      = $_SHOP_ROW ?? $row ?? [];
$shopImg  = $_SHOP_IMG ?? $shopImg ?? (DESIGN_HTTP . '/img/pr_sample01.jpg');

$isQr     = !empty($_SESSION['is_qr_order']) && !empty($_SESSION['qr_token']) && !empty($_SESSION['table_no']);
$tableNum = $_SESSION['table_no'] ?? '';

$DB->where('idx',$tb_idx);
$tableNo = $DB->getOne('shop_table_t','tb_name');

$mode = strtolower($_PAGE_MODE ?? ($_SESSION['order_mode'] ?? 'reservation'));
$mode = in_array($mode, ['reservation', 'takeout']) ? $mode : 'reservation';

$fullName = $row['sh_title'] . $row['sh_branch_nm'];
$shAddr   = trim($row['sh_addr1'] ?? '');

$shopLat  = (float)($row['sh_lat'] ?? 0);
$shopLng  = (float)($row['sh_lng'] ?? 0);

$hasCart  = !empty($_SESSION['cart_ct_ids']);
$cartQty  = (int)($_SESSION['cart_qty'] ?? 0);

// 카테고리 & 메뉴 로드 (기존 그대로)
$category = [];
$menus    = [];

if ($shopId > 0) {
    $DB->where('sh_idx', $shopId);
    $DB->where('sc_show', 'Y');
    $DB->where('sc_del', null, 'IS');
    $DB->orderBy('sc_order', 'DESC');
    $category = $DB->get('shop_category_t');

    $DB->join('shop_category_t c', 'm.sc_idx = c.idx', 'INNER');
    $DB->where('c.sh_idx', $shopId);
    $DB->where('m.sm_show', 'Y');
    $DB->orderBy('c.sc_order', 'DESC');
    $DB->orderBy('m.sm_order', 'DESC');
    $menus = $DB->get('shop_menu_t m', null, [
        'm.idx','m.sc_idx','m.sm_title','m.sm_image','m.sm_contents','m.sm_price','m.sm_su','m.sm_type', 'm.sm_age_show', 'm.sm_main'
    ]);
}

$menusJson = json_encode($menus, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

$reviewSummary = [
    'review_count' => 0,
    'avg_score'    => 0,
];

if ($shopId > 0) {
    $DB->where('sh_idx', $shopId);
    $DB->where('rv_show', 'Y');
    $DB->where('del_date', null, 'IS');
    $reviewSummary = $DB->getOne('review_t', 'COUNT(*) AS review_count, ROUND(IFNULL(AVG(rv_food_score), 0), 1) AS avg_score');
}

$reviewCount    = (int)($reviewSummary['review_count'] ?? 0);
$reviewAvgScore = number_format((float)($reviewSummary['avg_score'] ?? 0), 1);
$reviewListUrl  = REVIEW_PAGE.'/list.php?sh_idx=' . $shopId;
?>
<div class="wrap">
    <div class="idx_pg">
        <div class="container shop_hd">
            <?php if ($isQr): ?>
                <!-- QR 테이블 주문 헤더 -->
                <div class="d-flex align-items-center">
                    <div class="mr-2">
                        <p class="fs_20 fw_700"><?= htmlspecialchars($fullName) ?></p>
                        <p class="text-primary fs_15 fw_500 mt-2">테이블 <?= htmlspecialchars($tableNo['tb_name']) ?>번</p>
                    </div>
                    <div class="ml-auto item_img">
                        <div class="rect rounded-pill">
                            <img class="flex-shrink-0" src="<?= htmlspecialchars($shopImg) ?>">
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- 포장/예약 헤더 -->
                <div class="d-flex align-items-center">
                    <div class="mr-2">
                        <p class="fs_20 fw_700"><?= $fullName ?></p>
                        <p class="tg_500 fs_15 mt-2"><?= htmlspecialchars($shAddr ?: '주소 정보 없음') ?></p>
                    </div>
                    <div class="ml-auto item_img">
                        <div class="rect rounded-pill">
                            <img class="flex-shrink-0" src="<?= htmlspecialchars($shopImg) ?>">
                        </div>
                    </div>
                </div>
                <!-- 탭 버튼 -->
                <?php if (($_ALLOW_ORDER ?? false) && ($_ALLOW_TAKEOUT ?? false) && ($_ALLOW_RESERVATION ?? false)): ?>
                    <!-- 둘 다 가능 -->
                    <div class="ck_btn_group tabs-reserve-takeout mt-4">
                        <button type="button" class="btn btn-md btn-block tab-btn <?= $mode === 'reservation' ? 'btn-outline-primary' : '' ?>" data-tab="reserve">예약</button>
                        <button type="button" class="btn btn-md btn-block mt-0 tab-btn <?= $mode === 'takeout' ? 'btn-outline-primary' : '' ?>" data-tab="takeout">포장</button>
                    </div>

                <?php elseif (($_ALLOW_ORDER ?? false) && ($_ALLOW_RESERVATION ?? false)): ?>
                    <!-- 예약만 -->
                    <div class="ck_btn_group tabs-reserve-takeout mt-4">
                        <button type="button" class="btn btn-md btn-block btn-outline-primary tab-btn" data-tab="reserve">예약</button>
                    </div>

                <?php elseif (($_ALLOW_ORDER ?? false) && ($_ALLOW_TAKEOUT ?? false)): ?>
                    <!-- 포장만 -->
                    <div class="ck_btn_group tabs-reserve-takeout mt-4">
                        <button type="button" class="btn btn-md btn-block btn-outline-primary tab-btn" data-tab="takeout">포장</button>
                    </div>

                <?php else: ?>
                    <!-- 둘 다 불가 -->
                    <div class="mt-4">
                        <div class="alert alert-light mb-0" style="border:1px solid #eee;">
                            현재 이 매장은 <b>포장/예약 주문이 불가</b>합니다.
                        </div>
                    </div>
                <?php endif; ?>

                <!-- 예약 탭 정보 -->
                <div class="mt-4 tab-panel <?= $mode === 'reservation' ? '' : 'd-none' ?>" id="tab-reserve-info">
                    <div class="d-flex shop_story justify-content-between align-items-center">
                        <div class="tg_400 tit">운영시간</div>
                        <div class="flex-fill">
                            <?= htmlspecialchars($_TODAY_HOURS_TEXT ?? '휴무일') ?>
                        </div>
                        <a href="./info.php?sh_idx=<?= $shopId ?>" class="rounded-pill bg-light py-2 px-4 fs_13 tg_500">가게정보</a>
                    </div>
                </div>

                <!-- 포장 탭 정보 -->
                <div class="mt-3 tab-panel <?= $mode === 'takeout' ? '' : 'd-none' ?>" id="tab-takeout-info">
                    <div class="rounded mb-4 overflow-hidden" style="height:170px;">
                        <div id="takeoutMap" style="width:100%;height:100%;"></div>
                    </div>
                    <div class="d-flex shop_story justify-content-between align-items-center">
                        <div class="tg_400 tit">조리시간</div>
                        <a href="./info.php?sh_idx=<?= $shopId ?>" class="rounded-pill bg-light py-2 px-4 fs_13 tg_500">가게정보</a>
                    </div>
                    <div class="d-flex shop_story mt-3">
                        <div class="tg_400 tit">위치안내</div>
                        <div class="flex-fill text-right">
                            <p><?= htmlspecialchars($shAddr ?: '주소 정보 없음') ?><br>
                                <a href="javascript:void(0)" class="un_reboot_a tg_400 mt-2" id="btnCopyAddr">주소 복사</a>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <div class="container">
            <a href="<?= htmlspecialchars($reviewListUrl) ?>" class=" d-flex align-items-center justify-content-between py-4" style="border-top:1px solid #f3f4f6;">
                <div class="d-flex align-items-center">
                    <span class="fs_18 mr-2" style="color:#ffb100;">★</span>
                    <span class="fs_17 fw_700"><?= $reviewAvgScore ?></span>
                    <span class="fs_15 tg_400 ml-2">리뷰 <?= number_format($reviewCount) ?>개</span>
                </div>
                <img src="<?= DESIGN_HTTP ?>/img/ico_arrow1.png" class="flex-shrink-0" style="width:2rem;" alt="리뷰 보기">
            </a>
        </div>
        <div class="bar"></div>

        <!-- 카테고리 -->
        <section class="collapse_cate mb-3 mt-4">
            <div id="cate_cont" class="scroll_bar_none scroll_mouse">
                <div class="btn-group btn-group-toggle px_16" data-toggle="buttons" id="cateBtnGroup">
                    <?php foreach ($category as $index => $c): ?>
                        <label class="btn btn-outline-light btn-md rounded-pill <?= $index === 0 ? 'active' : '' ?>" data-sc-idx="<?= (int)$c['idx'] ?>">
                            <input type="radio" name="menu_cate" <?= $index === 0 ? 'checked' : '' ?>>
                            <?= htmlspecialchars($c['sc_title'] ?? '카테고리') ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <!-- 메뉴 리스트 -->
        <ul class="item_list" id="menuList"></ul>
        <!-- 하단 버튼들 -->
        <?php if ($isQr): ?>
            <?php if ($hasCart): ?>
                <div class="bottom_btn">
                    <button type="button" class="btn btn-primary btn-block btn-lg" onclick="location.href='<?=ORDER_PAGE?>/cart.php'">
                        장바구니 <span class="badge bg-white text-primary rounded-pill ml-2"><?= $cartQty ?></span>
                    </button>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <?php if ($_SESSION['mng']): ?>
                <?php if(!$hasOtherStoreCart): ?>
                <div id="tab-reserve-bottom" class="tab-bottom <?= $mode === 'reservation' ? '' : 'd-none' ?>">
                    <div class="bottom_btn">
                        <button type="button" class="btn btn-primary btn-block btn-lg"
                                onclick="location.href='<?= $hasCart ? '../order/cart.php' : '../rsrv/rsrv.php?sh_idx=' . $shopId ?>'">
                            <?= $hasCart ? '장바구니 <span class="badge bg-white text-primary rounded-pill ml-2">' . $cartQty . '</span>' : '즉시 예약' ?>
                        </button>
                    </div>
                </div>

                <div id="tab-takeout-bottom" class="tab-bottom <?= $mode === 'takeout' ? '' : 'd-none' ?>">
                    <?php if ($hasCart): ?>
                        <div class="bottom_btn">
                            <button type="button" class="btn btn-primary btn-block btn-lg" onclick="location.href='../order/cart.php'">
                                장바구니 <span class="badge bg-white text-primary rounded-pill ml-2"><?= $cartQty ?></span>
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
                <?php else: ?>
                <div class="tab-bottom">
                    <div class="bottom_btn">
                        <button type="button" class="btn btn-primary btn-block btn-lg">
                            다른 매장 장바구니가 담겨져 있습니다.<span class="badge bg-white text-primary rounded-pill ml-2"><?= $cartQty ?></span>
                        </button>
                    </div>
                </div>
                <?php endif;?>
            <?php elseif($_TODAY_HOURS_TEXT === '휴무일'): ?>
                <div class="bottom_btn">
                    <button type="button" class="btn btn-primary btn-block btn-lg" disabled>
                        휴무일
                    </button>
                </div>
            <?php else: ?>
                <div class="bottom_btn">
                    <button type="button" class="btn btn-primary btn-block btn-lg"
                            onclick="location.href='../auth/login.php'">
                        로그인
                    </button>
                </div>
            <?php endif;?>
        <?php endif; ?>

        <?php if ($isQr && empty($_SESSION['mng'])): ?>
            <div class="bottom_sheet" id="loginBottomSheet">
                <p class="text-right login_close"><a href="javascript:void(0)"><img class="flex-shrink-0" src="<?=DESIGN_HTTP?>/img/login_pop_x.png" style="width:3rem"></a></p>
                <p class="text-right mt-2"><a href="<?=AUTH_PAGE?>/login.php" class="login_bg"><img src="<?=DESIGN_HTTP?>/img/login_pop.png"></a></p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php include_once("../inc/modal.php");?>
<script>
    (function() {
        let ALLOW_ORDER = <?= json_encode((bool)($_ALLOW_ORDER ?? false)) ?>;
        let MENUS = <?= $menusJson ?>;
        let SHOP_GPS = { lat: <?= json_encode($shopLat) ?>, lng: <?= json_encode($shopLng) ?>, name: <?= json_encode($fullName) ?> };
        let SHOP_ADDR = <?= json_encode($shAddr ?: '') ?>;
        let KAKAO_JS_KEY = <?= json_encode(KAKAO_JAVASCRIPT_KEY) ?>;
        let DEFAULT_TAB = '<?= $mode === 'takeout' ? 'takeout' : 'reserve' ?>';
        let API_URL = '<?= SHOP_ACTIONS ?>/update.php';

        let takeoutMap = null;
        let takeoutMarker = null;

        // 메뉴 렌더링 함수
        function renderMenuList(list) {
            let $list = $('#menuList');
            $list.empty();
            console.log('list',list);
            if (!list || !list.length) {
                $list.append('<li><div class="item_box text-center py-5 tg_400">등록된 메뉴가 없습니다.</div></li>');
                return;
            }

            list.forEach(function(m) {
                let soldOut = m.sm_type === 'N';
                let img = m.sm_image && m.sm_image.trim() ? '/data/menu/'+m.sm_image : '';
                let price = Number(m.sm_price || 0).toLocaleString('ko-KR') + '원';

                let href = soldOut ? 'javascript:void(0)' : `<?=SHOP_PAGE?>/detail.php?id=${m.idx}`;

                let html = `
                <li>
                    <div class="item_box">
                        ${img ? `
                        <div class="item_img flex-shrink-0">
                            <div class="rect rounded">
                                <img src="${img}" alt="메뉴 이미지">
                            </div>
                        </div>
                        ` : '' }
                        <div class="w-100">
                            ${m.sm_main === 'Y' ? `<p class="pb-1"><span class="best-label">${m.sm_main === 'Y' ? '사장님 추천' : ''}<span></p>` : ''}
                            <p class="fw_500">${m.sm_title || ''}</p>
                            <p class="tg_400 mt-2 fs_15 line2_text">${m.sm_contents || ''} </p>
                            <p class="mt-3 fs_15 fw_700">${price}</p>
                            <p class="mt-3 fs_15 line2_text">${m.sm_age_show === 'Y' ? '19세 이상 판매 상품' : ''}</p>
                            ${soldOut ? '<p class="text-danger fw_700">품절</p>' : ''}
                        </div>
                        <a class="item_link" href="${href}" onclick="${soldOut ? 'return false;' : ''}"></a>
                    </div>
                </li>`;

                $list.append(html);
            });

            // 메뉴 클릭 → location.href로 이동
            $list.off('click', '.item_link').on('click', '.item_link', function(e) {
                // if (!ALLOW_ORDER) { e.preventDefault(); alert('현재 이 매장은 포장/예약 주문이 불가합니다.'); return; }
                if (!ALLOW_ORDER && !<?= json_encode($isQr) ?>) {
                    ModalUtil.alert({
                        title: '알림',
                        message: '현재 이 매장은 포장/예약 주문이 불가합니다.',
                        okText: '확인',
                        onOk: function () {
                        },
                    });
                    return;
                }

                if (this.href && this.href !== 'javascript:void(0)') {
                    e.preventDefault();
                    location.href = this.href;
                }
            });
        }

        // 카테고리 필터링 + 상단 타이틀 업데이트
        function filterByCategory(scIdx) {
            scIdx = parseInt(scIdx) || 0;

            let title = '전체 메뉴';
            if (scIdx > 0) {
                const cateLabel = $('#cateBtnGroup label[data-sc-idx="' + scIdx + '"]');
                title = cateLabel.text().trim() || '카테고리 메뉴';
            }

            // 상단 타이틀 업데이트
            $('#current_category_title').text(title);

            // 메뉴 필터링
            let filtered = scIdx > 0
                ? MENUS.filter(function(m) { return parseInt(m.sc_idx, 10) === scIdx; })
                : MENUS;

            renderMenuList(filtered);
        }

        // 카카오 맵 초기화 (기존 그대로)
        function initTakeoutMap() {
            if (!SHOP_GPS.lat || !SHOP_GPS.lng) return;

            function loadKakao(callback) {
                if (window.kakao && window.kakao.maps) {
                    callback();
                    return;
                }

                if (document.getElementById('kakaoMapSdk')) {
                    let interval = setInterval(function() {
                        if (window.kakao && window.kakao.maps) {
                            clearInterval(interval);
                            callback();
                        }
                    }, 100);
                    return;
                }

                let script = document.createElement('script');
                script.id = 'kakaoMapSdk';
                script.src = `//dapi.kakao.com/v2/maps/sdk.js?appkey=${encodeURIComponent(KAKAO_JS_KEY)}&autoload=false`;
                script.onload = function() { kakao.maps.load(callback); };
                document.head.appendChild(script);
            }

            loadKakao(function() {
                let container = document.getElementById('takeoutMap');
                if (!container) return;

                let center = new kakao.maps.LatLng(SHOP_GPS.lat, SHOP_GPS.lng);

                if (!takeoutMap) {
                    takeoutMap = new kakao.maps.Map(container, { center: center, level: 3 });
                    takeoutMarker = new kakao.maps.Marker({ position: center });
                    takeoutMarker.setMap(takeoutMap);
                } else {
                    takeoutMap.setCenter(center);
                    takeoutMarker.setPosition(center);
                }

                setTimeout(function() { takeoutMap.relayout(); }, 100);
            });
        }

        // 탭 전환 UI 업데이트
        function setTab(tab) {
            $('.tabs-reserve-takeout .tab-btn')
                .removeClass('btn-outline-primary')
                .filter(`[data-tab="${tab}"]`)
                .addClass('btn-outline-primary');

            $('#tab-reserve-info, #tab-reserve-bottom').toggleClass('d-none', tab !== 'reserve');
            $('#tab-takeout-info, #tab-takeout-bottom').toggleClass('d-none', tab !== 'takeout');

            if (tab === 'takeout') {
                initTakeoutMap();
            }
        }

        // 초기화 실행
        $(function() {
            // 카테고리 버튼 클릭 이벤트
            $('#cateBtnGroup').on('click', 'label', function() {
                let scIdx = parseInt($(this).data('sc-idx'), 10) || 0;
                $('#cateBtnGroup label').removeClass('active');
                $(this).addClass('active');
                filterByCategory(scIdx);
            });

            // 첫 번째 카테고리 자동 선택 & 렌더링
            const firstCate = $('#cateBtnGroup label').first();
            if (firstCate.length) {
                firstCate.addClass('active').find('input').prop('checked', true);
                const firstScIdx = parseInt(firstCate.data('sc-idx'), 10) || 0;
                filterByCategory(firstScIdx);
            } else {
                // 카테고리가 아예 없으면 전체 메뉴로 fallback
                $('#current_category_title').text('전체 메뉴');
                renderMenuList(MENUS);
            }

            // 탭 초기 설정
            setTab(DEFAULT_TAB);

            // 탭 클릭 → 서버에 모드 저장
            $('.tabs-reserve-takeout').on('click', '.tab-btn', function() {
                let tab = $(this).data('tab');
                let mode = tab === 'takeout' ? 'takeout' : 'reservation';

                $.post(API_URL, {
                    act: 'change_order_mode',
                    mode: mode
                }, function(res) {
                    if (res.success) {
                        setTab(tab);
                    } else {
                        ModalUtil.alert({
                            title: '알림',
                            message: '탭 변경에 실패했습니다: ' + (res.message || '다시 시도해주세요.'),
                            okText: '확인',
                            onOk: function () {
                            },
                        });
                    }
                }, 'json').fail(function() {
                    alert('서버 통신 오류가 발생했습니다.');
                });
            });

            // 주소 복사
            $('#btnCopyAddr').on('click', function() {
                if (!SHOP_ADDR) {
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
                    navigator.clipboard.writeText(SHOP_ADDR).then(function() {
                        ModalUtil.alert({
                            title: '알림',
                            message: '주소가 복사되었습니다.',
                            okText: '확인',
                            onOk: function () {
                            },
                        });
                    }).catch(function() {
                        ModalUtil.alert({
                            title: '알림',
                            message: '복사에 실패했습니다.',
                            okText: '확인',
                            onOk: function () {
                            },
                        });
                    });
                } else {
                    let ta = document.createElement('textarea');
                    ta.value = SHOP_ADDR;
                    document.body.appendChild(ta);
                    ta.select();
                    document.execCommand('copy');
                    document.body.removeChild(ta);
                    ModalUtil.alert({
                        title: '알림',
                        message: '주소가 복사되었습니다.',
                        okText: '확인',
                        onOk: function () {
                        },
                    });
                }
            });
            handleLoginSheet();
        });

        // 로그인 모달 쿠키 체크 & 닫기 시 24시간 차단
        function handleLoginSheet() {
            const sheetId = 'loginSheetHidden';
            const cookieName = sheetId + '_until';
            const hiddenUntil = localStorage.getItem(cookieName);

            if (hiddenUntil && new Date().getTime() < parseInt(hiddenUntil)) {
                $('#loginBottomSheet').hide();
                return;
            }

            $('#loginBottomSheet').show();

            $('.login_close a').on('click', function(e) {
                e.preventDefault();
                $('#loginBottomSheet').hide();

                // 24시간(86400000 ms) 동안 안 뜨게 저장
                const until = new Date().getTime() + 86400000;
                localStorage.setItem(cookieName, until);
            });
        }
    })();
</script>
