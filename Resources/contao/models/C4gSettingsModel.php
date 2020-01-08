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

namespace con4gis\CoreBundle\Resources\contao\models;

use Contao\Database;
use Contao\Model;

/**
 * Class C4gSettingsModel
 * @package con4gis\CoreBundle\Resources\contao\models
 */
class C4gSettingsModel extends Model
{
	/**
	 * Table name
	 * @var string
	 */
	protected static $strTable = 'tl_c4g_settings';

	public static function findSettings() {
        $collSettings = static::findAll();
        foreach ($collSettings as $objSettings) {
            return $objSettings;
        }
        return null;
	}
}