<div class="modal fade" id="modal_common" tabindex="-1" aria-hidden="true" style="z-index: 9999">
    <div class="modal-dialog modal-sm modal-dialog-centered" id="modal_common_dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center w-100" id="modal_common_title">알림</h5>
            </div>

            <div class="modal-body">
                <p class="text-center wh_pre line_h1_4 text-gray" id="modal_common_msg"></p>
            </div>

            <div class="modal-footer">
                <div class="form-row w-100" id="modal_common_btns">
                    <!-- JS로 버튼 렌더 -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function (global) {
        'use strict';

        if (!global.jQuery) return;
        const $ = global.jQuery;

        const ModalUtil = (() => {
            const $modal = $('#modal_common');
            const $dialog = $('#modal_common_dialog');
            const $title = $('#modal_common_title');
            const $msg = $('#modal_common_msg');
            const $btns = $('#modal_common_btns');

            // Bootstrap4/5 호환 show/hide
            const showModal = () => {
                try {
                    // BS4
                    $modal.modal('show');
                } catch (e) {
                    // BS5 fallback
                    if (global.bootstrap && global.bootstrap.Modal) {
                        const inst = global.bootstrap.Modal.getOrCreateInstance($modal[0]);
                        inst.show();
                    }
                }
            };

            const hideModal = () => {
                try {
                    $modal.modal('hide');
                } catch (e) {
                    if (global.bootstrap && global.bootstrap.Modal) {
                        const inst = global.bootstrap.Modal.getOrCreateInstance($modal[0]);
                        inst.hide();
                    }
                }
            };

            const setSize = (size) => {
                // size: 'sm' | 'md' | 'default' | 'full' | 'bottom'
                $modal.removeClass('modal_full modal_bottom');
                $dialog.removeClass('modal-sm modal-md modal-default');

                if (size === 'full') {
                    $modal.addClass('modal_full');
                    $dialog.addClass('modal-default');
                    return;
                }
                if (size === 'bottom') {
                    $modal.addClass('modal_bottom');
                    $dialog.addClass('modal-default');
                    return;
                }

                if (size === 'md') $dialog.addClass('modal-md');
                else if (size === 'default') $dialog.addClass('modal-default');
                else $dialog.addClass('modal-sm'); // default = sm
            };

            // ✅ 버튼 렌더 + "클릭 관통" 방지 포함
            const renderButtons = (buttons) => {
                $btns.empty();

                const cols = buttons.length === 2 ? ['col-4', 'col-8'] : ['col-12'];

                buttons.forEach((b, idx) => {
                    const colClass = buttons.length === 2 ? cols[idx] : cols[0];
                    const $col = $('<div>').addClass(colClass);

                    const $btn = $('<button type="button">')
                        .addClass(`btn btn-block ${b.className || 'btn-primary'}`)
                        .text(b.text || '확인');

                    // ⚠️ data-dismiss를 쓰면 BS4가 즉시 hide 처리하면서 click-through가 더 잘 발생할 수 있음
                    // 그래서 dismiss는 우리가 직접 hideModal로 처리합니다.
                    // if (b.dismiss === true) $btn.attr('data-dismiss', 'modal');

                    $btn.on('click', function (e) {
                        // ✅ click-through 방지 (아래 모달/backdrop으로 이벤트 전달 차단)
                        e.preventDefault();
                        e.stopPropagation();
                        if (typeof e.stopImmediatePropagation === 'function') e.stopImmediatePropagation();

                        // 콜백 먼저 실행
                        if (typeof b.onClick === 'function') b.onClick();

                        // ✅ 클릭 이벤트가 완전히 끝난 다음 모달 닫기 (관통 방지에 매우 효과적)
                        if (b.dismiss !== false) {
                            setTimeout(() => {
                                hideModal();
                            }, 0);
                        }
                    });

                    $col.append($btn);
                    $btns.append($col);
                });
            };

            // ---- public APIs ----
            const alert = (opt) => {
                const {
                    title = '알림',
                    message = '',
                    okText = '확인',
                    size = 'sm',
                    onOk = null,
                    okClass = 'btn-primary',
                } = opt || {};

                setSize(size);
                $title.text(title);
                $msg.text(message);

                renderButtons([{ text: okText, className: okClass, dismiss: true, onClick: onOk }]);
                showModal();
            };

            const confirm = (opt) => {
                const {
                    title = '확인',
                    message = '',
                    okText = '확인',
                    cancelText = '취소',
                    size = 'sm',
                    onOk = null,
                    onCancel = null,
                    okClass = 'btn-primary',
                    cancelClass = 'btn-outline-light',
                } = opt || {};

                setSize(size);
                $title.text(title);
                $msg.text(message);

                renderButtons([
                    { text: cancelText, className: cancelClass, dismiss: true, onClick: onCancel },
                    { text: okText, className: okClass, dismiss: true, onClick: onOk },
                ]);

                showModal();
            };

            // 메시지/타이틀만 바꾸고 버튼은 커스텀으로 쓰고 싶을 때
            const open = (opt) => {
                const {
                    title = '알림',
                    message = '',
                    size = 'sm',
                    buttons = [{ text: '확인', className: 'btn-primary', dismiss: true }],
                } = opt || {};

                setSize(size);
                $title.text(title);
                $msg.text(message);
                renderButtons(buttons);
                showModal();
            };

            // ✅ (선택) 다른 모달이 떠있는 상태에서 common 모달이 닫힐 때 body modal-open 유지
            $modal.on('hidden.bs.modal', function () {
                // Bootstrap4/5 공통: show 클래스가 붙은 모달이 남아 있으면 modal-open 유지
                if ($('.modal.show').length > 0) {
                    $('body').addClass('modal-open');
                }
            });

            return { alert, confirm, open, hide: hideModal };
        })();

        global.ModalUtil = ModalUtil;
    })(window);
</script>
