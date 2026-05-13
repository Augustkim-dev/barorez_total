<?php
// 컴퓨터의 아이피와 쿠키에 저장된 아이피가 다르다면 테이블에 반영함

$currentDir = getcwd(); // 현재 작업 디렉토리 가져오기

if ($_GET['chk_app'] != "Y") {
    if (ROOT_DIR !== $currentDir) {

        if (get_cookie('ck_visit_ip') != $_SERVER['REMOTE_ADDR']) {
            set_cookie('ck_visit_ip', $_SERVER['REMOTE_ADDR'], 86400); // 하루동안 저장

            $query = "SELECT MAX(vi_id) AS max_vi_id FROM visit_t";
            $tmp_row = $DB->rawQueryOne($query);
            $vi_id = ($tmp_row['max_vi_id'] ?? 0) + 1;

            // $_SERVER 값 정리 및 보안 처리
            $remote_addr = $_SERVER['REMOTE_ADDR'];
            $referer = '';
            if (isset($_SERVER['HTTP_REFERER'])) {
                $referer = clean_xss_tags(strip_tags($_SERVER['HTTP_REFERER']));
            }
            $user_agent = '';
            if (isset($_SERVER['HTTP_USER_AGENT'])) {
                $user_agent = clean_xss_tags(strip_tags($_SERVER['HTTP_USER_AGENT']));
            }

            $vi_browser = '';
            $vi_os = '';
            $vi_device = '';

            // Browscap 사용 (캐시 파일 존재 시)
            if (file_exists(DATA_PATH . '/cache/browscap_cache.php')) {
                include $_SERVER['DOCUMENT_ROOT'] . "/vendor/browscap/Browscap.php";
                $browscap = new phpbrowscap\Browscap(DATA_PATH . '/cache');
                $browscap->doAutoUpdate = false;
                $browscap->cacheFilename = 'browscap_cache.php';

                $info = $browscap->getBrowser($_SERVER['HTTP_USER_AGENT'] ?? '');

                $vi_browser = $info->Comment ?? '';
                $vi_os = $info->Platform ?? '';
                $vi_device = $info->Device_Type ?? '';
            }

            // 여기서 삼항 연산자 수정: 의도가 회원이면 mt_idx 사용, 아니면 0
            // 기존: $_SESSION['user']['mt_idx'] ? $_SESSION['_mt_idx'] : 0; → 문법 오류!
            // 수정: 회원 정보가 있으면 mt_idx 사용, 없으면 0
            $vi_mt_idx = 0;
            if (!empty($_SESSION['user']['mt_idx'])) {
                $vi_mt_idx = $_SESSION['user']['mt_idx'];
            } elseif (!empty($_SESSION['_mt_idx'])) {
                $vi_mt_idx = $_SESSION['_mt_idx'];
            }

            $arr_query = [
                'vi_id'       => $vi_id,
                'vi_mt_idx'   => $vi_mt_idx,
                'vi_ip'       => $remote_addr,
                'vi_date'     => TIME_YMD,
                'vi_time'     => date('H:i:s'),
                'vi_referer'  => $referer,
                'vi_agent'    => $user_agent,
                'vi_browser'  => $vi_browser,
                'vi_os'       => $vi_os,
                'vi_device'   => $vi_device
            ];

            $_last_idx = $DB->insert('visit_t', $arr_query);

            if ($_last_idx) {
                $query = "SELECT * FROM visit_sum_t WHERE vs_date = '" . TIME_YMD . "'";
                $vs = $DB->rawQueryOne($query);

                if (!$vs || !$vs['vs_count']) {
                    $DB->insert('visit_sum_t', [
                        'vs_date'  => TIME_YMD,
                        'vs_count' => 1
                    ]);
                } else {
                    $DB->where('vs_date', TIME_YMD);
                    $DB->update('visit_sum_t', [
                        'vs_count' => $vs['vs_count'] + 1
                    ]);
                }
            }
        }
    }
}

// 방문(visit) 관리: QR 재스캔 시 ACTIVE 방문이면 재사용, 없으면 신규 생성

if (!defined('VISIT_TTL_MINUTES')) {
    define('VISIT_TTL_MINUTES', 90); // 90분 후 자동 종료 기준
}

function visit_cookie_name(int $sh_idx, string $tableNo): string {
    $tableNo = preg_replace('/[^0-9a-zA-Z_\-]/', '', (string)$tableNo);
    return "visit_key_{$sh_idx}_{$tableNo}";
}

function generate_visit_key(): string {
    return bin2hex(random_bytes(32));
}

function close_visit($DB, int $tv_idx): void {
    if ($tv_idx <= 0) return;
    $DB->where('idx', $tv_idx);
    $DB->update('table_visit_t', [
        'tv_status' => 'CLOSED',
        'tv_ended'  => $DB->now(),
    ]);
}

function touch_visit($DB, int $tv_idx): void {
    if ($tv_idx <= 0) return;
    $DB->where('idx', $tv_idx);
    $DB->update('table_visit_t', [
        'tv_last_active' => $DB->now(),
    ]);
}

