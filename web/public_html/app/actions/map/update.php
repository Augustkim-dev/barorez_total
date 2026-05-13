<?php
include $_SERVER['DOCUMENT_ROOT'] . "/cfg/lib.inc.php";

header('Content-Type: application/json; charset=utf-8');

$act = $_POST['act'] ?? '';

function normalize_bounds($swLat, $swLng, $neLat, $neLng) {
    return [
        'swLat' => min((float)$swLat, (float)$neLat),
        'neLat' => max((float)$swLat, (float)$neLat),
        'swLng' => min((float)$swLng, (float)$neLng),
        'neLng' => max((float)$swLng, (float)$neLng),
    ];
}

function expand_bounds($swLat, $swLng, $neLat, $neLng, $bufferRate = 1.35) {
    $b = normalize_bounds($swLat, $swLng, $neLat, $neLng);

    $latGap = max($b['neLat'] - $b['swLat'], 0.002);
    $lngGap = max($b['neLng'] - $b['swLng'], 0.002);

    $latBuffer = $latGap * (($bufferRate - 1) / 2);
    $lngBuffer = $lngGap * (($bufferRate - 1) / 2);

    return [
        'swLat' => max(-90, $b['swLat'] - $latBuffer),
        'neLat' => min(90, $b['neLat'] + $latBuffer),
        'swLng' => max(-180, $b['swLng'] - $lngBuffer),
        'neLng' => min(180, $b['neLng'] + $lngBuffer),
    ];
}

function get_cluster_policy($zoomLevel) {
    if ($zoomLevel <= 5) {
        return [
            'mode' => 'shop',
            'cell' => null,
            'clusterMin' => null,
        ];
    }

    if ($zoomLevel === 6) {
        return [
            'mode' => 'mixed',
            'cell' => ['lat' => 0.028, 'lng' => 0.028],
            'clusterMin' => 100,
        ];
    }

    if ($zoomLevel === 7) {
        return [
            'mode' => 'cluster',
            'cell' => ['lat' => 0.022, 'lng' => 0.022],
            'clusterMin' => 1,
        ];
    }

    if ($zoomLevel === 8) {
        return [
            'mode' => 'cluster',
            'cell' => ['lat' => 0.026, 'lng' => 0.026],
            'clusterMin' => 1,
        ];
    }

    if ($zoomLevel === 9) {
        return [
            'mode' => 'cluster',
            'cell' => ['lat' => 0.036, 'lng' => 0.036],
            'clusterMin' => 1,
        ];
    }

    if ($zoomLevel === 10) {
        return [
            'mode' => 'cluster',
            'cell' => ['lat' => 0.18, 'lng' => 0.18],
            'clusterMin' => null,
        ];
    }

    if ($zoomLevel === 11) {
        return [
            'mode' => 'cluster',
            'cell' => ['lat' => 0.32, 'lng' => 0.32],
            'clusterMin' => null,
        ];
    }

    if ($zoomLevel === 12) {
        return [
            'mode' => 'cluster',
            'cell' => ['lat' => 0.56, 'lng' => 0.56],
            'clusterMin' => null,
        ];
    }
    return [
        'mode' => 'super_cluster',
        'cell' => null,
        'clusterMin' => null,
    ];
}

function build_shop_payload($rows) {
    $shops = [];

    foreach ($rows as $r) {
        $idx = (int)$r['idx'];
        $title = trim($r['sh_title'] ?? '');
        $branch = trim($r['sh_branch_nm'] ?? '');
        $fullName = $title . ($branch ? " [{$branch}]" : "");

        $images = [];
        foreach (['sh_img1', 'sh_img2', 'sh_img3'] as $key) {
            $filename = trim($r[$key] ?? '');
            if ($filename !== '') {
                $images[] = "/data/shop/{$idx}/rs_{$filename}";
            }
        }

        $shops[] = [
            'idx'         => $idx,
            'name'        => $fullName,
            'lat'         => (float)$r['lat'],
            'lng'         => (float)$r['lng'],
            'distance_m'  => isset($r['distance_m']) ? (int)round((float)$r['distance_m']) : 0,
            'images'      => $images,
            'takeout'     => ($r['sh_takeout_yn'] ?? 'N') === 'Y',
            'tel'         => (format_phone($r['sh_tel']) ?? ''),
            'mt_appr'     => trim($r['mt_appr'] ?? ''),
            'reservation' => ($r['sh_reserve_yn'] ?? 'N') === 'Y',
            'qr_order'    => ($r['sh_qr_yn'] ?? 'N') === 'Y',
            'qr_pay_type' => $r['sh_qr_pay_type'] ?? 'POSTPAY',
            'addr1'       => trim($r['sh_addr1'] ?? ''),
            'addr2'       => trim($r['sh_addr2'] ?? ''),
            'open_time'   => '',
        ];
    }

    return $shops;
}

