<?php

// 팔로우 신청
function followRequest($follower_idx, $following_idx) {
  global $DB, $CFG_LANG;

  // 차단 여부 확인: 상대방이 나를 차단한 경우
  $is_blocked = $DB->where('blocker_idx', $following_idx)
    ->where('blocked_idx', $follower_idx)
    ->has('member_follow_block_t');

  if ($is_blocked) {
    $json = ['success' => false, 'message' => $CFG_LANG['follow']['blocked_follow']];
    die(json_encode($json, JSON_UNESCAPED_UNICODE));
  }

  $exists = $DB->where('follower_idx', $follower_idx)
    ->where('following_idx', $following_idx)
    ->has('member_follow_t');

  if ($exists) {
    $json = ['success' => false, 'message' => $CFG_LANG['follow']['already_requested']];
    die(json_encode($json, JSON_UNESCAPED_UNICODE));
  }

  $data = [
    'follower_idx' => $follower_idx,
    'following_idx' => $following_idx,
    'status' => 'W',
    'follow_date' => $DB->now()
  ];

  $id = $DB->insert('member_follow_t', $data);

  if ($id) {
    $json = ['success' => true, 'message' => $CFG_LANG['follow']['request_success']];
  } else {
    $json = ['success' => false, 'message' => $CFG_LANG['follow']['request_fail']];
  }
  die(json_encode($json, JSON_UNESCAPED_UNICODE));
}

// 팔로우 승인
function followAccept($follower_idx, $following_idx) {
  global $DB, $CFG_LANG;

  $exists = $DB->where('follower_idx', $follower_idx)
    ->where('following_idx', $following_idx)->where('status', 'W')
    ->has('member_follow_t');

  if (!$exists) {
    $json = ['success' => false, 'message' => $CFG_LANG['follow']['no_request_to_accept']];
    die(json_encode($json, JSON_UNESCAPED_UNICODE));
  }

  $DB->where('follower_idx', $follower_idx)
    ->where('following_idx', $following_idx)
    ->where('status', 'W');
  $updateData = [
    'status' => 'Y',
    'confirm_date' => $DB->now()
  ];

  if ($DB->update('member_follow_t', $updateData)) {

    // 팔로잉 수 증가
    $DB->where('idx', $follower_idx)
      ->update('member_t', ['mt_following_cnt' => $DB->inc(1)]);

    // 팔로워 수 증가
    $DB->where('idx', $following_idx)
      ->update('member_t', ['mt_follower_cnt' => $DB->inc(1)]);

    $json = ['success' => true, 'message' => $CFG_LANG['follow']['accept_success']];

  } else {
    $json = ['success' => false, 'message' => $CFG_LANG['follow']['accept_fail']];
  }
  die(json_encode($json, JSON_UNESCAPED_UNICODE));
}

// 팔로우 거절
function followReject($follower_idx, $following_idx) {
  global $DB, $CFG_LANG;

  $exists = $DB->where('follower_idx', $follower_idx)
    ->where('following_idx', $following_idx)->where('status', 'W')
    ->has('member_follow_t');

  if (!$exists) {
    $json = ['success' => false, 'message' => $CFG_LANG['follow']['no_request_to_reject']];
    die(json_encode($json, JSON_UNESCAPED_UNICODE));
  }



  $DB->where('follower_idx', $follower_idx)
    ->where('following_idx', $following_idx)
    ->where('status', 'W');
  $updateData = [
    'status' => 'N',
    'confirm_date' => $DB->now()
  ];

  if ($DB->update('member_follow_t', $updateData)) {
    $json = ['success' => true, 'message' => $CFG_LANG['follow']['reject_success']];
  } else {
    $json = ['success' => false, 'message' => $CFG_LANG['follow']['reject_fail']];
  }
  die(json_encode($json, JSON_UNESCAPED_UNICODE));
}



