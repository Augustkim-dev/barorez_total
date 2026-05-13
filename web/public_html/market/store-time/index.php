<?
$_SUB_HEAD_TITLE = "매장관리>운영시간";
$_GET['hd_pc'] = ' '; //PC hd  로그인시공백 로그아웃시 logout
$hd_num = 'store'; //왼쪽메뉴 active 땜시 만듬
$hd_left = ' '; //왼쪽메뉴 on 땜시 만듬
include_once("../inc/head.php");
include_once("../inc/header.php");
include_once("../inc/modal.php");
?>

    <!-- 왼쪽 메뉴-->
<? include_once("../inc/left_menu.php"); ?>

    <div class="sub_pg ">
        <div class="sub_wr">
            <div class="hd_tit2 flex-row-reverse">
                <div class="flex-shrink-0 ml-auto">
                    <button type="button" class="btn  btn-outline-light rounded-pill " onclick="location.href='../store' ">매장정보</button>
                    <button type="button" class="btn btn-secondary rounded-pill ml-2" onclick="location.href='../store-time' ">운영시간</button>
                    <button type="button" class="btn btn-outline-light rounded-pill ml-2" onclick="location.href='../store-set' ">기능설정</button>
                </div>
                <div class="d-flex align-items-end flex-wrap">
                    <h3 class="tit_st1 mr-5">매장관리</h3>
                </div>
            </div>
            <form>
                <section class="card">
                    <div class="card-body">
                        <div class="tit_st3 d-flex  align-items-center ">
                            <p class="mr-3"><img src="<?=DESIGN_HTTP?>/market/img/join_ico4.svg" alt="이미지"></p>
                            <div>
                                <p>매장 운영 시간</p>
                                <p class="tg_500 fs_16 fw_400">토요일/일요일/브레이크 타임을 선택하지 않을시 평일운영시간으로 진행됩니다.</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mt-5">
                                <div class="d-flex  justify-content-between mb-4">
                                    <h3 class="tit_st4 ">평일 운영</h5>
                                        <div class="custom-control custom-switch switch-outside">
                                            <input type="checkbox"
                                                   class="custom-control-input"
                                                   id="customSwitch2"
                                                   data-on="사용중"
                                                   data-off="사용안함" checked>
                                            <span class="switch-state"></span>
                                            <label class="custom-control-label" for="customSwitch2"></label>
                                        </div>
                                </div>
                                <div class="store_time">
                                    <div class=" "><input type="text" class="form-control js-time" placeholder="00시" inputmode="numeric" maxlength="2"></div>
                                    <div class=" "><input type="text" class="form-control js-time" placeholder="00분" inputmode="numeric" maxlength="2"></div>
                                    <div class=" ">~</div>
                                    <div class=" "><input type="text" class="form-control js-time" placeholder="00시" inputmode="numeric" maxlength="2"></div>
                                    <div class=" "><input type="text" class="form-control js-time" placeholder="00분" inputmode="numeric" maxlength="2"></div>
                                </div>
                            </div>
                            <div class="col-md-6 mt-5">
                                <div class="d-flex  justify-content-between mb-4">
                                    <h3 class="tit_st4 ">브레이크 타임</h5>
                                        <div class="custom-control custom-switch switch-outside">
                                            <input type="checkbox"
                                                   class="custom-control-input"
                                                   id="customSwitch2_1"
                                                   data-on="사용중"
                                                   data-off="사용안함" checked>
                                            <span class="switch-state"></span>
                                            <label class="custom-control-label" for="customSwitch2_1"></label>
                                        </div>
                                </div>
                                <div class="store_time">
                                    <div class=" "><input type="text" class="form-control js-time" placeholder="00시" inputmode="numeric" maxlength="2"></div>
                                    <div class=" "><input type="text" class="form-control js-time" placeholder="00분" inputmode="numeric" maxlength="2"></div>
                                    <div class=" ">~</div>
                                    <div class=" "><input type="text" class="form-control js-time" placeholder="00시" inputmode="numeric" maxlength="2"></div>
                                    <div class=" "><input type="text" class="form-control js-time" placeholder="00분" inputmode="numeric" maxlength="2"></div>
                                </div>
                            </div>
                            <div class="col-md-6 mt-5">
                                <div class="d-flex  justify-content-between mb-4">
                                    <h3 class="tit_st4 ">토요일 운영</h5>
                                        <div class="custom-control custom-switch switch-outside">
                                            <input type="checkbox"
                                                   class="custom-control-input"
                                                   id="customSwitch2_2"
                                                   data-on="사용중"
                                                   data-off="사용안함">
                                            <span class="switch-state"></span>
                                            <label class="custom-control-label" for="customSwitch2_2"></label>
                                        </div>
                                </div>
                                <div class="store_time">
                                    <div class=" "><input type="text" class="form-control js-time" placeholder="00시" inputmode="numeric" maxlength="2"></div>
                                    <div class=" "><input type="text" class="form-control js-time" placeholder="00분" inputmode="numeric" maxlength="2"></div>
                                    <div class=" ">~</div>
                                    <div class=" "><input type="text" class="form-control js-time" placeholder="00시" inputmode="numeric" maxlength="2"></div>
                                    <div class=" "><input type="text" class="form-control js-time" placeholder="00분" inputmode="numeric" maxlength="2"></div>
                                </div>
                            </div>
                            <div class="col-md-6 mt-5">
                                <div class="d-flex  justify-content-between mb-4">
                                    <h3 class="tit_st4 ">일요일 운영</h5>
                                        <div class="custom-control custom-switch switch-outside">
                                            <input type="checkbox"
                                                   class="custom-control-input"
                                                   id="customSwitch2_3"
                                                   data-on="사용중"
                                                   data-off="사용안함">
                                            <span class="switch-state"></span>
                                            <label class="custom-control-label" for="customSwitch2_3"></label>
                                        </div>
                                </div>
                                <div class="store_time">
                                    <div class=" "><input type="text" class="form-control js-time" placeholder="00시" inputmode="numeric" maxlength="2"></div>
                                    <div class=" "><input type="text" class="form-control js-time" placeholder="00분" inputmode="numeric" maxlength="2"></div>
                                    <div class=" ">~</div>
                                    <div class=" "><input type="text" class="form-control js-time" placeholder="00시" inputmode="numeric" maxlength="2"></div>
                                    <div class=" "><input type="text" class="form-control js-time" placeholder="00분" inputmode="numeric" maxlength="2"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section class="card mt-4">
                    <div class="card-body">
                        <h3 class="tit_st3 mb-4 ">정기휴무일</h5>
                            <div class="btn-group-toggle store_gbtn" data-toggle="buttons">
                                <label class="btn btn-outline-light ">
                                    <input type="checkbox" name="options" id="option1"> 일요일
                                </label>
                                <label class="btn btn-outline-light  ">
                                    <input type="checkbox" name="options" id="option2"> 월요일
                                </label>
                                <label class="btn btn-outline-light  ">
                                    <input type="checkbox" name="options" id="option3"> 화요일
                                </label>
                                <label class="btn btn-outline-light  ">
                                    <input type="checkbox" name="options" id="option4"> 수요일
                                </label>
                                <label class="btn btn-outline-light  ">
                                    <input type="checkbox" name="options" id="option5"> 목요일
                                </label>
                                <label class="btn btn-outline-light  ">
                                    <input type="checkbox" name="options" id="option6"> 금요일
                                </label>
                                <label class="btn btn-outline-light  ">
                                    <input type="checkbox" name="options" id="option7"> 토요일
                                </label>
                            </div>
                    </div>
                </section>
                <section class="card mt-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h3 class="tit_st3  ">임시휴무일</h3>
                                <p class="mb-4 mt-2">해당 날짜가 지나면 자동으로 삭제됩니다.</p>
                            </div>
                            <p><button type="button" class="btn btn-secondary rounded-pill" data-toggle="modal" data-target="#modal_store">추가</button></p>
                        </div>
                        <div class="store_holiday">
                            <button type="button" class="btn btn-light  ">📅 2026.01.08 ~ 2026.01.20 <img src="<?=DESIGN_HTTP?>/market/img/ic_close.png" alt="닫기" style="width: 1.7rem;" class="ml-2"></button>
                            <button type="button" class="btn btn-light  ">📅 2026.05.01 <img src="<?=DESIGN_HTTP?>/market/img/ic_close.png" alt="닫기" style="width: 1.7rem;" class="ml-2"></button>
                            <button type="button" class="btn btn-light  ">📅 2026.08.01 ~ 2026.08.04 <img src="<?=DESIGN_HTTP?>/market/img/ic_close.png" alt="닫기" style="width: 1.7rem;" class="ml-2"></button>

                        </div>

                    </div>
                </section>
                <div class="text-center mt_50 mb-5">
                    <button type="button" class="btn btn-primary btn-lg btn-w1">저장</button>

                </div>
            </form>

        </div><!-- data-toggle="modal" data-target="#modal_store" 임시휴무일 추가(모달)-->
        <!-- 모달 md 1 -->
        <div class="modal fade" id="modal_store" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">임시휴무일 추가</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><img src="<?=DESIGN_HTTP?>/market/img/ic_close.png"></button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex date_input">
                            <input type="date" class="form-control  ">
                            <p>~</p>
                            <input type="date" class="form-control   ">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="form-row">
                            <div class="col-12"><button type="button" class="btn btn-secondary btn-block">추가</button></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
    (function () {
        'use strict';
        if (!window.jQuery) return;
        const $ = window.jQuery;

        const API_URL = './update.php';

        // -----------------------
        // DOM (네 HTML id 그대로)
        // -----------------------
        const $swWeek  = $('#customSwitch2');    // 평일 운영
        const $swBreak = $('#customSwitch2_1');  // 브레이크 타임
        const $swSat   = $('#customSwitch2_2');  // 토요일 운영(영업 여부)
        const $swSun   = $('#customSwitch2_3');  // 일요일 운영(영업 여부)

        // row 안 col-md-6 4개 순서: 0=평일, 1=브레이크, 2=토요일, 3=일요일
        const $cols = $('.card').first().find('.row').first().find('.col-md-6');
        const $weekTime  = $cols.eq(0).find('.store_time input');
        const $breakTime = $cols.eq(1).find('.store_time input');
        const $satTime   = $cols.eq(2).find('.store_time input');
        const $sunTime   = $cols.eq(3).find('.store_time input');

        const $btnSave = $('.btn.btn-primary.btn-lg.btn-w1').last();

        // 정기휴무일 체크(일~토: option1..option7)  option1=일(0), option2=월(1)... option7=토(6)
        const holidayMap = {
            0: $('#option1'),
            1: $('#option2'),
            2: $('#option3'),
            3: $('#option4'),
            4: $('#option5'),
            5: $('#option6'),
            6: $('#option7'),
        };

        // 임시휴무 UI
        const $tempWrap   = $('.store_holiday');
        const $modal      = $('#modal_store');
        const $tempStart  = $modal.find('input[type="date"]').eq(0);
        const $tempEnd    = $modal.find('input[type="date"]').eq(1);
        const $btnTempAdd = $modal.find('.modal-footer .btn.btn-secondary');

        // -----------------------
        // state
        // -----------------------
        let isFilling = false;
        let week  = null; // { "0":{bt_type,start_time,end_time}, ... }  bt_type: OPEN|CLOSE
        let brk   = null; // {start_time,end_time} or null
        let temps = [];

        // -----------------------
        // utils
        // -----------------------
        const pad2 = (n) => String(parseInt(n, 10) || 0).padStart(2, '0');

        function isChecked($el){ return $el.is(':checked'); }

        function timeToMin(st){ // "HH:MM:SS"
            if (!st) return null;
            const p = String(st).split(':');
            const h = parseInt(p[0], 10);
            const m = parseInt(p[1], 10);
            if (isNaN(h) || isNaN(m)) return null;
            return h * 60 + m;
        }

// start < end 이어야 true (같으면 false)
        function isValidRange(st, et){
            const a = timeToMin(st);
            const b = timeToMin(et);
            if (a === null || b === null) return false;
            return a < b;
        }

        // ✅ checked 설정 + 스위치 텍스트까지 맞춤
        function setChecked($el, on, trigger = true){
            $el.prop('checked', !!on);
            if (trigger) $el.trigger('change');
            syncOneSwitchState($el); // ✅ 트리거 여부와 상관없이 표시 동기화
        }

        function enable4($inputs, on){
            $inputs.prop('disabled', !on);
        }

        function splitTime(t){
            if (!t) return { h:'', m:'' };
            const s = String(t).split(':');
            return { h: String(parseInt(s[0],10) || 0), m: String(parseInt(s[1],10) || 0) };
        }

        function set4($inputs, st, et){
            const a = splitTime(st);
            const b = splitTime(et);
            $inputs.eq(0).val(a.h);
            $inputs.eq(1).val(a.m);
            $inputs.eq(2).val(b.h);
            $inputs.eq(3).val(b.m);
        }

        function get4($inputs){
            const sh = $.trim($inputs.eq(0).val());
            const sm = $.trim($inputs.eq(1).val());
            const eh = $.trim($inputs.eq(2).val());
            const em = $.trim($inputs.eq(3).val());
            const st = pad2(sh) + ':' + pad2(sm) + ':00';
            const et = pad2(eh) + ':' + pad2(em) + ':00';
            return { st, et, sh, sm, eh, em };
        }

        // -----------------------
        // ✅ 스위치 "사용중/사용안함" 표시 동기화
        // -----------------------
        function syncOneSwitchState($checkbox){
            // checkbox -> wrap(.switch-outside) -> .switch-state 텍스트 맞추기
            const $wrap = $checkbox.closest('.custom-control.custom-switch.switch-outside');
            const $state = $wrap.find('.switch-state').first();
            if (!$state.length) return;

            const on = $checkbox.is(':checked');
            const onText  = $checkbox.attr('data-on')  || '사용중';
            const offText = $checkbox.attr('data-off') || '사용안함';

            $state.text(on ? onText : offText);
            $state.toggleClass('is-on', on);
        }

        function syncAllSwitchStates(){
            $('.custom-control.custom-switch.switch-outside input[type="checkbox"].custom-control-input')
                .each(function(){ syncOneSwitchState($(this)); });
        }

        // change 이벤트에도 항상 동기화
        function bindSwitchStateSync(){
            $(document).on('change', '.custom-control.custom-switch.switch-outside input[type="checkbox"].custom-control-input', function(){
                syncOneSwitchState($(this));
            });
        }

        // -----------------------
        // time inputs: text + 숫자만 + 범위 보정
        // -----------------------
        function bindTimeInputs(){
            // ✅ 네가 원한대로: input 타입을 text로 강제 변경 + class 부여
            $('.store_time input').each(function(){
                $(this).attr('type', 'text').addClass('js-time').attr('inputmode','numeric');
            });

            // 숫자만 입력
            $(document).on('input', '.js-time', function(){
                let v = String(this.value || '');
                v = v.replace(/[^0-9]/g, ''); // 마이너스 포함 전부 제거
                if (v.length > 2) v = v.slice(0, 2);
                this.value = v;
            });

            // 붙여넣기 방어
            $(document).on('paste', '.js-time', function(){
                setTimeout(() => {
                    let v = String(this.value || '').replace(/[^0-9]/g, '').slice(0,2);
                    this.value = v;
                }, 0);
            });

            // blur 시 범위 보정 + 2자리
            // 시(index 0,2): 0~23 / 분(index 1,3): 0~59
            $(document).on('blur', '.store_time input.js-time', function(){
                const $inputs = $(this).closest('.store_time').find('input.js-time');
                const idx = $inputs.index(this);

                let n = parseInt(this.value, 10);
                if (isNaN(n)) { this.value = ''; return; }

                const isHour = (idx === 0 || idx === 2);
                const max = isHour ? 23 : 59;

                if (n < 0) n = 0;
                if (n > max) n = max;

                this.value = String(n).padStart(2,'0');
            });

            // 휠로 값 바뀌는 것 방지
            $(document).on('wheel', '.store_time input.js-time', function(e){
                this.blur();
                e.preventDefault();
            });
        }

        // -----------------------
        // holiday helpers
        // -----------------------
        let isSyncing = false;

        function setHolidayUI(dow, checked){
            const $c = holidayMap[dow];
            if (!$c || !$c.length) return;
            $c.prop('checked', !!checked);
            $c.closest('label').toggleClass('active', !!checked);
        }

        function isHoliday(dow){
            const $c = holidayMap[dow];
            return ($c && $c.length) ? $c.is(':checked') : false;
        }

        function allWeekdaysHoliday(){
            return isHoliday(1) && isHoliday(2) && isHoliday(3) && isHoliday(4) && isHoliday(5);
        }

        // ✅ 정기휴무 상태를 스위치에 반영 (표시까지 동기화)
        function applyHolidayToSwitches(){
            const satHoliday = isHoliday(6);
            const sunHoliday = isHoliday(0);
            const weekOff = allWeekdaysHoliday();

            // 토요일
            $swSat.prop('checked', !satHoliday);
            enable4($satTime, !satHoliday);

            // 일요일
            $swSun.prop('checked', !sunHoliday);
            enable4($sunTime, !sunHoliday);

            // 평일
            $swWeek.prop('checked', !weekOff);
            enable4($weekTime, !weekOff);

            // 브레이크는 정기휴무와 무관

            // ✅ 텍스트 동기화
            syncAllSwitchStates();
        }

        // -----------------------
        // temp holiday UI
        // -----------------------
        function fmtTempLabel(sd, ed){
            if (sd && ed && sd !== ed) return `📅 ${sd.replaceAll('-','.')} ~ ${ed.replaceAll('-','.')}`;
            return `📅 ${String(sd||'').replaceAll('-','.')}`;
        }

        function renderTemps(){
            $tempWrap.empty();
            (temps || []).forEach(t => {
                const label = fmtTempLabel(t.start_date, t.end_date);
                const $btn = $(`
        <button type="button" class="btn btn-light __temp_item" data-idx="${t.idx}">
          ${label}
          <img src="<?=DESIGN_HTTP?>/market/img/ic_close.png" alt="닫기" style="width:1.7rem;" class="ml-2">
        </button>
      `);
                $tempWrap.append($btn);
            });
        }

        // -----------------------
        // DB -> UI (fill)
        // -----------------------
        function fillUI(){
            if (!week) return;
            isFilling = true;

            // 1) 정기휴무 체크 = CLOSE인 요일
            for (let d=0; d<=6; d++){
                const it = week[String(d)] || {};
                setHolidayUI(d, it.bt_type === 'CLOSE');
            }

            // 2) 평일 대표 시간: 1~5 OPEN 첫번째
            let wSt = null, wEt = null;
            let anyWeekOpen = false;
            for (let d=1; d<=5; d++){
                const it = week[String(d)];
                if (it && it.bt_type === 'OPEN'){
                    anyWeekOpen = true;
                    if (!wSt && it.start_time && it.end_time){
                        wSt = it.start_time;
                        wEt = it.end_time;
                    }
                }
            }
            if (!wSt) { wSt = '09:00:00'; wEt = '20:00:00'; }

            // 3) 평일 스위치/입력
            setChecked($swWeek, anyWeekOpen, false);
            set4($weekTime, wSt, wEt);
            enable4($weekTime, anyWeekOpen);

            // 4) 토요일(6) - 스위치 = 영업 여부
            const sat = week["6"] || { bt_type:'CLOSE' };
            const satOpen = (sat.bt_type === 'OPEN');
            setChecked($swSat, satOpen, false);
            enable4($satTime, satOpen);
            if (satOpen) set4($satTime, sat.start_time || wSt, sat.end_time || wEt);
            else set4($satTime, wSt, wEt);

            // 5) 일요일(0)
            const sun = week["0"] || { bt_type:'CLOSE' };
            const sunOpen = (sun.bt_type === 'OPEN');
            setChecked($swSun, sunOpen, false);
            enable4($sunTime, sunOpen);
            if (sunOpen) set4($sunTime, sun.start_time || wSt, sun.end_time || wEt);
            else set4($sunTime, wSt, wEt);

            // 6) 브레이크
            const bkOn = !!(brk && brk.start_time && brk.end_time);
            setChecked($swBreak, bkOn, false);
            enable4($breakTime, bkOn);
            set4($breakTime, brk?.start_time || '15:00:00', brk?.end_time || '16:00:00');

            renderTemps();

            // ✅ 새로고침/초기 fill에서도 스위치 텍스트 정확히 맞춤
            syncAllSwitchStates();

            isFilling = false;
        }

        // -----------------------
        // 스위치 ↔ 정기휴무 자동 매핑
        // -----------------------
        function bindSwitches(){

            // 1) 스위치 변경 → 정기휴무 반영
            $swWeek.on('change', function(){
                if (isFilling || isSyncing) return;
                isSyncing = true;

                const on = isChecked($swWeek);
                enable4($weekTime, on);

                // 평일 ON이면 월~금 정기휴무 해제, OFF면 월~금 체크
                for (let d=1; d<=5; d++){
                    setHolidayUI(d, !on);
                }

                applyHolidayToSwitches();
                isSyncing = false;
            });

            $swSat.on('change', function(){
                if (isFilling || isSyncing) return;
                isSyncing = true;

                const on = isChecked($swSat);
                enable4($satTime, on);

                // 토요일 운영 ON이면 토요일 정기휴무 해제 / OFF면 체크
                setHolidayUI(6, !on);

                applyHolidayToSwitches();
                isSyncing = false;
            });

            $swSun.on('change', function(){
                if (isFilling || isSyncing) return;
                isSyncing = true;

                const on = isChecked($swSun);
                enable4($sunTime, on);

                // 일요일 운영 ON이면 일요일 정기휴무 해제 / OFF면 체크
                setHolidayUI(0, !on);

                applyHolidayToSwitches();
                isSyncing = false;
            });

            $swBreak.on('change', function(){
                if (isFilling || isSyncing) return;
                enable4($breakTime, isChecked($swBreak));
                syncOneSwitchState($swBreak); // 텍스트 즉시 맞춤
            });

            // 2) 정기휴무 체크박스 변경 → 스위치 반영
            $(document).on('change', '#option1,#option2,#option3,#option4,#option5,#option6,#option7', function(){
                if (isFilling || isSyncing) return;
                isSyncing = true;

                // 정기휴무 상태에 맞춰 스위치 동기화
                applyHolidayToSwitches();

                isSyncing = false;
            });
        }

        // -----------------------
        // 저장 payload 만들기
        // -----------------------
        function buildWeekJson(){
            const weekOn = isChecked($swWeek);
            const w = get4($weekTime);

            if (weekOn && (w.sh==='' || w.sm==='' || w.eh==='' || w.em==='')){
                ModalUtil.alert({
                    title: '매장관리',
                    message: '평일 운영 시간을 입력해 주세요.',
                    okText: '확인',
                    onOk: function () {
                    },
                });
                return null;
            }

            const satOn = isChecked($swSat);
            const sunOn = isChecked($swSun);

            const satT = get4($satTime);
            const sunT = get4($sunTime);

            // ✅ 평일 운영시간 검증
            if (weekOn) {
                if (!isValidRange(w.st, w.et)) {
                    ModalUtil.alert({
                        title: '매장관리',
                        message: '평일 운영시간이 올바르지 않습니다. (오픈 < 마감)',
                        okText: '확인',
                        onOk: function () {
                        },
                    });
                    return null;
                }
            }

// ✅ 토요일: 스위치 ON이고, 시간을 입력했거나 평일 fallback을 쓰는 경우 검증
            if (satOn) {
                const st = (satT.sh==='' || satT.sm==='' || satT.eh==='' || satT.em==='') ? w.st : satT.st;
                const et = (satT.sh==='' || satT.sm==='' || satT.eh==='' || satT.em==='') ? w.et : satT.et;

                if (!st || !et || !isValidRange(st, et)) {
                    ModalUtil.alert({
                        title: '매장관리',
                        message: '토요일 운영시간이 올바르지 않습니다. (오픈 < 마감)',
                        okText: '확인',
                        onOk: function () {
                        },
                    });
                    return null;
                }
            }

// ✅ 일요일
            if (sunOn) {
                const st = (sunT.sh==='' || sunT.sm==='' || sunT.eh==='' || sunT.em==='') ? w.st : sunT.st;
                const et = (sunT.sh==='' || sunT.sm==='' || sunT.eh==='' || sunT.em==='') ? w.et : sunT.et;

                if (!st || !et || !isValidRange(st, et)) {
                    ModalUtil.alert({
                        title: '매장관리',
                        message: '일요일 운영시간이 올바르지 않습니다. (오픈 < 마감)',
                        okText: '확인',
                        onOk: function () {
                        },
                    });
                    return null;
                }
            }

            // 토/일 스위치 ON이면 시간 입력이 비어있을 때 "평일시간 fallback" 허용
            // 단, 평일도 OFF인데 토/일만 ON이면 시간 필수
            const needSatTime = satOn && !weekOn && (satT.sh==='' || satT.sm==='' || satT.eh==='' || satT.em==='');
            const needSunTime = sunOn && !weekOn && (sunT.sh==='' || sunT.sm==='' || sunT.eh==='' || sunT.em==='');

            if (needSatTime){
                ModalUtil.alert({
                    title: '매장관리',
                    message: '토요일 운영 시간을 입력해 주세요.',
                    okText: '확인',
                    onOk: function () {
                    },
                });
                return null;
            }
            if (needSunTime){
                ModalUtil.alert({
                    title: '매장관리',
                    message: '일요일 운영 시간을 입력해 주세요.',
                    okText: '확인',
                    onOk: function () {
                    },
                });
                return null;
            }

            const out = {};
            for (let d=0; d<=6; d++){
                out[String(d)] = { bt_type:'CLOSE', start_time:null, end_time:null };

                // 정기휴무 체크면 CLOSE
                if (isHoliday(d)) continue;

                // 월~금
                if (d>=1 && d<=5){
                    if (!weekOn) continue;
                    out[String(d)] = { bt_type:'OPEN', start_time:w.st, end_time:w.et };
                    continue;
                }

                // 토요일
                if (d===6){
                    if (!satOn) continue;
                    const st = (satT.sh==='' || satT.sm==='' || satT.eh==='' || satT.em==='') ? w.st : satT.st;
                    const et = (satT.sh==='' || satT.sm==='' || satT.eh==='' || satT.em==='') ? w.et : satT.et;
                    out["6"] = { bt_type:'OPEN', start_time: st, end_time: et };
                    continue;
                }

                // 일요일
                if (d===0){
                    if (!sunOn) continue;
                    const st = (sunT.sh==='' || sunT.sm==='' || sunT.eh==='' || sunT.em==='') ? w.st : sunT.st;
                    const et = (sunT.sh==='' || sunT.sm==='' || sunT.eh==='' || sunT.em==='') ? w.et : sunT.et;
                    out["0"] = { bt_type:'OPEN', start_time: st, end_time: et };
                    continue;
                }
            }

            return out;
        }

        // -----------------------
        // 서버 호출
        // -----------------------
        function fetchData(){
            $.ajax({
                url: API_URL,
                type: 'POST',
                dataType: 'json',
                data: { act:'time_get' },
                success: function(res){
                    if (!res || !res.success){
                        alert((res && res.message) ? res.message : '데이터를 불러오지 못했습니다.');
                        return;
                    }
                    week  = res.data.week || {};
                    brk   = res.data.break || null;
                    temps = res.data.temp || [];
                    fillUI();
                    applyHolidayToSwitches(); // ✅ fill 후 한번 더 정리
                },
                error: function(xhr){
                    console.log(xhr.responseText);
                    alert('서버 통신 오류');
                }
            });
        }

        function saveData(){
            const outWeek = buildWeekJson();
            if (!outWeek) return;

            const breakOn = isChecked($swBreak);
            const bk = get4($breakTime);
            if (breakOn && (bk.sh==='' || bk.sm==='' || bk.eh==='' || bk.em==='')){
                ModalUtil.alert({
                    title: '매장관리',
                    message: '브레이크 타임 시간을 입력해 주세요.',
                    okText: '확인',
                    onOk: function () {
                    },
                });
                return;
            }

            if (breakOn) {
                if (!isValidRange(bk.st, bk.et)) {
                    ModalUtil.alert({
                        title: '매장관리',
                        message: '브레이크 타임 시간이 올바르지 않습니다. (시작 < 종료)',
                        okText: '확인',
                        onOk: function () {
                        },
                    });
                    return;
                }
            }

            const postData = {
                act: 'time_save',
                week_json: JSON.stringify(outWeek),
                break_on: breakOn ? 'Y' : 'N',
                break_start: breakOn ? bk.st : '',
                break_end: breakOn ? bk.et : '',
            };

            $.ajax({
                url: API_URL,
                type: 'POST',
                dataType: 'json',
                data: postData,
                success: function(res){
                    if (!res || !res.success){
                        alert((res && res.message) ? res.message : '저장 실패');
                        return;
                    }
                    ModalUtil.alert({
                        title: '매장관리',
                        message: res.message || '저장되었습니다.',
                        okText: '확인',
                        onOk: function () {
                        },
                    });
                    fetchData();
                },
                error: function(xhr){
                    console.log(xhr.responseText);
                    alert('서버 통신 오류');
                }
            });
        }

        function addTempHoliday(){
            const sd = $tempStart.val();
            const ed = $tempEnd.val() || sd;
            if (!sd){ alert('시작일을 선택해 주세요.'); return; }
            if (sd > ed){ alert('종료일이 시작일보다 빠릅니다.'); return; }

            $.ajax({
                url: API_URL,
                type: 'POST',
                dataType: 'json',
                data: { act:'temp_add', start_date: sd, end_date: ed },
                success: function(res){
                    if (!res || !res.success){
                        alert((res && res.message) ? res.message : '추가 실패');
                        return;
                    }
                    try { $modal.modal('hide'); } catch(e){}
                    $tempStart.val(''); $tempEnd.val('');
                    fetchData();
                },
                error: function(xhr){
                    console.log(xhr.responseText);
                    alert('서버 통신 오류');
                }
            });
        }

        function delTempHoliday(idx){
            $.ajax({
                url: API_URL,
                type: 'POST',
                dataType: 'json',
                data: { act:'temp_del', idx: idx },
                success: function(res){
                    if (!res || !res.success){
                        alert((res && res.message) ? res.message : '삭제 실패');
                        return;
                    }
                    fetchData();
                },
                error: function(xhr){
                    console.log(xhr.responseText);
                    alert('서버 통신 오류');
                }
            });
        }

        // -----------------------
        // init
        // -----------------------
        $(function(){
            bindSwitchStateSync(); // ✅ 스위치 텍스트 자동 동기화
            bindSwitches();
            bindTimeInputs();

            $btnSave.on('click', function(){ saveData(); });
            $btnTempAdd.on('click', function(){ addTempHoliday(); });

            $(document).on('click', '.__temp_item', function(){
                const idx = parseInt($(this).data('idx'), 10);
                if (!idx) return;
                delTempHoliday(idx);
            });

            // ✅ 새로고침 직후에도 퍼블 초기표기 꼬임 방지
            syncAllSwitchStates();

            fetchData();
        });

    })();
</script>

<? include_once("./inc/tail.php"); ?>
