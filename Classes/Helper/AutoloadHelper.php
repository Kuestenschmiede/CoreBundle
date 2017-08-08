<?php
/**
 * @package     esitlib
 * @version     1.0.0
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
        $folder     = (substr_count($strPath, 'system/modules')) ? '/templates' : '/Resources/contao/templates';
        $strPath    = self::makePath($strPath, $folder);
        $objFiles   = self::getFiles($strPath, $strRgex);

        if ($objFiles) {
            self::registerTemplates($objFiles);
        }
    }


    /**
     * Ruft das Laden der Klassesn (*.php) für den
     * übergebenen Pfad (inkl. Unterordnern) auf.
     * @param        $strPath
     * @param string $strRgex
     */
    public static function loadClasses($strPath, $strRgex = '/^.+\.php$/i')
    {
        $strPath  = self::makePath($strPath, '/classes');
        $objFiles = self::getFiles($strPath, $strRgex);

        if ($objFiles) {
            self::registerClasses($objFiles);
        }
    }


    /**
     * Registriert die gefundenen Templates bei Contao.
     * @param $objFiles
     */
    protected static function registerTemplates($objFiles)
    {
        foreach ($objFiles as $varFile) {
            $strFile = (is_array($varFile)) ? array_shift($varFile) : $varFile;
            $objFile = pathinfo($strFile);
            \TemplateLoader::addFile($objFile['filename'], str_replace(TL_ROOT . '/', '', $objFile['dirname']));
        }
    }


    /**
     * Registriert die gefundenen Klasses bei Contao.
     * @param $objFiles
     */
    protected static function registerClasses($objFiles)
    {
        foreach ($objFiles as $varFile) {
            $strFile      = (is_array($varFile)) ? array_shift($varFile) : $varFile;
            $objFile      = pathinfo($strFile);
            $strNamespace = self::registerNamespace($objFile['dirname']);
            $strClassname = $strNamespace . $objFile['filename'];
            $strPath      = str_replace(TL_ROOT . '/', '', $objFile['dirname']) . '/' . $objFile['basename'];
            \ClassLoader::addClass($strClassname, $strPath);
        }
    }


    /**
     * Prüft den übergebenen Pfad und ergänzt die fehlenden Bestandteile.
     * @param $strPath
     * @return mixed|string
     */
    protected static function makePath($strPath, $folder)
    {
        $strPath = (!substr_count($strPath, TL_ROOT)) ? TL_ROOT . '/' . $strPath : $strPath;
        $strPath.="$folder/";
        $strPath = str_replace('//', '/', $strPath);

        return $strPath;
    }


    /**
     * Registriert den Namespace bei Contao und gibt ihn zurück.
     * @param        $strPath
     * @param string $f
     * @return mixed|string
     */
    protected static function registerNamespace($strPath, $f = TL_ROOT.'/system/modules/edenCommon/config/config.php')
    {
        $strNamespace = str_replace(TL_ROOT . '/system/modules/', '', $strPath);
        $strNamespace = str_replace('/', '\\', $strNamespace);

        if (is_file($f)) {  #@todo raus und durch direkte Übergeben des Prefixes ersetzen!
            include_once($f); // FIX: namespace_prefix not found!

            if (isset($GLOBALS['ecn']['eden']['auoload']['namspaceprefix'])) {
                $strNamespace = $GLOBALS['ecn']['eden']['auoload']['namspaceprefix'] . "\\$strNamespace\\";
            }
        }

        \ClassLoader::addNamespace($strNamespace);

        return $strNamespace;
    }


    /**
     * Sucht die Templates im übergebenen Pfad.
     * @param $strPath
     * @return \RegexIterator
     */
    protected static function getFiles($strPath, $strRgex = '/^.+\.php$/i')
    {
        if (is_dir($strPath)) {
            $objDirectory   = new \RecursiveDirectoryIterator($strPath, \FilesystemIterator::SKIP_DOTS);
            $objIterator    = new \RecursiveIteratorIterator($objDirectory);
            $objFiles       = new \RegexIterator($objIterator, $strRgex, \RecursiveRegexIterator::GET_MATCH);

            return $objFiles;
        }

        return null;
    }
}
