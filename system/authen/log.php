<?php
if(!defined("ROOT"))DIE("DENIED");
$id=strtolower(I("id"));
$max=100;
$logs=array();
$logfile=@fopen(ROOT."/data/authen/authen.log","r");
if($logfile)while($log=fgets($logfile,1024000)){
	$log=explode("|",$log,6);
	if($id)if($id!=$log[0])continue;
	$logs[]=$log;
	if(count($logs)>$max)array_shift($logs);
}
?>
<div class="DialogWrapper">
<table class="table table-striped table-hover">
	<thead>
        <tr>
          <th style="width:60px;">帐号</th>
          <th style="width:80px;">登陆时间</th>
          <th style="width:80px;">登陆IP</th>
          <th style="width:80px;">登陆坐标</th>
          <th style="width:80px;">登陆地点</th>
          <th>浏览器UA</th>
          <th style="width:50px">操作</th>
        </tr>
      </thead>
      <tbody>
      	<?php foreach(array_reverse($logs) as $log){ ?>
      		<tr>
      			<td><?=H($log[0])?></td>
      			<td><?=H($log[1])?></td>
      			<td><?=H($log[2])?></td>
      			<td><?=H(str_replace(",",", ",$log[3]))?></td>
      			<td><?=H($log[4])?></td>
      			<td><?=H($log[5])?></td>
      			<td><a href="javascript:void(0);" onclick="MapLocate('<?=H($log[0])." <".H($log[2])."> [".H($log[1])."]"?>','<?=H($log[3])?>');">查看</a></td>
      		</tr>
      	<?php } ?>
      </tbody>
</table>
</div>