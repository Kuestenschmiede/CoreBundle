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

namespace con4gis\CoreBundle\Classes\Helper;

/**
 * Class AutoloadHelper
 * @package con4gis\CoreBundle\Classes\Helper
 */
class AutoloadHelper
{
    /**
     * Ruft das Laden der Templates (*.html5, *.xhtml und *.html) für den
     * übergebenen Pfad (inkl. Unterordnern) auf.
     * @param        $strPath
     * @param string $strRgex
     */
    public static function loadTemplates($strPath, $strRgex = '/^.+\.[x]*html[5]*$/i')
    {
        $folder = (substr_count($strPath, 'system/modules')) ? '/templates' : '/src/Resources/contao/templates';
        $strPath = self::makePath($strPath, $folder);
        $objFiles = self::getFiles($strPath, $strRgex);

        if ($objFiles) {
            self::registerTemplates($objFiles);
        }
    }

    /**
     * Registriert die gefundenen Templates bei Contao.
     * @param $objFiles
     */
    protected static function registerTemplates($objFiles)
    {
        $rootDir = System::getContainer()->getParameter('kernel.project_dir');
        foreach ($objFiles as $varFile) {
            $strFile = (is_array($varFile)) ? array_shift($varFile) : $varFile;
            $objFile = pathinfo($strFile);
            \Contao\TemplateLoader::addFile($objFile['filename'], str_replace($rootDir . '/', '', $objFile['dirname']));
        }
    }

    /**
     * Prüft den übergebenen Pfad und ergänzt die fehlenden Bestandteile.
     * @param $strPath
     * @param $folder
     * @return mixed|string
     */
    protected static function makePath($strPath, $folder)
    {
        $rootDir = System::getContainer()->getParameter('kernel.project_dir');
        $strPath = (!substr_count($strPath, $rootDir)) ? $rootDir . '/' . $strPath : $strPath;
        $strPath .= "$folder/";
        $strPath = str_replace('//', '/', $strPath);

        return $strPath;
    }

    /**
     * Sucht die Templates im übergebenen Pfad.
     * @param        $strPath
     * @param string $strRgex
     * @return null|\RegexIterator
     */
    protected static function getFiles($strPath, $strRgex = '/^.+\.php$/i')
    {
        if (is_dir($strPath)) {
            $objDirectory = new \RecursiveDirectoryIterator($strPath, \FilesystemIterator::SKIP_DOTS);
            $objIterator = new \RecursiveIteratorIterator($objDirectory);
            $objFiles = new \RegexIterator($objIterator, $strRgex, \RecursiveRegexIterator::GET_MATCH);

            return $objFiles;
        }

        return null;
    }
}
