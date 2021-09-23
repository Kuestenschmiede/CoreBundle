<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2021, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */

namespace con4gis\CoreBundle\Classes\Callback;

use con4gis\CoreBundle\Resources\contao\models\C4gLogModel;
use Contao\Backend;
use Contao\Folder;
use Contao\Message;
use Contao\PageRedirect;
use Contao\StringUtil;
use Contao\DataContainer;
use Contao\System;
use Dbafs;
use Exception;
use Symfony\Component\Yaml\Parser;
use ZipArchive;
use con4gis\CoreBundle\Classes\Events\AfterImportEvent;
use con4gis\CoreBundle\Classes\Events\AdditionalImportProxyDataEvent;
use DirectoryIterator;

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
        if ($localIoData) {
            foreach ($localIoData as $yamlConfig => $value) {
                $localResponses[$yamlConfig] = (object) $value['import'];
            }
        }

        foreach ($localResponses as $localResponse) {
            $responses[$responsesLength] = $localResponse;
            $responsesLength++;
        }
        $localData = $this->Database->prepare('SELECT * FROM tl_c4g_import_data')->execute();
        $localData = $localData->fetchAllAssoc();

        if (empty($localData)) {
            foreach ($responses as $response) {
                if (isset($response->datatype) && $response->datatype == 'diff') {
                    continue;
                }
                if (!$response->tables) {
                    $response->tables = '';
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
                if (isset($response->datatype) && $response->datatype == 'diff') {
                    continue;
                }
                if (!$response->tables) {
                    $response->tables = '';
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
            if (isset($response->datatype) && $response->datatype == 'diff') {
                continue;
            }
            if (!$response->tables) {
                $response->tables = '';
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
            PageRedirect::redirect('/contao?do=c4g_io_data');

            return false;
        }
        if ($importId) {
            $con4gisImportId = $importId;
            $cronImportData = $this->Database->prepare('SELECT importVersion, availableVersion FROM tl_c4g_import_data WHERE id=?')->execute($con4gisImportId)->fetchAssoc();
            if ($cronImportData['importVersion'] >= $cronImportData['availableVersion']) {
                return false;
            }
        } else {
            $data = $_REQUEST;
            $con4gisImportId = $data['id'];
        }

        $this->importRunning(true, $con4gisImportId);

//      lokaler Import
        if ($importId) {
            $localImportDatas = $this->getLocalIoData(true);
        } else {
            $localImportDatas = $this->getLocalIoData();
        }

        $availableLocal = false;
        foreach ($localImportDatas as $localImportData) {
            if ($localImportData['import']['id'] == $con4gisImportId) {
                $availableLocal = true;
                $importData = $localImportData;

                break;
            }
        }

        if ($availableLocal) {
            $imagePath = './../files' . $importData['images']['path'];
            $c4gPath = './../vendor/con4gis/' . $importData['general']['bundle'] . '/Resources/con4gis/' . $importData['general']['filename'];
            $cache = './../files/con4gis_import_data/io-data/' . str_replace('.c4g', '', $importData['general']['filename']);
            $importType = $importData['import']['type'];
            if (isset($importData['import']['datatype'])) {
                $importDataType = $importData['import']['datatype'];
            } else {
                $importDataType = 'full';
            }

            $alreadyImported = $this->Database->prepare('SELECT importVersion FROM tl_c4g_import_data WHERE id=?')->execute($con4gisImportId)->fetchAssoc();
            if ($alreadyImported['importVersion'] != '') {
                if ($importId && $importDataType == 'full') {
                    $deleted = $this->deleteBaseData($importId, true, true);
                    if (!$deleted) {
                        $this->importRunning(false, $con4gisImportId);
                        \Contao\Message::addError($GLOBALS['TL_LANG']['tl_c4g_import_data']['errorDeleteImports']);
                        C4gLogModel::addLogEntry('core', 'Error deleting old import data for automatic import. Stopped Import');
                        PageRedirect::redirect('/contao?do=c4g_io_data');

                        return false;
                    }
                } elseif ($importDataType == 'full') {
                    $deleted = $this->deleteBaseData(false, true, true);
                    if (!$deleted) {
                        $this->importRunning(false, $con4gisImportId);
                        \Contao\Message::addError($GLOBALS['TL_LANG']['tl_c4g_import_data']['errorDeleteImports']);
                        PageRedirect::redirect('/contao?do=c4g_io_data');

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
                $this->cpy($cache . '/images', $imagePath);
                $objFolder = new \Contao\Folder('files/con4gis_import_data');
                //if (!$objFolder->isUnprotected()) { //Rework >= Contao 4.7
                $objFolder->unprotect();
                //}
                $objFolder = new \Contao\Folder('files' . $importData['images']['path']);
                $objFolder->unprotect();
            }
            $this->chmod_r($imagePath, 0775, 0664);
            $file = file_get_contents($cache . '/data/' . str_replace('.c4g', '.json', $importData['general']['filename']));

            $sqlStatements = $this->getSqlFromJson($file, $importData['import']['uuid'], $importDataType, $importData['images']['path']);
            if ($importDataType == 'diff') {
                $this->deleteOldDiffImages($file);
            }

            if (!$sqlStatements) {
                C4gLogModel::addLogEntry('core', 'Error inserting/updating in database');
                \Contao\Message::addError($GLOBALS['TL_LANG']['tl_c4g_import_data']['importError']);
                $this->importRunning(false, $con4gisImportId);
                if (!$importId) {
                    $objFolder = new \Contao\Folder('files/con4gis_import_data/io-data/');
                    $objFolder->purge();
                    $objFolder->delete();
                    $objFolder = new \Contao\Folder('files' . $importData['images']['path']);
                    $objFolder->purge();
                    $objFolder->delete();
                }

                return false;
            }

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
            $this->Database->prepare('UPDATE tl_c4g_import_data SET importVersion=? WHERE id=?')->execute($importData['import']['version'], $con4gisImportId);
            $this->Database->prepare('UPDATE tl_c4g_import_data SET importUuid=? WHERE id=?')->execute($localImportData['import']['uuid'], $con4gisImportId);
            $this->Database->prepare('UPDATE tl_c4g_import_data SET importFilePath=? WHERE id=?')->execute($localImportData['images']['path'], $con4gisImportId);

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
            if ($importId) {
                $currentImport = $this->Database->prepare('SELECT importVersion, availableVersion FROM tl_c4g_import_data WHERE id=?')
                    ->execute($con4gisImportId)->fetchAssoc();
                if ($currentImport) {
                    $importVersion = $currentImport['importVersion'];
                    $availableVersion = $currentImport['availableVersion'];
                    if (($importVersion + 1) != $availableVersion || $importVersion == '') {
                        $basedataUrl .= '&datatype=full';
                    } else {
                        $basedataUrl .= '&datatype=diff';
                    }
                } else {
                    $basedataUrl .= '&datatype=diff';
                }
            } else {
                $basedataUrl .= '&datatype=full';
            }

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
                PageRedirect::redirect('/contao?do=c4g_io_data');

                return false;
            }

            // Getting data from downloades json config file
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

            if (isset($importData['import']['datatype'])) {
                $importDataType = $importData['import']['datatype'];
            } else {
                $importDataType = 'full';
            }

            $alreadyImported = $this->Database->prepare('SELECT importVersion FROM tl_c4g_import_data WHERE id=?')->execute($con4gisImportId)->fetchAssoc();
            if ($alreadyImported['importVersion'] != '') {
                if ($importId && $importDataType == 'full') {
                    $deleted = $this->deleteBaseData($importId, true, true);
                    if (!$deleted) {
                        $this->importRunning(false, $con4gisImportId);
                        \Contao\Message::addError($GLOBALS['TL_LANG']['tl_c4g_import_data']['errorDeleteImports']);

                        return false;
                    }
                } elseif ($importDataType == 'full') {
                    $deleted = $this->deleteBaseData(false, true, true);
                    if (!$deleted) {
                        $this->importRunning(false, $con4gisImportId);
                        \Contao\Message::addError($GLOBALS['TL_LANG']['tl_c4g_import_data']['errorDeleteImports']);
                        PageRedirect::redirect('/contao?do=c4g_io_data');

                        return false;
                    }
                }
            } elseif (($alreadyImported['importVersion'] == '' || !$alreadyImported) && $importId) {
                C4gLogModel::addLogEntry('core', 'Cant update automaticly. Import information not found in database. Abort import.');
                $this->importRunning(false, $con4gisImportId);

                return false;
            }

            $importType = $importData['import']['type'];

            $imagePath = './../files' . $importData['images']['path'];
            $cache = './../files/con4gis_import_data/io-data/' . str_replace('.c4g', '', $importData['general']['filename']);

            $zip = new ZipArchive;
            if ($zip->open($downloadPath . $filename) === true) {
                $zip->extractTo($cache);
                $zip->close();

                mkdir($imagePath, 0770, true);
                $this->cpy($cache . '/images', $imagePath);
                $objFolder = new \Contao\Folder('files/con4gis_import_data');
                //if (!$objFolder->isUnprotected()) { //Rework >= Contao 4.7
                $objFolder->unprotect();
                //}
                $objFolder = new \Contao\Folder('files' . $importData['images']['path']);
                $objFolder->unprotect();
            }
            $this->chmod_r($imagePath, 0775);
            $file = file_get_contents($cache . '/data/' . str_replace('.c4g', '.json', $importData['general']['filename']));
            $sqlStatements = $this->getSqlFromJson($file, $importData['import']['uuid'], $importDataType, $importData['images']['path']);
            if ($importDataType == 'diff') {
                $this->deleteOldDiffImages($file);
            }

            if (!$sqlStatements) {
                C4gLogModel::addLogEntry('core', 'Error inserting/updating in database');
                \Contao\Message::addError($GLOBALS['TL_LANG']['tl_c4g_import_data']['importError']);
                $this->importRunning(false, $con4gisImportId);
                if (!$importId) {
                    $objFolder = new \Contao\Folder('files/con4gis_import_data/io-data/');
                    $objFolder->purge();
                    $objFolder->delete();
                    $objFolder = new \Contao\Folder('files' . $importData['images']['path']);
                    $objFolder->purge();
                    $objFolder->delete();
                }

                return false;
            }

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

            $objFolder = new \Contao\Folder('files/con4gis_import_data/io-data/');
            $objFolder->purge();
            $objFolder->delete();
        }

        //Generate Symlinks and sync filesystem
        $this->import('Contao\Automator', 'Automator');
        $this->Automator->generateSymlinks();

        if (!isset($importType)) {
            $importType = 'notype';
        }
        $event = new AfterImportEvent();
        $event->setImportType($importType);
        $dispatcher = System::getContainer()->get('event_dispatcher');
        $dispatcher->dispatch($event::NAME, $event);
        $error = $event->getError();

        if ($error) {
            Message::addError($error);
        }

        $this->importRunning(false, $con4gisImportId);

        C4gLogModel::addLogEntry('core', 'The import data was successfully imported.');
        Message::addConfirmation($GLOBALS['TL_LANG']['tl_c4g_import_data']['importSuccessfull']);
        PageRedirect::redirect('/contao?do=c4g_io_data');
    }

    /**
     * updateBaseData
     */
    public function updateBaseData($importId = false)
    {
        if ($this->importRunning()) {
            \Contao\Message::addError($GLOBALS['TL_LANG']['tl_c4g_import_data']['importRunning']);
            PageRedirect::redirect('/contao?do=c4g_io_data');

            return false;
        }

        if ($importId) {
            $cronImportData = $this->Database->prepare('SELECT importVersion, availableVersion FROM tl_c4g_import_data WHERE id=?')->execute($importId)->fetchAssoc();
            if ($cronImportData['importVersion'] >= $cronImportData['availableVersion']) {
                if ($cronImportData['importVersion'] == '' || $cronImportData['importVersion'] == '0' || $cronImportData['importVersion'] == 0) {
                    C4gLogModel::addLogEntry('core', 'New import is currently unavailable. Import will not be updated.');
                } elseif ($cronImportData['availableVersion'] != $cronImportData['importVersion']) {
                    C4gLogModel::addLogEntry('core', 'Imported version is equal or higher than available version. Import will not be updated.');
                }

                return false;
            }
            $cronImport = true;
        } else {
            $cronImport = false;
            $data = $_REQUEST;
            $importId = $data['id'];
        }

        $cronImportData = $this->Database->prepare('SELECT * FROM tl_c4g_import_data WHERE id=?')->execute($importId)->fetchAssoc();
        if ($cronImportData && $cronImport) {
            $this->importBaseData($importId);
        } else {
            //ToDo: with importId only for manual diff import testing
            $this->importBaseData($importId);
        }

        PageRedirect::redirect('/contao?do=c4g_io_data');
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
        if ($con4gisDeleteTables == null or $con4gisDeleteTables == '') {
            $con4gisDeleteTables = ['tl_c4g_'];
        } else {
            $con4gisDeleteTables = explode(',', $con4gisDeleteTables);
            $con4gisDeleteTables = str_replace(' ', '', $con4gisDeleteTables);
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

        C4gLogModel::addLogEntry('core', 'The import data was successfully released.');
        Message::addConfirmation($GLOBALS['TL_LANG']['tl_c4g_import_data']['releasedSuccessfull']);
        PageRedirect::redirect('/contao?do=c4g_io_data');
    }

    /**
     * deleteBaseData
     */
    public function deleteBaseData($importId = false, $download = false, $update = false)
    {
        if (!$download) {
            if ($this->importRunning()) {
                C4gLogModel::addLogEntry('core', 'Import already running. Try again later ');
                \Contao\Message::addError($GLOBALS['TL_LANG']['tl_c4g_import_data']['importRunning']);
                PageRedirect::redirect('/contao?do=c4g_io_data');

                return false;
            }
        }

        if ($importId) {
            $con4gisDeleteId = $importId;
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
        if ($con4gisDeleteTables == null or $con4gisDeleteTables == '') {
            $con4gisDeleteTables = ['tl_c4g_'];
        } else {
            $con4gisDeleteTables = explode(',', $con4gisDeleteTables);
            $con4gisDeleteTables = str_replace(' ', '', $con4gisDeleteTables);
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
                        if (is_dir('files/con4gis_import_data/' . $con4gisImportFolder)) {
                            $importFolderCount = $importFolderCount + 1;
                        }
                    }
                }
                if ($importFolderCount > 1) {
                    C4gLogModel::addLogEntry('core', 'Older import folder in file system. Reimport everything manually.');
                    $this->importRunning(false, $con4gisDeleteId);
                    \Contao\Message::addError($GLOBALS['TL_LANG']['tl_c4g_import_data']['olderImport']);
                    PageRedirect::redirect('/contao?do=c4g_io_data');

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
                                if (is_dir('files/con4gis_import_data/' . $con4gisImportFolder)) {
                                    $objFolder = new \Contao\Folder('files/con4gis_import_data/' . $con4gisImportFolder);
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
                PageRedirect::redirect('/contao?do=c4g_io_data');

                return false;
            }

            $this->Database->prepare('UPDATE tl_c4g_import_data SET importVersion=? WHERE id=?')->execute('', $con4gisDeleteId);
            $this->Database->prepare('UPDATE tl_c4g_import_data SET importUuid=? WHERE id=?')->execute('0', $con4gisDeleteId);
            $this->Database->prepare('UPDATE tl_c4g_import_data SET importfilePath=? WHERE id=?')->execute('', $con4gisDeleteId);

            $this->loadBaseData(false);
        } else {
            C4gLogModel::addLogEntry('core', 'Error deleting unavailable import: wrong id set!');
            PageRedirect::redirect('/contao?do=c4g_io_data');

            return false;
        }

        if (!$download) {
            $this->importRunning(false, $con4gisDeleteId);
        }

        if (!$update) {
            C4gLogModel::addLogEntry('core', 'The import data was successfully deleted.');
            Message::addConfirmation($GLOBALS['TL_LANG']['tl_c4g_import_data']['deletedSuccessfull']);
            PageRedirect::redirect('/contao?do=c4g_io_data');
        } else {
            C4gLogModel::addLogEntry('core', 'The import data was successfully deleted.');

            return true;
        }
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

        $contents = '';
        $contentsJson = '';

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
            if ($contents == '' || $contentsJson == '') {
                try {
                    $errorContent = file_get_contents($localFile);
                    if ($errorContent == 'no_file') {
                        C4gLogModel::addLogEntry('core', "Didn't found file on Proxy-Server. Please try again later or contact support@con4gis.io");
                    } else {
                        C4gLogModel::addLogEntry('core', 'Downloaded import data file (' . $localFile . ') not complete.');
                    }
                } catch (\Throwable $e) {
                    C4gLogModel::addLogEntry('core', 'Error with downloaded import data file (' . $localFile . '). Error: ' . $e);

                    return false;
                }

                return false;
            }
            C4gLogModel::addLogEntry('core', 'The import data was successfully downloaded.');

            return true;
        } catch (\Exception $e) {
            C4gLogModel::addLogEntry('core', 'Error reading downloaded file: ' . $e);

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

            //Getting additional Data
            $event = new AdditionalImportProxyDataEvent();
            $dispatcher = System::getContainer()->get('event_dispatcher');
            $dispatcher->dispatch($event, $event::NAME);
            $additionalProxyData = $event->getProxyData();

            if ($additionalProxyData && is_array($additionalProxyData)) {
                foreach ($additionalProxyData as $proxyData) {
                    $basedataUrl .= '&' . $proxyData['proxyKey'] . '=' . str_replace(' ', '%20', $proxyData['proxyData']);
                }
            }

            $REQUEST = new \Contao\Request();
            if ($_SERVER['HTTP_REFERER']) {
                $REQUEST->setHeader('Referer', $_SERVER['HTTP_REFERER']);
            }
            if ($_SERVER['HTTP_USER_AGENT']) {
                $REQUEST->setHeader('User-Agent', $_SERVER['HTTP_USER_AGENT']);
            }
            $REQUEST->send($basedataUrl);
            $response = $REQUEST->response;
            if ($response) {
                if (substr($response, 0, 2) == '[{' && substr($response, -2, 2) == '}]') {
                    $responses = \GuzzleHttp\json_decode($response);

                    return $responses;
                }

                return false;
            }

            return $responses = [];
        }
    }

    public function getLocalIoData($importId = false)
    {
        $rootDir = System::getContainer()->getParameter('kernel.project_dir');
        $arrBasedataFolders = [
            'maps' => $rootDir . '/vendor/con4gis/maps/Resources/con4gis',
            'visualization' => $rootDir . '/vendor/con4gis/visualization/Resources/con4gis',
            'core' => $rootDir . '/vendor/con4gis/core/Resources/con4gis',
            'data' => $rootDir . '/vendor/con4gis/data/Resources/con4gis',
            'firefighter' => $rootDir . '/vendor/con4gis/firefighter/Resources/con4gis',
        ];

        $basedataFiles = [];

        foreach ($arrBasedataFolders as $arrBasedataFolder => $value) {
            if (file_exists($value) && is_dir($value)) {
                $basedataFiles[$arrBasedataFolder] = array_slice(scandir($value), 2);
                foreach ($basedataFiles[$arrBasedataFolder] as $basedataFile => $file) {
                    if (!$importId && str_ends_with($file, '-diff.c4g')) {
                        unset($basedataFiles[$arrBasedataFolder][$basedataFile]);

                        continue;
                    }
                    $basedataFiles[$arrBasedataFolder][$basedataFile] = $value . '/' . $file;
                }
            }
        }

        //Check if Diffs are available
        if ($importId) {
            foreach ($arrBasedataFolders as $arrBasedataFolder => $value) {
                if (file_exists($value) && is_dir($value)) {
                    foreach ($basedataFiles[$arrBasedataFolder] as $basedataFile => $file) {
                        if (str_ends_with($file, '-diff.c4g')) {
                            $importName = strstr($file, '-diff.c4g', true);
                            $fullimport = array_search($importName . '.c4g', $basedataFiles[$arrBasedataFolder]);
                            if ($fullimport) {
                                unset($basedataFiles[$arrBasedataFolder][$fullimport]);
                            }
                        }
                    }
                }
            }
        }

        $newYamlConfigArray = [];
        $count = 0;
        foreach ($basedataFiles as $basedataFile) {
            foreach ($basedataFile as $importFile) {
                $contents = '';
                $contentsJson = '';

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
                    if ($contents == '' || $contentsJson == '') {
                        C4gLogModel::addLogEntry('core', 'Import data file (' . $importFile . ') not complete.');
                        $newYamlConfigArray[$count] = false;
                    }
                } catch (\Throwable $e) {
                    C4gLogModel::addLogEntry('core', 'Import data file not complete: ' . $e);

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

    public function deleteOldDiffImages($file)
    {
        $rootDir = System::getContainer()->getParameter('kernel.project_dir');
        $jsonFile = (array) json_decode($file);
        if (isset($jsonFile['deleted']->tl_files)) {
            foreach ($jsonFile['deleted']->tl_files as $deleteTlFileDataset) {
                $deleteTlFileDataset = (array) $deleteTlFileDataset;
                $path = $deleteTlFileDataset['path'];
                $deleteFile = strrchr($path, '/');
                $deleteFolder = str_replace($deleteFile, '', $path);
                unlink($rootDir . '/' . $path);
                $scan = array_diff(scandir($rootDir . '/' . $deleteFolder), ['..', '.']);
                if (count($scan) == 1 && in_array('.public', $scan)) {
                    unlink($rootDir . '/' . $deleteFolder . '/.public');
                }
                $this->recursiveDeleteDiffFolder($deleteFolder);
            }
        }
    }

    public function recursiveDeleteDiffFolder($deleteFolder)
    {
        if (str_ends_with($deleteFolder, '/files')) {
            return false;
        }
        $rootDir = System::getContainer()->getParameter('kernel.project_dir');
        $folder = new Folder($deleteFolder);
        if ($folder->isEmpty()) {
            $this->recursiveRemoveDirectory($rootDir . '/' . $deleteFolder);
        } else {
            return false;
        }
        $deleteCurrentFolder = strrchr($deleteFolder, '/');
        $deletePreFolder = str_replace($deleteCurrentFolder, '', $deleteFolder);
        $this->recursiveDeleteDiffFolder($deletePreFolder);

        return true;
    }

    public function getSqlFromJson($file, $uuid, $importDataType, $imagePath)
    {
        $rootDir = System::getContainer()->getParameter('kernel.project_dir');
        if (!$file) {
            return false;
        }
        if ($importDataType == 'diff') {
            $idConfigFile = file_get_contents($rootDir . '/files' . $imagePath . '/id-config.json');
            if ($idConfigFile) {
                $allIdChangesJson = json_decode($idConfigFile);
                $allIdChangesJson = json_decode(json_encode($allIdChangesJson), true);
            } else {
                $allIdChangesJson = false;
            }
            $queryType = 'UPDATE';
        } else {
            $allIdChangesJson = false;
            $queryType = 'INSERT';
        }

        $filesMessageCount = 0;
        $importId = $uuid;
        $jsonFile = (array) json_decode($file);
        $sqlStatements = [];
        $relations = array_slice($jsonFile, -1, 1);
        $relationTables = [];
        $relationTablesPrimary = [];
        $dbRelation = [];
        $dbRelationPrimary = [];
        $hexValueFile = array_slice($jsonFile, -2, 1);
        $hexValueRelation = [];

        if (array_key_exists('hexValues', $hexValueFile)) {
            foreach ($hexValueFile['hexValues'] as $hexField => $hexValues) {
                $hexValueRelation[$hexField] = explode(',', $hexValues);
            }
        }

        foreach ($relations['relations'] as $key => $value) {
            //primary tables
            if ($key == 'NoRelations' && $value == 'ToDisplay') {
                break;
            }
            $firstTable = explode('.', $key);

            if (!in_array($firstTable[0], $relationTables)) {
                $relationTables[] = $firstTable[0];
            }

            $dbRelation[$firstTable[0]][] = $firstTable[1];

            //foreign tables
            $secondTable = explode('.', $value);

            if (!in_array($secondTable[0], $relationTablesPrimary)) {
                $relationTablesPrimary[] = $secondTable[0];
            }
            if (!in_array($secondTable[1], $dbRelationPrimary[$secondTable[0]])) {
                $dbRelationPrimary[$secondTable[0]][] = $secondTable[1];
            }
        }

        //Get all changed IDs
        $allChanges = $this->getIdChanges($jsonFile, $relationTablesPrimary, $dbRelationPrimary, $allIdChangesJson);
        $allIdChanges = $allChanges['allIdChanges'];
        $allIdChangesNonRelations = $allChanges['allIdChangesNonRelations'];

        $allIdChangesJson = json_encode($allIdChanges, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents($rootDir . '/files' . $imagePath . '/id-config.json', $allIdChangesJson);

        foreach ($jsonFile as $importDB => $importDatasets) {

            //sql statements for deleting removed data
            if ($importDB == 'deleted') {
                foreach ($importDatasets as $tableKey => $tableDataset) {
                    foreach ($tableDataset as $dataset) {
                        $dataset = (array) $dataset;
                        if (isset($dataset['uuid']) && !empty($dataset['uuid'])) {
                            if ($tableKey == 'tl_files') {
                                $path = stripslashes($dataset['path']);
                                $sqlStatements[] = 'DELETE FROM ' . $tableKey . " WHERE path='" . $path . "'";
                            } else {
                                $sqlStatements[] = 'DELETE FROM ' . $tableKey . " WHERE importId != '' && importId != 0 && uuid='" . $dataset['uuid'] . "'";
                            }
                        } elseif (isset($dataset['id']) && !empty($dataset['id'])) {
                            if ($tableKey == 'tl_files') {
                                $path = stripslashes($dataset['path']);
                                $sqlStatements[] = 'DELETE FROM ' . $tableKey . " WHERE path='" . $path . "'";
                            } else {
                                $sqlStatements[] = 'DELETE FROM ' . $tableKey . " WHERE importId != '' && importId != 0 && id=" . $allIdChanges[$tableKey]['id'][$dataset['id']];
                                unset($allIdChanges[$tableKey]['id'][$dataset['id']]);
                            }
                        }
                    }
                }
                $allIdChangesJson = json_encode($allIdChanges, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                file_put_contents($rootDir . '/files' . $imagePath . '/id-config.json', $allIdChangesJson);

                continue;
            }

            if ($importDB == 'relations' or $importDB == 'hexValues') {
                break;
            }

            if ($importDataType == 'diff') {
                $queryType = 'UPDATE';
            } else {
                $queryType = 'INSERT';
            }
            unset($updateWhereQuery, $updateWhereQueryValue);

            $dbFields = $this->Database->getFieldNames($importDB);
            if ($queryType == 'UPDATE' && in_array('uuid', $dbFields)) {
                if ($importDB == 'tl_files') {
                    $updateWhereQuery = ' WHERE path=';
                } else {
                    $updateWhereQuery = ' WHERE uuid=';
                }
            } elseif ($queryType == 'UPDATE' && !isset($allIdChanges[$importDB]['id'])) {
                C4gLogModel::addLogEntry('core', 'Skip update of table ' . $importDB . ' because of missing uuid.');

                continue;
            } elseif ($queryType == 'UPDATE' && isset($allIdChanges[$importDB]['id'])) {
                $updateWhereQuery = ' WHERE id=';
            }
            foreach ($importDatasets as $importDataset) {
                unset($updateWhereQueryValue);
                if ($importDataType == 'diff') {
                    $queryType = 'UPDATE';
                } else {
                    $queryType = 'INSERT';
                }

                $skipFilesEntry = false;
                $sqlStatement = '';
                $importDataset = (array) $importDataset;
                if ($queryType == 'UPDATE' && in_array('uuid', $dbFields) && $importDataset['uuid'] == '') {
                    C4gLogModel::addLogEntry('core', "Don't update dataset with id" . $importDataset['id'] . ' from table ' . $importDB . ' because of empty uuid.');

                    continue;
                } elseif ($queryType == 'UPDATE' && !array_key_exists($importDataset['id'], $allIdChanges[$importDB]['id']) && !in_array('uuid', $dbFields)) {
                    C4gLogModel::addLogEntry('core', "Don't update dataset with id" . $importDataset['id'] . ' from table ' . $importDB . ' because id not exists in id config.');

                    continue;
                }
                if ($queryType == 'UPDATE' && in_array('uuid', $dbFields) && $importDataset['uuid'] != '') {
                    //check if dataset can be updated or is a completely new one
                    if ($importDB == 'tl_files') {
                        $availableQuery = $this->Database->prepare('SELECT * FROM ' . $importDB . ' WHERE path=?')
                            ->execute($importDataset['path'])->fetchAssoc();
                    } else {
                        $availableQuery = $this->Database->prepare('SELECT * FROM ' . $importDB . " WHERE importId != '' && importId != 0 && uuid=?")
                            ->execute($importDataset['uuid'])->fetchAssoc();
                    }
                    if (!$availableQuery) {
                        $queryType = 'INSERT';
                    }
                } elseif ($queryType == 'UPDATE' && array_key_exists($importDataset['id'], $allIdChanges[$importDB]['id'])) {
                    $availableQuery = $this->Database->prepare('SELECT * FROM ' . $importDB . ' WHERE id=?')
                        ->execute($allIdChanges[$importDB]['id'][$importDataset['id']])->fetchAssoc();
                    if (!$availableQuery) {
                        $queryType = 'INSERT';
                    }
                }
                if (!array_key_exists('importId', $importDataset)) {
                    $importDataset['importId'] = $importId;
                }
                $primaryImportRelationTable = in_array($importDB, $relationTablesPrimary) ? $importDB : false;
                foreach ($importDataset as $importDbField => $importDbValue) {
                    if ($queryType == 'UPDATE' && in_array('uuid', $dbFields) && ($importDbField == 'id' || $importDbField == 'pid')) {
                        continue;
                    }
                    if ($queryType == 'UPDATE' && $importDbField == 'uuid' && $importDB != 'tl_files') {
                        $updateWhereQueryValue = $importDbValue;
                    } elseif ($queryType == 'UPDATE' && $importDbField == 'path' && $importDB == 'tl_files') {
                        $updateWhereQueryValue = $importDbValue;
                    } elseif ($updateWhereQuery == ' WHERE id=' && $importDbField == 'id') {
                        $updateWhereQueryValue = $allIdChanges[$importDB]['id'][$importDataset['id']];
                    }

                    if ($importDbField == 'id') {
                        if ($primaryImportRelationTable) {
                            $importDbValue = $allIdChanges[$importDB][$importDbField][$importDbValue];
                        } else {
                            $importDbValue = $allIdChangesNonRelations[$importDB][$importDbField][$importDbValue];
                        }
                    } elseif ($importDbField == 'importId') {
                        $importDbValue = $importId;
                    } elseif (in_array($importDB, $relationTables)) {
                        if (in_array($importDbField, $dbRelation[$importDB])) {
                            if ($importDbValue != '0') {
                                if (substr($importDbValue, 0, 2) == '0x' && $importDB != 'tl_files') {
                                    $unserial = hex2bin(substr($importDbValue, 2));

                                    if (strpos($unserial, '{')) {
                                        $unserial = unserialize($unserial);
                                        $unserial = $this->changeDbValue($importDB, $importDbField, $unserial, $allIdChanges, $relations);
                                        $newImportDbValue = serialize($unserial);
                                        $newImportDbValue = bin2hex($newImportDbValue);
                                        $newImportDbValue = $this->prepend('0x', $newImportDbValue);
                                        $importDbValue = $newImportDbValue;
                                    } else {
                                        $newImportDbValue = $this->changeDbValue($importDB, $importDbField, $unserial, $allIdChanges, $relations);
                                        $newImportDbValue = bin2hex($newImportDbValue);
                                        $newImportDbValue = $this->prepend('0x', $newImportDbValue);
                                        $importDbValue = $newImportDbValue;
                                    }
                                } elseif (substr($importDbValue, 0, 2) == 'a:') {
                                    $importDbValue = str_replace('\"', '"', $importDbValue);
                                    $unserial = StringUtil::deserialize($importDbValue);
                                    $unserial = $this->changeDbValue($importDB, $importDbField, $unserial, $allIdChanges, $relations);
                                    $newImportDbValue = serialize($unserial);
                                    $importDbValue = $newImportDbValue;
                                } elseif (strpos($importDbValue, '{')) {
                                    $unserial = hex2bin(substr($importDbValue, 2));
                                    $unserial = unserialize($unserial);
                                    $unserial = $this->changeDbValue($importDB, $importDbField, $unserial, $allIdChanges, $relations);
                                    $newImportDbValue = serialize($unserial);
                                    $newImportDbValue = bin2hex($newImportDbValue);
                                    $newImportDbValue = $this->prepend('0x', $newImportDbValue);
                                    $importDbValue = $newImportDbValue;
                                } elseif (is_numeric($importDbValue)) {
                                    $newImportDbValue = $this->changeDbValue($importDB, $importDbField, $importDbValue, $allIdChanges, $relations);
                                    $importDbValue = $newImportDbValue;
                                }
                            }
                        }
                    }

                    $isHexValue = false;
                    if (array_key_exists($importDB, $hexValueRelation)) {
                        if (in_array($importDbField, $hexValueRelation[$importDB])) {
                            $isHexValue = true;
                        }
                    }

                    if (in_array($importDbField, $dbFields)) {
                        if (($importDB == 'tl_files' && $importDbField == 'id') || (isset($updateWhereQueryValue) && $importDbField == 'uuid' && $importDB != 'tl_files')) {
                            $sqlStatement = $sqlStatement . '';
                        } else {
                            if ($queryType == 'INSERT') {
                                if ($sqlStatement == '' && substr($importDbValue, 0, 2) == '0x') {
                                    $sqlStatement = 'INSERT INTO `' . $importDB . '` (`' . $importDbField . '`) VALUES (' . $importDbValue . ');;';
                                } elseif ($sqlStatement == '' && $isHexValue && $importDbField != 'hash') {
                                    $sqlStatement = 'INSERT INTO `' . $importDB . '` (`' . $importDbField . "`) VALUES (UNHEX('" . $importDbValue . "'));;";
                                } elseif ($sqlStatement == '' && $this->isUuid($importDbValue) && $importDbField != 'hash') {
                                    $sqlStatement = 'INSERT INTO `' . $importDB . '` (`' . $importDbField . "`) VALUES (UNHEX('" . $importDbValue . "'));;";
                                } elseif ($sqlStatement == '' && substr($importDbValue, 0, 2) != '0x') {
                                    $sqlStatement = 'INSERT INTO `' . $importDB . '` (`' . $importDbField . "`) VALUES ('" . $importDbValue . "');;";
                                } elseif ($sqlStatement == '' && $importDbValue === null) {
                                    $sqlStatement = 'INSERT INTO `' . $importDB . '` (`' . $importDbField . '`) VALUES (NULL);;';
                                } elseif (substr($importDbValue, 0, 2) == '0x') {
                                    $sqlStatement = str_replace(') VALUES', ", `$importDbField`) VALUES", $sqlStatement);
                                    $sqlStatement = str_replace(');;', ", $importDbValue);;", $sqlStatement);
                                } elseif ($isHexValue && $importDbField != 'hash') {
                                    $sqlStatement = str_replace(') VALUES', ", `$importDbField`) VALUES", $sqlStatement);
                                    $sqlStatement = str_replace(');;', ", UNHEX('$importDbValue'));;", $sqlStatement);
                                } elseif ($this->isUuid($importDbValue) && $importDbField != 'hash') {
                                    $sqlStatement = str_replace(') VALUES', ", `$importDbField`) VALUES", $sqlStatement);
                                    $sqlStatement = str_replace(');;', ", UNHEX('$importDbValue'));;", $sqlStatement);
                                } elseif ($importDbValue === null) {
                                    $sqlStatement = str_replace(') VALUES', ", `$importDbField`) VALUES", $sqlStatement);
                                    $sqlStatement = str_replace(');;', ', NULL);;', $sqlStatement);
                                } else {
                                    $sqlStatement = str_replace(') VALUES', ", `$importDbField`) VALUES", $sqlStatement);
                                    $sqlStatement = str_replace(');;', ", '$importDbValue');;", $sqlStatement);
                                }
                            } elseif ($queryType == 'UPDATE') {
                                if ($sqlStatement == '' && substr($importDbValue, 0, 2) == '0x') {
                                    $sqlStatement = 'UPDATE `' . $importDB . '` SET ' . $importDbField . ' = ' . $importDbValue . ';;';
                                } elseif ($sqlStatement == '' && $isHexValue && $importDbField != 'hash') {
                                    $sqlStatement = 'UPDATE `' . $importDB . '` SET ' . $importDbField . " = UNHEX('" . $importDbValue . "');;";
                                } elseif ($sqlStatement == '' && $this->isUuid($importDbValue) && $importDbField != 'hash') {
                                    $sqlStatement = 'UPDATE `' . $importDB . '` SET ' . $importDbField . " = UNHEX('" . $importDbValue . "');;";
                                } elseif ($sqlStatement == '' && substr($importDbValue, 0, 2) != '0x') {
                                    $sqlStatement = 'UPDATE `' . $importDB . '` SET ' . $importDbField . " = '" . $importDbValue . "';;";
                                } elseif ($sqlStatement == '' && $importDbValue === null) {
                                    $sqlStatement = 'UPDATE `' . $importDB . '` SET ' . $importDbField . ' = NULL;;';
                                } elseif (substr($importDbValue, 0, 2) == '0x') {
                                    $sqlStatement = str_replace(';;', ", `$importDbField` = $importDbValue;;", $sqlStatement);
                                } elseif ($isHexValue && $importDbField != 'hash') {
                                    $sqlStatement = str_replace(';;', ", `$importDbField` = UNHEX('$importDbValue');;", $sqlStatement);
                                } elseif ($this->isUuid($importDbValue) && $importDbField != 'hash') {
                                    $sqlStatement = str_replace(';;', ", `$importDbField` = UNHEX('$importDbValue');;", $sqlStatement);
                                } elseif ($importDbValue === null) {
                                    $sqlStatement = str_replace(';;', ", `$importDbField` = NULL;;", $sqlStatement);
                                } else {
                                    $sqlStatement = str_replace(';;', ", `$importDbField` = '$importDbValue';;", $sqlStatement);
                                }
                            }
                        }
                    }
                }
                if ($importDB == 'tl_files' && $skipFilesEntry) {
                    if ($filesMessageCount == 0) {
                        C4gLogModel::addLogEntry('core', 'Files already imported. tl_files will not be imported');
                    }
                } else {
                    if ($queryType == 'UPDATE' && isset($updateWhereQuery) && isset($updateWhereQueryValue) && $updateWhereQuery != '' && $updateWhereQueryValue != '') {
                        $sqlStatement = str_replace(';;', $updateWhereQuery . "'" . $updateWhereQueryValue . "';", $sqlStatement);
                    } else {
                        $sqlStatement = str_replace(');;', ');', $sqlStatement);
                    }
                    $sqlStatements[] = $sqlStatement;
                }
            }
        }

        return $sqlStatements;
    }

    private function getIdChanges($jsonFile, $relationTablesPrimary, $dbRelationPrimary, $allIdChangesJson)
    {
        if ($allIdChangesJson) {
            $allIdChanges = $allIdChangesJson;
        } else {
            $allIdChanges = [];
        }
        $allIdChangesNonRelations = [];
        foreach ($jsonFile as $importDB => $importDatasets) {
            if ($importDB == 'relations' or $importDB == 'hexValues') {
                break;
            }

            $firstPrimaryChange = true;
            foreach ($importDatasets as $importDataset) {
                $importDataset = (array) $importDataset;
                $primaryImportRelationTable = in_array($importDB, $relationTablesPrimary) ? $importDB : false;
                foreach ($importDataset as $importDbField => $importDbValue) {
                    if ($importDbField == 'id') {
                        if ($primaryImportRelationTable) {
                            if (in_array($importDbField, $dbRelationPrimary[$importDB]) && is_numeric($importDbValue)) {
                                if ($firstPrimaryChange) {
                                    $highestId = $this->Database->prepare("SELECT * FROM $importDB ORDER BY id DESC LIMIT 1")->execute()->fetchAssoc();
                                    if ($highestId && $highestId != 0 && $highestId != '' && $highestId != null) {
                                        $highestId = (Int) $highestId[$importDbField];
                                        $nextId = $highestId + 1;
                                    } elseif (!$highestId) {
                                        $nextId = 1;
                                    }
                                    $firstPrimaryChange = false;
                                } else {
                                    $nextId = end($allIdChanges[$importDB][$importDbField]) + 1;
                                }
                                if (!isset($allIdChanges[$importDB][$importDbField][$importDbValue])) {
                                    $allIdChanges[$importDB][$importDbField][$importDbValue] = $nextId ?? 'nextId';
                                }
                                unset($nextId);
                            }
                        } else {
                            if (is_numeric($importDbValue)) {
                                if ($firstPrimaryChange) {
                                    $highestId = $this->Database->prepare("SELECT * FROM $importDB ORDER BY id DESC LIMIT 1")->execute()->fetchAssoc();
                                    if ($highestId && $highestId != 0 && $highestId != '' && $highestId != null) {
                                        $highestId = (Int) $highestId[$importDbField];
                                        $nextId = $highestId + 1;
                                    } elseif (!$highestId) {
                                        $nextId = 1;
                                    }
                                    $firstPrimaryChange = false;
                                } else {
                                    $nextId = end($allIdChangesNonRelations[$importDB][$importDbField]) + 1;
                                }
                                $allIdChangesNonRelations[$importDB][$importDbField][$importDbValue] = $nextId ?? 'nextId';
                                unset($nextId);
                            }
                        }

                        break;
                    }
                }
            }
        }

        return ['allIdChanges' => $allIdChanges, 'allIdChangesNonRelations' => $allIdChangesNonRelations];
    }

    private function changeDbValue($importDB, $importDbField, $importDbValue, $allIdChanges, $relations)
    {
        if (is_object($relations['relations'])) {
            $relations = (Array) $relations['relations'];
        }
        $primaryRelation = $relations[$importDB . '.' . $importDbField];
        $primaryRelation = explode('.', $primaryRelation);
        if (!is_array($importDbValue)) {
            $newValue = $allIdChanges[$primaryRelation[0]][$primaryRelation[1]][$importDbValue];

            return (String) $newValue;
        }
        foreach ($importDbValue as $key => $value) {
            if (is_array($value)) {
                $newValue[$key] = $this->changeDbValue($importDB, $importDbField, $value, $allIdChanges, $relations);
            } elseif (is_numeric($value) && $allIdChanges[$primaryRelation[0]][$primaryRelation[1]][$value]) {
                $newValue[$key] = (String) $allIdChanges[$primaryRelation[0]][$primaryRelation[1]][$value];
            } else {
                $newValue[$key] = (String) $importDbValue[$key];
            }
        }

        return $newValue ?? $importDbValue;
    }

    public function isUuid($uuid)
    {
        if (ctype_xdigit($uuid) && strlen($uuid) == 32) {
            return true;
        }

        return false;
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

    public function importRunning($running = false, $id = 0)
    {
        if ($id == 0) {
            $importRunning = $this->Database->prepare("SELECT id FROM tl_c4g_import_data WHERE importRunning = '1'")
                ->execute()->fetchAllAssoc();
            if ($importRunning) {
                foreach ($importRunning as $import) {
                    $this->Database->prepare("UPDATE tl_c4g_import_data SET tstamp=?, importRunning='' WHERE tstamp<=? AND importRunning='1' AND id=?")
                        ->execute(time(), time() - 600, $import['id']);
                }
                $importRunning = $this->Database->prepare("SELECT id FROM tl_c4g_import_data WHERE importRunning = '1'")
                    ->execute()->fetchAllAssoc();
                if ($importRunning) {
                    return true;
                }

                return false;
            }

            return false;
        } elseif ($id != 0) {
            if ($running) {
                $this->Database->prepare("UPDATE tl_c4g_import_data SET tstamp=?, importRunning = '1' WHERE id=?")->execute(time(), $id);
            } else {
                $this->Database->prepare("UPDATE tl_c4g_import_data SET tstamp=?, importRunning = '' WHERE id=?")->execute(time(), $id);
            }
        }
    }

    public function cpy($source, $dest)
    {
        if (is_dir($source)) {
            $dir_handle = opendir($source);
            while ($file = readdir($dir_handle)) {
                if ($file != '.' && $file != '..') {
                    if (is_dir($source . '/' . $file)) {
                        if (!is_dir($dest . '/' . $file)) {
                            mkdir($dest . '/' . $file);
                        }
                        $this->cpy($source . '/' . $file, $dest . '/' . $file);
                    } else {
                        copy($source . '/' . $file, $dest . '/' . $file);
                    }
                }
            }
            closedir($dir_handle);
        } else {
            copy($source, $dest);
        }
    }

    public function chmod_r($path, $modeDirectory = false, $modeFile = false)
    {
        $dir = new DirectoryIterator($path);
        if ($modeDirectory || $modeFile) {
            foreach ($dir as $item) {
                if ($item->isDir() && $modeDirectory) {
                    chmod($item->getPathname(), $modeDirectory);
                    if ($item->isDir() && !$item->isDot()) {
                        $this->chmod_r($item->getPathname(), $modeDirectory, $modeFile);
                    }
                } elseif (!$item->isDir() && $modeFile) {
                    chmod($item->getPathname(), $modeFile);
                }
            }
        }
    }

    public function deleteOlderImports($uuid, $con4gisDeleteTables)
    {
        if (strlen($uuid) > 5) {
            $importDatasetId = substr($uuid, 0, -5);
            $likeOperator = $importDatasetId . '_____';
        } else {
            $importDatasetId = $uuid;
            $likeOperator = $uuid;
        }

        if ($likeOperator == 0 or $likeOperator == '0_____' or $likeOperator == '_____') {
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
                            C4gLogModel::addLogEntry('core', 'Error deleting data from database. Abort import. ' . $e);

                            return false;
                        }
                    }
                }
            }
        }

        return true;
    }
}
