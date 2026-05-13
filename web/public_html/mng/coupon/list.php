<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu='9';
$chk_sub_menu='1';
include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.menu.inc.php";

// 발급대상 / 쿠폰상태 / 날짜 버튼에서 사용되는 값들
$search_level  = $_POST['search_level']  ?? '';
$search_status = $_POST['search_status'] ?? '';
$search_day    = $_POST['search_day']    ?? '';
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
                    <li class="breadcrumb-item active">쿠폰리스트</li>
                </ol>
            </nav>
        </div>
        <!-- //END PAGE HEADING -->
        <div class="container-fluid">
            <div class="card margin-bottom-0">
                <div class="card-body">

                    <!-- ===================== -->
                    <!--   검색 / 필터 영역     -->
                    <!-- ===================== -->
                    <form method="POST"
                          name="frm_search"
                          id="frm_search"
                          action="<?=$_SERVER['PHP_SELF']?>"
                          onsubmit="return frm_search_chk(this, event);"
                          class="row justify-content-between">

                        <!-- 1행 : 발급 대상 / 등록일 -->
                        <div class="d-flex justify-content-between align-items-center col-12">
                            <!-- 발급 대상 -->
                            <div class="d-flex justify-content-start align-items-center">
                                <label class="col-form-label mr-2" style="min-width: 60px">발급 대상</label>
                                <?php
                                $local_total_class_name = ($search_level == '') ? 'btn-secondary' : 'btn-outline-secondary';
                                ?>
                                <button type="button"
                                        data-local2=""
                                        class="margin-right-5 local2-search-btn btn <?=$local_total_class_name?>">
                                    전체
                                </button>

                                <?php foreach($arr_member_coupon as $key=>$value) {?>
                                    <?php
                                    $local_class_name = ($search_level == $key) ? 'btn-secondary' : 'btn-outline-secondary';
                                    ?>
                                    <button type="button"
                                            data-local2="<?php echo $key?>"
                                            class="margin-right-5 local2-search-btn btn <?=$local_class_name?>">
                                        <?php echo $value?>
                                    </button>
                                <?php }?>
                                <input type="hidden" name="search_level" id="search_level" value="<?=$search_level?>" />
                            </div>

                            <!-- 등록일(오늘/3일/7일/30일 + 기간 선택) -->
                            <div class="d-flex justify-content-start align-items-center">
                                <label class="col-form-label mr-2" style="min-width: 60px">등록일</label>
                                <?php foreach($arr_day as $key=>$value) {?>
                                    <?php
                                    $local_class_name = ($search_day == $key) ? 'btn-secondary' : 'btn-outline-secondary';
                                    ?>
                                    <button type="button"
                                            class="btn local2-search-btn mr-2 <?=$local_class_name?>"
                                            data-day="<?php echo $key?>">
                                        <?php echo $value ?>
                                    </button>
                                <?php }?>
                                <input type="hidden" name="search_day" id="search_day" value="<?=$search_day?>" />

                                <input type="date" class="col-sm-3 form-control" name="sdate" id="sdate" value="<?=$_POST['sdate']??''?>" />
                                <label for="edate" class="col-sm-1 text-center">~</label>
                                <input type="date" class="col-sm-3 form-control" name="edate" id="edate" value="<?=$_POST['edate']??''?>" />
                            </div>
                        </div>

                        <!-- 2행 : 쿠폰 상태 / 버튼들 -->
                        <div class="d-flex justify-content-between align-items-center col-12">
                            <!-- 쿠폰 상태 -->
                            <div class="d-flex justify-content-start align-items-center mt-2">
                                <label class="col-form-label mr-2" style="min-width: 60px">쿠폰 상태</label>

                                <?php
                                $local_total_class_name = ($search_status == '') ? 'btn-secondary' : 'btn-outline-secondary';
                                ?>
                                <button type="button"
                                        data-local=""
                                        class="margin-right-5 local-search-btn btn <?=$local_total_class_name?>">
                                    전체
                                </button>

                                <?php foreach($arr_coupon_status as $key=>$value) {?>
                                    <?php
                                    $local_class_name = ($search_status == $key) ? 'btn-secondary' : 'btn-outline-secondary';
                                    ?>
                                    <button type="button"
                                            data-local="<?php echo $key?>"
                                            class="margin-right-5 local-search-btn btn <?=$local_class_name?>">
                                        <?php echo $value?>
                                    </button>
                                <?php }?>
                                <input type="hidden" name="search_status" id="search_status" value="<?=$search_status?>" />
                            </div>

                            <!-- 검색 / 등록 / 초기화 -->
                            <div class="d-flex justify-content-start align-items-center mt-2 ">
                                <button type="submit" class="btn btn-secondary margin-right-5">검색</button>
                                <button type="button" class="btn btn-info margin-right-5"
                                        onclick="location.href='./form.php'">신규등록</button>
                                <button type="button" class="btn btn-gray" onclick="location.href='./list.php'">초기화</button>
                            </div>
                        </div>

                    </form>

                    <!-- ===================== -->
                    <!--    리스트 요청 폼      -->
                    <!-- ===================== -->
                    <form name="frm_list" id="frm_list" onsubmit="return false;">
                        <input type="hidden" name="act" id="act" value="list" />
                        <input type="hidden" name="obj_list" id="obj_list" value="coupon_list_box" />
                        <input type="hidden" name="obj_frm" id="obj_frm" value="frm_list" />
                        <input type="hidden" name="obj_uri" id="obj_uri" value="./update.php" />
                        <input type="hidden" name="obj_pg" id="obj_pg" value="1" />
                        <input type="hidden" name="obj_limit_num" id="obj_limit_num" value="10" />
                        <input type="hidden" name="obj_orderby" id="obj_orderby" value="" />
                        <input type="hidden" name="obj_order_desc_asc" id="obj_order_desc_asc" value="1" />

                        <!-- 검색 조건 복사용 hidden -->
                        <input type="hidden" name="obj_sel_search" id="obj_sel_search" value="<?=$_POST['sel_search']??''?>" />
                        <input type="hidden" name="obj_search_txt" id="obj_search_txt" value="<?=$_POST['search_txt']??''?>" />

                        <input type="hidden" name="obj_search_level"  id="obj_search_level"  value="<?=$search_level?>" />
                        <input type="hidden" name="obj_search_status" id="obj_search_status" value="<?=$search_status?>" />
                        <input type="hidden" name="obj_search_day"    id="obj_search_day"    value="<?=$search_day?>" />
                        <input type="hidden" name="obj_sdate"         id="obj_sdate"         value="<?=$_POST['sdate']??''?>" />
                        <input type="hidden" name="obj_edate"         id="obj_edate"         value="<?=$_POST['edate']??''?>" />
                    </form>

                    <div id="coupon_list_box"></div>

                    <!-- 상태 메시지 표시 -->
                    <div id="statusMessage" class="alert alert-success" style="display:none; position:fixed; bottom:20px; right:50px;"></div>

                    <!-- ===================== -->
                    <!--   JS : 리스트/필터     -->
                    <!-- ===================== -->
                    <script type="text/javascript">
                        // 검색 버튼 클릭 시
                        function frm_search_chk(f, e) {
                            if (e) e.preventDefault();

                            // 검색어, 날짜 등 입력값을 obj_* hidden 에 복사
                            $('#obj_sel_search').val($('#sel_search').val());
                            $('#obj_search_txt').val($('#search_txt').val());

                            $('#obj_search_day').val($('#search_day').val());
                            $('#obj_sdate').val($('#sdate').val());
                            $('#obj_edate').val($('#edate').val());

                            f_get_coupon_list(1, '');
                            return false;
                        }

                        // 리스트 로딩
                        function f_get_coupon_list(page, orderby) {
                            $('#obj_pg').val(page);
                            $('#obj_orderby').val(orderby || '');

                            const data = $('#frm_list').serialize();

                            $.ajax({
                                url: './update.php',
                                method: 'POST',
                                data: data,
                                success: function (res) {
                                    console.log(res);
                                    $('#coupon_list_box').html(res);
                                },
                                error: function (xhr, status, error) {
                                    console.error('AJAX Error:', status, error, xhr.responseText);

                                    // 에러가 나도 테이블 뼈대는 유지하고, 바디에만 "현재 등록된 쿠폰이 없습니다." 노출
                                    const emptyTableHtml = `
                <div class="table-responsive margin-top-20">
                    <table class="table table-striped table-bordered margin-bottom-20" id="listTable" style="min-width: 900px">
                        <thead class="thead-dark">
                            <tr>
                                <th class="text-center" style="width:80px;">번호</th>
                                <th class="text-center">쿠폰명</th>
                                <th class="text-center" style="width:120px;">발급대상</th>
                                <th class="text-center" style="width:150px;">쿠폰 할인</th>
                                <th class="text-center" style="width:140px;">최소 주문금액</th>
                                <th class="text-center" style="width:180px;">사용 기간</th>
                                <th class="text-center" style="width:100px;">사용 유무</th>
                                <th class="text-center" style="width:140px;">발급일시</th>
                                <th class="text-center" style="width:150px;">관리</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="9" class="text-center"><b>현재 등록된 쿠폰이 없습니다.</b></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            `;

                                    $('#coupon_list_box').html(emptyTableHtml);
                                }
                            });
                        }

                        $(document).ready(function() {

                            // 발급 대상 / 상태 select2 (혹시 쓰고있으면)
                            $(".searchByStatus").select2({
                                placeholder: "통합검색",
                                minimumResultsForSearch: -1,
                                width: '120px',
                            });

                            // ✅ 쿠폰 상태 버튼 : 클릭 시 즉시 필터 적용
                            $('.local-search-btn').on('click', function(){
                                let local = $(this).attr('data-local');

                                $('#search_status').val(local);
                                $('#obj_search_status').val(local);

                                $('.local-search-btn')
                                    .removeClass('btn-secondary')
                                    .addClass('btn-outline-secondary');

                                $(this)
                                    .removeClass('btn-outline-secondary')
                                    .addClass('btn-secondary');

                                f_get_coupon_list(1, '');
                            });

                            // ✅ 발급 대상 / 등록일 버튼 : 클릭 시 즉시 필터 적용
                            $('.local2-search-btn').on('click', function(){
                                let level = $(this).attr('data-local2');
                                let day   = $(this).attr('data-day');

                                // 발급 대상 버튼일 때
                                if (typeof level !== 'undefined') {
                                    $('#search_level').val(level);
                                    $('#obj_search_level').val(level);

                                    $('.local2-search-btn[data-local2]')
                                        .removeClass('btn-secondary')
                                        .addClass('btn-outline-secondary');
                                    $(this)
                                        .removeClass('btn-outline-secondary')
                                        .addClass('btn-secondary');

                                    f_get_coupon_list(1, '');
                                }

                                // 등록일(오늘/3일/7일/30일) 버튼일 때
                                if (typeof day !== 'undefined') {
                                    $('#search_day').val(day);
                                    $('#obj_search_day').val(day);

                                    // 기간 버튼 스타일 토글
                                    $('.local2-search-btn[data-day]')
                                        .removeClass('btn-secondary')
                                        .addClass('btn-outline-secondary');
                                    $(this)
                                        .removeClass('btn-outline-secondary')
                                        .addClass('btn-secondary');

                                    // 날짜 직접 선택은 비워주고, 버튼 기준으로만 검색
                                    $('#sdate').val('');
                                    $('#edate').val('');
                                    $('#obj_sdate').val('');
                                    $('#obj_edate').val('');

                                    f_get_coupon_list(1, '');
                                }
                            });

                            // 최초 로딩
                            var initPage = '<?= $_GET['pg'] ? (int)$_GET['pg'] : 1 ?>';
                            f_get_coupon_list(initPage, '');
                        });

                        function coupon_detail(ct_idx) {
                            var url = 'form.php?act=update&ct_idx=' + encodeURIComponent(ct_idx);
                            location.href = url;
                        }

                        function coupon_delete(ct_idx) {
                            if (!confirm('해당 쿠폰을 삭제하시겠습니까?\n삭제 후에는 복구할 수 없습니다.')) {
                                return;
                            }

                            $.ajax({
                                url: 'update.php',       // 실제 쿠폰 update 처리 파일 경로
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    act: 'delete',
                                    ct_idx: ct_idx
                                },
                                success: function (res) {
                                    if (res.success) {
                                        alert('쿠폰이 삭제되었습니다.');

                                        // 리스트 다시 갱신
                                        if (typeof f_get_coupon_list === 'function') {
                                            // 현재 페이지 유지해서 다시 조회 (obj_pg hidden 값 쓰는 경우)
                                            var pg = $('#obj_pg').val() || 1;
                                            f_get_coupon_list(pg);
                                        } else {
                                            location.reload();
                                        }
                                    } else {
                                        alert(res.message || '삭제 중 오류가 발생했습니다.');
                                    }
                                },
                                error: function (xhr, status, error) {
                                    console.error(error);
                                    alert('서버 통신 중 오류가 발생했습니다.');
                                }
                            });
                        }
                    </script>

                </div>
            </div>

        </div>
    </div>
    <!-- //END PAGE CONTENT CONTAINER -->
<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/foot.inc.php";
?>
