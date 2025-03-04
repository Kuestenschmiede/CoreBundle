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
namespace con4gis\CoreBundle\Classes;

use Composer\MetadataMinifier\MetadataMinifier;
use Contao\System;
use Symfony\Component\HttpClient\HttpClient;
use Composer\InstalledVersions;
/**
 * Class C4GVersionProvider
 * Provides methods to determine the latest version of a bundle from packagist.org.
 * @package con4gis\CoreBundle\Classes
 */
class C4GVersionProvider
{
    const REQUEST_URL = 'https://repo.packagist.org/p2/[vendor]/[package].json';

    /**
     * @param string $package
     * @return bool
     */
    public static function isInstalled(string $package)
    {
        if (System::getContainer()->hasParameter('kernel.packages')) {
            $installedPackages = System::getContainer()->getParameter('kernel.packages');
            return ($package && array_key_exists($package, $installedPackages));
        }
        else {
            return InstalledVersions::isInstalled($package);
        }


    }

    /**
     * @param string $package   The package in vendor/package structure.
     * @return string
     */
    public function getLatestVersion(string $package)
    {
        $arrPackage = explode('/', $package);
        $url = str_replace('[vendor]', $arrPackage[0], self::REQUEST_URL);
        $url = str_replace('[package]', $arrPackage[1], $url);
        $client = HttpClient::create();
        $response = $client->request('GET', $url)->getContent();
        if (!$response) {
            $response = '';
        }

        return $this->parseLatestVersion($response, $package, $arrPackage[0], $arrPackage[1]);
    }

    private function parseLatestVersion(string $json, string $package, string $vendor, string $packageName)
    {
        $arrJson = json_decode($json, true);
        $intError = json_last_error();
        if ($intError === JSON_ERROR_NONE) {
            $candidates = MetadataMinifier::expand($arrJson['packages'][$package]);
            $currentLatestVersion = '';
            foreach ($candidates as $candidate) {
                // ignore dev branches
                if (strpos($candidate['version'], 'dev') !== false) {
                    continue;
                }
                $version = $this->getComparableVersionString($candidate['version']);
                if ($this->compareVersions($currentLatestVersion, $version)) {
                    $currentLatestVersion = $version;
                }
            }

            return $currentLatestVersion;
        }
        // TODO handle json error
        return '';
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

        $toCompareFragments[0] = strpos($toCompareFragments[0], 'v') === false ? $toCompareFragments[0] : substr($toCompareFragments[0], 1);
        $comparableFragments[0] = strpos($comparableFragments[0], 'v') === false ? $comparableFragments[0] : substr($comparableFragments[0], 1);

        for ($i = 0; $i < 3; $i++) {
            $toValue = intval($toCompareFragments[$i]);
            $cmpValue = intval($comparableFragments[$i]);

            if ($toValue > $cmpValue) {
                return false;
            } elseif ($toValue < $cmpValue) {
                return true;
            }
        }

        // ToDo compare release candidates
        return false;
    }
}
