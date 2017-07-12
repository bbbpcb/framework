<?php
if(!defined("ROOT"))DIE("DENIED");
$name = I(V($_GET['name']), 'main', true);
$action = I(V($_GET['action']), '', true);
$filename=U("|data/configs/link.{$name}.json");
include U("|gears/configs/link.{$name}.php");
if($action=="json"){
  $id=I("id");
  if($id){
    if(array_key_exists($id,$config)){
      $sliders=(array)J(C($filename), true);
      $sliders=isset($sliders[$id])?$sliders[$id]:array();
      $sliders[]=array("name"=>"添加新链接...","type"=>"button","action"=>"append","iconSkin"=>"iconPlus","font"=>array("color"=>"#28a7e1"));
    }else{
      $sliders=array();
    }
  }else{
    $sliders=array();
    foreach($config as $fid=>$folder){
      $sliders[]=array("id"=>$fid,"name"=>$folder[0],"address"=>$fid,"image"=>$folder[1],"summary"=>"","type"=>"folder","isParent"=>true);
    }
  }
  echo json_encode($sliders);
  exit(0);
}elseif($action=="preview"){
	$folder=I("folder");
	if(array_key_exists($folder,$config)){
		$sliders=(array)J(C($filename), true);
		$sliders=isset($sliders[$folder])?$sliders[$folder]:array();
			?>
			  <div id="PreviewWell" class="well well-lg">
			      <?php $i=0;foreach($sliders as $link){$i++; ?>
			        <a href="<?=htmlspecialchars($link["address"])?>" title="<?=htmlspecialchars($link["name"])?>" <?=($link["address"]&&(strtolower(substr($link["address"],0,11))!="javascript:"))?" target=\"_blank\"":""?>><?php if($link["image"]){ ?><img src="<?=htmlspecialchars($link["image"])?>" <?=$link["name"]?"title=\"".htmlspecialchars($link["name"])."\"":""?>><?php }else{ ?><?=htmlspecialchars($link["name"])?><?php } ?></a>
			      <?php }if(!$i){ ?><div style="text-align:center;font-size:12px;color:gray;">暂无链接</div><?php } ?>
			  </div>
			  <?php
	}
	exit(0);
}elseif($action=="append"){
  $folder=I("folder");
  $title=I("title");
  $address=I("address");
  $image=I("image");
  $summary=I("summary");
  $sliders=(array)J(C($filename), true);
  if(!isset($sliders[$folder]))
  	$sliders[$folder]=array();
  $folder=&$sliders[$folder];
  $folder[]=array("name"=>$title?$title:"新链接","address"=>$address?$address:"javascript:void(0);","image"=>$image,"summary"=>$summary,"type"=>"link","iconSkin"=>"iconLink");
  C($filename,J($sliders));
  echo "*添加链接操作执行成功";
  exit(0);
}elseif($action=="modify"){
  $folder=I("folder");
  $index=max(intval(I("index")),0);
  $title=I("title");
  $address=I("address");
  $image=I("image");
  $summary=I("summary");
  $sliders=(array)J(C($filename), true);
  if(!isset($sliders[$folder]))
  	$sliders[$folder]=array();
  $folder=&$sliders[$folder];
  if($index<count($folder)){
  $pointer=&$folder[$index];
  $pointer["name"]=$title?$title:"新链接";
  $pointer["address"]=$address?$address:"javascript:void(0);";
  $pointer["image"]=$image;
  $pointer["summary"]=$summary;
  C($filename,J($sliders));
  echo "*修改链接操作执行成功";
  }else{die("*修改链接操作执行失败");}
  exit(0);
}elseif($action=="move"){
  $folder=I("folder");
  $sourceindex=max(intval(I("source")),0);
  $targetindex=max(intval(I("target")),0);
  $sliders=(array)J(C($filename), true);
  if(!isset($sliders[$folder]))
  	$sliders[$folder]=array();
  $folder=&$sliders[$folder];
  if($sourceindex<count($folder)){
    $targetindex=min($targetindex,count($folder));
    $newfolder=array();
    $c=count($folder);
    for($i=0;$i<=$c;$i++){
      if($i==$sourceindex)continue;
      if($i==$targetindex){$newfolder[]=$folder[$sourceindex];}
      if($i<$c)$newfolder[]=$folder[$i];
    }
    $sliders[I("folder")]=$newfolder;
    C($filename,J($sliders));
    echo "*移动链接操作执行成功";
  }else{die("*移动链接操作执行失败");}
  exit(0);
}elseif($action=="delete"){
  $folder=I("folder");
  $index=max(intval(I("index")),0);
  $title=I("title");
  $address=I("address");
  $image=I("image");
  $sliders=(array)J(C($filename), true);
  if(!isset($sliders[$folder]))
  	$sliders[$folder]=array();
  $folder=&$sliders[$folder];
  if($index<count($folder)){
  $spliced=array_splice($folder,$index,1);
  if($spliced[0]["name"]==$title&&$spliced[0]["address"]==$address){
    C($filename,J($sliders));
    echo "*删除链接操作执行成功";
  }else{die("*删除链接操作执行失败");}}else{die("*删除链接操作执行失败");}
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
#Currents{display:block;overflow:hidden;position:absolute;left:215px;right:10px;top:10px;font-size:12px;line-height:22px;height:22px;color:#555;cursor:default;}
#MainView{display:block;overflow:hidden;overflow-y:auto;position:absolute;left:215px;right:10px;top:40px;bottom:10px;}
#EditorFormFrame{display:block;overflow:hidden;position:relative;width:360px;}
#PreviewWell a{display:inline-block;*display:inline;*zoom:1;margin:5px;}
</style>
<?=$BODYBOF?>
	<ul id="TreeView" class="ztree"></ul>
	<div id="BtnGroup">
		<div class="btn-group btn-group-xs" role="group" aria-label="...">
		  <button type="button" class="btn btn-default" style="width:49px;" onclick="TreeViewRefresh();">刷新</button>
		  <button type="button" class="btn btn-default" style="width:49px;" onclick="TreeViewAppend();">添加</button>
		  <button type="button" class="btn btn-default" style="width:49px;" onclick="TreeViewModify();">修改</button>
		  <button type="button" class="btn btn-default" style="width:49px;" onclick="TreeViewDelete();">删除</button>
		</div>
	</div>
	<div id="Currents">
	    <label>当前:</label> &nbsp; <span id="CurrentNode">无</span>
	    &nbsp;&nbsp;&nbsp;
	    <label>链接:</label> &nbsp; <span id="CurrentNodeAddress">无</span>
	    &nbsp;&nbsp;&nbsp;
	    <label>图片:</label> &nbsp; <span id="CurrentNodeImage">无</span>
	</div>
	<div id="MainView">
		<div id="Preview"></div>
	</div>
	<div style="display:none;">
		<div id="EditorFormFrame" class="FormFrame"><form id="EditorForm" role="form" action="#edit" method="POST" onsubmit="return false"><div class="form-group FormItem FormItemEdit"><label class="FormLabel" for="Form1">链接标题</label><span class="FormHint">&nbsp;请输入链接标题&nbsp;</span><input id="Form1" class="form-control Form FormEdit " type="text" name="title" value="" /><div style="clear:both"></div></div><div class="form-group FormItem FormItemEdit" style="display:none;"><label class="FormLabel" for="Form2">链接描述</label><span class="FormHint">&nbsp;请输入链接描述&nbsp;(&nbsp;选填&nbsp;)&nbsp;</span><input id="Form2" class="form-control Form FormEdit " type="text" name="summary" value="" /><div style="clear:both"></div></div><div class="form-group FormItem FormItemUpload"><label class="FormLabel" for="Form3">链接图片</label><span class="FormHint">&nbsp;请输入链接图片&nbsp;(&nbsp;<span id="ImageSize"></span>&nbsp;)&nbsp;</span><div class="input-group"><input id="Form3" class="form-control Form FormUpload " type="text" name="image" value="" /><span class="input-group-btn"><button id="Form3Button" class="btn btn-default" type="button">上传</button></span></div><div style="clear:both"></div></div><script type="text/javascript">(function(css){var meta=document.createElement("link");meta.setAttribute("rel","stylesheet");meta.setAttribute("type","text/css");meta.setAttribute("href",css);document.getElementsByTagName("head")[0].appendChild(meta);})("gears/editor/themes/default/default.css");</script><script type="text/javascript" src="gears/editor/kindeditor-min.js"></script><script type="text/javascript" src="gears/editor/lang/zh_CN.js"></script><script type="text/javascript">KindEditor.ready(function(K){var KindEditorForm3=K.editor({uploadJson:'gears/editor/upload.php',fileManagerJson:'gears/editor/manager.php',allowFileManager:true});K('#Form3Button').click(function(){KindEditorForm3.loadPlugin('insertfile',function(){KindEditorForm3.plugin.fileDialog({fileUrl:K('#Form3').val(),clickFn:function(url,title){K('#Form3').val(url+((title && url!=title)?'#'+title:''));KindEditorForm3.hideDialog();}});});});});</script><div class="form-group FormItem FormItemEdit"><label class="FormLabel" for="Form4">链接地址</label><span class="FormHint">&nbsp;请输入链接地址&nbsp;</span><input id="Form4" class="form-control Form FormEdit " type="text" name="address" value="" /><div style="clear:both"></div></div></form></div>
	</div>
	<script type="text/javascript">
window.FirstTimeAsync=true;
window.DragParent="";
window.DragSourceIndex=0;
window.DragTargetIndex=0;
$(document).ready(function(){
  $.fn.zTree.init($("#TreeView"),{
    async: {
      enable: true,
      url: "<?=U(array("action"=>"json"))?>",
      autoParam: ["id"],
    },
    view:{
      fontCss:function(treeId, node){return node.font?node.font:{};},
      nameIsHTML: true
    },
    edit: {
      enable: true,
      showRemoveBtn: false,
      showRenameBtn: false,
      drag: {
        isCopy: false,
        isMove: true,
        next: false,
        inner: false
      }
    },
    callback: {
      onClick:function(event,treeId,treeNode,clickFlag){TreeViewOnFocus(treeNode);},
      onRemove:function(event,treeId,treeNode){TreeViewOnBlur();},
      onAsyncSuccess:function(event, treeId, treeNode, msg){
      	if(window.FirstTimeAsync){window.FirstTimeAsync=false;}else{return true;}
        var tree=$.fn.zTree.getZTreeObj(treeId);
        var nodes=tree.getNodes();
        if(nodes.length){tree.selectNode(nodes[0]);TreeViewOnFocus(nodes[0]);}
      },
      beforeDrag: function(treeId, treeNodes){return (treeNodes.length>0)?(treeNodes[0].type=="link"):false;},
      beforeDrop: function(treeId, treeNodes, targetNode, moveType){
        var targetnodetype=targetNode.type?targetNode.type:false;
        if(targetnodetype=="folder")return false;
        var parentnode=targetNode.getParentNode();
        var parent=parentnode.id;
        if(window.DragParent!=parent)return false;
        var tree=$.fn.zTree.getZTreeObj(treeId);
        window.DragTargetIndex=tree.getNodeIndex(targetNode);
        if(window.DragSourceIndex!=window.DragTargetIndex){
          $.post("<?=U(array("action"=>"move"))?>&folder="+parent,{source:window.DragSourceIndex,target:window.DragTargetIndex},
          function(data){if(data.substr(0,1)=="*"){
            /*parent.toast(data.substr(1));*/
            tree.reAsyncChildNodes(parentnode,"refresh");
            tree.selectNode(parentnode);
            TreeViewOnFocus(parentnode);
          }else{parent.toast("移动链接操作执行失败,请重试...");}});
        }
        return false;
      },
      onDrag:function(event, treeId, treeNodes){
          var tree=$.fn.zTree.getZTreeObj(treeId);
          window.DragSourceIndex=tree.getNodeIndex(treeNodes[0]);
          var parentnode=treeNodes[0].getParentNode();
          window.DragParent=parentnode.id;
      },
      onDrop:function(event, treeId, treeNodes, targetNode, moveType){
      },
    }
  },[]);
});
function TreeViewRefresh(){
  TreeViewOnBlur();
  window.FirstTimeAsync=true;
  var tree=$.fn.zTree.getZTreeObj("TreeView");
  tree.reAsyncChildNodes(null,"refresh");
}
function TreeViewAppend(){
  var folder="";
  var tree=$.fn.zTree.getZTreeObj("TreeView");
  var nodes = tree.getSelectedNodes();
  if(!nodes.length){alert("请在左侧区域选择目标文件夹");return;}
  var node=nodes[0];
  var nodeType=node.type?node.type:false;
  if(nodeType=="folder"){folder=node.id;}else{
    node=nodes[0].getParentNode();
    folder=node.id;
  }
  $("#EditorForm .FormEdit,#EditorForm .FormUpload").val("");
  $("#ImageSize").html(node.image);
  $.dialog({
      content:document.getElementById('EditorFormFrame'),
      title:"添加新链接...",
      lock:true,
      ok: function () {
          $.post("<?=U(array("action"=>"append"))?>&folder="+folder,$("#EditorForm").serialize(),
          function(data){if(data.substr(0,1)=="*"){
            parent.toast(data.substr(1));
            var tree=$.fn.zTree.getZTreeObj("TreeView");
            tree.reAsyncChildNodes(node,"refresh");
            tree.selectNode(node);
            TreeViewOnFocus(node);
            }else{parent.toast("添加链接操作执行失败,请重试...");}});
          return true;
      },
      cancelVal: '关闭',
      cancel: true
  });
}
function TreeViewModify(){
  var tree=$.fn.zTree.getZTreeObj("TreeView");
  var nodes = tree.getSelectedNodes();
  if(!nodes.length){alert("请在左侧区域选择需要修改的链接");return;}
  var node=nodes[0];
  var nodeType=node.type?node.type:false;
  if(nodeType!="link"){alert("请在左侧区域选择需要修改的链接");return;}
  var parentnode=nodes[0].getParentNode();
  var folder=parentnode.id;
  var nodeindex=tree.getNodeIndex(nodes[0]);
  $("#Form1").val(node.name);
  $("#Form2").val(node.summary);
  $("#Form3").val(node.image);
  $("#Form4").val(node.address);
  $("#ImageSize").html(parentnode.image);
  $.dialog({
      content:document.getElementById('EditorFormFrame'),
      title:"修改链接: "+node.name,
      lock:true,
      ok: function () {
          $.post("<?=U(array("action"=>"modify"))?>&folder="+folder+"&index="+nodeindex,$("#EditorForm").serialize(),
          function(data){if(data.substr(0,1)=="*"){
            parent.toast(data.substr(1));
            tree.reAsyncChildNodes(parentnode,"refresh");
            tree.selectNode(parentnode);
            TreeViewOnFocus(parentnode);
          }else{parent.toast("修改链接操作执行失败,请重试...");}});
          return true;
      },
      cancelVal: '关闭',
      cancel: true
  });
}
function TreeViewDelete(){
  var tree=$.fn.zTree.getZTreeObj("TreeView");
  var nodes = tree.getSelectedNodes();
  if(!nodes.length){alert("请在左侧区域选择需要删除的链接");return;}
  var node=nodes[0];
  var nodeType=node.type?node.type:false;
  if(nodeType!="link"){alert("请在左侧区域选择需要删除的链接");return;}
  var nodeindex=tree.getNodeIndex(nodes[0]);
  if(!confirm("确定要删除链接 ["+node.name+"] 吗?"))return;
  var parentnode=nodes[0].getParentNode();
  var folder=parentnode.id;
  $.post("<?=U(array("action"=>"delete"))?>&folder="+folder+"&index="+nodeindex,{title:node.name,address:node.address,image:node.image},
  function(data){if(data.substr(0,1)=="*"){
    parent.toast(data.substr(1));
    tree.reAsyncChildNodes(parentnode,"refresh");
    tree.selectNode(parentnode);
    TreeViewOnFocus(parentnode);
  }else{parent.toast("删除链接操作执行失败,请重试...");}});
}
function TreeViewOnFocus(node){
  var tree=$.fn.zTree.getZTreeObj("TreeView");
  var render=$("#Preview");
  var nodeType=node.type?node.type:false;
  var nodeindex=tree.getNodeIndex(node);
  var isParent=nodeType=="folder";
  var parentnode=isParent?node:node.getParentNode();
  var parent=parentnode.id;
  if(nodeType=="button"){
    TreeViewOnBlur();
    if(node.action=="append"){
    	$("#CurrentNode").html(parentnode.name);
    	$("#CurrentNodeAddress").html("添加新链接");
    	$("#CurrentNodeImage").html(parentnode.image?parentnode.image:"无");
    	TreeViewAppend();
    }
    isParent=true;
  }else{
  	if(isParent)if(!node.open)tree.expandNode(node,true,true,true);
    $("#CurrentNode").html(node.name);
    $("#CurrentNodeAddress").html(node.address);
    $("#CurrentNodeImage").html(node.image?node.image:"无");
  }
  if(!isParent){
  	return true;
  }
  render.html("");
  $.ajax({
    url: "<?=U(array("action"=>"preview"))?>&folder="+parent,
    cache: false,
    success: function(html){render.html(html);var s=$.trim($("#Preview .Scripted").text());if(s)eval(s);}
  });
}
function TreeViewOnBlur(){
  $("#CurrentNode").html("无");
  $("#CurrentNodeAddress").html("无");
  $("#CurrentNodeImage").html("无");
}
	</script>
<?=$BODYEOF?>
<?=$HTMLEOF?>