<?php
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
?>
<!doctype html>
<html lang="ko">
<head>
    <meta charset="utf-8" />
    <title>Google Callback</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>

<div id="googleCbStatus" style="padding:16px; font-size:14px;">
    구글 로그인 처리 중입니다...
</div>

<script>
    (function () {
        console.log('[GOOGLE_CB] callback loaded');

        const $status = $('#googleCbStatus');
        function setStatus(text) {
            console.log('[GOOGLE_CB] status:', text);
            $status.text(text);
        }

        const params = new URLSearchParams(location.search);
        const code  = params.get('code') || '';
        const state = params.get('state') || '';
        const error = params.get('error') || '';
        const errorDesc = params.get('error_description') || '';

        console.log('[GOOGLE_CB] query:', { code, state, error, errorDesc });

        // 구글 인증 오류
        if (error) {
            const msg = '구글 로그인 오류: ' + error + (errorDesc ? (' (' + errorDesc + ')') : '');
            setStatus(msg);
            notifyAndClose(false, msg);
            return;
        }

        // code/state 없으면 실패
        if (!code || !state) {
            const msg = '구글 인증 정보(code/state)가 누락되었습니다.';
            setStatus(msg);
            notifyAndClose(false, msg);
            return;
        }

        // state 검증(네이버 구조 유지)
        const savedState = localStorage.getItem('GOOGLE_OAUTH_STATE') || '';
        const autoLogin  = localStorage.getItem('GOOGLE_AUTO_LOGIN') || 'N';

        console.log('[GOOGLE_CB] savedState:', savedState);
        console.log('[GOOGLE_CB] autoLogin:', autoLogin);

        if (!savedState || savedState !== state) {
            const msg = 'state 값이 일치하지 않습니다. 다시 시도해 주세요.';
            setStatus(msg);
            notifyAndClose(false, msg);
            return;
        }

        localStorage.removeItem('GOOGLE_OAUTH_STATE');

        setStatus('서버로 로그인 정보를 전달하는 중입니다...');

        $.ajax({
            url: '<?=AUTH_ACTIONS?>/google_login.php',
            type: 'POST',
            dataType: 'json',
            timeout: 15000,
            data: {
                act: 'google_login',
                login_mode: 'web',
                code: code,
                state: state,
                auto_login: autoLogin,
                redirect_uri: '<?=CALLBACK_PAGE?>/google_callback.php',
            },
            beforeSend: function () {
                console.log('[GOOGLE_CB] server request start');
            },
            success: function (res) {
                console.log('[GOOGLE_CB] server response:', res);

                const ok = res && res.success === true;

                if (ok) {
                    const msg = (res && res.message) ? res.message : '구글 로그인 완료';
                    const redirect = (res && res.redirect) ? res.redirect : '<?=APP_PAGE?>';
                    setStatus(msg);
                    notifyAndClose(true, msg, redirect);
                } else {
                    const msg = (res && res.message) ? res.message : '구글 로그인 처리에 실패했습니다.';
                    setStatus(msg);
                    notifyAndClose(false, msg);
                }
            },
            error: function (xhr, status, err) {
                console.log('[GOOGLE_CB] ajax error:', { status, err });
                console.log('[GOOGLE_CB] responseText:', xhr && xhr.responseText);

                let msg = '서버 통신 중 오류가 발생했습니다. 잠시 후 다시 시도해 주세요.';
                if (status === 'timeout') msg = '서버 응답이 지연되고 있습니다. (timeout) 잠시 후 다시 시도해 주세요.';

                setStatus(msg);
                notifyAndClose(false, msg);
            }
        });

        function notifyAndClose(success, message, redirect) {
            const payload = {
                type: 'GOOGLE_LOGIN_RESULT',
                success: !!success,
                message: message || '',
                redirect: redirect || ''
            };

            console.log('[GOOGLE_CB] notify:', payload);

            try {
                if (window.opener && !window.opener.closed) {
                    window.opener.postMessage(payload, window.location.origin);
                    window.close();
                    return;
                }

                if (payload.success && payload.redirect) {
                    location.href = payload.redirect;
                }
            } catch (e) {
                console.log('[GOOGLE_CB] notify error:', e);
            }
        }
    })();
</script>
</body>
</html>
