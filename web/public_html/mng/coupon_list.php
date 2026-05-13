<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu='9';
$chk_sub_menu='1';
include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.menu.inc.php";
//include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.sidebar.inc.php";

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
                    <form method="POST" name="frm_search" id="frm_search" action="<?=$_SERVER['PHP_SELF']?>" onsubmit="return frm_search_chk(this, event);" class="row justify-content-between">
                        <div class="d-flex justify-content-between align-items-center col-12">
                            <div class="d-flex justify-content-start align-items-center">
                                <label for="date" class="col-form-label mr-2" style="min-width: 60px">발급 대상</label>
                                <?php
                                $local_total_class_name = 'btn-secondary';
                                if ($search_level != '') {
                                    $local_total_class_name = 'btn-outline-secondary';
                                }
                                ?>
                                <button type="button" data-local2="" class="margin-right-5 local2-search-btn btn <?php echo $local_total_class_name?>">
                                    전체
                                </button>


                                <?php foreach($arr_member_coupon as $key=>$value) {?>
                                    <?php
                                    $local_class_name = ($search_level == $key) ? 'btn-secondary' : 'btn-outline-secondary';
                                    ?>
                                    <button type="button" data-local2="<?php echo $key?>" class="margin-right-5 local2-search-btn btn <?php echo $local_class_name?>">
                                        <?php echo $value?>
                                    </button>
                                <?php }?>
                                <input type="hidden" name="search_level" id="search_level" value="<?=$search_level?>" />
                            </div>
                            <div class="d-flex justify-content-start align-items-center">
                                <label for="date" class="col-form-label mr-2" style="min-width: 60px">등록일</label>
                                <?php foreach($arr_day as $key=>$value) {?>
                                    <?php
                                    $local_class_name = ($search_day == $key) ? 'btn-secondary' : 'btn-outline-secondary';
                                    ?>
                                    <button type="button" class="btn local2-search-btn mr-2 <?php echo $local_class_name?>" data-day="<?php echo $key?>" ><?php echo $value ?></button>
                                <?php }?>
                                <input type="hidden" name="search_day" id="search_day" value="<?=$search_day?>" />
                                <input type="date" class="col-sm-3 form-control" name="sdate" id="sdate" value="<?=$_POST['sdate']?>" />
                                <label for="edate" class="col-sm-1 text-center">~</label>
                                <input type="date" class="col-sm-3 form-control" name="edate" id="edate" value="<?=$_POST['edate']?>" />
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center col-12">
                            <div class="d-flex justify-content-start align-items-center mt-2">
                                <label for="search_singo" class="col-form-label mr-2" style="min-width: 60px">쿠폰 상태</label>

                                <?php
                                $local_total_class_name = 'btn-secondary';
                                if ($search_status != '') {
                                    $local_total_class_name = 'btn-outline-secondary';
                                }
                                ?>
                                <button type="button" data-local="" class="margin-right-5 local-search-btn btn <?php echo $local_total_class_name?>">
                                    전체
                                </button>


                                <?php foreach($arr_coupon_status as $key=>$value) {?>
                                    <?php
                                    $local_class_name = ($search_status == $key) ? 'btn-secondary' : 'btn-outline-secondary';
                                    ?>
                                    <button type="button" data-local="<?php echo $key?>" class="margin-right-5 local-search-btn btn <?php echo $local_class_name?>">
                                        <?php echo $value?>
                                    </button>
                                <?php }?>
                                <input type="hidden" name="search_status" id="search_status" value="<?=$search_status?>" />
                            </div>
                            <div class="d-flex justify-content-start align-items-center mt-2 ">
                                <button type="submit" class="btn btn-secondary margin-right-5">검색</button>
                                <button type="button" class="btn btn-info margin-right-5" onclick="location.href='./coupon_form.php'">신규등록</button>
                                <button type="button" class="btn btn-gray" onclick="location.href='./list.php'">초기화</button>
                            </div>
                        </div>
                        <script type="text/javascript">
                            document.addEventListener("DOMContentLoaded", function () {
                                $(".searchByStatus").select2({
                                    placeholder: "통합검색",
                                    minimumResultsForSearch: -1,
                                    width: '120px',
                                });
                                $(".search_singo").select2({
                                    placeholder: "선택하세요",
                                    minimumResultsForSearch: -1,
                                });
                            });

                            function frm_search_chk(f) {
                                console.log('f',f);
                                return true;
                            }

                            $(document).ready(function() {
                                $('.local-search-btn').on('click', function(){
                                    let local = $(this).attr('data-local')
                                    $('#search_status').val(local);
                                    $('#obj_search_status').val(local);

                                    $('.local-search-btn').removeClass('btn-secondary').addClass('btn-outline-secondary');
                                    $(this).removeClass('btn-outline-secondary').addClass('btn-secondary');
                                })

                                $('.local2-search-btn').on('click', function(){
                                    let local = $(this).attr('data-local2')
                                    $('#search_level').val(local);
                                    $('#obj_search_level').val(local);

                                    $('.local2-search-btn').removeClass('btn-secondary').addClass('btn-outline-secondary');
                                    $(this).removeClass('btn-outline-secondary').addClass('btn-secondary');
                                })
                            })

                            <? if($_POST['sel_search']) { ?>$('#sel_search').val('<?=$_POST['sel_search']?>');<? } ?>
                        </script>
                    </form>

                    <!-- 게시물 리스트-->
                    <form name="frm_list" id="frm_list" onsubmit="return false;">
                        <input type="hidden" name="act" id="act" value="list" />
                        <input type="hidden" name="obj_list" id="obj_list" value="coupon_list_box" />
                        <input type="hidden" name="obj_frm" id="obj_frm" value="frm_list" />
                        <input type="hidden" name="obj_uri" id="obj_uri" value="./coupon_update.php" />
                        <input type="hidden" name="obj_pg" id="obj_pg" value="1" />
                        <input type="hidden" name="obj_limit_num" id="obj_limit_num" value="10" />
                        <input type="hidden" name="obj_orderby" id="obj_orderby" value="" />
                        <input type="hidden" name="obj_order_desc_asc" id="obj_order_desc_asc" value="1" />
                        <input type="hidden" name="obj_sel_search" id="obj_sel_search" value="<?=$_POST['sel_search']?>" />
                        <input type="hidden" name="obj_search_txt" id="obj_search_txt" value="<?=$_POST['search_txt']?>" />
                    </form>
                    <div id="coupon_list_box"></div>

                    <!-- 상태 메시지 표시 -->
                    <div id="statusMessage" class="alert alert-success" style="display:none; position:fixed; bottom:20px; right:50px;"></div>

                    <script>
                        $(document).ready(function() {
                            //f_get_box_mng_list();
                        });
                        $(document).ready(function () {
                            var history_data = history.state;
                            if(history_data) {
                                f_get_box_mng_list(history_data.page, '');
                            } else {
                                f_get_box_mng_list('<?=$_GET['pg']?>', '');
                            }
                        });

                        <?php if ($_POST['sel_search']) { ?>
                        $('#sel_search').val('<?=$_POST['sel_search']?>');
                        <?php } ?>
                        <?php if ($_POST['sel_mt_login_type']) { ?>
                        $('#sel_mt_login_type').val('<?=$_POST['sel_mt_login_type']?>');
                        <?php } ?>
                        <?php if ($_POST['sel_mt_status']) { ?>
                        $('#sel_mt_status').val('<?=$_POST['sel_mt_status']?>');
                        <?php } ?>
                        <?php if ($_POST['sel_mt_seller']) { ?>
                        $('#sel_mt_seller').val('<?=$_POST['sel_mt_seller']?>');
                        <?php } ?>

                    </script>


                    <script>
                        // 순서 저장
                        function saveSequence(currentPage, itemsPerPage) {
                            const table = document.getElementById('listTable');
                            if (!table) return;

                            const tbody = table.querySelector('tbody');
                            const rows = Array.from(tbody.querySelectorAll('tr[data-id]'));
                            const sequenceData = rows.map((row, index) => ({
                                idx: parseInt(row.dataset.id),
                                sequence: (currentPage - 1) * itemsPerPage + index + 1
                            }));


                            console.log('전송할 데이터:', sequenceData); // 전송 데이터 확인

                            $.ajax({
                                url: './notice_update.php',
                                method: 'POST',
                                data: {
                                    act: 'updateSequence',
                                    data: sequenceData
                                },
                                dataType: 'json',
                                success: function(response) {
                                    if(response.success) {
                                        window.location.reload();
                                    } else {
                                        alert(response.message || '순서 저장에 실패했습니다.');
                                    }
                                },
                                error: function(xhr, status, error) {
                                    // 에러 상세 정보 출력
                                    console.error('Status:', status);
                                    console.error('Error:', error);
                                    console.error('Response:', xhr.responseText);

                                    try {
                                        const errorResponse = JSON.parse(xhr.responseText);
                                        alert(errorResponse.message || '오류가 발생했습니다. 관리자에게 문의해주세요.');
                                    } catch(e) {
                                        alert('서버 응답 처리 중 오류가 발생했습니다: ' + xhr.responseText);
                                    }
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
