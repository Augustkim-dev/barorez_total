<section class="left_menu">
    <ul class="left_menu_nav">
        <li class="<?=$hd_left === 'index' ? 'on' : ''?>" data-badge="table">
            <a href="<?=MARKET_HTTP?>" class="">
                <p class="img_off"><img src="<?=DESIGN_HTTP?>/market/img/navi_qr_off.png" alt="테이블 아이콘"><span class="d-block mt-2">테이블</span></p>
                <p class="img_on"><img src="<?=DESIGN_HTTP?>/market/img/navi_qr_on.png" alt="테이블 아이콘"><span class="d-block mt-2">테이블</span></p>
            </a>
        </li>
        <li class="<?=$hd_left === 'pck_dtl' ? 'on' : ''?>" data-badge="pack">
            <a href="<?=MARKET_HTTP?>/pick-up" class="">
                <p class="img_off"><img src="<?=DESIGN_HTTP?>/market/img/navi_pack_off.png" alt="포장 아이콘"><span class="d-block mt-2">포장</span></p>
                <p class="img_on"><img src="<?=DESIGN_HTTP?>/market/img/navi_pack_on.png" alt="포장 아이콘"><span class="d-block mt-2">포장</span></p>
            </a>
        </li>
        <li class="<?=$hd_left === 'reserve_hst' ? 'on' : ''?>" data-badge="reserve">
            <a href="<?=MARKET_HTTP?>/reserve" class="">
                <p class="img_off"><img src="<?=DESIGN_HTTP?>/market/img/navi_cal_off.png" alt="예약 아이콘"><span class="d-block mt-2">예약</span></p>
                <p class="img_on"><img src="<?=DESIGN_HTTP?>/market/img/navi_cal_on.png" alt="예약 아이콘"><span class="d-block mt-2">예약</span></p>
            </a>
        </li>
        <li class="<?=$hd_left === 'cmp_list' ? 'on' : ''?>">
            <a href="<?=MARKET_HTTP?>/com-list" class="">
                <p class="img_off"><img src="<?=DESIGN_HTTP?>/market/img/navi_cancle_off.png" alt="완료/취소 아이콘"><span class="d-block mt-2">완료/취소</span></p>
                <p class="img_on"><img src="<?=DESIGN_HTTP?>/market/img/navi_cancle_on.png" alt="완료/취소 아이콘"><span class="d-block mt-2">완료/취소</span></p>
            </a>
        </li>
    </ul>
</section>

<audio id="badgeAlertAudio" preload="auto">
    <source src="<?=MARKET_HTTP?>/sound/order-alert.mp3" type="audio/mpeg">
</audio>

