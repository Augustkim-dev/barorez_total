<?php
function comment_get_thread($idx, $thread = ''){
  global $DB;
  // 답글 레벨 지정
  $reply_len = strlen($thread) + 1;
  $begin_reply_char = 'A';
  $end_reply_char = 'B';
  $reply_number = +1;

  $params = [$idx];
  $sql = "SELECT MAX(SUBSTRING(cmt_thread, $reply_len, 1)) as reply FROM comment_t WHERE idx = ? and SUBSTRING(cmt_thread, {$reply_len}, 1) <> '' ";
  if ($thread != '') $sql .= " and thread like '?%' ";

  if ($thread != '') {
    $sql .= " and cmt_thread like ?";
    $params[] = $thread . '%';
  }

  $row = $DB->ObjectBuilder()->rawQueryOne($sql, $params);

  if (!$row->reply) {
    $reply_char = $begin_reply_char;
  } else if ($row->reply == $end_reply_char) { // A~Z은 26 입니다.
    // return "더 이상 답변하실 수 없습니다. 답변은 26개 까지만 가능합니다.";
    return "error";
  } else {
    $reply_char = chr(ord($row->reply) + $reply_number);
  }
  $rs = $thread . $reply_char;

  return $rs;
}


function comment_get_list($gubun, $pid, $page = 1){
  global $DB;

  $DB->where('cmt_show', 'Y');
  $DB->where('cmt_gubun', $gubun);
  $DB->where('cmt_thread', '', '!=');
  $DB->where('pid', $pid);

  $DB->orderBy("cmt_order", "asc");

  $DB->setTrace(true); // 쿼리 추적 활성화
  $list = $DB->arraybuilder()->paginate("comment_t", $page, '*');
  $debug = $DB->trace; // 모든 쿼리 정보 배열로 반환
  //print_r($debug);
  //echo "<!-- pre getLastQuery>";
  //print_r($DB->getLastQuery());
  //echo "</pre --!>";



  //페이징
  //$n_page = $DB->totalPages;
  //$counts = $DB->totalCount;
  //$counts = $counts - (($pg - 1) * $_POST['obj_limit_num']);

  return $list;
}


function comment_get_count($gubun, $pid){

  // $sql = "SELECT count(*) as cnt FROM ".$this->table." WHERE bd_table = '".$bd_table."' and pid=".$pid." and cmt_thread ='' and cmt_show ='y' ";
  // $rs = collect(DB::select($sql))->first();
  // $cnt = (int) $rs->cnt;

  // return $cnt;
}


function comment_reply_get_list($gubun, $pid, $cid){

  // $sql = "SELECT * FROM ".$this->table." WHERE bd_table = '".$bd_table."' and pid=".$pid." and cid=".$cid." and cmt_thread !='' and cmt_show ='y' order by idx ";
  // $rs = DB::select($sql);
  // return $rs;
}

function comment_reply_get_count($gubun, $pid, $cid) {
  global $DB;

  $sql = "SELECT count(*) as cnt FROM comment_t WHERE cmt_gubun = ? and pid=? and cid=?  and cmt_thread !='' and cmt_show ='y' ";
  $rs = $DB->rawQueryOne($sql, [$gubun, $pid, $cid]);
  $cnt = (int) $rs['cnt'];

  return $cnt;
}

function isCommentLikedByUser($mt_idx, $cmt_idx) {
  global $DB;
  $tbl_like = "comment_like_t";

  $DB->where("mt_idx", $mt_idx);
  $DB->where("cmt_idx", $cmt_idx);
  $row = $DB->getOne($tbl_like, "COUNT(*) as cnt");

  return (isset($row['cnt']) && $row['cnt'] > 0);
}

function toggleCommentLike($mt_idx, $cmt_idx) {
  global $DB, $CFG_LANG;

  $tbl_like = "comment_like_t";
  $tbl_comment = "comment_t";

  // 본인이 작성한 댓글인지 확인 (좋아요 불가)
  $DB->where("idx", $cmt_idx);
  $comment = $DB->getOne($tbl_comment, "mt_idx");

  if (!$comment || $comment['mt_idx'] == $mt_idx) {
    return ['success' => false, 'message' => $CFG_LANG['comment']['self_msg']];
  }

  // 좋아요 여부 확인
  $DB->where("mt_idx", $mt_idx);
  $DB->where("cmt_idx", $cmt_idx);
  $row = $DB->getOne($tbl_like, "COUNT(*) as cnt");

  if ($row && $row['cnt'] > 0) {
    // 좋아요 취소
    $DB->where("mt_idx", $mt_idx);
    $DB->where("cmt_idx", $cmt_idx);
    $DB->delete($tbl_like);

    $DB->rawQuery("UPDATE {$tbl_comment} SET cmt_like = GREATEST(cmt_like - 1, 0) WHERE idx = ?", [$cmt_idx]);

    return ['success' => true, 'status' => 'deleted'];
  } else {
    // 좋아요 추가
    $DB->insert($tbl_like, [
      "mt_idx" => $mt_idx,
      "cmt_idx" => $cmt_idx,
      "created_at" => date("Y-m-d H:i:s")
    ]);

    $DB->rawQuery("UPDATE {$tbl_comment} SET cmt_like = cmt_like + 1 WHERE idx = ?", [$cmt_idx]);

    return ['success' => true, 'status' => 'added'];
  }
}


?>