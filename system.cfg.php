<?php
define("DEVMODE","DEVMODE");
define("CACHEXPIRE",0);
//define("DOMAIN","www.domain.com");
//define("ROUTER","http://www.domain.com/");
include(U("|data/configs/database.php"));
function cfg($name, $key = null, $default = null)
{
    global $_CONFIGURATIONS;
    if (!isset($_CONFIGURATIONS)) {
        $_CONFIGURATIONS = array();
    }
    if (!isset($_CONFIGURATIONS[$name])) {
        $f = U("|data/configs/{$name}.json");
        if (!is_file($f)) {
            return $default;
        }
        $_CONFIGURATIONS[$name] = J(C($f), true);
    }
    if ($key === null) {
        return $_CONFIGURATIONS[$name];
    }
    $cfg =& $_CONFIGURATIONS[$name];
    if (!is_array($cfg)) {
        return $default;
    }
    if (!array_key_exists($key, $cfg)) {
        return $default;
    }
    return V($cfg[$key], $default);
}
function css($css){
  static $CascadingStyleSheets=0;$CascadingStyleSheets++;
  return "<script type=\"text/javascript\">".($CascadingStyleSheets>1?"":"function CascadingStyleSheets(css){var style=document.createElement(\"style\");style.type=\"text/css\";if(style.styleSheet){style.styleSheet.cssText=css;}else{style.innerHTML=css;}document.getElementsByTagName(\"head\")[0].appendChild(style);}")."CascadingStyleSheets(\"".H($css,true)."\");</script>";
}