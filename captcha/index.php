<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>CAPTCHA</title>
<style>

@font-face {
font-family: quick;
src: url(font/quick.ttf);
}

body {
	margin: 0;
	background: #000000;
	font-family: quick;
	color: #eeeeee;
	user-select: none;
  }

.mmt {
    margin-left: 60px;
    font-size: 12px;
  }

.mnt {
    margin-left: 60px;
    font-size: 12px;
    background: #222222;
    border-style: solid;
    border-width: 1px;
    border-color: #333333;
    color: #eeeeee;
    font-family: quick;
  }

.mnt:focus {
    outline-style: none;
    border-color: #555555;
  }

.but {
    margin-left: 60px;
    border: none;
    background: #222222;
    border-style: solid;
    border-width: 1px;
    border-color: #333333;
    color: #eeeeee;
    font-family: quick;
    cursor: pointer;
  }

.but:hover {
    background: #333333;
  }

.but:focus {
    outline-style: none;
    border-color: #555555;
  }

.captcha {
  margin-left: 60px;
}

</style>
</head>

<?php
function crt($let)
 {
  $dir = $_SERVER['DOCUMENT_ROOT'] . "/captcha/letters/" . $let . ".png";
  $img = imagecreatefrompng($dir);
  $rot = rand(-15, 15);
  $imgr = imagerotate($img, $rot, 0);
  $rot = rand(0, 255);
  return $imgr;
 }

function deleteNUll($str)
 {
  for ($i = 0;$i < 5;$i++)
   {
    if ($str[$i] == '0') $str[$i] = 'g';
    if ($str[$i] == 'o') $str[$i] = 'p';
   }
  return $str;
 }

function deleteDir($path)
 {
  if (is_dir($path) === true)
   {
    $files = array_diff(scandir($path) , array(
      '.',
      '..'
    ));
    foreach ($files as $file)
     {
      deleteDir(realpath($path) . '/' . $file);
     }
    return rmdir($path);
   }
  else if (is_file($path) === true)
   {
    return unlink($path);
   }
  return false;
 }

if (isset($_GET['cpt']) && isset($_GET['val']))
 {
  $f = $_GET['cpt'];
  $cd = md5($f . md5(strtolower($_GET['val'])) . md5("uytrtyuiutr") . $f);
  if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/captcha/img/" . $f . "/" . $cd))
   {
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/captcha/img/" . $f . "/" . $f . ".png"))
     {
      deleteDir($_SERVER['DOCUMENT_ROOT'] . "/captcha/img/" . $f);
      echo <<<CGR1
<p class="mmt" style="color:#eeeeee">phCAPTCHA</p>
<p id="nr" class="mmt">Вы не робот ✔</p>
<script>
setTimeout(function(){window.parent.postMessage("captchaTrue$f")}, 100);
</script>
CGR1;
     }
   }
  else
   {
    deleteDir($_SERVER['DOCUMENT_ROOT'].'/captcha/img/'.$f);
    $rand = rand(0, 100000000);
    $str = md5($rand);
    $rstr = substr($str, 26);
    $rt = "uytrtyuiutr";
    $rt = md5($rt);
    $rstr = str_replace('0', 'g', $rstr);
    $rstr = str_replace('o', 'p', $rstr);
    $mdstr = md5($rt . $rstr);
    $mdtxt = md5($mdstr . md5($rstr) . $rt . $mdstr);
    mkdir($_SERVER['DOCUMENT_ROOT'] . "/captcha/img/" . $mdstr);
    mkdir($_SERVER['DOCUMENT_ROOT'] . "/captcha/img/" . $mdstr . "/" . $mdtxt);
    $dr = $_SERVER['DOCUMENT_ROOT'] . "/captcha/img/" . $mdstr . "/" . $mdstr . ".png";
    $capt = imagecreatetruecolor(720, 152);
    $img1 = crt($rstr[0]);
    $img2 = crt($rstr[1]);
    $img3 = crt($rstr[2]);
    $img4 = crt($rstr[3]);
    $img5 = crt($rstr[4]);
    $img6 = crt($rstr[5]);
    imagecopy($capt, $img1, 0, 0, 0, 0, 88, 152);
    imagecopy($capt, $img2, 120, 0, 0, 0, 88, 152);
    imagecopy($capt, $img3, 240, 0, 0, 0, 88, 152);
    imagecopy($capt, $img4, 360, 0, 0, 0, 88, 152);
    imagecopy($capt, $img5, 480, 0, 0, 0, 88, 152);
    imagecopy($capt, $img6, 600, 0, 0, 0, 88, 152);
    imagepng($capt, $dr);
    echo <<<CGR2
<p class="mmt" style="color:#ff0000">Данные введены неверно</p>
<p class="mmt">Для продолжения введите символы, указанные ниже</p>
<img src="img/$mdstr/$mdstr.png" width="300px" class="captcha"><br>
<input id="val" class="mnt" type="text" onkeypress="enter(event)"/>
<p><input class="but" type="submit" value="Отправить" onclick="send()"/></p>
<script>
setTimeout(function(){window.location.href = "/captcha/?time=true&img=$mdstr"}, 60000);
function send(){
  let vl = document.getElementById("val").value;
  window.location.href = "/captcha/index.php?cpt=$mdstr&val=" + vl;
}
function enter(e) {
  if (e.keyCode == 13) {
	send();
  }
}
</script>
CGR2;
    }    
} 
else if(isset($_GET['time']))
 {
  deleteDir($_SERVER['DOCUMENT_ROOT'] . "/captcha/img/" . $_GET['img']);
  $rand = rand(0, 100000000);
  $str = md5($rand);
  $rstr = substr($str, 26);
  $rt = "uytrtyuiutr";
  $rt = md5($rt);
  $rstr = str_replace('0', 'g', $rstr);
  $rstr = str_replace('o', 'p', $rstr);
  $mdstr = md5($rt . $rstr);
  $mdtxt = md5($mdstr . md5($rstr) . $rt . $mdstr);
  mkdir($_SERVER['DOCUMENT_ROOT'] . "/captcha/img/" . $mdstr);
  mkdir($_SERVER['DOCUMENT_ROOT'] . "/captcha/img/" . $mdstr . "/" . $mdtxt);
  $dr = $_SERVER['DOCUMENT_ROOT'] . "/captcha/img/" . $mdstr . "/" . $mdstr . ".png";
  $capt = imagecreatetruecolor(720, 152);
  $img1 = crt($rstr[0]);
  $img2 = crt($rstr[1]);
  $img3 = crt($rstr[2]);
  $img4 = crt($rstr[3]);
  $img5 = crt($rstr[4]);
  $img6 = crt($rstr[5]);
  imagecopy($capt, $img1, 0, 0, 0, 0, 88, 152);
  imagecopy($capt, $img2, 120, 0, 0, 0, 88, 152);
  imagecopy($capt, $img3, 240, 0, 0, 0, 88, 152);
  imagecopy($capt, $img4, 360, 0, 0, 0, 88, 152);
  imagecopy($capt, $img5, 480, 0, 0, 0, 88, 152);
  imagecopy($capt, $img6, 600, 0, 0, 0, 88, 152);
  imagepng($capt, $dr);
  echo <<<CGR3
<p class="mmt" style="color:#ff0000">Время ожидания вышло</p>
<p class="mmt">Для продолжения введите символы, указанные ниже</p>
<img src="img/$mdstr/$mdstr.png" width="300px" class="captcha"><br>
<input id="val" class="mnt" type="text" onkeypress="enter(event)"/>
<p><input class="but" type="submit" value="Отправить" onclick="send()"/></p>
<script>
setTimeout(function(){window.location.href = "/captcha/?time=true&img=$mdstr"}, 60000);
function send(){
  let vl = document.getElementById("val").value;
  window.location.href = "/captcha/index.php?cpt=$mdstr&val=" + vl;
}
function enter(e) {
  if (e.keyCode == 13) {
	send();
  }
}
</script>
CGR3;
 }
