<?php
if(!defined("ROOT"))DIE("DENIED");
$form = I(V($_POST['FORMNAME']), '', true);
if($form=="update"){
	R("db.php");$total=0;
	L(ROOT."/data/authen/reset.log",implode("|",array($Authen["id"],T($Authen["login"]),$Authen["ip"],$Authen["location"],$Authen["address"],R("UA"))));
	foreach(glob(U("|system/*.sql")) as $file)$total+=ImportSQL($file);
}
?>
<?=$HTMLBOF?>
<link rel="stylesheet" type="text/css" href="gears/jquery/bootstrap/css/bootstrap.min.css">
<script type="text/javascript" src="gears/jquery/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="gears/jquery/bootstrap/js/bootstrap.min.js"></script>
<style type="text/css">
html,body{margin:0;padding:0px;}
body{padding:20px;}
</style>
<?=$BODYBOF?>

<div class="alert alert-warning" role="alert"><strong>操作提示：</strong>重置后将清空所有数据表内容并恢复到初始状态,清除的数据将无法恢复,请谨慎操作!</div>

<div class="well" style="font-size:12px;">
  当网站由于各种原因需要将数据表恢复到出厂状态时,可以选择进行初始化重置操作.<br><br>
  一般进行初始化重置操作的原因有:
  <ul>
    <li>需要清空网站所有发布及未发布的数据时</li>
    <li>数据库表结构被破坏或丢失,并且尝试恢复无效后需要重新安装表结构时</li>
    <li>网站转让至其他公司,并且转让协议不包含网站现有数据时</li>
  </ul><br>
  注意: 初始化重置操作清除范围仅限于数据表中的所有数据,不包含管理员帐号数据及上传附件等.<br><br>
  警告: 初始化重置操作将清空所有数据表内容,强烈建议在备份好当前数据表内容后再进行该操作.<br>
</div>

<?php if($form=="update"){ ?>
<div class="alert alert-success" role="alert"><strong>状态提示：</strong>初始化重置操作于 <?=T(time())?> 执行完毕!</div>
<script type="text/javascript">$(document).ready(function(){parent.toast("初始化重置操作执行完毕!");});</script>
<?php }else{ ?>
<form role="form" method="POST" action="<?=$_SERVER["REQUEST_URI"]?>" onsubmit="return confirm('您确定要执行数据初始化重置操作吗?');">
	<input type="hidden" name="FORMNAME" value="update">
	<div class="checkbox">
		<label style="font-size:14px;color:red;font-weight:bold">
			<input type="checkbox" onchange="$('#GoSubmit').prop('disabled',!$(this).prop('checked'));"> 我已认真阅读以上操作须知并同意承担由此产生的一切后果
		</label>
	</div>
	<br>
	<input id="GoSubmit" class="btn btn-danger" type="submit" value="进行初始化重置操作" disabled style="padding-left: 22px;padding-right: 22px;">
</form>
<?php } ?>
<?=$BODYEOF?>
<?=$HTMLEOF?>