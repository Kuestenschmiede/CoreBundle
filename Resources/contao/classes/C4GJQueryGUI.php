<?php
namespace con4gis\CoreBundle\Resources\contao\classes;

use con4gis\CoreBundle\Resources\contao\classes\ResourceLoader;

if (!defined('TL_ROOT')) die('You can not access this file directly!');


/*
 * This file is part of con4gis,
 * the gis-kit for Contao CMS.
 *
 * @package    con4gis
 * @version    6
 * @author     con4gis contributors (see "authors.txt")
 * @license    LGPL-3.0-or-later
 * @copyright  Küstenschmiede GmbH Software & Design
 * @link       https://www.con4gis.org
 */


/**
 * Class C4GJQueryGUI
 */

class C4GJQueryGUI
{
	public static function initializeTree ( $addCore=false, $addJQuery=true, $addJQueryUI=true )
	{
		C4GJQueryGUI::initializeLibraries( $addCore, $addJQuery, $addJQueryUI, true, false, false, false, false, false, false, false, true, false);
	}

	public static function initializeLibraries ( $addCore=true, $addJQuery=true, $addJQueryUI=true, $useTree=true, $useTable=true, $useHistory=true, $useTooltip=true,
											   		$useMaps=false, $useGoogleMaps=false, $useMapsEditor=false, $useWswgEditor=false, $useScrollpane=false, $usePopups=false )
	{
		if ($addJQuery)
		{
			if (is_array( $GLOBALS['TL_JAVASCRIPT'] ) &&
				(array_search( 'assets/jquery/js/jquery.min.js|static', $GLOBALS['TL_JAVASCRIPT'] ) !== false))
			{
				// jQuery is already loaded by Contao, don't load again!
			}
			else {
                // Include JQuery JS
                ResourceLoader::loadJavaScriptResource('assets/jquery/js/jquery.min.js|static', $location = ResourceLoader::JAVASCRIPT, $key = 'c4g_jquery');
                // just until the old plugins are replaced
                // Set JQuery to noConflict mode immediately after load of jQuery
                ResourceLoader::loadJavaScriptResource('bundles/con4giscore/js/c4gjQueryNoConflict.js', $location = ResourceLoader::HEAD, $key = 'c4g_jquery_noconflict');
            }
		}


		if ($addJQueryUI || $useTree || $useMaps)
   		{
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/jquery-ui-1.12.1.custom/jquery-ui.min.js', $location = ResourceLoader::HEAD, $key = 'c4g_jquery_ui');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/jquery-ui-i18n.min.js', $location = ResourceLoader::HEAD, $key = 'c4g_jquery_ui_i18n');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/jquery.legacy.min.js', $location = ResourceLoader::HEAD, $key = 'c4g_a');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/js/DialogHandler.js', $location = ResourceLoader::HEAD, $key = 'dialog_handler');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/build/AlertHandler.js', $location = ResourceLoader::HEAD, $key = 'alert_handler');
   		}

		if ($useTable)
		{
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/jquery.scrollTo/jquery.scrollTo.min.js', $location = ResourceLoader::HEAD, $key = 'c4g_jq_scrollTo');

            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/datatables/media/js/jquery.dataTables.min.js', $location = ResourceLoader::HEAD, $key = 'c4g_jq_datatables');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/datatables/media/js/dataTables.jqueryui.min.js', $location = ResourceLoader::HEAD, $key = 'c4g_jq_datatables_ui');

            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Buttons/js/dataTables.buttons.min.js', $location = ResourceLoader::HEAD, $key = 'c4g_jq_datatables_buttons');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Buttons/js/buttons.print.min.js', $location = ResourceLoader::HEAD, $key = 'c4g_jq_datatables_buttons_print');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Buttons/js/buttons.jqueryui.min.js', $location = ResourceLoader::HEAD, $key = 'c4g_jq_datatables_buttons_jquery');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Buttons/js/pdfmake.min.js', $location = ResourceLoader::HEAD, $key = 'c4g_jq_datatables_buttons_pdf');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Buttons/js/buttons.html5.min.js', $location = ResourceLoader::HEAD, $key = 'c4g_jq_datatables_buttons_html5');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Buttons/js/vfs_fonts.js', $location = ResourceLoader::HEAD, $key = 'c4g_jq_datatables_buttons_font');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Buttons/js/jszip.min.js', $location = ResourceLoader::HEAD, $key = 'c4g_jq_datatables_jszip');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Scroller/js/dataTables.scroller.min.js', $location = ResourceLoader::HEAD, $key = 'c4g_jq_datatables_scroller');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Sorting/date-de.js', $location = ResourceLoader::HEAD, $key = 'c4g_jq_datatables_sort_date_de');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Sorting/text-de.js', $location = ResourceLoader::HEAD, $key = 'c4g_jq_datatables_sort_text_de');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Responsive/js/dataTables.responsive.min.js', $location = ResourceLoader::HEAD, $key = 'c4g_jq_datatables_responsive');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Responsive/js/responsive.jqueryui.min.js', $location = ResourceLoader::HEAD, $key = 'c4g_jq_datatables_responsive_ui');

			// Include DataTables CSS
			ResourceLoader::loadCssResourceDeferred('bundles/con4giscore/vendor/jQuery/plugins/datatables/media/css/jquery.dataTables_themeroller.css');
			ResourceLoader::loadCssResourceDeferred('bundles/con4giscore/vendor/jQuery/plugins/datatables/media/css/dataTables.jqueryui.min.css');

            // Include DataTables Extensions CSS
            ResourceLoader::loadCssResourceDeferred('bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Buttons/css/buttons.dataTables.min.css');
            ResourceLoader::loadCssResourceDeferred('bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Buttons/css/buttons.jqueryui.min.css');
            ResourceLoader::loadCssResourceDeferred('bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Scroller/css/scroller.dataTables.min.css');
            ResourceLoader::loadCssResourceDeferred('bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Scroller/css/scroller.jqueryui.min.css');
            ResourceLoader::loadCssResourceDeferred('bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Responsive/css/responsive.dataTables.min.css');
            ResourceLoader::loadCssResourceDeferred('bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Responsive/css/responsive.jqueryui.min.css');
        }

		if ($useTree || $useMaps)
		{
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/dynatree/jquery.dynatree.min.js', $location = ResourceLoader::HEAD, $key = 'c4g_jq_dynatree');
            ResourceLoader::loadCssResourceDeferred('bundles/con4giscore/vendor/jQuery/plugins/dynatree/skin/ui.dynatree.css');
		}

		if ($useHistory)
		{
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/jquery.history.js', $location = ResourceLoader::HEAD, $key = 'c4g_jq_history');
		}

		if ($useTooltip)
		{
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/jquery.tooltip.pack.js', $location = ResourceLoader::HEAD, $key = 'c4g_jq_tooltip_b');
		}

		if ($useWswgEditor)
		{
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/wswgEditor/editor.js', $location = ResourceLoader::HEAD, $key = 'c4g_jq_bbc');
            ResourceLoader::loadCssResourceDeferred('bundles/con4giscore/vendor/wswgEditor/css/editor.css');
            ResourceLoader::loadCssResourceDeferred('bundles/con4giscore/vendor/wswgEditor/css/bbcodes.css');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/fileUpload/upload.js', $location = ResourceLoader::HEAD, $key = 'c4g_jq_fileupload');
		}

		if ($useScrollpane)
		{
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/jScrollPane/js/jquery.jscrollpane.min.js', $location = ResourceLoader::HEAD, $key = 'c4g_jq_scrollpane');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/jScrollPane/js/jquery.mousewheel.js', $location = ResourceLoader::HEAD, $key = 'c4g_jq_mousewheel');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/jScrollPane/js/mwheelIntent.js', $location = ResourceLoader::HEAD, $key = 'c4g_mwheelintent');
            ResourceLoader::loadCssResourceDeferred('bundles/con4giscore/vendor/jQuery/plugins/jScrollPane/css/jquery.jscrollpane.css');
		}

		if ($usePopups || ($GLOBALS['con4gis']['projects']['installed']))
		{
            ResourceLoader::loadCssResourceDeferred('bundles/con4giscore/vendor/magnific-popup/magnific-popup.css');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/magnific-popup/jquery.magnific-popup.min.js', $location = ResourceLoader::HEAD, $key = 'magnific-popup');
		}

		//TODO: add own switch for maps
		if ($GLOBALS['con4gis']['projects']['installed']) {
            ResourceLoader::loadCssResourceDeferred('bundles/con4giscore/vendor/jQuery/plugins/lighbox2/css/lightbox.min.css');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/lighbox2/js/lightbox.min.js', $location = ResourceLoader::HEAD, $key = 'c4g_jq_lighbox2');
        }


		if ($useMaps && $GLOBALS['con4gis']['maps']['installed'])
		{
            // TODO: recieve and use profileId
            \con4gis\MapsBundle\Resources\contao\classes\ResourceLoader::loadResources();
            \con4gis\MapsBundle\Resources\contao\classes\ResourceLoader::loadTheme();

//            // Core-Resources
//            //
//            if (is_array( $GLOBALS['TL_JAVASCRIPT'] ) &&
//                (array_search( 'assets/jquery/core/' . JQUERY . '/jquery.min.js|static', $GLOBALS['TL_JAVASCRIPT'] ) !== false))
//            {
//                // jQuery is already loaded by Contao 3, don't load again!
//            }
//            else {
//                ResourceLoader::loadJavaScriptRessource('c4g_jquery', 'assets/jquery/js/jquery.min.js|static', true);
//            }
            // Load magnific-popup.js for projects
            if ($GLOBALS['con4gis']['projects']['installed']) {

                ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/magnific-popup/jquery.magnific-popup.min.js|static', $location = ResourceLoader::JAVASCRIPT, $key = 'magnific-popup');
                $GLOBALS['TL_CSS']['magnific-popup'] = 'bundles/con4giscore/vendor/magnific-popup/magnific-popup.css';
            }

            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/clipboard.min.js|static', $location = ResourceLoader::JAVASCRIPT, $key = 'clipboard');
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/jquery-simple-datetimepicker/1.13.0/jquery.simple-dtpicker.js|static', $location = ResourceLoader::JAVASCRIPT, $key = 'datetimepicker');

		}

		if ($addCore)
		{
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/js/c4gGui.js', $location = ResourceLoader::HEAD, $key = 'c4g_jquery_gui');
            ResourceLoader::loadCssResourceDeferred('bundles/con4giscore/css/c4gGui.css');
            ResourceLoader::loadCssResourceDeferred('bundles/con4giscore/css/c4gLoader.css');
		}

		if ($addJQueryUI || $useTree || $useMaps)
		{
			// Add the JQuery UI CSS to the bottom of the $GLOBALS['TL_CSS'] array to prevent overriding from other plugins
            $GLOBALS['TL_CSS']['c4g_jquery_ui_core'] = 'bundles/con4giscore/vendor/jQuery/jquery-ui-1.12.1.custom/jquery-ui.min.css';
            ResourceLoader::loadCssResourceDeferred('bundles/con4giscore/vendor/jQuery/jquery-ui-1.12.1.custom/jquery-ui.min.css');
			// Set the JQuery UI theme to be used
			if (empty($GLOBALS['TL_CSS']['c4g_jquery_ui'])) {
                ResourceLoader::loadCssResourceDeferred('bundles/con4giscore/vendor/jQuery/jquery-ui-1.12.1.custom/jquery-ui.theme.min.css');
			}
		}



    }
}
