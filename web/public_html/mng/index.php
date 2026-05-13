<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.menu.inc.php";
?>

<!-- PAGE CONTENT CONTAINER -->
<div class="content" id="content">
    <!-- PAGE HEADING -->
    <div class="page-heading">
        <div class="page-heading__container">
            <h1 class="title">Dashboard</h1>
            <p class="caption">
                가입 및 기타현황을 확인합니다.
            </p>
        </div>
        <nav aria-label="breadcrumb" role="navigation">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo MNG_HTTP?>">Porta</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </nav>
    </div>
    <!-- //END PAGE HEADING -->
    <div class="container-fluid">
      <form id="searchForm">
        <div class="row g-3 align-items-center">

          <div class="col-md-6 d-flex flex-wrap align-items-center">
            <label class="mr-3"><h5 class="mb-0">기간 설정</h5></label>
              <div class="btn-group w-auto" role="group" aria-label="select_category" id="quickRangeButtons">
                  <button type="button" class="btn btn-outline-secondary" data-days="0">오늘</button>
                  <button type="button" class="btn btn-outline-secondary" data-days="7">7일</button>
                  <button type="button" class="btn btn-outline-secondary" data-days="15">15일</button>
                  <button type="button" class="btn btn-outline-secondary" data-days="30">30일</button>
              </div>
          </div>

          <div class="col-sm-6">
            <div class="input-group mb-0">
              <input type="text" name="start_date" id="start_date" class="form-control" readonly />
              <span class="m-2">~</span>
              <input type="text" name="end_date" id="end_date" class="form-control" readonly />
              <span class="m-2"></span>
              <input type="submit" class="btn btn-secondary" value="검색" />
            </div>
          </div>
        </div>
      </form>


      <div class="row g-3 mt-3" id="summaryCards">
        <div class="col-12 col-lg-4 margin-bottom-20">
          <div class="widget">
            <div class="widget__icon_layer"><span class="li-user"></span></div>
            <div class="widget__container">
              <div class="widget__line">
                <div class="widget__icon"><span class="li-user"></span></div>
                <div class="widget__title" id="stat-signup-total">0명</div>
                <div class="widget__subtitle">총 회원수</div>
              </div>
              <div class="widget__box">
                <div class="widget__informer" id="stat-signup-today">가입: 0명</div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-12 col-lg-4 margin-bottom-20">
          <div class="widget">
            <div class="widget__icon_layer"><span class="li-user-minus"></span></div>
            <div class="widget__container">
              <div class="widget__line">
                <div class="widget__icon"><span class="li-user-minus"></span></div>
                <div class="widget__title" id="stat-withdrawal-total">0개</div>
                <div class="widget__subtitle">총 주문 수</div>
              </div>
              <div class="widget__box">
                <div class="widget__informer" id="stat-withdrawal-today">주문: 0개</div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-12 col-lg-4 margin-bottom-20">
          <div class="widget">
            <div class="widget__icon_layer"><span class="li-bubble-question"></span></div>
            <div class="widget__container">
              <div class="widget__line">
                <div class="widget__icon"><span class="li-bubble-question"></span></div>
                <div class="widget__title" id="stat-inquiry-total">0원</div>
                <div class="widget__subtitle">총 매출</div>
              </div>
              <div class="widget__box">
                <div class="widget__informer" id="stat-inquiry-today">매출: 0원</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="form-row mt-4">
        <div class="col-md-12">
          <div class="card nav-tabs-cardtop margin-top-40">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
              <li class="nav-item"><a class="nav-link text-bold active show" id="tab-1_" data-toggle="tab" href="#tab-1" role="tab" aria-controls="home" aria-selected="true">회원 현황</a></li>
              <li class="nav-item"><a class="nav-link text-bold" id="tab-2_" data-toggle="tab" href="#tab-2" role="tab" aria-controls="profile" aria-selected="false">주문 현황</a></li>
              <li class="nav-item"><a class="nav-link text-bold" id="tab-3_" data-toggle="tab" href="#tab-3" role="tab" aria-controls="profile" aria-selected="false">매출 현황</a></li>
            </ul>
            <div class="card-body">
              <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade active show" id="tab-1" role="tabpanel" aria-labelledby="tab-1_">
                  <canvas class="dashboard-chart" id="chart-signup" height="300"></canvas>
                </div>
                <div class="tab-pane fade" id="tab-2" role="tabpanel" aria-labelledby="tab-2_">
                  <canvas class="dashboard-chart" id="chart-withdrawal" height="300" ></canvas>
                </div>
                <div class="tab-pane fade" id="tab-3" role="tabpanel" aria-labelledby="tab-3_">
                  <canvas class="dashboard-chart" id="chart-review" height="300" ></canvas>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>
