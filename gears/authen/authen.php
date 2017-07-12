<?php
if(!defined("ROOT"))DIE("DENIED");
if($POST=(R("REQUEST_METHOD")=="POST")){
  $captcha=I("captcha");$authencaptcha=S("AUTHENCAPTCHA");S("AUTHENCAPTCHA",time());
  echo ((strlen($captcha))&&(strtoupper($captcha)==strtoupper($authencaptcha)))?Authen(strtolower(I("username")),I("password")):"!您输入的验证码不正确，请重新输入！";
  exit(0);
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <meta name="renderer" content="webkit">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?=$AuthenTitle?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
  <link rel="shortcut icon" href="gears/authen/authen.ico" type="image/x-icon" />
  <script type="text/javascript" src="gears/jquery/jquery-1.11.1.min.js"></script>
  <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=6b6c1a67eaa7db1ca6d6da28e590e343"></script>
  <style type="text/css">
    html,body{margin: 0;padding: 0;width: 100%;height: 100%;min-width: 880px;min-height: 500px;}
    html{background:#d6dee0 url(gears/authen/bg.gif) center center repeat-x;}
    body{background:url(gears/authen/bg.jpg) center center no-repeat;}
    #MAP{display: block;overflow: hidden;width: 100%;height: 100%;}
    #Authen{display: block;overflow: hidden;width:500px;height:500px;position:absolute;margin:-250px 0 0 -250px;left:50%;top:50%;background: url(gears/authen/authen.png) center center no-repeat;}
    #Authen #AuthenFormArea{display: block;overflow: hidden;width: 300px;margin: 0 auto;}
    #Authen #AuthenFormArea .AuthenFormLine{margin: 10px;height: 55px;}
    #Authen #AuthenFormArea .AuthenFormLine.AuthenFormLineBtns{padding-top: 10px;text-align: center;}
    #Authen #AuthenFormArea .AuthenFormLine .AuthenFormLineLabel{display:block;overflow:hidden;padding-left:3px;font-size:12px;line-height:20px;font-weight:bold;color:#666;}
    #Authen #AuthenFormArea .AuthenFormLine .AuthenFormLineInput{margin: 0;padding: 0px 10px;width:260px;font-size:12px;line-height:30px;height:30px;color:#555;border-radius:3px;border:1px solid #999;background:#f5f5f5;box-shadow:2px 2px 3px #ddd;}
    #Authen #AuthenFormArea .AuthenFormLine .AuthenFormLineInput:hover{background-color:#fff;color:#333;}
    #Authen #AuthenFormArea .AuthenFormLine #Captcha{width: 180px;background-position: right center;background-repeat: no-repeat;background-image: url(gears/authen/captcha.php?time=<?=time()?>);}
    #Authen #AuthenFormArea .AuthenFormLine #CaptchaLink{padding-left: 10px;font-size: 12px;text-decoration: none;color: #666;}
    #Authen #AuthenFormArea .AuthenFormLine #CaptchaLink:hover{color: #333;}
    #Authen #AuthenFormArea .AuthenFormLine .AuthenFormLineButton{width:280px;height:32px;font:bold 14px/30px Tahoma;color:#fff;cursor:pointer;border-radius: 5px;text-shadow:1px 1px 1px #555;box-shadow:2px 2px 3px #bbb;
      border:1px solid #858585;
      background: #bcbcbc; /* Old browsers */
      background: -moz-linear-gradient(top,  #bcbcbc 0%, #aaaaaa 44%, #979797 100%); /* FF3.6+ */
      background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#bcbcbc), color-stop(44%,#aaaaaa), color-stop(100%,#979797)); /* Chrome,Safari4+ */
      background: -webkit-linear-gradient(top,  #bcbcbc 0%,#aaaaaa 44%,#979797 100%); /* Chrome10+,Safari5.1+ */
      background: -o-linear-gradient(top,  #bcbcbc 0%,#aaaaaa 44%,#979797 100%); /* Opera 11.10+ */
      background: -ms-linear-gradient(top,  #bcbcbc 0%,#aaaaaa 44%,#979797 100%); /* IE10+ */
      background: linear-gradient(to bottom,  #bcbcbc 0%,#aaaaaa 44%,#979797 100%); /* W3C */
      filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#bcbcbc', endColorstr='#979797',GradientType=0 ); /* IE6-9 */
    }
    #Authen #AuthenFormArea .AuthenFormLine .AuthenFormLineButton:hover{
      background: #c4c4c4; /* Old browsers */
      background: -moz-linear-gradient(top,  #c4c4c4 0%, #b2b2b2 44%, #979797 100%); /* FF3.6+ */
      background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#c4c4c4), color-stop(44%,#b2b2b2), color-stop(100%,#979797)); /* Chrome,Safari4+ */
      background: -webkit-linear-gradient(top,  #c4c4c4 0%,#b2b2b2 44%,#979797 100%); /* Chrome10+,Safari5.1+ */
      background: -o-linear-gradient(top,  #c4c4c4 0%,#b2b2b2 44%,#979797 100%); /* Opera 11.10+ */
      background: -ms-linear-gradient(top,  #c4c4c4 0%,#b2b2b2 44%,#979797 100%); /* IE10+ */
      background: linear-gradient(to bottom,  #c4c4c4 0%,#b2b2b2 44%,#979797 100%); /* W3C */
      filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#c4c4c4', endColorstr='#979797',GradientType=0 ); /* IE6-9 */
    }
    #Authen #AuthenFormArea .AuthenFormLine .AuthenFormLineButton.active{
      border:1px solid #3581d5;
      background: #7abcff; /* Old browsers */
      background: -moz-linear-gradient(top,  #7abcff 0%, #60abf8 44%, #4096ee 100%); /* FF3.6+ */
      background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#7abcff), color-stop(44%,#60abf8), color-stop(100%,#4096ee)); /* Chrome,Safari4+ */
      background: -webkit-linear-gradient(top,  #7abcff 0%,#60abf8 44%,#4096ee 100%); /* Chrome10+,Safari5.1+ */
      background: -o-linear-gradient(top,  #7abcff 0%,#60abf8 44%,#4096ee 100%); /* Opera 11.10+ */
      background: -ms-linear-gradient(top,  #7abcff 0%,#60abf8 44%,#4096ee 100%); /* IE10+ */
      background: linear-gradient(to bottom,  #7abcff 0%,#60abf8 44%,#4096ee 100%); /* W3C */
      filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#7abcff', endColorstr='#4096ee',GradientType=0 ); /* IE6-9 */
    }
    #Authen #AuthenFormArea .AuthenFormLine .AuthenFormLineButton.active:hover{
      background: #8cc5ff; /* Old browsers */
      background: -moz-linear-gradient(top,  #8cc5ff 0%, #6cb2f7 44%, #4096ee 100%); /* FF3.6+ */
      background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#8cc5ff), color-stop(44%,#6cb2f7), color-stop(100%,#4096ee)); /* Chrome,Safari4+ */
      background: -webkit-linear-gradient(top,  #8cc5ff 0%,#6cb2f7 44%,#4096ee 100%); /* Chrome10+,Safari5.1+ */
      background: -o-linear-gradient(top,  #8cc5ff 0%,#6cb2f7 44%,#4096ee 100%); /* Opera 11.10+ */
      background: -ms-linear-gradient(top,  #8cc5ff 0%,#6cb2f7 44%,#4096ee 100%); /* IE10+ */
      background: linear-gradient(to bottom,  #8cc5ff 0%,#6cb2f7 44%,#4096ee 100%); /* W3C */
      filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#8cc5ff', endColorstr='#4096ee',GradientType=0 ); /* IE6-9 */
    }
    #Authen #AuthenBanner{display: block;overflow: hidden;width: 330px;height:60px;margin: 0 auto;margin-top:80px;background: url(<?=$AuthenBanner?>) center center no-repeat;}
  </style>
</head>
<body>
  <div id="MAP"></div>
  <script type="text/javascript"><?php $MAP=false; ?>(function(){<?php if($MAP){ ?>var map=new BMap.Map("MAP");map.centerAndZoom(new BMap.Point(116.331398,39.897445),7);<?php } ?>var geolocation=new BMap.Geolocation();geolocation.getCurrentPosition(function(e){if(this.getStatus()==BMAP_STATUS_SUCCESS){<?php if($MAP){ ?>map.panTo(new BMap.Point(e.point.lng,e.point.lat));<?php } ?>document.getElementById("Location").value=e.point.lng+","+e.point.lat;var coder=new BMap.Geocoder();coder.getLocation(e.point,function(rs){var v=rs.addressComponents;document.getElementById("Address").value=v.province+v.city+v.district+v.street+(v.business?v.business:v.streetNumber)})}},{enableHighAccuracy:true})})();</script>
  <div id="Authen">
    <div id="AuthenBanner"></div>
    <div id="AuthenFormArea">
      <form id="AuthenForm" method="POST" action="<?=R("REQUEST_URI")?>" onsubmit="return AuthenSubmit();">
        <div class="AuthenFormLine">
          <label class="AuthenFormLineLabel" id="UsernameLabel" for="Username">帐号：</label>
          <input class="AuthenFormLineInput" id="Username" maxlength="16" name="username" type="text" value="<?=I("username","")?>" placeholder="请输入登陆帐号">
        </div>
        <div class="AuthenFormLine">
          <label class="AuthenFormLineLabel" id="PasswordLabel" for="Password">密码：</label>
          <input class="AuthenFormLineInput" id="Password" maxlength="16" name="password" type="password" placeholder="请输入登陆密码">
        </div>
        <div class="AuthenFormLine">
          <label class="AuthenFormLineLabel" id="CaptchaLabel" for="Captcha">验证码：</label>
          <input class="AuthenFormLineInput" id="Captcha" maxlength="4" name="captcha" type="text" placeholder="验证码"><a id="CaptchaLink" href="javascript:void(0);" onclick="AuthenCaptcha();">刷新验证码</a>
        </div>
        <input id="Location" type="hidden" name="location" value="">
        <input id="Address" type="hidden" name="address" value="">
        <div class="AuthenFormLine AuthenFormLineBtns">
          <input class="AuthenFormLineButton active" id="SubmitBtn" type="submit" value="登&nbsp;陆">
        </div>
      </form>
    </div>
  </div>
  <script type="text/javascript">
    function AuthenCaptcha(){$("#Captcha").css("background-image","url(gears/authen/captcha.php?time="+(new Date()).getTime()+")").val("");}
    function AuthenSubmit(){$.post("<?=R("REQUEST_URI")?>",$("#AuthenForm").serialize(),function(data){AuthenCaptcha();if(data.substr(0,1)=="!"){alert(data.substr(1));$("#Password").val("").focus();}else{location.href=data;}});return false;}
    $(document).ready(function(){$("#Username").focus();});
  </script>
</body>
</html>