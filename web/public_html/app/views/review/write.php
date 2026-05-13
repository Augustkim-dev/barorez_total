<?php
$shopId        = (int)($_SHOP_ID ?? $shopId ?? ($_GET['sh_idx'] ?? 0));
$row           = $_SHOP_ROW ?? $row ?? [];
$shopImg       = $_SHOP_IMG ?? $shopImg ?? (defined('DESIGN_HTTP') ? DESIGN_HTTP . '/img/pr_sample01.jpg' : '/img/pr_sample01.jpg');
$orderId       = (int)($_ORDER_ID ?? $orderId ?? ($_GET['od_idx'] ?? 0));
$reviewAction  = $_REVIEW_ACTION ?? $reviewAction ?? REVIEW_ACTIONS.'/update.php';

$orderedMenus = $_ORDER_MENUS ?? $orderedMenus ?? [];

$reviewShopName = trim(($row['sh_title'] ?? '') . ($row['sh_branch_nm'] ?? ''));
$reviewShopName = $reviewShopName !== '' ? $reviewShopName : '매장명';

$reviewSubText = trim($row['sh_addr1'] ?? '');
if ($reviewSubText === '') {
    $reviewSubText = '이용하신 음식과 메뉴 후기를 남겨주시면 다른 고객에게 큰 도움이 됩니다.';
}

?>
<div class="wrap review-page">
    <div class="idx_pg">
        <form id="reviewForm" method="post" action="<?= htmlspecialchars($reviewAction) ?>" enctype="multipart/form-data">
            <input type="hidden" name="ot_idx" value="<?= $orderId ?>">
            <input type="hidden" name="sh_idx" value="<?= $shopId ?>">
            <input type="hidden" name="act" value="create_review">
            <input type="hidden" name="food_score" id="foodScore" value="0">

            <section class="container review-hero">
                <div class="review-hero__text">
                    <h2><?= htmlspecialchars($reviewShopName) ?></h2>
                    <p class="review-sub"><?= htmlspecialchars($reviewSubText) ?></p>
                </div>
                <div class="review-hero__thumb">
                    <img src="<?= htmlspecialchars($shopImg) ?>" alt="<?= htmlspecialchars($reviewShopName) ?>">
                </div>
            </section>

            <div class="bar"></div>

            <section class="container review-section">
                <div class="review-section__hd">
                    <div>
                        <h3>음식은 어떠셨나요?</h3>
                        <p class="review-section__desc">이용하신 음식 만족도를 별점으로 남겨주세요.</p>
                    </div>
                    <p class="review-rating-text" id="ratingLabel">별점을 선택해주세요</p>
                </div>

                <div class="review-stars" id="reviewStars">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <button type="button" class="review-star" data-value="<?= $i ?>" aria-label="<?= $i ?>점">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M12 2.2l2.93 5.94 6.55.95-4.74 4.61 1.12 6.52L12 17.15 6.14 20.22l1.12-6.52L2.52 9.09l6.55-.95L12 2.2z"></path>
                            </svg>
                        </button>
                    <?php endfor; ?>
                </div>
            </section>

            <section class="container review-section">
                <div class="review-section__hd">
                    <div>
                        <h3>리뷰를 작성해주세요</h3>
                        <p class="review-section__desc">맛, 양, 분위기, 재방문 의사 등을 자유롭게 적어주세요.</p>
                    </div>
                    <p class="review-count"><span id="reviewTextCount">0</span>/1000</p>
                </div>

                <div class="review-textarea-wrap">
                    <textarea
                            name="review_contents"
                            id="reviewContents"
                            maxlength="1000"
                            placeholder="주문하신 메뉴의 맛과 양, 이용 경험을 자세히 적어주시면 더 유용한 리뷰가 됩니다."
                    ></textarea>
                </div>
            </section>

            <section class="container review-section">
                <div class="review-section__hd">
                    <div>
                        <h3>사진 추가</h3>
                        <p class="review-section__desc">음식 사진은 최대 5장까지 첨부할 수 있습니다.</p>
                    </div>
                    <p class="review-count"><span id="photoCounter">0</span>/5</p>
                </div>

                <input type="file" id="reviewPhotos" name="review_images[]" accept="image/*" multiple hidden>

                <button type="button" class="review-photo-trigger" id="photoUploadTrigger">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M8 6.5l1.2-2h5.6l1.2 2H18a3 3 0 013 3v7a3 3 0 01-3 3H6a3 3 0 01-3-3v-7a3 3 0 013-3h2zm4 11.2a4.2 4.2 0 100-8.4 4.2 4.2 0 000 8.4zm0-1.8a2.4 2.4 0 100-4.8 2.4 2.4 0 000 4.8z" fill="#222"></path>
                    </svg>
                    <strong>사진 추가 (<span id="photoCounterInline">0</span>/5)</strong>
                    <span>선택 후 아래 미리보기에서 개별 삭제할 수 있어요.</span>
                </button>

                <div class="review-photo-preview" id="photoPreview"></div>
            </section>

            <section class="container review-section review-section--last">
                <div class="review-section__hd">
                    <div>
                        <h3>이용한 메뉴 체크</h3>
                        <p class="review-section__desc">해당 주문에 포함된 메뉴만 노출되도록 연결해주세요.</p>
                    </div>
                </div>

                <div class="review-menu-list">
                    <?php if (!empty($orderedMenus)): ?>
                        <?php foreach ($orderedMenus as $menu): ?>
                            <?php
                            $menuId     = (int)($menu['idx'] ?? $menu['menu_idx'] ?? 0);
                            $menuTitle  = trim((string)($menu['title'] ?? $menu['sm_title'] ?? '메뉴명'));
                            $menuOption = trim((string)($menu['option_text'] ?? $menu['option'] ?? $menu['sm_contents'] ?? ''));
                            $menuImage  = trim((string)($menu['image'] ?? $menu['sm_image'] ?? ''));

                            if ($menuImage !== '' && strpos($menuImage, 'http') !== 0 && strpos($menuImage, '/') !== 0) {
                                $menuImage = '/data/menu/' . ltrim($menuImage, '/');
                            }
                            ?>
                            <label class="review-menu-item">
                                <input type="checkbox" name="menu_ids[]" value="<?= $menuId ?>">
                                <span class="review-menu-check" aria-hidden="true">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M9.55 16.35L5.7 12.5l-1.4 1.4 5.25 5.25L19.7 9l-1.4-1.4z" fill="currentColor"></path>
                                    </svg>
                                </span>

                                <?php if ($menuImage !== ''): ?>
                                    <span class="review-menu-thumb">
                                        <img src="<?= htmlspecialchars($menuImage) ?>" alt="<?= htmlspecialchars($menuTitle) ?>">
                                    </span>
                                <?php else: ?>
                                    <span class="review-menu-thumb review-menu-thumb--empty">MENU</span>
                                <?php endif; ?>

                                <span class="review-menu-meta">
                                    <strong><?= htmlspecialchars($menuTitle) ?></strong>
                                    <?php if ($menuOption !== ''): ?>
                                        <span><?= htmlspecialchars($menuOption) ?></span>
                                    <?php endif; ?>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="review-empty">
                            실제 주문 메뉴 배열이 아직 없으면, 이 영역에 주문 메뉴 데이터만 연결하시면 됩니다.
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <div class="bottom_btn review-bottom">
                <button type="submit" class="btn review-submit" id="btnSubmitReview" disabled>등록하기</button>
            </div>
        </form>
    </div>
