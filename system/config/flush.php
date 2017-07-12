<?php
if(!defined("ROOT"))DIE("DENIED");
R("file.php");
$ok=FileDelete(ROOT.DIRECTORY_SEPARATOR."data".DIRECTORY_SEPARATOR."caches",false);
echo "!".($ok?"服务器时间 ".T(time(),"H:i:s")." 缓存已成功更新":"更新缓存操作失败,请检查目录权限!");