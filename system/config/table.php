<?php
if(!defined("ROOT"))DIE("DENIED");
$unit=I(V($_GET["unit"]),"",true);
$unitof=I(V($_GET["unitof"]),"个",true);
$table=I(V($_GET["table"]),"",true);
$where=I(V($_GET["where"]),"",true);
$orderby=I(W(V($_GET["orderby"]),V($_GET["order"])),"",true);
$primary=I(V($_GET["primary"]),"",true);
$action=I(V($_GET["action"]),"index",true);
$build=I(V($_GET["build"]),"",true);
$treemode=I("treemode");
$catkey=I("catkey");
$filterclass=I("filterclass");
$thekeywords=I("thekeywords");
$thesearchid=I("thesearchid");
$searchid=I("searchid");
$searchin=I("searchin");
$searchin=explode(",",$searchin);
$searchin=array_filter($searchin);
$wheres=array();
if($filterclass && $catkey)$wheres[]="(`{$catkey}` LIKE '%".addslashes($filterclass)."%')";
if($searchid && $thesearchid)$wheres[]="(`{$searchid}`='".addslashes($thesearchid)."')";
if($thekeywords && $searchin){
    foreach(explode(" ",$thekeywords) as $key){
        $key=trim($key);
        if(!strlen($key))continue;
        $key="%".addslashes($key)."%";
        $ws=array();
        foreach ($searchin as $sifield) {
            $ws[]="(`{$sifield}` LIKE '{$key}')";
        }
        $wheres[]="(".implode("OR",$ws).")";
    }
}
if($wheres){$where=($where?("(".$where.")AND"):"")."(".implode("AND",$wheres).")";}

global $unit,$unitof,$table,$where,$orderby,$primary,$build,$action;
function classoptions($class,$value=""){
  $class=J(C(U("|data/configs/class.{$class}.tree.json")),true);
  if(!is_array($class))$class=array();
  $option=array();
  foreach($class as $address=>$caption)$option[]=($address==$value?"*":"").$caption."=>".$address;
  return $option;
}
function tablecomment($table){
  $tablesfile=U("|data/caches/tablecomments.json");
  if((!defined("DEVMODE"))&&(is_file($tablesfile))){
    $tablecomments=J(C($tablesfile),true);
    if((is_array($tablecomments))&&(array_key_exists($table,$tablecomments)))
    return $tablecomments[$table];
  }
  $tablecomments=array();
  $query=M("SHOW TABLE STATUS");
  while($row=mysql_fetch_assoc($query))$tablecomments[$row["Name"]]=$row["Comment"];
  @C($tablesfile,J($tablecomments));
  return array_key_exists($table,$tablecomments)?$tablecomments[$table]:"";
}
function structure($table){
  global $primary;
  $structurefile=U("|data/caches/table.{$table}.json");
  if((!defined("DEVMODE"))&&(is_file($structurefile))){
    $structure=J(C($structurefile),true);
    if(!$primary)foreach($structure as $param)if(isset($param["primary"]))$primary=$param["primary"];
    return $structure;
  }
  $structurecfg=U("|data/configs/table.{$table}.json");
  $structure=is_file($structurecfg)?J(C($structurecfg),true):array();
  $query=M("SHOW FULL FIELDS FROM `{$table}`");
  while($field=mysql_fetch_assoc($query)){
    $id=$field["Field"];
    $param=array();
    $type=str_replace("("," ",$field["Type"])." ";
    $type=substr($type,0,strpos($type," "));
    $param["type"]=$type;
    $param["tipo"]=W(in_array($type,array("tinyint","smallint","mediumint","int","bigint"))?"integer":"",in_array($type,array("decimal","float","double"))?"float":"",in_array($type,array("char","varchar"))?"string":"",in_array($type,array("tinytext","text","mediumtext","longtext"))?"text":"",in_array($type,array("date","datetime","timestamp","time","year"))?"datetime":"","other");
    if((!$primary)&&(($field["Key"]=="PRI")||($field["Extra"]=="auto_increment")))$param["primary"]=$primary=$id;
    $comment=trim($field["Comment"]);
    $pos=strpos($comment,"{");
    $json="";
    if($pos!==false){
      $json=J(substr($comment,$pos),true);
      $comment=trim(substr($comment,0,$pos));
    }
    if((!strlen($comment))&&($primary==$id)){
      $comment="编号||ignore";
    }
    if((!strlen($comment))&&(substr($id,0,6)=="authen")){
      $comment="验证||ignore";
    }
    if(strlen($comment)){
      $i=0;
      foreach(explode("|",$comment,5) as $value){
        if(strlen($value))switch($i){
          case 0:$param["caption"]=$value;break;
          case 1:$param["hint"]=$value;break;
          case 2:$param["input"]=$value;break;
          case 3:$param["default"]=$value;break;
          case 4:$param["extra"]=$value;break;
        }
        $i++;
      }
    }
    if(is_array($json))foreach($json as $key=>$value)$param[strtolower($key)]=$value;
    if(isset($structure[$id]))$param=array_merge($param,$structure[$id]);
    $param["input"]=strtolower(V($param["input"],$param["tipo"]=="text"?"memo":"edit"));
    $param["caption"]=V($param["caption"],$id);
    $param["hint"]=V($param["hint"]);
    $param["default"]=V($param["default"],"",false);
    $param["reset"]=in_array($param["input"],array("checkbox","radiobox","combobox","listbox"));
    $param["ignore"]=in_array($param["input"],array("none","false","hidden","ignore","ignored"));
    $structure[$id]=$param;
  }
  @C($structurefile,J($structure));
  return $structure;
}$structure=structure($table);
$eventfile=U("|gears/tables/{$table}.php");
if(is_file($eventfile))include($eventfile);
if(!function_exists("BuildTitle")){
if(!$build)$build=tablecomment($table);
function BuildTitle($Index,$Row,$Primary,$Structure,$Build){
  if(!$Build){global $Unit;return $Unit.$Row[$Primary];}
  $Build=(substr($Build,0,1)=="=")?"echo ".substr($Build,1).";":"?>".$Build;
  extract($Row);C(true);eval($Build);return C(false);
}}

