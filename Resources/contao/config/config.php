<?php

/*
 * con4gis - the gis-kit
 *
 * @version   php 7
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2017.
 * @link      https://www.kuestenschmiede.de
 */

$GLOBALS['con4gis']['core']['version'] = '4.0';

array_insert($GLOBALS['BE_MOD'],1, array('con4gis' => array()));
array_insert($GLOBALS['BE_MOD'],2, array('con4gis_bricks' => array()));

/** Damit die CSS nicht nur im Modul selbst geladen wird */
$GLOBALS['TL_CSS'][] = '/bundles/con4giscore/con4gis.css';

if (class_exists('\Contao\StringUtil')) {
    $GLOBALS['con4gis']['stringClass'] = '\Contao\StringUtil';
} else {
    $GLOBALS['con4gis']['stringClass'] = '\Contao\String';
}