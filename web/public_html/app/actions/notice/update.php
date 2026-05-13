<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";

header('Content-Type: application/json');

if($_POST['act']=='list') {
    try {
        $page    = isset($_POST['page']) ? max(1, (int)$_POST['page']) : 1;
        $perPage = isset($_POST['per_page']) ? max(1, (int)$_POST['per_page']) : 10;
        $search  = trim($_POST['search'] ?? '');

        $DB->where('nt_show', 'Y');
        $DB->where('del_date', null, 'IS'); // del_date IS NULL

        if ($search !== '') {
            $like = '%' . $search . '%';
            $DB->where('(nt_title LIKE ? OR nt_content LIKE ?)', [$like, $like]);
        }

        $total = (int)$DB->getValue('notice_t', 'COUNT(*)');
        $totalPages = $total > 0 ? (int)ceil($total / $perPage) : 1;
        if ($totalPages <= 0) {
            $totalPages = 1;
        }
        if ($page > $totalPages) {
            $page = $totalPages;
        }

        $offset = ($page - 1) * $perPage;

        if ($search !== '') {
            $like = '%' . $search . '%';
            // 제목 + 내용 검색
            $DB->where('(nt_title LIKE ? OR nt_content LIKE ?)', [$like, $like]);
        }

        $DB->orderBy('nt_order', 'DESC');
        $DB->orderBy('nt_wdate', 'DESC');
        $DB->orderBy('idx', 'DESC');

        $rows = $DB->get('notice_t', [$offset, $perPage], ['idx', 'nt_title', 'nt_wdate']);

        $notices = [];
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

        $pageBlockSize = 5;
        $currentBlock  = (int)ceil($page / $pageBlockSize);
        $startPage     = ($currentBlock - 1) * $pageBlockSize + 1;
        $endPage       = min($startPage + $pageBlockSize - 1, $totalPages);

        $data = [
            'notices'     => $notices,
            'page'        => $page,
            'per_page'    => $perPage,
            'total'       => $total,
            'total_pages' => $totalPages,
            'pagination'  => [
                'page'            => $page,
                'per_page'        => $perPage,
                'total'           => $total,
                'total_pages'     => $totalPages,
                'page_block_size' => $pageBlockSize,
                'start_page'      => $startPage,
                'end_page'        => $endPage,
            ],
        ];

        echo json_encode([
            'success' => true,
            'data'    => $data,
            'sql'   => $DB->getLastQuery(),
            'search' => $search,
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage(),
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

?>
