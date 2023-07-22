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
	$sid = $_GET['1'];
	$nmr = $_GET['2'];
	$key1 = fgets(fopen($_SERVER["DOCUMENT_ROOT"]."/1_api/users/$ui/userkey.txt", "r"));
	$key2 = fgets(fopen($_SERVER["DOCUMENT_ROOT"]."/1_api/users/$sid/userkey.txt", "r"));
	$key1arr = str_split($key1);
	$key2arr = str_split($key2);
	$key1i = 0;
	$key2i = 0;
	for($i = 0; $i < count($key1arr); $i++) $key1i += mb_ord($key1arr[$i]);
	for($i = 0; $i < count($key2arr); $i++) $key2i += mb_ord($key2arr[$i]);
	$gkeyi = bcadd($key1i, $key2i, 0);
	$gkey = md5($gkeyi);
	if($ui > $sid)
	$did = md5($ui."_".$sid);
	else $did = md5($sid."_".$ui);
	$mysql = mysqli_connect("sql104.unaux.com", "unaux_33957416", "m9c9q8uz79", "unaux_33957416_apiphotogp");
	if(mysqli_connect_error() == null) {
	
		$encData = [];
		
		$query = <<<TBL
		SELECT data FROM dialog_$did LIMIT $nmr, 10000000
TBL;
		$dataArray = mysqli_fetch_all(mysqli_query($mysql, $query));
		//print_r($dataArray);
	
		$query = <<<TBL
		SELECT photo_id FROM dialog_$did LIMIT $nmr, 10000000
TBL;
		$pidArray = mysqli_fetch_all(mysqli_query($mysql, $query));
		//print_r($pidArray);
	
		$query = <<<TBL
		SELECT date FROM dialog_$did LIMIT $nmr, 10000000
TBL;
		$dateArray = mysqli_fetch_all(mysqli_query($mysql, $query));
		//print_r($dateArray);
	
		for($i = 0; $i < count($dataArray); $i++) {
			$expstr = explode('b', $dataArray[$i][0]);
			
			if(bcmod($nmr + $i + 1, 2, 1) == 0) $scode = substr(md5($gkey), 0, 16);
			else $scode = substr(md5($gkey), 16, 16);
		
			$scode = md5($scode.$dateArray[$i][0], true);
		
			$dmess = $aes -> decrypt(json_decode($expstr[0]), count(json_decode($expstr[0])), 
						$aes::modeOfOperation_OFB, str_split($scode), 16, json_decode($expstr[1]));
		
			$dmessarr = [];
			
			for($g = 0; $g < count($dmess); $g++){
				array_push($dmessarr, mb_chr($dmess[$g], 'UTF-8'));
			}
		
			$dmess = implode($dmessarr);
			array_push($encData, $dmess);
		}
		
		$ertpid = [];
		for($i = 0; $i < count($pidArray); $i++) array_push($ertpid, $pidArray[$i][0]);
	
		$ertdate = [];
		for($i = 0; $i < count($dateArray); $i++) array_push($ertdate, $dateArray[$i][0]);
	
		$encDataJ = json_encode($encData);
		$ertpidJ = json_encode($ertpid);
		$ertdateJ = json_encode($ertdate);
	
		$key = fopen($_SERVER["DOCUMENT_ROOT"] . "/1_api/scert/cert/key.txt", "r");
		$code = explode('.', bcmod(bcpow((string) $mx, (string) fgets($key), 1000), $pr, 1))[0];
		fclose($key);

		$scode = substr(md5($code), 0, 16);
	
		$encDataJarr = [];
		$dja = str_split($encDataJ);
		for($i = 0; $i < count($dja); $i++) {
			array_push($encDataJarr, ord($dja[$i]));
		}
	
		$encIdJarr = [];
		$ija = str_split($ertpidJ);
		for($i = 0; $i < count($ija); $i++) {
			array_push($encIdJarr, ord($ija[$i]));
		}
	
		$encDdJarr = [];
		$ddja = str_split($ertdateJ);
		for($i = 0; $i < count($ddja); $i++) {
			array_push($encDdJarr, ord($ddja[$i]));
		}
		
		$encrypt_data = $aes -> encrypt($encDataJarr, $aes::modeOfOperation_OFB, str_split($scode), 16, $iv);
		$encrypt_id = $aes -> encrypt($encIdJarr, $aes::modeOfOperation_OFB, str_split($scode), 16, $iv);
		$encrypt_date = $aes -> encrypt($encDdJarr, $aes::modeOfOperation_OFB, str_split($scode), 16, $iv);
	
		echo json_encode($encrypt_data['cipher'])."\n", 
			 json_encode($encrypt_id['cipher'])."\n", 
			 json_encode($encrypt_date['cipher'])."\n";
	
		//print_r($encData);
		//print_r($ertpid);
		//print_r($ertdate);
	
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