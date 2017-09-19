<?php
namespace con4gis\CoreBundle\Resources\contao\classes;

use con4gis\CoreBundle\Resources\contao\classes\ResourceLoader;

if (!defined('TL_ROOT')) die('You can not access this file directly!');


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


//		if ($addJQuery)
//		{
//			if (is_array( $GLOBALS['TL_JAVASCRIPT'] ) &&
//				(array_search( 'assets/jquery/core/' . JQUERY . '/jquery.min.js|static', $GLOBALS['TL_JAVASCRIPT'] ) !== false))
//			{
//				// jQuery is already loaded by Contao 3, don't load again!
//			}
//			else {
//				// Include JQuery JS
//				ResourceLoader::loadJavaScriptRessource('c4g_jquery', 'assets/jquery/js/jquery.min.js|static', true);
//				// just until the old plugins are replaced
//				// $GLOBALS['TL_JAVASCRIPT']['c4g_jquery_migrate']		= 'bundles/con4giscore/vendor/jQuery/jquery-migrate-1.2.1.min.js';
//				//C4GJQueryGUI::optimizeJSForContao3('c4g_jquery');
//				// Set JQuery to noConflict mode immediately after load of jQuery
//				$GLOBALS['TL_JAVASCRIPT']['c4g_jquery_noconflict'] 	= 'bundles/con4giscore/js/c4gjQueryNoConflict.js';
//				C4GJQueryGUI::optimizeJSForContao3('c4g_jquery_noconflict');
//
//			}
//		}


		if ($addJQueryUI || $useTree || $useMaps)
   		{
			// Include JQuery UI JS - only js to append the css files to the end of $GLOBALS['TL_CSS'] array.
			$GLOBALS['TL_JAVASCRIPT']['c4g_jquery_ui'] 		= 'bundles/con4giscore/vendor/jQuery/jquery-ui-1.12.1.custom/jquery-ui.min.js';
            $GLOBALS['TL_JAVASCRIPT']['c4g_jquery_ui_i18n'] = 'bundles/con4giscore/vendor/jQuery/jquery-ui-i18n.min.js';
            $GLOBALS ['TL_JAVASCRIPT']['c4g_a'] 	        = 'bundles/con4giscore/vendor/jQuery/plugins/jquery.legacy.min.js';

            $GLOBALS['TL_JAVASCRIPT'][] = 'bundles/con4giscore/js/DialogHanlder.js';
   		}

		if ($useTable)
		{
			// Include DataTables JS
			$GLOBALS['TL_JAVASCRIPT']['c4g_jq_datatables'] 	= 'bundles/con4giscore/vendor/jQuery/plugins/datatables/media/js/jquery.dataTables.min.js';
			$GLOBALS['TL_JAVASCRIPT']['c4g_jq_datatables_buttons'] = 'bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Buttons/js/dataTables.buttons.min.js';
			$GLOBALS['TL_JAVASCRIPT']['c4g_jq_datatables_buttons_print'] = 'bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Buttons/js/buttons.print.min.js';
			$GLOBALS['TL_JAVASCRIPT']['c4g_jq_datatables_buttons_jquery'] = 'bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Buttons/js/buttons.jqueryui.min.js';
			$GLOBALS['TL_JAVASCRIPT']['c4g_jq_datatables_buttons_pdf'] = 'bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Buttons/js/pdfmake.min.js';
			$GLOBALS['TL_JAVASCRIPT']['c4g_jq_datatables_buttons_html5'] = 'bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Buttons/js/buttons.html5.min.js';
			$GLOBALS['TL_JAVASCRIPT']['c4g_jq_datatables_buttons_font'] = 'bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Buttons/js/vfs_fonts.js';
			$GLOBALS['TL_JAVASCRIPT']['c4g_jq_datatables_jszip'] = 'bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Buttons/js/jszip.min.js';
            $GLOBALS['TL_JAVASCRIPT']['c4g_jq_scrollTo'] = 'bundles/con4giscore/vendor/jQuery/plugins/jquery.scrollTo/jquery.scrollTo.min.js';
            $GLOBALS['TL_JAVASCRIPT']['c4g_jq_datatables_scroller'] = 'bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Scroller/js/dataTables.scroller.min.js';
			$GLOBALS['TL_JAVASCRIPT']['c4g_jq_datatables_sort_date_de'] = 'bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Sorting/date-de.js';
            $GLOBALS['TL_JAVASCRIPT']['c4g_jq_datatables_responsive'] = 'bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Responsive/js/dataTables.responsive.min.js';
            $GLOBALS['TL_JAVASCRIPT']['c4g_jq_datatables_responsive_ui'] = 'bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Responsive/js/responsive.jqueryui.min.js';

			// Include DataTables CSS
			$GLOBALS['TL_CSS']['c4g_jq_datatables'] = 'bundles/con4giscore/vendor/jQuery/plugins/datatables/media/css/jquery.dataTables_themeroller.css';
			$GLOBALS['TL_CSS']['c4g_jq_datatables_buttons']	= 'bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Buttons/css/buttons.dataTables.min.css';
			$GLOBALS['TL_CSS']['c4g_jq_datatables_buttons_jquery'] = 'bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Buttons/css/buttons.jqueryui.min.css';
            $GLOBALS['TL_CSS']['c4g_jq_datatables_scroller'] = 'bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Scroller/css/scroller.dataTables.min.css';
            $GLOBALS['TL_CSS']['c4g_jq_datatables_scroller_ui'] = 'bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Scroller/css/scroller.jqueryui.min.css';
            $GLOBALS['TL_CSS']['c4g_jq_datatables_responsive'] = 'bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Responsive/css/responsive.dataTables.min.css';
            $GLOBALS['TL_CSS']['c4g_jq_datatables_responsive_ui'] = 'bundles/con4giscore/vendor/jQuery/plugins/datatables/extensions/Responsive/css/responsive.jqueryui.min.css';
        }

		if ($useTree || $useMaps)
		{
			// Include dynatree JS
			$GLOBALS['TL_JAVASCRIPT']['c4g_jq_dynatree'] 	= 'bundles/con4giscore/vendor/jQuery/plugins/dynatree/jquery.dynatree.min.js';
			// Include dynatree CSS
			$GLOBALS['TL_CSS']['c4g_jq_dynatree'] 			= 'bundles/con4giscore/vendor/jQuery/plugins/dynatree/skin/ui.dynatree.css';
		}

		if ($useHistory)
		{
   			$GLOBALS ['TL_JAVASCRIPT'] ['c4g_jq_history'] 	= 'bundles/con4giscore/vendor/jQuery/plugins/jquery.history.js';
		}

		if ($useTooltip)
		{
   			$GLOBALS ['TL_JAVASCRIPT'] ['c4g_jq_tooltip_b'] 	= 'bundles/con4giscore/vendor/jQuery/plugins/jquery.tooltip.pack.js';
		}

		if ($useWswgEditor)
		{
			$GLOBALS['TL_JAVASCRIPT']['c4g_jq_bbc'] 		= 'bundles/con4giscore/vendor/wswgEditor/editor.js';
			$GLOBALS['TL_CSS']['c4g_jq_bbc'] 				= 'bundles/con4giscore/vendor/wswgEditor/css/editor.css';
			$GLOBALS['TL_CSS']['c4g_jq_bbc2'] 				= 'bundles/con4giscore/vendor/wswgEditor/css/bbcodes.css';
			// also use file-upload
			$GLOBALS['TL_JAVASCRIPT']['c4g_jq_fileupload'] 	= 'bundles/con4giscore/vendor/fileUpload/upload.js';
		}

		if ($useScrollpane)
		{
			$GLOBALS['TL_JAVASCRIPT']['c4g_jq_scrollpane'] 	= 'bundles/con4giscore/vendor/jQuery/plugins/jScrollPane/js/jquery.jscrollpane.min.js';
			$GLOBALS['TL_JAVASCRIPT']['c4g_jq_mousewheel'] 	= 'bundles/con4giscore/vendor/jQuery/plugins/jScrollPane/js/jquery.mousewheel.js';
			$GLOBALS['TL_JAVASCRIPT']['c4g_mwheelintent'] 	= 'bundles/con4giscore/vendor/jQuery/plugins/jScrollPane/js/mwheelIntent.js';
			$GLOBALS['TL_CSS']['c4g_jq_scrollpane'] 		= 'bundles/con4giscore/vendor/jQuery/plugins/jScrollPane/css/jquery.jscrollpane.css';
		}

		if ($usePopups || ($GLOBALS['con4gis_projects_extension']['installed']))
		{
			$GLOBALS['TL_CSS']['magnific-popup'] = 'bundles/con4giscore/vendor/magnific-popup/magnific-popup.css';
			$GLOBALS['TL_JAVASCRIPT']['magnific-popup'] = 'bundles/con4giscore/vendor/magnific-popup/jquery.magnific-popup.min.js';
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

                $GLOBALS['TL_JAVASCRIPT']['magnific-popup'] = 'bundles/con4giscore/vendor/magnific-popup/jquery.magnific-popup.min.js|static';
                $GLOBALS['TL_CSS']['magnific-popup'] = 'bundles/con4giscore/vendor/magnific-popup/magnific-popup.css';
            }

            $GLOBALS['TL_JAVASCRIPT']['clipboard'] = 'bundles/con4giscore/vendor/clipboard.min.js|static';
            $GLOBALS['TL_JAVASCRIPT']['datetimepicker'] = 'bundles/con4giscore/vendor/jQuery/plugins/jquery-simple-datetimepicker/1.13.0/jquery.simple-dtpicker.js|static';

            //ToDo uncomment for PDF Export
            //$GLOBALS['TL_JAVASCRIPT']['jspdf'] = 'bundles/con4giscore/vendor/jspdf/jspdf.min.js|static';
		}

		if ($addCore)
		{
	        $GLOBALS ['TL_JAVASCRIPT']['c4g_jquery_gui'] = 'bundles/con4giscore/vendor/jQuery/plugins/jquery.c4gGui.js';
	        $GLOBALS ['TL_CSS']['c4g_jquery_gui'] = 'bundles/con4giscore/css/c4gGui.css';
			$GLOBALS ['TL_CSS']['c4g_loader'] = 'bundles/con4giscore/css/c4gLoader.css';
		}

		if ($addJQueryUI || $useTree || $useMaps)
		{
			// Add the JQuery UI CSS to the bottom of the $GLOBALS['TL_CSS'] array to prevent overriding from other plugins
			$GLOBALS['TL_CSS']['c4g_jquery_ui_core'] = 'bundles/con4giscore/vendor/jQuery/jquery-ui-1.12.1.custom/jquery-ui.min.css';
			// Set the JQuery UI theme to be used
			if (empty($GLOBALS['TL_CSS']['c4g_jquery_ui'])) {
				$GLOBALS['TL_CSS']['c4g_jquery_ui'] = 'bundles/con4giscore/vendor/jQuery/jquery-ui-1.12.1.custom/jquery-ui.theme.min.css';
			}
		}



    }
}
