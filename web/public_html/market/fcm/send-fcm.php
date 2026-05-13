<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";
header('Content-Type: application/json');

$tbl_name = "fcm_template_t";
$tbl_fcm_name = "fcm_t";
$tbl_alarm_name = "fcm_alarm_t";
$tbl_member_token_name = "member_fcm_token_t";
$tbl_member_name = "member_t";



if($_POST['act']=="send") {

  try {

    $title = $_POST['fcm_title'];
    $body = $_POST['fcm_body'];
    $user_ids = json_decode($_POST['user_ids'], true);

    // 필수 입력값 체크
    if(empty($_POST['fcm_title'])) {
      throw new Exception("제목을 입력하세요.");
    }
    if(empty($_POST['fcm_body'])) {
      throw new Exception("내용을 입력하세요.");
    }
    if (empty($user_ids) || !is_array($user_ids)) {
      throw new Exception("수신 대상 회원이 없습니다.");
    }


    // 트랜잭션 시작
    $DB->startTransaction();

    $target_link = $_POST['url'] ?? APP_DOMAIN;
    $web_target_link = $_POST['url2'] ?? APP_DOMAIN;

    $successCount = 0;
    $failCount = 0;

    foreach ($user_ids as $mt_idx) {

      $tpl_sql = "SELECT * FROM member_t WHERE idx = ?";
      $member = $DB->rawQueryOne($tpl_sql, [$mt_idx]);


      $replacements = [
        '{name}'     => $member['mt_name'],
        '{id}'       => $member['mt_id'],
        '{date}'     => date('Y-m-d'),
        '{time}'     => date('H:i'),
        '{datetime}' => date('Y-m-d H:i'),
        '{app}'      => ADMIN_NAME,
        '{url}'      => APP_DOMAIN,
      ];
      $title   = strtr($_POST['fcm_title'], $replacements);
      $body    = strtr($_POST['fcm_body'], $replacements);
      $message = strtr($_POST['fcm_message'], $replacements);


      $res_status = 'skip';

      //if(!empty($member['mt_app_token'])) {
      //  // 3. 푸시 전송
      //  $result = sendPushNotification($member['mt_app_token'], $title, $body, $target_link, []);
      //
      //  $res_status = $result['success'] ? 'success' : 'fail';
      //  $res_msg = ($res_status === 'fail' && isset($result['response']['error']['message']))
      //    ? $result['response']['error']['message']
      //    : ($res_status === 'fail' ? 'Unknown error' : '');
      //
      //  //4. 푸시 이력 저장
      //  $DB->insert('fcm_t', [
      //    'platform' => 'm',
      //    'mt_idx' => $member['idx'],
      //    'fcm_token' => $member['mt_app_token'],
      //    'title' => $title,
      //    'body' => $body,
      //    'url' => $target_link,
      //    'res_status' => $res_status,
      //    'res_msg' => $res_msg,
      //    'created_at' => date('Y-m-d H:i:s'),
      //  ]);
      //}


      $sql = "SELECT * FROM member_fcm_token_t WHERE mt_idx = ? ORDER BY idx ASC";
      $list = $DB->rawQuery($sql, [$mt_idx]);
      if (count($list) > 0) {
        foreach ($list as $item) {
          $icon = CDN_IMG_URL."/favicon/ms-icon-144x144.png";

          $desktop_target_link = $web_target_link;
          $mobile_target_link = $target_link;

          $target_link = $mobile_target_link;
          if($item['platform']=='desktop'){
            $target_link = $desktop_target_link;
          }

          $payload = [
            'l' => $target_link,
            't' => $item['fcm_token']
          ];
          $encrypted = encryptData(json_encode($payload));

          $secure_target_url = APP_DOMAIN."/webpush/redirect.php?q=" . rawurlencode($encrypted);

          $result = sendWebPushNotification($item['fcm_token'], $title, $body, $icon, $secure_target_url, []);

          if ($result['success']) {
            $res_status = 'success';
          } else {
            $res_status = $result['response']['error']['status'] ?? 'fail';
          }
          $res_msg = ($res_status !== 'success' && isset($result['response']['error']['message']))
            ? $result['response']['error']['message']
            : ($res_status === 'fail' ? 'Unknown error' : '');

          $errorsToDelete = [
            'NOT_FOUND',
          ];

          if(in_array($res_status, $errorsToDelete, true) ){
            $DB->where('fcm_token', $item['fcm_token']);
            $DB->delete('member_fcm_token_t');
          }

          $DB->insert('fcm_t', [
            'platform' => $item['platform'],
            'mt_idx' => $item['mt_idx'],
            'fcm_token' => $item['fcm_token'],
            'title' => $title,
            'body' => $body,
            'url' => $secure_target_url,
            'res' => json_encode($result),
            'res_status' => $res_status,
            'res_msg' => $res_msg,
            'created_at' => date('Y-m-d H:i:s'),
          ]);



        }
      }


      if($message) {
        $DB->insert('fcm_alarm_t', [
          'mt_idx' => $member['idx'],
          'message' => $message,
          'url' => $target_link,
          'is_read' => 'N',
          'created_at' => date('Y-m-d H:i:s'),
        ]);
      }


      if ($res_status === 'success') {
        $successCount++;
      } elseif ($res_status !== 'success') {
        $failCount++;
      }

    }

    $DB->commit();

    echo json_encode([
      'success' => true,
      'message' =>  "FCM 발송 결과: 성공 {$successCount}건 / 실패 {$failCount}건"
    ]);






  } catch (Exception $e) {
    $DB->rollback();
    echo json_encode([
      'success' => false,
      'message' => $e->getMessage()
    ]);
  }

}