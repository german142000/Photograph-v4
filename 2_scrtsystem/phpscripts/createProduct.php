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
$code = explode('.', bcmod(bcpow((string)$_POST['mix'], (string)fgets($key) , 1000), $_POST['prime'], 1)) [0];
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
				<p class='mainLogo'>Система сертификаци svsp</p>
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
        $query = <<<TBL1
		  SELECT count(*) FROM products
TBL1;
        $row = mysqli_fetch_row(mysqli_query($mysql, $query));
        if ($row[0] > 0)
        {
			$query = 'SELECT product_name FROM products';
		    $prArr = mysqli_fetch_all(mysqli_query($mysql, $query));
			$trg = true;
		    for($i = 0; $i < count($prArr); $i++) {
				if($prArr[$i][0] == $namestri) $trg = false;
			}
			if($trg){
            	$row[0]++;
            	$md5hes = md5($namestri);
            	$time = time();
            	$termtime = time() + (int)$termstri * 2592000;
           	 	$query = <<<TBL2
		  		INSERT products(id, product_name, product_xes, product_data, product_term)
		  		VALUES ($row[0], '$namestri', '$md5hes', '$time', '$termtime')
TBL2;
            	mysqli_query($mysql, $query);
			} else {
				echo '<script> document.body.innerHTML = "";</script>
				<p>Данный продукт уже существует</p>';
			    echo '<script> document.getElementById("cp").value = false </script>';
			}
        }
        else
        {
            $md5hes = md5($namestri);
            $time = time();
            $termtime = time() + (int)$termstri * 2592000;
            $query = <<<TBL3
		  INSERT products(id, product_name, product_xes, product_data, product_term)
		  VALUES (1, '$namestri', '$md5hes', '$time', '$termtime')
TBL3;
		  mysqli_query($mysql, $query);
		  }
		  echo '<script> document.body.innerHTML = "";</script><p>Продукт добавлен: '.$namestri.'; '.$termstri.'</p>';
	      print_r($prArr);
	}
} else {
echo '<script> document.body.innerHTML = "";</script><p>Доступ заблокирован - неверные имя пользователя или пароль</p>';
};

echo mysqli_error($mysql);

echo <<<FORM
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
			<input id='cp' type='hidden' name='cp'>
			<input id='iv' type='hidden' name='iv'>
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
		
		document.getElementById('toentr').submit();
		
	</script>
FORM;
?>
