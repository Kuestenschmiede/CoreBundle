<?php

namespace con4gis\CoreBundle\Classes\Callback;

use con4gis\CoreBundle\Resources\contao\models\C4gLogModel;
use Contao\Backend;
use Contao\StringUtil;
use Contao\DataContainer;
use Dbafs;
use Exception;
use gutesio\DataModelBundle\Classes\ChildFullTextContentUpdater;
use Symfony\Component\Yaml\Parser;
use ZipArchive;

class C4GImportDataCallback extends Backend
{
    public function __construct()
    {
        parent::__construct();
        $this->import('BackendUser', 'User');
    }

    /**
     * loadBaseData
     */
    public function loadBaseData($cron)
    {
        $cronIds = [];
        // Get installed contao and con4gis Core version
        $installedPackages = $this->getContainer()->getParameter('kernel.packages');
        $coreVersion = $installedPackages['con4gis/core'];
        $contaoVersion = $installedPackages['contao/core-bundle'];

        // Check current action
        $responses = $this->getCon4gisImportData('getBasedata.php', 'allData', false, $coreVersion, $contaoVersion);
        foreach ($responses as $keyResponse => $respons) {
            foreach ($respons as $innerKeyResponse => $innerResponse) {
                $responses[$keyResponse]->$innerKeyResponse = str_replace('"', '', $innerResponse);
            }
        }
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
        $localData = $this->Database->prepare('SELECT * FROM tl_c4g_import_data')->execute();
        $localData = $localData->fetchAllAssoc();

        if (empty($localData)) {
            foreach ($responses as $response) {
                if ($this->checkImportResponse($response)) {
                    if ($cron) {
                        $this->Database->prepare('INSERT INTO tl_c4g_import_data SET id=?, bundles=?, bundlesVersion=?, availableVersion=?, type=?, source=?')->execute($response->id, $response->bundles, $response->bundlesVersion, $response->version, $response->type, $response->source);
                        if ($response->type == 'gutesio') {
                            $cronIds[] = $response->id;
                        }
                    } else {
                        $this->Database->prepare('INSERT INTO tl_c4g_import_data SET id=?, caption=?, description=?, bundles=?, bundlesVersion=?, availableVersion=?, type=?, source=?')->execute($response->id, $this->replaceInsertTags($response->caption), $this->replaceInsertTags($response->description), $response->bundles, $response->bundlesVersion, $response->version, $response->type, $response->source);
                    }
                }
            }
        }
        //Update data from con4gis.io
        foreach ($localData as $data) {
            $available = false;
            foreach ($responses as $response) {
                if ($response->id == $data['id']) {
                    if ($this->checkImportResponse($response)) {
                        if ($cron) {
                            $dbExecute = $this->Database->prepare('UPDATE tl_c4g_import_data SET bundles=?, bundlesVersion=?, availableVersion=?, type=?, source=? WHERE id=?')->execute($response->bundles, $response->bundlesVersion, $response->version, $response->type, $response->source, $data['id']);
                            if ($response->type == 'gutesio') {
                                $cronIds[] = $response->id;
                            }
                        } else {
                            $dbExecute = $this->Database->prepare('UPDATE tl_c4g_import_data SET caption=?, description=?, bundles=?, bundlesVersion=?, availableVersion=?, type=?, source=? WHERE id=?')->execute($this->replaceInsertTags($response->caption), $this->replaceInsertTags($response->description), $response->bundles, $response->bundlesVersion, $response->version, $response->type, $response->source, $data['id']);
                        }
                    } elseif ($data['importVersion'] == '') {
                        if ($data['id'] != 0 or $data['id'] != '') {
                            $dbExecute = $this->Database->prepare('DELETE FROM tl_c4g_import_data WHERE id=?')->execute($data['id']);
                        }
                    } elseif ($data['importVersion'] != '') {
                        $dbExecute = $this->Database->prepare('UPDATE tl_c4g_import_data SET availableVersion=? WHERE id=?')->execute('', $data['id']);
                    }
                    $available = true;
                }
            }
            //Delete Import if it's not available anymore
            if (!$available) {
                if ($data['importVersion'] != '') {
                    $this->Database->prepare('UPDATE tl_c4g_import_data SET availableVersion=? WHERE id=?')->execute('', $data['id']);
                } else {
                    if ($data['id'] != 0 or $data['id'] != '') {
                        $this->Database->prepare('DELETE FROM tl_c4g_import_data WHERE id=?')->execute($data['id']);
                    } else {
                        C4gLogModel::addLogEntry('core', 'Error deleting unavailable import: wrong id set!');
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
                    if ($this->checkImportResponse($response)) {
                        $this->Database->prepare('INSERT INTO tl_c4g_import_data SET id=?, caption=?, description=?, bundles=?, bundlesVersion=?, availableVersion=?, type=?, source=?')->execute($response->id, $this->replaceInsertTags($response->caption), $this->replaceInsertTags($response->description), $response->bundles, $response->bundlesVersion, $response->version, $response->type, $response->source);
                    }
                }
                $count++;
            }
        }

        if ($cron) {
            return $cronIds;
        }
    }

    public function getStringBetween($string, $start, $end)
    {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) {
            return '';
        }
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;

        return substr($string, $ini, $len);
    }

