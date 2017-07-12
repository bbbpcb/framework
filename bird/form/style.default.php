<?
if(!defined("ROOT"))die("DENIED");
$_FORMDIALOGED=I(V($_GET["STAMP"]),FALSE,TRUE);
?><!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?=I(V($_GET["@title"]),"",TRUE)?></title>
    <link rel="stylesheet" type="text/css" href="gears/jquery/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="gears/system/system.global.css">
    <script type="text/javascript" src="gears/jquery/jquery-1.11.1.min.js"></script>
    <script type="text/javascript" src="gears/jquery/bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="gears/jquery/dialog/dialog.js?skin=blue"></script>
    <script type="text/javascript" src="gears/jquery/dialog/iframe.js"></script>
    <script type="text/javascript" src="gears/jquery/bird.js"></script>
    <style type="text/css">html,body{margin:0;padding:0;cursor:default;}</style>
</head>
<body>
<div id="FormRoot" style="margin:20px;"><?=form("UPDATE",I(V($_GET["@button"]),"提交",TRUE),"",$_FORM)?></div>
<script type="text/javascript">
$(document).ready(function(){
<? if($_FORMDIALOGED){ ?>
    $("#FormRoot .FormSubmit").hide();
    $.dialog.open.api.button({name:"<?=I(V($_GET["formbutton"]),"提交",TRUE)?>",focus:true,callback:function(){$("#FormRoot form").submit();return false;}},{name:"取消"});
<? } ?>
<? if(isset($_FORMBREAK)){ ?>
    <? if($_FORMBREAK){ ?>
        <? if(is_array($_FORMBREAK)){ ?>
            BirdToast($(".FormItem .Form[name='<?=trim(V($_FORMBREAK[0]))?>']").focus().parent(),"<?=trim(V($_FORMBREAK[1],"请正确填写该项内容"))?>");
        <? }else{ ?>
            $.dialog({fixed:true,ok:"确定",title:"提示",content:"<img src=\"gears/jquery/dialog/skins/icons/warning.png\"> <span style=\"font-size:12px;\"><?=addslashes(V($_FORMBREAK))?></span>"});
        <? } ?>
    <? }elseif($_FORMDIALOGED){ ?>
        $.dialog.close();
    <? }else{ ?>
        $.dialog({time:2,fixed:true,title:"提示",content:"<img src=\"gears/jquery/dialog/skins/icons/succeed.png\"> <span style=\"font-size:12px;\"><?=addslashes(I(V($_GET["@message"]),"提示：数据提交成功！",TRUE))?></span>"});
    <? } ?>
<? }elseif($_FORMDIALOGED){ ?>
    if(BirdFrameData("values")){
        BirdWrite("#FormRoot .FormItem .Form",BirdFrameData("values"));
        if(BirdFrameData("valuessubmit"))$("#FormRoot form").submit();
    }
    if(BirdFrameData("focus"))$("#FormRoot .FormItem .Form[name='"+BirdFrameData("focus")+"']").focus();
<? } ?>
});
</script>
</body>
</html>