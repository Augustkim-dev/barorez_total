<?
$_SUB_HEAD_TITLE = "메인화면";
$_GET['hd_pc'] = 'logout'; //PC hd  로그인시공백 로그아웃시 logout
$hd_num = ' '; //왼쪽메뉴 active 땜시 만듬
include_once("./inc/head.php");
include_once("./inc/modal.php");

if (isset($_SESSION['mng']['mt_level']) && (int)$_SESSION['mng']['mt_level'] !== 5) {
    echo "<script>
        console.log('[auth] mt_level not 5 -> force logout');
        alert('로그아웃을 진행하겠습니다');
        location.replace('./logout.php');
    </script>";
    exit;
}

?>

    <div class="sub_pg pl-0 bg-white">
        <div class="login_wr">
            <div class="login_l text-center">
                <div>
                    <img src="<?=DESIGN_HTTP?>/market/img/login_ico.png" alt=" 로고이미지">
                    <p class="mt-5 text-white fs_20">바쁜 사장님들을 위한 간편한 주문 접수!</p>
                </div>
            </div>

            <div class="login_r">
                <div class="sign_box_wp">
                    <h2 class="tit_st1 text-center">매장 관리자 로그인</h2>
                    <div class="sign_box">
                        <form role="form"
                              method="post"
                              name="frm_login"
                              id="frm_login"
                              action="./login_update.php"
                              target="hidden_ifrm">

                            <div class="ip_wr ip_invalid">
                                <input type="text" class="form-control" placeholder="아이디 입력" name="mt_id" id="mt_id">
                                <div class="form-text ip_invalid">아이디를 입력해 주세요.</div>
                            </div>

                            <div class="ip_wr mt-4 ip_invalid">
                                <input type="password" class="form-control" placeholder="비밀번호 입력" name="mt_pass" id="mt_pass">
                                <div class="form-text ip_invalid">비밀번호를 입력해 주세요.</div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg btn-block mt-4" id="btn_login">로그인</button>
                        </form>

                        <div class="login_fbtn d-flex justify-content-center mt-5">
                            <a href="<?=MARKET_HTTP?>/find_id.php">아이디 찾기</a>
                            <a href="<?=MARKET_HTTP?>/find_pw.php">비밀번호 찾기</a>
                            <a href="<?=MARKET_HTTP?>/join.php">회원가입</a>
                        </div>

                        <div class="fs_15 tg_500 sign_warii text-center">
                            이 페이지는 접근과 동시에 IP주소가 자동저장됩니다.<br>
                            관계자 이외에 접근시도는 해킹시도로 의심, 추적되어 불이익을
                            당할 수 도 있습니다.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            console.log('[login] script loaded');

            // ✅ 헬퍼: 공백 제거
            function trimValue(v) {
                return String(v || '').trim();
            }

            // ✅ 헬퍼: 메시지 DOM (기존 .form-text.ip_invalid 사용)
            function getInvalidMsgEl(inputEl) {
                // inputEl -> closest .ip_wr -> .form-text.ip_invalid
                return $(inputEl).closest('.ip_wr').find('.form-text.ip_invalid').first();
            }

            // ✅ 헬퍼: 에러 표시/숨김
            function showError(inputEl, message) {
                console.log('[login] showError', inputEl.id, message);
                $(inputEl).addClass('is-invalid');
                const $msg = getInvalidMsgEl(inputEl);
                $msg.text(message).show();
            }

            function hideError(inputEl) {
                console.log('[login] hideError', inputEl.id);
                $(inputEl).removeClass('is-invalid');
                const $msg = getInvalidMsgEl(inputEl);
                // 기본 문구가 이미 있으니 숨김만 처리
                $msg.hide();
            }

            // ✅ 입력값 검증
            function validateForm() {
                console.log('[login] validateForm');

                const idEl = document.getElementById('mt_id');
                const pwEl = document.getElementById('mt_pass');

                const mtId = trimValue(idEl.value);
                const mtPw = trimValue(pwEl.value);

                let ok = true;

                if (!mtId) {
                    showError(idEl, '아이디를 입력해 주세요.');
                    ok = false;
                } else {
                    hideError(idEl);
                }

                if (!mtPw) {
                    showError(pwEl, '비밀번호를 입력해 주세요.');
                    ok = false;
                } else {
                    hideError(pwEl);
                }

                return ok;
            }

            // ✅ 버튼 잠금
            function setLoading(isLoading) {
                const $btn = $('#btn_login');
                if (!$btn.length) return;

                console.log('[login] setLoading', isLoading);

                $btn.prop('disabled', isLoading);
                // 퍼블리싱 유지: 텍스트만 임시 변경
                $btn.text(isLoading ? '로그인 중' : '로그인');
            }

            // ✅ AJAX 로그인
            function requestLogin() {
                console.log('[login] requestLogin');

                const mtId = trimValue($('#mt_id').val());
                const mtPw = trimValue($('#mt_pass').val());

                setLoading(true);

                $.ajax({
                    url: './login_update.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        mt_id: mtId,
                        mt_pass: mtPw
                    },
                    success: function (res) {
                        console.log('[login] ajax success', res);

                        // ✅ 스피너 모달 닫기
                        try {
                            $('#splinner_modal').modal('hide');
                        } catch (e) {}

                        if (res && res.success) {
                            const go = res.redirect || '/market';
                            // ✅ 성공 시 이동
                            ModalUtil.alert({
                                title: '로그인',
                                message: '로그인이 되었습니다.',
                                okText: '확인',
                                onOk: function () {
                                    location.href = go;
                                },
                            });
                            return;
                        }

                        // ✅ 실패 메시지 표시
                        const msg = (res && res.message) ? res.message : '로그인에 실패했습니다.';
                        ModalUtil.alert({
                            title: '로그인 실패',
                            message: msg,
                            okText: '확인',          // 기본값 '확인'
                            size: 'sm',              // 'sm' | 'md' | 'default' | 'full' | 'bottom'
                        });

                        setLoading(false);
                    },
                    error: function (xhr, status, error) {
                        console.log('[login] ajax error', status, error);
                        console.log('[login] responseText', xhr.responseText);

                        try {
                            $('#splinner_modal').modal('hide');
                        } catch (e) {}

                        alert('통신 오류가 발생했습니다.');
                        setLoading(false);
                    }
                });
            }

            // ✅ 초기: 기존 안내문은 숨겨두고(원하면 보여도 됨)
            function initInvalidMsgHidden() {
                console.log('[login] initInvalidMsgHidden');
                $('#frm_login .form-text.ip_invalid').hide();
            }

            // ✅ 이벤트 바인딩
            function bindEvents() {
                console.log('[login] bindEvents');

                // 제출
                $('#frm_login').on('submit', function (e) {
                    console.log('[login] submit');

                    e.preventDefault();

                    if (!validateForm()) {
                        console.log('[login] validation failed');
                        return false;
                    }

                    requestLogin();
                    return false;
                });

                // 입력 시 에러 해제
                $('#mt_id').on('input', function () {
                    hideError(this);
                });
                $('#mt_pass').on('input', function () {
                    hideError(this);
                });

                // 엔터키 대응(혹시 submit 이벤트 누락 대비)
                $('#mt_id, #mt_pass').on('keydown', function (e) {
                    if (e.key === 'Enter') {
                        console.log('[login] enter key');
                    }
                });
            }

            // ✅ 시작
            $(document).ready(function () {
                console.log('[login] document ready');
                initInvalidMsgHidden();
                bindEvents();
            });
        })();
    </script>

<? include_once("./inc/tail.php"); ?>
