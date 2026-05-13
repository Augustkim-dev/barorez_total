<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";

if ($_POST['act'] == "list") {
    unset($list);
    $DB->pageLimit = $_POST['obj_limit_num'];
    $pg = $_POST['obj_pg'];

    //검색
    if ($_POST['search_txt']) {
        if ($_POST['sel_search'] == "all") {
            $DB->where('( instr(a1.nt_title, \''.$_POST['search_txt'].'\') or instr(a1.nt_content, \''.$_POST['search_txt'].'\') )');
        } else {
            $DB->where('( instr('.$_POST['sel_search'].', \''.$_POST['search_txt'].'\') )');
        }
    }

    if ($_POST['nt_show']) {
        $DB->where('a1.nt_show', $_POST['nt_show']);
    }

    //정렬
    if ($_POST['obj_order_desc_asc'] == '1') {
        $DB->orderBy("a1.legal_code", "asc");
    } else {
        $DB->orderBy("a1.legal_code", "asc");
    }

    $list = $DB->arraybuilder()->paginate("legal_dong_t a1", $pg, '*, legal_code as lt_idx');

    //페이징
    $n_page = $DB->totalPages;
    $counts = $DB->totalCount;
    $counts = $counts - (($pg - 1) * $n_limit_num);
    ?>
    <div class="table-responsive margin-top-20">
        <table class="table table-striped table-bordered margin-bottom-20" id="listTable" style="min-width: 800px">
            <thead class="thead-dark">
            <tr>
                <th class="text-center" style="width:20%;">
                    법정동코드
                </th>
                <th class="text-center" style="width:20%;">
                    시도명
                </th>
                <th class="text-center" style="width:20%;">
                    시군구명
                </th>
                <th class="text-center" style="width:20%;">
                    읍면동명
                </th>
                <th class="text-center" style="width:20%;">
                    리명
                </th>
            </tr>
            </thead>
            <tbody>
            <?php
            if ($list) {
                foreach ($list as $row) {
                    ?>
                    <tr draggable="true" data-id="<?=$row['idx']?>">
                        <td data-title="법정동코드" class="text-center">
                            <?=$row['legal_code']?>
                        </td>
                        <td data-title="시도명" class="text-center">
                            <?=$row['sido_name']?>
                        </td>
                        <td data-title="시군구명" class="text-center">
                            <?=$row['sigungu_name']?>
                        </td>
                        <td data-title="읍면동명" class="text-center">
                            <?=$row['dong_name']?>
                        </td>
                        <td data-title="리명" class="text-center">
                            <?=$row['ri_name']?>
                        </td>
                    </tr>
                    <?php
                    $counts--;
                }
            } else {
                ?>
                <tr>
                    <td colspan="7" class="text-center"><b>자료가 없습니다.</b></td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </div>

    <?php
    if($n_page > 1) {
        echo page_listing_xhr($pg, $n_page, 'f_get_box_mng_list');
    }

}

include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
