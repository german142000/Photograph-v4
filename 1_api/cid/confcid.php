<?php
ini_set("display_errors", 0);
ini_set("display_startup_errors", 0);
error_reporting(E_ALL);

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

$aescert = $aes -> decrypt(json_decode($_GET['1']), 32, 
						$aes::modeOfOperation_OFB, str_split($scode), 16, json_decode($_GET['2']));

$cid = [];

for ($i = 0; $i < 32; $i++)
{
    array_push($cid, mb_chr($aescert[$i], 'UTF-8'));
}

$cid = implode('', $cid).".cid";

$dir = scandir($_SERVER["DOCUMENT_ROOT"]."/1_api/cid/cids/");
array_push($dir, "end");
$r = false;

for($i = 2; $i < count($dir); $i++) {
	if($dir[$i] == $cid) {
		$r = true;
	}
}

if($r) echo "true";
else echo "false";
?>