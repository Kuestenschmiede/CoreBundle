<?php

/*
 * This file is part of Contao.
 *
 * (c) Leo Feyer
 *
 * @license LGPL-3.0-or-later
 */

use con4gis\CoreBundle\Classes\C4GVersionProvider;
use Contao\Image;
use Contao\StringUtil;

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
            'filter'                  => ['showBundle' => ["showBundle = ?", "1"]]
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
            )/*,
            'eighthButton' => array
            (
                'href'                => 'key=eighthButton',
                'icon'                => 'bundles/con4giscore/images/be-icons/edit.svg',
                'button_callback'     => ['tl_c4g_bricks', 'loadButton']
            )*/,
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

                    if ($bundles[$bundle]) {
                        continue;
                    }

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
                    $icon  = 'bundles/con4giscore/images/be-icons/invisible.svg';

                    $GLOBALS['TL_DCA']['tl_c4g_bricks']['list']['global_operations']['switchInstalled']['label'] = $label;
                    $GLOBALS['TL_DCA']['tl_c4g_bricks']['list']['global_operations']['switchInstalled']['icon'] = $icon;
                    break;
                case 'switchInstalled':
                    $GLOBALS['TL_DCA']['tl_c4g_bricks']['list']['sorting']['filter']['showBundle'] = ["showBundle = ?", "1"];
                    $label = $GLOBALS['TL_LANG']['tl_c4g_bricks']['switchInstalledAll'][0];
                    $icon  = 'bundles/con4giscore/images/be-icons/visible.svg';

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

        \Contao\Message::addInfo($GLOBALS['TL_LANG']['tl_c4g_bricks']['infotext']);
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
        $label = 'con4gis '.$GLOBALS['con4gis']['version'];
        return '<div class="con4gis_version" style="text-align: left;color: #0f3b5c;">'.$label/*Image::getHtml($icon, $label)*/.'</div>';
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

        //ToDo set configuration params in bundles
        if (strpos($href, 'firstButton') > 0) {
            if ($row['installedVersion'] && $row['withSettings'] && ($row['brickkey'] != 'core')) {
                $do = 'c4g_'.$row['brickkey'].'_configuration';
                $title = $GLOBALS['TL_LANG']['MOD']['c4g_'.$row['brickkey'].'_configuration'][0];
            } else if ($row['installedVersion']) {
                switch ($row['brickkey']) {
                    case "maps":
                        $do = 'c4g_map_baselayers';
                        $icon = 'bundles/con4gismaps/images/be-icons/baselayers.svg';
                        $title = $GLOBALS['TL_LANG']['MOD']['c4g_map_baselayers'][0];
                        break;
                    case "map-content":
                        $do = 'c4g_mapcontent_custom_field';
                        //$icon = 'bundles/con4gismapcontent/images/be-icons/customfields.svg';
                        $title = $GLOBALS['TL_LANG']['MOD']['c4g_mapcontent_custom_field'][0];
                        break;
                    case "tracking":
                        $do = 'c4g_'.$row['brickkey'];
                        //$icon = 'bundles/con4gistracking/images/be-icons/trackingconfig.svg';
                        $title = $GLOBALS['TL_LANG']['MOD']['c4g_'.$row['brickkey']][0];
                        break;
                    case "import":
                        $do = 'c4g_'.$row['brickkey'];
                        $title = $GLOBALS['TL_LANG']['MOD']['c4g_'.$row['brickkey']][0];
                        break;
                    case "export":
                        $do = 'c4g_'.$row['brickkey'];
                        $title = $GLOBALS['TL_LANG']['MOD']['c4g_'.$row['brickkey']][0];
                        break;
                    case "queue":
                        $do = 'c4g_'.$row['brickkey'];
                        $title = $GLOBALS['TL_LANG']['MOD']['c4g_'.$row['brickkey']][0];
                        $icon = 'bundles/con4gisqueue/images/be-icons/import_queue_2.svg';
                        break;
//                    case "pwa":
//                        $do = 'c4g_pwa_configuration';
//                        $icon = 'bundles/con4gismaps/images/be-icons/baselayers.png';
//                        break;
                    case "io-travel-costs":
                        $do = 'c4g_travel_costs_tariffs';
                        $icon = 'bundles/con4gisiotravelcosts/images/be-icons/travelcosts_tariff.svg';
                        $title = $GLOBALS['TL_LANG']['MOD']['c4g_travel_costs_tariffs'][0];
                        break;
                    case "visualization":
                        $do = 'c4g_visualization_chart_element';
                        $icon = 'bundles/con4gisvisualization/images/be-icons/charts.svg';
                        $title = $GLOBALS['TL_LANG']['MOD']['c4g_visualization_chart_element'][0];
                        break;
                    case "firefighter":
                        $do = 'c4g_firefighter_operation_types';
                        $icon = 'bundles/con4gisfirefighter/images/be-icons/operationtypes.svg';
                        $title = $GLOBALS['TL_LANG']['MOD']['c4g_firefighter_operation_types'][0];
                        break;
                    case "forum":
                        $do = 'c4g_forum';
                        $icon = 'bundles/con4gisforum/images/be-icons/forumstructure.svg';
                        $title = $GLOBALS['TL_LANG']['MOD']['c4g_forum'][0];
                        break;
                    default:
                        return;
                }
            } else {
                return;
            }
        } else if (strpos($href, 'secondButton') > 0) {
            if ($row['installedVersion']) {
                switch ($row['brickkey']) {
                    case "maps":
                        $do = 'c4g_map_locstyles';
                        $icon = 'bundles/con4gismaps/images/be-icons/locationstyles.svg';
                        $title = $GLOBALS['TL_LANG']['MOD']['c4g_map_locstyles'][0];
                        break;
                    case "map-content":
                        $do = 'c4g_mapcontent_element';
                        $icon = 'bundles/con4gismapcontent/images/be-icons/mapelements.svg';
                        $title = $GLOBALS['TL_LANG']['MOD']['c4g_mapcontent_element'][0];
                        break;
                    case "editor":
                        $do = 'c4g_editor_element_category';
                        $icon = 'bundles/con4giseditor/images/be-icons/editor_category.svg';
                        $title = $GLOBALS['TL_LANG']['MOD']['c4g_editor_element_category'][0];
                        break;
                    case "pwa":
                        $do = 'c4g_webpush_configuration';
                        $icon = 'bundles/con4gispwa/images/be-icons/webpush_config.svg';
                        $title = $GLOBALS['TL_LANG']['MOD']['c4g_webpush_configuration'][0];
                        break;
                    case "io-travel-costs":
                        $do = 'c4g_travel_costs_settings';
                        $title = $GLOBALS['TL_LANG']['MOD']['c4g_travel_costs_settings'][0];
                        break;
                    case "visualization":
                        $do = 'c4g_visualization_chart';
                        $icon = 'bundles/con4gisvisualization/images/be-icons/grafic.svg';
                        $title = $GLOBALS['TL_LANG']['MOD']['c4g_visualization_chart'][0];
                        break;
                    case "firefighter":
                        $do = 'c4g_firefighter_operation_categories';
                        $icon = 'bundles/con4gisfirefighter/images/be-icons/operationcategories.svg';
                        $title = $GLOBALS['TL_LANG']['MOD']['c4g_firefighter_operation_categories'][0];
                        break;
                    case "forum":
                        $do = 'c4g_forum_thread';
                        $icon = 'bundles/con4gisforum/images/be-icons/forumthreads.svg';
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
                            $do = 'c4g_map_themes';
                            $icon = 'bundles/con4gismaps/images/be-icons/maplayout.svg';
                            $title = $GLOBALS['TL_LANG']['MOD']['c4g_map_themes'][0];
                            break;
                        case "map-content":
                            $do = 'c4g_mapcontent_type';
                            $icon = 'bundles/con4gismapcontent/images/be-icons/mapcategory.svg';
                            $title = $GLOBALS['TL_LANG']['MOD']['c4g_mapcontent_type'][0];
                            break;
                        case "editor":
                            $do = 'c4g_editor_element_type';
                            $icon = 'bundles/con4giseditor/images/be-icons/editor_type.svg';
                            $title = $GLOBALS['TL_LANG']['MOD']['c4g_editor_element_type'][0];
                            break;
                        case "pwa":
                            $do = 'c4g_push_notification';
                            $icon = 'bundles/con4gispwa/images/be-icons/push_example.svg';
                            $title = $GLOBALS['TL_LANG']['MOD']['c4g_push_notification'][0];
                            break;
                        case "firefighter":
                            $do = 'c4g_firefighter_vehicle_types';
                            $icon = 'bundles/con4gisfirefighter/images/be-icons/vehicletypes.svg';
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
                        $do = 'c4g_map_profiles';
                        $icon = 'bundles/con4giscore/images/be-icons/global_settings_16.svg';
                        $title = $GLOBALS['TL_LANG']['MOD']['c4g_map_profiles'][0];
                        break;
                    case "map-content":
                        $do = 'c4g_mapcontent_directory';
                        $icon = 'bundles/con4gismapcontent/images/be-icons/mapfolder.svg';
                        $title = $GLOBALS['TL_LANG']['MOD']['c4g_mapcontent_directory'][0];
                        break;
                    case "pwa":
                        $do = 'c4g_push_subscription_type';
                        $icon = 'bundles/con4gispwa/images/be-icons/push_types.svg';
                        $title = $GLOBALS['TL_LANG']['MOD']['c4g_push_subscription_type'][0];
                        break;
                    case "firefighter":
                        $do = 'c4g_firefighter_vehicles';
                        $icon = 'bundles/con4gisfirefighter/images/be-icons/vehicles.svg';
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
                        $do = 'c4g_maps';
                        $icon = 'bundles/con4gismaps/images/be-icons/mapstructure.svg';
                        $title = $GLOBALS['TL_LANG']['MOD']['c4g_maps'][0];
                        break;
                    case "firefighter":
                        $do = 'c4g_firefighter_unit_types';
                        $icon = 'bundles/con4gisfirefighter/images/be-icons/unittypes.svg';
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
                        $do = 'c4g_map_tables';
                        $icon = 'bundles/con4gismaps/images/be-icons/sourcetables.svg';
                        $title = $GLOBALS['TL_LANG']['MOD']['c4g_map_tables'][0];
                        break;
                    case "firefighter":
                        $do = 'c4g_firefighter_units';
                        $icon = 'bundles/con4gisfirefighter/images/be-icons/units.svg';
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
                        $do = 'c4g_map_filters';
                        $icon = 'bundles/con4gismaps/images/be-icons/mapfilter.svg';
                        $title = $GLOBALS['TL_LANG']['MOD']['c4g_map_filters'][0];
                        break;
                    case "firefighter":
                        $do = 'c4g_firefighter_operations';
                        $icon = 'bundles/con4gifirefighter/images/be-icons/operations.svg';
                        $title = $GLOBALS['TL_LANG']['MOD']['c4g_firefighter_operations'][0];
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
        $ref = Input::get('ref');
        $href = '/contao/main.php?do='.$do.'&amp;ref='.$ref;

        return $this->User->hasAccess($do, 'modules') ? '<a href="' . $href . '" title="' . StringUtil::specialchars($title) . '"'.$attributes.'>'.Image::getHtml($icon, $label).'</a>' : Contao\Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)) . ' ';

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
