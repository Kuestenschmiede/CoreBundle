<?php

/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 10
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2025, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */

namespace con4gis\CoreBundle\Classes;

use con4gis\CoreBundle\Resources\contao\models\C4gSettingsModel;
use Contao\LayoutModel;
use Contao\System;

/**
 * Class ResourceLoader
 * @package c4g\Core
 */
class ResourceLoader
{
    const JAVASCRIPT = 'TL_JAVASCRIPT';
    const CSS = 'TL_CSS';
    const HEAD = 'TL_HEAD';
    const BODY = 'TL_BODY';
    const JQ_UI = 'c4g_jquery_ui';

    private static $loadedFiles = [];

    /**
     * @param $jsFile
     * @param string $location
     * @param string $key
     */
    public static function loadJavaScriptResource($jsFile, $location = self::JAVASCRIPT, $key = '')
    {
        $cacheKey = $jsFile . '_' . $location . '_' . $key;
        if (isset(self::$loadedFiles[$cacheKey])) {
            return;
        }
        self::$loadedFiles[$cacheKey] = true;

        $jsFile = C4GUtils::startsWith($jsFile, '/') ? $jsFile : '/' . $jsFile;
        $jsFileWithoutSlash = ltrim($jsFile, '/');

        $projectDir = System::getContainer()->getParameter('kernel.project_dir');
        $webDir = $projectDir . '/public';
        if (!file_exists($webDir)) {
            $webDir = $projectDir . '/web';
        }

        $timeStamp = file_exists($webDir . $jsFile) ? filemtime($webDir . $jsFile) : 0;

        $jsFileWithTimestamp = $timeStamp ? $jsFile . '?v=' . $timeStamp : $jsFile;

        switch ($location) {
            case self::JAVASCRIPT:
                if ($key === '') {
                    $GLOBALS[self::JAVASCRIPT][] = $jsFileWithoutSlash;
                } else {
                    $GLOBALS[self::JAVASCRIPT][$key] = $jsFileWithoutSlash;
                }
                break;

            case self::HEAD:
            case self::BODY:
                $scriptTag = '<script src="' . $jsFileWithTimestamp . '" defer></script>' . "\n";
                if ($key === '') {
                    $GLOBALS[$location][] = $scriptTag;
                } else {
                    $GLOBALS[$location][$key] = $scriptTag;
                }
                break;
        }
    }


    /**
     * @param $jsFile
     * @param string $key
     */
    public static function loadJavaScriptResourceModule($jsFile, $key = '')
    {
        if ($key === '') {
            $GLOBALS[self::BODY][] = '<script src="' . $jsFile . '" type="module"></script>' . "\n";
        } else {
            $GLOBALS[self::BODY][$key] = '<script src="' . $jsFile . '" type="module"></script>' . "\n";
        }
    }

    /**
     * @param $code
     * @param string $location
     * @param string $key
     */
    public static function loadJavaScriptResourceTag($code, $location = self::HEAD, $key = '')
    {
        switch ($location) {
            case self::HEAD:
                if ($key === '') {
                    $GLOBALS[self::HEAD][] = "<script>$code</script>\n";
                } else {
                    $GLOBALS[self::HEAD][$key] = "<script>$code</script>\n";
                }

                break;
            case self::BODY:
                if ($key === '') {
                    $GLOBALS[self::BODY][] = "<script>$code</script>\n";
                } else {
                    $GLOBALS[self::BODY][$key] = "<script>$code</script>\n";
                }

                break;
            default:
                break;
        }
    }

    /**
     * @param $cssFile
     * @param string $key
     */
    public static function loadCssResource($cssFile, $key = '')
    {
        $projectDir = System::getContainer()->getParameter('kernel.project_dir');
        $webDir = $projectDir . '/public';
        // check for older directory name
        if (!file_exists($webDir)) {
            $webDir = $projectDir . '/web';
        }
        if (!C4GUtils::startsWith($cssFile, '/')) {
            //$cssFile = '/' . $cssFile;
        } else {
            $cssFile = substr($cssFile, 1);
        }
        if (file_exists($webDir . '/'. $cssFile)) {
            $cssFile .= '|' . filemtime($webDir . '/'. $cssFile);
        }
        if ($key === '') {
            $GLOBALS[self::CSS][] = $cssFile;
        } else {
            $GLOBALS[self::CSS][$key] = $cssFile;
        }
    }

    /**
     * @param $styles
     * @param string $location
     * @param string $key
     */
    public static function loadCssResourceTag($styles, $location = self::HEAD, $key = '')
    {
        switch ($location) {
            case self::HEAD:
                if ($key === '') {
                    $GLOBALS[self::HEAD][] = "<style type=\"text/css\">$styles</style>";
                } else {
                    $GLOBALS[self::HEAD][$key] = "<style type=\"text/css\">$styles</style>";
                }

                break;
            case self::BODY:
                if ($key === '') {
                    $GLOBALS[self::BODY][] = "<style type=\"text/css\">$styles</style>";
                } else {
                    $GLOBALS[self::BODY][$key] = "<style type=\"text/css\">$styles</style>";
                }

                break;
            default:
                break;
        }
    }

