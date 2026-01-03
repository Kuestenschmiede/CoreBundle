<?php

/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @author con4gis contributors (see "authors.md")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2026, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */
use Contao\ArrayUtil;
use Contao\System;
use Symfony\Component\HttpFoundation\Request;

// API-Registration
$GLOBALS['TL_API'] = array();
$GLOBALS['TL_API']['fileUpload']  = 'con4gis\CoreBundle\Classes\C4GFileUpload';
$GLOBALS['TL_API']['imageUpload'] = 'con4gis\CoreBundle\Classes\C4GImageUpload';
$GLOBALS['TL_API']['deliver']     = 'con4gis\CoreBundle\Classes\C4GDeliverFileApi';

if (System::getContainer()->get('contao.routing.scope_matcher')->isBackendRequest(System::getContainer()->get('request_stack')->getCurrentRequest() ?? Request::create(''))) {
    $GLOBALS['TL_CSS'][] = '/bundles/con4giscore/dist/css/con4gis.min.css';
}

$GLOBALS['con4gis']['stringClass'] = '\Contao\StringUtil';

/**
 * Backend Modules
 */
ArrayUtil::arrayInsert($GLOBALS['BE_MOD'], array_search('content', array_keys($GLOBALS['BE_MOD'])) + 1, array
(
    'con4gis' => [
        'c4g_bricks'   => ['tables' => ['tl_c4g_bricks']],
        'c4g_settings' => ['tables' => ['tl_c4g_settings']],
        'c4g_io_data'  => ['tables' => ['tl_c4g_import_data']],
        'c4g_log'      => ['tables' => ['tl_c4g_log']]
    ]
));

ArrayUtil::arrayInsert($GLOBALS['BE_MOD'], array_search('con4gis', array_keys($GLOBALS['BE_MOD'])) + 1, array
(
    'con4gis_stage' => []
));

/**
 * Content Elements
 */
ArrayUtil::arrayInsert($GLOBALS['TL_CTE']['con4gis'], 2, array
(
    'c4g_activationpage' => 'con4gis\CoreBundle\Resources\contao\modules\ContentC4gActivationpage'
));
$GLOBALS['TL_MODELS']['tl_c4g_activationkey'] = 'con4gis\CoreBundle\Resources\contao\models\C4gActivationkeyModel';

$GLOBALS['TL_MODELS']['tl_c4g_settings'] = 'con4gis\CoreBundle\Resources\contao\models\C4gSettingsModel';

/**
 * Con4Gis Caching
 *
 * caching is not auto-enabled. To register a service to be cached, insert it in the GLOBALS-Array
 * eg: $GLOBALS['CON4GIS']['USE_CACHE']['SERVICES'][] = "layerService"; => layerService requests are cached
 *
 * it is also possible to enable caching while existence of defined get parameters and values
 * eg: $GLOBALS['CON4GIS']['USE_CACHE']['PARAMS']['method'] = array('getLive'); => request with method=getLive will be cached
 *
 */
if (!array_key_exists('CON4GIS', $GLOBALS) || !!array_key_exists('USE_CACHE', $GLOBALS['CON4GIS']))
{
    $GLOBALS['CON4GIS']['USE_CACHE'] = array();
    $GLOBALS['CON4GIS']['USE_CACHE']['SERVICES'] = array();
    $GLOBALS['CON4GIS']['USE_CACHE']['PARAMS'] = array();
}

/**
 * replace con4gis insertTags
 */
$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = array('\con4gis\CoreBundle\Classes\Hooks\con4gisInsertTags', 'replaceTag');

/** USE getUserNavigation Hook */
$GLOBALS['TL_HOOKS']['getUserNavigation'][] = array('con4gis\CoreBundle\Classes\Hooks\con4gisNavigation','activateNavigation');

$GLOBALS['TL_PURGE']['folders']['con4gis_log'] = [
    'callback' => ['\con4gis\CoreBundle\Classes\Contao\Callback\MaintenanceCallback', 'purgeLog'],
    'affected' => []
];