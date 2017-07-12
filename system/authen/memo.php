<?php
if(!defined("ROOT"))DIE("DENIED");
$super=true;
$flag=3;
$action=strtolower(I("action","list"));
if($action=="new"){
R("form.php");$script="";$msg="";if (I(V($_POST['FORMNAME']),'',true)=='post'){
$title=I("title");$content=I("content");
if(strlen($title)<1){$msg="请输入备忘标题,备忘标题不能为空!";}
elseif(strlen($content)<1){$msg="请输入备忘内容,备忘内容不能为空!";}
else{
  $id=M("timer",array("flag"=>$flag,"parent"=>0,"target"=>$Authen["id"],"source"=>$Authen["id"],"stamp"=>time(),"start"=>time(),"end"=>time(),"title"=>$title,"content"=>$content,"ip"=>R("IP"),"address"=>$Authen["address"],"location"=>$Authen["location"]));
  $msg="备忘保存成功!";
  $script="location.href='".U(array("action"=>"view","id"=>$id))."';";
}}}elseif($action=="modify"){
$id=intval(I("id"));$topic=M("timer","(`flag`={$flag})AND(`parent`=0)AND(`source`='".addslashes($Authen["id"])."')AND(`id`={$id})",1);
$pn=array("total"=>M("timer","(`flag`={$flag})AND(`parent`={$id})AND(`source`='".addslashes($Authen["id"])."')AND(`end`>0)",0));
R("form.php");$script="";$msg="";if (I(V($_POST['FORMNAME']),'',true)=='post'){
$title=I("title");$content=I("content");
if(strlen($title)<1){$msg="请输入备忘标题,备忘标题不能为空!";}
elseif(strlen($content)<1){$msg="请输入备忘内容,备忘内容不能为空!";}
else{
  M("timer",array("stamp"=>time(),"title"=>$title,"content"=>$content,"ip"=>R("IP"),"address"=>$Authen["address"],"location"=>$Authen["location"]),"(`flag`={$flag})AND(`parent`=0)AND(`id`={$id})");
  $msg="备忘保存成功!";
  $script="location.href='".U(array("action"=>"view","id"=>$id))."';";
}}}elseif($action=="modifyreply"){
$id=intval(I("id"));$rid=intval(I("rid"));$reply=M("timer","(`flag`={$flag})AND(`parent`={$id})AND(`id`={$rid})AND(`end`>0)",1);
R("form.php");$script="";$msg="";if (I(V($_POST['FORMNAME']),'',true)=='post'){$content=I("content");
if(strlen($content)<1){$msg="请输入备忘补充内容,补充内容不能为空!";}
else{
  M("timer",array("stamp"=>time(),"content"=>$content,"ip"=>R("IP"),"address"=>$Authen["address"],"location"=>$Authen["location"]),"(`flag`={$flag})AND(`parent`={$id})AND(`id`={$rid})AND(`end`>0)");
  $msg="备忘补充保存成功!";
  $script="location.href='".U(array("action"=>"view","id"=>$id))."';";
}}}elseif($action=="view"){
$id=intval(I("id"));$topic=M("timer","(`flag`={$flag})AND(`parent`=0)AND(`source`='".addslashes($Authen["id"])."')AND(`id`={$id})",1);
$data=M("timer","(`flag`={$flag})AND(`parent`={$id})AND(`end`>0)","`id` ASC",intval(I("viewpage")),10);
$pn=array_shift($data);$replymgr=($pn["total"] && ($super || ($topic["source"]==$Authen["id"])));
R("form.php");$script="";$msg="";if (I(V($_POST['FORMNAME']),'',true)=='reply'){
$content=I("content");if(strlen(trim(strip_tags($content,'<img><embed><iframe>')))<1){$msg="请输入备忘补充内容,补充内容不能为空!";}
else{
if(strpos($topic["title"],"补充")===0){$title=ltrim(substr($topic["title"],strlen("补充")),"x");$i=strpos($title,":");
$newtitle=($i===false)?("补充: ".$topic["title"]):("补充x".(max(intval(substr($title,0,$i)),1)+1).": ".trim(substr($title,$i+1)));
}else{$newtitle="补充: ".$topic["title"];}
  M("timer",array("flag"=>$flag,"parent"=>$id,"target"=>$topic["source"],"source"=>$Authen["id"],"stamp"=>time(),"start"=>time(),"end"=>1,"title"=>$newtitle,"content"=>$content,"ip"=>R("IP"),"address"=>$Authen["address"],"location"=>$Authen["location"]));
  M("timer",array("target"=>$Authen["id"],"end"=>time()),"(`flag`={$flag})AND(`parent`=0)AND(`id`={$id})");
  $msg="备忘补充保存成功!";
  $script="location.href='".U(array("action"=>"view","viewpage"=>$pn["maxpage"]+1))."';";
}}}elseif($action=="delete"){
$e=M("timer",array("flag"=>100),"(`flag`={$flag})AND(`parent`=0)AND(`source`='".addslashes($Authen["id"])."')AND(`id` in (".trim(I("id"),",")."))");
header("location: ".U(array("action"=>"list")));
exit(0);}elseif($action=="deletetopic"){
$e=M("timer",array("flag"=>100),"(`flag`={$flag})AND(`parent`=0)AND(`source`='".addslashes($Authen["id"])."')AND(`id` in (".trim(I("id"),",")."))");
echo "@{$e}条备忘删除成功!";
exit(0);}elseif($action=="deletereply"){
$id=intval(I("id"));
$e=M("timer",array("end"=>0),"(`flag`={$flag})AND(`parent`={$id})AND(`source`='".addslashes($Authen["id"])."')AND(`id` in (".trim(I("rid"),",")."))");
echo "@{$e}条备忘补充删除成功!";
exit(0);
}
?>
<?=$HTMLBOF?>
<link rel="stylesheet" type="text/css" href="gears/jquery/bootstrap/css/bootstrap.min.css">
<script type="text/javascript" src="gears/jquery/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="gears/jquery/bootstrap/js/bootstrap.min.js"></script>
<?php if(in_array($action,array("list","view"))){ ?>
<script type="text/javascript" src="gears/jquery/dialog/dialog.js?skin=blue"></script>
<script type="text/javascript" src="gears/jquery/dialog/iframe.js"></script>
<?php } ?>
<style type="text/css">
html,body{margin:0;padding:0px;}
body{padding:20px;}
.VAM{vertical-align: middle;}
.TopButtons{padding-bottom: 20px;}
.TopicRowStamp{color:#666;}
.TopicRowTitle a{color:#000;text-decoration:none;}
.AuthorInfor{color:gray;float:right;padding-top:2px;}
.ActionInfor{float:right;z-index:100;}
.ReplyHovered{display:none;}
.NowHovered .ReplyHovered{display:block;margin-bottom:6px;padding-bottom:6px;border-bottom:1px dashed #ccc;font-size:12px;line-height:12px;text-align:center;}
</style>
<?=$BODYBOF?>

<div class="TopButtons">
  <?php if(in_array($action,array("list"))){ ?>
  <a class="btn btn-default btn-sm" href="<?=U(array("action"=>"new"))?>">新增备忘</a>
  <?php } ?>
  <?php if(in_array($action,array("view","new"))){ ?>
  <a class="btn btn-default btn-sm" href="<?=U(array("action"=>"list"))?>">返回备忘列表</a>
  <?php } ?>
  <?php if(in_array($action,array("modify"))){ ?>
  <a class="btn btn-default btn-sm" href="<?=U(array("action"=>"view"))?>">取消编辑备忘</a>
  <?php } ?>
  <?php if(in_array($action,array("modifyreply"))){ ?>
  <a class="btn btn-default btn-sm" href="<?=U(array("action"=>"view"))?>">取消编辑备忘补充</a>
  <?php } ?>
  <?php if(in_array($action,array("list","view","modify","modifyreply"))){ ?><a class="btn btn-default btn-sm" href="<?=U(array("refresh"=>time()))?>">刷新</a><?php } ?>
  <?php if(in_array($action,array("list"))){ ?><a class="btn btn-default btn-sm TopicCheckboxToggled" href="javascript:void(0);" onclick="TopicDeleteSelected();" style="display:none;">删除所选备忘</a><?php } ?>
  <?php if($super && in_array($action,array("view","modify"))){ ?>
  <a class="btn btn-default btn-sm" href="javascript:void(0);" onclick="if(confirm('确定要删除该备忘<?=$pn["total"]?"及所有补充":""?>吗?'))location.href='<?=U(array("action"=>"delete","id"=>$id))?>';">删除该备忘<?=$pn["total"]?"及所有补充":""?></a>
  <?php } ?>
</div>

<?php
if($action=="list"){
$data=M("timer","(`flag`={$flag})AND(`parent`=0)AND(`source`='".addslashes($Authen["id"])."')","`id` DESC",intval(I("page")),10);
$pn=array_shift($data);
?>
<table class="table table-striped table-hover">
      <thead>
        <tr>
          <?php if($super){ ?><th style="width:30px;"><input id="TopicCheckboxToggle" type="checkbox" value="*" onchange="TopicCheckboxToggle(this);"></th><?php } ?>
          <th>标题</th>
          <th style="width:150px;">时间</th>
          <th style="width:100px;">操作</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($data as $row){ ?>
        <tr id="TopicID_<?=$row["id"]?>" class="TopicRow">
          <?php if($super){ ?><td><input id="TopicCheckbox_<?=$row["id"]?>" class="TopicCheckbox" type="checkbox" value="<?=$row["id"]?>" onclick="TopicCheckboxChange();"></td><?php } ?>
          <td class="TopicRowTitle"><a href="<?=U(array("action"=>"view","id"=>$row["id"]))?>" <?=$row["style"]?"style=\"".$row["style"]."\"":""?>><?=H($row["title"])?></a></td>
          <td><div class="TopicRowStamp" title="<?=H($row["address"]."(IP:".$row["ip"].")")?>" style="cursor:pointer;" onclick="MapLocate('<?=H($row["address"]."(IP:".$row["ip"].")")?>','<?=$row["location"]?>');"><?=T(max($row["end"],$row["stamp"]),"Y-m-d H:i")?></div></td>
          <td><a href="<?=U(array("action"=>"view","id"=>$row["id"]))?>">查看</a><?php if($super){ ?> <a href="javascript:void(0);" onclick="TopicDelete(<?=$row["id"]?>,this);">删除</a><?php } ?></td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
    <?php if(!$data){ ?><div style="text-align:center;padding:100px 0;color:#bbb;font-size:12px;">暂无任何备忘</div><?php } ?>
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
    function TopicCheckboxToggle(obj){$(".TopicCheckbox").prop("checked",$(obj).prop("checked"));TopicCheckboxChange();}
    function TopicCheckboxChange(){
      var checked=$(".TopicCheckbox:checked");
      $(".TopicCheckboxToggled").toggle(checked.length>0);
    }
    function TopicDeleting(id,obj){
      $.ajax({
         type: "POST",
         url: "<?=U(array("action"=>"deletetopic"))?>&id="+encodeURIComponent(id),
         data: "time="+(new Date()).getTime(),
         success: function(data){
           if(data.substr(0,1)=="!"){
             $.dialog(data.substr(1),{title:"提示"}).time(3);
           }
           if(data.substr(0,1)=="@"){
             parent.toast(data.substr(1));
             $(obj).parent().parent().remove();
             TopicCheckboxChange();
             if(!$(".TopicCheckbox").length){location.href=location.href;}
           }
         }
      });
    }
    function TopicDeleteSelected(){
      var checked=$(".TopicCheckbox:checked");
      if(!checked.length)return;
      if(!confirm("确定要删除选定的 "+checked.length+" 条备忘吗?"))return false;
      var id="";
      checked.each(function(){id+=","+$(this).val();});
      TopicDeleting(id,checked);
    }
    function TopicDelete(id,obj){
      var topic=$("#TopicID_"+id);
      if(!confirm("确定要删除备忘 "+topic.find(".TopicRowTitle").text()+" 吗?"))return false;
      TopicDeleting(id,obj);
    }
    function MapLocate(title,point){
      $.dialog.open('gears/map/map.html#center='+point+'&zoom=17&width=600&height=360&markers='+point,{
          title:title,width: 602, height: 362,lock:true
      });
    }
    </script>
<?php
}elseif($action=="view"){
?>
    <div class="panel panel-default">
      <div class="panel-heading"><?php if($topic["source"]==$Authen["id"]){ ?><div style="float:right;padding-left:15px;"><a href="<?=U(array("action"=>"modify"))?>">编辑</a></div><?php } ?><small class="AuthorInfor">该备忘记录于 <strong title="<?=H($topic["address"]."(IP:".$topic["ip"].")")?>" style="cursor:pointer;" onclick="MapLocate('<?=H($topic["address"]."(IP:".$topic["ip"].")")?>','<?=$topic["location"]?>');"><?=T($topic["stamp"])?></strong></small><strong><?=$topic["title"]?></strong><div class="clearfix"></div></div>
      <div class="panel-body"><?=$topic["content"]?></div>
      <div class="panel-footer" <?php if($pn["total"]){ ?>style="border-bottom: 1px solid #ddd;border-bottom-right-radius: 0px;border-bottom-left-radius: 0px;"<?php } ?>><?php if($replymgr){ ?><div style="float:right;"><a class="ReplyCheckboxToggled" href="javascript:void(0);" onclick="ReplyDeleteSelected();" style="display:none;">删除所选备忘补充</a></div><?php } ?><div <?php if($replymgr){ ?>class="checkbox"<?php } ?> style="margin:0;color:#666;margin-right:100px;"><?php if($replymgr){ ?><label><input id="ReplyCheckboxToggle" type="checkbox" value="*" onchange="ReplyCheckboxToggle(this);"><?php } ?> 共计 <?=$pn["total"]?> 条备忘补充<?php if($replymgr){ ?></label><?php } ?></div><?php if($replymgr){ ?><div class="clearfix"></div><?php } ?></div>
      <table class="table table-striped table-hover table-bordered">
        <tbody>
          <?php foreach($data as $row){ ?>
          <tr id="ReplyID_<?=$row["id"]?>">
              <?php if($replymgr){ ?><td style="width:40px;text-align:center;"><input id="ReplyCheckbox_<?=$row["id"]?>" class="ReplyCheckbox" type="checkbox" value="<?=$row["id"]?>" onclick="ReplyCheckboxChange();"></td><?php } ?>
              <td style="cursor:pointer;width:135px;font-size:12px;line-height:22px;" title="<?=H($row["address"]."(IP:".$row["ip"].")")?>" onclick="MapLocate('<?=H($row["address"]."(IP:".$row["ip"].")")?>','<?=$row["location"]?>');"><div class="TopicRowStamp"><?=T($row["stamp"])?></div></td>
              <td class="ReplyHover"><?php if($row["source"]==$Authen["id"]){ ?><div class="ReplyHovered"><a href="<?=U(array("action"=>"modifyreply","rid"=>$row["id"]))?>">编辑本条补充内容</a></div><?php } ?><?=$row["content"]?></td>
              <?php if($replymgr){ ?><td style="width:50px;text-align:center;"><a href="javascript:void(0);" onclick="ReplyDelete(<?=$row["id"]?>,this);">删除</a></td><?php } ?>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
    <?php if($pn["maxpage"]>1){ ?>
    <div class="PNFrame">
    <nav>
      <ul class="pagination pagination-sm">
        <li>
          <a href="<?=U(array("viewpage"=>$pn["page"]-1))?>" aria-label="Previous">
            <span aria-hidden="true">&laquo;</span>
          </a>
        </li>
        <?php foreach($pn["pn"] as $p){ ?>
        <li <?=$pn["page"]==$p?"class=\"active\"":""?>><a href="<?=U(array("viewpage"=>$p))?>"><?=$p?></a></li>
        <?php } ?>
        <li>
          <a href="<?=U(array("viewpage"=>$pn["page"]+1))?>" aria-label="Next">
            <span aria-hidden="true">&raquo;</span>
          </a>
        </li>
      </ul>
    </nav>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading"><strong>您可以对该备忘进行补充</strong></div>
      <div class="panel-body"><?=form("reply","保存备忘补充","",array(array("editor","content")))?></div>
    </div>
    <?php if($msg){ ?>
    <script type="text/javascript">$(document).ready(function(){parent.toast("<?=addslashes($msg)?>");});</script>
    <?php } ?>
    <?php if($script){ ?>
    <script type="text/javascript">$(document).ready(function(){<?=$script?>});</script>
    <?php } ?>
    <script>
    $(document).ready(function(){$(".ReplyHover").hover(function(){$(this).addClass("NowHovered");},function(){$(this).removeClass("NowHovered");});});
    function ReplyCheckboxToggle(obj){$(".ReplyCheckbox").prop("checked",$(obj).prop("checked"));ReplyCheckboxChange();}
    function ReplyCheckboxChange(){
      var checked=$(".ReplyCheckbox:checked");
      $(".ReplyCheckboxToggled").toggle(checked.length>0);
    }
    function ReplyDeleting(id,obj){
      $.ajax({
         type: "POST",
         url: "<?=U(array("action"=>"deletereply"))?>&id=<?=$id?>&rid="+encodeURIComponent(id),
         data: "time="+(new Date()).getTime(),
         success: function(data){
           if(data.substr(0,1)=="!"){
             $.dialog(data.substr(1),{title:"提示"}).time(3);
           }
           if(data.substr(0,1)=="@"){
             parent.toast(data.substr(1));
             $(obj).parent().parent().remove();
             ReplyCheckboxChange();
             if(!$(".ReplyCheckbox").length){location.href=location.href;}
           }
         }
      });
    }
    function ReplyDeleteSelected(){
      var checked=$(".ReplyCheckbox:checked");
      if(!checked.length)return;
      if(!confirm("确定要删除选定的 "+checked.length+" 条备忘补充吗?"))return false;
      var id="";
      checked.each(function(){id+=","+$(this).val();});
      ReplyDeleting(id,checked);
    }
    function ReplyDelete(id,obj){
      if(!confirm("确定要删除该备忘补充吗?"))return false;
      ReplyDeleting(id,obj);
    }
    function MapLocate(title,point){
      $.dialog.open('gears/map/map.html#center='+point+'&zoom=17&width=600&height=360&markers='+point,{
          title:title,width: 602, height: 362,lock:true
      });
    }
    </script>
<?php }elseif($action=="new"){ ?>
    <?=form("post","保存备忘","",array(
      array("edit","title","备忘标题","请输入备忘标题"),
      array("editor","content","备忘内容","请输入备忘内容"),
    ))?>
    <?php if($msg){ ?>
    <script type="text/javascript">$(document).ready(function(){parent.toast("<?=addslashes($msg)?>");});</script>
    <?php } ?>
    <?php if($script){ ?>
    <script type="text/javascript">$(document).ready(function(){<?=$script?>});</script>
    <?php } ?>
<?php }elseif($action=="modify"){ ?>
    <?=form("post","保存备忘","",array(
      array("edit","title","备忘标题","请输入备忘标题",$topic["title"]),
      array("editor","content","备忘内容","请输入备忘内容",$topic["content"]),
    ))?>
    <?php if($msg){ ?>
    <script type="text/javascript">$(document).ready(function(){parent.toast("<?=addslashes($msg)?>");});</script>
    <?php } ?>
    <?php if($script){ ?>
    <script type="text/javascript">$(document).ready(function(){<?=$script?>});</script>
    <?php } ?>
<?php }elseif($action=="modifyreply"){ ?>
    <?=form("post","保存备忘补充","",array(
      array("editor","content","补充内容","请输入补充内容",$reply["content"]),
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