<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @author con4gis contributors (see "authors.md")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2026, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */

/** FIELDS **/
$GLOBALS['TL_LANG']['tl_c4g_settings']['showBundleNames'] = [
    'Zeige Bausteinnamen in der Navigation',
    'Bei Aktivierung werden zusätzlich zu den Modulnamen, die Bundlenamen (Bausteine) in der Contao Navigation angezeigt, dass ist hilfrich wenn mehrere Bausteine im Dashboard aktiviert wurden.'
];
$GLOBALS['TL_LANG']['tl_c4g_settings']['c4g_uitheme_css_select'] = array(
    'jQuery UI ThemeRoller CSS Theme',
    'Wählen Sie hier eines der Standard UI-Themes aus. Sollten Sie im nächsten Schritt eine eigene Datei auswählen, wird die geladen.'
);
$GLOBALS['TL_LANG']['tl_c4g_settings']['c4g_appearance_themeroller_css'] = array(
    'jQuery UI ThemeRoller CSS Datei',
    'Optional: wählen Sie eine, mit dem jQuery UI ThemeRoller erstellte, CSS-Datei aus, um den Stil der Frontendmodule einfach anzupassen.'
);
$GLOBALS['TL_LANG']['tl_c4g_settings']['uploadAllowedImageTypes'] = array(
    'Erlaubte Bilddatei-Typen',
    'Kommagetrennte Liste von Bildformaten, die mit con4gis hochgeladen werden dürfen, z.B. image/png.'
);
$GLOBALS['TL_LANG']['tl_c4g_settings']['uploadAllowedImageWidth'] = array(
    'Maximale Bildbreite',
    'Maximale Breite für mit con4gis hochgeladene Bilder.'
);
$GLOBALS['TL_LANG']['tl_c4g_settings']['uploadAllowedImageHeight'] = array(
    'Maximale Bildhöhe',
    'Maximale Höhe für mit con4gis hochgeladene Bilder.'
);
$GLOBALS['TL_LANG']['tl_c4g_settings']['uploadPathImages'] = array(
    'Uploadverzeichnis für Bilder',
    'Verzeichnis, in das Bilder mit con4gis hochgeladen werden.'
);
$GLOBALS['TL_LANG']['tl_c4g_settings']['uploadAllowedDocumentTypes'] = array(
    'Erlaubte Dokument-Typen',
    'Kommagetrennte Liste von Dokumentformaten, die mit con4gis hochgeladen werden dürfen, z.B. application/pdf.'
);
$GLOBALS['TL_LANG']['tl_c4g_settings']['uploadPathDocuments'] = array(
    'Uploadverzeichnis für Dokumente',
    'Verzeichnis, in das Dokumente mit con4gis hochgeladen werden.'
);
$GLOBALS['TL_LANG']['tl_c4g_settings']['uploadAllowedGenericTypes'] = array(
    'Erlaubte Typen sonstiger Dateien',
    'Kommagetrennte Liste von Formaten, die mit con4gis außerhalb von Bild- und Dokumentkontext hochgeladen werden dürfen, z.B. application/zip.'
);
$GLOBALS['TL_LANG']['tl_c4g_settings']['uploadPathGeneric'] = array(
    'Uploadverzeichnis für sonstige Dateien',
    'Verzeichnis, in das sonstige Dateien mit con4gis hochgeladen werden.'
);
$GLOBALS['TL_LANG']['tl_c4g_settings']['uploadMaxFileSize'] = array(
    'Maximale Dateigröße',
    'Maximale Dateigröße für Uploads aller Art.'
);
$GLOBALS['TL_LANG']['tl_c4g_settings']['con4gisIoUrl'] = ['URL für con4gis Support', 'Gib hier die URL für die con4gis Support ein. Die URL wird Dir in Deinem Account angezeigt (account.con4gis.support).'];
$GLOBALS['TL_LANG']['tl_c4g_settings']['con4gisIoKey'] = ['API-Schlüssel für con4gis Support', 'Gib hier einen Deiner Schlüssel für con4gis Support ein (<a href="https://con4gis.org/support" target="_blank" rel="noopener">kostenfreien Schlüssel generieren</a>).'];
$GLOBALS['TL_LANG']['tl_c4g_settings']['disableJQueryLoading'] = ['jQuery nicht laden', 'Wenn Du diese Checkbox aktivierst, wird jQuery nicht geladen. Nur nützlich wenn Du jQuery aus einer anderen Quelle lädst.'];

/** INFO **/
$GLOBALS['TL_LANG']['tl_c4g_settings']['infotext'] =
    'Alle hier aufgeführten Einstellungsmöglichkeiten können von verschiedenen con4gis Bausteinen verwendet werden. Eventuell vorhandene Zusatzeinstellungen in den Frontendmodulen können diese ggf. überschreiben.';

