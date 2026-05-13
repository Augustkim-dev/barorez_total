<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$menu_map = [
  1 => '2',
  2 => '1',
  8 => '6'
];
$chk_menu = 1;
$chk_sub_menu= isset($menu_map[$_GET['sel_lv']]) ? $menu_map[$_GET['sel_lv']] : '3';
include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.menu.inc.php";
//include $_SERVER['DOCUMENT_ROOT']."/mng/inc/header.sidebar.inc.php";

$isDsiabled = "disabled";
$_act = "view";
$_act_txt = " 보기";


$DB->where('idx', $_GET['mt_idx']);
$row = $DB->getone($CFG_TBL['member']['default'], '*, idx as mt_idx');

$DB->where('mt_idx', $row['mt_idx']);
$DB->where('gmat_status', 2);
$DB->where('gmat_del', 'N');
$auths = $DB->get($CFG_TBL['golf_membership']['auth']);
$auth_cnt = $DB->count;


// 인증 회원권
$DB->join($CFG_TBL['golf_membership']['main']." a2", "a1.gmt_idx = a2.gmt_idx", "LEFT");
$DB->where("a1.mt_idx", $row['mt_idx']);
$DB->where("a1.gmat_del", "N");
$DB->where("a1.gmat_status", 2);
$DB->where("a2.gmt_del", "N");
$DB->orderBy("a1.gmat_wdate", "desc");
$auth_list = $DB->get($CFG_TBL['golf_membership']['auth']." a1");
//echo "<!-- pre getLastQuery>";
//print_r($DB->getLastQuery());
//echo "</pre --!>";

foreach ($auth_list as $key=>$auth) {
  $DB->where('ft_pidx', $auth['gmat_idx']);
  $DB->where("ft_type", "2");
  $DB->orderBy("ft_idx", "asc");
  $file_row = $DB->get($CFG_TBL['file']['default'], 1);
  $auth_list[$key]['gmt_img'] = !empty($file_row['ft_file']) ? $ct_golf_membership_url . '/' . $file_row['ft_file'] : $ct_no_img_x_url;
}

// 리뷰
$DB->where("a1.mt_idx", $row['mt_idx']);
$DB->where('a1.rt_del', 'N');
$DB->where('a2.gmt_del', 'N');
$DB->orderBy("a1.rt_wdate", "desc");
$select = "
        *, 
        a1.rt_idx as nt_idx,
        (
          SELECT COUNT(*) 
          FROM {$CFG_TBL['review']['like']} rlt 
          WHERE rlt.rt_idx = a1.rt_idx
        ) AS like_count
      ";

$table = "{$CFG_TBL['review']['default']} a1 
            LEFT JOIN {$CFG_TBL['golf_membership']['main']} a2 
            ON a1.gmt_idx = a2.gmt_idx";
$list = $DB->get($table, null, $select);

$review_list = [];
foreach ($list as $key=>$review) {
  $review_list[$key] = [
    'rt_idx'            => $review['rt_idx'],
    'gmt_golf_name'     => $review['gmt_golf_name'],
    'rt_content'        => $review['rt_content'],
    'rt_average_start'  => $review['rt_average_start'],
    'like_count'        => $review['like_count'],
    'rt_wdate'          => DateType($review['rt_wdate'], 4),
  ];
}


// 조인
$DB->where("a1.mt_idx", $row['mt_idx']);
$DB->where('a1.jt_del', 'N');
$DB->orderBy("a1.jt_wdate", "desc");
$select = "*";

$table = "{$CFG_TBL['join']['default']} as a1";
$list = $DB->get($table, null, $select);

$join_list = [];
foreach ($list as $key=>$join) {
  $join_list[$key] = [
    'jt_idx'            => $join['jt_idx'],
    'gmt_golf_name'     => $join['gmt_golf_name'],
    'jt_content'        => $join['jt_content'],
    'jt_jdate'          => DateType($join['jt_jdate'], 4),
    'jt_wdate'          => DateType($join['jt_wdate'], 4),
  ];
}

// 댓글
$DB->where("a1.mt_idx", $row['mt_idx']);
$DB->where('a1.rt_del', 'N');
$DB->where('a2.gmt_del', 'N');
$DB->orderBy("a1.rt_wdate", "desc");
$select = "*";
$table = "{$CFG_TBL['reply']['default']} a1 
            LEFT JOIN {$CFG_TBL['golf_membership']['main']} a2 
            ON a1.gmt_idx = a2.gmt_idx";
