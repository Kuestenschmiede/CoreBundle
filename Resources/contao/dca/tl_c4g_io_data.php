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
use Contao\Image;
use Contao\Input;
use Contao\StringUtil;
/**
 * Table tl_c4g_io_data
 */
$GLOBALS['TL_DCA']['tl_c4g_io_data'] = array
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
            array('tl_c4g_io_data', 'checkData'),
        ),
        'onsubmit_callback'			=> array
        (
            array('tl_c4g_io_data', 'saveData'),
        )
    ),
    'list' => array
    (
        'sorting' => array
        (
            'mode'                    => 2,
            'fields'                  => array('caption'),
            'panelLayout'             => 'search',
            'headerFields'            => array('caption', 'bundles', 'bundlesVersion', 'description', 'importVersion', 'availableVersion'),
            'icon'                    => 'bundles/con4giscore/images/be-icons/con4gis.org_dark.svg',
        ),
        'label' => array
        (
            'fields'                  => array('caption', 'bundles', 'bundlesVersion', 'description', 'importVersion', 'availableVersion'),
            'showColumns'             => true,
        ),
        'global_operations' => array
        (
//            'all' => [
//                'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
//                'href'                => 'act=select',
//                'class'               => 'header_edit_all',
//                'attributes'          => 'onclick="Backend.getScrollOffset();" accesskey="e"'
//            ],
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
                'button_callback'     => ['tl_c4g_io_data', 'con4gisIO'],
                'icon'                => 'bundles/con4giscore/images/be-icons/con4gis.org_dark.svg',
                'label'               => &$GLOBALS['TL_LANG']['tl_c4g_io_data']['con4gisIoImportData'],
            ),
        ),
        'operations' => array
        (
//            'show' => array
//            (
//                'label'               => &$GLOBALS['TL_LANG']['tl_c4g_io_data']['show'],
//                'href'                => 'act=show',
//                'icon'                => 'show.svg'
//            ),
            'import' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_c4g_io_data']['importData'],
                'href'                => 'key=importBaseData',
                'class'               => 'reload_version',
                'button_callback'     => ['tl_c4g_io_data', 'loadButtons'],
                'icon'                => 'bundles/con4giscore/images/be-icons/importData.svg'
            ),
            'update' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_c4g_io_data']['updateData'],
                'href'                => 'key=updateBaseData',
                'button_callback'     => ['tl_c4g_io_data', 'loadButtons'],
                'icon'                => 'bundles/con4giscore/images/be-icons/update_version.svg'
            ),
            'delete' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_c4g_io_data']['delete'],
                'href'                => 'key=deleteImport',
                'button_callback'     => ['tl_c4g_io_data', 'loadButtons'],
                'icon'                => 'bundles/con4giscore/images/be-icons/delete.svg',
//                'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
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
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_io_data']['id'],
            'sorting'                 => true,
            'search'                  => true,
        ),
        'tstamp' => array
        (
            'flag'                    => 6,
            'sql'                     => "int(10) NULL default 0",
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_io_data']['tstamp'],
            'default'                 => 0,
            'sorting'                 => true,
            'search'                  => true,
            'inputType'               => 'text',
        ),
        'importUuid' => array
        (
            'sql'                     => "int(10) unsigned NOT NULL",
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_io_data']['uuid'],
            'sorting'                 => true,
            'search'                  => true,
        ),
        'caption' => array
        (
            'sql'                     => "varchar(255) NOT NULL",
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_io_data']['caption'],
            'inputType'               => 'text',
            'default'                 => '',
            'inputType'               => 'text',
            'eval'                    => array('mandatory' => true),
            'sorting'                 => true,
            'search'                  => true,
            'filter'                  => true,
        ),
        'bundles' => array
        (
            'sql'                     => "text NOT NULL",
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_io_data']['bundles'],
            'inputType'               => 'text',
            'default'                 => '',
//            'eval'                    => array('mandatory' => true),
            'sorting'                 => true,
            'search'                  => true,
        ),
        'bundlesVersion' => array
        (
            'sql'                     => "text NOT NULL",
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_io_data']['bundlesVersion'],
            'inputType'               => 'text',
            'default'                 => '',
//            'eval'                    => array('mandatory' => true),
            'sorting'                 => true,
            'search'                  => true,
        ),
        'importVersion' => array
        (
            'sql'                     => "text NOT NULL",
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_io_data']['importVersion'],
            'inputType'               => 'text',
            'default'                 => '',
//            'eval'                    => array('mandatory' => true),
            'sorting'                 => true,
            'search'                  => true,
        ),
        'availableVersion' => array
        (
            'sql'                     => "text NOT NULL",
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_io_data']['availableVersion'],
            'inputType'               => 'text',
            'default'                 => '',
//            'eval'                    => array('mandatory' => true),
            'sorting'                 => true,
            'search'                  => true,
        ),
        'description' => array
        (
            'sql'                     => "text NOT NULL",
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_io_data']['description'],
            'inputType'               => 'text',
            'default'                 => '',
            'eval'                    => array('mandatory' => true),
            'sorting'                 => true,
            'search'                  => true,
        ),
        'con4gisImport' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_io_data']['con4gisImport'],
            'filter'                  => false,
            'inputType'               => 'select',
