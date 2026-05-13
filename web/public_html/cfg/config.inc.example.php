<?php
// ============================================================
// 맛집바로 (barorez) — config.inc.example.php
// ============================================================
// 운영/개발 환경 셋업: 본 파일을 cfg/config.inc.php 로 복사 후
// 모든 'CHANGE_ME_*' placeholder 를 실제 값으로 교체할 것.
// cfg/config.inc.php 는 .gitignore 에 의해 추적 제외 상태.
// ============================================================

define("APP_AUTHOR", "맛집바로");
//상단타이틀, URL 설정
define("APP_TITLE", '맛집바로');

define("APP_DOMAIN", 'https://barorez.com/');
define("CDN_HTTP", 'https://barorez.com/');
define("DESIGN_HTTP", 'https://barorez.com/design');
define("MNG_HTTP", 'https://barorez.com/mng');
define("MARKET_HTTP", 'https://barorez.com/market');
define("KEYWORDS", '맛집바로, 맛집바로');
define("DESCRIPTION", '맛집바로, 맛집바로');
define("ADMIN_NAME", '맛집바로');
define("COMPANY", '맛집바로');
define('AES256_ENC_KEY', 'CHANGE_ME_AES256_ENCRYPTION_KEY_32CHARS');

define('CDN_UTIL_URL',       CDN_HTTP.'app/utils');

define('CDN_ASSET_URL',      CDN_HTTP.'/assets');
define('CDN_IMG_URL',        CDN_ASSET_URL.'/img');
define('CDN_CSS_URL',        CDN_ASSET_URL.'/css');
define('CDN_JS_URL',         CDN_ASSET_URL.'/js');
define('CDN_PLUGIN_URL',         CDN_ASSET_URL.'/plugin');


define("MAIN_LOGO", CDN_IMG_URL.'/logo/logo_red.png');
define("OG_IMAGE", CDN_IMG_URL.'/logo/logo_red.png');


//css, js 캐시 리셋
$v_txt = "20251113_136";
$v_txt = time();
define("DEBUG_JWT", 'CHANGE_ME_DEBUG_JWT');
define("SECRETKEY", 'CHANGE_ME_RSA_PUBLIC_KEY_BASE64');
define("SERVER_NAME", 'API_JW');

//아임포트 테스트모드 완료
//define("IMP_INIAPI_IV", '');
//define("IMP_KEY", '');
//define("IMP_SECRET", '');
//define("IMP_CHANNNEL_KEY", '');
//define("IMP_STORE_ID", 'INIpayTest');

// 포트원 V2 연동 설정
define('PORTONE_API_KEY', 'CHANGE_ME_PORTONE_API_KEY');
define('PORTONE_API_SECRET', 'CHANGE_ME_PORTONE_API_SECRET'); // 웹결제 signkey 값
define('PORTONE_STORE_ID', 'CHANGE_ME_PORTONE_STORE_ID');
define('PORTONE_CHANNEL_KEY', 'CHANGE_ME_PORTONE_CHANNEL_KEY');
//알리고 완료, 문자충전 필요
/*
define("ALIGO_USER_ID", 'CHANGE_ME_ALIGO_USER_ID');
define("ALIGO_KEY", 'CHANGE_ME_ALIGO_KEY');
define("ALIGO_SENDER", 'CHANGE_ME_PHONE');

define("ALIGO_KAKAOAPI_APIKEY", 'CHANGE_ME_ALIGO_KAKAO_APIKEY');
define("ALIGO_KAKAOAPI_SENDERKEY", 'CHANGE_ME_ALIGO_KAKAO_SENDERKEY');
define("ALIGO_KAKAOAPI_USERID", 'CHANGE_ME_ALIGO_KAKAO_USERID');
define("ALIGO_KAKAOAPI_SENDER", 'CHANGE_ME_PHONE');
*/

