<?php
$ss = $_GET['3'];
$st = $_GET['4'];
$ui = $_GET['5'];
$nk = $_GET['6'];
$ts = $_GET['7'];
$tr = $_GET['8'];
$pr = $_GET['9'];
$mx = $_GET['10'];
$iv = $_GET['11'];
include($_SERVER['DOCUMENT_ROOT']."/1_api/api/intrKey.php");
if($uskey) {
	$usr1 = $_GET['1'];
	$usr2 = $_GET['2'];
	$key1 = fgets(fopen($_SERVER["DOCUMENT_ROOT"]."/1_api/users/$usr1/userkey.txt", "r"));
	$key2 = fgets(fopen($_SERVER["DOCUMENT_ROOT"]."/1_api/users/$usr2/userkey.txt", "r"));
	$key1arr = str_split($key1);
	$key2arr = str_split($key2);
	$key1i = 0;
	$key2i = 0;
	for($i = 0; $i < count($key1arr); $i++) $key1i += mb_ord($key1arr[$i]);
	for($i = 0; $i < count($key2arr); $i++) $key2i += mb_ord($key2arr[$i]);
	$gkeyi = bcadd($key1i, $key2i, 0);
	$gkey = md5($gkeyi);
	$mysql = mysqli_connect("sql104.unaux.com", "unaux_33957416", "m9c9q8uz79", "unaux_33957416_apiphotogp");
	if(mysqli_connect_error() == null) {
		$time = time();
		$mess = json_decode($_GET['12']);
		$mix = $_GET['13'];
		$prime = $_GET['14'];
		$iv = json_decode($_GET['15']);
		if($usr1 > $usr2)
		$did = md5($usr1."_".$usr2);
		else $did = md5($usr2."_".$usr1);
		$key = fopen($_SERVER["DOCUMENT_ROOT"] . "/1_api/scert/cert/key.txt", "r");
		$code = explode('.', bcmod(bcpow((string) $mix, (string) fgets($key), 1000), $prime, 1))[0];
		fclose($key);
		$scode = substr(md5($code), 0, 16);
	
		$aes = new AES();

		$mess = $aes -> decrypt($mess, count($mess), 
						$aes::modeOfOperation_OFB, str_split($scode), 16, $iv);
	
		$query = <<<TBL1
		  SELECT count(*) FROM dialog_$did
TBL1;
    	$row = mysqli_fetch_row(mysqli_query($mysql, $query))[0];
		$row = bcadd($row, 1, 0);
	
		if(bcmod($row, 2, 1) == 0) $scode = substr(md5($gkey), 0, 16);
		else $scode = substr(md5($gkey), 16, 16);
	
		$scode = md5($scode.$time, true);
	
		$encrypt_massage = $aes -> encrypt($mess, $aes::modeOfOperation_OFB, str_split($scode), 16, $iv);
	
		$data = json_encode($encrypt_massage['cipher']).'b'.json_encode($iv);
	
		$query = <<<TBL2
		  		INSERT dialog_$did (id, photo_id, date, data)
		  		VALUES ('$row', '$ui', '$time', '$data')
TBL2;
		if(mysqli_query($mysql, $query) != false) {
	
		file_put_contents($_SERVER["DOCUMENT_ROOT"]."/1_api/users/$usr2/csm/dialogs.csm", $ui."\n", FILE_APPEND);
	
		$errno = "message send";
		echo "201\n", $errno;
		
		} else {
		echo "send error: ".mysqli_error($mysql);
		}
	}
} else {
	if($errno == "user session false") {
		setcookie('nickname', null, -1, '/');
		setcookie('uid', null, -1, '/');
		setcookie('session', null, -1, '/');
		setcookie('strs', null, -1, '/');
		echo "101\n", $errno; //code 101 - ошибка сессии пользователя
		exit(0);
	}
	if($errno == "trusted false") {
		setcookie('trusted', null, -1, '/');
		setcookie('trs', null, -1, '/');
		echo "102\n", $errno; //code 102 - ошибка доверенного домена
		exit(0);
	}
}
?>