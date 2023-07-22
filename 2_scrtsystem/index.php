<!DOCTYPE html>
<html>
	<head>
		<meta charset='utf-8'>
		<title>Система сертификации svsp</title>
		<link rel='stylesheet' href='styles/style.css'>
	</head>
<?php
if($_GET['p'] == "876453435667687") {

	include($_SERVER['DOCUMENT_ROOT']."/2_scrtsystem/libs/svsp-number-generator.php");

	//phpinfo(-1);
    function strToInt($str) {
		$strs = iconv(mb_detect_encoding($str), 'ASCII//TRANSLIT', $str);
		$fstr = '';
		for($i = 0; $i < strlen($strs); $i++){
			$fstr += (string) (ord($strs[$i]) * time());
		}
		return $fstr * time();
	}

	$smNum = primeRandom(16, false);
    $root = rand(3, 20);
	$key = fopen($_SERVER['DOCUMENT_ROOT']."/2_scrtsystem/key/key.txt", "r");					   
	$mix = explode('.', bcmod(bcpow((string) $root, (string) fgets($key), 1000), 
				 (string) $smNum, 1))[0];
    fclose($key);

	echo <<<MNT
	<body onload='bodyLoad()'>
	    <script src='script/aes.js'></script>
		<script src='libs/md5.js'></script>
		<script src='libs/bigInteger.js'></script>
		<p class='mainLogo'>Система сертификации svsp</p>
		<p class='littleLogo1' style='margin-left: 60px'>Для продолжения вам необходимо войти в систему</p>
		<p class='littleLogo2' style='margin-left: 80px'>Имя пользователя:</p>
		<input id='nm' style='margin-left: 80px' type='text' name='nm' onblur='userAES()'>
		<p class='littleLogo2' style='margin-left: 80px'>Пароль:</p>
		<input id='pass' style='margin-left: 80px' type='password' name='pass' onblur='passAES()'><br>
		<form action="reg/enter.php" method="post">
			<input type='hidden' name='prime' value='$smNum'>
			<input id='cltmix' type='hidden' name='mix'>
			<input id='aesuser' type='hidden' name='aesuser'>
			<input id='aespass' type='hidden' name='aespass'>
			<input id='laesuser' type='hidden' name='laesuser'>
			<input id='laespass' type='hidden' name='laespass'>
			<input id='iv' type='hidden' name='iv'>
			<input style='margin-left: 80px; margin-top: 20px; cursor: pointer' type='submit' value='Вход'>
		</form>		
	</body>
</noscript>
<div style="text-align: center;"><div style="position:relative; top:0; margin-right:auto;margin-left:auto; z-index:99999">

</div></div>
	
	<script>
		function bodyLoad() {
			document.body.style.opacity = '1';
		}
		
		function getRandomInt(max) {
			return Math.floor(Math.random() * max);
		}
		
		let iv = [getRandomInt(9), getRandomInt(9), getRandomInt(9), getRandomInt(9),
		          getRandomInt(9), getRandomInt(9), getRandomInt(9), getRandomInt(9),
				  getRandomInt(9), getRandomInt(9), getRandomInt(9), getRandomInt(9),
				  getRandomInt(9), getRandomInt(9), getRandomInt(9), getRandomInt(9)];
				  
		document.getElementById('iv').value = JSON.stringify(iv);
		
		let now = new Date();
		let secret = JSBI.BigInt(getRandomInt(9999));
		let mix = JSBI.remainder(JSBI.exponentiate(JSBI.BigInt($root), secret), JSBI.BigInt($smNum));
		
		
		document.getElementById('cltmix').value = mix;
		
		let shdkey = JSBI.remainder(JSBI.exponentiate(JSBI.BigInt($mix), secret), JSBI.BigInt($smNum));
		
		let key = md5(shdkey.toString()).substring(0, 16);
		
		let pass;
		let presult;
		
		let user;
		let uresult;
		
		function userAES() {
		    let strByteArrayUser = [];
			user = document.getElementById('nm').value;
			document.getElementById('laesuser').value = user.length;
			for(let i = 0; i < user.length; i++) strByteArrayUser.push(user.charCodeAt(i));
			uresult = slowAES.encrypt(strByteArrayUser, slowAES.modeOfOperation.OFB, key, iv);
			let str = '';
			//for(i = 0; i < uresult.length; i++) str += String.fromCharCode(uresult[i]);
			//document.getElementById('aesuser').value = str;
			document.getElementById('aesuser').value = JSON.stringify(uresult);
		}
		
		function passAES() {
		    let strByteArrayPass = [];
			pass = document.getElementById('pass').value;
			document.getElementById('laespass').value = pass.length;
			for(let i = 0; i < pass.length; i++) strByteArrayPass.push(pass.charCodeAt(i));
			presult = slowAES.encrypt(strByteArrayPass, slowAES.modeOfOperation.OFB, key, iv);
			let str = '';
			//for(i = 0; i < presult.length; i++) str += String.fromCharCode(presult[i]);
			document.getElementById('aespass').value = JSON.stringify(presult);
		}
		
	</script>
MNT;

}
else echo '<p>password?</p><script>document.body.style.opacity="1"</script>';


?>
</html>
</noscript>
<div style="text-align: center;"><div style="position:relative; top:0; margin-right:auto;margin-left:auto; z-index:99999">

</div></div>
