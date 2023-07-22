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
	$gkey = md5($key1.$key2);
	file_put_contents($_SERVER['DOCUMENT_ROOT']."/1_api/users/$usr1/dialogs/$usr2.dlg", $gkey);
	file_put_contents($_SERVER['DOCUMENT_ROOT']."/1_api/users/$usr2/dialogs/$usr1.dlg", $gkey);
	$mysql = mysqli_connect("sql104.unaux.com", "unaux_33957416", "m9c9q8uz79", "unaux_33957416_apiphotogp");
	if(mysqli_connect_error() == null) {
		if($usr1 > $usr2)
		$did = md5($usr1."_".$usr2);
		else $did = md5($usr2."_".$usr1);
		$query = <<<TBL
		CREATE TABLE dialog_$did (
			id VARCHAR(10000000),
			photo_id TEXT,
			date TEXT,
			data TEXT
		)
TBL;
		mysqli_query($mysql, $query);
	}
	echo $errno;
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