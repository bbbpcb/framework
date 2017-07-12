<?php
if(!defined("ROOT"))DIE("DENIED");
$action=strtolower(I("action","inbox"));
$page=max(intval(I("page")),0);
?>
<?php
if($action=="send"){
R("form.php");$script="";$msg="";if (I(V($_POST['FORMNAME']),'',true)=='send'){
$targets=array_filter(explode(",",I("target")));$title=I("title");$content=I("content");
if(count($targets)<1){$msg="请输入消息收件人,收件人不能为空,多个收件人用英文逗号隔开!";}
elseif(strlen($title)<1){$msg="请输入消息标题,消息标题不能为空!";}
elseif(strlen($content)<1){$msg="请输入消息内容,消息内容不能为空!";}
else{
  $count=0;
  foreach($targets as $target){$target=trim($target);
    if(strlen($target)){$count+=M("timer",array("flag"=>1,"target"=>$target,"source"=>$Authen["id"],"stamp"=>time(),"start"=>1,"end"=>1,"title"=>$title,"content"=>$content,"ip"=>R("IP"),"address"=>$Authen["address"],"location"=>$Authen["location"]))>0;}
  }
  $msg="消息发送成功!";
  $script="location.href='".U(array("action"=>"sent"))."';";
}}}elseif($action=="read"){
$msg=M("timer","(`flag`=1)AND(`start`=1)AND((`target`='".addslashes($Authen["id"])."')OR(`source`='".addslashes($Authen["id"])."'))AND(`id`='".intval(I("id"))."')",1);
if(!$msg){die("找不到指定的消息记录,消息记录可能已被删除.");}
?>
<div style="display:block;width:660px;">
<div class="form-group FormItem FormItemMemo" style="margin-bottom:-20px;"><label class="FormLabel">消息内容</label><span class="FormHint">&nbsp;<small style="color:#888;">本消息于 <strong><?=T($msg["stamp"])?></strong> 发送至 <strong><?=H($msg["target"])?></strong></small>&nbsp;</span><div style="clear:both"></div><div class="well"><?=H($msg["content"])?></div></div>
</div>
<?php exit(0);}elseif($action=="reply"){
$msg=M("timer","(`flag`=1)AND(`end`>0)AND(`target`='".addslashes($Authen["id"])."')AND(`id`='".intval(I("id"))."')",1);
if(!$msg){die("找不到指定的消息记录,消息记录可能已被删除.");}
if($msg["end"]==1){M("timer",array("end"=>2),"`id`='".intval(I("id"))."'");}
R("form.php");
?>
<div style="display:block;width:660px;">
<div class="form-group FormItem FormItemMemo" style="margin-bottom:-10px;"><label class="FormLabel">消息内容</label><span class="FormHint">&nbsp;<small style="color:#888;">本消息由 <strong title="<?=H($msg["address"]."(IP:".$msg["ip"].")")?>" style="cursor:pointer;" onclick="MapLocate('<?=H($msg["address"]."(IP:".$msg["ip"].")")?>','<?=$msg["location"]?>');"><?=H($msg["source"])?></strong> 发送于 <strong><?=T($msg["stamp"])?></strong></small>&nbsp;</span><div style="clear:both"></div><div class="well"><?=H($msg["content"])?></div></div>
<div id="MsgQuickReply" style="margin-bottom:-20px;"><?=FormMemo("reply","快速回复","<small style='color:#888;'>请在下方文本框中输入回复内容</small>")?></div>
</div>
<?php exit(0);}elseif($action=="repling"){
$msg=M("timer","(`flag`=1)AND(`end`>0)AND(`target`='".addslashes($Authen["id"])."')AND(`id`='".intval(I("id"))."')",1);
if(!$msg){die("!找不到指定的消息记录,消息记录可能已被删除.");}
$content=trim(I("content"));
if(strlen($content)<1){die("!请正确输入回复内容,长度不得少于2个字.");}
if(strpos($msg["title"],"回复")===0){$title=ltrim(substr($msg["title"],strlen("回复")),"x");$i=strpos($title,":");
$newtitle=($i===false)?("回复: ".$msg["title"]):("回复x".(max(intval(substr($title,0,$i)),1)+1).": ".trim(substr($title,$i+1)));
}else{$newtitle="回复: ".$msg["title"];}
$id=M("timer",array("flag"=>1,"target"=>$msg["source"],"source"=>$Authen["id"],"stamp"=>time(),"start"=>1,"end"=>1,"tag"=>$msg["tag"],"title"=>$newtitle,"style"=>$msg["style"],"content"=>$content,"ip"=>R("IP"),"address"=>$Authen["address"],"location"=>$Authen["location"]));
if(!$id){die("!消息回复投递失败,请稍后重试.");}
echo "@回复消息投递成功!";
exit(0);}elseif($action=="inboxmark"){
$e=M("timer",array("end"=>2),"(`flag`=1)AND(`id` in (".trim(I("id"),",")."))AND(`end`=1)AND(`target`='".addslashes($Authen["id"])."')");
echo "@{$e}条消息标记为已读!";
exit(0);}elseif($action=="inboxdelete"){
$e=M("timer",array("end"=>0),"(`flag`=1)AND(`id` in (".trim(I("id"),",")."))AND(`end`>0)AND(`target`='".addslashes($Authen["id"])."')");
echo "@{$e}条消息删除成功!";
exit(0);}elseif($action=="sentdelete"){
$e=M("timer",array("start"=>0),"(`flag`=1)AND(`id` in (".trim(I("id"),",")."))AND(`start`=1)AND(`source`='".addslashes($Authen["id"])."')");
echo "@{$e}条消息删除成功!";
exit(0);} ?>
<?=$HTMLBOF?>
<link rel="stylesheet" type="text/css" href="gears/jquery/bootstrap/css/bootstrap.min.css">
<script type="text/javascript" src="gears/jquery/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="gears/jquery/bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript" src="gears/jquery/dialog/dialog.js?skin=blue"></script>
<script type="text/javascript" src="gears/jquery/dialog/iframe.js"></script>
<style type="text/css">
html,body{margin:0;padding:0px;}
body{padding:20px;}
.TopButtons{padding-bottom: 20px;}
.MessageRow{}
.MessageRow .MessageRowTitle{cursor:pointer;}
.MessageRowActive{}
.MessageRowActive .MessageRowBold{font-weight:bold;}
.PNFrame{text-align:center;}
</style>
<?=$BODYBOF?>

