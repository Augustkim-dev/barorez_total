<?php
// /order/views/history.php
// 컨트롤에서 내려온 변수: $orders, $cancelOrders, $activeTab

$activeTab = ($activeTab ?? 'order') === 'cancel' ? 'cancel' : 'order';

function buildThumbSrc($order) {
    $sh_idx = (int)($order['sh_idx'] ?? 0);
    $thumb  = $order['thumb'] ?? '';
    $isFile = !empty($order['thumb_is_file']); // 필요 시 컨트롤에서 추가

    if ($isFile && $sh_idx > 0 && $thumb) {
        return '/data/shop/' . $sh_idx . '/' . $thumb;
    }

    if ($thumb) return $thumb;
    return DESIGN_HTTP . '/img/pr_sample01.jpg';
}

function buildStoreLink($order) {
    $url = $order['detail_url'] ?? '';
    return $url ?: '#';
}

function isEmptyList($arr){
    return !is_array($arr) || count($arr) === 0;
}
?>

<div class="wrap">
    <div class="sub_pg bg-light">

        <ul class="nav nav_tab_line">
            <li class="nav-item">
                <button class="nav-link <?= $activeTab === 'order' ? 'active' : '' ?>" data-tab="order" type="button">
                    주문 내역
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link <?= $activeTab === 'cancel' ? 'active' : '' ?>" data-tab="cancel" type="button">
                    취소 내역
                </button>
            </li>
        </ul>

        <div class="container">

            <!-- 공통 필터 영역 -->
            <div class="order_filter py-3">
                <div class="sch_ip align-items-center">
                    <input id="search-keyword" type="search" class="form-control fs_14 flex-fill border-0"
                           placeholder="검색어를 입력해주세요">
                    <button id="search-btn" class="btn btn-icon flex-shrink-0" type="button">
                        <img src="<?=DESIGN_HTTP?>/img/ic_sch_gray.png" style="width:2.0rem;">
                    </button>
                </div>

                <div class="scroll_mouse scroll_bar_none mt-3 order_filter_tg mx_n16 ">
                    <div class="d-flex px_16">
                        <button type="button" class="btn btn-outline-light btn-md rounded-pill reset_btn" id="filter-reset">
                            <img src="<?=DESIGN_HTTP?>/img/sch_re1.svg" alt="초기화">
                        </button>

                        <button type="button" class="btn btn-outline-light btn-md rounded-pill"
                                data-toggle="modal" data-target="#pop_filter1">
                            주문상태 <span class="line_arrow down ml-2"></span>
                        </button>

                        <button type="button" class="btn btn-outline-light btn-md rounded-pill"
                                data-toggle="modal" data-target="#pop_filter2">
                            조회 기간 <span class="line_arrow down ml-2"></span>
                        </button>
                    </div>
                </div>
            </div>

            <div id="tab-order" class="order_tab_panel <?= $activeTab === 'order' ? '' : 'd-none' ?>"></div>
            <div id="tab-cancel" class="order_tab_panel <?= $activeTab === 'cancel' ? '' : 'd-none' ?>"></div>

            <!-- 로딩바 -->
            <div id="loading-bar" class="text-center py-4 d-none">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-2">로딩 중...</p>
            </div>

        </div>
    </div>
</div>

