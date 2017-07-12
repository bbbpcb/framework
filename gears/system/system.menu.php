<?php if(!defined("ROOT"))DIE("DENIED"); ?>

<a class="Menu first" onclick="fold(this);" href="javascript:void(0);" target="Frame">管理中心</a>
<div class="Sidebar">
	<a class="link" onclick="fold(this);" href="gears/prober/prober.php" target="Frame">服务器环境</a>
	<a class="link" onclick="fold(this);" href="?to=authen" target="Frame">管理员名单</a>
	 
</div>

<a class="Menu" onclick="fold(this);" href="javascript:void(0);" target="Frame">站务管理</a>
<div class="Sidebar">
	<a class="link" onclick="fold(this);" href="?to=config&name=main" target="Frame">网站设置</a>
	<a class="link" onclick="fold(this);" href="?to=config&at=chmod" target="Frame">安装维护</a>
	<div class="list">
		<a class="item" onclick="fold(this);" href="?to=config&at=chmod" target="Frame"> 文件夹权限</a>
		<a class="item" onclick="fold(this);" href="?to=config&at=database" target="Frame">数据库配置</a>
		<a class="item" onclick="fold(this);" href="?to=config&at=reset" target="Frame">初始化重置</a>
	</div>
</div>