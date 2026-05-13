<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$menu_map = [
    '' => ['key' => 1, 'label' => '회원관리'],
    'approval' => ['key' => 2, 'label' => '승인관리'],
    'secession' => ['key' => 3, 'label' => '탈퇴관리']
];
$type = $menu_map[$_GET['type']];
$chk_menu = 1;
$chk_sub_menu = $type['key'];
include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.menu.inc.php";



//if(!$menu_map[$_GET['type']]) {
//  showToast("오류가 발생했습니다.", "error");
//}

?>
  <!-- PAGE CONTENT CONTAINER -->
  <div class="content" id="content">
    <!-- PAGE HEADING -->
    <div class="page-heading">
      <div class="page-heading__container">
        <div class="icon">
          <span class="li-picture3"></span>
        </div>
        <h1 class="title"><?=$type['label']?></h1>
        <p class="caption">
          회원 등록, 수정, 삭제 등을 할 수 있습니다.
        </p>
      </div>
      <nav aria-label="breadcrumb" role="navigation">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="#"><?=$type['label']?></a></li>
          <li class="breadcrumb-item active">회원</li>
        </ol>
      </nav>
    </div>
    <!-- //END PAGE HEADING -->
    <div class="container-fluid">
      <div class="card margin-bottom-0">
        <div class="card-body">
          <form method="POST" name="frm_list" id="frm_list" action="<?=$_SERVER['PHP_SELF']?>" onsubmit="return frm_search_chk(this, event);">
            <input type="hidden" name="act" id="act" value="list" />
            <input type="hidden" name="obj_list" id="obj_list" value="member_list_box" />
            <input type="hidden" name="obj_frm" id="obj_frm" value="frm_list" />
            <input type="hidden" name="obj_uri" id="obj_uri" value="./member_update.php" />
            <input type="hidden" name="obj_pg" id="obj_pg" value="1" />
            <input type="hidden" name="obj_orderby" id="obj_orderby" value="" />
            <input type="hidden" name="obj_order_desc_asc" id="obj_order_desc_asc" value="1" />
            <input type="hidden" name="type" value="<?=$_GET['type']?>" />
            <div class="form-row d-flex justify-content-between">
              <div class="d-flex">
                  <?php if($type['key'] !== 2){?>
                  <select class="form-control searchByStatus" name="sel_search" id="sel_search" tabindex="-1" aria-hidden="true">
                      <option value="all">회원유형</option>
                      <option value="a1.nt_title">전체</option>
                      <option value="a1.nt_content">일반회원</option>
                      <option value="a1.nt_content">딜러회원</option>
                  </select>
                  <select class="form-control searchByStatus" name="sel_search" id="sel_search" tabindex="-1" aria-hidden="true">
                      <option value="all">회원상태</option>
                      <option value="a1.nt_title">전체</option>
                      <option value="a1.nt_content">정상</option>
                      <option value="a1.nt_content">정지</option>
                  </select>
                  <?php }else{?>
                  <select class="form-control searchByStatus" name="sel_search" id="sel_search" tabindex="-1" aria-hidden="true">
                      <option value="all">승인상태</option>
                      <option value="a1.nt_title">전체</option>
                      <option value="a1.nt_content">대기</option>
                      <option value="a1.nt_content">승인</option>
                      <option value="a1.nt_content">거절</option>
                  </select>
                  <?php }?>
                  <select class="form-control searchByStatus" name="sel_search" id="sel_search" tabindex="-1" aria-hidden="true">
                      <option value="all">통합검색</option>
                      <option value="a1.nt_title">제목</option>
                      <option value="a1.nt_content">내용</option>
                  </select>
                  <input type="text" class="form-control searchByText" name="search_txt" id="search_txt" value="<?=$_POST['search_txt']?>"  placeholder="검색어를 입력바랍니다.">
                  <button type="submit" class="btn btn-secondary">검색</button>
              </div>
              <div class="d-flex justify-content-end align-items-center">
                <script type="text/javascript">

                    document.addEventListener("DOMContentLoaded", function () {
                        $(".searchByStatus").select2({
                            placeholder: "통합검색",
                            minimumResultsForSearch: -1,
                            width: '120px',
                        });
                    });

                    function frm_search_chk(f) {
                        if(f.search_txt.value=="") {
                            alert("검색어를 입력바랍니다.");
                            f.search_txt.focus();
                            return false;
                        }

                        return true;
                    }

                    <? if($_POST['sel_search']) { ?>$('#sel_search').val('<?=$_POST['sel_search']?>');<? } ?>

                </script>

                <button type="button" class="btn btn-success margin-right-5" onclick="f_excel_down();">엑셀다운로드</button>
<!--                <button type="button" class="btn btn-info margin-right-5" onclick="location.href='./member_form.php'">회원등록</button>-->
                <button type="button" class="btn btn-gray" onclick="location.href='./notice_list.php'">초기화</button>
              </div>
            </div>
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
                          if(response.success) {
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
                      '.switch input[name="mt_status"]',  // 셀렉터
                      'id',                             // data 속성 이름
                      './member_update.php'    // 업데이트 URL
                  );
              });

              function f_excel_down() {
                  let p1 = $('#sel_search').val();
                  let p2 = $('#search_txt').val();
                  let query = `p1=${encodeURIComponent(p1)}&p2=${encodeURIComponent(p2)}`;


                  hidden_ifrm.document.location.href = `./member_excel_down.php?${query}`;

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
