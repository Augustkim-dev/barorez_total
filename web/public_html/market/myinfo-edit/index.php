<?
$_SUB_HEAD_TITLE = "내정보 수정";
$_GET['hd_pc'] = ' ';
$hd_num = 'setting';
$hd_num2 = 'setting1';
$hd_left = ' ';
include_once("../inc/head.php");
include_once("../inc/header.php");
include_once("../inc/modal.php");
?>

    <!-- 왼쪽 메뉴-->
<? include_once("../inc/left_menu.php"); ?>

    <div class="sub_pg ">
        <div class="sub_wr">
            <div class="hd_tit2 flex-row">
                <div class="d-flex align-items-end flex-wrap">
                    <h3 class="tit_st1 mr-5">내정보 수정</h3>
                </div>
            </div>

            <section class="card">
                <div class="card-body">
                    <form id="edit_form">
                        <div class="">
                            <div class="pb-5">
                                <p class="tit_st3 "><img src="<?=DESIGN_HTTP?>/market/img/join_ico1.svg" alt=" 이미지" class="mr-3">기본정보</p>
                                <div class="row">
                                    <div class="col-md-6 mt-5">
                                        <div class="form_wr">
                                            <div class="ip_tit required">
                                                <h5>아이디</h5>
                                            </div>
                                            <input type="text" class="form-control" id="mt_id" placeholder="아이디" disabled>
                                        </div>

                                        <div class="form_wr mt-5">
                                            <div class="ip_tit required">
                                                <h5>비밀번호</h5>
                                            </div>
                                            <input type="password" class="form-control" name="mt_pwd" id="mt_pwd" placeholder="새 비밀번호 입력 (변경 시 입력)">
                                            <div class="form-text text-danger" id="pwd_error" style="display:none;">비밀번호를 입력해주세요</div>
                                        </div>

                                        <div class="form_wr mt-5">
                                            <div class="ip_tit required">
                                                <h5>비밀번호 재입력</h5>
                                            </div>
                                            <input type="password" class="form-control" name="mt_pwd_re" id="mt_pwd_re" placeholder="비밀번호 재입력">
                                            <div class="form-text text-danger" id="pwd_re_error" style="display:none;">비밀번호가 일치하지 않습니다.</div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mt-5">
                                        <div class="form_wr">
                                            <div class="ip_tit required">
                                                <h5>이름</h5>
                                            </div>
                                            <input type="text" class="form-control" name="mt_name" id="mt_name" placeholder="이름 입력">
                                            <div class="form-text text-danger" id="name_error" style="display:none;">이름을 입력해주세요</div>
                                        </div>

                                        <div class="form_wr mt-5">
                                            <div class="ip_tit required">
                                                <h5>휴대폰번호</h5>
                                            </div>
                                            <div class="form-row">
                                                <div class="col">
                                                    <input type="text" class="form-control" name="mt_hp" id="mt_hp" placeholder="‘-’ 없이 숫자만 입력" maxlength="11">
                                                </div>
                                                <div class="col-4">
                                                    <button type="button" class="btn btn-secondary btn-block px-1" id="send_hp_code_btn" disabled>인증 요청</button>
                                                </div>
                                            </div>

                                            <div class="form-row mt-3" id="hp_verify_row" style="display:none;">
                                                <div class="col position-relative">
                                                    <p class="time_lim" id="timer">03:00</p>
                                                    <input type="text" class="form-control" id="hp_code" placeholder="인증번호 입력" maxlength="6">
                                                </div>
                                                <div class="col-4">
                                                    <button type="button" class="btn btn-primary btn-block" id="verify_hp_btn">확인</button>
                                                </div>
                                            </div>

                                            <div class="form-text text-danger mt-2" id="hp_error" style="display:none;"></div>
                                            <div class="form-text text-success mt-2" id="hp_success" style="display:none;">휴대폰 인증이 완료되었습니다.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-5 border-top pt-5 pb-5">
                                <p class="tit_st3   "><img src="<?=DESIGN_HTTP?>/market/img/join_ico3.svg" alt="이미지" class="mr-3">정산 정보</p>

                                <div class="row">
                                    <div class="col-md-6 mt-5">
                                        <div class="form_wr">
                                            <div class="ip_tit required">
                                                <h5>통장사본 파일첨부</h5>
                                            </div>
                                            <div class="d-flex">
                                                <div class="image_upload">
                                                    <input id="bankbook_file" name="bankbook_file" type="file" class="d-none" accept="image/*">
                                                    <label for="bankbook_file" class="upload_box">
                                                        <div class="rect" id="bankbook_preview"></div>
                                                        <p class="max_img">사진 1/1</p>
                                                    </label>
                                                    <button type="button" class="btn upload_del" id="bankbook_del"><img src="<?=DESIGN_HTTP?>/market/img/img_del.png"></button>
                                                </div>
                                            </div>
                                            <div class="form-text text-danger mt-2" id="bankbook_error" style="display:none;"></div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mt-5">
                                        <div class="form_wr">
                                            <div class="ip_tit required">
                                                <h5>정산 받을 계좌</h5>
                                            </div>
                                            <div class="form-row mb-3">
                                                <div class="col-12">
                                                    <div class="custom-sel">
                                                        <button type="button" class="select-trigger" id="bank_trigger">
                                                            은행선택
                                                        </button>
                                                        <ul class="select-options" id="bank_options">
                                                            <!-- 동적으로 추가됨 -->
                                                        </ul>
                                                        <input type="hidden" id="sh_bank" name="sh_bank">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-row mb-3">
                                                <div class="col-12">
                                                    <input type="text" class="form-control" name="sh_bank_holder" id="sh_bank_holder" placeholder="예금주 입력">
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col-12">
                                                    <input type="text" class="form-control" name="sh_bank_account" id="sh_bank_account" placeholder="‘-’ 없이 계좌번호 입력해주세요.">
                                                </div>
                                            </div>
                                            <div class="form-text text-danger mt-2" id="settle_error" style="display:none;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </section>

            <div class="d-flex justify-content-center mt_40 btn_group">
                <button type="button" class="btn btn-outline-light btn-lg btn-w2" id="cancel_btn">취소</button>
                <button type="button" class="btn btn-primary btn-lg btn-w2" id="submit_btn">수정 완료</button>
            </div>
        </div>
    </div>

