<?php

// global $CFG_TBL;
$CFG_TBL = [];

// 사용중

// 멤버
$CFG_TBL['member']['default']        = 'member_t';

// 매장
$CFG_TBL['shop']['default']        = 'shop_t';

// 메뉴
$CFG_TBL['menu']['default']         = 'shop_menu_t';
$CFG_TBL['menu']['category']        = 'shop_category_t';

// 메뉴 옵션
$CFG_TBL['option']['default']        = 'option_menu_t';
$CFG_TBL['option']['category']        = 'menu_option_category_t';

// 쿠폰
$CFG_TBL['coupon']['default']        = 'coupon_t';
$CFG_TBL['coupon']['log']        = 'coupon_log_t';

// 장바구니
$CFG_TBL['cart']['log']        = 'cart_t';
$CFG_TBL['cart']['option']        = 'cart_option_t';

// 결제
$CFG_TBL['orders']['default']        = 'orders_t';

// 정산
$CFG_TBL['settle']['default']       = 'settle_t';

// 공지
$CFG_TBL['notice']['default']        = 'notice_t';

// 1:1 문의
$CFG_TBL['qa']['default']        = 'qa_t';

// 영수증 자동 출력
$CFG_TBL['print']['client']        = 'print_client_t';
$CFG_TBL['print']['route_rule']    = 'print_route_rule_t';
$CFG_TBL['print']['job']           = 'print_job_t';



