<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
use Google\Client;
use Firebase\JWT\JWT;

if (!function_exists('createJWT')) {
  function createJWT($serviceAccount)
  {
    try {
      $now = time();

      $payload = [
        "iss" => $serviceAccount['client_email'],
        "scope" => "https://www.googleapis.com/auth/firebase.messaging",
        "aud" => "https://oauth2.googleapis.com/token",
        "exp" => $now + 3600,
        "iat" => $now
      ];

      // private_key에서 불필요한 공백 제거
      $privateKey = str_replace('\n', "\n", $serviceAccount['private_key']);
      return JWT::encode($payload, $privateKey, 'RS256');
    } catch (Exception $e) {
      error_log('JWT 생성 오류: ' . $e->getMessage());
      throw $e;
    }
  }
}

if (!function_exists('getGoogleAccessToken')) {
  function getGoogleAccessToken()
  {
    try {
      // 서비스 계정 파일 경로 (절대 경로 사용 권장)
      $serviceAccountFile = $_SERVER['DOCUMENT_ROOT'] . '/api/savefrom-ce0f7-firebase-adminsdk-fbsvc-5617647285.json';

      if (!file_exists($serviceAccountFile)) {
        throw new Exception('서비스 계정 파일을 찾을 수 없습니다.');
      }

      $serviceAccount = json_decode(file_get_contents($serviceAccountFile), true);

      if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('서비스 계정 JSON 파싱 오류: ' . json_last_error_msg());
      }



      $jwt = createJWT($serviceAccount);

      $url = 'https://oauth2.googleapis.com/token';

      $data = [
        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
        'assertion' => $jwt
      ];

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

      $response = curl_exec($ch);

      if (curl_errno($ch)) {
        throw new Exception('Curl 오류: ' . curl_error($ch));
      }

      curl_close($ch);

      $responseData = json_decode($response, true);

      if (!isset($responseData['access_token'])) {
        throw new Exception('액세스 토큰을 받지 못했습니다: ' . print_r($responseData, true));
      }

      return $responseData['access_token'];
    } catch (Exception $e) {
      error_log('토큰 획득 오류: ' . $e->getMessage());
      throw $e;
    }
  }
}


if (!function_exists('sendPushNotification')) {
  function sendPushNotification($deviceToken, $title, $body, $targetUrl = '', $additionalData = [])
  {
    try {
      $url = 'https://fcm.googleapis.com/v1/projects/porta-6ba10/messages:send';

      // data 필드에 url과 추가 데이터 병합
      $data = array_merge(
        ['title' => $title,'body' => $body,'targetUrl' => $targetUrl],  // URL 추가
        $additionalData   // 기타 추가 데이터
      );

      $message = [
        'message' => [
          'token' => urldecode($deviceToken),
          'notification' => [
            'title' => $title,
            'body' => $body
          ],
          'data' => $data,
          'android' => [
            'priority' => 'high',
            'notification' => [
              'sound' => 'default',
              'channel_id' => 'default'
            ]
          ],
          'apns' => [
            'headers' => [
              'apns-priority' => '10',
              'apns-push-type' => 'alert',
              'apns-topic' => 'com.porta'
            ],
            'payload' => [
              'aps' => [
                'alert' => [
                  'title' => $title,
                  'body' => $body
                ],
                'sound' => 'default',
                'badge' => 1,
                'mutable-content' => 1,
                'content-available' => 1
              ],
              'data' => $data  // 커스텀 데이터도 포함
            ]
          ]
        ]
      ];



      $accessToken = getGoogleAccessToken();
      $headers = [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json'
      ];

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));

      $response = curl_exec($ch);
      $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

      if (curl_errno($ch)) {
        throw new Exception('Curl 오류: ' . curl_error($ch));
      }

      curl_close($ch);

      return [
        'success' => ($httpcode == 200),
        'response' => json_decode($response, true)
      ];
    } catch (Exception $e) {
      error_log('푸시 알림 전송 오류: ' . $e->getMessage());
      return [
        'success' => false,
        'error' => $e->getMessage()
      ];
    }
  }

}

