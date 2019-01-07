<?php

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
	    $stmt->execute($time, $bundle, $message);
    }
}