if ($act === 'map') {
    try {
        $keyword = trim((string)($_POST['keyword'] ?? ''));
        $region = trim((string)($_POST['region'] ?? ($_POST['kw2'] ?? '')));
        $district = trim((string)($_POST['district'] ?? ''));
        $searchMode = trim((string)($_POST['searchMode'] ?? ''));

        $swLat = trim((string)($_POST['swLat'] ?? ''));
        $swLng = trim((string)($_POST['swLng'] ?? ''));
        $neLat = trim((string)($_POST['neLat'] ?? ''));
        $neLng = trim((string)($_POST['neLng'] ?? ''));

        $lat = trim((string)($_POST['lat'] ?? ''));
        $lng = trim((string)($_POST['lng'] ?? ''));
        $radius = (int)($_POST['radius'] ?? 15000);

        $refLat = trim((string)($_POST['refLat'] ?? ''));
        $refLng = trim((string)($_POST['refLng'] ?? ''));
        $zoomLevel = max(1, min(14, (int)($_POST['zoomLevel'] ?? 7)));

        $hasRef = ($refLat !== '' && $refLng !== '');
        $refLatF = $hasRef ? (float)$refLat : 0.0;
        $refLngF = $hasRef ? (float)$refLng : 0.0;

        $responseBounds = null;

        $regionAliasMap = [
            '서울특별시' => '서울',
            '부산광역시' => '부산',
            '대구광역시' => '대구',
            '인천광역시' => '인천',
            '광주광역시' => '광주',
            '대전광역시' => '대전',
            '울산광역시' => '울산',
            '세종특별자치시' => '세종',
            '경기도' => '경기',
            '강원특별자치도' => '강원',
            '충청북도' => '충북',
            '충청남도' => '충남',
            '전북특별자치도' => '전북',
            '전라북도' => '전북',
            '전라남도' => '전남',
            '경상북도' => '경북',
            '경상남도' => '경남',
            '제주특별자치도' => '제주',
        ];

        $regionSearch = $regionAliasMap[$region] ?? $region;

        $distanceExpr = $hasRef ? "
            6371000 * ACOS(LEAST(GREATEST(
                COS(RADIANS(?)) * COS(RADIANS(s.sh_lat_num)) *
                COS(RADIANS(s.sh_lng_num) - RADIANS(?)) +
                SIN(RADIANS(?)) * SIN(RADIANS(s.sh_lat_num))
            , -1.0), 1.0))
        " : "0";
        $refParams = $hasRef ? [$refLatF, $refLngF, $refLatF] : [];

        $whereKeyword = '';
        $kwParams = [];
        if ($keyword !== '') {
            $kw = "%{$keyword}%";

            $whereKeyword = "
                AND (
                    s.sh_title LIKE ?
                    OR s.sh_branch_nm LIKE ?
                    OR EXISTS (
                        SELECT 1
                        FROM shop_category_t sc
                        INNER JOIN shop_menu_t sm ON sm.sc_idx = sc.idx
                        WHERE sc.sh_idx = s.idx
                          AND sm.sm_show = 'Y'
                          AND TRIM(COALESCE(sm.sm_title, '')) <> ''
                          AND sm.sm_title LIKE ?
                    )
                )
            ";

            $kwParams = [$kw, $kw, $kw];
        }

        $whereArea = '';
        $areaParams = [];

        if ($regionSearch !== '' && $district !== '') {
            $whereArea = " AND s.sh_addr1 LIKE ?";
            $areaParams = ["{$regionSearch} {$district}%"];
        } elseif ($regionSearch !== '') {
            $whereArea = " AND s.sh_addr1 LIKE ?";
            $areaParams = ["{$regionSearch}%"];
        }

        $baseFrom = "
            FROM shop_t s
            INNER JOIN member_t m ON m.idx = s.mb_idx
            WHERE s.del_date IS NULL
              AND s.sh_show = 'Y'
              AND m.mt_appr IN ('Y', 'T')
              AND s.sh_lat_num IS NOT NULL
              AND s.sh_lng_num IS NOT NULL
              {$whereArea}
              {$whereKeyword}
        ";

        if ($searchMode === 'region') {
            $sql = "
                SELECT
                    s.idx, s.sh_title, s.sh_branch_nm, s.sh_addr1, s.sh_addr2, s.sh_tel, m.mt_appr,
                    s.sh_lat_num AS lat,
                    s.sh_lng_num AS lng,
                    s.sh_img1, s.sh_img2, s.sh_img3,
                    s.sh_show, s.sh_takeout_yn, s.sh_reserve_yn, s.sh_qr_yn, s.sh_qr_pay_type,
                    {$distanceExpr} AS distance_m
                {$baseFrom}
                ORDER BY distance_m ASC, s.idx DESC
                LIMIT 3000
            ";

            $params = array_merge($refParams, $areaParams, $kwParams);
            $rows = $DB->rawQuery($sql, !empty($params) ? $params : null);

            if ($DB->getLastErrno()) {
                throw new Exception($DB->getLastError());
            }

            $shops = build_shop_payload($rows);

            echo json_encode([
                'success' => true,
                'message' => 'OK',
                'data'    => [
                    'mode' => 'shop',
                    'shops' => $shops,
                    'clusters' => [],
                    'bounds' => null,
                    'total' => count($shops),
                ]
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        if ($swLat !== '' && $swLng !== '' && $neLat !== '' && $neLng !== '') {
            $buffered = expand_bounds($swLat, $swLng, $neLat, $neLng, 1.35);
            $responseBounds = $buffered;
            $policy = get_cluster_policy($zoomLevel);

            $boundsWhere = "
                AND s.sh_lat_num BETWEEN ? AND ?
                AND s.sh_lng_num BETWEEN ? AND ?
            ";
            $boundsParams = [
                $buffered['swLat'],
                $buffered['neLat'],
                $buffered['swLng'],
                $buffered['neLng']
            ];

            if ($policy['mode'] === 'shop') {
                $shopSql = "
                    SELECT
                        s.idx, s.sh_title, s.sh_branch_nm, s.sh_addr1, s.sh_addr2, s.sh_tel, m.mt_appr,
                        s.sh_lat_num AS lat,
                        s.sh_lng_num AS lng,
                        s.sh_img1, s.sh_img2, s.sh_img3,
                        s.sh_show, s.sh_takeout_yn, s.sh_reserve_yn, s.sh_qr_yn, s.sh_qr_pay_type,
                        {$distanceExpr} AS distance_m
                    {$baseFrom}
                    {$boundsWhere}
                    ORDER BY distance_m ASC, s.idx DESC
                ";

                $shopParams = array_merge($refParams, $areaParams, $kwParams, $boundsParams);
                $rows = $DB->rawQuery($shopSql, !empty($shopParams) ? $shopParams : null);

                if ($DB->getLastErrno()) {
                    throw new Exception($DB->getLastError());
                }

                $shops = build_shop_payload($rows);

                echo json_encode([
                    'success' => true,
                    'message' => 'OK',
                    'data'    => [
                        'mode' => 'shop',
                        'shops' => $shops,
                        'clusters' => [],
                        'bounds' => $responseBounds,
                        'total' => count($shops),
                    ]
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            if ($policy['mode'] === 'super_cluster') {
                $visible = normalize_bounds($swLat, $swLng, $neLat, $neLng);

                $countSql = "
                    SELECT COUNT(*) AS cnt
                    {$baseFrom}
                    {$boundsWhere}
                ";

                $countParams = array_merge($areaParams, $kwParams, $boundsParams);
                $countRows = $DB->rawQuery($countSql, !empty($countParams) ? $countParams : null);

                if ($DB->getLastErrno()) {
                    throw new Exception($DB->getLastError());
                }

                $totalCount = (int)($countRows[0]['cnt'] ?? 0);
                $clusters = [];

                if ($totalCount > 0) {
                    $clusters[] = [
                        'idx' => 'super_' . $zoomLevel,
                        'lat' => round(($visible['swLat'] + $visible['neLat']) / 2, 7),
                        'lng' => round(($visible['swLng'] + $visible['neLng']) / 2, 7),
                        'cluster_count' => $totalCount,
                    ];
                }

                echo json_encode([
                    'success' => true,
                    'message' => 'OK',
                    'data'    => [
                        'mode' => 'cluster',
                        'shops' => [],
                        'clusters' => $clusters,
                        'bounds' => $responseBounds,
                        'total' => $totalCount,
                    ]
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $cell = $policy['cell'];

            $bucketSql = "
                SELECT
                    FLOOR((s.sh_lat_num - ?) / ?) AS lat_bucket,
                    FLOOR((s.sh_lng_num - ?) / ?) AS lng_bucket,
                    ROUND(AVG(s.sh_lat_num), 7) AS lat,
                    ROUND(AVG(s.sh_lng_num), 7) AS lng,
                    COUNT(*) AS cluster_count
                {$baseFrom}
                {$boundsWhere}
                GROUP BY lat_bucket, lng_bucket
                ORDER BY cluster_count DESC
            ";

            $bucketParams = array_merge(
                [$buffered['swLat'], $cell['lat'], $buffered['swLng'], $cell['lng']],
                $areaParams,
                $kwParams,
                $boundsParams
            );

            $bucketRows = $DB->rawQuery($bucketSql, $bucketParams);

            if ($DB->getLastErrno()) {
                throw new Exception($DB->getLastError());
            }

            $totalCount = 0;
            $clusters = [];
            $sparseBuckets = [];

            foreach ($bucketRows as $r) {
                $count = (int)$r['cluster_count'];
                $totalCount += $count;

                $bucketItem = [
                    'idx' => (string)$r['lat_bucket'] . '_' . (string)$r['lng_bucket'],
                    'lat' => (float)$r['lat'],
                    'lng' => (float)$r['lng'],
                    'cluster_count' => $count,
                ];

                if ($policy['mode'] === 'cluster') {
                    $clusters[] = $bucketItem;
                    continue;
                }

                if ($count >= (int)$policy['clusterMin']) {
                    $clusters[] = $bucketItem;
                } else {
                    $sparseBuckets[] = [
                        'lat_bucket' => (int)$r['lat_bucket'],
                        'lng_bucket' => (int)$r['lng_bucket'],
                    ];
                }
            }

            if ($policy['mode'] === 'cluster') {
                echo json_encode([
                    'success' => true,
                    'message' => 'OK',
                    'data'    => [
                        'mode' => 'cluster',
                        'shops' => [],
                        'clusters' => $clusters,
                        'bounds' => $responseBounds,
                        'total' => $totalCount,
                    ]
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $shops = [];

            if (!empty($sparseBuckets)) {
                $bucketWhereParts = [];
                $bucketWhereParams = [];

                foreach ($sparseBuckets as $bucket) {
                    $latMin = $buffered['swLat'] + ($bucket['lat_bucket'] * $cell['lat']);
                    $latMax = $latMin + $cell['lat'];
                    $lngMin = $buffered['swLng'] + ($bucket['lng_bucket'] * $cell['lng']);
                    $lngMax = $lngMin + $cell['lng'];

                    $bucketWhereParts[] = "
                        (
                            s.sh_lat_num >= ? AND s.sh_lat_num < ?
                            AND s.sh_lng_num >= ? AND s.sh_lng_num < ?
                        )
                    ";

                    $bucketWhereParams[] = $latMin;
                    $bucketWhereParams[] = $latMax;
                    $bucketWhereParams[] = $lngMin;
                    $bucketWhereParams[] = $lngMax;
                }

                $shopSql = "
                    SELECT
                        s.idx, s.sh_title, s.sh_branch_nm, s.sh_addr1, s.sh_addr2, s.sh_tel, m.mt_appr,
                        s.sh_lat_num AS lat,
                        s.sh_lng_num AS lng,
                        s.sh_img1, s.sh_img2, s.sh_img3,
                        s.sh_show, s.sh_takeout_yn, s.sh_reserve_yn, s.sh_qr_yn, s.sh_qr_pay_type,
                        {$distanceExpr} AS distance_m
                    {$baseFrom}
                    {$boundsWhere}
                    AND (" . implode(' OR ', $bucketWhereParts) . ")
                    ORDER BY distance_m ASC, s.idx DESC
                ";

                $shopParams = array_merge(
                    $refParams,
                    $areaParams,
                    $kwParams,
                    $boundsParams,
                    $bucketWhereParams
                );

                $shopRows = $DB->rawQuery($shopSql, $shopParams);

                if ($DB->getLastErrno()) {
                    throw new Exception($DB->getLastError());
                }

                $shops = build_shop_payload($shopRows);
            }

            echo json_encode([
                'success' => true,
                'message' => 'OK',
                'data'    => [
                    'mode' => 'mixed',
                    'shops' => $shops,
                    'clusters' => $clusters,
                    'bounds' => $responseBounds,
                    'total' => $totalCount,
                ]
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        if ($lat !== '' && $lng !== '') {
            $latF = (float)$lat;
            $lngF = (float)$lng;
            $radius = max(100, min(100000, $radius));

            $radiusExpr = "
                6371000 * ACOS(LEAST(GREATEST(
                    COS(RADIANS(?)) * COS(RADIANS(s.sh_lat_num)) *
                    COS(RADIANS(s.sh_lng_num) - RADIANS(?)) +
                    SIN(RADIANS(?)) * SIN(RADIANS(s.sh_lat_num))
                , -1.0), 1.0))
            ";

            $sql = "
                SELECT *
                FROM (
                    SELECT
                        s.idx, s.sh_title, s.sh_branch_nm, s.sh_addr1, s.sh_addr2, s.sh_tel, m.mt_appr,
                        s.sh_lat_num AS lat,
                        s.sh_lng_num AS lng,
                        s.sh_img1, s.sh_img2, s.sh_img3,
                        s.sh_show, s.sh_takeout_yn, s.sh_reserve_yn, s.sh_qr_yn, s.sh_qr_pay_type,
                        {$distanceExpr} AS distance_m,
                        {$radiusExpr} AS radius_m
                    {$baseFrom}
                      AND {$radiusExpr} <= ?
                ) t
                ORDER BY t.distance_m ASC
                LIMIT 3000
            ";

            $params = array_merge(
                $refParams,
                [$latF, $lngF, $latF],
                $areaParams,
                $kwParams,
                [$latF, $lngF, $latF, $radius]
            );

            $rows = $DB->rawQuery($sql, $params);

            if ($DB->getLastErrno()) {
                throw new Exception($DB->getLastError());
            }

            $shops = build_shop_payload($rows);

            echo json_encode([
                'success' => true,
                'message' => 'OK',
                'data'    => [
                    'mode' => 'shop',
                    'shops' => $shops,
                    'clusters' => [],
                    'bounds' => null,
                    'total' => count($shops),
                ]
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        echo json_encode([
            'success' => true,
            'message' => 'OK',
            'data'    => [
                'mode' => 'shop',
                'shops' => [],
                'clusters' => [],
                'bounds' => null,
                'total' => 0,
            ]
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

if ($act === 'changList') {
    try {
        $changeData = $_POST['key'];
        $_SESSION['order_mode'] = $changeData;

        echo json_encode([
            'success' => true,
            'message' => 'OK',
            'data' => $changeData,
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

echo json_encode([
    'success' => false,
    'message' => '잘못된 요청입니다.'
], JSON_UNESCAPED_UNICODE);
exit;
