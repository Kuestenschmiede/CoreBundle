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

use con4gis\CoreBundle\Classes\C4GVersionProvider;

/**
 * Class C4GInfo
 * @package c4g
 */
class C4GInfo extends \BackendModule
{
	protected $strTemplate = 'be_c4g_info';
    
    /**
     * @var C4GVersionProvider
     */
	private $versionProvider = null;

	/**
     * Generate the module
     * @return string
     */
    public function generate()
    {
        $this->versionProvider = new C4GVersionProvider();
        $GLOBALS['TL_CSS'][] = 'bundles/con4giscore/css/be_c4g_info.css';
    	return parent::generate();
    }

	/**
     * Generate the module
     */
    protected function compile()
    {
        $packages = $this->getContainer()->getParameter('kernel.packages');
        $versions = $this->getLatestVersions($packages);
        $this->Template->packages = $packages;
        $this->Template->versions = $versions;
    }
    
    private function getLatestVersions($installedPackages)
    {
        $packages = [
            'con4gis/core',
            'con4gis/documents',
            'con4gis/editor',
            'con4gis/export',
            'con4gis/forum',
            'con4gis/groups',
            'con4gis/import',
            'con4gis/maps',
            'con4gis/routing',
            'con4gis/projects',
            'con4gis/pwa',
            'con4gis/queue',
            'con4gis/tracking',
        ];
        // only check installed packages
        $packages = array_intersect(array_keys($installedPackages), $packages);
        
        $versions = [];
        foreach ($packages as $package) {
            $versions[$package] = $this->versionProvider->getLatestVersion($package);
        }
        return $versions;
    }

}