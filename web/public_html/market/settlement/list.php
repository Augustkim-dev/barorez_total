<?php
// list.php : 정산 관리 리스트 페이지

include $_SERVER['DOCUMENT_ROOT']."/market/head.inc.php";
$chk_menu     = 6;   // 필요 시 메뉴 번호 맞게 조정
$chk_sub_menu = 1;
include $_SERVER['DOCUMENT_ROOT']."/market/inc/header.menu.inc.php";

$page_title = "정산 관리";

// 오늘 날짜 (기본값)
$today = date('Y-m-d');
?>
    <div class="container-fluid py-4">

        <!-- 상단 제목 -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
            <div>
                <h1 class="h3 fw-bold mb-1"><?= $page_title ?></h1>
                <p class="text-muted mb-0">기간별 · 상태별 정산 내역을 조회합니다.</p>
            </div>
        </div>

        <!-- 필터 카드 -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">

                <!-- 정산 기간 -->
                <div class="row g-3 align-items-center mb-3">
                    <div class="col-auto">
                        <span class="fw-semibold">정산 기간</span>
                    </div>
                    <div class="col-auto">
                        <div class="btn-group btn-group-sm" role="group" aria-label="정산 기간">
                            <button type="button" class="btn btn-outline-secondary active" data-range="all">전체</button>
                            <button type="button" class="btn btn-outline-secondary" data-range="today">오늘</button>
                            <button type="button" class="btn btn-outline-secondary" data-range="3d">3일</button>
                            <button type="button" class="btn btn-outline-secondary" data-range="7d">7일</button>
                            <button type="button" class="btn btn-outline-secondary" data-range="1m">30일</button>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="input-group input-group-sm">
                            <input type="date" id="start_date" class="form-control" value="<?= $today ?>">
                            <span class="input-group-text">~</span>
                            <input type="date" id="end_date" class="form-control" value="<?= $today ?>">
                        </div>
                    </div>
                </div>

                <!-- 상태 + 조회 버튼 -->
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <span class="fw-semibold">정산 상태</span>
                    </div>
                    <div class="col-auto">
                        <div class="btn-group btn-group-sm" role="group" aria-label="정산 상태">
                            <button type="button" class="btn btn-outline-secondary active" data-status="all">전체</button>
                            <button type="button" class="btn btn-outline-secondary" data-status="READY">미정산</button>
                            <button type="button" class="btn btn-outline-secondary" data-status="PLANNED">정산예정</button>
                            <button type="button" class="btn btn-outline-secondary" data-status="DONE">정산완료</button>
                        </div>
                    </div>
                    <div class="col-auto ms-auto">
                        <button type="button" class="btn btn-sm btn-primary" id="btnSearchSettle">
                            <i class="bi bi-search me-1"></i> 조회
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <!-- 정산 리스트 테이블 -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                        <tr>
                            <th style="width: 140px;">정산번호</th>
                            <th style="width: 120px;">정산 예정일</th>
                            <th class="text-end" style="width: 140px;">매출액</th>
                            <th class="text-end" style="width: 140px;">수수료</th>
                            <th class="text-end" style="width: 160px;">정산 금액</th>
                            <th class="text-center" style="width: 180px;">정산 기간</th>
                            <th class="text-center" style="width: 120px;">상태</th>
                            <th class="text-center" style="width: 100px;">관리</th>
                        </tr>
                        </thead>
                        <tbody id="settle_list_body">
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                정산 내역을 조회해 주세요.
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 페이징 -->
        <div id="paging_box_mng" class="mt-3 d-flex justify-content-center"></div>

    </div> <!-- /.container-fluid -->

    <script>
        // 현재 선택된 기간 타입 (all, today, 3d, 7d, 1m)
        let currentRange = 'all';
        let currentStatus = 'all';

        $(document).ready(function () {

            // 기간 버튼 클릭 시: 날짜 세팅 + 즉시 필터 적용
            $('.btn-group [data-range]').on('click', function () {
                $('.btn-group [data-range]').removeClass('active');
                $(this).addClass('active');

                currentRange = $(this).data('range');

                // 날짜 자동 셋팅
                setDateRangeByType(currentRange);

                // 버튼 선택 시 바로 리스트 재조회
                loadSettleList(1);
            });

            // 정산 상태 버튼 클릭 시: 바로 필터 적용
            $('.btn-group [data-status]').on('click', function () {
                const $group = $(this).closest('.btn-group');
                $group.find('[data-status]').removeClass('active');
                $(this).addClass('active');

                currentStatus = $(this).data('status'); // all / READY / PLANNED / DONE
                loadSettleList(1);
            });

            // 조회 버튼(날짜 직접 선택했을 때 사용)
            $('#btnSearchSettle').on('click', function () {
                loadSettleList(1);
            });

            // 페이지 로드시 기본 조회
            loadSettleList(1);
        });

        /**
         * 기간 타입에 따라 start_date / end_date 자동 설정
         */
        function setDateRangeByType(type) {
            const today = new Date(); // 오늘
            let start = new Date(today);

            if (type === 'all') {
                // all 은 서버에서 기간 필터를 안 걸도록 처리할 것이므로
                // 여기서는 날짜를 건드리지 않아도 됨 (원하면 초기화 정도만)
                return;
            }

            if (type === 'today') {
                start = today;
            } else if (type === '3d') {
                start.setDate(today.getDate() - 2); // 오늘 포함 3일
            } else if (type === '7d') {
                start.setDate(today.getDate() - 6); // 오늘 포함 7일
            } else if (type === '1m') {
                start.setMonth(today.getMonth() - 1);
            }

            const startStr = formatDate(start);
            const endStr   = formatDate(today);

            $('#start_date').val(startStr);
            $('#end_date').val(endStr);
        }

        /**
         * Date -> YYYY-MM-DD 문자열
         */
        function formatDate(d) {
            const year = d.getFullYear();
            const month = ('0' + (d.getMonth() + 1)).slice(-2);
            const day = ('0' + d.getDate()).slice(-2);
            return `${year}-${month}-${day}`;
        }

        /**
         * 정산 리스트 조회 AJAX
         */
        function loadSettleList(pg) {
            const $tbody  = $('#settle_list_body');
            const $paging = $('#paging_box_mng');

            const startDate = $('#start_date').val();
            const endDate   = $('#end_date').val();
            const status    = currentStatus; // 버튼에서 관리 중인 상태값

            // 로딩 표시
            $tbody.html(`
        <tr>
            <td colspan="8" class="text-center py-4 text-muted">
                <div class="spinner-border text-primary me-2" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                정산 내역을 불러오는 중입니다...
            </td>
        </tr>
    `);
            $paging.html('');

            $.ajax({
                url: './update.php',
                type: 'POST',
                dataType: 'json',          // 🔹 JSON 으로 변경
                data: {
                    act: 'list',
                    pg: pg,
                    start_date: startDate,
                    end_date: endDate,
                    status: status,
                    range: currentRange
                },
                success: function (res) {
                    console.log('🔍 settle list response:', res);

                    if (!res || res.success === false) {
                        const msg = (res && res.message) ? res.message : '정산 내역을 불러오는 중 오류가 발생했습니다.';
                        $tbody.html(`
                    <tr>
                        <td colspan="8" class="text-center py-4 text-danger">
                            ${msg}
                        </td>
                    </tr>
                `);
                        $paging.html('');
                        return;
                    }

                    const html   = $.trim(res.html || '');
                    const paging = res.paging || '';

                    if (!html) {
                        $tbody.html(`
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            조회된 정산 내역이 없습니다.
                        </td>
                    </tr>
                `);
                    } else {
                        $tbody.html(html);
                    }

                    $paging.html(paging);
                },
                error: function (xhr, status, error) {
                    console.error('❌ 정산 리스트 로드 오류:', error);
                    $tbody.html(`
                <tr>
                    <td colspan="8" class="text-center py-4 text-danger">
                        정산 내역을 불러오는 중 오류가 발생했습니다.
                    </td>
                </tr>
            `);
                    $paging.html('');
                }
            });
        }

        function f_get_settle_list(pg) {
            loadSettleList(pg);
        }

        /**
         * 상세 페이지 이동
         */
        function goSettleDetail(settleNo) {
            location.href = './form.php?no=' + encodeURIComponent(settleNo);
        }
    </script>

<?php
include $_SERVER['DOCUMENT_ROOT']."/market/foot.inc.php";
