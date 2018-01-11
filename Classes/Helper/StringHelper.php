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

/**
 * Class StringHelper
 * @package con4gis\CoreBundle\Classes\Helper
 */
class StringHelper
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
