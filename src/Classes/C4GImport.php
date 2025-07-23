<?php

namespace con4gis\CoreBundle\Classes;

use Composer\InstalledVersions;
use con4gis\CoreBundle\Classes\Events\AdditionalImportProxyDataEvent;
use con4gis\CoreBundle\Classes\Events\AfterImportEvent;
use con4gis\CoreBundle\Classes\Events\ImportSkipDatasetEvent;
use con4gis\CoreBundle\Resources\contao\models\C4gLogModel;
use con4gis\CoreBundle\Resources\contao\models\C4gSettingsModel;
use Contao\Database;
use Contao\Folder;
use Contao\PageRedirect;
use Contao\StringUtil;
use Contao\System;
use ZipArchive;
use Dflydev\DotAccessData\Data;
use JsonMachine\Items;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Yaml\Parser;

class C4GImport
{

    private Database $database;
    
    public function __construct()
    {
        $this->database = Database::getInstance();
    }

    /**
     * @param $cron
     * @return array|void
     */
    public function loadBaseData($cron)
    {
        $cronIds = [];
        // Get installed contao and con4gis Core version


        if (System::getContainer()->hasParameter('kernel.packages')) {
            $installedPackages = System::getContainer()->getParameter('kernel.packages');
            $coreVersion = $installedPackages['con4gis/core'];
            $contaoVersion = $installedPackages['contao/core-bundle'];
        }
        else {
            $installedPackages = InstalledVersions::getInstalledPackages();
            if (array_search('con4gis/core', $installedPackages)) {
                $coreVersion = InstalledVersions::getVersion('con4gis/core');
            }
            if (array_search('contao/core-bundle', $installedPackages)) {
                $contaoVersion = InstalledVersions::getVersion('contao/core-bundle');
            }
        }


        // Check current action
        $responses = $this->getCon4gisImportData(
            'getBasedata.php',
            'allData',
            false,
            $coreVersion,
            $contaoVersion
        );
        if ($responses) {
            foreach ($responses as $response) {
                foreach ($response as $innerKey => $innerResponse) {
                    $response->$innerKey = str_replace('"', '', $innerResponse);
                }
            }
        }
        $responsesLength = is_countable($responses) ? count($responses) : 0;
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
        $localData = $this->database->prepare('SELECT * FROM tl_c4g_import_data')->execute();
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
                        $statement = $this->database->prepare(
                            'INSERT INTO tl_c4g_import_data '.
                            'SET tstamp = ?, id = ?, bundles = ?, bundlesVersion = ?, availableVersion = ?, '.
                            'type = ?, source = ?, importTables = ?'
                        );
                        $statement->execute(
                            time(),
                            $response->id,
                            $response->bundles,
                            $response->bundlesVersion,
                            $response->version,
                            $response->type,
                            $response->source,
                            $response->tables
                        );
                        if (C4GUtils::stringContains($response->tables, $response->type)) {
                            $cronIds[] = $response->id;
                        }
                    } else {
                        $statement = $this->database->prepare(
                            'INSERT INTO tl_c4g_import_data '.
                            'SET tstamp = ?, id = ?, caption = ?, description = ?, bundles = ?, bundlesVersion = ?, '.
                            'availableVersion = ?, type = ?, source = ?, importTables = ?'
                        );
                        $statement->execute(
                            time(),
                            $response->id,
                            $response->caption,
                            $response->description,
                            $response->bundles,
                            $response->bundlesVersion,
                            $response->version,
                            $response->type,
                            $response->source,
                            $response->tables
                        );

                    }
                }
            }
        }

        foreach ($localData as $data) {
            $available = false;
            foreach ($responses as $response) {
                if (isset($response->datatype) && $response->datatype == 'diff') {
                    continue;
                }
                if (!property_exists($response, 'tables') || !$response->tables) {
                    $response->tables = '';
                }
                if ($response->id == $data['id']) {
                    if ($this->checkImportResponse($response)) {
                        if ($cron) {
                            $statement = $this->database->prepare(
                                'UPDATE tl_c4g_import_data SET tstamp = ?, bundles = ?, bundlesVersion = ?, '.
                                'availableVersion = ?, type = ?, source = ?, importTables = ? '.
                                'WHERE id = ?'
                            );
                            $statement->execute(
                                time(),
                                $response->bundles,
                                $response->bundlesVersion,
                                $response->version,
                                $response->type,
                                $response->source,
                                $response->tables,
                                $data['id']
                            );
                            if (C4GUtils::stringContains($response->tables, $response->type)) {
                                $cronIds[] = $response->id;
                            }
                        } else {
                            $statement = $this->database->prepare(
                                'UPDATE tl_c4g_import_data SET tstamp = ?, caption = ?, description = ?, bundles = ?, '.
                                'bundlesVersion = ?, availableVersion = ?, type = ?, source = ?, importTables = ? '.
                                'WHERE id = ?'
                            );
                            $statement->execute(
                                time(),
                                $response->caption,
                                $response->description,
                                $response->bundles,
                                $response->bundlesVersion,
                                $response->version,
                                $response->type,
                                $response->source,
                                $response->tables,
                                $data['id']
                            );
                        }
                    } elseif ($data['importVersion'] == '' || $data['importVersion'] == '0') {
                        if ($data['id']) {
                            $statement = $this->database->prepare(
                                'DELETE FROM tl_c4g_import_data WHERE id = ?'
                            );
                            $statement->execute($data['id']);
                        }
                    } else {
                        $statement = $this->database->prepare(
                            'UPDATE tl_c4g_import_data SET tstamp = ?, availableVersion = ? WHERE id = ?'
                        );
                        $statement->execute(time(), '', $data['id']);
                    }
                    $available = true;
                }
            }
            //Delete Import if it's not available anymore
            if (!$available) {
                if ($data['importVersion'] != '' || $data['importVersion'] != '0') {
                    $statement = $this->database->prepare(
                        'UPDATE tl_c4g_import_data SET tstamp = ?, availableVersion = ? WHERE id = ?'
                    );
                    $statement->execute(time(), '', $data['id']);
                } else {
                    if ($data['id'] != 0 or $data['id'] != '') {
                        $statement = $this->database->prepare(
                            'DELETE FROM tl_c4g_import_data WHERE id=?'
                        );
                        $statement->execute($data['id']);
                    } else {
                        C4gLogModel::addLogEntry(
                            'core',
                            'Error deleting unavailable import: wrong id set!'
                        );
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
            $arrayLength = is_countable($localData) ? count($localData) - 1 : 0;
            foreach ($localData as $data) {
                if ($data['id'] == $response->id) {
                    break;
                }
                if ($count == $arrayLength) {
                    if ($this->checkImportResponse($response)) {
                        $statement = $this->database->prepare(
                            'INSERT INTO tl_c4g_import_data SET tstamp = ?, id = ?, caption = ?, '.
                            'description = ?, bundles = ?, bundlesVersion = ?, '.
                            'availableVersion = ?, type = ?, source = ?, importTables = ?'
                        );
                        $statement->execute(
                            time(),
                            $response->id,
                            $response->caption,
                            $response->description,
                            $response->bundles,
                            $response->bundlesVersion,
                            $response->version,
                            $response->type,
                            $response->source,
                            $response->tables
                        );
                    }
                }
                $count++;
            }
        }

        if ($cron) {
            return $cronIds;
        }
    }

    /**
     * @param $importId
     * @return false|void
     * @throws Exception
     */
    public function importBaseData($importId = false, $output = false)
    {
        if ($this->importRunning()) {
            $output->writeln("Import already running");
            return false;
        }
        System::loadLanguageFile('tl_c4g_import_data');
        $availableLocal = false;
        if ($importId) {
            $con4gisImportId = $importId;
            $statement = $this->database->prepare(
                'SELECT importVersion, availableVersion FROM tl_c4g_import_data WHERE id = ?'
            );
            $cronImportData = $statement->execute($con4gisImportId)->fetchAssoc();
            if (!$cronImportData['availableVersion'] || ($cronImportData['importVersion'] && ($cronImportData['importVersion'] >= $cronImportData['availableVersion']))) {
                return false;
            }

            $this->importRunning(true, $con4gisImportId);
        } else {
            $data = $_REQUEST;
            $con4gisImportId = $data['id'];
            $localImportData = $this->getLocalIoData();
            foreach ($localImportData as $localImportDatum) {
                if ($localImportDatum['import']['id'] == $con4gisImportId) {
                    $availableLocal = true;
                    $importData = $localImportDatum;
                    break;
                }
            }
        }

        $rootDir = System::getContainer()->getParameter('kernel.project_dir');

        if ($availableLocal) {
            $imagePath = $rootDir.'/files' . $importData['images']['path'];
            $c4gPath = $rootDir.'/vendor/con4gis/' . $importData['general']['bundle'] . '/src/Resources/con4gis/' . $importData['general']['filename'];
            $cache = $rootDir.'/files/con4gis_import_data/io-data/' . str_replace('.c4g', '', $importData['general']['filename']);
            $importType = $importData['import']['type'];
            $importDataType = $importData['import']['datatype'] ?? 'full';

            $alreadyImported = $this->database->prepare('SELECT importVersion FROM tl_c4g_import_data WHERE id=?')->execute($con4gisImportId)->fetchAssoc();
            if ($alreadyImported['importVersion'] != '') {
                if ($importId && $importDataType == 'full') {
                    $deleted = $this->deleteBaseData($importId, true, true);
                    if (!$deleted) {
                        $this->importRunning(false, $con4gisImportId);
                        if ($output) {
                            $output->writeln($GLOBALS['TL_LANG']['tl_c4g_import_data']['errorDeleteImports']);
                        }
                        C4gLogModel::addLogEntry('core', 'Error deleting old import data for automatic import. Stopped Import');
                        PageRedirect::redirect(System::getContainer()->get('router')->generate('contao_backend') .'?do=c4g_io_data');
                        return false;
                    }
                } elseif ($importDataType == 'full') {
                    $deleted = $this->deleteBaseData(false, true, true);
                    if (!$deleted) {
                        $this->importRunning(false, $con4gisImportId);
                        if ($output)  {
                            $output->writeln($GLOBALS['TL_LANG']['tl_c4g_import_data']['errorDeleteImports']);
                        }
                        PageRedirect::redirect(System::getContainer()->get('router')->generate('contao_backend') .'?do=c4g_io_data');
                        return false;
                    }
                }
            } elseif (!$alreadyImported && $importId) {
                C4gLogModel::addLogEntry(
                    'core',
                    'Cant update automatically. Import not found in database. Abort import.'
                );
                $this->importRunning(false, $con4gisImportId);

                return false;
            }

            $zip = new ZipArchive();

            if ($zip->open($c4gPath) === true) {
                $zip->extractTo($cache);

                mkdir($imagePath, 0770, true);
                if (!$this->copy($cache . '/images', $imagePath)) {
                    C4gLogModel::addLogEntry(
                        'core',
                        'Error when copying the import data.'
                    );
                }
                $objFolder = new Folder('files/con4gis_import_data');
                $objFolder->unprotect();
                $objFolder = new Folder('files' . $importData['images']['path']);
                $objFolder->unprotect();
            }
            $zip->close();
            $this->recursivelyChangeFilePermissions($imagePath, 0775, 0664);
        } else {
            $objSettings = C4gSettingsModel::findSettings();
            $baseDataUrl = rtrim($objSettings->con4gisIoUrl, '/') . '/' . 'getBasedata.php';
            $baseDataUrl .= '?key=' . $objSettings->con4gisIoKey;
            $baseDataUrl .= '&mode=' . 'ioData';
            $baseDataUrl .= '&data=' . $con4gisImportId;
            if ($importId) {
                $currentImport = $this->database->prepare('SELECT importVersion, availableVersion FROM tl_c4g_import_data WHERE id=?')
                    ->execute($con4gisImportId)->fetchAssoc();
                if ($currentImport) {
                    $importVersion = (int) $currentImport['importVersion'];
                    $availableVersion = (int) $currentImport['availableVersion'];
                    if ((($importVersion + 1) != $availableVersion) || $importVersion == '') {
                        $baseDataUrl .= '&datatype=full';
                    } else {
                        $baseDataUrl .= '&datatype=diff';
                    }
                } else {
                    $baseDataUrl .= '&datatype=diff';
                }
            } else {
                $baseDataUrl .= '&datatype=full';
            }

            $downloadPath = $rootDir.'/files/con4gis_import_data/io-data/';
            $filename = 'io-data-proxy.c4g';
            $downloadFile = $downloadPath . $filename;

            //remove old files
            if (file_exists($downloadFile) || is_dir($downloadPath)) {
                $this->recursiveRemoveDirectory($downloadPath);
            }

            if (!mkdir($downloadPath, 0770, true)) {
                C4gLogModel::addLogEntry(
                    'core',
                    'Cant create path: '.$downloadPath.'. Abort import.'
                );
                $this->importRunning(false, $con4gisImportId);
                PageRedirect::redirect(System::getContainer()->get('router')->generate('contao_backend') .'?do=c4g_io_data');
                return false;
            }

            $downloadSuccess = $this->download($baseDataUrl, $downloadFile);
            if (!$downloadSuccess) {
                C4gLogModel::addLogEntry(
                    'core',
                    'Cant update. Import file cannot be downloaded. Abort import.'
                );
                $this->importRunning(false, $con4gisImportId);
                PageRedirect::redirect(System::getContainer()->get('router')->generate('contao_backend') .'?do=c4g_io_data');
                return false;
            }

            // Getting data from download's json config file
            $archive = new ZipArchive();
            $zip = $archive->open($downloadPath . $filename, ZipArchive::RDONLY);
            $extracted = false;
            if ($zip) {
                for ($i = 0; $i < $archive->numFiles; $i++) {
                    $statIndex = $archive->statIndex($i);
                    if ($statIndex['name'] == 'io-data.yml') {
                        $contents = $archive->getFromName($statIndex['name']);
                        $yaml = new Parser();
                        $importData = $yaml->parse($contents);
                    }
                }
            }
            $archive->close();

            //Check import data
            if (!$importData['import']['version'] || !$importData['import']['uuid'] || !$importData['images']['path'] || !$con4gisImportId) {
                C4gLogModel::addLogEntry(
                    'core',
                    'Cant update. Import file is empty. Abort import.'
                );
                $this->importRunning(false, $con4gisImportId);
                PageRedirect::redirect(System::getContainer()->get('router')->generate('contao_backend') .'?do=c4g_io_data');
                return false;
            }

            $importDataType = $importData['import']['datatype'] ?? 'full';

            $alreadyImported = $this->database->prepare(
                'SELECT importVersion FROM tl_c4g_import_data WHERE id = ?'
            )->execute($con4gisImportId)->fetchAssoc();
            if ($alreadyImported && $alreadyImported['importVersion']) {
                if ($importId && $importDataType == 'full') {
                    $deleted = $this->deleteBaseData($importId, true, true);
                    if (!$deleted) {
                        $this->importRunning(false, $con4gisImportId);

                        if ($output) {
                            $output->writeln($GLOBALS['TL_LANG']['tl_c4g_import_data']['errorDeleteImports']);
                        }

                        return false;
                    }
                } elseif ($importDataType == 'full') {
                    $deleted = $this->deleteBaseData(false, true, true);
                    if (!$deleted) {
                        $this->importRunning(false, $con4gisImportId);

                        if ($output) {
                            $output->writeln($GLOBALS['TL_LANG']['tl_c4g_import_data']['errorDeleteImports']);
                        }

                        PageRedirect::redirect(System::getContainer()->get('router')->generate('contao_backend') .'?do=c4g_io_data');

                        return false;
                    }
                }
            } elseif (!$alreadyImported && $importId) {
                C4gLogModel::addLogEntry(
                    'core',
                    'Cant update automatically. Import information not found in database. Abort import.'
                );
                $this->importRunning(false, $con4gisImportId);

                return false;
            }

            $importType = $importData['import']['type'];

            $imagePath = $rootDir.'/files' . $importData['images']['path'];


            $cache = $rootDir.'/files/con4gis_import_data/io-data/' . str_replace('.c4g', '', $importData['general']['filename']);

            $zip = new ZipArchive();
            if ($zip->open($downloadPath . $filename) === true) {
                $zip->extractTo($cache);

                mkdir($imagePath, 0770, true);
                $this->copy($cache . '/images', $imagePath);
                $objFolder = new Folder('files/con4gis_import_data');
                $objFolder->unprotect();
                $objFolder = new Folder('files' . $importData['images']['path']);
                $objFolder->unprotect();
            }
            $zip->close();
            $this->recursivelyChangeFilePermissions($imagePath, 0775);

            if (!$this->copy($cache . '/images', $imagePath)) {
                C4gLogModel::addLogEntry(
                    'core',
                    'Error when copying the import data.'
                );
            }

            $objFolder = new Folder('files/con4gis_import_data');
            $objFolder->unprotect();
            $objFolder = new Folder('files' . $importData['images']['path']);
            $objFolder->unprotect();
            $this->recursivelyChangeFilePermissions($imagePath, 0775);
        }

        $file = file_get_contents($cache . '/data/' . str_replace('.c4g', '.json', $importData['general']['filename']));
        $sqlStatements = $this->getSqlFromJson($file, $importData['import']['uuid'], $importDataType, $importData['images']['path']);
        if ($importDataType == 'diff') {
            $this->deleteOldDiffImages($file);
        }

        if (!$sqlStatements) {
            C4gLogModel::addLogEntry('core', 'Error inserting/updating in database');
            if ($output) {
                $output->writeln($GLOBALS['TL_LANG']['tl_c4g_import_data']['importError']);
            }
            $this->importRunning(false, $con4gisImportId);
            if (!$importId) {
                $objFolder = new Folder('files/con4gis_import_data/io-data/');
                $objFolder->purge();
                $objFolder->delete();
                $objFolder = new Folder('files' . $importData['images']['path']);
                $objFolder->purge();
                $objFolder->delete();
            }

            return false;
        }

        foreach ($sqlStatements as $sqlStatement) {
            if ($sqlStatement == '') {
                C4gLogModel::addLogEntry(
                    'core',
                    'Find empty sql statement'
                );
                continue; //ToDo check
            }
            else {
                C4gLogModel::addLogEntry(
                    'core',
                    $sqlStatement
                );
            }

            try {
                $this->database->query($sqlStatement);
            } catch (\Exception $e) {
                $output->writeln('Error while executing SQL-Import: ' . $e->getMessage());
                C4gLogModel::addLogEntry(
                    'core',
                    'Error while executing SQL-Import: ' . $e->getMessage()
                );
            }
        }
        $statement = $this->database->prepare(
            'UPDATE tl_c4g_import_data SET tstamp=?, importVersion = ?, importUuid = ?, importFilePath = ? WHERE id = ?'
        );
        $statement->execute(
            time(),
            $importData['import']['version'],
            $importData['import']['uuid'],
            $importData['images']['path'],
            $con4gisImportId
        );

        $objFolder = new Folder('files/con4gis_import_data/io-data/');
        $objFolder->purge();
        $objFolder->delete();

        $automator = System::importStatic('Contao\Automator', 'Automator');
        $automator->generateSymlinks();

        if (!isset($importType)) {
            $importType = 'notype';
        }
        $event = new AfterImportEvent();
        $event->setImportType($importType);
        $dispatcher = System::getContainer()->get('event_dispatcher');
        $dispatcher->dispatch($event, $event::NAME);
        $error = $event->getError();

        if ($error && $output) {
            $output->writeln($error);
        }

        $this->importRunning(false, $con4gisImportId);

        C4gLogModel::addLogEntry('core', 'The import data was successfully imported.');
    }

    /**
     * @param $importId
     * @param $download
     * @param $update
     * @return bool
     * @throws Exception
     */
    public function deleteBaseData($importId = false, $download = false, $update = false)
    {
        if (!$download) {
            if ($this->importRunning()) {
                C4gLogModel::addLogEntry('core', 'Import already running. Try again later ');
                return false;
            }
        }

        $rootDir = System::getContainer()->getParameter('kernel.project_dir');

        if ($importId) {
            $con4gisDeleteId = $importId;
        } else {
            $data = $_REQUEST;
            $con4gisDeleteId = $data['id'];
        }

        if (!$download) {
            $this->importRunning(true, $con4gisDeleteId);
        }

        $localData = $this->database->prepare(
            'SELECT * FROM tl_c4g_import_data WHERE id = ?'
        )->execute($con4gisDeleteId)->fetchAssoc();
        $con4gisDeleteUuid = $localData['importUuid'];
        $con4gisDeletePath = $localData['importFilePath'];
        $con4gisDeleteDirectory = $rootDir.'/files' . $con4gisDeletePath . '/';
        $con4gisDeleteUuidLength = strlen($con4gisDeleteUuid);
        $con4gisDeleteTables = $localData['importTables'];
        if ($con4gisDeleteTables == null or $con4gisDeleteTables == '') {
            $con4gisDeleteTables = ['tl_c4g_'];
        } else {
            $con4gisDeleteTables = explode(',', $con4gisDeleteTables);
            $con4gisDeleteTables = str_replace(' ', '', $con4gisDeleteTables);
        }

        if ($con4gisDeleteUuid != 0 && $con4gisDeleteUuid != '' && $con4gisDeleteUuidLength >= 6) {
            if ($importId) {
                $con4gisImportFolderScan = array_diff(scandir($rootDir.'/files/con4gis_import_data'), ['.', '..']);
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
                    Message::addError($GLOBALS['TL_LANG']['tl_c4g_import_data']['olderImport']);
                    PageRedirect::redirect(System::getContainer()->get('router')->generate('contao_backend') .'?do=c4g_io_data');
                    return false;
                }
            }

            if ($con4gisDeletePath != '') {
                if (is_dir($con4gisDeleteDirectory)) {
                    unlink($con4gisDeleteDirectory . '/.public');
                    if (strpos($con4gisDeleteDirectory, '/files/con4gis_import_data/')) {
                        $objFolder = new Folder('files' . $con4gisDeletePath);
                        if (!$objFolder->isEmpty()) {
                            $objFolder->purge();
                        }
                        $objFolder->delete();


                        $con4gisImportFolderScan = array_diff(scandir($rootDir.'/files/con4gis_import_data'), ['.', '..']);
                        if (strlen($con4gisDeleteUuid) > 5) {
                            $con4gisDeleteDatasetId = substr($con4gisDeleteUuid, 0, -5);
                        } else {
                            $con4gisDeleteDatasetId = $con4gisDeleteUuid;
                        }
                        foreach ($con4gisImportFolderScan as $con4gisImportFolder) {
                            if (substr($con4gisImportFolder, 0, -5) == $con4gisDeleteDatasetId) {
                                if (is_dir('files/con4gis_import_data/' . $con4gisImportFolder)) {
                                    $objFolder = new Folder('files/con4gis_import_data/' . $con4gisImportFolder);
                                    $objFolder->unprotect();
                                    if (!$objFolder->isEmpty()) {
                                        $objFolder->purge();
                                    }
                                    $objFolder->delete();
                                }
                            }
                        }

//ToDo Why do we always have to delete this directory as well?
//                        $con4gisImportFolderScan = array_diff(scandir($rootDir.'/files/con4gis_import_data'), ['.', '..']);
//                        if (count($con4gisImportFolderScan) == 1) {
//                            if (in_array('.public', $con4gisImportFolderScan)) {
//                                $objFolder = new Folder('files/con4gis_import_data');
//                                $objFolder->unprotect();
//                                $objFolder->delete();
//                            }
//                        }
                        $automator = System::importStatic('Contao\Automator', 'Automator');
                        $automator->generateSymlinks();
                    } else {
                        $this->importRunning(false, $con4gisDeleteId);
                        C4gLogModel::addLogEntry('core', 'Could not delete import directory: Wrong path!');
                    }
                }
            }

            $deletedOldImports = $this->deleteOlderImports($con4gisDeleteUuid, $con4gisDeleteTables);
            if (!$deletedOldImports) {
                $this->importRunning(false, $con4gisDeleteId);
                return false;
            }

            $statement = $this->database->prepare(
                'UPDATE tl_c4g_import_data SET tstamp = ?, importVersion = ?, importUuid = ?, importfilePath = ? WHERE id = ?'
            );
            $statement->execute(time(), '', '0', '', $con4gisDeleteId);
            $this->loadBaseData(false);
        } else {
            C4gLogModel::addLogEntry('core', 'Error deleting unavailable import: wrong id set!');
            return false;
        }

        if (!$download) {
            $this->importRunning(false, $con4gisDeleteId);
        }

        C4gLogModel::addLogEntry('core', 'The import data was successfully deleted.');
        if (!$update) {
            return false;
        }
        return true;
    }

    /**
     * @param $remoteFile
     * @param $localFile
     * @return bool
     */
    private function download($remoteFile, $localFile)
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
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        $contents = '';
        $contentsJson = '';

        try {
            $archive = new ZipArchive();
            $zip = $archive->open($localFile, ZipArchive::RDONLY);
            if ($zip) {
                for ($i = 0; $i < $archive->numFiles; $i++) {
                    $statIndex = $archive->statIndex($i);
                    if ($statIndex['name'] == 'io-data.yml') {
                        $contents = $archive->getFromName($statIndex['name']);
                    }
                    if (C4GUtils::stringContains($statIndex['name'], '.json')) {
                        $contentsJson = $archive->getFromName($statIndex['name']);
                    }
                }
            }
            if ($contents == '' || $contentsJson == '') {
                try {
                    $errorContent = file_get_contents($localFile);
                    if ($errorContent == 'no_file') {
                        C4gLogModel::addLogEntry(
                            'core',
                            'Did not find file on Proxy-Server. '.
                            'Please try again later or contact support@con4gis.org'
                        );
                    } else {
                        C4gLogModel::addLogEntry(
                            'core',
                            'Downloaded import data file (' . $localFile . ') not complete.'
                        );
                    }
                } catch (\Throwable $e) {
                    C4gLogModel::addLogEntry(
                        'core',
                        'Error with downloaded import data file (' . $localFile . '). Error: ' . $e
                    );
                }
                $archive && $zip ? $archive->close() : false;
                return false;
            }
            $archive && $zip ? $archive->close() : false;
            C4gLogModel::addLogEntry('core', 'The import data was successfully downloaded.');
            return true;
        } catch (\Exception $e) {
            C4gLogModel::addLogEntry('core', 'Error reading downloaded file: ' . $e);
            $archive && $zip  ? $archive->close() : false;
            return false;
        }
    }

    /**
     * @param $importData
     * @param $mode
     * @param $data
     * @param $coreVersion
     * @param $contaoVersion
     * @return array|false|mixed|void
     */
    public function getCon4gisImportData($importData, $mode, $data = false, $coreVersion = false, $contaoVersion = false)
    {
        $objSettings = C4gSettingsModel::findSettings();
        if ($objSettings->con4gisIoUrl && $objSettings->con4gisIoKey) {
            $baseDataUrl = rtrim($objSettings->con4gisIoUrl, '/') . '/' . $importData;
            $baseDataUrl .= '?key=' . $objSettings->con4gisIoKey;
            $arrData = [];
            $arrData['mode'] = $mode;
            if (isset($data)) {
                $arrData['data'] = str_replace(' ', '%20', $data);
            }
            if (isset($coreVersion)) {
                $arrData['coreVersion'] = str_replace(' ', '%20', $coreVersion);
            }
            if (isset($contaoVersion)) {
                $arrData['contaoVersion'] = str_replace(' ', '%20', $contaoVersion);
            }

            //Getting additional Data
            $event = new AdditionalImportProxyDataEvent();
            $dispatcher = System::getContainer()->get('event_dispatcher');
            $dispatcher->dispatch($event, $event::NAME);
            $additionalProxyData = $event->getProxyData();

            if ($additionalProxyData) {
                foreach ($additionalProxyData as $proxyData) {
                    $arrData[$proxyData['proxyKey']] = str_replace(' ', '%20', $proxyData['proxyData']);
                }
            }
            $client = HttpClient::create();
            $response = $client->request('GET', $baseDataUrl)->getContent();
            if (!$response) {
                $response = '';
            }

            $request = $client->request(
                'GET',
                $baseDataUrl,
                [
                    'headers' => [
                        'Referer'       => $_SERVER['HTTP_REFERER'] ?: "",
                        'User-Agent'    => $_SERVER['HTTP_USER_AGENT'] ?: ""
                    ],
                    'query' => $arrData
                ]
            );
            $response = $request->getContent();
            if ($response) {
                if (C4GUtils::startsWith($response, '[{') && C4GUtils::endsWith($response, '}]')) {
                    return \json_decode($response);
                }

                return false;
            }

            return [];
        }
    }

    /**
     * @param $importId
     * @return array
     */
    private function getLocalIoData($importId = false): array
    {
        $rootDir = System::getContainer()->getParameter('kernel.project_dir');
        $arrBasedataFolders = [
            'maps' => $rootDir . '/vendor/con4gis/maps/src/Resources/con4gis',
            'visualization' => $rootDir . '/vendor/con4gis/src/visualization/Resources/con4gis',
            'core' => $rootDir . '/vendor/con4gis/core/src/Resources/con4gis',
            'data' => $rootDir . '/vendor/con4gis/data/src/Resources/con4gis',
            'firefighter' => $rootDir . '/vendor/con4gis/firefighter/Resources/con4gis',
        ];

        $basedataFiles = [];

        foreach ($arrBasedataFolders as $arrBasedataFolder => $value) {
            if (file_exists($value) && is_dir($value)) {
                $basedataFiles[$arrBasedataFolder] = array_slice(scandir($value), 2);
                foreach ($basedataFiles[$arrBasedataFolder] as $basedataFile => $file) {
                    if (!$importId && C4GUtils::endsWith($file, '-diff.c4g')) {
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
                    foreach ($basedataFiles[$arrBasedataFolder] as $file) {
                        if (C4GUtils::endsWith($file, '-diff.c4g')) {
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
                    $archive = new ZipArchive();
                    $zip = $archive->open($importFile, ZipArchive::RDONLY);
                    if ($zip) {
                        for ($i = 0; $i < $archive->numFiles; $i++) {
                            $statIndex = $archive->statIndex($i);
                            if ($statIndex['name'] == 'io-data.yml') {
                                $contents = $archive->getFromName($statIndex['name']);
                                $yaml = new Parser();
                                $newYamlConfigArray[$count] = $yaml->parse($contents);
                                $count++;
                            }
                            if (C4GUtils::stringContains($statIndex['name'], '.json')) {
                                $contentsJson = $archive->getFromName($statIndex['name']);
                            }
                        }
                    }
                    $archive->close();
                    if ($contents == '' || $contentsJson == '') {
                        C4gLogModel::addLogEntry('core', 'Import data file (' . $importFile . ') not complete.');
                        $newYamlConfigArray[$count] = false;
                    }
                } catch (\Throwable $e) {
                    C4gLogModel::addLogEntry('core', 'Import data file not complete: ' . $e);
                    $archive ? $archive->close() : false;
                    return [];
                }
            }
        }

        return $newYamlConfigArray;
    }

    /**
     * @param $directory
     * @return void
     */
    private function recursiveRemoveDirectory($directory)
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
     * @param $file
     * @return void
     */
    private function deleteOldDiffImages($file)
    {
        $rootDir = System::getContainer()->getParameter('kernel.project_dir');
        $jsonFile = (array) \json_decode($file);
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

    /**
     * @param $deleteFolder
     * @return void
     * @throws Exception
     */
    private function recursiveDeleteDiffFolder($deleteFolder)
    {
        if (C4GUtils::endsWith($deleteFolder, '/files')) {
            return;
        }
        $rootDir = System::getContainer()->getParameter('kernel.project_dir');
        $folder = new Folder($deleteFolder);
        if ($folder->isEmpty()) {
            $this->recursiveRemoveDirectory($rootDir . '/' . $deleteFolder);
        } else {
            return;
        }
        $deleteCurrentFolder = strrchr($deleteFolder, '/');
        $deletePreFolder = str_replace($deleteCurrentFolder, '', $deleteFolder);
        $this->recursiveDeleteDiffFolder($deletePreFolder);

    }

    /**
     * @param $relations
     * @param $relationTables
     * @param $relationTablesPrimary
     * @param $dbRelation
     * @param $dbRelationPrimary
     * @param $hexValueRelation
     * @param $jsonFile
     * @param $file
     * @param $uuid
     * @param $importDataType
     * @param $imagePath
     * @return array
     */

    private function getSqlFromJson($file, $uuid, $importDataType, $imagePath): array
    {
        $rootDir = System::getContainer()->getParameter('kernel.project_dir');
        if (!$file) {
            return [];
        }

        if ($importDataType == 'diff') {
            $idConfigFile = file_get_contents($rootDir . '/files' . $imagePath . '/id-config.json');
            if ($idConfigFile) {
                $allIdChangesJson = \json_decode($idConfigFile);
                $allIdChangesJson = \json_decode(json_encode($allIdChangesJson), true);
            } else {
                $allIdChangesJson = false;
            }
        } else {
            $allIdChangesJson = false;
        }

        $importId = $uuid;
        $jsonFile = (array) \json_decode($file);
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
            if (is_array($dbRelationPrimary[$secondTable[0]]) && !in_array($secondTable[1], $dbRelationPrimary[$secondTable[0]])) {
                $dbRelationPrimary[$secondTable[0]][] = $secondTable[1];
            } elseif (!is_array($dbRelationPrimary[$secondTable[0]])) {
                $dbRelationPrimary[$secondTable[0]][] = $secondTable[1];
            }
        }

        //Get all changed IDs
        $allChanges = $this->getIdChanges($jsonFile, $relationTablesPrimary, $dbRelationPrimary, $allIdChangesJson);
        $allIdChanges = $allChanges['allIdChanges'];
        $allIdChangesNonRelations = $allChanges['allIdChangesNonRelations'];

        $allIdChangesJson = \json_encode($allIdChanges, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents($rootDir . '/files' . $imagePath . '/id-config.json', $allIdChangesJson);

        try {
            foreach ($jsonFile as $importDB => $importDatasets) {
                //sql statements for deleting removed data
                if ($importDB == 'deleted') {
                    foreach ($importDatasets as $tableKey => $tableDataset) {
                        $validTables = $this->database->listTables();
                        if (!in_array($tableKey, $validTables)) {
                            continue;
                        }
                        foreach ($tableDataset as $dataset) {
                            $dataset = (array) $dataset;
                            $query = "DELETE FROM $tableKey";
                            if ($tableKey == 'tl_files' && !empty($dataset['uuid']) && !empty($dataset['id'])) {
                                $path = stripslashes($dataset['path']);
                                $query .= " WHERE path=$path";
                            } elseif (!empty($dataset['uuid'])) {
                                $query .= " WHERE importId != '' AND importId != 0 AND uuid = '" .
                                    $dataset['uuid'] . "'";
                            } elseif (!empty($dataset['id'])) {
                                $query .= " WHERE importId != '' AND importId != 0 AND id = " .
                                    $allIdChanges[$tableKey]['id'][$dataset['id']];
                                unset($allIdChanges[$tableKey]['id'][$dataset['id']]);
                            } else {
                                continue;
                            }
                            $sqlStatements[] = $query;
                        }
                    }
                    $allIdChangesJson = \json_encode($allIdChanges, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                    file_put_contents($rootDir . '/files' . $imagePath . '/id-config.json', $allIdChangesJson);

                    continue;
                }

                if ($importDB == 'relations' || $importDB == 'hexValues') {
                    break;
                }

                if ($importDataType == 'diff') {
                    $queryType = 'UPDATE';
                } else {
                    $queryType = 'INSERT';
                }
                unset($updateWhereQuery, $updateWhereQueryValue);

                $dbFields = $this->database->getFieldNames($importDB);
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

                    $sqlStatement = '';
                    $importDataset = (array) $importDataset;

                    $event = new ImportSkipDatasetEvent(
                        $importDB,
                        $importDataset,
                        false
                    );

                    $dispatcher = System::getContainer()->get('event_dispatcher');
                    $dispatcher->dispatch($event);

                    if ($event->isSkip()) {
                        continue;
                    }

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
                            $availableQuery = $this->database->prepare('SELECT * FROM ' . $importDB . ' WHERE path=?')
                                ->execute($importDataset['path'])->fetchAssoc();
                        } else {
                            $availableQuery = $this->database->prepare('SELECT * FROM ' . $importDB . " WHERE importId != '' AND importId != 0 AND uuid=?")
                                ->execute($importDataset['uuid'])->fetchAssoc();
                        }
                        if (!$availableQuery) {
                            $queryType = 'INSERT';
                        }
                    } elseif ($queryType == 'UPDATE' && array_key_exists($importDataset['id'], $allIdChanges[$importDB]['id'])) {
                        $availableQuery = $this->database->prepare('SELECT * FROM ' . $importDB . ' WHERE id=?')
                            ->execute($allIdChanges[$importDB]['id'][$importDataset['id']])->fetchAssoc();
                        if (!$availableQuery) {
                            $queryType = 'INSERT';
                        }
                    }

                    if (!array_key_exists('importId', $importDataset)) {
                        $importDataset['importId'] = $importId;
                    }
                    $primaryImportRelationTable = in_array($importDB, $relationTablesPrimary) && $importDB;

                    foreach ($importDataset as $importDbField => $importDbValue) {
                        if ($queryType == 'UPDATE' && in_array('uuid', $dbFields) && ($importDbField == 'id' || $importDbField == 'pid')) {
                            continue;
                        }
                        if ($queryType == 'UPDATE' && $importDbField == 'uuid' && $importDB != 'tl_files') {
                            $updateWhereQueryValue = $importDbValue;
                        } elseif ($queryType == 'UPDATE' && $importDbField == 'path' && $importDB == 'tl_files') {
                            $updateWhereQueryValue = $importDbValue;
                        } elseif (isset($updateWhereQuery) && $updateWhereQuery && $importDbField && $updateWhereQuery == ' WHERE id=' && $importDbField == 'id') {
                            $updateWhereQueryValue = $allIdChanges[$importDB]['id'][$importDataset['id']];
                        }

                        if ($importDbField == 'id') {
                            if ($primaryImportRelationTable) {
                                $importDbValue = $allIdChanges[$importDB][$importDbField][$importDbValue];
                            } else {
                                $importDbValue = $allIdChangesNonRelations[$importDB][$importDbField][$importDbValue];
                            }
                            if ($importDbValue == '') {
                                $importDbValue = 0;
                            }
                        } elseif ($importDbField == 'importId') {
                            $importDbValue = $importId;
                        } elseif (in_array($importDB, $relationTables)) {
                            if (in_array($importDbField, $dbRelation[$importDB])) {
                                if ($importDbValue != '0') {
                                    if (C4GUtils::startsWith($importDbValue, '0x') && $importDB != 'tl_files') {
                                        $unserial = hex2bin(substr($importDbValue, 2));

                                        if (strpos($unserial, '{')) {
                                            $unserial = StringUtil::deserialize($unserial);
                                            $unserial = $this->changeDbValue($importDB, $importDbField, $unserial, $allIdChanges, $relations);
                                            $newImportDbValue = serialize($unserial);
                                            $newImportDbValue = '0x'.bin2hex($newImportDbValue);
                                            $importDbValue = $newImportDbValue;
                                        } else {
                                            $newImportDbValue = $this->changeDbValue($importDB, $importDbField, $unserial, $allIdChanges, $relations);
                                            $newImportDbValue = '0x'.bin2hex($newImportDbValue);
                                            $importDbValue = $newImportDbValue;
                                        }
                                    } elseif (C4GUtils::startsWith($importDbValue, 'a:')) {
                                        $importDbValue = str_replace('\"', '"', $importDbValue);
                                        $unserial = StringUtil::deserialize($importDbValue);
                                        $unserial = $this->changeDbValue($importDB, $importDbField, $unserial, $allIdChanges, $relations);
                                        $newImportDbValue = serialize($unserial);
                                        $importDbValue = $newImportDbValue;
                                    } elseif (strpos($importDbValue, '{')) {
                                        $unserial = hex2bin(substr($importDbValue, 2));
                                        $unserial = StringUtil::deserialize($unserial);
                                        $unserial = $this->changeDbValue($importDB, $importDbField, $unserial, $allIdChanges, $relations);
                                        $newImportDbValue = serialize($unserial);
                                        $newImportDbValue = '0x'.bin2hex($newImportDbValue);
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
                                    if ($sqlStatement == '' && C4GUtils::startsWith($importDbValue, '0x')) {
                                        $sqlStatement = 'INSERT INTO `' . $importDB . '` (`' . $importDbField . '`) VALUES (' . $importDbValue . ');;';
                                    } elseif ($sqlStatement == '' && $isHexValue && $importDbField != 'hash' && $importDbField != 'foreignKey') {
                                        $sqlStatement = 'INSERT INTO `' . $importDB . '` (`' . $importDbField . "`) VALUES (UNHEX('" . $importDbValue . "'));;";
                                    } elseif ($sqlStatement == '' && $this->isUuid($importDbValue) && $importDbField != 'hash' && $importDbField != 'foreignKey') {
                                        $sqlStatement = 'INSERT INTO `' . $importDB . '` (`' . $importDbField . "`) VALUES (UNHEX('" . $importDbValue . "'));;";
                                    } elseif ($sqlStatement == '' && !C4GUtils::startsWith($importDbValue, '0x')) {
                                        $sqlStatement = 'INSERT INTO `' . $importDB . '` (`' . $importDbField . "`) VALUES ('" . $importDbValue . "');;";
                                    } elseif ($sqlStatement == '' && $importDbValue === null) {
                                        $sqlStatement = 'INSERT INTO `' . $importDB . '` (`' . $importDbField . '`) VALUES (NULL);;';
                                    } elseif (C4GUtils::startsWith($importDbValue, '0x')) {
                                        $sqlStatement = str_replace(') VALUES', ", `$importDbField`) VALUES", $sqlStatement);
                                        $sqlStatement = str_replace(');;', ", $importDbValue);;", $sqlStatement);
                                    } elseif ($isHexValue && $importDbField != 'hash' && $importDbField != 'foreignKey') {
                                        $sqlStatement = str_replace(') VALUES', ", `$importDbField`) VALUES", $sqlStatement);
                                        $sqlStatement = str_replace(');;', ", UNHEX('$importDbValue'));;", $sqlStatement);
                                    } elseif ($this->isUuid($importDbValue) && $importDbField != 'hash' && $importDbField != 'foreignKey') {
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
                                    if ($sqlStatement == '' && C4GUtils::startsWith($importDbValue, '0x')) {
                                        $sqlStatement = 'UPDATE `' . $importDB . '` SET ' . $importDbField . ' = ' . $importDbValue . ';;';
                                    } elseif ($sqlStatement == '' && $isHexValue && $importDbField != 'hash' && $importDbField != 'foreignKey') {
                                        $sqlStatement = 'UPDATE `' . $importDB . '` SET ' . $importDbField . " = UNHEX('" . $importDbValue . "');;";
                                    } elseif ($sqlStatement == '' && $this->isUuid($importDbValue) && $importDbField != 'hash' && $importDbField != 'foreignKey') {
                                        $sqlStatement = 'UPDATE `' . $importDB . '` SET ' . $importDbField . " = UNHEX('" . $importDbValue . "');;";
                                    } elseif ($sqlStatement == '' && !C4GUtils::startsWith($importDbValue, '0x')) {
                                        $sqlStatement = 'UPDATE `' . $importDB . '` SET ' . $importDbField . " = '" . $importDbValue . "';;";
                                    } elseif ($sqlStatement == '' && $importDbValue === null) {
                                        $sqlStatement = 'UPDATE `' . $importDB . '` SET ' . $importDbField . ' = NULL;;';
                                    } elseif (C4GUtils::startsWith($importDbValue, '0x')) {
                                        $sqlStatement = str_replace(';;', ", `$importDbField` = $importDbValue;;", $sqlStatement);
                                    } elseif ($isHexValue && $importDbField != 'hash' && $importDbField != 'foreignKey') {
                                        $sqlStatement = str_replace(';;', ", `$importDbField` = UNHEX('$importDbValue');;", $sqlStatement);
                                    } elseif ($this->isUuid($importDbValue) && $importDbField != 'hash' && $importDbField != 'foreignKey') {
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

                    if (
                        $queryType == 'UPDATE' &&
                        isset($updateWhereQuery) &&
                        isset($updateWhereQueryValue) &&
                        $updateWhereQuery != '' &&
                        $updateWhereQueryValue != ''
                    ) {
                        $sqlStatement = str_replace(
                            ';;',
                            $updateWhereQuery . "'" . $updateWhereQueryValue . "';",
                            $sqlStatement
                        );
                    } else {
                        $sqlStatement = str_replace(');;', ');', $sqlStatement);
                    }
                    $sqlStatements[] = $sqlStatement;
                }
            }

        } catch (\Exception $e) {
            C4gLogModel::addLogEntry('core', 'Error translation json to sql. Abort import. ' . $e);

            return false;
        }

        return $sqlStatements;
    }
    /**
     * @param $jsonFile
     * @param $relationTablesPrimary
     * @param $dbRelationPrimary
     * @param $allIdChangesJson
     * @return array
     */
    private function getIdChanges($jsonFile, $relationTablesPrimary, $dbRelationPrimary, $allIdChangesJson): array
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
                $primaryImportRelationTable = in_array($importDB, $relationTablesPrimary) && $importDB;
                foreach ($importDataset as $importDbField => $importDbValue) {
                    if ($importDbField == 'id') {
                        if (is_numeric($importDbValue)) {
                            if (
                                !$primaryImportRelationTable ||
                                in_array($importDbField, $dbRelationPrimary[$importDB])
                            ) {
                                if ($firstPrimaryChange) {
                                    $highestId = $this->database->prepare(
                                        "SELECT * FROM $importDB ORDER BY id DESC LIMIT 1"
                                    )->execute()->fetchAssoc();
                                    if ($highestId) {
                                        $highestId = (int) $highestId[$importDbField];
                                        $nextId = $highestId + 1;
                                    } else {
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
                        }

                        break;
                    }
                }
            }
        }

        return ['allIdChanges' => $allIdChanges, 'allIdChangesNonRelations' => $allIdChangesNonRelations];
    }

    // Do not add return type to ensure BC for PHP < 8
    /**
     * @param $importDB
     * @param $importDbField
     * @param $importDbValue
     * @param $allIdChanges
     * @param $relations
     * @return array|string
     */
    private function changeDbValue($importDB, $importDbField, $importDbValue, $allIdChanges, $relations)
    {
        if (is_object($relations['relations'])) {
            $relations = (array) $relations['relations'];
        }
        $primaryRelation = $relations[$importDB . '.' . $importDbField];
        $primaryRelation = explode('.', $primaryRelation);
        if (!is_array($importDbValue)) {
            $newValue = $allIdChanges[$primaryRelation[0]][$primaryRelation[1]][$importDbValue];

            if (is_numeric($importDbValue) && is_null($newValue)) {
                $newValue = 0;
            }

            return (string) $newValue;
        }
        foreach ($importDbValue as $key => $value) {
            if (is_array($value)) {
                $newValue[$key] = $this->changeDbValue($importDB, $importDbField, $value, $allIdChanges, $relations);
            } elseif (is_numeric($value) && $allIdChanges[$primaryRelation[0]][$primaryRelation[1]][$value]) {
                $newValue[$key] = (string) $allIdChanges[$primaryRelation[0]][$primaryRelation[1]][$value];
            } else {
                $newValue[$key] = (string) $value;
            }
        }

        return $newValue ?? $importDbValue;
    }

    /**
     * @param $uuid
     * @return bool
     */
    private function isUuid($uuid): bool
    {
        return ctype_xdigit($uuid) && strlen($uuid) == 32;
    }

    /**
     * @param $response
     * @return bool
     */
    private function checkImportResponse($response): bool
    {
        $response = (array) $response;
        $requiredKeys = [
            'cloud_import',
            'uuid',
            'id',
            'caption',
            'description',
            'version',
            'bundles',
            'bundlesVersion',
            'source',
            'type'
        ];

        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $response)) {
                C4gLogModel::addLogEntry(
                    'core',
                    'Could not read import file or import file is not complete.'
                );
                return false;
            }
        }

        return true;
    }

    /**
     * @param $running
     * @param $id
     * @return bool|void
     */
    private function importRunning($running = false, $id = 0)
    {
        if ($id == 0) {
            $importRunning = $this->database->prepare(
                "SELECT id FROM tl_c4g_import_data WHERE importRunning = '1'"
            )->execute()->fetchAllAssoc();
            if ($importRunning) {
                foreach ($importRunning as $import) {
                    $this->database->prepare(
                        "UPDATE tl_c4g_import_data SET tstamp = ?, importRunning = '' ".
                        "WHERE tstamp <= ? AND importRunning = '1' AND id = ?"
                    )->execute(time(), time() - 600, $import['id']);
                }
                $importRunning = $this->database->prepare(
                    "SELECT id FROM tl_c4g_import_data WHERE importRunning = '1'"
                )->execute()->fetchAllAssoc();
                if ($importRunning) {
                    return true;
                }

                return false;
            }

            return false;
        } else {
            if ($running) {
                $this->database->prepare(
                    "UPDATE tl_c4g_import_data SET tstamp = ?, importRunning = '1' WHERE id= ?"
                )->execute(time(), $id);
            } else {
                $this->database->prepare(
                    "UPDATE tl_c4g_import_data SET tstamp = ?, importRunning = '' WHERE id = ?"
                )->execute(time(), $id);
            }
        }
    }

    /**
     * @param $source
     * @param $dest
     * @return bool
     */
    private function copy($source, $dest)
    {
        $result = false;
        if (is_dir($source)) {
            $dir_handle = opendir($source);
            while ($file = readdir($dir_handle)) {
                if ($file != '.' && $file != '..') {
                    if (is_dir($source . '/' . $file)) {
                        if (!is_dir($dest . '/' . $file)) {
                            mkdir($dest . '/' . $file);
                        }
                        $result = $this->copy($source . '/' . $file, $dest . '/' . $file);
                    } else {
                        $result = copy($source . '/' . $file, $dest . '/' . $file);
                    }
                }
            }
            closedir($dir_handle);
        } else {
            $result = copy($source, $dest);
        }

        return $result;
    }

    /**
     * @param $path
     * @param $modeDirectory
     * @param $modeFile
     * @return void
     */
    private function recursivelyChangeFilePermissions($path, $modeDirectory = false, $modeFile = false)
    {
        $dir = new \DirectoryIterator($path);
        if ($modeDirectory || $modeFile) {
            foreach ($dir as $item) {
                if ($item->isDir() && $modeDirectory) {
                    chmod($item->getPathname(), $modeDirectory);
                    if ($item->isDir() && !$item->isDot()) {
                        $this->recursivelyChangeFilePermissions($item->getPathname(), $modeDirectory, $modeFile);
                    }
                } elseif (!$item->isDir() && $modeFile) {
                    chmod($item->getPathname(), $modeFile);
                }
            }
        }
    }

    /**
     * @param $uuid
     * @param $con4gisDeleteTables
     * @return bool
     */
    private function deleteOlderImports($uuid, $con4gisDeleteTables): bool
    {
        if (strlen($uuid) > 5) {
            $importDatasetId = substr($uuid, 0, -5);
            $likeOperator = $importDatasetId . '_____';
        } else {
            $likeOperator = $uuid;
        }

        if ($likeOperator == 0 or $likeOperator == '0_____' or $likeOperator == '_____') {
            return false;
        }

        //Delete import data
        $tables = $this->database->listTables();
        foreach ($tables as $table) {
            foreach ($con4gisDeleteTables as $con4gisDeleteTable) {
                if (C4GUtils::stringContains($table, $con4gisDeleteTable)) {
                    if ($this->database->fieldExists('importId', $table)) {
                        try {
                            if (strpos($table, 'tl_c4g_') !== false) {
                                $this->database->prepare("DELETE FROM $table WHERE importId LIKE ?")->execute($likeOperator);
                            } else {
                                $this->database->prepare("DELETE FROM $table WHERE importId > 0")->execute();
                            }
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