<?php



/**
  회원 리뷰 수 기반으로 뱃지를 부여하거나 회수함
  $mt_idx = 123;
  syncReviewBadges($mt_idx);
 */
function syncReviewBadges($mt_idx) {

  global $DB;

  // 1. 현재 회원 리뷰 수 확인
  $DB->where('mt_idx', $mt_idx);
  $review_count = $DB->getValue('review_t', 'COUNT(*)');

  // 2. 리뷰 뱃지 마스터 불러오기
  $DB->where('bm_type', 'review');
  $DB->orderBy('bm_threshold', 'ASC');
  $badge_list = $DB->get('badge_master_t', null, ['idx', 'bm_threshold']);

  foreach ($badge_list as $badge) {
    $badge_idx = $badge['idx'];
    $threshold = $badge['bm_threshold'];

    // 3. 해당 뱃지를 이미 가지고 있는지 확인
    $DB->where('mt_idx', $mt_idx);
    $DB->where('badge_idx', $badge_idx);
    $hasBadge = $DB->getValue('member_badge_t', 'COUNT(*)') > 0;

    if ($review_count >= $threshold && !$hasBadge) {
      // 4. 조건 충족 + 아직 없음 → INSERT
      $DB->insert('member_badge_t', [
      'mt_idx' => $mt_idx,
      'badge_idx' => $badge_idx,
      'created_at' => date('Y-m-d H:i:s')
      ]);
    } elseif ($review_count < $threshold && $hasBadge) {
      // 5. 조건 미달 + 이미 있음 → DELETE
      $DB->where('mt_idx', $mt_idx);
      $DB->where('badge_idx', $badge_idx);
      $DB->delete('member_badge_t');
    }
  }
}

/**
품목수 기반으로 뱃지를 부여하거나 회수함
$mt_idx = 123;
syncWineTypeBadges($mt_idx);
 */
function syncWineTypeBadges($mt_idx) {
  global $DB;


  $sql = "SELECT type_id AS category, COUNT(*) AS cnt
            FROM review_t
            WHERE mt_idx = ?
              AND type_id IS NOT NULL
              AND type_id != 0
            GROUP BY type_id";
  $item_counts = $DB->rawQuery($sql, [$mt_idx]);

  // 2. 품종 1차용 뱃지 마스터 조회
  $DB->where('bm_type', 'wine_type');
  $badge_list = $DB->get('badge_master_t');

  foreach ($item_counts as $row) {
    $category = $row['category'];
    $count = $row['cnt'];

    foreach ($badge_list as $badge) {
      if ($badge['bm_category'] == $category) {
        $badge_idx = $badge['idx'];
        $threshold = $badge['bm_threshold'];

        // 뱃지 보유 여부 확인
        $DB->where('mt_idx', $mt_idx);
        $DB->where('badge_idx', $badge_idx);
        $hasBadge = $DB->getValue('member_badge_t', 'COUNT(*)') > 0;

        if ($count >= $threshold && !$hasBadge) {
          $DB->insert('member_badge_t', [
            'mt_idx' => $mt_idx,
            'badge_idx' => $badge_idx,
            'created_at' => date('Y-m-d H:i:s')
          ]);
        } elseif ($count < $threshold && $hasBadge) {
          $DB->where('mt_idx', $mt_idx);
          $DB->where('badge_idx', $badge_idx);
          $DB->delete('member_badge_t');
        }
      }
    }
  }
}

/**
품종수 기반으로 뱃지를 부여하거나 회수함
$mt_idx = 123;
syncWineGrapeBadges($mt_idx);
 */
