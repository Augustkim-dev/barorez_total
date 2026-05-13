<?
$_SUB_HEAD_TITLE = "메인화면";
$hd_pc = ''; //PC hd  로그인시공백 / 로그아웃시 logout
$hd_num = 'revenue'; //1차메뉴
$hd_left = 'reserve_hst'; //왼쪽메뉴 on 땜시 만듬
include_once("../inc/head.php");
include_once("../inc/header.php");
include_once("../inc/modal.php");
?>
    <!-- 왼쪽 메뉴-->
<? include_once("../inc/left_menu.php"); ?>

    <style>
        .wrap {
            background-color: #fff;
        }
    </style>


    <div class="sub_pg bg-white">
        <div class="rev_list_wr">
            <div class="rev_list">
                <div class="rev_list_box">
                    <h2 class="tit_st2 mt_50">예약관리</h2>
                    <div class="calendar_wp">
                        <div class="wp_l flex-fill">
                            <div class="calendar calendar_tutor">
                                <div class="calendar-header">
                                    <a href="#" class="arrow mr-3" id="prevMonth"><img src="<?=DESIGN_HTTP?>/market/img/pg_prev.svg"></a>
                                    <p id="calendarMonthYear">  </p>
                                    <a href="#" class="arrow  " id="nextMonth"><img src="<?=DESIGN_HTTP?>/market/img/pg_next.svg"></a>

                                </div>
                                <table>
                                    <thead>
                                    <tr>
                                        <th>일</th>
                                        <th>월</th>
                                        <th>화</th>
                                        <th>수</th>
                                        <th>목</th>
                                        <th>금</th>
                                        <th>토</th>
                                    </tr>
                                    </thead>
                                    <tbody id="calendarBody">

                                    <!-- 나머지 날짜도 동일하게 반복 -->
                                    </tbody>
                                </table>
                                <div class="point_ex">
                                    <p class=""><span class="point_ico mr-2"></span>예약날</p>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
            <div class="rev_list_dtl">
                <div class="rev_list_hd mt_30  ">
                    <div class="  ml-auto   pl-2">
                        <form class="sch_ip border align-items-center">
                            <input type="search" id="rvKeyword" class="form-control flex-fill border-0" placeholder="예약자명 검색">
                            <button class="btn btn-icon flex-shrink-0"><img src="<?=DESIGN_HTTP?>/market/img/ic_ip_sch.svg"></button>
                        </form>
                    </div>
                    <div class="d-flex align-items-end flex-fill   ">
                        <div class="  btn-group-toggle rev_btn_g " data-toggle="buttons">
                            <label class="btn btn-outline-secondary   active">
                                <input type="radio" name="options" id="option1" checked=""> 오늘 예약 <span class="ml-2" id="cnt_today"> </span>
                            </label>
                            <label class="btn btn-outline-secondary  ">
                                <input type="radio" name="options" id="option2"> 확정예약 <span class="ml-2" id="cnt_confirmed"> </span>
                            </label>
                            <label class="btn btn-outline-secondary  ">
                                <input type="radio" name="options" id="option3"> 대기중 <span class="ml-2" id="cnt_pending"> </span>
                            </label>
                        </div>
                    </div>

                </div>
                <section class="rev_card_list" id="revCardList">


                </section>


            </div>
        </div>

    </div>

    <!-- data-toggle="modal" data-target="#modal_rev1"D-2 예약 상세(예약접수)(모달)-->
<div class="modal modal_rr fade" id="modal_rev" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <button type="button" class="close1" data-dismiss="modal" aria-label="Close">
                <img src="<?=DESIGN_HTTP?>/market/img/ic_close.png" alt="닫기">
            </button>

            <div class="modal-body">
                <input type="hidden" id="rv_idx" value="0">

                <div class="d-flex align-items-center justify-content-between">
                    <span class="status" id="rvStatusBadge">예약대기</span>
                    <p class="d-flex align-items-center justify-content-center fs_16 tg_500">
                        <span class="mr-1"><img src="<?=DESIGN_HTTP?>/market/img/ico_time.svg" alt=" "></span>
                        <span id="rvElapsed">-</span>
                    </p>
                </div>

                <div class="detail_hd mt-4">
                    <div>
                        <h3 class="tit_st1">예약주문</h3>
                        <p class="mt-2 fw_600" id="rvTopSummary" style="display:none;"></p>
                    </div>

                    <div id="rvActionBtns">
                        <button type="button" class="btn btn-primary mr-3" id="btnRvAccept" style="display:none;">접수</button>
                        <button type="button" class="btn btn-light" id="btnRvReject" style="display:none;">거절</button>

                        <button type="button" class="btn btn-gray" id="btnRvCancel" style="display:none;">예약취소</button>
                        <button type="button" class="btn btn-primary" id="btnRvArrive" style="display:none;">도착 확인</button>
                    </div>
                </div>

                <section class="bill_wr">
                    <div class="py-4 border-bottom-dot mb-4">
                        <span class="mr-4">예약 번호 : <span id="rvNumber">-</span></span>
                        <span>예약일시 : <span id="rvDatetime">-</span></span>
                    </div>

                    <!-- ✅ 취소/거절 안내 (CANCELLED/REJECTED일 때만 보이기) -->
                    <div class="cancle_alim" id="rvCancelBox" style="display:none;">
                        <p class="fw_600" id="rvCancelReason">-</p>
                        <p id="rvCancelAt">-</p>
                    </div>

                    <!-- 예약정보 -->
                    <ul class="bill_list mb-5">
                        <li class="d-flex align-items-center justify-content-between ">
                            <p class="tit_st3">예약정보</p>
