<?php
/**
 * @package     con4gis
 * @filesource  StingHelper.php
 * @version     1.0.0
 * @since       30.11.17 - 11:37
 * @author      Patrick Froch <info@easySolutionsIT.de>
 * @link        http://easySolutionsIT.de
 * @copyright   e@sy Solutions IT 2017
 * @license     EULA
 */
namespace con4gis\CoreBundle\Classes\Helper;

/**
 * Class StingHelper
 * @package con4gis\CoreBundle\Classes\Helper
 */
class StingHelper
{


    /**
     * Ersetzt die nicht erlaubten Zeichen der übergebenen Zeichenkette (Whitelist-Filterung).
     * @param        $string
     * @param string $allowedSigns
     * @return null|string|string[]
     */
    public function removeSpecialSigns($string, $allowedSigns = '')
    {
        // Erlaubte Zeichen festlegen:
        $defaultSigns = 'a-zA-Z0-9' . preg_quote(" üöäÜÖÄß!§$%&/()=?`}][{@<>;:_,.-#'+*\\àÀèÈòÒùÙ");
        $allowedSigns = ($allowedSigns) ? $allowedSigns : $defaultSigns;

        // String filtern:
        $string = preg_replace("|[^" . $allowedSigns . "]|", '', $string);

        return $string;
    }
}
