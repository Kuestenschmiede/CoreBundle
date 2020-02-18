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
$GLOBALS['TL_LANG']['tl_c4g_io_data']['id'] = array("ID","");
$GLOBALS['TL_LANG']['tl_c4g_io_data']['tstamp'] = array("Timestamp","");
$GLOBALS['TL_LANG']['tl_c4g_io_data']['caption'] = array("Name of import","");
$GLOBALS['TL_LANG']['tl_c4g_io_data']['bundles'] = array("Affected bundles","");
$GLOBALS['TL_LANG']['tl_c4g_io_data']['bundlesVersion'] = array("Bundle versions","");
$GLOBALS['TL_LANG']['tl_c4g_io_data']['description'] = array("Description","");
$GLOBALS['TL_LANG']['tl_c4g_io_data']['con4gisImport'] = array("Import template","");
$GLOBALS['TL_LANG']['tl_c4g_io_data']['importVersion'] = array("Imported version","");
$GLOBALS['TL_LANG']['tl_c4g_io_data']['availableVersion'] = array("Available version","");

/** OPERATIONS **/
$GLOBALS['TL_LANG']['tl_c4g_io_data']['importData'] = array("Import data","");
$GLOBALS['TL_LANG']['tl_c4g_io_data']['updateData'] = array("Update data","");
$GLOBALS['TL_LANG']['tl_c4g_io_data']['releaseData'] = array("Release data","");
$GLOBALS['TL_LANG']['tl_c4g_io_data']['deleteData'] = array("Delete data","");

/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_c4g_io_data']['new'] = array('New import', 'Create new import');
$GLOBALS['TL_LANG']['tl_c4g_io_data']['importDialog'] = 'Do you really want to import the data now?';
$GLOBALS['TL_LANG']['tl_c4g_io_data']['deleteImportDialog'] = 'Do you really want to delete the import?';
$GLOBALS['TL_LANG']['tl_c4g_io_data']['updateImportDialog'] = 'During an update, all customised settings will be overwritten. Do you really want to continue?';
$GLOBALS['TL_LANG']['tl_c4g_io_data']['releaseImportDialog'] = 'This solves the import and can no longer be updated. All data is retained. Do you really want to continue?';
$GLOBALS['TL_LANG']['tl_c4g_io_data']['con4gisIoImportData'] = 'Overview of all imports';

/** INFOTEXT */
$GLOBALS['TL_LANG']['tl_c4g_io_data']['infotext'] = 'Here you can import and update new basic and demo data. If you have a <a href="https://con4gis.io"><b>con4gis.io</b></a> access, you can import even more data.';
$GLOBALS['TL_LANG']['tl_c4g_io_data']['infotextNoKey'] = 'You have no con4gis.io access. But you need one to be able to see imports. You can create this under <a href="https://con4gis.io"><b>con4gis.io</b></a>.';