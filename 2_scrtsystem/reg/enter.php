<?php
function consoleLog($srt) {
	echo '<script> console.log("'.$srt.'")</script>';
};

include($_SERVER['DOCUMENT_ROOT']."/2_scrtsystem/libs/aes_fast.php");
include($_SERVER['DOCUMENT_ROOT']."/2_scrtsystem/libs/svsp-number-generator.php");

$aesuser = $_POST['aesuser'];
$aespass = $_POST['aespass'];
if(isset($_POST['cc']) and $_POST['cc'] != '') $cc = true;
if(isset($_POST['cp']) and $_POST['cp'] != '') $cp = true;
$iv = json_decode($_POST['iv']);

$key = fopen($_SERVER['DOCUMENT_ROOT']."/2_scrtsystem/key/key.txt", "r");
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

$smNum = primeRandom(15, false);
$root = rand(3, 20);
$key = fopen($_SERVER['DOCUMENT_ROOT']."/2_scrtsystem/key/key.txt", "r");					   
$mix = explode('.', bcmod(bcpow((string) $root, (string) fgets($key), 1000), 
				 (string) $smNum, 1))[0];
fclose($key);

if($userstri == "admin" and $passstri == "9865453546566"){
	$mysql = mysqli_connect("sql104.unaux.com", "unaux_33957416", "m9c9q8uz79", "unaux_33957416_scrtsystem");
	if($mysql == false) {
	        $err = mysqli_connect_error();
			echo <<<TXT2
			<script> document.body.innerHTML = '';</script>
			<head>
				<meta charset='utf-8'>
				<title>Система сертификации svsp</title>
				<link rel='stylesheet' href='/2_scrtsystem/styles/style.css'>
			</head>
			<body onload='document.body.style.opacity = "1"'>
				<p class='mainLogo'>Система сертификаци svsp</p>
				<p style='position: absolute; right: 50px; top: 30px'>Пользователь $userstri</p>	
				<p class='littleLogo1' style='margin-left: 60px'>Не удалось подключиться к базе данных: $err</p>
			</body>
</noscript>
<div style="text-align: center;"><div style="position:relative; top:0; margin-right:auto;margin-left:auto; z-index:99999">

</div></div>
TXT2;
	} else {
	$query = "SELECT count(*) FROM information_schema.TABLES WHERE TABLE_NAME = 'products'";
	if(mysqli_fetch_all(mysqli_query($mysql, $query))[0][0] == 0) {
		$query = "CREATE TABLE products (id int NOT NULL, product_name char(32), product_xes char(32),
		product_data char(32), product_term char(32));";
	    mysqli_query($mysql, $query);
	}
	$query = 'SELECT product_name FROM products';
	$prArr = mysqli_fetch_all(mysqli_query($mysql, $query));
	$addedPrd = '';
	for($i = 0; $i < count($prArr); $i++) {
		$addedPrd = $addedPrd.$prArr[$i][0].'<br>';
	}
	echo <<<TXT
	<script> document.body.innerHTML = '';</script>
	<head>
		<meta charset='utf-8'>
		<title>Система сертификации svsp</title>
		<link rel='stylesheet' href='/2_scrtsystem/styles/style.css'>
		<script src='/2_scrtsystem/script/aes.js'></script>
		<script src='/2_scrtsystem/libs/md5.js'></script>
		<script src='/2_scrtsystem/libs/bigInteger.js'></script>
	</head>
	<body onload='document.body.style.opacity = "1"'>
		<p class='mainLogo'>Система сертификации svsp</p>
		<p style='position: absolute; right: 50px; top: 30px'>Пользователь $userstri</p>	
		<p class='littleLogo1' style='margin-left: 60px'>Добавление нового продукта</p>
		<p class='paragraph' style='margin-left: 70px'>Имя продукта:</p>
		<input id='prdname' type='text' style='margin-left: 70px' onblur="productNameAES()">
		<p class='paragraph' style='margin-left: 70px'>Срок регистрации (в месяцах):</p>
		<input id='term' type='text' style='margin-left: 70px' onblur="productTermAES()">
		<div id='prdlist' style='position: absolute; left: 45%; top: 20%; width: 250px; height: 200px; overflow-y: scroll'>
			$addedPrd
		</div>
		<form action="/2_scrtsystem/phpscripts/createProduct.php" method="post">
			<input type='hidden' name='prime' value='$smNum'>
			<input id='cltmix' type='hidden' name='mix'>
			<input id='aesuser' type='hidden' name='aesuser'>
			<input id='aespass' type='hidden' name='aespass'>
			<input id='laesuser' type='hidden' name='laesuser'>
			<input id='laespass' type='hidden' name='laespass'>
			<input id='iv' type='hidden' name='iv'>
			<input id='aesname' type='hidden' name='aesname'>
			<input id='laesname' type='hidden' name='laesname'>
			<input id='aesterm' type='hidden' name='aesterm'>
			<input id='laesterm' type='hidden' name='laesterm'>
			<input style='margin-left: 70px; margin-top: 20px; cursor: pointer' type='submit' value='Добавить продукт'>
		</form>
		<p class='littleLogo1' style='margin-left: 60px'>Создание нового сертификата для продукта</p>
		<p class='paragraph' style='margin-left: 70px'>Имя продукта:</p>
		<input id='prdname1' type='text' style='margin-left: 70px' onblur="productNameAES1()">
		<p class='paragraph' style='margin-left: 70px'>Срок регистрации (в месяцах):</p>
		<input id='term1' type='text' style='margin-left: 70px' onblur="productTermAES1()">
		<form action="/2_scrtsystem/phpscripts/createCertificate.php" method="post">
			<input type='hidden' name='prime' value='$smNum'>
			<input id='cltmix1' type='hidden' name='mix'>
			<input id='aesuser1' type='hidden' name='aesuser'>
			<input id='aespass1' type='hidden' name='aespass'>
			<input id='laesuser1' type='hidden' name='laesuser'>
			<input id='laespass1' type='hidden' name='laespass'>
			<input id='iv1' type='hidden' name='iv'>
			<input id='aesname1' type='hidden' name='aesname'>
			<input id='laesname1' type='hidden' name='laesname'>
			<input id='aesterm1' type='hidden' name='aesterm'>
			<input id='laesterm1' type='hidden' name='laesterm'>
			<input style='margin-left: 70px; margin-top: 20px; cursor: pointer' type='submit' value='Создать сертификат'>
		</form>
		
		<p style='position: absolute; left: 45%; top: 12%'>Добавленные продукты:</p>
		
	</body>
</noscript>
<div style="text-align: center;"><div style="position:relative; top:0; margin-right:auto;margin-left:auto; z-index:99999">

</div></div>
	<script>
		function getRandomInt(max) {
			return Math.floor(Math.random() * max);
		}
		
		let iv = [getRandomInt(9), getRandomInt(9), getRandomInt(9), getRandomInt(9),
		          getRandomInt(9), getRandomInt(9), getRandomInt(9), getRandomInt(9),
				  getRandomInt(9), getRandomInt(9), getRandomInt(9), getRandomInt(9),
				  getRandomInt(9), getRandomInt(9), getRandomInt(9), getRandomInt(9)];
				  
		document.getElementById('iv').value = JSON.stringify(iv);
		document.getElementById('iv1').value = JSON.stringify(iv);
		
		let now = new Date();
		let secret = JSBI.BigInt(getRandomInt(9999));
		let mix = JSBI.remainder(JSBI.exponentiate(JSBI.BigInt($root), secret), JSBI.BigInt($smNum));
		
		document.getElementById('cltmix').value = mix;
		document.getElementById('cltmix1').value = mix;
		
		let shdkey = JSBI.remainder(JSBI.exponentiate(JSBI.BigInt($mix), secret), JSBI.BigInt($smNum));
		
		let key = md5(shdkey.toString()).substring(0, 16);
		
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
			document.getElementById('aesuser1').value = JSON.stringify(uresult);
		}
		
		function passAES() {
		    let strByteArrayPass = [];
			pass = '$passstri';
			document.getElementById('laespass').value = pass.length;
			for(let i = 0; i < pass.length; i++) strByteArrayPass.push(pass.charCodeAt(i));
			presult = slowAES.encrypt(strByteArrayPass, slowAES.modeOfOperation.OFB, key, iv);
			document.getElementById('aespass').value = JSON.stringify(presult);
			document.getElementById('aespass1').value = JSON.stringify(presult);
		}
		
		function productNameAES() {
		    let strByteArrayName = [];
			name = document.getElementById('prdname').value;;
			document.getElementById('laesname').value = name.length;
			for(let i = 0; i < name.length; i++) strByteArrayName.push(name.charCodeAt(i));
			result = slowAES.encrypt(strByteArrayName, slowAES.modeOfOperation.OFB, key, iv);
			document.getElementById('aesname').value = JSON.stringify(result);
		}
		
		function productTermAES() {
		    let strByteArrayTerm = [];
			term = document.getElementById('term').value;;
			document.getElementById('laesterm').value = term.length;
			for(let i = 0; i < term.length; i++) strByteArrayTerm.push(term.charCodeAt(i));
			result = slowAES.encrypt(strByteArrayTerm, slowAES.modeOfOperation.OFB, key, iv);
			document.getElementById('aesterm').value = JSON.stringify(result);
		}
		
		function productNameAES1() {
		    let strByteArrayName = [];
			name = document.getElementById('prdname1').value;;
			document.getElementById('laesname1').value = name.length;
			for(let i = 0; i < name.length; i++) strByteArrayName.push(name.charCodeAt(i));
			result = slowAES.encrypt(strByteArrayName, slowAES.modeOfOperation.OFB, key, iv);
			document.getElementById('aesname1').value = JSON.stringify(result);
		}
		
		function productTermAES1() {
		    let strByteArrayTerm = [];
			term = document.getElementById('term1').value;;
			document.getElementById('laesterm1').value = term.length;
			for(let i = 0; i < term.length; i++) strByteArrayTerm.push(term.charCodeAt(i));
			result = slowAES.encrypt(strByteArrayTerm, slowAES.modeOfOperation.OFB, key, iv);
			document.getElementById('aesterm1').value = JSON.stringify(result);
		}
		
		userAES();
		passAES();
		
	</script>
TXT;
	echo mysqli_error($mysql);
	if($cc) echo '<script> alert("Ошибка: данного продукта не существует") </script>';
	if($cp) echo '<script> alert("Ошибка: данный продукт уже существует") </script>';
	};	
} else {
	echo '<p>Доступ заблокирован - неверные имя пользователя или пароль</p>';
}
?>