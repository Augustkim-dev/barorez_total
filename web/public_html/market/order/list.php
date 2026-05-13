<?php
// list.php: 주문 관리 리스트 페이지 (카드 UI)

include $_SERVER['DOCUMENT_ROOT']."/market/head.inc.php";
$chk_menu = 1;
$chk_sub_menu = 1;
include $_SERVER['DOCUMENT_ROOT']."/market/inc/header.menu.inc.php";

// 기본 변수 설정
$page_title    = "실시간 주문 관리";
$default_pg    = 1;
$default_limit = 12; // 한 페이지에 12개의 카드를 보여주도록 설정

// 초기 검색 값 설정 (필요에 따라)
$search_txt = $_GET['search_txt'] ?? '';
?>

    <div class="container-fluid py-4">

        <!-- 페이지 제목 영역 -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
            <div>
                <h1 class="h3 fw-bold mb-1"><?= $page_title ?></h1>
                <p class="text-muted mb-0">모든 주문을 한 곳에서 관리하세요.</p>
            </div>
        </div>

        <!-- 🔹 상단 탭: 현재 / 완료 / 취소 -->
        <div class="mb-3">
            <div class="btn-group" role="group" aria-label="Order tabs">
                <button type="button"
                        class="btn btn-outline-dark active order-tab"
                        data-tab="current">
                    현재 주문 내역
                </button>
                <button type="button"
                        class="btn btn-outline-dark order-tab"
                        data-tab="completed">
                    주문 완료
                </button>
                <button type="button"
                        class="btn btn-outline-dark order-tab"
                        data-tab="cancelled">
                    주문 취소
                </button>
            </div>
        </div>

        <!-- 상단 필터 & 검색 영역 -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body d-flex flex-column flex-lg-row align-items-lg-center gap-3">

                <!-- 검색 -->
                <div class="flex-grow-1 w-100">
                    <div class="position-relative">
                    <span class="position-absolute top-50 start-0 translate-middle-y ms-3 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                        <input
                                type="text"
                                id="orderSearchInput"
                                class="form-control ps-5"
                                placeholder="주문번호, 테이블, 고객명 검색..."
                                value="<?= htmlspecialchars($search_txt, ENT_QUOTES) ?>"
                        >
                    </div>
                </div>

                <!-- 주문 타입 필터 -->
                <select id="allOrders" class="form-control form-select-sm" onchange="f_get_box_mng_list(1);">
                    <option value="all">전체 주문</option>
                    <option value="table">테이블 주문</option>
                    <option value="takeout">포장 주문</option>
                    <option value="reservation">예약 주문</option>
                </select>

            </div>
        </div>
        <!-- /상단 필터 & 검색 영역 -->

        <!-- 리스트 컨테이너 -->
        <div id="list_box_mng" class="row g-3">
            <!-- 데이터는 이곳에 AJAX로 로드됩니다 -->
            <div class="col-12 text-center py-5 text-muted">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div>데이터를 불러오는 중입니다...</div>
            </div>
        </div>
        <!-- /리스트 컨테이너 -->

        <!-- 페이징 컨테이너 -->
        <div id="paging_box_mng" class="mt-4 d-flex justify-content-center">
            <!-- 페이징은 이곳에 AJAX로 로드됩니다 -->
        </div>
        <!-- /페이징 컨테이너 -->

    </div> <!-- /.container-fluid -->

    <script>
        // 🔹 현재 선택된 탭 상태 (current | completed | cancelled)
        let currentTab = 'current';

        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('orderSearchInput');

            if (searchInput) {
                // 엔터 키 검색 이벤트
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        f_get_box_mng_list(1);
                    }
                });
            }

            // 탭 클릭 이벤트
            document.querySelectorAll('.order-tab').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const tab = this.getAttribute('data-tab') || 'current';
                    currentTab = tab;

                    // active 클래스 토글
                    document.querySelectorAll('.order-tab').forEach(function(b) {
                        b.classList.remove('active');
                    });
                    this.classList.add('active');

                    // 탭 변경 시 1페이지부터 다시 로드
                    f_get_box_mng_list(1);
                });
            });

            // 초기 데이터 로드
            f_get_box_mng_list(<?= $default_pg ?>);
        });

        // ----------------------------------------------------
        // 리스트 데이터를 AJAX( fetch )로 불러오는 함수
        // ----------------------------------------------------
        function f_get_box_mng_list(pg) {
            const listContainer   = document.getElementById('list_box_mng');
            const pagingContainer = document.getElementById('paging_box_mng');
            const searchTxt       = document.getElementById('orderSearchInput').value;
            const searchCol       = 'all'; // 검색 컬럼은 예시로 'all'로 고정

            // 로딩 상태 표시
            listContainer.innerHTML = `
            <div class="col-12 text-center py-5 text-muted">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div>데이터를 불러오는 중입니다...</div>
            </div>
        `;
            pagingContainer.innerHTML = '';

            const formData = new FormData();
            formData.append('act', 'list');
            formData.append('obj_pg', pg);
            formData.append('obj_limit_num', <?= $default_limit ?>);
            formData.append('obj_search_txt', searchTxt);
            formData.append('obj_sel_search', searchCol);
            formData.append('obj_order_type', document.getElementById('allOrders').value);
            // 🔹 탭 정보 추가
            formData.append('tab', currentTab);

            // AJAX 요청
            fetch('./update.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.text())
                .then(data => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(data, 'text/html');

                    const listContent   = doc.getElementById('list_content');
                    const pagingContent = doc.getElementById('paging_content');

                    if (listContent) {
                        listContainer.innerHTML = listContent.innerHTML;
                    } else {
                        listContainer.innerHTML = '<div class="col-12 text-center py-5 text-danger">데이터 로드 중 오류가 발생했습니다.</div>';
                    }

                    if (pagingContent) {
                        pagingContainer.innerHTML = pagingContent.innerHTML;
                    }
                })
                .catch(error => {
                    console.error('Error fetching list:', error);
                    listContainer.innerHTML = '<div class="col-12 text-center py-5 text-danger">네트워크 오류가 발생했습니다.</div>';
                });
        }

        // ----------------------------------------------------
        // 주문 상태 액션 처리 함수 (접수/완료 등) - 기존 그대로
        // ----------------------------------------------------
        function f_action_order(action, nt_idx) {
            if (action === 'accept'   && !confirm('주문 번호 ' + nt_idx + '를 접수하고 조리를 시작하시겠습니까?')) return;
            if (action === 'complete' && !confirm('주문 번호 ' + nt_idx + '의 조리를 완료 처리하시겠습니까?')) return;
            if (action === 'serve'    && !confirm('주문 번호 ' + nt_idx + '을 완료 처리(서빙 완료)하시겠습니까?')) return;
            if (action === 'cancel'   && !confirm('주문 번호 ' + nt_idx + '을 취소하시겠습니까?')) return;
            if (action === 'pay'      && !confirm('해당 주문을 결제완료 처리하시겠습니까?')) return;

            const formData = new FormData();
            formData.append('act', action);
            formData.append('nt_idx', nt_idx);

            fetch('./update.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        alert(result.message || '성공적으로 처리되었습니다.');
                        const activePage = document.querySelector('.pagination .active a');
                        const currentPage = activePage ? activePage.dataset.pg : 1;
                        f_get_box_mng_list(currentPage);
                    } else {
                        alert('처리 실패: ' + (result.message || '알 수 없는 오류'));
                    }
                })
                .catch(error => {
                    console.error('Error processing action:', error);
                    alert('처리 중 오류가 발생했습니다.');
                });
        }

        // ----------------------------------------------------
        // 삭제 처리 - 기존 그대로 사용 가능 (필요 시 orders_t에 맞게 수정)
        // ----------------------------------------------------
        function f_post_del(url, nt_idx) {
            if (!confirm('선택된 항목을 정말 삭제하시겠습니까?')) return;

            const formData = new FormData();
            formData.append('act', 'del'); // 삭제 액션
            formData.append('nt_idx', nt_idx); // 삭제할 인덱스

            fetch(url, {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        alert('삭제되었습니다.');
                        const activePage = document.querySelector('.pagination .active a');
                        const currentPage = activePage ? activePage.dataset.pg : 1;
                        f_get_box_mng_list(currentPage);
                    } else {
                        alert('삭제 실패: ' + (result.message || '알 수 없는 오류'));
                    }
                })
                .catch(error => {
                    console.error('Error deleting item:', error);
                    alert('삭제 처리 중 오류가 발생했습니다.');
                });
        }
    </script>

<?php
include $_SERVER['DOCUMENT_ROOT']."/market/foot.inc.php";
