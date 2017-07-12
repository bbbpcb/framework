<?php
$l=4;$w=80;$h=20;$k="AUTHENCAPTCHA";
if(!isset($_SESSION))session_start();
$_SESSION[$k]=substr(str_shuffle("abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPRSTUVWXYZ23456789"),0,$l);
$img=imagecreate($w,$h);imagecolorallocatealpha($img,255,255,255,127);
foreach(str_split($_SESSION[$k]) as $i=>$c)imagechar($img,6,5+$i*floor(($w-10)/$l)+mt_rand(0,4)-2+5,mt_rand(0,4)+floor(($h-14)/2-2),$c,imagecolorallocate($img,mt_rand(30, 180),mt_rand(10, 100),mt_rand(40, 250)));
header("Content-Type: image/jpeg");
header("Expires: 0");
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
imagepng($img);
imagedestroy($img);