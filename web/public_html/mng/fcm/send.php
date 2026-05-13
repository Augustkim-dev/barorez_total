<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu='10';
$chk_sub_menu='4';
include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.menu.inc.php";

$DB->where('a1.del_status', 'N');
$DB->where('a1.mt_level', '2');
$DB->where('a1.mt_status', 'Y');
$DB->orderBy("a1.idx", "desc");
$list = $DB->get("member_t as a1");

$memeber_list2 = [];
foreach ($list as $row) {
  $item = $row;

  $item['app_push_txt'] = !empty($item['mt_app_token']) ? '가능' : '불가';
  $item['app_push_cls'] = !empty($item['mt_app_token']) ? 'text-success' : 'text-danger';

  $DB->where('a1.mt_idx', $row['idx']);
  $DB->orderBy("a1.idx", "desc");
  $token_list = $DB->get("member_fcm_token_t as a1");

  $item['web_push_txt'] = isset($token_list) && count($token_list) > 0 ? '가능' : '불가';
  $item['web_push_cls'] = isset($token_list) && count($token_list) > 0 ? 'text-success' : 'text-danger';

  $memeber_list2[] = $item;
}

$DB->where('a1.del_status', 'N');
$DB->where('a1.mt_level', '5');
$DB->where('a1.mt_status', 'Y');
$DB->orderBy("a1.idx", "desc");
$list = $DB->get("member_t as a1");

