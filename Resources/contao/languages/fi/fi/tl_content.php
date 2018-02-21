<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

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

/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['fields']['action_handler']           = array('Toiminnan käsittelijä', 'Käsittelijä jota kutsutaan, kun jäsen tulee sisään oikealla avaimella.');
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['fields']['confirmation']             = array('Mahdollistaa varmistuksen tälle toiminnalle', 'Tämä sallii jäsenen varmistaa toiminnan, joka pitäisi suorittaa tällä sivulla.');
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['fields']['confirmation_text']        = array('Vahvistus info teksti', 'Tämä teksti näkyy käyttäjälle ja sen pitäisi sisältää tietoa toiminnasta joka laukaistaan tälle sivulle.');
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['fields']['confirmation_button']      = array('Custom konfirmaatio-nappi-merkki', 'Tässä voit korvata oletusarvo labelin konformaatio napissa. (tyhjä = käytä oletusarvo labelia)');
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['fields']['success_msg']              = array('Custom onnistumis viesti', 'Laita viesti joka näkyy jäsenille, kun avain oli pätevä ja suoritettu funktio onnistui. Jos tämä on tyhjä, käsittelijöiden oletusviestiä käytetään, jos on olemassa, muuten aktivointisivun oletusviestiä käytetään.');
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['fields']['invalid_key_msg']          = array('Custom virheilmoitus (väärä avain)', 'Laita custom viesti joka näkyy jäsenelle, kun käytetty avain on väärä, tai jo käytössä. jos tämä on tyhjä, aktivointisivun oletusviestiä käytetään.');
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['fields']['handler_error_msg']        = array('Custom virheilmoitus (käsittelijä virhe)', 'Laita custom viesti joka näkyy jäsenelle, kun valittu funktio ei palannut onnistuneena. Jos tämä on tyhjä, käsittelijöiden oletusviestiä käytetään, jos olemassa, muuten aktivointisivun oletusviestiä käytetään.');
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['fields']['use_default_css']          = array('Lataa oletus CSS', 'Tämä lataa oletus CSS-tiedoston aktivointisivulle. Ota pois käytöstä tämä vaihtoehto jos haluat muotoilla tämän sivun manuaalisesti.');
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['fields']['c4g_activationpage_visitor_redirect'] = array('Ohjaa vierailijoita', 'Valitse sivu mihin vierailijat ohjataan');

/**
 * Legend
 */
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage_function_legend']                       = 'Funktio';
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage_custom_message_legend']                 = 'Custom viesti';

/**
 * Errors
 */
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['errors']['invalid_key']                  = 'Käytetty avain on väärä!';
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['errors']['key_not_claimed']              = 'VAROITUS: <br> &nbsp; Avainta ei voitu määritellä sinulle! <br> &nbsp; Ota yhteyttä systeemin ylläpitoon.';
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['errors']['handler_failed']               = 'Toimintaa ei voitu tehdä!';
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['errors']['no_handler']                   = 'Ei voitu löytää sopivaa toiminnan käsittelijää! <br> Ota yhteyttä systeemin ylläpitoon.';

/**
 * Misc.
 */
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['msc']['auto_action_handler']             = 'Valitse automaattisesti';
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['msc']['default_confirmation_button']     = 'Vahvista';
$GLOBALS['TL_LANG']['tl_content']['c4g_activationpage']['msc']['success_msg']                     = 'Toiminta suoritettiin onnistuneesti.';