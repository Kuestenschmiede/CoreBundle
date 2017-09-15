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
 * Class C4GInfo
 * @package c4g
 */
class C4GInfo extends \BackendModule
{
	protected $strTemplate = 'be_c4g_info';

	/**
     * Generate the module
     * @return string
     */
    public function generate()
    {
        $GLOBALS['TL_CSS'][] = 'bundles/con4giscore/css/be_c4g_info.css';

    	// check for actions (atm only "migrate")
//    	if (\Input::get('perf') != '') {
//    		if (\Input::get('perf') == 'migrate' && \Input::get('mod') != '') {
//        		$objCallback = new C4GMigration(\Input::get('mod'));
//        		return $objCallback->generate();
//    		} elseif (\Input::get('perf') == 'apicheck' && \Input::get('mod') != '') {
//                $objCallback = new C4GApiCheck(\Input::get('mod'));
//                return $objCallback->generate();
//            } elseif (\Input::get('perf') == 'membergroupsync') {
//                $objCallback = new C4GMembergroupSync();
//                return $objCallback->generate();
//            }
//    	}

    	return parent::generate();
    }

	/**
     * Generate the module
     */
    protected function compile()
    {
        // nothing to do here
    }

}