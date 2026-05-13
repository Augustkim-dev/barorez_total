<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";

$redirect_url = './list.php';
// -----------------------------
// 공통: 쿠폰 코드 생성
// -----------------------------
function generate_coupon_code_simple($length = 10) {
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // 헷갈리는 문자 제외
    $max   = strlen($chars) - 1;
    $code  = '';

    for ($i = 0; $i < $length; $i++) {
        $code .= $chars[mt_rand(0, $max)];
    }
    return $code;
}

/* ============================================================
 * 1) 쿠폰 등록 (JSON)
 * ========================================================== */
if ($_POST['act'] == 'input') {
    header('Content-Type: application/json; charset=utf-8');

    try {

        $ct_title          = trim($_POST['ct_title'] ?? '');
        $ct_show           = $_POST['ct_show'] ?? 'Y';

        $ct_type1          = (int)($_POST['ct_type1'] ?? 1); // 1: 기간 설정, 2: 발급일 기준
        $ct_sdate          = trim($_POST['ct_sdate'] ?? '');
        $ct_edate          = trim($_POST['ct_edate'] ?? '');
        $ct_days           = (int)($_POST['ct_days'] ?? 0);

        $ct_type2          = (int)($_POST['ct_type2'] ?? 1); // 현재는 항상 1(정액)
        $ct_discount1      = (int)($_POST['ct_discount1'] ?? 0);
        $ct_discount3      = (int)($_POST['ct_discount3'] ?? 0);

        $ct_target_scope   = $_POST['ct_target_scope'] ?? 'ALL';
        $ct_target_members = trim($_POST['ct_target_members'] ?? '');

        $ct_memo           = trim($_POST['ct_memo'] ?? '');

        $sh_idx = (int)($_SESSION['current_sh_idx'] ?? 0);

        // --------- 검증 ---------
        if ($ct_title === '') {
            throw new Exception('쿠폰명을 입력해주세요.');
        }

        if ($ct_discount1 <= 0) {
            throw new Exception('할인 금액을 1원 이상 입력해주세요.');
        }

        if (!in_array($ct_type1, [1, 2], true)) {
            $ct_type1 = 1;
        }

        if ($ct_type1 === 1) {
            if ($ct_sdate === '' || $ct_edate === '') {
                throw new Exception('유효기간 시작일과 종료일을 입력해주세요.');
            }
            if ($ct_sdate > $ct_edate) {
                throw new Exception('종료일은 시작일 이후여야 합니다.');
            }
            $ct_days = 0;
        } else {
            if ($ct_days <= 0) {
                throw new Exception('발급일 기준 유효일수를 1일 이상 입력해주세요.');
            }
            $ct_sdate = '';
            $ct_edate = '';
        }

        if (!in_array($ct_target_scope, ['ALL', 'MEMBER'], true)) {
            $ct_target_scope = 'ALL';
        }

        if ($ct_target_scope === 'MEMBER' && $ct_target_members === '') {
            throw new Exception('특정 회원 발급으로 설정하셨습니다. 회원을 지정해주세요.');
        }

        if (!in_array($ct_show, ['Y', 'N'], true)) {
            $ct_show = 'Y';
        }

        // --------- 쿠폰 코드 자동 생성 ---------
        $ct_code = generate_coupon_code_simple();

        // --------- INSERT ---------
        $DB->startTransaction();

        $max_order = $DB->getValue('coupon_t', 'COALESCE(MAX(ct_order), 0) + 1');

        $arr_query = [
            'ct_code'           => $ct_code,
            'sh_idx'            => $sh_idx,

            'ct_title'          => $ct_title,
            'ct_type1'          => $ct_type1,
            'ct_type2'          => $ct_type2,

            'ct_discount1'      => $ct_discount1,
            'ct_discount3'      => $ct_discount3,

            'ct_sdate'          => ($ct_sdate !== '' ? $ct_sdate : null),
            'ct_edate'          => ($ct_edate !== '' ? $ct_edate : null),
            'ct_days'           => ($ct_type1 === 2 ? $ct_days : null),

            'ct_show'           => $ct_show,

            'ct_target_scope'   => $ct_target_scope,
            'ct_target_members' => ($ct_target_scope === 'MEMBER' && $ct_target_members !== '') ? $ct_target_members : null,

            'ct_memo'           => ($ct_memo !== '' ? $ct_memo : null),

            'ct_order'          => $max_order,
            'ct_wdate'          => $DB->now(),
            'ct_udate'          => null,

            'ct_del_yn'         => 'N',
            'ct_del_date'       => null,
        ];

        $_last_idx = $DB->insert('coupon_t', $arr_query);
        if (!$_last_idx) {
            throw new Exception('쿠폰 등록 중 오류가 발생했습니다.');
        }

        $DB->commit();

        echo json_encode([
            'success' => true,
            'message' => '쿠폰이 성공적으로 등록되었습니다.',
            'idx'     => $_last_idx,
            'redirect'=> $redirect_url,
        ], JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        $DB->rollback();
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage(),
        ], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

/* ============================================================
 * 2) 쿠폰 수정 (JSON)
 * ========================================================== */
else if ($_POST['act'] == 'update') {
    header('Content-Type: application/json; charset=utf-8');

    try {
        $ct_idx = (int)($_POST['ct_idx'] ?? 0);
        if ($ct_idx <= 0) {
            throw new Exception('잘못된 접근입니다.');
        }

        $DB->where('idx', $ct_idx);
        $coupon = $DB->getOne('coupon_t');
        if (!$coupon) {
            throw new Exception('존재하지 않는 쿠폰입니다.');
        }

        $ct_title          = trim($_POST['ct_title'] ?? '');
        $ct_show           = $_POST['ct_show'] ?? 'Y';

        $ct_type1          = (int)($_POST['ct_type1'] ?? 1);
        $ct_sdate          = trim($_POST['ct_sdate'] ?? '');
        $ct_edate          = trim($_POST['ct_edate'] ?? '');
        $ct_days           = (int)($_POST['ct_days'] ?? 0);

        $ct_type2          = (int)($_POST['ct_type2'] ?? 1);
        $ct_discount1      = (int)($_POST['ct_discount1'] ?? 0);
        $ct_discount3      = (int)($_POST['ct_discount3'] ?? 0);

        $ct_target_scope   = $_POST['ct_target_scope'] ?? 'ALL';
        $ct_target_members = trim($_POST['ct_target_members'] ?? '');

        $ct_memo           = trim($_POST['ct_memo'] ?? '');

        if ($ct_title === '') {
            throw new Exception('쿠폰명을 입력해주세요.');
        }

        if ($ct_discount1 <= 0) {
            throw new Exception('할인 금액을 1원 이상 입력해주세요.');
        }

        if (!in_array($ct_type1, [1, 2], true)) {
            $ct_type1 = 1;
        }

        if ($ct_type1 === 1) {
            if ($ct_sdate === '' || $ct_edate === '') {
                throw new Exception('유효기간 시작일과 종료일을 입력해주세요.');
            }
            if ($ct_sdate > $ct_edate) {
                throw new Exception('종료일은 시작일 이후여야 합니다.');
            }
            $ct_days = 0;
        } else {
            if ($ct_days <= 0) {
                throw new Exception('발급일 기준 유효일수를 1일 이상 입력해주세요.');
            }
            $ct_sdate = '';
            $ct_edate = '';
        }

        if (!in_array($ct_target_scope, ['ALL', 'MEMBER'], true)) {
            $ct_target_scope = 'ALL';
        }

        if ($ct_target_scope === 'MEMBER' && $ct_target_members === '') {
            throw new Exception('특정 회원 발급으로 설정하셨습니다. 회원을 지정해주세요.');
        }

        if (!in_array($ct_show, ['Y', 'N'], true)) {
            $ct_show = 'Y';
        }

        // 코드 변경 불가: 기존 코드 사용
        $ct_code = $coupon['ct_code'];

        $DB->startTransaction();

        $arr_query = [
            'ct_code'           => $ct_code,
            'ct_title'          => $ct_title,
            'ct_type1'          => $ct_type1,
            'ct_type2'          => $ct_type2,
            'ct_discount1'      => $ct_discount1,
            'ct_discount3'      => $ct_discount3,
            'ct_sdate'          => ($ct_sdate !== '' ? $ct_sdate : null),
            'ct_edate'          => ($ct_edate !== '' ? $ct_edate : null),
            'ct_days'           => ($ct_type1 === 2 ? $ct_days : null),
            'ct_show'           => $ct_show,
            'ct_target_scope'   => $ct_target_scope,
            'ct_target_members' => ($ct_target_scope === 'MEMBER' && $ct_target_members !== '') ? $ct_target_members : null,
            'ct_memo'           => ($ct_memo !== '' ? $ct_memo : null),
            'ct_udate'          => $DB->now(),
        ];

        $DB->where('idx', $ct_idx);
        if ($DB->update('coupon_t', $arr_query) === false) {
            throw new Exception('쿠폰 수정 중 오류가 발생했습니다.');
        }

        $DB->commit();

        echo json_encode([
            'success' => true,
            'message' => '쿠폰이 성공적으로 수정되었습니다.',
            'idx'     => $ct_idx,
            'redirect'=> $redirect_url,
        ], JSON_UNESCAPED_UNICODE);

    } catch (Exception $e) {
        $DB->rollback();
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage(),
        ], JSON_UNESCAPED_UNICODE);
    }

    exit;
}

