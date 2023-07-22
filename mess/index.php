<!DOCTYPE html>
<html>
<head>
<title>Photograph - Мессенджер</title>
<meta charset="UTF-8">
<link rel='stylesheet' href='/style/style.css'>
<style>
.srchd {
	display: flex;
	flex-direction: row;
	opacity: 1;
	cursor: pointer;
	margin: 1px;
	margin-left: 3px;
	transition-duration: 0.3s;
}

.srchd:hover {
    margin: 3px;
    margin-left: 5px;
	background: #333333;
	border-color: #555555;
}

.srchn {
	margin: 3px; 
}

.srchid {
	font-size: 10px; 
	margin: 3px; 
	margin-bottom: 6px;
}

.srchimg {
	width: 10%;
	height: 10%;
	margin: 3%;
	border-radius: 50%
}

.sb {
	opacity: 1;
	cursor: pointer;
	transition-duration: 0.3s;
}

.sb:hover {
	background: #333333;
	border-color: #555555;
}

.msbox {
	display: flex; 
	position: absolute; 
	height: 70%; 
	justify-content: space-between;
	align-content: stretch
}

.msboxone {
	width: 70%;
	display: flex;
	flex-direction: column;
}

.msboxtwo {
	width: 30%;
	display: flex;
	flex-direction: column;
}

.ipn {
	display: flex;
	flex-direction: row;
}

.mymesFlex {
    opacity: 0;
	border: 0px;
	padding: 5px;
	display: flex;
	flex-direction: row;
	justify-content: flex-end;
	transition-duration: 0.3s;
}

.mesFlex {
    opacity: 0;
	border: 0px;
	padding: 5px;
	display: flex;
	flex-direction: row;
	justify-content: flex-start;
	transition-duration: 0.3s;
}

.mWindow {
    transition-duration: 0.5s;
	overflow-y: scroll;
}

.taleft {
	text-align: left;
}

.taright {
	text-align: right;
}

</style>
</head>
<body onload="bodyLoad()">
<script src="/libs/cookie.js"></script>
<script src='/config/config.js'></script>
<script src='/libs/aes.js'></script>
<script src='/libs/md5.js'></script>
<script src='/libs/bigInteger.js'></script>
<p id="logo" class='mainLogo'>Photograph</p>
<p class='paragraph labelButton' style='margin-left: 80px;' onclick="toMain()">Главная страница</p><br>
<p id='dp' class='paragraph labelButton' style='margin-left: 80px;' onclick="toReg()">Загрузить фотографии</p><br>
<p class='paragraph labelButton' style='margin-left: 80px; text-shadow: 0 0 7px #eee, 0 0 10px #eee, 0 0 21px #eee;
										cursor: default'>Мессенджер</p><br>
<p class='paragraph labelButton' style='margin-left: 80px;' onclick="toReg()">Мои фотографии</p><br>
<p id="nick" class='littleLogo2 labelButton' style='position: absolute; right: 80px; top: 30px' onclick="showMenu()">
    <?php echo $_COOKIE['nickname']?>
</p>
<div id="messageBox" class="msbox" style="border: none">
	<div class="msboxone">
		<div style="padding: 3px; font-size: 14px;">
			<p id="nck" style="transition-duration: 0.3s; padding: 0; margin: 0; user-select: text">Мессенджер</p>
		</div>
		<img id="phbar2"
	        style="position: absolute; width: 7%; margin-left: 30%; margin-top: 20%; transition-duration: 0.3s; padding: 1px; opacity: 0"
			 												src="/imgs/phbar.gif">
		<div id="mWin" class="mWindow" style="flex: 1">
			
		</div>
		<div class="ipn">
			<input id="sendMess" style="padding: 3px; font-size: 14px; flex: 1" type="text"
			   placeholder="Выберите диалог" disabled>
			<div id="sendButton" class="sb" style="padding: 2.8px; font-size: 14px"
				 onclick="sendMessage()">⇰</div>
		</div>
	</div>
	<div class="msboxtwo">
		<input id="searchBox" style="padding: 3px; font-size: 14px;" type="text" 
	   		placeholder="Поиск собеседника" onkeyup="searchUsers()">
		<img id="phbar"
	style="position: absolute; width: 7%; margin-top: 4%; margin-left: 11%; transition-duration: 0.3s; padding: 1px"
			 												src="/imgs/phbar.gif">
		<div id="dialogsBox" style="overflow-y: scroll; transition-duration: 0.3s; flex: 1;">
		</div>
	</div>
