<?
$_SUB_HEAD_TITLE = "포장 접수";
$hd_pc = ''; //PC hd  로그인시공백 / 로그아웃시 logout
$hd_num = 'revenue'; //1차메뉴
$hd_left = 'pck_dtl'; //왼쪽메뉴 on 땜시 만듬
include_once("../inc/head.php");
include_once("../inc/header.php");
include_once("../inc/modal.php");
?>
    <!-- 왼쪽 메뉴-->
<?php include_once("../inc/left_menu.php"); ?>

    <style>.wrap{background-color:#FFF} .pck_card{width:100%} </style>

    <div class="sub_pg  bg-white">
        <div class="pck_list_wr">
            <div class="pck_list">
                <div class="pck_list_box">

                    <!--신규리스트  -->
                    <div id="pck_list_new" class="collapse_ex">
                        <button type="button" class="btn" data-toggle="collapse" data-target="#pck_list_new_dtl">
                            <p>신규 <span id="pck_cnt_new" class="text-primary m-2">0건</span></p>
                            <img src="<?=DESIGN_HTTP?>/market/img/selectarrow.svg">
                        </button>

                        <div id="pck_list_new_dtl" class="collapse show" data-parent="#pck_list_new">
                            <!-- JS로 렌더 -->
                        </div>
                    </div>

                    <!-- 진행중 리스트  -->
                    <div id="pck_list_ing" class="collapse_ex">
                        <button type="button" class="btn" data-toggle="collapse" data-target="#pck_list_ing_dtl">
                            <p>진행중 <span id="pck_cnt_ing" class="text-primary m-2">0건</span></p>
                            <img src="<?=DESIGN_HTTP?>/market/img/selectarrow.svg">
                        </button>

                        <div id="pck_list_ing_dtl" class="collapse show" data-parent="#pck_list_ing">
                            <!-- JS로 렌더 -->
                        </div>
                    </div>

                    <!-- 완료/취소 리스트  -->
                    <div id="pck_list_fi" class="collapse_ex">
                        <button type="button" class="btn" data-toggle="collapse" data-target="#pck_list_fi_dtl">
                            <p>완료/취소 <span id="pck_cnt_fi" class="text-primary m-2">0건</span></p>
                            <img src="<?=DESIGN_HTTP?>/market/img/selectarrow.svg">
                        </button>

                        <div id="pck_list_fi_dtl" class="collapse show" data-parent="#pck_list_fi">
                            <!-- JS로 렌더 -->
                        </div>
                    </div>

                </div>
            </div>

            <!-- 상세 -->
            <div class="pck_list_dtl" id="pck_detail_box">
                <div id="pck_detail_wrap">
                    <div class="d-flex align-items-center justify-content-between">
                        <span id="pck_detail_status_badge" class="status status_01">-</span>
                        <p class="d-flex align-items-center justify-content-center fs_16 tg_500">
                            <span class="mr-1"><img src="<?=DESIGN_HTTP?>/market/img/ico_time.svg" alt=" "></span>
                            <span id="pck_detail_elapsed">-</span>
                        </p>
                    </div>

                    <div class="detail_hd mt-4">
                        <div>
                            <h3 class="tit_st1">포장주문</h3>
                            <p id="pck_detail_headline" class="mt-2 fw_600">-</p>
                        </div>

                        <!-- 상태별 액션 버튼 영역 -->
                        <div id="pck_detail_actions" class="d-flex pck_cont"></div>
                    </div>

                    <section class="bill_wr" id="pck_detail_bill">
                        <!-- JS 렌더 -->
                    </section>

                    <!-- PREPARING에서만 환불 버튼/폼 노출 -->
                    <div id="pck_detail_refund_wrap"></div>

                    <div id="pck_detail_customer" class="mt-4 d-flex align-items-center justify-content-between bg-light p-5 rounded">
                        <p class="fw_600">고객정보</p>
                        <p>비회원</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- B-3 주문변경(모달) -->
    <div class="modal modal_rr fade" id="modal_tbl2" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog ">
            <div class="modal-content">
                <button type="button" class="close1" data-dismiss="modal" aria-label="Close"><img src="<?=DESIGN_HTTP?>/market/img/ic_close.png" alt="닫기"></button>
                <div class="modal-body">
                    <div class="detail_hd mt-4">
                        <h2 class="tit_st1 d-flex align-items-center">
                            <a href="#" data-toggle="modal" data-target="#modal_tbl1" data-dismiss="modal" class="mr-4 line_h0 ">
                                <img src="<?=DESIGN_HTTP?>/market/img/ico_back.svg" alt=" 뒤로가기">
                            </a>
                            <span>주문 변경</span>
                        </h2>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal_tbl1" data-dismiss="modal">변경 완료</button>
                    </div>

                    <section class="py-5 border-top border-dark">
                        <ul id="edit_order_list" class="bill_list wide_gap">
                            <!-- JS로 렌더(기존 너가 작업한 주문변경 로직 연결하면 됨) -->
                            <li class="py-5 text-center tg_500">주문 변경은 접수대기에서만 가능합니다.</li>
                        </ul>
                    </section>
                </div>
            </div>
        </div>
    </div>

    <!-- B-4 옵션변경(모달) -->
    <div class="modal modal_rr fade" id="modal_tbl3" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog ">
            <div class="modal-content">
                <button type="button" class="close1" data-dismiss="modal" aria-label="Close"><img src="<?=DESIGN_HTTP?>/market/img/ic_close.png" alt="닫기"></button>
                <div class="modal-body">
                    <div class="detail_hd mt-4">
                        <h2 class="tit_st1 d-flex align-items-center">
                            <a href="#" data-toggle="modal" data-target="#modal_tbl2" data-dismiss="modal" class="mr-4 line_h0 ">
                                <img src="<?=DESIGN_HTTP?>/market/img/ico_back.svg" alt=" 뒤로가기">
                            </a>
                            <span>옵션 변경</span>
                        </h2>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal_tbl2" data-dismiss="modal">변경 완료</button>
                    </div>

                    <section class="py-5 border-top border-dark">
                        <ul id="edit_option_list" class="bill_list wide_gap">
                            <!-- 기존 옵션 변경 로직을 여기에 연결 -->
                            <li class="py-5 text-center tg_500">옵션 변경 화면</li>
                        </ul>
                    </section>
                </div>
            </div>
        </div>
    </div>

<script>
    // =========================
    // 공통 설정
    // =========================
    const API_URL = './update.php';
    let PCK_SELECTED_OT_IDX = 0;

    function apiPost(data, onOk, onErr) {
        $.ajax({
            url: API_URL,
            method: 'POST',
            dataType: 'json',
            data: data || {},
            success: function (res) { onOk && onOk(res); },
            error: function (xhr) { onErr && onErr(xhr); },
        });
    }

    // =========================
    // 유틸
    // =========================
    function escHtml(s) {
        return String(s ?? '').replace(/[&<>"']/g, (m) => (
            {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]
        ));
    }
    function won(n) {
        const num = Number(n || 0);
        return num.toLocaleString() + '원';
    }

    function renderNoData() {
        return ''
            + '<div class="no_data  ">'
            + '  <img src="<?=DESIGN_HTTP?>/market/img/img_mark3.svg" style="width:5rem">'
            + '  <p class=" tg_500 line_h1_4 mt-3">주문이 없습니다</p>'
            + '</div>';
    }

    function renderStatusBadgeOnCard(st) {
        st = String(st || '').toUpperCase();
        if (st === 'PREPARING') return '<p class="pck_alim" style="background-color:#1362E6;">음식<br>준비중</p>';
        if (st === 'COMPLETED') return '<p class="pck_alim" style="background-color:#23B169;">전달<br>완료</p>';
        if (st === 'CANCELLED') return '<p class="pck_alim" style="background-color:#6C757D;">취소</p>';
        return '<p class="pck_alim">접수</p>'; // PENDING/CONFIRMED
    }

    function renderCard(item, isActive) {
        const idx = Number(item.idx || 0);
        const elapsed = escHtml(item.elapsed || '');
        const summary = escHtml(item.items_summary || '');
        const total = won(item.total_price || 0);
        const customer = escHtml(item.customer_name || '비회원');
        const phone = escHtml(item.phone || '');
        const customerText = phone ? (customer + ' ' + phone) : customer;

        return ''
            + '<a class="pck_card ' + (isActive ? 'active' : '') + '" href="javascript:void(0);" data-ot-idx="' + idx + '">'
            + '  <div class="cardtxt">'
            + '    <div class="flex-fill">'
            + '      <p class="d-flex align-items-center text-primary fw_500">'
            + '        <span class="mr-2"><img src="<?=DESIGN_HTTP?>/market/img/ico_time2.svg" alt=" "></span>'
            + '        <span>' + elapsed + '</span>'
            + '      </p>'
            + '      <p class="line1_text fw_500 mt-2">' + summary + '</p>'
            + '    </div>'
            + '    <div class="">' + renderStatusBadgeOnCard(item.status) + '</div>'
            + '  </div>'
            + '  <p class="line1_text fs_16 tg_500 mt-1 ">' + escHtml(total) + ' (' + customerText + ')</p>'
            + '</a>';
    }

    // =========================
    // 리스트 렌더
    // =========================
    function renderPckLists(data) {
        data = data || {};
        const newList = Array.isArray(data.new_list) ? data.new_list : [];
        const ingList = Array.isArray(data.ing_list) ? data.ing_list : [];
        const fiList  = Array.isArray(data.fi_list)  ? data.fi_list  : [];

        // ✅ 선택 유지
        const selectedFromApi = Number(data.selected_ot_idx || 0);
        if (selectedFromApi) PCK_SELECTED_OT_IDX = selectedFromApi;
        if (!PCK_SELECTED_OT_IDX) {
            PCK_SELECTED_OT_IDX = Number(newList[0]?.idx || ingList[0]?.idx || fiList[0]?.idx || 0);
        }

        // ✅ 건수
        $('#pck_cnt_new').text(newList.length + '건');
        $('#pck_cnt_ing').text(ingList.length + '건');
        $('#pck_cnt_fi').text(fiList.length + '건');

        // ✅ 신규
        if (newList.length) {
            let html = '';
            newList.forEach(it => html += renderCard(it, Number(it.idx) === Number(PCK_SELECTED_OT_IDX)));
            $('#pck_list_new_dtl').html(html);
        } else {
            $('#pck_list_new_dtl').html(renderNoData());
        }

        // ✅ 진행중
        if (ingList.length) {
            let html = '';
            ingList.forEach(it => html += renderCard(it, Number(it.idx) === Number(PCK_SELECTED_OT_IDX)));
            $('#pck_list_ing_dtl').html(html);
        } else {
            $('#pck_list_ing_dtl').html(renderNoData());
        }

        // ✅ 완료/취소
        if (fiList.length) {
            let html = '';
            fiList.forEach(it => html += renderCard(it, Number(it.idx) === Number(PCK_SELECTED_OT_IDX)));
            $('#pck_list_fi_dtl').html(html);
        } else {
            $('#pck_list_fi_dtl').html(renderNoData());
        }
    }

    // =========================
    // ✅ 접수 시간(분) 읽기/세팅 유틸
    // =========================
    function getPckMinuteValue() {
        const $inp = $('#pck_minute_input');
        if (!$inp.length) return 0;

        // "10분" / "20" 모두 대응
        let v = String($inp.val() || '').replace(/[^\d]/g, '');
        let n = parseInt(v, 10);
        if (isNaN(n)) n = 0;

        // 5분 단위 보정
        n = Math.round(n / 5) * 5;
        if (n < 0) n = 0;
        return n;
    }

    function setPckMinuteValue(n) {
        const $inp = $('#pck_minute_input');
        if (!$inp.length) return;
        n = Number(n || 0);
        if (n < 0) n = 0;
        n = Math.round(n / 5) * 5;
        $inp.val(n + '분');
    }

    function pckMinuteAdjust(delta) {
        const cur = getPckMinuteValue();
        const next = Math.max(0, cur + Number(delta || 0));
        setPckMinuteValue(next);
    }

    // =========================
    // ✅ 상세 상단 상태 뱃지(class) 적용
    // =========================
    function applyDetailStatusBadge(status) {
        status = String(status || '').toUpperCase();

        const $b = $('#pck_detail_status_badge');
        if (!$b.length) return;

        // 기존 status_01/02/03/04 제거
        $b.removeClass('status_01 status_02 status_03 status_04');

        // 네 디자인 기준
        // status_01: 접수대기
        // status_02: 음식준비중
        // status_03: 전달완료
        // status_04: 취소(없으면 status_03 쓰거나 CSS 추가)
        if (status === 'PENDING') $b.addClass('status_01');
        else if (status === 'PREPARING') $b.addClass('status_02');
        else if (status === 'COMPLETED') $b.addClass('status_03');
        else if (status === 'CANCELLED') $b.addClass('status_04');
        else $b.addClass('status_01');
    }

    // =========================
    // 상세 렌더
    // =========================
    // =========================
    // 상세 렌더
    // =========================
    function renderDetail(d) {
        d = d || {};
        const order = d.order || {};
        const items = Array.isArray(d.items) ? d.items : [];
        const customer = d.customer || null;

        const status = String(d.status || '').toUpperCase();

        // 상태 뱃지/시간
        $('#pck_detail_status_badge').text(d.status_label || '-');
        applyDetailStatusBadge(status);
        $('#pck_detail_elapsed').text(d.elapsed || '-');

        // -------------------------
        // ✅ 헤더 문구 (메뉴N개 ㆍ금액 ㆍ준비시간)
        // -------------------------
        const menuCnt = Number(d.menu_count_total || 0);
        const totalPrice = Number(d.total_price || 0);

        const prepMin = Number(d.prep_minutes || 0); // ✅ 변경된 key
        let headline = '메뉴' + menuCnt + '개 ㆍ' + totalPrice.toLocaleString() + '원';

        // PREPARING/COMPLETED이면 준비시간 노출
        if ((status === 'PREPARING' || status === 'COMPLETED') && prepMin > 0) {
            headline += 'ㆍ' + '<span class="text-primary">' + prepMin + '분</span>';
        }

        $('#pck_detail_headline').html(headline);

        // -------------------------
        // ✅ 액션 영역(우측 버튼/뱃지) 상태별 렌더
        // -------------------------
        const otIdx = Number(order.idx || d.ot_idx || 0);
        $('#pck_detail_actions').html(renderDetailActions(status, otIdx, d));

        // -------------------------
        // bill 렌더 (이하 기존 로직 그대로)
        // -------------------------
        let billHtml = '';
        billHtml += ''
            + '<div class="py-4 border-bottom-dot mb-4">'
            + '  <span class="mr-4">주문 번호 : ' + escHtml(order.ot_number || '-') + '</span>'
            + '  <span>주문일시 : ' + escHtml(d.order_datetime || '-') + '</span>'
            + '</div>'
            + '<ul class="bill_list">';

        if (status === 'CANCELLED') {
            const reason = escHtml(d.cancel_reason || '취소 처리되었습니다.');
            const cancelAt = escHtml(d.cancel_at || '');
            billHtml += ''
                + '<div class="cancle_alim">'
                + '  <p class="fw_600">' + reason + '</p>'
                + '  <p>' + cancelAt + '</p>'
                + '</div>';
        }

        // ✅ 접수대기(PENDING)일 때만 주문변경 버튼 생성
        const editBtnHtml = (status === 'PENDING')
            ? ' <button type="button" class="btn btn-md btn-outline-light bg-light rounded-pill px-4"'
            + ' onclick="openOrderEdit(' + otIdx + ')"'
            + ' data-toggle="modal" data-target="#modal_tbl2" data-dismiss="modal">'
            + '   <span class="mr-2"><img src="<?=DESIGN_HTTP?>/market/img/ico_edit.svg" alt=" "></span>주문 변경'
            + ' </button>'
            : '';

        billHtml += ''
            + '<li class="d-flex align-items-center justify-content-between">'
            + '  <p class="tit_st3">주문메뉴</p>'
            // + editBtnHtml
            + '</li>';

        // 메뉴 라인들
        items.forEach(function (it, i) {
            const isLast = (i === items.length - 1);
            const title = escHtml(it.menu_name || '');
            const qty = Number(it.quantity || 0);
            const lineTotal = Number(it.total_price || 0);

            billHtml += ''
                + '<li>'
                + '  <div class="bill_box">'
                + '    <div class="flex-fill">'
                + '      <div>'
                + '        <div class="d-flex justify-content-between">'
                + '          <p class="fw_600 fs_20">' + title + '</p>'
                + '          <p class="flex-shrink-0 ml-4">' + qty + '개</p>'
                + '        </div>';

            if (Array.isArray(it.options) && it.options.length) {
                billHtml += '<ul class="dot_list tg_500 mt-4">';
                it.options.forEach(function (op) {
                    const opName = escHtml(op.option_name || '');
                    const opPrice = Number(op.option_price || 0);
                    const opQty = Number(op.quantity || 1);
                    const priceTxt = (opPrice > 0) ? ' (+' + opPrice.toLocaleString() + ')' : '';
                    const qtyTxt = (opQty > 1) ? ' x' + opQty : '';
                    billHtml += '<li>' + opName + priceTxt + qtyTxt + '</li>';
                });
                billHtml += '</ul>';
            }

            billHtml += ''
                + '      </div>'
                + '    </div>'
                + '    <div class="bill_money">' + (lineTotal ? won(lineTotal) : '') + '</div>'
                + '  </div>'
                + '</li>';

            if (!isLast) billHtml += '<li class="border-bottom-dot"></li>';
        });

        // 쿠폰 할인 / 결제수단 / 총 주문 금액
        const discount = Number(d.discount_amount || 0);
        const discountTxt = discount > 0 ? ('-' + discount.toLocaleString() + '원') : '미사용';
        const payMethodTxt = '카드 결제';

        // ✅ 결제 완료 금액은 환불 반영된 paid_after_refund가 있으면 그걸 사용
        const paidAfterRefund = (d.paid_after_refund != null) ? Number(d.paid_after_refund) : Number(d.paid_price || 0);
        const refunded = Number(d.refunded_price || 0);

        billHtml += ''
            + '<li class="border-bottom"></li>'
            + '<li>'
            + '  <div class="d-flex align-items-center justify-content-between mb-2">'
            + '    <p>쿠폰 할인</p>'
            + '    <p class="fw_700 fs_20 ">' + escHtml(discountTxt) + '</p>'
            + '  </div>'
            + '  <div class="d-flex align-items-center justify-content-between mb-2">'
            + '    <p>결제 수단</p>'
            + '    <p class="fw_700 fs_20 ">' + escHtml(payMethodTxt) + '</p>'
            + '  </div>'
            + '  <div class="d-flex align-items-center justify-content-between mb-2">'
            + '    <p>총 주문 금액</p>'
            + '    <p class="fw_700 fs_20 ">' + won(totalPrice) + '</p>'
            + '  </div>';

        if (refunded > 0) {
            billHtml += ''
                + '  <div class="d-flex align-items-center justify-content-between mb-2">'
                + '    <p>결제취소/환불 금액</p>'
                + '    <p class="fw_700 fs_20 ">' + (refunded > 0 ? refunded.toLocaleString() + '원' : '0원') + '</p>'
                + '  </div>';
        }

        billHtml += ''
            + '</li>'
            + '<li class="border-bottom border-dark"></li>'
            + '<li>'
            + '  <div class="d-flex align-items-center justify-content-between mb-3">'
            + '    <p class="fw_600">결제 완료 금액</p>'
            + '    <p class="fw_700 fs_24 text-primary ">' + won(paidAfterRefund) + '</p>'
            + '  </div>'
            + '</li>'
            + '</ul>';

        $('#pck_detail_bill').html(billHtml);

        $('#pck_detail_refund_wrap').html(renderRefundBox(d));

        // 고객정보
        if (customer) {
            const nm = escHtml(customer.name || '회원');
            const hp = escHtml(customer.hp || '');
            const txt = hp ? (nm + ' (' + hp + ')') : nm;
            $('#pck_detail_customer').show().find('p').eq(1).html(txt);
        } else {
            $('#pck_detail_customer').show().find('p').eq(1).html('비회원');
        }
    }

    function renderDetailActions(status, otIdx, d) {
        status = String(status || '').toUpperCase();

        // ✅ 접수대기(PENDING)
        if (status === 'PENDING') {
            return ''
                + '<button type="button" class="btn btn-light mr-3" id="btn_refund_submit" data-ot-idx="' + otIdx + '">거절</button>'
                + '<div class="item_opt_counter mr-3">'
                + '  <button type="button" class="btn item_opt_counter_btn pl-1" onclick="pckMinuteAdjust(-5)"><img src="<?=DESIGN_HTTP?>/market/img/ico_decrease.svg" alt="감소"></button>'
                + '  <input id="pck_minute_input" type="text" class="quantity" value="10분">'
                + '  <button type="button" class="btn item_opt_counter_btn pr-1" onclick="pckMinuteAdjust(5)"><img src="<?=DESIGN_HTTP?>/market/img/ico_increase.svg" alt="증가"></button>'
                + '</div>'
                + '<button type="button" class="btn btn-primary" onclick="packAction(\'accept\','+otIdx+')">접수</button>';
        }

        // ✅ 음식준비중(PREPARING)
        if (status === 'PREPARING') {
            return ''
                + '<button type="button" class="btn btn-primary px_60" onclick="packAction(\'complete\','+otIdx+')">전달완료</button>';
        }

        // ✅ 전달완료(COMPLETED): 버튼 대신 상태 박스 + 시간/소요
        if (status === 'COMPLETED') {
            const hm = (d && d.completed_hm) ? String(d.completed_hm) : '';
            const cookMin = (d && d.cook_elapsed_minutes) ? Number(d.cook_elapsed_minutes) : 0;

            // "전달 완료 18:24"
            let html = '<div class="btn_green px_60">전달 완료' + (hm ? ' ' + escHtml(hm) : '') + '</div>';

            // "총 XX분 소요" 같이 노출(원하면 문구/스타일 바꿔도 됨)
            // if (cookMin > 0) {
            //     html += '<div class="ml-3 tg_500 fs_16">총 ' + cookMin + '분 소요</div>';
            // }
            return html;
        }

        // ✅ 취소(CANCELLED) 등
        if (status === 'CANCELLED') return '';
        return '<button type="button" class="btn btn-outline-secondary" disabled>처리 완료</button>';
    }

    // =========================
    // API 호출
    // =========================
    function loadPackList() {
        apiPost(
            { act: 'pack_list', selected_ot_idx: PCK_SELECTED_OT_IDX },
            function (res) {
                if (!res || !res.success) {
                    console.log(res);
                    alert(res?.message || '리스트 조회 실패');
                    return;
                }
                renderPckLists(res.data);

                // ✅ 리스트 로드 후 선택된 주문 detail 로딩
                if (PCK_SELECTED_OT_IDX) loadPackDetail(PCK_SELECTED_OT_IDX);
            },
            function (xhr) {
                console.log(xhr);
                alert('네트워크 오류');
            }
        );
    }

    function loadPackDetail(otIdx) {
        apiPost(
            { act: 'pack_detail', ot_idx: otIdx },
            function (res) {
                if (!res || !res.success) {
                    console.log(res);
                    $('#pck_detail_bill').html('<div class="py-5 text-center tg_500">상세 조회 실패</div>');
                    return;
                }
                renderDetail(res.data);
            },
            function (xhr) {
                console.log(xhr);
                $('#pck_detail_bill').html('<div class="py-5 text-center tg_500">네트워크 오류</div>');
            }
        );
    }

    // ✅ 여기 핵심: accept 시 prep_min 같이 전송
    function packAction(action, otIdx) {
        if (!otIdx) return;

        let msg = '';
        if (action === 'accept') msg = '해당 포장 주문을 접수하시겠습니까?';
        if (action === 'reject') msg = '해당 포장 주문을 거절/취소 처리하시겠습니까?';
        if (action === 'complete') msg = '전달완료 처리하시겠습니까?';
        if (msg && !confirm(msg)) return;

        const payload = { act: 'pack_action', ot_idx: otIdx, action: action };

        // ✅ accept일 때 prep_min 포함
        if (action === 'accept') {
            const curTxt = String($('#pck_minute_input').val() || '10분');
            let m = parseInt(curTxt, 10);
            if (isNaN(m)) m = 10;
            m = Math.round(m / 5) * 5;
            payload.prep_min = m;
        }

        apiPost(payload, function (res) {
            if (!res || !res.success) {
                alert(res?.message || '처리 실패');
                return;
            }
            loadPackList();
        }, function (xhr) {
            console.log(xhr);
            alert('네트워크 오류');
        });
    }

    // =========================
    // 이벤트: 카드 클릭
    // =========================
    $(document).on('click', '.pck_card', function () {
        const idx = Number($(this).data('ot-idx') || 0);
        if (!idx) return;

        PCK_SELECTED_OT_IDX = idx;
        $('.pck_card').removeClass('active');
        $(this).addClass('active');
        loadPackDetail(idx);
    });

    $(function () {
        loadPackList();
    });

    function renderRefundBox(d) {
        d = d || {};
        const status = String(d.status || '').toUpperCase();
        const payStatus = String(d.pay_status || '').toUpperCase();

        // ✅ 음식준비중 + 결제완료일 때만 노출 (원하면 PENDING도 허용 가능)
        if (!(status === 'PREPARING' && payStatus === 'PAID')) return '';

        const otIdx = Number(d.order?.idx || d.ot_idx || 0);
        const paid = Number(d.paid_price || 0);
        const refunded = Number(d.refunded_price || 0);
        const refundable = Math.max(0, paid - refunded);

        // 환불할 게 없으면 숨김
        if (refundable <= 0) return '';

        return ''
            + '<button type="button" class="btn btn-secondary btn-block mt-4" id="btn_refund_toggle">'
            + '  결제 취소'
            + '</button>'
            + '<div class="pay_cncl mt-3" id="refund_panel" style="display:none;">'
            + '  <div class="form_wr">'
            + '    <div class="ip_tit">'
            + '      <h5 class="text-white">결제취소/환불 금액(원)</h5>'
            + '    </div>'
            + '    <div class="form-row">'
            + '      <div class="col-6">'
            + '        <input type="text" class="form-control" id="refund_amount" placeholder="0" value="' + refundable.toLocaleString() + '">'
            + '        <small class="text-white d-block mt-2">환불 가능: ' + refundable.toLocaleString() + '원</small>'
            + '      </div>'
            + '      <div class="col-3">'
            + '        <button type="button" class="btn btn-primary btn-block px-1" id="btn_refund_submit" data-ot-idx="' + otIdx + '">확인</button>'
            + '      </div>'
            + '      <div class="col-3">'
            + '        <button type="button" class="btn btn-outline-light btn-block px-1" id="btn_refund_cancel">취소</button>'
            + '      </div>'
            + '    </div>'
            + '  </div>'
            + '</div>';
    }

    // 환불 패널 토글
    $(document).on('click', '#btn_refund_toggle', function () {
        $('#refund_panel').toggle();
    });

    // 환불 취소(패널 닫기)
    $(document).on('click', '#btn_refund_cancel', function () {
        $('#refund_panel').hide();
    });

    // 환불 확인
    $(document).on('click', '#btn_refund_submit', function () {
        const otIdx = Number($(this).data('ot-idx') || 0);
        if (!otIdx) return;

        // let amount = String($('#refund_amount').val() || '');
        // amount = parseInt(amount.replace(/[^0-9]/g, ''), 10);
        // if (!amount || amount <= 0) {
        //     alert('환불 금액을 입력해주세요.');
        //     return;
        // }

        // if (!confirm(amount.toLocaleString() + '원을 환불 처리하시겠습니까?')) return;

        apiPost(
            { act: 'pack_refund', ot_idx: otIdx },
            function (res) {
                if (!res || !res.success) {
                    alert(res?.message || '환불 처리 실패');
                    return;
                }
                ModalUtil.alert({
                    title: '포장',
                    message: res.message || '환불 처리되었습니다.',
                    okText: '확인',
                    onOk: function () {
                    },
                });
                // ✅ 상세 재조회 + 리스트 갱신
                loadPackList();
                loadPackDetail(otIdx);
            },
            function (xhr) {
                console.log(xhr);
                alert('네트워크 오류');
            }
        );
    });

    // =========================
    // (선택) 네 기존 함수 훅 - 아직 구현 안 되어있으면 에러 방지용 더미
    // =========================
    window.openOrderEdit = window.openOrderEdit || function(otIdx){
        // TODO: 주문 변경 모달 로직 연결
        console.log('openOrderEdit:', otIdx);
    };
</script>

<? include_once("./inc/tail.php"); ?>
