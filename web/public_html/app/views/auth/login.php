<div class="wrap">
    <div class="sub_pg">
        <div class="container">
            <div class="login_logo text-center">
                <img src="<?=DESIGN_HTTP?>/img/logo.svg">
            </div>
            <form id="loginForm">
                <div class="" id="formId">
                    <input type="text"
                           class="form-control"
                           id="mt_id"
                           name="mt_id"
                           placeholder="아이디 입력">
                    <div class="form-text ip_invalid" id="idMsg">아이디를 다시 확인해주세요</div>
                </div>
                <div class="form_wr mt-2" id="formPw">
                    <input type="password"
                           class="form-control"
                           id="mt_pwd"
                           name="mt_pwd"
                           placeholder="비밀번호 입력">
                    <div class="form-text ip_invalid" id="pwMsg">비밀번호를 다시 확인해주세요</div>
                </div>
                <div class="mt-4 d-flex justify-content-between fs_15 align-items-center">
                    <div class="checks">
                        <label>
                            <input type="checkbox" name="auto_login" id="auto_login" value="Y">
                            <span class="ic_box"></span>
                            <div class="chk_p tg_500">
                                <p>자동 로그인</p>
                            </div>
                        </label>
                    </div>
                    <div class="fs_14">
                        <a href="<?=AUTH_PAGE?>/find_id.php" class="mr-3 tg_500 fw_500">아이디 찾기</a>
                        <a href="<?=AUTH_PAGE?>/find_pw.php" class="tg_500 fw_500">비밀번호 찾기</a>
                    </div>
                </div>
            </form>

            <!-- onclick 제거하고 id만 추가 -->
            <button type="button"
                    class="btn btn-lg btn-primary btn-block mt-5"
                    id="btnLogin">
                로그인
            </button>

            <button type="button"
                    onclick="location.href='<?=AUTH_PAGE?>/agree.php'"
                    class="btn btn-lg btn-outline-light btn-block mt-3">
                회원가입
            </button>

            <ul class="sns_login">
                <li><a href="javascript:void(0)" id="btnGoogleLogin"><img src="<?=DESIGN_HTTP?>/img/sns_google.svg" alt="구글 로그인"></a></li>
                <?php if($_SESSION['app_os'] !== 'android'):?>
                <li><a href="javascript:void(0)" id="btnAppleLogin"><img src="<?=DESIGN_HTTP?>/img/sns_apple.svg" alt="애플 로그인"></a></li>
                <?php endif;?>
                <li><a href="javascript:void(0)" id="btnNaverLogin"><img src="<?=DESIGN_HTTP?>/img/sns_naver.svg" alt="네이버 로그인"></a></li>
                <li><a href="javascript:void(0)" id="btnKakaoLogin"><img src="<?=DESIGN_HTTP?>/img/sns_kakao.svg" alt="카카오 로그인"></a></li>
            </ul>
        </div>
    </div>
</div>
<?php include_once("../inc/modal.php");?>

