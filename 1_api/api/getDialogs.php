<?php
$ss = $_GET['3'];
$st = $_GET['4'];
$ui = $_GET['5'];
$nk = $_GET['6'];
$ts = $_GET['7'];
$tr = $_GET['8'];
$pr = $_GET['9'];
$mx = $_GET['10'];
$iv = $_GET['11'];
include($_SERVER['DOCUMENT_ROOT']."/1_api/api/intrKey.php");
if($uskey) {
	$dls = scandir($_SERVER['DOCUMENT_ROOT']."/1_api/users/".$ui."/dialogs/");
	if(count($dls) == 2) { 
		echo "none"; 
		exit(0);
	} else {
		$mysql = mysqli_connect("sql104.unaux.com", "unaux_33957416", "m9c9q8uz79", "unaux_33957416_apiphotogp");
		if(mysqli_connect_error() == null) {
			$dls = array_splice($dls, 2);
			$nckm = [];
			$ids = [];
			$numMess = [];
			for($i = 0; $i < count($dls); $i++) {
				$id = str_replace('.dlg', '', $dls[$i]);
				$did = bcadd($id, $ui);
				$query = <<<TBL
		SELECT nickname FROM users WHERE photo_id = '$id'
TBL;
				$resw = mysqli_fetch_array(mysqli_query($mysql, $query));
				$query = <<<TBL1
		  SELECT count(*) FROM dialog_$did
TBL1;
				$row = mysqli_fetch_row(mysqli_query($mysql, $query))[0];
				array_push($nckm, $resw[0]);
				array_push($ids, $id);
				array_push($numMess, $row);
			}
			echo json_encode($ids)."\n", json_encode($nckm)."\n",  json_encode($numMess);
			exit(0);
		}
	}
} else {
	if($errno == "user session false") {
		setcookie('nickname', null, -1, '/');
		setcookie('uid', null, -1, '/');
		setcookie('session', null, -1, '/');
		setcookie('strs', null, -1, '/');
		//echo "<script> window.parent.postMessage('userSessionFalse') </script>";
		echo "101\n", $errno; //code 101 - ошибка сессии пользователя
		exit(0);
	}
	if($errno == "trusted false") {
		setcookie('trusted', null, -1, '/');
		setcookie('trs', null, -1, '/');
		//echo "<script> window.parent.postMessage('trustedFalse') </script>";
		echo "102\n", $errno; //code 102 - ошибка доверенного домена
		exit(0);
	}
}
?>