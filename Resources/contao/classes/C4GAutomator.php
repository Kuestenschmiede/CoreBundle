<?php

/*
 * This file is part of con4gis,
 * the gis-kit for Contao CMS.
 *
 * @package    con4gis
 * @version    6
 * @author     con4gis contributors (see "authors.txt")
 * @license    LGPL-3.0-or-later
 * @copyright  Küstenschmiede GmbH Software & Design
 * @link       https://www.con4gis.org
 */

namespace con4gis\CoreBundle\Resources\contao\classes;

use con4gis\CoreBundle\Resources\contao\classes\C4GApiCache;
use Contao\System;

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

        C4GApiCache::getInstance(System::getContainer())->clearCache();
        // Add a log entry
    }

}