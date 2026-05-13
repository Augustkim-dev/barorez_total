<?php
// list.php: 포장 주문 관리 리스트 페이지

include $_SERVER['DOCUMENT_ROOT']."/market/head.inc.php";
$chk_menu = 5;          // 메뉴 번호는 프로젝트에 맞게 조정
$chk_sub_menu = 2;      // 서브 메뉴 번호도 필요에 맞게
include $_SERVER['DOCUMENT_ROOT']."/market/inc/header.menu.inc.php";

// 기본 변수
$page_title    = "포장 주문 관리";
$default_pg    = 1;
$default_limit = 12;

$search_txt = $_GET['search_txt'] ?? '';
?>
    <div class="container-fluid py-4">

        <!-- 페이지 제목 -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
            <div>
                <h1 class="h3 fw-bold mb-1"><?= $page_title ?></h1>
                <p class="text-muted mb-0">픽업 주문을 확인하고 관리하세요.</p>
            </div>
        </div>

        <!-- 상단 요약 카드 -->
        <div id="summary_box" class="mb-4">
            <div class="row g-3">
                <div class="col-12 text-center py-4 text-muted">
                    <div class="spinner-border text-primary mb-2" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div>포장 주문 정보를 불러오는 중입니다...</div>
                </div>
            </div>
        </div>

        <!-- 상태 탭 + 검색 -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body d-flex flex-column flex-lg-row align-items-lg-center gap-3">

                <!-- 진행중 / 완료 탭 -->
                <div class="d-flex flex-wrap gap-2">
                    <button type="button"
                            class="btn btn-sm btn-dark takeout-status-tab"
                            data-tab="progress">
                        진행중 주문
                    </button>
                    <button type="button"
                            class="btn btn-sm btn-outline-secondary takeout-status-tab"
                            data-tab="done">
                        완료된 주문
                    </button>
                </div>

                <!-- 검색 -->
                <div class="flex-grow-1 w-100">
                    <div class="position-relative">
                    <span class="position-absolute top-50 start-0 translate-middle-y ms-3 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                        <input
                                type="text"
                                id="takeoutSearchInput"
                                class="form-control ps-5"
                                placeholder="주문번호, 고객명, 전화번호 검색..."
                                value="<?= htmlspecialchars($search_txt, ENT_QUOTES) ?>"
                        >
                    </div>
                </div>
            </div>
        </div>

        <!-- 리스트 컨테이너 -->
        <div id="list_box_takeout" class="row g-3">
            <div class="col-12 text-center py-5 text-muted">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div>데이터를 불러오는 중입니다...</div>
            </div>
        </div>

        <!-- 페이징 (필요 시 사용) -->
        <div id="paging_box_takeout" class="mt-4 d-flex justify-content-center"></div>

    </div><!-- /.container-fluid -->

    <script>
        let currentTakeoutTab = 'progress';            // 진행중 / 완료
        const TAKEOUT_LIMIT   = <?= (int)$default_limit ?>;

        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('takeoutSearchInput');

            if (searchInput) {
                searchInput.addEventListener('keypress', function (e) {
                    if (e.key === 'Enter') {
                        f_get_takeout_list(1);
                    }
                });
            }

            // 탭 클릭
            document.addEventListener('click', function (e) {
                const tabBtn = e.target.closest('.takeout-status-tab');
                if (tabBtn) {
                    currentTakeoutTab = tabBtn.dataset.tab || 'progress';

                    document.querySelectorAll('.takeout-status-tab').forEach(btn => {
                        if (btn.dataset.tab === currentTakeoutTab) {
                            btn.classList.remove('btn-outline-secondary');
                            btn.classList.add('btn-dark');
                        } else {
                            btn.classList.remove('btn-dark');
                            btn.classList.add('btn-outline-secondary');
                        }
                    });

                    f_get_takeout_list(1);
                }
            });

            // 초기 로드
            f_get_takeout_list(<?= (int)$default_pg ?>);
        });

        // ----------------------------------------------------
        // 포장 주문 리스트 AJAX 로드
        // ----------------------------------------------------
        function f_get_takeout_list(pg) {
            const listContainer    = document.getElementById('list_box_takeout');
            const pagingContainer  = document.getElementById('paging_box_takeout');
            const summaryContainer = document.getElementById('summary_box');
            const searchTxt        = document.getElementById('takeoutSearchInput').value;

            listContainer.innerHTML = `
            <div class="col-12 text-center py-5 text-muted">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div>데이터를 불러오는 중입니다...</div>
            </div>`;
            summaryContainer.innerHTML = `
            <div class="row g-3">
                <div class="col-12 text-center py-4 text-muted">
                    <div class="spinner-border text-primary mb-2" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div>포장 주문 정보를 불러오는 중입니다...</div>
                </div>
            </div>`;
            pagingContainer.innerHTML = '';

            const formData = new FormData();
            formData.append('act', 'list');
            formData.append('obj_pg', pg);
            formData.append('obj_limit_num', TAKEOUT_LIMIT);
            formData.append('obj_search_txt', searchTxt);
            formData.append('obj_tab', currentTakeoutTab); // progress | done

            fetch('./update.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.text())
                .then(data => {
                    const parser = new DOMParser();
                    const doc    = parser.parseFromString(data, 'text/html');

                    const summaryContent = doc.getElementById('summary_content');
                    const listContent    = doc.getElementById('list_content');
                    const pagingContent  = doc.getElementById('paging_content');

                    if (summaryContent) summaryContainer.innerHTML = summaryContent.innerHTML;

                    if (listContent) {
                        listContainer.innerHTML = listContent.innerHTML;
                    } else {
                        listContainer.innerHTML = `
                        <div class="col-12 text-center py-5 text-danger">
                            데이터 로드 중 오류가 발생했습니다.
                        </div>`;
                    }

                    if (pagingContent) {
                        pagingContainer.innerHTML = pagingContent.innerHTML;
                    }
                })
                .catch(error => {
                    console.error('Error fetching takeout list:', error);
                    listContainer.innerHTML = `
                    <div class="col-12 text-center py-5 text-danger">
                        네트워크 오류가 발생했습니다.
                    </div>`;
                });
        }

        // ----------------------------------------------------
        // 카드 내 액션 버튼 (접수확인 / 준비완료 / 픽업완료 / 취소) 더미 처리
        // ----------------------------------------------------
        function f_takeout_action(action, orderNo) {
            let msg = '';
            if (action === 'accept')   msg = '해당 주문을 접수 처리하시겠습니까?';
            if (action === 'prepare')  msg = '준비 완료로 상태를 변경하시겠습니까?';
            if (action === 'pickup')   msg = '픽업 완료 처리하시겠습니까?';
            if (action === 'cancel')   msg = '해당 주문을 취소 처리하시겠습니까?';

            if (msg && !confirm(msg)) return;

            const formData = new FormData();
            formData.append('act', 'takeout_action');
            formData.append('order_no', orderNo);
            formData.append('action', action);

            fetch('./update.php', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(result => {
                    if (result.success) {
                        alert('처리되었습니다. (현재는 더미 동작입니다)');
                        f_get_takeout_list(1);
                    } else {
                        alert(result.message || '처리 중 오류가 발생했습니다.');
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('처리 중 오류가 발생했습니다.');
                });
        }
    </script>

<?php
include $_SERVER['DOCUMENT_ROOT']."/market/foot.inc.php";
