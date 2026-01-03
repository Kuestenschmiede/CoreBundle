<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @author con4gis contributors (see "authors.md")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2026, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */
namespace con4gis\CoreBundle\Classes;

use Contao\Input;
use Contao\Environment;
use Contao\System;
use Contao\CoreBundle\Session;
use Contao\Config;

class C4GFileUpload
{
    public function generate()
    {
        $hasFrontendUser = System::getContainer()->get('contao.security.token_checker')->hasFrontendUser();
        try {
            define('TL_MODE', 'FE');
            define('TL_SCRIPT', 'SOMETHING');
            $initialize = $_SERVER['DOCUMENT_ROOT'] . '/../system/initialize.php';
            if (!file_exists($initialize)) {
                $initialize = '../system/initialize.php';
            }
            // Initialize the system
            require_once($initialize);
            // User not logged in...
            if (!$hasFrontendUser) {
                header('HTTP/1.0 403 Forbidden');

                return 'Forbidden';
            }
            System::loadLanguageFile('default');
            // xss cleanup
            $_FILES = Input::xssClean($_FILES);
            $rootDir = System::getContainer()->getParameter('kernel.project_dir');
            $sServerName = Environment::get('serverName');
            $sRequestUri = Environment::get('requestUri');
            $sHttps = Environment::get('https');
            $path = Environment::get('path');
            $sConfigUploadPath = Session::getInstance()->get('con4gisFileUploadPath');
            $sConfigUploadPath = Input::xssClean($sConfigUploadPath);
            $sSubfolder = date('Y-m-d');
            //if not configured, use fallbackpath
            if (empty($sConfigUploadPath)) {
                $sUploadDir = '/files/uploads/';
            } else {
                $sUploadDir = '/' . $sConfigUploadPath;
            }
            // add subfolder
            $sUploadDir = $sUploadDir . $sSubfolder;
            // create if not exist
            if (!is_dir($rootDir . '/' . $sUploadDir)) {
                mkdir($rootDir . '/' . $sUploadDir, 0777, true);
            }
            $sValidFileTypes = Session::getInstance()->get('c4g_forum_bbcodes_editor_uploadTypes');
            $sMaxFileSize = Session::getInstance()->get('c4g_forum_bbcodes_editor_maxFileSize');
            if (empty($sValidFileTypes)) {
                // get system-configured allowed filetypes
                $sValidFileTypes = Config::get('uploadTypes');
            }
            if (empty($sMaxFileSize)) {
                // get system-configured max filesize
                $sMaxFileSize = Config::get('maxFileSize');
            }
            $sValidFileTypes = Input::xssClean($sValidFileTypes);
            $sMaxFileSize = Input::xssClean($sMaxFileSize);
            //config array
            $aConfig = [
                'maxsize' => intval($sMaxFileSize),          // maximum file size, in KiloBytes (2 MB)
                'type' => explode(',', strtoupper($sValidFileTypes)),        // allowed extensions
            ];
            $sReturn = '';
            $CKEditorFuncNum = Input::get('CKEditorFuncNum');

            if (!empty($_FILES['upload']) && empty($_FILES['uploadFile'])) {
                $_FILES['uploadFile'] = $_FILES['upload'];
            }

            if (!empty($_FILES['uploadFile']) && strlen($_FILES['uploadFile']['name']) > 1 && !empty($_FILES['uploadFile']['tmp_name'])) {
                $aInfo = pathinfo($_FILES['uploadFile']['name']);
                $sUploadDir = trim($sUploadDir, '/') . '/';
                // test
                $sUploadDir = str_replace('web/', '', $sUploadDir);
                $sUploadDir = str_replace('public/', '', $sUploadDir);
                $sFileName = basename($_FILES['uploadFile']['name']);
                $sUniqFileName = md5(uniqid('', true)) . '.' . $aInfo['extension'];
                // get protocol and host name to send the absolute image path to CKEditor
                $sProtocol = !empty($sHttps) ? 'https://' : 'http://';
                $sSite = $sProtocol . $sServerName . $path . '/con4gis/api/deliver?file=';
                // build file path
                $sUploadpath = $rootDir . '/' . $sUploadDir . $sUniqFileName;       // full file path
                $sExtension = pathinfo($_FILES['uploadFile']['name']);
                $sType = $sExtension['extension'];       // gets extension
                // Checks if the file has allowed type, size, width and height (for images)
                if (!in_array(strtoupper($sType), $aConfig['type'])) {
                    $sError = sprintf($GLOBALS['TL_LANG']['MSC']['C4G_ERROR']['file_upload_invalid_extension'], $_FILES['uploadFile']['name']);
                } elseif ($_FILES['uploadFile']['size'] > $aConfig['maxsize']) {
                    $sError = sprintf($GLOBALS['TL_LANG']['MSC']['C4G_ERROR']['file_upload_invalid_size'], ($sMaxFileSize / 1024));
                }
                $sFileHash = md5($sUniqFileName . $GLOBALS['TL_CONFIG']['encryptionKey'] . $sFileName);
                // If no errors, upload the image, else, output the errors
                if ($sError == '') {
                    if (move_uploaded_file($_FILES['uploadFile']['tmp_name'], $sUploadpath)) {
                        $url = $sSite . $sUploadDir . $sFileName . '&u=' . $sUniqFileName . '&c=' . $sFileHash;
                        $message = sprintf($GLOBALS['TL_LANG']['MSC']['C4G_ERROR']['file_upload_successful'], $sFileName, number_format($_FILES['uploadFile']['size'] / 1024, 3, '.', ''));
                        $sReturn = "window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$url', '$message')";
                    } else {
                        $message = $GLOBALS['TL_LANG']['MSC']['C4G_ERROR']['file_upload_error'];
                        $sReturn = "window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '', '$message')";
                    }
                } else {
                    $sReturn = "window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '', '$sError')";
                }
            } else {
                if (!empty($_FILES['uploadFile']['name'])) {
                    $sExtension = pathinfo($_FILES['uploadFile']['name']);
                    $sType = $sExtension['extension'];       // gets extension
                    if (!in_array($sType, $aConfig['type'])) {
                        $message = sprintf($GLOBALS['TL_LANG']['MSC']['C4G_ERROR']['file_upload_invalid_extension'], $_FILES['uploadFile']['name']);
                    }
                }
                if (!empty($_FILES['uploadFile']['size'])) {
                    // Checks if the file has allowed type, size, width and height (for images)
                    if ($_FILES['uploadFile']['size'] > $aConfig['maxsize']) {
                        $message = sprintf($GLOBALS['TL_LANG']['MSC']['C4G_ERROR']['file_upload_invalid_size'], ($sMaxFileSize / 1024));
                    }
                }
                if (empty($message)) {
                    $message = $GLOBALS['TL_LANG']['MSC']['C4G_ERROR']['file_upload_error'];
                }
                $sReturn = "window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '', '$message')";
            }
        } catch (Exception $e) {
            $sReturn = "window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '', '" . $GLOBALS['TL_LANG']['MSC']['C4G_ERROR']['exception_' . $e->getCode()] . "')";
        }
        $sReturn = "<script>$sReturn;</script>";

        return ['data' => $sReturn, 'type' => 'Document'];
    }
}