// 팔로잉 가능한 회원목록
function getUnfollowingList($member_idx) {
  global $DB, $CFG_LANG;

  $sql = "
    SELECT m.idx, m.mt_id, m.mt_name, m.mt_nickname, m.mt_image1
    FROM member_t m
    LEFT JOIN member_follow_t f
      ON f.follower_idx = ? 
      AND f.following_idx = m.idx
      AND f.status = 'Y'
    WHERE m.idx != ?
      AND m.mt_level = 2        
      AND f.following_idx IS NULL
      AND m.idx NOT IN (
        SELECT blocked_idx FROM member_follow_block_t WHERE blocker_idx = ?
      )
      AND m.idx NOT IN (
        SELECT blocker_idx FROM member_follow_block_t WHERE blocked_idx = ?
      )
    ORDER BY m.idx DESC
  ";

  $result = $DB->rawQuery($sql, [$member_idx, $member_idx, $member_idx, $member_idx]);

  return ['list' => $result, 'total' => count($result)];
}

// 팔로우 바로 승인
function followApprove($follower_idx, $following_idx) {
  global $DB, $CFG_LANG;

  // 차단 여부 확인: 상대방이 나를 차단한 경우
  $is_blocked = $DB->where('blocker_idx', $following_idx)
    ->where('blocked_idx', $follower_idx)
    ->has('member_follow_block_t');

  if ($is_blocked) {
    $json = ['success' => false, 'message' => $CFG_LANG['follow']['blocked_follow']];
    die(json_encode($json, JSON_UNESCAPED_UNICODE));
  }

  $exists = $DB->where('follower_idx', $follower_idx)
    ->where('following_idx', $following_idx)
    ->where('status', 'Y')
    ->has('member_follow_t');

  if ($exists) {
    $json = ['success' => false, 'message' => $CFG_LANG['follow']['already_following']];
    die(json_encode($json, JSON_UNESCAPED_UNICODE));
  }

  $data = [
    'follower_idx' => $follower_idx,
    'following_idx' => $following_idx,
    'status' => 'Y',
    'follow_date' => $DB->now(),
    'confirm_date' => $DB->now()
  ];

  $id = $DB->insert('member_follow_t', $data);

  $tpl_sql = "SELECT * FROM fcm_template_t WHERE type = 'follow' AND activity = 'following' LIMIT 1";
  $tpl = $DB->rawQueryOne($tpl_sql);

  $DB->where('idx', $following_idx);
  $you = $DB->getOne('member_t');

  $DB->where('idx', $follower_idx);
  $me = $DB->getOne('member_t');

  $target_link = isset($tpl['target_link']) ? $tpl['target_link'] : APP_DOMAIN;
  $replacements = [
    '{user}' => $you['idx'],
  ];
  $target_link   = strtr($target_link, $replacements);
  $param = [
    'app_token'   => $you['mt_app_token'],
    'template'    => $tpl['idx'],
    'mt_idx'      => $you['idx'],
    'name'      => $me['mt_name'],
    'target_link' => $target_link
  ];
  $result = notifyByTemplate($param);

  $web_target_link = isset($tpl['web_target_link']) ? $tpl['web_target_link'] : APP_DOMAIN;
  $web_target_link = strtr($web_target_link, $replacements);

  $sql = "SELECT * FROM member_fcm_token_t WHERE mt_idx = ? AND platform = 'web' ORDER BY idx ASC";
  $list = $DB->rawQuery($sql, [$you['idx']]);
  if (count($list) > 0) {
    foreach ($list as $item) {
      $secure_target_url = APP_DOMAIN."/webpush/redirect.php"
        . "?redirect_mobile=" . urlencode($target_link)
        . "&redirect_pc=" . urlencode($web_target_link)
        . "&fcm_token=" . urlencode($item['fcm_token']);

      $param = [
        'web_token'   => $item['fcm_token'],
        'template'    => $tpl['idx'],
        'mt_idx'      => $you['idx'],
        'web_target_link' => $secure_target_url,
      ];
      $result = notifyByTemplate($param);
    }
  }




  if ($id) {
    $json = ['success' => true, 'message' => $CFG_LANG['follow']['approve_success']];
  } else {
    $json = ['success' => false, 'message' => $CFG_LANG['follow']['approve_fail']];
  }
  die(json_encode($json, JSON_UNESCAPED_UNICODE));
}

