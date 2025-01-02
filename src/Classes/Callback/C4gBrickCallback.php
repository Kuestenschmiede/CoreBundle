<?php

namespace con4gis\CoreBundle\Classes\Callback;

use con4gis\CoreBundle\Classes\C4GVersionProvider;
use Contao\Config;
use Contao\Date;
use Contao\DC_Table;
use Contao\Image;
use Contao\StringUtil;
use Contao\BackendUser;
use Contao\Backend;
use Contao\Database;
use Contao\Input;
use Contao\Controller;
use Contao\Environment;
use Composer\InstalledVersions;
use Contao\System;
use Symfony\Component\Security\Core\Security;

class C4gBrickCallback extends Backend
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
        $this->User = System::getContainer()->get('security.helper')->getUser();

        $iconPath = 'bundles/con4giscore/images/be-icons/';

        $this->bundles = [
            'core' => [
                'repo' => 'CoreBundle',
                'vendor' => 'con4gis',
                'description' => &$GLOBALS['TL_LANG']['tl_c4g_bricks']['core'],
                'icon' => $iconPath.'core_c4g.svg'
            ],
            'data' => [
                'repo' => 'DataBundle',
                'vendor' => 'con4gis',
                'description' => &$GLOBALS['TL_LANG']['tl_c4g_bricks']['data'],
                'icon' => $iconPath.'data_c4g.svg'
            ],
            'documents' => [
                'repo' => 'DocumentsBundle',
                'vendor' => 'con4gis',
                'description' => &$GLOBALS['TL_LANG']['tl_c4g_bricks']['documents'],
                'icon' => $iconPath.'documents_c4g.svg'
            ],
//            'editor' => [
//                'repo' => 'EditorBundle',
//                'vendor' => 'con4gis',
//                'description' => &$GLOBALS['TL_LANG']['tl_c4g_bricks']['editor'],
//                'icon' => $iconPath.'editor_c4g.svg'
//            ],
            'export' => [
                'repo' => 'ExportBundle',
                'vendor' => 'con4gis',
                'description' => &$GLOBALS['TL_LANG']['tl_c4g_bricks']['export'],
                'icon' => $iconPath.'export_c4g.svg'
            ],
//            'firefighter' => [
//                'repo' => 'FirefighterBundle',
//                'vendor' => 'con4gis',
//                'description' => &$GLOBALS['TL_LANG']['tl_c4g_bricks']['firefighter'],
//                'icon' => $iconPath.'firefighter_c4g.svg'
//            ],
            'framework' => [
                'repo' => 'FrameworksBundle',
                'vendor' => 'con4gis',
                'description' => &$GLOBALS['TL_LANG']['tl_c4g_bricks']['framework'],
                'icon' => $iconPath.'framework_c4g.svg'
            ],
//            'forum' => [
//                'repo' => 'ForumBundle',
//                'vendor' => 'con4gis',
//                'description' => &$GLOBALS['TL_LANG']['tl_c4g_bricks']['forum'],
//                'icon' => $iconPath.'forum_c4g.svg'
//            ],
            'groups' => [
                'repo' => 'GroupsBundle',
                'vendor' => 'con4gis',
                'description' => &$GLOBALS['TL_LANG']['tl_c4g_bricks']['groups'],
                'icon' => $iconPath.'groups_c4g.svg'
            ],
            'import' => [
                'repo' => 'ImportBundle',
                'vendor' => 'con4gis',
                'description' => &$GLOBALS['TL_LANG']['tl_c4g_bricks']['import'],
                'icon' => $iconPath.'import_c4g.svg'
            ],
//            'ldap' => [
//                'repo' => 'LdapBundle',
//                'vendor' => 'con4gis',
//                'description' => &$GLOBALS['TL_LANG']['tl_c4g_bricks']['ldap'],
//                'icon' => $iconPath.'ldap_c4g.svg'
//            ],
            'maps' => [
                'repo' => 'MapsBundle',
                'vendor' => 'con4gis',
                'description' => &$GLOBALS['TL_LANG']['tl_c4g_bricks']['maps'],
                'icon' => $iconPath.'maps_c4g.svg'
            ],
//            'oauth' => [
//                'repo' => 'OAuthBundle',
//                'vendor' => 'con4gis',
//                'description' => &$GLOBALS['TL_LANG']['tl_c4g_bricks']['oauth'],
//                'icon' => $iconPath.'oauth_c4g.svg'
//            ],
            'projects' => [
                'repo' => 'ProjectsBundle',
                'vendor' => 'con4gis',
                'description' => &$GLOBALS['TL_LANG']['tl_c4g_bricks']['projects'],
                'icon' => $iconPath.'projects_c4g.svg'
            ],
            'pwa' => [
                'repo' => 'PwaBundle',
                'vendor' => 'con4gis',
                'description' => &$GLOBALS['TL_LANG']['tl_c4g_bricks']['pwa'],
                'icon' => $iconPath.'pwa_c4g.svg'
            ],
            'queue' => [
                'repo' => 'QueueBundle',
                'vendor' => 'con4gis',
                'description' => &$GLOBALS['TL_LANG']['tl_c4g_bricks']['queue'],
                'icon' => $iconPath.'queue_c4g.svg'
            ],
            'reservation' => [
                'repo' => 'ReservationBundle',
                'vendor' => 'con4gis',
                'description' => &$GLOBALS['TL_LANG']['tl_c4g_bricks']['reservation'],
                'icon' => $iconPath.'reservation_c4g.svg'
            ],
            'tracking' => [
                'repo' => 'TrackingBundle',
                'vendor' => 'con4gis',
                'description' => &$GLOBALS['TL_LANG']['tl_c4g_bricks']['tracking'],
                'icon' => $iconPath.'tracking_c4g.svg'
            ],
            'travel-costs' => [
                'repo' => 'TravelCostsBundle',
                'vendor' => 'con4gis',
                'description' => &$GLOBALS['TL_LANG']['tl_c4g_bricks']['travel-costs'],
                'icon' => $iconPath.'travel-costs_c4g.svg'
            ],
            'visualization' => [
                'repo' => 'VisualizationBundle',
                'vendor' => 'con4gis',
                'description' => &$GLOBALS['TL_LANG']['tl_c4g_bricks']['visualization'],
                'icon' => $iconPath.'visualization_c4g.svg'
            ]
        ];
    }

    private function getLatestVersions() {
        $bundles = $this->bundles;
        $packages = [];
        foreach ($bundles as $bundle => $values) {
            $packages[] = $values['vendor'].'/'.$bundle;
        }

        $versions = [];
        foreach ($packages as $package) {
            if ($version = $this->versionProvider->getLatestVersion($package)) {
                $versions[$package] = $version;
            }
        }
        return $versions;
    }

    private function getInstalledPackages() {
        if ($this->getContainer()->hasParameter('kernel.packages')) {
            $packages = $this->getContainer()->getParameter('kernel.packages');
            $installed = [];
            foreach ($packages as $key => $value) {
                if (strpos($key, 'con4gis') !== false || strpos($key, 'gutesio') !== false) {
                    $installed[$key] = $value;
                }
            }
        }
        else {
            $packages = InstalledVersions::getInstalledPackages();
            $installed = [];
            foreach ($packages as $key => $value) {
                if (strpos($value, 'con4gis') !== false || strpos($key, 'gutesio') !== false) {
                    $installed[$value] = InstalledVersions::getVersion($value);
                }
            }
        }
        return $installed;
    }

    private function checkSettings($bundle) {
        if ($bundle == 'core') {
            return true;
        } else if (Database::getInstance()->tableExists('tl_c4g_'.$bundle.'_configuration')){
            $table = 'tl_c4g_'.$bundle.'_configuration';
            try {
                $result = Database::getInstance()->execute("SELECT * FROM $table LIMIT 1")->fetchAllAssoc();
                return true;
            } catch (Exception $e) {
                return false;
            }
        }
        else {
            return false;
        }
    }

    private function compareVersions($iv, $lv) {
        if (($iv != 0) && ($lv != 0)) {
            $ivArr = explode('.',$iv);
            $lvArr = explode('.',$lv);

            $imajor = key_exists(0, $ivArr) && $ivArr[0] && is_int($ivArr[0]) ? intval($ivArr[0]) : false;
            $lmajor = key_exists(0, $lvArr) && $lvArr[0] && is_int($lvArr[0]) ? intval($lvArr[0]) : false;
            $iminor = key_exists(1, $ivArr) && $ivArr[1] && is_int($ivArr[1]) ? intval($ivArr[1]) : false;
            $lminor = key_exists(1, $lvArr) && $lvArr[1] && is_int($lvArr[1]) ? intval($lvArr[1]) : false;
            $ibugfix = key_exists(2, $ivArr) && $ivArr[2] && is_int($ivArr[2]) ? intval($ivArr[2]) : false;
            $lbugfix = key_exists(2, $lvArr) && $lvArr[2] && is_int($lvArr[2]) ? intval($lvArr[2]) : false;

            if (($lmajor && $imajor) && ($lmajor > $imajor)) {
                return true;
            } else if (($lmajor && $imajor) && ($lmajor == $imajor)) {
                if (($lminor && $iminor) && ($lminor > $iminor)) {
                    return true;
                } else if (($lminor && $iminor) && ($lminor == $iminor)) {
                    if (($lbugfix && $ibugfix) && ($lbugfix > $ibugfix)) {
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
        $renewData = false;
        if ($bricks && $bricks[0]) {
            $tstamp = intval($bricks[0]['tstamp']);
            $before_two_days = time() - (2 * 24 * 60 * 60);
            $renewData = $tstamp < $before_two_days ? true : false; //autom. renew after two days
        }

        $bundles = $this->bundles;
        $installedPackages = $this->getInstalledPackages();
        $wrongCount = count($bricks) !== count($installedPackages);
        $renewData = $renewData ? $renewData : $wrongCount;

        if ($renewData || !$dc || !$bricks || $getPackages) {
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
                if (key_exists('con4gis/'.$bundle, $installedPackages) && $installedPackages['con4gis/'.$bundle]) {
                    $vendor = 'con4gis';
                    $installedVersion = $installedPackages['con4gis/'.$bundle];
                    $latestVersion = $this->versions['con4gis/'.$bundle];

                    $iv = (strpos($installedVersion,'v') == 0) ? substr($installedVersion, 1) : $installedVersion;
                    $lv = (strpos($latestVersion,'v') == 0) ? substr($latestVersion, 1) : $latestVersion;
                    if ($this->compareVersions($iv, $lv)) {
                        $latestVersion = '<b>'.$latestVersion.'</b>';
                    }
                } else if (key_exists('gutesio/'.$bundle, $installedPackages) && $installedPackages['gutesio/'.$bundle]) {
                    $vendor = 'gutesio';
                    $installedVersion = $installedPackages['gutesio/'.$bundle];
                    $latestVersion = $this->versions['gutesio/'.$bundle];

                    $iv = (strpos($installedVersion,'v') == 0) ? substr($installedVersion, 1) : $installedVersion;
                    $lv = (strpos($latestVersion,'v') == 0) ? substr($latestVersion, 1) : $latestVersion;
                    if ($this->compareVersions($iv, $lv)) {
                        $latestVersion = '<b>'.$latestVersion.'</b>';
                    }
                } else {
                    $installedVersion = '';
                    $vendor = $values['vendor'];
                    $latestVersion = $this->versions[$vendor.'/'.$bundle];
                }

                $set['tstamp'] = time();
                $set['pid'] = $this->User->id;
                $set['vendor'] = $vendor;
                $set['brickkey'] = $bundle;
                $set['brickname'] = $values['icon'] ? Image::getHtml($values['icon']) : $bundle;
                $set['repository'] = $values['repo'];
                $set['description'] = $values['description'] ?: '-';
                $set['installedVersion'] = $installedVersion ?: '-';
                $set['latestVersion'] = $latestVersion ?: '-';
                $set['withSettings'] = intval($this->checkSettings($bundle));
                $set['icon'] = $values['icon'];
                $set['showBundle'] = $installedVersion != '' ? "1" : "0";
                $set['favorite'] = key_exists($bundle, $favorites) && $favorites[$bundle] ? $favorites[$bundle] : '0';

                $this->Database->prepare("INSERT INTO tl_c4g_bricks %s")->set($set)->execute();
            }

            //get develop packages
            foreach ($installedPackages as $vendorBundle=>$version) {
                if ($bundle = $this->checkDevelopPackage($vendorBundle)) {
                    $installedVersion = $version;

                    $set['tstamp'] = time();
                    $set['pid'] = $this->User->id;
                    $set['vendor'] = $vendor;
                    $set['brickkey'] = $bundle;
                    $set['brickname'] = $bundle;
                    $set['repository'] = '-';
                    $set['description'] = '-';
                    $set['installedVersion'] = $installedVersion ?: '-';
                    $set['latestVersion']    = '-';
                    $set['withSettings'] = intval($this->checkSettings($bundle));
                    $set['showBundle'] = "1";
                    $set['favorite'] = key_exists($bundle, $favorites) && $favorites[$bundle] ? $favorites[$bundle] : '0';

                    $this->Database->prepare("INSERT INTO tl_c4g_bricks %s")->set($set)->execute();
                }
            }
        }

        return $bricks;
    }

    private function checkDevelopPackage($vendorBundle) {
        if (((substr($vendorBundle,0,7) == 'con4gis') || (substr($vendorBundle,0,7) == 'gutesio')) && (!key_exists($vendorBundle, $this->versions) || !$this->versions[$vendorBundle])) {
            $bundle = substr($vendorBundle, 8);

            if (key_exists($bundle, $this->bundles) && $this->bundles[$bundle]) {
                return false;
            }

            return $bundle;
        }
    }


    /**
     * checkButtons
     */
    public function checkButtons(DC_Table $dc)
    {
        $GLOBALS['TL_DCA']['tl_c4g_bricks']['list']['sorting']['filter'] = [];
        $GLOBALS['TL_DCA']['tl_c4g_bricks']['list']['sorting']['filter']['pid'] = array('pid = ?', $this->User->id);

        // Check current action
        $key = Input::get('key');
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
                            $favorite = $result['favorite'] === '1' ? '0' : '1';
                            Database::getInstance()->prepare("UPDATE tl_c4g_bricks SET favorite=? WHERE brickkey=? AND pid=?")->execute($favorite,$keyValue, $this->User->id);
                        }
                    }
                    break;
                default:
                    $bricks = $this->loadBricks(false);
                    break;
            }

            if ($deleteKey) {
                //delete key per redirect
                Controller::redirect(str_replace('&key='.$key, '', Environment::get('request')));
            }

        } else {
            $GLOBALS['TL_DCA']['tl_c4g_bricks']['list']['sorting']['filter']['showBundle'] = ["showBundle = ?", "1"];
            $bricks = $this->loadBricks($dc, false);
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

        if (!$result = Database::getInstance()->execute("SELECT id FROM tl_c4g_settings LIMIT 1")->fetchAssoc()) {
            return '';
        }

        $href = System::getContainer()->get('router')->generate('contao_backend'). '?do=c4g_settings&id="' . $result['id'].'"&rt='.$rt.'&key=openSettings';
        return $this->User->hasAccess('c4g_settings', 'modules') ? '<a href="' . $href . '" class="' . $class . '" title="' . StringUtil::specialchars($title) . '"' . $attributes . '>' . $label . '</a> ' : '';
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

        $href = System::getContainer()->get('router')->generate('contao_backend'). "?do=".$do."&key=".$actKey;
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

        $href = System::getContainer()->get('router')->generate('contao_backend'). "?do=$do&key=reloadVersions";
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
        $href = System::getContainer()->get('router')->generate('contao_backend'). "?do=c4g_io_data&rt=$rt&key=importData";
        return $this->User->hasAccess('c4g_io_data', 'modules') ?  '<a href="' . $href . '" class="' . $class . '" title="' . StringUtil::specialchars($title) . '"' . $attributes . '>' . $label . '</a> ' : '';
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
        $href = System::getContainer()->get('router')->generate('contao_backend'). "?do=c4g_log&rt=$rt&key=serverLogs";
        return $this->User->hasAccess('c4g_log', 'modules') ? '<a href="' . $href . '" class="' . $class . '" title="' . StringUtil::specialchars($title) . '"' . $attributes . '>' . $label . '</a> ' : '';
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
        return '<a href="https://con4gis.support"  class="' . $class . '" title="' . StringUtil::specialchars($title) . '"' . $attributes .' target="_blank" rel="noopener">' . $label . '</a><br>';
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
            if (array_key_exists('brick', $module ) && $module['brick']) {
                $brickArr[$module['brick']][] = ['do' => $key, 'icon' => $module['icon'] ?: '', 'title' => $GLOBALS['TL_LANG']['MOD'][$key][1]];
            }
        }

        $buttons = ['firstButton', 'secondButton', 'thirdButton', 'fourthButton', 'fifthButton', 'sixthButton', 'seventhButton', 'eighthButton', 'ninthButton', 'tenthButton', 'eleventhButton'];
        $foundButton = false;
        foreach ($buttons as $key=>$button) {
            if ((strpos($href, $button) > 0) && array_key_exists('brickkey', $row) && array_key_exists($row['brickkey'], $brickArr) && array_key_exists($key, $brickArr[$row['brickkey']]) && ($brickArr[$row['brickkey']][$key])) {
                $do = $brickArr[$row['brickkey']][$key]['do'];
                $icon = $brickArr[$row['brickkey']][$key]['icon'] ?: '';
                $title = $brickArr[$row['brickkey']][$key]['title'];
                $foundButton = true;
                break;
            }
        }

        if (!$foundButton) {
            return;
        }

        $attributes = 'style="margin-right:3px"';
        $imgAttributes = 'style="width: 18px; height: 18px"';
        $ref = Input::get('ref');
        $href = System::getContainer()->get('router')->generate('contao_backend'). '?do='.$do.'&amp;ref='.$ref;

        return $this->User->hasAccess($do, 'modules') ? '<a href="' . $href . '" title="' . StringUtil::specialchars($title) . '"'.$attributes.'>'.Image::getHtml($icon, $label, $imgAttributes).'</a>' : Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)) . ' ';
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
        if ($row['vendor'] == 'con4gis' && !$this->checkDevelopPackage($row['vendor'].'/'.$row['brickkey'])) {
            return '<a href="https://docs.con4gis.org/con4gis-' . $row['brickkey'] . '" title="' . StringUtil::specialchars($title) . '" ' . $attributes . ' target="_blank" rel="noopener">' . Image::getHtml($icon, $label, $imgAttributes) . '</a>';
        }
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

        if (!$this->checkDevelopPackage($row['vendor'].'/'.$row['brickkey'])) {
            return '<a href="https://packagist.org/packages/'.$row['vendor'].'/'.$row['brickkey'].'" title="'.StringUtil::specialchars($title).'" '.$attributes.' target="_blank" rel="noopener">'.Image::getHtml($icon, $label, $imgAttributes).'</a>';
        }
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
        if (!$this->checkDevelopPackage($row['vendor'].'/'.$row['brickkey'])) {
            return '<a href="https://github.com/Kuestenschmiede/' . $row['repository'] . '" title="' . StringUtil::specialchars($title) . '" target="_blank" rel="noopener">' . Image::getHtml($icon, $label, $imgAttributes) . '</a>';
        }
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

        $attributes = ' style="margin-right:3px"';
        $imgAttributes = 'style="width: 18px; height: 18px"';

        $showButton = false;
        foreach ($GLOBALS['BE_MOD']['con4gis'] as $key=>$module) {
            if (array_key_exists('brick', $module) && array_key_exists('brickkey', $row) && $module['brick'] == $row['brickkey']) {
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
            } else {
                $icon = 'bundles/con4giscore/images/be-icons/star.svg';
            }

            $href = System::getContainer()->get('router')->generate('contao_backend'). "?do=".$do."&key=switchFavorite_".$row['brickkey']."&rt=".$rt."&ref=''";
            $link = '<a href="' . $href . '" title="' . StringUtil::specialchars($title) . '"' . $attributes . '>'.Image::getHtml($icon, $label, $imgAttributes).'</a> ';
            return $link;
        }
    }
}
