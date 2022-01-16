<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2022, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */

/** FIELDS **/
$GLOBALS['TL_LANG']['tl_c4g_settings']['showBundleNames'] = [
    'Show brick name in navigation',
    'When activated, in addition to the module names, the bundle names are displayed in the Contao navigation, which is helpful if several modules are activated in the dashboard.'
];
$GLOBALS['TL_LANG']['tl_c4g_settings']['c4g_uitheme_css_select'] = [
    'jQuery UI ThemeRoller CSS theme',
    'Select a standart UI-Theme.'
];
$GLOBALS['TL_LANG']['tl_c4g_settings']['c4g_appearance_themeroller_css'] = [
    'jQuery UI ThemeRoller CSS file',
    'Optionally: select the CSS file you created with the jQuery UI ThemeRoller.'
];
$GLOBALS['TL_LANG']['tl_c4g_settings']['uploadAllowedImageTypes'] = [
    'Permitted image formats',
    'Comma-seperated list of formats, which are permitted to be uploaded over con4gis, e.g. image/png.'
];
$GLOBALS['TL_LANG']['tl_c4g_settings']['uploadAllowedImageWidth'] = [
    'Maximum image width',
    'Maximum width for images uploaded over con4gis'
];
$GLOBALS['TL_LANG']['tl_c4g_settings']['uploadAllowedImageHeight'] = [
    'Maximum image height',
    'Maximum height for images uploaded over con4gis'
];
$GLOBALS['TL_LANG']['tl_c4g_settings']['uploadPathImages'] = [
    'Upload path for images',
    'Directory to store uploaded images.'
];
$GLOBALS['TL_LANG']['tl_c4g_settings']['uploadAllowedDocumentTypes'] = [
    'Permitted document formats',
    'Comma-seperated list of formats, which are permitted to be uploaded over con4gis, e.g. application/pdf.'
];
$GLOBALS['TL_LANG']['tl_c4g_settings']['uploadPathDocuments'] = [
    'Upload path for documents',
    'Directory to store uploaded documents.'
];
$GLOBALS['TL_LANG']['tl_c4g_settings']['uploadAllowedGenericTypes'] = [
    'Other permitted formats',
    'Comma-seperated list of formats of other files, which are permitted to be uploaded over con4gis, e.g. application/zip.'
];
$GLOBALS['TL_LANG']['tl_c4g_settings']['uploadPathGeneric'] = [
        'Upload path for other files',
        'Directory to store other uploaded files.'
];
$GLOBALS['TL_LANG']['tl_c4g_settings']['uploadMaxFileSize'] = [
    'Maximum file size',
    'Maximum file size for other files.'
];
$GLOBALS['TL_LANG']['tl_c4g_settings']['con4gisIoUrl'] = ['URL for con4gis.io', 'Enter the URL for the con4gis.io map services here. The URL will be displayed in your IO Account.'];
$GLOBALS['TL_LANG']['tl_c4g_settings']['con4gisIoKey'] = ['API-Key for con4gis.io', 'Enter one of your keys for the con4gis.io map services here (<a href="https://con4gis.io" target="_blank" rel="noopener">generate free key</a>).'];
$GLOBALS['TL_LANG']['tl_c4g_settings']['disableJQueryLoading'] = ['Do not load jQuery', 'If you select this checkbox, jQuery will not be loaded. Useful if you already load jQuery from another source.'];

/** INFO **/
$GLOBALS['TL_LANG']['tl_c4g_settings']['infotext'] =
    'This settings will used by several con4gis bricks.';

/** LEGENDS **/
$GLOBALS['TL_LANG']['tl_c4g_settings']['global_legend'] = "Global settings";
$GLOBALS['TL_LANG']['tl_c4g_settings']['layout_legend'] = "jQuery UI settings (forum/groups/projects)";
$GLOBALS['TL_LANG']['tl_c4g_settings']['upload_legend'] = "Upload settings (forum/projects)";
$GLOBALS['TL_LANG']['tl_c4g_settings']['misc_legend'] = "Miscellaneous settings";
$GLOBALS['TL_LANG']['tl_c4g_settings']['con4gisIoLegend'] = 'con4gis.io map services';
$GLOBALS['TL_LANG']['tl_c4g_settings']['expert_legend'] = "Expert settings";

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
$GLOBALS['TL_LANG']['tl_c4g_settings']['new'] = ["add settings",""];
$GLOBALS['TL_LANG']['tl_c4g_settings']['edit'] = ["edit settings",""];
$GLOBALS['TL_LANG']['tl_c4g_settings']['copy'] = ["copy settings",""];
$GLOBALS['TL_LANG']['tl_c4g_settings']['delete'] = ["delete settings",""];
$GLOBALS['TL_LANG']['tl_c4g_settings']['show'] = ["show settings",""];