<div class="TopButtons">
  <a class="btn btn-default btn-sm" href="?to=<?=$TO?>">管理员名单</a>
  <div class="btn-group">
    <a class="btn btn-default btn-sm <?=$action=="send"?"active":""?>" href="?to=<?=$TO?>&at=<?=$AT?>&action=send">发新消息</a>
    <a class="btn btn-default btn-sm <?=$action=="inbox"?"active":""?>" href="?to=<?=$TO?>&at=<?=$AT?>&action=inbox">消息收件箱</a>
    <a class="btn btn-default btn-sm <?=$action=="sent"?"active":""?>" href="?to=<?=$TO?>&at=<?=$AT?>&action=sent">已发送消息</a>
  </div>
  <?php if(in_array($action,array("inbox","sent"))){ ?><a class="btn btn-default btn-sm" href="<?=U(array("refresh"=>time(),"page"=>1))?>">刷新</a><?php } ?>
  <?php if(in_array($action,array("inbox"))){ ?><a class="btn btn-default btn-sm MsgCheckboxToggled" href="javascript:void(0);" onclick="MsgMark();" style="display:none;">标记为已读</a><?php } ?>
  <?php if(in_array($action,array("inbox","sent"))){ ?><a class="btn btn-default btn-sm MsgCheckboxToggled" href="javascript:void(0);" onclick="MsgDeleteSelected();" style="display:none;">删除所选消息</a><?php } ?>
