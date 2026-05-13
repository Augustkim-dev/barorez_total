<?
$_SUB_HEAD_TITLE = "문의 작성";
$_GET['hd_pc'] = ' ';
$hd_num = 'qa';
$hd_left = ' ';
include_once("../inc/head.php");
include_once("../inc/header.php");
include_once("../inc/modal.php");

include_once $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";

global $DB;

$idx = (int)($_GET['idx'] ?? 0);
$is_view_mode = ($idx > 0);

$qa_data = null;
$images = [];

if ($is_view_mode) {
    // 본인 글만 조회 가능하도록 mt_idx 조건 추가
    $mt_idx = (int)($_SESSION['mng']['mt_idx'] ?? 0);

    $qa_data = $DB->where('idx', $idx)
        ->where('mt_idx', $mt_idx)
        ->getOne('qa_t');

    if (!$qa_data) {
        // 존재하지 않거나 타인 글 → 오류 처리
        echo "<script>alert('문의글을 찾을 수 없거나 권한이 없습니다.'); history.back();</script>";
        exit;
    }

    // 이미지 URL 준비 (rs_ 붙여서)
    $publicBase = rtrim(DATA_URL, '/') . '/qa/' . $mt_idx;
    for ($i = 1; $i <= 5; $i++) {
        $col = 'rt_img' . $i;
        $filename = trim($qa_data[$col] ?? '');
        if ($filename !== '') {
            $images[] = [
                'url' => $publicBase . '/' . $filename,
                'idx' => $i
            ];
        }
    }
}

$readonly = $is_view_mode ? 'readonly' : '';
$disabled = $is_view_mode ? 'disabled' : '';
?>

