<?php
FUNCTION HALT(){HEADER("HTTP/1.0 404 Not Found");EXIT(0);}HEADER("Expires: Mon, 11 Jul 1991 00:00:00 GMT");
HEADER("Cache-Control: no-cache, must-revalidate");HEADER("Pragma: no-cache");REQUIRE("system.php");
$_BIRD=TRIM(I(V($_GET["BIRD"]),"",TRUE),"/");IF(PREG_MATCH("/^[A-Za-z0-9]+$/",STR_REPLACE("/","",$_BIRD))){
$_BIRDFILE=U("|bird/".$_BIRD.".php");IF(!IS_FILE($_BIRDFILE))HALT();
$_FEEDBACK=ARRAY("bird"=>TIME(),"status"=>"","toasts"=>ARRAY(),"scripts"=>ARRAY(),"params"=>ARRAY());
FUNCTION Status($status){GLOBAL $_FEEDBACK;$_FEEDBACK["status"]=$status;}
FUNCTION Toast($toast,$delay=0,$duration=1800,$bind="$"){GLOBAL $_FEEDBACK;
$_FEEDBACK["toasts"][]=array("bind"=>$bind,"delay"=>$delay,"duration"=>$duration,"toast"=>$toast);}
FUNCTION Script($script,$delay=0){GLOBAL $_FEEDBACK;$_FEEDBACK["scripts"][]=array("delay"=>$delay,"script"=>$script);}
FUNCTION Param($key,$value){GLOBAL $_FEEDBACK;$_FEEDBACK["params"][$key]=$value;}
C(TRUE);INCLUDE($_BIRDFILE);$_FEEDBACK["html"]=C(FALSE);echo COUNT($_FILES)?H(J($_FEEDBACK)):J($_FEEDBACK);}
ELSEIF(SUBSTR($_BIRD,0,1)=="~"){REQUIRE(U("|bird/form/class.".SUBSTR($_BIRD,1).".php"));R("form.php");
$_BIRDFORM=NEW BirdForm();$_FORM=$_BIRDFORM->BuildForm();IF(I(V($_POST["FORMNAME"]),"",TRUE)=="UPDATE"){
$_DATA=FormData($_FORM);$_FORMBREAK=$_BIRDFORM->BreakForm($_DATA);IF(!$_FORMBREAK)$_BIRDFORM->SetData($_DATA);}
$_DATA=$_BIRDFORM->GetData();ARRAY_WALK($_FORM,"FormFill",$_DATA);REQUIRE($_BIRDFORM->stylefile);}ELSE{HALT();}