    /**
     * There is no HTML solution to load CSS deferred. This will actually add a JS block that will load the css when
     *  the page is loaded. Be careful with this because it can make the page look terrible before the CSS is actually
     *  loaded.
     * @param $cssFile
     */
    public static function loadCssResourceDeferred($cssFile)
    {
        self::loadJavaScriptResourceTag(
            "window.addEventListener('load', function() {" .
                "var link = document.createElement('link');" .
                "link.rel = 'stylesheet';" .
                "link.href = '$cssFile';" .
                "link.type = 'text/css';" .
                "var defer = document.getElementsByTagName('link')[0];" .
                "if  (typeof defer !== 'undefined') { defer.parentNode.insertBefore(link, defer); }" .
                'else { document.head.appendChild(link); }' .
            '});'
        );
    }

    /**
     * @param $location
     * @param $key
     * @return bool
     */
    public static function isResourceLoaded($location, $key)
    {
        return isset($GLOBALS[$location][$key]);
    }

    /**
     * @param $theme
     */
    public static function loadJqueryUiTheme($theme)
    {
        self::loadCssResourceDeferred("bundles/con4giscore/vendor/jQuery/ui-themes/themes/$theme/jquery-ui.css");
    }

    /**
     * @return bool
     */
    public static function isJqueryUiThemeLoaded()
    {
        return self::isResourceLoaded(self::CSS, self::JQ_UI);
    }

    /**
     * Function loadResourcesForModule
     *
     * Loads core-resources needed by the given module
     * @deprecated
     */
    public static function loadResourcesForModule($module)
    {
        global $objPage;

        //workaround hasJQuery param with contao >= 4.5
        if ($objPage && property_exists($objPage::class, 'layout') && $objPage->layout) {
            $objLayout = LayoutModel::findByPk($objPage->layout);
            $objPage->hasJQuery = $objLayout->addJQuery;
        }

        $neededResources = [];

        switch ($module) {
            case 'maps':
                $neededResources['clipboard'] = true;

                // check if jQuery needs to be loaded
                $jQueryLoaded = false;
                $scripts = $GLOBALS && key_exists('TL_JavaSCRIPT', $GLOBALS) ? $GLOBALS['TL_JAVASCRIPT'] : [];
                if (is_array($scripts)) {
                    foreach ($scripts as $strScriptUrl) {
                        if (preg_match('/assets\/jquery\/core\/\d+\.\d+\.\d+\/jquery\.min\.js/i', $strScriptUrl)) {
                            $jQueryLoaded = true;

                            break;
                        }
                    }
                }

                if ($objPage && $objPage->hasJQuery) {
                    $jQueryLoaded = true;
                }

                if (isset($GLOBALS['CON4GIS']['JQUERY-LOADED']) && $GLOBALS['CON4GIS']['JQUERY-LOADED']) {
                    $jQueryLoaded = true;
                }

                $neededResources['jquery'] = !$jQueryLoaded;
                $settings = C4gSettingsModel::findSettings();
                $dontLoadJQuery = isset($settings->disableJQueryLoading) && $settings->disableJQueryLoading;
                if ($dontLoadJQuery) {
                    $neededResources['jquery'] = false;
                }

                // Load magnific-popup.js for projects
                $neededResources['magnific-popup'] = false;//C4GVersionProvider::isInstalled('con4gis/projects');

                //ToDo switch for pdf export
                $neededResources['jspdf'] = false;

                break;

            default:
                return false;
        }

        return self::loadResources($neededResources);
    }

    /**
     * Function loadResources
     *
     * Loads the requested resources
     * @deprecated
     */
    public static function loadResources($resources = [])
    {
        if (!is_array($resources) || empty($resources)) {
            $allByDefault = true;
            $resources = [];
        } else {
            $allByDefault = false;
        }

        $resources = array_merge([
            'jquery' => $allByDefault,
            'magnific-popup' => $allByDefault,
            'clipboard' => $allByDefault,
            'jspdf' => $allByDefault,
        ],
        $resources);

        global $objPage;

        if ($resources['jquery']) {
            // load jQuery

            //workaround hasJQuery param with contao >= 4.5
            if ($objPage && $objPage->layout) {
                $objLayout = LayoutModel::findByPk($objPage->layout);
                $objPage->hasJQuery = $objLayout->addJQuery;
            }

            if ($objPage && $objPage->hasJQuery) {
                // jQuery is already loaded by Contao, don't load again!
            } else {
                ResourceLoader::loadJavaScriptResource('assets/jquery/js/jquery.min.js', self::HEAD, 'c4g_jquery');
            }
        }
        ResourceLoader::loadJavaScriptResource('/bundles/con4giscore/dist/js/C4GAjaxRequest.js|async|static', self::JAVASCRIPT, 'ajax-request');

        if ($resources['clipboard']) {
            // load clipboard
            ResourceLoader::loadJavaScriptResource('/bundles/con4giscore/vendor/clipboard.min.js', self::BODY, 'clipboard');
        }

        return true;
    }

    /**
     * @param $key
     * @param $jsFile
     * @deprecated
     */
    public static function loadJavaScriptModule($key, $jsFile)
    {
        self::loadJavaScriptResourceModule($jsFile, $key);
    }

    /**
     * @param $key
     * @param $jsFile
     * @deprecated
     */
    public static function loadJavaScriptDeferred($key, $jsFile)
    {
        self::loadJavaScriptResource($jsFile, self::BODY, $key);
    }

    public static function removeJavaScriptRessource($key)
    {
        unset($GLOBALS['TL_BODY'][$key]);
    }
}