<script>
    (function () {
        const currentShIdx = <?= (int)($_SESSION['current_sh_idx'] ?? 0) ?>;
        const hdLeft = "<?= addslashes($hd_left ?? '') ?>";
        const alertAudio = document.getElementById('badgeAlertAudio');

        if (!currentShIdx || !alertAudio) return;

        // 중복 실행 방지
        if (window.__badgeTickerStarted) return;
        window.__badgeTickerStarted = true;

        // ==================== 변수 선언 ====================
        const liMap = {
            TABLE: document.querySelector('li[data-badge="table"]'),
            PACK:  document.querySelector('li[data-badge="pack"]'),
            RESERVE: document.querySelector('li[data-badge="reserve"]')
        };

        const pageToBadge = {
            index: 'TABLE',
            pck_dtl: 'PACK',
            reserve_hst: 'RESERVE'
        };

        const currentBadgeType = pageToBadge[hdLeft] || '';

        let prevTableCount = null;
        let prevPackCount = null;
        let prevReserveCount = null;

        let isAudioUnlocked = false;
        let lastPlayTime = 0;
        let soundButtonEl = null;
        let pollTimer = null;
        let isPolling = false;

        const STORAGE_KEY = 'audio_unlocked_for_session';
        const playCooldown = 3000;
        const pollIntervalVisible = 1000;
        const pollIntervalHidden = 15000;
        const ajaxTimeout = 5000;

        // ==================== 유틸 함수 ====================
        function isPreviouslyUnlocked() {
            return sessionStorage.getItem(STORAGE_KEY) === 'true';
        }

        function markAsUnlocked() {
            sessionStorage.setItem(STORAGE_KEY, 'true');
        }

        function updateBadge(li, count) {
            if (!li) return;
            li.classList.toggle('navi_alim', count > 0);
        }

        // ==================== 사운드 버튼 ====================
        function createSoundButton() {
            if (soundButtonEl) return;

            soundButtonEl = document.createElement('button');
            soundButtonEl.type = 'button';
            soundButtonEl.textContent = '🛎️ 알림음 켜기';
            soundButtonEl.style.cssText = `
            position: fixed;
            right: 20px;
            bottom: 20px;
            z-index: 9999;
            padding: 14px 22px;
            border: none;
            border-radius: 50px;
            background: #e74c3c;
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            box-shadow: 0 8px 25px rgba(231, 76, 60, 0.35);
            cursor: pointer;
            white-space: nowrap;
        `;

            soundButtonEl.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                unlockAudio(true);
            });

            document.body.appendChild(soundButtonEl);
        }

        function removeSoundButton() {
            if (soundButtonEl) {
                soundButtonEl.remove();
                soundButtonEl = null;
            }
        }

        // ==================== 오디오 unlock ====================
        async function unlockAudio(fromButton = false) {
            if (isAudioUnlocked || !alertAudio) return;

            try {
                alertAudio.volume = fromButton ? 1 : 0.01;
                alertAudio.muted = false;

                const playPromise = alertAudio.play();

                if (playPromise) {
                    await playPromise;

                    // 성공 처리
                    alertAudio.pause();
                    alertAudio.currentTime = 0;
                    alertAudio.volume = 1;

                    isAudioUnlocked = true;
                    markAsUnlocked();
                    removeSoundButton();
                    removeUnlockListeners();

                    console.log('✅ 알림음이 활성화되었습니다.');
                }
            } catch (err) {
                console.log('자동 unlock 실패 → 버튼 표시');
                if (fromButton) createSoundButton();
            }
        }

        function removeUnlockListeners() {
            ['pointerdown', 'touchstart', 'keydown'].forEach(ev => {
                document.removeEventListener(ev, handleUserGesture, true);
            });
        }

        function handleUserGesture() {
            if (!isAudioUnlocked) unlockAudio(false);
        }

        function bindUnlockListeners() {
            ['pointerdown', 'touchstart', 'keydown'].forEach(ev => {
                document.addEventListener(ev, handleUserGesture, true);
            });
        }

        // ==================== 알림음 재생 ====================
        function playAlertSound() {
            if (!alertAudio) return;

            if (!isAudioUnlocked) {
                if (!soundButtonEl) createSoundButton();
                return;
            }

            const now = Date.now();
            if (now - lastPlayTime < playCooldown) return;

            try {
                alertAudio.currentTime = 0;
                alertAudio.volume = 1;
                const promise = alertAudio.play();
                if (promise) {
                    promise.then(() => { lastPlayTime = now; });
                }
            } catch (e) {
                createSoundButton();
            }
        }

        // ==================== 새 알림 체크 ====================
        function checkNewAlert(tableCnt, packCnt, reserveCnt) {
            if (prevTableCount === null && prevPackCount === null && prevReserveCount === null) {
                prevTableCount = tableCnt;
                prevPackCount = packCnt;
                prevReserveCount = reserveCnt;
                return;
            }

            const hasNew = (tableCnt > prevTableCount) ||
                (packCnt > prevPackCount) ||
                (reserveCnt > prevReserveCount);

            if (hasNew) {
                playAlertSound();
            }

            prevTableCount = tableCnt;
            prevPackCount = packCnt;
            prevReserveCount = reserveCnt;
        }

        // ==================== markSeen & Polling ====================
        function markSeen(badgeType) {
            if (!badgeType) return;

            $.ajax({
                url: '<?=MARKET_HTTP?>/badge_poll.php',
                type: 'POST',
                data: {
                    act: 'mark_seen',
                    sh_idx: currentShIdx,
                    badge_type: badgeType
                },
                dataType: 'json',
                timeout: ajaxTimeout
            });
        }

        function getPollInterval() {
            return document.hidden ? pollIntervalHidden : pollIntervalVisible;
        }

        function scheduleNextPoll(delay) {
            clearTimeout(pollTimer);
            pollTimer = setTimeout(pollBadges, typeof delay === 'number' ? delay : getPollInterval());
        }

        function pollBadges() {
            if (isPolling) {
                scheduleNextPoll();
                return;
            }

            isPolling = true;

            $.ajax({
                url: '<?=MARKET_HTTP?>/badge_poll.php',
                type: 'POST',
                data: {
                    act: 'check_badges',
                    sh_idx: currentShIdx
                },
                dataType: 'json',
                timeout: ajaxTimeout
            })
                .done(function (res) {
                    if (!res || !res.success) return;

                    const tableCnt = parseInt(res.table || 0, 10) || 0;
                    const packCnt  = parseInt(res.pack || 0, 10) || 0;
                    const reserveCnt = parseInt(res.reserve || 0, 10) || 0;

                    updateBadge(liMap.TABLE, tableCnt);
                    updateBadge(liMap.PACK, packCnt);
                    updateBadge(liMap.RESERVE, reserveCnt);

                    checkNewAlert(tableCnt, packCnt, reserveCnt);
                })
                .always(function () {
                    isPolling = false;
                    scheduleNextPoll();
                });
        }

        // ==================== 초기화 ====================
        document.addEventListener('visibilitychange', function () {
            scheduleNextPoll(document.hidden ? pollIntervalHidden : 300);
        });

        bindUnlockListeners();

        // 페이지 로드 후 자동 unlock 시도 (가장 핵심)
        setTimeout(() => {
            if (isPreviouslyUnlocked()) {
                unlockAudio(false);        // 이전에 unlock 성공한 적 있으면 자동 시도
            } else {
                createSoundButton();       // 처음이면 버튼 표시
            }
        }, 800);

        if (currentBadgeType) {
            markSeen(currentBadgeType);
        }

        pollBadges();

    })();
</script>
