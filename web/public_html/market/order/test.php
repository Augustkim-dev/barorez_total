<?php
// table_test.php
session_start();

// ✅ 테스트용: 매장키 세션 없으면 임시로 넣어두기
if (!isset($_SESSION['current_sh_idx'])) {
    $_SESSION['current_sh_idx'] = 1; // TODO: 실제 매장키로 변경
}
?>
<!doctype html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <title>QR ORDER 테이블관리 테스트</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- ✅ 테스트 페이지에서만 사용 (실 서비스 퍼블리싱 영향 없음) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* ✅ 대략적인 화면 구성용 (테스트 페이지 전용) */
        body { background:#f4f6f8; }
        .topbar { height:56px; background:#1f1f1f; color:#fff; display:flex; align-items:center; padding:0 16px; }
        .sidebar { width:88px; background:#ff3b00; min-height:calc(100vh - 56px); color:#fff; }
        .layout { display:flex; }
        .content { flex:1; padding:18px; }
        .grid { display:grid; grid-template-columns:repeat(4, minmax(0, 1fr)); gap:16px; }
        .tcard { border-radius:14px; background:#fff; border:1px solid #e5e9ef; padding:14px; min-height:220px; position:relative; cursor:pointer; }
        .tcard.empty { background:#eef1f4; cursor:default; }
        .badge-pill { border-radius:999px; padding:6px 10px; font-size:12px; font-weight:700; display:inline-block; }
        .badge-received { background:#ff3b00; color:#fff; }
        .badge-preparing { background:#1f77ff; color:#fff; }
        .badge-served { background:#17b26a; color:#fff; }
        .badge-empty { background:#6b7280; color:#fff; }
        .tno { font-size:44px; font-weight:800; line-height:1; text-align:center; margin:18px 0 8px; }
        .sum { text-align:center; color:#111; font-weight:800; }
        .items { font-size:12px; color:#444; text-align:center; min-height:34px; }
        .btn-wide { width:100%; border-radius:10px; padding:10px 12px; font-weight:800; }
        #qr_toast {
            position:fixed; left:50%; bottom:22px; transform:translateX(-50%);
            background:#111; color:#fff; padding:10px 14px; border-radius:999px;
            font-size:14px; box-shadow:0 10px 22px rgba(0,0,0,.22);
            display:none; z-index:9999;
        }
        .new-dot {
            position:absolute; left:14px; bottom:64px;
            background:#111; color:#fff; border-radius:999px;
            padding:8px 12px; font-size:12px; font-weight:700;
            display:none;
        }
        .small-muted { color:#6b7280; font-size:12px; }
    </style>
</head>
<body>

<div class="topbar">
    <div class="me-3">☰</div>
    <div class="fw-bold">QR ORDER</div>
    <div class="ms-auto small-muted" id="now_time"></div>
</div>

<div class="layout">
    <div class="sidebar d-flex flex-column align-items-center py-3">
        <div class="mb-3 fw-bold">테이블</div>
        <div class="mb-3">포장</div>
        <div class="mb-3">예약</div>
        <div class="mt-auto small">v0.1</div>
    </div>

    <div class="content">
        <div class="d-flex align-items-center mb-3">
            <h3 class="m-0 fw-bold">테이블관리</h3>
            <div class="ms-3">
                <button type="button" class="btn btn-sm btn-outline-dark" id="btn_refresh">수동 새로고침</button>
            </div>
            <div class="ms-auto">
                <!-- ✅ 테스트용: 추가주문 상황 만들기 버튼(선택 테이블에 주문 1건 생성) -->
                <select id="mock_table_no" class="form-select form-select-sm d-inline-block" style="width:120px;">
                    <option value="1">1번</option><option value="2">2번</option><option value="3">3번</option><option value="4">4번</option>
                    <option value="5">5번</option><option value="6">6번</option><option value="7">7번</option><option value="8">8번</option>
                </select>
                <button type="button" class="btn btn-sm btn-dark" id="btn_mock_order">모의 추가주문</button>
            </div>
        </div>

        <div class="grid" id="table_grid">
            <!-- JS 렌더링 -->
        </div>
    </div>
</div>

<div id="qr_toast">추가 주문이 들어왔습니다</div>

<!-- ✅ 우측 상세 패널 (Bootstrap Offcanvas) -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="orderDetailCanvas" aria-labelledby="orderDetailCanvasLabel" style="width: 420px;">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title fw-bold" id="orderDetailCanvasLabel">주문 상세</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body">
        <!-- ✅ JS 렌더링 영역 -->
        <div id="order_detail_body">
            <div class="text-muted">테이블을 선택하면 주문 상세가 표시됩니다.</div>
        </div>

        <!-- ✅ 하단 액션 영역 -->
        <div class="mt-3 d-grid gap-2" id="order_detail_actions" style="display:none;">
            <button type="button" class="btn btn-danger" id="btn_detail_prepare">음식 준비하기</button>
            <button type="button" class="btn btn-outline-danger" id="btn_detail_serve">전달 완료</button>
            <button type="button" class="btn btn-secondary" id="btn_detail_clear">좌석 비우기</button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>

<script>
    /**
     * ✅ 테스트 페이지 로직 개요
     * - 3초마다 table_api.php act=list 호출
     * - 카드 클릭 시 act=detail(tv_idx) 호출하여 우측 offcanvas 표시
     * - 버튼: 음식 준비하기 / 전달 완료 / 좌석 비우기 -> act=action 호출
     */

    (function(){
        console.log('[table_test] init');

        var POLL_MS = 3000;

        // ✅ 상세 패널 인스턴스
        var detailCanvas = null;
        var currentDetail = { tv_idx: 0, table_no: 0, status: '' };

        function ensureCanvas(){
            var el = document.getElementById('orderDetailCanvas');
            if (!el) {
                console.log('[detail] canvas element not found');
                return null;
            }
            if (!detailCanvas) {
                detailCanvas = new bootstrap.Offcanvas(el);
                console.log('[detail] bootstrap offcanvas ready');
            }
            return detailCanvas;
        }

        // ✅ 로컬 캐시: tv_idx별 마지막 주문 idx 저장
        function getCache(){
            try { return JSON.parse(localStorage.getItem('qr_table_cache') || '{}'); }
            catch(e){ console.log('[cache] parse error', e); return {}; }
        }
        function setCache(cache){
            localStorage.setItem('qr_table_cache', JSON.stringify(cache));
        }

        // ✅ 현재시간 표시
        function renderClock(){
            var d = new Date();
            var mm = String(d.getMonth()+1).padStart(2,'0');
            var dd = String(d.getDate()).padStart(2,'0');
            var hh = String(d.getHours()).padStart(2,'0');
            var mi = String(d.getMinutes()).padStart(2,'0');
            $('#now_time').text(mm+'월 '+dd+'일 '+hh+':'+mi);
        }

        // ✅ 토스트
        function showToast(msg){
            console.log('[toast]', msg);
            var $t = $('#qr_toast');
            $t.text(msg).stop(true,true).fadeIn(120);
            clearTimeout(window.__qrToastTimer);
            window.__qrToastTimer = setTimeout(function(){
                $t.fadeOut(180);
            }, 1800);
        }

        // ✅ 상세 열기
        function openDetail(tvIdx, tableNo, status){
            console.log('[detail] openDetail', { tvIdx: tvIdx, tableNo: tableNo, status: status });

            currentDetail.tv_idx = tvIdx;
            currentDetail.table_no = tableNo;
            currentDetail.status = status || '';

            var oc = ensureCanvas();
            if (!oc) return;

            // 로딩 UI
            $('#orderDetailCanvasLabel').text('테이블번호 ' + tableNo);
            $('#order_detail_body').html('<div class="text-muted">불러오는 중...</div>');
            $('#order_detail_actions').hide();

            oc.show();

            // ✅ 상세 API 호출
            $.ajax({
                url: './table_api.php',
                method: 'POST',
                dataType: 'json',
                data: { act: 'detail', tv_idx: tvIdx },
                success: function(res){
                    console.log('[detail] api success', res);

                    if (!res || !res.success) {
                        $('#order_detail_body').html('<div class="text-danger">상세 정보를 불러오지 못했습니다.</div>');
                        return;
                    }

                    renderDetail(res.data);
                },
                error: function(xhr){
                    console.log('[detail] api error', xhr.status, xhr.responseText);
                    $('#order_detail_body').html('<div class="text-danger">서버 오류</div>');
                }
            });
        }

        // ✅ 상세 렌더
        function renderDetail(data){
            console.log('[detail] renderDetail', data);

            // data: { table_no, tv_idx, status, orders:[], summary:{} }
            var orders = data.orders || [];
            var summary = data.summary || {};

            var headerHtml = ''
                + '<div class="mb-2">'
                + '  <span class="badge bg-danger me-2">'+(summary.badge || '주문접수')+'</span>'
                + '  <span class="text-muted small">'+(summary.elapsed || '')+'</span>'
                + '</div>'
                + '<div class="fw-bold fs-5 mb-1">테이블번호 '+(data.table_no || '')+'</div>'
                + '<div class="text-muted small mb-2">주문건수: '+orders.length+'건</div>'
                + '<hr>';

            // 주문 목록
            var listHtml = '<div class="fw-bold mb-2">주문내역</div>';

            if (!orders.length) {
                listHtml += '<div class="text-muted">주문이 없습니다.</div>';
            } else {
                orders.forEach(function(o){
                    var itemsText = '';
                    try {
                        var snap = o.ct_snapshot ? JSON.parse(o.ct_snapshot) : null;
                        if (snap && Array.isArray(snap.items)) {
                            itemsText = snap.items.map(function(it){
                                return (it.title || it.name || '메뉴') + (it.qty ? ' x'+it.qty : '');
                            }).join('<br>');
                        }
                    } catch(e) {
                        console.log('[detail] snapshot parse error', e);
                    }
                    if (!itemsText) itemsText = '<span class="text-muted">메뉴 스냅샷 표시 형식 미정</span>';

                    listHtml += ''
                        + '<div class="border rounded p-2 mb-2">'
                        + '  <div class="d-flex justify-content-between">'
                        + '    <div class="fw-bold">주문번호: '+(o.ot_number || '-')+'</div>'
                        + '    <div class="text-muted small">'+(o.ot_wdate || '')+'</div>'
                        + '  </div>'
                        + '  <div class="small mt-2">'+itemsText+'</div>'
                        + '  <div class="d-flex justify-content-between mt-2">'
                        + '    <div class="text-muted small">결제: '+(o.ot_pay_status || '-')+'</div>'
                        + '    <div class="fw-bold">'+(o.ot_total_price ? Number(o.ot_total_price).toLocaleString()+'원' : '')+'</div>'
                        + '  </div>'
                        + '</div>';
                });
            }

            // 합계
            var total = summary.total_price || 0;
            var discount = summary.discount || 0;
            var pay = summary.pay_method || '-';

            var sumHtml = ''
                + '<hr>'
                + '<div class="d-flex justify-content-between mb-1"><div class="text-muted">쿠폰 할인</div><div class="text-muted">-'+Number(discount).toLocaleString()+'원</div></div>'
                + '<div class="d-flex justify-content-between mb-1"><div class="text-muted">결제 수단</div><div>'+pay+'</div></div>'
                + '<div class="d-flex justify-content-between mt-2"><div class="fw-bold">총 주문 금액</div><div class="fw-bold text-danger">'+Number(total).toLocaleString()+'원</div></div>';

            $('#order_detail_body').html(headerHtml + listHtml + sumHtml);

            // ✅ 상태별 액션 버튼 노출(대략)
            var st = (data.status || '').toUpperCase();
            $('#order_detail_actions').show();
            $('#btn_detail_prepare').toggle(st === 'RECEIVED');
            $('#btn_detail_serve').toggle(st === 'PREPARING');
            $('#btn_detail_clear').toggle(st === 'SERVED');
        }

        // ✅ 상태 변경 액션
        function doAction(action, tvIdx){
            console.log('[doAction] action=', action, 'tvIdx=', tvIdx);

            $.ajax({
                url: './table_api.php',
                method: 'POST',
                dataType: 'json',
                data: { act: 'action', action: action, tv_idx: tvIdx },
                success: function(res){
                    console.log('[doAction] success', res);
                    if (!res || !res.success) {
                        alert(res.message || '처리 실패');
                        return;
                    }
                    fetchList();
                },
                error: function(xhr){
                    console.log('[doAction] ajax error', xhr.status, xhr.responseText);
                    alert('서버 오류');
                }
            });
        }

        // ✅ 상세 액션 버튼들(리스트의 doAction 재사용)
        $(document).on('click', '#btn_detail_prepare', function(){
            console.log('[detail] prepare click', currentDetail);
            if (!currentDetail.tv_idx) return;
            doAction('prepare', currentDetail.tv_idx);
        });
        $(document).on('click', '#btn_detail_serve', function(){
            console.log('[detail] serve click', currentDetail);
            if (!currentDetail.tv_idx) return;
            doAction('serve', currentDetail.tv_idx);
        });
        $(document).on('click', '#btn_detail_clear', function(){
            console.log('[detail] clear click', currentDetail);
            if (!currentDetail.tv_idx) return;
            doAction('clear', currentDetail.tv_idx);
        });

        // ✅ 테이블 카드 렌더
        function renderTables(list){
            console.log('[renderTables] count=', list.length);

            var $grid = $('#table_grid');
            $grid.empty();

            list.forEach(function(t){
                var badgeClass = 'badge-empty';
                if (t.status === 'RECEIVED') badgeClass = 'badge-received';
                if (t.status === 'PREPARING') badgeClass = 'badge-preparing';
                if (t.status === 'SERVED') badgeClass = 'badge-served';

                var isEmpty = (t.status === 'EMPTY' || !t.tv_idx);

                // ✅ 버튼 구성 (상태별)
                var btnHtml = '';
                if (!isEmpty) {
                    if (t.status === 'RECEIVED') {
                        btnHtml = '<button type="button" class="btn btn-danger btn-wide" data-action="prepare">음식 준비하기</button>';
                    } else if (t.status === 'PREPARING') {
                        btnHtml = '<button type="button" class="btn btn-outline-danger btn-wide" data-action="serve">전달 완료</button>';
                    } else if (t.status === 'SERVED') {
                        btnHtml = '<button type="button" class="btn btn-secondary btn-wide" data-action="clear">좌석 비우기</button>';
                    }
                }

                var html = ''
                    + '<div class="tcard '+(isEmpty ? 'empty' : '')+'"'
                    + ' id="tcard_'+t.table_no+'"'
                    + ' data-table-no="'+t.table_no+'"'
                    + ' data-tv-idx="'+(t.tv_idx || 0)+'"'
                    + ' data-status="'+(t.status || '')+'"'
                    + ' data-latest="'+(t.latest_order_idx || 0)+'"'
                    + ' data-has-new="'+(t.has_new ? 1 : 0)+'">'
                    + '   <div class="d-flex justify-content-between align-items-center">'
                    + '     <span class="badge-pill '+badgeClass+'">'+t.status_label+'</span>'
                    + '     <span class="small-muted">'+(t.elapsed || '')+'</span>'
                    + '   </div>'
                    + '   <div class="tno">'+t.table_no+'</div>'
                    + '   <div class="items">'+(t.items_summary || '')+'</div>'
                    + '   <div class="sum">'+(t.total_price ? Number(t.total_price).toLocaleString()+'원' : '')+'</div>'
                    + '   <div class="new-dot" id="newdot_'+t.table_no+'">추가 주문이 들어왔습니다</div>'
                    + '   <div class="mt-3">'+btnHtml+'</div>'
                    + '</div>';

                $grid.append(html);

                // ✅ 카드 내부 버튼 클릭(상태 변경)
                $('#tcard_'+t.table_no+' button[data-action]').on('click', function(e){
                    e.stopPropagation(); // ✅ 카드 클릭(상세열기)로 전파 방지
                    var action = $(this).data('action');
                    var tvIdx = parseInt($('#tcard_'+t.table_no).attr('data-tv-idx') || '0', 10);
                    console.log('[btn click]', 'table=', t.table_no, 'action=', action, 'tvIdx=', tvIdx);
                    if (!tvIdx) return;
                    doAction(action, tvIdx);
                });

                // ✅ 카드 클릭(상세 열기)
                $('#tcard_'+t.table_no).on('click', function(){
                    console.log('[card click]', 'table=', t.table_no);
                    var tvIdx = parseInt($(this).attr('data-tv-idx') || '0', 10);
                    if (!tvIdx) return;
                    openDetail(tvIdx, t.table_no, t.status);
                });

                // ✅ 카드 내 “추가 주문” 뱃지
                if (t.has_new) {
                    $('#newdot_'+t.table_no).show();
                }
            });

            // ✅ “추가주문 토스트”는 렌더 후 캐시 비교로 1회만 띄움
            checkNewOrders();
        }

        // ✅ 서버 리스트 호출
        function fetchList(){
            console.log('[fetchList] request');

            $.ajax({
                url: './table_api.php',
                method: 'POST',
                dataType: 'json',
                data: { act: 'list' },
                success: function(res){
                    console.log('[fetchList] success', res);
                    if (!res || !res.success) {
                        console.log('[fetchList] failed', res);
                        return;
                    }
                    renderTables(res.data.tables || []);
                },
                error: function(xhr){
                    console.log('[fetchList] ajax error', xhr.status, xhr.responseText);
                }
            });
        }

        // ✅ 새 주문 감지(토스트)
        function checkNewOrders(){
            console.log('[checkNewOrders] run');

            var cache = getCache();
            var cards = document.querySelectorAll('[data-tv-idx]');

            cards.forEach(function(card){
                var tvIdx = parseInt(card.getAttribute('data-tv-idx') || '0', 10);
                if (!tvIdx) return;

                var latest = parseInt(card.getAttribute('data-latest') || '0', 10);
                var hasNew = card.getAttribute('data-has-new') === '1';
                var prev = parseInt(cache[tvIdx] || '0', 10);

                console.log('[checkNewOrders]', 'tvIdx=', tvIdx, 'prev=', prev, 'latest=', latest, 'hasNew=', hasNew);

                if (latest > prev && hasNew) {
                    showToast('추가 주문이 들어왔습니다');
                }

                cache[tvIdx] = latest;
            });

            setCache(cache);
        }

        // ✅ 모의 추가주문(테스트 편의용)
        function mockAddOrder(tableNo){
            console.log('[mockAddOrder] tableNo=', tableNo);

            $.ajax({
                url: './table_api.php',
                method: 'POST',
                dataType: 'json',
                data: { act: 'mock_order', table_no: tableNo },
                success: function(res){
                    console.log('[mockAddOrder] success', res);
                    if (!res || !res.success) {
                        alert(res.message || '모의 주문 실패');
                        return;
                    }
                    fetchList();
                },
                error: function(xhr){
                    console.log('[mockAddOrder] ajax error', xhr.status, xhr.responseText);
                }
            });
        }

        // ✅ 이벤트 바인딩
        $('#btn_refresh').on('click', function(){
            console.log('[btn_refresh] click');
            fetchList();
        });

        $('#btn_mock_order').on('click', function(){
            var v = $('#mock_table_no').val();
            console.log('[btn_mock_order] click', v);
            mockAddOrder(v);
        });

        // ✅ 시작
        renderClock();
        setInterval(renderClock, 1000 * 10);

        fetchList();
        setInterval(fetchList, POLL_MS);

    })();
</script>
</body>
</html>
