<?php
/**
 * con4gis
 * @version   php 7
 * @package   con4gis
 * @author    con4gis authors (see "authors.txt")
 * @copyright Küstenschmiede GmbH Software & Design 2017
 * @link      https://www.kuestenschmiede.de
 */
namespace con4gis\CoreBundle\Classes\Helper;

use Contao\CoreBundle\HttpKernel\Bundle\ContaoModuleBundle;

/**
 * Class BundleHelper
 * @package con4gis\CoreBundle\Classes\Helper
 */
class BundleHelper
{


    /**
     * Pfad zu den Erweiterunen
     * @var string
     */
    protected static $extensionsPath = '/system/modules';


    /**
     * Pfad zu den Bundles
     * @var string
     */
    protected static $bundlePath = '/src';


    /**
     * Erstellt die Einträge der Contao-Module für den AppKernel.
     * @param $path
     * @param $bundles
     * @return array
     */
    public static function generateContaoBundles($path, $bundles)
    {
        $bundelPatteren = dirname($path) . self::$extensionsPath . '/*';
        $dirs           = glob($bundelPatteren);

        foreach ($dirs as $dir) {
            $bundles[] = new ContaoModuleBundle(basename($dir), $path);
        }

        return $bundles;
    }


    /**
     * Erstellt die Einträge der Bundles für den AppKernel.
     * @param $path
     * @param $bundles
     * @return array
     */
    public static function generateSymfonyBundles($path, $bundles)
    {
        $bundleRoot     = dirname($path) . self::$bundlePath;
        $bundelPatteren = $bundleRoot . '/*/*/*Bundle.php';
        $bundleFiles    = glob($bundelPatteren);

        if (is_array($bundleFiles) && count($bundleFiles)) {
            foreach ($bundleFiles as $bundleFile) {
                // coreBundle muss seperat registriert werden, damit der BundleHelper gefunden wird!
                if (!substr_count($bundleFile, 'src/con4gis/CoreBundle/con4gisCoreBundle.php')) {
                    $class     = str_replace($bundleRoot . '/', '', $bundleFile);
                    $class     = str_replace('/', '\\', $class);
                    $class     = str_replace('.php', '', $class);
                    $bundles[] = new $class();
                }
            }
        }

        return $bundles;
    }
}