/* ============================================================
 * 3) 쿠폰 삭제 (JSON, 소프트 삭제)
 * ========================================================== */
else if ($_POST['act'] == 'delete') {
    header('Content-Type: application/json; charset=utf-8');

    try {
        $ct_idx = (int)($_POST['ct_idx'] ?? 0);
        if ($ct_idx <= 0) {
            throw new Exception('삭제할 쿠폰을 선택해주세요.');
        }

        $DB->startTransaction();

        $DB->where('idx', $ct_idx);
        $coupon = $DB->getOne('coupon_t');
        if (!$coupon) {
            throw new Exception('존재하지 않는 쿠폰입니다.');
        }

        $DB->where('idx', $ct_idx);
        $res = $DB->update('coupon_t', [
            'ct_del_yn'   => 'Y',
            'ct_del_date' => $DB->now(),
        ]);

        if ($res === false) {
            throw new Exception('쿠폰 삭제 중 오류가 발생했습니다.');
        }

        $DB->commit();

        echo json_encode([
            'success' => true,
            'message' => '쿠폰이 삭제(비활성화) 처리되었습니다.',
            'redirect'=> $redirect_url,
        ], JSON_UNESCAPED_UNICODE);

    } catch (Exception $e) {
        $DB->rollback();
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage(),
        ], JSON_UNESCAPED_UNICODE);
    }

    exit;
}

