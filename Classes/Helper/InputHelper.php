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
