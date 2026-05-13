<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu= 3;
$chk_sub_menu= 1;
include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.menu.inc.php";

$search_day    = $_POST['search_day']    ?? '';
$search_status = $_POST['search_status'] ?? 'all';

// 매장 키 (필수)
$sh_idx = (int)($_GET['sh_idx'] ?? 0);
if ($sh_idx <= 0) {
    echo "<script>alert('잘못된 접근입니다. (매장 정보가 없습니다)'); history.back();</script>";
    exit;
}

// 이 매장의 카테고리 목록
$tbl_shop_category = $CFG_TBL['menu']['category'];
$DB->where('sh_idx', $sh_idx);
$DB->orderBy('sc_order', 'asc');
$DB->orderBy('idx', 'asc');
$category_list = $DB->get($tbl_shop_category);
?>
    <div class="content" id="content">
        <?php include_once "./pheading.php";?>
        <div class="container-fluid">
            <div class="card margin-bottom-0">
                <div class="card-body">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active"
                                   id="menu-tab"
                                   data-toggle="tab"
                                   href="#menu-content"
                                   role="tab"
                                   aria-controls="menu-content"
                                   aria-selected="true">메뉴관리</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link"
                                   id="order-tab"
                                   data-toggle="tab"
                                   href="#order-content"
                                   role="tab"
                                   aria-controls="order-content"
                                   aria-selected="false">주문내역</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link"
                                   id="cancel-tab"
                                   data-toggle="tab"
                                   href="#cancel-content"
                                   role="tab"
                                   aria-controls="cancel-content"
                                   aria-selected="false">취소내역</a>
                            </li>
                        </ul>
                    </div>

                    <div class="tab-content" id="tabContent">

                        <!-- ======================= -->
                        <!-- ① 메뉴관리 탭          -->
                        <!-- ======================= -->
                        <div class="tab-pane fade show active"
                             id="menu-content"
                             role="tabpanel"
                             aria-labelledby="menu-tab">

                            <div class="form-row mt-3">
                                <div class="col-12">
                                    <form method="POST"
                                          name="frm_search_menu"
                                          id="frm_search_menu"
                                          action="<?=$_SERVER['PHP_SELF']?>"
                                          onsubmit="return frm_search_chk(this, event, 'menu');"
                                          class="row justify-content-between">

                                        <!-- 카테고리 -->
                                        <div class="d-flex justify-content-between align-items-center col-12 mb-2">
                                            <div class="d-flex justify-content-start align-items-center">
                                                <label for="search_category_menu"
                                                       class="col-form-label mr-2"
                                                       style="min-width: 60px">카테고리</label>
                                                <select class="form-control"
                                                        name="search_category"
                                                        id="search_category_menu"
                                                        style="width: 180px;">
                                                    <option value="all">전체 카테고리</option>
                                                    <?php if (!empty($category_list)) {
                                                        $selected_category = $_POST['search_category'] ?? 'all';
                                                        foreach ($category_list as $cat) { ?>
                                                            <option value="<?=$cat['idx']?>"
                                                                <?=$selected_category == $cat['idx'] ? 'selected' : ''?>>
                                                                <?=htmlspecialchars($cat['sc_title'], ENT_QUOTES)?>
                                                            </option>
                                                        <?php }
                                                    } ?>
                                                </select>
                                            </div>
                                            <!-- 노출상태 + 판매상태 버튼 -->
