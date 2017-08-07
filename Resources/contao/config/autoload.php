<?php
/**
 * con4gis
 * @version   2.0.0
 * @package   con4gis
 * @author    con4gis authors (see "authors.txt")
 * @copyright Küstenschmiede GmbH Software & Design 2016 - 2017.
 * @link      https://www.kuestenschmiede.de
 */
/**
 * Variables
 */
$strBundle = 'con4gis/coreBundle';

/**
 * Register the templates
 */
\con4gis\coreBundle\classes\helper\AutoloadHelper::loadTemplates("/src/$strBundle/");