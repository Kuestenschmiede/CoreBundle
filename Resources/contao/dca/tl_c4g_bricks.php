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
			'fields'                  => ['brickname'],
			'panelLayout'             => '',
            'headerFields'            => ['brickname','description','installedVersion','latestVersion'],
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
                'button_callback'     => ['tl_c4g_bricks', 'con4gisVersion']
            ),
            'globalSettings' => array
            (
                'href'                => 'key=globalSettings',
                'class'               => 'header_global_settings',
                'button_callback'     => ['tl_c4g_bricks', 'globalSettings'],
                'icon'                => 'bundles/con4giscore/images/be-icons/global_settings_16.svg'
            ),
            'switchInstalled' => array
            (
                'href'                => 'key=switchInstalled',
                'class'               => 'header_switch_installed',
                'button_callback'     => ['tl_c4g_bricks', 'switchInstalled'],
                'icon'                => 'bundles/con4giscore/images/be-icons/show_install_aktiv.svg',
                'label'               => $GLOBALS['TL_LANG']['tl_c4g_bricks']['switchInstalled'][0]
            ),
            'reloadVersions' => array
            (
                'href'                => 'key=reloadVersions',
                'class'               => 'header_reload_versions',
                'button_callback'     => ['tl_c4g_bricks', 'reloadVersions'],
                'icon'                => 'bundles/con4giscore/images/be-icons/update_version.svg'
            ),
            'serverLogs' => array
            (
                'href'                => 'key=serverLogs',
                'class'               => 'header_server_logs',
                'button_callback'     => ['tl_c4g_bricks', 'serverLogs'],
                'icon'                => 'bundles/con4giscore/images/be-icons/serverlog.svg'
            ),
            'con4gisOrg' => array
            (
                'href'                => 'key=con4gisOrg',
                'class'               => 'header_con4gis_org',
                'button_callback'     => ['tl_c4g_bricks', 'con4gisOrg'],
                'icon'                => 'bundles/con4giscore/images/be-icons/con4gis.org_dark.svg'
            ),
            'con4gisIO' => array
            (
                'href'                => 'key=con4gisIO',
                'class'               => 'header_con4gis_io',
                'button_callback'     => ['tl_c4g_bricks', 'con4gisIO'],
                'icon'                => 'bundles/con4giscore/images/be-icons/con4gis.io.svg'
            )
		),
		'operations' => array
		(
            'firstButton' => array
            (
                'href'                => 'key=firstButton',
                'icon'                => 'bundles/con4giscore/images/be-icons/pen_16.svg',
                'button_callback'     => ['tl_c4g_bricks', 'loadButton']
            ),
            'secondButton' => array
            (
                'href'                => 'key=secondButton',
                'icon'                => 'bundles/con4giscore/images/be-icons/pen_16.svg',
                'button_callback'     => ['tl_c4g_bricks', 'loadButton']
            ),
            'thirdButton' => array
            (
                'href'                => 'key=thirdButton',
                'icon'                => 'bundles/con4giscore/images/be-icons/pen_16.svg',
                'button_callback'     => ['tl_c4g_bricks', 'loadButton']
            ),
            'fourthButton' => array
            (
                'href'                => 'key=fourthButton',
                'icon'                => 'bundles/con4giscore/images/be-icons/pen_16.svg',
                'button_callback'     => ['tl_c4g_bricks', 'loadButton']
            ),
            'fifthButton' => array
            (
                'href'                => 'key=fifthButton',
                'icon'                => 'bundles/con4giscore/images/be-icons/pen_16.svg',
                'button_callback'     => ['tl_c4g_bricks', 'loadButton']
            ),
            'sixthButton' => array
            (
                'href'                => 'key=sixthButton',
                'icon'                => 'bundles/con4giscore/images/be-icons/pen_16.svg',
                'button_callback'     => ['tl_c4g_bricks', 'loadButton']
            ),
            'seventhButton' => array
            (
                'href'                => 'key=seventhButton',
                'icon'                => 'bundles/con4giscore/images/be-icons/pen_16.svg',
                'button_callback'     => ['tl_c4g_bricks', 'loadButton']
            ),
            'eighthButton' => array
            (
                'href'                => 'key=eighthButton',
                'icon'                => 'bundles/con4giscore/images/be-icons/pen_16.svg',
                'button_callback'     => ['tl_c4g_bricks', 'loadButton']
            ),
            'showDocs' => array
            (
                'href'                => 'key=showDocs',
                'icon'                => 'bundles/con4giscore/images/be-icons/help_16.svg',
                'button_callback'     => ['tl_c4g_bricks', 'showDocs']
            ),
            'showPackagist' => array
            (
                'href'                => 'key=showPackagist',
                'icon'                => 'bundles/con4giscore/images/be-icons/packagist_16.svg',
                'button_callback'     => ['tl_c4g_bricks', 'showPackagist']
            ),
            'showGitHub' => array
            (
                'href'                => 'key=showGitHub',
                'icon'                => 'bundles/con4giscore/images/be-icons/github_16.svg',
                'button_callback'     => ['tl_c4g_bricks', 'showGitHub']
            ),
		)
	),

	// Palettes
	'palettes' => array
	(
		'default' => '{brick_legend},brickname,description,installedVersion,latestVersion,withSettings;'
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
        'brickname' => array
        (
            'inputType'               => 'text',
            'eval'                    => array('mandatory'=>false, 'unique'=>true, 'decodeEntities'=>true, 'maxlength'=>254, 'tl_class'=>'long'),
            'sql'                     => "varchar(254) NOT NULL default ''"
        ),
        'repository' => array
        (
            'inputType'               => 'text',
            'eval'                    => array('mandatory'=>false, 'unique'=>false, 'decodeEntities'=>true, 'maxlength'=>128, 'tl_class'=>'long'),
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
        'icon' => array
        (
            'inputType'               => 'text',
            'eval'                    => array('mandatory'=>false, 'unique'=>false, 'decodeEntities'=>true, 'maxlength'=>254, 'tl_class'=>'long'),
            'sql'                     => "varchar(254) NOT NULL default ''"
        ),
        'withSettings' => array
        (
            'inputType'               => 'checkbox',
            'default'                 => '0',
            'sql'                     => "char(1) NOT NULL default '0'"
        )
        ,
        'showBundle' => array
        (
            'inputType'               => 'checkbox',
            'default'                 => '0',
            'filter'                  => true,
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
     * @var array
     */
    private $installedPackages = [];

    /**
     * @var array
     */
    private $versions = [];

	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
        $this->versionProvider = new C4GVersionProvider();
		$this->import('Contao\BackendUser', 'User');

		$iconPath = 'bundles/con4giscore/images/be-icons/';

        $this->bundles = [
            'core' => [
                'repo' => 'CoreBundle',
                'description' => $GLOBALS['TL_LANG']['tl_c4g_bricks']['core'],
                'icon' => $iconPath.'core_c4g.svg'
            ],
            'documents' => [
                'repo' => 'DocumentsBundle',
                'description' => $GLOBALS['TL_LANG']['tl_c4g_bricks']['documents'],
                'icon' => $iconPath.'documents_c4g.svg'
            ],
            'editor' => [
                'repo' => 'EditorBundle',
                'description' => $GLOBALS['TL_LANG']['tl_c4g_bricks']['editor'],
                'icon' => $iconPath.'editor_c4g.svg'
            ],
            'export' => [
                'repo' => 'ExportBundle',
                'description' => $GLOBALS['TL_LANG']['tl_c4g_bricks']['export'],
                'icon' => $iconPath.'export_c4g.svg'
            ],
            'firefighter' => [
                'repo' => 'FirefighterBundle',
                'description' => $GLOBALS['TL_LANG']['tl_c4g_bricks']['firefighter'],
                'icon' => $iconPath.'firefighter_c4g.svg'
            ],
            'forum' => [
                'repo' => 'ForumBundle',
                'description' => $GLOBALS['TL_LANG']['tl_c4g_bricks']['forum'],
                'icon' => $iconPath.'forum_c4g.svg'
            ],
            'groups' => [
                'repo' => 'GroupsBundle',
                'description' => $GLOBALS['TL_LANG']['tl_c4g_bricks']['groups'],
                'icon' => $iconPath.'groups_c4g.svg'
            ],
            'import' => [
                'repo' => 'ImportBundle',
                'description' => $GLOBALS['TL_LANG']['tl_c4g_bricks']['import'],
                'icon' => $iconPath.'import_c4g.svg'
            ],
            'maps' => [
                'repo' => 'MapsBundle',
                'description' => $GLOBALS['TL_LANG']['tl_c4g_bricks']['maps'],
                'icon' => $iconPath.'maps_c4g.svg'
            ],
            'map-content' => [
                'repo' => 'MapContentBundle',
                'description' => $GLOBALS['TL_LANG']['tl_c4g_bricks']['mapContent'],
                'icon' => $iconPath.'maps_c4g.svg'
            ],
            'routing' => [
                'repo' => 'RoutingBundle',
                'description' => $GLOBALS['TL_LANG']['tl_c4g_bricks']['routing'],
                'icon' => $iconPath.'routing_c4g.svg'
            ],
            'projects' => [
                'repo' => 'ProjectsBundle',
                'description' => $GLOBALS['TL_LANG']['tl_c4g_bricks']['projects'],
                'icon' => $iconPath.'projects_c4g.svg'
            ],
            'pwa' => [
                'repo' => 'PwaBundle',
                'description' => $GLOBALS['TL_LANG']['tl_c4g_bricks']['pwa'],
                'icon' => $iconPath.'pwa_c4g.svg'
            ],
            'queue' => [
                'repo' => 'QueueBundle',
                'description' => $GLOBALS['TL_LANG']['tl_c4g_bricks']['queue'],
                'icon' => $iconPath.'queue_c4g.svg'
            ],
            'tracking' => [
                'repo' => 'TrackingBundle',
                'description' => $GLOBALS['TL_LANG']['tl_c4g_bricks']['tracking'],
                'icon' => $iconPath.'tracking_c4g.svg'
            ],
            'visualization' => [
                'repo' => 'VisualizationBundle',
                'description' => $GLOBALS['TL_LANG']['tl_c4g_bricks']['visualization'],
                'icon' => $iconPath.'visualization_c4g.svg'
            ],
            'io-travel-costs' => [
                'repo' => 'IOTravelCostsBundle',
                'description' => $GLOBALS['TL_LANG']['tl_c4g_bricks']['io-travel-costs'],
                'icon' => $iconPath.'io-travel-costs_c4g.svg'
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

    private function compareVersions($iv, $lv) {
	    if (($iv != 0) && ($lv != 0)) {
            $ivArr = explode('.',$iv);
            $lvArr = explode('.',$lv);

            if ($lvArr[0] > $ivArr[0]) {
                return true;
            } else if ($lvArr[0] == $ivArr[0]) {
                if ($lvArr[1] > $ivArr[1]) {
                    return true;
                } else if ($lvArr[1] == $lvArr[1]) {
                    if ($lvArr[2] > $ivArr[2]) {
                        return true;
                    }
                }
            }

        }

	    return false;
    }

	/**
	 * load brick versions
	 */
	private function loadBricks($dc, $getPackages = true)
	{
        $bricks = Database::getInstance()->execute("SELECT * FROM tl_c4g_bricks LIMIT 1")->fetchAllAssoc();

        $bundles = $this->bundles;

        if (!$dc || !$bricks || $getPackages) {
            $this->installedPackages = $this->getContainer()->getParameter('kernel.packages');
            $this->versions = $this->getLatestVersions();
        }

        if (!$dc || !$bricks) {
            $this->Database->prepare("DELETE FROM tl_c4g_bricks")->execute();

            //get official packages
            foreach ($bundles as $bundle => $values) {
                if ($this->installedPackages['con4gis/'.$bundle]) {
                    $installedVersion = $this->installedPackages['con4gis/'.$bundle];
                    $latestVersion = $this->versions['con4gis/'.$bundle];

                    $iv = (strpos($installedVersion,'v') == 0) ? substr($installedVersion, 1) : 0;
                    $lv = (strpos($latestVersion,'v') == 0) ? substr($latestVersion, 1) : 0;
                    if ($this->compareVersions($iv, $lv)) {
                        $latestVersion = '<b>'.$latestVersion.'</b>';
                    }
                } else {
                    $installedVersion = '';
                    $latestVersion    = $this->versions['con4gis/'.$bundle];
                }

                $set['tstamp'] = date();
                $set['brickkey'] = $bundle;
                $set['brickname'] = $values['icon'] ? Image::getHtml($values['icon']) : $bundle;
                $set['repository'] = $values['repo'];
                $set['description'] = $values['description'];
                $set['installedVersion'] = $installedVersion;
                $set['latestVersion'] = $latestVersion;
                $set['withSettings'] = intval($this->checkSettings($bundle));
                $set['icon'] = $values['icon'];
                $set['showBundle'] = $installedVersion != '' ? "1" : "0";

                $this->Database->prepare("INSERT INTO tl_c4g_bricks %s")->set($set)->execute();
            }

            //get develop packages
            foreach ($this->installedPackages as $vendorBundle=>$version) {
                if ((substr($vendorBundle,0,7) == 'con4gis') && (!$this->versions[$vendorBundle])) {
                    $bundle = substr($vendorBundle,8);
                    $installedVersion = $version;

                    $set['tstamp'] = date();
                    $set['brickkey'] = $bundle;
                    $set['brickname'] = $bundle;
                    $set['repository'] = '-';
                    $set['description'] = '-';
                    $set['installedVersion'] = $installedVersion;
                    $set['latestVersion']    = '-';
                    $set['withSettings'] = intval($this->checkSettings($bundle));
                    $set['showBundle'] = "1";

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
        $key = Contao\Input::get('key');
        if ($key) {
            switch ($key) {
                case 'switchAll':
                    $GLOBALS['TL_DCA']['tl_c4g_bricks']['list']['sorting']['filter'] = [];
                    $label = $GLOBALS['TL_LANG']['tl_c4g_bricks']['switchInstalled'][0];
                    $icon  = 'bundles/con4giscore/images/be-icons/show_install_passiv.svg';

                    $GLOBALS['TL_DCA']['tl_c4g_bricks']['list']['global_operations']['switchInstalled']['label'] = $label;
                    $GLOBALS['TL_DCA']['tl_c4g_bricks']['list']['global_operations']['switchInstalled']['icon'] = $icon;
                    break;
                case 'switchInstalled':
                    $GLOBALS['TL_DCA']['tl_c4g_bricks']['list']['sorting']['filter']['showBundle'] = ["showBundle = ?", "1"];
                    $label = $GLOBALS['TL_LANG']['tl_c4g_bricks']['switchInstalledAll'][0];
                    $icon  = 'bundles/con4giscore/images/be-icons/show_install_aktiv.svg';

                    $GLOBALS['TL_DCA']['tl_c4g_bricks']['list']['global_operations']['switchInstalled']['label'] = $label;
                    $GLOBALS['TL_DCA']['tl_c4g_bricks']['list']['global_operations']['switchInstalled']['icon'] = $icon;
                    break;
                case 'reloadVersions':
                    $this->loadBricks(false);
                    break;
            }
        } else {
            $this->loadBricks($dc, false);
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
     * switchInstalled
     * @param $href
     * @param $label
     * @param $title
     * @param $class
     * @param $attributes
     * @return string
     */
    public function switchInstalled($href, $label, $title, $class, $attributes)
    {
        $rt = Input::get('rt');
        $do = Input::get('do');

        $actKey = Input::get('key');

        if ($actKey == "switchInstalled") {
            $actKey = 'switchAll';
        } else {
            $actKey = 'switchInstalled';
        }

        $href = "/contao?do=".$do."&key=".$actKey;
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

        $href = "/contao?do=$do&key=reloadVersions";
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
     * loadButton
     * @param $row
     * @param $href
     * @param $label
     * @param $title
     * @param $icon
     * @return string
     */
    public function loadButton($row, $href, $label, $title, $icon) {
        $rt = Input::get('rt');

        //ToDo set configuration params in bundles

        if (strpos($href, 'firstButton') > 0) {
            if ($row['installedVersion'] && $row['withSettings'] && ($row['brickkey'] != 'core')) {
                $href = '/contao?do=c4g_'.$row['brickkey'].'_configuration&rt='.$rt.'&'.$href;
                $title = $GLOBALS['TL_LANG']['MOD']['c4g_'.$row['brickkey'].'_configuration'][0];

                switch ($row['brickkey']) {
                    case "routing":
                        $icon = 'bundles/con4gisrouting/images/be-icons/routingconfig.svg';
                        break;
                    case "pwa":
                        $icon = 'bundles/con4gispwa/images/be-icons/pwa_config.svg';
                        break;
                    case "editor":
                        $icon = 'bundles/con4giseditor/images/be-icons/editor_config.svg';
                        break;
                }
            } else if ($row['installedVersion']) {
                switch ($row['brickkey']) {
                    case "maps":
                        $href = '/contao?do=c4g_map_baselayers&rt='.$rt.'&key='.$row['brickkey'];
                        $icon = 'bundles/con4gismaps/images/be-icons/Basiskarte_2_16.svg';
                        $title = $GLOBALS['TL_LANG']['MOD']['c4g_map_baselayers'][0];
                        break;
                    case "map-content":
                        $href = '/contao?do=c4g_mapcontent_custom_field&rt='.$rt.'&key='.$row['brickkey'];
                        $icon = 'bundles/con4gismaps/images/be-icons/Basiskarte_2_16.svg';
                        $title = $GLOBALS['TL_LANG']['MOD']['c4g_mapcontent_custom_field'][0];
                        break;
                    case "tracking":
                        $href = '/contao?do=c4g_'.$row['brickkey'].'&rt='.$rt.'&key='.$row['brickkey'];
                        $icon = 'bundles/con4gistracking/images/be-icons/trackingconfig.svg';
                        $title = $GLOBALS['TL_LANG']['MOD']['c4g_'.$row['brickkey']][0];
                        break;
                    case "import":
                    case "export":
                    case "queue":
                        $href = '/contao?do=c4g_'.$row['brickkey'].'&rt='.$rt.'&key='.$row['brickkey'];
                        $title = $GLOBALS['TL_LANG']['MOD']['c4g_'.$row['brickkey']][0];
                        break;
//                    case "pwa":
//                        $href = '/contao?do=c4g_pwa_configuration&rt='.$rt.'&key='.$row['brickkey'];
//                        $icon = 'bundles/con4gismaps/images/be-icons/baselayers.png';
//                        break;
                    case "io-travel-costs":
                        $href = '/contao?do=c4g_travel_costs_tariffs&rt='.$rt.'&key='.$row['brickkey'];
                        $icon = 'tablewizard.svg';
                        $title = $GLOBALS['TL_LANG']['MOD']['c4g_travel_costs_tariffs'][0];
                        break;
                    case "visualization":
                        $href = '/contao?do=c4g_visualization_chart_element&rt='.$rt.'&key='.$row['brickkey'];
                        $icon = 'bundles/con4gisvisualization/images/be-icons/charts.svg';
                        $title = $GLOBALS['TL_LANG']['MOD']['c4g_visualization_chart_element'][0];
                        break;
                    case "firefighter":
                        $href = '/contao?do=c4g_firefighter_operation_types&rt='.$rt.'&key='.$row['brickkey'];
                        $icon = 'bundles/con4gismaps/images/be-icons/baselayers.png';
                        $title = $GLOBALS['TL_LANG']['MOD']['c4g_firefighter_operation_types'][0];
                        break;
                    case "forum":
                        $href = '/contao?do=c4g_forum&rt='.$rt.'&key='.$row['brickkey'];
                        $icon = 'bundles/con4gismaps/images/be-icons/baselayers.png';
                        $title = $GLOBALS['TL_LANG']['MOD']['c4g_forum'][0];
                        break;
                    default:
                        return;// Contao\Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)) . ' ';
                }
            } else {
                return;
            }
        } else if (strpos($href, 'secondButton') > 0) {
            if ($row['installedVersion']) {
                switch ($row['brickkey']) {
                    case "maps":
                        $href = '/contao?do=c4g_map_locstyles&rt=' . $rt . '&key=' . $row['brickkey'];
                        $icon = 'bundles/con4gismaps/images/be-icons/Lokationsstile_16.svg';
                        $title = $GLOBALS['TL_LANG']['MOD']['c4g_map_locstyles'][0];
                        break;
                    case "map-content":
                        $href = '/contao?do=c4g_mapcontent_element&rt=' . $rt . '&key=' . $row['brickkey'];
                        $icon = 'bundles/con4gismaps/images/be-icons/Lokationsstile_16.svg';
                        $title = $GLOBALS['TL_LANG']['MOD']['c4g_mapcontent_element'][0];
                        break;
                    case "editor":
                        $href = '/contao?do=c4g_editor_element_category&rt=' . $rt . '&key=' . $row['brickkey'];
                        $icon = 'bundles/con4giseditor/images/be-icons/editor_category.svg';
                        $title = $GLOBALS['TL_LANG']['MOD']['c4g_editor_element_category'][0];
                        break;
                    case "pwa":
                        $href = '/contao?do=c4g_webpush_configuration&rt=' . $rt . '&key=' . $row['brickkey'];
                        $icon = 'bundles/con4gispwa/images/be-icons/webpush_config.svg';
                        $title = $GLOBALS['TL_LANG']['MOD']['c4g_webpush_configuration'][0];
                        break;
                    case "io-travel-costs":
                        $href = '/contao?do=c4g_travel_costs_settings&rt=' . $rt . '&key=' . $row['brickkey'];
                        $title = $GLOBALS['TL_LANG']['MOD']['c4g_travel_costs_settings'][0];
                        break;
                    case "visualization":
                        $href = '/contao?do=c4g_visualization_chart&rt=' . $rt . '&key=' . $row['brickkey'];
                        $icon = 'bundles/con4gisvisualization/images/be-icons/grafic.svg';
                        $title = $GLOBALS['TL_LANG']['MOD']['c4g_visualization_chart'][0];
                        break;
                    case "firefighter":
                        $href = '/contao?do=c4g_firefighter_operation_categories&rt=' . $rt . '&key=' . $row['brickkey'];
                        $icon = 'bundles/con4gismaps/images/be-icons/locstyles.png';
                        $title = $GLOBALS['TL_LANG']['MOD']['c4g_firefighter_operation_categories'][0];
                        break;
                    case "forum":
                        $href = '/contao?do=c4g_forum_thread&rt=' . $rt . '&key=' . $row['brickkey'];
                        $icon = 'bundles/con4gismaps/images/be-icons/locstyles.png';
                        $title = $GLOBALS['TL_LANG']['MOD']['c4g_forum_thread'][0];
                        break;
                    default:
                        return;
                }
            }
        }  else if (strpos($href, 'thirdButton') > 0) {
                if ($row['installedVersion']) {
                    switch ($row['brickkey']) {
                        case "maps":
                            $href = '/contao?do=c4g_map_themes&rt='.$rt.'&key='.$row['brickkey'];
                            $icon = 'bundles/con4gismaps/images/be-icons/kartenlayout_16.svg';
                            $title = $GLOBALS['TL_LANG']['MOD']['c4g_map_themes'][0];
                            break;
                        case "map-content":
                            $href = '/contao?do=c4g_mapcontent_type&rt='.$rt.'&key='.$row['brickkey'];
                            $icon = 'bundles/con4gismaps/images/be-icons/kartenlayout_16.svg';
                            $title = $GLOBALS['TL_LANG']['MOD']['c4g_mapcontent_type'][0];
                            break;
                        case "editor":
                            $href = '/contao?do=c4g_editor_element_type&rt='.$rt.'&key='.$row['brickkey'];
                            $icon = 'bundles/con4giseditor/images/be-icons/editor_type.svg';
                            $title = $GLOBALS['TL_LANG']['MOD']['c4g_editor_element_type'][0];
                            break;
                        case "pwa":
                            $href = '/contao?do=c4g_push_notification&rt='.$rt.'&key='.$row['brickkey'];
                            $icon = 'bundles/con4gispwa/images/be-icons/push_example.svg';
                            $title = $GLOBALS['TL_LANG']['MOD']['c4g_push_notification'][0];
                            break;
                        case "firefighter":
                            $href = '/contao?do=c4g_firefighter_vehicle_types&rt='.$rt.'&key='.$row['brickkey'];
                            $icon = 'bundles/con4gismaps/images/be-icons/themes.png';
                            $title = $GLOBALS['TL_LANG']['MOD']['c4g_firefighter_vehicle_types'][0];
                            break;
                        default:
                            return;
                    }
                } else {
                    return;
                }
        }  else if (strpos($href, 'fourthButton') > 0) {
            if ($row['installedVersion']) {
                switch ($row['brickkey']) {
                    case "maps":
                        $href = '/contao?do=c4g_map_profiles&rt='.$rt.'&key='.$row['brickkey'];
                        $icon = 'bundles/con4gismaps/images/be-icons/map_profile.svg';
                        $title = $GLOBALS['TL_LANG']['MOD']['c4g_map_profiles'][0];
                        break;
                    case "map-content":
                        $href = '/contao?do=c4g_mapcontent_directory&rt='.$rt.'&key='.$row['brickkey'];
                        $icon = 'bundles/con4gismaps/images/be-icons/map_profile.svg';
                        $title = $GLOBALS['TL_LANG']['MOD']['c4g_mapcontent_directory'][0];
                        break;
                    case "pwa":
                        $href = '/contao?do=c4g_push_subscription_type&rt='.$rt.'&key='.$row['brickkey'];
                        $icon = 'bundles/con4gismaps/images/be-icons/profiles.png';
                        $title = $GLOBALS['TL_LANG']['MOD']['c4g_push_subscription_type'][0];
                        break;
                    case "firefighter":
                        $href = '/contao?do=c4g_firefighter_vehicles&rt='.$rt.'&key='.$row['brickkey'];
                        $icon = 'bundles/con4gismaps/images/be-icons/profiles.png';
                        $title = $GLOBALS['TL_LANG']['MOD']['c4g_firefighter_vehicles'][0];
                        break;
                    default:
                        return;
                }
            } else {
                return;
            }
        }  else if (strpos($href, 'fifthButton') > 0) {
            if ($row['installedVersion']) {
                switch ($row['brickkey']) {
                    case "maps":
                        $href = '/contao?do=c4g_maps&rt='.$rt.'&key='.$row['brickkey'];
                        $icon = 'bundles/con4gismaps/images/be-icons/kartenstruktur_16.png';
                        $title = $GLOBALS['TL_LANG']['MOD']['c4g_maps'][0];
                        break;
                    case "firefighter":
                        $href = '/contao?do=c4g_firefighter_unit_types&rt='.$rt.'&key='.$row['brickkey'];
                        $icon = 'bundles/con4gismaps/images/be-icons/map.png';
                        $title = $GLOBALS['TL_LANG']['MOD']['c4g_firefighter_unit_types'][0];
                        break;
                    default:
                        return;
                }
            } else {
                return;
            }
        }  else if (strpos($href, 'sixthButton') > 0) {
            if ($row['installedVersion']) {
                switch ($row['brickkey']) {
                    case "maps":
                        $href = '/contao?do=c4g_map_tables&rt='.$rt.'&key='.$row['brickkey'];
                        $icon = 'bundles/con4gismaps/images/be-icons/source_tables.svg';
                        $title = $GLOBALS['TL_LANG']['MOD']['c4g_map_tables'][0];
                        break;
                    case "firefighter":
                        $href = '/contao?do=c4g_firefighter_units&rt='.$rt.'&key='.$row['brickkey'];
                        $icon = 'bundles/con4gismaps/images/be-icons/map_location.png';
                        $title = $GLOBALS['TL_LANG']['MOD']['c4g_firefighter_units'][0];
                        break;
                    default:
                        return;
                }
            } else {
                return;
            }
        } else if (strpos($href, 'seventhButton') > 0) {
            if ($row['installedVersion']) {
                switch ($row['brickkey']) {
                    case "maps":
                        $href = '/contao?do=c4g_map_filters&rt='.$rt.'&key='.$row['brickkey'];
                        $icon = 'bundles/con4gismaps/images/be-icons/mapfolder.png';
                        $title = $GLOBALS['TL_LANG']['MOD']['c4g_map_filters'][0];
                        break;
                    case "firefighter":
                        $href = '/contao?do=c4g_firefighter_operations&rt='.$rt.'&key='.$row['brickkey'];
                        $icon = 'bundles/con4gismaps/images/be-icons/mapfolder.png';
                        $title = $GLOBALS['TL_LANG']['MOD']['c4g_firefighter_operations'][0];
                        break;
                    default:
                        return;
                }
            } else {
                return;
            }
        } else if (strpos($href, 'eighthButton') > 0) {
            if ($row['installedVersion']) {
                switch ($row['brickkey']) {
                    case "maps":
                        $href = '/contao?do=c4g_maps&rt='.$rt.'&key='.$row['brickkey'];
                        $icon = 'bundles/con4gismaps/images/be-icons/kartenstruktur_16.svg';
                        $title = $GLOBALS['TL_LANG']['MOD']['c4g_maps'][0];
                        break;
                    default:
                        return;
                }
            } else {
                return;
            }
        } else {
            return;
        }
        $attributes = 'style="margin-right:3px"';

        //ToDo check permissions
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
        return '<a href="https://github.com/Kuestenschmiede/'.$row['repository'].'" title="'.specialchars($title).'" target="_blank" rel="noopener">'.Image::getHtml($icon, $label).'</a>';
    }
}