<!--                            <button type="button" class="btn btn-md btn-outline-light bg-light rounded-pill px-4 rev_date_btn"><span class="mr-2"><img src="--><?php //=DESIGN_HTTP?><!--/market/img/ico_edit.svg" alt=" "></span>예약일시 변경</button>-->
                        </li>

                        <li class=" ">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <p class=" ">예약일시</p>
                                <p class="fw_700 fs_20" id="rvInfoDate">-</p>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <p class=" ">예약자</p>
                                <p class="fw_700 fs_20" id="rvInfoNameHp">-</p>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <p class=" ">예약인원</p>
                                <p class="fw_700 fs_20" id="rvInfoPeople">-</p>
                            </div>
                        </li>
                    </ul>

                    <!-- 주문메뉴 (선결제일 때만 렌더) -->
                    <ul class="bill_list" id="rvMenuWrap" style="display:none;">
                        <li class="d-flex align-items-center justify-content-between ">
                            <p class="tit_st3">주문메뉴</p>
                        </li>
                        <li id="rvMenuList"></li>

                        <li class="border-bottom border-dark"></li>

                        <!-- ✅ 결제내역 -->
                        <li class=" " id="rvPayInfoBox" style="display:none;">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <p class=" ">쿠폰 할인</p>
                                <p class="fw_700 fs_20" id="rvDiscountAmount">0원</p>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <p class=" ">결제 수단</p>
                                <p class="fw_700 fs_20" id="rvPayMethod">-</p>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <p class=" ">총 주문 금액</p>
                                <p class="fw_700 fs_20" id="rvTotalPrice">0원</p>
                            </div>

                            <div class="d-flex align-items-center justify-content-between mb-3 mt-3">
                                <p class="fw_600">결제 완료 금액</p>
                                <p class="fw_700 fs_24 text-primary" id="rvPaidPrice">0원</p>
                            </div>

                            <!-- ✅ 환불이 있을 때만 노출 -->
                            <div class="d-flex align-items-center justify-content-between mb-2" id="rvRefundBox" style="display:none;">
                                <p class=" ">환불 금액</p>
                                <p class="fw_700 fs_20" id="rvRefundedPrice">0원</p>
                            </div>
                        </li>
                    </ul>
                    <button type="button" class="btn btn-secondary btn-block mt-4" id="btnPayCancel" style="display:none;">결제 취소</button>

                    <div class="mt-4 d-flex align-items-center justify-content-between bg-light p-5 rounded" id="rvCustomerBox">
                        <p class="fw_600">고객정보</p>
                        <p id="rvCustomerText">-</p>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>

