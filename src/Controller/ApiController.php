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

namespace con4gis\CoreBundle\Controller;

use con4gis\CoreBundle\Classes\C4GApiCache;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\System;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class ApiController extends AbstractController
{
    public function __construct(ContaoFramework $framework)
    {
        $framework->initialize();
    }

    /**
     * @var string
     */
    private $_sApiUrl = 'con4gis/CoreBundle/src/Resources/contao/api/index.php';
    private static $_sApiBundleVersion = '1.1.1';

    public function runAction($_url_fragment)
    {

        // Get path
        $arrFragments = $this->getFramgentsFromRoutingParam($_url_fragment);

        // Extract api endpoint
        $strApiEndpoint = array_shift($arrFragments);

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
            if ($strResponse = C4GApiCache::getCacheData($strApiEndpoint, $arrFragments))
            {
                $blnOutputFromCache = true;
            }
        }

        if (!$blnOutputFromCache)
        {

            // Create the api endpoint handler
            $objHandler = new $GLOBALS['TL_API'][$strApiEndpoint]();

            $strResponse = $objHandler->generate($arrFragments);

            if ($blnUseCache)
            {
                // write data into cache
                C4GApiCache::putCacheData($strApiEndpoint, $arrFragments, $strResponse);
            }

        }

        // this is needed for the forum, because it must not send a json response
        if (is_array($strResponse) && count($strResponse) > 1) {
            $response = new Response($strResponse['data'], 200);
        } else if ($strResponse) {
            $response = new JsonResponse(json_decode($strResponse));
        }

        if ($response && ($response instanceof JsonResponse && \Input::get('callback')))
        {
            $response->setCallback(\Input::get('callback'));
        }

        if (!$response) {
            $response = [];
        }

        return $response;
    }

    public function deliverAction(Request $request)
    {
        $fileName = $request->query->get('file');
        $uuid = $request->query->get('u');
        $fileHash = $request->query->get('c');
        $aInfo     = pathinfo($fileName);
        $fileName = str_replace($aInfo['basename'], "", $fileName) . $uuid;
        $rootDir = System::getContainer()->getParameter('kernel.project_dir');
        if (file_exists($rootDir . '/' . $fileName)) {
            $response = new BinaryFileResponse($rootDir . '/' . $fileName);
            return $response;
        }
    }

    protected function getFramgentsFromRoutingParam($strUrlFrament)
    {
        // return the fragments
        return explode('/', $strUrlFrament);
    }


    /**
     * Split the request into fragments and find the api resource
     */
    protected function getFragmentsFromUrl($request)
    {

        // Return null on empty request path
        if ($request == '') {
            return null;
        }

        echo \Environment::get('request');

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

    /**
     * @return string
     */
    public static function getSApiBundleVersion()
    {
        return self::$_sApiBundleVersion;
    }
}