<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";

/* 쿠팡 파트너스 API */

date_default_timezone_set("GMT+0");
$datetime = date("ymd").'T'.date("His").'Z';

// Replace with your own ACCESS_KEY and SECRET_KEY
$ACCESS_KEY = "f78a4020-d811-42bb-a0cb-ccb14eaa0f4c";
$SECRET_KEY = "29420b6378fd146a7745db7173fe6201f76ad000";

$algorithm = "HmacSHA256";

$method = "GET";
$method2 = "POST";

$limit = "100";


//$arr = [1001,1002,1003,1004,1005,1006,1007,1008,1009,1010,1011,1012,1013,1014,1015,1016,1017,1018,1019,1020,1021,1024,1025,1026,1029];
$arr = [1001];
$ca_id = "1002";
//for($i = 0; $i < count($arr); $i++) {
	//switch ($arr[$i]) {
	switch ($ca_id) {
		case "1001":
			$categoryName = "여성패션"; break;
		case "1002":
			$categoryName = "남성패션"; break;
		case "1003":
			$categoryName = "베이비패션"; break;
		case "1004":
			$categoryName = "여아패션"; break;
		case "1005":
			$categoryName = "남아패션"; break;
		case "1006":
			$categoryName = "스포츠패션"; break;
		case "1007":
			$categoryName = "신발"; break;
		case "1008":
			$categoryName = "가방·잡화"; break;
		case "1009":
			$categoryName = "명품패션"; break;
		case "1010":
			$categoryName = "뷰티"; break;
		case "1011":
			$categoryName = "출산·유아동"; break;
		case "1012":
			$categoryName = "식품"; break;
		case "1013":
			$categoryName = "주방용품"; break;
		case "1014":
			$categoryName = "생활용품"; break;
		case "1015":
			$categoryName = "홈인테리어"; break;
		case "1016":
			$categoryName = "가전디지털"; break;
		case "1017":
			$categoryName = "스포츠·레저"; break;
		case "1018":
			$categoryName = "자동차용품"; break;
		case "1019":
			$categoryName = "도서·음반·DVD"; break;
		case "1020":
			$categoryName = "완구·취미"; break;
		case "1021":
			$categoryName = "문구·오피스"; break;
		case "1024":
			$categoryName = "헬스·건강식품"; break;
		case "1025":
			$categoryName = "국내여행"; break;
		case "1026":
			$categoryName = "해외여행"; break;
		case "1027":
			$categoryName = "반려동물용품"; break;
		default:
	}
	
	$path = "/v2/providers/affiliate_open_api/apis/openapi/v1/products/bestcategories/".$ca_id."?limit=".$limit;
	$message = $datetime.$method.str_replace("?", "", $path);


	$signature = hash_hmac('sha256', $message, $SECRET_KEY);
	$authorization  = "CEA algorithm=HmacSHA256, access-key=".$ACCESS_KEY.", signed-date=".$datetime.", signature=".$signature;

	$url = 'https://api-gateway.coupang.com'.$path;


	$curl = curl_init();        
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type:  application/json;charset=UTF-8", "Authorization:".$authorization));        
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $strjson);
	$result = curl_exec($curl);
	$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	curl_close($curl);

	//echo($httpcode);
	echo($result);
	//exit;

	$i=1;
	$json = json_decode($result, true);
	foreach($json['data'] as $val) {

		if ($i % 10 == 0) {
            // 50개씩 보내고 몇초간 쉰다.
            // 잘 보내지지 않는다고 생각되면 이 부분의 수치를 높여주세요.
            sleep(10);
        }

		$productId = $val['productId'];
		$productName = $val['productName'];
		$productPrice = $val['productPrice'];
		$productImage = $val['productImage'];
		$productUrl = $val['productUrl'];

		$isKeyword = $val['keyword'];
		//$categoryName = $val['categoryName'];
        $iRank = $val['rank'];
		$isRocket = $val['isRocket'];
        $isFreeShipping = $val['isFreeShipping'];

		//echo $productId;

		$path2 = "/v2/providers/affiliate_open_api/apis/openapi/v1/deeplink";

		$message2 = $datetime.$method2.str_replace("?", "", $path2);

		$signature2 = hash_hmac('sha256', $message2, $SECRET_KEY);
		$authorization  = "CEA algorithm=HmacSHA256, access-key=".$ACCESS_KEY.", signed-date=".$datetime.", signature=".$signature2;

		$url2 = 'https://api-gateway.coupang.com'.$path2;

		$strjson2 = '
			{
			  "subId" : "ejinicokr",
			  "coupangUrls": [
				"https://www.coupang.com/vp/products/'.$productId.'"
			  ]
			}
		';
		
		$curl = curl_init();        
		curl_setopt($curl, CURLOPT_URL, $url2);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method2);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type:  application/json;charset=UTF-8", "Authorization:".$authorization));        
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $strjson2);
		$result2 = curl_exec($curl);
		$httpcode2 = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		//echo($httpcode2);
		//echo($result2);
		$json2 = json_decode($result2);

		$originalUrl = $json2->data[0]->originalUrl;
		$shortenUrl = $json2->data[0]->shortenUrl;
		$landingUrl = $json2->data[0]->landingUrl;

	
		$productContent  = "<p style=\"line-height: 30px;font-size:20px !important;color:#2E7D32;\">온라인에서 보다보면 결정장애로 인해 무엇을 골라야할지 어려움이 많죠.</p>";
		$productContent .= "<p style=\"line-height: 30px;font-size:16px !important; color:#5d82d1\">그런 분들에게 도움이 되고자 쿠팡 베스트 아이템을 최저가 위주로 추천해드리고자 합니다.</p>";
		//$productContent .= "<p style=\"line-height: 30px;font-size:20;color:#2E7D32;\">[".$categoryName." 쿠팡 특가] 오늘 쿠팡세일 요건어때요?</p>";
		//$productContent .= "<p style=\"line-height: 30px;font-size:16; color:#5d82d1\">".$categoryName."</p>";
		$productContent .= "<p style=\"line-height: 30px;font-size:16px !important; color:#ec8b00\">".$productName."</p>";
		$productContent .= "<p style=\"line-height: 30px;font-size:18px !important; color:#c2175c\">판매가격 : ".number_format($productPrice)."</p>";
		$productContent .= "<p style=\"line-height: 35px;\"></p>";
		$productContent .= "<p style=\"line-height: 35px;\"></p>";
		$productContent .= "<p style=\"line-height: 35px;\"><img src=\"$productImage\"></p>";
		$productContent .= "<p style=\"line-height: 35px;\"></p>";
		$productContent .= "<p style=\"line-height: 30px;\">제 추천이 여러분에게 좋은 정보가 되었으면 좋겠어요;-)</p>";
		$productContent .= "<p style=\"line-height: 30px;\">아! 후기가 중요한데, 글이 길어지는 것 같아 링크 남겨둘게요!</p>";
		$productContent .= "<p style=\"line-height: 30px;font-size:18px !important; color:#c2175c\"><a href=\"".$shortenUrl."\" target=\"_blank\">".$productName." 구경하러 가기</a></p>";
		$productContent .= "<p style=\"line-height: 30px;\"></p>";
		$productContent .= "<p style=\"line-height: 30px;\">위 상품의 표시된 가격은 ".TIME_YMDHIS."일자 기준가격입니다. 해당 가격은 변동이 있을수 있습니다.</p>";
		$productContent .= "<p style=\"line-height: 30px;\"></p>";
		$productContent .= "<p style=\"line-height: 30px;font-size:12px !important;color:#8a8a8a\">해당 포스팅은 제휴마케팅 포함 광고료 커미션을 지급 받을 수 있습니다.</p>";

		//$productContent = "<p><img src=\"$productImage\"></p>";

	    //$productContent = preg_replace("#[\\\]+$#", "", $productContent);
		$productContent = preg_replace('/\"/','\"', $productContent);
		//echo $productContent;
		//echo $i." : ".$productName.":".$productUrl.":".$shortenUrl."<br>";


        unset($arr_query);
        $arr_query = array(
            "productId" => $productId,
            "categoryName" => $categoryName,
            "productName" => $productName,
            "productContent" => $productContent,
            "productImage" => $productImage,
            "productPrice" => $productPrice,
            "link1" => $originalUrl,
            "link2" => $shortenUrl,
            "iskeyword" => $isKeyword,
            "isRank" => $iRank,
            "isRocket" => $isRocket,
            "isFreeShipping" => $keyword,
            "ctGpt" => 'N',
            "ctHit" => 1,
            "ctWdate" => $DB->now(),
        );

        $_last_idx = $DB->insert('coupang_t', $arr_query);


        /*
		$wr_num = get_next_num($write_table);
		$wr_reply = '';

		$sql = " select wr_1 from g5_write_coupang where wr_1 = '{$productId}' and wr_7 = '{$ca_id}'";
		$row = sql_fetch($sql);
		if ($row['wr_1']) {
		}else{
			$sql = " insert into g5_write_coupang
					set  wr_num = '$wr_num',
						 wr_reply = '$wr_reply',
						 wr_comment = 0,
						 ca_name = '$categoryName',
						 wr_option = 'html1',
						 wr_subject = '$productName',
						 wr_content = '$productContent',
						 wr_link1 = '$originalUrl',
						 wr_link2 = '$shortenUrl',
						 wr_link1_hit = 0,
						 wr_link2_hit = 0,
						 wr_hit = 0,
						 wr_good = 0,
						 wr_nogood = 0,
						 mb_id = '{$member['mb_id']}',
						 wr_password = '$wr_password',
						 wr_name = '$wr_name',
						 wr_email = '$wr_email',
						 wr_homepage = '$wr_homepage',
						 wr_datetime = '".G5_TIME_YMDHIS."',
						 wr_last = '".G5_TIME_YMDHIS."',
						 wr_ip = '{$_SERVER['REMOTE_ADDR']}',
						 wr_1 = '$productId',
						 wr_2 = '$productName',
						 wr_3 = '$productPrice',
						 wr_4 = '$productImage',
						 wr_5 = '$originalUrl',
						 wr_6 = '$keyword',
						 wr_7 = '$ca_id',
						 wr_8 = '$rank',
						 wr_9 = '$isRocket',
						 wr_10 = '$search' ";
			sql_query($sql);
			//echo $sql."<br><br>";

			$wr_id = sql_insert_id();

			// 부모 아이디에 UPDATE
			sql_query(" update $write_table set wr_parent = '$wr_id' where wr_id = '$wr_id' ");

			// 새글 INSERT
			sql_query(" insert into {$g5['board_new_table']} ( bo_table, wr_id, wr_parent, bn_datetime, mb_id ) values ( 'coupang', '{$wr_id}', '{$wr_id}', '".G5_TIME_YMDHIS."', '{$member['mb_id']}' ) ");

			// 게시글 1 증가
			sql_query("update {$g5['board_table']} set bo_count_write = bo_count_write + 1 where bo_table = 'coupang'");

			$succ_count++;
		}
        */

		$i++;
	}


	echo "DB 입력수 : ".$succ_count;

include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";