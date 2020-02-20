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
$GLOBALS['TL_LANG']['tl_c4g_io_data']['tstamp'] = array("Zeitstempel","");
$GLOBALS['TL_LANG']['tl_c4g_io_data']['caption'] = array("Name des Imports","");
$GLOBALS['TL_LANG']['tl_c4g_io_data']['bundles'] = array("Betroffene Bundles","");
$GLOBALS['TL_LANG']['tl_c4g_io_data']['bundlesVersion'] = array("Bundle Versionen","");
$GLOBALS['TL_LANG']['tl_c4g_io_data']['description'] = array("Beschreibung","");
$GLOBALS['TL_LANG']['tl_c4g_io_data']['con4gisImport'] = array("Importtemplate","");
$GLOBALS['TL_LANG']['tl_c4g_io_data']['importVersion'] = array("Importierte Version","");
$GLOBALS['TL_LANG']['tl_c4g_io_data']['availableVersion'] = array("Verfügbare Version","");

/** OPERATIONS **/
$GLOBALS['TL_LANG']['tl_c4g_io_data']['importData'] = array("Daten importieren","");
$GLOBALS['TL_LANG']['tl_c4g_io_data']['updateData'] = array("Daten aktualisieren","");
$GLOBALS['TL_LANG']['tl_c4g_io_data']['releaseData'] = array("Datenimport lösen","");
$GLOBALS['TL_LANG']['tl_c4g_io_data']['deleteData'] = array("Daten löschen","");

/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_c4g_io_data']['new'] = array('Neuer Import', 'Einen neuen Import erstellen');
$GLOBALS['TL_LANG']['tl_c4g_io_data']['importDialog'] = 'Möchten Sie die Daten jetzt importieren?';
$GLOBALS['TL_LANG']['tl_c4g_io_data']['deleteImportDialog'] = 'Möchten Sie den Import wirklich löschen?';
$GLOBALS['TL_LANG']['tl_c4g_io_data']['updateImportDialog'] = 'Bei einem Update werden sämtliche benutzerdefinierten Einstellungen überschrieben. Möchten Sie wirklich fortfahren?';
$GLOBALS['TL_LANG']['tl_c4g_io_data']['releaseImportDialog'] = 'Hiermit wird der Import gelöst und kann nicht mehr aktualisiert werden. Alle Daten bleiben erhalten. Möchten Sie wirklich fortfahren?';
$GLOBALS['TL_LANG']['tl_c4g_io_data']['con4gisIoImportData'] = 'Übersicht aller Importe';

/** INFOTEXT */
$GLOBALS['TL_LANG']['tl_c4g_io_data']['infotext'] = 'Hier können Sie Grund- und Demodaten importieren bzw. aktualisieren. Wenn Sie einen <a href="https://con4gis.io"><b>con4gis.io</b></a> Zugang haben, stehen Ihnen über die Server weitere Importe zur Verfügung.';
$GLOBALS['TL_LANG']['tl_c4g_io_data']['infotextNoKey'] = 'Sie haben keinen con4gis.io Zugang hinterlegt. Damit Ihnen weitere Importe angezeigt werden benötigen Sie einen. Diesen können Sie unter <a href="https://con4gis.io"><b>con4gis.io</b></a> erstellen.';