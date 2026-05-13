<!-- 관리자 공통 Alert / Confirm Modal -->
<div class="modal fade alt_modal" id="adminCommonModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered modal-md" role="document">
    <div class="modal-content">
       <p class="text-center mb-4"><img src="./img/ico_ch2.png" alt="아이콘" style="width: 90px;" class="check-once"></p>
      <div class="modal-header py-2">
        <h5 class="modal-title mb-0 text-center"></h5>
      </div>
      <div class="modal-body border-0 ">
      </div>
      <div class="modal-footer d-flex p-0">
        <button type="button" class="btn btn-light btn-cancel  ">
          취소
        </button>
        <button type="button" class="btn btn-primary btn-confirm  ">
          확인
        </button>
      </div>
    </div>
  </div>
</div>

<script>
    (function () {
  const $modal = $('#adminCommonModal');

  window.g5Alert = function (msg, title = '알림') {
    $modal.find('.modal-title').text(title);
    $modal.find('.modal-body').html(msg);

    $modal.find('.btn-cancel').hide();
    $modal.find('.btn-confirm')
      .off('click')
      .show()
      .text('확인')
      .removeClass('btn-danger')
      .addClass('btn-primary');

    $modal.modal('show');
  };

  window.g5Confirm = function (msg, okCallback, title = '확인') {
    $modal.find('.modal-title').text(title);
    $modal.find('.modal-body').html(msg);

    $modal.find('.btn-cancel').show().text('취소');
    $modal.find('.btn-confirm')
      .off('click')
      .show()
      .text('확인')
      .removeClass('btn-primary')
      .addClass('btn-danger')
      .on('click', function () {
        $modal.modal('hide');
        if (okCallback) okCallback();
      });

    $modal.modal('show');
  };
})();

</script>