$list = $DB->get($table, null, $select);
$reply_list = [];
foreach ($list as $key=>$reply) {
  $level = '댓글';
  if($reply['rt_pidx'] != ''){
    $level = '답글';
  }
  $reply_list[$key] = [
    'rt_idx'            => $reply['rt_idx'],
    'rt_type'           => $arr_rt_type[$reply['rt_type']],
    'rt_level'          => $level,
    'rvt_content'       => $reply['rvt_content'],
  ];
}

// 구매/판매





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
        회원 등록, 수정, 삭제 등을 할 수 있습니다.
      </p>
    </div>
    <nav aria-label="breadcrumb" role="navigation">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="#"><?=$arr_mt_level[$_GET['sel_lv']]?>회원관리</a></li>
        <li class="breadcrumb-item active"><?=$arr_mt_level[$_GET['sel_lv']]?>회원</li>
      </ol>
    </nav>
  </div>
  <!-- //END PAGE HEADING -->
  <div class="container-fluid">
    <div class="card margin-bottom-0">

      <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
          <li class="nav-item"><a class="nav-link active" id="member-tab-1" data-toggle="tab" href="#member-1" role="tab" aria-controls="home" aria-selected="true">회원정보</a></li>

          <li class="nav-item"><a class="nav-link" id="member-tab-2" data-toggle="tab" href="#member-2" role="tab" aria-controls="member-2" aria-selected="false">인증회원권</a></li>
          <li class="nav-item"><a class="nav-link" id="member-tab-3" data-toggle="tab" href="#member-3" role="tab" aria-controls="member-3" aria-selected="false">리뷰</a></li>
          <li class="nav-item"><a class="nav-link" id="member-tab-4" data-toggle="tab" href="#member-4" role="tab" aria-controls="member-4" aria-selected="false">조인</a></li>
          <li class="nav-item"><a class="nav-link" id="member-tab-5" data-toggle="tab" href="#member-5" role="tab" aria-controls="member-5" aria-selected="false">댓글</a></li>
          <li class="nav-item"><a class="nav-link" id="member-tab-6" data-toggle="tab" href="#member-6" role="tab" aria-controls="member-6" aria-selected="false">구매/판매</a></li>

        </ul>
      </div>
      <div class="card-body">
        <!-- 판매 리스트-->
        <form name="frm_list" id="frm_list" onsubmit="return false;">
          <input type="hidden" name="act" value="sell_member_list" />
          <input type="hidden" name="obj_list" id="obj_list" value="sell_list_box" />
          <input type="hidden" name="obj_frm" id="obj_frm" value="frm_list" />
          <input type="hidden" name="obj_uri" id="obj_uri" value="./member_buysell_list.php" />
          <input type="hidden" name="obj_pg" id="obj_pg" value="1" />
          <input type="hidden" name="obj_limit_num" id="obj_limit_num" value="10" />
          <input type="hidden" name="obj_orderby" id="obj_orderby" value="" />
          <input type="hidden" name="obj_order_desc_asc" id="obj_order_desc_asc" value="1" />
          <input type="hidden" name="obj_sel_search" id="obj_sel_search" value="" />
          <input type="hidden" name="obj_search_txt" id="obj_search_txt" value="" />
          <input type="hidden" name="obj_search_status" id="obj_search_status" value="1" />
        </form>

        <!-- 구매 리스트-->
        <form name="buy_list" id="buy_list" onsubmit="return false;">
          <input type="hidden" name="act" value="buy_member_list" />
          <input type="hidden" name="list_v_list" id="list_v_list" value="buy_list_box" />
          <input type="hidden" name="list_v_frm" id="list_v_frm" value="buy_list" />
          <input type="hidden" name="list_v_uri" id="list_v_uri" value="./member_buysell_list.php" />
          <input type="hidden" name="list_v_pg" id="list_v_pg" value="1" />
          <input type="hidden" name="list_v_limit_num" id="list_v_limit_num" value="10" />
          <input type="hidden" name="list_v_orderby" id="list_v_orderby" value="" />
          <input type="hidden" name="list_v_order_desc_asc" id="list_v_order_desc_asc" value="1" />
          <input type="hidden" name="list_v_sel_search" id="list_v_sel_search" value="" />
          <input type="hidden" name="list_v_search_txt" id="list_v_search_txt" value="" />
          <input type="hidden" name="list_v_search_status" id="list_v_search_status" value="1" />
        </form>

        <form method="post" name="frm_form" id="frm_form" action="./member_update.php" target="hidden_ifrm" enctype="multipart/form-data">
          <input type="hidden" name="act" id="act" value="<?=$_act?>" />
          <input type="hidden" name="mt_idx" id="mt_idx" value="<?=$row['mt_idx']?>" />
          <input type="hidden" name="sel_lv" id="sel_lv" value="<?=$_GET['sel_lv']?>" />
          <div class="tab-content margin-top-15" id="myTabContent">
            <div class="tab-pane fade show active" id="member-1" role="tabpanel" aria-labelledby="member-tab-1">

              <?if($_GET['sel_lv']=='8'){?>
                <div class="form-group row">
                  <label for="mt_position" class="col-sm-2 col-form-label">직책 </label>
                  <div class="col-sm-10">
                    <input type="text" name="mt_position" id="mt_position" value="<?=$row['mt_position']?>" class="form-control" <?=$isDsiabled?>  />
                  </div>
                </div>
              <?}?>


              <div class="form-group row">
                <label for="mt_id" class="col-sm-2 col-form-label">아이디 </label>
                <div class="col-sm-4">
                  <input type="text" name="mt_id" id="mt_id" value="<?=$row['mt_id']?>" class="form-control" <?=$isDsiabled?> />
                </div>
                <label for="mt_name" class="col-sm-2 col-form-label">이름 </label>
                <div class="col-sm-4">
                  <input type="text" name="mt_name" id="mt_name" value="<?=$row['mt_name']?>" class="form-control" <?=$isDsiabled?> />
                </div>
              </div>

              <div class="form-group row">
                <label for="mt_birth" class="col-sm-2 col-form-label">생년월일 </label>
                <div class="col-sm-4">
                  <input type="text" name="mt_birth" id="mt_birth" value="<?=$row['mt_birth']?>" class="form-control" <?=$isDsiabled?> />
                </div>
                <label for="mt_gender" class="col-sm-2 col-form-label">성별 </label>
                <div class="col-sm-4">
                  <input type="text" name="mt_gender" id="mt_gender" value="<?= $row['mt_gender'] === 'M' ? '남성' : ($row['mt_gender'] === 'F' ? '여성' : '-') ?>" class="form-control" <?=$isDsiabled?> />
                </div>
              </div>

              <div class="form-group row">
                <label for="mt_hp" class="col-sm-2 col-form-label">휴대폰 번호 </label>
                <div class="col-sm-4">
                  <input type="text" name="mt_hp" id="mt_hp" value="<?=$row['mt_hp']?>" class="form-control" <?=$isDsiabled?> />
                </div>
                <label for="mt_email" class="col-sm-2 col-form-label">E-mail </label>
                <div class="col-sm-4">
                  <input type="text" name="mt_email" id="mt_email" value="<?=$row['mt_email']?>" class="form-control" <?=$isDsiabled?> />
                </div>
              </div>

              <div class="form-group row">
                <label class="col-sm-2 col-form-label">인증 회원권 </label>
                <div class="col-sm-4">
                  <input type="text"  value="<?=$auth_cnt?>" class="form-control" <?=$isDsiabled?> />
                </div>
                <label for="mt_wdate" class="col-sm-2 col-form-label">가입일 </label>
                <div class="col-sm-4">
                  <input type="text" name="mt_wdate" id="mt_wdate" value="<?=$row['mt_wdate']?>" class="form-control" <?=$isDsiabled?> />
                </div>
              </div>


              <div class="form-group row">
                <label for="mt_type" class="col-sm-2 col-form-label">가입유형</label>
                <div class="col-sm-10">
                  <select name="mt_type" id="mt_type" class="form-control" <?=$isDsiabled?> >
                    <?php
                    if ($_act == 'input') {
                      echo '<option value="1">'.$arr_mt_type[1].'</option>';
                    } else {
                      foreach ($arr_mt_type as $key => $value) {
                        $selected = ($row['mt_type']==$key)?'selected':'';
                        echo '<option value="'.$key.'" '.$selected.' >'.$value.'</option>';
                      }
                    }
                    ?>
                  </select>
                  <small class="form-text">가입유형은 일반, 카카오, 네이버, 구글, 애플 유형으로 구분됩니다.</small>
                </div>
              </div>
            </div>

            <div class="tab-pane fade" id="member-2" role="tabpanel" aria-labelledby="member-tab-2">
              <?php if (empty($auth_list)) : ?>
                <div class="text-center">등록된 내용이 없습니다.</div>
              <?php else : ?>s
                <div class="d-flex flex-wrap">
                  <?php
                    foreach ($auth_list as $row):
                      $prices = getGolfPrice($row['gmt_idx']);

                      //매입개수
                      $DB->where('gmt_idx', $row['gmt_idx']);
                      $DB->where('gmtt_del', 'N');
                      $DB->where('gmtt_level', '2');
                      $DB->where('gmtt_status', '1');
                      $rs = $DB->getone($CFG_TBL['golf_membership']['transaction'], 'count(*) as cnt');
                      $buy_count = $rs['cnt'] ?? 0;

                      //매물개수
                      $DB->where('gmt_idx', $row['gmt_idx']);
                      $DB->where('gmtt_del', 'N');
                      $DB->where('gmtt_level', '1');
                      $DB->where('gmtt_status', '1');
                      $rs = $DB->getone($CFG_TBL['golf_membership']['transaction'], 'count(*) as cnt');
                      $sell_count = $rs['cnt'] ?? 0;

                      //리뷰
                      $DB->where('gmt_idx', $row['gmt_idx']);
                      $DB->where('rt_del', 'N');
                      $rs = $DB->getone($CFG_TBL['review']['default'], 'count(*) as cnt');
                      $review_count = $rs['cnt'] ?? 0;


                  ?>
                    <div class="card m-2" style="width: 16rem; min-width: 16rem;">
                      <?php if (!empty($row['gmt_img'])): ?>
                        <img src="<?= htmlspecialchars($row['gmt_img']) ?>" class="card-img-top rounded mb-3" alt="회원권 이미지">
                      <?php endif; ?>

                      <div class="card-body">
                        <span class="badge badge-primary mb-2">
                          매매가능금액 <?= is_numeric($prices['gmt_conclusion_price']) ? number_format($prices['gmt_conclusion_price']) : $prices['gmt_conclusion_price'] ?>만원
                        </span>

                        <h5 class="card-title"><?= htmlspecialchars($row['gmt_golf_name']) ?></h5>

                        <div class="d-flex justify-content-between small mb-1">
                          <span>최근매매실거래가</span>
                          <span><?= is_numeric($prices['gmt_deal_price']) ? number_format($prices['gmt_deal_price']) : $prices['gmt_deal_price'] ?>만원</span>
                        </div>

                        <div class="d-flex justify-content-between text-muted small mb-1">
                          <span>즉시 매도가</span>
                          <span><?= is_numeric($prices['gmt_now_sale_price']) ? number_format($prices['gmt_now_sale_price']) : $prices['gmt_now_sale_price'] ?>만원</span>
                        </div>

                        <div class="d-flex justify-content-between text-muted small mb-3">
                          <span>즉시 매입가</span>
                          <span><?= is_numeric($prices['gmt_now_buy_price']) ? number_format($prices['gmt_now_buy_price']) : $prices['gmt_now_buy_price'] ?>만원</span>
                        </div>

                        <div class="d-flex justify-content-between text-muted small">
                          <span>매물 <?= number_format($sell_count) ?>건</span>
                          <span>매입 <?= number_format($buy_count) ?>건</span>
                          <span>리뷰 <?= number_format($review_count) ?>건</span>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </div>
            <div class="tab-pane fade" id="member-3" role="tabpanel" aria-labelledby="member-tab-3">
              <?php if (empty($review_list)) : ?>
                <div class="text-center">등록된 내용이 없습니다.</div>
              <?php else : ?>
                <div class="table-responsive">
                  <table class="table table-bordered table-hover text-center">
                    <thead class="thead-light">
                    <tr>
                      <th>골프장명</th>
                      <th>내용</th>
                      <th>평점</th>
                      <th>좋아요</th>
                      <th>등록일</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($review_list as $i => $review): ?>
                      <tr>
                        <td><?= htmlspecialchars($review['gmt_golf_name']) ?></td>
                        <td class="text-left"><?= nl2br(htmlspecialchars($review['rt_content'])) ?></td>
                        <td><?= number_format($review['rt_average_start'], 1) ?>점</td>
                        <td><?= number_format($review['like_count']) ?>개</td>
                        <td><?= htmlspecialchars($review['rt_wdate']) ?></td>
                      </tr>
                    <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              <?php endif; ?>
            </div>
            <div class="tab-pane fade" id="member-4" role="tabpanel" aria-labelledby="member-tab-4">
              <?php if (empty($join_list)) : ?>
                <div class="text-center">등록된 내용이 없습니다.</div>
              <?php else : ?>
                <div class="table-responsive">
                  <table class="table table-bordered table-hover text-center">
                    <thead class="thead-light">
                    <tr>
                      <th>골프장명</th>
                      <th>제목</th>
                      <th>초청일시</th>
                      <th>등록일</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($join_list as $i => $join): ?>
                      <tr>
                        <td><?= htmlspecialchars($join['gmt_golf_name']) ?></td>
                        <td class="text-left"><?= nl2br(htmlspecialchars($join['jt_content'])) ?></td>
                        <td><?= $join['jt_jdate'] ?></td>
                        <td><?= $join['jt_wdate'] ?></td>
                      </tr>
                    <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              <?php endif; ?>
            </div>
            <div class="tab-pane fade" id="member-5" role="tabpanel" aria-labelledby="member-tab-5">
              <?php if (empty($reply_list)) : ?>
                <div class="text-center">등록된 내용이 없습니다.</div>
              <?php else : ?>
                <div class="table-responsive">
                  <table class="table table-bordered table-hover text-center">
                    <thead class="thead-light">
                    <tr>
                      <th>타입</th>
                      <th>종류</th>
                      <th>내용</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($reply_list as $i => $reply): ?>
                      <tr>
                        <td><?= htmlspecialchars($reply['rt_type']) ?></td>
                        <td><?= htmlspecialchars($reply['rt_level']) ?></td>
                        <td class="text-left"><?= nl2br(htmlspecialchars($reply['rvt_content'])) ?></td>
                      </tr>
                    <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              <?php endif; ?>
            </div>
            <div class="tab-pane fade" id="member-6" role="tabpanel" aria-labelledby="member-tab-6">

              <div class="row">
                <div class="col-12 mt-2">
                  <div class="d-flex justify-content-start">
                    <?php foreach($arr_gmtt_status as $key=>$value) {?>
                      <?php
                      if($key == 4) continue;
                      $local_class_name = (1 == $key) ? 'btn-secondary' : 'btn-outline-secondary';
                      ?>
                      <button type="button" data-local="<?php echo $key?>" class="margin-right-5 local-search-btn btn <?php echo $local_class_name?>">
                        <?php echo $value?>
                      </button>
                    <?php }?>
                  </div>
                </div>
              </div>


              <div class="row">
                <div class="col-md-6">
                  <div id="sell_list_box"></div>
                </div>
                <div class="col-md-6">
                  <div id="buy_list_box"></div>
                </div>
              </div>


            </div>

          </div>
          <div class="form-group row justify-content-center margin-top-30">
            <button type="button"  onclick="history.go(-1);" class="btn btn-outline-secondary mx-1" >목록</button>
          </div>
        </form>
      </div>

      <script>
          $(document).ready(function () {
              var history_data = history.state;
              if(history_data) {
                  f_get_box_mng_list(history_data.page, '');
                  f_get_box_mng_second_list(history_data.page, '');
              } else {
                  f_get_box_mng_list('<?=$_GET['pg'] ? $_GET['pg'] : 1?>', '');
                  f_get_box_mng_second_list('<?=$_GET['pg'] ? $_GET['pg'] : 1?>', '');
              }
          });

          $(document).ready(function() {
              $('.local-search-btn').on('click', function(e){
                  let local = $(this).attr('data-local')

                  console.log(local)
                  $('.local-search-btn').removeClass('btn-secondary').addClass('btn-outline-secondary');
                  $(this).removeClass('btn-outline-secondary').addClass('btn-secondary');
                  $('#obj_search_status').val(local);
                  $('#list_v_search_status').val(local);
                  f_get_box_mng_list('1', '')
                  f_get_box_mng_second_list('1', '')
              })
          })
      </script>

    </div>
  </div>
</div>
<!-- //END PAGE CONTENT CONTAINER -->
<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/foot.inc.php";
?>
