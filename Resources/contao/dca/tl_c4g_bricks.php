<?php

/*
 * This file is part of Contao.
 *
 * (c) Leo Feyer
 *
 * @license LGPL-3.0-or-later
 */

use con4gis\CoreBundle\Classes\C4GVersionProvider;
use con4gis\CoreBundle\Classes\Helper\ArrayHelper;
use con4gis\CoreBundle\Resources\contao\models\C4gLogModel;
use Contao\Config;
use Contao\Date;
use Contao\Image;
use Contao\StringUtil;

$GLOBALS['TL_DCA']['tl_c4g_bricks'] = array
(
	// Config
	'config' => array
	(
		'dataContainer'    => 'Table',
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
		'onload_callback' => [['tl_c4g_bricks', 'checkButtons']],
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
                'button_callback'     => ['tl_c4g_bricks', 'con4gisVersion']
            ),
            'con4gisOrg' => array
            (
                'href'                => 'key=con4gisOrg',
                'class'               => 'header_con4gis_org',
                'button_callback'     => ['tl_c4g_bricks', 'con4gisOrg'],
                'icon'                => 'bundles/con4giscore/images/be-icons/con4gis.org_dark.svg',
                'label'               => 'con4gis.org'
            ),
            'con4gisIO' => array
            (
                'href'                => 'key=con4gisIO',
                'class'               => 'header_con4gis_io',
                'button_callback'     => ['tl_c4g_bricks', 'con4gisIO'],
                'icon'                => 'bundles/con4giscore/images/be-icons/con4gis.io.svg',
                'label'               => 'con4gis.io'
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
                'href'                => 'key=switchAll',
                'class'               => 'header_switch_installed',
                'button_callback'     => ['tl_c4g_bricks', 'switchInstalled'],
                'icon'                => 'bundles/con4giscore/images/be-icons/visible.svg',
                'label'               => $GLOBALS['TL_LANG']['tl_c4g_bricks']['switchInstalledAll'][0]
            ),
            'reloadVersions' => array
            (
                'href'                => 'key=reloadVersions',
                'class'               => 'header_reload_versions',
                'button_callback'     => ['tl_c4g_bricks', 'reloadVersions'],
                'icon'                => 'bundles/con4giscore/images/be-icons/update_version.svg'
            ),
            'importData' => array
            (
                'href'                => 'key=importData',
                'class'               => 'header_import_data',
                'button_callback'     => ['tl_c4g_bricks', 'importData'],
                'icon'                => 'bundles/con4giscore/images/be-icons/importData.svg'
            ),
            'serverLogs' => array
            (
                'href'                => 'key=serverLogs',
                'class'               => 'header_server_logs',
                'button_callback'     => ['tl_c4g_bricks', 'serverLogs'],
                'icon'                => 'bundles/con4giscore/images/be-icons/serverlog.svg'
            )
		),
		'operations' => array
		(
            'firstButton' => array
            (
                'href'                => 'key=firstButton',
                'icon'                => 'bundles/con4giscore/images/be-icons/edit.svg',
                'button_callback'     => ['tl_c4g_bricks', 'loadButton']
            ),
            'secondButton' => array
            (
                'href'                => 'key=secondButton',
                'icon'                => 'bundles/con4giscore/images/be-icons/edit.svg',
                'button_callback'     => ['tl_c4g_bricks', 'loadButton']
            ),
            'thirdButton' => array
            (
                'href'                => 'key=thirdButton',
                'icon'                => 'bundles/con4giscore/images/be-icons/edit.svg',
                'button_callback'     => ['tl_c4g_bricks', 'loadButton']
            ),
            'fourthButton' => array
            (
                'href'                => 'key=fourthButton',
                'icon'                => 'bundles/con4giscore/images/be-icons/edit.svg',
                'button_callback'     => ['tl_c4g_bricks', 'loadButton']
            ),
            'fifthButton' => array
            (
                'href'                => 'key=fifthButton',
                'icon'                => 'bundles/con4giscore/images/be-icons/edit.svg',
                'button_callback'     => ['tl_c4g_bricks', 'loadButton']
            ),
            'sixthButton' => array
            (
                'href'                => 'key=sixthButton',
                'icon'                => 'bundles/con4giscore/images/be-icons/edit.svg',
                'button_callback'     => ['tl_c4g_bricks', 'loadButton']
            ),
            'seventhButton' => array
            (
                'href'                => 'key=seventhButton',
                'icon'                => 'bundles/con4giscore/images/be-icons/edit.svg',
                'button_callback'     => ['tl_c4g_bricks', 'loadButton']
            ),
            'eighthButton' => array
            (
                'href'                => 'key=eighthButton',
                'icon'                => 'bundles/con4giscore/images/be-icons/edit.svg',
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
            'favorite' => array
            (
                'href'                => 'key=switchFavorite',
                'icon'                => 'bundles/con4giscore/images/be-icons/star.svg',
                'button_callback'     => ['tl_c4g_bricks', 'switchFavorite']
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
    private $versions = [];

	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
        $this->versionProvider = new C4GVersionProvider();
		$this->import('BackendUser', 'User');
        //$this->User->authenticate();

        $iconPath = 'bundles/con4giscore/images/be-icons/';

        $this->bundles = [
            'core' => [
                'repo' => 'CoreBundle',
                'description' => $GLOBALS['TL_LANG']['tl_c4g_bricks']['core'],
                'icon' => $iconPath.'core_c4g.svg'
            ],
            'data' => [
                'repo' => 'DataBundle',
                'description' => $GLOBALS['TL_LANG']['tl_c4g_bricks']['data'],
                'icon' => $iconPath.'data_c4g.svg'
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
	    $userid = $this->User->id;
	    $showBundle = '1';
        $bricks = Database::getInstance()->execute("SELECT * FROM tl_c4g_bricks WHERE pid=$userid AND showBundle=$showBundle")->fetchAllAssoc();
        if ($bricks && $bricks[0]) {
            $tstamp = intval($bricks[0]['tstamp']);
            $before_seven_days = time() - (7 * 24 * 60 * 60);
            $renewData = $tstamp < $before_seven_days ? true : false; //autom. renew after one week
        }

        $bundles = $this->bundles;

        if ($renewData || !$dc || !$bricks || $getPackages) {
            $installedPackages = $this->getContainer()->getParameter('kernel.packages');
            $this->versions = $this->getLatestVersions();
        }

        if ($renewData || !$dc || !$bricks) {

            $favorites = [];
            if ($bricks) {
                foreach ($bricks as $brick) {
                    $favorites[$brick['brickkey']] = $brick['favorite'];
                }
            }

            $this->Database->prepare("DELETE FROM tl_c4g_bricks WHERE pid=?")->execute($this->User->id);

            //get official packages
            foreach ($bundles as $bundle => $values) {
                if ($installedPackages['con4gis/'.$bundle]) {
                    $installedVersion = $installedPackages['con4gis/'.$bundle];
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

//                if ($installedVersion == '9999999-dev') {
//                   $installedVersion = 'dev';
//                }

                $set['tstamp'] = time();
                $set['pid'] = $this->User->id;
                $set['brickkey'] = $bundle;
                $set['brickname'] = $values['icon'] ? Image::getHtml($values['icon']) : $bundle;
                $set['repository'] = $values['repo'];
                $set['description'] = $values['description'];
                $set['installedVersion'] = $installedVersion;
                $set['latestVersion'] = $latestVersion;
                $set['withSettings'] = intval($this->checkSettings($bundle));
                $set['icon'] = $values['icon'];
                $set['showBundle'] = $installedVersion != '' ? "1" : "0";
                $set['favorite'] = $favorites[$bundle] ? $favorites[$bundle] : '0';

                $this->Database->prepare("INSERT INTO tl_c4g_bricks %s")->set($set)->execute();
            }

            //get develop packages
            foreach ($installedPackages as $vendorBundle=>$version) {
                if ((substr($vendorBundle,0,7) == 'con4gis') && (!$this->versions[$vendorBundle])) {
                    $bundle = substr($vendorBundle,8);

                    if ($bundles[$bundle]) {
                        continue;
                    }

                    $installedVersion = $version;

                    $set['tstamp'] = time();
                    $set['pid'] = $this->User->id;
                    $set['brickkey'] = $bundle;
                    $set['brickname'] = $bundle;
                    $set['repository'] = '-';
                    $set['description'] = '-';
                    $set['installedVersion'] = $installedVersion;
                    $set['latestVersion']    = '-';
                    $set['withSettings'] = intval($this->checkSettings($bundle));
                    $set['showBundle'] = "1";
                    $set['favorite'] = $favorites[$bundle] ? $favorites[$bundle] : '0';

                    $this->Database->prepare("INSERT INTO tl_c4g_bricks %s")->set($set)->execute();
                }
            }
        }

        return $bricks;
    }


    /**
     * checkButtons
     */
    public function checkButtons(Contao\DataContainer $dc)
    {
        $GLOBALS['TL_DCA']['tl_c4g_bricks']['list']['sorting']['filter'] = [];
        $GLOBALS['TL_DCA']['tl_c4g_bricks']['list']['sorting']['filter']['pid'] = array('pid = ?', $this->User->id);

        // Check current action
        $key = Contao\Input::get('key');
        $bricks = [];
        if ($key) {
            $switchKey = $key;
            $pos = strpos($key,'_');
            if ($pos) {
                $switchKey = substr($key, 0, $pos);
                $keyValue  = substr($key, $pos+1);
            }
            $deleteKey = true;

            switch ($switchKey) {
                case 'switchAll':
                    $label = $GLOBALS['TL_LANG']['tl_c4g_bricks']['switchInstalled'][0];
                    $icon  = 'bundles/con4giscore/images/be-icons/invisible.svg';

                    $GLOBALS['TL_DCA']['tl_c4g_bricks']['list']['global_operations']['switchInstalled']['label'] = $label;
                    $GLOBALS['TL_DCA']['tl_c4g_bricks']['list']['global_operations']['switchInstalled']['icon'] = $icon;
                    $deleteKey = false;
                    break;
                case 'switchInstalled':
                    $GLOBALS['TL_DCA']['tl_c4g_bricks']['list']['sorting']['filter']['showBundle'] = ["showBundle = ?", "1"];
                    $label = $GLOBALS['TL_LANG']['tl_c4g_bricks']['switchInstalledAll'][0];
                    $icon  = 'bundles/con4giscore/images/be-icons/visible.svg';

                    $GLOBALS['TL_DCA']['tl_c4g_bricks']['list']['global_operations']['switchInstalled']['label'] = $label;
                    $GLOBALS['TL_DCA']['tl_c4g_bricks']['list']['global_operations']['switchInstalled']['icon'] = $icon;
                    $deleteKey = false;
                    break;
                case 'reloadVersions':
                    $bricks = $this->loadBricks(false);
                    break;
                case 'switchFavorite':
                    if ($keyValue) {
                        $result = Database::getInstance()->prepare("SELECT favorite FROM tl_c4g_bricks WHERE brickkey=? AND pid=? LIMIT 1")->execute($keyValue, $this->User->id)->fetchAssoc();
                        if ($result) {
                            $favorite = $result['favorite'] == '1' ? '0' : '1';
                            Database::getInstance()->prepare("UPDATE tl_c4g_bricks SET favorite=? WHERE brickkey=? AND pid=?")->execute($favorite,$keyValue, $this->User->id);
                        }
                    }
                    break;
            }

            if ($deleteKey) {
                //delete key per redirect
                \Contao\Controller::redirect(str_replace('&key='.$key, '', \Environment::get('request')));
            }

        } else {
            $GLOBALS['TL_DCA']['tl_c4g_bricks']['list']['sorting']['filter']['showBundle'] = ["showBundle = ?", "1"];
            $bricks = $this->loadBricks($dc, false);
        }

        //ToDo
        //\Contao\Message::addInfo($GLOBALS['TL_LANG']['tl_c4g_bricks']['infotext']);
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
        return $this->User->hasAccess('c4g_settings', 'modules') ? '<a href="' . $href . '" class="' . $class . '" title="' . StringUtil::specialchars($title) . '"' . $attributes . '>' . $label . '</a> ' : Contao\Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)) . ' ';
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

        if ($actKey == "switchAll") {
            $actKey = 'switchInstalled';
        } else {
            $actKey = 'switchAll';
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
     * importData
     * @param $href
     * @param $label
     * @param $title
     * @param $class
     * @param $attributes
     * @return string
     */
    public function importData($href, $label, $title, $class, $attributes)
    {
        $rt = Input::get('rt');
        $href = "/contao?do=c4g_io_data&rt=$rt&key=importData";
        return $this->User->hasAccess('c4g_io_data', 'modules') ?  '<a href="' . $href . '" class="' . $class . '" title="' . StringUtil::specialchars($title) . '"' . $attributes . '>' . $label . '</a> ' : Contao\Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)) . ' ';
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
        return $this->User->hasAccess('c4g_log', 'modules') ? '<a href="' . $href . '" class="' . $class . '" title="' . StringUtil::specialchars($title) . '"' . $attributes . '>' . $label . '</a> ' : Contao\Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)) . ' ';
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
        return '<a href="https://con4gis.io"  class="' . $class . '" title="' . StringUtil::specialchars($title) . '"' . $attributes .' target="_blank" rel="noopener">' . $label . '</a><br>';
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
        //$icon = 'bundles/con4giscore/images/be-icons/con4gis-logo.svg';
        $userid = $this->User->id;
        $bricks = Database::getInstance()->execute("SELECT * FROM tl_c4g_bricks WHERE pid=$userid LIMIT 1")->fetchAllAssoc();
        $date = '';
        if ($bricks && $bricks[0] && ($bricks[0]['tstamp'] > 0)) {
            $date = Date::parse(Config::get('dateFormat'), $bricks[0]['tstamp']);
        }
        $label = $GLOBALS['TL_LANG']['tl_c4g_bricks']['lastLoading'].$date;
        return '<div class="con4gis_version" style="text-align: left;color: #0f3b5c;">'.$label.'</div>';
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
        $key = $row['brickkey'];

        $brickArr = [];
        foreach ($GLOBALS['BE_MOD']['con4gis'] as $key=>$module) {
            if ($module['brick']) {
                $brickArr[$module['brick']][] = ['do' => $key, 'icon' => $module['icon'], 'title' => $GLOBALS['TL_LANG']['MOD'][$key][1]];
            }
        }

        if ((strpos($href, 'firstButton') > 0) && ($brickArr[$row['brickkey']][0])) {
            $do = $brickArr[$row['brickkey']][0]['do'];
            $icon = $brickArr[$row['brickkey']][0]['icon'];
            $title = $brickArr[$row['brickkey']][0]['title'];
        } else if ((strpos($href, 'secondButton') > 0) && ($brickArr[$row['brickkey']][1])) {
            $do = $brickArr[$row['brickkey']][1]['do'];
            $icon = $brickArr[$row['brickkey']][1]['icon'];
            $title = $brickArr[$row['brickkey']][1]['title'];
        } else if ((strpos($href, 'thirdButton') > 0) && ($brickArr[$row['brickkey']][2])) {
            $do = $brickArr[$row['brickkey']][2]['do'];
            $icon = $brickArr[$row['brickkey']][2]['icon'];
            $title = $brickArr[$row['brickkey']][2]['title'];
        } else if ((strpos($href, 'fourthButton') > 0) && ($brickArr[$row['brickkey']][3])) {
            $do = $brickArr[$row['brickkey']][3]['do'];
            $icon = $brickArr[$row['brickkey']][3]['icon'];
            $title = $brickArr[$row['brickkey']][3]['title'];
        } else if ((strpos($href, 'fifthButton') > 0) && ($brickArr[$row['brickkey']][4])) {
            $do = $brickArr[$row['brickkey']][4]['do'];
            $icon = $brickArr[$row['brickkey']][4]['icon'];
            $title = $brickArr[$row['brickkey']][4]['title'];
        } else if ((strpos($href, 'sixthButton') > 0) && ($brickArr[$row['brickkey']][5])) {
            $do = $brickArr[$row['brickkey']][5]['do'];
            $icon = $brickArr[$row['brickkey']][5]['icon'];
            $title = $brickArr[$row['brickkey']][5]['title'];
        } else if ((strpos($href, 'seventhButton') > 0) && ($brickArr[$row['brickkey']][6])) {
            $do = $brickArr[$row['brickkey']][6]['do'];
            $icon = $brickArr[$row['brickkey']][6]['icon'];
            $title = $brickArr[$row['brickkey']][6]['title'];
        } else if ((strpos($href, 'eighthButton') > 0) && ($brickArr[$row['brickkey']][7])) {
            $do = $brickArr[$row['brickkey']][7]['do'];
            $icon = $brickArr[$row['brickkey']][7]['icon'];
            $title = $brickArr[$row['brickkey']][7]['title'];
        } else {
            return;
        }

        $attributes = 'style="margin-right:3px"';
        $imgAttributes = 'style="width: 18px; height: 18px"';
        $ref = Input::get('ref');
        $href = '/contao/main.php?do='.$do.'&amp;ref='.$ref;

        return $this->User->hasAccess($do, 'modules') ? '<a href="' . $href . '" title="' . StringUtil::specialchars($title) . '"'.$attributes.'>'.Image::getHtml($icon, $label, $imgAttributes).'</a>' : Contao\Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)) . ' ';

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
        $imgAttributes = 'style="width: 18px; height: 18px"';
        return '<a href="https://docs.con4gis.org/con4gis-'.$row['brickkey'].'" title="'.specialchars($title).'" '.$attributes.' target="_blank" rel="noopener">'.Image::getHtml($icon, $label, $imgAttributes).'</a>';
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
        $imgAttributes = 'style="width: 18px; height: 18px"';
        return '<a href="https://packagist.org/packages/con4gis/'.$row['brickkey'].'" title="'.specialchars($title).'" '.$attributes.' target="_blank" rel="noopener">'.Image::getHtml($icon, $label, $imgAttributes).'</a>';
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
        $imgAttributes = 'style="width: 18px; height: 18px"';
        return '<a href="https://github.com/Kuestenschmiede/'.$row['repository'].'" title="'.specialchars($title).'" target="_blank" rel="noopener">'.Image::getHtml($icon, $label, $imgAttributes).'</a>';
    }

    /**
     * @param $row
     * @param $href
     * @param $label
     * @param $title
     * @param $icon
     * @return string
     */
    public function switchFavorite($row, $href, $label, $title, $icon) {
        $rt = Input::get('rt');
        $do = Input::get('do');

        $attributes = 'style="margin-right:3px"';
        $imgAttributes = 'style="width: 18px; height: 18px"';

        $showButton = false;
        foreach ($GLOBALS['BE_MOD']['con4gis'] as $key=>$module) {
            if ($module['brick'] == $row['brickkey']) {
                $showButton = true;
                break;
            }
        }

        if (!$showButton) {
            return '';
        }

        $result = Database::getInstance()->prepare("SELECT favorite FROM tl_c4g_bricks WHERE brickkey=? AND pid=? LIMIT 1")->execute($row['brickkey'], $this->User->id)->fetchAssoc();
        if ($result) {
            if ($result['favorite'] == '1') {
                $icon = 'bundles/con4giscore/images/be-icons/star_light.svg';
            }
        }

        $href = "/contao?do=$do&key=switchFavorite_".$row['brickkey'];
        return '<a href="' . $href . '" title="' . StringUtil::specialchars($title) . '"' . $attributes . '>'.Image::getHtml($icon, $label, $imgAttributes).'</a> ';
    }
}
