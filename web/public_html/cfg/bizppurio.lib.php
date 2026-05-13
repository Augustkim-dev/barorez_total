<?php

/**
 * 비즈뿌리오 설정
 */
function bizppurio_config()
{
    return [
        'base_url' => 'https://api.bizppurio.com',
        'account'  => 'hc_rest',
        'password' => 'hmm1800!@#$',
        'sender'   => '1660-1825', // 예: 0212345678 또는 01012345678
        'timeout'  => 20,
    ];
}

/**
 * 전화번호 숫자만 추출
 */
function bizppurio_only_number($phone)
{
    return preg_replace('/[^0-9]/', '', (string)$phone);
}

/**
 * 공통 cURL 요청
 */
function bizppurio_request($method, $url, $headers = [], $body = null)
{
    $cfg = bizppurio_config();

    $ch = curl_init();

    $defaultHeaders = [
        'Content-Type: application/json; charset=utf-8',
        'Accept: application/json'
    ];

    $headers = array_merge($defaultHeaders, $headers);

    $options = [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => (int)$cfg['timeout'],
        CURLOPT_CUSTOMREQUEST  => strtoupper($method),
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
    ];

    if ($body !== null) {
        $options[CURLOPT_POSTFIELDS] = is_string($body)
            ? $body
            : json_encode($body, JSON_UNESCAPED_UNICODE);
    }

    curl_setopt_array($ch, $options);

    $responseBody = curl_exec($ch);
    $curlErrNo    = curl_errno($ch);
    $curlError    = curl_error($ch);
    $httpCode     = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    if ($curlErrNo) {
        return [
            'success'   => false,
            'http_code' => $httpCode,
            'message'   => 'cURL 오류: ' . $curlError,
            'data'      => null,
            'raw'       => $responseBody,
        ];
    }

    $decoded = json_decode($responseBody, true);

    return [
        'success'   => ($httpCode >= 200 && $httpCode < 300),
        'http_code' => $httpCode,
        'message'   => '',
        'data'      => $decoded,
        'raw'       => $responseBody,
    ];
}

/**
 * 토큰 발급
 */
function bizppurio_issue_token()
{
    $cfg = bizppurio_config();

    $basicToken = base64_encode($cfg['account'] . ':' . $cfg['password']);

    $headers = [
        'Authorization: Basic ' . $basicToken,
    ];

    $url = $cfg['base_url'] . '/v1/token';

    $result = bizppurio_request('POST', $url, $headers, new stdClass());

    if (!$result['success']) {
        return [
            'success' => false,
            'message' => '토큰 발급 실패',
            'result'  => $result,
        ];
    }

    $data = $result['data'];

    $accessToken = $data['accesstoken'] ?? $data['accessToken'] ?? '';

    if ($accessToken === '') {
        return [
            'success' => false,
            'message' => '토큰값이 비어 있습니다.',
            'result'  => $result,
        ];
    }

    return [
        'success'      => true,
        'message'      => '토큰 발급 성공',
        'access_token' => $accessToken,
        'result'       => $result,
    ];
}

/**
 * 문자 발송
 * - 휴대폰번호와 메시지만 받아서 발송
 */
function bizppurio_send_sms($to, $message)
{
    $cfg = bizppurio_config();

    $to      = bizppurio_only_number($to);
    $from    = bizppurio_only_number($cfg['sender']);
    $message = trim((string)$message);

    if ($to === '') {
        return [
            'success' => false,
            'message' => '휴대폰 번호가 비어 있습니다.',
        ];
    }

    if ($from === '') {
        return [
            'success' => false,
            'message' => '발신번호가 비어 있습니다.',
        ];
    }

    if ($message === '') {
        return [
            'success' => false,
            'message' => '메시지 내용이 비어 있습니다.',
        ];
    }

    $tokenRes = bizppurio_issue_token();
    if (!$tokenRes['success']) {
        return $tokenRes;
    }

    $accessToken = $tokenRes['access_token'];
    $refkey      = 'sms_' . date('YmdHis') . '_' . mt_rand(1000, 9999);

    $payload = [
        'account' => $cfg['account'],
        'type'    => 'sms',
        'from'    => $from,
        'to'      => $to,
        'content' => [
            'sms' => [
                'message' => $message,
            ],
        ],
        'refkey'  => $refkey,
    ];

    $headers = [
        'Authorization: Bearer ' . $accessToken,
    ];

    $url = $cfg['base_url'] . '/v3/message';

    $result = bizppurio_request('POST', $url, $headers, $payload);

    return [
        'success' => $result['success'],
        'message' => $result['success'] ? '인증번호 발송' : '문자 발송 실패',
        'payload' => $payload,
        'result'  => $result,
    ];
}
