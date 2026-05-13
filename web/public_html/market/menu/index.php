<?php
$_SUB_HEAD_TITLE = "메뉴관리";
$_GET['hd_pc'] = ' '; //PC hd  로그인시공백 로그아웃시 logout
$hd_num = 'menu'; //왼쪽메뉴 active 땜시 만듬
$hd_left = ' '; //왼쪽메뉴 on 땜시 만듬
include_once("../inc/head.php");
include_once("../inc/header.php");
include_once("../inc/modal.php");
?>

    <!-- 왼쪽 메뉴-->
<?php include_once("../inc/left_menu.php"); ?>

    <div class="sub_pg ">
        <div class="sub_wr">
            <div class="hd_tit2">
                <div class="flex-shrink-0 ml-auto">
                    <button type="button" class="btn btn-outline-primary  " data-toggle="modal" data-target="#modal_menu1">카테고리 추가</button>
                    <button type="button" class="btn btn-outline-primary ml-2" onclick="location.href='../menu-add' ">메뉴 추가</button>
                </div>
                <div class="d-flex flex-wrap align-items-center ">
                    <h3 class="tit_st1 mr-5">메뉴관리</h3>
                    <div class="d-flex ">
                        <input type="text" class="form-control " placeholder="메뉴명 검색"> <button type="button" class="btn btn-secondary ml-2 ">검색</button>
                    </div>
                </div>

            </div>

            <div class="card rounded-lg">
                <div class="">
                    <div class="collapse_cate menu_tab ">
                        <div id="cate_cont" class="touch_scroll scroll_bar_none flex-fill">
                            <div class="btn-group btn-group-toggle px_40" data-toggle="buttons" id="category_tabs">
                                <label class="btn active" data-sc-idx="0">
                                    <input type="radio" name="category_option" id="category_all" value="0" checked autocomplete="off">
                                    전체(0)
                                </label>
                            </div>
                        </div>

                    </div>
                </div>
                <section class="menu_wp" id="menu_list_container">

                </section>
            </div>
        </div>

    </div>

    <!-- data-toggle="modal" data-target="#modal_menu1" F-2 메뉴카테고리 추가(모달) -->
    <div class="modal modal_rr fade" id="modal_menu1" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog ">
            <div class="modal-content">
                <button type="button" class="close1" data-dismiss="modal" aria-label="Close"><img src="<?=DESIGN_HTTP?>/market/img/ic_close.png" alt="닫기"></button>
                <div class="modal-body">

                    <div class=" detail_hd mt-4">
                        <h2 class="tit_st1 d-flex align-items-center"> <span>메뉴 카테고리 추가</span></h2>
                        <div class="custom-control custom-switch switch-outside swh_l">
                            <input type="checkbox"
                                   class="custom-control-input"
                                   id="customSwitch_mm1"
                                   data-on="사용"
                                   data-off="사용안함">
                            <span class="switch-state"></span>
                            <label class="custom-control-label" for="customSwitch_mm1"></label>
                        </div>
                    </div>
                    <section class="py-5 border-top border-dark">
                        <div class="row">
                            <div class="col-4 fw_600 mb-4 d-flex align-items-center">
                                카테고리명
                            </div>
                            <div class="col-8 mb-4">
                                <input type="text" class="form-control" id="cat_title_add" placeholder="예)메인 메뉴,추천 메뉴,대표 메뉴">
                            </div>
                            <div class="col-4 fw_600 mb-4 d-flex align-items-center">
                                정렬 순서
                            </div>
                            <div class="col-8 mb-4">
                                <input type="text" class="form-control" id="cat_order_add" placeholder="1">
                            </div>


                        </div>
                        <div><button type="button" class="btn btn-primary btn-lg btn-block mt-5">카테고리 추가</button></div>

                    </section>
                </div>

            </div>
        </div>
    </div>

    <!-- data-toggle="modal" data-target="#modal_menu1" F-2 메뉴카테고리 편집(모달) -->
    <div class="modal modal_rr fade" id="modal_menu2" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog ">
            <div class="modal-content">
                <button type="button" class="close1" data-dismiss="modal" aria-label="Close"><img src="<?=DESIGN_HTTP?>/market/img/ic_close.png" alt="닫기"></button>
                <div class="modal-body">

                    <div class=" detail_hd mt-4">
                        <h2 class="tit_st1 d-flex align-items-center"> <span>메뉴 카테고리 편집</span></h2>
                        <div class="custom-control custom-switch switch-outside swh_l">
                            <input type="checkbox"
                                   class="custom-control-input"
                                   id="customSwitch_mm1_edit"
                                   data-on="사용"
                                   data-off="사용안함">
                            <span class="switch-state"></span>
                            <label class="custom-control-label" for="customSwitch_mm1_edit"></label>
                        </div>
                    </div>
                    <section class="py-5 border-top border-dark">
                        <div class="row">
                            <div class="col-4 fw_600 mb-4 d-flex align-items-center">
                                카테고리명
                            </div>
                            <div class="col-8 mb-4">
                                <input type="text" class="form-control" id="cat_title_edit" placeholder="예)메인 메뉴,추천 메뉴,대표 메뉴" value="메인메뉴">
                            </div>
                            <div class="col-4 fw_600 mb-4 d-flex align-items-center">
                                정렬 순서
                            </div>
                            <div class="col-8 mb-4">
                                <input type="text" class="form-control" id="cat_order_edit" placeholder="1" value="2">
                            </div>


                        </div>
                        <div class="form-row mt-5">

                            <div class="col-6"><button type="button" class="btn btn-light btn-lg btn-block btn-menu-category-delete">삭제</button></div>
                            <div class="col-6"><button type="button" class="btn  btn-primary btn-lg btn-block btn-menu-category-edit">수정 완료</button></div>
                        </div>


                    </section>
                </div>

            </div>
        </div>
    </div>

