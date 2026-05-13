<?php
// stats/list.php : 통계 리스트 + 차트 페이지

include $_SERVER['DOCUMENT_ROOT']."/market/head.inc.php";
$chk_menu     = 7;   // 필요에 맞게 조정
$chk_sub_menu = 1;
include $_SERVER['DOCUMENT_ROOT']."/market/inc/header.menu.inc.php";

$page_title = "통계 관리";
?>

    <div class="container-fluid py-4">

        <!-- 페이지 제목 -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
            <div>
                <h1 class="h3 fw-bold mb-1"><?= $page_title ?></h1>
                <p class="text-muted mb-0">기간별 주문/매출 통계를 확인하고 판매 베스트 상품을 조회할 수 있습니다.</p>
            </div>
        </div>

        <!-- ========================= -->
        <!--   상단 필터 영역          -->
        <!-- ========================= -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">

                <!-- 정렬/검색 영역 -->
                <div class="row g-3 align-items-end">

                    <!-- 기간 빠른 선택 -->
                    <div class="col-xl-4">
                        <label class="form-label d-block mb-2">기간 선택</label>
                        <div class="btn-group" role="group">
                            <button type="button"
                                    class="btn btn-sm btn-dark btn-range"
                                    data-range="today">
                                오늘
                            </button>
                            <button type="button"
                                    class="btn btn-sm btn-outline-secondary btn-range"
                                    data-range="7d">
                                7일
                            </button>
                            <button type="button"
                                    class="btn btn-sm btn-outline-secondary btn-range"
                                    data-range="15d">
                                15일
                            </button>
                            <button type="button"
                                    class="btn btn-sm btn-outline-secondary btn-range"
                                    data-range="30d">
                                30일
                            </button>
                            <button type="button"
                                    class="btn btn-sm btn-outline-secondary btn-range"
                                    data-range="all">
                                전체
                            </button>
                        </div>
                    </div>

                    <!-- 직접 기간 선택 -->
                    <div class="col-xl-4">
                        <label class="form-label">직접 기간 선택</label>
                        <div class="d-flex align-items-center gap-2">
                            <input type="date" id="stat_date_from" class="form-control form-control-sm">
                            <span class="text-muted">~</span>
                            <input type="date" id="stat_date_to" class="form-control form-control-sm">
                        </div>
                    </div>

                    <!-- 버튼 -->
                    <div class="col-xl-4 text-xl-end">
                        <label class="form-label d-none d-xl-block">&nbsp;</label>
                        <div class="d-flex d-xl-inline-flex gap-2">
                            <button type="button" class="btn btn-sm btn-dark px-4" id="btnStatSearch">
                                검색
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary px-4" id="btnStatReset">
                                초기화
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 숨은 값 -->
                <input type="hidden" id="stat_range" value="today">
                <input type="hidden" id="stat_tab_type" value="orders">

            </div>
        </div>

        <!-- ========================= -->
        <!--   상단 요약 카드          -->
        <!-- ========================= -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small mb-1">총 매출</div>
                        <div class="h4 fw-bold mb-0" id="sum_total_sales">-</div>
                        <div class="text-muted small mt-1">선택한 기간의 총 매출 금액</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small mb-1">총 주문 수</div>
                        <div class="h4 fw-bold mb-0" id="sum_total_orders">-</div>
                        <div class="text-muted small mt-1">판매된 상품 수량 합계</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small mb-1">평균 주문 금액</div>
                        <div class="h4 fw-bold mb-0" id="sum_avg_amount">-</div>
                        <div class="text-muted small mt-1">총 매출 ÷ 총 주문 수</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================= -->
        <!--   탭 + 차트 영역           -->
        <!-- ========================= -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 pb-0">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="h6 mb-1">주간 매출 현황</h2>
                        <p class="text-muted small mb-0">주문 유형별 추이</p>
                    </div>

                    <!-- 탭 (주문 현황 / 매출 현황) -->
                    <ul class="nav nav-tabs card-header-tabs" id="chartTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active stat-tab-link"
                               id="stat-tab-orders"
                               data-type="orders"
                               data-toggle="tab"
                               href="#stat-tab-pane"
                               role="tab"
                               aria-controls="stat-tab-pane"
                               aria-selected="true">
                                주문 현황
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link stat-tab-link"
                               id="stat-tab-sales"
                               data-type="sales"
                               data-toggle="tab"
                               href="#stat-tab-pane"
                               role="tab"
                               aria-controls="stat-tab-pane"
                               aria-selected="false">
                                매출 현황
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card-body" style="height: 200px;">
                <!-- 실제로는 탭 내용이 하나뿐이라 pane은 1개만 사용 -->
                <div class="tab-content" id="chartTabContent">
                    <div class="tab-pane fade show active" id="stat-tab-pane" role="tabpanel" aria-labelledby="stat-tab-orders">
                        <canvas id="statChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================= -->
        <!--   판매 베스트 30위 리스트 -->
        <!-- ========================= -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0">
                <h2 class="h6 mb-0">판매 베스트 30위</h2>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 70px;">순위</th>
                            <th class="text-center" style="width: 90px;">구분</th>
                            <th>상품명</th>
                            <th class="text-center" style="width: 120px;">카테고리</th>
                            <th class="text-end" style="width: 140px;">메뉴 가격</th>
                            <th class="text-end" style="width: 120px;">재고</th>
                            <th class="text-end" style="width: 120px;">판매량</th>
                        </tr>
                        </thead>
                        <tbody id="best_list_body">
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                데이터를 불러오는 중입니다...
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div> <!-- /.container-fluid -->

    <script>
        let statsChart = null;

        $(function () {
            // 초기 상태: 오늘 + 주문 현황
            $('.btn-range[data-range="today"]')
                .addClass('btn-dark')
                .removeClass('btn-outline-secondary');

            $('#stat_tab_type').val('orders');
            $('#stat-tab-orders').addClass('active');
            $('#stat-tab-sales').removeClass('active');

            // 날짜 기본값(선택): 오늘로 설정하고 싶으면 아래 주석 해제
            const today = new Date().toISOString().slice(0, 10);
            $('#stat_date_from').val('');
            $('#stat_date_to').val('');

            // 기간 버튼 클릭
            $(document).on('click', '.btn-range', function () {
                $('.btn-range').removeClass('btn-dark').addClass('btn-outline-secondary');
                $(this).addClass('btn-dark').removeClass('btn-outline-secondary');

                $('#stat_range').val($(this).data('range') || 'today');

                // 기간 버튼만 눌러도 바로 새로고침
                fetchStats();
            });

            // 상단 탭 (주문 현황 / 매출 현황) - 탭 UI
            $(document).on('click', '.stat-tab-link', function (e) {
                e.preventDefault();

                $('.stat-tab-link').removeClass('active');
                $(this).addClass('active');

                const type = $(this).data('type') || 'orders';
                $('#stat_tab_type').val(type);

                fetchStats();
            });

            // 검색 버튼
            $('#btnStatSearch').on('click', function () {
                fetchStats();
            });

            // 초기화 버튼
            $('#btnStatReset').on('click', function () {
                $('#stat_date_from').val('');
                $('#stat_date_to').val('');

                $('#stat_range').val('today');
                $('.btn-range').removeClass('btn-dark').addClass('btn-outline-secondary');
                $('.btn-range[data-range="today"]').addClass('btn-dark').removeClass('btn-outline-secondary');

                $('#stat_tab_type').val('orders');
                $('.stat-tab-link').removeClass('active');
                $('#stat-tab-orders').addClass('active');

                fetchStats();
            });

            // 최초 로드
            fetchStats();
        });

        /**
         * 통계 데이터 호출
         */
        function fetchStats() {
            const range   = $('#stat_range').val();
            const from    = $('#stat_date_from').val();
            const to      = $('#stat_date_to').val();
            const tabType = $('#stat_tab_type').val();

            $('#best_list_body').html(
                '<tr><td colspan="7" class="text-center py-4 text-muted">데이터를 불러오는 중입니다...</td></tr>'
            );

            $.ajax({
                url: './update.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    act: 'get_stats',
                    range: range,
                    date_from: from,
                    date_to: to,
                    tab_type: tabType
                },
                success: function (res) {
                    if (!res || !res.success) {
                        $('#best_list_body').html(
                            '<tr><td colspan="7" class="text-center text-danger py-4">' +
                            (res && res.message ? escapeHtml(res.message) : '데이터 로드 중 오류가 발생했습니다.') +
                            '</td></tr>'
                        );
                        return;
                    }

                    // 차트
                    if (res.chart) {
                        updateStatChart(res.chart);
                    }

                    // 요약
                    if (res.summary) {
                        renderSummary(res.summary);
                    }

                    // 판매 베스트
                    renderBestList(res.best_items || []);
                },
                error: function () {
                    $('#best_list_body').html(
                        '<tr><td colspan="7" class="text-center text-danger py-4">네트워크 오류가 발생했습니다.</td></tr>'
                    );
                }
            });
        }

        /**
         * Chart.js 차트 갱신
         */
        function updateStatChart(chartInfo) {
            if (!chartInfo || !chartInfo.labels) return;

            const ctx = document.getElementById('statChart').getContext('2d');

            if (statsChart) {
                statsChart.destroy();
            }

            const datasets = (chartInfo.datasets || []).map(function (ds) {
                return {
                    label: ds.label,
                    data: ds.data,
                    backgroundColor: ds.backgroundColor || 'rgba(0,0,0,0.05)',
                    borderColor: ds.borderColor || 'rgba(0,0,0,0.3)',
                    borderWidth: ds.borderWidth || 1.5,
                    tension: 0.3
                };
            });

            statsChart = new Chart(ctx, {
                type: chartInfo.type || 'bar',
                data: {
                    labels: chartInfo.labels,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                boxWidth: 12,
                                boxHeight: 12
                            }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                label: function (context) {
                                    const label = context.dataset.label || '';
                                    const value = context.parsed.y || 0;
                                    return ' ' + label + ': ' + numberFormat(value);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function (value) {
                                    return numberFormat(value);
                                }
                            }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        }

        /**
         * 상단 요약 카드 렌더링
         */
        function renderSummary(summary) {
            $('#sum_total_sales').text(summary.total_sales ? numberFormat(summary.total_sales) + '원' : '-');
            $('#sum_total_orders').text(summary.total_orders ? numberFormat(summary.total_orders) + '개' : '-');
            $('#sum_avg_amount').text(summary.avg_order_amt ? numberFormat(summary.avg_order_amt) + '원' : '-');
        }

        /**
         * 판매 베스트 리스트 렌더링
         */
        function renderBestList(items) {
            const $tbody = $('#best_list_body');

            if (!items || !items.length) {
                $tbody.html(
                    '<tr><td colspan="7" class="text-center py-4 text-muted">조회된 판매 데이터가 없습니다.</td></tr>'
                );
                return;
            }

            let html = '';
            items.forEach(function (item) {
                const rank = item.rank || '-';
                const name = escapeHtml(item.name || '');
                const category = escapeHtml(item.category || '-');
                const price = numberFormat(item.price || 0);
                const stock = numberFormat(item.stock || 0);
                const qty   = numberFormat(item.qty || 0);

                html += `
                <tr>
                    <td class="text-center">${rank}</td>
                    <td class="text-center">
                        ${
                    rank === 1
                        ? '<span class="badge bg-dark text-white">베스트</span>'
                        : ''
                }
                    </td>
                    <td>${name}</td>
                    <td class="text-center">${category}</td>
                    <td class="text-end">${price}원</td>
                    <td class="text-end">${stock}</td>
                    <td class="text-end">${qty}</td>
                </tr>
            `;
            });

            $tbody.html(html);
        }

        /**
         * 숫자 포맷팅 (3자리 콤마)
         */
        function numberFormat(num) {
            num = Number(num) || 0;
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        }

        /**
         * 간단한 HTML 이스케이프
         */
        function escapeHtml(str) {
            if (str === null || str === undefined) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }
    </script>

<?php
include $_SERVER['DOCUMENT_ROOT']."/market/foot.inc.php";
?>