/* ============================================================
 * 4) 쿠폰 리스트 (HTML 응답)
 * ========================================================== */
else if ($_POST['act'] == "list") {

    // 한 페이지당 개수 / 페이지 번호
    $rows = isset($_POST['obj_limit_num']) ? (int)$_POST['obj_limit_num'] : 10;
    $pg   = isset($_POST['obj_pg'])        ? (int)$_POST['obj_pg']        : 1;

    $DB->pageLimit = $rows;

    // --------------------------------------------------
    // 1) 통합 검색 (쿠폰명 + 코드)
    //    - 지금 list.php 에 검색어 UI 없으면 무시되어도 상관 없음
    // --------------------------------------------------
    if (!empty($_POST['obj_search_txt'])) {
        $txt = trim($_POST['obj_search_txt']);
        $sel = $_POST['obj_sel_search'] ?? 'all';

        if ($sel == 'all' || $sel == '') {
            $DB->where("(INSTR(a1.ct_title, ?) OR INSTR(a1.ct_code, ?))", [$txt, $txt]);
        } elseif (in_array($sel, ['ct_title', 'ct_code'], true)) {
            $DB->where("INSTR(a1.$sel, ?)", [$txt]);
        }
    }

    // --------------------------------------------------
    // 2) 발급 대상 필터 (ct_target_scope)
    //    - 버튼에서 오는 값: '' | 'ALL' | 'MEMBER'
    // --------------------------------------------------
    if (!empty($_POST['obj_search_level'])) {
        $scope = $_POST['obj_search_level'];   // 'ALL' or 'MEMBER'
        $DB->where('a1.ct_target_scope', $scope);
    }

    // --------------------------------------------------
    // 3) 쿠폰 상태 필터 (ct_show)
    //    - 버튼에서 오는 값: '' | 'Y' | 'N'
    // --------------------------------------------------
    if (!empty($_POST['obj_search_status'])) {
        $DB->where('a1.ct_show', $_POST['obj_search_status']);  // 'Y' or 'N'
    }

    // --------------------------------------------------
    // 4) 등록일 필터 (ct_wdate)
    //    - search_day 가 있으면 버튼 기준
    //    - 없고 sdate/edate 가 있으면 날짜 범위 기준
    // --------------------------------------------------
    $search_day = $_POST['obj_search_day'] ?? '';
    $sdate      = $_POST['obj_sdate']      ?? '';
    $edate      = $_POST['obj_edate']      ?? '';
    $range      = $_POST['range']      ?? 'all';   // all, today, 3d, 7d, 1m
    $start_date = trim($_POST['start_date'] ?? '');
    $end_date   = trim($_POST['end_date']   ?? '');
    $status     = $_POST['status']     ?? 'all';   // all, READY, PLANNED, DONE

    $today = date('Y-m-d');

// range !== all 인 경우, st_wdate(정산 신청일) 기준으로 사용할 날짜 보정
    if ($range !== 'all') {
        if ($start_date === '' || $end_date === '') {
            if ($range === 'today') {
                $start_date = $today;
                $end_date   = $today;
            } elseif ($range === '3d') {
                $start_date = date('Y-m-d', strtotime('-2 days', strtotime($today)));
                $end_date   = $today;
            } elseif ($range === '7d') {
                $start_date = date('Y-m-d', strtotime('-6 days', strtotime($today)));
                $end_date   = $today;
            } elseif ($range === '1m') {
                $start_date = date('Y-m-d', strtotime('-1 month', strtotime($today)));
                $end_date   = $today;
            }
        }
    }

    if ($sdate && $edate) {
        $DB->where("DATE(a1.ct_wdate) BETWEEN ? AND ?", [$sdate, $edate]);
    }

    // --------------------------------------------------
    // 5) 삭제 안 된 쿠폰만
    // --------------------------------------------------
    $DB->where('a1.ct_del_yn', 'N');

    // --------------------------------------------------
    // 6) 정렬
    // --------------------------------------------------
    $order_dir = (isset($_POST['obj_order_desc_asc']) && $_POST['obj_order_desc_asc'] == '1')
        ? 'desc'
        : 'asc';

    // 정렬 컬럼이 필요하면 여기서 분기 (지금은 idx 기준)
    $DB->orderBy('a1.idx', $order_dir);

    // --------------------------------------------------
    // 7) 리스트 조회 + 페이징
    // --------------------------------------------------
    $list = $DB->arraybuilder()->paginate("coupon_t a1", $pg, 'a1.*, a1.idx as ct_idx');

    $n_page = $DB->totalPages;
    $counts = $DB->totalCount;
    $counts = $counts - (($pg - 1) * $rows);
    ?>
    <div class="table-responsive margin-top-20">
        <table class="table table-striped table-bordered margin-bottom-20" id="listTable" style="min-width: 900px">
            <thead class="thead-dark">
            <tr>
                <th class="text-center" style="width:80px;">번호</th>
                <th class="text-center">쿠폰명</th>
                <th class="text-center" style="width:120px;">발급대상</th>
                <th class="text-center" style="width:150px;">쿠폰 할인</th>
                <th class="text-center" style="width:140px;">최소 주문금액</th>
                <th class="text-center" style="width:180px;">사용 기간</th>
                <th class="text-center" style="width:100px;">사용 유무</th>
                <th class="text-center" style="width:140px;">발급일시</th>
                <th class="text-center" style="width:130px;">관리</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($list) {
                foreach ($list as $row) {

                    $no = $counts--;                          // 번호
                    $title = $row['ct_title'];               // 쿠폰명
                    $code  = $row['ct_code'];                // 쿠폰 코드

                    // 발급 대상
                    $targetScope = $row['ct_target_scope'];  // ALL / MEMBER
                    $targetText  = ($targetScope === 'MEMBER') ? '특정 회원' : '전체 회원';

                    // 할인 정보
                    if ((int)$row['ct_type2'] === 1) {       // 정액
                        $discountText = number_format((int)$row['ct_discount1']).'원';
                    } else {                                 // 정율
                        $discountText = (int)$row['ct_discount1'].'%';
                    }

                    // 최소 주문 금액
                    $minOrderText = $row['ct_discount3'] > 0
                        ? number_format((int)$row['ct_discount3']).'원'
                        : '-';

                    // 사용 기간
                    if ((int)$row['ct_type1'] === 1) {
                        // 기간 설정: ct_sdate ~ ct_edate
                        $start = $row['ct_sdate'] ?: '';
                        $end   = $row['ct_edate'] ?: '';
                        $periodText = ($start && $end)
                            ? $start.' ~ '.$end
                            : ($start ?: $end ?: '-');
                    } else {
                        // 발급일 + N일
                        $periodText = '발급일 기준 '.$row['ct_days'].'일';
                    }

                    // 사용 여부
                    $useText = ($row['ct_show'] === 'Y') ? '사용' : '미사용';

                    // 발급일시
                    $wdate = $row['ct_wdate'] ? $row['ct_wdate'] : '-';
                    ?>
                    <tr>
                        <td class="text-center"><?= $no ?></td>
                        <td class="text-center">
                            <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?><br>
                            <small class="text-muted"><?= htmlspecialchars($code, ENT_QUOTES, 'UTF-8'); ?></small>
                        </td>
                        <td class="text-center"><?= $targetText; ?></td>
                        <td class="text-center"><?= $discountText; ?></td>
                        <td class="text-center"><?= $minOrderText; ?></td>
                        <td class="text-center"><?= htmlspecialchars($periodText, ENT_QUOTES, 'UTF-8'); ?></td>
                        <td class="text-center"><?= $useText; ?></td>
                        <td class="text-center"><?= htmlspecialchars($wdate, ENT_QUOTES, 'UTF-8'); ?></td>
                        <td class="text-center">
                            <button type="button" class="btn btn-outline-primary"
                                    onclick="coupon_detail('<?= $row['ct_idx'] ?>')">
                                상세
                            </button>
                            <button type="button" class="btn btn-outline-danger"
                                    onclick="coupon_delete('<?= $row['ct_idx'] ?>')">
                                삭제
                            </button>
                        </td>
                    </tr>
                    <?php
                }
            } else { ?>
                <tr>
                    <td colspan="9" class="text-center"><b>등록된 쿠폰이 없습니다.</b></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>

    <?php
    // 페이징
    if ($n_page > 1) {
        echo page_listing_xhr($pg, $n_page, 'f_get_coupon_list');
    }

    exit;
}

/* ============================================================
 * X) 발급 대상: 회원 목록 (모달에서 사용)
 *  - mt_level = 2 회원만
 * ========================================================== */
else if ($_POST['act'] == 'member_list') {

    // HTML 조각 반환 (JSON 아님)
    header('Content-Type: text/html; charset=utf-8');

    // 한 번에 너무 많이 안 뿌리게 기본 100명 제한 (원하면 늘려도 됨)
    $limit = 100;

    $DB->where('a1.mt_level', 2);      // ★ 레벨 2 회원만
    $DB->where('a1.mt_status', 'Y');   // 사용중 회원만 (필요 없으면 지워도 됨)
    $DB->orderBy('a1.idx', 'desc');

    $list = $DB->get('member_t a1', $limit, 'a1.idx as mt_idx, a1.mt_id, a1.mt_name, a1.mt_hp');
    ?>
    <table class="table table-sm table-bordered mb-0">
        <thead class="thead-light">
        <tr>
            <th class="text-center" style="width:40px;">
                <input type="checkbox" id="member_check_all">
            </th>
            <th class="text-center" style="width:120px;">이름</th>
            <th class="text-center" style="width:160px;">아이디</th>
            <th class="text-center" style="width:160px;">휴대폰번호</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($list) {
            foreach ($list as $row) { ?>
                <tr>
                    <td class="text-center">
                        <input type="checkbox"
                               class="member-check"
                               data-mt-idx="<?=$row['mt_idx']?>"
                               data-mt-name="<?=htmlspecialchars($row['mt_name'], ENT_QUOTES)?>"
                               data-mt-id="<?=htmlspecialchars($row['mt_id'], ENT_QUOTES)?>">
                    </td>
                    <td class="text-center"><?=htmlspecialchars($row['mt_name'], ENT_QUOTES)?></td>
                    <td class="text-center"><?=htmlspecialchars($row['mt_id'], ENT_QUOTES)?></td>
                    <td class="text-center"><?=htmlspecialchars($row['mt_hp'], ENT_QUOTES)?></td>
                </tr>
            <?php }
        } else { ?>
            <tr>
                <td colspan="4" class="text-center">표시할 회원이 없습니다.</td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

    <script>
        // 전체 선택
        $('#member_check_all').on('change', function () {
            const checked = $(this).is(':checked');
            $('#member_list_container').find('.member-check').prop('checked', checked);
        });
    </script>
    <?php
    exit;
}
/* ============================================================
 * 그 외 잘못된 요청
 * ========================================================== */
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'success' => false,
    'message' => '잘못된 요청입니다. (act 값 확인 필요)',
], JSON_UNESCAPED_UNICODE);
exit;
