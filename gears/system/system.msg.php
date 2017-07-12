<?php
require(dirname(dirname(dirname(__FILE__)))."/system.php");
$Authen=S("SystemAuthen");
if(!$Authen){
	echo "@登陆超时，请重新登陆！";
	exit(0);
}
$MsgCount=M("timer","(`flag`=1)AND(`end`=1)AND(`target`='".addslashes($Authen["id"])."')",0);
if(!$MsgCount){echo "!短消息";}else{echo "!<b>短消息</b>";}