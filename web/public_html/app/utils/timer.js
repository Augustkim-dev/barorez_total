/**
 * 인증 타이머 유틸리티
 * 사용법:
 *   const timer = AuthTimerFactory($('#timerElement'));
 *   timer.start(300);                    // 5분 시작
 *   timer.start(180, () => alert('만료!')); // 만료 시 콜백 지정
 *   timer.complete();                    // 인증 성공 시 종료
 *   timer.stop();                        // 수동 정지
 */
function AuthTimerFactory($timerEl, defaultExpireCallback = null) {
    let timerId = null;
    let remainSec = 0;
    let onExpireCallback = defaultExpireCallback;

    // UI 업데이트 (mm:ss 형식)
    function updateUI() {
        if (remainSec <= 0) {
            $timerEl.text('00:00');
            return;
        }
        const min = String(Math.floor(remainSec / 60)).padStart(2, '0');
        const sec = String(remainSec % 60).padStart(2, '0');
        $timerEl.text(`${min}:${sec}`);
    }

    // 타이머 시작
    function start(seconds = 300, expireCallback = null) {
        stop(); // 기존 타이머 정리

        remainSec = seconds;
        if (expireCallback) {
            onExpireCallback = expireCallback;
        }

        $timerEl.show().removeClass('text-success').addClass('text-danger');
        updateUI();

        timerId = setInterval(() => {
            remainSec--;
            if (remainSec <= 0) {
                stop();
                $timerEl.text('00:00');

                if (typeof onExpireCallback === 'function') {
                    onExpireCallback();
                }
            } else {
                updateUI();
            }
        }, 1000);
    }

    // 타이머 강제 정지
    function stop() {
        if (timerId) {
            clearInterval(timerId);
            timerId = null;
        }
        remainSec = 0;
    }

    // 인증 성공 시 호출 (타이머 종료 + UI 변경)
    function complete() {
        stop();
        $timerEl.text('인증완료')
            .removeClass('text-danger')
            .addClass('text-success');
    }

    // 외부에 노출할 메서드
    return {
        start,
        stop,
        complete
    };
}

// 전역으로 사용 가능하게 등록
window.AuthTimerFactory = AuthTimerFactory;
