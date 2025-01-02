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

/**
 * con4gis - the gis-kit
 *
 * @version   php 5
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2018
 * @link      https://www.kuestenschmiede.de
 */


namespace con4gis\CoreBundle\Resources\contao\models;

use Contao\Database;
use Contao\Model;
use \Iterator;
use \Throwable;

/**
 * Class C4gLogModel
 * @package con4gis\CoreBundle\Resources\contao\models
 */
class C4gLogModel extends Model
{
	/**
	 * Table name
	 * @var string
	 */
	protected static $strTable = 'tl_c4g_log';

	public static function addLogEntry(string $bundle, string $message) {
	    $time = time();
	    $db = Database::getInstance();
	    $stmt = $db->prepare("INSERT INTO ".self::$strTable." (tstamp, bundle, message) VALUES (?, ?, ?)");
	    try {
            $stmt->execute($time, $bundle, $message);
        } catch (Throwable $throwable) {

        }
    }

    public static function recursivelyLogIterator(Iterator $iterator) {
        static::recursivelyLog($iterator);
    }

    public static function recursivelyLogArray(array $array) {
        static::recursivelyLog($array);
    }

    private static function recursivelyLog($param) {
        foreach ($param as $key => $value) {
            if (is_array($value) || $value instanceof Iterator) {
                static::recursivelyLog($value);
            } else {
                static::addLogEntry($key, strval($value));
            }
        }
    }
}