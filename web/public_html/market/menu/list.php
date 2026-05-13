<?php
// list.php : 메뉴 관리 리스트 페이지

include $_SERVER['DOCUMENT_ROOT']."/market/head.inc.php";
$chk_menu     = '3';   // 필요 시 조정
$chk_sub_menu = '1';
include $_SERVER['DOCUMENT_ROOT']."/market/inc/header.menu.inc.php";

$page_title = "메뉴 관리";

// 현재 선택된 매장
$sh_idx = $_SESSION['current_sh_idx'] ?? 0;

// 메뉴 카테고리 셀렉트 박스용 데이터
$category_rows = [];
if ($sh_idx) {
    $DB->where('sh_idx', $sh_idx);
    $DB->orderBy('sc_order', 'ASC');
    $DB->orderBy('idx', 'ASC');
    $category_rows = $DB->get('shop_category_t', null, 'idx, sc_title');
}
?>
    <div class="container-fluid py-4">

        <!-- 상단 제목 & 버튼 -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
            <div>
                <h1 class="h3 fw-bold mb-1"><?= $page_title ?></h1>
                <p class="text-muted mb-0">메뉴와 카테고리, 옵션을 한 곳에서 관리하세요.</p>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <!-- 메뉴 추가 -->
                <button type="button"
                        class="btn btn-dark"
                        id="btnMenuAdd">
                    <i class="bi bi-plus-lg me-1"></i> 메뉴 추가
                </button>

                <button type="button"
                        class="btn btn-outline-secondary"
                        id="btnMenuCategoryAdd">
                    <i class="bi bi-folder-plus me-1"></i> 메뉴 카테고리 추가
                </button>
            </div>
        </div>

        <!-- 카테고리 탭 영역 -->
        <div class="mb-3" id="menu_category_tabs">
            <div class="d-flex justify-content-center py-3 text-muted">
                <div class="spinner-border text-primary me-2" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <span>카테고리 정보를 불러오는 중입니다...</span>
            </div>
        </div>

        <!-- 검색 영역 -->
        <div class="mb-3">
            <input type="text"
                   id="menuSearchInput"
                   class="form-control form-control-sm"
                   placeholder="메뉴명, 설명으로 검색 (엔터)">
        </div>

        <!-- 메뉴 카드 리스트 -->
        <div id="menu_list" class="row g-3">
            <div class="col-12 text-center py-5 text-muted">
                <div class="spinner-border text-primary mb-2" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div>메뉴 정보를 불러오는 중입니다...</div>
            </div>
        </div>

    </div><!-- /.container-fluid -->


    <!-- ========================= -->
    <!--   모달 (이 페이지 전용)   -->
    <!-- ========================= -->

    <!-- 1) 메뉴 추가/수정 모달 -->
    <div class="modal fade" id="menuAddModal" tabindex="-1" aria-labelledby="menuAddModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="border-radius:16px;">
                <!-- ✅ 이미지 업로드를 위해 enctype 추가 -->
                <form id="menuAddForm" enctype="multipart/form-data">
                    <input type="hidden" name="menu_id" id="menu_id">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="menuAddModalLabel">새 메뉴 추가</h5>
                        <button type="button" class="btn-close" data-dismiss="modal" aria-label="닫기"></button>
                    </div>
                    <div class="modal-body">

                        <!-- 메뉴명 -->
                        <div class="mb-3">
                            <label class="form-label">메뉴명</label>
                            <input type="text" name="menu_name" class="form-control" placeholder="예: 김치찌개" required>
                        </div>

                        <!-- 카테고리 -->
                        <div class="mb-3">
                            <label class="form-label">카테고리</label>
                            <select name="category" class="form-select" required id="menu_category_select">
                                <option value="">카테고리 선택</option>
                                <?php foreach ($category_rows as $cat): ?>
                                    <option value="<?= (int)$cat['idx'] ?>">
                                        <?= htmlspecialchars($cat['sc_title']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- 가격 -->
                        <div class="mb-3">
                            <label class="form-label">가격</label>
                            <div class="input-group">
                                <input type="number" name="price" class="form-control" placeholder="예: 9000" required>
                                <span class="input-group-text">원</span>
                            </div>
                        </div>

                        <!-- 설명 -->
                        <div class="mb-3">
                            <label class="form-label">설명</label>
                            <textarea name="description" class="form-control" placeholder="메뉴 설명을 입력하세요" rows="3"></textarea>
                        </div>

                        <!-- ✅ 메뉴 이미지 (1장) 추가 -->
                        <div class="mb-3">
                            <label class="form-label">메뉴 이미지 (1장)</label>

                            <!-- ✅ 수정 시 기존 이미지 유지/삭제 판단용 -->
                            <input type="hidden" name="old_menu_image" id="old_menu_image" value="">

                            <input type="file"
                                   name="menu_image"
                                   id="menu_image"
                                   class="form-control"
                                   accept="image/*">

                            <div class="mt-2 d-flex align-items-center gap-2">
                                <img id="menu_image_preview"
                                     src=""
                                     alt=""
                                     style="display:none; width:120px; height:120px; object-fit:cover; border-radius:12px; border:1px solid #eee;">

                                <button type="button"
                                        class="btn btn-outline-secondary btn-sm"
                                        id="btnMenuImageRemove"
                                        style="display:none;">
                                    이미지 제거
                                </button>
                            </div>

                            <div class="form-text text-muted">
                                JPG/PNG 권장
                            </div>
                        </div>

                        <!-- 판매 여부 -->
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="menuAddIsSale" name="is_sale" checked>
                            <label class="form-check-label" for="menuAddIsSale">판매 가능</label>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">취소</button>
                        <button type="submit" class="btn btn-dark">추가</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 2) 메뉴 카테고리 추가/수정 모달 -->
    <div class="modal fade" id="menuCategoryModal" tabindex="-1" aria-labelledby="menuCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:16px;">
                <form id="menuCategoryForm">
                    <input type="hidden" name="category_id" id="category_id">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="menuCategoryModalLabel">메뉴 카테고리 추가</h5>
                        <button type="button" class="btn-close" data-dismiss="modal" aria-label="닫기"></button>
                    </div>
                    <div class="modal-body">

                        <!-- 카테고리명 -->
                        <div class="mb-3">
                            <label class="form-label">카테고리명</label>
                            <input type="text" name="category_name" class="form-control" placeholder="예: 메인 메뉴" required>
                        </div>

                        <!-- 설명 (DB에는 없음, UI용) -->
                        <div class="mb-3">
                            <label class="form-label">설명 (선택)</label>
                            <textarea name="category_desc" class="form-control" rows="2" placeholder="카테고리 설명을 입력하세요"></textarea>
                        </div>

                        <!-- 정렬 순서 -->
                        <div class="mb-3">
                            <label class="form-label">정렬 순서</label>
                            <input type="number" name="category_order" class="form-control" placeholder="작을수록 상단에 노출" value="1">
                        </div>

                        <!-- 사용 여부 -->
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="categoryIsUse" name="is_use" checked>
                            <label class="form-check-label" for="categoryIsUse">사용</label>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">취소</button>
                        <button type="submit" class="btn btn-dark">추가</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 3) 옵션 카테고리 추가/수정 모달 -->
    <div class="modal fade" id="optionCategoryModal" tabindex="-1" aria-labelledby="optionCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:16px;">
                <form id="optionCategoryForm">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="optionCategoryModalLabel">옵션 카테고리 추가</h5>
                        <button type="button" class="btn-close" data-dismiss="modal" aria-label="닫기"></button>
                    </div>
                    <div class="modal-body">

                        <input type="hidden" name="menu_id" id="optionCategoryMenuId">
                        <input type="hidden" name="oc_idx" id="optionCategoryId">

                        <div class="small text-muted mb-2" id="optionCategoryMenuLabel">
                            선택된 메뉴: -
                        </div>

                        <div class="mb-3">
                            <label class="form-label">옵션 카테고리명</label>
                            <input type="text" name="option_category_name" class="form-control" placeholder="예: 밥 추가" required>
                        </div>

                        <!-- 필수 / 선택 -->
                        <div class="mb-3">
                            <label class="form-label d-block">선택 방식</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="option_required" id="optionRequiredY" value="Y" checked>
                                <label class="form-check-label" for="optionRequiredY">필수 선택</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="option_required" id="optionRequiredN" value="N">
                                <label class="form-check-label" for="optionRequiredN">선택 가능</label>
                            </div>
                        </div>

                        <!-- 사용 여부 -->
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="optionCategoryIsUse" name="is_use" checked>
                            <label class="form-check-label" for="optionCategoryIsUse">사용</label>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">취소</button>
                        <button type="submit" class="btn btn-dark">저장</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 4) 옵션 추가/수정 모달 -->
    <div class="modal fade" id="optionModal" tabindex="-1" aria-labelledby="optionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:16px;">
                <form id="optionForm">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="optionModalLabel">메뉴 옵션 추가</h5>
                        <button type="button" class="btn-close" data-dismiss="modal" aria-label="닫기"></button>
                    </div>
                    <div class="modal-body">

                        <input type="hidden" name="menu_id" id="optionMenuId">
                        <input type="hidden" name="om_idx" id="optionId">

                        <div class="small text-muted mb-2" id="optionMenuLabel">
                            선택된 메뉴: -
                        </div>

                        <div class="mb-3">
                            <label class="form-label">옵션명</label>
                            <input type="text" name="option_name" class="form-control" placeholder="예: 공기밥 추가" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">옵션 카테고리</label>
                            <select name="option_category" class="form-select" required>
                                <option value="">옵션 카테고리 선택</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">추가 금액</label>
                            <div class="input-group">
                                <input type="number" name="option_price" class="form-control" placeholder="예: 1000" required>
                                <span class="input-group-text">원</span>
                            </div>
                        </div>

                        <!-- is_default 는 DB에는 없음, UI만 -->
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="Y" id="optionIsDefault" name="is_default">
                            <label class="form-check-label" for="optionIsDefault">
                                기본 선택 옵션 (UI만, DB 저장 없음)
                            </label>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">취소</button>
                        <button type="submit" class="btn btn-dark">저장</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ========================= -->
    <!--   JS (이 페이지 전용)     -->
    <!-- ========================= -->
    <script>
        let currentCategory = 'all';

        // 공통: 모달 열기 (Bootstrap4 기준 jQuery .modal 사용)
        function showBsModalById(id) {
            const el = document.getElementById(id);
            if (!el) return;
            if (window.$ && $.fn.modal) {
                $(el).modal('show');
            } else {
                el.classList.add('show');
                el.style.display = 'block';
                el.removeAttribute('aria-hidden');
            }
        }

        // 공통: 모달 닫기
        function hideBsModalById(id) {
            const el = document.getElementById(id);
            if (!el) return;
            if (window.$ && $.fn.modal) {
                $(el).modal('hide');
            } else {
                el.classList.remove('show');
                el.style.display = 'none';
                el.setAttribute('aria-hidden', 'true');
            }
        }

        // 메뉴 리스트 + 카테고리 탭 로드
        function loadMenuList() {
            const category = currentCategory || 'all';
            const keyword  = $('#menuSearchInput').val() || '';

            console.log('[menu] loadMenuList', { category, keyword }); // ✅ 로그

            const $categoryContainer = $('#menu_category_tabs');
            const $listContainer     = $('#menu_list');

            $listContainer.html(`
                <div class="col-12 text-center py-5 text-muted">
                    <div class="spinner-border text-primary mb-2" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div>메뉴 정보를 불러오는 중입니다...</div>
                </div>
            `);

            $.ajax({
                url: './update.php',
                type: 'POST',
                data: {
                    act: 'list',
                    category: category,
                    search: keyword
                },
                dataType: 'html',
                success: function (html) {
                    const $doc  = $('<div>').html(html);
                    const $cat  = $doc.find('#category_content');
                    const $list = $doc.find('#list_content');

                    if ($cat.length) {
                        $categoryContainer.html($cat.html());
                    }

                    if ($list.length) {
                        $listContainer.html($list.html());
                    } else {
                        $listContainer.html(`
                            <div class="col-12 text-center py-5 text-danger">
                                메뉴 리스트 로드 중 오류가 발생했습니다.
                            </div>
                        `);
                    }
                },
                error: function (xhr) {
                    console.log('[menu] list ajax error', xhr.responseText); // ✅ 로그
                    $listContainer.html(`
                        <div class="col-12 text-center py-5 text-danger">
                            네트워크 오류가 발생했습니다.
                        </div>
                    `);
                }
            });
        }

        $(function () {

            // ====== 메뉴 모달 관련 공통 DOM 레퍼런스 ======
            const $menuAddForm        = $('#menuAddForm');
            const menuAddForm         = $menuAddForm[0];
            const $menuIdInput        = $('#menu_id');
            const $menuModalTitle     = $('#menuAddModalLabel');
            const $menuSubmitBtn      = $('#menuAddForm button[type="submit"]');
            const $menuCategorySelect = $('#menu_category_select');
            const $menuNameInput      = $('#menuAddForm input[name="menu_name"]');
            const $menuPriceInput     = $('#menuAddForm input[name="price"]');
            const $menuDescInput      = $('#menuAddForm textarea[name="description"]');
            const $menuIsSaleInput    = $('#menuAddIsSale');

            // ✅ 메뉴 추가 버튼 (추가 모드)
            $('#btnMenuAdd').on('click', function () {
                console.log('[menu] open add modal'); // ✅ 로그
                if (menuAddForm) menuAddForm.reset();
                $menuIdInput.val('');
                $menuCategorySelect.val('');
                $menuIsSaleInput.prop('checked', true);

                // ✅ 이미지 초기화
                $('#old_menu_image').val('');
                $('#menu_image').val('');
                $('#menu_image_preview').hide().attr('src', '');
                $('#btnMenuImageRemove').hide();

                $menuModalTitle.text('새 메뉴 추가');
                $menuSubmitBtn.text('추가');

                showBsModalById('menuAddModal');
            });

            // ✅ 메뉴 이미지 선택 시 미리보기
            $('#menu_image').on('change', function () {
                const file = this.files && this.files[0];
                console.log('[menu] image change', file); // ✅ 로그
                if (!file) return;

                const reader = new FileReader();
                reader.onload = function (e) {
                    $('#menu_image_preview').attr('src', e.target.result).show();
                    $('#btnMenuImageRemove').show();
                };
                reader.readAsDataURL(file);
            });

            // ✅ 이미지 제거 버튼
            $('#btnMenuImageRemove').on('click', function () {
                console.log('[menu] image remove click'); // ✅ 로그
                $('#menu_image').val('');
                $('#old_menu_image').val(''); // ✅ 기존 이미지도 제거 처리
                $('#menu_image_preview').hide().attr('src', '');
                $('#btnMenuImageRemove').hide();
            });

            // ✅ 메뉴 카테고리 추가 버튼 (추가 모드)
            $('#btnMenuCategoryAdd').on('click', function () {
                console.log('[menu] open category modal'); // ✅ 로그
                const form = $('#menuCategoryForm')[0];
                if (form) form.reset();
                $('#category_id').val('');
                $('#menuCategoryModalLabel').text('메뉴 카테고리 추가');
                $('#menuCategoryForm button[type="submit"]').text('추가');
                $('#categoryIsUse').prop('checked', true);
                showBsModalById('menuCategoryModal');
            });

            // 최초 리스트 로드
            loadMenuList();

            // 검색 엔터
            $('#menuSearchInput').on('keypress', function (e) {
                if (e.key === 'Enter') {
                    console.log('[menu] search enter'); // ✅ 로그
                    loadMenuList();
                }
            });

            // 전역 클릭 이벤트 위임
            $(document).on('click', function (e) {
                const $target = $(e.target);

                // 카테고리 탭 클릭 (필터)
                const $tabBtn = $target.closest('.menu-category-tab');
                if ($tabBtn.length) {
                    currentCategory = $tabBtn.data('category') || 'all';
                    console.log('[menu] category tab click', currentCategory); // ✅ 로그
                    loadMenuList();
                    return;
                }

                // ===== 메뉴 수정 버튼 =====
                const $editBtn = $target.closest('.btn-menu-edit');
                if ($editBtn.length) {
                    console.log('[menu] edit click', $editBtn.data()); // ✅ 로그

                    const menuId   = $editBtn.data('menuId');
                    const scIdx    = $editBtn.data('scIdx');
                    const name     = $editBtn.data('menuName') || '';
                    const subtitle = $editBtn.data('subtitle') || '';
                    const price    = $editBtn.data('price') || '';
                    const isSale   = $editBtn.data('isSale') === 'Y';

                    // ✅ 기존 이미지 경로
                    const menuImg  = $editBtn.data('menuImg') || '';

                    $menuIdInput.val(menuId);
                    $menuCategorySelect.val(scIdx);
                    $menuNameInput.val(name);
                    $menuPriceInput.val(price);
                    $menuDescInput.val(subtitle);
                    $menuIsSaleInput.prop('checked', isSale);

                    // ✅ 이미지 세팅
                    $('#menu_image').val('');
                    $('#old_menu_image').val(menuImg);

                    if (menuImg) {
                        $('#menu_image_preview').attr('src', menuImg).show();
                        $('#btnMenuImageRemove').show();
                    } else {
                        $('#menu_image_preview').hide().attr('src', '');
                        $('#btnMenuImageRemove').hide();
                    }

                    $menuModalTitle.text('메뉴 수정');
                    $menuSubmitBtn.text('저장');

                    showBsModalById('menuAddModal');
                    return;
                }

                // ===== 메뉴 삭제 버튼 =====
                const $delBtn = $target.closest('.btn-menu-delete');
                if ($delBtn.length) {
                    const menuId = $delBtn.data('menuId');
                    const name   = $delBtn.data('menuName') || '해당 메뉴';

                    console.log('[menu] delete click', { menuId, name }); // ✅ 로그

                    if (!menuId) return;

                    if (!confirm(`${name} 메뉴를 삭제하시겠습니까? 관련 옵션/옵션 카테고리도 함께 삭제됩니다.`)) {
                        return;
                    }

                    $.ajax({
                        url: './update.php',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            act: 'menu_delete',
                            menu_id: menuId
                        },
                        success: function (res) {
                            console.log('[menu] delete result', res); // ✅ 로그
                            if (!res.success) {
                                alert(res.message || '메뉴 삭제 중 오류가 발생했습니다.');
                                return;
                            }
                            alert(res.message || '메뉴가 삭제되었습니다.');
                            loadMenuList();
                        },
                        error: function (xhr) {
                            console.log('[menu] delete ajax error', xhr.responseText); // ✅ 로그
                            alert('메뉴 삭제 중 오류가 발생했습니다.');
                        }
                    });

                    return;
                }

                // ======= 🔹 메뉴 카테고리 수정 버튼 =======
                const $catEditBtn = $target.closest('.btn-menu-category-edit');
                if ($catEditBtn.length) {
                    console.log('[menu] category edit click', $catEditBtn.data()); // ✅ 로그
                    const scIdx  = $catEditBtn.data('scIdx');
                    const title  = $catEditBtn.data('scTitle') || '';
                    const memo   = $catEditBtn.data('scMemo') || '';
                    const order  = $catEditBtn.data('scOrder') || '';
                    const showYn = $catEditBtn.data('scShow') || 'Y';

                    const form = $('#menuCategoryForm')[0];
                    if (form) form.reset();

                    $('#category_id').val(scIdx);
                    $('input[name="category_name"]').val(title);
                    $('textarea[name="category_desc"]').val(memo);
                    $('input[name="category_order"]').val(order);
                    $('#categoryIsUse').prop('checked', showYn === 'Y');

                    $('#menuCategoryModalLabel').text('메뉴 카테고리 수정');
                    $('#menuCategoryForm button[type="submit"]').text('저장');

                    showBsModalById('menuCategoryModal');
                    return;
                }

                // ======= 🔹 메뉴 카테고리 삭제 버튼 =======
                const $catDelBtn = $target.closest('.btn-menu-category-delete');
                if ($catDelBtn.length) {
                    const scIdx = $catDelBtn.data('scIdx');
                    const title = $catDelBtn.data('scTitle') || '해당 카테고리';

                    console.log('[menu] category delete click', { scIdx, title }); // ✅ 로그

                    if (!scIdx) return;

                    if (!confirm(`${title} 카테고리를 삭제하시겠습니까?\n(해당 카테고리에 속한 메뉴가 있으면 삭제가 제한될 수 있습니다.)`)) {
                        return;
                    }

                    $.ajax({
                        url: './update.php',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            act: 'menu_category_delete',
                            sc_idx: scIdx
                        },
                        success: function (res) {
                            console.log('[menu] category delete result', res); // ✅ 로그
                            if (!res.success) {
                                alert(res.message || '카테고리 삭제 중 오류가 발생했습니다.');
                                return;
                            }
                            alert(res.message || '카테고리가 삭제되었습니다.');
                            loadMenuList();
                        },
                        error: function (xhr) {
                            console.log('[menu] category delete ajax error', xhr.responseText); // ✅ 로그
                            alert('카테고리 삭제 중 오류가 발생했습니다.');
                        }
                    });

                    return;
                }

                // ===== 메뉴별 옵션 카테고리 추가 버튼 =====
                const $ocBtn = $target.closest('.btn-option-category-add');
                if ($ocBtn.length) {
                    console.log('[menu] option category add click', $ocBtn.data()); // ✅ 로그
                    const menuId   = $ocBtn.data('menuId');
                    const menuName = $ocBtn.data('menuName') || '';

                    $('#optionCategoryId').val('');
                    $('#optionCategoryMenuId').val(menuId);
                    $('#optionCategoryMenuLabel').text(`선택된 메뉴: ${menuName} (ID: ${menuId})`);
                    $('input[name="option_required"][value="Y"]').prop('checked', true);
                    $('#optionCategoryIsUse').prop('checked', true);
                    $('input[name="option_category_name"]').val('');

                    $('#optionCategoryModalLabel').text('옵션 카테고리 추가');
                    showBsModalById('optionCategoryModal');
                    return;
                }

                // ===== 메뉴별 옵션 추가 버튼 =====
                const $optBtn = $target.closest('.btn-option-add');
                if ($optBtn.length) {
                    console.log('[menu] option add click', $optBtn.data()); // ✅ 로그
                    const menuId   = $optBtn.data('menuId');
                    const menuName = $optBtn.data('menuName') || '';

                    $('#optionId').val('');
                    $('#optionMenuId').val(menuId);
                    $('#optionMenuLabel').text(`선택된 메뉴: ${menuName} (ID: ${menuId})`);
                    $('#optionForm')[0].reset();

                    const $select = $('#optionForm select[name="option_category"]');
                    $select.html('<option value="">옵션 카테고리 불러오는 중...</option>');

                    $.ajax({
                        url: './update.php',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            act: 'get_option_categories',
                            sm_idx: menuId
                        },
                        success: function (res) {
                            console.log('[menu] get_option_categories result', res); // ✅ 로그
                            if (!res.success) {
                                alert(res.message || '옵션 카테고리를 불러오지 못했습니다.');
                                return;
                            }

                            const list = res.categories || [];
                            if (!list.length) {
                                if (confirm('등록된 옵션 카테고리가 없습니다. 먼저 옵션 카테고리를 추가하시겠습니까?')) {
                                    $('#optionCategoryId').val('');
                                    $('#optionCategoryMenuId').val(menuId);
                                    $('#optionCategoryMenuLabel').text(`선택된 메뉴: ${menuName} (ID: ${menuId})`);
                                    $('#optionCategoryModalLabel').text('옵션 카테고리 추가');
                                    showBsModalById('optionCategoryModal');
                                }
                                return;
                            }

                            $select.empty().append('<option value="">옵션 카테고리 선택</option>');
                            list.forEach(function (c) {
                                const text = c.oc_title + (c.oc_check === 'Y' ? ' (필수)' : ' (선택)');
                                $select.append(`<option value="${c.idx}">${text}</option>`);
                            });

                            $('#optionModalLabel').text('메뉴 옵션 추가');
                            showBsModalById('optionModal');
                        },
                        error: function (xhr) {
                            console.log('[menu] get_option_categories ajax error', xhr.responseText); // ✅ 로그
                            alert('옵션 카테고리를 불러오지 못했습니다.');
                        }
                    });

                    return;
                }

                // ===== 옵션 카테고리 수정 버튼 =====
                const $ocEditBtn = $target.closest('.btn-option-category-edit');
                if ($ocEditBtn.length) {
                    console.log('[menu] option category edit click', $ocEditBtn.data()); // ✅ 로그
                    const ocId    = $ocEditBtn.data('ocId');
                    const menuId  = $ocEditBtn.data('menuId');
                    const title   = $ocEditBtn.data('ocTitle') || '';
                    const check   = $ocEditBtn.data('ocCheck') || 'Y';
                    const showYn  = $ocEditBtn.data('ocShow') || 'Y';

                    $('#optionCategoryId').val(ocId);
                    $('#optionCategoryMenuId').val(menuId);
                    $('#optionCategoryMenuLabel').text(`선택된 메뉴 ID: ${menuId}`);
                    $('input[name="option_category_name"]').val(title);
                    $('input[name="option_required"][value="' + check + '"]').prop('checked', true);
                    $('#optionCategoryIsUse').prop('checked', showYn === 'Y');

                    $('#optionCategoryModalLabel').text('옵션 카테고리 수정');
                    showBsModalById('optionCategoryModal');
                    return;
                }

                // ===== 옵션 카테고리 삭제 버튼 =====
                const $ocDelBtn = $target.closest('.btn-option-category-delete');
                if ($ocDelBtn.length) {
                    const ocId   = $ocDelBtn.data('ocId');
                    const ocName = $ocDelBtn.data('ocTitle') || '해당 카테고리';

                    console.log('[menu] option category delete click', { ocId, ocName }); // ✅ 로그

                    if (!confirm(`${ocName} 옵션 카테고리를 삭제하시겠습니까?\n(하위 옵션도 함께 삭제됩니다)`)) {
                        return;
                    }

                    $.ajax({
                        url: './update.php',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            act: 'option_category_delete',
                            oc_idx: ocId
                        },
                        success: function (res) {
                            console.log('[menu] option category delete result', res); // ✅ 로그
                            if (!res.success) {
                                alert(res.message || '옵션 카테고리 삭제 중 오류가 발생했습니다.');
                                return;
                            }
                            alert(res.message || '옵션 카테고리가 삭제되었습니다.');
                            loadMenuList();
                        },
                        error: function (xhr) {
                            console.log('[menu] option category delete ajax error', xhr.responseText); // ✅ 로그
                            alert('옵션 카테고리 삭제 중 오류가 발생했습니다.');
                        }
                    });

                    return;
                }

                // ===== 옵션 수정 버튼 =====
                const $omEditBtn = $target.closest('.btn-option-edit');
                if ($omEditBtn.length) {
                    console.log('[menu] option edit click', $omEditBtn.data()); // ✅ 로그
                    const omId     = $omEditBtn.data('omId');
                    const ocId     = $omEditBtn.data('ocId');
                    const menuId   = $omEditBtn.data('menuId');
                    const omTitle  = $omEditBtn.data('omTitle') || '';
                    const omPrice  = $omEditBtn.data('omPrice') || 0;

                    $('#optionId').val(omId);
                    $('#optionMenuId').val(menuId);
                    $('#optionMenuLabel').text(`선택된 메뉴 ID: ${menuId}`);
                    $('#optionForm')[0].reset();
                    $('input[name="option_name"]').val(omTitle);
                    $('input[name="option_price"]').val(omPrice);

                    const $select = $('#optionForm select[name="option_category"]');
                    $select.html('<option value="">옵션 카테고리 불러오는 중...</option>');

                    $.ajax({
                        url: './update.php',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            act: 'get_option_categories',
                            sm_idx: menuId
                        },
                        success: function (res) {
                            console.log('[menu] get_option_categories (edit) result', res); // ✅ 로그
                            if (!res.success) {
                                alert(res.message || '옵션 카테고리를 불러오지 못했습니다.');
                                return;
                            }

                            const list = res.categories || [];
                            if (!list.length) {
                                alert('해당 메뉴에 등록된 옵션 카테고리가 없습니다.');
                                return;
                            }

                            $select.empty().append('<option value="">옵션 카테고리 선택</option>');
                            list.forEach(function (c) {
                                const text = c.oc_title + (c.oc_check === 'Y' ? ' (필수)' : ' (선택)');
                                const selected = (parseInt(c.idx, 10) === parseInt(ocId, 10)) ? 'selected' : '';
                                $select.append(`<option value="${c.idx}" ${selected}>${text}</option>`);
                            });

                            $('#optionModalLabel').text('메뉴 옵션 수정');
                            showBsModalById('optionModal');
                        },
                        error: function (xhr) {
                            console.log('[menu] get_option_categories (edit) ajax error', xhr.responseText); // ✅ 로그
                            alert('옵션 카테고리를 불러오지 못했습니다.');
                        }
                    });

                    return;
                }

                // ===== 옵션 삭제 버튼 =====
                const $omDelBtn = $target.closest('.btn-option-delete');
                if ($omDelBtn.length) {
                    const omId    = $omDelBtn.data('omId');
                    const omTitle = $omDelBtn.data('omTitle') || '해당 옵션';

                    console.log('[menu] option delete click', { omId, omTitle }); // ✅ 로그

                    if (!confirm(`${omTitle} 옵션을 삭제하시겠습니까?`)) {
                        return;
                    }

                    $.ajax({
                        url: './update.php',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            act: 'option_delete',
                            om_idx: omId
                        },
                        success: function (res) {
                            console.log('[menu] option delete result', res); // ✅ 로그
                            if (!res.success) {
                                alert(res.message || '옵션 삭제 중 오류가 발생했습니다.');
                                return;
                            }
                            alert(res.message || '옵션이 삭제되었습니다.');
                            loadMenuList();
                        },
                        error: function (xhr) {
                            console.log('[menu] option delete ajax error', xhr.responseText); // ✅ 로그
                            alert('옵션 삭제 중 오류가 발생했습니다.');
                        }
                    });

                    return;
                }

            }); // document click

            // ✅ 메뉴 추가/수정 폼 (이미지 포함 → FormData 사용)
            $('#menuAddForm').on('submit', function (e) {
                e.preventDefault();

                const menuId = $menuIdInput.val();
                const act    = menuId ? 'menu_update' : 'menu_save';

                console.log('[menu] submit', { act, menuId }); // ✅ 로그

                // ✅ multipart 전송
                const fd = new FormData(this);
                fd.append('act', act);

                // ✅ 체크박스는 unchecked면 값이 안 넘어가므로 강제 세팅
                fd.set('is_sale', $menuIsSaleInput.prop('checked') ? 'Y' : 'N');

                $.ajax({
                    url: './update.php',
                    type: 'POST',
                    dataType: 'json',
                    data: fd,
                    processData: false, // ✅ 필수
                    contentType: false, // ✅ 필수
                    success: function (res) {
                        console.log('[menu] submit result', res); // ✅ 로그
                        if (!res.success) {
                            alert(res.message || '메뉴 저장 중 오류가 발생했습니다.');
                            return;
                        }

                        alert(res.message || (act === 'menu_update'
                            ? '메뉴가 수정되었습니다.'
                            : '메뉴가 추가되었습니다.'));

                        hideBsModalById('menuAddModal');
                        if (menuAddForm) menuAddForm.reset();
                        $menuIdInput.val('');

                        // ✅ 이미지 UI 초기화
                        $('#old_menu_image').val('');
                        $('#menu_image').val('');
                        $('#menu_image_preview').hide().attr('src', '');
                        $('#btnMenuImageRemove').hide();

                        loadMenuList();
                    },
                    error: function (xhr) {
                        console.log('[menu] submit ajax error', xhr.responseText); // ✅ 로그
                        alert('메뉴 저장 중 오류가 발생했습니다.');
                    }
                });
            });

            // 🔹 메뉴 카테고리 추가/수정 폼
            $('#menuCategoryForm').on('submit', function (e) {
                e.preventDefault();

                const categoryId = $('#category_id').val();
                const act        = categoryId ? 'menu_category_update' : 'menu_category_save';

                console.log('[menu] category submit', { act, categoryId }); // ✅ 로그

                const formData = $(this).serialize() + '&act=' + act;

                $.ajax({
                    url: './update.php',
                    type: 'POST',
                    dataType: 'json',
                    data: formData,
                    success: function (res) {
                        console.log('[menu] category submit result', res); // ✅ 로그
                        if (!res.success) {
                            alert(res.message || '카테고리 저장 중 오류가 발생했습니다.');
                            return;
                        }
                        alert(res.message || (act === 'menu_category_update'
                            ? '카테고리가 수정되었습니다.'
                            : '카테고리가 추가되었습니다.'));

                        hideBsModalById('menuCategoryModal');
                        $('#menuCategoryForm')[0].reset();
                        $('#category_id').val('');
                        loadMenuList();
                    },
                    error: function (xhr) {
                        console.log('[menu] category submit ajax error', xhr.responseText); // ✅ 로그
                        alert('카테고리 저장 중 오류가 발생했습니다.');
                    }
                });
            });

            // 옵션 카테고리 추가/수정 폼
            $('#optionCategoryForm').on('submit', function (e) {
                e.preventDefault();

                const ocIdx = $('#optionCategoryId').val();
                const act   = ocIdx ? 'option_category_update' : 'option_category_save';

                console.log('[menu] option category submit', { act, ocIdx }); // ✅ 로그

                const formData = $(this).serialize() + '&act=' + act;

                $.ajax({
                    url: './update.php',
                    type: 'POST',
                    dataType: 'json',
                    data: formData,
                    success: function (res) {
                        console.log('[menu] option category submit result', res); // ✅ 로그
                        if (!res.success) {
                            alert(res.message || '옵션 카테고리 저장 중 오류가 발생했습니다.');
                            return;
                        }
                        alert(res.message || '옵션 카테고리가 저장되었습니다.');
                        hideBsModalById('optionCategoryModal');
                        $('#optionCategoryForm')[0].reset();
                        loadMenuList();
                    },
                    error: function (xhr) {
                        console.log('[menu] option category submit ajax error', xhr.responseText); // ✅ 로그
                        alert('옵션 카테고리 저장 중 오류가 발생했습니다.');
                    }
                });
            });

            // 옵션 추가/수정 폼
            $('#optionForm').on('submit', function (e) {
                e.preventDefault();

                const omIdx = $('#optionId').val();
                const act   = omIdx ? 'option_update' : 'option_save';

                console.log('[menu] option submit', { act, omIdx }); // ✅ 로그

                const formData = $(this).serialize() + '&act=' + act;

                $.ajax({
                    url: './update.php',
                    type: 'POST',
                    dataType: 'json',
                    data: formData,
                    success: function (res) {
                        console.log('[menu] option submit result', res); // ✅ 로그
                        if (!res.success) {
                            alert(res.message || '옵션 저장 중 오류가 발생했습니다.');
                            return;
                        }
                        alert(res.message || '옵션이 저장되었습니다.');
                        hideBsModalById('optionModal');
                        $('#optionForm')[0].reset();
                        loadMenuList();
                    },
                    error: function (xhr) {
                        console.log('[menu] option submit ajax error', xhr.responseText); // ✅ 로그
                        alert('옵션 저장 중 오류가 발생했습니다.');
                    }
                });
            });

        }); // jQuery ready
    </script>

<?php
include $_SERVER['DOCUMENT_ROOT']."/market/foot.inc.php";
?>
