<!DOCTYPE html>
<html>
<script src='/config/config.js'></script>
<script src='/libs/aes.js'></script>
<script src='/libs/md5.js'></script>
<script src='/libs/bigInteger.js'></script>
<script>
window.parent.postMessage("trsKeyLoad");
function getRandomInt(max) {
	return Math.floor(Math.random() * max);
}

let htrq = new XMLHttpRequest();
htrq.open('GET', apiServer + '/api/api.php', true);
htrq.send();

htrq.onload = function() {

	let numr = htrq.response.split('\n', 4);
	let prime = numr[0];
	let root = numr[1];
	let serverMix = numr[2];
			
	let now = new Date();
	let secret = JSBI.BigInt(getRandomInt(9999));
	let mix = JSBI.remainder(JSBI.exponentiate(JSBI.BigInt(root), secret), JSBI.BigInt(prime));
	let shdkey = JSBI.remainder(JSBI.exponentiate(JSBI.BigInt(serverMix), secret), JSBI.BigInt(prime));
	let key = md5(shdkey.toString()).substring(0, 16);

	let iv = [getRandomInt(9), getRandomInt(9), getRandomInt(9), getRandomInt(9),
		  	getRandomInt(9), getRandomInt(9), getRandomInt(9), getRandomInt(9),
		  	getRandomInt(9), getRandomInt(9), getRandomInt(9), getRandomInt(9),
		  	getRandomInt(9), getRandomInt(9), getRandomInt(9), getRandomInt(9)];
		
	let SS = '<?php echo $_COOKIE['session']?>';
	let SSresult;
	let ST = '<?php echo $_COOKIE['strs']?>';
	let STresult;
	let UI = '<?php echo $_COOKIE['uid']?>';
	let UIresult;
	let NK = '<?php echo $_COOKIE['nickname']?>';
	let NKresult;
	let TS = '<?php echo $_COOKIE['trusted']?>';
	let TSresult;
	let TR = '<?php echo $_COOKIE['trs']?>';
	let TRresult;

	let strByteArraySS = [];
	for(let i = 0; i < SS.length; i++) strByteArraySS.push(SS.charCodeAt(i));
	SSresult = JSON.stringify(slowAES.encrypt(strByteArraySS, slowAES.modeOfOperation.OFB, key, iv));

	let strByteArrayST = [];
	for(let i = 0; i < ST.length; i++) strByteArrayST.push(ST.charCodeAt(i));
	STresult = JSON.stringify(slowAES.encrypt(strByteArrayST, slowAES.modeOfOperation.OFB, key, iv));

	let strByteArrayUI = [];
	for(let i = 0; i < UI.length; i++) strByteArrayUI.push(UI.charCodeAt(i));
	UIresult = JSON.stringify(slowAES.encrypt(strByteArrayUI, slowAES.modeOfOperation.OFB, key, iv));

	let strByteArrayNK = [];
	for(let i = 0; i < NK.length; i++) strByteArrayNK.push(NK.charCodeAt(i));
	NKresult = JSON.stringify(slowAES.encrypt(strByteArrayNK, slowAES.modeOfOperation.OFB, key, iv));

	let strByteArrayTS = [];
	for(let i = 0; i < TS.length; i++) strByteArrayTS.push(TS.charCodeAt(i));
	TSresult = JSON.stringify(slowAES.encrypt(strByteArrayTS, slowAES.modeOfOperation.OFB, key, iv));

	let strByteArrayTR = [];
	for(let i = 0; i < TR.length; i++) strByteArrayTR.push(TR.charCodeAt(i));
	TRresult = JSON.stringify(slowAES.encrypt(strByteArrayTR, slowAES.modeOfOperation.OFB, key, iv));


	let htrqr = new XMLHttpRequest();
	htrqr.open('GET', apiServer + '/api/key.php?3='
	       + SSresult + '&4=' + STresult + '&5=' + UIresult + '&6=' + NKresult +
			  '&7=' + TSresult + '&8=' + TRresult + '&9=' + prime + '&10=' + mix.toString() + 
			  '&11=' + JSON.stringify(iv), true);
	htrqr.send();

	htrqr.onload = function() {
		let numr = htrqr.response.split('\n', 3);
		window.parent.postMessage(numr[1]);
	}

}
</script>
</html>