// 언팔로우 처리
function unfollow($follower_idx, $following_idx) {
  global $DB, $CFG_LANG;


  $exists = $DB->where('follower_idx', $follower_idx)
    ->where('following_idx', $following_idx)->where('status', 'Y')
    ->has('member_follow_t');

  if (!$exists) {
    $json = ['success' => false, 'message' => $CFG_LANG['follow']['no_follow_to_unfollow']];
    die(json_encode($json, JSON_UNESCAPED_UNICODE));
  }


  $DB->where('follower_idx', $follower_idx)
    ->where('following_idx', $following_idx)
    ->where('status', 'Y');
  if ($DB->delete('member_follow_t')) {

    // 팔로잉 수 감소
    $DB->where('idx', $follower_idx)
      ->update('member_t', ['mt_following_cnt' => $DB->dec(1)]);

    // 팔로워 수 감소
    $DB->where('idx', $following_idx)
      ->update('member_t', ['mt_follower_cnt' => $DB->dec(1)]);

    $json = ['success' => true, 'message' => $CFG_LANG['follow']['unfollow_success']];
  } else {
    $json = ['success' => false, 'message' => $CFG_LANG['follow']['unfollow_fail']];
  }
  die(json_encode($json, JSON_UNESCAPED_UNICODE));
}

// 팔로워 제거 기능
function followRemove($my_idx, $follower_idx) {
  global $DB, $CFG_LANG;

  $exists = $DB->where('follower_idx', $follower_idx)
    ->where('following_idx', $my_idx)
    ->where('status', 'Y')
    ->has('member_follow_t');

  if (!$exists) {
    die(json_encode(['success' => false, 'message' => $CFG_LANG['follow']['not_follower']], JSON_UNESCAPED_UNICODE));
  }

  if ($DB->where('follower_idx', $follower_idx)
    ->where('following_idx', $my_idx)
    ->where('status', 'Y')
    ->delete('member_follow_t')) {

    $DB->where('idx', $follower_idx)->update('member_t', ['mt_following_cnt' => $DB->dec(1)]);
    $DB->where('idx', $my_idx)->update('member_t', ['mt_follower_cnt' => $DB->dec(1)]);

    die(json_encode(['success' => true, 'message' => $CFG_LANG['follow']['remove_success']], JSON_UNESCAPED_UNICODE));
  } else {
    die(json_encode(['success' => false, 'message' => $CFG_LANG['follow']['remove_fail']], JSON_UNESCAPED_UNICODE));
  }
}


// 팔로잉 목록
function getFollowingList($member_idx) {
  global $DB, $CFG_LANG;

  $DB->join("member_t m", "f.following_idx = m.idx", "INNER");
  $DB->where("f.follower_idx", $member_idx);
  $DB->where("f.status", 'Y');
  $DB->orderBy("f.confirm_date", "DESC");

  $result = $DB->get("member_follow_t f", null, "m.idx, m.mt_id, m.mt_name, m.mt_nickname, m.mt_image1, f.follower_idx, f.following_idx, f.confirm_date");
  $query = $DB->getLastQuery();
  //echo "<!-- pre getFollowingList>";
  //print_r($query);
  //echo "</pre --!>";

  return ['list' => $result, 'total' => $DB->count];
}

// 팔로워 목록
function getFollowerList($member_idx) {
  global $DB, $CFG_LANG;

  $DB->join("member_t m", "f.follower_idx = m.idx", "INNER");
  $DB->where("f.following_idx", $member_idx);
  $DB->where("f.status", 'Y');
  $DB->orderBy("f.confirm_date", "DESC");

  $result = $DB->get("member_follow_t f", null, "m.idx, m.mt_id, m.mt_name, m.mt_nickname, m.mt_image1, f.follower_idx, f.following_idx,  f.confirm_date");
  return ['list' => $result, 'total' => $DB->count];
}


