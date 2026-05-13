<?php
// table_sse.php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

// PHP 실행 시간 제한 해제 (공유호스팅에서는 제한 있을 수 있음)
set_time_limit(0);

// 마지막으로 클라이언트가 받은 이벤트 ID (재연결 시 활용)
$lastEventId = isset($_SERVER['HTTP_LAST_EVENT_ID']) ? (int)$_SERVER['HTTP_LAST_EVENT_ID'] : 0;

// 변경 감지 기준 파일 (간단 테스트용)
// 실제로는 DB의 MAX(updated_at) 등을 쓰는 게 좋음
$cacheFile = __DIR__ . '/last_table_update.txt';

// 파일이 없으면 현재 시간으로 생성
if (!file_exists($cacheFile)) {
    file_put_contents($cacheFile, time());
}

while (true) {
    clearstatcache(); // 파일 상태 캐시 갱신
    $currentTimestamp = (int)file_get_contents($cacheFile);

    // 새로운 변경이 있으면 클라이언트에게 알림
    if ($currentTimestamp > $lastEventId) {
        echo "event: table-updated\n";
        echo "id: {$currentTimestamp}\n";
        echo "data: " . json_encode([
                'timestamp' => date('Y-m-d H:i:s', $currentTimestamp),
                'message'   => '테이블 목록 또는 주문 상태가 업데이트되었습니다.'
            ]) . "\n\n";
        flush(); // 즉시 전송

        $lastEventId = $currentTimestamp;
    }

    // 4초마다 체크 (너무 짧으면 서버 부하 ↑, 너무 길면 지연 ↑)
    sleep(4);

    // 연결이 끊어졌으면 종료
    if (connection_aborted()) {
        break;
    }
}
