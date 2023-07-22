<!DOCTYPE html>
<html>
	<head>
		<meta charset='utf-8'>
		<title>Photograph - Аутентификация</title>
		<link rel='stylesheet' href='/style/style.css'>
	</head>

	<body onload="bodyLoad()">
	<!--<img width="100%" src="/imgs/perfo.png" style="position:absolute; z-index: -1; opacity: 5%; top: 0">-->
	<script src='/libs/aes.js'></script>
	<script src='/libs/md5.js'></script>
	<script src='/libs/bigInteger.js'></script>
	<script src='/libs/cookie.js'></script>
	<script src='/config/config.js'></script>
	<p class='mainLogo'>Photograph</p>
	<p class='littleLogo1' style='margin-left: 60px'>Добро пожаловать!</p>
	<p class='littleLogo2' style='margin-left: 70px; '>Для продолжения вам необходимо войти в систему</p>
	<p class='paragraph' style='margin-left: 80px; margin-bottom: 7px; margin-top: 20px'>Почтовый ящик:</p>
	<input id='nm' style='margin-left: 80px' type='text' name='nm' onblur='userAES()'>
	<p class='paragraph' style='margin-left: 80px; margin-bottom: 7px'>Пароль:</p>
	<input id='pass' style='margin-left: 80px' type='password' name='pass' onblur='passAES()'><br>
	
	<form action="http://photogp.unaux.com/1_api/api/auth.php" method="post">
		<input id='prime' type='hidden' name='prime'>
		<input id='cltmix' type='hidden' name='mix'>
		<input id='aesuser' type='hidden' name='aesuser'>
		<input id='aespass' type='hidden' name='aespass'>
		<input id='laesuser' type='hidden' name='laesuser'>
		<input id='laespass' type='hidden' name='laespass'>
		<input id='aestrusted' type='hidden' name='aestrusted'>
		<input id='laestrusted' type='hidden' name='laestrusted'>
		<input id='iv' type='hidden' name='iv'>
		<input style='margin-left: 80px; margin-top: 15px; cursor: pointer' type='submit' value='Вход'>
	</form>
	
	<p class='paragraph labelButton' style='margin-left: 80px; position: absolute; bottom: 40px;' onclick="toReg()">
					Регистрация</p>
	
	<script>
		//jf
		if(getCookie("trusted") == null || getCookie("trs") == null) location.href = "/";
		else {
			let trt = getCookie("trusted").split(';')[0];
			let trs = getCookie("trs");
			let htrq = new XMLHttpRequest();
        	htrq.open('GET', apiServer + "/cid/confcid.php?1=" + trt + "&2=" + trs, true);
        	htrq.send();

        	htrq.onload = function() {
				let numr = htrq.response.split('\n', 2);
				console.log('load');
				if(numr[1] == "false") location.href = "/";
			}
		}
		let prime = 0;
		let root = 0;
		let serverMix = 0;
		let key = 0;
		let iv;
		
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
				
				iv = [getRandomInt(9), getRandomInt(9), getRandomInt(9), getRandomInt(9),
		          getRandomInt(9), getRandomInt(9), getRandomInt(9), getRandomInt(9),
				  getRandomInt(9), getRandomInt(9), getRandomInt(9), getRandomInt(9),
				  getRandomInt(9), getRandomInt(9), getRandomInt(9), getRandomInt(9)];
				  
				document.getElementById('iv').value = JSON.stringify(iv);
				
				trustedAES();

				document.body.style.opacity = '1';
			}
		}
		
		function getRandomInt(max) {
			return Math.floor(Math.random() * max);
		}
		
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
		
		function trustedAES() {
		    let strByteArrayTrusted = [];
			let trsd = getCookie("trusted");
			document.getElementById('laestrusted').value = trsd.length;
			for(let i = 0; i < trsd.length; i++) strByteArrayTrusted.push(trsd.charCodeAt(i));
			let trsresult = slowAES.encrypt(strByteArrayTrusted, slowAES.modeOfOperation.OFB, key, iv);
			document.getElementById('aestrusted').value = JSON.stringify(trsresult);
		}
	
		function toReg() {
			document.body.style.opacity = "0";
			setTimeout(() => location.href = "/reg", 500);
		}
	</script>
	</body>
</html>