<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";

if ($_POST['act'] == "input") {
    if($_POST['ct_name'] == "") {
        p_alert("잘못된 접근입니다. ct_name", 'back');
    }
    if($_POST['ct_show'] == "") {
        p_alert("잘못된 접근입니다. ct_name", 'back');
    }

    unset($arr_query);
    $arr_query = array(
        "ct_name" => clean_xss_tags($_POST['ct_name']),
        "ct_sub_name" => clean_xss_tags($_POST['ct_sub_name']),
        "ct_level" => 0,
        "ct_rank" => 1,
        "ct_pid" => 0,
        "ct_show" => $_POST['ct_show'],
        "ct_datetime" => $DB->now(),
    );

    $_last_idx = $DB->insert('board_category_t', $arr_query);

    p_alert("등록되었습니다.", "./blog_category.php");
} elseif ($_POST['act'] == "update") {
    if($_POST['ct_name'] == "") {
        p_alert("잘못된 접근입니다. ct_name", 'back');
    }
    if($_POST['ct_show'] == "") {
        p_alert("잘못된 접근입니다. ct_show", 'back');
    }

    unset($arr_query);
    $arr_query = array(
        "ct_name" => clean_xss_tags($_POST['ct_name']),
        "ct_sub_name" => clean_xss_tags($_POST['ct_sub_name']),
        "ct_show" => $_POST['ct_show'],
    );
    $DB->where('ct_id', $_POST['ct_idx']);
    $DB->update('board_category_t', $arr_query);
    $_last_idx = $_POST['ct_idx'];

    p_alert("수정되었습니다.");

} elseif ($_POST['act'] == "delete") {

    $DB->where('ct_id', $_POST['idx']);
    $DB->delete('board_category_t');

    echo "Y";

} elseif ($_POST['act'] == "list") {
    unset($list);
    $DB->pageLimit = $n_limit_num;
    $pg = $_POST['obj_pg'];

    //검색
    if ($_POST['search_txt']) {
        if ($_POST['sel_search'] == "all") {
            $DB->where('( instr(a1.ct_name, \''.$_POST['search_txt'].'\') or instr(a1.ct_sub_name, \''.$_POST['search_txt'].'\') )');
        } else {
            $DB->where('( instr('.$_POST['sel_search'].', \''.$_POST['search_txt'].'\') )');
        }
    }

    if ($_POST['ct_show']) {
        $DB->where('a1.ct_show', $_POST['ct_show']);
    }

    //정렬
    if ($_POST['obj_order_desc_asc'] == '1') {
        $DB->orderBy("a1.ct_id", "desc");
    } else {
        $DB->orderBy("a1.ct_id", "asc");
    }

    $list = $DB->arraybuilder()->paginate("board_category_t a1", $pg, '*, ct_id as ct_idx');

    //페이징
    $n_page = $DB->totalPages;
    $counts = $DB->totalCount;
    $counts = $counts - (($pg - 1) * $n_limit_num);
    ?>
    <div class="table-responsive margin-top-20">
        <table class="table table-striped table-bordered margin-bottom-20" style="min-width: 800px">
            <thead class="thead-dark">
            <tr>
                <th class="text-center" style="width:100px;">
                    번호
                </th>
                <th class="text-center" style="width:160px;">
                    관리
                </th>
                <th class="text-center">
                    카테고리명
                </th>
                <th class="text-center">
                    카테고리설명
                </th>
                <th class="text-center" style="width:120px;">
                    노출여부
                </th>
                <th class="text-center" style="width:140px;">
                    등록일시
                </th>
            </tr>
            </thead>
            <tbody>
            <?php
            if ($list) {
                foreach ($list as $row) {
                    ?>
                    <tr>
                        <td data-title="번호" class="text-center">
                            <?=$counts?>
                        </td>
                        <td data-title="관리" class="text-center">
                            <input type="button" class="btn btn-outline-primary btn-sm" value="수정" onclick="location.href='./blog_category_form.php?act=update&ct_idx=<?=$row['ct_idx']?>'" />
                            <input type="button" class="btn btn-outline-danger btn-sm" value="삭제" onclick="f_post_del('./blog_category_update.php', '<?=$row['ct_idx']?>');" />
                        </td>
                        <td data-title="카테고리명">
                            <span class="line1_text"><?=$row['ct_name']?></span>
                        </td>
                        <td data-title="카테고리설명" class="text-center">
                            <?=$row['ct_sub_name']?>
                        </td>
                        <td data-title="노출여부" class="text-center">
                            <?=$row['ct_show']?>
                        </td>
                        <td data-title="등록일시" class="text-center">
                            <?=DateType($row['ct_datetime'], 4)?>
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
