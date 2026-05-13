<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require $_SERVER['DOCUMENT_ROOT']."/vendor/autoload.php";
//require '../vendor/autoload.php';


function sendMailer($from, $fromname,$to,$toname,$title,$msg){
  global $CFG_LANG;

  $mail = new PHPMailer(true);
  $result = [];
  try {
    //Server settings
    $mail->CharSet = 'UTF-8';
    // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = getConfigValue('NAVER', 'MAIL_HOST');                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = getConfigValue('NAVER', 'MAIL_USERNAME');                      //SMTP username
    $mail->Password   = getConfigValue('NAVER', 'MAIL_PASSWORD');                              //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port       = getConfigValue('NAVER', 'MAIL_PORT');                  //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom($from, $fromname);
    $mail->addAddress($to, $toname);

    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = $title;
    $mail->Body    = $msg;

    $mail->send();
    $result['status'] = 'ok';
    $result['message'] = $CFG_LANG['common']['sent_success'];
  } catch (Exception $e) {
    $result['status'] = 'fail';
    $result['message'] = $mail->ErrorInfo;
  }
  return $result;

}
