<?php
// /mng/shop/update.php

include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";

$tbl_member_name = $CFG_TBL['member']['default'];   // member_t
$tbl_shop_name   = $CFG_TBL['shop']['default'];     // shop_t

// 메뉴 관련 테이블
$tbl_shop_menu      = $CFG_TBL['menu']['default'];          // shop_menu_t
$tbl_shop_category  = $CFG_TBL['menu']['category'];      // shop_category_t
$tbl_moc            = $CFG_TBL['option']['category']; // menu_option_category_t
$tbl_option_menu    = $CFG_TBL['option']['default'];        // option_menu_t

// 결제
$tbl_order_name    = $CFG_TBL['orders']['default'];     // orders_t

$act = $_POST['act'] ?? '';

/**
 * 공통: JSON 응답 헬퍼
 */
function json_response($success, $message = '', $extra = []) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array_merge([
        'success' => $success,
        'message' => $message,
    ], $extra), JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * 1) 매장 리스트 (Ajax)
 * - act = list
 * - member_t(mt_level=5, mt_appr='Y') + shop_t 조인
 */
if ($act === 'list') {

    unset($list);

    $pageLimit = (int)($_POST['obj_limit_num'] ?? 10);
    $DB->pageLimit = $pageLimit;
    $pg            = (int)($_POST['obj_pg'] ?? 1);

    $obj_search_status = $_POST['obj_search_status'] ?? 'all'; // 지역
    $obj_sel_search    = $_POST['obj_sel_search']    ?? 'all'; // 검색필드
    $obj_search_txt    = $_POST['obj_search_txt']    ?? '';
    $obj_search_day    = $_POST['obj_search_day']    ?? '';
    $sdate             = $_POST['sdate']             ?? '';
    $edate             = $_POST['edate']             ?? '';

    // ================================
    // ① 기본 조인/조건: 가맹점주 + 매장
    // ================================
    // s: shop_t, m: member_t
    // fk_sh_member (mb_idx → member_t.idx)
    $DB->join($tbl_member_name . " m", "s.mb_idx = m.idx", "INNER");

    // 가맹점주만
    $DB->where('m.mt_level', 5);
    $DB->where('m.mt_appr', ['Y','T'], 'IN');

    // 삭제 매장 제외 : del_date IS NULL
    $DB->where('s.del_date', null, 'IS');

    // ================================
    // ② 지역 필터 (주소 기반)
    // ================================
    if ($obj_search_status !== '' && $obj_search_status !== 'all') {
        // 지역 셀렉트의 value(예: '서울', '부산' 등)가 sh_addr1 안에 포함되는지 체크
        $loc = $obj_search_status;
        $DB->where("( instr(s.sh_addr1, '{$loc}') )");
    }

    // ================================
    // ③ 검색어 필터
    // ================================
    if ($obj_search_txt !== '') {
        $search_txt = $obj_search_txt;

        if ($obj_sel_search === 'name') {
            // 매장명만 (sh_title)
            $DB->where("( instr(s.sh_title, '{$search_txt}') )");
        } elseif ($obj_sel_search === 'rt_name') {
            // 대표자명만 (매장 대표자명 또는 가맹점주 이름)
            $DB->where("(
                instr(s.sh_ceo_nm, '{$search_txt}')
                OR instr(m.mt_name, '{$search_txt}')
            )");
        } else {
            // 통합검색: 매장명 + 대표자명 + 연락처
            $DB->where("(
                instr(s.sh_title, '{$search_txt}')
                OR instr(s.sh_ceo_nm, '{$search_txt}')
                OR instr(m.mt_name, '{$search_txt}')
                OR instr(m.mt_hp, '{$search_txt}')
            )");
        }
    }

    // ================================
    // ④ 날짜(등록일) 필터
    // ================================
    // shop_t 등록일 컬럼: sh_wdate
    $dateColumn = 's.sh_wdate';

    if ($obj_search_day !== '') {
        $today = date('Y-m-d');

        if ($obj_search_day == '1') {
            // 오늘
            $DB->where($dateColumn, $today . ' 00:00:00', '>=');
        } elseif ($obj_search_day == '2') {
            // 최근 7일
            $start_date = date('Y-m-d', strtotime('-7 days'));
            $DB->where($dateColumn, $start_date . ' 00:00:00', '>=');
        } elseif ($obj_search_day == '3') {
            // 최근 30일
            $start_date = date('Y-m-d', strtotime('-30 days'));
            $DB->where($dateColumn, $start_date . ' 00:00:00', '>=');
        }
    }

    if ($sdate && $edate) {
        $start = $sdate . ' 00:00:00';
        $end   = $edate . ' 23:59:59';
        $DB->where($dateColumn, [$start, $end], 'BETWEEN');
    }

    // ================================
    // ⑤ 정렬
    // ================================
    $orderby        = $_POST['obj_orderby']        ?? '';
    $order_desc_asc = $_POST['obj_order_desc_asc'] ?? '1';

    if ($orderby) {
        $DB->orderBy($orderby, $order_desc_asc == '1' ? 'asc' : 'desc');
    } else {
        // 기본: idx 역순
        $DB->orderBy("s.idx", "desc");
    }

    // ================================
    // ⑥ 페이징 + 조회
    // ================================
    $fields = "
        s.*,
        m.mt_id,
        m.mt_name,
        m.mt_hp,
        s.idx AS sh_idx
    ";

    $list   = $DB->arraybuilder()->paginate($tbl_shop_name . " s", $pg, $fields);
    $n_page = $DB->totalPages;
    $counts = $DB->totalCount;
    $counts = $counts - (($pg - 1) * $pageLimit);

    ?>
    <div class="table-responsive margin-top-20">
        <table class="table table-striped table-bordered margin-bottom-20"
               id="listTable"
               style="min-width: 800px">
            <thead class="thead-dark">
            <tr>
                <th class="text-center" style="width:80px;">번호</th>
                <th class="text-center" style="width:160px;">매장명</th>
                <th class="text-center">지역/주소</th>
                <th class="text-center" style="width:120px;">대표자명</th>
                <th class="text-center" style="width:140px;">연락처</th>
                <th class="text-center" style="width:90px;">오픈여부</th>
                <th class="text-center" style="width:140px;">등록일</th>
                <th class="text-center" style="width:130px;">관리</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($list) {
                foreach ($list as $row) { ?>
                    <tr draggable="true" data-id="<?=$row['sh_idx']?>">
                        <!-- 번호 -->
                        <td data-title="번호" class="text-center">
                            <?=$counts?>
                        </td>

                        <!-- 매장명 -->
                        <td class="text-center">
                            <span class="line1_text"><?=$row['sh_title']?></span>
                        </td>

                        <!-- 지역/주소 -->
                        <td class="text-center">
                            <span class="line1_text">
                                <?php
                                $zip   = $row['sh_zip']   ?? '';
                                $addr1 = $row['sh_addr1'] ?? '';
                                $addr2 = $row['sh_addr2'] ?? '';
                                echo trim($zip . ' ' . $addr1 . ' ' . $addr2);
                                ?>
                            </span>
                        </td>

                        <!-- 대표자명 (매장 대표자명 → 없으면 가맹점주 이름) -->
                        <td class="text-center">
                            <span class="line1_text">
                                <?= $row['sh_ceo_nm'] ?: $row['mt_name'] ?>
                            </span>
                        </td>

                        <!-- 연락처 (가맹점주 휴대폰) -->
                        <td class="text-center">
                            <span class="line1_text"><?=format_phone($row['mt_hp'])?></span>
                        </td>

                        <!-- 오픈여부 (현재는 del_date 기준으로 단순 표기) -->
                        <td class="text-center">
                            <span class="line1_text">
                                <?php
                                // del_date IS NULL로만 가져오므로 사실상 전부 "오픈"
                                echo $row['sh_show'] === 'Y' ? '오픈' : '미오픈';
                                ?>
                            </span>
                        </td>

                        <!-- 등록일 -->
                        <td class="text-center">
                            <span class="line1_text">
                                <?php
                                echo isset($row['sh_wdate']) ? DateType($row['sh_wdate'], 4) : '';
                                ?>
                            </span>
                        </td>

                        <!-- 관리 -->
                        <td data-title="관리" class="text-center">
                            <!-- 이 매장의 상세(메뉴/주문/취소)는 list2.php에서 처리 -->
                            <input type="button"
                                   class="btn btn-outline-info"
                                   value="상세"
                                   onclick="location.href='./list2.php?sh_idx=<?=$row['sh_idx']?>'" />
<!--                            <input type="button"-->
<!--                                   class="btn btn-outline-danger"-->
<!--                                   value="삭제"-->
<!--                                   onclick="f_post_del('./update.php', '--><?php //=$row['sh_idx']?><!--');" /> -->
                        </td>
                    </tr>
                    <?php
                    $counts--;
                }
            } else { ?>
                <tr>
                    <td colspan="8" class="text-center"><b>자료가 없습니다.</b></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>

    <?php
    if ($n_page > 1) {
        echo page_listing_xhr($pg, $n_page, 'f_get_box_mng_list');
    }
    exit;
}

/**
 * 2) 매장 삭제
 * - act = delete
 * - idx = shop_t.idx (sh_idx)
 */
else if ($act === 'delete') {

    header('Content-Type: application/json; charset=utf-8');

    $idx = (int)($_POST['idx'] ?? 0);
    if ($idx <= 0) {
        json_response(false, '잘못된 접근입니다.(idx)');
    }

    try {
        $DB->startTransaction();

        // 해당 매장 존재 여부 확인
        $DB->where('idx', $idx);
        $row = $DB->getOne($tbl_shop_name);

        if (!$row) {
            throw new Exception('삭제할 매장 정보가 존재하지 않습니다.');
        }

        // 실제 삭제
        // (필요하면 여기서 soft delete로 변경: del_date = now())
        $DB->where('idx', $idx);
        if (!$DB->delete($tbl_shop_name)) {
            throw new Exception('매장 삭제 처리에 실패했습니다.');
        }

        $DB->commit();
        json_response(true, '삭제되었습니다.', [
            'redirect' => './list.php',
        ]);

    } catch (Exception $e) {
        $DB->rollback();
        json_response(false, $e->getMessage());
    }
}

/**
 * 3) 메뉴관리 리스트 (Ajax)
 * - act = list_menu
 * - 대상 매장: sh_idx
 */
else if ($act === 'list_menu') {

    unset($list);

    $pageLimit = (int)($_POST['obj_limit_num'] ?? 10);
    $DB->pageLimit = $pageLimit;
    $pg            = (int)($_POST['obj_pg'] ?? 1);

    $sh_idx          = (int)($_POST['sh_idx'] ?? 0);          // 매장 키
    $search_category = $_POST['search_category'] ?? 'all';    // 카테고리
    $search_txt      = $_POST['search_txt']      ?? '';       // 메뉴명
    $search_show     = $_POST['search_show']     ?? 'all';    // 노출상태 (Y/N/all)
    $search_sale     = $_POST['search_sale']     ?? 'all';    // 판매상태 (Y/N/all)

    if ($sh_idx <= 0) {
        echo '<div class="alert alert-danger">매장 정보가 올바르지 않습니다.</div>';
        exit;
    }

    // JOIN 구조
    // sm: shop_menu_t
    // sc: shop_category_t
    // sh: shop_t
    $DB->join($tbl_shop_category . " sc", "sm.sc_idx = sc.idx", "LEFT");
    $DB->join($tbl_shop_name   . " sh", "sc.sh_idx = sh.idx", "LEFT");

    // 대상 매장 고정
    $DB->where('sc.sh_idx', $sh_idx);

    // 카테고리 필터
    if ($search_category !== '' && $search_category !== 'all') {
        $DB->where('sm.sc_idx', (int)$search_category);
    }

    // 노출상태 필터 (sm_show)
    if ($search_show !== '' && $search_show !== 'all') {
        $DB->where('sm.sm_show', $search_show);
    }

    // 판매상태 필터 (sm_type)
    if ($search_sale !== '' && $search_sale !== 'all') {
        $DB->where('sm.sm_type', $search_sale);
    }

    // 메뉴명 검색
    if ($search_txt !== '') {
        $DB->where("instr(sm.sm_title, '{$search_txt}')");
    }

    // 정렬
    $orderby        = $_POST['obj_orderby']        ?? '';
    $order_desc_asc = $_POST['obj_order_desc_asc'] ?? '1';

    if ($orderby) {
        $DB->orderBy($orderby, $order_desc_asc == '1' ? 'asc' : 'desc');
    } else {
        // 기본: 카테고리 순서 → 메뉴 순서 → 메뉴 idx 역순
        $DB->orderBy('sc.sc_order', 'asc');
        $DB->orderBy('sm.sm_order', 'asc');
        $DB->orderBy('sm.idx',      'desc');
    }

    // 조회 필드
    $fields = "
        sm.*,
        sc.sc_title,
        sh.sh_title  AS shop_title,
        sm.idx       AS sm_idx
    ";

    $list   = $DB->arraybuilder()->paginate($tbl_shop_menu . " sm", $pg, $fields);
    $n_page = $DB->totalPages;
    $counts = $DB->totalCount;
    $counts = $counts - (($pg - 1) * $pageLimit);
    ?>
    <div class="table-responsive margin-top-20">
        <table class="table table-striped table-bordered margin-bottom-20"
               id="listTable"
               style="min-width: 900px">
            <thead class="thead-dark">
            <tr>
                <th class="text-center" style="width:70px;">번호</th>
                <th class="text-center" style="width:150px;">카테고리</th>
                <th class="text-center" style="width:160px;">메뉴명</th>
                <th class="text-center" style="width:120px;">기본 금액</th>
                <th class="text-center" style="width:100px;">판매상태</th>
                <th class="text-center" style="width:100px;">노출상태</th>
                <th class="text-center" style="width:140px;">등록일</th>
                <th class="text-center" style="width:70px;">메뉴 정보</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if ($list) {

                foreach ($list as $row) {

                    // 판매상태 표시 (sm_type: 'Y','N' / 주석은 "판매 중지")
                    $saleText = ($row['sm_type'] === 'Y') ? '판매중지' : '판매중';

                    // 노출상태 표시
                    $showText = ($row['sm_show'] === 'Y') ? '노출' : '미노출';

                    // 기본 금액
                    $priceText = is_null($row['sm_price']) ? '-' : number_format($row['sm_price']) . ' 원';
                    ?>
                    <tr draggable="true" data-id="<?=$row['idx']?>">
                        <!-- 번호 -->
                        <td class="text-center"><?=$counts?></td>

                        <!-- 카테고리 -->
                        <td class="text-center">
                            <?=htmlspecialchars($row['sc_title'] ?? '-', ENT_QUOTES)?>
                        </td>

                        <!-- 메뉴명 -->
                        <td class="text-center">
                            <?=htmlspecialchars($row['sm_title'] ?? '-', ENT_QUOTES)?>
                        </td>

                        <!-- 기본 금액 -->
                        <td class="text-center">
                            <?=$priceText?>
                        </td>

                        <!-- 판매상태 -->
                        <td class="text-center">
                            <?=$saleText?>
                        </td>

                        <!-- 노출상태 -->
                        <td class="text-center">
                            <?=$showText?>
                        </td>

                        <!-- 등록일 -->
                        <td class="text-center">
                            <?= isset($row['sm_wdate']) ? DateType($row['sm_wdate'], 4) : '' ?>
                        </td>

                        <!-- 메뉴 정보 (버튼들) -->
                        <td class="text-center">
                            <!-- 메뉴 상세 모달 버튼 -->
                            <button type="button"
                                    class="btn btn-outline-info btn-menu-detail"
                                    data-idx="<?=$row['idx']?>">
                                메뉴 상세
                            </button>
                        </td>
                    </tr>
                    <?php
                    $counts--;
                }

            } else { ?>
                <tr>
                    <td colspan="8" class="text-center"><b>자료가 없습니다.</b></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <?php
    if ($n_page > 1) {
        echo page_listing_xhr($pg, $n_page, 'f_get_box_mng_list', 'menu');
    }
    exit;
}

// =========================================================
// 메뉴 상세 정보 (모달용) - ACT: menu_detail
// =========================================================
else if ($act === 'menu_detail') {

    header('Content-Type: text/html; charset=utf-8');

    $sm_idx = (int)($_POST['sm_idx'] ?? 0);
    if ($sm_idx <= 0) {
        echo '<div class="alert alert-danger">잘못된 접근입니다.(메뉴 키)</div>';
        exit;
    }

    // 1) 메뉴 기본 정보 + 카테고리 + 매장
    $DB->join($tbl_shop_category . " sc", "sm.sc_idx = sc.idx", "LEFT");
    $DB->join($tbl_shop_name . " s", "sc.sh_idx = s.idx", "LEFT");

    $DB->where('sm.idx', $sm_idx);
    $menu = $DB->getOne($tbl_shop_menu . " sm", "
        sm.*,
        sc.sc_title,
        s.sh_title
    ");

    if (!$menu) {
        echo '<div class="alert alert-danger">메뉴 정보를 찾을 수 없습니다.</div>';
        exit;
    }

    // 2) 옵션 카테고리 목록
    $DB->where('sm_idx', $sm_idx);
    $DB->orderBy('oc_order', 'asc');
    $optionCategories = $DB->get($tbl_moc);

    // 3) 각 옵션 카테고리별 옵션 목록 묶기
    $optionsByCategory = [];
    if ($optionCategories) {
        foreach ($optionCategories as $oc) {
            $oc_idx = (int)$oc['idx'];

            $DB->where('oc_idx', $oc_idx);
            $DB->orderBy('om_order', 'asc');
            $optList = $DB->get($tbl_option_menu);

            $optionsByCategory[$oc_idx] = $optList ?: [];
        }
    }

    // 4) 표시용 텍스트들 준비
    $saleText = ($menu['sm_type'] === 'N') ? '판매중지' : '판매중';
    $basePriceText = is_null($menu['sm_price']) ? '-' : number_format($menu['sm_price']) . ' 원';

    // 이미지 URL (프로젝트 상황에 맞게 수정 가능)
    $imgTag = '';
    if (!empty($menu['sm_img'])) {
        // sm_img에 전체 경로나 상대 경로가 들어있다고 가정
        $imgSrc = htmlspecialchars($menu['sm_img'], ENT_QUOTES);
        $imgTag = '<img src="' . $imgSrc . '" alt="메뉴 이미지" class="img-fluid rounded border" style="max-height:200px;">';
    }

    ?>
    <div class="menu-detail-modal">

        <!-- 상단: 메뉴 기본 정보 -->
        <div class="mb-3">
            <h5 class="mb-1">
                [<?=htmlspecialchars($menu['sc_title'] ?? '카테고리 없음', ENT_QUOTES)?>]
                <?=htmlspecialchars($menu['sm_title'] ?? '-', ENT_QUOTES)?> <?=$menu['sm_age_show'] === 'Y' ? '(19세이상)' : ''?>
            </h5>
            <p class="text-muted mb-1">
                매장명:
                <strong><?=htmlspecialchars($menu['sh_title'] ?? '-', ENT_QUOTES)?></strong>
            </p>
            <p class="mb-1">
                기본 금액:
                <strong><?=$basePriceText?></strong>
            </p>
            <p class="mb-1">
                판매상태:
                <span class="badge badge-<?=($menu['sm_type'] === 'N') ? 'danger' : 'success'?>">
                    <?=$saleText?>
                </span>
            </p>
            <?php if (!empty($menu['sm_contents'])) { ?>
                <p class="mt-2 mb-0">
                    <?=nl2br(htmlspecialchars($menu['sm_contents'], ENT_QUOTES))?>
                </p>
            <?php } ?>
        </div>

        <!-- 메뉴 이미지 -->
        <?php if ($imgTag) { ?>
            <div class="mb-3 text-center">
                <?=$imgTag?>
            </div>
        <?php } ?>

        <!-- 옵션 카테고리 / 옵션 -->
        <div class="mt-3">
            <h6 class="mb-2">옵션 정보</h6>
            <?php
            if (!$optionCategories) {
                echo '<p class="text-muted mb-0">등록된 옵션이 없습니다.</p>';
            } else {
                foreach ($optionCategories as $oc) {

                    $oc_idx   = (int)$oc['idx'];
                    $oc_title = $oc['oc_title'] ?? '';
                    $oc_check = ($oc['oc_check'] === 'Y') ? '필수' : '선택';
                    $oc_show  = ($oc['oc_show'] === 'Y') ? '노출' : '미노출';

                    $optList  = $optionsByCategory[$oc_idx] ?? [];
                    ?>
                    <div class="border rounded p-2 mb-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div>
                                <strong><?=htmlspecialchars($oc_title, ENT_QUOTES)?></strong>
                                <span class="text-muted small">
                                    (<?=$oc_check?> / <?=$oc_show?>)
                                </span>
                            </div>
                        </div>

                        <?php if (!$optList) { ?>
                            <p class="text-muted small mb-0">옵션이 없습니다.</p>
                        <?php } else { ?>
                            <table class="table table-sm mb-0">
                                <thead>
                                <tr>
                                    <th style="width:60%;">옵션 명</th>
                                    <th style="width:20%;">가격</th>
                                    <th style="width:20%;">노출</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($optList as $opt) {

                                    $optTitle = $opt['om_title'] ?? '';
                                    $optPrice = is_null($opt['om_price'])
                                        ? '-'
                                        : number_format($opt['om_price']) . ' 원';
                                    $optShow  = ($opt['om_show'] === 'Y') ? '노출' : '미노출';
                                    ?>
                                    <tr>
                                        <td><?=htmlspecialchars($optTitle, ENT_QUOTES)?></td>
                                        <td><?=$optPrice?></td>
                                        <td><?=$optShow?></td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        <?php } ?>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
    </div>
    <?php
    exit;
}

// =========================================================
// 주문내역 리스트 (ACT: list_order)
// =========================================================
else if ($act === "list_order") {

    unset($list);

    $pageLimit = (int)($_POST['obj_limit_num'] ?? 10);
    $DB->pageLimit = $pageLimit;
    $pg            = (int)($_POST['obj_pg'] ?? 1);

    $sh_idx           = (int)($_POST['sh_idx'] ?? 0);               // 매장 키
    $search_day       = $_POST['search_day']       ?? '';           // 1/7/30
    $sdate            = $_POST['sdate']            ?? '';           // 직접 시작일
    $edate            = $_POST['edate']            ?? '';           // 직접 종료일
    $sel_search       = $_POST['sel_search']       ?? 'all';        // all/order_code/user_name
    $search_txt       = $_POST['search_txt']       ?? '';           // 검색어
    $search_order_type= $_POST['search_order_type']?? 'all';        // all/takeout/reserve/table

    if ($sh_idx <= 0) {
        echo '<div class="alert alert-danger">매장 정보가 올바르지 않습니다.</div>';
        exit;
    }

    // 주문(o) + 회원(m)
    $DB->join($tbl_member_name . " m", "o.mt_idx = m.idx", "LEFT");

    // 매장 필터
    $DB->where('o.sh_idx', $sh_idx);

    // 주문 유형(포장/예약/테이블)
    if ($search_order_type !== '' && $search_order_type !== 'all') {
        if ($search_order_type === 'takeout') {
            // 포장: 예약X + 테이블X
            $DB->where("( (o.rv_idx IS NULL OR o.rv_idx = 0) AND (o.ot_table IS NULL OR o.ot_table = '') )");
        } elseif ($search_order_type === 'reserve') {
            // 예약: 예약키 존재
            $DB->where("( o.rv_idx IS NOT NULL AND o.rv_idx <> 0 )");
        } elseif ($search_order_type === 'table') {
            // 테이블(QR): 테이블 번호 존재
            $DB->where("( o.ot_table IS NOT NULL AND o.ot_table <> '' )");
        }
    }

    // 검색어 (주문번호 / 주문자명)
    if ($search_txt !== '') {
        if ($sel_search === 'order_code') {
            $DB->where("instr(o.ot_number, '{$search_txt}')");
        } elseif ($sel_search === 'user_name') {
            $DB->where("instr(m.mt_name, '{$search_txt}')");
        } else {
            // 통합검색: 주문번호 + 주문자명
            $DB->where("(
                instr(o.ot_number, '{$search_txt}')
                OR instr(m.mt_name, '{$search_txt}')
            )");
        }
    }

    // 결제일 기준 컬럼:
    // 선결제(PREPAID) → ot_pay_date
    // 후결제(POSTPAID) → ot_pay_date 없으면 ot_wdate 사용
    $dateExpr = "CASE WHEN o.ot_pay_type = 'PREPAID' THEN o.ot_pay_date ELSE IFNULL(o.ot_pay_date, o.ot_wdate) END";

    // 빠른 선택: 오늘 / 7일 / 30일
    if ($search_day !== '') {
        $today = date('Y-m-d');
        if ($search_day == '1') {
            $start_date = $today;
        } elseif ($search_day == '7') {
            $start_date = date('Y-m-d', strtotime('-7 days'));
        } elseif ($search_day == '30') {
            $start_date = date('Y-m-d', strtotime('-30 days'));
        }

        if (!empty($start_date)) {
            $DB->where($dateExpr, $start_date . ' 00:00:00', '>=');
        }
    }

    // 직접 날짜 선택 (결제일 범위)
    if ($sdate && $edate) {
        $start = $sdate . ' 00:00:00';
        $end   = $edate . ' 23:59:59';
        $DB->where($dateExpr, [$start, $end], 'BETWEEN');
    }

    // 정렬 (최근 주문 순)
    $orderby        = $_POST['obj_orderby']        ?? '';
    $order_desc_asc = $_POST['obj_order_desc_asc'] ?? '1';

    if ($orderby) {
        $DB->orderBy($orderby, $order_desc_asc == '1' ? 'asc' : 'desc');
    } else {
        $DB->orderBy('o.ot_wdate', 'desc');
        $DB->orderBy('o.idx',      'desc');
    }

    // 조회 필드
    $fields = "
        o.*,
        m.mt_name,
        o.idx AS ot_idx
    ";

    $list   = $DB->arraybuilder()->paginate($tbl_order_name . " o", $pg, $fields);
    $n_page = $DB->totalPages;
    $counts = $DB->totalCount;
    $counts = $counts - (($pg - 1) * $pageLimit);
    ?>
    <div class="table-responsive margin-top-20">
        <table class="table table-striped table-bordered margin-bottom-20" id="listTable" style="min-width: 900px">
            <thead class="thead-dark">
            <tr>
                <th class="text-center" style="width:70px;">번호</th>
                <th class="text-center" style="width:160px;">결제일시</th>
                <th class="text-center" style="width:100px;">주문상태</th>
                <th class="text-center" style="width:150px;">주문번호</th>
                <th class="text-center" style="width:120px;">주문자</th>
                <th class="text-center" style="width:140px;">총 결제 금액</th>
                <th class="text-center" style="width:70px;">주문 정보</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if ($list) {
                // 상태 텍스트 매핑
                $statusMap = [
                    'PENDING'   => '대기',
                    'CONFIRMED' => '확정',
                    'PREPARING' => '준비중',
                    'COMPLETED' => '완료',
                    'CANCELLED' => '취소',
                ];

                foreach ($list as $row) {

                    // 결제일시 표시 (선결제/후결제 고려)
                    $payDate = null;
                    if ($row['ot_pay_type'] === 'PREPAID') {
                        $payDate = $row['ot_pay_date'] ?: $row['ot_wdate'];
                    } else {
                        // 후결제: 결제일 있으면 사용, 없으면 주문일
                        $payDate = $row['ot_pay_date'] ?: $row['ot_wdate'];
                    }

                    // 주문상태 텍스트
                    $otStatusText = $statusMap[$row['ot_status']] ?? $row['ot_status'];

                    // 주문자
                    $orderUser = '비회원';
                    if (!empty($row['mt_idx']) && $row['mt_idx'] > 0 && !empty($row['mt_name'])) {
                        $orderUser = $row['mt_name'];
                    }

                    // 총 결제 금액 = 총 금액 - 할인금액
                    $totalPrice = (float)$row['ot_total_price'] - (float)$row['ot_discount_amount'];
                    if ($totalPrice < 0) $totalPrice = 0;
                    ?>
                    <tr data-id="<?=$row['ot_idx']?>">
                        <!-- 번호 -->
                        <td class="text-center"><?=$counts?></td>

                        <!-- 결제일시 -->
                        <td class="text-center">
                            <?= $payDate ? DateType($payDate, 4) : '-' ?>
                        </td>

                        <!-- 주문상태 -->
                        <td class="text-center">
                            <?=$otStatusText?>
                        </td>

                        <!-- 주문번호 -->
                        <td class="text-center">
                            <?=htmlspecialchars($row['ot_number'], ENT_QUOTES)?>
                        </td>

                        <!-- 주문자 -->
                        <td class="text-center">
                            <?=$orderUser?>
                        </td>

                        <!-- 총 결제 금액 -->
                        <td class="text-center">
                            <?=number_format($totalPrice)?> 원
                        </td>

                        <!-- 주문정보 버튼 -->
                        <td class="text-center">
                            <button type="button"
                                    class="btn btn-outline-info btn-order-info"
                                    data-idx="<?=$row['ot_idx']?>">
                                주문 정보
                            </button>
                        </td>
                    </tr>
                    <?php
                    $counts--;
                }
            } else { ?>
                <tr>
                    <td colspan="7" class="text-center"><b>자료가 없습니다.</b></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <?php
    if ($n_page > 1) {
        echo page_listing_xhr($pg, $n_page, 'f_get_box_mng_list', 'order');
    }
    exit;
}

// =========================================================
// 주문 상세 (ACT: order_detail) - 모달 내용
// =========================================================
else if ($act === 'order_detail') {

    $idx = (int)($_POST['idx'] ?? 0);
    if ($idx <= 0) {
        echo '<div class="alert alert-danger">잘못된 접근입니다.(idx)</div>';
        exit;
    }

    // 주문 + 회원
    $DB->join($tbl_member_name . " m", "o.mt_idx = m.idx", "LEFT");
    $DB->where('o.idx', $idx);
    $row = $DB->getOne($tbl_order_name . " o");

    if (!$row) {
        echo '<div class="alert alert-danger">주문 정보를 찾을 수 없습니다.</div>';
        exit;
    }

    // 주문자
    $orderUser = '비회원';
    if (!empty($row['mt_idx']) && $row['mt_idx'] > 0 && !empty($row['mt_name'])) {
        $orderUser = $row['mt_name'];
    }

    // 결제 방식 / 결제 상태 텍스트
    $payTypeMap = [
        'PREPAID'  => '선결제',
        'POSTPAID' => '후결제',
    ];
    $payStatusMap = [
        'UNPAID' => '미결제',
        'PAID'   => '결제완료',
        'REFUND' => '환불',
    ];

    $payTypeText   = $payTypeMap[$row['ot_pay_type']]   ?? $row['ot_pay_type'];
    $payStatusText = $payStatusMap[$row['ot_pay_status']] ?? $row['ot_pay_status'];

    // 결제일시
    $payDate = null;
    if ($row['ot_pay_type'] === 'PREPAID') {
        $payDate = $row['ot_pay_date'] ?: $row['ot_wdate'];
    } else {
        $payDate = $row['ot_pay_date'] ?: $row['ot_wdate'];
    }

    // 총 결제 금액
    $totalPrice = (float)$row['ot_total_price'] - (float)$row['ot_discount_amount'];
    if ($totalPrice < 0) $totalPrice = 0;

    // ct_snapshot 파싱
    $snapshot = [];
    if (!empty($row['ct_snapshot'])) {
        $snapshot = json_decode($row['ct_snapshot'], true);
    }
    $items   = $snapshot['items']   ?? [];
    $summary = $snapshot['summary'] ?? [];
    ?>
    <div class="order-detail-wrapper">
        <!-- 기본 정보 -->
        <h6 class="mb-2">기본 정보</h6>
        <table class="table table-sm table-bordered">
            <tbody>
            <tr>
                <th style="width:120px;">주문번호</th>
                <td><?=htmlspecialchars($row['ot_number'], ENT_QUOTES)?></td>
                <th style="width:120px;">주문자</th>
                <td><?=$orderUser?></td>
            </tr>
            <tr>
                <th>결제방식</th>
                <td><?=$payTypeText?></td>
                <th>결제상태</th>
                <td><?=$payStatusText?></td>
            </tr>
            <tr>
                <th>주문상태</th>
                <td colspan="3"><?=$row['ot_status']?></td>
            </tr>
            <tr>
                <th>결제일시</th>
                <td colspan="3">
                    <?= $payDate ? DateType($payDate, 4) : '-' ?>
                </td>
            </tr>
            <tr>
                <th>총 결제 금액</th>
                <td><?=number_format($totalPrice)?> 원</td>
                <th>쿠폰 할인</th>
                <td><?=number_format((float)$row['ot_discount_amount'])?> 원</td>
            </tr>
            <tr>
                <th>요청사항</th>
                <td colspan="3"><?=nl2br(htmlspecialchars($row['ot_notes'] ?? '', ENT_QUOTES))?></td>
            </tr>
            </tbody>
        </table>

        <!-- 메뉴 정보 -->
        <h6 class="mt-3 mb-2">주문 메뉴</h6>
        <table class="table table-sm table-bordered">
            <thead class="thead-light">
            <tr>
                <th style="width:200px;">메뉴명</th>
                <th style="width:80px;" class="text-center">수량</th>
                <th style="width:120px;" class="text-right">단가</th>
                <th style="width:120px;" class="text-right">금액</th>
                <th>옵션</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($items) {
                foreach ($items as $it) {
                    $menuName   = $it['menu_name']   ?? '';
                    $qty        = $it['quantity']    ?? 0;
                    $unitPrice  = $it['unit_price']  ?? 0;
                    $totalItem  = $it['total_price'] ?? ($qty * $unitPrice);
                    $options    = $it['options']     ?? [];
                    ?>
                    <tr>
                        <td><?=htmlspecialchars($menuName, ENT_QUOTES)?></td>
                        <td class="text-center"><?=$qty?></td>
                        <td class="text-right"><?=number_format((float)$unitPrice)?> 원</td>
                        <td class="text-right"><?=number_format((float)$totalItem)?> 원</td>
                        <td>
                            <?php
                            if ($options) {
                                foreach ($options as $op) {
                                    $opName   = $op['option_name']  ?? '';
                                    $opPrice  = $op['option_price'] ?? 0;
                                    $opQty    = $op['quantity']     ?? 1;
                                    echo '- ' . htmlspecialchars($opName, ENT_QUOTES)
                                        . ' (+' . number_format((float)$opPrice) . '원 x ' . (int)$opQty . '개)<br>';
                                }
                            } else {
                                echo '-';
                            }
                            ?>
                        </td>
                    </tr>
                    <?php
                }
            } else { ?>
                <tr>
                    <td colspan="5" class="text-center">메뉴 정보가 없습니다.</td>
                </tr>
            <?php } ?>
            </tbody>
        </table>

        <!-- 요약 정보 -->
        <h6 class="mt-3 mb-2">요약</h6>
        <table class="table table-sm table-bordered">
            <tbody>
            <tr>
                <th style="width:120px;">상품 합계</th>
                <td><?=number_format((float)($summary['sub_total'] ?? 0))?> 원</td>
            </tr>
            <tr>
                <th>할인 금액</th>
                <td><?=number_format((float)($summary['discount'] ?? 0))?> 원</td>
            </tr>
            <tr>
                <th>최종 결제 금액</th>
                <td><?=number_format((float)($summary['total'] ?? $totalPrice))?> 원</td>
            </tr>
            </tbody>
        </table>
    </div>
    <?php
    exit;
}

// =========================================================
// 취소내역 리스트 (ACT: list_cancel)
//  - orders_t 에서 취소된 데이터만
//  - 나머지 필터/출력 형식은 list_order 와 동일
// =========================================================
else if ($act === "list_cancel") {

    unset($list);

    $pageLimit = (int)($_POST['obj_limit_num'] ?? 10);
    $DB->pageLimit = $pageLimit;
    $pg            = (int)($_POST['obj_pg'] ?? 1);

    $sh_idx            = (int)($_POST['sh_idx'] ?? 0);               // 매장 키
    $search_day        = $_POST['search_day']        ?? '';          // 1/7/30
    $sdate             = $_POST['sdate']             ?? '';          // 직접 시작일
    $edate             = $_POST['edate']             ?? '';          // 직접 종료일
    $sel_search        = $_POST['sel_search']        ?? 'all';       // all/order_code/user_name
    $search_txt        = $_POST['search_txt']        ?? '';          // 검색어
    $search_order_type = $_POST['search_order_type'] ?? 'all';       // all/takeout/reserve/table

    if ($sh_idx <= 0) {
        echo '<div class="alert alert-danger">매장 정보가 올바르지 않습니다.</div>';
        exit;
    }

    // 주문(o) + 회원(m)
    $DB->join($tbl_member_name . " m", "o.mt_idx = m.idx", "LEFT");

    // 매장 필터
    $DB->where('o.sh_idx', $sh_idx);

    // ✅ 취소된 데이터만
    $DB->where('o.ot_status', 'CANCELLED');

    // 주문 유형(포장/예약/테이블) - 주문내역과 동일 로직
    if ($search_order_type !== '' && $search_order_type !== 'all') {
        if ($search_order_type === 'takeout') {
            // 포장: 예약X + 테이블X
            $DB->where("( (o.rv_idx IS NULL OR o.rv_idx = 0) AND (o.ot_table IS NULL OR o.ot_table = '') )");
        } elseif ($search_order_type === 'reserve') {
            // 예약: 예약키 존재
            $DB->where("( o.rv_idx IS NOT NULL AND o.rv_idx <> 0 )");
        } elseif ($search_order_type === 'table') {
            // 테이블(QR): 테이블 번호 존재
            $DB->where("( o.ot_table IS NOT NULL AND o.ot_table <> '' )");
        }
    }

    // 검색어 (주문번호 / 주문자명)
    if ($search_txt !== '') {
        if ($sel_search === 'order_code') {
            $DB->where("instr(o.ot_number, '{$search_txt}')");
        } elseif ($sel_search === 'user_name') {
            $DB->where("instr(m.mt_name, '{$search_txt}')");
        } else {
            // 통합검색: 주문번호 + 주문자명
            $DB->where("(
                instr(o.ot_number, '{$search_txt}')
                OR instr(m.mt_name, '{$search_txt}')
            )");
        }
    }

    // ✅ 취소일 기준 컬럼
    // ot_cancel 이 있으면 취소일, 없으면 주문일(ot_wdate) 사용
    $dateExpr = "IFNULL(o.ot_cancel, o.ot_wdate)";

    // 빠른 선택: 오늘 / 7일 / 30일
    if ($search_day !== '') {
        $today = date('Y-m-d');
        $start_date = '';

        if ($search_day == '1') {
            $start_date = $today;
        } elseif ($search_day == '7') {
            $start_date = date('Y-m-d', strtotime('-7 days'));
        } elseif ($search_day == '30') {
            $start_date = date('Y-m-d', strtotime('-30 days'));
        }

        if (!empty($start_date)) {
            $DB->where($dateExpr, $start_date . ' 00:00:00', '>=');
        }
    }

    // 직접 날짜 선택 (취소일 범위)
    if ($sdate && $edate) {
        $start = $sdate . ' 00:00:00';
        $end   = $edate . ' 23:59:59';
        $DB->where($dateExpr, [$start, $end], 'BETWEEN');
    }

    // 정렬 (최근 취소 순)
    $orderby        = $_POST['obj_orderby']        ?? '';
    $order_desc_asc = $_POST['obj_order_desc_asc'] ?? '1';

    if ($orderby) {
        $DB->orderBy($orderby, $order_desc_asc == '1' ? 'asc' : 'desc');
    } else {
        $DB->orderBy('o.ot_cancel', 'desc');
        $DB->orderBy('o.idx',       'desc');
    }

    // 조회 필드
    $fields = "
        o.*,
        m.mt_name,
        o.idx AS ot_idx
    ";

    $list   = $DB->arraybuilder()->paginate($tbl_order_name . " o", $pg, $fields);
    $n_page = $DB->totalPages;
    $counts = $DB->totalCount;
    $counts = $counts - (($pg - 1) * $pageLimit);
    ?>
    <div class="table-responsive margin-top-20">
        <table class="table table-striped table-bordered margin-bottom-20" id="listTable" style="min-width: 900px">
            <thead class="thead-dark">
            <tr>
                <th class="text-center" style="width:70px;">번호</th>
                <th class="text-center" style="width:160px;">취소일시</th>
                <th class="text-center" style="width:100px;">주문상태</th>
                <th class="text-center" style="width:150px;">주문번호</th>
                <th class="text-center" style="width:120px;">주문자</th>
                <th class="text-center" style="width:140px;">총 결제 금액</th>
                <th class="text-center" style="width:70px;">주문 정보</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if ($list) {
                $statusMap = [
                    'PENDING'   => '대기',
                    'CONFIRMED' => '확정',
                    'PREPARING' => '준비중',
                    'COMPLETED' => '완료',
                    'CANCELLED' => '취소',
                ];

                foreach ($list as $row) {

                    // 취소일시 표시
                    $cancelDate = $row['ot_cancel'] ?: $row['ot_wdate'];

                    // 주문상태 텍스트 (대부분 CANCELLED)
                    $otStatusText = $statusMap[$row['ot_status']] ?? $row['ot_status'];

                    // 주문자
                    $orderUser = '비회원';
                    if (!empty($row['mt_idx']) && $row['mt_idx'] > 0 && !empty($row['mt_name'])) {
                        $orderUser = $row['mt_name'];
                    }

                    // 총 결제 금액 = 총 금액 - 할인금액
                    $totalPrice = (float)$row['ot_total_price'] - (float)$row['ot_discount_amount'];
                    if ($totalPrice < 0) $totalPrice = 0;
                    ?>
                    <tr data-id="<?=$row['ot_idx']?>">
                        <!-- 번호 -->
                        <td class="text-center"><?=$counts?></td>

                        <!-- 취소일시 -->
                        <td class="text-center">
                            <?= $cancelDate ? DateType($cancelDate, 4) : '-' ?>
                        </td>

                        <!-- 주문상태 -->
                        <td class="text-center">
                            <?=$otStatusText?>
                        </td>

                        <!-- 주문번호 -->
                        <td class="text-center">
                            <?=htmlspecialchars($row['ot_number'], ENT_QUOTES)?>
                        </td>

                        <!-- 주문자 -->
                        <td class="text-center">
                            <?=$orderUser?>
                        </td>

                        <!-- 총 결제 금액 -->
                        <td class="text-center">
                            <?=number_format($totalPrice)?> 원
                        </td>

                        <!-- 주문정보 버튼 (주문내역과 동일 모달 사용) -->
                        <td class="text-center">
                            <button type="button"
                                    class="btn btn-outline-info btn-order-info"
                                    data-idx="<?=$row['ot_idx']?>">
                                주문 정보
                            </button>
                        </td>
                    </tr>
                    <?php
                    $counts--;
                }
            } else { ?>
                <tr>
                    <td colspan="7" class="text-center"><b>자료가 없습니다.</b></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <?php
    if ($n_page > 1) {
        echo page_listing_xhr($pg, $n_page, 'f_get_box_mng_list', 'cancel');
    }
    exit;
}

include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
