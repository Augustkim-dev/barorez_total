<?php
$shopId        = (int)($_SHOP_ID ?? 0);
$row           = $_SHOP_ROW ?? [];
$shopImg       = $_SHOP_IMG ?? (DESIGN_HTTP . '/img/pr_sample01.jpg');
$menuRow       = $_MENU_ROW ?? null;
$summary       = $_REVIEW_SUMMARY ?? [];
$currentSort   = $_REVIEW_SORT ?? 'latest';

$reviewCount = (int)($summary['review_count'] ?? 0);
$avgScore    = number_format((float)($summary['avg_score'] ?? 0), 1);

$scoreMap = [
    5 => (int)($summary['score_5'] ?? 0),
    4 => (int)($summary['score_4'] ?? 0),
    3 => (int)($summary['score_3'] ?? 0),
    2 => (int)($summary['score_2'] ?? 0),
    1 => (int)($summary['score_1'] ?? 0),
];

function review_list_percent($count, $total)
{
    $count = (int)$count;
    $total = (int)$total;
    if ($total < 1) {
        return 0;
    }
    return (int)round(($count / $total) * 100);
}

$pageTitle = !empty($menuRow['sm_title'])
    ? trim((string)$menuRow['sm_title']) . ' 리뷰'
    : ($shopFullName !== '' ? $shopFullName . ' 리뷰' : '리뷰');

$pageDesc = !empty($menuRow['sm_title'])
    ? $shopFullName . '에서 ' . trim((string)$menuRow['sm_title']) . '에 작성된 리뷰만 모아봤어요.'
    : '이 매장에 작성된 전체 리뷰를 보여드려요.';

$reviewConfig = [
    'apiUrl'        => $_REVIEW_API_URL ?? (REVIEW_ACTIONS . '/update.php'),
    'shopId'        => $shopId,
    'menuId'        => (int)($menuRow['idx'] ?? 0),
    'sort'          => $currentSort,
    'pageSize'      => (int)($_REVIEW_PAGE_SIZE ?? 10),
    'hasMore'       => !empty($_INITIAL_HAS_MORE),
    'initialReviews'=> $_INITIAL_REVIEWS ?? [],
];
?>
<div class="wrap review-list-page">
    <div class="idx_pg">
        <section class="container review-summary-card">
            <div class="review-summary-card__score">
                <strong><?= $avgScore ?></strong>
                <div class="review-stars review-stars--summary">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <span class="review-star-icon <?= $i <= round((float)$avgScore) ? 'is-active' : '' ?>">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M12 2.2l2.93 5.94 6.55.95-4.74 4.61 1.12 6.52L12 17.15 6.14 20.22l1.12-6.52L2.52 9.09l6.55-.95L12 2.2z"></path>
                            </svg>
                        </span>
                    <?php endfor; ?>
                </div>
                <p>리뷰 <?= number_format($reviewCount) ?>개</p>
            </div>

            <div class="review-summary-card__graph">
                <?php for ($score = 5; $score >= 1; $score--): ?>
                    <?php $percent = review_list_percent($scoreMap[$score], $reviewCount); ?>
                    <div class="review-graph-row">
                        <span class="review-graph-row__label"><?= $score ?></span>
                        <div class="review-graph-row__bar">
                            <i style="width: <?= $percent ?>%;"></i>
                        </div>
                        <span class="review-graph-row__value"><?= $percent ?>%</span>
                    </div>
                <?php endfor; ?>
            </div>
        </section>

        <section class="container review-toolbar">
            <div class="review-toolbar__scope">
                <?php if (!empty($menuRow['sm_title'])): ?>
                    <span class="review-scope-chip"><?= htmlspecialchars($menuRow['sm_title']) ?></span>
                <?php else: ?>
                    <span class="review-scope-chip">전체 리뷰</span>
                <?php endif; ?>
            </div>

            <div class="review-sort-wrap">
                <select id="reviewSort" class="review-sort-select">
                    <option value="latest" <?= $currentSort === 'latest' ? 'selected' : '' ?>>최신순</option>
                    <option value="rating_high" <?= $currentSort === 'rating_high' ? 'selected' : '' ?>>별점 높은 순</option>
                    <option value="rating_low" <?= $currentSort === 'rating_low' ? 'selected' : '' ?>>별점 낮은 순</option>
                </select>
                <span class="review-sort-arrow"></span>
            </div>
        </section>

        <section class="container review-feed" id="reviewFeed"></section>

        <div class="review-feed-loader d-none" id="reviewFeedLoader">
            <div class="spinner-border text-primary" role="status"></div>
            <p>리뷰 불러오는 중...</p>
        </div>

        <div id="reviewFeedSentinel"></div>
    </div>
