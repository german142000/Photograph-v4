<!DOCTYPE html>
<html>
	<head>
		<meta charset='utf-8'>
		<title>Photograph - Регистрация</title>
		<link rel='stylesheet' href='/style/style.css'>
		<script src='/config/config.js'></script>
		<script src='/libs/cookie.js'></script>
	</head>
	<body onload="load()">
		<iframe width="30%" height="40%" style="border: none; position: absolute; top: 30%; left: 30%" 
				src="/captcha"></iframe>
		<script>
			function load() {
				document.body.style.opacity = '1';
			}
		
			window.addEventListener("message", function(event) {
				let data = event.data;
			
				if(data.indexOf("captchaTrue") == 0) {
					document.body.style.opacity = "0";
					setTimeout(() => location.href = "/1_api/cpt/confMail.php?1=<?php echo $_GET['1']?>&2=" + 
							   "<?php echo $_GET['2']?>", 500);
				}
			});
		</script>
	</body>
</html>