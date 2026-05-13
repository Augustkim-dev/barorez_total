<?
$_SUB_HEAD_TITLE = "문의하기";
$_GET['hd_pc'] = '1';
$hd_num = 'qa';
$hd_num2 = 'qa1';
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
                <span>문의하기</span>
            </h2>
            <p>기간별, 상태별 문의 내역을 조회합니다.</p>
        </div>

        <div class="card cmp_box">
            <div class="card-header justify-content-between">
                <div class="d-flex">
                    <div>
                        <div class="custom-sel">
                            <button type="button" class="select-trigger" id="status_trigger">
                                문의 상태
                            </button>
                            <ul class="select-options" id="status_options">
                                <li data-value="all">전체</li>
                                <li data-value="pending">답변대기</li>
                                <li data-value="answered">답변완료</li>
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
                <button type="button" class="btn btn-secondary" onclick="location.href='../qa-edit/'">작성</button>
            </div>

            <div class="card-body">
                <h3 class="tit_st2 pr-3">문의내역</h3>

                <section class="table_scroll mt-4">
                    <table class="table_01" summary=" ">
                        <caption>정산내역 리스트</caption>
                        <colgroup>
                            <col width="*">
                            <col width="*">
                            <col width="*">
                            <col width="*">
                            <col width="*">
                        </colgroup>
                        <thead>
                        <tr>
                            <th>번호</th>
                            <th>제목</th>
                            <th>답변상태</th>
                            <th>일시</th>
                            <th>관리</th>
                        </tr>
                        </thead>
                        <tbody id="qa_list_body">
                        <tr><td colspan="8" class="text-center py-4 text-muted">조회 중...</td></tr>
                        </tbody>
                    </table>
                </section>

                <!-- 페이징 -->
                <div class="mt-4 text-center" id="qa_paging"></div>
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

        // 상태 선택
        $('#status_options li').on('click', function() {
            currentStatus = $(this).data('value');
            $('#status_trigger').text($(this).text());
            $('#selected_status').val(currentStatus);
            $('#status_trigger').closest('.custom-sel').removeClass('active');
            loadQaList(1);
        });

        $('#status_trigger').on('click', function(e) {
            e.stopPropagation();
            $(this).closest('.custom-sel').toggleClass('active');
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('.custom-sel').length) {
                $('.custom-sel').removeClass('active');
            }
        });

        // 기간 선택
        $('#period_buttons input[type="radio"]').on('change', function() {
            currentPeriod = $(this).val();
            $('#start_date, #end_date').val('');
            // if (currentPeriod !== 'custom') {
            //     $('#start_date, #end_date').val('').prop('disabled', true);
            // } else {
            //     $('#start_date, #end_date').prop('disabled', false);
            // }
            loadQaList(1);
        });

        $('#start_date, #end_date').on('change', function() {
            currentPeriod = 'custom';
            currentStartDate = $('#start_date').val();
            currentEndDate = $('#end_date').val();
            // loadQaList(1);
        });

        $('#search_btn').on('click', function() {
            currentStartDate = $('#start_date').val();
            currentEndDate = $('#end_date').val();
            loadQaList(1);
        });

        // 리스트 불러오기
        function loadQaList(page) {
            currentPage = page;

            $.ajax({
                url: './update.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    act: 'qa_list',
                    status: currentStatus,
                    period: currentPeriod,
                    start_date: currentStartDate,
                    end_date: currentEndDate,
                    pg: page
                },
                success: function(res) {
                    if (res.success) {
                        renderQaTable(res.data);
                        renderPaging(res.total_pages, page);
                    } else {
                        $('#qa_list_body').html('<tr><td colspan="5" class="text-center py-4 text-muted">' + (res.message || '조회된 문의 내역이 없습니다.') + '</td></tr>');
                        $('#qa_paging').html('');
                    }
                },
                error: function() {
                    $('#qa_list_body').html('<tr><td colspan="5" class="text-center py-4 text-muted">서버 연결 오류</td></tr>');
                }
            });
        }

        // 테이블 렌더링
        function renderQaTable(items) {
            const $body = $('#qa_list_body');
            $body.empty();

            if (!items || items.length === 0) {
                $body.html('<tr><td colspan="5" class="text-center py-4 text-muted">조회된 문의 내역이 없습니다.</td></tr>');
                return;
            }

            items.forEach(item => {
                let statusHtml = '';
                if (item.rt_status === 'answered') {
                    statusHtml = '<span class="badge bg-success text-white">답변완료</span>';
                } else {
                    statusHtml = '<span class="badge bg-warning text-dark">답변대기</span>';
                }

                const row = `
            <tr>
                <td>${item.idx}</td>
                <td class="text-start">
                    <a href="javascript:void(0)" onclick="viewQaDetail(${item.idx})" class="text-dark fw-medium">
                        ${item.rt_title}
                    </a>
                </td>
                <td class="text-center">${statusHtml}</td>
                <td class="text-center">${item.created_at_fmt}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill"
                            onclick="viewQaDetail(${item.idx})">
                        보기
                    </button>
                </td>
            </tr>
            `;
                $body.append(row);
            });
        }

        // 페이징 렌더링
        function renderPaging(totalPages, current) {
            const $paging = $('#qa_paging');
            $paging.empty();

            if (totalPages <= 1) return;

            let html = '<nav><ul class="pagination justify-content-center">';

            html += `<li class="page-item ${current === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="loadQaList(${current-1}); return false;">이전</a>
             </li>`;

            for (let i = 1; i <= totalPages; i++) {
                html += `<li class="page-item ${i === current ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="loadQaList(${i}); return false;">${i}</a>
                 </li>`;
            }

            html += `<li class="page-item ${current === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="loadQaList(${current+1}); return false;">다음</a>
             </li>`;

            html += '</ul></nav>';
            $paging.html(html);
        }

        // 문의 상세 보기 (필요한 페이지로 연결)
        window.viewQaDetail = function(qaIdx) {
            location.href = `../qa-edit?idx=${qaIdx}`;
            // 또는 모달로 띄우고 싶다면 모달 열기 로직 추가
        };

        // 초기 로드
        loadQaList(1);
    });
</script>

<? include_once("./inc/tail.php"); ?>
