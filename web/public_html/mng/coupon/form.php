<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu     = 9;
$chk_sub_menu = 1;
include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.menu.inc.php";

$row = [];
if (isset($_GET['act']) && $_GET['act'] === 'update') {
    $ct_idx = (int)($_GET['ct_idx'] ?? 0);
    if ($ct_idx > 0) {
        $DB->where('idx', $ct_idx);
        $row = $DB->getOne('coupon_t', '*, idx AS ct_idx');
    }
    $_act     = 'update';
    $_act_txt = ' 수정';
} else {
    $_act     = 'input';
    $_act_txt = ' 등록';
}

$ct_type1 = isset($row['ct_type1']) ? (string)$row['ct_type1'] : '1';

$ct_target_scope   = $row['ct_target_scope'] ?? 'ALL';
$ct_target_members = $row['ct_target_members'] ?? '';

// 선택된 회원 상세 정보 (이름/아이디 표시용)
$selectedMembers = [];

if ($ct_target_scope === 'MEMBER' && $ct_target_members !== '') {
    // "1,5,7" -> [1,5,7]
    $ids = array_filter(array_map('intval', explode(',', $ct_target_members)));
    if ($ids) {
        $DB->where('idx', $ids, 'IN');
        // 필요 컬럼만
        $selectedMembers = $DB->get('member_t', null, 'idx, mt_name, mt_id');
    }
}

