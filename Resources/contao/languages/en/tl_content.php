<?php

/*
 * This file is part of con4gis,
 * the gis-kit for Contao CMS.
 *
 * @package    con4gis
 * @version    7
 * @author     con4gis contributors (see "authors.txt")
 * @license    LGPL-3.0-or-later
 * @copyright  Küstenschmiede GmbH Software & Design
 * @link       https://www.con4gis.org
 */

/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['fields']['action_handler']           = array('Action handler', 'The handler that will be called, when a member with a valid key enters this page.');
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['fields']['confirmation']             = array('Enable confirmation for action', 'This will allow the member to confirm the action, that should be executed on this page.');
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['fields']['confirmation_text']        = array('Confirmation info text', 'This text will be shown to the user and should contain information about the action that will be triggered with this page.');
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['fields']['confirmation_button']      = array('Custom confirmation-button-label', 'Here you can replace the default label of the confirmation-button. (empty = use default label)');
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['fields']['success_msg']              = array('Custom success message', 'Enter a custom message that will be shown to the member, when the key was valid and the executed function returned a success. If this is empty, the handlers default message will be used, if existant, otherwise the activationpage default message will be used.');
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['fields']['invalid_key_msg']          = array('Custom error message (invalid key)', 'Enter a custom message that will be shown to the member, when the used key is invalid or already used. If this is empty, the activationpage default message will be used.');
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['fields']['handler_error_msg']        = array('Custom error message (handler error)', 'Enter a custom message that will be shown to the member, when the choosen function did not return a success. If this is empty, the handlers default message will be used, if existant, otherwise the activationpage default message will be used.');
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['fields']['use_default_css']          = array('Load default CSS', 'This will load a default CSS-file for the activationpage. Disable this option if you wish to style this page manually.');
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['fields']['c4g_activationpage_visitor_redirect'] = array('Redirect visitors', 'Choose a page to which visitors should be redirected.');

/**
 * Legend
 */
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage_function_legend']                       = 'Function';
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage_custom_message_legend']                 = 'Custom messages';

/**
 * Errors
 */
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['errors']['invalid_key']                  = 'The used key is invalid!';
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['errors']['key_not_claimed']              = 'WARNING: <br> &nbsp; The key could not be assigned to you! <br> &nbsp; Please contact the Systemadministrator.';
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['errors']['handler_failed']               = 'The action could not be performed!';
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['errors']['no_handler']                   = 'Could not find an appropriate action-handler! <br> Please contact the Systemadministrator.';

/**
 * Misc.
 */
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['msc']['auto_action_handler']             = 'Choose automatically';
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['msc']['default_confirmation_button']     = 'Confirm';
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['msc']['success_msg']                     = 'The action was performed successfully.';