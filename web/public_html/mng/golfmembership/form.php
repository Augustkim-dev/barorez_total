<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu='13';
$chk_sub_menu='1';
include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.menu.inc.php";
//include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.sidebar.inc.php";

$chk_post_code = "Y";
//$chk_ckeditor = "Y";

if ($_GET['act'] == "update") {
    $DB->where('gmt_idx', $_GET['nt_idx']);
    $row = $DB->getone($CFG_TBL['golf_membership']['main'], '*, gmt_idx as nt_idx');
    $_act = "update";
    $_act_txt = " 수정";

    $where = ['gmt_idx'=>$row['gmt_idx']];
    $order = ['order'=>'gmgf_num', 'by'=>'ASC'];
    $greenfee_list = getListByTable($CFG_TBL['golf_membership']['greenfee'], $where, $order);
    $greenfee_count = count($greenfee_list);
    $loop_greenfee_count = max(10, $greenfee_count);

    $where = ['gmt_idx'=>$row['gmt_idx']];
    $order = ['order'=>'gmmf_num', 'by'=>'ASC'];
    $myeong_list = getListByTable($CFG_TBL['golf_membership']['myeong'], $where, $order);
    $myeong_count = count($myeong_list);
    $loop_myeong_count = max(10, $myeong_count);

} else if ($_GET['act'] == "copy") {
  $DB->where('gmt_idx', $_GET['nt_idx']);
  $row = $DB->getone($CFG_TBL['golf_membership']['main'], '*, gmt_idx as nt_idx');
  $_act = "copy";
  $_act_txt = " 복사";

  $where = ['gmt_idx'=>$row['gmt_idx']];
  $order = ['order'=>'gmgf_num', 'by'=>'ASC'];
  $greenfee_list = getListByTable($CFG_TBL['golf_membership']['greenfee'], $where, $order);
  $greenfee_count = count($greenfee_list);
  $loop_greenfee_count = max(10, $greenfee_count);


  $where = ['gmt_idx'=>$row['gmt_idx']];
  $order = ['order'=>'gmmf_num', 'by'=>'ASC'];
  $myeong_list = getListByTable($CFG_TBL['golf_membership']['myeong'], $where, $order);
  $myeong_count = count($myeong_list);
  $loop_myeong_count = max(10, $myeong_count);

} else {
    $_act = "input";
    $_act_txt = " 등록";

  $loop_greenfee_count = max(10, 1);
  $loop_myeong_count = max(10, 1);
}







