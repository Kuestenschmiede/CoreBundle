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

/**
 * Class C4gSettingsModel
 * @package con4gis\CoreBundle\Resources\contao\models
 */
class C4gSettingsModel extends \Contao\Model
{
	/**
	 * Table name
	 * @var string
	 */
	protected static $strTable = 'tl_c4g_settings';

	public static function findSettings() {
	    $db = Database::getInstance();
	    $stmt = $db->prepare("SELECT * FROM " . self::$strTable . " LIMIT 1");
	    return new self($stmt->execute());
	}
}