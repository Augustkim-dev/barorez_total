<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";

if ($_POST['act'] == "input") {
    if($_POST['bt_title'] == "") {
        p_alert("잘못된 접근입니다. bt_title", 'back');
    }
    if($_POST['bt_content'] == "") {
        p_alert("잘못된 접근입니다. bt_content", 'back');
    }
    if($_POST['bt_show'] == "") {
        p_alert("잘못된 접근입니다. bt_show", 'back');
    }

    //$_POST['bt_content']  = str_replace('<ul>', '<ul class="list">', $_POST['bt_content']);

    // 또는 정규식(preg_replace) 사용 - 더 정확한 매칭을 위해
    $_POST['bt_content'] = preg_replace('/<ul(?![^>]*class=)([^>]*)>/', '<ul class="list"$1>', $_POST['bt_content']);

    unset($arr_query);
    $arr_query = array(
        "mt_idx"        => 1,
        "mt_id"         => "admin",
        "mt_name"       => "",
        "bt_title"      => $_POST['bt_title'],
        "bt_keyword"    => $_POST['bt_keyword'],
        "bt_content"    => $_POST['bt_content'],
        "bt_catetory"   => $_POST['bt_catetory'],
        "bt_show"       => $_POST['bt_show'],
        "bt_hit"        => 1,
        "bt_wdate"      => $DB->now(),
    );

    $_last_idx = $DB->insert('blog_t', $arr_query);

    p_alert("등록되었습니다.", "./blog_list.php");

}else if ($_POST['act'] == "update") {

    //$_POST['bt_content']  = str_replace('<ul>', '<ul class="list">', $_POST['bt_content']);

    // 또는 정규식(preg_replace) 사용 - 더 정확한 매칭을 위해
    $_POST['bt_content'] = preg_replace('/<ul(?![^>]*class=)([^>]*)>/', '<ul class="list"$1>', $_POST['bt_content']);

    unset($arr_query);
    $arr_query = array(
        "bt_title"      => $_POST['bt_title'],
        "bt_keyword"    => $_POST['bt_keyword'],
        "bt_content"    => $_POST['bt_content'],
        "bt_catetory"   => $_POST['bt_catetory'],
        "bt_show"       => $_POST['bt_show'],
        "bt_udate"      => $DB->now(),
    );
    $DB->where('idx', $_POST['btIdx']);
    $DB->update('blog_t', $arr_query);
    $_last_idx = $_POST['idx'];

    p_alert("수정되었습니다.");

} elseif ($_POST['act'] == "delete") {
    $DB->where('idx', $_POST['idx']);
    $row = $DB->getone('blog_t');

    // 에디터 썸네일 삭제
    //delete_editor_thumbnail($row['nt_content']);
    // 에디터 이미지 삭제
    //delete_editor_image($row['nt_content']);

    $DB->where('idx', $_POST['idx']);
    $DB->delete('blog_t');

    echo "Y";





} elseif ($_POST['act'] == "list") {
    unset($list);
    $DB->pageLimit = $n_limit_num;
    $pg = $_POST['obj_pg'];

    //검색
    if ($_POST['search_txt']) {
        if ($_POST['sel_search'] == "all") {
            $DB->where('( instr(a1.bt_title, \''.$_POST['search_txt'].'\') or instr(a1.bt_content, \''.$_POST['search_txt'].'\') )');
        } else {
            $DB->where('( instr('.$_POST['sel_search'].', \''.$_POST['search_txt'].'\') )');
        }
    }

    if ($_POST['bt_show']) {
        $DB->where('a1.bt_show', $_POST['bt_show']);
    }

    //정렬
    if ($_POST['obj_order_desc_asc'] == '1') {
        $DB->orderBy("a1.idx", "desc");
    } else {
        $DB->orderBy("a1.idx", "asc");
    }

    $list = $DB->arraybuilder()->paginate("blog_t a1", $pg, '*, idx as btIdx');

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
                    제목
                </th>
                <th class="text-center" style="width:120px;">
                    노출여부
                </th>
                <th class="text-center" style="width:120px;">
                    조회수
                </th>
                <th class="text-center" style="width:140px;">
                    등록일시
                </th>
                <th class="text-center" style="width:140px;">
                    수정일시
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
                            <input type="button" class="btn btn-outline-primary btn-sm" value="수정" onclick="location.href='./blog_form.php?act=update&btIdx=<?=$row['btIdx']?>'" />
                            <input type="button" class="btn btn-outline-danger btn-sm" value="삭제" onclick="f_post_del('./blog_update.php', '<?=$row['btIdx']?>');" />
                        </td>
                        <td data-title="제목">
                            <span class="line1_text"><?=$row['bt_title']?></span>
                        </td>
                        <td data-title="노출여부" class="text-center">
                            <?=$row['bt_show']?>
                        </td>
                        <td data-title="조회수" class="text-center">
                            <?=number_format($row['bt_hit'])?>
                        </td>
                        <td data-title="등록일시" class="text-center">
                            <?=DateType($row['bt_wdate'], 4)?>
                        </td>
                        <td data-title="수정일시" class="text-center">
                            <?=DateType($row['bt_udate'], 4)?>
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
