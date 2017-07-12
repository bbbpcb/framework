<?php
/* ----------------------------------------------------
 * Name: MyProber（探测PHP服务器运行环境）
 * Version: 0.26
 * Auther: 郭纪凯
 * QQ: 283489 Weibo: http://weibo.com/guojikai
 * Date: 2011-06-27
 * ---------------------------------------------------- */
header("Expires: 0");
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
 if(!isset($_SESSION))session_start();
 if(!isset($_SESSION["SystemAuthen"]))exit(0);
 if(!$_SESSION["SystemAuthen"])exit(0);
//定义
$title = 'MyProber';
$version = "0.26"; //版本号

error_reporting(7); //抑制所有错误信息
@header("content-Type: text/html; charset=utf-8"); //输出编码
date_default_timezone_set('PRC');
ob_start();

//Yes & No
define('YES', '<span class="yes">√</span>');
define('NO', '<span class="no">×</span>');

//GET方法
if($_GET['act'] == "phpinfo") { //网页为 phpinfo()
  phpinfo();
  exit;
}if($_GET['act'] == "enable_functions") { //网页为 激活的函数
  echo "<pre>";
  echo "这里显示系统所支持的所有函数（包括自定义函数）\n";
  print_r( get_defined_functions() );
  echo "</pre>";
  exit;
} elseif($_GET['act'] == "disable_functions") { //网页为 禁用的函数
  $dis_func = explode(',', get_cfg_var("disable_functions"));
  echo "<pre>";
  echo "这里显示系统被禁用的函数\n";
  print_r( $dis_func );
  echo "</pre>";
  exit;
} elseif($_GET['act'] == "server_test") { //网页为 服务器性能测试
  if($_GET['i'] == 0) { //整形
    $timeStart = gettimeofday();
    for($i = 0; $i < 3000000; $i++) {
      $t = 1+1;
    }
    $timeEnd = gettimeofday();
    $time = ($timeEnd["usec"]-$timeStart["usec"])/1000000+$timeEnd["sec"]-$timeStart["sec"];
    echo round($time, 3)."秒";
  } elseif($_GET['i'] == 1) { //浮点
    $t = pi();
    $timeStart = gettimeofday();
    for($i = 0; $i < 3000000; $i++) {
      sqrt($t);
    }
    $timeEnd = gettimeofday();
    $time = ($timeEnd["usec"]-$timeStart["usec"])/1000000+$timeEnd["sec"]-$timeStart["sec"];
    echo round($time, 3)."秒";
  } else { //数据I/O
    $php_self = preg_replace("/(.{0,}?\/+)/", "", $_SERVER['PHP_SELF']);
    $fp = fopen($php_self, 'r');
    $timeStart = gettimeofday();
    for($i = 0; $i < 10000; $i++) {
      fread($fp, 10240);
      rewind($fp);
    }
    $timeEnd = gettimeofday();
    fclose($fp);
    $time = ($timeEnd["usec"]-$timeStart["usec"])/1000000+$timeEnd["sec"]-$timeStart["sec"];
    echo round($time, 3)."秒";
  }
  exit;
} elseif($_GET['act'] == "sendmail") { //网页为 邮件发送测试
  echo (false !== @mail($_GET['email'], 'MAIL SERVER TEST', 'This email is sent by MyProber.')) ? '<span class="f-green">结果：发送成功！</span>' : '<span class="f-red">结果：发送失败！</span>';
  exit;
} elseif($_GET['act'] == "function_check") { //网页为 函数支持检测
  echo '结果：'.str_replace('()', '', $_GET['function_name']).'() '.is_func( str_replace('()', '', $_GET['function_name']) );
  exit;
} elseif($_GET['act'] == "configuration_check") { //网页为 PHP配置参数检测
  echo '结果：'.$_GET['config_name'].' '.get_cfg($_GET['config_name']);
  exit;
} elseif($_POST['act'] == "mysql_connect") {  //网页为 MySQL连接测试
  if(function_exists("mysql_close") == 1) {
    if ( @mysql_connect($_POST['host'].":".$_POST['port'], $_POST['user'], $_POST['password']) ){
      $mysql_connect_result = '<span class="f-green">结果：连接成功！</span>';
    } else {
      $mysql_connect_result = '<span class="f-red">结果：连接失败！</span>';
    }
  } else {
    $mysql_connect_result = '<span class="f-red">结果：服务器不支持MySQL数据库！</span>';
  }
}

$time_start = get_microtime_float(); //开始时间
$dis_func = get_cfg_var("disable_functions");
$php_host = preg_replace('~^www\.~i', '', $_SERVER['HTTP_HOST']);
$php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
$php_url = 'http://'.$php_host.$php_self;

//函数：打印
function p($var) {
  var_dump($var);
  exit;
}

//函数：计时
function get_microtime_float() {
  $mtime = microtime();
  $mtime = explode(' ', $mtime);
  return $mtime[1] + $mtime[0];
}

//函数：内存使用量
function memory_usage() {
  $memory  = ( ! function_exists('memory_get_usage')) ? '0' : round(memory_get_usage()/1024/1024, 2).'MB';
  return $memory;
}

//函数：单位转换
function format_size($size) {
  $danwei=array(' B ',' K ',' M ',' G ',' T ');
  $allsize=array();
  $i=0;
  for($i = 0; $i <4; $i++) {
    if(floor($size/pow(1024,$i))==0){break;}
  }
  for($l = $i-1; $l >=0; $l--) {
    $allsize1[$l]=floor($size/pow(1024,$l));
    $allsize[$l]=$allsize1[$l]-$allsize1[$l+1]*1024;
  }
  $len = count($allsize);
  for($j = $len-1; $j >=0; $j--) {
    $strlen = 4-strlen($allsize[$j]);
    if($strlen==1)
      $allsize[$j] = "<font color='#FFFFFF'>0</font>".$allsize[$j];
    elseif($strlen==2)
      $allsize[$j] = "<font color='#FFFFFF'>00</font>".$allsize[$j];
    elseif($strlen==3)
      $allsize[$j] = "<font color='#FFFFFF'>000</font>".$allsize[$j];

    $fsize=$fsize.$allsize[$j].$danwei[$j];
  }  
  return $fsize;
}

//函数：检测函数支持
function is_func($funName = '') {
    if (!$funName || trim($funName) == '' || preg_match('~[^a-z0-9\_]+~i', $funName, $tmp)) return '错误';
  return (false !== function_exists($funName)) ? YES : NO;
}
//函数：检测PHP设置参数
function get_cfg($varName) {
  $result = get_cfg_var($varName);
  if($result == 0) return NO;
  elseif($result == 1) return YES;
  else return $result;
}

//linux系统探测
function sys_linux() {
    // CPU
    if (false === ($str = @file("/proc/cpuinfo"))) return false;
    $str = implode("", $str);
    @preg_match_all("/model\s+name\s{0,}\:+\s{0,}([\w\s\)\(\@.-]+)([\r\n]+)/s", $str, $model);
    @preg_match_all("/cpu\s+MHz\s{0,}\:+\s{0,}([\d\.]+)[\r\n]+/", $str, $mhz);
    @preg_match_all("/cache\s+size\s{0,}\:+\s{0,}([\d\.]+\s{0,}[A-Z]+[\r\n]+)/", $str, $cache);
    @preg_match_all("/bogomips\s{0,}\:+\s{0,}([\d\.]+)[\r\n]+/", $str, $bogomips);
    if (false !== is_array($model[1]))  {
    $res['cpu']['num'] = sizeof($model[1]);
    $res['cpu']['num_text'] = str_replace(array(1,2,4,8,16), array('单','双','四','八','十六'), $res['cpu']['num']).'核';
    /*
        for($i = 0; $i < $res['cpu']['num']; $i++) {
            $res['cpu']['model'][] = $model[1][$i].'&nbsp;('.$mhz[1][$i].')';
            $res['cpu']['mhz'][] = $mhz[1][$i];
            $res['cpu']['cache'][] = $cache[1][$i];
            $res['cpu']['bogomips'][] = $bogomips[1][$i];
        }*/
    $x1 = ($res['cpu']['num']==1) ? '' : ' ×'.$res['cpu']['num'];
    $mhz[1][0] = ' | 频率:'.$mhz[1][0];
    $cache[1][0] = ' | 二级缓存:'.$cache[1][0];
    $bogomips[1][0] = ' | Bogomips:'.$bogomips[1][0];
    $res['cpu']['model'][] = $model[1][0].$mhz[1][0].$cache[1][0].$bogomips[1][0].$x1;
        if (false !== is_array($res['cpu']['model'])) $res['cpu']['model'] = implode("<br />", $res['cpu']['model']);
        if (false !== is_array($res['cpu']['mhz'])) $res['cpu']['mhz'] = implode("<br />", $res['cpu']['mhz']);
        if (false !== is_array($res['cpu']['cache'])) $res['cpu']['cache'] = implode("<br />", $res['cpu']['cache']);
        if (false !== is_array($res['cpu']['bogomips'])) $res['cpu']['bogomips'] = implode("<br />", $res['cpu']['bogomips']);
  }
    // NETWORK
    // UPTIME
    if (false === ($str = @file("/proc/uptime"))) return false;
    $str = explode(' ', implode("", $str));
    $str = trim($str[0]);
    $min = $str / 60;
    $hours = $min / 60;
    $days = floor($hours / 24);
    $hours = floor($hours - ($days * 24));
    $min = floor($min - ($days * 60 * 24) - ($hours * 60));
    if ($days !== 0) $res['uptime'] = $days."天";
    if ($hours !== 0) $res['uptime'] .= $hours."小时";
    $res['uptime'] .= $min."分钟";
    // MEMORY
    if(false === ($str = @file("/proc/meminfo"))) return false;
    $str = implode("", $str);
    preg_match_all("/MemTotal\s{0,}\:+\s{0,}([\d\.]+).+?MemFree\s{0,}\:+\s{0,}([\d\.]+).+?Cached\s{0,}\:+\s{0,}([\d\.]+).+?SwapTotal\s{0,}\:+\s{0,}([\d\.]+).+?SwapFree\s{0,}\:+\s{0,}([\d\.]+)/s", $str, $buf);
    preg_match_all("/Buffers\s{0,}\:+\s{0,}([\d\.]+)/s", $str, $buffers);
    $res['mem_total'] = round($buf[1][0]/1024, 2);
    $res['mem_free'] = round($buf[2][0]/1024, 2);
    $res['mem_buffers'] = round($buffers[1][0]/1024, 2);
    $res['mem_cached'] = round($buf[3][0]/1024, 2);
    $res['mem_used'] = $res['mem_total']-$res['mem_free'];
    $res['mem_percent'] = (floatval($res['mem_total'])!=0)?round($res['mem_used']/$res['mem_total']*100,2):0;
    $res['mem_real_used'] = $res['mem_total'] - $res['mem_free'] - $res['mem_cached'] - $res['mem_buffers']; //真实内存使用
    $res['mem_real_free'] = $res['mem_total'] - $res['mem_real_used']; //真实空闲
    $res['mem_real_percent'] = (floatval($res['mem_total'])!=0)?round($res['mem_real_used']/$res['mem_total']*100,2):0; //真实内存使用率
    $res['mem_cached_percent'] = (floatval($res['mem_cached'])!=0)?round($res['mem_cached']/$res['mem_total']*100,2):0; //Cached内存使用率
    $res['swap_total'] = round($buf[4][0]/1024, 2);
    $res['swap_free'] = round($buf[5][0]/1024, 2);
    $res['swap_used'] = round($res['swap_total']-$res['swap_free'], 2);
    $res['swap_percent'] = (floatval($res['swap_total'])!=0)?round($res['swap_used']/$res['swap_total']*100,2):0;
    // LOAD AVG
    if (false === ($str = @file("/proc/loadavg"))) return false;
    $str = explode(' ', implode("", $str));
    $str = array_chunk($str, 4);
    $res['load_avg'] = implode(' ', $str[0]);
    return $res;
}

//FreeBSD系统探测
function sys_freebsd() {
  //CPU
  if (false === ($res['cpu']['num'] = get_key("hw.ncpu"))) return false;
  $res['cpu']['num_text'] = str_replace(array(1,2,4,8,16), array('单','双','四','八','十六'), $res['cpu']['num']).'核';
  $res['cpu']['model'] = get_key("hw.model");
  //LOAD AVG
  if (false === ($res['load_avg'] = get_key("vm.loadavg"))) return false;
  //UPTIME
  if (false === ($buf = get_key("kern.boottime"))) return false;
  $buf = explode(' ', $buf);
  $sys_ticks = time() - intval($buf[3]);
  $min = $sys_ticks / 60;
  $hours = $min / 60;
  $days = floor($hours / 24);
  $hours = floor($hours - ($days * 24));
  $min = floor($min - ($days * 60 * 24) - ($hours * 60));
  if ($days !== 0) $res['uptime'] = $days."天";
  if ($hours !== 0) $res['uptime'] .= $hours."小时";
  $res['uptime'] .= $min."分钟";
 //MEMORY
  if (false === ($buf = get_key("hw.physmem"))) return false;
  $res['mem_total'] = round($buf/1024/1024, 2);
  $str = get_key("vm.vmtotal");
  preg_match_all("/\nVirtual Memory[\:\s]*\(Total[\:\s]*([\d]+)K[\,\s]*Active[\:\s]*([\d]+)K\)\n/i", $str, $buff, PREG_SET_ORDER);
  preg_match_all("/\nReal Memory[\:\s]*\(Total[\:\s]*([\d]+)K[\,\s]*Active[\:\s]*([\d]+)K\)\n/i", $str, $buf, PREG_SET_ORDER);
  $res['mem_real_used'] = round($buf[0][2]/1024, 2);
  $res['mem_cached'] = round($buff[0][2]/1024, 2);
  $res['mem_used'] = round($buf[0][1]/1024, 2) + $res['mem_cached'];
  $res['mem_free'] = $res['mem_total'] - $res['mem_used'];
  $res['mem_percent'] = (floatval($res['mem_total'])!=0)?round($res['mem_used']/$res['mem_total']*100,2):0;
  $res['mem_real_percent'] = (floatval($res['mem_total'])!=0)?round($res['mem_real_used']/$res['mem_total']*100,2):0;
  return $res;
}

//取得参数值 FreeBSD
function get_key($keyName) {
  return do_command('sysctl', "-n $keyName");
}

//确定执行文件位置 FreeBSD
function find_command($commandName) {
  $path = array('/bin', '/sbin', '/usr/bin', '/usr/sbin', '/usr/local/bin', '/usr/local/sbin');
  foreach($path as $p) {
    if (@is_executable("$p/$commandName")) return "$p/$commandName";
  }
  return false;
}

//执行系统命令 FreeBSD
function do_command($commandName, $args) {
  $buffer = "";
  if (false === ($command = find_command($commandName))) return false;
  if ($fp = @popen("$command $args", 'r')) {
    while (!@feof($fp)) {
      $buffer .= @fgets($fp, 4096);
    }
    return trim($buffer);
  }
  return false;
}

