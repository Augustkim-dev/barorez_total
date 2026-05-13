<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu = isset($menu_map[$_GET['sel_lv']]) ? $menu_map[$_GET['sel_lv']] : '3';
$chk_sub_menu='1';
include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.menu.inc.php";
//include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.sidebar.inc.php";


$row = get_mem_info('idx', $_GET['mt_idx']);

$res = getUnfollowingList($row['idx']);
$list1 = $res['list'];
$count1 = $res['total'];

echo "<!-- pre>";
print_r($list1);
echo "</pre --!>";


$res = getFollowingList($row['idx']);
$list2 = $res['list'];
$count2 = $res['total'];

$res = getFollowerList($row['idx']);
$list3 = $res['list'];
$count3 = $res['total'];
//
//$res = getMyFollowPendingList($row['idx']);
//$list3 = $res['list'];
//$count3 = $res['total'];
//
//$res = getFollowRejectedList($row['idx']);
//$list4 = $res['list'];
//$count4 = $res['total'];
//
//$res = getFollowPendingList($row['idx']);
//$list5 = $res['list'];
//$count5 = $res['total'];
//
//$res = getFollowerRejectedList($row['idx']);
//$list6 = $res['list'];
//$count6 = $res['total'];

$_act = "follow";
$_act_txt = " 팔로우";
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
        팔로우 정보를 볼 수 있습니다.
      </p>
    </div>
    <nav aria-label="breadcrumb" role="navigation">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="#"><?=$arr_mt_level[$_GET['sel_lv']]?>회원관리</a></li>
        <li class="breadcrumb-item active"><?=$arr_mt_level[$_GET['sel_lv']]?>팔로우</li>
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
          <li class="nav-item"><a class="nav-link active" id="member-tab-1" data-toggle="tab" href="#member-1" role="tab" aria-controls="home" aria-selected="true">가능목록 (<?php echo $count1?>)</a></li>
          <li class="nav-item"><a class="nav-link" id="member-tab-2" data-toggle="tab" href="#member-2" role="tab" aria-controls="home" aria-selected="true">팔로잉 (<?php echo $count2?>)</a></li>
          <li class="nav-item"><a class="nav-link" id="member-tab-3" data-toggle="tab" href="#member-3" role="tab" aria-controls="home" aria-selected="true">팔로워 (<?php echo $count3?>)</a></li>

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
                    if($list1):
                      foreach($list1 as $member) {
                         $img = profileImageUrl($member['mt_image1'], $member_img_dir, $member_img_url);
                  ?>
                    <div class="col-sm-6 col-md-2">
                      <div class="user user--rounded user--bordered user--xlg">
                        <img src="<?php echo $img?>" />
                        <div class="user__name">
                          <strong><?php echo $member['mt_name']?></strong><br><span><?php echo $member['mt_id']?></span><br>
                          <button class="btn btn-danger btn-xs btn-following" data-follower-idx="<?=$row['mt_idx']?>" data-following-idx="<?php echo $member['idx']?>">팔로잉</button>
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
              <div class="tab-pane fade" id="member-2" role="tabpanel" aria-labelledby="member-tab-2">

                <div class="row">
                  <?php
                  if($list2):
                    foreach($list2 as $member) {
                      $img = profileImageUrl($member['mt_image1'], $member_img_dir, $member_img_url);
                      ?>
                      <div class="col-sm-6 col-md-2">
                        <div class="user user--rounded user--bordered user--xlg">
                          <img src="<?php echo $img?>" />
                          <div class="user__name">
                            <strong><?php echo $member['mt_name']?></strong><br><span><?php echo $member['mt_id']?></span><br>
                            <i class="text-muted"><?php echo DateType($member['confirm_date'], "7")?></i>
                            <button class="btn btn-danger btn-xs btn-unfollow" data-follower-idx="<?=$member['follower_idx']?>" data-following-idx="<?php echo $member['following_idx']?>">언팔로우</button>
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

              <div class="tab-pane fade" id="member-3" role="tabpanel" aria-labelledby="member-tab-3">

                <div class="row">
                  <?php
                  if($list3):
                    foreach($list3 as $member) {
                      $img = profileImageUrl($member['mt_image1'], $member_img_dir, $member_img_url);
                      ?>
                      <div class="col-sm-6 col-md-2">
                        <div class="user user--rounded user--bordered user--xlg">
                          <img src="<?php echo $img?>" />
                          <div class="user__name">
                            <strong><?php echo $member['mt_name']?></strong><br><span><?php echo $member['mt_id']?></span><br>
                            <button class="btn btn-danger btn-xs btn-follow-remove" data-my-idx="<?=$member['following_idx']?>" data-follower-idx="<?php echo $member['follower_idx']?>">팔로워 삭제</button>
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
            
              <div class="tab-pane fade" id="member-4" role="tabpanel" aria-labelledby="member-tab-4">

                <div class="row">
                  <?php
                  if($list4):
                    foreach($list4 as $member) {
                      $img = profileImageUrl($member['mt_image1'], $member_img_dir, $member_img_url);
                      ?>
                      <div class="col-sm-6 col-md-2">
                        <div class="user user--rounded user--bordered user--xlg">
                          <img src="<?php echo $img?>" />
                          <div class="user__name">
                            <strong><?php echo $member['mt_name']?></strong><br><span><?php echo $member['mt_id']?></span><br>
                            <i class="text-muted">거절: <?php echo DateType($member['confirm_date'], "7")?></i>
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
              <div class="tab-pane fade" id="member-5" role="tabpanel" aria-labelledby="member-tab-5">

                <div class="row">
                  <?php
                  if($list5):
                    foreach($list5 as $member) {
                      $img = profileImageUrl($member['mt_image1'], $member_img_dir, $member_img_url);
                      ?>
                      <div class="col-sm-6 col-md-2">
                        <div class="user user--rounded user--bordered user--xlg">
                          <img src="<?php echo $img?>" />
                          <div class="user__name">
                            <strong><?php echo $member['mt_name']?></strong><br><span><?php echo $member['mt_id']?></span><br>
                            <button class="btn btn-success btn-xs btn-follow-accept" data-follower-idx="<?=$member['follower_idx']?>" data-following-idx="<?php echo $member['following_idx']?>">승인</button>
                            <button class="btn btn-danger btn-xs btn-follow-reject" data-follower-idx="<?=$member['follower_idx']?>" data-following-idx="<?php echo $member['following_idx']?>">거절</button>
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
              <div class="tab-pane fade" id="member-6" role="tabpanel" aria-labelledby="member-tab-6">

                <div class="row">
                  <?php
                  if($list6):
                    foreach($list6 as $member) {
                      $img = profileImageUrl($member['mt_image1'], $member_img_dir, $member_img_url);
                      ?>
                      <div class="col-sm-6 col-md-2">
                        <div class="user user--rounded user--bordered user--xlg">
                          <img src="<?php echo $img?>" />
                          <div class="user__name">
                            <strong><?php echo $member['mt_name']?></strong><br><span><?php echo $member['mt_id']?></span><br>
                            <i class="text-muted">거절: <?php echo DateType($member['confirm_date'], "7")?></i>
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


          // 팔로우 승인 처리
          $(document).on('click', '.btn-follow-accept', function() {
              if(!confirm('팔로우 요청을 승인하시겠습니까?')) return;


              let follower_idx = $(this).data('follower-idx');
              let following_idx = $(this).data('following-idx');


              const formData = new FormData();
              formData.append('act', 'follow_accept');
              formData.append('follower_idx', follower_idx);
              formData.append('following_idx', following_idx);

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
                          app.toastr.showSuccess(response.message, './member_follow.php?<?php echo $_query_str?>');
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

          // 팔로우 거절 처리
          $(document).on('click', '.btn-follow-reject', function() {
              if(!confirm('팔로우 요청을 거절하시겠습니까?')) return;

              let follower_idx = $(this).data('follower-idx');
              let following_idx = $(this).data('following-idx');

              const formData = new FormData();
              formData.append('act', 'follow_reject');
              formData.append('follower_idx', follower_idx);
              formData.append('following_idx', following_idx);

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
                          app.toastr.showSuccess(response.message, './member_follow.php?<?php echo $_query_str?>');
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


          // 팔로잉
          $(document).on('click', '.btn-following', function() {
              if(!confirm('팔로잉 하시겠습니까?')) return;


              let follower_idx = $(this).data('follower-idx');
              let following_idx = $(this).data('following-idx');


              const formData = new FormData();
              formData.append('act', 'following');
              formData.append('follower_idx', follower_idx);
              formData.append('following_idx', following_idx);

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
                          app.toastr.showSuccess(response.message, './member_follow.php?<?php echo $_query_str?>');
                      } else {
                          app.toastr.showError(response.message);
                      }
                  },
                  error: (xhr, status, error) => {
                      $('#splinner_modal').modal('hide');
                      console.error(xhr)
                      // app.toastr.showError(response.message);
                  }
              });
          });

          // 언팔로우
          $(document).on('click', '.btn-unfollow', function() {
              if(!confirm('언팔로우 하시겠습니까?')) return;


              let follower_idx = $(this).data('follower-idx');
              let following_idx = $(this).data('following-idx');


              const formData = new FormData();
              formData.append('act', 'unfollow');
              formData.append('follower_idx', follower_idx);
              formData.append('following_idx', following_idx);

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
                          app.toastr.showSuccess(response.message, './member_follow.php?<?php echo $_query_str?>');
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

          // 팔로워 제거 기능
          $(document).on('click', '.btn-follow-remove', function() {
              if(!confirm('팔로워를 제거 하시겠습니까?')) return;


              let follower_idx = $(this).data('follower-idx');
              let my_idx = $(this).data('my-idx');


              const formData = new FormData();
              formData.append('act', 'follow_remove');
              formData.append('follower_idx', follower_idx);
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
                          app.toastr.showSuccess(response.message, './member_follow.php?<?php echo $_query_str?>');
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