// 내가 팔로우 신청한 승인대기 목록
function getMyFollowPendingList($member_idx) {
  global $DB, $CFG_LANG;

  $DB->join("member_t m", "f.following_idx = m.idx", "INNER");
  $DB->where("f.follower_idx", $member_idx); // 내가 신청한 사람
  $DB->where("f.status", 'W'); // 승인 대기 상태
  $DB->orderBy("f.follow_date", "DESC");

  $result = $DB->get("member_follow_t f", null, "m.idx, m.mt_id, m.mt_name, m.mt_nickname, m.mt_image1, f.follower_idx, f.following_idx, f.follow_date");
  return ['list' => $result, 'total' => $DB->count];
}


// 나를 팔로우 신청한 승인대기  목록
function getFollowPendingList($member_idx) {
  global $DB, $CFG_LANG;

  $DB->join("member_t m", "f.follower_idx = m.idx", "INNER");
  $DB->where("f.following_idx", $member_idx);
  $DB->where("f.status", 'W');
  $DB->orderBy("f.follow_date", "DESC");

  $result = $DB->get("member_follow_t f", null, "m.idx, m.mt_id, m.mt_name, m.mt_nickname, m.mt_image1, f.follower_idx, f.following_idx,  f.follow_date");
  return ['list' => $result, 'total' => $DB->count];
}


// 내가 팔로우 신청한 상대방이 승인거절 목록
function getFollowRejectedList($member_idx) {
  global $DB, $CFG_LANG;

  $DB->join("member_t m", "f.following_idx = m.idx", "INNER");
  $DB->where("f.follower_idx", $member_idx);
  $DB->where("f.status", 'N');
  $DB->orderBy("f.confirm_date", "DESC");

  $result = $DB->get("member_follow_t f", null, "m.idx, m.mt_id, m.mt_name, m.mt_nickname, m.mt_image1, f.follower_idx, f.following_idx, f.confirm_date");
  return ['list' => $result, 'total' => $DB->count];
}

// 나를 팔로우 신청한 내가 승인거절 목록
function getFollowerRejectedList($member_idx) {
  global $DB, $CFG_LANG;

  $DB->join("member_t m", "f.follower_idx = m.idx", "INNER");
  $DB->where("f.following_idx", $member_idx); // 나를 팔로우 신청한 사람
  $DB->where("f.status", 'N'); // 내가 거절함
  $DB->orderBy("f.confirm_date", "DESC");

  $result = $DB->get("member_follow_t f", null, "m.idx, m.mt_id, m.mt_name, m.mt_nickname, m.mt_image1, f.follower_idx, f.following_idx, f.confirm_date");
  return ['list' => $result, 'total' => $DB->count];
}

// 팔로워 차단
function blockFollower($me_idx, $follower_idx) {
  global $DB, $CFG_LANG;

  // 이미 차단했는지 확인
  $is_blocked = $DB->where('blocker_idx', $me_idx)
    ->where('blocked_idx', $follower_idx)
    ->has('member_follow_block_t');

  if ($is_blocked) {
    die(json_encode(['success' => false, 'message' => $CFG_LANG['follow']['already_blocked']], JSON_UNESCAPED_UNICODE));
  }

  // 차단 정보 등록
  $data = [
    'blocker_idx' => $me_idx,
    'blocked_idx' => $follower_idx,
    'block_date' => $DB->now()
  ];
  $DB->insert('member_follow_block_t', $data);

  // 팔로우 관계 끊기 (일방 or 양방)
  $DB->where('(follower_idx = ? AND following_idx = ?) OR (follower_idx = ? AND following_idx = ?)',
    [$me_idx, $follower_idx, $follower_idx, $me_idx])
    ->delete('member_follow_t');

  // 팔로잉 수 감소
  $DB->where('idx', $follower_idx)
    ->update('member_t', ['mt_following_cnt' => $DB->dec(1)]);

  // 팔로워 수 감소
  $DB->where('idx', $me_idx)
    ->update('member_t', ['mt_follower_cnt' => $DB->dec(1)]);

  die(json_encode(['success' => true, 'message' => $CFG_LANG['follow']['block_success']], JSON_UNESCAPED_UNICODE));
}

