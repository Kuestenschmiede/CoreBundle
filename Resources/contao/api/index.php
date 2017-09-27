<?php

/**
 * con4gis - the gis-kit
 *
 * @version   php 5
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2017.
 * @link      https://www.kuestenschmiede.de
 */

    // index.php is a frontend script
    define('TL_MODE', 'FE');
    // Start the session so we can access known request tokens
    @session_start();

    // Allow to bypass the token check
    if (!isset($_POST['REQUEST_TOKEN'])) {
        /**
         *
         */
        define('BYPASS_TOKEN_CHECK', true);
    }

    $initialize = $_SERVER["DOCUMENT_ROOT"].'/system/initialize.php';
    if (!file_exists($initialize)) {
        $initialize = '../../../../system/initialize.php';
    }

    // Initialize the system
    require_once($initialize);

    /**
     * Class Api4Gis
     */
    class Api4Gis extends Frontend
    {


        /**
         * @var string
         */
        private $_sApiUrl = 'src/con4gis/CoreBundle/Resources/contao/api/index.php';


        /**
         * Initialize the object
         */
        public function __construct()
        {

            // Load user object before calling the parent constructor
            $this->import('FrontendUser', 'User');
            parent::__construct();

            // Check whether a user is logged in
            define('BE_USER_LOGGED_IN', $this->getLoginStatus('BE_USER_AUTH'));
            define('FE_USER_LOGGED_IN', $this->getLoginStatus('FE_USER_AUTH'));
        }


        /**
         * Run the controller
         */
        public function run()
        {

            // Set default headers for api
            header('Content-Type: application/json');

            // Maintenance mode
            if ($GLOBALS['TL_CONFIG']['maintenanceMode'] && !BE_USER_LOGGED_IN) {
                header('HTTP/1.1 503 Service Unavailable');
                exit;
            }

            // Get path
            $arrFragments = $this->getFragmentsFromUrl();

            // Stop on empty path
            if (empty($arrFragments)) {
                header('HTTP/1.1 400 Bad Request');
                exit;
            }

            // Extract api endpoint
            $strApiEndpoint = array_shift($arrFragments);

            // check if its a test-call
            if ($strApiEndpoint == 'c4g_apicheck_ajax') {
                if (!$arrFragments[0] || array_key_exists($arrFragments[0], $GLOBALS['TL_API'])) {
                    return true;
                } else {
                    header('HTTP/1.1 501 Not Implemented');
                    exit;
                }
            }

            // Stop if no matching endpoint is found
            if (!array_key_exists($strApiEndpoint, $GLOBALS['TL_API'])) {
                header('HTTP/1.1 400 Bad Request');
                exit;
            }

            $blnUseCache = false;
            $blnOutputFromCache = false;

            if (!\Config::get('debugMode') && (\Config::get('cacheMode') == 'both' || \Config::get('cacheMode') == 'server') && in_array($strApiEndpoint, $GLOBALS['CON4GIS']['USE_CACHE']['SERVICES']))
            {
                $blnUseCache = true;
            }

            if (is_array($GLOBALS['CON4GIS']['USE_CACHE']['PARAMS']))
            {
                foreach ($GLOBALS['CON4GIS']['USE_CACHE']['PARAMS'] as $key=>$arrValues)
                {
                    if (\Input::get($key) && in_array(\Input::get($key), $arrValues))
                    {
                        $blnUseCache = true;
                    }
                }
            }


            if ($blnUseCache)
            {
                // check for cached data
                if ($strResponse = \con4gis\CoreBundle\Resources\contao\classes\C4GApiCache::getCacheData($strApiEndpoint, $arrFragments))
                {
                    $blnOutputFromCache = true;
                }
            }

            // Generate the result

            // check for jsonp request
            if (\Input::get('callback'))
            {
                echo \Input::get('callback') . '(';
            }

            if ($blnOutputFromCache)
            {
                echo $strResponse;
            }
            else
            {

                // Create the api endpoint handler
                $objHandler = new $GLOBALS['TL_API'][$strApiEndpoint]();

                $strResponse = $objHandler->generate($arrFragments);
                if (is_array($strResponse)) {
                    header("Content-Type: " . $strResponse['type']);
                    $strResponse = $strResponse['data'];
                }

                if ($blnUseCache)
                {
                    // write data into cache
                    \con4gis\CoreBundle\Resources\contao\classes\C4GApiCache::putCacheData($strApiEndpoint, $arrFragments, $strResponse);
                }

                echo $strResponse;

            }


            // check for jsonp request
            if (\Input::get('callback'))
            {
                echo ');';
            }

        }


        /**
         * Split the request into fragments and find the api resource
         */
        protected function getFragmentsFromUrl()
        {

            // Return null on empty request path
            if (\Environment::get('request') == '') {
                return null;
            }

            $test = \Environment::get('request');

            // Get the request string without the index.php fragment
            if (\Environment::get('request') == $this->_sApiUrl . 'index.php') {
                $strRequest = '';
            } else {
                list($strRequest) = explode('?', str_replace($this->_sApiUrl . 'index.php/', '', \Environment::get('request')), 2);
            }

            // Remove api fragment
            if (substr($strRequest, 0, strlen($this->_sApiUrl)) == $this->_sApiUrl) {
                $strRequest = substr($strRequest, strlen($this->_sApiUrl));
            }

            // URL decode here
            $strRequest = rawurldecode($strRequest);
            $strRequest = substr($strRequest,1);

            // return the fragments
            return explode('/', $strRequest);
        }
    }

    /**
     * Instantiate controller
     */
    $objApi = new Api4Gis();
    $objApi->run();