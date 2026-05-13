<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu     = 6; // 메뉴 인덱스는 기존 규칙에 맞게
$chk_sub_menu = 1;
include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.menu.inc.php";

// 지역 배열은 head.inc.php나 config에서 불러온다고 가정
// 예시:
// $arr_location = [
//     '서울' => '서울',
//     '경기' => '경기',
//     '인천' => '인천',
//     '부산' => '부산',
// ];

$current_tab = $_GET['tab'] ?? 'shop'; // 탭 유지용
?>
    <!-- PAGE CONTENT CONTAINER -->
    <div class="content" id="content">
        <!-- PAGE HEADING -->
        <?php include_once "./pheading.php";?>
        <!-- //END PAGE HEADING -->

        <div class="container-fluid">
            <div class="card margin-bottom-0">
                <div class="card-body">

                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" id="settleTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link <?=($current_tab === 'shop' ? 'active' : '')?>"
                                   id="shop-tab" data-toggle="tab"
                                   href="#shop-content" role="tab" aria-controls="shop-content"
                                   aria-selected="<?=($current_tab === 'shop' ? 'true' : 'false')?>">
                                    매장 리스트
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?=($current_tab === 'settle' ? 'active' : '')?>"
                                   id="settle-list-tab" data-toggle="tab"
                                   href="#settle-list-content" role="tab" aria-controls="settle-list-content"
                                   aria-selected="<?=($current_tab === 'settle' ? 'true' : 'false')?>">
                                    정산 리스트
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="tab-content" id="settleTabContent">

                        <!-- ========================= -->
                        <!-- ① 매장 리스트 탭          -->
                        <!-- ========================= -->
                        <div class="tab-pane fade <?=($current_tab === 'shop' ? 'show active' : '')?>"
                             id="shop-content"
                             role="tabpanel" aria-labelledby="shop-tab">

                            <!-- 검색 폼 -->
                            <div class="form-row mt-3">
                                <div class="col-12">
                                    <form method="POST"
                                          name="frm_search_shop"
                                          id="frm_search_shop"
                                          action="<?=$_SERVER['PHP_SELF']?>"
                                          onsubmit="return frm_search_shop_chk(this, event);"
                                          class="row justify-content-between">

                                        <!-- 1) 지역 선택 -->
                                        <div class="d-flex justify-content-between align-items-center col-12 mb-2">
                                            <div class="d-flex justify-content-start align-items-center">
                                                <label for="search_location_shop"
                                                       class="col-form-label mr-2"
                                                       style="min-width: 60px">지역</label>
                                                <select class="form-control searchByStatus"
                                                        name="search_location"
                                                        id="search_location_shop"
                                                        style="width: 180px;">
                                                    <option value="all">전체 지역</option>
                                                    <?php if (!empty($arr_location)) {
                                                        $sel_location = $_POST['search_location'] ?? 'all';
                                                        foreach ($arr_location as $key => $value) { ?>
                                                            <option value="<?=htmlspecialchars($value, ENT_QUOTES)?>"
                                                                <?=($sel_location == $value ? 'selected' : '')?>>
                                                                <?=htmlspecialchars($value, ENT_QUOTES)?>
                                                            </option>
                                                        <?php }
                                                    } ?>
                                                </select>
                                            </div>
                                            <div class="d-flex justify-content-start align-items-center">
                                                <label class="col-form-label mr-2"
                                                       style="min-width: 60px">정산상태</label>
                                                <?php
                                                // 버튼: 전체 / 미정산 / 정산완료
                                                $arr_settle_status_shop = [
                                                    'all'       => '전체',
                                                    'unsettled' => '미정산',
                                                    'settled'   => '정산완료',
                                                ];
                                                $current_settle_shop = $_POST['search_settle_status'] ?? 'all';
                                                foreach ($arr_settle_status_shop as $key => $value) {
                                                    $btnClass = ($current_settle_shop === $key)
                                                        ? 'btn-secondary'
                                                        : 'btn-outline-secondary';
                                                    ?>
                                                    <button type="button"
                                                            data-status="<?=$key?>"
                                                            class="btn settle-status-btn-shop mr-1 <?=$btnClass?>">
                                                        <?=$value?>
                                                    </button>
                                                <?php } ?>
                                                <input type="hidden" name="search_settle_status"
                                                       id="search_settle_status_shop"
                                                       value="<?=$current_settle_shop?>"/>
                                            </div>
                                        </div>

                                        <!-- 2) 검색어 + 정산상태 -->
                                        <div class="d-flex justify-content-between align-items-center col-12">

                                            <!-- 검색어 -->
                                            <div class="d-flex justify-content-start align-items-center">
                                                <label for="sel_search_shop" class="col-form-label mr-2"
                                                       style="min-width: 60px">검색어</label>
                                                <select class="form-control searchByStatus"
                                                        name="sel_search"
                                                        id="sel_search_shop"
                                                        style="width: 150px;">
                                                    <?php
                                                    $sel_search_shop = $_POST['sel_search'] ?? 'all';
                                                    ?>
                                                    <option value="all"       <?=$sel_search_shop=='all'?'selected':''?>>통합검색</option>
                                                    <option value="shop_title"<?=$sel_search_shop=='shop_title'?'selected':''?>>매장명</option>
                                                    <option value="biz_no"    <?=$sel_search_shop=='biz_no'?'selected':''?>>사업자번호</option>
                                                    <option value="ceo_name"  <?=$sel_search_shop=='ceo_name'?'selected':''?>>대표자명</option>
                                                </select>
                                                <input type="text"
                                                       class="form-control searchByText"
                                                       name="search_txt"
                                                       id="search_txt_shop"
                                                       value="<?=$_POST['search_txt'] ?? ''?>"
                                                       placeholder="검색어를 입력해주세요.">
                                            </div>
                                            <div class="d-flex justify-content-start align-items-center">
                                                <button type="submit" class="btn btn-secondary margin-right-5">검색</button>
                                                <button type="button" class="btn btn-gray"
                                                        onclick="location.href='./list.php?tab=shop'">
                                                    초기화
                                                </button>
                                            </div>
                                            <!-- 정산 상태 버튼 -->
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- 리스트 폼 -->
                            <form name="frm_list_shop" id="frm_list_shop" onsubmit="return false;">
                                <input type="hidden" name="act" id="act_shop" value="shop_list"/>
                                <input type="hidden" name="obj_list" id="obj_list_shop" value="shop_list_box"/>
                                <input type="hidden" name="obj_frm" id="obj_frm_shop" value="frm_list_shop"/>
                                <input type="hidden" name="obj_uri" id="obj_uri_shop" value="./update.php"/>
                                <input type="hidden" name="obj_pg" id="obj_pg_shop" value="1"/>
                                <input type="hidden" name="obj_limit_num" id="obj_limit_num_shop" value="10"/>
                                <input type="hidden" name="obj_orderby" id="obj_orderby_shop" value=""/>
                                <input type="hidden" name="obj_order_desc_asc" id="obj_order_desc_asc_shop" value="1"/>

                                <!-- 검색 값 복사용 -->
                                <input type="hidden" name="obj_search_location" id="obj_search_location"
                                       value="<?=$_POST['search_location'] ?? 'all'?>"/>
                                <input type="hidden" name="obj_sel_search" id="obj_sel_search"
                                       value="<?=$_POST['sel_search'] ?? 'all'?>"/>
                                <input type="hidden" name="obj_search_txt" id="obj_search_txt"
                                       value="<?=$_POST['search_txt'] ?? ''?>"/>
                                <input type="hidden" name="obj_settle_status" id="obj_settle_status"
                                       value="<?=$current_settle_shop?>"/>
                            </form>

                            <div id="shop_list_box" class="mt-3"></div>
                        </div>

                        <!-- ========================= -->
                        <!-- ② 정산 리스트 탭          -->
                        <!-- ========================= -->
                        <div class="tab-pane fade <?=($current_tab === 'settle' ? 'show active' : '')?>"
                             id="settle-list-content"
                             role="tabpanel" aria-labelledby="settle-list-tab">

                            <!-- 정산 리스트 검색 폼 -->
                            <div class="form-row mt-3">
                                <div class="col-12">
                                    <form method="POST"
                                          name="frm_search_settle"
                                          id="frm_search_settle"
                                          action="<?=$_SERVER['PHP_SELF']?>?tab=settle"
                                          onsubmit="return frm_search_settle_chk(this, event);"
                                          class="row justify-content-between">

                                        <!-- 1) 정산기간 버튼 + 직접 선택 -->
                                        <div class="d-flex justify-content-between align-items-center col-12 mb-2">
                                            <div class="d-flex justify-content-start align-items-center">
                                                <label class="col-form-label mr-2" style="min-width: 60px">정산기간</label>

                                                <?php
                                                // search_day: '', '1','2','3','4'
                                                $search_day_settle = $_POST['search_day'] ?? '';
                                                $dayBtns = [
                                                    ''  => '전체',
                                                    '1' => '오늘',
                                                    '2' => '3일',
                                                    '3' => '7일',
                                                    '4' => '30일',
                                                ];
                                                foreach ($dayBtns as $key => $label) {
                                                    $btnClass = ($search_day_settle === (string)$key)
                                                        ? 'btn-secondary'
                                                        : 'btn-outline-secondary';
                                                    ?>
                                                    <button type="button"
                                                            class="btn mr-1 settle-day-btn <?=$btnClass?>"
                                                            data-day="<?=$key?>">
                                                        <?=$label?>
                                                    </button>
                                                <?php } ?>

                                                <input type="hidden" name="search_day" id="search_day_settle"
                                                       value="<?=$search_day_settle?>"/>

                                                <!-- 직접 기간 선택 -->
                                                <?php
                                                $sdate_settle = $_POST['sdate'] ?? '';
                                                $edate_settle = $_POST['edate'] ?? '';
                                                ?>
                                                <input type="date" class="col-sm-3 form-control ml-3"
                                                       name="sdate" id="sdate_settle"
                                                       value="<?=$sdate_settle?>"/>
                                                <label for="edate_settle" class="col-sm-1 text-center">~</label>
                                                <input type="date" class="col-sm-3 form-control"
                                                       name="edate" id="edate_settle"
                                                       value="<?=$edate_settle?>"/>
                                            </div>
                                            <div class="d-flex justify-content-start align-items-center">
                                                <label class="col-form-label mr-2"
                                                       style="min-width: 60px">정산상태</label>
                                                <?php
                                                $arr_settle_status_list = [
                                                    'all'     => '전체',
                                                    'planned' => '정산예정',
                                                    'done'    => '정산완료',
                                                ];
                                                $current_settle_list = $_POST['settle_status'] ?? 'all';
                                                foreach ($arr_settle_status_list as $key => $value) {
                                                    $btnClass = ($current_settle_list === $key)
                                                        ? 'btn-secondary'
                                                        : 'btn-outline-secondary';
                                                    ?>
                                                    <button type="button"
                                                            data-status="<?=$key?>"
                                                            class="btn settle-status-btn-list mr-1 <?=$btnClass?>">
                                                        <?=$value?>
                                                    </button>
                                                <?php } ?>
                                                <input type="hidden" name="settle_status" id="settle_status_list"
                                                       value="<?=$current_settle_list?>"/>
                                            </div>
                                        </div>

                                        <!-- 2) 검색어 + 정산상태 -->
                                        <div class="d-flex justify-content-between align-items-center col-12">

                                            <!-- 검색어 -->
                                            <div class="d-flex justify-content-start align-items-center">
                                                <label for="sel_search_settle" class="col-form-label mr-2"
                                                       style="min-width: 60px">검색어</label>
                                                <?php
                                                $sel_search_settle = $_POST['sel_search_settle'] ?? 'all';
                                                $search_txt_settle = $_POST['search_txt_settle'] ?? '';
                                                ?>
                                                <select class="form-control searchByStatus"
                                                        name="sel_search_settle"
                                                        id="sel_search_settle"
                                                        style="width: 150px;">
                                                    <option value="all"          <?=$sel_search_settle=='all'?'selected':''?>>통합검색</option>
                                                    <option value="shop_title"   <?=$sel_search_settle=='shop_title'?'selected':''?>>매장명</option>
                                                    <option value="settle_number"<?=$sel_search_settle=='settle_number'?'selected':''?>>정산번호</option>
                                                </select>
                                                <input type="text"
                                                       class="form-control searchByText"
                                                       name="search_txt_settle"
                                                       id="search_txt_settle"
                                                       value="<?=$search_txt_settle?>"
                                                       placeholder="검색어를 입력해주세요.">
                                            </div>

                                            <!-- 정산 상태 버튼 (정산예정 / 정산완료) -->
                                            <div class="d-flex justify-content-start align-items-center">
                                                <button type="submit" class="btn btn-secondary margin-right-5">검색</button>
                                                <button type="button" class="btn btn-gray"
                                                        onclick="location.href='./list.php?tab=settle'">
                                                    초기화
                                                </button>
                                            </div>
                                        </div>

                                    </form>
                                </div>
                            </div>

                            <!-- 정산 리스트 폼 -->
                            <form name="frm_list_settle" id="frm_list_settle" onsubmit="return false;">
                                <input type="hidden" name="act" id="act_settle" value="settle_list"/>
                                <input type="hidden" name="obj_list" id="obj_list_settle" value="settle_list_box"/>
                                <input type="hidden" name="obj_frm" id="obj_frm_settle" value="frm_list_settle"/>
                                <input type="hidden" name="obj_uri" id="obj_uri_settle" value="./update.php"/>
                                <input type="hidden" name="obj_pg" id="obj_pg_settle" value="1"/>
                                <input type="hidden" name="obj_limit_num" id="obj_limit_num_settle" value="10"/>
                                <input type="hidden" name="obj_orderby" id="obj_orderby_settle" value=""/>
                                <input type="hidden" name="obj_order_desc_asc" id="obj_order_desc_asc_settle" value="1"/>

                                <!-- 검색 값 복사용 -->
                                <input type="hidden" name="obj_search_day" id="obj_search_day_settle"
                                       value="<?=$search_day_settle?>"/>
                                <input type="hidden" name="obj_sel_search" id="obj_sel_search_settle"
                                       value="<?=$sel_search_settle?>"/>
                                <input type="hidden" name="obj_search_txt" id="obj_search_txt_settle"
                                       value="<?=$search_txt_settle?>"/>
                                <input type="hidden" name="obj_settle_status" id="obj_settle_status_list"
                                       value="<?=$current_settle_list?>"/>
                                <input type="hidden" name="sdate" id="obj_sdate_settle" value="<?=$sdate_settle?>"/>
                                <input type="hidden" name="edate" id="obj_edate_settle" value="<?=$edate_settle?>"/>
                            </form>

                            <div id="settle_list_box" class="mt-3"></div>
                        </div>

                    </div> <!-- //tab-content -->

                    <div id="statusMessage" class="alert alert-success"
                         style="display:none; position:fixed; bottom:20px; right:50px;"></div>

                    <script>
                        // ===============================
                        //  매장 리스트 검색 submit
                        // ===============================
                        function frm_search_shop_chk(f, e) {
                            if (e) e.preventDefault();

                            // 검색조건 hidden에 복사
                            $('#obj_search_location').val($('#search_location_shop').val());
                            $('#obj_sel_search').val($('#sel_search_shop').val());
                            $('#obj_search_txt').val($('#search_txt_shop').val());
                            $('#obj_settle_status').val($('#search_settle_status_shop').val());

                            f_get_box_mng_list_shop(1, '');
                            return false;
                        }

                        // ===============================
                        //  매장 리스트 로딩 함수
                        // ===============================
                        function f_get_box_mng_list_shop(page, orderby) {
                            const frmId = '#frm_list_shop';
                            const listBoxId = '#shop_list_box';

                            $('#obj_pg_shop').val(page);
                            $('#obj_orderby_shop').val(orderby || '');

                            const data = $(frmId).serialize() + '&' + $('#frm_search_shop').serialize();

                            $.ajax({
                                url: './update.php',
                                method: 'POST',
                                data: data,
                                success: function (html) {
                                    $(listBoxId).html(html);
                                },
                                error: function (xhr, status, error) {
                                    console.error('AJAX Error:', status, error, xhr.responseText);
                                    $(listBoxId).html(
                                        '<div class="alert alert-danger">데이터 로드에 실패했습니다.</div>'
                                    );
                                }
                            });
                        }

                        // ===============================
                        //  정산 리스트 검색 submit
                        //  (직접 기간 + 검색어용)
                        // ===============================
                        function frm_search_settle_chk(f, e) {
                            if (e) e.preventDefault();

                            // 검색조건 hidden에 복사
                            $('#obj_search_day_settle').val($('#search_day_settle').val());
                            $('#obj_sel_search_settle').val($('#sel_search_settle').val());
                            $('#obj_search_txt_settle').val($('#search_txt_settle').val());
                            $('#obj_settle_status_list').val($('#settle_status_list').val());
                            $('#obj_sdate_settle').val($('#sdate_settle').val());
                            $('#obj_edate_settle').val($('#edate_settle').val());

                            f_get_box_mng_list_settle(1, '');
                            return false;
                        }

                        // ===============================
                        //  정산 리스트 로딩 함수
                        // ===============================
                        function f_get_box_mng_list_settle(page, orderby) {
                            const frmId = '#frm_list_settle';
                            const listBoxId = '#settle_list_box';

                            $('#obj_pg_settle').val(page);
                            $('#obj_orderby_settle').val(orderby || '');

                            const data = $(frmId).serialize() + '&' + $('#frm_search_settle').serialize();

                            $.ajax({
                                url: './update.php',
                                method: 'POST',
                                data: data,
                                success: function (html) {
                                    $(listBoxId).html(html);
                                },
                                error: function (xhr, status, error) {
                                    console.error('AJAX Error:', status, error, xhr.responseText);
                                    $(listBoxId).html(
                                        '<div class="alert alert-danger">데이터 로드에 실패했습니다.</div>'
                                    );
                                }
                            });
                        }

                        $(document).ready(function () {

                            // Select2 공통
                            $(".searchByStatus").select2({
                                placeholder: "선택하세요",
                                minimumResultsForSearch: -1,
                                width: 'auto'
                            });

                            // ---------------------------
                            //   매장 리스트: 지역 변경
                            // ---------------------------
                            $('#search_location_shop').on('change', function () {
                                $('#obj_search_location').val($(this).val());
                                f_get_box_mng_list_shop(1, '');
                            });

                            // ---------------------------
                            //   매장 리스트: 정산 상태 버튼
                            // ---------------------------
                            $(document).on('click', '.settle-status-btn-shop', function () {
                                const status = $(this).data('status');
                                $('#search_settle_status_shop').val(status);
                                $('#obj_settle_status').val(status);

                                $('.settle-status-btn-shop')
                                    .removeClass('btn-secondary')
                                    .addClass('btn-outline-secondary');
                                $(this)
                                    .removeClass('btn-outline-secondary')
                                    .addClass('btn-secondary');

                                f_get_box_mng_list_shop(1, '');
                            });

                            // ---------------------------
                            //   정산 리스트: 기간 버튼 (즉시 적용)
                            // ---------------------------
                            $(document).on('click', '.settle-day-btn', function () {
                                const day = $(this).data('day');
                                $('#search_day_settle').val(day);
                                $('#obj_search_day_settle').val(day);

                                // 버튼 스타일 변경
                                $('.settle-day-btn')
                                    .removeClass('btn-secondary')
                                    .addClass('btn-outline-secondary');
                                $(this)
                                    .removeClass('btn-outline-secondary')
                                    .addClass('btn-secondary');

                                // ▶ 바로 리스트 갱신
                                f_get_box_mng_list_settle(1, '');
                            });

                            // 날짜 직접 선택 시, 버튼 선택 해제
                            $('#sdate_settle, #edate_settle').on('change', function () {
                                $('#search_day_settle').val('');
                                $('#obj_search_day_settle').val('');
                                $('.settle-day-btn')
                                    .removeClass('btn-secondary')
                                    .addClass('btn-outline-secondary');
                                // 직접 기간은 검색 버튼 눌렀을 때만 적용
                            });

                            // ---------------------------
                            //   정산 리스트: 상태 버튼 (즉시 적용)
                            // ---------------------------
                            $(document).on('click', '.settle-status-btn-list', function () {
                                const status = $(this).data('status');
                                $('#settle_status_list').val(status);
                                $('#obj_settle_status_list').val(status);

                                $('.settle-status-btn-list')
                                    .removeClass('btn-secondary')
                                    .addClass('btn-outline-secondary');
                                $(this)
                                    .removeClass('btn-outline-secondary')
                                    .addClass('btn-secondary');

                                // ▶ 바로 리스트 갱신
                                f_get_box_mng_list_settle(1, '');
                            });

                            // ---------------------------
                            //   탭 진입 시 리스트 로딩
                            // ---------------------------
                            const currentTab = '<?=$current_tab?>';

                            if (currentTab === 'shop') {
                                f_get_box_mng_list_shop('<?= $_GET['pg'] ? (int)$_GET['pg'] : 1 ?>', '');
                            } else if (currentTab === 'settle') {
                                f_get_box_mng_list_settle('<?= $_GET['pg'] ? (int)$_GET['pg'] : 1 ?>', '');
                            }

                            // 탭 클릭 시 해당 탭의 리스트 다시 로딩
                            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                                const target = $(e.target).attr('href');
                                if (target === '#shop-content') {
                                    f_get_box_mng_list_shop(1, '');
                                    history.replaceState(null, '', '?tab=shop');
                                } else if (target === '#settle-list-content') {
                                    f_get_box_mng_list_settle(1, '');
                                    history.replaceState(null, '', '?tab=settle');
                                }
                            });
                        });
                    </script>

                </div>
            </div>
        </div>
    </div>
    <!-- //END PAGE CONTENT CONTAINER -->
<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/foot.inc.php";
