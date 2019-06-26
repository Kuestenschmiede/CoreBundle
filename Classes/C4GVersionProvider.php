<?php
/**
 * This file is part of con4gis,
 * the gis-kit for Contao CMS.
 *
 * @package   	con4gis
 * @version        6
 * @author  	    con4gis contributors (see "authors.txt")
 * @license 	    LGPL-3.0-or-later
 * @copyright 	Küstenschmiede GmbH Software & Design
 * @link              https://www.con4gis.org
 *
 */

namespace con4gis\CoreBundle\Classes;


use Contao\Request;

/**
 * Class C4GVersionProvider
 * Provides methods to determine the latest version of a bundle from packagist.org.
 * @package con4gis\CoreBundle\Classes
 */
class C4GVersionProvider
{
   
    const REQUEST_URL = "https://repo.packagist.org/p/[vendor]/[package].json";
    
    /**
     * @param string $package   The package in vendor/package structure.
     * @return string
     */
    public function getLatestVersion(string $package)
    {
        $arrPackage = explode("/", $package);
        $url = str_replace('[vendor]', $arrPackage[0], self::REQUEST_URL);
        $url = str_replace('[package]', $arrPackage[1], $url);
        $request = new Request();
        $request->send($url);
        return $this->parseLatestVersion($request->response, $package);
    }
    
    private function parseLatestVersion(string $json, string $package)
    {
        $arrJson = json_decode($json, true);
        $intError = json_last_error();
        if ($intError === JSON_ERROR_NONE) {
            $candidates = array_keys($arrJson['packages'][$package]);
            $currentLatestVersion = "";
            foreach ($candidates as $candidate) {
                // ignore dev branches
                if (strpos($candidate, 'dev') !== false) {
                    continue;
                }
                $version = $this->getComparableVersionString($candidate);
                if ($this->compareVersions($currentLatestVersion, $version)) {
                    $currentLatestVersion = $version;
                }
            }
            return $currentLatestVersion;
        } else {
            // TODO handle json error
            return "";
        }
    }
    
    /**
     * Normalizes the version strings, e.g. removes the v-prefixes.
     * @param $version
     * @return string
     */
    private function getComparableVersionString($version)
    {
        if (strpos($version, 'v')) {
            $version = str_replace('v', '', $version);
        }
        return $version;
    }
    
    /**
     * Checks if the $comparable version string is higher than the $toCompare version string.
     * Returns true if it's newer, false otherwise.
     * @param $toCompare
     * @param $comparable
     * @return bool
     */
    private function compareVersions($toCompare, $comparable)
    {
        $toCompareFragments = explode('.', $toCompare);
        $comparableFragments = explode('.', $comparable);
        // compare major version
        if ($toCompareFragments[0] > $comparableFragments[0]) {
            return false;
        } elseif ($toCompareFragments[0] < $comparableFragments[0]) {
            return true;
        }
        // compare minor version
        if ($toCompareFragments[1] > $comparableFragments[1]) {
            return false;
        } elseif ($toCompareFragments[1] < $comparableFragments[1]) {
            return true;
        }
        // compare bugfix version
        if ($toCompareFragments[2] > $comparableFragments[2]) {
            return false;
        } elseif ($toCompareFragments[2] < $comparableFragments[2]) {
            return true;
        }
        return false;
    }
}