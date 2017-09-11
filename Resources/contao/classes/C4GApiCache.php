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

namespace con4gis\CoreBundle\Resources\contao\classes;

class C4GApiCache extends \Frontend
{

    public static function getCacheKey($strApiEndpoint, $arrFragments)
    {


        $strCacheKey =  $strApiEndpoint . "#" . serialize($arrFragments);
        $strChecksum = md5($strCacheKey);

        return $strChecksum;
    }

    public static function hasCacheData($strChecksum)
    {


        if (file_exists(TL_ROOT . '/' . self::getCacheFile($strChecksum)))
        {



            return true;
        }

        return false;
    }

    private static function getCacheFile($strChecksum)
    {
        return 'system/cache/con4gis/' . $strChecksum . '.json';
    }

    public static function getCacheData($strApiEndpoint, $arrFragments)
    {

        $strChecksum = self::getCacheKey($strApiEndpoint, $arrFragments);

        if (self::hasCacheData($strChecksum))
        {

            $objFile = new \File(self::getCacheFile($strChecksum), true);

            $strCacheContent = $objFile->getContent();
            return $strCacheContent;

        }

        return false;

    }

    private static function saveCacheData($strChecksum, $strContent)
    {


        \File::putContent(self::getCacheFile($strChecksum), $strContent);


    }

    public static function putCacheData($strApiEndpoint, $arrFragments, $strContent)
    {
        $strChecksum = self::getCacheKey($strApiEndpoint, $arrFragments);

        self::saveCacheData($strChecksum, $strContent);

    }


}