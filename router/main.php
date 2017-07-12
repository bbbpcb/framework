<?php REQUIRE("global/global.php"); ?>
<?php $Logined=S("Logined"); ?>
<?php REQUIRE("global/header.php"); ?>
<?php if(R("mobile")){ ?>
  <style type="text/css">
  #Wrapper{overflow: hidden;overflow-y:auto;}
  </style>
  <div class="RA MainFrameMobile">
    <img class="BO" src="/images/mainbg.png">
    <? if($Logined){ ?>
    <a class="A BO MainBtn" href="<?=U("~/choose/")?>">
      <img class="BO" src="/images/mainbtn.png">
    </a>
    <? }else{ ?>
    <div class="A BO MainLogin">
        <input class="A YH BO MainLoginID LoginPost" type="text" value="" placeholder="请输入用户帐号" name="username">
        <input class="A YH BO MainLoginPS LoginPost" type="password" value="" placeholder="请输入用户密码" name="password">
        <a class="A YH BO MainLoginGO" href="javascript:void(0);" onclick="BirdPost(this,'.LoginPost','login',false,'正在登录...');">登&nbsp;录</a>
    </div>
    <? } ?>
  </div>
  <div class="BR" style="height:20px;"></div>
  <script type="text/javascript">
  window.resizings.push(function(){
    var h=$("#Wrapper").outerHeight();
    var fh=$(".MainFrameMobile").height();
    $(".MainFrameMobile").css("margin-top",(h>fh?Math.floor((h-fh)*2/5):0)+"px");
  });
  </script>
<?php }else{ ?>
  <div class="A BO MainFramePC AIH" AIH="0.8">
    <? if($Logined){ ?>
    <a class="A BO MainBtn" href="<?=U("~/choose/")?>">
      <img class="BO" src="/images/mainbtn.png">
    </a>
    <? }else{ ?>
    <div class="A BO MainLogin">
        <input class="A YH BO MainLoginID LoginPost" type="text" value="" placeholder="请输入用户帐号" name="username">
        <input class="A YH BO MainLoginPS LoginPost" type="password" value="" placeholder="请输入用户密码" name="password">
        <a class="A YH BO MainLoginGO" href="javascript:void(0);" onclick="BirdPost(this,'.LoginPost','login',false,'正在登录...');">登&nbsp;录</a>
    </div>
    <? } ?>
  </div>
  <script type="text/javascript">
  window.resizings.push(function(){
    var h=$("#Wrapper").outerHeight();
    $(".MainFramePC").css("margin-top",Math.max(h>622?Math.floor((h-622)/2):0,100)+"px");
  });
  </script>
<?php } ?>
<?php REQUIRE("global/footer.php"); ?>