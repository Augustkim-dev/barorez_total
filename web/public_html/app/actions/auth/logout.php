<?php
include $_SERVER['DOCUMENT_ROOT']."/cfg/lib.inc.php";

header('Content-Type: application/json');

$token = $_POST['token'] ?? $_SESSION['app_token'];
if($_POST['act']=="logout") {
    try {
        $DB->where('mt_app_token',$token);
        $DB->update('member_t', [
            'mt_app_token' => ''
        ]);

        if (!empty($_SESSION['mng'])) {
            unset($_SESSION['mng']);
            unset($_SESSION['cart_ct_ids']);
            unset($_SESSION['cart_store_ids']);
            unset($_SESSION['cart_qty']);
        }

        if($_SESSION['qr_token']){
            $redirect =  APP_PAGE;
        }else{
            $redirect = MAP_PAGE;
        }

        $json = [
            'success'  => true,
            'message'  => '로그아웃되었습니다.',
//            'message'  => $DB->getLastQuery(),
            'redirect' => $redirect
        ];
        echo json_encode($json, JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        $DB->rollback();
        die(json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE));
    }
}

?>
