<?
$_SUB_HEAD_TITLE = "매장관리>매장정보";
$_GET['hd_pc'] = ' '; //PC hd  로그인시공백 로그아웃시 logout
$hd_num = 'store'; //왼쪽메뉴 active 땜시 만듬
$hd_left = ' '; //왼쪽메뉴 on 땜시 만듬
include_once("../inc/head.php");
include_once("../inc/header.php");
include_once("../inc/modal.php");
?>

    <!-- 왼쪽 메뉴-->
<? include_once("../inc/left_menu.php"); ?>

    <div class="sub_pg ">
        <div class="sub_wr">
            <div class="hd_tit2 flex-row-reverse">
                <div class="flex-shrink-0 ml-auto">
                    <button type="button" class="btn btn-secondary rounded-pill " onclick="location.href='../store' ">매장정보</button>
                    <button type="button" class="btn btn-outline-light rounded-pill ml-2" onclick="location.href='../store-time' ">운영시간</button>
                    <button type="button" class="btn btn-outline-light rounded-pill ml-2" onclick="location.href='../store-set' ">기능설정</button>
                </div>
                <div class="d-flex align-items-end flex-wrap">
                    <h3 class="tit_st1 mr-5">매장관리</h3>
                </div>
            </div>

            <!-- ✅ 파일업로드 포함: 반드시 multipart -->
            <form id="storeForm" enctype="multipart/form-data" onsubmit="return false;">
                <input type="hidden" id="sh_lat" name="sh_lat" value="">
                <input type="hidden" id="sh_lng" name="sh_lng" value="">
                <!-- 회원가입에 작성한 내용 그대로 나옴-->
                <section class="card">
                    <div class="card-body">
                        <p class="tit_st3"><img src="<?=DESIGN_HTTP?>/market/img/join_ico2.svg" alt="이미지" class="mr-3">사업자(매장) 정보</p>

                        <div class="row">
                            <div class="col-md-6 mt-5">
                                <div class="form_wr">
                                    <div class="ip_tit required"><h5>상호(법인명)</h5></div>
                                    <input type="text" class="form-control" id="sh_corp_nm" name="sh_corp_nm" placeholder="사업자등록증에 기재된 상호(법인명) 입력">
                                    <div class="form-text ip_invalid" style="display:none;">반대문구</div>
                                </div>

                                <div class="form_wr mt-5">
                                    <div class="ip_tit required"><h5>사업자등록번호</h5></div>
                                    <input type="text" class="form-control" id="sh_biz_no" name="sh_biz_no" placeholder="입력하세요">
                                    <div class="form-text ip_invalid" style="display:none;">반대문구</div>
                                </div>

                                <div class="form_wr mt-5">
                                    <div class="ip_tit required"><h5>매장명</h5></div>
                                    <input type="text" class="form-control" id="sh_title" name="sh_title" placeholder="매장명 입력">
                                    <div class="form-text ip_invalid" style="display:none;">반대문구</div>
                                </div>

                                <div class="form_wr mt-5">
                                    <div class="ip_tit required"><h5>매장 연락처</h5></div>
                                    <input type="text" class="form-control" id="sh_tel" name="sh_tel" placeholder="연락처 입력">
                                    <div class="form-text ip_invalid" style="display:none;">반대문구</div>
                                </div>
                            </div>

                            <div class="col-md-6 mt-5">
                                <div class="form_wr">
                                    <div class="ip_tit required"><h5>대표자명</h5></div>
                                    <input type="text" class="form-control" id="sh_ceo_nm" name="sh_ceo_nm" placeholder="대표자명 입력">
                                    <div class="form-text ip_invalid" style="display:none;">반대문구</div>
                                </div>

                                <div class="form_wr mt-5">
                                    <div class="ip_tit required"><h5>주소</h5></div>

                                    <div class="form-row">
                                        <div class="col">
                                            <input type="text" class="form-control" id="sh_zip" name="sh_zip" placeholder="우편번호 검색시 자동등록" readonly>
                                        </div>
                                        <div class="col-4">
                                            <button type="button" class="btn btn-secondary btn-block px-1" id="btn_zip_search">우편번호 검색</button>
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <input type="text" class="form-control" id="sh_addr1" name="sh_addr1" placeholder="우편번호 검색시 자동등록" readonly>
                                    </div>
                                    <div class="mt-3">
                                        <input type="text" class="form-control" id="sh_addr2" name="sh_addr2" placeholder="상세주소">
                                    </div>
                                    <div class="form-text ip_invalid" style="display:none;">오류 텍스트</div>
                                </div>

                                <!-- ✅ 사업자등록증 -->
                                <div class="form_wr mt-5">
                                    <div class="ip_tit required"><h5>사업자등록증</h5></div>

                                    <div class="d-flex">
                                        <!-- 빈 슬롯 -->
                                        <div class="image_upload" id="biz_wrap">
                                            <!-- ✅ name 중요: $_FILES['biz_file']로 들어옴 -->
                                            <input id="biz_file" name="sh_biz_file" type="file" class="d-none" accept="image/*,application/pdf">
                                            <label for="biz_file" class="upload_box">
                                                <div class="rect">
                                                    <!-- ✅ 미리보기 이미지 -->
                                                    <img id="biz_preview" src="" style="display:none; width:100%; height:100%; object-fit:cover;">
                                                </div>
                                                <p class="max_img">사진 1/1</p>
                                            </label>
                                            <!-- ✅ 삭제 버튼: data-del -->
                                            <button type="button" class="btn upload_del" data-del="biz">
                                                <img src="<?=DESIGN_HTTP?>/market/img/img_del.png">
                                            </button>
                                        </div>
                                    </div>

                                    <div class="form-text ip_invalid" style="display:none;">반대문구</div>

                                    <!-- ✅ 삭제 플래그(서버로 같이 전송) -->
                                    <input type="hidden" id="del_biz" name="del_biz" value="N">
                                </div>

                            </div>
                        </div>

                    </div>
                </section>

                <section class="card mt-4">
                    <div class="card-body">
                        <div class="form_wr">
                            <div class="ip_tit"><h5>매장 소개</h5></div>
                            <textarea class="form-control" id="sh_contents" name="sh_contents" placeholder="매장을 소개하는 문구를 입력하세요" rows="5"></textarea>
                            <p class="text-right mt-2 tg_500 fs_14" id="contents_count">(0/500)</p>
                        </div>
                    </div>
                </section>

                <!-- ✅ 매장 이미지(최소 3장) - DB는 3장만 있으니 3개만 연결 -->
                <section class="card mt-4 ">
                    <div class="card-body">
                        <div class="form_wr">
                            <div class="ip_tit">
                                <h5>매장 이미지(최소 3장)</h5>
                            </div>

                            <!-- ✅ 여기 안에 슬롯이 JS로 '1개 → 2개 → 3개' 늘어남 -->
                            <div class="d-flex" id="shop_imgs_wrap"></div>

                            <!-- ✅ 서버 전송은 고정 input 3개로 처리 (FormData에 포함됨) -->
                            <input id="shop_img1" name="shop_img1" type="file" class="d-none" accept="image/*">
                            <input id="shop_img2" name="shop_img2" type="file" class="d-none" accept="image/*">
                            <input id="shop_img3" name="shop_img3" type="file" class="d-none" accept="image/*">
                            <input id="shop_img4" name="shop_img4" type="file" class="d-none" accept="image/*">
                            <input id="shop_img5" name="shop_img5" type="file" class="d-none" accept="image/*">

                            <!-- ✅ 삭제 플래그 -->
                            <input type="hidden" id="del_img1" name="del_img1" value="N">
                            <input type="hidden" id="del_img2" name="del_img2" value="N">
                            <input type="hidden" id="del_img3" name="del_img3" value="N">
                            <input type="hidden" id="del_img4" name="del_img4" value="N">
                            <input type="hidden" id="del_img5" name="del_img5" value="N">
                        </div>
                    </div>
                </section>

            </form>

            <div class="text-center mt_50 mb-5">
                <button type="button" class="btn btn-primary btn-lg btn-w1" id="btn_store_save">저장</button>
            </div>

        </div>
    </div>