else
 {
  $rand = rand(0, 100000000);
  $str = md5($rand);
  $rstr = substr($str, 26);
  $rt = "uytrtyuiutr";
  $rt = md5($rt);
  $rstr = str_replace('0', 'g', $rstr);
  $rstr = str_replace('o', 'p', $rstr);
  $mdstr = md5($rt . $rstr);
  $mdtxt = md5($mdstr . md5($rstr) . $rt . $mdstr);
  mkdir($_SERVER['DOCUMENT_ROOT'] . "/captcha/img/" . $mdstr);
  mkdir($_SERVER['DOCUMENT_ROOT'] . "/captcha/img/" . $mdstr . "/" . $mdtxt);
  $dr = $_SERVER['DOCUMENT_ROOT'] . "/captcha/img/" . $mdstr . "/" . $mdstr . ".png";
  $capt = imagecreatetruecolor(720, 152);
  $img1 = crt($rstr[0]);
  $img2 = crt($rstr[1]);
  $img3 = crt($rstr[2]);
  $img4 = crt($rstr[3]);
  $img5 = crt($rstr[4]);
  $img6 = crt($rstr[5]);
  imagecopy($capt, $img1, 0, 0, 0, 0, 88, 152);
  imagecopy($capt, $img2, 120, 0, 0, 0, 88, 152);
  imagecopy($capt, $img3, 240, 0, 0, 0, 88, 152);
  imagecopy($capt, $img4, 360, 0, 0, 0, 88, 152);
  imagecopy($capt, $img5, 480, 0, 0, 0, 88, 152);
  imagecopy($capt, $img6, 600, 0, 0, 0, 88, 152);
  imagepng($capt, $dr);
  echo <<<CGR4
<p class="mmt">phCAPTCHA</p>
<p class="mmt">Для продолжения введите символы, указанные ниже</p>
<img src="img/$mdstr/$mdstr.png" width="300px" class="captcha"><br>
<input id="val" class="mnt" type="text" onkeypress="enter(event)"/>
<p><input class="but" type="submit" value="Отправить" onclick="send()"/></p>
<script>
setTimeout(function(){window.location.href = "/captcha/?time=true&img=$mdstr"}, 60000);
function send(){
  let vl = document.getElementById("val").value;
  window.location.href = "/captcha/index.php?cpt=$mdstr&val=" + vl;
}
  function enter(e) {
    if (e.keyCode == 13) {
	  send();
    }
  }
</script>
CGR4;
 }
?>
<body>
</body>
</html>
