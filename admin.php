<?php
header("Expires: 0");
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
require("system.php");
if(isset($_GET["quit"])){
	S("SystemAuthen",false);
	header("location: ./");
	exit(0);
}
$Authen=S("SystemAuthen");
if(!$Authen){
	function Authen($username,$password){
		include(ROOT."/gears/system/system.authen.php");
		$account=SystemAuthenAccount($username);
		if(!$account)return "!您输入的帐号不存在，请正确输入帐号！";
		if(SystemAuthenSaltps(V($account["salt"]),$password)!=V($account["ps"]))return "!您输入的密码有误,请重新输入！";
		if(!SystemAuthenSuper($username))return "!登录被拒绝，只有超级管理员可以登陆管理后台！";
		$account["ip"]=R("IP");
		$account["login"]=time();
		$account["address"]=I("address");
		$account["location"]=I("location");
		if(defined("DEVMODE") && $account["ip"]=="127.0.0.1"){
			$account["address"]="服务器本机";
			$account["location"]="114.528274,37.983028";
		}
		@SystemAuthenAccount($username,$account);
		L(ROOT."/data/authen/authen.log",implode("|",array($username,T($account["login"]),$account["ip"],$account["location"],$account["address"],R("UA"))));
		$account["id"]=$username;
		$account["super"]=SystemAuthenSuper($username);
        S("SystemAuthen",$account);
		return basename(__FILE__);
	}
	$AuthenTitle="网站管理中心";
	$AuthenBanner="gears/system/system.authen.png";
	include(ROOT."/gears/authen/authen.php");
	exit(0);
}
if(isset($_GET["to"])){
$HTMLBOF=<<<HTMLBOF
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="utf-8">
	<meta name="renderer" content="webkit">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>网站管理中心</title>
HTMLBOF;
$BODYBOF=<<<BODYBOF
	<link rel="stylesheet" href="gears/system/system.global.css">
	<script type="text/javascript" src="gears/jquery/bird.js"></script>
</head>
<body>
BODYBOF;
$BODYEOF=<<<BODYEOF
</body>
BODYEOF;
$HTMLEOF=<<<HTMLEOF
</html>
HTMLEOF;
	$TO=strtolower($_GET["to"]);
	$AT=isset($_GET["at"])?strtolower($_GET["at"]):"index";
	define("SYSTEM",ROOT."/system");
	include(SYSTEM."/{$TO}/{$AT}.php");
	exit(0);
}
include(ROOT."/gears/system/system.php");