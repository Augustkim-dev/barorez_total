<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";

// 리스트와 동일하게 type 에 따른 서브메뉴 매핑
$menu_map = [
    ''          => 1, // 가맹점주 회원
    'approval'  => 2, // 승인관리
    'secession' => 3, // 탈퇴관리
];

$type = $_GET['type'] ?? '';
$chk_menu = 2;
$chk_sub_menu = $menu_map[$type] ?? 1;

include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.menu.inc.php";

$mt_idx = (int)($_GET['mt_idx'] ?? 0);
if ($mt_idx <= 0) {
    echo "<script>alert('잘못된 접근입니다.'); history.back();</script>";
    exit;
}

// 가맹점주 회원 정보 로드
$DB->where('idx', $mt_idx);
$row = $DB->getOne('member_t', '*, idx as mt_idx');
if (!$row) {
    echo "<script>alert('회원 정보를 찾을 수 없습니다.'); history.back();</script>";
    exit;
}

$_act = "update";
$_act_txt = "수정";

// 매장(사업자/정산) 정보 로드
$shop_img_url = '/data/shop';   // 필요 시 CDN 경로로 변경

$DB->where('mb_idx', $row['mt_idx']);
$DB->where('del_date', null, 'IS');
$shop_rows = $DB->get('shop_t', null, '
    idx,
    sh_title,
    sh_corp_nm,
    sh_biz_no,
    sh_ceo_nm,
    sh_branch_nm,
    sh_zip,
    sh_addr1,
    sh_addr2,
    sh_lat,
    sh_lng,
    sh_img1,
    sh_img2,
    sh_img3,
    sh_biz_file,
    sh_tel,
    sh_contents,
    sh_bank,
    sh_bank_holder,
    sh_bank_account,
    sh_bankbook
');

$shop_id = $shop_rows['idx'];

$stores = [];
foreach ($shop_rows as $srow) {
    $store_name = trim((string)($srow['sh_corp_nm'] ?? ''));
    if ($store_name === '') {
        $store_name = $srow['sh_title'] ?? '';
    }

    $stores[] = [
        'store_idx'      => (int)$srow['idx'],
        'store_name'     => $store_name,
        'biz_no'         => $srow['sh_biz_no']        ?? '',
        'shop_name'      => $srow['sh_title']         ?? '',
        'branch_name'    => $srow['sh_branch_nm']     ?? '',
        'owner_name'     => $srow['sh_ceo_nm']        ?? '',
        'zip'            => $srow['sh_zip']           ?? '',
        'addr1'          => $srow['sh_addr1']         ?? '',
        'addr2'          => $srow['sh_addr2']         ?? '',
        'lat'            => $srow['sh_lat']           ?? '',
        'lng'            => $srow['sh_lng']           ?? '',
        'img1'           => $srow['sh_img1']          ?? '',
        'img2'           => $srow['sh_img2']          ?? '',
        'img3'           => $srow['sh_img3']          ?? '',
        'biz_file'       => $srow['sh_biz_file']      ?? '',
        'sh_tel'         => $srow['sh_tel']      ?? '',
        'sh_contents'         => $srow['sh_contents']      ?? '',
        'settle_bank'    => $srow['sh_bank']          ?? '',
        'settle_holder'  => $srow['sh_bank_holder']   ?? '',
        'settle_account' => $srow['sh_bank_account']  ?? '',
        'bankbook'       => $srow['sh_bankbook']      ?? '',
    ];
}

?>
    <style>
        /* 매장 이미지/통장사본 썸네일 */
        .thumb-img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 0.5rem;
            border: 1px solid #dee2e6;
            margin-right: 6px;
            margin-bottom: 6px;
            background-color: #f8f9fa;
        }
        .readonly-box {
            background-color: #f8f9fa;
        }
    </style>

    <!-- PAGE CONTENT CONTAINER -->
    <div class="content" id="content">
        <!-- PAGE HEADING -->
        <div class="page-heading">
            <div class="page-heading__container">
                <div class="icon">
                    <span class="li-users2"></span>
                </div>
                <h1 class="title">가맹점주 상세 / 수정</h1>
                <p class="caption">
                    가맹점주 기본 정보(비밀번호, 이름, 휴대폰번호)만 수정 가능하며, 매장 및 정산 정보는 조회만 가능합니다.
                </p>
            </div>
        </div>
        <!-- //END PAGE HEADING -->

        <div class="container-fluid">
            <div class="card margin-bottom-0">
                <div class="card-body">
                    <form method="post"
                          name="frm_seller"
                          id="frm_seller"
                          action="./update.php"
                          enctype="multipart/form-data">

                        <input type="hidden" name="act" id="act" value="<?=$_act?>" />
                        <input type="hidden" name="mt_idx" id="mt_idx" value="<?=$row['mt_idx']?>" />
                        <input type="hidden" name="type" value="<?=htmlspecialchars($type, ENT_QUOTES)?>" />
                        <!-- mt_level 유지 (관리자가 변경 불가) -->
                        <input type="hidden" name="mt_level" value="<?=htmlspecialchars($row['mt_level'])?>">

                        <!-- ========================== -->
                        <!-- 1. 가맹점주 기본 정보 -->
                        <!-- ========================== -->
                        <h4 class="mb-3">기본 정보</h4>
                        <div class="border rounded p-3 mb-4">
                            <div class="form-group row align-items-center">
                                <label for="mt_id" class="col-sm-2 col-form-label">아이디</label>
                                <div class="col-sm-4">
                                    <input type="text"
                                           name="mt_id"
                                           id="mt_id"
                                           class="form-control"
                                           value="<?=htmlspecialchars($row['mt_id'])?>"
                                           readonly>
                                </div>
                                <label for="mt_id" class="col-sm-2 col-form-label">승인여부</label>
                                <div class="col-sm-4">
                                    <select name="mt_appr" class="form-control">
                                        <option value="N" <?=$row['mt_appr'] === 'N' ? 'selected' : ''?>>미승인</option>
                                        <option value="Y" <?=$row['mt_appr'] === 'Y' ? 'selected' : ''?>>승인</option>
                                        <option value="T" <?=$row['mt_appr'] === 'T' ? 'selected' : ''?>>임시</option>
                                        <option value="D" <?=$row['mt_appr'] === 'D' ? 'selected' : ''?>>거절</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row align-items-center">
                                <label for="mt_pwd" class="col-sm-2 col-form-label">비밀번호</label>
                                <div class="col-sm-4">
                                    <input type="password"
                                           name="mt_pwd"
                                           id="mt_pwd"
                                           class="form-control"
                                           minlength="8"
                                           maxlength="50"
                                           placeholder="변경 시에만 입력 (8자 이상)">
                                    <small class="form-text text-muted">
                                        비밀번호 변경 시에만 입력해주세요.
                                    </small>
                                </div>

                                <label for="mt_pwd_re" class="col-sm-2 col-form-label">비밀번호 확인</label>
                                <div class="col-sm-4">
                                    <input type="password"
                                           name="mt_pwd_re"
                                           id="mt_pwd_re"
                                           class="form-control"
                                           minlength="8"
                                           maxlength="50"
                                           placeholder="비밀번호 확인">
                                </div>
                            </div>

                            <div class="form-group row align-items-center">
                                <label for="mt_name" class="col-sm-2 col-form-label">이름 <b class="text-danger">*</b></label>
                                <div class="col-sm-4">
                                    <input type="text"
                                           name="mt_name"
                                           id="mt_name"
                                           class="form-control"
                                           value="<?=htmlspecialchars($row['mt_name'])?>"
                                           placeholder="이름 입력">
                                </div>

                                <label for="mt_hp" class="col-sm-2 col-form-label">휴대폰 번호 <b class="text-danger">*</b></label>
                                <div class="col-sm-4">
                                    <input type="text"
                                           name="mt_hp"
                                           id="mt_hp"
                                           class="form-control"
                                           value="<?=htmlspecialchars($row['mt_hp'])?>"
                                           placeholder="'-' 없이 숫자만 입력"
                                           numberOnly>
                                </div>
                            </div>

                            <?php if ($_GET['act'] == "update") { ?>
                                <div class="form-group row align-items-center">
                                    <label class="col-sm-2 col-form-label">가입일시</label>
                                    <div class="col-sm-4 col-form-label">
                                        <?=DateType($row['mt_wdate'], 6)?>
                                    </div>
                                    <label class="col-sm-2 col-form-label">최종수정일</label>
                                    <div class="col-sm-4 col-form-label">
                                        <?=DateType($row['mt_ldate'], 6)?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>

                        <!-- ========================== -->
                        <!-- 2. 사업자(매장) 정보 - 읽기 전용 -->
                        <!-- ========================== -->
                        <h4 class="mb-3">사업자(매장) 정보 (읽기 전용)</h4>

                        <?php if (empty($stores)) { ?>

                            <div class="border rounded p-3 mb-4 readonly-box">
                                <p class="text-muted mb-0">등록된 매장 정보가 없습니다.</p>
                            </div>

                        <?php } else { ?>

                            <!-- 매장 탭 헤더 -->
                            <ul class="nav nav-tabs" id="storeTabs" role="tablist">
                                <?php foreach ($stores as $index => $store):
                                    $store_no = $store['store_idx'];
                                    $active   = ($index === 0) ? 'active' : '';
                                    ?>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link <?=$active?>"
                                           id="store-tab-<?=$store_no?>"
                                           data-toggle="tab"
                                           href="#store-pane-<?=$store_no?>"
                                           role="tab"
                                           aria-controls="store-pane-<?=$store_no?>"
                                           aria-selected="<?=$index===0 ? 'true' : 'false'?>">
                                            매장
                                            <?php if (!empty($store['shop_name'])): ?>
                                                <small class="text-muted">(<?=htmlspecialchars($store['shop_name'])?>)</small>
                                            <?php endif; ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>

                            <div class="border border-top-0 rounded-bottom p-3 mb-4 readonly-box">
                                <div class="tab-content" id="storeTabContent">

                                    <?php foreach ($stores as $index => $store):
                                        $store_no = $store['store_idx'];
                                        $active   = ($index === 0) ? 'show active' : '';
                                        ?>
                                        <div class="tab-pane fade <?=$active?>"
                                             id="store-pane-<?=$store_no?>"
                                             role="tabpanel"
                                             aria-labelledby="store-tab-<?=$store_no?>">

                                            <div class="border rounded p-3 mb-3 bg-white">
                                                <div class="form-group row">
                                                    <label class="col-sm-2 col-form-label">상호(법인명)</label>
                                                    <div class="col-sm-4">
                                                        <input type="text" class="form-control"
                                                               value="<?=htmlspecialchars($store['store_name'])?>" readonly>
                                                    </div>

                                                    <label class="col-sm-2 col-form-label">대표자명</label>
                                                    <div class="col-sm-4">
                                                        <input type="text" class="form-control"
                                                               value="<?=htmlspecialchars($store['owner_name'])?>" readonly>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-sm-2 col-form-label">사업자등록번호</label>
                                                    <div class="col-sm-4">
                                                        <input type="text" class="form-control"
                                                               value="<?=htmlspecialchars($store['biz_no'])?>" readonly>
                                                    </div>

                                                    <label class="col-sm-2 col-form-label">매장명</label>
                                                    <div class="col-sm-4">
                                                        <input type="text" class="form-control"
                                                               value="<?=htmlspecialchars($store['shop_name'])?>" readonly>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-sm-2 col-form-label">매장연락처</label>
                                                    <div class="col-sm-4">
                                                        <input type="text" class="form-control"
                                                               value="<?=htmlspecialchars($store['sh_tel'])?>" readonly>
                                                    </div>

                                                    <label class="col-sm-2 col-form-label">위도 / 경도</label>
                                                    <div class="col-sm-4">
                                                        <input type="text" class="form-control"
                                                               value="<?=htmlspecialchars($store['lat'])?> / <?=htmlspecialchars($store['lng'])?>"
                                                               readonly>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-sm-2 col-form-label">주소</label>
                                                    <div class="col-sm-10">
                                                        <input type="text"
                                                               class="form-control mb-1"
                                                               value="<?=htmlspecialchars($store['zip'])?>"
                                                               readonly>
                                                        <input type="text"
                                                               class="form-control mb-1"
                                                               value="<?=htmlspecialchars($store['addr1'])?>"
                                                               readonly>
                                                        <input type="text"
                                                               class="form-control"
                                                               value="<?=htmlspecialchars($store['addr2'])?>"
                                                               readonly>
                                                    </div>
                                                </div>

                                                <!-- 매장 이미지 -->
                                                <div class="form-group row">
                                                    <label class="col-sm-2 col-form-label">매장 이미지</label>
                                                    <div class="col-sm-10">
                                                        <?php
                                                        $hasImg = false;
                                                        for ($i=1; $i<=3; $i++) {
                                                            $key = 'img'.$i;
                                                            if (!empty($store[$key])) {
                                                                $hasImg = true;
                                                                $url = $shop_img_url.'/'.$store_no.'/rs_'.htmlspecialchars($store[$key]);
                                                                echo '<a href="'.$shop_img_url.'/'.$store_no.'/'.htmlspecialchars($store[$key]).'" target="_blank"><img src="'.$url.'" class="thumb-img" alt="store image '.$i.'"></a>';
                                                            }
                                                        }
                                                        if (!$hasImg) {
                                                            echo '<p class="text-muted mb-0">등록된 매장 이미지가 없습니다.</p>';
                                                        }
                                                        ?>
                                                    </div>
                                                </div>

                                                <!-- 사업자등록증 -->
                                                <div class="form-group row">
                                                    <label class="col-sm-2 col-form-label">사업자등록증</label>
                                                    <div class="col-sm-10">
                                                        <?php if (!empty($store['biz_file'])): ?>
                                                            <a href="<?=$shop_img_url.'/'.$store_no.'/'.htmlspecialchars($store['biz_file'])?>"
                                                               target="_blank"
                                                               class="btn btn-sm btn-outline-primary">
                                                                사업자등록증 파일 보기
                                                            </a>
                                                        <?php else: ?>
                                                            <p class="text-muted mb-0">등록된 사업자등록증 파일이 없습니다.</p>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-sm-2 col-form-label">매장소개</label>
                                                    <div class="col-sm-10">
                                                        <textarea class="form-control" rows="10" readonly><?=$store['sh_contents']?></textarea>
                                                    </div>
                                                </div>

                                                <!-- 정산 정보 -->
                                                <hr>
                                                <h6 class="mb-3">정산 정보</h6>
                                                <div class="form-group row">
                                                    <label class="col-sm-2 col-form-label">은행</label>
                                                    <div class="col-sm-4">
                                                        <input type="text" class="form-control"
                                                               value="<?=htmlspecialchars($store['settle_bank'])?>"
                                                               readonly>
                                                    </div>

                                                    <label class="col-sm-2 col-form-label">예금주</label>
                                                    <div class="col-sm-4">
                                                        <input type="text" class="form-control"
                                                               value="<?=htmlspecialchars($store['settle_holder'])?>"
                                                               readonly>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-sm-2 col-form-label">계좌번호</label>
                                                    <div class="col-sm-4">
                                                        <input type="text" class="form-control"
                                                               value="<?=htmlspecialchars($store['settle_account'])?>"
                                                               readonly>
                                                    </div>

                                                    <label class="col-sm-2 col-form-label">통장 사본</label>
                                                    <div class="col-sm-4">
                                                        <?php if (!empty($store['bankbook'])): ?>
                                                            <a href="<?=$shop_img_url.'/'.$store_no.'/'.htmlspecialchars($store['bankbook'])?>" target="_blank">
                                                                <img src="<?=$shop_img_url.'/'.$store_no.'/rs_'.htmlspecialchars($store['bankbook'])?>"
                                                                     class="thumb-img"
                                                                     alt="bankbook">
                                                            </a>
                                                        <?php else: ?>
                                                            <p class="text-muted mb-0">등록된 통장 사본이 없습니다.</p>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>

                                            </div><!-- /.border -->
                                        </div><!-- /.tab-pane -->
                                    <?php endforeach; ?>

                                </div><!-- /.tab-content -->
                            </div><!-- /.border -->

                        <?php } // end if stores ?>

                        <!-- 하단 버튼 -->
                        <div class="form-group row justify-content-center margin-top-30">
                            <button type="button"
                                    onclick="history.back();"
                                    class="btn btn-outline-secondary mx-1">
                                목록
                            </button>
                            <button type="submit"
                                    class="btn btn-secondary mx-1">
                                저장
                            </button>
                        </div>
                    </form>
                </div>

                <script>
                    $(function () {
                        // 간단한 폼 검증 + AJAX 저장
                        $("#frm_seller").on('submit', function(e){
                            e.preventDefault();

                            const mt_name = $('#mt_name').val().trim();
                            const mt_hp   = $('#mt_hp').val().trim();
                            const mt_pwd  = $('#mt_pwd').val();
                            const mt_pwd_re = $('#mt_pwd_re').val();

                            if (!mt_name) {
                                alert('이름을 입력해주세요.');
                                $('#mt_name').focus();
                                return false;
                            }

                            if (!mt_hp) {
                                alert('휴대폰 번호를 입력해주세요.');
                                $('#mt_hp').focus();
                                return false;
                            }

                            // 휴대폰 형식 간단 체크 (01로 시작, 숫자만)
                            const hpReg = /^01[0-9][0-9]{3,4}[0-9]{4}$/;
                            // if (!hpReg.test(mt_hp)) {
                            //     alert("올바른 휴대폰 번호 형식이 아닙니다.\n('-' 없이 숫자만 입력)");
                            //     $('#mt_hp').focus();
                            //     return false;
                            // }

                            if (mt_pwd || mt_pwd_re) {
                                if (mt_pwd.length < 8) {
                                    alert('비밀번호는 8자 이상 입력해주세요.');
                                    $('#mt_pwd').focus();
                                    return false;
                                }
                                if (mt_pwd !== mt_pwd_re) {
                                    alert('비밀번호가 일치하지 않습니다.');
                                    $('#mt_pwd_re').focus();
                                    return false;
                                }
                            }

                            const form = this;
                            const formData = new FormData(form);

                            $.ajax({
                                url: $(form).attr('action'),
                                type: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
                                beforeSend: function(){
                                    $('#splinner_modal').modal('show');
                                },
                                success: function (response) {
                                    $('#splinner_modal').modal('hide');

                                    // JSON이 아니게 돌아오는 경우도 방어
                                    let res = response;
                                    if (typeof response === 'string') {
                                        try { res = JSON.parse(response); } catch(e) { res = {success:false, message:response}; }
                                    }

                                    if (res.success) {
                                        app.toastr.showSuccess(res.message || '저장되었습니다.', res.redirect || 'list.php?type=<?=$type?>');
                                    } else {
                                        app.toastr.showError(res.message || '처리 중 오류가 발생했습니다.');
                                    }
                                },
                                error: function () {
                                    $('#splinner_modal').modal('hide');
                                    app.toastr.showError('서버 통신 중 오류가 발생했습니다.');
                                }
                            });

                            return false;
                        });
                    });
                </script>
            </div>
        </div>
    </div>
    <!-- //END PAGE CONTENT CONTAINER -->

<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/foot.inc.php";
?>
