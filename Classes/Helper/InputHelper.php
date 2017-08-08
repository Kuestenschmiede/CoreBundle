<?php
/**
 * con4gis
 * @version   2.0.0
 * @package   con4gis
 * @author    con4gis authors (see "authors.txt")
 * @copyright Küstenschmiede GmbH Software & Design 2016 - 2017.
 * @link      https://www.kuestenschmiede.de
 */
namespace con4gis\CoreBundle\Classes\Helper;

use Contao\Input;

/**
 * Class InputHelper
 * @package con4gis\CoreBundle\Classes\Helper
 */
class InputHelper
{


    /**
     * Gibt alle Daten einer Eingabemethode [POST|GET] zurück.
     * @param string $methode
     * @param bool   $decode
     * @return array
     */
    public static function getAllData($methode = 'post', $decode = false)
    {
        $data = array();

        if ($methode == 'post') {
            $keys = array_keys($_POST);

            foreach ($keys as $key) {
                $data[$key] = ($decode) ? urldecode(Input::post($key)) : Input::post($key);
            }
        } elseif ($methode = 'get') {
            $keys = array_keys($_GET);

            foreach ($keys as $key) {
                $data[$key] = ($decode) ? urldecode(Input::get($key)) : Input::get($key);
            }
        }

        return $data;
    }
}