if($action=="delete"){
$e=M($table,null,"(`{$primary}` in (".trim(I(V($_GET["id"]),"",true),",")."))");
echo "@{$e}".$unitof.$unit."删除成功!";
exit(0);
}elseif(in_array($action,array("index","append","modify"))){
if($action!="index"){
R("form.php");$form=I(V($_POST["FORMNAME"]),"",true);
  $values=array();
  if($form){
    foreach($structure as $fid=>$field)if(!$field["ignore"])$values[$fid]=I(V($_POST[md5($fid)]),"",true);
    if(array_key_exists("authenoperator",$structure))$values["authenoperator"]=$Authen["id"];
    if(array_key_exists("authenaddress",$structure))$values["authenaddress"]=$Authen["address"];
    if(array_key_exists("authenlocation",$structure))$values["authenlocation"]=$Authen["location"];
    if(array_key_exists("authenstamp",$structure))$values["authenstamp"]=time();
    if(array_key_exists("authenip",$structure))$values["authenip"]=R("IP");
    if(array_key_exists("authenua",$structure))$values["authenua"]=R("UA");
    if($form=="append"){
      if(array_key_exists("authenappendoperator",$structure))$values["authenappendoperator"]=$Authen["id"];
      if(array_key_exists("authenappendaddress",$structure))$values["authenappendaddress"]=$Authen["address"];
      if(array_key_exists("authenappendlocation",$structure))$values["authenappendlocation"]=$Authen["location"];
      if(array_key_exists("authenappend",$structure))$values["authenappend"]=time();
      if(array_key_exists("authenappended",$structure))$values["authenappended"]=time();
      if(array_key_exists("authenappendip",$structure))$values["authenappendip"]=R("IP");
      if(array_key_exists("authenappendua",$structure))$values["authenappendua"]=R("UA");
      if(function_exists("WhenFormSubmit"))WhenFormSubmit($values,$structure);
      if(function_exists("WhenFormSubmit_Append"))WhenFormSubmit_Append($values,$structure);
      $i=M($table,$values);
      if($treemode){
        echo "<script type=\"text/javascript\" src=\"gears/jquery/jquery-1.11.1.min.js\"></script><script type=\"text/javascript\" src=\"gears/jquery/dialog/dialog.js?skin=blue\"></script><script type=\"text/javascript\" src=\"gears/jquery/dialog/iframe.js\"></script><script type=\"text/javascript\">parent.toast(\"".addslashes($unit."已添加!")."\");$.dialog.close();</script>";
      }else{
        echo "<script type=\"text/javascript\">parent.toast(\"".addslashes($unit."已添加!")."\");location.href=\"".U(array("action"=>"append"))."\";</script>";
        exit(0);
      }
      
    }elseif($form=="modify"){
      if(array_key_exists("authenmodifyoperator",$structure))$values["authenmodifyoperator"]=$Authen["id"];
      if(array_key_exists("authenmodifyaddress",$structure))$values["authenmodifyaddress"]=$Authen["address"];
      if(array_key_exists("authenmodifylocation",$structure))$values["authenmodifylocation"]=$Authen["location"];
      if(array_key_exists("authenmodify",$structure))$values["authenmodify"]=time();
      if(array_key_exists("authenmodifyip",$structure))$values["authenmodifyip"]=R("IP");
      if(array_key_exists("authenmodifyua",$structure))$values["authenmodifyua"]=R("UA");
      if(function_exists("WhenFormSubmit"))WhenFormSubmit($values,$structure);
      if(function_exists("WhenFormSubmit_Modify"))WhenFormSubmit_Modify($values,$structure);
      $e=M($table,$values,"`{$primary}`=".intval(I(V($_GET["id"]),0,true)));
      if($treemode){
        echo "<script type=\"text/javascript\" src=\"gears/jquery/jquery-1.11.1.min.js\"></script><script type=\"text/javascript\" src=\"gears/jquery/dialog/dialog.js?skin=blue\"></script><script type=\"text/javascript\" src=\"gears/jquery/dialog/iframe.js\"></script><script type=\"text/javascript\">parent.toast(\"".addslashes($unit."已保存!")."\");$.dialog.close();</script>";
      }else{
        echo "<script type=\"text/javascript\">parent.toast(\"".addslashes($unit."已保存!")."\");location.href=\"".U(array("action"=>"index"))."\";</script>";
        exit(0);
      }
      
    }
  }
}
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
.RowTitle a{color:#000;text-decoration:none;}
.RowAuthenOperator{font-size:12px;line-height:14px;margin-top:-2px;color:#888;}
.RowAuthenStamp{font-size:12px;line-height:14px;color:#999;margin-bottom:-6px;}
.FormCombobox{font-family:simsun;}
</style>
<script>function ChangeClass(who,oldurl){location.href=oldurl+"&page=1&filterclass="+encodeURIComponent($(who).val());}</script>
<?=$BODYBOF?>
<div class="TopButtons" style="<?=$treemode?"display:none;":""?>">
    <div style="float:left;">
    <? if(5>6){ ?>
    <?php if(in_array($action,array("index"))){ ?><a class="btn btn-default btn-sm" href="<?=U(array("action"=>"append"))?>">添加<?=$unit?></a><?php } ?>
    <?php if(in_array($action,array("append","modify"))){ ?><a class="btn btn-default btn-sm" href="<?=U(array("action"=>"index"))?>">返回<?=$unit?>列表</a><?php } ?>
    <? } ?>
    <?php if(in_array($action,array("modify"))){ ?><a class="btn btn-default btn-sm" href="<?=U(array("action"=>"index"))?>">返回<?=$unit?>列表</a><?php } ?>
    <?php if(in_array($action,array("index","modify"))){ ?><a class="btn btn-default btn-sm" href="<?=U(array("refresh"=>time()))?>">刷新</a><?php } ?>
    <?php if(in_array($action,array("index"))){ ?><a class="btn btn-default btn-sm RowCheckboxToggled" href="javascript:void(0);" onclick="RowDeleteSelected();" style="display:none;">删除所选<?=$unit?></a><?php } ?>
    </div>
    <?php if(in_array($action,array("index"))){ ?>
      <div style="float:left;padding-left:5px;">
      <script>function ChangeKeywords(who,oldurl){location.href=oldurl+"&page=1&thekeywords="+encodeURIComponent($("#IPTK").val())+"&thesearchid="+encodeURIComponent($("#IPUS").val());return false;}</script>
      <form method="POST" action="<?=U(array("page"=>"1"))?>" class="form-inline" onsubmit="return ChangeKeywords(this,'<?=U(array("page"=>"1"))?>');">
      
      <? if(I("catname")){ ?>
      <div class="form-group">
        <label class="sr-only">分类</label>
        <div class="input-group">
          <div class="input-group-addon">分类</div>
          <select class="form-control input-sm" onchange="ChangeClass(this,'<?=U(array("page"=>"1"))?>');">
            <option value="">所有分类</option>
          <? foreach(J(C(U("|data/configs/class.".I("catname").".tree.json")),true) as $classKey=>$classValue){ ?>
            <option value="<?=$classKey?>" <?=$filterclass==$classKey?"selected":""?>><?=$classValue?></option>
          <? } ?>
          </select>
        </div>
      </div>
      <? } ?>
      <? if($searchin){ ?>
      <div class="form-group">
        <label class="sr-only" for="IPTK">关键字</label>
        <div class="input-group">
          <div class="input-group-addon">关键字</div>
          <input type="text" class="form-control input-sm" id="IPTK" name="thekeywords" placeholder="搜索关键字" value="<?=H($thekeywords)?>">
        </div>
      </div>
      <button type="submit" id="FilterBtnGo" class="btn btn-primary input-sm">搜索</button>
      </form>
      </div>
      <? } ?>
    <?php } ?>
    <div style="clear:both"></div>
</div>

<?php
if($action=="index"){
$authens=array_key_exists("authenoperator",$structure) && array_key_exists("authenstamp",$structure);
$data=M($table,$where,$orderby,intval(I(V($_GET["page"]),1,true)),intval(I(V($_GET["count"]),10,true)));
$pn=array_shift($data);
?>
<table class="table table-striped table-hover">
  <thead>
    <tr>
      <th style="width:80px;"><input id="RowCheckboxToggle" type="checkbox" value="*" onchange="RowCheckboxToggle(this);">&nbsp;ID</th>
      <th><?=$unit?>列表</th>
      <?php if($authens){ ?><th style="width:150px;">最后编辑</th><?php } ?>
      <th style="text-align:center;width:150px;">操作</th>
    </tr>
  </thead>
  <tbody>
  <?php $i=0;foreach($data as $row){ ?>
    <tr id="RowID_<?=$row[$primary]?>" class="Row">
        <td><label><input id="RowCheckbox_<?=$row[$primary]?>" class="RowCheckbox" type="checkbox" value="<?=$row[$primary]?>" onclick="RowCheckboxChange();">&nbsp;<?=$row[$primary]?></label></td>

        <td class="RowTitle"><a href="<?=U(array("action"=>"modify","id"=>$row[$primary]))?>"><?=H(BuildTitle($i,$row,$primary,$structure,$build))?></a></td>
        <?php if($authens){$autheninfor=V($row["authenaddress"]).(isset($row["authenip"])?"(IP:".$row["authenip"].")":""); ?><td><div class="RowAuthenOperator"><strong title="<?=H($autheninfor)?>" <?php if(V($row["authenlocation"])){ ?>style="cursor:pointer;" onclick="MapLocate('<?=H($autheninfor)?>','<?=$row["authenlocation"]?>');" <?php } ?>><?=$row["authenoperator"]?></strong>&nbsp;</div><div class="RowAuthenStamp"><?=T($row["authenstamp"],"Y-m-d H:i")?></div></td><?php } ?>
        <td style="text-align:center;"><a href="<?=U(array("action"=>"modify","id"=>$row[$primary]))?>">编辑</a> <a href="javascript:void(0);" onclick="RowDelete(<?=$row[$primary]?>,this);">删除</a></td>
    </tr>
  <?php $i++;} ?>
  </tbody>
</table>
<?php if(!$data){ ?><div style="text-align:center;padding:100px 0;color:#bbb;font-size:12px;">暂无<?=$unit?>数据</div><?php } ?>
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
<script type="text/javascript">
function RowCheckboxToggle(obj){$(".RowCheckbox").prop("checked",$(obj).prop("checked"));RowCheckboxChange();}
function RowCheckboxChange(){
  var checked=$(".RowCheckbox:checked");
  $(".RowCheckboxToggled").toggle(checked.length>0);
}
function RowDeleting(id,obj){
  $.ajax({
     type: "POST",
     url: "<?=U(array("action"=>"delete"))?>&id="+encodeURIComponent(id),
     data: "time="+(new Date()).getTime(),
     success: function(data){
       if(data.substr(0,1)=="!"){
         $.dialog(data.substr(1),{title:"提示"}).time(3);
       }
       if(data.substr(0,1)=="@"){
         parent.toast(data.substr(1));
         $(obj).parents("tr").remove();
         RowCheckboxChange();
         if(!$(".RowCheckbox").length){location.href=location.href;}
       }
     }
  });
}
function RowDeleteSelected(){
  var checked=$(".RowCheckbox:checked");
  if(!checked.length)return;
  if(!confirm("确定要删除选定的 "+checked.length+" <?=$unitof.$unit?>吗?"))return false;
  var id="";
  checked.each(function(){id+=","+$(this).val();});
  RowDeleting(id,checked);
}
function RowDelete(id,obj){
  var row=$("#RowID_"+id);
  if(!confirm("确定要删除该<?=$unit?>吗?"))return false;
  RowDeleting(id,obj);
}
function MapLocate(title,point){
  $.dialog.open('gears/map/map.html#center='+point+'&zoom=17&width=600&height=360&markers='+point,{
      title:title,width: 602, height: 362,lock:true
  });
}
</script>
<?php }else{ ?>
<?php
$append=$action=="append";
if(!$append){
  $id=intval(I(V($_GET["id"]),0,true));
  $row=M($table,"`{$primary}`={$id}",1);
  if(!$row){header("location: ".U(array("action"=>"index")));exit(0);}
}
$forms=array();
foreach($structure as $fid=>$field){
  $value=$field["default"];
  if($field["input"]=="class"){
    $field["input"]="combobox";
    $value=classoptions($value,$append?"":$row[$fid]);
  }elseif(!$append){
    $value=$field["reset"]?FormOptions($value,explode("|",$row[$fid])):$row[$fid];
  }
  $item=array($field["input"],md5($fid),$field["caption"],$field["hint"],$value);
  if(!$field["ignore"])$forms[$fid]=$item;
}
if(function_exists("WhenFormCreate"))WhenFormCreate($forms,$structure);
if($append){
if(function_exists("WhenFormCreate_Append"))WhenFormCreate_Append($forms,$structure);
}else{
if(function_exists("WhenFormCreate_Modify"))WhenFormCreate_Modify($forms,$structure);
}
?>
<?=form($action,$append?"添加".$unit:"保存".$unit,"",$forms);?>
<?php } ?>

<?=$BODYEOF?>
<?=$HTMLEOF?>
<?php } ?>