/**
 * FormState (공통)
 * - 입력폼 블록(.form_wr) 기준으로 ip_invalid / ip_valid 토글
 * - 문구는 같은 블록의 .form-text 첫번째 요소에 출력
 *
 * ✅ 퍼블 수정 없이 사용
 * ✅ let/const만 사용
 * ✅ console.log 포함
 * ✅ jQuery 의존
 */
(function (global) {
    'use strict';

    const FormState = (() => {
        const getFormWr = ($el) => $el.closest('.form_wr');

        const getMsgEl = ($el) => {
            // 퍼블의 문구 영역 재활용 (ip_invalid class 여부 무관하게 .form-text 사용)
            const $wr = getFormWr($el);
            return $wr.find('.form-text').first();
        };

        const setState = ($el, state, msg) => {
            const $wr = getFormWr($el);
            const $msg = getMsgEl($el);

            // console.log('[FormState] setState:', $el.attr('id'), state, msg);

            // wrapper 상태 토글
            $wr.removeClass('ip_invalid ip_valid');
            if (state === 'invalid') $wr.addClass('ip_invalid');
            if (state === 'valid') $wr.addClass('ip_valid');

            // message 상태 토글 + 문구 출력
            if ($msg.length) {
                $msg.removeClass('ip_invalid ip_valid');

                if (state === 'invalid') $msg.addClass('ip_invalid');
                if (state === 'valid') $msg.addClass('ip_valid');

                if (typeof msg === 'string') $msg.text(msg);
            }
        };

        const clearState = ($el, msg) => {
            setState($el, 'clear', msg);
        };

        const setInvalid = ($el, msg) => {
            setState($el, 'invalid', msg);
        };

        const setValid = ($el, msg) => {
            setState($el, 'valid', msg);
        };

        // 숫자만 입력 유틸도 같이 공통으로 쓰일 가능성이 높아서 같이 제공
        const bindOnlyNumber = (selector) => {
            $(document).on('input', selector, function () {
                const before = this.value;
                const after = (before || '').replace(/[^0-9]/g, '');
                if (before !== after) {
                    // console.log('[FormState] onlyNumber filtered:', before, '->', after);
                    this.value = after;
                }
            });
        };

        return {
            setState,
            clearState,
            setInvalid,
            setValid,
            bindOnlyNumber,
        };
    })();

    global.FormState = FormState;
})(window);