<? include_once("../inc/left_menu.php"); ?>

    <div class="sub_pg">
        <div class="sub_wr">
            <div class="hd_tit2 flex-row">
                <div class="d-flex align-items-end flex-wrap">
                    <h3 class="tit_st1 mr-5"><?= $is_view_mode ? '문의글 보기' : '문의글 작성' ?></h3>
                </div>
            </div>

            <form id="qaForm" enctype="multipart/form-data" onsubmit="return false;">
                <section class="card">
                    <div class="card-body">
                        <div class="form_wr">
                            <div class="ip_tit required"><h5>제목</h5></div>
                            <input type="text" class="form-control" id="rt_title" name="rt_title"
                                   placeholder="문의 제목을 입력해 주세요" maxlength="100"
                                   value="<?= htmlspecialchars($qa_data['rt_title'] ?? '') ?>"
                                <?= $readonly ?> <?= $disabled ?>>
                            <div class="form-text ip_invalid" style="display:none;"></div>
                        </div>

                        <div class="form_wr mt-5">
                            <div class="ip_tit required"><h5>문의 내용</h5></div>
                            <textarea class="form-control" id="rt_description" name="rt_description"
                                      placeholder="문의하실 내용을 자세히 작성해 주세요" rows="8" maxlength="2000"
                                  <?= $readonly ?> <?= $disabled ?>><?= htmlspecialchars($qa_data['rt_description'] ?? '') ?></textarea>
                            <p class="text-right mt-2 tg_500 fs_14" id="desc_count">
                                (<?= mb_strlen($qa_data['rt_description'] ?? '', 'UTF-8') ?>/2000)
                            </p>
                            <div class="form-text ip_invalid" style="display:none;"></div>
                        </div>
                    </div>
                </section>

                <section class="card mt-4">
                    <div class="card-body">
                        <div class="form_wr">
                            <div class="ip_tit">
                                <h5>첨부 이미지<?= $is_view_mode ? '' : ' (최대 5장)' ?></h5>
                                <?php if (!$is_view_mode): ?>
                                    <small class="tg_500">jpg, png, gif / 각 10MB 이하</small>
                                <?php endif; ?>
                            </div>

                            <div class="d-flex flex-wrap gap-3" id="qa_imgs_wrap">
                                <?php if ($is_view_mode): ?>
                                    <?php if (empty($images)): ?>
                                        <div class="text-muted py-3">첨부된 이미지가 없습니다.</div>
                                    <?php else: ?>
                                        <?php foreach ($images as $img): ?>
                                            <div class="image_upload on">
                                                <div class="upload_box">
                                                    <div class="rect">
                                                        <img src="<?= htmlspecialchars($img['url']) ?>"
                                                             style="width:100%;height:100%;object-fit:cover;"
                                                             alt="첨부 이미지 <?= $img['idx'] ?>">
                                                    </div>
                                                    <p class="max_img">사진 <?= $img['idx'] ?></p>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>

                            <?php if (!$is_view_mode): ?>
                                <!-- 신규 작성 시에만 파일 input 노출 -->
                                <input id="qa_img1" name="qa_img1" type="file" class="d-none" accept="image/*">
                                <input id="qa_img2" name="qa_img2" type="file" class="d-none" accept="image/*">
                                <input id="qa_img3" name="qa_img3" type="file" class="d-none" accept="image/*">
                                <input id="qa_img4" name="qa_img4" type="file" class="d-none" accept="image/*">
                                <input id="qa_img5" name="qa_img5" type="file" class="d-none" accept="image/*">

                                <input type="hidden" name="del_img1" value="N">
                                <input type="hidden" name="del_img2" value="N">
                                <input type="hidden" name="del_img3" value="N">
                                <input type="hidden" name="del_img4" value="N">
                                <input type="hidden" name="del_img5" value="N">
                            <?php endif; ?>
                        </div>
                    </div>
                </section>

                <?php if ($is_view_mode && trim($qa_data['rt_response_text'] ?? '') !== ''): ?>
                    <section class="card mt-4">
                        <div class="card-body">
                            <div class="form_wr mt-5">
                                <div class="ip_tit"><h5>문의 답변</h5></div>
                                <textarea class="form-control" id="rt_description" name="rt_description"
                                          placeholder="문의하실 내용을 자세히 작성해 주세요" rows="8" maxlength="2000"
                                  <?= $readonly ?> <?= $disabled ?>><?= htmlspecialchars($qa_data['rt_response_text'] ?? '') ?></textarea>
                                <p class="text-right mt-2 tg_500 fs_14">
                                    답변일시: <?= $qa_data['updated_at'] ? date('Y.m.d H:i', strtotime($qa_data['updated_at'])) : '-' ?>
                                </p>
                            </div>
                        </div>
                    </section>
                <?php endif; ?>

                <div class="d-flex justify-content-center mt_40 btn_group">
                    <?php if (!$is_view_mode): ?>
                        <button type="button" class="btn btn-outline-light btn-lg btn-w2" id="cancel_btn">취소</button>
                        <button type="button" class="btn btn-primary btn-lg btn-w2" id="submit_btn">문의 등록</button>
                    <?php else: ?>
                        <button type="button" class="btn btn-outline-secondary btn-lg btn-w3" onclick="history.back()">목록으로</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

