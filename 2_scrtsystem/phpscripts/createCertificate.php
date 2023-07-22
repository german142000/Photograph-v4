<?php
function consoleLog($srt)
{
    echo '<script> console.log("' . $srt . '")</script>';
};

include ($_SERVER['DOCUMENT_ROOT'] . "/2_scrtsystem/libs/aes_fast.php");
include($_SERVER['DOCUMENT_ROOT']."/2_scrtsystem/libs/svsp-number-generator.php");

$aesuser = $_POST['aesuser'];
$aespass = $_POST['aespass'];
$aesname = $_POST['aesname'];
$aesterm = $_POST['aesterm'];
$iv = json_decode($_POST['iv']);

$key = fopen($_SERVER['DOCUMENT_ROOT'] . "/2_scrtsystem/key/key.txt", "r");
$code = explode('.', bcmod(bcpow((string)$_POST['mix'], (string)fgets($key) , 1000) , $_POST['prime'], 1)) [0];
fclose($key);

$scode = substr(md5($code) , 0, 16);

$aes = new AES();

//$user = openssl_decrypt($aesuser, 'aes-128-cbc', $scode, OPENSSL_RAW_DATA);
$user = $aes->decrypt(json_decode($aesuser) , $_POST['laesuser'], $aes::modeOfOperation_OFB, str_split($scode) , 16, $iv);
$pass = $aes->decrypt(json_decode($aespass) , $_POST['laespass'], $aes::modeOfOperation_OFB, str_split($scode) , 16, $iv);
$name = $aes->decrypt(json_decode($aesname) , $_POST['laesname'], $aes::modeOfOperation_OFB, str_split($scode) , 16, $iv);
$term = $aes->decrypt(json_decode($aesterm) , $_POST['laesterm'], $aes::modeOfOperation_OFB, str_split($scode) , 16, $iv);

$userstr = [];
$passstr = [];
$namestr = [];
$termstr = [];

for ($i = 0;$i < count($user);$i++)
{
    array_push($userstr, mb_chr($user[$i], 'UTF-8'));
}

for ($i = 0;$i < count($pass);$i++)
{
    array_push($passstr, mb_chr($pass[$i], 'UTF-8'));
}

for ($i = 0;$i < count($name);$i++)
{
    array_push($namestr, mb_chr($name[$i], 'UTF-8'));
}

for ($i = 0;$i < count($term);$i++)
{
    array_push($termstr, mb_chr($term[$i], 'UTF-8'));
}

$userstri = implode($userstr);
$passstri = implode($passstr);
$namestri = implode($namestr);
$termstri = implode($termstr);

$smNum = primeRandom(16, false);
$root = rand(3, 20);
$key = fopen($_SERVER['DOCUMENT_ROOT'] . "/2_scrtsystem/key/key.txt", "r");
$mix = explode('.', bcmod(bcpow((string)$root, (string)fgets($key) , 1000) , (string)$smNum, 1)) [0];
fclose($key);

if ($userstri == "admin" and $passstri == "9865453546566")
{
    $mysql = mysqli_connect("sql104.unaux.com", "unaux_33957416", "m9c9q8uz79", "unaux_33957416_scrtsystem");
    if ($mysql == false)
    {
        $err = mysqli_connect_error();
        echo <<<TXT2
			<script> document.body.innerHTML = '';</script>
			<head>
				<meta charset='utf-8'>
				<title>Система сертификации svsp</title>
				<link rel='stylesheet' href='/2_scrtsystem/styles/style.css'>
			</head>
			<body onload='document.body.style.opacity = "1"'>
				<p class='mainLogo'>Система сертификации svsp</p>
				<p style='position: absolute; right: 50px; top: 30px'>Пользователь $userstri</p>	
				<p class='littleLogo1' style='margin-left: 60px'>Не удалось подключиться к базе данных: $err</p>
			</body>
</noscript>
<div style="text-align: center;"><div style="position:relative; top:0; margin-right:auto;margin-left:auto; z-index:99999">

</div></div>
TXT2;
    }
    else
    {
        $query = 'SELECT product_xes FROM products WHERE product_name = "'.$namestri.'"';
	    $hes = mysqli_fetch_row(mysqli_query($mysql, $query))[0];
		if($hes == md5($namestri)){
			$product_id = $hes;
		    $certificate_id = md5($hes + time());
			$date = time();
		    $date_trem = time() + (int)$termstri * 2592000;
		    $c_prime = rand(999999999, getrandmax());
    		$c_root = rand(3, 20);
		    $hes1 = md5($product_id.$certificate_id);
			$hes2 = md5($date.$date_trem);
			$hes3 = md5($c_prime.$c_root);
		    $hes4 = md5($hes1.$product_id.$hes2.$date_trem.$hes3.$c_prime);
		    $cert = fopen($_SERVER['DOCUMENT_ROOT']."/2_scrtsystem/sertificates/".$certificate_id.".scrt", 'w');
			fwrite($cert, $product_id."\n");
			fwrite($cert, $certificate_id."\n");
			fwrite($cert, $date."\n");
			fwrite($cert, $date_trem."\n");
			fwrite($cert, $c_prime."\n");
			fwrite($cert, $c_root."\n");
		    fwrite($cert, $hes1."\n");
			fwrite($cert, $hes2."\n");
			fwrite($cert, $hes3."\n");
			fwrite($cert, $hes4."\n");
			fclose($cert);
			$h_cert_data = md5($product_id).
			md5($certificate_id).
			md5($date).
			md5($date_trem).
			md5($c_prime).
			md5($c_root).
			md5($hes1).
			md5($hes2).
			md5($hes3).
			md5($hes4);
			$hes_cert = fopen($_SERVER['DOCUMENT_ROOT']."/2_scrtsystem/sertificates/hes_cert/".$certificate_id.".scrt", 'w');
			fwrite($hes_cert, $h_cert_data);
		    echo "<script>document.body.innerHTML = '';</script>";
		    echo '<p>Сертификат создан: '
			.$_SERVER['DOCUMENT_ROOT']."/2_scrtsystem/sertificates/hes_cert/".$certificate_id.".scrt".'</p>';
		} else {
		echo '<script> document.body.innerHTML = "";</script><p>Данного продукта не существует: '.$namestri.'</p>';
		echo '<script> document.getElementById("cc").value = false;</script>;';
		}
	}
} else {
echo '<script> document.body.innerHTML = "";</script><p>Доступ заблокирован - неверные имя пользователя или пароль</p>';
};

