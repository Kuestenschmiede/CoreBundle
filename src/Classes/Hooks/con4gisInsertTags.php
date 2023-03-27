<?php

/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2022, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */

namespace con4gis\CoreBundle\Classes\Hooks;

use Contao\System;

/**
 * Class con4gisInsertTag
 * @package con4gis\CoreBundle\Classes\Hooks
 */
class con4gisInsertTags extends \System
{
    /**
     * Instanz des Symfony EventDispatchers
     * @var null
     */
    protected $dispatcher = null;

    /**
     * con4gisInsertTag constructor.
     * @param null $dispatcher
     */
    public function __construct($dispatcher = null)
    {
        if ($dispatcher !== null) {
            $this->dispatcher = $dispatcher;
        } else {
            $this->dispatcher = System::getContainer()->get('event_dispatcher');
        }
    }

    private function checkVersionTag($versionTag)
    {
        if (trim($versionTag)[0] !== 'v') {
            return 'v' . $versionTag;
        }

        return $versionTag;
    }

    /**
     * @param $strTag
     * @return bool
     */
    public function replaceTag($strTag)
    {
//        $packages = System::getContainer()->getParameter('kernel.packages');
        if ($packages && $strTag) {
            $arrSplit = explode('::', $strTag);

            if ($arrSplit && (($arrSplit[0] == 'con4gis')) && isset($arrSplit[1])) {
                $fieldName = $arrSplit[1];
                switch ($fieldName) {
                    case 'version': return $packages['con4gis/maps'];
                    default:
                        if ($packages['con4gis/' . $fieldName]) {
                            return $this->checkVersionTag($packages['con4gis/' . $fieldName]);
                        }

                        return 'not installed';
                }
            }
        }

        return false;
    }
}
