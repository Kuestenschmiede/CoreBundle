<?php
/**
 * con4gis - the gis-kit
 *
 * @version   php 5
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2016.
 * @link      https://www.kuestenschmiede.de
 */

namespace con4gis\CoreBundle\Controller;

use con4gis\CoreBundle\Resources\contao\classes\C4GApiCache;
use con4gis\CoreBundle\Resources\contao\classes\C4GUtils;
use Contao\Database;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

class BaseController extends Controller
{
    protected static $useCache = false;

    protected static $outputFromCache = false;

    protected $responseData = "";

    protected $cacheInstance = null;

    /**
     * @var EntityManager
     */
    protected $entityManager = null;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher = null;

    /**
     * BaseController constructor.
     */
    public function __construct()
    {
        $this->cacheInstance = C4GApiCache::getInstance();
    }


    protected function initialize($withEntityManager=true)
    {
        if ($withEntityManager) {
            $this->entityManager = $this->container->get('doctrine.orm.entity_manager');
        }
        $this->eventDispatcher = $this->container->get('event_dispatcher');
    }

    protected function initializeContao()
    {
        $this->container->get('contao.framework')->initialize();
    }

    protected function getCacheRequest(Request $request)
    {
        return $request->getHost() . $request->getRequestUri();
    }

    protected function getCacheFragments(Request $request)
    {

        return ['request' => $request->query->all()];
    }

    protected function checkForCacheSettings($configParam)
    {
        $this->initializeContao();
        $cacheSettings = Database::getInstance()->execute("SELECT * FROM tl_c4g_settings LIMIT 1")->fetchAllAssoc();
        $cacheSettings = $cacheSettings[0]['caching'];
        self::$useCache = (is_array(deserialize($cacheSettings)) && in_array($configParam, deserialize($cacheSettings)));
    }

    protected function checkAndStoreCachedData(Request $request)
    {
        if (($returnData = $this->cacheInstance->getCacheData($this->getCacheRequest($request), $this->getCacheFragments($request)))  !== false)
        {
            self::$outputFromCache = true;
            $this->responseData = $returnData;
        }
    }

    protected function storeDataInCache(Request $request)
    {
        $this->cacheInstance->putCacheData($this->getCacheRequest($request), $this->getCacheFragments($request), $this->responseData);
    }

    /**
     * Returns whether a frontend user is currently logged in.
     */
    protected function checkFeUser()
    {
        return C4GUtils::checkFrontendUserLogin();
    }
}