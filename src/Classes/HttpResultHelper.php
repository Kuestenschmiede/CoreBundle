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

namespace con4gis\CoreBundle\Classes;

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

    protected static function EndRequest($strHeader)
    {
        header($strHeader);
        die;
    }
}
