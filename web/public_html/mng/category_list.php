<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.menu.inc.php";
//include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.sidebar.inc.php";

?>

    <style>
        .tree-list {
            list-style: none;
            padding-left: 20px;
        }
        .tree-node {
            padding: 5px 0;
            display: flex;
            align-items: center;
        }
        .tree-toggle, .tree-no-children {
            cursor: pointer;
            width: 20px;
            text-align: center;
            margin-right: 5px;
        }
        .tree-label {
            font-weight: 500;
        }
        .text-muted .tree-label {
            text-decoration: line-through;
            opacity: 0.7;
        }
    </style>

    <!-- PAGE CONTENT CONTAINER -->
    <div class="content" id="content">
        <!-- PAGE HEADING -->
        <div class="page-heading">
            <div class="page-heading__container">
                <div class="icon">
                    <span class="li-picture3"></span>
                </div>
                <h1 class="title">카테고리</h1>
                <p class="caption">
                    카테고리 등록, 수정, 삭제 등을 할 수 있습니다.
                </p>
            </div>
            <nav aria-label="breadcrumb" role="navigation">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#">상품관리</a></li>
                    <li class="breadcrumb-item active">카테고리</li>
                </ol>
            </nav>
        </div>
        <!-- //END PAGE HEADING -->
        <div class="container-fluid">
            <div class="card margin-bottom-0">
                <div class="card-body">
                    <form method="POST" name="frm_search" id="frm_search" action="<?=$_SERVER['PHP_SELF']?>" onsubmit="return frm_search_chk(this, event);">
                        <div class="form-row">
                            <div class="col-6 col-lg-3">
                                <div class="dt-buttons2 btn-group">
                                    <button class="btn btn-light buttons-double-up buttons-html5"
                                            tabindex="0"
                                            aria-controls="dt-example-buttons"
                                            onclick="categoryMoveToTop()"
                                            id="moveTopBtn"
                                            data-toggle="tooltip"
                                            data-placement="top"
                                            title="맨 위로">
                                        <span class="fa fa-angle-double-up"></span>
                                    </button>
                                    <button class="btn btn-light buttons-up buttons-html5"
                                            tabindex="0"
                                            aria-controls="dt-example-buttons"
                                            onclick="categoryMoveUp()"
                                            id="moveUpBtn"
                                            data-toggle="tooltip"
                                            data-placement="top"
                                            title="위로">
                                        <span class="fa fa-angle-up"></span>
                                    </button>
                                    <button class="btn btn-light buttons-down buttons-html5"
                                            tabindex="0"
                                            aria-controls="dt-example-buttons"
                                            onclick="categoryMoveDown()"
                                            id="moveDownBtn"
                                            data-toggle="tooltip"
                                            data-placement="top"
                                            title="아래로">
                                        <span class="fa fa-angle-down"></span>
                                    </button>
                                    <button class="btn btn-light buttons-double-down buttons-html5"
                                            tabindex="0"
                                            aria-controls="dt-example-buttons"
                                            onclick="categoryMoveToBottom()"
                                            id="moveBottomBtn"
                                            data-toggle="tooltip"
                                            data-placement="top"
                                            title="맨 아래로">
                                        <span class="fa fa-angle-double-down"></span>
                                    </button>
                                    <button class="btn btn-light buttons-save buttons-html5"
                                            type="button"
                                            tabindex="0"
                                            aria-controls="dt-example-buttons"
                                            onclick="saveCategorySequence(document.getElementById('obj_pg').value, document.getElementById('obj_limit_num').value)"
                                            id="saveOrderBtn"
                                            data-toggle="tooltip"
                                            data-placement="top"
                                            title="순서 저장">
                                        <span class="fa fa-save"></span>
                                    </button>
                                    <button class="btn btn-light buttons-html5"
                                            tabindex="0"
                                            aria-controls="dt-example-buttons"
                                            onclick="showCategoryTree()"
                                            id="viewTreeBtn"
                                            data-toggle="tooltip"
                                            data-placement="top"
                                            title="트리 구조 보기">
                                        <span class="fa fa-sitemap"></span>
                                    </button>
                                    <button class="btn btn-light buttons-html5"
                                            tabindex="0"
                                            aria-controls="dt-example-buttons"
                                            onclick="exportCategories()"
                                            id="exportBtn"
                                            data-toggle="tooltip"
                                            data-placement="top"
                                            title="내보내기">
                                        <span class="fa fa-download"></span>
                                    </button>
                                    <button class="btn btn-light buttons-html5"
                                            tabindex="0"
                                            aria-controls="dt-example-buttons"
                                            onclick="showImportModal()"
                                            id="importBtn"
                                            data-toggle="tooltip"
                                            data-placement="top"
                                            title="가져오기">
                                        <span class="fa fa-upload"></span>
                                    </button>
                                    <button class="btn btn-light buttons-html5"
                                            tabindex="0"
                                            aria-controls="dt-example-buttons"
                                            onclick="resetFilters()"
                                            id="resetFiltersBtn"
                                            data-toggle="tooltip"
                                            data-placement="top"
                                            title="필터 초기화">
                                        <span class="fa fa-refresh"></span>
                                    </button>
                                    <button class="btn btn-light buttons-html5"
                                            tabindex="0"
                                            aria-controls="dt-example-buttons"
                                            onclick="bulkToggleVisibility('Y')"
                                            id="showSelectedBtn"
                                            data-toggle="tooltip"
                                            data-placement="top"
                                            title="선택 노출">
                                        <span class="fa fa-eye"></span>
                                    </button>
                                    <button class="btn btn-light buttons-html5"
                                            tabindex="0"
                                            aria-controls="dt-example-buttons"
                                            onclick="bulkToggleVisibility('N')"
                                            id="hideSelectedBtn"
                                            data-toggle="tooltip"
                                            data-placement="top"
                                            title="선택 숨김">
                                        <span class="fa fa-eye-slash"></span>
                                    </button>
                                </div>
                                <script>
                                    $(document).ready(function(){
                                        $('[data-toggle="tooltip"]').tooltip();
                                    });
                                </script>
                            </div>

                            <div class="col-6 col-lg-9 d-flex justify-content-end align-items-center">
                                <!-- 필터링 영역 -->
                                <div class="form-inline mr-2">
                                    <select class="form-control mr-2" id="filter_ct_show" name="filter_ct_show">
                                        <option value="">모든 상태</option>
                                        <option value="Y">노출</option>
                                        <option value="N">숨김</option>
                                    </select>
                                    <select class="form-control mr-2" id="filter_ct_level" name="filter_ct_level">
                                        <option value="">모든 레벨</option>
                                        <option value="1">1차 카테고리</option>
                                        <option value="2">2차 카테고리</option>
                                        <option value="3">3차 카테고리</option>
                                    </select>
                                    <select class="form-control mr-2" id="filter_ct_pid" name="filter_ct_pid">
                                        <option value="">모든 상위 카테고리</option>
                                        <!-- 상위 카테고리 목록은 AJAX로 로드 -->
                                    </select>
                                    <select class="form-control mr-2" id="obj_limit_num" name="obj_limit_num">
                                        <option value="10">10개씩</option>
                                        <option value="20">20개씩</option>
                                        <option value="50">50개씩</option>
                                        <option value="100">100개씩</option>
                                    </select>
                                </div>
                                <button type="button" class="btn btn-info margin-right-5" onclick="location.href='./category_form.php'">신규등록</button>
                                <button type="button" class="btn btn-gray" onclick="location.href='./category_list.php'">초기화</button>
                            </div>
                        </div>

                    </form>

                    <!-- 게시물 리스트-->
                    <form name="frm_list" id="frm_list" onsubmit="return false;">
                        <input type="hidden" name="act" id="act" value="list" />
                        <input type="hidden" name="obj_list" id="obj_list" value="category_list_box" />
                        <input type="hidden" name="obj_frm" id="obj_frm" value="frm_list" />
                        <input type="hidden" name="obj_uri" id="obj_uri" value="./category_update.php" />
                        <input type="hidden" name="obj_pg" id="obj_pg" value="1" />
                        <input type="hidden" name="obj_limit_num" id="obj_limit_num" value="10" />
                        <input type="hidden" name="obj_orderby" id="obj_orderby" value="" />
                        <input type="hidden" name="obj_order_desc_asc" id="obj_order_desc_asc" value="1" />
                        <input type="hidden" name="obj_sel_search" id="obj_sel_search" value="<?=$_POST['sel_search']?>" />
                        <input type="hidden" name="obj_search_txt" id="obj_search_txt" value="<?=$_POST['search_txt']?>" />
                    </form>
                    <div id="category_list_box"></div>

                    <!-- 상태 메시지 표시 -->
                    <div id="statusMessage" class="alert alert-success" style="display:none; position:fixed; bottom:20px; right:50px;"></div>

                    <!-- 카테고리 트리 모달 -->
                    <div class="modal fade" id="categoryTreeModal" tabindex="-1" role="dialog" aria-labelledby="categoryTreeModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="categoryTreeModalLabel">카테고리 트리 구조</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div id="categoryTreeContainer" class="p-3"></div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">닫기</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 카테고리 가져오기 모달 -->
                    <div class="modal fade" id="importCategoryModal" tabindex="-1" role="dialog" aria-labelledby="importCategoryModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="importCategoryModalLabel">카테고리 데이터 가져오기</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="categoryImportFile">JSON 파일 선택</label>
                                        <input type="file" class="form-control-file" id="categoryImportFile" accept=".json,application/json">
                                        <small class="form-text text-muted">이전에 내보낸 카테고리 JSON 파일을 선택하세요.</small>
                                    </div>
                                    <div class="alert alert-warning">
                                        <i class="fa fa-exclamation-triangle"></i> 주의: 가져오기를 실행하면 기존 데이터가 덮어쓰기 될 수 있습니다.
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">취소</button>
                                    <button type="button" class="btn btn-primary" onclick="importCategories()">가져오기</button>
                                </div>
                            </div>
                        </div>
                    </div>


                    <script>
                        // 페이지 로드 시 초기화
                        $(document).ready(function() {
                            // 카테고리 목록 로드
                            f_get_category_list();

                            // 상위 카테고리 목록 로드
                            loadParentCategories();

                            // 툴팁 초기화
                            $('[data-toggle="tooltip"]').tooltip();

                            // 검색 버튼 클릭 시
                            $('#btnSearch').on('click', function() {
                                f_get_category_list(1);
                                return false;
                            });

                            // 검색어 입력 필드에서 엔터 키 입력 시
                            $('#obj_search_txt').on('keypress', function(e) {
                                if (e.which === 13) {
                                    f_get_category_list(1);
                                    return false;
                                }
                            });

                            // 필터 변경 시
                            $('#filter_ct_show, #filter_ct_level, #filter_ct_pid, #obj_limit_num').on('change', function() {
                                f_get_category_list(1);
                            });

                            // 초기화 버튼 클릭 시
                            $('#btnReset').on('click', function() {
                                resetFilters();
                                return false;
                            });

                            // 상태 메시지 요소 추가
                            if ($('#statusMessage').length === 0) {
                                $('body').append('<div id="statusMessage" class="alert alert-info" style="position: fixed; bottom: 20px; right: 20px; z-index: 9999; display: none;"></div>');
                            }

                            // 카테고리 트리 모달 추가
                            if ($('#categoryTreeModal').length === 0) {
                                $('body').append(`
            <div class="modal fade" id="categoryTreeModal" tabindex="-1" role="dialog" aria-labelledby="categoryTreeModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="categoryTreeModalLabel">카테고리 트리 구조</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div id="categoryTreeContainer" class="p-3"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">닫기</button>
                        </div>
                    </div>
                </div>
            </div>
            <style>
                .tree-list {
                    list-style: none;
                    padding-left: 20px;
                }
                .tree-node {
                    padding: 5px 0;
                    display: flex;
                    align-items: center;
                }
                .tree-toggle, .tree-no-children {
                    cursor: pointer;
                    width: 20px;
                    text-align: center;
                    margin-right: 5px;
                }
                .tree-label {
                    font-weight: 500;
                }
                .text-muted .tree-label {
                    text-decoration: line-through;
                    opacity: 0.7;
                }
            </style>
        `);
                            }

                            // 가져오기 모달 추가
                            if ($('#importCategoryModal').length === 0) {
                                $('body').append(`
            <div class="modal fade" id="importCategoryModal" tabindex="-1" role="dialog" aria-labelledby="importCategoryModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="importCategoryModalLabel">카테고리 데이터 가져오기</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="categoryImportFile">JSON 파일 선택</label>
                                <input type="file" class="form-control-file" id="categoryImportFile" accept=".json,application/json">
                                <small class="form-text text-muted">이전에 내보낸 카테고리 JSON 파일을 선택하세요.</small>
                            </div>
                            <div class="alert alert-warning">
                                <i class="fa fa-exclamation-triangle"></i> 주의: 가져오기를 실행하면 기존 데이터가 덮어쓰기 될 수 있습니다.
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">취소</button>
                            <button type="button" class="btn btn-primary" onclick="importCategories()">가져오기</button>
                        </div>
                    </div>
                </div>
            </div>
        `);
                            }
                        });

                        function f_get_category_list(pg = "") {
                            // 페이지 번호가 없을 경우 로컬 스토리지에서 가져오거나 기본값 1 설정
                            if (pg == null || pg == "") {
                                var ls_obj_pg = localStorage.getItem("category_obj_pg");
                                if (ls_obj_pg) {
                                    pg = ls_obj_pg;

                                    // 로컬 스토리지에 저장된 필터 값들 복원
                                    for (let i = 0; i < localStorage.length; i++) {
                                        let key = localStorage.key(i);
                                        if (key.startsWith('category_') && key != '@tosspayments/client-id') {
                                            const elementId = key.replace('category_', '');
                                            if (localStorage.getItem(key) && $("#" + elementId).val() == "") {
                                                $("#" + elementId).val(localStorage.getItem(key));
                                            }
                                        }
                                    }
                                } else {
                                    pg = 1;
                                }
                            }

                            // 현재 페이지 설정
                            $("#obj_pg").val(parseInt(pg));

                            // 검색 조건 및 필터 값 가져오기
                            const obj_sel_search = $('#obj_sel_search').val();
                            const obj_search_txt = $('#obj_search_txt').val();
                            const obj_limit_num = $('#obj_limit_num').val() || 10;
                            const ct_show = $('#filter_ct_show').val();
                            const ct_level = $('#filter_ct_level').val();
                            const ct_pid = $('#filter_ct_pid').val();

                            // 로딩 표시
                            $('#category_list_box').html('<div class="text-center p-5"><i class="fa fa-spinner fa-spin fa-2x"></i></div>');

                            // AJAX 요청
                            $.ajax({
                                url: './category_update.php',
                                type: 'POST',
                                data: {
                                    act: 'list',
                                    obj_pg: pg,
                                    obj_limit_num: obj_limit_num,
                                    obj_sel_search: obj_sel_search,
                                    obj_search_txt: obj_search_txt,
                                    ct_show: ct_show,
                                    ct_level: ct_level,
                                    ct_pid: ct_pid
                                },
                                success: function(data) {
                                    if (data) {
                                        // 로컬 스토리지에 현재 페이지와 검색 조건 저장
                                        localStorage.setItem("category_obj_pg", pg);
                                        localStorage.setItem("category_obj_sel_search", obj_sel_search);
                                        localStorage.setItem("category_obj_search_txt", obj_search_txt);
                                        localStorage.setItem("category_obj_limit_num", obj_limit_num);
                                        localStorage.setItem("category_filter_ct_show", ct_show);
                                        localStorage.setItem("category_filter_ct_level", ct_level);
                                        localStorage.setItem("category_filter_ct_pid", ct_pid);

                                        // 결과 데이터를 category_list_box에 삽입
                                        $('#category_list_box').html(data);

                                        // 체크박스 전체 선택/해제 기능
                                        $('#selectAll').on('change', function() {
                                            $('.rowCheckbox').prop('checked', $(this).prop('checked'));

                                            // 체크 상태에 따라 행 하이라이트
                                            if ($(this).prop('checked')) {
                                                $('.rowCheckbox').closest('tr').addClass('table-active');
                                            } else {
                                                $('.rowCheckbox').closest('tr').removeClass('table-active');
                                            }
                                        });

                                        // 행 선택 시 하이라이트 효과
                                        $('.rowCheckbox').on('change', function() {
                                            if ($(this).prop('checked')) {
                                                $(this).closest('tr').addClass('table-active');
                                            } else {
                                                $(this).closest('tr').removeClass('table-active');
                                            }
                                        });
                                    }

                                    // 테이블 초기화 함수 호출 (드래그 앤 드롭 등)
                                    if (typeof initializeTable === 'function') {
                                        initializeTable();
                                    } else {
                                        console.log('initializeTable 함수가 정의되지 않았습니다.');
                                    }
                                },
                                error: function(xhr, status, error) {
                                    $('#category_list_box').html('<div class="alert alert-danger">목록을 불러오는 중 오류가 발생했습니다.</div>');
                                    console.error(error);
                                }
                            });

                            return false;
                        }

                        // 카테고리 삭제 함수
                        function deleteCategory(ct_id) {
                            if (!confirm('정말 이 카테고리를 삭제하시겠습니까?')) {
                                return;
                            }

                            $.ajax({
                                url: './category_update.php',
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    act: 'delete',
                                    ct_id: ct_id
                                },
                                success: function(response) {
                                    if (response.success) {
                                        alert(response.message);
                                        f_get_category_list(); // 목록 새로고침
                                    } else {
                                        alert(response.message || '삭제 중 오류가 발생했습니다.');
                                    }
                                },
                                error: function() {
                                    alert('서버 통신 중 오류가 발생했습니다.');
                                }
                            });
                        }

                        // 선택한 카테고리 노출/숨김 처리 함수
                        function bulkToggleVisibility(show) {
                            const selectedIds = [];
                            $('.rowCheckbox:checked').each(function() {
                                selectedIds.push($(this).val());
                            });

                            if (selectedIds.length === 0) {
                                alert('선택된 카테고리가 없습니다.');
                                return;
                            }

                            const actionText = show === 'Y' ? '노출' : '숨김';

                            if (!confirm(`선택한 ${selectedIds.length}개 카테고리를 ${actionText} 처리하시겠습니까?`)) {
                                return;
                            }

                            $.ajax({
                                url: './category_update.php',
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    act: 'bulkToggleVisibility',
                                    ids: selectedIds,
                                    show: show
                                },
                                success: function(response) {
                                    if (response.success) {
                                        alert(response.message);
                                        f_get_category_list(); // 목록 새로고침
                                    } else {
                                        alert(response.message || '처리 중 오류가 발생했습니다.');
                                    }
                                },
                                error: function() {
                                    alert('서버 통신 중 오류가 발생했습니다.');
                                }
                            });
                        }

                        // 카테고리 순서 변경 함수들 (이름 변경)
                        function categoryMoveUp() {
                            const selectedRow = $('.rowCheckbox:checked').closest('tr');
                            if (selectedRow.length !== 1) {
                                alert('순서 변경을 위해 하나의 항목만 선택해주세요.');
                                return;
                            }

                            const prevRow = selectedRow.prev('tr');
                            if (prevRow.length === 0) {
                                showStatusMessage('이미 최상단 항목입니다.');
                                return;
                            }

                            selectedRow.insertBefore(prevRow);
                            highlightRow(selectedRow);
                        }

                        function categoryMoveDown() {
                            const selectedRow = $('.rowCheckbox:checked').closest('tr');
                            if (selectedRow.length !== 1) {
                                alert('순서 변경을 위해 하나의 항목만 선택해주세요.');
                                return;
                            }

                            const nextRow = selectedRow.next('tr');
                            if (nextRow.length === 0) {
                                showStatusMessage('이미 최하단 항목입니다.');
                                return;
                            }

                            selectedRow.insertAfter(nextRow);
                            highlightRow(selectedRow);
                        }

                        function categoryMoveToTop() {
                            const selectedRow = $('.rowCheckbox:checked').closest('tr');
                            if (selectedRow.length !== 1) {
                                alert('순서 변경을 위해 하나의 항목만 선택해주세요.');
                                return;
                            }

                            const firstRow = selectedRow.siblings().first();
                            if (selectedRow.index() <= firstRow.index()) {
                                showStatusMessage('이미 최상단 항목입니다.');
                                return;
                            }

                            const tbody = selectedRow.parent();
                            selectedRow.detach().prependTo(tbody);
                            highlightRow(selectedRow);
                        }

                        function categoryMoveToBottom() {
                            const selectedRow = $('.rowCheckbox:checked').closest('tr');
                            if (selectedRow.length !== 1) {
                                alert('순서 변경을 위해 하나의 항목만 선택해주세요.');
                                return;
                            }

                            const lastRow = selectedRow.siblings().last();
                            if (selectedRow.index() >= lastRow.index()) {
                                showStatusMessage('이미 최하단 항목입니다.');
                                return;
                            }

                            const tbody = selectedRow.parent();
                            selectedRow.detach().appendTo(tbody);
                            highlightRow(selectedRow);
                        }

                        // 순서 저장 함수
                        function saveCategorySequence(page, limit) {
                            // 모든 행의 ID와 순서 수집
                            const rows = $('#categoryTable tbody tr');
                            if (rows.length === 0) {
                                showStatusMessage('저장할 카테고리가 없습니다.');
                                return;
                            }

                            const sequenceData = [];
                            rows.each(function(index) {
                                const categoryId = $(this).data('category-id');
                                if (categoryId) {
                                    sequenceData.push({
                                        id: categoryId,
                                        sequence: index + 1
                                    });
                                }
                            });

                            if (sequenceData.length === 0) {
                                showStatusMessage('저장할 카테고리 정보를 찾을 수 없습니다.');
                                return;
                            }

                            // 서버에 순서 저장 요청
                            $.ajax({
                                url: './category_update.php',
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    act: 'saveSequence',
                                    sequence_data: JSON.stringify(sequenceData),
                                    page: page,
                                    limit: limit
                                },
                                beforeSend: function() {
                                    showStatusMessage('카테고리 순서를 저장 중입니다...');
                                },
                                success: function(response) {
                                    if (response.success) {
                                        showStatusMessage('카테고리 순서가 성공적으로 저장되었습니다.');
                                        // 필요하다면 목록 새로고침
                                        setTimeout(function() {
                                            f_get_category_list(page);
                                        }, 1000);
                                    } else {
                                        showStatusMessage('카테고리 순서 저장에 실패했습니다: ' + (response.message || '알 수 없는 오류'));
                                    }
                                },
                                error: function(xhr, status, error) {
                                    showStatusMessage('서버 통신 중 오류가 발생했습니다: ' + error);
                                }
                            });
                        }

                        // 행 하이라이트 효과
                        function highlightRow(row) {
                            row.addClass('bg-light-success');
                            setTimeout(() => {
                                row.removeClass('bg-light-success');
                            }, 1000);

                            // 상태 메시지 표시
                            showStatusMessage('순서가 변경되었습니다. 저장 버튼을 클릭하여 변경사항을 저장하세요.');
                        }

                        // 상태 메시지 표시 함수
                        function showStatusMessage(message) {
                            const statusEl = $('#statusMessage');
                            statusEl.text(message).fadeIn();
                            setTimeout(() => {
                                statusEl.fadeOut();
                            }, 3000);
                        }

                        // 상위 카테고리 목록 로드
                        function loadParentCategories() {
                            $.ajax({
                                url: './category_update.php',
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    act: 'getParentCategories'
                                },
                                success: function(response) {
                                    if (response.success && response.data) {
                                        const select = $('#filter_ct_pid');
                                        select.find('option:gt(0)').remove(); // 첫 번째 옵션 외 모두 제거

                                        response.data.forEach(category => {
                                            select.append(`<option value="${category.ct_id}">${category.ct_name}</option>`);
                                        });

                                        // 로컬 스토리지에 저장된 값이 있으면 복원
                                        const savedPid = localStorage.getItem('category_filter_ct_pid');
                                        if (savedPid) {
                                            select.val(savedPid);
                                        }
                                    }
                                }
                            });
                        }

                        // 필터 초기화
                        function resetFilters() {
                            $('#filter_ct_show, #filter_ct_level, #filter_ct_pid').val('');
                            $('#obj_limit_num').val('10');
                            $('#obj_sel_search').val('all');
                            $('#obj_search_txt').val('');

                            // 로컬 스토리지에서 카테고리 관련 항목 삭제
                            for (let i = 0; i < localStorage.length; i++) {
                                let key = localStorage.key(i);
                                if (key.startsWith('category_')) {
                                    localStorage.removeItem(key);
                                }
                            }

                            // 목록 새로고침
                            f_get_category_list(1);

                            showStatusMessage('필터가 초기화되었습니다.');
                        }

                        // 카테고리 트리 시각화 함수
                        function showCategoryTree() {
                            // 모달 표시
                            $('#categoryTreeModal').modal('show');

                            // 트리 데이터 로드
                            $.ajax({
                                url: './category_update.php',
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    act: 'getCategoryTree'
                                },
                                success: function(response) {
                                    if (response.success && response.data) {
                                        renderCategoryTree(response.data);
                                    } else {
                                        $('#categoryTreeContainer').html('<div class="alert alert-danger">카테고리 트리를 불러오는데 실패했습니다.</div>');
                                    }
                                },
                                error: function() {
                                    $('#categoryTreeContainer').html('<div class="alert alert-danger">서버 통신 중 오류가 발생했습니다.</div>');
                                }
                            });
                        }

                        // 트리 렌더링 함수
                        function renderCategoryTree(data) {
                            const treeContainer = $('#categoryTreeContainer');
                            treeContainer.empty();

                            // 트리 생성
                            const treeHtml = buildTreeHtml(data);
                            treeContainer.html(treeHtml);

                            // 트리 노드 토글 기능
                            $('.tree-toggle').on('click', function() {
                                const icon = $(this).find('i');
                                const childrenContainer = $(this).closest('li').children('ul');

                                if (childrenContainer.is(':visible')) {
                                    childrenContainer.hide();
                                    icon.removeClass('fa-minus-square').addClass('fa-plus-square');
                                } else {
                                    childrenContainer.show();
                                    icon.removeClass('fa-plus-square').addClass('fa-minus-square');
                                }
                            });
                        }

                        // 트리 HTML 생성 함수
                        function buildTreeHtml(categories, parentId = 0) {
                            const children = categories.filter(cat => cat.ct_pid == parentId);

                            if (children.length === 0) return '';

                            let html = '<ul class="tree-list">';

                            children.forEach(category => {
                                const hasChildren = categories.some(cat => cat.ct_pid == category.ct_id);

                                html += `<li>
            <div class="tree-node ${category.ct_show === 'N' ? 'text-muted' : ''}">
                ${hasChildren ? `<span class="tree-toggle"><i class="fa fa-minus-square"></i></span>` : '<span class="tree-no-children"><i class="fa fa-circle-o"></i></span>'}
                <span class="tree-label">${category.ct_name}</span>
                <span class="badge badge-${category.ct_show === 'Y' ? 'success' : 'secondary'} ml-2">${category.ct_show === 'Y' ? '노출' : '숨김'}</span>
            </div>
            ${buildTreeHtml(categories, category.ct_id)}
        </li>`;
                            });

                            html += '</ul>';
                            return html;
                        }

                        // 카테고리 데이터 내보내기
                        function exportCategories() {
                            $.ajax({
                                url: './category_update.php',
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    act: 'exportCategories'
                                },
                                success: function(response) {
                                    if (response.success && response.data) {
                                        // JSON 데이터를 문자열로 변환
                                        const jsonData = JSON.stringify(response.data, null, 2);

                                        // 다운로드 링크 생성
                                        const blob = new Blob([jsonData], { type: 'application/json' });
                                        const url = URL.createObjectURL(blob);
                                        const a = document.createElement('a');
                                        a.href = url;
                                        a.download = 'categories_' + formatDate(new Date()) + '.json';
                                        document.body.appendChild(a);
                                        a.click();
                                        document.body.removeChild(a);
                                        URL.revokeObjectURL(url);
                                    } else {
                                        alert(response.message || '데이터 내보내기에 실패했습니다.');
                                    }
                                },
                                error: function() {
                                    alert('서버 통신 중 오류가 발생했습니다.');
                                }
                            });
                        }

                        // 날짜 포맷 함수
                        function formatDate(date) {
                            const year = date.getFullYear();
                            const month = String(date.getMonth() + 1).padStart(2, '0');
                            const day = String(date.getDate()).padStart(2, '0');
                            return `${year}${month}${day}`;
                        }

                        // 카테고리 데이터 가져오기 모달 표시
                        function showImportModal() {
                            $('#importCategoryModal').modal('show');
                        }

                        // 파일에서 카테고리 데이터 가져오기
                        function importCategories() {
                            const fileInput = document.getElementById('categoryImportFile');
                            if (!fileInput.files || fileInput.files.length === 0) {
                                alert('파일을 선택해주세요.');
                                return;
                            }

                            const file = fileInput.files[0];
                            if (file.type !== 'application/json' && !file.name.endsWith('.json')) {
                                alert('JSON 파일만 가져올 수 있습니다.');
                                return;
                            }

                            const reader = new FileReader();
                            reader.onload = function(e) {
                                try {
                                    const data = JSON.parse(e.target.result);

                                    // 서버로 데이터 전송
                                    $.ajax({
                                        url: './category_update.php',
                                        type: 'POST',
                                        dataType: 'json',
                                        data: {
                                            act: 'importCategories',
                                            data: JSON.stringify(data)
                                        },
                                        success: function(response) {
                                            if (response.success) {
                                                alert(response.message || '카테고리 데이터를 성공적으로 가져왔습니다.');
                                                $('#importCategoryModal').modal('hide');
                                                f_get_category_list(); // 목록 새로고침
                                            } else {
                                                alert(response.message || '데이터 가져오기에 실패했습니다.');
                                            }
                                        },
                                        error: function() {
                                            alert('서버 통신 중 오류가 발생했습니다.');
                                        }
                                    });
                                } catch (error) {
                                    alert('잘못된 JSON 형식입니다: ' + error.message);
                                }
                            };
                            reader.readAsText(file);
                        }


                    </script>

                </div>
            </div>

        </div>
    </div>
    <!-- //END PAGE CONTENT CONTAINER -->
<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/foot.inc.php";
?>