//카카오 완료
define("KAKAO_NATIVEAPP_KEY", 'CHANGE_ME_KAKAO_NATIVEAPP_KEY');
define("KAKAO_RESTAPI_KEY", 'CHANGE_ME_KAKAO_RESTAPI_KEY');
//define("KAKAO_JAVASCRIPT_KEY", '');
//define("KAKAO_JAVASCRIPT_KEY", '');
define("KAKAO_JAVASCRIPT_KEY", 'CHANGE_ME_KAKAO_JAVASCRIPT_KEY');
define("KAKAO_ADMIN_KEY", 'CHANGE_ME_KAKAO_ADMIN_KEY');
define("KAKAO_CALLBACK_URL", APP_DOMAIN.'api/kakaoOauth.php');
define("KAKAO_CLIENT_SECRET", 'CHANGE_ME_KAKAO_CLIENT_SECRET');

//네이버 완료
define("NAVER_CLIENT_ID", 'CHANGE_ME_NAVER_CLIENT_ID');
define("NAVER_CLIENT_SECRET", 'CHANGE_ME_NAVER_CLIENT_SECRET');
define("NAVER_CALLBACK_URL", APP_DOMAIN.'api/sns_login.php');
define("NAVER_SERVICE_URL", APP_DOMAIN);

//애플 연동
define("APPLE_CLIENT_ID", 'CHANGE_ME_APPLE_CLIENT_ID');
define("APPLE_CLIENT_SECRET", APP_DOMAIN . '/CHANGE_ME_APPLE_AUTH_KEY.p8');
define("APPLE_CALLBACK_URL", APP_DOMAIN.'/sns_login_apple_update.php');

//구글 완료
//API Key : CHANGE_ME_GOOGLE_API_KEY
define("GOOGLE_PROJECT_NAME", 'CHANGE_ME_GOOGLE_PROJECT_NAME');
define("GOOGLE_PROJECT_ID", 'CHANGE_ME_GOOGLE_PROJECT_ID');
define("GOOGLE_PROJECT_NUMBER", 'CHANGE_ME_GOOGLE_PROJECT_NUMBER');
define("GOOGLE_CLIENT_ID", 'CHANGE_ME_GOOGLE_CLIENT_ID.apps.googleusercontent.com');
define("GOOGLE_CLIENT_SECRET", 'CHANGE_ME_GOOGLE_CLIENT_SECRET');
define("GOOGLE_CLIENT_PWD", 'CHANGE_ME_GOOGLE_CLIENT_PWD');
define("GOOGLE_REDIRECT_URI", APP_DOMAIN.'/callback/google_callback.php');

//업데이트됨 220915
define('fcmSendID', 'CHANGE_ME_FCM_SEND_ID');
define('fcmKey', 'CHANGE_ME_FCM_LEGACY_KEY');
define('YOUTUBE_KEY', 'CHANGE_ME_YOUTUBE_API_KEY');

define('DATA_PATH', $_SERVER['DOCUMENT_ROOT'].'/app/data');
define('DATA_URL', CDN_HTTP.'data');
define('DIR_PERMISSION', 0755); // 디렉토리 생성시 퍼미션
define('FILE_PERMISSION', 0644); // 파일 생성시 퍼미션
define('EDITOR_DIR', 'editorimage');

$allim_company = "EJINI";



//게시판 리스팅수
$bt_file_num = 2;
$n_site_limit_num = 9;
$n_limit_num = 10;
$notice_limit_num = 10;
$faq_limit_num = 10;
$qna_limit_num = 10;
$review_limit_num = 10;
$wish_limit_num = 10;
$pt_image_num = 10;
$join_hp_cnt = 3;
$order_limit_num = 10;
$faq_limit_num = 10;
$event_limit_num = 12;
$event_detail_limit_num = 8;
$product_limit_num = 12;
$brand_limit_num = 24;
$wish_limit_num = 10;
$pt_image_num = 10;
$addressbook_limit_num = 5;
$influencer_limit_num = 8;
$my_following_influencer_limit_num = 9;
$product_review_limit_num = 4;
$product_qna_limit_num = 4;

//이미지 업로드 가능 확장자
$ct_image_ext = "jpg;png;gif;jpeg;bmp";

//노프로필이미지 링크
$ct_no_profile_url = CDN_IMG_URL."/img_thumb_100px.png";

//노이미지 링크
$ct_no_img_url = CDN_IMG_URL."/img_none.jpg";
$ct_no_img_x_url = CDN_IMG_URL."/img_no_image2.png";
$ct_no_img_wine_url = CDN_IMG_URL."/img_none_wine.png";
$ct_no_img_bookmark_url = CDN_IMG_URL."/img_none_bookmark.png";

