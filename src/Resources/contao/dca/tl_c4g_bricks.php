<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2022, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */

use con4gis\CoreBundle\Classes\Callback\C4gBrickCallback;
use Contao\DC_Table;

$cbClass = C4gBrickCallback::class;
$GLOBALS['TL_DCA']['tl_c4g_bricks'] = array
(
	// Config
	'config' => array
	(
		'dataContainer'    => DC_Table::class,
		'notCopyable'      => true,
		'notCreatable'     => true,
		'enableVersioning' => false,
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary',
			)
		),
		'onload_callback' => [[$cbClass, 'checkButtons']],
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 1,
            'fields'                  => ['brickname'],
			'panelLayout'             => 'filter',
            'headerFields'            => ['brickname','description','installedVersion','latestVersion'],
            'filter'                  => ['showBundle' => ["showBundle = ?", "1"]],
            'icon'                    => 'bundles/con4giscore/images/be-icons/con4gis_blue.svg',
		),
		'label' => array
		(
            'fields'              =>  ['brickname','description','installedVersion','latestVersion'],
            'showColumns'         => true
		),
		'global_operations' => array
		(
            'con4gisVersion' => array
            (
                'href'                => 'key=thisVersion',
                'class'               => 'header_con4gis_version',
                'button_callback'     => [$cbClass, 'con4gisVersion']
            ),
            'con4gisOrg' => array
            (
                'href'                => 'key=con4gisOrg',
                'class'               => 'header_con4gis_org',
                'button_callback'     => [$cbClass, 'con4gisOrg'],
                'icon'                => 'bundles/con4giscore/images/be-icons/con4gis.org_dark.svg',
                'label'               => 'con4gis.org'
            ),
            'con4gisIO' => array
            (
                'href'                => 'key=con4gisIO',
                'class'               => 'header_con4gis_io',
                'button_callback'     => [$cbClass, 'con4gisIO'],
                'icon'                => 'bundles/con4giscore/images/be-icons/con4gis.io.svg',
                'label'               => 'con4gis.io'
            ),
            'globalSettings' => array
            (
                'href'                => 'key=globalSettings',
                'class'               => 'header_global_settings',
                'button_callback'     => [$cbClass, 'globalSettings'],
                'icon'                => 'bundles/con4giscore/images/be-icons/global_settings_16.svg',
                'label'               => &$GLOBALS['TL_LANG']['tl_c4g_bricks']['globalSettings'][0]
            ),
            'switchInstalled' => array
            (
                'href'                => 'key=switchAll',
                'class'               => 'header_switch_installed',
                'button_callback'     => [$cbClass, 'switchInstalled'],
                'icon'                => 'bundles/con4giscore/images/be-icons/visible.svg',
                'label'               => &$GLOBALS['TL_LANG']['tl_c4g_bricks']['switchInstalledAll'][0]
            ),
            'reloadVersions' => array
            (
                'href'                => 'key=reloadVersions',
                'class'               => 'header_reload_versions',
                'button_callback'     => [$cbClass, 'reloadVersions'],
                'icon'                => 'bundles/con4giscore/images/be-icons/update_version.svg',
                'label'               => &$GLOBALS['TL_LANG']['tl_c4g_bricks']['reloadVersions'][0]
            ),
            'importData' => array
            (
                'href'                => 'key=importData',
                'class'               => 'header_import_data',
                'button_callback'     => [$cbClass, 'importData'],
                'icon'                => 'bundles/con4giscore/images/be-icons/importData.svg',
                'label'               => &$GLOBALS['TL_LANG']['tl_c4g_bricks']['importData'][0]
            ),
            'serverLogs' => array
            (
                'href'                => 'key=serverLogs',
                'class'               => 'header_server_logs',
                'button_callback'     => [$cbClass, 'serverLogs'],
                'icon'                => 'bundles/con4giscore/images/be-icons/serverlog.svg',
                'label'               => &$GLOBALS['TL_LANG']['tl_c4g_bricks']['serverLogs'][0]
            )
		),
		'operations' => array
		(
            'firstButton' => array
            (
                'href'                => 'key=firstButton',
                'icon'                => 'bundles/con4giscore/images/be-icons/edit.svg',
                'button_callback'     => [$cbClass, 'loadButton']
            ),
            'secondButton' => array
            (
                'href'                => 'key=secondButton',
                'icon'                => 'bundles/con4giscore/images/be-icons/edit.svg',
                'button_callback'     => [$cbClass, 'loadButton']
            ),
            'thirdButton' => array
            (
                'href'                => 'key=thirdButton',
                'icon'                => 'bundles/con4giscore/images/be-icons/edit.svg',
                'button_callback'     => [$cbClass, 'loadButton']
            ),
            'fourthButton' => array
            (
                'href'                => 'key=fourthButton',
                'icon'                => 'bundles/con4giscore/images/be-icons/edit.svg',
                'button_callback'     => [$cbClass, 'loadButton']
            ),
            'fifthButton' => array
            (
                'href'                => 'key=fifthButton',
                'icon'                => 'bundles/con4giscore/images/be-icons/edit.svg',
                'button_callback'     => [$cbClass, 'loadButton']
            ),
            'sixthButton' => array
            (
                'href'                => 'key=sixthButton',
                'icon'                => 'bundles/con4giscore/images/be-icons/edit.svg',
                'button_callback'     => [$cbClass, 'loadButton']
            ),
            'seventhButton' => array
            (
                'href'                => 'key=seventhButton',
                'icon'                => 'bundles/con4giscore/images/be-icons/edit.svg',
                'button_callback'     => [$cbClass, 'loadButton']
            ),
            'eighthButton' => array
            (
                'href'                => 'key=eighthButton',
                'icon'                => 'bundles/con4giscore/images/be-icons/edit.svg',
                'button_callback'     => [$cbClass, 'loadButton']
            ),
            'ninthButton' => array
            (
                'href'                => 'key=ninthButton',
                'icon'                => 'bundles/con4giscore/images/be-icons/edit.svg',
                'button_callback'     => [$cbClass, 'loadButton']
            ),
            'tenthButton' => array
            (
                'href'                => 'key=tenthButtonn',
                'icon'                => 'bundles/con4giscore/images/be-icons/edit.svg',
                'button_callback'     => [$cbClass, 'loadButton']
            ),
            'eleventhButton' => array
            (
                'href'                => 'key=eleventhButton',
                'icon'                => 'bundles/con4giscore/images/be-icons/edit.svg',
                'button_callback'     => [$cbClass, 'loadButton']
            ),
            'showDocs' => array
            (
                'href'                => 'key=showDocs',
                'icon'                => 'bundles/con4giscore/images/be-icons/help_16.svg',
                'button_callback'     => [$cbClass, 'showDocs']
            ),
            'showPackagist' => array
            (
                'href'                => 'key=showPackagist',
                'icon'                => 'bundles/con4giscore/images/be-icons/packagist_16.svg',
                'button_callback'     => [$cbClass, 'showPackagist']
            ),
            'showGitHub' => array
            (
                'href'                => 'key=showGitHub',
                'icon'                => 'bundles/con4giscore/images/be-icons/github_16.svg',
                'button_callback'     => [$cbClass, 'showGitHub']
            ),
            'favorite' => array
            (
                'href'                => 'key=switchFavorite',
                'icon'                => 'bundles/con4giscore/images/be-icons/star.svg',
                'button_callback'     => [$cbClass, 'switchFavorite']
            ),
		)
	),

	// Palettes
	'palettes' => array
	(
		'default' => '{brick_legend},brickname,description,installedVersion,latestVersion,withSettings,favorite;'
	),

	// Fields
	'fields' => array
	(
        'id' =>
        [
            'sql' => "int(10) unsigned NOT NULL auto_increment"
        ],
        'pid' =>
        [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'tstamp' =>
        [
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ],
		'brickkey' =>
        [
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>false, 'unique'=>true, 'decodeEntities'=>true, 'maxlength'=>128, 'tl_class'=>'long'),
            'sql'                     => "varchar(128) NOT NULL default ''",
            'filter'                  => true
        ],
        'brickname' =>
        [
            'inputType'               => 'text',
            'eval'                    => array('mandatory'=>false, 'unique'=>true, 'decodeEntities'=>true, 'maxlength'=>254, 'tl_class'=>'long'),
            'sql'                     => "varchar(254) NOT NULL default ''",
            'search'                  => true,
        ],
        'repository' =>
        [
            'inputType'               => 'text',
            'eval'                    => array('mandatory'=>false, 'unique'=>false, 'decodeEntities'=>true, 'maxlength'=>128, 'tl_class'=>'long'),
            'sql'                     => "varchar(128) NOT NULL default ''"
        ],
        'description' =>
        [
            'inputType'               => 'text',
            'eval'                    => array('mandatory'=>false, 'unique'=>false, 'decodeEntities'=>true, 'maxlength'=>254, 'tl_class'=>'long'),
            'sql'                     => "varchar(254) NOT NULL default ''",
            'search'                  => true
        ],
        'installedVersion' =>
        [
            'inputType'               => 'text',
            'eval'                    => array('mandatory'=>false, 'unique'=>false, 'decodeEntities'=>true, 'maxlength'=>64, 'tl_class'=>'long'),
            'sql'                     => "varchar(64) NOT NULL default ''"
        ],
        'latestVersion' =>
        [
            'inputType'               => 'text',
            'eval'                    => array('mandatory'=>false, 'unique'=>false, 'decodeEntities'=>true, 'maxlength'=>64, 'tl_class'=>'long'),
            'sql'                     => "varchar(64) NOT NULL default ''"
        ],
        'icon' =>
        [
            'inputType'               => 'text',
            'eval'                    => array('mandatory'=>false, 'unique'=>false, 'decodeEntities'=>true, 'maxlength'=>254, 'tl_class'=>'long'),
            'sql'                     => "varchar(254) NOT NULL default ''"
        ],
        'withSettings' =>
        [
            'inputType'               => 'checkbox',
            'default'                 => '0',
            'sql'                     => "char(1) NOT NULL default '0'"
        ],
        'showBundle' =>
        [
            'inputType'               => 'checkbox',
            'default'                 => '0',
            'sql'                     => "char(1) NOT NULL default '0'"
        ],
        'favorite' =>
        [
            'inputType'               => 'checkbox',
            'default'                 => '0',
            'sql'                     => "char(1) NOT NULL default '0'"
        ]
	)
);
