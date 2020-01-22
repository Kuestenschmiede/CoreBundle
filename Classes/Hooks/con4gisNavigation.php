<?php

/*
 * This file is part of con4gis,
 * the gis-kit for Contao CMS.
 *
 * @package    con4gis
 * @version    7
 * @author     con4gis contributors (see "authors.txt")
 * @license    LGPL-3.0-or-later
 * @copyright  Küstenschmiede GmbH Software & Design
 * @link       https://www.con4gis.org
 */

namespace con4gis\CoreBundle\Classes\Hooks;

use Contao\System;

/**
 * Class con4gisNavigation
 * @package con4gis\CoreBundle\Classes\Hooks
 */
class con4gisNavigation extends \System
{
    /**
     * Instanz des Symfony EventDispatchers
     * @var null
     */
    protected $dispatcher = null;

    /**
     * con4gisNavigation constructor.
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


    public function activateNavigation($arrModules, $blnShowAll)
    {
        if (!\Database::getInstance()->tableExists('tl_c4g_bricks')) {
            return $arrModules;
        }

        $result = \Database::getInstance()
            ->prepare('SELECT brickkey, favorite FROM tl_c4g_bricks')
            ->execute()->fetchAllAssoc();

        if ($result) {
            foreach ($result as $brick) {
                $brickkey = $brick['brickkey'];
                $favorite = $brick['favorite'];

                foreach ($arrModules['con4gis']['modules'] as $name=>$module) {
                    if ($module['brick'] && $module['brick'] == $brickkey) {
                        $additionalClass = $arrModules['con4gis']['modules'][$name]['class'];
                        $arrModules['con4gis']['modules'][$name]['class'] = $favorite == '1' ? $additionalClass.' c4g_visible_brick' : $additionalClass.' c4g_invisible_brick';
                    }
                }
            }
        }

        return $arrModules;
    }

}
