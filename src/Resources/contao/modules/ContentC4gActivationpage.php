<?php

/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 10
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2025, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */

namespace con4gis\CoreBundle\Resources\contao\modules;

use con4gis\CoreBundle\Resources\contao\models\C4gActivationkeyModel;
use Contao\System;
use Contao\Input;
use Contao\Module;
use Contao\BackendTemplate;
use Contao\FrontendUser;

/**
 * Class Content_c4g_activationpage
 * @package c4g
 */
class ContentC4gActivationpage extends Module
{
    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'ce_c4g_activationpage';

    /**
     * Generate content element
     */
    public function generate()
    {
        if (System::getContainer()->get('contao.routing.scope_matcher')->isBackendRequest(System::getContainer()->get('request_stack')->getCurrentRequest() ?? Request::create('')))
        {
            $objTemplate = new BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### ' . ($this->c4g_activationpage_action_handler ?: mb_strtoupper($GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['msc']['auto_action_handler']) ). ' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = System::getContainer()->get('router')->generate('contao_backend'). '?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }

        return parent::generate();
    }

    /**
     * Generate module
     */
    protected function compile()
    {
        // prepare stuff
        $this->import(FrontendUser::class, 'User');
        System::loadLanguageFile('tl_content');
        $stateClass = array
        (
            1 => 'c4g_ap_success',
            0 => 'c4g_ap_confirm',
            -1 => 'c4g_ap_failure'
        );

        // $session = \Session::getInstance();

        // write key to session, so it will not get lost
        if (Input::get('key')) {
            // $session->set('c4g_activationkey_' . $this->id, \Input::get('key'));
            $_SESSION['c4g_activationkey_' . $this->id] = Input::get('key');
        }

        $hasFrontendUser = System::getContainer()->get('contao.security.token_checker')->hasFrontendUser();
        if (!$hasFrontendUser && $this->c4g_activationpage_visitor_redirect) {
            // redirect to defined page
            $objPage = $this->Database->prepare("SELECT id, alias FROM tl_page WHERE id=?")
                ->limit(1)
                ->execute($this->c4g_activationpage_visitor_redirect);

            if ($objPage->numRows) {
                \Controller::redirect($this->generateFrontendUrl($objPage->fetchAssoc()));
            } else {
                $this->Template->state = $stateClass[-1];
                $this->Template->output = 'Redirect error';
                return false;
            }
        }

        // load default CSS if enabled
        if ($this->c4g_activationpage_use_default_css) {
            $GLOBALS['TL_CSS']['c4g_activationpage'] = 'bundles/con4giscore/dist/css/ce_c4g_activationpage.min.css';
        }

        // check if a confirmation is needed
        if ($this->c4g_activationpage_confirmation && !Input::get('confirm')) {
            $this->Template->state = $stateClass[0];
            $this->Template->output = $this->c4g_activationpage_confirmation_text;
            $delim = (preg_match('/\?/', System::getContainer()->get('contao.insert_tag.parser')->replace('{{env::request}}')) > 0) ? '&' : '?';
            $this->Template->output .= '<a href="{{env::path}}{{env::request}}' . $delim . 'confirm=true" class="c4g_button"><span class="c4g_button_text">' . ($this->c4g_activationpage_confirmation_button ?: $GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['msc']['default_confirmation_button']) . '</span></a>';
        } else {
            // 1) check key
            $action = '';
            $this->Template->state = $stateClass[-1];
            // $key = \Input::get('key') ?: $session->get('c4g_activationkey_' . $this->id);
            $key = Input::get('key') ?: $_SESSION['c4g_activationkey_' . $this->id];
            $this->Template->output = '';
            if (!empty( $key ) && C4gActivationkeyModel::keyIsValid( $key )) {
                $action = C4gActivationkeyModel::getActionForKey( $key );
            }
            if (!empty( $action )) {
                // the key is valid
                //
                $action = explode(':', $action);
                if ($action[1]) {
                    $action[1] = explode('&', $action[1]);
                }
                // 2) find handler
                if (!$this->c4g_activationpage_action_handler || $this->c4g_activationpage_action_handler === $action[0]) {
                    $actionHandler = $GLOBALS['C4G_ACTIVATIONACTION'][$action[0]];
                }
                if (!empty( $actionHandler )) {
                    // handler found
                    // 3) execute handler
                    try {
                        $clActionHandler = new $actionHandler();
                        $arrResponse = $clActionHandler->performActivationAction( $action[0], $action[1] ?: array() );
                        if ($arrResponse && $arrResponse['success']) {
                            // everything went right
                            // claim key and return the success
                            $this->Template->state = $stateClass[1];
                            $this->Template->output .= $this->c4g_activationpage_success_msg ?: ($arrResponse['output'] ?: $GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['msc']['success_msg']);
                            if( !C4gActivationkeyModel::assignUserToKey( (FE_USER_LOGGED_IN ? $this->User->id : -1), $key) ) {
                                $this->Template->output .= '<div class="c4g_ap_warning">' . $GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['errors']['key_not_claimed'] . '</div>';
                            }
                        } else {
                            // ERROR: the handlers action failed
                            $this->Template->output .= $this->c4g_activationpage_handler_error_msg ?: ($arrResponse['output'] ?: $GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['errors']['handler_failed']);
                        }
                    } catch (Exeption $e) {
                        $this->Template->output .= $GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['errors']['no_handler'];
                    }
                } else {
                    // ERROR: no appropriate handler found
                    $this->Template->output .= $GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['errors']['no_handler'];
                }
            } else {
                // ERROR: the key is invalid
                $this->Template->output .= $this->c4g_activationpage_invalid_key_msg ?: $GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['errors']['invalid_key'];
            }
        }
    }

}
