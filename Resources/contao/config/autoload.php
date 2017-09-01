<?php
/**
 * con4gis
 * @version   php 7
 * @package   con4gis
 * @author    con4gis authors (see "authors.txt")
 * @copyright Küstenschmiede GmbH Software & Design 2017
 * @link      https://www.kuestenschmiede.de
 */
/**
 * Variables
 */
$strBundle = 'con4gis/CoreBundle';

/**
 * Register the templates
 */
\con4gis\CoreBundle\Classes\Helper\AutoloadHelper::loadTemplates("/src/$strBundle/");