//            'eval'                    => array('mandatory' => true),
            'options_callback'        => ['tl_c4g_io_data', 'getCon4gisImportTemplates'],
            'sql'                     => "int NOT NULL default 0"
        )
    ),
);

/**
 * Class tl_c4g_io_data
 */
class tl_c4g_io_data extends Contao\Backend
{

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
        $importVersion = $arrRow['importVersion'];
        $availableVersion = $arrRow['availableVersion'];
        $bundles = explode(",", $arrRow['bundles']);
        $bundlesVersion = explode(",", $arrRow['bundlesVersion']);
        $isInstalled = false;
        $installedPackages = $this->getContainer()->getParameter('kernel.packages');

        $bundles = str_replace(" ", "", $bundles);
        $bundlesVersion = str_replace(" ", "", $bundlesVersion);

        foreach ($bundles as $bundle => $value) {
            $bundleName = $localData = $this->Database->prepare("SELECT brickkey FROM tl_c4g_bricks WHERE repository=?")->execute($value)->fetchAllAssoc();
            $version = $installedPackages['con4gis/'.$bundleName[0]['brickkey']];

            if ($version == $bundlesVersion[$bundle]) {
                $isInstalled = true;
            } else {
                $isInstalled = false;
                break;
            }

        }

        if ($href) {
            switch ($href) {
                case 'key=importBaseData':
                    if ($importVersion == "" && $isInstalled == true) {
                        return '<a href="'.$this->addToUrl($href).'&id='.$id.'" title="'.$title.'"'.$attributes.'>'.\Image::getHtml($icon, $label).'</a> ';
                    }
                    break;
                case 'key=updateBaseData':
                    if ($importVersion != "" && $availableVersion != "" && $isInstalled == true && $availableVersion != $importVersion) {
                        return '<a href="'.$this->addToUrl($href).'&id='.$id.'" title="'.$title.'" onclick="return confirm(\''.$GLOBALS['TL_LANG']['tl_c4g_io_data']['updateImportDialog'].'\')"'.$attributes.'>'.\Image::getHtml($icon, $label).'</a> ';
                    }
                    break;
                case 'key=deleteImport':
                    if ($importVersion != "") {
                        return '<a href="'.$this->addToUrl($href).'&id='.$id.'" title="'.$title.'" onclick="return confirm(\''.$GLOBALS['TL_LANG']['tl_c4g_io_data']['deleteImportDialog'].'\')"'.$attributes.'>'.\Image::getHtml($icon, $label).'</a> ';
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
        $localData = $this->Database->prepare("SELECT * FROM tl_c4g_io_data")->execute();
        $localData = $localData->fetchAllAssoc();

        if (empty($localData)) {
            foreach ($responses as $response) {
            $this->Database->prepare("INSERT INTO tl_c4g_io_data SET id=?, caption=?, description=?, bundles=?, bundlesVersion=?, availableVersion=?")->execute($response->id, $response->caption, $response->description, $response->bundles, $response->bundlesVersion, $response->version);
            }
        }
            //Update data from con4gis.io
            foreach ($localData as $data) {
                $available = false;
                foreach ($responses as $response) {
                    if ($response->id == $data['id']) {
                        $this->Database->prepare("UPDATE tl_c4g_io_data SET caption=?, description=?, bundles=?, bundlesVersion=?, availableVersion=? WHERE id=?")->execute($response->caption, $response->description, $response->bundles, $response->bundlesVersion, $response->version, $data['id']);
                        $available = true;
                    }
                }
                //Delete Import if it's not available anymore
                if (!$available) {
                    if ($data['importVersion'] != "") {
                        $this->Database->prepare("UPDATE tl_c4g_io_data SET availableVersion=? WHERE id=?")->execute("", $data['id']);
                    } else {
                        $this->Database->prepare("DELETE FROM tl_c4g_io_data WHERE id=?")->execute($data['id']);
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
                        $this->Database->prepare("INSERT INTO tl_c4g_io_data SET id=?, caption=?, description=?, bundles=?, availableVersion=?")->execute($response->id, $response->caption, $response->description, $response->bundles, $response->version);
                    }
                    $count++;
                }
            }

//        $this->Database->prepare("DELETE FROM tl_c4g_io_data")->execute();
//
//        foreach ($responses as $response) {
//            $this->Database->prepare("INSERT INTO tl_c4g_io_data SET id=?, caption=?, description=?, bundles=?, availableVersion=?")->execute($response->id, $response->caption, $response->description, $response->bundles, $response->version);
//        }
    }

    /**
     * importBaseData
     */
    public function importBaseData()
    {
        $data = $_REQUEST;

        $con4gisImportId = $data['id'];

        $importData = $this->getCon4gisImportData("getBasedata.php", "specificData", $con4gisImportId);
        $importURL = $importData[0]->url;
        $importUuid = $importData[0]->uuid;

        if(strpos($importURL,".sql")!==false) {
            $file = file_get_contents($importURL, true);
//            echo $file;
            $sqlStatements = explode(";\n", $file);
            $counter = 0;
            foreach ($sqlStatements as $sqlStatement) {
                if ($sqlStatement == "") {
                    break;
                }

//                $sqlStatement = str_replace(") VALUES", ", `uuid`) VALUES", $sqlStatement );
                $sqlStatement = str_replace("0),", "$importUuid),", $sqlStatement);
                $sqlStatement = substr_replace($sqlStatement, "$importUuid)", -2, 2);

                $this->Database->execute($sqlStatement);
            }
        }

        $this->Database->prepare("UPDATE tl_c4g_io_data SET importVersion=? WHERE id=?")->execute($importData[0]->version, $data['id']);
        $this->Database->prepare("UPDATE tl_c4g_io_data SET importUuid=? WHERE id=?")->execute($importUuid, $data['id']);

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
     * deleteBaseData
     */
    public function deleteBaseData()
    {
        $data = $_REQUEST;

        $con4gisDeleteId = $data['id'];
        $localData = $this->Database->prepare("SELECT * FROM tl_c4g_io_data WHERE id=?")->execute($con4gisDeleteId);
        $con4gisDeleteUuid = $localData->importUuid;

        $this->Database->prepare("DELETE FROM tl_c4g_maps WHERE importId=?")->execute($con4gisDeleteUuid);
        $this->Database->prepare("DELETE FROM tl_c4g_map_baselayers WHERE importId=?")->execute($con4gisDeleteUuid);
        $this->Database->prepare("DELETE FROM tl_c4g_map_filters WHERE importId=?")->execute($con4gisDeleteUuid);
        $this->Database->prepare("DELETE FROM tl_c4g_map_layer_content WHERE importId=?")->execute($con4gisDeleteUuid);
        $this->Database->prepare("DELETE FROM tl_c4g_map_locstyles WHERE importId=?")->execute($con4gisDeleteUuid);
        $this->Database->prepare("DELETE FROM tl_c4g_map_overlays WHERE importId=?")->execute($con4gisDeleteUuid);
        $this->Database->prepare("DELETE FROM tl_c4g_map_profiles WHERE importId=?")->execute($con4gisDeleteUuid);
        $this->Database->prepare("DELETE FROM tl_c4g_map_tables WHERE importId=?")->execute($con4gisDeleteUuid);
        $this->Database->prepare("DELETE FROM tl_c4g_map_themes WHERE importId=?")->execute($con4gisDeleteUuid);

        $this->Database->prepare("UPDATE tl_c4g_io_data SET importVersion=? WHERE id=?")->execute("", $con4gisDeleteId);

        $this->loadBaseData();
    }

    /**
     * saveData
     */
    public function saveData(DataContainer $dc)
    {
        $con4gisImport = $this->Input->post('con4gisImport');

        $responses = $this->getCon4gisImportData("getBasedata.php", "specificData", $con4gisImport);

        foreach ($responses as $response) {
            $objUpdate = $this->Database->prepare("UPDATE tl_c4g_io_data SET bundles=? WHERE id=?")->execute($response->bundles, $dc->id);
        }

    }

    /**
     * checkData
     */
    public function checkData()
    {
        $objSettings = \con4gis\CoreBundle\Resources\contao\models\C4gSettingsModel::findSettings();
        $key = Contao\Input::get('key');

        if ($objSettings->con4gisIoUrl && $objSettings->con4gisIoKey) {
            // Check current action
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
                }
            }

            \Contao\Message::addInfo($GLOBALS['TL_LANG']['tl_c4g_io_data']['infotext']);
        } else {

            if ($key == "deleteImport") {
                $this->deleteBaseData();
            } else {
                $localData = $this->Database->prepare("SELECT * FROM tl_c4g_io_data")->execute();
                $localData = $localData->fetchAllAssoc();

                foreach ($localData as $data) {
                    if ($data['importVersion'] != "") {
                        $this->Database->prepare("UPDATE tl_c4g_io_data SET availableVersion=? WHERE id=?")->execute("", $data['id']);
                    } else {
                        $this->Database->prepare("DELETE FROM tl_c4g_io_data WHERE id=?")->execute($data['id']);
                    }
                }
            }
            \Contao\Message::addInfo($GLOBALS['TL_LANG']['tl_c4g_io_data']['infotextNoKey']);
        }
        
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
        return '<a href="https://con4gis.io"  class="' . $class . '" title="' . StringUtil::specialchars($title) . '"' . $attributes .' target="_blank" rel="noopener">' . $label . '</a><br>';
    }

}