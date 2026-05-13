<?php
// update.php : 테이블 관리 (JSON API)

include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";

header('Content-Type: application/json; charset=utf-8');

// 현재 선택된 매장
$sh_idx = (int)($_SESSION['current_sh_idx'] ?? 0);

$act = $_POST['act'] ?? '';

try {

    // ==========================
    // ① 테이블 추가
    // ==========================
    if ($act === 'add_table') {

        if ($sh_idx <= 0) {
            echo json_encode([
                'success' => false,
                'message' => '선택된 매장이 없습니다. 매장을 먼저 선택해 주세요.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $tb_name  = trim($_POST['tb_name']  ?? '');
        $tb_seats = (int)($_POST['tb_seats'] ?? 0);

        if ($tb_name === '' || $tb_seats <= 0) {
            echo json_encode([
                'success' => false,
                'message' => '테이블명과 좌석 수를 올바르게 입력해 주세요.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // tb_no 자동 증가 (현재 max + 1)
        $DB->where('sh_idx', $sh_idx);
        $maxNo  = (int)$DB->getValue('shop_table_t', 'MAX(tb_no)') ?: 0;
        $nextNo = $maxNo + 1;

        $data = [
            'sh_idx'   => $sh_idx,
            'tb_name'  => $tb_name,
            'tb_no'    => $nextNo,
            'tb_seats' => $tb_seats,
            'use_yn'   => 'Y',
        ];

        $newId = $DB->insert('shop_table_t', $data);

        if (!$newId) {
            echo json_encode([
                'success' => false,
                'message' => '테이블 추가에 실패했습니다.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        echo json_encode([
            'success'  => true,
            'message'  => '테이블이 추가되었습니다.',
            'table_id' => $newId,
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ==========================
    // ② 테이블 리스트 (JSON)
    // ==========================
    if ($act === 'list') {

        if ($sh_idx <= 0) {
            echo json_encode([
                'success' => false,
                'message' => '선택된 매장이 없습니다. 매장을 먼저 선택해 주세요.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // shop_table_t + shop_table_qr_t JOIN
        $DB->where('t.sh_idx', $sh_idx);
        $DB->orderBy('t.tb_no', 'ASC');
        $DB->orderBy('t.idx', 'ASC');
        $DB->join('shop_table_qr_t qr', 'qr.tb_idx = t.idx', 'LEFT');
        $rows = $DB->get('shop_table_t t', null, '
            t.idx,
            t.tb_name,
            t.tb_seats,
            t.use_yn,
            qr.qr_file
        ');

        $tables = [];

        foreach ($rows as $row) {
            $hasQr  = !empty($row['qr_file']);
            $tables[] = [
                'id'           => (int)$row['idx'],
                'name'         => $row['tb_name'],
                'seats'        => (int)$row['tb_seats'],
                'status'       => 'empty',      // 지금은 전부 empty
                'elapsed'      => null,         // 확장용
                'amount'       => null,
                'reserved_name'=> null,
                'reserved_time'=> null,
                // ✅ QR 관련
                'qr_generated' => $hasQr,
                'qr_url'       => $hasQr ? $row['qr_file'] : null,
            ];
        }

        $summary = [
            'empty'    => 0,
            'ordering' => 0,
            'reserved' => 0,
            'payment'  => 0,
        ];
        foreach ($tables as $t) {
            if (isset($summary[$t['status']])) {
                $summary[$t['status']]++;
            }
        }

        echo json_encode([
            'success' => true,
            'summary' => $summary,
            'tables'  => $tables,
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ==========================
    // ③ QR 코드 생성
    // ==========================
    if ($act === 'generate_qr') {

        if ($sh_idx <= 0) {
            echo json_encode([
                'success' => false,
                'message' => '선택된 매장이 없습니다. 매장을 먼저 선택해 주세요.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $tableId   = (int)($_POST['table_id'] ?? 0);
        $tableName = trim($_POST['table_name'] ?? '');

        if ($tableId <= 0 || $tableName === '') {
            echo json_encode([
                'success' => false,
                'message' => '유효하지 않은 테이블 정보입니다.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $shopInfo = encrypt_member_id($sh_idx.'/'.$tableName);
        // QR에 넣을 URL (원하는 URL로 수정)
        $baseUrl = 'https://qrorder.epicque.com?table_id=';
        $qrText  = $baseUrl . $shopInfo;

        // 외부 QR 생성 API
        $encoded = urlencode($qrText);
        $qrUrl   = "https://api.qrserver.com/v1/create-qr-code/?size=260x260&data={$encoded}";

        $qrDir = $_SERVER['DOCUMENT_ROOT'] . '/data/qr/'.$sh_idx.'/';
        if (!is_dir($qrDir)) {
            mkdir($qrDir, 0775, true);
        }

        // 파일명: table_1.png
        $fileName = "table_{$tableId}.png";
        $filePath = $qrDir . $fileName;
        $fileUrl  = '/data/qr/'.$sh_idx.'/'.$fileName;   // ✅ 웹에서 쓰는 상대 경로

        $imgData = @file_get_contents($qrUrl);
        if ($imgData === false) {
            echo json_encode([
                'success' => false,
                'message' => 'QR 서버에서 이미지를 가져오지 못했습니다.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $result = @file_put_contents($filePath, $imgData);
        if ($result === false) {
            echo json_encode([
                'success' => false,
                'message' => 'QR 이미지를 저장하지 못했습니다.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // ✅ DB upsert: shop_table_qr_t
        $qrData = [
            'sh_idx'  => $sh_idx,
            'tb_idx'  => $tableId,
            'qr_text' => $qrText,
            'qr_file' => $fileUrl,
        ];

        $DB->where('tb_idx', $tableId);
        $exist = $DB->getOne('shop_table_qr_t', 'idx');

        if ($exist) {
            $DB->where('idx', $exist['idx']);
            if (!$DB->update('shop_table_qr_t', $qrData)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'QR 정보 업데이트에 실패했습니다.'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }
            $qr_idx = (int)$exist['idx'];
        } else {
            $qr_idx = $DB->insert('shop_table_qr_t', $qrData);
            if (!$qr_idx) {
                echo json_encode([
                    'success' => false,
                    'message' => 'QR 정보 저장에 실패했습니다.'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }
        }

        echo json_encode([
            'success'      => true,
            'message'      => 'QR 코드가 생성되었습니다.',
            'qr_id'        => $qr_idx,
            'qr_url'       => $fileUrl,      // 서버에 저장된 PNG
            'table_id'     => $tableId,
            'table_name'   => $tableName,
            'qr_generated' => true,
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($act === 'delete_table') {

        if ($sh_idx <= 0) {
            echo json_encode([
                'success' => false,
                'message' => '선택된 매장이 없습니다. 매장을 먼저 선택해 주세요.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $table_id = (int)($_POST['table_id'] ?? 0);
        if ($table_id <= 0) {
            throw new Exception('삭제할 테이블 정보가 올바르지 않습니다.');
        }

        // ✅ 1) 이 테이블이 현재 선택된 매장(sh_idx)에 속하는지 확인
        $DB->where('idx', $table_id);
        $DB->where('sh_idx', $sh_idx);
        $tableRow = $DB->getOne('shop_table_t', 'idx, sh_idx');

        if (!$tableRow) {
            throw new Exception('해당 테이블 정보를 찾을 수 없습니다.');
        }

        // ✅ 2) QR 정보 조회 (shop_table_qr_t)
        $DB->where('tb_idx', $table_id);
        $DB->where('sh_idx', $sh_idx);
        $qrRow = $DB->getOne('shop_table_qr_t', 'idx, qr_file');

        // ✅ 3) DB 삭제를 트랜잭션으로 처리
        $DB->startTransaction();

        // (1) QR 레코드 삭제
        if ($qrRow && isset($qrRow['idx'])) {
            $DB->where('idx', (int)$qrRow['idx']);
            if (!$DB->delete('shop_table_qr_t')) {
                $DB->rollback();
                throw new Exception('QR 정보 삭제 중 오류가 발생했습니다.');
            }
        }

        // (2) 테이블 레코드 삭제
        $DB->where('idx', $table_id);
        $DB->where('sh_idx', $sh_idx);
        if (!$DB->delete('shop_table_t')) {
            $DB->rollback();
            throw new Exception('테이블 삭제 중 오류가 발생했습니다.');
        }

        $DB->commit();

        // ✅ 4) 실제 QR 이미지 파일 삭제 (DB 커밋 후 파일 삭제)
        if ($qrRow && !empty($qrRow['qr_file'])) {
            // qr_file 예: /data/qr/3/table_1.png
            $qrFilePath = $_SERVER['DOCUMENT_ROOT'] . $qrRow['qr_file'];

            if (file_exists($qrFilePath)) {
                @unlink($qrFilePath);
            }
        }

        echo json_encode([
            'success' => true,
            'message' => '테이블과 QR 코드 정보가 삭제되었습니다.',
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ==========================
    // ④ 기타 act
    // ==========================
    echo json_encode([
        'success' => false,
        'message' => '지원하지 않는 요청입니다.',
    ], JSON_UNESCAPED_UNICODE);
    exit;

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => '예외가 발생했습니다: ' . $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