<!-- 모달들 (기존 그대로) -->
<div class="modal modal_bottom fade" id="pop_filter1" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">주문상태</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <img src="<?=DESIGN_HTTP?>/img/ic_close.png">
                </button>
            </div>
            <div class="modal-body pt-2">
                <div class="btn-group btn-group-toggle" data-toggle="buttons" id="filter-kind-group">
                    <label class="btn btn-outline-light dark rounded-pill btn-md px-5 active">
                        <input type="radio" name="filter_kind" value="" id="all_kind" checked> 전체
                    </label>
                    <label class="btn btn-outline-light dark rounded-pill btn-md px-5">
                        <input type="radio" name="filter_kind" value="qr"> QR주문
                    </label>
                    <label class="btn btn-outline-light dark rounded-pill btn-md px-5">
                        <input type="radio" name="filter_kind" value="reservation"> 예약
                    </label>
                    <label class="btn btn-outline-light dark rounded-pill btn-md px-5">
                        <input type="radio" name="filter_kind" value="takeout"> 포장
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <div class="form-row">
                    <div class="col-3">
                        <button type="button" class="btn btn-outline-light btn-block" id="filter-kind-reset">초기화</button>
                    </div>
                    <div class="col-9">
                        <button type="button" class="btn btn-primary btn-block" id="filter-apply-kind" data-dismiss="modal">검색</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal modal_bottom fade" id="pop_filter2" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">조회 기간</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <img src="<?=DESIGN_HTTP?>/img/ic_close.png">
                </button>
            </div>

            <div class="modal-body pt-2">
                <div class="btn-group btn-group-toggle" data-toggle="buttons" id="filter-period-group">
                    <label class="btn btn-outline-light dark rounded-pill btn-md px-4 active">
                        <input type="radio" name="filter_period" value="1m" id="all_date" checked> 최근 1개월
                    </label>
                    <label class="btn btn-outline-light dark rounded-pill btn-md px-4">
                        <input type="radio" name="filter_period" value="3m"> 최근 3개월
                    </label>
                    <label class="btn btn-outline-light dark rounded-pill btn-md px-4">
                        <input type="radio" name="filter_period" value="6m"> 최근 6개월
                    </label>
                    <label class="btn btn-outline-light dark rounded-pill btn-md px-4">
                        <input type="radio" name="filter_period" value="custom"> 직접 선택
                    </label>
                </div>

                <div class="form-row mt-5">
                    <div class="form_wr col-6">
                        <div class="ip_tit"><h5>시작일</h5></div>
                        <input id="date-from" type="date" class="form-control fs_14">
                    </div>

                    <div class="form_wr col-6">
                        <div class="ip_tit"><h5>종료일</h5></div>
                        <input id="date-to" type="date" class="form-control fs_14">
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <div class="form-row">
                    <div class="col-3">
                        <button type="button" class="btn btn-outline-light btn-block" id="filter-date-reset">초기화</button>
                    </div>
                    <div class="col-9">
                        <button type="button" class="btn btn-primary btn-block" id="filter-apply-date" data-dismiss="modal">검색</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const DESIGN_HTTP  = "<?=DESIGN_HTTP?>";
    const ORDER_API_URL = "<?= ORDER_ACTIONS ?>/order.php";

    let currentTab  = "<?= $activeTab ?>"; // order | cancel
    let currentPage = 1;
    let isLoading   = false;
    let hasMoreData = true;


    function appendOrderCard(order) {
        let badgeHtml = `
            <span class="badg ${order.badge?.color_class || ''}">
                <span class="ic_img ${order.badge?.icon || ''} mr-2"></span>
                ${order.badge?.text || ''}
            </span>
        `;

        if (order.status_text) {
            badgeHtml += `<span class="${order.status_color_class || ''} ml-3">${order.status_text}</span>`;
        }

        console.log('order.thumb',order)
        const storeLink = order.detail_url || '#';
        let thumbSrc = order.thumb || (DESIGN_HTTP + '/img/pr_sample01.jpg');

        let rowsHtml = '';
        (order.rows || []).forEach(row => {
            if (!row.label) {
                rowsHtml += `<div>${row.value || ''}</div>`;
            } else {
                rowsHtml += `
                    <div class="d-flex align-items-center rsrv_list">
                        <dt class="tg_400">${row.label}</dt>
                        <dd class="${row.dd_class || ''}">${row.value || ''}</dd>
                    </div>
                `;
            }
        });

        const status = String(order.status || order.ot_status || '').toUpperCase();
        const hasReview = String(order.has_review || 'N').toUpperCase() === 'Y';
        const canWriteReview = typeof order.can_write_review === 'boolean'
            ? order.can_write_review
            : (status === 'COMPLETED' && !hasReview);

        const reviewHtml = canWriteReview ? `
            <div class="d-flex align-items-center rsrv_list border-top mt-4 pt-4">
                <dt class="tg_400">리뷰작성</dt>
                <dd class="ml-auto">
                    <a href="../review/write.php?ot_idx=${order.ot_idx || order.idx || 0}&sh_idx=${order.sh_idx || 0}"
                       class="btn btn-outline-primary btn-sm rounded-pill px-4">
                        리뷰작성
                    </a>
                </dd>
            </div>
        ` : '';

        const cardHtml = `
            <div class="card mb-3">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="mr-2">
                            <p class="d-flex align-items-center">${badgeHtml}</p>
                            <a href="${storeLink}" class="d-flex align-items-center mt-2">
                                <p class="fs_18 fw_700 line1_text">${order.store_name || '매장'}</p>
                                <img src="${DESIGN_HTTP}/img/ico_arrow1.png" class="ml-3 flex-shrink-0" style="width: 2rem;">
                            </a>
                        </div>
                        <div class="ml-auto">
                            <div class="item_img">
                                <a href="${storeLink}" class="d-block">
                                    <div class="rect rounded-pill">
                                        <img class="flex-shrink-0" src="${thumbSrc}" alt="매장 이미지">
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                    <p class="tg_400 fs_14">
                        ${order.code_label || '번호'} : ${order.code_text || ''} | ${order.code_date || ''}
                    </p>
                </div>
                <div class="card-body">
                    ${rowsHtml}
                    <div class="d-flex align-items-center rsrv_list border-top mt-4 pt-4">
                        <dt class="tg_400">결제금액</dt>
                        <dd class="fw_700 ml-auto">${order.payment || '0원'}</dd>
                    </div>
                    ${reviewHtml}
                </div>
            </div>
        `;

        $(`#tab-${currentTab}`).append(cardHtml);
    }

    // AJAX 로드 함수 (필터/검색 시 사용)
    function loadHistory(reload = false) {
        if (isLoading || (!hasMoreData && !reload)) return;

        if (reload) {
            currentPage = 1;
            hasMoreData = true;
            $(`#tab-${currentTab}`).empty();
        }

        isLoading = true;
        $('#loading-bar').removeClass('d-none');

        $.ajax({
            url: ORDER_API_URL,
            type: 'POST',
            dataType: 'json',
            data: {
                act: 'order_history_list',
                tab: currentTab,
                page: currentPage,
                kind: $('input[name="filter_kind"]:checked').val() || '',
                search: $('#search-keyword').val().trim(),
                date_from: $('#date-from').val() || '',
                date_to: $('#date-to').val() || ''
            },
            success: function(res) {
                if (res && res.success) {
                    (res.data || []).forEach(item => appendOrderCard(item));
                    hasMoreData = !!res.hasMore;

                    if (reload && (!res.data || res.data.length === 0)) {
                        $(`#tab-${currentTab}`).html('<div class="no_data text-center py-5"><p>내역이 없습니다.</p></div>');
                    }
                } else {
                    alert(res?.message || '데이터를 불러오지 못했습니다.');
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                alert('서버 연결에 실패했습니다.');
            },
            complete: function() {
                isLoading = false;
                $('#loading-bar').addClass('d-none');
                currentPage++;
            }
        });
    }

    // 탭 전환
    $('.nav_tab_line .nav-link').on('click', function() {
        currentTab = $(this).data('tab');
        $('.nav_tab_line .nav-link').removeClass('active');
        $(this).addClass('active');

        $('.order_tab_panel').addClass('d-none');
        $(`#tab-${currentTab}`).removeClass('d-none');

        loadHistory(true);
    });

    $(document).ready(function() {
        loadHistory(true);
    });
    // 무한 스크롤
    $(window).on('scroll', function() {
        if ($(window).scrollTop() + $(window).height() >= $(document).height() - 300) {
            loadHistory(false);
        }
    });

    // 검색, 필터 적용 시 AJAX 호출
    $('#search-btn').on('click', () => loadHistory(true));
    $('#search-keyword').on('keypress', e => { if (e.which === 13) loadHistory(true); });
    $('#filter-apply-kind, #filter-apply-date').on('click', () => loadHistory(true));

    // 필터 초기화
    $('#filter-reset, #filter-kind-reset, #filter-date-reset').on('click', function() {
        $('#search-keyword').val('');
        $('#date-from, #date-to').val('');
        $('#filter-kind-group label').removeClass('active');
        $('#filter-period-group label').removeClass('active');
        $('input[name="filter_kind"][value=""]').prop('checked', true).closest('label').addClass('active');
        $('input[name="filter_period"][value="1m"]').prop('checked', true).closest('label').addClass('active');;
        loadHistory(true);
    });

    // 기간 선택 시 날짜 자동 입력
    $('input[name="filter_period"]').on('change', function() {
        const v = $(this).val();
        if (v !== 'custom') {
            const now = new Date();
            const to = new Date(now.getFullYear(), now.getMonth(), now.getDate());
            const from = new Date(to);
            if (v === '1m') from.setMonth(from.getMonth() - 1);
            if (v === '3m') from.setMonth(from.getMonth() - 3);
            if (v === '6m') from.setMonth(from.getMonth() - 6);

            const pad = n => n.toString().padStart(2, '0');
            $('#date-from').val(`${from.getFullYear()}-${pad(from.getMonth()+1)}-${pad(from.getDate())}`);
            $('#date-to').val(`${to.getFullYear()}-${pad(to.getMonth()+1)}-${pad(to.getDate())}`);
        }
    });

</script>