</div>

<?php
if(in_array($action,array("inbox","sent"))){
    $inbox=$action=="inbox";
    $data=M("timer",$inbox?("(`flag`=1)AND(`end`>0)AND(`target`='".addslashes($Authen["id"])."')"):("(`flag`=1)AND(`start`=1)AND(`source`='".addslashes($Authen["id"])."')"),"`stamp` DESC,`id` DESC",$page,10);
    $pn=array_shift($data);
?>
    <table class="table table-striped table-hover">
      <thead>
        <tr>
          <th style="width:30px;"><input id="MsgCheckboxToggle" type="checkbox" value="*" onchange="MsgCheckboxToggle(this);"></th>
          <th style="width:150px;">消息时间</th>
          <th style="width:80px;"><?=$inbox?"发件人":"收件人"?></th>
          <th>消息标题</th>
          <th style="width:100px;">操作</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($data as $row){$unread=$inbox?($row["end"]==1):false; ?>
        <tr id="MessageID_<?=$row["id"]?>" class="MessageRow <?=$unread?"MessageRowActive":""?>">
          <td><input id="MsgCheckbox_<?=$row["id"]?>" class="MsgCheckbox" type="checkbox" value="<?=$row["id"]?>" onclick="MsgCheckboxChange();"></td>
          <td class="MessageRowBold MessageRowStamp"><small><?=T($row["stamp"])?></small></td>
          <td class="MessageRowBold MessageRowSource"><?=H($inbox?$row["source"]:$row["target"])?></td>
          <td class="MessageRowBold MessageRowTitle" <?=$row["style"]?"style=\"".$row["style"]."\"":""?> onclick="MsgRead(<?=$row["id"]?>);"><?=H($row["title"])?></td>
          <td><a href="javascript:void(0);" onclick="MsgRead(<?=$row["id"]?>);">查看</a> <a href="javascript:void(0);" onclick="MsgDelete(<?=$row["id"]?>,this);">删除</a></td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
    <?php if(!$data){ ?><div style="text-align:center;padding:100px 0;color:#bbb;font-size:12px;">暂无消息数据</div><?php } ?>
    <?php if($pn["maxpage"]>1){ ?>
    <div class="PNFrame">
    <nav>
      <ul class="pagination pagination-sm">
        <li>
          <a href="<?=U(array("page"=>$pn["page"]-1))?>" aria-label="Previous">
            <span aria-hidden="true">&laquo;</span>
          </a>
        </li>
        <?php foreach($pn["pn"] as $p){ ?>
        <li <?=$pn["page"]==$p?"class=\"active\"":""?>><a href="<?=U(array("page"=>$p))?>"><?=$p?></a></li>
        <?php } ?>
        <li>
          <a href="<?=U(array("page"=>$pn["page"]+1))?>" aria-label="Next">
            <span aria-hidden="true">&raquo;</span>
          </a>
        </li>
      </ul>
    </nav>
    </div>
    <?php } ?>
    <script>
    function MsgCheckboxToggle(obj){$(".MsgCheckbox").prop("checked",$(obj).prop("checked"));MsgCheckboxChange();}
    function MsgCheckboxChange(){
      var checked=$(".MsgCheckbox:checked");
      $(".MsgCheckboxToggled").toggle(checked.length>0);
    }
    function MsgMark(){
      var checked=$(".MessageRowActive .MsgCheckbox:checked");
      if(!checked.length)return;
      var id="";
      checked.each(function(){id+=","+$(this).val();});
      $.ajax({
         type: "POST",
         url: '?to=<?=$TO?>&at=<?=$AT?>&action=inboxmark&id='+encodeURIComponent(id),
         data: "time="+(new Date()).getTime(),
         success: function(data){
           if(data.substr(0,1)=="!"){
             $.dialog(data.substr(1),{title:"提示"}).time(3);
           }
           if(data.substr(0,1)=="@"){
             parent.toast(data.substr(1));
             checked.parent().parent().removeClass("MessageRowActive");
           }
         }
      });
    }
    function MsgDeleting(id,obj){
      $.ajax({
         type: "POST",
         url: '?to=<?=$TO?>&at=<?=$AT?>&action=<?=$inbox?"inboxdelete":"sentdelete"?>&id='+encodeURIComponent(id),
         data: "time="+(new Date()).getTime(),
         success: function(data){
           if(data.substr(0,1)=="!"){
             $.dialog(data.substr(1),{title:"提示"}).time(3);
           }
           if(data.substr(0,1)=="@"){
             parent.toast(data.substr(1));
             $(obj).parent().parent().remove();
             MsgCheckboxChange();
             if(!$(".MsgCheckbox").length){location.href=location.href;}
           }
         }
      });
    }
    function MsgDeleteSelected(){
      var checked=$(".MsgCheckbox:checked");
      if(!checked.length)return;
      if(!confirm("确定要删除选定的 "+checked.length+" 条消息吗?"))return false;
      var id="";
      checked.each(function(){id+=","+$(this).val();});
      MsgDeleting(id,checked);
    }
    function MsgDelete(id,obj){
      var msg=$("#MessageID_"+id);
      if(!confirm("确定要删除消息 ["+msg.find(".MessageRowSource").text()+"] "+msg.find(".MessageRowTitle").text()+" 吗?"))return false;
      MsgDeleting(id,obj);
    }
    function MsgRead(id){
      var msg=$("#MessageID_"+id);
      if(msg.hasClass("MessageRowActive"))msg.removeClass("MessageRowActive");
      var dialog = $.dialog({title:"["+msg.find(".MessageRowSource").text()+"] "+msg.find(".MessageRowTitle").text(),padding:"10",lock:true,width: 660});
          $.ajax({
              url: '?to=<?=$TO?>&at=<?=$AT?>&action=<?=$inbox?"reply":"read"?>&id='+encodeURIComponent(id),
              success: function (data) {
                  <?php if($inbox){ ?>
                  dialog.content(data).button({
                      name: '回复',
                      focus: true,
                      callback: function (){
                        var reply=$.trim($("#MsgQuickReply textarea").val());
                        if(reply){
                          $.post('?to=<?=$TO?>&at=<?=$AT?>&action=repling&id='+encodeURIComponent(id),{time:(new Date()).getTime(),content:reply},
                               function(data){
                                       if(data.substr(0,1)=="!"){
                                         $.dialog(data.substr(1),{title:"提示"}).time(3);
                                       }
                                       if(data.substr(0,1)=="@"){
                                         parent.toast(data.substr(1));
                                         dialog.close();
                                       }
                               });
                               return false;
                        }
                      }
                  }, {
                      name: '取消'
                  });
                  $("#MsgQuickReply textarea").focus();
                  <?php }else{ ?>
                  dialog.content(data).button({name: '确定',focus: true,});
                  <?php } ?>
              }
          });
    }
    function MapLocate(title,point){
      $.dialog.open('gears/map/map.html#center='+point+'&zoom=17&width=600&height=360&markers='+point,{
          title:title,width: 602, height: 362,
      });
    }
    </script>
<?php }elseif($action=="send"){ ?>
    <?=form("send","发送消息","",array(
      array("edit","target","消息收件人","请输入收件人帐号,多个收件人用英文半角逗号分割"),
      array("edit","title","消息标题","请输入消息标题"),
      array("memo","content","消息正文","请输入消息正文"),
    ))?>
    <?php if($msg){ ?>
    <script type="text/javascript">$(document).ready(function(){parent.toast("<?=addslashes($msg)?>");});</script>
    <?php } ?>
    <?php if($script){ ?>
    <script type="text/javascript">$(document).ready(function(){<?=$script?>});</script>
    <?php } ?>
<?php } ?>


<?=$BODYEOF?>
<?=$HTMLEOF?>