//windows系统探测
function sys_windows() {return false;
  if(PHP_VERSION >= 5) {
    if(!class_exists("COM",false))
      return false;
    $obj_locator = new COM("WbemScripting.SWbemLocator");
    $wmi =& $obj_locator->ConnectServer();
  } else {
    return false;
  }
  //CPU
  $cpuinfo = GetWMI($wmi, "Win32_Processor", array("Name", "L2CacheSize", "NumberOfCores"));
  $res['cpu']['num'] = $cpuinfo[0]['NumberOfCores'];
  if (null == $res['cpu']['num']) {
    $res['cpu']['num'] = 1;
  }
  $res['cpu']['num_text'] = str_replace(array(1,2,4,8,16), array('单','双','四','八','十六'), $res['cpu']['num']).'核';
  /*
  for ($i=0;$i<$res['cpu']['num'];$i++) {

    $res['cpu']['model'] .= $cpuinfo[0]['Name']."<br />";

    $res['cpu']['cache'] .= $cpuinfo[0]['L2CacheSize']."<br />";

  }*/
  $cpuinfo[0]['L2CacheSize'] = ' ('.$cpuinfo[0]['L2CacheSize'].')';
  $x1 = ($res['cpu']['num']==1) ? '' : ' ×'.$res['cpu']['num'];
  $res['cpu']['model'] = $cpuinfo[0]['Name'].$cpuinfo[0]['L2CacheSize'].$x1;
  //SYSINFO
  $sysinfo = GetWMI($wmi, "Win32_OperatingSystem", array('LastBootUpTime','TotalVisibleMemorySize','FreePhysicalMemory','Caption','CSDVersion','SerialNumber','InstallDate'));
  $sysinfo[0]['Caption']=iconv('GBK', 'UTF-8',$sysinfo[0]['Caption']);
  $sysinfo[0]['CSDVersion']=iconv('GBK', 'UTF-8',$sysinfo[0]['CSDVersion']);
  $res['win_n'] = $sysinfo[0]['Caption'].' '.$sysinfo[0]['CSDVersion']." 序列号:{$sysinfo[0]['SerialNumber']} 于".date('Y年m月d日H:i:s',strtotime(substr($sysinfo[0]['InstallDate'],0,14)))."安装";
  //UPTIME
  $res['uptime'] = $sysinfo[0]['LastBootUpTime'];
  $sys_ticks = time() - strtotime(substr($res['uptime'], 0, 14));
  $min = $sys_ticks / 60;
  $hours = $min / 60;
  $days = floor($hours / 24);
  $hours = floor($hours - ($days * 24));
  $min = floor($min - ($days * 60 * 24) - ($hours * 60));
  if ($days !== 0) $res['uptime'] = $days."天";
  if ($hours !== 0) $res['uptime'] .= $hours."小时";
  $res['uptime'] .= $min."分钟";
  //MEMORY
  $res['mem_total'] = round($sysinfo[0]['TotalVisibleMemorySize']/1024,2);
  $res['mem_free'] = round($sysinfo[0]['FreePhysicalMemory']/1024,2);
  $res['mem_used'] = $res['mem_total']-$res['mem_free'];  //上面两行已经除以1024,这行不用再除了
  $res['mem_percent'] = round($res['mem_used'] / $res['mem_total']*100,2);
  //LoadPercentage
  $loadinfo = GetWMI($wmi, "Win32_Processor", array("LoadPercentage"));
  $res['load_avg'] = $loadinfo[0]['LoadPercentage'];
  return $res;
}
function GetWMI(&$wmi, $strClass, $strValue = array()) {
  $arrData = array();
  $objWEBM = $wmi->Get($strClass);
  $arrProp = $objWEBM->Properties_;
  $arrWEBMCol = $objWEBM->Instances_();
  foreach($arrWEBMCol as $objItem) {
    @reset($arrProp);
    $arrInstance = array();
    foreach($arrProp as $propItem) {
      eval("\$value = \$objItem->" . $propItem->Name . ";");
      if (empty($strValue)) {
        $arrInstance[$propItem->Name] = trim($value);
      } else {
        if (in_array($propItem->Name, $strValue)) {
          $arrInstance[$propItem->Name] = trim($value);
        }
      }
    }
    $arrData[] = $arrInstance;
  }
  return $arrData;
}

//根据操作系统取得CPU相关信息
switch(PHP_OS) {
  case "Linux":
    $sysReShow = (($sys_info = sys_linux()) !== false) ? "show" : "none";
    break;
  case "FreeBSD":
    $sysReShow = (($sys_info = sys_freebsd()) !== false) ? "show" : "none";
    break;
  case "WINNT":
    $sysReShow = (($sys_info = sys_windows()) !== false) ? "show" : "none";
    break;
  default:
    break;
}
$sys_info['disk_total'] = round(@disk_total_space('.') / (1024*1024*1024), 2);

//整理实时数据
$realtime = array(
  'time' => date('Y年n月j日 H:i:s'),
  'uptime' => $sys_info['uptime'],
  'disk_free' => round(@disk_free_space('.') / (1024*1024*1024), 2).' G',
  'mem_used' => round($sys_info['mem_used']/1024, 2).' G',
  'mem_free' => round($sys_info['mem_free']/1024, 2).' G',
  'mem_cached' => round($sys_info['mem_cached']/1024, 2).' G',
  'mem_buffers' => round($sys_info['mem_buffers']/1024, 2).' G',
  'mem_real_used' => round($sys_info['mem_real_used']/1024, 2).' G', //真实内存使用
  'mem_real_free' => round($sys_info['mem_real_free']/1024, 2).' G', //真实内存空闲
  'mem_real_percent' => (int)$sys_info['mem_real_percent'].'%', //真实内存使用比率
  'mem_percent' => (int)$sys_info['mem_percent'].'%', //内存总使用率
  'mem_cached_percent' => (int)$sys_info['mem_cached_percent'].'%', //cache内存使用率
  'swap_percent' => (int)$sys_info['swap_percent'].'%',
  'load_avg' => $sys_info['load_avg'] //系统平均负载
);

//网卡流量
$network = @file("/proc/net/dev"); 
for($i=2; $i<count($network); $i++) {
  preg_match_all( "/([^\s]+):[\s]{0,}(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)/", $network[$i], $info);
  $realtime['network']['input'][$i] = format_size($info[2][0]);
  $realtime['network']['output'][$i] = format_size($info[10][0]);
}

