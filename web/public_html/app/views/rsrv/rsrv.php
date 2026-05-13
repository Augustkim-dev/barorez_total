
<div class="wrap">
    <div class="sub_pg pb_lg">
        <div class="container pb-5">
            <p class="fs_18 fw_600 mt-5">예약 날짜를 선택하세요</p>
            <div class="calendar border">
                <div class="calendar-header">
                    <a href="#" class="arrow mr-2" id="prevMonth">
                        <img src="<?=DESIGN_HTTP?>/img/pg_prev.png" width="17px">
                    </a>
                    <span id="calendarMonthLabel" class="mx-3"></span>
                    <a href="#" class="arrow" id="nextMonth">
                        <img src="<?=DESIGN_HTTP?>/img/pg_next.png" width="17px">
                    </a>
                </div>
                <table>
                    <thead>
                    <tr>
                        <th>일</th><th>월</th><th>화</th><th>수</th><th>목</th><th>금</th><th>토</th>
                    </tr>
                    </thead>
                    <tbody id="calendarBody"></tbody>
                </table>
            </div>
        </div>

        <div class="container border-top py-5">
            <p class="fs_18 fw_600">예약 시간 선택하세요</p>
            <div class="mx_n16 mt-4">
                <div class="scroll_bar_none scroll_mouse">
                    <div class="btn-group btn-group-toggle px_16" data-toggle="buttons" id="timeSlotContainer"></div>
                </div>
            </div>
        </div>

        <div class="container border-top py-5">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <p class="fs_18 fw_600">인원을 선택하세요</p>
                    <p class="fs_13 tg_400 mt-2">성인, 유아 구분없이 전체 인원 선택</p>
                </div>
                <div class="item_opt_counter">
                    <button type="button" class="btn item_opt_counter_btn" id="qtyDec">
                        <img src="<?=DESIGN_HTTP?>/img/ico_decrease.svg" alt="감소">
                    </button>
                    <input type="text" class="quantity" id="peopleQty" value="1" readonly>
                    <button type="button" class="btn item_opt_counter_btn" id="qtyInc">
                        <img src="<?=DESIGN_HTTP?>/img/ico_increase.svg" alt="증가">
                    </button>
                </div>
            </div>
        </div>

        <div class="bar"></div>

        <div class="container py-5">
            <p class="fs_18 fw_600">예약자 정보</p>
            <div class="form_wr mt_20">
                <div class="ip_tit"><h5>예약자명</h5></div>
                <input type="text" class="form-control" id="reserver_name" placeholder="이름을 입력하세요">
            </div>
            <div class="form_wr mt_20">
                <div class="ip_tit"><h5>휴대폰번호</h5></div>
                <input type="text" class="form-control" id="reserver_phone" placeholder="'-' 없이 숫자만 입력">
            </div>
        </div>

        <div class="bar"></div>

        <div class="container py-5">
            <p class="fs_18 fw_600">
                <img src="<?=DESIGN_HTTP?>/img/ico_alim.png" width="31px">
                예약 전 반드시 확인하세요!
            </p>
            <p class="fw_600 mt-4">노쇼 / 당일 예약취소는 환불이 불가능합니다.</p>
            <ul class="list_style_2 fs_15 mt-3">
                <li>건전한 예약문화를 위해 노쇼 / 당일 예약취소 건에 대해서는 환불이 불가능합니다.</li>
                <li><?=$rsvMsg['rs_notice']?></li>
            </ul>
            <p class="mt-3 fw_600">예약완료 후 업체의 확인연락 후 예약이 확정됩니다.</p>
        </div>

        <?php if (empty($_SESSION['cart_ct_ids'])): ?>
            <div class="bottom_btn tg_600">
                <p class="mb-3 text-center fs_15">모든 내용을 확인하셨나요?</p>
                <button type="button" class="btn btn-primary btn-block btn-lg" onclick="submitReservation()">
                    즉시 예약
                </button>
            </div>
        <?php else: ?>
            <div class="bottom_btn tg_600">
                <p class="mb-3 text-center fs_15">모든 내용을 확인하셨나요?</p>
                <button type="button" class="btn btn-primary btn-block btn-lg" onclick="submitNext()">
                    다음
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php include_once("../inc/modal.php");?>
<script>
    (function() {
        const allowSameDay   = <?= json_encode($allowSameDay) ?>;
        const closedDays     = <?= $closedDaysJson ?>;
        const slotsByDayType = <?= $slotsJson ?>;
        const todayStr       = '<?= $todayStr ?>';
        const currentTime    = '<?= $currentTime ?>';

        let today = new Date();
        let currentYear  = today.getFullYear();
        let currentMonth = today.getMonth() + 1;

        let selectedDateStr = formatDate(currentYear, currentMonth, today.getDate());
        let selectedTime    = '';

        const calendarBody   = document.getElementById('calendarBody');
        const monthLabel     = document.getElementById('calendarMonthLabel');
        const prevMonthBtn   = document.getElementById('prevMonth');
        const nextMonthBtn   = document.getElementById('nextMonth');
        const timeContainer  = document.getElementById('timeSlotContainer');

        const qtyInput  = document.getElementById('peopleQty');
        const qtyDecBtn = document.getElementById('qtyDec');
        const qtyIncBtn = document.getElementById('qtyInc');

        const nameInput  = document.getElementById('reserver_name');
        const phoneInput = document.getElementById('reserver_phone');

        function formatDate(y, m, d) {
            return y + '-' + String(m).padStart(2, '0') + '-' + String(d).padStart(2, '0');
        }

        function getDayType(date) {
            const dow = new Date(date).getDay();
            if (dow === 0) return 'SUN';
            if (dow === 6) return 'SAT';
            return 'WEEKDAY';
        }

        function isClosed(dateStr) {
            const dateObj = new Date(dateStr);
            return closedDays.includes(dateObj.getDay());
        }

        function isPast(dateStr) {
            return new Date(dateStr) < new Date(today.getFullYear(), today.getMonth(), today.getDate());
        }

        function isToday(dateStr) {
            return dateStr === todayStr;
        }

        function isTimeBeforeCurrent(time) {
            // 1. 오늘 날짜가 아니면 과거 시간 체크 안 함
            if (!isToday(selectedDateStr)) {
                return false;
            }

            // 2. 문자열을 분 단위 숫자로 변환해서 비교
            const toMinutes = (t) => {
                const [hour, minute] = t.split(':').map(Number);
                return hour * 60 + minute;
            };
            const currentMinutes = toMinutes(currentTime);   // 예: "14:50" → 890
            const slotMinutes    = toMinutes(time);          // 예: "07:00" → 420

            return slotMinutes < currentMinutes;   // 현재 시간보다 이전이면 true
        }

        // ==================== 시간 슬롯 렌더링 + 이벤트 재바인딩 ====================
        async function renderTimeSlots(dateStr) {
            timeContainer.innerHTML = '<p class="text-center text-muted py-4">시간 확인 중...</p>';

            const dayType = getDayType(dateStr);
            const availableSlots = slotsByDayType[dayType] || [];

            if (availableSlots.length === 0) {
                timeContainer.innerHTML = '<p class="text-center text-muted py-4">해당 날짜에 예약 가능한 시간이 없습니다.</p>';
                selectedTime = '';
                return;
            }

            timeContainer.innerHTML = '';
            let hasEnabled = false;
            let firstEnabled = '';

            for (const slot of availableSlots) {
                const time = slot.time;
                const maxCount = slot.max_count;

                const isBeforeCurrent = isTimeBeforeCurrent(time);
                let reservedCount = 0;

                try {
                    reservedCount = await getReservedCount(dateStr, time);
                } catch (e) {
                    console.warn('예약 건수 조회 실패');
                }

                const isFull = reservedCount >= maxCount;
                const disabled = isBeforeCurrent || isFull;

                const id = 'time_' + time.replace(/:/g, '');

                const isChecked = !hasEnabled && !disabled;
                if (isChecked) {
                    hasEnabled = true;
                    firstEnabled = time;
                }

                const btnClass = disabled
                    ? 'btn-outline-secondary disabled'
                    : (isChecked ? 'btn-outline-primary active' : 'btn-outline-primary');

                const html = `
            <label class="btn ${btnClass} rounded-pill mx-1 mb-2">
                <input type="radio" name="reservation_time" id="${id}" value="${time}"
                       ${isChecked ? 'checked' : ''} ${disabled ? 'disabled' : ''}>
                ${time}
                ${isFull ? '<small class="text-danger d-block">(예약마감)</small>' : ''}
                ${isBeforeCurrent ? '<small class="text-danger d-block">(지난 시간)</small>' : ''}
            </label>
        `;

                timeContainer.insertAdjacentHTML('beforeend', html);
            }

            selectedTime = firstEnabled || '';
            rebindTimeSlotEvents();
        }

        // 시간 슬롯 이벤트 재바인딩 (중요!)
        function rebindTimeSlotEvents() {
            timeContainer.querySelectorAll('input[name="reservation_time"]').forEach(input => {
                input.addEventListener('change', function() {
                    if (this.checked) selectedTime = this.value;
                });
            });

            // disabled 라벨 클릭 방지
            timeContainer.querySelectorAll('label.disabled').forEach(label => {
                label.addEventListener('click', e => e.preventDefault());
            });
        }

        // ==================== 캘린더 ====================
        function renderCalendar() {
            monthLabel.textContent = `${currentYear}. ${String(currentMonth).padStart(2, '0')}`;

            let firstDay = new Date(currentYear, currentMonth - 1, 1).getDay();
            let daysInMonth = new Date(currentYear, currentMonth, 0).getDate();

            let html = '';
            let day = 1;

            for (let week = 0; week < 6; week++) {
                html += '<tr>';
                for (let dow = 0; dow < 7; dow++) {
                    if (week === 0 && dow < firstDay) {
                        html += '<td></td>';
                        continue;
                    }
                    if (day > daysInMonth) {
                        html += '<td></td>';
                        continue;
                    }

                    const dateStr = formatDate(currentYear, currentMonth, day);
                    const isPastDate = isPast(dateStr);
                    const isClosedDate = isClosed(dateStr);
                    const disabled = isPastDate || isClosedDate;

                    const inputId = 'cal_' + dateStr.replace(/-/g, '');
                    const checked = (dateStr === selectedDateStr) ? ' checked' : '';
                    const tdClass = (dow === 0) ? 'sunday' : (dow === 6 ? 'saturday' : '');

                    html += `<td class="${tdClass}">
                    <input type="radio" name="calendar_date" id="${inputId}" value="${dateStr}" ${checked} ${disabled ? 'disabled' : ''}>
                    <label for="${inputId}">${day}</label>
                </td>`;

                    day++;
                }
                html += '</tr>';
                if (day > daysInMonth) break;
            }

            calendarBody.innerHTML = html;

            // 날짜 선택 이벤트
            calendarBody.querySelectorAll('input[name="calendar_date"]').forEach(input => {
                input.addEventListener('change', function() {
                    if (this.checked) {
                        selectedDateStr = this.value;
                        renderTimeSlots(selectedDateStr);
                    }
                });
            });

            renderTimeSlots(selectedDateStr);
        }

        // ==================== 예약 건수 조회 ====================
        async function getReservedCount(date, time) {
            return new Promise((resolve) => {
                $.ajax({
                    url: '<?= RSRV_ACTIONS ?>/update.php',
                    type: 'POST',
                    data: { act: 'check_reserved_count', date, time, sh_idx: <?= $sh_idx ?> },
                    dataType: 'json',
                    timeout: 5000,
                    success: (res) => resolve(res.success ? (res.data?.count || 0) : 0),
                    error: () => resolve(0)
                });
            });
        }

        // ==================== 수량 ====================
        function updateQuantity(val) {
            let v = Math.max(<?= $minPerson ?>, Math.min(<?= $maxPerson ?>, val));
            qtyInput.value = v;
        }

        // ==================== 폼 데이터 수집 ====================
        function collectPayload() {
            const dateInput = document.querySelector('input[name="calendar_date"]:checked');
            const timeInput = document.querySelector('input[name="reservation_time"]:checked');

            const date   = dateInput ? dateInput.value : '';
            const time   = timeInput ? timeInput.value : selectedTime;
            const people = parseInt(qtyInput.value, 10) || 1;
            const name   = nameInput.value.trim();
            const phone  = phoneInput.value.trim();

            if (!date) return alertModal('예약 날짜를 선택해주세요.');
            if (!time) return alertModal('예약 시간을 선택해주세요.');
            if (people < <?= $minPerson ?> || people > <?= $maxPerson ?>) return alertModal('인원을 확인해주세요.');
            if (!name) return alertModal('예약자명을 입력해주세요.');
            if (!phone || phone.replace(/\D/g, '').length < 9) return alertModal('휴대폰번호를 올바르게 입력해주세요.');

            return { date, time, people, reserver_name: name, reserver_phone: phone };
        }

        function alertModal(msg) {
            ModalUtil.alert({ title: '알림', message: msg, okText: '확인' });
            return false;
        }

        // ==================== 세션 저장 (Promise로 순서 보장) ====================
        function saveSession(payload) {
            return new Promise((resolve, reject) => {
                const formData = new FormData();
                formData.append('act', 'save_session');
                formData.append('date', payload.date);
                formData.append('time', payload.time);
                formData.append('people', payload.people);
                formData.append('reserver_name', payload.reserver_name);
                formData.append('reserver_phone', payload.reserver_phone);

                $.ajax({
                    url: '<?= RSRV_ACTIONS ?>/update.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    timeout: 8000,
                    cache: false,
                    success: (res) => res.success ? resolve(res) : reject(res.message || '저장 실패'),
                    error: () => reject('서버 통신 오류')
                });
            });
        }

        window.submitReservation = async function() {
            const payload = collectPayload();
            if (!payload) return;

            console.log('payload',payload);

            try {
                await saveSession(payload);

                const formData = new FormData();
                formData.append('act', 'create_reservation');
                formData.append('rv_date', payload.date);
                formData.append('rv_time', payload.time);
                formData.append('rv_people', payload.people);
                formData.append('rv_name', payload.reserver_name);
                formData.append('rv_hp', payload.reserver_phone);
                formData.append('sh_idx', <?= $sh_idx ?>);

                const res = await $.ajax({
                    url: '<?= RSRV_ACTIONS ?>/update.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json'
                });

                if (res.success) {
                    ModalUtil.alert({
                        title: '알림',
                        message: '예약이 성공적으로 접수되었습니다!',
                        okText: '확인',
                        onOk: () => location.href = './rsrv_cmp.php?rv_idx=' + (res.data?.rv_idx || '')
                    });
                } else {
                    alertModal('예약 실패: ' + (res.message || '다시 시도해주세요.'));
                }
            } catch (err) {
                alertModal(typeof err === 'string' ? err : '예약 처리 중 오류가 발생했습니다.');
            }
        };

        // submitNext도 동일하게 async/await 적용 추천
        window.submitNext = async function() {
            const payload = collectPayload();
            if (!payload) return;

            try {
                await saveSession(payload);
                location.href = '../order/order.php'
            } catch (err) {
                alertModal(typeof err === 'string' ? err : '오류가 발생했습니다.');
            }
        };

        // ==================== 초기화 ====================
        document.addEventListener('DOMContentLoaded', function() {
            renderCalendar();
            updateQuantity(<?= $minPerson ?>);

            qtyDecBtn.addEventListener('click', () => updateQuantity(parseInt(qtyInput.value) - 1));
            qtyIncBtn.addEventListener('click', () => updateQuantity(parseInt(qtyInput.value) + 1));

            prevMonthBtn.addEventListener('click', e => { e.preventDefault(); currentMonth--; if (currentMonth < 1) { currentMonth = 12; currentYear--; } renderCalendar(); });
            nextMonthBtn.addEventListener('click', e => { e.preventDefault(); currentMonth++; if (currentMonth > 12) { currentMonth = 1; currentYear++; } renderCalendar(); });
        });
    })();
</script>
