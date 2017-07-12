<? require ("system.php");
$_ROUTER = R();
 
if (!count($_ROUTER)) $_ROUTER[] = "intro";
$_CACHEMETA = array("switch" => CACHEXPIRE && ($_SERVER["REQUEST_METHOD"] == "GET"), "expire" => time() + CACHEXPIRE, "hash" => md5(implode("/", $_ROUTER)), "header" => array());
$_HEADER = & $_CACHEMETA["header"];
$_CACHEFILE = ROOT . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "caches" . DIRECTORY_SEPARATOR . $_CACHEMETA["hash"] . ".html";


if (is_file($_CACHEFILE)) {
    $F = @fopen($_CACHEFILE, "r");
    if ($F) {
        $_CACHEDMETA = unserialize(fread($F, intval(trim(fread($F, 20)))));
        if (($_CACHEDMETA) && (time() < $_CACHEDMETA["expire"])) {
            foreach ($_CACHEDMETA["header"] as $header) header($header);
            while (!feof($F)) echo fread($F, 3600);
            fclose($F);
            die();
        } else {
            fclose($F);
            unlink($_CACHEFILE);
        }
    }
}
if (is_file(ROOT . DIRECTORY_SEPARATOR . "router" . DIRECTORY_SEPARATOR . $_ROUTER[0] . ".php")) {
    ob_start();
    require (ROOT . DIRECTORY_SEPARATOR . "router" . DIRECTORY_SEPARATOR . $_ROUTER[0] . ".php");
    foreach ($_HEADER as $header) header($header);
    if ($_CACHEMETA["switch"]) {
        $_CACHEMETA = serialize($_CACHEMETA);
        $_CACHEMETASIZE = strlen($_CACHEMETA);
        $_CACHEMETASIZE.= str_repeat(" ", 20 - strlen($_CACHEMETASIZE));
        C($_CACHEFILE, $_CACHEMETASIZE . $_CACHEMETA . ob_get_contents());
    }
    ob_end_flush();
} else {
    header("Expires: Mon, 11 Jul 1991 00:00:00 GMT");
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache");
    header("Location: /404/");
} ?>