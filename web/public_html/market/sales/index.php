<?
$_SUB_HEAD_TITLE = "메인화면";
$_GET['hd_pc'] = '1';
$hd_num = 'revenue';
$hd_num2 = 'revenue1';
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
                    <span>정산관리</span>
                </h2>
                <p>기간별, 상태별 정산 내역을 조회합니다.</p>
            </div>

            <div class="card cmp_box">
                <div class="card-header">
                    <div>
                        <div class="custom-sel">
                            <button type="button" class="select-trigger" id="status_trigger">
                                정산 상태
                            </button>
                            <ul class="select-options" id="status_options">
                                <li data-value="all">전체</li>
                                <li data-value="READY">미정산</li>
                                <li data-value="PLANNED">정산예정</li>
                                <li data-value="DONE">정산완료</li>
                            </ul>
                            <input type="hidden" id="selected_status" value="all">
                        </div>
                    </div>

                    <div class="">
                        <div class="btn-group btn-group-toggle btn_toggle_primary group_sm" data-toggle="buttons" id="period_buttons">
                            <label class="btn btn-outline-light active">
                                <input type="radio" name="period" id="period_all" value="all" checked> 전체
                            </label>
                            <label class="btn btn-outline-light">
                                <input type="radio" name="period" id="period_today" value="today"> 오늘
                            </label>
                            <label class="btn btn-outline-light">
                                <input type="radio" name="period" id="period_3days" value="3days"> 3일
                            </label>
                            <label class="btn btn-outline-light">
                                <input type="radio" name="period" id="period_7days" value="7days"> 7일
                            </label>
                            <label class="btn btn-outline-light">
                                <input type="radio" name="period" id="period_30days" value="30days"> 30일
                            </label>
                        </div>
                    </div>

                    <div class="d-flex align-items-center">
                        <input type="date" class="form-control" id="start_date">
                        <p class="px-2">~</p>
                        <input type="date" class="form-control" id="end_date">
                    </div>

                    <button type="button" class="btn btn-secondary" id="search_btn">조회</button>
                </div>

                <div class="card-body">
                    <h3 class="tit_st2 pr-3">정산내역</h3>

                    <section class="table_scroll mt-4">
                        <table class="table_01" summary=" ">
                            <caption>정산내역 리스트</caption>
                            <colgroup>
                                <col width="*">
                                <col width="*">
                                <col width="*">
                                <col width="*">
                                <col width="*">
                                <col width="*">
                                <col width="*">
                                <col width="*">
                            </colgroup>
                            <thead>
                            <tr>
                                <th>정산번호</th>
                                <th>정산 예정일</th>
                                <th>매출액</th>
                                <th>수수료(원)</th>
                                <th>정산금액(원)</th>
                                <th>정산기간</th>
                                <th>상태</th>
                                <th>관리</th>
                            </tr>
                            </thead>
                            <tbody id="settle_list_body">
                            <tr><td colspan="8" class="text-center py-4 text-muted">조회 중...</td></tr>
                            </tbody>
                        </table>
                    </section>

                    <!-- 페이징 -->
                    <div class="mt-4 text-center" id="settle_paging"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            let currentPage = 1;
            let currentStatus = 'all';
            let currentPeriod = 'all';
            let currentStartDate = '';
            let currentEndDate = '';

            // 상태 셀렉트 초기화
            $('#status_options li').on('click', function() {
                currentStatus = $(this).data('value');
                $('#status_trigger').text($(this).text());
                $('#selected_status').val(currentStatus);
                $('#status_trigger').closest('.custom-sel').removeClass('active');
                loadSettleList(1);
            });

            $('#status_trigger').on('click', function(e) {
                e.stopPropagation();
                $(this).closest('.custom-sel').toggleClass('active');
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('.custom-sel').length) $('.custom-sel').removeClass('active');
            });

            // 기간 라디오 버튼
            $('#period_buttons input[type="radio"]').on('change', function() {
                currentPeriod = $(this).val();
                $('#start_date, #end_date').val('');
                // if (currentPeriod !== 'custom') {
                //     $('#start_date, #end_date').val('').prop('disabled', true);
                // } else {
                //     $('#start_date, #end_date').prop('disabled', false);
                // }

                loadSettleList(1);
            });

            $('#start_date, #end_date').on('change', function() {
                $('#period_buttons input[value="custom"]').prop('checked', true).trigger('change');
                currentPeriod = 'custom';
                currentStartDate = $('#start_date').val();
                currentEndDate = $('#end_date').val();
            });

            $('#search_btn').on('click', function() {
                currentStartDate = $('#start_date').val();
                currentEndDate = $('#end_date').val();
                loadSettleList(1);
            });

            // 리스트 불러오기
            function loadSettleList(page) {
                currentPage = page;

                $.ajax({
                    url: './update.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        act: 'list',
                        status: currentStatus,
                        period: currentPeriod,
                        start_date: currentStartDate,
                        end_date: currentEndDate,
                        pg: page
                    },
                    success: function(res) {
                        if (res.success) {
                            renderTable(res.data);
                            renderPaging(res.total_pages, page);
                        } else {
                            $('#settle_list_body').html('<tr><td colspan="8" class="text-center py-4 text-muted">' + (res.message || '조회된 정산 내역이 없습니다.') + '</td></tr>');
                            $('#settle_paging').html('');
                        }
                    },
                    error: function() {
                        $('#settle_list_body').html('<tr><td colspan="8" class="text-center py-4 text-muted">서버 연결 오류</td></tr>');
                    }
                });
            }

            // 테이블 렌더링 (프론트에서 HTML 생성)
            function renderTable(items) {
                const $body = $('#settle_list_body');
                $body.empty();

                if (!items || items.length === 0) {
                    $body.html('<tr><td colspan="8" class="text-center py-4 text-muted">조회된 정산 내역이 없습니다.</td></tr>');
                    return;
                }

                items.forEach(item => {
                    const planDate = item.st_plan_date ? item.st_plan_date.replace(/-/g, '.') : '-';

                    const totalAmount = Number(item.st_total_amount || 0).toLocaleString() + '원';
                    const serviceFee  = Number(item.st_service_fee  || 0).toLocaleString() + '원';
                    const finalAmount = Number(item.st_final_amount || 0).toLocaleString() + '원';

                    const period = (item.st_start_date && item.st_end_date)
                        ? item.st_start_date.replace(/-/g, '.') + '~' + item.st_end_date.replace(/-/g, '.')
                        : '-';

                    let statusHtml = '';
                    if (item.st_status === 'DONE') {
                        statusHtml = '<p>정산완료</p>';
                    } else if (item.st_status === 'PLANNED') {
                        statusHtml = '<p class="text-success">정산예정</p>';
                    } else {
                        statusHtml = '<p class="text-danger">미정산</p>';
                    }

                    const row = `
                <tr>
                    <td>${item.st_number}</td>
                    <td>${planDate}</td>
                    <td class="text-end">${totalAmount}</td>
                    <td class="text-end">${serviceFee}</td>
                    <td class="text-end fw-bold">${finalAmount}</td>
                    <td class="text-center">${period}</td>
                    <td class="text-center">${statusHtml}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill"
                                onclick="viewSettleDetail('${item.st_number}')">
                            상세
                        </button>
                    </td>
                </tr>
            `;
                    $body.append(row);
                });
            }

            // 페이징 렌더링 (간단 버전 - 기존 page_listing_xhr 함수가 있다면 그걸 사용하세요)
            function renderPaging(totalPages, current) {
                const $paging = $('#settle_paging');
                $paging.empty();

                if (totalPages <= 1) return;

                let html = '<nav><ul class="pagination justify-content-center">';

                // 이전
                html += `<li class="page-item ${current === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" onclick="loadSettleList(${current-1}); return false;">이전</a>
                 </li>`;

                // 페이지 번호
                for (let i = 1; i <= totalPages; i++) {
                    html += `<li class="page-item ${i === current ? 'active' : ''}">
                        <a class="page-link" href="#" onclick="loadSettleList(${i}); return false;">${i}</a>
                     </li>`;
                }

                // 다음
                html += `<li class="page-item ${current === totalPages ? 'disabled' : ''}">
                    <a class="page-link" href="#" onclick="loadSettleList(${current+1}); return false;">다음</a>
                 </li>`;

                html += '</ul></nav>';
                $paging.html(html);
            }

            // 상세 보기 (임시)
            window.viewSettleDetail = function(settleNo) {
                // alert('정산 상세 보기 준비 중입니다.\n정산번호: ' + settleNo + '\n(나중에 상세 페이지로 연결 예정)');
                location.href = '../sales-info/?no=' + settleNo;
            };

            // 초기 로드
            loadSettleList(1);
        });
    </script>

<? include_once("./inc/tail.php"); ?>
