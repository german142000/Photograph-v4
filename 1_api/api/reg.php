<link rel='stylesheet' href='/style/style.css'>
<?php
include($_SERVER['DOCUMENT_ROOT']."/1_api/libs/aes_fast.php");
include($_SERVER['DOCUMENT_ROOT']."/1_api/libs/svsp-number-generator.php");
include($_SERVER['DOCUMENT_ROOT']."/1_api/config/config.php");

$aesuser = $_POST['aesuser'];
$aespass = $_POST['aespass'];
$iv = json_decode($_POST['iv']);

$key = fopen($_SERVER["DOCUMENT_ROOT"] . "/1_api/scert/cert/key.txt", "r");
$code = explode('.', bcmod(bcpow((string) $_POST['mix'], (string) fgets($key), 1000), 
				 $_POST['prime'], 1))[0];
fclose($key);

$scode = substr(md5($code), 0, 16);

$aes = new AES();

$user = $aes -> decrypt(json_decode($aesuser), $_POST['laesuser'], 
						$aes::modeOfOperation_OFB, str_split($scode), 16, $iv);
$pass = $aes -> decrypt(json_decode($aespass), $_POST['laespass'], 
						$aes::modeOfOperation_OFB, str_split($scode), 16, $iv);

$userstr = [];
$passstr = [];

for($i = 0; $i < count($user); $i++){
array_push($userstr, mb_chr($user[$i], 'UTF-8'));
}

for($i = 0; $i < count($pass); $i++){
array_push($passstr, mb_chr($pass[$i], 'UTF-8'));
}

$userstri = implode($userstr);
$passstri = implode($passstr);

$mysql = mysqli_connect("sql104.unaux.com", "unaux_33957416", "m9c9q8uz79", "unaux_33957416_apiphotogp");
if(mysqli_connect_error() == null) {
	$err = "no errors";
	$query = <<<TBL1
		  SELECT count(*) FROM users
TBL1;
    $row = mysqli_fetch_row(mysqli_query($mysql, $query));
	$row[0]++;
	$uid = 9999999999 - $row[0];
	if(filter_var($userstri, FILTER_VALIDATE_EMAIL)) {
		mkdir($_SERVER['DOCUMENT_ROOT']."/1_api/users/".$uid);
		file_put_contents($_SERVER['DOCUMENT_ROOT']."/1_api/users/".$uid."/userkey.txt", md5($uid + time()));
		$cmail = hash('ripemd160', md5($userstri));
		$query = <<<TBL3
		SELECT mail FROM users WHERE mail = '$cmail'
TBL3;
		echo "arr ";
		$arrm = mysqli_fetch_array(mysqli_query($mysql, $query));
		if(count($arrm)) {
			echo "<script> location.href = '".$config['clientServer']."/reg/?err=mailExists' </script>";
		} else {
			$cpass = hash('ripemd160', md5($passstri));
			$query = <<<TBL2
		  		INSERT users(id, photo_id, mail, pass, nickname)
		  		VALUES ('$row[0]', '$uid', '$cmail', '$cpass', null)
TBL2;
			mysqli_query($mysql, $query);
			$merr = trim(mysqli_error($mysql));
			$merr = str_replace("\r\n", "", $merr);
			if($merr == null) {
				echo "<script> console.log('db: ' + 'no errors') </script>";
				echo "<script> location.href = '/1_api/cpt/index.php?1=".$aesuser."b".$_POST['iv']."b".$_POST['laesuser'].
				"b".$_POST['mix']."b".$_POST['prime'].
				"&2=".$uid."' </script>";
			} else echo $merr;
		}
	}
	else echo "<script> location.href = '".$config['clientServer']."/reg/?err=mailError' </script>";
}
else $err = mysqli_connect_error();
echo "<script> console.log('db: ' + \"$err\") </script>";
?>
