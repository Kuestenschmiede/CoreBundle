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

namespace con4gis\CoreBundle\Classes\Hooks;

use con4gis\CoreBundle\Resources\contao\models\C4gSettingsModel;
use Contao\BackendUser;
use Contao\System;
use Contao\Database;

/**
 * Class con4gisNavigation
 * @package con4gis\CoreBundle\Classes\Hooks
 */
class con4gisNavigation extends System
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
        $this->import(BackendUser::class, 'User');

        if ($dispatcher !== null) {
            $this->dispatcher = $dispatcher;
        } else {
            $this->dispatcher = System::getContainer()->get('event_dispatcher');
        }
    }

    /**
     * @param $arrModules
     * @param $blnShowAll
     * @return mixed
     */
    public function activateNavigation($arrModules, $blnShowAll)
    {
        if (!Database::getInstance()->tableExists('tl_c4g_bricks')) {
            return $arrModules;
        }

        $result = Database::getInstance()
            ->prepare('SELECT brickkey, favorite FROM tl_c4g_bricks WHERE pid=?')
            ->execute($this->User->id)->fetchAllAssoc();

        if ($result) {
            $settings = C4gSettingsModel::findAll();
            $showBundleNames = false;
            if ($settings && $settings[0]) {
                $showBundleNames = $settings[0]->showBundleNames;
            }

            foreach ($result as $brick) {
                $brickkey = $brick['brickkey'];
                $favorite = $brick['favorite'];

                foreach ($arrModules['con4gis']['modules'] as $name => $module) {
                    if (isset($module['brick']) && $module['brick'] == $brickkey) {
                        $additionalClass = $arrModules['con4gis']['modules'][$name]['class'];
                        if ($showBundleNames) {
                            $arrModules['con4gis']['modules'][$name]['label'] = '[' . $brickkey . '] ' . $GLOBALS['TL_LANG']['MOD'][$name][0];
                        } else {
                            $arrModules['con4gis']['modules'][$name]['label'] = '॰ ' . $GLOBALS['TL_LANG']['MOD'][$name][0];
                        }
                        $arrModules['con4gis']['modules'][$name]['class'] = $favorite == '1' ? $additionalClass . ' c4g_visible_brick' : $additionalClass . ' c4g_invisible_brick';
                    }
                }
            }
        } else {
            //Initial invisible
            foreach ($arrModules['con4gis']['modules'] as $name => $module) {
                if ($name != 'c4g_bricks') {
                    $additionalClass = $arrModules['con4gis']['modules'][$name]['class'];
                    $arrModules['con4gis']['modules'][$name]['class'] = $additionalClass . ' c4g_invisible_brick';
                }
            }
        }

        //fallback to remove empty staging nav
        if (isset($arrModules['con4gis_stage']) && $arrModules['con4gis_stage'] && $arrModules['con4gis_stage']['modules'] && is_countable($arrModules['con4gis_stage']['modules']) && count($arrModules['con4gis_stage']['modules']) == 0) {
            unset($arrModules['con4gis_stage']);
        }

        return $arrModules;
    }
}
