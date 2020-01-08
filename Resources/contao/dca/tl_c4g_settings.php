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

$GLOBALS['TL_DCA']['tl_c4g_settings'] = array
(

	// Config
	'config' => array
	(
        'dataContainer'     => 'Table',
        'enableVersioning'  => false,
        'notDeletable' => true,
        'notCopyable' => true,
        'closed' => (\Input::get('id')),
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


    //List
    'list' => array
    (
        'sorting' => array
        (
            'mode'              => 2,
            'fields'            => array('test'),
            'panelLayout'       => 'limit',
        ),

        'label' => array
        (
            'fields'            => array('test'),
            'showColumns'       => true,
            'format'            => '%s',
        ),

        'global_operations' => [],

        'operations' => array
        (
            'edit' => array
            (
                'label'         => $GLOBALS['TL_LANG']['tl_c4g_settings']['edit'],
                'href'          => 'act=edit',
                'icon'          => 'edit.svg',
            )
        )
    ),

	// Palettes
	'palettes' => array
	(
		'default' => '{layout_legend:hide},c4g_uitheme_css_select,c4g_appearance_themeroller_css;'.
                     '{upload_legend:hide},uploadAllowedImageTypes,uploadAllowedImageWidth,uploadAllowedImageHeight,uploadPathImages,uploadAllowedDocumentTypes,uploadPathDocuments,uploadAllowedGenericTypes,uploadPathGeneric,uploadMaxFileSize;'.
                     '{con4gisIoLegend:hide},con4gisIoUrl,con4gisIoKey;'
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
        'c4g_uitheme_css_select' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_settings']['c4g_uitheme_css_select'],
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
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_settings']['c4g_appearance_themeroller_css'],
            'exclude'                 => true,
            'inputType'               => 'fileTree',
            'eval'                    => array('fieldType'=>'radio', 'files'=>true, 'extensions'=>'css', 'class'=>'long'),
            'sql'                     => "binary(16) NULL"
        ),
        'uploadAllowedImageTypes' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_c4g_settings']['uploadAllowedImageTypes'],
            'inputType' => 'text',
            'default' => 'image/jpg,image/jpeg,image/png,image/gif',
            'eval' => array('mandatory' => false),
            'sql' => 'varchar(255) NOT NULL default "image/jpg,image/jpeg,image/png,image/gif"'
        ),
        'uploadAllowedImageWidth' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_c4g_settings']['uploadAllowedImageWidth'],
            'inputType' => 'text',
            'default'   => '800',
            'eval'      => array('mandatory' => false, 'rgxp' => 'digit', 'nospace' => true, 'tl_class' => 'w50'),
            'sql'       => "varchar(10) NOT NULL default '800'"
        ),
        'uploadAllowedImageHeight' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_c4g_settings']['uploadAllowedImageHeight'],
            'inputType' => 'text',
            'default'   => '600',
            'eval'      => array('mandatory' => false, 'rgxp' => 'digit', 'nospace' => true, 'tl_class' => 'w50'),
            'sql'       => "varchar(10) NOT NULL default '600'"
        ),
        'uploadPathImages'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_c4g_settings']['uploadPathImages'],
            'exclude'   => true,
            'default'   => null,
            'inputType' => 'fileTree',
            'eval'      => array('fieldType' => 'radio', 'tl_class' => 'clr', 'mandatory' => false),
            'sql'       => "blob NULL"
        ),
        'uploadAllowedDocumentTypes' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_c4g_settings']['uploadAllowedDocumentTypes'],
            'inputType' => 'text',
            'default' => 'application/pdf',
            'eval' => array('mandatory' => false),
            'sql' => 'varchar(255) NOT NULL default "application/pdf"'
        ),
        'uploadPathDocuments'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_c4g_settings']['uploadPathDocuments'],
            'exclude'   => true,
            'default'   => null,
            'inputType' => 'fileTree',
            'eval'      => array('fieldType' => 'radio', 'tl_class' => 'clr', 'mandatory' => false),
            'sql'       => "blob NULL"
        ),
        'uploadAllowedGenericTypes' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_c4g_settings']['uploadAllowedGenericTypes'],
            'inputType' => 'text',
            'default' => 'application/zip',
            'eval' => array('mandatory' => false),
            'sql' => 'varchar(255) NOT NULL default "application/zip"'
        ),
        'uploadPathGeneric'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_c4g_settings']['uploadPathGeneric'],
            'exclude'   => true,
            'default'   => null,
            'inputType' => 'fileTree',
            'eval'      => array('fieldType' => 'radio', 'mandatory' => false, 'tl_class' => 'clr'),
            'sql'       => "blob NULL"
        ),
        'uploadMaxFileSize' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_c4g_settings']['uploadMaxFileSize'],
            'inputType' => 'text',
            'default'   => '2048000',
            'eval'      => array('mandatory' => false, 'rgxp' => 'digit', 'nospace' => true, 'tl_class' => 'clr'),
            'sql'       => "varchar(255) NOT NULL default '2048000'"
        ),
        'con4gisIoUrl' =>[
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_settings']['con4gisIoUrl'],
            'exclude'                 => true,
            'inputType'               => 'text',
            'eval'                    => ['maxlength' => 100],
            'sql'                     => "varchar(100) default ''"
        ],
        'con4gisIoKey' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_settings']['con4gisIoKey'],
            'exclude'                 => true,
            'inputType'               => 'text',
            'eval'                    => ['maxlength' => 32],
            'sql'                     => "varchar(32) default ''"
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

        if (\Input::get('key')) return;

        if(!$objConfig->numRows && !\Input::get('act'))
        {
            $this->redirect($this->addToUrl('act=create'));
        }


        if(!\Input::get('id') && !\Input::get('act'))
        {
            $GLOBALS['TL_DCA']['tl_c4g_settings']['config']['notCreatable'] = true;
            $this->redirect($this->addToUrl('act=edit&id='.$objConfig->id));
        }

        \Contao\Message::addInfo($GLOBALS['TL_LANG']['tl_c4g_settings']['infotext']);
    }
}
