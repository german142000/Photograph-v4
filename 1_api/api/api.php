<?php
include($_SERVER["DOCUMENT_ROOT"]."/1_api/libs/svsp-number-generator.php");

$smNum = primeRandom(16, false);
$root = rand(3, 20);

$key = fopen($_SERVER["DOCUMENT_ROOT"] . "/1_api/scert/cert/key.txt", "r");
$mix = explode(".", bcmod(bcpow((string)$root, (string) fgets($key), 1000), (string)$smNum, 1))[0];
fclose($key);

echo $smNum."\n", $root."\n", $mix."\n";
?>