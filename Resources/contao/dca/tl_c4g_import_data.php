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

use con4gis\CoreBundle\Classes\C4GVersionProvider;
use Contao\Folder;
use Contao\Image;
use Contao\Input;
use Contao\StringUtil;
use Contao\System;
use Symfony\Component\Yaml\Parser;
use con4gis\CoreBundle\Resources\contao\models\C4gLogModel;

/**
 * Table tl_c4g_import_data
 */
$GLOBALS['TL_DCA']['tl_c4g_import_data'] = array
(

    // Config
    'config' => array
    (
        'dataContainer'               => 'Table',
        'sql'                         => array
        (
            'keys' => array
            (
                'id'     => 'primary',
            )
        ),
        'closed' => true,
        'onload_callback'			=> array
        (
            array('tl_c4g_import_data', 'checkData'),
        ),
        'onsubmit_callback'			=> array
        (
            array('tl_c4g_import_data', 'saveData'),
        )
    ),
    'list' => array
    (
        'sorting' => array
        (
            'mode'                    => 2,
            'fields'                  => array('caption'),
            'panelLayout'             => 'search',
            'headerFields'            => array('caption', 'type', 'source', 'bundles', 'bundlesVersion', 'description', 'importVersion', 'availableVersion'),
            'icon'                    => 'bundles/con4giscore/images/be-icons/con4gis_blue.svg',
        ),
        'label' => array
        (
            'fields'                  => array('caption', 'type', 'source', 'bundles', 'bundlesVersion', 'description', 'importVersion', 'availableVersion'),
            'showColumns'             => true,
        ),
        'global_operations' => array
        (
            'back' => [
                'href'                => 'key=back',
                'class'               => 'header_back',
                'button_callback'     => ['\con4gis\CoreBundle\Classes\Helper\DcaHelper', 'back'],
                'icon'                => 'back.svg',
                'label'               => &$GLOBALS['TL_LANG']['MSC']['backBT'],
            ],
            'con4gisIoOverview' => array
            (
                'href'                => 'key=con4gisIoOverview',
                'button_callback'     => ['tl_c4g_import_data', 'con4gisIO'],
                'icon'                => 'bundles/con4giscore/images/be-icons/con4gis_blue.svg',
                'label'               => &$GLOBALS['TL_LANG']['tl_c4g_import_data']['con4gisIoImportData'],
            ),
        ),
        'operations' => array
        (
            'import' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_c4g_import_data']['importData'],
                'href'                => 'key=importBaseData',
                'class'               => 'reload_version',
                'button_callback'     => ['tl_c4g_import_data', 'loadButtons'],
                'icon'                => 'bundles/con4giscore/images/be-icons/importData.svg'
            ),
            'update' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_c4g_import_data']['updateData'],
                'href'                => 'key=updateBaseData',
                'button_callback'     => ['tl_c4g_import_data', 'loadButtons'],
                'icon'                => 'bundles/con4giscore/images/be-icons/update_version.svg'
            ),
            'releaseImport' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_c4g_import_data']['releaseImport'],
                'href'                => 'key=releaseBaseData',
                'button_callback'     => ['tl_c4g_import_data', 'loadButtons'],
                'icon'                => 'bundles/con4giscore/images/be-icons/cut.svg'
            ),
            'delete' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_c4g_import_data']['deleteData'],
                'href'                => 'key=deleteImport',
                'button_callback'     => ['tl_c4g_import_data', 'loadButtons'],
                'icon'                => 'bundles/con4giscore/images/be-icons/delete.svg',
            )
        )
    ),

    // Select
    'select' => array
    (
        'buttons_callback' => array()
    ),

    // Edit
    'edit' => array
    (
        'buttons_callback' => array()
    ),

    // Palettes
    'palettes' => array
    (
        '__selector__'                => array(''),
        'default'                     => 'caption,description,con4gisImport'
    ),

    'subpalettes' => array
    (
        ''                            => ''
    ),

    // Fields
    'fields' => array
    (
        'id' => array
        (
            'sql'                     => "int(10) unsigned NOT NULL auto_increment",
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_import_data']['id'],
            'sorting'                 => true,
            'search'                  => true,
        ),
        'tstamp' => array
        (
            'flag'                    => 6,
            'sql'                     => "int(10) NULL default 0",
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_import_data']['tstamp'],
            'default'                 => 0,
            'sorting'                 => true,
            'search'                  => true,
            'inputType'               => 'text',
        ),
        'importUuid' => array
        (
            'sql'                     => "int(10) unsigned NOT NULL",
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_import_data']['uuid'],
            'sorting'                 => true,
            'search'                  => true,
        ),
        'importFilePath' => array
        (
            'sql'                     => "varchar(255) NOT NULL",
            'default'                 => '',
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_import_data']['importFilePath'],
            'sorting'                 => true,
            'search'                  => true,
        ),
        'caption' => array
        (
            'sql'                     => "varchar(255) NOT NULL",
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_import_data']['caption'],
            'inputType'               => 'text',
            'default'                 => '',
            'inputType'               => 'text',
            'eval'                    => array('mandatory' => true),
            'sorting'                 => true,
            'search'                  => true,
            'filter'                  => true,
        ),
        'type' => array
        (
            'sql'                     => "varchar(255) NOT NULL",
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_import_data']['type'],
            'inputType'               => 'select',
            'options'                 => array(
                'demo'                  => $GLOBALS['TL_LANG']['tl_c4g_import_data']['type_demo'],
                'basedata'              => $GLOBALS['TL_LANG']['tl_c4g_import_data']['type_basedata']
            ),
            'default'                 => '',
            'sorting'                 => true,
            'search'                  => true,
        ),
        'source' => array
        (
            'sql'                     => "varchar(255) NOT NULL",
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_import_data']['source'],
            'inputType'               => 'select',
            'options'                 => array(
                'locale'                => $GLOBALS['TL_LANG']['tl_c4g_import_data']['source_locale'],
                'io'                    => $GLOBALS['TL_LANG']['tl_c4g_import_data']['source_io']
            ),
            'default'                 => '',
            'sorting'                 => true,
            'search'                  => true,
        ),
        'bundles' => array
        (
            'sql'                     => "text NOT NULL",
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_import_data']['bundles'],
            'inputType'               => 'text',
            'default'                 => '',
            'sorting'                 => true,
            'search'                  => true,
        ),
        'bundlesVersion' => array
        (
            'sql'                     => "text NOT NULL",
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_import_data']['bundlesVersion'],
            'inputType'               => 'text',
            'default'                 => '',
            'sorting'                 => true,
            'search'                  => true,
        ),
        'importVersion' => array
        (
            'sql'                     => "text NOT NULL",
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_import_data']['importVersion'],
            'inputType'               => 'text',
            'default'                 => '',
            'sorting'                 => true,
            'search'                  => true,
        ),
        'availableVersion' => array
        (
            'sql'                     => "text NOT NULL",
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_import_data']['availableVersion'],
            'inputType'               => 'text',
            'default'                 => '',
            'sorting'                 => true,
            'search'                  => true,
        ),
        'description' => array
        (
            'sql'                     => "text NOT NULL",
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_import_data']['description'],
            'inputType'               => 'text',
            'default'                 => '',
            'eval'                    => array('mandatory' => true),
            'sorting'                 => true,
            'search'                  => true,
        ),
        'con4gisImport' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_import_data']['con4gisImport'],
            'filter'                  => false,
            'inputType'               => 'select',
            'options_callback'        => ['tl_c4g_import_data', 'getCon4gisImportTemplates'],
            'sql'                     => "int NOT NULL default 0"
        )
    ),
);

