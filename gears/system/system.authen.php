<?php
if(!defined("ROOT"))DIE("DENIED");
function SystemAuthenSuper($role){
	return strtolower($role)=="admin";
}
function SystemAuthenSaltps($salt,$ps){
	return sha1(md5($salt).md5($ps));
}
function SystemAuthenRoster(){
	$roster=array();
	foreach(glob(ROOT."/data/authen/*.json") as $filename){
		$role=basename($filename,".json");
		$roster[$role]=J(C($filename),true);
	}
	return $roster;
}
function SystemAuthenAccount($role,$data=null){
	$filename=ROOT."/data/authen/".strtolower($role).".json";
	return (func_num_args()>1)?C($filename,$data===null?$data:J($data)):J(C($filename),true);
}
function SystemAuthenAccountDelete($role){
	$filename=ROOT."/data/authen/".strtolower($role).".json";
	return rename($filename,$filename.".".time().".deleted");
}