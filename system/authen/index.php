<?php
if(!defined("ROOT"))DIE("DENIED");
require(ROOT."/gears/system/system.authen.php");
$roster=SystemAuthenRoster();
?>
<?=$HTMLBOF?>
<link rel="stylesheet" type="text/css" href="gears/jquery/bootstrap/css/bootstrap.min.css">
<script type="text/javascript" src="gears/jquery/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="gears/jquery/bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript" src="gears/jquery/dialog/dialog.js?skin=blue"></script>
<script type="text/javascript" src="gears/jquery/dialog/iframe.js"></script>
<style type="text/css">
html,body{margin:0;padding:0px;}
body{padding:20px;}
.VAM{vertical-align: middle;}
.DialogWrapper{display: block;overflow: hidden;position: relative;overflow-y:auto;width:760px;height: 380px;}
.DialogWrapper table{font-size: 12px;}
.DialogWrapperProfile{display: block;overflow: hidden;position: relative;overflow-y:auto;width:660px;}
.DialogWrapperProfile .form-group{font-size: 12px;}
.TopButtons{padding-bottom: 20px;}
</style>
<?=$BODYBOF?>

<div class="TopButtons">
  <div class="btn-group">
    <?php if($Authen["super"]){ ?><button type="button" class="btn btn-default btn-sm" onclick="Profile('新增管理员','');">新增管理员</button><?php } ?>
    <button type="button" class="btn btn-default btn-sm" onclick="AuthenLog('系统登陆记录','');">系统登陆记录</button>
    <button type="button" class="btn btn-default btn-sm" onclick="AuthenLog('我的登陆记录','<?=H($Authen["id"])?>');">我的登陆记录</button>
    <button type="button" class="btn btn-default btn-sm" onclick="Profile('编辑我的资料','<?=H($Authen["id"])?>');">编辑我的资料</button>
    <a class="btn btn-default btn-sm" href="?to=<?=$TO?>&at=msg&action=inbox">我的短消息</a>
  </div>
</div>

<table class="table table-striped table-hover">
  <thead>
    <tr>
      <th>帐号</th>
      <th>真实姓名</th>
      <th>手机号码</th>
      <th>电子邮箱</th>
      <th>联系QQ</th>
      <th>最后登陆时间</th>
      <th>操作</th>
    </tr>
  </thead>
  <tbody>
  <?php 
  foreach($roster as $id => $card){
    $super=SystemAuthenSuper($id);
    $realname=V($card["realname"]);
    $phone=V($card["phone"]);
    $email=V($card["email"]);
    $qq=V($card["qq"]);
    $ip=V($card["ip"],"0.0.0.0");
    $login=V($card["login"]);
    $address=V($card["address"],"未知地点");
    $location=V($card["location"],"");
    ?>
    <tr>
      <td><a href="?to=<?=$TO?>&at=msg&action=send&target=<?=urlencode($id)?>" title="向 <?=H($id)?> 发送消息"><?=H($id)?></a></td>
      <td><?=$realname?H($realname):"-"?></td>
      <td><?=$phone?H($phone):"-"?></td>
      <td><?=$email?"<a href=\"mailto:".H($email)."\" target=\"_blank\">".H($email)."</a>":"-"?></td>
      <td><?=$qq?"<a href=\"http://wpa.qq.com/msgrd?V=1&Uin=".H($qq)."\" target=\"_blank\"><img class=\"VAM\" src=\"http://wpa.qq.com/pa?p=1:".H($qq).":4\"><span class=\"VAM\">".H($qq)."</span></a>":"-"?></td>
      <td><?=$login?"<a title=\"登陆IP：".H($ip)."\" href=\"javascript:void(0);\" onclick=\"MapLocate('".H($id)." <".H($ip)."> [".T($login)."] ','".H($location)."');\">".T($login)."</a>":"-"?></td>
      <td>
        <a href="javascript:void(0);" onclick="AuthenLog('<?=H($id)?> 登陆记录','<?=H($id)?>');">记录</a>
        <?php if($Authen["super"]){ ?>
        <a href="javascript:void(0);" onclick="Profile('编辑帐号资料','<?=H($id)?>');">编辑</a>
        <a href="javascript:void(0);" onclick="Remove(this,'<?=H($id)?>');">删除</a>
        <?php } ?>
      </td>
    </tr>
  <?php } ?>
  </tbody>
</table>

<script type="text/javascript">
    function Refresh(){
      location.href=location.href;
    }
    function Remove(obj,id){
      if(!confirm("确定要删除帐号 ["+id+"] 吗?"))return false;
      $.ajax({
         type: "POST",
         url: '?to=<?=$TO?>&at=profile&id='+encodeURIComponent(id)+'&action=delete',
         data: "time="+(new Date()).getTime(),
         success: function(data){
           if(data.substr(0,1)=="!"){
             $.dialog(data.substr(1),{title:"提示"}).time(3);
           }
           if(data.substr(0,1)=="@"){
             parent.toast(data.substr(1));
             $(obj).parent().parent().slideUp("slow");
           }
         }
      });
    }
    function Profile(title,id){
      var dialog = $.dialog({title:title,lock:true,width: 660});
      $.ajax({
          url: '?to=<?=$TO?>&at=profile&id='+encodeURIComponent(id),
          success: function (data) {
              dialog.content(data).button({
                  name: '保存',
                  focus: true,
                  callback: function () {
                      $.ajax({
                       type: "POST",
                       url: $("#ProfileForm").attr("action"),
                       data: $("#ProfileForm").serialize(),
                       success: function(data){
                         if(data.substr(0,1)=="!"){
                           $.dialog(data.substr(1),{title:"提示"}).time(3);
                         }
                         if(data.substr(0,1)=="@"){
                           dialog.close();
                           parent.toast(data.substr(1));
                           Refresh();
                         }
                       }
                     });
                    return false;
                  }
              }, {
                  name: '取消'
              });
          }
      });
    }
    function AuthenLog(title,id){
      var dialog = $.dialog({title:title,padding:"0",lock:true,width: 760, height: 380});
      $.ajax({
          url: '?to=<?=$TO?>&at=log&id='+encodeURIComponent(id),
          success: function (data) {
              dialog.content(data);
          }
      });
    }
    function MapLocate(title,point){
      $.dialog.open('gears/map/map.html#center='+point+'&zoom=17&width=600&height=360&markers='+point,{
          title:title,width: 602, height: 362,
      });
    }
    <?php if(I("action")=="profile"){ ?>
    $(document).ready(function(){Profile('编辑我的资料','<?=H($Authen["id"])?>');});
    <?php } ?>
</script>
<?=$BODYEOF?>
<?=$HTMLEOF?>