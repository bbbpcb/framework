<?php 
$MimeList=array(
"image/jpeg"=>"jpg",
"image/pjpeg"=>"jpg",
"image/x-png"=>"png",
"image/png"=>"png",
"image/gif"=>"gif",
"image/bmp"=>"bmp",
);
$OK=TRUE;
$FilePath="data/uploads/".T(TIME(),"Ymd")."/";
$FileType=STRTOLOWER(V($MimeList[$_FILES['BirdUpload']['type']],pathinfo($_FILES['BirdUpload']['name'],PATHINFO_EXTENSION)));
$FileName=$FilePath.T(TIME(),"YmdHis").rand(10000,99999).".".$FileType;
if(($OK)&&(!FILE_EXISTS(U("|".$FilePath)))){$OK=MKDIR(U("|".$FilePath),0777,TRUE);if(!$OK)Param("error","创建目录失败");}
if(($OK)&&($_FILES['BirdUpload']['size']>1048576*1024)){$OK=FALSE;Param("error","文件大小错误");}
if(($OK)&&(in_array($FileType,array("php")))){$OK=FALSE;Param("error","文件类型错误");}
if(($OK)&&(MOVE_UPLOADED_FILE($_FILES['BirdUpload']['tmp_name'],U("|".$FileName))===FALSE)){$OK=FALSE;Param("error","文件存储失败");}
Param("filename",$OK?"/".$FileName:"");
Param("filesize",$_FILES['BirdUpload']['size']);
Param("done",$OK);