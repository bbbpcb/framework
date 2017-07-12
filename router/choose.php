<?php REQUIRE("global/global.php"); ?>
<? //$Logined=S("Logined");if(!$Logined)G("/"); ?>
<?php REQUIRE("global/header.php"); ?>
<?php $NAVIDX=1;REQUIRE("global/title.php"); ?>
<div class="Root">
  <div class="ChooseMode YH AIH" AIH="0.8">
  
  
  
  
  
    <a class="ChooseModeItem<?=R("mobile")?"":" L"?>" href="<?=U("~/xiezilou/")?>">
      <div class="Image"><img class="BO" src="/images/choose1.png"></div>
      <div class="Title">写字楼</div>
      <div class="Summary">主体用于办公的建筑物，裙楼可带局部底商</div>
    </a>
    
    <a class="ChooseModeItem<?=R("mobile")?"":" L"?>"<?=R("mobile")?"":" style=\"margin-left:110px;\""?>" href="<?=U("~/chanyeyuan/")?>">
      <div class="Image"><img class="BO" src="/images/choose3.png"></div>
      <div class="Title">研发产业园</div>
      <div class="Summary">研发产业园</div>
    </a>
    
    <a class="ChooseModeItem<?=R("mobile")?"":" R"?>" href="javascript:void(0);">
      <div class="Image"><img class="BO" src="/images/choose2.png"></div>
      <div class="Title">购物中心</div>
      <div class="Summary">购物中心类型模块待开发中</div>
    </a>

    <div class="C"></div>
  </div>
</div>
<div class="BR" style="height:<?=R("mobile")?"20":"50"?>px"></div>
<?php REQUIRE("global/footer.php"); ?>