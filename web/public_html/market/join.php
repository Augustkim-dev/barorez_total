<?
$_SUB_HEAD_TITLE = "회원가입";
$_GET['hd_pc'] = 'logout'; //PC hd  로그인시공백 로그아웃시 logout
$hd_num = ' '; //왼쪽메뉴 active 땜시 만듬
include_once("./inc/head.php");
?>

    <div class="sub_pg pl-0 ">
        <div class="join_form_wr">
            <div class="hd_tit">
                <h2 class="tit_st1 d-flex align-items-center">
                    <a href="#" onclick="history.back(); return false;" class="mr-4 line_h0 ">
                        <img src="<?=DESIGN_HTTP?>/market/img/ico_back.svg" alt=" 뒤로가기">
                    </a>
                    <span>회원가입</span>
                </h2>
            </div>

            <!-- ✅ form 태그 적용 -->
            <form id="frm_register" method="post" action="./join_update.php" enctype="multipart/form-data">
                <input type="hidden" name="act" value="register">

                <!-- ✅ 상태값(기존 스크립트에서 쓰던 값 유지) -->
                <input type="hidden" name="id_check_ok" id="id_check_ok" value="N">
                <input type="hidden" name="hp_cert_ok" id="hp_cert_ok" value="N">

                <!-- ✅ 약관 hidden(기존 스크립트에서 쓰던 값 유지) -->
                <input type="hidden" name="agree_terms" id="agree_terms_val" value="N">
                <input type="hidden" name="agree_privacy" id="agree_privacy_val" value="N">

                <!-- ✅ 주소/좌표 배열형(서버가 [0]으로 받는 구조 유지) -->
                <input type="hidden" name="lat[]" id="lat_1" value="">
                <input type="hidden" name="lng[]" id="lng_1" value="">

                <div class="join_form">

                    <!-- =======================
                         기본정보
                    ======================== -->
                    <div class="pb-5">
                        <p class="tit_st3 ">
                            <img src="<?=DESIGN_HTTP?>/market/img/join_ico1.svg" alt=" 이미지" class="mr-3">기본정보
                        </p>

                        <div class="row">
                            <div class="col-md-6 mt-5">

                                <!-- 아이디 -->
                                <div class="form_wr" id="id_div">
                                    <div class="ip_tit required">
                                        <h5>아이디</h5>
                                    </div>
                                    <div class="form-row">
                                        <div class="col-8">
                                            <!-- ✅ name/id 적용 -->
                                            <input type="text" class="form-control" name="mb_id" id="mb_id" placeholder="아이디 입력">
                                        </div>
                                        <div class="col-4">
                                            <!-- ✅ id 부여(클래스 변경 X) -->
                                            <button type="button" class="btn btn-secondary btn-block px-1" id="btnCheckId">중복 확인</button>
                                        </div>
                                    </div>
                                    <div class="form-text ip_invalid" id="id_check_msg">중복확인을 진행해 주세요.</div>
                                </div>

                                <!-- 비밀번호 -->
                                <div class="form_wr mt-5">
                                    <div class="ip_tit required">
                                        <h5>비밀번호</h5>
                                    </div>
                                    <input type="password" class="form-control" name="mb_pw" id="mb_pw" placeholder="비밀번호 입력(영소문, 숫자 포함 8~16자)">
                                    <div class="form-text ip_invalid" id="pw_msg">비밀번호는 영소문/숫자 포함 8~16자여야 합니다.</div>
                                </div>

                                <!-- 비밀번호 재입력 -->
                                <div class="form_wr mt-5">
                                    <div class="ip_tit required">
                                        <h5>비밀번호 재입력</h5>
                                    </div>
                                    <input type="password" class="form-control" name="mb_pw_re" id="mb_pw_re" placeholder="비밀번호 재입력">
                                    <div class="form-text ip_invalid" id="pw_re_msg">비밀번호가 일치하지 않습니다.</div>
                                </div>

                            </div>

                            <div class="col-md-6 mt-5">

                                <!-- 이름 -->
                                <div class="form_wr">
                                    <div class="ip_tit required">
                                        <h5>이름</h5>
                                    </div>
                                    <input type="text" class="form-control" name="mb_name" id="mb_name" placeholder="이름 입력">
                                    <div class="form-text ip_invalid" id="name_msg">이름을 입력해 주세요.</div>
                                </div>

                                <!-- 휴대폰번호 인증 (HPAuth 적용) -->
                                <div class="form_wr mt-5">
                                    <div class="ip_tit required">
                                        <h5>휴대폰번호</h5>
                                    </div>

                                    <div class="form-row">
                                        <div class="col">
                                            <input type="text" class="form-control" name="mb_hp" id="mb_hp" placeholder="‘-’ 없이 숫자만 입력">
                                        </div>
                                        <div class="col-4">
                                            <button type="button" class="btn btn-secondary btn-block px-1" id="btnSendHpCode">인증 요청</button>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="col mt-3 position-relative">
                                            <p class="time_lim" id="hp_timer">00:00</p>
                                            <input type="text" class="form-control" id="hp_code" placeholder="인증번호 입력">
                                        </div>
                                        <div class="col-4 mt-3">
                                            <button type="button" class="btn btn-primary btn-block" id="btnVerifyHpCode" disabled>확인</button>
                                        </div>
                                    </div>

                                    <div class="form-text ip_invalid" id="hp_check_msg">인증 요청 후 인증번호를 입력해 주세요.</div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- =======================
                         사업자(매장) 정보
                    ======================== -->
                    <div class="mt-5 border-top pt-5 pb-5">
                        <p class="tit_st3">
                            <img src="<?=DESIGN_HTTP?>/market/img/join_ico2.svg" alt="이미지" class="mr-3">사업자(매장) 정보
                        </p>

                        <div class="row">
                            <div class="col-md-6 mt-5">

                                <div class="form_wr">
                                    <div class="ip_tit required">
                                        <h5>상호(법인명)</h5>
                                    </div>
                                    <!-- ✅ 배열 name 유지 -->
                                    <input type="text" class="form-control" name="store_name[]" id="store_name_1" placeholder="사업자등록증에 기재된 상호(법인명) 입력">
                                    <div class="form-text ip_invalid">상호(법인명)을 입력해 주세요.</div>
                                </div>

                                <div class="form_wr mt-5">
                                    <div class="ip_tit required">
                                        <h5>사업자등록번호</h5>
                                    </div>
                                    <input type="text" class="form-control" name="biz_no[]" id="biz_no_1" placeholder="입력하세요">
                                    <div class="form-text ip_invalid">사업자등록번호를 입력해 주세요.</div>
                                </div>

                                <div class="form_wr mt-5">
                                    <div class="ip_tit required">
                                        <h5>매장명</h5>
                                    </div>
                                    <input type="text" class="form-control" name="shop_name[]" id="shop_name_1" placeholder="매장명 입력">
                                    <div class="form-text ip_invalid">매장명을 입력해 주세요.</div>
                                </div>

                                <div class="form_wr mt-5">
                                    <div class="ip_tit">
                                        <h5>매장 전화번호</h5>
                                    </div>
                                    <input type="text" class="form-control" name="shop_tel[]" id="shop_tel_1" placeholder="전화번호 입력">
                                </div>
                            </div>

                            <div class="col-md-6 mt-5">

                                <div class="form_wr">
                                    <div class="ip_tit required">
                                        <h5>대표자명</h5>
                                    </div>
                                    <input type="text" class="form-control" name="owner_name[]" id="owner_name_1" placeholder="대표자명 입력">
                                    <div class="form-text ip_invalid">대표자명을 입력해 주세요.</div>
                                </div>

                                <!-- 주소 -->
                                <div class="form_wr mt-5">
                                    <div class="ip_tit required">
                                        <h5>주소</h5>
                                    </div>

                                    <div class="form-row">
                                        <div class="col">
                                            <input type="text" class="form-control" name="zip[]" id="zip_1" placeholder="우편번호 검색시 자동등록" readonly>
                                        </div>
                                        <div class="col-4">
                                            <button type="button" class="btn btn-secondary btn-block px-1 btn-addr-search" id="btnAddrSearch_1">우편번호 검색</button>
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <input type="text" class="form-control" name="addr1[]" id="addr1_1" placeholder="우편번호 검색시 자동등록" readonly>
                                    </div>

                                    <div class="mt-3">
                                        <input type="text" class="form-control" name="addr2[]" id="addr2_1" placeholder="상세주소">
                                    </div>

                                    <div class="form-text ip_invalid">주소를 입력해 주세요.</div>
                                </div>

                                <!-- 사업자등록증 파일 (PDF 가능) -->
                                <div class="form_wr mt-5">
                                    <div class="ip_tit required">
                                        <h5>사업자등록증</h5>
                                    </div>

                                    <div class="d-flex">
                                        <div class="image_upload" id="biz_upload_1">
                                            <!-- ✅ id 유니크(중요) / name은 API 그대로 -->
                                            <input id="biz_file_1" type="file" name="biz_file" class="d-none" accept=".jpg,.jpeg,.png,.gif,.webp,.pdf">
                                            <label for="biz_file_1" class="upload_box">
                                                <div class="rect"></div>
                                                <p class="max_img">사진 0/1</p>
                                            </label>
                                            <button type="button" class="btn upload_del"><img src="<?=DESIGN_HTTP?>/market/img/img_del.png"></button>
                                        </div>
                                    </div>

                                    <div class="form-text ip_invalid">사업자등록증 파일을 첨부해 주세요.</div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- =======================
                         정산 정보
                    ======================== -->
                    <div class="mt-5 border-top pt-5 pb-5">
                        <p class="tit_st3">
                            <img src="<?=DESIGN_HTTP?>/market/img/join_ico3.svg" alt="이미지" class="mr-3">정산 정보
                        </p>

                        <div class="row">
                            <div class="col-md-6 mt-5">

                                <div class="form_wr">
                                    <div class="ip_tit required">
                                        <h5>통장사본 파일첨부</h5>
                                    </div>

                                    <div class="d-flex">
                                        <div class="image_upload" id="bankbook_upload_1">
                                            <!-- ✅ name은 API 그대로(배열형) -->
                                            <input id="store_bankbook_1" type="file" name="store_bankbook[]" class="d-none" accept=".jpg,.jpeg,.png,.gif,.webp">
                                            <label for="store_bankbook_1" class="upload_box">
                                                <div class="rect"></div>
                                                <p class="max_img">사진 0/1</p>
                                            </label>
                                            <button type="button" class="btn upload_del"><img src="<?=DESIGN_HTTP?>/market/img/img_del.png"></button>
                                        </div>
                                    </div>

                                    <div class="form-text ip_invalid">통장사본 파일을 첨부해 주세요.</div>
                                </div>

                            </div>

                            <div class="col-md-6 mt-5">

                                <div class="form_wr">
                                    <div class="ip_tit required">
                                        <h5>정산 받을계좌</h5>
                                    </div>

                                    <div class="form-row">
                                        <div class="col-12 mb-3">
                                            <div class="custom-sel">
                                                <button type="button" class="select-trigger">은행선택</button>
                                                <ul class="select-options">
                                                    <li data-value="kbbank">국민은행 (KB국민은행)</li>
                                                    <li data-value="shinhan">신한은행</li>
                                                    <li data-value="woori">우리은행</li>
                                                    <li data-value="hana">하나은행</li>
                                                    <li data-value="ibk">기업은행 (IBK기업은행)</li>
                                                    <li data-value="nh">농협은행 (NH농협은행)</li>
                                                    <li data-value="suhyup">수협은행</li>
                                                    <li data-value="bnk">BNK부산은행</li>
                                                    <li data-value="kyongnam">경남은행</li>
                                                    <li data-value="daegu">iM뱅크 (구 대구은행)</li>
                                                    <li data-value="kdb">산업은행 (KDB산업은행)</li>
                                                    <li data-value="sc">SC제일은행</li>
                                                    <li data-value="citi">씨티은행</li>
                                                    <li data-value="jeonbuk">전북은행</li>
                                                    <li data-value="gwangju">광주은행</li>
                                                    <li data-value="jeju">제주은행</li>
                                                    <li data-value="kbank">케이뱅크</li>
                                                    <li data-value="kakaobank">카카오뱅크</li>
                                                    <li data-value="tossbank">토스뱅크</li>
                                                    <li data-value="post">우체국</li>
                                                    <li data-value="savings">새마을금고</li>
                                                    <li data-value="shinhyeop">신협</li>
                                                </ul>
                                                <!-- ✅ name/id 적용 -->
                                                <input type="hidden" name="settle_bank[]" id="settle_bank_1">
                                            </div>
                                        </div>

                                        <div class="col-12 mb-3">
                                            <input type="text" class="form-control" name="settle_holder[]" id="settle_holder_1" placeholder="예금주 입력">
                                        </div>

                                        <div class="col-12 mb-3">
                                            <input type="text" class="form-control" name="settle_account[]" id="settle_account_1" placeholder="‘-’ 없이 계좌번호 입력해주세요.">
                                        </div>
                                    </div>

                                    <div class="form-text ip_invalid">계좌정보를 입력하세요.</div>
                                </div>

                            </div>

                            <!-- 약관 -->
                            <div class="col-12 mt-5">
                                <div class="form_wr mt-5">
                                    <div class="ip_tit required">
                                        <h5>약관 동의</h5>
                                    </div>

                                    <div class="p-4 bg-light rounded mb-4">
                                        <div class="checks w-100 m-0 d-flex justify-content-between align-items-center">
                                            <label class="d-flex align-items-center w-100">
                                                <input type="checkbox" id="chkAllAgree">
                                                <span class="ic_box"></span>
                                                <div class="fw_600">
                                                    <p>전체 동의합니다.</p>
                                                </div>
                                            </label>
                                        </div>
                                    </div>

                                    <div id="terms_wr" class="terms_checks pl-4">
                                        <ul>
                                            <li id="terms_hd01">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="checks_wr mb-0">
                                                        <div class="checks mb-0">
                                                            <label>
                                                                <input type="checkbox" class="agree-required" id="agree_privacy">
                                                                <span class="ic_box"></span>
                                                                <div class="chk_p"><p>개인정보처리방침 (필수)</p></div>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <button type="button" class="btn btn-link collapse_bt" data-toggle="collapse" data-target="#terms01">
                                                        <div class=""></div>
                                                    </button>
                                                </div>
                                                <div id="terms01" class="collapse" data-parent="#terms_wr">
                                                    <div class="terms_cont bg-light rounded mt-3"><div class="edit_style">...</div></div>
                                                </div>
                                            </li>

                                            <li id="terms_hd02" class="mt_20">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="checks_wr mb-0">
                                                        <div class="checks mb-0">
                                                            <label>
                                                                <input type="checkbox" class="agree-required" id="agree_terms">
                                                                <span class="ic_box"></span>
                                                                <div class="chk_p"><p>이용약관 (필수)</p></div>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <button type="button" class="btn btn-link collapse_bt" data-toggle="collapse" data-target="#terms02">
                                                        <div class=""></div>
                                                    </button>
                                                </div>
                                                <div id="terms02" class="collapse" data-parent="#terms_wr">
                                                    <div class="terms_cont bg-light rounded mt-3"><div class="edit_style">...</div></div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>

                                    <div class="text-center mt_50">
                                        <!-- ✅ 실제 submit -->
                                        <button type="submit" class="btn btn-primary btn-lg btn-w1" id="btnRegister">회원가입</button>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <!-- Daum 주소 검색 API -->
    <script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
    <!-- 카카오 지도 JS -->
    <script src="//dapi.kakao.com/v2/maps/sdk.js?appkey=<?=KAKAO_JAVASCRIPT_KEY?>&libraries=services"></script>

    <script src="<?=MARKET_HTTP?>/utils/formState.js"></script>
    <script src="<?=MARKET_HTTP?>/utils/hpAuth.js"></script>

