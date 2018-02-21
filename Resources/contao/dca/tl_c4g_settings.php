<?php

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

        'global_operations' => '',

        'operations' => array
        (
            'edit' => array
            (
                'label'         => $GLOBALS['TL_LANG']['tl_c4g_settings']['edit'],
                'href'          => 'act=edit',
                'icon'          => 'edit.gif',
            )
        )
    ),

	// Palettes
	'palettes' => array
	(
		'default' => '{layout_legend},c4g_uitheme_css_select,c4g_appearance_themeroller_css;'
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
        )
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
