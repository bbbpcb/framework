<?php
$page=max(intval(I("page")),1);
$data=M("xiezilou","`id`>0","`id` DESC",$page,5);
$pn=array_shift($data);
param("page",$pn["page"]);
param("pn","第 {$pn["page"]} / {$pn["maxpage"]} 页");
?>
<table class="Table">
  <tr class="FirstRow">
    <td class="Label" style="width:20%;height:40px;">项目名称</td>
    <td class="Label" style="width:12%;height:40px;">建筑面积<? if(!R("mobile")){ ?><span class="IB">（㎡）</span><? } ?></td>
    <td class="Label" style="width:12%;height:40px;">占地面积<? if(!R("mobile")){ ?><span class="IB">（㎡）</span><? } ?></td>
    <td class="Label" style="width:20%;height:40px;">管理费<span class="IB">标准</span><? if(!R("mobile")){ ?><span class="IB">（元/㎡/月）</span><? } ?></td>
    <td class="Label" style="width:12%;height:40px;"><?=R("mobile")?"":"预计"?>服务等级</td>
    <td class="Label" style="width:12%;height:40px;">详情</td>
    <td class="Label" style="width:12%;height:40px;">删除</td>
  </tr>
<? if($data){ ?>
  <? foreach ($data as $row) { ?>
  <tr class="SavedRow<?=$row["id"]?>">
    <td class="Value" style="height:40px;"><?=H($row["title"])?></td>
    <td class="Value" style="height:40px;"><?=H($row["jzmj"])?></td>
    <td class="Value" style="height:40px;"><?=H($row["zdmj"])?></td>
    <td class="Value" style="height:40px;"><?=H($row["glfbz"])?></td>
    <td class="Value" style="height:40px;"><?=H($row["fwdj"])?></td>
    <td class="Value" style="height:40px;"><a href="<?=U("~/xiezilou/{$row["id"]}/")?>" target="_blank">资源配置</a></td>
    <td class="Value" style="height:40px;"><a class="Delete" href="javascript:void(0);" onclick="if(confirm('确定要删除该条项目数据吗?'))BirdPost(this,{'id':'<?=$row["id"]?>'},'listdelete',false,'正在删除...');">X</a></td>
  </tr>
  <? } ?>
<? }else{ ?>
  <tr>
    <td colspan="7" class="Value" style="height:40px;">
      <div class="BO" style="text-align:center;color:#888;padding:80px 0;">暂无数据</div>
    </td>
  </tr>
<? } ?>
</table>