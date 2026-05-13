<?php


function getGolfPrice($gmt_idx) {
  global $DB, $_SESSION, $CFG_TBL;

  // 즉시매도가 가져오기 - 판매
  $sql = "
        SELECT *
        FROM  {$CFG_TBL['golf_membership']['transaction']}  
        WHERE gmt_idx = ? AND gmtt_status = 1 AND gmtt_level = 2 AND gmtt_show = 'Y' AND gmtt_del = 'N'
        ORDER BY gmtt_hope_price DESC, gmtt_wdate ASC
        LIMIT ?
    ";
  $gmt_now_buy_price_info = $DB->rawQueryOne($sql, [$gmt_idx, 1]);

  $gmt_now_buy_price_idx = 0;
  $gmt_now_buy_price = 0;
  if($gmt_now_buy_price_info){
    $gmt_now_buy_price_idx = $gmt_now_buy_price_info['gmtt_idx'];
    $gmt_now_buy_price = $gmt_now_buy_price_info['gmtt_hope_price'];
  }

  //즉시매입가 가져오기 - 구매

  $sql = "
        SELECT *
        FROM  {$CFG_TBL['golf_membership']['transaction']}  
        WHERE gmt_idx = ? AND gmtt_status = 1 AND gmtt_level = 1 AND gmtt_show = 'Y' AND gmtt_del = 'N'
        ORDER BY gmtt_hope_price, gmtt_wdate ASC
        LIMIT ?
    ";
  $gmt_now_sale_price_info = $DB->rawQueryOne($sql, [$gmt_idx, 1]);
  $gmt_now_sale_price_idx = 0;
  $gmt_now_sale_price = 0;
  if($gmt_now_sale_price_info){
    $gmt_now_sale_price_idx = $gmt_now_sale_price_info['gmtt_idx'];
    $gmt_now_sale_price = $gmt_now_sale_price_info['gmtt_hope_price'];
  }


  //최근 매매 실거래가
  $sql = "
        SELECT *
        FROM  {$CFG_TBL['golf_membership']['transaction']}  
        WHERE gmt_idx = ? AND gmtt_status = 3 
        ORDER BY gmtt_edate DESC
        LIMIT ?
    ";
  $gmt_deal_price_info = $DB->rawQueryOne($sql, [$gmt_idx, 1]);
  $gmt_deal_price = 0;
  $gmt_conclusion_price = 0;
  if($gmt_deal_price_info){
    $gmt_deal_price = $gmt_deal_price_info['gmtt_hope_price'];
  }

  $gmt_conclusion_price = floor(($gmt_now_buy_price + $gmt_now_sale_price) / 2);


  $result = [
    'gmt_now_buy_price_idx' => $gmt_now_buy_price_idx,
    'gmt_now_buy_price' => $gmt_now_buy_price,
    'gmt_now_sale_price_idx' => $gmt_now_sale_price_idx,
    'gmt_now_sale_price' => $gmt_now_sale_price,
    'gmt_deal_price' => $gmt_deal_price,
    'gmt_conclusion_price' => $gmt_conclusion_price,
  ];


  return $result;


}