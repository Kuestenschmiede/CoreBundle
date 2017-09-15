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

    /**
     * Miscellaneous
     */
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['BACK']   = 'Takaisin';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['CANCEL'] = 'Peruuta';

    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['TITLESUB'] = 'Sisältö-Manageri Geo-Informaatio-Systeemiä varten';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['INTRO'] = 'con4gis4';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['FOOTER'] = '%s Projekti.';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['DEVELOP'] = 'Developer version';

    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MIGRATION']['HEADLINE']      = 'Migration (cfs_%1$s -> con4gis_%1$s)';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MIGRATION']['INTRO']         = 'Suorittamalla tämän migraation se kopio olemassaolevan datan "cfs_%1$s" paikkaan "con4gis_%1$s" ja jälkeenpäin konfiguroi Contaon käynnistymään ohjelmalla "con4gis_%1$s".';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MIGRATION']['INTROWARN']     = 'Huomio että tämä ohittaa jokaisen pääsyn "con4gis_%s".';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MIGRATION']['NOMODULEERROR'] = 'Et voi suorittaa tätä migraatiota, koska %s ei ole asennettu. Asenna moduuli ja yritä uudelleen.';

    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MIGRATION']['SUCCESS']       = 'Onnistui';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MIGRATION']['TRANSFEREDROW'] = 'Onnistuneesti siirrettiin %d %d riviä';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MIGRATION']['TRANSFEREDCOL'] = 'Onnistuneesti siirrettiin %d kolumnia';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MIGRATION']['SUCCESSMSG_1']  = 'Siirto suoritettu.';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MIGRATION']['SUCCESSMSG_2']  = 'Uudelleenkonfiguraatio suoritettu. Moduuli "cfs_%s" voidaan nyt poistaa.';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MIGRATION']['SUCCESSMSG_3']  = 'Tietokannan päivitys vaaditaan';

    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MIGRATION']['FAIL']      = 'Epäonnistui';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MIGRATION']['FAILMSG_1'] = '%d %d siirtoa epäonnistui. Tarkista asennuksesi ja yritä uudelleen.';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MIGRATION']['FAILMSG_2'] = 'Uudelleenkonfiguraatio epäonnistui. Tarkista asennuksesi ja yritä uudelleen.';


    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['APICHECK']['HEADLINE']       = 'API-Tarkistus';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['APICHECK']['INTRO']          = 'Tämä työkalu tarkistaa con4gis-API ja uudelleenkonfiguroi .htaccess-tiedoston API-hakemistossa, jos tarpeellista.';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['APICHECK']['WARNING']        = 'Tämä työkalu voi vahingoittaa .htaccess-tiedostoa API-hakemistossa ("CoreBundle/Resources/contao/api/"), jos olet editoinut sitä jo manuaalisesti. Kannattaa tehdä varmuuskopio tiedostolle ennen kuin suoritat tämän työkalun.';

    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['APICHECK']['WORKS']          = 'API toimii.';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['APICHECK']['REPAIRED']       = 'API on korjattu onnistuneesti.';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['APICHECK']['STILLBROKEN']    = 'API on rikki eikä voitu korjata automaattisesti.';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['APICHECK']['OTHER']          = 'HTTP-Pyyntö palautti odottamattoman Statuskoodin.';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['APICHECK']['MODULENOTFOUND'] = 'API:n päätepisti näyttäisi puuttuvan.';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['APICHECK']['NOWRITERIGHTS']  = '.htaccessia ei voitu muokata. (Pääsy evätty!)';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['APICHECK']['NOREWRITEBASE']  = '.htaccessia ei voitu muokata. (Muotovirhe!)';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['APICHECK']['UNKNOWNERROR']   = 'Tuntematon virhe tapahtui...';


    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MEMBERGROUPSYNC']['HEADLINE']       = 'Synkronoi membergroup-bindings';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MEMBERGROUPSYNC']['INTRO']          = 'Tämä työkalu synkronoi "jäsen->ryhmät" "ryhmä->jäsen"-siteeksi tietokannassa.';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MEMBERGROUPSYNC']['WARNING']        = 'Tämä työkalu voi ohittaa olemassaolevan "ryhmä->jäsenet"-siteet, mikä on mitä haluat useimmissa tapauksissa.';

    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MEMBERGROUPSYNC']['SUCCESS']                 = 'Siteet synkronoitiin onnistuneesti.';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MEMBERGROUPSYNC']['FAILED']                  = 'Siteitä ei voitu synkronoida oikein.';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MEMBERGROUPSYNC']['ERROR_GROUPLIMITREACHED'] = 'Ei voitu lisätä jäsentä jolla on ID %s ryhmään jossa on ID %s, koska se ylittäisi ryhmälimiitin.';

