<?php
/**
 * con4gis for Contao Open Source CMS
 *
 * @version   php 7
 * @package   con4gis-Core (CoreBundle)
 * @author    con4gis contributors
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2018 - 2018
 * @link      https://www.kuestenschmiede.de
 */

/** FIELDS **/
$GLOBALS['TL_LANG']['tl_c4g_import_data']['id'] = array("ID","");
$GLOBALS['TL_LANG']['tl_c4g_import_data']['tstamp'] = array("Timestamp","");
$GLOBALS['TL_LANG']['tl_c4g_import_data']['caption'] = array("Name of import","");
$GLOBALS['TL_LANG']['tl_c4g_import_data']['type'] = array("Type","");
$GLOBALS['TL_LANG']['tl_c4g_import_data']['source'] = array("Source","");
$GLOBALS['TL_LANG']['tl_c4g_import_data']['bundles'] = array("Affected bricks","");
$GLOBALS['TL_LANG']['tl_c4g_import_data']['bundlesVersion'] = array("Brick versions","");
$GLOBALS['TL_LANG']['tl_c4g_import_data']['description'] = array("Description","");
$GLOBALS['TL_LANG']['tl_c4g_import_data']['con4gisImport'] = array("Import template","");
$GLOBALS['TL_LANG']['tl_c4g_import_data']['importVersion'] = array("Imported version","");
$GLOBALS['TL_LANG']['tl_c4g_import_data']['availableVersion'] = array("Available version","");

/** OPERATIONS **/
$GLOBALS['TL_LANG']['tl_c4g_import_data']['importData'] = array("Import data","");
$GLOBALS['TL_LANG']['tl_c4g_import_data']['updateData'] = array("Update data","");
$GLOBALS['TL_LANG']['tl_c4g_import_data']['releaseData'] = array("Release data","");
$GLOBALS['TL_LANG']['tl_c4g_import_data']['deleteData'] = array("Delete data","");

/**
 * Reference
 */
$GLOBALS['TL_LANG']['tl_c4g_import_data']['type_demo'] = "Demo data";
$GLOBALS['TL_LANG']['tl_c4g_import_data']['type_basedata'] = "Base data";
$GLOBALS['TL_LANG']['tl_c4g_import_data']['source_io'] = "con4gis.io";
$GLOBALS['TL_LANG']['tl_c4g_import_data']['source_locale'] = "Locale";

/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_c4g_import_data']['new'] = array('New import', 'Create new import');
$GLOBALS['TL_LANG']['tl_c4g_import_data']['importDialog'] = 'Do you really want to import the data now?';
$GLOBALS['TL_LANG']['tl_c4g_import_data']['deleteImportDialog'] = 'Do you really want to delete the import?';
$GLOBALS['TL_LANG']['tl_c4g_import_data']['updateImportDialog'] = 'During an update, all customised settings will be overwritten. Do you really want to continue?';
$GLOBALS['TL_LANG']['tl_c4g_import_data']['releaseImportDialog'] = 'This solves the import and can no longer be updated. All data is retained. Do you really want to continue?';
$GLOBALS['TL_LANG']['tl_c4g_import_data']['con4gisIoImportData'] = 'Overview of all imports';

/** INFOTEXT */
$GLOBALS['TL_LANG']['tl_c4g_import_data']['infotext'] = 'Here you can import or update basic and demo data. Some modules provide demo data. If you have <a href="https://con4gis.io"><b>con4gis.io</b></a> access, further basic and demo data imports are available via the servers.';
$GLOBALS['TL_LANG']['tl_c4g_import_data']['infotextNoKey'] = 'You have no con4gis.io access. But you need one to be able to see imports. You can create this under <a href="https://con4gis.io"><b>con4gis.io</b></a>.';
$GLOBALS['TL_LANG']['tl_c4g_import_data']['importRunning'] = 'The import is already running. Only data of one import at a time can be imported.';
$GLOBALS['TL_LANG']['tl_c4g_import_data']['olderImport'] = 'Older import folder in file system. Reimport everything manually.';
$GLOBALS['TL_LANG']['tl_c4g_import_data']['errorDeleteImports'] = 'Error while deleting older imports..';
$GLOBALS['TL_LANG']['tl_c4g_import_data']['releasingError'] = 'Error releasing unavailable import: wrong id set.';
$GLOBALS['TL_LANG']['tl_c4g_import_data']['importError'] = 'The import was not installed. For more information see con4gis log';
$GLOBALS['TL_LANG']['tl_c4g_import_data']['importSuccessfull'] = 'The import was successfully installed.';
$GLOBALS['TL_LANG']['tl_c4g_import_data']['deletedSuccessfull'] = 'The import was successfully deleted.';
$GLOBALS['TL_LANG']['tl_c4g_import_data']['releasedSuccessfull'] = 'The import was successfully release. You can now change data without losing data.';
