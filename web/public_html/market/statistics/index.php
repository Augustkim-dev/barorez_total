<?
$_SUB_HEAD_TITLE = "통계관리";
$_GET['hd_pc'] = ' ';
$hd_num = 'revenue';
$hd_num2 = 'revenue2';
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
                    <span>통계관리</span>
                </h2>
            </div>

            <div class="row stati_row">
                <div class="col-12">
                    <div class="card rounded-lg">
                        <div class="card-body">
                            <div class="stati_hd">
                                <div class="hd_wp">
                                    <p class="fw_600 mb-3">기간선택</p>
                                    <div class="btn-group btn-group-toggle btn_toggle_primary group_sm" data-toggle="buttons" id="period_buttons">
                                        <label class="btn btn-outline-light active">
                                            <input type="radio" name="options" id="period_all" value="all" checked> 전체
                                        </label>
                                        <label class="btn btn-outline-light">
                                            <input type="radio" name="options" id="period_today" value="today"> 오늘
                                        </label>
                                        <label class="btn btn-outline-light">
                                            <input type="radio" name="options" id="period_3days" value="3days"> 3일
                                        </label>
                                        <label class="btn btn-outline-light">
                                            <input type="radio" name="options" id="period_7days" value="7days"> 7일
                                        </label>
                                        <label class="btn btn-outline-light">
                                            <input type="radio" name="options" id="period_30days" value="30days"> 30일
                                        </label>
                                    </div>
                                </div>

                                <div class="hd_wp">
                                    <p class="fw_600 mb-3">날짜 선택</p>
                                    <div class="d-flex align-items-center">
                                        <input type="date" class="form-control" id="start_date">
                                        <p class="px-2">~</p>
                                        <input type="date" class="form-control" id="end_date">
                                    </div>
                                </div>

                                <div class="hd_wp">
                                    <p class="fw_600 mb-lg-3 d-none d-lg-block">&nbsp;</p>
                                    <div class="d-flex">
                                        <button type="button" class="btn btn-secondary mx-2" id="search_btn">검색</button>
                                        <button type="button" class="btn btn-outline-secondary px-4 flex-shrink-0" id="reset_btn">
                                            <img src="<?=DESIGN_HTTP?>/market/img/ico_reset.svg" alt="초기화">
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-12">
                    <div class="card rounded-lg h-100">
                        <div class="card-body">
                            <div class="tit_st4 d-flex align-items-center mb-5">
                                <span class="mr-2"><img src="<?=DESIGN_HTTP?>/market/img/stat_img1.svg" alt=" "></span>
                                <p>총 매출</p>
                            </div>
                            <p class="tit_st1" id="total_sales">0원</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-12">
                    <div class="card rounded-lg h-100">
                        <div class="card-body">
                            <div class="tit_st4 d-flex align-items-center mb-5">
                                <span class="mr-2"><img src="<?=DESIGN_HTTP?>/market/img/stat_img2.svg" alt=" "></span>
                                <p>총 주문 수</p>
                            </div>
                            <p class="tit_st1" id="total_orders">0건</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-12 h-100">
                    <div class="card rounded-lg">
                        <div class="card-body">
                            <div class="tit_st4 d-flex align-items-center mb-5">
                                <span class="mr-2"><img src="<?=DESIGN_HTTP?>/market/img/stat_img1.svg" alt=" "></span>
                                <p>평균 주문 금액</p>
                            </div>
                            <p class="tit_st1" id="avg_order_amount">0원</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-8 col-12">
                    <div class="card rounded-lg h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-5">
                                <div>
                                    <p class="tit_st4">주간 매출 현황</p>
                                    <p class="tg_500 fs_16 mt-1">주문 유형별 비교 그래프입니다.</p>
                                </div>
                                <div class="btn-group btn-group-toggle btn_toggle_primary group_sm" data-toggle="buttons">
                                    <label class="btn btn-outline-light active">
                                        <input type="radio" name="graph_type" value="order" checked> 주문 현황
                                    </label>
                                    <label class="btn btn-outline-light">
                                        <input type="radio" name="graph_type" value="sales"> 매출 현황
                                    </label>
                                </div>
                            </div>
                            <div id="chart_container" style="height:480px; position:relative;">
                                <canvas id="sales_chart" style="height:300px; width:100%;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-12">
                    <div class="card rounded-lg h-100">
                        <div class="card-body">
                            <div>
                                <p class="tit_st4">판매 베스트 10위</p>
                                <p class="tg_500 fs_16 mt-1">우리 매장에 가장 인기 있는 메뉴</p>
                            </div>
                            <ul class="stati_ranking" id="best_menu_list">
                                <!-- 동적으로 채워짐 -->
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    $(document).ready(function() {
        let chartInstance = null;

        // ────────────────────────────────────────────────
        // 초기화 & 기본값
        // ────────────────────────────────────────────────
        let currentPeriod = 'all';
        let startDate = '';
        let endDate = '';

        // ────────────────────────────────────────────────
        // 기간/날짜/검색/초기화 이벤트
        // ────────────────────────────────────────────────
        $('#period_buttons input[type="radio"]').on('change', function() {
            currentPeriod = $(this).val();
            $('#start_date, #end_date').val('');
            // if (currentPeriod !== 'custom') {
            //     $('#start_date, #end_date').val('').prop('disabled', true);
            // } else {
            //     $('#start_date, #end_date').prop('disabled', false);
            // }
            loadStatistics();
        });

        $('#start_date, #end_date').on('change', function() {
            $('#period_buttons input[value="custom"]').prop('checked', true);
            currentPeriod = 'custom';
            startDate = $('#start_date').val();
            endDate = $('#end_date').val();
        });

        $('#search_btn').on('click', loadStatistics);
        $('#reset_btn').on('click', function() {
            currentPeriod = 'all';
            startDate = '';
            endDate = '';
            $('#period_all').prop('checked', true);
            $('#start_date, #end_date').val('');
            // $('#start_date, #end_date').val('').prop('disabled', true);
            loadStatistics();
        });

        $('input[name="graph_type"]').on('change', loadStatistics);

        // ────────────────────────────────────────────────
        // 통계 데이터 불러오기
        // ────────────────────────────────────────────────
        function loadStatistics() {
            $.ajax({
                url: './update.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    act: 'get_statistics',
                    period: currentPeriod,
                    start_date: startDate,
                    end_date: endDate,
                    graph_type: $('input[name="graph_type"]:checked').val() || 'order'
                },
                success: function(res) {
                    console.log('API 응답 전체:', res); // 디버깅용

                    if (res.success) {
                        $('#total_sales').text((res.total_sales || 0).toLocaleString() + '원');
                        $('#total_orders').text((res.total_orders || 0) + '건');
                        $('#avg_order_amount').text((res.avg_order_amount || 0).toLocaleString() + '원');

                        const $list = $('#best_menu_list');
                        $list.empty();
                        if (res.best_menus && Array.isArray(res.best_menus) && res.best_menus.length > 0) {
                            res.best_menus.forEach((item, idx) => {
                                $list.append(`
                                <li>
                                    <div class="text-primary fw_700 flex-shrink-0">${idx + 1}위</div>
                                    <div class="flex-fill">
<!--                                        <a href="./menu_edit.php?idx=${item.sm_idx || ''}">${item.menu_name || '이름 없음'}</a>-->
                                        <a>${item.menu_name || '이름 없음'}</a>
                                    </div>
                                    <div class="ml-auto flex-shrink-0">${(item.quantity || 0)}건</div>
                                </li>
                            `);
                            });
                        } else {
                            $list.append('<li>데이터 없음</li>');
                        }

                        // 그래프 데이터 안전 처리
                        const graphData = res.graph_data || {};
                        const labels = Array.isArray(graphData.labels) ? graphData.labels : ['데이터 없음'];
                        const datasets = Array.isArray(graphData.datasets) ? graphData.datasets : [{
                            label: '데이터 없음',
                            data: [0],
                            backgroundColor: 'rgba(200,200,200,0.6)'
                        }];

                        renderChart(labels, datasets, res.graph_type || 'order');
                    } else {
                        console.warn('API 성공 플래그 false:', res.message);
                        alert(res.message || '통계 데이터를 불러올 수 없습니다.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('통계 API 호출 실패:', status, error, xhr.responseText);
                    alert('서버와의 연결에 문제가 발생했습니다.');
                }
            });
        }

        // ────────────────────────────────────────────────
        // Chart.js 그래프 렌더링 (Y축 범위 동적 + 높이 300px 고정)
        // ────────────────────────────────────────────────
        function renderChart(labels, datasets, graphType) {
            const canvas = document.getElementById('sales_chart');
            if (!canvas) {
                console.error('캔버스 요소(sales_chart)를 찾을 수 없습니다.');
                return;
            }

            if (chartInstance) {
                chartInstance.destroy();
                chartInstance = null;
            }

            // 컨테이너 높이 고정
            const container = canvas.parentElement;
            if (container) {
                container.style.height = '480px';
                container.style.position = 'relative';
            }

            canvas.style.height = '300px';
            canvas.style.width = '100%';

            // 데이터가 없으면 더미
            if (!labels || labels.length === 0) {
                labels = ['월', '화', '수', '목', '금', '토', '일'];
                datasets = [{
                    label: '데이터 없음',
                    data: [0,0,0,0,0,0,0],
                    backgroundColor: 'rgba(200,200,200,0.5)'
                }];
            }

            // 스택된 최대값 계산
            let maxY = 0;
            datasets.forEach(dataset => {
                if (dataset.data && dataset.data.length > 0) {
                    const datasetMax = Math.max(...dataset.data);
                    maxY = Math.max(maxY, datasetMax);
                }
            });

            // Y축 6단계 고정 로직
            let suggestedMax = 0;
            let stepSize = 0;

            if (graphType === 'sales') {
                // 매출: 기본 10만원, 6단계로 나누면 간격 20,000원
                if (maxY <= 100000) {
                    suggestedMax = 100000;
                    stepSize = 20000; // 0 → 20k → 40k → 60k → 80k → 100k (6단계)
                } else {
                    // 10만원 초과 시 최대값을 6단계로 나누고, 간격을 5만원 단위로 반올림
                    const interval = Math.ceil(maxY / 5) * 50000 / 5; // 5만원 단위로 조정
                    suggestedMax = Math.ceil(maxY / interval) * interval;
                    stepSize = interval;
                }
            } else {
                // 주문: 기본 10건, 6단계로 나누면 간격 2건
                if (maxY <= 10) {
                    suggestedMax = 10;
                    stepSize = 2; // 0 → 2 → 4 → 6 → 8 → 10 (6단계)
                } else {
                    // 10건 초과 시 최대값을 6단계로 나누고, 간격을 5건 단위로 반올림
                    const interval = Math.ceil(maxY / 5) * 5 / 5; // 5건 단위
                    suggestedMax = Math.ceil(maxY / interval) * interval;
                    stepSize = interval;
                }
            }

            chartInstance = new Chart(canvas, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: datasets.map((ds, i) => ({
                        ...ds,
                        stack: 'stack',
                        backgroundColor: ds.backgroundColor || [
                            'rgba(255, 99, 132, 0.7)',   // 테이블
                            'rgba(255, 159, 64, 0.7)',   // 주문
                            'rgba(54, 162, 235, 0.7)'    // 예약
                        ][i % 3]
                    }))
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                boxWidth: 12,
                                padding: 20
                            }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) label += ': ';
                                    const value = context.parsed.y;
                                    label += value.toLocaleString() + (graphType === 'sales' ? '원' : '건');
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            stacked: true,
                            ticks: {
                                maxRotation: 0,
                                minRotation: 0
                            }
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            suggestedMax: suggestedMax,
                            ticks: {
                                stepSize: stepSize,
                                callback: function(value) {
                                    return value.toLocaleString() + (graphType === 'sales' ? '원' : '건');
                                }
                            }
                        }
                    }
                }
            });
        }

        // 페이지 로드 시 기본 통계 불러오기
        loadStatistics();
    });
</script>

<? include_once("../inc/tail.php"); ?>
