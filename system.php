<?php
if (!defined("ROOT")) {
    define("ROOT", dirname(__FILE__));
    function C($name) {
        if (!is_string($name)) return $name ? (ob_start() ? "" : "") : ob_get_contents() . (ob_end_clean() ? "" : "");
        return (func_num_args() == 1) ? (is_file($name) ? file_get_contents($name) : "") : ((($data = func_get_arg(1)) === null) ? unlink($name) : file_put_contents($name, $data));
    }
    function F($f = null, $n = array()) {
        if (is_array($f)) {
            $n = $f;
            $f = null;
        }
        if (!is_array($n)) $n = explode("|", $n);
        $f = $f ? $f : array_shift($n);
        if (!$f) return null;
        $f = is_array($f) ? $f : explode(".", $f, 2);
        if (count($f) == 1) {
            $c = null;
            $f = array_shift($f);
            if (!function_exists($f)) return null;
        } else {
            $c = array_shift($f);
            if (!class_exists($c)) return null;
            $f = array_shift($f);
            $c = new $c();
            if (!method_exists($c, $f)) return null;
        }
        return call_user_func_array($c ? array($c, $f) : $f, $n);
    }
    function G($url = "/", $msg = null) {
        if (is_string($msg)) return "<meta charset=\"utf-8\" /><script type=\"text/javascript\">" . ($msg ? "alert(\"" . addslashes($msg) . "\");" : "") . "window.location=\"" . addslashes($url) . "\";</script>";
        header("location: " . $url);
        die(0);
    }
    function H($s, $hex = false) {
        if (!$hex) return htmlspecialchars($s);
        $h = "";
        for ($i = 0;$i < strlen($s);$i++) $h.= "\\x" . (ord($s[$i]) > 16 ? "" : "0") . dechex(ord($s[$i]));
        return $h;
    }
    function I($name, $default = "", $valued = false) {
        $value = $valued ? V($name, $default) : V($_REQUEST[$name], $default);
        if (is_array($value)) $value = implode("|", array_filter($value));
        return trim(get_magic_quotes_gpc() ? stripslashes($value) : $value);
    }
    function J($s, $d = null) {
        return $d === null ? json_encode($s) : json_decode($s, $d);
    }
    function L($file, $log) {
        return ($f = @fopen($file, "a")) ? (fwrite($f, (is_array($log) ? json_encode($log) : $log) . "\r\n")) : false;
    }
    function M() {
        static $DBC;
        if (!$DBC) ($db = parse_url(DATABASE)) && ((($DBC = mysql_connect($db["host"], $db["user"], $db["pass"])) && (mysql_select_db(trim($db["path"], "/"), $DBC)) && (mysql_query("SET NAMES 'UTF8'", $DBC)) && (mysql_query("SET @@sql_mode=''", $DBC))) or die(mysql_error()));
        $A = func_get_args();
        $C = count($A);
        switch ($C) {
            case 0:
                return $DBC;
            break;
            case 1:
                $result = mysql_query($A[0], $DBC) or die(mysql_error());
                return $result;
            break;
        }
        $w = $C > 2 ? $A[2] : "";
        if (is_array($A[1])) {
            $a = array();
            foreach ($A[1] as $k => $v) $a[] = "`" . $k . "`='" . addslashes($v) . "'";
            return M(($w ? "UPDATE" : "INSERT INTO") . " `{$A[0]}` " . ($a ? "SET " . implode(",", $a) . " " : "") . ($w ? "WHERE " . $w : "")) ? ($w ? mysql_affected_rows($DBC) : mysql_insert_id($DBC)) : 0;
        } elseif (is_null($A[1])) {
            return M("DELETE FROM `{$A[0]}` " . ($w ? "WHERE " . $w : "")) ? mysql_affected_rows($DBC) : 0;
        }
        $r = array();
        if ($C < 3) {
            $q = M("SELECT * FROM `{$A[0]}` " . ($A[1] ? "WHERE " . $A[1] : ""));
            while ($i = mysql_fetch_assoc($q)) $r[] = $i;
            mysql_free_result($q);
            return $r;
        }
        if ($C > 3) {
            $c = max($C > 4 ? intval($A[4]) : 20, 1);
            $t = M($A[0], $A[1], 0);
            $m = max(ceil($t / $c), 1);
            $p = max(min(intval($A[3]), $m), 1);
            $l = 5;
            $n = array_unique(array_merge(range(1, min($l, $m)), range(max($p - floor($l / 2), 1), min($p + floor($l / 2), $m)), range(max(($m - $l + 1), 1), $m)));
            $r[] = array("page" => $p, "count" => $c, "total" => $t, "maxpage" => $m, "pn" => $n);
            $q = M("SELECT * FROM `{$A[0]}`" . ($A[1] ? " WHERE " . $A[1] : "") . ($w ? " ORDER BY " . $w : "") . " LIMIT " . ($p - 1) * $c . "," . $c);
            while ($i = mysql_fetch_assoc($q)) $r[] = $i;
            mysql_free_result($q);
            return $r;
        }
        $q = M("SELECT " . ($w ? "*" : "COUNT(*)") . " FROM `{$A[0]}` " . ($A[1] ? "WHERE " . $A[1] : ""));
        $r = mysql_num_rows($q) ? ($w ? mysql_fetch_assoc($q) : @array_shift(mysql_fetch_row($q))) : ($w ? array() : 0);
        mysql_free_result($q);
        return $r;
    }
    function O() {
        static $O;
        $O++;
        global $_GLOBAL;
        if (!is_array($_GLOBAL)) $_GLOBAL = array();
        $_PARAM = func_get_args();
        $_FILE = U(array_shift($_PARAM));
        if (count($_PARAM) === (int)is_array($_PARAM[0])) $_PARAM = array_shift($_PARAM);
        if (is_file($_FILE)) include $_FILE;
    }
    function R($name = "", $default = "") {
        if ($name) {
            if (strpos($name, ".")) {
                $file = ROOT . "/market/{$name}";
                return is_file($file) ? ((include_once ($file)) ? true : false) : false;
            }
            $name = strtoupper($name);
            if ($name == "IP") return W(R("HTTP_CLIENT_IP"), R("HTTP_X_FORWARDED_FOR"), R("REMOTE_ADDR"), $default);
            if ($name == "MOBILE") return preg_match("/(android|mobile|iphone)/i", R("UA"));
            if ($name == "DOMAIN") return W(defined("DOMAIN") ? constant("DOMAIN") : R("HTTP_HOST"), $default);
            if ($name == "ROUTER") return W(defined("ROUTER") ? constant("ROUTER") : (is_file(ROOT . "/.htaccess") ? "/" : "/?/"), $default);
            if ($name == "UA") $name = "HTTP_USER_AGENT";
            return V($_SERVER[$name], $default);
        }
        $u = $_SERVER["REQUEST_URI"];
        $u = J($u) == "null" ? iconv("GBK", "UTF-8", $u) : $u;
        $s = $_SERVER["SCRIPT_NAME"];
        if (strpos($u, $s) === 0) $u = substr($u, strlen($s));
        $i = strpos($u, "?");
        if ($i !== false) $u = substr($u, $i + 1);
        $u = trim(urldecode($u), "/");
	 if($u=='jd'){
		 $u='';
		 }
		 
        return $u ? explode("/", $u) : array();
    }
    function &S() {
        if (!isset($_SESSION)) session_start();
        $A = func_get_args();
        switch (count($A)) {
            case 1:
                if (!isset($_SESSION[$A[0]])) $_SESSION[$A[0]] = "";
                return $_SESSION[$A[0]];
                break;
            case 2:
                $_SESSION[$A[0]] = $A[1];
                if ($A[1] === null) {
                    unset($_SESSION[$A[0]]);
                    return $A[1];
                }
                return $_SESSION[$A[0]];
                break;
            default:
                return $_SESSION;
            }
    }
    function T($time, $format = "Y-m-d H:i:s", $zone = 8) {
        if (is_string($format)) return gmdate($format, $time + ($zone * 3600));
        $t = array_filter(explode(" ", trim(str_replace(array("-", "/", "\\", ".", ",", ":", ";"), " ", $time)) . " 00 00 00 00 00 00"));
        return gmmktime($t[3], $t[4], $t[5], $t[1], $t[2], $t[0]) - $zone * 3600;
    }
    function U($n = array(), $u = null) {
        $u = $u === null ? $_SERVER["REQUEST_URI"] : $u;
        $p = array();
        $i = strpos($u, "?");
        $b = $i === false ? $u : substr($u, 0, $i) . (parse_str(substr($u, $i + 1), $p) ? "" : "");
        if (is_array($n)) return $b . "?" . http_build_query(array_merge($p, $n));
        $F = str_replace("\\", "/", $n);
        switch (substr($F, 0, 1)) {
            case "|":
                $F = ROOT . "/" . substr($F, 1);
            break;
            case "!":
                $F = dirname($_SERVER["SCRIPT_FILENAME"]) . "/" . substr($F, 1);
            break;
            case "@":
                $F = "http://" . R("DOMAIN") . substr($F, 1);
            break;
            case "~":
			     $F = R("ROUTER").'?'. ltrim(substr($F, 1), "/");
            break;
            case "#":
                $F = ROOT . "/output/" . substr($F, 1);
            break;
            case "?":
                $F = $b . "?" . substr($F, 1);
            break;
            case "&":
                $n = array();
                parse_str(substr($F, 1), $n);
                $F = $b . "?" . http_build_query(array_merge($p, $n));
            break;
        }
        return $F;
    }
    function V(&$value, $default = "", $judge = true) {
        return isset($value) ? ($judge ? ($value ? $value : $default) : $value) : $default;
    }
    function W() {
        foreach (func_get_args() as $param) if ($param) return $param;
        return null;
    }
    include (ROOT . "/system.cfg.php");
}
