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
            'headerFields'            => ['brickkey','description','installedVersion','latestVersion'],
		),
		'label' => array
		(
            'fields'              =>  ['brickkey','description','installedVersion','latestVersion'],
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
            'globalSettings' => array
            (
                'href'                => 'key=globalSettings',
                'class'               => 'header_global_settings',
                'button_callback'     => ['tl_c4g_bricks', 'globalSettings'],
                'icon'                => 'settings.svg'
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
            ),
            'con4gisOrg' => array
            (
                'href'                => 'key=con4gisOrg',
                'class'               => 'header_con4gis_org',
                'button_callback'     => ['tl_c4g_bricks', 'con4gisOrg'],
                'icon'                => 'news.svg'
            ),
            'con4gisIO' => array
            (
                'href'                => 'key=con4gisIO',
                'class'               => 'header_con4gis_io',
                'button_callback'     => ['tl_c4g_bricks', 'con4gisIO'],
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
		'default' => '{brick_legend},brickkey,description,installedVersion,latestVersion,withSettings;'
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
            'sql'                     => "varchar(128) NOT NULL default ''"
        ),
        'description' => array
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
        ),
        'withSettings' => array
        (
            'inputType'               => 'checkbox',
            'default'                 => '0',
            'sql'                     => "char(1) NOT NULL default '0'"
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
     * @var array
     */
    private $bundles = [];

	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
        $this->versionProvider = new C4GVersionProvider();
		$this->import('Contao\BackendUser', 'User');

        $this->bundles = [
            'core' => [
                'repo' => 'CoreBundle',
                'description' => $GLOBALS['TL_LANG']['tl_c4g_bricks']['core']
            ],
            'documents' => [
                'repo' => 'DocumentsBundle',
                'description' => $GLOBALS['TL_LANG']['tl_c4g_bricks']['documents']
            ],
            'editor' => [
                'repo' => 'EditorBundle',
                'description' => $GLOBALS['TL_LANG']['tl_c4g_bricks']['editor']
            ],
            'export' => [
                'repo' => 'ExportBundle',
                'description' => $GLOBALS['TL_LANG']['tl_c4g_bricks']['export']
            ],
            'firefighter' => [
                'repo' => 'FirefighterBundle',
                'description' => $GLOBALS['TL_LANG']['tl_c4g_bricks']['firefighter']
            ],
            'forum' => [
                'repo' => 'ForumBundle',
                'description' => $GLOBALS['TL_LANG']['tl_c4g_bricks']['forum']
            ],
            'groups' => [
                'repo' => 'GroupsBundle',
                'description' => $GLOBALS['TL_LANG']['tl_c4g_bricks']['groups']
            ],
            'groups' => [
                'repo' => 'GroupsBundle',
                'description' => $GLOBALS['TL_LANG']['tl_c4g_bricks']['groups']
            ],
            'import' => [
                'repo' => 'ImportBundle',
                'description' => $GLOBALS['TL_LANG']['tl_c4g_bricks']['import']
            ],
            'maps' => [
                'repo' => 'MapsBundle',
                'description' => $GLOBALS['TL_LANG']['tl_c4g_bricks']['maps']
            ],
            'routing' => [
                'repo' => 'RoutingBundle',
                'description' => $GLOBALS['TL_LANG']['tl_c4g_bricks']['routing']
            ],
            'projects' => [
                'repo' => 'ProjectsBundle',
                'description' => $GLOBALS['TL_LANG']['tl_c4g_bricks']['projects']
            ],
            'pwa' => [
                'repo' => 'PwaBundle',
                'description' => $GLOBALS['TL_LANG']['tl_c4g_bricks']['pwa']
            ],
            'queue' => [
                'repo' => 'QueueBundle',
                'description' => $GLOBALS['TL_LANG']['tl_c4g_bricks']['queue']
            ],
            'tracking' => [
                'repo' => 'TrackingBundle',
                'description' => $GLOBALS['TL_LANG']['tl_c4g_bricks']['tracking']
            ],
            'visualization' => [
                'repo' => 'VisualizationBundle',
                'description' => $GLOBALS['TL_LANG']['tl_c4g_bricks']['visualization']
            ],
            'io-travel-costs' => [
                'repo' => 'IOTravelCostsBundle',
                'description' => $GLOBALS['TL_LANG']['tl_c4g_bricks']['io-travel-costs']
            ]
        ];
	}

    private function getLatestVersions()
    {
        $bundles = $this->bundles;
        $packages = [];
        foreach ($bundles as $bundle => $values) {
            $packages[] = 'con4gis/'.$bundle;
        }

        $versions = [];
        foreach ($packages as $package) {
            $versions[$package] = $this->versionProvider->getLatestVersion($package);
        }
        return $versions;
    }

    private function checkSettings($bundle) {
	    if ($bundle == 'core') {
	        return true;
        } else {
	        $table = 'tl_c4g_'.$bundle.'_configuration';
	        try {
                $result = Database::getInstance()->execute("SELECT * FROM $table LIMIT 1")->fetchAllAssoc();
                return true;
            } catch (Exception $e) {
	            return false;
            }
        }
    }

	/**
	 * load brick versions
	 */
	public function loadBricks($dc)
	{
        $bicks = Database::getInstance()->execute("SELECT * FROM tl_c4g_bricks LIMIT 1")->fetchAllAssoc();

        if ((!$dc) || (!$bicks)) {
            $bundles = $this->bundles;
            $installedPackages = $this->getContainer()->getParameter('kernel.packages');
            $versions = $this->getLatestVersions();

            $this->Database->prepare("DELETE FROM tl_c4g_bricks")->execute();

            //get official packages
            foreach ($bundles as $bundle => $values) {
                if ($installedPackages['con4gis/'.$bundle]) {
                    $installedVersion = $installedPackages['con4gis/'.$bundle];
                    $latestVersion    = $versions['con4gis/'.$bundle];
                } else {
                    $installedVersion = '';
                    $latestVersion    = $versions['con4gis/'.$bundle];
                }

                $set['tstamp'] = date();
                $set['brickkey'] = $bundle;
                $set['repository'] = $values['repo'];
                $set['description'] = $values['description'];
                $set['installedVersion'] = $installedVersion;
                $set['latestVersion'] = $latestVersion;
                $set['withSettings'] = intval($this->checkSettings($bundle));

                $this->Database->prepare("INSERT INTO tl_c4g_bricks %s")->set($set)->execute();
            }

            //get develop packages
            foreach ($installedPackages as $vendorBundle=>$version) {
                if ((substr($vendorBundle,0,7) == 'con4gis') && (!$versions[$vendorBundle])) {
                    $bundle = substr($vendorBundle,8);
                    $installedVersion = $version;

                    $set['tstamp'] = date();
                    $set['brickkey'] = $bundle;
                    $set['repository'] = '-';
                    $set['description'] = '-';
                    $set['installedVersion'] = $installedVersion;
                    $set['latestVersion']    = '-';
                    $set['withSettings'] = intval($this->checkSettings($bundle));

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
     * globalSettings
     * @param $href
     * @param $label
     * @param $title
     * @param $class
     * @param $attributes
     * @return string
     */
    public function globalSettings($href, $label, $title, $class, $attributes)
    {
        $rt = Input::get('rt');
        $result = Database::getInstance()->execute("SELECT id FROM tl_c4g_settings LIMIT 1")->fetchAssoc();
        $href = '/contao?do=c4g_settings&id="' . $result['id'].'"&rt='.$rt.'&key=openSettings';
        return '<a href="' . $href . '" class="' . $class . '" title="' . StringUtil::specialchars($title) . '"' . $attributes . '>' . $label . '</a> ';
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
     * con4gisOrg
     * @param $href
     * @param $label
     * @param $title
     * @param $class
     * @param $attributes
     * @return string
     */
    public function con4gisOrg($href, $label, $title, $class, $attributes)
    {
        return '<a href="https://con4gis.org"  class="' . $class . '" title="' . StringUtil::specialchars($title) . '"' . $attributes .' target="_blank" rel="noopener">' . $label . '</a>';
    }

    /**
     * con4gisIO
     * @param $href
     * @param $label
     * @param $title
     * @param $class
     * @param $attributes
     * @return string
     */
    public function con4gisIO($href, $label, $title, $class, $attributes)
    {
        return '<a href="https://con4gis.io"  class="' . $class . '" title="' . StringUtil::specialchars($title) . '"' . $attributes .' target="_blank" rel="noopener">' . $label . '</a>';
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

        if ($row['installedVersion'] && $row['withSettings'] && ($row['brickkey'] != 'core')) {
            $href = '/contao?do=c4g_'.$row['brickkey'].'_configuration&rt='.$rt.'&key=openSettings';
        } else if ($row['installedVersion'] && ($row['brickkey'] == 'import')) {

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
