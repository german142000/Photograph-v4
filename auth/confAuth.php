<?php
header("Access-Control-Allow-Origin: *");
error_reporting(E_ALL);

if($_GET['api'] == '1') {
    
    include($_SERVER["DOCUMENT_ROOT"]."/libs/svsp-number-generator.php");
    
    $smNum = primeRandom(16, false);
    $root = rand(3, 20);

    $mix = explode(".", bcmod(bcpow((string) $root, '3678', 1000), (string) $smNum, 1))[0];

    echo $smNum."\n", $root."\n", $mix."\n";
    
} else if($_GET['api'] == '2') {
    
    include($_SERVER["DOCUMENT_ROOT"]."/libs/aes_fast.php");
    
    $code = explode('.', bcmod(bcpow((string) $_GET['1'], '3678', 1000), (string) $_GET['7'], 1))[0];
    $scode = substr(md5($code), 0, 16);
    
    $ss = json_decode($_GET['2']);
    $st = json_decode($_GET['3']);
    $ui = json_decode($_GET['4']);
    $nc = json_decode($_GET['5']);
    $iv = json_decode($_GET['6']);
    
    $aes = new AES();

	$ss = $aes -> decrypt($ss, count($ss), 
						$aes::modeOfOperation_OFB, str_split($scode), 16, $iv);
						
	$st = $aes -> decrypt($st, count($st), 
						$aes::modeOfOperation_OFB, str_split($scode), 16, $iv);
						
	$ui = $aes -> decrypt($ui, count($ui), 
						$aes::modeOfOperation_OFB, str_split($scode), 16, $iv);
						
	$nc = $aes -> decrypt($nc, count($nc), 
						$aes::modeOfOperation_OFB, str_split($scode), 16, $iv);
						
	$ssstr = [];
	$ststr = [];
	$uistr = [];
	$nkstr = [];
	
	for($i = 0; $i < count($ss); $i++){
		array_push($ssstr, mb_chr($ss[$i], 'UTF-8'));
	}

	for($i = 0; $i < count($st); $i++){
		array_push($ststr, mb_chr($st[$i], 'UTF-8'));
	}

	for($i = 0; $i < count($ui); $i++){
		array_push($uistr, mb_chr($ui[$i], 'UTF-8'));
	}

	for($i = 0; $i < count($nc); $i++){
		array_push($nkstr, mb_chr($nc[$i], 'UTF-8'));
	}
	
	$ss = implode($ssstr);
	$st = implode($ststr);
	$ui = implode($uistr);
	$nk = implode($nkstr);
	
	setcookie('nickname', $nk, time()+60*60*24*30, '/');
	setcookie('uid', $ui, time()+60*60*24*30, '/');
	setcookie('session', $ss, time()+60*60*24*30, '/');
	setcookie('strs', $st, time()+60*60*24*30, '/');
	
	echo "<script> location.href = '/main' </script>";
}
?>