<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";


$tbl_name = "visit_t";
$tbl_sum_name = "visit_sum_t";

if($_POST['act'] == "list") {
    unset($list);
    $DB->pageLimit = $_POST['obj_limit_num'];
    $pg = $_POST['obj_pg'];
    if ($_POST['obj_sdate'] && $_POST['obj_edate']) {
      $DB->where('a1.vi_date BETWEEN ? AND ?', [$_POST['obj_sdate'], $_POST['obj_edate']]);
    }
    //정렬
    if ($_POST['obj_order_desc_asc'] == '1') {
      $DB->orderBy("a1.vi_id", "desc");
    } else {
      $DB->orderBy("a1.vi_id", "asc");
    }
    $DB->setTrace(true); // 쿼리 추적 활성화
    $list = $DB->arraybuilder()->paginate($tbl_name." a1", $pg, '*');
    $debug = $DB->trace; // 모든 쿼리 정보 배열로 반환
    //print_r($debug);

    //페이징
    $n_page = $DB->totalPages;
    $counts = $DB->totalCount;
    $counts = $counts - (($pg - 1) * $_POST['obj_limit_num']);
    ?>
    <div class="table-responsive margin-top-20">
        <table class="table table-striped table-bordered margin-bottom-20" id="listTable" style="min-width: 800px">
            <thead class="thead-dark">
                <tr>
                    <th class="text-center" style="width:80px;">
                      IP
                    </th>
                    <th class="text-center">
                      접속경로
                    </th>
                    <th class="text-center">
                      브라우저
                    </th>
                    <th class="text-center">
                      OS
                    </th>
                    <th class="text-center">
                      접속기기
                    </th>
                    <th class="text-center">
                      일시
                    </th>

                </tr>
            </thead>
            <tbody>
            <?php
            if ($list) {
                foreach ($list as $row) {

                  $brow = $row['vi_browser'];
                  if(!$brow)
                    $brow = get_brow($row['vi_agent']);

                  $os = $row['vi_os'];
                  if(!$os)
                    $os = get_os($row['vi_agent']);

                  $device = $row['vi_device'];

                  $link = '';
                  $link2 = '';
                  $referer = '';
                  $title = '';
                  if ($row['vi_referer']) {

                    $referer = $row['vi_referer'];
                    $referer = urldecode($referer);


                    $title = str_replace(array('<', '>', '&'), array("&lt;", "&gt;", "&amp;"), $referer);
                    $link = '<a href="'.$row['vi_referer'].'" target="_blank">';
                    $link = str_replace('&', "&amp;", $link);
                    $link2 = '</a>';
                  }


                  $ip = $row['vi_ip'];

                  if ($brow == '기타') { $brow = '<span title="'.$row['vi_agent'].'">'.$brow.'</span>'; }
                  if ($os == '기타') { $os = '<span title="'.$row['vi_agent'].'">'.$os.'</span>'; }


                  ?>
                <tr  data-id="<?=$row['idx']?>">
                  <td class="text-center"  ><?php echo $ip ?></td>
                  <td class="text-center"  style="cursor:pointer"><?php echo $link ?><?php echo $title ?><?php echo $link2 ?></td>
                  <td class="text-center" ><?php echo $brow ?></td>
                  <td class="text-center" ><?php echo $os ?></td>
                  <td class="text-center" ><?php echo $device; ?></td>
                  <td class="text-center" ><?php echo $row['vi_date'] ?> <?php echo $row['vi_time'] ?></td>



                </tr>
                <?php
                    $counts--;
                }
            } else {
                ?>
                <tr>
                    <td colspan="6" class="text-center"><b>자료가 없습니다.</b></td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </div>

<?php
    if($n_page > 1) {
        echo page_listing_xhr($pg, $n_page, 'f_get_box_mng_list');
    }


}
else if($_POST['act'] == "domain") {
  unset($list);
  $DB->pageLimit = $_POST['obj_limit_num'];
  $pg = $_POST['obj_pg'];
  if ($_POST['obj_sdate'] && $_POST['obj_edate']) {
    $DB->where('a1.vi_date BETWEEN ? AND ?', [$_POST['obj_sdate'], $_POST['obj_edate']]);
  }
  //정렬
  if ($_POST['obj_order_desc_asc'] == '1') {
    $DB->orderBy("a1.vi_id", "desc");
  } else {
    $DB->orderBy("a1.vi_id", "asc");
  }
  $DB->setTrace(true); // 쿼리 추적 활성화
  $list = $DB->arraybuilder()->paginate($tbl_name." a1", $pg, '*');
  $debug = $DB->trace; // 모든 쿼리 정보 배열로 반환
  //print_r($debug);

  //페이징
  $n_page = $DB->totalPages;
  $counts = $DB->totalCount;
  $counts = $counts - (($pg - 1) * $_POST['obj_limit_num']);

  $sum_count = 0;
  if($list) {
    foreach($list as $row) {
      $str = $row['vi_referer'];
      preg_match("/^http[s]*:\/\/([\.\-\_0-9a-zA-Z]*)\//", $str, $match);
      $s = isset($match[1]) ? $match[1] : 0;
      $s = preg_replace("/^(www\.|search\.|dirsearch\.|dir\.search\.|dir\.|kr\.search\.|myhome\.)(.*)/", "\\2", $s);

      if( isset($arr[$s]) ){
        $arr[$s]++;
      } else {
        $arr[$s] = 1;
      }

      if ($arr[$s] > $max) $max = $arr[$s];

      $sum_count++;
    }
  }

  $i = 0;
  $k = 0;
  $save_count = -1;
  $tot_count = 0;
  ?>
  <div class="table-responsive margin-top-20">
    <table class="table table-striped table-bordered margin-bottom-20" id="listTable" style="min-width: 800px">
      <thead class="thead-dark">
      <tr>
        <th class="text-center" style="width:80px;">
          순위
        </th>
        <th class="text-center">
          접속도메인
        </th>
        <th class="text-center">
          그래프
        </th>
        <th class="text-center">
          접속자수
        </th>
        <th class="text-center">
          비율
        </th>

      </tr>
      </thead>
      <tbody>
      <?php
      if (count($arr)) {
        arsort($arr);
        foreach ($arr as $key=>$value) {

          $count = $arr[$key];
          if ($save_count != $count) {
            $i++;
            $no = $i;
            $save_count = $count;
          } else {
            $no = '';
          }

          if (!$key) {
            $link = '';
            $link2 = '';
            $key = '직접';
          } else {
            $link = '<a href="./list.php?domain='.$key.'">';
            $link2 = '</a>';
          }

          $rate = ($count / $sum_count * 100);
          $s_rate = number_format($rate, 1);


          ?>
          <tr  >
            <td class="text-center"  ><?php echo $no ?></td>
            <td class="text-center"><?php echo $link ?><?php echo $key ?><?php echo $link2 ?></td>
            <td class="text-center" >
              <div class="progress progress-md">
                <div class="progress-bar bg-success progress-bar-striped" role="progressbar" style="width: <?php echo $s_rate ?>%" aria-valuenow="<?php echo $s_rate ?>" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
            </td>
            <td class="text-center" ><?php echo $count ?></td>
            <td class="text-center" ><?php echo $s_rate; ?></td>
          </tr>
          <?php
          $counts--;
        }
      } else {
        ?>
        <tr>
          <td colspan="5" class="text-center"><b>자료가 없습니다.</b></td>
        </tr>
        <?php
      }
      ?>
      </tbody>
      <tfoot>
      <tr>
        <td colspan="3" class="text-center">합계</td>
        <td class="text-center"><strong><?php echo $sum_count ?></strong></td>
        <td class="text-center">100%</td>
      </tr>
      </tfoot>
    </table>
  </div>

  <?php
  if($n_page > 1) {
    echo page_listing_xhr($pg, $n_page, 'f_get_box_mng_list');
  }


}
else if($_POST['act'] == "browser") {
  unset($list);
  $DB->pageLimit = $_POST['obj_limit_num'];
  $pg = $_POST['obj_pg'];
  if ($_POST['obj_sdate'] && $_POST['obj_edate']) {
    $DB->where('a1.vi_date BETWEEN ? AND ?', [$_POST['obj_sdate'], $_POST['obj_edate']]);
  }
  //정렬
  if ($_POST['obj_order_desc_asc'] == '1') {
    $DB->orderBy("a1.vi_id", "desc");
  } else {
    $DB->orderBy("a1.vi_id", "asc");
  }
  $DB->setTrace(true); // 쿼리 추적 활성화
  $list = $DB->arraybuilder()->paginate($tbl_name." a1", $pg, '*');
  $debug = $DB->trace; // 모든 쿼리 정보 배열로 반환
  //print_r($debug);

  //페이징
  $n_page = $DB->totalPages;
  $counts = $DB->totalCount;
  $counts = $counts - (($pg - 1) * $_POST['obj_limit_num']);

  $sum_count = 0;
  if($list) {
    foreach($list as $row) {
      $s = $row['vi_browser'];
      if(!$s)
        $s = get_brow($row['vi_agent']);

      if( isset($arr[$s]) ){
        $arr[$s]++;
      } else {
        $arr[$s] = 1;
      }

      if ($arr[$s] > $max) $max = $arr[$s];

      $sum_count++;
    }
  }

  $i = 0;
  $k = 0;
  $save_count = -1;
  $tot_count = 0;
  ?>
  <div class="table-responsive margin-top-20">
    <table class="table table-striped table-bordered margin-bottom-20" id="listTable" style="min-width: 800px">
      <thead class="thead-dark">
      <tr>
        <th class="text-center" style="width:80px;">
          순위
        </th>
        <th class="text-center">
          브라우저
        </th>
        <th class="text-center">
          그래프
        </th>
        <th class="text-center">
          접속자수
        </th>
        <th class="text-center">
          비율
        </th>

      </tr>
      </thead>
      <tbody>
      <?php
      if (count($arr)) {
        arsort($arr);
        foreach ($arr as $key=>$value) {

          $count = $arr[$key];
          if ($save_count != $count) {
            $i++;
            $no = $i;
            $save_count = $count;
          } else {
            $no = '';
          }

          if (!$key) {
            $link = '';
            $link2 = '';
            $key = '직접';
          } else {
            $link = '<a href="./list.php?domain='.$key.'">';
            $link2 = '</a>';
          }

          $rate = ($count / $sum_count * 100);
          $s_rate = number_format($rate, 1);


          ?>
          <tr  >
            <td class="text-center"  ><?php echo $no ?></td>
            <td class="text-center"><?php echo $key ?></td>
            <td class="text-center" >
              <div class="progress progress-md">
                <div class="progress-bar bg-success progress-bar-striped" role="progressbar" style="width: <?php echo $s_rate ?>%" aria-valuenow="<?php echo $s_rate ?>" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
            </td>
            <td class="text-center" ><?php echo $count ?></td>
            <td class="text-center" ><?php echo $s_rate; ?></td>
          </tr>
          <?php
          $counts--;
        }
      } else {
        ?>
        <tr>
          <td colspan="5" class="text-center"><b>자료가 없습니다.</b></td>
        </tr>
        <?php
      }
      ?>
      </tbody>
      <tfoot>
      <tr>
        <td colspan="3" class="text-center">합계</td>
        <td class="text-center"><strong><?php echo $sum_count ?></strong></td>
        <td class="text-center">100%</td>
      </tr>
      </tfoot>
    </table>
  </div>

  <?php
  if($n_page > 1) {
    echo page_listing_xhr($pg, $n_page, 'f_get_box_mng_list');
  }


}
else if($_POST['act'] == "os") {
  unset($list);
  $DB->pageLimit = $_POST['obj_limit_num'];
  $pg = $_POST['obj_pg'];
  if ($_POST['obj_sdate'] && $_POST['obj_edate']) {
    $DB->where('a1.vi_date BETWEEN ? AND ?', [$_POST['obj_sdate'], $_POST['obj_edate']]);
  }
  //정렬
  if ($_POST['obj_order_desc_asc'] == '1') {
    $DB->orderBy("a1.vi_id", "desc");
  } else {
    $DB->orderBy("a1.vi_id", "asc");
  }
  $DB->setTrace(true); // 쿼리 추적 활성화
  $list = $DB->arraybuilder()->paginate($tbl_name." a1", $pg, '*');
  $debug = $DB->trace; // 모든 쿼리 정보 배열로 반환
  //print_r($debug);

  //페이징
  $n_page = $DB->totalPages;
  $counts = $DB->totalCount;
  $counts = $counts - (($pg - 1) * $_POST['obj_limit_num']);

  $sum_count = 0;
  if($list) {
    foreach($list as $row) {
      $s = $row['vi_os'];
      if(!$s)
        $s = get_os($row['vi_agent']);

      if( isset($arr[$s]) ){
        $arr[$s]++;
      } else {
        $arr[$s] = 1;
      }

      if ($arr[$s] > $max) $max = $arr[$s];

      $sum_count++;
    }
  }

  $i = 0;
  $k = 0;
  $save_count = -1;
  $tot_count = 0;
  ?>
  <div class="table-responsive margin-top-20">
    <table class="table table-striped table-bordered margin-bottom-20" id="listTable" style="min-width: 800px">
      <thead class="thead-dark">
      <tr>
        <th class="text-center" style="width:80px;">
          순위
        </th>
        <th class="text-center">
          운영체제
        </th>
        <th class="text-center">
          그래프
        </th>
        <th class="text-center">
          접속자수
        </th>
        <th class="text-center">
          비율
        </th>

      </tr>
      </thead>
      <tbody>
      <?php
      if (count($arr)) {
        arsort($arr);
        foreach ($arr as $key=>$value) {

          $count = $arr[$key];
          if ($save_count != $count) {
            $i++;
            $no = $i;
            $save_count = $count;
          } else {
            $no = '';
          }

          if (!$key) {
            $link = '';
            $link2 = '';
            $key = '직접';
          } else {
            $link = '<a href="./list.php?domain='.$key.'">';
            $link2 = '</a>';
          }

          $rate = ($count / $sum_count * 100);
          $s_rate = number_format($rate, 1);


          ?>
          <tr  >
            <td class="text-center"  ><?php echo $no ?></td>
            <td class="text-center"><?php echo $key ?></td>
            <td class="text-center" >
              <div class="progress progress-md">
                <div class="progress-bar bg-success progress-bar-striped" role="progressbar" style="width: <?php echo $s_rate ?>%" aria-valuenow="<?php echo $s_rate ?>" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
            </td>
            <td class="text-center" ><?php echo $count ?></td>
            <td class="text-center" ><?php echo $s_rate; ?></td>
          </tr>
          <?php
          $counts--;
        }
      } else {
        ?>
        <tr>
          <td colspan="5" class="text-center"><b>자료가 없습니다.</b></td>
        </tr>
        <?php
      }
      ?>
      </tbody>
      <tfoot>
      <tr>
        <td colspan="3" class="text-center">합계</td>
        <td class="text-center"><strong><?php echo $sum_count ?></strong></td>
        <td class="text-center">100%</td>
      </tr>
      </tfoot>
    </table>
  </div>

  <?php
  if($n_page > 1) {
    echo page_listing_xhr($pg, $n_page, 'f_get_box_mng_list');
  }


}
else if($_POST['act'] == "device") {
  unset($list);
  $DB->pageLimit = $_POST['obj_limit_num'];
  $pg = $_POST['obj_pg'];
  if ($_POST['obj_sdate'] && $_POST['obj_edate']) {
    $DB->where('a1.vi_date BETWEEN ? AND ?', [$_POST['obj_sdate'], $_POST['obj_edate']]);
  }
  //정렬
  if ($_POST['obj_order_desc_asc'] == '1') {
    $DB->orderBy("a1.vi_id", "desc");
  } else {
    $DB->orderBy("a1.vi_id", "asc");
  }
  $DB->setTrace(true); // 쿼리 추적 활성화
  $list = $DB->arraybuilder()->paginate($tbl_name." a1", $pg, '*');
  $debug = $DB->trace; // 모든 쿼리 정보 배열로 반환
  //print_r($debug);

  //페이징
  $n_page = $DB->totalPages;
  $counts = $DB->totalCount;
  $counts = $counts - (($pg - 1) * $_POST['obj_limit_num']);

  $sum_count = 0;
  if($list) {
    foreach($list as $row) {
      $s = $row['vi_device'];
      if(!$s)
        $s = get_os($row['vi_agent']);

      if( isset($arr[$s]) ){
        $arr[$s]++;
      } else {
        $arr[$s] = 1;
      }

      if ($arr[$s] > $max) $max = $arr[$s];

      $sum_count++;
    }
  }

  $i = 0;
  $k = 0;
  $save_count = -1;
  $tot_count = 0;
  ?>
  <div class="table-responsive margin-top-20">
    <table class="table table-striped table-bordered margin-bottom-20" id="listTable" style="min-width: 800px">
      <thead class="thead-dark">
      <tr>
        <th class="text-center" style="width:80px;">
          순위
        </th>
        <th class="text-center">
          접속기기
        </th>
        <th class="text-center">
          그래프
        </th>
        <th class="text-center">
          접속자수
        </th>
        <th class="text-center">
          비율
        </th>

      </tr>
      </thead>
      <tbody>
      <?php
      if (count($arr)) {
        arsort($arr);
        foreach ($arr as $key=>$value) {

          $count = $arr[$key];
          if ($save_count != $count) {
            $i++;
            $no = $i;
            $save_count = $count;
          } else {
            $no = '';
          }

          if (!$key) {
            $link = '';
            $link2 = '';
            $key = '직접';
          } else {
            $link = '<a href="./list.php?domain='.$key.'">';
            $link2 = '</a>';
          }

          $rate = ($count / $sum_count * 100);
          $s_rate = number_format($rate, 1);


          ?>
          <tr  >
            <td class="text-center"  ><?php echo $no ?></td>
            <td class="text-center"><?php echo $key ?></td>
            <td class="text-center" >
              <div class="progress progress-md">
                <div class="progress-bar bg-success progress-bar-striped" role="progressbar" style="width: <?php echo $s_rate ?>%" aria-valuenow="<?php echo $s_rate ?>" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
            </td>
            <td class="text-center" ><?php echo $count ?></td>
            <td class="text-center" ><?php echo $s_rate; ?></td>
          </tr>
          <?php
          $counts--;
        }
      } else {
        ?>
        <tr>
          <td colspan="5" class="text-center"><b>자료가 없습니다.</b></td>
        </tr>
        <?php
      }
      ?>
      </tbody>
      <tfoot>
      <tr>
        <td colspan="3" class="text-center">합계</td>
        <td class="text-center"><strong><?php echo $sum_count ?></strong></td>
        <td class="text-center">100%</td>
      </tr>
      </tfoot>
    </table>
  </div>

  <?php
  if($n_page > 1) {
    echo page_listing_xhr($pg, $n_page, 'f_get_box_mng_list');
  }


}
else if($_POST['act'] == "hour") {
  unset($list);
  $DB->pageLimit = $_POST['obj_limit_num'];
  $pg = $_POST['obj_pg'];
  if ($_POST['obj_sdate'] && $_POST['obj_edate']) {
    $DB->where('a1.vi_date BETWEEN ? AND ?', [$_POST['obj_sdate'], $_POST['obj_edate']]);
  }
  //정렬
  if ($_POST['obj_order_desc_asc'] == '1') {
    $DB->orderBy("a1.vi_id", "desc");
  } else {
    $DB->orderBy("a1.vi_id", "asc");
  }
  $DB->setTrace(true); // 쿼리 추적 활성화
  $list = $DB->arraybuilder()->paginate($tbl_name." a1", $pg, 'SUBSTRING(a1.vi_time,1,2) as vi_hour, count(a1.vi_id) as cnt');
  $debug = $DB->trace; // 모든 쿼리 정보 배열로 반환
  //print_r($debug);

  //페이징
  $n_page = $DB->totalPages;
  $counts = $DB->totalCount;
  $counts = $counts - (($pg - 1) * $_POST['obj_limit_num']);

  $sum_count  = 0;
  if($list) {
    $i=0;
    foreach($list as $row) {
      $arr[$row['vi_hour']] = $row['cnt'];

      if ($row['cnt'] > $max) $max = $row['cnt'];

      $sum_count += $row['cnt'];
      $i++;
    }
  }

  $k = 0;
  ?>
  <div class="table-responsive margin-top-20">
    <table class="table table-striped table-bordered margin-bottom-20" id="listTable" style="min-width: 800px">
      <thead class="thead-dark">
      <tr>
        <th class="text-center" style="width:80px;">
          시간
        </th>
        <th class="text-center">
          그래프
        </th>
        <th class="text-center">
          접속자수
        </th>
        <th class="text-center">
          비율
        </th>

      </tr>
      </thead>
      <tbody>
      <?php
      if ($i) {
        for ($i=0; $i<24; $i++) {

          $hour = sprintf("%02d", $i);
          $count = isset($arr[$hour]) ? (int) $arr[$hour] : 0;

          $rate = ($count / $sum_count * 100);
          $s_rate = number_format($rate, 1);


          ?>
          <tr  >
            <td class="text-center"  ><?php echo $hour ?></td>
            <td class="text-center" >
              <div class="progress progress-md">
                <div class="progress-bar bg-success progress-bar-striped" role="progressbar" style="width: <?php echo $s_rate ?>%" aria-valuenow="<?php echo $s_rate ?>" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
            </td>
            <td class="text-center" ><?php echo $count ?></td>
            <td class="text-center" ><?php echo $s_rate; ?></td>
          </tr>
          <?php
          $counts--;
        }
      } else {
        ?>
        <tr>
          <td colspan="4" class="text-center"><b>자료가 없습니다.</b></td>
        </tr>
        <?php
      }
      ?>
      </tbody>
      <tfoot>
      <tr>
        <td colspan="2" class="text-center">합계</td>
        <td class="text-center"><strong><?php echo $sum_count ?></strong></td>
        <td class="text-center">100%</td>
      </tr>
      </tfoot>
    </table>
  </div>

  <?php
  if($n_page > 1) {
    echo page_listing_xhr($pg, $n_page, 'f_get_box_mng_list');
  }


}
else if($_POST['act'] == "week") {
  unset($list);
  $DB->pageLimit = $_POST['obj_limit_num'];
  $pg = $_POST['obj_pg'];
  if ($_POST['obj_sdate'] && $_POST['obj_edate']) {
    $DB->where('vs_date BETWEEN ? AND ?', [$_POST['obj_sdate'], $_POST['obj_edate']]);
  }
  //정렬
  if ($_POST['obj_order_desc_asc'] == '1') {
    $DB->orderBy("WEEKDAY(vs_date)", "asc");
  } else {
    $DB->orderBy("WEEKDAY(vs_date)", "asc");
  }
  $DB->groupBy("WEEKDAY(vs_date)");
  $DB->setTrace(true); // 쿼리 추적 활성화
  $list = $DB->arraybuilder()->paginate($tbl_sum_name." ", $pg, 'WEEKDAY(vs_date) as weekday_date, SUM(vs_count) as cnt');
  $debug = $DB->trace; // 모든 쿼리 정보 배열로 반환
  //print_r($debug);

  //페이징
  $n_page = $DB->totalPages;
  $counts = $DB->totalCount;
  $counts = $counts - (($pg - 1) * $_POST['obj_limit_num']);

  $weekday = array ('월', '화', '수', '목', '금', '토', '일');
  $max = 0;
  $sum_count = 0;
  $arr = array();

  if($list) {
    $i=0;
    foreach($list as $row) {
      $arr[$row['weekday_date']] = $row['cnt'];

      $sum_count += $row['cnt'];
      $i++;
    }
  }

  $k = 0;
  ?>
  <div class="table-responsive margin-top-20">
    <table class="table table-striped table-bordered margin-bottom-20" id="listTable" style="min-width: 800px">
      <thead class="thead-dark">
      <tr>
        <th class="text-center" style="width:80px;">
          요일
        </th>
        <th class="text-center">
          그래프
        </th>
        <th class="text-center">
          접속자수
        </th>
        <th class="text-center">
          비율
        </th>

      </tr>
      </thead>
      <tbody>
      <?php
      if ($i) {
        for ($i=0; $i<7; $i++) {

          $count = isset($arr[$i]) ? (int) $arr[$i] : 0;

          $rate = ($count / $sum_count * 100);
          $s_rate = number_format($rate, 1);


          ?>
          <tr  >
            <td class="text-center"  ><?php echo $weekday[$i] ?></td>
            <td class="text-center" >
              <div class="progress progress-md">
                <div class="progress-bar bg-success progress-bar-striped" role="progressbar" style="width: <?php echo $s_rate ?>%" aria-valuenow="<?php echo $s_rate ?>" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
            </td>
            <td class="text-center" ><?php echo $count ?></td>
            <td class="text-center" ><?php echo $s_rate; ?></td>
          </tr>
          <?php
          $counts--;
        }
      } else {
        ?>
        <tr>
          <td colspan="4" class="text-center"><b>자료가 없습니다.</b></td>
        </tr>
        <?php
      }
      ?>
      </tbody>
      <tfoot>
      <tr>
        <td colspan="2" class="text-center">합계</td>
        <td class="text-center"><strong><?php echo $sum_count ?></strong></td>
        <td class="text-center">100%</td>
      </tr>
      </tfoot>
    </table>
  </div>

  <?php
  if($n_page > 1) {
    echo page_listing_xhr($pg, $n_page, 'f_get_box_mng_list');
  }


}
else if($_POST['act'] == "day") {
  unset($list);
  $DB->pageLimit = $_POST['obj_limit_num'];
  $pg = $_POST['obj_pg'];
  if ($_POST['obj_sdate'] && $_POST['obj_edate']) {
    $DB->where('vs_date BETWEEN ? AND ?', [$_POST['obj_sdate'], $_POST['obj_edate']]);
  }
  //정렬
  if ($_POST['obj_order_desc_asc'] == '1') {
    $DB->orderBy("vs_date", "asc");
  } else {
    $DB->orderBy("vs_date", "asc");
  }

  $DB->setTrace(true); // 쿼리 추적 활성화
  $list = $DB->arraybuilder()->paginate($tbl_sum_name." ", $pg, 'vs_date, vs_count as cnt');
  $debug = $DB->trace; // 모든 쿼리 정보 배열로 반환
  //print_r($debug);

  //페이징
  $n_page = $DB->totalPages;
  $counts = $DB->totalCount;
  $counts = $counts - (($pg - 1) * $_POST['obj_limit_num']);

  $weekday = array ('월', '화', '수', '목', '금', '토', '일');
  $max = 0;
  $sum_count = 0;
  $arr = array();

  if($list) {
    $i=0;
    foreach($list as $row) {
      $arr[$row['vs_date']] = $row['cnt'];

      if ($row['cnt'] > $max) $max = $row['cnt'];

      $sum_count += $row['cnt'];
    }
  }

  $k = 0;
  ?>
  <div class="table-responsive margin-top-20">
    <table class="table table-striped table-bordered margin-bottom-20" id="listTable" style="min-width: 800px">
      <thead class="thead-dark">
      <tr>
        <th class="text-center" style="width:180px;">
          일
        </th>
        <th class="text-center">
          그래프
        </th>
        <th class="text-center" style="width:180px;">
          접속자수
        </th>
        <th class="text-center" style="width:180px;">
          비율
        </th>

      </tr>
      </thead>
      <tbody>
      <?php
      if (count($arr)) {
        foreach ($arr as $key=>$value) {

          $count = $value;

          $rate = ($count / $sum_count * 100);
          $s_rate = number_format($rate, 1);


          ?>
          <tr  >
            <td class="text-center"  ><a href="./list.php?sel_search_sdate=<?php echo $key ?>&amp;sel_search_edate=<?php echo $key ?>"><?php echo $key ?></a></td>
            <td class="text-center" >
              <div class="progress progress-md">
                <div class="progress-bar bg-success progress-bar-striped" role="progressbar" style="width: <?php echo $s_rate ?>%" aria-valuenow="<?php echo $s_rate ?>" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
            </td>
            <td class="text-center" ><?php echo $count ?></td>
            <td class="text-center" ><?php echo $s_rate; ?></td>
          </tr>
          <?php
          $counts--;
        }
      } else {
        ?>
        <tr>
          <td colspan="4" class="text-center"><b>자료가 없습니다.</b></td>
        </tr>
        <?php
      }
      ?>
      </tbody>
      <tfoot>
      <tr>
        <td colspan="2" class="text-center">합계</td>
        <td class="text-center"><strong><?php echo $sum_count ?></strong></td>
        <td class="text-center">100%</td>
      </tr>
      </tfoot>
    </table>
  </div>

  <?php
  if($n_page > 1) {
    echo page_listing_xhr($pg, $n_page, 'f_get_box_mng_list');
  }


}
else if($_POST['act'] == "month") {
  unset($list);
  $DB->pageLimit = $_POST['obj_limit_num'];
  $pg = $_POST['obj_pg'];
  if ($_POST['obj_sdate'] && $_POST['obj_edate']) {
    $DB->where('vs_date BETWEEN ? AND ?', [$_POST['obj_sdate'], $_POST['obj_edate']]);
  }
  //정렬
  if ($_POST['obj_order_desc_asc'] == '1') {
    $DB->orderBy("vs_month", "desc");
  } else {
    $DB->orderBy("vs_month", "asc");
  }
  $DB->groupBy("vs_month");
  $DB->setTrace(true); // 쿼리 추적 활성화
  $list = $DB->arraybuilder()->paginate($tbl_sum_name." ", $pg, 'SUBSTRING(vs_date,1,7) as vs_month, SUM(vs_count) as cnt');
  $debug = $DB->trace; // 모든 쿼리 정보 배열로 반환
  //print_r($debug);

  //페이징
  $n_page = $DB->totalPages;
  $counts = $DB->totalCount;
  $counts = $counts - (($pg - 1) * $_POST['obj_limit_num']);

  $weekday = array ('월', '화', '수', '목', '금', '토', '일');
  $max = 0;
  $sum_count = 0;
  $arr = array();

  if($list) {
    $i=0;
    foreach($list as $row) {
      $arr[$row['vs_month']] = $row['cnt'];

      if ($row['cnt'] > $max) $max = $row['cnt'];

      $sum_count += $row['cnt'];
    }
  }

  $k = 0;
  ?>
  <div class="table-responsive margin-top-20">
    <table class="table table-striped table-bordered margin-bottom-20" id="listTable" style="min-width: 800px">
      <thead class="thead-dark">
      <tr>
        <th class="text-center" style="width:180px;">
          월
        </th>
        <th class="text-center">
          그래프
        </th>
        <th class="text-center" style="width:180px;">
          접속자수
        </th>
        <th class="text-center" style="width:180px;">
          비율
        </th>

      </tr>
      </thead>
      <tbody>
      <?php
      if (count($arr)) {
        foreach ($arr as $key=>$value) {

          $count = $value;

          $rate = ($count / $sum_count * 100);
          $s_rate = number_format($rate, 1);


          ?>
          <tr  >
            <td class="text-center"  ><a href="./list.php?sel_search_sdate=<?php echo $key ?>-01&amp;sel_search_edate=<?php echo $key ?>-31"><?php echo $key ?></a></td>
            <td class="text-center" >
              <div class="progress progress-md">
                <div class="progress-bar bg-success progress-bar-striped" role="progressbar" style="width: <?php echo $s_rate ?>%" aria-valuenow="<?php echo $s_rate ?>" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
            </td>
            <td class="text-center" ><?php echo number_format($count) ?></td>
            <td class="text-center" ><?php echo $s_rate; ?></td>
          </tr>
          <?php
          $counts--;
        }
      } else {
        ?>
        <tr>
          <td colspan="4" class="text-center"><b>자료가 없습니다.</b></td>
        </tr>
        <?php
      }
      ?>
      </tbody>
      <tfoot>
      <tr>
        <td colspan="2" class="text-center">합계</td>
        <td class="text-center"><strong><?php echo $sum_count ?></strong></td>
        <td class="text-center">100%</td>
      </tr>
      </tfoot>
    </table>
  </div>

  <?php
  if($n_page > 1) {
    echo page_listing_xhr($pg, $n_page, 'f_get_box_mng_list');
  }


}
else if($_POST['act'] == "year") {
  unset($list);
  $DB->pageLimit = $_POST['obj_limit_num'];
  $pg = $_POST['obj_pg'];
  if ($_POST['obj_sdate'] && $_POST['obj_edate']) {
    $DB->where('vs_date BETWEEN ? AND ?', [$_POST['obj_sdate'], $_POST['obj_edate']]);
  }
  //정렬
  if ($_POST['obj_order_desc_asc'] == '1') {
    $DB->orderBy("vs_year", "desc");
  } else {
    $DB->orderBy("vs_year", "asc");
  }
  $DB->groupBy("vs_year");
  $DB->setTrace(true); // 쿼리 추적 활성화
  $list = $DB->arraybuilder()->paginate($tbl_sum_name." ", $pg, 'SUBSTRING(vs_date,1,4) as vs_year, SUM(vs_count) as cnt');
  $debug = $DB->trace; // 모든 쿼리 정보 배열로 반환
  //print_r($debug);

  //페이징
  $n_page = $DB->totalPages;
  $counts = $DB->totalCount;
  $counts = $counts - (($pg - 1) * $_POST['obj_limit_num']);

  $weekday = array ('월', '화', '수', '목', '금', '토', '일');
  $max = 0;
  $sum_count = 0;
  $arr = array();

  if($list) {
    $i=0;
    foreach($list as $row) {
      $arr[$row['vs_year']] = $row['cnt'];

      if ($row['cnt'] > $max) $max = $row['cnt'];

      $sum_count += $row['cnt'];
    }
  }

  $k = 0;
  ?>
  <div class="table-responsive margin-top-20">
    <table class="table table-striped table-bordered margin-bottom-20" id="listTable" style="min-width: 800px">
      <thead class="thead-dark">
      <tr>
        <th class="text-center" style="width:180px;">
          년도
        </th>
        <th class="text-center">
          그래프
        </th>
        <th class="text-center" style="width:180px;">
          접속자수
        </th>
        <th class="text-center" style="width:180px;">
          비율
        </th>

      </tr>
      </thead>
      <tbody>
      <?php
      if (count($arr)) {
        foreach ($arr as $key=>$value) {

          $count = $value;

          $rate = ($count / $sum_count * 100);
          $s_rate = number_format($rate, 1);


          ?>
          <tr  >
            <td class="text-center"  ><a href="./list.php?sel_act=month&amp;sel_search_sdate=<?php echo $key ?>-01-01&amp;sel_search_edate=<?php echo $key ?>-12-31"><?php echo $key ?></a></td>
            <td class="text-center" >
              <div class="progress progress-md">
                <div class="progress-bar bg-success progress-bar-striped" role="progressbar" style="width: <?php echo $s_rate ?>%" aria-valuenow="<?php echo $s_rate ?>" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
            </td>
            <td class="text-center" ><?php echo number_format($count) ?></td>
            <td class="text-center" ><?php echo $s_rate; ?></td>
          </tr>
          <?php
          $counts--;
        }
      } else {
        ?>
        <tr>
          <td colspan="4" class="text-center"><b>자료가 없습니다.</b></td>
        </tr>
        <?php
      }
      ?>
      </tbody>
      <tfoot>
      <tr>
        <td colspan="2" class="text-center">합계</td>
        <td class="text-center"><strong><?php echo $sum_count ?></strong></td>
        <td class="text-center">100%</td>
      </tr>
      </tfoot>
    </table>
  </div>

  <?php
  if($n_page > 1) {
    echo page_listing_xhr($pg, $n_page, 'f_get_box_mng_list');
  }


}



include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
