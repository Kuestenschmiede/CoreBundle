<?php

namespace con4gis\CoreBundle\Resources\contao\classes;

class C4GDeliverFileApi {

    public function generate() {
        define("TL_MODE", "FE");
//        $sRootPath = dirname($_SERVER['SCRIPT_FILENAME']) . "/../../../../";
//        require_once($sRootPath . "system/initialize.php");

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


        if (class_exists('con4gis\ApiBundle\Controller\ApiController') &&  (version_compare( VERSION, '4', '>=' ))) {
            $sFileHashGenerated = md5($aUriVars['u'] . $GLOBALS['TL_CONFIG']['encryptionKey'] . basename($aUriVars[$path.'/con4gis/api/deliver?file']));
        } else {
            $sFileHashGenerated = md5($aUriVars['u'] . $GLOBALS['TL_CONFIG']['encryptionKey'] . basename($aUriVars[$path.'/bundles/con4giscore/vendor/deliver_php?file']));
        }
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
        return array('data' => file_get_contents(TL_ROOT . "/" . $sFilePath), 'type' => 'application/octet-stream');
    }
}