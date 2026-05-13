<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu = isset($menu_map[$_GET['sel_lv']]) ? $menu_map[$_GET['sel_lv']] : '3';
$chk_sub_menu='1';
include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.menu.inc.php";
//include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.sidebar.inc.php";


$row = get_mem_info('idx', $_GET['mt_idx']);

$sql = "
      SELECT b.idx, b.bm_name, mb.idx as my_badge_idx
      FROM member_badge_t mb
      LEFT JOIN badge_master_t b ON mb.badge_idx = b.idx
      WHERE mb.mt_idx = ?
        ORDER BY b.w_order DESC
  ";

$badges = $DB->rawQuery($sql, [$row['idx']]);

$_act = "badge";
$_act_txt = " 뱃지";
?>
<!-- PAGE CONTENT CONTAINER -->
<div class="content" id="content">
  <!-- PAGE HEADING -->
  <div class="page-heading">
    <div class="page-heading__container">
      <div class="icon">
        <span class="li-picture3"></span>
      </div>
      <h1 class="title"><?=$arr_mt_level[$_GET['sel_lv']]?>회원</h1>
      <p class="caption">
        뱃지 정보를 볼 수 있습니다.
      </p>
    </div>
    <nav aria-label="breadcrumb" role="navigation">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="#"><?=$arr_mt_level[$_GET['sel_lv']]?>회원관리</a></li>
        <li class="breadcrumb-item active"><?=$arr_mt_level[$_GET['sel_lv']]?>뱃지</li>
      </ol>
    </nav>
  </div>
  <!-- //END PAGE HEADING -->
  <div class="container-fluid">

    <div class="row">
      <div class="col-12 col-lg-6"><div class="user user--rounded user--bordered user--lg margin-bottom-20"><img src="<?php echo $row['profile']?>"><div class="user__name"><strong><?php echo $row['mt_name']?></strong><br><span class="text-muted"><?php echo $row['mt_email']?></span></div></div></div>
    </div>

    <div class="card margin-bottom-0">

      <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
          <li class="nav-item"><a class="nav-link active" id="member-tab-1" data-toggle="tab" href="#member-1" role="tab" aria-controls="home" aria-selected="true">목록 (<?php echo count($badges)?>)</a></li>


        </ul>
      </div>
      <div class="card-body">
        <form method="post" name="frm_form" id="frm_form" action="./member_update.php" target="hidden_ifrm" enctype="multipart/form-data">
          <input type="hidden" name="act" id="act" value="<?=$_act?>" />
          <input type="hidden" name="mt_idx" id="mt_idx" value="<?=$row['mt_idx']?>" />

          <div class="tab-content margin-top-15" id="myTabContent">
            <div class="tab-pane fade show active" id="member-1" role="tabpanel" aria-labelledby="member-tab-1">

              <div class="row">
                <?php
                if($badges):
                  foreach($badges as $badge) {
                    ?>
                    <div class="col-sm-6 col-md-2 mb-2">
                      <div class="user user--rounded user--bordered user--xlg">
                        <img src="<?php echo CDN_IMG_URL?>/img_badge_example.png" />
                        <div class="user__name">
                          <strong><?php echo $badge['bm_name']?></strong><br>
                          <button class="btn btn-danger btn-xs btn-badge-remove" data-idx="<?=$badge['my_badge_idx']?>" data-my-idx="<?=$row['mt_idx']?>">삭제</button>
                        </div>
                      </div>
                    </div>
                    <?php
                  }
                else:
                  ?>
                  <div class="col-12">
                    <p class="text-center font-weight-bold">자료가 없습니다.</p>
                  </div>

                <?php endif?>
              </div>

            </div>



          </div>
          <div class="form-group row justify-content-center margin-top-30">
            <a href="./member_list.php?<?php echo $_query_str?>" class="btn btn-outline-secondary mx-1" >목록</a>

          </div>
        </form>
      </div>

      <script>



          $(document).ready(function() {
              // 탭 관련 기능
              const tabHandler = {
                  init() {
                      this.initializeTab();
                      this.bindTabEvents();
                  },
                  initializeTab() {
                      const urlParams = new URLSearchParams(window.location.search);
                      const tabParam = urlParams.get('tab');

                      if (tabParam) {
                          this.activateTab(tabParam);
                      }
                  },
                  bindTabEvents() {
                      $('a[data-toggle="tab"]').on('shown.bs.tab', (e) => {
                          const tabId = $(e.target).attr('id');
                          this.updateUrl(tabId);
                      });
                  },
                  activateTab(tabParam) {
                      $('#' + tabParam).tab('show');
                      $('.nav-link').removeClass('active');
                      $('#' + tabParam).addClass('active');
                      $('.tab-pane').removeClass('show active');
                      $(('#' + tabParam.replace('-tab', '')).replace('-1', '-1')).addClass('show active');
                  },
                  updateUrl(tabId) {
                      const newUrl = new URL(window.location.href);
                      newUrl.searchParams.set('tab', tabId);
                      window.history.pushState({}, '', newUrl);
                  }
              };
              tabHandler.init();
          });


          // 제거 기능
          $(document).on('click', '.btn-badge-remove', function() {
              if(!confirm('획득한 뱃지를 제거 하시겠습니까?')) return;


              let badge_idx = $(this).data('idx');
              let my_idx = $(this).data('my-idx');


              const formData = new FormData();
              formData.append('act', 'badge_remove');
              formData.append('badge_idx', badge_idx);
              formData.append('my_idx', my_idx);

              $.ajax({
                  url: './member_update.php',
                  type: 'POST',
                  dataType: 'json',
                  data: formData,
                  processData: false,
                  contentType: false,
                  beforeSend: () => $('#splinner_modal').modal('show'),
                  success: function(response) {
                      $('#splinner_modal').modal('hide');
                      console.log(response)
                      if(response.success) {
                          app.toastr.showSuccess(response.message, './member_badge.php?<?php echo $_query_str?>');
                      } else {
                          app.toastr.showError(response.message);
                      }
                  },
                  error: (xhr, status, error) => {
                      $('#splinner_modal').modal('hide');
                      app.toastr.showError(response.message);
                  }
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
