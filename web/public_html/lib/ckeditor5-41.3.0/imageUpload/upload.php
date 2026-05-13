<?php
require_once("config.php");

// 업로드 DIALOG 에서 전송된 값
$funcNum = $_GET['CKEditorFuncNum'];
$CKEditor = $_GET['CKEditor'];
$langCode = $_GET['langCode'];

$tempfile = $_FILES['upload']['tmp_name'];
$filename = $_FILES['upload']['name'];


$size = getimagesize($_FILES['upload']['tmp_name']);
$extensions = array(1 => 'gif', 2 => 'jpg', 3 => 'png');
$file_ext = $extensions[$size[2]]; // 파일 확장자
if (!$file_ext) exit;

//$size = @getimagesize($source_file);
$type = substr($filename, strrpos($filename, ".")+1);
$found = false;
switch ($type) {
    case "jpg":
    case "jpeg":
    case "gif":
    case "png":
        $found = true;
}

if ($found != true) {
    exit;
}

// 저장 파일 이름: 년월일시분초_렌덤문자
// 20140327125959_abcdefghi.jpg

//$filename = "KakaoTalk_Photo_2023-08-14-14-31.jpg";
$filename = cke_replace_filename($filename);
$save_file = SAVE_DIR . '/' . $filename;
$save_url = SAVE_URL . '/' . $filename;


move_uploaded_file($tempfile, $save_file);
$imgsize = getimagesize($save_file);

/*
    $width = $info[0];
    $height = $info[1];

    if ($info['mime'] == 'image/jpeg')
        $image = imagecreatefromjpeg($file);

    elseif ($info['mime'] == 'image/gif')
        $image = imagecreatefromgif($file);

    elseif ($info['mime'] == 'image/png')
        $image = imagecreatefrompng($file);

    $dst = imagecreatetruecolor($w, $h);
    imagecopyresampled($dst, $image, 0, 0, 0, 0, $w, $h, $width, $height);
	$imgsize = imagejpeg($dst, $destination, $quality);
*/
$filesize = filesize($save_file);

if (!$imgsize) {
    $filesize = 0;
    $random_name = '-ERR';
    unlink($save_file);
};

/*
try {
    if(defined('FILE_PERMISSION')) chmod($save_file, DATA_FILE_PERMISSION);
} catch (Exception $e) {

}
*/
/*echo "<script>window.parent.CKEDITOR.tools.callFunction($funcNum, '$save_url', '업로드완료');</script>";*/

$json = array(
"uploaded"=> 1
,"fileName"=> basename($save_url)
,"url"=> $save_url);

echo json_encode($json);
?>