function set_visit_cookie(int $sh_idx, string $tableNo, string $visitKey): void {
    $name = visit_cookie_name($sh_idx, $tableNo);
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');

    setcookie($name, $visitKey, [
        'expires'  => time() + 3600 * 6,
        'path'     => '/',
        'secure'   => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

function get_visit_cookie(int $sh_idx, string $tableNo): string {
    $name = visit_cookie_name($sh_idx, $tableNo);
    return $_COOKIE[$name] ?? '';
}

function find_active_visit_by_key($DB, int $sh_idx, string $tableNo, string $visitKey) {
    if ($visitKey === '') return null;

    $DB->where('visit_key', $visitKey);
    $DB->where('sh_idx', $sh_idx);
    $DB->where('tv_table', $tableNo);
    $DB->where('tv_status', 'ACTIVE');
    return $DB->getOne('table_visit_t');
}

function find_active_visit_by_member($DB, int $sh_idx, string $tableNo, int $mt_idx) {
    if ($mt_idx <= 0) return null;
    $DB->where('mt_idx', $mt_idx);
    $DB->where('sh_idx', $sh_idx);
    $DB->where('tv_table', $tableNo);
    $DB->where('tv_status', 'ACTIVE');
    $DB->orderBy('idx', 'DESC');
    return $DB->getOne('table_visit_t');
}

function is_visit_expired(array $visitRow): bool {
    $last = $visitRow['tv_last_active'] ?? null;
    if (!$last) return true;

    $lastTs = strtotime($last);
    if ($lastTs <= 0) return true;

    $ttlSeconds = VISIT_TTL_MINUTES * 60;
    return (time() - $lastTs) > $ttlSeconds;
}

function create_visit($DB, int $sh_idx, string $tableNo, int $mt_idx = 0): array {
    $visitKey = generate_visit_key();
    $ip = $_SERVER['REMOTE_ADDR'] ?? null;
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? null;

    $tv_idx = $DB->insert('table_visit_t', [
        'visit_key'      => $visitKey,
        'sh_idx'         => $sh_idx,
        'tv_table'       => $tableNo,
        'mt_idx'         => ($mt_idx > 0 ? $mt_idx : null),
        'tv_status'      => 'ACTIVE',
        'tv_started'     => $DB->now(),
        'tv_last_active' => $DB->now(),
        'tv_ip'          => $ip,
        'tv_ua'          => mb_substr((string)$ua, 0, 255),
    ]);

    if (!$tv_idx) {
        throw new Exception('방문 세션 생성에 실패했습니다.');
    }

    return [
        'tv_idx'     => (int)$tv_idx,
        'visit_key'  => $visitKey,
    ];
}

function ensure_visit($DB, int $sh_idx, string $tableNo, int $mt_idx = 0): array {
    $tableNo = trim((string)$tableNo);
    if ($sh_idx <= 0 || $tableNo === '') {
        throw new Exception('매장/테이블 정보가 올바르지 않습니다.');
    }

    // 1. 세션 우선
    $sessTv = (int)($_SESSION['tv_idx'] ?? 0);
    $sessKey = (string)($_SESSION['visit_key'] ?? '');

    if ($sessTv > 0 && $sessKey !== '') {
        $v = find_active_visit_by_key($DB, $sh_idx, $tableNo, $sessKey);
        if ($v && !is_visit_expired($v)) {
            touch_visit($DB, (int)$v['idx']);
            set_visit_cookie($sh_idx, $tableNo, $sessKey);
            return ['tv_idx' => (int)$v['idx'], 'visit_key' => $sessKey];
        }
        if ($v && is_visit_expired($v)) {
            close_visit($DB, (int)$v['idx']);
        }
    }

    // 2. 쿠키 기반
    $cookieKey = get_visit_cookie($sh_idx, $tableNo);
    if ($cookieKey !== '') {
        $v = find_active_visit_by_key($DB, $sh_idx, $tableNo, $cookieKey);
        if ($v && !is_visit_expired($v)) {
            touch_visit($DB, (int)$v['idx']);
            $_SESSION['tv_idx'] = (int)$v['idx'];
            $_SESSION['visit_key'] = $cookieKey;
            return ['tv_idx' => (int)$v['idx'], 'visit_key' => $cookieKey];
        }
        if ($v && is_visit_expired($v)) {
            close_visit($DB, (int)$v['idx']);
        }
    }

    // 3. 회원 복원
    if ($mt_idx > 0) {
        $v = find_active_visit_by_member($DB, $sh_idx, $tableNo, $mt_idx);
        if ($v && !is_visit_expired($v)) {
            touch_visit($DB, (int)$v['idx']);
            $_SESSION['tv_idx'] = (int)$v['idx'];
            $_SESSION['visit_key'] = (string)$v['visit_key'];
            set_visit_cookie($sh_idx, $tableNo, (string)$v['visit_key']);
            return ['tv_idx' => (int)$v['idx'], 'visit_key' => (string)$v['visit_key']];
        }
        if ($v && is_visit_expired($v)) {
            close_visit($DB, (int)$v['idx']);
        }
    }

    // 4. 신규 생성
    $new = create_visit($DB, $sh_idx, $tableNo, $mt_idx);
    $_SESSION['tv_idx'] = (int)$new['tv_idx'];
    $_SESSION['visit_key'] = (string)$new['visit_key'];
    set_visit_cookie($sh_idx, $tableNo, (string)$new['visit_key']);

    return $new;
}
?>
