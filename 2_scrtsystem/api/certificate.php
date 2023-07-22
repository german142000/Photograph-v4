<?php
ini_set("display_errors", 0);
ini_set("display_startup_errors", 0);
error_reporting(E_ALL);

include($_SERVER["DOCUMENT_ROOT"]."/2_scrtsystem/libs/aes_fast.php");

$aes_product_id = $_GET["1"];
$aes_certificate_id = $_GET["2"];
$aes_date = $_GET["3"];
$aes_date_trem = $_GET["4"];
$aes_c_prime = $_GET["5"];
$aes_c_root = $_GET["6"];
$aes_hes1 = $_GET["7"];
$aes_hes2 = $_GET["8"];
$aes_hes3 = $_GET["9"];
$aes_hes4 = $_GET["10"];

$laes_product_id = $_GET["11"];
$laes_certificate_id = $_GET["12"];
$laes_date = $_GET["13"];
$laes_date_trem = $_GET["14"];
$laes_c_prime = $_GET["15"];
$laes_c_root = $_GET["16"];
$laes_hes1 = $_GET["17"];
$laes_hes2 = $_GET["18"];
$laes_hes3 = $_GET["19"];
$laes_hes4 = $_GET["20"];

$mix = $_GET["21"];
$prime = $_GET["22"];
$iv = json_decode($_GET["23"]);

$key = fopen($_SERVER["DOCUMENT_ROOT"]."/2_scrtsystem/key/key.txt", "r");
$code = explode(".", bcmod(bcpow((string) $mix, (string) fgets($key), 1000), 
				 $prime, 1))[0];
fclose($key);

$scode = substr(md5($code), 0, 16);

$aes = new AES();

$aes_product_id = $aes -> decrypt(json_decode($aes_product_id), $laes_product_id, 
						$aes::modeOfOperation_OFB, str_split($scode), 16, $iv);
$aes_certificate_id = $aes -> decrypt(json_decode($aes_certificate_id), $laes_certificate_id, 
						$aes::modeOfOperation_OFB, str_split($scode), 16, $iv);
$aes_date = $aes -> decrypt(json_decode($aes_date), $laes_date, 
						$aes::modeOfOperation_OFB, str_split($scode), 16, $iv);
$aes_date_trem = $aes -> decrypt(json_decode($aes_date_trem), $laes_date_trem, 
						$aes::modeOfOperation_OFB, str_split($scode), 16, $iv);
$aes_c_prime = $aes -> decrypt(json_decode($aes_c_prime), $laes_c_prime, 
						$aes::modeOfOperation_OFB, str_split($scode), 16, $iv);
$aes_c_root = $aes -> decrypt(json_decode($aes_c_root), $laes_c_root, 
						$aes::modeOfOperation_OFB, str_split($scode), 16, $iv);
$aes_hes1 = $aes -> decrypt(json_decode($aes_hes1), $laes_hes1, 
						$aes::modeOfOperation_OFB, str_split($scode), 16, $iv);
$aes_hes2 = $aes -> decrypt(json_decode($aes_hes2), $laes_hes2, 
						$aes::modeOfOperation_OFB, str_split($scode), 16, $iv);
$aes_hes3 = $aes -> decrypt(json_decode($aes_hes3), $laes_hes3, 
						$aes::modeOfOperation_OFB, str_split($scode), 16, $iv);
$aes_hes4 = $aes -> decrypt(json_decode($aes_hes4), $laes_hes4, 
						$aes::modeOfOperation_OFB, str_split($scode), 16, $iv);

$strA = [];
$strB = [];
$strC = [];
$strD = [];
$strE = [];
$strF = [];
$strG = [];
$strH = [];
$strI = [];
$strK = [];

for($i = 0; $i < count($aes_product_id); $i++){
array_push($strA, mb_chr($aes_product_id[$i], "UTF-8"));
}

for($i = 0; $i < count($aes_certificate_id); $i++){
array_push($strB, mb_chr($aes_certificate_id[$i], "UTF-8"));
}

for($i = 0; $i < count($aes_date); $i++){
array_push($strC, mb_chr($aes_date[$i], "UTF-8"));
}

for($i = 0; $i < count($aes_date_trem); $i++){
array_push($strD, mb_chr($aes_date_trem[$i], "UTF-8"));
}

for($i = 0; $i < count($aes_c_prime); $i++){
array_push($strE, mb_chr($aes_c_prime[$i], "UTF-8"));
}

for($i = 0; $i < count($aes_c_root); $i++){
array_push($strF, mb_chr($aes_c_root[$i], "UTF-8"));
}

for($i = 0; $i < count($aes_hes1); $i++){
array_push($strG, mb_chr($aes_hes1[$i], "UTF-8"));
}

for($i = 0; $i < count($aes_hes2); $i++){
array_push($strH, mb_chr($aes_hes2[$i], "UTF-8"));
}

for($i = 0; $i < count($aes_hes3); $i++){
array_push($strI, mb_chr($aes_hes3[$i], "UTF-8"));
}

for($i = 0; $i < count($aes_hes4); $i++){
array_push($strK, mb_chr($aes_hes4[$i], "UTF-8"));
}

$product_id = implode($strA);
$certificate_id = implode($strB);
$date = implode($strC);
$date_trem = implode($strD);
$c_prime = implode($strE);
$c_root = implode($strF);
$hes1 = implode($strG);
$hes2 = implode($strH);
$hes3 = implode($strI);
$hes4 = implode($strK);

$h_cert_data = md5($product_id).
			md5($certificate_id).
			md5($date).
			md5($date_trem).
			md5($c_prime).
			md5($c_root).
			md5($hes1).
			md5($hes2).
			md5($hes3).
			md5($hes4);

$mysql = mysqli_connect("sql104.unaux.com", "unaux_33957416", "m9c9q8uz79", "unaux_33957416_scrtsystem");
$query = "SELECT product_term FROM products WHERE product_xes = \"".$product_id."\"";
$product_term = mysqli_fetch_row(mysqli_query($mysql, $query))[0];

if(file_get_contents($_SERVER["DOCUMENT_ROOT"]."/2_scrtsystem/sertificates/hes_cert/".$certificate_id.".scrt") == $h_cert_data 
   and (int) $date_trem > time() and (int) $product_term > time()) 
	echo "true";
else
	echo "false";

?>