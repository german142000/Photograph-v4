<link rel='stylesheet' href='/style/style.css'>
<?php
include($_SERVER['DOCUMENT_ROOT']."/1_api/libs/aes_fast.php");

$aesuser = $_POST['aesuser'];
$iv = json_decode($_POST['iv']);

$key = fopen($_SERVER["DOCUMENT_ROOT"] . "/1_api/scert/cert/key.txt", "r");
$code = explode('.', bcmod(bcpow((string) $_POST['mix'], (string) fgets($key), 1000), 
				 $_POST['prime'], 1))[0];
fclose($key);

$scode = substr(md5($code), 0, 16);

$aes = new AES();

$user = $aes -> decrypt(json_decode($aesuser), $_POST['laesuser'], 
						$aes::modeOfOperation_OFB, str_split($scode), 16, $iv);

$userstr = [];
$uid = $_POST['uid'];
$lk = $_POST['rurl'];

for($i = 0; $i < count($user); $i++){
array_push($userstr, mb_chr($user[$i], 'UTF-8'));
}

$userstri = implode($userstr);

$query = <<<TBL
UPDATE users SET nickname = '$userstri' WHERE photo_id = '$uid';
TBL;

$mysql = mysqli_connect("sql104.unaux.com", "unaux_33957416", "m9c9q8uz79", "unaux_33957416_apiphotogp");
if(mysqli_connect_error() == null) {
	mysqli_query($mysql, $query);
	echo "<script> location.href = '/auth?err=no' </script>";
}
else echo "<script> location.href = '/auth?err=err' </script>";
?>