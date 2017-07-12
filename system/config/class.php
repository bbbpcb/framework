<?php
if(!defined("ROOT"))DIE("DENIED");
$name = I(V($_GET['name']), 'main', true);
$action = I(V($_GET['action']), '', true);
include U("|gears/system/system.class.php");
$class=@SystemClassLoad($name);
if($action=="json"){
  function JsonClass(&$Parent,&$Children,$Name,$Address="",$Root=true){
    $Parent["name"]=$Name;
    $Parent["address"]=$Root?"":($Address?$Address.".".$Name:$Name);
    if(is_array($Children)){
      $Parent["children"]=array();
      $Parent["open"]=true;
      $Array=&$Parent["children"];
      foreach($Children as $name=>$value){
        $array=array();
        JsonClass($array,$value,$name,$Parent["address"],false);
        if($array)$Array[]=$array;
      }if($Root)$Parent=$Parent["children"];
    }elseif($Root){$Parent=array();}
  }
  if(isset($_REQUEST["address"])){
    $pth=I("address");
    $pths=array_filter(explode(".",trim($pth,".")));
    $pthed=$pths?true:false;
    $pop=array_pop($pths);
    $list=@array_flip(SystemClassList($class,$pth));
    $array=array();
    JsonClass($array,$list,$pop,implode(".",$pths),!$pthed);
    echo json_encode($array);
  }else{
    $array=array();
    JsonClass($array,$class,$name);
    echo json_encode($array);
  }
exit(0);}elseif($action=="source"){
  echo C(SystemClassFile($name));
  exit(0);
}elseif($action=="save"){
  $content=SystemClassReturn(I("content"));
  $class=SystemClassDecode($content);
  SystemClassSave($class,$name);
  echo "*保存分类源数据成功";
  exit(0);
}elseif($action=="append"){
  $address=I("address");
  $items=array_filter(explode("\r\n",I("acitem")));
  if($items){
    $pre=(I("actype")=="root"?"":($address?$address.".":""));
    $text="";foreach($items as $item)$text.="\r\n".$pre.$item;
    $classtext=SystemClassReturn(C(SystemClassFile($name)).$text);
    $class=SystemClassDecode($classtext);
    SystemClassSave($class,$name);
    echo "*添加分类操作执行成功";}
  exit(0);
}elseif($action=="delete"){
  if(SystemClassDelete($class,I("address"))){SystemClassSave($class,$name);echo "*删除分类操作执行成功";}
  exit(0);
}elseif($action=="rename"){
  if(SystemClassRename($class,I("address"),I("newname"))){SystemClassSave($class,$name);echo "*修改分类操作执行成功";}
  exit(0);
}
?>
<?=$HTMLBOF?>
<link rel="stylesheet" type="text/css" href="gears/jquery/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="gears/jquery/ztree/ztree.css">
<script type="text/javascript" src="gears/jquery/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="gears/jquery/bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript" src="gears/jquery/dialog/dialog.js?skin=blue"></script>
<script type="text/javascript" src="gears/jquery/ztree/ztree.js"></script>
<style type="text/css">
html,body{margin:0;padding:0px;display:block;overflow:hidden;width:100%;height:100%;}
.ztree li span.button.iconLink_ico_docu{margin-right:5px; background: url(gears/jquery/ztree/img/zTreeStandard.png) no-repeat scroll -128px -64px transparent; vertical-align:top; *vertical-align:middle}
.ztree li span.button.iconPlus_ico_docu{margin-right:5px; background: url(gears/jquery/ztree/img/zTreeStandard.png) no-repeat scroll right top transparent; vertical-align:top; *vertical-align:middle}
.ztree li a.curSelectedNode{height:18px;}
#TreeView{display:block;overflow:auto;position:absolute;left:10px;top:40px;bottom:10px;width:200px;}
#BtnGroup{display:block;overflow:hidden;position:absolute;left:10px;top:10px;width:200px;}
#BtnGroup button{color:#888;}
#BtnGroup button:hover{color:#333;}
#Currents{display:block;position:absolute;left:215px;right:10px;top:10px;font-size:12px;line-height:22px;height:22px;color:#555;cursor:default;}
#Currents #CurrentNodeInfor{color:#888;}
#MainView{display:block;overflow:hidden;position:absolute;left:215px;right:10px;top:40px;bottom:10px;}
#Editor{display:block;position:absolute;left:0;top:5px;bottom:50px;font-size:14px;line-height:20px;width:100%;padding:5px 10px;}
#EditorPanel{display:block;overflow:hidden;position:absolute;left:0;bottom:0;height:40px;width:100%;}
#EditorSave{position:absolute;top:0;right:0;z-index:100;}
#EditorHint{display:none;color:#888;}
.DialogHint{font-size:12px;line-height:30px;color:#333;font-weight:bold;display:inline-block;*display:inline;*zoom:1;}
#NewName{padding:5px 10px;width:300px;}
.DialogLabel{font-weight:bold;font-size:12px;line-height:24px;color:#333;}
#AppendCatParent{padding:5px 10px;width:400px;color:#333;}
#DialogForm{line-height:20px;}
</style>
<?=$BODYBOF?>
	<ul id="TreeView" class="ztree"></ul>
	<div id="BtnGroup">
		<div class="btn-group btn-group-xs" role="group" aria-label="...">
		  <button type="button" class="btn btn-default" style="width:49px;" onclick="TreeViewRefresh();">刷新</button>
		  <button type="button" class="btn btn-default" style="width:49px;" onclick="TreeViewAppend();">添加</button>
		  <button type="button" class="btn btn-default" style="width:49px;" onclick="TreeViewRename();">修改</button>
		  <button type="button" class="btn btn-default" style="width:49px;" onclick="TreeViewDelete();">删除</button>
		</div>
	</div>
	<div id="Currents">
	    <label>当前分类:</label> &nbsp; <span id="CurrentNode">无</span><span id="CurrentNodeInfor"></span>
	    &nbsp;&nbsp;&nbsp;
	    <label>完整路径:</label> &nbsp; <span id="CurrentNodeAddress">无</span>
	</div>
	<div id="MainView">
    <textarea id="Editor" disabled><?=C(SystemClassFile($name))?></textarea>
    <div id="EditorPanel">
      <input id="EditorSave" class="btn btn-primary" type="button" value="保存分类数据" style="display:none" onclick="EditorSave();">
      <div class="checkbox"><label onselectstart="return false;"><input id="EditorCanEdit" type="checkbox" onchange="EditorMode();"><strong>快速编辑模式</strong></label>&nbsp;&nbsp;<small id="EditorHint">每行一条分类路径,分类层级请使用半角英文句号分割.</small></div>
    </div>
	</div>
	<div style="display:none">
    <div id="DialogRename">
      <div class="DialogHint" style="font-size:14px;">修改分类名称：</div>
      &nbsp;&nbsp;
      <input id="NewName" type=text value="">
    </div>
    <div id="DialogFormed">
      <form id="DialogForm" onsubmit="return false">
      <div class="DialogHint">分类级别：</div>
      <input id="AppendCatTypeRoot" name="actype" type=radio value="root" onclick="$('#AppendCatParentFrame').hide();">&nbsp;<label class="DialogLabel" for="AppendCatTypeRoot" onclick="$('#AppendCatParentFrame').hide();">顶级分类</label>&nbsp;&nbsp;
      <input id="AppendCatTypeChild" name="actype" type=radio value="child" onclick="$('#AppendCatParentFrame').show();">&nbsp;<label id="AppendCatTypeChildLabel" class="DialogLabel" for="AppendCatTypeChild" onclick="$('#AppendCatParentFrame').show();">子级分类</label><br>
      <div id="AppendCatParentFrame">
      <div class="DialogHint">上级分类：</div>
      <input id="AppendCatParent" name="address" type=text value="" readonly><br>
      </div>
      <div class="DialogHint">分类项目：</div>&nbsp;&nbsp;<small style="color:#888;">每行一条分类路径,分类层级请使用半角英文句号分割.</small>
      <textarea id="AppendCat" name="acitem" style="width:500px;height:200px;font-size:14px;line-height:20px;padding:5px 10px;"></textarea>
      </form>
    </div>
	</div>
	<script type="text/javascript">
$(document).ready(function(){
  $.fn.zTree.init($("#TreeView"),{
    async: {
      enable: true,
      url: "<?=U(array("action"=>"json"))?>",
    },
    callback: {
      onClick:function(event,treeId,treeNode,clickFlag){TreeViewOnFocus(treeNode);},
      onRemove:function(event,treeId,treeNode){TreeViewOnBlur();},
      onAsyncSuccess:function(event, treeId, treeNode, msg){
        var tree=$.fn.zTree.getZTreeObj(treeId);
        var nodes=tree.getNodes();
        if(nodes.length){tree.selectNode(nodes[0]);TreeViewOnFocus(nodes[0]);}
      },
    }
  },[]);
});
function TreeViewOnFocus(treeNode){
  $("#CurrentNode").html(treeNode.name);
  $("#CurrentNodeInfor").html("");
  $("#CurrentNodeAddress").html(treeNode.address);
  $.getJSON("<?=U(array("action"=>"json"))?>&address="+encodeURIComponent(treeNode.address), function(json){
    var children=json.children;
    var count=children.length;
    var title="";
    if(count)for(var i=0;i<count;i++)title+=children[i].name+(i<count-1?",&nbsp;":"");
    $("#CurrentNodeInfor").html(count?" <span id='CurrentNodeInforTip' title='"+title+"' data-toggle='tooltip' data-placement='bottom'>(包含 "+count+" 个子分类)</span>":"");
    if(count)$('#CurrentNodeInforTip').tooltip();
  });
}
function TreeViewOnBlur(){
  $("#CurrentNode").html("无");
  $("#CurrentNodeInfor").html("");
  $("#CurrentNodeAddress").html("无");
}
function TreeViewRefresh(){
  TreeViewOnBlur();
  var tree=$.fn.zTree.getZTreeObj("TreeView");
  tree.reAsyncChildNodes(null,"refresh");
  $("#Editor").val("正在载人,请稍后...");
  $.ajax({
    url: "<?=U(array("action"=>"source"))?>",
    cache: false,success: function(data){$("#Editor").val(data);}
  });
}
function TreeViewAppend(){
  var tree=$.fn.zTree.getZTreeObj("TreeView");
  var nodes=tree.getSelectedNodes();
  var node=false;if(nodes.length){node=nodes[0];}
  $("#AppendCat").val("");
  if(node){
    $("#AppendCatTypeChildLabel").css({"color":"#333"});
    $("#AppendCatTypeChild").prop("disabled", false);
    $("#AppendCatTypeRoot").prop("checked", false);
    $("#AppendCatTypeChild").prop("checked", true);
    $("#AppendCatParentFrame").show();
    $("#AppendCatParent").val(node.address);
  }else{
    $("#AppendCatTypeChildLabel").css({"color":"#aaa"});
    $("#AppendCatTypeChild").prop("disabled", true);
    $("#AppendCatTypeChild").prop("checked", false);
    $("#AppendCatTypeRoot").prop("checked", true);
    $("#AppendCatParentFrame").hide();
    $("#AppendCatParent").val("");
  }
  $.dialog({
      content:document.getElementById('DialogFormed'),
      padding:"10",
      width:500,
      title:"添加分类",
      ok: function () {
          $.post("<?=U(array("action"=>"append"))?>",$("#DialogForm").serialize(),
          function(data){if(data.substr(0,1)=="*"){parent.toast(data.substr(1));TreeViewRefresh();}else{parent.toast("添加分类失败,请重试...");}});
          return true;
      },
      cancelVal: '关闭',
      cancel: true
  });
}
function TreeViewRename(){
  var tree=$.fn.zTree.getZTreeObj("TreeView");
  var nodes=tree.getSelectedNodes();
  if(!nodes.length){alert("请在左侧区域选择需要修改的分类");return;}
  var node=nodes[0];
  $("#NewName").val(node.name);
  $.dialog({
      content:document.getElementById('DialogRename'),
      title:"修改分类",
      ok: function () {
          var oldname=node.name;
          var newname=$("#NewName").val();
          if(!newname.length){return true;}
          if(oldname==newname){return true;}
          $.post("<?=U(array("action"=>"rename"))?>",{name:node.name,address:node.address,newname:$("#NewName").val()},
          function(data){if(data.substr(0,1)=="*"){parent.toast(data.substr(1));TreeViewRefresh();}else{parent.toast("修改分类失败,请重试...");}});
          return true;
      },
      cancelVal: '关闭',
      cancel: true
  });
}
function TreeViewDelete(){
  var tree=$.fn.zTree.getZTreeObj("TreeView");
  var nodes = tree.getSelectedNodes();
  if(!nodes.length){alert("请在左侧区域选择需要删除的分类");return;}
  var node=nodes[0];
  if(!confirm("确定要删除分类 ["+node.name+"] 吗?"))return;
  $.post("<?=U(array("action"=>"delete"))?>",{name:node.name,address:node.address},
  function(data){if(data.substr(0,1)=="*"){parent.toast(data.substr(1));TreeViewRefresh();}else{parent.toast("删除分类失败,请重试...");}});
}
function EditorMode(){
  var mode=$("#EditorCanEdit").prop("checked");
  $("#Editor").prop("disabled",!mode);
  $("#EditorSave,#EditorHint").toggle(mode);
}
function EditorSave(){
  $.post("<?=U(array("action"=>"save"))?>",{content:$("#Editor").val()},
  function(data){if(data.substr(0,1)=="*"){parent.toast(data.substr(1));TreeViewRefresh();}else{parent.toast("保存分类数据操作失败,请重试...");}});
}
	</script>
<?=$BODYEOF?>
<?=$HTMLEOF?>