//회원 링크
$member_img_dir = $_SERVER['DOCUMENT_ROOT']."/data/members/";
$member_img_url = CDN_HTTP."/data/members/";

//뱃지 업로드 링크
$badge_img_dir = $_SERVER['DOCUMENT_ROOT']."/data/badge/";
$badge_img_url = CDN_HTTP."/data/badge/";


//seller 업로드 링크
$member_seller_dir = $_SERVER['DOCUMENT_ROOT']."/data/seller/";
$member_seller_url = CDN_HTTP."/data/seller/";

//store 업로드 링크
$member_store_dir = $_SERVER['DOCUMENT_ROOT']."/data/store/";
$member_store_url = CDN_HTTP."/data/store/";

//store 업로드 링크
$member_agency_dir = $_SERVER['DOCUMENT_ROOT']."/data/store/";
$member_agency_url = CDN_HTTP."/data/store/";

//이미지 업로드 링크
$ct_img_dir = $_SERVER['DOCUMENT_ROOT']."/data/uploads/";
$ct_img_url = CDN_HTTP."/data/uploads/";

//brand 업로드 링크
$ct_brand_dir = $_SERVER['DOCUMENT_ROOT']."/data/brand/";
$ct_brand_url = CDN_HTTP."/data/brand/";

//popup
$ct_popup_dir = $_SERVER['DOCUMENT_ROOT']."/data/popup/";
$ct_popup_url = CDN_HTTP."/data/popup/";

//banner
$ct_banner_dir = $_SERVER['DOCUMENT_ROOT']."/data/banner/";
$ct_banner_url = CDN_HTTP."/data/banner/";

//catetory 업로드 링크
$ct_category_dir = $_SERVER['DOCUMENT_ROOT']."/data/category/";
$ct_category_url = CDN_HTTP."/data/category/";

//product 업로드 링크
$ct_product_dir = $_SERVER['DOCUMENT_ROOT']."/data/product/";
$ct_product_url = CDN_HTTP."/data/product/";

//chat 업로드 링크
$ct_chat_dir = $_SERVER['DOCUMENT_ROOT']."/data/chat/";
$ct_chat_url = CDN_HTTP."/data/chat/";

//pdf 업로드 링크
$ct_pdf_dir = $_SERVER['DOCUMENT_ROOT']."/data/pdf/";
$ct_pdf_url = CDN_HTTP."/data/pdf/";

//review 업로드 링크
$ct_review_dir = $_SERVER['DOCUMENT_ROOT']."/data/review/";
$ct_review_url = CDN_HTTP."/data/review/";

//qa 업로드 링크
$ct_qa_dir = $_SERVER['DOCUMENT_ROOT']."/data/qa/";
$ct_qa_url = CDN_HTTP."/data/qa/";

//event 업로드 링크
$ct_event_dir = $_SERVER['DOCUMENT_ROOT']."/data/event/";
$ct_event_url = CDN_HTTP."/data/event/";

//mainbanner 업로드 링크
$ct_mainbanner_dir = $_SERVER['DOCUMENT_ROOT']."/data/mainbanner/";
$ct_mainbanner_url = CDN_HTTP."/data/mainbanner/";

//landing 업로드 링크
$ct_landing_dir = $_SERVER['DOCUMENT_ROOT']."/data/landing/";
$ct_landing_url = CDN_HTTP."/data/landing/";

//excel 업로드 링크
$ct_excel_dir = $_SERVER['DOCUMENT_ROOT']."/data/excel/";
$ct_excel_url = CDN_HTTP."/data/excel/";

//excel 업로드 링크
$ct_notice_dir = $_SERVER['DOCUMENT_ROOT']."/data/notice/";
$ct_notice_url = CDN_HTTP."/data/notice/";

$ct_faq_dir = $_SERVER['DOCUMENT_ROOT']."/data/faq/";
$ct_faq_url = CDN_HTTP."/data/faq/";


//excel 업로드 링크
$ct_upjong_dir = $_SERVER['DOCUMENT_ROOT']."/data/upjong/";
$ct_upjong_url = CDN_HTTP."/data/upjong/";