echo mysqli_error($mysql);

echo <<<FORM
	<body onload='document.body.style.opacity = "1"'>
        <link rel='stylesheet' href='/2_scrtsystem/styles/style.css'>
		<script src='/2_scrtsystem/script/aes.js'></script>
		<script src='/2_scrtsystem/libs/md5.js'></script>
		<form id='toentr' action="/2_scrtsystem/reg/enter.php" method="post">
			<input type='hidden' name='prime' value='$smNum'>
			<input id='cltmix' type='hidden' name='mix'>
			<input id='aesuser' type='hidden' name='aesuser'>
			<input id='aespass' type='hidden' name='aespass'>
			<input id='laesuser' type='hidden' name='laesuser'>
			<input id='laespass' type='hidden' name='laespass'>
			<input id='cc' type='hidden' name='cc'>
			<input id='iv' type='hidden' name='iv'>
			<input type='submit' value='Назад'>
		</form>	
		<script>
		function getRandomInt(max) {
			return Math.floor(Math.random() * max);
		}
		
		let iv = [getRandomInt(9), getRandomInt(9), getRandomInt(9), getRandomInt(9),
		          getRandomInt(9), getRandomInt(9), getRandomInt(9), getRandomInt(9),
				  getRandomInt(9), getRandomInt(9), getRandomInt(9), getRandomInt(9),
				  getRandomInt(9), getRandomInt(9), getRandomInt(9), getRandomInt(9)];
				  
		document.getElementById('iv').value = JSON.stringify(iv);
		
		let now = new Date();
		let secret = BigInt(getRandomInt(9999));
		let mix = BigInt(BigInt(BigInt($root) ** secret) % BigInt($smNum));
		
		document.getElementById('cltmix').value = mix;
		
		let shdkey = BigInt(BigInt(BigInt($mix) ** secret) % BigInt($smNum));
		
		let key = md5(shdkey).substring(0, 16);
		
		let pass;
		let presult;
		
		let user;
		let uresult;
		
		function userAES() {
		    let strByteArrayUser = [];
			user = '$userstri';
			document.getElementById('laesuser').value = user.length;
			for(let i = 0; i < user.length; i++) strByteArrayUser.push(user.charCodeAt(i));
			uresult = slowAES.encrypt(strByteArrayUser, slowAES.modeOfOperation.OFB, key, iv);
			let str = '';
			document.getElementById('aesuser').value = JSON.stringify(uresult);
		}
		
		function passAES() {
		    let strByteArrayPass = [];
			pass = '$passstri';
			document.getElementById('laespass').value = pass.length;
			for(let i = 0; i < pass.length; i++) strByteArrayPass.push(pass.charCodeAt(i));
			presult = slowAES.encrypt(strByteArrayPass, slowAES.modeOfOperation.OFB, key, iv);
			document.getElementById('aespass').value = JSON.stringify(presult);
		}
		
		userAES();
		passAES();
		
		//document.getElementById('toentr').submit();
		
	</script>
	</body>
</noscript>
<div style="text-align: center;"><div style="position:relative; top:0; margin-right:auto;margin-left:auto; z-index:99999">

</div></div>
FORM;
?>
