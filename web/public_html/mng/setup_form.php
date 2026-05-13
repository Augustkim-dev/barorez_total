<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu='99';
$chk_sub_menu='2';
include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.menu.inc.php";
//include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.sidebar.inc.php";

$DB->where('idx', '1');
$row = $DB->getone('setup_t');
?>


        <!-- PAGE CONTENT CONTAINER -->
        <div class="content" id="content">
            <!-- PAGE HEADING -->
            <div class="page-heading">
                <div class="page-heading__container">
                    <div class="icon">
                        <span class="li-register"></span>
                    </div>
                    <h1 class="title">기본환경설정</h1>
                    <p class="caption">
                        홈페이지 기본환경을 관리 할 수 있습니다.
                    </p>
                </div>
                <nav aria-label="breadcrumb" role="navigation">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/mng">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="#">설정</a></li>
                        <li class="breadcrumb-item active">기본환경설정</li>
                    </ol>
                </nav>
            </div>
            <!-- //END PAGE HEADING -->
            <div class="container-fluid">
                <div class="card">
                    <div class="card-body">
                        <h4 id="rw-fe-basic">기본설정</h4>
                        <p class="subtitle margin-bottom-20">
                            &nbsp;
                        </p>
                        <form method="post" name="frm_form" id="frm_form" action="./setup_update.php" target="hidden_ifrm" enctype="multipart/form-data">
                            <input type="hidden" name="act" id="act" value="update" />
                            <div class="form-group row">
                                <label for="st_company_name" class="col-sm-2 col-form-label">회사명</label>
                                <div class="col-sm-10">
                                    <input type="text" name="st_company_name" id="st_company_name" value="<?=$row['st_company_name']?>" class="form-control" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="st_company_boss" class="col-sm-2 col-form-label">대표자</label>
                                <div class="col-sm-10">
                                    <input type="text" name="st_company_boss" id="st_company_boss" value="<?=$row['st_company_boss']?>" class="form-control" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="st_privacy_admin" class="col-sm-2 col-form-label">개인정보책임관리자</label>
                                <div class="col-sm-10">
                                    <input type="text" name="st_privacy_admin" id="st_privacy_admin" value="<?=$row['st_privacy_admin']?>" class="form-control" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="st_customer_tel" class="col-sm-2 col-form-label">고객센터 연락처</label>
                                <div class="col-sm-10">
                                    <input type="text" name="st_customer_tel" id="st_customer_tel" value="<?=$row['st_customer_tel']?>" class="form-control" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="st_customer_email" class="col-sm-2 col-form-label">고객센터 이메일</label>
                                <div class="col-sm-10">
                                    <input type="text" name="st_customer_email" id="st_customer_email" value="<?=$row['st_customer_email']?>" class="form-control" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="st_customer_time" class="col-sm-2 col-form-label">고객센터 시간</label>
                                <div class="col-sm-10">
                                    <input type="text" name="st_customer_time" id="st_customer_time" value="<?=$row['st_customer_time']?>" class="form-control" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="st_company_add" class="col-sm-2 col-form-label">우편번호</label>
                                <div class="col-sm-10">
                                    <input type="text" name="st_company_zipcode" id="st_company_zipcode" value="<?=$row['st_company_zipcode']?>" class="form-control" maxlength="5" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="st_company_add" class="col-sm-2 col-form-label">주소</label>
                                <div class="col-sm-10">
                                    <input type="text" name="st_company_add" id="st_company_add" value="<?=$row['st_company_add']?>" class="form-control" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="st_company_num1" class="col-sm-2 col-form-label">사업자등록번호</label>
                                <div class="col-sm-10">
                                    <input type="text" name="st_company_num1" id="st_company_num1" value="<?=$row['st_company_num1']?>" class="form-control" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="st_company_num1" class="col-sm-2 col-form-label">통신판매업 신고번호</label>
                                <div class="col-sm-10">
                                    <input type="text" name="st_company_num2" id="st_company_num2" value="<?=$row['st_company_num2']?>" class="form-control" />
                                </div>
                            </div>
                            <div class="form-group row">
                              <label for="st_id_filter" class="col-sm-2 col-form-label">아이디 필터링</label>
                              <div class="col-sm-10">
                                <textarea name="st_id_filter" id="st_id_filter" class="form-control" rows="3"><?=$row['st_id_filter']?></textarea>
                                <span class="form-text">입력된 단어가 포함된 아이디는 가입할 수 없습니다. 단어와 단어 사이는 ,로 구분합니다.</span>
                              </div>
                            </div>
                            <div class="form-group row">
                                <label for="st_filter" class="col-sm-2 col-form-label">단어 필터링</label>
                                <div class="col-sm-10">
                                    <textarea name="st_filter" id="st_filter" class="form-control" rows="3"><?=$row['st_filter']?></textarea>
                                    <span class="form-text">입력된 단어가 포함된 내용은 게시할 수 없습니다. 단어와 단어 사이는 ,로 구분합니다.</span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="st_possible_ip" class="col-sm-2 col-form-label">접근가능 IP</label>
                                <div class="col-sm-4">
                                    <textarea name="st_possible_ip" id="st_possible_ip" class="form-control" rows="3"><?=$row['st_possible_ip']?></textarea>
                                    <span class="form-text">입력된 IP의 컴퓨터만 접근할 수 있습니다.</span>
                                    <span class="form-text">123.123.+ 도 입력 가능. (엔터로 구분)</span>
                                </div>

                                <label for="st_intercept_ip" class="col-sm-2 col-form-label">접근차단 IP</label>
                                <div class="col-sm-4">
                                    <textarea name="st_intercept_ip" id="st_intercept_ip" class="form-control" rows="3"><?=$row['st_intercept_ip']?></textarea>
                                    <span class="form-text">입력된 IP의 컴퓨터는 접근할 수 없음.</span>
                                    <span class="form-text">123.123.+ 도 입력 가능. (엔터로 구분)</span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="st_analytics" class="col-sm-2 col-form-label">방문자분석 스크립트</label>
                                <div class="col-sm-10">
                                    <textarea name="st_analytics" id="st_analytics" class="form-control" rows="3"><?=$row['st_analytics']?></textarea>
                                    <span class="form-text">방문자분석 스크립트 코드를 입력합니다. 예) 구글 애널리틱스</span>
                                    <span class="form-text">관리자 페이지에서는 이 코드를 사용하지 않습니다.</span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="st_analytics" class="col-sm-2 col-form-label">추가 메타태그</label>
                                <div class="col-sm-10">
                                    <textarea name="st_add_meta" id="st_add_meta" class="form-control" rows="3"><?=$row['st_add_meta']?></textarea>
                                    <span class="form-text">추가로 사용하실 meta 태그를 입력합니다.</span>
                                    <span class="form-text">관리자 페이지에서는 이 코드를 사용하지 않습니다.</span>
                                </div>
                            </div>

                          <div class="form-group row">
                            <label for="st_bank_name" class="col-sm-2 col-form-label">예금자 계좌</label>
                            <div class="col-sm-3">
                              <select name="st_bank_name" id="st_bank_name" class="form-control select-simple">
                                <option value="">선택하세요</option>
                                <?php
                                foreach($arr_ct_refund_bank as $key=>$bank) {
                                  $selected = ($row['st_bank_name'] == $key) ? 'selected' : '';
                                  echo "<option value=\"{$key}\" {$selected}>{$bank}</option>";
                                }
                                ?>

                              </select>

                            </div>
                            <div class="col-sm-4">
                              <input type="text" name="st_bank_num" id="st_bank_num" value="<?=$row['st_bank_num']?>" class="form-control" />

                            </div>
                            <div class="col-sm-3">
                              <input type="text" name="st_bank_user" id="st_bank_user" value="<?=$row['st_bank_user']?>" class="form-control" />

                            </div>
                          </div>



                            <div class="form-group row">
                              <label for="st_aos_version" class="col-sm-2 col-form-label">안드로이드 앱 버전</label>
                              <div class="col-sm-6">
                                <input type="text" name="st_aos_version" id="st_aos_version" value="<?=$row['st_aos_version']?>" class="form-control" />
                              </div>
                              <div class="col-sm-4">
                                <div class="form-check form-check-inline">
                                  <div class="custom-control custom-checkbox mr-3">
                                    <input type="radio" class="custom-control-input" name="st_aos_update" id="st_aos_update1" value="1" <?=($row['st_aos_update'] == '1' ? 'checked' : '')?> />
                                    <label class="custom-control-label" for="st_aos_update1">선택 업데이트</label>
                                  </div>
                                  <div class="custom-control custom-checkbox mr-3">
                                    <input type="radio" class="custom-control-input" name="st_aos_update" id="st_aos_update2" value="2" <?=($row['st_aos_update'] == '2' ? 'checked' : '')?> />
                                    <label class="custom-control-label" for="st_aos_update2">강제 업데이트</label>
                                  </div>
                                </div>

                              </div>
                            </div>

                            <div class="form-group row">
                              <label for="st_ios_version" class="col-sm-2 col-form-label">iOS 앱 버전</label>
                              <div class="col-sm-6">
                                <input type="text" name="st_ios_version" id="st_ios_version" value="<?=$row['st_ios_version']?>" class="form-control" />
                              </div>
                              <div class="col-sm-4">
                                <div class="form-check form-check-inline">
                                  <div class="custom-control custom-checkbox mr-3">
                                    <input type="radio" class="custom-control-input" name="st_ios_update" id="st_ios_update1" value="1" <?=($row['st_ios_update'] == '1' ? 'checked' : '')?> />
                                    <label class="custom-control-label" for="st_ios_update1">선택 업데이트</label>
                                  </div>
                                  <div class="custom-control custom-checkbox mr-3">
                                    <input type="radio" class="custom-control-input" name="st_ios_update" id="st_ios_update2" value="2" <?=($row['st_ios_update'] == '2' ? 'checked' : '')?> />
                                    <label class="custom-control-label" for="st_ios_update2">강제 업데이트</label>
                                  </div>
                                </div>

                              </div>
                            </div>


                            <div class="form-group row justify-content-center margin-top-30">
                                <button class="btn btn-secondary" type="submit">확인</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- //END PAGE CONTENT CONTAINER -->
<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/foot.inc.php";
?>
