<?php
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
?>
<!doctype html>
<html lang="ko">
<head>
    <meta charset="utf-8" />
    <title>Naver Callback</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>

<!-- ✅ 흰 화면 방지: 진행 상태 표시(퍼블리싱 영향 최소) -->
<div id="naverCbStatus" style="padding:16px; font-size:14px;">
    네이버 로그인 처리 중입니다...
</div>

<script>
    (function () {
        console.log('[NAVER_CB] callback loaded');

        const $status = $('#naverCbStatus');
        function setStatus(text) {
            console.log('[NAVER_CB] status:', text);
            $status.text(text);
        }

        // 1) query 파싱
        const params = new URLSearchParams(location.search);
        const code  = params.get('code') || '';
        const state = params.get('state') || '';
        const error = params.get('error') || '';
        const errorDesc = params.get('error_description') || '';

        console.log('[NAVER_CB] query:', { code, state, error, errorDesc });

        // 2) 네이버 인증 오류 처리
        if (error) {
            const msg = '네이버 로그인 오류: ' + error + (errorDesc ? (' (' + errorDesc + ')') : '');
            setStatus(msg);
            notifyAndClose(false, msg);
            return;
        }

        // code/state 없으면 실패
        if (!code || !state) {
            const msg = '네이버 인증 정보(code/state)가 누락되었습니다.';
            setStatus(msg);
            notifyAndClose(false, msg);
            return;
        }

        // 3) state 검증 (현 구조 유지)
        const savedState = localStorage.getItem('NAVER_OAUTH_STATE') || '';
        const autoLogin  = localStorage.getItem('NAVER_AUTO_LOGIN') || 'N';

        console.log('[NAVER_CB] savedState:', savedState);
        console.log('[NAVER_CB] autoLogin:', autoLogin);

        if (!savedState || savedState !== state) {
            // ✅ 여기서도 흰화면이 아니라 메시지가 보이게
            const msg = 'state 값이 일치하지 않습니다. 다시 시도해 주세요.';
            setStatus(msg);
            notifyAndClose(false, msg);
            return;
        }

        // 1회 사용 후 삭제
        localStorage.removeItem('NAVER_OAUTH_STATE');

        // 4) 서버로 전달
        setStatus('서버로 로그인 정보를 전달하는 중입니다...');

        $.ajax({
            url: '<?=AUTH_ACTIONS?>/naver_login.php',
            type: 'POST',
            dataType: 'json',
            timeout: 15000, // ✅ 15초 제한 (무한대기 방지)
            data: {
                act: 'naver_login',
                login_mode: 'web',
                code: code,
                state: state,
                auto_login: autoLogin,
                // ✅ 서버에서 token 요청시 필요할 수 있는 redirect_uri
                redirect_uri: window.location.origin + '/callback/naver_callback.php'
            },
            beforeSend: function () {
                console.log('[NAVER_CB] server request start');
            },
            success: function (res) {
                console.log('[NAVER_CB] server response:', res);

                // optional chaining 대신 안전 처리(환경 이슈 방지)
                const ok = res && res.success === true;

                if (ok) {
                    const msg = (res && res.message) ? res.message : '네이버 로그인 완료';
                    const redirect = (res && res.redirect) ? res.redirect : '<?=APP_PAGE?>';
                    setStatus(msg);
                    notifyAndClose(true, msg, redirect);
                } else {
                    const msg = (res && res.message) ? res.message : '네이버 로그인 처리에 실패했습니다.';
                    setStatus(msg);
                    notifyAndClose(false, msg);
                }
            },
            error: function (xhr, status, err) {
                // ✅ 여기가 가장 중요: 실제 서버가 뭐라고 응답했는지 출력
                console.log('[NAVER_CB] ajax error:', { status, err });
                console.log('[NAVER_CB] responseText:', xhr && xhr.responseText);

                let msg = '서버 통신 중 오류가 발생했습니다. 잠시 후 다시 시도해 주세요1.';
                if (status === 'timeout') msg = '서버 응답이 지연되고 있습니다. (timeout) 잠시 후 다시 시도해 주세요.';

                setStatus(msg);
                notifyAndClose(false, msg);
            }
        });

        // 5) 결과 처리
        function notifyAndClose(success, message, redirect) {
            const payload = {
                type: 'NAVER_LOGIN_RESULT',
                success: !!success,
                message: message || '',
                redirect: redirect || ''
            };

            console.log('[NAVER_CB] notify:', payload);

            try {
                // 팝업이면 부모로 전달 후 닫기
                if (window.opener && !window.opener.closed) {
                    window.opener.postMessage(payload, window.location.origin);
                    window.close();
                    return;
                }

                // 팝업이 아니면 여기서 처리
                if (payload.success && payload.redirect) {
                    location.href = payload.redirect;
                } else {
                    // 이미 status 영역에 보여주고 있으니 그대로 둠
                }
            } catch (e) {
                console.log('[NAVER_CB] notify error:', e);
            }
        }
    })();
</script>
</body>
</html>
