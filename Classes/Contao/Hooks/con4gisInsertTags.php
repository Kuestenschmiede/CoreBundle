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

namespace con4gis\CoreBundle\Classes\Contao\Hooks;

use Contao\System;


/**
 * Class con4gisInsertTag
 * @package con4gis\CoreBundle\Classes\Contao\Hooks
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

    private function checkVersionTag($versionTag) {
        if (trim($versionTag)[0] !== 'v') {
            return 'v'.$versionTag;
        } else {
            return $versionTag;
        }
    }

    /**
     * @param $strTag
     * @return bool
     */
    public function replaceTag($strTag)
    {
        $packages = System::getContainer()->getParameter('kernel.packages');
        if ($packages && $strTag) {
            $arrSplit = explode('::', $strTag);

            if ($arrSplit && (($arrSplit[0] == 'con4gis')) && isset($arrSplit[1])) {
                $fieldName = $arrSplit[1];
                switch($fieldName) {
                    case 'version': return $GLOBALS['con4gis']['version'];
                    case 'core':
                        if ($packages['con4gis/core']) {
                            return $this->checkVersionTag($packages['con4gis/core']);
                        } else {
                            return 'not installed';
                        }
                    case 'maps':
                        if ($packages['con4gis/maps']) {
                            return $this->checkVersionTag($packages['con4gis/maps']);
                        } else {
                            return 'not installed';
                        }
                    case 'forum':
                        if ($packages['con4gis/forum']) {
                            return $this->checkVersionTag($packages['con4gis/forum']);
                        } else {
                            return 'not installed';
                        }
                    case 'import':
                        if ($packages['con4gis/import']) {
                            return $this->checkVersionTag($packages['con4gis/import']);
                        } else {
                            return 'not installed';
                        }
                    case 'export':
                        if ($packages['con4gis/export']) {
                            return $this->checkVersionTag($packages['con4gis/export']);
                        } else {
                            return 'not installed';
                        }
                    case 'documents':
                        if ($packages['con4gis/documents']) {
                            return $this->checkVersionTag($packages['con4gis/documents']);
                        } else {
                            return 'not installed';
                        }
                    case 'queue':
                        if ($packages['con4gis/queue']) {
                            return $this->checkVersionTag($packages['con4gis/queue']);
                        } else {
                            return 'not installed';
                        }
                    case 'pwa':
                        if ($packages['con4gis/pwa']) {
                            return $this->checkVersionTag($packages['con4gis/pwa']);
                        } else {
                            return 'not installed';
                        }
                    case 'groups':
                        if ($packages['con4gis/groups']) {
                            return $this->checkVersionTag($packages['con4gis/groups']);
                        } else {
                            return 'not installed';
                        }
                    case 'projects':
                        if ($packages['con4gis/projects']) {
                            return $this->checkVersionTag($packages['con4gis/projects']);
                        } else {
                            return 'not installed';
                        }
                    case 'routing':
                        if ($packages['con4gis/routing']) {
                            return $this->checkVersionTag($packages['con4gis/routing']);
                        } else {
                            return 'not installed';
                        }
                    case 'editor':
                        if ($packages['con4gis/editor']) {
                            return $this->checkVersionTag($packages['con4gis/editor']);
                        } else {
                            return 'not installed';
                        }
                    case 'tracking':
                        if ($packages['con4gis/tracking']) {
                            return $this->checkVersionTag($packages['con4gis/tracking']);
                        } else {
                            return 'not installed';
                        }
                    case 'tracking-android':
                        if ($packages['con4gis/tracking-android']) {
                            return $this->checkVersionTag($packages['con4gis/tracking-android']);
                        } else {
                            return 'not installed';
                        }
                    default:
                        return 'unknown';
                }

            }
        }

        return false;
    }
}
