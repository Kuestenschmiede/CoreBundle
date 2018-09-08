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

use Contao\System;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Contao\FrontendUser;


class C4GApiCache
{
    /**
     * @var FilesystemAdapter
     */
    protected $cacheInstance;

    /**
     * @var C4GApiCache
     */
    protected static $instance = null;

    public static function getInstance() {
        if (!static::$instance) {
            static::$instance = new self();
        }
        return static::$instance;
    }

    /**
     * C4GApiCache constructor.
     */
    protected function __construct()
    {
        $container = System::getContainer();
        $this->cacheInstance = new FilesystemAdapter(
            $namespace = 'con4gis',
            $defaultLifetime = 0,
            $directory = $container->getParameter('kernel.cache_dir')
        );
    }

    public function clearCache() {
        $this->cacheInstance->clear();
    }

    public function getCacheKey($strApiEndpoint, $arrFragments)
    {
        $frontendIndex = new \FrontendIndex();

        if (FE_USER_LOGGED_IN) {
            $arrFragments['userId'] = FrontendUser::getInstance();
        }

        $strCacheKey =  $strApiEndpoint . "#" . serialize($arrFragments);
        $strChecksum = md5($strCacheKey);

        return $strChecksum;
    }

    public function hasCacheData($cacheChecksum)
    {
        return $this->cacheInstance->hasItem($cacheChecksum);
    }

    private function getCacheFile($strChecksum)
    {
        return 'system/cache/con4gis/' . $strChecksum . '.json';
    }

    public function getCacheData($strApiEndpoint, $arrFragments)
    {
        $strChecksum = $this->getCacheKey($strApiEndpoint, $arrFragments);
        if ($this->hasCacheData($strChecksum)) {
            return $this->cacheInstance->getItem($strChecksum)->get();
        }
        return false;
    }

    private function saveCacheData($strChecksum, $strContent)
    {
        $cacheData = $this->cacheInstance->getItem($strChecksum);
        $cacheData->set($strContent);
        return $this->cacheInstance->save($cacheData);
    }

    public function putCacheData($strApiEndpoint, $arrFragments, $strContent)
    {
        $strChecksum = $this->getCacheKey($strApiEndpoint, $arrFragments);
        $this->saveCacheData($strChecksum, $strContent);
    }


}