?>
    <!-- PAGE CONTENT CONTAINER -->
    <div class="content" id="content">
        <!-- PAGE HEADING -->
      <?php include_once "./pheading.php";?>
        <!-- //END PAGE HEADING -->
        <div class="container-fluid">
            <div class="card margin-bottom-0">
                <div class="card-body">

                    <div class="card-header">
                      <ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
                        <li class="nav-item"><a class="nav-link active" id="step-tab-1" data-toggle="tab" href="#step-1" role="tab" aria-controls="home" aria-selected="true">기본</a></li>
                        <li class="nav-item"><a class="nav-link" id="step-tab-2" data-toggle="tab" href="#step-2" role="tab" aria-controls="home" aria-selected="true">그린피 이용요금</a></li>
                        <li class="nav-item"><a class="nav-link" id="step-tab-3" data-toggle="tab" href="#step-3" role="tab" aria-controls="home" aria-selected="true">명의개서시 필요비용</a></li>
                        <li class="nav-item"><a class="nav-link" id="step-tab-4" data-toggle="tab" href="#step-4" role="tab" aria-controls="home" aria-selected="true">이용정보</a></li>
                        <li class="nav-item"><a class="nav-link" id="step-tab-5" data-toggle="tab" href="#step-5" role="tab" aria-controls="home" aria-selected="true">이미지</a></li>
                      </ul>
                    </div>


                    <form method="post" name="frm_form" id="frm_form" action="./update.php" target="hidden_ifrm" enctype="multipart/form-data">
                        <input type="hidden" name="act" id="act" value="<?=$_act?>" />
                        <input type="hidden" name="nt_idx" id="nt_idx" value="<?=$row['nt_idx']?>" />
                        <div class="tab-content margin-top-15">
                          <div class="tab-pane fade show active" id="step-1" role="tabpanel" aria-labelledby="step-tab-1">


                            <div class="form-group row margin-top-30">
                              <label for="gmt_golf_name" class="col-sm-2 col-form-label">골프장명</label>
                              <div class="col-sm-6 form-validate">
                                <input type="text" name="gmt_golf_name" id="gmt_golf_name" value="<?=$row['gmt_golf_name']?>" placeholder="골프장명 입력" class="form-control">
                              </div>
                              <div class="col-sm-4">

                                <div class="form-check form-check-inline">
                                  <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="gmt_captain" id="gmt_captain" value="Y" <?=($row['gmt_captain'] == 'Y' ? 'checked' : '')?> />
                                    <label class="custom-control-label" for="gmt_captain">대표 회원권</label>
                                  </div>
                                </div>

                              </div>
                            </div>


                            <div class="form-group row margin-top-30">
                              <label for="gmt_local" class="col-sm-2 col-form-label">지역</label>
                              <div class="col-sm-4 form-validate">
                                <select name="gmt_local" id="gmt_local" class="form-control select-simple">
                                  <option value="">선택하세요.</option>
                                  <?php
                                  foreach($arr_gmt_local_type as $key=>$local) {
                                    $selected = ($row['gmt_local'] == $key) ? 'selected' : '';
                                    printf('<option value="%s" %s>%s</option>', $key, $selected, $local);
                                  }
                                  ?>
                                </select>
                              </div>
                              <label for="gmt_owdate" class="col-sm-2 col-form-label">개장일</label>
                              <div class="col-sm-4 form-validate">
                                <input type="date" name="gmt_owdate" id="gmt_owdate" value="<?=$row['gmt_owdate']?>" placeholder="개장일 입력" class="form-control">
                              </div>
                            </div>


                            <div class="form-group row">
                              <label for="gmt_thum" class="col-sm-2 col-form-label">썸네일</label>
                              <div class="col-sm-10 form-validate">
                                <?php
                                  $editor_name = 'gmt_thum';
                                  echo "<textarea name='".$editor_name."' id='".$editor_name."' class='form-control'>".$row[$editor_name]."</textarea>";
                                ?>
                              </div>
                            </div>

                            <div class="form-group row">
                              <label for="gmt_url" class="col-sm-2 col-form-label">홈페이지주소</label>
                              <div class="col-sm-10">
                                <input type="text" name="gmt_url" id="gmt_url" value="<?=$row['gmt_url']?>" placeholder="홈페이지주소 입력" class="form-control">
                              </div>
                            </div>

                            <div class="form-group row">
                              <label for="gmt_hole" class="col-sm-2 col-form-label">홀수</label>
                              <div class="col-sm-4 form-validate">
                                <input type="text" name="gmt_hole" id="gmt_hole" value="<?=$row['gmt_hole']?>" class="form-control">
                              </div>
                              <label for="gmt_person" class="col-sm-2 col-form-label">회원수</label>
                              <div class="col-sm-4 form-validate">
                                <input type="text" name="gmt_person" id="gmt_person" value="<?=$row['gmt_person']?>" class="form-control">
                              </div>
                            </div>

                            <div class="form-group row">
                              <label for="gmt_sale_price" class="col-sm-2 col-form-label">분양가 <small>(단위 : 만원)</small></label>
                              <div class="col-sm-4 form-validate">
                                <input type="text" name="gmt_sale_price" id="gmt_sale_price" value="<?=$row['gmt_sale_price']?>" class="form-control">
                              </div>
                              <label for="gmt_hp" class="col-sm-2 col-form-label">전화번호</label>
                              <div class="col-sm-4 form-validate">
                                <input type="text" name="gmt_hp" id="gmt_hp" value="<?=$row['gmt_hp']?>" class="form-control">
                              </div>
                            </div>

                            <div class="form-group row">
                              <label for="wrap_zip1" class="col-sm-2 col-form-label">주소</label>
                              <div class="col-sm-10">
                                <p class="form-inline">
                                  <input type="text" class="form-control" name="gmt_zip" id="gmt_zip" value="<?=$row['gmt_zip']?>" style="width:100px;" placeholder="" readonly="">
                                  <button type="button" class="btn btn-secondary ml-2" onclick="DaumPostcode('gmt_zip', 'gmt_add1', 'gmt_add2', 'wrap_zip1');">우편번호</button>
                                </p>
                                <div id="wrap_zip1" style="display:none;border:1px solid;width:100%;height:300px;margin:5px 0;position:relative">
                                  <img src="//i1.daumcdn.net/localimg/localimages/07/postcode/320/close.png" id="btnFoldWrap" style="cursor:pointer;position:absolute;right:0px;top:-1px;z-index:1" onclick="foldDaumPostcode('wrap_zip1')" alt="접기 버튼">
                                </div>
                                <p class="form-validate">
                                  <input type="text" class="form-control" name="gmt_add1" id="gmt_add1" value="<?=$row['gmt_add1']?>" placeholder="" readonly="">
                                </p>
                                <p class="form-validate">
                                  <input type="text" class="form-control" name="gmt_add2" id="gmt_add2" value="<?=$row['gmt_add2']?>" placeholder="">
                                </p>
                              </div>
                            </div>


                            <div class="form-group row">
                              <label for="gmt_show" class="col-sm-2 col-form-label">노출여부</label>
                              <div class="col-sm-10">
                                <select name="gmt_show" id="gmt_show" class="form-control">
                                  <option value="Y">사용</option>
                                  <option value="N">미사용</option>
                                </select>
                              </div>
                            </div>
                            <? if ($_GET['act'] == "update") {?>
                              <div class="form-group row">
                                <label for="created_at" class="col-md-2 col-form-label">등록일시</label>
                                <div class="col-md-10">
                                  <?=DateType($row['gmt_wdate'], 6)?>
                                </div>
                              </div>
                            <? } ?>

                            <div class="form-group row justify-content-center margin-top-30">
                                <? if($_act=='update'){?>
                                  <button type="button" class="btn btn-primary d-none" data-gmt-idx="<?=$row['gmt_idx']?>" onclick="onPush(this);" >변경 알림 푸시 전송</button>
                                <? } ?>
                                <button type="button"  onclick="history.go(-1);" class="btn btn-outline-secondary mx-1" >목록</button>
                                <button type="submit" class="btn btn-secondary" ><?=$_act_txt?></button>
                            </div>
                          </div>

                          <div class="tab-pane fade" id="step-2" role="tabpanel" aria-labelledby="step-tab-2">

                            <table class="table" id="greenfee_table">
                              <thead>
                              <tr>
                                <th class="text-center">회원구분</th>
                                <th class="text-center">주중</th>
                                <th class="text-center">주말</th>
                                <th class="text-center">삭제</th>
                              </tr>
                              </thead>
                              <tbody>

                              <?php
                                for ($i = 0; $i < $loop_greenfee_count; $i++) {
                                  $gf = $greenfee_list[$i] ?? ['gmgf_name'=>'', 'gmgf_weekday'=>'', 'gmgf_weekend'=>''];
                              ?>
                                  <tr>
                                    <td class="text-center">
                                      <input type="text" name="gmgf_name[]" class="form-control" value="<?= htmlspecialchars($gf['gmgf_name']) ?>" placeholder="직접입력" />
                                    </td>
                                    <td class="text-center">
                                      <input type="text" name="gmgf_weekday[]" class="form-control" value="<?= htmlspecialchars($gf['gmgf_weekday']) ?>" placeholder="직접입력" />
                                    </td>
                                    <td class="text-center">
                                      <input type="text" name="gmgf_weekend[]" class="form-control" value="<?= htmlspecialchars($gf['gmgf_weekend']) ?>" placeholder="직접입력" />
                                    </td>
                                    <td class="text-center">
                                      <button type="button" class="btn btn-danger btn-sm btn-remove">삭제</button>
                                    </td>
                                  </tr>
                              <?php } ?>
                              </tbody>
                            </table>

                            <div class="text-right mb-3">
                              <button type="button" id="add_row" class="btn btn-primary">추가</button>
                            </div>



                            <div class="form-group row justify-content-center margin-top-30">

                              <button type="button"  onclick="history.go(-1);" class="btn btn-outline-secondary mx-1" >목록</button>
                              <button type="submit" class="btn btn-secondary" ><?=$_act_txt?></button>
                            </div>
                          </div>

                          <div class="tab-pane fade" id="step-3" role="tabpanel" aria-labelledby="step-tab-3">


                            <table class="table" id="myeong_table">
                              <thead>
                              <tr>
                                <th class="text-center">분류</th>
                                <th class="text-center">요금</th>
                                <th class="text-center">비고</th>
                                <th class="text-center">삭제</th>
                              </tr>
                              </thead>
                              <tbody>
                              <?php
                                for ($i = 0; $i < $loop_myeong_count; $i++) {
                                $gf = $myeong_list[$i] ?? ['gmgf_name'=>'', 'gmmf_price'=>'', 'gmmt_info'=>''];
                              ?>
                                <tr>
                                  <td class="text-center"><input type="text" name="gmmf_name[]" value="<?= htmlspecialchars($gf['gmgf_name']) ?>" class="form-control" placeholder="직접입력" /></td>
                                  <td class="text-center"><input type="text" name="gmmf_price[]" value="<?= htmlspecialchars($gf['gmmf_price']) ?>" class="form-control" placeholder="직접입력" /></td>
                                  <td class="text-center"><input type="text" name="gmmt_info[]" value="<?= htmlspecialchars($gf['gmmt_info']) ?>" class="form-control" placeholder="직접입력" /></td>
                                  <td class="text-center">
                                    <button type="button" class="btn btn-danger btn-sm btn-remove-myeong">삭제</button>
                                  </td>
                                </tr>
                              <?php } ?>
                              </tbody>
                            </table>

                            <div class="text-right mb-3">
                              <button type="button" id="add_myeong" class="btn btn-primary">추가</button>
                            </div>


                            <div class="form-group row justify-content-center margin-top-30">

                              <button type="button"  onclick="history.go(-1);" class="btn btn-outline-secondary mx-1" >목록</button>
                              <button type="submit" class="btn btn-secondary" ><?=$_act_txt?></button>
                            </div>
                          </div>

                          <div class="tab-pane fade" id="step-4" role="tabpanel" aria-labelledby="step-tab-4">

                            <div class="form-group row margin-top-30">
                              <label for="gmt_golf_name" class="col-sm-2 col-form-label">회원권 종류</label>
                              <div class="col-sm-10">

                                <div class="form-check form-check-inline">

                                  <?php
                                    $checked_user_types = explode('|:|', $row['gmt_user_type']);
                                    foreach($arr_gmt_user_type as $key=>$user) {
                                  ?>
                                      <div class="custom-control custom-checkbox mr-4">
                                        <input type="checkbox" class="custom-control-input" name="gmt_user_type[]" id="gmt_user_type<?php echo $key?>" value="<?php echo $key?>" <?= in_array($key, $checked_user_types) ? 'checked' : '' ?>  />
                                        <label class="custom-control-label" for="gmt_user_type<?php echo $key?>"><?php echo $user?></label>
                                      </div>

                                  <?php } ?>

                                </div>

                              </div>
                            </div>

                            <div class="form-group row">
                              <label for="gmt_membership" class="col-sm-2 col-form-label">회원구성</label>
                              <div class="col-sm-10 form-validate">
                                <?php
                                $editor_name = 'gmt_membership';
                                echo "<textarea name='".$editor_name."' id='".$editor_name."' class='form-control'>".$row[$editor_name]."</textarea>";
                                ?>
                              </div>
                            </div>

                            <div class="form-group row">
                              <label for="gmt_benefit" class="col-sm-2 col-form-label">회원혜택</label>
                              <div class="col-sm-10 form-validate">
                                <?php
                                $editor_name = 'gmt_benefit';
                                echo "<textarea name='".$editor_name."' id='".$editor_name."' class='form-control'>".$row[$editor_name]."</textarea>";
                                ?>
                              </div>
                            </div>

                            <div class="form-group row">
                              <label for="gmt_point" class="col-sm-2 col-form-label">회원권특징</label>
                              <div class="col-sm-10 form-validate">
                                <?php
                                $editor_name = 'gmt_point';
                                echo "<textarea name='".$editor_name."' id='".$editor_name."' class='form-control'>".$row[$editor_name]."</textarea>";
                                ?>
                              </div>
                            </div>

                            <div class="form-group row">
                              <label for="gmt_temp" class="col-sm-2 col-form-label">매매 시 특이사항</label>
                              <div class="col-sm-10 form-validate">
                                <?php
                                $editor_name = 'gmt_temp';
                                echo "<textarea name='".$editor_name."' id='".$editor_name."' class='form-control'>".$row[$editor_name]."</textarea>";
                                ?>
                              </div>
                            </div>

                            <div class="form-group row margin-top-30">
                              <label for="gmt_yeyaglyul" class="col-sm-2 col-form-label">예약률</label>
                              <div class="col-sm-10 form-validate">
                                <input type="text" name="gmt_yeyaglyul" id="gmt_yeyaglyul" value="<?=$row['gmt_yeyaglyul']?>" placeholder="예약률 입력" class="form-control">
                              </div>
                            </div>


                            <div class="form-group row margin-top-30">
                              <label for="gmt_golf_name" class="col-sm-2 col-form-label">회원예약율</label>
                              <div class="col-sm-10">

                                <div class="form-check form-check-inline">

                                  <?php
                                  $checked_reserv_types = explode('|:|', $row['gmt_reservation']);
                                  foreach($arr_gmt_reservation_type as $key=>$reservation) {
                                    ?>
                                    <div class="custom-control custom-checkbox mr-4">
                                      <input type="checkbox" class="custom-control-input" name="gmt_reservation[]" id="gmt_reservation<?php echo $key?>" value="<?php echo $key?>" <?= in_array($key, $checked_reserv_types) ? 'checked' : '' ?> />
                                      <label class="custom-control-label" for="gmt_reservation<?php echo $key?>"><?php echo $reservation?></label>
                                    </div>

                                  <?php } ?>

                                </div>

                              </div>
                            </div>


                            <div class="form-group row">
                              <label for="gmt_document" class="col-sm-2 col-form-label">준비서류</label>
                              <div class="col-sm-10 form-validate">
                                <?php
                                $editor_name = 'gmt_document';
                                echo "<textarea name='".$editor_name."' id='".$editor_name."' class='form-control'>".$row[$editor_name]."</textarea>";
                                ?>
                              </div>
                            </div>




                            <div class="form-group row justify-content-center margin-top-30">
                              <button type="button"  onclick="history.go(-1);" class="btn btn-outline-secondary mx-1" >목록</button>
                              <button type="submit" class="btn btn-secondary" ><?=$_act_txt?></button>
                            </div>
                          </div>

                          <div class="tab-pane fade " id="step-5" role="tabpanel" aria-labelledby="step-tab-5">


                            <div class="form-group row">
                              <label for="w_image" class="col-sm-2 col-form-label">사진 (최대 10개)</label>
                              <div class="col-sm-10">
                                <div class="upload-container" id="sortableContainer">
                                  <div class="upload-box" id="uploadTrigger">
                                    <div class="plus">+</div>
                                    <div class="text">Upload</div>
                                  </div>
                                </div>
                                <input type="file" class="filepond d-none" multiple>
                                <small class="form-text ">(이미지 사이즈 : 720x324)</small>
                              </div>

                            </div>


                            <div class="form-group row justify-content-center margin-top-30">

                              <button type="button"  onclick="history.go(-1);" class="btn btn-outline-secondary mx-1" >목록</button>
                              <button type="submit" class="btn btn-secondary" ><?=$_act_txt?></button>
                            </div>
                          </div>

                        </div>

                    </form>



                    <script>

                        $(document).ready(function () {
                            const $table = $('#greenfee_table tbody');

                            $('#add_row').on('click', function () {
                                const newRow = `
                                      <tr>
                                        <td class="text-center"><input type="text" name="gmgf_name[]" class="form-control" placeholder="직접입력" /></td>
                                        <td class="text-center"><input type="text" name="gmgf_weekday[]" class="form-control" placeholder="직접입력" /></td>
                                        <td class="text-center"><input type="text" name="gmgf_weekend[]" class="form-control" placeholder="직접입력" /></td>
                                        <td class="text-center"><button type="button" class="btn btn-danger btn-sm btn-remove">삭제</button></td>
                                      </tr>`;
                                $table.append(newRow);
                            });

                            $(document).on('click', '.btn-remove', function () {
                                const $rows = $('#greenfee_table tbody tr');
                                const $currentRow = $(this).closest('tr');
                                if ($rows.length > 10) {
                                    $currentRow.remove();
                                } else {
                                    $currentRow.find('input[type="text"]').val('');
                                }
                            });



                            const $myeongtable = $('#myeong_table tbody');

                            $('#add_myeong').on('click', function () {
                                const newRow = `
                                      <tr>
                                        <td class="text-center"><input type="text" name="gmmf_name[]" class="form-control" placeholder="직접입력" /></td>
                                        <td class="text-center"><input type="text" name="gmmf_price[]" class="form-control" placeholder="직접입력" /></td>
                                        <td class="text-center"><input type="text" name="gmmt_info[]" class="form-control" placeholder="직접입력" /></td>
                                        <td class="text-center"><button type="button" class="btn btn-danger btn-sm btn-remove-myeong">삭제</button></td>
                                      </tr>`;
                                $myeongtable.append(newRow);
                            });

                            $(document).on('click', '.btn-remove-myeong', function () {

                                const $rows = $('#myeong_table tbody tr');s
                                const $currentRow = $(this).closest('tr');
                                if ($rows.length > 10) {
                                    $currentRow.remove();
                                } else {
                                    $currentRow.find('input[type="text"]').val('');
                                }
                            });
                        });

                        function onPush(e){
                            const gmtIdx = e.dataset.gmtIdx;
                            if(!gmtIdx || gmtIdx==''){
                                app.toastr.showError('잘못된 접근입니다. 새로고침 후 다시 시도해주세요.');
                                return false;
                            }

                            const formData = new FormData();
                            formData.append('act', 'onPush');
                            formData.append('gmt_idx', gmtIdx);


                            $.confirm({
                                title: '변경 알림 푸시 전송',
                                content: "알림 전송하시겠습니까?",
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
                                                url: './update.php',
                                                type: 'POST',
                                                data: formData,
                                                dataType: 'json',
                                                processData: false,
                                                contentType: false,
                                                beforeSend: () => $('#splinner_modal').modal('show'),
                                                success: (response) => {
                                                    $('#splinner_modal').modal('hide');
                                                    console.log(response)
                                                    if(response.success) {
                                                        app.toastr.showSuccess(response.message, response.redirect);
                                                    } else {
                                                        app.toastr.showError(response.message);
                                                    }
                                                },
                                                error: (xhr, status, error) => {
                                                    $('#splinner_modal').modal('hide');
                                                    console.error(error)
                                                    app.toastr.showError(error);
                                                }
                                            });

                                        },
                                    },
                                },
                            });






                        }

                        $(document).ready(function() {



                            // FileUploader 초기화
                            const uploader = createFileUploader({
                                container: '.upload-container',
                                trigger: '#uploadTrigger',
                                filepondElement: '.filepond',
                                maxFiles: 10,
                                maxFileSize: '5MB',
                                allowedFileTypes: ['image/jpeg', 'image/png', 'image/jpg'],
                                imageMinWidth: 100,
                                imageMinHeight: 100,
                                imageMaxWidth: 4000,
                                imageMaxHeight: 4000,
                                ajaxUrl: './update.php'
                            });


                            // 폼 검증 및 제출 핸들러
                            const formHandler = {
                                init() {
                                    this.initializeValidation();
                                    this.setInitialValues();
                                },

                                initializeValidation() {
                                    $("#frm_form").validate({
                                        submitHandler: this.handleSubmit,
                                        rules: {
                                            gmt_golf_name: { required: true },
                                            gmt_local: { required: true },
                                            gmt_owdate: { required: true },
                                            gmt_thum: { required: true },
                                            gmt_hole: { required: true },
                                            gmt_person: { required: true },
                                            gmt_sale_price: { required: true },
                                            gmt_hp: { required: true },
                                            gmt_zip: { required: true },
                                            gmt_add1: { required: true },
                                            gmt_add2: { required: true },
                                            gmt_membership: { required: true },
                                            gmt_benefit: { required: true },
                                            gmt_point: { required: true },
                                            gmt_temp: { required: true },
                                            gmt_yeyaglyul: { required: true },
                                            gmt_document: { required: true },
                                            'gmt_user_type[]': {
                                                required: function (element) {
                                                    return $('input[name="gmt_user_type[]"]:checked').length === 0;
                                                }
                                            },
                                            'gmt_reservation[]': {
                                                required: function (element) {
                                                    return $('input[name="gmt_reservation[]"]:checked').length === 0;
                                                }
                                            }
                                        },
                                        messages: {
                                            gmt_golf_name: { required: '골프장명을 입력해주세요.' },
                                            gmt_local: { required: '지역을 선택헤주세요.' },
                                            gmt_owdate: { required: '개장일 입력해주세요.' },
                                            gmt_thum: { required: '썸네일을 입력해주세요.' },
                                            gmt_hole: { required: '홀수를 입력해주세요.' },
                                            gmt_person: { required: '회원수를 입력해주세요.' },
                                            gmt_sale_price: { required: '분양가를 입력해주세요.' },
                                            gmt_hp: { required: '전화번호를 입력해주세요.' },
                                            gmt_zip: { required: '주소를 입력해주세요.' },
                                            gmt_add1: { required: '주소를 입력해주세요.' },
                                            gmt_add2: { required: '주소를 입력해주세요.' },
                                            gmt_membership: { required: '회원구성을 입력해주세요.' },
                                            gmt_benefit: { required: '회원혜택을 입력해주세요.' },
                                            gmt_point: { required: '회원권특징을 입력해주세요.' },
                                            gmt_temp: { required: '매매시 특이사항을 입력해주세요.' },
                                            gmt_yeyaglyul: { required: '예약률을 입력해주세요.' },
                                            gmt_document: { required: '준비서류를 입력해주세요.' },

                                        },
                                        ignore: function(index, element) {
                                            return $(element).is(":hidden") && !$(element).hasClass("always-validate");
                                        },
                                        errorElement: 'span',
                                        errorPlacement: (error, element) => {
                                            error.addClass('invalid-feedback');
                                            if(element.attr('name') === 'gmt_user_type[]') {
                                                app.toastr.showError('회원권 종류를 1개 이상 선택해주세요.');
                                            } else if(element.attr('name') === 'gmt_user_type[]') {
                                                app.toastr.showError('회원예약율을 1개 이상 선택해주세요.');
                                            } else {
                                                element.closest('.form-validate').append(error);
                                            }

                                        },
                                        highlight: (element) => $(element).addClass('is-invalid'),
                                        unhighlight: (element) => $(element).removeClass('is-invalid')
                                    });
                                },
                                handleSubmit(form) {



                                    const formData = new FormData(form);
                                    formData.append('maxFiles', uploader.options.maxFiles);

                                    const imageOrder = uploader.getImageOrder();
                                    formData.append('image_order', JSON.stringify(imageOrder));

                                    // FilePond 파일들을 FormData에 추가
                                    const files = uploader.getPond().getFiles();

                                    const findFileById = (id) => {
                                        const found = files.find(f => f.id === id);
                                        return found ? found.file : null;
                                    };

                                    // console.log(imageOrder)
                                    // console.log(files)
                                    imageOrder.forEach((img, index) => {
                                        if (img.type === 'new') {
                                            const fileObj = findFileById(img.id);
                                            if (fileObj) {
                                                console.log(fileObj)
                                                formData.append(`membership${index + 1}`, fileObj);
                                            }
                                        }
                                    });

                                    // 삭제된 파일 정보 전송
                                    const removedFiles = uploader.getRemovedFiles(); // 삭제된 파일 번호 배열
                                    formData.append('removed_files', JSON.stringify(removedFiles));

                                    // console.log(...formData)
                                    $.ajax({
                                        url: './update.php',
                                        type: 'POST',
                                        data: formData,
                                        processData: false,
                                        contentType: false,
                                        beforeSend: () => $('#splinner_modal').modal('show'),
                                        success: (response) => {
                                            $('#splinner_modal').modal('hide');
                                            console.log(response)
                                            if(response.success) {
                                                app.toastr.showSuccess(response.message, response.redirect);
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
                                    return false;
                                },
                                setInitialValues() {

                                }
                            };

                            // 초기화
                            formHandler.init();

                            <? if($row['gmt_show']) { ?>
                              $('#gmt_show').val('<?=$row['gmt_show']?>');
                            <? } ?>
                    

                            const mt_idx = '<?php echo $row["gmt_idx"] ?? ""; ?>';

                            console.log(mt_idx)
                            if(mt_idx != '') {
                                uploader.loadImages(mt_idx);
                            }


                        });



                    </script>
                </div>
            </div>

        </div>
    </div>
    <!-- //END PAGE CONTENT CONTAINER -->
<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/foot.inc.php";
?>