</div>
<iframe id="getDialogs" style="position: absolute; top: -200px; opacity: 0; z-index: -1"></iframe>
<iframe id="srtUser" style="position: absolute; top: -200px; opacity: 0; z-index: -1"></iframe>
<iframe id="crtDialog" style="position: absolute; top: -200px; opacity: 0; z-index: -1"></iframe>
<iframe id="getDialog" style="position: absolute; top: -200px; opacity: 0; z-index: -1"></iframe>
<iframe id="smif" style="position: absolute; top: -200px; opacity: 0; z-index: -1"></iframe>
<iframe id="csm" style="position: absolute; top: -200px; opacity: 0; z-index: -1"></iframe>
<iframe id="trsKey" style="position: absolute; top: -200px; opacity: 0; z-index: -1" src="/trustedKey/key.php"></iframe>
<script>

	let api = false;
	let prime;
	let root;
	let serverMix;
	let mix;
	let key;
	let iv;
	let crpm;

	let selectUID = "none";
	let UIDnumMess = "none";
	let selectName = "none";
	
	let nextMess = false;

	let trsKeyFrameLoad = false;
	let getDialogsFrameLoad = false;
	let srtUserFrameLoad = false;
	let getDialogFrameLoad = false;
	let smifFrameLoad = false;
	let csmFrameLoad = false;
	let crtDialogFrameLoad = false;

	document.getElementById("trsKey").onload = function() {
		setTimeout(() => {if(!trsKeyFrameLoad) document.getElementById("trsKey").src = document.getElementById("trsKey").src; else trsKeyFrameLoad = false}, 200);
	}

	document.getElementById("getDialogs").onload = function() {
		setTimeout(() => {if(!getDialogsFrameLoad) document.getElementById("getDialogs").src = document.getElementById("getDialogs").src; else getDialogsFrameLoad = false}, 200);	
	}

	document.getElementById("srtUser").onload = function() {
		setTimeout(() => {if(!srtUserFrameLoad) document.getElementById("srtUser").src = document.getElementById("srtUser").src; else srtUserFrameLoad = false}, 200);	
	}

	document.getElementById("getDialog").onload = function() {
		setTimeout(() => {if(!getDialogFrameLoad) document.getElementById("getDialog").src = document.getElementById("getDialog").src; else getDialogFrameLoad = false}, 200);	
	}

	document.getElementById("smif").onload = function() {
		setTimeout(() => {if(!smifFrameLoad) document.getElementById("smif").src = document.getElementById("smif").src; else smifFrameLoad = false}, 200);	
	}

	document.getElementById("csm").onload = function() {
		setTimeout(() => {if(!csmFrameLoad) document.getElementById("csm").src = document.getElementById("csm").src; else csmFrameLoad = false}, 200);	
	}

	document.getElementById("crtDialog").onload = function() {
		setTimeout(() => {if(!crtDialogFrameLoad) document.getElementById("crtDialog").src = document.getElementById("crtDialog").src; else crtDialogFrameLoad = false}, 200);	
	}

	document.getElementById("sendMess").onkeyup = 
	function(event) {
		if(event.key == "Enter") sendMessage();
		else {
			let strByteArrayMess = [];
			let mess = document.getElementById('sendMess').value;
			for(let i = 0; i < mess.length; i++) strByteArrayMess.push(mess.charCodeAt(i));
			crpm = slowAES.encrypt(strByteArrayMess, slowAES.modeOfOperation.OFB, key, iv);
		}
	}
		
	function getRandomInt(max) {
		return Math.floor(Math.random() * max);
	}

	function bodyLoad() {
		document.body.style.opacity = '1';
		document.getElementById("getDialogs").src = "getDialogs.php";
	}

	function toMain() {
		document.body.style.opacity = '0';
		setTimeout(() => location.href = "/main", 500);
	}

	function searchUsers() {
		document.getElementById("phbar").style.opacity = "1";
		document.getElementById("dialogsBox").style.opacity = "0";
		if(document.getElementById("searchBox").value != "")
		document.getElementById("srtUser").src = "srtUser.php?srch=" +
				document.getElementById("searchBox").value;
		else document.getElementById("getDialogs").src = "getDialogs.php";
	}

	let mrg = 80 + document.getElementById("dp").offsetWidth;
	let logoh = 60 + document.getElementById("logo").offsetHeight;

	document.getElementById("messageBox").style.width = "calc(90% - " + mrg + "px)";
	document.getElementById("messageBox").style.left = "calc(5% + " + mrg + "px)";
	document.getElementById("messageBox").style.top = logoh + "px";

	window.addEventListener("message", function(event) {
	
		let data = event.data;
	
		if(data.indexOf("srch") == 0) {
			let arr = data.split('\n', 3);
			showSearchUsers(arr[2], arr[1]);
		}
	
		if(data.indexOf("102") == 0) {
			console.log("102");
			eraseCookie("trusted");
			eraseCookie("trs");
			location.href = "/index.php?nc=true&path=mess";
		}
	
		if(data.indexOf("101") == 0) {
			console.log("101");
			eraseCookie("uid");
			eraseCookie("nickname");
			eraseCookie("session");
			eraseCookie("strs");
			eraseCookie("trusted");
			eraseCookie("trs");
			location.href = "/auth";
		}
	
		if(data.indexOf("dls") == 0) {
			let numrc = data.split("\n", 5)
			if(numrc[2] != 'none') showUsersDialogs(numrc[3], numrc[2], numrc[4]);
			else {
				document.getElementById("dialogsBox").innerHTML = "";
				document.getElementById("dialogsBox").style.opacity = "1";
			}
		}
	
		if(data.indexOf("dlg") == 0) {
			let numrc = data.split("\n", 5)
			showDialogData(numrc[1], numrc[2], numrc[3]);
		}
	
		if(data.indexOf("usrch") == 0) {
			let numrc = data.split("\n", 2)
			let chusers = JSON.parse(numrc[1]);
			for(let i = 0; i < chusers.length; i++) {
				if(chusers[i] == selectUID) {
				    nextMess = true;
				    showDialog(selectName, selectUID, UIDnumMess, false);
				}
			}
		}
		
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
			location.href = "/index.php?nc=true&path=mess";
		}

		if(data.indexOf("trsKeyLoad")) {
			trsKeyFrameLoad = true;
		}

		if(data.indexOf("getDialogsFrameLoad")) {
			getDialogsFrameLoad = true;
		}

		if(data.indexOf("srtUserFrameLoad")) {
			srtUserFrameLoad = true;
		}

		if(data.indexOf("getDialogFrameLoad")) {
			getDialogFrameLoad = true;
		}

		if(data.indexOf("smifFrameLoad")) {
			smifFrameLoad = true;
		}

		if(data.indexOf("crtDialogFrameLoad")) {
			crtDialogFrameLoad = true;
		}

	});

	function showSearchUsers(nick, ids) {
		document.getElementById("dialogsBox").style.opacity = "0";
		setTimeout(function() {
			document.getElementById("dialogsBox").innerHTML = "";
			let nickArr = JSON.parse(nick);
			let idArr = JSON.parse(ids);
			for(let i = 0; i < nickArr.length; i++) {
				if(nickArr[i] != "end") {
					let dv = document.createElement('div');
					dv.className = "srchd";
					dv.id = "sd" + i;
					let img = document.createElement('img');
					img.className = 'srchimg';
					img.src = "/imgs/ptz.jpg";
					dv.appendChild(img);
					let dvi = document.createElement('div');
					dvi.style.width = "50%";
					dvi.style.border = '0px';
					dvi.style.background = '#00000000';
					let nick = document.createElement('p');
					nick.className = "srchn";
					nick.innerText = nickArr[i];
					dvi.appendChild(nick);
					let ids = document.createElement('p');
					ids.className = "srchid";
					ids.innerText = "id: " + idArr[i];
					dv.onclick = function() {
					    document.getElementById('searchBox').value = '';
						document.getElementById('crtDialog').src = "createDialog.php?1=" + '<?php echo $_COOKIE['uid']?>'+
							"&2=" + idArr[i];
						document.getElementById("dialogsBox").style.opacity = "0";
						document.getElementById("getDialogs").src = "getDialogs.php";
					}
					dvi.appendChild(ids);
					dv.appendChild(dvi);
					document.getElementById("dialogsBox").appendChild(dv);
					document.getElementById("sd" + i).onload = function() {
					document.getElementById("sd" + i).style.opacity = "1";
					}
				}
			}
			document.getElementById("dialogsBox").style.opacity = "1";
		}, 300);
		document.getElementById("phbar").style.opacity = "0";
	}

	function showUsersDialogs(nick, ids, numMess) {
		document.getElementById("dialogsBox").style.opacity = "0";
		setTimeout(function() {
			document.getElementById("dialogsBox").innerHTML = "";
			let nickArr = JSON.parse(nick);
			let idArr = JSON.parse(ids);
			let numArr = JSON.parse(numMess);
			for(let i = 0; i < nickArr.length; i++) {
				if(nickArr[i] != "end") {
					let dv = document.createElement('div');
					dv.className = "srchd";
					dv.id = "sd" + i;
					let img = document.createElement('img');
					img.className = 'srchimg';
					img.src = "/imgs/ptz.jpg";
					dv.appendChild(img);
					let dvi = document.createElement('div');
					dvi.style.width = "50%";
					dvi.style.border = '0px';
					dvi.style.background = '#00000000';
					let nick = document.createElement('p');
					nick.className = "srchn";
					nick.innerText = nickArr[i];
					dvi.appendChild(nick);
					let ids = document.createElement('p');
					ids.className = "srchid";
					ids.innerText = "id: " + idArr[i];
					dv.onclick = function() {
						showDialog(nickArr[i], idArr[i], numArr[i], true);
					}
					dvi.appendChild(ids);
					dv.appendChild(dvi);
					document.getElementById("dialogsBox").appendChild(dv);
					document.getElementById("sd" + i).onload = function() {
					document.getElementById("sd" + i).style.opacity = "1";
					}
				}
			}
			document.getElementById("dialogsBox").style.opacity = "1";
		}, 300);
		document.getElementById("phbar").style.opacity = "0";
	}

	function showDialog(name, id, nm, sn) {
	    document.getElementById("sendMess").disabled = false;
	    document.getElementById("sendMess").placeholder = "Сообщение"
	    if(!nextMess) {
	        document.getElementById("phbar2").style.opacity = '1';
	        let mes = document.querySelectorAll(".mymesFlex");
		    for(let i = 0; i < mes.length; i++) {
		        mes[i].style.opacity = '0';
		    }
		    mes = document.querySelectorAll(".mesFlex");
		    for(let i = 0; i < mes.length; i++) {
    		    mes[i].style.opacity = '0';
    		}
	    }
	    setTimeout(function() {
		    if(sn && name != selectName) {
		    	document.getElementById("nck").style.opacity = '0';
		    	setTimeout(function() {
		    		document.getElementById("nck").innerHTML = name;
	        		document.getElementById("nck").style.opacity = '1';
		    	}, 300);
		    }
		    selectUID = id;
		    UIDnumMess = nm;
		    selectName = name;
		    let sm = 0;
		    if(nm > 13) sm = nm - 12;
		    document.getElementById("getDialog").src = "getDialog.php?1=" + id + "&2=" + sm;
	    }, 500);
	}

	let htrq = new XMLHttpRequest();
	htrq.open('GET', apiServer + '/api/api.php', true);
	htrq.send();

	htrq.onload = function() {

		let numr = htrq.response.split('\n', 4);
		prime = numr[0];
		root = numr[1];
		serverMix = numr[2];

		let secret = JSBI.BigInt(getRandomInt(9999));
		mix = JSBI.remainder(JSBI.exponentiate(JSBI.BigInt(root), secret), JSBI.BigInt(prime));
		let shdkey = JSBI.remainder(JSBI.exponentiate(JSBI.BigInt(serverMix), secret), JSBI.BigInt(prime));
		key = md5(shdkey.toString()).substring(0, 16);

		iv = [getRandomInt(9), getRandomInt(9), getRandomInt(9), getRandomInt(9),
		  		  getRandomInt(9), getRandomInt(9), getRandomInt(9), getRandomInt(9),
		  		  getRandomInt(9), getRandomInt(9), getRandomInt(9), getRandomInt(9),
		  		  getRandomInt(9), getRandomInt(9), getRandomInt(9), getRandomInt(9)];
		
		api = true;
	
	}

	function sendMessage() {
		if(selectUID != "none") {
			document.getElementById("smif").src = "sendMessage.php?1=<?php echo $_COOKIE['uid']?>&2=" + selectUID +
			"&12=" + JSON.stringify(crpm) + "&13=" + mix.toString() + "&14=" + prime + "&15=" + JSON.stringify(iv);
		
			let vlt = document.getElementById("sendMess").value;
			document.getElementById("sendMess").value = "";
			let csmxhrx = new XMLHttpRequest();
		
			csmxhrx.open('GET',  "/csm/csm.php", true);
			csmxhrx.send();

			csmxhrx.onload = function() {
				let numr = csmxhrx.response.split('\n', 3);
				if(numr[1] == "202") {
					let fbx = document.createElement("div");
					fbx.className = 'mymesFlex';
					let mdv = document.createElement("div");
					mdv.style.padding = "3px";
					mdv.style.fontSize = "14px";
					let mss = document.createElement("p");
					mss.style.padding = "0";
					mss.style.margin = "0";
					mss.style.userSelect = "text";
					mss.innerText = vlt;
					mdv.appendChild(mss);
					fbx.appendChild(mdv);
					document.getElementById("mWin").appendChild(fbx);
					document.getElementById("mWin").scrollTop = document.getElementById("mWin").scrollHeight;
					let mes = document.querySelectorAll(".mymesFlex");
		            for(let i = 0; i < mes.length; i++) {
		                mes[i].style.opacity = '1';
		            }
				} else window.postMessage("usrch\n" + numr[1]);
			}
		}
	}

	function showDialogData(ddata, ids, date) {
		let data = JSON.parse(ddata); 
		let id = JSON.parse(ids);
		let dt = JSON.parse(date);
		let sendMess = 0;
		document.getElementById("mWin").innerHTML = "";
		for(let i = 0; i < data.length; i++) {
			let fbx = document.createElement("div");
			if(id[i] == "<?php echo $_COOKIE['uid']?>") fbx.className = 'mymesFlex';
			else fbx.className = 'mesFlex';
			if(nextMess) {
			    fbx.style.opacity = '1';
			}
			let mdv = document.createElement("div");
			mdv.style.padding = "3px";
			mdv.style.fontSize = "14px";
			let mss = document.createElement("p");
			mss.style.padding = "0";
			mss.style.margin = "0";
			mss.style.userSelect = "text";
			mss.innerText = data[i];
			mdv.appendChild(mss);
			let dte = new Date(dt[i]*1000);
			let time = document.createElement("p");
			time.style.padding = "3px";
			time.style.margin = "0";
			time.style.userSelect = "text";
			time.style.fontSize = "6px";
			let min = dte.getMinutes();
			if(min < 10) min = "0" + min.toString();
			time.innerText = dte.getHours() + ":" + min + ", " + dte.getDate() + "." + dte.getMonth() + "." + dte.getFullYear();
			if(id[i] == "<?php echo $_COOKIE['uid']?>") time.className = 'taright';
			else time.className = 'taleft';
			mdv.appendChild(time);
			fbx.appendChild(mdv);
			document.getElementById("mWin").appendChild(fbx);
		}	
		document.getElementById("mWin").scrollTop = document.getElementById("mWin").scrollHeight;
		document.getElementById("phbar2").style.opacity = '0';
		let mes = document.querySelectorAll(".mymesFlex");
		for(let i = 0; i < mes.length; i++) {
		    mes[i].style.opacity = '1';
		}
		mes = document.querySelectorAll(".mesFlex");
		for(let i = 0; i < mes.length; i++) {
		    mes[i].style.opacity = '1';
		}
		nextMess = false;
	}

	let csmxhr = new XMLHttpRequest();
	setInterval(function() {
		
		csmxhr.open('GET',  "/csm/csm.php", true);
		csmxhr.send();

		csmxhr.onload = function() {
			let numr = csmxhr.response.split('\n', 3);
			if(numr[1] != "202" && csmxhr.status != 502) window.postMessage("usrch\n" + numr[1]);
		}
	}, 10000);
	
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
        if (e.target.id != 'mdv' && e.target.id != 'nick' && document.getElementById("mdv") != null) {
            umenu = false;
            document.getElementById("mdv").style.opacity = "0";
            setTimeout(() => document.body.removeChild(document.getElementById("mdv")), 300);
        }
    });
    
</script>
</body>
</html>