<?php
/**
 * con4gis - the gis-kit
 *
 * @version   php 7
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2018
 * @link      https://www.kuestenschmiede.de
 */


    if (!isset($_POST['Path']) || !isset($_FILES['File']['tmp_name'])) {
        die();
    }

    define("TL_MODE","FE");
//    $sRootPath = dirname($_SERVER['SCRIPT_FILENAME']) . "/../../../../../";
//    require_once($sRootPath."system/initialize.php");

    $initialize = $_SERVER["DOCUMENT_ROOT"].'/system/initialize.php';
    if (!file_exists($initialize)) {
        $initialize = '../../../../../system/initialize.php';
    }

    // Initialize the system
    require_once($initialize);

    // User not logged in...
    $user = \FrontendUser::getInstance();
    if (!$user->authenticate()) {
        header('HTTP/1.0 403 Forbidden');
        echo "Forbidden";
        die();
    }

    // xss cleanup
    $_FILES = \Contao\Input::xssClean($_FILES);

    $sTempname        = $_FILES['File']['tmp_name'];
    $sFileName        = $_FILES['File']['name'];
    $sFileType        = $_FILES['File']['type'];
    $sDestinationPath = \Contao\Input::post('Path');

    if ($sFileType != "image/gif" && $sFileType != "image/jpeg" && $sFileType != "image/png") {
        die();
    }

    $aImageType = getimagesize($sTempname);

    switch ($aImageType[2]) {
        case "1":
            $sExtension    = "gif";
            $sUniqID    = uniqid();
            $sFileName = $sUniqID . ".gif";
            break;
        case "2":
            $sExtension    = "jpg";
            $sUniqID    = uniqid();
            $sFileName = $sUniqID . ".jpg";
            break;
        case "3":
            $sExtension    = "png";
            $sUniqID    = uniqid();
            $sFileName = $sUniqID . ".png";
            break;
    }

    if (empty($sError)) {
        $sDestination = $sDestinationPath . $sFileName;

        $rootDir = System::getContainer()->getParameter('kernel.project_dir');
        if (move_uploaded_file($sTempname, $rootDir."/". $sDestination)) {
            echo $sDestination;
        } else {
            echo 0;
        }
    }