if (!function_exists('sendWebPushNotification')) {
  function sendWebPushNotification($deviceToken, $title, $body, $icon, $targetUrl = '', $additionalData = [])
  {

    $client = new Client();
    $client->setAuthConfig($_SERVER['DOCUMENT_ROOT'] . '/porta-6ba10-firebase-adminsdk-fbsvc-92b15bc973.json');
    $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
    $accessToken = $client->fetchAccessTokenWithAssertion()['access_token'];

    try {
      $url = "https://fcm.googleapis.com/v1/projects/porta-6ba10/messages:send";


      $message = [
        'message' => [
          'token' => $deviceToken,
          'webpush' => [
            'notification' => [
              'title' => $title,
              'body' => $body,
              'icon' => $icon,
              //'image' => $image,   // 본문에 표시할 큰 이미지
            ],
            'headers' => [
              'Urgency' => "high",
            ],
            'data' => [
              'click_action' => $targetUrl
            ]
          ]
        ]
      ];



      $headers = [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json; UTF-8',
      ];

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));

      $response = curl_exec($ch);
      $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

      if (curl_errno($ch)) {
        throw new Exception('Curl 오류: ' . curl_error($ch));
      }

      curl_close($ch);

      return [
        'success' => ($httpcode == 200),
        'response' => json_decode($response, true)
      ];
    } catch (Exception $e) {
      error_log('푸시 알림 전송 오류: ' . $e->getMessage());
      return [
        'success' => false,
        'error' => $e->getMessage()
      ];
    }
  }

}


if (!function_exists('notifyByTemplate')) {
  function notifyByTemplate($param) {
    global $DB;

    /**
     * $param 구조:
     * - mt_idx        : 회원 IDX
     * - app_token     : 회원의 앱 토큰
     * - web_token     : 회원의 웹 토큰
     * - target_link        :
     * - web_target_link    :
     */

    // 1. 템플릿 정보 가져오기
    $tpl_sql = "SELECT * FROM fcm_template_t WHERE idx = ?";
    $tpl = $DB->rawQueryOne($tpl_sql, [$param['template']]);
    if (!$tpl) return false;

    $lang = 'en';
    if (!empty($param['mt_idx'])) {
      $sql = "
        SELECT L.lang 
        FROM member_t M 
        LEFT JOIN locale_t L ON M.mt_language = L.idx 
        WHERE M.idx = ?
      ";
      $lang_row = $DB->rawQueryOne($sql, [$param['mt_idx']]);
      if (!empty($lang_row['lang'])) {
        $lang = $lang_row['lang']; // 예: 'en'
      }
    }

    $title_field   = $lang === 'ko' ? 'title'   : "title_{$lang}";
    $body_field    = $lang === 'ko' ? 'body'    : "body_{$lang}";
    $message_field = $lang === 'ko' ? 'message' : "message_{$lang}";

    // 2. 치환 키워드 정의
    $replacements = [
      '{APP_NAME}' => ADMIN_NAME,
      '{TITLE}'    => $param['title'],
      '{NAME}'    => $param['name'],
    ];

    $title   = strtr($tpl[$title_field] ?? $tpl['title'], $replacements);
    $body    = strtr($tpl[$body_field] ?? $tpl['body'], $replacements);
    $message = strtr($tpl[$message_field] ?? $tpl['message'], $replacements);
    $url     = $param['target_link'];


    if(!empty($param['app_token'])) {
      // 3. 푸시 전송
      $result = sendPushNotification($param['app_token'], $title, $body, $url, []);

      $res_status = $result['success'] ? 'success' : 'fail';
      $res_msg = ($res_status === 'fail' && isset($result['response']['error']['message']))
        ? $result['response']['error']['message']
        : ($res_status === 'fail' ? 'Unknown error' : '');

      //4. 푸시 이력 저장
      $DB->insert('fcm_t', [
        'template' => $tpl['idx'],
        'mt_idx' => $param['mt_idx'],
        'fcm_token' => $param['app_token'],
        'title' => $title,
        'body' => $body,
        'url' => $url,
        'res' => json_encode($result),
        'res_status' => $res_status,
        'res_msg' => $res_msg,
        'created_at' => date('Y-m-d H:i:s'),
      ]);
    }

    if(!empty($param['web_token'])) {
      $icon = CDN_IMG_URL."/favicon/ms-icon-144x144.png";

      $result = sendWebPushNotification($param['web_token'], $title, $body, $icon, $param['web_target_link'], []);

      $res_status = $result['success'] ? 'success' : 'fail';
      $res_msg = ($res_status === 'fail' && isset($result['response']['error']['message']))
        ? $result['response']['error']['message']
        : ($res_status === 'fail' ? 'Unknown error' : '');

      //4. 푸시 이력 저장
      $DB->insert('fcm_t', [
        'platform' => $param['platform'],
        'template' => $tpl['idx'],
        'mt_idx' => $param['mt_idx'],
        'fcm_token' => $param['web_token'],
        'title' => $title,
        'body' => $body,
        'url' => $param['web_target_link'],
        'res' => json_encode($result),
        'res_status' => $res_status,
        'res_msg' => $res_msg,
        'created_at' => date('Y-m-d H:i:s'),
      ]);
    }

    // 5. 앱 알림 저장
    $DB->insert('fcm_alarm_t', [
      'mt_idx'     => $param['mt_idx'],
      'template'   => $tpl['idx'],
      'message'    => $message,
      'url'        => $url,
      'is_read'    => 'N',
      'created_at' => date('Y-m-d H:i:s'),
    ]);

    return true;
  }
}