<script>
    (function(){
        const API_URL = './update.php';

        let state = {
            ym: '',
            selectedDate: '',
            filter: 'TODAY',     // TODAY(=선택일 전체) | CONFIRMED | PENDING
            keyword: '',
            countsByDate: {}     // { 'YYYY-MM-DD': n }
        };

        function pad2(n){ return String(n).padStart(2,'0'); }
        function formatCount(n){ return (parseInt(n||0,10)) + '건'; }

        function getTodayDateStr(){
            const d = new Date();
            return d.getFullYear() + '-' + pad2(d.getMonth()+1) + '-' + pad2(d.getDate());
        }

        function postAPI(payload){
            return $.ajax({
                url: API_URL,
                method: 'POST',
                dataType: 'json',
                data: payload
            });
        }

        // ✅ counts_by_date: 어떤 형태로 오든 'YYYY-MM-DD' => count 로 정규화
        function normalizeCountsByDate(raw){
            const map = {};
            if(!raw) return map;

            if(Array.isArray(raw)){
                raw.forEach(r => {
                    const k = String(r.date || r.rv_date || r.d || '').slice(0,10);
                    const v = parseInt(r.cnt ?? r.count ?? r.n ?? 0, 10);
                    if(k) map[k] = v;
                });
                return map;
            }

            if(typeof raw === 'object'){
                Object.keys(raw).forEach(key => {
                    const k = String(key).slice(0,10);
                    map[k] = parseInt(raw[key] || 0, 10);
                });
                return map;
            }

            return map;
        }

        // ✅ 탭 카운트 갱신
        function updateTabCounts(counts){
            const c = counts || {};
            $('#cnt_today').text(formatCount(c.today));
            $('#cnt_confirmed').text(formatCount(c.confirmed));
            $('#cnt_pending').text(formatCount(c.pending));
        }

        // 상태 -> class
        function statusClass(st){
            st = String(st||'').toUpperCase();
            if(st==='PENDING') return 'text-primary';
            if(st==='CONFIRMED') return 'text-blue';
            if(st==='ARRIVED') return 'text-danger';
            return 'text-muted';
        }

        // 상태 -> 라벨
        function statusLabel(st){
            st = String(st||'').toUpperCase();
            if(st==='PENDING') return '예약대기';
            if(st==='CONFIRMED') return '예약확정';
            if(st==='ARRIVED') return '도착완료';
            return st;
        }

        const ITEM_STATUS_ORDER = { PENDING: 1, CONFIRMED: 2, ARRIVED: 3 };
        function sortItems(items){
            return (items||[]).slice().sort((a,b)=>{
                const as = String(a.rv_status||'').toUpperCase();
                const bs = String(b.rv_status||'').toUpperCase();
                const ai = ITEM_STATUS_ORDER[as] || 999;
                const bi = ITEM_STATUS_ORDER[bs] || 999;
                if(ai !== bi) return ai - bi;

                // 같은 상태면 등록순 or idx순
                return (parseInt(a.idx||0,10) - parseInt(b.idx||0,10));
            });
        }

        function escapeHtml(str){
            return String(str||'')
                .replace(/&/g,'&amp;').replace(/</g,'&lt;')
                .replace(/>/g,'&gt;').replace(/"/g,'&quot;')
                .replace(/'/g,'&#039;');
        }

        function renderRefundBox(d){
            console.log('??');
            d = d || {};
            const rvStatus = String(d.rv_status || '').toUpperCase();     // 예약상태
            const rvType   = String(d.rv_type || '').toUpperCase();       // PREPAID / POSTPAID
            const pay      = d.pay || {};
            const payStatus = String(pay.pay_status || '').toUpperCase(); // PAID/...

            // ✅ 예약확정 + 선결제 + 결제완료일 때만
            if (!(rvStatus === 'CONFIRMED' && rvType === 'PREPAID' && payStatus === 'PAID')) return '';

            const otIdx = Number(pay.ot_idx || pay.idx || d.ot_idx || 0);

            const total = Number(pay.total_price || 0);
            const discount = Number(pay.discount_amount || 0);
            const refunded = Number(pay.refunded_amount || 0);


            const paidBase = Math.max(0, total - discount);
            const refundable = Math.max(0, paidBase - refunded);

            if (otIdx <= 0) return '';
            if (refundable <= 0) return '';

            // ✅ 버튼은 기존 #btnPayCancel 사용, 여기서는 패널만 렌더
            return ''
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

        function formatMoney(n){
            const v = parseInt(n || 0, 10);
            return v.toLocaleString('ko-KR') + '원';
        }

        function computeElapsedLabel(d){
            if(d && d.elapsed_label) return String(d.elapsed_label);
            const m = parseInt((d && (d.elapsed_min || d.elapsed_minutes)) || 0, 10);
            if(m > 0) return `${m}분 전`;
            return '-';
        }

// ct_snapshot 파싱(이중 인코딩 대응)
        function parseSnapshot(raw){
            // ✅ raw가 이미 object/array로 오는 경우 대응
            if (raw && typeof raw === 'object') {
                // array면 items로 간주
                if (Array.isArray(raw)) return { items: raw, summary: {} };
                return {
                    items: Array.isArray(raw.items) ? raw.items : [],
                    summary: (raw.summary && typeof raw.summary === 'object') ? raw.summary : {}
                };
            }

            if(!raw) return { items: [], summary: {} };

            let obj = null;
            try { obj = JSON.parse(String(raw)); } catch(e){ obj = null; }

            // ✅ 이중 인코딩 문자열 대응
            if(typeof obj === 'string'){
                try { obj = JSON.parse(obj); } catch(e){ obj = null; }
            }

            if(!obj || typeof obj !== 'object') return { items: [], summary: {} };

            return {
                items: Array.isArray(obj.items) ? obj.items : [],
                summary: (obj.summary && typeof obj.summary === 'object') ? obj.summary : {}
            };
        }

// 옵션 동일성 시그니처(같은 메뉴+같은 옵션이면 합치기)
        function optionSignature(options){
            const arr = Array.isArray(options) ? options : [];
            const norm = arr.map(o => ({
                option_name: String(o.option_name || ''),
                option_price: parseInt(o.option_price || 0, 10),
                quantity: parseInt(o.quantity || 1, 10),
            })).sort((a,b)=>{
                const ak = `${a.option_name}:${a.option_price}:${a.quantity}`;
                const bk = `${b.option_name}:${b.option_price}:${b.quantity}`;
                return ak.localeCompare(bk);
            });
            return JSON.stringify(norm);
        }

// 동일 옵션 메뉴 합치기
        function mergeSnapshotItems(items){
            const map = new Map();

            (Array.isArray(items) ? items : []).forEach(it => {
                const name = String(it.menu_name || it.name || '').trim();
                if(!name) return;

                const unit = parseInt(it.unit_price || 0, 10);
                const qty  = parseInt(it.quantity || it.qty || 1, 10);
                const opts = Array.isArray(it.options) ? it.options : [];

                const key = `${name}||${unit}||${optionSignature(opts)}`;

                if(!map.has(key)){
                    map.set(key, { menu_name: name, unit_price: unit, quantity: qty, options: opts });
                }else{
                    const cur = map.get(key);
                    cur.quantity += qty;
                    map.set(key, cur);
                }
            });

            return Array.from(map.values());
        }

// 주문메뉴 HTML(퍼블 스타일 그대로: bill_box + dot_list + border-bottom-dot)
        function buildMenuLis(merged){
            if(!Array.isArray(merged) || merged.length === 0){
                return `<li class="js-dyn-menu"><div class="py-3 text-muted">주문 메뉴 정보가 없습니다.</div></li>`;
            }

            let html = '';
            merged.forEach((m, idx) => {
                const name = escapeHtml(m.menu_name);
                const qty  = parseInt(m.quantity || 1, 10);
                const unit = parseInt(m.unit_price || 0, 10);
                const total = unit * qty;

                const opts = Array.isArray(m.options) ? m.options : [];
                const optsHtml = opts.length ? `
      <ul class="dot_list tg_500 mt-4">
        ${opts.map(o=>{
                    const on = escapeHtml(o.option_name || '');
                    const op = parseInt(o.option_price || 0, 10);
                    const oq = parseInt(o.quantity || 1, 10);
                    const pTxt = op > 0 ? ` (+${op.toLocaleString('ko-KR')})` : '';
                    const qTxt = oq > 1 ? ` x${oq}` : '';
                    return `<li>${on}${pTxt}${qTxt}</li>`;
                }).join('')}
      </ul>
    ` : ``;

                html += `
<li class="js-dyn-menu">
  <div class="bill_box">
    <div class="flex-fill">
      <div>
        <div class="d-flex justify-content-between">
          <p class="fw_600 fs_20">${name}</p>
          <p class="flex-shrink-0 ml-4">${qty}개</p>
        </div>
        ${optsHtml}
      </div>
    </div>
    <div class="bill_money">${formatMoney(total)}</div>
  </div>
</li>
`;
                if(idx < merged.length-1){
                    html += `<li class="border-bottom-dot js-dyn-sep"></li>`;
                }
            });

            return html;
        }

        // ✅ 카드(날짜+시간 그룹) 1개 안에 row 여러개
        function renderDatetimeGroups(groups){
            const $wrap = $('#revCardList');
            groups = Array.isArray(groups) ? groups : [];

            if(groups.length === 0){
                $wrap.html(`<div class="py-5 text-center text-muted">선택한 날짜에 예약 내역이 없습니다.</div>`);
                return;
            }

            let html = '';

            groups.forEach(g => {
                const header = g.datetime_label || g.label || '';

                html += `
<div class="card mb-3" data-dt="${escapeHtml(g.datetime||'')}">
  <div class="card-header">
    <h4 class="tit_st4 d-flex align-items-center">
      <img src="<?=DESIGN_HTTP?>/market/img/ico_calender3.svg" class="mr-2">
      ${escapeHtml(header)}
    </h4>
  </div>
  <div class="card-body">
`;

                const items = sortItems((g.items || []).filter(it => String(it.rv_status||'').toUpperCase() !== 'ARRIVED'));
                items.forEach(item => {
                    const st = String(item.rv_status||'').toUpperCase();
                    const stCls = statusClass(st);
                    const stText = statusLabel(st);
                    const rvType = String(item.rv_type || '').toUpperCase();

                    const people = parseInt(item.rv_people || 1, 10);
                    const extra = (people > 1) ? ` 외 ${people-1}명` : '';
                    const who = `${item.rv_name || ''}${extra}(${item.rv_hp || ''})`;

                    let desc = String(item.items_summary || '').trim();
                    if (!desc) {
                        desc = rvType === 'PREPAID' ? '선결제 주문' : '후불결제입니다.';
                    }

                    let btnHtml = '';
                    if(st === 'PENDING'){
                        btnHtml = `<button type="button" class="btn btn-gray ml-auto rev_btn js-reject">예약 거절</button>`;
                    }else if(st === 'CONFIRMED'){
                        btnHtml = `
    <button type="button" class="btn btn-primary ml-auto rev_btn js-arrive">도착 확인</button>
    <button type="button" class="btn btn-outline-secondary ml-2 rev_btn js-cancel">예약 취소</button>
  `;
                    }

                    html += `
    <div class="card_wr d-flex align-items-center" data-rv-idx="${item.idx}">
      <div class="mr-2 flex-fill">
        <p class="tit_st4">
          <span class="${stCls} mr-2">${escapeHtml(stText)}</span>
          <span class="d-inline-block">${escapeHtml(who)}</span>
        </p>
        ${desc ? `<p class="fs_16 tg_500 mt-3">${escapeHtml(desc)}</p>` : ``}
        <a href="javascript:void(0)" class="item_link js-open-detail"></a>
      </div>
      <div class="d-flex align-items-center">
        ${btnHtml}
      </div>
    </div>
`;
                });

                html += `
  </div>
</div>`;
            });

            $wrap.html(html);
        }

        // ✅ 캘린더 렌더(+n)
        function renderCalendar(year, month){
            const first = new Date(year, month-1, 1);
            const last  = new Date(year, month, 0);
            const startWeekday = first.getDay();
            const daysInMonth  = last.getDate();

            const todayStr = getTodayDateStr();
            $('#calendarMonthYear').text(`${year}. ${pad2(month)}`);

            const currentYm = `${year}-${pad2(month)}`;
            if(state.selectedDate && state.selectedDate.slice(0,7) !== currentYm){
                state.selectedDate = `${year}-${pad2(month)}-01`;
            }
            if(!state.selectedDate){
                if(todayStr.slice(0,7) === currentYm) state.selectedDate = todayStr;
                else state.selectedDate = `${year}-${pad2(month)}-01`;
            }

            let html = '';
            let day = 1;

            for(let row=0; row<6; row++){
                html += '<tr>';
                for(let col=0; col<7; col++){
                    const cellIndex = row*7 + col;

                    if(cellIndex < startWeekday || day > daysInMonth){
                        html += '<td></td>';
                        continue;
                    }

                    const dateStr = `${year}-${pad2(month)}-${pad2(day)}`;
                    const id = `d_${year}${pad2(month)}${pad2(day)}`;

                    const cnt = parseInt(state.countsByDate[dateStr] || 0, 10);
                    const hasResv = cnt > 0;

                    const checkedAttr = (dateStr === state.selectedDate) ? 'checked' : '';
                    const sundayClass = (col===0) ? 'sunday' : '';
                    const revClass = hasResv ? 'rev_date' : '';

                    html += `
<td class="${sundayClass}">
  <input type="radio" name="date" id="${id}" data-date="${dateStr}" ${checkedAttr} class="${revClass}">
  <label for="${id}">${day}</label>
  ${hasResv ? `<div class="resv_point"><span class="point_num">+${cnt}</span></div>` : ``}
</td>`;
                    day++;
                }
                html += '</tr>';
                if(day > daysInMonth) break;
            }

            $('#calendarBody').html(html);

            $('#calendarBody input[type=radio][name=date]').off('change').on('change', function(){
                const d = $(this).data('date');
                if(!d) return;
                state.selectedDate = d;
                loadList();
            });

            $(`#calendarBody input[data-date="${state.selectedDate}"]`).prop('checked', true);
        }

        // ✅ 리스트 로드 (API는 날짜+시간 묶음으로 groups 내려주는 형태로 변경)
        function loadList(){
            const isTodayMode = (state.filter === 'TODAY');

            const payload = {
                act: 'rv_list',
                filter: state.filter,
                keyword: state.keyword
            };

            // ✅ TODAY만 선택 날짜로 조회
            if(isTodayMode){
                payload.date = state.selectedDate || getTodayDateStr();
            }else{
                payload.date = ''; // ✅ CONFIRMED/PENDING는 날짜 조건 제거
            }

            postAPI(payload)
                .done(function(res){
                    console.log('res',res)
                    if(!res || res.success !== true){
                        alert(res?.message || '조회 실패');
                        updateTabCounts({today:0, confirmed:0, pending:0});
                        renderDatetimeGroups([]);
                        return;
                    }
                    const data = res.data || {};
                    updateTabCounts(data.counts || {today:0, confirmed:0, pending:0});
                    renderDatetimeGroups(data.groups || []);
                })
                .fail(function(){ alert('서버 통신 오류'); });
        }

        // ✅ 캘린더 데이터 로드 + 렌더
        function loadCalendar(year, month){
            const ym = `${year}-${pad2(month)}`;
            state.ym = ym;

            postAPI({ act:'rv_calendar', ym: ym })
                .done(function(res){
                    if(!res || res.success !== true){
                        alert(res && res.message ? res.message : '캘린더 조회 실패');
                        state.countsByDate = {};
                        renderCalendar(year, month);
                        loadList();
                        return;
                    }

                    const data = res.data || {};
                    // ✅ PENDING+CONFIRMED만 카운트해서 내려온다고 가정(서버에서 처리)
                    state.countsByDate = normalizeCountsByDate(data.counts_by_date);

                    renderCalendar(year, month);
                    loadList();
                })
                .fail(function(){
                    alert('서버 통신 오류');
                });
        }

        // ✅ 모달 오픈
        function openRvModal(){
            $('#modal_rev').modal('show');
        }

        // ✅ 상세 모달 채우기 (PREPAID 메뉴/결제 부분 “중복 렌더 제거” + 방어 강화)
        function fillRvModal(d){
            d = d || {};
            $('#rv_idx').val(d.idx || 0);

            const st = String(d.rv_status || '').toUpperCase();
            const rvType = String(d.rv_type || '').toUpperCase();

            function statusBadgeClass(st){
                st = String(st||'').toUpperCase();
                if(st === 'PENDING')   return 'status status_01';
                if(st === 'CONFIRMED') return 'status status_02';
                if(st === 'ARRIVED')   return 'status status_03';
                if(st === 'CANCELLED') return 'status status_04';
                if(st === 'REJECTED')  return 'status status_04';
                return 'status';
            }

            $('#rvStatusBadge')
                .attr('class', statusBadgeClass(st))
                .text(statusLabel(st));

            $('#rvElapsed').text(d.elapsed_label || '-');

            if(String(d.top_summary || '').trim()){
                $('#rvTopSummary').text(d.top_summary).show();
            }else{
                $('#rvTopSummary').hide();
            }

            $('#rvNumber').text(d.rv_number || '-');
            $('#rvDatetime').text(d.datetime_label || '-');

            $('#rvInfoDate').text(d.datetime_label || '-');
            $('#rvInfoNameHp').text((d.rv_name || '-') + '(' + (d.rv_hp || '-') + ')');
            $('#rvInfoPeople').text((d.rv_people || 1) + '명');

            if (st === 'CANCELLED' || st === 'REJECTED') {
                $('#rvCancelReason').text(d.rv_cancel_reason || '-');
                $('#rvCancelAt').text(d.rv_cancel_at || '-');
                $('#rvCancelBox').show();
            } else {
                $('#rvCancelBox').hide();
            }

            $('.js-info-divider').remove();
            const $infoUl = $('.bill_list.mb-5');
            if($infoUl.length){
                $infoUl.append('<li class="border-bottom border-dark js-info-divider"></li>');
            }

            $('#btnRvAccept,#btnRvReject,#btnRvCancel,#btnRvArrive').hide();

            if(st === 'PENDING'){
                $('#btnRvAccept').show();
                $('#btnRvReject').show();
            }else if(st === 'CONFIRMED'){
                $('#btnRvCancel').show();
                $('#btnRvArrive').show();
            }

            $('#rvMenuWrap').hide();
            $('#rvMenuList').empty();
            $('#rvPayInfoBox').hide();
            $('#btnPayCancel').hide();
            $('#rvRefundBox').hide();
            $('.js-pay-divider').remove();
            $('#refund_panel').remove();

            $('#rvCustomerText').text((d.rv_name || '-') + ' (' + (d.rv_hp || '-') + ')');

            const menus = Array.isArray(d.menus) ? d.menus : [];
            if (menus.length) {
                let menuHtml = '';

                menus.forEach((m, idx) => {
                    const name = m.name || '';
                    const qty = parseInt(m.qty || 1, 10) || 1;
                    const priceLabel = m.price_label || '';

                    const opts = Array.isArray(m.options) ? m.options : [];
                    menuHtml += `
          <div class="bill_box">
            <div class="flex-fill">
              <div>
                <div class="d-flex justify-content-between">
                  <p class="fw_600 fs_20">${escapeHtml(name)}</p>
                  <p class="flex-shrink-0 ml-4">${qty}개</p>
                </div>

                ${opts.length ? `
                  <ul class="dot_list tg_500 mt-4">
                    ${opts.map(o => `<li>${escapeHtml(o)}</li>`).join('')}
                  </ul>
                ` : ``}
              </div>
            </div>
            <div class="bill_money">${escapeHtml(priceLabel)}</div>
          </div>
        `;

                    if(idx !== menus.length - 1){
                        menuHtml += `<div class="border-bottom-dot"></div>`;
                    }
                });

                $('#rvMenuList').html(menuHtml);
                $('#rvMenuWrap').show();
            }

            if(rvType === 'PREPAID'){
                const pay = d.pay || {};
                const discountLabel = pay.discount_label || '0원';
                const totalLabel = pay.total_price_label || '0원';
                const paidLabel = pay.paid_label || '0원';
                const payMethodLabel = '카드 결제';

                $('#rvDiscountAmount').text(discountLabel);
                $('#rvPayMethod').text(payMethodLabel);
                $('#rvTotalPrice').text(totalLabel);
                $('#rvPaidPrice').text(paidLabel);

                const refunded = parseInt(pay.refunded_amount || 0, 10) || 0;
                if(refunded > 0){
                    $('#rvRefundedPrice').text(pay.refunded_label || '0원');
                    $('#rvRefundBox').show();

                    const $totalRow = $('#rvTotalPrice').closest('.d-flex');
                    if($totalRow.length){
                        $('#rvRefundBox').insertBefore($totalRow);
                    }
                }else{
                    $('#rvRefundBox').hide();
                }

                const $paidRow = $('#rvPaidPrice').closest('.d-flex');
                if($paidRow.length){
                    $paidRow.before('<div class="border-bottom border-dark js-pay-divider my-3"></div>');
                }

                $('#rvPayInfoBox').show();

                const refundHtml = renderRefundBox(d);
                if (refundHtml) {
                    $('#rvCustomerBox').before(refundHtml);
                    $('#btnPayCancel').show();
                } else {
                    $('#btnPayCancel').hide();
                }
            }
        }

        // ✅ 상세 로드
        function loadDetail(rvIdx){
            postAPI({ act:'rv_detail', rv_idx: rvIdx })
                .done(function(res){
                    if(!res || res.success !== true){
                        alert(res && res.message ? res.message : '상세 조회 실패');
                        return;
                    }
                    const d = res.data || {};
                    fillRvModal(d);
                    openRvModal();
                })
                .fail(function(){
                    alert('서버 통신 오류(상세)');
                });
        }

        // ✅ 이벤트 바인딩
        function bindEvents(){
            const $tabWrap = $('.rev_btn_g');

            $tabWrap.find('#option1').off('change').on('change', function(){
                if(this.checked){ state.filter='TODAY'; loadList(); }
            });
            $tabWrap.find('#option2').off('change').on('change', function(){
                if(this.checked){ state.filter='CONFIRMED'; loadList(); }
            });
            $tabWrap.find('#option3').off('change').on('change', function(){
                if(this.checked){ state.filter='PENDING'; loadList(); }
            });

            // 검색
            $('#rvKeyword').off('keydown').on('keydown', function(e){
                if(e.key === 'Enter'){
                    e.preventDefault();
                    state.keyword = $(this).val().trim();
                    loadList();
                }
            });

            $('.sch_ip').off('submit').on('submit', function(e){
                e.preventDefault();
                state.keyword = $('#rvKeyword').val().trim();
                loadList();
            });

            $('.sch_ip button').off('click').on('click', function(e){
                e.preventDefault();
                state.keyword = $('#rvKeyword').val().trim();
                loadList();
            });

            // 이전/다음 달
            $('#prevMonth').off('click').on('click', function(e){
                e.preventDefault();
                const [y,m] = state.ym ? state.ym.split('-').map(Number) : [new Date().getFullYear(), new Date().getMonth()+1];
                const d = new Date(y, m-2, 1);
                state.selectedDate = '';
                loadCalendar(d.getFullYear(), d.getMonth()+1);
            });

            $('#nextMonth').off('click').on('click', function(e){
                e.preventDefault();
                const [y,m] = state.ym ? state.ym.split('-').map(Number) : [new Date().getFullYear(), new Date().getMonth()+1];
                const d = new Date(y, m, 1);
                state.selectedDate = '';
                loadCalendar(d.getFullYear(), d.getMonth()+1);
            });

            // ✅ 리스트: 상세 열기
            $('#revCardList').off('click', '.js-open-detail').on('click', '.js-open-detail', function(e){
                e.preventDefault();
                const rvIdx = $(this).closest('.card_wr').data('rv-idx');
                if(!rvIdx) return;
                loadDetail(rvIdx);
            });

            function refreshCalendarOnly(){
                if(!state.ym) return;
                const [y, m] = state.ym.split('-').map(Number);

                postAPI({ act:'rv_calendar', ym: state.ym })
                    .done(function(res){
                        if(!res || res.success !== true){
                            return;
                        }
                        const data = res.data || {};
                        state.countsByDate = normalizeCountsByDate(data.counts_by_date);

                        // ✅ 선택 날짜 유지하면서 같은 월 캘린더만 재렌더
                        renderCalendar(y, m);
                    });
            }

            function callRvAction(rvIdx, action){
                return $.ajax({
                    url: API_URL,               // 네가 쓰는 update.php
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        act: 'rv_action',
                        rv_idx: rvIdx,
                        action: action,           // 'ARRIVE' | 'REJECT' | 'CANCEL'
                    }
                });
            }

            // 거절
            $('#revCardList')
                .off('click', '.js-reject')
                .on('click', '.js-reject', function(e){
                    e.preventDefault();
                    const rvIdx = $(this).closest('[data-rv-idx]').data('rv-idx');
                    if(!rvIdx) return;

                    callRvAction(rvIdx, 'REJECT')
                        .done(function(res){
                            if(!res || res.success !== true){
                                alert(res && res.message ? res.message : '예약 거절 실패');
                                return;
                            }
                            // ✅ 성공 후: 캘린더/리스트 갱신
                            refreshCalendarOnly();  // ✅ 캘린더 카운트 갱신
                            loadList();
                        })
                        .fail(function(){
                            alert('서버 통신 오류');
                        });
                });

            // 도착 확인
            $('#revCardList')
                .off('click', '.js-arrive')
                .on('click', '.js-arrive', function(e){
                    e.preventDefault();
                    const rvIdx = $(this).closest('[data-rv-idx]').data('rv-idx');
                    if(!rvIdx) return;

                    callRvAction(rvIdx, 'ARRIVE')
                        .done(function(res){
                            if(!res || res.success !== true){
                                alert(res && res.message ? res.message : '도착 확인 실패');
                                return;
                            }
                            loadList();
                        })
                        .fail(function(){
                            alert('서버 통신 오류');
                        });
                });

            // 예약 취소
            $('#revCardList')
                .off('click', '.js-cancel')
                .on('click', '.js-cancel', function(e){
                    e.preventDefault();
                    const rvIdx = $(this).closest('[data-rv-idx]').data('rv-idx');
                    if(!rvIdx) return;

                    callRvAction(rvIdx, 'CANCEL')
                        .done(function(res){
                            if(!res || res.success !== true){
                                alert(res && res.message ? res.message : '예약 취소 실패');
                                return;
                            }
                            loadList();
                        })
                        .fail(function(){
                            alert('서버 통신 오류');
                        });
                });

            // ✅ 모달 버튼들도 rv_action으로 통일
            $('#btnRvAccept').off('click').on('click', function(){
                const rvIdx = parseInt($('#rv_idx').val()||'0',10);
                if(!rvIdx) return;

                callRvAction(rvIdx, 'ACCEPT')
                    .done(function(res){
                        if(!res || res.success !== true){
                            alert(res?.message || '접수 실패');
                            return;
                        }
                        $('#modal_rev').modal('hide');
                        refreshCalendarOnly();
                        loadList();
                    })
                    .fail(()=>alert('서버 통신 오류'));
            });

            $('#btnRvArrive').off('click').on('click', function(){
                const rvIdx = parseInt($('#rv_idx').val()||'0',10);
                if(!rvIdx) return;

                callRvAction(rvIdx, 'ARRIVE')
                    .done(function(res){
                        if(!res || res.success !== true){
                            alert(res?.message || '도착 확인 실패');
                            return;
                        }
                        $('#modal_rev').modal('hide');
                        refreshCalendarOnly();
                        loadList();
                    })
                    .fail(()=>alert('서버 통신 오류'));
            });

            $('#btnRvCancel').off('click').on('click', function(){
                const rvIdx = parseInt($('#rv_idx').val()||'0',10);
                if(!rvIdx) return;

                callRvAction(rvIdx, 'CANCEL')
                    .done(function(res){
                        if(!res || res.success !== true){
                            alert(res?.message || '예약 취소 실패');
                            return;
                        }
                        $('#modal_rev').modal('hide');
                        refreshCalendarOnly();
                        loadList();
                    })
                    .fail(()=>alert('서버 통신 오류'));
            });

            $('#btnRvReject').off('click').on('click', function(){
                const rvIdx = parseInt($('#rv_idx').val()||'0',10);
                if(!rvIdx) return;

                callRvAction(rvIdx, 'REJECT')
                    .done(function(res){
                        if(!res || res.success !== true){
                            alert(res?.message || '예약 거절 실패');
                            return;
                        }
                        $('#modal_rev').modal('hide');
                        refreshCalendarOnly();
                        loadList();
                    })
                    .fail(()=>alert('서버 통신 오류'));
            });

            // ✅ 결제취소 버튼(기존 #btnPayCancel) 클릭 시 패널 토글
            $(document).off('click', '#btnPayCancel').on('click', '#btnPayCancel', function(e){
                e.preventDefault();
                if (!$('#refund_panel').length) return; // 패널이 없으면 무시
                $('#refund_panel').toggle();
            });

            // 결제취소 패널 취소
            $(document).off('click', '#btn_refund_cancel').on('click', '#btn_refund_cancel', function(e){
                e.preventDefault();
                $('#refund_panel').hide();
            });

            // 결제취소 확인
            $(document).off('click', '#btn_refund_submit').on('click', '#btn_refund_submit', function(e){
                e.preventDefault();

                const otIdx = parseInt($(this).data('ot-idx') || '0', 10);
                if(!otIdx){ alert('주문키가 없습니다.'); return; }

                const amt = parseInt(String($('#refund_amount').val()||'0').replace(/[^\d]/g,''),10) || 0;
                if(amt <= 0){ alert('환불 금액을 입력해주세요.'); return; }

                postAPI({ act:'pay_refund', ot_idx: otIdx, amount: amt })
                    .done(function(res){
                        if(!res || res.success !== true){
                            alert(res?.message || '환불 실패');
                            return;
                        }

                        const d = res.data || {};
                        ModalUtil.alert({
                            title: '포장',
                            message: res.message || '환불 처리되었습니다.',
                            okText: '확인',
                            onOk: function () {
                            },
                        });

                        // ✅ 즉시 화면 갱신 (환불금액/결제완료금액 차감)
                        // - 환불 금액 row 노출 + 값 세팅
                        if(d.refunded_total != null){
                            $('#rvRefundedPrice').text(Number(d.refunded_total).toLocaleString('ko-KR') + '원');
                            $('#rvRefundBox').show();
                        }

                        // - 결제 완료 금액을 "남은 결제금액"으로 보여주고 싶다면 여기서 교체
                        if(d.paid_remaining != null){
                            $('#rvPaidPrice').text(Number(d.paid_remaining).toLocaleString('ko-KR') + '원');
                        }

                        // ✅ 전액 환불이면: 예약이 취소되므로 모달 닫고 리스트/캘린더 갱신 권장
                        if(String(d.refund_type||'') === 'FULL'){
                            $('#modal_rev').modal('hide');
                            loadList(); // 네 함수 그대로
                            return;
                        }

                        // 부분환불이면: 패널 닫고 남은 환불가능금액 표시 업데이트(원하면)
                        $('#refund_panel').hide();

                        // 남은 환불 가능 금액 = paid_remaining (동일)
                        const remain = Math.max(0, Number(d.paid_remaining || 0));
                        $('#refund_amount').val(remain.toLocaleString('ko-KR'));

                    })
                    .fail(function(){
                        alert('서버 통신 오류');
                    });
            });

            $('#modal_rev').off('hidden.bs.modal').on('hidden.bs.modal', function(){
                $('#btnPayCancel').hide();
                $('#refund_panel').remove();
            });
        }

        // init
        $(function(){
            updateTabCounts({today:0, confirmed:0, pending:0});

            const now = new Date();
            state.selectedDate = getTodayDateStr();

            bindEvents();
            loadCalendar(now.getFullYear(), now.getMonth()+1);
        });

    })();
</script>

<? include_once("./inc/tail.php"); ?>
