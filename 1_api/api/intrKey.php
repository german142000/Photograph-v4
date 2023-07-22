<?php
$uskey = false;
$errno = "no errors";
include($_SERVER['DOCUMENT_ROOT']."/1_api/config/config.php");
$api = $config['apiSrever'];
$sc = $config['clientServer'];
if(!isset($ss)) {$errno = "user session false";}
if(!isset($st)) {$errno = "user session false";}
if(!isset($ui)) {$errno = "user session false";}
if(!isset($nk)) {$errno = "user session false";}
if(!isset($ts)) {$errno = "trusted false";}
if(!isset($tr)) {$errno = "trusted false";}

if(isset($ss) && isset($st) && isset($ui) && isset($nk) && isset($ts) && isset($tr)) {
	include($_SERVER["DOCUMENT_ROOT"]."/1_api/libs/aes_fast.php");

	$ss = json_decode($ss);
	$st = json_decode($st);
	$ui = json_decode($ui);
	$nk = json_decode($nk);
	$ts = json_decode($ts);
	$tr = json_decode($tr);

	$key = fopen($_SERVER["DOCUMENT_ROOT"] . "/1_api/scert/cert/key.txt", "r");
	$code = explode('.', bcmod(bcpow((string) $mx, (string) fgets($key), 1000), $pr, 1))[0];
	fclose($key);

	$scode = substr(md5($code), 0, 16);

	$aes = new AES();
	$iv = json_decode($iv);

	$ss = $aes -> decrypt($ss, count($ss), 
						$aes::modeOfOperation_OFB, str_split($scode), 16, $iv);

	$st = $aes -> decrypt($st, count($st), 
						$aes::modeOfOperation_OFB, str_split($scode), 16, $iv);

	$ui = $aes -> decrypt($ui, count($ui), 
						$aes::modeOfOperation_OFB, str_split($scode), 16, $iv);

	$nk = $aes -> decrypt($nk, count($nk), 
						$aes::modeOfOperation_OFB, str_split($scode), 16, $iv);

	$ts = $aes -> decrypt($ts, count($ts), 
						$aes::modeOfOperation_OFB, str_split($scode), 16, $iv);

	$tr = $aes -> decrypt($tr, count($tr), 
						$aes::modeOfOperation_OFB, str_split($scode), 16, $iv);
	
	$ssstr = [];
	$ststr = [];
	$uistr = [];
	$nkstr = [];
	$tsstr = [];
	$trstr = [];

	for($i = 0; $i < count($ss); $i++){
		array_push($ssstr, mb_chr($ss[$i], 'UTF-8'));
	}

	for($i = 0; $i < count($st); $i++){
		array_push($ststr, mb_chr($st[$i], 'UTF-8'));
	}

	for($i = 0; $i < count($ui); $i++){
		array_push($uistr, mb_chr($ui[$i], 'UTF-8'));
	}

	for($i = 0; $i < count($nk); $i++){
		array_push($nkstr, mb_chr($nk[$i], 'UTF-8'));
	}

	for($i = 0; $i < count($ts); $i++){
		array_push($tsstr, mb_chr($ts[$i], 'UTF-8'));
	}

	for($i = 0; $i < count($tr); $i++){
		array_push($trstr, mb_chr($tr[$i], 'UTF-8'));
	}

	$ss = implode($ssstr);
	$st = implode($ststr);
	$ui = implode($uistr);
	$nk = implode($nkstr);
	$ts = implode($tsstr);
	$tr = implode($trstr);

	$key = fopen($_SERVER["DOCUMENT_ROOT"] . "/1_api/scert/cert/key.txt", "r");
	$scode = substr(md5(fgets($key)), 0, 16);
	fclose($key);

	$aescert = $aes -> decrypt(json_decode($ss), 32, 
						$aes::modeOfOperation_OFB, str_split($scode), 16, json_decode($st));

	$cid = [];

	for ($i = 0; $i < 32; $i++)
	{
	    array_push($cid, mb_chr($aescert[$i], 'UTF-8'));
	}

	$ukey = implode('', $cid).".txt";

	if(!file_exists($_SERVER['DOCUMENT_ROOT']."/1_api/users/".$ui."/keys/".$ukey)) {
		$errno = "user session false";
		//echo "<script> window.parent.postMessage('userSessionFalse') </script>"; exit(0);
	} else {
		$ufkey = fopen($_SERVER['DOCUMENT_ROOT']."/1_api/users/".$ui."/keys/".$ukey, "r");
		$trsf = fgets($ufkey);
		fclose($ufkey);
		$trs1 = $trsf;
		$trs2 = $ts;
		if(json_decode($trsf) == json_decode($ts)) {
			$uskey = true;
		} else {
			$errno = "trusted false";
		}
	}
}
?>