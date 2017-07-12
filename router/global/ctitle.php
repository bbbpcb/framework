<? $NAVIDX=min(intval(V($NAVIDX)),5); ?>
<? if(R("mobile")){ ?>
  <div class="BR" style="height:15px;"></div>
  <div class="Root">
    <div class="NavTitle">
      <div class="NavTitleBtns YH RA">
        <a class="L BO Btn Orange" href="javascript:void(0);" onclick="GoBack();">返回</a>
        <a class="R BO Btn" id="CesuanTishiBtn" href="javascript:void(0);" onclick="GoHint();">提示</a>
        <div class="A NavTitleLogo"><a href="<?=U("~/main/")?>"><img src="/images/navtitle.png"></a></div>
        <div class="C"></div>
      </div>
    </div>
  </div>
  <div class="BR" style="height:15px;"></div>
  <div class="Root">
    <div class="NavDots BO RA YH">
      <a class="NavDot NavDot1<?=$NAVIDX>=1?" active":""?>" href="<?=U("~/choose/")?>">选择物<br>业类型</a>
 
      <a class="NavDot NavDotMargin NavDot2<?=$NAVIDX>=2?" active":""?>" href="javascript:void(0);" onclick="GoPageInput();">输入<br>数据</a>
      <a class="NavDot NavDotMargin NavDot3<?=$NAVIDX>=3?" active":""?>" href="javascript:void(0);" onclick="GoPageResult();">测算<br>结果</a>
      <a class="NavDot NavDot5<?=$NAVIDX>=4?" active":""?>" href="javascript:void(0);" onclick="GoPageSaved();">保存<br>列表</a>
      <div class="C"></div>
      <div class="NavSep NavSep2<?=$NAVIDX>=2?" active":""?>" style="left:60px;"></div>
      <div class="NavSep NavSep3<?=$NAVIDX>=3?" active":""?>"></div>
      <div class="NavSep NavSep4<?=$NAVIDX>=4?" active":""?>" style="right:61px;"></div>
 
    </div>
  </div>
  <script type="text/javascript">
  window.resizings.push(function(){
    var w=$(".NavDots").innerWidth();
    $(".NavDots .NavDotMargin").css("margin-left",(w>300?Math.floor((w-300)/1.9):0)+"px");
    $(".NavDots .NavSep").toggle(w>300);
    if(w>300){
      var n=(w-300)/2;
      $(".NavDots .NavSep").css("width",n+"px");
      $(".NavDots .NavSep3").css("left",(120+n)+"px");
     // $(".NavDots .NavSep4").css("right",(120+n)+"px");
    }
  });
  </script>
  <div class="BR" style="height:15px;"></div>
<? }else{ ?>
  <div class="BR" style="height:100px;"></div>
  <div class="Root">
    <div class="NavTitle">
      <div class="L BO NavTitleLogo"><a href="<?=U("~/main/")?>"><img class="BO" src="/images/navtitle.png"></a></div>
      <div class="R BO NavTitleBtns YH">
        <div class="BR" style="height:12px;"></div>
        <a class="L BO Btn Orange" href="javascript:void(0);" onclick="GoBack();">返回上一页</a>
        <a class="L BO Btn" id="CesuanTishiBtn" href="javascript:void(0);" onclick="GoHint();">测算提示</a>
        <div class="C"></div>
      </div>
      <div class="C"></div>
    </div>
  </div>
  <? if(preg_match("/(MSIE)/i",R("UA"))){ ?>
  <!--[if lte IE 8]><script type="text/javascript">$(document).ready(function(){$(".NavTitle .NavTitleBtns .Btn").corner("3px");});</script><![endif]-->
  <? } ?>
  <div class="BR" style="height:60px;"></div>
  <div class="Root">
    <div class="NavDots BO RA YH">
      <a class="NavDot NavDot1<?=$NAVIDX>=1?" active":""?>" href="<?=U("~/choose/")?>">选择物<br>业类型</a>
       <a class="NavDot NavDot2<?=$NAVIDX>=2?" active":""?>" href="javascript:void(0);" onclick="GoPageInput();">输入<br>数据</a>
      <a class="NavDot NavDot3<?=$NAVIDX>=3?" active":""?>" href="javascript:void(0);" onclick="GoPageResult();">测算<br>结果</a>
      <a class="NavDot NavDot4<?=$NAVIDX>=4?" active":""?>" href="javascript:void(0);" onclick="GoPageSaved();">保存<br>列表</a>
      <div class="NavSep NavSep2<?=$NAVIDX>=2?" active":""?>"></div>
      <div class="NavSep NavSep3<?=$NAVIDX>=3?" active":""?>"></div>
      <div class="NavSep NavSep4<?=$NAVIDX>=4?" active":""?>"></div>
 
    </div>
  </div>
  <div class="BR" style="height:60px;"></div>
<? } ?>
<script type="text/javascript">
window.NAVIDX=<?=$NAVIDX?>;
window.JingsuanMode=false;
function GoPageInput(){
  
    GoPage(6);
  
}
function GoPageResult(){
	 
    $("#JingsuanBtn").click();
     GoPage(7);
   
   
}
function GoPageSaved(){
   
    GoPage(8);
  
}
function GoBack(){GoPage(window.NAVIDX==6?2:window.NAVIDX-1);}
function GoHint(){BirdFrame("#CesuanTishiBtn","<?=U("~/chint/")?>",{},{},false,"测算提示","<?=R("mobile")?"300":"500"?>px","<?=R("mobile")?"66%":"600px"?>");}
function GoPage(page){
  var section="<?=$_ROUTER[0]?>";
  window.NAVIDX=page;
   
  if(page<1){location.href="<?=U("~/main/")?>";return false;}
  if(page==1){location.href="<?=U("~/choose/")?>";}
  if(section=="choose"){alert("请先选择物业类型！");return false;}
  if(page==3)window.JingsuanMode=false;
  if(page==6)window.JingsuanMode=true;
  if(window.NAVIDX==8)page=page-3;
 
  
  $(".Pages .Page.active").removeClass("active");
  $(".Pages .Page.Page"+page).addClass("active");
  $("#Wrapper").removeClass("Input Output Save InputX OutputX");
  $("#Wrapper").addClass($(".Pages .Page.Page"+page).attr("section"));
  <? if(preg_match("/(MSIE)/i",R("UA"))){ ?>
  if(window.OIE){
    if(page>1){
      if(page==2){$("#OIEBG").attr("src","/images/wallpapers/2.jpg");}
      else if(page==3){$("#OIEBG").attr("src","/images/wallpapers/3.jpg");}
      else if(page==4){$("#OIEBG").attr("src","/images/wallpapers/4.jpg");}
      else if(page==5){$("#OIEBG").attr("src","/images/wallpapers/7.jpg");}
      else if(page==6){$("#OIEBG").attr("src","/images/wallpapers/5.jpg");}
      else if(page==7){$("#OIEBG").attr("src","/images/wallpapers/6.jpg");}
    }
  }
  <? } ?>
  if(page>5)page=page-4;
  $(".NavDots .NavDot.active").removeClass("active");
  $(".NavDots .NavSep.active").removeClass("active");
  //$(".NavDots .NavDot.NavDot"+page).addClass("active");
  for(var i=page;i>=1;i--){
      $(".NavDots .NavDot"+i).addClass("active");
      if(i>=2)$(".NavDots .NavSep"+i).addClass("active");
  };
  if(page==5){SavedPageWait();BirdPost(this,'.SavedListPost','clist','#SavedTableFrame');}
}
</script>