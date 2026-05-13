<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$menu_map = [
    '' => ['key' => 1, 'label' => '회원관리'],
    'secession' => ['key' => 2, 'label' => '탈퇴관리']
];
$type = $menu_map[$_GET['type']];
$chk_menu = 1;
$chk_sub_menu = $type['key'];
include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.menu.inc.php";

//if(!$menu_map[$_GET['type']]) {
//  showToast("오류가 발생했습니다.", "error");
//}
$search_level = $_POST['search_level'] ?? '';

?>
<!-- PAGE CONTENT CONTAINER -->
<div class="content" id="content">
    <!-- PAGE HEADING -->
    <?php include_once "./pheading.php";?>
    <!-- //END PAGE HEADING -->
    <div class="container-fluid">
        <div class="card margin-bottom-0">
            <div class="card-body">
                <div class="form-row">
                    <div class="col-12">
                        <form method="POST" name="frm_search" id="frm_search" action="<?=$_SERVER['PHP_SELF']?>?type=<?=urlencode($_GET['type'])?>" onsubmit="return frm_search_chk(this, event);" class="row justify-content-between">
                            <?php if($_GET['type'] !== 'approval'){?>
                            <div class="d-flex justify-content-between align-items-center col-12">
                                <div class="d-flex justify-content-start align-items-center">
                                    <label for="date" class="col-form-label mr-2" style="min-width: 60px">회원 유형</label>
                                    <?php
                                    $local_total_class_name = 'btn-secondary';
                                    if ($search_level != '') {
                                        $local_total_class_name = 'btn-outline-secondary';
                                    }
                                    ?>
                                    <button type="button" data-local2="" class="margin-right-5 local2-search-btn btn <?php echo $local_total_class_name?>">
                                        전체
                                    </button>


                                    <?php foreach($arr_member_status as $key=>$value) {?>
                                        <?php
                                        $local_class_name = ($search_level == $key) ? 'btn-secondary' : 'btn-outline-secondary';
                                        ?>
                                        <button type="button" data-local2="<?php echo $key?>" class="margin-right-5 local2-search-btn btn <?php echo $local_class_name?>">
                                            <?php echo $value?>
                                        </button>
                                    <?php }?>
                                    <input type="hidden" name="search_level" id="search_level" value="<?=$search_level?>" />
                                </div>
                            </div>
                            <?php } ?>
                            <div class="d-flex justify-content-between align-items-center col-12">
                                <div class="d-flex justify-content-start align-items-center">
                                    <label for="sel_search" class="col-form-label mr-2" style="min-width: 60px">검색어</label>
                                    <select class="form-control searchByStatus" name="sel_search" id="sel_search" tabindex="-1" aria-hidden="true">
                                        <option value="all">통합검색</option>
                                        <option value="mt_name">이름</option>
                                        <option value="mt_id">아이디</option>
                                        <option value="mt_hp">휴대폰번호</option>
                                    </select>
                                    <input type="text" class="form-control searchByText" name="search_txt" id="search_txt" value="<?=$_POST['search_txt']?>"  placeholder="검색어를 입력바랍니다.">
                                </div>
                                <div class="d-flex justify-content-start align-items-center mt-2 ">
                                    <button type="submit" class="btn btn-secondary margin-right-5">검색</button>
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

                                // ✅ 검색 폼 submit 시: 페이지 새로고침 막고, 검색어만 반영해서 리스트 갱신
                                function frm_search_chk(f, e) {
                                    if (e && e.preventDefault) {
                                        e.preventDefault();
                                    }

                                    // 검색 조건 hidden에 반영
                                    $('#obj_sel_search').val($('#sel_search').val());
                                    $('#obj_search_txt').val($('#search_txt').val());

                                    // 첫 페이지로 초기화
                                    $('#obj_pg').val(1);

                                    // AJAX 리스트 호출
                                    if (typeof f_get_box_mng_list === 'function') {
                                        f_get_box_mng_list();
                                    }

                                    return false; // 기본 submit 막기
                                }

                                $(document).ready(function() {
                                    // ✅ 회원 상태(local-search-btn)는 기존과 동일 동작 (원하면 여기에도 바로 적용 로직 추가 가능)
                                    $('.local-search-btn').on('click', function(){
                                        let local = $(this).attr('data-local')
                                        $('#search_status').val(local);
                                        $('#obj_search_status').val(local);

                                        $('.local-search-btn')
                                            .removeClass('btn-secondary')
                                            .addClass('btn-outline-secondary');
                                        $(this)
                                            .removeClass('btn-outline-secondary')
                                            .addClass('btn-secondary');

                                        // 필요하면 상태 버튼도 즉시 반영하고 싶을 때:
                                        // $('#obj_pg').val(1);
                                        // f_get_box_mng_list();
                                    });

                                    // ✅ 회원유형(local2-search-btn) 버튼: 클릭 시 바로 리스트 갱신
                                    $('.local2-search-btn').on('click', function(){
                                        let local = $(this).attr('data-local2');

                                        // 검색 폼 hidden 값
                                        $('#search_level').val(local);

                                        // 리스트용 hidden 값
                                        $('#obj_search_level').val(local);

                                        // 버튼 스타일 변경
                                        $('.local2-search-btn')
                                            .removeClass('btn-secondary')
                                            .addClass('btn-outline-secondary');
                                        $(this)
                                            .removeClass('btn-outline-secondary')
                                            .addClass('btn-secondary');

                                        // 첫 페이지로 초기화
                                        $('#obj_pg').val(1);

                                        // ✅ 회원유형 선택 즉시 리스트 새로고침
                                        if (typeof f_get_box_mng_list === 'function') {
                                            f_get_box_mng_list();
                                        }
                                    });
                                });

                                <? if($_POST['sel_search']) { ?>$('#sel_search').val('<?= $_POST['sel_search'] ?>');<? } ?>
                            </script>
                        </form>
                    </div>
                </div>

                <form name="frm_list" id="frm_list" onsubmit="return false;">
                    <input type="hidden" name="act" id="act" value="list" />
                    <input type="hidden" name="obj_list" id="obj_list" value="member_list_box" />
                    <input type="hidden" name="obj_frm" id="obj_frm" value="frm_list" />
                    <input type="hidden" name="obj_uri" id="obj_uri" value="./update.php" />
                    <input type="hidden" name="obj_pg" id="obj_pg" value="1" />
                    <input type="hidden" name="obj_orderby" id="obj_orderby" value="" />
                    <input type="hidden" name="obj_order_desc_asc" id="obj_order_desc_asc" value="1" />
                    <input type="hidden" name="type" value="<?=$_GET['type']?>" />
                    <input type="hidden" name="obj_sel_search" id="obj_sel_search" value="<?=$_POST['sel_search']?>" />
                    <input type="hidden" name="obj_search_txt" id="obj_search_txt" value="<?=$_POST['search_txt']?>" />
                    <input type="hidden" name="obj_search_status" id="obj_search_status" value="<?=$_POST['search_status']?>" />
                    <input type="hidden" name="obj_search_level" id="obj_search_level" value="<?=$_POST['search_level']?>" />
                </form>
                <div id="member_list_box"></div>
                <script>
                    $(document).ready(function() {
                        f_get_box_mng_list();
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

                    // Toastr 초기화 함수
                    function initToastr() {
                        toastr.options = {
                            "closeButton": true,
                            "progressBar": true,
                            "positionClass": "toast-bottom-right",
                            "timeOut": "3000",
                            "extendedTimeOut": "1000",
                            "preventDuplicates": true,
                            "showMethod": "fadeIn",
                            "hideMethod": "fadeOut",
                            "showDuration": "300",
                            "hideDuration": "300"
                        };
                    }

                    // 토글 상태 업데이트 함수
                    function updateToggleStatus(url, data, $element) {
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: data,
                            dataType: 'json',
                            success: function(response) {
                                console.log(response)
                                if(response.success) {
                                    let statue = response?.data === 'Y' ? '가능' : '불가능';
                                    $('#statue').text(statue);
                                    toastr.success('노출 상태가 변경되었습니다.');
                                } else {
                                    $element.prop('checked', !$element.is(':checked'));
                                    toastr.error(response.message || '처리 중 오류가 발생했습니다.');
                                }
                            },
                            error: function() {
                                $element.prop('checked', !$element.is(':checked'));
                                toastr.error('서버 통신 오류가 발생했습니다.');
                            }
                        });
                    }

                    // 토글 이벤트 핸들러 설정 함수
                    function initToggleHandler(selector, dataIdAttribute, updateUrl) {
                        $(document).on('change', selector, function() {
                            var $this = $(this);
                            var itemId = $this.closest('tr').data(dataIdAttribute);

                            if(!itemId) {
                                toastr.error('항목 정보를 찾을 수 없습니다.');
                                $this.prop('checked', !$this.is(':checked'));
                                return;
                            }

                            var isChecked = $this.is(':checked');
                            var data = {
                                act: 'updateShow',
                                [dataIdAttribute]: itemId,
                                mt_status: isChecked ? 'Y' : 'N'
                            };

                            updateToggleStatus(updateUrl, data, $this);
                        });
                    }

                    // 사용 예시
                    $(document).ready(function() {
                        // Toastr 초기화
                        initToastr();

                        // 카테고리 토글 이벤트 초기화
                        initToggleHandler(
                            '.switch input[name="del_status"]',  // 셀렉터
                            'id',                             // data 속성 이름
                            './update.php'    // 업데이트 URL
                        );
                    });

                    function f_excel_down() {
                        let p1 = $('#sel_search').val();
                        let p2 = $('#search_txt').val();
                        let p3 = $('#obj_search_level').val();
                        let p4 = '<?=$_GET['type']?>';
                        let p5 = $('#obj_order_desc_asc').val();
                        let query = `p1=${encodeURIComponent(p1)}&p2=${encodeURIComponent(p2)}&p3=${encodeURIComponent(p3)}&p4=${encodeURIComponent(p4)}&p5=${encodeURIComponent(p5)}`;
                        console.log(query);

                        hidden_ifrm.document.location.href = `../member_excel_down.php?${query}`;

                        return false;
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
