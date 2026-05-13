<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";

if ($_POST['act'] == "list") {
    unset($list);
    $DB->pageLimit = $_POST['obj_limit_num'];
    $pg = $_POST['obj_pg'];

    /**
     * 🔍 검색 조건 시작
     */
    // 시/도
    if (!empty($_POST['province'])) {
        $DB->where("SUBSTRING_INDEX(a1.addr_road, ' ', 1)", $_POST['province']);
    }

    // 시/군/구
    if (!empty($_POST['city'])) {
        $DB->where("SUBSTRING_INDEX(SUBSTRING_INDEX(a1.addr_road, ' ', 2), ' ', -1)", $_POST['city']);
    }

    // 읍/면/동
    if (!empty($_POST['district'])) {
        $DB->where("SUBSTRING_INDEX(SUBSTRING_INDEX(a1.addr_road, ' ', 3), ' ', -1)", $_POST['district']);
    }

    // 주소 타입에 따른 주소 검색
    if (!empty($_POST['addr_type'])) {
        if ($_POST['addr_type'] === 'road' && !empty($_POST['addr_road'])) {
            $DB->where('a1.addr_road', '%' . $_POST['addr_road'] . '%', 'LIKE');
        }
        elseif ($_POST['addr_type'] === 'jibeon' && !empty($_POST['addr_jibeon'])) {
            $DB->where('a1.addr_jibeon', '%' . $_POST['addr_jibeon'] . '%', 'LIKE');
        }
    }

    // 사업장명
    if (!empty($_POST['business_name'])) {
        $DB->where("a1.business_name", '%' . $_POST['business_name'] . '%', 'LIKE');
    }

    // 건물용도
    if (!empty($_POST['building_use'])) {
        $DB->where("a1.building_use", $_POST['building_use']);
    }

    // 용도지구
    if (!empty($_POST['zoning_district'])) {
        $DB->where('zoning_district', $_POST['zoning_district'], 'IN');
    }

    // 인허가일자 기간
    if (!empty($_POST['date_start']) && !empty($_POST['date_end'])) {
        $start = $_POST['date_start'] . ' 00:00:00';
        $end   = $_POST['date_end'] . ' 23:59:59';
        $DB->where('a1.license_date', [$start, $end], 'BETWEEN');
    }

    // 객실수
    if ($_POST['room_min'] !== '' && $_POST['room_max'] !== '') {
        $DB->where('a1.room_count', [$_POST['room_min'], $_POST['room_max']], 'BETWEEN');
    } elseif ($_POST['room_min'] !== '') {
        $DB->where('a1.room_count', $_POST['room_min'], '>=');
    } elseif ($_POST['room_max'] !== '') {
        $DB->where('a1.room_count', $_POST['room_max'], '<=');
    }

    // 시설면적
    if ($_POST['area_min'] !== '' && $_POST['area_max'] !== '') {
        $DB->where('a1.facility_area', [$_POST['area_min'], $_POST['area_max']], 'BETWEEN');
    } elseif ($_POST['area_min'] !== '') {
        $DB->where('a1.facility_area', $_POST['area_min'], '>=');
    } elseif ($_POST['area_max'] !== '') {
        $DB->where('a1.facility_area', $_POST['area_max'], '<=');
    }

    // 지상층수
    if ($_POST['floor_above_min'] !== '' && $_POST['floor_above_max'] !== '') {
        $DB->where('a1.floor_above', [$_POST['floor_above_min'], $_POST['floor_above_max']], 'BETWEEN');
    } elseif ($_POST['floor_above_min'] !== '') {
        $DB->where('a1.floor_above', $_POST['floor_above_min'], '>=');
    } elseif ($_POST['floor_above_max'] !== '') {
        $DB->where('a1.floor_above', $_POST['floor_above_max'], '<=');
    }

    // 지하층수
    if ($_POST['floor_below_min'] !== '' && $_POST['floor_below_max'] !== '') {
        $DB->where('a1.floor_below', [$_POST['floor_below_min'], $_POST['floor_below_max']], 'BETWEEN');
    } elseif ($_POST['floor_below_min'] !== '') {
        $DB->where('a1.floor_below', $_POST['floor_below_min'], '>=');
    } elseif ($_POST['floor_below_max'] !== '') {
        $DB->where('a1.floor_below', $_POST['floor_below_max'], '<=');
    }

    /**
     * 🔍 검색 조건 끝
     */

    $list = $DB->arraybuilder()->paginate("guesthouse_t a1", $pg, '*, a1.id as nt_idx');

    $n_page = $DB->totalPages;
    $counts = $DB->totalCount;
    $counts = $counts - (($pg - 1) * $_POST['obj_limit_num']);
    ?>
    <div class="table-responsive margin-top-20">
        <table class="table table-striped table-bordered margin-bottom-20" id="listTable" style="min-width: 800px">
            <thead class="thead-dark">
            <tr>
                <th class="text-center">번호</th>
                <th class="text-center">관리</th>
                <th class="text-center">사업장명</th>
                <th class="text-center">주소</th>
                <th class="text-center">건물용도</th>
                <th class="text-center">용도지구</th>
                <th class="text-center">총층수</th>
                <th class="text-center">지상층수</th>
                <th class="text-center">지하층수</th>
                <th class="text-center">객실수</th>
                <th class="text-center">시설면적</th>
                <th class="text-center">인허가일자</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if ($list) {
                foreach ($list as $row) { ?>
                    <tr data-id="<?=$row['nt_idx']?>">
                        <td class="text-center"><?=$counts?></td>
                        <td class="text-center">
                            <input type="button" class="btn btn-outline-info btn-sm" value="상세" onclick="location.href='./form.php?act=update&id=<?=$row['nt_idx']?>'" />
                        </td>
                        <td class="text-center"><?=$row['business_name'] ?? '-'?></td>
                        <td class="text-center"><?=$row['addr_road'] ?? '-'?></td>
                        <td class="text-center"><?=$row['building_use'] ?? '-'?></td>
                        <td class="text-center"><?=$row['zoning_district'] ?? '-'?></td>
                        <td class="text-center"><?=$row['total_floors'] ?? 0?></td>
                        <td class="text-center"><?=$row['floor_above'] ?? 0?></td>
                        <td class="text-center"><?=$row['floor_below'] ?? 0?></td>
                        <td class="text-center"><?=$row['room_count'] ?? 0?></td>
                        <td class="text-center"><?=$row['facility_area'] ? $row['facility_area'].'㎡' : '-'?></td>
                        <td class="text-center"><?=$row['license_date'] ?? '-'?></td>
                    </tr>
                    <?php $counts--; }
            } else { ?>
                <tr>
                    <td colspan="12" class="text-center"><b>자료가 없습니다.</b></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <?php
    if ($n_page > 1) {
        echo page_listing_xhr($pg, $n_page, 'f_get_box_mng_list');
    }
}
include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
