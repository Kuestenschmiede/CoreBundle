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

use Contao\Image;
use Contao\StringUtil;
use Contao\System;
use Contao\Input;
use con4gis\CoreBundle\Classes\Callback\C4GImportDataCallback;
use con4gis\CoreBundle\Classes\Events\BeforeImportButtonLoadEvent;
use Contao\BackendUser;
use Contao\DC_Table;
use Composer\InstalledVersions;
/**
 * Table tl_c4g_import_data
 */
$GLOBALS['TL_DCA']['tl_c4g_import_data'] = array
(

    // Config
    'config' => array
    (
        'dataContainer'    => DC_Table::class,
        'notCopyable'      => true,
        'notCreatable'     => true,
        'notEditable'      => true,
        'notDeletable'     => true,
        'enableVersioning' => false,
        'switchToEdit'     => false,
        'sql'              => array
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
            'label_callback'          => ['tl_c4g_import_data', 'labelCallback'],
        ),
        'global_operations' => array
        (
            'back' => [
                'href'                => 'do=c4g_bricks&table=tl_c4g_bricks',
                'class'               => 'header_back',
                'icon'                => 'back.svg',
                'label'               => &$GLOBALS['TL_LANG']['MSC']['backBT'],
            ]
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
            'sql'                     => "int(10) unsigned NOT NULL default '0'",
            'default'                 => 0,
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_import_data']['uuid'],
            'sorting'                 => true,
            'search'                  => true,
        ),
        'importFilePath' => array
        (
            'sql'                     => "varchar(255) NOT NULL default ''",
            'default'                 => '',
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_import_data']['importFilePath'],
            'sorting'                 => true,
            'search'                  => true,
        ),
        'caption' => array
        (
            'sql'                     => "varchar(255) NOT NULL default ''",
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_import_data']['caption'],
            'inputType'               => 'text',
            'default'                 => '',
            'eval'                    => array('mandatory' => true),
            'sorting'                 => true,
            'search'                  => true,
            'filter'                  => true,
        ),
        'type' => array
        (
            'sql'                     => "varchar(255) NOT NULL default ''",
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_import_data']['type'],
            'inputType'               => 'select',
            'options'                 => array(
                'demo'                  => &$GLOBALS['TL_LANG']['tl_c4g_import_data']['type_demo'],
                'basedata'              => &$GLOBALS['TL_LANG']['tl_c4g_import_data']['type_basedata'],
            ),
            'default'                 => '',
            'sorting'                 => true,
            'search'                  => true,
        ),
        'source' => array
        (
            'sql'                     => "varchar(255) NOT NULL default ''",
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_import_data']['source'],
            'inputType'               => 'select',
            'options'                 => array(
                'locale'                => &$GLOBALS['TL_LANG']['tl_c4g_import_data']['source_locale'],
                'io'                    => &$GLOBALS['TL_LANG']['tl_c4g_import_data']['source_io']
            ),
            'default'                 => '',
            'sorting'                 => true,
            'search'                  => true,
        ),
        'bundles' => array
        (
            'sql'                     => "varchar(255) NOT NULL default ''",
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_import_data']['bundles'],
            'inputType'               => 'text',
            'default'                 => '',
            'sorting'                 => true,
            'search'                  => true,
        ),
        'bundlesVersion' => array
        (
            'sql'                     => "text NOT NULL default ''",
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_import_data']['bundlesVersion'],
            'inputType'               => 'text',
            'default'                 => '',
            'sorting'                 => true,
            'search'                  => true,
        ),
        'importVersion' => array
        (
            'sql'                     => "varchar(255) NOT NULL default ''",
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_import_data']['importVersion'],
            'inputType'               => 'text',
            'default'                 => '',
            'sorting'                 => true,
            'search'                  => true,
        ),
        'availableVersion' => array
        (
            'sql'                     => "varchar(255) NOT NULL default ''",
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_import_data']['availableVersion'],
            'inputType'               => 'text',
            'default'                 => '',
            'sorting'                 => true,
            'search'                  => true,
        ),
        'description' => array
        (
            'sql'                     => "text NULL",
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
            'sql'                     => "int NOT NULL default 0",
        ),
        'importRunning' => array
        (
            'filter'                  => false,
            'inputType'               => 'checkbox',
            'sql'                     => "char(1) NOT NULL default ''",
        ),
        'importTables' => array(
            'filter'                  => false,
            'inputType'               => 'text',
            'sql'                     => "varchar(255) NOT NULL default ''",
            'default'                  => "",
        )
    ),
);

