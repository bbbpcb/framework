<?php
if(!defined("ROOT"))DIE("DENIED");
require(ROOT."/gears/system/system.authen.php");
$action=I("action","read");
$id=I("id");
$self=$id==$Authen["id"];
$profile=SystemAuthenAccount($id);
if($action=="submit"){
  if(!$Authen["super"])if(!$self)die("!对不起，您没有权限管理该帐号!");
  $password=I("password");
  $passwordagain=I("passwordagain");
  if($password!=$passwordagain)die("!两次密码输入不一致,请重新输入!");
  if($password)if(strlen($password)<6)die("!密码长度不能小于6位,请重新输入!");
  if(!$id){
    if(!$password)die("!密码不能为空,请重新输入!");
    $username=strtolower(I("username"));
    if(!$username)die("!帐号不能为空,请正确输入帐号!");
    if(!preg_match('|^[0-9a-zA-Z]+$|',$username))die("!帐号只能包含字母和数字，请正确输入帐号!");
    if(array_key_exists($username,SystemAuthenRoster()))die("!帐号已存在,请重新输入帐号!");
    $id=$username;
    $profile=array();
  }
  $realname=I("realname");
  if($realname){
    $profile["realname"]=$realname;
  }
  $phone=I("phone");
  if($phone){
    if(!preg_match('|^[0-9]+$|',$phone))die("!手机号码只能包含数字，请正确输入!");
    if(strlen($phone)<7 || strlen($phone)>12)die("!手机号码格式错误，请正确输入!");
    $profile["phone"]=$phone;
  }
  $email=I("email");
  if($email){
    if(!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix",$email))die("!电子邮箱格式错误，请正确输入!");
    $profile["email"]=$email;
  }
  $qq=I("qq");
  if($qq){
    if(!preg_match('|^[0-9]+$|',$qq))die("!联系QQ只能包含数字，请正确输入!");
    if(strlen($qq)<5 || strlen($qq)>12)die("!联系QQ格式错误，请正确输入!");
    $profile["qq"]=$qq;
  }
  if($password){
    $profile["salt"]=md5(uniqid());
    $profile["ps"]=SystemAuthenSaltps($profile["salt"],$password);
  }
  SystemAuthenAccount($id,$profile);
  echo "@帐号保存成功";
  exit(0);
}
if($action=="delete"){
  if(!$Authen["super"])die("!对不起，您没有权限删除该帐号!");
  if(!$id)die("!该帐号不存在，请刷新后重试!");
  if($id=="admin")die("!对不起，您没有权限删除该帐号!");
  if(!SystemAuthenAccountDelete($id))die("!删除操作执行失败，请检查网站目录权限!");
  echo "@删除帐号成功";
  exit(0);
}
?>
<div class="DialogWrapperProfile">
  <form id="ProfileForm" role="form" method="POST" action="?to=<?=$TO?>&at=profile&id=<?=urldecode($id)?>&action=submit" onsubmit="return false;">


    <div class="row">
      <div class="col-md-6">
        <div class="form-group input-group-sm">
          <label for="InputRealname">帐号<?php if(!$id){ ?><span style="color:#888">( 仅限字母及数字 )</span><?php } ?></label>
          <input id="InputRealname" type="text" class="form-control" value="<?=H($id)?>" <?=$id?"readonly":" name=\"username\""?> placeholder="请输入帐号...">
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group input-group-sm">
          <label for="InputGrouptype">权限</label>
          <input id="InputGrouptype" type="text" class="form-control" value="<?=SystemAuthenSuper($id)?"超级管理员":"管理员"?>" readonly>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="form-group input-group-sm">
          <label for="InputRealname">真实姓名</label>
          <input id="InputRealname" name="realname" type="text" class="form-control" value="<?=H(V($profile["realname"]))?>" placeholder="请输入真实姓名...">
        </div>
      </div>
      <div class="col-md-6">
       <div class="form-group input-group-sm">
        <label for="InputPhone">手机号码</label>
        <input id="InputPhone" name="phone" type="text" class="form-control" value="<?=H(V($profile["phone"]))?>" placeholder="请输入手机号码...">
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-6">
      <div class="form-group input-group-sm">
        <label for="InputEmail">电子邮箱</label>
        <input id="InputEmail" name="email" type="text" class="form-control" value="<?=H(V($profile["email"]))?>" placeholder="请输入邮箱地址...">
      </div>
    </div>
    <div class="col-md-6">
     <div class="form-group input-group-sm">
      <label for="InputQQ">联系QQ</label>
      <input id="InputQQ" name="qq" type="text" class="form-control" value="<?=H(V($profile["qq"]))?>" placeholder="请输入联系QQ...">
    </div>
  </div>
</div>

<?php if(!$id){ ?>
<div class="row">
  <div class="col-md-6">
    <div class="form-group input-group-sm">
      <label for="InputPassword">登陆密码</label>
      <input id="InputPassword" name="password" type="password" class="form-control" placeholder="请输入登陆密码...">
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group input-group-sm">
      <label for="InputPasswordAgain">确认登陆密码</label>
      <input id="InputPasswordAgain" name="passwordagain" type="password" class="form-control" placeholder="请再次输入登陆密码...">
    </div>  
  </div>
</div>
<?php }else{ ?>
<div class="row">
  <div class="col-md-6">
    <div class="form-group input-group-sm">
      <label for="InputPassword">登陆密码 <span style="color:#888">( 无需修改请留空 )</span></label>
      <input id="InputPassword" name="password" type="password" class="form-control" placeholder="请输入登陆密码,留空则不修改...">
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group input-group-sm">
      <label for="InputPasswordAgain">确认登陆密码 <span style="color:#888">( 无需修改请留空 )</span></label>
      <input id="InputPasswordAgain" name="passwordagain" type="password" class="form-control" placeholder="请再次输入登陆密码,留空则不修改...">
    </div>  
  </div>
</div>
<?php } ?>

</form>
</div>