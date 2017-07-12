<?php
if(!defined("ROOT"))DIE("DENIED");
R('form.php');
$name = I(V($_GET['name']), '', true);
$file = I(V($_GET['file']), $name, true);
$form = I(V($_POST['FORMNAME']), '', true);
include U("|gears/configs/{$name}.php");
if ($form == 'update') {
    C(U("|data/configs/{$file}.json"), J(FormData($config)));
}
global $values;
$values = J(C(U("|data/configs/{$file}.json")), true);
if (!is_array($values)) {
    $values = array();
}
array_walk($config, 'FormFill', $values);
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
<?=form("update","保存设置","",$config)?>
<?php if($form=="update"){ ?>
<script type="text/javascript">$(document).ready(function(){parent.toast("当前设置已保存!");});</script>
<?php } ?>
<?=$BODYEOF?>
<?=$HTMLEOF?>