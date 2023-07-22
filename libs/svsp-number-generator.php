<?php

function strcut($strd, $n, $m) {
	$arrd = str_split($strd);
	$cutarr = [];
	for($i = $n; $i < $m + 1; $i++) {
		array_push($cutarr, $arrd[$i]);
	}
	return implode($cutarr);
}

function random($numlet, $fn, $mn = null, $mx = null) {
	if($numlet > 400) {
		return "false";
	}
	$do_time = substr(microtime(false), 0, 10);
	$arr = array(1, 2, 3, 4, 5, 6, 7, 8, 9);
	$sum = 0;
	for($i = 0; $i < count($arr); $i++) {
		$sum += $arr[$i];
	}
	$po_time = substr(microtime(false), 0, 10);
	$entropia1 = bcsub($po_time, $do_time, 6);

	$do_time = substr(microtime(false), 0, 10);
	$arr = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20);
	for($i = 0; $i < count($arr); $i++) {
		$sum *= $arr[$i];
	}
	$po_time = substr(microtime(false), 0, 10);
	$entropia2 = bcsub($po_time, $do_time, 6);

	$do_time = substr(microtime(false), 0, 10);
	$arr = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 
				 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20);
	for($i = 0; $i < count($arr); $i++) {
		$sum /= $arr[$i];
	}
	$po_time = substr(microtime(false), 0, 10);
	$entropia3 = bcsub($po_time, $do_time, 6);

	$do_time = substr(microtime(false), 0, 10);
	$arr = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 
				 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20);
	for($i = 0; $i < count($arr); $i++) {
		$sum = $arr[$i] * $sum % time();
	}
	$po_time = substr(microtime(false), 0, 10);
	$entropia4 = bcsub($po_time, $do_time, 6);

	$do_time = substr(microtime(false), 0, 10);
	for($i = 0; $i < 1000; $i++){
		$sum = $sum + ($sum + $sum) ** $sum;
	}
	$po_time = substr(microtime(false), 0, 10);
	$entropia5 = bcsub($po_time, $do_time, 6);

	$do_time = substr(microtime(false), 0, 10);
	for($i = 0; $i < 1000; $i++){
		$sum = $sum + ($sum * $sum) ** $sum;
	}
	$po_time = substr(microtime(false), 0, 10);
	$entropia6 = bcsub($po_time, $do_time, 6);

	$do_time = substr(microtime(false), 0, 10);
	for($i = 0; $i < 1000; $i++){
		$sum = $sum + ($sum * $sum) * $sum;
	}
	$po_time = substr(microtime(false), 0, 10);
	$entropia7 = bcsub($po_time, $do_time, 6);

	$str = hash("ripemd320", $entropia1).hash("ripemd320", $entropia2).hash("ripemd320", $entropia3).
					hash("ripemd320", $entropia4).hash("ripemd320", $entropia5).
					hash("ripemd320", $entropia6).hash("ripemd320", $entropia7);

	$sarr = str_split($str);
	$summ = 1;
	for($i = 0; $i < count($sarr); $i++){
		$summ = bcadd(ord($sarr[$i]) + rand(1, 256), bcmul($summ, strlen($summ)));
	}

	$arr = array(1, 3, 5, 7, 9, 2);
	$summ = bcsub($summ, bcmul(strlen($summ), $arr[rand(0, 5)]));

	if($mn == null and $mx == null) {
		$strt = rand(0, count($sarr) - $numlet);
		if($fn) $substr = substr($summ, $strt, rand(1, $numlet));
		else $substr = substr($summ, $strt, $numlet);
		if(str_split($substr)[0] == "0") $substr = rand(1, 9).substr($substr, 1);
		$substr = bcadd($substr, 1);
	    return $substr;
	} else {
		if(count(str_split($mx)) > $numlet or $numlet > count(str_split($mx))) {
			return "false";
		}
	
		if($mn < 0 or $mn > $mx or $mx < $mn) {
			return "false";
		}
	
		$strt = rand(0, count($sarr) - $numlet);
		if($numlet < 400) 
			if($fn) $substr = substr($summ, $strt, rand($mn, $numlet));
			else $substr = substr($summ, $strt, $numlet);
		while(bccomp($substr, $mx) == 1 or bccomp($substr, $mn) == -1) {
			$strt = rand(0, count($sarr) - $numlet);
			if($fn) $substr = substr($summ, $strt, rand($mn, $numlet));
			else $substr = substr($summ, $strt, $numlet);
			if(str_split($substr)[0] == "0") $substr = rand(1, str_split($mx)[0]).substr($substr, 1);
		}
		if(str_split($substr)[0] == "0") $substr = rand(1, str_split($mx)[0]).substr($substr, 1);
		while(bccomp($substr, $mx) == 1 or bccomp($substr, $mn) == -1) {
			$strt = rand(0, count($sarr) - $numlet);
			if($fn) $substr = substr($summ, $strt, rand($mn, $numlet));
			else $substr = substr($summ, $strt, $numlet);
			if(str_split($substr)[0] == "0") $substr = rand(1, str_split($mx)[0]).substr($substr, 1);
		}
		$substr = bcadd($substr, 1);
		return $substr;
	}

}

function millerRabinTest($n, $k = 1) {
	if($n == 2 or $n == 3) return true;

	if(bccomp($n, 2) == -1) return false;

	if(bcmod($n, 2, 0) == 0) return false;

	$d = bcsub($n, 1);
	$s = 0;

	while(bcmod($d, 2, 0) == 0) {
		$d = bcdiv($d, 2);
		$s = bcadd($s, 1);
	}

	for($i = 0; $i < $k; $i++) {
	
		$e = bcsub($n, 2);
		$r = 0;
		if(strlen(getrandmax()) < strlen($e)) {
			$r = rand(2, getrandmax());
		} else {
			$r = rand(2, (int) $e);
		}
		$x = bcpowmod($r, $d, $n);
	
		if(bccomp($x, 1) == 0 or $x == bcsub($n, 1)) continue;
	
		for($f = 0; bccomp($f, bcsub($s, 1)) == -1; $f++) {
			$x = bcpowmod($x, 2, $n);
			if(bccomp($x, 1) == 0) return false;
			if(bccomp($x, bcsub($n, 1)) == 0) break;
		}
	
		if(bccomp($x, bcsub($n, 1)) != 0) return false;
	}

	return true;
}

function primeRandom($numlet, $fn, $mn = null, $mx = null) {
	$x = random($numlet, $fn, $mn, $mx);
	while(!millerRabinTest($x, 16)) {
		$x = random($numlet, $fn, $mn, $mx);
	}
	return $x;
}
?>
