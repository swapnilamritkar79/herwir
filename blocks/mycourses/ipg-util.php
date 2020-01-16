<?php

date_default_timezone_set('Europe/London');
$dateTime= date('Y:m:d-H:i:s');
// 4035871111114977
function getDateTime() {
 global $dateTime;
 return $dateTime;
}
function createHash($chargetotal, $currency) {
 $storename = "2220540392";
 $sharedSecret = "Qz7>p3-cvb";
 $stringToHash = $storename . getDateTime() . $chargetotal . $currency . $sharedSecret;
 $ascii = bin2hex($stringToHash);
 return hash('sha256',$ascii);
}
?>