    /**
     * importBaseData
     */
    public function importBaseData($importId = false)
    {
        if ($this->importRunning()) {
            return false;
        }
        if ($importId) {
            $con4gisImportId = $importId;
            $gutesImportData = $this->Database->prepare('SELECT importVersion, availableVersion FROM tl_c4g_import_data WHERE id=?')->execute($con4gisImportId)->fetchAssoc();
            if ($gutesImportData['importVersion'] >= $gutesImportData['availableVersion']) {
                return false;
            }
        } else {
            $data = $_REQUEST;
            $con4gisImportId = $data['id'];
        }

        $this->importRunning(true, $con4gisImportId);

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

        $gutesIoImport = false;

        if ($availableLocal) {
            $imagePath = './../files' . $importData['images']['path'];
            $c4gPath = './../vendor/con4gis/' . $importData['general']['bundle'] . '/Resources/con4gis/' . $importData['general']['filename'];
            $cache = './../var/cache/prod/con4gis/io-data/' . str_replace('.c4g', '', $importData['general']['filename']);

            if ($importData['import']['source'] == "gutesio") {
                $gutesIoImport = true;
            }

            $zip = new ZipArchive;
            if ($zip->open($c4gPath) === true) {
                $zip->extractTo($cache);
                $zip->close();

                $images = array_slice(scandir($cache . '/images/'), 2);
                mkdir($imagePath, 0770, true);
                foreach ($images as $image) {
                    copy($cache . '/images/' . $image, $imagePath . '/' . $image);
                }
                $objFolder = new \Contao\Folder('files/con4gis_import_data');
                //if (!$objFolder->isUnprotected()) { //Rework >= Contao 4.7
                $objFolder->unprotect();
                //}
                $objFolder = new \Contao\Folder('files' . $importData['images']['path']);
                $objFolder->unprotect();

//                $this->makeFolderAvailableForPublic($imagePath);
//                $this->makeFolderAvailableForPublic("./../files/con4gis_import_data");
            }

            $file = file_get_contents($cache . '/data/' . str_replace('.c4g', '.json', $importData['general']['filename']));

            $sqlStatements = $this->getSqlFromJson($file, $importData['import']['uuid']);

            foreach ($sqlStatements as $sqlStatement) {
                if ($sqlStatement == '') {
                    break;
                }

                try {
                    $this->Database->query($sqlStatement);
                } catch (Exception $e) {
                    C4gLogModel::addLogEntry('core', 'Error while executing SQL-Import: ' . $e->getMessage());
                }
            }
            $this->Database->prepare('UPDATE tl_c4g_import_data SET importVersion=?WHERE id=?')->execute($importData['import']['version'], $con4gisImportId);
            $this->Database->prepare('UPDATE tl_c4g_import_data SET importUuid=? WHERE id=?')->execute($localImportData['import']['uuid'], $con4gisImportId);
            $this->Database->prepare('UPDATE tl_c4g_import_data SET importFilePath=? WHERE id=?')->execute($localImportData['images']['path'], $con4gisImportId);

            if ($gutesIoImport) {
                $contentUpdate = new ChildFullTextContentUpdater();
                $contentUpdate->update();
            }

            $objFolder = new \Contao\Folder('var/cache/prod/con4gis/io-data/');
            $objFolder->purge();
            $objFolder->delete();
//            $this->recursiveRemoveDirectory($cache);

//      lokaler Import Ende
        } elseif (!$availableLocal) {
            $objSettings = \con4gis\CoreBundle\Resources\contao\models\C4gSettingsModel::findSettings();
            $basedataUrl = rtrim($objSettings->con4gisIoUrl, '/') . '/' . 'getBasedata.php';
            $basedataUrl .= '?key=' . $objSettings->con4gisIoKey;
            $basedataUrl .= '&mode=' . 'ioData';
            $basedataUrl .= '&data=' . $con4gisImportId;
            $downloadPath = './../var/cache/prod/con4gis/io-data/';
            $filename = 'io-data-proxy.c4g';
            $downloadFile = $downloadPath.$filename;
            if ( file_exists( $downloadPath ) && is_dir( $downloadPath ) ) {
                $this->recursiveRemoveDirectory($downloadPath);
            }
            mkdir($downloadPath, 0770, true);
            $this->download($basedataUrl, $downloadFile);

            $zip = zip_open($downloadPath . $filename);

            if ($zip) {
                while ($zip_entry = zip_read($zip)) {
                    if (zip_entry_name($zip_entry) == 'io-data.yml') {
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

            if ($importData['import']['source'] == "gutesio") {
                $gutesIoImport = true;
            }

            $imagePath = './../files' . $importData['images']['path'];
//            $c4gPath = "./../vendor/con4gis/".$importData['general']['bundle']."/Resources/con4gis/".$importData['general']['filename'];
            $cache = './../var/cache/prod/con4gis/io-data/' . str_replace('.c4g', '', $importData['general']['filename']);

            $zip = new ZipArchive;
            if ($zip->open($downloadPath . $filename) === true) {
                $zip->extractTo($cache);
                $zip->close();

                $images = array_slice(scandir($cache . '/images/'), 2);
                mkdir($imagePath, 0770, true);
                foreach ($images as $image) {
                    copy($cache . '/images/' . $image, $imagePath . '/' . $image);
                }
                $objFolder = new \Contao\Folder('files/con4gis_import_data');
                if (!$objFolder->isUnprotected()) {
                    $objFolder->unprotect();
                }
                $objFolder = new \Contao\Folder('files' . $importData['images']['path']);
                $objFolder->unprotect();
//                $this->makeFolderAvailableForPublic($imagePath);
//                $this->makeFolderAvailableForPublic("./../files/con4gis_import_data");
            }
            $file = file_get_contents($cache . '/data/' . str_replace('.c4g', '.json', $importData['general']['filename']));
            $sqlStatements = $this->getSqlFromJson($file, $importData['import']['uuid']);

            foreach ($sqlStatements as $sqlStatement) {
                if ($sqlStatement == '') {
                    break;
                }

                try {
                    $this->Database->query($sqlStatement);
                } catch (Exception $e) {
                    C4gLogModel::addLogEntry('core', 'Error while executing SQL-Import: ' . $e->getMessage());
                }
            }
            $this->Database->prepare('UPDATE tl_c4g_import_data SET importVersion=?WHERE id=?')->execute($importData['import']['version'], $con4gisImportId);
            $this->Database->prepare('UPDATE tl_c4g_import_data SET importUuid=? WHERE id=?')->execute($importData['import']['uuid'], $con4gisImportId);
            $this->Database->prepare('UPDATE tl_c4g_import_data SET importFilePath=? WHERE id=?')->execute($importData['images']['path'], $con4gisImportId);

            if ($gutesIoImport) {
                $contentUpdate = new ChildFullTextContentUpdater();
                $contentUpdate->update();
            }

            $objFolder = new \Contao\Folder('var/cache/prod/con4gis/io-data/');
            $objFolder->purge();
            $objFolder->delete();
//            $this->recursiveRemoveDirectory("./../var/cache/prod/con4gis/io-data/".str_replace(".c4g", "", $importData['general']['filename']));
//            unlink("./../var/cache/prod/con4gis/io-data/".$filename);
        }
        //Generate Symlinks and sync filesystem
        $this->import('Contao\Automator', 'Automator');
        $this->Automator->generateSymlinks();

        Dbafs::syncFiles();

        $this->importRunning(false, $con4gisImportId);
    }

    /**
     * updateBaseData
     */
    public function updateBaseData($importId = false)
    {
        if ($this->importRunning()) {
            return false;
        }

        if ($importId) {
            $gutesImportData = $this->Database->prepare('SELECT importVersion, availableVersion FROM tl_c4g_import_data WHERE id=?')->execute($importId)->fetchAssoc();
            if ($gutesImportData['importVersion'] >= $gutesImportData['availableVersion']) {
                return false;
            }
        }

        // Check current action
        $this->deleteBaseData($importId);
        $this->importBaseData($importId);
    }

    /**
     * releaseBaseData
     */
    public function releaseBaseData()
    {
        // Check current action
        $data = $_REQUEST;
        $con4gisDeleteId = $data['id'];
        $localData = $this->Database->prepare('SELECT * FROM tl_c4g_import_data WHERE id=?')->execute($con4gisDeleteId);
        $con4gisReleaseUuid = $localData->importUuid;
        $con4gisReleaseBundles = $localData->bundles;

        if ($con4gisReleaseUuid != 0 && $con4gisReleaseUuid != '' && $con4gisReleaseUuid >= 6) {
            //Release import data
            $tables = $this->Database->listTables();

            foreach ($tables as $table) {
                if (strpos($table, 'tl_c4g_') !== false or strpos($table, 'tl_gutesio_') !== false) {
                    if ($this->Database->fieldExists('importId', $table)) {
                        $this->Database->prepare("UPDATE $table SET importId=? WHERE importId=?")->execute('0', $con4gisReleaseUuid);
                    }
                }
            }

            $this->Database->prepare('UPDATE tl_c4g_import_data SET importVersion=? WHERE id=?')->execute('', $con4gisDeleteId);
            $this->Database->prepare('UPDATE tl_c4g_import_data SET importUuid=? WHERE id=?')->execute('0', $con4gisDeleteId);
            $this->Database->prepare('UPDATE tl_c4g_import_data SET importfilePath=? WHERE id=?')->execute('', $con4gisDeleteId);

            $this->loadBaseData(false);
        } else {
            C4gLogModel::addLogEntry('core', 'Error releasing unavailable import: wrong id set!');
        }
    }

    /**
     * deleteBaseData
     */
    public function deleteBaseData($importId = false)
    {
        if ($this->importRunning()) {
            return false;
        }

        if ($importId) {
            $con4gisDeleteId = $importId;
            $gutesImportData = $this->Database->prepare('SELECT type FROM tl_c4g_import_data WHERE id=?')->execute($con4gisDeleteId)->fetchAssoc();
            if ($gutesImportData['type'] != 'gutesio' or $gutesImportData == null) {
                return false;
            }
        } else {
            $data = $_REQUEST;
            $con4gisDeleteId = $data['id'];
        }

        $this->importRunning(true, $con4gisDeleteId);

        $localData = $this->Database->prepare('SELECT * FROM tl_c4g_import_data WHERE id=?')->execute($con4gisDeleteId);
        $con4gisDeleteUuid = $localData->importUuid;
        $con4gisDeleteBundles = $localData->bundles;
        $con4gisDeletePath = $localData->importFilePath;
        $con4gisDeleteDirectory = './../files' . $con4gisDeletePath . '/';
        $con4gisDeleteUuidLength = strlen($con4gisDeleteUuid);

        if ($con4gisDeleteUuid != 0 && $con4gisDeleteUuid != '' && $con4gisDeleteUuidLength >= 6) {
            if ($con4gisDeletePath != '') {
                if (is_dir($con4gisDeleteDirectory)) {
                    unlink($con4gisDeleteDirectory . '/.public');
                    if (strpos($con4gisDeleteDirectory, '/files/con4gis_import_data/')) {
                        $objFolder = new \Contao\Folder('files' . $con4gisDeletePath);
                        if (!$objFolder->isEmpty()) {
                            $objFolder->purge();
                        }
                        $objFolder->delete();
                        $con4gisImportFolderScan = array_diff(scandir('./../files/con4gis_import_data'), ['.', '..']);
                        if (count($con4gisImportFolderScan) == 1) {
                            if (in_array('.public', $con4gisImportFolderScan)) {
                                $objFolder = new \Contao\Folder('files/con4gis_import_data');
                                $objFolder->unprotect();
                                $objFolder->delete();
                            }
                        }
                        $this->import('Contao\Automator', 'Automator');
                        $this->Automator->generateSymlinks();
                        //Sync filesystem
                        Dbafs::syncFiles();
                    } else {
                        C4gLogModel::addLogEntry('core', 'Could not delete import directory: Wrong path!');
                    }
                }
            }

            //Delete import data
            $tables = $this->Database->listTables();

            foreach ($tables as $table) {
                if (strpos($table, 'tl_c4g_') !== false or strpos($table, 'tl_gutesio_') !== false) {
                    if ($this->Database->fieldExists('importId', $table)) {
                        $this->Database->prepare("DELETE FROM $table WHERE importId=?")->execute($con4gisDeleteUuid);
                    }
                }
            }

            $this->Database->prepare('UPDATE tl_c4g_import_data SET importVersion=? WHERE id=?')->execute('', $con4gisDeleteId);
            $this->Database->prepare('UPDATE tl_c4g_import_data SET importUuid=? WHERE id=?')->execute('0', $con4gisDeleteId);
            $this->Database->prepare('UPDATE tl_c4g_import_data SET importfilePath=? WHERE id=?')->execute('', $con4gisDeleteId);

            $this->loadBaseData(false);
        } else {
            C4gLogModel::addLogEntry('core', 'Error deleting unavailable import: wrong id set!');
        }

        $this->importRunning(false, $con4gisDeleteId);
    }

    public function download($remoteFile, $localFile) {
        $fp = fopen($localFile, 'w');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $remoteFile);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER , false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION , true);
        curl_setopt($ch, CURLOPT_AUTOREFERER , true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.2.12) Gecko/20101026 Firefox/3.6.12');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        $con = curl_exec($ch);
        curl_close($ch);
        fclose($fp);
    }

    public function strposa($haystack, $needles = [], $offset = 0)
    {
        $chr = [];
        foreach ($needles as $needle) {
            $res = strpos($haystack, $needle, $offset);
            if ($res !== false) {
                $chr[$needle] = $res;
            }
        }
        if (empty($chr)) {
            return false;
        }

        return min($chr);
    }

    /**
     * saveData
     */
    public function saveData(DataContainer $dc)
    {
        $con4gisImport = $this->Input->post('con4gisImport');

        $responses = $this->getCon4gisImportData('getBasedata.php', 'specificData', $con4gisImport);

        foreach ($responses as $response) {
            $objUpdate = $this->Database->prepare('UPDATE tl_c4g_import_data SET bundles=? WHERE id=?')->execute($response->bundles, $dc->id);
        }
    }

    /**
     * getCon4gisImportData
     */
    public function getCon4gisImportData($importData, $mode, $data = false, $coreVersion = false, $contaoVersion = false)
    {
        $objSettings = \con4gis\CoreBundle\Resources\contao\models\C4gSettingsModel::findSettings();
        if ($objSettings->con4gisIoUrl && $objSettings->con4gisIoKey) {
            $basedataUrl = rtrim($objSettings->con4gisIoUrl, '/') . '/' . $importData;
            $basedataUrl .= '?key=' . $objSettings->con4gisIoKey;
            $basedataUrl .= '&mode=' . $mode;
            if (isset($data)) {
                $basedataUrl .= '&data=' . str_replace(' ', '%20', $data);
            }
            if (isset($coreVersion)) {
                $basedataUrl .= '&coreVersion=' . str_replace(' ', '%20', $coreVersion);
            }
            if (isset($contaoVersion)) {
                $basedataUrl .= '&contaoVersion=' . str_replace(' ', '%20', $contaoVersion);
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
            }

            return $responses = [];
        }
    }

    public function getLocalIoData()
    {
        $arrBasedataFolders = [
            'maps' => './../vendor/con4gis/maps/Resources/con4gis',
            'visualization' => './../vendor/con4gis/visualization/Resources/con4gis',
            'core' => './../vendor/con4gis/core/Resources/con4gis',
            'data' => './../vendor/con4gis/data/Resources/con4gis',
            'firefighter' => './../vendor/con4gis/firefighter/Resources/con4gis',
            'operator' => './../vendor/gutesio/operator/Resources/gutesio',
        ];

        $dir = getcwd();
        $basedataFiles = [];

        foreach ($arrBasedataFolders as $arrBasedataFolder => $value ) {
            if ( file_exists( $value ) && is_dir( $value ) ) {
                $basedataFiles[$arrBasedataFolder] = array_slice(scandir($value), 2);
                foreach ($basedataFiles[$arrBasedataFolder] as $basedataFile => $file) {
                    $basedataFiles[$arrBasedataFolder][$basedataFile] = $value.'/'.$file;
                }
            }
        }

        $newYamlConfigArray = [];
        $count = 0;
        foreach ($basedataFiles as $basedataFile) {
            foreach ($basedataFile as $importFile) {
                $zip = zip_open($importFile);

                if ($zip) {
                    while ($zip_entry = zip_read($zip)) {
                        if (zip_entry_name($zip_entry) == 'io-data.yml') {
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
        foreach (glob("{$directory}/*") as $file) {
            if (is_dir($file)) {
                $this->recursiveRemoveDirectory($file);
            } elseif (!is_link($file)) {
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
        return '<a href="https://con4gis.io/blaupausen"  class="' . $class . '" title="' . StringUtil::specialchars($title) . '"' . $attributes . ' target="_blank" rel="noopener">' . $label . '</a><br>';
    }

    public function makeFolderAvailableForPublic($href)
    {
        $handle = fopen($href . '/.public', 'w');
        $this->import('Contao\Automator', 'Automator');
        $this->Automator->generateSymlinks();
    }

    public function getSqlFromJson($file, $uuid)
    {
        $filesMessageCount = 0;
        $importId = $uuid;
        $uuid = substr($uuid, 0, 4);
        $jsonFile = (array) json_decode($file);
        $jsonSize = sizeof($jsonFile);
        $sqlStatements = [];
        $relations = array_slice($jsonFile, -1, 1);
        $relationTables = [];
        $dbRelation = [];

        foreach ($relations['relations'] as $key => $value) {
            $firstTable = explode('.', $key);

            if (!in_array($firstTable[0], $relationTables)) {
                $relationTables[] = $firstTable[0];
            }

            $dbRelation[$firstTable[0]][] = $firstTable[1];
        }

        //Check for previous released import
        $firstImportTable = array_key_first($jsonFile);
        $newId = $uuid;
        $importUuidCheck = '%' . $uuid . '%';

        if (substr($jsonFile['tl_files'][0]->uuid, 0, 2) == '0x') {
            $firstTlFilesUuid = substr($jsonFile['tl_files'][0]->uuid, 2);
        } else {
            $firstTlFilesUuid = $jsonFile['tl_files'][0]->uuid;
        }
        $firstTableQuery = $this->Database->prepare("SELECT id FROM $firstImportTable WHERE id LIKE ?")->execute($importUuidCheck)->fetchAllAssoc();
        $tlFilesTableQuery = $this->Database->prepare('SELECT uuid FROM tl_files WHERE HEX(uuid) LIKE ?')->execute('%' . $firstTlFilesUuid . '%')->fetchAllAssoc();

        while ($firstTableQuery) {
            $newId = rand(1001, 9999);
            $firstTableQuery = $this->Database->prepare("SELECT id FROM $firstImportTable WHERE id LIKE ?")->execute($newId)->fetchAllAssoc();
        }
        foreach ($jsonFile as $importDB => $importDatasets) {
            if ($importDB == 'relations') {
                break;
            }

            $dbFields = $this->Database->getFieldNames($importDB);
            foreach ($importDatasets as $importDataset) {
                $sqlStatement = '';
                $importDataset = (array) $importDataset;
                if (!array_key_exists('importId', $importDataset)) {
                    $importDataset['importId'] = $importId;
                }
                foreach ($importDataset as $importDbField => $importDbValue) {
                    if ($importDbField == 'id') {
                        $importDbValue = $this->prepend($newId, $importDbValue);
                    } elseif ($importDbField == 'importId') {
                        $importDbValue = $importId;
                    } elseif (in_array($importDB, $relationTables)) {
                        if (in_array($importDbField, $dbRelation[$importDB])) {
                            if ($importDbValue != '0') {
                                if (substr($importDbValue, 0, 2) == '0x' && $importDB != 'tl_files') {
                                    $unserial = hex2bin(substr($importDbValue, 2));

                                    if (strpos($unserial, '{')) {
                                        $unserial = unserialize($unserial);
                                        $unserial = $this->replaceId($unserial, $newId);
                                        $newImportDbValue = serialize($unserial);
                                        $newImportDbValue = bin2hex($newImportDbValue);
                                        $newImportDbValue = $this->prepend('0x', $newImportDbValue);
                                        $importDbValue = $newImportDbValue;
                                    } else {
                                        $newImportDbValue = $this->prepend($newId, $unserial);
                                        $newImportDbValue = bin2hex($newImportDbValue);
                                        $newImportDbValue = $this->prepend('0x', $newImportDbValue);
                                        $importDbValue = $newImportDbValue;
                                    }
                                } elseif (substr($importDbValue, 0, 2) == 'a:') {
                                    $importDbValue = str_replace('\"', '"', $importDbValue);
                                    $unserial = StringUtil::deserialize($importDbValue);
                                    $unserial = $this->replaceId($unserial, $newId);
                                    $newImportDbValue = serialize($unserial);
                                    $importDbValue = $newImportDbValue;
                                } elseif (strpos($importDbValue, '{')) {
                                    $unserial = hex2bin(substr($importDbValue, 2));
                                    $unserial = unserialize($unserial);
                                    $unserial = $this->replaceId($unserial, $newId);
                                    $newImportDbValue = serialize($unserial);
                                    $newImportDbValue = bin2hex($newImportDbValue);
                                    $newImportDbValue = $this->prepend('0x', $newImportDbValue);
                                    $importDbValue = $newImportDbValue;
                                } elseif (is_numeric($importDbValue)) {
                                    $newImportDbValue = $this->prepend($newId, $importDbValue);
                                    $importDbValue = $newImportDbValue;
                                }
                            }
                        }
                    }

                    if (in_array($importDbField, $dbFields)) {
                        if ($importDB == 'tl_files' && $importDbField == 'id') {
                            $sqlStatement = $sqlStatement . '';
                        } else {
                            if ($sqlStatement == '' && substr($importDbValue, 0, 2) == '0x') {
                                $sqlStatement = 'INSERT INTO `' . $importDB . '` (`' . $importDbField . '`) VALUES (' . $importDbValue . ');;';
                            } elseif ($sqlStatement == '' && substr($importDbValue, 0, 2) != '0x') {
                                $sqlStatement = 'INSERT INTO `' . $importDB . '` (`' . $importDbField . "`) VALUES ('" . $importDbValue . "');;";
                            } elseif ($sqlStatement == '' && $importDbValue === null) {
                                $sqlStatement = 'INSERT INTO `' . $importDB . '` (`' . $importDbField . '`) VALUES (NULL);;';
                            } elseif (substr($importDbValue, 0, 2) == '0x') {
                                $sqlStatement = str_replace(') VALUES', ", `$importDbField`) VALUES", $sqlStatement);
                                $sqlStatement = str_replace(');;', ", $importDbValue);;", $sqlStatement);
                            } elseif ($importDbValue === null) {
                                $sqlStatement = str_replace(') VALUES', ", `$importDbField`) VALUES", $sqlStatement);
                                $sqlStatement = str_replace(');;', ', NULL);;', $sqlStatement);
                            } else {
                                $sqlStatement = str_replace(') VALUES', ", `$importDbField`) VALUES", $sqlStatement);
                                $sqlStatement = str_replace(');;', ", '$importDbValue');;", $sqlStatement);
                            }
                        }
                    } else {
                        if ($importDB != 'tl_files' && $importDbField != 'id') {
                            C4gLogModel::addLogEntry('core', 'The import database field <b>' . $importDbField . '</b> is not in the database <b>' . $importDB . '</b>.');
                        }
                    }
                }
                if ($importDB == 'tl_files' && $tlFilesTableQuery) {
                    if ($filesMessageCount == 0) {
                        C4gLogModel::addLogEntry('core', 'Files already imported. tl_files will not be imported');
                    }
                } else {
                    $sqlStatement = str_replace(');;', ');', $sqlStatement);
                    $sqlStatements[] = $sqlStatement;
                }
            }
        }

        return $sqlStatements;
    }

    public function prepend($string, $chunk)
    {
        if (!empty($chunk) && isset($chunk)) {
            return $string . $chunk;
        }

        return $string;
    }

    public function checkImportResponse($response)
    {
        $response = (array) $response;
        $keys = ['cloud_import', 'uuid', 'id', 'caption', 'description', 'version', 'bundles', 'bundlesVersion', 'source', 'type'];
        foreach ($keys as $key) {
            if (array_key_exists($key, $response)) {
                $checkBool = true;
            } else {
                $checkBool = false;

                break;
            }
        }
        if ($checkBool) {
            return true;
        }
        C4gLogModel::addLogEntry('core', 'Could not read import file or import file is not complete.');

        return false;
    }

    public function replaceId($array, $newId)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = $this->replaceId($value, $newId);
            }

            if (is_numeric($value)) {
                $array[$key] = $this->prepend($newId, $value);
            }
        }

        return $array;
    }

    public function importRunning($running = false, $id = 0) {
        if ($id == 0) {
            $importRunning = $this->Database->prepare("SELECT id FROM tl_c4g_import_data WHERE importRunning = '1'")->execute()->fetchAllAssoc();
            if ($importRunning) {
                return true;
            } else {
                return false;
            }
        } elseif ($id != 0) {
            if ($running) {
                $this->Database->prepare("UPDATE tl_c4g_import_data SET importRunning = '1' WHERE id=?")->execute($id);
            } else {
                $this->Database->prepare("UPDATE tl_c4g_import_data SET importRunning = '' WHERE id=?")->execute($id);
            }
        }
    }
}
