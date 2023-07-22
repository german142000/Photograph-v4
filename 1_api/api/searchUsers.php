<?php
$srchrq = $_GET['srch'];
if(!isset($_GET['srch'])) {
	echo "false";
	exit(0);
}
$mysql = mysqli_connect("sql104.unaux.com", "unaux_33957416", "m9c9q8uz79", "unaux_33957416_apiphotogp");
if(mysqli_connect_error() == null) {
	$query = <<<TBL
	SELECT photo_id FROM users WHERE nickname LIKE '$srchrq%'
TBL;
	$res = mysqli_fetch_all(mysqli_query($mysql, $query));
	$ids = [];
	$nckm = [];
	for($i = 0; $i < count($res); $i++) {
		array_push($ids, $res[$i][0]);
		$id = $res[$i][0];
		$query = <<<TBL
		SELECT nickname FROM users WHERE photo_id = '$id'
TBL;
		$resw = mysqli_fetch_array(mysqli_query($mysql, $query));
		array_push($nckm, $resw[0]);
	}
	echo json_encode($ids)."\n", json_encode($nckm)."\n";

} else echo "false";
?>