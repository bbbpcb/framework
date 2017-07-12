<?
function HandlePost(){
  $id=I("username");
  $ps=I("password");
  include(ROOT."/gears/system/system.authen.php");
  $account=SystemAuthenAccount($id);
  if(!$account){
    Toast("您输入的帐号不存在，请正确输入帐号！");
    param("username","");
    param("password","");
    return false;
  }
  if(SystemAuthenSaltps(V($account["salt"]),$ps)!=V($account["ps"])){
    Toast("您输入的密码有误，请重新输入！");
    param("password","");
    return false;
  }
  $account["ip"]=R("IP");
	$account["login"]=time();
	$account["address"]="物业计算";
	$account["location"]="";
	@SystemAuthenAccount($id,$account);
	L(ROOT."/data/authen/authen.log",implode("|",array($id,T($account["login"]),$account["ip"],$account["location"],$account["address"],R("UA"))));
	S("Logined",$id);
	status("帐号登录成功…");
  script("location.href='/?/choose/';",300);
}

HandlePost();

/*
$cfgid=cfg("main","username","test");
$cfgps=cfg("main","password","123456");
if((md5($id)==md5($cfgid))&&(md5($ps)==md5($cfgps))){
    S("Logined",$id);
    status("帐号登录成功…");
    script("location.href='/?/choose/';",300);
}else{
    Toast("您输入的帐号密码有误…");
    param("password","");
}
*/