// button
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['BTN']['MIGRATE']         = 'Muuta dataa';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['BTN']['CHECKAPI']        = 'Tarkista API';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['BTN']['SYNCBINDINGS']    = 'Synkronoi siteet';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['BTN']['UNINSTALL']       = 'Poista "cfs_%s"';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['BTN']['DBUPDATE']        = 'Päivitä tietokanta';

// links
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['CONTAO_BOARD']           = 'Yhteisö Board (FI)';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['GITHUB']                 = 'Fork on github';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['PLAYSTORE']              = 'Hommaa App <br>(Android)';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MIGRATIONTOOL']          = 'Muuttotyökalu';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['APICHECKTOOL']           = 'API-Tarkistus';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MEMBERGROUPSYNCTOOL']    = 'Synkronoi ryhmäsiteet';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['INSTALL']                = 'Asenna %s';

    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['con4gis_documentation'] = 'con4gis Compendium (german)';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['con4gis_website']     = 'Verkkosivu projekti';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['github_coastforge']   = 'Coastforge @ GitHub';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['osm_website']         = 'Verkkosivu OpenStreetMap';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['ol_website']          = 'Verkkosivu OpenLayers';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['overpassapi_website'] = 'Overpass-API informations';


    $GLOBALS['TL_LANG']['MSC']['C4G_ERROR']['file_upload_successful']           = "%s onnistuneesti latasi: \\n- Koko: %s KB";
    $GLOBALS['TL_LANG']['MSC']['C4G_ERROR']['file_upload_error']                = "Ei kyetty lataamaan tiedostoa";
    $GLOBALS['TL_LANG']['MSC']['C4G_ERROR']['file_upload_invalid_extension']    = 'Tidosto: %s ei ole sallinut laajennustyyppiä.';
    $GLOBALS['TL_LANG']['MSC']['C4G_ERROR']['file_upload_invalid_size']         = '\\n Maksimi tiedostokoon täytyy olla: %s KB.';

    $GLOBALS['TL_LANG']['MSC']['C4G_ERROR']['image_upload_successful']         = "%s onnistuneesti latautui: \\n- Koko: %s KB \\n- Kuvan leveys x korkeus: %s x %s";
    $GLOBALS['TL_LANG']['MSC']['C4G_ERROR']['image_upload_error']              = "Ei kyetty lataamaan tiedostoa";
    $GLOBALS['TL_LANG']['MSC']['C4G_ERROR']['image_upload_invalid_extension']  = 'Tiedosto: %s ei ole sallinut laajennustyyppiä.';
    $GLOBALS['TL_LANG']['MSC']['C4G_ERROR']['image_upload_invalid_size']       = '\\n Maksimi tiedostokoon täytyy olla: %s KB.';
    $GLOBALS['TL_LANG']['MSC']['C4G_ERROR']['image_upload_invalid_dimensions'] = '\\n leveys x korkeus = %s x %s \\n Maksimi leveys x korkeus täytyy olla: %s x %s';

    $GLOBALS['TL_LANG']['MSC']['C4G_ERROR']['INVALID_EMAIL_ADDRESS']        = 'Väärä sähköpostiosoite.';
    $GLOBALS['TL_LANG']['MSC']['C4G_ERROR']['NO_EMAIL_ADDRESS']             = 'Kirjoita vähintään yksi sopiva sähköpostiosoite.';
    $GLOBALS['TL_LANG']['MSC']['C4G_ERROR']['NO_EMAIL_SUBJECT']             = 'Kirjoita aihe tälle sähköpostille.';
    $GLOBALS['TL_LANG']['MSC']['C4G_ERROR']['NO_EMAIL_MESSAGE']             = 'Kirjoita viesti tälle sähköpostille.';

    // messages for exceptions by code
    // $GLOBALS['TL_LANG']['MSC']['C4G_ERROR']['exception_xxx'] = "";
?>