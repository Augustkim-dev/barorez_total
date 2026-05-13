<?php
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
?>
<!doctype html>
<html lang="ko">
<head>
    <meta charset="utf-8" />
    <title>Apple Callback</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>

<div id="appleCbStatus" style="padding:16px; font-size:14px;">
    애플 로그인 처리 중입니다...
</div>

<script>
    (function () {
        console.log('[APPLE_CB] callback loaded');

        const $status = $('#appleCbStatus');
        function setStatus(text) {
            console.log('[APPLE_CB] status:', text);
            $status.text(text);
        }

        // POST로 받은 데이터 (response_mode=form_post)
        const code       = '<?= addslashes($_POST['code'] ?? '') ?>';
        const state      = '<?= addslashes($_POST['state'] ?? '') ?>';
        const id_token   = '<?= addslashes($_POST['id_token'] ?? '') ?>';
        const user_json  = '<?= addslashes($_POST['user'] ?? '') ?>';

        console.log('[APPLE_CB] received:', { code: !!code, state, id_token: !!id_token, user_json: !!user_json });

        // 기본 검증
        if (!code || !state || !id_token) {
            const msg = '애플 인증 정보가 누락되었습니다.';
            setStatus(msg);
            notifyAndClose(false, msg);
            return;
        }

        // state 검증 (localStorage만 사용 - 구글/네이버와 동일)
        const savedState = localStorage.getItem('APPLE_OAUTH_STATE') || '';
        const autoLogin  = localStorage.getItem('APPLE_AUTO_LOGIN') || 'N';

        console.log('[APPLE_CB] savedState:', savedState);

        if (!savedState || savedState !== state) {
            const msg = 'state 값이 일치하지 않습니다. 다시 시도해 주세요.';
            setStatus(msg);
            notifyAndClose(false, msg);
            return;
        }

        localStorage.removeItem('APPLE_OAUTH_STATE');
        localStorage.removeItem('APPLE_AUTO_LOGIN');

        setStatus('서버로 로그인 정보를 전달하는 중입니다...');

        $.ajax({
            url: '<?=AUTH_ACTIONS?>/apple_login.php',
            type: 'POST',
            dataType: 'json',
            timeout: 15000,
            data: {
                act: 'apple_login',
                code: code,
                state: state,
                id_token: id_token,
                user: user_json,
                auto_login: autoLogin,
                redirect_uri: '<?=CALLBACK_PAGE?>/apple_callback.php'
            },
            beforeSend: function () {
                console.log('[APPLE_CB] server request start');
            },
            success: function (res) {
                console.log('[APPLE_CB] server response:', res);

                const ok = res && res.success === true;

                if (ok) {
                    const msg = res.message || '애플 로그인 완료';
                    const redirect = res.redirect || '<?=APP_PAGE?>';
                    setStatus(msg);
                    notifyAndClose(true, msg, redirect);
                } else {
                    const msg = res.message || '애플 로그인 처리에 실패했습니다.';
                    setStatus(msg);
                    notifyAndClose(false, msg);
                }
            },
            error: function (xhr, status, err) {
                console.log('[APPLE_CB] ajax error:', { status, err });
                let msg = '서버 통신 중 오류가 발생했습니다.';
                if (status === 'timeout') msg += ' (timeout)';
                setStatus(msg);
                notifyAndClose(false, msg);
            }
        });

        function notifyAndClose(success, message, redirect) {
            const payload = {
                type: 'APPLE_LOGIN_RESULT',
                success: !!success,
                message: message || '',
                redirect: redirect || ''
            };

            console.log('[APPLE_CB] notify:', payload);

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
                console.log('[APPLE_CB] notify error:', e);
            }
        }
    })();
</script>
</body>
</html>
