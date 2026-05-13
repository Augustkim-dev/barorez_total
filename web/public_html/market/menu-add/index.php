<?
$_SUB_HEAD_TITLE = "메뉴추가";
$_GET['hd_pc'] = ' ';
$hd_num = 'menu';
$hd_num2 = 'menu';
$hd_left = ' ';
include_once("../inc/head.php");
include_once("../inc/header.php");
include_once("../inc/modal.php");
?>

    <!-- 왼쪽 메뉴-->
<? include_once("../inc/left_menu.php"); ?>

    <div class="sub_pg ">
        <div class="sub_wr">
            <div class="hd_tit2 fs_16 flex-row">
                <h2 class="tit_st1 d-flex align-items-center mr-5 ">
                    <a href="#" onclick="history.back(); return false;" class="mr-4 line_h0 ">
                        <img src="<?=DESIGN_HTTP?>/market/img/ico_back.svg" alt=" 뒤로가기">
                    </a>
                    <span id="page_title">새 메뉴 추가</span>
                </h2>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-8">
                            <div class="form_wr">
                                <div class="ip_tit required">
                                    <h5>메뉴명</h5>
                                </div>
                                <input type="text" class="form-control" id="menu_title" placeholder="메뉴명 입력">
                                <div class="form-text ip_invalid">메뉴명을 입력해주세요</div>
                            </div>
                        </div>
                        <div class="col-4 align-items-end d-flex justify-content-around">
                            <div class="custom-control custom-switch switch-outside">
                                <span class="switch-state"></span>
                                <input type="checkbox"
                                       class="custom-control-input"
                                       id="customSwitch_menu1"
                                       data-on="판매가능"
                                       data-off="미판매"
                                       checked>
                                <label class="custom-control-label" for="customSwitch_menu1" id="customSwitch_menu1_label"></label>
                            </div>
                            <div class="custom-control custom-switch switch-outside">
                                <span class="switch-state"></span>
                                <input type="checkbox"
                                       class="custom-control-input"
                                       id="customSwitch_menu2"
                                       data-on="추천"
                                       data-off="비추천"
                                >
                                <label class="custom-control-label" for="customSwitch_menu2" id="customSwitch_menu2_label"></label>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-5">
                        <div class="col-md-6">
                            <div class="form_wr ip_valid">
                                <div class="ip_tit required">
                                    <h5>카테고리</h5>
                                </div>
                                <div class="custom-sel" id="category_select_wrapper">
                                    <button type="button" class="select-trigger" id="category_trigger">
                                        카테고리 선택
                                    </button>

                                    <ul class="select-options" id="category_options">
                                        <!-- 동적으로 채워짐 -->
                                    </ul>

                                    <input type="hidden" name="sc_idx" id="selected_sc_idx">
                                </div>
                                <div class="form-text ip_invalid">카테고리를 선택해주세요</div>
                            </div>

                            <div class="form_wr mt-5 ip_valid">
                                <div class="ip_tit required">
                                    <h5>가격(원)</h5>
                                </div>
                                <input type="text" class="form-control" id="menu_price" placeholder="0">
                                <div class="form-text ip_invalid">가격을 입력해주세요</div>
                            </div>

                            <div class="form_wr mt-5 ip_valid">
                                <div class="ip_tit required">
                                    <h5>메뉴 설명</h5>
                                </div>
                                <textarea class="form-control" id="menu_description" placeholder="메뉴 소개하는 문구를 간략하게 입력하세요" rows="3" style="min-height: 10rem;"></textarea>
                                <p class="text-right mt-2 tg_500 fs_14">(0/100)</p>
                                <div class="form-text ip_invalid">설명을 입력해주세요</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form_wr mt-5 ip_valid">
                                <div class="ip_tit">
                                    <h5>메뉴 이미지(1장)</h5>
                                </div>
                                <div class="imgup_wp">
                                    <div class="image_upload">
                                        <input id="menu_image_file" type="file" class="d-none" accept="image/*">
                                        <label for="menu_image_file" class="upload_box">
                                            <div class="rect" id="menu_image_preview"></div>
                                            <p class="max_img">0/1</p>
                                        </label>
                                        <button type="button" class="btn upload_del" id="menu_image_delete">
                                            <img src="<?=DESIGN_HTTP?>/market/img/img_del.png">
                                        </button>
                                    </div>
                                    <p class="fs_16 text-left mt-4 line_h1_4">
                                        JPG/PNG 권장됩니다.<br>
                                        이미지 규격 1:1비율로 1:1비율의 사진이 아닐 경우 이미지가 잘릴 수 있습니다.<br>
                                        추천사이즈는 가로 800px 세로 800px 사이즈 입니다.
                                    </p>
                                </div>
                            </div>

                            <div class="mt-5">
                                <div class="checks">
                                    <label>
                                        <input type="checkbox" id="is_adult_check">
                                        <span class="ic_box"></span>
                                        <div class="chk_p fs_20">
                                            <p>19세 이상 판매 품목일 경우 체크해주세요</p>
                                        </div>
                                    </label>
                                </div>
                                <p class="alim_txt fs_16 line_h1_4">
                                    주류 등 19세 이상 판매 품목은 청소년보호법에 따라 성인 여부 확인이 필수입니다. 매장 방문 시 신분증을 확인해 주시기 바라며, 미확인으로 인한 법적 책임은 가맹점에 귀속됩니다.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-5">
                        <div>
                            <h3 class="tit_st2">옵션 설정</h3>
                            <p class="tg_500 mt-1">옵션을 카테고리별로 그룹화하여 관리합니다</p>
                        </div>
                        <button type="button" class="btn btn-dark" id="add_option_category">+ 옵션 카테고리 추가</button>
                    </div>

                    <div id="option_categories_container">
                        <!-- 동적으로 추가되는 옵션 카테고리 -->
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-center mt_40 btn_group" id="button_group">
                <button type="button" class="btn btn-primary btn-lg btn-w1" id="submit_new">등록하기</button>

                <button type="button" class="btn btn-outline-danger btn-lg btn-w2" id="delete_menu" style="display:none;">
                    메뉴 삭제
                </button>
                <button type="button" class="btn btn-primary btn-lg btn-w2" id="submit_update" style="display:none;">
                    수정 완료
                </button>
            </div>
        </div>
    </div>

