<?php
// /market/qa/list.php

include $_SERVER['DOCUMENT_ROOT']."/market/head.inc.php";
$chk_menu     = 99;
$chk_sub_menu = 2;
include $_SERVER['DOCUMENT_ROOT']."/market/inc/header.menu.inc.php";

$qa_img_url = "/data/qa/";
?>

    <style>
        .upload-container {
            display: inline-block;
            position: relative;
            width: 140px;
            height: 140px;
        }

        .upload-box {
            position: relative;
            width: 140px;
            height: 140px;
            border: 1px dashed #ced4da;
            border-radius: 0.75rem;
            background-color: #f8f9fa;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            background-size: cover;          /* 기본 contain */
            background-position: center;
            background-repeat: no-repeat;
            overflow: hidden;
            transition: background-color .15s ease-in-out, border-color .15s ease-in-out;
        }

        .upload-box:hover {
            background-color: #f1f3f5;
            border-color: #adb5bd;
        }

        .upload-box .upload-content {
            text-align: center;
            color: #adb5bd;
        }

        .upload-box .upload-content .plus {
            font-size: 24px;
            line-height: 1;
        }

        .upload-box .upload-content .text {
            font-size: 0.85rem;
        }

        .upload-box.has-image .upload-content {
            display: none;
        }

        .upload-box .remove-btn {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            border: none;
            background: rgba(0,0,0,0.6);
            color: #fff;
            font-size: 16px;
            line-height: 1;
            display: none;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            padding: 0;
        }

        .upload-box.has-image .remove-btn {
            display: flex;
        }
    </style>

    <div class="content" id="content">
        <div class="page-heading">
            <div class="page-heading__container">
                <h1 class="title">1:1 문의관리</h1>
                <p class="caption">회원들의 1:1 문의를 확인하고 관리할 수 있습니다.</p>
            </div>
        </div>

        <div class="container-fluid">

            <!-- 검색 / 필터 / 문의하기 버튼 -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body d-flex flex-column flex-xl-row gap-3 align-items-xl-center">

                    <!-- 상태 필터 -->
                    <div class="d-flex align-items-center gap-2">
                        <label for="filterStatus" class="small text-muted mb-0">답변 상태</label>
                        <select id="filterStatus" class="form-select form-select-sm" style="min-width: 140px;">
                            <option value="all">전체</option>
                            <option value="pending">답변 대기</option>
                            <option value="answered">답변 완료</option>
                        </select>
                    </div>

                    <!-- 검색어 -->
                    <div class="flex-grow-1 w-100">
                        <div class="position-relative">
                        <span class="position-absolute top-50 start-0 translate-middle-y ms-3 text-muted">
                            <i class="bi bi-search"></i>
                        </span>
                            <input
                                type="text"
                                id="searchKeyword"
                                class="form-control ps-5"
                                placeholder="제목 또는 내용으로 검색..."
                            >
                        </div>
                    </div>

                    <!-- 버튼 영역 -->
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnSearch">
                            검색
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnReset">
                            초기화
                        </button>
                        <button type="button" class="btn btn-sm btn-dark" id="btnQaWrite">
                            문의하기
                        </button>
                    </div>
                </div>
            </div>

            <!-- 리스트 카드 -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                            <tr>
                                <th style="width:80px;" class="text-center">번호</th>
                                <th>제목</th>
                                <th style="width:120px;" class="text-center">답변 상태</th>
                                <th style="width:180px;" class="text-center">문의일시</th>
                                <th style="width:180px;" class="text-center">관리</th>
                            </tr>
                            </thead>
                            <tbody id="qaTableBody">
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <div class="spinner-border text-primary mb-3" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <div>데이터를 불러오는 중입니다...</div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- 페이징 -->
                    <div class="p-3 d-flex justify-content-center" id="qaPagination">
                        <!-- AJAX로 삽입 -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 1:1 문의 작성 / 수정 모달 -->
    <div class="modal fade" id="qaWriteModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content rounded-4 border-0">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="qaModalTitle">1:1 문의하기</h5>
                    <!-- Bootstrap4 방식 close 버튼 -->
                    <button type="button" class="close" data-dismiss="modal" aria-label="닫기">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="qaWriteForm" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="qa_idx" id="qa_idx" value="">

                        <div class="mb-3">
                            <label for="rt_title" class="form-label">제목</label>
                            <input type="text" class="form-control" id="rt_title" name="rt_title" maxlength="255"
                                   placeholder="제목을 입력해 주세요.">
                        </div>

                        <div class="mb-3">
                            <label for="rt_description" class="form-label">문의 내용</label>
                            <textarea class="form-control" id="rt_description" name="rt_description" rows="6"
                                      placeholder="상세 내용을 입력해 주세요."></textarea>
                        </div>

                        <!-- 이미지 업로드 (5장) -->
                        <div class="mb-3">
                            <label class="form-label">이미지 첨부 (최대 5장)</label>
                            <div class="small text-muted mb-2">
                                박스를 클릭하면 이미지를 선택할 수 있습니다. 수정 시 박스를 다시 클릭하면 이미지를 변경할 수 있습니다.
                            </div>
                            <div class="row g-3">
                                <?php for ($i=1; $i<=5; $i++): ?>
                                    <div class="pl-3">
                                        <div class="upload-container">
                                            <div class="upload-box"
                                                 id="uploadRtImageTrigger<?= $i ?>"
                                                 data-existing-image="">
                                                <div class="upload-content">
                                                    <div class="plus">+</div>
                                                    <div class="text">Upload (<?= $i ?>)</div>
                                                </div>
                                                <button type="button" class="remove-btn">×</button>
                                            </div>
                                        </div>
                                        <input type="file"
                                               class="filepond d-none"
                                               name="rt_img<?= $i ?>"
                                               id="rt_img<?= $i ?>"
                                               accept="image/*">
                                        <input type="hidden"
                                               name="rt_img<?= $i ?>_delete"
                                               id="rt_img<?= $i ?>_delete"
                                               value="N">
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <div class="modal-footer border-0 px-0">
                            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">닫기</button>
                            <button type="submit" class="btn btn-dark" id="qaSubmitBtn">문의하기</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const QA_PAGE_SIZE    = 10;
        const QA_IMG_BASE_URL = '<?= $qa_img_url ?>';

        let qaIsSubmitting = false;
        let qaCurrentPage  = 1;

        // ================================
        // 업로드 박스 공통 함수
        // ================================
        function setUploadBoxPreview($box, url) {
            $box.css({
                'background-image': "url('" + url + "')",
                'background-size': 'contain',     // 🔁 contain 으로 고정
                'background-position': 'center',
                'background-repeat': 'no-repeat'
            });
            $box.addClass('has-image');
        }

        function resetUploadBox($box) {
            $box.css('background-image', 'none');
            $box.removeClass('has-image');
            const $content = $box.find('.upload-content');
            $content.show();
        }

        function initUploadBox(idx) {
            const $box     = $('#uploadRtImageTrigger' + idx);
            const $input   = $('#rt_img' + idx);
            const $delFlag = $('#rt_img' + idx + '_delete');

            if ($box.length === 0 || $input.length === 0) return;

            const existing = $box.data('existing-image');
            if (existing) {
                setUploadBoxPreview($box, existing);
                $box.find('.upload-content').hide();
            } else {
                resetUploadBox($box);
            }

            // 박스 클릭 → 파일 선택 (X 버튼 제외)
            $box.off('click').on('click', function (e) {
                if ($(e.target).hasClass('remove-btn')) return;
                $input.click();
            });

            // 파일 선택 시 미리보기
            $input.off('change').on('change', function () {
                const file = this.files[0];
                if (!file) {
                    if (!$box.data('existing-image')) {
                        resetUploadBox($box);
                    }
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (e) {
                    setUploadBoxPreview($box, e.target.result);
                    $box.find('.upload-content').hide();
                    if ($delFlag.length) $delFlag.val('N');
                };
                reader.readAsDataURL(file);
            });

            // 삭제 버튼
            const $removeBtn = $box.find('.remove-btn');
            $removeBtn.off('click').on('click', function (e) {
                e.stopPropagation();
                $input.val('');
                $box.data('existing-image', '');
                resetUploadBox($box);
                if ($delFlag.length) $delFlag.val('Y');
            });
        }

        function initAllUploadBoxes() {
            for (let i=1; i<=5; i++) {
                initUploadBox(i);
            }
        }

        function resetQaForm() {
            const $form = $('#qaWriteForm');
            $form[0].reset();
            $('#qa_idx').val('');

            for (let i=1; i<=5; i++) {
                const $box     = $('#uploadRtImageTrigger' + i);
                const $input   = $('#rt_img' + i);
                const $delFlag = $('#rt_img' + i + '_delete');

                $box.data('existing-image', '');
                resetUploadBox($box);
                $input.val('');
                $delFlag.val('N');
            }
        }

        function openQaWrite() {
            resetQaForm();
            $('#qaModalTitle').text('1:1 문의하기');
            $('#qaSubmitBtn').text('문의하기');
            $('#qaWriteModal').modal('show');
        }

        function openQaEdit(idx) {
            resetQaForm();
            $('#qa_idx').val(idx);
            $('#qaModalTitle').text('1:1 문의 수정');
            $('#qaSubmitBtn').text('수정하기');

            $.ajax({
                url: './update.php',
                type: 'POST',
                data: {
                    act: 'get',
                    qa_idx: idx
                },
                dataType: 'json',
                success: function (res) {
                    if (!res || !res.success || !res.data) {
                        alert(res && res.message ? res.message : '문의 내용을 불러오지 못했습니다.');
                        return;
                    }
                    const d = res.data;
                    $('#rt_title').val(d.rt_title || '');
                    $('#rt_description').val(d.rt_description || '');

                    // ✅ 기존 이미지 적용 (리사이즈 rs_ 우선)
                    for (let i=1; i<=5; i++) {
                        const field   = 'rt_img' + i;
                        const fieldRs = field + '_rs';

                        const $box    = $('#uploadRtImageTrigger' + i);
                        const $input  = $('#rt_img' + i);
                        const $delFlg = $('#rt_img' + i + '_delete');

                        $input.val('');
                        $delFlg.val('N');

                        const filename = d[fieldRs] || d[field];  // rs_가 있으면 우선 사용
                        if (filename) {
                            const url = QA_IMG_BASE_URL + filename;
                            $box.data('existing-image', url);
                            setUploadBoxPreview($box, url);
                            $box.find('.upload-content').hide();
                        } else {
                            $box.data('existing-image', '');
                            resetUploadBox($box);
                        }
                    }

                    $('#qaWriteModal').modal('show');
                },
                error: function (xhr, status, error) {
                    console.error(error);
                    alert('문의 내용을 불러오는 중 오류가 발생했습니다.');
                }
            });
        }

        function submitQaForm() {
            if (qaIsSubmitting) return;
            qaIsSubmitting = true;

            const formEl = document.getElementById('qaWriteForm');
            const fd     = new FormData(formEl);
            fd.append('act', 'save');

            $.ajax({
                url: './update.php',
                type: 'POST',
                data: fd,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (res) {
                    qaIsSubmitting = false;

                    if (!res || !res.success) {
                        alert(res && res.message ? res.message : '저장 중 오류가 발생했습니다.');
                        return;
                    }

                    alert(res.message || '저장되었습니다.');
                    $('#qaWriteModal').modal('hide');
                    loadQaList(qaCurrentPage || 1);
                },
                error: function (xhr, status, error) {
                    qaIsSubmitting = false;
                    console.error(error);
                    alert('저장 중 오류가 발생했습니다.');
                }
            });
        }

        function deleteQa(idx) {
            if (!confirm('해당 문의를 삭제하시겠습니까?')) return;

            $.ajax({
                url: './update.php',
                type: 'POST',
                data: {
                    act: 'delete',
                    qa_idx: idx
                },
                dataType: 'json',
                success: function (res) {
                    if (!res || !res.success) {
                        alert(res && res.message ? res.message : '삭제 중 오류가 발생했습니다.');
                        return;
                    }
                    alert(res.message || '삭제되었습니다.');
                    loadQaList(qaCurrentPage || 1);
                },
                error: function (xhr, status, error) {
                    console.error(error);
                    alert('삭제 중 오류가 발생했습니다.');
                }
            });
        }

        function loadQaList(page) {
            qaCurrentPage = page;

            const $tbody  = $('#qaTableBody');
            const $paging = $('#qaPagination');

            $tbody.html(`
            <tr>
                <td colspan="5" class="text-center text-muted py-4">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div>데이터를 불러오는 중입니다...</div>
                </td>
            </tr>
        `);
            $paging.empty();

            $.ajax({
                url: './update.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    act: 'list',
                    obj_pg: page,
                    obj_limit_num: QA_PAGE_SIZE,
                    obj_status: $('#filterStatus').val(),
                    obj_search_txt: $('#searchKeyword').val()
                },
                success: function (res) {
                    if (!res || !res.success) {
                        $tbody.html(`
                        <tr>
                            <td colspan="5" class="text-center text-danger py-4">
                                ${(res && res.message) ? res.message : '데이터 로드 중 오류가 발생했습니다.'}
                            </td>
                        </tr>
                    `);
                        return;
                    }

                    const list  = res.list || [];
                    const pager = res.pagination || { pg: 1, total_pages: 1 };

                    if (!list.length) {
                        $tbody.html(`
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                등록된 1:1 문의가 없습니다.
                            </td>
                        </tr>
                    `);
                    } else {
                        let html = '';
                        list.forEach(function (row) {
                            const statusBadge = row.rt_status === 'answered'
                                ? '<span class="badge bg-success rounded-pill">답변 완료</span>'
                                : '<span class="badge bg-secondary rounded-pill">답변 대기</span>';

                            html += `
                            <tr>
                                <td class="text-center">${row.no}</td>
                                <td style="cursor: pointer;" onclick="location.href='./form.php?id=${row.idx}'">${escapeHtml(row.rt_title || '')}</td>
                                <td class="text-center">${statusBadge}</td>
                                <td class="text-center">${row.created_at || ''}</td>
                                <td class="text-center">
                                    <button type="button"
                                            class="btn btn-sm btn-outline-secondary me-1"
                                            onclick="openQaEdit(${row.idx})">
                                        수정
                                    </button>
                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="deleteQa(${row.idx})">
                                        삭제
                                    </button>
                                </td>
                            </tr>
                        `;
                        });
                        $tbody.html(html);
                    }

                    // 페이징
                    let pHtml       = '';
                    const cur       = pager.pg || 1;
                    const totalPage = pager.total_pages || 1;
                    const prev      = cur > 1 ? cur - 1 : 1;
                    const next      = cur < totalPage ? cur + 1 : totalPage;

                    if (totalPage > 1) {
                        pHtml += '<nav><ul class="pagination mb-0">';
                        pHtml += `
                        <li class="page-item ${(cur <= 1) ? 'disabled' : ''}">
                            <a class="page-link" href="javascript:void(0);" onclick="loadQaList(${prev})">이전</a>
                        </li>
                    `;
                        for (let i=1; i<=totalPage; i++) {
                            pHtml += `
                            <li class="page-item ${(i === cur) ? 'active' : ''}">
                                <a class="page-link" href="javascript:void(0);" onclick="loadQaList(${i})">${i}</a>
                            </li>
                        `;
                        }
                        pHtml += `
                        <li class="page-item ${(cur >= totalPage) ? 'disabled' : ''}">
                            <a class="page-link" href="javascript:void(0);" onclick="loadQaList(${next})">다음</a>
                        </li>
                    `;
                        pHtml += '</ul></nav>';
                    }
                    $paging.html(pHtml);
                },
                error: function (xhr, status, error) {
                    console.error(error);
                    $('#qaTableBody').html(`
                    <tr>
                        <td colspan="5" class="text-center text-danger py-4">
                            네트워크 오류가 발생했습니다.
                        </td>
                    </tr>
                `);
                }
            });
        }

        function escapeHtml(str) {
            if (str === null || str === undefined) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        // ================================
        // DOM Ready
        // ================================
        $(function () {
            // 업로드 박스 초기화
            initAllUploadBoxes();

            // 문의하기 버튼
            $('#btnQaWrite').on('click', function () {
                openQaWrite();
            });

            // 검색 버튼
            $('#btnSearch').on('click', function () {
                loadQaList(1);
            });

            // 초기화 버튼
            $('#btnReset').on('click', function () {
                $('#filterStatus').val('all');
                $('#searchKeyword').val('');
                loadQaList(1);
            });

            // 엔터 검색
            $('#searchKeyword').on('keypress', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    loadQaList(1);
                }
            });

            // 상태 변경 시 자동 검색
            $('#filterStatus').on('change', function () {
                loadQaList(1);
            });

            // 폼 submit → AJAX
            $('#qaWriteForm').on('submit', function (e) {
                e.preventDefault();
                submitQaForm();
            });

            // 초기 리스트 로드
            loadQaList(1);
        });
    </script>

<?php
include $_SERVER['DOCUMENT_ROOT']."/market/foot.inc.php";
?>
