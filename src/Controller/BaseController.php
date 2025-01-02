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

namespace con4gis\CoreBundle\Controller;

use con4gis\CoreBundle\Classes\C4GApiCache;
use con4gis\CoreBundle\Classes\C4GUtils;
use Contao\Database;
use Contao\StringUtil;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

class BaseController extends AbstractController
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
    public function __construct(ContainerInterface $container)
    {
        $this->cacheInstance = C4GApiCache::getInstance($container);
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
        $cacheSettings = isset($cacheSettings[0]['caching']) ? $cacheSettings[0]['caching'] : '';
        self::$useCache = (is_array(StringUtil::deserialize($cacheSettings)) && in_array($configParam, StringUtil::deserialize($cacheSettings)));
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
        return C4GUtils::isFrontendUserLoggedIn();
    }
}