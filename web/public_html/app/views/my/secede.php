
    <div class="wrap">
        <div class="sub_pg ">
            <div class="container">
                <div class="bg-light   rounded px_20 py_20">
                    <p class="tit_st3">탈퇴 전 확인하세요!</p>
                    <div class="mt-3 line_h1_5">
                        계정삭제, 리뷰삭제, 탈퇴처리 방침 내용 노출됩니다. 계정삭제, 리뷰삭제, 탈퇴처리 방침 내용 노출됩니다.
                        계정삭제, 리뷰삭제, 탈퇴처리 방침 내용 노출됩니다. 계정삭제, 리뷰삭제, 탈퇴처리 방침 내용 노출됩니다.
                        계정삭제, 리뷰삭제, 탈퇴처리 방침 내용 노출됩니다. 계정삭제, 리뷰삭제, 탈퇴처리 방침 내용 노출됩니다.
                    </div>
                </div>
                <div class="checks mt-4">
                    <label>
                        <input type="checkbox" name="chk1" id="secedeAgree">
                        <span class="ic_box"></span>
                        <div class="chk_p">
                            <p>탈퇴처리방침 동의합니다.</p>
                        </div>
                    </label>
                </div>
            </div>

        </div>
        <div class="bottom_btn bg-white">
            <div class="form-row">
                <div class="col-12">
                    <button type="button"
                            class="btn btn-primary btn-block btn-lg"
                            id="btnOpenSecede"
                            data-toggle="modal"
                            data-target="#pop_secede">
                        탈퇴하기
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- 탈퇴 팝업-->
    <div class="modal fade" id="pop_secede" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-body mt-5">
                    <div class="no_data  ">
                        <img src="<?=DESIGN_HTTP?>/img/img_mark.png">
                        <p class="   line_h1_4 mt-3 fs_18 fw_600">탈퇴 시 서비스 이용이 불가합니다.<br>
                            탈퇴 하시겠습니까?</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="form-row justify-content-end">
                        <div class="col-4"><button type="button" class="btn btn-outline-light btn-block  " data-dismiss="modal">취소</button></div>
                        <div class="col-8">
                            <button type="button"
                                    class="btn btn-primary btn-block"
                                    id="btnConfirmSecede"
                                    data-dismiss="modal">
                                확인
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(function () {
            console.log('회원 탈퇴 스크립트 초기화');

            const $agree       = $('#secedeAgree');
            const $btnOpen     = $('#btnOpenSecede');
            const $modal       = $('#pop_secede');
            const $btnConfirm  = $('#btnConfirmSecede');

            // 1) 모달 열기 전에 약관 동의 체크 여부 확인
            $btnOpen.on('click', function (e) {
                console.log('탈퇴하기 버튼 클릭');

                if (!$agree.prop('checked')) {
                    e.preventDefault();
                    e.stopPropagation();
                    alert('탈퇴처리방침에 동의해 주세요.');
                    return false;
                }

            });


            // 2) 모달에서 "확인" 클릭 시 → 실제 탈퇴 처리 AJAX
            $btnConfirm.on('click', function () {
                console.log('탈퇴 확인 버튼 클릭');

                if (!$agree.prop('checked')) {
                    alert('탈퇴처리방침에 동의해 주세요.');
                    return;
                }

                const mt_idx = <?=$_SESSION['mng']['mt_idx']?>;
                let url = '<?=AUTH_ACTIONS?>/secede.php'
                $.ajax({
                    url: url, // 🔥 실제 탈퇴 API 경로
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        act: 'secede',
                        mt_idx: mt_idx // 사용 안 한다면 서버에서 무시해도 됨
                    },
                    beforeSend: function () {
                        console.log('회원 탈퇴 AJAX 요청 시작');
                        $btnConfirm.prop('disabled', true);
                    },
                    success: function (res) {
                        console.log('회원 탈퇴 AJAX 응답:', res);

                        if (res && res.success) {
                            if (res.redirect) {
                                location.href = res.redirect;
                            } else {
                                location.href = '<?=APP_PAGE?>';
                            }
                        } else {
                            alert(res && res.message ? res.message : '탈퇴 처리 중 오류가 발생했습니다.');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('회원 탈퇴 AJAX 오류:', status, error);
                        console.log('서버 응답:', xhr.responseText);
                        alert('탈퇴 처리 중 오류가 발생했습니다. 잠시 후 다시 시도해 주세요.');
                    },
                    complete: function () {
                        $btnConfirm.prop('disabled', false);
                    }
                });
            });
        });
    </script>