//Ajax取服务器实时数据
if($_GET['act'] == 'realtime') {
  echo json_encode($realtime);
  exit;
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $title; ?> v<?php echo $version; ?> PHP探针</title>
<style type="text/css">
html{color:#404040;font-size:12px;overflow:-moz-scrollbars-vertical;overflow-y:scroll}
body,div,dl,dt,dd,ul,ol,li,h1,h2,h3,h4,h5,h6,pre,code,form,fieldset,legend,input,button,textarea,p,blockquote,th,td{margin:0;padding:0;font-family:Tahoma,"Microsoft Yahei",Arial}
header,nav,article,section,aside,footer{display:block}
table{border-collapse:collapse;border-spacing:0}
fieldset,img{border:0}
address,caption,cite,code,dfn,em,strong,th,var,optgroup{font-style:inherit;font-weight:inherit}
strong{font-weight:bold}
del,ins{text-decoration:none}
li{list-style:none}
caption,th{text-align:left}
h1,h2,h3,h4,h5,h6{font-size:100%;font-weight:normal}
q:before,q:after{content:''}
abbr,acronym{border:0;font-variant:normal}
sup{vertical-align:baseline}
sub{vertical-align:baseline}
legend{color:#000}
input,button,textarea,select,optgroup,option{font-family:inherit;font-size:inherit;font-style:inherit;font-weight:inherit}
input,button,textarea,select{*font-size:100%}
a{font-size:12px;color:#067bb2;text-decoration:none}
a:hover{color:#f60;text-decoration:underline}
a:active{color:#f00;text-decoration:none}
input.btn{height:25px;display:inline-block;outline:0;line-height:23px;-moz-border-radius:3px;-webkit-border-radius:3px;border-radius:3px;font-size:14px;padding:0 20px;-moz-box-shadow:2px 2px 3px #999;-webkit-box-shadow:2px 2px 3px #999;box-shadow:2px 2px 3px #999;color:#555;background:-moz-linear-gradient(19% 75% 90deg,#ddd,#eee,#fff 100%);background:-webkit-gradient(linear,0% 0,0% 100%,from(#fff),to(#ddd),color-stop(.3,#eee));border:1px solid #ccc}
input.btn:hover{background:-moz-linear-gradient(19% 75% 90deg,#f0deb8,#fdf0d1,#fff 100%);background:-webkit-gradient(linear,0% 0,0% 100%,from(#FFF),to(#f0deb8),color-stop(.3,#fdf0d1));color:#836d4d;border:1px solid #d4c198}
input.btn:active{background:-moz-linear-gradient(19% 75% 90deg,#fff,#fdf0d1,#f0deb8 100%);background:-webkit-gradient(linear,0% 0,0% 100%,from(#f0deb8),to(#fff),color-stop(.3,#fdf0d1));color:#836d4d;border:1px solid #d4c198;-moz-box-shadow:2px 2px 3px #999;-webkit-box-shadow:0 0 0 #999;box-shadow:1px 1px 2px #999}
input.active{background:-moz-linear-gradient(19% 75% 90deg,#f0deb8,#fdf0d1,#fff 100%);background:-webkit-gradient(linear,0% 0,0% 100%,from(#FFF),to(#f0deb8),color-stop(.3,#fdf0d1));color:#836d4d;border:1px solid #d4c198}
input.q{width:300px;height:25px;line-height:25px;padding:0 4px;font-size:13px;color:#a8a8a8;background:#f7f7f7;border:1px solid #ccc;-moz-border-radius:3px;-webkit-border-radius:3px;border-radius:3px;-moz-box-shadow:2px 2px 3px #ddd inset;-webkit-box-shadow:2px 2px 3px #ddd inset;box-shadow:2px 2px 3px #ddd inset}
textarea.q{width:400px;height:200px;line-height:20px;padding:2px 5px;font-size:12px;overflow:auto;color:#444;border:solid 1px #cecece;background:url(image/input.png) no-repeat 0 0}
textarea.q2{background:url(image/input.png) no-repeat 0 -500px}
.bar{border:solid 1px #999;background:#fff;height:5px;font-size:2px;width:90%;margin:2px 0 5px 0;padding:1px}
.bar-dotted{border:1px dotted #999;background:#fff;height:5px;font-size:2px;width:90%;margin:2px 0 5px 0;padding:1px}
.barli-red{background:#f60;height:5px;margin:0;padding:0}
.barli-blue{background:#09F;height:5px;margin:0;padding:0}
.barli-green{background:#36b52a;height:5px;margin:0;padding:0}
.barli-grey{background:#999;height:5px;margin:0;padding:0}
.f-grey{color:#999}
.f-pink{color:#f800f3}
.f-orange{color:#f60}
.f-blue{color:#00f}
.f-red{color:#c00}
.f-green{color:#0a0}
.f-12{font-size:12px}
.f-14{font-size:14px}
.f-b{font-weight:bold}
.t-c{text-align:center}
.yes,.no{font-size:14px}
.yes{color:green}
.no{color:red}
.imgbor1,.imgbor{border:solid 1px #ddd;padding:1px;background:#fff}
.clear{clear:both;width:0;height:0;line-height:0;margin:0;padding:0}
.hide{display:none}
.container{margin:18px;}
.header .top1{height:6px;background:#ddd;border-bottom:solid 1px #aaa;border-left:solid 1px #aaa;border-right:solid 1px #aaa}
.header .logo{height:90px;padding:0 20px;color:#404040;border-bottom:solid 1px #bbb}
.header .logo a{font:Italic bold 35px/85px "Verdana";color:#71c300;text-decoration:none;margin-right:5px;text-shadow:1px 2px 3px #333}
.header .logo span{float:right}
.header .logo span a{font:Normal 12px/90px "Simsun";text-shadow:0;color:#067bb2;margin:0}
.header .logo a b{color:#ffb300}
.main{}
.main .nav-box{height:34px}
.main .nav{height:32px;border-top:solid 1px #eee;border-bottom:solid 1px #aaa;background:#ccc;border-left:solid 1px #aaa;border-right:solid 1px #aaa}
.main .nav li{width:120px;height:32px;float:left;border-left:solid 1px #eee;border-right:solid 1px #999;background:#ccc}
.main .nav li a{height:32px;line-height:32px;display:block;text-align:center;color:#404040;font-size:14px}
.main .nav li a:hover{background:#bbb;text-decoration:none}
.main .nav li.active{background:#aaa}
.main .nav li.active a{color:#fff;font-weight:bold}
.main .table{margin-bottom: 16px;}
.main .table .title{height:30px;line-height:30px;font-size:14px;padding:0 10px;background:#eee;border: solid 1px #ddd;border-bottom: 0;}
.main .table .title span{font:14px/30px bold "Webdings";color:#f60;margin-right:8px;display: none;}
.main .table .title a.more{float:right;line-height:30px;font:12px/30px bold Webdings;font-family:"Webdings";text-decoration:none;color:#b8b8b8;display: none;}
.main .table .content{width:100%;background:#fff;border-collapse:collapse}
.main .table .content td{border:solid 1px #ddd;line-height:20px;padding:3px 8px}
.main .table .content td.realtime-btn{padding:10px 8px}
.main .table .content td.servertest-btn{padding-bottom:7px}
.main .table .content td.form{padding:10px 8px}
.footer{margin:5px 0 10px;height:40px;line-height:40px;over-flow:hidden}
.footer .copy{float:left;height:40px}
.footer .link{float:right;height:40px}
</style>
<script>
(function(){var _jQuery=window.jQuery,_$=window.$;var jQuery=window.jQuery=window.$=function(selector,context){return new jQuery.fn.init(selector,context);};var quickExpr=/^[^<]*(<(.|\s)+>)[^>]*$|^#(\w+)$/,isSimple=/^.[^:#\[\.]*$/,undefined;jQuery.fn=jQuery.prototype={init:function(selector,context){selector=selector||document;if(selector.nodeType){this[0]=selector;this.length=1;return this;}if(typeof selector=="string"){var match=quickExpr.exec(selector);if(match&&(match[1]||!context)){if(match[1])selector=jQuery.clean([match[1]],context);else{var elem=document.getElementById(match[3]);if(elem){if(elem.id!=match[3])return jQuery().find(selector);return jQuery(elem);}selector=[];}}else
return jQuery(context).find(selector);}else if(jQuery.isFunction(selector))return jQuery(document)[jQuery.fn.ready?"ready":"load"](selector);return this.setArray(jQuery.makeArray(selector));},jquery:"1.2.6",size:function(){return this.length;},length:0,get:function(num){return num==undefined?jQuery.makeArray(this):this[num];},pushStack:function(elems){var ret=jQuery(elems);ret.prevObject=this;return ret;},setArray:function(elems){this.length=0;Array.prototype.push.apply(this,elems);return this;},each:function(callback,args){return jQuery.each(this,callback,args);},index:function(elem){var ret=-1;return jQuery.inArray(elem&&elem.jquery?elem[0]:elem,this);},attr:function(name,value,type){var options=name;if(name.constructor==String)if(value===undefined)return this[0]&&jQuery[type||"attr"](this[0],name);else{options={};options[name]=value;}return this.each(function(i){for(name in options)jQuery.attr(type?this.style:this,name,jQuery.prop(this,options[name],type,i,name));});},css:function(key,value){if((key=='width'||key=='height')&&parseFloat(value)<0)value=undefined;return this.attr(key,value,"curCSS");},text:function(text){if(typeof text!="object"&&text!=null)return this.empty().append((this[0]&&this[0].ownerDocument||document).createTextNode(text));var ret="";jQuery.each(text||this,function(){jQuery.each(this.childNodes,function(){if(this.nodeType!=8)ret+=this.nodeType!=1?this.nodeValue:jQuery.fn.text([this]);});});return ret;},wrapAll:function(html){if(this[0])jQuery(html,this[0].ownerDocument).clone().insertBefore(this[0]).map(function(){var elem=this;while(elem.firstChild)elem=elem.firstChild;return elem;}).append(this);return this;},wrapInner:function(html){return this.each(function(){jQuery(this).contents().wrapAll(html);});},wrap:function(html){return this.each(function(){jQuery(this).wrapAll(html);});},append:function(){return this.domManip(arguments,true,false,function(elem){if(this.nodeType==1)this.appendChild(elem);});},prepend:function(){return this.domManip(arguments,true,true,function(elem){if(this.nodeType==1)this.insertBefore(elem,this.firstChild);});},before:function(){return this.domManip(arguments,false,false,function(elem){this.parentNode.insertBefore(elem,this);});},after:function(){return this.domManip(arguments,false,true,function(elem){this.parentNode.insertBefore(elem,this.nextSibling);});},end:function(){return this.prevObject||jQuery([]);},find:function(selector){var elems=jQuery.map(this,function(elem){return jQuery.find(selector,elem);});return this.pushStack(/[^+>] [^+>]/.test(selector)||selector.indexOf("..")>-1?jQuery.unique(elems):elems);},clone:function(events){var ret=this.map(function(){if(jQuery.browser.msie&&!jQuery.isXMLDoc(this)){var clone=this.cloneNode(true),container=document.createElement("div");container.appendChild(clone);return jQuery.clean([container.innerHTML])[0];}else
return this.cloneNode(true);});var clone=ret.find("*").andSelf().each(function(){if(this[expando]!=undefined)this[expando]=null;});if(events===true)this.find("*").andSelf().each(function(i){if(this.nodeType==3)return;var events=jQuery.data(this,"events");for(var type in events)for(var handler in events[type])jQuery.event.add(clone[i],type,events[type][handler],events[type][handler].data);});return ret;},filter:function(selector){return this.pushStack(jQuery.isFunction(selector)&&jQuery.grep(this,function(elem,i){return selector.call(elem,i);})||jQuery.multiFilter(selector,this));},not:function(selector){if(selector.constructor==String)if(isSimple.test(selector))return this.pushStack(jQuery.multiFilter(selector,this,true));else
selector=jQuery.multiFilter(selector,this);var isArrayLike=selector.length&&selector[selector.length-1]!==undefined&&!selector.nodeType;return this.filter(function(){return isArrayLike?jQuery.inArray(this,selector)<0:this!=selector;});},add:function(selector){return this.pushStack(jQuery.unique(jQuery.merge(this.get(),typeof selector=='string'?jQuery(selector):jQuery.makeArray(selector))));},is:function(selector){return!!selector&&jQuery.multiFilter(selector,this).length>0;},hasClass:function(selector){return this.is("."+selector);},val:function(value){if(value==undefined){if(this.length){var elem=this[0];if(jQuery.nodeName(elem,"select")){var index=elem.selectedIndex,values=[],options=elem.options,one=elem.type=="select-one";if(index<0)return null;for(var i=one?index:0,max=one?index+1:options.length;i<max;i++){var option=options[i];if(option.selected){value=jQuery.browser.msie&&!option.attributes.value.specified?option.text:option.value;if(one)return value;values.push(value);}}return values;}else
return(this[0].value||"").replace(/\r/g,"");}return undefined;}if(value.constructor==Number)value+='';return this.each(function(){if(this.nodeType!=1)return;if(value.constructor==Array&&/radio|checkbox/.test(this.type))this.checked=(jQuery.inArray(this.value,value)>=0||jQuery.inArray(this.name,value)>=0);else if(jQuery.nodeName(this,"select")){var values=jQuery.makeArray(value);jQuery("option",this).each(function(){this.selected=(jQuery.inArray(this.value,values)>=0||jQuery.inArray(this.text,values)>=0);});if(!values.length)this.selectedIndex=-1;}else
this.value=value;});},html:function(value){return value==undefined?(this[0]?this[0].innerHTML:null):this.empty().append(value);},replaceWith:function(value){return this.after(value).remove();},eq:function(i){return this.slice(i,i+1);},slice:function(){return this.pushStack(Array.prototype.slice.apply(this,arguments));},map:function(callback){return this.pushStack(jQuery.map(this,function(elem,i){return callback.call(elem,i,elem);}));},andSelf:function(){return this.add(this.prevObject);},data:function(key,value){var parts=key.split(".");parts[1]=parts[1]?"."+parts[1]:"";if(value===undefined){var data=this.triggerHandler("getData"+parts[1]+"!",[parts[0]]);if(data===undefined&&this.length)data=jQuery.data(this[0],key);return data===undefined&&parts[1]?this.data(parts[0]):data;}else
return this.trigger("setData"+parts[1]+"!",[parts[0],value]).each(function(){jQuery.data(this,key,value);});},removeData:function(key){return this.each(function(){jQuery.removeData(this,key);});},domManip:function(args,table,reverse,callback){var clone=this.length>1,elems;return this.each(function(){if(!elems){elems=jQuery.clean(args,this.ownerDocument);if(reverse)elems.reverse();}var obj=this;if(table&&jQuery.nodeName(this,"table")&&jQuery.nodeName(elems[0],"tr"))obj=this.getElementsByTagName("tbody")[0]||this.appendChild(this.ownerDocument.createElement("tbody"));var scripts=jQuery([]);jQuery.each(elems,function(){var elem=clone?jQuery(this).clone(true)[0]:this;if(jQuery.nodeName(elem,"script"))scripts=scripts.add(elem);else{if(elem.nodeType==1)scripts=scripts.add(jQuery("script",elem).remove());callback.call(obj,elem);}});scripts.each(evalScript);});}};jQuery.fn.init.prototype=jQuery.fn;function evalScript(i,elem){if(elem.src)jQuery.ajax({url:elem.src,async:false,dataType:"script"});else
jQuery.globalEval(elem.text||elem.textContent||elem.innerHTML||"");if(elem.parentNode)elem.parentNode.removeChild(elem);}function now(){return+new Date;}jQuery.extend=jQuery.fn.extend=function(){var target=arguments[0]||{},i=1,length=arguments.length,deep=false,options;if(target.constructor==Boolean){deep=target;target=arguments[1]||{};i=2;}if(typeof target!="object"&&typeof target!="function")target={};if(length==i){target=this;--i;}for(;i<length;i++)if((options=arguments[i])!=null)for(var name in options){var src=target[name],copy=options[name];if(target===copy)continue;if(deep&&copy&&typeof copy=="object"&&!copy.nodeType)target[name]=jQuery.extend(deep,src||(copy.length!=null?[]:{}),copy);else if(copy!==undefined)target[name]=copy;}return target;};var expando="jQuery"+now(),uuid=0,windowData={},exclude=/z-?index|font-?weight|opacity|zoom|line-?height/i,defaultView=document.defaultView||{};jQuery.extend({noConflict:function(deep){window.$=_$;if(deep)window.jQuery=_jQuery;return jQuery;},isFunction:function(fn){return!!fn&&typeof fn!="string"&&!fn.nodeName&&fn.constructor!=Array&&/^[\s[]?function/.test(fn+"");},isXMLDoc:function(elem){return elem.documentElement&&!elem.body||elem.tagName&&elem.ownerDocument&&!elem.ownerDocument.body;},globalEval:function(data){data=jQuery.trim(data);if(data){var head=document.getElementsByTagName("head")[0]||document.documentElement,script=document.createElement("script");script.type="text/javascript";if(jQuery.browser.msie)script.text=data;else
script.appendChild(document.createTextNode(data));head.insertBefore(script,head.firstChild);head.removeChild(script);}},nodeName:function(elem,name){return elem.nodeName&&elem.nodeName.toUpperCase()==name.toUpperCase();},cache:{},data:function(elem,name,data){elem=elem==window?windowData:elem;var id=elem[expando];if(!id)id=elem[expando]=++uuid;if(name&&!jQuery.cache[id])jQuery.cache[id]={};if(data!==undefined)jQuery.cache[id][name]=data;return name?jQuery.cache[id][name]:id;},removeData:function(elem,name){elem=elem==window?windowData:elem;var id=elem[expando];if(name){if(jQuery.cache[id]){delete jQuery.cache[id][name];name="";for(name in jQuery.cache[id])break;if(!name)jQuery.removeData(elem);}}else{try{delete elem[expando];}catch(e){if(elem.removeAttribute)elem.removeAttribute(expando);}delete jQuery.cache[id];}},each:function(object,callback,args){var name,i=0,length=object.length;if(args){if(length==undefined){for(name in object)if(callback.apply(object[name],args)===false)break;}else
for(;i<length;)if(callback.apply(object[i++],args)===false)break;}else{if(length==undefined){for(name in object)if(callback.call(object[name],name,object[name])===false)break;}else
for(var value=object[0];i<length&&callback.call(value,i,value)!==false;value=object[++i]){}}return object;},prop:function(elem,value,type,i,name){if(jQuery.isFunction(value))value=value.call(elem,i);return value&&value.constructor==Number&&type=="curCSS"&&!exclude.test(name)?value+"px":value;},className:{add:function(elem,classNames){jQuery.each((classNames||"").split(/\s+/),function(i,className){if(elem.nodeType==1&&!jQuery.className.has(elem.className,className))elem.className+=(elem.className?" ":"")+className;});},remove:function(elem,classNames){if(elem.nodeType==1)elem.className=classNames!=undefined?jQuery.grep(elem.className.split(/\s+/),function(className){return!jQuery.className.has(classNames,className);}).join(" "):"";},has:function(elem,className){return jQuery.inArray(className,(elem.className||elem).toString().split(/\s+/))>-1;}},swap:function(elem,options,callback){var old={};for(var name in options){old[name]=elem.style[name];elem.style[name]=options[name];}callback.call(elem);for(var name in options)elem.style[name]=old[name];},css:function(elem,name,force){if(name=="width"||name=="height"){var val,props={position:"absolute",visibility:"hidden",display:"block"},which=name=="width"?["Left","Right"]:["Top","Bottom"];function getWH(){val=name=="width"?elem.offsetWidth:elem.offsetHeight;var padding=0,border=0;jQuery.each(which,function(){padding+=parseFloat(jQuery.curCSS(elem,"padding"+this,true))||0;border+=parseFloat(jQuery.curCSS(elem,"border"+this+"Width",true))||0;});val-=Math.round(padding+border);}if(jQuery(elem).is(":visible"))getWH();else
jQuery.swap(elem,props,getWH);return Math.max(0,val);}return jQuery.curCSS(elem,name,force);},curCSS:function(elem,name,force){var ret,style=elem.style;function color(elem){if(!jQuery.browser.safari)return false;var ret=defaultView.getComputedStyle(elem,null);return!ret||ret.getPropertyValue("color")=="";}if(name=="opacity"&&jQuery.browser.msie){ret=jQuery.attr(style,"opacity");return ret==""?"1":ret;}if(jQuery.browser.opera&&name=="display"){var save=style.outline;style.outline="0 solid black";style.outline=save;}if(name.match(/float/i))name=styleFloat;if(!force&&style&&style[name])ret=style[name];else if(defaultView.getComputedStyle){if(name.match(/float/i))name="float";name=name.replace(/([A-Z])/g,"-$1").toLowerCase();var computedStyle=defaultView.getComputedStyle(elem,null);if(computedStyle&&!color(elem))ret=computedStyle.getPropertyValue(name);else{var swap=[],stack=[],a=elem,i=0;for(;a&&color(a);a=a.parentNode)stack.unshift(a);for(;i<stack.length;i++)if(color(stack[i])){swap[i]=stack[i].style.display;stack[i].style.display="block";}ret=name=="display"&&swap[stack.length-1]!=null?"none":(computedStyle&&computedStyle.getPropertyValue(name))||"";for(i=0;i<swap.length;i++)if(swap[i]!=null)stack[i].style.display=swap[i];}if(name=="opacity"&&ret=="")ret="1";}else if(elem.currentStyle){var camelCase=name.replace(/\-(\w)/g,function(all,letter){return letter.toUpperCase();});ret=elem.currentStyle[name]||elem.currentStyle[camelCase];if(!/^\d+(px)?$/i.test(ret)&&/^\d/.test(ret)){var left=style.left,rsLeft=elem.runtimeStyle.left;elem.runtimeStyle.left=elem.currentStyle.left;style.left=ret||0;ret=style.pixelLeft+"px";style.left=left;elem.runtimeStyle.left=rsLeft;}}return ret;},clean:function(elems,context){var ret=[];context=context||document;if(typeof context.createElement=='undefined')context=context.ownerDocument||context[0]&&context[0].ownerDocument||document;jQuery.each(elems,function(i,elem){if(!elem)return;if(elem.constructor==Number)elem+='';if(typeof elem=="string"){elem=elem.replace(/(<(\w+)[^>]*?)\/>/g,function(all,front,tag){return tag.match(/^(abbr|br|col|img|input|link|meta|param|hr|area|embed)$/i)?all:front+"></"+tag+">";});var tags=jQuery.trim(elem).toLowerCase(),div=context.createElement("div");var wrap=!tags.indexOf("<opt")&&[1,"<select multiple='multiple'>","</select>"]||!tags.indexOf("<leg")&&[1,"<fieldset>","</fieldset>"]||tags.match(/^<(thead|tbody|tfoot|colg|cap)/)&&[1,"<table>","</table>"]||!tags.indexOf("<tr")&&[2,"<table><tbody>","</tbody></table>"]||(!tags.indexOf("<td")||!tags.indexOf("<th"))&&[3,"<table><tbody><tr>","</tr></tbody></table>"]||!tags.indexOf("<col")&&[2,"<table><tbody></tbody><colgroup>","</colgroup></table>"]||jQuery.browser.msie&&[1,"div<div>","</div>"]||[0,"",""];div.innerHTML=wrap[1]+elem+wrap[2];while(wrap[0]--)div=div.lastChild;if(jQuery.browser.msie){var tbody=!tags.indexOf("<table")&&tags.indexOf("<tbody")<0?div.firstChild&&div.firstChild.childNodes:wrap[1]=="<table>"&&tags.indexOf("<tbody")<0?div.childNodes:[];for(var j=tbody.length-1;j>=0;--j)if(jQuery.nodeName(tbody[j],"tbody")&&!tbody[j].childNodes.length)tbody[j].parentNode.removeChild(tbody[j]);if(/^\s/.test(elem))div.insertBefore(context.createTextNode(elem.match(/^\s*/)[0]),div.firstChild);}elem=jQuery.makeArray(div.childNodes);}if(elem.length===0&&(!jQuery.nodeName(elem,"form")&&!jQuery.nodeName(elem,"select")))return;if(elem[0]==undefined||jQuery.nodeName(elem,"form")||elem.options)ret.push(elem);else
ret=jQuery.merge(ret,elem);});return ret;},attr:function(elem,name,value){if(!elem||elem.nodeType==3||elem.nodeType==8)return undefined;var notxml=!jQuery.isXMLDoc(elem),set=value!==undefined,msie=jQuery.browser.msie;name=notxml&&jQuery.props[name]||name;if(elem.tagName){var special=/href|src|style/.test(name);if(name=="selected"&&jQuery.browser.safari)elem.parentNode.selectedIndex;if(name in elem&&notxml&&!special){if(set){if(name=="type"&&jQuery.nodeName(elem,"input")&&elem.parentNode)throw"type property can't be changed";elem[name]=value;}if(jQuery.nodeName(elem,"form")&&elem.getAttributeNode(name))return elem.getAttributeNode(name).nodeValue;return elem[name];}if(msie&&notxml&&name=="style")return jQuery.attr(elem.style,"cssText",value);if(set)elem.setAttribute(name,""+value);var attr=msie&&notxml&&special?elem.getAttribute(name,2):elem.getAttribute(name);return attr===null?undefined:attr;}if(msie&&name=="opacity"){if(set){elem.zoom=1;elem.filter=(elem.filter||"").replace(/alpha\([^)]*\)/,"")+(parseInt(value)+''=="NaN"?"":"alpha(opacity="+value*100+")");}return elem.filter&&elem.filter.indexOf("opacity=")>=0?(parseFloat(elem.filter.match(/opacity=([^)]*)/)[1])/100)+'':"";}name=name.replace(/-([a-z])/ig,function(all,letter){return letter.toUpperCase();});if(set)elem[name]=value;return elem[name];},trim:function(text){return(text||"").replace(/^\s+|\s+$/g,"");},makeArray:function(array){var ret=[];if(array!=null){var i=array.length;if(i==null||array.split||array.setInterval||array.call)ret[0]=array;else
while(i)ret[--i]=array[i];}return ret;},inArray:function(elem,array){for(var i=0,length=array.length;i<length;i++)if(array[i]===elem)return i;return-1;},merge:function(first,second){var i=0,elem,pos=first.length;if(jQuery.browser.msie){while(elem=second[i++])if(elem.nodeType!=8)first[pos++]=elem;}else
while(elem=second[i++])first[pos++]=elem;return first;},unique:function(array){var ret=[],done={};try{for(var i=0,length=array.length;i<length;i++){var id=jQuery.data(array[i]);if(!done[id]){done[id]=true;ret.push(array[i]);}}}catch(e){ret=array;}return ret;},grep:function(elems,callback,inv){var ret=[];for(var i=0,length=elems.length;i<length;i++)if(!inv!=!callback(elems[i],i))ret.push(elems[i]);return ret;},map:function(elems,callback){var ret=[];for(var i=0,length=elems.length;i<length;i++){var value=callback(elems[i],i);if(value!=null)ret[ret.length]=value;}return ret.concat.apply([],ret);}});var userAgent=navigator.userAgent.toLowerCase();jQuery.browser={version:(userAgent.match(/.+(?:rv|it|ra|ie)[\/: ]([\d.]+)/)||[])[1],safari:/webkit/.test(userAgent),opera:/opera/.test(userAgent),msie:/msie/.test(userAgent)&&!/opera/.test(userAgent),mozilla:/mozilla/.test(userAgent)&&!/(compatible|webkit)/.test(userAgent)};var styleFloat=jQuery.browser.msie?"styleFloat":"cssFloat";jQuery.extend({boxModel:!jQuery.browser.msie||document.compatMode=="CSS1Compat",props:{"for":"htmlFor","class":"className","float":styleFloat,cssFloat:styleFloat,styleFloat:styleFloat,readonly:"readOnly",maxlength:"maxLength",cellspacing:"cellSpacing"}});jQuery.each({parent:function(elem){return elem.parentNode;},parents:function(elem){return jQuery.dir(elem,"parentNode");},next:function(elem){return jQuery.nth(elem,2,"nextSibling");},prev:function(elem){return jQuery.nth(elem,2,"previousSibling");},nextAll:function(elem){return jQuery.dir(elem,"nextSibling");},prevAll:function(elem){return jQuery.dir(elem,"previousSibling");},siblings:function(elem){return jQuery.sibling(elem.parentNode.firstChild,elem);},children:function(elem){return jQuery.sibling(elem.firstChild);},contents:function(elem){return jQuery.nodeName(elem,"iframe")?elem.contentDocument||elem.contentWindow.document:jQuery.makeArray(elem.childNodes);}},function(name,fn){jQuery.fn[name]=function(selector){var ret=jQuery.map(this,fn);if(selector&&typeof selector=="string")ret=jQuery.multiFilter(selector,ret);return this.pushStack(jQuery.unique(ret));};});jQuery.each({appendTo:"append",prependTo:"prepend",insertBefore:"before",insertAfter:"after",replaceAll:"replaceWith"},function(name,original){jQuery.fn[name]=function(){var args=arguments;return this.each(function(){for(var i=0,length=args.length;i<length;i++)jQuery(args[i])[original](this);});};});jQuery.each({removeAttr:function(name){jQuery.attr(this,name,"");if(this.nodeType==1)this.removeAttribute(name);},addClass:function(classNames){jQuery.className.add(this,classNames);},removeClass:function(classNames){jQuery.className.remove(this,classNames);},toggleClass:function(classNames){jQuery.className[jQuery.className.has(this,classNames)?"remove":"add"](this,classNames);},remove:function(selector){if(!selector||jQuery.filter(selector,[this]).r.length){jQuery("*",this).add(this).each(function(){jQuery.event.remove(this);jQuery.removeData(this);});if(this.parentNode)this.parentNode.removeChild(this);}},empty:function(){jQuery(">*",this).remove();while(this.firstChild)this.removeChild(this.firstChild);}},function(name,fn){jQuery.fn[name]=function(){return this.each(fn,arguments);};});jQuery.each(["Height","Width"],function(i,name){var type=name.toLowerCase();jQuery.fn[type]=function(size){return this[0]==window?jQuery.browser.opera&&document.body["client"+name]||jQuery.browser.safari&&window["inner"+name]||document.compatMode=="CSS1Compat"&&document.documentElement["client"+name]||document.body["client"+name]:this[0]==document?Math.max(Math.max(document.body["scroll"+name],document.documentElement["scroll"+name]),Math.max(document.body["offset"+name],document.documentElement["offset"+name])):size==undefined?(this.length?jQuery.css(this[0],type):null):this.css(type,size.constructor==String?size:size+"px");};});function num(elem,prop){return elem[0]&&parseInt(jQuery.curCSS(elem[0],prop,true),10)||0;}var chars=jQuery.browser.safari&&parseInt(jQuery.browser.version)<417?"(?:[\\w*_-]|\\\\.)":"(?:[\\w\u0128-\uFFFF*_-]|\\\\.)",quickChild=new RegExp("^>\\s*("+chars+"+)"),quickID=new RegExp("^("+chars+"+)(#)("+chars+"+)"),quickClass=new RegExp("^([#.]?)("+chars+"*)");jQuery.extend({expr:{"":function(a,i,m){return m[2]=="*"||jQuery.nodeName(a,m[2]);},"#":function(a,i,m){return a.getAttribute("id")==m[2];},":":{lt:function(a,i,m){return i<m[3]-0;},gt:function(a,i,m){return i>m[3]-0;},nth:function(a,i,m){return m[3]-0==i;},eq:function(a,i,m){return m[3]-0==i;},first:function(a,i){return i==0;},last:function(a,i,m,r){return i==r.length-1;},even:function(a,i){return i%2==0;},odd:function(a,i){return i%2;},"first-child":function(a){return a.parentNode.getElementsByTagName("*")[0]==a;},"last-child":function(a){return jQuery.nth(a.parentNode.lastChild,1,"previousSibling")==a;},"only-child":function(a){return!jQuery.nth(a.parentNode.lastChild,2,"previousSibling");},parent:function(a){return a.firstChild;},empty:function(a){return!a.firstChild;},contains:function(a,i,m){return(a.textContent||a.innerText||jQuery(a).text()||"").indexOf(m[3])>=0;},visible:function(a){return"hidden"!=a.type&&jQuery.css(a,"display")!="none"&&jQuery.css(a,"visibility")!="hidden";},hidden:function(a){return"hidden"==a.type||jQuery.css(a,"display")=="none"||jQuery.css(a,"visibility")=="hidden";},enabled:function(a){return!a.disabled;},disabled:function(a){return a.disabled;},checked:function(a){return a.checked;},selected:function(a){return a.selected||jQuery.attr(a,"selected");},text:function(a){return"text"==a.type;},radio:function(a){return"radio"==a.type;},checkbox:function(a){return"checkbox"==a.type;},file:function(a){return"file"==a.type;},password:function(a){return"password"==a.type;},submit:function(a){return"submit"==a.type;},image:function(a){return"image"==a.type;},reset:function(a){return"reset"==a.type;},button:function(a){return"button"==a.type||jQuery.nodeName(a,"button");},input:function(a){return/input|select|textarea|button/i.test(a.nodeName);},has:function(a,i,m){return jQuery.find(m[3],a).length;},header:function(a){return/h\d/i.test(a.nodeName);},animated:function(a){return jQuery.grep(jQuery.timers,function(fn){return a==fn.elem;}).length;}}},parse:[/^(\[) *@?([\w-]+) *([!*$^~=]*) *('?"?)(.*?)\4 *\]/,/^(:)([\w-]+)\("?'?(.*?(\(.*?\))?[^(]*?)"?'?\)/,new RegExp("^([:.#]*)("+chars+"+)")],multiFilter:function(expr,elems,not){var old,cur=[];while(expr&&expr!=old){old=expr;var f=jQuery.filter(expr,elems,not);expr=f.t.replace(/^\s*,\s*/,"");cur=not?elems=f.r:jQuery.merge(cur,f.r);}return cur;},find:function(t,context){if(typeof t!="string")return[t];if(context&&context.nodeType!=1&&context.nodeType!=9)return[];context=context||document;var ret=[context],done=[],last,nodeName;while(t&&last!=t){var r=[];last=t;t=jQuery.trim(t);var foundToken=false,re=quickChild,m=re.exec(t);if(m){nodeName=m[1].toUpperCase();for(var i=0;ret[i];i++)for(var c=ret[i].firstChild;c;c=c.nextSibling)if(c.nodeType==1&&(nodeName=="*"||c.nodeName.toUpperCase()==nodeName))r.push(c);ret=r;t=t.replace(re,"");if(t.indexOf(" ")==0)continue;foundToken=true;}else{re=/^([>+~])\s*(\w*)/i;if((m=re.exec(t))!=null){r=[];var merge={};nodeName=m[2].toUpperCase();m=m[1];for(var j=0,rl=ret.length;j<rl;j++){var n=m=="~"||m=="+"?ret[j].nextSibling:ret[j].firstChild;for(;n;n=n.nextSibling)if(n.nodeType==1){var id=jQuery.data(n);if(m=="~"&&merge[id])break;if(!nodeName||n.nodeName.toUpperCase()==nodeName){if(m=="~")merge[id]=true;r.push(n);}if(m=="+")break;}}ret=r;t=jQuery.trim(t.replace(re,""));foundToken=true;}}if(t&&!foundToken){if(!t.indexOf(",")){if(context==ret[0])ret.shift();done=jQuery.merge(done,ret);r=ret=[context];t=" "+t.substr(1,t.length);}else{var re2=quickID;var m=re2.exec(t);if(m){m=[0,m[2],m[3],m[1]];}else{re2=quickClass;m=re2.exec(t);}m[2]=m[2].replace(/\\/g,"");var elem=ret[ret.length-1];if(m[1]=="#"&&elem&&elem.getElementById&&!jQuery.isXMLDoc(elem)){var oid=elem.getElementById(m[2]);if((jQuery.browser.msie||jQuery.browser.opera)&&oid&&typeof oid.id=="string"&&oid.id!=m[2])oid=jQuery('[@id="'+m[2]+'"]',elem)[0];ret=r=oid&&(!m[3]||jQuery.nodeName(oid,m[3]))?[oid]:[];}else{for(var i=0;ret[i];i++){var tag=m[1]=="#"&&m[3]?m[3]:m[1]!=""||m[0]==""?"*":m[2];if(tag=="*"&&ret[i].nodeName.toLowerCase()=="object")tag="param";r=jQuery.merge(r,ret[i].getElementsByTagName(tag));}if(m[1]==".")r=jQuery.classFilter(r,m[2]);if(m[1]=="#"){var tmp=[];for(var i=0;r[i];i++)if(r[i].getAttribute("id")==m[2]){tmp=[r[i]];break;}r=tmp;}ret=r;}t=t.replace(re2,"");}}if(t){var val=jQuery.filter(t,r);ret=r=val.r;t=jQuery.trim(val.t);}}if(t)ret=[];if(ret&&context==ret[0])ret.shift();done=jQuery.merge(done,ret);return done;},classFilter:function(r,m,not){m=" "+m+" ";var tmp=[];for(var i=0;r[i];i++){var pass=(" "+r[i].className+" ").indexOf(m)>=0;if(!not&&pass||not&&!pass)tmp.push(r[i]);}return tmp;},filter:function(t,r,not){var last;while(t&&t!=last){last=t;var p=jQuery.parse,m;for(var i=0;p[i];i++){m=p[i].exec(t);if(m){t=t.substring(m[0].length);m[2]=m[2].replace(/\\/g,"");break;}}if(!m)break;if(m[1]==":"&&m[2]=="not")r=isSimple.test(m[3])?jQuery.filter(m[3],r,true).r:jQuery(r).not(m[3]);else if(m[1]==".")r=jQuery.classFilter(r,m[2],not);else if(m[1]=="["){var tmp=[],type=m[3];for(var i=0,rl=r.length;i<rl;i++){var a=r[i],z=a[jQuery.props[m[2]]||m[2]];if(z==null||/href|src|selected/.test(m[2]))z=jQuery.attr(a,m[2])||'';if((type==""&&!!z||type=="="&&z==m[5]||type=="!="&&z!=m[5]||type=="^="&&z&&!z.indexOf(m[5])||type=="$="&&z.substr(z.length-m[5].length)==m[5]||(type=="*="||type=="~=")&&z.indexOf(m[5])>=0)^not)tmp.push(a);}r=tmp;}else if(m[1]==":"&&m[2]=="nth-child"){var merge={},tmp=[],test=/(-?)(\d*)n((?:\+|-)?\d*)/.exec(m[3]=="even"&&"2n"||m[3]=="odd"&&"2n+1"||!/\D/.test(m[3])&&"0n+"+m[3]||m[3]),first=(test[1]+(test[2]||1))-0,last=test[3]-0;for(var i=0,rl=r.length;i<rl;i++){var node=r[i],parentNode=node.parentNode,id=jQuery.data(parentNode);if(!merge[id]){var c=1;for(var n=parentNode.firstChild;n;n=n.nextSibling)if(n.nodeType==1)n.nodeIndex=c++;merge[id]=true;}var add=false;if(first==0){if(node.nodeIndex==last)add=true;}else if((node.nodeIndex-last)%first==0&&(node.nodeIndex-last)/first>=0)add=true;if(add^not)tmp.push(node);}r=tmp;}else{var fn=jQuery.expr[m[1]];if(typeof fn=="object")fn=fn[m[2]];if(typeof fn=="string")fn=eval("false||function(a,i){return "+fn+";}");r=jQuery.grep(r,function(elem,i){return fn(elem,i,m,r);},not);}}return{r:r,t:t};},dir:function(elem,dir){var matched=[],cur=elem[dir];while(cur&&cur!=document){if(cur.nodeType==1)matched.push(cur);cur=cur[dir];}return matched;},nth:function(cur,result,dir,elem){result=result||1;var num=0;for(;cur;cur=cur[dir])if(cur.nodeType==1&&++num==result)break;return cur;},sibling:function(n,elem){var r=[];for(;n;n=n.nextSibling){if(n.nodeType==1&&n!=elem)r.push(n);}return r;}});jQuery.event={add:function(elem,types,handler,data){if(elem.nodeType==3||elem.nodeType==8)return;if(jQuery.browser.msie&&elem.setInterval)elem=window;if(!handler.guid)handler.guid=this.guid++;if(data!=undefined){var fn=handler;handler=this.proxy(fn,function(){return fn.apply(this,arguments);});handler.data=data;}var events=jQuery.data(elem,"events")||jQuery.data(elem,"events",{}),handle=jQuery.data(elem,"handle")||jQuery.data(elem,"handle",function(){if(typeof jQuery!="undefined"&&!jQuery.event.triggered)return jQuery.event.handle.apply(arguments.callee.elem,arguments);});handle.elem=elem;jQuery.each(types.split(/\s+/),function(index,type){var parts=type.split(".");type=parts[0];handler.type=parts[1];var handlers=events[type];if(!handlers){handlers=events[type]={};if(!jQuery.event.special[type]||jQuery.event.special[type].setup.call(elem)===false){if(elem.addEventListener)elem.addEventListener(type,handle,false);else if(elem.attachEvent)elem.attachEvent("on"+type,handle);}}handlers[handler.guid]=handler;jQuery.event.global[type]=true;});elem=null;},guid:1,global:{},remove:function(elem,types,handler){if(elem.nodeType==3||elem.nodeType==8)return;var events=jQuery.data(elem,"events"),ret,index;if(events){if(types==undefined||(typeof types=="string"&&types.charAt(0)=="."))for(var type in events)this.remove(elem,type+(types||""));else{if(types.type){handler=types.handler;types=types.type;}jQuery.each(types.split(/\s+/),function(index,type){var parts=type.split(".");type=parts[0];if(events[type]){if(handler)delete events[type][handler.guid];else
for(handler in events[type])if(!parts[1]||events[type][handler].type==parts[1])delete events[type][handler];for(ret in events[type])break;if(!ret){if(!jQuery.event.special[type]||jQuery.event.special[type].teardown.call(elem)===false){if(elem.removeEventListener)elem.removeEventListener(type,jQuery.data(elem,"handle"),false);else if(elem.detachEvent)elem.detachEvent("on"+type,jQuery.data(elem,"handle"));}ret=null;delete events[type];}}});}for(ret in events)break;if(!ret){var handle=jQuery.data(elem,"handle");if(handle)handle.elem=null;jQuery.removeData(elem,"events");jQuery.removeData(elem,"handle");}}},trigger:function(type,data,elem,donative,extra){data=jQuery.makeArray(data);if(type.indexOf("!")>=0){type=type.slice(0,-1);var exclusive=true;}if(!elem){if(this.global[type])jQuery("*").add([window,document]).trigger(type,data);}else{if(elem.nodeType==3||elem.nodeType==8)return undefined;var val,ret,fn=jQuery.isFunction(elem[type]||null),event=!data[0]||!data[0].preventDefault;if(event){data.unshift({type:type,target:elem,preventDefault:function(){},stopPropagation:function(){},timeStamp:now()});data[0][expando]=true;}data[0].type=type;if(exclusive)data[0].exclusive=true;var handle=jQuery.data(elem,"handle");if(handle)val=handle.apply(elem,data);if((!fn||(jQuery.nodeName(elem,'a')&&type=="click"))&&elem["on"+type]&&elem["on"+type].apply(elem,data)===false)val=false;if(event)data.shift();if(extra&&jQuery.isFunction(extra)){ret=extra.apply(elem,val==null?data:data.concat(val));if(ret!==undefined)val=ret;}if(fn&&donative!==false&&val!==false&&!(jQuery.nodeName(elem,'a')&&type=="click")){this.triggered=true;try{elem[type]();}catch(e){}}this.triggered=false;}return val;},handle:function(event){var val,ret,namespace,all,handlers;event=arguments[0]=jQuery.event.fix(event||window.event);namespace=event.type.split(".");event.type=namespace[0];namespace=namespace[1];all=!namespace&&!event.exclusive;handlers=(jQuery.data(this,"events")||{})[event.type];for(var j in handlers){var handler=handlers[j];if(all||handler.type==namespace){event.handler=handler;event.data=handler.data;ret=handler.apply(this,arguments);if(val!==false)val=ret;if(ret===false){event.preventDefault();event.stopPropagation();}}}return val;},fix:function(event){if(event[expando]==true)return event;var originalEvent=event;event={originalEvent:originalEvent};var props="altKey attrChange attrName bubbles button cancelable charCode clientX clientY ctrlKey currentTarget data detail eventPhase fromElement handler keyCode metaKey newValue originalTarget pageX pageY prevValue relatedNode relatedTarget screenX screenY shiftKey srcElement target timeStamp toElement type view wheelDelta which".split(" ");for(var i=props.length;i;i--)event[props[i]]=originalEvent[props[i]];event[expando]=true;event.preventDefault=function(){if(originalEvent.preventDefault)originalEvent.preventDefault();originalEvent.returnValue=false;};event.stopPropagation=function(){if(originalEvent.stopPropagation)originalEvent.stopPropagation();originalEvent.cancelBubble=true;};event.timeStamp=event.timeStamp||now();if(!event.target)event.target=event.srcElement||document;if(event.target.nodeType==3)event.target=event.target.parentNode;if(!event.relatedTarget&&event.fromElement)event.relatedTarget=event.fromElement==event.target?event.toElement:event.fromElement;if(event.pageX==null&&event.clientX!=null){var doc=document.documentElement,body=document.body;event.pageX=event.clientX+(doc&&doc.scrollLeft||body&&body.scrollLeft||0)-(doc.clientLeft||0);event.pageY=event.clientY+(doc&&doc.scrollTop||body&&body.scrollTop||0)-(doc.clientTop||0);}if(!event.which&&((event.charCode||event.charCode===0)?event.charCode:event.keyCode))event.which=event.charCode||event.keyCode;if(!event.metaKey&&event.ctrlKey)event.metaKey=event.ctrlKey;if(!event.which&&event.button)event.which=(event.button&1?1:(event.button&2?3:(event.button&4?2:0)));return event;},proxy:function(fn,proxy){proxy.guid=fn.guid=fn.guid||proxy.guid||this.guid++;return proxy;},special:{ready:{setup:function(){bindReady();return;},teardown:function(){return;}},mouseenter:{setup:function(){if(jQuery.browser.msie)return false;jQuery(this).bind("mouseover",jQuery.event.special.mouseenter.handler);return true;},teardown:function(){if(jQuery.browser.msie)return false;jQuery(this).unbind("mouseover",jQuery.event.special.mouseenter.handler);return true;},handler:function(event){if(withinElement(event,this))return true;event.type="mouseenter";return jQuery.event.handle.apply(this,arguments);}},mouseleave:{setup:function(){if(jQuery.browser.msie)return false;jQuery(this).bind("mouseout",jQuery.event.special.mouseleave.handler);return true;},teardown:function(){if(jQuery.browser.msie)return false;jQuery(this).unbind("mouseout",jQuery.event.special.mouseleave.handler);return true;},handler:function(event){if(withinElement(event,this))return true;event.type="mouseleave";return jQuery.event.handle.apply(this,arguments);}}}};jQuery.fn.extend({bind:function(type,data,fn){return type=="unload"?this.one(type,data,fn):this.each(function(){jQuery.event.add(this,type,fn||data,fn&&data);});},one:function(type,data,fn){var one=jQuery.event.proxy(fn||data,function(event){jQuery(this).unbind(event,one);return(fn||data).apply(this,arguments);});return this.each(function(){jQuery.event.add(this,type,one,fn&&data);});},unbind:function(type,fn){return this.each(function(){jQuery.event.remove(this,type,fn);});},trigger:function(type,data,fn){return this.each(function(){jQuery.event.trigger(type,data,this,true,fn);});},triggerHandler:function(type,data,fn){return this[0]&&jQuery.event.trigger(type,data,this[0],false,fn);},toggle:function(fn){var args=arguments,i=1;while(i<args.length)jQuery.event.proxy(fn,args[i++]);return this.click(jQuery.event.proxy(fn,function(event){this.lastToggle=(this.lastToggle||0)%i;event.preventDefault();return args[this.lastToggle++].apply(this,arguments)||false;}));},hover:function(fnOver,fnOut){return this.bind('mouseenter',fnOver).bind('mouseleave',fnOut);},ready:function(fn){bindReady();if(jQuery.isReady)fn.call(document,jQuery);else
jQuery.readyList.push(function(){return fn.call(this,jQuery);});return this;}});jQuery.extend({isReady:false,readyList:[],ready:function(){if(!jQuery.isReady){jQuery.isReady=true;if(jQuery.readyList){jQuery.each(jQuery.readyList,function(){this.call(document);});jQuery.readyList=null;}jQuery(document).triggerHandler("ready");}}});var readyBound=false;function bindReady(){if(readyBound)return;readyBound=true;if(document.addEventListener&&!jQuery.browser.opera)document.addEventListener("DOMContentLoaded",jQuery.ready,false);if(jQuery.browser.msie&&window==top)(function(){if(jQuery.isReady)return;try{document.documentElement.doScroll("left");}catch(error){setTimeout(arguments.callee,0);return;}jQuery.ready();})();if(jQuery.browser.opera)document.addEventListener("DOMContentLoaded",function(){if(jQuery.isReady)return;for(var i=0;i<document.styleSheets.length;i++)if(document.styleSheets[i].disabled){setTimeout(arguments.callee,0);return;}jQuery.ready();},false);if(jQuery.browser.safari){var numStyles;(function(){if(jQuery.isReady)return;if(document.readyState!="loaded"&&document.readyState!="complete"){setTimeout(arguments.callee,0);return;}if(numStyles===undefined)numStyles=jQuery("style, link[rel=stylesheet]").length;if(document.styleSheets.length!=numStyles){setTimeout(arguments.callee,0);return;}jQuery.ready();})();}jQuery.event.add(window,"load",jQuery.ready);}jQuery.each(("blur,focus,load,resize,scroll,unload,click,dblclick,"+"mousedown,mouseup,mousemove,mouseover,mouseout,change,select,"+"submit,keydown,keypress,keyup,error").split(","),function(i,name){jQuery.fn[name]=function(fn){return fn?this.bind(name,fn):this.trigger(name);};});var withinElement=function(event,elem){var parent=event.relatedTarget;while(parent&&parent!=elem)try{parent=parent.parentNode;}catch(error){parent=elem;}return parent==elem;};jQuery(window).bind("unload",function(){jQuery("*").add(document).unbind();});jQuery.fn.extend({_load:jQuery.fn.load,load:function(url,params,callback){if(typeof url!='string')return this._load(url);var off=url.indexOf(" ");if(off>=0){var selector=url.slice(off,url.length);url=url.slice(0,off);}callback=callback||function(){};var type="GET";if(params)if(jQuery.isFunction(params)){callback=params;params=null;}else{params=jQuery.param(params);type="POST";}var self=this;jQuery.ajax({url:url,type:type,dataType:"html",data:params,complete:function(res,status){if(status=="success"||status=="notmodified")self.html(selector?jQuery("<div/>").append(res.responseText.replace(/<script(.|\s)*?\/script>/g,"")).find(selector):res.responseText);self.each(callback,[res.responseText,status,res]);}});return this;},serialize:function(){return jQuery.param(this.serializeArray());},serializeArray:function(){return this.map(function(){return jQuery.nodeName(this,"form")?jQuery.makeArray(this.elements):this;}).filter(function(){return this.name&&!this.disabled&&(this.checked||/select|textarea/i.test(this.nodeName)||/text|hidden|password/i.test(this.type));}).map(function(i,elem){var val=jQuery(this).val();return val==null?null:val.constructor==Array?jQuery.map(val,function(val,i){return{name:elem.name,value:val};}):{name:elem.name,value:val};}).get();}});jQuery.each("ajaxStart,ajaxStop,ajaxComplete,ajaxError,ajaxSuccess,ajaxSend".split(","),function(i,o){jQuery.fn[o]=function(f){return this.bind(o,f);};});var jsc=now();jQuery.extend({get:function(url,data,callback,type){if(jQuery.isFunction(data)){callback=data;data=null;}return jQuery.ajax({type:"GET",url:url,data:data,success:callback,dataType:type});},getScript:function(url,callback){return jQuery.get(url,null,callback,"script");},getJSON:function(url,data,callback){return jQuery.get(url,data,callback,"json");},post:function(url,data,callback,type){if(jQuery.isFunction(data)){callback=data;data={};}return jQuery.ajax({type:"POST",url:url,data:data,success:callback,dataType:type});},ajaxSetup:function(settings){jQuery.extend(jQuery.ajaxSettings,settings);},ajaxSettings:{url:location.href,global:true,type:"GET",timeout:0,contentType:"application/x-www-form-urlencoded",processData:true,async:true,data:null,username:null,password:null,accepts:{xml:"application/xml, text/xml",html:"text/html",script:"text/javascript, application/javascript",json:"application/json, text/javascript",text:"text/plain",_default:"*/*"}},lastModified:{},ajax:function(s){s=jQuery.extend(true,s,jQuery.extend(true,{},jQuery.ajaxSettings,s));var jsonp,jsre=/=\?(&|$)/g,status,data,type=s.type.toUpperCase();if(s.data&&s.processData&&typeof s.data!="string")s.data=jQuery.param(s.data);if(s.dataType=="jsonp"){if(type=="GET"){if(!s.url.match(jsre))s.url+=(s.url.match(/\?/)?"&":"?")+(s.jsonp||"callback")+"=?";}else if(!s.data||!s.data.match(jsre))s.data=(s.data?s.data+"&":"")+(s.jsonp||"callback")+"=?";s.dataType="json";}if(s.dataType=="json"&&(s.data&&s.data.match(jsre)||s.url.match(jsre))){jsonp="jsonp"+jsc++;if(s.data)s.data=(s.data+"").replace(jsre,"="+jsonp+"$1");s.url=s.url.replace(jsre,"="+jsonp+"$1");s.dataType="script";window[jsonp]=function(tmp){data=tmp;success();complete();window[jsonp]=undefined;try{delete window[jsonp];}catch(e){}if(head)head.removeChild(script);};}if(s.dataType=="script"&&s.cache==null)s.cache=false;if(s.cache===false&&type=="GET"){var ts=now();var ret=s.url.replace(/(\?|&)_=.*?(&|$)/,"$1_="+ts+"$2");s.url=ret+((ret==s.url)?(s.url.match(/\?/)?"&":"?")+"_="+ts:"");}if(s.data&&type=="GET"){s.url+=(s.url.match(/\?/)?"&":"?")+s.data;s.data=null;}if(s.global&&!jQuery.active++)jQuery.event.trigger("ajaxStart");var remote=/^(?:\w+:)?\/\/([^\/?#]+)/;if(s.dataType=="script"&&type=="GET"&&remote.test(s.url)&&remote.exec(s.url)[1]!=location.host){var head=document.getElementsByTagName("head")[0];var script=document.createElement("script");script.src=s.url;if(s.scriptCharset)script.charset=s.scriptCharset;if(!jsonp){var done=false;script.onload=script.onreadystatechange=function(){if(!done&&(!this.readyState||this.readyState=="loaded"||this.readyState=="complete")){done=true;success();complete();head.removeChild(script);}};}head.appendChild(script);return undefined;}var requestDone=false;var xhr=window.ActiveXObject?new ActiveXObject("Microsoft.XMLHTTP"):new XMLHttpRequest();if(s.username)xhr.open(type,s.url,s.async,s.username,s.password);else
xhr.open(type,s.url,s.async);try{if(s.data)xhr.setRequestHeader("Content-Type",s.contentType);if(s.ifModified)xhr.setRequestHeader("If-Modified-Since",jQuery.lastModified[s.url]||"Thu, 01 Jan 1970 00:00:00 GMT");xhr.setRequestHeader("X-Requested-With","XMLHttpRequest");xhr.setRequestHeader("Accept",s.dataType&&s.accepts[s.dataType]?s.accepts[s.dataType]+", */*":s.accepts._default);}catch(e){}if(s.beforeSend&&s.beforeSend(xhr,s)===false){s.global&&jQuery.active--;xhr.abort();return false;}if(s.global)jQuery.event.trigger("ajaxSend",[xhr,s]);var onreadystatechange=function(isTimeout){if(!requestDone&&xhr&&(xhr.readyState==4||isTimeout=="timeout")){requestDone=true;if(ival){clearInterval(ival);ival=null;}status=isTimeout=="timeout"&&"timeout"||!jQuery.httpSuccess(xhr)&&"error"||s.ifModified&&jQuery.httpNotModified(xhr,s.url)&&"notmodified"||"success";if(status=="success"){try{data=jQuery.httpData(xhr,s.dataType,s.dataFilter);}catch(e){status="parsererror";}}if(status=="success"){var modRes;try{modRes=xhr.getResponseHeader("Last-Modified");}catch(e){}if(s.ifModified&&modRes)jQuery.lastModified[s.url]=modRes;if(!jsonp)success();}else
jQuery.handleError(s,xhr,status);complete();if(s.async)xhr=null;}};if(s.async){var ival=setInterval(onreadystatechange,13);if(s.timeout>0)setTimeout(function(){if(xhr){xhr.abort();if(!requestDone)onreadystatechange("timeout");}},s.timeout);}try{xhr.send(s.data);}catch(e){jQuery.handleError(s,xhr,null,e);}if(!s.async)onreadystatechange();function success(){if(s.success)s.success(data,status);if(s.global)jQuery.event.trigger("ajaxSuccess",[xhr,s]);}function complete(){if(s.complete)s.complete(xhr,status);if(s.global)jQuery.event.trigger("ajaxComplete",[xhr,s]);if(s.global&&!--jQuery.active)jQuery.event.trigger("ajaxStop");}return xhr;},handleError:function(s,xhr,status,e){if(s.error)s.error(xhr,status,e);if(s.global)jQuery.event.trigger("ajaxError",[xhr,s,e]);},active:0,httpSuccess:function(xhr){try{return!xhr.status&&location.protocol=="file:"||(xhr.status>=200&&xhr.status<300)||xhr.status==304||xhr.status==1223||jQuery.browser.safari&&xhr.status==undefined;}catch(e){}return false;},httpNotModified:function(xhr,url){try{var xhrRes=xhr.getResponseHeader("Last-Modified");return xhr.status==304||xhrRes==jQuery.lastModified[url]||jQuery.browser.safari&&xhr.status==undefined;}catch(e){}return false;},httpData:function(xhr,type,filter){var ct=xhr.getResponseHeader("content-type"),xml=type=="xml"||!type&&ct&&ct.indexOf("xml")>=0,data=xml?xhr.responseXML:xhr.responseText;if(xml&&data.documentElement.tagName=="parsererror")throw"parsererror";if(filter)data=filter(data,type);if(type=="script")jQuery.globalEval(data);if(type=="json")data=eval("("+data+")");return data;},param:function(a){var s=[];if(a.constructor==Array||a.jquery)jQuery.each(a,function(){s.push(encodeURIComponent(this.name)+"="+encodeURIComponent(this.value));});else
for(var j in a)if(a[j]&&a[j].constructor==Array)jQuery.each(a[j],function(){s.push(encodeURIComponent(j)+"="+encodeURIComponent(this));});else
s.push(encodeURIComponent(j)+"="+encodeURIComponent(jQuery.isFunction(a[j])?a[j]():a[j]));return s.join("&").replace(/%20/g,"+");}});jQuery.fn.extend({show:function(speed,callback){return speed?this.animate({height:"show",width:"show",opacity:"show"},speed,callback):this.filter(":hidden").each(function(){this.style.display=this.oldblock||"";if(jQuery.css(this,"display")=="none"){var elem=jQuery("<"+this.tagName+" />").appendTo("body");this.style.display=elem.css("display");if(this.style.display=="none")this.style.display="block";elem.remove();}}).end();},hide:function(speed,callback){return speed?this.animate({height:"hide",width:"hide",opacity:"hide"},speed,callback):this.filter(":visible").each(function(){this.oldblock=this.oldblock||jQuery.css(this,"display");this.style.display="none";}).end();},_toggle:jQuery.fn.toggle,toggle:function(fn,fn2){return jQuery.isFunction(fn)&&jQuery.isFunction(fn2)?this._toggle.apply(this,arguments):fn?this.animate({height:"toggle",width:"toggle",opacity:"toggle"},fn,fn2):this.each(function(){jQuery(this)[jQuery(this).is(":hidden")?"show":"hide"]();});},slideDown:function(speed,callback){return this.animate({height:"show"},speed,callback);},slideUp:function(speed,callback){return this.animate({height:"hide"},speed,callback);},slideToggle:function(speed,callback){return this.animate({height:"toggle"},speed,callback);},fadeIn:function(speed,callback){return this.animate({opacity:"show"},speed,callback);},fadeOut:function(speed,callback){return this.animate({opacity:"hide"},speed,callback);},fadeTo:function(speed,to,callback){return this.animate({opacity:to},speed,callback);},animate:function(prop,speed,easing,callback){var optall=jQuery.speed(speed,easing,callback);return this[optall.queue===false?"each":"queue"](function(){if(this.nodeType!=1)return false;var opt=jQuery.extend({},optall),p,hidden=jQuery(this).is(":hidden"),self=this;for(p in prop){if(prop[p]=="hide"&&hidden||prop[p]=="show"&&!hidden)return opt.complete.call(this);if(p=="height"||p=="width"){opt.display=jQuery.css(this,"display");opt.overflow=this.style.overflow;}}if(opt.overflow!=null)this.style.overflow="hidden";opt.curAnim=jQuery.extend({},prop);jQuery.each(prop,function(name,val){var e=new jQuery.fx(self,opt,name);if(/toggle|show|hide/.test(val))e[val=="toggle"?hidden?"show":"hide":val](prop);else{var parts=val.toString().match(/^([+-]=)?([\d+-.]+)(.*)$/),start=e.cur(true)||0;if(parts){var end=parseFloat(parts[2]),unit=parts[3]||"px";if(unit!="px"){self.style[name]=(end||1)+unit;start=((end||1)/e.cur(true))*start;self.style[name]=start+unit;}if(parts[1])end=((parts[1]=="-="?-1:1)*end)+start;e.custom(start,end,unit);}else
e.custom(start,val,"");}});return true;});},queue:function(type,fn){if(jQuery.isFunction(type)||(type&&type.constructor==Array)){fn=type;type="fx";}if(!type||(typeof type=="string"&&!fn))return queue(this[0],type);return this.each(function(){if(fn.constructor==Array)queue(this,type,fn);else{queue(this,type).push(fn);if(queue(this,type).length==1)fn.call(this);}});},stop:function(clearQueue,gotoEnd){var timers=jQuery.timers;if(clearQueue)this.queue([]);this.each(function(){for(var i=timers.length-1;i>=0;i--)if(timers[i].elem==this){if(gotoEnd)timers[i](true);timers.splice(i,1);}});if(!gotoEnd)this.dequeue();return this;}});var queue=function(elem,type,array){if(elem){type=type||"fx";var q=jQuery.data(elem,type+"queue");if(!q||array)q=jQuery.data(elem,type+"queue",jQuery.makeArray(array));}return q;};jQuery.fn.dequeue=function(type){type=type||"fx";return this.each(function(){var q=queue(this,type);q.shift();if(q.length)q[0].call(this);});};jQuery.extend({speed:function(speed,easing,fn){var opt=speed&&speed.constructor==Object?speed:{complete:fn||!fn&&easing||jQuery.isFunction(speed)&&speed,duration:speed,easing:fn&&easing||easing&&easing.constructor!=Function&&easing};opt.duration=(opt.duration&&opt.duration.constructor==Number?opt.duration:jQuery.fx.speeds[opt.duration])||jQuery.fx.speeds.def;opt.old=opt.complete;opt.complete=function(){if(opt.queue!==false)jQuery(this).dequeue();if(jQuery.isFunction(opt.old))opt.old.call(this);};return opt;},easing:{linear:function(p,n,firstNum,diff){return firstNum+diff*p;},swing:function(p,n,firstNum,diff){return((-Math.cos(p*Math.PI)/2)+0.5)*diff+firstNum;}},timers:[],timerId:null,fx:function(elem,options,prop){this.options=options;this.elem=elem;this.prop=prop;if(!options.orig)options.orig={};}});jQuery.fx.prototype={update:function(){if(this.options.step)this.options.step.call(this.elem,this.now,this);(jQuery.fx.step[this.prop]||jQuery.fx.step._default)(this);if(this.prop=="height"||this.prop=="width")this.elem.style.display="block";},cur:function(force){if(this.elem[this.prop]!=null&&this.elem.style[this.prop]==null)return this.elem[this.prop];var r=parseFloat(jQuery.css(this.elem,this.prop,force));return r&&r>-10000?r:parseFloat(jQuery.curCSS(this.elem,this.prop))||0;},custom:function(from,to,unit){this.startTime=now();this.start=from;this.end=to;this.unit=unit||this.unit||"px";this.now=this.start;this.pos=this.state=0;this.update();var self=this;function t(gotoEnd){return self.step(gotoEnd);}t.elem=this.elem;jQuery.timers.push(t);if(jQuery.timerId==null){jQuery.timerId=setInterval(function(){var timers=jQuery.timers;for(var i=0;i<timers.length;i++)if(!timers[i]())timers.splice(i--,1);if(!timers.length){clearInterval(jQuery.timerId);jQuery.timerId=null;}},13);}},show:function(){this.options.orig[this.prop]=jQuery.attr(this.elem.style,this.prop);this.options.show=true;this.custom(0,this.cur());if(this.prop=="width"||this.prop=="height")this.elem.style[this.prop]="1px";jQuery(this.elem).show();},hide:function(){this.options.orig[this.prop]=jQuery.attr(this.elem.style,this.prop);this.options.hide=true;this.custom(this.cur(),0);},step:function(gotoEnd){var t=now();if(gotoEnd||t>this.options.duration+this.startTime){this.now=this.end;this.pos=this.state=1;this.update();this.options.curAnim[this.prop]=true;var done=true;for(var i in this.options.curAnim)if(this.options.curAnim[i]!==true)done=false;if(done){if(this.options.display!=null){this.elem.style.overflow=this.options.overflow;this.elem.style.display=this.options.display;if(jQuery.css(this.elem,"display")=="none")this.elem.style.display="block";}if(this.options.hide)this.elem.style.display="none";if(this.options.hide||this.options.show)for(var p in this.options.curAnim)jQuery.attr(this.elem.style,p,this.options.orig[p]);}if(done)this.options.complete.call(this.elem);return false;}else{var n=t-this.startTime;this.state=n/this.options.duration;this.pos=jQuery.easing[this.options.easing||(jQuery.easing.swing?"swing":"linear")](this.state,n,0,1,this.options.duration);this.now=this.start+((this.end-this.start)*this.pos);this.update();}return true;}};jQuery.extend(jQuery.fx,{speeds:{slow:600,fast:200,def:400},step:{scrollLeft:function(fx){fx.elem.scrollLeft=fx.now;},scrollTop:function(fx){fx.elem.scrollTop=fx.now;},opacity:function(fx){jQuery.attr(fx.elem.style,"opacity",fx.now);},_default:function(fx){fx.elem.style[fx.prop]=fx.now+fx.unit;}}});jQuery.fn.offset=function(){var left=0,top=0,elem=this[0],results;if(elem)with(jQuery.browser){var parent=elem.parentNode,offsetChild=elem,offsetParent=elem.offsetParent,doc=elem.ownerDocument,safari2=safari&&parseInt(version)<522&&!/adobeair/i.test(userAgent),css=jQuery.curCSS,fixed=css(elem,"position")=="fixed";if(elem.getBoundingClientRect){var box=elem.getBoundingClientRect();add(box.left+Math.max(doc.documentElement.scrollLeft,doc.body.scrollLeft),box.top+Math.max(doc.documentElement.scrollTop,doc.body.scrollTop));add(-doc.documentElement.clientLeft,-doc.documentElement.clientTop);}else{add(elem.offsetLeft,elem.offsetTop);while(offsetParent){add(offsetParent.offsetLeft,offsetParent.offsetTop);if(mozilla&&!/^t(able|d|h)$/i.test(offsetParent.tagName)||safari&&!safari2)border(offsetParent);if(!fixed&&css(offsetParent,"position")=="fixed")fixed=true;offsetChild=/^body$/i.test(offsetParent.tagName)?offsetChild:offsetParent;offsetParent=offsetParent.offsetParent;}while(parent&&parent.tagName&&!/^body|html$/i.test(parent.tagName)){if(!/^inline|table.*$/i.test(css(parent,"display")))add(-parent.scrollLeft,-parent.scrollTop);if(mozilla&&css(parent,"overflow")!="visible")border(parent);parent=parent.parentNode;}if((safari2&&(fixed||css(offsetChild,"position")=="absolute"))||(mozilla&&css(offsetChild,"position")!="absolute"))add(-doc.body.offsetLeft,-doc.body.offsetTop);if(fixed)add(Math.max(doc.documentElement.scrollLeft,doc.body.scrollLeft),Math.max(doc.documentElement.scrollTop,doc.body.scrollTop));}results={top:top,left:left};}function border(elem){add(jQuery.curCSS(elem,"borderLeftWidth",true),jQuery.curCSS(elem,"borderTopWidth",true));}function add(l,t){left+=parseInt(l,10)||0;top+=parseInt(t,10)||0;}return results;};jQuery.fn.extend({position:function(){var left=0,top=0,results;if(this[0]){var offsetParent=this.offsetParent(),offset=this.offset(),parentOffset=/^body|html$/i.test(offsetParent[0].tagName)?{top:0,left:0}:offsetParent.offset();offset.top-=num(this,'marginTop');offset.left-=num(this,'marginLeft');parentOffset.top+=num(offsetParent,'borderTopWidth');parentOffset.left+=num(offsetParent,'borderLeftWidth');results={top:offset.top-parentOffset.top,left:offset.left-parentOffset.left};}return results;},offsetParent:function(){var offsetParent=this[0].offsetParent;while(offsetParent&&(!/^body|html$/i.test(offsetParent.tagName)&&jQuery.css(offsetParent,'position')=='static'))offsetParent=offsetParent.offsetParent;return jQuery(offsetParent);}});jQuery.each(['Left','Top'],function(i,name){var method='scroll'+name;jQuery.fn[method]=function(val){if(!this[0])return;return val!=undefined?this.each(function(){this==window||this==document?window.scrollTo(!i?val:jQuery(window).scrollLeft(),i?val:jQuery(window).scrollTop()):this[method]=val;}):this[0]==window||this[0]==document?self[i?'pageYOffset':'pageXOffset']||jQuery.boxModel&&document.documentElement[method]||document.body[method]:this[0][method];};});jQuery.each(["Height","Width"],function(i,name){var tl=i?"Left":"Top",br=i?"Right":"Bottom";jQuery.fn["inner"+name]=function(){return this[name.toLowerCase()]()+num(this,"padding"+tl)+num(this,"padding"+br);};jQuery.fn["outer"+name]=function(margin){return this["inner"+name]()+num(this,"border"+tl+"Width")+num(this,"border"+br+"Width")+(margin?num(this,"margin"+tl)+num(this,"margin"+br):0);};});})();
(function($){$.tourTabing=function(a,b){if(typeof(a)=='undefined')return $.tourTabingObject;var c=c||{};c.navId=a;c.contentId=b;$.tourTabingObject=new $.tourTabingCore(c);$.tourTabingObject.init()};$.tourTabingCore=function(d){var e=this;e.div=$('#'+d.navId);e.divDefaultHeight=0;e.navs=$('#'+d.navId+' > li');e.contents=$('#'+d.contentId+' > .tabing');e.heights=[];e.len=e.contents.length;e.init=function(){$(e.navs[0]).addClass('active');e.divDefaultHeight=$(e.div).offset().top;$(e.div).css({left:$(e.div).offset().left,width:$(e.div).outerWidth(),zIndex:99});$(e.navs).each(function(i){e.heights[i]=$(e.contents[i]).offset().top;$(this).bind('click',function(){e.active(i)});$(this).find('a').focus(function(){this.blur()})});$(window).bind('scroll',e.move).bind('resize',e.move)};e.active=function(a){e.reset();$(e.navs[a]).addClass('active');$(window).scrollTop(e.heights[a]-$(e.div).outerHeight())};e.reset=function(){$(e.navs).each(function(i){$(this).removeClass('active')})};e.move=function(){var a=$(window).scrollTop();var b=($.browser.msie&&parseInt($.browser.version)<=6)?'absolute':'fixed';if(a>e.divDefaultHeight){$(e.div).css({position:b,top:0})}else{$(e.div).css({position:'static',top:e.divDefaultHeight})}var c=[];$.each(e.heights,function(i){c.push(e.heights[i])});c.reverse();$.each(c,function(i){if(a>(c[i]-$(e.div).outerHeight()-1)){e.reset();$(e.navs[e.len-i-1]).addClass('active');return false}})}}})(jQuery);var realtimeInterval;function getRealTimeData(){$.getJSON('<?php echo $php_url; ?>?act=realtime',displayRealTimeData)}function displayRealTimeData(a){$("#time").text(a.time);$("#uptime").text(a.uptime);$("#diskFree").html(a.disk_free);$("#memTotal").html(a.mem_total);$("#memUsed").html(a.mem_used);$("#memFree").html(a.mem_free);$("#memPercent").html(a.mem_percent);$("#memCached").html(a.mem_cached);$("#memCachedPercent").html(a.mem_cached_percent);$("#memCachedBuffers").html(a.mem_cached_buffers);$("#memRealUsed").html(a.mem_real_used);$("#memRealFree").html(a.mem_real_free);$("#memRealPercent").html(a.mem_real_percent);$("#swapUsed").html(a.swap_used);$("#swapFree").html(a.swap_free);$("#swapPercent").html(a.swap_percent);$("#loadAvg").html(a.load_avg);$('#barMemPercent').width(a.mem_percent);$('#barMemCachedPercent').width(a.mem_cached_percent);$('#barMemRealPercent').width(a.mem_real_percent);$('#barSwapPercent').width(a.swap_percent);if(typeof(a.network)!='undefined'){$("#networkInput2").html(a.network['input'][2]);$("#networkInput3").html(a.network['input'][3]);$("#networkInput4").html(a.network['input'][4]);$("#networkInput5").html(a.network['input'][5]);$("#networkInput6").html(a.network['input'][6]);$("#networkInput7").html(a.network['input'][7]);$("#networkInput8").html(a.network['input'][8]);$("#networkInput9").html(a.network['input'][9]);$("#networkInput10").html(a.network['input'][10]);$("#networkOutput2").html(a.network['output'][2]);$("#networkOutput3").html(a.network['output'][3]);$("#networkOutput4").html(a.network['output'][4]);$("#networkOutput5").html(a.network['output'][5]);$("#networkOutput6").html(a.network['output'][6]);$("#networkOutput7").html(a.network['output'][7]);$("#networkOutput8").html(a.network['output'][8]);$("#networkOutput9").html(a.network['output'][9]);$("#networkOutput10").html(a.network['output'][10])}}$(document).ready(function(){$('#barMemPercent').width('<?php echo $realtime['mem_percent']; ?>');$('#barMemCachedPercent').width('<?php echo $realtime['mem_cached_percent']; ?>');$('#barMemRealPercent').width('<?php echo $realtime['mem_real_percent']; ?>');$('#barSwapPercent').width('<?php echo $realtime['swap_percent']; ?>');$('.q:input').each(function(i){$(this).click(function(){if($(this).data('fd')!=true){$(this).val('');$(this).css({color:'#c00'});$(this).data('fd',true)}})});$('#realtimeButton').toggle(function(){$(this).addClass('active').val('关闭实时数据');realtimeInterval=setInterval(getRealTimeData,1000)},function(){$(this).removeClass('active').val('开启实时数据').next().html('');clearInterval(realtimeInterval)});$('.servertest-btn > .btn').each(function(i){$(this).click(function(){$(this).prev().prev().text('...').load('<?php echo $php_url; ?>?act=server_test&i='+i)})});$('#sendmailButton').click(function(){$(this).parent().next().text('...').load('<?php echo $php_url; ?>?act=sendmail&email='+$(this).prev().val())});$('#functionCheckButton').click(function(){$(this).parent().next().text('...').load('<?php echo $php_url; ?>?act=function_check&function_name='+$(this).prev().val())});$('#configCheckButton').click(function(){$(this).parent().next().text('...').load('<?php echo $php_url; ?>?act=configuration_check&config_name='+$(this).prev().val())});$.tourTabing('tourTabingNav','tourTabingContent')});
</script>
</head>
<body>
<div class="container">
  <div class="header" style="display:none;">
    <div class="top1"></div>
    <div class="logo"><span><a href="http://myprober.sinaapp.com" target="_blank">MyProber 主页</a> | <a href="http://weibo.com/guojikai" target="_blank">反馈</a></span><a href="<?php echo $php_url; ?>"><b>My</b>Prober</a> ver <?php echo $version; ?></div>
  </div>
  <div class="main" id="tourTabingContent">
    <div class="nav-box" style="display:none;">
      <ul class="nav" id="tourTabingNav">
        <li><a href="javascript:;">服务器参数</a></li>
        <li><a href="javascript:;">实时数据</a></li>
        <li><a href="javascript:;">PHP基本参数</a></li>
        <li><a href="javascript:;">组件支持</a></li>
        <li><a href="javascript:;">数据库支持</a></li>
        <li><a href="javascript:;">性能测试</a></li>
      </ul>
    </div>
    <div class="table tabing">
      <div class="title"><a href="#top" title="返回顶部" class="more">55</a><span>8</span>服务器参数</div>
      <table class="content">
        <tr>
          <td>服务器名/IP</td>
          <td colspan="3"><?php echo @get_current_user(); ?>@<?php echo $_SERVER['SERVER_NAME']; ?>(<?php echo $_SERVER['SERVER_ADDR']; ?>) | 你的IP：<?php echo @$_SERVER['REMOTE_ADDR']; ?></td>
        </tr>
        <tr>
          <td>服务器标识</td>
          <td colspan="3"><?php echo ($sys_info['win_n'] != '' ? $sys_info['win_n'] : @php_uname()); ?></td>
        </tr>
        <tr>
          <td width="14%">服务器操作系统</td>
          <td width="36%"><?php $os = explode(' ', php_uname()); echo $os[0]; ?> 内核版本：<?php echo ('/'==DIRECTORY_SEPARATOR ? $os[2] : $os[1]); ?></td>
          <td width="14%">服务器解译引擎</td>
          <td width="36%"><?php echo $_SERVER['SERVER_SOFTWARE']; ?></td>
        </tr>
        <tr>
          <td>服务器语言</td>
          <td><?php echo getenv("HTTP_ACCEPT_LANGUAGE"); ?></td>
          <td>服务器端口</td>
          <td><?php echo $_SERVER['SERVER_PORT']; ?></td>
        </tr>
        <tr>
          <td>服务器主机名</td>
          <td><?php echo ('/'==DIRECTORY_SEPARATOR ? $os[1] : $os[2]); ?></td>
          <td>绝对路径</td>
          <td><?php echo $_SERVER['DOCUMENT_ROOT'] ? str_replace('\\','/',$_SERVER['DOCUMENT_ROOT']) : str_replace('\\','/',dirname(__FILE__)); ?></td>
        </tr>
        <tr style="display:none;">
          <td>管理员邮箱</td>
          <td><?php echo (empty($_SERVER['SERVER_ADMIN']) ? NO : $_SERVER['SERVER_ADMIN']); ?></td>
          <td>探针路径</td>
          <td><?php echo str_replace('\\','/',__FILE__) ? str_replace('\\','/',__FILE__) : $_SERVER['SCRIPT_FILENAME']; ?></td>
        </tr>
      </table>
    </div>
    <div class="table tabing">
      <div class="title"><a href="#top" title="返回顶部" class="more">55</a><span>8</span>服务器状态</div>
      <table class="content">
        <?php if($sys_info['mem_total']){ ?>
        <tr>
          <td>实时开关</td>
          <td colspan="3" class="realtime-btn"><input type="button" value="开启实时数据" class="btn" id="realtimeButton"  /></td>
        </tr>
        <?php } ?>
        <tr>
          <td width="14%">服务器当前时间</td>
          <td id="time"><?php echo $realtime['time']; ?></td>
          <td width="14%">服务器已运行时间</td>
          <td id="uptime"><?php echo $realtime['uptime']?$realtime['uptime']:"未知"; ?></td>
        </tr>
        <tr>
          <td>网站目录空间</td>
          <td id="diskTotal"><?php echo $sys_info['disk_total']; ?> G</td>
          <td>网站目录可用空间</td>
          <td><span class="f-red" id="diskFree"><?php echo $realtime['disk_free']; ?></span></td>
        </tr>
        <?php if($sys_info['cpu']){ ?>
        <tr>
          <td>CPU型号</td>
          <td colspan="3"><?php echo '['.$sys_info['cpu']['num_text'].'] '.$sys_info['cpu']['model']; ?></td>
        </tr>
        <?php } ?>
        <?php if($sys_info['mem_total']){ ?>
        <tr>
          <td>内存使用状况</td>
          <td colspan="3">

<?php
$tmp = array('mem_total', 'mem_used', 'mem_free', 'mem_percent','mem_cached',
             'mem_real_percent', 'swap_total', 'swap_used', 'swap_free', 'swap_percent'
             );
foreach ($tmp as $v) {
  $sys_info[$v] = $sys_info[$v] ? $sys_info[$v] : 0;
}
?>

          物理内存：共 <span class='f-red'><?php echo round($sys_info['mem_total']/1024, 2); ?> G</span> ，已用
          <span class='f-red' id="memUsed"><?php echo $realtime['mem_used']; ?></span> ，空闲
          <span class='f-red' id="memFree"><?php echo $realtime['mem_free']; ?></span> ，使用率
          <span id="memPercent"><?php echo $realtime['mem_percent']; ?></span>
          <div class="bar"><div id="barMemPercent" class="barli-green">&nbsp;</div></div>
<?php
//判断如果cache为0，不显示
if($sys_info['mem_cached'] > 0) {
?>    
      Cache化内存为 <span id="memCached"><?php echo $realtime['mem_cached']; ?></span> ，使用率 
          <span id="memCachedPercent"><?php echo $realtime['mem_cached_percent']; ?></span>  | Buffers缓冲为  
          <span id="memBuffers"><?php echo $realtime['mem_buffers']; ?></span>
          <div class="bar"><div id="barMemCachedPercent" class="barli-blue">&nbsp;</div></div> 真实内存使用
          <span id="memRealUsed"><?php echo $realtime['mem_real_used']; ?></span> ，真实内存空闲
          <span id="memRealFree"><?php echo $realtime['mem_real_free']; ?></span> ，使用率
          <span id="memRealPercent"><?php echo $realtime['mem_real_percent']; ?></span>
          <div class="bar-dotted"><div id="barMemRealPercent" class="barli-grey">&nbsp;</div></div> 
<?php
}
//判断如果SWAP区为0，不显示
if($sys_info['swap_total'] > 0) {
?>  
          SWAP区：共 <?php echo round($sys_info['swap_total']/1024, 2); ?> G ，已使用
          <?php echo round($sys_info['swap_used']/1024, 2); ?> G ，空闲
          <?php echo round($sys_info['swap_free']/1024, 2); ?> G ，使用率
          <?php echo $realtime['swap_percent']; ?>
          <div class="bar"><div id="barSwapPercent" class="barli-red">&nbsp;</div></div>
<?php
}  
?>      
    </td>
        </tr>
        <?php } ?>
        <?php if($realtime['load_avg']){ ?>
        <tr>
          <td>系统平均负载</td>
          <td colspan="3"><span class="f-pink" id="loadAvg"><?php echo $realtime['load_avg']; ?></span></td>
        </tr>
        <?php } ?>
      </table>
    </div>
<?php if(($network = @file("/proc/net/dev")) !== false) { ?>
    <div class="table">
      <div class="title"><a href="#top" title="返回顶部" class="more">55</a><span>8</span>网络使用状况</div>
      <table class="content">
<?php for($i=2; $i<count($network); $i++) { ?>
        <tr>
          <td width="14%"><?php echo $info[1][0]; ?></td>
          <td width="43%">已接收： <span class="red" id="networkInput<?php echo $i; ?>"><?php echo $realtime['network']['input'][$i]; ?></span></td>
          <td width="43%">已发送： <span class="red" id="networkOutput<?php echo $i; ?>"><?php echo $realtime['network']['output'][$i]; ?></span></td>
        </tr>
<?php } ?>
      </table>
    </div>
<?php } ?>
    <div class="table tabing">
      <div class="title"><a href="#top" title="返回顶部" class="more">55</a><span>8</span>PHP基本参数</div>
      <table class="content">
        <tr>
          <td width="32%">PHP信息（phpinfo）</td>
          <td width="18%"><?php echo (false !== eregi('phpinfo', $dis_func)) ? '<span class="no">×</span>' : '<a href="'.$php_url.'?act=phpinfo" target="_blank">phpinfo()</a>'; ?></td>
          <td width="32%">PHP版本（php_version）</td>
          <td width="18%"><?php echo PHP_VERSION; ?></td>
        </tr>
        <tr>
          <td width="32%">PHP运行方式</td>
          <td width="18%"><?php echo php_sapi_name(); ?></td>
          <td width="32%">PHP运行安全模式（safe_mode）</td>
          <td width="18%"><?php echo get_cfg("safe_mode"); ?></td>
        </tr>
        <tr>
          <td width="32%">脚本占用最大内存（memory_limit）</td>
          <td width="18%"><?php echo get_cfg('memory_limit'); ?></td>
          <td width="32%">POST方法提交最大限制（post_max_size）</td>
          <td width="18%"><?php echo get_cfg("post_max_size"); ?></td>
        </tr>
        <tr>
          <td width="32%">上传文件最大限制（upload_max_filesize）</td>
          <td width="18%"><?php echo get_cfg("upload_max_filesize"); ?></td>
          <td width="32%">浮点型数据显示的有效位数（precision）</td>
          <td width="18%"><?php echo get_cfg("precision"); ?></td>
        </tr>
        <tr>
          <td width="32%">脚本超时时间（max_execution_time）</td>
          <td width="18%"><?php echo get_cfg("max_execution_time"); ?>秒</td>
          <td width="32%">socket超时时间（default_socket_timeout）</td>
          <td width="18%"><?php echo get_cfg("default_socket_timeout"); ?>秒</td>
        </tr>
        <tr>
          <td width="32%">PHP页面根目录（doc_root）</td>
          <td width="18%"><?php echo get_cfg("doc_root"); ?></td>
          <td width="32%">用户根目录（user_dir）</td>
          <td width="18%"><?php echo get_cfg("user_dir"); ?></td>
        </tr>
        <tr>
          <td width="32%">dl()函数（enable_dl）</td>
          <td width="18%"><?php echo get_cfg("enable_dl"); ?></td>
          <td width="32%">指定包含文件目录（include_path）</td>
          <td width="18%"><?php echo get_cfg("include_path"); ?></td>
        </tr>
        <tr>
          <td width="32%">显示错误信息（display_errors）</td>
          <td width="18%"><?php echo get_cfg("display_errors"); ?></td>
          <td width="32%">自定义全局变量（register_globals）</td>
          <td width="18%"><?php echo get_cfg("register_globals"); ?></td>
        </tr>
        <tr>
          <td width="32%">数据反斜杠转义（magic_quotes_gpc）</td>
          <td width="18%"><?php echo get_cfg("magic_quotes_gpc"); ?></td>
          <td width="32%">短标签（short_open_tag）</td>
          <td width="18%"><?php echo get_cfg("short_open_tag"); ?></td>
        </tr>
        <tr>
          <td width="32%">ASP风格标记（asp_tags）</td>
          <td width="18%"><?php echo get_cfg("asp_tags"); ?></td>
          <td width="32%">忽略重复错误信息（ignore_repeated_errors）</td>
          <td width="18%"><?php echo get_cfg("ignore_repeated_errors"); ?></td>
        </tr>
        <tr>
          <td width="32%">忽略重复的错误源（ignore_repeated_source）</td>
          <td width="18%"><?php echo get_cfg("ignore_repeated_source"); ?></td>
          <td width="32%">报告内存泄漏（report_memleaks）</td>
          <td width="18%"><?php echo get_cfg("report_memleaks"); ?></td>
        </tr>
        <tr>
          <td width="32%">自动字符串转义（magic_quotes_gpc）</td>
          <td width="18%"><?php echo get_cfg("magic_quotes_gpc"); ?></td>
          <td width="32%">外部字符串自动转义（magic_quotes_runtime）</td>
          <td width="18%"><?php echo get_cfg("magic_quotes_runtime"); ?></td>
        </tr>
        <tr>
          <td width="32%">打开远程文件（allow_url_fopen）</td>
          <td width="18%"><?php echo get_cfg("allow_url_fopen"); ?></td>
          <td width="32%">声明argv和argc变量（register_argc_argv）</td>
          <td width="18%"><?php echo get_cfg("register_argc_argv"); ?></td>
        </tr>
        <tr>
          <td width="32%">Cookie 支持</td>
          <td width="18%"><?php echo isset($_COOKIE) ? YES : NO; ?></td>
          <td width="32%">拼写检查（ASpell Library）</td>
          <td width="18%"><?php echo is_func("aspell_check_raw"); ?></td>
        </tr>
        <tr>
          <td width="32%">高精度数学运算（BCMath）</td>
          <td width="18%"><?php echo is_func("bcadd"); ?></td>
          <td width="32%">PREL相容语法（PCRE）</td>
          <td width="18%"><?php echo is_func("preg_match"); ?></td>
        </tr>
        <tr>
          <td width="32%">PDF文档支持</td>
          <td width="18%"><?php echo is_func("pdf_close"); ?></td>
          <td width="32%">SNMP网络管理协议</td>
          <td width="18%"><?php echo is_func("snmpget"); ?></td>
        </tr>
        <tr>
          <td width="32%">VMailMgr邮件处理</td>
          <td width="18%"><?php echo is_func("vm_adduser"); ?></td>
          <td width="32%">Curl支持</td>
          <td width="18%"><?php echo is_func("curl_init"); ?></td>
        </tr>
        <tr>
          <td width="32%">SMTP支持</td>
          <td width="18%"><?php echo get_cfg_var("SMTP") ? YES : NO; ?></td>
          <td width="32%">SMTP地址</td>
          <td width="18%"><?php echo get_cfg_var("SMTP") ? get_cfg_var("SMTP") :NO; ?></td>
        </tr>
        <tr>
          <td width="32%">默认支持函数（enable_functions）</td>
          <td width="18%"><a href="<?php echo $php_url; ?>?act=enable_functions" target="_blank">get_defined_functions()</a></td>
          <td width="32%">被禁用的函数（disable_functions）</td>
          <td width="18%"><?php echo (empty($dis_func) ? '无' : '<a href="'.$php_url.'?act=disable_functions" target="_blank">disable_functions</a>'); ?></td>
        </tr>
      </table>
    </div>
    <div class="table tabing">
      <div class="title"><a href="#top" title="返回顶部" class="more">55</a><span>8</span>组件支持</div>
      <table class="content">
        <tr>
          <td width="32%">FTP支持</td>
          <td width="18%"><?php echo is_func("ftp_login"); ?></td>
          <td width="32%">XML解析支持</td>
          <td width="18%"><?php echo is_func("xml_set_object"); ?></td>
        </tr>
        <tr>
          <td width="32%">Session支持</td>
          <td width="18%"><?php echo is_func("session_start"); ?></td>
          <td width="32%">Socket支持</td>
          <td width="18%"><?php echo is_func("socket_accept"); ?></td>
        </tr>
        <tr>
          <td width="32%">Calendar支持</td>
          <td width="18%"><?php echo is_func('cal_days_in_month'); ?></td>
          <td width="32%">允许URL打开文件</td>
          <td width="18%"><?php echo get_cfg("allow_url_fopen"); ?></td>
        </tr>
        <tr>
          <td width="32%">GD库支持</td>
          <td width="18%"><?php
if(function_exists(gd_info)) {
  $gd_info = @gd_info();
  echo $gd_info["GD Version"];
} else {
  echo '<font color="red">×</font>';
}
?></td>
          <td width="32%">压缩文件支持(Zlib)</td>
          <td width="18%"><?php echo is_func("gzclose"); ?></td>
        </tr>
        <tr>
          <td width="32%">IMAP电子邮件系统函数库</td>
          <td width="18%"><?php echo is_func("imap_close"); ?></td>
          <td width="32%">历法运算函数库</td>
          <td width="18%"><?php echo is_func("JDToGregorian"); ?></td>
        </tr>
        <tr>
          <td width="32%">正则表达式函数库</td>
          <td width="18%"><?php echo is_func("preg_match"); ?></td>
          <td width="32%">WDDX支持</td>
          <td width="18%"><?php echo is_func("wddx_add_vars"); ?></td>
        </tr>
        <tr>
          <td width="32%">Iconv编码转换</td>
          <td width="18%"><?php echo is_func("iconv"); ?></td>
          <td width="32%">mbstring</td>
          <td width="18%"><?php echo is_func("mb_eregi"); ?></td>
        </tr>
        <tr>
          <td width="32%">高精度数学运算</td>
          <td width="18%"><?php echo is_func("bcadd"); ?></td>
          <td width="32%">LDAP目录协议</td>
          <td width="18%"><?php echo is_func("ldap_close"); ?></td>
        </tr>
        <tr>
          <td width="32%">MCrypt加密处理</td>
          <td width="18%"><?php echo is_func("mcrypt_cbc"); ?></td>
          <td width="32%">哈稀计算</td>
          <td width="18%"><?php echo is_func("mhash_count"); ?></td>
        </tr>
      </table>
    </div>
    <div class="table" style="display:none;">
      <div class="title"><a href="#top" title="返回顶部" class="more">55</a><span>8</span>第三方组件</div>
      <table class="content">
        <tr>
          <td width="32%">Zend版本</td>
          <td width="18%"><?php $zend_version = zend_version(); echo (empty($zend_version) ? NO : $zend_version); ?></td>
          <td width="32%"><?php $PHP_VERSION = substr(PHP_VERSION, 2, 1); echo ($PHP_VERSION > 2 ? 'ZendGuardLoader[启用]' : 'Zend Optimizer'); ?></td>
          <td width="18%"><?php
if($PHP_VERSION > 2) {
  echo (get_cfg_var("zend_loader.enable")) ? YES : NO;
} else{
  if(function_exists('zend_optimizer_version')) {
    echo zend_optimizer_version();
  } else{
    echo (get_cfg_var("zend_optimizer.optimization_level") || get_cfg_var("zend_extension_manager.optimizer_ts") || get_cfg_var("zend.ze1_compatibility_mode") || get_cfg_var("zend_extension_ts")) ? YES : NO;
  }
}
?></td>
          </td>
        </tr>
        <tr>
          <td width="32%">eAccelerator</td>
          <td width="18%"><?php echo (phpversion('eAccelerator') != '') ? phpversion('eAccelerator') : NO; ?></td>
          <td width="32%">ioncube</td>
          <td width="18%"><?php echo (extension_loaded('ionCube Loader') ? ionCube_Loader_version().'.'.(int)substr($ys, 3, 2) : NO ); ?></td>
        </tr>
        <tr>
          <td width="32%">XCache</td>
          <td width="18%"><?php echo ((phpversion('XCache')) != '' ? phpversion('XCache') : NO); ?></td>
          <td width="32%">APC</td>
          <td width="18%"><?php echo ((phpversion('APC')) != '' ? phpversion('APC') : NO); ?></td>
        </tr>
      </table>
    </div>
    <div class="table">
      <div class="title"><a href="#top" title="返回顶部" class="more">55</a><span>8</span>缓存组件</div>
      <table class="content">
        <tr>
          <td width="32%">Memcache</td>
          <td width="18%"><?php echo ((phpversion('Memcache')) != '' ? phpversion('Memcache') : NO); ?></td>
          <td width="32%">Redis</td>
          <td width="18%"><?php echo ((phpversion('Redis')) != '' ? phpversion('Redis') : NO); ?></td>
        </tr>
      </table>
    </div>
    <div class="table tabing">
      <div class="title"><a href="#top" title="返回顶部" class="more">55</a><span>8</span>数据库支持</div>
      <table class="content">
        <tr>
          <td width="32%">MySQL</td>
          <td width="18%"><?php
echo is_func("mysql_close");
if(function_exists("mysql_get_server_info")) {
  $s = @mysql_get_server_info(); //@mysql_get_client_info();
  echo ($s ? ' 版本：'.$s : '');
}
?></td>
          <td width="32%">ODBC</td>
          <td width="18%"><?php echo is_func("odbc_close"); ?></td>
        </tr>
        <tr>
          <td width="32%">Oracle</td>
          <td width="18%"><?php echo is_func("ora_close"); ?></td>
          <td width="32%">SQL Server</td>
          <td width="18%"><?php echo is_func("mssql_close"); ?></td>
        </tr>
        <tr>
          <td width="32%">dBASE</td>
          <td width="18%"><?php echo is_func("dbase_close"); ?></td>
          <td width="32%">mSQL</td>
          <td width="18%"><?php echo is_func("msql_close"); ?></td>
        </tr>
        <tr>
          <td width="32%">SQLite</td>
          <td width="18%"><?php
echo is_func("sqlite_close");
if(function_exists('sqlite_close')) {
  echo " 版本：".@sqlite_libversion();
}
?></td>
          <td width="32%">Hyperwave</td>
          <td width="18%"><?php echo is_func("hw_close"); ?></td>
        </tr>
        <tr>
          <td width="32%">Postgre SQL</td>
          <td width="18%"><?php echo is_func("pg_close"); ?></td>
          <td width="32%">Informix</td>
          <td width="18%"><?php echo is_func("ifx_close"); ?></td>
        </tr>
        <tr>
          <td width="32%">DBA</td>
          <td width="18%"><?php echo is_func("dba_close"); ?></td>
          <td width="32%">DBM</td>
          <td width="18%"><?php echo is_func("dbmclose"); ?></td>
        </tr>
        <tr>
          <td width="32%">MongoDB</td>
          <td width="18%"><?php echo ((phpversion('Mongo')) != '' ? phpversion('Mongo') : NO); ?></td>
          <td width="32%">&nbsp;</td>
          <td width="18%">&nbsp;</td>
        </tr>
      </table>
    </div>
    <div class="table tabing" style="display:none;">
      <div class="title"><a href="#top" title="返回顶部" class="more">55</a><span>8</span>服务器性能测试</div>
      <table class="content">
        <tr>
          <td width="19%" class="t-c">参照对象</td>
          <td width="17%" class="t-c">整数运算能力测试<br />(1+1运算300万次)</td>
          <td width="17%" class="t-c">浮点运算能力测试<br />(圆周率开平方300万次)</td>
          <td width="17%" class="t-c">数据I/O能力测试<br />(读取10K文件1万次)</td>
          <td>CPU信息</a></td>
        </tr>
        <tr>
          <td>作者的笔记本</td>
          <td class="t-c">0.335秒</td>
          <td class="t-c">0.619秒</td>
          <td class="t-c">0.064秒</td>
          <td>2 x Core i5-2410M @ 2.23GHz</td>
        </tr>
        <tr>
          <td>作者的服务器</td>
          <td class="t-c">0.203秒</td>
          <td class="t-c">0.532秒</td>
          <td class="t-c">0.047秒</td>
          <td>2 x Core E5400 @ 2.70GHz</td>
        </tr>
        <tr>
          <td>新浪APP引擎</td>
          <td class="t-c">0.237秒</td>
          <td class="t-c">0.564秒</td>
          <td class="t-c">0.029秒</td>
          <td>未知</td>
        </tr>
        <tr>
          <td>当前机器</td>
          <td class="servertest-btn t-c"><span class="f-red">未测试</span><br /><input type="button" value="测试" class="btn" /></td>
          <td class="servertest-btn t-c"><span class="f-red">未测试</span><br /><input type="button" value="测试" class="btn" /></td>
          <td class="servertest-btn t-c"><span class="f-red">未测试</span><br /><input type="button" value="测试" class="btn" /></td>
          <td><?php echo $sys_info['cpu']['model']; ?></td>
        </tr>
      </table>
    </div>
    <div class="table">
      <div class="title"><a href="#top" title="返回顶部" class="more">55</a><span>8</span>自定义测试</div>
      <table class="content">
        <tr>
          <form action="<?php echo $php_url; ?>#bottom" method="post">
          <input type="hidden" name="act" value="mysql_connect" />
          <td width="19%">MySQL连接测试</td>
          <td class="form"><input type="text" name="host" value="localhost" class="q" style="width: 78px;" /> <input type="text" name="port" value="3306" class="q" style="width: 40px;" /> <input type="text" name="user" value="用户名" class="q" style="width: 70px;" /> <input type="text" name="password" value="密码" class="q" style="width: 70px;" /> <input type="submit" value="测试" class="btn" /></td>
          <td width="22%"><?php echo (empty($mysql_connect_result) ? '&nbsp;' : $mysql_connect_result); ?></td>
          </form>
        </tr>
        <tr style="display:none;">
          <td width="19%">邮件发送测试</td>
          <td class="form"><input type="text" value="输入邮件地址" class="q" /> <input type="button" value="测试" class="btn" id="sendmailButton" /></td>
          <td width="22%">&nbsp;</td>
        </tr>
        <tr>
          <td width="19%">函数支持检测</td>
          <td class="form"><input type="text" value="输入函数名称" class="q" /> <input type="button" value="测试" class="btn" id="functionCheckButton" /></td>
          <td width="22%">&nbsp;</td>
        </tr>
        <tr>
          <td width="19%">PHP配置参数检测</td>
          <td class="form"><input type="text" value="输入参数名称" class="q" /> <input type="button" value="测试" class="btn" id="configCheckButton" /></td>
          <td width="22%">&nbsp;</td>
        </tr>
      </table>
    </div>
    <div class="table" style="display:none;">
      <div class="title"><a href="#top" title="返回顶部" class="more">55</a><span>8</span>下载探针</div>
      <table class="content">
        <tr>
          <td width="19%">官方下载</td>
          <td class="form"><a href="http://myprober.sinaapp.com/MyProber.zip" target="_blank">点此下载</a></td>
        </tr>
      </table>
    </div>
  </div>
  <div class="footer" style="display:none;">
    <div class="copy">&copy;2010 <span>Powered by</span> <a href="http://weibo.com/guojikai" target="_blank">Guojikai</a> | Processed in <?php echo sprintf('%0.4f', get_microtime_float() - $time_start); ?> seconds. <?php echo memory_usage(); ?> memory usage.</div>
    <div class="link">
      Valid <a href="http://validator.w3.org/check?uri=referer">XHTML</a>&nbsp;&nbsp;Valid <a href="http://jigsaw.w3.org/css-validator/validator?uri=<?php echo $php_url; ?>">CSS</a>
    </div>
  </div>
</div>
</body>
</html>