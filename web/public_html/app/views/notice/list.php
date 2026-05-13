<?php
// =====================
// 1. 페이징 설정 (초기 렌더용)
// =====================
$perPage = 2; // 페이지당 게시글 수
$page    = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

$total      = count($notices);
$totalPages = max(1, (int)ceil($total / $perPage));
$offset     = ($page - 1) * $perPage;

// 현재 페이지에 보여줄 데이터
$pageNotices = array_slice($notices, $offset, $perPage);

// 페이지 블록 (1~5, 6~10 이런 식으로)
$pageBlockSize = 5;
$currentBlock  = (int)ceil($page / $pageBlockSize);
$startPage     = ($currentBlock - 1) * $pageBlockSize + 1;
$endPage       = min($startPage + $pageBlockSize - 1, $totalPages);

$hasData = $total > 0;
?>

<div class="wrap">
    <div class="sub_pg">

        <!-- 검색 영역 -->
        <div class="bg-light py-3">
            <div class="container">
                <form class="sch_ip align-items-center" id="noticeSearchForm">
                    <input type="search"
                           class="form-control fs_14 flex-fill border-0"
                           id="noticeSearchInput"
                           placeholder="검색어를 입력해주세요">
                    <button class="btn btn-icon flex-shrink-0" type="submit">
                        <img src="<?=DESIGN_HTTP?>/img/ic_sch_gray.png" style="width:2.0rem;">
                    </button>
                </form>
            </div>
        </div>

        <!-- 공지사항 없음 문구 -->
        <div class="no_data"
             id="noticeNoData"
             style="<?= $hasData ? 'display:none;' : '' ?>">
            <img src="<?= DESIGN_HTTP ?>/img/img_mark.png">
            <p class="line_h1_4 mt-3 fs_15">등록된 공지사항이 없습니다.</p>
        </div>

        <!-- 공지사항 리스트 컨테이너 (항상 존재해야 AJAX에서 조작 가능) -->
        <div class="notice_list border-top fs_15 fw_500"
             id="noticeListWrapper"
             style="<?= $hasData ? '' : 'display:none;' ?>">
            <ul id="noticeList">
                <?php if ($hasData): ?>
                    <?php foreach ($pageNotices as $notice): ?>
                        <li>
                            <a class="item d-flex align-items-center"
                               href="./detail.php?idx=<?= $notice['idx'] ?>">
                                <div class="flex-fill">
                                    <div class="line1_text flex-fill">
                                        <?= htmlspecialchars($notice['title'], ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                    <div class="tg_400 fs_13 mt-2">
                                        <?= htmlspecialchars($notice['regdate'], ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                </div>
                                <img class="flex-shrink-0"
                                     src="<?= DESIGN_HTTP ?>/img/ic_more02.png"
                                     style="width:2.0rem;">
                            </a>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>

        <!-- 🔥 로딩바 영역 (Bootstrap 스피너 사용, 퍼블리싱 class 건들지 않고 id만 추가) -->
        <div id="noticeLoading" style="display:none; text-align:center; padding:20px 0;">
            <div class="spinner-border" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>

        <!-- 페이징 컨테이너 (항상 존재하게) -->
        <article class="my-5"
                 id="noticePagination"
                 style="<?= ($hasData && $totalPages > 1) ? '' : 'display:none;' ?>">
            <ul class="pagination fs_16" id="noticePaginationList">
                <?php if ($hasData && $totalPages > 1): ?>
                    <!-- 이전 -->
                    <li>
                        <?php if ($page <= 1): ?>
                            <a href="#" class="disabled arrow">
                                <img src="<?= DESIGN_HTTP ?>/img/pg_prev.svg">
                            </a>
                        <?php else: ?>
                            <a href="#"
                               class="arrow"
                               data-page="<?= $page - 1 ?>">
                                <img src="<?= DESIGN_HTTP ?>/img/pg_prev.svg">
                            </a>
                        <?php endif; ?>
                    </li>

                    <!-- 페이지 번호 -->
                    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                        <li>
                            <a href="#"
                               class="<?= $i === $page ? 'on' : '' ?>"
                                <?php if ($i !== $page): ?>
                                    data-page="<?= $i ?>"
                                <?php endif; ?>>
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <!-- 다음 -->
                    <li>
                        <?php if ($page >= $totalPages): ?>
                            <a href="#" class="disabled arrow">
                                <img src="<?= DESIGN_HTTP ?>/img/pg_next.svg">
                            </a>
                        <?php else: ?>
                            <a href="#"
                               class="arrow"
                               data-page="<?= $page + 1 ?>">
                                <img src="<?= DESIGN_HTTP ?>/img/pg_next.svg">
                            </a>
                        <?php endif; ?>
                    </li>
                <?php endif; ?>
            </ul>
        </article>

    </div>
</div>

<!-- ===========================
     공지사항 AJAX 스크립트
     =========================== -->
<script>
    $(function () {
        console.log('공지사항 리스트 스크립트 초기화');

        // PHP에서 내려주는 초기 값
        let currentPage = <?= (int)$page ?>;
        const perPage   = <?= (int)$perPage ?>;

        // 상태 저장용 key (뒤로가기 시 페이지/검색어 유지)
        const noticeStorageKey = 'notice_list_state';

        const $noData         = $('#noticeNoData');
        const $listWrapper    = $('#noticeListWrapper');
        const $list           = $('#noticeList');
        const $pagination     = $('#noticePagination');
        const $paginationList = $('#noticePaginationList');
        const $searchForm     = $('#noticeSearchForm');
        const $searchInput    = $('#noticeSearchInput');
        const $loading        = $('#noticeLoading');

        // 로딩바 표시
        function showLoading() {
            console.log('공지사항 로딩바 표시');
            if ($loading.length) {
                $loading.show();
            }
        }

        // 로딩바 숨기기
        function hideLoading() {
            console.log('공지사항 로딩바 숨기기');
            if ($loading.length) {
                $loading.hide();
            }
        }

        // 리스트 렌더링
        function renderNotices(items) {
            console.log('공지사항 렌더링 데이터:', items);

            if (!$list.length) {
                console.warn('#noticeList 요소를 찾지 못했습니다.');
                return;
            }

            $list.empty();

            // 데이터 없음
            if (!items || !items.length) {
                console.log('공지사항 데이터 없음, no_data 표시');

                if ($noData.length) {
                    $noData.show();
                }
                if ($listWrapper.length) {
                    $listWrapper.hide();
                }
                if ($pagination.length) {
                    $pagination.hide();
                }
                return;
            }

            // 데이터 있음
            if ($noData.length) {
                $noData.hide();
            }
            if ($listWrapper.length) {
                $listWrapper.show();
            }

            items.forEach(function (notice) {
                const $li = $('<li>');
                const $a  = $('<a>')
                    .addClass('item d-flex align-items-center')
                    .attr('href', './detail.php?idx=' + notice.idx);

                const $flexDiv = $('<div>').addClass('flex-fill');
                const $titleDiv = $('<div>')
                    .addClass('line1_text flex-fill')
                    .text(notice.title);
                const $dateDiv = $('<div>')
                    .addClass('tg_400 fs_13 mt-2')
                    .text(notice.regdate);

                $flexDiv.append($titleDiv).append($dateDiv);

                const $img = $('<img>')
                    .addClass('flex-shrink-0')
                    .attr('src', '<?= DESIGN_HTTP ?>/img/ic_more02.png')
                    .attr('style', 'width:2.0rem;');

                $a.append($flexDiv).append($img);
                $li.append($a);
                $list.append($li);
            });
        }

        // 페이징 렌더링
        function renderPagination(meta) {
            console.log('공지사항 페이징 메타:', meta);

            if (!$pagination.length || !$paginationList.length) {
                console.warn('#noticePagination 또는 #noticePaginationList 요소를 찾지 못했습니다.');
                return;
            }

            if (!meta || meta.total_pages <= 1) {
                $pagination.hide();
                return;
            }

            $pagination.show();
            $paginationList.empty();

            const page       = meta.page;
            const totalPages = meta.total_pages;
            const startPage  = meta.start_page;
            const endPage    = meta.end_page;

            // 이전 버튼
            const $prevLi = $('<li>');
            const $prevA  = $('<a>')
                .addClass('arrow')
                .attr('href', '#')
                .append($('<img>').attr('src', '<?= DESIGN_HTTP ?>/img/pg_prev.svg'));

            if (page <= 1) {
                $prevA.addClass('disabled');
            } else {
                $prevA.attr('data-page', page - 1);
            }
            $prevLi.append($prevA);
            $paginationList.append($prevLi);

            // 페이지 번호
            for (let i = startPage; i <= endPage; i++) {
                const $li = $('<li>');
                const $a  = $('<a>')
                    .attr('href', '#')
                    .text(i);

                if (i === page) {
                    $a.addClass('on');
                } else {
                    $a.attr('data-page', i);
                }

                $li.append($a);
                $paginationList.append($li);
            }

            // 다음 버튼
            const $nextLi = $('<li>');
            const $nextA  = $('<a>')
                .addClass('arrow')
                .attr('href', '#')
                .append($('<img>').attr('src', '<?= DESIGN_HTTP ?>/img/pg_next.svg'));

            if (page >= totalPages) {
                $nextA.addClass('disabled');
            } else {
                $nextA.attr('data-page', page + 1);
            }
            $nextLi.append($nextA);
            $paginationList.append($nextLi);
        }

        // 공지사항 리스트 AJAX 로드
        function loadNoticePage(page) {
            console.log('공지사항 페이지 로드 요청:', page);

            const keyword = $.trim($searchInput.val() || '');
            let url = '<?=NOTICE_ACTIONS?>/update.php';
            $.ajax({
                url: url, // 🔥 실제 API 경로
                type: 'POST',
                dataType: 'json',
                data: {
                    page: page,
                    per_page: perPage,
                    search: keyword,
                    act: 'list',
                },
                beforeSend: function () {
                    console.log('공지사항 리스트 AJAX 전송 시작', { page, perPage, keyword });
                    showLoading(); // 🔥 API 호출 시작 시 로딩바 표시
                },
                success: function (res) {
                    console.log('공지사항 리스트 응답:', res);

                    if (res && res.success && res.data) {
                        currentPage = res.data.page;

                        // 🔥 현재 상태 sessionStorage에 저장 (뒤로가기 시 복원용)
                        try {
                            const state = {
                                page: currentPage,
                                per_page: perPage,
                                search: keyword
                            };
                            sessionStorage.setItem(noticeStorageKey, JSON.stringify(state));
                            console.log('공지사항 상태 저장:', state);
                        } catch (e) {
                            console.log('공지사항 상태 저장 실패:', e);
                        }

                        renderNotices(res.data.notices);
                        renderPagination(res.data.pagination);
                    } else {
                        alert(res && res.message ? res.message : '공지사항을 불러오지 못했습니다.');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('공지사항 리스트 AJAX 오류:', status, error);
                    console.log('서버 원본 응답:', xhr.responseText);
                    alert('공지사항을 불러오는 중 오류가 발생했습니다. 잠시 후 다시 시도해 주세요.');
                },
                complete: function () {
                    hideLoading(); // 🔥 성공/실패 상관없이 완료 시 로딩바 숨김
                }
            });
        }

        // 페이징 클릭 이벤트 (동적/초기 공통)
        $(document).on('click', '#noticePagination a', function (e) {
            const page = $(this).data('page');

            // data-page가 없으면(현재 페이지 or disabled) 아무 동작 안 함
            if (!page) {
                return;
            }

            e.preventDefault();
            console.log('공지사항 페이지 링크 클릭:', page);
            loadNoticePage(page);
        });

        // 검색 폼 submit → 1페이지부터 다시 조회
        $searchForm.on('submit', function (e) {
            e.preventDefault();
            console.log('공지사항 검색:', $searchInput.val());
            loadNoticePage(1);
        });

        // 🔥 페이지 로드시 sessionStorage에 저장된 상태가 있으면 복원
        (function restoreState() {
            try {
                const saved = sessionStorage.getItem(noticeStorageKey);
                if (!saved) {
                    return;
                }

                const state = JSON.parse(saved);
                console.log('저장된 공지사항 상태:', state);

                if (!state) {
                    return;
                }

                if (state.search) {
                    $searchInput.val(state.search);
                }

                // page가 1이고 검색어도 없다면 굳이 AJAX 다시 호출 안 함
                if (state.page && (state.page !== 1 || state.search)) {
                    // 🔥 뒤로가기 시 1페이지가 잠깐 보이는 깜빡임 방지:
                    // 기존 리스트/없는데이터/페이징 숨기고 로딩바 먼저 보여줌
                    if ($listWrapper.length) {
                        $listWrapper.hide();
                    }
                    if ($noData.length) {
                        $noData.hide();
                    }
                    if ($pagination.length) {
                        $pagination.hide();
                    }
                    showLoading();

                    loadNoticePage(state.page);
                }
            } catch (e) {
                console.log('공지사항 상태 복원 실패:', e);
            }
        })();
    });
</script>