<script src="https://developers.kakao.com/sdk/js/kakao.js"></script>
<script src="<?=CDN_UTIL_URL?>/msg_err.js"></script>
<script>
    // =============================================
    // 1. 전역 설정 (Config)
    // =============================================
    const GOOGLE_CONFIG = {
        CLIENT_ID: '<?=GOOGLE_CLIENT_ID?>',
        REDIRECT_URI: '<?=CALLBACK_PAGE?>/google_callback.php',
        SCOPE: 'openid email profile'
    };

    const KAKAO_CONFIG = {
        APP_KEY: '<?=KAKAO_JAVASCRIPT_KEY?>',
        REDIRECT_URI: window.location.origin + '/auth/kakao_callback.php'
    };

    const NAVER_CONFIG = {
        CLIENT_ID: '<?=NAVER_CLIENT_ID?>',
        REDIRECT_URI: '<?=CALLBACK_PAGE?>/naver_callback.php'
    };

    const APPLE_CONFIG = {
        CLIENT_ID: '<?=APPLE_CLIENT_ID?>',
        REDIRECT_URI: '<?=CALLBACK_PAGE?>/apple_callback.php',
        SCOPE: 'name email',
        RESPONSE_TYPE: 'code id_token',
        RESPONSE_MODE: 'form_post'
    };

    // =============================================
    // 2. 헬퍼 함수
    // =============================================
    function generateState(len = 20) {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let s = '';
        for (let i = 0; i < len; i++) s += chars.charAt(Math.floor(Math.random() * chars.length));
        return s;
    }

    // =============================================
    // 3. 메인 초기화 (모든 이벤트 바인딩)
    // =============================================
    $(function () {
        // ------------------ 일반 로그인 관련 변수 ------------------
        const $form       = $('#loginForm');
        const $idInput    = $('#mt_id');
        const $idWrap     = $('#formId');
        const $idError    = $('#idMsg');
        const $pwInput    = $('#mt_pwd');
        const $pwWrap     = $('#formPw');
        const $pwError    = $('#pwMsg');
        const $autoLogin  = $('#auto_login');
        const $btnLogin   = $('#btnLogin');

        // 초기 상태 리셋
        resetFieldState($idWrap, $idError);
        resetFieldState($pwWrap, $pwError);

        // ------------------ 앱 여부 체크 ------------------
        const IS_APP = <?=!empty($_SESSION['app_os']) ? 'true' : 'false'?>;

        // =============================================
        // 일반 로그인 (아이디/비밀번호)
        // =============================================
        $btnLogin.on('click', function () {
            console.log('로그인 버튼 클릭');

            // 상태 초기화
            resetFieldState($idWrap, $idError);
            resetFieldState($pwWrap, $pwError);

            const mt_id  = $.trim($idInput.val());
            const mt_pwd = $.trim($pwInput.val());
            const auto   = $autoLogin.is(':checked') ? 'Y' : 'N';

            let hasError = false;

            if (!mt_id) {
                showError($idWrap, $idError, '아이디를 입력해 주세요.');
                if (!hasError) $idInput.focus();
                hasError = true;
            }
            if (!mt_pwd) {
                showError($pwWrap, $pwError, '비밀번호를 입력해 주세요.');
                if (!hasError) $pwInput.focus();
                hasError = true;
            }

            if (hasError) {
                ModalUtil.alert({
                    title: '로그인',
                    message: '아이디와 비밀번호를 확인해 주세요.',
                    okText: '확인'
                });
                return;
            }

            // AJAX 로그인
            $.ajax({
                url: '<?=AUTH_ACTIONS?>/login.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    act: 'login',
                    mt_id: mt_id,
                    mt_pwd: mt_pwd,
                    auto_login: auto
                },
                beforeSend: () => {
                    console.log('로그인 AJAX 전송 시작');
                    $btnLogin.prop('disabled', true);
                },
                success: function (res) {
                    console.log('로그인 응답:', res);

                    if (res?.success) {
                        ModalUtil.alert({
                            title: '로그인',
                            message: res.message || '로그인에 성공했습니다.',
                            okText: '확인',
                            onOk: function () {
                                location.href = res.redirect || '<?=APP_PAGE?>';
                            }
                        });
                    } else {
                        ModalUtil.alert({
                            title: '로그인',
                            message: res?.message || '아이디 또는 비밀번호를 다시 확인해 주세요.',
                            okText: '확인'
                        });

                        showError($idWrap, $idError, '아이디를 다시 확인해 주세요.');
                        showError($pwWrap, $pwError, '비밀번호를 다시 확인해 주세요.');
                        $pwInput.val('').focus();
                    }
                },
                error: function () {
                    alert('로그인 처리 중 오류가 발생했습니다. 잠시 후 다시 시도해 주세요.');
                },
                complete: () => {
                    $btnLogin.prop('disabled', false);
                }
            });
        });

        // 엔터키 로그인
        $idInput.add($pwInput).on('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                $btnLogin.trigger('click');
            }
        });

        // 폼 submit 방지
        $form.on('submit', e => e.preventDefault());

        // =============================================
        // 카카오 SDK 초기화
        // =============================================
        if (typeof Kakao !== 'undefined' && !Kakao.isInitialized()) {
            Kakao.init(KAKAO_CONFIG.APP_KEY);
            console.log('카카오 SDK 초기화 완료:', Kakao.isInitialized());
        }

        // =============================================
        // 앱 → 웹뷰로 소셜로그인 결과 전달 (React Native에서 호출)
        // =============================================
        window.onAppSocialLoginResult = function (result) {
            console.log('[APP_LOGIN] 앱 소셜로그인 결과 수신:', result);

            if (!result || result.type !== 'APP_SOCIAL_LOGIN_RESULT') return;

            if (!result.success) {
                ModalUtil.alert({
                    title: '로그인',
                    message: result.message || '소셜 로그인 처리 중 오류가 발생했습니다.',
                    okText: '확인'
                });
                return;
            }

            const userData = result.data || {};
            const provider = result.provider || '';

            switch (provider) {
                case 'kakao':
                    processKakaoLogin(userData);
                    break;
                case 'naver':
                    processNaverLogin(userData);
                    break;
                case 'google':
                    processGoogleLogin(userData);
                    break;
                case 'apple':
                    processAppleLogin(userData);
                    break;
                default:
                    ModalUtil.alert({
                        title: '로그인',
                        message: '지원하지 않는 로그인 방식입니다.',
                        okText: '확인'
                    });
            }
        };

        // CustomEvent 백업 (필요 시)
        window.addEventListener('APP_SOCIAL_LOGIN_RESULT', function (e) {
            if (typeof window.onAppSocialLoginResult === 'function') {
                window.onAppSocialLoginResult(e.detail);
            }
        });

        // =============================================
        // 소셜 로그인 버튼 이벤트 (앱/웹 분기)
        // =============================================
        function postSocialLoginToApp(provider) {
            try {
                const payload = {
                    type: 'SOCIAL_LOGIN',
                    provider: provider,
                    auto_login: $('#auto_login').is(':checked') ? 'Y' : 'N'
                };

                if (window.ReactNativeWebView && window.ReactNativeWebView.postMessage) {
                    window.ReactNativeWebView.postMessage(JSON.stringify(payload));
                    return true;
                }
                console.warn('[LOGIN] ReactNativeWebView 객체 없음');
                return false;
            } catch (e) {
                console.error('[LOGIN] postMessage 전송 실패:', e);
                return false;
            }
        }

        // 네이버
        $('#btnNaverLogin').on('click', function (e) {
            e.preventDefault();
            if (IS_APP) {
                postSocialLoginToApp('naver');
                return;
            }
            naverLogin();
        });

        // 카카오
        $('#btnKakaoLogin').on('click', function (e) {
            e.preventDefault();
            if (IS_APP) {
                postSocialLoginToApp('kakao');
                return;
            }
            kakaoLogin();
        });

        // 애플
        $('#btnAppleLogin').on('click', function (e) {
            e.preventDefault();
            if (IS_APP) {
                postSocialLoginToApp('apple');
                return;
            }
            appleLogin();
        });

        // 구글
        $('#btnGoogleLogin').on('click', function (e) {
            e.preventDefault();
            if (IS_APP) {
                postSocialLoginToApp('google');
                return;
            }
            googleLogin();
        });
    });

    // =============================================
    // 4. 소셜 로그인 처리 함수들 (카카오 스타일 통일)
    // =============================================

    /**
     * 카카오 로그인 (웹 전용 SDK)
     */
    function kakaoLogin() {
        if (typeof Kakao === 'undefined') {
            ModalUtil.alert({ title: '로그인', message: '카카오 로그인 서비스를 불러오는 중입니다. 잠시 후 다시 시도해 주세요.', okText: '확인' });
            return;
        }

        Kakao.Auth.login({
            success: function (authObj) {
                Kakao.API.request({
                    url: '/v2/user/me',
                    success: function (response) {
                        const userData = {
                            kakao_id: response.id,
                            email: response.kakao_account?.email || '',
                            nickname: response.properties?.nickname || '',
                            profile_image: response.properties?.profile_image || '',
                            access_token: authObj.access_token
                        };
                        processKakaoLogin(userData);
                    },
                    fail: function (error) {
                        console.error('카카오 사용자 정보 요청 실패:', error);
                        ModalUtil.alert({ title: '로그인', message: '사용자 정보를 가져오는데 실패했습니다.', okText: '확인' });
                    }
                });
            },
            fail: function (err) {
                if (err.error !== 'access_denied') {
                    ModalUtil.alert({ title: '로그인', message: '카카오 로그인에 실패했습니다. 다시 시도해 주세요.', okText: '확인' });
                }
            }
        });
    }

    /**
     * 서버 처리 공통 함수 (카카오)
     */
    function processKakaoLogin(userData) {
        $.ajax({
            url: '<?=AUTH_ACTIONS?>/kakao_login.php',
            type: 'POST',
            dataType: 'json',
            data: {
                act: 'kakao_login',
                kakao_id: userData.kakao_id,
                email: userData.email,
                nickname: userData.nickname,
                profile_image: userData.profile_image,
                access_token: userData.access_token
            },
            beforeSend: () => console.log('카카오 로그인 처리 중...'),
            success: function (res) {
                if (res?.success) {
                    ModalUtil.alert({
                        title: '로그인',
                        message: res.message || '카카오 로그인에 성공했습니다.',
                        okText: '확인',
                        onOk: () => location.href = res.redirect || '<?=APP_PAGE?>'
                    });
                } else {
                    const msg = res?.message || '카카오 로그인 처리 중 오류가 발생했습니다.';
                    ModalUtil.alert({
                        title: '로그인',
                        message: msg,
                        okText: '확인',
                        onOk: () => {
                            if (res?.need_register) {
                                location.href = '<?=AUTH_PAGE?>/kakao_register.php?kakao_id=' + userData.kakao_id;
                            }
                        }
                    });
                }
            },
            error: () => alert('카카오 로그인 처리 중 오류가 발생했습니다.')
        });
    }

    /**
     * 네이버 로그인 (웹: 팝업 + 콜백, 앱: 네이티브)
     */
    function naverLogin() {
        if (!NAVER_CONFIG.CLIENT_ID) {
            ModalUtil.alert({ title: '로그인', message: '네이버 Client ID가 설정되지 않았습니다.', okText: '확인' });
            return;
        }

        const state = generateState(24);
        const auto = $('#auto_login').is(':checked') ? 'Y' : 'N';

        localStorage.setItem('NAVER_OAUTH_STATE', state);
        localStorage.setItem('NAVER_AUTO_LOGIN', auto);

        const authorizeUrl = 'https://nid.naver.com/oauth2.0/authorize' +
            '?response_type=code' +
            '&client_id=' + encodeURIComponent(NAVER_CONFIG.CLIENT_ID) +
            '&redirect_uri=' + encodeURIComponent(NAVER_CONFIG.REDIRECT_URI) +
            '&state=' + encodeURIComponent(state);

        const popup = openPopup(authorizeUrl, 'naverLoginPopup');

        if (!popup) return;

        // 결과 수신 (한 번만 처리)
        const onMsg = function (event) {
            if (event.origin !== window.location.origin) return;
            const data = event.data || {};
            if (data.type !== 'NAVER_LOGIN_RESULT') return;

            window.removeEventListener('message', onMsg);

            if (data.success) {
                ModalUtil.alert({
                    title: '로그인',
                    message: data.message || '네이버 로그인 성공',
                    okText: '확인',
                    onOk: () => location.href = data.redirect || '<?=APP_PAGE?>'
                });
            } else {
                ModalUtil.alert({ title: '로그인', message: data.message || '네이버 로그인 실패', okText: '확인' });
            }
        };
        window.addEventListener('message', onMsg);
    }

    /**
     * 구글 로그인 (웹: 팝업 + 콜백, 앱: 네이티브)
     */
    function googleLogin() {
        if (!GOOGLE_CONFIG.CLIENT_ID) {
            ModalUtil.alert({ title: '로그인', message: '구글 Client ID가 설정되지 않았습니다.', okText: '확인' });
            return;
        }

        const state = generateState(24);
        const auto = $('#auto_login').is(':checked') ? 'Y' : 'N';

        localStorage.setItem('GOOGLE_OAUTH_STATE', state);
        localStorage.setItem('GOOGLE_AUTO_LOGIN', auto);

        const authorizeUrl = 'https://accounts.google.com/o/oauth2/v2/auth' +
            '?response_type=code' +
            '&client_id=' + encodeURIComponent(GOOGLE_CONFIG.CLIENT_ID) +
            '&redirect_uri=' + encodeURIComponent(GOOGLE_CONFIG.REDIRECT_URI) +
            '&scope=' + encodeURIComponent(GOOGLE_CONFIG.SCOPE) +
            '&state=' + encodeURIComponent(state) +
            '&prompt=select_account';

        const popup = openPopup(authorizeUrl, 'googleLoginPopup');

        if (!popup) return;

        const onMsg = function (event) {
            if (event.origin !== window.location.origin) return;
            const data = event.data || {};
            if (data.type !== 'GOOGLE_LOGIN_RESULT') return;

            window.removeEventListener('message', onMsg);

            if (data.success) {
                ModalUtil.alert({
                    title: '로그인',
                    message: data.message || '구글 로그인 성공',
                    okText: '확인',
                    onOk: () => location.href = data.redirect || '<?=APP_PAGE?>'
                });
            } else {
                ModalUtil.alert({ title: '로그인', message: data.message || '구글 로그인 실패', okText: '확인' });
            }
        };
        window.addEventListener('message', onMsg);
    }

    /**
     * 애플 로그인 (웹: 팝업 + 콜백, 앱: 네이티브)
     */
    function appleLogin() {
        if (!APPLE_CONFIG.CLIENT_ID) {
            ModalUtil.alert({ title: '로그인', message: '애플 로그인이 설정되지 않았습니다.', okText: '확인' });
            return;
        }

        const state = generateState(24);
        const auto = $('#auto_login').is(':checked') ? 'Y' : 'N';

        localStorage.setItem('APPLE_OAUTH_STATE', state);
        localStorage.setItem('APPLE_AUTO_LOGIN', auto);

        const authorizeUrl = 'https://appleid.apple.com/auth/authorize' +
            '?client_id=' + encodeURIComponent(APPLE_CONFIG.CLIENT_ID) +
            '&redirect_uri=' + encodeURIComponent(APPLE_CONFIG.REDIRECT_URI) +
            '&response_type=' + encodeURIComponent(APPLE_CONFIG.RESPONSE_TYPE) +
            '&response_mode=' + encodeURIComponent(APPLE_CONFIG.RESPONSE_MODE) +
            '&scope=' + encodeURIComponent(APPLE_CONFIG.SCOPE) +
            '&state=' + encodeURIComponent(state);

        const popup = openPopup(authorizeUrl, 'appleLoginPopup');

        if (!popup) return;

        const onMsg = function (event) {
            if (event.origin !== window.location.origin) return;
            const data = event.data || {};
            if (data.type !== 'APPLE_LOGIN_RESULT') return;

            window.removeEventListener('message', onMsg);

            if (data.success) {
                ModalUtil.alert({
                    title: '로그인',
                    message: data.message || '애플 로그인 성공',
                    okText: '확인',
                    onOk: () => location.href = data.redirect || '<?=APP_PAGE?>'
                });
            } else {
                ModalUtil.alert({ title: '로그인', message: data.message || '애플 로그인 실패', okText: '확인' });
            }
        };
        window.addEventListener('message', onMsg);
    }

    /**
     * 팝업 공통 오픈 함수 (중복 제거)
     */
    function openPopup(url, name) {
        const w = 520, h = 720;
        const left = (screen.width / 2) - (w / 2);
        const top = (screen.height / 2) - (h / 2);

        const popup = window.open(
            url,
            name,
            `width=${w},height=${h},left=${left},top=${top},resizable=yes,scrollbars=yes`
        );

        if (!popup) {
            ModalUtil.alert({
                title: '로그인',
                message: '팝업이 차단되어 로그인을 진행할 수 없습니다. 팝업 허용 후 다시 시도해 주세요.',
                okText: '확인'
            });
        }
        return popup;
    }

    // =============================================
    // 5. 앱 네이티브용 서버 처리 함수 (카카오와 완전 동일 구조)
    // =============================================

    function processNaverLogin(userData) {
        $.ajax({
            url: '<?=AUTH_ACTIONS?>/naver_login.php',
            type: 'POST',
            dataType: 'json',
            data: {
                act: 'naver_login',
                login_mode: 'app',
                naver_id: userData.naver_id,
                email: userData.email,
                nickname: userData.nickname,
                profile_image: userData.profile_image,
                access_token: userData.access_token
            },
            beforeSend: () => console.log('네이버 로그인 처리 중...'),
            success: function (res) {
                if (res?.success) {
                    ModalUtil.alert({
                        title: '로그인',
                        message: res.message || '네이버 로그인에 성공했습니다.',
                        okText: '확인',
                        onOk: () => location.href = res.redirect || '<?=APP_PAGE?>'
                    });
                } else {
                    const msg = res?.message || '네이버 로그인 처리 중 오류가 발생했습니다.';
                    ModalUtil.alert({
                        title: '로그인',
                        message: msg,
                        okText: '확인',
                        onOk: () => {
                            if (res?.need_register) {
                                location.href = '<?=AUTH_PAGE?>/naver_register.php?naver_id=' + encodeURIComponent(userData.naver_id);
                            }
                        }
                    });
                }
            },
            error: () => alert('네이버 로그인 처리 중 오류가 발생했습니다.')
        });
    }

    function processGoogleLogin(userData) {
        $.ajax({
            url: '<?=AUTH_ACTIONS?>/google_login.php',
            type: 'POST',
            dataType: 'json',
            data: {
                act: 'google_login',
                login_mode: 'app',
                id_token: userData.id_token || '',
                google_id: userData.google_id,
                email: userData.email,
                nickname: userData.nickname,
                profile_image: userData.profile_image,
                access_token: userData.access_token
            },
            beforeSend: () => console.log('구글 로그인 처리 중...'),
            success: function (res) {
                if (res?.success) {
                    ModalUtil.alert({
                        title: '로그인',
                        message: res.message || '구글 로그인에 성공했습니다.',
                        okText: '확인',
                        onOk: () => location.href = res.redirect || '<?=APP_PAGE?>'
                    });
                } else {
                    const msg = res?.message || '구글 로그인 처리 중 오류가 발생했습니다.';
                    ModalUtil.alert({
                        title: '로그인',
                        message: msg,
                        okText: '확인',
                        onOk: () => {
                            if (res?.need_register) {
                                location.href = '<?=AUTH_PAGE?>/google_register.php?google_id=' + encodeURIComponent(userData.google_id);
                            }
                        }
                    });
                }
            },
            error: () => alert('구글 로그인 처리 중 오류가 발생했습니다.')
        });
    }

    function processAppleLogin(userData) {
        $.ajax({
            url: '<?=AUTH_ACTIONS?>/apple_login.php',
            type: 'POST',
            dataType: 'json',
            data: {
                act: 'apple_login',
                apple_id: userData.apple_id,
                email: userData.email,
                nickname: userData.nickname,
                access_token: userData.access_token || '',
                id_token: userData.id_token || ''
            },
            beforeSend: () => console.log('애플 로그인 처리 중...'),
            success: function (res) {
                if (res?.success) {
                    ModalUtil.alert({
                        title: '로그인',
                        message: res.message || '애플 로그인에 성공했습니다.',
                        okText: '확인',
                        onOk: () => location.href = res.redirect || '<?=APP_PAGE?>'
                    });
                } else {
                    const msg = res?.message || '애플 로그인 처리 중 오류가 발생했습니다.';
                    ModalUtil.alert({
                        title: '로그인',
                        message: msg,
                        okText: '확인',
                        onOk: () => {
                            if (res?.need_register) {
                                location.href = '<?=AUTH_PAGE?>/apple_register.php?apple_id=' + encodeURIComponent(userData.apple_id);
                            }
                        }
                    });
                }
            },
            error: () => alert('애플 로그인 처리 중 오류가 발생했습니다.')
        });
    }

    /**
     * 카카오 로그아웃 (필요 시 호출)
     */
    function kakaoLogout() {
        if (typeof Kakao !== 'undefined' && Kakao.Auth.getAccessToken()) {
            Kakao.Auth.logout(() => console.log('카카오 로그아웃 완료'));
        }
    }
</script>