</div>

<div class="review-viewer" id="reviewImageViewer" hidden aria-hidden="true">
    <div class="review-viewer__top">
        <button type="button" class="review-viewer__close" id="reviewViewerClose" aria-label="닫기">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M6 6l12 12M18 6L6 18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"></path>
            </svg>
        </button>
        <p class="review-viewer__count" id="reviewViewerCount">1 / 1</p>
    </div>

    <div class="review-viewer__body">
        <button type="button" class="review-viewer__nav review-viewer__nav--prev" id="reviewViewerPrev" aria-label="이전 이미지">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M15.5 4.5L8 12l7.5 7.5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
        </button>

        <div class="review-viewer__track" id="reviewViewerTrack"></div>

        <button type="button" class="review-viewer__nav review-viewer__nav--next" id="reviewViewerNext" aria-label="다음 이미지">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M8.5 4.5L16 12l-7.5 7.5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
        </button>
    </div>

    <div class="review-viewer__bottom">
        <div class="review-viewer__dots" id="reviewViewerDots"></div>
    </div>
</div>

<style>
    .review-list-page {
        background: #fff;
        min-height: 100vh;
    }
    .review-list-page .idx_pg {
        min-height: 100vh;
        background: #fff;
        padding-bottom: 4rem;
    }
    .review-list-page .container {
        width: 100%;
        max-width: 76rem;
        margin: 0 auto;
    }
    .review-list-topbar {
        position: sticky;
        top: 0;
        z-index: 30;
        display: flex;
        align-items: center;
        gap: 1rem;
        height: 6.4rem;
        padding: 0 1.8rem;
        background: rgba(255,255,255,.96);
        backdrop-filter: blur(12px);
        border-bottom: 1px solid #edf0f3;
    }
    .review-list-topbar__back {
        width: 4rem;
        height: 4rem;
        padding: 0;
        border: 0;
        border-radius: 999px;
        background: transparent;
        color: #1f2328;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .review-list-topbar__back svg {
        width: 2.2rem;
        height: 2.2rem;
    }
    .review-list-topbar h1 {
        margin: 0;
        font-size: 2.3rem;
        font-weight: 700;
        color: #1f2328;
        letter-spacing: -.02em;
        line-height: 1.3;
        word-break: keep-all;
    }
    .review-list-hero {
        display: flex;
        align-items: center;
        gap: 1.6rem;
        padding: 2.2rem 1.8rem 1.4rem;
    }
    .review-list-hero__text {
        min-width: 0;
        flex: 1;
    }
    .review-list-hero__eyebrow {
        margin: 0 0 .8rem;
        font-size: 1.2rem;
        font-weight: 700;
        letter-spacing: .14em;
        text-transform: uppercase;
        color: #ff5a1f;
    }
    .review-list-hero__text h2 {
        margin: 0;
        font-size: 2.7rem;
        line-height: 1.3;
        font-weight: 700;
        color: #1f2328;
        word-break: keep-all;
    }
    .review-list-hero__text p:last-child {
        margin: .8rem 0 0;
        font-size: 1.45rem;
        line-height: 1.6;
        color: #7b8794;
    }
    .review-list-hero__thumb {
        width: 8rem;
        height: 8rem;
        border-radius: 2rem;
        overflow: hidden;
        background: #f3f5f7;
        flex-shrink: 0;
        box-shadow: 0 1rem 2.4rem rgba(31,35,40,.10);
    }
    .review-list-hero__thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .review-summary-card {
        display: grid;
        grid-template-columns: 1fr 1.2fr;
        gap: 1.6rem;
        margin: 0 1.8rem 1.4rem;
        padding: 2rem 1.8rem;
        border-radius: 2.4rem;
    }
    .review-summary-card__score {
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .review-summary-card__score strong {
        font-size: 4.8rem;
        line-height: 1;
        font-weight: 800;
        color: #1f2328;
    }
    .review-summary-card__score p {
        margin: .8rem 0 0;
        font-size: 1.45rem;
        color: #697586;
    }
    .review-summary-card__graph {
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: .85rem;
    }
    .review-graph-row {
        display: grid;
        grid-template-columns: 1.8rem 1fr 3.8rem;
        align-items: center;
        gap: .8rem;
    }
    .review-graph-row__label,
    .review-graph-row__value {
        font-size: 1.35rem;
        color: #7b8794;
    }
    .review-graph-row__bar {
        position: relative;
        height: .8rem;
        border-radius: 999px;
        background: #e3e8ee;
        overflow: hidden;
    }
    .review-graph-row__bar i {
        position: absolute;
        inset: 0 auto 0 0;
        display: block;
        border-radius: inherit;
        background: linear-gradient(90deg, #ffb100 0%, #ffc933 100%);
    }
    .review-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1.2rem;
        padding: .8rem 1.8rem 1.8rem;
    }
    .review-scope-chip {
        display: inline-flex;
        align-items: center;
        height: 3.4rem;
        padding: 0 1.4rem;
        border-radius: 999px;
        background: #eef4ff;
        color: #2b6ff2;
        font-size: 1.35rem;
        font-weight: 600;
    }
    .review-sort-wrap {
        position: relative;
        flex-shrink: 0;
    }
    .review-sort-select {
        min-width: 14.5rem;
        height: 4.2rem;
        padding: 0 4rem 0 1.6rem;
        border: 1px solid #dde3ea;
        border-radius: 999px;
        background: #fff;
        font-size: 1.45rem;
        font-weight: 600;
        color: #1f2328;
        appearance: none;
        -webkit-appearance: none;
    }
    .review-sort-arrow {
        position: absolute;
        top: 50%;
        right: 1.5rem;
        width: .9rem;
        height: .9rem;
        border-right: 2px solid #6b7280;
        border-bottom: 2px solid #6b7280;
        transform: translateY(-65%) rotate(45deg);
        pointer-events: none;
    }
    .review-feed {
        padding: 0 1.8rem 2.4rem;
    }
    .review-card {
        padding: 2.2rem 0;
        border-top: 1px solid #edf0f3;
    }
    .review-card:first-child {
        border-top: 0;
    }
    .review-card__head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1.2rem;
        margin-bottom: 1.4rem;
    }
    .review-card__writer {
        margin: 0 0 .6rem;
        font-size: 1.8rem;
        font-weight: 700;
        color: #1f2328;
    }
    .review-card__date {
        font-size: 1.35rem;
        color: #8b95a1;
        white-space: nowrap;
        padding-top: .2rem;
    }
    .review-stars {
        display: flex;
        align-items: center;
        gap: .2rem;
    }
    .review-stars--summary {
        margin-top: 1rem;
    }
    .review-stars--card {
        gap: .15rem;
    }
    .review-star-icon {
        display: inline-flex;
        width: 1.9rem;
        height: 1.9rem;
        color: #dfe5eb;
    }
    .review-star-icon.is-active {
        color: #ffb100;
    }
    .review-star-icon svg {
        width: 100%;
        height: 100%;
        fill: currentColor;
    }
    .review-card__gallery {
        position: relative;
        margin-bottom: 1.4rem;
    }
    .review-card__gallery-track {
        display: flex;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        scrollbar-width: none;
        -ms-overflow-style: none;
        border-radius: 2rem;
        background: #f3f5f7;
        -webkit-overflow-scrolling: touch;
    }
    .review-card__gallery-track::-webkit-scrollbar {
        display: none;
    }
    .review-card__gallery-slide {
        flex: 0 0 100%;
        scroll-snap-align: start;
        overflow: hidden;
        background: #f3f5f7;
        cursor: zoom-in;
    }
    .review-card__gallery-slide img {
        width: 100%;
        aspect-ratio: 1.08 / 1;
        object-fit: cover;
        display: block;
        user-select: none;
        -webkit-user-drag: none;
    }
    .review-card__gallery-dots {
        position: absolute;
        left: 0;
        right: 0;
        bottom: 1.2rem;
        display: flex;
        justify-content: center;
        gap: .6rem;
    }
    .review-card__gallery-dot {
        width: .7rem;
        height: .7rem;
        padding: 0;
        border: 0;
        border-radius: 999px;
        background: rgba(255,255,255,.5);
    }
    .review-card__gallery-dot.is-active {
        width: 1.9rem;
        background: #fff;
    }
    .review-card__content {
        margin: 0;
        font-size: 1.6rem;
        line-height: 1.75;
        color: #2b3138;
        word-break: keep-all;
    }
    .review-card__menus {
        display: flex;
        flex-wrap: wrap;
        gap: .8rem;
        margin-top: 1.4rem;
    }
    .review-menu-chip {
        display: inline-flex;
        align-items: center;
        min-height: 3.2rem;
        padding: 0 1.3rem;
        border-radius: 999px;
        background: #f2f7ff;
        color: #2b6ff2;
        font-size: 1.3rem;
        font-weight: 600;
    }
    .review-menu-chip--more {
        background: #f3f5f7;
        color: #6b7280;
    }
    .review-empty {
        padding: 6rem 2rem;
        border: 1px dashed #dde3ea;
        border-radius: 2.2rem;
        background: #fafbfc;
        text-align: center;
    }
    .review-empty strong {
        display: block;
        font-size: 1.9rem;
        color: #1f2328;
    }
    .review-empty p {
        margin: .8rem 0 0;
        font-size: 1.45rem;
        line-height: 1.6;
        color: #8b95a1;
    }
    .review-feed-loader {
        padding: 2rem 1.8rem 4rem;
        text-align: center;
    }
    .review-feed-loader p {
        margin: .8rem 0 0;
        font-size: 1.4rem;
        color: #8b95a1;
    }
    #reviewFeedSentinel {
        height: 1px;
    }
    .is-review-viewer-open,
    .is-review-viewer-open body {
        overflow: hidden;
    }
    .review-viewer[hidden] {
        display: none !important;
    }
    .review-viewer {
        position: fixed;
        inset: 0;
        z-index: 1200;
        background: rgba(0,0,0,.96);
        display: flex;
        flex-direction: column;
    }
    .review-viewer__top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: calc(1.2rem + env(safe-area-inset-top)) 1.6rem 1.2rem;
        color: #fff;
    }
    .review-viewer__close {
        width: 4.4rem;
        height: 4.4rem;
        padding: 0;
        border: 0;
        border-radius: 999px;
        background: rgba(255,255,255,.12);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .review-viewer__close svg {
        width: 2rem;
        height: 2rem;
    }
    .review-viewer__count {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 600;
        color: #fff;
    }
    .review-viewer__body {
        position: relative;
        flex: 1;
        min-height: 0;
    }
    .review-viewer__track {
        height: 100%;
        display: flex;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        scrollbar-width: none;
        -ms-overflow-style: none;
        -webkit-overflow-scrolling: touch;
    }
    .review-viewer__track::-webkit-scrollbar {
        display: none;
    }
    .review-viewer__slide {
        flex: 0 0 100%;
        scroll-snap-align: start;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1.6rem;
    }
    .review-viewer__slide img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        border-radius: 1.6rem;
    }
    .review-viewer__nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 4.8rem;
        height: 4.8rem;
        padding: 0;
        border: 0;
        border-radius: 999px;
        background: rgba(255,255,255,.14);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 2;
    }
    .review-viewer__nav svg {
        width: 2rem;
        height: 2rem;
    }
    .review-viewer__nav--prev {
        left: 1.2rem;
    }
    .review-viewer__nav--next {
        right: 1.2rem;
    }
    .review-viewer__nav.is-hidden {
        display: none;
    }
    .review-viewer__bottom {
        padding: 1.2rem 1.6rem calc(1.6rem + env(safe-area-inset-bottom));
    }
    .review-viewer__dots {
        display: flex;
        justify-content: center;
        gap: .7rem;
    }
    .review-viewer__dot {
        width: .8rem;
        height: .8rem;
        padding: 0;
        border: 0;
        border-radius: 999px;
        background: rgba(255,255,255,.32);
    }
    .review-viewer__dot.is-active {
        width: 2.2rem;
        background: #fff;
    }
    @media (min-width: 768px) {
        .review-list-page {
            background: #f4f6f8;
        }
        .review-list-page .idx_pg {
            max-width: 76rem;
            margin: 0 auto;
            box-shadow: 0 2rem 5rem rgba(15,23,42,.08);
        }
    }
    @media (max-width: 560px) {
        .review-summary-card {
            grid-template-columns: 1fr;
        }
        .review-viewer__nav {
            width: 4.2rem;
            height: 4.2rem;
        }
    }