?>
    <div class="content" id="content">
        <div class="page-heading">
            <div class="page-heading__container">
                <div class="icon">
                    <span class="li-picture3"></span>
                </div>
                <h1 class="title">쿠폰관리</h1>
                <p class="caption">쿠폰 등록 및 수정을 할 수 있습니다.</p>
            </div>
            <nav aria-label="breadcrumb" role="navigation">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#">쿠폰관리</a></li>
                    <li class="breadcrumb-item active">쿠폰<?=$_act_txt?></li>
                </ol>
            </nav>
        </div>

        <div class="container-fluid">
            <div class="card margin-bottom-0">
                <div class="card-body">
                    <form method="post"
                          name="frm_form"
                          id="frm_form"
                          action="./update.php"
                          enctype="multipart/form-data">
                        <input type="hidden" name="act" id="act" value="<?=$_act?>">
                        <input type="hidden" name="ct_idx" id="ct_idx" value="<?=$row['ct_idx'] ?? ''?>">

                        <h4 class="margin-bottom-20">쿠폰<?=$_act_txt?></h4>

                        <!-- 쿠폰명 -->
                        <div class="form-group row">
                            <label for="ct_title" class="col-sm-2 col-form-label">
                                쿠폰명 <span class="text-danger">*</span>
                            </label>
                            <div class="col-sm-10">
                                <input type="text"
                                       name="ct_title"
                                       id="ct_title"
                                       value="<?=htmlspecialchars($row['ct_title'] ?? '', ENT_QUOTES)?>"
                                       class="form-control"
                                       required>
                            </div>
                        </div>

                        <?php if ($_act === 'update') : ?>
                            <!-- 쿠폰코드 (수정 시에만 표시, 읽기 전용) -->
                            <div class="form-group row">
                                <label for="ct_code" class="col-sm-2 col-form-label">쿠폰코드</label>
                                <div class="col-sm-10">
                                    <input type="text"
                                           name="ct_code"
                                           id="ct_code"
                                           value="<?=htmlspecialchars($row['ct_code'] ?? '', ENT_QUOTES)?>"
                                           class="form-control"
                                           readonly>
                                    <small class="form-text text-muted">
                                        쿠폰 등록 시 자동으로 생성된 코드입니다.
                                    </small>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- 사용 여부 -->
                        <div class="form-group row">
                            <label for="ct_show" class="col-sm-2 col-form-label">사용 여부</label>
                            <div class="col-sm-10">
                                <select name="ct_show" id="ct_show" class="form-control">
                                    <option value="Y" <?=($row['ct_show'] ?? 'Y') === 'Y' ? 'selected' : ''?>>사용</option>
                                    <option value="N" <?=($row['ct_show'] ?? 'Y') === 'N' ? 'selected' : ''?>>미사용</option>
                                </select>
                            </div>
                        </div>

                        <!-- 유효기간 설정 -->
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">
                                유효기간 설정 <span class="text-danger">*</span>
                            </label>
                            <div class="col-sm-10">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="ct_type1_1" name="ct_type1" value="1"
                                           class="custom-control-input" <?=$ct_type1 === '1' ? 'checked' : ''?>>
                                    <label class="custom-control-label" for="ct_type1_1">기간 설정</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="ct_type1_2" name="ct_type1" value="2"
                                           class="custom-control-input" <?=$ct_type1 === '2' ? 'checked' : ''?>>
                                    <label class="custom-control-label" for="ct_type1_2">발급일 기준</label>
                                </div>

                                <!-- 기간 설정 -->
                                <div id="wrap_date_range" class="mt-2"
                                     style="<?=$ct_type1 === '2' ? 'display:none' : ''?>">
                                    <div class="input-group">
                                        <input type="date" name="ct_sdate" id="ct_sdate"
                                               value="<?=htmlspecialchars($row['ct_sdate'] ?? '', ENT_QUOTES)?>"
                                               class="form-control">
                                        <div class="input-group-prepend input-group-append">
                                            <span class="input-group-text">~</span>
                                        </div>
                                        <input type="date" name="ct_edate" id="ct_edate"
                                               value="<?=htmlspecialchars($row['ct_edate'] ?? '', ENT_QUOTES)?>"
                                               class="form-control">
                                    </div>
                                </div>

                                <!-- 발급일 기준 N일 -->
                                <div id="wrap_date_days" class="mt-2"
                                     style="<?=$ct_type1 === '2' ? '' : 'display:none'?>">
                                    <div class="input-group">
                                        <input type="number" name="ct_days" id="ct_days"
                                               value="<?=htmlspecialchars($row['ct_days'] ?? '', ENT_QUOTES)?>"
                                               min="1"
                                               class="form-control" placeholder="유효일수(일)">
                                        <div class="input-group-append">
                                            <span class="input-group-text">일</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 할인 금액 (정액만) -->
                        <input type="hidden" name="ct_type2" value="1">
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">
                                할인 금액 <span class="text-danger">*</span>
                            </label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <input type="number"
                                           name="ct_discount1"
                                           id="ct_discount1"
                                           min="0"
                                           value="<?=htmlspecialchars($row['ct_discount1'] ?? '', ENT_QUOTES)?>"
                                           class="form-control" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">원</span>
                                    </div>
                                </div>
                                <small class="form-text text-muted">정액(원) 할인만 사용합니다.</small>
                            </div>
                        </div>

                        <!-- 최소 주문금액 -->
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">최소 주문금액</label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <input type="number"
                                           name="ct_discount3"
                                           id="ct_discount3"
                                           min="0"
                                           value="<?=htmlspecialchars($row['ct_discount3'] ?? '', ENT_QUOTES)?>"
                                           class="form-control"
                                           placeholder="제한 없음">
                                    <div class="input-group-append">
                                        <span class="input-group-text">원</span>
                                    </div>
                                </div>
                                <small class="form-text text-muted">
                                    설정 시 해당 금액 이상 주문 시에만 쿠폰 사용 가능
                                </small>
                            </div>
                        </div>

                        <!-- 발급 대상 -->
                        <h4 class="margin-top-30">발급 대상 설정</h4>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">발급 대상</label>
                            <div class="col-sm-10">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio"
                                           id="scope_all"
                                           name="ct_target_scope"
                                           value="ALL"
                                           class="custom-control-input"
                                        <?=$ct_target_scope === 'ALL' ? 'checked' : ''?>>
                                    <label class="custom-control-label" for="scope_all">전체 회원</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio"
                                           id="scope_member"
                                           name="ct_target_scope"
                                           value="MEMBER"
                                           class="custom-control-input"
                                        <?=$ct_target_scope === 'MEMBER' ? 'checked' : ''?>>
                                    <label class="custom-control-label" for="scope_member">특정 회원</label>
                                </div>

                                <div id="wrap_member_select"
                                     class="mt-3"
                                     style="<?=$ct_target_scope === 'MEMBER' ? '' : 'display:none'?>">
                                    <input type="hidden"
                                           name="ct_target_members"
                                           id="ct_target_members"
                                           value="<?=htmlspecialchars($ct_target_members, ENT_QUOTES)?>">

                                    <button type="button"
                                            class="btn btn-outline-primary btn-sm mb-2"
                                            id="btn_open_member_modal">
                                        회원 지정
                                    </button>

                                    <div id="selected_members_box" class="border rounded p-2" style="min-height:40px;">
                                        <small class="text-muted" id="selected_members_empty"
                                            <?=(!empty($selectedMembers) ? 'style="display:none"' : '')?>>
                                            선택된 회원이 없습니다.
                                        </small>
                                        <ul id="selected_members_list" class="list-unstyled mb-0">
                                            <?php if (!empty($selectedMembers)): ?>
                                                <?php foreach ($selectedMembers as $m): ?>
                                                    <li>
                                                        <?=htmlspecialchars($m['mt_name'], ENT_QUOTES)?>
                                                        (<?=htmlspecialchars($m['mt_id'], ENT_QUOTES)?>, <?=$m['idx']?>)
                                                    </li>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 관리자 메모 -->
                        <div class="form-group row">
                            <label for="ct_memo" class="col-sm-2 col-form-label">관리자 메모</label>
                            <div class="col-sm-10">
                            <textarea name="ct_memo" id="ct_memo" rows="3" class="form-control"><?=htmlspecialchars($row['ct_memo'] ?? '', ENT_QUOTES)?></textarea>
                            </div>
                        </div>

                        <?php if ($_act === 'update') { ?>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">등록일시</label>
                                <div class="col-sm-10">
                                    <?=!empty($row['ct_wdate']) ? DateType($row['ct_wdate'], 6) : '-'?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">수정일시</label>
                                <div class="col-sm-10">
                                    <?=!empty($row['ct_udate']) ? DateType($row['ct_udate'], 6) : '-'?>
                                </div>
                            </div>
                        <?php } ?>

                        <div class="form-group row justify-content-center margin-top-30">
                            <button type="button" onclick="location.href='./list.php';"
                                    class="btn btn-outline-secondary mx-1">목록</button>
                            <button type="submit" class="btn btn-secondary mx-1">확인</button>
                        </div>
                    </form>

                    <!-- 회원 선택 모달 (껍데기만, 실제 구현은 프로젝트 맞게) -->
                    <div class="modal fade" id="memberSelectModal" tabindex="-1" role="dialog"
                         aria-labelledby="memberSelectModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="memberSelectModalLabel">회원 지정</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="닫기">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <p class="text-muted mb-2">
                                        ※ 이 영역에 회원 검색/선택 UI를 구현하세요.
                                    </p>
                                    <div id="member_list_container">
                                        <!-- TODO: 회원 리스트/검색 폼 구현 -->
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button"
                                            class="btn btn-secondary"
                                            data-dismiss="modal">취소</button>
                                    <button type="button"
                                            class="btn btn-primary"
                                            id="btn_apply_member_select">선택 적용</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script>
                        $(function () {
                            // 유효기간 토글
                            $('input[name="ct_type1"]').on('change', function () {
                                if ($(this).val() === '1') {
                                    $('#wrap_date_range').show();
                                    $('#wrap_date_days').hide();
                                } else {
                                    $('#wrap_date_range').hide();
                                    $('#wrap_date_days').show();
                                }
                            });

                            // 발급 대상 토글
                            $('input[name="ct_target_scope"]').on('change', function () {
                                if ($(this).val() === 'MEMBER') {
                                    $('#wrap_member_select').show();
                                } else {
                                    $('#wrap_member_select').hide();
                                }
                            });

                            // 회원지정 모달 오픈
                            $('#btn_open_member_modal').on('click', function () {
                                $('#memberSelectModal').modal('show');

                                // 모달 열릴 때마다 리스트 불러오기
                                $.ajax({
                                    url: './update.php',
                                    type: 'POST',
                                    data: { act: 'member_list' },
                                    success: function (html) {
                                        $('#member_list_container').html(html);

                                        // 이미 저장된 회원들이 있으면 체크 표시
                                        const selectedIds = ($('#ct_target_members').val() || '')
                                            .split(',')
                                            .map(function (v) { return v.trim(); })
                                            .filter(function (v) { return v !== ''; });

                                        selectedIds.forEach(function (id) {
                                            $('#member_list_container')
                                                .find('.member-check[data-mt-idx="' + id + '"]')
                                                .prop('checked', true);
                                        });
                                    },
                                    error: function (xhr) {
                                        alert('회원 목록을 불러오는 중 오류가 발생했습니다. [' + xhr.status + ']');
                                    }
                                });
                            });

                            // 회원 선택 적용 예시 (실 데이터는 직접 채우면 됨)
                            $('#btn_apply_member_select').on('click', function () {
                                const selectedMembers = [];

                                $('#member_list_container').find('.member-check:checked').each(function () {
                                    const mt_idx = $(this).data('mt-idx');
                                    const name   = $(this).data('mt-name');
                                    const id     = $(this).data('mt-id');

                                    selectedMembers.push({ mt_idx, name, id });
                                });

                                if (selectedMembers.length === 0) {
                                    alert('선택된 회원이 없습니다.');
                                    return;
                                }

                                // hidden 필드에 mt_idx CSV 저장
                                const ids = selectedMembers.map(function (m) { return m.mt_idx; });
                                $('#ct_target_members').val(ids.join(','));

                                // 선택된 회원 목록 UI 갱신
                                const $list = $('#selected_members_list');
                                $list.empty();

                                $('#selected_members_empty').toggle(selectedMembers.length === 0);

                                selectedMembers.forEach(function (m) {
                                    $list.append(
                                        '<li>' +
                                        m.name + ' (' + m.id + ', ' + m.mt_idx + ')' +
                                        '</li>'
                                    );
                                });

                                $('#memberSelectModal').modal('hide');
                            });

                            // 폼 submit -> ajax
                            $('#frm_form').on('submit', function (e) {
                                e.preventDefault();

                                // 간단 검증
                                if (!$('#ct_title').val().trim()) {
                                    alert('쿠폰명을 입력해주세요.');
                                    $('#ct_title').focus();
                                    return false;
                                }

                                const type1 = $('input[name="ct_type1"]:checked').val();
                                if (type1 === '1') {
                                    if (!$('#ct_sdate').val()) {
                                        alert('유효기간 시작일을 입력해주세요.');
                                        $('#ct_sdate').focus();
                                        return false;
                                    }
                                    if (!$('#ct_edate').val()) {
                                        alert('유효기간 종료일을 입력해주세요.');
                                        $('#ct_edate').focus();
                                        return false;
                                    }
                                    if ($('#ct_sdate').val() > $('#ct_edate').val()) {
                                        alert('종료일은 시작일 이후여야 합니다.');
                                        $('#ct_edate').focus();
                                        return false;
                                    }
                                } else {
                                    const days = parseInt($('#ct_days').val(), 10);
                                    if (!days || days <= 0) {
                                        alert('유효일수를 1일 이상 입력해주세요.');
                                        $('#ct_days').focus();
                                        return false;
                                    }
                                }

                                const discount = parseInt($('#ct_discount1').val(), 10);
                                if (!discount || discount <= 0) {
                                    alert('할인 금액은 1원 이상 입력해주세요.');
                                    $('#ct_discount1').focus();
                                    return false;
                                }

                                if ($('input[name="ct_target_scope"]:checked').val() === 'MEMBER') {
                                    if (!$('#ct_target_members').val().trim()) {
                                        alert('특정 회원 발급을 선택하셨습니다. 회원을 지정해주세요.');
                                        return false;
                                    }
                                }

                                const form = this;
                                const fd = new FormData(form);

                                $.ajax({
                                    url: $(form).attr('action'),
                                    type: 'POST',
                                    data: fd,
                                    processData: false,
                                    contentType: false,
                                    dataType: 'json',
                                    success: function (res) {
                                        console.log('res', res);
                                        if (!res) {
                                            alert('서버 응답이 올바르지 않습니다.');
                                            return;
                                        }
                                        if (res.success) {
                                            alert(res.message || '저장되었습니다.');
                                            if (res.redirect) {
                                                location.href = res.redirect;
                                            }
                                        } else {
                                            alert(res.message || '처리 중 오류가 발생했습니다.');
                                        }
                                    },
                                    error: function (xhr) {
                                        alert('서버 오류가 발생했습니다. [' + xhr.status + ']');
                                        console.log(xhr.responseText);
                                    }
                                });

                                return false;
                            });
                        });
                    </script>

                </div>
            </div>
        </div>
    </div>
<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/foot.inc.php";
?>