</div>

<style>
    .review-page {
        background: #fff;
        min-height: 100vh;
    }
    .review-page .idx_pg {
        min-height: 100vh;
        padding-bottom: 11rem;
        background: #fff;
    }
    .review-page .container {
        width: 100%;
        max-width: 76rem;
        margin: 0 auto;
    }
    .review-topbar {
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
    .review-topbar__back {
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
    .review-topbar__back svg {
        width: 2.2rem;
        height: 2.2rem;
    }
    .review-topbar h1 {
        margin: 0;
        font-size: 2.4rem;
        font-weight: 700;
        color: #1f2328;
        letter-spacing: -.02em;
    }
    .review-page .bar {
        height: 1rem;
        background: #f6f7f9;
    }
    .review-hero {
        display: flex;
        align-items: center;
        gap: 1.6rem;
        padding: 2.4rem 1.8rem;
    }
    .review-hero__text {
        min-width: 0;
        flex: 1;
    }
    .review-eyebrow {
        margin: 0 0 .8rem;
        font-size: 1.2rem;
        font-weight: 700;
        letter-spacing: .14em;
        text-transform: uppercase;
        color: #ff5a1f;
    }
    .review-hero h2 {
        margin: 0;
        font-size: 2.8rem;
        line-height: 1.3;
        font-weight: 700;
        color: #1f2328;
        word-break: keep-all;
    }
    .review-sub {
        margin: .8rem 0 0;
        font-size: 1.5rem;
        line-height: 1.6;
        color: #7b8794;
    }
    .review-hero__thumb {
        width: 8.8rem;
        height: 8.8rem;
        border-radius: 2.2rem;
        overflow: hidden;
        flex-shrink: 0;
        background: #f1f3f6;
        box-shadow: 0 1rem 2.4rem rgba(31,35,40,.10);
    }
    .review-hero__thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .review-section {
        padding: 2.8rem 1.8rem;
        border-bottom: 1px solid #f0f2f5;
    }
    .review-section--last {
        border-bottom: 0;
    }
    .review-section__hd {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 1.2rem;
        margin-bottom: 1.8rem;
    }
    .review-section__hd h3 {
        margin: 0;
        font-size: 2.2rem;
        font-weight: 700;
        line-height: 1.35;
        color: #1f2328;
        word-break: keep-all;
    }
    .review-section__desc {
        margin: .6rem 0 0;
        font-size: 1.4rem;
        line-height: 1.5;
        color: #97a1ad;
    }
    .review-rating-text,
    .review-count {
        margin: 0;
        font-size: 1.4rem;
        font-weight: 600;
        color: #97a1ad;
        white-space: nowrap;
    }
    .review-stars {
        display: flex;
        gap: .8rem;
    }
    .review-star {
        width: 4.8rem;
        height: 4.8rem;
        padding: 0;
        border: 0;
        background: transparent;
        color: #dfe5eb;
        transition: transform .16s ease, color .16s ease;
    }
    .review-star svg {
        width: 100%;
        height: 100%;
        fill: currentColor;
    }
    .review-star.is-active,
    .review-star.is-preview {
        color: #ff5a1f;
    }
    .review-star:active {
        transform: scale(.96);
    }
    .review-textarea-wrap {
        padding: 1.8rem;
        border: 1px solid #e7ebef;
        border-radius: 2rem;
        background: #fff;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.7);
    }
    .review-textarea-wrap textarea {
        width: 100%;
        min-height: 18rem;
        padding: 0;
        border: 0;
        outline: 0;
        resize: none;
        background: transparent;
        font-size: 1.6rem;
        line-height: 1.7;
        color: #1f2328;
    }
    .review-textarea-wrap textarea::placeholder {
        color: #a1abb6;
    }
    .review-photo-trigger {
        width: 100%;
        min-height: 11.2rem;
        padding: 2rem 1.6rem;
        border: 2px dashed var(--primary);
        border-radius: 2rem;
        background: var(--light);
        color: #1aa7ff;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: .8rem;
        text-align: center;
    }
    .review-photo-trigger svg {
        width: 3.6rem;
        height: 3.6rem;
    }
    .review-photo-trigger strong {
        font-size: 1.6rem;
        font-weight: 700;
        color: var(--text);
    }
    .review-photo-trigger span {
        font-size: 1.5rem;
        line-height: 1.5;
        color: var(--text);
    }
    .review-photo-preview {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1.2rem;
        margin-top: 1.6rem;
    }
    .review-photo-item {
        position: relative;
        aspect-ratio: 1 / 1;
        border-radius: 1.8rem;
        overflow: hidden;
        background: #f1f3f6;
    }
    .review-photo-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .review-photo-remove {
        position: absolute;
        top: .8rem;
        right: .8rem;
        width: 2.8rem;
        height: 2.8rem;
        padding: 0;
        border: 0;
        border-radius: 999px;
        background: rgba(17,24,39,.76);
        color: #fff;
        font-size: 1.8rem;
        line-height: 1;
    }
    .review-menu-list {
        display: flex;
        flex-direction: column;
        gap: 1.2rem;
    }
    .review-menu-item {
        position: relative;
        display: flex;
        align-items: center;
        gap: 1.4rem;
        padding: 1.4rem;
        border: 1px solid #edf1f4;
        border-radius: 2rem;
        background: #fff;
        transition: border-color .16s ease, box-shadow .16s ease, background .16s ease;
        cursor: pointer;
    }
    .review-menu-item.is-checked {
        border-color: #ffd5c8;
        background: #fff9f6;
        box-shadow: 0 1rem 2.4rem rgba(31,35,40,.05);
    }
    .review-menu-item input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }
    .review-menu-check {
        width: 2.4rem;
        height: 2.4rem;
        border: 1.5px solid #d5dde5;
        border-radius: 999px;
        background: #fff;
        color: transparent;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: all .16s ease;
    }
    .review-menu-check svg {
        width: 1.4rem;
        height: 1.4rem;
    }
    .review-menu-item.is-checked .review-menu-check {
        background: #ff5a1f;
        border-color: #ff5a1f;
        color: #fff;
        box-shadow: 0 .8rem 1.8rem rgba(255,90,31,.22);
    }
    .review-menu-thumb {
        width: 6.4rem;
        height: 6.4rem;
        border-radius: 1.8rem;
        overflow: hidden;
        background: #f3f5f7;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .review-menu-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .review-menu-thumb--empty {
        font-size: 1.1rem;
        font-weight: 700;
        letter-spacing: .08em;
        color: #97a1ad;
    }
    .review-menu-meta {
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: .4rem;
    }
    .review-menu-meta strong {
        font-size: 1.8rem;
        line-height: 1.4;
        font-weight: 700;
        color: #1f2328;
        word-break: keep-all;
    }
    .review-menu-meta span {
        font-size: 1.4rem;
        line-height: 1.5;
        color: #8692a0;
    }
    .review-empty {
        padding: 2rem 1.6rem;
        border: 1px dashed #dde3ea;
        border-radius: 1.8rem;
        background: #fafbfc;
        font-size: 1.4rem;
        line-height: 1.6;
        color: #97a1ad;
    }
    .review-bottom {
        padding: 1.4rem 1.8rem calc(1.4rem + env(safe-area-inset-bottom));
        background: linear-gradient(180deg, rgba(255,255,255,0) 0%, #fff 28%, #fff 100%);
    }
    .review-submit {
        width: 100%;
        height: 5.8rem;
        padding: 0 1.6rem;
        border: 0;
        border-radius: 1.8rem;
        background: #ff5a1f;
        color: #fff;
        font-size: 2rem;
        font-weight: 700;
        box-shadow: 0 1.6rem 2.8rem rgba(255,90,31,.24);
    }
    .review-submit:disabled {
        background: #d9e0e7;
        color: #8792a0;
        box-shadow: none;
    }
    @media (min-width: 768px) {
        .review-page {
            background: #f4f6f8;
        }
        .review-page .idx_pg {
            max-width: 76rem;
            margin: 0 auto;
            box-shadow: 0 2rem 5rem rgba(15,23,42,.08);
        }
    }
</style>
<?php include_once("../inc/modal.php");?>
<script>
    (function () {
        var reviewForm = document.getElementById('reviewForm');
        var foodScore = document.getElementById('foodScore');
        var ratingLabel = document.getElementById('ratingLabel');
        var stars = Array.prototype.slice.call(document.querySelectorAll('.review-star'));
        var reviewContents = document.getElementById('reviewContents');
        var reviewTextCount = document.getElementById('reviewTextCount');
        var submitButton = document.getElementById('btnSubmitReview');

        var photoInput = document.getElementById('reviewPhotos');
        var photoUploadTrigger = document.getElementById('photoUploadTrigger');
        var photoPreview = document.getElementById('photoPreview');
        var photoCounter = document.getElementById('photoCounter');
        var photoCounterInline = document.getElementById('photoCounterInline');
        var maxPhotoCount = 5;
        var photoStore = window.DataTransfer ? new DataTransfer() : null;

        var reviewApiUrl = reviewForm.getAttribute('action');
        var isSubmitting = false;

        var ratingTextMap = {
            1: '아쉬워요',
            2: '무난했어요',
            3: '괜찮았어요',
            4: '맛있었어요',
            5: '정말 만족했어요'
        };

        function getReviewText() {
            return reviewContents.value.trim();
        }

        function updateSubmitState() {
            var hasScore = Number(foodScore.value || 0) > 0;
            var hasReview = getReviewText().length >= 20;
            submitButton.disabled = !hasScore || !hasReview || isSubmitting;
        }

        function paintStars(value) {
            stars.forEach(function (star, index) {
                star.classList.remove('is-preview');
                star.classList.toggle('is-active', index < value);
            });

            ratingLabel.textContent = value > 0 ? ratingTextMap[value] : '별점을 선택해주세요';
            updateSubmitState();
        }

        stars.forEach(function (star) {
            star.addEventListener('mouseenter', function () {
                var value = Number(this.getAttribute('data-value') || 0);
                stars.forEach(function (item, index) {
                    item.classList.toggle('is-preview', index < value);
                });
            });

            star.addEventListener('click', function () {
                var value = Number(this.getAttribute('data-value') || 0);
                foodScore.value = value;
                paintStars(value);
            });
        });

        document.getElementById('reviewStars').addEventListener('mouseleave', function () {
            paintStars(Number(foodScore.value || 0));
        });

        reviewContents.addEventListener('input', function () {
            reviewTextCount.textContent = this.value.length;
            updateSubmitState();
        });

        function syncMenuCheckedState() {
            var checkboxes = document.querySelectorAll('.review-menu-item input[type="checkbox"]');
            checkboxes.forEach(function (checkbox) {
                checkbox.closest('.review-menu-item').classList.toggle('is-checked', checkbox.checked);
            });
        }

        document.addEventListener('change', function (e) {
            if (e.target.matches('.review-menu-item input[type="checkbox"]')) {
                syncMenuCheckedState();
            }
        });

        function renderPhotoPreview() {
            photoPreview.innerHTML = '';

            if (!photoStore || photoStore.files.length === 0) {
                photoCounter.textContent = '0';
                photoCounterInline.textContent = '0';
                return;
            }

            Array.prototype.slice.call(photoStore.files).forEach(function (file, index) {
                var item = document.createElement('div');
                item.className = 'review-photo-item';

                var img = document.createElement('img');
                img.alt = '리뷰 사진 ' + (index + 1);

                var removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'review-photo-remove';
                removeBtn.setAttribute('data-index', index);
                removeBtn.setAttribute('aria-label', '사진 삭제');
                removeBtn.textContent = '×';

                var reader = new FileReader();
                reader.onload = function (event) {
                    img.src = event.target.result;
                };
                reader.readAsDataURL(file);

                item.appendChild(img);
                item.appendChild(removeBtn);
                photoPreview.appendChild(item);
            });

            photoCounter.textContent = String(photoStore.files.length);
            photoCounterInline.textContent = String(photoStore.files.length);
        }

        function syncPhotoInput() {
            if (!photoStore) return;
            photoInput.files = photoStore.files;
            renderPhotoPreview();
        }

        if (photoUploadTrigger) {
            photoUploadTrigger.addEventListener('click', function () {
                photoInput.click();
            });
        }

        if (photoInput) {
            photoInput.addEventListener('change', function (e) {
                var incomingFiles = Array.prototype.slice.call(e.target.files || []);
                if (!incomingFiles.length) return;

                if (!photoStore) {
                    alert('현재 브라우저에서는 사진 미리보기 기능을 지원하지 않습니다.');
                    return;
                }

                var remain = maxPhotoCount - photoStore.files.length;
                if (remain <= 0) {
                    alert('사진은 최대 5장까지 등록할 수 있습니다.');
                    return;
                }

                if (incomingFiles.length > remain) {
                    alert('사진은 최대 5장까지 등록할 수 있습니다.');
                }

                incomingFiles.slice(0, remain).forEach(function (file) {
                    if (/^image\//.test(file.type)) {
                        photoStore.items.add(file);
                    }
                });

                syncPhotoInput();
                // photoInput.value = '';
            });
        }

        photoPreview.addEventListener('click', function (e) {
            var removeBtn = e.target.closest('.review-photo-remove');
            if (!removeBtn || !photoStore) return;

            var removeIndex = Number(removeBtn.getAttribute('data-index'));
            var nextStore = new DataTransfer();

            Array.prototype.slice.call(photoStore.files).forEach(function (file, index) {
                if (index !== removeIndex) {
                    nextStore.items.add(file);
                }
            });

            photoStore = nextStore;
            syncPhotoInput();
        });

        reviewForm.addEventListener('submit', function (e) {
            e.preventDefault();

            var score = Number(foodScore.value || 0);
            var reviewText = getReviewText();

            if (score < 1) {
                alert('음식 별점을 선택해주세요.');
                return;
            }

            if (reviewText.length < 20) {
                alert('리뷰는 20자 이상 입력해주세요.');
                reviewContents.focus();
                return;
            }

            if (photoStore && photoStore.files.length > maxPhotoCount) {
                alert('사진은 최대 5장까지 등록할 수 있습니다.');
                return;
            }

            if (isSubmitting) {
                return;
            }

            var formData = new FormData(reviewForm);
            formData.set('food_score', String(score));
            formData.set('review_contents', reviewText);

            formData.delete('review_images[]');

            if (photoStore && photoStore.files && photoStore.files.length) {
                Array.prototype.slice.call(photoStore.files).forEach(function (file) {
                    formData.append('review_images[]', file, file.name);
                });
            }

            isSubmitting = true;
            submitButton.textContent = '등록 중...';
            updateSubmitState();

            $.ajax({
                url: reviewApiUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (res) {
                    if (res && res.success) {
                        ModalUtil.alert({
                            title: '알림',
                            message: res.message || '결제 검증 실패',
                            okText: '확인',
                            onOk: function () {
                                location.href= res && res?.redirect_url;
                            },
                        });
                    }
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                    alert('서버 통신 중 오류가 발생했습니다.');
                },
                complete: function () {
                    isSubmitting = false;
                    submitButton.textContent = '등록하기';
                    updateSubmitState();
                }
            });
        });

        syncMenuCheckedState();
        paintStars(0);
        renderPhotoPreview();
        updateSubmitState();
    })();
</script>

