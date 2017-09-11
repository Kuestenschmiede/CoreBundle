<?php

/**
 * con4gis - the gis-kit
 *
 * @version   php 5
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2017.
 * @link      https://www.kuestenschmiede.de
 */

namespace c4g;

/**
 * Class HttpResultHelper
 * @package c4g
 */
class HttpResultHelper
{
    public static function BadRequest()
    {
        self::EndRequest('HTTP/1.1 400 Bad Request');
    }

    public static function NotFound()
    {
        self::EndRequest('HTTP/1.1 404 Not Found');
    }

    public static function MethodNotAllowed()
    {
        self::EndRequest('HTTP/1.1 405 Method Not Allowed');
    }

    public static function InternalServerError()
    {
        self::EndRequest('HTTP/1.1 500 Internal Server Error');
    }

    protected static function EndRequest($strHeader) {
        header($strHeader);
        die;
    }
}
