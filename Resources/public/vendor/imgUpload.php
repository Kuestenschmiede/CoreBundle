<?php
// PHP Upload Script for CKEditor:  http://coursesweb.net/
ini_set("display_errors","1");
    try {
        define("TL_MODE", "FE");
//        $sRootPath = dirname($_SERVER['SCRIPT_FILENAME']) . "/../../../../";
//        require_once($sRootPath . "system/initialize.php");
        $initialize = $_SERVER["DOCUMENT_ROOT"].'/system/initialize.php';
        if (!file_exists($initialize)) {
            $initialize = '../../../../system/initialize.php';
        }

        // Initialize the system
        require_once($initialize);

        // User not logged in...
        if (!FE_USER_LOGGED_IN) {
            header('HTTP/1.0 403 Forbidden');
            echo "Forbidden";
            die();
        }


        \System::loadLanguageFile("default");

        // xss cleanup
        $_FILES = \Input::xssClean($_FILES);

        $sServerName = \Environment::get("serverName");
        $sRequestUri = \Environment::get("requestUri");
        $sHttps      = \Environment::get("https");
        $path        = \Environment::get("path");

        $sConfigUploadPath = \Session::getInstance()->get("con4gisImageUploadPath");
        $sConfigUploadPath = \Input::xssClean($sConfigUploadPath);
        $sSubfolder        = date("Y-m-d");

        //if not configured, use fallbackpath
        if (empty($sConfigUploadPath)) {
            $sUploadPath = \Config::get("uploadPath");
            $sUploadDir  = "/" . $sUploadPath . "/uploads/";
        } else {
            $sUploadDir = "/" . $sConfigUploadPath;
        }

        // add subfolder
        $sUploadDir = $sUploadDir . $sSubfolder;


        // create if not exist
        if (!is_dir(TL_ROOT . "/" . $sUploadDir)) {
            mkdir(TL_ROOT . "/" . $sUploadDir, 0777, true);
        }

        // HERE SET THE PATH TO THE FOLDER WITH IMAGES ON YOUR SERVER (RELATIVE TO THE ROOT OF YOUR WEBSITE ON SERVER)


        $sValidFileTypes = \Session::getInstance()->get("c4g_forum_bbcodes_editor_uploadTypes");
        $sMaxFileSize    = \Session::getInstance()->get("c4g_forum_bbcodes_editor_maxFileSize");
        $sMaxImageWidth  = \Session::getInstance()->get("c4g_forum_bbcodes_editor_imageWidth");
        $sMaxImageheight = \Session::getInstance()->get("c4g_forum_bbcodes_editor_imageHeight");


        if (empty($sValidFileTypes)) {
            // get system-configured allowed filetypes
            $sValidFileTypes = \Config::get("uploadTypes");
        }
        if (empty($sMaxFileSize)) {
            // get system-configured max filesize
            $sMaxFileSize = \Config::get("maxFileSize");
        }
        if (empty($sMaxImageWidth)) {
            // get system-configured max filesize
            $sMaxImageWidth = \Config::get("imageWidth");
        }
        if (empty($sMaxImageheight)) {
            // get system-configured max filesize
            $sMaxImageheight = \Config::get("imageHeight");
        }

        $sValidFileTypes = \Input::xssClean($sValidFileTypes);
        $sMaxFileSize    = \Input::xssClean($sMaxFileSize);
        $sMaxImageWidth  = \Input::xssClean($sMaxImageWidth);
        $sMaxImageheight = \Input::xssClean($sMaxImageheight);


        // HERE PERMISSIONS FOR IMAGE
        $imgsets = array(
            'maxsize'   => intval($sMaxFileSize),          // maximum file size, in KiloBytes (2 MB)
            'maxwidth'  => intval($sMaxImageWidth),          // maximum allowed width, in pixels
            'maxheight' => intval($sMaxImageheight),         // maximum allowed height, in pixels
            'minwidth'  => 10,           // minimum allowed width, in pixels
            'minheight' => 10,          // minimum allowed height, in pixels
            'type'      => explode(",", $sValidFileTypes)        // allowed extensions
        );

        $sReturn = '';


        $CKEditorFuncNum = \Input::get('CKEditorFuncNum');
        if (!empty($_FILES['upload']) && strlen($_FILES['upload']['name']) > 1 && !empty($_FILES['upload']['tmp_name'])) {
            $sUploadDir = trim($sUploadDir, '/') . '/';
            $real_name   = basename($_FILES['upload']['name']);

            // get protocol and host name to send the absolute image path to CKEditor
            $protocol = !empty($_SERVER['HTTPS']) ? 'https://' : 'http://';
            $site     = $protocol . $_SERVER['SERVER_NAME'] . '/';

            $sExtension  = pathinfo($_FILES['upload']['name']);
            $sType       = $sExtension['extension'];       // gets extension
            $img_name   = md5(uniqid('', true)) . "." .$sType;
            $uploadpath = TL_ROOT . '/' . $sUploadDir . $img_name;       // full file path
            list($width, $height) = getimagesize($_FILES['upload']['tmp_name']);     // gets image width and height
            $err = '';         // to store the errors

            // Checks if the file has allowed type, size, width and height (for images)
            if (!in_array($sType, $imgsets['type'])) {
                $err = sprintf($GLOBALS['TL_LANG']['MSC']['C4G_ERROR']['image_upload_invalid_extension'], $_FILES['upload']['name']);
            }elseif ($_FILES['upload']['size'] > $imgsets['maxsize']) {
                $err = sprintf($GLOBALS['TL_LANG']['MSC']['C4G_ERROR']['image_upload_invalid_size'], ($sMaxFileSize / 1024));
            }elseif (isset($width) && isset($height)) {
                if ($width > $imgsets['maxwidth'] || $height > $imgsets['maxheight']) {
                    $err = sprintf($GLOBALS['TL_LANG']['MSC']['C4G_ERROR']['image_upload_invalid_dimensions'], $width, $height, $imgsets['maxwidth'], $imgsets['maxheight']);
                }elseif ($width < $imgsets['minwidth'] || $height < $imgsets['minheight']) {
                    $err = sprintf($GLOBALS['TL_LANG']['MSC']['C4G_ERROR']['image_upload_invalid_dimensions'], $width, $height, $imgsets['maxwidth'], $imgsets['maxheight']);
                }
            }


            if(!empty($path)){
                $path = substr($path,1)."/";
            }else{
                $path = "";
            }
            // If no errors, upload the image, else, output the errors
            if ($err == '') {
                if (move_uploaded_file($_FILES['upload']['tmp_name'], $uploadpath)) {
                    $url     = $site . $path.$sUploadDir . $img_name;
                    $message = sprintf($GLOBALS['TL_LANG']['MSC']['C4G_ERROR']['image_upload_successful'], $real_name, number_format($_FILES['upload']['size'] / 1024, 3, '.', ''), $width, $height);
                    $sReturn = "window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$url', '$message')";
                } else {
                    $sReturn = "window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '', '" . $GLOBALS['TL_LANG']['MSC']['C4G_ERROR']['image_upload_error'] . "')";
                }
            } else {
                $sReturn = "window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '', '" . $err . "')";
            }
        } else {
            $message = "";

            if (!empty($_FILES['upload']['size'])) {
                if ($_FILES['upload']['size'] > $imgsets['maxsize']) {
                    $message .= sprintf($GLOBALS['TL_LANG']['MSC']['C4G_ERROR']['image_upload_invalid_size'], ($sMaxFileSize / 1024));
                } else {
                    $message .= $GLOBALS['TL_LANG']['MSC']['C4G_ERROR']['image_upload_error'];
                }
            } else {
                $message .= $GLOBALS['TL_LANG']['MSC']['C4G_ERROR']['image_upload_error'];
            }

            $sReturn = "window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '', '$message')";
        }
    } catch (Exception $e) {

    }
    echo "<script>$sReturn;</script>";
