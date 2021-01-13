<?php

namespace con4gis\CoreBundle\Classes\Callback;

use con4gis\CoreBundle\Resources\contao\models\C4gLogModel;
use Contao\Backend;
use Contao\FilesModel;
use Contao\Search;
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
        if ($responses) {
            foreach ($responses as $keyResponse => $respons) {
                foreach ($respons as $innerKeyResponse => $innerResponse) {
                    $responses[$keyResponse]->$innerKeyResponse = str_replace('"', '', $innerResponse);
                }
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
                if (!$response->tables) {
                    $response->tables = "";
                }
                if ($this->checkImportResponse($response)) {
                    if ($cron) {
                        $this->Database->prepare('INSERT INTO tl_c4g_import_data SET id=?, bundles=?, bundlesVersion=?, availableVersion=?, type=?, source=?, importTables=?')->execute($response->id, $response->bundles, $response->bundlesVersion, $response->version, $response->type, $response->source, $response->tables);
                        if (strpos($response->tables, $response->type) !== false) {
                            $cronIds[] = $response->id;
                        }
                    } else {
                        $this->Database->prepare('INSERT INTO tl_c4g_import_data SET id=?, caption=?, description=?, bundles=?, bundlesVersion=?, availableVersion=?, type=?, source=?, importTables=?')->execute($response->id, $this->replaceInsertTags($response->caption), $this->replaceInsertTags($response->description), $response->bundles, $response->bundlesVersion, $response->version, $response->type, $response->source, $response->tables);
                    }
                }
            }
        }
        //Update data from con4gis.io
        foreach ($localData as $data) {
            $available = false;
            foreach ($responses as $response) {
                if (!$response->tables) {
                    $response->tables = "";
                }
                if ($response->id == $data['id']) {
                    if ($this->checkImportResponse($response)) {
                        if ($cron) {
                            $dbExecute = $this->Database->prepare('UPDATE tl_c4g_import_data SET bundles=?, bundlesVersion=?, availableVersion=?, type=?, source=?, importTables=? WHERE id=?')->execute($response->bundles, $response->bundlesVersion, $response->version, $response->type, $response->source, $response->tables, $data['id']);
                            if (strpos($response->tables, $response->type)) {
                                $cronIds[] = $response->id;
                            }
                        } else {
                            $dbExecute = $this->Database->prepare('UPDATE tl_c4g_import_data SET caption=?, description=?, bundles=?, bundlesVersion=?, availableVersion=?, type=?, source=?, importTables=? WHERE id=?')->execute($this->replaceInsertTags($response->caption), $this->replaceInsertTags($response->description), $response->bundles, $response->bundlesVersion, $response->version, $response->type, $response->source, $response->tables, $data['id']);
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
            if (!$response->tables) {
                $response->tables = "";
            }
            $count = 0;
            $arrayLength = count($localData) - 1;
            foreach ($localData as $data) {
                if ($data['id'] == $response->id) {
                    break;
                }
                if ($data['id'] != $response->id && $count == $arrayLength) {
                    if ($this->checkImportResponse($response)) {
                        $this->Database->prepare('INSERT INTO tl_c4g_import_data SET id=?, caption=?, description=?, bundles=?, bundlesVersion=?, availableVersion=?, type=?, source=?, importTables=?')->execute($response->id, $this->replaceInsertTags($response->caption), $this->replaceInsertTags($response->description), $response->bundles, $response->bundlesVersion, $response->version, $response->type, $response->source, $response->tables);
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
            \Contao\Message::addError($GLOBALS['TL_LANG']['tl_c4g_import_data']['importRunning']);
            PageRedirect::redirect("/contao?do=c4g_io_data");
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
            $cache = './../files/con4gis_import_data/io-data/' . str_replace('.c4g', '', $importData['general']['filename']);

            if ($importData['import']['source'] == 'gutesio') {
                $gutesIoImport = true;
            }

            $alreadyImported = $this->Database->prepare('SELECT importVersion FROM tl_c4g_import_data WHERE id=?')->execute($con4gisImportId)->fetchAssoc();
            if ($alreadyImported['importVersion'] != '') {
                if ($importId) {
                    $deleted = $this->deleteBaseData($importId, true);
                    if (!$deleted) {
                        $this->importRunning(false, $con4gisImportId);
                        \Contao\Message::addError($GLOBALS['TL_LANG']['tl_c4g_import_data']['errorDeleteImports']);
                        PageRedirect::redirect("/contao?do=c4g_io_data");
                        return false;
                    }
                } else {
                    $deleted = $this->deleteBaseData(false, true);
                    if (!$deleted) {
                        $this->importRunning(false, $con4gisImportId);
                        \Contao\Message::addError($GLOBALS['TL_LANG']['tl_c4g_import_data']['errorDeleteImports']);
                        PageRedirect::redirect("/contao?do=c4g_io_data");
                        return false;
                    }
                }
            } elseif (($alreadyImported['importVersion'] == '' || !$alreadyImported) && $importId) {
                C4gLogModel::addLogEntry('core', 'Cant update automaticly. Import not found in database. Abort import.');
                $this->importRunning(false, $con4gisImportId);
                return false;
            }

            $zip = new ZipArchive;
            if ($zip->open($c4gPath) === true) {
                $zip->extractTo($cache);
                $zip->close();

                mkdir($imagePath, 0770, true);
                $this->cpy($cache."/images", $imagePath);
                $objFolder = new \Contao\Folder('files/con4gis_import_data');
                //if (!$objFolder->isUnprotected()) { //Rework >= Contao 4.7
                $objFolder->unprotect();
                //}
                $objFolder = new \Contao\Folder('files' . $importData['images']['path']);
                $objFolder->unprotect();

            }

            $file = file_get_contents($cache . '/data/' . str_replace('.c4g', '.json', $importData['general']['filename']));

            $sqlStatements = $this->getSqlFromJson($file, $importData['import']['uuid'], $con4gisImportId);

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

            $objFolder = new \Contao\Folder('files/con4gis_import_data/io-data/');
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
            $downloadPath = './../files/con4gis_import_data/io-data/';
            $filename = 'io-data-proxy.c4g';
            $downloadFile = $downloadPath . $filename;
            if (file_exists($downloadPath) && is_dir($downloadPath)) {
                $this->recursiveRemoveDirectory($downloadPath);
            }

            mkdir($downloadPath, 0770, true);
            $downloadSuccess = $this->download($basedataUrl, $downloadFile);
            if (!$downloadSuccess) {
                $this->importRunning(false, $con4gisImportId);
                PageRedirect::redirect("/contao?do=c4g_io_data");
                return false;
            }
            $alreadyImported = $this->Database->prepare('SELECT importVersion FROM tl_c4g_import_data WHERE id=?')->execute($con4gisImportId)->fetchAssoc();
            if ($alreadyImported['importVersion'] != '') {
                if ($importId) {
                    $deleted = $this->deleteBaseData($importId, true);
                    if (!$deleted) {
                        $this->importRunning(false, $con4gisImportId);
                        \Contao\Message::addError($GLOBALS['TL_LANG']['tl_c4g_import_data']['errorDeleteImports']);
                        return false;
                    }
                } else {
                    $deleted = $this->deleteBaseData(false, true);
                    if (!$deleted) {
                        $this->importRunning(false, $con4gisImportId);
                        \Contao\Message::addError($GLOBALS['TL_LANG']['tl_c4g_import_data']['errorDeleteImports']);
                        PageRedirect::redirect("/contao?do=c4g_io_data");
                        return false;
                    }
                }
            } elseif (($alreadyImported['importVersion'] == '' || !$alreadyImported) && $importId) {
                C4gLogModel::addLogEntry('core', 'Cant update automaticly. Import information not found in database. Abort import.');
                $this->importRunning(false, $con4gisImportId);
                return false;
            }

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

            if ($importData['import']['source'] == 'gutesio') {
                $gutesIoImport = true;
            }

            $imagePath = './../files' . $importData['images']['path'];
//            $c4gPath = "./../vendor/con4gis/".$importData['general']['bundle']."/Resources/con4gis/".$importData['general']['filename'];
            $cache = './../files/con4gis_import_data/io-data/' . str_replace('.c4g', '', $importData['general']['filename']);

            $zip = new ZipArchive;
            if ($zip->open($downloadPath . $filename) === true) {
                $zip->extractTo($cache);
                $zip->close();

                mkdir($imagePath, 0770, true);
                $this->cpy($cache."/images", $imagePath);
                $objFolder = new \Contao\Folder('files/con4gis_import_data');
                //if (!$objFolder->isUnprotected()) { //Rework >= Contao 4.7
                $objFolder->unprotect();
                try {
                    Dbafs::addResource('files/con4gis_import_data');
                } catch (\Exception $e) {
                    C4gLogModel::addLogEntry('core', 'Error synchronize new import file folder: '.$e);
                }
                //}
                $objFolder = new \Contao\Folder('files' . $importData['images']['path']);
                $objFolder->unprotect();
                $objFolder->synchronize();

            }
            $file = file_get_contents($cache . '/data/' . str_replace('.c4g', '.json', $importData['general']['filename']));
            $sqlStatements = $this->getSqlFromJson($file, $importData['import']['uuid'], $con4gisImportId);

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

            $objFolder = new \Contao\Folder('files/con4gis_import_data/io-data/');
            $objFolder->purge();
            $objFolder->delete();
//            $this->recursiveRemoveDirectory("./../var/cache/prod/con4gis/io-data/".str_replace(".c4g", "", $importData['general']['filename']));
//            unlink("./../var/cache/prod/con4gis/io-data/".$filename);
        }
        //Generate Symlinks and sync filesystem
        $this->import('Contao\Automator', 'Automator');
        $this->Automator->generateSymlinks();
//
//        Dbafs::syncFiles();

        $this->importRunning(false, $con4gisImportId);

        PageRedirect::redirect("/contao?do=c4g_io_data");

    }

    /**
     * updateBaseData
     */
    public function updateBaseData($importId = false)
    {
        if ($this->importRunning()) {
            \Contao\Message::addError($GLOBALS['TL_LANG']['tl_c4g_import_data']['importRunning']);
            PageRedirect::redirect("/contao?do=c4g_io_data");
            return false;
        }

        if ($importId) {
            $gutesImportData = $this->Database->prepare('SELECT importVersion, availableVersion FROM tl_c4g_import_data WHERE id=?')->execute($importId)->fetchAssoc();
            if ($gutesImportData['importVersion'] >= $gutesImportData['availableVersion']) {
                if ($gutesImportData['availableVersion'] == $gutesImportData['importVersion']) {
                    C4gLogModel::addLogEntry('core', 'Imported version is the same as the available version. Import will not be updated.');
                } elseif ($gutesImportData['importVersion'] == "" || $gutesImportData['importVersion'] == "0" || $gutesImportData['importVersion'] == 0) {
                    C4gLogModel::addLogEntry('core', 'New import is currently creating at gutes.io. Import will not be updated.');
                } else {
                    C4gLogModel::addLogEntry('core', 'Imported version is equal or higher than available version. Import will not be updated.');
                }
                return false;
            } else {
                $cronImport = true;
            }
        } else {
            $cronImport = false;
            $data = $_REQUEST;
            $importId = $data['id'];
        }

        $gutesImportData = $this->Database->prepare('SELECT * FROM tl_c4g_import_data WHERE id=? AND type=?')->execute($importId, 'gutesio')->fetchAssoc();
        if ($gutesImportData && $cronImport) {

//            $this->deleteBaseData($importId);
            $this->importBaseData($importId);
        } else {
            // Check current action
//            $this->deleteBaseData();
            $this->importBaseData();
        }

        PageRedirect::redirect("/contao?do=c4g_io_data");

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
        $con4gisDeleteTables = $localData->importTables;
        if ($con4gisDeleteTables == null OR $con4gisDeleteTables == "") {
            $con4gisDeleteTables = array("tl_c4g_");
        } else {
            $con4gisDeleteTables = explode(",", $con4gisDeleteTables);
            $con4gisDeleteTables = str_replace(" ", "", $con4gisDeleteTables);
        }

        if ($con4gisReleaseUuid != 0 && $con4gisReleaseUuid != '' && $con4gisReleaseUuid >= 6) {
            //Release import data
            $tables = $this->Database->listTables();

            foreach ($tables as $table) {
                foreach ($con4gisDeleteTables as $con4gisDeleteTable) {
                    if (strpos($table, $con4gisDeleteTable) !== false) {
                        if ($this->Database->fieldExists('importId', $table)) {
                            $this->Database->prepare("UPDATE $table SET importId=? WHERE importId=?")->execute('0', $con4gisReleaseUuid);
                        }
                    }
                }
            }

            $this->Database->prepare('UPDATE tl_c4g_import_data SET importVersion=? WHERE id=?')->execute('', $con4gisDeleteId);
            $this->Database->prepare('UPDATE tl_c4g_import_data SET importUuid=? WHERE id=?')->execute('0', $con4gisDeleteId);
            $this->Database->prepare('UPDATE tl_c4g_import_data SET importfilePath=? WHERE id=?')->execute('', $con4gisDeleteId);

            $this->loadBaseData(false);
        } else {
            C4gLogModel::addLogEntry('core', 'Error releasing unavailable import: wrong id set!');
            \Contao\Message::addError($GLOBALS['TL_LANG']['tl_c4g_import_data']['releasingError']);
        }

        PageRedirect::redirect("/contao?do=c4g_io_data");
    }

    /**
     * deleteBaseData
     */
    public function deleteBaseData($importId = false, $download = false)
    {
        if (!$download) {
            if ($this->importRunning()) {
                C4gLogModel::addLogEntry('core', 'Import already running. Try again later ');
                \Contao\Message::addError($GLOBALS['TL_LANG']['tl_c4g_import_data']['importRunning']);
                PageRedirect::redirect("/contao?do=c4g_io_data");
                return false;
            }
        }

        if ($importId) {
            $con4gisDeleteId = $importId;
            $gutesImportData = $this->Database->prepare('SELECT type FROM tl_c4g_import_data WHERE id=?')->execute($con4gisDeleteId)->fetchAssoc();
            if ($gutesImportData['type'] != 'gutesio' or $gutesImportData == null) {
                C4gLogModel::addLogEntry('core', 'Only gutesio imports are available or automatic updates.');
                return false;
            }
        } else {
            $data = $_REQUEST;
            $con4gisDeleteId = $data['id'];
        }

        if (!$download) {
            $this->importRunning(true, $con4gisDeleteId);
        }

        $localData = $this->Database->prepare('SELECT * FROM tl_c4g_import_data WHERE id=?')->execute($con4gisDeleteId);
        $con4gisDeleteUuid = $localData->importUuid;
        $con4gisDeleteBundles = $localData->bundles;
        $con4gisDeletePath = $localData->importFilePath;
        $con4gisDeleteDirectory = './../files' . $con4gisDeletePath . '/';
        $con4gisDeleteUuidLength = strlen($con4gisDeleteUuid);
        $con4gisDeleteTables = $localData->importTables;
        if ($con4gisDeleteTables == null OR $con4gisDeleteTables == "") {
            $con4gisDeleteTables = array("tl_c4g_");
        } else {
            $con4gisDeleteTables = explode(",", $con4gisDeleteTables);
            $con4gisDeleteTables = str_replace(" ", "", $con4gisDeleteTables);
        }

        if ($con4gisDeleteUuid != 0 && $con4gisDeleteUuid != '' && $con4gisDeleteUuidLength >= 6) {

            if ($importId) {
                $con4gisImportFolderScan = array_diff(scandir('./../files/con4gis_import_data'), ['.', '..']);
                if (strlen($con4gisDeleteUuid) > 5) {
                    $con4gisDeleteDatasetId = substr($con4gisDeleteUuid, 0, -5);
                } else {
                    $con4gisDeleteDatasetId = $con4gisDeleteUuid;
                }
                $importFolderCount = 0;
                foreach ($con4gisImportFolderScan as $con4gisImportFolder) {
                    if (substr($con4gisImportFolder, 0, -5) == $con4gisDeleteDatasetId) {
                        if (is_dir('files/con4gis_import_data/'.$con4gisImportFolder)) {
                            $importFolderCount = $importFolderCount + 1;
                        }
                    }
                }
                if ($importFolderCount > 1) {
                    C4gLogModel::addLogEntry('core', 'Older import folder in file system. Reimport everything manually.');
                    $this->importRunning(false, $con4gisDeleteId);
                    \Contao\Message::addError($GLOBALS['TL_LANG']['tl_c4g_import_data']['olderImport']);
                    PageRedirect::redirect("/contao?do=c4g_io_data");
                    return false;
                }
            }

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
                        if (strlen($con4gisDeleteUuid) > 5) {
                            $con4gisDeleteDatasetId = substr($con4gisDeleteUuid, 0, -5);
                        } else {
                            $con4gisDeleteDatasetId = $con4gisDeleteUuid;
                        }
                        foreach ($con4gisImportFolderScan as $con4gisImportFolder) {
                            if (substr($con4gisImportFolder, 0, -5) == $con4gisDeleteDatasetId) {
                                if (is_dir('files/con4gis_import_data/'.$con4gisImportFolder)) {
                                    $objFolder = new \Contao\Folder('files/con4gis_import_data/'.$con4gisImportFolder);
                                    $objFolder->unprotect();
                                    if (!$objFolder->isEmpty()) {
                                        $objFolder->purge();
                                    }
                                    $objFolder->delete();
                                }
                            }
                        }
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
//                        //Sync filesystem
//                        Dbafs::syncFiles();
                    } else {
                        $this->importRunning(false, $con4gisDeleteId);
                        C4gLogModel::addLogEntry('core', 'Could not delete import directory: Wrong path!');
                    }
                }
            }

            $deletedOldImports = $this->deleteOlderImports($con4gisDeleteUuid, $con4gisDeleteTables);
            if (!$deletedOldImports) {
                $this->importRunning(false, $con4gisDeleteId);
                \Contao\Message::addError($GLOBALS['TL_LANG']['tl_c4g_import_data']['errorDeleteImports']);
                PageRedirect::redirect("/contao?do=c4g_io_data");
                return false;
            }

            $this->Database->prepare('UPDATE tl_c4g_import_data SET importVersion=? WHERE id=?')->execute('', $con4gisDeleteId);
            $this->Database->prepare('UPDATE tl_c4g_import_data SET importUuid=? WHERE id=?')->execute('0', $con4gisDeleteId);
            $this->Database->prepare('UPDATE tl_c4g_import_data SET importfilePath=? WHERE id=?')->execute('', $con4gisDeleteId);

            $this->loadBaseData(false);
        } else {
            C4gLogModel::addLogEntry('core', 'Error deleting unavailable import: wrong id set!');
            PageRedirect::redirect("/contao?do=c4g_io_data");
            return false;
        }

        if (!$download) {
            $this->importRunning(false, $con4gisDeleteId);
        }

        PageRedirect::redirect("/contao?do=c4g_io_data");
        return true;
    }

    public function download($remoteFile, $localFile)
    {
        $fp = fopen($localFile, 'w');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $remoteFile);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.2.12) Gecko/20101026 Firefox/3.6.12');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        $con = curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        $contents = "";
        $contentsJson = "";
        try {
            $zip = zip_open($localFile);
            if ($zip) {
                while ($zip_entry = zip_read($zip)) {
                    if (zip_entry_name($zip_entry) == 'io-data.yml') {
                        if (zip_entry_open($zip, $zip_entry)) {
                            // Read open directory entry
                            $contents = zip_entry_read($zip_entry);
                            zip_entry_close($zip_entry);

//                            break;
                        }
                    }
                    if (strpos(zip_entry_name($zip_entry), '.json') !== false) {
                        if (zip_entry_open($zip, $zip_entry)) {
                            // Read open directory entry
                            $contentsJson = zip_entry_read($zip_entry);
                            zip_entry_close($zip_entry);

//                                    break;
                        }
                    }
                }
                zip_close($zip);
            }
            if ($contents == "" || $contentsJson == "") {
                C4gLogModel::addLogEntry("core", "Downloaded import data file (".$localFile.") not complete.");
                return false;
            } else {
                return true;
            }
        } catch (\Exception $e) {
            C4gLogModel::addLogEntry("core", "Error reading downloaded file: ".$e);
            return false;
        }
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
            $response = $REQUEST->response;
            if ($response) {
                if (substr($response, 0, 2) == "[{" && substr($response, -2, 2) == "}]") {
                    $responses = \GuzzleHttp\json_decode($response);
                    return $responses;
                }  else {
                    return false;
                }
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

        foreach ($arrBasedataFolders as $arrBasedataFolder => $value) {
            if (file_exists($value) && is_dir($value)) {
                $basedataFiles[$arrBasedataFolder] = array_slice(scandir($value), 2);
                foreach ($basedataFiles[$arrBasedataFolder] as $basedataFile => $file) {
                    $basedataFiles[$arrBasedataFolder][$basedataFile] = $value . '/' . $file;
                }
            }
        }

        $newYamlConfigArray = [];
        $count = 0;
        foreach ($basedataFiles as $basedataFile) {
            foreach ($basedataFile as $importFile) {
                $contents = "";
                $contentsJson = "";
                try {
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

//                                    break;
                                }
                            }
                            if (strpos(zip_entry_name($zip_entry), '.json') !== false) {
                                if (zip_entry_open($zip, $zip_entry)) {
                                    // Read open directory entry
                                    $contentsJson = zip_entry_read($zip_entry);
                                    zip_entry_close($zip_entry);

//                                    break;
                                }
                            }
                        }
                        zip_close($zip);
                    }
                    if ($contents == "" || $contentsJson == "") {
                        C4gLogModel::addLogEntry("core", "Import data file (".$importFile.") not complete.");
                        $newYamlConfigArray[$count] = false;
                    }
                } catch (\Throwable $e) {
                    C4gLogModel::addLogEntry("core", "Import data file not complete: ".$e);
                    return false;
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

    public function getSqlFromJson($file, $uuid, $con4gisImportId)
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
        $hexValueFile = array_slice($jsonFile, -2, 1);
        $hexValueRelation = [];

        if (array_key_exists("hexValues", $hexValueFile)) {
            foreach ($hexValueFile['hexValues'] as $hexField => $hexValues) {
                $hexValueRelation[$hexField] = explode(",", $hexValues);
            }
        }

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

        try {
            $firstTableQuery = $this->Database->prepare("SELECT id FROM $firstImportTable WHERE id LIKE ?")->execute($importUuidCheck)->fetchAllAssoc();
        } catch (Exception $e) {
            $this->importRunning(false, $con4gisImportId);
            C4gLogModel::addLogEntry('core', 'Error while executing SQL-Import: ' . $e->getMessage());
        }

        $tlFilesTableQuery = $this->Database->prepare('SELECT uuid FROM tl_files WHERE HEX(uuid) LIKE ?')->execute('%' . $firstTlFilesUuid . '%')->fetchAllAssoc();

        while ($firstTableQuery) {
            $newId = rand(1001, 9999);
            try {
                $firstTableQuery = $this->Database->prepare("SELECT id FROM $firstImportTable WHERE id LIKE ?")->execute($newId)->fetchAllAssoc();
            } catch (Exception $e) {
                $this->importRunning(false, $con4gisImportId);
                C4gLogModel::addLogEntry('core', 'Error while executing SQL-Import: ' . $e->getMessage());
            }
        }
        foreach ($jsonFile as $importDB => $importDatasets) {
            if ($importDB == 'relations' OR $importDB == 'hexValues') {
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

//                    if ($this->isUuid($importDbValue)) {
//                        $importDbValue = "UNHEX('".$importDbValue."')";
//                    }
                    $isHexValue = false;
                    if (array_key_exists($importDB, $hexValueRelation)) {
                        if (in_array($importDbField, $hexValueRelation[$importDB])) {
                            $isHexValue = true;
                        }
                    }

                    if (in_array($importDbField, $dbFields)) {
                        if ($importDB == 'tl_files' && $importDbField == 'id') {
                            $sqlStatement = $sqlStatement . '';
                        } else {
                            if ($sqlStatement == '' && substr($importDbValue, 0, 2) == '0x') {
                                $sqlStatement = 'INSERT INTO `' . $importDB . '` (`' . $importDbField . '`) VALUES (' . $importDbValue . ');;';
                            } elseif ($sqlStatement == '' && $isHexValue && $importDbField != "hash") {
                                $sqlStatement = 'INSERT INTO `' . $importDB . '` (`' . $importDbField . "`) VALUES (UNHEX('" . $importDbValue . "'));;";
                            } elseif ($sqlStatement == '' && $this->isUuid($importDbValue) && $importDbField != "hash") {
                                $sqlStatement = 'INSERT INTO `' . $importDB . '` (`' . $importDbField . "`) VALUES (UNHEX('" . $importDbValue . "'));;";
                            } elseif ($sqlStatement == '' && substr($importDbValue, 0, 2) != '0x') {
                                $sqlStatement = 'INSERT INTO `' . $importDB . '` (`' . $importDbField . "`) VALUES ('" . $importDbValue . "');;";
                            } elseif ($sqlStatement == '' && $importDbValue === null) {
                                $sqlStatement = 'INSERT INTO `' . $importDB . '` (`' . $importDbField . '`) VALUES (NULL);;';
                            } elseif (substr($importDbValue, 0, 2) == '0x') {
                                $sqlStatement = str_replace(') VALUES', ", `$importDbField`) VALUES", $sqlStatement);
                                $sqlStatement = str_replace(');;', ", $importDbValue);;", $sqlStatement);
                            } elseif ($isHexValue && $importDbField != "hash") {
                                $sqlStatement = str_replace(') VALUES', ", `$importDbField`) VALUES", $sqlStatement);
                                $sqlStatement = str_replace(');;', ", UNHEX('$importDbValue'));;", $sqlStatement);
                            } elseif ($this->isUuid($importDbValue) && $importDbField != "hash") {
                                $sqlStatement = str_replace(') VALUES', ", `$importDbField`) VALUES", $sqlStatement);
                                $sqlStatement = str_replace(');;', ", UNHEX('$importDbValue'));;", $sqlStatement);
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

    public function isUuid($uuid) {
        if (ctype_xdigit($uuid) && strlen($uuid) == 32) {
            return true;
        } else {
            return false;
        }
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

    public function importRunning($running = false, $id = 0)
    {
        if ($id == 0) {
            $importRunning = $this->Database->prepare("SELECT id FROM tl_c4g_import_data WHERE importRunning = '1'")->execute()->fetchAllAssoc();
            if ($importRunning) {
                return true;
            }

            return false;
        } elseif ($id != 0) {
            if ($running) {
                $this->Database->prepare("UPDATE tl_c4g_import_data SET importRunning = '1' WHERE id=?")->execute($id);
            } else {
                $this->Database->prepare("UPDATE tl_c4g_import_data SET importRunning = '' WHERE id=?")->execute($id);
            }
        }
    }

    public function cpy($source, $dest)
    {
        if(is_dir($source)) {
            $dir_handle=opendir($source);
            while($file=readdir($dir_handle)){
                if($file!="." && $file!=".."){
                    if(is_dir($source."/".$file)){
                        if(!is_dir($dest."/".$file)){
                            mkdir($dest."/".$file);
                        }
                        $this->cpy($source."/".$file, $dest."/".$file);
                    } else {
                        copy($source."/".$file, $dest."/".$file);
                    }
                }
            }
            closedir($dir_handle);
        } else {
            copy($source, $dest);
        }
    }

    public function deleteOlderImports($uuid, $con4gisDeleteTables) {
        if (strlen($uuid) > 5) {
            $importDatasetId = substr($uuid, 0, -5);
            $likeOperator = $importDatasetId."_____";
        } else {
            $importDatasetId = $uuid;
            $likeOperator = $uuid;
        }

        if ($likeOperator == 0 OR $likeOperator == "0_____" OR $likeOperator == "_____") {
            return false;
        }

        //Delete import data
        $tables = $this->Database->listTables();

        foreach ($tables as $table) {
            foreach ($con4gisDeleteTables as $con4gisDeleteTable) {
                if (strpos($table, $con4gisDeleteTable) !== false) {
                    if ($this->Database->fieldExists('importId', $table)) {
                        try {
                            $this->Database->prepare("DELETE FROM $table WHERE importId LIKE ?")->execute($likeOperator);
                        } catch (\Exception $e) {
                            C4gLogModel::addLogEntry("core", "Error deleting data from database. Abort import. ".$e);
                            return false;
                        }
                    }
                }
            }
        }
        return true;
    }

}