/**
 * Class tl_c4g_import_data
 */
class tl_c4g_import_data extends Contao\Backend
{

    /**
     * Import the back end user object
     */
    public function __construct()
    {
        parent::__construct();
        $this->import('BackendUser', 'User');
    }

    /**
     * loadButtons
     * @param array $arrRow the current row
     * @param string $href the url of the embedded link of the button
     * @param string $label label text for the button
     * @param string $title title value for the button
     * @param string $icon url of the image for the button
     * @param array $attributes additional attributes for the button (fetched from the array key "attributes" in the DCA)
     * @return string
     */
    public function loadButtons($arrRow, $href, $label, $title, $icon, $attributes)
    {
        $id = $arrRow['id'];
        //$userid = $this->User->id;
        $importVersion = $arrRow['importVersion'];
        $availableVersion = $arrRow['availableVersion'];
        $source = $arrRow['source'];
        $bundles = explode(",", $arrRow['bundles']);
        $bundlesVersion = explode(",", $arrRow['bundlesVersion']);
        $isInstalled = false;
        $installedPackages = $this->getContainer()->getParameter('kernel.packages');

        $bundles = str_replace(" ", "", $bundles);
        $bundlesVersion = str_replace(" ", "", $bundlesVersion);

        if ($source == "io") {
            foreach ($bundles as $key => $value) {
//            $pos = strpos($value, 'Bundle');
//            $bundleName = strtolower(substr($value,0,$pos));
                $version = $installedPackages['con4gis/'.$value];

                //Remove Bugfix release Number
                if (substr_count($version, ".") == 2) {
                    $temp = explode('.', $version);
                    unset($temp[count($temp) - 1]);
                    $version = implode('.', $temp);
                }

                //ToDo dev versions compare
                if (($version == $bundlesVersion[$key]) || strpos($version, 'dev')) {
                    $isInstalled = true;
                } else {
                    $isInstalled = false;
                    break;
                }

            }
        } else {
            $isInstalled = true;
        }


        if ($href) {
            switch ($href) {
                case 'key=importBaseData':
                    if ($importVersion == "" && $isInstalled == true) {
                        return '<a href="'.$this->addToUrl($href).'&id='.$id.'" title="'.$label.'" onclick="return confirm(\''.$GLOBALS['TL_LANG']['tl_c4g_import_data']['importDialog'].'\')"'.$attributes.'>'.\Image::getHtml($icon, $label).'</a> ';
                    }
                    break;
                case 'key=updateBaseData':
                    if ($importVersion != "" && $availableVersion != "" && $isInstalled == true && $availableVersion != $importVersion) {
                        return '<a href="'.$this->addToUrl($href).'&id='.$id.'" title="'.$label.'" onclick="return confirm(\''.$GLOBALS['TL_LANG']['tl_c4g_import_data']['updateImportDialog'].'\')"'.$attributes.'>'.\Image::getHtml($icon, $label).'</a> ';
                    }
                    break;
                case 'key=releaseBaseData':
                    if ($importVersion != "" && $isInstalled == true) {
                        return '<a href="'.$this->addToUrl($href).'&id='.$id.'" title="'.$label.'" onclick="return confirm(\''.$GLOBALS['TL_LANG']['tl_c4g_import_data']['releaseImportDialog'].'\')"'.$attributes.'>'.\Image::getHtml($icon, $label).'</a> ';
                    }
                    break;
                case 'key=deleteImport':
                    if ($importVersion != "") {
                        return '<a href="'.$this->addToUrl($href).'&id='.$id.'" title="'.$label.'" onclick="return confirm(\''.$GLOBALS['TL_LANG']['tl_c4g_import_data']['deleteImportDialog'].'\')"'.$attributes.'>'.\Image::getHtml($icon, $label).'</a> ';
                    }
                    break;
            }
        }
    }

