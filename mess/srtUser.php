<!DOCTYPE html>
<html>
<script src='/config/config.js'></script>
<script>
window.parent.postMessage("srtUserFrameLoad");

let htrq = new XMLHttpRequest();
htrq.open('GET', apiServer + '/api/searchUsers.php?srch=<?php echo $_GET["srch"] ?>', true);
htrq.send();

htrq.onload = function() {

	if(htrq.status == 502) {
		htrq.status = 0;
		htrq.abort();
		htrq.open('GET', apiServer + '/api/searchUsers.php?srch=<?php echo $_GET["srch"] ?>', true);
		htrq.send();
		return;
	}

	let numr = htrq.response.split('\n', 2);
	window.parent.postMessage("srch\n" + numr[0] + "\n" + numr[1]);
}
</script>
</html>