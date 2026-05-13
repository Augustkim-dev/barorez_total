<?php
// list.php : 테이블 관리 리스트 페이지

include $_SERVER['DOCUMENT_ROOT']."/market/head.inc.php";
$chk_menu = 2;
$chk_sub_menu = 1; // 필요에 맞게 조정
include $_SERVER['DOCUMENT_ROOT']."/market/inc/header.menu.inc.php";

$page_title = "테이블 관리";
?>

    <div class="container-fluid py-4">
        <!-- 페이지 제목 + 테이블 추가 버튼 -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
            <div>
                <h1 class="h3 fw-bold mb-1"><?= $page_title ?></h1>
                <p class="text-muted mb-0">매장 내 테이블 현황을 실시간으로 확인하세요.</p>
            </div>
            <button type="button" class="btn btn-primary" id="btnAddTable">
                <i class="bi bi-plus-lg me-1"></i> 테이블 추가
            </button>
        </div>

        <!-- 상단 요약 카드 영역 -->
        <div id="table_summary" class="mb-4">
            <div class="row g-3">
                <div class="col-12 text-center py-4 text-muted">
                    <div class="spinner-border text-primary mb-2" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div>테이블 정보를 불러오는 중입니다...</div>
                </div>
            </div>
        </div>

        <!-- 테이블 카드 리스트 -->
        <div id="table_list" class="row g-3">
            <div class="col-12 text-center py-5 text-muted">
                <div class="spinner-border text-primary mb-2" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div>테이블 정보를 불러오는 중입니다...</div>
            </div>
        </div>

    </div> <!-- /.container-fluid -->

    <!-- QR 미리보기 모달 -->
    <div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content" style="border-radius: 12px;">
                <div class="modal-header">
                    <h5 class="modal-title" id="qrModalLabel">QR 코드</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="qrPreviewImg" src="" alt="QR 미리보기" class="img-fluid mb-3">
                    <a id="qrDownloadLink" href="#" class="btn btn-primary w-100" download>
                        <i class="bi bi-download me-1"></i> 다운로드
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ✅ 테이블 추가 모달 -->
    <div class="modal fade" id="tableModal" tabindex="-1" aria-labelledby="tableModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 12px;">
                <div class="modal-header">
                    <h5 class="modal-title" id="tableModalLabel">테이블 추가</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="닫기"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="tb_name" class="form-label">테이블명</label>
                        <input type="text" class="form-control" id="tb_name" placeholder="예) 테이블 1">
                    </div>
                    <div class="mb-3">
                        <label for="tb_seats" class="form-label">좌석 수</label>
                        <input type="number" class="form-control" id="tb_seats" min="1" max="20" value="4">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">취소</button>
                    <button type="button" class="btn btn-primary" id="btnSaveTable">저장</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 초기 로딩
            loadTableList();

            // ✅ 테이블 추가 모달 관련 변수
            const tableModalEl   = document.getElementById('tableModal');
            const tableModal     = new bootstrap.Modal(tableModalEl);
            const tbNameInput    = document.getElementById('tb_name');
            const tbSeatsInput   = document.getElementById('tb_seats');
            const btnSaveTable   = document.getElementById('btnSaveTable');

            // ✅ 테이블 추가 버튼 → 모달 열기
            document.getElementById('btnAddTable').addEventListener('click', function () {
                tbNameInput.value  = '';
                tbSeatsInput.value = 4;
                btnSaveTable.disabled = false;
                btnSaveTable.textContent = '저장';
                tableModal.show();
            });

            // ✅ 테이블 추가 모달 저장 버튼
            btnSaveTable.addEventListener('click', function () {
                const name  = tbNameInput.value.trim();
                const seats = parseInt(tbSeatsInput.value, 10);

                if (name === '') {
                    alert('테이블명을 입력해 주세요.');
                    tbNameInput.focus();
                    return;
                }
                if (!seats || seats <= 0) {
                    alert('좌석 수를 올바르게 입력해 주세요.');
                    tbSeatsInput.focus();
                    return;
                }

                const formData = new FormData();
                formData.append('act', 'add_table');
                formData.append('tb_name', name);
                formData.append('tb_seats', seats);

                btnSaveTable.disabled = true;
                btnSaveTable.textContent = '저장 중...';

                $.ajax({
                    url: './update.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function (result) {
                        if (!result.success) {
                            alert(result.message || '테이블 추가 중 오류가 발생했습니다.');
                            btnSaveTable.disabled = false;
                            btnSaveTable.textContent = '저장';
                            return;
                        }

                        tableModal.hide();
                        loadTableList();
                    },
                    error: function (xhr, status, error) {
                        console.error('add_table AJAX error:', status, error, xhr.responseText);
                        alert('테이블 추가 중 오류가 발생했습니다.');
                        btnSaveTable.disabled = false;
                        btnSaveTable.textContent = '저장';
                    }
                });
            });

            // QR 버튼(생성/보기) 이벤트 위임
            document.addEventListener('click', function (e) {
                const generateBtn = e.target.closest('.btn-generate-qr');
                const viewBtn     = e.target.closest('.btn-view-qr');
                const deleteBtn   = e.target.closest('.btn-delete-table'); // ✅ 추가

                // 1) QR 생성 버튼
                if (generateBtn) {
                    const tableId   = generateBtn.dataset.tableId;
                    const tableName = generateBtn.dataset.tableName;

                    if (!tableId) return;

                    const formData = new FormData();
                    formData.append('act', 'generate_qr');
                    formData.append('table_id', tableId);
                    formData.append('table_name', tableName);

                    generateBtn.disabled = true;
                    generateBtn.innerText = '생성 중...';

                    $.ajax({
                        url: './update.php',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: 'json',
                        success: function (result) {
                            if (!result.success) {
                                alert(result.message || 'QR 생성 중 오류가 발생했습니다.');
                                generateBtn.disabled = false;
                                generateBtn.innerText = 'QR 코드 생성';
                                return;
                            }

                            generateBtn.classList.remove('btn-generate-qr', 'btn-outline-secondary');
                            generateBtn.classList.add('btn-view-qr', 'btn-secondary');
                            generateBtn.disabled = false;
                            generateBtn.innerText = 'QR 코드 보기';
                            generateBtn.dataset.qrUrl     = result.qr_url;
                            generateBtn.dataset.tableName = result.table_name;

                            openQrModal(result.table_name, result.qr_url);
                        },
                        error: function (xhr, status, error) {
                            console.error('generate_qr AJAX error:', status, error, xhr.responseText);
                            alert('QR 생성 중 오류가 발생했습니다.');
                            generateBtn.disabled = false;
                            generateBtn.innerText = 'QR 코드 생성';
                        }
                    });

                    return;
                }

                // 2) QR 보기 버튼
                if (viewBtn) {
                    const tableName = viewBtn.dataset.tableName;
                    const qrUrl     = viewBtn.dataset.qrUrl;

                    if (!qrUrl) {
                        alert('QR 코드 정보가 없습니다. 다시 생성해 주세요.');
                        return;
                    }

                    openQrModal(tableName, qrUrl);
                }

                if (deleteBtn) {
                    const tableId   = deleteBtn.dataset.tableId;
                    const tableName = deleteBtn.dataset.tableName || '';

                    if (!tableId) return;

                    if (!confirm(`'${tableName}' 테이블을 삭제하시겠습니까?\n생성된 QR 코드 이미지도 함께 삭제됩니다.`)) {
                        return;
                    }

                    $.ajax({
                        url: './update.php',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            act: 'delete_table',    // ✅ 백엔드 act
                            table_id: tableId
                        },
                        success: function (res) {
                            if (!res || !res.success) {
                                alert(res && res.message ? res.message : '테이블 삭제 중 오류가 발생했습니다.');
                                return;
                            }
                            alert(res.message || '테이블이 삭제되었습니다.');
                            loadTableList(); // ✅ 리스트 다시 로딩
                        },
                        error: function (xhr, status, error) {
                            console.error('delete_table AJAX error:', status, error, xhr.responseText);
                            alert('테이블 삭제 중 네트워크 오류가 발생했습니다.');
                        }
                    });
                }
            });
        });

        // 상태 메타 (JS 쪽)
        const STATUS_META = {
            empty: {
                label: '빈자리',
                dotClass: 'bg-success',
                bgColor: '#EAF7EC',
            },
            ordering: {
                label: '주문중',
                dotClass: 'bg-info',
                bgColor: '#E7F0FF',
            },
            reserved: {
                label: '예약됨',
                dotClass: 'bg-danger',
                bgColor: '#F1E7FF',
            },
            payment: {
                label: '결제대기',
                dotClass: 'bg-warning',
                bgColor: '#F8EDE1',
            },
        };

        function escapeHtml(str) {
            if (str == null) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        // ✅ 테이블 리스트 로드 (JSON 기반)
        function loadTableList() {
            const summaryContainer = document.getElementById('table_summary');
            const listContainer    = document.getElementById('table_list');

            // 로딩 표시
            summaryContainer.innerHTML = `
            <div class="row g-3">
                <div class="col-12 text-center py-4 text-muted">
                    <div class="spinner-border text-primary mb-2" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div>테이블 정보를 불러오는 중입니다...</div>
                </div>
            </div>`;
            listContainer.innerHTML = `
            <div class="col-12 text-center py-5 text-muted">
                <div class="spinner-border text-primary mb-2" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div>테이블 정보를 불러오는 중입니다...</div>
            </div>`;

            $.ajax({
                url: './update.php',
                type: 'POST',
                data: { act: 'list' },
                dataType: 'json',
                success: function (res) {
                    if (!res.success) {
                        summaryContainer.innerHTML = `
                        <div class="row g-3">
                            <div class="col-12 text-center py-4 text-danger">
                                ${escapeHtml(res.message || '요약 정보 로드 중 오류가 발생했습니다.')}
                            </div>
                        </div>`;
                        listContainer.innerHTML = `
                        <div class="col-12 text-center py-5 text-danger">
                            ${escapeHtml(res.message || '테이블 리스트 로드 중 오류가 발생했습니다.')}
                        </div>`;
                        return;
                    }

                    const summary = res.summary || { empty:0, ordering:0, reserved:0, payment:0 };
                    const tables  = res.tables || [];

                    // 요약 카드 렌더링
                    summaryContainer.innerHTML = renderSummaryCards(summary);

                    // 리스트 렌더링
                    listContainer.innerHTML = renderTableCards(tables);
                },
                error: function (xhr, status, error) {
                    console.error('list AJAX error:', status, error, xhr.responseText);
                    summaryContainer.innerHTML = `
                    <div class="row g-3">
                        <div class="col-12 text-center py-4 text-danger">
                            네트워크 오류가 발생했습니다.
                        </div>
                    </div>`;
                    listContainer.innerHTML = `
                    <div class="col-12 text-center py-5 text-danger">
                        네트워크 오류가 발생했습니다.
                    </div>`;
                }
            });
        }

        function renderSummaryCards(summary) {
            return `
        <div class="row g-3 mb-3">
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex justify-content-between align-items-center py-3">
                        <div>
                            <div class="small text-muted mb-1">빈자리</div>
                            <div class="h4 mb-0">${summary.empty || 0}</div>
                        </div>
                        <span class="rounded-circle d-inline-block ${STATUS_META.empty.dotClass}"
                              style="width:10px;height:10px;"></span>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex justify-content-between align-items-center py-3">
                        <div>
                            <div class="small text-muted mb-1">주문중</div>
                            <div class="h4 mb-0">${summary.ordering || 0}</div>
                        </div>
                        <span class="rounded-circle d-inline-block ${STATUS_META.ordering.dotClass}"
                              style="width:10px;height:10px;"></span>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex justify-content-between align-items-center py-3">
                        <div>
                            <div class="small text-muted mb-1">예약됨</div>
                            <div class="h4 mb-0">${summary.reserved || 0}</div>
                        </div>
                        <span class="rounded-circle d-inline-block ${STATUS_META.reserved.dotClass}"
                              style="width:10px;height:10px;"></span>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex justify-content-between align-items-center py-3">
                        <div>
                            <div class="small text-muted mb-1">결제대기</div>
                            <div class="h4 mb-0">${summary.payment || 0}</div>
                        </div>
                        <span class="rounded-circle d-inline-block ${STATUS_META.payment.dotClass}"
                              style="width:10px;height:10px;"></span>
                    </div>
                </div>
            </div>
        </div>`;
        }

        function renderTableCards(tables) {
            if (!tables.length) {
                return `
            <div class="col-12 text-center py-5 text-muted">
                등록된 테이블이 없습니다.
            </div>`;
            }

            return tables.map(function (t) {
                const status = t.status || 'empty';
                const meta   = STATUS_META[status] || STATUS_META.empty;

                let statusHtml = '';

                if (status === 'empty') {
                    statusHtml = `<div class="fw-semibold">빈자리</div>`;
                } else if (status === 'ordering') {
                    statusHtml = `
                <div class="fw-semibold mb-2">주문중</div>
                <div class="d-flex justify-content-between text-muted mb-1">
                    <span><i class="bi bi-clock me-1"></i>${t.elapsed || 0}분</span>
                </div>
                <div class="fw-bold">
                    ${(t.amount ? Number(t.amount).toLocaleString() : '0')}원
                </div>`;
                } else if (status === 'reserved') {
                    statusHtml = `
                <div class="fw-semibold mb-2">예약됨</div>
                <div class="mb-1">${escapeHtml(t.reserved_name || '')}</div>
                <div class="text-muted">${escapeHtml(t.reserved_time || '')}</div>`;
                } else if (status === 'payment') {
                    statusHtml = `
                <div class="fw-semibold mb-2">결제대기</div>
                <div class="fw-bold">
                    ${(t.amount ? Number(t.amount).toLocaleString() : '0')}원
                </div>`;
                }

                const name  = escapeHtml(t.name || '');
                const seats = t.seats ? parseInt(t.seats, 10) : 0;
                const id    = t.id ? parseInt(t.id, 10) : 0;

                // ✅ QR 존재 여부
                const hasQr = !!t.qr_generated && !!t.qr_url;
                let qrBtnHtml = '';

                if (hasQr) {
                    // 이미 QR 생성된 경우 → 보기 버튼
                    const qrUrlEsc = escapeHtml(t.qr_url);
                    qrBtnHtml = `
                <button type="button"
                        class="btn btn-secondary btn-sm btn-view-qr"
                        data-table-name="${name}"
                        data-qr-url="${qrUrlEsc}">
                    <i class="bi bi-qr-code me-1"></i>QR 코드 보기
                </button>`;
                } else {
                    // 아직 QR 없음 → 생성 버튼
                    qrBtnHtml = `
                <button type="button"
                        class="btn btn-outline-secondary btn-sm btn-generate-qr"
                        data-table-name="${name}"
                        data-table-id="${id}">
                    <i class="bi bi-qr-code me-1"></i>QR 코드 생성
                </button>`;
                }

                return `
        <div class="col-xl-3 col-lg-4 col-md-6 mb-5">
            <div class="card border-0 shadow-sm h-100"
                 style="background-color: ${meta.bgColor};">
                <div class="card-body d-flex flex-column py-3">

                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="fw-bold mb-1">${name}</h6>
                            <div class="small text-muted">
                                <i class="bi bi-people me-1"></i>${seats}인석
                            </div>
                        </div>
                        <span class="rounded-circle d-inline-block ${meta.dotClass}"
                              style="width:10px;height:10px;"></span>
                    </div>

                    <hr class="my-2">

                    <div class="mt-1 small text-dark">
                        ${statusHtml}
                    </div>

                    <div class="mt-auto pt-3 d-flex justify-content-end">
                        <button type="button"
                                class="btn btn-outline-danger btn-sm btn-delete-table"
                                data-table-id="${id}"
                                data-table-name="${name}">
                            <i class="bi bi-trash me-1"></i>삭제
                        </button>
                        ${qrBtnHtml}
                    </div>

                </div>
            </div>
        </div>`;
            }).join('');
        }

        // QR 모달 열기
        function openQrModal(tableName, qrUrl) {
            const img   = document.getElementById('qrPreviewImg');
            const link  = document.getElementById('qrDownloadLink');
            const title = document.getElementById('qrModalLabel');

            const urlWithCacheBust = qrUrl + '?v=' + Date.now();

            img.src = urlWithCacheBust;
            title.textContent = (tableName || '테이블') + ' QR 코드';
            link.href = qrUrl;
            link.download = (tableName || 'table').replace(/\s+/g, '_') + '_qr.png';

            const modal = new bootstrap.Modal(document.getElementById('qrModal'));
            modal.show();
        }
    </script>
<?php
include $_SERVER['DOCUMENT_ROOT']."/market/foot.inc.php";
