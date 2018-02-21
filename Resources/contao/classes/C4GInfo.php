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
    	return parent::generate();
    }

	/**
     * Generate the module
     */
    protected function compile()
    {
        $packages = $this->getContainer()->getParameter('kernel.packages');
        $this->Template->packages = $packages;
    }

}