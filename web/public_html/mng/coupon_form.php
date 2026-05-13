<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu='5';
$chk_sub_menu='1';
include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.menu.inc.php";
//include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.sidebar.inc.php";


if ($_GET['act'] == "update") {
    $DB->where('idx', $_GET['ct_idx']);
    $row = $DB->getone('coupon_t', '*, idx as ct_idx');

    $_act = "update";
    $_act_txt = " 수정";
} else {
    $_act = "input";
    $_act_txt = " 등록";
}
?>
    <!-- PAGE CONTENT CONTAINER -->
    <div class="content" id="content">
        <!-- PAGE HEADING -->
        <div class="page-heading">
            <div class="page-heading__container">
                <div class="icon">
                    <span class="li-picture3"></span>
                </div>
                <h1 class="title">쿠폰관리</h1>
                <p class="caption">
                    쿠폰관리 등록, 수정, 삭제 등을 할 수 있습니다.
                </p>
            </div>
            <nav aria-label="breadcrumb" role="navigation">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#">쿠폰관리</a></li>
                    <li class="breadcrumb-item active">쿠폰<?=$_act_txt?></li>
                </ol>
            </nav>
        </div>
        <!-- //END PAGE HEADING -->
        <div class="container-fluid">
            <div class="card margin-bottom-0">
                <div class="card-body">
                    <form method="post" name="frm_form" id="frm_form" action="./coupon_update.php" target="hidden_ifrm" enctype="multipart/form-data">
                        <input type="hidden" name="act" id="act" value="<?=$_act?>" />
                        <input type="hidden" name="ct_idx" id="ct_idx" value="<?=$row['ct_idx']?>" />
                        <input type="hidden" name="file_count" id="file_count" value="<?=$file_count?>" />
                        <input type="hidden" name="board" id="board" value="<?=$board?>" />
                        <div class="card-body">
                            <h4 id="rw-fe-basic">쿠폰 <?=$_act_txt?></h4>

                            <div class="form-group row margin-top-30">
                                <label for="ct_seller_idx" class="col-sm-2 col-form-label">스토어 <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <select name="ct_seller_idx" id="ct_seller_idx" class="form-control" style="display: none;" required>
                                        <option value="">스토어를 선택해주세요</option>
                                        <?php if($row['ct_seller_idx']) {
                                            // 기존 선택된 판매자가 있을 경우 표시
                                            $seller_info = getSeller($row['ct_seller_idx']);
                                            if($seller_info) { ?>
                                                <option value="<?=$seller_info['mb_idx']?>" selected><?=$seller_info['mb_name']?> (<?=$seller_info['mb_id']?>)</option>
                                            <?php }
                                        } ?>
                                    </select>
                                    <div id="seller_loading_container" class="text-center py-3">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="sr-only">로딩중...</span>
                                        </div>
                                        <div class="mt-2">스토어 정보를 불러오는 중입니다...</div>
                                    </div>
                                </div>
                            </div>


                            <div class="form-group row">
                                <label for="ct_title" class="col-sm-2 col-form-label">쿠폰명 <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <input type="text" name="ct_title" id="ct_title" value="<?=$row['ct_title']?>" class="form-control" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="ct_code" class="col-sm-2 col-form-label">쿠폰코드</label>
                                <div class="col-sm-10">
                                    <input type="text" name="ct_code" id="ct_code" value="<?=$row['ct_code']?>"
                                           placeholder="<?= $_act == 'input' ? '쿠폰 코드는 자동으로 생성됩니다' : '' ?>"
                                           class="form-control" readonly>
                                </div>
                            </div>


                            <div class="form-group row">
                                <label for="ct_show" class="col-sm-2 col-form-label">사용여부</label>
                                <div class="col-sm-10">
                                    <select name="ct_show" id="ct_show" class="form-control select-simple" data-initial-value="<?=$row['ct_show']?>">
                                        <option value="Y">사용</option>
                                        <option value="N">미사용</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="ct_type1" class="col-sm-2 col-form-label">유효기간 설정 <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="ct_type1_1" name="ct_type1" value="1" class="custom-control-input" <?=$row['ct_type1']=='1'?'checked':''?> <?=$row['ct_type1']==''?'checked':''?>>
                                        <label class="custom-control-label" for="ct_type1_1">기간 설정</label>
                                    </div>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="ct_type1_2" name="ct_type1" value="2" class="custom-control-input" <?=$row['ct_type1']=='2'?'checked':''?>>
                                        <label class="custom-control-label" for="ct_type1_2">발급일 기준</label>
                                    </div>

                                    <div id="date_range" class="mt-2" style="<?=$row['ct_type1']=='2'?'display:none':''?>">
                                        <div class="input-group">
                                            <input type="date" name="ct_sdate" id="ct_sdate" value="<?=$row['ct_sdate']?>" class="form-control" placeholder="시작일">
                                            <div class="input-group-prepend input-group-append">
                                                <span class="input-group-text">~</span>
                                            </div>
                                            <input type="date" name="ct_edate" id="ct_edate" value="<?=$row['ct_edate']?>" class="form-control" placeholder="종료일">
                                        </div>
                                    </div>

                                    <div id="date_days" class="mt-2" style="<?=$row['ct_type1']=='1'||$row['ct_type1']==''?'display:none':''?>">
                                        <div class="input-group">
                                            <input type="number" name="ct_days" id="ct_days" value="<?=$row['ct_days']?>" class="form-control" min="1" placeholder="유효일수">
                                            <div class="input-group-append">
                                                <span class="input-group-text">일</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="ct_method" class="col-sm-2 col-form-label">쿠폰종류</label>
                                <div class="col-sm-10">
                                    <select name="ct_method" id="ct_method" class="form-control select-simple" data-initial-value="<?=$row['ct_method']?>">
                                        <option value="1">개별상품할인</option>
                                        <option value="2">카테고리할인</option>
                                        <option value="3">주문금액할인</option>
                                        <option value="4">배송비할인</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="ct_target" class="col-sm-2 col-form-label">적용 대상</label>
                                <div class="col-sm-10">
                                    <select name="ct_target" id="ct_target" class="form-control select-simple" data-initial-value="<?=$row['ct_target']?>">
                                        <option value="0">전체 상품</option>
                                        <option value="1">특정 카테고리</option>
                                        <option value="2">특정 상품</option>
                                    </select>

                                    <div id="target_category" class="mt-2" style="<?=$row['ct_target']=='1'?'':'display:none'?>">
                                        <select name="ct_target_category[]" id="ct_target_category" class="form-control select2" multiple data-placeholder="카테고리 선택">
                                            <?php
                                            // 카테고리 목록 출력
                                            $selected_categories = explode(',', $row['ct_target_name']);
                                            foreach($categories as $category) {
                                                $selected = in_array($category['idx'], $selected_categories) ? 'selected' : '';
                                                echo '<option value="'.$category['idx'].'" '.$selected.'>'.$category['name'].'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <div id="target_product" class="mt-2" style="<?=$row['ct_target']=='2'?'':'display:none'?>">
                                        <select name="ct_target_product[]" id="ct_target_product" class="form-control select2" multiple data-placeholder="상품 선택">
                                            <?php
                                            // 상품 목록 출력
                                            $selected_products = explode(',', $row['ct_target_name']);
                                            foreach($products as $product) {
                                                $selected = in_array($product['idx'], $selected_products) ? 'selected' : '';
                                                echo '<option value="'.$product['idx'].'" '.$selected.'>'.$product['name'].'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="ct_type2" class="col-sm-2 col-form-label">할인 유형 <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <!--<input type="hidden" id="ct_type2_hidden" name="ct_type2" value="<?=$row['ct_type2'] ?: '1'?>">-->
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="ct_type2_1" name="ct_type2" value="1" class="custom-control-input" <?=$row['ct_type2']=='1'?'checked':''?> <?=$row['ct_type2']==''?'checked':''?>>
                                        <label class="custom-control-label" for="ct_type2_1">정액 할인</label>
                                    </div>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="ct_type2_2" name="ct_type2" value="2" class="custom-control-input" <?=$row['ct_type2']=='2'?'checked':''?>>
                                        <label class="custom-control-label" for="ct_type2_2">정률 할인</label>
                                    </div>

                                    <div id="discount_amount" class="mt-2">
                                        <div class="input-group">
                                            <input type="number" name="ct_discount1" id="ct_discount1" value="<?=$row['ct_discount1']?>" class="form-control" min="0" required>
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="discount_unit"><?=$row['ct_type2']=='2'?'%':'원'?></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="discount_max" class="mt-2" style="<?=$row['ct_type2']=='1'?'':'display:none'?>">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">최대 할인금액</span>
                                            </div>
                                            <input type="number" name="ct_discount2" id="ct_discount2" value="<?=$row['ct_discount2']?>" class="form-control" min="0" placeholder="제한 없음">
                                            <div class="input-group-append">
                                                <span class="input-group-text">원</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="ct_discount3" class="col-sm-2 col-form-label">최소 주문금액</label>
                                <div class="col-sm-10">
                                    <div class="input-group">
                                        <input type="number" name="ct_discount3" id="ct_discount3" value="<?=$row['ct_discount3']?>" class="form-control" min="0" placeholder="제한 없음">
                                        <div class="input-group-append">
                                            <span class="input-group-text">원</span>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">설정 시 해당 금액 이상 주문 시에만 쿠폰 사용 가능</small>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="ct_auto_issue" class="col-sm-2 col-form-label">자동 발급</label>
                                <div class="col-sm-10">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="ct_auto_issue" name="ct_auto_issue" value="Y" <?=$row['ct_auto_issue']=='Y'?'checked':''?>>
                                        <label class="custom-control-label" for="ct_auto_issue">자동 발급 사용</label>
                                    </div>

                                    <div id="auto_issue_options" class="mt-2" style="<?=$row['ct_auto_issue']=='Y'?'':'display:none'?>">
                                        <select name="ct_auto_type" id="ct_auto_type" class="form-control select-simple" data-initial-value="<?=$row['ct_auto_type']?>">
                                            <option value="1">회원 가입 시</option>
                                            <option value="2">생일 축하</option>
                                            <option value="3">첫 구매 시</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="ct_issue_limit" class="col-sm-2 col-form-label">발급 제한</label>
                                <div class="col-sm-10">
                                    <div class="input-group">
                                        <input type="number" name="ct_issue_limit" id="ct_issue_limit" value="<?=$row['ct_issue_limit']?>" class="form-control" min="0" placeholder="제한 없음">
                                        <div class="input-group-append">
                                            <span class="input-group-text">개</span>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">설정 시 해당 개수만큼만 발급 가능 (0 = 무제한)</small>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="ct_memo" class="col-sm-2 col-form-label">관리자 메모</label>
                                <div class="col-sm-10">
                                    <textarea name="ct_memo" id="ct_memo" class="form-control" rows="3"><?=$row['ct_memo']?></textarea>
                                </div>
                            </div>

                            <? if ($_GET['act'] == "update") {?>
                                <div class="form-group row">
                                    <label class="col-md-2 col-form-label">발급 현황</label>
                                    <div class="col-md-10">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="card bg-light">
                                                    <div class="card-body text-center">
                                                        <h5 class="card-title">총 발급 수</h5>
                                                        <p class="card-text display-4"><?=number_format($row['ct_download'])?></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="card bg-light">
                                                    <div class="card-body text-center">
                                                        <h5 class="card-title">사용 수</h5>
                                                        <p class="card-text display-4"><?=number_format($row['ct_use'])?></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="card bg-light">
                                                    <div class="card-body text-center">
                                                        <h5 class="card-title">사용 가능 수</h5>
                                                        <p class="card-text display-4"><?=number_format($row['ct_download'] - $row['ct_use'])?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="ct_wdate" class="col-md-2 col-form-label">등록일시</label>
                                    <div class="col-md-10">
                                        <?=DateType($row['ct_wdate'], 6)?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="ct_udate" class="col-md-2 col-form-label">수정일시</label>
                                    <div class="col-md-10">
                                        <?=DateType($row['ct_udate'], 6)?>
                                    </div>
                                </div>
                            <? } ?>

                            <h4 id="rw-fe-advanced" class="margin-top-30">발급 대상 설정</h4>
                            <div class="form-group row">
                                <label for="ct_member_type" class="col-sm-2 col-form-label">회원 등급</label>
                                <div class="col-sm-10">
                                    <select name="ct_member_type[]" id="ct_member_type" class="form-control select2" multiple data-placeholder="모든 회원 등급">
                                        <?php
                                        // 회원 등급 목록 출력
                                        $selected_grades = explode(',', $row['ct_member_type']);
                                        foreach($member_grades as $grade) {
                                            $selected = in_array($grade['idx'], $selected_grades) ? 'selected' : '';
                                            echo '<option value="'.$grade['idx'].'" '.$selected.'>'.$grade['name'].'</option>';
                                        }
                                        ?>
                                    </select>
                                    <small class="form-text text-muted">선택하지 않으면 모든 회원 등급에 발급 가능</small>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="ct_member_limit" class="col-sm-2 col-form-label">회원당 발급 제한</label>
                                <div class="col-sm-10">
                                    <div class="input-group">
                                        <input type="number" name="ct_member_limit" id="ct_member_limit" value="<?=$row['ct_member_limit']?>" class="form-control" min="0" placeholder="제한 없음">
                                        <div class="input-group-append">
                                            <span class="input-group-text">개</span>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">설정 시 회원당 해당 개수만큼만 발급 가능 (0 = 무제한)</small>
                                </div>
                            </div>

                            <div class="form-group row justify-content-center margin-top-30">
                                <button type="button" onclick="history.go(-1);" class="btn btn-outline-secondary mx-1">목록</button>
                                <button type="submit" class="btn btn-secondary">확인</button>
                            </div>
                        </div>
                    </form>

                    <!-- 자바스크립트 -->
                    <script>
                        $(document).ready(function() {
                            // Select2가 적용된 select 요소들을 일반 select로 되돌리기
                            const selectsToRestore = ['#ct_show', '#ct_method', '#ct_target', '#ct_auto_type'];

                            selectsToRestore.forEach(selector => {
                                if($(selector).hasClass('select2-hidden-accessible')) {
                                    $(selector).select2('destroy');

                                    // 중복된 Select2 컨테이너 제거
                                    $('.select2-container').filter(function() {
                                        return $(this).prev('select').attr('id') === selector.substring(1); // #을 제거
                                    }).remove();
                                }
                            });

                            // 폼 처리 객체
                            const formHandler = {
                                init() {
                                    this.loadSellers();
                                    this.initializeValidation();
                                    this.initializeSelect2();
                                    this.setInitialValues();
                                    this.bindEvents();
                                },

                                initializeSelect2() {
                                    if($.fn.select2) {
                                        // 회원 등급 Select2 초기화
                                        $('#ct_member_type').select2({
                                            placeholder: "모든 회원 등급",
                                            width: '100%'
                                        });

                                        // 기본 select 요소들은 Select2를 적용하지 않음
                                        $('.select2').not('#ct_method, #ct_target, #ct_show, #ct_auto_type')
                                            .not('.select2-hidden-accessible').select2();
                                    }
                                },

                                initializeValidation() {
                                    $("#frm_form").validate({
                                        submitHandler: this.handleSubmit,
                                        rules: {
                                            ct_title: {
                                                required: true
                                            },
                                            ct_discount1: {
                                                required: true,
                                                number: true
                                            }
                                        },
                                        messages: {
                                            ct_title: {
                                                required: "쿠폰명을 입력해주세요"
                                            },
                                            ct_discount1: {
                                                required: "할인 금액/비율을 입력해주세요",
                                                number: "숫자만 입력해주세요"
                                            }
                                        },
                                        errorElement: 'span',
                                        errorPlacement: (error, element) => {
                                            error.addClass('invalid-feedback');
                                            element.closest('.col-sm-10').append(error);
                                        },
                                        highlight: (element) => $(element).addClass('is-invalid'),
                                        unhighlight: (element) => $(element).removeClass('is-invalid')
                                    });
                                },

                                handleSubmit(form) {
                                    // 추가 유효성 검사
                                    if (!formHandler.validateAdditionalFields()) {
                                        return false;
                                    }

                                    const formData = new FormData(form);

                                    $.ajax({
                                        url: './coupon_update.php',
                                        type: 'POST',
                                        data: formData,
                                        processData: false,
                                        contentType: false,
                                        beforeSend: () => $('#splinner_modal').modal('show'),
                                        success: (response) => {
                                            $('#splinner_modal').modal('hide');
                                            if(response.success) {
                                                alert(response.message);
                                                if(response.redirect) {
                                                    window.location.href = response.redirect;
                                                }
                                            } else {
                                                alert(response.message);
                                            }
                                        },
                                        error: (xhr, status, error) => {
                                            $('#splinner_modal').modal('hide');
                                            alert('처리 중 오류가 발생했습니다.');
                                        }
                                    });
                                    return false;
                                },

                                validateAdditionalFields() {

                                    // 유효기간 검사
                                    if($('input[name="ct_type1"]:checked').val() == '1') {
                                        if($('#ct_sdate').val() == '') {
                                            alert('시작일을 설정해주세요.');
                                            $('#ct_sdate').focus();
                                            return false;
                                        }

                                        if($('#ct_edate').val() == '') {
                                            alert('종료일을 설정해주세요.');
                                            $('#ct_edate').focus();
                                            return false;
                                        }

                                        if($('#ct_sdate').val() > $('#ct_edate').val()) {
                                            alert('종료일은 시작일보다 이후여야 합니다.');
                                            $('#ct_edate').focus();
                                            return false;
                                        }
                                    } else {
                                        if($('#ct_days').val() == '') {
                                            alert('유효일수를 입력해주세요.');
                                            $('#ct_days').focus();
                                            return false;
                                        }

                                        if(parseInt($('#ct_days').val()) <= 0) {
                                            alert('유효일수는 1일 이상이어야 합니다.');
                                            $('#ct_days').focus();
                                            return false;
                                        }
                                    }

                                    // 할인 유형 검사
                                    if($('input[name="ct_type2"]:checked').val() == '2') {  // 정률 할인인 경우
                                        // 할인율 검증
                                        const discountRate = parseFloat($('#ct_discount1').val());
                                        if(isNaN(discountRate) || discountRate <= 0) {
                                            alert('할인 비율은 0보다 큰 숫자를 입력해야 합니다.');
                                            $('#ct_discount1').focus();
                                            return false;
                                        }

                                        if(discountRate > 100) {
                                            alert('할인 비율은 최대 100%까지 설정 가능합니다.');
                                            $('#ct_discount1').focus();
                                            return false;
                                        }

                                        // 최대 할인 금액 검증
                                        if($('#ct_discount2').val() == '') {
                                            if(!confirm('정률 할인 시 최대 할인 금액을 설정하지 않으면 제한 없이 할인됩니다. 계속하시겠습니까?')) {
                                                $('#ct_discount2').focus();
                                                return false;
                                            }
                                        } else {
                                            const maxDiscount = parseFloat($('#ct_discount2').val());
                                            if(!$.isNumeric($('#ct_discount2').val())) {
                                                alert('최대 할인 금액은 숫자만 입력 가능합니다.');
                                                $('#ct_discount2').focus();
                                                return false;
                                            }

                                            if(maxDiscount <= 0) {
                                                alert('최대 할인 금액은 0보다 큰 값을 입력해야 합니다.');
                                                $('#ct_discount2').focus();
                                                return false;
                                            }
                                        }
                                    }


                                    // 쿠폰 종류에 따른 검사
                                    const couponMethod = $('#ct_method').val();

                                    // 배송비할인인 경우 정액할인만 가능
                                    if(couponMethod == '4' && $('input[name="ct_type2"]:checked').val() != '2') {
                                        alert('배송비할인은 정액할인만 가능합니다.');
                                        return false;
                                    }

                                    // 개별상품할인이나 카테고리할인인 경우 적용 대상 검사
                                    if(couponMethod == '1') { // 개별상품할인
                                        if($('#ct_target').val() == '2') { // 특정 상품
                                            if(!$('#ct_target_product').val() || $('#ct_target_product').val().length === 0) {
                                                alert('적용할 상품을 선택해주세요.');
                                                $('#ct_target_product').focus();
                                                return false;
                                            }
                                        }
                                    } else if(couponMethod == '2') { // 카테고리할인
                                        if($('#ct_target').val() != '1') {
                                            alert('카테고리할인은 특정 카테고리만 선택 가능합니다.');
                                            return false;
                                        }

                                        if(!$('#ct_target_category').val() || $('#ct_target_category').val().length === 0) {
                                            alert('적용할 카테고리를 선택해주세요.');
                                            $('#ct_target_category').focus();
                                            return false;
                                        }
                                    }

                                    // 자동 발급 옵션 검사
                                    if($('#ct_auto_issue').is(':checked')) {
                                        if($('#ct_min_price').val() != '' && !$.isNumeric($('#ct_min_price').val())) {
                                            alert('최소 구매 금액은 숫자만 입력 가능합니다.');
                                            $('#ct_min_price').focus();
                                            return false;
                                        }
                                    }

                                    return true;
                                },

                                setInitialValues() {
                                    // 쿠폰 종류 초기값 설정 (data-initial-value가 있으면 그 값을, 없으면 기본값 3으로 설정)
                                    const methodValue = $('#ct_method').data('initial-value') || '3';
                                    $('#ct_method').val(methodValue);

                                    // 적용 대상 초기값 설정
                                    const targetValue = $('#ct_target').data('initial-value') || '0';
                                    $('#ct_target').val(targetValue);

                                    // 사용여부 초기값 설정
                                    const showValue = $('#ct_show').data('initial-value') || 'Y';
                                    $('#ct_show').val(showValue);

                                    // 자동 발급 유형 초기값 설정
                                    const autoTypeValue = $('#ct_auto_type').data('initial-value') || '1';
                                    $('#ct_auto_type').val(autoTypeValue);

                                    // 라디오 버튼 초기값 설정
                                    ['ct_type1', 'ct_type2'].forEach(field => {
                                        const value = $(`input[name="${field}"]`).data('initial-value');
                                        if(value) {
                                            $(`input[name="${field}"][value="${value}"]`).prop('checked', true);
                                        }
                                    });

                                    // 체크박스 초기값 설정
                                    const autoIssue = $('#ct_auto_issue').data('initial-value');
                                    if(autoIssue === 'Y') {
                                        $('#ct_auto_issue').prop('checked', true);
                                    }

                                    // 초기 상태 업데이트 (모든 필드 초기값 설정 후 호출)
                                    this.updateFormState();

                                    // 쿠폰 종류가 카테고리할인(2)인 경우 카테고리 선택 필드 표시
                                    if($('#ct_method').val() == '2') {
                                        this.updateCouponMethodFields();
                                    }
                                },

                                bindEvents() {
                                    // 유효기간 설정 변경 이벤트
                                    $('input[name="ct_type1"]').change(() => {
                                        this.updatePeriodFields();
                                    });

                                    // 할인 유형 변경 이벤트
                                    $('input[name="ct_type2"]').change(() => {
                                        this.updateDiscountUnit();
                                    });

                                    // 쿠폰 종류 변경 이벤트
                                    $('#ct_method').change(() => {
                                        this.updateCouponMethodFields();
                                        this.updateDiscountTypeOptions();
                                        this.updateTargetFields();
                                    });

                                    // 적용 대상 변경 이벤트
                                    $('#ct_target').change(() => {
                                        this.updateTargetFields();
                                    });

                                    // 자동 발급 체크박스 변경 이벤트
                                    $('#ct_auto_issue').change(() => {
                                        this.updateAutoIssueOptions();
                                    });

                                    // 할인율 입력 시 실시간 검증
                                    $('#ct_discount1').on('input', () => {
                                        if($('input[name="ct_type2"]:checked').val() == '2') {  // 정률 할인(2)인 경우
                                            const value = parseFloat($('#ct_discount1').val());
                                            if(value > 100) {
                                                $('#ct_discount1').val(100);
                                                // 선택적으로 사용자에게 알림
                                                // alert('할인 비율은 최대 100%까지 설정 가능합니다.');
                                            }
                                        }
                                    });

                                    // 쿠폰 코드 생성 버튼 클릭 이벤트 (버튼이 있는 경우)
                                    $('#generate_code').click(() => {
                                        this.generateCouponCode();
                                    });

                                    // 날짜 선택기 초기화 (datepicker가 있다고 가정)
                                    if($.fn.datepicker) {
                                        $('.datepicker').datepicker({
                                            format: 'yyyy-mm-dd',
                                            autoclose: true,
                                            todayHighlight: true
                                        });
                                    }
                                },

                                updateFormState() {
                                    this.updatePeriodFields();
                                    this.updateDiscountUnit();
                                    this.updateCouponMethodFields();
                                    this.updateDiscountTypeOptions();
                                    this.updateAutoIssueOptions();
                                },

                                updatePeriodFields() {
                                    if($('input[name="ct_type1"]:checked').val() == '1') {
                                        $('#date_range').show();
                                        $('#date_days').hide();
                                    } else {
                                        $('#date_range').hide();
                                        $('#date_days').show();
                                    }
                                },

                                updateDiscountUnit() {
                                    if($('input[name="ct_type2"]:checked').val() == '2') {
                                        $('#discount_unit').text('%');
                                        $('#discount_max').show();
                                        // 정률 할인인 경우 최대값을 100으로 제한
                                        $('#ct_discount1').attr('max', '100');
                                        // 현재 값이 100을 초과하면 100으로 조정
                                        if(parseFloat($('#ct_discount1').val()) > 100) {
                                            $('#ct_discount1').val(100);
                                        }
                                    } else {
                                        $('#discount_unit').text('원');
                                        $('#discount_max').hide();
                                        // 정액 할인인 경우 최대값 제한 제거
                                        $('#ct_discount1').removeAttr('max');
                                    }
                                },

                                updateDiscountTypeOptions() {
                                    const couponMethod = $('#ct_method').val();

                                    if(couponMethod == '4') {
                                        // 정액할인(1) 선택 및 정률할인(2) 비활성화
                                        $('input[name="ct_type2"][value="1"]').prop('checked', true);
                                        $('input[name="ct_type2"]').prop('disabled', true);

                                        // 할인 단위 업데이트
                                        this.updateDiscountUnit();

                                        if($('#discount_type_notice').length === 0) {
                                            $('input[name="ct_type2"]').closest('.col-sm-10').append(
                                                '<div id="discount_type_notice" class="text-info mt-2">배송비할인은 정액할인만 가능합니다.</div>'
                                            );
                                        }
                                    } else {
                                        $('input[name="ct_type2"]').prop('disabled', false);
                                        $('#discount_type_notice').remove();
                                    }
                                },

                                updateCouponMethodFields() {
                                    const couponMethod = $('#ct_method').val();
                                    const prevMethod = this.prevCouponMethod || '';

                                    // 쿠폰 종류가 변경되었을 때 선택값 초기화
                                    if(prevMethod !== '' && prevMethod !== couponMethod) {
                                        // 상품 선택값 초기화
                                        if($('#ct_target_product').data('select2')) {
                                            $('#ct_target_product').val(null).trigger('change');
                                        }
                                        // 카테고리 선택값 초기화
                                        if($('#ct_target_category').data('select2')) {
                                            $('#ct_target_category').val(null).trigger('change');
                                        }
                                    }

                                    // 현재 쿠폰 종류 저장
                                    this.prevCouponMethod = couponMethod;

                                    // 쿠폰 종류에 따라 적용 대상 표시 여부 및 옵션 제한
                                    if(couponMethod == '1') {
                                        // 개별상품할인인 경우 적용 대상 섹션 표시
                                        $('.form-group.row').has('#ct_target').show();

                                        // 적용 대상 선택 필드 표시
                                        $('#ct_target').show();
                                        $('#ct_target').next('.select2-container').show();

                                        // 개별상품할인 적용 대상 옵션 설정
                                        this.updateTargetOptions(couponMethod);

                                        // 적용 대상에 따라 하위 필드 표시
                                        this.updateTargetFields();
                                    }
                                    else if(couponMethod == '2') {
                                        // 카테고리할인인 경우

                                        // 적용 대상 섹션은 표시
                                        $('.form-group.row').has('#ct_target').show();

                                        // 적용 대상 선택 필드 숨김
                                        $('#ct_target').hide();
                                        $('#ct_target').next('.select2-container').hide();

                                        // 자동으로 특정 카테고리(1) 값 설정
                                        $('#ct_target').val('1');

                                        // 상품 선택 필드 숨김
                                        $('#target_product').hide();

                                        // 카테고리 선택 필드 표시
                                        $('#target_category').show();

                                        // 카테고리 목록이 아직 로드되지 않았다면 로드
                                        if($('#ct_target_category option').length <= 1) {
                                            this.loadCategories();
                                        } else {
                                            // 이미 로드된 경우 Select2 재초기화
                                            if($.fn.select2) {
                                                // 기존 Select2 인스턴스가 있다면 제거
                                                if($('#ct_target_category').data('select2')) {
                                                    $('#ct_target_category').select2('destroy');
                                                }

                                                // 요소 표시
                                                $('#ct_target_category').show();

                                                // Select2 초기화 (드롭다운이 열리지 않도록 설정)
                                                $('#ct_target_category').select2({
                                                    placeholder: "카테고리 선택",
                                                    allowClear: true,
                                                    width: '100%',
                                                    dropdownAutoWidth: true,
                                                    closeOnSelect: true,
                                                    language: {
                                                        noResults: function() {
                                                            return "검색 결과가 없습니다";
                                                        }
                                                    }
                                                });

                                                // Select2 컨테이너 표시
                                                $('#ct_target_category').next('.select2-container').show();
                                            }
                                        }
                                    }
                                    else {
                                        // 주문금액할인(3)이나 배송비할인(4)인 경우 적용 대상 섹션 숨김
                                        $('.form-group.row').has('#ct_target').hide();
                                        $('#target_category, #target_product').hide();
                                    }
                                },

                                updateTargetOptions(couponMethod) {
                                    // 기존 선택값 저장
                                    const currentValue = $('#ct_target').val();

                                    // 모든 옵션 제거
                                    $('#ct_target').empty();

                                    if(couponMethod == '1') { // 개별상품할인
                                        // 전체 상품(0)과 특정 상품(2) 옵션만 추가
                                        $('#ct_target').append('<option value="0">전체 상품</option>');
                                        $('#ct_target').append('<option value="2">특정 상품</option>');

                                        // 기존 값이 유효하면 유지, 아니면 기본값(0) 설정
                                        if(currentValue == '0' || currentValue == '2') {
                                            $('#ct_target').val(currentValue);
                                        } else {
                                            $('#ct_target').val('0');
                                        }
                                    }
                                    // 카테고리할인(2)인 경우 여기서는 옵션을 설정하지 않음
                                    // 대신 updateCouponMethodFields에서 직접 처리
                                },

                                updateTargetFields() {
                                    const couponMethod = $('#ct_method').val();

                                    // 카테고리할인인 경우 이 함수에서는 처리하지 않음
                                    if(couponMethod == '2') {
                                        return;
                                    }

                                    // 이전 상태를 저장하여 변경 여부를 확인하기 위한 변수
                                    const prevMethod = this.prevCouponMethod || '';
                                    const prevTarget = this.prevCouponTarget || '';

                                    // 현재 상태 저장
                                    this.prevCouponMethod = couponMethod;
                                    this.prevCouponTarget = $('#ct_target').val();

                                    // 모든 대상 필드 숨김
                                    $('#target_category, #target_product').hide();

                                    // 적용대상이 변경되었을 경우 기존 선택된 항목 초기화
                                    if(prevTarget !== '' && prevTarget !== $('#ct_target').val()) {
                                        // 이전 선택이 특정 상품이었다면 상품 선택 초기화
                                        if(prevTarget == '2') {
                                            if($('#ct_target_product').data('select2')) {
                                                $('#ct_target_product').val(null).trigger('change');
                                            }
                                        }
                                        // 이전 선택이 특정 카테고리였다면 카테고리 선택 초기화
                                        else if(prevTarget == '1') {
                                            if($('#ct_target_category').data('select2')) {
                                                $('#ct_target_category').val(null).trigger('change');
                                            }
                                        }
                                    }

                                    if(couponMethod == '1') { // 개별상품할인
                                        // 쿠폰 종류가 변경되었을 때 선택값 초기화
                                        if(prevMethod !== '' && prevMethod !== couponMethod) {
                                            // 상품 선택값 초기화
                                            if($('#ct_target_product').data('select2')) {
                                                $('#ct_target_product').val(null).trigger('change');
                                            }
                                            // 카테고리 선택값 초기화
                                            if($('#ct_target_category').data('select2')) {
                                                $('#ct_target_category').val(null).trigger('change');
                                            }
                                        }

                                        if($('#ct_target').val() == '2') {
                                            // 특정 상품을 선택한 경우
                                            $('#target_product').show();
                                            $('#ct_target_product').show();
                                            $('#ct_target_product').next('.select2-container').show();

                                            // 상품 목록이 아직 로드되지 않았다면 로드
                                            if($('#ct_target_product option').length <= 1) {
                                                // 기존 Select2 인스턴스 제거
                                                if($.fn.select2 && $('#ct_target_product').data('select2')) {
                                                    $('#ct_target_product').select2('destroy');
                                                }

                                                // 상품 로드
                                                this.loadProducts();
                                            }
                                        } else if($('#ct_target').val() == '1') {
                                            // 특정 카테고리를 선택한 경우
                                            $('#target_category').show();
                                            $('#ct_target_category').show();
                                            $('#ct_target_category').next('.select2-container').show();

                                            // 카테고리 목록이 아직 로드되지 않았다면 로드
                                            if($('#ct_target_category option').length <= 1) {
                                                // 기존 Select2 인스턴스 제거
                                                if($.fn.select2 && $('#ct_target_category').data('select2')) {
                                                    $('#ct_target_category').select2('destroy');
                                                }

                                                // 카테고리 로드
                                                this.loadCategories();
                                            }
                                        }
                                    }
                                },

                                updateAutoIssueOptions() {
                                    if($('#ct_auto_issue').is(':checked')) {
                                        $('#auto_issue_options').show();
                                    } else {
                                        $('#auto_issue_options').hide();
                                    }
                                },

                                generateCouponCode() {
                                    $.ajax({
                                        url: './coupon_update.php',
                                        type: 'POST',
                                        data: { act: 'generate_code' },
                                        dataType: 'json',
                                        beforeSend: () => $('#splinner_modal').modal('show'),
                                        success: (response) => {
                                            $('#splinner_modal').modal('hide');
                                            if(response.success) {
                                                $('#ct_code').val(response.coupon_code);
                                                $('#ct_code').removeClass('is-invalid');
                                            } else {
                                                alert(response.message || '쿠폰 코드 생성 중 오류가 발생했습니다.');
                                            }
                                        },
                                        error: () => {
                                            $('#splinner_modal').modal('hide');
                                            alert('처리 중 오류가 발생했습니다.');
                                        }
                                    });
                                },

                                loadSellers() {
                                    // 이미 로딩 중이면 중복 실행 방지
                                    if(this.isLoadingSellers) return;
                                    this.isLoadingSellers = true;

                                    const selectElement = $('#ct_seller_idx');
                                    const loadingContainer = $('#seller_loading_container');

                                    // 기존 옵션 초기화 (선택된 값 유지)
                                    const selectedOption = selectElement.find('option:selected').clone();
                                    selectElement.empty();

                                    // 선택된 값이 있으면 다시 추가
                                    if(selectedOption.length > 0) {
                                        selectElement.append(selectedOption);
                                    }

                                    $.ajax({
                                        url: './coupon_update.php',
                                        type: 'POST',
                                        data: {
                                            act: 'get_sellers',
                                            mb_level: 5 // 판매자 레벨 (필요에 따라 조정)
                                        },
                                        dataType: 'json',
                                        success: (response) => {
                                            console.log('스토어 데이터:', response);

                                            // 숨겨진 상태에서 옵션 추가
                                            if(response.success && response.sellers && response.sellers.length > 0) {
                                                response.sellers.forEach(seller => {
                                                    // 이미 선택된 옵션과 중복되지 않도록 체크
                                                    if(selectedOption.length === 0 || selectedOption.val() !== seller.mt_idx) {
                                                        selectElement.append(
                                                            `<option value="${seller.mt_idx}">${seller.mt_name} (${seller.mt_id})</option>`
                                                        );
                                                    }
                                                });
                                            } else {
                                                if(selectedOption.length === 0) {
                                                    selectElement.append('<option value="">스토어 정보가 없습니다</option>');
                                                }
                                                console.error('스토어 로드 실패:', response.message || '스토어 정보를 불러오는 데 실패했습니다.');
                                            }

                                            // 로딩 컨테이너 제거
                                            loadingContainer.remove();

                                            // 숨겨진 상태에서 Select2 초기화
                                            selectElement.select2({
                                                placeholder: "스토어 선택",
                                                allowClear: true,
                                                width: '100%',
                                                dropdownAutoWidth: true,
                                                closeOnSelect: true,
                                                language: {
                                                    noResults: function() {
                                                        return "검색 결과가 없습니다";
                                                    }
                                                }
                                            });

                                            // 초기화 완료 후 표시
                                            selectElement.show();
                                            selectElement.next('.select2-container').css('opacity', 0).animate({opacity: 1}, 200);

                                            this.isLoadingSellers = false;
                                        },
                                        error: (xhr, status, error) => {
                                            console.error('AJAX 오류:', xhr, status, error);

                                            // 로딩 컨테이너 제거
                                            loadingContainer.remove();

                                            // 에러 메시지와 함께 빈 select 생성
                                            if(selectedOption.length === 0) {
                                                selectElement.append('<option value="">판매자 로드 실패</option>');
                                            }

                                            // 숨겨진 상태에서 Select2 초기화
                                            selectElement.select2({
                                                placeholder: "판매자 선택",
                                                allowClear: true,
                                                width: '100%'
                                            });

                                            // 초기화 완료 후 표시
                                            selectElement.show();
                                            selectElement.next('.select2-container').css('opacity', 0).animate({opacity: 1}, 200);

                                            this.isLoadingSellers = false;
                                        }
                                    });
                                },

                                loadProducts() {
                                    // 이미 로딩 중이면 중복 요청 방지
                                    if(this.isLoadingProducts) return;
                                    this.isLoadingProducts = true;

                                    // 기존 Select2 제거
                                    if($.fn.select2 && $('#ct_target_product').data('select2')) {
                                        $('#ct_target_product').select2('destroy');
                                    }

                                    // 로딩 중 표시를 위한 임시 컨테이너 생성
                                    const loadingContainer = $('<div id="product_loading_container" style="width:100%; padding: 10px; text-align:center;">상품 로딩 중...</div>');
                                    $('#target_product').html(loadingContainer);

                                    // 실제 select 요소 생성 (숨김 상태)
                                    const selectElement = $('<select id="ct_target_product" class="form-control" style="display:none;"></select>');
                                    $('#target_product').append(selectElement);

                                    $.ajax({
                                        url: './coupon_update.php',
                                        type: 'POST',
                                        data: {
                                            act: 'get_products',
                                            show_active: 'Y'
                                        },
                                        dataType: 'json',
                                        success: (response) => {
                                            console.log('상품 데이터:', response);

                                            // 숨겨진 상태에서 옵션 추가
                                            if(response.success && response.products && response.products.length > 0) {
                                                response.products.forEach(product => {
                                                    selectElement.append(
                                                        `<option value="${product.pt_idx}">${product.pt_title}</option>`
                                                    );
                                                });
                                            } else {
                                                selectElement.append('<option value="">상품이 없습니다</option>');
                                                console.error('상품 로드 실패:', response.message || '상품 정보를 불러오는 데 실패했습니다.');
                                            }

                                            // 로딩 컨테이너 제거
                                            loadingContainer.remove();

                                            // 숨겨진 상태에서 Select2 초기화
                                            selectElement.select2({
                                                placeholder: "상품 선택",
                                                allowClear: true,
                                                width: '100%',
                                                dropdownAutoWidth: true,
                                                closeOnSelect: true,
                                                language: {
                                                    noResults: function() {
                                                        return "검색 결과가 없습니다";
                                                    }
                                                }
                                            });

                                            // 초기화 완료 후 표시
                                            selectElement.show();
                                            selectElement.next('.select2-container').css('opacity', 0).animate({opacity: 1}, 200);

                                            this.isLoadingProducts = false;
                                        },
                                        error: (xhr, status, error) => {
                                            console.error('AJAX 오류:', xhr, status, error);

                                            // 로딩 컨테이너 제거
                                            loadingContainer.remove();

                                            // 에러 메시지와 함께 빈 select 생성
                                            selectElement.append('<option value="">상품 로드 실패</option>');

                                            // 숨겨진 상태에서 Select2 초기화
                                            selectElement.select2({
                                                placeholder: "상품 선택",
                                                allowClear: true,
                                                width: '100%'
                                            });

                                            // 초기화 완료 후 표시
                                            selectElement.show();
                                            selectElement.next('.select2-container').css('opacity', 0).animate({opacity: 1}, 200);

                                            this.isLoadingProducts = false;
                                        }
                                    });
                                },

                                loadCategories() {
                                    // 이미 로딩 중이면 중복 요청 방지
                                    if(this.isLoadingCategories) return;
                                    this.isLoadingCategories = true;

                                    // 기존 Select2 제거
                                    if($.fn.select2 && $('#ct_target_category').data('select2')) {
                                        $('#ct_target_category').select2('destroy');
                                    }

                                    // 로딩 중 표시를 위한 임시 컨테이너 생성
                                    const loadingContainer = $('<div id="category_loading_container" style="width:100%; padding: 10px; text-align:center;">카테고리 로딩 중...</div>');
                                    $('#target_category').html(loadingContainer);

                                    // 실제 select 요소 생성 (숨김 상태)
                                    const selectElement = $('<select id="ct_target_category" class="form-control" style="display:none;"></select>');
                                    $('#target_category').append(selectElement);

                                    $.ajax({
                                        url: './coupon_update.php',
                                        type: 'POST',
                                        data: {
                                            act: 'get_categories',
                                            ct_show: 'Y'
                                        },
                                        dataType: 'json',
                                        success: (response) => {
                                            console.log('카테고리 데이터:', response);

                                            // 로딩 컨테이너 제거
                                            loadingContainer.remove();

                                            if(response.success && response.categories) {
                                                // 기존 buildCategoryOptions 함수 호출 (selectElement를 사용)
                                                this.buildCategoryOptions(response.categories, selectElement);
                                            } else {
                                                selectElement.append('<option value="">카테고리가 없습니다</option>');
                                                console.error('카테고리 로드 실패:', response.message || '카테고리 정보를 불러오는 데 실패했습니다.');
                                            }

                                            // 숨겨진 상태에서 Select2 초기화
                                            selectElement.select2({
                                                placeholder: "카테고리 선택",
                                                allowClear: true,
                                                width: '100%',
                                                dropdownAutoWidth: true,
                                                closeOnSelect: true,
                                                language: {
                                                    noResults: function() {
                                                        return "검색 결과가 없습니다";
                                                    }
                                                }
                                            });

                                            // 초기화 완료 후 표시
                                            selectElement.show();
                                            selectElement.next('.select2-container').css('opacity', 0).animate({opacity: 1}, 200);

                                            this.isLoadingCategories = false;
                                        },
                                        error: (xhr, status, error) => {
                                            console.error('AJAX 오류:', xhr, status, error);

                                            // 로딩 컨테이너 제거
                                            loadingContainer.remove();

                                            // 에러 메시지와 함께 빈 select 생성
                                            selectElement.append('<option value="">카테고리 로드 실패</option>');

                                            // 숨겨진 상태에서 Select2 초기화
                                            selectElement.select2({
                                                placeholder: "카테고리 선택",
                                                allowClear: true,
                                                width: '100%'
                                            });

                                            // 초기화 완료 후 표시
                                            selectElement.show();
                                            selectElement.next('.select2-container').css('opacity', 0).animate({opacity: 1}, 200);

                                            this.isLoadingCategories = false;
                                        }
                                    });
                                },

                                buildCategoryOptions(categories, selectElement) {
                                    console.log('카테고리 구성 시작:', categories.length); // 디버깅용

                                    // 카테고리 계층 구조 구성
                                    // 1. 레벨 0(최상위) 카테고리 먼저 추가
                                    const topCategories = categories.filter(cat => cat.ct_level == 0 || cat.ct_pid == null);

                                    // 2. 각 최상위 카테고리와 그 하위 카테고리 추가
                                    topCategories.forEach(topCat => {
                                        selectElement.append(
                                            `<option value="${topCat.ct_idx}">${topCat.ct_name}</option>`
                                        );

                                        // 레벨 1 카테고리 (최상위 카테고리의 직접 하위 카테고리)
                                        const level1Categories = categories.filter(cat => cat.ct_pid == topCat.ct_id && cat.ct_level == 1);
                                        level1Categories.forEach(level1Cat => {
                                            selectElement.append(
                                                `<option value="${level1Cat.ct_idx}">└ ${level1Cat.ct_name}</option>`
                                            );

                                            // 레벨 2 카테고리 (레벨 1 카테고리의 하위 카테고리)
                                            const level2Categories = categories.filter(cat => cat.ct_pid == level1Cat.ct_id && cat.ct_level == 2);
                                            level2Categories.forEach(level2Cat => {
                                                selectElement.append(
                                                    `<option value="${level2Cat.ct_idx}">　└ ${level2Cat.ct_name}</option>`
                                                );
                                            });
                                        });
                                    });

                                    console.log('카테고리 구성 완료'); // 디버깅용
                                },

                                addCategoryOption(category, allCategories, depth) {
                                    // 들여쓰기로 계층 구조 표현
                                    const indent = '　'.repeat(depth);
                                    const prefix = depth > 0 ? indent + '└ ' : '';

                                    // 옵션 추가
                                    $('#category_select').append(
                                        `<option value="${category.ct_id}">${prefix}${category.ct_name}</option>`
                                    );

                                    // 하위 카테고리 찾기 및 재귀적으로 추가
                                    const childCategories = allCategories.filter(cat => cat.ct_pid === category.ct_id);
                                    childCategories.forEach(childCat => {
                                        this.addCategoryOption(childCat, allCategories, depth + 1);
                                    });
                                }

                            };

                            // 초기화
                            formHandler.init();
                        });


                    </script>




                </div>
            </div>

        </div>
    </div>
    <!-- //END PAGE CONTENT CONTAINER -->
<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/foot.inc.php";
?>