<?php if (!$is_view_mode): ?>
    <!-- 신규 작성 모드에서만 동작하는 스크립트 -->
    <script>
        (function(){
            'use strict';
            if (!window.jQuery) return;
            const $ = window.jQuery;

            const API_URL = './update.php';
            const MAX_IMGS = 5;

            // FormState 생략 (기존 그대로 사용 가능)

            const $form = $('#qaForm');
            const $rt_title = $('#rt_title');
            const $rt_desc = $('#rt_description');
            const $desc_count = $('#desc_count');
            const $imgWrap = $('#qa_imgs_wrap');

            let slots = new Array(MAX_IMGS).fill(null);
            let activePickIndex = -1;
            let $imgPicker = null;

            const trim = v => (v ?? '').trim();

            function ensurePicker() {
                if ($imgPicker) return;
                $imgPicker = $('<input type="file" accept="image/*" class="d-none">')
                    .appendTo($form)
                    .on('change', function(){
                        const file = this.files?.[0];
                        if (!file) return;
                        if (activePickIndex < 0 || activePickIndex >= MAX_IMGS) return;

                        slots[activePickIndex] = {kind:'file', file};
                        compressSlots();
                        renderSlots();
                        this.value = '';
                        activePickIndex = -1;
                    });
            }

            function compressSlots() {
                slots = slots.filter(Boolean).concat(new Array(MAX_IMGS).fill(null));
            }

            function slotPreview(item) {
                if (!item) return '';
                if (item.kind === 'file') return URL.createObjectURL(item.file);
                return '';
            }

            function renderSlots() {
                ensurePicker();
                const filled = slots.filter(Boolean).length;
                const showCount = Math.min(filled + 1, MAX_IMGS);

                let html = '';
                for (let i = 0; i < showCount; i++) {
                    const item = slots[i];
                    const url = slotPreview(item);
                    const num = i + 1;

                    html += `
            <div class="image_upload ${url ? 'on' : ''}" data-idx="${i}">
                <label class="upload_box js-pick" style="cursor:pointer;">
                    <div class="rect">
                        ${url ? `<img src="${url}" style="width:100%;height:100%;object-fit:cover;">` : ''}
                    </div>
                    <p class="max_img">사진 ${num}/${MAX_IMGS}</p>
                </label>
                <button type="button" class="btn upload_del js-del" data-idx="${i}" ${url ? '' : 'style="display:none"'}>
                    <img src="<?=DESIGN_HTTP?>/market/img/img_del.png" alt="삭제">
                </button>
            </div>`;
                }
                $imgWrap.html(html);
            }

            // 이벤트 바인딩 (신규 작성 시에만)
            $(document).on('click', '.js-pick', function(e){
                e.preventDefault();
                activePickIndex = Number($(this).closest('.image_upload').data('idx'));
                $imgPicker.trigger('click');
            });

            $(document).on('click', '.js-del', function(e){
                e.preventDefault();
                e.stopPropagation();
                const idx = Number($(this).data('idx'));
                slots[idx] = null;
                compressSlots();
                renderSlots();
            });

            $rt_desc.on('input', function(){
                let len = this.value.length;
                if (len > 2000) {
                    this.value = this.value.slice(0,2000);
                    len = 2000;
                }
                $desc_count.text(`(${len}/2000)`);
            });

            $('#submit_btn').on('click', function(){
                if (!$rt_title.val().trim()) {
                    alert('제목을 입력해 주세요.');
                    $rt_title.focus();
                    return;
                }
                if (!$rt_desc.val().trim()) {
                    alert('문의 내용을 입력해 주세요.');
                    $rt_desc.focus();
                    return;
                }

                const fd = new FormData();
                fd.append('act', 'qa_write');
                fd.append('rt_title', $rt_title.val().trim());
                fd.append('rt_description', $rt_desc.val().trim());

                slots.forEach((item, i) => {
                    if (item?.kind === 'file') {
                        fd.append(`qa_img${i+1}`, item.file);
                    }
                });

                $.ajax({
                    url: API_URL,
                    type: 'POST',
                    data: fd,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success(res) {
                        if (res?.success) {
                            alert(res.message || '문의가 등록되었습니다.');
                            location.href = '../qa';  // 목록 페이지 경로
                        } else {
                            alert(res?.message || '등록 실패');
                        }
                    },
                    error() {
                        alert('서버 통신 오류');
                    }
                });
            });

            $('#cancel_btn').on('click', () => {
                if (confirm('작성 중인 내용이 사라집니다.\n정말 취소하시겠습니까?')) {
                    history.back();
                }
            });

            // 초기화
            renderSlots();
            $rt_title.focus();
        })();
    </script>
<?php endif; // 신규 작성 모드 스크립트 끝 ?>

<? include_once("./inc/tail.php"); ?>
