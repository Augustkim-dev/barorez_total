<?php
// /mng/shop/list.php (예시 경로)

include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu = 3;
$chk_sub_menu = 1;
include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.menu.inc.php";

$search_day    = $_POST['search_day']    ?? '';
$search_status = $_POST['search_status'] ?? 'all'; // 지역
$search_txt    = $_POST['search_txt']    ?? '';
$sel_search    = $_POST['sel_search']    ?? 'all'; // 통합검색/매장명/대표자명
?>

    <!-- PAGE CONTENT CONTAINER -->
    <div class="content" id="content">
        <!-- PAGE HEADING -->
        <?php include_once "./pheading.php";?>
        <!-- //END PAGE HEADING -->

        <div class="container-fluid">
            <div class="card margin-bottom-0">
                <div class="card-body">

                    <!-- =============================== -->
                    <!-- 검색 영역 -->
                    <!-- =============================== -->
                    <div class="form-row">
                        <div class="col-12">
                            <form method="POST"
                                  name="frm_search"
                                  id="frm_search"
                                  action="<?=$_SERVER['PHP_SELF']?>"
                                  onsubmit="return frm_search_chk(this, event);"
                                  class="row justify-content-between">

                                <!-- 지역 선택 -->
                                <div class="d-flex justify-content-between align-items-center col-12 mb-2">
                                    <div class="d-flex justify-content-start align-items-center">
                                        <label for="search_status_select"
                                               class="col-form-label mr-2"
                                               style="min-width: 60px">지역</label>
                                        <select class="form-control"
                                                name="search_status"
                                                id="search_status_select"
                                                tabindex="-1"
                                                aria-hidden="true">
                                            <option value="all">전체 지역</option>
                                            <?php foreach($arr_location as $key => $value) { ?>
                                                <option value="<?=$key?>" <?=$search_status == $key ? 'selected' : ''?>>
                                                    <?=$value?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- 검색어 -->
                                <div class="d-flex justify-content-between align-items-center col-12">
                                    <div class="d-flex justify-content-start align-items-center">
                                        <label for="sel_search_field"
                                               class="col-form-label mr-2"
                                               style="min-width: 60px">검색어</label>
                                        <select class="form-control"
                                                name="sel_search"
                                                id="sel_search_field"
                                                tabindex="-1"
                                                aria-hidden="true"
                                                style="max-width: 140px;">
                                            <option value="all"     <?=$sel_search=='all'     ? 'selected' : ''?>>통합검색</option>
                                            <option value="name"    <?=$sel_search=='name'    ? 'selected' : ''?>>매장명</option>
                                            <option value="rt_name" <?=$sel_search=='rt_name' ? 'selected' : ''?>>대표자명</option>
                                        </select>
                                        <input type="text"
                                               class="form-control ml-2"
                                               name="search_txt"
                                               id="search_txt"
                                               value="<?=htmlspecialchars($search_txt, ENT_QUOTES)?>"
                                               placeholder="검색어를 입력바랍니다.">
                                    </div>

                                    <div class="d-flex justify-content-start align-items-center mt-2">
                                        <button type="submit" class="btn btn-secondary margin-right-5">검색</button>
                                        <button type="button" class="btn btn-gray" onclick="location.href='./list.php'">초기화</button>
                                    </div>
                                </div>

                                <script type="text/javascript">
                                    // 검색 Submit
                                    function frm_search_chk(f, event) {
                                        if (event) event.preventDefault();

                                        // 검색조건을 hidden 필드에 반영
                                        $('#obj_sel_search').val($('#sel_search_field').val());
                                        $('#obj_search_txt').val($('#search_txt').val());
                                        $('#obj_search_status').val($('#search_status_select').val() || 'all');
                                        $('#obj_pg').val(1);

                                        // Ajax 리스트 호출
                                        f_get_box_mng_list(1, '');
                                        return false;
                                    }
                                </script>
                            </form>
                        </div>
                    </div>

                    <!-- =============================== -->
                    <!-- 리스트용 Hidden Form -->
                    <!-- =============================== -->
                    <form name="frm_list" id="frm_list" onsubmit="return false;">
                        <input type="hidden" name="act"                   id="act"                   value="list" />
                        <input type="hidden" name="obj_list"              id="obj_list"              value="notice_list_box" />
                        <input type="hidden" name="obj_frm"               id="obj_frm"               value="frm_list" />
                        <input type="hidden" name="obj_uri"               id="obj_uri"               value="./update.php" />
                        <input type="hidden" name="obj_pg"                id="obj_pg"                value="1" />
                        <input type="hidden" name="obj_limit_num"         id="obj_limit_num"         value="10" />
                        <input type="hidden" name="obj_orderby"           id="obj_orderby"           value="" />
                        <input type="hidden" name="obj_order_desc_asc"    id="obj_order_desc_asc"    value="1" />

                        <!-- 검색 조건들 -->
                        <input type="hidden" name="obj_sel_search"        id="obj_sel_search"        value="<?=$sel_search?>" />
                        <input type="hidden" name="obj_search_txt"        id="obj_search_txt"        value="<?=htmlspecialchars($search_txt, ENT_QUOTES)?>" />
                        <input type="hidden" name="obj_search_status"     id="obj_search_status"     value="<?=$search_status?>" />
                        <input type="hidden" name="obj_search_day"        id="obj_search_day"        value="<?=$search_day?>" />
                        <input type="hidden" name="sdate"                 id="sdate"                 value="<?=$_POST['sdate'] ?? ''?>" />
                        <input type="hidden" name="edate"                 id="edate"                 value="<?=$_POST['edate'] ?? ''?>" />
                    </form>

                    <!-- Ajax로 리스트가 그려질 영역 -->
                    <div id="notice_list_box"></div>

                </div>
            </div>
        </div>
    </div>
    <!-- //END PAGE CONTENT CONTAINER -->

    <script type="text/javascript">
        $(document).ready(function () {
            // select2 사용 시
            if ($.fn.select2) {
                $("#search_status_select").select2({
                    placeholder: "전체 지역",
                    minimumResultsForSearch: -1,
                    width: '140px',
                });
                $("#sel_search_field").select2({
                    placeholder: "통합검색",
                    minimumResultsForSearch: -1,
                    width: '140px',
                });
            }

            // 최초 진입 시 페이지 복원 or 1페이지
            var history_data = history.state;
            if (history_data && history_data.page) {
                f_get_box_mng_list(history_data.page, '');
            } else {
                f_get_box_mng_list('<?= $_GET['pg'] ? $_GET['pg'] : 1 ?>', '');
            }

            // 지역 변경 시 바로 적용
            $('#search_status_select').on('change', function () {
                var val = $(this).val() || 'all';
                $('#obj_search_status').val(val);
                $('#obj_pg').val(1);
                f_get_box_mng_list(1, '');
            });
        });
    </script>

<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/foot.inc.php";
?>
