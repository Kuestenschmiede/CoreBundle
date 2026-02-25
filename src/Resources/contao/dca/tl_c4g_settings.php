<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @author con4gis contributors (see "authors.md")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2026, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */
use Contao\Backend;
use Contao\Input;
use Contao\DC_Table;
use Contao\Database;

$GLOBALS['TL_DCA']['tl_c4g_settings'] = array
(

	// Config
	'config' => array
	(
        'dataContainer'     => DC_Table::class,
        'enableVersioning'  => false,
        'notDeletable' => true,
        'notCopyable' => true,
        'closed' => (Input::get('id')),
        'onload_callback'			=> array
        (
            array('tl_c4g_settings', 'loadDataset'),
        ),
        'sql'               => array
        (
            'keys' => array
            (
                'id' => 'primary',
            )
        )
    ),

	// Palettes
	'palettes' => array
	(
		'default' => '{global_legend},showBundleNames;'.
                     '{con4gisIoLegend},con4gisIoUrl,con4gisIoKey;'.
                     '{upload_legend:hide},uploadAllowedImageTypes,uploadAllowedImageWidth,uploadAllowedImageHeight,uploadPathImages,uploadAllowedDocumentTypes,uploadPathDocuments,uploadAllowedGenericTypes,uploadPathGeneric,uploadMaxFileSize;'.
                     '{layout_legend:hide},c4g_uitheme_css_select,c4g_appearance_themeroller_css;' .
                     '{expert_legend:hide},disableJQueryLoading;'
	),

	// Fields
	'fields' => array
	(
        'id' => array
        (
            'sql' => "int(10) unsigned NOT NULL auto_increment"
        ),

        'tstamp' => array
        (
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ),
        'showBundleNames' =>
        [
            'exclude'                 => true,
            'default'                 => false,
            'inputType'               => 'checkbox',
            'eval'                    => ['tl_class'=>'clr'],
            'sql'                     => "char(1) NOT NULL default '0'"
        ],
        'con4gisIoUrl' =>[
            'exclude'                 => true,
            'inputType'               => 'text',
            'eval'                    => ['maxlength' => 100],
            'sql'                     => "varchar(100) default ''"
        ],
        'con4gisIoKey' => [
            'exclude'                 => true,
            'inputType'               => 'text',
            'eval'                    => ['maxlength' => 34],
            'sql'                     => "varchar(34) default ''"
        ],
        'c4g_uitheme_css_select' => array
        (
            'exclude'                 => true,
            'default'                 => 'base',
            'inputType'               => 'radio',
            'options'                 => array('base','black-tie','blitzer','cupertino','dark-hive','dot-luv','eggplant','excite-bike','flick','hot-sneaks','humanity','le-frog','mint-choc','overcast','pepper-grinder','redmond','smoothness','south-street','start','sunny','swanky-purse','trontastic','ui-darkness','ui-lightness','vader'),
            'eval'                    => array('mandatory'=>true, 'submitOnChange' => true),
            'reference'               => &$GLOBALS['TL_LANG']['tl_c4g_settings']['c4g_references'],
            'sql'                     => "char(100) NOT NULL default 'base'"
        ),
        'c4g_appearance_themeroller_css' => array
        (
            'exclude'                 => true,
            'inputType'               => 'fileTree',
            'eval'                    => array('fieldType'=>'radio', 'files'=>true, 'extensions'=>'css', 'class'=>'long'),
            'sql'                     => "binary(16) NULL"
        ),
        'uploadAllowedImageTypes' => array
        (
            'inputType' => 'text',
            'default' => 'image/jpg,image/jpeg,image/png,image/gif',
            'eval' => array('mandatory' => false),
            'sql' => 'varchar(255) NOT NULL default "image/jpg,image/jpeg,image/png,image/gif"'
        ),
        'uploadAllowedImageWidth' => array
        (
            'inputType' => 'text',
            'default'   => '800',
            'eval'      => array('mandatory' => false, 'rgxp' => 'digit', 'nospace' => true, 'tl_class' => 'w50'),
            'sql'       => "varchar(10) NOT NULL default '800'"
        ),
        'uploadAllowedImageHeight' => array
        (
            'inputType' => 'text',
            'default'   => '600',
            'eval'      => array('mandatory' => false, 'rgxp' => 'digit', 'nospace' => true, 'tl_class' => 'w50'),
            'sql'       => "varchar(10) NOT NULL default '600'"
        ),
        'uploadPathImages'  => array
        (
            'exclude'   => true,
            'default'   => null,
            'inputType' => 'fileTree',
            'eval'      => array('fieldType' => 'radio', 'tl_class' => 'clr', 'mandatory' => false),
            'sql'       => "blob NULL"
        ),
        'uploadAllowedDocumentTypes' => array
        (
            'inputType' => 'text',
            'default' => 'application/pdf',
            'eval' => array('mandatory' => false),
            'sql' => 'varchar(255) NOT NULL default "application/pdf"'
        ),
        'uploadPathDocuments'  => array
        (
            'exclude'   => true,
            'default'   => null,
            'inputType' => 'fileTree',
            'eval'      => array('fieldType' => 'radio', 'tl_class' => 'clr', 'mandatory' => false),
            'sql'       => "blob NULL"
        ),
        'uploadAllowedGenericTypes' => array
        (
            'inputType' => 'text',
            'default' => 'application/zip',
            'eval' => array('mandatory' => false),
            'sql' => 'varchar(255) NOT NULL default "application/zip"'
        ),
        'uploadPathGeneric'  => array
        (
            'exclude'   => true,
            'default'   => null,
            'inputType' => 'fileTree',
            'eval'      => array('fieldType' => 'radio', 'mandatory' => false, 'tl_class' => 'clr'),
            'sql'       => "blob NULL"
        ),
        'uploadMaxFileSize' => array
        (
            'inputType' => 'text',
            'default'   => '2048000',
            'eval'      => array('mandatory' => false, 'rgxp' => 'digit', 'nospace' => true, 'tl_class' => 'clr'),
            'sql'       => "varchar(255) NOT NULL default '2048000'"
        ),
        'disableJQueryLoading' => [
            'exclude'                 => true,
            'default'                 => false,
            'inputType'               => 'checkbox',
            'eval'                    => ['tl_class'=>'clr'],
            'sql'                     => "char(1) NOT NULL default ''"
        ]
    )
);

/**
 * Class tl_c4g_settings
 */
class tl_c4g_settings extends Backend
{
    public function loadDataset()
    {
        $objConfig = Database::getInstance()->prepare("SELECT id FROM tl_c4g_settings")->execute();

        if (Input::get('key')) return;

        if(!$objConfig->numRows && !Input::get('act'))
        {
            $this->redirect($this->addToUrl('act=create'));
        }


        if(!Input::get('id') && !Input::get('act'))
        {
            $GLOBALS['TL_DCA']['tl_c4g_settings']['config']['notCreatable'] = true;
            $this->redirect($this->addToUrl('act=edit&id='.$objConfig->id));
        }

        \Contao\Message::addInfo($GLOBALS['TL_LANG']['tl_c4g_settings']['infotext']);
    }
}
