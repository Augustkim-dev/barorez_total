<?php
ob_start('ob_gzhandler');

$uri = $_SERVER['REQUEST_URI'];
$first_segment = explode('/', trim($uri, '/'))[0];
$is_admin = ($first_segment === 'mng');
if ($is_admin) {
  header("Pragma: no-cache");
  header("Cache-Control: no-cache");
} else {
  // 사용자 페이지 - 강력한 캐시 차단
  header("Pragma: no-cache");
  header("Cache-Control: no-cache, no-store, must-revalidate");
  header("Expires: 0");
}
header("Content-Type: text/html; charset=utf-8");
header("Access-Control-Allow-Origin: *");

//$isHttps =
//    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
//    (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
//
//ini_set('session.gc_maxlifetime', 86400);
//ini_set('session.use_trans_sid', 0);
//ini_set('session.use_cookies', 1);
//ini_set('session.use_only_cookies', 1);
//ini_set('session.name', 'barorez');
//
//session_save_path('/var/lib/php/session/barorez');
//
//session_set_cookie_params([
//    'lifetime' => 0,
//    'path' => '/',
//    'domain' => 'barorez.com',
//    'secure' => $isHttps,
//    'httponly' => true,
//    'samesite' => 'Lax',
//]);

if (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https') {
    $_SERVER['HTTPS'] = 'on';
}

$isHttps =
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');

ini_set('session.cookie_domain', '');

session_name('barorez');
session_save_path($_SERVER['DOCUMENT_ROOT'].'/sessions');

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => $isHttps,
    'httponly' => true,
    'samesite' => 'Lax',
]);
$sessionDebugBefore = [
    'php_version' => PHP_VERSION,
    'session_status_before' => session_status(),
    'session_auto_start' => ini_get('session.auto_start'),
    'auto_prepend_file' => ini_get('auto_prepend_file'),
    'cookie_params_before' => session_get_cookie_params(),
];

$setCookieParamsResult = session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => $isHttps,
    'httponly' => true,
    'samesite' => 'Lax',
]);

$sessionDebugAfter = [
    'set_cookie_params_result' => $setCookieParamsResult,
    'cookie_params_after' => session_get_cookie_params(),
];
session_start();

//ini_set('session.cache_expire', 86400);
//ini_set('session.gc_maxlifetime', 86400);
//ini_set('session.use_trans_sid', 0);// PHPSESSID를 자동으로 넘기지 않음
//ini_set('url_rewriter.tags', '');   // 링크에 PHPSESSID가 따라다니는것을 무력화함
//ini_set("session.gc_probability", 1);
//ini_set("session.gc_divisor", 100);
//ini_set("session.name", 'barorez'); // 세션명
////ini_set("session.cookie_domain", "barorez.com");
//
////redis 세션 사용
////ini_set('session.save_handler', 'redis');
////ini_set('session.save_path', 'tcp://127.0.0.1:6379');
//
////파일 세션 사용
//session_save_path($_SERVER['DOCUMENT_ROOT'].'/sessions');
//
//session_cache_limiter('nocache, must_revalidate');
//session_set_cookie_params([
//    'lifetime' => 0,
//    'path' => '/',
//    'secure' => $isHttps,
//    'httponly' => true,
//    'samesite' => 'Lax',
//]);
//session_start();

// 보안설정이나 프레임이 달라도 쿠키가 통하도록 설정
header('P3P: CP="ALL CURa ADMa DEVa TAIa OUR BUS IND PHY ONL UNI PUR FIN COM NAV INT DEM CNT STA POL HEA PRE LOC OTC"');

error_reporting(E_ERROR);
ini_set('display_errors', '1');

if (function_exists('opcache_reset')) {
    opcache_reset();
}

include $_SERVER['DOCUMENT_ROOT']."/cfg/table.inc.php";
include $_SERVER['DOCUMENT_ROOT']."/cfg/db.inc.php";
include $_SERVER['DOCUMENT_ROOT']."/cfg/config.inc.php";
include $_SERVER['DOCUMENT_ROOT']."/cfg/config.arr.inc.php";
include $_SERVER['DOCUMENT_ROOT']."/cfg/mail.inc.php";
include $_SERVER['DOCUMENT_ROOT']."/cfg/MobileDetect.inc.php";
include $_SERVER['DOCUMENT_ROOT']."/cfg/coupon.inc.php";
include $_SERVER['DOCUMENT_ROOT']."/cfg/follow.inc.php";
include $_SERVER['DOCUMENT_ROOT']."/cfg/comment.inc.php";
include $_SERVER['DOCUMENT_ROOT']."/cfg/golf.inc.php";
include $_SERVER['DOCUMENT_ROOT']."/cfg/wine.inc.php";
include $_SERVER['DOCUMENT_ROOT']."/cfg/badge.inc.php";
include $_SERVER['DOCUMENT_ROOT']."/cfg/point.inc.php";
include $_SERVER['DOCUMENT_ROOT']."/cfg/push.inc.php";
//include $_SERVER['DOCUMENT_ROOT']."/cfg/htmlpurify.inc.php";
include $_SERVER['DOCUMENT_ROOT']."/cfg/visit.inc.php";
include $_SERVER['DOCUMENT_ROOT']."/cfg/lang.inc.php";



//echo "Wait..";
//exit;

$detect_mobile = new \Detection\MobileDetect();
if ($detect_mobile->isMobile()) {
    $chk_mobile = true;
} else {
    $chk_mobile = false;
}

if ($_SERVER['REMOTE_ADDR'] == '115.93.39.5') {
    error_reporting(E_ERROR);
    ini_set('display_errors', '1');
    //$chk_admin = true;
}

if (isset($_SESSION['mng']['mt_level']) && ($_SESSION['mng']['mt_level'] == '9' || $_SESSION['mng']['mt_level'] == '10')) {
  $chk_admin = true;
} else {
  $chk_admin = false;
}

$_query_array = [];
$_query_str = '';
$_raw_query_string = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
parse_str($_raw_query_string, $_query_array);
$_query_str = http_build_query($_query_array);

$setup_info = get_setup_t_info();

// 백엔드 전용
function alert_backend_safe($msg, $url = "", $ttl = "") {
  include $_SERVER['DOCUMENT_ROOT'] . '/views/jalert/backend.php';
  exit;
}


function alert($msg, $url="", $ttl="")
{
    if ($msg != "") {
        echo "<script type=\"text/javascript\">
        jalert_url('".$msg."', '".$url."', '".$ttl."');
        </script>";
    } else {
        echo "<script type=\"text/javascript\">
        ".$url.";
        </script>";
    }
    exit;
}

function alert_b($msg, $url="")
{
    if ($url == "") {
        $url = "history.go(-1)";
    } else {
        $url = "document.location.href = '".$url."'";
    }

    if ($msg != "") {
        echo "<script type=\"text/javascript\">
            jalert('".$msg."');".$url.";
            </script>";
    } else {
        echo "<script type=\"text/javascript\">
        ".$url.";
        </script>";
    }
    exit;
}

// 유비
function page_replace($msg, $page){
    if ($msg != "") {
        echo "<script type=\"text/javascript\">
            jalert('".$msg."');
            document.location.replace('".$page."');
        </script>";
    } else {
        echo "<script type=\"text/javascript\">
            document.location.replace('".$page."');
        </script>";
    }
}

function just_alert($msg)
{
    echo "<script type=\"text/javascript\">
        alert('".$msg."');
        </script>";
}

function p_alert($msg, $url="", $ttl="")
{
    if ($msg != "") {
        echo "<script type=\"text/javascript\">
        parent.jalert_url('".$msg."', '".$url."', '".$ttl."');
        </script>";
    } else {
        echo "<script type=\"text/javascript\">
        ".$url.";
        </script>";
    }
    exit;
}

function p_confirm($msg, $url1, $url2)
{
    echo "<script type=\"text/javascript\">
    if(confirm('".$msg."')) {
        parent.document.location.href = '".$url1."';
    } else {
        parent.document.location.href = '".$url2."';
    }
    </script>";
    exit;
}

function p_reload_to($url="")
{
    if ($url == "") {
        $url = "parent.location.reload()";
    } else {
        $url = "parent.document.location.href = '".$url."'";
    }

    echo "<script type=\"text/javascript\">
    ".$url.";
    </script>";
    exit;
}

function gotourl($url)
{
    $url = "document.location.href = '".$url."'";
    echo "<script type=\"text/javascript\">
    ".$url.";
    </script>";
    exit;
}

function top_location_url($url)
{
    $url = "top.location.href = '".$url."'";
    echo "<script type=\"text/javascript\">
    ".$url.";
    </script>";
    exit;
}

function p_replace_gotourl($url)
{
    echo "<script type=\"text/javascript\">
    parent.document.location.replace('".$url."');
    </script>";
    exit;
}
function p_gotourl($url)
{
    $url = "parent.document.location.href = '".$url."'";
    echo "<script type=\"text/javascript\">
    ".$url.";
    </script>";
    exit;
}
function p_gotourl_post_msg($url, $type, $data = array())
{
    global $isWebView;
    $url = "parent.document.location.href = '".$url."'";
    echo "<script type=\"text/javascript\">";
    if ($isWebView) {
        echo "Meatrider.postMessage(JSON.stringify({method: '{$type}', param: ".json_encode($data).",}));";
        echo $url;
    } else {
        echo $url;
    }
    echo "</script>";
    exit;
}

function ps_gotourl($url)
{
    $url = "opener.document.location.href = '".$url."'";
    echo "<script type=\"text/javascript\">
    ".$url.";
    </script>";
    exit;
}

function page_listing_xhr($cur_page, $total_page, $func_name, $frm_name='')
{
    $retValue = '<ul class="page-light pagination justify-content-center mt-4 mb-2">';
    if ($cur_page > 1) {

        $retValue .= '<li class="page-item" onclick="'.$func_name.'(1, \''.$frm_name.'\')"> <a class="page-link" href="javascript:;" aria-label="처음"><span aria-hidden="true" class="fa fa-angle-double-left"></span></a></li>';
        $retValue .= '<li class="page-item" onclick="'.$func_name.'(\''.($cur_page-1).'\', \''.$frm_name.'\')"> <a class="page-link" href="javascript:;" aria-label="이전"><span aria-hidden="true" class="fa fa-angle-left"></span></a></li>';
    } else {
        $retValue .= '<li class="page-item disabled"><a class="page-link" href="javascript:;" tabindex="-1" aria-disabled="true"><span aria-hidden="true" class="fa fa-angle-left"></span></a></li>';
    }
    $start_page = (((int)(($cur_page - 1) / 5)) * 5) + 1;
    $end_page = $start_page + 5;
    if ($end_page >= $total_page) {
        $end_page = $total_page;
    }
    if ($total_page > 1) {
        for ($k=$start_page;$k<=$end_page;$k++) {
            if ($cur_page != $k) {
                $retValue .= '<li class="page-item" onclick="'.$func_name.'(\''.$k.'\', \''.$frm_name.'\')"><a class="page-link" href="javascript:;">'.$k.'</a></li>';
            } else {
                $retValue .= '<li class="page-item active" aria-current="page" onclick="'.$func_name.'(\''.$k.'\', \''.$frm_name.'\')"> <a class="page-link" href="javascript:;">'.$k.'</a></li>';
            }
        }
    }

    if ($cur_page < $total_page && $total_page > 1) {
        $retValue .= '<li class="page-item" onclick="'.$func_name.'(\''.($cur_page+1).'\', \''.$frm_name.'\')"> <a class="page-link" href="javascript:;" aria-label="다음"><span aria-hidden="true" class="fa fa-angle-right"></span></a></li>';
        $retValue .= '<li class="page-item" onclick="'.$func_name.'(\''.$total_page.'\', \''.$frm_name.'\')"> <a class="page-link" href="javascript:;" aria-label="마지막"><span aria-hidden="true" class="fa fa-angle-double-right"></span></a></li>';
    } else {
        $retValue .= '<li class="page-item disabled"> <a class="page-link" href="#" tabindex="-1" aria-disabled="true"><span aria-hidden="true" class="fa fa-angle-right"></span></a></li>';
    }
    $retValue .= '</ul>';

    return $retValue;
}

function page_listing_simple($cur_page, $total_page, $func_name, $frm_name='')
{
    $retValue = '';
    $retValue .= '<article class="my-5">';
    $retValue .= '<ul class="pagination fs_16">';
    if ($cur_page > 1) {
        $retValue .= '<li class=""><a class="arrow" href="javascript:'.$func_name.'(\''.(1).'\', \''.$frm_name.'\');"><img src="'.DESIGN_HTTP.'/img/pg_prev_prev.svg" /></a></li>';
        $retValue .= '<li class=""><a class="arrow" href="javascript:'.$func_name.'(\''.($cur_page-1).'\', \''.$frm_name.'\');"><img src="'.DESIGN_HTTP.'/img/pg_prev.svg" /></a></li>';
    } else {
        $retValue .= '<li class=""><a class="arrow disabled" href="javascript:'.$func_name.'(\''.(1).'\', \''.$frm_name.'\');"><img src="'.DESIGN_HTTP.'/img/pg_prev_prev.svg" /></a></li>';
        $retValue .= '<li class=""><a class="arrow disabled" href="javascript:;"><img src="'.DESIGN_HTTP.'/img/pg_prev.svg" /></a></li>';
    }
    $start_page = (((int)(($cur_page - 1) / 5)) * 5) + 1;
    $end_page = $start_page + 5;
    if ($end_page >= $total_page) {
        $end_page = $total_page;
    }
    if ($total_page > 1) {
        for ($k=$start_page;$k<=$end_page;$k++) {
            if ($cur_page != $k) {
                $retValue .= '<li class=""><a href="javascript:'.$func_name.'(\''.$k.'\', \''.$frm_name.'\');">'.$k.'</a></li>';
            } else {
                $retValue .= '<li class=""><a class="on" href="javascript:'.$func_name.'(\''.$k.'\', \''.$frm_name.'\');">'.$k.'</a></li>';
            }
        }
    }

    if ($cur_page < $total_page && $total_page > 1) {
        $retValue .= '<li class=""><a class="arrow" href="javascript:'.$func_name.'(\''.($cur_page+1).'\', \''.$frm_name.'\');"><img src="'.DESIGN_HTTP.'/img/pg_next.svg" /></a></li>';
        $retValue .= '<li class=""><a class="arrow" href="javascript:'.$func_name.'(\''.($total_page).'\', \''.$frm_name.'\');"><img src="'.DESIGN_HTTP.'/img/pg_next_next.svg" /></a></li>';
    } else {
        $retValue .= '<li class=""><a class="arrow disabled" href="javascript:;"><img src="'.DESIGN_HTTP.'/img/pg_next.svg" /></a></li>';
        $retValue .= '<li class=""><a class="arrow disabled" href="javascript:'.$func_name.'(\''.($total_page).'\', \''.$frm_name.'\');"><img src="'.DESIGN_HTTP.'/img/pg_next_next.svg" /></a></li>';
    }
    $retValue .= '</ul>';
    $retValue .= '</article>';

    return $retValue;
}

function page_listing($cur_page, $total_page, $url, $link_id="")
{
    $retValue = "<nav class=\"m-3\" aria-label=\"Page navigation\"><ul class=\"page-light pagination justify-content-center\">";
    if ($cur_page > 1) {
        $retValue .= "<li class=\"page-item\"><a class=\"page-link\" aria-label=\"이전\" href=\"".$url.($cur_page-1).$link_id."\"><span aria-hidden=\"true\">&laquo;</span></a></li>";
    } else {
        $retValue .= "<li class=\"page-item disabled\"><a class=\"page-link\" href=\"#\" tabindex=\"-1\" aria-disabled=\"true\"><span aria-hidden=\"true\">&laquo;</span></a></li>";
    }
    $start_page = (((int)(($cur_page - 1) / 5)) * 5) + 1;
    $end_page = $start_page + 5;
    if ($end_page >= $total_page) {
        $end_page = $total_page;
    }
    if ($total_page > 1) {
        for ($k=$start_page;$k<=$end_page;$k++) {
            if ($cur_page != $k) {
                $retValue .= "<li class=\"page-item\"><a class=\"page-link\" href=\"".$url.$k.$link_id."\">".$k."</a></li>";
            } else {
                $retValue .= "<li class=\"page-item active\" aria-current=\"page\"><a class=\"page-link\" href=\"".$url.$k.$link_id."\">".$k."</a></li>";
            }
        }
    }

    if ($cur_page < $total_page && $total_page > 1) {
        $retValue .= "<li class=\"page-item\"><a class=\"page-link\" aria-label=\"다음\" href=\"".$url.($cur_page+1).$link_id."\"><span aria-hidden=\"true\">&raquo;</span></a></li>";
    } else {
        $retValue .= "<li class=\"page-item disabled\"><a class=\"page-link\" href=\"#\" tabindex=\"-1\" aria-disabled=\"true\"><span aria-hidden=\"true\">&raquo;</span></a></li>";
    }
    $retValue .= "</ul></nav>";

    return $retValue;
}


function get_datetime_diff($sdatetime, $edatetime)
{
    $sdate  = new DateTime($sdatetime);
    $edate  = new DateTime($edatetime);

    $rtn = $edate->format('U') - $sdate->format('U');

    return $rtn;
}

function get_image_url_mng($pt_image, $no_cache="", $no_img="")
{
    global $ct_no_img_url, $ct_img_url, $ct_img_dir;

    if ($no_img) {
        $no_img_t = $no_img;
    } else {
        $no_img_t = $ct_no_img_url;
    }

    if (is_file($ct_img_dir."/".$pt_image)) {
        if ($no_cache=='Y') {
            $rtn = $ct_img_url."/".$pt_image."?v=".time();
        } else {
            $rtn = $ct_img_url."/".$pt_image;
        }
    } else {
        $rtn = $no_img_t;
    }

    return $rtn;
}

function get_image_url($pt_image, $no_img="")
{
    global $ct_no_img_url, $ct_img_url, $ct_img_dir, $setup_info;

    if ($no_img) {
        $no_img_t = $no_img;
    } else {
        $no_img_t = $ct_no_img_url;
    }

    if ($pt_image && is_file($ct_img_dir."/".$pt_image)) {
        $rtn = $ct_img_url."/".$pt_image.'?ver='.$setup_info['st_optimize_date'];
    } else {
        $rtn = $no_img_t;
    }

    return $rtn;
}

function resolveImageUrl ($image, $dir, $url)
{
  global $ct_no_profile_url, $ct_no_img_url, $setup_info;

  $rtn = $ct_no_img_url;
  if(!empty($image)) {
    $filepath = $dir . $image;
    if(file_exists($filepath)) {
      $rtn = $url.$image.'?ver='.$setup_info['st_optimize_date'];
    }
  }

  return $rtn;
}

function profileImageUrl ($image, $dir, $url)
{
  global $ct_no_profile_url, $ct_no_img_url, $setup_info;

  $rtn = $ct_no_profile_url;
  if(!empty($image)) {
    $filepath = $dir . $image;
    if(file_exists($filepath)) {
      $rtn = $url.$image.'?ver='.$setup_info['st_optimize_date'];
    }
  }

  return $rtn;
}

function price2kor($total_price)
{
    $trans_kor = array("","일","이","삼","사","오","육","칠","팔","구");
    $price_unit = array("","십","백","천","만","십","백","천","억","십","백","천","조","십","백","천");
    $valuecode = array("","만","억","조");

    $value = strlen($total_price);

    $k = 0;
    for ($i=$value;$i>0;$i--) {
        $vv = "";
        $vc = substr($total_price, $k, 1);
        $vt = $trans_kor[$vc];
        $k++;

        if ($i%5 ==0) {
            $vv = $valuecode[$i/5];
        } else {
            if ($vc) {
                $vv = $price_unit[$i-1];
            }
        }

        $vr = $vr.$vt.$vv;
    }

    return $vr;
}

function number_shorten($number, $precision = 0)
{
    $suffixes = ['', 'K', 'M', 'B', 'T', 'Qa', 'Qi'];
    if ($number<1000) {
        return number_format($number);
    } else {
        $index = (int) log(abs($number), 1000);
        $index = max(0, min(count($suffixes) - 1, $index));
        return number_format($number / 1000 ** $index, $precision) . $suffixes[$index];
    }
}

function check_file_ext($filename, $allow_ext) {
    if($filename == "") return true;
    $ext = get_file_ext($filename);
    $allow_ext = explode(";", $allow_ext);
    $sw_allow_ext = false;
    for ($i=0; $i<count($allow_ext); $i++)
        if($ext == $allow_ext[$i])
        {
            $sw_allow_ext = true;
            break;
        }

    return $sw_allow_ext;
}

function upload_file($srcfile, $destfile, $dir) {
    if($destfile == "") return false;
    move_uploaded_file($srcfile, $dir.$destfile);
    chmod($dir.$destfile, FILE_PERMISSION);

    return true;
}

function get_file_ext($filename) {
    if($filename == "") return "";
    $type = explode(".", $filename);
    $ext = strtolower($type[count($type)-1]);

    return $ext;
}

function cut_str($strSource,$iStart,$iLength,$tail="") {
    $iSourceLength = mb_strlen($strSource, "UTF-8");

    if($iSourceLength > $iLength) {
        return mb_substr($strSource, $iStart, $iLength, "UTF-8").$tail;
    } else {
        return $strSource;
    }
}

/** @smtp Mail 보내기
 *
 * @param $fromName 보내는 사람 이름
 * @param $fromEmail 보내는 사람 메일
 * @param $toName 받는 사람 이름
 * @param $toEmail 받는 사람 메일
 * @param $subject 메일제목
 * @param $contents 메일 내용
 * @param $isDebug 디버깅할때 1로 해서 사용하세요.
 * @return sendmail_flag 성공(true) 실패(false) 여부
 */
function sendMail($fromName, $fromEmail, $toName, $toEmail, $subject, $contents, $isDebug=0) {
    //Configuration
    $smtp_host = "smtp.gmail.com";
    $port = 587;
    $type = "text/html";
    $charSet = "UTF-8";

    //Open Socket
    $fp = @fsockopen($smtp_host, $port, $errno, $errstr, 1);
    if($fp){
        //Connection and Greetting
        $returnMessage = fgets($fp, 128);
        if($isDebug)
            print "CONNECTING MSG:".$returnMessage."\n";
        fputs($fp, "HELO YA\r\n");
        $returnMessage = fgets($fp, 128);
        if($isDebug)
            print "GREETING MSG:".$returnMessage."\n";

        // 이부분에 다음과 같이 로긴과정만 들어가면됩니다.
        fputs($fp, "auth login\r\n");
        fgets($fp,128);
        fputs($fp, base64_encode("")."\r\n");
        fgets($fp,128);
        fputs($fp, base64_encode("")."\r\n");
        fgets($fp,128);

        fputs($fp, "MAIL FROM: <".$fromEmail.">\r\n");
        $returnvalue[0] = fgets($fp, 128);
        fputs($fp, "rcpt to: <".$toEmail.">\r\n");
        $returnvalue[1] = fgets($fp, 128);

        if($isDebug){
            print "returnvalue:";
            print_r($returnvalue);
        }

        //Data
        fputs($fp, "data\r\n");
        $returnMessage = fgets($fp, 128);
        if($isDebug)
            print "data:".$returnMessage;
        fputs($fp, "Return-Path: ".$fromEmail."\r\n");
        $fromName = "=?".$fromName."?B?".base64_encode($fromName)."?=";
        fputs($fp, "From: ".$fromName." <".$fromEmail.">\r\n");
        fputs($fp, "To: <".$toEmail.">\r\n");
        $subject = "=?".$charSet."?B?".base64_encode($subject)."?=";

        fputs($fp, "Subject: ".$subject."\r\n");
        fputs($fp, "Content-Type: ".$type."; charset=\"".$charSet."\"\r\n");
        fputs($fp, "Content-Transfer-Encoding: base64\r\n");
        fputs($fp, "\r\n");
        $contents= chunk_split(base64_encode($contents));

        fputs($fp, $contents);
        fputs($fp, "\r\n");
        fputs($fp, "\r\n.\r\n");
        $returnvalue[2] = fgets($fp, 128);

        //Close Connection
        fputs($fp, "quit\r\n");
        fclose($fp);

        //Message
        if (strstr($returnvalue[0], "^250")&&strstr($returnvalue[1], "^250")&&strstr($returnvalue[2], "^250")){
            $sendmail_flag = true;
        }else {
            $sendmail_flag = false;
            print "NO :".$errno.", STR : ".$errstr;
        }
    }

    if (! $sendmail_flag){
        echo "메일 보내기 실패";
    }
    return $sendmail_flag;
}

// 파일 업로드 처리 함수
function handle_file_upload($board, $file, $filePosition, $_last_idx, $file_field_name, $upload_dir) {
    global $DB;

    if(!$file['name']) return false;

    // 디렉토리 존재 확인 및 생성
    if(!is_dir($upload_dir)) {
        // 디렉토리 생성
        if(!@mkdir($upload_dir, 0755, true)) {
            echo "디렉토리 생성 실패";
            return false;
        }
        // 권한 설정
        @chmod($upload_dir, 0755);
    }

    $timestamp = time();

    // 원본 파일명과 확장자 분리
    $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $bf_source = $file['name'];

    // 저장될 파일명 생성
    $bf_file = "{$file_field_name}_{$_last_idx}_{$filePosition}_{$timestamp}.{$file_ext}";

    // 파일 업로드
    if(move_uploaded_file($file['tmp_name'], $upload_dir.$bf_file)) {
        // 업로드된 파일 권한 설정
        @chmod($upload_dir.$bf_file, 0644);

        // 파일 정보 초기화
        $width = 0;
        $height = 0;
        $type = 0;

        // 이미지 파일인 경우에만 이미지 정보 가져오기
        $image_extensions = array('gif', 'jpg', 'jpeg', 'png', 'bmp', 'webp');
        if(in_array($file_ext, $image_extensions)) {
            $image_info = @getimagesize($upload_dir.$bf_file);
            if($image_info) {
                $width = $image_info[0];
                $height = $image_info[1];
                $type = $image_info[2];
            }
        }

        // 파일 크기
        $filesize = filesize($upload_dir.$bf_file);

        // DB에 파일 정보 저장
        $data = array(
            'board' => $board,
            'bo_id' => $_last_idx,      // 게시물 번호
            'bf_no' => $filePosition,   // 파일 순서 (1 또는 2)
            'bf_source' => $bf_source,
            'bf_file' => $bf_file,
            'bf_download' => 0,
            'bf_content' => '',
            'bf_fileurl' => '',
            'bf_thumburl' => '',
            'bf_storage' => '',
            'bf_filesize' => $filesize,
            'bf_width' => $width,
            'bf_height' => $height,
            'bf_type' => $type,
            'bf_datetime' => date('Y-m-d H:i:s')
        );

        $DB->insert('board_file_t', $data);
        return true;
    }
    return false;
}

function thumnail($file, $save_filename, $save_path, $max_width, $max_height) {
    $img_info = getimagesize($file);
    if($img_info[2] == 1)
    {
        $src_img = ImageCreateFromGif($file);
    }elseif($img_info[2] == 2){
        $src_img = ImageCreateFromJPEG($file);
    }elseif($img_info[2] == 3){
        $src_img = ImageCreateFromPNG($file);
    }else{
        return 0;
    }
    $img_width = $img_info[0];
    $img_height = $img_info[1];

    if($img_width > $max_width || $img_height > $max_height)
    {
        if($img_width == $img_height)
        {
            $dst_width = $max_width;
            $dst_height = $max_height;
        }elseif($img_width > $img_height){
            $dst_width = $max_width;
            $dst_height = ceil(($max_width / $img_width) * $img_height);
        }else{
            $dst_height = $max_height;
            $dst_width = ceil(($max_height / $img_height) * $img_width);
        }
    }else{
        $dst_width = $img_width;
        $dst_height = $img_height;
    }
    if($dst_width < $max_width) $srcx = ceil(($max_width - $dst_width)/2); else $srcx = 0;
    if($dst_height < $max_height) $srcy = ceil(($max_height - $dst_height)/2); else $srcy = 0;

    if($img_info[2] == 1)
    {
        $dst_img = imagecreate($max_width, $max_height);
    }else{
        $dst_img = imagecreatetruecolor($max_width, $max_height);
    }

    $bgc = ImageColorAllocate($dst_img, 255, 255, 255);
    ImageFilledRectangle($dst_img, 0, 0, $max_width, $max_height, $bgc);
    ImageCopyResampled($dst_img, $src_img, $srcx, $srcy, 0, 0, $dst_width, $dst_height, ImageSX($src_img),ImageSY($src_img));

    if($img_info[2] == 1)
    {
        ImageInterlace($dst_img);
        ImageGif($dst_img, $save_path.$save_filename);
    }elseif($img_info[2] == 2){
        ImageInterlace($dst_img);
        ImageJPEG($dst_img, $save_path.$save_filename);
    }elseif($img_info[2] == 3){
        ImagePNG($dst_img, $save_path.$save_filename);
    }
    @ImageDestroy($dst_img);
    @ImageDestroy($src_img);
}

function thumnail_width($file, $save_filename, $save_path, $max_width) {
    $img_info = getimagesize($file);
    if($img_info[2] == 1) {
        $src_img = ImageCreateFromGif($file);
    } else if($img_info[2] == 2) {
        $src_img = ImageCreateFromJPEG($file);
    } else if($img_info[2] == 3) {
        $src_img = ImageCreateFromPNG($file);
    } else {
        return 0;
    }

    $img_width = $img_info[0];
    $img_height = $img_info[1];

    $dst_width = $max_width;
    $dst_height = round($dst_width*($img_height/$img_width));

    $srcx = 0;
    $srcy = 0;

    if($img_info[2] == 1) {
        $dst_img = imagecreate($dst_width, $dst_height);
    } else {
        $dst_img = imagecreatetruecolor($dst_width, $dst_height);
        imagealphablending( $dst_img, false );
        imagesavealpha( $dst_img, true );
    }

    ImageCopyResampled($dst_img, $src_img, $srcx, $srcy, 0, 0, $dst_width, $dst_height, ImageSX($src_img),ImageSY($src_img));

    if($img_info[2] == 1) {
        ImageInterlace($dst_img);
        ImageGif($dst_img, $save_path.$save_filename);
    } else if($img_info[2] == 2) {
        ImageInterlace($dst_img);
        ImageJPEG($dst_img, $save_path.$save_filename);
    } else if($img_info[2] == 3) {
        ImagePNG($dst_img, $save_path.$save_filename);
    }
    @ImageDestroy($dst_img);
    @ImageDestroy($src_img);
}

function thumbnail_crop_center($file, $save_filename, $save_path, $max_width, $max_height) {
    //사이즈에 맞춰 채워 넣는 방식으로 수정, 아래 scale_image_fill 함수 참고 2015-04-21 이창민
    $img_info = getimagesize($file);

    if($img_info[2] == 1) {
        $src = ImageCreateFromGif($file);
    } else if($img_info[2] == 2) {
        $src = ImageCreateFromJPEG($file);
    } else if($img_info[2] == 3) {
        $src = ImageCreateFromPNG($file);
    } else {
        return 0;
    }

    $dst = imagecreatetruecolor($max_width, $max_height);
    imagefill($dst, 0, 0, imagecolorallocate($dst, 255, 255, 255));

    $src_width = imagesx($src);
    $src_height = imagesy($src);

    $dst_width = imagesx($dst);
    $dst_height = imagesy($dst);

    $new_width = $dst_width;
    $new_height = round($new_width*($src_height/$src_width));
    $new_x = 0;
    $new_y = round(($dst_height-$new_height)/2);

    $next = $new_height < $dst_height;

    if($next) {
        $new_height = $dst_height;
        $new_width = round($new_height*($src_width/$src_height));
        $new_x = round(($dst_width - $new_width)/2);
        $new_y = 0;
    }

    imagecopyresampled($dst, $src , $new_x, $new_y, 0, 0, $new_width, $new_height, $src_width, $src_height);

    if($img_info[2] == 1) {
        ImageInterlace($dst);
        ImageGif($dst, $save_path.$save_filename);
    } else if($img_info[2] == 2) {
        ImageInterlace($dst);
        ImageJPEG($dst, $save_path.$save_filename);
    } else if($img_info[2] == 3) {
        ImagePNG($dst, $save_path.$save_filename);
    }

    @ImageDestroy($dst);
    @ImageDestroy($src);
}

function scale_image_fill($src_image, $save_filename, $save_path, $max_width, $max_height) {
    $img_info = getimagesize($src_image);

    if($img_info[2] == 1) {
        $src = ImageCreateFromGif($src_image);
    } else if($img_info[2] == 2) {
        $src = ImageCreateFromJPEG($src_image);
    } else if($img_info[2] == 3) {
        $src = ImageCreateFromPNG($src_image);
    } else {
        return 0;
    }

    $dst = imagecreatetruecolor($max_width, $max_height);
    imagefill($dst, 0, 0, imagecolorallocate($dst, 255, 255, 255));

    $src_width = imagesx($src);
    $src_height = imagesy($src);

    $dst_width = imagesx($dst);
    $dst_height = imagesy($dst);

    $new_width = $dst_width;
    $new_height = round($new_width*($src_height/$src_width));
    $new_x = 0;
    $new_y = round(($dst_height-$new_height)/2);

    $next = $new_height < $dst_height;

    if($next) {
        $new_height = $dst_height;
        $new_width = round($new_height*($src_width/$src_height));
        $new_x = round(($dst_width - $new_width)/2);
        $new_y = 0;
    }

    imagecopyresampled($dst, $src , $new_x, $new_y, 0, 0, $new_width, $new_height, $src_width, $src_height);

    if($img_info[2] == 1) {
        ImageInterlace($dst);
        ImageGif($dst, $save_path.$save_filename);
    } else if($img_info[2] == 2) {
        ImageInterlace($dst);
        ImageJPEG($dst, $save_path.$save_filename);
    } else if($img_info[2] == 3) {
        ImagePNG($dst, $save_path.$save_filename);
    }

    @ImageDestroy($dst);
    @ImageDestroy($src);
}

function scale_image_fit($src_image, $save_filename, $save_path, $max_width, $max_height) {
    $img_info = getimagesize($src_image);

    if($img_info[2] == 1) {
        $src = ImageCreateFromGif($src_image);
    } else if($img_info[2] == 2) {
        $src = ImageCreateFromJPEG($src_image);
    } else if($img_info[2] == 3) {
        $src = ImageCreateFromPNG($src_image);
    } else {
        return 0;
    }

    $dst = imagecreatetruecolor($max_width, $max_height);
    imagefill($dst, 0, 0, imagecolorallocate($dst, 255, 255, 255));

    $src_width = imagesx($src);
    $src_height = imagesy($src);

    $dst_width = imagesx($dst);
    $dst_height = imagesy($dst);

    $new_width = $dst_width;
    $new_height = round($new_width*($src_height/$src_width));
    $new_x = 0;
    $new_y = round(($dst_height-$new_height)/2);

    $next = $new_height > $dst_height;

    if($next) {
        $new_height = $dst_height;
        $new_width = round($new_height*($src_width/$src_height));
        $new_x = round(($dst_width - $new_width)/2);
        $new_y = 0;
    }

    imagecopyresampled($dst, $src , $new_x, $new_y, 0, 0, $new_width, $new_height, $src_width, $src_height);

    if($img_info[2] == 1) {
        ImageInterlace($dst);
        ImageGif($dst, $save_path.$save_filename);
    } else if($img_info[2] == 2) {
        ImageInterlace($dst);
        ImageJPEG($dst, $save_path.$save_filename);
    } else if($img_info[2] == 3) {
        ImagePNG($dst, $save_path.$save_filename);
    }

    @ImageDestroy($dst);
    @ImageDestroy($src);
}

function resize_crop_image($max_width, $max_height, $source_file, $dst_dir, $crop = true, $watermark = false, $quality = 70, $orientation = 0) {

    $imgsize = getimagesize($source_file);
    $width = $imgsize[0];
    $height = $imgsize[1];
    $mime = $imgsize['mime'];

    switch($mime){
        case 'image/gif':
            $image_create = 'imagecreatefromgif';
            $image = 'imagegif';
            break;

        case 'image/png':
            $image_create = 'imagecreatefrompng';
            $image = 'imagepng';
            $quality = 7;
            break;

        case 'image/jpeg':
            $image_create = 'imagecreatefromjpeg';
            $image = 'imagejpeg';
            $quality = 80;
            break;

        default:
            return false;
            break;
    }

    $src_img = $image_create($source_file);
    $is_lotate = false;
    if ($mime == 'image/jpeg') {
        // 자동으로 이미지가 회전되는 현상 해결
        $exif = exif_read_data($source_file);
        $exif_Orientation = $exif['Orientation'] ? $exif['Orientation'] : $orientation;
        if(!empty($exif_Orientation)) {
            switch($exif_Orientation) {
                case 8:
                    $src_img = imagerotate($src_img, 90, 0);
                    $is_lotate = true;
                    break;
                case 3:
                    $src_img = imagerotate($src_img, 180, 0);
                    break;
                case 6:
                    $src_img = imagerotate($src_img, -90, 0);
                    $is_lotate = true;
                    break;
            }
        }
    }

    if (!$crop) {
        // 회전됨에 따라 가로, 세로 뒤바꾸기
        if ($is_lotate) {
            $tmp_max_width = $max_width;
            $max_width = $max_height;
            $max_height = $tmp_max_width;
            $tmp_width = $width;
            $width = $height;
            $height = $tmp_width;
        }

        if ($width >= $height) {
            // 가로가 클 경우 - 가로 제한을 기준으로 리사이즈 (세로:비율에 따른 축소, 가로:max-width) -- (original height / original width) x new width = new height
            $max_height = ceil(($height / $width) * $max_width);
            $max_width = $max_width;
        } else {
            // 세로가 클 경우 - 세로 제한을 기준으로 리사이즈 (세로:max-height, 가로:비율에 따른 축소) -- (original width / original height) x new height = new width
            $max_width = ceil(($width / $height) * $max_height);
            $max_height = $max_height;
        }
    }

    $dst_img = imagecreatetruecolor($max_width, $max_height);

    // png는 배경 불투명하게
    if ( $mime == 'image/png' ) {
        imagealphablending($dst_img, false);
        imagesavealpha($dst_img, true);
        $transparent = imagecolorallocatealpha($dst_img, 255, 255, 255, 127);
        imagefilledrectangle($dst_img, 0, 0, $width, $height, $transparent);
    }

    if ($crop) {
        $width_new = $height * $max_width / $max_height;
        $height_new = $width * $max_height / $max_width;
        //if the new width is greater than the actual width of the image, then the height is too large and the rest cut off, or vice versa
        if($width_new > $width){
            //cut point by height
            $h_point = (($height - $height_new) / 2);
            //copy image
            imagecopyresampled($dst_img, $src_img, 0, 0, 0, $h_point, $max_width, $max_height, $width, $height_new);
        }else{
            //cut point by width
            $w_point = (($width - $width_new) / 2);
            imagecopyresampled($dst_img, $src_img, 0, 0, $w_point, 0, $max_width, $max_height, $width_new, $height);
        }
    } else {
        imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $max_width, $max_height, $width, $height);
    }

    $image($dst_img, $dst_dir, $quality);

    if( $watermark == true ) {
        $stamp = imagecreatefrompng($_SERVER['DOCUMENT_ROOT'] . '/images/watermark.png');
        $im = imagecreatefromjpeg($dst_dir);

        $sx = imagesx($stamp);
        $sy = imagesy($stamp);

        $x_dst = ( $max_width - $sx ) / 2;
        $y_dst = ( $max_height - $sy ) / 2;

        imagecopy($im, $stamp, $x_dst, $y_dst, 0, 0, imagesx($stamp), imagesy($stamp));
        imagejpeg($im, $dst_dir, 100);
        if($im)imagedestroy($im);
    }

    if($dst_img)imagedestroy($dst_img);
    if($src_img)imagedestroy($src_img);
}

function encrypt($str, $key) {
    # Add PKCS7 padding.
    $block = mcrypt_get_block_size('des', 'ecb');
    if (($pad = $block - (strlen($str) % $block)) < $block) {
        $str .= str_repeat(chr($pad), $pad);
    }

    return mcrypt_encrypt(MCRYPT_DES, $key, $str, MCRYPT_MODE_ECB);
}

function decrypt($str, $key) {
    $str = mcrypt_decrypt(MCRYPT_DES, $key, $str, MCRYPT_MODE_ECB);

    # Strip padding out.
    $block = mcrypt_get_block_size('des', 'ecb');
    $pad = ord($str[($len = strlen($str)) - 1]);
    if ($pad && $pad < $block && preg_match(
            '/' . chr($pad) . '{' . $pad . '}$/', $str
        )
    ) {
        return substr($str, 0, strlen($str) - $pad);
    }
    return $str;
}

function get_openssl_encrypt($data) {
    $pass = DECODEKEY;
    $iv = DECODEKEY;

    $endata = @openssl_encrypt($data , "aes-256-cbc", $pass, true, $iv);
    $endata = base64_encode($endata);

    return $endata;
}

function get_openssl_decrypt($endata) {
    $pass = DECODEKEY;
    $iv = DECODEKEY;

    $data = base64_decode($endata);
    $dedata = @openssl_decrypt($data , "aes-256-cbc", $pass, true, $iv);

    return $dedata;
}

function get_text($str) {
    $source[] = "/</";
    $target[] = "&lt;";
    $source[] = "/>/";
    $target[] = "&gt;";
    $source[] = "/\'/";
    $target[] = "&#039;";

    return preg_replace($source, $target, strip_tags($str));
}

function stripTagsAndLimit($html, $limit = 100) {
    $text = strip_tags($html);
    $text = trim(preg_replace('/\s+/', ' ', $text));
    return mb_substr($text, 0, $limit, 'UTF-8');
}

function cal_remain_days($s_date, $e_date) {
    if($e_date=="") return "0";

    $s_date_ex = explode("-", $s_date);
    $e_date_ex = explode("-", $e_date);

    $s_time = mktime(0, 0, 0, $s_date_ex[1], $s_date_ex[2], $s_date_ex[0]);
    $e_time = mktime(23, 59, 59, $e_date_ex[1], $e_date_ex[2], $e_date_ex[0]);

    if($s_time > $e_time) {
        return 0;
    } else {
        $result_time = ($e_time - $s_time) / (60*60*24);

        if($result_time < 0) {
            return 0;
        } else {
            return round($result_time);
        }
    }
}

function cal_remain_days2($s_date, $e_date) {
    if ($e_date == "") {
        return "0";
    }

    $s_date_ex = explode(" ", $s_date);
    $s_date_ex2 = explode("-", $s_date_ex[0]);
    $s_date_ex3 = explode(":", $s_date_ex[1]);
    $e_date_ex = explode(" ", $e_date);
    $e_date_ex2 = explode("-", $e_date_ex[0]);
    $e_date_ex3 = explode(":", $e_date_ex[1]);

    $s_time = mktime(0, 0, 0, $s_date_ex2[1], $s_date_ex2[2], $s_date_ex2[0]);
    $e_time = mktime(23, 59, 59, $e_date_ex2[1], $e_date_ex2[2], $e_date_ex2[0]);

    if ($s_time > $e_time) {
        $rtn = 0;
    } else {
        $result_time = ($e_time - $s_time) / (60 * 60 * 24);

        $rtn = round($result_time);
    }

    return $rtn;
}

function make_mktime($date) {
    $date_ex = explode(" ", $date);
    $date_ex2 = explode("-", $date_ex[0]);
    $date_ex3 = explode(":", $date_ex[1]);

    if($date_ex[1]) {
        $s_time = mktime($date_ex3[0], $date_ex3[1], $date_ex3[2], $date_ex2[1], $date_ex2[2], $date_ex2[0]);
    } else {
        $s_time = mktime(0, 0, 0, $date_ex2[1], $date_ex2[2], $date_ex2[0]);
    }

    return $s_time;
}

function quote2entities($string,$entities_type='number') {
    $search = array("\"","'");
    $replace_by_entities_name = array("&quot;","&apos;");
    $replace_by_entities_number = array("&#34;","&#39;");
    $do = null;
    if ($entities_type == 'number') {
        $do = str_replace($search,$replace_by_entities_number,$string);
    } else if ($entities_type == 'name') {
        $do = str_replace($search,$replace_by_entities_name,$string);
    } else {
        $do = addslashes($string);
    }

    return $do;
}

function printr($arr_val) {
    echo "<pre>";
    print_r($arr_val);
    echo "</pre>";
}

function fnc_Day_Name($strDate){
    $strDate = substr($strDate,0,10);
    $days = array("일","월","화","수","목","금","토");
    //$days = array("Sun","Mon","Tue","Wed","Thu","Fri","Sat");
    $temp_day = date("w", strtotime($strDate));
    return $days[$temp_day];
}

function DateType($strDate, $type="1"){
    if($strDate=="" || $strDate=="0000-00-00 00:00:00") {
        $strDate = "-";
    } else {
        if($type=="1") { // use
            $strDate = str_replace("-",".",substr($strDate,0,10));
        } else if($type=="2") { // use
            $strDate = str_replace("-",".",substr($strDate,2,8));
        } else if($type=="3") { // use
            $strDate = str_replace("-",".",substr($strDate,0,10))."(".fnc_Day_Name($strDate).")";
        } else if($type=="4") { // use
            $strDate = str_replace("-",".",substr($strDate,2,8))."(".fnc_Day_Name($strDate).")&nbsp;".substr($strDate,11,5);
        } else if($type=="5") { // use
            $strDate = str_replace("-",".",substr($strDate,2,8))." ".substr($strDate,11,5);
        } else if($type=="6") { // use
            $strDate = substr($strDate,0,10)." ".substr($strDate,11,5);
        } else if($type=="7") { // use
            $strDate = substr($strDate,0,10);
        } else if($type=="8") {
            $strDate = str_replace("-","/",substr($strDate,0,10))." ".substr($strDate,11,5);
        } else if($type=="9") {
            $strDate = str_replace("-","/",substr($strDate,0,10));
        } else if($type=="10") {
            $strDate = str_replace("-","년 ",substr($strDate,2,5))."월";
        } else if($type=="11") {
            $strDate_ex1 = explode(' ', $strDate);
            $strDate_ex2 = explode('-', $strDate_ex1[0]);

            $strDate = $strDate_ex2[0]."년 ".$strDate_ex2[1]."월 ".$strDate_ex2[2]."일";
        }
    }

    return $strDate;
}

function substr_star($str){
    $str_len = mb_strlen($str);
    $str_arr = str_split($str);

    $result = "";
    for($i=0 ; $i < $str_len ; $i++){
        if($i < 3){
            $result .= $str_arr[$i];
        }else{
            $result .= "*";
        }
    }
    return $result;
}

function mt_pw_make() {
    return substr(md5(time()), 0, 8);
}

function mt_sms_make() {
    return mt_rand(111111, 999999);
}

$valid_txt_id = " (영소문자, 숫자 조합 5~15자)";
$valid_msg_id = "5~15자의 영문 소문자, 숫자, _ 만 입력하세요.";
$valid_txt_pw = " (영문, 숫자, 특수문자 조합 7~20자)";
$valid_msg_pw1 = "영문, 숫자, 특수문자를 포함하여 7~20자 이내로 입력하세요.";
$valid_msg_pw2 = "사용가능한 특수문자는 [!@#$%^&*+=] 입니다.";

function valid_id($mt_id) {
    global $DB;
    $msg = "";

    $cf_prohibit_id = "admin,administrator,master,webmaster,manager,root,su,guest";
    $cf_prohibit_arr = explode(',', $cf_prohibit_id);

    //$row = $DB->fetch_query("select * from member_t where mt_id = '".$mt_id."'");
    $DB->where('mt_id', $mt_id);
    $row = $DB->getone('member_t');
    if($row['idx']) {
        if($row['mt_level'] <= '1') {
            $msg = "사용하실 수 없는 아이디입니다.";
        } else {
            $msg = "이미 사용중인 아이디입니다.";
        }
    } else {
        if (strlen($mt_id) < 5) {
            $msg = "아이디는 최소 5글자 이상 입력하세요.";
        }
        if (preg_match("/[^0-9a-zA-Z_]+/i", $mt_id)) {
            $msg = "아이디는 영문자, 숫자, _ 만 입력하세요.";
        }
        $eng = preg_match('/[a-zA-Z]/u', $mt_id);
        if( !( $eng > 0 ) ) {
            $msg = "아이디는 영문을 포함하여 입력하세요.";
        }
        if (in_array($mt_id, $cf_prohibit_arr)) {
            $msg = "사용하실 수 없는 아이디입니다.";
        }
        /*if (preg_match("/[\,]?{$mt_id}/i", $cf_prohibit_id)) {
            $msg = "사용하실 수 없는 아이디입니다.";
        }*/
    }
    return $msg;
}
function valid_id_check($mt_id, $chk_type) {
    global $DB;
    $msg = "";

    $cf_prohibit_id = "adm,admin,administrator,master,webmaster,manager,root,su,guest";
    $cf_prohibit_arr = explode(',', $cf_prohibit_id);

    if ($mt_id) {
        if ($chk_type==='duplication') {
            //$row = $DB->fetch_query("select * from member_t where mt_id = '".trim($mt_id)."'");
            $DB->where('mt_id', trim($mt_id));
            $row = $DB->getone('member_t');
            if($row['idx']) {
                if($row['mt_level'] <= '1') {
                    $msg = "사용하실 수 없는 아이디입니다.";
                } else {
                    $msg = "이미 사용중인 아이디입니다.";
                }
            }
        } else if ($chk_type==='special_char') {
            if (preg_match("/[^0-9a-zA-Z_]+/i", $mt_id)) {
                $msg = "아이디는 영문자, 숫자, _ 만 입력하세요.";
            }

            $eng = preg_match('/[a-zA-Z]/u', $mt_id);
            if( !( $eng > 0 ) ) {
                $msg = "아이디는 영문을 포함하여 입력하세요.";
            }
        } else if ($chk_type==='prohibit') {
            if (in_array($mt_id, $cf_prohibit_arr)) {
                $msg = "사용하실 수 없는 아이디입니다.";
            }
        }
    } else {
        $msg = "아이디를 입력하세요.";
    }

    return $msg;
}
function valid_password($reg_password, $reg_level=2) {
    $reg_level = $reg_level*1;
    $pw = $reg_password;
    $num = preg_match('/[0-9]/u', $pw);
    $eng = preg_match('/[a-zA-Z]/u', $pw);
    $spe = preg_match("/[\!\@\#\$\%\^\&\*\+\=]/u",$pw);

    // 영어 대소문자, 숫자, 특수문자 패턴 정의
    $pattern = '/^[a-zA-Z0-9!@#\$%\^&\*\+=]+$/';

    if ($pw) {
        if(strlen($pw) < 7) {
            return "비밀번호는 7자리 이상 입력하세요.";
        }

        if(preg_match("/\s/u", $pw) == true) {
            return "비밀번호는 공백없이 입력하세요.";
        }

        if (!preg_match($pattern, $pw)) {
            return "특수문자는 !@#$%^&*+=만 사용해 주세요.";
        }

        //if( $num == 0 || $eng == 0 || $spe == 0 ) {
        if( !( $num > 0 && $eng > 0 && $spe > 0 ) ) {
            return "비밀번호는 영문, 숫자, 특수문자를 조합하여 입력하세요.";
        }
    }

    return "";
}
function valid_email($reg_email, $mt_id="") {
    global $DB;

    $DB->where("mt_status", "Y")->where("mt_level > '1'")->where('mt_email', $reg_email);
    if ($mt_id) {
        $DB->where("mt_id != '".$mt_id."'");
    }
    $row = $DB->getone('member_t', 'COUNT(*) AS cnt');
    if($row['cnt']) {
        return "이미 사용중인 이메일입니다.";
    } else {
        if (!preg_match("/([0-9a-zA-Z_-]+)@([0-9a-zA-Z_-]+)\.([0-9a-zA-Z_-]+)/", $reg_email))
            return "이메일이 형식에 맞지 않습니다.";
        else
            return "";
    }
}
function valid_hp($reg_hp, $mt_id="", $mt_level="", $chk_type="") {
    global $DB;
    $reg_hp = preg_replace("/[^0-9]/", "", $reg_hp);

    $DB->where("mt_status", "Y")->where('mt_hp', format_phone($reg_hp));
    if ($mt_id) {
        $DB->where("mt_id != '".$mt_id."'");
    }
    if ($mt_level) {
        /*if ($mt_level == '4') {
            $DB->where("(mt_level = '4' OR mt_level = '3')");
        } else {
            $DB->where("mt_level = '".$mt_level."'");
        }*/
        $DB->where("mt_level = '".$mt_level."'");
    } else {
        $DB->where("mt_level > '1'");
    }
    $row = $DB->getone('member_t', 'COUNT(*) AS cnt');
    if($row['cnt'] && !$chk_type) {
        return "이미 사용중인 휴대폰번호입니다.";
    } else {
        if(!$reg_hp)
            return "휴대폰번호를 입력하세요.";
        else {
            if(preg_match("/^01[0-9]{8,9}$/", $reg_hp))
                return "";
            else
                return "휴대폰번호를 올바르게 입력하세요.";
        }
    }
}
function valid_hp_check($reg_hp) {
    $reg_hp = preg_replace("/[^0-9]/", "", $reg_hp);

    if(!$reg_hp)
        return "휴대폰번호를 입력하세요.";
    else {
        if(preg_match("/^01[0-9]{8,9}$/", $reg_hp))
            return "";
        else
            return "휴대폰번호를 올바르게 입력하세요.";
    }
}
function valid_name($reg_nick) {
    if (!check_string($reg_nick, GET_HANGUL + GET_ALPHABETIC))
        return "이름은 공백없이 한글, 영문만 입력 가능합니다.";
    else {
        $msg = f_text_filter($reg_nick, '이름');
        if ($msg) {
            return $msg;
        } else {
            return "";
        }
    }
}
function valid_nick($reg_nick) {
    $cf_prohibit_id = "adm,admin,administrator,master,webmaster,manager,root,su,관리자,최고관리자,최고 관리자,운영자,미트라이더,meatrider";
    $cf_prohibit_arr = explode(',', $cf_prohibit_id);

    if ($reg_nick) {
        if(preg_match("/\s/u", $reg_nick) == true) {
            return "닉네임은 공백없이 한글, 영문, 숫자만 입력 가능합니다.";//, _-.
        }
        if (preg_match("/[^0-9a-zA-Z가-힣]+/i", $reg_nick)) {//\_\-\.
            return "닉네임은 공백없이 한글, 영문, 숫자만 입력 가능합니다.";//, _-.
        }
        $str = preg_match('/[0-9a-zA-Z가-힣]/u', $reg_nick);
        if( !( $str > 0 ) ) {
            return "닉네임을 올바르게 입력하세요.";
        }
        if (in_array($reg_nick, $cf_prohibit_arr)) {
            return "사용하실 수 없는 닉네임입니다.";
        }
    } else {
        $msg = f_text_filter($reg_nick, '닉네임');
        if ($msg) {
            return $msg;
        } else {
            return "";
        }
    }
//    if (!check_string($reg_nick, GET_HANGUL + GET_ALPHABETIC + GET_NUMERIC))
//        return "닉네임은 공백없이 한글, 영문, 숫자만 입력 가능합니다.";
//    else
//        return "";
}
// 문자열이 한글, 영문, 숫자, 특수문자로 구성되어 있는지 검사
function check_string($str, $options) {

    $s = '';
    for($i=0;$i<strlen($str);$i++) {
        $c = $str[$i];
        $oc = ord($c);

        // 한글
        if ($oc >= 0xA0 && $oc <= 0xFF) {
            if ($options & GET_HANGUL) {
                $s .= $c . $str[$i+1] . $str[$i+2];
            }
            $i+=2;
        }
        // 숫자
        else if ($oc >= 0x30 && $oc <= 0x39) {
            if ($options & GET_NUMERIC) {
                $s .= $c;
            }
        }
        // 영대문자
        else if ($oc >= 0x41 && $oc <= 0x5A) {
            if (($options & GET_ALPHABETIC) || ($options & GET_ALPHAUPPER)) {
                $s .= $c;
            }
        }
        // 영소문자
        else if ($oc >= 0x61 && $oc <= 0x7A) {
            if (($options & GET_ALPHABETIC) || ($options & GET_ALPHALOWER)) {
                $s .= $c;
            }
        }
        // 공백
        else if ($oc == 0x20) {
            if ($options & GET_SPACE) {
                $s .= $c;
            }
        }
        else {
            if ($options & GET_SPECIAL) {
                $s .= $c;
            }
        }
    }

    // 넘어온 값과 비교하여 같으면 참, 틀리면 거짓
    return ($str == $s);
}
//------------------------------------------------------------------------------------------------------------------

function save_remote_img_curl_fn($url, $dir, $tmpname) {
    $filename = '';

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_NOBODY, true);

    curl_exec ($ch);

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if($http_code == 200) {
        $filename = basename($url);
        if(preg_match("/\.(gif|jpg|jpeg|png)$/i", $filename)) {
            $filepath = $dir;
            @mkdir($filepath, '0755');
            @chmod($filepath, '0755');

            // 파일 다운로드
            $path = $filepath.'/'.$tmpname;
            $fp = fopen ($path, 'w');

            $ch = curl_init();
            curl_setopt( $ch, CURLOPT_URL, $url );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, false );
            curl_setopt( $ch, CURLOPT_BINARYTRANSFER, true );
            curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
            curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
            curl_setopt( $ch, CURLOPT_FILE, $fp );
            curl_exec( $ch );
            curl_close( $ch );

            fclose( $fp );

            // 다운로드 파일이 이미지인지 체크
            if(is_file($path)) {
                $size = @getimagesize($path);
                if($size[2] < 1 || $size[2] > 3) {
                    @unlink($path);
                    $filename = '';
                } else {
                    $ext = array(1=>'gif', 2=>'jpg', 3=>'png');
                    $filename = $tmpname.'.'.$ext[$size[2]];
                    rename($path, $filepath.'/'.$filename);
                    //@chmod($filepath.'/'.$filename, '0644');
                }
            }
        }
    }

    return $filename;
}

function save_remote_img_curl($url, $dir, $mt_idx) {
    $filename = '';

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_NOBODY, true);

    curl_exec ($ch);

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if($http_code == 200) {
        $filename = basename($url);
        if(preg_match("/\.(gif|jpg|jpeg|png)$/i", $filename)) {
            //$tmpname = date('YmdHis').(microtime(true) * 10000);
            $tmpname = "mt_img_".$mt_idx."_".date("YmdHis");
            $filepath = $dir;
            @mkdir($filepath, '0755');
            @chmod($filepath, '0755');

            // 파일 다운로드
            $path = $filepath.'/'.$tmpname;
            $fp = fopen ($path, 'w');

            $ch = curl_init();
            curl_setopt( $ch, CURLOPT_URL, $url );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, false );
            curl_setopt( $ch, CURLOPT_BINARYTRANSFER, true );
            curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
            curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
            curl_setopt( $ch, CURLOPT_FILE, $fp );
            curl_exec( $ch );
            curl_close( $ch );

            fclose( $fp );

            // 다운로드 파일이 이미지인지 체크
            if(is_file($path)) {
                $size = @getimagesize($path);
                if($size[2] < 1 || $size[2] > 3) {
                    @unlink($path);
                    $filename = '';
                } else {
                    $ext = array(1=>'gif', 2=>'jpg', 3=>'png');
                    $filename = $tmpname.'.'.$ext[$size[2]];
                    rename($path, $filepath.'/'.$filename);
                    @chmod($filepath.'/'.$filename, '0644');
                }
            }
        }
    }

    return $filename;
}

function save_remote_img_file($url, $dir, $mt_idx) {
    $filename = file_get_contents($url);
    $img_info = pathinfo($url);
    $tmpname = "mt_img_".$mt_idx."_".date("YmdHis").'.'.$img_info[extension];
    file_put_contents($dir."/".$tmpname, $filename);

    return $tmpname;
}

function save_facebook_profile_img($url, $dir, $mt_idx) {
    $filename = '';

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_NOBODY, true);

    curl_exec ($ch);

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if($http_code == 200) {
        $filename = basename($url);
        $filename_ex = explode("?", $filename);
        $filename = $filename_ex[0];
        if(preg_match("/\.(gif|jpg|jpeg|png)$/i", $filename)) {
            //$tmpname = date('YmdHis').(microtime(true) * 10000);
            $tmpname = "mt_img_".$mt_idx."_".date("YmdHis");
            $filepath = $dir;
            @mkdir($filepath, '0755');
            @chmod($filepath, '0755');

            // 파일 다운로드
            $path = $filepath.'/'.$tmpname;
            $fp = fopen ($path, 'w');

            $ch = curl_init();
            curl_setopt( $ch, CURLOPT_URL, $url );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, false );
            curl_setopt( $ch, CURLOPT_BINARYTRANSFER, true );
            curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
            curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
            curl_setopt( $ch, CURLOPT_FILE, $fp );
            curl_exec( $ch );
            curl_close( $ch );

            fclose( $fp );

            // 다운로드 파일이 이미지인지 체크
            if(is_file($path)) {
                $size = @getimagesize($path);
                if($size[2] < 1 || $size[2] > 3) {
                    @unlink($path);
                    $filename = '';
                } else {
                    $ext = array(1=>'gif', 2=>'jpg', 3=>'png');
                    $filename = $tmpname.'.'.$ext[$size[2]];
                    rename($path, $filepath.'/'.$filename);
                    //@chmod($filepath.'/'.$filename, '0644');
                }
            }
        }
    }

    return $filename;
}

function inconv_post($s1, $s2, $arr) {
    foreach($arr as $key => $val) {
        $arr[$key] = iconv($s1, $s2, $val);
    }

    return $arr;
}

function date_diffrent($sdate, $edate) {
    $date1 = new DateTime($sdate);
    $date2 = new DateTime($edate);
    $diff = date_diff($date1, $date2);

    $return = "";
    if($diff->days==0) {
        if($diff->d==0) {
            if($diff->h==0) {
                if($diff->i==0) {
                    $return = $diff->s."초";
                } else {
                    $return = $diff->i."분";
                }
            } else {
                $return = $diff->h."시";
            }
        }
    } else {
        if($diff->days>7) {
            $return = round($diff->days/7)."주";
        } else {
            $return = $diff->days."일";
        }
    }

    return $return;
}

function save_parsing_img($url, $dir, $pt_size, $bt_idx, $img_num) {
    $filename = '';

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_NOBODY, true);

    curl_exec ($ch);

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if($http_code == 200) {
        $filename = basename($url);
        $filename_ex = explode("?", $filename);
        $filename = $filename_ex[0];
        if(preg_match("/\.(gif|jpg|jpeg|png)$/i", $filename)) {
            //$tmpname = date('YmdHis').(microtime(true) * 10000);
            $tmpname = "pt_img_".$pt_size."_".$bt_idx."_".$img_num;
            $filepath = $dir;
            @mkdir($filepath, '0755');
            @chmod($filepath, '0755');

            // 파일 다운로드
            $path = $filepath.'/'.$tmpname;
            $fp = fopen ($path, 'w');

            $ch = curl_init();
            curl_setopt( $ch, CURLOPT_URL, $url );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, false );
            curl_setopt( $ch, CURLOPT_BINARYTRANSFER, true );
            curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
            curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
            curl_setopt( $ch, CURLOPT_FILE, $fp );
            curl_exec( $ch );
            curl_close( $ch );

            fclose( $fp );

            // 다운로드 파일이 이미지인지 체크
            if(is_file($path)) {
                $size = @getimagesize($path);
                if($size[2] < 1 || $size[2] > 3) {
                    @unlink($path);
                    $filename = '';
                } else {
                    $ext = array(1=>'gif', 2=>'jpg', 3=>'png');
                    $filename = $tmpname.'.'.$ext[$size[2]];
                    rename($path, $filepath.'/'.$filename);
                    //@chmod($filepath.'/'.$filename, '0644');
                }
            }
        }
    }

    return $filename;
}

function save_owner_img($url, $dir, $pt_barcode, $pt_idx) {
    $rtn_filename = '';

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_NOBODY, true);

    curl_exec ($ch);

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if($http_code == 200) {
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_BINARYTRANSFER, true );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch, CURLOPT_HEADER, false );
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 3 );
        $raw = curl_exec( $ch );
        curl_close( $ch );

        if(stristr($url, 'product_image.php')) {
            $url_ex = explode("?img=", $url);
            $filename = $url_ex[1];
        } else {
            $url_info = pathinfo($url);
            $filename = $url_info[basename];
        }

        $path = $dir."/".$filename;

        $fp = fopen ($path, 'w');
        fwrite($fp, $raw);
        fclose( $fp );

        if(is_file($path)) {
            $size = @getimagesize($path);
            if($size[2] < 1 || $size[2] > 3) {
                @unlink($path);
                $rtn_filename = '';
            } else {
                $ext = array(1=>'gif', 2=>'jpg', 3=>'png');
                $rtn_filename = $pt_barcode."_".$pt_idx.'.'.$ext[$size[2]];
                rename($path, $dir.'/'.$rtn_filename);
            }
        }
    }

    return $rtn_filename;
}

function get_pt_file_url($pt_file, $mng_chk="") {
    global $ct_noimg_url, $ct_product_url, $ct_product_dir_a, $ct_product_dir_r;

    $pt_file_ex = explode("|", $pt_file);

    if($pt_file_ex[0]=="http") {
        $pt_file_ex_txt = strip_tags($pt_file_ex[1]);
    } else {
        if($mng_chk=="Y") {
            $pt_dir = $ct_product_dir_a;
        } else {
            $pt_dir = $ct_product_dir_r;
        }

        if(is_file($pt_dir."/".$pt_file_ex[0])) {
            $pt_file_ex_txt = $ct_product_url."/".$pt_file_ex[0];
        } else {
            $pt_file_ex_txt = $ct_noimg_url;
        }
    }

    return $pt_file_ex_txt;
}

function get_file_url($file_nm)
{
    global $ct_no_img_url, $ct_editor_url, $ct_editor_dir;

    if ($file_nm=="http") {
        $rtn = strip_tags($file_nm);
    } else {
        if (is_file($ct_editor_dir."/".$file_nm)) {
            $rtn = $ct_editor_url."/".$file_nm;
        } else {
            $rtn = $ct_no_img_url;
        }
    }

    return $rtn;
}

function save_url_img($url, $dir, $tmp_nm) {
    $filename = '';

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_NOBODY, true);

    curl_exec ($ch);

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if($http_code == 200) {
        $filename = basename($url);
        $filename_ex = explode("?", $filename);
        $filename = $filename_ex[0];
        if(preg_match("/\.(gif|jpg|jpeg|png)$/i", $filename)) {
            $tmpname = $tmp_nm;
            $filepath = $dir;
//				@mkdir($filepath, '0755');
//				@chmod($filepath, '0755');

            // 파일 다운로드
            $path = $filepath.'/'.$tmpname;
            $fp = fopen ($path, 'w');

            $ch = curl_init();
            curl_setopt( $ch, CURLOPT_URL, $url );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, false );
            curl_setopt( $ch, CURLOPT_BINARYTRANSFER, true );
            curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
            curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 3 );
            curl_setopt( $ch, CURLOPT_FILE, $fp );
            curl_exec( $ch );
            curl_close( $ch );

            fclose( $fp );

            // 다운로드 파일이 이미지인지 체크
            if(is_file($path)) {
                $size = @getimagesize($path);
                if($size[2] < 1 || $size[2] > 3) {
                    @unlink($path);
                    $filename = '';
                } else {
                    $ext = array(1=>'gif', 2=>'jpg', 3=>'png');
                    $filename = $tmpname.'.'.$ext[$size[2]];
                    rename($path, $filepath.'/'.$filename);
                    //@chmod($filepath.'/'.$filename, '0644');
                }
            }
        }
    }

    return $filename;
}

function f_curl_post($url, $code) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "selfcode=".$code);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (iPhone; CPU iPhone OS 9_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $rtn = curl_exec($ch);
    curl_close($ch);

    return $rtn;
}

function f_curl_post_field($url, $field) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $field);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (iPhone; CPU iPhone OS 9_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $rtn = curl_exec($ch);
    curl_close($ch);

    return $rtn;
}

function ex_title_chk($title) {
    global $arr_ex_title;

    $q = 0;
    foreach($arr_ex_title as $key => $val) {
        if(strstr($title, $val)) {
            $q++;
        }
    }

    if($q>0) {
        return "";
    } else {
        return $title;
    }
}

function get_time() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

function format_phone($phone) {
    $phone = preg_replace("/[^0-9]/", "", $phone);
    $length = strlen($phone);

    switch($length){
        case 11 :
            return preg_replace("/([0-9]{3})([0-9]{4})([0-9]{4})/", "$1-$2-$3", $phone);
            break;
        case 10:
            return preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "$1-$2-$3", $phone);
            break;
        case 9:
            return preg_replace("/([0-9]{2})([0-9]{3})([0-9]{4})/", "$1-$2-$3", $phone);
            break;
        default :
            return $phone;
            break;
    }
}

function format_biz_number($value) {
    $value = preg_replace("/[^0-9]/", "", $value);
    return preg_replace("/([0-9]{3})([0-9]{2})([0-9]{5})/", "$1-$2-$3", $value);
}

function delete_all($dir) {
    $d = @dir($dir);
    while ($entry = $d->read()) {
        if ($entry == "." || $entry == "..") continue;
        if (is_dir($entry)) delete_all($entry);
        else unlink($dir."/".$entry);
    }
}

function f_sms_send($receiver, $msg, $subject="", $rdate="", $rtime="") {
    $sms_url = "https://apis.aligo.in/send/";
    $sms['user_id'] = ALIGO_USER_ID;
    $sms['key'] = ALIGO_KEY;

    if (ALIGO_USER_ID && $receiver) {
        $host_info = explode("/", $sms_url);
        $port = $host_info[0] == 'https:' ? 443 : 80;

        $sms['msg'] = stripslashes($msg);
        $sms['receiver'] = $receiver;
        $sms['destination'] = '';
        $sms['sender'] = ALIGO_SENDER;
        $sms['rdate'] = $rdate;
        $sms['rtime'] = $rtime;
        $sms['testmode_yn'] = '';
        $sms['title'] = $subject;
        $sms['msg_type'] = 'SMS';

        $oCurl = curl_init();
        curl_setopt($oCurl, CURLOPT_PORT, $port);
        curl_setopt($oCurl, CURLOPT_URL, $sms_url);
        curl_setopt($oCurl, CURLOPT_POST, 1);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS, $sms);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
        $ret = curl_exec($oCurl);
        curl_close($oCurl);

        return json_decode($ret);
    }
}

function f_kalim_token() {
    $_apiURL	  =	'https://kakaoapi.aligo.in/akv10/token/create/30/s/';
    $_hostInfo	  =	parse_url($_apiURL);
    $_port		  =	(strtolower($_hostInfo['scheme']) == 'https') ? 443 : 80;
    $_variables	=	array(
        'apikey' => ALIGO_KEY,
        'userid' => ALIGO_USER_ID,
    );

    $oCurl = curl_init();
    curl_setopt($oCurl, CURLOPT_PORT, $_port);
    curl_setopt($oCurl, CURLOPT_URL, $_apiURL);
    curl_setopt($oCurl, CURLOPT_POST, 1);
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($oCurl, CURLOPT_POSTFIELDS, http_build_query($_variables));
    curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);

    $ret = curl_exec($oCurl);
    $error_msg = curl_error($oCurl);
    curl_close($oCurl);

    // JSON 문자열 배열 변환
    $retArr = json_decode($ret);

    // 결과값 출력
    return $retArr;
}
function f_kalim_auth($token) {
    $_apiURL	  =	'https://kakaoapi.aligo.in/akv10/profile/auth/';
    $_hostInfo	=	parse_url($_apiURL);
    $_port		  =	(strtolower($_hostInfo['scheme']) == 'https') ? 443 : 80;
    $_variables	=	array(
        'apikey' 		=> ALIGO_KEY,
        'userid' 		=> ALIGO_USER_ID,
        'token'       	=> $token,
        'plusid'      	=> ALIGO_CHANNEL_ID,
        'phonenumber' 	=> ALIGO_SENDER,
    );

    $oCurl = curl_init();
    curl_setopt($oCurl, CURLOPT_PORT, $_port);
    curl_setopt($oCurl, CURLOPT_URL, $_apiURL);
    curl_setopt($oCurl, CURLOPT_POST, 1);
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($oCurl, CURLOPT_POSTFIELDS, http_build_query($_variables));
    curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);

    $ret = curl_exec($oCurl);
    $error_msg = curl_error($oCurl);
    curl_close($oCurl);

    // JSON 문자열 배열 변환
    $retArr = json_decode($ret);

    // 결과값 출력
    return $retArr;
}
function f_kalim_category($token) {
    $_apiURL	  =	'https://kakaoapi.aligo.in/akv10/category/';
    $_hostInfo	=	parse_url($_apiURL);
    $_port		  =	(strtolower($_hostInfo['scheme']) == 'https') ? 443 : 80;
    $_variables	=	array(
        'apikey' 		=> ALIGO_KEY,
        'userid' 		=> ALIGO_USER_ID,
        'token'       	=> $token,
    );

    $oCurl = curl_init();
    curl_setopt($oCurl, CURLOPT_PORT, $_port);
    curl_setopt($oCurl, CURLOPT_URL, $_apiURL);
    curl_setopt($oCurl, CURLOPT_POST, 1);
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($oCurl, CURLOPT_POSTFIELDS, http_build_query($_variables));
    curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);

    $ret = curl_exec($oCurl);
    $error_msg = curl_error($oCurl);
    curl_close($oCurl);

    // JSON 문자열 배열 변환
    $retArr = json_decode($ret);

    // 결과값 출력
    return $retArr;
}
function f_kalim_profile_add($token,$num) {
    $_apiURL    =	'https://kakaoapi.aligo.in/akv10/profile/add/';
    $_hostInfo	=	parse_url($_apiURL);
    $_port		  =	(strtolower($_hostInfo['scheme']) == 'https') ? 443 : 80;
    $_variables	=	array(
        'apikey' 		=> ALIGO_KEY,
        'userid' 		=> ALIGO_USER_ID,
        'token'       	=> $token,
        'plusid'		=> ALIGO_CHANNEL_ID,
        'authnum'		=> $num,
        'phonenumber'   => ALIGO_SENDER,
        'categorycode'  => '0060005', //
    );

    $oCurl = curl_init();
    curl_setopt($oCurl, CURLOPT_PORT, $_port);
    curl_setopt($oCurl, CURLOPT_URL, $_apiURL);
    curl_setopt($oCurl, CURLOPT_POST, 1);
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($oCurl, CURLOPT_POSTFIELDS, http_build_query($_variables));
    curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);

    $ret = curl_exec($oCurl);
    $error_msg = curl_error($oCurl);
    curl_close($oCurl);

    // JSON 문자열 배열 변환
    $retArr = json_decode($ret);

    // 결과값 출력
    return $retArr;
}
function f_kalim_profile_list($token) {
    $_apiURL		=	'https://kakaoapi.aligo.in/akv10/profile/list/';
    $_hostInfo	=	parse_url($_apiURL);
    $_port		  =	(strtolower($_hostInfo['scheme']) == 'https') ? 443 : 80;
    $_variables	=	array(
        'apikey' 		=> ALIGO_KEY,
        'userid' 		=> ALIGO_USER_ID,
        'token'       	=> $token,
        'plusid'		=> ALIGO_CHANNEL_ID,
    );

    $oCurl = curl_init();
    curl_setopt($oCurl, CURLOPT_PORT, $_port);
    curl_setopt($oCurl, CURLOPT_URL, $_apiURL);
    curl_setopt($oCurl, CURLOPT_POST, 1);
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($oCurl, CURLOPT_POSTFIELDS, http_build_query($_variables));
    curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);

    $ret = curl_exec($oCurl);
    $error_msg = curl_error($oCurl);
    curl_close($oCurl);

    // JSON 문자열 배열 변환
    $retArr = json_decode($ret);

    // 결과값 출력
    return $retArr;
}
function f_kalim_template_list($token) {
    $_apiURL		=	'https://kakaoapi.aligo.in/akv10/template/list/';
    $_hostInfo	=	parse_url($_apiURL);
    $_port		  =	(strtolower($_hostInfo['scheme']) == 'https') ? 443 : 80;
    $_variables	=	array(
        'apikey' 		=> ALIGO_KEY,
        'userid' 		=> ALIGO_USER_ID,
        'token'       	=> $token,
        'senderkey' 	=> ALIGO_CHANNEL_KEY,
    );

    $oCurl = curl_init();
    curl_setopt($oCurl, CURLOPT_PORT, $_port);
    curl_setopt($oCurl, CURLOPT_URL, $_apiURL);
    curl_setopt($oCurl, CURLOPT_POST, 1);
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($oCurl, CURLOPT_POSTFIELDS, http_build_query($_variables));
    curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);

    $ret = curl_exec($oCurl);
    $error_msg = curl_error($oCurl);
    curl_close($oCurl);

    // JSON 문자열 배열 변환
    $retArr = json_decode($ret);

    // 결과값 출력
    return $retArr;
}
function f_kalim_send($token, $type, $ref_idx="") {
    global $DB, $setup_info, $arr_template_code, $arr_ot_type;

    $tpl_code = $arr_template_code[$type];
    $subject = "";
    $message = "";
    $button = "";
    $link = "";
    $receiver_hp = "";

    if ($tpl_code) {
        switch ($type) {
            case 'orderFin_seller':
                $DB->where("ot_code", $ref_idx);
                $row_ot = $DB->getone("order_t");

                $DB->where("mt_idx", $row_ot['seller_mt_idx']);
                $row_mt = $DB->getone("member_t");

                $receiver_hp = $row_mt['mt_hp'];
                $button = "주문확인";
                $link = CDN_SELLER_HTTP.'/order_detail.php?ot_code='.$ref_idx;

                $subject = "[".APP_AUTHOR."] 신규 주문이 도착했어요!";
                $message = $subject."\r\n";
                $message .= "\r\n■ 주문상점: ".$row_ot['st_name'];
                $message .= "\r\n■ 주문번호: ".$arr_ot_type[$row_ot['ot_type']]." ".$ref_idx;
                $message .= "\r\n■ 상품명: ".$row_ot['ot_title'];
                $message .= "\r\n■ 주문일시: ".DateType($row_ot['ot_pdate'], 5);
                $message .= "\r\n■ 주문금액: ".number_format($row_ot['ot_price'])."원";
                $message .= "\r\n\r\n자세한 내용은 ".APP_AUTHOR."사장님 > 주문관리에서 확인 부탁드립니다.";
                break;
        }

        $main_scheme = 'meatriderApp';
        $seller_scheme = 'meatriderSellerApp';

        $button_app_link = $seller_scheme.'://'.$link;//f_dynamiclinks($link);

        $receiver_hp = str_replace(' ','', $receiver_hp);
        $receiver_hp = str_replace('.','', $receiver_hp);
        $receiver_hp = str_replace('-','', $receiver_hp);
        if ($receiver_hp) {
            $_apiURL	  =	'https://kakaoapi.aligo.in/akv10/alimtalk/send/';
            $_hostInfo	=	parse_url($_apiURL);
            $_port		  =	(strtolower($_hostInfo['scheme']) == 'https') ? 443 : 80;
            $_variables	=	array(
                'apikey' 	  => ALIGO_KEY,
                'userid' 	  => ALIGO_USER_ID,
                'token'       => $token,
                'senderkey'   => ALIGO_CHANNEL_KEY,
                'tpl_code'    => $tpl_code,
                'sender'      => ALIGO_SENDER,
                'receiver_1'  => $receiver_hp,
                'subject_1'   => $subject,
                'message_1'   => $message,
                //'button_1'    => ,
                'testMode'   => 'N',
            );

            if ($button) {
                $appBtn = '{"name":"'.$button.'(APP)","linkType":"AL","linkIos":"'.$button_app_link.'","linkAnd":"'.$button_app_link.'"}';
                $webBtn = '{"name":"'.$button.'(WEB)","linkType":"WL","linkP":"'.$link.'", "linkM": "'.$link.'"}';
                $_variables['button_1'] = '{"button":['.$appBtn.','.$webBtn.']}';
            }

            $oCurl = curl_init();
            curl_setopt($oCurl, CURLOPT_PORT, $_port);
            curl_setopt($oCurl, CURLOPT_URL, $_apiURL);
            curl_setopt($oCurl, CURLOPT_POST, 1);
            curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($oCurl, CURLOPT_POSTFIELDS, http_build_query($_variables));
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);

            $ret = curl_exec($oCurl);
            $error_msg = curl_error($oCurl);
            curl_close($oCurl);

            // JSON 문자열 배열 변환
            $retArr = json_decode($ret);

            // 결과값 출력
            return $retArr;
        } else {
            return null;
        }
    }
}

function f_dynamiclinks($params){
    $web_api_key = 'AIzaSyBVq_O_-YbnKlAB-6edG59ZlA799_Wp0gE';
    if ($web_api_key) {
        $url = 'https://firebasedynamiclinks.googleapis.com/v1/shortLinks?key=' . $web_api_key;
        $data = array(
            "dynamicLinkInfo" => array(
                "dynamicLinkDomain" => "meatrider.page.link",
                "link" => APP_DOMAIN.$params,//APP_DOMAIN.'/?'.$params,
                "androidInfo" => array(
                    "androidPackageName" => "com.dmonster.meatrider",
                ),
                "iosInfo" => array(
                    "iosBundleId" => "com.dmonster.meatrider",
                ),
            )
        );

        $headers = array('Content-Type: application/json');

        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_POST, true );
        curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, json_encode($data) );

        $data = curl_exec ( $ch );
        curl_close ( $ch );

        $short_url = json_decode($data);
        if(isset($short_url->error)){
            return $short_url->error->message;
        } else {
            return $short_url->shortLink;
        }
    }
}

//$result 성공: true, 실패: false  $msg 전달 문구  $data 배열 데이터
function result_data($result, $msg, $data) {
    $arr = array();

    $arr['result'] = $result;
    $arr['msg'] = $msg;
    $arr['data'] = $data;

    $obj = json_encode($arr, JSON_UNESCAPED_UNICODE);

    return $obj;
}

function recusive_category($_level, $_pid) {
    global $DB;

    unset($list);
    $DB->where('ct_level', $_level);
    $DB->where('ct_pid', $_pid);
    $DB->orderBy('ct_rank', 'asc')->orderBy('ct_id', 'asc')->orderBy('ct_name', 'asc');
    $list = $DB->get('category_t');

    if($list) {
        foreach($list as $row) {
            $s_level = "";
            if($row['ct_level']) {
//					$s_level = "&nbsp;&nbsp;&nbsp;┗";
//					for($i=1; $i<$row['ct_level']; $i++) $s_level = "&nbsp;&nbsp;&nbsp;".$s_level;
                $s_level = '<span style="padding-left: '.($row['ct_level']*10).'px"></span>┗';
            }

            $ct_name_t = get_text($row['ct_name']);

            $s_add = $s_mod = $s_del = "";
            if ((int)$row['ct_level']<1) {
                //$s_add = "<a href='./category_form.php?act=add&ct_idx=".$row['ct_id']."&ct_level=".$row['ct_level']."' class='btn btn-outline-secondary btn-sm mx-sm-1'>추가</a>";
                $s_add = "<a href=\"javascript:void(layerPop('set_category', '', 'add', '".$row['ct_id']."'))\" class='btn btn-outline-secondary btn-sm mx-sm-1'>추가</a>";
            }
            //$s_mod = "<a href='./category_form.php?act=update&ct_idx=".$row['ct_id']."' class='btn btn-outline-primary btn-sm mx-sm-1'>수정</a>";
            $s_mod = "<a href=\"javascript:void(layerPop('set_category', '', 'update', '".$row['ct_id']."'))\" class='btn btn-outline-primary btn-sm mx-sm-1'>수정</a>";
            $s_del = "<a href='javascript:;' onclick=\"f_post_del('./category_update.php','".$row['ct_id']."')\" class='btn btn-outline-danger btn-sm mx-sm-1'>삭제</a>";

            echo "<tr>
						<td>".$s_level." ".$ct_name_t."</td>
						<td>".$row['ct_level']."</td>
						<td>".$row['ct_rank']."</td>
                        <td><div class='d-flex justify-content-end'>".$s_add."".$s_mod."".$s_del."</div></td>
					</tr>";

            recusive_category($row['ct_level']+1, $row['ct_id']);
        }
    }

    return false;
}

function recusive_category1($cate, $_level, $_pid) {
    global $DB;

    unset($list);
    $DB->where('ct_level', $_level);
    $DB->where('ct_pid', $_pid);
    $DB->orderBy('ct_rank', 'asc')->orderBy('ct_id', 'asc')->orderBy('ct_name', 'asc');
    $list = $DB->get("category_{$cate}_t");

    if($list) {
        foreach($list as $row) {
            $s_level = "";
            if($row['ct_level']) {
//					$s_level = "&nbsp;&nbsp;&nbsp;┗";
//					for($i=1; $i<$row['ct_level']; $i++) $s_level = "&nbsp;&nbsp;&nbsp;".$s_level;
                $s_level = '<span style="padding-left: '.($row['ct_level']*10).'px"></span>┗';
            }

            $ct_name_t = get_text($row['ct_name']);

            $s_add = "";
            $s_mod = "<a href='./category_".$cate."_form.php?act=update&ct_idx=".$row['ct_id']."' class='btn btn-outline-primary btn-sm mx-sm-1'>수정</a>";
            $s_del = "<a href='javascript:;' onclick=\"f_post_del('./category_".$cate."_update.php','".$row['ct_id']."')\" class='btn btn-outline-danger btn-sm mx-sm-1'>삭제</a>";

            if ($cate==='return') {
                $s_del = "";
            }
            echo "<tr>";
            echo 	"<td>".$s_level." ".$ct_name_t."</td>";
            if ($cate==='main') {
                echo 	"<td>".(!$row['ct_level']?'':'<div class="media product_list_media"><img src="'.get_image_url($row['ct_icon']).'" alt="" /></div>')."</td>";
                echo 	"<td>".$row['ct_url']."</td>";
            }
            if ($cate==='main' && !$row['ct_level']) {
                $s_mod = $s_del = "";
                $s_add = "<a href=\"javascript:void(layerPop('set_category', '_".$cate."', 'add', '".$row['ct_id']."'))\" class='btn btn-outline-secondary btn-sm mx-sm-1'>추가</a>";
            }
            echo 	"<td>".$row['ct_rank']."</td>";
            if (($cate==='faq' || $cate==='qna' || $cate==='main' || $cate==='style') && $row['ct_show']) {
                echo "<td>";
                if ($cate==='main' && !$row['ct_level']) {
                } else {
                    echo '<label class="switch-button"><input type="checkbox" class="intercept" name="ct_show" data-tbl="category_'.$cate.'_t" data-idx="'.$row['ct_id'].'" value="'.$row['ct_show'].'" '.($row['ct_show']==='Y'?'checked':'').' /> <span class="onoff-switch"></span></label>';
                }
                echo "</td>";
            }
            echo 	"<td><div class='d-flex justify-content-end'>".$s_add."".$s_mod."".$s_del."</div></td>";
            echo "</tr>";

            recusive_category1($cate, $row['ct_level']+1, $row['ct_id']);
        }
    }

    return false;
}

function recusive_category2($cate="", $_level, $_pid, $_ppid='') {
    global $DB;

    unset($list);
    $DB->where('ct_level', $_level);
    if ($_pid) { $DB->where('ct_pid', $_pid); }
    if ($_ppid) { $DB->where('ct_tid', $_ppid); }
    $DB->orderBy('ct_rank', 'asc')->orderBy('ct_id', 'asc')->orderBy('ct_name', 'asc');
    $list = $DB->get("category{$cate}_t");

    if($list) {
        foreach($list as $row) {
            $s_level = "";
            if($row['ct_level'] > 1 && !$_ppid) {
                //					$s_level = "&nbsp;&nbsp;&nbsp;┗";
                //					for($i=1; $i<$row['ct_level']; $i++) $s_level = "&nbsp;&nbsp;&nbsp;".$s_level;
                $s_level = '<span style="padding-left: '.($row['ct_level']*10).'px"></span>┗';
            }

            $ct_name_t = get_text($row['ct_name']);

            $s_add = "";
            if($row['ct_level']==0 || $row['ct_level']==1) {
                $s_add = "<a href=\"javascript:void(layerPop('set_category', '".$cate."', 'add', '".$row['ct_id']."'))\" class='btn btn-outline-secondary btn-sm mx-sm-1'>추가</a>";
            }
            $s_mod = "<a href=\"javascript:void(layerPop('set_category', '".$cate."', 'update', '".$row['ct_id']."'))\" class='btn btn-outline-primary btn-sm mx-sm-1'>수정</a>";
            //$s_del = "<a href='javascript:;' onclick=\"f_proc_del('delete_category', '".$row['ct_id']."', '".$cate."')\" class='btn btn-outline-danger btn-sm mx-sm-1'>삭제</a>";
            $s_del = "<a href='javascript:;' onclick=\"f_post_del('./category{$cate}_update.php','".$row['ct_id']."')\" class='btn btn-outline-danger btn-sm mx-sm-1'>삭제</a>";

            //$cbt = $DB->fetch_query("select COUNT(*) AS cnt from category{$cate}_t where ct_level = '".($_level-1)."' AND ct_pid = '".$row['ct_tid']."' ORDER BY ct_rank, ct_id");
            $DB->where('ct_level', ($_level-1))->where('ct_pid', $row['ct_tid']);
            $DB->orderBy("ct_rank","asc")->orderBy("ct_id","asc");
            $cbt = $DB->getone("category{$cate}_t", 'COUNT(*) AS cnt');

            $_class = '';
            if ($cate==='_charge') {
                if($row['ct_level']=='1') { $_class = ' class="table-primary"'; }
                if($row['ct_level']=='2') { $_class = ' class="table-secondary"'; }
            }

            echo "<tr".$_class.">";
            //echo 	"<td>".recusive_ca_name($row['ct_pid'], $cate)."</td>";
            if ($_ppid && $cbt['cnt']) {
                $ca_pname_t = recusive_ca_name($row['ct_pid'], $cate);
                $arr_ca_pname_t = explode('|', $ca_pname_t);
                echo 	"<td>".$arr_ca_pname_t[0]."</td>";
            }
            echo 	"<td>".$s_level." ".$ct_name_t."</td>";
            echo 	"<td>".$row['ct_level']."</td>";
            echo 	"<td>".$row['ct_rank']."</td>";
            if ($row['ct_show']) { echo "<td>".$row['ct_show']."</td>"; }
            echo 	"<td><div class='d-flex justify-content-end'>".$s_add."".$s_mod."".$s_del."</div></td>";
            echo "</tr>";

            recusive_category2($cate, $row['ct_level']+1, $row['ct_id']);
        }
    }

    return false;
}

function recusive_ca_name($ct_id) {
    global $DB;

    //$query = "select * from category_t where ct_id = '".$ct_id."'";
    //$row = $DB->fetch_query($query);
    $DB->where('ct_id', $ct_id);
    $row = $DB->getone("category_t");
    if ($row) {
        if($row['ct_pid']=='0') {
            return $row['ct_name'];
        } else {
            return $row['ct_name']."|".recusive_ca_name($row['ct_pid']);
        }
    } else { return ""; }
}

function recusive_ca_id($ct_id) {
    global $DB;

    //$query = "select * from category_t where ct_id = '".$ct_id."'";
    //$row = $DB->fetch_query($query);
    $DB->where('ct_id', $ct_id);
    $row = $DB->getone("category_t");

    if($row['ct_pid']=='0') {
        return $row['ct_id'];
    } else {
        return $row['ct_id']."|".recusive_ca_id($row['ct_pid']);
    }
}

function get_ca_name_breadcrumb($ct_id) {
    $ca_name_t = recusive_ca_name($ct_id);
    $ca_name_t_ex = explode('|', $ca_name_t);
    krsort($ca_name_t_ex);
    $ca_name_t_ex_im = implode(' > ', $ca_name_t_ex);

    return $ca_name_t_ex_im;
}

function get_ca_name_breadcrumb_short($ct_id) {
    $ca_name_t = recusive_ca_name($ct_id);
    $ca_name_t_ex = explode('|', $ca_name_t);
    krsort($ca_name_t_ex);

    $ca_name_t_ex_arr = array();
    $q = 0;
    foreach($ca_name_t_ex as $key => $val) {
        if($q<2) {
            $ca_name_t_ex_arr[] = $val;
        }
        $q++;
    }

    $ca_name_t_ex_im = implode(' > ', $ca_name_t_ex_arr);

    return $ca_name_t_ex_im;
}

function get_product_info($pt_idx, $_table="product_t") {
    global $DB, $ct_img_url, $pt_image_num;

    unset($arr_rtn);

    //상품기본정보
    $DB->where('idx', $pt_idx);
    $row1 = $DB->getone("product_t", '*, idx as pt_idx');

    if($row1['pt_sale_type_chk']=='Y') {
        $row1['pt_discount'] = $row1['pt_discount_price'];
    } else {
        $row1['pt_discount'] = $row1['pt_discount_per'];
    }

    $row1['seller_idx'] = $row1['st_idx'];

    for($q=1;$q<=$pt_image_num;$q++) {
        if($row1['pt_image'.$q]!='') {
            $row1['pt_image'.$q.'_url'] = $ct_img_url.'/'.$row1['pt_image'.$q].'?ver='.date('ymdHis');
            $row1['pt_image'.$q.'_on'] = $row1['pt_image'.$q];
        }
    }

    if($row1=='') {
        $row1 = array();
    }

    $arr_rtn['product_t'] = $row1;

    return $arr_rtn;
}

function f_member_leave($mt_idx, $mt_level) {

}

function get_member($level='', $mt_id='', $mt_info=false) {
    global $DB, $setup_info, $ct_img_url, $arr_mt_login_type;

    $member = array();
    if ($mt_id) {
        if ($level == 'manager') {
            $DB->where('mt_id', $mt_id);
            $DB->where("mt_status", "Y")->where("mt_level >= '8'")->where("mt_rdate", "");
            $DB->orderBy('idx', 'DESC');
            $member = $DB->getone("member_t a1", "*, a1.idx as mt_idx");
        } else if ($level == 'seller') {
            $DB->where('mt_id', $mt_id);
            $DB->where("mt_status", "Y")->where("mt_level > '1'")->where("mt_rdate", "");
            $DB->where("(CASE WHEN mt_level = '4' THEN mt_seller = 'Y'
                              ELSE mt_level = '3' END)");
            $DB->orderBy('idx', 'DESC');
            $member = $DB->getone("member_t a1", "*, a1.idx as mt_idx");
        } else if ($level == 'user') {
            $DB->where('mt_id', $mt_id);
            $DB->where("mt_status", "Y")->where("mt_level", '2')->where("mt_rdate", "");
            $DB->orderBy('idx', 'DESC');
            $member = $DB->getone("member_t a1", "*, a1.idx as mt_idx");
        } else {
            $DB->where('mt_id', $mt_id);
            $DB->where("mt_status", "Y")->where("mt_level > '1'")->where("mt_rdate", "");
            $DB->orderBy('idx', 'DESC');
            $member = $DB->getone("member_t a1", "*, a1.idx as mt_idx");
        }
    }

    if ($mt_info) {
        if ($member['mt_level'] == '2') {
            $cart_num = get_ct_cnt();
            $member['cart_count'] = $cart_num;

            //$DB->where("a1.send_to", "%,".$member['mt_idx'].",%", "like");
            //$DB->where("a1.read_no", "%,".$member['mt_idx'].",%", "like");
            $DB->where("FIND_IN_SET('{$member['mt_idx']}', a1.send_to) > 0");
            $DB->where("FIND_IN_SET('{$member['mt_idx']}', a1.read_no) = 0");
            $DB->where("a1.pst_show", 'Y');
            $alim_num = $DB->getone("pushnotification_t a1", "COUNT(*) AS cnt");
            if($alim_num['cnt']>99) {
                $cnt_t = '99+';
            } else {
                $cnt_t = $alim_num['cnt'];
            }
            $member['alim_count'] = $cnt_t;

            $member['mt_point'] = number_format($member['mt_point']);

            $DB->where("a1.mt_idx", $member['mt_idx']);
            $DB->where("a1.rt_status", 'Y');
            $DB->where("a1.parent_id", '0');
            $review_count = $DB->getone("review_t a1", "COUNT(*) AS cnt");
            $member['review_count'] = number_format($review_count['cnt']);

            //$DB->where("(a1.mt_id IN ( '{$member['mt_id']}', '전체회원' ) OR FIND_IN_SET('{$member['mt_id']}', a1.mt_id) > 0)");
            $DB->where("(a1.mt_idx = '전체회원' OR FIND_IN_SET('{$member['mt_idx']}', a1.mt_idx) > 0)");
            $DB->where("(a1.cp_start <= '".TIME_YMD."' AND a1.cp_end >= '".TIME_YMD."')");
            $DB->where("(a1.cp_id NOT IN (SELECT a2.cp_id FROM coupon_log_t a2 WHERE mt_idx = '".$member['mt_idx']."'))");
            $coupon_count = $DB->getone("coupon_t a1", "COUNT(*) AS cnt");// 보유 쿠폰 수
            $member['coupon_count'] = number_format($coupon_count['cnt']);

            //$DB->where("a1.mt_idx", $member['mt_idx']);
            //$qna_count = $DB->getone("qna_t a1", "COUNT(*) AS cnt");

            //$DB->where("a1.mt_idx", $member['mt_idx']);
            //$DB->where("a1.ot_status != '0' AND a1.ot_status != '1'");
            //$order_count = $DB->getone("order_t a1", "COUNT(*) AS cnt");

            //$DB->where("a1.mt_idx", $member['mt_idx']);
            //$DB->where("a1.ot_status != '0' AND a1.ot_status != '1' AND a1.ot_read = 'N'");
            //$order_new_count = $DB->getone("order_t a1", "COUNT(*) AS cnt");

            if ($member['mt_login_type'] == '1') {
                $member['mt_join_event'] = '';
                $member['mt_join_status'] = 'Y';
            } else {
                $mt_join_event = array();
                if (!$member['mt_hp']) {
                    $add_point = calc_point('join', 1);
                    if ($add_point > 0) {
                        $DB->where('mt_idx', $member['mt_idx'])->where('rel_table', 'join')->where('rel_item', $member['mt_idx'])->where('plt_type', 'P');
                        $po = $DB->getone('point_log_t', 'COUNT(*) AS cnt');
                        if (!$po['cnt']) {
                            $mt_join_event[] = '포인트';
                        }
                    }

                    $cz_id = '1';
                    $cz_id = preg_replace('#[^0-9]#', '', $cz_id);
                    $DB->where('cz_id', $cz_id);
                    $DB->where('cz_show', 'Y');
                    $DB->where("(cz_start <= '".date('Y-m-d')."' AND cz_end >= '".date('Y-m-d')."')");
                    $cp = $DB->getone('coupon_zone_t');
                    if ($cp['cz_id']) {
                        if ($cp['cz_start'] <= date('Y-m-d') && $cp['cz_end'] >= date('Y-m-d')) {
                            if ($cp['cz_qty'] * 1 > 0) {
                                // 발급여부
                                if (!is_coupon_downloaded($member['mt_idx'], $cp['cz_id'])) {
                                    $mt_join_event[] = '쿠폰';
                                }
                            }
                        }
                    }
                }
                $member['mt_join_event'] = ($mt_join_event ? '내정보입력을 완료하시면 '.implode('&', $mt_join_event).'의 혜택이 지급됩니다!' : '');
                $member['mt_join_status'] = $mt_join_event ? 'N' : 'Y';
            }

            $DB->where("mt_idx", $member['mt_idx']);
            $DB->where("mat_default", 'Y');
            $DB->orderBy("mat_wdate", 'desc');
            $mat_t = $DB->getone("member_address_t a1", "*, a1.idx as mat_idx");
            $member['mat_t'] = $mat_t;

            $_user_add1 = $mat_t['idx'] ? ($mat_t['mat_add1']?$mat_t['mat_add1']:$mat_t['mat_add1_sub']) : $setup_info['st_default_add1'];
            $_user_add2 = $mat_t['idx'] ? $mat_t['mat_add2'] : $setup_info['st_default_add2'];
            $_user_lat = $mat_t['idx'] ? $mat_t['mat_lat'] : $setup_info['st_default_lat'];
            $_user_lng = $mat_t['idx'] ? $mat_t['mat_lng'] : $setup_info['st_default_lng'];
            $member['_user_add1'] = $_user_add1;
            $member['_user_add2'] = $_user_add2;
            $member['_user_lat'] = $_user_lat;
            $member['_user_lng'] = $_user_lng;
        }
    }

    return $member;
}

function get_guest(){ //비회원 정보
    global $DB, $setup_info, $ct_img_url;

    $guest = array();

    $DB->where("temp_mt_id", get_cookie('ck_guest_id'));
    $DB->where("mat_default", 'Y');
    $DB->orderBy("mat_wdate", 'desc');
    $mat_t = $DB->getone("member_address_t a1", "*, a1.idx as mat_idx");
    $guest['mat_t'] = $mat_t;

    $_user_add1 = $mat_t['idx'] ? ($mat_t['mat_add1']?$mat_t['mat_add1']:$mat_t['mat_add1_sub']) : $setup_info['st_default_add1'];
    $_user_add2 = $mat_t['idx'] ? $mat_t['mat_add2'] : $setup_info['st_default_add2'];
    $_user_lat = $mat_t['idx'] ? $mat_t['mat_lat'] : $setup_info['st_default_lat'];
    $_user_lng = $mat_t['idx'] ? $mat_t['mat_lng'] : $setup_info['st_default_lng'];
    $guest['_user_add1'] = $_user_add1;
    $guest['_user_add2'] = $_user_add2;
    $guest['_user_lat'] = $_user_lat;
    $guest['_user_lng'] = $_user_lng;

    /*if ($mat_t['idx']) {
        $guest['mat_t'] = $mat_t;
        $_user_add1 = ($mat_t['mat_add1']?$mat_t['mat_add1']:$mat_t['mat_add1_sub']);
        $_user_add2 = $mat_t['mat_add2'];
        $_user_lat = $mat_t['mat_lat'];
        $_user_lng = $mat_t['mat_lng'];
    } else {
        $_user_add1 = $setup_info['st_default_add1'];
        $_user_add2 = $setup_info['st_default_add2'];
        $_user_lat = $setup_info['st_default_lat'];
        $_user_lng = $setup_info['st_default_lng'];
    }
    $guest['_user_add1'] = $_user_add1;
    $guest['_user_add2'] = $_user_add2;
    $guest['_user_lat'] = $_user_lat;
    $guest['_user_lng'] = $_user_lng;*/

    return $guest;
}
function set_guest_info($mt_idx){//비회원일때 저장한 정보 회원정보에 업데이트
    global $DB;
    if (get_cookie('ck_guest_id') && $mt_idx) {
        /*if ($_uid) {
            // 비회원이었을때 담은 장바구니 업데이트
            unset($arr_query);
            $arr_query = array(
                'mt_idx' => $mt_idx,
            );
            $DB->where('mt_idx', '');
            $DB->where('ot_code', $_uid)->where('temp_mt_id', get_cookie('ck_guest_id'));
            $DB->update("cart_t", $arr_query);
        }*/

        //--------------------------------------------------------------------------------------------------
        // 비회원이었을때 담은 주소설정 업데이트
        unset($arr_query);
        $arr_query = array(
            'mt_idx' => $mt_idx,
        );
        $DB->where("(mt_idx = '' OR mt_idx IS NULL)");
        $DB->where('temp_mt_id', get_cookie('ck_guest_id'));
        $DB->update("member_address_t", $arr_query);

        $mat_idx = '';
        $DB->where("mt_idx", $mt_idx);
        $DB->where("mat_default", 'Y');
        $DB->orderBy("mat_wdate", "DESC");
        $list_mat = $DB->get("member_address_t");
        foreach ($list_mat as $row_mat) {
            if (!$mat_idx) {
                $mat_idx = $row_mat['idx'];
            }
        }
        if ($DB->count > 1 && $mat_idx) {
            unset($arr_query);
            $arr_query = array(
                'mat_default' => 'N',
            );
            $DB->where("mt_idx", $mt_idx);
            $DB->where("mat_default", 'Y');
            $DB->where("idx != '".$mat_idx."'");
            $DB->update("member_address_t", $arr_query);
        }
        //--------------------------------------------------------------------------------------------------
    }
}

function get_member_id($mt_id, $row=array()) {
    global $DB, $arr_mt_login_type;

    $shtml = $row['mt_login_type']!='1' ? cut_str($mt_id, 0, 5, '..').' ('.$arr_mt_login_type[$row['mt_login_type']].'연동계정)' : $mt_id;

    return $shtml;
}

function get_mem_info($field, $chk) {
    global $DB, $ct_no_profile_url, $member_img_dir, $member_img_url;

    //$DB->where("a1.mt_level > '1'");
    $DB->where("a1.$field", $chk);
    $row = $DB->getone("member_t a1", "*, a1.idx AS mt_idx");

    $profile = $ct_no_profile_url;
    if(!empty($row['mt_image1'])) {
      $filepath = $member_img_dir . $row['mt_image1'];
      if(file_exists($filepath)) {
        $profile = $member_img_url . $row['mt_image1'];
      }
    }
    $row['profile'] = $profile;

    return $row;
}
function get_seller_info($row, $style_gubun="", $style_class="") {
    global $DB, $ct_no_profile_url, $thumb_wd, $thumb_wd;

    $seller_name = $row['st_name'];

    $seller_image = '';
    if ($row['st_image1']) {
        $seller_image = get_list_thumbnail($row['st_image1'], '', $thumb_wd, $thumb_wd);
        if (!$seller_image) {
            $seller_image = get_image_url($row['st_image1'], $ct_no_profile_url);
        }
    } else {
        $seller_image = $ct_no_profile_url;
    }

    $shtml = "";
    if ($style_gubun === 'mng') {
        $seller_link = './store_form.php?act=update&st_idx='.$row['st_idx'];
        $shtml .= '<div class="profile_info">';
        $shtml .=   '<div class="left"><img src="'.$seller_image.'" class="profile_img" alt="" /></div>';
        $shtml .=   '<div class="right text-left"><a href="'.$seller_link.'" target="_blank" class="text-primary">'.$seller_name.'</a></div>';
        $shtml .= '</div>';
    } else {
        $seller_link = './shop_detail.php?idx='.$row['st_idx'];
        if ($style_gubun === 'main') {
            $seller_image = '';
            if ($row['st_boss_image1']) {
                $seller_image = get_list_thumbnail($row['st_boss_image1'], '', $thumb_wd, $thumb_wd);
                if (!$seller_image) {
                    $seller_image = get_image_url($row['st_boss_image1'], $ct_no_profile_url);
                }
            } else {
                $seller_image = $ct_no_profile_url;
            }
        }

        // 리뷰
        $DB->where("a1.st_idx", $row['st_idx']);
        $DB->where("a1.rt_status", "Y");
        $DB->where("a1.parent_id", '0');
        $row_rt = $DB->getone("review_t a1", "COUNT(*) AS cnt, avg(rt_score) as avg_score");
        $reviewCnt = number_format($row_rt['cnt']);
        $reviewAvg = $row_rt['cnt'] ? number_format($row_rt['avg_score'], 1) : 0;

        $row['reviewCnt'] = $reviewCnt;
        $row['reviewAvg'] = $reviewAvg;
        /*if ($row['st_type'] != 'C') {

        } else {
            if($row['scdt_price_type']=='1') { //무료
                $row['st_send_cost'] = '0원';
            } else if($row['scdt_price_type']=='2') { //조건부무료
                $row['st_send_cost'] = '0원~'.number_format($row['scdt_price']).'원';
            } else if($row['scdt_price_type']=='3') { //유료
                $row['st_send_cost'] = number_format($row['scdt_price']).'원';
            }
        }*/

        if ($row['st_send_cost_show']!='N') {
            $d_info = get_store_t_delivery_info($row);
            if ($d_info['ct_delivery_chk']) {
                if ($d_info['min'] && $d_info['max']) {
                    $row['st_send_cost'] = number_format($d_info['min']).'원 ~ '.number_format($d_info['max']).'원';
                } else if ($d_info['max']) {
                    $row['st_send_cost'] = '0원 ~ '.number_format($d_info['max']).'원';
                } else {
                    $row['st_send_cost'] = '0원';
                }
            } else {
                $row['st_send_cost'] = $d_info['ct_delivery_chk_msg'];
            }
        }

        $shtml .= '<div class="item media'.$style_class.'">';
        $shtml .=   '<div class="thum mr_20">';
        $shtml .=     '<div class="rect rounded" style="width:8.0rem;"><img src="'.$seller_image.'" alt="" /></div>';
        $shtml .=   '</div>';
        $shtml .=   '<div class="item_body">';
        $shtml .=     '<div class="name line1_text">'.$seller_name.'</div>';
        if ($row['st_content_show']=='Y') {
            $shtml .= '<div class="intrd line1_text">'.$row['st_content'].'</div>';
        }
        $shtml .=     '<div class="info01">';
        $shtml .=       '<div class="if_sp">';
        $shtml .=         '<span class="ic_img ic_star mr_1"></span>';
        $shtml .=         '<span><b>'.$row['reviewAvg'].'점</b>('.$row['reviewCnt'].')</span>';
        $shtml .=       '</div>';
        if (isset($row['distance']) && $row['st_type'] != 'C') {
            $shtml .=   '<div class="if_sp">';
            $shtml .=     '<span class="ic_img ic_map"></span>';
            $shtml .=     '<span>거리 '.number_format($row['distance'],2).'km</span>';
            $shtml .=   '</div>';
        }
        $shtml .=     '</div>';
        $shtml .=     '<div class="info02">';
        if ($row['st_type'] == 'C') {
            $shtml .=   '<span class="badge badge-sm badge_parcel mr-2">택배</span>';
        }
        if ($row['st_send_cost_show']!='N') {
            $shtml .=   '<div class="if_sp"><span class="text-gray">'.($row['st_type'] != 'C' ? '배달비' : '배송비').'</span> '.$row['st_send_cost'].'</div>';
        }
        $shtml .=       '<div class="if_sp"><span class="text-gray">최소주문금액</span> '.number_format($row['st_min_price']).'원</div>';
        if ($row['st_type'] != 'C') {
            $shtml .=   '<div class="if_sp"><span class="text-gray">운영시간</span> '.check_hour($row).'</div>';
        }
        $shtml .=     '</div>';
        $shtml .=   '</div>';
        $shtml .= '</div>';
        $shtml .= '<a class="item_link" href="'.$seller_link.'"></a>';
    }
    return $shtml;
}

function get_store_t_delivery_info($row) {
    global $DB;

    $price = array();
    $shtml = '';
    $ct_delivery_chk = false;
    $ct_delivery_chk_msg = "";
    if ($row['st_type'] == 'A') {
        $sadt_if_price = 0;
        $DB->where('st_idx', $row['idx']);
        $DB->orderBy('sadt_if_price', 'desc');
        $list_dpt = $DB->get("store_delivery_info_t");
        foreach ($list_dpt as $row_dpt) {
            // 배대사 api
            $d_rtn = get_delivery_price($row, ($sadt_if_price ? $sadt_if_price : $row_dpt['sadt_if_price'])*1 - 1);
            $ct_delivery_chk = $d_rtn['ct_delivery_chk'];
            $ct_delivery_chk_msg = $d_rtn['ct_delivery_chk_msg'];
            $ct_delivery_default_price = $d_rtn['st_send_cost_total'];
            $ct_delivery_price = $ct_delivery_default_price;

            if ((int)$row['st_delivery_support_price'] > 0) { // 매장별 본사 부담액 차감
                $ct_delivery_price -= (int)$row['st_delivery_support_price'];
            }

            // 정육점 부담액 차감
            if ($row_dpt['sadt_price_set']) {
                $ct_delivery_price -= (int)$row_dpt['sadt_price_set'];
            } else {
                $ct_delivery_price -= (int)$row['sadt_price'];
            }
            if ($ct_delivery_price<0) {
                $ct_delivery_price = 0;
            }

            $shtml .= '<li class="d-flex align-items-center justify-content-between pb_8">';
            $shtml .=   '<p class="fw_500">'.number_format($row_dpt['sadt_if_price']).'원 '.($sadt_if_price?'~ '.number_format($sadt_if_price).'원 미만':'이상').'</p>';
            $shtml .=   '<p class="fw_300">'.number_format($ct_delivery_price).'원</p>';
            $shtml .= '</li>';

            $price[] = $ct_delivery_price;
            $sadt_if_price = $row_dpt['sadt_if_price'];
        }

        // 배대사 api
        $d_rtn = get_delivery_price($row, ($sadt_if_price ? $sadt_if_price*1 - 1 : 0));
        $ct_delivery_chk = $d_rtn['ct_delivery_chk'];
        $ct_delivery_chk_msg = $d_rtn['ct_delivery_chk_msg'];
        $ct_delivery_default_price = $d_rtn['st_send_cost_total'];
        $ct_delivery_price = $ct_delivery_default_price;

        if ((int)$row['st_delivery_support_price'] > 0) { // 매장별 본사 부담액 차감
            $ct_delivery_price -= (int)$row['st_delivery_support_price'];
        }
        $ct_delivery_price -= (int)$row['sadt_price'];
        if ($ct_delivery_price<0) {
            $ct_delivery_price = 0;
        }

        $shtml .= '<li class="d-flex align-items-center justify-content-between pb_8">';
        $shtml .=   '<p class="fw_500">0원 '.($sadt_if_price?'~ '.number_format($sadt_if_price).'원 미만':'이상').'</p>';
        $shtml .=   '<p class="fw_300">'.number_format($ct_delivery_price).'원</p>';
        $shtml .= '</li>';

        $price[] = $ct_delivery_price;
    } else {
        $ct_delivery_chk = true;
        if($row['scdt_price_type']=='1') { //무료
            $shtml .= '<li class="d-flex align-items-center justify-content-between pb_8">';
            $shtml .=   '<p class="fw_500">0원 이상</p>';
            $shtml .=   '<p class="fw_300">무료</p>';
            $shtml .= '</li>';

            $price[] = 0;
        } else if($row['scdt_price_type']=='2') { //조건부무료
            $shtml .= '<li class="d-flex align-items-center justify-content-between pb_8">';
            $shtml .=   '<p class="fw_500">'.number_format($row['scdt_if_price']).'원 이상</p>';
            $shtml .=   '<p class="fw_300">무료</p>';
            $shtml .= '</li>';
            $shtml .= '<li class="d-flex align-items-center justify-content-between pb_8">';
            $shtml .=   '<p class="fw_500">0원 ~ '.number_format($row['scdt_if_price']).'원 미만</p>';
            $shtml .=   '<p class="fw_300">'.number_format($row['scdt_price']).'원</p>';
            $shtml .= '</li>';

            $price[] = 0;
            $price[] = $row['scdt_price'];
        } else if($row['scdt_price_type']=='3') { //유료
            $shtml .= '<li class="d-flex align-items-center justify-content-between pb_8">';
            $shtml .=   '<p class="fw_500">0원 이상</p>';
            $shtml .=   '<p class="fw_300">'.number_format($row['scdt_price']).'원</p>';
            $shtml .= '</li>';

            $price[] = $row['scdt_price'];
        }
    }

    sort($price);

    $min = $price[0];
    $max = $price[count($price)-1];

    return array(
        'min' => $min,
        'max' => $min == $max ? 0 : $max,
        'shtml' => $shtml,
        'ct_delivery_chk' => $ct_delivery_chk,
        'ct_delivery_chk_msg' => $ct_delivery_chk_msg,
        '$d_rtn' => $d_rtn,
    );
}
function get_delivery_price($row, $ct_price=0) {
    global $DB, $_user_add1, $_user_add2, $_user_lat, $_user_lng;

    $ct_delivery_chk = false;
    $ct_delivery_chk_msg = "";
    $st_send_cost = '';//'3,500원~5,000원';
    $st_send_cost_total = 0;
    // 배대사 api
    if ($row['st_delivery_agency']=='barogo') {
        if ($row['orderAgencyStoreId'] && $_user_add1 && $_user_add2) {
            $request_post = array(
                "orderAgencyId" => BAROGO_ID,
                "orderAgencyStoreId" => $row['orderAgencyStoreId'],
                "dropRoadAddress" => $_user_add1,
                "dropAddressDetail" => $_user_add2,
                "dropLocation" => ['latitude' => $_user_lat, 'longitude' => $_user_lng],
                "pickupWishAt" => ((time()+1200)*1000),
            );
            if ($ct_price) {
                $request_post["totalPayPrice"] = $ct_price;
            }
            $d_rtn = delivery_barogo('/api/delivery-possible', $request_post);
            if($d_rtn['statusCode']=='200') {
                if($d_rtn['data']['isPossible']=='true') {
                    $ct_delivery_chk = true;
                    $st_send_cost = number_format($d_rtn['data']['deliveryInfo']['deliveryPrice']).'원 ~ '.number_format($d_rtn['data']['deliveryInfo']['totalDeliveryPrice']).'원';
                    $st_send_cost_total = $d_rtn['data']['deliveryInfo']['totalDeliveryPrice']*1;
                }
            }
        }
    } else if ($row['st_delivery_agency']=='vroong') {
        if ($row['branch_code'] && $_user_add1 && $_user_add2) {
            $request_post = array(
                "branch_code" => $row['branch_code'],
                //"dest_address" => $_user_add1,
                //"dest_address_detail" => $_user_add2,
                "dest_address_road" => $_user_add1,
                "dest_address_detail_road" => $_user_add2,
                "dest_lat" => $_user_lat,
                "dest_lng" => $_user_lng,
            );
            $d_rtn = delivery_vroong('/api/delivery/submit_fee', $request_post);
            if($d_rtn['result']=='SUCCESS') {
                $ct_delivery_chk = true;
                $st_send_cost = number_format($d_rtn['base_fee']).'원 ~ '.number_format($d_rtn['sum_total']).'원';
                $st_send_cost_total = $d_rtn['sum_total']*1;
            } else {
                if ($d_rtn['error_code']=='DESTINATION_OUT_OF_DELIVERABLE_REGION') { $ct_delivery_chk_msg = "배달불가지역"; }
            }
        }
    } else {

    }

    return array(
        //'st_delivery_agency' => $row['st_delivery_agency'],
        //'orderAgencyStoreId' => $row['orderAgencyStoreId'],
        //'$_user_add1' => $_user_add1,
        //'$_user_add2' => $_user_add2,
        //'ct_price' => $ct_price,
        'd_rtn' => $d_rtn,
        'request_post' => $request_post,
        'ct_delivery_chk' => $ct_delivery_chk,
        'ct_delivery_chk_msg' => $ct_delivery_chk ? "" : ($ct_delivery_chk_msg ? $ct_delivery_chk_msg : "배달불가"),
        'st_send_cost' => $st_send_cost,
        'st_send_cost_total' => $st_send_cost_total,
    );
}
function get_product_t_info($pt_idx, $_table="product_t") {
    global $DB;

    $DB->where('idx', $pt_idx);
    $row = $DB->getone("product_t");

    $row['seller_mt_idx'] = $row['mt_idx'];
    $row['pt_idx'] = $row['idx'];
    $row['seller_idx'] = $row['st_idx'];

    return $row;
}

function get_product_list($row, $style_gubun="", $display=array(), $_member=array()) {
    global $DB, $arr_gender, $ct_no_img_url, $ct_no_profile_url, $thumb_wd, $chk_admin;

    $shtml = '';

    $fileurl = get_list_thumbnail($row['pt_image1'], '', $thumb_wd, $thumb_wd);
    if (!$fileurl) {
        $fileurl = get_image_url($row['pt_image1']);
    }

    //$ca_name_breadcrumb_t = "";
    //if($row['ct_id']&&!$row['listtype']) $ca_name_breadcrumb_t = get_ca_name_breadcrumb_short($row['ct_id']);

    $ca_name_breadcrumb_t = "";
    /*$ct_id_arr = explode(',',$row['ct_id']);
    foreach ($ct_id_arr as $value) {
        $crow = $DB->fetch_query("select * from category_t where ct_id = '".$value."'");
        if ($ca_name_breadcrumb_t) { $ca_name_breadcrumb_t .= "<br/>"; }
        $ca_name_breadcrumb_t .= $arr_gender[$crow['ct_gender']].' &gt; '.get_ca_name_breadcrumb_short($value);
    }*/
    if ($row['pt_menu']) {
        $ct_id_arr = explode(',',$row['pt_menu']);
        foreach ($ct_id_arr as $value) {
            $DB->where('ct_id', $value);
            $crow = $DB->getone('category_menu_t');
            if ($ca_name_breadcrumb_t) { $ca_name_breadcrumb_t .= " / "; }
            $ca_name_breadcrumb_t .= $crow['ct_name'];
        }
    }

    if ($style_gubun === 'mng') {
        //$onclick = "onclick=\"f_popup('./product_form.php?act=update&pt_idx=".$row['pt_idx']."');\"";
        $onclick = "onclick=\"location.href='./product_form.php?act=update&pt_idx=".$row['pt_idx']."';\"";

        //$fileurl = $ct_no_img_url;
        //if ($row['pt_image1'] && file_exists(DATA_PATH.'/'.$row['pt_image1'])) { $fileurl = DATA_URL.'/'.$row['pt_image1'].'?ver='.date('ymdHis'); }

        $shtml .= '<div class="media product_list_media">';
        $shtml .= '<a href="javascript:;" onclick="layerPop(\'swiper_image\',\'pt\',\''.$row['pt_idx'].'\')"><img src="'.$fileurl.'" onerror="this.src=\''.$ct_no_img_url.'\'" class="align-self-center mr-3" alt="'.$row['pt_title'].'"></a>';
        $shtml .= '<div class="media-body">';
        if ($display!='ca' && $ca_name_breadcrumb_t) {
            $shtml .= '<small class="mb-1 d-block text-secondary" style="line-height: 1.2;">'.$ca_name_breadcrumb_t.'</small>';
        }
        $shtml .= '<h5 class="font-weight-bold" '.$onclick.'>'.cut_str($row['pt_title'], 0, 40, '..').'</h5>';
        if($row['listtype']) {
            //$shtml .= '<h5 class="text-info font-weight-bold">'.number_format($row['ct_price']).'원</h5>';
            //$shtml .= '<p><small class="mt-2">'.($row['ct_opt_value'] ? "선택옵션 : ".$row['ct_opt_value'].", " : "").'수량 : '.number_format($row['ct_opt_qty']).'</small></p>';
            if ($row['ct_opt_value']) {
                $ct_opt_name_ex = explode('|:|', $row['ct_opt_name']);
                $ct_opt_value_ex = explode('|:|', $row['ct_opt_value']);
                $ct_opt_price_ex = explode('|:|', $row['ct_opt_price']);
                for ($qq = 0; $qq < count($ct_opt_value_ex); $qq++) {
                    $shtml .= '<div class="if_sp">';
                    $shtml .=   '<span class="text-gray mr-1">'.$ct_opt_name_ex[$qq].'</span>'.$ct_opt_value_ex[$qq].'(+'.number_format($ct_opt_price_ex[$qq]).'원)';
                    if ($qq < count($ct_opt_value_ex) - 1) {
                        $shtml .= ", ";
                    }
                    $shtml .= '</div>';
                }
            }

            if($row['ct_opt_direct']!='' && $row['ct_opt_direct']!='|:|') {
                $ct_opt_direct_ex = explode('|:|', $row['ct_opt_direct']);
                if($ct_opt_direct_ex) {
                    $shtml .= '<div class="if_sp">';
                    $shtml .=   "<span class=\"text-gray mr-1\">직접입력</span>";
                    foreach ($ct_opt_direct_ex as $key => $val) {
                        if($val) {
                            $shtml .= $val.", ";
                        }
                    }
                    $shtml .= '</div>';
                }
            }
        } else {
            if($row['pt_price']) {
                if($row['pt_discount_per']) {
                    $shtml .= '<span class="text-info" style="text-decoration:line-through;">'.number_format($row['pt_selling_price']).'원</span> -> <span class="text-info">'.number_format($row['pt_price']).'원</span> (<span class="text-secondary">'.number_format($row['pt_discount_per']).'%</span>)';
                } else {
                    $shtml .= '<span class="text-info font-weight-bold">'.number_format($row['pt_price']).'원</span>';
                }
            }
        }
        $shtml .= '</div>';
        $shtml .= '</div>';
    } else if ($style_gubun === 'mng_td') {

        $shtml .= '<td class="text-center">';
        $shtml .=   '<div class="list_media_sm">';
        $shtml .=     '<a href="javascript:;" onclick="layerPop(\'swiper_image\',\'pt\',\''.$row['pt_idx'].'\')"><img src="'.$fileurl.'" onerror="this.src=\''.$ct_no_img_url.'\'" class="align-self-center" alt="'.$row['pt_title'].'"></a>';
        $shtml .=   '</div>';
        $shtml .= '</td>';
        $shtml .= '<td class="text-left'.($row['onclick'] ? ' text-primary' : '').'" '.$row['onclick'].'>'.cut_str($row['pt_title'], 0, 40, '..').'</td>';
        $shtml .= '<td class="text-left">'.$ca_name_breadcrumb_t.'</td>';
        $shtml .= '<td class="text-right">';
        if($row['listtype']) {
            $shtml .= '<p class="mb-0 text-info font-weight-bold">'.number_format($row['ct_price']).'원</p>';
            $shtml .= '<p class="mb-0"><small class="mt-2">'.($row['ct_opt_value'] ? "선택옵션 : ".$row['ct_opt_value'].", " : "").'수량 : '.number_format($row['ct_opt_qty']).'</small></p>';
        } else {
            if($row['pt_price']) {
                if($row['pt_discount_per']) {
                    $shtml .= '<span class="text-info" style="text-decoration:line-through;">'.number_format($row['pt_selling_price']).'원</span> -> <span class="text-info">'.number_format($row['pt_price']).'원</span> (<span class="text-secondary">'.number_format($row['pt_discount_per']).'%</span>)';
                } else {
                    $shtml .= '<span class="text-info font-weight-bold">'.number_format($row['pt_price']).'원</span>';
                }
            }
        }
        $shtml .= '</td>';
    } else if ($style_gubun === 'store') {//판매사화면

    } else if ($style_gubun === 'justtxt') {//사용자화면에서 이미지없이
        if($row['pt_time_deal'] == 'Y') {
            $shtml .= '<div class="detail_tit py-5 px_16 border-bottom">';
            $shtml .=   '<div class="name">'.$row['pt_title'].'</div>';

            /*if ($row['pt_discount_per']) {
                $shtml .= '<div class="before_price">'.number_format($row['pt_selling_price']).'</div>';
            }*/
            $shtml .=   '<div class="price">';
            if ($row['pt_discount_per']) {
                $shtml .= '<span class="dsc mr-2">'.number_format($row['pt_discount_per']).'%</span>';
            }
            $shtml .=     '<span class="">'.number_format($row['pt_price']).'원</span>';
            if ($row['pt_unit_type'] == 'W' && $row['pt_weight_price']) {
                $shtml .= '<span class="unit_price ml-2">(단위당 가격 '.number_format($row['pt_weight_price']).'원)</span>';
            }
            $shtml .=   '</div>';

            $shtml .= '</div>';
        } else {
            $shtml .= '<div class="item">';
            $shtml .=   '<div class="item_body">';
            $shtml .=     '<div class="cate">'.$ca_name_breadcrumb_t.'</div>';
            $shtml .=     '<div class="name fs_20">'.$row['pt_title'].'</div>';
            $shtml .=     '<div class="d-flex align-items-end justify-content-between">';
            $shtml .=       '<div class="flex-fill">';

            if ($row['pt_discount_per']) {
                $shtml .=     '<div class="before_price fs_15">'.number_format($row['pt_selling_price']).'</div>';
            }
            $shtml .=         '<div class="price fs_20">';
            if ($row['pt_discount_per']) {
                $shtml .=       '<span class="dsc mr-2">'.number_format($row['pt_discount_per']).'%</span>';
            }
            $shtml .=           '<span class="">'.number_format($row['pt_price']).'원</span>';
            if ($row['pt_unit_type'] == 'W' && $row['pt_weight_price']) {
                $shtml .=       '<span class="unit_price ml-2 fs_12">(단위당 가격 '.number_format($row['pt_weight_price']).'원)</span>';
            }
            $shtml .=         '</div>';

            $shtml .=       '</div>';
            $shtml .=     '</div>';
            $shtml .=   '</div>';
            $shtml .= '</div>';
        }
    } else if ($style_gubun === 'index') {//사용자화면 메인

        $seller_name = $row['st_name'];
        $seller_link = './shop_detail.php?idx='.$row['st_idx'];

        $shtml .= '<div class="item">';
        $shtml .=   '<div class="thum mb_20">';
        $shtml .=     '<div class="rect rounded"><img src="'.$fileurl.'" alt="" loading="lazy" /></div>';
        $shtml .=     '<button type="button" class="badge btn-plus" onclick="f_addCart(\''.$row['pt_idx'].'\')"><img src="'.DESIGN_HTTP.'/img/ico_add_cart.png" style="width:2.6rem;" /></button>';
        $shtml .=   '</div>';
        $shtml .=   '<div class="item_body">';
        $shtml .=     '<button type="button" class="btn btn-outline-light btn-sm rounded-pill mb-3" onclick="location.href=\''.$seller_link.'\'">';
        $shtml .=       '<div class="line1_text">'.$seller_name.'</div>';
        $shtml .=       '<img class="ml-2" src="'.DESIGN_HTTP.'/img/shop_arrow.png" style="width:0.5rem;" />';
        $shtml .=     '</button>';
        $shtml .=     '<div class="name line2_text">'.$row['pt_title'].'</div>';

        if ($row['pt_discount_per']) {
            $shtml .= '<div class="before_price">'.number_format($row['pt_selling_price']).'</div>';
        }
        $shtml .=     '<div class="price">';
        $shtml .=       '<span>'.number_format($row['pt_price']).'원</span>';
        if ($row['pt_discount_per']) {
            $shtml .=   '<span class="dsc mx-2">'.number_format($row['pt_discount_per']).'%</span>';
        }
        /*if ($row['pt_unit_type'] == 'W' && $row['pt_weight_price']) {
            $shtml .=   '<span class="unit_price">(단위당 '.number_format($row['pt_weight_price']).'원)</span>';
        }*/
        $shtml .=     '</div>';

        $shtml .=   '</div>';
        $shtml .= '</div>';
        $shtml .= '<a href="./item_detail.php?idx='.$row['pt_idx'].'" class="item_link"></a>';
    } else {//사용자화면

        $shtml .= '<li class="col">';
        $shtml .=   '<div class="item media">';
        $shtml .=     '<div class="thum mr_20">';
        $shtml .=       '<div class="rect rounded" style="width:8.0rem;"><img src="'.$fileurl.'" alt="" loading="lazy" /></div>';
        $shtml .=     '</div>';
        $shtml .=     '<div class="item_body">';
        $shtml .=       '<div class="cate">'.$ca_name_breadcrumb_t.'</div>';
        $shtml .=       '<div class="name line2_text">'.$row['pt_title'].'</div>';
        $shtml .=       '<div class="d-flex align-items-end justify-content-between">';
        $shtml .=         '<div class="flex-fill">';

        if ($row['pt_discount_per']) {
            $shtml .=       '<div class="before_price">'.number_format($row['pt_selling_price']).'</div>';
        }
        $shtml .=           '<div class="price">';
        if ($row['pt_discount_per']) {
            $shtml .=         '<span class="dsc mr-2">'.number_format($row['pt_discount_per']).'%</span>';
        }
        $shtml .=             '<span class="">'.number_format($row['pt_price']).'원</span>';
        if ($row['pt_unit_type'] == 'W' && $row['pt_weight_price']) {
            $shtml .=         '<span class="unit_price ml-2">(단위당 가격 '.number_format($row['pt_weight_price']).'원)</span>';
        }
        $shtml .=           '</div>';

        $shtml .=         '</div>';
        $shtml .=       '</div>';
        $shtml .=     '</div>';
        $shtml .=   '</div>';
        $shtml .=   '<a href="./item_detail.php?idx='.$row['pt_idx'].'" class="item_link"></a>';
        $shtml .= '</li>';
    }

    return $shtml;
}

function get_order_list($row, $claim_t = array()) {
    global $DB;

    $_cancel = false;
    $_review = false;
    $_review_chk = false;
    $_delivery = false;
    $_return = false;
    $_return_cancel = false;
    $_confirm = false;
    $_timer = false;

    if ($row['ot_type'] == 'A') {
        if ($row['ot_status'] < '3') {
            $_cancel = true;
        }
        if ($row['ot_status'] >= '3' && $row['ot_status'] <= '8') {
            $_timer = true;
        }
    }
    if ($row['ot_type'] == 'B') {
        if ($row['ot_status'] < '3') {
            $_cancel = true;
        }
        if ($row['ot_status'] == '3') {
            $_timer = true;
        }
    }
    if ($row['ot_type'] == 'C') {
        if ($row['ot_status'] < '3') {
            $_cancel = true;
        }
        if ($row['ot_status'] == '8') {
            $_delivery = true;
        }
        if ($claim_t['idx'] && $claim_t[$claim_t['claim_r_column'].'_status'] == 'D' && ($row['ot_status'] == '70' || $row['ot_status'] == '80' || $row['ot_status'] == '90')) {
            $_return_cancel = true;
        } else {
            if ($row['ot_status'] == '8' || $row['ot_status'] == '7' || $row['ot_status'] == '81') {
                $_return = true;
            }
        }
        if ($row['ot_status'] == '8' || $row['ot_status'] == '9') {
            $_confirm = true;
        }
        if ($row['ot_status'] == '10' || $row['ot_status'] == '81') {
            $_review_chk = true;
        }
    }

    if ($row['ot_type'] == 'A' || $row['ot_type'] == 'B') {
        if ($row['ot_status'] == '9') {
            $_review_chk = true;
        }
        $timer = array();
        $ot_adate_t = $ot_dsdate_t = "";
        if ($row['ot_type'] == 'B') {
            if ($row['ot_status'] == '3') {
                if ($row['ot_store_time']) {
                    //$ot_adate = date('Y-m-d H:i:s', strtotime($row['ot_adate'].' + '.$row['ot_store_time'].' minute'));
                    /*$dateTime = new DateTime($row['ot_adate']);

                    // 초를 DateInterval 포맷으로 변환
                    $interval = new DateInterval('PT'.$row['ot_store_time'].'S');
                    // 초 추가
                    $dateTime->add($interval);
                    $hour = $dateTime->format('H');
                    $min  = $dateTime->format('i');
                    if ($hour > 12) {
                        $hour = $hour - 12;
                        $ot_adate_t = "오후 " . $hour. ":". $min;
                    } else {
                        $ot_adate_t = "오전 " . $hour. ":". $min;
                    }*/
                    $ot_adate_t = date("A h : i", $row['ot_store_time']);
                    $ot_adate_t = str_replace('AM','오전',$ot_adate_t);
                    $ot_adate_t = str_replace('PM','오후',$ot_adate_t);
                }
            }
        } else if ($row['ot_type'] == 'A') {
            if ($row['ot_status'] == '3' || $row['ot_status'] == '4' || $row['ot_status'] == '6' || $row['ot_status'] == '8') {
                if ($row['ot_delivery_time']) {
                    //$ot_dsdate = date('Y-m-d H:i:s', strtotime($row['ot_dsdate'].' + '.$row['ot_delivery_time'].' minute'));
                    /*$dateTime = new DateTime($row['ot_adate']);

                    // 초를 DateInterval 포맷으로 변환
                    $interval = new DateInterval('PT'.$row['ot_delivery_time'].'S');
                    // 초 추가
                    $dateTime->add($interval);

                    $hour = $dateTime->format('H');
                    $min  = $dateTime->format('i');
                    if ($hour > 12) {
                        $hour = $hour - 12;
                        $ot_dsdate_t = "오후 " . $hour. ":". $min;
                    } else {
                        $ot_dsdate_t = "오전 " . $hour. ":". $min;
                    }*/
                    $ot_dsdate_t = date("A h : i", $row['ot_delivery_time']);
                    $ot_dsdate_t = str_replace('AM','오전',$ot_dsdate_t);
                    $ot_dsdate_t = str_replace('PM','오후',$ot_dsdate_t);

                    $DB->where('ot_code', $row['ot_code']);
                    //$DB->where("odt_response IS NOT NULL");
                    $DB->orderBy('odt_idx', "desc");
                    $row_odt = $DB->getone('order_delivery_t');
                    $pickupExpectedAt = $row_odt['odt_pick_time'];
                    if ($pickupExpectedAt && ($pickupExpectedAt / 1000) > $row['ot_delivery_time']) {
                        //$ot_dsdate_t = date("A h : i", ($pickupExpectedAt / 1000));
                        $dateTime = new DateTime();
                        $dateTime->setTimestamp($pickupExpectedAt / 1000);
                        $dateTime->modify('+'.$row_odt['ot_count_time2'].' minutes');
                        $ot_dsdate_t = $dateTime->format('A h : i');
                        $ot_dsdate_t = str_replace('AM','오전',$ot_dsdate_t);
                        $ot_dsdate_t = str_replace('PM','오후',$ot_dsdate_t);
                    }
                }
            }
        }
    }

    if ($_review_chk) {
        $DB->where('a1.ot_code', $row['ot_code']);
        $DB->where("a1.rt_status", "Y");
        $DB->where("a1.parent_id", '0');
        $row_rt = $DB->getone("review_t a1", "COUNT(*) AS cnt");
        if (!$row_rt['cnt'] && cal_remain_days(date('Y-m-d', strtotime($row['ot_dedate'])), date('Y-m-d')) < 28) {
            $_review = true;
        }
    }

    $shtml = '';
    $shtml .= '<div class="form-row mt-3">';
    if ($_cancel) {
        $shtml .= '<div class="col"><button type="button" class="btn btn-outline-light btn-block text-body" onclick="jconfirm1(\'정말로 주문을 취소하시겠습니까?\', \'주문취소\', jc_o_cancel, \''.$row['ot_code'].'\');">주문취소</button></div>';//layerPop(\'cancel_modal\', \''.$row['ot_code'].'\')
    }
    if ($_delivery) {
        $shtml .= '<div class="col"><button type="button" class="btn btn-outline-light btn-block text-body" onclick="layerPop(\'delivery_search\', \''.$row['ot_code'].'\')">배송조회</button></div>';
    }
    if ($_confirm) {
        $shtml .= '<div class="col"><button type="button" class="btn btn-outline-light btn-block text-body" onclick="jconfirm1(\'구매확정 후에 리뷰를 작성하실 수 있습니다.<br/>구매확정 하시겠습니까?\', \'구매확정\', jc_o_confirm, \''.$row['ot_code'].'\');">구매확정</button></div>';
    }
    if ($_review) {
        $shtml .= '<div class="col"><button type="button" class="btn btn-outline-light btn-block text-body" onclick="location.href=\'./review_form.php?ot_code='.$row['ot_code'].'&url='.urlencode(CDN_MAIN_HTTP.'/order_history.php').'\'">리뷰쓰기</button></div>';
    }
    /*if ($_return_cancel) {
        if ($row['ot_status'] == '70') {
            $claim_txt = '취소';
        } else {
            $claim_txt = '교환/반품';
        }
        $shtml .= '<div class="col"><button type="button" class="btn btn-outline-light btn-block text-body" onclick="claim_t_cancel(\''.$claim_t['claim_table'].'\', \''.$claim_t['idx'].'\', \''.$claim_t['claim_title'].'\')">'.$claim_txt.' 요청 철회</button></div>';
    } else {
        if ($_return) {
            $claim_txt = '교환/반품';
            $shtml .= '<div class="col"><button type="button" class="btn btn-outline-light btn-block text-body" onclick="location.href=\'./order_return_form.php?ot_code='.$row['ot_code'].'\'">'.$claim_txt.' 요청</button></div>';
        }
    }*/
    //$shtml .= '<div class="col"><button type="button" class="btn btn-outline-light btn-block text-body" onclick="location.href=\'./qna_form.php?qt_type=2&ot_code='.$row['ot_code'].'&pt_idx='.$row['pt_idx'].'\'">문의하기</button></div>';
    $shtml .= '</div>';

    if ($_timer) {
        if ($row['ot_type'] == 'B' && $ot_adate_t) {
            $shtml .= '<div class="text-primary bg-primary-light fw_500 py_9 px-2 text-center rounded-pill mt-2">'.$ot_adate_t.' 완료예정</div>';
        } else if ($ot_dsdate_t) {
            $shtml .= '<div class="text-primary bg-primary-light fw_500 py_9 px-2 text-center rounded-pill mt-2">'.($ot_dsdate_t).' 도착예정</div>';
        }
    }

    return $shtml;
}

function get_setup_t_info() {
    global $DB;

    $DB->where('idx', '1');
    $row = $DB->getone('setup_t');

    return $row;
}
function get_category_info($ct_id) {
    global $DB;

    $DB->where('ct_id', $ct_id);
    $row = $DB->getone('category_t');

    return $row;
}
function get_bootom_ct_id($ct_id) {
    global $DB;

    $DB->where('ct_id', $ct_id);
    $row = $DB->getone('category_bottom_all');

    return $row['ct_id_txt'];
}

function get_bottom_all($ct_id) {
    global $DB;

    unset($list);
    $DB->where('ct_pid', $ct_id);
    $list = $DB->get("category_t");

    $arr_ct_id_txt = array();
    $arr_ct_id_txt[] = $ct_id;
    if($list) {
        foreach($list as $row) {
            if($row['ct_id']) {
                $arr_ct_id_txt[] = $row['ct_id'];

                unset($list2);
                $DB->where('ct_pid', $row['ct_id']);
                $list2 = $DB->get("category_t");

                if($list2) {
                    foreach($list2 as $row2) {
                        if($row2['ct_id']) {
                            $arr_ct_id_txt[] = $row2['ct_id'];

                            unset($list3);
                            $DB->where('ct_pid', $row2['ct_id']);
                            $list3 = $DB->get("category_t");

                            if($list3) {
                                foreach($list3 as $row3) {
                                    if($row3['ct_id']) {
                                        $arr_ct_id_txt[] = $row3['ct_id'];

                                        unset($list4);
                                        $DB->where('ct_pid', $row3['ct_id']);
                                        $list4 = $DB->get("category_t");

                                        if($list4) {
                                            foreach($list4 as $row4) {
                                                if($row4['ct_id']) {
                                                    $arr_ct_id_txt[] = $row4['ct_id'];

                                                    unset($list5);
                                                    $DB->where('ct_pid', $row4['ct_id']);
                                                    $list5 = $DB->get("category_t");

                                                    if($list5) {
                                                        foreach($list5 as $row5) {
                                                            if($row5['ct_id']) {
                                                                $arr_ct_id_txt[] = $row5['ct_id'];
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    return $arr_ct_id_txt;
}
//------------------------------------------------------------------------------------------------------------------
function check_holiday($row, $datetime=''){
    global $DB;

    $nowDate = $datetime ? $datetime : TIME_YMDHIS;
    $nowDate_w = date('w', strtotime(substr($nowDate,0,10)));
    $nowDate_hi = date('H:i', strtotime(substr($nowDate,11,5)));
    $tomorrow = date('Y-m-d', strtotime($nowDate.' + 1 days'));

    $st_idx = $row['idx'];
    if ($row['st_show']!=='Y') {
        $msg = $row['st_show'];
        return array('value' => $msg, 'msg' => "영업중인 상점이 아닙니다.");
    }
    if ($row['st_live']!=='Y') {
        $msg = $row['st_live'];
        return array('value' => $msg, 'msg' => "영업중인 상점이 아닙니다.");
    }

    // 영업시간
    if ($row['st_type']!='C') {
        if (!($row['st_live']=='Y' && $row['st_live_wdate'] >= $nowDate)) {
            // 오늘 휴무인 경우
            $DB->where("st_idx", $st_idx);
            $DB->where("sh_date = LEFT('{$nowDate}',10)");
            $holiday = $DB->getone('store_holiday_t', 'COUNT(*) AS cnt');
            if ($holiday['cnt']) {
                $msg = 'D';
                return array('value' => $msg, 'msg' => "휴무일입니다.");
            }

            if ($row['st_public_holiday'] == 'Y') {
                $DB->where('hdt_date', date('Ymd', strtotime($nowDate)));
                $row_hdt = $DB->getone('holiday_t', "COUNT(*) AS cnt");
                if ($row_hdt['cnt']) {
                    $msg = 'D';
                    return array('value' => $msg, 'msg' => "휴무일입니다.");
                }
            }
        }

        $DB->where('st_idx', $st_idx);
        $hours_chk = $DB->getone('store_hour_t', 'COUNT(*) AS cnt');
        if ($hours_chk['cnt']) {
            $DB->where('st_idx', $st_idx)->where("FIND_IN_SET('{$nowDate_w}', st_yoil) > 0");
            $hours = $DB->getone('store_hour_t', "COUNT(*) AS cnt, REPLACE(st_hour,' ','')");
            if (!$hours['cnt']) {
                $msg = 'D';
                return array('value' => $msg, 'msg' => "준비중입니다.");
            }

            if ($hours['st_hour'] != '00:00~00:00' || $hours['st_hour'] != '00:00 ~ 00:00') {
                unset($hours);
                $DB->where('st_idx', $st_idx)->where("FIND_IN_SET('{$nowDate_w}', st_yoil) > 0");
                $DB->where("SUBSTRING_INDEX(SUBSTRING_INDEX(REPLACE(st_hour,' ',''),'~',1),'~',-1) <= '".$nowDate_hi."'");
                $DB->where("SUBSTRING_INDEX(SUBSTRING_INDEX(REPLACE(st_hour,' ',''),'~',2),'~',-1) > '".$nowDate_hi."'");
                $hours = $DB->getone('store_hour_t', "COUNT(*) AS cnt
                , SUBSTRING_INDEX(SUBSTRING_INDEX(REPLACE(st_hour,' ',''),'~',1),'~',-1) AS st_shour
                , SUBSTRING_INDEX(SUBSTRING_INDEX(REPLACE(st_hour,' ',''),'~',2),'~',-1) AS st_ehour");
                /*$hours = $DB->fetch_assoc("SELECT COUNT(*) AS cnt
                , SUBSTRING_INDEX(SUBSTRING_INDEX(st_hour,'~',1),'~',-1) AS st_shour
                , SUBSTRING_INDEX(SUBSTRING_INDEX(st_hour,'~',2),'~',-1) AS st_ehour
                FROM store_hour_t WHERE st_idx='{$st_idx}' AND FIND_IN_SET('{$nowDate_w}', st_yoil) > 0
                    AND SUBSTRING_INDEX(SUBSTRING_INDEX(st_hour,'~',1),'~',-1) <= '".$nowDate_hi."'
                    AND SUBSTRING_INDEX(SUBSTRING_INDEX(st_hour,'~',2),'~',-1) > '".$nowDate_hi."' ");*/
                if (!$hours['cnt']) {
                    $msg = 'D';
                    return array('value' => $msg, 'msg' => "준비중입니다.");
                }
            }
        }

        /*// 정기휴일
        $st_holiday_str = trim($row['st_holiday']);
        $st_holiday_arr = explode('|',$st_holiday_str);
        $st_holiday_arr = array_values(array_filter(array_map('trim', $st_holiday_arr)));
        for ($k=0;$k<count($st_holiday_arr);$k++) {
            $st_holiday = explode('::', $st_holiday_arr[$k]);
            $do_st_yoil_arr = explode(",",$st_holiday[0]);
            $do_st_week_arr = explode(",",$st_holiday[1]);
            if (in_array($nowDate_w, $do_st_yoil_arr)) {

                if($do_st_yoil_arr[0] == '0') $do_st_yoil_arr[0] = '7';
                $nowJucha = getWeekInfo(date("Y-m-d"), $do_st_yoil_arr[0]); // 첫번째 정기휴일의 요일을 기준으로

                if (in_array($nowJucha, $do_st_week_arr) && in_array($nowDate_w, $do_st_yoil_arr)) {
                    $msg = 'D';
                    return array('value' => $msg, 'msg' => "오늘은 상점 정기휴일입니다.");
                }
            }
        }*/
    } else {
        return array('value' => 'Y', 'msg' => "");
    }
}
//------------------------------------------------------------------------------------------------------------------
function check_hour($row, $datetime=''){
    global $DB;

    $nowDate = $datetime ? $datetime : TIME_YMDHIS;
    $nowDate_w = date('w', strtotime(substr($nowDate,0,10)));
    $nowDate_hi = date('H:i', strtotime(substr($nowDate,11,5)));
    $tomorrow = date('Y-m-d', strtotime($nowDate.' + 1 days'));

    $st_idx = $row['idx'];
    if ($row['st_live']=='Y') {
        if ($row['st_type']!='C') {
            if (!($row['st_live']=='Y' && $row['st_live_wdate'] >= $nowDate)) {
                // 오늘 휴무인 경우
                $DB->where("st_idx", $st_idx);
                $DB->where("sh_date = LEFT('{$nowDate}',10)");
                $holiday = $DB->getone('store_holiday_t', 'COUNT(*) AS cnt');
                if ($holiday['cnt']) {
                    return "휴무일";
                }

                if ($row['st_public_holiday'] == 'Y') {
                    $DB->where('hdt_date', date('Ymd', strtotime($nowDate)));
                    $row_hdt = $DB->getone('holiday_t', "COUNT(*) AS cnt");
                    if ($row_hdt['cnt']) {
                        return "휴무일";
                    }
                }
            }

            $DB->where('st_idx', $st_idx)->where("FIND_IN_SET('{$nowDate_w}', st_yoil) > 0");
            $hours = $DB->getone('store_hour_t');
            if ($hours['st_hour']) {
                return str_replace('~', ' ~ ', $hours['st_hour']);
            }
        }
    }
}
//------------------------------------------------------------------------------------------------------------------
function getWeekInfo($_date, $BASIC_DOW) {

    //$BASIC_DOW = 1; // 1(mon) ~ 7(sun)
    list($yy, $mm, $dd) = explode('-', $_date);

    $dow = date('N', mktime(0, 0, 0, $mm, 1, $yy));

    if ($dow <= $BASIC_DOW)
    {
        $diff = $BASIC_DOW - $dow;
        $srt_day = $diff+1;
    } else {
        $diff = 7-$dow;
        $srt_day = $diff + $BASIC_DOW + 1;
    }

    $dd = preg_replace('/(0)(\d)/','$2', $dd);
    $srt_day = preg_replace('/(0)(\d)/','$2', $srt_day);

    if ($dd < $srt_day)
    {
        $new_date = date('Y-m-d', mktime(0, 0, 0, $mm, 0, $yy));
        return getWeekInfo($new_date, $BASIC_DOW);

    } else {
        $wom = ceil(($dd-($srt_day-1))/7);

        // 이곳을 수정하면 원하시는 결과로 리턴하셔도 됩니다.
        //$new_date = (int)$mm. '-' .$wom;
        $new_date = $wom;
        return $new_date;
    }
}
//------------------------------------------------------------------------------------------------------------------
function f_get_weekday(){ //주말&공휴일이 아닌 평일 구하기
    global $DB;

    // 현재 날짜 + 1로 시작
    $date = new DateTime();
    $date->modify('+1 day');

    while (true) {
        $dayOfWeek = $date->format('N'); // 1 (for Monday) through 7 (for Sunday)
        $formattedDate = $date->format('Ymd');

        // 주말 확인
        if ($dayOfWeek == 6 || $dayOfWeek == 7) {
            $date->modify('+1 day');
            continue;
        }

        // 특정 휴일 확인
        $DB->where("hdt_date", $formattedDate);
        $hdt = $DB->getone('holiday_t', "COUNT(*) AS cnt");
        if ($hdt['cnt']) {
            $date->modify('+1 day');
            continue;
        }

        break; // 평일이면 반복 종료
    }

    return $date->format('Y-m-d');//평일
}
//------------------------------------------------------------------------------------------------------------------
function alim_goto($pst_type='', $pst_index=''){
    global $DB;

    $link = "";
    if ($pst_type) {
        switch ($pst_type) {
            //사용자
            case 'qaAnswer':
                $link = CDN_MAIN_HTTP.'/qna.php';
                break;
            case 'reviewRequest':
                $link = CDN_MAIN_HTTP.'/order_history.php';
                break;
            case 'orderFin_A':
            case 'orderFin_B':
            case 'orderFin_C':
            case 'orderCancel':
            case 'orderPickup':
            case 'deliveryNow_A':
            case 'deliveryNow_C':
            case 'deliverFin_A':
            case 'deliverFin_C':
                $link = CDN_MAIN_HTTP.'/order_history_detail.php?ot_code='.$pst_index;
                break;
            case 'sentBy_store':
                $link = CDN_MAIN_HTTP.'/shop_detail.php?idx='.$pst_index;
                break;
            case 'sentBy_mng':
                $link = CDN_MAIN_HTTP.'/alim.php';
                break;
            //판매자
            case 'orderFin_seller':
                $link = CDN_SELLER_HTTP.'/order_list.php';
                break;
            case 'orderCancel_seller':
            case 'deliveryCallOk_seller':
            case 'deliveryFin_A_seller':
            case 'orderUpdate_seller':
                $link = CDN_SELLER_HTTP.'/order_detail.php?ot_code='.$pst_index;
                break;
        }
    }

    return $link;
}
//------------------------------------------------------------------------------------------------------------------

/*function send_notification_topic ($topic, $message, $app_key) {
    $url = "https://fcm.googleapis.com/fcm/send";

    $fields = array('to' => '/topics/'.$topic , 'notification' => $message, 'data' => $message);

    $headers = array(
        'Authorization:key ='.$app_key,
        'Content-Type: application/json'
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

    $response = curl_exec($ch);

    curl_close($ch);
    return $response;
}

function send_notification ($tokens, $message, $app_key) {
    $url = 'https://fcm.googleapis.com/fcm/send';

    $fields = array(
        'registration_ids' => $tokens,
        'priority' => 'high',
        'content_available' => true,
        'click_action'=>"",
        'notification' => $message,
        'data' => $message
    );

    $headers = array(
        'Authorization:key ='.$app_key,
        'Content-Type: application/json'
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    $result = curl_exec($ch);

    if ($result === FALSE) {
        die('Curl failed: ' . curl_error($ch));
    }
    curl_close($ch);
    return $result;
}*/
function send_notification ($tokens, $message, $message1, $app_key, $projectId, $pst_idx='') {
    global $DB, $send_fcm;

    unset($rtn);
    $rtn = array();
    /*
    foreach($tokens as $key => $val) {
        $body = [
            'message' => [
                'token' => $val,
                'notification' => [
                    'title' => $message['title'],
                    'body' => $message['body'],
                ],
                'data' => [
                    'title' => $message['title'],
                    'body' => $message['body'],
                    'event_url' => $message['url'],
                    'image' => $message['image'],
                ],
                'android' => [
                    'notification' => [
                        'image' => $message['image'],
                    ]
                ],
                'apns' => [
                    'payload' => [
                        'aps' => [
                            'mutable-content' => 1
                        ],
                    ],
                    'fcm_options' => [
                        'image' => $message['image'],
                    ],
                ],
            ],
        ];

        $result[] = $send_fcm->send($body, $app_key, $projectId);
    }
    */

    require_once ($_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php');

    $url = 'https://fcm.googleapis.com/v1/projects/'.$projectId.'/messages:send';
    putenv('GOOGLE_APPLICATION_CREDENTIALS='.$_SERVER['DOCUMENT_ROOT'].'/'.$app_key);

    $scope = 'https://www.googleapis.com/auth/firebase.messaging';

    $client = new Google_Client();
    $client->useApplicationDefaultCredentials();
    $client->setScopes($scope);

    $auth_key = $client->fetchAccessTokenWithAssertion();

    $headers = array(
        'Authorization: Bearer ' . $auth_key['access_token'],
        'Content-Type: application/json'
    );
    /*$ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    $result = curl_exec($ch);

    if ($result === FALSE) {
        die('Curl failed: ' . curl_error($ch));
    }
    curl_close($ch);

    $result = json_decode($result, true);
    if ($result['error']) {
        $message_status = json_encode($result['error']);
    } else {
        $message_status = 'Y';
    }*/

    $multiCurl = array();
    $mh = curl_multi_init();
    curl_multi_setopt($mh, CURLMOPT_PIPELINING,CURLPIPE_MULTIPLEX);

    foreach ($tokens as $i => $token) {
        $fields = array(
            'message' => array(
                'token' => $token,
                'notification' => $message,
                'data' => $message1,
            )
        );
        $multiCurl[$i] = curl_init();
        curl_setopt($multiCurl[$i], CURLOPT_URL,$url);
        curl_setopt($multiCurl[$i], CURLOPT_HTTPHEADER, $headers);
        curl_setopt($multiCurl[$i], CURLOPT_RETURNTRANSFER,true);
        curl_setopt($multiCurl[$i], CURLOPT_POST, true);
        curl_setopt($multiCurl[$i], CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($multiCurl[$i], CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($multiCurl[$i], CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_2_0);
        curl_setopt($multiCurl[$i], CURLOPT_POSTFIELDS, json_encode($fields, JSON_UNESCAPED_UNICODE));
        curl_multi_add_handle($mh, $multiCurl[$i]);
    }

    $index = null;
    do {
        curl_multi_exec($mh, $index);
        curl_multi_select($mh);
    } while($index > 0);

    $message_status = '';
    $pst_fail_count = 0;
    foreach($multiCurl as $k => $ch) {
        $result[$k] = curl_multi_getcontent($ch);
        $response = json_decode($result[$k], true);
        if ($response['error']) {
            $message_status = json_encode($response['error']);
            $pst_fail_count++;
        }
        $rtn[] = $response;
        if ($pst_idx) {
            unset($arr_query);
            $arr_query = array(
                'message_status' => $message_status ? $message_status : "Y",
                'pst_fail_count' => $pst_fail_count,
            );
            $DB->where('idx', $pst_idx);
            $DB->update('pushnotification_t', $arr_query);
        }
        curl_multi_remove_handle($mh, $ch);
    }
    curl_multi_close($mh);

    return $rtn;
}
//----------------------------------------------------------------------------------------------------------------------
//알림 등록 (구분, 알림타입, 대상아이디, 관련테이블, 해당인덱스, bo_table, point) // 읽음여부(Y,N)
function proc_noti($push_type, $send_to, $ref_table='', $ref_idx='', $ref_param=array(), $push_board='', $point='', $push_link="", $send_msg="") {
    global $DB;
    global $arr_grade, $ct_img_url, $arr_ct_status;

    $send_to_arr = explode(',',$send_to); //개별유저 아이디
    $send_to_arr = array_values(array_filter(array_map('trim',$send_to_arr)));
    $send_check = false;
    $show_noti = true;
    if (count($send_to_arr) > 0) { $send_check = true; }
    if ($push_board==='notice' || $push_type==='admin' || ($push_type == 'sentBy_mng' && $ref_param['prt_class'] == '1')) { $send_check = true; }
    if ($send_check===true) {

        $pst_type = $push_type;
        $pst_category = '1';
        $pst_level = '1';
        $shot_title = '';
        $point_str = $point ? str_replace('-','',$point) : '';

        switch($push_type) {
            case "push":
                $DB->where('p_type', $ref_table);
                $wr = $DB->getone('push_t');

                $pst_level = $wr['p_level'];
                $pst_type = $wr['p_type'];
                //$push_link = $wr['p_link'];
                $push_link = alim_goto($pst_type, $ref_idx);

                $title = $wr['p_subject'] ? $wr['p_subject'] : $wr['ct_name'];
                $content = $wr['p_content'];
                if ($wr['p_save'] == 'N') {
                    $show_noti = false;
                }

                $wr_data = array('title'=> $title, 'content'=> $content);
                // $src 를 $dst 로 변환
                unset($src);
                unset($dst);

                if ($ref_table==='orderCancel' || $ref_table==='orderCancel_seller' ||
                    $ref_table==='deliveryCallOk_seller' || $ref_table==='deliveryFin_A_seller') {
                    $DB->where('ot_code', $ref_idx);
                    $row_ot = $DB->getone('order_t');

                    $src[] = "/{{ot_code}}/";
                    $dst[] = $row_ot['ot_code'];
                }
                if ($ref_table==='orderCancel' || $ref_table==='orderCancel_seller') {
                    $src[] = "/{{cancel_reason}}/";
                    $dst[] = $row_ot['ot_cancel_memo'] ? $row_ot['ot_cancel_memo'] : $row_ot['ot_cancel_category'];
                }
                if ($ref_table==='deliveryCallOk_seller') {
                    $ot_dsdate_t = date('H:i', $row_ot['ot_delivery_time']);

                    $DB->where('ot_code', $row_ot['ot_code']);
                    $DB->orderBy('odt_idx', "desc");
                    $row_odt = $DB->getone('order_delivery_t');
                    if ($row_odt['odt_idx']) {
                        $pickupExpectedAt = $row_odt['odt_pick_time'];
                        if ($pickupExpectedAt) {
                            $ot_dsdate_t = date('H:i', ($pickupExpectedAt / 1000));
                        }
                    }

                    $src[] = "/{{time}}/";
                    $dst[] = $ot_dsdate_t;
                }
                if ($ref_table==='noticeMini1') {
                    $DB->where('idx', $ref_idx);
                    $row_nt = $DB->getone('notice_mini_t');
                    if ($row_nt['nt_title'] && $row_nt['nt_content']) {
                        $title = $row_nt['nt_title'];
                        $content = $row_nt['nt_content'];
                    }
                }
                /*if ($ref_table==='join') {
                    $DB->where('idx', $ref_idx);
                    $mt = $DB->getone('member_t');

                    $src[] = "/{{member_name}}/";
                    $dst[] = $mt['mt_nick']?$mt['mt_nick']:"회원";
                }
                if ($ref_table==='orderFin' || $ref_table==='orderFin_vbank') {
                    $DB->where('idx', $ref_param['mt_idx']);
                    $mt = $DB->getone('member_t');

                    $src[] = "/{{member_name}}/";
                    $dst[] = $mt['mt_nick']?$mt['mt_nick']:"회원";
                }
                if ($ref_table==='deliverFin') {
                    $DB->where('ot_code', $ref_idx);
                    $row_ct = $DB->getone('cart_t');
                    $src[] = "/{{point}}/";
                    $dst[] = calc_point('photo_review');
                }
                if ($ref_table==='gradeUp' || $ref_table==='gradeDown') {
                    $DB->where('idx', $ref_idx);
                    $mt = $DB->getone("member_t");
                    $src[] = "/{{grade}}/";
                    $dst[] = $arr_grade[$mt['mt_grade']];
                }*/

                if ($src || $dst) {
                    $content = preg_replace($src, $dst, $content);
                }
                $content = str_replace('&nbsp;',' ',$content);
                $wr_data['replace'] = $content;

                break;

            case "sentBy_mng":
            case "sentBy_store":
                //즉시/예약푸시
                $pst_category = '2';

                $DB->where('idx', $ref_idx);
                $wr = $DB->getone('push_reserve_t');

                if ($push_type == "sentBy_store" || ($push_type == "sentBy_mng" && $ref_param['prt_class'] == '3')) {
                    if ($ref_param['st_idx']) {
                        $push_link = alim_goto($pst_type, $ref_param['st_idx']);
                    }
                }

                $title = $wr['prt_title'];
                $content = $wr['prt_content'];
                break;

            case "notice": // 공지사항
                $target_name = "";
                $title 		= "공지알림";
                $content 	= get_text($target_name);
                break;

            case "admin":
                $DB->where('idx', $ref_idx);
                $wr = $DB->getone("pushnotification_t");
                $title 		= $wr['pst_title'];
                $content 	= $wr['pst_content'];
                $push_link = '';
                break;

            default:
                $title      = '알림';
                $content    = $send_msg ? $send_msg : "";
                break;
        }

        if (!$title && !$content) { $send_check = false; }

        $title  = $title ? $title : '알림';
        $content = $content ? $content : "새소식이 도착했습니다";

        $send_title = $title;
        $send_msg = get_text(stripslashes($content));

        if ($send_check) {
            /*switch ($push_type) {
                case 'push':
                case 'review':
                    $chk_status = "mt_pushing1";
                    break;
                case 'admin':
                case 'notice':
                    $chk_status = "mt_pushing2";
                    break;
                default:
                    $chk_status = "mt_pushing";
                    break;
            }*/

            $DB->where('a1.del_status', 'N');
            $DB->where("a1.mt_status", 'Y');
            if ($pst_level && $pst_level != '1') {
                $DB->where("a1.mt_level", $pst_level);
            } else {
                $DB->where("a1.mt_level", '2');
            }
            $push_qry = "";
            for ($j=0;$j<count($send_to_arr);$j++) {
                $push_qry .= "idx = '{$send_to_arr[$j]}'";
                if ($j<count($send_to_arr)-1) {
                    $push_qry .= " OR ";
                }
            }
            $DB->where("({$push_qry})");
            $result = $DB->get("member_t a1", null, "*, idx as mt_idx");
            //------------------------------------------------------------------------------------------
            $total = $DB->count;
            $total_page = ceil($total/1000);//총 페이지
            $ii = 0;
            $data = array();
            foreach ($result as $row) {
                $data[$ii] = $row;
                $ii++;
            }
            //------------------------------------------------------------------------------------------
            $i = 0;
            for($p=1; $p<=$total_page; $p++){
                $tokens = array();

                if($total-(1000 * $p) > 0){//다음페이지가 있는지(남은게 천개보다 큰지) 확인.
                    $max = 1000;
                }else{
                    $max = $total-(1000 * ($p-1));//$total;
                }

                $send_to_id = '';//',';
                $send_to_id_arr = array();
                for($j=0; $j<$max; $j++){
                    if ($data[$i]['mt_pushing'.$pst_category]==='Y') {
                        $tokens[] = $data[$i]["mt_app_token"];
                    }
                    $send_to_id_arr[] = $data[$i]["mt_idx"];
                    //$send_to_id .= ',';

                    $i++;
                }
                $send_to_id_arr = array_values(array_filter(array_map('trim',$send_to_id_arr)));
                $send_to_id = implode(',', $send_to_id_arr);

                $sound = "default";
                $channel_id = "";

                $_last_idx = "";
                if ($push_board==='notice' || ($push_type == 'sentBy_mng' && $ref_param['prt_class'] == '1')) { $send_to_id = ''; }
                if ($push_type==="admin" || $push_type==="chat") {
                    $_last_idx = $ref_idx;
                } else {
                    $send_msg = addslashes($send_msg);
                    $send_msg = str_replace('&gt;','>',$send_msg);
                    $send_msg = str_replace('&lt;','<',$send_msg);
                    $send_msg = str_replace('&nbsp;',' ',$send_msg);
                    $send_msg = str_replace('<br />',"\n",$send_msg);

                    unset($arr_query);
                    $arr_query = array(
                        'pst_type' => $pst_type,
                        'pst_index' => $ref_idx,
                        'pst_category' => $pst_category,
                        'pst_level' => $pst_level,
                        'pst_link' => $push_link,
                        'send_to' => $send_to_id,
                        'pst_title' => $send_title,
                        //'pst_shot_memo' => $shot_title,
                        'pst_content' => $send_msg,
                        'pst_wdate' => $DB->now(),
                        'pst_show' => 'Y',
                        'pst_count' => count($send_to_id_arr),
                    );
                    if (!$show_noti) {
                        $arr_query['pst_show'] = 'N';
                    }

                    if ($pst_type==='orderFinCheck_seller') {
                        $DB->where('pst_index', $ref_idx)->where('pst_type', $pst_type)->where('send_to', $send_to_id);
                        $chk = $DB->getone("pushnotification_t");
                        if ($chk['idx']) {
                            unset($arr_query);
                            $arr_query = array(
                                'pst_wdate' => $DB->now(),
                            );
                            $DB->where('idx', $chk['idx']);
                            $DB->update('pushnotification_t', $arr_query);
                            $_last_idx = $chk['idx'];
                        } else {
                            $_last_idx = $DB->insert('pushnotification_t', $arr_query);
                        }
                    } else {
                        $_last_idx = $DB->insert('pushnotification_t', $arr_query);
                    }
                }
                if ($tokens) {
                    $send_msg = cut_str($send_msg, 0, 200, '..');
                    $send_msg = stripslashes($send_msg);
                    $send_msg = str_replace('&gt;','>',$send_msg);
                    $send_msg = str_replace('&lt;','<',$send_msg);
                    $send_msg = str_replace('&nbsp;',' ',$send_msg);
                    $send_msg = str_replace('<br />',"\n",$send_msg);

                    //$send_to_id_arr = explode(',', $send_to_id);
                    //$send_to_id_arr = array_values(array_filter(array_map('trim',$send_to_id_arr)));

                    $message = array("title" => $send_title, "body" => $send_msg, "image" => ""
                        //, "event_url" => $push_link
                        //, "icon" => "", "sound" => $sound, "android_channel_id" => $channel_id
                        //, "ref_idx" => $ref_idx
                        //, "ref_param" => $ref_param
                        //, "push_id" => $_last_idx
                        //,"push_type" => $pst_type, "push_level" => $pst_level, "send_to" => $send_to_id_arr
                    );
                    $message1 = array("title" => $send_title, "body" => $send_msg, "image" => ""
                    , "event_url" => $push_link
                    );
                    if ($pst_level > '1') {
                        $projectId = PROJECT_ID_SELLER;
                        $app_key = AUTH_KEY_CONTENT_FILE_NM_SELLER;
                    } else {
                        $projectId = PROJECT_ID;
                        $app_key = AUTH_KEY_CONTENT_FILE_NM;
                    }
                    $message_status = send_notification($tokens, $message, $message1, $app_key, $projectId, $_last_idx);
                    /*if ($message_status) {
                        unset($arr_query);
                        $arr_query = array(
                            'message_status' => $message_status,
                        );
                        $DB->where('idx', $_last_idx);
                        $DB->update('pushnotification_t', $arr_query);
                    }*/
                }
                sleep(1);//천개 보내고 휴식
            }
        }
        $retArr = array();
        $retArr['send_to'] = $send_to;
        $retArr['tokens'] = $tokens;
        $retArr['message'] = $message;
        $retArr['message1'] = $message1;
        $retArr['response'] = $message_status;
        $retArr['send_to_id'] = $send_to_id;
        //$retArr['wr'] = $wr;
        //$retArr['wr_data'] = $wr_data;

        return $retArr;
    } else {
        $retArr = array();
        $retArr['send_to'] = $send_to;
        $retArr['message'] = "";
        $retArr['response'] = "";

        return $retArr;
    }
}
//----------------------------------------------------------------------------------------------------------------------
//function get_template_info($ptl_idx) {
//    global $DB;
//
//    $DB->where('idx', $ptl_idx);
//    $row4 = $DB->getone('template_t');
//
//    /*
//    unset($arr_rtn);
//
//    if($row4['pdt_add_section_price_chk']=='Y') {
//        $row4['pdt_add_section_price_chk_t'] = '1';
//    } else {
//        $row4['pdt_add_section_price_chk_t'] = '2';
//    }
//    if($row4['pdt_add_section_price_type_chk']=='2') {
//        $row4['pdt_add_section_price_type_chk_t'] = '1';
//    } else {
//        $row4['pdt_add_section_price_type_chk_t'] = '2';
//    }
//
//    $arr_rtn['product_deliveryInfo_t'] = $row4;*/
//
//    if($row4=='') {
//        $row4 = array();
//    }
//
//    return $row4;
//}

function get_mt_code($mt_login_type = 1) {
    global $DB;

    $unique = false;
    do {
        $mb_uid = rand_str(1,3).substr(strtoupper(md5(mt_rand())), 0, 5);
        //$query = "select COUNT(*) AS cnt from member_t where mt_login_type <> '".$mt_login_type."' and mt_id = '".$mb_uid."'";
        //$cnt = $DB->fetch_query($query);
        $DB->where('mt_id', $mb_uid)->where("mt_login_type != '".$mt_login_type."'");
        $cnt = $DB->getone('member_t', 'COUNT(*) AS cnt');
        if ($cnt['cnt'] < 1) {
            $unique = true;
            break;
        }
    }
    while ($unique == false);

    return $mb_uid;
}

function get_pt_code() {
    global $DB;

    $unique = false;
    do {
        $pt_code = rand_str(1,3).substr(strtoupper(md5(mt_rand())), 0, 7);
        //$query = "select COUNT(*) AS cnt from product_t where pt_code = '".$pt_code."'";
        //$cnt = $DB->fetch_query($query);
        $DB->where('pt_code', $pt_code);
        $cnt = $DB->getone('product_t', 'COUNT(*) AS cnt');
        if ($cnt['cnt'] < 1) {
            $unique = true;
            break;
        }
    }
    while ($unique == false);

    return $pt_code;
}

function get_uid() {
    global $DB;

    $unique = false;
    do {
        $uid = date('YmdHis', time()) . str_pad((int)((float)microtime()*100), 2, "0", STR_PAD_LEFT);
        //$query = "select COUNT(*) AS cnt from uniqid_t where uq_id = '".$uid."'";
        //$cnt = $DB->fetch_query($query);
        $DB->where('uq_id', $uid);
        $cnt = $DB->getone('uniqid_t', 'COUNT(*) AS cnt');
        if ($cnt['cnt'] < 1) {
            unset($arr_query);
            $arr_query = array(
                'uq_id' => $uid,
                'uq_ip' => $_SERVER['REMOTE_ADDR'],
            );
            $DB->insert('uniqid_t', $arr_query);
            $unique = true;
            break;
        }
    }
    while ($unique == false);

    return $uid;
}

function get_ot_code() {
    global $DB;

    $unique = false;
    do {
        $uid = substr(date("ymd", time()).strtoupper(md5(mt_rand())), 0, 10);
        //$query = "select COUNT(*) AS cnt from order_t where ot_code = '".$uid."'";
        //$cnt = $DB->fetch_query($query);
        $DB->where('ot_code', $uid);
        $cnt = $DB->getone('order_t', 'COUNT(*) AS cnt');
        if ($cnt['cnt'] < 1) {
            $unique = true;
            break;
        }
    }
    while ($unique == false);

    return $uid;
}

function get_ot_pcode() {
    global $DB;

    $unique = false;
    do {
        $uid = substr("P".date("ymdHis", time()).strtoupper(md5(mt_rand())), 0, 16);
        $DB->where('ot_pcode', $uid);
        $cnt = $DB->getone('cart_t', 'COUNT(*) AS cnt');
        if ($cnt['cnt'] < 1) {
            $unique = true;
            break;
        }
    }
    while ($unique == false);

    return $uid;
}

function get_cpt_code() {
    global $DB;

    $unique = false;
    do {
        $uid = substr(date("ymd", time()).strtoupper(md5(mt_rand())), 0, 10);
        $DB->where('cpt_code', $uid);
        $cnt = $DB->getone('calculate_payout_t', 'COUNT(*) AS cnt');
        if ($cnt['cnt'] < 1) {
            $unique = true;
            break;
        }
    }
    while ($unique == false);

    return $uid;
}
function get_cpt_uuid() {
    global $DB;

    $unique = false;
    do {
        $uid = rand_str(15,7).substr(strtoupper(md5(mt_rand())), 0, 5);
        $DB->where('cpt_uuid', $uid);
        $cnt = $DB->getone('calculate_uuid_t', 'COUNT(*) AS cnt');
        if ($cnt['cnt'] < 1) {
            $unique = true;
            break;
        }
    }
    while ($unique == false);

    return $uid;
}
function get_payoutStoreId() {
    global $DB;

    $unique = false;
    do {
        $uid = 'T'.substr(date("ymd", time()).rand_str(5,1), 0, 10);
        $DB->where('payoutStoreId', $uid);
        $cnt = $DB->getone('store_t', 'COUNT(*) AS cnt');
        if ($cnt['cnt'] < 1) {
            $unique = true;
            break;
        }
    }
    while ($unique == false);

    return $uid;
}

function get_unick($mt_id="") {
    global $DB;

    $unique = false;
    do {
        $uid = '회원'.substr(date("ymdHi", time()).strtoupper(md5(mt_rand())), 0, 13);
        $DB->where('mt_nick', $uid);
        if ($mt_id) {
            $DB->where("mt_id != '".$mt_id."'");
        }
        $cnt = $DB->getone('member_t', 'COUNT(*) AS cnt');
        if ($cnt['cnt'] < 1) {
            $unique = true;
            break;
        }
    }
    while ($unique == false);

    return $uid;
}

function mt_id_pad($mt_id) {
    if ($mt_id) {
        return cut_str($mt_id, 0, 1, '').'****';
    } else {
        return "";
    }
    //return str_pad(cut_str($mt_id, 0, 3, ''), 7, '****');
}
function mt_str_pad($string){ // mb_str_pad("텍스트", 40, '*')
    $string = trim($string);
    $length = mb_strlen($string, 'utf-8');
    $string_changed = $string;
    if ($length <= 2) { // 한두 글자면 그냥 뒤에 별표 붙여서 내보낸다.
        $string_changed = mb_substr($string, 0, 1, 'utf-8') . '*';
    }
    if ($length >= 3) { // 3으로 나눠서 앞뒤.
        $leave_length = floor($length/3); // 남겨 둘 길이.
        $asterisk_length = $length - ($leave_length * 2);
        $offset = $leave_length + $asterisk_length;
        $head = mb_substr($string, 0, $leave_length, 'utf-8');
        $tail = mb_substr($string, $offset, $leave_length, 'utf-8');
        $string_changed = $head . implode('', array_fill(0, $asterisk_length, '*')) . $tail;
    }
    return $string_changed;
}
//------------------------------------------------------------------------------------------------------------------
function get_path()
{
    $result['path'] = str_replace('\\', '/', dirname(__FILE__));
    $tilde_remove = preg_replace('/^\/\~[^\/]+(.*)$/', '$1', $_SERVER['SCRIPT_NAME']);
    $document_root = str_replace($tilde_remove, '', $_SERVER['SCRIPT_FILENAME']);
    $root = str_replace($document_root, '', $result['path']);
    $port = $_SERVER['SERVER_PORT'] != 80 ? ':'.$_SERVER['SERVER_PORT'] : '';
    $http = 'http' . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on') ? 's' : '') . '://';
    $user = str_replace(str_replace($document_root, '', $_SERVER['SCRIPT_FILENAME']), '', $_SERVER['SCRIPT_NAME']);
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
    if(isset($_SERVER['HTTP_HOST']) && preg_match('/:[0-9]+$/', $host))
        $host = preg_replace('/:[0-9]+$/', '', $host);
    $result['url'] = $http.$host.$port.$user.$root;
    return $result;
}
// 세션변수 생성
function set_session($session_name, $value) {
    $$session_name = $_SESSION[$session_name] = $value;
}
// 세션변수값 얻음
function get_session($session_name) {
    return isset($_SESSION[$session_name]) ? $_SESSION[$session_name] : '';
}
// 쿠키변수 생성
function set_cookie($cookie_name, $value, $expire) {
    setcookie(md5($cookie_name), base64_encode($value), time() + $expire, '/', '');
    //setcookie($cookie_name, $value, time() + $expire, '/', '');
}
// 쿠키변수값 얻음
function get_cookie($cookie_name) {
    $cookie = md5($cookie_name);
    if (array_key_exists($cookie, $_COOKIE))
        return base64_decode($_COOKIE[$cookie]);
    else
        return "";
}
//------------------------------------------------------------------------------------------------------------------
function get_selected($field, $value)
{
    return ($field==$value) ? ' selected="selected"' : '';
}
//------------------------------------------------------------------------------------------------------------------
// HTML 특수문자 변환 htmlspecialchars
function htmlspecialchars2($str) {
    $trans = array("\"" => "&#034;", "'" => "&#039;", "<"=>"&#060;", ">"=>"&#062;");
    $str = strtr($str, $trans);
    return $str;
}
//------------------------------------------------------------------------------------------------------------------
// 내용을 변환
function conv_content($content, $html, $filter=true, $conv_txt=array()) {
    if ($html) {
        $source = array();
        $target = array();

        $source[] = "//";
        $target[] = "";

        if ($html == 2) { // 자동 줄바꿈
            $source[] = "/\n/";
            $target[] = "<br/>";
        }

        // 테이블 태그의 개수를 세어 테이블이 깨지지 않도록 한다.
        $table_begin_count = substr_count(strtolower($content), "<table");
        $table_end_count = substr_count(strtolower($content), "</table");
        for ($i=$table_end_count; $i<$table_begin_count; $i++)
        {
            $content .= "</table>";
        }

        $content = preg_replace($source, $target, $content);

        if($filter)
            $content = xss_clean($content);
    }
    else // text 이면
    {
        // & 처리 : &amp; &nbsp; 등의 코드를 정상 출력함
        $content = html_symbol($content);

        // 공백 처리
//			$content = str_replace("  ", "&nbsp; ", $content);
//			$content = str_replace("\n ", "\n&nbsp;", $content);
        $content = str_replace("&#038;nbsp;", " ", $content);

        $content = get_text($content, 1);

        $content = url_auto_link($content);
    }

    if ($conv_txt) {
        // $src 를 $dst 로 변환
        unset($src);
        unset($dst);
        $get_video_html = "";
        if ($conv_txt['pt_video']) {
            $get_video_html = get_video_html($conv_txt);
        }

        if (strpos($content, "{{video_viewer}}") != false) {
            $src[] = "/{{video_viewer}}/";
            $dst[] = $get_video_html;

            if ($src || $dst) {
                $content = preg_replace($src, $dst, $content);
            }
        } else {
            $content .= $get_video_html;
        }
    }

    return $content;
}
// HTML SYMBOL 변환
// &nbsp; &amp; &middot; 등을 정상으로 출력
function html_symbol($str) {
    return preg_replace("/\&([a-z0-9]{1,20}|\#[0-9]{0,3});/i", "&#038;\\1;", $str);
}
function url_auto_link($str) {
    $str = str_replace(array("&lt;", "&gt;", "&amp;", "&quot;", "&nbsp;", "&#039;"), array("\t_lt_\t", "\t_gt_\t", "&", "\"", "\t_nbsp_\t", "'"), $str);
    $str = preg_replace("/([^(href=\"?'?)|(src=\"?'?)]|\(|^)((http|https|ftp|telnet|news|mms):\/\/[a-zA-Z0-9\.-]+\.[가-힣\xA1-\xFEa-zA-Z0-9\.:&#!=_\?\/~\+%@;\-\|\,\(\)]+)/i", "\\1<A HREF=\"\\2\" TARGET=\"\">\\2</A>", $str);
    $str = preg_replace("/(^|[\"'\s(])(www\.[^\"'\s()]+)/i", "\\1<A HREF=\"http://\\2\" TARGET=\"\">\\2</A>", $str);
    $str = preg_replace("/[0-9a-z_-]+@[a-z0-9._-]{4,}/i", "<a href=\"mailto:\\0\">\\0</a>", $str);
    $str = str_replace(array("\t_nbsp_\t", "\t_lt_\t", "\t_gt_\t", "'"), array("&nbsp;", "&lt;", "&gt;", "&#039;"), $str);

    return $str;
}
//------------------------------------------------------------------------------------------------------------------
// 마이크로 타임을 얻어 계산 형식으로 만듦
function get_microtime() {
    list($usec, $sec) = explode(" ",microtime());
    return ((float)$usec + (float)$sec);
}
// 파일명에서 특수문자 제거
function get_safe_filename($name) {
    $pattern = '/["\'<>=#&!%\\\\(\)\*\+\?]/';
    $name = preg_replace($pattern, '', $name);

    return $name;
}
// 파일명 치환
function replace_filename($name) {
    @session_start();
    $ss_id = session_id();
    $usec = get_microtime();
    $file_path = pathinfo($name);
    $ext = $file_path['extension'];
    $return_filename = sha1($ss_id.$_SERVER['REMOTE_ADDR'].$usec);
    if( $ext )
        $return_filename .= '.'.$ext;

    return $return_filename;
}
// 첨부파일 썸네일 삭제
function delete_file_thumbnail($file) { // $filepath
    if(!$file)
        return;

    $fn = preg_replace("/\.[^\.]+$/i", "", basename($file));
    $files = glob(DATA_PATH.'/thumb-'.$fn.'*');
    if (is_array($files)) {
        foreach ($files as $filename)
            unlink($filename);
    }
}
// 에디터 이미지 얻기
function get_editor_image($contents, $view=true) {
    if(!$contents)
        return false;

    // $contents 중 img 태그 추출
    if ($view)
        $pattern = "/<img([^>]*)>/iS";
    else
        $pattern = "/<img[^>]*src=[\'\"]?([^>\'\"]+[^>\'\"]+)[\'\"]?[^>]*>/i";
    preg_match_all($pattern, $contents, $matchs);

    return $matchs;
}
// 에디터 썸네일 삭제
function delete_editor_thumbnail($contents) {
    if(!$contents)
        return;

    // $contents 중 img 태그 추출
    $matchs = get_editor_image($contents, false);

    if(!$matchs)
        return;

    for($i=0; $i<count($matchs[1]); $i++) {
        // 이미지 path 구함
        $imgurl = @parse_url($matchs[1][$i]);
        $srcfile = $_SERVER['DOCUMENT_ROOT'].$imgurl['path'];

        $filename = preg_replace("/\.[^\.]+$/i", "", basename($srcfile));
        $filepath = dirname($srcfile);
        $files = glob($filepath.'/thumb-'.$filename.'*');
        if (is_array($files)) {
            foreach($files as $filename)
                unlink($filename);
        }
    }
}
// 에디터 이미지 삭제
function delete_editor_image($content, $mode='del') {
    global $ct_editor_url, $ct_editor_dir;

    if(!$content || $mode == 'move') {
        return $content;
    } else if($mode == 'del') {
        ;
    } else {
        return ($mode == 'copy') ? $content : '';
    }

    $imgs = get_editor_image($content, false);

    for($i=0;$i<count($imgs[1]);$i++) {

        // 이미지 path 구함
        $p = @parse_url($imgs[1][$i]);

        //if(strpos($p['path'], "/uploads/") != 0) {
        //    $data_path = preg_replace("/^\/.*\/uploads/", "/uploads", $p['path']);
        //} else {
        $data_path = $p['path'];
        //}

        $is_destfile = false;
        if(preg_match('/(gif|jpe?g|bmp|png)$/i', strtolower(end(explode('.', $data_path))))){

            $destfile = ( ! preg_match('/\w+\/\.\.\//', $data_path) ) ? GET_PATH.$data_path : '';

            if($destfile && preg_match('/\/images\/editor\//', $destfile) && is_file($destfile)) {
                $is_destfile = true;
            }
        }

        if($is_destfile) {
            if($mode == 'copy') { //다른이름으로 복사
                $ym = date('ym', SERVER_TIME);
//					$data_dir = GET_PATH.'/editor/'.$ym;
//                    $data_url = DATA_URL.'/editor/'.$ym;
                $data_dir = $ct_editor_dir;
                $data_url = $ct_editor_url;
                if(!is_dir($data_dir)) {
                    @mkdir($data_dir, DIR_PERMISSION);
                    @chmod($data_dir, DIR_PERMISSION);
                }
                $filename = basename($destfile);
                $chars_array = array_merge(range(0,9), range('a','z'), range('A','Z'));
                shuffle($chars_array);
                $shuffle = implode('', $chars_array);
                $file_name = abs(ip2long($_SERVER['REMOTE_ADDR'])).'_'.substr($shuffle,0,8).'_'.replace_filename($filename);
                $save_file = sprintf('%s/%s', $data_dir, $file_name);
                $save_url = sprintf('%s/%s', $data_url, $file_name);
                @copy($destfile, $save_file);
                @chmod($save_file, FILE_PERMISSION);
                $content = str_replace($imgs[1][$i], $save_url, $content);
            } else { //이미지 삭제
                @chmod($destfile, FILE_PERMISSION);
                @unlink($destfile);
            }
        }
    }

    return ($mode == 'copy') ? $content : '';
}
// 에디터 이미지 삭제
function delete_editor_image_diff($content1, $content2) {
    if ($content1) {
        $pt_content_img1 = array();
        $imgs = get_editor_image($content1, false);
        if ($imgs) {
            for($i=0;$i<count($imgs[1]);$i++) {
                // 이미지 path 구함
                $p = @parse_url($imgs[1][$i]);

                $data_path = $p['path'];

                if(preg_match('/(gif|jpe?g|bmp|png)$/i', strtolower(end(explode('.', $data_path))))){
                    $destfile = ( ! preg_match('/\w+\/\.\.\//', $data_path) ) ? GET_PATH.$data_path : '';
                    if($destfile && preg_match('/\/images\/editor\//', $destfile) && is_file($destfile)) {
                        $pt_content_img1[] = $destfile;
                    }
                }
            }
        }
        unset($imgs);

        $pt_content_img2 = array();
        $imgs = get_editor_image($content2, false);
        if ($imgs) {
            for($i=0;$i<count($imgs[1]);$i++) {
                // 이미지 path 구함
                $p = @parse_url($imgs[1][$i]);

                $data_path = $p['path'];

                if(preg_match('/(gif|jpe?g|bmp|png)$/i', strtolower(end(explode('.', $data_path))))){
                    $destfile = ( ! preg_match('/\w+\/\.\.\//', $data_path) ) ? GET_PATH.$data_path : '';
                    if($destfile && preg_match('/\/images\/editor\//', $destfile) && is_file($destfile)) {
                        $pt_content_img2[] = $destfile;
                    }
                }
            }
        }
        unset($imgs);

        $result = array_values(array_diff($pt_content_img1, $pt_content_img2));
        foreach ($result as $value) {
            if ($value && is_file($value)) {
                @chmod($value, FILE_PERMISSION);
                @unlink($value);
            }
        }
    }

    return true;
}
// 에디터 이미지 리사이징
function resize_editor_image($content) {
    global $ct_editor_url, $ct_editor_dir;

    if ($content) {
        $imgs = get_editor_image($content, false);
        for($i=0;$i<count($imgs[1]);$i++) {
            // 이미지 path 구함
            $p = @parse_url($imgs[1][$i]);

            $data_path = $p['path'];
            $data_name = $p['path'];
            $data_name = str_replace($ct_editor_url.'/','',$data_name);
            $data_name = str_replace('/images/editor/','',$data_name);

            if(preg_match('/(gif|jpe?g|bmp|png)$/i', strtolower(end(explode('.', $data_path))))){
                $destfile = ( ! preg_match('/\w+\/\.\.\//', $data_path) ) ? GET_PATH.$data_path : '';
                if($destfile && preg_match('/\/images\/editor\//', $destfile) && is_file($destfile)) {
                    thumnail_width($destfile, $data_name, $ct_editor_dir."/", 1200);
                }
            }
        }
    }
}

function get_video_html($row){
    global $ct_video_dir, $ct_video_url;
    $shtml = '';

    if ($row['pt_video'] && is_file($ct_video_dir.'/'.$row['pt_video'])) {
        $pt_video_size = explode(':', $row['pt_video_size']);
        $maxWidth = 700;
        $maxHeight = 700;
        $newWidth = $sourceWidth = $pt_video_size[0];
        $newHeight = $sourceHeight = $pt_video_size[1];
        if ($sourceWidth > $maxWidth) {
            $aspectRatio = $sourceWidth / $sourceHeight;

            if ($sourceWidth > $maxWidth || $sourceHeight > $maxHeight) {
                if ($maxWidth / $maxHeight > $aspectRatio) {
                    $maxWidth = $maxHeight * $aspectRatio;
                } else {
                    $maxHeight = $maxWidth / $aspectRatio;
                }

                $newWidth = $maxWidth;
                $newHeight = $maxHeight;
            } else {
                $newWidth = $sourceWidth;
                $newHeight = $sourceHeight;
            }
        }

        $file_url = $ct_video_url.'/'.$row['pt_video'];
        $file_name = str_replace('.mp4','',$row['pt_video']);

        $shtml .= '<div style="text-align: center;">';
        $shtml .= '<video width="'.$newWidth.'" height="'.$newHeight.'" controls>';
        $shtml .=   '<source src="'.$ct_video_url.'/'.$file_name.'.mp4" type="video/mp4">';
        $shtml .=   '<source src="'.$ct_video_url.'/'.$file_name.'.ogg" type="video/ogg">';
        $shtml .=   '<source src="'.$ct_video_url.'/'.$file_name.'.webm" type="video/webm">';
        $shtml .=   '<object data="'.$ct_video_url.'/'.$file_name.'.mp4" width="'.$newWidth.'" height="'.$newHeight.'">';
        $shtml .=     '<embed src="'.$ct_video_url.'/'.$file_name.'.swf" width="'.$newWidth.'" height="'.$newHeight.'">';
        $shtml .=   '</object>';
        $shtml .= '</video>';
        $shtml .= '</div>';
    }
    return $shtml;
}

// 동일한 host url 인지
function check_url_host($url, $msg='', $return_url=APP_DOMAIN, $is_redirect=false)
{
    if(!$msg)
        $msg = 'url에 타 도메인을 지정할 수 없습니다.';

    $p = @parse_url($url);
    $host = preg_replace('/:[0-9]+$/', '', $_SERVER['HTTP_HOST']);
    $is_host_check = false;

    // url을 urlencode 를 2번이상하면 parse_url 에서 scheme와 host 값을 가져올수 없는 취약점이 존재함
    if ( $is_redirect && !isset($p['host']) && urldecode($url) != $url ){
        $i = 0;
        while($i <= 3){
            $url = urldecode($url);
            if( urldecode($url) == $url ) break;
            $i++;
        }

        if( urldecode($url) == $url ){
            $p = @parse_url($url);
        } else {
            $is_host_check = true;
        }
    }

    if(stripos($url, 'http:') !== false) {
        if(!isset($p['scheme']) || !$p['scheme'] || !isset($p['host']) || !$p['host'])
            alert('url 정보가 올바르지 않습니다.', $return_url);
    }

    //php 5.6.29 이하 버전에서는 parse_url 버그가 존재함
    //php 7.0.1 ~ 7.0.5 버전에서는 parse_url 버그가 존재함
    if ( $is_redirect && (isset($p['host']) && $p['host']) ) {
        $bool_ch = false;
        foreach( array('user','host') as $key) {
            if ( isset( $p[ $key ] ) && strpbrk( $p[ $key ], ':/?#@' ) ) {
                $bool_ch = true;
            }
        }
        if( $bool_ch ){
            $regex = '/https?\:\/\/'.$host.'/i';
            if( ! preg_match($regex, $url) ){
                $is_host_check = true;
            }
        }
    }

    if ((isset($p['scheme']) && $p['scheme']) || (isset($p['host']) && $p['host']) || $is_host_check) {
        //if ($p['host'].(isset($p['port']) ? ':'.$p['port'] : '') != $_SERVER['HTTP_HOST']) {
        if ( ($p['host'] != $host) || $is_host_check ) {
            echo '<script>'.PHP_EOL;
            echo 'alert("url에 타 도메인을 지정할 수 없습니다.");'.PHP_EOL;
            echo 'document.location.href = "'.$return_url.'";'.PHP_EOL;
            echo '</script>'.PHP_EOL;
            echo '<noscript>'.PHP_EOL;
            echo '<p>'.$msg.'</p>'.PHP_EOL;
            echo '<p><a href="'.$return_url.'">돌아가기</a></p>'.PHP_EOL;
            echo '</noscript>'.PHP_EOL;
            exit;
        }
    }
}

function check_mail_bot($ip=''){

    //아이피를 체크하여 메일 크롤링을 방지합니다.
    $check_ips = array('211.249.40.');
    $bot_message = 'bot 으로 판단되어 중지합니다.';

    if($ip){
        foreach( $check_ips as $c_ip ){
            if( preg_match('/^'.preg_quote($c_ip).'/', $ip) ) {
                die($bot_message);
            }
        }
    }

    // user agent를 체크하여 메일 크롤링을 방지합니다.
    $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    if ($user_agent === 'Carbon' || strpos($user_agent, 'BingPreview') !== false || strpos($user_agent, 'Slackbot') !== false) {
        die($bot_message);
    }
}

function escape_trim($field){
    $str = call_user_func('sql_escape_string', $field);
    return $str;
}

//htmlpurify
function xss_clean($data) {
    global $purifier;

    $rtn = $purifier->purify($data);

    return $rtn;
}
function xss_clean_arr($data) {
    global $purifier;

    $rtn = array();
    foreach($data as $key => $val) {
        $rtn[$key] = $purifier->purify($val);
    }

    return $rtn;
}
// XSS 관련 태그 제거
function clean_xss_tags($str, $check_entities=0, $is_remove_tags=0, $cur_str_len=0, $is_trim_both=0) {
    $str = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $str);
    $str = str_replace('&lt;script&gt;', '', $str);
    $str = str_replace('&lt;/script&gt;', '', $str);

    if( $is_trim_both ) {
        // tab('\t'), formfeed('\f'), vertical tab('\v'), newline('\n'), carriage return('\r') 를 제거한다.
        $str = preg_replace("#[\t\f\v\n\r]#", '', $str);
    }

    if( $is_remove_tags ){
        $str = strip_tags($str);
    }

    if( $cur_str_len ){
        $str = utf8_strcut($str, $cur_str_len, '');
    }

    $str_len = strlen($str);

    $i = 0;
    while($i <= $str_len){
        $result = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $str);

        if( $check_entities ){
            $result = str_replace(array('&colon;', '&lpar;', '&rpar;', '&NewLine;', '&Tab;'), '', $result);
        }

        $result = preg_replace('#([^\p{L}]|^)(?:javascript|jar|applescript|vbscript|vbs|wscript|jscript|behavior|mocha|livescript|view-source)\s*:(?:.*?([/\\\;()\'">]|$))#ius',
            '$1$2', $result);

        if((string)$result === (string)$str) break;

        $str = $result;
        $i++;
    }

    return $str;
}
// UTF-8 문자열 자르기
function utf8_strcut( $str, $size, $suffix='...' ) {
    if( function_exists('mb_strlen') && function_exists('mb_substr') ){
        if(mb_strlen($str)<=$size) {
            return $str;
        } else {
            $str = mb_substr($str, 0, $size, 'utf-8');
            $str .= $suffix;
        }
    } else {
        $substr = substr( $str, 0, $size * 2 );
        $multi_size = preg_match_all( '/[\x80-\xff]/', $substr, $multi_chars );

        if ( $multi_size > 0 )
            $size = $size + intval( $multi_size / 3 ) - 1;

        if ( strlen( $str ) > $size ) {
            $str = substr( $str, 0, $size );
            $str = preg_replace( '/(([\x80-\xff]{3})*?)([\x80-\xff]{0,2})$/', '$1', $str );
            $str .= $suffix;
        }
    }

    return $str;
}

//------------------------------------------------------------------------------------------------------------------
/*
	 * 랜덤 문자열 생성(인수 : 길이, 타입)
	 * 지정된 타입의 문자열로 지정된 길이의 랜덤 문자열을 반환한다.
	 * 타입 0 : 영문 대소문자(A-Z,a-z), 숫자(0-9)
	 * 타입 1 : 영문 대문자(A-Z), 숫자(0-9)
	 * 타입 2 : 영문 소문자(a-z), 숫자(0-9)
	 * 타입 3 : 영문 대문자(A-Z)
	 * 타입 4 : 영문 소문자(a-z)
	 * 타입 5 : 숫자(0-9)
	 * 디폴트 : false 반환.
	*/
function rand_str($length, $type) {
    switch($type){
        case 0:
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
            break;
        case 1:
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
            break;
        case 2:
            $chars = 'abcdefghijklmnopqrstuvwxyz1234567890';
            break;
        case 3:
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            break;
        case 4:
            $chars = 'abcdefghijklmnopqrstuvwxyz';
            break;
        case 5:
            $chars = '1234567890';
            break;
        case 6:
            $chars = '!@#$%^&*';
            break;
        case 7:
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
            break;
        case 8:
            $chars = '123456789';
            break;
        default:
            return false;
    }

    $chars_length = (strlen($chars) - 1);
    $string = '';

    for ($i = 0; $i < $length; $i = strlen($string)){
        //$string .= $chars{rand(0, $chars_length)};
        $string .= substr($chars, rand(0, $chars_length), 1);
    }

    return $string;
}
//------------------------------------------------------------------------------------------------------------------
if(!function_exists('get_member_code')){
    //회원코드생성
    function get_member_code($random_chars = '')
    {
        $characters = array(
            "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "m",
            "n", "p", "r", "s", "t", "u", "v", "w", "x", "y", "z",
            "1", "2", "3", "4", "5", "6", "7", "8", "9");

        $characters_eng = array(
            "A", "B", "C", "D", "E", "F", "G", "H", "J", "K", "L", "M",
            "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z",
        );

        $characters_int = array(
            "1", "2", "3", "4", "5", "6", "7", "8", "9");

        $keys = array();

        while (count($keys) < 5){
            $x = mt_rand(0, count($characters) - 1);

            //if(!in_array($x, $keys)) {
            $keys[] = $x;
            //}
        }

        foreach ($keys as $key){
            $random_chars .= $characters[$key];
        }

        return $random_chars;
    }
}
//------------------------------------------------------------------------------------------------------------------
function get_uniqid() {
    global $DB;

    //$DB->db_query(" LOCK TABLE uniqid_t WRITE ");
    while (1) {
        // 년월일시분초에 100분의 1초 두자리를 추가함 (1/100 초 앞에 자리가 모자르면 0으로 채움)
        $key = date('YmdHis', time()) . str_pad((int)(microtime()*100), 2, "0", STR_PAD_LEFT);

        unset($arr_query);
        $arr_query = array(
            "uq_id" => $key,
            "uq_ip" => $_SERVER['REMOTE_ADDR'],
        );
        $_last_idx = $DB->insert('uniqid_t', $arr_query);
        //$_last_idx = $DB->db_query(" insert into uniqid_t set uq_id = '$key', uq_ip = '{$_SERVER['REMOTE_ADDR']}' ", false);
        if ($_last_idx) break; // 쿼리가 정상이면 빠진다.

        // insert 하지 못했으면 일정시간 쉰다음 다시 유일키를 만든다.
        usleep(10000); // 100분의 1초를 쉰다
    }
    //$DB->db_query(" UNLOCK TABLES ");

    return $key;
}
//------------------------------------------------------------------------------------------------------------------
function sort_link($col, $flag='asc', $query_string=''){
    global $sst, $sod;
    $sst = $sst ? $sst : $_REQUEST['sst'];
    $sod = $sod ? $sod : $_REQUEST['sod'];

    $arr = array();
    $arr1 = explode('&',$query_string);
    foreach ($arr1 as $row) {
        $arr2 = explode('=',$row);
        if (!($arr2[0]==='sst' || $arr2[0]==='sod')) {
            $arr[] = $row;
        }
    }

    $query_string = implode("&amp;", $arr);

    $arr_query = array();
    $arr_query[] = "sst=$col";
    $arr_query[] = "sod=$flag";
    $qstr = implode("&amp;", $arr_query);

    $addClass = $sst===$col&&$sod===$flag?'text-primary':'';

    $str  = "<a href=\"?{$query_string}&amp;{$qstr}\" data-sst=\"{$col}\" data-sod=\"{$flag}\" class=\"sort_link {$addClass}\">▲</a>";

    $flag = $flag==='asc'?'desc':'asc';
    $arr_query = array();
    $arr_query[] = "sst=$col";
    $arr_query[] = "sod=$flag";
    $qstr = implode("&amp;", $arr_query);
    $addClass = $sst===$col&&$sod===$flag?'text-primary':'';

    $str .= "<a href=\"?{$query_string}&amp;{$qstr}\" data-sst=\"{$col}\" data-sod=\"{$flag}\" class=\"sort_link {$addClass}\">▼</a>";//{$_SERVER['SCRIPT_NAME']}
    return $str;
}
//------------------------------------------------------------------------------------------------------------------
function f_search_txt($stx, $sfl){

    // 검색필드를 구분자로 나눈다. 여기서는 ||
    $field = explode('||', trim($sfl));

    // 검색어를 구분자로 나눈다. 여기서는 공백
    $s = explode(' ', strip_tags($stx));
    if( count($s) > 1 ){
        $s = array_slice($s, 0, 2);
        $stx = implode(' ', $s);
    }
    $str = '(';
    $op1 = '';
    for ($i=0; $i<count($s); $i++) {
        if (trim($s[$i]) == '') continue;

        $search_str = $s[$i];

        $str .= $op1;
        $str .= "(";
        $op2 = '';
        // 필드의 수만큼 다중 필드 검색 가능 (필드1+필드2...)
        for ($k=0; $k<count($field); $k++) {
            $str .= $op2;
            $str .= "INSTR({$field[$k]}, '{$search_str}')";
            $op2 = " or ";
        }
        $str .= ")";

        $op1 = " and ";
    }
    $str .= ")";

    return $str;
}
function search_text($stx, $str) {
    // 문자앞에 \ 를 붙입니다.
    $src = array('/', '|');
    $dst = array('\/', '\|');

    if (!trim($stx) && $stx !== '0') return $str;

    // 검색어 전체를 공란으로 나눈다
    $s = explode(' ', $stx);

    // "/(검색1|검색2)/i" 와 같은 패턴을 만듬
    $pattern = '';
    $bar = '';
    for ($m=0; $m<count($s); $m++) {
        if (trim($s[$m]) == '') continue;
        $tmp_str = quotemeta($s[$m]);
        $tmp_str = str_replace($src, $dst, $tmp_str);
        $pattern .= $bar . $tmp_str . "(?![^<]*>)";
        $bar = "|";
    }

    // 지정된 검색 폰트의 색상, 배경색상으로 대체
    $replace = "<span class=\"text-primary\" style=\"width: auto;\">\\1</span>";

    return preg_replace("/($pattern)/i", $replace, $str);
}

function f_text_filter($stx, $sfl=""){
    global $setup_info;

    $subj = "";
    $filter = explode(",", trim($setup_info['st_filter']));
    for ($i=0; $i<count($filter); $i++) {
        $str = $filter[$i];

        // 필터링 (찾으면 중지)
        $subj = "";
        $pos = stripos($stx, $str);
        if ($pos !== false) {
            $subj = $str;
            break;
        }
    }

    if ($subj) {
        return ($sfl?$sfl."에 ":"")."금칙어가 포함되어 있습니다.";
    } else {
        return "";
    }
}
//------------------------------------------------------------------------------------------------------------------
function is_admin($mt_id) { // 관리자인가?
    if (!$mt_id) return;

    if ('admin' === $mt_id) return 'super';
    return '';
}
//------------------------------------------------------------------------------------------------------------------
function is_level($mt_level) { // 회원구분
    if (!$mt_level) return;

    if ($mt_level=='10') return 'super';
    if ($mt_level=='9') return '계정관리자';
    if ($mt_level=='8') return '일반관리자';
    if ($mt_level=='4') return 'seller';
    return '';
}
//------------------------------------------------------------------------------------------------------------------
function arr_searchId($id, $array) {
    foreach ($array as $key => $val) {
        if ($val === $id) {
            return $key;
        }
    }
    return null;
}
//------------------------------------------------------------------------------------------------------------------
function formatTimeUnit($value) {
    // 값이 10 미만인 경우 앞에 0을 붙입니다.
    return str_pad($value, 2, '0', STR_PAD_LEFT);
}
function get_timer($end, $format=""){ // 남은시간
    $result = array();

    if ((strlen($end) > 10 && $end > TIME_YMDHIS) || (strlen($end) == 10 && $end > TIME_YMD)) {
        $endDate = new DateTime($end);
        $now = new DateTime();
        $interval = $now->diff($endDate);

        $now_t = time();
        $endDate_t = strtotime($end);
        $seconds = $endDate_t - $now_t;

        $days = floor($seconds / (3600*24));
        $hoursLeft = floor(($seconds - ($days * 3600 * 24)) / 3600);
        $minutesLeft = floor(($seconds - ($days * 3600 * 24) - ($hoursLeft * 3600)) / 60);
        $remainingSeconds = $seconds - ($days * 3600 * 24) - ($hoursLeft * 3600) - ($minutesLeft * 60);

        $formattedHours = formatTimeUnit($hoursLeft);
        $formattedMinutes = formatTimeUnit($minutesLeft);
        $formattedSeconds = formatTimeUnit($remainingSeconds);

        $timeString = "";

        if ($format == 'timedeal') {
            $timeString = "{$formattedHours}시간 : {$formattedMinutes}분 : {$formattedSeconds}초";
            if ($days > 0) $timeString = "{$days}일 : " . $timeString;

            $result['time'] = $timeString;
            $result['seconds'] = $seconds;
        } else {
            /*$hours = $min = $sec = 0;$diff = "";
            $startDate = strtotime(TIME_YMDHIS);
            $endDate = strtotime( date('Y-m-d H:i:s',strtotime($end)) );
            if ($endDate > $startDate) {
                $diff = $endDate - $startDate;
                $hours = floor($diff/3600);
                $diff = $diff - ($hours*3600);
                $min = floor($diff/60); if (strlen($min)===1) {$min = '0'.$min;}
                $sec = $diff - ($min*60); if (strlen($sec)===1) {$sec = '0'.$sec;}
            }
            $result['timer'] = $hours==0&&$min&&$sec ? $min.':'.$sec : ""; // 시간 카운트
            $result['time'] = $diff ? $diff-5 : 0;*/
        }
    } else {
        $result['time'] = "";
        $result['seconds'] = 0;
    }

    return $result;
}
//------------------------------------------------------------------------------------------------------------------
// 점수를 100% 기준으로 환산
function convertScoreToPercentage($score, $maxScore = 5) {
    $percentage = ($score / $maxScore) * 100;
    return $percentage;
}

function review_icon($val) {
    $str = "";
    $val = $val*1;
    for ($i=1;$i<=$val;$i++) {
        if ($val>=$i) {
            $str .= '<span></span>';
        }
        if ($val-$i===0.5) {
            $str .= '<span class="half"></span>';
        }
    }
    for ($i=1;$i<=(5 - $val);$i++) {
        $str .= '<span class="off"></span>';
    }

    if (!$str) {
        $str .= '<span class="i_0"></span>';
    }

    return '<span class="star">'.$str.'</span>';
}
//------------------------------------------------------------------------------
// 금액 표시
function display_price($price, $lang="") {
    $price = str_replace(' ','',$price);
    $price = str_replace(',','',$price);

    return (int)$price;
}
function floorp($val, $precision) {
    $mult = pow(10, $precision); // Can be cached in lookup table
    return floor($val * $mult) / $mult;
}

function get_pt_title_ot_code($ot_code) {
    global $DB;

    $DB->where('ot_code', $ot_code);
    $DB->where("ct_select", '1');
    $row_ot1 = $DB->getone('cart_t', 'COUNT(*) AS cnt');

    $DB->where('ot_code', $ot_code);
    $DB->where("ct_select", '1');
    $DB->orderBy("pt_idx","desc")->orderBy("idx","desc");
    $row_ot2 = $DB->getone('cart_t', 'pt_title');

    if($row_ot1['cnt']>1) {
        $pt_title_t = $row_ot2['pt_title']." 외 ".($row_ot1['cnt']-1)." 건";
    } else {
        $pt_title_t = $row_ot2['pt_title'];
    }

    return $pt_title_t;
}

function get_mt_point($mt_idx) {
    global $DB;

    $sum_po_point = 0;

    // 포인트합
    /*$row = $DB->fetch_query("select sum(plt_price) as sum_po_point from point_log_t where mt_idx = '{$mt_idx}'
                          AND CASE WHEN plt_edate IS NOT NULL then plt_edate > NOW() ELSE plt_edate IS NULL END");*/
    $DB->where('mt_idx', $mt_idx)->where("CASE WHEN plt_edate IS NOT NULL then plt_edate > NOW() ELSE plt_edate IS NULL END");
    $row = $DB->getone('point_log_t', 'sum(plt_price) as sum_po_point');

    if ((int)$row['sum_po_point'] >= 0) {
        $sum_po_point = $row['sum_po_point'];
    }
    /*
    unset($list);
    $query = "select * from point_log_t where mt_idx = '".$mt_idx."'";
    $list = $DB->select_query($query);
    if($list) {
        foreach($list as $row) {
            if($row['plt_type']=='P') {
                $sum_po_point += $row['plt_price'];
            } else {
                $sum_po_point -= $row['plt_price'];
            }
        }
    }*/

    return $sum_po_point;
}

function use_mt_point($mt_idx, $pt_idx, $ot_code, $ot_pcode, $plt_type, $plt_price, $plt_memo) {
    global $DB;

    $plt_edate_t = date("Y-m-d H:i:s",strtotime("+12 month", time()));

    unset($arr_query);
    $arr_query = array(
        "mt_idx" => $mt_idx,
        "pt_idx" => $pt_idx,
        "ot_code" => $ot_code,
        "ot_pcode" => $ot_pcode,
        "plt_type" => $plt_type,
        "plt_price" => $plt_price,
        "plt_memo" => $plt_memo,
        "plt_wdate" => $DB->now(),
        "plt_edate" => $plt_edate_t,
    );

    $_last_idx = $DB->insert('point_log_t', $arr_query);

    return $_last_idx;
}
function insert_point($mt_idx, $point, $class='4', $memo, $act_tbl, $act_id) {
    global $DB;

    if ($mt_idx) {
        if ((int)$point >= 0) { $plt_type = 'P'; } else { $plt_type = 'M'; }

        $plt_edate_t = date("Y-m-d H:i:s",strtotime("+12 month", time()));

        //$chk = $DB->fetch_query("select COUNT(*) AS cnt from point_log_t where mt_idx = '{$mt_idx}' AND rel_table = '{$act_tbl}' AND rel_item = '{$act_id}' AND plt_type = '{$plt_type}' AND plt_memo = '{$memo}' ");
        $DB->where('mt_idx', $mt_idx)->where('rel_table', $act_tbl)->where('rel_item', $act_id)->where('plt_type', $plt_type)->where('plt_memo', $memo);
        $chk = $DB->getone('point_log_t', 'COUNT(*) AS cnt');

        if (!$chk['cnt']) {
            /*$row = $DB->fetch_query("select sum(plt_price) as sum_po_point from point_log_t where mt_idx = '{$mt_idx}'
                                AND CASE WHEN plt_edate IS NOT NULL then plt_edate > NOW() ELSE plt_edate IS NULL END");*/
            $DB->where('mt_idx', $mt_idx)->where("CASE WHEN plt_edate IS NOT NULL then plt_edate > NOW() ELSE plt_edate IS NULL END");
            $row = $DB->getone('point_log_t', 'sum(plt_price) as sum_po_point');

            $sum_po_point = 0;
            if ((int)$row['sum_po_point'] >= 0) {
                $sum_po_point = $row['sum_po_point'];
            }

            unset($arr_query);
            $arr_query = array(
                'mt_idx' => $mt_idx,
                'plt_price' => $point,
                'mt_price' => $sum_po_point,
                'plt_memo' => $memo,
                'plt_class' => $class,
                'plt_type' => $plt_type,
                'rel_table' => $act_tbl,
                'rel_item' => $act_id,
                'plt_wdate' => $DB->now(),
                "plt_edate" => $plt_edate_t,
            );
            $DB->insert('point_log_t', $arr_query);

            // 포인트합
            /*$row = $DB->fetch_query("select sum(plt_price) as sum_po_point from point_log_t where mt_idx = '{$mt_idx}'
                              AND CASE WHEN plt_edate IS NOT NULL then plt_edate > NOW() ELSE plt_edate IS NULL END");*/
            $DB->where('mt_idx', $mt_idx)->where("CASE WHEN plt_edate IS NOT NULL then plt_edate > NOW() ELSE plt_edate IS NULL END");
            $row = $DB->getone('point_log_t', 'sum(plt_price) as sum_po_point');
            $sum_po_point = 0;
            if ((int)$row['sum_po_point'] >= 0) {
                $sum_po_point = $row['sum_po_point'];
            }
            unset($arr_query);
            $arr_query = array(
                "mt_point" => $sum_po_point,
            );
            $DB->where('idx', $mt_idx);
            $DB->update('member_t', $arr_query);
        }
    }
}

//------------------------------------------------------------------------------------------------------------------
function calc_point($type, $total=0){
    global $setup_info;
    if ($type==='order_confirm') {
        $st_point_arr = explode('|', $setup_info['st_point_od_confirm']);
    } else if ($type==='order') {
        $st_point_arr = explode('|', $setup_info['st_point_od']);
    } else if ($type==='text_review') {
        $st_point_arr = explode('|', $setup_info['st_point_review1']);
    } else if ($type==='photo_review') {
        $st_point_arr = explode('|', $setup_info['st_point_review2']);
    } else if ($type==='join') {
        $st_point_arr = explode('|', $setup_info['st_point_join']);
    }
    if ($total) {
        if ($st_point_arr[1]==='1') {
            $point = round($total*($st_point_arr[0]/100));
        } else {
            $point = $st_point_arr[0]*1;
        }
    } else {
        $point = $st_point_arr[0];
        if ($st_point_arr[1]==='1') {
            $point .= '%';
        } else {
            $point .= 'P';
        }
    }

    return $point;
}

//------------------------------------------------------------------------------------------------------------------
//function insert_visit($mt_id='', $temp_mt_id='', $vi_os='') {
//    global $DB;
//    $temp_id = $mt_id ? $mt_id : $temp_mt_id;
//    //$vi_os = $vi_os ? $vi_os : 'android';
//
//    //$visit = $DB->fetch_query("SELECT COUNT(*) AS cnt FROM visit_t WHERE vi_temp_id='{$temp_id}' AND vi_date='".TIME_YMD."' ");
//    $DB->where('vi_temp_id', $temp_id)->where('vi_date', TIME_YMD);
//    $visit = $DB->getone('visit_t', 'COUNT(*) AS cnt');
//    if (!$visit['cnt']) {
//        //$tmp_row = $DB->fetch_query("SELECT MAX(vi_id) AS max_vi_id FROM visit_t ");
//        $tmp_row = $DB->getone('visit_t', 'MAX(vi_id) AS max_vi_id');
//        $vi_id = $tmp_row['max_vi_id'] + 1;
//        $remote_addr = escape_trim($_SERVER['REMOTE_ADDR']);
//        $referer = "";
//        if (isset($_SERVER['HTTP_REFERER']))
//            $referer = escape_trim(clean_xss_tags(strip_tags($_SERVER['HTTP_REFERER'])));
//        $user_agent  = escape_trim(clean_xss_tags(strip_tags($_SERVER['HTTP_USER_AGENT'])));
//
//        $DB->db_query("insert visit_t ( vi_id, vi_mt_idx, vi_temp_id, vi_ip, vi_date, vi_time, vi_referer, vi_agent, vi_os ) values ( '{$vi_id}', '{$mt_id}', '{$temp_id}', '{$remote_addr}', '".TIME_YMD."', '".date('H:i:s')."', '{$referer}', '{$user_agent}', '{$vi_os}' ) ");
//    }
//}
//------------------------------------------------------------------------------------------------------------------
// 장바구니 상품삭제
function cart_item_clean() {
    global $DB;

    // 장바구니 보관일
    $de_cart_keep_term = 20;

    // ct_select_time이 기준시간 이상 경과된 경우 변경
    $cart_stock_limit = 3;

    $stocktime = 0;
    if($cart_stock_limit > 0) {
        if($cart_stock_limit > $de_cart_keep_term * 24)
            $cart_stock_limit = $de_cart_keep_term * 24;

        $stocktime = SERVER_TIME - (3600 * $cart_stock_limit);

        unset($arr_query);
        $arr_query = array(
            'ct_select' => '0',
        );
        $DB->where('ct_select', '1')->where('ct_status', '0')->where("UNIX_TIMESTAMP(ct_select_time) < '$stocktime'");
        $DB->update("cart_t", $arr_query);
    }

    // 설정 시간이상 경과된 상품 삭제
    $statustime = SERVER_TIME - (86400 * $de_cart_keep_term);

    $DB->where('ct_status', '0')->where("UNIX_TIMESTAMP(ct_wdate) < '$statustime'");
    $DB->delete('cart_t');
}

// cart id 설정
function set_cart_id($direct=0) {
    global $DB;
    $de_guest_cart_use = false;
    // 장바구니 보관일
    $de_cart_keep_term = 20;

    if ($direct) {
        $tmp_cart_id = get_session('ss_cart_direct');
        if(!$tmp_cart_id) {
            $tmp_cart_id = get_ot_code();
            set_session('ss_cart_direct', $tmp_cart_id);
        }
    } else {
        // 비회원장바구니 cart id 쿠키설정
        if($de_guest_cart_use) {
            $tmp_cart_id = preg_replace('/[^a-z0-9_\-]/i', '', get_cookie('ck_guest_cart_id'));
            if($tmp_cart_id) {
                set_session('ss_cart_id', $tmp_cart_id);
            } else {
                $tmp_cart_id = get_ot_code();
                set_session('ss_cart_id', $tmp_cart_id);
                set_cookie('ck_guest_cart_id', $tmp_cart_id, ($de_cart_keep_term * 86400));
            }
        } else {
            $tmp_cart_id = get_session('ss_cart_id');
            if(!$tmp_cart_id) {
                $tmp_cart_id = get_ot_code();
                set_session('ss_cart_id', $tmp_cart_id);
            }
        }

        // 보관된 회원장바구니 자료 cart id 변경
        if($_SESSION['user']['mt_idx'] && $tmp_cart_id) {
            unset($arr_query);
            $arr_query = array(
                'ot_code' => $tmp_cart_id,
            );
            $DB->where('mt_idx', $_SESSION['user']['mt_idx'])->where('ct_status', '0')->where('buy_now', 'N');
            $DB->update('cart_t', $arr_query);
        }
    }
}

// 상품의 재고 (창고재고수량 - 주문대기수량)
function get_pt_stock_qty($pt_idx, $ct_opt_name="", $ct_opt_value="") {
    global $DB;

    return 1000;
}

// 장바구니 건수 검사
function get_cart_count($_uid)
{
    global $DB;

    $DB->where('ot_code', $_uid)->where('ct_status', '0')->where('ct_select', '1');
    $row = $DB->getone('cart_t', 'count(*) as cnt');
    $cnt_t = (int)$row['cnt'];
    return $cnt_t;
}

function get_ct_cnt($_uid="") {
    global $DB;

    $DB->where('ct_status', '0')->where('buy_now', 'N');
    if ($_uid) {
        $DB->where('ot_code', $_uid);
    } else {
        $DB->where('mt_idx', $_SESSION['user']['mt_idx']);
    }
    $row_ptc = $DB->getone('cart_t', 'count(*) as cnt');

    if($row_ptc['cnt']>99) {
        $cnt_t = '99+';
    } else {
        $cnt_t = $row_ptc['cnt'];
    }

    return $cnt_t;
}


/*** 아래 쿠폰 관련 내용 사용 안함 ***/
function insert_coupon_old($cz_id, $mt_idx, $mt_id) {
    global $DB;
    $_last_idx = "";
    $msg = "";

    if ($mt_idx) {
        $cz_id = preg_replace('#[^0-9]#', '', $cz_id);
        $DB->where('cz_id', $cz_id);
        $DB->where('cz_show', 'Y');
        $DB->where("(cz_start <= '".date('Y-m-d')."' AND cz_end >= '".date('Y-m-d')."')");
        $cp = $DB->getone('coupon_zone_t');
        if ($cp['cz_id']) {
            if ($cp['cz_start'] <= date('Y-m-d') && $cp['cz_end'] >= date('Y-m-d')) {
                // 발급여부
                if (is_coupon_downloaded($mt_idx, $cp['cz_id'])) {
                    $msg = "이미 다운로드하신 쿠폰입니다.";
                }
                if ($cp['cz_qty'] * 1 <= 0) {
                    $msg = "수량 소진된 쿠폰입니다.";
                }

                if (!$msg) {
                    // 쿠폰발급
                    do {
                        $cp_id = get_coupon_id();
                        $DB->where('cp_id', $cp_id);
                        $row3 = $DB->getone('coupon_t', 'count(*) as cnt');
                        if(!$row3['cnt'])
                            break;
                    } while(1);

                    $cp = array_map('addslashes', $cp);
                    $cp_start = date('Y-m-d');
                    $period = $cp['cz_period'] - 1;
                    if($period < 0)
                        $period = 0;
                    $cp_end = date('Y-m-d', strtotime("+{$period} days", time()));

                    unset($arr_query);
                    $arr_query = array(
                        'cp_id' => $cp_id,
                        'cp_subject' => $cp['cz_subject'],
                        'cp_method' => $cp['cp_method'],
                        'cp_target' => $cp['cp_target'],
                        'mt_idx' => $mt_idx,
                        'mt_id' => $mt_id,
                        'cz_id' => $cz_id,
                        'cp_start' => $cp_start,
                        'cp_end' => $cp_end,
                        'cp_type' => $cp['cp_type'],
                        'cp_price' => $cp['cp_price'],
                        'cp_trunc' => $cp['cp_trunc'],
                        'cp_minimum' => $cp['cp_minimum'],
                        'cp_maximum' => $cp['cp_maximum'],
                        'cp_datetime' => $DB->now(),
                        'st_support_chk' => $cp['st_support_chk'],
                        'st_support_price' => $cp['st_support_price'],
                    );
                    $_last_idx = $DB->insert('coupon_t', $arr_query);
                    if ($_last_idx) {
                        $DB->where("cz_id", $cz_id);
                        $cpt = $DB->getone("coupon_zone_t");

                        // 다운로드 증가
                        unset($arr_query);
                        $arr_query = array(
                            'cz_qty' => ((int)$cpt['cz_qty'] > 0 ? (int)$cpt['cz_qty']-1 : 0),
                            'cz_download' => ((int)$cpt['cz_download']+1),
                        );
                        $DB->where("cz_id", $cz_id);
                        $DB->update('coupon_zone_t', $arr_query);
                    }
                }
            } else {
                $msg = "등록기간이 지난 쿠폰입니다.";
            }
        } else {
            $msg = "쿠폰정보가 존재하지 않습니다.";
        }
    }
    return $msg;
}

function ot_use_discount_old($ot_code_t, $row_ot, $ot_status_t=""){
    global $DB, $socket_client;

    $DB->where("a1.idx", $row_ot['st_idx']);
    $row_st = $DB->getone("store_t a1", "*, a1.idx AS st_idx");

    //포인트 사용시 포인트 차감
    if ($row_ot['ot_use_point']) {
        $DB->where("rel_table", 'order');
        $DB->where("rel_item", $ot_code_t);
        $chk = $DB->getone("point_log_t", "COUNT(*) AS cnt");
        if (!$chk['cnt']) {
            insert_point($row_ot['mt_idx'], -$row_ot['ot_use_point'], '1', '결제시 포인트 사용으로 차감', 'order', $ot_code_t);
        }
    }
    //쿠폰 사용시 기록
    if ($row_ot['ot_use_coupon'] && $row_ot['ot_cp_id']) {
        $DB->where("cp_id", $row_ot['ot_cp_id']);
        $DB->where("ot_code", $ot_code_t);
        $chk = $DB->getone("coupon_log_t", "COUNT(*) AS cnt");
        if (!$chk['cnt']) {
            $DB->where("cp_id", $row_ot['ot_cp_id']);
            $DB->orderBy("cp_no", "DESC");
            $coupon = $DB->getone("coupon_t");

            unset($arr_query);
            $arr_query = array(
                'cp_id' => $row_ot['ot_cp_id'],
                'cp_subject' => $coupon['cp_subject'],
                'cp_method' => $coupon['cp_method'],
                'cp_price' => $row_ot['ot_use_coupon'],
                'st_idx' => $row_ot['st_idx'],
                'mt_idx' => $row_ot['mt_idx'],
                'mt_id' => $row_ot['mt_id'],
                'ot_code' => $ot_code_t,
                'cl_datetime' => $DB->now(),
            );
            $DB->insert('coupon_log_t', $arr_query);

            $st_support_price = 0;
            if ($coupon['st_support_chk'] == 'Y') {
                if ((int)$row_ot['ot_price'] - (int)$coupon['st_support_price'] > 0) {
                    $st_support_price = $coupon['st_support_price'];
                }
            } else {
                $c_st_support_price = floor($row_ot['ot_price']*($coupon['st_support_price']/100));
                if ($c_st_support_price > 0) {
                    $st_support_price = $c_st_support_price;
                }
            }
            unset($arr_query);
            $arr_query = array(
                "ot_coupon_support" => $st_support_price,
                "ot_delivery_support" => $row_st['st_delivery_support_price'],
            );
            $DB->where('ot_code', $ot_code_t);
            $DB->update('order_t', $arr_query);
        }
    }

    /*if ($row_ot['ot_rhp']) { // 주문정보 카카오알림톡 혹은 문자전송
        if (!$row_ot['mt_idx']) { // 비회원일때 문자
            $sms_msg = "[".APP_AUTHOR."] 주문완료\r\n"."■ 주문번호: ".$ot_code_t.". 자세한 내용은 주문 상세내역에서 확인바랍니다.";
            f_sms_send($row_ot['ot_rhp'], $sms_msg);
        }
    }*/

    if ($ot_status_t == '2') {
        if ($row_st['mt_idx']) {
            $mt_staff = array();
            $mt_staff[] = $row_st['mt_idx'];
            $DB->where('a1.mt_status', 'Y');
            $DB->where('a1.mt_level', '3');
            $DB->where('a1.st_idx', $row_ot['st_idx']);
            $list_mt = $DB->get("member_t a1", "idx as mt_idx");
            foreach ($list_mt as $row_mt) {
                $mt_staff[] = $row_mt['mt_idx'];
            }

            proc_noti("push", implode(',', $mt_staff), 'orderFin_seller', $ot_code_t, array('seller_idx' => $row_ot['st_idx']));
            /*$kalim_token_t = f_kalim_token();
            if ($kalim_token_t->token) {
                f_kalim_send($kalim_token_t->token, 'orderFin', $ot_code_t);
            }*/
        }

        // socket 전송
        if ($socket_client) {
            $arr = ['ot_code' => $row_ot['ot_code'], 'send_mt_idx' => $row_ot['mt_idx'], 'mt_idx' => $row_ot['mt_idx'], 'st_idx' => $row_ot['st_idx']];
            $socket_client->emit('update_order', $arr);
        }
    }
}

// 쿠폰번호 생성함수
function get_coupon_id_old() {
    $len = 16;
    $chars = "ABCDEFGHJKLMNPQRSTUVWXYZ123456789";

    srand((double)microtime()*1000000);

    $i = 0;
    $str = '';

    while ($i < $len) {
        $num = rand() % strlen($chars);
        $tmp = substr($chars, $num, 1);
        $str .= $tmp;
        $i++;
    }

    $str = preg_replace("/([0-9A-Z]{4})([0-9A-Z]{4})([0-9A-Z]{4})([0-9A-Z]{4})/", "\\1-\\2-\\3-\\4", $str);

    return $str;
}
//------------------------------------------------------------------------------
// 쿠폰 사용체크
function is_used_coupon_old($mt_idx, $cp_id) {
    global $DB;

    $used = false;

    $DB->where('mt_idx', $mt_idx)->where('cp_id', $cp_id);
    $row = $DB->getone('coupon_log_t', 'count(*) as cnt');

    if($row['cnt'])
        $used = true;

    return $used;
}
//------------------------------------------------------------------------------
// 다운로드한 쿠폰인지
function is_coupon_downloaded_old($mt_idx, $cz_id) {
    global $DB;

    if(!$mt_idx)
        return false;

    $DB->where('mt_idx', $mt_idx)->where('cz_id', $cz_id);
    $row = $DB->getone('coupon_t', 'count(*) as cnt');

    return ($row['cnt'] > 0);
}
//------------------------------------------------------------------------------------------------------------------
function get_coupon_t_info_old($cp_method='1', $cp_target){
    global $DB, $member;

    $couponItems = array();
    $download_cnt = 0;

    $DB->where("cp_method", $cp_method);
    $DB->where("cp_target", $cp_target);
    $DB->where("(cz_start <= '".date('Y-m-d')."' AND cz_end >= '".date('Y-m-d')."')");
    $DB->where('cz_show', 'Y');
    $DB->orderBy("cp_price", "asc");
    $DB->orderBy("cz_id", "desc");
    $addQuery = ", (CASE WHEN cz_end = '9999-12-31' THEN 1 ELSE (NOW() between date_format(cz_start, '%Y-%m-%d %H:%i:%s') and DATE_ADD(date_format(cz_end, '%Y-%m-%d %H:%i:%s'), INTERVAL +1 DAY)) END) AS diff";
    $list_cpt = $DB->get("coupon_zone_t", null, "*".$addQuery);
    foreach ($list_cpt as $row_cpt) {
        if($row_cpt['cp_type']) {
            $cp_price_t = $row_cpt['cp_price'].'%';
        } else {
            $cp_price_t = number_format($row_cpt['cp_price']).'원';
        }

        $download = '';
        $disabled = '';
        $addClass = '';

        if ($row_cpt['cz_start'] || $row_cpt['cz_end']) {
            $addClass = $row_cpt['diff'] ? " active" : " expire";
        } else {
            $addClass = " expire";
            $row_cpt['diff'] = "";
        }
        //$disabled = $row_cpt['diff'] ? '' : 'disabled';

        // 다운로드 쿠폰인지
        if (is_coupon_downloaded($member['mt_idx'], $row_cpt['cz_id'])) {
            $addClass = " cmplt";
            $disabled = 'disabled';
            $download = 'Y';
            $download_cnt++;
        }

        $couponItems[] = array(
            'cz_id' => $row_cpt['cz_id'],
            'cp_subject' => $row_cpt['cz_subject'],
            'cp_start' => $row_cpt['cz_start'],
            'cp_end' => $row_cpt['cz_end'],
            'cp_period' => $row_cpt['cz_period'],
            'cp_trunc' => $row_cpt['cp_trunc'],
            'cp_minimum' => $row_cpt['cp_minimum'],
            'cp_maximum' => $row_cpt['cp_maximum'],
            'cp_qty' => $row_cpt['cz_qty'],
            'cp_price' => $row_cpt['cp_price'],
            'cp_price_t' => $cp_price_t,
            'download' => $download,
            'disabled' => $disabled,
            'addClass' => $addClass,
        );
    }

    return array(
        'coupon_list' => $couponItems,
        'coupon_cnt' => count($couponItems),
        'download_all' => count($couponItems) === $download_cnt ? 'Y' : 'N',
    );
}
function get_coupon_list_old($row, $row_st = array()){
    $shtml = "";
    $shtml .= '<li class="col">';
    $shtml .=   '<button type="button" class="coupon_item'.$row['addClass'].'" '.$row['disabled'].''.($row['disabled']?'':' onclick="f_coupon_download(this,\''.$row['cz_id'].'\')"').'>';
    if ($row_st) {
        $seller_name = $row_st['st_name'];
        $seller_link = './shop_detail.php?idx='.$row_st['st_idx'];
        $shtml .= '<a class="btn btn-primary btn-sm rounded-pill mb-2" href="'.$seller_link.'">';
        $shtml .= '<div class="line1_text fw_700">'.$seller_name.'</div>';
        $shtml .= '<img class="ml-3" src="'.DESIGN_HTTP.'/img/shop_arrow_w.png" style="width:0.5rem;">';
        $shtml .= '</a>';
    }
    $shtml .=     '<div class="media w-100 align-items-center">';
    $shtml .=       '<div class="flex-fill">';
    $shtml .=         '<p class="name line2_text">'.$row['cp_subject'].'</p>';
    $shtml .=         '<p class="price line1_text">'.$row['cp_price_t'].' 할인</p>';
    $shtml .=         '<p class="info01 line1_text">'.($row['cp_end'] ? '~'.DateType($row['cp_end'], 2).'까지 사용 가능' : '(만료기한없음)').'</p>';
    $shtml .=         '<p class="info02 line1_text">최소 주문금액 : '.($row['cp_minimum'] ? number_format($row['cp_minimum']).'원' : '(없음)').'</p>';
    $shtml .=       '</div>';
    $shtml .=       '<div class="btn_wr">';
    $shtml .=         '<div class="coup_down">';
    $shtml .=           '<div class="ico"></div>';
    $shtml .=         '</div>';
    $shtml .=       '</div>';
    $shtml .=     '</div>';
    $shtml .=   '</button>';
    $shtml .= '</li>';

    return $shtml;
}
//------------------------------------------------------------------------------------------------------------------
function delivery_finish(){ // 배송완료 처리
    global $DB, $setup_info;

    if ($setup_info['st_sweettrack_key']) {
        $DB->where('ct_status', '8');
        $DB->where("ct_delivery_code IS NOT NULL");
        $DB->where("ct_delivery_number IS NOT NULL");
        $list = $DB->get("cart_t");
        foreach ($list as $row) {
            $url = "http://info.sweettracker.co.kr/api/v1/trackingInfo?t_key=".$setup_info['st_sweettrack_key']."&t_code=".$row['ct_delivery_code']."&t_invoice=".$row['ct_delivery_number'];

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => trim($url),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json",
                )
            ));
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            $jsonArray = json_decode($response, true);

            if ($jsonArray['complete']===true) {
                unset($arr_query);
                $arr_query = array(
                    'ct_status' => '9',
                    'ct_dedate' => $DB->now(),
                );
                $DB->where('idx',$row['idx']);
                $DB->update('cart_t', $arr_query);

                proc_noti("push", $row['mt_idx'], 'deliverFin', $row['ot_code'], array('ot_code'=> $row['ot_code']));
            }
        }
    }
}
//------------------------------------------------------------------------------------------------------------------
function purchase_confirm($row, $status='') { // 구매확정 처리
    global $DB, $setup_info, $arr_pay_type;

    if ($row['ot_code']) {
        if ($row['ot_type'] == 'C') {
            unset($arr_query);
            $arr_query = array(
                "ct_status" => '10',
                "ct_status_prev" => '',
                "ct_rdate" => $DB->now(),
            );
            $DB->where('ot_code', $row['ot_code']);
            $DB->update('cart_t', $arr_query);

            unset($arr_query);
            $arr_query = array(
                "ot_status" => '10',
                "ot_status_prev" => '',
                "ot_rdate" => $DB->now(),
            );
            $DB->where('ot_code', $row['ot_code']);
            $DB->update('order_t', $arr_query);
        }

        if ($status!='return') {
            $ot_price = (int)$row['ot_price'] - (int)$row['ot_refund_price'];
            if ($ot_price > 0) {
                if ($row['ot_type'] == 'C') {
                    $DB->where('idx', $row['mt_idx'])->where("mt_level > '1'");
                    $row_mt = $DB->getone("member_t", "idx as mt_idx, mt_id");
                    if ($row_mt['mt_idx']) {
                        $plt_price = calc_point('order_confirm', $ot_price);
                        $plt_memo = "구매확정 - ".$row['ot_code'];
                        $plt_class = '2';

                        insert_point($row_mt['mt_idx'], $plt_price, $plt_class, $plt_memo, 'orderConfirm', $row['ot_code']);
                    }
                }

                // 정산입력

                $DB->where('idx', $row['st_idx']);
                $row_st = $DB->getone("store_t");

                $cat_tdate = f_get_weekday();//date('Y-m-d', strtotime(TIME_YMD.' +1 day'));

                $commission_card = floor($ot_price*($setup_info['st_commission_card']/100)); //결제수수료
                //$commission_card = 0;
                /*switch ($row['ot_pay_type']) {
                    case '카드':
                    case 'card':
                    case 'CARD':
                        $commission_card = floor($ot_price*($setup_info['st_commission_card']/100));
                        break;
                    case '카카오페이':
                    case 'kakaopay':
                    case 'KAKAOPAY':
                        $commission_card = floor($ot_price*($setup_info['st_commission_kakaopay']/100));
                        break;
                    case '네이버페이':
                    case 'naverpay':
                    case 'NAVERPAY':
                        $commission_card = floor($ot_price*($setup_info['st_commission_naverpay']/100));
                        break;
                }*/

                $commission = floor($ot_price*($setup_info['st_commission']/100)); //서비스수수료
                $cat_charge = $commission_card + $commission;

                $cat_pay_price_sum = $ot_price;
                $delivery_price_sum = $row['ot_delivery_charge']+$row['ot_delivery_charge_extra'];
                $cat_delivery_support = (int)$delivery_price_sum - (int)$row['ot_delivery_support'];

                $cat_price = $ot_price - $cat_charge - $cat_delivery_support - (int)$row['ot_coupon_support'];
                $cat_price_tax = floor($cat_price*0.1);

                unset($arr_query);
                $arr_query = array(
                    "seller_mt_idx" => $row_st['mt_idx'],
                    "st_idx" => $row['st_idx'],
                    //"st_name" => $row_st['st_name'],
                    //"st_company_name" => $row_st['st_company_name'],
                    //"st_company_num1" => $row_st['st_company_num1'],
                    //"st_service_commission" => $row_st['st_service_commission'],
                    //"cat_account" => $row_st['st_bank'].'|'.$row_st['st_bank_account'].'|'.$row_st['st_bank_name'],
                    "ot_code" => $row['ot_code'],
                    "ot_pay_type" => $row['ot_pg_method'] ? $row['ot_pg_method'] : $arr_pay_type[$row['ot_pay_type']],//$row['ot_pay_type'],
                    "cat_tdate" => $cat_tdate,
                    //"cat_ydate" => $_POST['cat_ydate'],
                    //"cat_sdate" => $_POST['cat_sdate'],
                    //"cat_edate" => $_POST['cat_edate'],
                    "cat_price" => $cat_price,
                    "cat_price_tax" => $cat_price_tax,
                    //"cat_pay_price1" => $_POST['cat_pay_price1'],
                    //"cat_pay_price2" => $_POST['cat_pay_price2'],
                    //"cat_pay_price3" => $_POST['cat_pay_price3'],
                    //"cat_pay_price4" => $_POST['cat_pay_price4'],
                    //"cat_pay_price5" => $_POST['cat_pay_price5'],
                    //"cat_pay_price6" => $_POST['cat_pay_price6'],
                    //"cat_pay_price7" => $_POST['cat_pay_price7'],
                    //"cat_pay_price8" => $_POST['cat_pay_price8'],
                    //"cat_pay_price9" => $_POST['cat_pay_price9'],
                    //"cat_pay_price10" => $_POST['cat_pay_price10'],
                    "cat_pay_price_sum" => $cat_pay_price_sum,

                    "cat_charge_pay" => $commission_card,
                    "cat_charge_service" => $commission,
                    "cat_charge_sum" => $cat_charge,

                    "cat_delivery_price" => $delivery_price_sum,
                    "cat_delivery_support" => $cat_delivery_support,
                    "cat_delivery_support_mng" => $row['ot_delivery_support'],

                    "cat_coupon" => $row['ot_use_coupon'],
                    "cat_coupon_support" => $row['ot_coupon_support'],
                    "cat_coupon_support_mng" => (int)$row['ot_use_coupon'] - (int)$row['ot_coupon_support'],
                    "cat_point" => $row['ot_use_point'],

                    //"cat_refund" => $_POST['cat_refund'],
                    //"cat_defer" => $_POST['cat_defer'],
                    //"cat_memo" => $_POST['cat_memo'],
                    "cat_wdate" => $DB->now(),
                );

                $DB->where('ot_code', $row['ot_code']);
                $row_calct = $DB->getone("calculate_t");
                if ($row_calct['idx']) {
                    $DB->where('idx',$row_calct['idx']);
                    $DB->update('calculate_t', $arr_query);
                } else {
                    $DB->insert('calculate_t', $arr_query);
                }

                //------------------------------------------------------------------------------------------------------
                // 지급대행 테이블에 당일 정산내역 합계 저장
                $addColumn = ", SUM(cat_price) AS sum_cat_price";
                $addColumn .= ", SUM(cat_pay_price_sum) AS sum_cat_pay_price_sum";
                $addColumn .= ", SUM(cat_charge_pay) AS sum_cat_charge_pay";
                $addColumn .= ", SUM(cat_charge_service) AS sum_cat_charge_service";
                $addColumn .= ", SUM(cat_charge_sum) AS sum_cat_charge_sum";
                $addColumn .= ", SUM(cat_delivery_price) AS sum_cat_delivery_price";
                $addColumn .= ", SUM(cat_delivery_support) AS sum_cat_delivery_support";
                $addColumn .= ", SUM(cat_delivery_support_mng) AS sum_cat_delivery_support_mng";
                $addColumn .= ", SUM(cat_coupon) AS sum_cat_coupon";
                $addColumn .= ", SUM(cat_coupon_support) AS sum_cat_coupon_support";
                $addColumn .= ", SUM(cat_coupon_support_mng) AS sum_cat_coupon_support_mng";
                $addColumn .= ", SUM(cat_point) AS cat_point";
                $DB->where("LEFT(cat_wdate, 10)", TIME_YMD);
                $DB->where("cat_status", 'N');
                $DB->where("st_idx", $row['st_idx']);
                //$DB->groupBy("st_idx");
                $list_cpt = $DB->get("calculate_t a1", null, "*".$addColumn);
                foreach ($list_cpt as $row_cpt) {
                    //$cat_tdate = date("Y-m-d", strtotime('+1 day'));

                    unset($arr_query);
                    $arr_query = array(
                        "seller_mt_idx" => $row_st['mt_idx'],
                        "st_idx" => $row_cpt['st_idx'],
                        "st_name" => $row_st['st_name'],
                        "st_company_name" => $row_st['st_company_name'],
                        "st_company_num1" => $row_st['st_company_num1'],
                        "payoutStoreId" => $row_st['payoutStoreId'],
                        "st_service_commission" => $row_st['st_service_commission'],
                        "cat_account" => $row_st['st_bank'].'|'.$row_st['st_bank_account'].'|'.$row_st['st_bank_name'],
                        "cat_tdate" => $cat_tdate,
                        //"cat_ydate" => $_POST['cat_ydate'],
                        //"cat_sdate" => $_POST['cat_sdate'],
                        //"cat_edate" => $_POST['cat_edate'],
                        "cat_price" => $row_cpt['sum_cat_price'],
                        "cat_pay_price_sum" => $row_cpt['sum_cat_pay_price_sum'],

                        "cat_charge_pay" => $row_cpt['sum_cat_charge_pay'],
                        "cat_charge_service" => $row_cpt['sum_cat_charge_service'],
                        "cat_charge_sum" => $row_cpt['sum_cat_charge_sum'],

                        "cat_delivery_price" => $row_cpt['sum_cat_delivery_price'],
                        "cat_delivery_support" => $row_cpt['sum_cat_delivery_support'],
                        "cat_delivery_support_mng" => $row_cpt['sum_cat_delivery_support_mng'],

                        "cat_coupon" => $row_cpt['sum_cat_coupon'],
                        "cat_coupon_support" => $row_cpt['sum_cat_coupon_support'],
                        "cat_coupon_support_mng" => $row_cpt['sum_cat_coupon_support_mng'],

                        "cat_point" => $row_cpt['sum_cat_point'],
                    );

                    $DB->where("st_idx", $row_cpt['st_idx']);
                    $DB->where("LEFT(cat_wdate, 10)", substr($row_cpt['cat_wdate'], 0, 10));
                    $payout_t = $DB->getone("calculate_payout_t a1");
                    if ($payout_t['idx']) {
                        if (!$payout_t['cat_status']) {
                            $DB->where("idx", $payout_t['idx']);
                            $DB->update("calculate_payout_t", $arr_query);
                        }
                    } else {
                        $cpt_code = get_cpt_code();
                        $arr_query['cpt_code'] = $cpt_code;
                        $arr_query['cat_wdate'] = $row_cpt['cat_wdate'];
                        $_last_idx = $DB->insert("calculate_payout_t", $arr_query);
                        if ($_last_idx) {
                            unset($arr_query);
                            $arr_query = array(
                                'cpt_code' => $cpt_code,
                            );
                            $DB->where("st_idx", $row_cpt['st_idx']);
                            $DB->where("LEFT(cat_wdate, 10)", substr($row_cpt['cat_wdate'], 0, 10));
                            $DB->where("cat_status", 'N');
                            $DB->update("calculate_t", $arr_query);
                        }
                    }
                }
                //------------------------------------------------------------------------------------------------------
            }
        }
    }
}

function get_ct_delivery_price($st_idx, $ct_type, $ct_price, $ct_opt_qty, $free=false) {
    global $DB;

    $DB->where('idx', $st_idx);
    $row_st = $DB->getone('store_t');

    $DB->where('st_idx', $st_idx);
    $row_pt = $DB->getone('product_t');

    $ct_delivery_default_price = 0;
    $ct_delivery_price_add = 0;
    $ct_delivery_qty = 0;
    $ct_delivery_price = 0;
    //$ct_opt_qty = array_sum($ct_opt_qty);
    $ct_opt_qty = 1;
    $ct_type = $ct_type ? $ct_type : $row_st['st_type'];

    if ($ct_type == 'A') {
        /*$ct_delivery_chk = false;
        $ct_delivery_default_price = 5000;*/

        // 배대사 api
        $d_rtn = get_delivery_price($row_st, $ct_price*1);
        $ct_delivery_chk = $d_rtn['ct_delivery_chk'];
        $ct_delivery_default_price = $d_rtn['st_send_cost_total'];

        $ct_delivery_price_add = 0;
        $ct_delivery_qty = 1;
        $ct_delivery_price = $ct_delivery_default_price;

        if ((int)$row_st['st_delivery_support_price'] > 0) { // 매장별 본사 부담액 차감
            $ct_delivery_price -= (int)$row_st['st_delivery_support_price'];
        }

        // 정육점 부담액 차감
        $DB->where('st_idx', $st_idx);
        $DB->where("sadt_if_price <= '".$ct_price."'");
        $DB->orderBy('sadt_if_price', 'DESC');
        $row_dpt = $DB->getone("store_delivery_info_t");
        if ($row_dpt['sadt_price_set']) {
            $ct_delivery_price -= (int)$row_dpt['sadt_price_set'];
        } else {
            $ct_delivery_price -= (int)$row_st['sadt_price'];
        }
    } else {
        $ct_delivery_chk = true;
        if ($row_pt['pcdt_price_chk']=='Y') {
            if($row_pt['pcdt_if_price']<=$ct_price) {
                $ct_delivery_default_price = 0;
                $ct_delivery_price_add = 0;
                $ct_delivery_qty = 1;
                $ct_delivery_price = 0;
            } else {
                $ct_delivery_default_price = $row_pt['pcdt_price'];
                $ct_delivery_price_add = 0;
                $ct_delivery_qty = 1;
                $ct_delivery_price = $row_pt['pcdt_price'];
            }
        } else {
            if($row_st['scdt_price_type']=='1') { //무료
                $ct_delivery_default_price = 0;
                $ct_delivery_price_add = 0;
                $ct_delivery_qty = 1;
                $ct_delivery_price = 0;
            } else if($row_st['scdt_price_type']=='2') { //조건부무료
                if($row_st['scdt_if_price']<=$ct_price) {
                    $ct_delivery_default_price = 0;
                    $ct_delivery_price_add = 0;
                    $ct_delivery_qty = 1;
                    $ct_delivery_price = 0;
                } else {
                    $ct_delivery_default_price = $row_st['scdt_price'];
                    $ct_delivery_price_add = 0;
                    $ct_delivery_qty = 1;
                    $ct_delivery_price = $row_st['scdt_price'];
                }
            } else if($row_st['scdt_price_type']=='3') { //유료
                $ct_delivery_default_price = $row_st['scdt_price'];
                $ct_delivery_price_add = 0;
                /*if($row_st['pdt_set_chk']=='N') {
                    $ct_delivery_qty = $ct_opt_qty;
                    $row_st['scdt_price'] = ((int)$row_st['scdt_price']*(int)$ct_opt_qty);
                    $ct_delivery_price = $row_st['scdt_price'];
                } else {*/
                $ct_delivery_qty = 1;
                $ct_delivery_price = $row_st['scdt_price'];
                //}
            } else if($row_st['scdt_price_type']=='4') { //수량별
                $ct_delivery_default_price = $row_st['scdt_price'];
                $ct_delivery_price_add = 0;
                $ct_delivery_qty = 1;
                $ct_delivery_price = ($row_st['scdt_price']*ceil((int)$ct_opt_qty/$row_st['scdt_pay_qty']));
            }
        }
    }

    if ($free || $ct_type == 'B') {
        $ct_delivery_default_price = 0;
        $ct_delivery_price_add = 0;
        $ct_delivery_qty = 0;
        $ct_delivery_price = 0;
    }
    if ($ct_delivery_price<0) {
        $ct_delivery_price = 0;
    }

    $arr_rtn = array();
    $arr_rtn['ct_delivery_default_price'] = $ct_delivery_default_price;
    $arr_rtn['ct_delivery_price_add'] = $ct_delivery_price_add;
    $arr_rtn['ct_delivery_qty'] = $ct_delivery_qty;
    $arr_rtn['ct_delivery_price'] = $ct_delivery_price;
    $arr_rtn['ct_delivery_chk'] = $ct_delivery_chk;
    //$arr_rtn['$ct_type'] = $ct_type;
    //$arr_rtn['$d_rtn'] = $d_rtn;

    return $arr_rtn;
}

//------------------------------------------------------------------------------------------------------------------
function f_delivery_tracking($t_code, $t_invoice){ // 스마트택배 송장번호 등록
    global $setup_info;

    if ($setup_info['st_sweettrack_key']) {
        $url = "http://info.sweettracker.co.kr/api/v1/trackingInfo?t_key=".$setup_info['st_sweettrack_key']."&t_code=".$t_code."&t_invoice=".$t_invoice;

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => trim($url),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
            )
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        $jsonArray = json_decode($response, true);

        return $jsonArray;
    }
}
//------------------------------------------------------------------------------------------------------------------
function f_check_bank_account($bank_code, $bank_num){ // 포트원(아임포트) 예금주조회
    $rtn = "";

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.iamport.kr/users/getToken',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => 'imp_key='.IMP_KEY.'&imp_secret='.IMP_SECRET,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded'
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);

    $jsonArray = json_decode($response, true);
    //printr($jsonArray); echo '<br/>';

    if ($jsonArray) {
        if ($jsonArray['code'] == '0') {
            $access_token = $jsonArray['response']['access_token'];
            if ($access_token) {
                $curl1 = curl_init();

                curl_setopt_array($curl1, array(
                    CURLOPT_URL => 'https://api.iamport.kr/vbanks/holder?bank_code='.$bank_code.'&bank_num='.$bank_num,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => array(
                        'Authorization: Bearer '.$access_token,
                        'Content-Type: application/json'
                    ),
                ));

                $response1 = curl_exec($curl1);

                curl_close($curl1);

                $jsonArray1 = json_decode($response1, true);
                //printr($jsonArray1); echo '<br/>';
                if ($jsonArray1['code'] == '0') {
                    $rtn = $jsonArray1['response']['bank_holder']; // 예금주명
                }
            }
        }
    }

    return $rtn;
}
//------------------------------------------------------------------------------------------------------------------

function f_payments_cancel($ot_code, $ct_status_t = '71', $amount = 0) {
    global $DB;

    $rtn = "";
    $isCancel = false;
    $DB->where('ot_code', $ot_code);
    $row_ot = $DB->getone('order_t');

    if($row_ot['idx'] && $row_ot['ot_pg_tid']) {
        $url = "https://api.tosspayments.com/v1/payments/{$row_ot['ot_pg_tid']}/cancel";
        $headers = array( 'Content-Type: application/json', 'Authorization: Basic '.PAYMENTS_KEY );

        if ($ct_status_t == '71') { // 고객이 직접 주문취소
            $cancelReason = '고객 주문취소';
        } else { // 판매자가 주문취소
            //$cancelReason = $row_ot['ot_cancel_memo'] ? $row_ot['ot_cancel_memo'] : $row_ot['ot_cancel_category'];
            //$cancelReason = $cancelReason ? $cancelReason : '판매자 주문취소';
            $cancelReason = '판매자 주문취소';
        }
        $fields = array (
            'cancelReason' => $cancelReason,
        );
        if ($amount) {
            $fields['cancelAmount'] = $amount*1;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response);
        if (($result->status == 'CANCELED' || $result->status == 'PARTIAL_CANCELED') && $result->cancels) { // 결제취소 성공
            $ot_refund_price = 0;
            foreach ($result->cancels as $cancels) {
                $ot_refund_price += $cancels->cancelAmount;
                if ($result->lastTransactionKey == $cancels->transactionKey) {
                    $isCancel = true;
                    unset($arr_query);
                    $arr_query = array(
                        "imp_uid" => $cancels->transactionKey,
                        "ot_code" => $ot_code,
                        "rsp_txt" => $response,
                        "amount" => $cancels->cancelAmount,
                        "wdate" => date('Y-m-d H:i:s', strtotime($cancels->canceledAt)),
                        'oplt_type' => 'C',
                    );
                    $DB->insert('order_pay_log_t', $arr_query);
                }
            }

            unset($arr_query);
            $arr_query['ot_refund_price'] = $ot_refund_price;
            if ($isCancel) {
                //결제취소 시 사용 쿠폰과 포인트 돌려주기
                if ($row_ot['ot_use_point'] && (int)$row_ot['ot_rest_point'] > 0) {
                    $DB->where("mt_idx", $row_ot['mt_idx']);
                    $DB->where("plt_type", 'M');
                    $DB->where("rel_table", 'order');
                    $DB->where("rel_item", $ot_code);
                    $DB->orderBy("idx", "DESC");
                    $row_plt = $DB->getone("point_log_t");
                    if ($row_plt['idx']) {
                        insert_point($row_ot['mt_idx'], $row_ot['ot_rest_point'], '1', "주문취소 - {$ot_code}", 'order', $ot_code);
                        $arr_query['ot_rest_point'] = '0';
                    }
                }

                if ($row_ot['ot_use_coupon'] && (int)$row_ot['ot_rest_coupon'] > 0 && $row_ot['ot_cp_id']) {
                    $DB->where("cp_id", $row_ot['ot_cp_id']);
                    $DB->where("ot_code", $ot_code);
                    $DB->delete('coupon_log_t');
                    $arr_query['ot_rest_coupon'] = '0';
                }
            }

            if ($arr_query) {
                $DB->where("ot_code", $ot_code);
                $DB->update('order_t', $arr_query);
            }
            $rtn = 'Y';
        } else {
            unset($arr_query);
            $arr_query = array(
                "ot_code" => $ot_code,
                "ot_status" => $ct_status_t,
                "olt_type" => 'C',
                "olt_message" => $response,
                "olt_wdate" => $DB->now(),
            );
            $DB->insert('order_log_t', $arr_query);
            $rtn = $result->message;
        }
    } else {
        $rtn = 'error';
    }

    return $rtn;
}

function f_payments_confirm($payment_uid, $merchant_uid, $amount) {
    global $DB;

    $url = "https://api.tosspayments.com/v1/payments/confirm";
    $headers = array( 'Content-Type: application/json', 'Authorization: Basic '.PAYMENTS_KEY );

    $fields = array (
        'paymentKey' => $payment_uid,
        'orderId' => $merchant_uid,
        'amount' => $amount,
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    $rtn = curl_exec($ch);
    curl_close($ch);

    return $rtn;
}

function f_tosspay_post($url, $fields, $uuid="") {
    $headers = array( 'Content-Type: application/json', 'Authorization: Basic '.PAYMENTS_KEY );
    if ($uuid) {
        $headers[] = 'Idempotency-Key: '.$uuid;
    }
    $ch = curl_init(TOSSPAYMENTS_URL.$url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    $response = curl_exec($ch);
    $error_msg = curl_error($ch);
    curl_close($ch);
    $rtn = json_decode($response, true);

    return $rtn;
}

function f_tosspay_get($url) {
    $headers = array( 'Content-Type: application/json', 'Authorization: Basic '.PAYMENTS_KEY );
    $ch = curl_init(TOSSPAYMENTS_URL.$url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    $response = curl_exec($ch);
    curl_close($ch);
    $rtn = json_decode($response, true);

    return $rtn;
}

function payouts_submall($act, $arr){
    global $DB;
    $msg = "";

    if ($act=='input' || $act=='update') {
        $fields = array(
            'account' => ['bank' => $arr['st_bank'], 'accountNumber' => str_replace('-','',$arr['st_bank_account']), 'holderName' => $arr['st_bank_name']],
            'companyName' => $arr['st_name'],
            'representativeName' => $arr['st_company_boss'],
            'businessNumber' => str_replace('-','',$arr['st_company_num1']),
            'phoneNumber' => str_replace('-','',$arr['mt_hp']),
            'email' => $arr['mt_email'],
        );
    } else {
        $fields = array();
    }
    if ($act=='input') {
        $fields['subMallId'] = trim($arr['payoutStoreId']);//date('ymdHi').'1'
        $fields['type'] = $arr['st_company_type']=='2'?'CORPORATE':'INDIVIDUAL';
        $rtn = f_tosspay_post("payouts/sub-malls", $fields);
        if ($rtn) {
            if ($rtn['code']) {
                $msg = $rtn['message']."\n".$rtn['code'];
            } else {
                if ($rtn['subMallId']) {
                    unset($arr_query);
                    $arr_query = array(
                        'payoutStoreId' => $rtn['subMallId'],
                    );
                    $DB->where('idx', $arr['st_idx']);
                    $DB->update('store_t', $arr_query);

                    $DB->where("st_idx", $arr['st_idx']);
                    $DB->where("(payoutStoreId = '' OR payoutStoreId IS NULL)");
                    $payout_t = $DB->getone("calculate_payout_t", "COUNT(*) AS cnt");
                    if ($payout_t['cnt']) {
                        $DB->where("st_idx", $arr['st_idx']);
                        $DB->where("(payoutStoreId = '' OR payoutStoreId IS NULL)");
                        $DB->update("calculate_payout_t", array('payoutStoreId' => $rtn['subMallId']));
                    }

                    $msg = "Y";
                }
            }
        }
    } else if ($act=='update') {
        $rtn = f_tosspay_post("payouts/sub-malls/{$arr['payoutStoreId']}", $fields);
        if ($rtn) {
            if ($rtn['code']) {
                $msg = $rtn['message']."\n".$rtn['code'];
            } else {
                if ($rtn['subMallId']) {
                    $DB->where("st_idx", $arr['st_idx']);
                    $DB->where("(payoutStoreId = '' OR payoutStoreId IS NULL)");
                    $payout_t = $DB->getone("calculate_payout_t", "COUNT(*) AS cnt");
                    if ($payout_t['cnt']) {
                        $DB->where("st_idx", $arr['st_idx']);
                        $DB->where("(payoutStoreId = '' OR payoutStoreId IS NULL)");
                        $DB->update("calculate_payout_t", array('payoutStoreId' => $rtn['subMallId']));
                    }

                    $msg = "Y";
                }
            }
        }
    } else if ($act=='delete') {
        $rtn = f_tosspay_post("payouts/sub-malls/{$arr['payoutStoreId']}/delete", $fields);
        if ($rtn) {
            if ($rtn['code']) {
                $msg = $rtn['message']."\n".$rtn['code'];
            } else {
                unset($arr_query);
                $arr_query = array(
                    'payoutStoreId' => '',
                );
                $DB->where('idx', $arr['st_idx']);
                $DB->update('store_t', $arr_query);

                $msg = "Y";
            }
        }
    }

    return $msg;
}

function delivery_barogo($url, $post) {
    $ch = curl_init(BAROGO_URL.$url);
    $post = json_encode($post);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , "Authorization: Bearer ".BAROGO_API_KEY ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    $result = curl_exec($ch);
    curl_close($ch);
    $rtn = json_decode($result, true);

    return $rtn;
}
function delivery_barogo_get($url) {
    $ch = curl_init(BAROGO_URL.$url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , "Authorization: Bearer ".BAROGO_API_KEY ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    $result = curl_exec($ch);
    curl_close($ch);
    $rtn = json_decode($result, true);

    return $rtn;
}

function delivery_vroong($url, $post) {
    $ch = curl_init(VROONG_URL.$url);
    $post = json_encode($post);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , 'apikey: '.VROONG_API_KEY, 'secret: '.VROONG_SECRET ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    $result = curl_exec($ch);
    curl_close($ch);
    $rtn = json_decode($result, true);

    return $rtn;
}

function delivery_possible($ot_code, $row_st, $post){ // 배달가능여부 조회 및 주문접수 저장
    global $DB, $member, $socket_client, $arr_delivery_agency_rk, $arr_err_barogo;
    $return_msg = "";

    $DB->where('ot_code', $ot_code);
    $row_ot = $DB->getone('order_t');

    //배달 정보 저장
    if ($row_st['st_type']=='A' && $row_ot['ot_type']=='A') {
        $DB->where('ot_code', $ot_code);
        $row_odt = $DB->getone('order_delivery_t');

        if($row_odt['odt_idx']) {
            unset($arr_query);
            $arr_query = array(
                "odt_type" => $arr_delivery_agency_rk[$row_st['st_delivery_agency']],
                "ot_count_time1" => $post['count_time1'],
                "ot_count_time2" => $post['count_time2'],
                "odt_pick_address1" => $row_st['st_add1'],
                "odt_pick_address2" => $row_st['st_add2'],
                "odt_drop_address1" => $row_ot['ot_radd1'],
                "odt_drop_address2" => $row_ot['ot_radd2'],
                "ot_price" => $row_ot['ot_price'],
                "ot_store_time" => $post['count_time1_u'],
            );
            $DB->where('odt_idx', $row_odt['odt_idx']);
            $DB->update('order_delivery_t', $arr_query);
        } else {
            unset($arr_query);
            $arr_query = array(
                "ot_code" => $ot_code,
                "ot_count_time1" => $post['count_time1'],
                "ot_count_time2" => $post['count_time2'],
                "odt_pick_address1" => $row_st['st_add1'],
                "odt_pick_address2" => $row_st['st_add2'],
                "odt_drop_address1" => $row_ot['ot_radd1'],
                "odt_drop_address2" => $row_ot['ot_radd2'],
                "ot_price" => $row_ot['ot_price'],
                "ot_store_time" => $post['count_time1_u'],
                "odt_type" => $arr_delivery_agency_rk[$row_st['st_delivery_agency']],
                "odt_wdate" => $DB->now(),
            );
            $DB->insert('order_delivery_t', $arr_query);
        }

        if($row_st['st_delivery_agency']=='barogo') { //바로고
            $request_post = array(
                "orderAgencyId" => BAROGO_ID,
                "orderAgencyStoreId" => $row_st['orderAgencyStoreId'],
                "dropRoadAddress" => $row_ot['ot_radd1'],
                "dropAddressDetail" => $row_ot['ot_radd2'],
                "pickupWishAt" => ($post['count_time1_u']*1000),
            );
            $rtn = delivery_barogo('/api/delivery-possible', $request_post);
            if($rtn['statusCode']=='200') {
                if($rtn['data']['isPossible']=='true') {
                    unset($arr_query);
                    $arr_query = array(
                        "odt_isPossible" => $rtn['data']['isPossible'],
                        "odt_pick_time" => $rtn['data']['deliveryInfo']['pickupExpectedAt'],
                        "odt_pick_distance" => $rtn['data']['deliveryInfo']['deliveryDistance'],
                        "odt_base_fee" =>$rtn['data']['deliveryInfo']['deliveryPrice'],
                        "odt_extra_fee" => $rtn['data']['deliveryInfo']['totalExtraCharge'],
                        "odt_sum_fee" => $rtn['data']['deliveryInfo']['totalDeliveryPrice'],
                        "odt_wdate" => $DB->now(),
                    );
                    $DB->where('ot_code', $ot_code);
                    $DB->update('order_delivery_t', $arr_query);

                    $return_msg = "Y";
                } else {
                    if ($rtn['reason']) {
                        unset($arr_query);
                        $arr_query = array(
                            "odt_log" => json_encode(array('code' => $rtn['reason'], 'message' => $arr_err_barogo[$rtn['reason']])),
                        );
                        $DB->where('ot_code', $ot_code);
                        $DB->update('order_delivery_t', $arr_query);
                    }

                    $return_msg = "배달대행사 사정으로 픽업이 되지 않습니다. 다시 확인바랍니다.";
                }
            } else {
                $return_msg = "배달대행사 사정으로 픽업이 되지 않습니다. 다시 확인바랍니다.";
            }
        } else if($row_st['st_delivery_agency']=='vroong') { //부릉
            $request_post = array(
                "branch_code" => $row_st['branch_code'],
                //"dest_address" => $row_ot['ot_radd1'],
                //"dest_address_detail" => $row_ot['ot_radd2'],
                "dest_address_road" => $row_ot['ot_radd1'],
                "dest_address_detail_road" => $row_ot['ot_radd2'],
                "dest_lat" => $row_ot['ot_rlat'],
                "dest_lng" => $row_ot['ot_rlng'],
                "order_pickup_time" => date("Y-m-d H:i:s", $post['count_time1_u']),
            );
            $rtn = delivery_vroong('/api/delivery/submit_reserve_fee', $request_post);
            if($rtn['result']=='SUCCESS') {
                unset($arr_query);
                $arr_query = array(
                    "odt_isPossible" => 'Y',
                    "odt_pick_time" => $rtn['order_pickup_time'],
                    "odt_pick_distance" => $rtn['distance'],
                    "odt_base_fee" =>$rtn['base_fee'],
                    "odt_extra_fee" => $rtn['extra_fee'],
                    "odt_sum_fee" => $rtn['sum_total'],
                    "odt_wdate" => $DB->now(),
                );
                $DB->where('ot_code', $ot_code);
                $DB->update('order_delivery_t', $arr_query);

                $return_msg = "Y";
            } else {
                unset($arr_query);
                $arr_query = array(
                    "odt_log" => json_encode(array('code' => $rtn['error_code'], 'message' => $rtn['error_message'])),
                );
                $DB->where('ot_code', $ot_code);
                $DB->update('order_delivery_t', $arr_query);

                $return_msg = "배달대행사 사정으로 픽업이 되지 않습니다. 다시 확인바랍니다.";
            }
        }
    } else {
        $return_msg = "Y";
    }

    if ($return_msg == "Y") {
        unset($arr_query);
        $arr_query = array(
            "ot_store_time" => $post['count_time1_u'],
            "ot_delivery_time" => $post['count_time2_u'],
            "ot_status" => '3',
            "ot_adate" => $DB->now(),
        );
        $DB->where('ot_code', $ot_code);
        $DB->update('order_t', $arr_query);

        $ct_push_t = 'orderFin_A';
        if ($ct_push_t) {
            proc_noti("push", $row_ot['mt_idx'], $ct_push_t, $ot_code);
        }
        // socket 전송
        if ($socket_client) {
            $arr = ['ot_code' => $row_ot['ot_code'], 'send_mt_idx' => $member['mt_idx'], 'mt_idx' => $row_ot['mt_idx'], 'st_idx' => $row_ot['st_idx']];
            $socket_client->emit('update_order', $arr);
        }
    }

    return $return_msg;
}

function delivery_rider_call($ot_code, $row_st){ // 라이더호출
    global $DB, $arr_err_barogo;
    $return_msg = "";

    $DB->where('ot_code', $ot_code);
    $row_ot = $DB->getone('order_t');

    if ($row_st['st_type']=='A' && $row_ot['ot_type']=='A') {
        $DB->where('ot_code', $ot_code);
        $row_odt = $DB->getone('order_delivery_t');

        //배대사 배달접수
        if($row_odt['odt_type']=='1') { //바로고
            if($row_ot['ot_rmemo2']=='') {
                $row_ot['ot_rmemo2'] = '-';
            }
            $request_post = array(
                "orderType" => "FOR_DELIVERY",
                "orderAgencyId" => BAROGO_ID,
                "orderAgencyStoreId" => $row_st['orderAgencyStoreId'],
                "orderAgencyOrderId" => $ot_code,
                "totalPayPrice" => $row_ot['ot_price'],
                "actualPayPrice" => $row_ot['ot_price'],
                "prepaidPrice" => $row_ot['ot_price'],
                "prepaidMethod" => "CARD",
                "paymentCashPrice" => 0,
                "paymentCardPrice" => 0,
                "taxFreePayPrice" => 0,
                "orderProducts" => array(
                    0 => array(
                        "type" => "FOOD",
                        "name" => $row_ot['ot_title'],
                        "totalPrice" => $row_ot['ot_price'],
                        "unitPrice" => $row_ot['ot_price'],
                        "quantity" => 1,
                    )
                ),
                "ordererPhone" => str_replace('-', '', $row_ot['ot_hp']),
                "receiverPhone" => str_replace('-', '', $row_ot['ot_rhp']),
                "ordererName" => $row_ot['ot_name'],
                "receiverName" => $row_ot['ot_rname'],
                "storeOrderMemo" => APP_AUTHOR,
                "ordererOrderMemo" => $row_ot['ot_rmemo2'],
                "dropRoadAddress" => $row_ot['ot_radd1'],
                "dropAddressDetail" => $row_ot['ot_radd2'],
                "isUntact" => true,
                "isReservation" => false,
                "pickupWishAt" => ((time()+($row_odt['ot_count_time1']*60))*1000),
                "orderAgencyOrderCreatedAt" => ($row_ot['ot_store_time']*1000),
            );

            $rtn = delivery_barogo('/api/orders', $request_post);
            if($rtn['statusCode']=='200') {
                if($rtn['data']['isSuccess']=='true') {
                    unset($arr_query);
                    $arr_query = array(
                        "odt_isPossible" => $rtn['data']['isPossible'],
                        "odt_pick_time" => $rtn['data']['deliveryInfo']['pickupExpectedAt'],
                        "odt_pick_distance" => $rtn['data']['deliveryInfo']['deliveryDistance'],
                        "odt_base_fee" =>$rtn['data']['deliveryInfo']['deliveryPrice'],
                        "odt_extra_fee" => $rtn['data']['deliveryInfo']['totalExtraCharge'],
                        "odt_sum_fee" => $rtn['data']['deliveryInfo']['totalDeliveryPrice'],
                        "odt_request" => json_encode($request_post),
                        "odt_response" => json_encode($rtn),
                        "odt_wdate" => $DB->now(),
                    );
                    $DB->where('ot_code', $ot_code);
                    $DB->update('order_delivery_t', $arr_query);

                    $return_msg = "라이더 호출되었습니다.<br/>라이더 배차시 까지 잠시만 기다려 주세요.";
                } else {
                    if ($rtn['reason']) {
                        unset($arr_query);
                        $arr_query = array(
                            "odt_log" => json_encode(array('code' => $rtn['reason'], 'message' => $arr_err_barogo[$rtn['reason']])),
                        );
                        $DB->where('ot_code', $ot_code);
                        $DB->update('order_delivery_t', $arr_query);
                    }

                    $return_msg = "배달대행사 사정으로 픽업이 되지 않습니다. 다시 확인바랍니다.";
                }
            } else {
                $return_msg = "배달대행사 사정으로 픽업이 되지 않습니다. 다시 확인바랍니다.";
            }
        } else if($row_st['st_delivery_agency']=='vroong') { //부릉
            $request_post = array(
                "branch_code" => $row_st['branch_code'],
                "request_id" => $ot_code,
                "delivery_value" => $row_ot['ot_price'],
                "payment_method" => "PREPAID",
                "item_detail" => array(
                    0 => array(
                        "type" => "ITEM",
                        "name" => $row_ot['ot_title'],
                        "unit_price" => $row_ot['ot_price'],
                        "quantity" => 1,
                        "stock_code" => '999',
                        "option_detail" => array(
                            0 => array(
                                "name" => "FOOD",
                                "quantity" => 1,
                                "unit_price" => $row_ot['ot_price'],
                            ),
                        ),
                    )
                ),
                "sender_phone" => str_replace('-', '', $row_st['st_tel']),
                "receiverPhone" => str_replace('-', '', $row_ot['ot_rhp']),
                "order_notes" => $row_ot['ot_rmemo2'],
                "dest_address_road" => $row_ot['ot_radd1'],
                "dest_address_detail_road" => $row_ot['ot_radd2'],
                "dest_lat" => $row_ot['ot_rlat'],
                "dest_lng" => $row_ot['ot_rlng'],
                "contactless" => true,
                "pickup_in" => ((time()+($row_odt['ot_count_time1']*60))*1000),
            );

            $rtn = delivery_vroong('/api/delivery/submit', $request_post);
            if($rtn['result']=='SUCCESS') {
                unset($arr_query);
                $arr_query = array(
                    "odt_delivery_id" => $rtn['delivery_id'],
                    "odt_pick_distance" => $rtn['distance'],
                    "odt_base_fee" =>$rtn['base_fee'],
                    "odt_extra_fee" => $rtn['extra_fee'],
                    "odt_sum_fee" => $rtn['sum_total'],
                    "odt_request" => json_encode($request_post),
                    "odt_response" => json_encode($rtn),
                    "odt_wdate" => $DB->now(),
                );
                $DB->where('ot_code', $ot_code);
                $DB->update('order_delivery_t', $arr_query);

                $return_msg = "라이더 호출되었습니다.<br/>라이더 배차시 까지 잠시만 기다려 주세요.";
            } else {
                unset($arr_query);
                $arr_query = array(
                    "odt_log" => json_encode(array('code' => $rtn['error_code'], 'message' => $rtn['error_message'])),
                );
                $DB->where('ot_code', $ot_code);
                $DB->update('order_delivery_t', $arr_query);

                $return_msg = "배달대행사 사정으로 픽업이 되지 않습니다. 다시 확인바랍니다.";
            }
        } else {

        }
    } else {
        $return_msg = "Y";
    }

    return $return_msg;
}
function delivery_rider_update($ot_code, $row_st){ //배달 정보 업데이트
    global $DB, $socket_client;
    $return_msg = "";
    $ct_push_t = $ct_push_seller_t = "";
    $chk_update = 'N';
    $chk_cancel = 'N';

    $DB->where('ot_code', $ot_code);
    $row_ot = $DB->getone('order_t');

    if ($row_st['st_type']=='A' && $row_ot['ot_type']=='A') {
        //배달 대행사에 상태확인후 업데이트
        if($row_st['st_delivery_agency']=='barogo') { //바로고
            /* //--바로고는 웹훅이 있어서 아래 소스 필요없음
            $rtn = delivery_barogo_get('/api/orders/'.$ot_code);
            //printr($rtn);

            if($rtn['statusCode']=='200') {
                if($rtn['data']['deliveries'][0]['status']=='ALLOCATED') { //배차
                    if ($row_ot['ot_status'] != '6') {
                        unset($arr_query);
                        $arr_query = array(
                            "ot_status" => '6',
                        );
                        $DB->where('ot_code', $ot_code);
                        $DB->update('order_t', $arr_query);

                        $chk_cancel = 'M';
                        $chk_update = 'Y';
                        $ct_push_seller_t = "deliveryCallOk_seller";
                    }
                } else if($rtn['data']['deliveries'][0]['status']=='PICKUP_FINISHED') { //픽업 완료
                    if ($row_ot['ot_status'] != '8') {
                        unset($arr_query);
                        $arr_query = array(
                            "ot_status" => '8',
                            "ot_dsdate" => $DB->now(),
                        );
                        $DB->where('ot_code', $ot_code);
                        $DB->update('order_t', $arr_query);

                        $chk_cancel = 'M';
                        $chk_update = 'Y';
                        $ct_push_t = "deliveryNow_A";
                    }
                } else if($rtn['data']['deliveries'][0]['status']=='DROP_FINISHED') { //배달 완료
                    if ($row_ot['ot_status'] != '9') {
                        unset($arr_query);
                        $arr_query = array(
                            "ot_status" => '9',
                            "ot_dedate" => $DB->now(),
                        );
                        $DB->where('ot_code', $ot_code);
                        $DB->update('order_t', $arr_query);

                        $chk_cancel = 'M';
                        $chk_update = 'Y';
                        $ct_push_t = "deliverFin_A";
                        $ct_push_seller_t = "deliveryFin_A_seller";
                    }
                } else if($rtn['data']['deliveries'][0]['status']=='ALLOCATION_CANCELED' ||
                    $rtn['data']['deliveries'][0]['status']=='REJECTED' ||
                    $rtn['data']['deliveries'][0]['status']=='FAILED' ||
                    $rtn['data']['deliveries'][0]['status']=='CANCELED') { //취소, 배차 취소, 배차 거절
                    if ($row_ot['ot_status'] != '78') {
                        unset($arr_query);
                        $arr_query = array(
                            "ot_cancel_category" => '',
                            "ot_cancel_memo" => '배달대행사 사정으로 배달이 취소되었습니다.',
                            "ot_status" => '78',
                            "ot_cdate" => $DB->now(),
                            "ot_end" => 'Y',
                        );
                        $DB->where('ot_code', $ot_code);
                        $DB->update('order_t', $arr_query);

                        //포인트, 쿠폰, 결제취소 처리해야함
                        f_payments_cancel($ot_code, '78');

                        $chk_cancel = 'Y';
                        $chk_update = 'Y';
                        $ct_push_t = "orderCancel";
                    }
                }

                unset($arr_query);
                $arr_query = array(
                    "odt_status" => $rtn['data']['deliveries'][0]['status'],
                    "odt_driver_hp" => $rtn['data']['deliveries'][0]['driverPhone'],
                    "odt_pick_time" => $rtn['data']['deliveries'][0]['pickupExpectedAt'],
                    "odt_wdate" => $DB->now(),
                );
                $DB->where('ot_code', $ot_code);
                $DB->update('order_delivery_t', $arr_query);

            } else {
                $return_msg = "배달대행사와 통신이 제대로 되지 않았습니다. 재접속 바랍니다.";
            }
            */
        } else if($row_st['st_delivery_agency']=='vroong') { //부릉
            $DB->where('ot_code', $ot_code);
            $DB->orderBy('odt_idx', "desc");
            $row_odt = $DB->getone('order_delivery_t');
            if ($row_odt['odt_delivery_id']) {
                //배송중 조회
                $request_post = array(
                    "delivery_id" => $row_odt['odt_delivery_id'],
                );
                $rtn = delivery_vroong('/api/delivery/track', $request_post);
                if($rtn['result']=='SUCCESS') {
                    /*RESERVED: 예약
                    SUBMIT_FAILED: 접수 실패
                    ASSIGNING_AGENT: 배송기사 지정중
                    PICKING_UP: 배송기사 픽업중
                    DELIVERING: 배송기사 배달중
                    COMPLETED: 배송 완료
                    CANCELED: 배송 취소*/
                    if ($row_odt['odt_status'] != $rtn['status']) {
                        unset($arrQuery);
                        $arrQuery = array(
                            "odt_callback" => json_encode($rtn),
                            "odt_type" => '2',
                            "ot_code" => $ot_code,
                            "odt_pick_distance" => $rtn['agent_distance_to_origin'],
                            "odt_driver_hp" => $rtn['agent_phone'],
                            "odt_status" => $rtn['status'],
                            "odt_wdate" => $DB->now(),
                        );
                        $DB->insert('order_delivery_log_t', $arrQuery);

                        unset($arrQuery);
                        $arrQuery = array(
                            "odt_request" => json_encode($request_post),
                            "odt_response" => json_encode($rtn),
                            "odt_wdate" => $DB->now(),
                        );
                        if ($rtn['status']) { $arrQuery['odt_status'] = $rtn['status']; }
                        if ($rtn['agent_phone']) { $arrQuery['odt_driver_hp'] = $rtn['agent_phone']; }
                        if ($rtn['agent_assigned_at']) { $arrQuery['odt_assigned_at'] = date('Y-m-d H:i:s', $rtn['agent_assigned_at']); }
                        if ($rtn['agent_picked_up_at']) { $arrQuery['odt_picked_up_at'] = date('Y-m-d H:i:s', $rtn['agent_picked_up_at']); }
                        if ($rtn['completed_at']) { $arrQuery['odt_completed_at'] = date('Y-m-d H:i:s', $rtn['completed_at']); }
                        if ($rtn['canceled_at']) { $arrQuery['odt_canceled_at'] = date('Y-m-d H:i:s', $rtn['canceled_at']); }
                        $DB->where('ot_code', $ot_code);
                        $DB->update('order_delivery_t', $arrQuery);

                        if ($arrQuery['odt_status']=='ASSIGNING_AGENT') { //라이더 배차
                            if ($row_ot['ot_status'] != '6') {
                                unset($arr_query);
                                $arr_query = array(
                                    "ot_status" => '6',
                                );
                                $DB->where('ot_code', $ot_code);
                                $DB->update('order_t', $arr_query);

                                $chk_cancel = 'M';
                                $chk_update = 'Y';
                                $ct_push_seller_t = "deliveryCallOk_seller";
                            }
                        } else if ($arrQuery['odt_status']=='PICKING_UP') { //픽업 완료
                            if ($row_ot['ot_status'] != '8') {
                                unset($arr_query);
                                $arr_query = array(
                                    "ot_status" => '8',
                                    "ot_dsdate" => $DB->now(),
                                );
                                $DB->where('ot_code', $ot_code);
                                $DB->update('order_t', $arr_query);

                                $chk_cancel = 'M';
                                $chk_update = 'Y';
                                $ct_push_t = "deliveryNow_A";
                            }
                        } else if ($arrQuery['odt_status']=='DELIVERING') { //배달중
                        } else if ($arrQuery['odt_status']=='COMPLETED') { //배달 완료
                            if ($row_ot['ot_status'] != '9') {
                                unset($arr_query);
                                $arr_query = array(
                                    "ot_status" => '9',
                                    "ot_dedate" => $DB->now(),
                                );
                                $DB->where('ot_code', $ot_code);
                                $DB->update('order_t', $arr_query);

                                $chk_cancel = 'M';
                                $chk_update = 'Y';
                                $ct_push_t = "deliverFin_A";
                                $ct_push_seller_t = "deliveryFin_A_seller";
                            }
                        } else if ($arrQuery['odt_status']=='CANCELED' || //취소
                            $arrQuery['odt_status']=='SUBMIT_FAILED') { //실패
                            if ($row_ot['ot_status'] != '78') {
                                unset($arr_query);
                                $arr_query = array(
                                    "ot_cancel_category" => '',
                                    "ot_cancel_memo" => '배달대행사 사정으로 배달이 취소되었습니다.',
                                    "ot_status" => '78',
                                    "ot_cdate" => $DB->now(),
                                    "ot_end" => 'Y',
                                );
                                $DB->where('ot_code', $ot_code);
                                $DB->update('order_t', $arr_query);

                                //포인트, 쿠폰, 결제취소 처리해야함
                                f_iamport_cancel($ot_code, '78');

                                $chk_cancel = 'Y';
                                $chk_update = 'Y';
                                $ct_push_t = "orderCancel";
                            }
                        }
                    }
                } else {
                    unset($arr_query);
                    $arr_query = array(
                        "odt_log" => json_encode(array('code' => $rtn['error_code'], 'message' => $rtn['error_message'])),
                    );
                    $DB->where('ot_code', $ot_code);
                    $DB->update('order_delivery_t', $arr_query);

                    $return_msg = "배달대행사 ERROR : ".$rtn['error_message'];
                }
            }
        } else {

        }

        if($chk_cancel=='Y') {
            $return_msg = "배달대행사 사정으로 배달이 취소되었습니다.";
        } else if($chk_cancel=='M') {
            $return_msg = "주문상태가 변경되었습니다. 확인바랍니다.";
        }

        if ($chk_update == 'Y') {
            // socket 전송
            if ($socket_client) {
                $arr = ['ot_code' => $row_ot['ot_code'], 'send_mt_idx' => '', 'mt_idx' => $row_ot['mt_idx'], 'st_idx' => $row_ot['st_idx']];
                $socket_client->emit('update_order', $arr);
            }

            if ($ct_push_t) {
                $output = proc_noti("push", $row_ot['mt_idx'], $ct_push_t, $ot_code);
            }
            if ($ct_push_seller_t) {
                $DB->where('idx', $row_ot['st_idx']);
                $row_st = $DB->getone('store_t');
                if ($row_st['mt_idx']) {
                    $mt_staff = array();
                    $mt_staff[] = $row_st['mt_idx'];
                    $DB->where('a1.mt_status', 'Y');
                    $DB->where('a1.mt_level', '3');
                    $DB->where('a1.st_idx', $row_ot['st_idx']);
                    $list_mt = $DB->get("member_t a1", "idx as mt_idx");
                    foreach ($list_mt as $row_mt) {
                        $mt_staff[] = $row_mt['mt_idx'];
                    }
                    $output = proc_noti("push", implode(',', $mt_staff), $ct_push_seller_t, $ot_code);
                }
            }
        }
    } else {
        $return_msg = "Y";
    }

    return array(
        'msg' => $return_msg,
        'chk_update' => $chk_update,
        'push_output' => $output,
        '$ct_push_t' => $ct_push_t,
        '$ct_push_seller_t' => $ct_push_seller_t,
    );
}

//------------------------------------------------------------------------------------------------------------------
function upload_file_data($files, $filename, $reheight=700){
    unset($arr_query_i);
    $file = $files['tmp_name'];
    $file_name = $files['name'];
    $file_size = $files['size'];
    $file_type = $files['type'];

    if($file_name!="") {
        $temp_img = $filename.".".get_file_ext($file_name);
        upload_file($file, $temp_img, DATA_PATH."/");

        $filepath = DATA_PATH."/".$temp_img;

        $timg = getimagesize($filepath);
        $width = $timg[0];
        $height = $timg[1];

        $exif = exif_read_data($filepath);
        $orientation = $exif['Orientation'];
        if ($orientation) {
            $dst_height = $reheight;
            $dst_width = round(($dst_height * $width) / $height);
            resize_crop_image($dst_width, $dst_height, $filepath, $filepath, false, false, 100, $orientation);
        } else {
            thumnail_width($filepath, $temp_img, DATA_PATH."/", $reheight);
        }

        return $temp_img;
    } else {
        return "";
    }
}

function upload_file_multi_data($files, $key, $filename, $reheight=700){
    unset($arr_query_i);
    $file = $files['tmp_name'][$key];
    $file_name = $files['name'][$key];
    $file_size = $files['size'][$key];
    $file_type = $files['type'][$key];

    if($file_name!="") {
        $temp_img = $filename.".".get_file_ext($file_name);
        upload_file($file, $temp_img, DATA_PATH."/");

        $filepath = DATA_PATH."/".$temp_img;

        $timg = getimagesize($filepath);
        $width = $timg[0];
        $height = $timg[1];

        $exif = exif_read_data($filepath);
        $orientation = $exif['Orientation'];
        if ($orientation) {
            $dst_height = $reheight;
            $dst_width = round(($dst_height * $width) / $height);
            resize_crop_image($dst_width, $dst_height, $filepath, $filepath, false, false, 100, $orientation);
        } else {
            thumnail_width($filepath, $temp_img, DATA_PATH."/", $reheight);
        }

        return $temp_img;
    } else {
        return "";
    }
}
//------------------------------------------------------------------------------------------------------------------
function upload_file1($file, $filepath){
    $image_extension = 'gif|jpg|jpeg|png';
    $flash_extension = 'swf';

    // 첨부파일
    $file_dir = DATA_PATH.'/'.$filepath;

    @mkdir($file_dir, DIR_PERMISSION);
    @chmod($file_dir, DIR_PERMISSION);

    $chars_array = array_merge(range(0,9), range('a','z'), range('A','Z'));
    $upload_max_filesize = number_format(1048576);// 2097152

    //--------------------------------------------------------------------------------------------------
    // 가변 파일 업로드
    $file_upload_msg = '';
    $upload = array();
    if ($file['name']) {
        $upload['file']     = '';
        $upload['source']   = '';
        $upload['filesize'] = 0;
        $upload['image']    = array();
        $upload['image'][0] = '';
        $upload['image'][1] = '';
        $upload['image'][2] = '';

        $tmp_file  = $file['tmp_name'];
        $filesize  = $file['size'];
        $filename  = $file['name'];
        $filename  = get_safe_filename($filename);

        // 서버에 설정된 값보다 큰파일을 업로드 한다면
        if ($filename) {
            if ($file['error'] == 1) {
                $file_upload_msg .= '\"'.$filename.'\" 파일의 용량이 서버에 설정('.$upload_max_filesize.')된 값보다 크므로 업로드 할 수 없습니다.\\n';
            }
            else if ($file['error'] != 0) {
                $file_upload_msg .= '\"'.$filename.'\" 파일이 정상적으로 업로드 되지 않았습니다.\\n';
            }
        }

        if (is_uploaded_file($tmp_file) && !$file_upload_msg) {
            //=================================================================\
            // 090714
            // 이미지나 플래시 파일에 악성코드를 심어 업로드 하는 경우를 방지
            // 에러메세지는 출력하지 않는다.
            //-----------------------------------------------------------------
            $timg = @getimagesize($tmp_file);
            // image type
            if ( preg_match("/\.({$image_extension})$/i", $filename) ||
                preg_match("/\.({$flash_extension})$/i", $filename) ) {
                if ($timg['2'] < 1 || $timg['2'] > 16)
                    $file_upload_msg .= 'false';
            }
            //=================================================================
            if (!$file_upload_msg) {
                $upload['image'] = $timg;

                // 프로그램 원래 파일명
                $upload['source'] = $filename;
                $upload['filesize'] = $filesize;

                // 아래의 문자열이 들어간 파일은 -x 를 붙여서 웹경로를 알더라도 실행을 하지 못하도록 함
                $filename = preg_replace("/\.(php|phtm|htm|cgi|pl|exe|jsp|asp|inc)/i", "$0-x", $filename);

                shuffle($chars_array);
                $shuffle = implode('', $chars_array);

                // 첨부파일 첨부시 첨부파일명에 공백이 포함되어 있으면 일부 PC에서 보이지 않거나 다운로드 되지 않는 현상이 있습니다. (길상여의 님 090925)
                $upload['file'] = abs(ip2long($_SERVER['REMOTE_ADDR'])).'_'.substr($shuffle,0,8).'_'.replace_filename($filename);

                $dest_file = $file_dir.'/'.$upload['file'];

                // 업로드가 안된다면 에러메세지 출력하고 죽어버립니다.
                $error_code = move_uploaded_file($tmp_file, $dest_file) or die($file['error']);

                // 올라간 파일의 퍼미션을 변경합니다.
                chmod($dest_file, FILE_PERMISSION);
            }
        }
    }
    return $file_upload_msg ? $file_upload_msg : $upload['file'];
}
//------------------------------------------------------------------------------------------------------------------
function get_list_thumbnail($file_name='', $content='', $thumb_width, $thumb_height) {
    global $setup_info;
    $edt = true;
    $dir = 'images/uploads';
    $image_extension = 'gif|jpg|jpeg|png';
    $filepath = DATA_PATH;
    $fileurl = DATA_URL;

    if ($content) {
        $dir = 'images/editor';
        $fileurl = DATA_URL."/images/editor";
        $matches = get_editor_image($content, false);
        for($i=0; $i<count($matches[1]); $i++) {
            // 이미지 path 구함
            $p = parse_url($matches[1][$i]);
            if(strpos($p['path'], '/'.$dir.'/') != 0)
                $data_path = preg_replace('/^\/.*\/'.$dir.'/', '/'.$dir, $p['path']);
            else
                $data_path = $p['path'];

            $srcfile = GET_PATH.$data_path;

            if(preg_match("/\.({$image_extension})$/i", $srcfile) && is_file($srcfile)) {
                $size = @getimagesize($srcfile);
                if(empty($size))
                    continue;

                $filename = basename($srcfile);
                $filepath = dirname($srcfile);

                break;
            }
        }
    }
    if ($file_name) {
        $filename = $file_name;
        $data_path = '';
    }

    if(!$filename)
        return false;

    $tname = thumbnail($filename, $filepath, $filepath, $thumb_width, $thumb_height);

    if($tname) {
        if($edt) {
            // 오리지날 이미지
            $ori = DATA_URL.'/'.$data_path;
            // 썸네일 이미지
            $src = DATA_URL.'/'.$tname."?v=".str_replace('-','',$setup_info['st_optimize_date']);//GET_URL.str_replace($filename, $tname, $data_path);
        } else {
            $ori = DATA_URL.'/'.$filename;
            $src = DATA_URL.'/'.$tname;
        }
    } else {
        $thumb = '';
        //return false;
    }

    $thumb = $src;//array("src"=>$src, "ori"=>$ori);

    return $thumb;
}
//------------------------------------------------------------------------------------------------------------------
function thumbnail($filename, $source_path, $target_path, $thumb_width, $thumb_height, $is_create=false, $is_crop=true, $crop_mode='center', $is_sharpen=false, $um_value='80/0.5/3'){

    if(!$thumb_width && !$thumb_height)
        return;

    $source_file = "$source_path/$filename";

    if(!is_file($source_file)) // 원본 파일이 없다면
        return;

    $size = @getimagesize($source_file);
    if($size[2] < 1 || $size[2] > 3) // gif, jpg, png 에 대해서만 적용
        return;

    if (!is_dir($target_path)) {
        @mkdir($target_path, DIR_PERMISSION);
        @chmod($target_path, DIR_PERMISSION);
    }

    // 디렉토리가 존재하지 않거나 쓰기 권한이 없으면 썸네일 생성하지 않음
    if(!(is_dir($target_path) && is_writable($target_path)))
        return '';

    // Animated GIF는 썸네일 생성하지 않음
    if($size[2] == 1) {
        if(is_animated_gif($source_file))
            return basename($source_file);
    }

    $ext = array(1 => 'gif', 2 => 'jpg', 3 => 'png');

    $thumb_filename = preg_replace("/\.[^\.]+$/i", "", $filename); // 확장자제거
    $thumb_file = "$target_path/thumb-{$thumb_filename}_{$thumb_width}x{$thumb_height}.".$ext[$size[2]];

    $thumb_time = @filemtime($thumb_file);
    $source_time = @filemtime($source_file);

    if (file_exists($thumb_file)) {
        if ($is_create == false && $source_time < $thumb_time) {
            return basename($thumb_file);
        }
    }

    // 원본파일의 GD 이미지 생성
    $src = null;
    $degree = 0;

    if ($size[2] == 1) {
        $src = @imagecreatefromgif($source_file);
        $src_transparency = @imagecolortransparent($src);
    } else if ($size[2] == 2) {
        $src = @imagecreatefromjpeg($source_file);

        if(function_exists('exif_read_data')) {
            // exif 정보를 기준으로 회전각도 구함
            $exif = @exif_read_data($source_file);
            if(!empty($exif['Orientation'])) {
                switch($exif['Orientation']) {
                    case 8:
                        $degree = 90;
                        break;
                    case 3:
                        $degree = 180;
                        break;
                    case 6:
                        $degree = -90;
                        break;
                }

                // 회전각도 있으면 이미지 회전
                if($degree) {
                    $src = imagerotate($src, $degree, 0);

                    // 세로사진의 경우 가로, 세로 값 바꿈
                    if($degree == 90 || $degree == -90) {
                        $tmp = $size;
                        $size[0] = $tmp[1];
                        $size[1] = $tmp[0];
                    }
                }
            }
        }
    } else if ($size[2] == 3) {
        $src = @imagecreatefrompng($source_file);
        @imagealphablending($src, true);
    } else {
        return;
    }

    if(!$src)
        return;

    $is_large = true;
    // width, height 설정
    if($thumb_width) {
        if(!$thumb_height) {
            $thumb_height = round(($thumb_width * $size[1]) / $size[0]);
        } else {
            if($size[0] < $thumb_width || $size[1] < $thumb_height)
                $is_large = false;
        }
    } else {
        if($thumb_height) {
            $thumb_width = round(($thumb_height * $size[0]) / $size[1]);
        }
    }

    $dst_x = 0;
    $dst_y = 0;
    $src_x = 0;
    $src_y = 0;
    $dst_w = $thumb_width;
    $dst_h = $thumb_height;
    $src_w = $size[0];
    $src_h = $size[1];

    $ratio = $dst_h / $dst_w;

    if($is_large) {
        // 크롭처리
        if($is_crop) {
            switch($crop_mode)
            {
                case 'center':
                    if($size[1] / $size[0] >= $ratio) {
                        $src_h = round($src_w * $ratio);
                        $src_y = round(($size[1] - $src_h) / 2);
                    } else {
                        $src_w = round($size[1] / $ratio);
                        $src_x = round(($size[0] - $src_w) / 2);
                    }
                    break;
                default:
                    if($size[1] / $size[0] >= $ratio) {
                        $src_h = round($src_w * $ratio);
                    } else {
                        $src_w = round($size[1] / $ratio);
                    }
                    break;
            }

            $dst = imagecreatetruecolor($dst_w, $dst_h);

            if($size[2] == 3) {
                imagealphablending($dst, false);
                imagesavealpha($dst, true);
            } else if($size[2] == 1) {
                $palletsize = imagecolorstotal($src);
                if($src_transparency >= 0 && $src_transparency < $palletsize) {
                    $transparent_color   = imagecolorsforindex($src, $src_transparency);
                    $current_transparent = imagecolorallocate($dst, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
                    imagefill($dst, 0, 0, $current_transparent);
                    imagecolortransparent($dst, $current_transparent);
                }
            }
        } else { // 비율에 맞게 생성
            $dst = imagecreatetruecolor($dst_w, $dst_h);
            $bgcolor = imagecolorallocate($dst, 255, 255, 255); // 배경색

            if($src_w > $src_h) {
                $tmp_h = round(($dst_w * $src_h) / $src_w);
                $dst_y = round(($dst_h - $tmp_h) / 2);
                $dst_h = $tmp_h;
            } else {
                $tmp_w = round(($dst_h * $src_w) / $src_h);
                $dst_x = round(($dst_w - $tmp_w) / 2);
                $dst_w = $tmp_w;
            }

            if($size[2] == 3) {
                $bgcolor = imagecolorallocatealpha($dst, 0, 0, 0, 127);
                imagefill($dst, 0, 0, $bgcolor);
                imagealphablending($dst, false);
                imagesavealpha($dst, true);
            } else if($size[2] == 1) {
                $palletsize = imagecolorstotal($src);
                if($src_transparency >= 0 && $src_transparency < $palletsize) {
                    $transparent_color   = imagecolorsforindex($src, $src_transparency);
                    $current_transparent = imagecolorallocate($dst, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
                    imagefill($dst, 0, 0, $current_transparent);
                    imagecolortransparent($dst, $current_transparent);
                } else {
                    imagefill($dst, 0, 0, $bgcolor);
                }
            } else {
                imagefill($dst, 0, 0, $bgcolor);
            }
        }
    } else {
        $dst = imagecreatetruecolor($dst_w, $dst_h);
        $bgcolor = imagecolorallocate($dst, 255, 255, 255); // 배경색

        if($src_w < $dst_w) {
            if($src_h >= $dst_h) {
                $dst_x = round(($dst_w - $src_w) / 2);
                $src_h = $dst_h;
            } else {
                $dst_x = round(($dst_w - $src_w) / 2);
                $dst_y = round(($dst_h - $src_h) / 2);
                $dst_w = $src_w;
                $dst_h = $src_h;
            }
        } else {
            if($src_h < $dst_h) {
                $dst_y = round(($dst_h - $src_h) / 2);
                $dst_h = $src_h;
                $src_w = $dst_w;
            }
        }

        if($size[2] == 3) {
            $bgcolor = imagecolorallocatealpha($dst, 0, 0, 0, 127);
            imagefill($dst, 0, 0, $bgcolor);
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
        } else if($size[2] == 1) {
            $palletsize = imagecolorstotal($src);
            if($src_transparency >= 0 && $src_transparency < $palletsize) {
                $transparent_color   = imagecolorsforindex($src, $src_transparency);
                $current_transparent = imagecolorallocate($dst, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
                imagefill($dst, 0, 0, $current_transparent);
                imagecolortransparent($dst, $current_transparent);
            } else {
                imagefill($dst, 0, 0, $bgcolor);
            }
        } else {
            imagefill($dst, 0, 0, $bgcolor);
        }
    }

    imagecopyresampled($dst, $src, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);

    if($size[2] == 1) {
        imagegif($dst, $thumb_file);
    } else if($size[2] == 3) {
        $png_compress = 5;
        imagepng($dst, $thumb_file, $png_compress);
    } else {
        $jpg_quality = 90;
        imagejpeg($dst, $thumb_file, $jpg_quality);
    }

    chmod($thumb_file, FILE_PERMISSION); // 추후 삭제를 위하여 파일모드 변경

    imagedestroy($src);
    imagedestroy($dst);

    return basename($thumb_file);
}
//------------------------------------------------------------------------------------------------------------------
function is_animated_gif($filename) {
    if(!($fh = @fopen($filename, 'rb')))
        return false;
    $count = 0;
    while(!feof($fh) && $count < 2) {
        $chunk = fread($fh, 1024 * 100); //read 100kb at a time
        $count += preg_match_all('#\x00\x21\xF9\x04.{4}\x00(\x2C|\x21)#s', $chunk, $matches);
    }

    fclose($fh);
    return $count > 1;
}
//------------------------------------------------------------------------------------------------------------------
function astxt($message, $arr='', $id='Error') {

    if($message && $arr && is_array($arr)) {
        $arr_cnt = count($arr);
        for($i=0; $i < $arr_cnt; $i++) {
            $message = preg_replace("/\[".$i."\:([^\]]*)\]/is", $arr[$i], $message);
        }
    }

    $message = ($message) ? $message : $id;

    return $message;
}

function lang($lang, $id, $arr='') {

    @include (GET_PATH.'/api/alert_'.$lang.'.php');

    $message = astxt($langs[$id], $arr, $id);

    return $message;
}


function getConfigValue($category, $key) {
  global $DB;
  $DB->where('category', $category);
  $DB->where('config_key', $key);
  $row = $DB->getOne('setup_config_t', 'config_value');

  return $row['config_value'] ?? null;
}


function lastVisitKorean($lastLoginTime) {
  $lastTime = new DateTime($lastLoginTime);
  $currentTime = new DateTime();

  $interval = $currentTime->diff($lastTime);

  $parts = [];

  if ($interval->d > 0) {
    $parts[] = $interval->d . '일';
  }
  if ($interval->h > 0) {
    $parts[] = $interval->h . '시간';
  }
  if ($interval->i > 0) {
    $parts[] = $interval->i . '분';
  }

  if (empty($parts)) {
    return '방금 전';
  } else {
    return implode(' ', $parts) . ' 전';
  }
}

if (!function_exists('get_client_ip')) {
  function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
      $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
      $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
      $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
      $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
      $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
      $ipaddress = getenv('REMOTE_ADDR');
    else
      $ipaddress = 'UNKNOWN';
    return $ipaddress;
  }
}




if (!function_exists('no_session_to_welcome')) {
  function no_session_to_welcome() {
    global $_SESSION, $CFG_LANG;
    if(!isset($_SESSION['user']['mt_idx'])){
      alert($CFG_LANG['common']['invalid_access'], "/auth/welcome.php");
    }

  }
}

if (!function_exists('is_session_to_mypage')) {
  function is_session_to_mypage() {
    global $_SESSION, $CFG_LANG;

    if(isset($_SESSION['user']['mt_idx'])){
      alert($CFG_LANG['common']['invalid_access'], "/mypage/dashboard.php");
    }

  }
}


if (!function_exists('checkUserDevice')) {
  /**
   * 사용자 디바이스 정보 확인 함수
   * @return array 디바이스 정보 (웹뷰 여부, iOS 여부, Android 여부)
   */
  function checkUserDevice()
  {
    $userAgent = $_SERVER['HTTP_USER_AGENT'];

    // 웹뷰 체크 (앱 내 웹뷰인지 확인)
    $isWebView = (
      strpos($userAgent, 'wv') !== false ||
      strpos($userAgent, 'FBAN') !== false ||
      strpos($userAgent, 'FBAV') !== false ||
      strpos($userAgent, 'Line') !== false ||
      strpos($userAgent, 'Instagram') !== false ||
      isset($_SERVER['HTTP_X_REQUESTED_WITH'])
    );

    // iOS 체크
    $isIOS = (
      strpos($userAgent, 'iPhone') !== false ||
      strpos($userAgent, 'iPad') !== false ||
      strpos($userAgent, 'iPod') !== false ||
      strpos($userAgent, 'Mac') !== false
    );

    // Android 체크
    $isAndroid = strpos($userAgent, 'Android') !== false;

    return [
      'isWebView' => $isWebView,
      'isIOS' => $isIOS,
      'isAndroid' => $isAndroid
    ];
  }
}

if (!function_exists('encryptData')) {
  function encryptData($plaintext)
  {
    $key = AES256_ENC_KEY;
    $iv = random_bytes(16); // AES-256-CBC는 16바이트 IV 필요
    $ciphertext = openssl_encrypt($plaintext, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
    return base64_encode($iv . $ciphertext);
  }
}

if (!function_exists('decryptData')) {
  function decryptData($encrypted)
  {
    $key = AES256_ENC_KEY;
    $data = base64_decode($encrypted);
    $iv = substr($data, 0, 16);
    $ciphertext = substr($data, 16);
    return openssl_decrypt($ciphertext, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
  }
}

if (!function_exists('cropImageWithRatio')) {


  function fixImageOrientation($filePath) {
    if (!function_exists('exif_read_data')) return;

    $exif = @exif_read_data($filePath);
    if (empty($exif['Orientation'])) return;

    $image = imagecreatefromjpeg($filePath);
    switch ($exif['Orientation']) {
      case 3:
        $image = imagerotate($image, 180, 0);
        break;
      case 6:
        $image = imagerotate($image, -90, 0);
        break;
      case 8:
        $image = imagerotate($image, 90, 0);
        break;
      default:
        return;
    }

    imagejpeg($image, $filePath, 90);
    imagedestroy($image);
  }


  function cropImageWithRatio($source_path, $output_path, $target_width, $target_height) {
    $image_info = getimagesize($source_path);
    if (!$image_info) {
      error_log("❌ getimagesize 실패");
      return false;
    }

    $orig_width = $image_info[0];
    $orig_height = $image_info[1];
    $type = $image_info[2];

    if ($orig_width > 5000 || $orig_height > 5000) {
      //error_log("❌ 이미지 해상도 너무 큼: {$orig_width}x{$orig_height}");
      return false;
    }

    if ($type == IMAGETYPE_JPEG) {
      fixImageOrientation($source_path);
    }


    switch ($type) {
      case IMAGETYPE_JPEG:
        $source_image = imagecreatefromjpeg($source_path);
        break;
      case IMAGETYPE_PNG:
        $source_image = imagecreatefrompng($source_path);
        break;
      case IMAGETYPE_GIF:
        $source_image = imagecreatefromgif($source_path);
        break;
      default:
        return false;
    }

    $target_ratio = $target_width / $target_height;
    $new_width = $orig_width;
    $new_height = $orig_width / $target_ratio;

    if ($new_height > $orig_height) {
      $new_height = $orig_height;
      $new_width = $orig_height * $target_ratio;
    }

    $src_x = max(0, ($orig_width - $new_width) / 2);
    $src_y = max(0, ($orig_height - $new_height) / 2);

    // 크롭한 영역 임시 이미지
    $cropped_image = imagecreatetruecolor($new_width, $new_height);

    imagecopy($cropped_image, $source_image, 0, 0, $src_x, $src_y, $new_width, $new_height);

    // 리사이즈된 최종 이미지
    $resized_image = imagecreatetruecolor($target_width, $target_height);

    // PNG 투명도 유지
    if ($type == IMAGETYPE_PNG) {
      imagealphablending($resized_image, false);
      imagesavealpha($resized_image, true);
      $transparent = imagecolorallocatealpha($resized_image, 255, 255, 255, 127);
      imagefilledrectangle($resized_image, 0, 0, $target_width, $target_height, $transparent);
    }

    // 크롭된 이미지를 리사이즈
    imagecopyresampled(
      $resized_image, $cropped_image,
      0, 0, 0, 0,
      $target_width, $target_height,
      $new_width, $new_height
    );

    // 저장
    switch ($type) {
      case IMAGETYPE_JPEG:
        imagejpeg($resized_image, $output_path, 100);
        break;
      case IMAGETYPE_PNG:
        imagepng($resized_image, $output_path, 0);
        break;
      case IMAGETYPE_GIF:
        imagegif($resized_image, $output_path);
        break;
      default:
        return false;
    }



    imagedestroy($source_image);
    imagedestroy($cropped_image);
    imagedestroy($resized_image);

    return true;
  }

}

if (!function_exists('getlanlng')) {

  function getlanlng($address){


    $query = urlencode($address);
    $url = 'https://naveropenapi.apigw.ntruss.com/map-geocode/v2/geocode?query='.$query;

    $client_id = getConfigValue('NAVER', 'CLIENT_ID');
    $client_secret = getConfigValue('NAVER', 'CLIENT_SECRET');

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      "X-NCP-APIGW-API-KEY-ID: $client_id",
      "X-NCP-APIGW-API-KEY: $client_secret"
    ));

    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);


    if ($err) {
      return [
        'success' => false,
        'error' => $err,
        'data' => null
      ];
    }

    $data = json_decode($response, true);
    return [
      'success' => true,
      'error' => null,
      'data' => $data
    ];

  }
}

if (!function_exists('renderStars')) {
  function renderStars($star)
  {
    $output = '<span class="pl-2 d-flex flex-row">';

    // 정수/실수 처리
    $fullStars = floor($star / 2);
    $isHalf = ($star / 2 - $fullStars) >= 0.5;

    for ($i = 0; $i < 5; $i++) {
      if ($i < $fullStars) {
        $output .= '<i class="fa fa-star text-warning me-1"></i>'; // 꽉 찬 별
      } elseif ($i === $fullStars && $isHalf) {
        $output .= '<i class="fa fa-star-half text-warning me-1"></i>'; // 반 별
      } else {
        $output .= '<i class="fa fa-star-o text-warning me-1"></i>'; // 빈 별
      }
    }

    $output .= '</span>';
    return $output;
  }
}

if (!function_exists('log_cron')) {
  function log_cron($message)
  {
    $base_log_dir = $_SERVER['DOCUMENT_ROOT'] . '/cron/log';
    if (!file_exists($base_log_dir)) {
      mkdir($base_log_dir, 0755, true);
    }

    // 현재 실행 중인 파일명 → auto_event_start, auto_event_end 등
    $filename = basename($_SERVER['SCRIPT_FILENAME'], '.php') . '.log';

    $log_path = $base_log_dir . '/' . $filename;

    // 로그 메시지 포맷
    $datetime = date('Y-m-d H:i:s');
    $line = "[{$datetime}] {$message}\n";

    file_put_contents($log_path, $line, FILE_APPEND);
  }
}

function showToast($message, $type = 'success') {
    ?>
    <style>
        .toast-container {
            position: fixed;
            width: 100%;
            bottom: 0;
            left: 0;
            z-index: 9999;
        }
        .toast {
            width: 90%;
            margin: 10px auto;
            padding: 12px 20px;
            border-radius: 6px;
            color: white;
            font-size: 14px;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.4s ease;
        }
        .toast.success { background-color: #28a745; }
        .toast.error { background-color: #dc3545; }
        .toast.warning { background-color: #ffc107; color: black; }
        .toast.show {
            opacity: 1;
            transform: translateY(0);
        }
    </style>

    <div class="toast-container" id="toastContainer"></div>

    <script>
        (function() {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = 'toast <?=$type?>';
            toast.innerText = <?=json_encode($message)?>;
            container.appendChild(toast);
            setTimeout(() => toast.classList.add('show'), 10);
            // setTimeout(() => {
            //     toast.classList.remove('show');
            //     setTimeout(() => toast.remove(), 400);
            // }, 3000);
        })();
    </script>
    <?php
}

function encrypt_member_id($mt_id) {
    // 실제 서비스에서는 config 에서 키/IV를 관리하는 것이 좋음
    $secretKey = 'CHANGE_ME_FIND_ID_KEY'; // 길게/복잡하게 바꿔주세요
    $secretIv  = 'CHANGE_ME_FIND_ID_IV';  // 16바이트 정도

    $key = hash('sha256', $secretKey, true);
    $iv  = substr(hash('sha256', $secretIv), 0, 16);

    $cipherText = openssl_encrypt($mt_id, 'AES-256-CBC', $key, 0, $iv);
    if ($cipherText === false) {
        return null;
    }

    // URL-safe Base64
    return strtr(base64_encode($cipherText), '+/=', '-_,');
}

function decrypt_member_id($token) {
    $secretKey = 'CHANGE_ME_FIND_ID_KEY';
    $secretIv  = 'CHANGE_ME_FIND_ID_IV';

    $key = hash('sha256', $secretKey, true);
    $iv  = substr(hash('sha256', $secretIv), 0, 16);

    $cipherText = base64_decode(strtr($token, '-_,', '+/='));
    if ($cipherText === false) {
        return null;
    }

    $plain = openssl_decrypt($cipherText, 'AES-256-CBC', $key, 0, $iv);
    return $plain === false ? null : $plain;
}

/**
 * 옵션 시그니처(같은 옵션 조합인지 비교용)
 */
function makeOptionSignatureFromRows(array $rows): string {
    $pairs = [];
    foreach ($rows as $r) {
        $oc = (int)($r['oc_idx'] ?? 0);
        $om = (int)($r['om_idx'] ?? 0);
        if ($oc > 0 && $om > 0) $pairs[] = $oc . ':' . $om;
    }
    sort($pairs); // 선택 순서 무관
    return md5(implode('|', $pairs));
}

function getCartOptionSignature($DB, int $ct_idx): string {
    $DB->where('ct_idx', $ct_idx);
    $rows = $DB->get('cart_options_t', null, ['oc_idx','om_idx']);
    return makeOptionSignatureFromRows($rows ?: []);
}

/**
 * ct_ids 기준으로 cart, options 삭제 (FK 때문에 options 먼저)
 */
function clearCartByCtIds($DB, array $ctIds): void {
    $ctIds = array_values(array_filter(array_map('intval', $ctIds)));
    if (empty($ctIds)) return;

    $DB->where('ct_idx', $ctIds, 'IN');
    $DB->delete('cart_options_t');

    $DB->where('idx', $ctIds, 'IN');
    $DB->delete('cart_t');
}

/**
 * 회원 장바구니 삭제 (매장 1개 정책)
 */
function clearCartByMember($DB, int $mt_idx): void {
    if ($mt_idx <= 0) return;

    $DB->where('mt_idx', $mt_idx);
    $rows = $DB->get('cart_t', null, ['idx']);
    $ctIds = array_map(function($r){ return (int)$r['idx']; }, $rows ?: []);
    clearCartByCtIds($DB, $ctIds);
}

/**
 * (세션 없을 때) 현재 장바구니 매장 st_id를 추정
 * - 회원이면 cart_t에서 mt_idx 기준으로 st_id 하나 가져오기
 * - 비회원이면 세션 ct_ids로 cart_t 조회 후 st_id 하나 가져오기
 */
function detectCurrentStoreId($DB, int $mt_idx): int {
    if ($mt_idx > 0) {
        $DB->where('mt_idx', $mt_idx);
        $row = $DB->getOne('cart_t', 'st_id');
        return (int)($row['st_id'] ?? 0);
    }
    $ctIds = $_SESSION['cart_ct_ids'] ?? [];
    $ctIds = array_values(array_filter(array_map('intval', $ctIds)));
    if (!empty($ctIds)) {
        $DB->where('idx', $ctIds, 'IN');
        $row = $DB->getOne('cart_t', 'st_id');
        return (int)($row['st_id'] ?? 0);
    }
    return 0;
}


// 환불 함수
//function cancelPortonePayment(string $paymentId, string $reason = '고객 요청'): array {
//    $ch = curl_init();
//
//    curl_setopt_array($ch, [
//        CURLOPT_URL => 'https://api.portone.io/payments/' . rawurlencode($paymentId) . '/cancel',
//        CURLOPT_POST => true,
//        CURLOPT_HTTPHEADER => [
//            'Content-Type: application/json',
//            'Authorization: PortOne ' . PORTONE_API_SECRET,
//        ],
//        CURLOPT_POSTFIELDS => json_encode([
//            'reason' => $reason,
//        ], JSON_UNESCAPED_UNICODE),
//        CURLOPT_RETURNTRANSFER => true,
//        CURLOPT_TIMEOUT => 30,
//    ]);
//
//    $response = curl_exec($ch);
//    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//    $err = curl_error($ch);
//    curl_close($ch);
//
//    if ($err) throw new Exception('포트원 취소 통신 실패: ' . $err);
//    if ($httpCode < 200 || $httpCode >= 300) {
//        throw new Exception("포트원 취소 실패 ({$httpCode}): " . $response);
//    }
//
//    $data = json_decode($response, true);
//    if (!$data) throw new Exception('포트원 취소 응답 파싱 실패: ' . $response);
//
//    return $data;
//}

/**
 * 포트원 결제 취소 (전액/부분 모두 지원) 추후 환불 되는거 확인 후 적용
 *
 * @param string $paymentId     포트원 결제 ID (imp_uid 또는 paymentId)
 * @param float  $amount        취소할 금액 (부분환불 시 입력, 전액은 생략 또는 전체 금액)
 * @param string $reason        취소 사유 (필수)
 * @return array                포트원 응답 데이터
 * @throws Exception
 * $result = cancelPortonePayment('payment_abc123', reason: '고객 요청에 의한 전체 취소');
 * echo "전액 취소 완료: " . $result['cancelledAmount'] . "원\n";
 *
 * // 2. 부분환불 (예: 10,000원만 환불)
 * $result = cancelPortonePayment('payment_abc123', amount: 10000, reason: '일부 상품 불량');
 * echo "부분환불 완료: " . $result['cancelledAmount'] . "원\n";
 */
function cancelPortonePayment(string $paymentId, string $reason = '고객 요청', float $amount = 0): array
{
    // 누락 방지용 안전장치: logPayment가 없으면 임시 정의
    if (!function_exists('logPayment')) {
        function logPayment($level, $message, $context = []) {
            $line = sprintf(
                "[%s] [%s] %s | %s\n",
                date('Y-m-d H:i:s'),
                strtoupper($level),
                $message,
                json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            );
            $logDir = $_SERVER['DOCUMENT_ROOT'] . '/_logs';
            if (!is_dir($logDir)) @mkdir($logDir, 0755, true);
            @file_put_contents($logDir . '/payment.log', $line, FILE_APPEND);
        }
    }

    $payload = [
        'reason' => $reason,
    ];

    if ($amount > 0) {
        $payload['amount'] = $amount;
    }

    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL            => 'https://api.portone.io/payments/' . rawurlencode($paymentId) . '/cancel',
        CURLOPT_POST           => true,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'Authorization: PortOne ' . PORTONE_API_SECRET,
        ],
        CURLOPT_POSTFIELDS     => json_encode($payload, JSON_UNESCAPED_UNICODE),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_SSL_VERIFYPEER => false,  // 운영 환경에서는 true + 인증서 확인
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_FOLLOWLOCATION => true,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr  = curl_error($ch);
    curl_close($ch);

    if ($curlErr) {
        logPayment('ERROR', '포트원 취소 curl 오류', [
            'paymentId' => $paymentId,
            'curl_error' => $curlErr,
            'http_code' => $httpCode,
        ]);
        throw new Exception('포트원 취소 통신 실패: ' . $curlErr);
    }

    if ($httpCode < 200 || $httpCode >= 300) {
        logPayment('ERROR', '포트원 취소 HTTP 에러', [
            'paymentId' => $paymentId,
            'http_code' => $httpCode,
            'response'  => $response,
        ]);
        throw new Exception("포트원 취소 실패 (HTTP {$httpCode}): " . $response);
    }

    $data = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
        logPayment('ERROR', '포트원 취소 응답 파싱 실패', [
            'paymentId' => $paymentId,
            'response'  => $response,
        ]);
        throw new Exception('포트원 취소 응답 파싱 실패: ' . $response);
    }

    logPayment('INFO', '포트원 취소 성공', [
        'paymentId' => $paymentId,
        'reason'    => $reason,
        'amount'    => $amount,
        'response'  => $data,
    ]);

    return $data;
}
