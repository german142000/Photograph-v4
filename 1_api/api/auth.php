<link rel='stylesheet' href='/style/style.css'>
<script src='/1_api/config/config.js'></script>
<?php
include($_SERVER['DOCUMENT_ROOT']."/1_api/libs/aes_fast.php");
include($_SERVER['DOCUMENT_ROOT']."/1_api/libs/svsp-number-generator.php");
include($_SERVER['DOCUMENT_ROOT']."/1_api/config/config.php");

$cs = $config['clientServer'];

$aesuser = $_POST['aesuser'];
$aespass = $_POST['aespass'];
$aestrusted = $_POST['aestrusted'];
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
$trusted = $aes -> decrypt(json_decode($aestrusted), $_POST['laestrusted'], 
						$aes::modeOfOperation_OFB, str_split($scode), 16, $iv);

$userstr = [];
$passstr = [];
$trustedstr = [];

for($i = 0; $i < count($user); $i++){
array_push($userstr, mb_chr($user[$i], 'UTF-8'));
}

for($i = 0; $i < count($pass); $i++){
array_push($passstr, mb_chr($pass[$i], 'UTF-8'));
}

for($i = 0; $i < count($trusted); $i++){
array_push($trustedstr, mb_chr($trusted[$i], 'UTF-8'));
}

$userstri = implode($userstr);
$passstri = implode($passstr);
$trustedstri = implode($trustedstr);
$trustedstri = explode(";", $trustedstri)[0];

$cmail = hash('ripemd160', md5($userstri));
$cpass = hash('ripemd160', md5($passstri));

$mysql = mysqli_connect("sql104.unaux.com", "unaux_33957416", "m9c9q8uz79", "unaux_33957416_apiphotogp");
if(mysqli_connect_error() == null) {

$query = <<<TBL
SELECT pass FROM users WHERE mail = '$cmail'
TBL;
$res = mysqli_fetch_array(mysqli_query($mysql, $query));
if($res[0] == null) {
	echo "<script> location.href = '/auth/?err=dpass' </script>";
	exit(0);
} else if($res[0] == $cpass){
	$key = md5($cmail.time());
	$query = <<<TBL2
SELECT photo_id FROM users WHERE mail = '$cmail'
TBL2;
	$ui = mysqli_fetch_array(mysqli_query($mysql, $query))[0];
	file_put_contents($_SERVER['DOCUMENT_ROOT']."/1_api/users/$ui/keys/$key.txt", $trustedstri);
	$query = <<<TBL2
SELECT nickname FROM users WHERE mail = '$cmail'
TBL2;
	$nc = mysqli_fetch_array(mysqli_query($mysql, $query))[0];

	$iv = array(rand(1, 9), rand(1, 9), rand(1, 9), rand(1, 9),
				rand(1, 9), rand(1, 9), rand(1, 9), rand(1, 9),
				rand(1, 9), rand(1, 9), rand(1, 9), rand(1, 9),
				rand(1, 9), rand(1, 9), rand(1, 9), rand(1, 9));

	$keyr = fopen($_SERVER["DOCUMENT_ROOT"] . "/1_api/scert/cert/key.txt", "r");
	$scode = substr(md5(fgets($keyr)), 0, 16);
	fclose($keyr);

	$certarr = [];
	$cid = str_split($key);
	for($i = 0; $i < count($cid); $i++) {
		array_push($certarr, ord($cid[$i]));
	}

	$aescert = $aes -> encrypt($certarr, $aes::modeOfOperation_OFB, str_split($scode), 16, $iv);
	    
	$ss = json_encode($aescert["cipher"]);

	//setcookie("session", $aescid, time()+60*60*24*30, "/", "", false, true);
	//setcookie("strs", json_encode($iv), time()+60*60*24*30, "/", "", false, true);
	
	$st = json_encode($iv);
	
	$req = file_get_contents($cs."/auth/confAuth.php?api=1");
	$req = explode("\n", $req);
	
	$prime = $req[0];
	$root = $req[1];
	$mix = $req[2];
	
	$key = fopen($_SERVER["DOCUMENT_ROOT"] . "/1_api/scert/cert/key.txt", "r");
    $code = explode('.', bcmod(bcpow((string) $mix, (string) fgets($key), 1000), $prime, 1))[0];
    fclose($key);
    
    $key = fopen($_SERVER["DOCUMENT_ROOT"] . "/1_api/scert/cert/key.txt", "r");
    $smix = explode(".", bcmod(bcpow((string)$root, (string) fgets($key), 1000), (string)$prime, 1))[0];
    fclose($key);
    
    $scode = substr(md5($code), 0, 16);
    
    $uiarr = [];
	$ui = str_split($ui);
	for($i = 0; $i < count($ui); $i++) {
		array_push($uiarr, ord($ui[$i]));
	}
	
	$ncarr = [];
	$nc = str_split($nc);
	for($i = 0; $i < count($nc); $i++) {
		array_push($ncarr, ord($nc[$i]));
	}
	
	$ssarr = [];
	$ss = str_split($ss);
	for($i = 0; $i < count($ss); $i++) {
		array_push($ssarr, ord($ss[$i]));
	}
	
	$starr = [];
	$st = str_split($st);
	for($i = 0; $i < count($st); $i++) {
		array_push($starr, ord($st[$i]));
	}
	
	$eui = $aes -> encrypt($uiarr, $aes::modeOfOperation_OFB, str_split($scode), 16, $iv);
	$enc = $aes -> encrypt($ncarr, $aes::modeOfOperation_OFB, str_split($scode), 16, $iv);
	$ess = $aes -> encrypt($ssarr, $aes::modeOfOperation_OFB, str_split($scode), 16, $iv);
	$est = $aes -> encrypt($starr, $aes::modeOfOperation_OFB, str_split($scode), 16, $iv);
	
	$eui = json_encode($eui['cipher']);
	$enc = json_encode($enc['cipher']);
	$ess = json_encode($ess['cipher']);
	$est = json_encode($est['cipher']);
	$st = json_encode($iv);
	
	echo "<script> location.href = '/auth/confAuth.php?api=2&1=$smix&2=$ess&3=$est&4=$eui&5=$enc&6=$st&7=$prime' </script>";
} else {
	echo "<script> location.href = '/auth/?err=data' </script>";
	exit(0);
}

} else echo "<script> location.href = /auth/?err=db' </script>";

?>