function syncWineGrapeBadges($mt_idx) {
  global $DB;

  $sql = "SELECT DISTINCT pt_idx FROM review_t WHERE mt_idx = ?";
  $reviewed_wines = $DB->rawQuery($sql, [$mt_idx]);
  $wine_ids = array_column($reviewed_wines, 'pt_idx');
  if (empty($wine_ids)) return;


  $placeholders = implode(',', array_fill(0, count($wine_ids), '?'));
  $sql = "
    SELECT grape_id, COUNT(DISTINCT wine_id) AS cnt
    FROM wine_product_grape_t
    WHERE wine_id IN ($placeholders)
    GROUP BY grape_id
  ";
  $item_counts = $DB->rawQuery($sql, $wine_ids);


  $DB->where('bm_type', 'wine_grape');
  $badge_list = $DB->get('badge_master_t');

  foreach ($item_counts as $row) {
    $category = $row['grape_id'];
    $count = $row['cnt'];

    foreach ($badge_list as $badge) {
      if ($badge['bm_category'] == $category) {
        $badge_idx = $badge['idx'];
        $threshold = $badge['bm_threshold'];

        // 뱃지 보유 여부 확인
        $DB->where('mt_idx', $mt_idx);
        $DB->where('badge_idx', $badge_idx);
        $hasBadge = $DB->getValue('member_badge_t', 'COUNT(*)') > 0;

        if ($count >= $threshold && !$hasBadge) {
          $DB->insert('member_badge_t', [
            'mt_idx' => $mt_idx,
            'badge_idx' => $badge_idx,
            'created_at' => date('Y-m-d H:i:s')
          ]);
        } elseif ($count < $threshold && $hasBadge) {
          $DB->where('mt_idx', $mt_idx);
          $DB->where('badge_idx', $badge_idx);
          $DB->delete('member_badge_t');
        }
      }
    }
  }
}


/**
  회원 리뷰 수 기반으로 국가 뱃지를 부여하거나 회수함
  $mt_idx = 123;
  syncCountryBadges($mt_idx);
 */
function syncCountryBadges($mt_idx) {
  global $DB;


  $sql = "SELECT wine_country AS category, COUNT(*) AS cnt
            FROM review_t
            WHERE mt_idx = ?
            GROUP BY wine_country";
  $country_counts = $DB->rawQuery($sql, [$mt_idx]);

  $DB->where('bm_type', 'country');
  $badge_list = $DB->get('badge_master_t');

  foreach ($country_counts as $row) {
    $category = $row['category'];
    $count = $row['cnt'];

    foreach ($badge_list as $badge) {
      if ($badge['bm_category'] === $category) {
        $badge_idx = $badge['idx'];
        $threshold = $badge['bm_threshold'];

        $DB->where('mt_idx', $mt_idx);
        $DB->where('badge_idx', $badge_idx);
        $hasBadge = $DB->getValue('member_badge_t', 'COUNT(*)') > 0;

        if ($count >= $threshold && !$hasBadge) {
          $DB->insert('member_badge_t', [
            'mt_idx' => $mt_idx,
            'badge_idx' => $badge_idx,
            'created_at' => date('Y-m-d H:i:s')
          ]);
        } elseif ($count < $threshold && $hasBadge) {
          $DB->where('mt_idx', $mt_idx);
          $DB->where('badge_idx', $badge_idx);
          $DB->delete('member_badge_t');
        }
      }
    }
  }
}

/**
 * 리뷰 수에 따라 회원 등급을 lover로 자동 승급
 *
 * @param int $mt_idx 회원 ID
 * @param object $DB DB 객체
 */
function syncMemberGrade($mt_idx) {

  global $DB;

  // 1. 회원 현재 등급 확인
  $DB->where('idx', $mt_idx);
  $current_grade = $DB->getValue('member_t', 'mt_grade');

  // 2. 현재 등급이 rookie인 경우만 승급 시도
  if ($current_grade !== 'rookie') return;

  // 3. 리뷰 수 가져오기
  $DB->where('mt_idx', $mt_idx);
  $review_count = $DB->getValue('review_t', 'COUNT(*)');

  if ($review_count >= 100) {
    // 4. lover로 승급
    $DB->where('idx', $mt_idx);
    $DB->update('member_t', [
      'mt_grade' => 'lover'
    ]);
  }
}

/**
 * 회원이 보유할 수 있는 모든 유형의 뱃지를 자동 갱신
   syncAllBadges($mt_idx);
 */
function syncAllBadges($mt_idx) {
  syncReviewBadges($mt_idx);
  syncWineTypeBadges($mt_idx);
  syncWineGrapeBadges($mt_idx);
  syncCountryBadges($mt_idx);
  syncMemberGrade($mt_idx);
}


