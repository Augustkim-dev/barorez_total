<?php
include $_SERVER['DOCUMENT_ROOT']."/market/head.inc.php";
$chk_menu = 99;
$chk_sub_menu = 1;
include $_SERVER['DOCUMENT_ROOT']."/market/inc/header.menu.inc.php";

$shop_img_url = '/data/shop/';
$member = [
    'mb_id'   => $_SESSION['mng']['mt_id'],
    'mb_name' => $_SESSION['mng']['mt_name'],
    'mb_hp'   => format_phone($_SESSION['mng']['mt_hp']),
];

$DB->where('mb_idx', $_SESSION['mng']['mt_idx']);
$DB->where('del_date', null, 'IS');
$rows = $DB->get('shop_t', null, '
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
    sh_tel,
    sh_biz_file,
    sh_bank,
    sh_bank_holder,
    sh_bank_account,
    sh_bankbook
');

$stores = [];
foreach ($rows as $row) {
    $store_name = trim((string)($row['sh_corp_nm'] ?? ''));
    if ($store_name === '') {
        $store_name = $row['sh_title'] ?? '';
    }

    $stores[] = [
        'store_idx'      => (int)$row['idx'],
        'store_name'     => $store_name,
        'biz_no'         => $row['sh_biz_no']        ?? '',
        'shop_name'      => $row['sh_title']         ?? '',
        'branch_name'    => $row['sh_branch_nm']     ?? '',
        'owner_name'     => $row['sh_ceo_nm']        ?? '',
        'zip'            => $row['sh_zip']           ?? '',
        'addr1'          => $row['sh_addr1']         ?? '',
        'addr2'          => $row['sh_addr2']         ?? '',
        'lat'            => $row['sh_lat']           ?? '',
        'lng'            => $row['sh_lng']           ?? '',
        'img1'           => $row['sh_img1']          ?? '',
        'img2'           => $row['sh_img2']          ?? '',
        'img3'           => $row['sh_img3']          ?? '',
        'biz_file'       => $row['sh_biz_file']      ?? '',
        'settle_bank'    => $row['sh_bank']          ?? '',
        'settle_holder'  => $row['sh_bank_holder']   ?? '',
        'settle_account' => $row['sh_bank_account']  ?? '',
        'bankbook'       => $row['sh_bankbook']      ?? '',
        'shop_tel'       => $row['sh_tel']           ?? '',
        // 정산 정보가 모두 있으면 인증 완료로 간주
        'settle_cert_ok' => (
            ($row['sh_bank'] ?? '') !== '' &&
            ($row['sh_bank_holder'] ?? '') !== '' &&
            ($row['sh_bank_account'] ?? '') !== ''
        ) ? 'Y' : 'N',
    ];
}

$store_count = max(1, count($stores));

?>
<style>
    /* ✅ QA 스타일 업로드 박스 공통 (140x140, contain) */
    .upload-container {
        display: inline-block;
        position: relative;
        width: 140px;
        height: 140px;
        margin-right: 8px;
        margin-bottom: 8px;
    }

    .upload-box {
        position: relative;
        width: 140px;
        height: 140px;
        border: 1px dashed #ced4da;
        border-radius: 0.75rem;
        background-color: #f8f9fa;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        background-size: contain;
        background-position: center;
        background-repeat: no-repeat;
        overflow: hidden;
        transition: background-color .15s ease-in-out, border-color .15s ease-in-out;
    }

    .upload-box:hover {
        background-color: #f1f3f5;
        border-color: #adb5bd;
    }

    .upload-box .upload-content {
        text-align: center;
        color: #adb5bd;
    }

    .upload-box .upload-content .plus {
        font-size: 24px;
        line-height: 1;
    }

    .upload-box .upload-content .text {
        font-size: 0.85rem;
    }

    .upload-box.has-image .upload-content {
        display: none;
    }

    .upload-box .remove-btn {
        position: absolute;
        top: 8px;
        right: 8px;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        border: none;
        background: rgba(0,0,0,0.6);
        color: #fff;
        font-size: 16px;
        line-height: 1;
        display: none;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        padding: 0;
    }

    .upload-box.has-image .remove-btn {
        display: flex;
    }
</style>

<div class="content" id="content">
    <?php include_once "./pheading.php";?>
    <div class="container-fluid">
        <div class="card margin-bottom-0">
            <div class="card-body">
                <h4 class="mb-3">내 정보 관리</h4>

                <form name="frm_myinfo" id="frm_myinfo" method="post" action="./update.php" enctype="multipart/form-data" >
                    <input type="hidden" value="input" name="act">

                    <!-- ========================== -->
                    <!-- 1. 기본 정보 -->
                    <!-- ========================== -->
                    <h5 class="mb-3">기본 정보</h5>
                    <div class="border rounded p-3 mb-4">

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">아이디</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" value="<?=htmlspecialchars($member['mb_id'])?>" readonly>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="mb_pw" class="col-sm-2 col-form-label">비밀번호</label>
                            <div class="col-sm-4 form-validate">
                                <input type="password" name="mb_pw" id="mb_pw" class="form-control" placeholder="변경 시에만 입력 (영문,숫자 조합 8~16자)">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="mb_pw_re" class="col-sm-2 col-form-label">비밀번호 확인</label>
                            <div class="col-sm-4 form-validate">
                                <input type="password" name="mb_pw_re" id="mb_pw_re" class="form-control" placeholder="비밀번호 확인">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="mb_name" class="col-sm-2 col-form-label">이름</label>
                            <div class="col-sm-4 form-validate">
                                <input type="text" name="mb_name" id="mb_name" value="<?=htmlspecialchars($member['mb_name'])?>" class="form-control" placeholder="이름 입력">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="mb_hp" class="col-sm-2 col-form-label">휴대전화</label>
                            <div class="col-sm-4 form-validate">
                                <input type="text" name="mb_hp" id="mb_hp" value="<?=htmlspecialchars($member['mb_hp'])?>" class="form-control" placeholder="'-' 없이 숫자만 입력">
                            </div>
                        </div>
                    </div>

                    <!-- ========================== -->
                    <!-- 2. 사업자(매장) 정보 - 탭 -->
                    <!-- ========================== -->

                    <h5 class="mb-3">사업자(매장) 정보</h5>

                    <!-- 탭 헤더 -->
                    <ul class="nav nav-tabs" id="storeTabs" role="tablist">
                        <?php for ($i = 0; $i < $store_count; $i++):
                            $active   = ($i === 0) ? 'active' : '';
                            $store_no = $i + 1;
                            ?>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link <?=$active?>"
                                   id="store-tab-<?=$store_no?>"
                                   data-toggle="tab"
                                   href="#store-pane-<?=$store_no?>"
                                   role="tab"
                                   aria-controls="store-pane-<?=$store_no?>"
                                   aria-selected="<?=$i===0 ? 'true':'false'?>">
                                    매장 <?=$store_no?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>

                    <div class="d-flex justify-content-between align-items-center mt-2 mb-3">
                        <small class="text-muted">※ 여러 매장을 운영 중인 경우, 매장을 추가하여 정보를 입력해주세요.</small>
                        <button type="button" id="btnAddStoreTab" class="btn btn-sm btn-outline-secondary">
                            + 매장 추가
                        </button>
                    </div>

                    <!-- 탭 콘텐츠 -->
                    <div class="tab-content" id="storeTabContent">

                        <?php if ($store_count > 0): ?>
                            <?php foreach ($stores as $idx => $store):
                                $store_no = $idx + 1;
                                $active   = ($idx === 0) ? 'show active' : '';
                                ?>
                                <div class="tab-pane fade <?=$active?>" id="store-pane-<?=$store_no?>" role="tabpanel" aria-labelledby="store-tab-<?=$store_no?>">
                                    <div class="border rounded p-3 mb-4 store-pane" data-store-no="<?=$store_no?>">

                                        <input type="hidden" name="store_idx[]" value="<?= (int)$store['store_idx'] ?>">

                                        <!-- ✅ 위도/경도 히든 -->
                                        <input type="hidden" name="lat[]" value="<?=htmlspecialchars($store['lat'])?>">
                                        <input type="hidden" name="lng[]" value="<?=htmlspecialchars($store['lng'])?>">

                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">상호(법인명)</label>
                                            <div class="col-sm-4 form-validate">
                                                <input type="text" name="store_name[]" class="form-control" value="<?=htmlspecialchars($store['store_name'])?>" placeholder="사업자등록증에 기재된 상호 입력">
                                            </div>

                                            <label class="col-sm-2 col-form-label">대표자명</label>
                                            <div class="col-sm-4 form-validate">
                                                <input type="text" name="owner_name[]" class="form-control" value="<?=htmlspecialchars($store['owner_name'])?>" placeholder="대표자명 입력">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">사업자등록번호</label>
                                            <div class="col-sm-4 form-validate">
                                                <input type="text" name="biz_no[]" class="form-control" value="<?=htmlspecialchars($store['biz_no'])?>" placeholder="'-' 포함 또는 미포함">
                                            </div>

                                            <label class="col-sm-2 col-form-label">매장명</label>
                                            <div class="col-sm-4 form-validate">
                                                <input type="text" name="shop_name[]" class="form-control" value="<?=htmlspecialchars($store['shop_name'])?>" placeholder="예) 맛있는식당 본점">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">지점명</label>
                                            <div class="col-sm-4 form-validate">
                                                <input type="text" name="branch_name[]" class="form-control" value="<?=htmlspecialchars($store['branch_name'])?>" placeholder="예) 본점, 강남점">
                                            </div>

                                            <label class="col-sm-2 col-form-label">전화번호</label>
                                            <div class="col-sm-4 form-validate">
                                                <input type="text" name="shop_tel[]" class="form-control" value="<?=htmlspecialchars($store['shop_tel'])?>" placeholder="예) 010-0000-0000">
                                            </div>
                                        </div>

<!--                                        <div class="form-group row">-->
<!--                                            <label class="col-sm-2 col-form-label">운영시간</label>-->
<!--                                            <div class="col-sm-4 form-validate">-->
<!--                                                <input type="text" name="branch_name[]" class="form-control" value="--><?php //=htmlspecialchars($store['branch_name'])?><!--" placeholder="예) 09:00 ~ 18:00">-->
<!--                                            </div>-->
<!---->
<!--                                            <label class="col-sm-2 col-form-label">휴무일</label>-->
<!--                                            <div class="col-sm-4 form-validate">-->
<!--                                                <input type="text" name="shop_name[]" class="form-control" value="--><?php //=htmlspecialchars($store['shop_name'])?><!--" placeholder="예) 매주 일요일">-->
<!--                                            </div>-->
<!--                                        </div>-->


                                        <!-- 주소 + 다음 API 버튼 -->
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">주소</label>
                                            <div class="col-sm-10">
                                                <div class="form-inline mb-2">
                                                    <input type="text" class="form-control" name="zip[]" value="<?=htmlspecialchars($store['zip'])?>" style="width:120px;" placeholder="우편번호" readonly>
                                                    <button type="button" class="btn btn-secondary ml-2 btn-addr-search">주소 검색</button>
                                                </div>
                                                <input type="text" class="form-control mb-2" name="addr1[]" value="<?=htmlspecialchars($store['addr1'])?>" placeholder="기본주소" readonly>
                                                <input type="text" class="form-control" name="addr2[]" value="<?=htmlspecialchars($store['addr2'])?>" placeholder="상세주소">
                                                <small class="form-text text-muted">
                                                    ※ 주소 저장 시 위도/경도가 자동 계산되어 함께 저장됩니다.
                                                </small>
                                            </div>
                                        </div>

                                        <!-- 매장 이미지 3장 -->
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">매장 이미지</label>
                                            <div class="col-sm-10">
                                                <div class="d-flex flex-wrap">
                                                    <?php for ($imgIdx=1; $imgIdx<=3; $imgIdx++):
                                                        $imgKey = 'img'.$imgIdx;
                                                        $imgVal = $store[$imgKey] ?? '';
                                                        $existingUrl = $imgVal ? ($shop_img_url.$store['store_idx'].'/rs_'.htmlspecialchars($imgVal)) : '';
                                                        ?>
                                                        <div class="upload-container"
                                                             data-upload-type="store"
                                                             data-upload-key="store_img<?=$imgIdx?>">
                                                            <div class="upload-box" data-existing-image="<?=$existingUrl?>">
                                                                <div class="upload-content">
                                                                    <div class="plus">+</div>
                                                                    <div class="text">이미지 <?=$imgIdx?></div>
                                                                </div>
                                                                <button type="button" class="remove-btn">×</button>
                                                            </div>
                                                            <input type="file"
                                                                   name="store_img<?=$imgIdx?>[]"
                                                                   class="d-none upload-input"
                                                                   accept="image/*">
                                                            <input type="hidden"
                                                                   name="store_img<?=$imgIdx?>_delete[]"
                                                                   class="upload-delete-flag"
                                                                   value="N">
                                                            <input type="hidden"
                                                                   name="store_img<?=$imgIdx?>_old[]"
                                                                   class="upload-old-name"
                                                                   value="<?=htmlspecialchars($imgVal)?>">
                                                        </div>
                                                    <?php endfor; ?>
                                                </div>
                                                <small class="form-text text-muted">
                                                    JPG, PNG 이미지 업로드 가능 (각 매장 최대 3장)
                                                </small>
                                            </div>
                                        </div>

                                        <!-- 사업자등록증 -->
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">사업자등록증</label>
                                            <div class="col-sm-10">
                                                <input type="file" name="biz_file[]" class="form-control-file form-control">
                                                <input type="hidden" name="biz_file_delete[]" value="N">
                                                <input type="hidden" name="biz_file_old[]" value="<?=htmlspecialchars($store['biz_file'])?>">
                                                <?php if (!empty($store['biz_file'])): ?>
                                                    <div class="mt-1">
                                                        <a href="<?=$shop_img_url.$store['store_idx'].'/'.htmlspecialchars($store['biz_file'])?>" target="_blank">
                                                            기존 파일 보기
                                                        </a>
                                                        <!-- 필요 시 삭제 버튼 사용
                                                        <button type="button" class="btn btn-sm btn-outline-danger ml-2 btn-bizfile-delete">
                                                            삭제 표시
                                                        </button>
                                                        -->
                                                    </div>
                                                <?php endif; ?>
                                                <small class="form-text text-muted">이미지(JPG, PNG) 또는 PDF 업로드</small>
                                            </div>
                                        </div>

                                        <!-- 정산 정보 (매장별) -->
                                        <hr>
                                        <h6 class="mb-3">정산 정보</h6>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">은행</label>
                                            <div class="col-sm-4">
                                                <select name="settle_bank[]" class="form-control">
                                                    <option value="">은행 선택</option>
                                                    <?php
                                                    $bankOptions = [
                                                        'KB'    => 'KB국민은행',
                                                        'SH'    => '신한은행',
                                                        'HN'    => '하나은행',
                                                        'WR'    => '우리은행',
                                                        'IBK'   => 'IBK기업은행',
                                                        'NH'    => 'NH농협은행',
                                                        'CT'    => '씨티은행',
                                                        'KAKAO' => '카카오뱅크',
                                                        'K'     => '케이뱅크',
                                                        'TOSS'  => '토스뱅크',
                                                    ];
                                                    foreach ($bankOptions as $code => $label):
                                                        $selected = ($store['settle_bank'] === $code) ? 'selected' : '';
                                                        ?>
                                                        <option value="<?=$code?>" <?=$selected?>><?=$label?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <label class="col-sm-2 col-form-label">예금주</label>
                                            <div class="col-sm-4">
                                                <input type="text" name="settle_holder[]" class="form-control" placeholder="예금주 이름 입력" value="<?=htmlspecialchars($store['settle_holder'])?>">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">계좌번호</label>
                                            <div class="col-sm-4">
                                                <input type="text" name="settle_account[]" class="form-control" placeholder="'-' 없이 숫자만 입력" value="<?=htmlspecialchars($store['settle_account'])?>">
                                            </div>
                                            <div class="col-sm-4">
                                                <button type="button" class="btn btn-outline-secondary btn-account-cert">
                                                    계좌 인증 요청
                                                </button>
                                                <small class="form-text text-muted">
                                                    인증 요청 후, 안내에 따라 계좌 인증을 완료해 주세요.
                                                </small>
                                            </div>
                                            <!-- 계좌 인증 여부 -->
                                            <input type="hidden" name="settle_cert_ok[]" value="<?=htmlspecialchars($store['settle_cert_ok'])?>">
                                        </div>

                                        <!-- 통장 사본 (매장별) -->
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">통장 사본</label>
                                            <div class="col-sm-10">
                                                <?php
                                                $bankbookUrl = !empty($store['bankbook'])
                                                    ? $shop_img_url.$store['store_idx'].'/rs_'.htmlspecialchars($store['bankbook'])
                                                    : '';
                                                ?>
                                                <div class="upload-container"
                                                     data-upload-type="bankbook"
                                                     data-upload-key="store_bankbook">
                                                    <div class="upload-box" data-existing-image="<?=$bankbookUrl?>">
                                                        <div class="upload-content">
                                                            <div class="plus">+</div>
                                                            <div class="text">통장 사본 업로드</div>
                                                        </div>
                                                        <button type="button" class="remove-btn">×</button>
                                                    </div>
                                                    <input type="file"
                                                           name="store_bankbook[]"
                                                           class="d-none upload-input"
                                                           accept="image/*">
                                                    <input type="hidden"
                                                           name="store_bankbook_delete[]"
                                                           class="upload-delete-flag"
                                                           value="N">
                                                    <input type="hidden"
                                                           name="store_bankbook_old[]"
                                                           class="upload-old-name"
                                                           value="<?=htmlspecialchars($store['bankbook'])?>">
                                                </div>
                                                <small class="form-text text-muted">
                                                    통장 첫 페이지(예금주/계좌번호가 보이도록) 이미지 업로드
                                                </small>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-sm-12 text-right">
                                                <button type="button" class="btn btn-outline-danger btn-sm btn-remove-store">
                                                    매장 삭제
                                                </button>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- 매장 0개일 때 기본 템플릿 하나 출력 -->
                            <div class="tab-pane fade show active" id="store-pane-1" role="tabpanel" aria-labelledby="store-tab-1">
                                <div class="border rounded p-3 mb-4 store-pane" data-store-no="1">
                                    <input type="hidden" name="store_idx[]" value="">
                                    <!-- ✅ 위도/경도 히든 -->
                                    <input type="hidden" name="lat[]" value="">
                                    <input type="hidden" name="lng[]" value="">

                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">상호(법인명)</label>
                                        <div class="col-sm-4 form-validate">
                                            <input type="text" name="store_name[]" class="form-control" placeholder="사업자등록증에 기재된 상호 입력">
                                        </div>

                                        <label class="col-sm-2 col-form-label">대표자명</label>
                                        <div class="col-sm-4 form-validate">
                                            <input type="text" name="owner_name[]" class="form-control" placeholder="대표자명 입력">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">사업자등록번호</label>
                                        <div class="col-sm-4 form-validate">
                                            <input type="text" name="biz_no[]" class="form-control" placeholder="'-' 포함 또는 미포함">
                                        </div>

                                        <label class="col-sm-2 col-form-label">매장명</label>
                                        <div class="col-sm-4 form-validate">
                                            <input type="text" name="shop_name[]" class="form-control" placeholder="예) 맛있는식당 본점">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">지점명</label>
                                        <div class="col-sm-4 form-validate">
                                            <input type="text" name="branch_name[]" class="form-control" placeholder="예) 본점, 강남점">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">주소</label>
                                        <div class="col-sm-10">
                                            <div class="form-inline mb-2">
                                                <input type="text" class="form-control" name="zip[]" style="width:120px;" placeholder="우편번호" readonly>
                                                <button type="button" class="btn btn-secondary ml-2 btn-addr-search">주소 검색</button>
                                            </div>
                                            <input type="text" class="form-control mb-2" name="addr1[]" placeholder="기본주소" readonly>
                                            <input type="text" class="form-control" name="addr2[]" placeholder="상세주소">
                                            <small class="form-text text-muted">
                                                ※ 주소 저장 시 위도/경도가 자동 계산되어 함께 저장됩니다.
                                            </small>
                                        </div>
                                    </div>

                                    <!-- 매장 이미지 3장 -->
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">매장 이미지</label>
                                        <div class="col-sm-10">
                                            <div class="d-flex flex-wrap">
                                                <?php for ($imgIdx=1; $imgIdx<=3; $imgIdx++): ?>
                                                    <div class="upload-container"
                                                         data-upload-type="store"
                                                         data-upload-key="store_img<?=$imgIdx?>">
                                                        <div class="upload-box" data-existing-image="">
                                                            <div class="upload-content">
                                                                <div class="plus">+</div>
                                                                <div class="text">이미지 <?=$imgIdx?></div>
                                                            </div>
                                                            <button type="button" class="remove-btn">×</button>
                                                        </div>
                                                        <input type="file"
                                                               name="store_img<?=$imgIdx?>[]"
                                                               class="d-none upload-input"
                                                               accept="image/*">
                                                        <input type="hidden"
                                                               name="store_img<?=$imgIdx?>_delete[]"
                                                               class="upload-delete-flag"
                                                               value="N">
                                                        <input type="hidden"
                                                               name="store_img<?=$imgIdx?>_old[]"
                                                               class="upload-old-name"
                                                               value="">
                                                    </div>
                                                <?php endfor; ?>
                                            </div>
                                            <small class="form-text text-muted">
                                                JPG, PNG 이미지 업로드 가능 (각 매장 최대 3장)
                                            </small>
                                        </div>
                                    </div>

                                    <!-- 사업자등록증 -->
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">사업자등록증</label>
                                        <div class="col-sm-10">
                                            <input type="file" name="biz_file[]" class="form-control-file form-control">
                                            <input type="hidden" name="biz_file_delete[]" value="N">
                                            <input type="hidden" name="biz_file_old[]" value="">
                                            <small class="form-text text-muted">이미지(JPG, PNG) 또는 PDF 업로드</small>
                                        </div>
                                    </div>

                                    <!-- 정산 정보 -->
                                    <hr>
                                    <h6 class="mb-3">정산 정보</h6>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">은행</label>
                                        <div class="col-sm-4">
                                            <select name="settle_bank[]" class="form-control">
                                                <option value="">은행 선택</option>
                                                <option value="KB">KB국민은행</option>
                                                <option value="SH">신한은행</option>
                                                <option value="HN">하나은행</option>
                                                <option value="WR">우리은행</option>
                                                <option value="IBK">IBK기업은행</option>
                                                <option value="NH">NH농협은행</option>
                                                <option value="CT">씨티은행</option>
                                                <option value="KAKAO">카카오뱅크</option>
                                                <option value="K">케이뱅크</option>
                                                <option value="TOSS">토스뱅크</option>
                                            </select>
                                        </div>

                                        <label class="col-sm-2 col-form-label">예금주</label>
                                        <div class="col-sm-4">
                                            <input type="text" name="settle_holder[]" class="form-control" placeholder="예금주 이름 입력">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">계좌번호</label>
                                        <div class="col-sm-4">
                                            <input type="text" name="settle_account[]" class="form-control" placeholder="'-' 없이 숫자만 입력">
                                        </div>
                                        <div class="col-sm-4">
                                            <button type="button" class="btn btn-outline-secondary btn-account-cert">
                                                계좌 인증 요청
                                            </button>
                                            <small class="form-text text-muted">
                                                인증 요청 후, 안내에 따라 계좌 인증을 완료해 주세요.
                                            </small>
                                        </div>
                                        <input type="hidden" name="settle_cert_ok[]" value="N">
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">통장 사본</label>
                                        <div class="col-sm-10">
                                            <div class="upload-container"
                                                 data-upload-type="bankbook"
                                                 data-upload-key="store_bankbook">
                                                <div class="upload-box" data-existing-image="">
                                                    <div class="upload-content">
                                                        <div class="plus">+</div>
                                                        <div class="text">통장 사본 업로드</div>
                                                    </div>
                                                    <button type="button" class="remove-btn">×</button>
                                                </div>
                                                <input type="file"
                                                       name="store_bankbook[]"
                                                       class="d-none upload-input"
                                                       accept="image/*">
                                                <input type="hidden"
                                                       name="store_bankbook_delete[]"
                                                       class="upload-delete-flag"
                                                       value="N">
                                                <input type="hidden"
                                                       name="store_bankbook_old[]"
                                                       class="upload-old-name"
                                                       value="">
                                            </div>
                                            <small class="form-text text-muted">
                                                통장 첫 페이지(예금주/계좌번호가 보이도록) 이미지 업로드
                                            </small>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-sm-12 text-right">
                                            <button type="button" class="btn btn-outline-danger btn-sm btn-remove-store">
                                                매장 삭제
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        <?php endif; ?>

                    </div><!-- /.tab-content -->

                    <!-- 숨겨진 매장 템플릿 (JS로 복제용) -->
                    <script type="text/template" id="store-pane-template">
                        <div class="tab-pane fade" id="__PANE_ID__" role="tabpanel" aria-labelledby="__TAB_ID__">
                            <div class="border rounded p-3 mb-4 store-pane" data-store-no="__NO__">
                                <input type="hidden" name="store_idx[]" value="">
                                <!-- ✅ 위도/경도 히든 -->
                                <input type="hidden" name="lat[]" value="">
                                <input type="hidden" name="lng[]" value="">

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">상호(법인명)</label>
                                    <div class="col-sm-4 form-validate">
                                        <input type="text" name="store_name[]" class="form-control" placeholder="사업자등록증에 기재된 상호 입력">
                                    </div>

                                    <label class="col-sm-2 col-form-label">대표자명</label>
                                    <div class="col-sm-4 form-validate">
                                        <input type="text" name="owner_name[]" class="form-control" placeholder="대표자명 입력">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">사업자등록번호</label>
                                    <div class="col-sm-4 form-validate">
                                        <input type="text" name="biz_no[]" class="form-control" placeholder="'-' 포함 또는 미포함">
                                    </div>

                                    <label class="col-sm-2 col-form-label">매장명</label>
                                    <div class="col-sm-4 form-validate">
                                        <input type="text" name="shop_name[]" class="form-control" placeholder="예) 맛있는식당 본점">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">지점명</label>
                                    <div class="col-sm-4 form-validate">
                                        <input type="text" name="branch_name[]" class="form-control" placeholder="예) 본점, 강남점">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">주소</label>
                                    <div class="col-sm-10">
                                        <div class="form-inline mb-2">
                                            <input type="text" class="form-control" name="zip[]" style="width:120px;" placeholder="우편번호" readonly>
                                            <button type="button" class="btn btn-secondary ml-2 btn-addr-search">주소 검색</button>
                                        </div>
                                        <input type="text" class="form-control mb-2" name="addr1[]" placeholder="기본주소" readonly>
                                        <input type="text" class="form-control" name="addr2[]" placeholder="상세주소">
                                        <small class="form-text text-muted">
                                            ※ 주소 저장 시 위도/경도가 자동 계산되어 함께 저장됩니다.
                                        </small>
                                    </div>
                                </div>

                                <!-- 매장 이미지 3장 -->
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">매장 이미지</label>
                                    <div class="col-sm-10">
                                        <div class="d-flex flex-wrap">
                                            <?php for ($imgIdx=1; $imgIdx<=3; $imgIdx++): ?>
                                                <div class="upload-container"
                                                     data-upload-type="store"
                                                     data-upload-key="store_img<?=$imgIdx?>">
                                                    <div class="upload-box" data-existing-image="">
                                                        <div class="upload-content">
                                                            <div class="plus">+</div>
                                                            <div class="text">이미지 <?=$imgIdx?></div>
                                                        </div>
                                                        <button type="button" class="remove-btn">×</button>
                                                    </div>
                                                    <input type="file"
                                                           name="store_img<?=$imgIdx?>[]"
                                                           class="d-none upload-input"
                                                           accept="image/*">
                                                    <input type="hidden"
                                                           name="store_img<?=$imgIdx?>_delete[]"
                                                           class="upload-delete-flag"
                                                           value="N">
                                                    <input type="hidden"
                                                           name="store_img<?=$imgIdx?>_old[]"
                                                           class="upload-old-name"
                                                           value="">
                                                </div>
                                            <?php endfor; ?>
                                        </div>
                                        <small class="form-text text-muted">
                                            JPG, PNG 이미지 업로드 가능 (각 매장 최대 3장)
                                        </small>
                                    </div>
                                </div>

                                <!-- 사업자등록증 -->
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">사업자등록증</label>
                                    <div class="col-sm-10">
                                        <input type="file" name="biz_file[]" class="form-control-file form-control">
                                        <input type="hidden" name="biz_file_delete[]" value="N">
                                        <input type="hidden" name="biz_file_old[]" value="">
                                        <small class="form-text text-muted">이미지(JPG, PNG) 또는 PDF 업로드</small>
                                    </div>
                                </div>

                                <!-- 정산 정보 -->
                                <hr>
                                <h6 class="mb-3">정산 정보</h6>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">은행</label>
                                    <div class="col-sm-4">
                                        <select name="settle_bank[]" class="form-control">
                                            <option value="">은행 선택</option>
                                            <option value="KB">KB국민은행</option>
                                            <option value="SH">신한은행</option>
                                            <option value="HN">하나은행</option>
                                            <option value="WR">우리은행</option>
                                            <option value="IBK">IBK기업은행</option>
                                            <option value="NH">NH농협은행</option>
                                            <option value="CT">씨티은행</option>
                                            <option value="KAKAO">카카오뱅크</option>
                                            <option value="K">케이뱅크</option>
                                            <option value="TOSS">토스뱅크</option>
                                        </select>
                                    </div>

                                    <label class="col-sm-2 col-form-label">예금주</label>
                                    <div class="col-sm-4">
                                        <input type="text" name="settle_holder[]" class="form-control" placeholder="예금주 이름 입력">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">계좌번호</label>
                                    <div class="col-sm-4">
                                        <input type="text" name="settle_account[]" class="form-control" placeholder="'-' 없이 숫자만 입력">
                                    </div>
                                    <div class="col-sm-4">
                                        <button type="button" class="btn btn-outline-secondary btn-account-cert">
                                            계좌 인증 요청
                                        </button>
                                        <small class="form-text text-muted">
                                            인증 요청 후, 안내에 따라 계좌 인증을 완료해 주세요.
                                        </small>
                                    </div>
                                    <input type="hidden" name="settle_cert_ok[]" value="N">
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">통장 사본</label>
                                    <div class="col-sm-10">
                                        <div class="upload-container"
                                             data-upload-type="bankbook"
                                             data-upload-key="store_bankbook">
                                            <div class="upload-box" data-existing-image="">
                                                <div class="upload-content">
                                                    <div class="plus">+</div>
                                                    <div class="text">통장 사본 업로드</div>
                                                </div>
                                                <button type="button" class="remove-btn">×</button>
                                            </div>
                                            <input type="file"
                                                   name="store_bankbook[]"
                                                   class="d-none upload-input"
                                                   accept="image/*">
                                            <input type="hidden"
                                                   name="store_bankbook_delete[]"
                                                   class="upload-delete-flag"
                                                   value="N">
                                            <input type="hidden"
                                                   name="store_bankbook_old[]"
                                                   class="upload-old-name"
                                                   value="">
                                        </div>
                                        <small class="form-text text-muted">
                                            통장 첫 페이지(예금주/계좌번호가 보이도록) 이미지 업로드
                                        </small>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-12 text-right">
                                        <button type="button" class="btn btn-outline-danger btn-sm btn-remove-store">
                                            매장 삭제
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </script>

                    <!-- 하단 버튼 -->
                    <div class="form-group row justify-content-center mt-4">
                        <button type="button" onclick="history.back();" class="btn btn-outline-secondary mx-1">취소</button>
                        <button type="submit" class="btn btn-primary mx-1">저장</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<?php include $_SERVER['DOCUMENT_ROOT']."/market/foot.inc.php"; ?>

<!-- Daum 주소 검색 API -->
<script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>

<!-- ✅ 카카오 지도 JS (Geocoder 사용용) : appkey는 JavaScript 키로 교체 -->
<script src="//dapi.kakao.com/v2/maps/sdk.js?appkey=<?=KAKAO_JAVASCRIPT_KEY?>&libraries=services"></script>

<script>
    console.log('rr','<?=KAKAO_JAVASCRIPT_KEY?>')
    // ================================
    // 업로드 박스 공통 함수 (QA 스타일)
    // ================================
    function setUploadBoxPreview($box, url) {
        $box.css({
            'background-image': "url('" + url + "')",
            'background-size': 'contain',
            'background-position': 'center',
            'background-repeat': 'no-repeat'
        });
        $box.addClass('has-image');
        $box.find('.upload-content').hide();
    }

    function resetUploadBox($box) {
        $box.css('background-image', 'none');
        $box.removeClass('has-image');
        $box.find('.upload-content').show();
    }

    function initUploadContainer($container) {
        if ($container.data('upload-initialized') === true) return;
        $container.data('upload-initialized', true);

        const $box    = $container.find('.upload-box');
        const $input  = $container.find('.upload-input');
        const $delFlg = $container.find('.upload-delete-flag');
        const $old    = $container.find('.upload-old-name');

        let existing = $box.data('existing-image');
        if (existing) {
            setUploadBoxPreview($box, existing);
        } else {
            resetUploadBox($box);
        }

        // 박스 클릭 → 파일 선택 (X 버튼 제외)
        $box.on('click', function (e) {
            if ($(e.target).hasClass('remove-btn')) return;
            $input.trigger('click');
        });

        // 파일 선택 시 미리보기
        $input.on('change', function () {
            const file = this.files[0];
            if (!file) {
                // 파일 선택 취소
                existing = '';
                $box.data('existing-image', '');
                resetUploadBox($box);
                if ($delFlg.length) $delFlg.val('N');
                if ($old.length && !$old.val()) $old.val('');
                return;
            }
            const reader = new FileReader();
            reader.onload = function (e) {
                setUploadBoxPreview($box, e.target.result);
                if ($delFlg.length) $delFlg.val('N');
            };
            reader.readAsDataURL(file);
        });

        // 삭제 버튼
        $box.find('.remove-btn').on('click', function (e) {
            e.stopPropagation();
            $input.val('');
            $box.data('existing-image', '');
            resetUploadBox($box);
            if ($delFlg.length) $delFlg.val('Y');
            if ($old.length)    $old.val('');
        });
    }

    function initAllUploadContainers($root) {
        const $scope = $root ? $root : $(document);
        $scope.find('.upload-container').each(function () {
            initUploadContainer($(this));
        });
    }

    // ✅ 전역 카카오 Geocoder 객체 (주소 → 좌표 변환용)
    var kakaoGeocoder = null;

    $(function () {

        // ✅ 카카오 Geocoder 초기화
        if (window.kakao && kakao.maps && kakao.maps.services) {
            kakaoGeocoder = new kakao.maps.services.Geocoder();
        } else {
            console.warn('Kakao Maps JS SDK가 로드되지 않았습니다. 위도/경도 자동 계산이 동작하지 않습니다.');
        }

        // 현재 탭 개수
        let storeIndex = <?=$store_count?>;

        // 초기 업로드 박스 셋업
        initAllUploadContainers();

        // -----------------------------------
        // 매장 추가 (탭 + 내용)
        // -----------------------------------
        $('#btnAddStoreTab').on('click', function () {
            storeIndex++;
            const no     = storeIndex;
            const tabId  = 'store-tab-' + no;
            const paneId = 'store-pane-' + no;

            const $tabs = $('#storeTabs');
            $tabs.find('.nav-link').removeClass('active');
            $tabs.append(
                '<li class="nav-item" role="presentation">' +
                '  <a class="nav-link active" id="'+tabId+'" data-toggle="tab" href="#'+paneId+'" role="tab" aria-controls="'+paneId+'" aria-selected="true">매장 '+no+'</a>' +
                '</li>'
            );

            const tpl = $('#store-pane-template').html()
                .replace(/__PANE_ID__/g, paneId)
                .replace(/__TAB_ID__/g, tabId)
                .replace(/__NO__/g, no);

            $('#storeTabContent .tab-pane').removeClass('show active');
            $('#storeTabContent').append(tpl);

            const $pane = $('#'+paneId);
            $pane.addClass('show active');

            // 새로 append 된 영역 안의 업로드 박스 초기화
            initAllUploadContainers($pane);
        });

        function removeStorePaneById(paneId) {
            $('#storeTabs a[href="#' + paneId + '"]').closest('li').remove();
            $('#' + paneId).remove();

            if ($('#storeTabs a.nav-link').length === 0) {
                $('#btnAddStoreTab').trigger('click');
            } else {
                const $firstTab = $('#storeTabs a.nav-link').first();
                $firstTab.addClass('active');
                $($firstTab.attr('href')).addClass('show active');
            }
        }

        // -----------------------------------
        // 매장 삭제
        // -----------------------------------
        $(document).on('click', '.btn-remove-store', function () {
            const $pane  = $(this).closest('.tab-pane');
            const paneId = $pane.attr('id');

            if (!confirm('현재 매장 정보를 삭제하시겠습니까?')) return;

            const storeIdx = $pane.find('input[name="store_idx[]"]').val();

            if (!storeIdx || parseInt(storeIdx, 10) <= 0) {
                removeStorePaneById(paneId);
                return;
            }

            $.ajax({
                url: './update.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    act: 'delete',
                    store_idx: storeIdx
                },
                success: function (res) {
                    if (res && res.success) {
                        alert(res.message || '매장이 삭제되었습니다.');
                        removeStorePaneById(paneId);
                    } else {
                        alert((res && res.message) || '매장 삭제에 실패했습니다.');
                    }
                },
                error: function () {
                    alert('통신 오류로 매장 삭제에 실패했습니다.');
                }
            });
        });

        // -----------------------------------
        // 사업자등록증 삭제 표시 버튼 (옵션)
        // -----------------------------------
        // $(document).on('click', '.btn-bizfile-delete', function () {
        //     if (!confirm('기존 사업자등록증 파일을 삭제 처리하시겠습니까?')) return;
        //
        //     const $wrap = $(this).closest('.form-group');
        //     $wrap.find('input[name="biz_file_delete[]"]').val('Y');
        //     $wrap.find('input[name="biz_file_old[]"]').val('');
        //     $(this).prev('a').css('text-decoration', 'line-through');
        // });

        // -----------------------------------
        // 다음 주소 검색 + ✅ 카카오 Geocoder로 위도/경도 세팅
        // -----------------------------------
        $(document).on('click', '.btn-addr-search', function () {
            const $pane  = $(this).closest('.store-pane');
            const $zip   = $pane.find('input[name="zip[]"]');
            const $addr1 = $pane.find('input[name="addr1[]"]');
            const $addr2 = $pane.find('input[name="addr2[]"]');
            const $lat   = $pane.find('input[name="lat[]"]');   // ✅ 히든 lat
            const $lng   = $pane.find('input[name="lng[]"]');   // ✅ 히든 lng

            new daum.Postcode({
                oncomplete: function(data) {
                    let addr = data.roadAddress;
                    if (!addr || addr === '') {
                        addr = data.jibunAddress;
                    }

                    $zip.val(data.zonecode);
                    $addr1.val(addr);
                    $addr2.focus();

                    // ✅ Geocoder 준비 여부 체크
                    if (!kakaoGeocoder) {
                        console.warn('kakaoGeocoder가 아직 준비되지 않았습니다.');
                        $lat.val('');
                        $lng.val('');
                        return;
                    }

                    kakaoGeocoder.addressSearch(addr, function(result, status) {
                        if (status === kakao.maps.services.Status.OK && result[0]) {
                            $lat.val(result[0].y);  // 위도
                            $lng.val(result[0].x);  // 경도
                            // console.log('geocode 성공:', result[0].y, result[0].x);
                        } else {
                            $lat.val('');
                            $lng.val('');
                            console.warn('Geocoding 실패:', status, result);
                        }
                    });
                }
            }).open();
        });

        // -----------------------------------
        // 정산 계좌 인증 버튼 (매장별)
        // -----------------------------------
        $(document).on('click', '.btn-account-cert', function () {
            const $pane   = $(this).closest('.store-pane');
            const $bank   = $pane.find('select[name="settle_bank[]"]');
            const $holder = $pane.find('input[name="settle_holder[]"]');
            const $acct   = $pane.find('input[name="settle_account[]"]');
            const $cert   = $pane.find('input[name="settle_cert_ok[]"]');

            const bank    = $bank.val();
            const holder  = $holder.val().trim();
            const account = $acct.val().trim();

            if (!bank) {
                alert('은행을 선택해 주세요.');
                $bank.focus();
                return;
            }
            if (!holder) {
                alert('예금주를 입력해 주세요.');
                $holder.focus();
                return;
            }
            if (!account) {
                alert('계좌번호를 입력해 주세요.');
                $acct.focus();
                return;
            }

            // TODO: 실제 계좌 인증 API 연동
            // ✅ 현재는 테스트용으로 바로 인증 완료 처리
            $cert.val('Y');
            alert('계좌 인증이 완료되었습니다. (실제 서비스에서는 계좌 인증 API를 연동해주세요)');
        });

    });
</script>
