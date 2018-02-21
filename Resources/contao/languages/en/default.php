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

    /**
     * Miscellaneous
     */
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['BACK']   = 'Back';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['CANCEL'] = 'Cancel';

    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['TITLESUB'] = 'Contao for geographical information systems';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['INTRO'] = 'con4gis4';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['FOOTER'] = 'A %s Project.';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['DEVELOP'] = 'Not installed with composer';

    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MIGRATION']['HEADLINE']      = 'Migration (cfs_%1$s -> con4gis_%1$s)';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MIGRATION']['INTRO']         = 'Running this migration will copy the existing data from "cfs_%1$s" to "con4gis_%1$s" and afterwards configurates Contao to run with "con4gis_%1$s".';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MIGRATION']['INTROWARN']     = 'Please note that this will OVERRIDE every entry of "con4gis_%s".';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MIGRATION']['NOMODULEERROR'] = 'You cannot run this migration, since %s is not installed. Please install the module and try again.';

    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MIGRATION']['SUCCESS']       = 'Success';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MIGRATION']['TRANSFEREDROW'] = 'Successfully transfered %d of %d rows';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MIGRATION']['TRANSFEREDCOL'] = 'Successfully transfered %d columns';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MIGRATION']['SUCCESSMSG_1']  = 'Transfer complete.';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MIGRATION']['SUCCESSMSG_2']  = 'Reconfiguration complete. The module "cfs_%s" can now be uninstalled.';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MIGRATION']['SUCCESSMSG_3']  = 'Database update required';

    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MIGRATION']['FAIL']      = 'Failed';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MIGRATION']['FAILMSG_1'] = '%d of %d transfers failed. Please check your installation and try again.';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MIGRATION']['FAILMSG_2'] = 'Reconfiguration failed. Please check your installation and try again.';


    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['APICHECK']['HEADLINE']       = 'API-Check';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['APICHECK']['INTRO']          = 'This tool checks the con4gis-API and reconfigures the .htaccess-File in the API-directory, if necessary.';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['APICHECK']['WARNING']        = 'This tool can damage the .htaccess-file in the API-directory ("CoreBundle/Resources/contao/api/"), if you have already edited it manually. You should make a backup of the file before running this tool.';

    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['APICHECK']['WORKS']          = 'The API works.';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['APICHECK']['REPAIRED']       = 'The API has been repaired successfully.';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['APICHECK']['STILLBROKEN']    = 'The API is broken and could not be repaired automatically.';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['APICHECK']['OTHER']          = 'The HTTP-Request returned an unexpected Statuscode.';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['APICHECK']['MODULENOTFOUND'] = 'The API\'s endpoint seems to be missing.';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['APICHECK']['NOWRITERIGHTS']  = 'The .htaccess could not been modified. (Permission denied!)';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['APICHECK']['NOREWRITEBASE']  = 'The .htaccess could not been modified. (Format error!)';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['APICHECK']['UNKNOWNERROR']   = 'An unknown error occurred...';


    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MEMBERGROUPSYNC']['HEADLINE']       = 'Synchronize membergroup-bindings';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MEMBERGROUPSYNC']['INTRO']          = 'This tool synchronizes "member->groups" to "group->members"-bindings in the database.';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MEMBERGROUPSYNC']['WARNING']        = 'This tool can override existing "group->members"-bindings, which is what you want in most cases though.';

    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MEMBERGROUPSYNC']['SUCCESS']                 = 'Bindings were synchronized successfully.';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MEMBERGROUPSYNC']['FAILED']                  = 'The bindings could not be synchronized correctly.';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MEMBERGROUPSYNC']['ERROR_GROUPLIMITREACHED'] = 'Could not add member with ID %s to group with ID %s, because it would exceed the grouplimit.';

    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['FORUMREMOVEBBCODE']['HEADLINE'] = 'Remove bbCode from con4gis 3 posts.';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['FORUMREMOVEBBCODE']['INTRO']    = 'This tool removes bbCode from older con4gis 3 posts.';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['FORUMREMOVEBBCODE']['WARNING']  = 'Older bbCode posts (con4gis 3) will reformated and saved.';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['FORUMREMOVEBBCODE']['SUCCESS']  = 'The bbCode was removed successfully.';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['FORUMREMOVEBBCODE']['FAILED']   = 'The bbCode could not be removed correctly.';

