<?php
if(!defined("ROOT"))DIE("DENIED");
$d = array();
$f = array();
$ok = array();
$dirs = array('data/');
while (count($dirs)) {
    $dir = array_shift($dirs);
    $full = ROOT . '/' . $dir;
    if (!is_writable($full)) {
        chmod($full, 777);
    }
    if (!is_writable($full)) {
        $d[] = $dir;
        continue;
    }
    if (is_writable($full)) {
    	$ok[] = $dir;
    }
    $handle = opendir(ROOT . '/' . $dir);
    while (false !== ($file = readdir($handle))) {
        if ($file == '.' || $file == '..' || $file == '') {
            continue;
        }
        $full = ROOT . '/' . $dir . $file;
        if (is_file($full)) {
            if (!is_writable($full)) {
                chmod($full, 777);
            }
            if (!is_writable($full)) {
                $f[] = $dir . $file;
                continue;
            }
        } elseif (is_dir($full)) {
            $dirs[] = $dir . $file . '/';
        }
    }
    closedir($handle);
}
?>
<?=$HTMLBOF?>
<link rel="stylesheet" type="text/css" href="gears/jquery/bootstrap/css/bootstrap.min.css">
<script type="text/javascript" src="gears/jquery/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="gears/jquery/bootstrap/js/bootstrap.min.js"></script>
<style type="text/css">
html,body{margin:0;padding:0px;}
body{padding:20px;}
</style>
<?=$BODYBOF?>

<?php if(count($d)){ ?>
	<div class="alert alert-danger" role="alert"><strong>权限检测：</strong>文件夹权限异常，部分文件夹无法正常写入，请通过FTP工具设置以下文件夹及子项的属性为 <strong>777</strong> 可写状态.</div>
	<div class="panel panel-danger">
	  <div class="panel-heading">权限异常的文件夹列表</div>
	  <div class="panel-body">
	  <textarea class="form-control" rows="15" style="border-color:#ebccd1;background-color:#f2dede;color:#a94442;"><?=implode("\r\n",$d)?></textarea>
	  </div>
	</div>
	<div style="margin-bottom:20px;"><a class="btn btn-danger" href="?to=config&at=database" style="padding-left: 22px;padding-right: 22px;">忽略以上异常并开始配置数据库</a>&nbsp;&nbsp;<span style="font-size:12px;color:#888;">点击前往配置网站数据库连接信息.</span></div>
<?php }elseif(count($f)){ ?>
	<div class="alert alert-warning" role="alert"><strong>权限检测：</strong>文件夹权限正常，但文件夹所包含的部分文件无法正常写入，请通过FTP工具设置以下文件的属性为 <strong>777</strong> 可写状态.</div>
	<div class="panel panel-warning">
	  <div class="panel-heading">权限异常的文件列表</div>
	  <div class="panel-body">
	  <textarea class="form-control" rows="15" style="border-color:#faebcc;background-color:#fcf8e3;color:#8a6d3b;"><?=implode("\r\n",$f)?></textarea>
	  </div>
	</div>
	<div style="margin-bottom:20px;"><a class="btn btn-warning" href="?to=config&at=database" style="padding-left: 22px;padding-right: 22px;">忽略以上文件并开始配置数据库</a>&nbsp;&nbsp;<span style="font-size:12px;color:#888;">点击前往配置网站数据库连接信息.</span></div>
<?php }else{ ?>
	<div class="alert alert-success" role="alert"><strong>权限检测：</strong>文件夹权限正常，网站运行目录可以正常读写.</div>
	<div class="panel panel-success">
	  <div class="panel-heading">网站运行目录权限正常</div>
	  <div class="panel-body">
	  <textarea class="form-control" rows="15" style="border-color:#d6e9c6;background-color:#dff0d8;color:#3c763d;"><?=implode("\r\n",$ok)?></textarea>
	  </div>
	</div>
	<div style="margin-bottom:20px;"><a class="btn btn-success" href="?to=config&at=database" style="padding-left: 22px;padding-right: 22px;">开始配置数据库</a>&nbsp;&nbsp;<span style="font-size:12px;color:#888;">点击前往配置网站数据库连接信息.</span></div>
<?php } ?>

<?=$BODYEOF?>
<?=$HTMLEOF?>