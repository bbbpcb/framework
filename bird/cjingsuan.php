<?php
function HandlePost(){
  $项目名称=I(md5("项目名称"));
  if(!strlen($项目名称)){
    return array("项目名称","请输入项目名称");
  }
  
  
  $物业管理费=intval(I(md5("管理费用标准"),0));
  
  
  if($物业管理费==0){
	  return array("管理费用标准","请输入物业管理标准费用");
	  }
  if($物业管理费>10){
	  return array("管理费用标准","物业管理标准费用不用超出10元");
	  }	  
  
  $总建筑面积=intval(I(md5("总建筑面积"),0));
  
   
  
  if($总建筑面积<40000){
	   return array("总建筑面积","总建筑面积应大于40000㎡");
	   }
  
  
  $地下室层数=I(md5("地下室层数"),0);
  $物业管理费=I(md5("管理费用标准"),0);
  $管理费用标准=$物业管理费;
 

  $楼栋数量=I(md5("楼栋数量"),0);
  $楼栋层数=I(md5("楼栋层数"),0);  
  $服务等级=I(md5("服务等级"),"B");

  $总建筑面积=I(md5("总建筑面积"));
 
  $占地面积=I(md5("占地面积"),0);
  $写字楼=I(md5("写字楼"),0);
  $商业=I(md5("商业"),0);
  $绿化=I(md5("绿化"),0);
  $外围地面面积=I(md5("外围地面面积"),0);
  $地下室=I(md5("地下室"),0);
  $大堂数量=I(md5("大堂数量"),0);
  $大堂总面积=I(md5("大堂总面积"),0);
  $洗手间数量=I(md5("洗手间数量"),0);
  $洗手间蹲位=I(md5("洗手间蹲位"),0);
  $洗手间小便斗=I(md5("洗手间小便斗"),0);
  $是否配置洗手间耗品=I(md5("是否配置洗手间耗品"),0);
  $消防楼道=I(md5("消防楼道"),0);
  $电梯数量=I(md5("电梯数量"),0);
   
  $茶水间=I(md5("茶水间"),0);
  $大垃圾桶=I(md5("大垃圾桶"),0);
  $烟灰盅=I(md5("烟灰盅"),0);
  $休闲椅=I(md5("休闲椅"),0);
  $雕塑或艺术品数量=I(md5("雕塑或艺术品数量"),0);
  $外围配置的垃圾桶=I(md5("外围配置的垃圾桶"),0);
  $车场岗亭及道闸=I(md5("车场岗亭及道闸"),0);
  $景观水池=I(md5("景观水池"),0);
  $垃圾中转站或垃圾房数量=I(md5("垃圾中转站或垃圾房数量"),0);
  $室内公共面积=I(md5("室内公共面积"),0);
  $车库楼数=I(md5("车库楼数"),0);
  $车库总层楼=I(md5("车库总层楼"),0);
  $车库总面积=I(md5("车库总面积"),0);
  $实际办公总人数=I(md5("实际办公总人数"),0);
   
  $服务等级=W(
      $总建筑面积>= 40000?W(
      $物业管理费<=2?"BM":"",
      $物业管理费<=5?"B":"",
      $物业管理费<=8?"A":"",
      "AA"
    ):0,
	  W(
      //$物业管理费<=10?"B":"",
      $物业管理费<=10?"A":"",
      "AA"
    )
  );

  if(!is_numeric($管理费用标准)){
    return array("管理费用标准","请正确输入物业管理费");
  }
  if(!is_numeric($总建筑面积)){
    return array("总建筑面积","请正确输入总建筑面积");
  }
 
  foreach (explode("|","写字楼|地下室层数|楼栋数量|楼栋层数|室内公共面积|外围地面面积|地下室|绿化|大堂面积|洗手间数量|实际办公总人数|大堂数量|车库总层楼|车库总面积|车场岗亭及道闸") as $key) {
    if(!is_numeric(I(md5($key),0))){
      return array($key,"请正确输入".$key);
    }
  }
  
  foreach (explode("|","大堂数量|外围配置的垃圾桶|车库楼数|洗手间数量|大垃圾桶|外围垃圾桶|洗手间蹲位|茶水间|吸烟室|烟灰盅|车场岗亭及道闸|雕塑或艺术品数量|垃圾中转站或垃圾房数量|洗手间小便斗") as $key) {
    if(!is_numeric(I(md5($key),0))){
      return array($key,"请正确输入".$key."数量");
    }
  }
 
  Param(md5("BM洗手间"),round($洗手间数量/8));
  Param(md5("B洗手间"),round($洗手间数量/6));
  Param(md5("A洗手间"),round($洗手间数量/4));
  Param(md5("AA洗手间"),round($洗手间数量/3));
  
  Param(md5("BM写字楼"),round(($写字楼)/6000));
  Param(md5("B写字楼"),round(($写字楼)/3500));
  Param(md5("A写字楼"),round(($写字楼)/2500));
  Param(md5("AA写字楼"),round(($写字楼)/2000));
  
  Param(md5("BM外围地面"),round($外围地面面积/10000));
  Param(md5("B外围地面"),round($外围地面面积/8000));
  Param(md5("A外围地面"),round($外围地面面积/6000));
  Param(md5("AA外围地面"),round($外围地面面积/5000));

  Param(md5("BM地下车库"),round(($车库总面积+$地下室)/10000));
  Param(md5("B地下车库"),round(($车库总面积+$地下室)/8000));
  Param(md5("A地下车库"),round(($车库总面积+$地下室)/7000));
  Param(md5("AA地下车库"),round(($车库总面积+$地下室)/6000));
    
 //洗手间
 //室内公共区域（含大堂）
 //外围地面
// 地下车库

  Param(md5("BM主管"),1);
  Param(md5("B主管"),1);
  Param(md5("A主管"),1);
  Param(md5("AA主管"),1);
  
  Param(md5("BM领班"),1);
  Param(md5("B领班"),1);
  Param(md5("A领班"),$总建筑面积<=150000?1:2);
  Param(md5("AA领班"),$总建筑面积<=150000?1:2);
  $renbm= round($洗手间数量/8)+round(($写字楼)/6000)+round($外围地面面积/10000)+round(($车库总面积+$地下室)/10000)+2;
  $renb= round($洗手间数量/6)+round(($写字楼)/3500)+round($外围地面面积/8000)+round(($车库总面积+$地下室)/8000)+2;
  $rena= round($洗手间数量/4)+round(($写字楼)/2500)+round($外围地面面积/6000)+round(($车库总面积+$地下室)/7000)+1+($总建筑面积<=150000?1:2);
  $renaa= round($洗手间数量/3)+round(($写字楼)/2000)+round($外围地面面积/5000)+round(($车库总面积+$地下室)/6000)+1+($总建筑面积<=150000?1:2);
  
  Param(md5("BM机动及轮休"),round(($renbm)/6));
  Param(md5("B机动及轮休"),round(($renb)/6));
  Param(md5("A机动及轮休"),round(($rena)/6));
  Param(md5("AA机动及轮休"),round(($renaa)/6));
  

  GLOBAL $_FEEDBACK;
  
  $合计=array("BM"=>0,"B"=>0,"A"=>0,"AA"=>0);
  foreach(explode("|","洗手间|写字楼|外围地面|地下车库|主管|领班|机动及轮休") as $item) {
    $itemname=explode("/",$item);
    $itemname=array_shift($itemname); 
    $itemname=explode("(",$itemname);
    $itemname=array_shift($itemname);
    $合计["BM"]+=$_FEEDBACK["params"][md5("BM".$itemname)];
    $合计["B"]+=$_FEEDBACK["params"][md5("B".$itemname)];
    $合计["A"]+=$_FEEDBACK["params"][md5("A".$itemname)];
    $合计["AA"]+=$_FEEDBACK["params"][md5("AA".$itemname)];
  }

  Param(md5("BM合计"),$合计["BM"]);
  Param(md5("B合计"),$合计["B"]);
  Param(md5("A合计"),$合计["A"]);
  Param(md5("AA合计"),$合计["AA"]);

  $G7G12Count=0;
  //基础岗位数据
  foreach(explode("|","洗手间|写字楼|外围地面|地下车库") as $item) {
    $itemname=explode("/",$item);
    $itemname=array_shift($itemname); 
    $itemname=explode("(",$itemname);
    $itemname=array_shift($itemname);
	 
    $bm+=$_FEEDBACK["params"][md5("BM".$itemname)];
    $b+=$_FEEDBACK["params"][md5("B".$itemname)];
    $a+=$_FEEDBACK["params"][md5("A".$itemname)];
    $aa+=$_FEEDBACK["params"][md5("AA".$itemname)];
	 
	  if($_FEEDBACK["params"][md5("AA".$itemname)]>0){
	
	     $G7G12Count++;
	
	}
	
  }
   $wai =0;
  if($服务等级 == 'BM'){
   $G7G12Count = $bm;
   $wai =round($外围地面面积/10000);
   $wai1=$wai+round(($车库总面积+$地下室)/10000);
  }elseif($服务等级 == 'B'){
   $G7G12Count = $b;
   $wai =round($外围地面面积/8000);
    $wai1=$wai+round(($车库总面积+$地下室)/8000);
  }elseif($服务等级 == 'A'){
   $G7G12Count = $a;
    $wai =round($外围地面面积/6000);
	 $wai1=$wai+round(($车库总面积+$地下室)/7000);
  }elseif($服务等级 == 'AA'){
   $G7G12Count = $aa;
    $wai =round($外围地面面积/5000);
	 $wai1=$wai+round(($车库总面积+$地下室)/6000);
  }
  
  $dt=0;
  switch($总建筑面积){
  
  case $s <= 100000 && $s>40000;
  $dt = 1;
  break;
  case $s <= 150000;
  $dt = 2;
  break;
  case $s <=200000;
  $dt = 3;
  break;
  case $s <=350000;
  $dt = 4;
  break;
  
  case $s <=400000;
  $dt = 5;
  break;
  case $s > 400000;
  $dt = 6;
  break;
  
  }
  
  
  
  $G14=$_FEEDBACK["params"][md5("AA机动")];

  Param(md5("CC_保洁工具车"),$G7G12Count);
  Param(md5("CC_地拖"),$G7G12Count+$dt);
  Param(md5("CC_尘推"),$G7G12Count);
  Param(md5("CC_厕刷"),$洗手间数量);
  Param(md5("CC_云石刀"),$G7G12Count+$dt);
  Param(md5("CC_清洁喷壶"),$G7G12Count+$dt);
  Param(md5("CC_涂水器"),$dt);
  Param(md5("CC_玻璃刮刀"),$dt);
  Param(md5("CC_伸缩杆"),$dt);
  
  Param(md5("CC_竹扫把"),$wai);
  
  
  
  Param(md5("CC_胶扫把"),$G7G12Count+$dt);
  Param(md5("CC_灰斗"),$G7G12Count+$dt);
  Param(md5("CC_毛巾"),$G7G12Count+$dt);
  Param(md5("CC_百洁布"),$G7G12Count+$dt);
  Param(md5("CC_软毛扫把"),$G7G12Count+$dt);
  Param(md5("CC_垃圾铲"),$G7G12Count+$dt);
  Param(md5("CC_消毒液"),$洗手间数量);
  Param(md5("CC_大垃圾袋"),$大垃圾桶+$外围配置的垃圾桶);
  Param(md5("CC_小垃圾袋"),$大堂数量+$洗手间数量+$茶水间+$烟灰盅+$休闲椅);
  Param(md5("CC_通用清洁剂"),$G7G12Count+$dt);
  Param(md5("CC_洁厕剂"),$洗手间数量);
  Param(md5("CC_玻璃清洁剂"),$dt);
  Param(md5("CC_不锈钢光亮剂"),$dt);
  Param(md5("CC_垃圾车"),$wai1);
  Param(md5("CC_升降机"),$总建筑面积<=150000?1:2);
  Param(md5("CC_吸水机"),$dt);
  Param(md5("CC_吸尘器"),$dt);
  Param(md5("CC_吹干机"),$dt);
  Param(md5("CC_洗地机"),$dt);
  Param(md5("CC_高压水枪"),$dt);
  Param(md5("CC_多用途清洁车"),W(
    $外围地面面积<=20000?1:0,
    $外围地面面积<=50000?2:0,
    3
  ));
  Param(md5("CC_全自动洗地机"),W(
    $大堂面积<5000?1:0,
    $大堂面积<10000?2:0,
    3
  ));
  Param(md5("CC_水管"),$wai1);
  Param(md5("CC_晶面粉剂"),$dt);
  Param(md5("CC_地毯清洗机"),$dt);
  Param(md5("CC_自动扶梯清洗机"),$dt);


  Param(md5("WR_自动喷香剂"),$洗手间数量);
  Param(md5("WR_洗手液"),round($实际办公总人数*18.9));
  Param(md5("WR_擦手纸"),round($实际办公总人数*0.63));
  Param(md5("WR_大卷纸"),round($实际办公总人数*0.37));
  
  script("GoPage(7);");
  script("JingsuanLevel(\"{$服务等级}\");");
  return false;
}
$result=HandlePost();
if($result){script("BirdToast(\$(\".JingsuanPost[name='".md5(V($result[0]))."']\").focus(),\"".addslashes(V($result[1]))."\");");}