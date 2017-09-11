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


/**
 * Provide methods to run automated jobs.
 *
 */
class C4GAutomator extends \System
{

    /**
     * Make the constuctor public
     */
    public function __construct()
    {
        parent::__construct();
        self::log('Purged the con4gis cache', __METHOD__, TL_CRON);
    }

    /**
     * Purge the con4gis cache
     */
    public static function purgeApiCache()
    {
        // Purge the folder
        $objFolder = new \Folder('system/cache/con4gis');
        $objFolder->purge();

        // Add a log entry

    }

}