/** LEGENDS **/
$GLOBALS['TL_LANG']['tl_c4g_settings']['global_legend'] = "Globale Einstellungen";
$GLOBALS['TL_LANG']['tl_c4g_settings']['layout_legend'] = "jQuery UI Einstellungen (forum/groups/projects)";
$GLOBALS['TL_LANG']['tl_c4g_settings']['upload_legend'] = "Uploadeinstellungen (forum/projects)";
$GLOBALS['TL_LANG']['tl_c4g_settings']['misc_legend'] = "Sonstige Einstellungen";
$GLOBALS['TL_LANG']['tl_c4g_settings']['con4gisIoLegend'] = 'con4gis Kartendienste';
$GLOBALS['TL_LANG']['tl_c4g_settings']['expert_legend'] = "Experten-Einstellungen";

/** Field References */
$GLOBALS['TL_LANG']['tl_c4g_settings']['c4g_references']['base']      = 'base';
$GLOBALS['TL_LANG']['tl_c4g_settings']['c4g_references']['black-tie'] = 'black-tie';
$GLOBALS['TL_LANG']['tl_c4g_settings']['c4g_references']['blitzer']   = 'blitzer';
$GLOBALS['TL_LANG']['tl_c4g_settings']['c4g_references']['cupertino'] = 'cupertino';
$GLOBALS['TL_LANG']['tl_c4g_settings']['c4g_references']['dark-hive'] = 'dark-hive';
$GLOBALS['TL_LANG']['tl_c4g_settings']['c4g_references']['dot-luv']   = 'dot-luv';
$GLOBALS['TL_LANG']['tl_c4g_settings']['c4g_references']['eggplant']  = 'eggplant';
$GLOBALS['TL_LANG']['tl_c4g_settings']['c4g_references']['excite-bike']   = 'excite-bike';
$GLOBALS['TL_LANG']['tl_c4g_settings']['c4g_references']['flick']         = 'flick';
$GLOBALS['TL_LANG']['tl_c4g_settings']['c4g_references']['hot-sneaks']    = 'hot-sneaks';
$GLOBALS['TL_LANG']['tl_c4g_settings']['c4g_references']['humanity']      = 'humanity';
$GLOBALS['TL_LANG']['tl_c4g_settings']['c4g_references']['le-frog']       = 'le-frog';
$GLOBALS['TL_LANG']['tl_c4g_settings']['c4g_references']['mint-choc']     = 'mint-choc';
$GLOBALS['TL_LANG']['tl_c4g_settings']['c4g_references']['overcast']      = 'overcast';
$GLOBALS['TL_LANG']['tl_c4g_settings']['c4g_references']['pepper-grinder'] = 'pepper-grinder';
$GLOBALS['TL_LANG']['tl_c4g_settings']['c4g_references']['redmond']       = 'redmond';
$GLOBALS['TL_LANG']['tl_c4g_settings']['c4g_references']['smoothness']    = 'smoothness';
$GLOBALS['TL_LANG']['tl_c4g_settings']['c4g_references']['south-street']  = 'south-street';
$GLOBALS['TL_LANG']['tl_c4g_settings']['c4g_references']['start']         = 'start';
$GLOBALS['TL_LANG']['tl_c4g_settings']['c4g_references']['sunny']         = 'sunny';
$GLOBALS['TL_LANG']['tl_c4g_settings']['c4g_references']['swanky-purse']  = 'swanky-purse';
$GLOBALS['TL_LANG']['tl_c4g_settings']['c4g_references']['trontastic']    = 'trontastic';
$GLOBALS['TL_LANG']['tl_c4g_settings']['c4g_references']['ui-darkness']   = 'ui-darkness';
$GLOBALS['TL_LANG']['tl_c4g_settings']['c4g_references']['ui-lightness']  = 'ui-lightness';
$GLOBALS['TL_LANG']['tl_c4g_settings']['c4g_references']['vader']         = 'vader';

/** OPERATIONS **/
$GLOBALS['TL_LANG']['tl_c4g_settings']['new'] = array("Einstellung hinzufügen","");
$GLOBALS['TL_LANG']['tl_c4g_settings']['edit'] = array("Einstellung bearbeiten","");
$GLOBALS['TL_LANG']['tl_c4g_settings']['copy'] = array("Einstellung kopieren","");
$GLOBALS['TL_LANG']['tl_c4g_settings']['delete'] = array("Einstellung löschen","");
$GLOBALS['TL_LANG']['tl_c4g_settings']['show'] = array("Einstellung anzeigen","");
