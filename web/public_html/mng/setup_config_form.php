<?php
include $_SERVER['DOCUMENT_ROOT'] . "/mng/head.inc.php";
$chk_menu = '99';
$chk_sub_menu = '6';
include $_SERVER['DOCUMENT_ROOT'] . "/mng/inc/header.menu.inc.php";

$DB->where('category', ['ALIGO', 'KAKAO', 'NAVER', 'APPLE', 'GOOGLE', 'PORTONE'], 'IN');
$DB->orderBy("idx", "asc");
$result = $DB->get("setup_config_t");
$lists = [];
foreach ($result as $row) {
  $category = $row['category'];
  if (!isset($lists[$category])) {
    $lists[$category] = [];
  }
  $lists[$category][] = $row;
}

$list1 = $lists['ALIGO'] ?? [];
$list2 = $lists['KAKAO'] ?? [];
$list3 = $lists['NAVER'] ?? [];
$list4 = $lists['APPLE'] ?? [];
$list5 = $lists['GOOGLE'] ?? [];
$list6 = $lists['PORTONE'] ?? [];


$DB->where('idx', '1');
$setup = $DB->getone('setup_t');

?>


  <!-- PAGE CONTENT CONTAINER -->
  <div class="content" id="content">
    <!-- PAGE HEADING -->
    <div class="page-heading">
      <div class="page-heading__container">
        <div class="icon">
          <span class="li-register"></span>
        </div>
        <h1 class="title">연동정보설정</h1>
        <p class="caption">
          홈페이지 연동정보를 관리 할 수 있습니다.
        </p>
      </div>
      <nav aria-label="breadcrumb" role="navigation">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="/mng">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="#">설정</a></li>
          <li class="breadcrumb-item active">연동정보설정</li>
        </ol>
      </nav>
    </div>
    <!-- //END PAGE HEADING -->
    <div class="container-fluid">
      <div class="card margin-bottom-0">

        <div class="card-header">
          <ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
            <li class="nav-item"><a class="nav-link active" id="member-tab-1" data-toggle="tab" href="#member-1"
                                    role="tab" aria-controls="home" aria-selected="true">알리고</a></li>

            <li class="nav-item"><a class="nav-link" id="member-tab-2" data-toggle="tab" href="#member-2" role="tab"
                                    aria-controls="profile" aria-selected="false">카카오</a></li>

            <li class="nav-item"><a class="nav-link" id="member-tab-3" data-toggle="tab" href="#member-3" role="tab"
                                    aria-controls="profile" aria-selected="false">네이버</a></li>
            <li class="nav-item"><a class="nav-link" id="member-tab-4" data-toggle="tab" href="#member-4" role="tab"
                                    aria-controls="profile" aria-selected="false">애플</a></li>

            <li class="nav-item"><a class="nav-link" id="member-tab-5" data-toggle="tab" href="#member-5" role="tab"
                                    aria-controls="profile" aria-selected="false">구글</a></li>

            <li class="nav-item"><a class="nav-link" id="member-tab-6" data-toggle="tab" href="#member-6" role="tab"
                                    aria-controls="profile" aria-selected="false">포트원</a></li>

          </ul>
        </div>
        <div class="card-body">
          <form method="post" name="frm_form" id="frm_form" action="./setup_update.php" target="hidden_ifrm"
                enctype="multipart/form-data">
            <input type="hidden" name="act" id="act" value="config"/>
            <div class="tab-content margin-top-15" id="myTabContent">
              <div class="tab-pane fade show active" id="member-1" role="tabpanel" aria-labelledby="member-tab-1">

                <? foreach($list1 as $row) { ?>
                <div class="form-group row">
                  <label for="mt_id" class="col-sm-2 col-form-label"><?php echo $row['description']?></label>
                  <div class="col-sm-10">
                    <input type="text" name="<?php echo $row['category'] ?>[<?php echo $row['config_key'] ?>]"  value="<?php echo $row['config_value']?>" class="form-control">
                  </div>
                </div>
                <? }?>


              </div>

              <div class="tab-pane fade" id="member-2" role="tabpanel" aria-labelledby="member-tab-2">

                <? foreach($list2 as $row) { ?>
                  <div class="form-group row">
                    <label for="mt_id" class="col-sm-2 col-form-label"><?php echo $row['description']?></label>
                    <div class="col-sm-10">
                      <input type="text" name="<?php echo $row['category'] ?>[<?php echo $row['config_key'] ?>]"  value="<?php echo $row['config_value']?>" class="form-control">
                    </div>
                  </div>
                <? }?>

              </div>
              <div class="tab-pane fade" id="member-3" role="tabpanel" aria-labelledby="member-tab-3">

                <? foreach($list3 as $row) { ?>
                  <div class="form-group row">
                    <label for="mt_id" class="col-sm-2 col-form-label"><?php echo $row['description']?></label>
                    <div class="col-sm-10">
                      <input type="text" name="<?php echo $row['category'] ?>[<?php echo $row['config_key'] ?>]"  value="<?php echo $row['config_value']?>" class="form-control">
                    </div>
                  </div>
                <? }?>

              </div>
              <div class="tab-pane fade" id="member-4" role="tabpanel" aria-labelledby="member-tab-4">

                <? foreach($list4 as $row) { ?>
                  <div class="form-group row">
                    <label for="mt_id" class="col-sm-2 col-form-label"><?php echo $row['description']?></label>
                    <div class="col-sm-10">
                      <input type="text" name="<?php echo $row['category'] ?>[<?php echo $row['config_key'] ?>]"  value="<?php echo $row['config_value']?>" class="form-control">
                    </div>
                  </div>
                <? }?>

              </div>
              <div class="tab-pane fade" id="member-5" role="tabpanel" aria-labelledby="member-tab-5">

                <? foreach($list5 as $row) { ?>
                  <div class="form-group row">
                    <label for="mt_id" class="col-sm-2 col-form-label"><?php echo $row['description']?></label>
                    <div class="col-sm-10">
                      <input type="text" name="<?php echo $row['category'] ?>[<?php echo $row['config_key'] ?>]"  value="<?php echo $row['config_value']?>" class="form-control">
                    </div>
                  </div>
                <? }?>

              </div>


              <div class="tab-pane fade" id="member-6" role="tabpanel" aria-labelledby="member-tab-6">

                <? foreach($list6 as $row) { ?>
                  <div class="form-group row">
                    <label for="mt_id" class="col-sm-2 col-form-label"><?php echo $row['description']?></label>
                    <div class="col-sm-10">
                      <input type="text" name="<?php echo $row['category'] ?>[<?php echo $row['config_key'] ?>]"  value="<?php echo $row['config_value']?>" class="form-control">
                    </div>
                  </div>
                <? }?>


                <div class="form-group row">
                  <label for="mt_id" class="col-sm-2 col-form-label">포트원 결제모드</label>
                  <div class="col-sm-10 d-flex align-items-center">
                    <div class="custom-control custom-radio custom-control-inline">
                      <input type="radio" id="st_portone_no" name="st_portone" value="no" class="custom-control-input" <?=$setup['st_portone']=='no'?'checked':''?>>
                      <label class="custom-control-label" for="st_portone_no">사용안함</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                      <input type="radio" id="st_portone_test" name="st_portone" value="test" class="custom-control-input" <?=$setup['st_portone']=='test'?'checked':''?>>
                      <label class="custom-control-label" for="st_portone_test">테스트결제</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                      <input type="radio" id="st_portone_real" name="st_portone" value="real" class="custom-control-input" <?=$setup['st_portone']=='real'?'checked':''?>>
                      <label class="custom-control-label" for="st_portone_real">실결제</label>
                    </div>

                  </div>
                </div>






              </div>


              <div class="form-group row justify-content-center margin-top-30">
                <button type="submit" class="btn btn-secondary">확인</button>
              </div>
          </form>
        </div>
        <script type="text/javascript" src="<?= MNG_HTTP ?>/js/fileupload.js?v=<?= $v_txt ?>"></script>
        <script>


            $(document).ready(function () {

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


            // 페이지 로드 후 지도 초기화
            document.addEventListener('DOMContentLoaded', function () {
                console.log('DOM 로드 완료');

                // 탭 변경 이벤트 감지
                $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                    const targetId = $(e.target).attr('id');
                    console.log('탭 변경:', targetId);


                });

                // 페이지 로드 시 사업자 정보 탭이 활성화되어 있으면 지도 초기화
                if ($('#member-tab-4').hasClass('active')) {

                }
            });
        </script>

      </div>
    </div>
  </div>
  <!-- //END PAGE CONTENT CONTAINER -->
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/mng/foot.inc.php";
?>