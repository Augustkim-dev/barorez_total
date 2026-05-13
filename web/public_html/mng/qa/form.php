<?php
// /market/qa/form.php : 1:1 문의 상세 + 답변 작성

include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu     = 7; // 메뉴 번호는 프로젝트에 맞게 조정
$chk_sub_menu = 1;
include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.menu.inc.php";

global $DB;

$idx = (int)($_GET['nt_idx'] ?? 0);
if ($idx <= 0) {
    echo "<script>alert('잘못된 접근입니다.');history.back();</script>";
    exit;
}

$DB->where('idx', $idx);
$row = $DB->getOne('qa_t');

if (!$row || $row['rt_show'] === 'N') {
    echo "<script>alert('해당 문의를 찾을 수 없습니다.');history.back();</script>";
    exit;
}

// 업로드 경로 (마지막 슬래시는 붙이지 않고 아래에서 / 추가)
$qa_upload_url = '/data/qa';

function h($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<div class="container-fluid py-4">

    <!-- 헤더 -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">1:1 문의 상세</h1>
            <p class="text-muted mb-0">문의 내용을 확인하고 답변을 작성할 수 있습니다.</p>
        </div>
    </div>

    <!-- 문의 정보 -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0">
            <h5 class="mb-0">문의 내용</h5>
        </div>
        <div class="card-body">

            <div class="row mb-3">
                <div class="col-md-8">
                    <div class="fw-bold">제목</div>
                    <div><?= h($row['rt_title']) ?></div>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <div class="fw-bold">문의일시</div>
                    <div>
                        <?= $row['created_at'] ? date('Y.m.d (D) H:i', strtotime($row['created_at'])) : '' ?>
                    </div>
                </div>
            </div>

            <hr>

            <div class="mb-3">
                <div class="fw-bold mb-2">문의내용</div>
                <div style="white-space:pre-line;"><?= h($row['rt_description']) ?></div>
            </div>

            <?php
            $images = [];
            for ($i=1; $i<=5; $i++) {
                $field = 'rt_img'.$i;
                if (!empty($row[$field])) {
                    $images[] = $row[$field];
                }
            }
            if (!empty($images)): ?>
                <div class="mt-3">
                    <div class="fw-bold mb-2">첨부 이미지</div>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($images as $img): ?>
                            <?php
                            // 원본 파일명
                            $orig = h($img);
                            // 썸네일은 rs_ 접두어 사용 (리사이즈된 파일)
                            $thumb = 'rs_' . $orig;
                            ?>
                            <a href="<?= $qa_upload_url . '/' . $row['mt_idx'] . '/' . $orig ?>" target="_blank">
                                <img src="<?= $qa_upload_url . '/' . $row['mt_idx'] . '/' . $orig ?>"
                                     alt=""
                                     class="img-thumbnail"
                                     style="width:120px; height:120px; object-fit:contain;">
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <!-- 관리자 답변 -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">답변</h5>
            <?php if (!empty($row['rt_name'])): ?>
                <span class="small text-muted">
                    답변자: <?= h($row['rt_name']) ?>
                    <?php if (!empty($row['updated_at'])): ?>
                        (<?= date('Y.m.d H:i', strtotime($row['updated_at'])) ?>)
                    <?php endif; ?>
                </span>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <form id="qaAnswerForm">
                <input type="hidden" name="idx" value="<?= (int)$row['idx'] ?>">

                <div class="mb-3">
                    <textarea name="rt_response_text"
                              class="form-control"
                              rows="8"
                              placeholder="답변 내용을 입력해주세요."><?= h($row['rt_response_text']) ?></textarea>
                </div>

                <div class="d-flex justify-content-between">
                    <div class="small text-muted align-self-center">
                        * 답변을 비워두면 상태는 ‘미답변’으로 유지됩니다.
                    </div>
                    <div>
                        <button type="button"
                                class="btn btn-outline-secondary me-2"
                                onclick="history.back();">목록</button>
                        <button type="submit"
                                class="btn btn-secondary">저장</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    // jQuery 사용해서 submit → AJAX 처리
    $(function () {
        $('#qaAnswerForm').on('submit', function (e) {
            e.preventDefault();
            saveQaAnswer();
        });
    });

    function saveQaAnswer() {
        const formEl = document.getElementById('qaAnswerForm');
        const fd     = new FormData(formEl);
        fd.append('act', 'answer');

        $.ajax({
            url: './update.php',
            type: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (res) {
                if (!res || !res.success) {
                    alert(res && res.message ? res.message : '저장 중 오류가 발생했습니다.');
                    return;
                }
                alert(res.message || '저장되었습니다.');
                // 목록으로 이동
                location.href = './list.php';
            },
            error: function (xhr, status, error) {
                console.error(error);
                alert('저장 중 네트워크 오류가 발생했습니다.');
            }
        });
    }
</script>

<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/foot.inc.php";
?>
