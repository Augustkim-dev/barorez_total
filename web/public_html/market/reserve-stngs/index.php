<?
$_SUB_HEAD_TITLE = "예약설정";
$_GET['hd_pc'] = ' ';
$hd_num = 'store';
$hd_left = ' ';
include_once("../inc/head.php");
include_once("../inc/header.php");
include_once("../inc/modal.php");
?>
<? include_once("../inc/left_menu.php"); ?>

    <div class="sub_pg ">
        <div class="sub_wr">
            <div class="hd_tit ">
                <div class="d-flex  ">
                    <h2 class="tit_st1 d-flex align-items-center mr-5">
                        <a href="#" onclick="history.back(); return false;" class="mr-4 line_h0 ">
                            <img src="<?=DESIGN_HTTP?>/market/img/ico_back.svg" alt=" 뒤로가기">
                        </a>
                        <span>예약설정</span>
                    </h2>
                </div>
            </div>

            <form id="reserveSettingForm" name="reserveSettingForm">
                <input type="hidden" id="rs_slot_unit_min" name="rs_slot_unit_min" value="30">
                <input type="hidden" id="slots_json" name="slots_json" value="[]">

                <section class="card mt-4 rounded-lg ">
                    <div class="card-body">
                        <div class="form_wr">
                            <div class="ip_tit"><h5>예약 안내글</h5></div>
                            <textarea class="form-control" id="rs_notice" name="rs_notice" placeholder="예약취소에 대한 수수료 및 안내사항이 있으실 여기 적어주세요" rows="5"></textarea>
                            <p class="text-right mt-2 tg_500 fs_14" id="rs_notice_counter">(0/300)</p>
                        </div>
                    </div>
                </section>

                <section class="card mt-4 rounded-lg ">
                    <div class="card-body">
                        <h3 class="tit_st2">예약 시간 설정</h3>
                        <p class="tg_500 fs_16">예약 가능한 시간대와 테이블 개수를 설정하세요</p>

                        <div class="mt-5 border-top pt-5" id="timeSetWrap">
                            <div class="d-flex justify-content-between align-items-center mb-3 ">
                                <h3 class="tit_st3">예약 가능 시간대</h3>
                                <button type="button" class="btn btn-secondary btn-md" id="btnAddTimeSlot">시간대 추가</button>
                            </div>

                            <!-- ✅ time_set 템플릿(1개만 존재) -->
                            <div class="form-row rounded p-4 align-items-end time_set" id="timeSetTemplate">
                                <div class="col-2">
                                    <div class="custom-switch mb-4">
                                        <input type="checkbox" class="custom-control-input" id="search_switch" checked>
                                        <label class="custom-control-label" for="search_switch"></label>
                                    </div>
                                </div>

                                <div class="col-3">
                                    <div class="form_wr">
                                        <div class="ip_tit"><h5>시간</h5></div>
                                        <div class="custom-sel slotDaySelect">
                                            <button type="button" class="select-trigger">평일</button>
                                            <ul class="select-options">
                                                <li data-value="1">평일</li>
                                                <li data-value="2">토요일</li>
                                                <li data-value="4">일요일</li>
                                            </ul>
                                            <input type="hidden" name="slot_day_value" value="1">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-4">
                                    <div class="form_wr">
                                        <div class="ip_tit"><h5>&nbsp;</h5></div>
                                        <div class="time_form">
                                            <div class="input_txt">
                                                <span>시</span>
                                                <input type="number" class="form-control" placeholder="00" value="07" name="slot_hour">
                                            </div>:
                                            <div class="input_txt">
                                                <span>분</span>
                                                <input type="number" class="form-control" placeholder="00" value="00" name="slot_minute">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-2">
                                    <div class="form_wr">
                                        <div class="ip_tit"><h5>예약건수</h5></div>
                                        <input type="number" class="form-control" placeholder="1" value="1" name="slot_max_count">
                                    </div>
                                </div>

                                <div class="col-1 text-center">
                                    <a href="" class="mb-3 btnDeleteSlot">
                                        <img src="<?=DESIGN_HTTP?>/market/img/ico_delete.svg" alt=" 삭제" style="width:5rem">
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 border-top pt-5">
                            <h3 class="tit_st3">예약 제한 설정</h3>

                            <div class="border p-5 d-flex align-items-center justify-content-between rounded mt-3">
                                <div>
                                    <h3 class="tit_st4">당일 예약 허용</h3>
                                    <p class="tg_500 mt-2 fs_16">예약 당일에도 예약을 받을 수 있습니다.</p>
                                </div>
                                <div>
                                    <div class="custom-control custom-switch switch-outside">
                                        <span class="switch-state"></span>
                                        <input type="checkbox" class="custom-control-input" id="rs_allow_same_day" name="rs_allow_same_day" checked>
                                        <label class="custom-control-label" for="rs_allow_same_day"></label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row mt-5">
                                <div class="col-4">
                                    <div class="form_wr">
                                        <div class="ip_tit"><h5>최대 예약 가능 일수(일)</h5></div>
                                        <input type="text" class="form-control" id="rs_max_reserve_days" name="rs_max_reserve_days" value="0">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form_wr">
                                        <div class="ip_tit"><h5>최소 예약 인원(명)</h5></div>
                                        <input type="text" class="form-control" id="rs_min_person" name="rs_min_person" value="1">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form_wr">
                                        <div class="ip_tit"><h5>최대 예약 인원(명)</h5></div>
                                        <input type="text" class="form-control" id="rs_max_person" name="rs_max_person" value="1">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </section>

                <section class="card mt-4 rounded-lg ">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between ">
                            <div>
                                <h3 class="tit_st2">당일취소 및 미방문 위약금 설정</h3>
                                <p class="tg_500 fs_16">예약 후 미방문(노쇼) 시 위약금을 설정할 수 있습니다. 선결제 주문만 위약금결제가 가능합니다.</p>
                            </div>
                            <div>
                                <div class="custom-control custom-switch switch-outside">
                                    <span class="switch-state"></span>
                                    <input type="checkbox" class="custom-control-input" id="rp_use" name="rp_use" checked>
                                    <label class="custom-control-label" for="rp_use"></label>
                                </div>
                            </div>
                        </div>

                        <div class="row mt_50">
                            <div class="col-6">
                                <div class="form_wr">
                                    <div class="ip_tit"><h5>위약금 금액/비율</h5></div>
                                    <div class="d-flex">
                                        <div class="custom-sel mr-2" id="rpTypeSelect">
                                            <button type="button" class="select-trigger">고정금액</button>
                                            <ul class="select-options">
                                                <li data-value="FIXED">고정금액</li>
                                                <li data-value="PERCENT">일정비율</li>
                                            </ul>
                                            <input type="hidden" name="rp_type" id="rp_type" value="FIXED">
                                        </div>
                                        <input type="text" class="form-control" id="rp_value" name="rp_value" value="0">
                                    </div>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="form_wr">
                                    <div class="ip_tit"><h5>무료취소 기한</h5></div>
                                    <div class="d-flex align-items-center">
                                        <div class="custom-sel mr-2" id="rpFreeSelect">
                                            <button type="button" class="select-trigger">24시간</button>
                                            <ul class="select-options">
                                                <li data-value="1440">24시간</li>
                                                <li data-value="4320">3일</li>
                                            </ul>
                                            <input type="hidden" name="rp_free_cancel_before_min" id="rp_free_cancel_before_min" value="1440">
                                        </div>
                                        <p class="flex-shrink-0">전까지</p>
                                    </div>
                                    <p class="mt-2 fs_15 tg_500">예약 시간 기준으로 지정된 시간 전까지는 위약금 없이 취소 가능합니다</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </section>
            </form>

            <div class="text-center mt_50 mb-5">
                <button type="button" class="btn btn-primary btn-lg btn-w1" id="btnSaveReserveSetting">설정 저장</button>
            </div>

        </div>
    </div>

    <script>
        $(document).ready(function(){

            // =========================
            // const/let + function only
            // =========================
            const API_URL = './update.php';

            const $form = $('#reserveSettingForm');
            const $wrap = $('#timeSetWrap');
            const $btnAdd = $('#btnAddTimeSlot');
            const $btnSave = $('#btnSaveReserveSetting');

            // 템플릿은 DOM에서 제거해두고 clone만 사용
            const $template = $('#timeSetTemplate').clone(false,false);
            $('#timeSetTemplate').remove();

            // -------------------------
            // util
            // -------------------------
            function pad2(n){
                const v = parseInt(n || 0, 10);
                if (isNaN(v)) return '00';
                return (v < 10) ? '0' + v : String(v);
            }

            function sanitizeInt(val){
                const s = String(val ?? '').replace(/[^0-9]/g, '');
                if (s === '') return null;
                return parseInt(s, 10);
            }

            function dayValueFromType(type){
                const t = String(type || 'WEEKDAY').toUpperCase();
                if (t === 'SAT') return { value:'2', text:'토요일' };
                if (t === 'SUN') return { value:'4', text:'일요일' };
                return { value:'1', text:'평일' };
            }

            function dayTypeFromValue(v){
                const s = String(v || '1');
                if (s === '2') return 'SAT';
                if (s === '4') return 'SUN';
                return 'WEEKDAY';
            }

            function syncNoticeCounter(){
                let v = $('#rs_notice').val() || '';
                if (v.length > 300) {
                    v = v.substring(0, 300);
                    $('#rs_notice').val(v);
                }
                $('#rs_notice_counter').text('(' + v.length + '/300)');
            }

            // number -> text + numeric only (필요시)
            function bindOnlyDigits($input){
                $input.off('.onlyDigits');

                $input.on('input.onlyDigits', function(){
                    const before = $input.val();
                    const after = String(before).replace(/[^0-9]/g,'');
                    if (before !== after) $input.val(after);
                });

                $input.on('keypress.onlyDigits', function(e){
                    const ch = String.fromCharCode(e.which);
                    if (!/[0-9]/.test(ch)) e.preventDefault();
                });
            }

            function convertNumbersToText($scope){
                const $nums = $scope.find('input[type="number"]');
                $nums.each(function(){
                    const $inp = $(this);
                    $inp.attr('type','text');
                    $inp.attr('inputmode','numeric');
                    $inp.attr('autocomplete','off');
                    bindOnlyDigits($inp);
                });
            }

            function normalizeSwitchIds(){
                $wrap.find('.time_set').each(function(i){
                    const $row = $(this);
                    const $sw = $row.find('.custom-switch .custom-control-input');
                    const $lb = $row.find('.custom-switch .custom-control-label');
                    const id = 'slot_switch_' + (i+1);
                    $sw.attr('id', id);
                    $lb.attr('for', id);
                });
            }

            function dayOrder(dayType){
                const d = String(dayType || 'WEEKDAY').toUpperCase();
                if (d === 'WEEKDAY') return 1;
                if (d === 'SAT') return 2;
                if (d === 'SUN') return 3;
                return 9;
            }

            function sortSlotsForView(slots){
                const list = Array.isArray(slots) ? slots.slice() : [];
                list.sort(function(a, b){
                    const da = dayOrder(a.slot_day_type);
                    const db = dayOrder(b.slot_day_type);
                    if (da !== db) return da - db;

                    const ha = parseInt(a.slot_hour || 0, 10);
                    const hb = parseInt(b.slot_hour || 0, 10);
                    if (ha !== hb) return ha - hb;

                    const ma = parseInt(a.slot_minute || 0, 10);
                    const mb = parseInt(b.slot_minute || 0, 10);
                    return ma - mb;
                });
                return list;
            }
            // -------------------------
            // custom select: open class only
            // (1) time slot day select (inside #timeSetWrap only)
            // (2) rpTypeSelect
            // (3) rpFreeSelect
            // -------------------------
            function closeAllSelectOpen(){
                // open 클래스만 제거
                $('.custom-sel.open').removeClass('open');
            }

            // (1) 슬롯 요일 셀렉트
            $wrap.on('click', '.time_set .slotDaySelect .select-trigger', function(e){
                e.preventDefault();
                e.stopPropagation();

                const $sel = $(this).closest('.slotDaySelect');
                const isOpen = $sel.hasClass('open');

                closeAllSelectOpen();
                if (!isOpen) $sel.addClass('open');
                return false;
            });

            $wrap.on('click', '.time_set .slotDaySelect .select-options li', function(e){
                e.preventDefault();
                e.stopPropagation();

                const $li = $(this);
                const value = String($li.data('value') || '1');
                const text = $li.text().trim();

                const $sel = $li.closest('.slotDaySelect');
                $sel.find('.select-trigger').text(text);
                $sel.find('input[type="hidden"][name="slot_day_value"]').val(value);

                $sel.removeClass('open');
                return false;
            });

            // (2) 위약금 타입
            $(document).on('click', '#rpTypeSelect .select-trigger', function(e){
                e.preventDefault();
                e.stopPropagation();

                const $sel = $('#rpTypeSelect');
                const isOpen = $sel.hasClass('open');

                closeAllSelectOpen();
                if (!isOpen) $sel.addClass('open');
                return false;
            });

            $(document).on('click', '#rpTypeSelect .select-options li', function(e){
                e.preventDefault();
                e.stopPropagation();

                const $li = $(this);
                const value = String($li.data('value') || 'FIXED');
                const text = $li.text().trim();

                $('#rpTypeSelect .select-trigger').text(text);
                $('#rp_type').val(value);

                $('#rpTypeSelect').removeClass('open');
                return false;
            });

            // (3) 무료취소 기한
            $(document).on('click', '#rpFreeSelect .select-trigger', function(e){
                e.preventDefault();
                e.stopPropagation();

                const $sel = $('#rpFreeSelect');
                const isOpen = $sel.hasClass('open');

                closeAllSelectOpen();
                if (!isOpen) $sel.addClass('open');
                return false;
            });

            $(document).on('click', '#rpFreeSelect .select-options li', function(e){
                e.preventDefault();
                e.stopPropagation();

                const $li = $(this);
                const value = String($li.data('value') || '1440');
                const text = $li.text().trim();

                $('#rpFreeSelect .select-trigger').text(text);
                $('#rp_free_cancel_before_min').val(value);

                $('#rpFreeSelect').removeClass('open');
                return false;
            });

            // 바깥 클릭: 모두 닫기
            $(document).on('click', function(){
                closeAllSelectOpen();
            });

            // -------------------------
            // slots render
            // -------------------------
            function createSlotRow(slot){
                const $row = $template.clone(false,false);

                const use = (slot.slot_use || 'Y') === 'Y';
                const day = dayValueFromType(slot.slot_day_type);

                $row.find('.custom-switch .custom-control-input').prop('checked', use);

                // 요일
                $row.find('.slotDaySelect .select-trigger').text(day.text);
                $row.find('input[type="hidden"][name="slot_day_value"]').val(day.value);

                // 시/분
                const h = pad2(slot.slot_hour ?? 7);
                const m = pad2(slot.slot_minute ?? 0);

                const $timeInputs = $row.find('.time_form input.form-control');
                $($timeInputs.get(0)).val(h);
                $($timeInputs.get(1)).val(m);

                // 예약건수
                $row.find('input.form-control').filter(function(){
                    return $(this).closest('.time_form').length === 0;
                }).first().val(slot.slot_max_count ?? 1);

                return $row;
            }

            function renderSlots(slots){
                $wrap.find('.time_set').remove();

                let list = slots;
                if (!Array.isArray(list) || list.length === 0) {
                    list = [
                        {slot_use:'Y', slot_day_type:'WEEKDAY', slot_hour:7, slot_minute:0, slot_max_count:1, slot_sort:1},
                        {slot_use:'Y', slot_day_type:'WEEKDAY', slot_hour:7, slot_minute:30, slot_max_count:1, slot_sort:2},
                    ];
                }

                // ✅ 여기 추가: 요일/시간 정렬
                list = sortSlotsForView(list);

                for (let i=0; i<list.length; i++){
                    const $row = createSlotRow(list[i]);
                    $wrap.append($row);
                }

                convertNumbersToText($wrap);
                normalizeSwitchIds();
            }

            function applySetting(data){
                const setting = data.setting || {};
                const penalty = data.penalty || {};
                const slots = data.slots || [];

                $('#rs_notice').val(setting.rs_notice || '');
                syncNoticeCounter();

                $('#rs_allow_same_day').prop('checked', (setting.rs_allow_same_day || 'Y') === 'Y');
                $('#rs_max_reserve_days').val(setting.rs_max_reserve_days ?? 0);
                $('#rs_min_person').val(setting.rs_min_person ?? 1);
                $('#rs_max_person').val(setting.rs_max_person ?? 1);
                $('#rs_slot_unit_min').val(setting.rs_slot_unit_min ?? 30);

                $('#rp_use').prop('checked', (penalty.rp_use || 'Y') === 'Y');
                $('#rp_value').val(penalty.rp_value ?? 0);

                $('#rp_type').val((penalty.rp_type || 'FIXED'));
                $('#rpTypeSelect .select-trigger').text((String(penalty.rp_type).toUpperCase() === 'PERCENT') ? '일정비율' : '고정금액');

                const freeMin = penalty.rp_free_cancel_before_min ?? 1440;
                $('#rp_free_cancel_before_min').val(freeMin);
                $('#rpFreeSelect .select-trigger').text((freeMin === 4320) ? '3일' : '24시간');

                renderSlots(slots);
            }

            // -------------------------
            // slots_json build + duplicate validation
            // -------------------------
            function buildSlotsJsonOrFail(){
                const slots = [];
                const dup = {};

                let hasDup = false;

                $wrap.find('.time_set').each(function(i){
                    const $row = $(this);

                    const slot_use = $row.find('.custom-switch .custom-control-input').prop('checked') ? 'Y' : 'N';

                    const dayValue = $row.find('input[type="hidden"][name="slot_day_value"]').val() || '1';
                    const slot_day_type = dayTypeFromValue(dayValue);

                    const $timeInputs = $row.find('.time_form input.form-control');
                    const h = sanitizeInt($($timeInputs.get(0)).val()) ?? 0;
                    const m = sanitizeInt($($timeInputs.get(1)).val()) ?? 0;

                    const $count = $row.find('input.form-control').filter(function(){
                        return $(this).closest('.time_form').length === 0;
                    }).first();
                    let c = sanitizeInt($count.val()) ?? 1;
                    if (c < 1) c = 1;

                    // 활성만 중복 체크
                    if (slot_use === 'Y') {
                        const key = slot_day_type + '|' + h + '|' + m;
                        if (dup[key]) {
                            hasDup = true;
                            return false; // break each
                        }
                        dup[key] = true;
                    }

                    slots.push({
                        slot_use: slot_use,
                        slot_day_type: slot_day_type,
                        slot_hour: h,
                        slot_minute: m,
                        slot_max_count: c,
                        slot_sort: i + 1
                    });
                });

                if (hasDup) {
                    ModalUtil.alert({
                        title: '매장관리',
                        message: '같은 시간대를 중복으로 설정할 수 없습니다.',
                        okText: '확인',
                        onOk: function () {
                        },
                    });
                    return null;
                }

                const sorted = sortSlotsForView(slots);
                for (let i=0; i<sorted.length; i++){
                    sorted[i].slot_sort = i + 1;
                }

                $('#slots_json').val(JSON.stringify(sorted));
                return sorted;
            }

            // -------------------------
            // add/remove row
            // -------------------------
            function addSlotRowDefault(){
                const slot = {slot_use:'Y', slot_day_type:'WEEKDAY', slot_hour:7, slot_minute:0, slot_max_count:1, slot_sort:0};
                const $row = createSlotRow(slot);
                $wrap.append($row);

                convertNumbersToText($wrap);
                normalizeSwitchIds();
            }

            $btnAdd.on('click', function(){
                addSlotRowDefault();
            });

            $wrap.on('click', '.btnDeleteSlot', function(e){
                e.preventDefault();

                const count = $wrap.find('.time_set').length;
                if (count <= 1) {
                    ModalUtil.alert({
                        title: '매장관리',
                        message: '최소 1개의 시간대는 필요합니다.',
                        okText: '확인',
                        onOk: function () {
                        },
                    });
                    return false;
                }

                $(this).closest('.time_set').remove();
                normalizeSwitchIds();
                return false;
            });

            // -------------------------
            // fetch / save
            // -------------------------
            function fetchSetting(){
                $.ajax({
                    url: API_URL,
                    type: 'POST',
                    dataType: 'json',
                    data: { act:'reserve_set_get' },
                    success: function(res){
                        if (res && res.success) {
                            applySetting(res.data || {});
                        } else {
                            alert((res && res.message) ? res.message : '조회 실패');
                        }
                    },
                    error: function(xhr, status, err){
                        console.log('reserve_set_get error:', status, err, xhr.responseText);
                        alert('서버 통신 중 오류가 발생했습니다.');
                    }
                });
            }

            function appendOrReplaceHidden(name, value){
                const $exist = $form.find('input[type="hidden"][name="'+name+'"]');
                if ($exist.length) $exist.val(value);
                else $('<input>').attr({type:'hidden', name:name, value:value}).appendTo($form);
            }

            function saveSetting(){
                // 체크박스는 Y/N으로 통일
                appendOrReplaceHidden('rs_allow_same_day', $('#rs_allow_same_day').prop('checked') ? 'Y' : 'N');
                appendOrReplaceHidden('rp_use', $('#rp_use').prop('checked') ? 'Y' : 'N');

                // slots_json 생성 + 프론트 중복 체크
                const slots = buildSlotsJsonOrFail();
                if (!slots) return;

                const formData = $form.serializeArray();
                formData.push({ name:'act', value:'reserve_set_update' });

                $.ajax({
                    url: API_URL,
                    type: 'POST',
                    dataType: 'json',
                    data: formData,
                    success: function(res){
                        if (res && res.success) {
                            ModalUtil.alert({
                                title: '매장관리',
                                message: res.message || '저장되었습니다.',
                                okText: '확인',
                                onOk: function () {
                                },
                            });
                            if (res.data) applySetting(res.data);
                        } else {
                            alert((res && res.message) ? res.message : '저장 실패');
                        }
                    },
                    error: function(xhr, status, err){
                        console.log('reserve_set_update error:', status, err, xhr.responseText);
                        alert('서버 통신 중 오류가 발생했습니다.');
                    }
                });
            }

            // -------------------------
            // init
            // -------------------------
            $('#rs_notice').on('input', function(){
                syncNoticeCounter();
            });

            $btnSave.on('click', function(){
                saveSetting();
            });

            // 최초 로딩
            fetchSetting();

        });
    </script>

<? include_once("./inc/tail.php"); ?>
