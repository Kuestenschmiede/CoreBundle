<?php
/*
 * This file is part of con4gis,
 * the gis-kit for Contao CMS.
 *
 * @package    con4gis
 * @version    7
 * @author     con4gis contributors (see "authors.txt")
 * @license    LGPL-3.0-or-later
 * @copyright  Küstenschmiede GmbH Software & Design
 * @link       https://www.con4gis.org
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
        $defaultSigns = 'a-zA-Z0-9' . preg_quote("\+*?[^]$(){}=!<>|:-#");
        $allowedSigns = ($allowedSigns) ? $allowedSigns : $defaultSigns;

        // String filtern:
        $string = preg_replace('|[^' . $allowedSigns . ']|', '', $string);

        return $string;
    }
}
