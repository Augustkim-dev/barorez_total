<?php



if (!function_exists('add_cash_action')) {
  function add_cash_action($param) {
    global $DB;

    if(!$param['mt_idx'] || !$param['cash']) {
      return false;
    }

    $mt_idx = $param['mt_idx'];
    $ot_gubun = $param['ot_gubun'] ?? '';
    $ot_code = $param['ot_code'] ?? '';
    $message = $param['message'] ?? '';

    $DB->where('idx', $mt_idx);
    $my = $DB->getOne('member_t');
    if(!$my){
      return false;
    }

    $point = 0;
    if($param['cash']){
      $point = (int) $param['cash'];
    }

    if(0>=$point){
      return false;
    }


    // 1. 사용내역 추가
    $date = date('Y-m-d H:i:s', time());
    $timestamp = strtotime($date." +365 days");
    $expire_date = date("Y-m-d H:i:s", $timestamp);

    unset($arr_query);
    $arr_query = array(
      "ot_gubun" => $ot_gubun,
      "ot_code" => $ot_code,
      "mt_idx" => $my['idx'],
      "point" => $point,
      'status' => "add",
      "regdate" => $date,
      "expired" => 0,
      "expire_date" => $expire_date,
      "message" => $message,
    );
    $_last_idx = $DB->insert('cash_history_t', $arr_query);



    $mt_point = $my['mt_point'] + $point;
    unset($arr_query);
    $arr_query = array(
      "mt_point" => $mt_point,
    );
    $DB->where('idx', $my['idx']);
    $DB->update('member_t', $arr_query);

    return true;
  }
}




if (!function_exists('use_cash_action')) {
  function use_cash_action($param) {
    global $DB;

    if(!$param['mt_idx'] || !$param['cash']) {
      return false;
    }

    $mt_idx = $param['mt_idx'];
    $cash = (int) $param['cash'];
    $ot_gubun = $param['ot_gubun'] ?? '';
    $ot_code = $param['ot_code'] ?? '';
    $message = $param['message'] ?? '';

    $DB->where('idx', $mt_idx);
    $my = $DB->getOne('member_t');
    $sum = $my['mt_point'];
    if($sum<$cash){
      return false;
    }

    // 1. 사용내역 추가
    $cash *= -1;
    $date = date('Y-m-d H:i:s', time());

    unset($arr_query);
    $arr_query = array(
      "ot_gubun" => $ot_gubun,
      "ot_code" => $ot_code,
      "mt_idx" => $mt_idx,
      "point" => $cash,
      'status' => "remove",
      "regdate" => $date,
      "expired" => 1,
      "expire_date" => $date,
      "message" => $message,
    );
    $_last_idx = $DB->insert('cash_history_t', $arr_query);


    // 2. 사용처리
    $remaining = abs($cash);
    $add_query = " AND status = 'add' AND expired <> 1 ";
    $add_query .= ' AND mt_idx = "' . $mt_idx . '" ';
    $add_query .= ' AND point > use_point ';  // 남은 포인트가 있는 경우.
    $query1 = "SELECT * FROM cash_history_t WHERE 1  ".$add_query." ORDER BY expire_date ASC ";
    $list = $DB->query($query1);
    foreach($list as $row){
      $available = $row['point'] - $row['use_point'];

      if ($available >= $remaining) {
        // 현재 row에서 충분히 차감 가능할 경우
        $use_point = $row['use_point'] + $remaining;
        $udt = ['use_point' => $use_point];
        if ($use_point == $row['point']) {
          // 포인트가 모두 사용된 경우 expired 상태 업데이트
          $udt['expired'] = 100;
        }
        $DB->where('idx', $row['idx']);
        $DB->update('cash_history_t', $udt);
        break;
      } else {
        // 현재 row에서 일부만 차감 가능할 경우
        $udt = [
          'use_point' => $row['point'],
          'expired' => '100',
        ];
        $DB->where('idx', $row['idx']);
        $DB->update('cash_history_t', $udt);

        $remaining -= $available; // 남은포인트 차감하고 다음으로 넘김.
      }
    }


    $remain_point = $my['mt_point'] + $cash;
    unset($arr_query);
    $arr_query = array(
      "mt_point" => $remain_point,
    );
    $DB->where('idx', $my['idx']);
    $DB->update('member_t', $arr_query);

    return true;
  }
}


if (!function_exists('expired_cash_action')) {
  function expired_cash_action($mt_idx) {
    global $DB;

    $remain_point = 0;

    $DB->where('idx', $mt_idx);
    $my = $DB->getOne('member_t');

    // 기간만료 차감처리
    $add_query = " AND status = 'add' AND expired = 0 AND expire_date < now() ";
    $add_query .= ' AND mt_idx = "' . $my['idx'] . '" ';

    $query1 = "SELECT IFNULL(sum(point - use_point),0) as `expired_sum` FROM cash_history_t WHERE 1  ".$add_query."";
    $row = $DB->query($query1);

    if($row['expired_sum'] > 0){
      $expire_point = (int)$row['expired_sum'] * -1;
      //echo "<!-- pre expired_query1>";
      //print_r($expire_point);
      //echo "</pre --!>";

      $set = [
        'mt_idx' => $my['idx'],
        'point' => $expire_point,
        'status' => 'remove_expired',
        'expired' => '1',
        'regdate' => date('Y-m-d H:i:s', time()),
      ];
      $DB->insert('cash_history_t', $set);

      // 3. 기존 포인트(use_point)에서 만료 포인트 차감
      $remaining = abs($expire_point);
      $query2 = "SELECT * FROM cash_history_t WHERE status = 'add' AND expired = 0 AND mt_idx = '{$my['idx']}' ORDER BY expire_date ASC";
      $list = $DB->query($query2);
      foreach ($list as $row) {
        $available = $row['point'] - $row['use_point'];

        if ($available >= $remaining) {
          // 현재 row에서 충분히 차감 가능할 경우
          $use_point = $row['use_point'] + $remaining;
          $udt = ['use_point' => $use_point];

          if ($use_point == $row['point']) {
            $udt['expired'] = 100; // 만료 처리
          }

          $DB->where('idx', $row['idx']);
          $DB->update('cash_history_t', $udt);
          break;
        } else {
          // 현재 row에서 일부만 차감 가능할 경우
          $udt = [
            'use_point' => $row['point'],
            'expired' => '100',
          ];

          $DB->where('idx', $row['idx']);
          $DB->update('cash_history_t', $udt);

          $remaining -= $available; // 남은 포인트 차감하고 다음으로 넘김.
        }
      }


      $remain_point = $my['mt_point'] + $expire_point;
      unset($arr_query);
      $arr_query = array(
        "mt_point" => $remain_point,
      );
      $DB->where('idx', $my['idx']);
      $DB->update('member_t', $arr_query);
    }




    return $remain_point;
  }
}


if (!function_exists('check_cash_usable_action')) {
  function check_cash_usable_action($ot_code) {
    global $DB, $_SESSION;
    // 사용 기록 있는지 확인
    $add_query = " AND status = 'add' AND expired = 0 AND expire_date > now() ";
    $add_query .= " AND ot_code = '" . $ot_code . "' ";

    $query1 = "SELECT IFNULL(point,0) as `point`, IFNULL(sum(point - use_point),0) as `use_sum` FROM cash_history_t WHERE 1  ".$add_query."";
    $row = $DB->query($query1);
    if($row['point']>0 && $row['point'] == $row['use_sum']){
      return true;
    } else {
      return  false;
    }
  }
}
