<?php REQUIRE("global/global.php"); ?>
<? //$Logined=S("Logined");if(!$Logined)G("/"); ?>
<?php REQUIRE("global/cheader.php"); ?>
  <?php $SID=V($_ROUTER[1]);$SOW=array();$SAVED=array();if(strlen($SID) && is_numeric($SID)){$SID=intval($SID);$SOW=M("chanyeyuan","`id`={$SID}",1);$SAVED=J($SOW["post"],true);}if(!is_array($SAVED))$SAVED=array(); ?>
  <?php $NAVIDX=2;REQUIRE("global/ctitle.php"); ?>
  <script type="text/javascript">
function CusuanLevel(lv) {
    $(".CusuanResultFrame .TableFrame .Table").removeClass("LevelBM LevelB LevelA LevelAA");
    if (lv) $(".CusuanResultFrame .TableFrame .Table").addClass("Level" + lv);
}
function JingsuanLevel(lv) {
    $(".JingsuanResultFrame .TableFrame .Table").removeClass("LevelBM LevelB LevelA LevelAA");
    if (lv) $(".JingsuanResultFrame .TableFrame .Table").addClass("Level" + lv);
} <?
if ($SOW) { ?>$(document).ready(function() {
        GoPage( <?=V($SOW["type"]) ? 6 : 3 ?>); <?
        if ($SOW["type"]) { ?>$("#JingsuanBtn").click(); <?
        } else { ?>$("#CusuanBtn").click(); <?
        } ?>$(".ResultSaveBar").hide();
        $(".NavDots .NavDot").addClass("actived");
        $(".NavDots .NavSep").addClass("actived");
        $(".NavTitleBtns .Btn.Orange").attr("onclick", "window.close();history.go(-1);");
        $(".NavDots .NavDot").unbind("click").attr("onclick", "return false;");
    }); <?
} ?></script>
  <div class="Root">
    <div class="Pages">
      <div class="Page Page5" section="Save">
        <div class="SavedResultFrame BO RA">
          <div class="Caption YH">项目数据列表</div>
          <div class="SavedListPost" name="page" style="display:none;">1</div>
          <div id="SavedListWaiting" style="display:none;">
            <table class="Table">
              <tr class="FirstRow">
                <td class="Label" style="width:20%;height:40px;">项目名称</td>
                <td class="Label" style="width:12%;height:40px;">建筑面积
                  <? if(!R("mobile")){ ?>
                  <span class="IB">（㎡）</span>
                  <? } ?></td>
                <td class="Label" style="width:12%;height:40px;">占地面积
                  <? if(!R("mobile")){ ?>
                  <span class="IB">（㎡）</span>
                  <? } ?></td>
                <td class="Label" style="width:20%;height:40px;">管理费<span class="IB">标准</span>
                  <? if(!R("mobile")){ ?>
                  <span class="IB">（元/㎡/月）</span>
                  <? } ?></td>
                <td class="Label" style="width:12%;height:40px;"><?=R("mobile")?"":"预计"?>
                  服务等级</td>
                <td class="Label" style="width:12%;height:40px;">详情</td>
                <td class="Label" style="width:12%;height:40px;">删除</td>
              </tr>
              <tr>
                <td colspan="7" class="Value" style="height:40px;"><div class="BO" style="text-align:center;color:#888;padding:80px 0;">载入中...</div></td>
              </tr>
            </table>
          </div>
          <div id="SavedTableFrame" class="TableFrame Saved YH"></div>
          <div class="BR" style="height:<?=R("mobile")?"20":"15"?>px;"></div>
          <div class="SavePN"> <a class="SavePNItem SavePNPrev" href="javascript:void(0);" onclick="SavedPagePrev();BirdPost(this,'.SavedListPost','clist','#SavedTableFrame');"></a> <a class="SavePNItem SavePNNext" href="javascript:void(0);" onclick="SavedPageNext();BirdPost(this,'.SavedListPost','clist','#SavedTableFrame');"></a> <a class="SavePNItem SavePNHome" href="/"></a>
            <div class="SavePNL YH SavedListPost" name="pn"></div>
            <div class="C"></div>
            <script type="text/javascript">
          function SavedPageWait(){$("#SavedTableFrame").html($("#SavedListWaiting").html());}
          function SavedPagePrev(){$(".SavedListPost").html(parseInt($(".SavedListPost").text())-1);SavedPageWait();}
          function SavedPageNext(){$(".SavedListPost").html(parseInt($(".SavedListPost").text())+1);SavedPageWait();}
          </script> 
          </div>
        </div>
      </div>
      <div class="Page Page6 active" section="InputX">
        <?
 
		
            $XiangmuItems=array(
                array("总建筑面积","㎡","总建筑面积"),
                array("占地面积","㎡","占地面积"),
				array("写字楼","㎡","写字楼"),
                array("商业","㎡","商业"),
				array("绿化(乔灌木草皮)面积","㎡","绿化"),
				array("外围地面面积","㎡","外围地面面积"),
                array("地下室","㎡","地下室"),
            
            );
		 $RichangItems1=array(
			    array("大<br>堂"=>array( array("数量","个","大堂数量"),
				                    array("总面积","㎡","大堂总面积"),
				)),
			   array("洗<br>手<br>间"=>array( array("数量","个","洗手间数量"),
				                    array("蹲位","个","洗手间蹲位"),
									 array("小便斗","个","洗手间小便斗"),
				)),
			    array("是否配置洗手间耗品","是/否","是否配置洗手间耗品"),
                array("消防楼道","个","消防楼道"),
                array("电梯数量","部","电梯数量"),
         
				);
  	

			 
			 
            $RichangItems2=array(
               
                array("茶水间","个","茶水间"),
                array("大垃圾桶","个","大垃圾桶"),
                array("烟灰盅","个","烟灰盅"),
                array("休闲椅","个","休闲椅"),
                array("雕塑或艺术品数量","个","雕塑或艺术品数量"),
                array("外围配置的垃圾桶","个","外围配置的垃圾桶"),
                array("车场岗亭及道闸","套","车场岗亭及道闸"),
                array("景观水池","","景观水池"),
                array("垃圾中转站或垃圾房数量","个","垃圾中转站或垃圾房数量"),
				array("室内公共面积","㎡","室内公共面积"),
				 array("立<br>体<br>车<br>库"=>array( array("楼数","栋","车库楼数"),
				 array("总层楼","层","车库总层楼"),
				  array("总面积","㎡","车库总面积"),
				)),
            );
			
			
			$Qita=array(
                array("实际办公总人数","","实际办公总人数"),
                array("","",""),
			 
            
            );
			
			 
      ?>
        <div class="JingsuanFrame BO RA">
          <div class="Caption YH">基础数据一览表</div>
          <div class="BO">
            <div class="BO RA"<? if(!R("mobile")){ ?> style="float:left;width:50%;margin-right:-3px;"<? } ?>>
              <div class="TableFrame YH">
                <table class="Table">
                  <tr class="FirstRow">
                    <td colspan="3" class="Label" style="width:50%;">项目名称</td>
                    <td class="Input"><input class="YH FieldInput JingsuanPost" type="text" name="<?=md5("项目名称")?>" value="<?=H(V($SAVED[md5("项目名称")]))?>"></td>
                  </tr>
                  <tr>
                    <td colspan="3" class="Title" style="">地下室层数<span class="IB">（层）</span></td>
                    <td class="Input"><input class="YH FieldInput JingsuanPost" type="text" name="<?=md5("地下室层数")?>" value="<?=H(V($SAVED[md5("地下室层数")]))?>"></td>
                  </tr>
                  <tr>
                    <td colspan="3" class="Title" style="">管理费用标准<span class="IB">（元/㎡/月）</span></td>
                    <td class="Input" style="height:40px;"><input class="YH FieldInput CusuanPost JingsuanPost JingsuanPrePost" type="text" name="<?=md5("管理费用标准")?>" value="<?=H(V($SAVED[md5("管理费用标准")]))?>" onchange="BirdPost(this,'.JingsuanPrePost','cjingsuanpre');"></td>
                  </tr>
                  <? $i=0;foreach($XiangmuItems as $item){ ?>
                  <tr>
                    <? if(!$i){ ?>
                    <td rowspan="<?=count($XiangmuItems)?>" class="Title" style="width:10%;">项<br>
                      目<br>
                      面<br>
                      积</td>
                    <? } ?>
                    <td class="Title" colspan="2"  style=""><?=$item[0]?>
                      <? if($item[1]){ ?>
                      <span class="IB">（
                      <?=$item[1]?>
                      ）</span>
                      <? } ?></td>
                    <td class="Input"><input class="YH FieldInput CusuanPost JingsuanPost<?=$item[2]=="管理费用标准"?" JingsuanPrePost":""?>" type="text" name="<?=md5($item[2])?>" value="<?=H(V($SAVED[md5($item[2])]))?>" <? if($item[2]=="管理费用标准"){ ?> onchange="BirdPost(this,'.JingsuanPrePost','cjingsuanpre');"<? } ?>></td>
                  </tr>
                  <? $i++;} ?>
                  <? $i=0;foreach($RichangItems1 as $k=> $item){   ?>
                  <tr>
                    <? if(!$i){ ?>
                    <td rowspan="8" class="Title" style="width:10%;">日<br>
                      常<br>
                      清<br>
                      洁</td>
                    <? } ?>
                    <?php if(count($RichangItems1[$k])==1){?>
                    <?php $y=0; foreach($RichangItems1[$k] as $k=> $val){    ?>
                    <? if(!$y){ ?>
                    <td rowspan="<?=count($val)?>" class="Title" style="width:15%;" ><?=$k?></td>
                    <?php }?>
                    <?php $s=0; foreach($val as  $v){  ?>
                    <? if($s){ ?>
                  <tr>
                    <?php }?>
                    <td><?=$v[0]?>
                      <? if($v[1]){ ?>
                      <span class="IB">（
                      <?=$v[1]?>
                      ）</span>
                      <? } ?></td>
                    <td class="Input"><input class="YH FieldInput CusuanPost JingsuanPost" type="text" name="<?=md5($v[2])?>" value="<?=H(V($SAVED[md5($v[2])]))?>" ></td>
                    <? if($s){ ?>
                  </tr>
                  <?php }?>
                  <?php $s++; } ?>
                  <?php $y++; }?>
                  <?php }else{ ?>
                  
                    <td class="Title" colspan="2"  style="" ><?=$item[0]?>
                      <? if($item[1]){ ?>
                      <span class="IB">（
                      <?=$item[1]?>
                      ）</span>
                      <? } ?></td>
                    <td class="Input"><input class="YH FieldInput CusuanPost JingsuanPost" type="text" name="<?=md5($item[2])?>" value="<?=H(V($SAVED[md5($item[2])]))?>" ></td>
                    <? } ?>
                  </tr>
                  <? $i++;} ?>
                </table>
              </div>
            </div>
            <div class="BO RA"<? if(!R("mobile")){ ?> style="float:right;width:50%;"<? } ?>>
              <div class="TableFrame YH">
                <table class="Table">
                  <tr class="FirstRow">
                    <td colspan="3" class="Label" style="width:50%;">楼栋数量<span class="IB">（栋）</span></td>
                    <td class="Input"><input class="YH FieldInput JingsuanPost" type="text" name="<?=md5("楼栋数量")?>" value="<?=H(V($SAVED[md5("楼栋数量")]))?>"></td>
                  </tr>
                  <tr>
                    <td colspan="3" class="Title" style="">各楼栋层数<span class="IB"> (层)</span></td>
                    <td class="Input"><input class="YH FieldInput JingsuanPost" type="text" name="<?=md5("楼栋层数")?>" value="<?=H(V($SAVED[md5("楼栋层数")]))?>"></td>
                  </tr>
                  <tr>
                    <td colspan="3" class="Title" style="">预计清洁服务等级</td>
                    <? if(preg_match("/(msie)/i",R("UA"))){ ?>
                    <td class="Input" style="height:40px;"><div class="BR" style="height:40px;width:10px;"></div>
                      <div class="A BO" style="left:0;top:0;bottom:0;right:0;">
                        <select id="JingsuanFWDJ" class="YH FieldSelect CusuanPost JingsuanPost JingsuanPrePost" name="<?=md5("服务等级")?>" style="width:120%;">
                          <option value="BM">乙级以下</option>
                          <option value="B" selected>乙级</option>
                          <option value="A">甲级</option>
                          <option value="AA">超甲级</option>
                        </select>
                        <div class="A BO" style="left:0;top:0;right:0;bottom:0;background:url(/images/transparent.gif);"></div>
                      </div></td>
                    <? }else{ ?>
                    <td class="Input" style="height:40px;"><div class="A BO" style="left:0;top:0;bottom:0;right:0;">
                        <select id="JingsuanFWDJ" class="YH FieldSelect CusuanPost JingsuanPost JingsuanPrePost" name="<?=md5("服务等级")?>" style="width:120%; color:#fff;background: #df8230;">
                          <option value="BM">乙级以下</option>
                          <option value="B" selected>乙级</option>
                          <option value="A">甲级</option>
                          <option value="AA">超甲级</option>
                        </select>
                        <div class="A BO" style="left:0;top:0;right:0;bottom:0;     background: #f57301;
    z-index: -1;"></div>
                      </div></td>
                    <? } ?>
                  </tr>
                  <? $svfw=V($SAVED[md5("服务等级")]);if($svfw){ ?>
                  <script type="text/javascript">$(document).ready(function(){$("#JingsuanFWDJ").val("<?=$svfw?>");});</script>
                  <? } ?>
                  <? $i=0;foreach($RichangItems2 as $k=> $item){   ?>
                  <tr>
                    <? if(!$i){ ?>
                    <td rowspan="13" class="Title" style="width:10%;">日<br>
                      常<br>
                      清<br>
                      洁</td>
                    <? } ?>
                    <?php if(count($RichangItems2[$k])==1){?>
                    <?php $y=0; foreach($RichangItems2[$k] as $k=> $val){    ?>
                    <? if(!$y){ ?>
                    <td rowspan="<?=count($val)?>" class="Title" style="width:20%;" ><?=$k?></td>
                    <?php }?>
                    <?php $s=0; foreach($val as  $v){  ?>
                    <? if($s){ ?>
                  <tr>
                    <?php }?>
                    <td><?=$v[0]?>
                      <? if($v[1]){ ?>
                      <span class="IB">（
                      <?=$v[1]?>
                      ）</span>
                      <? } ?></td>
                    <td class="Input"><input class="YH FieldInput CusuanPost JingsuanPost" type="text" name="<?=md5($v[2])?>" value="<?=H(V($SAVED[md5($v[2])]))?>" ></td>
                    <? if($s){ ?>
                  </tr>
                  <?php }?>
                  <?php $s++; } ?>
                  <?php $y++; }?>
                  <?php }else{ ?>
                  
                    <td class="Title" colspan="2"  style="" ><?=$item[0]?>
                      <? if($item[1]){ ?>
                      <span class="IB">（
                      <?=$item[1]?>
                      ）</span>
                      <? } ?></td>
                    <td class="Input"><input class="YH FieldInput CusuanPost JingsuanPost" type="text" name="<?=md5($item[2])?>" value="<?=H(V($SAVED[md5($item[2])]))?>" ></td>
                    <? } ?>
                  </tr>
                  <? $i++;} ?>
                  <? $i=0;foreach($Qita as $item){ ?>
                  <tr>
                    <? if(!$i){ ?>
                    <td rowspan="<?=count($Qita)?>" class="Title" style="width:10%;">其<br>
                      他<br>
                      事<br>
                      项</td>
                    <? } ?>
                    <td class="Title" colspan="2"  style=""><?=$item[0]?>
                      <? if($item[1]){ ?>
                      <span class="IB">（
                      <?=$item[1]?>
                      ）</span>
                      <? } ?></td>
                    <td class="Input"><input class="YH FieldInput CusuanPost JingsuanPost" type="text" name="<?=md5($item[2])?>" value="<?=H(V($SAVED[md5($item[2])]))?>" ></td>
                  </tr>
                  <? $i++;} ?>
                </table>
              </div>
            </div>
            <div class="C"></div>
          </div>
          <a href="javascript:void(0);" id="JingsuanBtn" class="BO YH BigBtn Orange" style="<?=R("mobile")?"":"width:260px;"?>" onclick="BirdPost(this,'.JingsuanPost','cjingsuan',false,'正在提交…');">计算</a> </div>
      </div>
      <div class="Page Page7" section="OutputX">
        <div class="JingsuanResultFrame BO RA JingsuanResult" name="HTML">
          <div class="Caption YH">精算结果</div>
          <div class="ResultTabs YH">  <a class="ResultTab<?=R("mobile")?" R":""?> active" href="javascript:void(0);">
            <?=R("mobile")?"按实际保洁面积配置":"按实际保洁面积配置"?>
            </a> <a class="ResultTab<?=R("mobile")?" L":""?>" href="javascript:void(0);">
            <?=R("mobile")?"常用清洁物料进场配置":"常用清洁物料进场配置"?>
            </a> <a class="ResultTab<?=R("mobile")?" R":""?>" href="javascript:void(0);">
            <?=R("mobile")?"洗手间特定月消耗测算":"洗手间特定月消耗测算"?>
            </a>
            <? if(!R("mobile")){ ?>
            <div class="R BO ResultTabUnit" style="height: 35px;line-height: 34px;color:#f08324;font-size:14px;">单位：人数</div>
            <? } ?>
            <div class="C"></div>
          </div>
          <script type="text/javascript">$(document).ready(function(){
            $(".JingsuanResultFrame .ResultTabs .ResultTab").click(function(){
                if($(this).hasClass("active"))return false;
                $(".JingsuanResultFrame .ResultTabs .ResultTab.active").removeClass("active");
                $(this).addClass("active");
                $(".JingsuanResultFrame .ResultFrames .TableFrame.active").removeClass("active");
                <? if(!R("mobile")){ ?>$(".ResultTabUnit").toggle($(this).index(".ResultTab")<2);<? } ?>
                $($(".JingsuanResultFrame .ResultFrames .TableFrame").get($(this).index(".ResultTab"))).addClass("active");
            });
        });</script>
          <div class="ResultFrames">
            
            <div class="TableFrame YH active">
              <table class="Table">
                <tr class="FirstRow">
                  <td class="Label" style="width:10%;height:40px;">序号</td>
                  <td class="Label LevelBMX" style="width:26%;height:40px;">岗位</td>
                  <td class="Label LevelBM LevelBX" style="width:16%;height:40px;">乙级以下</td>
                  <td class="Label LevelB LevelAX" style="width:16%;height:40px;">乙级</td>
                  <td class="Label LevelA LevelAAX" style="width:16%;height:40px;">甲级</td>
                  <td class="Label LevelAA" style="width:16%;height:40px;">超甲级</td>
                </tr>
                <? $i=0;foreach(explode("|","洗手间|室内公共区域（含大堂）|外围地面/车行道|车库(包括垃圾房、停车场）|主管|领班|机动及轮休") as $item) {$i++; ?>
                                  
                <?
				
                $itemname=explode("/",$item);
                $itemname=array_shift($itemname); 
                $itemname=explode("(",$itemname);
                $itemname=array_shift($itemname); 
				     if($itemname=='室内公共区域（含大堂）'){
					 $itemname ='写字楼';
					 }    
					  if($itemname=='车库'){
					 $itemname ='地下车库';
					 } 
					 
                ?>
                <tr>
                  <td class="Value" style="height:40px;"><?=$i?></td>
                  <td class="Value LevelBMX" style="height:40px;"><?=$item?></td>
                  <td class="Value LevelBM LevelBX" style=""><div class="BO JingsuanPost" name="<?=md5("BM".$itemname)?>">0</div></td>
                  <td class="Value LevelB LevelAX" style=""><div class="BO JingsuanPost" name="<?=md5("B".$itemname)?>">0</div></td>
                  <td class="Value LevelA LevelAAX" style=""><div class="BO JingsuanPost" name="<?=md5("A".$itemname)?>">0</div></td>
                  <td class="Value LevelAA" style=""><div class="BO JingsuanPost" name="<?=md5("AA".$itemname)?>">0</div></td>
                </tr>
                <? } ?>
                <tr>
                  <td class="Value LevelBMX" colspan="2" style="height:40px;">合计</td>
                  <td class="Value LevelBM LevelBMY LevelBX" style=""><div class="BO JingsuanPost" name="<?=md5("BM合计")?>">0</div></td>
                  <td class="Value LevelB LevelBY LevelAX" style=""><div class="BO JingsuanPost" name="<?=md5("B合计")?>">0</div></td>
                  <td class="Value LevelA LevelAY LevelAAX" style=""><div class="BO JingsuanPost" name="<?=md5("A合计")?>">0</div></td>
                  <td class="Value LevelAA LevelAAY" style=""><div class="BO JingsuanPost" name="<?=md5("AA合计")?>">0</div></td>
                </tr>
              </table>
            </div>
            <div class="TableFrame YH">
              <table class="Table">
                <tr class="FirstRow">
                  <td class="Label" style="width:10%;height:40px;">序号</td>
                  <td class="Label" style="width:30%;height:40px;">名称</td>
                  <td class="Label" style="width:10%;height:40px;">单位</td>
                  <td class="Label" style="width:30%;height:40px;">配置原则</td>
                  <td class="Label" style="width:20%;height:40px;">数量</td>
                </tr>
                <? $CommonCleanItems=array(
                array("保洁工具车","部","每个清洁岗位配置一部"),
                array("地拖","个","每个清洁岗位配置一个"),
                array("尘推","套","每个清洁岗位配置一套（洗手间岗位除外）"),
                array("厕刷","个","每个洗手间配置一个"),
                array("云石刀","把","专项或特殊岗位配置一把"),
                array("清洁喷壶","个","每个清洁岗位配置一个"),
                array("涂水器","套","专项或特殊岗位配置一套"),
                array("玻璃刮刀","把","专项或特殊岗位配置一把"),
                array("伸缩杆","个","专项或特殊岗位配置一个"),
                array("竹扫把","把","外围岗位配置一把"),
                array("胶扫把","个","每个岗位配置一个"),
                array("灰斗","个","每个岗位配置一个"),
                array("毛巾","条","每个岗位配置一条"),
                array("百洁布","条","特殊岗位配置一条"),
                array("软毛扫把","个","特殊岗位配置一个"),
                array("垃圾铲","个","特殊岗位配置一个"),
                array("消毒液","支","特殊岗位配置一支"),
                array("大垃圾袋","包","每个岗位配置一包"),
                array("小垃圾袋","包","每个岗位配置一包（外围岗位除外）"),
                array("通用清洁剂","瓶","每个岗位配置一瓶"),
                array("洁厕剂","支","每个洗手间配置一支"),
                array("玻璃清洁剂","支","专项或特殊岗位配置一支"),
                array("不锈钢光亮剂","瓶","专项或特殊岗位配置一瓶"),
                array("垃圾车","部","外围或地下室或特殊岗位配置一部"),
                array("升降机","台","由现场实际情况确定"),
                array("吸水机","台","专项或特殊岗位配置一台"),
                array("吸尘器","台","特殊岗位配置一台（包括小型）"),
                array("吹干机","台","特殊岗位配置一台"),
                array("洗地机","台","石材养护或特殊岗位配置一台"),
                array("高压水枪","部","外围岗位配置一部"),
                array("多用途清洁车","部","由现场实际情况确定"),
                array("全自动洗地机","部","大堂岗位配置一部"),
                array("水管","卷","外围或特殊岗位配置一卷"),
                array("晶面粉剂","瓶","石材养护岗位配置"),
                array("地毯清洗机","部","根据地毯面积大小来确定"),
                array("自动扶梯清洗机","部","根据扶梯数量来确定"),
            ); ?>
                <? $i=0;foreach($CommonCleanItems as $item) {$i++; ?>
                <tr>
                  <td class="Value" style="height:40px;"><?=$i?></td>
                  <td class="Value" style="height:40px;"><?=$item[0]?></td>
                  <td class="Value" style="height:40px;"><?=$item[1]?></td>
                  <td class="Value" style="height:40px;"><?=$item[2]?></td>
                  <td class="Value" style="height:40px;"><div class="BO JingsuanPost" name="<?=md5("CC_".$item[0])?>">0</div></td>
                </tr>
                <? } ?>
              </table>
            </div>
            <div class="TableFrame YH">
              <table class="Table">
                <tr class="FirstRow">
                  <td class="Label" style="width:10%;height:40px;">序号</td>
                  <td class="Label" style="width:30%;height:40px;">名称</td>
                  <td class="Label" style="width:10%;height:40px;">单位</td>
                  <td class="Label" style="width:30%;height:40px;">配置原则</td>
                  <td class="Label" style="width:20%;height:40px;">数量</td>
                </tr>
                <? $WashRoomItems=array(
                array("自动喷香剂","支","每个洗手间配置一支"),
                array("洗手液","ml","每人每月配置18.9ml"),
                array("擦手纸","包","每个人每月按0.63包配置，上下浮动为10%，规格为一箱20包"),
                array("大卷纸","卷","每个人每月按0.37卷配置，上下浮动为10%，规格为一箱12卷"),
            ); ?>
                <? $i=0;foreach($WashRoomItems as $item) {$i++; ?>
                <tr>
                  <td class="Value" style="height:40px;"><?=$i?></td>
                  <td class="Value" style="height:40px;"><?=$item[0]?></td>
                  <td class="Value" style="height:40px;"><?=$item[1]?></td>
                  <td class="Value" style="height:40px;"><?=$item[2]?></td>
                  <td class="Value" style="height:40px;"><div class="BO JingsuanPost" name="<?=md5("WR_".$item[0])?>">0</div></td>
                </tr>
                <? } ?>
              </table>
            </div>
          </div>
        </div>
        <div class="BO ResultSaveBar" style="text-align:center;"> <a href="javascript:void(0);" class="<?=R("mobile")?"BO":"IB"?> YH BigBtn Orange" style="<?=R("mobile")?"":"width:260px;"?>" onclick="BirdPost(this,'.JingsuanPost,.JingsuanResult','cjingsuansave',false,'正在保存…');">保存数据</a> </div>
      </div>
    </div>
  </div>
  <div class="BR" style="height:<?=R("mobile")?"20":"50"?>px"></div>
  <?php REQUIRE("global/footer.php"); ?>