<script>
    (function () {
        'use strict';

        // ---------------------------
        // 의존성 체크
        // ---------------------------
        if (!window.jQuery) return;
        if (!window.FormState) { console.log('[join] FormState not found'); return; }
        if (!window.HPAuth) { console.log('[join] HPAuth not found'); return; }

        const $ = window.jQuery;

        const $frm = $('#frm_register');

        // ---------------------------
        // 공통 헬퍼
        // ---------------------------
        const val = ($el) => ($el.val() || '').toString().trim();
        const onlyNum = (s) => (s || '').toString().replace(/[^0-9]/g, '');

        const scrollToEl = ($el) => {
            try {
                const top = ($el.offset().top || 0) - 120;
                $('html, body').stop(true).animate({ scrollTop: top }, 150);
            } catch (e) {}
        };

        const focusInvalid = ($el, msg) => {
            window.FormState.setInvalid($el, msg);
            scrollToEl($el);
            try { $el.focus(); } catch (e) {}
        };

        // “입력하면 확인되었습니다.” / 비었으면 초기화(바로 빨간색 안띄움)
        const bindOkLive = ($el, emptyMsg, okMsg) => {
            if (!$el || !$el.length) return;
            $el.on('input', function () {
                if (val($el)) window.FormState.setValid($el, okMsg || '확인되었습니다.');
                else window.FormState.clearState($el, emptyMsg || '');
            });
        };

        // ---------------------------
        // 요소 캐싱 (id/name 확정이라 매우 짧아짐)
        // ---------------------------
        const $mbId   = $('#mb_id');
        const $mbPw   = $('#mb_pw');
        const $mbPwRe = $('#mb_pw_re');
        const $mbName = $('#mb_name');

        const $mbHp   = $('#mb_hp');
        const $hpCode = $('#hp_code');

        const $btnCheckId   = $('#btnCheckId');
        const $btnSendHp    = $('#btnSendHpCode');
        const $btnVerifyHp  = $('#btnVerifyHpCode');

        const $idCheckOk = $('#id_check_ok');
        const $hpCertOk  = $('#hp_cert_ok');

        const $corpNm = $('#store_name_1');
        const $bizNo  = $('#biz_no_1');
        const $shopNm = $('#shop_name_1');
        const $shopHp = $('#shop_tel_1');
        const $ceoNm  = $('#owner_name_1');

        const $zip   = $('#zip_1');
        const $addr1 = $('#addr1_1');
        const $addr2 = $('#addr2_1');
        const $lat   = $('#lat_1');
        const $lng   = $('#lng_1');

        const $bizFile = $('#biz_file_1');            // name="biz_file"
        const $bankbook = $('#store_bankbook_1');     // name="store_bankbook[]"

        const $settleBank = $('#settle_bank_1');      // name="settle_bank[]"
        const $settleHolder = $('#settle_holder_1');  // name="settle_holder[]"
        const $settleAccount = $('#settle_account_1');// name="settle_account[]"

        const $agreePrivacy = $('#agree_privacy');
        const $agreeTerms   = $('#agree_terms');
        const $agreeAll     = $('#chkAllAgree');

        const $agreeTermsVal   = $('#agree_terms_val');
        const $agreePrivacyVal = $('#agree_privacy_val');

        // ---------------------------
        // 숫자만 입력 (공통)
        // ---------------------------
        window.FormState.bindOnlyNumber('#mb_hp');
        window.FormState.bindOnlyNumber('#hp_code');
        window.FormState.bindOnlyNumber('#biz_no_1');
        window.FormState.bindOnlyNumber('#settle_account_1');

        // ---------------------------
        // 1) 아이디 중복확인
        // ---------------------------
        const checkId = () => {
            const id = val($mbId);
            if (!id) {
                $idCheckOk.val('N');
                focusInvalid($mbId, '아이디를 입력해주세요.');
                return;
            }

            // 진행 표시
            window.FormState.clearState($mbId, '중복 확인중...');

            $.ajax({
                url: './join_update.php',
                type: 'POST',
                dataType: 'json',
                data: { act: 'check_id', mb_id: id },
                success: (res) => {
                    console.log('[join][check_id]', res);
                    if (res && res.success) {
                        $idCheckOk.val('Y');
                        window.FormState.setValid($mbId, res.message || '사용 가능한 아이디입니다.');
                    } else {
                        $idCheckOk.val('N');
                        window.FormState.setInvalid($mbId, (res && res.message) || '이미 사용중인 아이디입니다.');
                    }
                },
                error: (xhr) => {
                    console.log('[join][check_id] error', xhr);
                    $idCheckOk.val('N');
                    window.FormState.setInvalid($mbId, '통신 오류로 중복확인에 실패했습니다.');
                }
            });
        };

        $btnCheckId.on('click', checkId);
        $mbId.on('input', function () {
            $idCheckOk.val('N');
            window.FormState.clearState($mbId, '중복확인을 진행해 주세요.');
        });

        // ---------------------------
        // 2) 비밀번호 규칙/일치 (영소문+숫자 8~16)
        // ---------------------------
        const pwRuleOk = (pw) => {
            const s = String(pw || '');
            if (s.length < 8 || s.length > 16) return false;
            if (!/[a-z]/.test(s)) return false;
            if (!/[0-9]/.test(s)) return false;
            return true;
        };

        const validatePwLive = () => {
            const pw = val($mbPw);
            const re = val($mbPwRe);

            if (!pw) {
                window.FormState.clearState($mbPw, '');
                return false;
            }
            if (!pwRuleOk(pw)) {
                window.FormState.setInvalid($mbPw, '영소문, 숫자 포함 8~16자리로 입력해주세요.');
                return false;
            }
            window.FormState.setValid($mbPw, '확인되었습니다.');

            if (!re) {
                window.FormState.clearState($mbPwRe, '');
                return false;
            }
            if (pw !== re) {
                window.FormState.setInvalid($mbPwRe, '비밀번호가 일치하지 않습니다.');
                return false;
            }
            window.FormState.setValid($mbPwRe, '확인되었습니다.');
            return true;
        };

        $mbPw.on('input', validatePwLive);
        $mbPwRe.on('input', validatePwLive);

        // ---------------------------
        // 3) HPAuth 연결 (회원가입용)
        // ---------------------------
        window.HPAuth.init({
            ajaxUrl: './join_update.php',
            hpInput: '#mb_hp',
            codeInput: '#hp_code',
            sendBtn: '#btnSendHpCode',
            verifyBtn: '#btnVerifyHpCode',
            timerEl: '#hp_timer',
            certOkInput: '#hp_cert_ok',
            hpCode: '#hp_code',
            actSend: 'send_hp_code',
            actVerify: 'verify_hp_code',

            sendTextDefault: '인증 요청',
            sendTextRetry: '재 요청',

            msgDefault: '인증 요청 후 인증번호를 입력해 주세요.',
            msgSending: '인증번호 전송중...',
            msgVerifying: '인증번호 확인중...',
            msgExpired: '인증시간이 만료되었습니다. 다시 인증 요청을 해주세요.',
            msgVerified: '인증이 완료되었습니다.',

            timerSec: 300,
            lockOnSuccess: true,
        });

        // ---------------------------
        // 4) 파일 업로드 UI (0/1 + 미리보기 + 삭제)
        // ---------------------------
        const initUpload = ($input) => {
            if (!$input.length) return;

            const $wrap  = $input.closest('.image_upload');
            const $label = $wrap.find('label.upload_box').first();
            const $rect  = $label.find('.rect').first();
            const $count = $label.find('.max_img').first();
            const $del   = $wrap.find('.upload_del').first();

            const setCount = (hasFile) => {
                if ($count.length) $count.text(hasFile ? '사진 1/1' : '사진 0/1');
            };

            const clear = () => {
                try { $input.val(''); } catch (e) {}
                $wrap.removeClass('on');
                $rect.empty();
                setCount(false);
            };

            const preview = (file) => {
                $wrap.addClass('on');
                $rect.empty();

                const isImg = (file.type || '').indexOf('image/') === 0;
                if (isImg) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        $rect.empty().append($('<img>').attr('src', e.target.result));
                    };
                    reader.readAsDataURL(file);
                } else {
                    // pdf 등
                    $rect.append(
                        $('<div>').css({
                            display: 'flex',
                            alignItems: 'center',
                            justifyContent: 'center',
                            height: '100%',
                            fontSize: '14px'
                        }).text(file.name)
                    );
                }

                setCount(true);
            };

            // 초기 표시
            setCount(false);

            // label 클릭 -> input click
            $label.on('click', function (e) {
                e.preventDefault();
                $input.trigger('click');
            });

            // change
            $input.on('change', function () {
                const f = this.files && this.files[0] ? this.files[0] : null;
                if (!f) { clear(); return; }
                preview(f);

                // 파일 선택하면 “확인되었습니다.”
                window.FormState.setValid($input, '확인되었습니다.');
            });

            // delete
            $del.on('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                clear();
                window.FormState.setInvalid($input, '파일을 첨부해 주세요.');
            });
        };

        initUpload($bizFile);
        initUpload($bankbook);

        // ---------------------------
        // 5) 약관 체크 -> hidden(Y/N)
        // ---------------------------
        const syncAgree = () => {
            $agreeTermsVal.val($agreeTerms.is(':checked') ? 'Y' : 'N');
            $agreePrivacyVal.val($agreePrivacy.is(':checked') ? 'Y' : 'N');
        };
        syncAgree();

        $agreeTerms.on('change', syncAgree);
        $agreePrivacy.on('change', syncAgree);

        $agreeAll.on('change', function () {
            const on = $(this).is(':checked');
            $agreeTerms.prop('checked', on);
            $agreePrivacy.prop('checked', on);
            syncAgree();
        });

        // ---------------------------
        // 6) 주소 검색 (Daum + Kakao geocoder)
        // ---------------------------
        let kakaoGeocoder = null;

        const initGeocoder = () => {
            if (window.kakao && kakao.maps && kakao.maps.services) {
                kakaoGeocoder = new kakao.maps.services.Geocoder();
                console.log('[join][addr] geocoder ready');
            } else {
                console.log('[join][addr] kakao sdk not loaded');
            }
        };
        initGeocoder();

        const openPostcode = () => {
            if (!window.daum || !daum.Postcode) {
                console.log('[join][addr] daum.Postcode not loaded');
                return;
            }

            new daum.Postcode({
                oncomplete: function (data) {
                    let addr = data.roadAddress;
                    if (!addr) addr = data.jibunAddress;

                    $zip.val(data.zonecode || '');
                    $addr1.val(addr || '');
                    $addr2.val('').focus();

                    // zip/addr1 ok
                    window.FormState.setValid($zip, '확인되었습니다.');
                    window.FormState.setValid($addr1, '확인되었습니다.');
                    window.FormState.clearState($addr2, '상세주소를 입력해주세요.');

                    // 좌표 변환
                    if (!kakaoGeocoder) initGeocoder();

                    if (!kakaoGeocoder || !addr) {
                        $lat.val(''); $lng.val('');
                        return;
                    }

                    kakaoGeocoder.addressSearch(addr, function (result, status) {
                        if (status === kakao.maps.services.Status.OK && result && result[0]) {
                            $lat.val(result[0].y || '');
                            $lng.val(result[0].x || '');
                        } else {
                            $lat.val(''); $lng.val('');
                        }
                    });
                }
            }).open();
        };

        // 버튼(아이디 확정)
        $('#btnAddrSearch_1').on('click', function (e) {
            e.preventDefault();
            openPostcode();
        });

        // 상세주소 입력 시 ok 표시
        $addr2.on('input', function () {
            if (val($addr2)) window.FormState.setValid($addr2, '확인되었습니다.');
            else window.FormState.clearState($addr2, '상세주소를 입력해주세요.');
        });

        // ---------------------------
        // 7) 은행 커스텀 셀렉트
        // ---------------------------
        $(document).on('click', '.custom-sel .select-options li', function () {
            const v = ($(this).data('value') || '').toString();
            const t = ($(this).text() || '').trim();

            $settleBank.val(v);
            $(this).closest('.custom-sel').find('.select-trigger').text(t || '은행선택');

            if (v) window.FormState.setValid($settleBank, '확인되었습니다.');
        });

        // ---------------------------
        // 8) 라이브 “확인되었습니다.” 처리 (필수들)
        // ---------------------------
        bindOkLive($mbName, '이름을 입력해 주세요.');
        bindOkLive($corpNm, '상호(법인명)을 입력해 주세요.');
        bindOkLive($bizNo, '사업자등록번호를 입력해 주세요.');
        bindOkLive($shopNm, '매장명을 입력해 주세요.');
        // bindOkLive($shopHp, '전화번호를 입력해 주세요.');
        bindOkLive($ceoNm, '대표자명을 입력해 주세요.');
        bindOkLive($settleHolder, '예금주를 입력해 주세요.');
        bindOkLive($settleAccount, '계좌번호를 입력해 주세요.');

        // ---------------------------
        // 9) 최종 검증 (alert 금지)
        // ---------------------------
        const validateAll = () => {
            // 아이디
            if (!val($mbId)) { focusInvalid($mbId, '아이디를 입력해주세요.'); return false; }
            if ($idCheckOk.val() !== 'Y') { focusInvalid($mbId, '아이디 중복확인을 완료해주세요.'); return false; }

            // 비밀번호
            const pw = val($mbPw);
            const re = val($mbPwRe);
            if (!pw) { focusInvalid($mbPw, '비밀번호를 입력해주세요.'); return false; }
            if (!pwRuleOk(pw)) { focusInvalid($mbPw, '영소문, 숫자 포함 8~16자리로 입력해주세요.'); return false; }
            if (!re) { focusInvalid($mbPwRe, '비밀번호를 재입력해주세요.'); return false; }
            if (pw !== re) { focusInvalid($mbPwRe, '비밀번호가 일치하지 않습니다.'); return false; }

            // 이름
            if (!val($mbName)) { focusInvalid($mbName, '이름을 입력해 주세요.'); return false; }

            // 휴대폰
            if (!val($mbHp)) { focusInvalid($mbHp, '휴대폰번호를 입력해 주세요.'); return false; }
            if ($hpCertOk.val() !== 'Y') { focusInvalid($mbHp, '휴대폰 인증을 완료해주세요.'); return false; }

            // 사업자정보
            if (!val($corpNm)) { focusInvalid($corpNm, '상호(법인명)을 입력해 주세요.'); return false; }
            if (!val($bizNo)) { focusInvalid($bizNo, '사업자등록번호를 입력해 주세요.'); return false; }
            if (!val($shopNm)) { focusInvalid($shopNm, '매장명을 입력해 주세요.'); return false; }
            // if (!val($shopHp)) { focusInvalid($shopHp, '전화번호를 입력해 주세요.'); return false; }
            if (!val($ceoNm)) { focusInvalid($ceoNm, '대표자명을 입력해 주세요.'); return false; }

            // 주소
            if (!val($zip)) { focusInvalid($zip, '우편번호 검색을 진행해 주세요.'); return false; }
            if (!val($addr1)) { focusInvalid($addr1, '주소를 입력해 주세요.'); return false; }
            if (!val($addr2)) { focusInvalid($addr2, '상세주소를 입력해 주세요.'); return false; }

            // 사업자등록증 파일
            if (!$bizFile[0].files || !$bizFile[0].files[0]) {
                focusInvalid($bizFile, '사업자등록증 파일을 첨부해 주세요.');
                return false;
            }

            // 정산정보
            if (!val($settleBank)) {
                // 커스텀 버튼에 포커스 주는게 더 자연스러움
                const $trigger = $('.custom-sel .select-trigger').first();
                if ($trigger.length) {
                    const $wr = $trigger.closest('.form_wr');
                    $wr.removeClass('ip_valid').addClass('ip_invalid');
                    $wr.find('.form-text').first().text('은행을 선택해주세요.').removeClass('ip_valid').addClass('ip_invalid');
                    scrollToEl($trigger);
                    $trigger.focus();
                } else {
                    focusInvalid($settleBank, '은행을 선택해주세요.');
                }
                return false;
            }

            if (!val($settleHolder)) { focusInvalid($settleHolder, '예금주를 입력해 주세요.'); return false; }
            if (!val($settleAccount)) { focusInvalid($settleAccount, '계좌번호를 입력해 주세요.'); return false; }

            // 통장사본 파일
            if (!$bankbook[0].files || !$bankbook[0].files[0]) {
                focusInvalid($bankbook, '통장사본 파일을 첨부해 주세요.');
                return false;
            }

            // 약관
            syncAgree();
            if ($agreePrivacyVal.val() !== 'Y') {
                const $wr = $agreePrivacy.closest('.form_wr');
                $wr.removeClass('ip_valid').addClass('ip_invalid');
                $wr.find('.form-text').first().text('개인정보처리방침(필수)에 동의해주세요.').removeClass('ip_valid').addClass('ip_invalid');
                scrollToEl($agreePrivacy);
                $agreePrivacy.focus();
                return false;
            }
            if ($agreeTermsVal.val() !== 'Y') {
                const $wr = $agreeTerms.closest('.form_wr');
                $wr.removeClass('ip_valid').addClass('ip_invalid');
                $wr.find('.form-text').first().text('이용약관(필수)에 동의해주세요.').removeClass('ip_valid').addClass('ip_invalid');
                scrollToEl($agreeTerms);
                $agreeTerms.focus();
                return false;
            }

            return true;
        };

        // ---------------------------
        // 10) submit (join_update.php는 JSON 응답이므로 AJAX로 처리)
        // ---------------------------
        $frm.on('submit', function (e) {
            e.preventDefault();

            console.log('[join] submit');

            if (!validateAll()) return;

            const formData = new FormData($frm[0]);
            formData.set('act', 'register'); // 혹시라도 누락 대비

            $.ajax({
                url: './join_update.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: (res) => {
                    console.log('[join] register res', res);
                    if (res && res.success) {
                        location.href = './join_cmp.php';
                    } else {
                        // 대표 에러는 아이디 블록에 노출
                        window.FormState.setInvalid($mbId, (res && res.message) || '회원가입에 실패했습니다.');
                    }
                },
                error: (xhr) => {
                    console.log('[join] register error', xhr);
                    window.FormState.setInvalid($mbId, '서버 통신 오류가 발생했습니다.');
                }
            });
        });

        console.log('[join] script ready');
    })();
</script>

    <style>
        /* ✅ 미리보기 이미지가 안 보이는 경우를 대비(퍼블 깨짐 방지 수준) */
        .image_upload .rect img.__preview {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .image_upload .rect .__filetext {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }
    </style>

<? include_once("./inc/tail.php"); ?>
