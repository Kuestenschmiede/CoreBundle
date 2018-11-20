<?php

/**
 * con4gis - the gis-kit
 *
 * @version   php 5
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2018
 * @link      https://www.kuestenschmiede.de
 */

namespace con4gis\CoreBundle\Resources\contao\classes;

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

    /**
     * @param $jsFile
     * @param string $location
     * @param string $key
     */
    public static function loadJavaScriptResource($jsFile, $location = self::JAVASCRIPT, $key = '') {
        switch ($location) {
            case self::JAVASCRIPT:
                if ($key === '') {
                    $GLOBALS[self::JAVASCRIPT][] = $jsFile;
                } else {
                    $GLOBALS[self::JAVASCRIPT][$key] = $jsFile;
                }
                break;
            case self::HEAD:
                if ($key === '') {
                    $GLOBALS[self::HEAD][] = '<script src="' . $jsFile . '" defer></script>' . "\n";
                } else {
                    $GLOBALS[self::HEAD][$key] = '<script src="' . $jsFile . '" defer></script>' . "\n";
                }
                break;
            case self::BODY:
                if ($key === '') {
                    $GLOBALS[self::BODY][] = '<script src="' . $jsFile . '" defer></script>' . "\n";
                } else {
                    $GLOBALS[self::BODY][$key] = '<script src="' . $jsFile . '" defer></script>' . "\n";
                }
                break;
            default:
                break;
        }
    }

    /**
     * @param $jsFile
     * @param string $key
     */
    public static function loadJavaScriptResourceModule($jsFile, $key = '') {
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
    public static function loadJavaScriptResourceTag($code, $location = self::HEAD, $key = '') {
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
    public static function loadCssResource($cssFile, $key = '') {
        if ($key === '') {
            $GLOBALS[self::CSS][] = $cssFile;
        } else {
            $GLOBALS[self::CSS][$key] = $cssFile;
        }
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

        $neededResources = array();

        switch ($module) {
            case 'maps':
                // Maps 3
                //
                $neededResources['clipboard'] = true;


                // check if jQuery needs to be loaded
                $jQueryLoaded = false;
                $scripts = $GLOBALS['TL_JAVASCRIPT'];
                if (is_array($scripts)) {
                    foreach ($scripts as $strScriptUrl) {
                        if (preg_match('/assets\/jquery\/core\/\d+\.\d+\.\d+\/jquery\.min\.js/i', $strScriptUrl)) {
                            $jQueryLoaded = true;
                            break;
                        }
                    }
                }

                if ($objPage->hasJQuery)
                {
                    $jQueryLoaded = true;
                }

                if ($GLOBALS['CON4GIS']['JQUERY-LOADED'])
                {
                    $jQueryLoaded = true;
                }

                $neededResources['jquery'] = !$jQueryLoaded;
                
                // Load magnific-popup.js for projects
                $neededResources['magnific-popup'] = $GLOBALS['con4gis']['projects']['installed'];

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
    public static function loadResources($resources=array())
    {
        if (!is_array($resources) || empty($resources)) {
            $allByDefault = true;
            $resources = array();
        } else {
            $allByDefault = false;
        }

        $resources = array_merge(array
        (
            'jquery' => $allByDefault,
            'magnific-popup' => $allByDefault,
            'clipboard' => $allByDefault,
            'jspdf' => $allByDefault,
        ),
        $resources);

        if ($resources['jquery']) {
            // load jQuery
            if (version_compare( VERSION, '3', '>=' ) &&
                is_array( $GLOBALS['TL_JAVASCRIPT'] ) &&
                (array_search( 'assets/jquery/js/jquery.min.js|static', $GLOBALS['TL_JAVASCRIPT'] ) !== false))
            {
                // jQuery is already loaded by Contao 3, don't load again!
            }
            else {
                self::loadJavaScriptRessource('c4g_jquery', 'assets/jquery/js/jquery.min.js|static', true);
            }
        }
        // TODO test, später richtig einbinden
        self::loadJavaScriptRessource('ajax-request', 'bundles/con4giscore/js/C4GAjaxRequest.js', true);
        if ($resources['magnific-popup']) {
            // load magnific-popup
            self::loadJavaScriptRessource('magnific-popup', 'bundles/con4giscore/vendor/magnific-popup/jquery.magnific-popup.min.js', true);
            self::loadCssRessource('magnific-popup', 'bundles/con4giscore/vendor/magnific-popup/magnific-popup.css');

        }
        if ($resources['clipboard']) {
            // load clipboard
            self::loadJavaScriptRessource('clipboard', 'bundles/con4giscore/vendor/clipboard.min.js', true);
        }
        if ($resources['jspdf']) {
            // load clipboard
            self::loadJavaScriptRessource('jspdf', 'bundles/con4giscore/vendor/jspdf/jspdf.min.js', true);
            //$GLOBALS['TL_JAVASCRIPT']['jspdf.plugin.from_html'] = 'bundles/con4giscore/vendor/jspdf/plugins/from_html.js|static';
        }

        return true;
    }

    /**
     * @param $key
     * @param $cssFile
     * @deprecated
     */
    public static function loadCssRessource($key, $cssFile) {
        self::loadCssResource($cssFile, $key);
    }

    /**
     * @param $key
     * @param $jsFile
     * @param bool $inHeader
     * @param bool $es6Module
     * @deprecated
     */
    public static function loadJavaScriptRessource($key, $jsFile, $inHeader = false, $es6Module = false) {
        if ($inHeader) {
            self::loadJavaScriptResource($jsFile, self::JAVASCRIPT, $key);
        } else {
            if ($es6Module) {
                self::loadJavaScriptResourceModule($jsFile, $key);
            } else {
                self::loadJavaScriptResource($jsFile, self::BODY, $key);
            }
        }

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