<script>
    $(document).ready(function() {
        let hpVerified = false;           // 휴대폰 인증 완료 여부
        let originalHp = '';              // 원본 휴대폰번호 (페이지 로드 시 저장)
        let timerInterval = null;         // 타이머 제어용

        // 은행 목록 초기화
        const banks = [
            { value: 'kbbank',      text: '국민은행 (KB국민은행)' },
            { value: 'shinhan',     text: '신한은행' },
            { value: 'woori',       text: '우리은행' },
            { value: 'hana',        text: '하나은행' },
            { value: 'ibk',         text: '기업은행 (IBK기업은행)' },
            { value: 'nh',          text: '농협은행 (NH농협은행)' },
            { value: 'suhyup',      text: '수협은행' },
            { value: 'bnk',         text: 'BNK부산은행' },
            { value: 'kyongnam',    text: '경남은행' },
            { value: 'daegu',       text: 'iM뱅크 (구 대구은행)' },
            { value: 'kdb',         text: '산업은행 (KDB산업은행)' },
            { value: 'sc',          text: 'SC제일은행' },
            { value: 'citi',        text: '씨티은행' },
            { value: 'jeonbuk',     text: '전북은행' },
            { value: 'gwangju',     text: '광주은행' },
            { value: 'jeju',        text: '제주은행' },
            { value: 'kbank',       text: '케이뱅크' },
            { value: 'kakaobank',   text: '카카오뱅크' },
            { value: 'tossbank',    text: '토스뱅크' },
            { value: 'post',        text: '우체국' },
            { value: 'savings',     text: '새마을금고' },
            { value: 'shinhyeop',   text: '신협' }
        ];

        const $bankOptions = $('#bank_options');
        banks.forEach(bank => {
            $bankOptions.append(`<li data-value="${bank.value}">${bank.text}</li>`);
        });

        $bankOptions.find('li').on('click', function() {
            $('#bank_trigger').text($(this).text());
            $('#sh_bank').val($(this).data('value'));
            $('#bank_trigger').closest('.custom-sel').removeClass('active');
        });

        $('#bank_trigger').on('click', function(e) {
            e.stopPropagation();
            $(this).closest('.custom-sel').toggleClass('active');
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('.custom-sel').length) $('.custom-sel').removeClass('active');
        });

        // 페이지 로드 시 기존 정보 불러오기
        loadUserInfo();

        function loadUserInfo() {
            $.ajax({
                url: './update.php',
                type: 'POST',
                dataType: 'json',
                data: { act: 'get_user_info' },
                success: function(res) {
                    if (res.success) {
                        // 기본 정보
                        $('#mt_id').val(res.data.mt_id || '');
                        $('#mt_name').val(res.data.mt_name || '');
                        $('#mt_hp').val(res.data.mt_hp || '').data('original_hp', res.data.mt_hp || '');
                        originalHp = res.data.mt_hp || '';

                        // 정산 정보
                        if (res.data.sh_bank) {
                            const $bank = $bankOptions.find(`li[data-value="${res.data.sh_bank}"]`);
                            if ($bank.length) {
                                $('#bank_trigger').text($bank.text());
                                $('#sh_bank').val(res.data.sh_bank);
                            }
                        }
                        $('#sh_bank_holder').val(res.data.sh_bank_holder || '');
                        $('#sh_bank_account').val(res.data.sh_bank_account || '');

                        // 통장사본 미리보기
                        if (res.data.sh_bankbook) {
                            $('#bankbook_preview').html(`<img src="/data/shop/${res.data.sh_idx}/${res.data.sh_bankbook}" style="width:100%; height:100%; object-fit:cover;">`);
                            $('.max_img').text('사진 1/1');
                        } else {
                            $('.max_img').text('사진 0/1');
                        }

                        // 초기 상태 체크
                        checkHpChanged();
                        updateSubmitButton();
                    } else {
                        ModalUtil.alert({
                            title: '회원정보',
                            message: res.message || '회원 정보를 불러올 수 없습니다.',
                            okText: '확인',
                            onOk: function () {
                            },
                        });
                    }
                },
                error: function() {
                    alert('서버와의 연결에 문제가 발생했습니다.');
                }
            });
        }

        // 휴대폰번호 변경 감지 & 인증 요청 버튼 제어
        $('#mt_hp').on('input', function() {
            checkHpChanged();
            updateSubmitButton();
        });

        function checkHpChanged() {
            const currentHp = $('#mt_hp').val().trim();
            const original = $('#mt_hp').data('original_hp') || '';

            if (currentHp !== original && currentHp.length >= 10 && /^\d+$/.test(currentHp)) {
                // 변경됨 → 인증 요청 버튼 활성화
                $('#send_hp_code_btn').prop('disabled', false);
                $('#hp_error').text('휴대폰번호 변경 시 인증이 필요합니다.').show();
                hpVerified = false;
            } else {
                // 변경 없음 → 인증 불필요, 버튼 비활성화
                $('#send_hp_code_btn').prop('disabled', true);
                $('#hp_error').hide();
                $('#hp_success').hide();
                $('#hp_verify_row').hide();
                hpVerified = true;  // 변경 안 했으니 인증 완료로 간주
            }
        }

        // 수정 완료 버튼 활성화/비활성화
        function updateSubmitButton() {
            const pwd = $('#mt_pwd').val().trim();
            const pwdRe = $('#mt_pwd_re').val().trim();
            const name = $('#mt_name').val().trim();
            const hp = $('#mt_hp').val().trim();
            const bank = $('#sh_bank').val();
            const holder = $('#sh_bank_holder').val().trim();
            const account = $('#sh_bank_account').val().trim();

            let valid = true;

            // 비밀번호 입력 시 일치 여부
            if (pwd !== '' && pwd !== pwdRe) valid = false;

            // 필수값 체크
            if (name === '') valid = false;
            if (hp === '') valid = false;
            if (bank === '' || holder === '' || account === '') valid = false;

            // 휴대폰 변경 시 인증 완료 여부 체크
            if (hp !== originalHp && !hpVerified) valid = false;

            $('#submit_btn').prop('disabled', !valid);
        }

        // 입력 필드 변화 시 버튼 상태 업데이트
        $('#mt_pwd, #mt_pwd_re, #mt_name, #mt_hp, #sh_bank, #sh_bank_holder, #sh_bank_account').on('input change', function() {
            if ($(this).attr('id') === 'mt_hp') checkHpChanged();
            updateSubmitButton();
        });

        // 인증 요청 버튼
        $('#send_hp_code_btn').on('click', function() {
            const hp = $('#mt_hp').val().trim();
            if (hp.length < 10 || !/^\d+$/.test(hp)) {
                $('#hp_error').text('올바른 휴대폰번호(10~11자리 숫자)를 입력해주세요.').show();
                return;
            }

            $('#send_hp_code_btn').prop('disabled', true).text('발송 중...');

            $.ajax({
                url: './update.php',
                type: 'POST',
                dataType: 'json',
                data: { act: 'send_hp_code', hp: hp },
                success: function(res) {
                    $('#send_hp_code_btn').prop('disabled', false).text('인증 요청');

                    if (res.success) {
                        ModalUtil.alert({
                            title: '회원정보',
                            message: res.message,
                            okText: '확인',
                            onOk: function () {
                            },
                        });
                        $('#hp_verify_row').show();
                        $('#hp_error').hide();
                        $('#hp_success').hide();

                        let timeLeft = 180;
                        $('#timer').text('03:00');
                        if (timerInterval) clearInterval(timerInterval);
                        timerInterval = setInterval(() => {
                            timeLeft--;
                            let min = Math.floor(timeLeft / 60).toString().padStart(2,'0');
                            let sec = (timeLeft % 60).toString().padStart(2,'0');
                            $('#timer').text(`${min}:${sec}`);

                            if (timeLeft <= 0) {
                                clearInterval(timerInterval);
                                $('#hp_verify_row').hide();
                                $('#hp_error').text('인증 시간이 만료되었습니다. 다시 요청해주세요.').show();
                                $('#send_hp_code_btn').prop('disabled', false);
                            }
                        }, 1000);
                    } else {
                        $('#hp_error').text(res.message || '인증 요청에 실패했습니다.').show();
                    }
                },
                error: function() {
                    $('#send_hp_code_btn').prop('disabled', false).text('인증 요청');
                    $('#hp_error').text('서버 연결 오류').show();
                }
            });
        });

        // 인증번호 확인 버튼
        $('#verify_hp_btn').on('click', function() {
            const code = $('#hp_code').val().trim();
            if (code.length !== 6 || !/^\d+$/.test(code)) {
                $('#hp_error').text('6자리 숫자 인증번호를 입력해주세요.').show();
                return;
            }

            $.ajax({
                url: './update.php',
                type: 'POST',
                dataType: 'json',
                data: { act: 'verify_hp_code', hp_code: code },
                success: function(res) {
                    if (res.success) {
                        clearInterval(timerInterval);
                        $('#hp_verify_row').hide();
                        $('#hp_success').show().text('휴대폰 인증이 완료되었습니다.');
                        $('#hp_error').hide();
                        hpVerified = true;
                        updateSubmitButton();
                        ModalUtil.alert({
                            title: '회원정보',
                            message: '인증완료',
                            okText: '확인',
                            onOk: function () {
                            },
                        });
                    } else {
                        $('#hp_error').text(res.message || '인증번호가 일치하지 않습니다.').show();
                    }
                },
                error: function() {
                    $('#hp_error').text('서버 연결 오류').show();
                }
            });
        });

        // 통장사본 파일 미리보기
        $('#bankbook_file').on('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(ev) {
                $('#bankbook_preview').html(`<img src="${ev.target.result}" style="width:100%; height:100%; object-fit:cover;">`);
                $('.max_img').text('사진 1/1');
            };
            reader.readAsDataURL(file);
        });

        // 통장사본 삭제
        $('#bankbook_del').on('click', function() {
            $('#bankbook_file').val('');
            $('#bankbook_preview').empty();
            $('.max_img').text('사진 0/1');
        });

        // 수정 완료 버튼
        $('#submit_btn').on('click', function() {
            // 휴대폰 변경 시 인증 여부 최종 확인
            if ($('#mt_hp').val().trim() !== originalHp && !hpVerified) {
                ModalUtil.alert({
                    title: '회원정보',
                    message: '휴대폰번호 변경 시 인증을 완료해주세요.',
                    okText: '확인',
                    onOk: function () {
                    },
                });
                return;
            }

            const formData = new FormData($('#edit_form')[0]);
            formData.append('act', 'update_user_info');

            const bankbookFile = $('#bankbook_file')[0].files[0];
            if (bankbookFile) formData.append('bankbook_file', bankbookFile);

            $.ajax({
                url: './update.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        ModalUtil.alert({
                            title: '회원정보',
                            message: res.message || '정보가 성공적으로 수정되었습니다.',
                            okText: '확인',
                            onOk: function () {
                                location.reload();
                            },
                        });
                    } else {
                        alert(res.message || '수정에 실패했습니다.');
                    }
                },
                error: function() {
                    alert('서버와의 연결에 문제가 발생했습니다.');
                }
            });
        });

        // 취소 버튼
        $('#cancel_btn').on('click', function() {
            ModalUtil.confirm({
                title: '회원정보',
                message: '수정을 취소하시겠습니까?',
                okText: '확인',
                cancelText: '취소',
                onOk: function () {
                    history.go(-2);
                },
                onCancel: function (){
                    return false;
                }
            });
        });
    });
</script>

<? include_once("../inc/tail.php"); ?>
