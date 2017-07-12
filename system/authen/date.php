<?php
if(!defined("ROOT"))DIE("DENIED");
$action=strtolower(I("action","main"));
if($action=="main"){
?>
<?=$HTMLBOF?>
<link rel="stylesheet" type="text/css" href="gears/jquery/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="gears/jquery/calendar/calendar.css">
<link rel="stylesheet" type="text/css" href="gears/jquery/calendar/theme/theme.css">
<script type="text/javascript" src="gears/jquery/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="gears/jquery/bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript" src="gears/jquery/dialog/dialog.js?skin=blue"></script>
<script type="text/javascript" src="gears/jquery/dialog/iframe.js"></script>
<script type="text/javascript" src="gears/jquery/calendar/moment.js"></script>
<script type="text/javascript" src="gears/jquery/calendar/calendar.js"></script>
<script type="text/javascript" src="gears/jquery/calendar/lang.js"></script>
<style type="text/css">
html,body{margin:0;padding:0px;}
body{padding:20px;}
#Calendar{font-family:"Microsoft Yahei";}
#Calendar h2{font-size:22px;}
#Calendar .fc-event-container{cursor:pointer;}
.FormItem{margin:5px 0 5px 5px;}
.FormItemEdit input{padding:3px 10px;width:300px;}
.FormItemSelect select{padding:3px 10px;}
.FormItemTextarea textarea{padding:3px 10px;width:300px;}
</style>
<?=$BODYBOF?>
<div id="Calendar"></div>
<script>
function CalendarHeight(){return $(window).height()-50;}
$(document).ready(function(){
  $('#Calendar').fullCalendar({
        theme:true,
        selectable:true,
        eventLimit: true,
        dragOpacity:{agenda:.5},
        events:"<?=U(array("action"=>"events"))?>",
        header: {
          left: 'prev,next today',
          center: 'title',
          right: 'month,agendaWeek,agendaDay'
        },
        timezone:"UTC",
        select:function(start,end){
                       var dialog=art.dialog({id:'DATE_NEW',title:"新建日程 "+start.format('YYYY-MM-DD'),lock:true,padding:"10"});
                       $.ajax({url:"<?=U(array("action"=>"append"))?>&start="+start.unix()+"&end="+end.unix(),cache: false,
                              success:function(data){dialog.content(data).button({name:'保存',focus:true,callback:function(){if($("#EventAppendForm").length)$.post($("#EventAppendForm").attr("action"),$("#EventAppendForm").serialize(),function(data){if(data.substr(0,1)=="*"){var msg=data.substr(1);$('#Calendar').fullCalendar('refetchEvents');dialog.close();parent.toast(msg);}else{alert("保存日程失败,请稍后重试...");}});return false;}},{name:'取消'});}
                        });
        },
        eventClick:function(event,jsEvent,view){
                        var dialog=art.dialog({id:'DATE_MODIFY',title:"编辑日程 #"+event.id,lock:true,padding:"10"});
                        $.ajax({url:"<?=U(array("action"=>"modify"))?>&id="+event.id,cache: false,
                              success:function(data){dialog.content(data).button({name:'保存',focus:true,callback:function(){if($("#EventModifyForm").length)$.post($("#EventModifyForm").attr("action"),$("#EventModifyForm").serialize(),function(data){if(data.substr(0,1)=="*"){var msg=data.substr(1);$('#Calendar').fullCalendar('refetchEvents');dialog.close();parent.toast(msg);}else{alert("保存日程失败,请稍后重试...");}});return false;}},{name:'删除',callback:function(){if($("#EventDeleteForm").length)$.post($("#EventDeleteForm").attr("action"),$("#EventDeleteForm").serialize(),function(data){if(data.substr(0,1)=="*"){var msg=data.substr(1);$('#Calendar').fullCalendar('refetchEvents');dialog.close();parent.toast(msg);}else{alert("删除日程失败,请稍后重试...");}});return false;}},{name:'取消'});}
                        });
        },
        windowResize:function(view){$('#Calendar').fullCalendar('option','height',CalendarHeight());},
        height:CalendarHeight()
    });
});
</script>
<?=$BODYEOF?>
<?=$HTMLEOF?>
<?php
}elseif($action=="events"){
function ParseRequestTime($p,$g=null){$q=new DateTime($p,$g?$g:new DateTimeZone('UTC'));if($g){$q->setTimezone($g);}return $q->format("U");}
$start=ParseRequestTime($_GET['start']);$end=ParseRequestTime($_GET['end']);$data=array();
foreach(M("timer","(`flag`=2)AND(`target`='".addslashes($Authen["id"])."')AND(((`start`>='{$start}')AND(`start`<='{$end}'))OR((`end`>='{$start}')AND(`end`<='{$end}'))) ORDER BY `id` ASC") as $e)$data[]=array("id"=>$e["id"],"start"=>T($e["start"],"Y-m-d H:i:s",0),"end"=>T($e["end"],"Y-m-d H:i:s",0),"title"=>$e["title"],"color"=>$e["style"],"allDay"=>(Round(($e["end"]-$e["start"])/3600)>23)||(T($e["start"],"Y-m-d",0)!=T($e["end"],"Y-m-d",0)));
echo json_encode($data);
}elseif(in_array($action,array("append","modify"))){
$append=($action=="append");
if($append){
$start=intval(I("start",time()));
$end=intval(I("end",$start+3600));
if($end<=$start)$end=$start+3600;
$color="#3a87ad";
}else{
$id=intval(I("id"));
$date=M("timer","(`flag`=2)AND(`target`='".addslashes($Authen["id"])."')AND(`id`='{$id}')",1);
if(!$date)die("该日程不存在,请刷新后重试.");
$color=$date["style"];
}
?>
<div id="Event<?=ucfirst($action)?>">
<?php if(!$append){ ?><form id="EventDeleteForm" method="POST" action="<?=U(array("action"=>"post","post"=>"delete","id"=>$id))?>" onsubmit="return false;"></form><?php } ?>
<form id="Event<?=ucfirst($action)?>Form" method="POST" action="<?=U(array("action"=>"post","post"=>$action,"id"=>$append?"":$id))?>" onsubmit="return false;">
  <div class="FormItem FormItemEdit"><label for="FIE_S">起始时间：</label> <input type="text" id="FIE_S" name="start" value="<?=$append?T($start,"Y-m-d H:i",0):T($date["start"],"Y-m-d H:i",0)?>"></div>
  <div class="FormItem FormItemEdit"><label for="FIE_E">结束时间：</label> <input type="text" id="FIE_E" name="end" value="<?=$append?T($end,"Y-m-d H:i",0):T($date["end"],"Y-m-d H:i",0)?>"></div>
  <div class="FormItem FormItemEdit"><label for="FIE_T">日程标题：</label> <input type="text" id="FIE_T" name="title" value="<?=$append?T($start,"Y-m-d",0)." 日程":H($date["title"])?>"></div>
  <div class="FormItem FormItemSelect"><label for="FIE_C">日程标识：</label> <select id="FIE_C" name="color" onchange="$('#ColorSample').css({'background':$('#FIE_C').val()});">
    <option value="#3a87ad" <?=$color=="#3a87ad"?"selected":""?>>默认 - 一般日程</option>
    <option value="red" <?=$color=="red"?"selected":""?>>红色 - 重要日程</option>
    <option value="blue" <?=$color=="blue"?"selected":""?>>蓝色 - 工作日程</option>
    <option value="green" <?=$color=="green"?"selected":""?>>绿色 - 会议日程</option>
    <option value="orange" <?=$color=="orange"?"selected":""?>>橙色 - 阶段日程</option>
    <option value="black" <?=$color=="black"?"selected":""?>>黑色 - 其他日程</option>
  </select>&nbsp;&nbsp;<span id="ColorSample" style="display:inline-block;*display:inline;*zoom:1;color:#FFF;background:<?=$color?>;color:#FFF;padding:2px 18px;border-radius:3px;text-shadow:0 -1px 1px gray;">日程标识显示样例</span></div>
  <div class="FormItem FormItemTextarea" style="margin-bottom:-20px;"><label for="FIE_T" style="vertical-align:top;">日程内容：</label> <textarea rows="5" name="content"><?=$append?"":$date["content"]?></textarea></div>
</form>
</div>
<?php
}elseif($action=="post"){
  $post=I("post");
  if(in_array($post,array("append","modify"))){
    $start=T(I("start"),0,0);
    $end=T(I("end"),0,0);
    if($end<=$start)$end=$start+3600;
  }
  if(in_array($post,array("modify","delete"))){
    $id=intval(I("id"));
  }
  if($post=="append"){
    echo M("timer",array("flag"=>2,"target"=>$Authen["id"],"source"=>$Authen["id"],"stamp"=>time(),"start"=>$start,"end"=>$end,"style"=>I("color"),"title"=>I("title"),"ip"=>R("ip"),"address"=>$Authen["address"],"location"=>$Authen["location"],"content"=>I("content")))?"*提示:日程添加成功!":"";
  }elseif($post=="modify"){
    echo M("timer",array("stamp"=>time(),"start"=>$start,"end"=>$end,"style"=>I("color"),"title"=>I("title"),"ip"=>R("ip"),"address"=>$Authen["address"],"location"=>$Authen["location"],"content"=>I("content")),"(`flag`=2)AND(`target`='".addslashes($Authen["id"])."')AND(`id`='{$id}')")?"*提示:日程编辑成功!":"*提示:日程编辑无变化!";
  }elseif($post=="delete"){
    echo M("timer",null,"(`flag`=2)AND(`target`='".addslashes($Authen["id"])."')AND(`id`='{$id}')")?"*提示:日程删除成功!":"*提示:日程删除失败,请稍后重试!";
  }
}
?>