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

use Contao\FrontendUser;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\DependencyInjection\ContainerInterface;

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

    public static function getInstance(ContainerInterface $container)
    {
        if (!static::$instance) {
            static::$instance = new self($container);
        }

        return static::$instance;
    }

    /**
     * C4GApiCache constructor.
     */
    protected function __construct(ContainerInterface $container)
    {
        $this->cacheInstance = new FilesystemAdapter(
            $namespace = 'con4gis',
            $defaultLifetime = 0,
            $directory = $container->getParameter('kernel.cache_dir')
        );
    }

    public function clearCache()
    {
        $this->cacheInstance->clear();
    }

    public function getCacheKey($strApiEndpoint, $arrFragments)
    {
        $frontendIndex = new \Contao\FrontendIndex();
        $hasFrontendUser = \Contao\System::getContainer()->get('contao.security.token_checker')->hasFrontendUser();
        if ($hasFrontendUser) {
            $arrFragments['userId'] = FrontendUser::getInstance()->id;
        }

        $strCacheKey = $strApiEndpoint . '#' . serialize($arrFragments);
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
