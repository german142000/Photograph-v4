<?php
if(isset($_GET['path']) && !isset($_GET['ip'])) $path = "location.href = '/".$_GET['path']."'";
else if(isset($_GET['ip']) && isset($_GET['1']) && isset($_GET['2'])) 
	$path = "location.href = '".$_GET['ip']."?1=".$_GET['1']."&2=".$_GET['2']."'";
else if(isset($_GET['ip']) && isset($_GET['1']) && !isset($_GET['2'])) 
	$path = "location.href = '".$_GET['ip']."?1=".$_GET['1']."'";
else if(isset($_GET['ip']) && !isset($_GET['1']) && !isset($_GET['2'])) 
	$path = "location.href = '".$_GET['ip']."'";
else if(isset($_COOKIE['session']) && isset($_COOKIE['strs'])) $path = "location.href = \"/main\"";
else $path = "location.href = \"/auth\"";
if(isset($_GET['nc'])) {
	$trs = $_COOKIE['trusted'];
	if(isset($_GET['path'])) $prt = "?path=".$_GET['path'];
	$script = <<<TBL
	location.href = '/trustedKey/updateKey.php$prt';
TBL;
	$return = $script;
} else $return = $path;
?>
<!DOCTYPE html>
<html>
<head>
<title>Photograph</title>
<meta charset="UTF-8">
<style> body {background: #000} </style>
</head>
<body>
<script src="libs/check_certificate_svsp_certificate_system.js"></script>
<script src="libs/cookie.js"></script>
<script src='/config/config.js'></script>
<script>
function checkCertDone(bl){
	if(bl) {
		let iv = [getRandomInt(9), getRandomInt(9), getRandomInt(9), getRandomInt(9),
                getRandomInt(9), getRandomInt(9), getRandomInt(9), getRandomInt(9),
                getRandomInt(9), getRandomInt(9), getRandomInt(9), getRandomInt(9),
                getRandomInt(9), getRandomInt(9), getRandomInt(9), getRandomInt(9)
        ];
		
		let htrqw = new XMLHttpRequest();
        htrqw.open('GET', apiServer + '/cid/cid.php?1=' + JSON.stringify(iv), true);
        htrqw.send();

        htrqw.onload = function() {

			if(htrqw.status == 502) {
				htrqw.send();
				return;
			}

			let numr = htrqw.response.split('\n', 3);
			console.log(numr[1]);	
			setCookie("trusted", numr[1], 0);
			setCookie("trs", JSON.stringify(iv), 0);
			<?php echo $return?>
		}
	}
}

function checkCert(arr){
	checkSCRTcertificate(arr, certCenter, checkCertDone);
}

getSCRTcertificate(checkCert, apiServer + "/scert");

</script>
</body>
</html>