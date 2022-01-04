<?php

/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2021, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */

/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['fields']['action_handler']           = array('Action handler', 'Der Handler, der aufgerufen wird, wenn ein Mitglied mit einem gültigen Schlüssel die Seite aufruft.');
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['fields']['confirmation']             = array('Aktionsbestätigung aktivieren', 'Aktiviert eine Bestätigungsseite, auf der der Benutzer die Aktion vor der Ausführung bestätigen kann.');
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['fields']['confirmation_text']        = array('Bestätigungs-Info', 'Dieser Text wird auf der Bestätigungsseite angezeigt und sollte Informationen über die auszuführende Aktion beinhalten.');
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['fields']['confirmation_button']      = array('Benutzerdefinierte Bestätigungs-Button-Beschriftung', 'Dieser Text erscheint auf dem Bestätigungs-Button. (Leer = Standard-Beschriftung)');
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['fields']['success_msg']              = array('Benutzerdefinierte Erfolgsmeldung', 'Dieser Text wird angezeigt, wenn die Aktion erfolgreich ausgeführt wurde. Ist dieses Feld leer, so wird die Standardausgabe des "Handlers" ausgegeben, insofern es eine gibt, ansonnsten eine Standardausgabe der Aktivierungsseite.');
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['fields']['invalid_key_msg']          = array('Benutzerdefinierte Fehlermeldung (ungültiger Schlüssel)', 'Dieser Text wird angezeigt, wenn der verwendete Schlüssel ungültig ist. Ist dieses Feld leer, wird eine Standardausgabe der Aktivierungsseite angezeigt.');
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['fields']['handler_error_msg']        = array('Benutzerdefinierte Fehlermeldung (Ausführungsfehler)', 'Dieser Text wird angezeigt, wenn die Aktion nicht ausgeführt werden konnte. Ist dieses Feld leer, so wird die Standardausgabe des "Handlers" ausgegeben, insofern es eine gibt, ansonnsten eine Standardausgabe der Aktivierungsseite.');
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['fields']['use_default_css']          = array('Standard-CSS laden', 'Die standard CSS-Datei der Aktivierungsseite laden. Deaktivieren Sie diese Option, wenn Sie die Aktivierungsseite selbst gestalten wollen.');
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['fields']['c4g_activationpage_visitor_redirect'] = array('Weiterleitungsseite für Besucher', 'Geben Sie hier eine Seite ein, auf die Besucher weitergeleitet werden sollen (z.B. eine Seite mit einem Login-Formular).');

/**
 * Legend
 */
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage_function_legend']                       = 'Funktion';
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage_custom_message_legend']                 = 'Benutzerdefinierte Ausgaben';

/**
 * Errors
 */
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['errors']['invalid_key']                  = 'Der benutzte Schlüssel ist ungültig!';
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['errors']['key_not_claimed']              = 'WARNUNG: <br> &nbsp; Der Schlüssel konnte nicht von Ihnen entwertet werden! <br> &nbsp; Bitte kontaktieren Sie den Systemadministrator.';
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['errors']['handler_failed']               = 'Die Aktion konnte nicht ausgeführt werden!';
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['errors']['no_handler']                   = 'Es konnte kein passender "Action-handler" gefunden werden ! <br> Bitte kontaktieren Sie den Systemadministrator.';

/**
 * Misc.
 */
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['msc']['auto_action_handler']             = 'Automatisch auswählen';
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['msc']['default_confirmation_button']     = 'Bestätigen';
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['msc']['success_msg']                     = 'Die Aktion wurde erfolgreich ausgeführt.';
