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

    /**
     * @param $string
     * @return void
     */
    public static function addSpaceBeforeBracket($string) {
        $result = $string;
        $bPos = strpos($string, '&40;');
        if (($bPos > 1) && ($string[$bPos-1] != ' ')) {
            $str1 = substr($string,0, $bPos);
            $result = $str1.' '.substr($string, $bPos);
        }

        return $result;
    }

    /**
     * @param $string
     * @return void
     */
    public static function spaceToNbsp($string) {
        $htmlTag = false;
        for ($i = 0; $i < strlen($string); $i++) {
            if ($string[$i] === '<') {
                $htmlTag = true;
            } else if ($string[$i] === '>') {
                $htmlTag = false;
            } else if (!trim($string[$i]) && (trim($string[$i]) !== '0') && !$htmlTag) {
                $string[$i] = '~';
            }
        }
        $result = str_replace('~','&nbsp;', $string);
        return $result;
    }

    /**
     * @param $str
     * @return bool
     */
    public static function isBinary($str)
    {
        $umlauts = explode(',', 'Ŕ,Á,Â,Ă,Ä,Ĺ,Ç,Č,É,Ę,Ë,Ě,Í,Î,Ď,Ň,Ó,Ô,Ő,Ö,Ř,Ů,Ú,Ű,Ü,Ý,ŕ,á,â,ă,ä,ĺ,ç,č,é,ę,ë,ě,í,î,ď,đ,ň,ó,ô,ő,ö,ř,ů,ú,ű,ü,ý,˙,Ń,ń,ß');
        foreach ($umlauts as $umlaut) {
            if (false !== (strpos($str, $umlaut))) {
                return false;
            }
        }

        if (preg_match('~[^\x20-\x7E\t\r\n]~', $str) > 0) {
            return preg_match('~[^\x20-\x7E\t\r\n]~', $str) > 0;
        }
    }

    /**
     * @param $text
     * @param $length
     * @return false|string
     */
    public static function truncate($text, $length)
    {
        $text = strip_tags($text);
        $length = abs((int) $length);
        $firstFullstop = strpos($text, '.');
        if ($firstFullstop && $firstFullstop <= ($length - 1)) {
            return substr($text, 0, $firstFullstop);
        }
        if (strlen($text) > $length) {
            $text = preg_replace("/^(.{1,$length})(\s.*|$)/s", '\\1...', $text);
        }

        return(trim($text));
    }
}
