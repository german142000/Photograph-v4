<!DOCTYPE html>
<html>
	<head>
		<meta charset='utf-8'>
		<title>Photograph - Завершение регистрации</title>
		<link rel='stylesheet' href='/style/style.css'>
	</head>

	<body onload="bodyLoad()">
	<img width="100%" src="/imgs/perfo.png" style="position:absolute; z-index: -1; opacity: 5%; top: 0">
	<script src='/libs/aes.js'></script>
	<script src='/libs/md5.js'></script>
	<script src='/libs/bigInteger.js'></script>
	<script src='/libs/cookie.js'></script>
	<script src='/config/config.js'></script>
	<p class='mainLogo'>Photograph</p>
	<p class='littleLogo1' style='margin-left: 60px'>Завершение регистрации</p>
	<p class='littleLogo2' style='margin-left: 70px; '>Введите ваш никнейм ниже</p>
	<p class='paragraph' style='margin-left: 80px; margin-bottom: 7px; margin-top: 20px'>Ник:</p>
	<input id='nm' style='margin-left: 80px' type='text' name='nm' onblur='userAES()'>
	
	<form action="/1_api/api/cn.php" method="post">
		<input id='prime' type='hidden' name='prime'>
		<input id='cltmix' type='hidden' name='mix'>
		<input id='aesuser' type='hidden' name='aesuser'>
		<input id='laesuser' type='hidden' name='laesuser'>
		<input id='iv' type='hidden' name='iv'>
		<input type='hidden' name='uid' value="<?php echo $_GET['1']?>">
		<input id="ru" type='hidden' name='rurl'>
		<input style='margin-left: 80px; margin-top: 15px; cursor: pointer' type='submit' value='Подтвердить'>
	</form>
	
	<!--<p class='paragraph labelButton' style='margin-left: 80px; position: absolute; bottom: 40px;' onclick="toReg()">
					Аутентификация</p>-->
	
	<script>
		let prime = 0;
		let root = 0;
		let serverMix = 0;
		let key = 0;
		
		function bodyLoad() {
        	let htrq = new XMLHttpRequest();
        	htrq.open('GET', apiServer + '/api/api.php', true);
        	htrq.send();

        	htrq.onload = function() {

            	let numr = htrq.response.split('\n', 4);
				prime = numr[0];
				root = numr[1];
				serverMix = numr[2];
				document.getElementById('prime').value = prime;
			
				let now = new Date();
				let secret = JSBI.BigInt(getRandomInt(9999));
				let mix = JSBI.remainder(JSBI.exponentiate(JSBI.BigInt(root), secret), JSBI.BigInt(prime));
				document.getElementById('cltmix').value = mix;
				let shdkey = JSBI.remainder(JSBI.exponentiate(JSBI.BigInt(serverMix), secret), JSBI.BigInt(prime));
				key = md5(shdkey.toString()).substring(0, 16);

				document.body.style.opacity = '1';
				document.getElementById('ru').value = "/auth";
			}
		}
		
		function getRandomInt(max) {
			return Math.floor(Math.random() * max);
		}
		
		let iv = [getRandomInt(9), getRandomInt(9), getRandomInt(9), getRandomInt(9),
		          getRandomInt(9), getRandomInt(9), getRandomInt(9), getRandomInt(9),
				  getRandomInt(9), getRandomInt(9), getRandomInt(9), getRandomInt(9),
				  getRandomInt(9), getRandomInt(9), getRandomInt(9), getRandomInt(9)];
				  
		document.getElementById('iv').value = JSON.stringify(iv);
		
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
	
		function toReg() {
			document.body.style.opacity = "0";
			setTimeout(() => location.href = clientServer + "/auth", 500);
		}
	</script>
	</body>
</html>