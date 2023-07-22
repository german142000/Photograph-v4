<!DOCTYPE html>
<html>
<head>
<title>Photograph - Главная страница</title>
<meta charset="UTF-8">
<link rel='stylesheet' href='/style/style.css'>
</head>
<iframe id="trsKey" style="position: absolute; top: -200px; opacity: 0; z-index: -1" src="/trustedKey/key.php"></iframe>
<body onload="bodyLoad()">
<script src="/libs/cookie.js"></script>
<script src='/config/config.js'></script>
<p class='mainLogo'>Photograph</p>
<p class='paragraph labelButton' style='margin-left: 80px; text-shadow: 0 0 7px #eee, 0 0 10px #eee, 0 0 21px #eee;
										cursor: default'>Главная страница</p><br>
<p id='dp' class='paragraph labelButton' style='margin-left: 80px;' onclick="toReg()">Загрузить фотографии</p><br>
<p class='paragraph labelButton' style='margin-left: 80px;' onclick="toMess()">Мессенджер</p><br>
<p class='paragraph labelButton' style='margin-left: 80px;' onclick="toReg()">Мои фотографии</p><br>
<p id="nick" class='littleLogo2 labelButton' style='position: absolute; right: 80px; top: 30px' onclick="showMenu()">
    <?php echo $_COOKIE['nickname']?>
</p>
<center>
    <p class="littleLogo1" style="margin: 3px; text-shadow: 0 0 7px #eee, 0 0 10px #eee, 0 0 21px #eee;">Раздел в разработке<br>
        <p class="littleLogo2" style="margin: 3px">Скоро здесь будет что-то интересное</p>
    </p>
</center>
<script>
    
	function bodyLoad() {
		document.body.style.opacity = '1';
	}

	function toMess() {
		document.body.style.opacity = '0';
		setTimeout(() => location.href = "/mess", 500);
	}
	
		window.addEventListener("message", function(event) {
	
		let data = event.data;
		
		if(data.indexOf("user session false") == 0) {
			console.log("101");
			eraseCookie("uid");
			eraseCookie("nickname");
			eraseCookie("session");
			eraseCookie("strs");
			eraseCookie("trusted");
			eraseCookie("trs");
			location.href = "/auth";
		}
		
		if(data.indexOf("trusted false") == 0) {
			eraseCookie("trusted");
			eraseCookie("trs");
			location.href = "/index.php?nc=true&path=main";
		}
	});
	
	let umenu = false;
	
	function showMenu() {
	    if(!umenu){
	        let mdv = document.createElement('div');
	        mdv.style.textAlign = "right";
	        mdv.style.padding = "3px";
	        mdv.style.paddingLeft = "16px";
	        mdv.style.paddingRight = "16px";
	        mdv.style.transitionDuration = "0.3s";
    	    mdv.style.display = "inline-block";
	        mdv.style.opacity = "0";
	        mdv.id = "mdv";
	        mdv.style.position = "absolute";
	        mdv.style.top = "70px";
	        mdv.style.right = "80px";
	        mdv.style.borderColor = "#555";
	        mdv.style.background = "#333";
	        let br = document.createElement('br');
	        let psett = document.createElement('p');
	        psett.className = "paragraph labelButton";
	        psett.innerText = "Настройки";
	        let pexit = document.createElement('p');
	        pexit.className = "paragraph labelButton";
	        pexit.innerText = "Выход";
	        pexit.onclick = function(){
	            eraseCookie("uid");
		    	eraseCookie("nickname");
		    	eraseCookie("session");
		    	eraseCookie("strs");
		    	eraseCookie("trusted");
		    	eraseCookie("trs");
		    	location.href = "/auth";
	        }
	        mdv.appendChild(psett);
	        mdv.appendChild(br);
	        mdv.appendChild(pexit);
	        document.body.appendChild(mdv);
	        setTimeout(() => document.getElementById("mdv").style.opacity = "1", 200);
	        umenu = true;
	    }
	}
	
	document.addEventListener('click', function(e) {
        if (e.target.id != 'mdv' && e.target.id != 'nick' ) {
            umenu = false;
            document.getElementById("mdv").style.opacity = "0";
            setTimeout(() => document.body.removeChild(document.getElementById("mdv")), 300);
        }
    });
	
</script>
</body>
</html>