/**
 * Class tl_c4g_import_data
 */
class tl_c4g_import_data extends Contao\Backend
{

    private $importDataCallback = null;

    /**
     * Import the back end user object
     */
    public function __construct()
    {
        parent::__construct();
        $this->import(BackendUser::class, 'User');
        $this->importDataCallback = new C4GImportDataCallback();
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

        $event = new BeforeImportButtonLoadEvent();
        $dispatcher = System::getContainer()->get('event_dispatcher');
        $event->setImportData($arrRow);
        $dispatcher->dispatch($event, $event::NAME);
        $importCompatible = $event->getImportCompatible();
        $updateCompatible = $event->getUpdateCompatible();
        $releaseCompatible = $event->getReleaseCompatible();
        $compatibleImportType = $event->getImportType();
        $vendor = $event->getVendor();

        $id = $arrRow['id'];
        //$userid = $this->User->id;
        $importVersion = $arrRow['importVersion'];
        $importType = $arrRow['type'];
        $availableVersion = $arrRow['availableVersion'];
        $source = $arrRow['source'];
        $bundles = explode(",", $arrRow['bundles']);
        $bundlesVersion = explode(",", $arrRow['bundlesVersion']);
        $isInstalled = false;
//        $installedPackages = System::getContainer()->getParameter('kernel.packages');
        if (System::getContainer()->hasParameter('kernel.packages')) {
            $installedPackages = System::getContainer()->getParameter('kernel.packages');
        }
        else {
            $installedPackages = [];
            $allInstalledPackages = InstalledVersions::getInstalledPackages();
            foreach ($allInstalledPackages as $key => $installedPackage) {
                $installedPackages[$installedPackage] = InstalledVersions::getVersion($installedPackage);
            }
        }
        $importAllowed = true;

        $bundles = str_replace(" ", "", $bundles);
        $bundlesVersion = str_replace(" ", "", $bundlesVersion);

        if ($source != "locale") {
            foreach ($bundles as $key => $value) {
                $pos = strpos($value, 'Bundle');
                $bundleName = strtolower(substr($value,0,$pos));
                $version = key_exists('con4gis/'.$value, $installedPackages) ? $installedPackages['con4gis/'.$value] : false;
                if (!$version) {
                    if (!empty($compatibleImportType)) {
                        if (in_array($importType, $compatibleImportType)) {
                            $importAllowed = false;
                        }
                    }
                    $version = $installedPackages[$vendor.'/'.$value];
                }

                //Remove Bugfix release Number
                if (substr_count($version, ".") == 2 && strpos($version, 'dev') !== true) {
                    $temp = explode('.', $version);
                    unset($temp[count($temp) - 1]);
                    $version = implode('.', $temp);
                }

                //Check if Version contains x or -
                $allMinorVersions = false;
                if (strpos($bundlesVersion[$key], '.x') !== false) {
                    $bundlesVersion[$key] = strtok($bundlesVersion[$key], ".x");
                    $allMinorVersions = true;
                }
                if (strpos($bundlesVersion[$key], '-') !== false) {
                    $bothVersions = explode("-", $bundlesVersion[$key]);
                    $versionFrom = explode(".", $bothVersions[0]);
                    $versionTo = explode(".", $bothVersions[1]);
                    if ($versionFrom[0] == $versionTo[0]) {
                        $versionRange = range($versionFrom[1], $versionTo[1]);
                        foreach ($versionRange as $subVersion) {
                            if ($versionFrom[0].'.'.$subVersion == $version) {
                                $bundlesVersion[$key] = $version;
                                break;
                            }
                        }
                    }
                }

                if ($allMinorVersions) {
                    if (strpos($version, 'dev') !== true) {
                        $version = strtok($version, ".");
                    }
                }

                //ToDo dev versions compare
                if (($version == $bundlesVersion[$key]) || strpos($version, 'dev') !== false) {
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
                    if ($importVersion == "" && $isInstalled && ((!$importAllowed && $importCompatible) || $importAllowed )) {
                        return '<a href="'.$this->addToUrl($href).'&id='.$id.'" title="'.$label.'" onclick="return confirm(\''.$GLOBALS['TL_LANG']['tl_c4g_import_data']['importDialog'].'\')"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ';
                    }
                    break;
                case 'key=updateBaseData':
                    if ($importVersion != "" && $availableVersion != "" && $isInstalled && $availableVersion != $importVersion && !$importAllowed && $updateCompatible) {
                        return '<a href="'.$this->addToUrl($href).'&id='.$id.'" title="'.$label.'" onclick="return confirm(\''.$GLOBALS['TL_LANG']['tl_c4g_import_data']['updateImportDialog'].'\')"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ';
                    } elseif ($importVersion != "" && $availableVersion != "" && $isInstalled && $availableVersion != $importVersion && $importAllowed) {
                        return '<a href="'.$this->addToUrl($href).'&id='.$id.'" title="'.$label.'" onclick="return confirm(\''.$GLOBALS['TL_LANG']['tl_c4g_import_data']['updateImportDialog'].'\')"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ';
                    }
                    break;
                case 'key=releaseBaseData':
                    if ($importVersion != "" && $isInstalled && ((!$importAllowed && $releaseCompatible) || $importAllowed )) {
                        return '<a href="'.$this->addToUrl($href).'&id='.$id.'" title="'.$label.'" onclick="return confirm(\''.$GLOBALS['TL_LANG']['tl_c4g_import_data']['releaseImportDialog'].'\')"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ';
                    }
                    break;
                case 'key=deleteImport':
                    if ($importVersion != "") {
                        return '<a href="'.$this->addToUrl($href).'&id='.$id.'" title="'.$label.'" onclick="return confirm(\''.$GLOBALS['TL_LANG']['tl_c4g_import_data']['deleteImportDialog'].'\')"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ';
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
        $this->importDataCallback->loadBaseData(false);
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
        $key = Input::get('key');

            if ($key) {
                switch ($key) {
                    case 'importBaseData':
                        $this->importDataCallback->importBaseData();
                        break;
                    case 'updateBaseData':
                        $this->importDataCallback->updateBaseData();
                        break;
                    case 'deleteImport':
                        $this->importDataCallback->deleteBaseData();
                        break;
                    case 'importData':
                        $this->importDataCallback->loadBaseData(false);
                        break;
                    case 'releaseBaseData':
                        $this->importDataCallback->releaseBaseData();
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
        if (System::getContainer()->hasParameter('kernel.packages')) {
            $installedPackages = System::getContainer()->getParameter('kernel.packages');
            $coreVersion = $installedPackages['con4gis/core'];
            $contaoVersion = $installedPackages['contao/core-bundle'];
        }
        else {
            $contaoVersion = InstalledVersions::getVersion('contao/core-bundle');
            $coreVersion = InstalledVersions::getVersion('con4gis/core');
        }
        
        $responses = $this->importDataCallback->getCon4gisImportData("getBasedata.php", "allData", false, $coreVersion, $contaoVersion);
        $arrReturn = [];
        foreach ($responses as $response) {
            $arrReturn[$response->id] = System::getContainer()->get('contao.insert_tag.parser')->replace($response->caption);
        }
        return $arrReturn;
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
        return '<a href="https://con4gis.support"  class="' . $class . '" title="' . StringUtil::specialchars($title) . '"' . $attributes .' target="_blank" rel="noopener">' . $label . '</a><br>';
    }

    /**
     * @param $row
     * @param $label
     * @return var
     */
    public function labelCallback($row, $label){
        $userLng = BackendUser::getInstance()->language;

        $caption = $this->get_string_between($row['caption'], "::".$userLng."}}", "{{");
        $description = $this->get_string_between($row['description'], "::".$userLng."}}", "{{");
        
        $newLabel['caption'] = $caption;
        $newLabel['type'] = $row['type'];
        $newLabel['source'] = $row['source'];
        $newLabel['bundles'] = $row['bundles'];
        $newLabel['bundlesVersion'] = $row['bundlesVersion'];
        $newLabel['description'] = $description;
        $newLabel['importVersion'] = $row['importVersion'];
        $newLabel['availableVersion'] = $row['availableVersion'];

        return $newLabel;
    }

    private function get_string_between($string, $start, $end){
        $string = " ".$string;
        $ini = strpos($string,$start);
        if ($ini == 0) return "";
        $ini += strlen($start);
        $len = strpos($string,$end,$ini) - $ini;
        return substr($string,$ini,$len);
    }

}