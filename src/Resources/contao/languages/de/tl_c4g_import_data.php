<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @author con4gis contributors (see "authors.md")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2026, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */

/**
 * con4gis for Contao Open Source CMS
 *
 * @package   con4gis-Core (CoreBundle)
 * @author    con4gis contributors
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @link      https://www.kuestenschmiede.de
 */

/** FIELDS **/
$GLOBALS['TL_LANG']['tl_c4g_import_data']['id'] = array("ID","");
$GLOBALS['TL_LANG']['tl_c4g_import_data']['tstamp'] = array("Zeitstempel","");
$GLOBALS['TL_LANG']['tl_c4g_import_data']['caption'] = array("Name des Imports","");
$GLOBALS['TL_LANG']['tl_c4g_import_data']['type'] = array("Typ","");
$GLOBALS['TL_LANG']['tl_c4g_import_data']['source'] = array("Quelle","");
$GLOBALS['TL_LANG']['tl_c4g_import_data']['bundles'] = array("Betroffene Bausteine","");
$GLOBALS['TL_LANG']['tl_c4g_import_data']['bundlesVersion'] = array("Baustein Versionen","");
$GLOBALS['TL_LANG']['tl_c4g_import_data']['description'] = array("Beschreibung","");
$GLOBALS['TL_LANG']['tl_c4g_import_data']['con4gisImport'] = array("Importtemplate","");
$GLOBALS['TL_LANG']['tl_c4g_import_data']['importVersion'] = array("Importierte Version","");
$GLOBALS['TL_LANG']['tl_c4g_import_data']['availableVersion'] = array("Verfügbare Version","");

/** OPERATIONS **/
$GLOBALS['TL_LANG']['tl_c4g_import_data']['importData'] = array("Daten importieren","");
$GLOBALS['TL_LANG']['tl_c4g_import_data']['updateData'] = array("Daten aktualisieren","");
$GLOBALS['TL_LANG']['tl_c4g_import_data']['releaseData'] = array("Datenimport lösen","");
$GLOBALS['TL_LANG']['tl_c4g_import_data']['deleteData'] = array("Daten löschen","");

/**
 * Reference
 */
$GLOBALS['TL_LANG']['tl_c4g_import_data']['type_demo'] = "Demodaten";
$GLOBALS['TL_LANG']['tl_c4g_import_data']['type_basedata'] = "Grunddaten";
$GLOBALS['TL_LANG']['tl_c4g_import_data']['source_io'] = "con4gis Supporter";
$GLOBALS['TL_LANG']['tl_c4g_import_data']['source_locale'] = "Lokal";

/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_c4g_import_data']['new'] = array('Neuer Import', 'Einen neuen Import erstellen');
$GLOBALS['TL_LANG']['tl_c4g_import_data']['importDialog'] = 'Möchten Sie die Daten jetzt importieren?';
$GLOBALS['TL_LANG']['tl_c4g_import_data']['deleteImportDialog'] = 'Möchten Sie den Import wirklich löschen?';
$GLOBALS['TL_LANG']['tl_c4g_import_data']['updateImportDialog'] = 'Bei einem Update werden sämtliche benutzerdefinierten Einstellungen überschrieben. Möchten Sie wirklich fortfahren?';
$GLOBALS['TL_LANG']['tl_c4g_import_data']['releaseImportDialog'] = 'Hiermit wird der Import gelöst und kann nicht mehr aktualisiert werden. Alle Daten bleiben erhalten. Möchten Sie wirklich fortfahren?';
$GLOBALS['TL_LANG']['tl_c4g_import_data']['con4gisIoImportData'] = 'Übersicht aller Importe';

/** INFOTEXT */
$GLOBALS['TL_LANG']['tl_c4g_import_data']['infotext'] = 'Hier kannst Du Grund- und Demodaten importieren bzw. aktualisieren. Einige Bausteine liefern Demodaten mit. Wenn Du <a href="https://con4gis.org/support"><b>con4gis Supporter*in</b></a> bist, dann stehen Dir über die Server weitere Grund- und Demodaten-Importe zur Verfügung.';
$GLOBALS['TL_LANG']['tl_c4g_import_data']['infotextNoKey'] = 'Du hast keinen con4gis Support Zugang hinterlegt. Damit Dir weitere Importe angezeigt werden benötigst Du einen. Diesen kannst Du unter <a href="https://con4gis.org/support"><b>con4gis.org</b></a> erstellen.';
$GLOBALS['TL_LANG']['tl_c4g_import_data']['importRunning'] = 'Der Import läuft bereits. Es können immer nur Daten eines Import zur selben Zeit importiert werden.';
$GLOBALS['TL_LANG']['tl_c4g_import_data']['olderImport'] = 'Älterer Importordner im Dateisystem. Bitte den Import manuell neu einspielen.';
$GLOBALS['TL_LANG']['tl_c4g_import_data']['errorDeleteImports'] = 'Fehler beim Löschen alter Importe.';
$GLOBALS['TL_LANG']['tl_c4g_import_data']['releasingError'] = 'Fehler beim lösen des Imports: falsche Import Id.';
$GLOBALS['TL_LANG']['tl_c4g_import_data']['importSuccessfull'] = 'Der Import wurde erfolgreich installiert.';
$GLOBALS['TL_LANG']['tl_c4g_import_data']['importError'] = 'Der Import konnte nicht installiert werden. Weitere Informationen im con4gis-Log';
$GLOBALS['TL_LANG']['tl_c4g_import_data']['deletedSuccessfull'] = 'Der Import wurde erfolgreich gelöscht.';
$GLOBALS['TL_LANG']['tl_c4g_import_data']['releasedSuccessfull'] = 'Der Import wurde erfolgreich gelöst. Ein anpassen ohne Datenverlust ist jetzt möglich.';