    /**
     * loadBaseData
     */
    public function loadBaseData()
    {
        // Check current action
        $responses = $this->getCon4gisImportData("getBasedata.php", "allData");
        $responsesLength = count($responses);
        $localIoData = $this->getLocalIoData();

        $localResponses = [];
        foreach ($localIoData as $yamlConfig => $value) {
            $localResponses[$yamlConfig] = (object) $value['import'];
        }

        foreach ($localResponses as $localResponse) {
            $responses[$responsesLength] = $localResponse;
            $responsesLength++;
        }
        $localData = $this->Database->prepare("SELECT * FROM tl_c4g_import_data")->execute();
        $localData = $localData->fetchAllAssoc();

       if (empty($localData)) {
            foreach ($responses as $response) {
                $this->Database->prepare("INSERT INTO tl_c4g_import_data SET id=?, caption=?, description=?, bundles=?, bundlesVersion=?, availableVersion=?, type=?, source=?")->execute($response->id, self::replaceInsertTags($response->caption), self::replaceInsertTags($response->description), $response->bundles, $response->bundlesVersion, $response->version, $response->type, $response->source);
            }
        }
        //Update data from con4gis.io
        foreach ($localData as $data) {
            $available = false;
            foreach ($responses as $response) {
                if ($response->id == $data['id']) {
                    $this->Database->prepare("UPDATE tl_c4g_import_data SET caption=?, description=?, bundles=?, bundlesVersion=?, availableVersion=?, type=?, source=? WHERE id=?")->execute(self::replaceInsertTags($response->caption, false), self::replaceInsertTags($response->description), $response->bundles, $response->bundlesVersion, $response->version, $response->type, $response->source, $data['id']);
                    $available = true;
                }
            }
            //Delete Import if it's not available anymore
            if (!$available) {
                if ($data['importVersion'] != "") {
                    $this->Database->prepare("UPDATE tl_c4g_import_data SET availableVersion=? WHERE id=?")->execute("", $data['id']);
                } else {
                    if ($data['id'] != 0 OR $data['id'] != "") {
                        $this->Database->prepare("DELETE FROM tl_c4g_import_data WHERE id=?")->execute($data['id']);
                    } else {
                        C4gLogModel::addLogEntry("core", "Error deleting unavailable import: wrong id set!");
                    }
                }
            }
        }

        //Check for new data
        foreach ($responses as $response) {
            $count = 0;
            $arrayLength = count($localData) - 1;
            foreach ($localData as $data) {
                if ($data['id'] == $response->id) {
                    break;
                }
                if ($data['id'] != $response->id && $count == $arrayLength) {
                    $this->Database->prepare("INSERT INTO tl_c4g_import_data SET id=?, caption=?, description=?, bundles=?, availableVersion=?")->execute($response->id, self::replaceInsertTags($response->caption), self::replaceInsertTags($response->description), $response->bundles, $response->version);
                }
                $count++;
            }
        }

    }

