<?php
if((!defined("ROOT"))||(!S("SystemAuthen")))die("DENIED");
class BirdForm{

    public $configname;
    public $configfile;
    public $structure;
    public $stylefile;

    public function __construct(){
        $this->stylefile=U("|bird/form/style.default.php");
        $this->configname=I(V($_GET["config"]),"main",TRUE);
        $this->configfile=U("|data/configs/".I(V($_GET["file"]),$this->configname,TRUE).".json");
        include U("|gears/configs/{$this->configname}.php");
        $this->structure=$config;
    }

    public function BuildForm(){
        return $this->structure;
    }

    public function BreakForm($data){
        return false;
    }

    public function GetData(){
        $data=J(C($this->configfile),true);
        return is_array($data)?$data:array();
    }

    public function SetData($data){
        return C($this->configfile,J($data));
    }
    
}