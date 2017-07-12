<?php
if(!defined("ROOT"))DIE("DENIED");
$form = I(V($_POST['FORMNAME']), '', true);
if($form=="update"){
	C(U("|data/configs/database.php"),"<?php if(!defined('ROOT'))DIE('DENIED');define('DATABASE','".strtolower(I("type"))."://".I("user").":".I("pass")."@".I("host")."/".I("name")."');");
	G(U(array("status"=>"updated","time"=>time())));
}
R('form.php');
$database=array("host"=>"localhost","user"=>"root","pass"=>"","name"=>"test");
if(defined("DATABASE")){
	$db=parse_url(DATABASE);
	$database["host"]=V($db["host"],"localhost");
	$database["user"]=V($db["user"],"root");
	$database["pass"]=V($db["pass"]);
	$database["name"]=trim(V($db["path"],"test"),"/");
}
function DatabaseCheck($db, $auto = false)
{
    $link = @mysql_connect($db['host'], $db['user'], $db['pass']);
    if (!$link) {
        return array('result' => false, 'reason' => mysql_error(), 'flag' => 1);
    }
    if (!@mysql_query('SET NAMES \'UTF8\'', $link)) {
        return array('result' => false, 'reason' => mysql_error(), 'flag' => 2);
    }
    if (!@mysql_query('SET @@sql_mode=\'\'', $link)) {
        return array('result' => false, 'reason' => mysql_error(), 'flag' => 3);
    }
    if (!@mysql_select_db($db['name'], $link)) {
        if ($auto) {
            @mysql_query('CREATE DATABASE `' . $db['name'] . '` COLLATE \'utf8_general_ci\';', $link);
            if (!@mysql_select_db($db['name'], $link)) {
                return array('result' => false, 'reason' => mysql_error(), 'flag' => 4);
            }
        } else {
            return array('result' => false, 'reason' => mysql_error(), 'flag' => 4);
        }
    }
    $tables = array();
    $query = @mysql_query('SHOW TABLES', $link);
    if ($query) {
        while ($row = mysql_fetch_array($query)) {
            $tables[] = $row[0];
        }
        mysql_free_result($query);
    }
    return array('result' => true, 'tables' => $tables);
}
$check=DatabaseCheck($database,I("status")=="updated");
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

<?php if($check["result"]){ ?>
	<?php if(in_array("timer", $check["tables"])){ ?>
		<div class="alert alert-success" role="alert"><strong>连接状态：</strong>数据库通信正常...</div>
	<?php }else{ ?>
		<div class="alert alert-warning" role="alert"><strong>连接状态：</strong>数据库通信正常，但找不到网站相关数据表，是否进行网站初始化安装?</div>
		<div style="margin-bottom:20px;"><a class="btn btn-warning" href="?to=config&at=reset" style="padding-left: 22px;padding-right: 22px;"><strong>网站初始化安装</strong></a>&nbsp;&nbsp;<span style="font-size:12px;color:#888;">可以将数据库恢复到初始状态并重新安装网站所需相关数据表.</span></div>
	<?php } ?>
<?php }else{ ?>
	<div class="alert alert-danger" role="alert"><strong>连接状态：</strong>数据库通信异常，<?=W($check["flag"]==1?"与数据库服务器连接失败":"",$check["flag"]==2?"设置字符编码失败":"",$check["flag"]==3?"设置连接模式失败":"",$check["flag"]==4?"选择名为 `<strong>".$database["name"]."</strong>` 的数据库失败":"")?>！</div>
	<div class="well well-sm" style="font-size:12px;color:#888;">&nbsp;&nbsp;<strong style="color:#666;">错误反馈：</strong> <?=$check["reason"]?></div>
<?php } ?>

<?=form("update","保存配置","",array(
	array("text","type","数据库类型","支持的数据库类型","MySQL"),
	array("edit","host","数据库主机","请填写数据库主机",V($database["host"])),
	array("edit","name","数据库名","请填写数据库名称",V($database["name"])),
	array("edit","user","帐号","请填写数据库帐号",V($database["user"])),
	array("password","pass","密码","请填写数据库密码",V($database["pass"])),
))?>
<?php if($form=="update"){ ?>
<script type="text/javascript">$(document).ready(function(){parent.toast("当前设置已保存!");});</script>
<?php } ?>
<?=$BODYEOF?>
<?=$HTMLEOF?>