$memeber_list5 = [];
foreach ($list as $row) {
  $item = $row;

  $item['app_push_txt'] = !empty($item['mt_app_token']) ? '가능' : '불가';
  $item['app_push_cls'] = !empty($item['mt_app_token']) ? 'text-success' : 'text-danger';

  $DB->where('a1.platform', 'web');
  $DB->where('a1.mt_idx', $row['idx']);
  $DB->orderBy("a1.idx", "desc");
  $token_list = $DB->get("member_fcm_token_t as a1");

  $item['web_push_txt'] = isset($token_list) && count($token_list) > 0 ? '가능' : '불가';
  $item['web_push_cls'] = isset($token_list) && count($token_list) > 0 ? 'text-success' : 'text-danger';

  $memeber_list5[] = $item;
}
?>
  <!-- PAGE CONTENT CONTAINER -->
  <div class="content" id="content">
    <!-- PAGE HEADING -->
    <?php include_once "./pheading2.php";?>
    <!-- //END PAGE HEADING -->
    <div class="container-fluid">


      <div class="row">
        <!-- 일반회원 목록 -->
        <div class="col-md-4">
          <div class="mb-2 d-flex justify-content-between align-items-center">
            <strong>일반회원</strong>
            <div>
              <button class="btn btn-sm btn-secondary btn-check-all" data-target="normalMemberList">전체선택</button>
              <button class="btn btn-sm btn-light btn-uncheck-all" data-target="normalMemberList">전체해제</button>
              <button class="btn btn-sm btn-success btn-add-selected" data-target="normalMemberList">선택추가</button>
            </div>
          </div>
          <ul id="normalMemberList" class="list-group mb-4" style="max-height:300px; overflow-y:auto;">
            <?php foreach ($memeber_list2 as $row): ?>
              <li class="list-group-item d-flex justify-content-between align-items-center" data-user-id="<?= $row['idx'] ?>">
                <div class="form-check">
                  <label class="form-check-label">
                    <input type="checkbox" class="form-check-input">
                    <?= htmlspecialchars($row['mt_name']) ?> (<?= htmlspecialchars($row['mt_id']) ?>)
                    <strong>APP(<span class="<?= htmlspecialchars($row['app_push_cls']) ?>"><?= htmlspecialchars($row['app_push_txt']) ?></span>)</strong>
                    <strong>WEB(<span class="<?= htmlspecialchars($row['web_push_cls']) ?>"><?= htmlspecialchars($row['web_push_txt']) ?></span>)</strong>
                  </label>
                </div>
                <button class="btn btn-sm btn-outline-success">추가</button>
              </li>
            <?php endforeach; ?>
          </ul>

        </div>

        <!-- 판매자 회원 목록 -->
        <div class="col-md-4">
          <div class="mb-2 d-flex justify-content-between align-items-center">
            <strong>판매자 회원</strong>
            <div>
              <button class="btn btn-sm btn-secondary btn-check-all" data-target="sellerMemberList">전체선택</button>
              <button class="btn btn-sm btn-light btn-uncheck-all" data-target="sellerMemberList">전체해제</button>
              <button class="btn btn-sm btn-success btn-add-selected" data-target="sellerMemberList">선택추가</button>
            </div>
          </div>
          <ul id="sellerMemberList" class="list-group mb-4" style="max-height:300px; overflow-y:auto;">
            <?php foreach ($memeber_list5 as $row): ?>
              <li class="list-group-item d-flex justify-content-between align-items-center" data-user-id="<?= $row['idx'] ?>">
                <div class="form-check">
                  <label class="form-check-label">
                    <input type="checkbox" class="form-check-input">
                    <?= htmlspecialchars($row['mt_name']) ?> (<?= htmlspecialchars($row['mt_id']) ?>)
                    <strong>APP(<span class="<?= htmlspecialchars($row['app_push_cls']) ?>"><?= htmlspecialchars($row['app_push_txt']) ?></span>)</strong>
                    <strong>WEB(<span class="<?= htmlspecialchars($row['web_push_cls']) ?>"><?= htmlspecialchars($row['web_push_txt']) ?></span>)</strong>
                  </label>
                </div>
                <button class="btn btn-sm btn-outline-success">추가</button>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>

        <!-- 발송 대상자 -->
        <div class="col-md-4">
          <div class="mb-2 d-flex justify-content-between align-items-center">
            <strong>발송 대상자 (<span id="targetCount">0</span>명)</strong>
            <div>
              <button class="btn btn-sm btn-secondary btn-check-all" data-target="targetList">전체선택</button>
              <button class="btn btn-sm btn-light btn-uncheck-all" data-target="targetList">전체해제</button>
              <button class="btn btn-sm btn-danger btn-remove-selected" data-target="targetList">선택삭제</button>
            </div>
          </div>
          <ul id="targetList" class="list-group mb-4" style="max-height:300px; overflow-y:auto;"></ul>

        </div>
      </div>

      <hr>

      <!-- 메시지 입력 -->
      <div class="mt-4 row">
        <form method="post" name="frm_form" id="frm_form" class="col-md-4">
          <input type="hidden" name="act" id="act" value="send" />
          <div class="mb-3 form-group row">
            <label for="fcm_title" class="col-sm-2 col-form-label">제목 <span class="text-danger">*</span></label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="fcm_title" name="fcm_title" required>
            </div>
          </div>

          <div class="mb-3 form-group row">
            <label for="fcm_body" class="col-sm-2 col-form-label">내용 <span class="text-danger">*</span></label>
            <div class="col-sm-10">
              <textarea class="form-control" id="fcm_body" rows="5" name="fcm_body"  required></textarea>
            </div>
          </div>

          <div class="mb-3 form-group row">
            <label for="fcm_message" class="col-sm-2 form-label">메시지</label>
            <div class="col-sm-10">
             <input type="text" class="form-control" id="fcm_message" name="fcm_message" >
              <small class="form-text">입력시, 알람으로 저장됩니다.</small>
            </div>
          </div>


          <div class="mb-3 form-group row">
            <label for="url" class="col-sm-2 form-label">타겟링크(M)</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="url" name="url" >
            </div>
          </div>
          <div class="mb-3 form-group row">
            <label for="url2" class="col-sm-2 form-label">타겟링크(PC)</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="url2" name="url2" >
            </div>
          </div>

          <div class="mb-3 form-group row">

            <div class="col-sm-10 offset-md-2">

                <button id="sendFcmBtn" type="submit" class="btn btn-primary">FCM 발송</button>

            </div>
          </div>


        </form>

        <div class="col-md-4">
          <div class="card">
            <div class="card-header fw-bold">치환 변수 안내 (제목,내용,메시지)</div>
            <div class="card-body">
              <ul class="list-unstyled mb-0">
                <li><code>{name}</code> - 회원 이름</li>
                <li><code>{id}</code> - 회원 아이디</li>
                <li><code>{date}</code> - 오늘 날짜 (Y-m-d)</li>
                <li><code>{time}</code> - 현재 시간 (H:i)</li>
                <li><code>{datetime}</code> - 현재 날짜/시간(Y-m-d H:i</li>
                <li><code>{app}</code> - 앱/사이트 이름 : <?php echo ADMIN_NAME?></li>
                <li><code>{url}</code> - 대표주소 : <?php echo APP_DOMAIN?></li>
              </ul>
              <hr>
              <p class="text-muted mb-0">예: <code>{name}</code>님, 이벤트가 도착했어요!</p>
            </div>
          </div>
        </div>
      </div>


    </div>
  </div>
  <!-- //END PAGE CONTENT CONTAINER -->



<script>
    // 전체선택
    $('.btn-check-all').on('click', function () {
        const target = $(this).data('target');
        $(`#${target} input[type=checkbox]`).prop('checked', true);
    });

    // 전체해제
    $('.btn-uncheck-all').on('click', function () {
        const target = $(this).data('target');
        $(`#${target} input[type=checkbox]`).prop('checked', false);
    });

    // 선택삭제
    $('.btn-remove-selected').on('click', function () {
        const target = $(this).data('target');
        $(`#${target} li`).each(function () {
            if ($(this).find('input[type=checkbox]').prop('checked')) {
                const $li = $(this);
                if ($li.attr('data-origin')) {
                    const origin = $li.attr('data-origin');
                    $li.removeAttr('data-origin');
                    $li.find('button').removeClass('btn-outline-danger').addClass('btn-outline-success').text('추가');
                    $(`#${origin}`).append($li);
                } else {
                    $li.remove();
                }
            }
        });
        updateTargetCount();
    });

    // 단일 추가 버튼 클릭 시 (개별 추가)
    $(document).on('click', '.btn-outline-success', function (e) {
        e.stopPropagation();
        const $li = $(this).closest('li');
        const originId = $li.closest('ul').attr('id');
        const userId = $li.data('user-id');
        if ($('#targetList li[data-user-id="' + userId + '"]').length === 0) {
            $li.attr('data-origin', originId);
            $li.find('button').removeClass('btn-outline-success').addClass('btn-outline-danger').text('삭제');
            $li.find('input[type=checkbox]').prop('checked', false);
            $('#targetList').append($li);
            updateTargetCount();
        }
    });

    // 선택추가 버튼 클릭 (복수 선택용)
    $('.btn-add-selected').on('click', function () {
        const originId = $(this).data('target');
        $(`#${originId} li`).each(function () {
            const $li = $(this);
            if ($li.find('input[type=checkbox]').prop('checked')) {
                const userId = $li.data('user-id');
                if ($('#targetList li[data-user-id="' + userId + '"]').length === 0) {
                    $li.attr('data-origin', originId);
                    $li.find('button').removeClass('btn-outline-success').addClass('btn-outline-danger').text('삭제');
                    $li.find('input[type=checkbox]').prop('checked', false);
                    $('#targetList').append($li);
                }
            }
        });
        updateTargetCount();
    });

    // 삭제 버튼 → 원래 목록으로 복귀
    $(document).on('click', '.btn-outline-danger', function (e) {
        e.stopPropagation();
        const $li = $(this).closest('li');
        const origin = $li.attr('data-origin');
        $li.removeAttr('data-origin');
        $li.find('button').removeClass('btn-outline-danger').addClass('btn-outline-success').text('추가');
        $li.find('input[type=checkbox]').prop('checked', false);
        $(`#${origin}`).append($li);
        updateTargetCount();
    });

    function updateTargetCount() {
        $('#targetCount').text($('#targetList li').length);
    }

    $(document).ready(function() {
      $("#frm_form").validate({
          submitHandler: function(form){

              const userIds = [];
              $('#targetList .list-group-item').each(function () {
                  userIds.push($(this).data('user-id'));
              });

              if (userIds.length === 0) {
                  alert('발송할 회원을 선택해주세요.');
                  return false;
              }

              console.log(userIds)

              const formData = new FormData(form);
              formData.append('user_ids', JSON.stringify(userIds));
              console.log(...formData)


              $.confirm({
                  title: 'FCM 발송',
                  content: "발송 하시겠습니까?",
                  buttons: {
                      cancel: {
                          text: "취소",
                          btnClass: "btn-outline-light",
                      },
                      confirm: {
                          text: "확인",
                          btnClass: "btn-primary",
                          action: function () {

                              $.ajax({
                                  url: './send-fcm.php',
                                  type: 'POST',
                                  data: formData,
                                  processData: false,
                                  contentType: false,
                                  beforeSend: () => $('#splinner_modal').modal('show'),
                                  success: (response) => {

                                      console.log(response)
                                      $('#splinner_modal').modal('hide');
                                      if(response.success) {
                                          app.toastr.showSuccess(response.message);
                                      } else {
                                          app.toastr.showError(response.message);
                                      }
                                  },
                                  error: (xhr, status, error) => {
                                      $('#splinner_modal').modal('hide');
                                      console.error(error)
                                      app.toastr.showError(response.message);
                                  }
                              });

                          },
                      },
                  },
              });





              return false;
          },
          rules: {
              fcm_title: {
                  required: true
              },
              fcm_body: {
                  required: true
              },
          },
          messages: {
              fcm_title: {
                  required: "제목을 입력해주세요"
              },
              fcm_body: {
                  required: "내용을 입력해주세요"
              },
          },
          errorElement: 'span',
          errorPlacement: (error, element) => {
              error.addClass('invalid-feedback');
              element.closest('.col-sm-10').append(error);
          },
          highlight: (element) => $(element).addClass('is-invalid'),
          unhighlight: (element) => $(element).removeClass('is-invalid')
      });
    });
</script>
<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/foot.inc.php";
?>