<!-- //END PAGE CONTENT CONTAINER -->

<script type="text/javascript" src="<?=MNG_HTTP?>/js/vendors/chartjs/Chart.bundle.min.js"></script>
<script type="text/javascript" src="<?=MNG_HTTP?>/js/vendors/chartjs/utils.js"></script>
<script>
    const chartMap = {};
    function initChart(id, label, color) {
        const ctx = document.getElementById(id).getContext('2d');
        var configLine = {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: label,
                    backgroundColor: color,
                    borderColor: color,
                    data: [],
                    fill: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                title: {
                    display: false
                },
                tooltips: {
                    mode: 'index',
                    intersect: false,
                },
                hover: {
                    mode: 'nearest',
                    intersect: true
                },
                scales: {
                    xAxes: [{
                        display: true,
                        scaleLabel: {
                            display: false
                        }
                    }],
                    yAxes: [{
                        display: true,
                        scaleLabel: {
                            display: false
                        }
                    }]
                }
            }
        };


        chartMap[id] = new Chart(ctx,configLine);
    }
    initChart('chart-signup', '가입', window.chartColors.primary);
    initChart('chart-withdrawal', '주문', window.chartColors.success);
    initChart('chart-review', '매출', window.chartColors.warning);

    function loadDashboardStats(startDate, endDate) {
        const formData = $.param({
            act:'chart',
            start_date: startDate,
            end_date: endDate
        });

        // console.log('[요청 데이터]', formData);

        $.post('./dashboard_update.php', formData, function (res) {
            // console.log('[응답 데이터]', res);

            // 카드 값 세팅
            $('#stat-signup-total').text(res.signup_total.toLocaleString() + '명');      // 총 회원수
            $('#stat-withdrawal-total').text(res.withdrawal_total.toLocaleString() + '개'); // 총 주문 수
            $('#stat-inquiry-total').text(res.inquiry_total.toLocaleString() + '원');    // 총 매출

// 기간 내 수치 (마지막 날 기준)
            const rangeSignup     = res.chart_data.signup.slice(-1)[0]?.count || 0;
            const rangeWithdrawal = res.chart_data.withdrawal.slice(-1)[0]?.count || 0;
            const rangeSales      = res.inquiry_pending_in_range || 0;

            $('#stat-signup-today').text('가입: '  + rangeSignup.toLocaleString()     + '명');
            $('#stat-withdrawal-today').text('주문: ' + rangeWithdrawal.toLocaleString() + '개');
            $('#stat-inquiry-today').text('매출: ' + rangeSales.toLocaleString()      + '원');

            // 차트 갱신
            for (const key in res.chart_data) {
                const chart = chartMap['chart-' + key];
                chart.data.labels = res.chart_data[key].map(d => d.date);
                chart.data.datasets[0].data = res.chart_data[key].map(d => d.count);
                chart.update();
            }
        }, 'json');
    }

    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        const target = $(e.target).attr('href');
        $(target).find('canvas').css('height', '300px');
    });


    function loadLatestActivity(startDate, endDate) {
        const formData = $.param({
            act: 'latest',
            start_date: startDate,
            end_date: endDate
        });

        console.log('[최신 데이터 요청]', formData);

        $.post('./dashboard_update.php', formData, function (res) {
            console.log('[최신 데이터 응답]', res);

            const latest = res.latest;



            // 방문자
            let htmlVisit = '';
            latest.visit.forEach(item => {
                htmlVisit += `<tr>
                  <td class="text-center">${item.vi_date}</td>
                  <td class="text-center">${item.vi_ip}</td>
                  <td>
                    <div class="user user--rounded user--bordered user--lg">
                      <img src="${item.profile}" alt="프로필" width="40" height="40">
                      <div class="user__name">
                        <strong>${item.mt_name}</strong><br>
                        <span class="text-muted">${item.mt_id || '-'}</span>
                      </div>
                    </div>
                  </td>

                </tr>`;
            });
            $('#latest-visit').html(htmlVisit);

            // 회원
            let htmlMember = '';
            latest.member.forEach(item => {
                htmlMember += `<tr>
                  <td class="text-center">${item.created_at}</td>
                  <td >
                      <div class="user user--rounded user--bordered user--lg mb-2">
                        <img src="${item.profile}" alt="프로필" />
                        <div class="user__name">
                          <strong>${item.mt_name}</strong><br>
                          <span class="text-muted">${item.mt_email}</span>
                        </div>
                      </div>
                  </td>
                </tr>`;
            });
            $('#latest-member').html(htmlMember);

            // 1:1문의
            let htmlQnA = '';
            latest.qna.forEach(item => {
                htmlQnA += `<tr>
                  <td class="text-center">${item.created_at}</td>
                  <td><span class="text-truncate d-inline-block" style="max-width: 200px;">${item.rt_title}</span></td>
                  <td>
                      <div class="user user--rounded user--bordered user--lg mb-2">
                        <img src="${item.profile}" alt="프로필" />
                        <div class="user__name">
                          <strong>${item.mt_name}</strong><br>
                          <span class="text-muted">${item.mt_email}</span>
                        </div>
                      </div>
                  </td>
                </tr>`;
            });
            $('#latest-qna').html(htmlQnA);

        }, 'json');
    }


    $(document).ready(function () {
        const today = new Date();
        const endDate = today.toISOString().split('T')[0];

        const start = new Date();
        start.setDate(start.getDate() - 6); // 기본 7일
        const startDate = start.toISOString().split('T')[0];

        $('#start_date').val(startDate);
        $('#end_date').val(endDate);

        // 날짜 선택 위젯
        $('#start_date').datetimepicker({
            format: 'Y-m-d',
            onShow: function (ct) {
                this.setOptions({
                    maxDate: $('#end_date').val() ? $('#end_date').val() : false
                })
            },
            timepicker: false
        });

        $('#end_date').datetimepicker({
            format: 'Y-m-d',
            onShow: function (ct) {
                this.setOptions({
                    minDate: $('#start_date').val() ? $('#start_date').val() : false
                })
            },
            timepicker: false
        });

        // ✅ 기간 버튼 클릭 시: 바로 날짜 세팅 + 대시보드 호출
        $('#quickRangeButtons button').on('click', function () {
            const days = parseInt($(this).data('days'), 10);

            const end = new Date();     // 오늘
            const start = new Date();
            start.setDate(end.getDate() - (days === 0 ? 0 : (days - 1)));
            // ex) 7일 버튼 -> 오늘 포함 7일치

            const format = (d) => d.toISOString().split('T')[0];
            const startStr = format(start);
            const endStr   = format(end);

            $('#start_date').val(startStr);
            $('#end_date').val(endStr);

            // 버튼 active 표시
            $('#quickRangeButtons button').removeClass('active');
            $(this).addClass('active');

            // 🔥 여기서 바로 데이터 로딩
            loadDashboardStats(startStr, endStr);
            loadLatestActivity(startStr, endStr);
        });

        // ✅ 날짜 직접 선택 시에는 버튼 선택 해제만 하고, 자동 검색은 안 함
        $('#start_date, #end_date').on('change', function () {
            $('#quickRangeButtons button').removeClass('active');
        });

        // ✅ 검색 버튼 눌렀을 때만 수동 날짜 적용
        $('#searchForm').on('submit', function (e) {
            e.preventDefault();
            const startDate = $('#start_date').val();
            const endDate   = $('#end_date').val();
            if (!startDate || !endDate) {
                alert('기간을 선택해주세요.');
                return;
            }
            loadDashboardStats(startDate, endDate);
            loadLatestActivity(startDate, endDate);
        });

        // 페이지 최초 로딩 시 기본 7일치 호출
        loadDashboardStats(startDate, endDate);
        loadLatestActivity(startDate, endDate);
    });


</script>

<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/foot.inc.php";
?>
