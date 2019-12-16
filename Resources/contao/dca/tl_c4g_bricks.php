<?php

/*
 * This file is part of Contao.
 *
 * (c) Leo Feyer
 *
 * @license LGPL-3.0-or-later
 */

use con4gis\CoreBundle\Classes\C4GVersionProvider;

$GLOBALS['TL_DCA']['tl_c4g_bricks'] = array
(
	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		//'ctable'                      => array('tl_c4g_map_baselayers', 'tl_style_sheet', 'tl_layout', 'tl_image_size'),
		'notCopyable'                 => true,
		'notCreatable'                => true,
		'enableVersioning'            => false,
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary'
			)
		),
		'onload_callback' => [['tl_c4g_bricks', 'checkButtons']],
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 1,
			'fields'                  => ['brickkey'],
			'panelLayout'             => '',
            'headerFields'            => ['brickkey','repository','installedVersion','latestVersion'],
		),
		'label' => array
		(
            'fields'              =>  ['brickkey','repository','installedVersion','latestVersion'],
            'showColumns'         => true
		),
		'global_operations' => array
		(
            'con4gisVersion' => array
            (
                'href'                => 'key=thisVersion',
                'class'               => 'header_con4gis_version',
                'button_callback'     => ['tl_c4g_bricks', 'con4gisVersion']
            ),
			'reloadVersions' => array
            (
                'href'                => 'key=reloadVersions',
                'class'               => 'header_reload_versions',
                'button_callback'     => ['tl_c4g_bricks', 'reloadVersions'],
                'icon'                => 'theme_import.svg'
            ),
            'serverLogs' => array
            (
                'href'                => 'key=serverLogs',
                'class'               => 'header_server_logs',
                'button_callback'     => ['tl_c4g_bricks', 'serverLogs'],
                'icon'                => 'news.svg'
            )
		),
		'operations' => array
		(
            'openSettings' => array
            (
                'href'                => 'table=tl_c4g_settings',
                'icon'                => 'settings.svg',
                'button_callback'     => ['tl_c4g_bricks', 'openSettings']
            )/*,
			'show' => array
			(
				'href'                => 'act=show',
				'icon'                => 'show.svg',
				'attributes'          => 'style="margin-right:3px"'
			)*/,
            'showDocs' => array
            (
                'href'                => 'key=showDocs',
                'icon'                => 'help.svg',
                'button_callback'     => ['tl_c4g_bricks', 'showDocs']
            ),
            'showPackagist' => array
            (
                'href'                => 'key=showPackagist',
                'icon'                => 'visible.svg',
                'button_callback'     => ['tl_c4g_bricks', 'showPackagist']
            ),
            'showGitHub' => array
            (
                'href'                => 'key=showGitHub',
                'icon'                => 'sync.svg',
                'button_callback'     => ['tl_c4g_bricks', 'showGitHub']
            ),
		)
	),

	// Palettes
	'palettes' => array
	(
		'default' => '{brick_legend},brickkey,repository,installedVersion,latestVersion;'
	),

	// Fields
	'fields' => array
	(
        'id' =>
        [
            'sql'                     => "int(10) unsigned NOT NULL auto_increment"
        ],
        'tstamp' =>
        [
            'sql'                     => "int(10) unsigned NOT NULL default '0'"
        ],
		'brickkey' => array
		(
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>false, 'unique'=>true, 'decodeEntities'=>true, 'maxlength'=>128, 'tl_class'=>'long'),
            'sql'                     => "varchar(128) NOT NULL default ''"
		),
        'repository' => array
        (
            'inputType'               => 'text',
            'eval'                    => array('mandatory'=>false, 'unique'=>false, 'decodeEntities'=>true, 'maxlength'=>254, 'tl_class'=>'long'),
            'sql'                     => "varchar(254) NOT NULL default ''"
        ),
        'installedVersion' => array
        (
            'inputType'               => 'text',
            'eval'                    => array('mandatory'=>false, 'unique'=>false, 'decodeEntities'=>true, 'maxlength'=>64, 'tl_class'=>'long'),
            'sql'                     => "varchar(64) NOT NULL default ''"
        ),
        'latestVersion' => array
        (
            'inputType'               => 'text',
            'eval'                    => array('mandatory'=>false, 'unique'=>false, 'decodeEntities'=>true, 'maxlength'=>64, 'tl_class'=>'long'),
            'sql'                     => "varchar(64) NOT NULL default ''"
        )
	)
);

/**
 * Class tl_c4g
 */
