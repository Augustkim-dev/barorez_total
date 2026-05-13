<?php
$totalReviewCount = (int)($_MY_REVIEW_TOTAL ?? 0);
$initialReviews   = $_MY_REVIEW_ITEMS ?? [];
$hasMore          = !empty($_MY_REVIEW_HAS_MORE);
$pageSize         = (int)($_MY_REVIEW_PAGE_SIZE ?? 10);
$apiUrl           = $_MY_REVIEW_API_URL ?? (REVIEW_ACTIONS . '/update.php');

$pageConfig = [
    'apiUrl'         => $apiUrl,
    'pageSize'       => $pageSize,
    'hasMore'        => $hasMore,
    'initialReviews' => $initialReviews,
];
?>
<div class="wrap my-review-page">
    <div class="idx_pg">
        <section class="container my-review-feed" id="myReviewFeed"></section>

        <div class="my-review-loader d-none" id="myReviewLoader">
            <div class="spinner-border text-primary" role="status"></div>
            <p>리뷰 불러오는 중...</p>
        </div>

        <div id="myReviewSentinel"></div>
    </div>
</div>

<div class="my-review-viewer" id="myReviewImageViewer" hidden aria-hidden="true">
    <div class="my-review-viewer__top">
        <button type="button" class="my-review-viewer__close" id="myReviewViewerClose" aria-label="닫기">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M6 6l12 12M18 6L6 18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"></path>
            </svg>
        </button>
        <p class="my-review-viewer__count" id="myReviewViewerCount">1 / 1</p>
    </div>

    <div class="my-review-viewer__body">
        <button type="button" class="my-review-viewer__nav my-review-viewer__nav--prev" id="myReviewViewerPrev" aria-label="이전 이미지">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M15.5 4.5L8 12l7.5 7.5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
        </button>

        <div class="my-review-viewer__track" id="myReviewViewerTrack"></div>

        <button type="button" class="my-review-viewer__nav my-review-viewer__nav--next" id="myReviewViewerNext" aria-label="다음 이미지">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M8.5 4.5L16 12l-7.5 7.5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
        </button>
    </div>

    <div class="my-review-viewer__bottom">
        <div class="my-review-viewer__dots" id="myReviewViewerDots"></div>
    </div>
</div>

