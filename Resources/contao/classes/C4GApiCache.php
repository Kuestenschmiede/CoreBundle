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

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Contao\FrontendUser;


class C4GApiCache extends \Frontend
{

    protected static $hasInstance = false;

    /**
     * @var FilesystemAdapter
     */
    protected static $instance;

    public static function getInstance() {

        if (static::$hasInstance) {
            return static::$instance;
        } else {

            $container = \System::getContainer();

            self::$instance = new FilesystemAdapter(
                $namespace = 'con4gis',
                $defaultLifetime = 0,
                $directory = $container->getParameter('kernel.cache_dir')
            );
        }

    }

    public static function clearCache() {
        if (!static::$hasInstance)
        {
            self::getInstance();
        }

        self::$instance->clear();
    }

    public static function getCacheKey($strApiEndpoint, $arrFragments)
    {

        if (!static::$hasInstance)
        {
            self::getInstance();
        }

        $frontendIndex = new \FrontendIndex();

        if (FE_USER_LOGGED_IN) {
            $frontendIndex->import('FrontendUser', 'User');
            $arrFragments['userId'] = $frontendIndex->User->id;
        }

        $strCacheKey =  $strApiEndpoint . "#" . serialize($arrFragments);
        $strChecksum = md5($strCacheKey);

        return $strChecksum;
    }

    public static function hasCacheData($cacheChecksum)
    {

        if (!static::$hasInstance)
        {
            self::getInstance();
        }

        return self::$instance->hasItem($cacheChecksum);

    }

    private static function getCacheFile($strChecksum)
    {
        if (!static::$hasInstance)
        {
            self::getInstance();
        }

        return 'system/cache/con4gis/' . $strChecksum . '.json';
    }

    public static function getCacheData($strApiEndpoint, $arrFragments)
    {

        if (!static::$hasInstance)
        {
            self::getInstance();
        }

        $strChecksum = self::getCacheKey($strApiEndpoint, $arrFragments);

        if (self::hasCacheData($strChecksum))
        {

            return self::$instance->getItem($strChecksum)->get();

        }

        return false;

    }

    private static function saveCacheData($strChecksum, $strContent)
    {
        if (!static::$hasInstance)
        {
            self::getInstance();
        }

        $cacheData = self::$instance->getItem($strChecksum);
        $cacheData->set($strContent);

        return self::$instance->save($cacheData);


    }

    public static function putCacheData($strApiEndpoint, $arrFragments, $strContent)
    {
        if (!static::$hasInstance)
        {
            self::getInstance();
        }

        $strChecksum = self::getCacheKey($strApiEndpoint, $arrFragments);

        self::saveCacheData($strChecksum, $strContent);

    }


}