// 팔로워 차단해제
function unblockFollower($me_idx, $follower_idx) {
  global $DB, $CFG_LANG;

  $deleted = $DB->where('blocker_idx', $me_idx)
    ->where('blocked_idx', $follower_idx)
    ->delete('member_follow_block_t');

  if ($deleted) {
    die(json_encode(['success' => true, 'message' => $CFG_LANG['follow']['unblock_success']], JSON_UNESCAPED_UNICODE));
  } else {
    die(json_encode(['success' => false, 'message' => $CFG_LANG['follow']['unblock_fail']], JSON_UNESCAPED_UNICODE));
  }
}





// 내가 상대를 차단했는지 확인
function myBlocked($me_idx, $target_idx) {
  global $DB, $CFG_LANG;

  return $DB->where('blocker_idx', $me_idx)
    ->where('blocked_idx', $target_idx)
    ->has('member_follow_block_t');
}

// 상대가 나를 차단했는지 확인
function otherBlocked($target_idx, $me_idx) {
  global $DB, $CFG_LANG;

  return $DB->where('blocker_idx', $target_idx)
    ->where('blocked_idx', $me_idx)
    ->has('member_follow_block_t');
}

/*
 *
if (myBlocked($my_idx, $other_idx)) {
  echo "내가 이 사람을 차단했음";
}
if (otherBlocked($other_idx, $my_idx)) {
  echo "상대가 나를 차단했음";
}
if (isBlocked($my_idx, $other_idx)) {
  die(json_encode(['success' => false, 'message' => '해당 회원과는 상호작용할 수 없습니다.'], JSON_UNESCAPED_UNICODE));
}
 */
// 차단 여부 확인
function isBlocked($me_idx, $target_idx) {
  return myBlocked($me_idx, $target_idx) || otherBlocked($target_idx, $me_idx);
}

// 나의 차단 목록
function getBlockedList($me_idx) {
  global $DB, $CFG_LANG;

  $DB->join("member_t m", "b.blocked_idx = m.idx", "INNER");
  $DB->where("b.blocker_idx", $me_idx);
  $DB->orderBy("b.block_date", "DESC");

  $result = $DB->get("member_follow_block_t b", null,
    "m.idx, m.mt_id, m.mt_name, m.mt_nickname, m.mt_image1, b.block_date");

  return ['list' => $result, 'total' => $DB->count];
}



function getFollowingListExceptMe($member_idx, $my_idx) {
  global $DB;

  $DB->join("member_t m", "f.following_idx = m.idx", "INNER");
  $DB->where("f.follower_idx", $member_idx);
  $DB->where("f.status", 'Y');
  $DB->where("f.following_idx", $my_idx, "!="); // 너 제외
  $DB->orderBy("f.confirm_date", "DESC");

  $result = $DB->get("member_follow_t f", null, "m.idx, m.mt_id, m.mt_name, m.mt_nickname, m.mt_image1, f.follower_idx, f.following_idx, f.confirm_date");
  return ['list' => $result, 'total' => $DB->count];
}


function getFollowerListExceptMe($member_idx, $my_idx) {
  global $DB;

  $DB->join("member_t m", "f.follower_idx = m.idx", "INNER");
  $DB->where("f.following_idx", $member_idx);
  $DB->where("f.status", 'Y');
  $DB->where("f.follower_idx", $my_idx, "!="); // 너 제외
  $DB->orderBy("f.confirm_date", "DESC");

  $result = $DB->get("member_follow_t f", null, "m.idx, m.mt_id, m.mt_name, m.mt_nickname, m.mt_image1, f.follower_idx, f.following_idx, f.confirm_date");
  return ['list' => $result, 'total' => $DB->count];
}


function getMyFollowingIds($my_idx) {
  global $DB;

  $DB->where("follower_idx", $my_idx);
  $DB->where("status", 'Y');
  $rows = $DB->getValue("member_follow_t", "following_idx", null); // 배열로 리턴됨
  return $rows ?: [];
}