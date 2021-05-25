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

/** FIELDS **/
$GLOBALS['TL_LANG']['tl_c4g_bricks']['brickkey'] = ['Brick', ''];
$GLOBALS['TL_LANG']['tl_c4g_bricks']['brickname'] = ['Brick', ''];
$GLOBALS['TL_LANG']['tl_c4g_bricks']['repository'] = ['Repository', ''];
$GLOBALS['TL_LANG']['tl_c4g_bricks']['description'] = ['Description', ''];
$GLOBALS['TL_LANG']['tl_c4g_bricks']['installedVersion'] = ['Installed', ''];
$GLOBALS['TL_LANG']['tl_c4g_bricks']['latestVersion'] = ['Latest', ''];
$GLOBALS['TL_LANG']['tl_c4g_bricks']['icon'] = ['Icon', ''];
$GLOBALS['TL_LANG']['tl_c4g_bricks']['showBundle'] = ['Show bundle', ''];

/** LEGENDS **/
$GLOBALS['TL_LANG']['tl_c4g_bricks']['brick_legend'] = "Brick informations";

$GLOBALS['TL_LANG']['tl_c4g_bricks']['globalSettings'] = ["Settings", "Settings that apply to several bricks."];
$GLOBALS['TL_LANG']['tl_c4g_bricks']['switchInstalled'] = ["Installed bricks", "Use this button to switch between only installed and all bricks."];
$GLOBALS['TL_LANG']['tl_c4g_bricks']['switchInstalledAll'] = ["All bricks", "Über Use this button to switch between only installed and all bricks."];
$GLOBALS['TL_LANG']['tl_c4g_bricks']['reloadVersions'] = ["Reload versions","The versions are reloaded by the Packagist."];
$GLOBALS['TL_LANG']['tl_c4g_bricks']['importData'] = ["Import data","'Import basic and demo data."];
$GLOBALS['TL_LANG']['tl_c4g_bricks']['serverLogs'] = ["Server Logs","Logs from the con4gis bricks are displayed."];
$GLOBALS['TL_LANG']['tl_c4g_bricks']['con4gisOrg'] = ["con4gis.org","All about the GIS kit."];
$GLOBALS['TL_LANG']['tl_c4g_bricks']['con4gisIO'] = ["con4gis.io","Map services for Contao."];
$GLOBALS['TL_LANG']['tl_c4g_bricks']['firstButton'] = "Open settings";
$GLOBALS['TL_LANG']['tl_c4g_bricks']['secondButton'] = "Open settings";
$GLOBALS['TL_LANG']['tl_c4g_bricks']['thirdButton'] = "Open settings";
$GLOBALS['TL_LANG']['tl_c4g_bricks']['fourthButton'] = "Open settings";
$GLOBALS['TL_LANG']['tl_c4g_bricks']['fifthButton'] = "Open settings";
$GLOBALS['TL_LANG']['tl_c4g_bricks']['sixthButton'] = "Open settings";
$GLOBALS['TL_LANG']['tl_c4g_bricks']['seventhButton'] = "Open settings";
$GLOBALS['TL_LANG']['tl_c4g_bricks']['showDocs'] = "Displays in the con4gis Docs";
$GLOBALS['TL_LANG']['tl_c4g_bricks']['showPackagist'] = "Display on Packagist";
$GLOBALS['TL_LANG']['tl_c4g_bricks']['showGitHub'] = "Display on GitHub";
$GLOBALS['TL_LANG']['tl_c4g_bricks']['favorite'] = "Favorites are displayed directly in the Contao navigation.";

/** LABELS */
$GLOBALS['TL_LANG']['tl_c4g_bricks']['lastLoading'] = 'last updated on ';

/** OPERATIONS **/
$GLOBALS['TL_LANG']['tl_c4g_bricks']['new'] = array("Add settings","");
$GLOBALS['TL_LANG']['tl_c4g_bricks']['edit'] = array("Edit settings","");
$GLOBALS['TL_LANG']['tl_c4g_bricks']['copy'] = array("Copy settings","");
$GLOBALS['TL_LANG']['tl_c4g_bricks']['delete'] = array("Delete settings","");
$GLOBALS['TL_LANG']['tl_c4g_bricks']['show'] = array("Show settings","");

/** DESCRIPTIONS */
$GLOBALS['TL_LANG']['tl_c4g_bricks']['core'] = 'con4gis core functions';
$GLOBALS['TL_LANG']['tl_c4g_bricks']['documents'] = 'PDF Generator';
$GLOBALS['TL_LANG']['tl_c4g_bricks']['editor'] = 'Frontend mapeditor (abandoned)';
$GLOBALS['TL_LANG']['tl_c4g_bricks']['export'] = 'CSV Exporter';
$GLOBALS['TL_LANG']['tl_c4g_bricks']['firefighter'] = 'Operation Management (firefighter)';
$GLOBALS['TL_LANG']['tl_c4g_bricks']['forum'] = 'Discussions forum, FAQ and ticketsystem';
$GLOBALS['TL_LANG']['tl_c4g_bricks']['framework'] = 'React Framework for front-end modules (NEW)';
$GLOBALS['TL_LANG']['tl_c4g_bricks']['groups'] = 'Frontend member management';
$GLOBALS['TL_LANG']['tl_c4g_bricks']['import'] = 'CSV Importer';
$GLOBALS['TL_LANG']['tl_c4g_bricks']['ldap'] = 'LDAP Interface (frontend/backend)';
$GLOBALS['TL_LANG']['tl_c4g_bricks']['maps'] = 'The map brick';
$GLOBALS['TL_LANG']['tl_c4g_bricks']['data'] = 'Maintain map and list data';
$GLOBALS['TL_LANG']['tl_c4g_bricks']['reservation'] = 'Reservation forms (tables, rooms, ...)';
$GLOBALS['TL_LANG']['tl_c4g_bricks']['projects'] = 'Framework for frontend modules';
$GLOBALS['TL_LANG']['tl_c4g_bricks']['pwa'] = 'Progressive Web App';
$GLOBALS['TL_LANG']['tl_c4g_bricks']['queue'] = 'Batch processing of processes (queue)';
$GLOBALS['TL_LANG']['tl_c4g_bricks']['tracking'] = 'Position tracking (abandoned)';
$GLOBALS['TL_LANG']['tl_c4g_bricks']['visualization'] = 'Chartgenerator';
$GLOBALS['TL_LANG']['tl_c4g_bricks']['io-travel-costs'] = 'Travel cost calculator (requires con4gis.io)';

/** INFOTEXT */
$GLOBALS['TL_LANG']['tl_c4g_bricks']['infotext'] = 'Welcome to the con4gis control center. Here you can use the bricks and see what else con4gis has to offer.'.
    ' A special feature is the new data import: with the help of a con4gis.io access you can easily import demo and basic data.';