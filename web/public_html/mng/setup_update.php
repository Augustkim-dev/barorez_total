<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";
include_once $_SERVER['DOCUMENT_ROOT']."/cfg/config.mng.inc.php";

if($_POST['act']=="update") {

    $arr_query = array(
        ...(isset($_POST['st_agree1']) ? ["st_agree1" => $_POST['st_agree1']] : []),
        ...(isset($_POST['st_agree2']) ? ["st_agree2" => $_POST['st_agree2']] : []),
        ...(isset($_POST['st_agree3']) ? ["st_agree3" => $_POST['st_agree3']] : []),
        ...(isset($_POST['st_agree4']) ? ["st_agree4" => $_POST['st_agree4']] : []),
        ...(isset($_POST['st_agree5']) ? ["st_agree5" => $_POST['st_agree5']] : []),
        ...(isset($_POST['st_agree6']) ? ["st_agree6" => $_POST['st_agree6']] : []),
        ...(isset($_POST['st_agree7']) ? ["st_agree7" => $_POST['st_agree7']] : []),
        ...(isset($_POST['st_agree8']) ? ["st_agree8" => $_POST['st_agree8']] : []),
        ...(isset($_POST['st_agree9']) ? ["st_agree9" => $_POST['st_agree9']] : []),
        ...(isset($_POST['st_agree10']) ? ["st_agree10" => $_POST['st_agree10']] : []),
        ...(isset($_POST['st_agree11']) ? ["st_agree11" => $_POST['st_agree11']] : []),
        ...(isset($_POST['st_commission1']) ? ["st_commission1" => $_POST['st_commission1']] : []),
        ...(isset($_POST['st_commission2']) ? ["st_commission2" => $_POST['st_commission2']] : []),
        ...(isset($_POST['st_commission3']) ? ["st_commission3" => $_POST['st_commission3']] : []),
        ...(isset($_POST['st_commission4']) ? ["st_commission4" => $_POST['st_commission4']] : []),
        ...(isset($_POST['st_commission5']) ? ["st_commission5" => $_POST['st_commission5']] : []),
        ...(isset($_POST['st_commission6']) ? ["st_commission6" => $_POST['st_commission6']] : []),
        ...(isset($_POST['st_logis']) ? ["st_logis" => $_POST['st_logis']] : []),
        ...(isset($_POST['st_customer_tel']) ? ["st_customer_tel" => $_POST['st_customer_tel']] : []),
        ...(isset($_POST['st_customer_time']) ? ["st_customer_time" => $_POST['st_customer_time']] : []),
        ...(isset($_POST['st_company_zipcode']) ? ["st_company_zipcode" => $_POST['st_company_zipcode']] : []),
        ...(isset($_POST['st_company_add']) ? ["st_company_add" => $_POST['st_company_add']] : []),
        ...(isset($_POST['st_company_num1']) ? ["st_company_num1" => $_POST['st_company_num1']] : []),
        ...(isset($_POST['st_company_boss']) ? ["st_company_boss" => $_POST['st_company_boss']] : []),
        ...(isset($_POST['st_company_num2']) ? ["st_company_num2" => $_POST['st_company_num2']] : []),
        ...(isset($_POST['st_privacy_admin']) ? ["st_privacy_admin" => $_POST['st_privacy_admin']] : []),
        ...(isset($_POST['st_company_name']) ? ["st_company_name" => $_POST['st_company_name']] : []),
        ...(isset($_POST['st_customer_email']) ? ["st_customer_email" => $_POST['st_customer_email']] : []),
        ...(isset($_POST['st_sns_link1']) ? ["st_sns_link1" => $_POST['st_sns_link1']] : []),
        ...(isset($_POST['st_sns_link2']) ? ["st_sns_link2" => $_POST['st_sns_link2']] : []),
        ...(isset($_POST['st_sns_link3']) ? ["st_sns_link3" => $_POST['st_sns_link3']] : []),
        ...(isset($_POST['st_sns_link4']) ? ["st_sns_link4" => $_POST['st_sns_link4']] : []),
        ...(isset($_POST['st_sns_link5']) ? ["st_sns_link5" => $_POST['st_sns_link5']] : []),
        ...(isset($_POST['st_sns_link6']) ? ["st_sns_link6" => $_POST['st_sns_link6']] : []),
        ...(isset($_POST['st_sns_link7']) ? ["st_sns_link7" => $_POST['st_sns_link7']] : []),
        ...(isset($_POST['st_google_link']) ? ["st_google_link" => $_POST['st_google_link']] : []),
        ...(isset($_POST['st_apple_link']) ? ["st_apple_link" => $_POST['st_apple_link']] : []),
        ...(isset($_POST['st_sweettrack_key']) ? ["st_sweettrack_key" => $_POST['st_sweettrack_key']] : []),
        ...(isset($_POST['st_sweettrack_date']) ? ["st_sweettrack_date" => $_POST['st_sweettrack_date']] : []),
        ...(isset($_POST['st_purchase_cdate']) ? ["st_purchase_cdate" => $_POST['st_purchase_cdate']] : []),
        ...(isset($_POST['st_purchase_rdate']) ? ["st_purchase_rdate" => $_POST['st_purchase_rdate']] : []),
        ...(isset($_POST['st_coupon_use']) ? ["st_coupon_use" => $_POST['st_coupon_use']] : []),
        ...(isset($_POST['st_coupon_price']) ? ["st_coupon_price" => $_POST['st_coupon_price']] : []),
        ...(isset($_POST['st_coupon_minimum']) ? ["st_coupon_minimum" => $_POST['st_coupon_minimum']] : []),
        ...(isset($_POST['st_coupon_term']) ? ["st_coupon_term" => $_POST['st_coupon_term']] : []),
        ...(isset($_POST['st_grade_coupon_use']) ? ["st_grade_coupon_use" => $_POST['st_grade_coupon_use']] : []),
        ...(isset($_POST['st_grade_coupon_price']) ? ["st_grade_coupon_price" => $_POST['st_grade_coupon_price']] : []),
        ...(isset($_POST['st_grade_coupon_minimum']) ? ["st_grade_coupon_minimum" => $_POST['st_grade_coupon_minimum']] : []),
        ...(isset($_POST['st_grade_coupon_term']) ? ["st_grade_coupon_term" => $_POST['st_grade_coupon_term']] : []),
        ...(isset($_POST['st_point_join']) ? ["st_point_join" => $_POST['st_point_join']] : []),
        ...(isset($_POST['st_point_od_confirm']) ? ["st_point_od_confirm" => $_POST['st_point_od_confirm']] : []),
        ...(isset($_POST['st_point_od_confirm_chk']) ? ["st_point_od_confirm_chk" => $_POST['st_point_od_confirm_chk']] : []),
        ...(isset($_POST['st_point_review_text']) ? ["st_point_review_text" => $_POST['st_point_review_text']] : []),
        ...(isset($_POST['st_point_review_photo']) ? ["st_point_review_photo" => $_POST['st_point_review_photo']] : []),
        ...(isset($_POST['st_meta_title']) ? ["st_meta_title" => $_POST['st_meta_title']] : []),
        ...(isset($_POST['st_meta_author']) ? ["st_meta_author" => $_POST['st_meta_author']] : []),
        ...(isset($_POST['st_meta_description']) ? ["st_meta_description" => $_POST['st_meta_description']] : []),
        ...(isset($_POST['st_meta_keywords']) ? ["st_meta_keywords" => $_POST['st_meta_keywords']] : []),
        ...(isset($_POST['st_add_meta']) ? ["st_add_meta" => $_POST['st_add_meta']] : []),
        ...(isset($_POST['st_id_filter']) ? ["st_id_filter" => $_POST['st_id_filter']] : []),
        ...(isset($_POST['st_filter']) ? ["st_filter" => $_POST['st_filter']] : []),
        ...(isset($_POST['st_possible_ip']) ? ["st_possible_ip" => $_POST['st_possible_ip']] : []),
        ...(isset($_POST['st_intercept_ip']) ? ["st_intercept_ip" => $_POST['st_intercept_ip']] : []),
        ...(isset($_POST['st_analytics']) ? ["st_analytics" => $_POST['st_analytics']] : []),
        ...(isset($_POST['st_bank_name']) ? ["st_bank_name" => $_POST['st_bank_name']] : []),
        ...(isset($_POST['st_bank_num']) ? ["st_bank_num" => $_POST['st_bank_num']] : []),
        ...(isset($_POST['st_bank_user']) ? ["st_bank_user" => $_POST['st_bank_user']] : []),
        ...(isset($_POST['st_aos_version']) ? ["st_aos_version" => $_POST['st_aos_version']] : []),
        ...(isset($_POST['st_aos_update']) ? ["st_aos_update" => $_POST['st_aos_update']] : []),
        ...(isset($_POST['st_ios_version']) ? ["st_ios_version" => $_POST['st_ios_version']] : []),
        ...(isset($_POST['st_ios_update']) ? ["st_ios_update" => $_POST['st_ios_update']] : []),
    );


    $DB->where('idx', 1);
    $DB->update('setup_t', $arr_query);

    p_alert("수정되었습니다.");
}
elseif($_POST['act']=="config") {





  try {
    $DB->startTransaction();


    $arr_query = array(
      ...(isset($_POST['st_portone']) ? ["st_portone" => $_POST['st_portone']] : []),
    );

    if (!empty($arr_query)) {
      $DB->where('idx', 1);
      if (!$DB->update('setup_t', $arr_query)) {
        throw new Exception("포트원 결제모드 업데이트 실패");
      }
    }

    unset($_POST['act']);
    unset($_POST['st_portone']);
    $arr_query = [];
    foreach ($_POST as $category => $configs) {
      foreach ($configs as $key => $value) {
        $DB->where('category', $category);
        $DB->where('config_key', $key);
        $arr_query=[];
        $arr_query['config_value'] = $value;
        if (!$DB->update('setup_config_t', $arr_query)) {
          throw new Exception("$category::$key 정보 업데이트 실패");
        }
      }
    }

    $DB->commit();
    p_alert("저장 되었습니다.");

  } catch (Exception $e) {
    $DB->rollback();
    //echo json_encode([
    //  'success' => false,
    //  'message' => $e->getMessage()
    //]);
    p_alert($e->getMessage());
  }

}

include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