    public function getStringBetween($string, $start, $end){
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    /**
     * importBaseData
     */
    public function importBaseData()
    {
        $data = $_REQUEST;

        $con4gisImportId = $data['id'];
//      lokaler Import

        $localImportDatas = $this->getLocalIoData();
        $availableLocal = false;
        foreach ($localImportDatas as $localImportData) {
            if ($localImportData['import']['id'] == $con4gisImportId) {
                $availableLocal = true;
                $importData = $localImportData;
                break;
            }
        }

        if ($availableLocal) {
            $imagePath = "./../files".$importData['images']['path'];
            $c4gPath = "./../vendor/con4gis/".$importData['general']['bundle']."/Resources/con4gis/".$importData['general']['filename'];
            $cache = "./../var/cache/prod/con4gis/io-data/".str_replace(".c4g", "", $importData['general']['filename']);

            $zip = new ZipArchive;
            if ($zip->open($c4gPath) === TRUE) {
                $zip->extractTo($cache);
                $zip->close();

                $images = array_slice(scandir($cache."/images/"), 2);
                mkdir($imagePath, 0770, true);
                foreach ($images as $image) {
                    copy($cache."/images/".$image, $imagePath."/".$image);
                }
                $this->makeFolderAvailableForPublic($imagePath);
                $this->makeFolderAvailableForPublic("./../files/con4gis_import_data");
            }

            $file = file_get_contents($cache."/data/".str_replace(".c4g", ".json", $importData['general']['filename']));

            $sqlStatements = $this->getSqlFromJson($file, $importData['import']['uuid']);

            foreach ($sqlStatements as $sqlStatement) {
                if ($sqlStatement == "") {
                    break;
                }

                try {
                    $this->Database->query($sqlStatement);

                } catch (Exception $e) {
                    C4gLogModel::addLogEntry("core", "Error while executing SQL-Import: ".$e->getMessage());
                }
            }
            $this->Database->prepare("UPDATE tl_c4g_import_data SET importVersion=?WHERE id=?")->execute($importData['import']['version'], $con4gisImportId);
            $this->Database->prepare("UPDATE tl_c4g_import_data SET importUuid=? WHERE id=?")->execute($localImportData['import']['uuid'], $con4gisImportId);
            $this->Database->prepare("UPDATE tl_c4g_import_data SET importFilePath=? WHERE id=?")->execute($localImportData['images']['path'], $con4gisImportId);

            $this->recursiveRemoveDirectory($cache);


//      lokaler Import Ende

        } elseif (!$availableLocal) {

            $objSettings = \con4gis\CoreBundle\Resources\contao\models\C4gSettingsModel::findSettings();
            $basedataUrl = rtrim($objSettings->con4gisIoUrl, "/") . "/" . "getBasedata.php";
            $basedataUrl .= "?key=" . $objSettings->con4gisIoKey;
            $basedataUrl .= "&mode=" . "ioData";
            $basedataUrl .= "&data=" . $con4gisImportId;
            $downloadPath = "./../var/cache/prod/con4gis/io-data/";
            $filename = 'io-data-proxy.c4g';
            mkdir($downloadPath, 0770, true);
            file_put_contents($downloadPath.$filename, file_get_contents($basedataUrl));

            $zip = zip_open($downloadPath.$filename);

            if ($zip) {
                while ($zip_entry = zip_read($zip)) {
                    if (zip_entry_name($zip_entry) == "io-data.yml") {
                        if (zip_entry_open($zip, $zip_entry)) {
                            // Read open directory entry
                            $contents = zip_entry_read($zip_entry);
                            zip_entry_close($zip_entry);
                            $yaml = new Parser();
                            $importData = $yaml->parse($contents);
                            break;
                        }
                    }
                }
                zip_close($zip);
            }

            $imagePath = "./../files".$importData['images']['path'];
//            $c4gPath = "./../vendor/con4gis/".$importData['general']['bundle']."/Resources/con4gis/".$importData['general']['filename'];
            $cache = "./../var/cache/prod/con4gis/io-data/".str_replace(".c4g", "", $importData['general']['filename']);

            $zip = new ZipArchive;
            if ($zip->open($downloadPath.$filename) === TRUE) {
                $zip->extractTo($cache);
                $zip->close();

                $images = array_slice(scandir($cache."/images/"), 2);
                mkdir($imagePath, 0770, true);
                foreach ($images as $image) {
                    copy($cache."/images/".$image, $imagePath."/".$image);
                }
                $this->makeFolderAvailableForPublic($imagePath);
                $this->makeFolderAvailableForPublic("./../files/con4gis_import_data");
            }
            $file = file_get_contents($cache."/data/".str_replace(".c4g", ".json", $importData['general']['filename']));
            $sqlStatements = $this->getSqlFromJson($file, $importData['import']['uuid']);


            foreach ($sqlStatements as $sqlStatement) {
                if ($sqlStatement == "") {
                    break;
                }

                try {
                    $this->Database->query($sqlStatement);

                } catch (Exception $e) {
                    C4gLogModel::addLogEntry("core", "Error while executing SQL-Import: ".$e->getMessage());
                }
            }
            $this->Database->prepare("UPDATE tl_c4g_import_data SET importVersion=?WHERE id=?")->execute($importData['import']['version'], $con4gisImportId);
            $this->Database->prepare("UPDATE tl_c4g_import_data SET importUuid=? WHERE id=?")->execute($importData['import']['uuid'], $con4gisImportId);
            $this->Database->prepare("UPDATE tl_c4g_import_data SET importFilePath=? WHERE id=?")->execute($importData['images']['path'], $con4gisImportId);

            $this->recursiveRemoveDirectory("./../var/cache/prod/con4gis/io-data/".str_replace(".c4g", "", $importData['general']['filename']));
            unlink("./../var/cache/prod/con4gis/io-data/".$filename);
        }
        //Sync filesystem
        Dbafs::syncFiles();
    }

    /**
     * updateBaseData
     */
    public function updateBaseData()
    {
        // Check current action
        $this->deleteBaseData();
        $this->importBaseData();
    }

    /**
     * releaseBaseData
     */
    public function releaseBaseData()
    {
        // Check current action
        $data = $_REQUEST;
        $con4gisDeleteId = $data['id'];
        $localData = $this->Database->prepare("SELECT * FROM tl_c4g_import_data WHERE id=?")->execute($con4gisDeleteId);
        $con4gisReleaseUuid = $localData->importUuid;
        $con4gisReleaseBundles = $localData->bundles;

        if ($con4gisReleaseUuid != 0 && $con4gisReleaseUuid != "" && $con4gisReleaseUuid >= 6) {
            //Release import data
            $tables = $this->Database->listTables();

            if (strpos($con4gisReleaseBundles, 'maps') !== false) {
                foreach ($tables as $table) {
                    if (strpos($table, 'map') !== false) {
                        $this->Database->prepare("UPDATE $table SET importId=? WHERE importId=?")->execute("0", $con4gisReleaseUuid);
                    }
                }
            }

            if (strpos($con4gisReleaseBundles, 'firefighter') !== false) {
                foreach ($tables as $table) {
                    if (strpos($table, 'map') !== false) {
                        $this->Database->prepare("UPDATE $table SET importId=? WHERE importId=?")->execute("0", $con4gisReleaseUuid);
                    }
                }
            }

            if (strpos($con4gisReleaseBundles, 'visualization') !== false) {
                foreach ($tables as $table) {
                    if (strpos($table, 'map') !== false) {
                        $this->Database->prepare("UPDATE $table SET importId=? WHERE importId=?")->execute("0", $con4gisReleaseUuid);
                    }
                }
            }

            if (strpos($con4gisReleaseBundles, 'data') !== false) {
                $this->Database->prepare("UPDATE $table SET importId=? WHERE importId=?")->execute("0", $con4gisReleaseUuid);
            }

            if (strpos($con4gisReleaseBundles, 'editor') !== false) {

                foreach ($tables as $table) {
                    if (strpos($table, 'map') !== false) {
                        $this->Database->prepare("UPDATE $table SET importId=? WHERE importId=?")->execute("0", $con4gisReleaseUuid);
                    }
                }            }

            if (strpos($con4gisReleaseBundles, 'forum') !== false) {
                foreach ($tables as $table) {
                    if (strpos($table, 'map') !== false) {
                        $this->Database->prepare("UPDATE $table SET importId=? WHERE importId=?")->execute("0", $con4gisReleaseUuid);
                    }
                }
            }

            if (strpos($con4gisReleaseBundles, 'io-travel-costs') !== false) {
                foreach ($tables as $table) {
                    if (strpos($table, 'map') !== false) {
                        $this->Database->prepare("UPDATE $table SET importId=? WHERE importId=?")->execute("0", $con4gisReleaseUuid);
                    }
                }
            }

            if (strpos($con4gisReleaseBundles, 'projects') !== false) {
                foreach ($tables as $table) {
                    if (strpos($table, 'map') !== false) {
                        $this->Database->prepare("UPDATE $table SET importId=? WHERE importId=?")->execute("0", $con4gisReleaseUuid);
                    }
                }
            }

            if (strpos($con4gisReleaseBundles, 'routing') !== false) {
                foreach ($tables as $table) {
                    if (strpos($table, 'map') !== false) {
                        $this->Database->prepare("UPDATE $table SET importId=? WHERE importId=?")->execute("0", $con4gisReleaseUuid);
                    }
                }
            }

            if (strpos($con4gisReleaseBundles, 'tracking') !== false) {
                foreach ($tables as $table) {
                    if (strpos($table, 'map') !== false) {
                        $this->Database->prepare("UPDATE $table SET importId=? WHERE importId=?")->execute("0", $con4gisReleaseUuid);
                    }
                }
            }

            if ($this->strposa($con4gisReleaseBundles, 'pwa') !== false) {
                foreach ($tables as $table) {
                    if (strpos($table, 'map') !== false) {
                        $this->Database->prepare("UPDATE $table SET importId=? WHERE importId=?")->execute("0", $con4gisReleaseUuid);
                    }
                }
            }

            $this->Database->prepare("UPDATE tl_c4g_import_data SET importVersion=? WHERE id=?")->execute("", $con4gisDeleteId);
            $this->Database->prepare("UPDATE tl_c4g_import_data SET importUuid=? WHERE id=?")->execute("0", $con4gisDeleteId);
            $this->Database->prepare("UPDATE tl_c4g_import_data SET importfilePath=? WHERE id=?")->execute("", $con4gisDeleteId);

            $this->loadBaseData();
        } else {
            C4gLogModel::addLogEntry("core", "Error releasing unavailable import: wrong id set!");
        }
    }

    /**
     * deleteBaseData
     */
    public function deleteBaseData()
    {
        $data = $_REQUEST;
        $con4gisDeleteId = $data['id'];
        $localData = $this->Database->prepare("SELECT * FROM tl_c4g_import_data WHERE id=?")->execute($con4gisDeleteId);
        $con4gisDeleteUuid = $localData->importUuid;
        $con4gisDeleteBundles = $localData->bundles;
        $con4gisDeletePath = $localData->importFilePath;
        $con4gisDeleteDirectory = "./../files".$con4gisDeletePath."/";
        $con4gisDeleteUuidLength = strlen($con4gisDeleteUuid);

        if ($con4gisDeleteUuid != 0 && $con4gisDeleteUuid != "" && $con4gisDeleteUuidLength >= 6) {
            if ($con4gisDeletePath != "") {
                if (is_dir($con4gisDeleteDirectory)) {
                    unlink($con4gisDeleteDirectory."/.public");
                    if (strpos($con4gisDeleteDirectory, "/files/con4gis_import_data/")) {
                        $this->recursiveRemoveDirectory($con4gisDeleteDirectory."/");
                        $con4gisImportFolderScan = array_diff(scandir("./../files/con4gis_import_data"), array(".", ".."));
                        if (count($con4gisImportFolderScan) == 1) {
                            if (in_array(".public", $con4gisImportFolderScan)) {
                                $this->recursiveRemoveDirectory("./../con4gis_import_data");
                            }
                        }
                        $this->import('Contao\Automator', 'Automator');
                        $this->Automator->generateSymlinks();
                        //Sync filesystem
                        Dbafs::syncFiles();
                    } else {
                        C4gLogModel::addLogEntry("core", "Could not delete import directory: Wrong path!");
                    }
                }
            }

            //Delete import data
            $tables = $this->Database->listTables();
            if (strpos($con4gisDeleteBundles, 'maps') !== false) {
                $this->deleteSqlImport($tables, "c4g_map", $con4gisDeleteUuid);
            }

            if (strpos($con4gisDeleteBundles, 'firefighter') !== false) {
                $this->deleteSqlImport($tables, "c4g_firefighter", $con4gisDeleteUuid);
            }

            if (strpos($con4gisDeleteBundles, 'visualization') !== false) {
                $this->deleteSqlImport($tables, "c4g_visualization", $con4gisDeleteUuid);
            }

            if (strpos($con4gisDeleteBundles, 'data') !== false) {
                $this->deleteSqlImport($tables, "c4g_data", $con4gisDeleteUuid);
            }

            if (strpos($con4gisDeleteBundles, 'editor') !== false) {
                $this->deleteSqlImport($tables, "c4g_editor", $con4gisDeleteUuid);
            }

            if (strpos($con4gisDeleteBundles, 'forum') !== false) {
                $this->deleteSqlImport($tables, "c4g_forum", $con4gisDeleteUuid);
            }

            if (strpos($con4gisDeleteBundles, 'io-travel-costs') !== false) {
                $this->deleteSqlImport($tables, "c4g_travel_costs", $con4gisDeleteUuid);
            }

            if (strpos($con4gisDeleteBundles, 'projects') !== false) {
                $this->deleteSqlImport($tables, "c4g_projects", $con4gisDeleteUuid);
            }

            if (strpos($con4gisDeleteBundles, 'routing') !== false) {
                $this->deleteSqlImport($tables, "c4g_routing", $con4gisDeleteUuid);
            }

            if (strpos($con4gisDeleteBundles, 'tracking') !== false) {
                $this->deleteSqlImport($tables, "c4g_tracking", $con4gisDeleteUuid);
            }

            if ($this->strposa($con4gisDeleteBundles, 'pwa') !== false) {
                $this->deleteSqlImport($tables, "c4g_tracking", $con4gisDeleteUuid);
            }

            $this->Database->prepare("UPDATE tl_c4g_import_data SET importVersion=? WHERE id=?")->execute("", $con4gisDeleteId);
            $this->Database->prepare("UPDATE tl_c4g_import_data SET importUuid=? WHERE id=?")->execute("0", $con4gisDeleteId);
            $this->Database->prepare("UPDATE tl_c4g_import_data SET importfilePath=? WHERE id=?")->execute("", $con4gisDeleteId);

            $this->loadBaseData();

        } else {
            C4gLogModel::addLogEntry("core", "Error deleting unavailable import: wrong id set!");
        }

    }

    function deleteSqlImport($tables, $bundle, $con4gisDeleteUuid) {
        foreach ($tables as $table) {
            if (is_array($bundle)) {

            } elseif (strpos($table, $bundle) !== false) {
                $this->Database->prepare("DELETE FROM $table WHERE importId=?")->execute($con4gisDeleteUuid);
            }
        }
    }

    function strposa($haystack, $needles=array(), $offset=0) {
        $chr = array();
        foreach($needles as $needle) {
            $res = strpos($haystack, $needle, $offset);
            if ($res !== false) $chr[$needle] = $res;
        }
        if(empty($chr)) return false;
        return min($chr);
    }

    /**
     * saveData
     */
    public function saveData(DataContainer $dc)
    {
        $con4gisImport = $this->Input->post('con4gisImport');

        $responses = $this->getCon4gisImportData("getBasedata.php", "specificData", $con4gisImport);

        foreach ($responses as $response) {
            $objUpdate = $this->Database->prepare("UPDATE tl_c4g_import_data SET bundles=? WHERE id=?")->execute($response->bundles, $dc->id);
        }

    }

    /**
     * checkData
     */
    public function checkData()
    {
        $key = Contao\Input::get('key');

            if ($key) {
                switch ($key) {
                    case 'importBaseData':
                        $this->importBaseData();
                        break;
                    case 'updateBaseData':
                        $this->updateBaseData();
                        break;
                    case 'deleteImport':
                        $this->deleteBaseData();
                        break;
                    case 'importData':
                        $this->loadBaseData();
                        break;
                    case 'releaseBaseData':
                        $this->releaseBaseData();
                        break;
                }
            }

            \Contao\Message::addInfo($GLOBALS['TL_LANG']['tl_c4g_import_data']['infotext']);
        
    }

    /**
     * getCon4gisImportTemplates
     */
    public function getCon4gisImportTemplates()
    {

        $responses = $this->getCon4gisImportData("getBasedata.php", "allData");
        $arrReturn = [];
        foreach ($responses as $response) {
            $arrReturn[$response->id] = \InsertTags::replaceInsertTags($response->caption);
        }
        return $arrReturn;
    }

    /**
     * getCon4gisImportData
     */
    public function getCon4gisImportData($importData, $mode, $data = false)
    {
        $objSettings = \con4gis\CoreBundle\Resources\contao\models\C4gSettingsModel::findSettings();
        if ($objSettings->con4gisIoUrl && $objSettings->con4gisIoKey) {
            $basedataUrl = rtrim($objSettings->con4gisIoUrl, "/") . "/" . $importData;
            $basedataUrl .= "?key=" . $objSettings->con4gisIoKey;
            $basedataUrl .= "&mode=" . $mode;
            if (isset($data)) {
                $basedataUrl .= "&data=" . str_replace(' ', '%20', $data);
            }
            $REQUEST = new \Request();
            if ($_SERVER['HTTP_REFERER']) {
                $REQUEST->setHeader('Referer', $_SERVER['HTTP_REFERER']);
            }
            if ($_SERVER['HTTP_USER_AGENT']) {
                $REQUEST->setHeader('User-Agent', $_SERVER['HTTP_USER_AGENT']);
            }
            $REQUEST->send($basedataUrl);
            if ($REQUEST->response) {
                return $responses = \GuzzleHttp\json_decode($REQUEST->response);
            } else {
                return $responses = [];
            }
        }
    }

    public function getLocalIoData()
    {
        $arrBasedataFolders = [
            'maps' => './../vendor/con4gis/maps/Resources/con4gis',
            'visualization' => './../vendor/con4gis/visualization/Resources/con4gis',
            'core' => './../vendor/con4gis/core/Resources/con4gis',
            'data' => './../vendor/con4gis/data/Resources/con4gis',
            'firefighter' => './../vendor/con4gis/firefighter/Resources/con4gis'
        ];

        $dir = getcwd();
        $basedataFiles = [];

        foreach ($arrBasedataFolders as $arrBasedataFolder => $value ) {
            $basedataFiles[$arrBasedataFolder] = array_slice(scandir($value), 2);
            foreach ($basedataFiles[$arrBasedataFolder] as $basedataFile => $file) {
                $basedataFiles[$arrBasedataFolder][$basedataFile] = $value.'/'.$file;
            }
        }

        $newYamlConfigArray = [];
        $count = 0;
        foreach ($basedataFiles as $basedataFile) {
            foreach ($basedataFile as $importFile) {
                $zip = zip_open($importFile);

                if ($zip) {
                    while ($zip_entry = zip_read($zip)) {
                        if (zip_entry_name($zip_entry) == "io-data.yml") {
                            if (zip_entry_open($zip, $zip_entry)) {
                                // Read open directory entry
                                $contents = zip_entry_read($zip_entry);
                                zip_entry_close($zip_entry);
                                $yaml = new Parser();
                                $newYamlConfigArray[$count] = $yaml->parse($contents);
                                $count++;
                                break;
                            }
                        }
                    }
                    zip_close($zip);
                }
            }
        }

        return $newYamlConfigArray;
    }

    public function recursiveRemoveDirectory($directory)
    {
        foreach(glob("{$directory}/*") as $file)
        {
            if(is_dir($file)) {
                $this->recursiveRemoveDirectory($file);
            } else if(!is_link($file)) {
                unlink($file);
            }
        }
        rmdir($directory);
    }

    /**
     * con4gisIO
     * @param $href
     * @param $label
     * @param $title
     * @param $class
     * @param $attributes
     * @return string
     */
    public function con4gisIO($href, $label, $title, $class, $attributes)
    {
        return '<a href="https://con4gis.io/blaupausen"  class="' . $class . '" title="' . StringUtil::specialchars($title) . '"' . $attributes .' target="_blank" rel="noopener">' . $label . '</a><br>';
    }

    public function makeFolderAvailableForPublic($href)
    {
        $handle = fopen($href."/.public", "w");
        $this->import('Contao\Automator', 'Automator');
        $this->Automator->generateSymlinks();
    }

    public function getSqlFromJson($file, $uuid)
    {
        $jsonFile = (array) json_decode($file);
        $jsonSize = sizeof($jsonFile);
        $sqlStatements = [];
        $relations = array_slice($jsonFile, -1, 1);
        $relationTables = [];
        $dbRelation = [];

        foreach ($relations['relations'] as $key => $value) {
            $firstTable = explode(".", $key);

                if (!in_array($firstTable[0], $relationTables)) {
                    $relationTables[] = $firstTable[0];
                }

                $dbRelation[$firstTable[0]][] = $firstTable[1];


        }

        //Check for previous released import
        $firstImportTable = array_key_first($jsonFile);
        $newId = $uuid;
        $importUuidCheck = "%".$uuid."%";

        if (substr($jsonFile['tl_files'][0]->uuid, 0, 2) == "0x") {
            $firstTlFilesUuid = substr($jsonFile['tl_files'][0]->uuid, 2);
        } else {
            $firstTlFilesUuid = $jsonFile['tl_files'][0]->uuid;
        }
        $firstTableQuery = $this->Database->prepare("SELECT id FROM $firstImportTable WHERE id LIKE ?")->execute($importUuidCheck)->fetchAllAssoc();
        $tlFilesTableQuery = $this->Database->prepare("SELECT uuid FROM tl_files WHERE HEX(uuid) LIKE ?")->execute("%".$firstTlFilesUuid."%")->fetchAllAssoc();

        while ($firstTableQuery) {
            $newId = rand(100001, 999999);
            $firstTableQuery = $this->Database->prepare("SELECT id FROM $firstImportTable WHERE id LIKE ?")->execute($newId)->fetchAllAssoc();
        }

        foreach ($jsonFile as $importDB => $importDatasets) {
            if ($importDB == "relations") {
                break;
            }

            $dbFields = $this->Database->getFieldNames($importDB);
            foreach ($importDatasets as $importDataset) {
                $sqlStatement = "";
                $importDataset = (array) $importDataset;
                if (!array_key_exists("importId", $importDataset)) {
                    $importDataset['importId'] = $uuid;
                }
                foreach ($importDataset as $importDbField => $importDbValue) {
                    if ($importDbField == "id") {
                       $importDbValue = $this->prepend($newId, $importDbValue);
                    } elseif ($importDbField == "importId") {
                        $importDbValue = $uuid;
                    } elseif (in_array($importDB, $relationTables)) {
                        if (in_array($importDbField, $dbRelation[$importDB])) {
                            if ($importDbValue != "0") {

                                if (substr($importDbValue, 0, 2) == "0x" && $importDB != "tl_files") {
                                    $unserial = hex2bin(substr($importDbValue, 2));

                                    if (strpos($unserial, "{")) {
                                        $unserial = unserialize($unserial);
                                        foreach ($unserial as $key => $value) {
                                            $unserial[$key] = $this->prepend($newId, $value);
                                        }
                                        $newImportDbValue = serialize($unserial);
                                        $newImportDbValue = bin2hex($newImportDbValue);
                                        $newImportDbValue = $this->prepend("0x", $newImportDbValue);
                                        $importDbValue = $newImportDbValue;
                                    } else {
                                        $newImportDbValue = $this->prepend($newId, $unserial);
                                        $newImportDbValue = bin2hex($newImportDbValue);
                                        $newImportDbValue = $this->prepend("0x", $newImportDbValue);
                                        $importDbValue = $newImportDbValue;
                                    }
                                } elseif (strpos($importDbValue, "{")) {
                                    $unserial = hex2bin(substr($importDbValue, 2));
                                    $unserial = unserialize($unserial);
                                    foreach ($unserial as $key => $value) {
                                        $unserial[$key] = $this->prepend($newId, $value);
                                    }
                                    $newImportDbValue = serialize($unserial);
                                    $newImportDbValue = bin2hex($newImportDbValue);
                                    $newImportDbValue = $this->prepend("0x", $newImportDbValue);
                                        $importDbValue = $newImportDbValue;
                                } elseif (is_numeric($importDbValue)) {
                                    $newImportDbValue = $this->prepend($newId, $importDbValue);
                                    $importDbValue = $newImportDbValue;
                                }
                            }
                        }
                    }
                    if (in_array($importDbField, $dbFields)) {
                        if ($sqlStatement == "" && substr($importDbValue, 0, 2) == "0x") {
                            $sqlStatement = 'INSERT INTO `'.$importDB.'` ('.$importDbField.') VALUES ('.$importDbValue.');';
                        } elseif ($sqlStatement == "" && substr($importDbValue, 0, 2) != "0x") {
                            $sqlStatement = "INSERT INTO `".$importDB."` (".$importDbField.") VALUES ('".$importDbValue."');";
                        } elseif (substr($importDbValue, 0, 2) == "0x") {
                            $sqlStatement = str_replace(") VALUES", ", $importDbField) VALUES", $sqlStatement);
                            $sqlStatement = str_replace(");", ", $importDbValue);", $sqlStatement);
                        } else {
                            $sqlStatement = str_replace(") VALUES", ", $importDbField) VALUES", $sqlStatement);
                            $sqlStatement = str_replace(");", ", '$importDbValue');", $sqlStatement);
                        }
                    } else {
                        C4gLogModel::addLogEntry("core", "The import database field <b>".$importDbField."</b> is not in the database <b>".$importDB."</b>.");
                    }
                }
                if ($importDB == "tl_files" && $tlFilesTableQuery) {
                    C4gLogModel::addLogEntry("core", "Files already imported. tl_files will not be imported");
                } else {
//                    if ($importDB != "tl_c4g_maps") {
                        $sqlStatements[] = $sqlStatement;
//                    }
                }
            }
        }

        return $sqlStatements;
    }

    function prepend($string, $chunk)
    {
        if(!empty($chunk) && isset($chunk)) {
            return $string.$chunk;
        }
        else {
            return $string;
        }
    }

}