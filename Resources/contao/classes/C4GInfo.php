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