<script>
    $(document).ready(function() {
        const urlParams = new URLSearchParams(window.location.search);
        const menuIdx = parseInt(urlParams.get('idx')) || 0;
        const isEditMode = menuIdx > 0;

        let uploadedImageFile = null;      // 새로 선택한 파일 (등록/수정 시 전송용)
        let existingImageName = '';        // 수정 모드 기존 이미지명 (삭제 시 ''로 초기화)

        // 타이틀 & 버튼 영역 제어
        if (isEditMode) {
            $('#page_title').text('메뉴 수정');
            $('#submit_new').hide();
            $('#delete_menu').show();
            $('#submit_update').show();
            loadMenuDetail(); // 수정 데이터 불러오기
        } else {
            $('#page_title').text('새 메뉴 추가');
            $('#submit_new').show();
            $('#delete_menu').hide();
            $('#submit_update').hide();
            loadCategories(); // 신규 등록 시 카테고리 로드
        }

        // ────────────────────────────────────────────────
        // 카테고리 목록 불러오기 & 선택 처리
        // ────────────────────────────────────────────────
        function loadCategories(selectedScIdx = null) {
            $.ajax({
                url: './update.php',
                type: 'POST',
                dataType: 'json',
                data: { act: 'get_categories' },
                success: function(res) {
                    const $options = $('#category_options');
                    $options.empty();

                    if (res.success && res.categories && res.categories.length > 0) {
                        res.categories.forEach(cat => {
                            $options.append(`
                            <li data-value="${cat.idx}">${cat.title}</li>
                        `);
                        });

                        $options.find('li').on('click', function() {
                            const value = $(this).data('value');
                            const text  = $(this).text();
                            $('#category_trigger').text(text);
                            $('#selected_sc_idx').val(value);
                            $('#category_select_wrapper').removeClass('active');
                        });

                        // 수정 모드에서 선택된 카테고리 반영
                        if (selectedScIdx) {
                            const $selected = $options.find(`li[data-value="${selectedScIdx}"]`);
                            if ($selected.length) {
                                $('#category_trigger').text($selected.text());
                                $('#selected_sc_idx').val(selectedScIdx);
                            }
                        }
                    } else {
                        $options.append('<li>등록된 카테고리가 없습니다.</li>');
                    }
                },
                error: function() {
                    $('#category_options').html('<li>카테고리 불러오기 실패</li>');
                }
            });
        }

        // 카테고리 셀렉트 열기/닫기
        $('#category_trigger').on('click', function(e) {
            e.stopPropagation();
            $('#category_select_wrapper').toggleClass('active');
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('#category_select_wrapper').length) {
                $('#category_select_wrapper').removeClass('active');
            }
        });

        // ────────────────────────────────────────────────
        // 이미지 미리보기 & 삭제 처리
        // ────────────────────────────────────────────────
        $('#menu_image_file').on('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            uploadedImageFile = file;

            const reader = new FileReader();
            reader.onload = function(ev) {
                $('#menu_image_preview').html(`
                <img src="${ev.target.result}" style="width:100%; height:100%; object-fit:cover; border-radius:8px;">
            `);
                $('.max_img').text('1/1');
                $('.upload_del').show();
            };
            reader.readAsDataURL(file);
        });

        $('#menu_image_delete').on('click', function() {
            $('#menu_image_file').val('');
            $('#menu_image_preview').empty();
            $('.max_img').text('0/1');
            uploadedImageFile = null;
            existingImageName = ''; // 삭제 의사 표시
        });

        // ────────────────────────────────────────────────
        // 옵션 카테고리 추가 / 삭제 / 항목 추가 / 삭제
        // ────────────────────────────────────────────────
        let optionCategoryCounter = 0;

        $('#add_option_category').on('click', function() {
            optionCategoryCounter++;
            const html = `
            <section class="memu_opt container-fluid" data-oc-index="${optionCategoryCounter}">
                <div class="row">
                    <div class="col-lg-4 bg-light col-12">
                        <div class="d-flex align-items-center justify-content-between">
                            <h4 class="tit_st3">옵션 ${optionCategoryCounter}</h4>
                            <a href="#" class="tg_500 remove-option-category">
                                <img src="<?=DESIGN_HTTP?>/market/img/ico_delete2.svg" alt="삭제" style="width:2.8rem">삭제
                            </a>
                        </div>
                        <div class="form_wr mt-5">
                            <div class="ip_tit required"><h5>옵션 카테고리명</h5></div>
                            <input type="text" class="form-control oc_title" placeholder="입력하세요">
                        </div>
                        <div class="form_wr mt-5">
                            <div class="ip_tit required"><h5>최대 선택 개수</h5></div>
                            <input type="number" class="form-control oc_max_select" placeholder="1" value="1">
                        </div>
                        <div class="checks mt-4">
                            <label>
                                <input type="checkbox" class="oc_is_required" checked>
                                <span class="ic_box"></span>
                                <div class="chk_p fs_20"><p>필수 선택</p></div>
                            </label>
                        </div>
                    </div>
                    <div class="col-lg-8 col-12">
                        <div class="form_wr">
                            <div class="ip_tit"><h5>옵션 항목</h5></div>
                        </div>
                        <div class="option-items"></div>
                        <button type="button" class="btn btn-outline-secondary btn-block add-option-item">+ 옵션 추가</button>
                    </div>
                </div>
            </section>
        `;
            $('#option_categories_container').append(html);
        });

        // 옵션 카테고리 삭제
        $(document).on('click', '.remove-option-category', function(e) {
            e.preventDefault();
            if (confirm('이 옵션 카테고리와 하위 모든 옵션을 삭제하시겠습니까?\n되돌릴 수 없습니다.')) {
                $(this).closest('.memu_opt').remove();
            }
        });

        // 옵션 항목 추가
        $(document).on('click', '.add-option-item', function() {
            const html = `
            <div class="d-flex memu_opt2 mt-2">
                <input type="text" class="form-control om_title" placeholder="예 : 레귤러, 라지">
                <div class="input_txt">
                    <span>원</span>
                    <input type="number" class="form-control om_price" placeholder="0" value="0">
                </div>
                <a href="#" class="ml-4 flex-shrink-0 remove-option-item">
                    <img src="<?=DESIGN_HTTP?>/market/img/ico_close.svg" alt="삭제">
                </a>
            </div>
        `;
            $(this).prev('.option-items').append(html);
        });

        // 옵션 항목 삭제
        $(document).on('click', '.remove-option-item', function(e) {
            e.preventDefault();
            $(this).closest('.memu_opt2').remove();
        });

        // ────────────────────────────────────────────────
        // 등록 / 수정 실행 (신규 & 수정 공통)
        // ────────────────────────────────────────────────
        function submitForm() {
            const formData = new FormData();

            formData.append('act', isEditMode ? 'update_menu_with_options' : 'add_menu_with_options');
            if (isEditMode) {
                formData.append('menu_idx', menuIdx);
            }

            formData.append('sc_idx', $('#selected_sc_idx').val() || '');
            formData.append('sm_title', $('#menu_title').val().trim());
            formData.append('sm_price', parseInt($('#menu_price').val().replace(/[^0-9]/g, '')) || 0);
            formData.append('sm_contents', $('#menu_description').val().trim());
            formData.append('sm_type', $('#customSwitch_menu1').is(':checked') ? 'Y' : 'N');
            formData.append('sm_main', $('#customSwitch_menu2').is(':checked') ? 'Y' : 'N');
            formData.append('is_adult', $('#is_adult_check').is(':checked') ? 'Y' : 'N');

            // 이미지 파일 (새로 선택된 경우만)
            const imageFile = $('#menu_image_file')[0].files[0];
            if (imageFile) {
                formData.append('menu_image', imageFile);
            } else if (isEditMode && existingImageName) {
                formData.append('existing_image', existingImageName); // 기존 이미지 유지
            } else {
                formData.append('existing_image', ''); // 이미지 삭제 의사
            }

            // 옵션 카테고리 수집
            const optionCategories = [];
            $('.memu_opt').each(function() {
                const $section = $(this);
                const title = $section.find('.oc_title').val().trim();
                if (!title) return;

                const oc = {
                    title: title,
                    required: $section.find('.oc_is_required').is(':checked') ? 'Y' : 'N',
                    max_select: parseInt($section.find('.oc_max_select').val()) || 1,
                    options: []
                };

                $section.find('.memu_opt2').each(function() {
                    const om_title = $(this).find('.om_title').val().trim();
                    const om_price = parseInt($(this).find('.om_price').val()) || 0;
                    if (om_title) {
                        oc.options.push({ title: om_title, price: om_price });
                    }
                });

                if (oc.options.length > 0) {
                    optionCategories.push(oc);
                }
            });

            formData.append('option_categories', JSON.stringify(optionCategories));

            // 유효성 검사
            if (!formData.get('sc_idx')) {
                ModalUtil.alert({
                    title: '메뉴',
                    message: '카테고리를 선택해주세요.',
                    okText: '확인',
                    onOk: function () {
                    },
                });
                return;
            }
            if (!formData.get('sm_title')) {
                ModalUtil.alert({
                    title: '메뉴',
                    message: '메뉴명을 입력해주세요.',
                    okText: '확인',
                    onOk: function () {
                    },
                });
                return;
            }
            if (parseInt(formData.get('sm_price')) <= 0) {
                ModalUtil.alert({
                    title: '메뉴',
                    message: '가격을 올바르게 입력해주세요.',
                    okText: '확인',
                    onOk: function () {
                    },
                });
                return;
            }

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
                            title: '메뉴',
                            message: res.message,
                            okText: '확인',
                            onOk: function () {
                                location.href = '../menu';
                            },
                        });
                    } else {
                        alert(res.message || (isEditMode ? '수정' : '등록') + '에 실패했습니다.');
                    }
                },
                error: function() {
                    alert('서버와의 연결에 문제가 발생했습니다.');
                }
            });
        }

        // 등록하기 버튼 (신규)
        $('#submit_new').on('click', submitForm);

        // 수정 완료 버튼 (수정)
        $('#submit_update').on('click', submitForm);

        // ────────────────────────────────────────────────
        // 메뉴 삭제 기능 (수정 모드에서만)
        // ────────────────────────────────────────────────
        $('#delete_menu').on('click', function() {
            ModalUtil.confirm({
                title: '메뉴',
                message: '해당 메뉴를 정말 삭제하시겠습니까?',
                okText: '확인',
                cancelText: '취소',
                onOk: function () {
                    $.ajax({
                        url: './update.php',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            act: 'delete_menu',
                            menu_idx: menuIdx
                        },
                        success: function(res) {
                            if (res.success) {
                                location.href = '../menu';
                            } else {
                                alert(res.message || '삭제에 실패했습니다.');
                            }
                        },
                        error: function() {
                            alert('서버 연결 오류');
                        }
                    });
                },
                onCancel: function (){
                    return false;
                }
            });
        });

        function syncSwitchLabel($input) {
            const isChecked = $input.is(':checked');
            const onText = $input.data('on') || 'ON';
            const offText = $input.data('off') || 'OFF';
            const text = isChecked ? onText : offText;

            const $wrap = $input.closest('.custom-switch');
            $wrap.find('.switch-state').text(text);

            // label은 스위치 UI용으로 두고, 접근성용 속성만 보조로 넣음
            $wrap.find(`label[for="${$input.attr('id')}"]`).attr('aria-label', text);
        }

        function toChecked(value) {
            return value === true || value === 'Y' || value === '1' || value === 1;
        }

        $('#customSwitch_menu1, #customSwitch_menu2').on('change', function() {
            syncSwitchLabel($(this));
        });

        syncSwitchLabel($('#customSwitch_menu1'));
        syncSwitchLabel($('#customSwitch_menu2'));
        // ────────────────────────────────────────────────
        // 수정 모드 전용: 데이터 불러오기
        // ────────────────────────────────────────────────
        function loadMenuDetail() {
            $.ajax({
                url: './update.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    act: 'get_menu_detail',
                    menu_idx: menuIdx
                },
                success: function(res) {
                    if (!res.success) {
                        alert(res.message || '메뉴 정보를 불러올 수 없습니다.');
                        history.back();
                        return;
                    }

                    const m = res.menu;

                    $('#menu_title').val(m.title);
                    $('#menu_price').val(m.price);
                    $('#menu_description').val(m.contents);
                    const isSale = toChecked(m.is_sale);
                    $('#customSwitch_menu1').prop('checked', isSale);
                    syncSwitchLabel($('#customSwitch_menu1'));

                    const isMain = toChecked(m.is_main);
                    $('#customSwitch_menu2').prop('checked', isMain);
                    syncSwitchLabel($('#customSwitch_menu2'));
                    $('#is_adult_check').prop('checked', m.is_adult);
                    $('#selected_sc_idx').val(m.sc_idx);

                    // 기존 이미지
                    if (m.image) {
                        existingImageName = m.image;
                        $('#menu_image_preview').html(`
                        <img src="/data/menu/${m.image}" style="width:100%; height:100%; object-fit:cover; border-radius:8px;">
                    `);
                        $('.max_img').text('1/1');
                        $('.upload_del').show();
                    }

                    // 옵션 카테고리 렌더링
                    if (res.option_categories && res.option_categories.length > 0) {
                        res.option_categories.forEach(function(oc) {
                            $('#add_option_category').trigger('click');
                            const $section = $('.memu_opt').last();
                            $section.find('.oc_title').val(oc.title);
                            $section.find('.oc_max_select').val(oc.max_select);
                            $section.find('.oc_is_required').prop('checked', oc.required);

                            oc.options.forEach(function(om) {
                                $section.find('.add-option-item').trigger('click');
                                const $item = $section.find('.memu_opt2').last();
                                $item.find('.om_title').val(om.title);
                                $item.find('.om_price').val(om.price);
                            });
                        });
                    }

                    // 카테고리 이름 표시
                    loadCategories(m.sc_idx);
                },
                error: function() {
                    alert('메뉴 데이터를 불러오는 중 오류가 발생했습니다.');
                    history.back();
                }
            });
        }
    });
</script>

<? include_once("../inc/tail.php"); ?>