class tl_c4g_bricks extends Contao\Backend
{
    /**
     * @var C4GVersionProvider
     */
    private $versionProvider = null;

	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
        $this->versionProvider = new C4GVersionProvider();
		$this->import('Contao\BackendUser', 'User');
	}

    private function getLatestVersions()
    {
        $bundles = $GLOBALS['con4gis']['bundles'];
        $packages = [];
        foreach ($bundles as $package=>$bundle) {
            $packages[] = 'con4gis/'.$package;
        }

        $versions = [];
        foreach ($packages as $package) {
            $versions[$package] = $this->versionProvider->getLatestVersion($package);
        }
        return $versions;
    }

	/**
	 * load brick versions
	 */
	public function loadBricks($dc)
	{
        $bicks = Database::getInstance()->execute("SELECT * FROM tl_c4g_bricks LIMIT 1")->fetchAllAssoc();

        if ((!$dc) || (!$bicks)) {
            $bundles = $GLOBALS['con4gis']['bundles'];
            $installedPackages = $this->getContainer()->getParameter('kernel.packages');
            $versions = $this->getLatestVersions();

            $this->Database->prepare("DELETE FROM tl_c4g_bricks")->execute();

            //get official packages
            foreach ($GLOBALS['con4gis']['bundles'] as $bundle => $repo) {
                if ($installedPackages['con4gis/'.$bundle]) {
                    $installedVersion = $installedPackages['con4gis/'.$bundle];
                    $latestVersion    = $versions['con4gis/'.$bundle];
                } else {
                    $installedVersion = '';
                    $latestVersion    = $versions['con4gis/'.$bundle];
                }

                $set['tstamp'] = date();
                $set['brickkey'] = $bundle;
                $set['repository'] = $repo;
                $set['installedVersion'] = $installedVersion;
                $set['latestVersion']    = $latestVersion;

                $this->Database->prepare("INSERT INTO tl_c4g_bricks %s")->set($set)->execute();
            }

            //get develop packages
            foreach ($installedPackages as $vendorBundle=>$version) {
                if ((substr($vendorBundle,0,7) == 'con4gis') && (!$versions[$vendorBundle])) {
                    $bundle = substr($vendorBundle,8);
                    $installedVersion = $version;
                    $repo = '-';
                    $set['tstamp'] = date();
                    $set['brickkey'] = $bundle;
                    $set['repository'] = $repo;
                    $set['installedVersion'] = $installedVersion;
                    $set['latestVersion']    = '-';

                    $this->Database->prepare("INSERT INTO tl_c4g_bricks %s")->set($set)->execute();
                }
            }
        }
	}

    /**
     * checkButtons
     */
    public function checkButtons(Contao\DataContainer $dc)
    {
        // Check current action
        if (Contao\Input::get('key')) {
            switch (Contao\Input::get('key')) {
                case 'reloadVersions':
                    $this->loadBricks(false);
            }
        }
    }

    /**
     * reloadVersions
     * @param $href
     * @param $label
     * @param $title
     * @param $class
     * @param $attributes
     * @return string
     */
    public function reloadVersions($href, $label, $title, $class, $attributes)
    {
        $rt = Input::get('rt');
        $do = Input::get('do');

        $href = "/contao?do=$do&table=tl_c4g_bricks&rt=$rt&key=reloadVersions";
        return '<a href="' . $href . '" class="' . $class . '" title="' . StringUtil::specialchars($title) . '"' . $attributes . '>' . $label . '</a> ';
    }

    /**
     * serverLogs
     * @param $href
     * @param $label
     * @param $title
     * @param $class
     * @param $attributes
     * @return string
     */
    public function serverLogs($href, $label, $title, $class, $attributes)
    {
        $rt = Input::get('rt');
        $href = "/contao?do=c4g_log&rt=$rt&key=serverLogs";
        return '<a href="' . $href . '" class="' . $class . '" title="' . StringUtil::specialchars($title) . '"' . $attributes . '>' . $label . '</a> ';
    }

    /**
     * con4gisVersion
     * @param $href
     * @param $label
     * @param $title
     * @param $class
     * @param $attributes
     * @return string
     */
    public function con4gisVersion($href, $label, $title, $class, $attributes)
    {
        return '<div class="con4gis_version" style="margin-bottom: 16px; color: #0f3b5c;">con4gis '.$GLOBALS['con4gis']['version'].'</div>';
    }

    /**
     * openSettings
     * @param $row
     * @param $href
     * @param $label
     * @param $title
     * @param $icon
     * @return string
     */
    public function openSettings($row, $href, $label, $title, $icon) {
        //ToDo check permissions
        $rt = Input::get('rt');

        if ($row['brickkey'] == 'core') {
            $result = Database::getInstance()->execute("SELECT id FROM tl_c4g_settings LIMIT 1")->fetchAssoc();
            $href = '/contao?do=c4g_settings&id="' . $row['id'].'"&rt='.$rt.'&key=openSettings';
        } else if ($row['installedVersion']) {
            $href = '/contao?do=c4g_'.$row['brickkey'].'_configuration&rt='.$rt.'&key=openSettings';
        } else {
            return Contao\Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)) . ' ';
        }
        $attributes = 'style="margin-right:3px"';
        return  /*$this->User->hasAccess('uploadPathGeneric', 'c4g_settings') ? */'<a href="' . $href . '" title="' . StringUtil::specialchars($title) . '"'.$attributes.'>'.Image::getHtml($icon, $label).'</a>'/* : Contao\Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)) . ' '*/;
    }

    /**
     * showDocs
     * @param $row
     * @param $href
     * @param $label
     * @param $title
     * @param $icon
     * @return string
     */
    public function showDocs($row, $href, $label, $title, $icon) {
        $attributes = 'style="margin-right:3px"';
        return '<a href="https://docs.con4gis.org/con4gis-'.$row['brickkey'].'" title="'.specialchars($title).'" '.$attributes.' target="_blank" rel="noopener">'.Image::getHtml($icon, $label).'</a>';
    }

    /**
     * showPackagist
     * @param $row
     * @param $href
     * @param $label
     * @param $title
     * @param $icon
     * @return string
     */
    public function showPackagist($row, $href, $label, $title, $icon) {
        $attributes = 'style="margin-right:3px"';
        return '<a href="https://packagist.org/packages/con4gis/'.$row['brickkey'].'" title="'.specialchars($title).'" '.$attributes.' target="_blank" rel="noopener">'.Image::getHtml($icon, $label).'</a>';
    }

    /**
     * showGitHub
     * @param $row
     * @param $href
     * @param $label
     * @param $title
     * @param $icon
     * @return string
     */
    public function showGitHub($row, $href, $label, $title, $icon) {
        return '<a href="https://docs.con4gis.org/con4gis-'.$row['repository'].'" title="'.specialchars($title).'" target="_blank" rel="noopener">'.Image::getHtml($icon, $label).'</a>';
    }
}