<!-- Daum 주소 검색 API -->
<script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<!-- 카카오 지도 JS -->
<script src="//dapi.kakao.com/v2/maps/sdk.js?appkey=<?=KAKAO_JAVASCRIPT_KEY?>&libraries=services"></script>


<script>
    (function () {
        'use strict';

        if (!window.jQuery) return;
        const $ = window.jQuery;

        // =========================
        // 설정
        // =========================
        const API_URL = './update.php';
        const MAX_IMGS = 5;
        const MIN_IMGS = 3;

        // DATA_URL 이 있으면 사용, 없으면 현재 origin 사용 (프로젝트에 맞게 조정 가능)
        const DATA_BASE = (typeof window.DATA_URL !== 'undefined' && window.DATA_URL)
            ? String(window.DATA_URL).replace(/\/+$/,'')
            : (location.origin);

        // =========================
        // FormState (join 때 방식 호환)
        // =========================
        const FS = window.FormState || {
            setInvalid: function($el, msg){
                const $wr = $el.closest('.form_wr');
                $wr.removeClass('ip_valid').addClass('ip_invalid');
                const $t = $wr.find('.form-text').first();
                if ($t.length) $t.text(msg || '').show();
            },
            setValid: function($el, msg){
                const $wr = $el.closest('.form_wr');
                $wr.removeClass('ip_invalid').addClass('ip_valid');
                const $t = $wr.find('.form-text').first();
                if ($t.length) $t.text(msg || '확인되었습니다.').hide();
            },
            clearState: function($el, msg){
                const $wr = $el.closest('.form_wr');
                $wr.removeClass('ip_invalid ip_valid');
                const $t = $wr.find('.form-text').first();
                if ($t.length) {
                    if (msg) $t.text(msg).show();
                    else $t.hide();
                }
            },
            bindOnlyNumber: function(sel){
                $(document).on('input', sel, function(){
                    this.value = String(this.value || '').replace(/[^0-9]/g,'');
                });
            }
        };

        // =========================
        // DOM 캐시
        // =========================
        const $form = $('#storeForm');

        const $sh_corp_nm = $('#sh_corp_nm');
        const $sh_biz_no  = $('#sh_biz_no');
        const $sh_title   = $('#sh_title');
        const $sh_tel     = $('#sh_tel');
        const $sh_ceo_nm  = $('#sh_ceo_nm');

        const $sh_zip   = $('#sh_zip');
        const $sh_addr1 = $('#sh_addr1');
        const $sh_addr2 = $('#sh_addr2');
        let $sh_lat   = $('#sh_lat');
        let $sh_lng   = $('#sh_lng');

        const $sh_contents = $('#sh_contents');
        const $contents_count = $('#contents_count');

        const $bizFile = $('#biz_file');      // name="biz_file"
        const $bizPreview = $('#biz_preview');
        const $delBiz = $('#del_biz');

        const $wrapImgs = $('#shop_imgs_wrap');
        const $btnSave = $('#btn_store_save');

        // =========================
        // 상태값
        // =========================
        let SHOP_ID = 0;                  // sh_idx
        let BIZ_SERVER_NAME = '';         // DB에 저장된 사업자등록증 파일명(=rs_없음)
        let BIZ_NEW_FILE = null;          // 새로 선택한 사업자등록증 파일
        let BIZ_DELETED = false;

        /**
         * slots: 길이 MAX_IMGS, 앞에서부터 채워지는 구조
         * 각 slot은 null 또는
         *  - { kind:'server', name:'abc.jpg' }   // DB 저장값(=rs_없음)
         *  - { kind:'file',   file: File }
         */
        let slots = new Array(MAX_IMGS).fill(null);

        // 파일 선택을 재사용하기 위한 단일 picker
        let activePickIndex = -1;
        let $imgPicker = null;

        // =========================
        // 유틸
        // =========================
        const s = (v) => (v === null || v === undefined) ? '' : String(v);
        const trim = (v) => s(v).trim();

        function scrollToEl($el){
            try {
                const top = ($el.offset().top || 0) - 120;
                $('html, body').stop(true).animate({ scrollTop: top }, 150);
            } catch(e){}
        }

        function focusInvalid($el, msg){
            FS.setInvalid($el, msg);
            scrollToEl($el);
            try { $el.focus(); } catch(e){}
        }

        function setVal($el, v){
            if (!$el || !$el.length) return;
            $el.val(s(v));
        }

        function getVal($el){
            return ($el && $el.length) ? trim($el.val()) : '';
        }

        // ✅ 매장 이미지 URL: 표시용은 rs_ 붙여야 함
        function shopImgUrl(fileNameNoRs){
            const fn = trim(fileNameNoRs);
            if (!fn) return '';
            // 이미 절대 URL이면 그대로
            if (/^https?:\/\//i.test(fn)) return fn;
            // 표시용은 rs_ + 파일명
            return DATA_BASE + '/data/shop/' + SHOP_ID + '/rs_' + fn;
        }

        // ✅ 사업자등록증 URL: rs_ 붙이지 않음
        function bizFileUrl(fileNameNoRs){
            const fn = trim(fileNameNoRs);
            if (!fn) return '';
            if (/^https?:\/\//i.test(fn)) return fn;
            return DATA_BASE + '/data/shop/' + SHOP_ID + '/' + fn;
        }

        function revokeObjUrl(url){
            try { URL.revokeObjectURL(url); } catch(e){}
        }

        function countFilledSlots(){
            return slots.filter(Boolean).length;
        }

        // ✅ 핵심: 중간 삭제하면 앞으로 땡김(압축)
        function compressSlots(){
            const filled = slots.filter(Boolean);        // 순서 유지
            slots = filled.concat(new Array(MAX_IMGS - filled.length).fill(null));
        }

        // 이미지 슬롯 렌더링
        function ensurePicker(){
            if ($imgPicker && $imgPicker.length) return;
            $imgPicker = $('<input type="file" accept="image/*" class="d-none" />');
            $imgPicker.attr('id', 'shop_img_picker');
            $form.append($imgPicker);

            $imgPicker.on('change', function(){
                const f = this.files && this.files[0] ? this.files[0] : null;
                if (!f) return;

                if (activePickIndex < 0 || activePickIndex >= MAX_IMGS) return;

                slots[activePickIndex] = { kind:'file', file: f };
                compressSlots();          // ✅ 항상 앞에서부터 채우는 구조 유지
                renderSlots();

                // picker 초기화
                this.value = '';
                activePickIndex = -1;
            });
        }

        function slotPreview(i){
            const it = slots[i];
            if (!it) return '';
            if (it.kind === 'server') return shopImgUrl(it.name);
            if (it.kind === 'file') {
                try { return URL.createObjectURL(it.file); } catch(e){ return ''; }
            }
            return '';
        }

        function renderSlots(){
            ensurePicker();

            // ✅ UI는 "채워진 수 + 1" 만큼만 보여주되 최대 5
            const filled = countFilledSlots();
            const visible = Math.min(Math.max(filled + 1, 1), MAX_IMGS);

            let html = '';
            for (let i=0; i<visible; i++){
                const n = i+1;
                const it = slots[i];
                const url = it ? slotPreview(i) : '';
                const isOn = !!url;

                html += `
        <div class="image_upload ${isOn ? 'on' : ''}" data-slot="${i}">
          <label class="upload_box js-slot-pick" style="cursor:pointer;">
            <div class="rect">
              ${isOn ? `<img src="${url}" style="width:100%;height:100%;object-fit:cover;">` : ``}
            </div>
            <p class="max_img">사진 ${n}/${MAX_IMGS}</p>
          </label>
          <button type="button" class="btn upload_del js-slot-del" data-slot="${i}" ${isOn ? '' : 'style="display:none;"'}>
            <img src="<?=DESIGN_HTTP?>/market/img/img_del.png" alt="">
          </button>
        </div>
      `;
            }

            $wrapImgs.html(html);

            // file preview로 만든 objectURL은 렌더 때마다 새로 생길 수 있으니
            // 엄격하게 관리하려면 캐싱/리스트로 revoke 처리 필요.
            // (여기서는 큰 용량/빈번 렌더 아니라면 실사용 문제 거의 없음)
        }

        // =========================
        // 슬롯 이벤트
        // =========================
        function bindSlotEvents(){
            // pick
            $(document).on('click', '.js-slot-pick', function(e){
                e.preventDefault();
                const $slot = $(this).closest('.image_upload');
                const idx = parseInt($slot.data('slot'), 10);
                if (isNaN(idx)) return;

                activePickIndex = idx;
                $imgPicker.trigger('click');
            });

            // delete
            $(document).on('click', '.js-slot-del', function(e){
                e.preventDefault();
                e.stopPropagation();

                const idx = parseInt($(this).data('slot'), 10);
                if (isNaN(idx)) return;

                // ✅ 해당 슬롯 제거 후 압축(앞으로 땡김)
                slots[idx] = null;
                compressSlots();
                renderSlots();
            });
        }

        // =========================
        // 사업자등록증 미리보기/삭제
        // =========================
        function renderBiz(){
            // 서버 파일이 있고 삭제 아니고 새 파일 없으면 서버 미리보기
            if (BIZ_SERVER_NAME && !BIZ_DELETED && !BIZ_NEW_FILE){
                const url = bizFileUrl(BIZ_SERVER_NAME);
                if ($bizPreview && $bizPreview.length){
                    $bizPreview.attr('src', url).show();
                }
                $('#biz_wrap').addClass('on');
                $('.upload_del[data-del="biz"]').show();
                $delBiz.val('N');
                return;
            }

            // 새 파일
            if (BIZ_NEW_FILE){
                const isImg = (BIZ_NEW_FILE.type || '').indexOf('image/') === 0;
                if ($bizPreview && $bizPreview.length){
                    if (isImg){
                        const reader = new FileReader();
                        reader.onload = (ev) => {
                            $bizPreview.attr('src', ev.target.result).show();
                        };
                        reader.readAsDataURL(BIZ_NEW_FILE);
                    } else {
                        // pdf 등: 이미지 대신 숨김 처리(원하면 파일명 출력 UI 추가)
                        $bizPreview.attr('src','').hide();
                    }
                }
                $('#biz_wrap').addClass('on');
                $('.upload_del[data-del="biz"]').show();
                $delBiz.val('N');
                return;
            }

            // 없음
            $('#biz_wrap').removeClass('on');
            if ($bizPreview && $bizPreview.length) $bizPreview.attr('src','').hide();
            $('.upload_del[data-del="biz"]').hide();
        }

        function bindBizEvents(){
            $bizFile.on('change', function(){
                const f = this.files && this.files[0] ? this.files[0] : null;
                if (!f) return;

                BIZ_NEW_FILE = f;
                BIZ_DELETED = false;
                $delBiz.val('N');
                renderBiz();
            });

            $(document).on('click', '.upload_del[data-del="biz"]', function(e){
                e.preventDefault();
                e.stopPropagation();

                // ✅ 기존/신규 모두 삭제 처리
                BIZ_NEW_FILE = null;
                BIZ_DELETED = true;
                $delBiz.val('Y');

                try { $bizFile.val(''); } catch(e){}
                renderBiz();
            });
        }

        // =========================
        // 좌표(주소검색) - join 방식 그대로
        // =========================
        let kakaoGeocoder = null;

        function initGeocoder(){
            if (window.kakao && kakao.maps && kakao.maps.services){
                kakaoGeocoder = new kakao.maps.services.Geocoder();
            }
        }

        function openPostcode(){
            if (!window.daum || !daum.Postcode) return;

            new daum.Postcode({
                oncomplete: function(data){
                    let addr = data.roadAddress;
                    if (!addr) addr = data.jibunAddress;

                    setVal($sh_zip, data.zonecode || '');
                    setVal($sh_addr1, addr || '');
                    setVal($sh_addr2, '');
                    try { $sh_addr2.focus(); } catch(e){}

                    FS.setValid($sh_zip, '확인되었습니다.');
                    FS.setValid($sh_addr1, '확인되었습니다.');
                    FS.clearState($sh_addr2, '상세주소를 입력해주세요.');

                    // 좌표 변환
                    initGeocoder();
                    if (!kakaoGeocoder || !addr){
                        setVal($sh_lat, '');
                        setVal($sh_lng, '');
                        return;
                    }

                    kakaoGeocoder.addressSearch(addr, function(result, status){
                        if (status === kakao.maps.services.Status.OK && result && result[0]){
                            setVal($sh_lat, result[0].y || '');
                            setVal($sh_lng, result[0].x || '');
                        } else {
                            setVal($sh_lat, '');
                            setVal($sh_lng, '');
                        }
                    });
                }
            }).open();
        }

        // =========================
        // 유효성(회원가입 스타일)
        // =========================
        FS.bindOnlyNumber('#sh_biz_no');
        FS.bindOnlyNumber('#sh_tel');

        function bindOkLive($el, emptyMsg){
            if (!$el || !$el.length) return;
            $el.on('input', function(){
                if (getVal($el)) FS.setValid($el, '확인되었습니다.');
                else FS.clearState($el, emptyMsg || '');
            });
        }

        bindOkLive($sh_corp_nm, '상호(법인명)을 입력해 주세요.');
        bindOkLive($sh_biz_no,  '사업자등록번호를 입력해 주세요.');
        bindOkLive($sh_title,   '매장명을 입력해 주세요.');
        bindOkLive($sh_tel,     '연락처를 입력해 주세요.');
        bindOkLive($sh_ceo_nm,  '대표자명을 입력해 주세요.');
        bindOkLive($sh_addr2,   '상세주소를 입력해 주세요.');

        function validateAll(){
            if (!getVal($sh_corp_nm)) { focusInvalid($sh_corp_nm, '상호(법인명)을 입력해 주세요.'); return false; }
            if (!getVal($sh_biz_no))  { focusInvalid($sh_biz_no,  '사업자등록번호를 입력해 주세요.'); return false; }
            if (!getVal($sh_title))   { focusInvalid($sh_title,   '매장명을 입력해 주세요.'); return false; }
            if (!getVal($sh_tel))     { focusInvalid($sh_tel,     '연락처를 입력해 주세요.'); return false; }
            if (!getVal($sh_ceo_nm))  { focusInvalid($sh_ceo_nm,  '대표자명을 입력해 주세요.'); return false; }

            if (!getVal($sh_zip))     { focusInvalid($sh_zip,     '우편번호 검색을 진행해 주세요.'); return false; }
            if (!getVal($sh_addr1))   { focusInvalid($sh_addr1,   '주소를 입력해 주세요.'); return false; }
            if (!getVal($sh_addr2))   { focusInvalid($sh_addr2,   '상세주소를 입력해 주세요.'); return false; }

            // 좌표는 주소검색 기반이면 보통 필수
            if (!getVal($sh_lat) || !getVal($sh_lng)){
                focusInvalid($sh_addr1, '좌표 변환에 실패했습니다. 주소를 다시 선택해 주세요.');
                return false;
            }

            // 사업자등록증: 기존이 없고, 새파일도 없고, 삭제표시라면 막기
            // const hasBiz = (BIZ_SERVER_NAME && !BIZ_DELETED) || !!BIZ_NEW_FILE;
            // if (!hasBiz){
            //     focusInvalid($bizFile, '사업자등록증 파일을 첨부해 주세요.');
            //     return false;
            // }
            //
            // // 매장 이미지 최소 3장: "서버 남은 + 새 업로드" 포함
            // const imgCnt = countFilledSlots();
            // if (imgCnt < MIN_IMGS){
            //     scrollToEl($wrapImgs);
            //     ModalUtil.alert({
            //         title: '알림',
            //         message: '매장 이미지는 최소 ' + MIN_IMGS + '장 이상\n 등록해 주세요.',
            //         okText: '확인',
            //         onOk: function () {
            //         },
            //     });
            //     return false;
            // }

            return true;
        }

        // 소개 글자수
        function bindContentsCount(){
            $(document).on('input', '#sh_contents', function(){
                const v = s($(this).val());
                const max = 500;
                if (v.length > max) $(this).val(v.substring(0, max));
                const nowLen = Math.min($(this).val().length, max);
                if ($contents_count && $contents_count.length){
                    $contents_count.text('(' + nowLen + '/' + max + ')');
                }
            });
        }

        // =========================
        // store_get -> 폼 채우기
        // =========================
        function fillForm(d){
            SHOP_ID = parseInt(d.idx || d.sh_idx || 0, 10) || 0;

            setVal($sh_corp_nm, d.sh_corp_nm);
            setVal($sh_biz_no,  d.sh_biz_no);
            setVal($sh_title,   d.sh_title);
            setVal($sh_tel,     d.sh_tel);
            setVal($sh_ceo_nm,  d.sh_ceo_nm);

            setVal($sh_zip,   d.sh_zip);
            setVal($sh_addr1, d.sh_addr1);
            setVal($sh_addr2, d.sh_addr2);

            setVal($sh_lat, d.sh_lat);
            setVal($sh_lng, d.sh_lng);

            setVal($sh_contents, d.sh_contents);
            if ($sh_contents && $sh_contents.length) $sh_contents.trigger('input');

            // 사업자등록증 (DB 저장값은 rs_없음)
            BIZ_SERVER_NAME = trim(d.sh_biz_file);
            BIZ_NEW_FILE = null;
            BIZ_DELETED = false;
            $delBiz.val('N');
            try { $bizFile.val(''); } catch(e){}
            renderBiz();

            // 매장 이미지 1~5 (DB 저장값은 rs_없음)
            const names = [
                trim(d.sh_img1), trim(d.sh_img2), trim(d.sh_img3), trim(d.sh_img4), trim(d.sh_img5)
            ];

            // slots 초기화: 서버값을 앞에서부터 채움(중간이 비어있어도 압축)
            slots = new Array(MAX_IMGS).fill(null);
            let p = 0;
            for (let i=0; i<MAX_IMGS; i++){
                if (names[i]) {
                    slots[p++] = { kind:'server', name: names[i] };
                }
            }
            compressSlots();
            renderSlots();
        }

        function fetchStore(){
            $.ajax({
                url: API_URL,
                type: 'POST',
                dataType: 'json',
                data: { act:'store_get' },
                success: function(res){
                    if (!res || !res.success){
                        alert((res && res.message) ? res.message : '매장 정보를 불러오지 못했습니다.');
                        return;
                    }
                    fillForm(res.data || {});
                },
                error: function(xhr){
                    console.log(xhr.responseText);
                    alert('서버 통신 오류');
                }
            });
        }

        // =========================
        // 저장(store_update)
        //  - 핵심: slots(서버+신규)를 "1~5로 재패킹"해서 전송
        //  - keep_img1~5 : 서버에 남길 파일명(=rs_없음)
        //  - shop_img1~5 : 새 업로드 file
        //  - del_img1~5  : 비워진 인덱스는 Y
        // =========================
        function submitStore(){
            if (!validateAll()) return;

            // 항상 압축해서 1..N 채움 상태로
            compressSlots();

            const fd = new FormData();
            fd.append('act', 'store_update');

            // 기본 텍스트
            fd.append('sh_corp_nm', getVal($sh_corp_nm));
            fd.append('sh_biz_no',  getVal($sh_biz_no));
            fd.append('sh_title',   getVal($sh_title));
            fd.append('sh_tel',     getVal($sh_tel));
            fd.append('sh_ceo_nm',  getVal($sh_ceo_nm));

            fd.append('sh_zip',   getVal($sh_zip));
            fd.append('sh_addr1', getVal($sh_addr1));
            fd.append('sh_addr2', getVal($sh_addr2));

            fd.append('sh_lat', getVal($sh_lat));
            fd.append('sh_lng', getVal($sh_lng));

            fd.append('sh_contents', getVal($sh_contents));

            // 사업자등록증
            // ✅ 서버에 남길 파일명은 rs_ 없는 DB값 그대로
            fd.append('keep_biz', (BIZ_SERVER_NAME && !BIZ_DELETED) ? BIZ_SERVER_NAME : '');
            fd.append('del_biz',  (BIZ_DELETED && !BIZ_NEW_FILE) ? 'Y' : 'N');
            if (BIZ_NEW_FILE) fd.append('biz_file', BIZ_NEW_FILE);

            // 매장 이미지 1~5
            for (let i=0; i<MAX_IMGS; i++){
                const idx = i+1;
                const it = slots[i];

                // 기본값
                fd.append('keep_img' + idx, '');
                fd.append('del_img'  + idx, 'Y');   // 비면 삭제로

                if (!it) continue;

                if (it.kind === 'server'){
                    // ✅ keep_imgN에 "DB 파일명(rs_없음)" 저장
                    fd.set('keep_img' + idx, it.name);
                    fd.set('del_img'  + idx, 'N');
                } else if (it.kind === 'file'){
                    fd.set('del_img' + idx, 'N');
                    fd.append('shop_img' + idx, it.file);
                }
            }

            $.ajax({
                url: API_URL,
                type: 'POST',
                data: fd,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(res){
                    if (!res || !res.success){
                        alert((res && res.message) ? res.message : '저장 실패');
                        return;
                    }
                    ModalUtil.alert({
                        title: '알림',
                        message: res.message || '저장되었습니다.',
                        okText: '확인',
                        onOk: function () {
                        },
                    });
                    fetchStore(); // 저장 후 서버 기준으로 다시 로드 (썸네일/DB 동기화)
                },
                error: function(xhr){
                    console.log(xhr.responseText);
                    alert('서버 통신 오류');
                }
            });
        }

        // =========================
        // 초기 바인딩
        // =========================
        $(function(){

            // ✅ 좌표 input이 없으면 자동 생성 (혹시 퍼블에 누락된 경우)
            if (!$sh_lat.length) $form.append('<input type="hidden" id="sh_lat" name="sh_lat" value="">');
            if (!$sh_lng.length) $form.append('<input type="hidden" id="sh_lng" name="sh_lng" value="">');

            $sh_lat = $('#sh_lat');
            $sh_lng = $('#sh_lng');

            initGeocoder();
            bindSlotEvents();
            bindBizEvents();
            bindContentsCount();

            // 최초 렌더(빈 슬롯 1개)
            renderSlots();
            renderBiz();

            // 주소 검색 버튼
            $('#btn_zip_search').on('click', function(e){
                e.preventDefault();
                openPostcode();
            });

            // 저장 버튼
            $btnSave.on('click', function(){
                submitStore();
            });

            // 초기 데이터 로드
            fetchStore();
        });

    })();
</script>

<? include_once("./inc/tail.php"); ?>
