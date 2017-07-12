<?php
if(!defined("ROOT"))DIE("DENIED");
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="utf-8">
	<meta name="renderer" content="webkit">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>网站管理中心</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<link rel="shortcut icon" href="gears/system/system.ico" type="image/x-icon" />
	<style type="text/css"><?php $Size=array("header"=>62,"footer"=>0,"sidebar"=>180); ?>
	html,body{margin:0;padding:0;width:100%;height:100%;background:#eee;}
	a{outline:0;}
	#Wrapper{display:block;overflow:hidden;position:relative;width:100%;height:100%;margin:0 auto;min-width:600px;min-height:360px;background:#fff;}
	.Block{display:block;overflow:hidden;position:absolute;}
	#Header{left:0;top:0;right:0;height:<?=$Size["header"]-1?>px;background:#f8f8f8 url(gears/system/system.edge.png) left top repeat-x;border-bottom:1px solid #387ab4;}
	#Header #Logo{left:0;top:0;width:<?=$Size["sidebar"]?>px;height:60px;background:url(gears/system/system.logo.png) center center no-repeat;}
	#Header .Drop{top:0;border:1px solid #ddd;border-top:0;border-radius:0 0 5px 5px;box-shadow:0px 0px 2px #d6d6d6;}
	#Header .Drop .Dropper{line-height:16px;font-size:12px;border-radius:0 0 5px 5px;border:1px solid #fff;background:#f7f7f7;color:#666;padding:0 8px  1px 8px;vertical-align:middle;}
	#Header .Drop .Dropper img{vertical-align:middle;}
	#Header .Drop .Dropper span.span{vertical-align:middle;}
	#Header .Drop .Dropper a{text-decoration:none;color:#666;}
	#Header .Drop .Dropper a:hover{color:#222;}
	#Header .Link{right:10px;}
	#Header .Link #Link{}
	#Header .User{left:<?=$Size["sidebar"]-1?>px;}
	#Header .User #User{cursor:default;}
	#Header .User #User #Message b{color:red;text-shadow:1px 0px 2px #fff799;}
	#Header #Menu{left:<?=$Size["sidebar"]-1?>px;top:<?=$Size["header"]-35?>px;right:0;}
	#Header #Menu .Menu{display:block;overflow:hidden;float:left;margin-right:10px;line-height:30px;height:30px;font-size:14px;font-weight:bold;text-decoration:none;padding:0 20px;border:1px solid #aaa;border-bottom-color:#eee;background:#f5f5f5 url(gears/system/system.edge.png) left -32px repeat-x;color:#999;font-family:"Microsoft Yahei",Arial,SimSun;border-radius:5px 5px 0 0;margin-top:5px;}
	#Header #Menu .Menu.first{border-left-width:1px;}
	#Header #Menu .Menu:hover{color:#888;background-position:left -10px;line-height:30px;margin-top:2px;}
	#Header #Menu .Menu.active{background:#fff url(gears/system/system.edge.png) left top repeat-x;color:#387ab4;border-top:2px solid #387ab4;border-bottom-width:0;line-height:32px;height:35px;margin-top:0;}
	#Header #Menu .Sidebar{display:none;}
	<?php if($Size["footer"]>0){ ?>#Footer{left:0;bottom:0;right:0;height:<?=$Size["footer"]?>px;line-height:<?=$Size["footer"]?>px;background:#f8f8f8 url(gears/system/system.edge.png) left bottom repeat-x;text-align:center;font-size:12px;color:#666;}<?php } ?>
	#Sidest{top:<?=$Size["header"]?>px;bottom:<?=$Size["footer"]?>px;left:0;width:<?=$Size["sidebar"]?>px;background:#4091d5 url(gears/system/system.side.png) left top repeat-y;}
	#Sidest #Sider{left:0;top:0;bottom:0;width:<?=$Size["sidebar"]+32?>px;overflow-y:auto;}
	#Sidest #Sider #Sidebar{display:block;overflow:hidden;width:<?=$Size["sidebar"]-1?>px;cursor:default;}
	#Sidebar a{cursor:pointer;}
	#Sidebar .link{display:block;overflow:hidden;line-height:28px;border-top:1px solid #73b8ec;border-bottom:1px solid #387ab4;color:#f8f8f8;font-size:14px;font-weight:bold;text-decoration:none;padding:5px 10px;text-align:center;text-shadow:1px 1px 2px #266caa;cursor:pointer;font-family:"Microsoft Yahei",Arial,SimSun;}
	#Sidebar .link:hover{color:#fff;text-shadow:2px 2px 3px #255988;}
	#Sidebar .link.active{border-top:1px solid #fff;background:#f6f6f6;color:#333;text-shadow:0px 0px 0px #fff;}
	#Sidebar .link.fold{}
	#Sidebar .link.fold:hover{}
	#Sidebar .link.fold.active{}
	#Sidebar .list{display:none;overflow:hidden;font-size:12px;line-height:30px;color:#ddeeff;}
	#Sidebar .list.active{display:block;}
	#Sidebar .list a{text-decoration:none;color:#ddeeff;}
	#Sidebar .list .item{display:block;overflow:hidden;line-height:20px;padding: 5px 10px;text-align:center;color:#ddeeff;}
	#Sidebar .list a:hover{color:#fff;}
	#Sidebar .list .item:hover{background:#387ab4;}
	#Sidebar .list .item.active{background:#387ab4;}
	#Sidebar .list a.item.active{color:#fff;}
	#Sidebar .list .item .active{color:#fff;}
	#Window{top:<?=$Size["header"]?>px;bottom:<?=$Size["footer"]?>px;left:<?=$Size["sidebar"]?>px;right:0;background:#fefefe;}
	#Frame{display:block;overflow:hidden;width:100%;height:100%;border:0;}
	#Wrapper.FullFrame #Sidest #Sider #Sidebar{display:none;}
	#Wrapper.FullFrame #Window{left:0;}
	#Toast{display:none;bottom:50px;left:50%;width:600px;margin-left:-300px;text-align:center;}
	#Toast #Toasting{display:inline-block;*display:inline;*zoom:1;background:#555;background:rgba(0,0,0,0.66);border-radius:9px;font-size:12px;line-height:20px;color:#fff;padding:0 20px;cursor:default;text-shadow:1px 1px 1px #333;}
	@media print{.NoPrint{display:none;}}
	</style>
</head>
<body>
	<div id="Wrapper" class="FullFrame">
		<div id="Header" class="Block">
			<a id="Logo" class="Block" href="javascript:void(0);"></a>
			<div class="User Drop Block">
				<div id="User" class="Dropper">
					<img src="gears/system/system.user.png">
					<span class="span"><?=$Authen["super"]?"高级":""?>管理员：<a href="?to=authen&action=profile" target="Frame"><strong><?=$Authen["id"]?></strong></a></span>
					<span class="span">[<a href="?to=authen&at=msg" target="Frame" id="Message">短消息</a>]</span>
					<span class="span">[<a href="?to=authen&at=memo" target="Frame">备忘录</a>]</span>
					<span class="span">[<a href="?to=authen&at=date" target="Frame">日程表</a>]</span>
					<span class="span">[<a href="?quit=<?=time()?>">退出</a>]</span>
				</div>
			</div>
			<div class="Link Drop Block NoPrint">
				<div id="Link" class="Dropper">
					<span class="span">
						<a href="./" target="_blank">网站首页</a>
						|
						<a href="javascript:void(0);" onclick="flush();">更新缓存</a>
						|
						<a href="?quit=<?=time()?>">退出系统</a>
					</span>
				</div>
			</div>
			<div id="Menu" class="Block">
				<?php require("system.menu.php"); ?>
				<div style="clear:both"></div>
			</div>
		</div>
		<?php if($Size["footer"]>0){ ?><div id="Footer" class="Block">Copyright <span style="font-family:Arial;">&copy;</span> <?=date("Y")?> 网站管理中心 版权所有 All Rights Reserved</div><?php } ?>
		<div id="Sidest" class="Block">
			<div id="Sider" class="Block">
				<div id="Sidebar"></div>
			</div>
		</div>
		<div id="Window" class="Block"><iframe id="Frame" name="Frame" frameborder="0" src="about:blank"></iframe></div>
		<div id="Toast" class="Block"><span id="Toasting"></span></div>
	</div>
	<script type="text/javascript">
	window.toaster=0;
	function hasClass(obj,cls){return obj.className.match(new RegExp('(\\s|^)'+cls+'(\\s|$)'));}  
    function addClass(obj,cls){if(!hasClass(obj,cls))obj.className+=" "+cls;}
    function removeClass(obj,cls){if(hasClass(obj,cls))obj.className=obj.className.replace(new RegExp('(\\s|^)'+cls+'(\\s|$)'),' ');}
    function addChildClass(obj,cls){var childs=obj.childNodes;for(var i=0;i<childs.length;i++)if(childs[i].nodeType==1)addClass(childs[i],cls);}
    function removeChildClass(obj,cls){var childs=obj.childNodes;for(var i=0;i<childs.length;i++)if(childs[i].nodeType==1)removeClass(childs[i],cls);}
    function fold(obj){
    	removeChildClass(obj.parentNode,"active");
    	removeChildClass(obj.parentNode,"fold");
		addClass(obj,"active");
		var next=obj.nextSibling;
		while((next!=null)&&(next.nodeType!=1))
			next=next.nextSibling;
		if((next!=null)&&(hasClass(next,"list"))){
			addClass(obj,"fold");
			removeChildClass(next,"active");
			addClass(next,"active");
		}
		if(hasClass(obj,"Menu")){
			var flag=(next!=null)&&(hasClass(next,"Sidebar"));
			if(flag){
				document.getElementById("Sidebar").innerHTML=next.innerHTML;
				removeClass(document.getElementById("Wrapper"),"FullFrame");
				var items=document.getElementById("Sidebar").childNodes;
				for(var i=0;i<items.length;i++)
					if(items[i].nodeType==1)
						if(hasClass(items[i],"link")){
							items[i].click();
							break;
						}
			}else{
				addClass(document.getElementById("Wrapper"),"FullFrame");
			}
		}
    }
    function menu(){
    	var menus=document.getElementById("Menu").childNodes;
    	for(var i=0;i<menus.length;i++)
    		if(menus[i].nodeType==1)
    			if(hasClass(menus[i],"Menu")){
    				fold(menus[i]);
    				break;
    			}
    }
    function toast(msg,duration){
    	duration=duration?duration:3000;
    	if(window.toaster)
    	clearTimeout(window.toaster);
    	window.toaster=0;
    	document.getElementById("Toasting").innerHTML=msg.indexOf("</")>0?msg:"<span>"+msg+"</span>";
    	document.getElementById("Toast").style.display="block";
    	if(duration)setTimeout(function(){
    		window.toaster=0;
    		document.getElementById("Toast").style.display="none";
    	},duration);
    }
    function post(url,param,callback){
    	var request=null;
		if(window.XMLHttpRequest){
			request=new XMLHttpRequest();
		}else if(window.ActiveXObject){
			request=new ActiveXObject("Microsoft.XMLHTTP");
		}
		if (request){
			request.open("post",url,true);
			request.setRequestHeader('Content-type','application/x-www-form-urlencoded');
		    request.onreadystatechange=function(){
		    	if (request.readyState==4){
		    		if(request.status==200){
		    			callback(request.responseText);
					}else{
						toast("服务器无响应,请检查网络");
					}
				}
		    };
		    request.send(param);
		}
	}
	function ping(){
		post("gears/system/system.msg.php","time="+(new Date()).getTime(),function(data){
			if(data.substr(0,1)=="!"){
				document.getElementById("Message").innerHTML=data.substr(1);
			}else if(data.substr(0,1)=="@"){
				toast(data.substr(1));
			}
		});
	}
	function flush(){
		post("?to=config&at=flush","time="+(new Date()).getTime(),function(data){
			if(data.substr(0,1)=="!"){
				toast(data.substr(1));
			}else{
				toast("服务器无响应,请检查网络");
			}
		});
	}
	setInterval(function(){ping();},30000);
	ping();
	menu();
	</script>
</body>
</html>