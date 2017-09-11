<?php

/**
 * con4gis - the gis-kit
 *
 * @version   php 5
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2017.
 * @link      https://www.kuestenschmiede.de
 */

namespace c4g;

/**
 * Class C4GMigration
 * @package c4g
 */
class C4GMigration extends \BackendModule
{
    protected $strTemplate = 'be_c4g_migration';
    protected $module = '';
    protected $action = '';
    protected $output = array();
    protected $buttons = array();

    /**
     * [__construct description]
     * @param [type] $mod [description]
     */
    public function __construct( $mod )
    {
        parent::__construct();
        // import database
        // $this->import('Database');

        // set targeted module
        $this->module = $mod;

    }

    /**
     * Generate the module
     * @return string
     */
    public function generate()
    {
        if (\Input::get('act') != '') {
            $this->action = \Input::get('act');
        } else {
            $this->action = 'init';
        }

        switch( $this->action )
        {
            case 'exec':
                $this->migrate();
                break;
            case 'back':
                \Controller::redirect( \Environment::get('script') . '?do=c4g_core' );
            case 'dbupdate':
                \Controller::redirect( \Environment::get('script') . '?do=repository_manager&update=database' );
            case 'uninstall':
                \Controller::redirect( \Environment::get('script') . '?do=repository_manager&uninstall=cfs_' . $this->module );
            case 'init':
            default:
                $this->output[] = sprintf($GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MIGRATION']['INTRO'], $this->module);
                $this->output[] = '<span class="c4g_error">' . sprintf($GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MIGRATION']['INTROWARN'], $this->module) . '</span>';
                if ($this->checkMod()) {
                    $this->buttons[] = array(
                        action  => 'exec',
                        label   => $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['BTN']['MIGRATE']
                    );
                    $this->buttons[] = array(
                        action  => 'back',
                        label   => $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['CANCEL']
                    );
                } else {
                    $this->buttons[] = array(
                        action  => 'back',
                        label   => $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['BACK']
                    );
                }
                break;
        }


        return parent::generate();
    }

    /**
     * Generate the module
     */
    protected function compile()
    {
        $this->Template->mod = $this->module;

        $this->Template->action = $this->action ?: false;
        $this->Template->output = $this->output ?: '';

        $this->Template->buttons = $this->buttons ?: '';

        // $GLOBALS['TL_CSS'][] = 'system/modules/con4gis_core/assets/css/be_c4g_info.css';
        // $this->Template->c4gModules->con4gis_maps->installed = $GLOBALS[];
    }


    protected function checkMod()
    {
        if (!$GLOBALS['con4gis_' . $this->module . '_extension']['installed']) {
            $this->output[] = '<span class="c4g_errorblock">' .
                                sprintf( $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MIGRATION']['NOMODULEERROR'], 'con4gis_'.$this->module ) .
                                '</span>';
            return false;
        }
        if (!$GLOBALS['cfs_' . $this->module . '_extension']['installed']) {
            $this->output[] = '<span class="c4g_errorblock">' .
                                sprintf( $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MIGRATION']['NOMODULEERROR'], 'cfs_'.$this->module ).
                                '</span>';
            return false;
        }

        // everything is okay
        return true;
    }

    protected function updateType( $table )
    {
        $type = 'cfs_' . $this->module;
        if ($this->module == 'forum') {
            $type .= '_comfort';
        }
        $newType = 'c4g_' . $this->module;
        $success = true;

        $output = '<strong>' . $table . '</strong> (type):<br>&nbsp;' . $type . ' <span style="color:#999;">-&gt;</span> ' . $newType . ': ';

        if ($this->Database->prepare( "UPDATE $table SET type = ? WHERE type = ?" )->execute( $newType, $type )) {
            $output .= '<span class="c4g_success">' . $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MIGRATION']['SUCCESS'] . '</span>';
        } else {
            $output .= '<span class="c4g_error">' . $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MIGRATION']['FAIL'] . '</span>';
            $success = false;
        }

        $this->output[] = $output;

        return $success;
    }

    protected function migrate()
    {
        $successCount = 0;
        $errorCount = 0;
        // [tricky]: little specialcasing for cfs_maps, since most of the tables are named "cfs_map_*"
        $mod = ($this->module == 'maps') ? 'map' : $this->module;

        // fetch all related tables
        foreach ($this->Database->listTables() as $table) {
            if (C4GUtils::startsWith($table, 'tl_cfs_'.$mod)) {
                // calculate the new name
                $newTable = str_replace('_cfs_', '_c4g_', $table);
                $output = '<strong>' . $table . ' &nbsp;<span style="color:#bbb">-&gt;</span>&nbsp; ' . $newTable . ':</strong><br> &nbsp; ';
                // copy the data and generate an appropriate output
                try {
                    // recreate new table (since "DISABLE KEYS" didn't work)
                    $this->Database->prepare( "DROP TABLE $newTable" )->execute();
                    $this->Database->prepare( "CREATE TABLE $newTable LIKE $table" )->execute();
                    if ($this->Database->prepare( "INSERT INTO $newTable SELECT * FROM $table" )->execute()) {
                        $output .= ' <span class="c4g_success">' .
                                    sprintf( $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MIGRATION']['TRANSFEREDROW'],
                                        $this->Database->prepare( "SELECT COUNT(*) AS count FROM $newTable" )->execute()->first()->count,
                                        $this->Database->prepare( "SELECT COUNT(*) AS count FROM $table" )->execute()->first()->count
                                    ) .
                                    ' </span>';

                        $successCount++;
                    } else {
                        $output .= ' <span class="c4g_error">' . $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MIGRATION']['FAIL'] . '</span>';
                        $errorCount++;
                    }
                } catch (Exception $e) {
                    $errorCount++;
                    $output .= ' <span class="c4g_error">' . $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MIGRATION']['FAIL'] . '<span>';
                }
                $this->output[] = $output;

            } elseif ($table == 'tl_module' || $table == 'tl_content' || ($mod == 'map' && $table == 'tl_calendar_events')) {
                $successCountF = 0;
                $errorCountF = 0;
                $output = '<strong>' . $table . ':</strong>';

                foreach ($this->Database->getFieldNames( $table ) as $field) {
                    if ($table == 'tl_calendar_events') {
                        $identifier = 'cfs_';
                    } else {
                        $identifier = 'cfs_' . $mod;
                    }
                    if (C4GUtils::startsWith($field, $identifier)) {
                        // calculate the new name
                        $newField = str_replace('cfs_', 'c4g_', $field);
                        $output .= '<br> &nbsp; <em>' . $field . ' &nbsp;->&nbsp; ' . $newField . ':</em>';
                        try {
                            $output .= $this->Database->prepare( "UPDATE $table SET $newField = $field" )->execute()?
                                    ' <span class="c4g_success">' . $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MIGRATION']['SUCCESS'] . '</span>' :
                                    ' <span class="c4g_error">' . $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MIGRATION']['FAIL'] . '</span>';
                            $successCountF++;
                        } catch (Exception $e) {
                            $errorCountF++;
                            $output .= ' <span class="c4g_error">' . $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MIGRATION']['FAIL'] . '<span>';
                        }
                    }
                }
                if ($errorCountF <= 0) {
                    $msg = '<br><span class="c4g_success">' . sprintf( $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MIGRATION']['TRANSFEREDCOL'], $successCountF ) . '</span>';
                    $successCount++;
                } else {
                    $msg = '<br><span class="c4g_error">' . $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MIGRATION']['FAIL'] . '</span>';
                    $errorCount++;
                }
                $this->output[] = $output . $msg;
            }
        }

        if ($errorCount <= 0) {
            $this->output[] = '<br><span class="c4g_successblock">' . $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MIGRATION']['SUCCESSMSG_1'] . '</span><br>';

            //re-add "editorfield"
            if ($mod == 'map') {
                $this->Database->prepare( "ALTER TABLE `tl_c4g_map_profiles` ADD `editor` char(1) NOT NULL default ''" )->execute();
            }

            if ($this->updateType('tl_module') && $this->updateType('tl_content')) {
                $this->output[] = '<br><span class="c4g_successblock">' .
                                sprintf( $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MIGRATION']['SUCCESSMSG_2'], $this->module ) .
                                '</span>';
                $this->output[] = '<br><span class="c4g_warningblock">' .
                                sprintf( $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MIGRATION']['SUCCESSMSG_3'], $this->module ) .
                                '</span>';
                $this->buttons[] = array(
                    action  => 'dbupdate',
                    label   => sprintf( $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['BTN']['DBUPDATE'], $this->module)
                );
                $this->buttons[] = array(
                    action  => 'uninstall',
                    label   => sprintf( $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['BTN']['UNINSTALL'], $this->module)
                );
            } else {
                $this->output[] = '<br><span class="c4g_errorblock">' .
                                $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MIGRATION']['FAILMSG_2'] .
                                '</span>';
            }

        } else {
            $this->output[] = '<br><span class="c4g_errorblock">' .
                                sprintf( $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MIGRATION']['FAILMSG_1'], $errorCount, $errorCount+$successCount ) .
                                '</span>';
        }

        $this->buttons[] = array(
                    action  => 'back',
                    label   => $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['BACK']
                );
    }
}