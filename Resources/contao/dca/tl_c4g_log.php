<?php

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
        )
    ),
    'list' => array
    (
        'sorting' => array
        (
            'mode'                    => 2,
            'fields'                  => array('id'),
            'panelLayout'             => 'filter;sort,search,limit',
            'headerFields'            => array('id', 'tstamp', 'bundle', 'message'),
        ),
        'label' => array
        (
            'fields'                  => array('id', 'tstamp', 'bundle', 'message'),
            'showColumns'             => true,
        ),
        'global_operations' => array
        (
            'all' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'                => 'act=select',
                'class'               => 'header_edit_all',
                'attributes'          => 'onclick="Backend.getScrollOffset();" accesskey="e"'
            )
        ),
        'operations' => array
        (
            'delete' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_c4g_log']['delete'],
                'href'                => 'act=delete',
                'icon'                => 'delete.gif',
                'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
            ),
            'show' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_c4g_log']['show'],
                'href'                => 'act=show',
                'icon'                => 'show.gif'
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
            'sql'                     => "int(10) NULL default 0",
            'default'                 => 0,
            'sorting'                 => true,
            'search'                  => true,
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