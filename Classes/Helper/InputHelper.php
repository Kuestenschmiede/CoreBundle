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


    /**
     * Gibt den RequestToken zurück.
     * @param string $serviceName
     * @param string $tokenName
     * @return mixed
     */
    public static function getRequestToken(
        $serviceName = 'security.csrf.token_manager',
        $tokenName = 'contao.csrf_token_name'
    ) {
        $c      = \System::getContainer();
        $param  = $c->getParameter($tokenName);
        return $c->get($serviceName)->getToken($param)->getValue();
    }
}
