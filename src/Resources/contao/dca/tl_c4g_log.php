<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2021, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */

/**
 * con4gis - the gis-kit
 *
 * @version   php 7
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2018
 * @link      https://www.kuestenschmiede.de
 */

/**
 * Table tl_c4g_log
 */
$GLOBALS['TL_DCA']['tl_c4g_log'] = array
(

    // Config
    'config' => array
    (
        'dataContainer'               => 'Table',
        'sql'                         => array
        (
            'keys' => array
            (
                'id'     => 'primary',
            )
        ),
        'closed' => true,
    ),
    'list' => array
    (
        'sorting' => array
        (
            'mode'                    => 2,
            'fields'                  => array('id DESC'),
            'panelLayout'             => 'filter;sort,search,limit',
            'headerFields'            => array('tstamp', 'bundle', 'message', 'id'),
            'icon'                    => 'bundles/con4giscore/images/be-icons/con4gis_blue.svg',
        ),
        'label' => array
        (
            'fields'                  => array('tstamp', 'bundle', 'message', 'id'),
            'showColumns'             => true,
        ),
        'global_operations' => array
        (
            'all' => [
                'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'                => 'act=select',
                'class'               => 'header_edit_all',
                'attributes'          => 'onclick="Backend.getScrollOffset();" accesskey="e"'
            ],
            'back' => [
                //'href'                => 'key=back',
                'href'                => 'do=c4g_bricks&table=tl_c4g_bricks',
                'class'               => 'header_back',
                'icon'                => 'back.svg',
                'label'               => &$GLOBALS['TL_LANG']['MSC']['backBT'],
            ],
        ),
        'operations' => array
        (
            'delete' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_c4g_log']['delete'],
                'href'                => 'act=delete',
                'icon'                => 'delete.svg',
                'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
            ),
            'show' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_c4g_log']['show'],
                'href'                => 'act=show',
                'icon'                => 'show.svg'
            )
        )
    ),

    // Select
    'select' => array
    (
        'buttons_callback' => array()
    ),

    // Edit
    'edit' => array
    (
        'buttons_callback' => array()
    ),

    // Palettes
    'palettes' => array
    (
        '__selector__'                => array(''),
        'default'                     => ''
    ),

    'subpalettes' => array
    (
        ''                            => ''
    ),

    // Fields
    'fields' => array
    (
        'id' => array
        (
            'sql'                     => "int(10) unsigned NOT NULL auto_increment",
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_log']['id'],
            'sorting'                 => true,
            'search'                  => true,
        ),
        'tstamp' => array
        (
            'flag'                    => 6,
            'sql'                     => "int(10) NULL default 0",
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_log']['tstamp'],
            'default'                 => 0,
            'sorting'                 => true,
            'search'                  => true,
            'inputType'               => 'text',
        ),
        'bundle' => array
        (
            'sql'                     => "varchar(255) NOT NULL",
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_log']['bundle'],
            'inputType'               => 'text',
            'default'                 => '',
            'eval'                    => array('mandatory' => true),
            'sorting'                 => true,
            'search'                  => true,
            'filter'                  => true,
        ),
        'message' => array
        (
            'sql'                     => "text NOT NULL",
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_log']['message'],
            'inputType'               => 'text',
            'default'                 => '',
            'eval'                    => array('mandatory' => true),
            'sorting'                 => true,
            'search'                  => true,
        ),
    ),
);
class tl_c4g_log extends \Backend
{

}