<?php
include($_SERVER['DOCUMENT_ROOT']."/1_api/config/config.php");
$api = $config['apiSrever'];
$sc = $config['clientServer'];

include($_SERVER["DOCUMENT_ROOT"]."/1_api/libs/aes_fast.php");
$pr = $_GET['1'];
$mx = $_GET['2'];
$iv = json_decode($_GET['3']);
$ui = json_decode($_GET['4']);

$key = fopen($_SERVER["DOCUMENT_ROOT"] . "/1_api/scert/cert/key.txt", "r");
$code = explode('.', bcmod(bcpow((string) $mx, (string) fgets($key), 1000), $pr, 1))[0];
fclose($key);

$scode = substr(md5($code), 0, 16);

$aes = new AES();

$ui = $aes -> decrypt($ui, count($ui), 
						$aes::modeOfOperation_OFB, str_split($scode), 16, $iv);

$uistr = [];

for($i = 0; $i < count($ui); $i++){
	array_push($uistr, mb_chr($ui[$i], 'UTF-8'));
}

$ui = implode($uistr);

$df = file_get_contents($_SERVER["DOCUMENT_ROOT"]."/1_api/users/$ui/csm/dialogs.csm");
$df = explode("\n", $df);

if(count($df) == 1) echo "202";
else {
	unlink($_SERVER["DOCUMENT_ROOT"]."/1_api/users/$ui/csm/dialogs.csm");
	file_put_contents($_SERVER["DOCUMENT_ROOT"]."/1_api/users/$ui/csm/dialogs.csm", "");
	echo "203\n", json_encode($df);
}
?>