//카테고리 아이콘 업로드
$category_dir = $_SERVER['DOCUMENT_ROOT']."/data/manufacturers/";
$category_url = CDN_HTTP."/data/manufacturers/";


// 골프회원권 업로드 링크
$ct_golf_membership_dir = $_SERVER['DOCUMENT_ROOT']."/data/golf_membership/";
$ct_golf_membership_url = CDN_HTTP."/data/golf_membership/";

// 회원권 업로드 링크
$ct_certi_membership_dir = $_SERVER['DOCUMENT_ROOT']."/data/certimembership/";
$ct_certi_membership_url = CDN_HTTP."/data/certimembership/";

// 회원권판매  업로드 링크
$ct_sell_membership_dir = $_SERVER['DOCUMENT_ROOT']."/data/sellmembership/";
$ct_sell_membership_url = CDN_HTTP."/data/sellmembership/";


//와인 업로드 링크
$wine_product_img_dir = $_SERVER['DOCUMENT_ROOT']."/data/wineproduct/";
$wine_product_img_url = CDN_HTTP."/data/wineproduct/";

//와인 빈티지 링크
$wine_vintage_img_dir = $_SERVER['DOCUMENT_ROOT']."/data/winevintage/";
$wine_vintage_img_url = CDN_HTTP."/data/winevintage/";

//와인 지역 링크
$wine_region_img_dir = $_SERVER['DOCUMENT_ROOT']."/data/wineregion/";
$wine_region_img_url = CDN_HTTP."/data/wineregion/";

//와인 와이너리 링크
$wine_winery_img_dir = $_SERVER['DOCUMENT_ROOT']."/data/winewinery/";
$wine_winery_img_url = CDN_HTTP."/data/winewinery/";


//와인품종 업로드 링크
$wine_type_img_dir = $_SERVER['DOCUMENT_ROOT']."/data/winetype/";
$wine_type_img_url = CDN_HTTP."/data/winetype/";

//와인국가 업로드 링크
$wine_country_img_dir = $_SERVER['DOCUMENT_ROOT']."/data/winecountry/";
$wine_country_img_url = CDN_HTTP."/data/winecountry/";

//와인맛 업로드 링크
$wine_taste_img_dir = $_SERVER['DOCUMENT_ROOT']."/data/winetaste/";
$wine_taste_img_url = CDN_HTTP."/data/winetaste/";

//와인페어링 업로드 링크
$wine_pairing_img_dir = $_SERVER['DOCUMENT_ROOT']."/data/winepairing/";
$wine_pairing_img_url = CDN_HTTP."/data/winepairing/";

//와인품종 업로드 링크
$wine_variety_img_dir = $_SERVER['DOCUMENT_ROOT']."/data/winevariety/";
$wine_variety_img_url = CDN_HTTP."/data/winevariety/";

//와인색상 업로드 링크
$wine_color_img_dir = $_SERVER['DOCUMENT_ROOT']."/data/winecolor/";
$wine_color_img_url = CDN_HTTP."/data/winecolor/";

//와인향/맛 업로드 링크
$wine_flavor_img_dir = $_SERVER['DOCUMENT_ROOT']."/data/wineflavor/";
$wine_flavor_img_url = CDN_HTTP."/data/wineflavor/";

//와인셀러 업로드 링크
$device_img_dir = $_SERVER['DOCUMENT_ROOT']."/data/device/";
$device_img_url = CDN_HTTP."/data/device/";


//폴더 환경
define("ROOT_PATH", '/mng');
define("ROOT_DIR", $_SERVER['DOCUMENT_ROOT'].'/mng');
define("ROOT_URL", CDN_HTTP.'/mng');


/********************
시간 상수
 ********************/
// 서버의 시간과 실제 사용하는 시간이 틀린 경우 수정하세요.
// 하루는 86400 초입니다. 1시간은 3600초
// 6시간이 빠른 경우 time() + (3600 * 6);
// 6시간이 느린 경우 time() - (3600 * 6);
define('SERVER_TIME', time());
define('TIME_YMDHIS', date('Y-m-d H:i:s', SERVER_TIME));
define('TIME_YMD', substr(TIME_YMDHIS, 0, 10));
define('TIME_HIS', substr(TIME_YMDHIS, 11, 8));


/********************
 * Actions 경로
 ********************/
