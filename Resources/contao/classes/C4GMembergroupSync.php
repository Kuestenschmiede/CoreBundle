<?php

/**
 * con4gis - the gis-kit
 *
 * @version   php 5
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2018
 * @link      https://www.kuestenschmiede.de
 */

namespace con4gis\CoreBundle\Resources\contao\classes;

use con4gis\GroupsBundle\Resources\models\MemberModel;
use con4gis\GroupsBundle\Resources\models\MemberGroupModel;

/**
 * Class C4GMembergroupSync
 * @package c4g
 */
class C4GMembergroupSync extends \BackendModule
{
    protected $strTemplate = 'be_c4g_membergroupsync';
    protected $action = '';
    protected $output = array();
    protected $buttons = array();

    /**
     * [__construct description]
     */
    public function __construct()
    {
        parent::__construct();
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
                $this->syncMemberGroupBindings();
                break;
            case 'back':
                \Controller::redirect( 'contao/main.php?do=c4g_core' );
            case 'init':
            default:
                $this->output[] = $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MEMBERGROUPSYNC']['INTRO'];
                $this->output[] = '<span class="c4g_warning">' . $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MEMBERGROUPSYNC']['WARNING'] . '</span>';

                $this->buttons[] = array(
                    action  => 'exec',
                    label   => $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['BTN']['SYNCBINDINGS']
                );
                break;
        }
        $this->buttons[] = array(
            action  => 'back',
            label   => $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['BACK']
        );

        return parent::generate();
    }

    /**
     * Generate the module
     */
    protected function compile()
    {
        $this->Template->action = $this->action ?: false;
        $this->Template->output = $this->output ?: '';

        $this->Template->buttons = $this->buttons ?: '';
    }

    protected function syncMemberGroupBindings()
    {
        if (class_exists('con4gis\GroupsBundle\Resources\models\MemberModel') && class_exists('con4gis\GroupsBundle\Resources\models\MemberGroupModel')) {

            // fetch all enabled members
            $objMembers = MemberModel::findAll(array('disable' => ''));

            if ($objMembers) {
                foreach ($objMembers as $objMember) {
                    $memberGroupIds = $objMember->groups ? unserialize($objMember->groups) : array();

                    foreach ($memberGroupIds as $memberGroupId) {
                        if (!MemberGroupModel::isMemberOfGroup( $memberGroupId, $objMember->id )) {
                            $objGroup = MemberGroupModel::findByPk( $memberGroupId );
                            if ($objGroup) {
                                // check if the group has a member-limitation
                                if ($objGroup->cg_max_member > 0 && $objGroup->cg_max_member <= count(unserialize($objGroup->cg_member))) {
                                    $this->output[] = '<span class="c4g_warning">' . sprintf($GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MEMBERGROUPSYNC']['ERROR_GROUPLIMITREACHED'], $objMember->id, $objGroup->id) . '</span>';
                                    continue;
                                }

                                $members = $objGroup->cg_member ? unserialize( $objGroup->cg_member ) : array();
                                $members[] = $objMember->id;
                                $objGroup->cg_member = serialize( $members );
                                $objGroup->save();
                            }
                        }
                    }
                }
                $this->output[] = '<span class="c4g_success">' . $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MEMBERGROUPSYNC']['SUCCESS'] . '</span>';
                return true;
            }
        }

        $this->output[] = '<span class="c4g_error">' .$GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MEMBERGROUPSYNC']['FAILED'] . '</span>';
        return false;
    }

}