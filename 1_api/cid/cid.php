<?php
ini_set("display_errors", 0);
ini_set("display_startup_errors", 0);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");

$ip = $_SERVER['REMOTE_ADDR'];
$time = time();
$rand = rand(0, 999999);
$cid = $ip + (string) $time + (string) $rand;
$cid = md5($cid);
file_put_contents($_SERVER['DOCUMENT_ROOT']."/1_api/cid/cids/".$cid.".cid", $cid);

include($_SERVER["DOCUMENT_ROOT"]."/1_api/libs/aes_fast.php");

$key = fopen($_SERVER["DOCUMENT_ROOT"] . "/1_api/scert/cert/key.txt", "r");
$scode = substr(md5(fgets($key)), 0, 16);
fclose($key);

$certarr = [];
$cid = str_split($cid);
for($i = 0; $i < count($cid); $i++) {
	array_push($certarr, ord($cid[$i]));
}

$aes = new AES();

$aescert = $aes -> encrypt($certarr, $aes::modeOfOperation_OFB, str_split($scode), 16, json_decode($_GET['1']));
	    
$aescid = json_encode($aescert["cipher"]);

echo $aescid."\n";

?>