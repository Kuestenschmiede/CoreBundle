<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 10
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2025, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */
namespace con4gis\CoreBundle\Classes;

use Contao\Input;
use Contao\Environment;
use Contao\System;
use Contao\BackendUser;

class C4GDeliverFileApi
{
    public function generate()
    {
        define('TL_MODE', 'FE');

        // grab SERVER and GET-vars
        $sFilePath = Input::get('file');
        $sUniqFileName = Input::get('u');
        $sFileHash = Input::get('c');
        $sServerName = Environment::get('serverName');
        $sRequestUri = Environment::get('requestUri');
        $sHttps = Environment::get('https');
        $path = Environment::get('path');

        $aInfo = pathinfo($sFilePath);
        $sFilePath = str_replace($aInfo['basename'], '', $sFilePath) . $sUniqFileName;

        // User not logged in...
        $hasFrontendUser = System::getContainer()->get('contao.security.token_checker')->hasFrontendUser();
        if (!$hasFrontendUser) {
            header('HTTP/1.0 404 Not Found');
            die();
        }

        // Filepath missing...
        if (empty($sFilePath)) {
            header('HTTP/1.0 404 Not Found');
            die();
        }

        // Hash missing
        if (empty($sFileHash)) {
            header('HTTP/1.0 404 Not Found');
            die();
        }

        // file does not exist
        $rootDir = System::getContainer()->getParameter('kernel.project_dir');
        if (!file_exists($rootDir . '/' . $sFilePath)) {
            header('HTTP/1.0 404 Not Found');
            die();
        }

        // check hash
        $protocol = !empty($sHttps) ? 'https://' : 'http://';
        $sUrl = $protocol . $sServerName . $sRequestUri;

        // extract uri vars
        parse_str($sRequestUri, $aUriVars);

        if (class_exists('con4gis\ApiBundle\Controller\ApiController') && (version_compare(VERSION, '4', '>='))) {
            $sFileHashGenerated = md5($aUriVars['u'] . $GLOBALS['TL_CONFIG']['encryptionKey'] . basename($aUriVars[$path . '/con4gis/api/deliver?file']));
        } else {
            $sFileHashGenerated = md5($aUriVars['u'] . $GLOBALS['TL_CONFIG']['encryptionKey'] . basename($aUriVars[$path . '/bundles/con4giscore/vendor/deliver_php?file']));
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
        header('Content-Length: ' . filesize($rootDir . '/' . $sFilePath));

        return ['data' => file_get_contents($rootDir . '/' . $sFilePath), 'type' => 'application/octet-stream'];
    }
}