<script>
    let currentCategoryId = 0;
    let currentKeyword = '';

    const $categoryTabs  = $('#category_tabs');
    const $menuContainer = $('#menu_list_container');
    const $searchInput   = $('.sub_pg input.form-control[placeholder="메뉴명 검색"]');
    const $searchBtn     = $searchInput.siblings('.btn');

    function loadCategories() {
        $.ajax({
            url: './update.php',
            type: 'POST',
            dataType: 'json',
            data: { act: 'menu_category_list' },
            success: function(res) {
                if (!res.success) {
                    alert(res.message || '카테고리 로드 실패');
                    return;
                }

                $categoryTabs.empty();

                // 전체 탭
                $categoryTabs.append(`
                <label class="btn active" data-sc-idx="0">
                    <input type="radio" name="category_option" value="0" checked autocomplete="off">
                    전체(${res.all_count})
                </label>
            `);

                res.categories.forEach(cat => {
                    $categoryTabs.append(`
                    <label class="btn" data-sc-idx="${cat.idx}">
                        <input type="radio" name="category_option" value="${cat.idx}" autocomplete="off">
                        ${cat.title}(${cat.menu_count})
                    </label>
                `);
                });

                $categoryTabs.find('label').on('click', function() {
                    $categoryTabs.find('label').removeClass('active');
                    $(this).addClass('active');
                    currentCategoryId = parseInt($(this).data('sc-idx')) || 0;
                    loadMenus();
                });
            }
        });
    }

    function loadMenus() {
        $.ajax({
            url: './update.php',
            type: 'POST',
            dataType: 'json',
            data: {
                act: 'menu_list_by_category',
                sc_idx: currentCategoryId,
                keyword: currentKeyword
            },
            success: function(res) {
                if (!res.success) {
                    $menuContainer.html('<p class="text-center py-5 text-danger">메뉴 목록을 불러올 수 없습니다.</p>');
                    return;
                }

                $menuContainer.empty();

                // 카테고리가 하나도 없을 때
                if (!res.categories || res.categories.length === 0) {
                    $menuContainer.html('<p class="text-center py-5">등록된 카테고리가 없습니다.</p>');
                    return;
                }

                // 각 카테고리 순회 (메뉴 0개여도 카테고리 자체는 표시)
                res.categories.forEach(function(cat) {
                    const catMenus = cat.menus || [];
                    const menuCount = catMenus.length;

                    let html = `
                    <div class="mu_list" data-sc-idx="${cat.idx}">
                        <div class="mu_hd">
                            <div class="d-flex align-items-center">
                                <p class="tit_st3 text-white">${cat.title}(${menuCount})</p>
                                <button type="button" class="btn text-white flex-shrink-0"
                                        data-toggle="modal" data-target="#modal_menu2"
                                        data-sc-idx="${cat.idx}">
                                    <span class="mr-2"><img src="<?=DESIGN_HTTP?>/market/img/ico_edit.svg" alt=" " class="icon_w"></span>편집
                                </button>
                            </div>
                            <p class="d-flex align-content-center mb-4 mb-lg-0">
                                <span><img src="<?=DESIGN_HTTP?>/market/img/img_mark2.svg" class="mr-2" alt=" "></span>
                                판매중이 아닐 경우 품절로 표시됩니다.
                            </p>
                        </div>
                `;

                    // 메뉴가 없을 때 메시지
                    if (menuCount === 0) {
                        html += `
                        <div class="mu_box text-center py-5 text-muted">
                            이 카테고리에 등록된 메뉴가 없습니다.
                        </div>
                    `;
                    }
                    // 메뉴가 있을 때 각 메뉴 렌더링
                    else {
                        catMenus.forEach(function(menu) {
                            const isSoldOut = menu.sold_out;
                            const switchId = `switch_menu_${menu.idx}`;
                            const checkedAttr = !isSoldOut ? 'checked' : '';

                            const imgHtml = menu.image
                                ? `<img class="" src="${menu.image}" alt="${menu.title}">`
                                : '';  // 이미지 없으면 빈 div로 유지하거나 원하는 대체 표시

                            // 옵션 HTML 생성
                            let optionsHtml = '<p class="text-muted small mt-2">옵션 없음</p>';

                            if (menu.options && menu.options.length > 0) {
                                optionsHtml = '';
                                menu.options.forEach(function(oc) {
                                    const requiredText = oc.required ? '(필수)' : '(선택)';
                                    let subOptionsHtml = '';

                                    if (oc.options && oc.options.length > 0) {
                                        oc.options.forEach(function(om) {
                                            const priceText = om.price > 0
                                                ? `+${Number(om.price).toLocaleString()}원`
                                                : '';
                                            subOptionsHtml += `
                                            <p>${om.title}
                                                <span class="text-primary">${priceText}</span>
                                            </p>`;
                                        });
                                    } else {
                                        subOptionsHtml = '<p class="text-muted">등록된 옵션이 없습니다.</p>';
                                    }

                                    optionsHtml += `
                                    <dl>
                                        <dt>${oc.title} ${requiredText}</dt>
                                        <dd>
                                            <div class="d-flex sub_op flex-wrap">
                                                ${subOptionsHtml}
                                            </div>
                                        </dd>
                                    </dl>
                                `;
                                });
                            }

                            html += `
                            <div class="mu_box" data-menu-idx="${menu.idx}">
                                <div class="flex-column-reverse d-flex flex-md-row">
                                    <div class="flex-fill">
                                        <div class="item_box">
                                            <div class="item_img flex-shrink-0">
                                                <div class="rect rounded">
                                                    ${imgHtml}
                                                </div>
                                            </div>
                                            <div>
                                                <p class="fw_500 tit_st4">${menu.title}${menu.age ? ` (${menu.age})` : ''}</p>
                                                <p class="tg_400 mt-2 line1_text">${menu.contents || ''}</p>
                                                <p class="mt-3 fw_700 tit_st3">${Number(menu.price).toLocaleString()}원</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ml-auto flex-shrink-0 d-flex mb-3 align-items-start">
                                        <button type="button" class="btn btn-md bg-light rounded-pill px-4 ml-3"
                                                onclick="location.href='../menu-add/?idx=${menu.idx}'">
                                            <span class="mr-2"><img src="<?=DESIGN_HTTP?>/market/img/ico_edit.svg" alt=" "></span> 편집
                                        </button>
                                        <div class="mt-3 flex-shrink-0">
                                            <div class="custom-control custom-switch switch-outside swh_l">
                                                <input type="checkbox" class="custom-control-input"
                                                       id="${switchId}" ${checkedAttr}
                                                       data-menu-idx="${menu.idx}">
                                                <span class="switch-state"></span>
                                                <label class="custom-control-label" for="${switchId}"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- 옵션 영역 -->
                                <div class="mu_box_sub">
                                    ${optionsHtml}
                                </div>
                            </div>
                        `;
                        });
                    }

                    html += '</div>'; // .mu_list 닫기
                    $menuContainer.append(html);
                });
            },
            error: function(xhr, status, error) {
                console.error('loadMenus AJAX 오류:', status, error);
                $menuContainer.html('<p class="text-center py-5 text-danger">서버와의 연결에 문제가 발생했습니다.</p>');
            }
        });
    }

    $(document).on('change', '.custom-control-input[data-menu-idx]', function() {
        const $this = $(this);
        const menuIdx = $this.data('menu-idx');
        const isChecked = this.checked;
        const newState = isChecked ? 'Y' : 'N';

        const $stateText = $this.closest('.custom-control').find('.switch-state'); // 필요시

        $stateText.text(isChecked ? '판매중' : '판매 마감');

        $.ajax({
            url: './update.php',
            type: 'POST',
            dataType: 'json',
            data: {
                act: 'menu_toggle_sale',
                menu_idx: menuIdx,
                is_sale: newState
            },
            success: function(res) {
                if (!res.success) {
                    alert(res.message || '상태 변경에 실패했습니다.');
                    // 실패 시 원상복구
                    this.checked = !isChecked;
                    $stateText.text(isChecked ? '판매 마감' : '판매중');
                    return;
                }

                // 성공 시 추가 UI 반영 (필요시)
                console.log('상태 변경 완료:', res.message);
            },
            error: function() {
                alert('서버 연결 오류가 발생했습니다.');
                // 실패 시 원상복구
                this.checked = !isChecked;
                $stateText.text(isChecked ? '판매 마감' : '판매중');
            }
        });
    });

    // 카테고리 추가 모달 열릴 때 초기화
    $('#modal_menu1').on('show.bs.modal', function () {
        $('#cat_title_add').val('');
        $('#cat_order_add').val('1');
        $('#customSwitch_mm1').prop('checked', true);
    });

    // 카테고리 추가 버튼 클릭
    $('#modal_menu1 .btn-primary.btn-block').on('click', function () {
        const title = $('#cat_title_add').val().trim();
        const order = parseInt($('#cat_order_add').val()) || 1;
        const show  = $('#customSwitch_mm1').is(':checked') ? 'Y' : 'N';

        if (title === '') {
            ModalUtil.alert({
                title: '메뉴',
                message: '카테고리명을 입력해주세요.',
                okText: '확인',
                onOk: function () {
                },
            });
            return;
        }

        $.ajax({
            url: './update.php',
            type: 'POST',
            data: {
                act: 'menu_category_add',
                title: title,
                order: order,
                show: show
            },
            success: function(res) {
                if (res.success) {
                    ModalUtil.alert({
                        title: '메뉴',
                        message: res.message,
                        okText: '확인',
                        onOk: function () {
                        },
                    });
                    // alert(res.message);
                    $('#modal_menu1').modal('hide');
                    loadCategories();
                    loadMenus();
                } else {
                    alert(res.message || '추가 실패');
                }
            },
            error: function() {
                alert('서버 오류');
            }
        });
    });

    // 카테고리 편집 모달 데이터 채우기
    $(document).on('click', 'button[data-target="#modal_menu2"]', function () {
        const sc_idx = $(this).data('sc-idx');
        if (!sc_idx) return;
        // 목록에서 찾는 방식 (간단 구현)
        // 더 정확히 하려면 별도 get_category API 만들어도 됨
        $.ajax({
            url: './update.php',
            type: 'POST',
            data: { act: 'menu_category_list' },
            success: function(res) {
                if (res.success) {
                    const cat = res.categories.find(c => c.idx === parseInt(sc_idx));
                    if (cat) {
                        $('#cat_title_edit').val(cat.title);
                        $('#cat_order_edit').val(cat.order);
                        $('#customSwitch_mm1_edit').prop('checked', cat.show);

                        $('.btn-menu-category-edit').data('cat-idx', cat.idx);
                        $('.btn-menu-category-delete').data('sc-idx', cat.idx);
                        $('.btn-menu-category-delete').data('sc-title', cat.title);
                    }
                }
            }
        });
    });

    // 카테고리 수정 실행
    $('.btn-menu-category-edit').on('click', function () {
        const cat_idx = $(this).data('cat-idx');
        const title   = $('#cat_title_edit').val().trim();
        const order   = parseInt($('#cat_order_edit').val()) || 1;
        const show    = $('#customSwitch_mm1_edit').is(':checked') ? 'Y' : 'N';

        if (!cat_idx || title === '') {
            ModalUtil.alert({
                title: '메뉴',
                message: '필수 값을 확인해주세요.',
                okText: '확인',
                onOk: function () {
                },
            });
            return;
        }

        $.ajax({
            url: './update.php',
            type: 'POST',
            data: {
                act: 'menu_category_update',
                cat_idx: cat_idx,
                title: title,
                order: order,
                show: show
            },
            success: function(res) {
                if (res.success) {
                    ModalUtil.alert({
                        title: '메뉴',
                        message: res.message,
                        okText: '확인',
                        onOk: function () {
                        },
                    });
                    $('#modal_menu2').modal('hide');
                    loadCategories();
                    loadMenus();
                } else {
                    alert(res.message || '수정 실패');
                }
            },
            error: function() {
                alert('서버 오류');
            }
        });
    });

    // 카테고리 삭제 버튼 (목록에 추가해야 함)
    $('.btn-menu-category-delete').on('click', function () {
        const cat_idx = $(this).data('sc-idx');
        const title   = $(this).data('sc-title');

        ModalUtil.confirm({
            title: '메뉴',
            message: `"${title}" 카테고리를 정말 삭제하시겠습니까?`,
            okText: '확인',
            cancelText: '취소',
            onOk: function () {
                $.ajax({
                    url: './update.php',
                    type: 'POST',
                    data: {
                        act: 'menu_category_delete',
                        cat_idx: cat_idx
                    },
                    success: function(res) {
                        if (res.success) {
                            $('#modal_menu2').modal('hide');

                            loadCategories();
                            loadMenus();
                        } else {
                            alert(res.message || '삭제 실패');
                        }
                    },
                    error: function() {
                        alert('서버 오류');
                    }
                });
            },
            onCancel: function (){
                return false;
            }
        });
    });

    $(document).ready(function() {
        loadCategories();
        loadMenus();

        $searchBtn.on('click', () => {
            currentKeyword = $searchInput.val().trim();
            loadMenus();
        });

        $searchInput.on('keypress', e => {
            if (e.which === 13) {
                currentKeyword = $searchInput.val().trim();
                loadMenus();
            }
        });
    });
</script>
<?php include_once("../inc/tail.php"); ?>