<!--                                            <div class="d-flex justify-content-start align-items-center">-->
<!---->
<!--                                                 노출상태 -->
<!--                                                <div class="mb-1 d-flex align-items-center">-->
<!--                                                    <label class="col-form-label mr-2" style="min-width: 60px">노출상태</label>-->
<!--                                                    --><?php //$arr_show_status = ['Y' => '노출', 'N' => '미노출']; ?>
<!--                                                    <button type="button"-->
<!--                                                            data-local="all"-->
<!--                                                            class="margin-right-5 local-show-btn-menu btn btn-secondary">-->
<!--                                                        전체-->
<!--                                                    </button>-->
<!--                                                    --><?php //foreach($arr_show_status as $key=>$value) { ?>
<!--                                                        <button type="button"-->
<!--                                                                data-local="--><?php //=$key?><!--"-->
<!--                                                                class="margin-right-5 local-show-btn-menu btn btn-outline-secondary">-->
<!--                                                            --><?php //=$value?>
<!--                                                        </button>-->
<!--                                                    --><?php //} ?>
<!--                                                    <input type="hidden"-->
<!--                                                           name="search_show"-->
<!--                                                           id="search_show_menu"-->
<!--                                                           value="--><?php //=htmlspecialchars($_POST['search_show'] ?? 'all', ENT_QUOTES)?><!--" />-->
<!--                                                </div>-->
<!---->
<!--                                            </div>-->
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center col-12">

                                            <!-- 검색어 (메뉴명만) -->
                                            <div class="d-flex justify-content-start align-items-center">
                                                <label for="search_txt_menu"
                                                       class="col-form-label mr-2"
                                                       style="min-width: 60px">메뉴명</label>
                                                <input type="text"
                                                       class="form-control"
                                                       name="search_txt"
                                                       id="search_txt_menu"
                                                       value="<?=$_POST['search_txt'] ?? ''?>"
                                                       placeholder="메뉴명을 입력바랍니다.">
                                                <button type="submit" class="btn btn-secondary margin-left-5">검색</button>
                                                <button type="button"
                                                        class="btn btn-gray margin-left-5"
                                                        onclick="location.href='./list2.php?sh_idx=<?=$sh_idx?>&tab=menu'">
                                                    초기화
                                                </button>
                                            </div>
                                            <div class="d-flex justify-content-start align-items-center">
                                                <!-- 판매상태 -->
                                                <div class="d-flex align-items-center">
                                                    <label class="col-form-label mr-2" style="min-width: 60px">판매상태</label>
                                                    <?php
                                                    // sm_type enum('Y','N'), 기본값 'Y'
                                                    // 여기서는 Y=판매중, N=판매중지 로 사용
                                                    $arr_sale_status = ['Y' => '판매중', 'N' => '판매중지'];
                                                    ?>
                                                    <button type="button"
                                                            data-local="all"
                                                            class="margin-right-5 local-sale-btn-menu btn btn-secondary">
                                                        전체
                                                    </button>
                                                    <?php foreach($arr_sale_status as $key=>$value) { ?>
                                                        <button type="button"
                                                                data-local="<?=$key?>"
                                                                class="margin-right-5 local-sale-btn-menu btn btn-outline-secondary">
                                                            <?=$value?>
                                                        </button>
                                                    <?php } ?>
                                                    <input type="hidden"
                                                           name="search_sale"
                                                           id="search_sale_menu"
                                                           value="<?=htmlspecialchars($_POST['search_sale'] ?? 'all', ENT_QUOTES)?>" />
                                                </div>
                                            </div>
                                        </div>

                                        <!-- 이 매장 키 -->
                                        <input type="hidden" name="sh_idx" value="<?=$sh_idx?>">
                                    </form>
                                </div>
                            </div>

                            <!-- 메뉴 리스트 폼 -->
                            <form name="frm_list_menu"
                                  id="frm_list_menu"
                                  onsubmit="return false;">
                                <input type="hidden" name="act"                value="list_menu" />
                                <input type="hidden" name="obj_list"           value="menu_list_box" />
                                <input type="hidden" name="obj_frm"            value="frm_list_menu" />
                                <input type="hidden" name="obj_uri"            value="./update.php" />
                                <input type="hidden" name="obj_pg"             value="1" />
                                <input type="hidden" name="obj_limit_num"      value="10" />
                                <input type="hidden" name="obj_orderby"        value="" />
                                <input type="hidden" name="obj_order_desc_asc" value="1" />
                                <input type="hidden" name="sh_idx"             value="<?=$sh_idx?>">
                            </form>
                            <div id="menu_list_box" class="mt-3"></div>
                        </div>


                        <!-- ======================= -->
                        <!-- ② 주문내역 탭           -->
                        <!-- ======================= -->
                        <div class="tab-pane fade" id="order-content" role="tabpanel" aria-labelledby="order-tab">
                            <div class="form-row mt-3">
                                <div class="col-12">
                                    <form method="POST"
                                          name="frm_search_order"
                                          id="frm_search_order"
                                          action="<?=$_SERVER['PHP_SELF']?>"
                                          onsubmit="return frm_search_chk(this, event, 'order');"
                                          class="row justify-content-between">

                                        <!-- 결제일: 오늘/7일/30일 + 직접 선택 -->
                                        <div class="d-flex justify-content-between align-items-center col-12 mb-2">
                                            <div class="d-flex justify-content-start align-items-center">
                                                <label for="sdate_order" class="col-form-label mr-2" style="min-width: 60px">결제일</label>

                                                <div class="btn-group mr-3" role="group" aria-label="결제일 빠른 선택">
                                                    <button type="button"
                                                            class="btn btn-outline-secondary btn-order-day"
                                                            data-day="1">
                                                        오늘
                                                    </button>
                                                    <button type="button"
                                                            class="btn btn-outline-secondary btn-order-day"
                                                            data-day="7">
                                                        7일
                                                    </button>
                                                    <button type="button"
                                                            class="btn btn-outline-secondary btn-order-day"
                                                            data-day="30">
                                                        30일
                                                    </button>
                                                </div>

                                                <!-- 직접 선택 -->
                                                <input type="date" class="col-sm-3 form-control" name="sdate" id="sdate_order" value="" />
                                                <label for="edate_order" class="col-sm-1 text-center">~</label>
                                                <input type="date" class="col-sm-3 form-control" name="edate" id="edate_order" value="" />

                                                <!-- 빠른 선택 값 저장 -->
                                                <input type="hidden" name="search_day" id="search_day_order" value="" />
                                            </div>
                                            <div class="d-flex justify-content-start align-items-center">
                                                <label for="search_type_order" class="col-form-label mr-2" style="min-width: 60px">주문상태</label>
                                                <button type="button"
                                                        data-type="all"
                                                        class="margin-right-5 btn btn-secondary btn-order-type">
                                                    전체
                                                </button>
                                                <button type="button"
                                                        data-type="takeout"
                                                        class="margin-right-5 btn btn-outline-secondary btn-order-type">
                                                    포장
                                                </button>
                                                <button type="button"
                                                        data-type="reserve"
                                                        class="margin-right-5 btn btn-outline-secondary btn-order-type">
                                                    예약
                                                </button>
                                                <button type="button"
                                                        data-type="table"
                                                        class="btn btn-outline-secondary btn-order-type">
                                                    테이블(QR)
                                                </button>
                                                <input type="hidden"
                                                       name="search_order_type"
                                                       id="search_type_order"
                                                       value="all" />
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center col-12">

                                            <!-- 검색어: 통합검색 / 주문번호 / 주문자명 -->
                                            <div class="d-flex justify-content-start align-items-center">
                                                <label for="sel_search_order" class="col-form-label mr-2" style="min-width: 60px">검색어</label>
                                                <select class="form-control"
                                                        name="sel_search"
                                                        id="sel_search_order"
                                                        style="width: 130px;">
                                                    <option value="all">통합검색</option>
                                                    <option value="order_code">주문번호</option>
                                                    <option value="user_name">주문자명</option>
                                                </select>
                                                <input type="text"
                                                       class="form-control margin-left-5"
                                                       name="search_txt"
                                                       id="search_txt_order"
                                                       value=""
                                                       placeholder="주문번호, 주문자명을 입력바랍니다.">
                                            </div>

                                            <!-- 주문 상태(유형): 포장 / 예약 / 테이블(QR) -->
                                            <div class="d-flex justify-content-start align-items-center">
                                                <button type="submit" class="btn btn-secondary margin-right-5">검색</button>
                                                <button type="button"
                                                        class="btn btn-gray"
                                                        onclick="location.href='./list2.php?sh_idx=<?=$sh_idx?>&tab=order'">
                                                    초기화
                                                </button>
                                            </div>
                                        </div>

                                        <!-- 매장 키 -->
                                        <input type="hidden" name="sh_idx" value="<?=$sh_idx?>">
                                    </form>
                                </div>
                            </div>

                            <!-- 주문 리스트 폼 (AJAX용) -->
                            <form name="frm_list_order"
                                  id="frm_list_order"
                                  onsubmit="return false;">
                                <input type="hidden" name="act"                value="list_order" />
                                <input type="hidden" name="obj_list"           value="order_list_box" />
                                <input type="hidden" name="obj_frm"            value="frm_list_order" />
                                <input type="hidden" name="obj_uri"            value="./update.php" />
                                <input type="hidden" name="obj_pg"             value="1" />
                                <input type="hidden" name="obj_limit_num"      value="10" />
                                <input type="hidden" name="obj_orderby"        value="" />
                                <input type="hidden" name="obj_order_desc_asc" value="1" />
                                <input type="hidden" name="sh_idx"             value="<?=$sh_idx?>">
                            </form>

                            <div id="order_list_box" class="mt-3"></div>
                        </div>

                        <!-- ======================= -->
                        <!-- ③ 취소내역 탭           -->
                        <!-- ======================= -->
                        <div class="tab-pane fade" id="cancel-content" role="tabpanel" aria-labelledby="cancel-tab">
                            <div class="form-row mt-3">
                                <div class="col-12">
                                    <form method="POST"
                                          name="frm_search_cancel"
                                          id="frm_search_cancel"
                                          action="<?=$_SERVER['PHP_SELF']?>"
                                          onsubmit="return frm_search_chk(this, event, 'cancel');"
                                          class="row justify-content-between">

                                        <!-- 취소일: 오늘/7일/30일 + 직접 선택 -->
                                        <div class="d-flex justify-content-between align-items-center col-12 mb-2">
                                            <div class="d-flex justify-content-start align-items-center">
                                                <label for="sdate_cancel" class="col-form-label mr-2" style="min-width: 60px">취소일</label>

                                                <div class="btn-group mr-3" role="group" aria-label="취소일 빠른 선택">
                                                    <button type="button"
                                                            class="btn btn-outline-secondary btn-cancel-day"
                                                            data-day="1">
                                                        오늘
                                                    </button>
                                                    <button type="button"
                                                            class="btn btn-outline-secondary btn-cancel-day"
                                                            data-day="7">
                                                        7일
                                                    </button>
                                                    <button type="button"
                                                            class="btn btn-outline-secondary btn-cancel-day"
                                                            data-day="30">
                                                        30일
                                                    </button>
                                                </div>

                                                <!-- 직접 선택 -->
                                                <input type="date" class="col-sm-3 form-control" name="sdate" id="sdate_cancel" value="" />
                                                <label for="edate_cancel" class="col-sm-1 text-center">~</label>
                                                <input type="date" class="col-sm-3 form-control" name="edate" id="edate_cancel" value="" />

                                                <!-- 빠른 선택 값 저장 -->
                                                <input type="hidden" name="search_day" id="search_day_cancel" value="" />
                                            </div>
                                            <!-- 주문 상태(유형): 포장 / 예약 / 테이블(QR) -->
                                            <div class="d-flex justify-content-start align-items-center">
                                                <label for="search_type_cancel" class="col-form-label mr-2" style="min-width: 60px">주문상태</label>
                                                <button type="button"
                                                        data-type="all"
                                                        class="margin-right-5 btn btn-secondary btn-cancel-type">
                                                    전체
                                                </button>
                                                <button type="button"
                                                        data-type="takeout"
                                                        class="margin-right-5 btn btn-outline-secondary btn-cancel-type">
                                                    포장
                                                </button>
                                                <button type="button"
                                                        data-type="reserve"
                                                        class="margin-right-5 btn btn-outline-secondary btn-cancel-type">
                                                    예약
                                                </button>
                                                <button type="button"
                                                        data-type="table"
                                                        class="btn btn-outline-secondary btn-cancel-type">
                                                    테이블(QR)
                                                </button>
                                                <input type="hidden"
                                                       name="search_order_type"
                                                       id="search_type_cancel"
                                                       value="all" />
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center col-12">

                                            <!-- 검색어: 통합검색 / 주문번호 / 주문자명 -->
                                            <div class="d-flex justify-content-start align-items-center">
                                                <label for="sel_search_cancel" class="col-form-label mr-2" style="min-width: 60px">검색어</label>
                                                <select class="form-control"
                                                        name="sel_search"
                                                        id="sel_search_cancel"
                                                        style="width: 130px;">
                                                    <option value="all">통합검색</option>
                                                    <option value="order_code">주문번호</option>
                                                    <option value="user_name">주문자명</option>
                                                </select>
                                                <input type="text"
                                                       class="form-control margin-left-5"
                                                       name="search_txt"
                                                       id="search_txt_cancel"
                                                       value=""
                                                       placeholder="주문번호, 주문자명을 입력바랍니다.">
                                            </div>
                                            <div class="d-flex justify-content-start align-items-center">
                                                <!-- 검색 버튼 눌러야 적용되는 것: 직접 취소일 선택, 검색어 -->
                                                <button type="submit" class="btn btn-secondary margin-right-5">검색</button>
                                                <button type="button"
                                                        class="btn btn-gray"
                                                        onclick="location.href='./list2.php?sh_idx=<?=$sh_idx?>&tab=cancel'">
                                                    초기화
                                                </button>
                                            </div>
                                        </div>

                                        <!-- 매장 키 -->
                                        <input type="hidden" name="sh_idx" value="<?=$sh_idx?>">
                                    </form>
                                </div>
                            </div>

                            <!-- 취소내역 리스트 폼 (AJAX용) -->
                            <form name="frm_list_cancel"
                                  id="frm_list_cancel"
                                  onsubmit="return false;">
                                <input type="hidden" name="act"                value="list_cancel" />
                                <input type="hidden" name="obj_list"           value="cancel_list_box" />
                                <input type="hidden" name="obj_frm"            value="frm_list_cancel" />
                                <input type="hidden" name="obj_uri"            value="./update.php" />
                                <input type="hidden" name="obj_pg"             value="1" />
                                <input type="hidden" name="obj_limit_num"      value="10" />
                                <input type="hidden" name="obj_orderby"        value="" />
                                <input type="hidden" name="obj_order_desc_asc" value="1" />
                                <input type="hidden" name="sh_idx"             value="<?=$sh_idx?>">
                            </form>

                            <div id="cancel_list_box" class="mt-3"></div>
                        </div>
                    </div>

                    <div id="statusMessage"
                         class="alert alert-success"
                         style="display:none; position:fixed; bottom:20px; right:50px;"></div>

                    <!-- 메뉴 상세 모달 -->
                    <div class="modal fade" id="menuDetailModal" tabindex="-1" role="dialog" aria-labelledby="menuDetailModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="menuDetailModalLabel">메뉴 상세 정보</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body" id="menuDetailBody">
                                    로딩중...
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 주문 상세 모달 -->
                    <div class="modal fade" id="orderDetailModal" tabindex="-1" role="dialog" aria-labelledby="orderDetailModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="orderDetailModalLabel">주문 상세 정보</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body" id="orderDetailBody">
                                    로딩중...
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">닫기</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script>
                        // 검색폼 submit → 해당 탭 리스트 로딩
                        function frm_search_chk(f, event, tabName) {
                            if (event) event.preventDefault();
                            const name = tabName || (f.id.replace('frm_search_', '') || 'menu');
                            // 검색 시 항상 1페이지부터
                            f_get_box_mng_list(1, '', name);
                            return false;
                        }

                        // 공통 리스트 로딩 함수
                        function f_get_box_mng_list(page, orderby, tabName) {
                            tabName = tabName || 'menu';

                            let frmId       = '#frm_list_'   + tabName;
                            let listBoxId   = '#'           + tabName + '_list_box';
                            let searchFrmId = '#frm_search_' + tabName;

                            // 페이지/정렬 설정
                            $(frmId).find('input[name="obj_pg"]').val(page);
                            $(frmId).find('input[name="obj_orderby"]').val(orderby || '');

                            // 리스트 폼 + 검색 폼 데이터 합치기
                            let data = $(frmId).serialize() + '&' + $(searchFrmId).serialize();

                            console.log('Load list:', tabName, 'page:', page, 'data:', data);

                            $.ajax({
                                url: './update.php',
                                method: 'POST',
                                data: data,
                                success: function (response) {
                                    $(listBoxId).html(response);
                                },
                                error: function (xhr, status, error) {
                                    console.error("AJAX Error:", status, error);
                                    console.error("Response Text:", xhr.responseText);
                                    $(listBoxId).html(
                                        '<div class="alert alert-danger">데이터 로드에 실패했습니다. (콘솔 확인 필요)</div>'
                                    );
                                }
                            });
                        }

                        $(document).ready(function() {
                            // 기본: 메뉴 탭 1페이지 로드
                            const initialTab = 'menu';
                            f_get_box_mng_list('<?= $_GET["pg"] ? $_GET["pg"] : 1 ?>', '', initialTab);

                            // 탭 변경 시 해당 탭 1페이지 로드
                            $('#myTab a').on('shown.bs.tab', function (e) {
                                const newTab = $(e.target).attr('href')
                                    .replace('#', '')
                                    .replace('-content', '');
                                f_get_box_mng_list(1, '', newTab);
                            });

                            // ============================
                            // 메뉴 탭 - 카테고리 변경 시 즉시 적용
                            // ============================
                            $(document).on('change', '#search_category_menu', function () {
                                f_get_box_mng_list(1, '', 'menu');
                            });

                            // ============================
                            // 메뉴 탭 - 노출상태 버튼 (즉시 적용)
                            // ============================
                            $(document).on('click', '.local-show-btn-menu', function(){
                                let local = $(this).attr('data-local');
                                $('#search_show_menu').val(local);

                                $('.local-show-btn-menu')
                                    .removeClass('btn-secondary')
                                    .addClass('btn-outline-secondary');
                                $(this)
                                    .removeClass('btn-outline-secondary')
                                    .addClass('btn-secondary');

                                // 필터 변경 즉시 리스트 갱신
                                f_get_box_mng_list(1, '', 'menu');
                            });

                            // ============================
                            // 메뉴 탭 - 판매상태 버튼 (즉시 적용)
                            // ============================
                            $(document).on('click', '.local-sale-btn-menu', function(){
                                let local = $(this).attr('data-local');
                                $('#search_sale_menu').val(local);

                                $('.local-sale-btn-menu')
                                    .removeClass('btn-secondary')
                                    .addClass('btn-outline-secondary');
                                $(this)
                                    .removeClass('btn-outline-secondary')
                                    .addClass('btn-secondary');

                                // 필터 변경 즉시 리스트 갱신
                                f_get_box_mng_list(1, '', 'menu');
                            });

                            // 주문/취소 탭 버튼들은 기존 핸들러 그대로 유지
                            $(document).on('click', '.local-search-btn-order', function(){
                                let local = $(this).attr('data-local');
                                $('#search_status_order').val(local);

                                $('.local-search-btn-order')
                                    .removeClass('btn-secondary')
                                    .addClass('btn-outline-secondary');
                                $(this)
                                    .removeClass('btn-outline-secondary')
                                    .addClass('btn-secondary');
                            });

                            $(document).on('click', '.local-search-btn-cancel', function(){
                                let local = $(this).attr('data-local');
                                $('#search_status_cancel').val(local);

                                $('.local-search-btn-cancel')
                                    .removeClass('btn-secondary')
                                    .addClass('btn-outline-secondary');
                                $(this)
                                    .removeClass('btn-outline-secondary')
                                    .addClass('btn-secondary');
                            });

                            // select2는 주문/취소쪽에서 쓰고 있으면 그대로 유지
                            if ($.fn.select2) {
                                $(".searchByStatus").select2({
                                    placeholder: "선택하세요",
                                    minimumResultsForSearch: -1,
                                    width: '120px',
                                });
                            }

                            // ==============================
                            // 메뉴 상세 모달: 버튼 클릭 이벤트
                            // ==============================
                            $(document).on('click', '.btn-menu-detail', function () {
                                const smIdx = $(this).data('idx');
                                if (!smIdx) {
                                    alert('메뉴 정보가 올바르지 않습니다.');
                                    return;
                                }

                                // 모달 초기 메시지
                                $('#menuDetailBody').html('로딩중...');
                                $('#menuDetailModal').modal('show');

                                $.ajax({
                                    url: './update.php',
                                    method: 'POST',
                                    data: {
                                        act: 'menu_detail',
                                        sm_idx: smIdx
                                    },
                                    success: function (html) {
                                        $('#menuDetailBody').html(html);
                                    },
                                    error: function (xhr, status, error) {
                                        console.error('메뉴 상세 로딩 오류:', status, error, xhr.responseText);
                                        $('#menuDetailBody').html(
                                            '<div class="alert alert-danger">메뉴 정보를 불러오는 중 오류가 발생했습니다.</div>'
                                        );
                                    }
                                });
                            });
                        });

                        // 검색 폼 submit 시 호출
                        function frm_search_chk(f, e, tabName) {
                            e.preventDefault(); // 기본 submit 막기
                            // 검색 버튼 눌렀을 때만 적용되는 조건들을 포함해서 1페이지부터 다시 조회
                            f_get_box_mng_list(1, '', tabName);
                            return false;
                        }

                        // 탭별 리스트 Ajax 로딩 함수
                        function f_get_box_mng_list(page, orderby, tabName) {
                            let frmId      = '#frm_list_' + tabName;
                            let listBoxId  = '#' + tabName + '_list_box';
                            let searchForm = '#frm_search_' + tabName;

                            // 페이지, 정렬 값 세팅
                            $(frmId).find('input[name="obj_pg"]').val(page);
                            $(frmId).find('input[name="obj_orderby"]').val(orderby);

                            // 리스트 폼 + 검색 폼 데이터 합치기
                            let data = $(frmId).serialize() + '&' + $(searchForm).serialize();

                            $.ajax({
                                url: './update.php',
                                method: 'POST',
                                data: data,
                                success: function(response) {
                                    $(listBoxId).html(response);
                                },
                                error: function(xhr, status, error) {
                                    console.error("AJAX Error:", status, error);
                                    console.error("Response Text:", xhr.responseText);
                                    $(listBoxId).html('<div class="alert alert-danger">데이터 로드에 실패했습니다. (콘솔 확인 필요)</div>');
                                }
                            });
                        }

                        $(document).ready(function() {
                            // 기본 활성 탭(예: 메뉴) 불러오기
                            const initialTab = '<?=(isset($_GET["tab"]) ? $_GET["tab"] : "menu")?>';
                            f_get_box_mng_list('<?=$_GET['pg'] ? $_GET['pg'] : 1?>', '', initialTab);

                            // 탭 전환 시 해당 탭 첫 페이지 로드
                            $('#myTab a').on('shown.bs.tab', function (e) {
                                const newTab = $(e.target).attr('href')
                                    .replace('#', '')
                                    .replace('-content', '');
                                f_get_box_mng_list(1, '', newTab);
                            });

                            // ==============================
                            // 주문 탭: 결제일 빠른 선택 버튼
                            // ==============================
                            $(document).on('click', '.btn-order-day', function() {
                                $('.btn-order-day').removeClass('btn-secondary').addClass('btn-outline-secondary');
                                $(this).removeClass('btn-outline-secondary').addClass('btn-secondary');

                                const day = $(this).data('day'); // 1 / 7 / 30
                                $('#search_day_order').val(day);

                                // 직접 입력한 날짜는 초기화
                                $('#sdate_order').val('');
                                $('#edate_order').val('');

                                // 빠른 선택은 즉시 적용
                                f_get_box_mng_list(1, '', 'order');
                            });

                            // 직접 날짜 선택 시, 빠른 선택 값 제거
                            $('#sdate_order, #edate_order').on('change', function() {
                                $('#search_day_order').val('');
                                $('.btn-order-day').removeClass('btn-secondary').addClass('btn-outline-secondary');
                            });

                            // ==============================
                            // 주문 탭: 주문 상태(포장/예약/테이블) 버튼
                            // ==============================
                            $(document).on('click', '.btn-order-type', function() {
                                $('.btn-order-type').removeClass('btn-secondary').addClass('btn-outline-secondary');
                                $(this).removeClass('btn-outline-secondary').addClass('btn-secondary');

                                const type = $(this).data('type'); // all / takeout / reserve / table
                                $('#search_type_order').val(type);

                                // 주문 상태 변경은 즉시 적용
                                f_get_box_mng_list(1, '', 'order');
                            });

                            // ==============================
                            // 주문 상세 모달 열기
                            // ==============================
                            $(document).on('click', '.btn-order-info', function() {
                                const idx = $(this).data('idx');
                                if (!idx) return;

                                $('#orderDetailBody').html('로딩중...');
                                $('#orderDetailModal').modal('show');

                                $.ajax({
                                    url: './update.php',
                                    method: 'POST',
                                    data: { act: 'order_detail', idx: idx },
                                    success: function(html) {
                                        $('#orderDetailBody').html(html);
                                    },
                                    error: function(xhr, status, error) {
                                        console.error('order_detail error', status, error);
                                        $('#orderDetailBody').html('<div class="alert alert-danger">주문 상세 정보를 불러오지 못했습니다.</div>');
                                    }
                                });
                            });

                            $(document).on('click', '.btn-cancel-day', function() {
                                $('.btn-cancel-day').removeClass('btn-secondary').addClass('btn-outline-secondary');
                                $(this).removeClass('btn-outline-secondary').addClass('btn-secondary');

                                const day = $(this).data('day'); // 1 / 7 / 30
                                $('#search_day_cancel').val(day);

                                // 직접 입력한 날짜는 초기화
                                $('#sdate_cancel').val('');
                                $('#edate_cancel').val('');

                                // 빠른 선택은 즉시 적용
                                f_get_box_mng_list(1, '', 'cancel');
                            });

                            // 취소일 직접 선택 시, 빠른 선택 값 제거
                            $('#sdate_cancel, #edate_cancel').on('change', function() {
                                $('#search_day_cancel').val('');
                                $('.btn-cancel-day').removeClass('btn-secondary').addClass('btn-outline-secondary');
                            });

                            // ==============================
                            // 취소내역 탭: 주문 상태(포장/예약/테이블) 버튼
                            // ==============================
                            $(document).on('click', '.btn-cancel-type', function() {
                                $('.btn-cancel-type').removeClass('btn-secondary').addClass('btn-outline-secondary');
                                $(this).removeClass('btn-outline-secondary').addClass('btn-secondary');

                                const type = $(this).data('type'); // all / takeout / reserve / table
                                $('#search_type_cancel').val(type);

                                // 주문 상태 변경은 즉시 적용
                                f_get_box_mng_list(1, '', 'cancel');
                            });
                        });
                    </script>

                    <?php /* 순서저장/토글 관련 기존 스크립트 있으면 여기 재사용 */ ?>

                </div>
            </div>
        </div>
    </div>
<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/foot.inc.php";
?>