</style>


<script>
    window.REVIEW_LIST_CONFIG = <?= json_encode($reviewConfig, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
</script>

<script>
    (function () {
        var config = window.REVIEW_LIST_CONFIG || {};

        var reviewApiUrl = config.apiUrl || '';
        var reviewShIdx = Number(config.shopId || 0);
        var reviewSmIdx = Number(config.menuId || 0);
        var reviewSort = config.sort || 'latest';
        var reviewPageSize = Number(config.pageSize || 10);
        var initialReviews = Array.isArray(config.initialReviews) ? config.initialReviews : [];
        var hasMore = !!config.hasMore;
        var nextPage = 2;
        var isLoading = false;
        var observer = null;

        var sortSelect = document.getElementById('reviewSort');
        var feed = document.getElementById('reviewFeed');
        var feedLoader = document.getElementById('reviewFeedLoader');
        var sentinel = document.getElementById('reviewFeedSentinel');

        var viewer = document.getElementById('reviewImageViewer');
        var viewerTrack = document.getElementById('reviewViewerTrack');
        var viewerDots = document.getElementById('reviewViewerDots');
        var viewerCount = document.getElementById('reviewViewerCount');
        var viewerClose = document.getElementById('reviewViewerClose');
        var viewerPrev = document.getElementById('reviewViewerPrev');
        var viewerNext = document.getElementById('reviewViewerNext');

        var viewerImages = [];
        var viewerIndex = 0;

        function escapeHtml(value) {
            return String(value == null ? '' : value).replace(/[&<>"']/g, function (char) {
                return {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#39;'
                }[char];
            });
        }

        function nl2brSafe(value) {
            return escapeHtml(value).replace(/\n/g, '<br>');
        }

        function makeStarHtml(score) {
            var html = '';
            var i;

            for (i = 1; i <= 5; i++) {
                html += ''
                    + '<span class="review-star-icon ' + (i <= score ? 'is-active' : '') + '">'
                    + '    <svg viewBox="0 0 24 24" aria-hidden="true">'
                    + '        <path d="M12 2.2l2.93 5.94 6.55.95-4.74 4.61 1.12 6.52L12 17.15 6.14 20.22l1.12-6.52L2.52 9.09l6.55-.95L12 2.2z"></path>'
                    + '    </svg>'
                    + '</span>';
            }

            return html;
        }

        function makeGalleryHtml(images) {
            if (!images || !images.length) {
                return '';
            }

            var slidesHtml = '';
            var dotsHtml = '';
            var payload = encodeURIComponent(JSON.stringify(images));

            images.forEach(function (src, index) {
                slidesHtml += ''
                    + '<div class="review-card__gallery-slide" data-index="' + index + '">'
                    + '    <img src="' + escapeHtml(src) + '" alt="리뷰 이미지 ' + (index + 1) + '">'
                    + '</div>';

                if (images.length > 1) {
                    dotsHtml += ''
                        + '<button type="button" class="review-card__gallery-dot ' + (index === 0 ? 'is-active' : '') + '" data-index="' + index + '" aria-label="이미지 ' + (index + 1) + '번 보기"></button>';
                }
            });

            return ''
                + '<div class="review-card__gallery" data-images="' + payload + '">'
                + '    <div class="review-card__gallery-track">' + slidesHtml + '</div>'
                + (images.length > 1
                    ? '<div class="review-card__gallery-dots">' + dotsHtml + '</div>'
                    : '')
                + '</div>';
        }

        function makeMenuHtml(menus) {
            if (!menus || !menus.length) {
                return '';
            }

            var visibleMenus = menus.slice(0, 3);
            var restCount = Math.max(0, menus.length - visibleMenus.length);
            var html = '<div class="review-card__menus">';

            visibleMenus.forEach(function (menuName) {
                html += '<span class="review-menu-chip">' + escapeHtml(menuName) + '</span>';
            });

            if (restCount > 0) {
                html += '<span class="review-menu-chip review-menu-chip--more">+' + restCount + '</span>';
            }

            html += '</div>';
            return html;
        }

        function renderReviewCard(review) {
            var score = Number(review.score || 0);
            var writerName = review.writer_name || '방문 고객';
            var dateLabel = review.date_label || '';
            var content = review.content || '';
            var images = Array.isArray(review.images) ? review.images : [];
            var menus = Array.isArray(review.menus) ? review.menus : [];

            return ''
                + '<article class="review-card">'
                + '    <div class="review-card__head">'
                + '        <div>'
                + '            <p class="review-card__writer">' + escapeHtml(writerName) + '</p>'
                + '            <div class="review-stars review-stars--card">' + makeStarHtml(score) + '</div>'
                + '        </div>'
                + '        <span class="review-card__date">' + escapeHtml(dateLabel) + '</span>'
                + '    </div>'
                +          makeGalleryHtml(images)
                + '    <p class="review-card__content">' + nl2brSafe(content) + '</p>'
                +          makeMenuHtml(menus)
                + '</article>';
        }

        function renderEmptyState() {
            feed.innerHTML = ''
                + '<div class="review-empty">'
                + '    <strong>아직 등록된 리뷰가 없습니다.</strong>'
                + '    <p>첫 리뷰를 남겨보세요.</p>'
                + '</div>';
        }

        function renderReviewCards(items, appendMode) {
            var list = Array.isArray(items) ? items : [];
            var html = list.map(renderReviewCard).join('');

            if (!appendMode) {
                if (!html) {
                    renderEmptyState();
                    return;
                }
                feed.innerHTML = html;
                initGalleries(feed);
                return;
            }

            if (!html) {
                return;
            }

            feed.insertAdjacentHTML('beforeend', html);
            initGalleries(feed);
        }

        function setGalleryDotState(gallery, index) {
            var dots = gallery.querySelectorAll('.review-card__gallery-dot');
            dots.forEach(function (dot, dotIndex) {
                dot.classList.toggle('is-active', dotIndex === index);
            });
        }

        function initGallery(gallery) {
            if (!gallery || gallery.dataset.inited === 'Y') {
                return;
            }

            var track = gallery.querySelector('.review-card__gallery-track');
            var dotsWrap = gallery.querySelector('.review-card__gallery-dots');

            if (!track) {
                return;
            }

            function syncGalleryState() {
                var width = track.clientWidth || 1;
                var index = Math.round(track.scrollLeft / width);
                setGalleryDotState(gallery, index);
            }

            track.addEventListener('scroll', syncGalleryState);

            if (dotsWrap) {
                dotsWrap.addEventListener('click', function (e) {
                    var dot = e.target.closest('.review-card__gallery-dot');
                    if (!dot) {
                        return;
                    }

                    var index = Number(dot.getAttribute('data-index') || 0);
                    track.scrollTo({
                        left: track.clientWidth * index,
                        behavior: 'smooth'
                    });
                });
            }

            gallery.addEventListener('click', function (e) {
                var slide = e.target.closest('.review-card__gallery-slide');
                if (!slide) {
                    return;
                }

                var payload = gallery.getAttribute('data-images') || '';
                var images = [];
                var index = Number(slide.getAttribute('data-index') || 0);

                try {
                    images = JSON.parse(decodeURIComponent(payload));
                } catch (err) {
                    images = [];
                }

                openViewer(images, index);
            });

            syncGalleryState();
            gallery.dataset.inited = 'Y';
        }

        function initGalleries(root) {
            var scope = root || document;
            var galleries = scope.querySelectorAll('.review-card__gallery');
            galleries.forEach(initGallery);
        }

        function renderViewer() {
            if (!viewerTrack || !viewerDots) {
                return;
            }

            viewerTrack.innerHTML = viewerImages.map(function (src, index) {
                return ''
                    + '<div class="review-viewer__slide" data-index="' + index + '">'
                    + '    <img src="' + escapeHtml(src) + '" alt="리뷰 이미지 ' + (index + 1) + '">'
                    + '</div>';
            }).join('');

            viewerDots.innerHTML = viewerImages.map(function (_, index) {
                return ''
                    + '<button type="button" class="review-viewer__dot ' + (index === viewerIndex ? 'is-active' : '') + '" data-index="' + index + '" aria-label="이미지 ' + (index + 1) + '번 보기"></button>';
            }).join('');

            syncViewerState();
        }

        function syncViewerState() {
            if (!viewerImages.length) {
                viewerCount.textContent = '0 / 0';
                return;
            }

            viewerCount.textContent = (viewerIndex + 1) + ' / ' + viewerImages.length;

            var dots = viewerDots.querySelectorAll('.review-viewer__dot');
            dots.forEach(function (dot, index) {
                dot.classList.toggle('is-active', index === viewerIndex);
            });

            var singleImage = viewerImages.length <= 1;
            viewerPrev.classList.toggle('is-hidden', singleImage);
            viewerNext.classList.toggle('is-hidden', singleImage);
        }

        function scrollViewerTo(index, smooth) {
            if (!viewerTrack || !viewerImages.length) {
                return;
            }

            if (index < 0) {
                index = 0;
            }
            if (index > viewerImages.length - 1) {
                index = viewerImages.length - 1;
            }

            viewerIndex = index;
            viewerTrack.scrollTo({
                left: viewerTrack.clientWidth * index,
                behavior: smooth ? 'smooth' : 'auto'
            });
            syncViewerState();
        }

        function openViewer(images, index) {
            if (!viewer || !images || !images.length) {
                return;
            }

            viewerImages = images.slice();
            viewerIndex = index || 0;

            renderViewer();

            viewer.hidden = false;
            viewer.setAttribute('aria-hidden', 'false');
            document.documentElement.classList.add('is-review-viewer-open');
            document.body.classList.add('is-review-viewer-open');

            requestAnimationFrame(function () {
                scrollViewerTo(viewerIndex, false);
            });
        }

        function closeViewer() {
            if (!viewer) {
                return;
            }

            viewer.hidden = true;
            viewer.setAttribute('aria-hidden', 'true');
            viewerImages = [];
            viewerIndex = 0;
            viewerTrack.innerHTML = '';
            viewerDots.innerHTML = '';
            document.documentElement.classList.remove('is-review-viewer-open');
            document.body.classList.remove('is-review-viewer-open');
        }

        function setLoadingState(loading) {
            isLoading = loading;
            if (feedLoader) {
                feedLoader.classList.toggle('d-none', !loading);
            }
        }

        function loadMoreReviews() {
            if (!hasMore || isLoading || !reviewApiUrl) {
                return;
            }

            setLoadingState(true);

            $.ajax({
                url: reviewApiUrl,
                type: 'POST',
                dataType: 'json',
                data: {
                    act: 'review_list_page',
                    sh_idx: reviewShIdx,
                    sm_idx: reviewSmIdx,
                    sort: reviewSort,
                    page: nextPage,
                    limit: reviewPageSize
                },
                success: function (res) {
                    if (!res || !res.success) {
                        alert((res && res.message) ? res.message : '리뷰를 불러오지 못했습니다.');
                        return;
                    }

                    renderReviewCards(res.items || [], true);
                    hasMore = !!res.hasMore;
                    nextPage += 1;

                    if (!hasMore && observer) {
                        observer.disconnect();
                    }
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                    alert('서버 연결에 실패했습니다.');
                },
                complete: function () {
                    setLoadingState(false);
                }
            });
        }

        function setupInfiniteScroll() {
            if (!sentinel || !hasMore) {
                return;
            }

            if ('IntersectionObserver' in window) {
                observer = new IntersectionObserver(function (entries) {
                    entries.forEach(function (entry) {
                        if (entry.isIntersecting) {
                            loadMoreReviews();
                        }
                    });
                }, {
                    root: null,
                    rootMargin: '400px 0px',
                    threshold: 0
                });

                observer.observe(sentinel);
                return;
            }

            $(window).on('scroll.reviewList', function () {
                if (!hasMore || isLoading) {
                    return;
                }

                if ($(window).scrollTop() + $(window).height() >= $(document).height() - 400) {
                    loadMoreReviews();
                }
            });
        }

        if (viewerTrack) {
            viewerTrack.addEventListener('scroll', function () {
                if (!viewerImages.length) {
                    return;
                }

                var width = viewerTrack.clientWidth || 1;
                var index = Math.round(viewerTrack.scrollLeft / width);

                if (index !== viewerIndex) {
                    viewerIndex = index;
                    syncViewerState();
                }
            });
        }

        if (viewerDots) {
            viewerDots.addEventListener('click', function (e) {
                var dot = e.target.closest('.review-viewer__dot');
                if (!dot) {
                    return;
                }

                var index = Number(dot.getAttribute('data-index') || 0);
                scrollViewerTo(index, true);
            });
        }

        if (viewerPrev) {
            viewerPrev.addEventListener('click', function () {
                scrollViewerTo(viewerIndex - 1, true);
            });
        }

        if (viewerNext) {
            viewerNext.addEventListener('click', function () {
                scrollViewerTo(viewerIndex + 1, true);
            });
        }

        if (viewerClose) {
            viewerClose.addEventListener('click', closeViewer);
        }

        document.addEventListener('keydown', function (e) {
            if (!viewer || viewer.hidden) {
                return;
            }

            if (e.key === 'Escape') {
                closeViewer();
            } else if (e.key === 'ArrowLeft') {
                scrollViewerTo(viewerIndex - 1, true);
            } else if (e.key === 'ArrowRight') {
                scrollViewerTo(viewerIndex + 1, true);
            }
        });

        if (sortSelect) {
            sortSelect.addEventListener('change', function () {
                var url = new URL(window.location.href);
                url.searchParams.set('sort', this.value);
                window.location.href = url.toString();
            });
        }

        renderReviewCards(initialReviews, false);
        setupInfiniteScroll();
    })();
</script>
