<?php
ini_set("display_errors", 0);
ini_set("display_startup_errors", 0);
error_reporting(E_ALL);
include($_SERVER['DOCUMENT_ROOT']."/config/config.php");
include($_SERVER["DOCUMENT_ROOT"]."/libs/aes_fast.php");
$rmt = file_get_contents($_SERVER['DOCUMENT_ROOT']."/1_api/api/api.php");
$rmt = explode("\n", $rmt);

$smNum = $rmt[0];
$root = $rmt[1];
$serverMix = $rmt[2];

$secret = rand(1000, 9999);
$mix = explode(".", bcmod(bcpow((string)$root, (string) $secret, 1000), (string)$smNum, 1))[0];
$code = explode('.', bcmod(bcpow((string) $serverMix, (string) $secret, 1000), $smNum, 1))[0];

$scode = substr(md5($code), 0, 16);
	
$iv = array(rand(1, 9), rand(1, 9), rand(1, 9), rand(1, 9),
			rand(1, 9), rand(1, 9), rand(1, 9), rand(1, 9),
			rand(1, 9), rand(1, 9), rand(1, 9), rand(1, 9),
			rand(1, 9), rand(1, 9), rand(1, 9), rand(1, 9));
	
$aes = new AES();
	
$certarr = [];
$cid = str_split($_COOKIE['uid']);
for($i = 0; $i < count($cid); $i++) {
	array_push($certarr, ord($cid[$i]));
}

$aescert = $aes -> encrypt($certarr, $aes::modeOfOperation_OFB, str_split($scode), 16, $iv);
	    
$aescid = json_encode($aescert["cipher"]);
	
$iv = json_encode($iv);
	
$req = file_get_contents("http://photogp.unaux.com/1_api/api/csm.php?1=$smNum&2=$mix&3=$iv&4=$aescid");
$req = explode("\n", $req);
if($req[1] == "202") echo "202";
else print_r($req[2]);
?>