define("APP_ACTIONS", APP_DOMAIN . "/app/actions");
define("AUTH_ACTIONS", APP_ACTIONS . "/auth");
define("MY_ACTIONS", APP_ACTIONS . "/my");
define("NOTICE_ACTIONS", APP_ACTIONS . "/notice");
define("ORDER_ACTIONS", APP_ACTIONS . "/order");
define("STORE_ACTIONS", APP_ACTIONS . "/store");
define("MAP_ACTIONS", APP_ACTIONS . "/map");
define("RSRV_ACTIONS", APP_ACTIONS . "/rsrv");
define("BBS_ACTIONS", APP_ACTIONS . "/bbs");
define("CASH_ACTIONS", APP_ACTIONS . "/cash");
define("WINE_ACTIONS", APP_ACTIONS . "/wine");
define("CELLAR_ACTIONS", APP_ACTIONS . "/cellar");
define("SHOP_ACTIONS", APP_ACTIONS . "/shop");
define("ALARM_ACTIONS", APP_ACTIONS . "/alarm");
define("USER_ACTIONS", APP_ACTIONS . "/user");
define("DEVICE_ACTIONS", APP_ACTIONS . "/device");
define("REVIEW_ACTIONS", APP_ACTIONS . "/review");

/********************
 * Views 경로
 ********************/
define('VIEWS_PATH', $_SERVER['DOCUMENT_ROOT'].'/app/views');
define("VIEWS_MAIN_PATH", VIEWS_PATH . "/main");
define("VIEWS_AUTH_PATH", VIEWS_PATH . "/auth");
define("VIEWS_MY_PATH", VIEWS_PATH . "/my");
define("VIEWS_NOTICE_PATH", VIEWS_PATH . "/notice");
define("VIEWS_QA_PATH", VIEWS_PATH . "/qa");
define("VIEWS_EVENT_PATH", VIEWS_PATH . "/event");
define("VIEWS_POLICY_PATH", VIEWS_PATH . "/policy");
define("VIEWS_SHOP_PATH", VIEWS_PATH . "/shop");
define("VIEWS_ORDER_PATH", VIEWS_PATH . "/order");
define("VIEWS_MAP_PATH", VIEWS_PATH . "/map");
define("VIEWS_RSRV_PATH", VIEWS_PATH . "/rsrv");
define("VIEWS_COUPON_PATH", VIEWS_PATH . "/coupon");
define("VIEWS_CUSTOMER_PATH", VIEWS_PATH . "/customer");
define("VIEWS_TERM_PATH", VIEWS_PATH . "/term");
define("VIEWS_ALARM_PATH", VIEWS_PATH . "/alarm");
define("VIEWS_USER_PATH", VIEWS_PATH . "/user");
define("VIEWS_DEVICE_PATH", VIEWS_PATH . "/device");
define("VIEWS_LANDING_PATH", VIEWS_PATH . "/landing");
define("VIEWS_REVIEW_PATH", VIEWS_PATH . "/review");
define("VIEWS_SEARCH_PATH", VIEWS_PATH . "/search");

/********************
 * 페이지 경로
 ********************/
define('APP_PAGE', CDN_HTTP.'app');
define('AUTH_PAGE', APP_PAGE.'/auth');
define('MAP_PAGE', APP_PAGE.'/map');
define('ORDER_PAGE', APP_PAGE.'/order');
define('SHOP_PAGE', APP_PAGE.'/shop');
define('MY_PAGE', APP_PAGE.'/my');
define('REVIEW_PAGE', APP_PAGE.'/review');

define('CALLBACK_PAGE', APP_PAGE.'/callback');

/********************
 * 영수증 자동 출력 시스템
 ********************/
// HMAC 공유 시크릿 — 64자 hex 권장. 운영 환경에서 직접 채워 넣을 것.
//   생성: php -r "echo bin2hex(random_bytes(32)).PHP_EOL;"
//   또는: openssl rand -hex 32
define('PRINT_SHARED_SECRET', '');

// Server C 내부 엔드포인트 (PHP → Server C webhook 발송용, Phase 2에서 실제 사용)
define('PRINT_SERVER_URL', 'http://127.0.0.1:3000');

// HMAC 서명 헤더명
define('PRINT_HMAC_HEADER_NAME', 'X-Signature');
