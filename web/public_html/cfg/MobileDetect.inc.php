<?php

$detect_mobile = new \Detection\MobileDetect();
if ($detect_mobile->isMobile()) {
    $chk_mobile = true;
} else {
    $chk_mobile = false;
}