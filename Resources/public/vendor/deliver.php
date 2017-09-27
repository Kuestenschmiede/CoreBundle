<?php
define("TL_MODE", "FE");
define("TL_SCRIPT", "SOMETHING");
//    $sRootPath = dirname($_SERVER['SCRIPT_FILENAME']) . "/../../../../../";
//    require_once($sRootPath . "system/initialize.php");
$initialize = $_SERVER["DOCUMENT_ROOT"] . '/../../system/initialize.php';
if (!file_exists($initialize)) {
    $initialize = '../../../system/initialize.php';
}
// Initialize the system
require_once($initialize);
// grab SERVER and GET-vars
$sFilePath     = \Input::get("file");
$sUniqFileName = \Input::get("u");
$sFileHash     = \Input::get("c");
$sServerName   = \Environment::get("serverName");
$sRequestUri   = \Environment::get("requestUri");
$sHttps        = \Environment::get("https");
$path      = \Environment::get("path");
$aInfo     = pathinfo($sFilePath);
$sFilePath = str_replace($aInfo['basename'], "", $sFilePath) . $sUniqFileName;
// User not logged in...
if (!FE_USER_LOGGED_IN) {
    header("HTTP/1.0 404 Not Found");
    die();
}
// Filepath missing...
if (empty($sFilePath)) {
    header("HTTP/1.0 404 Not Found");
    die();
}
// Hash missing
if (empty($sFileHash)) {
    header("HTTP/1.0 404 Not Found");
    die();
}
// file does not exist
if (!file_exists(TL_ROOT . "/" . $sFilePath)) {
    header("HTTP/1.0 404 Not Found");
    die();
}
// check hash
$protocol = !empty($sHttps) ? 'https://' : 'http://';
$sUrl     = $protocol . $sServerName . $sRequestUri;
// extract uri vars
parse_str($sRequestUri, $aUriVars);
$sFileHashGenerated = md5($aUriVars['u'] . $GLOBALS['TL_CONFIG']['encryptionKey'] . basename($aUriVars[$path.'bundles/con4giscore/vendor/deliver_php?file']));
if ($sFileHash !== $sFileHashGenerated) {
    header('HTTP/1.0 404 Not Found');
    die();
}
// output
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename=' . $aInfo['basename']);
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize(TL_ROOT . "/" . $sFilePath));
echo file_get_contents(TL_ROOT . "/" . $sFilePath);
die();
