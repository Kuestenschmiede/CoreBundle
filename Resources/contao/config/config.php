<?php

/*
 * con4gis - the gis-kit
 *
 * @version   php 5
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2017.
 * @link      https://www.kuestenschmiede.de
 */

array_insert($GLOBALS['BE_MOD'],1, array('con4gis' => array()));
array_insert($GLOBALS['BE_MOD'],2, array('con4gis_bricks' => array()));

/** Damit die CSS nicht nur im Modul selbst geladen wird */
$GLOBALS['TL_CSS'][] = '/bundles/con4giscore/con4gis.css|static';