<style>
    .my-review-page {
        background: #fff;
        min-height: 100vh;
    }
    .my-review-page .idx_pg {
        min-height: 100vh;
        background: #fff;
        padding-bottom: 4rem;
    }
    .my-review-page .container {
        width: 100%;
        max-width: 76rem;
        margin: 0 auto;
    }
    .my-review-topbar {
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
    .my-review-topbar__back {
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
    .my-review-topbar__back svg {
        width: 2.2rem;
        height: 2.2rem;
    }
    .my-review-topbar h1 {
        margin: 0;
        font-size: 2.3rem;
        font-weight: 700;
        color: #1f2328;
        letter-spacing: -.02em;
    }
    .my-review-hero {
        padding: 2.4rem 1.8rem 1.8rem;
    }
    .my-review-hero__eyebrow {
        margin: 0 0 .8rem;
        font-size: 1.2rem;
        font-weight: 700;
        letter-spacing: .14em;
        text-transform: uppercase;
        color: #ff5a1f;
    }
    .my-review-hero h2 {
        margin: 0;
        font-size: 2.8rem;
        line-height: 1.3;
        font-weight: 700;
        color: #1f2328;
    }
    .my-review-hero__desc {
        margin: .8rem 0 0;
        font-size: 1.45rem;
        line-height: 1.6;
        color: #7b8794;
    }
    .my-review-hero__count {
        margin-top: 1.6rem;
        display: inline-flex;
        align-items: center;
        gap: .8rem;
        padding: 1.1rem 1.5rem;
        border-radius: 1.6rem;
        background: #f7f8fa;
    }
    .my-review-hero__count strong {
        font-size: 2rem;
        font-weight: 800;
        color: #1f2328;
    }
    .my-review-hero__count span {
        font-size: 1.35rem;
        color: #697586;
    }
    .my-review-feed {
        padding: 0 1.8rem 2.4rem;
    }
    .my-review-card {
        padding: 2.2rem 0;
        border-top: 1px solid #edf0f3;
    }
    .my-review-card:first-child {
        border-top: 0;
    }
    .my-review-card__head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1.2rem;
        margin-bottom: 1.4rem;
    }
    .my-review-card__store {
        margin: 0 0 .6rem;
        font-size: 1.8rem;
        font-weight: 700;
        color: #1f2328;
        line-height: 1.45;
        word-break: keep-all;
    }
    .my-review-card__date {
        font-size: 1.35rem;
        color: #8b95a1;
        white-space: nowrap;
        padding-top: .2rem;
    }
    .my-review-stars {
        display: flex;
        align-items: center;
        gap: .15rem;
    }
    .my-review-star-icon {
        display: inline-flex;
        width: 1.9rem;
        height: 1.9rem;
        color: #dfe5eb;
    }
    .my-review-star-icon.is-active {
        color: #ffb100;
    }
    .my-review-star-icon svg {
        width: 100%;
        height: 100%;
        fill: currentColor;
    }
    .my-review-card__gallery {
        position: relative;
        margin-bottom: 1.4rem;
    }
    .my-review-card__gallery-track {
        display: flex;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        scrollbar-width: none;
        -ms-overflow-style: none;
        border-radius: 2rem;
        background: #f3f5f7;
        -webkit-overflow-scrolling: touch;
    }
    .my-review-card__gallery-track::-webkit-scrollbar {
        display: none;
    }
    .my-review-card__gallery-slide {
        flex: 0 0 100%;
        scroll-snap-align: start;
        overflow: hidden;
        background: #f3f5f7;
        cursor: zoom-in;
    }
    .my-review-card__gallery-slide img {
        width: 100%;
        aspect-ratio: 1.08 / 1;
        object-fit: cover;
        display: block;
        user-select: none;
        -webkit-user-drag: none;
    }
    .my-review-card__gallery-dots {
        position: absolute;
        left: 0;
        right: 0;
        bottom: 1.2rem;
        display: flex;
        justify-content: center;
        gap: .6rem;
    }
    .my-review-card__gallery-dot {
        width: .7rem;
        height: .7rem;
        padding: 0;
        border: 0;
        border-radius: 999px;
        background: rgba(255,255,255,.5);
    }
    .my-review-card__gallery-dot.is-active {
        width: 1.9rem;
        background: #fff;
    }
    .my-review-card__content {
        margin: 0;
        font-size: 1.6rem;
        line-height: 1.75;
        color: #2b3138;
        word-break: keep-all;
    }
    .my-review-card__menus {
        display: flex;
        flex-wrap: wrap;
        gap: .8rem;
        margin-top: 1.4rem;
    }
    .my-review-menu-chip {
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
    .my-review-menu-chip--more {
        background: #f3f5f7;
        color: #6b7280;
    }
    .my-review-empty {
        padding: 6rem 2rem;
        border: 1px dashed #dde3ea;
        border-radius: 2.2rem;
        background: #fafbfc;
        text-align: center;
    }
    .my-review-empty strong {
        display: block;
        font-size: 1.9rem;
        color: #1f2328;
    }
    .my-review-empty p {
        margin: .8rem 0 0;
        font-size: 1.45rem;
        line-height: 1.6;
        color: #8b95a1;
    }
    .my-review-loader {
        padding: 2rem 1.8rem 4rem;
        text-align: center;
    }
    .my-review-loader p {
        margin: .8rem 0 0;
        font-size: 1.4rem;
        color: #8b95a1;
    }
    #myReviewSentinel {
        height: 1px;
    }
    .is-my-review-viewer-open,
    .is-my-review-viewer-open body {
        overflow: hidden;
    }
    .my-review-viewer[hidden] {
        display: none !important;
    }
    .my-review-viewer {
        position: fixed;
        inset: 0;
        z-index: 1200;
        background: rgba(0,0,0,.96);
        display: flex;
        flex-direction: column;
    }
    .my-review-viewer__top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: calc(1.2rem + env(safe-area-inset-top)) 1.6rem 1.2rem;
        color: #fff;
    }
    .my-review-viewer__close {
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
    .my-review-viewer__close svg {
        width: 2rem;
        height: 2rem;
    }
    .my-review-viewer__count {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 600;
        color: #fff;
    }
    .my-review-viewer__body {
        position: relative;
        flex: 1;
        min-height: 0;
    }
    .my-review-viewer__track {
        height: 100%;
        display: flex;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        scrollbar-width: none;
        -ms-overflow-style: none;
        -webkit-overflow-scrolling: touch;
    }
    .my-review-viewer__track::-webkit-scrollbar {
        display: none;
    }
    .my-review-viewer__slide {
        flex: 0 0 100%;
        scroll-snap-align: start;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1.6rem;
    }
    .my-review-viewer__slide img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        border-radius: 1.6rem;
    }
    .my-review-viewer__nav {
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
    .my-review-viewer__nav svg {
        width: 2rem;
        height: 2rem;
    }
    .my-review-viewer__nav--prev {
        left: 1.2rem;
    }
    .my-review-viewer__nav--next {
        right: 1.2rem;
    }
    .my-review-viewer__nav.is-hidden {
        display: none;
    }
    .my-review-viewer__bottom {
        padding: 1.2rem 1.6rem calc(1.6rem + env(safe-area-inset-bottom));
    }
    .my-review-viewer__dots {
        display: flex;
        justify-content: center;
        gap: .7rem;
    }
    .my-review-viewer__dot {
        width: .8rem;
        height: .8rem;
        padding: 0;
        border: 0;
        border-radius: 999px;
        background: rgba(255,255,255,.32);
    }
    .my-review-viewer__dot.is-active {
        width: 2.2rem;
        background: #fff;
    }
    @media (min-width: 768px) {
        .my-review-page {
            background: #f4f6f8;
        }
        .my-review-page .idx_pg {
            max-width: 76rem;
            margin: 0 auto;
            box-shadow: 0 2rem 5rem rgba(15,23,42,.08);
        }
    }
    @media (max-width: 560px) {
        .my-review-viewer__nav {
            width: 4.2rem;
            height: 4.2rem;
        }
    }
</style>

<script>
    window.MY_REVIEW_PAGE_CONFIG = <?= json_encode($pageConfig, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
</script>

<script>
    (function () {
        var config = window.MY_REVIEW_PAGE_CONFIG || {};

        var reviewApiUrl = config.apiUrl || '';
        var reviewPageSize = Number(config.pageSize || 10);
        var initialReviews = Array.isArray(config.initialReviews) ? config.initialReviews : [];
        var hasMore = !!config.hasMore;
        var nextPage = 2;
        var isLoading = false;
        var observer = null;

        var feed = document.getElementById('myReviewFeed');
        var loader = document.getElementById('myReviewLoader');
        var sentinel = document.getElementById('myReviewSentinel');

        var viewer = document.getElementById('myReviewImageViewer');
        var viewerTrack = document.getElementById('myReviewViewerTrack');
        var viewerDots = document.getElementById('myReviewViewerDots');
        var viewerCount = document.getElementById('myReviewViewerCount');
        var viewerClose = document.getElementById('myReviewViewerClose');
        var viewerPrev = document.getElementById('myReviewViewerPrev');
        var viewerNext = document.getElementById('myReviewViewerNext');

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
            for (var i = 1; i <= 5; i++) {
                html += ''
                    + '<span class="my-review-star-icon ' + (i <= score ? 'is-active' : '') + '">'
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
                    + '<div class="my-review-card__gallery-slide" data-index="' + index + '">'
                    + '    <img src="' + escapeHtml(src) + '" alt="리뷰 이미지 ' + (index + 1) + '">'
                    + '</div>';

                if (images.length > 1) {
                    dotsHtml += ''
                        + '<button type="button" class="my-review-card__gallery-dot ' + (index === 0 ? 'is-active' : '') + '" data-index="' + index + '" aria-label="이미지 ' + (index + 1) + '번 보기"></button>';
                }
            });

            return ''
                + '<div class="my-review-card__gallery" data-images="' + payload + '">'
                + '    <div class="my-review-card__gallery-track">' + slidesHtml + '</div>'
                + (images.length > 1 ? '<div class="my-review-card__gallery-dots">' + dotsHtml + '</div>' : '')
                + '</div>';
        }

        function makeMenuHtml(menus) {
            if (!menus || !menus.length) {
                return '';
            }

            var visibleMenus = menus.slice(0, 3);
            var restCount = Math.max(0, menus.length - visibleMenus.length);
            var html = '<div class="my-review-card__menus">';

            visibleMenus.forEach(function (menuName) {
                html += '<span class="my-review-menu-chip">' + escapeHtml(menuName) + '</span>';
            });

            if (restCount > 0) {
                html += '<span class="my-review-menu-chip my-review-menu-chip--more">+' + restCount + '</span>';
            }

            html += '</div>';
            return html;
        }

        function renderReviewCard(review) {
            var storeName = review.store_name || '매장 정보 없음';
            var score = Number(review.score || 0);
            var content = review.content || '';
            var dateLabel = review.date_label || '';
            var images = Array.isArray(review.images) ? review.images : [];
            var menus = Array.isArray(review.menus) ? review.menus : [];

            return ''
                + '<article class="my-review-card">'
                + '    <div class="my-review-card__head">'
                + '        <div>'
                + '            <p class="my-review-card__store">' + escapeHtml(storeName) + '</p>'
                + '            <div class="my-review-stars">' + makeStarHtml(score) + '</div>'
                + '        </div>'
                + '        <span class="my-review-card__date">' + escapeHtml(dateLabel) + '</span>'
                + '    </div>'
                +          makeGalleryHtml(images)
                + '    <p class="my-review-card__content">' + nl2brSafe(content) + '</p>'
                +          makeMenuHtml(menus)
                + '</article>';
        }

        function renderEmptyState() {
            feed.innerHTML = ''
                + '<div class="my-review-empty">'
                + '    <strong>작성한 리뷰가 없습니다.</strong>'
                + '    <p>주문 후 첫 리뷰를 남겨보세요.</p>'
                + '</div>';
        }

        function renderCards(items, appendMode) {
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
            var dots = gallery.querySelectorAll('.my-review-card__gallery-dot');
            dots.forEach(function (dot, dotIndex) {
                dot.classList.toggle('is-active', dotIndex === index);
            });
        }

        function initGallery(gallery) {
            if (!gallery || gallery.dataset.inited === 'Y') {
                return;
            }

            var track = gallery.querySelector('.my-review-card__gallery-track');
            var dotsWrap = gallery.querySelector('.my-review-card__gallery-dots');

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
                    var dot = e.target.closest('.my-review-card__gallery-dot');
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
                var slide = e.target.closest('.my-review-card__gallery-slide');
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
            var galleries = (root || document).querySelectorAll('.my-review-card__gallery');
            galleries.forEach(initGallery);
        }

        function renderViewer() {
            viewerTrack.innerHTML = viewerImages.map(function (src, index) {
                return ''
                    + '<div class="my-review-viewer__slide" data-index="' + index + '">'
                    + '    <img src="' + escapeHtml(src) + '" alt="리뷰 이미지 ' + (index + 1) + '">'
                    + '</div>';
            }).join('');

            viewerDots.innerHTML = viewerImages.map(function (_, index) {
                return ''
                    + '<button type="button" class="my-review-viewer__dot ' + (index === viewerIndex ? 'is-active' : '') + '" data-index="' + index + '" aria-label="이미지 ' + (index + 1) + '번 보기"></button>';
            }).join('');

            syncViewerState();
        }

        function syncViewerState() {
            if (!viewerImages.length) {
                viewerCount.textContent = '0 / 0';
                return;
            }

            viewerCount.textContent = (viewerIndex + 1) + ' / ' + viewerImages.length;

            var dots = viewerDots.querySelectorAll('.my-review-viewer__dot');
            dots.forEach(function (dot, index) {
                dot.classList.toggle('is-active', index === viewerIndex);
            });

            var singleImage = viewerImages.length <= 1;
            viewerPrev.classList.toggle('is-hidden', singleImage);
            viewerNext.classList.toggle('is-hidden', singleImage);
        }

        function scrollViewerTo(index, smooth) {
            if (!viewerImages.length) {
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
            if (!images || !images.length) {
                return;
            }

            viewerImages = images.slice();
            viewerIndex = index || 0;

            renderViewer();

            viewer.hidden = false;
            viewer.setAttribute('aria-hidden', 'false');
            document.documentElement.classList.add('is-my-review-viewer-open');
            document.body.classList.add('is-my-review-viewer-open');

            requestAnimationFrame(function () {
                scrollViewerTo(viewerIndex, false);
            });
        }

        function closeViewer() {
            viewer.hidden = true;
            viewer.setAttribute('aria-hidden', 'true');
            viewerImages = [];
            viewerIndex = 0;
            viewerTrack.innerHTML = '';
            viewerDots.innerHTML = '';
            document.documentElement.classList.remove('is-my-review-viewer-open');
            document.body.classList.remove('is-my-review-viewer-open');
        }

        function setLoadingState(loading) {
            isLoading = loading;
            if (loader) {
                loader.classList.toggle('d-none', !loading);
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
                    act: 'my_review_list_page',
                    page: nextPage,
                    limit: reviewPageSize
                },
                success: function (res) {
                    if (!res || !res.success) {
                        alert((res && res.message) ? res.message : '리뷰를 불러오지 못했습니다.');
                        return;
                    }

                    renderCards(res.items || [], true);
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

            $(window).on('scroll.myReview', function () {
                if (!hasMore || isLoading) {
                    return;
                }

                if ($(window).scrollTop() + $(window).height() >= $(document).height() - 400) {
                    loadMoreReviews();
                }
            });
        }

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

        viewerDots.addEventListener('click', function (e) {
            var dot = e.target.closest('.my-review-viewer__dot');
            if (!dot) {
                return;
            }

            var index = Number(dot.getAttribute('data-index') || 0);
            scrollViewerTo(index, true);
        });

        viewerPrev.addEventListener('click', function () {
            scrollViewerTo(viewerIndex - 1, true);
        });

        viewerNext.addEventListener('click', function () {
            scrollViewerTo(viewerIndex + 1, true);
        });

        viewerClose.addEventListener('click', closeViewer);

        document.addEventListener('keydown', function (e) {
            if (viewer.hidden) {
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

        renderCards(initialReviews, false);
        setupInfiniteScroll();
    })();
</script>
