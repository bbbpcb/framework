<?php if(!defined("ROOT"))DIE("DENIED"); ?><!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <meta name="renderer" content="webkit">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="format-detection" content="telephone=no">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
  <title><?=H(trim(V($_TITLE)." ".cfg("main","sitename")))?></title>
  <meta name="keywords" content="<?=H(V($_KEYWORDS))?><?=H(cfg("main","keywords"))?>">
  <meta name="description" content="<?=H(V($_DESCRIPTION))?><?=H(cfg("main","description"))?>">
  <script type="text/javascript" src="/gears/jquery/jquery-1.11.1.min.js"></script>
  <? if(preg_match("/(MSIE)/i",R("UA"))){ ?><!--[if lte IE 8]><script type="text/javascript" src="/gears/jquery/jquery.corner.js"></script><![endif]--><? } ?>
  <script type="text/javascript">window.resizings=[];window.OIE=false;function CascadingStyleSheets(css){var style=document.createElement("style");style.type="text/css";if(style.styleSheet){style.styleSheet.cssText=css;}else{style.innerHTML=css;}document.getElementsByTagName("head")[0].appendChild(style);}function Resizing(){<? if(preg_match("/(MSIE)/i",R("UA"))){ ?>if(window.OIE)setTimeout("OIEBG();",500);<? } ?>$("#Wrapper").css("bottom",$("#Footer").outerHeight()+"px");$.each(window.resizings,function(i,f){f();});}$(document).ready(function(){<? if(!preg_match("/(MSIE)/i",R("UA"))){ ?>$(".AIH a").hover(function(){var aih=parseFloat($(this).parents(".AIH").attr("AIH"));$(this).finish().find("img").fadeTo("fast",Math.max(isNaN(aih)?0.6:aih,0.6));},function(){$(this).find("img").fadeTo("fast",1.0);});<? } ?><? if(preg_match("/(MSIE)/i",R("UA"))){ ?>if(window.OIE){var bg=$("#Wrapper").css("background-image");bg=bg.substr(5,bg.length-7);$("#Wrapper").addClass("OIE");$("<div id='Wrapimg'></div>").prependTo("body");$("<img id='OIEBG' class='A BO' src='"+bg+"'/>").appendTo("#Wrapimg");}<? } ?>$(window).resize(function(){Resizing();});Resizing();setTimeout(function(){Resizing();},360);});</script><? if(preg_match("/(MSIE)/i",R("UA"))){ ?><!--[if lte IE 8]><script type="text/javascript">function OIEBG(){var w=$(window).innerWidth();var h=$(window).innerHeight();var zw=w/1920;var zh=h/850;var img=$("#OIEBG");if(zw<zh){img.css({"width":"auto","height":"100%","top":"0","left":Math.round((w-1920*zh)/2)+"px"});}else{img.css({"height":"auto","width":"100%","left":"0","top":Math.round((h-850*zw)/2)+"px"});}}window.OIE=true;</script><![endif]--><? } ?>
  <script type="text/javascript" src="/gears/jquery/dialog/dialog.js?skin=aero"></script>
  <script type="text/javascript" src="/gears/jquery/dialog/iframe.js"></script>
  <script type="text/javascript" src="/gears/jquery/bird.js"></script>
  <link rel="stylesheet" href="/images/theme.css">
  <?=V($_META)?>
  <?=cfg("main","headmeta")?>
</head>
<body>
  <div id="Wrapper" class="<?=R("mobile")?"MOBILE":"PC"?> <?=ucfirst($_ROUTER[0])?>">