<?php
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
$_ENABLED_INC_TOP = true;
$_ENABLED_INC_QUICK = true;
//$_SUB_HEAD_TITLE = "로그인";
include_once $_SERVER['DOCUMENT_ROOT']."/app/inc/head.php";
$_SUB_HEAD_TITLE = "공지사항"; //헤더에 타이틀명이 없을경우 공백
$hd_num = 2; //모바일 hd 1~n까지 있음
$_GET['bt_menu'] = ''; //모바일 하단메뉴 있음1, 없음 공백
include_once $_SERVER['DOCUMENT_ROOT']."/app/inc/header.php";

// ==========================================
//  notice_t → $notices 배열로 변환
// ==========================================
$notices = [];

try {
    // 노출 여부 Y, 삭제되지 않은 공지사항만
    $DB->where('nt_show', 'Y');
    $DB->where('del_date', null, 'IS');  // del_date IS NULL

    // 정렬: nt_order 우선, 그 다음 최신 등록일, idx 역순
    $DB->orderBy('nt_order', 'DESC');
    $DB->orderBy('nt_wdate', 'DESC');
    $DB->orderBy('idx', 'DESC');

    // 필요한 컬럼만 조회
    $rows = $DB->get('notice_t', null, ['idx', 'nt_title', 'nt_wdate']);

    if ($rows) {
        foreach ($rows as $row) {
            $regdate = '';
            if (!empty($row['nt_wdate'])) {
                $regdate = date('Y.m.d', strtotime($row['nt_wdate']));
            }

            $notices[] = [
                'idx'     => (int)$row['idx'],
                'title'   => $row['nt_title'],
                'regdate' => $regdate,
            ];
        }
    }

} catch (Exception $e) {
    // 에러가 나도 페이지가 죽지 않게만 처리 (로그만 남기고 $notices는 빈 배열 유지)
    error_log('[NOTICE_LIST_ERROR] '.$e->getMessage());
}

$view_path = VIEWS_NOTICE_PATH."/list.php";
if (file_exists($view_path)) {
    include_once $view_path;
} else {

}

include_once $_SERVER['DOCUMENT_ROOT'] . "/app/inc/tail.php";
?>
