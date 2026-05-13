<?php

$default_lang = 'en';

$member_locale_idx  = $_SESSION['user']['mt_language'] ?? null;
$user_lang          = $_SESSION['user_lang'] ?? null;

// locale_t에서 사용 가능한 언어 목록 불러오기
$locale_rows = $DB->rawQuery("SELECT idx, lang, locale, is_default FROM locale_t WHERE w_show = 'Y' ORDER BY w_order ASC");

$lang_by_idx = [];      // idx → lang
$lang_list = [];        // 허용된 lang 목록
$default_lang_from_db = null;

foreach ($locale_rows as $row) {
  $lang_by_idx[$row['idx']] = $row['lang'];
  $lang_list[] = $row['lang'];

  if ($row['is_default'] === 'Y') {
    $default_lang_from_db = $row['lang']; // 이걸 default로 써야지
  }
}

$default_lang = $default_lang_from_db ?? $default_lang;

// 언어 결정
$resolved_lang = $default_lang;


include_once $_SERVER['DOCUMENT_ROOT'] . "/cfg/locale.{$resolved_lang}.php";



/*
  getValidationMessage('required', '비밀번호');
  getValidationMessage('same', '비밀번호 확인', ['other' => '비밀번호']);
  getValidationMessage('size.range', '비밀번호', [0 => 8, 1 => 20]);
  getValidationMessage('regex.password', '비밀번호');
 */
function getValidationMessage($key, $attribute = '', $replacements = []) {
  global $CFG_LANG;

  $msg = $CFG_LANG['validation'];

  // dot notation 탐색 (예: size.min, regex.password)
  foreach (explode('.', $key) as $k) {
    if (isset($msg[$k])) {
      $msg = $msg[$k];
    } else {
      return ''; // 메시지가 존재하지 않으면 빈 문자열 반환
    }
  }

  if (!is_string($msg)) return '';

  // 기본 치환: :attribute → 실제 필드명
  $msg = str_replace(':attribute', $attribute, $msg);

  // 동적 치환 (예: :min, :max, :other, {0}, {1}, {$key} 등 모두 대응)
  foreach ($replacements as $k => $v) {
    $msg = str_replace([":$k", "{$k}", "{{{$k}}}", "{{$k}}"], $v, $msg);
    if (is_numeric($k)) {
      // 숫자 인덱스인 경우도 따로 치환 처리 (예: {0}, {1})
      $msg = str_replace(["{{$k}}"], $v, $msg);
    }
  }

  return $msg;
}


/*
 * $CFG_LANG['user']['greeting'] = '안녕하세요, {name}님! 오늘은 {day}입니다.';
 * echo sfLang($CFG_LANG['user']['greeting'], ['name' => '철수','day' => '목요일']);
 */
function sfLang($text, $replacements = []) {

  if (!is_string($text)) return '';

  foreach ($replacements as $k => $v) {
    $text = str_replace("{" . $k . "}", $v, $text);
  }

  return $text;
}