// button
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['BTN']['MIGRATE']         = 'Migrate data';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['BTN']['CHECKAPI']        = 'Check API';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['BTN']['SYNCBINDINGS']    = 'Sync bindings';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['BTN']['UNINSTALL']       = 'Uninstall "cfs_%s"';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['BTN']['DBUPDATE']        = 'Update database';

// links
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['CONTAO_BOARD']           = 'Community Board (DE)';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['GITHUB']                 = 'Fork on github';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['PLAYSTORE']              = 'Get the App <br>(Android)';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MIGRATIONTOOL']          = 'Migrationtool';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['APICHECKTOOL']           = 'API-Check';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['MEMBERGROUPSYNCTOOL']    = 'Sync group-bindings';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['FORUMREMOVEBBCODETOOL']  = 'Remove con4gis 3 bbCode';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['INSTALL']                = 'Install %s';

    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['con4gis_documentation']  = 'con4gis Compendium (german)';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['con4gis_website']        = 'Website Project';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['github_coastforge']      = 'Coastforge @ GitHub';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['osm_website']            = 'Website OpenStreetMap';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['ol_website']             = 'Website OpenLayers';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['overpassapi_website']    = 'Overpass-API informations (OpenStreetMap requests)';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['nominatim_website']      = 'Nominatim Informations (Search and reverse search)';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['osrm_website']           = 'OSRM Informationen (Open Source Routing Machine)';

    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['con4gis_support']        = 'con4gis website';
    $GLOBALS['TL_LANG']['MSC']['C4G_BE_INFO']['coastforge_support']     = 'Küstenschmiede website';

    $GLOBALS['TL_LANG']['MSC']['C4G_ERROR']['file_upload_successful']           = "%s successfully uploaded: \\n- Size: %s KB";
    $GLOBALS['TL_LANG']['MSC']['C4G_ERROR']['file_upload_error']                = "Unable to upload the file";
    $GLOBALS['TL_LANG']['MSC']['C4G_ERROR']['file_upload_invalid_extension']    = 'The file: %s has not the allowed extension type.';
    $GLOBALS['TL_LANG']['MSC']['C4G_ERROR']['file_upload_invalid_size']         = '\\n Maximum file size must be: %s KB.';

    $GLOBALS['TL_LANG']['MSC']['C4G_ERROR']['image_upload_successful']         = "%s successfully uploaded: \\n- Size: %s KB \\n- Image Width x Height: %s x %s";
    $GLOBALS['TL_LANG']['MSC']['C4G_ERROR']['image_upload_error']              = "Unable to upload the file";
    $GLOBALS['TL_LANG']['MSC']['C4G_ERROR']['image_upload_invalid_extension']  = 'The file: %s has not the allowed extension type.';
    $GLOBALS['TL_LANG']['MSC']['C4G_ERROR']['image_upload_invalid_size']       = '\\n Maximum file size must be: %s KB.';
    $GLOBALS['TL_LANG']['MSC']['C4G_ERROR']['image_upload_invalid_dimensions'] = '\\n Width x Height = %s x %s \\n The maximum Width x Height must be: %s x %s';

    $GLOBALS['TL_LANG']['MSC']['C4G_ERROR']['INVALID_EMAIL_ADDRESS']        = 'Invalid email-address.';
    $GLOBALS['TL_LANG']['MSC']['C4G_ERROR']['NO_EMAIL_ADDRESS']             = 'Please type in at least one valid email-address.';
    $GLOBALS['TL_LANG']['MSC']['C4G_ERROR']['NO_EMAIL_SUBJECT']             = 'Please enter a subject for this email.';
    $GLOBALS['TL_LANG']['MSC']['C4G_ERROR']['NO_EMAIL_MESSAGE']             = 'Please enter a message for this email.';

    // messages for exceptions by code
    // $GLOBALS['TL_LANG']['MSC']['C4G_ERROR']['exception_xxx'] = "";

    $GLOBALS['TL_LANG']['tl_maintenance_jobs']['con4gis'] = array('Purge the con4gis cache', 'Removes the temporary con4gis api files.');